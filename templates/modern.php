<?php
// Modern template — clean two-column design
$b   = $d['basic'];
$edu = $d['education'];
$exp = $d['experience'];
$sk  = $d['skills'];
$per = $d['personal'];
$ext = $d['extras'];
$sty = $d['styling'];
$vis = $sty['sections_visible'];
$acc = htmlspecialchars($sty['accent_color'] ?? '#2563eb', ENT_QUOTES);
$fs  = (int)($sty['font_size'] ?? 14);
$ff  = htmlspecialchars($sty['font_family'] ?? 'Inter', ENT_QUOTES);
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
function nl($v){ return nl2br(h($v)); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($b['full_name']) ?> — Resume</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&family=Georgia&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html{font-size:<?= $fs ?>px}
body{font-family:'<?= $ff ?>',sans-serif;color:#1e293b;background:#fff;line-height:1.55}
.page{display:flex;min-height:297mm;max-width:210mm;margin:0 auto;background:#fff}
.sidebar{width:200px;background:<?= $acc ?>;color:#fff;flex-shrink:0;padding:28px 20px}
.main{flex:1;padding:28px 28px 28px 24px;overflow:hidden}
/* Sidebar */
.s-photo-wrap{text-align:center;margin-bottom:20px}
.s-photo{width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.4)}
.s-photo-placeholder{width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;margin:0 auto;font-size:36px;color:rgba(255,255,255,.6)}
.s-name{font-size:1em;font-weight:700;text-align:center;line-height:1.3;margin-bottom:4px;word-break:break-word}
.s-contact{margin-top:16px}
.s-section-title{font-size:.7em;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.6);border-bottom:1px solid rgba(255,255,255,.2);padding-bottom:4px;margin:16px 0 8px}
.s-item{display:flex;align-items:flex-start;gap:7px;font-size:.78em;margin-bottom:6px;word-break:break-word;color:rgba(255,255,255,.92)}
.s-item i{margin-top:2px;min-width:14px;opacity:.8;font-size:.9em}
.skill-bar-wrap{margin-bottom:8px}
.skill-bar-label{display:flex;justify-content:space-between;font-size:.75em;margin-bottom:3px;color:rgba(255,255,255,.9)}
.skill-bar{height:5px;background:rgba(255,255,255,.25);border-radius:3px;overflow:hidden}
.skill-bar-fill{height:100%;background:rgba(255,255,255,.85);border-radius:3px;transition:width .3s}
/* Main */
.m-name{font-size:2em;font-weight:800;color:#0f172a;line-height:1.1}
.m-title{font-size:1em;color:<?= $acc ?>;font-weight:500;margin-top:2px;margin-bottom:16px}
.section{margin-bottom:20px}
.section-title{font-size:.8em;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:<?= $acc ?>;border-bottom:2px solid <?= $acc ?>;padding-bottom:4px;margin-bottom:12px}
.entry{margin-bottom:12px}
.entry-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:2px}
.entry-title{font-weight:600;font-size:.88em;color:#0f172a}
.entry-subtitle{font-size:.82em;color:#475569;margin-bottom:3px}
.entry-date{font-size:.78em;color:<?= $acc ?>;font-weight:500;white-space:nowrap;margin-left:8px}
.entry-desc{font-size:.8em;color:#475569;line-height:1.6}
.obj-text{font-size:.85em;color:#334155;line-height:1.7}
.tag-list{display:flex;flex-wrap:wrap;gap:5px}
.tag{background:<?= $acc ?>18;color:<?= $acc ?>;padding:2px 8px;border-radius:4px;font-size:.72em;font-weight:500}
@media print{
  body{print-color-adjust:exact;-webkit-print-color-adjust:exact}
  .page{max-width:100%;box-shadow:none}
}
</style>
</head>
<body>
<div class="page">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="s-photo-wrap">
      <?php if (!empty($b['photo'])): ?>
        <img class="s-photo" src="<?= defined('UPLOAD_URL') ? UPLOAD_URL . h($b['photo']) : '../uploads/photos/' . h($b['photo']) ?>" alt="Photo">
      <?php else: ?>
        <div class="s-photo-placeholder">&#9786;</div>
      <?php endif; ?>
      <div class="s-name"><?= h($b['full_name'] ?: 'Your Name') ?></div>
    </div>

    <div class="s-section-title">Contact</div>
    <div class="s-contact">
      <?php if ($b['phone']): ?><div class="s-item"><span style="width:14px;display:inline-block">&#9990;</span><?= h($b['phone']) ?></div><?php endif; ?>
      <?php if ($b['email']): ?><div class="s-item"><span style="width:14px;display:inline-block">&#9993;</span><?= h($b['email']) ?></div><?php endif; ?>
      <?php if ($b['city'] || $b['state']): ?><div class="s-item"><span style="width:14px;display:inline-block">&#9679;</span><?= h(implode(', ', array_filter([$b['city'],$b['state']]))) ?></div><?php endif; ?>
      <?php if ($b['linkedin']): ?><div class="s-item"><span style="width:14px;display:inline-block">in</span><?= h(preg_replace('#https?://(www\.)?#', '', $b['linkedin'])) ?></div><?php endif; ?>
      <?php if ($b['github']): ?><div class="s-item"><span style="width:14px;display:inline-block">gh</span><?= h(preg_replace('#https?://(www\.)?#', '', $b['github'])) ?></div><?php endif; ?>
      <?php if ($b['website']): ?><div class="s-item"><span style="width:14px;display:inline-block">&#127760;</span><?= h(preg_replace('#https?://(www\.)?#', '', $b['website'])) ?></div><?php endif; ?>
    </div>

    <?php if (!empty($sk) && $vis['skills']): ?>
    <div class="s-section-title">Skills</div>
    <?php foreach ($sk as $skill): if (!$skill['name']) continue; ?>
    <div class="skill-bar-wrap">
      <div class="skill-bar-label"><span><?= h($skill['name']) ?></span></div>
      <div class="skill-bar"><div class="skill-bar-fill" style="width:<?= (int)($skill['level'] ?? 80) ?>%"></div></div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($per['languages']): ?>
    <div class="s-section-title">Languages</div>
    <div class="s-item"><?= h($per['languages']) ?></div>
    <?php endif; ?>

    <?php if ($per['hobbies']): ?>
    <div class="s-section-title">Interests</div>
    <div class="s-item"><?= h($per['hobbies']) ?></div>
    <?php endif; ?>
  </aside>

  <!-- Main -->
  <main class="main">
    <div class="m-name"><?= h($b['full_name'] ?: 'Your Name') ?></div>
    <?php if (!empty($exp)): ?><div class="m-title"><?= h($exp[0]['job_title'] ?? '') ?></div><?php endif; ?>

    <?php if ($b['objective'] && $vis['objective']): ?>
    <div class="section">
      <div class="section-title">Professional Summary</div>
      <p class="obj-text"><?= nl($b['objective']) ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($exp) && $vis['experience']): ?>
    <div class="section">
      <div class="section-title">Work Experience</div>
      <?php foreach ($exp as $e): if (!$e['job_title']) continue; ?>
      <div class="entry">
        <div class="entry-header">
          <span class="entry-title"><?= h($e['job_title']) ?></span>
          <span class="entry-date"><?= h($e['date_from']) ?><?= $e['date_from']?' – ':'' ?><?= !empty($e['is_current'])?'Present':h($e['date_to']) ?></span>
        </div>
        <div class="entry-subtitle"><?= h($e['company']) ?><?= $e['location'] ? ', '.h($e['location']) : '' ?><?= $e['employment_type'] ? ' · '.h($e['employment_type']) : '' ?></div>
        <?php if ($e['description']): ?><div class="entry-desc"><?= nl($e['description']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($edu) && $vis['education']): ?>
    <div class="section">
      <div class="section-title">Education</div>
      <?php foreach ($edu as $e): if (!$e['degree']) continue; ?>
      <div class="entry">
        <div class="entry-header">
          <span class="entry-title"><?= h($e['degree']) ?><?= $e['field']?' — '.h($e['field']):'' ?></span>
          <span class="entry-date"><?= h($e['year_from']) ?><?= $e['year_from']?' – ':'' ?><?= h($e['year_to']) ?></span>
        </div>
        <div class="entry-subtitle"><?= h($e['institution']) ?><?= $e['university']?' · '.h($e['university']):'' ?><?= $e['grade']?' · '.h($e['grade']):'' ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($ext['certifications']) && $vis['certifications']): ?>
    <div class="section">
      <div class="section-title">Certifications</div>
      <?php foreach ($ext['certifications'] as $c): if (!$c['name']) continue; ?>
      <div class="entry">
        <div class="entry-header">
          <span class="entry-title"><?= h($c['name']) ?></span>
          <?php if ($c['date']): ?><span class="entry-date"><?= h($c['date']) ?></span><?php endif; ?>
        </div>
        <?php if ($c['issuer']): ?><div class="entry-subtitle"><?= h($c['issuer']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($ext['projects']) && $vis['projects']): ?>
    <div class="section">
      <div class="section-title">Projects</div>
      <?php foreach ($ext['projects'] as $p): if (!$p['name']) continue; ?>
      <div class="entry">
        <div class="entry-title"><?= h($p['name']) ?><?= $p['role']?' — '.h($p['role']):'' ?></div>
        <?php if ($p['tech_stack']): ?><div class="entry-subtitle"><?= h($p['tech_stack']) ?></div><?php endif; ?>
        <?php if ($p['description']): ?><div class="entry-desc"><?= nl($p['description']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($ext['achievements']) && $vis['achievements']): ?>
    <div class="section">
      <div class="section-title">Achievements</div>
      <ul style="padding-left:16px">
        <?php foreach ($ext['achievements'] as $a): if (!$a['title']) continue; ?>
        <li class="entry-desc"><?= h($a['title']) ?><?= $a['year']?' ('.$a['year'].')':'' ?><?= $a['description']?' — '.$a['description']:'' ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <?php if (!empty($ext['references']) && $vis['references']): ?>
    <div class="section">
      <div class="section-title">References</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <?php foreach ($ext['references'] as $r): if (!$r['name']) continue; ?>
        <div class="entry">
          <div class="entry-title"><?= h($r['name']) ?></div>
          <div class="entry-subtitle"><?= h($r['designation']) ?><?= $r['company']?', '.h($r['company']):'' ?></div>
          <?php if ($r['email']): ?><div class="entry-desc"><?= h($r['email']) ?></div><?php endif; ?>
          <?php if ($r['phone']): ?><div class="entry-desc"><?= h($r['phone']) ?></div><?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </main>
</div>
</body>
</html>
