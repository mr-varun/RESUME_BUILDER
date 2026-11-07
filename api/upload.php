<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/boot.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Helpers::jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

CSRF::verifyRequest(false); // verifies $_POST['_csrf']

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    Helpers::jsonResponse(['success' => false, 'error' => 'No file uploaded or upload error']);
}

$file = $_FILES['photo'];

// Validate size
if ($file['size'] > MAX_PHOTO_SIZE) {
    Helpers::jsonResponse(['success' => false, 'error' => 'File too large. Max 2 MB allowed.']);
}

// Validate MIME type using finfo (not just extension)
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
if (!in_array($mime, ALLOWED_PHOTO_TYPES, true)) {
    Helpers::jsonResponse(['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, WebP allowed.']);
}

// Generate safe filename
$ext      = match($mime) { 'image/png' => '.png', 'image/webp' => '.webp', default => '.jpg' };
$filename = bin2hex(random_bytes(12)) . $ext;
$destPath = UPLOAD_DIR . $filename;

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    Helpers::jsonResponse(['success' => false, 'error' => 'Failed to save file. Check permissions.']);
}

// Delete old photo if provided — validate path stays within UPLOAD_DIR
if (!empty($_POST['old_photo'])) {
    $safeBase = basename($_POST['old_photo']);
    // Only allow alphanumeric, dash, dot — no path traversal
    if (preg_match('/^[a-zA-Z0-9_\-]+\.(jpg|jpeg|png|webp)$/i', $safeBase)) {
        $oldFile = UPLOAD_DIR . $safeBase;
        // Extra check: resolved path must start with UPLOAD_DIR
        $realOld = realpath($oldFile);
        $realDir = realpath(UPLOAD_DIR);
        if ($realOld && $realDir && str_starts_with($realOld, $realDir)) {
            @unlink($realOld);
        }
    }
}

Helpers::jsonResponse(['success' => true, 'filename' => $filename, 'url' => UPLOAD_URL . $filename]);
