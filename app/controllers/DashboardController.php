<?php

/**
 * ORLMS - Dashboard Controller
 *
 * Handles the main dashboard page shown after login.
 *
 * Routes:
 *   GET /dashboard → index() — main dashboard
 */

class DashboardController extends Controller
{
    public function __construct()
    {
        // Require login for all dashboard actions
        $this->requireLogin();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — Main Dashboard Page
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Shows the main dashboard with document counts and recent activity.
     *
     * URL: /dashboard
     */
    public function index(): void
    {
        // Load needed models
        $ordinanceModel  = $this->model('OrdinanceModel');
        $resolutionModel = $this->model('ResolutionModel');

        // ── Document Counts ───────────────────────────────────────────────────
        $db = \Database::getInstance()->getConnection();

        $enactedOrd = (int) $db->query("SELECT COUNT(*) FROM ordinances WHERE status IN ('enacted','published','approved','signed_lce')")->fetchColumn();
        $enactedRes = (int) $db->query("SELECT COUNT(*) FROM resolutions WHERE status IN ('enacted','published','approved')")->fetchColumn();

        $reviewOrd  = (int) $db->query("SELECT COUNT(*) FROM ordinances WHERE status IN ('submitted','under_review','endorsed')")->fetchColumn();
        $reviewRes  = (int) $db->query("SELECT COUNT(*) FROM resolutions WHERE status IN ('submitted','under_review','endorsed')")->fetchColumn();

        $stats = [
            'total_ordinances'      => $ordinanceModel->count(),
            'total_resolutions'     => $resolutionModel->count(),
            'pending_review'        => $reviewOrd + $reviewRes,
            'enacted'               => $enactedOrd + $enactedRes,
            'draft'                 => $ordinanceModel->countWhere('status', 'draft')
                                     + $resolutionModel->countWhere('status', 'draft'),
            'rejected'              => $ordinanceModel->countWhere('status', 'rejected')
                                     + $resolutionModel->countWhere('status', 'rejected'),
        ];

        // ── Recent Ordinances (latest 5) ──────────────────────────────────────
        $recentOrdinances = $ordinanceModel->getRecent(5);

        // ── Recent Resolutions (latest 5) ─────────────────────────────────────
        $recentResolutions = $resolutionModel->getRecent(5);

        // ── Chart 1: Document Status Distribution ─────────────────────────────
        $statusCountsRaw = $ordinanceModel->query(
            "SELECT status, COUNT(*) as doc_count 
             FROM (
                 SELECT status FROM ordinances
                 UNION ALL
                 SELECT status FROM resolutions
             ) combined
             GROUP BY status"
        );

        $statusCounts = [
            'draft'        => 0,
            'submitted'    => 0,
            'under_review' => 0,
            'endorsed'     => 0,
            'approved'     => 0,
            'enacted'      => 0,
            'published'    => 0,
            'rejected'     => 0,
            'archived'     => 0,
            'implemented'  => 0,
            'amended'      => 0,
        ];
        foreach ($statusCountsRaw as $row) {
            if (array_key_exists($row['status'], $statusCounts)) {
                $statusCounts[$row['status']] = (int)$row['doc_count'];
            }
        }

        $chartStatusData = [
            'Drafts'       => $statusCounts['draft'],
            'In Review'    => $statusCounts['submitted'] + $statusCounts['under_review'] + $statusCounts['endorsed'],
            'Enacted'      => $statusCounts['enacted'] + $statusCounts['approved'] + $statusCounts['amended'],
            'Published'    => $statusCounts['published'] + $statusCounts['implemented'],
            'Rejected'     => $statusCounts['rejected'] + $statusCounts['archived'],
        ];

        // ── Chart 2: Document Distribution by Committee ──────────────────────
        $committeeStats = $ordinanceModel->query(
            "SELECT c.name, 
                    COUNT(o.id) AS ordinance_count, 
                    (SELECT COUNT(r.id) FROM resolutions r WHERE r.committee_id = c.id) AS resolution_count
             FROM committees c
             LEFT JOIN ordinances o ON o.committee_id = c.id
             GROUP BY c.id, c.name
             ORDER BY c.name ASC"
        );
 
        // ── Role-Specific Data Generation ────────────────────────────────────
        $userRole = $_SESSION['user_role'] ?? 'legislative_staff';
        $userId   = $_SESSION['user_id'] ?? 0;
        $roleData = [];

        if ($userRole === 'super_admin') {
            $totalUsers = 0;
            $totalCommittees = 0;
            $recentAuditLogs = [];
            try {
                $totalUsers = (int) $db->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();
                $totalCommittees = (int) $db->query("SELECT COUNT(*) FROM committees WHERE is_active = 1")->fetchColumn();
                $recentAuditLogs = $db->query(
                    "SELECT a.id, a.action, a.table_name, a.created_at, COALESCE(u.name, 'System') as user_name 
                     FROM audit_logs a 
                     LEFT JOIN users u ON a.user_id = u.id 
                     ORDER BY a.created_at DESC LIMIT 5"
                )->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e) {}

            $roleData = [
                'total_users'      => $totalUsers,
                'total_committees' => $totalCommittees,
                'recent_logs'      => $recentAuditLogs,
            ];
        } elseif ($userRole === 'legislative_staff') {
            $draftCount = (int) ($stats['draft'] ?? 0);
            $flaggedAiCount = 0;
            $staffQueue = [];
            try {
                $flaggedAiCount = (int) $db->query(
                    "SELECT COUNT(*) FROM ai_validation_reports WHERE validation_status = 'flagged'"
                )->fetchColumn();
                $staffQueue = $db->query(
                    "SELECT 'ordinance' as type, id, ordinance_no as doc_no, title, status, updated_at 
                     FROM ordinances WHERE status = 'draft' 
                     UNION ALL 
                     SELECT 'resolution' as type, id, resolution_no as doc_no, title, status, updated_at 
                     FROM resolutions WHERE status = 'draft' 
                     ORDER BY updated_at DESC LIMIT 5"
                )->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e) {}

            $roleData = [
                'draft_count'      => $draftCount,
                'flagged_ai_count' => $flaggedAiCount,
                'staff_queue'      => $staffQueue,
            ];
        } elseif ($userRole === 'committee_member') {
            $underReviewCount = 0;
            $readyToEndorseCount = 0;
            $committeeQueue = [];
            try {
                $underReviewCount = (int) $db->query(
                    "SELECT (SELECT COUNT(*) FROM ordinances WHERE status IN ('submitted','under_review')) + 
                            (SELECT COUNT(*) FROM resolutions WHERE status IN ('submitted','under_review'))"
                )->fetchColumn();

                $readyToEndorseCount = (int) $db->query(
                    "SELECT (SELECT COUNT(*) FROM ordinances WHERE status = 'under_review') + 
                            (SELECT COUNT(*) FROM resolutions WHERE status = 'under_review')"
                )->fetchColumn();

                $committeeQueue = $db->query(
                    "SELECT 'ordinance' as type, o.id, o.ordinance_no as doc_no, o.title, o.status, COALESCE(c.name, 'Unassigned') as committee_name, o.updated_at 
                     FROM ordinances o 
                     LEFT JOIN committees c ON o.committee_id = c.id 
                     WHERE o.status IN ('submitted', 'under_review') 
                     UNION ALL 
                     SELECT 'resolution' as type, r.id, r.resolution_no as doc_no, r.title, r.status, COALESCE(c.name, 'Unassigned') as committee_name, r.updated_at 
                     FROM resolutions r 
                     LEFT JOIN committees c ON r.committee_id = c.id 
                     WHERE r.status IN ('submitted', 'under_review') 
                     ORDER BY updated_at DESC LIMIT 5"
                )->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e) {}

            $roleData = [
                'under_review_count'  => $underReviewCount,
                'ready_endorse_count' => $readyToEndorseCount,
                'committee_queue'     => $committeeQueue,
            ];
        } elseif ($userRole === 'sp_member') {
            $endorsedCount = 0;
            $plenaryQueue = [];
            try {
                $endorsedCount = (int) $db->query(
                    "SELECT (SELECT COUNT(*) FROM ordinances WHERE status = 'endorsed') + 
                            (SELECT COUNT(*) FROM resolutions WHERE status = 'endorsed')"
                )->fetchColumn();

                $plenaryQueue = $db->query(
                    "SELECT 'ordinance' as type, o.id, o.ordinance_no as doc_no, o.title, o.status, COALESCE(c.name, 'General Committee') as committee_name, o.updated_at 
                     FROM ordinances o 
                     LEFT JOIN committees c ON o.committee_id = c.id 
                     WHERE o.status = 'endorsed' 
                     UNION ALL 
                     SELECT 'resolution' as type, r.id, r.resolution_no as doc_no, r.title, r.status, COALESCE(c.name, 'General Committee') as committee_name, r.updated_at 
                     FROM resolutions r 
                     LEFT JOIN committees c ON r.committee_id = c.id 
                     WHERE r.status = 'endorsed' 
                     ORDER BY updated_at DESC LIMIT 5"
                )->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e) {}

            $roleData = [
                'endorsed_count' => $endorsedCount,
                'enacted_count'  => $stats['enacted'] ?? 0,
                'plenary_queue'  => $plenaryQueue,
            ];
        }

        $this->render('dashboard/index', [
            'pageTitle'         => 'Dashboard',
            'stats'             => $stats,
            'recentOrdinances'  => $recentOrdinances,
            'recentResolutions' => $recentResolutions,
            'chartStatusData'   => $chartStatusData,
            'committeeStats'    => $committeeStats,
            'userRole'          => $userRole,
            'roleData'          => $roleData,
        ]);
    }
}
