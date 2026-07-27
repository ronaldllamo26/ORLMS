<?php

/**
 * ORLMS - Approval and Enactment Controller
 *
 * Handles the approval workflow for endorsed ordinances and resolutions.
 * Authorized roles: super_admin (acts as approving authority)
 *
 * Routes:
 *   GET  /approval                        → index()
 *   GET  /approval/view/{type}/{id}       → view($type, $id)
 *   POST /approval/approve/{type}/{id}    → approve($type, $id)
 *   POST /approval/enact/{type}/{id}      → enact($type, $id)
 *   POST /approval/reject/{type}/{id}     → reject($type, $id)
 */

class ApprovalController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — All endorsed documents awaiting approval
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $db = \Database::getInstance()->getConnection();

        // Endorsed ordinances
        $stmtOrd = $db->prepare(
            "SELECT o.id, o.ordinance_no AS doc_no, o.title, o.subject,
                    o.status, o.date_filed, o.created_at,
                    u.name AS author_name, 'ordinance' AS doc_type
             FROM ordinances o
             LEFT JOIN users u ON o.author_id = u.id
             WHERE o.status IN ('endorsed','approved')
             ORDER BY o.created_at ASC"
        );
        $stmtOrd->execute();
        $ordinances = $stmtOrd->fetchAll(PDO::FETCH_ASSOC);

        // Endorsed resolutions
        $stmtRes = $db->prepare(
            "SELECT r.id, r.resolution_no AS doc_no, r.title, r.subject,
                    r.status, r.date_filed, r.created_at,
                    u.name AS author_name, 'resolution' AS doc_type
             FROM resolutions r
             LEFT JOIN users u ON r.author_id = u.id
             WHERE r.status IN ('endorsed','approved')
             ORDER BY r.created_at ASC"
        );
        $stmtRes->execute();
        $resolutions = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

        $endorsed = array_merge($ordinances, $resolutions);
        usort($endorsed, fn($a, $b) => strtotime($a['created_at']) - strtotime($b['created_at']));

        $this->render('approval/index', [
            'pageTitle' => 'Approval and Enactment',
            'endorsed'  => $endorsed,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VIEW — Review endorsed document for approval decision
    // ─────────────────────────────────────────────────────────────────────────

    public function view(string $type, string $id): void
    {
        if (!in_array($type, ['ordinance', 'resolution'])) {
            $this->flash('error', 'Invalid document type.');
            $this->redirect('approval');
        }

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->getByIdWithAuthor((int) $id);

        if (!$document) {
            $this->flash('error', ucfirst($type) . ' not found.');
            $this->redirect('approval');
        }

        // Latest AI validation report
        $aiModel  = $this->model('AiValidationModel');
        $aiReport = $aiModel->getLatestForDocument($type, (int) $id);

        // Review log history
        $db   = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "SELECT rl.*, u.name AS reviewer_name
             FROM review_logs rl
             LEFT JOIN users u ON rl.reviewed_by = u.id
             WHERE rl.document_type = :type AND rl.document_id = :id
             ORDER BY rl.created_at DESC"
        );
        $stmt->execute([':type' => $type, ':id' => (int) $id]);
        $reviewHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('approval/view', [
            'pageTitle'     => 'Approval: ' . ($document['ordinance_no'] ?? $document['resolution_no'] ?? 'Document'),
            'document'      => $document,
            'docType'       => $type,
            'aiReport'      => $aiReport,
            'reviewHistory' => $reviewHistory,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // APPROVE — Approve the endorsed document
    // ─────────────────────────────────────────────────────────────────────────

    public function approve(string $type, string $id): void
    {
        $this->requireRole([ROLE_SUPER_ADMIN]);

        if (!$this->isPost()) {
            $this->redirect('approval/view/' . $type . '/' . $id);
        }

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->findById((int) $id);

        if (!$document || $document['status'] !== STATUS_ENDORSED) {
            $this->flash('error', 'Only endorsed documents can be approved.');
            $this->redirect('approval');
        }

        $model->updateStatus((int) $id, STATUS_APPROVED);

        $this->logApproval($type, (int) $id, 'approved', null);

        $userModel = $this->model('UserModel');
        $userModel->logAudit(
            $this->userId(), 'APPROVE', $type . 's', (int) $id,
            ['status' => STATUS_ENDORSED], ['status' => STATUS_APPROVED]
        );

        $docNo = $document['ordinance_no'] ?? $document['resolution_no'] ?? '#' . $id;
        $this->flash('success', $docNo . ' has been approved. Ready for enactment.');
        $this->redirect('approval');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ENACT — Enact the approved document (final legislative action)
    // ─────────────────────────────────────────────────────────────────────────

    public function enact(string $type, string $id): void
    {
        $this->requireRole([ROLE_SUPER_ADMIN]);

        if (!$this->isPost()) {
            $this->redirect('approval/view/' . $type . '/' . $id);
        }

        $effectiveDate = trim($this->post('effective_date', date('Y-m-d')));

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->findById((int) $id);

        if (!$document || $document['status'] !== STATUS_APPROVED) {
            $this->flash('error', 'Only approved documents can be enacted.');
            $this->redirect('approval');
        }

        $model->updateStatus((int) $id, STATUS_ENACTED);

        $this->logApproval($type, (int) $id, 'enacted', 'Effective date: ' . $effectiveDate);

        $userModel = $this->model('UserModel');
        $userModel->logAudit(
            $this->userId(), 'ENACT', $type . 's', (int) $id,
            ['status' => STATUS_APPROVED],
            ['status' => STATUS_ENACTED, 'effective_date' => $effectiveDate]
        );

        $docNo = $document['ordinance_no'] ?? $document['resolution_no'] ?? '#' . $id;
        $this->flash('success', $docNo . ' has been enacted successfully and is now in effect.');
        $this->redirect('approval');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REJECT — Reject at approval stage (moves to Archive)
    // ─────────────────────────────────────────────────────────────────────────

    public function reject(string $type, string $id): void
    {
        $this->requireRole([ROLE_SUPER_ADMIN]);

        if (!$this->isPost()) {
            $this->redirect('approval/view/' . $type . '/' . $id);
        }

        $reason = trim($this->post('reason', ''));

        if (empty($reason)) {
            $this->flash('error', 'A rejection reason is required.');
            $this->redirect('approval/view/' . $type . '/' . $id);
        }

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->findById((int) $id);

        if (!$document || !in_array($document['status'], [STATUS_ENDORSED, STATUS_APPROVED])) {
            $this->flash('error', 'This document cannot be rejected at its current status.');
            $this->redirect('approval');
        }

        $model->updateStatus((int) $id, STATUS_REJECTED);

        $this->logApproval($type, (int) $id, 'rejected', $reason);

        $userModel = $this->model('UserModel');
        $userModel->logAudit(
            $this->userId(), 'REJECT', $type . 's', (int) $id,
            ['status' => $document['status']],
            ['status' => STATUS_REJECTED, 'reason' => $reason]
        );

        $docNo = $document['ordinance_no'] ?? $document['resolution_no'] ?? '#' . $id;
        $this->flash('success', $docNo . ' has been rejected and moved to the Archive.');
        $this->redirect('approval');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE: LOG APPROVAL ACTION
    // ─────────────────────────────────────────────────────────────────────────

    private function logApproval(string $type, int $id, string $action, ?string $reason): void
    {
        $db   = \Database::getInstance()->getConnection();
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
