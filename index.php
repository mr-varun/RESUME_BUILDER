<?php
require_once __DIR__ . '/includes/boot.php';

if (Auth::check()) Helpers::redirect(APP_URL . '/dashboard.php');

$error = '';
$tab   = $_GET['tab'] ?? 'login';
if (!REGISTRATION_ENABLED && $tab === 'register') $tab = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyRequest();
    $tab = $_POST['tab'] ?? 'login';
    if ($tab === 'login') {
        $r = Auth::login($_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($r['success']) Helpers::redirect(APP_URL . '/dashboard.php');
        else $error = $r['error'];
    } elseif ($tab === 'register') {
        if (!REGISTRATION_ENABLED) { $error = 'Registration is currently disabled.'; }
        else {
            $r = Auth::register($_POST['name'] ?? '', $_POST['email'] ?? '', $_POST['password'] ?? '', $_POST['phone'] ?? '');
            if ($r['success']) Helpers::redirect(APP_URL . '/dashboard.php');
            else $error = $r['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= APP_NAME ?> — Professional Resume Builder</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html,body{min-height:100%;font-family:'Inter',system-ui,sans-serif;-webkit-font-smoothing:antialiased}
body{display:flex;min-height:100vh;background:#f8fafc}

/* ── Left panel ── */
.hero{flex:1;display:flex;flex-direction:column;justify-content:center;padding:60px 64px;position:relative;overflow:hidden;min-height:100vh;background:#ffffff}
/* Subtle dot grid */
.hero::before{content:'';position:absolute;inset:0;background-image:radial-gradient(#e2e8f0 1px,transparent 1px);background-size:28px 28px;pointer-events:none}
/* Colored blobs */
.blob1{position:absolute;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(99,102,241,.08),transparent 70%);top:-120px;right:-80px;pointer-events:none}
.blob2{position:absolute;width:380px;height:380px;border-radius:50%;background:radial-gradient(circle,rgba(139,92,246,.07),transparent 70%);bottom:-80px;left:-60px;pointer-events:none}
.blob3{position:absolute;width:260px;height:260px;border-radius:50%;background:radial-gradient(circle,rgba(16,185,129,.06),transparent 70%);top:40%;right:10%;pointer-events:none}

.hero-content{position:relative;z-index:1;max-width:540px}

/* Logo */
.hero-logo{display:flex;align-items:center;gap:10px;margin-bottom:48px}
.logo-mark{width:40px;height:40px;border-radius:11px;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:center;font-size:1rem;color:#fff;box-shadow:0 6px 20px rgba(99,102,241,.3)}
.logo-name{font-size:1.15rem;font-weight:800;color:#1e293b;letter-spacing:-.02em}
.logo-name span{color:#6366f1}

/* Headline */
.hero-headline{font-size:2.9rem;font-weight:900;color:#0f172a;line-height:1.08;letter-spacing:-.04em;margin-bottom:18px}
.hero-headline .accent{background:linear-gradient(135deg,#6366f1,#8b5cf6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hero-sub{font-size:1rem;color:#64748b;line-height:1.7;margin-bottom:36px;max-width:440px}

/* Feature pills */
.feature-pills{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:36px}
.pill{display:inline-flex;align-items:center;gap:7px;padding:7px 14px;background:#fff;border:1.5px solid #e2e8f0;border-radius:50px;font-size:.8rem;font-weight:500;color:#475569;box-shadow:0 1px 3px rgba(0,0,0,.04);transition:.2s}
.pill:hover{border-color:#c7d2fe;color:#4f46e5;background:#eef2ff}
.pill i{font-size:.75rem}

/* Quick mode CTA */
.cta-quick{display:inline-flex;align-items:center;gap:12px;padding:12px 20px;background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;text-decoration:none;color:#374151;font-size:.875rem;font-weight:600;box-shadow:0 1px 4px rgba(0,0,0,.05);transition:.2s}
.cta-quick:hover{border-color:#c7d2fe;box-shadow:0 4px 12px rgba(99,102,241,.12);transform:translateY(-1px)}
.cta-icon{width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;flex-shrink:0}
.cta-badge{background:#fef3c7;color:#92400e;font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:5px;letter-spacing:.04em;text-transform:uppercase}

/* Stats strip */
.stats-strip{display:flex;gap:32px;margin-top:48px;padding-top:32px;border-top:1px solid #e2e8f0}
.stat-item{display:flex;flex-direction:column}
.stat-n{font-size:1.5rem;font-weight:800;color:#0f172a;letter-spacing:-.02em;display:block}
.stat-l{font-size:.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-top:1px;display:block}

/* Decorative resume card */
.hero-visual{position:absolute;right:-20px;bottom:60px;width:260px;transform:rotate(3deg);box-shadow:0 20px 60px rgba(0,0,0,.12);border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;pointer-events:none;z-index:0}
.rv-header{height:52px;background:linear-gradient(135deg,#6366f1,#8b5cf6);padding:12px 16px;display:flex;align-items:center;gap:10px}
.rv-avatar{width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.3);flex-shrink:0}
.rv-name{height:10px;background:rgba(255,255,255,.7);border-radius:3px;width:80px}
.rv-body{background:#fff;padding:14px 16px;display:flex;flex-direction:column;gap:8px}
.rv-line{height:6px;background:#e2e8f0;border-radius:3px}
.rv-line.w90{width:90%}.rv-line.w70{width:70%}.rv-line.w80{width:80%}.rv-line.w50{width:50%}
.rv-sec-title{height:5px;background:#6366f1;border-radius:2px;width:60px;opacity:.4}
.rv-badge{display:inline-flex;gap:4px;padding:3px 8px;background:#eef2ff;border-radius:4px}
.rv-badge-dot{width:32px;height:5px;background:#a5b4fc;border-radius:2px}
.rv-tags{display:flex;gap:5px}

/* ── Right auth panel ── */
.auth-panel{width:460px;flex-shrink:0;background:#f8fafc;border-left:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;padding:40px 36px;min-height:100vh}
.auth-box{width:100%;max-width:380px}
.auth-box-title{font-size:1.4rem;font-weight:800;color:#0f172a;margin-bottom:4px;letter-spacing:-.025em}
.auth-box-sub{font-size:.875rem;color:#64748b;margin-bottom:26px}
/* Tabs */
.auth-tabs{display:flex;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px;padding:4px;gap:4px;margin-bottom:22px}
.auth-tab{flex:1;height:34px;border-radius:8px;border:none;background:none;font-size:.875rem;font-weight:600;color:#94a3b8;cursor:pointer;font-family:inherit;transition:.15s}
.auth-tab.active{background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;box-shadow:0 2px 8px rgba(99,102,241,.3)}
/* Form */
.f-group{margin-bottom:14px}
.f-label{display:block;font-size:.78rem;font-weight:600;color:#475569;margin-bottom:5px}
.f-input{display:block;width:100%;height:40px;padding:0 13px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:.875rem;font-family:inherit;color:#1e293b;background:#fff;outline:none;transition:.15s}
.f-input:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.f-input::placeholder{color:#94a3b8}
/* Submit */
.btn-submit{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;height:42px;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;border:none;border-radius:10px;font-size:.9375rem;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:0 4px 16px rgba(99,102,241,.28);transition:.15s;margin-top:4px}
.btn-submit:hover{filter:brightness(1.07);box-shadow:0 6px 22px rgba(99,102,241,.35)}
.btn-submit:active{transform:translateY(1px)}
/* Divider */
.auth-divider{display:flex;align-items:center;gap:10px;margin:16px 0;color:#94a3b8;font-size:.78rem}
.auth-divider::before,.auth-divider::after{content:'';flex:1;height:1px;background:#e2e8f0}
/* Quick link */
.btn-quicklink{display:flex;align-items:center;justify-content:center;gap:9px;width:100%;height:40px;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.875rem;font-weight:600;color:#374151;cursor:pointer;font-family:inherit;text-decoration:none;transition:.15s}
.btn-quicklink:hover{border-color:#c7d2fe;background:#fafafe;color:#4f46e5}
.btn-quicklink .ql-icon{width:26px;height:26px;border-radius:7px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.72rem;flex-shrink:0}
/* Error */
.err-box{display:flex;align-items:center;gap:8px;padding:10px 13px;background:#fee2e2;border:1px solid #fca5a5;border-radius:9px;font-size:.82rem;color:#b91c1c;margin-bottom:14px}
.err-box i{flex-shrink:0}

@media(max-width:1100px){.hero{padding:40px 40px}.hero-visual{display:none}}
@media(max-width:920px){
  body{flex-direction:column}
  .hero{min-height:auto;padding:40px 24px}
  .hero-headline{font-size:2.2rem}
  .auth-panel{width:100%;border-left:none;border-top:1px solid #e2e8f0;padding:32px 24px;min-height:auto}
}
</style>
</head>
<body>

<!-- ── Left Hero ── -->
<div class="hero">
  <div class="blob1"></div>
  <div class="blob2"></div>
  <div class="blob3"></div>

  <!-- Decorative resume card -->
  <div class="hero-visual">
    <div class="rv-header"><div class="rv-avatar"></div><div class="rv-name"></div></div>
    <div class="rv-body">
      <div class="rv-sec-title"></div>
      <div class="rv-line w90"></div><div class="rv-line w70"></div><div class="rv-line w80"></div>
      <div class="rv-sec-title" style="margin-top:4px"></div>
      <div class="rv-line w80"></div><div class="rv-line w50"></div>
      <div class="rv-tags">
        <div class="rv-badge"><div class="rv-badge-dot"></div></div>
        <div class="rv-badge"><div class="rv-badge-dot" style="width:24px"></div></div>
        <div class="rv-badge"><div class="rv-badge-dot" style="width:20px"></div></div>
      </div>
    </div>
  </div>

  <div class="hero-content">
    <div class="hero-logo">
      <div class="logo-mark"><i class="fas fa-file-alt"></i></div>
      <div class="logo-name">Resume<span>Forge</span></div>
    </div>

    <h1 class="hero-headline">
      Build resumes that<br>get you <span class="accent">hired faster</span>
    </h1>
    <p class="hero-sub">
      Create stunning resumes, CVs, and biodatas in minutes. Live preview,
      15 professional templates, PDF download and shareable links — all free.
    </p>

    <div class="feature-pills">
      <span class="pill"><i class="fas fa-magic" style="color:#6366f1"></i> Step-by-step wizard</span>
      <span class="pill"><i class="fas fa-eye" style="color:#10b981"></i> Live preview</span>
      <span class="pill"><i class="fas fa-file-pdf" style="color:#ef4444"></i> PDF download</span>
      <span class="pill"><i class="fas fa-palette" style="color:#f59e0b"></i> 15 templates</span>
      <span class="pill"><i class="fas fa-link" style="color:#0ea5e9"></i> Share link + QR</span>
      <span class="pill"><i class="fas fa-save" style="color:#8b5cf6"></i> Auto-save</span>
    </div>

    <a href="quick.php" class="cta-quick">
      <div class="cta-icon"><i class="fas fa-bolt"></i></div>
      <div>
        <div style="font-size:.875rem;font-weight:700;color:#1e293b">Quick Biodata — No Login</div>
        <div style="font-size:.74rem;color:#64748b;margin-top:1px">Fill &amp; print in under 2 minutes</div>
      </div>
      <span class="cta-badge">Free</span>
    </a>

    <div class="stats-strip">
      <div class="stat-item"><span class="stat-n">15+</span><span class="stat-l">Templates</span></div>
      <div class="stat-item"><span class="stat-n">3</span><span class="stat-l">Doc Types</span></div>
      <div class="stat-item"><span class="stat-n">100%</span><span class="stat-l">Free</span></div>
      <div class="stat-item"><span class="stat-n">∞</span><span class="stat-l">Documents</span></div>
    </div>
  </div>
</div>

<!-- ── Auth Panel ── -->
<div class="auth-panel">
  <div class="auth-box">
    <div class="auth-box-title"><?= $tab==='register'?'Create account':'Welcome back 👋' ?></div>
    <div class="auth-box-sub"><?= $tab==='register'?'Sign up to start building your resume':'Sign in to continue to your dashboard' ?></div>

    <?php if ($error): ?>
    <div class="err-box"><i class="fas fa-exclamation-circle"></i><?= Helpers::e($error) ?></div>
    <?php endif; ?>

    <?php if (REGISTRATION_ENABLED): ?>
    <div class="auth-tabs">
      <button class="auth-tab <?= $tab==='login'?'active':'' ?>" onclick="switchTab('login')">Sign In</button>
      <button class="auth-tab <?= $tab==='register'?'active':'' ?>" onclick="switchTab('register')">Register</button>
    </div>
    <?php endif; ?>

    <!-- Login -->
    <form id="form-login" method="POST" style="<?= $tab!=='login'?'display:none':'' ?>">
      <?= CSRF::field() ?><input type="hidden" name="tab" value="login">
      <div class="f-group"><label class="f-label">Email Address</label>
        <input class="f-input" type="email" name="email" required value="<?= Helpers::e($_POST['email'] ?? '') ?>"></div>
      <div class="f-group"><label class="f-label">Password</label>
        <input class="f-input" type="password" name="password" required></div>
      <button type="submit" class="btn-submit">Sign In <i class="fas fa-arrow-right"></i></button>
    </form>

    <!-- Register -->
    <?php if (REGISTRATION_ENABLED): ?>
    <form id="form-register" method="POST" style="<?= $tab!=='register'?'display:none':'' ?>">
      <?= CSRF::field() ?><input type="hidden" name="tab" value="register">
      <div class="f-group"><label class="f-label">Full Name</label>
        <input class="f-input" type="text" name="name" required value="<?= Helpers::e($_POST['name'] ?? '') ?>"></div>
      <div class="f-group"><label class="f-label">Email Address</label>
        <input class="f-input" type="email" name="email" required value="<?= Helpers::e($_POST['email'] ?? '') ?>"></div>
      <div class="f-group"><label class="f-label">Phone <span style="font-weight:400;color:#94a3b8">(optional)</span></label>
        <input class="f-input" type="tel" name="phone" value="<?= Helpers::e($_POST['phone'] ?? '') ?>"></div>
      <div class="f-group"><label class="f-label">Password</label>
        <input class="f-input" type="password" name="password" required></div>
      <button type="submit" class="btn-submit">Create Account <i class="fas fa-arrow-right"></i></button>
    </form>
    <?php endif; ?>

    <div class="auth-divider">or</div>
    <a href="quick.php" class="btn-quicklink">
      <div class="ql-icon"><i class="fas fa-bolt"></i></div>
      Quick Biodata — No Login Required
    </a>
  </div>
</div>

<script>
function switchTab(tab) {
  document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('[id^="form-"]').forEach(f => f.style.display = 'none');
  document.querySelector(`.auth-tab[onclick*="${tab}"]`)?.classList.add('active');
  document.getElementById('form-' + tab).style.display = '';
  document.querySelector('.auth-box-title').textContent = tab === 'register' ? 'Create account' : 'Welcome back 👋';
  document.querySelector('.auth-box-sub').textContent   = tab === 'register' ? 'Sign up to start building your resume' : 'Sign in to continue to your dashboard';
}
</script>
</body>
</html>

