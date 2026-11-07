/* ═══════════════════════════════════════════════════════════
   ResumeForge — Global App JS
   Toast · Mobile Sidebar · Session Warning · Keyboard Shortcuts
═══════════════════════════════════════════════════════════ */
'use strict';

/* ════════════════════════════════════════════════════════════
   TOAST ENGINE
════════════════════════════════════════════════════════════ */
const Toast = (() => {
  const icons = {
    success: 'fas fa-check-circle',
    error:   'fas fa-times-circle',
    warning: 'fas fa-exclamation-triangle',
    info:    'fas fa-info-circle',
  };

  function getContainer() {
    let c = document.getElementById('toast-container');
    if (!c) {
      c = document.createElement('div');
      c.id = 'toast-container';
      document.body.appendChild(c);
    }
    return c;
  }

  function show(message, type = 'info', duration = 4500) {
    const container = getContainer();
    const id  = 'toast-' + Date.now();
    const el  = document.createElement('div');
    el.className = `toast toast-${type}`;
    el.id = id;
    el.innerHTML = `
      <div class="toast-icon"><i class="${icons[type] || icons.info}"></i></div>
      <div class="toast-body">
        <div class="toast-title">${_title(type)}</div>
        <div class="toast-msg">${_esc(message)}</div>
      </div>
      <button class="toast-close" onclick="Toast.dismiss('${id}')">
        <i class="fas fa-times"></i>
      </button>
      <div class="toast-progress">
        <div class="toast-progress-bar" style="width:100%"></div>
      </div>`;
    container.appendChild(el);

    // Animate in
    requestAnimationFrame(() => {
      requestAnimationFrame(() => el.classList.add('show'));
    });

    // Shrink progress bar
    const bar = el.querySelector('.toast-progress-bar');
    if (bar) {
      bar.style.transition = `width ${duration}ms linear`;
      requestAnimationFrame(() => { requestAnimationFrame(() => { bar.style.width = '0%'; }); });
    }

    // Auto-dismiss
    const timer = setTimeout(() => dismiss(id), duration);
    el._timer = timer;
    return id;
  }

  function dismiss(id) {
    const el = document.getElementById(id);
    if (!el) return;
    clearTimeout(el._timer);
    el.classList.remove('show');
    el.classList.add('hide');
    setTimeout(() => el.remove(), 300);
  }

  function _title(type) {
    return { success:'Success', error:'Error', warning:'Warning', info:'Info' }[type] || 'Info';
  }

  function _esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  return { show, dismiss,
    success: (m, d) => show(m, 'success', d),
    error:   (m, d) => show(m, 'error',   d),
    warning: (m, d) => show(m, 'warning', d),
    info:    (m, d) => show(m, 'info',    d),
  };
})();

// Global convenience (replaces alert() in old code)
window.Toast = Toast;

/* ════════════════════════════════════════════════════════════
   MOBILE SIDEBAR
════════════════════════════════════════════════════════════ */
const MobileSidebar = (() => {
  function init() {
    const sidebar  = document.querySelector('.sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    const hamburger= document.getElementById('hamburger-btn');
    if (!sidebar) return;

    function open() {
      sidebar.classList.add('open');
      if (overlay) overlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
    function close() {
      sidebar.classList.remove('open');
      if (overlay) overlay.classList.remove('show');
      document.body.style.overflow = '';
    }

    hamburger?.addEventListener('click', () =>
      sidebar.classList.contains('open') ? close() : open()
    );
    overlay?.addEventListener('click', close);

    // Close on nav item click (mobile)
    sidebar.querySelectorAll('.nav-item').forEach(item =>
      item.addEventListener('click', () => { if (window.innerWidth < 768) close(); })
    );
  }
  return { init };
})();

/* ════════════════════════════════════════════════════════════
   SESSION EXPIRY WARNING
   PHP session lifetime is 7 days, but we show a UX warning
   after 50 minutes of inactivity to prompt the user to save.
════════════════════════════════════════════════════════════ */
const SessionGuard = (() => {
  const WARN_AFTER  = 50 * 60 * 1000;  // 50 min idle → warn
  const EXPIRE_AFTER= 60 * 60 * 1000;  // 60 min idle → redirect to login
  let warnTimer, expireTimer;

  function reset() {
    clearTimeout(warnTimer);
    clearTimeout(expireTimer);
    document.getElementById('session-warning')?.classList.remove('show');
    warnTimer   = setTimeout(warn,   WARN_AFTER);
    expireTimer = setTimeout(expire, EXPIRE_AFTER);
  }

  function warn() {
    const el = document.getElementById('session-warning');
    if (el) el.classList.add('show');
  }

  function expire() {
    window.location.href = (window.APP_URL || '') + '/index.php';
  }

  function init() {
    // Only run when user is logged in (page has APP_URL set)
    if (!window.APP_URL) return;
    ['click','keydown','mousemove','scroll','touchstart'].forEach(e =>
      document.addEventListener(e, reset, { passive: true })
    );
    reset();
  }

  return { init, reset };
})();

/* ════════════════════════════════════════════════════════════
   CHAR COUNTER  (attach to any textarea with data-maxlen)
════════════════════════════════════════════════════════════ */
function initCharCounters() {
  document.querySelectorAll('textarea[data-maxlen], input[data-maxlen]').forEach(el => {
    const max = parseInt(el.getAttribute('data-maxlen'));
    const wrap = el.parentElement;
    if (!wrap) return;
    wrap.style.position = 'relative';

    const counter = document.createElement('span');
    counter.className = 'char-count';
    wrap.appendChild(counter);

    function update() {
      const len = el.value.length;
      counter.textContent = `${len}/${max}`;
      counter.className = 'char-count' + (len > max ? ' over' : len > max * .85 ? ' warn' : '');
    }
    el.addEventListener('input', update);
    update();
  });
}

/* ════════════════════════════════════════════════════════════
   CONFIRM DIALOG (nicer than browser confirm())
════════════════════════════════════════════════════════════ */
function confirmDialog(message, onConfirm, { title = 'Are you sure?', danger = true } = {}) {
  const existing = document.getElementById('confirm-modal');
  if (existing) existing.remove();

  const overlay = document.createElement('div');
  overlay.className = 'modal-overlay';
  overlay.id = 'confirm-modal';
  overlay.innerHTML = `
    <div class="modal" style="max-width:420px">
      <div class="modal-header">
        <span class="modal-title" style="${danger?'color:#dc2626':''}">
          <i class="fas fa-${danger?'exclamation-triangle':'question-circle'}" style="margin-right:7px"></i>${_esc(title)}
        </span>
        <button class="modal-close" id="cfm-cancel-x"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body" style="padding-top:16px;padding-bottom:16px">
        <p style="font-size:.875rem;color:#475569;line-height:1.6">${_esc(message)}</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-ghost btn-sm" id="cfm-cancel">Cancel</button>
        <button class="btn ${danger?'btn-danger':'btn-primary'} btn-sm" id="cfm-ok">
          ${danger ? '<i class="fas fa-trash"></i> Delete' : '<i class="fas fa-check"></i> Confirm'}
        </button>
      </div>
    </div>`;
  document.body.appendChild(overlay);
  requestAnimationFrame(() => overlay.classList.add('open'));

  function close() { overlay.classList.remove('open'); setTimeout(() => overlay.remove(), 200); }
  overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
  overlay.querySelector('#cfm-cancel')?.addEventListener('click', close);
  overlay.querySelector('#cfm-cancel-x')?.addEventListener('click', close);
  overlay.querySelector('#cfm-ok')?.addEventListener('click', () => { close(); onConfirm(); });

  function _esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }
}
window.confirmDialog = confirmDialog;

/* ════════════════════════════════════════════════════════════
   BUTTON LOADING STATE
════════════════════════════════════════════════════════════ */
function btnLoading(btn, loading = true) {
  if (!btn) return;
  if (loading) {
    btn._origHTML = btn.innerHTML;
    btn._origDisabled = btn.disabled;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + (btn._loadingText || 'Please wait…');
    btn.style.opacity = '.7';
  } else {
    btn.disabled = btn._origDisabled || false;
    btn.innerHTML = btn._origHTML || btn.innerHTML;
    btn.style.opacity = '';
  }
}
window.btnLoading = btnLoading;

/* ════════════════════════════════════════════════════════════
   DRAG & DROP PHOTO UPLOAD
════════════════════════════════════════════════════════════ */
function initDropZone(areaId, inputId, onFile) {
  const area  = document.getElementById(areaId);
  const input = document.getElementById(inputId);
  if (!area || !input) return;

  ['dragenter','dragover'].forEach(e =>
    area.addEventListener(e, ev => { ev.preventDefault(); area.classList.add('drag-over'); })
  );
  ['dragleave','drop'].forEach(e =>
    area.addEventListener(e, ev => { ev.preventDefault(); area.classList.remove('drag-over'); })
  );
  area.addEventListener('drop', ev => {
    const file = ev.dataTransfer.files[0];
    if (file) onFile(file);
  });
  area.addEventListener('click', () => input.click());
}
window.initDropZone = initDropZone;

/* ════════════════════════════════════════════════════════════
   AUTO-DISMISS FLASH ALERTS
════════════════════════════════════════════════════════════ */
function initFlashAlerts() {
  document.querySelectorAll('.alert[id]').forEach(el => {
    setTimeout(() => {
      el.style.transition = 'opacity .4s ease';
      el.style.opacity    = '0';
      setTimeout(() => el.remove(), 400);
    }, 4000);
  });
}

/* ════════════════════════════════════════════════════════════
   INIT
════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  MobileSidebar.init();
  SessionGuard.init();
  initCharCounters();
  initFlashAlerts();
});
