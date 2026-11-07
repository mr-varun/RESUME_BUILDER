<?php
require_once __DIR__ . '/includes/boot.php';
Auth::requireLogin();
$user     = Auth::user();
$fullUser = DB::row('SELECT * FROM users WHERE id = ?', [$user['id']]);

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyRequest();
    $action = $_POST['action'] ?? '';

    // ── Update profile info ──────────────────────────────────
    if ($action === 'update_profile') {
        $name  = trim($_POST['name']  ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');
        $phone = preg_replace('/[^0-9+\-() ]/', '', $phone);
        if (strlen($name) < 2)
            $error = 'Name must be at least 2 characters.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $error = 'Invalid email address.';
        else {
            $existing = DB::row('SELECT id FROM users WHERE email = ? AND id != ?', [$email, $user['id']]);
            if ($existing) {
                $error = 'That email is already used by another account.';
            } else {
                DB::exec('UPDATE users SET name=?, email=?, phone=?, updated_at=NOW() WHERE id=?',
                    [$name, $email, $phone, $user['id']]);
                $_SESSION['user_name']  = $name;
                $_SESSION['user_email'] = $email;
                $fullUser['name']  = $name;
                $fullUser['email'] = $email;
                $fullUser['phone'] = $phone;
                $success = 'Profile updated successfully.';
            }
        }
    }

    // ── Change password ──────────────────────────────────────
    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!password_verify($current, $fullUser['password']))
            $error = 'Current password is incorrect.';
        elseif (strlen($new) < 8)
            $error = 'New password must be at least 8 characters.';
        elseif ($new !== $confirm)
            $error = 'New passwords do not match.';
        else {
            $hash = password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]);
            DB::exec('UPDATE users SET password=?, updated_at=NOW() WHERE id=?', [$hash, $user['id']]);
            $success = 'Password changed successfully.';
        }
    }

    // ── Delete account ───────────────────────────────────────
    if ($action === 'delete_account') {
        $confirm = $_POST['confirm_delete'] ?? '';
        if ($confirm !== 'DELETE')
            $error = 'Type DELETE exactly to confirm.';
        else {
            DB::exec('DELETE FROM users WHERE id=?', [$user['id']]);
            Auth::logout();
            Helpers::redirect(APP_URL . '/index.php');
        }
    }
}

// ── Stats ────────────────────────────────────────────────────
$docCount  = DB::row('SELECT COUNT(*) as c FROM documents WHERE user_id=?', [$user['id']])['c'] ?? 0;
$sharedCnt = DB::row('SELECT COUNT(*) as c FROM documents WHERE user_id=? AND public_enabled=1', [$user['id']])['c'] ?? 0;
$totalViews= DB::row('SELECT COALESCE(SUM(view_count),0) as v FROM documents WHERE user_id=?', [$user['id']])['v'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/components.css">
</head>
<body class="app-body">

<!-- Sidebar -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <a href="dashboard.php">
      <div class="logo-icon"><i class="fas fa-file-alt"></i></div>
      <div class="logo-text">Resume<span>Forge</span></div>
    </a>
  </div>
  <div class="nav-section-label">Main Menu</div>
  <nav class="sidebar-nav">
    <a href="dashboard.php" class="nav-item"><div class="nav-icon blue"><i class="fas fa-th-large"></i></div> Dashboard</a>
    <a href="wizard.php?action=new&type=resume" class="nav-item"><div class="nav-icon green"><i class="fas fa-briefcase"></i></div> New Resume</a>
    <a href="wizard.php?action=new&type=cv" class="nav-item"><div class="nav-icon purple"><i class="fas fa-scroll"></i></div> New CV</a>
    <a href="wizard.php?action=new&type=biodata" class="nav-item"><div class="nav-icon orange"><i class="fas fa-id-card"></i></div> New Biodata</a>
    <div class="nav-section-label">Account</div>
    <a href="profile.php" class="nav-item active"><div class="nav-icon pink"><i class="fas fa-user-circle"></i></div> My Profile</a>
  </nav>
  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
      <div><div class="user-name"><?= Helpers::e($user['name']) ?></div><div class="user-email"><?= Helpers::e($user['email']) ?></div></div>
    </div>
    <a href="logout.php" class="nav-item nav-logout"><div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div> Sign Out</a>
  </div>
</aside>

<main class="main-content">
  <div class="page-header">
    <div>
      <div class="page-title">My Profile</div>
      <div class="page-sub">Manage your account information and preferences</div>
    </div>
    <a href="dashboard.php" class="btn btn-ghost"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
  </div>

  <?php if ($success): ?>
  <div class="alert alert-success" id="flash-msg"><i class="fas fa-check-circle"></i><?= Helpers::e($success) ?></div>
  <?php elseif ($error): ?>
  <div class="alert alert-error" id="flash-msg"><i class="fas fa-exclamation-circle"></i><?= Helpers::e($error) ?></div>
  <?php endif; ?>

  <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start">

    <!-- Left: avatar + stats -->
    <div>
      <div class="profile-section" style="text-align:center;padding:28px">
        <div class="profile-avatar-lg" style="margin:0 auto 16px">
          <?= strtoupper(substr($user['name'], 0, 1)) ?>
        </div>
        <div style="font-size:1rem;font-weight:700;color:#1e293b"><?= Helpers::e($fullUser['name']) ?></div>
        <div style="font-size:.8rem;color:#64748b;margin-top:3px"><?= Helpers::e($fullUser['email']) ?></div>
        <div style="font-size:.74rem;color:#94a3b8;margin-top:6px">
          Member since <?= date('M Y', strtotime($fullUser['created_at'])) ?>
        </div>
      </div>

      <!-- Stats -->
      <div class="profile-section">
        <div class="profile-section-title"><i class="fas fa-chart-bar"></i> Account Stats</div>
        <div style="display:flex;flex-direction:column;gap:12px">
          <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0">
            <div style="display:flex;align-items:center;gap:8px;font-size:.82rem;color:#475569">
              <i class="fas fa-file-alt" style="color:#6366f1;width:16px"></i> Documents
            </div>
            <span style="font-size:.9rem;font-weight:700;color:#1e293b"><?= $docCount ?></span>
          </div>
          <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0">
            <div style="display:flex;align-items:center;gap:8px;font-size:.82rem;color:#475569">
              <i class="fas fa-share-alt" style="color:#10b981;width:16px"></i> Shared Docs
            </div>
            <span style="font-size:.9rem;font-weight:700;color:#1e293b"><?= $sharedCnt ?></span>
          </div>
          <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0">
            <div style="display:flex;align-items:center;gap:8px;font-size:.82rem;color:#475569">
              <i class="fas fa-eye" style="color:#f59e0b;width:16px"></i> Total Views
            </div>
            <span style="font-size:.9rem;font-weight:700;color:#1e293b"><?= $totalViews ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right: forms -->
    <div>
      <!-- Update profile -->
      <div class="profile-section">
        <div class="profile-section-title"><i class="fas fa-user-edit"></i> Personal Information</div>
        <form method="POST">
          <?= CSRF::field() ?>
          <input type="hidden" name="action" value="update_profile">
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" name="name" value="<?= Helpers::e($fullUser['name']) ?>" required>
            </div>
            <div class="form-group">
              <label>Phone</label>
              <input type="tel" name="phone" value="<?= Helpers::e($fullUser['phone'] ?? '') ?>">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:16px">
            <label>Email Address</label>
            <input type="email" name="email" value="<?= Helpers::e($fullUser['email']) ?>" required>
          </div>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
        </form>
      </div>

      <!-- Change password -->
      <div class="profile-section">
        <div class="profile-section-title"><i class="fas fa-lock"></i> Change Password</div>
        <form method="POST">
          <?= CSRF::field() ?>
          <input type="hidden" name="action" value="change_password">
          <div class="form-group" style="margin-bottom:12px">
            <label for="current_password">Current Password</label>
            <input id="current_password" type="password" name="current_password"
              autocomplete="current-password" required>
          </div>
          <div class="form-row" style="margin-bottom:16px">
            <div class="form-group">
              <label for="newPwd">New Password</label>
              <input id="newPwd" type="password" name="new_password"
                autocomplete="new-password" required minlength="8">
            </div>
            <div class="form-group">
              <label for="confirmPwd">Confirm New Password</label>
              <input id="confirmPwd" type="password" name="confirm_password"
                autocomplete="new-password" required minlength="8" oninput="checkPwdMatch()">
            </div>
          </div>
          <div id="pwd-match-msg" style="display:none;font-size:.76rem;margin-bottom:12px"></div>
          <!-- Password strength -->
          <div style="margin-bottom:14px">
            <div style="display:flex;justify-content:space-between;font-size:.72rem;color:#94a3b8;margin-bottom:4px">
              <span>Password strength</span><span id="pwd-strength-label">—</span>
            </div>
            <div class="progress-h"><div class="progress-h-bar" id="pwd-strength-bar" style="width:0%"></div></div>
          </div>
          <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Update Password</button>
        </form>
      </div>

      <!-- Danger zone -->
      <div class="profile-section danger-zone">
        <div class="profile-section-title"><i class="fas fa-exclamation-triangle"></i> Danger Zone</div>
        <p style="font-size:.82rem;color:#64748b;margin-bottom:16px">
          Deleting your account will permanently remove all your documents, shared links, and data. This action cannot be undone.
        </p>
        <button class="btn btn-danger btn-sm" onclick="document.getElementById('delete-form').style.display=document.getElementById('delete-form').style.display==='none'?'':'none'">
          <i class="fas fa-trash"></i> Delete My Account
        </button>
        <form method="POST" id="delete-form" style="display:none;margin-top:14px;padding:14px;background:#fef2f2;border-radius:8px;border:1px solid #fca5a5">
          <?= CSRF::field() ?>
          <input type="hidden" name="action" value="delete_account">
          <div class="form-group" style="margin-bottom:10px">
            <label style="color:#dc2626">Type <strong>DELETE</strong> to confirm</label>
            <input type="text" name="confirm_delete" style="border-color:#fca5a5">
          </div>
          <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Permanently Delete Account</button>
        </form>
      </div>
    </div>
  </div>
</main>

<script>
function checkPwdMatch() {
  const n = document.getElementById('newPwd').value;
  const c = document.getElementById('confirmPwd').value;
  const msg = document.getElementById('pwd-match-msg');
  if (!c) { msg.style.display = 'none'; return; }
  msg.style.display = 'flex';
  msg.style.alignItems = 'center';
  msg.style.gap = '5px';
  if (n === c) {
    msg.style.color = '#059669';
    msg.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
  } else {
    msg.style.color = '#dc2626';
    msg.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
  }
}
document.getElementById('newPwd')?.addEventListener('input', function() {
  const v = this.value;
  const bar = document.getElementById('pwd-strength-bar');
  const lbl = document.getElementById('pwd-strength-label');
  let score = 0;
  if (v.length >= 8)  score++;
  if (v.length >= 12) score++;
  if (/[A-Z]/.test(v)) score++;
  if (/[0-9]/.test(v)) score++;
  if (/[^A-Za-z0-9]/.test(v)) score++;
  const map = [[0,'0%','','#e2e8f0'],[1,'25%','Weak','#ef4444'],[2,'50%','Fair','#f59e0b'],[3,'65%','Good','#3b82f6'],[4,'85%','Strong','#10b981'],[5,'100%','Very Strong','#059669']];
  const [,w,t,c] = map[Math.min(score,5)];
  bar.style.width = w; bar.style.background = c;
  lbl.textContent = t; lbl.style.color = c;
  checkPwdMatch();
});
// Auto-hide flash
setTimeout(() => { document.getElementById('flash-msg')?.remove(); }, 4000);
</script>
</body>
</html>

