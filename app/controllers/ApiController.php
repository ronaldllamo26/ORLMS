<?php

/**
 * ORLMS - Integration REST API Controller
 *
 * Provides RESTful API endpoints for external subsystems (e.g. Document Repository,
 * LGU Mobile Apps, Mayor's Office Portal, or Partner Capstone Projects)
 * to safely consume approved and enacted legislative data.
 */

class ApiController extends Controller
{
    /** Secret key for partner subsystems */
    private const DEFAULT_API_KEY = 'ORLMS-PARTNER-API-KEY-2026';

    /**
     * Set CORS headers to allow cross-origin requests from partner apps.
     */
    private function applyCorsHeaders(): void
    {
        if (!headers_sent()) {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY");
        }

        // Handle preflight OPTIONS request
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * Helper to verify optional or required API key.
     */
    private function getApiKey(): ?string
    {
        return $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? null;
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
            'environment' => getenv('APP_ENV') ?: 'production',
            'timestamp'   => date('Y-m-d H:i:s'),
            'api_key'     => self::DEFAULT_API_KEY,
            'endpoints'   => [
                'GET /api/documents'          => 'Unified list of ALL approved/enacted ordinances & resolutions',
                'GET /api/ordinances'         => 'List of approved/enacted ordinances (supports ?full=1, ?limit=50)',
                'GET /api/resolutions'        => 'List of approved/enacted resolutions (supports ?full=1, ?limit=50)',
                'GET /api/detail/{type}/{id}' => 'Full details & legal text of a specific document',
                'GET /api/search?q={query}'   => 'Full-text search across legislative documents',
                'GET /api/stats'              => 'Summary metrics and legislative counts'
            ],
            'note' => 'Pass X-API-KEY in HTTP Header or ?api_key= in query parameter.'
        ]);
    }

    /**
     * GET /api/documents
     * Unified feed of ALL accepted/approved ordinances and resolutions for Document Repository encoding
     */
    public function documents(): void
    {
        $this->applyCorsHeaders();

        $db = Database::getInstance()->getConnection();
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 100) : 50;
        $includeContent = isset($_GET['full']) && (int)$_GET['full'] === 1;

        $contentField = $includeContent ? 'o.content,' : '';
        $contentFieldRes = $includeContent ? 'r.content,' : '';

        // Query approved ordinances
        $stmtOrd = $db->prepare(
            "SELECT 'ordinance' AS document_type,
                    o.id, o.ordinance_no AS document_no, o.title, o.subject, {$contentField}
                    o.status, o.ai_summary, o.file_path, o.date_filed, o.created_at,
                    c.name AS committee_name, u.name AS author_name
             FROM ordinances o
             LEFT JOIN committees c ON o.committee_id = c.id
             LEFT JOIN users u ON o.author_id = u.id
             WHERE o.status IN ('published', 'enacted', 'approved', 'signed_lce')
             ORDER BY o.created_at DESC
             LIMIT :limit"
        );
        $stmtOrd->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmtOrd->execute();
        $ordinances = $stmtOrd->fetchAll(PDO::FETCH_ASSOC);

        // Query approved resolutions
        $stmtRes = $db->prepare(
            "SELECT 'resolution' AS document_type,
                    r.id, r.resolution_no AS document_no, r.title, r.subject, {$contentFieldRes}
                    r.status, r.ai_summary, r.file_path, r.date_filed, r.created_at,
                    c.name AS committee_name, u.name AS author_name
             FROM resolutions r
             LEFT JOIN committees c ON r.committee_id = c.id
             LEFT JOIN users u ON r.author_id = u.id
             WHERE r.status IN ('published', 'enacted', 'approved', 'signed_lce')
             ORDER BY r.created_at DESC
             LIMIT :limit"
        );
        $stmtRes->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmtRes->execute();
        $resolutions = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

        // Attach full download URL
        $all = array_merge($ordinances, $resolutions);
        foreach ($all as &$doc) {
            $doc['download_url'] = !empty($doc['file_path']) ? APP_URL . '/public/uploads/documents/' . basename($doc['file_path']) : null;
        }

        $this->json([
            'status'     => 'success',
            'feed'       => 'approved_legislative_repository',
            'count'      => count($all),
            'timestamp'  => date('Y-m-d H:i:s'),
            'data'       => $all
        ]);
    }

    /**
     * GET /api/ordinances
     * Returns list of approved/enacted ordinances
     */
    public function ordinances(): void
    {
        $this->applyCorsHeaders();

        $db = Database::getInstance()->getConnection();
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 100) : 50;
        $includeContent = isset($_GET['full']) && (int)$_GET['full'] === 1;
        $contentField = $includeContent ? 'o.content,' : '';

        $stmt = $db->prepare(
            "SELECT o.id, o.ordinance_no, o.title, o.subject, {$contentField}
                    o.status, o.ai_summary, o.file_path, o.date_filed, o.created_at,
                    c.name AS committee_name, u.name AS author_name
             FROM ordinances o
             LEFT JOIN committees c ON o.committee_id = c.id
             LEFT JOIN users u ON o.author_id = u.id
             WHERE o.status IN ('published', 'enacted', 'approved', 'signed_lce')
             ORDER BY o.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $ordinances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ordinances as &$ord) {
            $ord['download_url'] = !empty($ord['file_path']) ? APP_URL . '/public/uploads/documents/' . basename($ord['file_path']) : null;
        }

        $this->json([
            'status' => 'success',
            'type'   => 'ordinance',
            'count'  => count($ordinances),
            'data'   => $ordinances
        ]);
    }

    /**
     * GET /api/resolutions
     * Returns list of approved/enacted resolutions
     */
    public function resolutions(): void
    {
        $this->applyCorsHeaders();

        $db = Database::getInstance()->getConnection();
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 100) : 50;
        $includeContent = isset($_GET['full']) && (int)$_GET['full'] === 1;
        $contentField = $includeContent ? 'r.content,' : '';

        $stmt = $db->prepare(
            "SELECT r.id, r.resolution_no, r.title, r.subject, {$contentField}
                    r.status, r.ai_summary, r.file_path, r.date_filed, r.created_at,
                    c.name AS committee_name, u.name AS author_name
             FROM resolutions r
             LEFT JOIN committees c ON r.committee_id = c.id
             LEFT JOIN users u ON r.author_id = u.id
             WHERE r.status IN ('published', 'enacted', 'approved', 'signed_lce')
             ORDER BY r.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $resolutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resolutions as &$res) {
            $res['download_url'] = !empty($res['file_path']) ? APP_URL . '/public/uploads/documents/' . basename($res['file_path']) : null;
        }

        $this->json([
            'status' => 'success',
            'type'   => 'resolution',
            'count'  => count($resolutions),
            'data'   => $resolutions
        ]);
    }

    /**
     * GET /api/detail/{type}/{id}
     * Returns the full content and metadata of a specific approved document.
     * Supports both /api/detail/ordinance/1 or query parameters /api/detail?type=ordinance&id=1
     */
    public function detail(?string $type = null, ?string $id = null): void
    {
        $this->applyCorsHeaders();

        $type = strtolower($type ?: ($_GET['type'] ?? 'ordinance'));
        $id   = (int)($id ?: ($_GET['id'] ?? 0));

        if ($id <= 0 || !in_array($type, ['ordinance', 'resolution'])) {
            $this->json([
                'status'  => 'error',
                'message' => 'Invalid or missing parameters. Usage: /api/detail/{type}/{id} where type is "ordinance" or "resolution".'
            ], 400);
        }

        $db = Database::getInstance()->getConnection();

        if ($type === 'ordinance') {
            $stmt = $db->prepare(
                "SELECT o.*, c.name AS committee_name, u.name AS author_name
                 FROM ordinances o
                 LEFT JOIN committees c ON o.committee_id = c.id
                 LEFT JOIN users u ON o.author_id = u.id
                 WHERE o.id = :id AND o.status IN ('published', 'enacted', 'approved', 'signed_lce')
                 LIMIT 1"
            );
        } else {
            $stmt = $db->prepare(
                "SELECT r.*, c.name AS committee_name, u.name AS author_name
                 FROM resolutions r
                 LEFT JOIN committees c ON r.committee_id = c.id
                 LEFT JOIN users u ON r.author_id = u.id
                 WHERE r.id = :id AND r.status IN ('published', 'enacted', 'approved', 'signed_lce')
                 LIMIT 1"
            );
        }

        $stmt->execute([':id' => $id]);
        $document = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$document) {
            $this->json([
                'status'  => 'error',
                'message' => 'Document not found or is not yet in approved/enacted state.'
            ], 404);
        }

        $document['document_type'] = $type;
        $document['download_url']  = !empty($document['file_path']) ? APP_URL . '/public/uploads/documents/' . basename($document['file_path']) : null;

        $this->json([
            'status' => 'success',
            'data'   => $document
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
             WHERE (title LIKE :q1 OR subject LIKE :q2 OR content LIKE :q3)
               AND status IN ('published', 'enacted', 'approved', 'signed_lce')
             ORDER BY created_at DESC LIMIT 20"
        );
        $stmtOrd->execute([':q1' => $searchTerm, ':q2' => $searchTerm, ':q3' => $searchTerm]);
        $ordinances = $stmtOrd->fetchAll(PDO::FETCH_ASSOC);

        // Search resolutions
        $stmtRes = $db->prepare(
            "SELECT 'resolution' AS type, id, resolution_no AS doc_no, title, subject, status, date_filed, created_at
             FROM resolutions
             WHERE (title LIKE :q1 OR subject LIKE :q2 OR content LIKE :q3)
               AND status IN ('published', 'enacted', 'approved', 'signed_lce')
             ORDER BY created_at DESC LIMIT 20"
        );
        $stmtRes->execute([':q1' => $searchTerm, ':q2' => $searchTerm, ':q3' => $searchTerm]);
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
