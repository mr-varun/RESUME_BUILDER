<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/boot.php';

$docId = (int)($_GET['id'] ?? 0);

// Allow either logged-in user's doc or a guest doc (quick mode)
if (Auth::check()) {
    $doc = Document::getForUser($docId, Auth::userId());
    if (!$doc) {
        // Try guest doc stored in session
        if (isset($_SESSION['guest_doc_id']) && $_SESSION['guest_doc_id'] === $docId) {
            $doc = Document::get($docId);
        }
    }
} else {
    // Quick mode: only allow guest doc from session
    if (isset($_SESSION['guest_doc_id']) && $_SESSION['guest_doc_id'] === $docId) {
        $doc = Document::get($docId);
    } else {
        $doc = null;
    }
}

if (!$doc) {
    echo '<html><body style="display:flex;align-items:center;justify-content:center;height:100vh;font-family:Inter,sans-serif;color:#94a3b8;font-size:.9rem"><p>Preview not available</p></body></html>';
    exit;
}

$template = $doc['data']['styling']['template'] ?? 'modern';
$allowed  = ['modern','classic','biodata','executive','minimal','creative','timeline','elegant','tech','corporate','compact','bold','fresh','indian','marriage_biodata'];
if (!in_array($template, $allowed, true)) $template = 'modern';

$templateFile = ROOT_PATH . '/templates/' . $template . '.php';
if (!file_exists($templateFile)) {
    $templateFile = ROOT_PATH . '/templates/modern.php';
}

$d = $doc['data'];
include $templateFile;
