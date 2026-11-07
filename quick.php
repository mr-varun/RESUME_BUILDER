<?php
require_once __DIR__ . '/includes/boot.php';

$step  = (int)($_GET['step'] ?? 1);
$docId = (int)($_SESSION['guest_doc_id'] ?? 0);
$csrf  = CSRF::token();

// ── Load previous customer by phone (AJAX) ─────────────────
if (($_GET['action'] ?? '') === 'lookup_customer') {
    header('Content-Type: application/json');
    $phone = preg_replace('/[^0-9+]/', '', $_GET['phone'] ?? '');
    if (strlen($phone) < 7) { echo json_encode(['found' => false]); exit; }
    $cust = DB::row('SELECT c.*, d.data FROM customers c LEFT JOIN documents d ON d.id=c.last_doc_id WHERE c.phone = ?', [$phone]);
    if (!$cust) { echo json_encode(['found' => false]); exit; }
    $data = json_decode($cust['data'] ?? '{}', true) ?: [];
    echo json_encode(['found' => true, 'name' => $cust['name'], 'email' => $cust['email'], 'doc_count' => $cust['doc_count'], 'last_doc_id' => $cust['last_doc_id'], 'data' => $data]);
    exit;
}

// ── Load previous customer's document into current session ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'load_customer') {
    CSRF::verifyRequest();
    $lastDocId = (int)($_POST['last_doc_id'] ?? 0);
    if ($lastDocId) {
        $prevDoc = Document::get($lastDocId);
        if ($prevDoc) {
            // Clone into a new guest doc
            $newId = Document::createGuest('biodata', 'Quick Biodata');
            DB::exec('UPDATE documents SET data=?, updated_at=NOW() WHERE id=?',
                [json_encode($prevDoc['data'], JSON_UNESCAPED_UNICODE), $newId]);
            $_SESSION['guest_doc_id'] = $newId;
            Helpers::redirect(APP_URL . '/quick.php?step=1&loaded=1');
        }
    }
    Helpers::redirect(APP_URL . '/quick.php?step=1');
}

// ── Save customer record ────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_customer') {
    CSRF::verifyRequest();
    $name  = trim($_POST['cust_name']  ?? '');
    $phone = preg_replace('/[^0-9+]/', '', trim($_POST['cust_phone'] ?? ''));
    $email = trim($_POST['cust_email'] ?? '');
    // Validate email if provided
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $email = '';
    // Validate phone length (7–15 digits)
    if ($phone && (strlen($phone) < 7 || strlen($phone) > 16)) $phone = '';
    if ($name && $docId) {
        $existing = $phone ? DB::row('SELECT id FROM customers WHERE phone = ?', [$phone]) : null;
        if ($existing) {
            DB::exec('UPDATE customers SET name=?, email=?, last_doc_id=?, doc_count=doc_count+1, updated_at=NOW() WHERE id=?',
                [$name, $email, $docId, $existing['id']]);
        } else {
            DB::exec('INSERT INTO customers (name, phone, email, last_doc_id) VALUES (?,?,?,?)',
                [$name, $phone, $email, $docId]);
        }
    }
    Helpers::redirect(APP_URL . '/quick.php?step=4&saved=1');
}

// ── New / Reset ─────────────────────────────────────────────
if (($_GET['action'] ?? '') === 'new' || !$docId) {
    $docId = Document::createGuest('biodata', 'Quick Biodata');
    $_SESSION['guest_doc_id'] = $docId;
    Helpers::redirect(APP_URL . '/quick.php?step=1');
}

$doc = Document::get($docId);
if (!$doc) {
    $docId = Document::createGuest('biodata', 'Quick Biodata');
    $_SESSION['guest_doc_id'] = $docId;
    $doc = Document::get($docId);
}
$d = $doc['data'];

// Recent customers (for shop)
$customers = DB::rows('SELECT c.* FROM customers c ORDER BY c.updated_at DESC LIMIT 15');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quick Biodata — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/components.css">
<style>
body{background:#f8fafc}
.quick-wrap{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:24px 16px}
.quick-header{text-align:center;margin-bottom:28px;width:100%}
.quick-logo{display:inline-flex;align-items:center;gap:9px;font-size:1.15rem;font-weight:800;color:#1e293b;text-decoration:none;margin-bottom:6px}
.quick-logo .logo-icon{width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem}
.quick-logo span{color:#6366f1}
.quick-sub{font-size:.82rem;color:#94a3b8}
.quick-card{width:100%;max-width:560px;background:#fff;border-radius:14px;padding:24px;border:1px solid #e2e8f0;box-shadow:0 2px 12px rgba(0,0,0,.05)}
.quick-card h2{font-size:1rem;font-weight:700;color:#1e293b;margin-bottom:3px}
.quick-card .sub{font-size:.78rem;color:#94a3b8;margin-bottom:18px}
/* Step dots */
.step-dots{display:flex;gap:7px;margin-bottom:18px}
.step-dot{flex:1;height:4px;border-radius:2px;background:#e2e8f0;transition:.3s}
.step-dot.done{background:#10b981}
.step-dot.active{background:#6366f1}
/* Form */
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:.76rem;font-weight:600;color:#475569;margin-bottom:4px}
.form-group input,.form-group select,.form-group textarea{display:block;width:100%;height:38px;padding:0 11px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.855rem;font-family:inherit;color:#1e293b;background:#fff;outline:none;transition:.15s;appearance:none}
.form-group input:focus,.form-group select:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.form-group textarea{height:auto;min-height:70px;padding:9px 11px;resize:vertical}
.form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.btn-row{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:18px}
/* Customer lookup */
.cust-lookup{display:flex;gap:8px;margin-bottom:14px;padding:12px;background:#f0f4ff;border-radius:9px;border:1px solid #c7d2fe}
.cust-lookup input{flex:1;height:36px;padding:0 10px;border:1.5px solid #c7d2fe;border-radius:7px;font-family:inherit;font-size:.82rem;outline:none}
.cust-lookup input:focus{border-color:#6366f1}
.cust-found-banner{padding:10px 12px;background:#d1fae5;border:1px solid #6ee7b7;border-radius:8px;font-size:.8rem;color:#065f46;display:none;align-items:center;gap:8px;margin-bottom:12px}
/* Customers table */
.cust-table{width:100%;border-collapse:collapse;font-size:.8rem;margin-top:12px}
.cust-table th{text-align:left;padding:7px 10px;color:#94a3b8;font-weight:600;border-bottom:1px solid #e2e8f0;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em}
.cust-table td{padding:8px 10px;color:#1e293b;border-bottom:1px solid #f1f5f9;vertical-align:middle}
.cust-table tr:last-child td{border-bottom:none}
.cust-table tr:hover td{background:#f8fafc}
.action-row{display:flex;gap:5px}
/* Result card */
.result-actions{display:flex;flex-direction:column;gap:10px;margin-top:6px}
@media(max-width:600px){.form-row-2{grid-template-columns:1fr}}
</style>
</head>
<body>
<div id="toast-container"></div>
<div class="quick-wrap">

  <!-- Header -->
  <div class="quick-header">
    <a href="index.php" class="quick-logo">
      <div class="logo-icon"><i class="fas fa-file-alt"></i></div>
      Resume<span>Forge</span>
    </a>
    <div class="quick-sub">Quick Biodata Mode — No Login Required</div>
  </div>

  <!-- Flash -->
  <?php if ($_GET['loaded'] ?? false): ?>
  <div class="alert alert-success" style="max-width:560px;width:100%;margin-bottom:14px">
    <i class="fas fa-check-circle"></i> Previous customer data loaded. Review and update below.
  </div>
  <?php endif; ?>

  <div class="quick-card">
    <?php
    // ── STEP 1 ──────────────────────────────────────────────
    if ($step === 1):
    ?>
    <h2>Personal Details</h2>
    <div class="sub">Fill in the basic information for the biodata</div>
    <div class="step-dots">
      <div class="step-dot active"></div>
      <div class="step-dot"></div>
      <div class="step-dot"></div>
      <div class="step-dot"></div>
    </div>

    <!-- Customer lookup -->
    <div class="cust-lookup">
      <i class="fas fa-search" style="color:#6366f1;align-self:center;font-size:.9rem"></i>
      <input type="tel" id="lookup-phone" oninput="lookupCustomer(this.value)">
      <button class="btn btn-ghost btn-sm" onclick="clearLookup()"><i class="fas fa-times"></i></button>
    </div>
    <div class="cust-found-banner" id="found-banner">
      <i class="fas fa-user-check"></i>
      <div>
        <strong id="found-name"></strong> found
        (<span id="found-count"></span> visit<span id="found-count-s"></span>)
      </div>
      <form method="POST" style="margin-left:auto">
        <?= CSRF::field() ?>
        <input type="hidden" name="action" value="load_customer">
        <input type="hidden" name="last_doc_id" id="found-doc-id" value="">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-download"></i> Load Data</button>
      </form>
    </div>

    <div class="form-row-2">
      <div class="form-group">
        <label>Full Name <span style="color:#ef4444">*</span></label>
        <input type="text" id="full_name" value="<?= Helpers::e($d['basic']['full_name']) ?>" required>
      </div>
      <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" id="dob" value="<?= Helpers::e($d['personal']['dob']) ?>">
      </div>
    </div>
    <div class="form-row-2">
      <div class="form-group">
        <label>Gender</label>
        <select id="gender">
          <option value="">Select</option>
          <?php foreach(['Male','Female','Other'] as $g): ?>
          <option <?= $d['personal']['gender']===$g?'selected':'' ?>><?= $g ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Marital Status</label>
        <select id="marital_status">
          <option value="">Select</option>
          <?php foreach(['Single','Married','Divorced','Widowed'] as $ms): ?>
          <option <?= $d['personal']['marital_status']===$ms?'selected':'' ?>><?= $ms ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-row-2">
      <div class="form-group">
        <label>Phone <span style="color:#ef4444">*</span></label>
        <input type="tel" id="phone" value="<?= Helpers::e($d['basic']['phone']) ?>">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" id="email" value="<?= Helpers::e($d['basic']['email']) ?>">
      </div>
    </div>
    <div class="form-group">
      <label>Address</label>
      <textarea id="address"><?= Helpers::e($d['basic']['address']) ?></textarea>
    </div>
    <div class="form-row-2">
      <div class="form-group">
        <label>Father's Name</label>
        <input type="text" id="father_name" value="<?= Helpers::e($d['personal']['father_name']) ?>">
      </div>
      <div class="form-group">
        <label>Mother's Name</label>
        <input type="text" id="mother_name" value="<?= Helpers::e($d['personal']['mother_name']) ?>">
      </div>
    </div>
    <div class="form-row-2">
      <div class="form-group">
        <label>Religion</label>
        <input type="text" id="religion" value="<?= Helpers::e($d['personal']['religion']) ?>">
      </div>
      <div class="form-group">
        <label>Caste</label>
        <input type="text" id="caste" value="<?= Helpers::e($d['personal']['caste']) ?>">
      </div>
    </div>
    <div class="btn-row">
      <a href="index.php" class="btn btn-ghost btn-sm"><i class="fas fa-sign-in-alt"></i> Login</a>
      <button type="button" class="btn btn-primary" onclick="quickSave(2)">
        Next <i class="fas fa-arrow-right"></i>
      </button>
    </div>

    <?php
    // ── STEP 2 ──────────────────────────────────────────────
    elseif ($step === 2):
    ?>
    <h2>Education &amp; Skills</h2>
    <div class="sub">Add your qualifications and skills</div>
    <div class="step-dots">
      <div class="step-dot done"></div>
      <div class="step-dot active"></div>
      <div class="step-dot"></div>
      <div class="step-dot"></div>
    </div>
    <div class="form-group">
      <label>Highest Qualification</label>
      <input type="text" id="edu_degree" value="<?= Helpers::e($d['education'][0]['degree'] ?? '') ?>">
    </div>
    <div class="form-row-2">
      <div class="form-group">
        <label>Institution / College</label>
        <input type="text" id="edu_inst" value="<?= Helpers::e($d['education'][0]['institution'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Year Passed</label>
        <input type="text" id="edu_year" value="<?= Helpers::e($d['education'][0]['year_to'] ?? '') ?>">
      </div>
    </div>
    <div class="form-row-2">
      <div class="form-group">
        <label>Grade / Percentage</label>
        <input type="text" id="edu_grade" value="<?= Helpers::e($d['education'][0]['grade'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Occupation</label>
        <input type="text" id="occupation" value="<?= Helpers::e($d['personal']['occupation'] ?? '') ?>">
      </div>
    </div>
    <div class="form-group">
      <label>Skills / Technical Knowledge</label>
      <input type="text" id="skills" value="<?= Helpers::e(implode(', ', array_filter(array_column($d['skills'], 'name')))) ?>">
    </div>
    <div class="form-row-2">
      <div class="form-group">
        <label>Languages Known</label>
        <input type="text" id="languages" value="<?= Helpers::e($d['personal']['languages']) ?>">
      </div>
      <div class="form-group">
        <label>Hobbies / Interests</label>
        <input type="text" id="hobbies" value="<?= Helpers::e($d['personal']['hobbies']) ?>">
      </div>
    </div>
    <div class="btn-row">
      <a href="quick.php?step=1" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
      <button type="button" class="btn btn-primary" onclick="quickSave(3)">
        Next <i class="fas fa-arrow-right"></i>
      </button>
    </div>

    <?php
    // ── STEP 3 ──────────────────────────────────────────────
    elseif ($step === 3):
    ?>
    <h2>Save Customer <span style="font-size:.75rem;color:#94a3b8;font-weight:400">(Optional)</span></h2>
    <div class="sub">For print shops — saves this customer for quick reprint next time</div>
    <div class="step-dots">
      <div class="step-dot done"></div>
      <div class="step-dot done"></div>
      <div class="step-dot active"></div>
      <div class="step-dot"></div>
    </div>
    <form method="POST">
      <?= CSRF::field() ?>
      <input type="hidden" name="action" value="save_customer">
      <div class="form-row-2">
        <div class="form-group">
          <label>Customer Name</label>
          <input type="text" name="cust_name" value="<?= Helpers::e($d['basic']['full_name']) ?>">
        </div>
        <div class="form-group">
          <label>Phone (for lookup next time)</label>
          <input type="tel" name="cust_phone" value="<?= Helpers::e($d['basic']['phone']) ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Email <span style="font-weight:400;color:#94a3b8">(optional)</span></label>
        <input type="email" name="cust_email" value="<?= Helpers::e($d['basic']['email']) ?>">
      </div>
      <div class="btn-row">
        <a href="quick.php?step=4" class="btn btn-ghost btn-sm">Skip</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save &amp; Continue</button>
      </div>
    </form>

    <?php
    // ── STEP 4 ──────────────────────────────────────────────
    elseif ($step === 4):
    ?>
    <h2>Ready to Print! <i class="fas fa-check-circle" style="color:#10b981;font-size:1rem"></i></h2>
    <div class="sub">Your biodata is ready — download or print it now</div>
    <div class="step-dots">
      <div class="step-dot done"></div>
      <div class="step-dot done"></div>
      <div class="step-dot done"></div>
      <div class="step-dot active"></div>
    </div>
    <?php if ($_GET['saved'] ?? false): ?>
    <div class="alert alert-success" style="margin-bottom:14px"><i class="fas fa-user-check"></i> Customer saved for future reprints.</div>
    <?php endif; ?>
    <div class="result-actions">
      <a href="api/pdf.php?id=<?= $docId ?>" target="_blank" class="btn btn-primary btn-lg" style="justify-content:center">
        <i class="fas fa-file-pdf"></i> Download PDF
      </a>
      <button onclick="window.open('api/pdf.php?id=<?= $docId ?>&print=1','_blank')" class="btn btn-ghost" style="justify-content:center">
        <i class="fas fa-print"></i> Print Directly
      </button>
      <div style="display:flex;gap:10px">
        <a href="quick.php?step=1" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center"><i class="fas fa-edit"></i> Edit</a>
        <a href="quick.php?action=new" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center"><i class="fas fa-plus"></i> New Biodata</a>
        <a href="index.php" class="btn btn-soft-primary btn-sm" style="flex:1;justify-content:center"><i class="fas fa-heart"></i> Full Editor</a>
      </div>
    </div>

    <?php if (!empty($customers)): ?>
    <!-- Recent customers table -->
    <div style="margin-top:24px;padding-top:18px;border-top:1px solid #e2e8f0">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
        <div style="font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em">
          <i class="fas fa-users" style="color:#6366f1;margin-right:5px"></i> Recent Customers
        </div>
        <input type="text" id="cust-search" oninput="filterCustomers(this.value)"
          style="height:30px;padding:0 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:.76rem;width:140px;outline:none">
      </div>
      <table class="cust-table" id="cust-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Phone</th>
            <th style="text-align:center">Visits</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($customers as $c): ?>
          <tr data-name="<?= Helpers::e(strtolower($c['name'])) ?>" data-phone="<?= Helpers::e($c['phone']) ?>">
            <td style="font-weight:600"><?= Helpers::e($c['name']) ?></td>
            <td style="color:#64748b"><?= Helpers::e($c['phone']) ?></td>
            <td style="text-align:center">
              <span class="badge badge-resume"><?= $c['doc_count'] ?></span>
            </td>
            <td>
              <div class="action-row">
                <?php if ($c['last_doc_id']): ?>
                <a href="api/pdf.php?id=<?= $c['last_doc_id'] ?>" target="_blank" class="btn btn-ghost btn-xs" title="Reprint last"><i class="fas fa-print"></i> Reprint</a>
                <form method="POST" style="display:inline">
                  <?= CSRF::field() ?>
                  <input type="hidden" name="action" value="load_customer">
                  <input type="hidden" name="last_doc_id" value="<?= $c['last_doc_id'] ?>">
                  <button type="submit" class="btn btn-soft-primary btn-xs" title="Load & edit"><i class="fas fa-edit"></i> Edit</button>
                </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

    <?php endif; ?>
  </div><!-- /quick-card -->

  <div style="text-align:center;margin-top:16px;font-size:.76rem;color:#94a3b8">
    <a href="index.php" style="color:#6366f1">Sign in</a> for full features — dashboard, share links, QR codes and more.
  </div>
</div><!-- /quick-wrap -->

<script>
const DOC_ID     = <?= $docId ?>;
const CSRF_TOKEN = '<?= $csrf ?>';
const APP_URL    = '<?= APP_URL ?>';

// ── Step save ─────────────────────────────────────────────
async function quickSave(nextStep) {
  const btn = event?.target;
  if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…'; }

  const step = <?= $step ?>;
  let data   = {};

  if (step === 1) {
    const name = document.getElementById('full_name')?.value?.trim();
    if (!name) {
      document.getElementById('full_name')?.focus();
      if (btn) { btn.disabled = false; btn.innerHTML = 'Next <i class="fas fa-arrow-right"></i>'; }
      return;
    }
    data = {
      basic: {
        full_name: name,
        phone:     document.getElementById('phone')?.value   || '',
        email:     document.getElementById('email')?.value   || '',
        address:   document.getElementById('address')?.value || '',
      },
      personal: {
        dob:            document.getElementById('dob')?.value           || '',
        gender:         document.getElementById('gender')?.value        || '',
        marital_status: document.getElementById('marital_status')?.value|| '',
        father_name:    document.getElementById('father_name')?.value   || '',
        mother_name:    document.getElementById('mother_name')?.value   || '',
        religion:       document.getElementById('religion')?.value      || '',
        caste:          document.getElementById('caste')?.value         || '',
      },
    };
  } else if (step === 2) {
    const skillsRaw = (document.getElementById('skills')?.value || '')
      .split(',').map(s => s.trim()).filter(Boolean);
    data = {
      education: [{
        degree:      document.getElementById('edu_degree')?.value || '',
        institution: document.getElementById('edu_inst')?.value   || '',
        year_to:     document.getElementById('edu_year')?.value   || '',
        grade:       document.getElementById('edu_grade')?.value  || '',
      }],
      skills:   skillsRaw.map(s => ({ name: s, level: 80 })),
      personal: {
        occupation: document.getElementById('occupation')?.value || '',
        languages:  document.getElementById('languages')?.value  || '',
        hobbies:    document.getElementById('hobbies')?.value    || '',
      },
    };
  }

  try {
    await fetch('api/autosave.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF_TOKEN },
      body:    JSON.stringify({ doc_id: DOC_ID, data, doc_type: 'biodata' }),
    });
  } catch(e) {}
  window.location.href = 'quick.php?step=' + nextStep;
}

// ── Customer lookup ────────────────────────────────────────
let lookupTimer;
async function lookupCustomer(phone) {
  clearTimeout(lookupTimer);
  const cleaned = phone.replace(/[^0-9+]/g,'');
  if (cleaned.length < 7) { hideBanner(); return; }
  lookupTimer = setTimeout(async () => {
    try {
      const res  = await fetch(`quick.php?action=lookup_customer&phone=${encodeURIComponent(cleaned)}`);
      const data = await res.json();
      if (data.found) showBanner(data);
      else hideBanner();
    } catch { hideBanner(); }
  }, 600);
}
function showBanner(data) {
  document.getElementById('found-banner').style.display = 'flex';
  document.getElementById('found-name').textContent   = data.name;
  document.getElementById('found-count').textContent  = data.doc_count;
  document.getElementById('found-count-s').textContent= data.doc_count !== 1 ? 's' : '';
  document.getElementById('found-doc-id').value       = data.last_doc_id;
  // Prefill form fields from loaded data
  const b = data.data?.basic || {};
  const p = data.data?.personal || {};
  if (b.full_name) setValue('full_name', b.full_name);
  if (b.phone)     setValue('phone', b.phone);
  if (b.email)     setValue('email', b.email);
  if (b.address)   setValue('address', b.address);
  if (p.father_name) setValue('father_name', p.father_name);
  if (p.mother_name) setValue('mother_name', p.mother_name);
  if (p.religion)    setValue('religion', p.religion);
  if (p.caste)       setValue('caste', p.caste);
  if (p.dob)         setValue('dob', p.dob);
  if (p.gender)      setValue('gender', p.gender);
  if (p.marital_status) setValue('marital_status', p.marital_status);
}
function hideBanner() {
  document.getElementById('found-banner').style.display = 'none';
}
function clearLookup() {
  document.getElementById('lookup-phone').value = '';
  hideBanner();
}
function setValue(id, val) {
  const el = document.getElementById(id);
  if (el) el.value = val;
}

// ── Customer table search ──────────────────────────────────
function filterCustomers(q) {
  const lq = q.toLowerCase();
  document.querySelectorAll('#cust-table tbody tr').forEach(row => {
    const name  = row.dataset.name  || '';
    const phone = row.dataset.phone || '';
    row.style.display = (!lq || name.includes(lq) || phone.includes(lq)) ? '' : 'none';
  });
}
</script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>

