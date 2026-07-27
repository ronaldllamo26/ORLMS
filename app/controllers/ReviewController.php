<?php

/**
 * ORLMS - Review and Endorsement Controller
 *
 * Handles the review workflow for submitted ordinances and resolutions.
 * Authorized roles: sp_member, committee_member, super_admin
 *
 * Routes:
 *   GET  /review                          → index()       — documents for review
 *   GET  /review/view/{type}/{id}         → view($t,$id)  — review a document
 *   POST /review/endorse/{type}/{id}      → endorse()     — approve and endorse
 *   POST /review/reject/{type}/{id}       → reject()      — reject with reason
 *   POST /review/return/{type}/{id}       → returnDoc()   — return for revision
 */

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — All documents pending review
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $db = \Database::getInstance()->getConnection();

        // Fetch submitted ordinances
        $stmtOrd = $db->prepare(
            "SELECT o.id, o.ordinance_no AS doc_no, o.title, o.subject,
                    o.status, o.date_filed, o.created_at,
                    u.name AS author_name,
                    'ordinance' AS doc_type
             FROM ordinances o
             LEFT JOIN users u ON o.author_id = u.id
             WHERE o.status IN ('submitted','under_review')
             ORDER BY o.created_at ASC"
        );
        $stmtOrd->execute();
        $ordinances = $stmtOrd->fetchAll(PDO::FETCH_ASSOC);

        // Fetch submitted resolutions
        $stmtRes = $db->prepare(
            "SELECT r.id, r.resolution_no AS doc_no, r.title, r.subject,
                    r.status, r.date_filed, r.created_at,
                    u.name AS author_name,
                    'resolution' AS doc_type
             FROM resolutions r
             LEFT JOIN users u ON r.author_id = u.id
             WHERE r.status IN ('submitted','under_review')
             ORDER BY r.created_at ASC"
        );
        $stmtRes->execute();
        $resolutions = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

        // Merge and sort by date
        $pending = array_merge($ordinances, $resolutions);
        usort($pending, fn($a, $b) => strtotime($a['created_at']) - strtotime($b['created_at']));

        $this->render('review/index', [
            'pageTitle' => 'Review and Endorsement',
            'pending'   => $pending,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VIEW — Review a single document
    // ─────────────────────────────────────────────────────────────────────────

    public function view(string $type, string $id): void
    {
        if (!in_array($type, ['ordinance', 'resolution'])) {
            $this->flash('error', 'Invalid document type.');
            $this->redirect('review');
        }

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->getByIdWithAuthor((int) $id);

        if (!$document) {
            $this->flash('error', ucfirst($type) . ' not found.');
            $this->redirect('review');
        }

        // Fetch the latest AI validation report for this document
        $aiModel = $this->model('AiValidationModel');
        $aiReport = $aiModel->getLatestForDocument($type, (int) $id);

        // Fetch review history
        $db = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "SELECT rl.*, u.name AS reviewer_name
             FROM review_logs rl
             LEFT JOIN users u ON rl.reviewed_by = u.id
             WHERE rl.document_type = :type AND rl.document_id = :id
             ORDER BY rl.created_at DESC"
        );
        $stmt->execute([':type' => $type, ':id' => (int) $id]);
        $reviewHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('review/view', [
            'pageTitle'     => 'Review: ' . ($document['ordinance_no'] ?? $document['resolution_no'] ?? 'Document'),
            'document'      => $document,
            'docType'       => $type,
            'aiReport'      => $aiReport,
            'reviewHistory' => $reviewHistory,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ENDORSE — Approve and endorse the document
    // ─────────────────────────────────────────────────────────────────────────

    public function endorse(string $type, string $id): void
    {
        $this->requireRole([ROLE_SP_MEMBER, ROLE_COMMITTEE_MEMBER, ROLE_SUPER_ADMIN]);

        if (!$this->isPost()) {
            $this->redirect('review/view/' . $type . '/' . $id);
        }

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->findById((int) $id);

        if (!$document) {
            $this->flash('error', ucfirst($type) . ' not found.');
            $this->redirect('review');
        }

        $allowedStatuses = ['submitted', 'under_review'];
        if (!in_array($document['status'], $allowedStatuses)) {
            $this->flash('error', 'This document cannot be endorsed at its current status.');
            $this->redirect('review/view/' . $type . '/' . $id);
        }

        // Update document status
        $model->updateStatus((int) $id, STATUS_ENDORSED);

        // Log the review action
        $this->logReview($type, (int) $id, 'endorsed', null);

        // System audit log
        $userModel = $this->model('UserModel');
        $userModel->logAudit(
            $this->userId(), 'ENDORSE', $type . 's', (int) $id,
            ['status' => $document['status']],
            ['status' => STATUS_ENDORSED]
        );

        $docNo = $document['ordinance_no'] ?? $document['resolution_no'] ?? '#' . $id;
        $this->flash('success', $docNo . ' has been endorsed successfully and forwarded for approval.');
        $this->redirect('review');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REJECT — Reject the document (moves to Archive)
    // ─────────────────────────────────────────────────────────────────────────

    public function reject(string $type, string $id): void
    {
        $this->requireRole([ROLE_SP_MEMBER, ROLE_COMMITTEE_MEMBER, ROLE_SUPER_ADMIN]);

        if (!$this->isPost()) {
            $this->redirect('review/view/' . $type . '/' . $id);
        }

        $reason = trim($this->post('reason', ''));

        if (empty($reason)) {
            $this->flash('error', 'A rejection reason is required.');
            $this->redirect('review/view/' . $type . '/' . $id);
        }

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->findById((int) $id);

        if (!$document) {
            $this->flash('error', ucfirst($type) . ' not found.');
            $this->redirect('review');
        }

        $allowedStatuses = ['submitted', 'under_review'];
        if (!in_array($document['status'], $allowedStatuses)) {
            $this->flash('error', 'This document cannot be rejected at its current status.');
            $this->redirect('review/view/' . $type . '/' . $id);
        }

        // Update document status to rejected
        $model->updateStatus((int) $id, STATUS_REJECTED);

        // Log the rejection with reason
        $this->logReview($type, (int) $id, 'rejected', $reason);

        // System audit log
        $userModel = $this->model('UserModel');
        $userModel->logAudit(
            $this->userId(), 'REJECT', $type . 's', (int) $id,
            ['status' => $document['status']],
            ['status' => STATUS_REJECTED, 'reason' => $reason]
        );

        $docNo = $document['ordinance_no'] ?? $document['resolution_no'] ?? '#' . $id;
        $this->flash('success', $docNo . ' has been rejected and moved to the Archive.');
        $this->redirect('review');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RETURN — Return document for revision (back to draft)
    // ─────────────────────────────────────────────────────────────────────────

    public function returnDoc(string $type, string $id): void
    {
        $this->requireRole([ROLE_SP_MEMBER, ROLE_COMMITTEE_MEMBER, ROLE_SUPER_ADMIN]);

        if (!$this->isPost()) {
            $this->redirect('review/view/' . $type . '/' . $id);
        }

        $reason = trim($this->post('reason', ''));

        if (empty($reason)) {
            $this->flash('error', 'Please provide a reason for returning this document for revision.');
            $this->redirect('review/view/' . $type . '/' . $id);
        }

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->findById((int) $id);

        if (!$document) {
            $this->flash('error', ucfirst($type) . ' not found.');
            $this->redirect('review');
        }

        // Return to draft for revision
        $model->updateStatus((int) $id, STATUS_DRAFT);

        // Log the return action
        $this->logReview($type, (int) $id, 'returned_for_revision', $reason);

        // System audit log
        $userModel = $this->model('UserModel');
        $userModel->logAudit(
            $this->userId(), 'RETURN_FOR_REVISION', $type . 's', (int) $id,
            ['status' => $document['status']],
            ['status' => STATUS_DRAFT, 'reason' => $reason]
        );

        $docNo = $document['ordinance_no'] ?? $document['resolution_no'] ?? '#' . $id;
        $this->flash('success', $docNo . ' has been returned to the author for revision.');
        $this->redirect('review');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE: LOG REVIEW ACTION
    // ─────────────────────────────────────────────────────────────────────────

    private function logReview(string $type, int $id, string $action, ?string $reason): void
    {
        $db = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "INSERT INTO review_logs
             (document_type, document_id, action, reason, reviewed_by)
             VALUES (:type, :id, :action, :reason, :user)"
        );
        $stmt->execute([
            ':type'   => $type,
            ':id'     => $id,
            ':action' => $action,
            ':reason' => $reason,
            ':user'   => $this->userId(),
        ]);
    }
}
