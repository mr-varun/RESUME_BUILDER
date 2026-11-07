<?php
// ── Database ──────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'resume_builder');
define('DB_USER', 'root');          // Change for production
define('DB_PASS', '');              // Change for production
define('DB_CHARSET', 'utf8mb4');

// ── Application ───────────────────────────────────────────
define('APP_NAME', 'ResumeForge');
define('APP_URL',  'http://localhost/RESUME_BUILDER'); // Change for production
define('APP_VERSION', '1.0.0');

// ── Paths ─────────────────────────────────────────────────
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_DIR',  ROOT_PATH . '/uploads/photos/');
define('UPLOAD_URL',  APP_URL  . '/uploads/photos/');
define('TEMPLATE_DIR', ROOT_PATH . '/templates/');

// ── File upload limits ────────────────────────────────────
define('MAX_PHOTO_SIZE', 2 * 1024 * 1024);   // 2 MB
define('ALLOWED_PHOTO_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// ── Session config (call before session_start) ────────────
define('SESSION_NAME', 'rf_sess');
define('SESSION_LIFETIME', 7 * 24 * 3600);  // 7 days

// ── Slug length for public links ──────────────────────────
define('SLUG_LENGTH', 10);

// ── Timezone ──────────────────────────────────────────────
date_default_timezone_set('Asia/Kolkata');

// ── Feature flags ─────────────────────────────────────────
define('REGISTRATION_ENABLED', true);   // ← set true to allow new signups

// ── Error display (set false on production) ───────────────
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
