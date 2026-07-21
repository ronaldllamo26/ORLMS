<?php

/**
 * ORLMS - AI Validation Controller
 *
 * Routes:
 *   GET /ai_validation                          → index()
 *   GET /ai_validation/run/{type}/{id}          → run($type, $id)
 *   GET /ai_validation/report/{id}              → report($id)
 */

class AiValidationController extends Controller
{
    /** @var AiValidationModel */
    private AiValidationModel $aiModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->aiModel = $this->model('AiValidationModel');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — List all AI validation reports
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $reports = $this->aiModel->getAllReports();
        $counts  = $this->aiModel->getStatusCounts();

        $this->render('ai_validation/index', [
            'pageTitle' => 'AI Validation Reports',
            'reports'   => $reports,
            'counts'    => $counts,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RUN — Trigger AI validation for a document
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Runs the AI validation for a given document.
     *
     * URL: /ai_validation/run/{type}/{id}
     *      e.g. /ai_validation/run/ordinance/5
     *
     * @param  string $type  'ordinance' or 'resolution'
     * @param  string $id    Document ID
     */
    public function run(string $type, string $id): void
    {
        // Only super_admin and legislative_staff can trigger validation
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_LEGISLATIVE_STAFF]);

        // Validate type
        if (!in_array($type, ['ordinance', 'resolution'])) {
            $this->flash('error', 'Invalid document type specified.');
            $this->redirect('ai_validation');
        }

        $documentId = (int) $id;

        if ($documentId <= 0) {
            $this->flash('error', 'Invalid document ID.');
            $this->redirect('ai_validation');
        }

        // Verify document exists
        $model    = $this->model($type === 'ordinance' ? 'OrdinanceModel' : 'ResolutionModel');
        $document = $model->findById($documentId);

        if (!$document) {
            $this->flash('error', ucfirst($type) . ' not found.');
            $this->redirect('ai_validation');
        }

        // Check API key is configured
        if (empty(GROQ_API_KEY) || GROQ_API_KEY === 'your_groq_api_key_here') {
            $this->flash('error', 'Groq API key is not configured. Please update config/config.php.');
            $this->redirect($type . '/view/' . $documentId);
        }

        // Run the validation
        $reportId = $this->aiModel->runValidation($type, $documentId, $this->userId());

        if ($reportId) {
            // Log the validation
            $userModel = $this->model('UserModel');
            $userModel->logAudit(
                $this->userId(),
                'AI_VALIDATE',
                $type . 's',
                $documentId,
                null,
                ['report_id' => $reportId]
            );

            $this->flash('success', 'AI Validation completed successfully.');
            $this->redirect('ai_validation/report/' . $reportId);
        } else {
            $this->flash('error', 'AI Validation failed. Please check the API key and try again.');
            $this->redirect($type . '/view/' . $documentId);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REPORT — View a single validation report
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Shows a full AI validation report.
     *
     * URL: /ai_validation/report/{id}
     *
     * @param  string $id  Report ID
     */
    public function report(string $id): void
    {
        $report = $this->aiModel->getReportById((int) $id);

        if (!$report) {
            $this->flash('error', 'Validation report not found.');
            $this->redirect('ai_validation');
        }

        // Decode JSON detail fields
        $completenessDetails = json_decode($report['completeness_details'] ?? '{}', true) ?? [];
        $similarityDetails   = json_decode($report['similarity_details'] ?? '{}', true) ?? [];

        $this->render('ai_validation/report', [
            'pageTitle'           => 'AI Validation Report',
            'report'              => $report,
            'completenessDetails' => $completenessDetails,
            'similarityDetails'   => $similarityDetails,
        ]);
    }
}
