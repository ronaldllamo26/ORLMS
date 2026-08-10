<?php

/**
 * ORLMS - Resolution Controller
 *
 * Routes:
 *   GET  /resolution              → index()
 *   GET  /resolution/view/{id}    → view($id)
 *   GET  /resolution/create       → create()
 *   POST /resolution/create       → create()
 *   GET  /resolution/edit/{id}    → edit($id)
 *   POST /resolution/edit/{id}    → edit($id)
 *   GET  /resolution/submit/{id}  → submit($id)
 *   GET  /resolution/delete/{id}  → delete($id)
 */

class ResolutionController extends Controller
{
    /** @var ResolutionModel */
    private ResolutionModel $resolutionModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->resolutionModel = $this->model('ResolutionModel');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $resolutions = $this->resolutionModel->getAllWithAuthor();

        $this->render('resolutions/index', [
            'pageTitle'   => 'Resolutions',
            'resolutions' => $resolutions,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VIEW
    // ─────────────────────────────────────────────────────────────────────────

    public function view(string $id): void
    {
        $resolution = $this->resolutionModel->getByIdWithAuthor((int) $id);

        if (!$resolution) {
            $this->flash('error', 'Resolution not found.');
            $this->redirect('resolution');
        }

        // Fetch the latest AI validation report for this resolution
        $aiModel  = $this->model('AiValidationModel');
        $aiReport = $aiModel->getLatestForDocument('resolution', (int) $id);

        // Fetch review/approval history
        $db   = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "SELECT rl.*, u.name AS reviewer_name
             FROM review_logs rl
             LEFT JOIN users u ON rl.reviewed_by = u.id
             WHERE rl.document_type = 'resolution' AND rl.document_id = :id
             ORDER BY rl.created_at DESC"
        );
        $stmt->execute([':id' => (int) $id]);
        $reviewHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch all active committees for the referral dropdown
        $stmtComm = $db->prepare("SELECT id, name FROM committees WHERE is_active = TRUE ORDER BY name ASC");
        $stmtComm->execute();
        $committees = $stmtComm->fetchAll(PDO::FETCH_ASSOC);

        $this->render('resolutions/view', [
            'pageTitle'     => $resolution['resolution_no'] ?? 'View Resolution',
            'resolution'    => $resolution,
            'aiReport'      => $aiReport,
            'reviewHistory' => $reviewHistory,
            'committees'    => $committees, // Pass committees list
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────────────────────

    public function create(): void
    {
        $this->requireRole([ROLE_LEGISLATIVE_STAFF, ROLE_SUPER_ADMIN]);

        $errors = [];
        $input  = [];

        if ($this->isPost()) {
            $input = [
                'title'      => $this->post('title', ''),
                'subject'    => $this->post('subject', ''),
                'content'    => $this->post('content', ''),
                'date_filed' => $this->post('date_filed', date('Y-m-d')),
            ];

            if (empty($input['title']))   { $errors['title']   = 'Title is required.'; }
            if (empty($input['subject'])) { $errors['subject'] = 'Subject is required.'; }
            if (empty($input['content'])) { $errors['content'] = 'Document content is required.'; }

            $filePath = null;
            if (!empty($_FILES['document_file']['name'])) {
                $uploadResult = $this->handleFileUpload($_FILES['document_file']);
                if ($uploadResult['success']) {
                    $filePath = $uploadResult['path'];
                } else {
                    $errors['file'] = $uploadResult['error'];
                }
            }

            if (empty($errors)) {
                $resolutionNo = $this->resolutionModel->generateResolutionNo();

                $newId = $this->resolutionModel->insert([
                    'resolution_no' => $resolutionNo,
                    'title'         => $input['title'],
                    'subject'       => $input['subject'],
                    'content'       => $input['content'],
                    'author_id'     => $this->userId(),
                    'status'        => STATUS_DRAFT,
                    'file_path'     => $filePath,
                    'date_filed'    => $input['date_filed'],
                ]);

                if ($newId) {
                    $userModel = $this->model('UserModel');
                    $userModel->logAudit(
                        $this->userId(), 'CREATE', 'resolutions',
                        (int) $newId, null,
                        ['resolution_no' => $resolutionNo, 'title' => $input['title']]
                    );

                    $this->flash('success', 'Resolution ' . $resolutionNo . ' created successfully.');
                    $this->redirect('resolution/view/' . $newId);
                } else {
                    $errors['general'] = 'Failed to save resolution. Please try again.';
                }
            }
        }

        $this->render('resolutions/create', [
            'pageTitle' => 'New Resolution',
            'errors'    => $errors,
            'input'     => $input,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────────────────────

    public function edit(string $id): void
    {
        $this->requireRole([ROLE_LEGISLATIVE_STAFF, ROLE_SUPER_ADMIN]);

        $resolution = $this->resolutionModel->findById((int) $id);

        if (!$resolution) {
            $this->flash('error', 'Resolution not found.');
            $this->redirect('resolution');
        }

        if ($resolution['status'] !== STATUS_DRAFT) {
            $this->flash('error', 'Only draft resolutions can be edited.');
            $this->redirect('resolution/view/' . $id);
        }

        $errors = [];
        $input  = $resolution;

        if ($this->isPost()) {
            $input = [
                'title'      => $this->post('title', ''),
                'subject'    => $this->post('subject', ''),
                'content'    => $this->post('content', ''),
                'date_filed' => $this->post('date_filed', $resolution['date_filed']),
            ];

            if (empty($input['title']))   { $errors['title']   = 'Title is required.'; }
            if (empty($input['subject'])) { $errors['subject'] = 'Subject is required.'; }
            if (empty($input['content'])) { $errors['content'] = 'Document content is required.'; }

            if (!empty($_FILES['document_file']['name'])) {
                $uploadResult = $this->handleFileUpload($_FILES['document_file']);
                if ($uploadResult['success']) {
                    $input['file_path'] = $uploadResult['path'];
                } else {
                    $errors['file'] = $uploadResult['error'];
                }
            }

            if (empty($errors)) {
                $updated = $this->resolutionModel->update((int) $id, [
                    'title'      => $input['title'],
                    'subject'    => $input['subject'],
                    'content'    => $input['content'],
                    'date_filed' => $input['date_filed'],
                    'file_path'  => $input['file_path'] ?? $resolution['file_path'],
                ]);

                if ($updated) {
                    $userModel = $this->model('UserModel');
                    $userModel->logAudit(
                        $this->userId(), 'UPDATE', 'resolutions', (int) $id,
                        ['title' => $resolution['title']],
                        ['title' => $input['title']]
                    );
                    $this->flash('success', 'Resolution updated successfully.');
                    $this->redirect('resolution/view/' . $id);
                } else {
                    $errors['general'] = 'Failed to update resolution. Please try again.';
                }
            }
        }

        $this->render('resolutions/edit', [
            'pageTitle'  => 'Edit Resolution',
            'resolution' => $resolution,
            'errors'     => $errors,
            'input'      => $input,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUBMIT
    // ─────────────────────────────────────────────────────────────────────────

    public function submit(string $id): void
    {
        $this->requireRole([ROLE_LEGISLATIVE_STAFF, ROLE_SUPER_ADMIN]);

        $resolution = $this->resolutionModel->findById((int) $id);

        if (!$resolution) {
            $this->flash('error', 'Resolution not found.');
            $this->redirect('resolution');
        }

        if ($resolution['status'] !== STATUS_DRAFT) {
            $this->flash('error', 'Only draft resolutions can be submitted for review.');
            $this->redirect('resolution/view/' . $id);
        }

        $updated = $this->resolutionModel->updateStatus((int) $id, STATUS_PENDING);

        if ($updated) {
            $userModel = $this->model('UserModel');
            $userModel->logAudit(
                $this->userId(), 'SUBMIT', 'resolutions', (int) $id,
                ['status' => STATUS_DRAFT], ['status' => STATUS_PENDING]
            );

            // Auto-trigger AI Validation if API key is set
            $aiReportMsg = '';
            if (!empty(GROQ_API_KEY) && GROQ_API_KEY !== 'your_groq_api_key_here') {
                $aiModel = $this->model('AiValidationModel');
                $reportId = $aiModel->runValidation('resolution', (int) $id, $this->userId());
                if ($reportId) {
                    $userModel->logAudit(
                        $this->userId(),
                        'AI_VALIDATE',
                        'resolutions',
                        (int) $id,
                        null,
                        ['report_id' => $reportId, 'triggered_on' => 'submit']
                    );
                    $aiReportMsg = ' AI Validation report has been automatically generated.';
                }
            }

            $this->flash('success', 'Resolution submitted for review successfully.' . $aiReportMsg);
        } else {
            $this->flash('error', 'Failed to submit resolution. Please try again.');
        }

        $this->redirect('resolution/view/' . $id);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────────────────────────────────

    public function delete(string $id): void
    {
        $this->requireRole([ROLE_LEGISLATIVE_STAFF, ROLE_SUPER_ADMIN]);

        $resolution = $this->resolutionModel->findById((int) $id);

        if (!$resolution || $resolution['status'] !== STATUS_DRAFT) {
            $this->flash('error', 'Only draft resolutions may be deleted.');
            $this->redirect('resolution');
        }

        $deleted = $this->resolutionModel->delete((int) $id);

        if ($deleted) {
            $userModel = $this->model('UserModel');
            $userModel->logAudit(
                $this->userId(), 'DELETE', 'resolutions', (int) $id,
                ['resolution_no' => $resolution['resolution_no'], 'title' => $resolution['title']],
                null
            );
            $this->flash('success', 'Draft resolution deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete resolution.');
        }

        $this->redirect('resolution');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REFER — Refer resolution to a committee (POST)
    // ─────────────────────────────────────────────────────────────────────────
    public function refer(string $id): void
    {
        $this->requireRole([ROLE_LEGISLATIVE_STAFF, ROLE_SUPER_ADMIN]);

        if (!$this->isPost()) {
            $this->redirect('resolution/view/' . $id);
        }

        $committeeId = (int) $this->post('committee_id');
        if (!$committeeId) {
            $this->flash('error', 'Please select a valid committee.');
            $this->redirect('resolution/view/' . $id);
        }

        $resolution = $this->resolutionModel->findById((int) $id);
        if (!$resolution) {
            $this->flash('error', 'Resolution not found.');
            $this->redirect('resolution');
        }

        // Can only refer if status is 'submitted' (or 'under_review' to change committee)
        if (!in_array($resolution['status'], [STATUS_PENDING, STATUS_REVIEWED])) {
            $this->flash('error', 'Only submitted documents can be referred to a committee.');
            $this->redirect('resolution/view/' . $id);
        }

        // Update committee_id and status to 'under_review' (STATUS_REVIEWED)
        $updated = $this->resolutionModel->update((int) $id, [
            'committee_id' => $committeeId,
            'status'       => STATUS_REVIEWED
        ]);

        if ($updated) {
            $db = \Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT name FROM committees WHERE id = :id");
            $stmt->execute([':id' => $committeeId]);
            $comm = $stmt->fetch(PDO::FETCH_ASSOC);
            $commName = $comm['name'] ?? 'Unknown Committee';

            // Log review action
            $stmtLog = $db->prepare(
                "INSERT INTO review_logs (document_type, document_id, action, reason, reviewed_by)
                 VALUES ('resolution', :id, 'referred', :reason, :user)"
            );
            $stmtLog->execute([
                ':id'     => (int) $id,
                ':reason' => 'Referred to ' . $commName,
                ':user'   => $this->userId(),
            ]);

            // Audit log
            $userModel = $this->model('UserModel');
            $userModel->logAudit(
                $this->userId(),
                'REFER_COMMITTEE',
                'resolutions',
                (int) $id,
                ['status' => $resolution['status'], 'committee_id' => $resolution['committee_id']],
                ['status' => STATUS_REVIEWED, 'committee_id' => $committeeId]
            );

            $this->flash('success', 'Resolution has been successfully referred to ' . $commName . '.');
        } else {
            $this->flash('error', 'Failed to refer resolution to committee.');
        }

        $this->redirect('resolution/view/' . $id);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE: FILE UPLOAD
    // ─────────────────────────────────────────────────────────────────────────

    private function handleFileUpload(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'path' => null, 'error' => 'File upload failed.'];
        }
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            return ['success' => false, 'path' => null, 'error' => 'File size must not exceed 10MB.'];
        }

        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES)) {
            return ['success' => false, 'path' => null, 'error' => 'Only PDF and Word documents are allowed.'];
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'RES_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;

        if (!is_dir(UPLOAD_PATH)) { mkdir(UPLOAD_PATH, 0755, true); }

        if (move_uploaded_file($file['tmp_name'], UPLOAD_PATH . $filename)) {
            return ['success' => true, 'path' => $filename, 'error' => null];
        }

        return ['success' => false, 'path' => null, 'error' => 'Failed to save uploaded file.'];
    }
}
