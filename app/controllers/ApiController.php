<?php

/**
 * ORLMS - REST API Controller
 *
 * Provides RESTful API endpoints for integration with other subsystems:
 *   1. POST /api/receive             → Submits a new document from another subsystem.
 *   2. GET  /api/documents           → Lists ordinances/resolutions (can filter by ?status=).
 *   3. GET  /api/documents/{type}    → Lists only ordinances or resolutions.
 *   4. GET  /api/detail/{type}/{id}  → Gets full details of a specific document + AI report.
 */

class ApiController extends Controller
{
    /** @var OrdinanceModel */
    private OrdinanceModel $ordinanceModel;

    /** @var ResolutionModel */
    private ResolutionModel $resolutionModel;

    /** @var UserModel */
    private UserModel $userModel;

    /** @var ConsultationModel */
    private ConsultationModel $consultationModel;

    public function __construct()
    {
        // No session check ($this->requireLogin()) so external subsystems can connect.
        $this->ordinanceModel = $this->model('OrdinanceModel');
        $this->resolutionModel = $this->model('ResolutionModel');
        $this->userModel = $this->model('UserModel');
        $this->consultationModel = $this->model('ConsultationModel');
    }

    /**
     * GET /api
     * API Root Endpoint - Returns system status and available API endpoints.
     */
    public function index(): void
    {
        $this->json([
            'status'      => 'online',
            'system'      => APP_NAME,
            'version'     => APP_VERSION,
            'timestamp'   => date('c'),
            'endpoints'   => [
                'POST /api/receive'              => 'Submit drafted ordinance or resolution for processing',
                'GET  /api/documents'            => 'Fetch list of all ordinances and resolutions',
                'GET  /api/documents/{type}'     => 'Fetch documents filtered by type (ordinance or resolution)',
                'GET  /api/detail/{type}/{id}'   => 'Fetch full details and AI validation report of a document',
                'POST /api/consultations'        => 'Submit public consultation and hearing results'
            ]
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. POST /api/receive — Receive document from another subsystem
    // ─────────────────────────────────────────────────────────────────────────
    public function receive(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Only POST requests are allowed.'], 405);
        }

        // Get JSON payload or standard POST variables
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (empty($data)) {
            $data = $_POST;
        }

        // Validate required fields
        $requiredFields = ['title', 'subject', 'content', 'document_type'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->json(['success' => false, 'message' => "Field '{$field}' is required."], 400);
            }
        }

        $docType = strtolower(trim($data['document_type']));
        if ($docType !== 'ordinance' && $docType !== 'resolution') {
            $this->json(['success' => false, 'message' => "Field 'document_type' must be 'ordinance' or 'resolution'."], 400);
        }

        // Determine status (defaults to 'submitted')
        $status = trim($data['status'] ?? STATUS_PENDING);
        $allowedStatuses = [STATUS_DRAFT, STATUS_PENDING];
        if (!in_array($status, $allowedStatuses)) {
            $status = STATUS_PENDING;
        }

        // Determine author (defaults to Super Admin / staff ID 1)
        $authorId = isset($data['author_id']) ? (int)$data['author_id'] : null;
        if (!$authorId) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT id FROM users WHERE is_active = TRUE AND role IN ('legislative_staff', 'super_admin') ORDER BY role DESC LIMIT 1");
            $defaultUser = $stmt->fetch(PDO::FETCH_ASSOC);
            $authorId = $defaultUser ? (int)$defaultUser['id'] : 1;
        }

        $dateFiled = !empty($data['date_filed']) ? trim($data['date_filed']) : date('Y-m-d');

        if ($docType === 'ordinance') {
            $trackingNo = $this->ordinanceModel->generateOrdinanceNo();
            $newId = $this->ordinanceModel->insert([
                'ordinance_no' => $trackingNo,
                'title'        => trim($data['title']),
                'subject'      => trim($data['subject']),
                'content'      => trim($data['content']),
                'author_id'    => $authorId,
                'status'       => $status,
                'date_filed'   => $dateFiled,
            ]);
        } else {
            $trackingNo = $this->resolutionModel->generateResolutionNo();
            $newId = $this->resolutionModel->insert([
                'resolution_no' => $trackingNo,
                'title'         => trim($data['title']),
                'subject'       => trim($data['subject']),
                'content'       => trim($data['content']),
                'author_id'     => $authorId,
                'status'        => $status,
                'date_filed'    => $dateFiled,
            ]);
        }

        if ($newId) {
            $this->userModel->logAudit(
                $authorId,
                'API_RECEIVE',
                $docType === 'ordinance' ? 'ordinances' : 'resolutions',
                (int) $newId,
                null,
                ['tracking_no' => $trackingNo, 'title' => trim($data['title']), 'source' => 'external_api']
            );

            // Trigger AI validation if status is submitted
            $aiTriggered = false;
            if ($status === STATUS_PENDING && !empty(GROQ_API_KEY) && GROQ_API_KEY !== 'your_groq_api_key_here') {
                $aiModel = $this->model('AiValidationModel');
                $reportId = $aiModel->runValidation($docType, (int) $newId, $authorId);
                if ($reportId) {
                    $this->userModel->logAudit(
                        $authorId,
                        'AI_VALIDATE',
                        $docType === 'ordinance' ? 'ordinances' : 'resolutions',
                        (int) $newId,
                        null,
                        ['report_id' => $reportId, 'triggered_on' => 'api_submit']
                    );
                    $aiTriggered = true;
                }
            }

            $this->json([
                'success'      => true,
                'message'      => ucfirst($docType) . ' received and saved successfully.',
                'id'           => (int)$newId,
                'tracking_no'  => $trackingNo,
                'status'       => $status,
                'ai_validated' => $aiTriggered
            ], 201);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to save document.'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. GET /api/documents — List all ordinances and resolutions
    // ─────────────────────────────────────────────────────────────────────────
    public function documents(string $type = ''): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->json(['success' => false, 'message' => 'Only GET requests are allowed.'], 405);
        }

        $type = strtolower(trim($type));
        $status = $this->get('status', ''); // e.g. ?status=submitted

        $ordinances = [];
        $resolutions = [];

        if (empty($type) || $type === 'ordinance') {
            if (!empty($status)) {
                $ordinances = $this->ordinanceModel->getByStatus($status);
            } else {
                $ordinances = $this->ordinanceModel->getAllWithAuthor();
            }
        }

        if (empty($type) || $type === 'resolution') {
            if (!empty($status)) {
                $resolutions = $this->resolutionModel->getByStatus($status);
            } else {
                $resolutions = $this->resolutionModel->getAllWithAuthor();
            }
        }

        $this->json([
            'success' => true,
            'count'   => count($ordinances) + count($resolutions),
            'data'    => [
                'ordinances'  => $ordinances,
                'resolutions' => $resolutions
            ]
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. GET /api/detail/{type}/{id} — View single document details + AI validation report
    // ─────────────────────────────────────────────────────────────────────────
    public function detail(string $type = '', string $id = ''): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->json(['success' => false, 'message' => 'Only GET requests are allowed.'], 405);
        }

        $type = strtolower(trim($type));
        $id = (int)$id;

        if ($type !== 'ordinance' && $type !== 'resolution') {
            $this->json(['success' => false, 'message' => "Type must be 'ordinance' or 'resolution'."], 400);
        }

        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'Valid ID is required.'], 400);
        }

        $document = ($type === 'ordinance') 
            ? $this->ordinanceModel->getByIdWithAuthor($id) 
            : $this->resolutionModel->getByIdWithAuthor($id);

        if (!$document) {
            $this->json(['success' => false, 'message' => 'Document not found.'], 404);
        }

        // Get latest AI validation report
        $aiModel = $this->model('AiValidationModel');
        $aiReport = $aiModel->getLatestForDocument($type, $id);

        $this->json([
            'success' => true,
            'data'    => [
                'document'  => $document,
                'ai_report' => $aiReport ?: null
            ]
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. POST /api/consultations — Receive public hearing consultation data
    // ─────────────────────────────────────────────────────────────────────────
    public function consultations(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Only POST requests are allowed.'], 405);
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (empty($data)) {
            $data = $_POST;
        }

        // Validate required fields
        $requiredFields = ['document_id', 'document_type', 'hearing_date', 'venue'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->json(['success' => false, 'message' => "Field '{$field}' is required."], 400);
            }
        }

        $docType = strtolower(trim($data['document_type']));
        if ($docType !== 'ordinance' && $docType !== 'resolution') {
            $this->json(['success' => false, 'message' => "Field 'document_type' must be 'ordinance' or 'resolution'."], 400);
        }

        $docId = (int)$data['document_id'];
        if ($docId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid document ID.'], 400);
        }

        // Verify the document exists in our database
        $document = ($docType === 'ordinance') 
            ? $this->ordinanceModel->findById($docId) 
            : $this->resolutionModel->findById($docId);

        if (!$document) {
            $this->json(['success' => false, 'message' => "Document not found in ORLMS database."], 404);
        }

        // Save consultation
        $newId = $this->consultationModel->insert([
            'document_id'        => $docId,
            'document_type'      => $docType,
            'hearing_date'       => trim($data['hearing_date']),
            'venue'              => trim($data['venue']),
            'total_participants' => isset($data['total_participants']) ? (int)$data['total_participants'] : 0,
            'total_opinions'     => isset($data['total_opinions']) ? (int)$data['total_opinions'] : 0,
            'sentiment_summary'  => isset($data['sentiment_summary']) ? trim($data['sentiment_summary']) : null,
            'summary_report'     => isset($data['summary_report']) ? trim($data['summary_report']) : null,
            'report_file_url'    => isset($data['report_file_url']) ? trim($data['report_file_url']) : null,
        ]);

        if ($newId) {
            // Log audit using system default user
            $this->userModel->logAudit(
                1,
                'API_RECEIVE_CONSULTATION',
                $docType === 'ordinance' ? 'ordinances' : 'resolutions',
                $docId,
                null,
                ['consultation_id' => (int)$newId, 'source' => 'external_api']
            );

            $this->json([
                'success' => true,
                'message' => 'Public consultation data received and linked successfully.',
                'id'      => (int)$newId
            ], 201);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to save consultation data.'], 500);
        }
    }
}
