<?php
require_once __DIR__ . '/includes/boot.php';
Auth::requireLogin();
$user = Auth::user();

$action = $_GET['action'] ?? '';
$docId  = (int)($_GET['id'] ?? 0);
$initTab = $_GET['tab'] ?? 'step1';

// Create new document
if ($action === 'new') {
    $type = in_array($_GET['type'] ?? '', ['resume','cv','biodata']) ? $_GET['type'] : 'resume';
    $docId = Document::create($user['id'], $type, 'Untitled ' . ucfirst($type));
    Helpers::redirect(APP_URL . '/wizard.php?id=' . $docId);
}

// Load existing
$doc = Document::getForUser($docId, $user['id']);
if (!$doc) {
    Helpers::redirect(APP_URL . '/dashboard.php');
}

$d    = $doc['data'];
$st   = $d['styling'];
$csrf = CSRF::token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit — <?= Helpers::e($doc['title']) ?> | <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/wizard.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/components.css">
<style>
/* Wizard page-specific extras — all general styles now in wizard.css */
/* Visually-hidden label utility */
.sr-only {
  position: absolute; width: 1px; height: 1px;
  overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap;
}
</style>
</head>
<body class="wizard-body">

<!-- Top bar -->
<header class="wizard-topbar">
  <a href="dashboard.php" class="topbar-logo">
    <div class="logo-dot"><i class="fas fa-file-alt"></i></div>
    <span>Resume<b style="color:#fff;font-weight:800">Forge</b></span>
    <span class="topbar-sep"></span>
    <span>Editor</span>
  </a>
  <div class="topbar-title">
    <input type="text" id="docTitle" value="<?= Helpers::e($doc['title']) ?>" class="title-input" maxlength="200">
    <span class="save-indicator saved" id="saveIndicator"><span class="dot"></span> <span id="saveText">Saved</span></span>
  </div>
  <div class="topbar-actions">
    <label for="docStatus" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0)">Document status</label>
    <select id="docStatus" onchange="updateMeta({status: this.value})" title="Document status">
      <option value="draft" <?= $doc['status']==='draft'?'selected':'' ?>>Draft</option>
      <option value="final" <?= $doc['status']==='final'?'selected':'' ?>>Final</option>
    </select>
    <button id="previewToggleBtn" onclick="togglePreview()" class="btn btn-sm btn-ghost"><i class="fas fa-eye-slash"></i> Preview</button>
    <a href="api/pdf.php?id=<?= $docId ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-file-pdf"></i> PDF</a>
    <button onclick="printPreview()" class="btn btn-sm btn-ghost"><i class="fas fa-print"></i> Print</button>
    <a href="dashboard.php" class="btn btn-sm btn-ghost"><i class="fas fa-arrow-left"></i> Back</a>
  </div>
</header>

<div class="wizard-layout">
  <!-- LEFT: Form Panel -->
  <div class="wizard-form-panel">
    <!-- Step tabs — labels update dynamically per doc type -->
    <div class="step-tabs">
      <button class="step-tab active" data-step="1" onclick="goStep(1)"><span class="step-num">1</span> <span class="step-lbl">Type</span></button>
      <button class="step-tab" data-step="2" onclick="goStep(2)"
        data-lbl-resume="Basic Info" data-lbl-cv="Basic Info" data-lbl-biodata="Contact Info"><span class="step-num">2</span> <span class="step-lbl">Basic Info</span></button>
      <button class="step-tab" data-step="3" onclick="goStep(3)"
        data-lbl-resume="Education" data-lbl-cv="Education" data-lbl-biodata="Education"><span class="step-num">3</span> <span class="step-lbl">Education</span></button>
      <button class="step-tab" data-step="4" onclick="goStep(4)"
        data-lbl-resume="Experience" data-lbl-cv="Experience" data-lbl-biodata="Personal Details"><span class="step-num">4</span> <span class="step-lbl">Experience</span></button>
      <button class="step-tab" data-step="5" onclick="goStep(5)"
        data-lbl-resume="Skills" data-lbl-cv="Skills &amp; Personal" data-lbl-biodata="Family Details"><span class="step-num">5</span> <span class="step-lbl">Skills</span></button>
      <button class="step-tab" data-step="6" onclick="goStep(6)"
        data-lbl-resume="Extras" data-lbl-cv="Research &amp; More" data-lbl-biodata="Contact &amp; More"><span class="step-num">6</span> <span class="step-lbl">Extras</span></button>
      <button class="step-tab" data-step="7" onclick="goStep(7)"><span class="step-num">7</span> <span class="step-lbl">Style</span></button>
      <button class="step-tab" data-step="8" onclick="goStep(8)"><span class="step-num">8</span> <span class="step-lbl">Share</span></button>
    </div>

    <!-- Progress bar -->
    <div class="progress-wrap"><div class="progress-bar" id="progressBar" style="width:12.5%"></div></div>

    <div class="step-content">

      <!-- ═══ STEP 1: Document Type ═══ -->
      <div class="step-pane active" id="step-1">
        <div class="step-header"><h2>Document Type</h2><p>Choose the type of document you want to create</p></div>
        <div class="doc-type-grid">
          <label class="type-card <?= $doc['doc_type']==='resume'?'active':'' ?>">
            <input type="radio" name="doc_type" value="resume" <?= $doc['doc_type']==='resume'?'checked':'' ?> onchange="applyDocTypeFilter('resume');updateMeta({doc_type:'resume'})">
            <div class="type-card-icon"><i class="fas fa-briefcase"></i></div>
            <div class="type-card-text">
              <strong>Resume</strong>
              <span>1–2 pages · Skills & experience · Corporate jobs</span>
            </div>
            <div class="type-card-check"><i class="fas fa-check"></i></div>
          </label>
          <label class="type-card <?= $doc['doc_type']==='cv'?'active':'' ?>">
            <input type="radio" name="doc_type" value="cv" <?= $doc['doc_type']==='cv'?'checked':'' ?> onchange="applyDocTypeFilter('cv');updateMeta({doc_type:'cv'})">
            <div class="type-card-icon"><i class="fas fa-scroll"></i></div>
            <div class="type-card-text">
              <strong>CV (Curriculum Vitae)</strong>
              <span>Detailed academic & professional · No page limit</span>
            </div>
            <div class="type-card-check"><i class="fas fa-check"></i></div>
          </label>
          <label class="type-card <?= $doc['doc_type']==='biodata'?'active':'' ?>">
            <input type="radio" name="doc_type" value="biodata" <?= $doc['doc_type']==='biodata'?'checked':'' ?> onchange="applyDocTypeFilter('biodata');updateMeta({doc_type:'biodata'})">
            <div class="type-card-icon"><i class="fas fa-id-card"></i></div>
            <div class="type-card-text">
              <strong>Biodata</strong>
              <span>Personal details · Marriage / Govt applications</span>
            </div>
            <div class="type-card-check"><i class="fas fa-check"></i></div>
          </label>
        </div>
      </div>

      <!-- ═══ STEP 2: Basic Info ═══ -->
      <div class="step-pane" id="step-2">
        <div class="step-header"><h2>Basic Information</h2><p>Your contact and personal details</p></div>

        <div class="photo-upload-area" id="photoUploadArea">
          <?php if ($d['basic']['photo']): ?>
            <img id="photoPreview" src="<?= UPLOAD_URL . Helpers::e($d['basic']['photo']) ?>" alt="Photo">
          <?php else: ?>
            <div id="photoPlaceholder"><i class="fas fa-user-circle"></i><span>Upload Photo</span></div>
            <img id="photoPreview" style="display:none" alt="Photo">
          <?php endif; ?>
          <input type="hidden" data-field="basic.photo" value="<?= Helpers::e($d['basic']['photo']) ?>">
          <input type="file" id="photoInput" accept="image/jpeg,image/png,image/webp" style="display:none" onchange="uploadPhoto(this)">
          <div class="photo-actions">
            <button type="button" onclick="document.getElementById('photoInput').click()" class="btn btn-sm btn-ghost">
              <i class="fas fa-upload"></i> Upload Photo
            </button>
            <button type="button" onclick="removePhoto()" class="btn btn-sm btn-ghost" id="removePhotoBtn" style="<?= $d['basic']['photo']?'':'display:none' ?>">
              <i class="fas fa-trash"></i> Remove
            </button>
          </div>
          <p class="hint">JPG, PNG or WebP · Max 2 MB</p>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="basic_full_name">Full Name <span class="req">*</span></label>
            <input id="basic_full_name" type="text" data-field="basic.full_name"
              value="<?= Helpers::e($d['basic']['full_name']) ?>"
              autocomplete="name" required>
          </div>
          <div class="form-group">
            <label for="basic_email">Email <span class="req">*</span></label>
            <input id="basic_email" type="email" data-field="basic.email"
              value="<?= Helpers::e($d['basic']['email']) ?>"
              autocomplete="email" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="basic_phone">Phone <span class="req">*</span></label>
            <input id="basic_phone" type="tel" data-field="basic.phone"
              value="<?= Helpers::e($d['basic']['phone']) ?>"
              autocomplete="tel" required>
          </div>
          <div class="form-group">
            <label for="basic_city">City</label>
            <input id="basic_city" type="text" data-field="basic.city"
              value="<?= Helpers::e($d['basic']['city']) ?>"
              autocomplete="address-level2">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="basic_state">State</label>
            <input id="basic_state" type="text" data-field="basic.state"
              value="<?= Helpers::e($d['basic']['state']) ?>"
              autocomplete="address-level1">
          </div>
          <div class="form-group">
            <label for="basic_country">Country</label>
            <input id="basic_country" type="text" data-field="basic.country"
              value="<?= Helpers::e($d['basic']['country']) ?>"
              autocomplete="country-name">
          </div>
        </div>
        <div class="form-group">
          <label>Address</label>
          <input type="text" data-field="basic.address" value="<?= Helpers::e($d['basic']['address']) ?>">
        </div>
        <!-- Resume & CV only -->
        <div data-show-for="resume,cv">
          <div class="form-row">
            <div class="form-group">
              <label><i class="fab fa-linkedin"></i> LinkedIn</label>
              <input type="url" data-field="basic.linkedin" value="<?= Helpers::e($d['basic']['linkedin']) ?>">
            </div>
            <div class="form-group">
              <label><i class="fab fa-github"></i> GitHub</label>
              <input type="url" data-field="basic.github" value="<?= Helpers::e($d['basic']['github']) ?>">
            </div>
          </div>
          <div class="form-group">
            <label>Website / Portfolio</label>
            <input type="url" data-field="basic.website" value="<?= Helpers::e($d['basic']['website']) ?>">
          </div>
          <div class="form-group">
            <label>Professional Summary / Objective</label>
            <div class="field-wrap">
            <textarea id="basic_objective" data-field="basic.objective"
              rows="4" maxlength="600" data-maxlen="600"><?= Helpers::e($d['basic']['objective']) ?></textarea>
          </div>
          </div>
        </div>

      </div>

      <!-- ═══ STEP 3: Education ═══ -->
      <div class="step-pane" id="step-3">
        <div class="step-header">
          <h2>Education</h2><p>Add your educational qualifications</p>
          <button type="button" class="btn btn-sm btn-primary" onclick="addEntry('education')"><i class="fas fa-plus"></i> Add Education</button>
        </div>
        <div id="education-list" class="dynamic-list">
          <?php foreach ($d['education'] as $i => $edu): ?>
          <div class="dynamic-entry" data-index="<?= $i ?>">
            <div class="entry-header">
              <span class="entry-title"><?= Helpers::e($edu['degree'] ?: 'Education ' . ($i+1)) ?></span>
              <div>
                <button type="button" class="btn-icon" onclick="toggleEntry(this)"><i class="fas fa-chevron-up"></i></button>
                <button type="button" class="btn-icon danger" onclick="removeEntry('education', <?= $i ?>)"><i class="fas fa-trash"></i></button>
              </div>
            </div>
            <div class="entry-body">
              <div class="form-row">
                <div class="form-group"><label>Degree / Qualification <span class="req">*</span></label>
                  <input type="text" data-field="education[<?=$i?>].degree" value="<?= Helpers::e($edu['degree']) ?>"></div>
                <div class="form-group"><label>Institution <span class="req">*</span></label>
                  <input type="text" data-field="education[<?=$i?>].institution" value="<?= Helpers::e($edu['institution']) ?>"></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label>University / Board</label>
                  <input type="text" data-field="education[<?=$i?>].university" value="<?= Helpers::e($edu['university']) ?>"></div>
                <div class="form-group"><label>Grade / CGPA</label>
                  <input type="text" data-field="education[<?=$i?>].grade" value="<?= Helpers::e($edu['grade']) ?>"></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label>Year From</label>
                  <input type="text" data-field="education[<?=$i?>].year_from" value="<?= Helpers::e($edu['year_from']) ?>"></div>
                <div class="form-group"><label>Year To</label>
                  <input type="text" data-field="education[<?=$i?>].year_to" value="<?= Helpers::e($edu['year_to']) ?>"></div>
              </div>
              <div class="form-group"><label>Notes</label>
                <textarea data-field="education[<?=$i?>].description" rows="2"><?= Helpers::e($edu['description']) ?></textarea></div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($d['education'])): ?><div class="empty-list-hint"><i class="fas fa-graduation-cap"></i> No education added yet. Click "Add Education" above.</div><?php endif; ?>
        </div>
      </div>

      <!-- ═══ STEP 4: Experience (Resume/CV) | Personal Details (Biodata) ═══ -->
      <div class="step-pane" id="step-4">

        <!-- ── Resume / CV: Work Experience ── -->
        <div data-show-for="resume,cv">
        <div class="step-header">
          <h2>Work Experience</h2><p>Add your professional experience</p>
          <button type="button" class="btn btn-sm btn-primary" onclick="addEntry('experience')"><i class="fas fa-plus"></i> Add Experience</button>
        </div>
        <div id="experience-list" class="dynamic-list">
          <?php foreach ($d['experience'] as $i => $exp): ?>
          <div class="dynamic-entry" data-index="<?= $i ?>">
            <div class="entry-header">
              <span class="entry-title"><?= Helpers::e($exp['job_title'] ?: 'Experience ' . ($i+1)) ?></span>
              <div>
                <button type="button" class="btn-icon" onclick="toggleEntry(this)"><i class="fas fa-chevron-up"></i></button>
                <button type="button" class="btn-icon danger" onclick="removeEntry('experience', <?= $i ?>)"><i class="fas fa-trash"></i></button>
              </div>
            </div>
            <div class="entry-body">
              <div class="form-row">
                <div class="form-group"><label>Job Title <span class="req">*</span></label>
                  <input type="text" data-field="experience[<?=$i?>].job_title" value="<?= Helpers::e($exp['job_title']) ?>"></div>
                <div class="form-group"><label>Company <span class="req">*</span></label>
                  <input type="text" data-field="experience[<?=$i?>].company" value="<?= Helpers::e($exp['company']) ?>"></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label>Location</label>
                  <input type="text" data-field="experience[<?=$i?>].location" value="<?= Helpers::e($exp['location']) ?>"></div>
                <div class="form-group"><label>Employment Type</label>
                  <select data-field="experience[<?=$i?>].employment_type">
                    <?php foreach(['Full-time','Part-time','Contract','Internship','Freelance'] as $et): ?>
                    <option <?= ($exp['employment_type']??'Full-time')===$et?'selected':'' ?>><?= $et ?></option>
                    <?php endforeach; ?>
                  </select></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label>From</label>
                  <input type="month" data-field="experience[<?=$i?>].date_from" value="<?= Helpers::e($exp['date_from']) ?>"></div>
                <div class="form-group">
                  <label for="exp-date-to-<?=$i?>">To
                    <span style="font-weight:400;font-size:.76rem;margin-left:6px">
                      <input type="checkbox" data-field="experience[<?=$i?>].is_current"
                        <?= !empty($exp['is_current'])?'checked':'' ?>
                        onchange="toggleCurrentJob(this, <?=$i?>)"
                        id="exp-current-<?=$i?>">
                      <label for="exp-current-<?=$i?>" style="font-weight:400">Currently working</label>
                    </span>
                  </label>
                  <input type="month" data-field="experience[<?=$i?>].date_to"
                    value="<?= Helpers::e($exp['date_to']) ?>"
                    id="exp-date-to-<?=$i?>" <?= !empty($exp['is_current'])?'disabled':'' ?>>
                </div>
              </div>
              <div class="form-group"><label>Description</label>
                <textarea data-field="experience[<?=$i?>].description" rows="3"><?= Helpers::e($exp['description']) ?></textarea></div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($d['experience'])): ?><div class="empty-list-hint"><i class="fas fa-briefcase"></i> No experience added yet.</div><?php endif; ?>
        </div>
        </div><!-- /resume,cv -->

        <!-- ── Biodata: Personal & Astrology Details ── -->
        <div data-show-for="biodata">
        <div class="step-header"><h2>Personal &amp; Birth Details</h2><p>Personal information for the biodata</p></div>
        <div class="form-row">
          <div class="form-group"><label>Date of Birth</label>
            <input type="date" data-field="personal.dob" value="<?= Helpers::e($d['personal']['dob']) ?>"></div>
          <div class="form-group"><label>Birth Time</label>
            <input type="text" data-field="personal.birth_time" value="<?= Helpers::e($d['personal']['birth_time'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Birth Place</label>
            <input type="text" data-field="personal.birth_place" value="<?= Helpers::e($d['personal']['birth_place'] ?? '') ?>"></div>
          <div class="form-group"><label>Nakshtra Pad</label>
            <input type="text" data-field="personal.nakshtra_pad" value="<?= Helpers::e($d['personal']['nakshtra_pad'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Rashi (Zodiac)</label>
            <input type="text" data-field="personal.rashi" value="<?= Helpers::e($d['personal']['rashi'] ?? '') ?>"></div>
          <div class="form-group"><label>Nakshtra</label>
            <input type="text" data-field="personal.nakshtra" value="<?= Helpers::e($d['personal']['nakshtra'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Gotra</label>
            <input type="text" data-field="personal.gotra" value="<?= Helpers::e($d['personal']['gotra'] ?? '') ?>"></div>
          <div class="form-group"><label>Manglik</label>
            <select data-field="personal.manglik">
              <option value="">Select</option>
              <?php foreach(['No','Yes','Anshik Manglik'] as $m): ?><option <?= ($d['personal']['manglik']??'')===$m?'selected':'' ?>><?= $m ?></option><?php endforeach; ?>
            </select></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Height</label>
            <input type="text" data-field="personal.height" value="<?= Helpers::e($d['personal']['height']) ?>"></div>
          <div class="form-group"><label>Weight</label>
            <input type="text" data-field="personal.weight" value="<?= Helpers::e($d['personal']['weight']) ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Religion</label>
            <input type="text" data-field="personal.religion" value="<?= Helpers::e($d['personal']['religion']) ?>"></div>
          <div class="form-group"><label>Caste</label>
            <input type="text" data-field="personal.caste" value="<?= Helpers::e($d['personal']['caste']) ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Occupation / Profession</label>
            <input type="text" data-field="personal.occupation" value="<?= Helpers::e($d['personal']['occupation'] ?? '') ?>"></div>
          <div class="form-group"><label>Annual Income</label>
            <input type="text" data-field="personal.annual_income" value="<?= Helpers::e($d['personal']['annual_income'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Land / Property</label>
            <input type="text" data-field="personal.land" value="<?= Helpers::e($d['personal']['land'] ?? '') ?>"></div>
          <div class="form-group"><label>Marital Status</label>
            <select data-field="personal.marital_status">
              <option value="">Select</option>
              <?php foreach(['Single','Married','Divorced','Widowed'] as $ms): ?><option <?= $d['personal']['marital_status']===$ms?'selected':'' ?>><?= $ms ?></option><?php endforeach; ?>
            </select></div>
        </div>
        <div class="form-group"><label>Languages Known</label>
          <input type="text" data-field="personal.languages" value="<?= Helpers::e($d['personal']['languages']) ?>"></div>
        <div class="form-group"><label>Hobbies / Interests</label>
          <input type="text" data-field="personal.hobbies" value="<?= Helpers::e($d['personal']['hobbies']) ?>"></div>
        </div><!-- /biodata -->

      </div>

      <!-- ═══ STEP 5: Skills (Resume) | Skills+Personal (CV) | Family Details (Biodata) ═══ -->
      <div class="step-pane" id="step-5">

        <!-- ── Resume: Skills only ── -->
        <div data-show-for="resume">
        <div class="step-header"><h2>Skills &amp; Expertise</h2><p>Add your technical and professional skills</p>
          <button type="button" class="btn btn-sm btn-primary" onclick="addEntry('skills')"><i class="fas fa-plus"></i> Add Skill</button>
        </div>
        <div id="skills-list" class="skills-grid">
          <?php foreach ($d['skills'] as $i => $sk): ?>
          <div class="skill-entry" data-index="<?= $i ?>">
            <input type="text" data-field="skills[<?=$i?>].name" value="<?= Helpers::e($sk['name']) ?>" class="skill-name">
            <div class="skill-level-wrap">
              <input type="range" data-field="skills[<?=$i?>].level" min="10" max="100" step="10" value="<?= (int)($sk['level'] ?? 80) ?>" class="skill-range" oninput="updateSkillLabel(this)">
              <span class="skill-pct"><?= (int)($sk['level'] ?? 80) ?>%</span>
            </div>
            <button type="button" class="btn-icon danger sm" onclick="removeEntry('skills', <?= $i ?>)"><i class="fas fa-times"></i></button>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="section-divider"><span>Personal</span></div>
        <div class="form-row">
          <div class="form-group"><label>Languages Known</label>
            <input type="text" data-field="personal.languages" value="<?= Helpers::e($d['personal']['languages']) ?>"></div>
          <div class="form-group"><label>Hobbies / Interests</label>
            <input type="text" data-field="personal.hobbies" value="<?= Helpers::e($d['personal']['hobbies']) ?>"></div>
        </div>
        </div><!-- /resume -->

        <!-- ── CV: Skills + Full Personal ── -->
        <div data-show-for="cv">
        <div class="step-header"><h2>Skills &amp; Personal Info</h2><p>Skills, qualifications and personal details</p>
          <button type="button" class="btn btn-sm btn-primary" onclick="addEntry('skills')"><i class="fas fa-plus"></i> Add Skill</button>
        </div>
        <div id="skills-list-cv" class="skills-grid">
          <?php foreach ($d['skills'] as $i => $sk): ?>
          <div class="skill-entry" data-index="<?= $i ?>">
            <input type="text" data-field="skills[<?=$i?>].name" value="<?= Helpers::e($sk['name']) ?>" class="skill-name">
            <div class="skill-level-wrap">
              <input type="range" data-field="skills[<?=$i?>].level" min="10" max="100" step="10" value="<?= (int)($sk['level'] ?? 80) ?>" class="skill-range" oninput="updateSkillLabel(this)">
              <span class="skill-pct"><?= (int)($sk['level'] ?? 80) ?>%</span>
            </div>
            <button type="button" class="btn-icon danger sm" onclick="removeEntry('skills', <?= $i ?>)"><i class="fas fa-times"></i></button>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="section-divider"><span>Personal Details</span></div>
        <div class="form-row">
          <div class="form-group"><label>Date of Birth</label>
            <input type="date" data-field="personal.dob" value="<?= Helpers::e($d['personal']['dob']) ?>"></div>
          <div class="form-group"><label>Gender</label>
            <select data-field="personal.gender"><option value="">Select</option>
              <?php foreach(['Male','Female','Other','Prefer not to say'] as $g): ?><option <?= $d['personal']['gender']===$g?'selected':'' ?>><?= $g ?></option><?php endforeach; ?>
            </select></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Nationality</label>
            <input type="text" data-field="personal.nationality" value="<?= Helpers::e($d['personal']['nationality']) ?>"></div>
          <div class="form-group"><label>Marital Status</label>
            <select data-field="personal.marital_status"><option value="">Select</option>
              <?php foreach(['Single','Married','Divorced','Widowed'] as $ms): ?><option <?= $d['personal']['marital_status']===$ms?'selected':'' ?>><?= $ms ?></option><?php endforeach; ?>
            </select></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Languages Known</label>
            <input type="text" data-field="personal.languages" value="<?= Helpers::e($d['personal']['languages']) ?>"></div>
          <div class="form-group"><label>Hobbies / Interests</label>
            <input type="text" data-field="personal.hobbies" value="<?= Helpers::e($d['personal']['hobbies']) ?>"></div>
        </div>
        </div><!-- /cv -->

        <!-- ── Biodata: Family Details ── -->
        <div data-show-for="biodata">
        <div class="step-header"><h2>Family Details</h2><p>Information about your family members</p></div>
        <div class="form-row">
          <div class="form-group"><label>Father's Name <span class="req">*</span></label>
            <input type="text" data-field="personal.father_name" value="<?= Helpers::e($d['personal']['father_name']) ?>"></div>
          <div class="form-group"><label>Father's Occupation</label>
            <input type="text" data-field="personal.father_occupation" value="<?= Helpers::e($d['personal']['father_occupation'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Mother's Name <span class="req">*</span></label>
            <input type="text" data-field="personal.mother_name" value="<?= Helpers::e($d['personal']['mother_name']) ?>"></div>
          <div class="form-group"><label>Mother's Occupation</label>
            <input type="text" data-field="personal.mother_occupation" value="<?= Helpers::e($d['personal']['mother_occupation'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Brothers</label>
            <input type="text" data-field="personal.brothers" value="<?= Helpers::e($d['personal']['brothers'] ?? '') ?>"></div>
          <div class="form-group"><label>Sisters</label>
            <input type="text" data-field="personal.sisters" value="<?= Helpers::e($d['personal']['sisters'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Uncle / Relative Name</label>
            <input type="text" data-field="personal.uncle_name" value="<?= Helpers::e($d['personal']['uncle_name'] ?? '') ?>"></div>
          <div class="form-group"><label>Family Type</label>
            <select data-field="personal.family_type"><option value="">Select</option>
              <?php foreach(['Joint Family','Nuclear Family'] as $ft): ?><option <?= ($d['personal']['family_type']??'')===$ft?'selected':'' ?>><?= $ft ?></option><?php endforeach; ?>
            </select></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Family Status</label>
            <select data-field="personal.family_status"><option value="">Select</option>
              <?php foreach(['Middle Class','Upper Middle Class','Rich','High Class'] as $fs2): ?><option <?= ($d['personal']['family_status']??'')===$fs2?'selected':'' ?>><?= $fs2 ?></option><?php endforeach; ?>
            </select></div>
          <div class="form-group"><label>Nationality</label>
            <input type="text" data-field="personal.nationality" value="<?= Helpers::e($d['personal']['nationality']) ?>"></div>
        </div>
        </div><!-- /biodata -->

      </div>

      <!-- ═══ STEP 6: Extras (Resume/CV) | Contact & More (Biodata) ═══ -->
      <div class="step-pane" id="step-6">

        <!-- ── Resume / CV: Certifications, Projects, Achievements, References ── -->
        <div data-show-for="resume,cv">
        <div class="step-header"><h2>Extras</h2><p>Certifications, projects, achievements &amp; references</p></div>

        <!-- Certifications -->
        <div class="section-divider"><span>Certifications</span>
          <button type="button" class="btn btn-xs btn-ghost" onclick="addEntry('certifications')"><i class="fas fa-plus"></i></button></div>
        <div id="certifications-list" class="dynamic-list compact">
          <?php foreach ($d['extras']['certifications'] as $i => $cert): ?>
          <div class="dynamic-entry compact-entry" data-index="<?= $i ?>">
            <div class="form-row">
              <div class="form-group"><input type="text" data-field="extras.certifications[<?=$i?>].name" value="<?= Helpers::e($cert['name']) ?>"></div>
              <div class="form-group"><input type="text" data-field="extras.certifications[<?=$i?>].issuer" value="<?= Helpers::e($cert['issuer']) ?>"></div>
              <div class="form-group"><input type="text" data-field="extras.certifications[<?=$i?>].date" value="<?= Helpers::e($cert['date']) ?>"></div>
              <button type="button" class="btn-icon danger sm" onclick="removeEntry('certifications', <?=$i?>)"><i class="fas fa-times"></i></button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Projects -->
        <div class="section-divider"><span>Projects</span>
          <button type="button" class="btn btn-xs btn-ghost" onclick="addEntry('projects')"><i class="fas fa-plus"></i></button></div>
        <div id="projects-list" class="dynamic-list compact">
          <?php foreach ($d['extras']['projects'] as $i => $proj): ?>
          <div class="dynamic-entry" data-index="<?= $i ?>">
            <div class="entry-header">
              <span class="entry-title"><?= Helpers::e($proj['name'] ?: 'Project ' . ($i+1)) ?></span>
              <button type="button" class="btn-icon danger" onclick="removeEntry('projects', <?=$i?>)"><i class="fas fa-trash"></i></button>
            </div>
            <div class="entry-body">
              <div class="form-row">
                <div class="form-group"><label>Project Name</label><input type="text" data-field="extras.projects[<?=$i?>].name" value="<?= Helpers::e($proj['name']) ?>"></div>
                <div class="form-group"><label>Role</label><input type="text" data-field="extras.projects[<?=$i?>].role" value="<?= Helpers::e($proj['role']) ?>"></div>
              </div>
              <div class="form-group"><label>Tech Stack</label><input type="text" data-field="extras.projects[<?=$i?>].tech_stack" value="<?= Helpers::e($proj['tech_stack']) ?>"></div>
              <div class="form-group"><label>Description</label><textarea data-field="extras.projects[<?=$i?>].description" rows="2"><?= Helpers::e($proj['description']) ?></textarea></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Achievements -->
        <div class="section-divider"><span>Achievements</span>
          <button type="button" class="btn btn-xs btn-ghost" onclick="addEntry('achievements')"><i class="fas fa-plus"></i></button></div>
        <div id="achievements-list" class="dynamic-list compact">
          <?php foreach ($d['extras']['achievements'] as $i => $ach): ?>
          <div class="dynamic-entry compact-entry" data-index="<?= $i ?>">
            <div class="form-row">
              <div class="form-group"><input type="text" data-field="extras.achievements[<?=$i?>].title" value="<?= Helpers::e($ach['title']) ?>"></div>
              <div class="form-group"><input type="text" data-field="extras.achievements[<?=$i?>].year" value="<?= Helpers::e($ach['year']) ?>" style="max-width:100px"></div>
              <button type="button" class="btn-icon danger sm" onclick="removeEntry('achievements', <?=$i?>)"><i class="fas fa-times"></i></button>
            </div>
            <div class="form-group"><textarea data-field="extras.achievements[<?=$i?>].description" rows="1"><?= Helpers::e($ach['description']) ?></textarea></div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- References -->
        <div class="section-divider"><span>References</span>
          <button type="button" class="btn btn-xs btn-ghost" onclick="addEntry('references')"><i class="fas fa-plus"></i></button></div>
        <div id="references-list" class="dynamic-list compact">
          <?php foreach ($d['extras']['references'] as $i => $ref): ?>
          <div class="dynamic-entry compact-entry" data-index="<?= $i ?>">
            <div class="form-row">
              <div class="form-group"><input type="text" data-field="extras.references[<?=$i?>].name" value="<?= Helpers::e($ref['name']) ?>"></div>
              <div class="form-group"><input type="text" data-field="extras.references[<?=$i?>].designation" value="<?= Helpers::e($ref['designation']) ?>"></div>
            </div>
            <div class="form-row">
              <div class="form-group"><input type="email" data-field="extras.references[<?=$i?>].email" value="<?= Helpers::e($ref['email']) ?>"></div>
              <div class="form-group"><input type="tel" data-field="extras.references[<?=$i?>].phone" value="<?= Helpers::e($ref['phone']) ?>"></div>
              <button type="button" class="btn-icon danger sm" onclick="removeEntry('references', <?=$i?>)"><i class="fas fa-times"></i></button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        </div><!-- /resume,cv -->

        <!-- ── Biodata: Contact Details & More ── -->
        <div data-show-for="biodata">
        <div class="step-header"><h2>Contact &amp; More Details</h2><p>Address, contact info and additional information</p></div>
        <div class="form-row">
          <div class="form-group"><label>Contact Person Name</label>
            <input type="text" data-field="personal.contact_person" value="<?= Helpers::e($d['personal']['contact_person'] ?? '') ?>"></div>
          <div class="form-group"><label>Contact Address</label>
            <input type="text" data-field="personal.contact_address" value="<?= Helpers::e($d['personal']['contact_address'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Taluka</label>
            <input type="text" data-field="personal.taluka" value="<?= Helpers::e($d['personal']['taluka'] ?? '') ?>"></div>
          <div class="form-group"><label>District</label>
            <input type="text" data-field="personal.district" value="<?= Helpers::e($d['personal']['district'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Left God / Deity Caption</label>
            <input type="text" data-field="personal.god_left" value="<?= Helpers::e($d['personal']['god_left'] ?? '|| Shree Ganesh ||') ?>"></div>
          <div class="form-group"><label>Right God / Deity Caption</label>
            <input type="text" data-field="personal.god_right" value="<?= Helpers::e($d['personal']['god_right'] ?? '|| Shree Devi Prasanna ||') ?>"></div>

        <!-- Deity image upload -->
        <div class="section-divider"><span>Deity / God Images (optional)</span></div>
        <div class="form-row">
          <div class="form-group">
            <label>Left Image <span class="hint">JPG/PNG, max 1 MB</span></label>
            <input type="hidden" data-field="personal.god_left_img" value="<?= Helpers::e($d['personal']['god_left_img'] ?? '') ?>">
            <div class="drop-zone" onclick="document.getElementById('godLeftInput').click()" style="padding:12px;font-size:.77rem;color:#94a3b8">
              <?php if (!empty($d['personal']['god_left_img'])): ?>
                <img src="<?= UPLOAD_URL . Helpers::e($d['personal']['god_left_img']) ?>" style="height:60px;margin:0 auto;display:block">
              <?php else: ?>
                <i class="fas fa-image" style="font-size:1.4rem;color:#c7d2fe;display:block;text-align:center;margin-bottom:4px"></i>
                Click to upload left deity image
              <?php endif; ?>
            </div>
            <input type="file" id="godLeftInput" accept="image/*" style="display:none" onchange="uploadDeityImg(this,'god_left_img','godLeftInput')">
          </div>
          <div class="form-group">
            <label>Right Image <span class="hint">JPG/PNG, max 1 MB</span></label>
            <input type="hidden" data-field="personal.god_right_img" value="<?= Helpers::e($d['personal']['god_right_img'] ?? '') ?>">
            <div class="drop-zone" onclick="document.getElementById('godRightInput').click()" style="padding:12px;font-size:.77rem;color:#94a3b8">
              <?php if (!empty($d['personal']['god_right_img'])): ?>
                <img src="<?= UPLOAD_URL . Helpers::e($d['personal']['god_right_img']) ?>" style="height:60px;margin:0 auto;display:block">
              <?php else: ?>
                <i class="fas fa-image" style="font-size:1.4rem;color:#c7d2fe;display:block;text-align:center;margin-bottom:4px"></i>
                Click to upload right deity image
              <?php endif; ?>
            </div>
            <input type="file" id="godRightInput" accept="image/*" style="display:none" onchange="uploadDeityImg(this,'god_right_img','godRightInput')">
          </div>
        </div>
        </div>
        <!-- Achievements in biodata context -->
        <div class="section-divider"><span>Co-Curricular / Achievements</span>
          <button type="button" class="btn btn-xs btn-ghost" onclick="addEntry('certifications')"><i class="fas fa-plus"></i></button></div>
        <div id="certifications-list-bio" class="dynamic-list compact">
          <?php foreach ($d['extras']['certifications'] as $i => $cert): ?>
          <div class="dynamic-entry compact-entry" data-index="<?= $i ?>">
            <div class="form-row">
              <div class="form-group"><input type="text" data-field="extras.certifications[<?=$i?>].name" value="<?= Helpers::e($cert['name']) ?>"></div>
              <div class="form-group"><input type="text" data-field="extras.certifications[<?=$i?>].issuer" value="<?= Helpers::e($cert['issuer']) ?>"></div>
              <div class="form-group"><input type="text" data-field="extras.certifications[<?=$i?>].date" value="<?= Helpers::e($cert['date']) ?>"></div>
              <button type="button" class="btn-icon danger sm" onclick="removeEntry('certifications', <?=$i?>)"><i class="fas fa-times"></i></button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        </div><!-- /biodata -->

      </div>

      <!-- ═══ STEP 7: Styling ═══ -->
      <div class="step-pane" id="step-7">
        <div class="step-header"><h2>Template &amp; Styling</h2><p>Customize the look of your document</p></div>

        <!-- Completeness meter -->
        <div class="completeness-panel">
          <div class="completeness-header">
            <span class="completeness-title"><i class="fas fa-tasks" style="color:#6366f1;margin-right:5px"></i> Profile Completeness</span>
            <span class="completeness-pct" id="completeness-pct">–%</span>
          </div>
          <div class="progress-h" style="margin-bottom:10px">
            <div class="progress-h-bar primary" id="completeness-bar" style="width:0%"></div>
          </div>
          <div class="completeness-list" id="completeness-list"></div>
        </div>

        <!-- ATS Score -->
        <div class="ats-panel" id="ats-panel">
          <div class="ats-header">
            <div class="ats-title"><i class="fas fa-robot"></i> ATS Compatibility Score</div>
            <div class="ats-score-badge ok" id="ats-score-badge">
              <i class="fas fa-exclamation-circle"></i> –% ATS Score
            </div>
          </div>
          <div class="ats-tips" id="ats-tips-list">
            <div class="ats-tip warn"><i class="fas fa-spinner fa-spin"></i><span>Analysing your document…</span></div>
          </div>
        </div>

        <div class="form-group"><label>Template <span style="font-size:.75rem;font-weight:400;color:var(--text-muted)">(<?= count($GLOBALS['tmpl_list'] ?? []) ?> designs)</span></label>
        <?php
        $tmpl_list = [
          'modern'    => ['Modern',             '2-col sidebar, photo, clean blue',       '#2563eb','side'],
          'executive' => ['Executive',          'Dark header, gold accent, two-column',   '#c9a84c','exec'],
          'classic'   => ['Classic',            'Single column, centered header, serif',  '#1e40af','classic'],
          'minimal'   => ['Minimal',            'Swiss design, thin lines, whitespace',   '#e11d48','min'],
          'creative'  => ['Creative',           'Diagonal panel, bold left strip',        '#7c3aed','creat'],
          'timeline'  => ['Timeline',           'Vertical timeline for experience',       '#0891b2','tl'],
          'elegant'   => ['Elegant',            'Centered serif, decorative dividers',    '#92400e','eleg'],
          'tech'      => ['Tech / Dev',         'Dark header, monospace, code badges',    '#22d3ee','tech'],
          'corporate' => ['Corporate',          'Navy bar, formal business layout',       '#1e3a5f','corp'],
          'bold'      => ['Bold',               'Oversized type, color blocking',         '#dc2626','bold'],
          'fresh'     => ['Fresh',              'Soft gradient cards, rounded design',    '#059669','fresh'],
          'compact'   => ['Compact',            'Maximum density, one-page optimized',    '#1d4ed8','cmpct'],
          'indian'           => ['Indian Traditional',  '⭐ Classic Indian resume with table',    '#1a6b6b','ind'],
          'marriage_biodata' => ['Marriage Biodata',   '💒 Traditional marriage biodata format', '#8B0000','mbd'],
          'biodata'   => ['Traditional Biodata','Marriage / government format',           '#7c2d12','bio'],
        ];
        $GLOBALS['tmpl_list'] = $tmpl_list;
        ?>
        <div class="template-grid-full">
          <?php foreach($tmpl_list as $tmpl => [$label, $desc, $color, $cls]): ?>
          <label class="template-card-full <?= $st['template']===$tmpl?'active':'' ?>">
            <input type="radio" name="styling_template" value="<?= $tmpl ?>"
              <?= $st['template']===$tmpl?'checked':'' ?> data-field="styling.template" onchange="triggerSave()">
            <div class="tcf-thumb tcf-<?= $cls ?>" style="--tc:<?= $color ?>">
              <div class="tcf-header"></div>
              <div class="tcf-body">
                <div class="tcf-line l1"></div>
                <div class="tcf-line l2"></div>
                <div class="tcf-line l3"></div>
              </div>
            </div>
            <div class="tcf-info">
              <strong><?= $label ?></strong>
              <span><?= $desc ?></span>
            </div>
          </label>
          <?php endforeach; ?>
        </div>
        </div>

        <div class="form-row">
          <div class="form-group"><label>Accent Color</label>
            <div class="color-wrap">
              <input type="color" data-field="styling.accent_color" value="<?= Helpers::e($st['accent_color']) ?>" oninput="triggerSave()">
              <input type="text" id="colorHex" value="<?= Helpers::e($st['accent_color']) ?>" maxlength="7" oninput="syncColor(this)">
            </div>
          </div>
          <div class="form-group"><label>Font Size (<?= $st['font_size'] ?>px)</label>
            <input type="range" data-field="styling.font_size" min="11" max="18" value="<?= (int)$st['font_size'] ?>" oninput="this.previousElementSibling.textContent='Font Size (' + this.value + 'px)';triggerSave()">
          </div>
        </div>

        <div class="form-group"><label>Font Family</label>
          <select data-field="styling.font_family" onchange="triggerSave()">
            <?php foreach(['Inter','Georgia','Times New Roman','Arial','Roboto'] as $ff): ?>
            <option <?= $st['font_family']===$ff?'selected':'' ?>><?= $ff ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group"><label>Section Visibility</label>
          <div class="sections-grid">
            <?php foreach ($st['sections_visible'] as $sec => $vis): ?>
            <label class="section-toggle">
              <input type="checkbox" data-field="styling.sections_visible.<?= $sec ?>" <?= $vis?'checked':'' ?> onchange="triggerSave()">
              <span><?= ucfirst($sec) ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- ═══ STEP 8: Share ═══ -->
      <div class="step-pane" id="step-8">
        <div class="step-header"><h2>Share & Publish</h2><p>Share your document with a unique link</p></div>

        <div class="share-card">
          <div class="share-row">
            <div class="share-info">
              <h4>Public Link</h4>
              <p>Enable to generate a shareable URL for your document</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" id="publicToggle" <?= $doc['public_enabled']?'checked':'' ?> onchange="togglePublicLink(this)">
              <span class="toggle-track"></span>
            </label>
          </div>

          <div id="publicLinkArea" style="<?= $doc['public_enabled']?'':'display:none' ?>">
            <div class="link-display">
              <input type="text" id="publicLinkInput" readonly value="<?= $doc['public_slug'] ? APP_URL.'/view/'.$doc['public_slug'] : '' ?>">
              <button onclick="copyLink()" class="btn btn-sm btn-primary"><i class="fas fa-copy"></i></button>
              <a id="publicLinkOpen" href="<?= $doc['public_slug'] ? APP_URL.'/view/'.$doc['public_slug'] : '#' ?>" target="_blank" class="btn btn-sm btn-ghost"><i class="fas fa-external-link-alt"></i></a>
            </div>

            <div class="form-row" style="margin-top:16px">
              <div class="form-group">
                <label>Link Expiry (optional)</label>
                <input type="date" id="linkExpiry" value="<?= Helpers::e($doc['link_expiry'] ?? '') ?>" min="<?= date('Y-m-d') ?>" onchange="saveExpiry(this.value)">
              </div>
              <div class="form-group">
                <label>View Count</label>
                <div class="stat-display"><i class="fas fa-eye"></i> <?= $doc['view_count'] ?> views</div>
              </div>
            </div>

            <?php if ($doc['public_slug']): ?>
            <div class="qr-area">
              <img src="<?= Helpers::qrUrl(APP_URL.'/view/'.$doc['public_slug']) ?>" alt="QR Code" class="qr-img">
              <div class="qr-actions">
                <p>Scan to open on mobile</p>
                <a href="<?= Helpers::qrUrl(APP_URL.'/view/'.$doc['public_slug']) ?>" download="qr-code.png" class="btn btn-sm btn-ghost"><i class="fas fa-download"></i> Download QR</a>
                <a href="https://wa.me/?text=<?= urlencode(APP_URL.'/view/'.$doc['public_slug']) ?>" target="_blank" class="btn btn-sm btn-ghost" style="color:#25d366"><i class="fab fa-whatsapp"></i> WhatsApp</a>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="share-card" style="margin-top:16px">
          <h4>Download & Print</h4>
          <div class="share-actions-row">
            <a href="api/pdf.php?id=<?= $docId ?>" target="_blank" class="btn btn-primary"><i class="fas fa-file-pdf"></i> Download PDF</a>
            <button onclick="printPreview()" class="btn btn-ghost"><i class="fas fa-print"></i> Print</button>
          </div>
        </div>
      </div>

    </div><!-- /step-content -->

    <!-- Navigation -->
    <div class="wizard-nav">
      <button class="btn btn-ghost" id="btnPrev" onclick="changeStep(-1)" style="display:none"><i class="fas fa-arrow-left"></i> Previous</button>
      <div style="flex:1"></div>
      <button class="btn btn-primary" id="btnNext" onclick="changeStep(1)">Next <i class="fas fa-arrow-right"></i></button>
      <button class="btn btn-success" id="btnFinish" style="display:none" onclick="goStep(8)"><i class="fas fa-check"></i> Finish</button>
    </div>
  </div>

  <!-- RIGHT: Preview Panel -->
  <div class="wizard-preview-panel">
    <div class="preview-header">
      <span class="preview-label"><i class="fas fa-eye"></i> Live Preview</span>
      <div class="preview-actions">
        <button onclick="refreshPreview()" class="btn btn-xs btn-ghost" title="Refresh"><i class="fas fa-sync"></i></button>
        <a href="api/pdf.php?id=<?= $docId ?>" target="_blank" class="btn btn-xs btn-primary"><i class="fas fa-file-pdf"></i> PDF</a>
      </div>
    </div>
    <div class="preview-loader" id="previewLoader"><div class="spinner"></div></div>
    <iframe id="previewFrame" src="api/preview.php?id=<?= $docId ?>" class="preview-iframe"></iframe>
  </div>
</div>

<script>
const DOC_ID     = <?= $docId ?>;
const CSRF_TOKEN = '<?= $csrf ?>';
const APP_URL    = '<?= APP_URL ?>';
window.APP_URL   = APP_URL;
</script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
<script src="<?= APP_URL ?>/assets/js/wizard.js"></script>
<!-- Session warning -->
<div id="session-warning">
  <i class="fas fa-clock"></i>
  Your session will expire in <strong>5 minutes</strong>. Unsaved changes may be lost.
  <button class="btn btn-sm btn-primary" onclick="SessionGuard.reset();document.getElementById('session-warning').classList.remove('show')">Stay Active</button>
</div>
<div id="toast-container"></div>
</body>
</html>


