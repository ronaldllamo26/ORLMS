<?php

/**
 * ORLMS - PHP Built-in Server Router
 *
 * This file replaces Apache's .htaccess mod_rewrite rules
 * when using PHP's built-in development server.
 *
 * Usage:
 *   php -S localhost:8000 router.php
 *
 * It serves static files (CSS, JS, images) directly,
 * and routes everything else through index.php.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Strip the /Capstone prefix if present (for compatibility)
$uri = preg_replace('#^/Capstone#', '', $uri);

// If the requested file exists on disk, serve it directly
// (CSS, JS, images, fonts, uploaded files, etc.)
$filePath = __DIR__ . $uri;
if ($uri !== '/' && file_exists($filePath) && is_file($filePath)) {
    return false; // Let PHP's built-in server handle the file
}

// Otherwise, route through index.php (the front controller)
require_once __DIR__ . '/index.php';
