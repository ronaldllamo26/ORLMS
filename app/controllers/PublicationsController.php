<?php

/**
 * ORLMS - Publications Controller
 *
 * Manages the publication of enacted ordinances and resolutions.
 * Publishing requires: official publication reference + plain-language summary.
 * After publishing, document status changes to 'published'.
 *
 * Routes:
 *   GET  /publications                    → index()
 *   GET  /publications/view/{id}          → view($id)
 *   GET  /publications/publish/{type}/{id}→ publish($type, $id)
 *   POST /publications/publish/{type}/{id}→ publish($type, $id)
 */

class PublicationsController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — List all published documents + enacted (pending publication)
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $db = \Database::getInstance()->getConnection();

        // Already published
        $stmt = $db->prepare(
            "SELECT p.*,
                    u.name AS published_by_name,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.ordinance_no
                        WHEN 'resolution' THEN r.resolution_no
                    END AS doc_no,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.title
                        WHEN 'resolution' THEN r.title
                    END AS doc_title,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN au.name
                        WHEN 'resolution' THEN ru.name
                    END AS author_name
             FROM publications p
             LEFT JOIN users u ON p.published_by = u.id
             LEFT JOIN ordinances o ON p.document_type = 'ordinance' AND p.document_id = o.id
             LEFT JOIN users au ON o.author_id = au.id
             LEFT JOIN resolutions r ON p.document_type = 'resolution' AND p.document_id = r.id
             LEFT JOIN users ru ON r.author_id = ru.id
             ORDER BY p.published_at DESC"
        );
        $stmt->execute();
        $published = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enacted but not yet published
        $stmtOrd = $db->prepare(
            "SELECT o.id, o.ordinance_no AS doc_no, o.title, o.subject,
                    o.status, o.date_filed, u.name AS author_name,
                    'ordinance' AS doc_type
             FROM ordinances o
             LEFT JOIN users u ON o.author_id = u.id
             LEFT JOIN publications p ON p.document_type='ordinance' AND p.document_id=o.id
             WHERE o.status = 'enacted' AND p.id IS NULL
             ORDER BY o.updated_at DESC"
        );
        $stmtOrd->execute();

        $stmtRes = $db->prepare(
            "SELECT r.id, r.resolution_no AS doc_no, r.title, r.subject,
                    r.status, r.date_filed, u.name AS author_name,
                    'resolution' AS doc_type
             FROM resolutions r
             LEFT JOIN users u ON r.author_id = u.id
             LEFT JOIN publications p ON p.document_type='resolution' AND p.document_id=r.id
             WHERE r.status = 'enacted' AND p.id IS NULL
             ORDER BY r.updated_at DESC"
        );
        $stmtRes->execute();

        $pendingPublication = array_merge(
            $stmtOrd->fetchAll(PDO::FETCH_ASSOC),
            $stmtRes->fetchAll(PDO::FETCH_ASSOC)
        );

        $this->render('publications/index', [
            'pageTitle'          => 'Publications',
            'published'          => $published,
            'pendingPublication' => $pendingPublication,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VIEW — View a publication record
    // ─────────────────────────────────────────────────────────────────────────

    public function view(string $id): void
    {
        $db   = \Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "SELECT p.*,
                    u.name AS published_by_name,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.ordinance_no
                        WHEN 'resolution' THEN r.resolution_no
                    END AS doc_no,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.title
                        WHEN 'resolution' THEN r.title
                    END AS doc_title,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.content
                        WHEN 'resolution' THEN r.content
                    END AS doc_content,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.subject
                        WHEN 'resolution' THEN r.subject
                    END AS doc_subject,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN o.date_filed
                        WHEN 'resolution' THEN r.date_filed
                    END AS date_filed,
                    CASE p.document_type
                        WHEN 'ordinance'  THEN au.name
                        WHEN 'resolution' THEN ru.name
                    END AS author_name
             FROM publications p
             LEFT JOIN users u ON p.published_by = u.id
             LEFT JOIN ordinances o ON p.document_type='ordinance' AND p.document_id=o.id
             LEFT JOIN users au ON o.author_id = au.id
             LEFT JOIN resolutions r ON p.document_type='resolution' AND p.document_id=r.id
             LEFT JOIN users ru ON r.author_id = ru.id
             WHERE p.id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => (int) $id]);
        $publication = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$publication) {
            $this->flash('error', 'Publication record not found.');
            $this->redirect('publications');
        }

        $this->render('publications/view', [
            'pageTitle'   => 'Publication: ' . ($publication['doc_no'] ?? 'Document'),
            'publication' => $publication,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUBLISH — Publish an enacted document
    // ─────────────────────────────────────────────────────────────────────────

    public function publish(string $type, string $id): void
    {
        $this->requireRole([ROLE_SUPER_ADMIN]);

        if (!in_array($type, ['ordinance', 'resolution'])) {
            $this->flash('error', 'Invalid document type.');
            $this->redirect('publications');
        }

        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->getByIdWithAuthor((int) $id);

        if (!$document || $document['status'] !== STATUS_ENACTED) {
            $this->flash('error', 'Only enacted documents can be published.');
            $this->redirect('publications');
        }

        $errors = [];
        $input  = [];

        if ($this->isPost()) {
            $input = [
                'publication_ref' => trim($this->post('publication_ref', '')),
                'plain_summary'   => trim($this->post('plain_summary', '')),
            ];

            if (empty($input['publication_ref'])) {
                $errors['publication_ref'] = 'Publication reference is required (e.g. Official Gazette Vol. XX No. XX).';
            }
            if (empty($input['plain_summary'])) {
                $errors['plain_summary'] = 'A plain-language summary is required.';
            }

            // Handle file upload (optional)
            $filePath = null;
            if (!empty($_FILES['publication_file']['name'])) {
                $up = $this->handleFileUpload($_FILES['publication_file']);
                if ($up['success']) {
                    $filePath = $up['path'];
                } else {
                    $errors['file'] = $up['error'];
                }
            }

            if (empty($errors)) {
                $db   = \Database::getInstance()->getConnection();
                $stmt = $db->prepare(
                    "INSERT INTO publications
                     (document_type, document_id, publication_ref, plain_summary, file_path, published_by)
                     VALUES (:type, :doc_id, :ref, :summary, :file, :user)"
                );
                $stmt->execute([
                    ':type'    => $type,
                    ':doc_id'  => (int) $id,
                    ':ref'     => $input['publication_ref'],
                    ':summary' => $input['plain_summary'],
                    ':file'    => $filePath,
                    ':user'    => $this->userId(),
                ]);

                // Update document status to published
                $model->updateStatus((int) $id, STATUS_PUBLISHED);

                // Audit log
                $userModel = $this->model('UserModel');
                $userModel->logAudit(
                    $this->userId(), 'PUBLISH', $type . 's', (int) $id,
                    ['status' => STATUS_ENACTED],
                    ['status' => STATUS_PUBLISHED, 'ref' => $input['publication_ref']]
                );

                $noField = $type === 'ordinance' ? 'ordinance_no' : 'resolution_no';
                $docNo   = $document[$noField] ?? '#' . $id;
                $this->flash('success', $docNo . ' has been published successfully.');
                $this->redirect('publications');
            }
        }

        $this->render('publications/publish', [
            'pageTitle' => 'Publish Document',
            'document'  => $document,
            'docType'   => $type,
            'errors'    => $errors,
            'input'     => $input,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE: FILE UPLOAD HANDLER
    // ─────────────────────────────────────────────────────────────────────────

    private function handleFileUpload(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'path' => null, 'error' => 'File upload failed.'];
        }
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            return ['success' => false, 'path' => null, 'error' => 'File must not exceed 10MB.'];
        }

        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES)) {
            return ['success' => false, 'path' => null, 'error' => 'Only PDF and Word documents are allowed.'];
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'PUB_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;

        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], UPLOAD_PATH . $filename)) {
            return ['success' => true, 'path' => $filename, 'error' => null];
        }

        return ['success' => false, 'path' => null, 'error' => 'Failed to save file.'];
    }
}
