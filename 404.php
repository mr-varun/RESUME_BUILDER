<?php
http_response_code(404);
$appUrl  = defined('APP_URL') ? APP_URL : '';
$appName = defined('APP_NAME') ? APP_NAME : 'ResumeForge';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 Not Found — <?= $appName ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;background:#f8fafc;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
.box{text-align:center;max-width:440px}
.code{font-size:7rem;font-weight:900;color:#e2e8f0;letter-spacing:-.05em;line-height:1;margin-bottom:0}
.icon{font-size:3rem;color:#6366f1;margin-bottom:16px}
h1{font-size:1.5rem;font-weight:800;color:#1e293b;margin-bottom:8px}
p{font-size:.9rem;color:#64748b;line-height:1.7;margin-bottom:28px}
.actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.btn{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:9px;font-size:.875rem;font-weight:600;text-decoration:none;transition:.15s;border:none;cursor:pointer;font-family:inherit}
.btn-primary{background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;box-shadow:0 4px 14px rgba(99,102,241,.3)}
.btn-ghost{background:#fff;color:#64748b;border:1.5px solid #e2e8f0}
.btn-ghost:hover{background:#f1f5f9;color:#1e293b}
</style>
</head>
<body>
<div class="box">
  <div class="code">404</div>
  <div class="icon"><i class="fas fa-file-circle-question"></i></div>
  <h1>Page not found</h1>
  <p>The page you're looking for doesn't exist, may have moved, or the link has expired.</p>
  <div class="actions">
    <a href="<?= $appUrl ?>/dashboard.php" class="btn btn-primary"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="<?= $appUrl ?>/index.php" class="btn btn-ghost"><i class="fas fa-home"></i> Home</a>
  </div>
</div>
</body>
</html>
