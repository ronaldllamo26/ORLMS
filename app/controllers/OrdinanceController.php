<?php

/**
 * ORLMS - Ordinance Controller
 *
 * Handles all CRUD operations for ordinances.
 *
 * Routes:
 *   GET  /ordinance              → index()       — list all
 *   GET  /ordinance/view/{id}    → view($id)     — view single
 *   GET  /ordinance/create       → create()      — show form
 *   POST /ordinance/create       → create()      — save new
 *   GET  /ordinance/edit/{id}    → edit($id)     — show edit form
 *   POST /ordinance/edit/{id}    → edit($id)     — save changes
 *   GET  /ordinance/submit/{id}  → submit($id)   — submit draft for review
 *   GET  /ordinance/delete/{id}  → delete($id)   — delete draft only
 */

class OrdinanceController extends Controller
{
    /** @var OrdinanceModel */
    private OrdinanceModel $ordinanceModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->ordinanceModel = $this->model('OrdinanceModel');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — List all ordinances
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $ordinances = $this->ordinanceModel->getAllWithAuthor();

        $this->render('ordinances/index', [
            'pageTitle'  => 'Ordinances',
            'ordinances' => $ordinances,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VIEW — Single ordinance detail
    // ─────────────────────────────────────────────────────────────────────────

    public function view(string $id): void
    {
        $ordinance = $this->ordinanceModel->getByIdWithAuthor((int) $id);

        if (!$ordinance) {
            $this->flash('error', 'Ordinance not found.');
            $this->redirect('ordinance');
        }

        // Fetch the latest AI validation report for this ordinance
        $aiModel  = $this->model('AiValidationModel');
        $aiReport = $aiModel->getLatestForDocument('ordinance', (int) $id);

        // Fetch review/approval history
        $db   = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "SELECT rl.*, u.name AS reviewer_name
             FROM review_logs rl
             LEFT JOIN users u ON rl.reviewed_by = u.id
             WHERE rl.document_type = 'ordinance' AND rl.document_id = :id
             ORDER BY rl.created_at DESC"
        );
        $stmt->execute([':id' => (int) $id]);
        $reviewHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('ordinances/view', [
            'pageTitle'     => $ordinance['ordinance_no'] ?? 'View Ordinance',
            'ordinance'     => $ordinance,
            'aiReport'      => $aiReport,
            'reviewHistory' => $reviewHistory,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE — Show form (GET) | Save new ordinance (POST)
    // ─────────────────────────────────────────────────────────────────────────

    public function create(): void
    {
        // Only legislative_staff and super_admin can create
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

            // Validate
            if (empty($input['title'])) {
                $errors['title'] = 'Title is required.';
            }
            if (empty($input['subject'])) {
                $errors['subject'] = 'Subject is required.';
            }
            if (empty($input['content'])) {
                $errors['content'] = 'Document content is required.';
            }

            // Handle file upload (optional)
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
                // Generate ordinance number
                $ordinanceNo = $this->ordinanceModel->generateOrdinanceNo();

                $newId = $this->ordinanceModel->insert([
                    'ordinance_no' => $ordinanceNo,
                    'title'        => $input['title'],
                    'subject'      => $input['subject'],
                    'content'      => $input['content'],
                    'author_id'    => $this->userId(),
                    'status'       => STATUS_DRAFT,
                    'file_path'    => $filePath,
                    'date_filed'   => $input['date_filed'],
                ]);

                if ($newId) {
                    // Log the action
                    $userModel = $this->model('UserModel');
                    $userModel->logAudit(
                        $this->userId(),
                        'CREATE',
                        'ordinances',
                        (int) $newId,
                        null,
                        ['ordinance_no' => $ordinanceNo, 'title' => $input['title']]
                    );

                    $this->flash('success', 'Ordinance ' . $ordinanceNo . ' has been created successfully.');
                    $this->redirect('ordinance/view/' . $newId);
                } else {
                    $errors['general'] = 'Failed to save ordinance. Please try again.';
                }
            }
        }

        $this->render('ordinances/create', [
            'pageTitle' => 'New Ordinance',
            'errors'    => $errors,
            'input'     => $input,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EDIT — Show edit form (GET) | Save changes (POST)
    // ─────────────────────────────────────────────────────────────────────────

    public function edit(string $id): void
    {
        $this->requireRole([ROLE_LEGISLATIVE_STAFF, ROLE_SUPER_ADMIN]);

        $ordinance = $this->ordinanceModel->findById((int) $id);

        if (!$ordinance) {
            $this->flash('error', 'Ordinance not found.');
            $this->redirect('ordinance');
        }

        // Only draft ordinances can be edited
        if ($ordinance['status'] !== STATUS_DRAFT) {
            $this->flash('error', 'Only draft ordinances can be edited.');
            $this->redirect('ordinance/view/' . $id);
        }

        $errors = [];
        $input  = $ordinance; // pre-fill with existing data

        if ($this->isPost()) {
            $input = [
                'title'      => $this->post('title', ''),
                'subject'    => $this->post('subject', ''),
                'content'    => $this->post('content', ''),
                'date_filed' => $this->post('date_filed', $ordinance['date_filed']),
            ];

            // Validate
            if (empty($input['title'])) {
                $errors['title'] = 'Title is required.';
            }
            if (empty($input['subject'])) {
                $errors['subject'] = 'Subject is required.';
            }
            if (empty($input['content'])) {
                $errors['content'] = 'Document content is required.';
            }

            // Handle file upload (optional)
            if (!empty($_FILES['document_file']['name'])) {
                $uploadResult = $this->handleFileUpload($_FILES['document_file']);
                if ($uploadResult['success']) {
                    $input['file_path'] = $uploadResult['path'];
                } else {
                    $errors['file'] = $uploadResult['error'];
                }
            }

            if (empty($errors)) {
                $updated = $this->ordinanceModel->update((int) $id, [
                    'title'      => $input['title'],
                    'subject'    => $input['subject'],
                    'content'    => $input['content'],
                    'date_filed' => $input['date_filed'],
                    'file_path'  => $input['file_path'] ?? $ordinance['file_path'],
                ]);

                if ($updated) {
                    $userModel = $this->model('UserModel');
                    $userModel->logAudit(
                        $this->userId(),
                        'UPDATE',
                        'ordinances',
                        (int) $id,
                        ['title' => $ordinance['title']],
                        ['title' => $input['title']]
                    );

                    $this->flash('success', 'Ordinance updated successfully.');
                    $this->redirect('ordinance/view/' . $id);
                } else {
                    $errors['general'] = 'Failed to update ordinance. Please try again.';
                }
            }
        }

        $this->render('ordinances/edit', [
            'pageTitle' => 'Edit Ordinance',
            'ordinance' => $ordinance,
            'errors'    => $errors,
            'input'     => $input,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUBMIT — Submit a draft ordinance for review
    // ─────────────────────────────────────────────────────────────────────────

    public function submit(string $id): void
    {
        $this->requireRole([ROLE_LEGISLATIVE_STAFF, ROLE_SUPER_ADMIN]);

        $ordinance = $this->ordinanceModel->findById((int) $id);

        if (!$ordinance) {
            $this->flash('error', 'Ordinance not found.');
            $this->redirect('ordinance');
        }

        if ($ordinance['status'] !== STATUS_DRAFT) {
            $this->flash('error', 'Only draft ordinances can be submitted for review.');
            $this->redirect('ordinance/view/' . $id);
        }

        $updated = $this->ordinanceModel->updateStatus((int) $id, STATUS_PENDING);

        if ($updated) {
            $userModel = $this->model('UserModel');
            $userModel->logAudit(
                $this->userId(),
                'SUBMIT',
                'ordinances',
                (int) $id,
                ['status' => STATUS_DRAFT],
                ['status' => STATUS_PENDING]
            );

            // Auto-trigger AI Validation if API key is set
            $aiReportMsg = '';
            if (!empty(GROQ_API_KEY) && GROQ_API_KEY !== 'your_groq_api_key_here') {
                $aiModel = $this->model('AiValidationModel');
                $reportId = $aiModel->runValidation('ordinance', (int) $id, $this->userId());
                if ($reportId) {
                    $userModel->logAudit(
                        $this->userId(),
                        'AI_VALIDATE',
                        'ordinances',
                        (int) $id,
                        null,
                        ['report_id' => $reportId, 'triggered_on' => 'submit']
                    );
                    $aiReportMsg = ' AI Validation report has been automatically generated.';
                }
            }

            $this->flash('success', 'Ordinance submitted for review successfully.' . $aiReportMsg);
        } else {
            $this->flash('error', 'Failed to submit ordinance. Please try again.');
        }

        $this->redirect('ordinance/view/' . $id);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE — Delete a draft ordinance (draft only)
    // ─────────────────────────────────────────────────────────────────────────

    public function delete(string $id): void
    {
        $this->requireRole([ROLE_LEGISLATIVE_STAFF, ROLE_SUPER_ADMIN]);

        $ordinance = $this->ordinanceModel->findById((int) $id);

        if (!$ordinance) {
            $this->flash('error', 'Ordinance not found.');
            $this->redirect('ordinance');
        }

        // Only allow deletion of drafts
        if ($ordinance['status'] !== STATUS_DRAFT) {
            $this->flash('error', 'Only draft ordinances may be deleted. Submitted or enacted ordinances cannot be removed.');
            $this->redirect('ordinance/view/' . $id);
        }

        $deleted = $this->ordinanceModel->delete((int) $id);

        if ($deleted) {
            $userModel = $this->model('UserModel');
            $userModel->logAudit(
                $this->userId(),
                'DELETE',
                'ordinances',
                (int) $id,
                ['ordinance_no' => $ordinance['ordinance_no'], 'title' => $ordinance['title']],
                null
            );

            $this->flash('success', 'Draft ordinance deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete ordinance.');
        }

        $this->redirect('ordinance');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE: FILE UPLOAD HANDLER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Handles an uploaded document file (PDF or DOCX).
     *
     * @param  array $file  The $_FILES entry
     * @return array        ['success' => bool, 'path' => string|null, 'error' => string|null]
     */
    private function handleFileUpload(array $file): array
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'path' => null, 'error' => 'File upload failed.'];
        }

        // Validate file size
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            return ['success' => false, 'path' => null, 'error' => 'File size must not exceed 10MB.'];
        }

        // Validate MIME type
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES)) {
            return ['success' => false, 'path' => null, 'error' => 'Only PDF and Word documents (.doc, .docx) are allowed.'];
        }

        // Generate unique filename
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'ORD_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;

        // Ensure upload directory exists
        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0755, true);
        }

        $destination = UPLOAD_PATH . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'path' => $filename, 'error' => null];
        }

        return ['success' => false, 'path' => null, 'error' => 'Failed to save uploaded file.'];
    }
}
