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
        $this->render('home/index', [
            'pageTitle' => 'Home - Legislative Portal'
        ], false); // Custom full-width layout
    }
}
