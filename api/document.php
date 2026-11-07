<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/boot.php';
header('Content-Type: application/json');

if (!Auth::check()) {
    Helpers::jsonResponse(['success' => false, 'error' => 'Unauthenticated'], 401);
}
CSRF::verifyRequest(true);

$raw    = file_get_contents('php://input');
$body   = json_decode($raw, true) ?? [];
$action = $body['action'] ?? ($_GET['action'] ?? '');
$userId = Auth::userId();

switch ($action) {
    case 'create':
        $type  = in_array($body['doc_type'] ?? '', ['resume','cv','biodata']) ? $body['doc_type'] : 'resume';
        $title = trim($body['title'] ?? 'Untitled ' . ucfirst($type));
        $id    = Document::create($userId, $type, $title);
        Helpers::jsonResponse(['success' => true, 'doc_id' => $id,
            'redirect' => APP_URL . '/wizard.php?id=' . $id]);
        break;

    case 'delete':
        $id     = (int)($body['doc_id'] ?? 0);
        $result = Document::delete($id, $userId);
        Helpers::jsonResponse(['success' => $result]);
        break;

    case 'duplicate':
        $id    = (int)($body['doc_id'] ?? 0);
        $newId = Document::duplicate($id, $userId);
        Helpers::jsonResponse(['success' => (bool)$newId, 'new_id' => $newId]);
        break;

    case 'toggle_status':
        $id  = (int)($body['doc_id'] ?? 0);
        $doc = Document::getForUser($id, $userId);
        if (!$doc) Helpers::jsonResponse(['success' => false, 'error' => 'Not found'], 404);
        $newStatus = $doc['status'] === 'draft' ? 'final' : 'draft';
        Document::updateMeta($id, $userId, ['status' => $newStatus]);
        Helpers::jsonResponse(['success' => true, 'status' => $newStatus]);
        break;

    default:
        Helpers::jsonResponse(['success' => false, 'error' => 'Unknown action'], 400);
}
