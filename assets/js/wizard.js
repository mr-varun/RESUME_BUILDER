/* ══════════════════════════════════════════════════════════
   ResumeForge Wizard Engine — clean single-file version
══════════════════════════════════════════════════════════ */
'use strict';

/* ── State ─────────────────────────────────────────────── */
let currentStep    = 1;
const TOTAL_STEPS  = 8;
let saveTimer      = null;
let previewTimer   = null;
let sideTimer      = null;    // completeness / ATS debounce
let pendingChanges = false;

/* ── Bootstrap ─────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  initStepTabs();
  initInputListeners();
  initTitleListener();
  initPreview();
  initPreviewToggle();
  initKeyboardShortcuts();
  initDragDropPhoto();
  updateNav();

  const savedType = document.querySelector('input[name="doc_type"]:checked')?.value || 'resume';
  applyDocTypeFilter(savedType);

  // Delay side-panel calcs so DOM is ready
  setTimeout(refreshSidePanel, 400);

  // Auto-suggest title from name field
  document.querySelector('[data-field="basic.full_name"]')
    ?.addEventListener('blur', autoTitleFromName);
});

/* ══════════════════════════════════════════════════════════
   DOC-TYPE FILTER — show/hide fields per type
══════════════════════════════════════════════════════════ */
function applyDocTypeFilter(type) {
  document.querySelectorAll('[data-show-for]').forEach(el => {
    const types = el.getAttribute('data-show-for').split(',').map(s => s.trim());
    el.style.display = types.includes(type) ? '' : 'none';
  });
  updateStepLabels(type);
}

function updateStepLabels(type) {
  document.querySelectorAll('.step-tab[data-lbl-' + type + ']').forEach(tab => {
    const lbl  = tab.getAttribute('data-lbl-' + type);
    const span = tab.querySelector('.step-lbl');
    if (span && lbl) span.textContent = lbl;
  });
}

/* ══════════════════════════════════════════════════════════
   STEP NAVIGATION
══════════════════════════════════════════════════════════ */
function goStep(n) {
  if (n < 1 || n > TOTAL_STEPS) return;
  document.querySelectorAll('.step-pane').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.step-tab').forEach(t => {
    const s = parseInt(t.dataset.step);
    t.classList.toggle('active', s === n);
    t.classList.toggle('done',   s < n);
    if (s >= n) t.classList.remove('done');
  });
  const pane = document.getElementById('step-' + n);
  const tab  = document.querySelector('.step-tab[data-step="' + n + '"]');
  if (pane) pane.classList.add('active');
  if (tab)  tab.classList.add('active');
  currentStep = n;
  updateNav();
  updateProgress();
}

function changeStep(delta) {
  if (delta > 0 && !validateCurrentStep()) return;
  goStep(currentStep + delta);
}

function initStepTabs() {
  document.querySelectorAll('.step-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      const s = parseInt(tab.dataset.step);
      if (s) goStep(s);
    });
  });
}

function updateNav() {
  const prev   = document.getElementById('btnPrev');
  const next   = document.getElementById('btnNext');
  const finish = document.getElementById('btnFinish');
  if (prev)   prev.style.display   = currentStep > 1 ? '' : 'none';
  if (next)   next.style.display   = currentStep < TOTAL_STEPS - 1 ? '' : 'none';
  if (finish) finish.style.display = currentStep >= TOTAL_STEPS - 1 ? '' : 'none';
}

function updateProgress() {
  const bar = document.getElementById('progressBar');
  if (bar) bar.style.width = (currentStep / TOTAL_STEPS * 100) + '%';
}

/* ══════════════════════════════════════════════════════════
   STEP VALIDATION
══════════════════════════════════════════════════════════ */
const REQUIRED = {
  2: [
    { field: 'basic.full_name', label: 'Full Name' },
    { field: 'basic.phone',     label: 'Phone Number' },
    { field: 'basic.email',     label: 'Email Address' },
  ],
};

function validateCurrentStep() {
  const rules = REQUIRED[currentStep];
  if (!rules) return true;
  document.querySelectorAll('.field-error').forEach(el => el.classList.remove('field-error'));

  for (const { field, label } of rules) {
    const el = document.querySelector(`[data-field="${field}"]`);
    if (el && !el.value.trim()) {
      el.classList.add('field-error');
      el.addEventListener('input', () => el.classList.remove('field-error'), { once: true });
      el.focus();
      window.Toast?.error(`Please fill in: ${label}`);
      return false;
    }
  }
  return true;
}

/* ══════════════════════════════════════════════════════════
   INPUT LISTENERS
══════════════════════════════════════════════════════════ */
function initInputListeners() {
  const panel = document.querySelector('.wizard-form-panel');
  if (!panel) return;
  panel.addEventListener('input',  onFormChange);
  panel.addEventListener('change', onFormChange);
}

function onFormChange(e) {
  const el = e.target;

  if (el.classList.contains('skill-range'))  updateSkillLabel(el);
  updateEntryTitle(el);

  if (el.name === 'doc_type') {
    document.querySelectorAll('.type-card').forEach(c => c.classList.remove('active'));
    el.closest('.type-card')?.classList.add('active');
  }
  if (el.name === 'styling_template') {
    document.querySelectorAll('.template-card-full').forEach(c => c.classList.remove('active'));
    el.closest('.template-card-full')?.classList.add('active');
  }

  pendingChanges = true;
  scheduleAutoSave();
  schedulePreviewUpdate();
  scheduleSidePanel();
}

function updateEntryTitle(input) {
  const entry     = input.closest('.dynamic-entry');
  const titleSpan = entry?.querySelector('.entry-title');
  if (!titleSpan) return;
  const field = input.getAttribute('data-field') || '';
  if (field.includes('degree') || field.includes('job_title') ||
      (field.includes('name') && !field.includes('full_name'))) {
    if (input.value.trim()) titleSpan.textContent = input.value.trim();
  }
}

function initTitleListener() {
  document.getElementById('docTitle')?.addEventListener('input', () => {
    pendingChanges = true;
    scheduleAutoSave();
  });
}

/* ══════════════════════════════════════════════════════════
   AUTO-SAVE  (single declaration — no duplicates)
══════════════════════════════════════════════════════════ */
function scheduleAutoSave() {
  clearTimeout(saveTimer);
  setSaveState('saving');
  saveTimer = setTimeout(doSave, 1500);
}

function triggerSave() {
  pendingChanges = true;
  scheduleAutoSave();
  schedulePreviewUpdate();
}

async function doSave() {
  if (!pendingChanges) { setSaveState('saved'); return; }
  setSaveState('saving');

  const payload = {
    doc_id:   DOC_ID,
    data:     collectFormData(),
    title:    document.getElementById('docTitle')?.value?.trim() || '',
    doc_type: document.querySelector('input[name="doc_type"]:checked')?.value || 'resume',
    status:   document.getElementById('docStatus')?.value || 'draft',
  };

  try {
    const res  = await fetch('api/autosave.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF_TOKEN },
      body:    JSON.stringify(payload),
    });
    const json = await res.json();
    if (json.success) {
      setSaveState('saved');
      pendingChanges = false;
      // Refresh preview AFTER data is persisted to DB
      refreshPreview();
    } else {
      setSaveState('error');
      window.Toast?.error('Save failed. Check your connection.');
    }
  } catch {
    setSaveState('error');
    window.Toast?.error('Save failed. Check your connection.');
  }
}

// Keep 'autoSave' as a global alias so updateMeta() / external callers still work
function autoSave() { return doSave(); }

function setSaveState(state) {
  const indicator = document.getElementById('saveIndicator');
  const text      = document.getElementById('saveText');
  if (!indicator || !text) return;
  indicator.className = 'save-indicator ' + state;
  text.textContent = { saving: 'Saving…', saved: 'Saved', error: 'Save error' }[state] || '';
}

/* ══════════════════════════════════════════════════════════
   COLLECT FORM DATA
══════════════════════════════════════════════════════════ */
function collectFormData() {
  const data = {
    basic:    {},
    education:  [],
    experience: [],
    skills:     [],
    personal:   {},
    extras: {
      certifications: [],
      projects:       [],
      achievements:   [],
      references:     [],
    },
    styling: { sections_visible: {} },
  };

  const allFields = document.querySelectorAll('[data-field]');

  // Pass 1 — visible fields (not hidden by data-show-for filter)
  // These always win; they reflect what the user can actually see & edit.
  allFields.forEach(el => {
    if (isDocTypeHidden(el)) return;
    const val = getFieldValue(el);
    if (val === undefined) return;
    setNestedValue(data, el.getAttribute('data-field'), val);
  });

  // Pass 2 — hidden fields (wrong doc-type or collapsed sections)
  // Only fills paths that Pass 1 left completely unset, preserving saved data
  // when the user switches doc types.
  allFields.forEach(el => {
    if (!isDocTypeHidden(el)) return;
    const field = el.getAttribute('data-field');
    const val   = getFieldValue(el);
    if (val === undefined) return;
    if (!hasNestedValue(data, field)) setNestedValue(data, field, val);
  });

  return data;
}

// Returns true if the element lives inside a data-show-for container
// that is currently hidden (display:none set by applyDocTypeFilter).
function isDocTypeHidden(el) {
  let p = el.parentElement;
  while (p && p !== document.documentElement) {
    if (p.hasAttribute('data-show-for') && p.style.display === 'none') return true;
    p = p.parentElement;
  }
  return false;
}

// Returns true when the nested path already has a non-empty value.
function hasNestedValue(obj, path) {
  const re = /([^.\[\]]+)|\[(\d+)\]/g;
  let m, cur = obj;
  while ((m = re.exec(path)) !== null) {
    const key = m[1] !== undefined ? m[1] : parseInt(m[2]);
    if (cur === undefined || cur === null) return false;
    cur = cur[key];
  }
  return cur !== undefined && cur !== null && cur !== '';
}

function getFieldValue(el) {
  if (el.type === 'checkbox') return el.checked;
  if (el.type === 'radio')    return el.checked ? el.value : undefined;
  return el.value;
}

function setNestedValue(obj, path, value) {
  const tokens = [];
  const re = /([^.\[\]]+)|\[(\d+)\]/g;
  let m;
  while ((m = re.exec(path)) !== null) {
    tokens.push(m[1] !== undefined ? m[1] : parseInt(m[2]));
  }
  if (!tokens.length) return;

  let cur = obj;
  for (let i = 0; i < tokens.length - 1; i++) {
    const t    = tokens[i];
    const next = tokens[i + 1];
    if (cur[t] === undefined || cur[t] === null) {
      cur[t] = typeof next === 'number' ? [] : {};
    }
    // If expected array but got object (or vice versa), coerce
    if (typeof next === 'number' && !Array.isArray(cur[t])) cur[t] = [];
    if (typeof next === 'string' && Array.isArray(cur[t]))  cur[t] = {};
    cur = cur[t];
  }

  const last = tokens[tokens.length - 1];
  if (typeof last === 'number') {
    if (!Array.isArray(cur)) return;
    while (cur.length <= last) cur.push({});
    if (typeof cur[last] !== 'object' || cur[last] === null) cur[last] = {};
    // If value is a primitive, merge into the object under a _val key
    // (shouldn't happen with current field naming)
    cur[last] = value;
  } else {
    cur[last] = value;
  }
}

/* ══════════════════════════════════════════════════════════
   LIVE PREVIEW
══════════════════════════════════════════════════════════ */
function schedulePreviewUpdate() {
  clearTimeout(previewTimer);
  previewTimer = setTimeout(refreshPreview, 2500);
}

function refreshPreview() {
  const iframe = document.getElementById('previewFrame');
  const loader = document.getElementById('previewLoader');
  if (!iframe) return;
  if (loader) loader.classList.add('active');
  iframe.onload = () => { if (loader) loader.classList.remove('active'); };
  iframe.src = 'api/preview.php?id=' + DOC_ID + '&t=' + Date.now();
}

function initPreview() {
  const iframe = document.getElementById('previewFrame');
  const loader = document.getElementById('previewLoader');
  if (!iframe) return;
  iframe.onload = () => { if (loader) loader.classList.remove('active'); };
}

/* ══════════════════════════════════════════════════════════
   PREVIEW TOGGLE
══════════════════════════════════════════════════════════ */
function togglePreview() {
  const panel  = document.querySelector('.wizard-preview-panel');
  const layout = document.querySelector('.wizard-layout');
  const btn    = document.getElementById('previewToggleBtn');
  if (!panel) return;
  const hidden = panel.style.display === 'none';
  panel.style.display = hidden ? '' : 'none';
  layout.classList.toggle('preview-hidden', !hidden);
  btn.innerHTML = hidden
    ? '<i class="fas fa-eye-slash"></i> Preview'
    : '<i class="fas fa-eye"></i> Preview';
  localStorage.setItem('rf_preview_hidden', hidden ? '0' : '1');
}

function initPreviewToggle() {
  const panel  = document.querySelector('.wizard-preview-panel');
  const layout = document.querySelector('.wizard-layout');
  const btn    = document.getElementById('previewToggleBtn');
  if (!panel || !btn) return;
  if (localStorage.getItem('rf_preview_hidden') === '1') {
    panel.style.display = 'none';
    layout.classList.add('preview-hidden');
    btn.innerHTML = '<i class="fas fa-eye"></i> Preview';
  }
}

/* ══════════════════════════════════════════════════════════
   PRINT
══════════════════════════════════════════════════════════ */
function printPreview() {
  window.open('api/pdf.php?id=' + DOC_ID + '&print=1', '_blank');
}

/* ══════════════════════════════════════════════════════════
   META UPDATE (doc type / status)
══════════════════════════════════════════════════════════ */
function updateMeta(fields) {
  pendingChanges = true;
  scheduleAutoSave();
}

/* ══════════════════════════════════════════════════════════
   PHOTO UPLOAD
══════════════════════════════════════════════════════════ */
function initDragDropPhoto() {
  const area = document.getElementById('photoUploadArea');
  if (!area) return;
  area.addEventListener('dragover',  e => { e.preventDefault(); area.classList.add('drag-over'); });
  area.addEventListener('dragleave', ()=> area.classList.remove('drag-over'));
  area.addEventListener('drop', e => {
    e.preventDefault();
    area.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) handlePhotoFile(file);
  });
}

function uploadPhoto(input) {
  const file = input.files?.[0];
  if (file) handlePhotoFile(file);
}

async function handlePhotoFile(file) {
  if (!['image/jpeg','image/png','image/webp'].includes(file.type)) {
    window.Toast?.error('Only JPG, PNG or WebP images are allowed.');
    return;
  }
  if (file.size > 2 * 1024 * 1024) {
    window.Toast?.error('Photo must be smaller than 2 MB.');
    return;
  }

  const uploadBtn = document.querySelector('.photo-actions .btn');
  if (uploadBtn) { uploadBtn.disabled = true; uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading…'; }

  const fd = new FormData();
  fd.append('photo', file);
  fd.append('_csrf', CSRF_TOKEN);
  const oldPhoto = document.querySelector('[data-field="basic.photo"]')?.value || '';
  if (oldPhoto) fd.append('old_photo', oldPhoto);

  try {
    const res  = await fetch('api/upload.php', { method: 'POST', body: fd });
    const json = await res.json();
    if (json.success) {
      let hidden = document.querySelector('[data-field="basic.photo"]');
      if (!hidden) {
        hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.setAttribute('data-field', 'basic.photo');
        document.querySelector('.wizard-form-panel')?.appendChild(hidden);
      }
      hidden.value = json.filename;
      const preview     = document.getElementById('photoPreview');
      const placeholder = document.getElementById('photoPlaceholder');
      if (preview)     { preview.src = json.url; preview.style.display = ''; }
      if (placeholder) placeholder.style.display = 'none';
      const removeBtn = document.getElementById('removePhotoBtn');
      if (removeBtn) removeBtn.style.display = '';
      triggerSave();
      window.Toast?.success('Photo uploaded.');
    } else {
      window.Toast?.error(json.error || 'Upload failed.');
    }
  } catch {
    window.Toast?.error('Upload failed. Check your connection.');
  } finally {
    if (uploadBtn) { uploadBtn.disabled = false; uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Photo'; }
  }
}

function removePhoto() {
  const preview     = document.getElementById('photoPreview');
  const placeholder = document.getElementById('photoPlaceholder');
  const hidden      = document.querySelector('[data-field="basic.photo"]');
  if (preview)     { preview.src = ''; preview.style.display = 'none'; }
  if (placeholder) placeholder.style.display = '';
  if (hidden)      hidden.value = '';
  document.getElementById('removePhotoBtn').style.display = 'none';
  triggerSave();
}

/* ══════════════════════════════════════════════════════════
   DEITY IMAGE UPLOAD
══════════════════════════════════════════════════════════ */
async function uploadDeityImg(input, fieldName, inputId) {
  const file = input.files?.[0];
  if (!file) return;
  if (file.size > 1024 * 1024) { window.Toast?.warning('Deity image must be under 1 MB.'); return; }

  const fd = new FormData();
  fd.append('photo', file);
  fd.append('_csrf', CSRF_TOKEN);
  try {
    const res  = await fetch('api/upload.php', { method: 'POST', body: fd });
    const json = await res.json();
    if (json.success) {
      const hidden = document.querySelector(`[data-field="personal.${fieldName}"]`);
      if (hidden) hidden.value = json.filename;
      const zone = document.getElementById(inputId)?.previousElementSibling;
      if (zone) zone.innerHTML = `<img src="${json.url}" style="height:60px;margin:0 auto;display:block">`;
      triggerSave();
      window.Toast?.success('Image uploaded.');
    } else {
      window.Toast?.error(json.error || 'Upload failed.');
    }
  } catch { window.Toast?.error('Upload failed.'); }
}

/* ══════════════════════════════════════════════════════════
   COLLAPSE / EXPAND ENTRIES
══════════════════════════════════════════════════════════ */
function toggleEntry(btn) {
  const body = btn.closest('.dynamic-entry')?.querySelector('.entry-body');
  if (!body) return;
  body.classList.toggle('collapsed');
  const icon = btn.querySelector('i');
  if (icon) icon.className = body.classList.contains('collapsed')
    ? 'fas fa-chevron-down' : 'fas fa-chevron-up';
}

function toggleCurrentJob(checkbox, idx) {
  const toInput = document.getElementById('exp-date-to-' + idx);
  if (toInput) toInput.disabled = checkbox.checked;
}

/* ══════════════════════════════════════════════════════════
   DYNAMIC ENTRIES — add / remove
══════════════════════════════════════════════════════════ */
const ENTRY_TEMPLATES = {
  education: idx => `
    <div class="entry-header">
      <span class="entry-title">Education ${idx+1}</span>
      <div>
        <button type="button" class="btn-icon sm" onclick="toggleEntry(this)"><i class="fas fa-chevron-up"></i></button>
        <button type="button" class="btn-icon danger sm" onclick="removeEntry('education',${idx})"><i class="fas fa-trash"></i></button>
      </div>
    </div>
    <div class="entry-body">
      <div class="form-row">
        <div class="form-group"><label>Degree <span class="req">*</span></label>
          <input type="text" data-field="education[${idx}].degree" placeholder="B.Tech Computer Science"></div>
        <div class="form-group"><label>Institution <span class="req">*</span></label>
          <input type="text" data-field="education[${idx}].institution" placeholder="IIT Mumbai"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>University / Board</label>
          <input type="text" data-field="education[${idx}].university" placeholder="Mumbai University"></div>
        <div class="form-group"><label>Grade / CGPA</label>
          <input type="text" data-field="education[${idx}].grade" placeholder="8.5 CGPA"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Year From</label>
          <input type="text" data-field="education[${idx}].year_from" placeholder="2018"></div>
        <div class="form-group"><label>Year To</label>
          <input type="text" data-field="education[${idx}].year_to" placeholder="2022 or Present"></div>
      </div>
    </div>`,

  experience: idx => `
    <div class="entry-header">
      <span class="entry-title">Experience ${idx+1}</span>
      <div>
        <button type="button" class="btn-icon sm" onclick="toggleEntry(this)"><i class="fas fa-chevron-up"></i></button>
        <button type="button" class="btn-icon danger sm" onclick="removeEntry('experience',${idx})"><i class="fas fa-trash"></i></button>
      </div>
    </div>
    <div class="entry-body">
      <div class="form-row">
        <div class="form-group"><label>Job Title <span class="req">*</span></label>
          <input type="text" data-field="experience[${idx}].job_title" placeholder="Software Engineer"></div>
        <div class="form-group"><label>Company <span class="req">*</span></label>
          <input type="text" data-field="experience[${idx}].company" placeholder="TechCorp Pvt Ltd"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Location</label>
          <input type="text" data-field="experience[${idx}].location" placeholder="Mumbai, India"></div>
        <div class="form-group"><label>Type</label>
          <select data-field="experience[${idx}].employment_type">
            <option>Full-time</option><option>Part-time</option>
            <option>Contract</option><option>Internship</option><option>Freelance</option>
          </select></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>From</label>
          <input type="month" data-field="experience[${idx}].date_from"></div>
        <div class="form-group">
          <label for="exp-date-to-${idx}">To
            <span style="font-weight:400;font-size:.74rem;margin-left:6px">
              <input type="checkbox" id="exp-cur-${idx}"
                data-field="experience[${idx}].is_current"
                onchange="toggleCurrentJob(this,${idx})">
              <label for="exp-cur-${idx}" style="font-weight:400;cursor:pointer"> Currently working</label>
            </span>
          </label>
          <input id="exp-date-to-${idx}" type="month" data-field="experience[${idx}].date_to">
        </div>
      </div>
      <div class="form-group"><label>Description</label>
        <textarea data-field="experience[${idx}].description" rows="3"
          placeholder="• Developed REST APIs…&#10;• Led team of 5 engineers…"></textarea></div>
    </div>`,

  skills: idx => `
    <div class="skill-entry" data-index="${idx}">
      <input type="text" data-field="skills[${idx}].name" placeholder="Skill name" class="skill-name">
      <div class="skill-level-wrap">
        <input type="range" data-field="skills[${idx}].level" min="10" max="100" step="10"
          value="80" class="skill-range" oninput="updateSkillLabel(this)">
        <span class="skill-pct">80%</span>
      </div>
      <button type="button" class="btn-icon danger sm" onclick="removeEntry('skills',${idx})">
        <i class="fas fa-times"></i></button>
    </div>`,

  certifications: idx => `
    <div class="dynamic-entry compact-entry" data-index="${idx}">
      <div class="form-row">
        <div class="form-group"><input type="text" data-field="extras.certifications[${idx}].name"
          placeholder="Certification name"></div>
        <div class="form-group"><input type="text" data-field="extras.certifications[${idx}].issuer"
          placeholder="Issuing org"></div>
        <div class="form-group" style="max-width:90px"><input type="text"
          data-field="extras.certifications[${idx}].date" placeholder="2024"></div>
        <button type="button" class="btn-icon danger sm" onclick="removeEntry('certifications',${idx})">
          <i class="fas fa-times"></i></button>
      </div>
    </div>`,

  projects: idx => `
    <div class="dynamic-entry" data-index="${idx}">
      <div class="entry-header">
        <span class="entry-title">Project ${idx+1}</span>
        <button type="button" class="btn-icon danger sm" onclick="removeEntry('projects',${idx})">
          <i class="fas fa-trash"></i></button>
      </div>
      <div class="entry-body">
        <div class="form-row">
          <div class="form-group"><label>Name</label>
            <input type="text" data-field="extras.projects[${idx}].name" placeholder="E-Commerce Platform"></div>
          <div class="form-group"><label>Role</label>
            <input type="text" data-field="extras.projects[${idx}].role" placeholder="Full-Stack Dev"></div>
        </div>
        <div class="form-group"><label>Tech Stack</label>
          <input type="text" data-field="extras.projects[${idx}].tech_stack" placeholder="PHP, MySQL, React"></div>
        <div class="form-group"><label>Description</label>
          <textarea data-field="extras.projects[${idx}].description" rows="2"></textarea></div>
      </div>
    </div>`,

  achievements: idx => `
    <div class="dynamic-entry compact-entry" data-index="${idx}">
      <div class="form-row">
        <div class="form-group"><input type="text" data-field="extras.achievements[${idx}].title"
          placeholder="Achievement title"></div>
        <div class="form-group" style="max-width:80px"><input type="text"
          data-field="extras.achievements[${idx}].year" placeholder="Year"></div>
        <button type="button" class="btn-icon danger sm" onclick="removeEntry('achievements',${idx})">
          <i class="fas fa-times"></i></button>
      </div>
      <div class="form-group"><textarea data-field="extras.achievements[${idx}].description"
        rows="1" placeholder="Brief description…"></textarea></div>
    </div>`,

  references: idx => `
    <div class="dynamic-entry compact-entry" data-index="${idx}">
      <div class="form-row">
        <div class="form-group"><input type="text" data-field="extras.references[${idx}].name"
          placeholder="Reference name"></div>
        <div class="form-group"><input type="text" data-field="extras.references[${idx}].designation"
          placeholder="Designation"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><input type="email" data-field="extras.references[${idx}].email"
          placeholder="Email"></div>
        <div class="form-group"><input type="tel" data-field="extras.references[${idx}].phone"
          placeholder="Phone"></div>
        <button type="button" class="btn-icon danger sm" onclick="removeEntry('references',${idx})">
          <i class="fas fa-times"></i></button>
      </div>
    </div>`,
};

function getListElement(type) {
  return document.getElementById({
    education:       'education-list',
    experience:      'experience-list',
    skills:          'skills-list',
    certifications:  'certifications-list',
    projects:        'projects-list',
    achievements:    'achievements-list',
    references:      'references-list',
  }[type])
  // Fallbacks for CV / biodata secondary lists
  || document.getElementById({ skills: 'skills-list-cv', certifications: 'certifications-list-bio' }[type])
  || null;
}

function addEntry(type) {
  const list = getListElement(type);
  if (!list) return;
  list.querySelector('.empty-list-hint')?.remove();

  const existing = list.querySelectorAll('[data-index]');
  const idx      = existing.length;
  const tmpl     = ENTRY_TEMPLATES[type];
  if (!tmpl) return;

  const isSkill = type === 'skills';
  const wrapper = document.createElement('div');
  wrapper.className = isSkill ? '' : 'dynamic-entry';
  wrapper.innerHTML = tmpl(idx);

  if (isSkill) {
    list.appendChild(wrapper.firstElementChild);
  } else {
    list.appendChild(wrapper);
  }
  wrapper.querySelector('input,textarea')?.focus();
  triggerSave();
}

function removeEntry(type, idx) {
  const list  = getListElement(type);
  if (!list) return;
  const entry = list.querySelector(`[data-index="${idx}"]`);
  if (entry) entry.remove();

  // Re-index remaining
  list.querySelectorAll('[data-index]').forEach((el, i) => {
    el.setAttribute('data-index', i);
    el.querySelectorAll('[data-field]').forEach(f => {
      f.setAttribute('data-field', f.getAttribute('data-field').replace(/\[\d+\]/, `[${i}]`));
    });
    el.querySelectorAll(`[onclick*="removeEntry('${type}'"]`).forEach(b => {
      b.setAttribute('onclick', `removeEntry('${type}',${i})`);
    });
  });

  if (list.children.length === 0) {
    const icons = { education:'graduation-cap', experience:'briefcase', skills:'tools',
      certifications:'certificate', projects:'code', achievements:'trophy', references:'user-tie' };
    list.innerHTML = `<div class="empty-list-hint">
      <i class="fas fa-${icons[type]||'plus'}"></i> No ${type} added yet.</div>`;
  }
  triggerSave();
}

/* ══════════════════════════════════════════════════════════
   SKILL HELPERS
══════════════════════════════════════════════════════════ */
function updateSkillLabel(slider) {
  const pct = slider.closest('.skill-level-wrap')?.querySelector('.skill-pct');
  if (pct) pct.textContent = slider.value + '%';
}

/* ══════════════════════════════════════════════════════════
   COLOR SYNC
══════════════════════════════════════════════════════════ */
function syncColor(input) {
  const colorInput = document.querySelector('[data-field="styling.accent_color"]');
  if (colorInput && /^#[0-9a-fA-F]{6}$/.test(input.value)) {
    colorInput.value = input.value;
    triggerSave();
  }
}

/* ══════════════════════════════════════════════════════════
   SHARE / PUBLIC LINK
══════════════════════════════════════════════════════════ */
async function togglePublicLink(checkbox) {
  const area = document.getElementById('publicLinkArea');
  if (checkbox.checked) {
    const expiry = document.getElementById('linkExpiry')?.value || null;
    try {
      const res  = await fetch('api/share.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF_TOKEN },
        body:   JSON.stringify({ action: 'enable', doc_id: DOC_ID, expiry }),
      });
      const json = await res.json();
      if (json.success) {
        document.getElementById('publicLinkInput').value = json.url;
        document.getElementById('publicLinkOpen').href   = json.url;
        if (area) area.style.display = '';
        window.Toast?.success('Public link enabled!');
        location.reload();
      }
    } catch { checkbox.checked = false; }
  } else {
    try {
      await fetch('api/share.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF_TOKEN },
        body:   JSON.stringify({ action: 'disable', doc_id: DOC_ID }),
      });
      if (area) area.style.display = 'none';
      window.Toast?.info('Public link disabled.');
    } catch {}
  }
}

function copyLink() {
  const input = document.getElementById('publicLinkInput');
  if (!input?.value) return;
  navigator.clipboard.writeText(input.value).then(() => {
    window.Toast?.success('Link copied to clipboard!');
  });
}

async function saveExpiry(value) {
  try {
    await fetch('api/share.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF_TOKEN },
      body:   JSON.stringify({ action: 'update_expiry', doc_id: DOC_ID, expiry: value || null }),
    });
    window.Toast?.success('Expiry date saved.');
  } catch {}
}

/* ══════════════════════════════════════════════════════════
   KEYBOARD SHORTCUTS
══════════════════════════════════════════════════════════ */
function initKeyboardShortcuts() {
  document.addEventListener('keydown', e => {
    const inInput = ['INPUT','TEXTAREA','SELECT'].includes(document.activeElement?.tagName);

    // Ctrl/Cmd + S → force save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
      e.preventDefault();
      pendingChanges = true;
      doSave();
      window.Toast?.info('Saving…', 1200);
    }
    // Alt + Arrow → change step (not when typing)
    if (e.altKey && !inInput) {
      if (e.key === 'ArrowRight') { e.preventDefault(); changeStep(1);  }
      if (e.key === 'ArrowLeft')  { e.preventDefault(); changeStep(-1); }
    }
    // Escape → close dropdowns / modals
    if (e.key === 'Escape') {
      document.querySelectorAll('.more-menu.open').forEach(m => m.classList.remove('open'));
      document.querySelectorAll('.modal-overlay.open').forEach(m => {
        m.classList.remove('open');
        setTimeout(() => m.remove(), 200);
      });
    }
  });
}

/* ══════════════════════════════════════════════════════════
   AUTO-TITLE FROM NAME
══════════════════════════════════════════════════════════ */
function autoTitleFromName() {
  const nameEl  = document.querySelector('[data-field="basic.full_name"]');
  const titleEl = document.getElementById('docTitle');
  if (!nameEl || !titleEl) return;
  const name = nameEl.value.trim();
  if (!name) return;
  if (/^untitled/i.test(titleEl.value.trim())) {
    const type = document.querySelector('input[name="doc_type"]:checked')?.value || 'resume';
    titleEl.value = `${name}'s ${type.charAt(0).toUpperCase() + type.slice(1)}`;
    pendingChanges = true;
    scheduleAutoSave();
  }
}

/* ══════════════════════════════════════════════════════════
   COMPLETENESS METER + ATS SCORE  (debounced side panel)
══════════════════════════════════════════════════════════ */
function scheduleSidePanel() {
  clearTimeout(sideTimer);
  sideTimer = setTimeout(refreshSidePanel, 800);
}

function refreshSidePanel() {
  updateCompleteness();
  updateAtsScore();
}

function updateCompleteness() {
  const pctEl  = document.getElementById('completeness-pct');
  const barEl  = document.getElementById('completeness-bar');
  const listEl = document.getElementById('completeness-list');
  if (!pctEl) return;

  const data   = collectFormData();
  const type   = document.querySelector('input[name="doc_type"]:checked')?.value || 'resume';
  const checks = getCompletenessChecks(data, type);
  const done   = checks.filter(c => c.ok).length;
  const pct    = Math.round(done / checks.length * 100);

  pctEl.textContent = pct + '%';
  if (barEl) {
    barEl.style.width = pct + '%';
    barEl.className = 'progress-h-bar ' + (pct >= 80 ? 'success' : pct >= 50 ? 'primary' : 'warning');
  }
  if (listEl) {
    listEl.innerHTML = checks.map(c => `
      <div class="check-item ${c.ok ? 'done' : 'missing'}">
        <i class="fas fa-${c.ok ? 'check-circle' : 'circle'}"></i> ${c.label}
      </div>`).join('');
  }
}

function getCompletenessChecks(data, type) {
  const b = data.basic    || {};
  const p = data.personal || {};
  const base = [
    { label: 'Full name',       ok: !!b.full_name?.trim() },
    { label: 'Email address',   ok: !!b.email?.trim() },
    { label: 'Phone number',    ok: !!b.phone?.trim() },
    { label: 'City / Location', ok: !!(b.city?.trim() || b.address?.trim()) },
    { label: 'Profile photo',   ok: !!b.photo },
    { label: 'Education',       ok: (data.education||[]).some(e => e.degree?.trim()) },
    { label: 'Skills (5+)',     ok: (data.skills||[]).filter(s => s.name?.trim()).length >= 5 },
  ];
  if (type !== 'biodata') {
    base.push(
      { label: 'Professional summary',  ok: !!b.objective?.trim() },
      { label: 'Work experience',       ok: (data.experience||[]).some(e => e.job_title?.trim()) },
      { label: 'LinkedIn profile',      ok: !!b.linkedin?.trim() },
    );
  } else {
    base.push(
      { label: 'Date of birth',  ok: !!p.dob },
      { label: "Father's name", ok: !!p.father_name?.trim() },
      { label: "Mother's name", ok: !!p.mother_name?.trim() },
    );
  }
  return base;
}

function updateAtsScore() {
  const badgeEl = document.getElementById('ats-score-badge');
  const tipsEl  = document.getElementById('ats-tips-list');
  if (!badgeEl) return;

  const data  = collectFormData();
  const b     = data.basic || {};
  const tips  = [];
  let score   = 0;

  function check(ok, passText, failText) {
    tips.push({ ok, text: ok ? passText : failText });
    if (ok) score++;
  }

  check(!!b.full_name?.trim(),          'Full name present',                    'Add your full name');
  check(!!b.email?.trim(),              'Email address present',                'Add a contact email');
  check(!!b.phone?.trim(),              'Phone number present',                 'Add a phone number');
  check(b.objective?.length > 50,       'Summary is detailed (50+ chars)',      'Write a summary (50+ chars) — ATS scans this first');
  check((data.experience||[]).some(e => e.job_title?.trim()), 'Work experience listed', 'Add at least one work experience');
  check((data.education||[]).some(e => e.degree?.trim()),     'Education section present', 'Add your education');
  check((data.skills||[]).filter(s=>s.name?.trim()).length >= 5, '5+ skills listed', 'Add at least 5 skills — ATS matches keywords');
  check(!!b.linkedin?.trim(),           'LinkedIn profile linked',              'Add your LinkedIn URL');
  check((data.experience||[]).some(e => e.description?.length > 80), 'Experience has descriptions', 'Add detail to experience entries (80+ chars)');
  check(!b.objective?.toLowerCase().includes('i am'), 'Summary avoids first-person "I am"', 'Avoid "I am/I have" in your summary for ATS compatibility');

  const pct = Math.round(score / tips.length * 100);
  badgeEl.className = 'ats-score-badge ' + (pct >= 80 ? 'good' : pct >= 55 ? 'ok' : 'poor');
  badgeEl.innerHTML = `<i class="fas fa-${pct>=80?'check-circle':pct>=55?'exclamation-circle':'times-circle'}"></i> ${pct}% ATS Score`;

  if (tipsEl) {
    tipsEl.innerHTML = tips.map(t => `
      <div class="ats-tip ${t.ok ? 'pass' : 'fail'}">
        <i class="fas fa-${t.ok ? 'check' : 'times'}"></i>
        <span>${t.text}</span>
      </div>`).join('');
  }
}

/* ══════════════════════════════════════════════════════════
   UNLOAD GUARD
══════════════════════════════════════════════════════════ */
window.addEventListener('beforeunload', e => {
  if (pendingChanges) { e.preventDefault(); e.returnValue = ''; }
});
