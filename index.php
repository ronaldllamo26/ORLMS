<?php

/**
 * ORLMS - Ordinance and Resolution Lifecycle Management System
 * Entry Point / Front Controller
 *
 * All HTTP requests pass through this file.
 * It loads the configuration, core classes, and starts the router.
 */

// ─────────────────────────────────────────────────────────────────────────────
// 1. DEFINE ROOT PATH
//    Used throughout the app to build absolute file paths.
// ─────────────────────────────────────────────────────────────────────────────
define('ROOT', dirname(__FILE__));
define('APP_ROOT', ROOT . '/app');
define('CORE_ROOT', ROOT . '/core');
define('CONFIG_ROOT', ROOT . '/config');
define('PUBLIC_ROOT', ROOT . '/public');

// ─────────────────────────────────────────────────────────────────────────────
// 2. START SESSION
//    Must be called before any output. Used for auth, flash messages, etc.
// ─────────────────────────────────────────────────────────────────────────────
session_start();

if (!function_exists('is_internal_portal')) {
    /**
     * Check if current request is accessing through the Internal / Staff Portal domain
     * (e.g. portal.orlms.sjdm-legislative.com, staff.*, admin.*, portal-*)
     */
    function is_internal_portal(): bool {
        $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
        if (str_contains($host, 'portal') || 
            str_contains($host, 'staff') || 
            str_contains($host, 'admin')) {
            return true;
        }
        // Also allow preview via query parameter or if staff is already logged in
        if (isset($_GET['staff']) || isset($_GET['portal']) || !empty($_SESSION['user_id'])) {
            return true;
        }
        return false;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// 3. SET ERROR REPORTING (Environment Aware)
// ─────────────────────────────────────────────────────────────────────────────
$appEnv = getenv('APP_ENV') ?: 'development';
if ($appEnv === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}


// ─────────────────────────────────────────────────────────────────────────────
// 4. LOAD COMPOSER AUTOLOADER
// ─────────────────────────────────────────────────────────────────────────────
if (file_exists(ROOT . '/vendor/autoload.php')) {
    require_once ROOT . '/vendor/autoload.php';
}

// ─────────────────────────────────────────────────────────────────────────────
// 5. LOAD CONFIGURATION
//    Contains DB credentials, app settings, and Groq API key.
// ─────────────────────────────────────────────────────────────────────────────
require_once CONFIG_ROOT . '/config.php';
require_once CONFIG_ROOT . '/database.php';

// ─────────────────────────────────────────────────────────────────────────────
// 5. LOAD CORE CLASSES
//    Base classes that all controllers and models inherit from.
// ─────────────────────────────────────────────────────────────────────────────
require_once CORE_ROOT . '/Database.php';
require_once CORE_ROOT . '/Model.php';
require_once CORE_ROOT . '/Controller.php';
require_once CORE_ROOT . '/App.php';
require_once CORE_ROOT . '/RateLimiter.php';
if (file_exists(APP_ROOT . '/helpers/GeoIPHelper.php')) {
    require_once APP_ROOT . '/helpers/GeoIPHelper.php';
}

// ─────────────────────────────────────────────────────────────────────────────
// 6. ENFORCE RATE LIMITING & DOS PROTECTION
// ─────────────────────────────────────────────────────────────────────────────
RateLimiter::check(100, 20, 60);

// ─────────────────────────────────────────────────────────────────────────────
// 7. START THE ROUTER
//    App.php reads the URL and loads the correct controller and method.
// ─────────────────────────────────────────────────────────────────────────────
$app = new App();
