<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/boot.php';
header('Content-Type: application/json');

CSRF::verifyRequest(true);

if (!Auth::check()) {
    Helpers::jsonResponse(['success' => false, 'error' => 'Unauthenticated'], 401);
}

$raw    = file_get_contents('php://input');
$body   = json_decode($raw, true);
$action = $body['action'] ?? '';
$docId  = (int)($body['doc_id'] ?? 0);
$userId = Auth::userId();

if ($action === 'enable') {
    $expiry = !empty($body['expiry']) ? $body['expiry'] : null;
    $slug   = Document::enablePublic($docId, $userId, $expiry);
    $url    = APP_URL . '/view/' . $slug;
    Helpers::jsonResponse([
        'success' => true,
        'slug'    => $slug,
        'url'     => $url,
        'qr_url'  => Helpers::qrUrl($url),
    ]);
}

if ($action === 'disable') {
    Document::disablePublic($docId, $userId);
    Helpers::jsonResponse(['success' => true]);
}

if ($action === 'update_expiry') {
    $expiry = !empty($body['expiry']) ? $body['expiry'] : null;
    Document::updateMeta($docId, $userId, ['link_expiry' => $expiry]);
    Helpers::jsonResponse(['success' => true]);
}

Helpers::jsonResponse(['success' => false, 'error' => 'Unknown action'], 400);
