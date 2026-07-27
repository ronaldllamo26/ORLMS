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

        $this->render('dashboard/index', [
            'pageTitle'         => 'Dashboard',
            'stats'             => $stats,
            'recentOrdinances'  => $recentOrdinances,
            'recentResolutions' => $recentResolutions,
        ]);
    }
}
