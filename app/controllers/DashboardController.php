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
        $stats = [
            'total_ordinances'      => $ordinanceModel->count(),
            'total_resolutions'     => $resolutionModel->count(),
            'pending_review'        => $ordinanceModel->countWhere('status', 'submitted')
                                     + $resolutionModel->countWhere('status', 'submitted'),
            'enacted'               => $ordinanceModel->countWhere('status', 'enacted')
                                     + $resolutionModel->countWhere('status', 'enacted'),
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
 
        $this->render('dashboard/index', [
            'pageTitle'         => 'Dashboard',
            'stats'             => $stats,
            'recentOrdinances'  => $recentOrdinances,
            'recentResolutions' => $recentResolutions,
            'chartStatusData'   => $chartStatusData,
            'committeeStats'    => $committeeStats,
        ]);
    }
}
