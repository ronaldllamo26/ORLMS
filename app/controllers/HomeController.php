<?php

/**
 * ORLMS - Home Controller (Landing Page)
 *
 * Handles the public landing page of the system.
 */
class HomeController extends Controller
{
    /**
     * Renders the public landing page.
     *
     * GET /
     */
    public function index(): void
    {
        $stats = [
            'ordinances' => 0,
            'resolutions' => 0,
            'committees'  => 0,
        ];

        try {
            $db = \Database::getInstance()->getConnection();
            
            // Count published ordinances
            $stmt = $db->query("SELECT COUNT(*) FROM ordinances WHERE status IN ('enacted','published','approved')");
            $stats['ordinances'] = (int) $stmt->fetchColumn();

            // Count published resolutions
            $stmt = $db->query("SELECT COUNT(*) FROM resolutions WHERE status IN ('enacted','published','approved','endorsed')");
            $stats['resolutions'] = (int) $stmt->fetchColumn();

            // Count committees
            $stmt = $db->query("SELECT COUNT(*) FROM committees");
            $stats['committees'] = (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            // Fallback defaults if database check has any issue
            error_log('HomeController Stats Error: ' . $e->getMessage());
        }

        $this->render('home/index', [
            'pageTitle' => 'Home - Legislative Portal',
            'stats'     => $stats,
        ], false); // Custom full-width layout
    }
}
