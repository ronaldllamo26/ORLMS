<?php

/**
 * ORLMS - Archive Controller
 *
 * Displays all rejected and archived documents.
 * Archive is read-only — no editing, no deletion, no status changes.
 *
 * Routes:
 *   GET /archive                      → index()
 *   GET /archive/view/{type}/{id}     → view($type, $id)
 */

class ArchiveController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — List all archived/rejected documents
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $db = \Database::getInstance()->getConnection();

        // Rejected/archived ordinances
        $stmtOrd = $db->prepare(
            "SELECT o.id, o.ordinance_no AS doc_no, o.title, o.subject,
                    o.status, o.date_filed, o.created_at,
                    u.name AS author_name,
                    'ordinance' AS doc_type,
                    rl.reason AS rejection_reason,
                    rl.created_at AS rejected_at,
                    ru.name AS rejected_by_name
             FROM ordinances o
             LEFT JOIN users u ON o.author_id = u.id
             LEFT JOIN review_logs rl ON rl.document_type = 'ordinance'
                 AND rl.document_id = o.id
                 AND rl.action = 'rejected'
                 AND rl.id = (
                     SELECT MAX(r2.id) FROM review_logs r2
                     WHERE r2.document_type = 'ordinance'
                     AND r2.document_id = o.id
                     AND r2.action = 'rejected'
                 )
             LEFT JOIN users ru ON rl.reviewed_by = ru.id
             WHERE o.status IN ('rejected','archived')
             ORDER BY o.created_at DESC"
        );
        $stmtOrd->execute();
        $archivedOrdinances = $stmtOrd->fetchAll(PDO::FETCH_ASSOC);

        // Rejected/archived resolutions
        $stmtRes = $db->prepare(
            "SELECT r.id, r.resolution_no AS doc_no, r.title, r.subject,
                    r.status, r.date_filed, r.created_at,
                    u.name AS author_name,
                    'resolution' AS doc_type,
                    rl.reason AS rejection_reason,
                    rl.created_at AS rejected_at,
                    ru.name AS rejected_by_name
             FROM resolutions r
             LEFT JOIN users u ON r.author_id = u.id
             LEFT JOIN review_logs rl ON rl.document_type = 'resolution'
                 AND rl.document_id = r.id
                 AND rl.action = 'rejected'
                 AND rl.id = (
                     SELECT MAX(r2.id) FROM review_logs r2
                     WHERE r2.document_type = 'resolution'
                     AND r2.document_id = r.id
                     AND r2.action = 'rejected'
                 )
             LEFT JOIN users ru ON rl.reviewed_by = ru.id
             WHERE r.status IN ('rejected','archived')
             ORDER BY r.created_at DESC"
        );
        $stmtRes->execute();
        $archivedResolutions = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

        // Merge all archived docs
        $archived = array_merge($archivedOrdinances, $archivedResolutions);
        usort($archived, fn($a, $b) =>
            strtotime($b['rejected_at'] ?? $b['created_at'])
            - strtotime($a['rejected_at'] ?? $a['created_at'])
        );

        $this->render('archive/index', [
            'pageTitle' => 'Archive',
            'archived'  => $archived,
            'totalOrd'  => count($archivedOrdinances),
            'totalRes'  => count($archivedResolutions),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VIEW — View a single archived document (read-only)
    // ─────────────────────────────────────────────────────────────────────────

    public function view(string $type, string $id): void
    {
        if (!in_array($type, ['ordinance', 'resolution'])) {
            $this->flash('error', 'Invalid document type.');
            $this->redirect('archive');
        }

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->getByIdWithAuthor((int) $id);

        if (!$document) {
            $this->flash('error', ucfirst($type) . ' not found.');
            $this->redirect('archive');
        }

        // Ensure only archived/rejected docs are accessible here
        if (!in_array($document['status'], ['rejected', 'archived'])) {
            $this->flash('error', 'This document is not in the Archive.');
            $this->redirect($type . '/view/' . $id);
        }

        // Fetch full workflow history
        $db   = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "SELECT rl.*, u.name AS reviewer_name
             FROM review_logs rl
             LEFT JOIN users u ON rl.reviewed_by = u.id
             WHERE rl.document_type = :type AND rl.document_id = :id
             ORDER BY rl.created_at ASC"
        );
        $stmt->execute([':type' => $type, ':id' => (int) $id]);
        $workflowHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get the rejection entry specifically
        $rejectionEntry = null;
        foreach (array_reverse($workflowHistory) as $log) {
            if ($log['action'] === 'rejected') {
                $rejectionEntry = $log;
                break;
            }
        }

        // Get AI report if any
        $aiModel  = $this->model('AiValidationModel');
        $aiReport = $aiModel->getLatestForDocument($type, (int) $id);

        $this->render('archive/view', [
            'pageTitle'       => 'Archive: ' . ($document['ordinance_no'] ?? $document['resolution_no'] ?? 'Document'),
            'document'        => $document,
            'docType'         => $type,
            'workflowHistory' => $workflowHistory,
            'rejectionEntry'  => $rejectionEntry,
            'aiReport'        => $aiReport,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OVERRIDE — 2/3 Override of Committee Rejection
    // ─────────────────────────────────────────────────────────────────────────

    public function override(string $type, string $id): void
    {
        $this->requireRole([ROLE_LEGISLATIVE_STAFF, ROLE_SUPER_ADMIN]);

        if (!$this->isPost()) {
            $this->redirect('archive/view/' . $type . '/' . $id);
        }

        $reason = trim($this->post('override_reason', ''));

        if (empty($reason)) {
            $this->flash('error', 'A reason or minutes of meeting reference is required to override.');
            $this->redirect('archive/view/' . $type . '/' . $id);
        }

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->findById((int) $id);

        if (!$document) {
            $this->flash('error', ucfirst($type) . ' not found.');
            $this->redirect('archive');
        }

        if ($document['status'] !== 'rejected') {
            $this->flash('error', 'Only rejected documents can be overridden.');
            $this->redirect('archive/view/' . $type . '/' . $id);
        }

        // Send back to under_review or endorsed? Usually, an override pushes it to the next step (endorsed) or back to review.
        // We will send it to 'endorsed' (approved by override) so it proceeds to 2nd reading.
        $model->updateStatus((int) $id, STATUS_ENDORSED);

        // Log the override action
        $db = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "INSERT INTO review_logs
             (document_type, document_id, action, reason, reviewed_by)
             VALUES (:type, :id, 'override_2_3', :reason, :user)"
        );
        $stmt->execute([
            ':type'   => $type,
            ':id'     => (int) $id,
            ':reason' => '2/3 Override: ' . $reason,
            ':user'   => $this->userId(),
        ]);

        $userModel = $this->model('UserModel');
        $userModel->logAudit(
            $this->userId(), 'OVERRIDE', $type . 's', (int) $id,
            ['status' => $document['status']],
            ['status' => STATUS_ENDORSED, 'reason' => $reason]
        );

        $this->flash('success', 'Committee decision overridden successfully. Document moved to Endorsed state.');
        $this->redirect('approval'); // Usually goes to 2nd reading / approval stage next
    }
}
