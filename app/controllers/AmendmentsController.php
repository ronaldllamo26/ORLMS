<?php

/**
 * ORLMS - Amendments & Revisions Controller
 *
 * Manages amendments to enacted and published ordinances/resolutions.
 *
 * Workflow:
 *   Create (draft) → Submit → Approve or Reject
 *
 * Routes:
 *   GET  /amendments                      → index()
 *   GET  /amendments/view/{id}            → view($id)
 *   GET  /amendments/create/{type}/{docId}→ create($type, $docId)
 *   POST /amendments/create/{type}/{docId}→ create($type, $docId)
 *   POST /amendments/submit/{id}          → submit($id)
 *   POST /amendments/approve/{id}         → approve($id)
 *   POST /amendments/reject/{id}          → reject($id)
 */

class AmendmentsController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — All amendments with filters
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $db = \Database::getInstance()->getConnection();

        $stmt = $db->prepare(
            "SELECT a.*,
                    u.name AS amended_by_name,
                    CASE a.document_type
                        WHEN 'ordinance'  THEN o.ordinance_no
                        WHEN 'resolution' THEN r.resolution_no
                    END AS doc_no,
                    CASE a.document_type
                        WHEN 'ordinance'  THEN o.title
                        WHEN 'resolution' THEN r.title
                    END AS doc_title
             FROM amendments a
             LEFT JOIN users u ON a.amended_by = u.id
             LEFT JOIN ordinances o ON a.document_type='ordinance' AND a.document_id=o.id
             LEFT JOIN resolutions r ON a.document_type='resolution' AND a.document_id=r.id
             ORDER BY a.amended_at DESC"
        );
        $stmt->execute();
        $amendments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Summary counts
        $summary = ['draft' => 0, 'submitted' => 0, 'approved' => 0, 'rejected' => 0];
        foreach ($amendments as $am) {
            $summary[$am['status']] = ($summary[$am['status']] ?? 0) + 1;
        }

        // Enacted/published docs for "Create Amendment" quick-links
        $stmtDocs = $db->prepare(
            "SELECT id, ordinance_no AS doc_no, title, 'ordinance' AS doc_type
             FROM ordinances WHERE status IN ('enacted','published')
             UNION ALL
             SELECT id, resolution_no AS doc_no, title, 'resolution' AS doc_type
             FROM resolutions WHERE status IN ('enacted','published')
             ORDER BY doc_no ASC"
        );
        $stmtDocs->execute();
        $enactedDocs = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);

        $this->render('amendments/index', [
            'pageTitle'   => 'Amendments & Revisions',
            'amendments'  => $amendments,
            'summary'     => $summary,
            'enactedDocs' => $enactedDocs,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VIEW — Amendment detail
    // ─────────────────────────────────────────────────────────────────────────

    public function view(string $id): void
    {
        $db   = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "SELECT a.*,
                    u.name AS amended_by_name,
                    CASE a.document_type
                        WHEN 'ordinance'  THEN o.ordinance_no
                        WHEN 'resolution' THEN r.resolution_no
                    END AS doc_no,
                    CASE a.document_type
                        WHEN 'ordinance'  THEN o.title
                        WHEN 'resolution' THEN r.title
                    END AS doc_title,
                    CASE a.document_type
                        WHEN 'ordinance'  THEN o.status
                        WHEN 'resolution' THEN r.status
                    END AS doc_status
             FROM amendments a
             LEFT JOIN users u ON a.amended_by = u.id
             LEFT JOIN ordinances o ON a.document_type='ordinance' AND a.document_id=o.id
             LEFT JOIN resolutions r ON a.document_type='resolution' AND a.document_id=r.id
             WHERE a.id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => (int) $id]);
        $amendment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$amendment) {
            $this->flash('error', 'Amendment not found.');
            $this->redirect('amendments');
        }

        $userRole = $_SESSION['user_role'] ?? '';
        $this->render('amendments/view', [
            'pageTitle' => 'Amendment ' . ($amendment['amendment_no'] ?? '#' . $id),
            'amendment' => $amendment,
            'userRole'  => $userRole,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE — Draft a new amendment for an enacted document
    // ─────────────────────────────────────────────────────────────────────────

    public function create(string $type, string $docId): void
    {
        if (!in_array($type, ['ordinance', 'resolution'])) {
            $this->flash('error', 'Invalid document type.');
            $this->redirect('amendments');
        }

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->getByIdWithAuthor((int) $docId);

        if (!$document || !in_array($document['status'], ['enacted', 'published'])) {
            $this->flash('error', 'Amendments can only be created for enacted or published documents.');
            $this->redirect('amendments');
        }

        $errors = [];
        $input  = [];

        if ($this->isPost()) {
            $input = [
                'amendment_no' => trim($this->post('amendment_no', '')),
                'description'  => trim($this->post('description', '')),
                'changes'      => trim($this->post('changes', '')),
            ];

            if (empty($input['description'])) {
                $errors['description'] = 'Amendment description is required.';
            }
            if (empty($input['changes'])) {
                $errors['changes'] = 'Please describe the specific changes being made.';
            }

            // Auto-generate amendment_no if blank
            if (empty($input['amendment_no'])) {
                $db  = \Database::getInstance()->getConnection();
                $st  = $db->prepare(
                    "SELECT COUNT(*) FROM amendments
                     WHERE document_type = :type AND document_id = :id"
                );
                $st->execute([':type' => $type, ':id' => (int) $docId]);
                $count = (int) $st->fetchColumn();
                $noField = $type === 'ordinance' ? 'ordinance_no' : 'resolution_no';
                $input['amendment_no'] = 'AMEND-' . ($document[$noField] ?? '') . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            }

            if (empty($errors)) {
                $db   = \Database::getInstance()->getConnection();
                $stmt = $db->prepare(
                    "INSERT INTO amendments
                     (document_type, document_id, amendment_no, description, changes, status, amended_by)
                     VALUES (:type, :docid, :no, :desc, :changes, 'draft', :user)"
                );
                $stmt->execute([
                    ':type'    => $type,
                    ':docid'   => (int) $docId,
                    ':no'      => $input['amendment_no'],
                    ':desc'    => $input['description'],
                    ':changes' => $input['changes'],
                    ':user'    => $this->userId(),
                ]);
                $newId = $db->lastInsertId();

                $userModel = $this->model('UserModel');
                $userModel->logAudit(
                    $this->userId(), 'CREATE_AMENDMENT', 'amendments', (int) $newId,
                    null,
                    ['amendment_no' => $input['amendment_no'], 'document' => $document['ordinance_no'] ?? $document['resolution_no'] ?? '']
                );

                $this->flash('success', 'Amendment "' . $input['amendment_no'] . '" created as draft.');
                $this->redirect('amendments/view/' . $newId);
            }
        }

        $this->render('amendments/create', [
            'pageTitle' => 'New Amendment',
            'document'  => $document,
            'docType'   => $type,
            'errors'    => $errors,
            'input'     => $input,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUBMIT — Submit a draft amendment for approval
    // ─────────────────────────────────────────────────────────────────────────

    public function submit(string $id): void
    {
        if (!$this->isPost()) { $this->redirect('amendments'); }

        $am = $this->getAmendment((int) $id);
        if (!$am || $am['status'] !== 'draft') {
            $this->flash('error', 'Only draft amendments can be submitted.');
            $this->redirect('amendments/view/' . $id);
        }

        $this->updateAmendmentStatus((int) $id, 'submitted');
        $this->logAmendmentAction('SUBMIT_AMENDMENT', (int) $id, 'draft', 'submitted');
        $this->flash('success', 'Amendment submitted for approval.');
        $this->redirect('amendments/view/' . $id);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // APPROVE — Approve a submitted amendment
    // ─────────────────────────────────────────────────────────────────────────

    public function approve(string $id): void
    {
        $this->requireRole([ROLE_SUPER_ADMIN]);
        if (!$this->isPost()) { $this->redirect('amendments'); }

        $am = $this->getAmendment((int) $id);
        if (!$am || $am['status'] !== 'submitted') {
            $this->flash('error', 'Only submitted amendments can be approved.');
            $this->redirect('amendments/view/' . $id);
        }

        $this->updateAmendmentStatus((int) $id, 'approved');
        $this->logAmendmentAction('APPROVE_AMENDMENT', (int) $id, 'submitted', 'approved');
        $this->flash('success', 'Amendment approved successfully.');
        $this->redirect('amendments/view/' . $id);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REJECT — Reject a submitted amendment
    // ─────────────────────────────────────────────────────────────────────────

    public function reject(string $id): void
    {
        $this->requireRole([ROLE_SUPER_ADMIN]);
        if (!$this->isPost()) { $this->redirect('amendments'); }

        $reason = trim($this->post('reason', ''));
        if (empty($reason)) {
            $this->flash('error', 'Rejection reason is required.');
            $this->redirect('amendments/view/' . $id);
        }

        $am = $this->getAmendment((int) $id);
        if (!$am || $am['status'] !== 'submitted') {
            $this->flash('error', 'Only submitted amendments can be rejected.');
            $this->redirect('amendments/view/' . $id);
        }

        $this->updateAmendmentStatus((int) $id, 'rejected');
        $this->logAmendmentAction('REJECT_AMENDMENT', (int) $id, 'submitted', 'rejected', $reason);
        $this->flash('success', 'Amendment rejected.');
        $this->redirect('amendments/view/' . $id);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function getAmendment(int $id): array|false
    {
        $db   = \Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM amendments WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function updateAmendmentStatus(int $id, string $status): void
    {
        $db   = \Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE amendments SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $id]);
    }

    private function logAmendmentAction(string $action, int $id, string $from, string $to, string $reason = ''): void
    {
        $userModel = $this->model('UserModel');
        $new = ['status' => $to];
        if ($reason) $new['reason'] = $reason;
        $userModel->logAudit(
            $this->userId(), $action, 'amendments', $id,
            ['status' => $from],
            $new
        );
    }
}
