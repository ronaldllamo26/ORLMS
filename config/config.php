<?php

/**
 * ORLMS - Application Configuration
 *
 * Contains all global constants used throughout the system:
 * database credentials, app settings, and Groq API configuration.
 *
 * IMPORTANT: Do not commit this file to any public repository.
 * Add config.php to your .gitignore file.
 */

// ─────────────────────────────────────────────────────────────────────────────
// APPLICATION SETTINGS
// ─────────────────────────────────────────────────────────────────────────────
define('APP_NAME',    'Ordinance and Resolution Lifecycle Management System');
define('APP_SHORT',   'ORLMS');
define('APP_VERSION', '1.0.0');
define('APP_URL',     'http://localhost/Capstone');
define('APP_ROOT_URL', '/Capstone');

// Timezone — set to Philippine Standard Time
date_default_timezone_set('Asia/Manila');

// ─────────────────────────────────────────────────────────────────────────────
// DATABASE CREDENTIALS
// ─────────────────────────────────────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_NAME',    'orlms_db');
define('DB_CHARSET', 'utf8mb4');

// ─────────────────────────────────────────────────────────────────────────────
// GROQ API CONFIGURATION
// ─────────────────────────────────────────────────────────────────────────────
define('GROQ_API_KEY',     'YOUR_GROQ_API_KEY_HERE'); // Set your actual Groq API key here
define('GROQ_API_URL',     'https://api.groq.com/openai/v1/chat/completions');
define('GROQ_MODEL',       'llama-3.1-8b-instant');
define('GROQ_MAX_TOKENS',  2048);
define('GROQ_TEMPERATURE', 0.3); // Low temperature = more consistent/factual AI responses

// ─────────────────────────────────────────────────────────────────────────────
// FILE UPLOAD SETTINGS
// ─────────────────────────────────────────────────────────────────────────────
define('UPLOAD_PATH',     dirname(__DIR__) . '/public/uploads/documents/');
define('UPLOAD_URL',      APP_URL . '/public/uploads/documents/');
define('UPLOAD_MAX_SIZE', 10 * 1024 * 1024); // 10MB in bytes
define('UPLOAD_ALLOWED_TYPES', ['application/pdf', 'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// ─────────────────────────────────────────────────────────────────────────────
// DOCUMENT NUMBER FORMAT
// ─────────────────────────────────────────────────────────────────────────────
// Format: ORD-YYYY-NNN and RES-YYYY-NNN
// Example: ORD-2025-001, RES-2025-042
define('ORD_PREFIX', 'ORD');
define('RES_PREFIX', 'RES');

// ─────────────────────────────────────────────────────────────────────────────
// AI VALIDATION SETTINGS
// ─────────────────────────────────────────────────────────────────────────────
define('AI_DUPLICATE_THRESHOLD', 70);  // Flag as duplicate if similarity >= 70%
define('AI_SIMILAR_THRESHOLD',   50);  // Show comparison if similarity >= 50%

// ─────────────────────────────────────────────────────────────────────────────
// SESSION SETTINGS
// ─────────────────────────────────────────────────────────────────────────────
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// ─────────────────────────────────────────────────────────────────────────────
// USER ROLES
// ─────────────────────────────────────────────────────────────────────────────
define('ROLE_SUPER_ADMIN',       'super_admin');
define('ROLE_LEGISLATIVE_STAFF', 'legislative_staff');
define('ROLE_COMMITTEE_MEMBER',  'committee_member');
define('ROLE_SP_MEMBER',         'sp_member');

// ─────────────────────────────────────────────────────────────────────────────
// DOCUMENT STATUSES
// ─────────────────────────────────────────────────────────────────────────────
define('STATUS_DRAFT',    'draft');
define('STATUS_PENDING',  'submitted');
define('STATUS_REVIEWED', 'under_review');
define('STATUS_ENDORSED', 'endorsed');
define('STATUS_APPROVED', 'approved');
define('STATUS_ENACTED',  'enacted');
define('STATUS_REJECTED', 'rejected');
define('STATUS_ARCHIVED', 'archived');
define('STATUS_PUBLISHED','published');

// ─────────────────────────────────────────────────────────────────────────────
// PAGINATION
// ─────────────────────────────────────────────────────────────────────────────
define('RECORDS_PER_PAGE', 15);
