<?php
/**
 * One-time installer — DELETE THIS FILE after running!
 * Access: http://yourdomain.com/install.php
 */
define('ROOT_PATH', __DIR__);
require_once __DIR__ . '/config/config.php';

// Block if already installed
$lockFile = __DIR__ . '/.installed';
if (file_exists($lockFile)) {
    die('Already installed. Please delete install.php from your server.');
}

$errors = [];
$done   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = trim($_POST['db_host'] ?? 'localhost');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = $_POST['db_pass'] ?? '';

    // Validate db name — only allow safe identifier characters
    if (!$dbName || !$dbUser) {
        $errors[] = 'DB Name and User are required.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $dbName)) {
        $errors[] = 'Database name may only contain letters, numbers and underscores.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$dbHost;charset=utf8mb4", $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            // Safe: $dbName validated to [a-zA-Z0-9_] above
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$dbName}`");

            $sql = file_get_contents(__DIR__ . '/database.sql');
            // Strip USE statement since we set it above
            $sql = preg_replace('/^USE\s+`[^`]+`;/m', '', $sql);
            $sql = preg_replace('/^CREATE DATABASE.*?;/ms', '', $sql);
            foreach (array_filter(array_map('trim', explode(';', $sql))) as $query) {
                if ($query) $pdo->exec($query);
            }

            // Update config with new values
            $configPath = __DIR__ . '/config/config.php';
            $content    = file_get_contents($configPath);
            $content    = preg_replace("/define\('DB_HOST',.*?\);/", "define('DB_HOST', '$dbHost');", $content);
            $content    = preg_replace("/define\('DB_NAME',.*?\);/", "define('DB_NAME', '$dbName');", $content);
            $content    = preg_replace("/define\('DB_USER',.*?\);/", "define('DB_USER', '$dbUser');", $content);
            $content    = preg_replace("/define\('DB_PASS',.*?\);/", "define('DB_PASS', '$dbPass');", $content);
            file_put_contents($configPath, $content);

            // Create upload dirs
            $dirs = [__DIR__ . '/uploads', __DIR__ . '/uploads/photos'];
            foreach ($dirs as $dir) {
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                file_put_contents($dir . '/.htaccess', "Options -Indexes\nphp_flag engine off\n");
            }

            // Lock file
            file_put_contents($lockFile, date('Y-m-d H:i:s'));

            $done = true;
        } catch (PDOException $e) {
            $errors[] = 'DB Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Install — <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}body{font-family:'Inter',sans-serif;background:#0f172a;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
.card{background:#1e293b;border:1px solid #334155;border-radius:12px;padding:32px;width:100%;max-width:480px}
h1{color:#60a5fa;font-size:1.4rem;font-weight:800;margin-bottom:4px}h1 span{color:#fff}
p{color:#64748b;font-size:.85rem;margin-bottom:24px}
label{display:block;font-size:.8rem;font-weight:500;color:#94a3b8;margin-bottom:6px}
input{width:100%;padding:10px 12px;background:#0f172a;border:1px solid #334155;border-radius:8px;color:#f1f5f9;font-size:.9rem;font-family:inherit;margin-bottom:14px}
input:focus{outline:none;border-color:#2563eb}
button{width:100%;padding:12px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer;font-family:inherit}
button:hover{background:#1d4ed8}
.error{background:#7f1d1d;border:1px solid #991b1b;color:#fca5a5;padding:12px;border-radius:8px;font-size:.85rem;margin-bottom:16px}
.success{background:#14532d;border:1px solid #166534;color:#86efac;padding:16px;border-radius:8px;font-size:.9rem}
.success a{color:#4ade80;font-weight:600}
</style>
</head>
<body>
<div class="card">
  <h1>Resume<span>Forge</span> Setup</h1>
  <p>One-time installation wizard. Delete install.php after completing setup.</p>

  <?php if ($done): ?>
  <div class="success">
    <strong>Installation complete!</strong><br><br>
    ✅ Database created and configured<br>
    ✅ Upload directories created<br><br>
    Demo login: <strong>demo@example.com</strong> / <strong>demo1234</strong><br><br>
    <strong>Important:</strong> Delete this file from your server!<br><br>
    <a href="index.php">→ Go to <?= APP_NAME ?></a>
  </div>
  <?php else: ?>
  <?php foreach ($errors as $err): ?><div class="error"><?= htmlspecialchars($err) ?></div><?php endforeach; ?>
  <form method="POST">
    <label>DB Host</label><input name="db_host" value="localhost" required>
    <label>DB Name</label><input name="db_name" value="resume_builder" required>
    <label>DB Username</label><input name="db_user" required>
    <label>DB Password</label><input type="password" name="db_pass">
    <button type="submit">Run Installation</button>
  </form>
  <?php endif; ?>
</div>
</body>
</html>
