<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/boot.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Helpers::jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

CSRF::verifyRequest(true);

if (!Auth::check()) {
    Helpers::jsonResponse(['success' => false, 'error' => 'Unauthenticated'], 401);
}

$raw  = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!$body || !isset($body['doc_id'], $body['data'])) {
    Helpers::jsonResponse(['success' => false, 'error' => 'Invalid payload'], 400);
}

$docId  = (int) $body['doc_id'];
$userId = Auth::userId();
$data   = $body['data'];
$meta   = [];

if (isset($body['title']))    $meta['title']    = $body['title'];
if (isset($body['doc_type'])) $meta['doc_type'] = in_array($body['doc_type'], ['resume','cv','biodata']) ? $body['doc_type'] : 'resume';
if (isset($body['status']))   $meta['status']   = in_array($body['status'], ['draft','final']) ? $body['status'] : 'draft';

$saved = Document::save($docId, $userId, $data, $meta);

Helpers::jsonResponse(['success' => $saved, 'ts' => date('H:i:s')]);
