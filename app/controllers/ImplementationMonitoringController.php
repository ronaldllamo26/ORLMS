<?php

/**
 * ORLMS - Implementation Monitoring Controller
 *
 * Tracks the implementation status of enacted and published ordinances/resolutions.
 * Any logged-in user can view. Logging new entries requires at least legislative_staff.
 *
 * Routes:
 *   GET  /implementation_monitoring                   → index()
 *   GET  /implementation_monitoring/view/{type}/{id}  → view($type, $id)
 *   POST /implementation_monitoring/log/{type}/{id}   → log($type, $id)
 */

class ImplementationMonitoringController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — All enacted/published documents with latest monitoring status
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $db = \Database::getInstance()->getConnection();

        // Enacted/published ordinances with their latest monitoring status
        $stmtOrd = $db->prepare(
            "SELECT o.id, o.ordinance_no AS doc_no, o.title, o.subject,
                    o.status, o.date_filed, u.name AS author_name,
                    'ordinance' AS doc_type,
                    ml.implementation_status AS latest_impl_status,
                    ml.implementation_notes  AS latest_notes,
                    ml.logged_at             AS last_logged_at,
                    lu.name                  AS last_logged_by
             FROM ordinances o
             LEFT JOIN users u ON o.author_id = u.id
             LEFT JOIN monitoring_logs ml
                 ON ml.document_type = 'ordinance'
                 AND ml.document_id = o.id
                 AND ml.id = (
                     SELECT MAX(m2.id) FROM monitoring_logs m2
                     WHERE m2.document_type = 'ordinance'
                     AND m2.document_id = o.id
                 )
             LEFT JOIN users lu ON ml.logged_by = lu.id
             WHERE o.status IN ('enacted','published')
             ORDER BY o.updated_at DESC"
        );
        $stmtOrd->execute();

        $stmtRes = $db->prepare(
            "SELECT r.id, r.resolution_no AS doc_no, r.title, r.subject,
                    r.status, r.date_filed, u.name AS author_name,
                    'resolution' AS doc_type,
                    ml.implementation_status AS latest_impl_status,
                    ml.implementation_notes  AS latest_notes,
                    ml.logged_at             AS last_logged_at,
                    lu.name                  AS last_logged_by
             FROM resolutions r
             LEFT JOIN users u ON r.author_id = u.id
             LEFT JOIN monitoring_logs ml
                 ON ml.document_type = 'resolution'
                 AND ml.document_id = r.id
                 AND ml.id = (
                     SELECT MAX(m2.id) FROM monitoring_logs m2
                     WHERE m2.document_type = 'resolution'
                     AND m2.document_id = r.id
                 )
             LEFT JOIN users lu ON ml.logged_by = lu.id
             WHERE r.status IN ('enacted','published')
             ORDER BY r.updated_at DESC"
        );
        $stmtRes->execute();

        $documents = array_merge(
            $stmtOrd->fetchAll(PDO::FETCH_ASSOC),
            $stmtRes->fetchAll(PDO::FETCH_ASSOC)
        );

        // Summary counts by implementation status
        $summary = ['pending' => 0, 'ongoing' => 0, 'completed' => 0, 'delayed' => 0, 'no_log' => 0];
        foreach ($documents as $doc) {
            $status = $doc['latest_impl_status'] ?? null;
            if ($status && isset($summary[$status])) {
                $summary[$status]++;
            } else {
                $summary['no_log']++;
            }
        }

        $this->render('implementation_monitoring/index', [
            'pageTitle' => 'Implementation Monitoring',
            'documents' => $documents,
            'summary'   => $summary,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VIEW — Monitoring history for a specific document
    // ─────────────────────────────────────────────────────────────────────────

    public function view(string $type, string $id): void
    {
        if (!in_array($type, ['ordinance', 'resolution'])) {
            $this->flash('error', 'Invalid document type.');
            $this->redirect('implementation_monitoring');
        }

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->getByIdWithAuthor((int) $id);

        if (!$document) {
            $this->flash('error', ucfirst($type) . ' not found.');
            $this->redirect('implementation_monitoring');
        }

        if (!in_array($document['status'], ['enacted', 'published'])) {
            $this->flash('error', 'Only enacted or published documents can be monitored.');
            $this->redirect('implementation_monitoring');
        }

        // Fetch monitoring log history
        $db   = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "SELECT ml.*, u.name AS logged_by_name
             FROM monitoring_logs ml
             LEFT JOIN users u ON ml.logged_by = u.id
             WHERE ml.document_type = :type AND ml.document_id = :id
             ORDER BY ml.logged_at DESC"
        );
        $stmt->execute([':type' => $type, ':id' => (int) $id]);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('implementation_monitoring/view', [
            'pageTitle' => 'Monitoring: ' . ($document['ordinance_no'] ?? $document['resolution_no'] ?? 'Document'),
            'document'  => $document,
            'docType'   => $type,
            'logs'      => $logs,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOG — Add a new monitoring log entry
    // ─────────────────────────────────────────────────────────────────────────

    public function log(string $type, string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('implementation_monitoring/view/' . $type . '/' . $id);
        }

        if (!in_array($type, ['ordinance', 'resolution'])) {
            $this->flash('error', 'Invalid document type.');
            $this->redirect('implementation_monitoring');
        }

        $status = trim($this->post('implementation_status', ''));
        $notes  = trim($this->post('implementation_notes', ''));

        if (!in_array($status, ['pending', 'ongoing', 'completed', 'delayed'])) {
            $this->flash('error', 'Please select a valid implementation status.');
            $this->redirect('implementation_monitoring/view/' . $type . '/' . $id);
        }

        if (empty($notes)) {
            $this->flash('error', 'Implementation notes are required.');
            $this->redirect('implementation_monitoring/view/' . $type . '/' . $id);
        }

        $db   = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "INSERT INTO monitoring_logs
             (document_type, document_id, implementation_status, implementation_notes, logged_by)
             VALUES (:type, :id, :status, :notes, :user)"
        );
        $stmt->execute([
            ':type'   => $type,
            ':id'     => (int) $id,
            ':status' => $status,
            ':notes'  => $notes,
            ':user'   => $this->userId(),
        ]);

        // Audit log
        $userModel = $this->model('UserModel');
        $userModel->logAudit(
            $this->userId(), 'LOG_MONITORING', $type . 's', (int) $id,
            null,
            ['status' => $status, 'notes' => substr($notes, 0, 100)]
        );

        $this->flash('success', 'Implementation status updated successfully.');
        $this->redirect('implementation_monitoring/view/' . $type . '/' . $id);
    }
}
