<?php

/**
 * ORLMS - Base Controller Class
 *
 * All application controllers extend this class.
 * Provides helpers for:
 *   - Loading models
 *   - Rendering views (with or without the main layout)
 *   - Redirecting
 *   - Checking authentication and role access
 *   - Returning JSON responses (for AJAX)
 *   - Flash messages (success / error / info)
 */

class Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // MODEL LOADER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Loads and returns an instance of a model class.
     *
     * Usage: $this->model('OrdinanceModel')
     *
     * The model file must be located at:
     *   app/models/OrdinanceModel.php
     *
     * @param  string $model  The model class name (e.g. 'OrdinanceModel')
     * @return object         An instance of the requested model
     */
    protected function model(string $model): object
    {
        $path = APP_ROOT . '/models/' . $model . '.php';

        if (!file_exists($path)) {
            die('ORLMS Error: Model not found — ' . $model . '.php');
        }

        require_once $path;
        return new $model();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VIEW RENDERER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Renders a view file, optionally wrapped inside the main layout.
     *
     * Usage:
     *   $this->view('ordinances/index', ['ordinances' => $data])
     *   $this->view('auth/login', [], false)  ← no layout (for login page)
     *
     * @param  string $view      Path to the view relative to app/views/
     *                           Example: 'ordinances/index'
     * @param  array  $data      Data to pass to the view (extracted as variables)
     * @param  bool   $useLayout Whether to wrap the view in the main layout
     * @return void
     */
    protected function render(string $view, array $data = [], bool $useLayout = true): void
    {
        $viewPath = ROOT . '/src/pages/' . $view . '.php';

        if (!file_exists($viewPath)) {
            die('ORLMS Error: View not found — ' . $view . '.php');
        }

        // Extract data array into individual variables
        // Example: ['title' => 'Dashboard'] becomes $title = 'Dashboard' in the view
        if (!empty($data)) {
            extract($data);
        }

        if ($useLayout) {
            // Capture the view content into a buffer
            ob_start();
            require_once $viewPath;
            $content = ob_get_clean();

            // Pass it to the layout — the layout will echo $content
            require_once ROOT . '/src/pages/layouts/main.php';
        } else {
            // Render the view directly (no layout) — used for login page
            require_once $viewPath;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REDIRECT
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Redirects the browser to a given path within the application.
     *
     * Usage: $this->redirect('ordinances')
     *        $this->redirect('auth/login')
     *
     * @param  string $path  The path relative to APP_ROOT_URL
     * @return void
     */
    protected function redirect(string $path = ''): void
    {
        $url = APP_ROOT_URL . '/' . ltrim($path, '/');
        header('Location: ' . $url);
        exit;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // JSON RESPONSE — for AJAX requests
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Sends a JSON response and terminates the script.
     * Used for AJAX endpoints.
     *
     * Usage:
     *   $this->json(['success' => true, 'message' => 'Saved.'])
     *   $this->json(['success' => false, 'message' => 'Error.'], 400)
     *
     * @param  array $data        The data to encode as JSON
     * @param  int   $statusCode  HTTP status code (default 200)
     * @return void
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FLASH MESSAGES
    // Stored in session, displayed once, then cleared automatically.
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Sets a flash message in the session.
     *
     * Types: 'success' | 'error' | 'warning' | 'info'
     *
     * Usage: $this->flash('success', 'Ordinance saved successfully.')
     *
     * @param  string $type     Message type
     * @param  string $message  The message text
     * @return void
     */
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AUTHENTICATION GUARDS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Checks if a user is currently logged in.
     *
     * @return bool
     */
    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Requires the user to be logged in.
     * Redirects to login page if not authenticated.
     *
     * Call this at the top of any controller method that requires login.
     *
     * @return void
     */
    protected function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            $this->flash('error', 'You must be logged in to access that page.');
            $this->redirect('auth/login');
        }
    }

    /**
     * Requires the user to have a specific role.
     * Redirects to dashboard with an error if the role does not match.
     *
     * Usage: $this->requireRole('super_admin')
     *        $this->requireRole(['super_admin', 'legislative_staff'])
     *
     * @param  string|array $roles  A single role string or an array of allowed roles
     * @return void
     */
    protected function requireRole(string|array $roles): void
    {
        $this->requireLogin();

        $userRole = $_SESSION['user_role'] ?? '';

        if (is_string($roles)) {
            $roles = [$roles];
        }

        if (!in_array($userRole, $roles)) {
            $this->flash('error', 'You do not have permission to access that page.');
            $this->redirect('dashboard');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SESSION HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns the currently logged-in user's ID.
     *
     * @return int|null
     */
    protected function userId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    /**
     * Returns the currently logged-in user's role.
     *
     * @return string|null
     */
    protected function userRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Returns the currently logged-in user's name.
     *
     * @return string|null
     */
    protected function userName(): ?string
    {
        return $_SESSION['user_name'] ?? null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REQUEST HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Checks if the current request is a POST request.
     *
     * @return bool
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Checks if the current request is an AJAX request.
     *
     * @return bool
     */
    protected function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Sanitizes and returns a value from the POST array.
     *
     * @param  string $key      The POST key
     * @param  mixed  $default  Default value if key is not set
     * @return mixed
     */
    protected function post(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    /**
     * Sanitizes and returns a value from the GET array.
     *
     * @param  string $key      The GET key
     * @param  mixed  $default  Default value if key is not set
     * @return mixed
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }
}
