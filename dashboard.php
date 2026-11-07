<?php
require_once __DIR__ . '/includes/boot.php';
Auth::requireLogin();
$user = Auth::user();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verifyRequest(false);
    $action = $_POST['action'] ?? '';
    $docId  = (int)($_POST['doc_id'] ?? 0);

    if ($action === 'delete') {
        Document::delete($docId, $user['id']);
        Helpers::jsonResponse(['success' => true]);
    }
    if ($action === 'duplicate') {
        $newId = Document::duplicate($docId, $user['id']);
        Helpers::jsonResponse(['success' => true, 'new_id' => $newId]);
    }
    if ($action === 'toggle_status') {
        $doc = Document::getForUser($docId, $user['id']);
        if ($doc) {
            $newStatus = $doc['status'] === 'draft' ? 'final' : 'draft';
            Document::updateMeta($docId, $user['id'], ['status' => $newStatus]);
            Helpers::jsonResponse(['success' => true, 'status' => $newStatus]);
        }
        Helpers::jsonResponse(['success' => false], 404);
    }
    exit;
}

$search = trim($_GET['q'] ?? '');
$type   = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';
$docs   = Document::userDocs($user['id'], $search, $type, $status);

$counts = ['all' => 0, 'resume' => 0, 'cv' => 0, 'biodata' => 0, 'draft' => 0, 'final' => 0];
foreach ($docs as $d) {
    $counts['all']++;
    $counts[$d['doc_type']] = ($counts[$d['doc_type']] ?? 0) + 1;
    $counts[$d['status']]++;
}

$typeIcons = ['resume' => 'fa-briefcase', 'cv' => 'fa-scroll', 'biodata' => 'fa-id-card'];
$typeLabels = ['resume' => 'Resume', 'cv' => 'CV', 'biodata' => 'Biodata'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/components.css">
<style>
/* Doc-card action buttons — uniform height matching btn-sm */
.doc-actions .btn      { height: 30px; font-size: .76rem; }
.doc-actions .btn-icon { width: 30px;  height: 30px; }
/* Mobile topbar logo icon size */
.mobile-topbar .logo-icon { width: 28px; height: 28px; border-radius: 7px; font-size: .7rem; }
</style>
</head>
<body class="app-body">

<!-- Mobile topbar -->
<div class="mobile-topbar">
  <div class="mobile-logo">
    <div class="logo-icon" style="width:28px;height:28px;border-radius:7px;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.7rem"><i class="fas fa-file-alt"></i></div>
    Resume<span>Forge</span>
  </div>
  <button class="hamburger" id="hamburger-btn"><i class="fas fa-bars"></i></button>
</div>
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<!-- Session warning -->
<div id="session-warning">
  <i class="fas fa-clock"></i>
  Your session will expire in <strong>5 minutes</strong>. Save your work.
  <button class="btn btn-sm btn-primary" onclick="window.location.reload()">Stay Logged In</button>
  <button class="btn btn-sm btn-ghost" onclick="document.getElementById('session-warning').classList.remove('show')">Dismiss</button>
</div>
<div id="toast-container"></div>

<!-- ── Sidebar ───────────────────────────────────────────── -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <a href="dashboard.php">
      <div class="logo-icon"><i class="fas fa-file-alt"></i></div>
      <div class="logo-text">Resume<span>Forge</span></div>
    </a>
  </div>

  <div class="nav-section-label">Main Menu</div>
  <nav class="sidebar-nav">
    <a href="dashboard.php" class="nav-item active">
      <div class="nav-icon blue"><i class="fas fa-th-large"></i></div>
      Dashboard
    </a>
    <a href="wizard.php?action=new&type=resume" class="nav-item">
      <div class="nav-icon green"><i class="fas fa-briefcase"></i></div>
      New Resume
    </a>
    <a href="wizard.php?action=new&type=cv" class="nav-item">
      <div class="nav-icon purple"><i class="fas fa-scroll"></i></div>
      New CV
    </a>
    <a href="wizard.php?action=new&type=biodata" class="nav-item">
      <div class="nav-icon orange"><i class="fas fa-id-card"></i></div>
      New Biodata
    </a>

    <div class="nav-section-label" style="margin-top:8px">Quick Tools</div>
    <a href="quick.php" class="nav-item">
      <div class="nav-icon pink"><i class="fas fa-bolt"></i></div>
      Quick Mode
    </a>
    <div class="nav-section-label" style="margin-top:8px">Account</div>
    <a href="profile.php" class="nav-item">
      <div class="nav-icon" style="background:#f5f3ff;color:#7c3aed"><i class="fas fa-user-circle"></i></div>
      My Profile
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
      <div>
        <div class="user-name"><?= Helpers::e($user['name']) ?></div>
        <div class="user-email"><?= Helpers::e($user['email']) ?></div>
      </div>
    </div>
    <a href="logout.php" class="nav-item nav-logout">
      <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
      Sign Out
    </a>
  </div>
</aside>

<!-- ── Main content ───────────────────────────────────────── -->
<main class="main-content">

  <!-- Page header -->
  <div class="page-header">
    <div class="page-header-left">
      <div class="page-title">My Documents</div>
      <div class="page-sub">Manage all your resumes, CVs and biodatas in one place</div>
    </div>
    <a href="wizard.php?action=new" class="btn btn-primary btn-lg">
      <i class="fas fa-plus"></i> New Document
    </a>
  </div>

  <!-- Stats row -->
  <div class="stats-row">
    <div class="stat-card stat-blue">
      <div class="stat-icon"><i class="fas fa-folder-open"></i></div>
      <div class="stat-info"><div class="stat-num"><?= $counts['all'] ?></div><div class="stat-label">Total Docs</div></div>
    </div>
    <div class="stat-card stat-green">
      <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
      <div class="stat-info"><div class="stat-num"><?= $counts['resume'] ?></div><div class="stat-label">Resumes</div></div>
    </div>
    <div class="stat-card stat-purple">
      <div class="stat-icon"><i class="fas fa-scroll"></i></div>
      <div class="stat-info"><div class="stat-num"><?= $counts['cv'] ?></div><div class="stat-label">CVs</div></div>
    </div>
    <div class="stat-card stat-orange">
      <div class="stat-icon"><i class="fas fa-id-card"></i></div>
      <div class="stat-info"><div class="stat-num"><?= $counts['biodata'] ?></div><div class="stat-label">Biodatas</div></div>
    </div>
    <div class="stat-card stat-yellow">
      <div class="stat-icon"><i class="fas fa-pen"></i></div>
      <div class="stat-info"><div class="stat-num"><?= $counts['draft'] ?></div><div class="stat-label">Drafts</div></div>
    </div>
    <div class="stat-card stat-teal">
      <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
      <div class="stat-info"><div class="stat-num"><?= $counts['final'] ?></div><div class="stat-label">Final</div></div>
    </div>
  </div>

  <!-- Filter bar -->
  <div class="filter-bar">
    <form method="GET" class="filter-form" id="filterForm">
      <div class="search-wrap">
        <i class="fas fa-search"></i>
        <input type="search" name="q" value="<?= Helpers::e($search) ?>"
          oninput="debounceSearch()" onchange="this.form.submit()"
          autocomplete="off" aria-label="Search documents">
      </div>
      <select name="type" onchange="this.form.submit()">
        <option value="">All Types</option>
        <option value="resume"  <?= $type==='resume'  ?'selected':'' ?>>Resume</option>
        <option value="cv"      <?= $type==='cv'      ?'selected':'' ?>>CV</option>
        <option value="biodata" <?= $type==='biodata' ?'selected':'' ?>>Biodata</option>
      </select>
      <select name="status" onchange="this.form.submit()">
        <option value="">All Status</option>
        <option value="draft" <?= $status==='draft'?'selected':'' ?>>Draft</option>
        <option value="final" <?= $status==='final'?'selected':'' ?>>Final</option>
      </select>
      <?php if ($search||$type||$status): ?>
        <a href="dashboard.php" class="btn btn-ghost btn-sm"><i class="fas fa-times"></i> Clear</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Documents grid -->
  <?php if (empty($docs)): ?>
  <div class="empty-state">
    <div class="empty-state-icon"><i class="fas fa-file-circle-plus"></i></div>
    <h3>No documents yet</h3>
    <p>Create your first resume, CV, or biodata to get started.</p>
    <div style="display:flex;gap:10px;margin-top:8px;flex-wrap:wrap;justify-content:center">
      <a href="wizard.php?action=new&type=resume" class="btn btn-primary"><i class="fas fa-briefcase"></i> New Resume</a>
      <a href="wizard.php?action=new&type=biodata" class="btn btn-ghost"><i class="fas fa-id-card"></i> New Biodata</a>
    </div>
  </div>
  <?php else: ?>
  <div class="docs-grid" id="docsGrid">
    <?php foreach ($docs as $doc): ?>
    <?php
      $icon  = $typeIcons[$doc['doc_type']] ?? 'fa-file-alt';
      $label = $typeLabels[$doc['doc_type']] ?? 'Document';
    ?>
    <div class="doc-card" id="doc-<?= $doc['id'] ?>">
      <div class="doc-card-header">
        <div class="doc-card-icon doc-icon-<?= $doc['doc_type'] ?>">
          <i class="fas <?= $icon ?>"></i>
        </div>
        <div>
          <span class="badge badge-<?= $doc['doc_type'] ?>"><i class="fas <?= $icon ?>"></i> <?= $label ?></span>
        </div>
        <div class="doc-card-header-right">
          <span class="badge badge-<?= $doc['status'] ?>">
            <i class="fas fa-<?= $doc['status']==='final'?'check-circle':'pen' ?>"></i>
            <?= ucfirst($doc['status']) ?>
          </span>
        </div>
      </div>

      <div class="doc-title"><?= Helpers::e($doc['title']) ?></div>

      <div class="doc-meta">
        <span><i class="fas fa-clock"></i> <?= Helpers::timeAgo($doc['updated_at']) ?></span>
        <?php if ($doc['public_enabled']): ?>
        <span style="color:#10b981"><i class="fas fa-link"></i> Shared</span>
        <?php endif; ?>
        <?php if ($doc['view_count'] > 0): ?>
        <span><i class="fas fa-eye"></i> <?= $doc['view_count'] ?> views</span>
        <?php endif; ?>
      </div>

      <div class="doc-actions">
        <a href="wizard.php?id=<?= $doc['id'] ?>" class="btn btn-primary btn-sm">
          <i class="fas fa-edit"></i> Edit
        </a>
        <a href="api/pdf.php?id=<?= $doc['id'] ?>" target="_blank" class="btn btn-ghost btn-sm">
          <i class="fas fa-file-pdf"></i> PDF
        </a>
        <button type="button" class="btn btn-ghost btn-sm" onclick="toggleStatus(<?= $doc['id'] ?>)">
          <i class="fas fa-<?= $doc['status']==='draft'?'lock':'lock-open' ?>"></i>
          <?= $doc['status']==='draft'?'Finalize':'Draft' ?>
        </button>
        <div class="btn-more-wrap">
          <button type="button" class="btn-icon sm" onclick="toggleMenu(<?= $doc['id'] ?>)" title="More options" aria-label="More options">
            <i class="fas fa-ellipsis-v"></i>
          </button>
          <div class="more-menu" id="menu-<?= $doc['id'] ?>">
            <button onclick="duplicateDoc(<?= $doc['id'] ?>)">
              <i class="fas fa-copy"></i> Duplicate
            </button>
            <a href="wizard.php?id=<?= $doc['id'] ?>&tab=share">
              <i class="fas fa-share-alt"></i> Share & QR
            </a>
            <?php if ($doc['public_enabled'] && $doc['public_slug']): ?>
            <a href="view/<?= $doc['public_slug'] ?>" target="_blank">
              <i class="fas fa-external-link-alt"></i> Public Link
            </a>
            <?php endif; ?>
            <div class="divider"></div>
            <button class="danger" onclick="deleteDoc(<?= $doc['id'] ?>)">
              <i class="fas fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</main>

<script>
const CSRF = '<?= CSRF::token() ?>';
const APP_URL = '<?= APP_URL ?>';

async function doAction(action, docId) {
  const fd = new FormData();
  fd.append('_csrf', CSRF);
  fd.append('action', action);
  fd.append('doc_id', docId);
  const r = await fetch('dashboard.php', { method: 'POST', body: fd });
  return r.json();
}

function deleteDoc(id) {
  confirmDialog(
    'This will permanently delete the document and all its data. This cannot be undone.',
    async () => {
      const r = await doAction('delete', id);
      if (r.success) {
        Toast.success('Document deleted.');
        const el = document.getElementById('doc-' + id);
        el.style.transition = 'all .3s ease';
        el.style.opacity = '0';
        el.style.transform = 'scale(.94)';
        setTimeout(() => el.remove(), 300);
      } else {
        Toast.error('Could not delete. Please try again.');
      }
    },
    { title: 'Delete document?' }
  );
}

async function duplicateDoc(id) {
  Toast.info('Duplicating…', 1500);
  const r = await doAction('duplicate', id);
  if (r.success) {
    Toast.success('Document duplicated!');
    setTimeout(() => window.location.reload(), 800);
  } else {
    Toast.error('Duplication failed.');
  }
}

async function toggleStatus(id) {
  const r = await doAction('toggle_status', id);
  if (r.success) {
    Toast.success(`Status changed to ${r.status}.`);
    setTimeout(() => window.location.reload(), 600);
  }
}

function toggleMenu(id) {
  document.querySelectorAll('.more-menu').forEach(m => {
    if (m.id !== 'menu-' + id) m.classList.remove('open');
  });
  document.getElementById('menu-' + id)?.classList.toggle('open');
}
document.addEventListener('click', e => {
  if (!e.target.closest('.btn-more-wrap'))
    document.querySelectorAll('.more-menu').forEach(m => m.classList.remove('open'));
});
window.APP_URL = '<?= APP_URL ?>';

// Debounce search so it submits after user stops typing (400ms)
let _searchTimer;
function debounceSearch() {
  clearTimeout(_searchTimer);
  _searchTimer = setTimeout(() => document.getElementById('filterForm').submit(), 400);
}
</script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>

