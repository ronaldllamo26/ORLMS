<?php

/**
 * ORLMS - Database Configuration
 *
 * Validates that all required database constants are defined
 * before the application attempts to connect.
 * The actual PDO connection is handled by core/Database.php (singleton).
 *
 * This file acts as a pre-flight check for DB config values.
 */

// ─────────────────────────────────────────────────────────────────────────────
// VALIDATE REQUIRED DB CONSTANTS
// These must already be defined in config/config.php before this file loads.
// ─────────────────────────────────────────────────────────────────────────────
$required_db_constants = ['DB_HOST', 'DB_PORT', 'DB_USER', 'DB_PASS', 'DB_NAME', 'DB_CHARSET'];

foreach ($required_db_constants as $constant) {
    if (!defined($constant)) {
        die('ORLMS Fatal Error: Missing database configuration constant: ' . $constant);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// DSN (Data Source Name)
// Used by core/Database.php when creating the PDO connection.
// ─────────────────────────────────────────────────────────────────────────────
define('DB_DSN', 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET);

