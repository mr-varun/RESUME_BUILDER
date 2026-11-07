<?php
require_once __DIR__ . '/includes/boot.php';

$slug = $_GET['slug'] ?? preg_replace('#^.*/view/([a-z0-9_-]+)$#i', '$1', $_SERVER['REQUEST_URI'] ?? '');
$slug = preg_replace('/[^a-zA-Z0-9_-]/', '', $slug);

if (!$slug) {
    http_response_code(404);
    die('Document not found.');
}

$doc = Document::getBySlug($slug);
if (!$doc) {
    http_response_code(404);
    die('This document is not available. The link may have expired or been disabled.');
}

Document::incrementView($doc['id']);

$d        = $doc['data'];
$template = $d['styling']['template'] ?? 'modern';
$allowed  = ['modern','classic','biodata','executive','minimal','creative','timeline','elegant','tech','corporate','compact','bold','fresh','indian','marriage_biodata'];
if (!in_array($template, $allowed, true)) $template = 'modern';
$templateFile = ROOT_PATH . '/templates/' . $template . '.php';
if (!file_exists($templateFile)) $templateFile = ROOT_PATH . '/templates/modern.php';

// Wrap with a minimal sharing bar on top
$shareUrl = APP_URL . '/view/' . $slug;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Helpers::e($doc['title']) ?> — Shared via <?= APP_NAME ?></title>
<meta property="og:title"       content="<?= Helpers::e($doc['title']) ?>">
<meta property="og:description" content="View this document on <?= Helpers::e(APP_NAME) ?>">
<meta property="og:url"         content="<?= Helpers::e($shareUrl) ?>">
<meta property="og:type"        content="website">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:#f1f5f9}
.share-bar{display:flex;align-items:center;justify-content:space-between;padding:10px 24px;background:#0f172a;position:sticky;top:0;z-index:100;gap:12px;flex-wrap:wrap}
.share-bar-brand{font-size:.9rem;font-weight:700;color:#60a5fa}
.share-bar-brand span{color:#fff}
.share-bar-title{font-size:.85rem;color:#94a3b8;flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0 12px}
.share-bar-actions{display:flex;gap:8px;flex-shrink:0}
.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;border:none;text-decoration:none;transition:.15s;white-space:nowrap;font-family:inherit}
.btn-primary{background:#2563eb;color:#fff}
.btn-ghost{background:rgba(255,255,255,.08);color:#94a3b8;border:1px solid rgba(255,255,255,.12)}
.btn-ghost:hover{background:rgba(255,255,255,.15);color:#f1f5f9}
.doc-frame{max-width:900px;margin:24px auto;background:#fff;box-shadow:0 4px 24px rgba(0,0,0,.12);border-radius:4px;overflow:hidden}
.view-meta{text-align:center;padding:12px;color:#94a3b8;font-size:.75rem;border-top:1px solid #e2e8f0}
</style>
</head>
<body>
<div class="share-bar">
  <a href="<?= APP_URL ?>" class="share-bar-brand">Resume<span>Forge</span></a>
  <span class="share-bar-title"><?= Helpers::e($doc['title']) ?></span>
  <div class="share-bar-actions">
    <a href="api/pdf.php?id=<?= $doc['id'] ?>&slug=<?= $slug ?>" target="_blank" class="btn btn-primary"><i class="fas fa-download"></i> Download PDF</a>
    <a href="https://wa.me/?text=<?= urlencode($shareUrl) ?>" target="_blank" class="btn btn-ghost" style="color:#25d366"><i class="fab fa-whatsapp"></i></a>
    <button onclick="copyUrl()" class="btn btn-ghost" id="copyBtn"><i class="fas fa-copy"></i> Copy Link</button>
  </div>
</div>

<div class="doc-frame">
  <?php include $templateFile; ?>
</div>

<div class="view-meta">
  <i class="fas fa-eye"></i> <?= $doc['view_count'] ?> views ·
  Shared via <a href="<?= APP_URL ?>" style="color:#2563eb"><?= APP_NAME ?></a> ·
  Created <?= Helpers::formatDate($doc['created_at']) ?>
</div>

<script>
function copyUrl() {
  navigator.clipboard.writeText('<?= Helpers::e($shareUrl) ?>').then(() => {
    const btn = document.getElementById('copyBtn');
    btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
    setTimeout(() => { btn.innerHTML = '<i class="fas fa-copy"></i> Copy Link'; }, 2000);
  });
}
</script>
</body>
</html>
