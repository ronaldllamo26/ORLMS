<?php

/**
 * ORLMS - Application Router (App)
 *
 * Reads the URL, determines which controller and method to call,
 * loads the controller file, and calls the method with any URL parameters.
 *
 * URL Structure:
 *   /Capstone/{controller}/{method}/{param1}/{param2}/...
 *
 * Examples:
 *   /Capstone/                         → DashboardController → index()
 *   /Capstone/auth/login               → AuthController → login()
 *   /Capstone/ordinance                → OrdinanceController → index()
 *   /Capstone/ordinance/view/5         → OrdinanceController → view(5)
 *   /Capstone/ai_validation/report/3   → AiValidationController → report(3)
 */

class App
{
    // ─────────────────────────────────────────────────────────────────────────
    // DEFAULTS
    // ─────────────────────────────────────────────────────────────────────────

    /** Default controller when no URL segment is provided */
    protected string $defaultController = 'HomeController';

    /** Default method when no method segment is provided */
    protected string $defaultMethod = 'index';

    // ─────────────────────────────────────────────────────────────────────────
    // CONSTRUCTOR — entry point of the router
    // ─────────────────────────────────────────────────────────────────────────

    public function __construct()
    {
        $urlSegments = $this->parseUrl();
        $this->route($urlSegments);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // URL PARSER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Reads the current REQUEST_URI, strips the base path,
     * and returns a clean array of URL segments.
     *
     * Example:
     *   REQUEST_URI: /Capstone/ordinance/view/5
     *   Returns:     ['ordinance', 'view', '5']
     *
     * @return array
     */
    private function parseUrl(): array
    {
        // Get the full request URI
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove query string (everything after ?)
        $path = parse_url($requestUri, PHP_URL_PATH);

        // Strip the application base path (/Capstone)
        $basePath = rtrim(APP_ROOT_URL, '/');
        if (!empty($basePath) && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        // Remove leading and trailing slashes
        $path = trim($path, '/');

        // Return empty array if no path
        if (empty($path)) {
            return [];
        }

        // Sanitize each segment — only allow alphanumeric, underscores, hyphens
        $segments = explode('/', $path);
        $segments = array_values(array_filter($segments, function ($segment) {
            return !empty($segment) && preg_match('/^[a-zA-Z0-9_\-]+$/', $segment);
        }));

        return $segments;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ROUTER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Determines the controller, method, and parameters from the URL segments,
     * then loads and calls the appropriate controller method.
     *
     * @param  array $url  The parsed URL segments
     * @return void
     */
    private function route(array $url): void
    {
        // ── Step 1: Determine Controller ──────────────────────────────────────

        $controllerName = $this->defaultController;

        if (!empty($url[0])) {
            $controllerName = $this->toControllerName($url[0]);
            array_shift($url);
        }

        // Build the path to the controller file
        $controllerFile = APP_ROOT . '/controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            $this->notFound($controllerName);
            return;
        }

        require_once $controllerFile;

        // Verify the class exists after requiring the file
        if (!class_exists($controllerName)) {
            $this->notFound($controllerName);
            return;
        }

        $controller = new $controllerName();

        // ── Step 2: Determine Method ──────────────────────────────────────────

        $method = $this->defaultMethod;

        if (!empty($url[0])) {
            $requestedMethod = $url[0];

            if (method_exists($controller, $requestedMethod)) {
                $method = $requestedMethod;
                array_shift($url);
            } else {
                // Method not found — show 404
                $this->notFound($controllerName . '::' . $requestedMethod . '()');
                return;
            }
        }

        // ── Step 3: Collect Remaining URL Segments as Parameters ──────────────

        $params = array_values($url);

        // ── Step 4: Call the Controller Method with Parameters ────────────────

        call_user_func_array([$controller, $method], $params);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CONTROLLER NAME CONVERTER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Converts a URL segment to a Controller class name.
     *
     * Rules:
     *  - Underscores separate words: 'ai_validation' → 'AiValidation'
     *  - Hyphens are treated as underscores: 'ai-validation' → 'AiValidation'
     *  - 'Controller' suffix is appended automatically
     *
     * Examples:
     *   'auth'           → 'AuthController'
     *   'ordinance'      → 'OrdinanceController'
     *   'ai_validation'  → 'AiValidationController'
     *
     * @param  string $segment  The URL segment
     * @return string           The controller class name
     */
    private function toControllerName(string $segment): string
    {
        // Replace hyphens with underscores for consistency
        $segment = str_replace('-', '_', $segment);

        // Split by underscore, capitalize each part, rejoin
        $parts = explode('_', $segment);
        $name  = implode('', array_map('ucfirst', $parts));

        return $name . 'Controller';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 404 HANDLER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Displays a 404 Not Found response.
     *
     * @param  string $attempted  What was attempted (for debug info)
     * @return void
     */
    private function notFound(string $attempted = ''): void
    {
        http_response_code(404);

        $notFoundView = ROOT . '/src/pages/errors/404.php';

        if (file_exists($notFoundView)) {
            require_once $notFoundView;
        } else {
            echo '<!DOCTYPE html><html><head>';
            echo '<title>404 - Page Not Found | ORLMS</title>';
            echo '<style>body{font-family:Inter,sans-serif;background:#f4f6f9;display:flex;';
            echo 'align-items:center;justify-content:center;height:100vh;margin:0;}';
            echo '.box{background:#fff;padding:40px 50px;border-top:4px solid #1a3a5c;';
            echo 'border-radius:4px;text-align:center;}h1{color:#1a3a5c;font-size:48px;';
            echo 'margin:0;}h2{color:#6c757d;font-weight:400;}a{color:#c9a84c;}</style>';
            echo '</head><body><div class="box">';
            echo '<h1>404</h1><h2>Page Not Found</h2>';
            echo '<p><a href="' . APP_ROOT_URL . '">Return to Dashboard</a></p>';
            echo '</div></body></html>';
        }
    }
}
