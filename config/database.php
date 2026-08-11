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
$sslmode = (DB_HOST !== 'localhost' && DB_HOST !== '127.0.0.1') ? ';sslmode=require' : '';
$endpointOpt = '';
if (DB_HOST !== 'localhost' && DB_HOST !== '127.0.0.1') {
    $parts = explode('.', DB_HOST);
    if (!empty($parts[0])) {
        $endpointId = str_replace('-pooler', '', $parts[0]);
        $endpointOpt = ";options='endpoint=" . $endpointId . "'";
    }
}
define('DB_DSN', 'pgsql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . $sslmode . $endpointOpt);
