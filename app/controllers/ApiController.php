<?php

/**
 * ORLMS - Integration API Controller
 *
 * Provides RESTful API endpoints for external systems (e.g. LGU mobile apps,
 * Mayor's Office portal, or external Capstone projects) to safely consume
 * published legislative data.
 */

class ApiController extends Controller
{
    /**
     * Set CORS headers to allow cross-origin requests from partner apps.
     */
    private function applyCorsHeaders(): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, X-API-KEY");

        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * GET /api
     * API Root / Documentation & Health Status
     */
    public function index(): void
    {
        $this->applyCorsHeaders();

        $this->json([
            'status'      => 'online',
            'system'      => APP_NAME,
            'short_name'  => APP_SHORT,
            'version'     => APP_VERSION,
            'timestamp'   => date('Y-m-d H:i:s'),
            'endpoints'   => [
                'GET /api/ordinances'  => 'List published ordinances',
                'GET /api/resolutions' => 'List published resolutions',
                'GET /api/search?q='   => 'Search legislative documents by keyword',
                'GET /api/stats'       => 'Public statistics and summary metrics'
            ]
        ]);
    }

    /**
     * GET /api/ordinances
     * Returns list of published ordinances
     */
    public function ordinances(): void
    {
        $this->applyCorsHeaders();

        $ordinanceModel = $this->model('OrdinanceModel');
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 50) : 20;

        $ordinances = $ordinanceModel->query(
            "SELECT o.id, o.ordinance_no, o.title, o.subject, o.status, o.ai_summary, o.date_filed, o.created_at,
                    c.name AS committee_name
             FROM ordinances o
             LEFT JOIN committees c ON o.committee_id = c.id
             WHERE o.status IN ('published', 'enacted', 'approved', 'signed_lce')
             ORDER BY o.created_at DESC
             LIMIT :limit",
            [':limit' => $limit]
        );

        $this->json([
            'status' => 'success',
            'count'  => count($ordinances),
            'data'   => $ordinances
        ]);
    }

    /**
     * GET /api/resolutions
     * Returns list of published resolutions
     */
    public function resolutions(): void
    {
        $this->applyCorsHeaders();

        $resolutionModel = $this->model('ResolutionModel');
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 50) : 20;

        $resolutions = $resolutionModel->query(
            "SELECT r.id, r.resolution_no, r.title, r.subject, r.status, r.date_filed, r.created_at,
                    c.name AS committee_name
             FROM resolutions r
             LEFT JOIN committees c ON r.committee_id = c.id
             WHERE r.status IN ('published', 'enacted', 'approved', 'signed_lce')
             ORDER BY r.created_at DESC
             LIMIT :limit",
            [':limit' => $limit]
        );

        $this->json([
            'status' => 'success',
            'count'  => count($resolutions),
            'data'   => $resolutions
        ]);
    }

    /**
     * GET /api/search?q=keyword
     * Searches published ordinances and resolutions
     */
    public function search(): void
    {
        $this->applyCorsHeaders();

        $query = trim($_GET['q'] ?? $_GET['keyword'] ?? '');

        if (empty($query)) {
            $this->json([
                'status'  => 'error',
                'message' => 'Query parameter "q" or "keyword" is required.'
            ], 400);
        }

        $db = Database::getInstance()->getConnection();
        $searchTerm = '%' . $query . '%';

        // Search ordinances
        $stmtOrd = $db->prepare(
            "SELECT 'ordinance' AS type, id, ordinance_no AS doc_no, title, subject, status, date_filed, created_at
             FROM ordinances
             WHERE (title LIKE :q OR subject LIKE :q OR content LIKE :q)
               AND status IN ('published', 'enacted', 'approved', 'signed_lce')
             ORDER BY created_at DESC LIMIT 20"
        );
        $stmtOrd->execute([':q' => $searchTerm]);
        $ordinances = $stmtOrd->fetchAll(PDO::FETCH_ASSOC);

        // Search resolutions
        $stmtRes = $db->prepare(
            "SELECT 'resolution' AS type, id, resolution_no AS doc_no, title, subject, status, date_filed, created_at
             FROM resolutions
             WHERE (title LIKE :q OR subject LIKE :q OR content LIKE :q)
               AND status IN ('published', 'enacted', 'approved', 'signed_lce')
             ORDER BY created_at DESC LIMIT 20"
        );
        $stmtRes->execute([':q' => $searchTerm]);
        $resolutions = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

        $results = array_merge($ordinances, $resolutions);

        $this->json([
            'status' => 'success',
            'query'  => $query,
            'count'  => count($results),
            'data'   => $results
        ]);
    }

    /**
     * GET /api/stats
     * Returns system statistics overview
     */
    public function stats(): void
    {
        $this->applyCorsHeaders();

        $db = Database::getInstance()->getConnection();

        $totalOrdinances = (int)$db->query("SELECT COUNT(*) FROM ordinances WHERE status IN ('published', 'enacted', 'approved', 'signed_lce')")->fetchColumn();
        $totalResolutions = (int)$db->query("SELECT COUNT(*) FROM resolutions WHERE status IN ('published', 'enacted', 'approved', 'signed_lce')")->fetchColumn();
        $totalCommittees = (int)$db->query("SELECT COUNT(*) FROM committees WHERE is_active = 1")->fetchColumn();

        $this->json([
            'status' => 'success',
            'stats'  => [
                'published_ordinances'  => $totalOrdinances,
                'published_resolutions' => $totalResolutions,
                'active_committees'     => $totalCommittees,
                'last_updated'          => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
