<?php
// Classic template — traditional single-column professional style
$b   = $d['basic'];
$edu = $d['education'];
$exp = $d['experience'];
$sk  = $d['skills'];
$per = $d['personal'];
$ext = $d['extras'];
$sty = $d['styling'];
$vis = $sty['sections_visible'];
$acc = htmlspecialchars($sty['accent_color'] ?? '#1e40af', ENT_QUOTES);
$fs  = (int)($sty['font_size'] ?? 14);
$ff  = htmlspecialchars($sty['font_family'] ?? 'Georgia', ENT_QUOTES);
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
function nl($v){ return nl2br(h($v)); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= h($b['full_name']) ?> — CV</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html{font-size:<?= $fs ?>px}
body{font-family:'<?= $ff ?>',Georgia,serif;color:#1e293b;background:#fff;line-height:1.6}
.page{max-width:210mm;margin:0 auto;padding:32px 40px;background:#fff}
.header{text-align:center;border-bottom:3px solid <?= $acc ?>;padding-bottom:16px;margin-bottom:20px}
.h-name{font-size:2.1em;font-weight:700;letter-spacing:.02em;color:#0f172a}
.h-contact{display:flex;justify-content:center;flex-wrap:wrap;gap:12px 20px;margin-top:8px;font-size:.8em;color:#475569}
.h-contact span{display:flex;align-items:center;gap:4px}
.section{margin-bottom:18px}
.sec-title{font-size:.85em;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:<?= $acc ?>;margin-bottom:8px;padding-bottom:3px;border-bottom:1px solid <?= $acc ?>44}
.entry{display:grid;grid-template-columns:1fr auto;gap:2px 16px;margin-bottom:10px}
.entry-main{grid-column:1}
.entry-date{font-size:.78em;color:<?= $acc ?>;font-weight:600;white-space:nowrap;text-align:right;padding-top:2px}
.entry-title{font-weight:600;font-size:.9em;color:#0f172a}
.entry-sub{font-size:.82em;color:#475569;font-style:italic}
.entry-desc{font-size:.82em;color:#334155;margin-top:4px;line-height:1.65}
.obj-text{font-size:.87em;color:#334155;line-height:1.75;text-align:justify}
.skills-wrap{display:flex;flex-wrap:wrap;gap:5px}
.skill-chip{padding:3px 10px;background:<?= $acc ?>15;border:1px solid <?= $acc ?>44;border-radius:3px;font-size:.78em;color:<?= $acc ?>}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:8px 24px}
@media print{body{print-color-adjust:exact;-webkit-print-color-adjust:exact}.page{padding:20px 28px}}
</style>
</head>
<body>
<div class="page">
  <div class="header">
    <div class="h-name"><?= h($b['full_name'] ?: 'Your Name') ?></div>
    <div class="h-contact">
      <?php if ($b['phone']): ?><span>📞 <?= h($b['phone']) ?></span><?php endif; ?>
      <?php if ($b['email']): ?><span>✉ <?= h($b['email']) ?></span><?php endif; ?>
      <?php if ($b['city']||$b['state']): ?><span>📍 <?= h(implode(', ',array_filter([$b['city'],$b['state']]))) ?></span><?php endif; ?>
      <?php if ($b['linkedin']): ?><span>🔗 <?= h(preg_replace('#https?://(www\.)?#','',$b['linkedin'])) ?></span><?php endif; ?>
      <?php if ($b['github']): ?><span>💻 <?= h(preg_replace('#https?://(www\.)?#','',$b['github'])) ?></span><?php endif; ?>
      <?php if ($b['website']): ?><span>🌐 <?= h(preg_replace('#https?://(www\.)?#','',$b['website'])) ?></span><?php endif; ?>
    </div>
  </div>

  <?php if ($b['objective'] && $vis['objective']): ?>
  <div class="section">
    <div class="sec-title">Professional Summary</div>
    <p class="obj-text"><?= nl($b['objective']) ?></p>
  </div>
  <?php endif; ?>

  <?php if (!empty($exp) && $vis['experience']): ?>
  <div class="section">
    <div class="sec-title">Work Experience</div>
    <?php foreach ($exp as $e): if (!$e['job_title']) continue; ?>
    <div class="entry">
      <div class="entry-main">
        <div class="entry-title"><?= h($e['job_title']) ?> — <span style="font-weight:normal"><?= h($e['company']) ?></span><?= $e['location']?', '.h($e['location']):'' ?></div>
        <?php if ($e['employment_type']): ?><div class="entry-sub"><?= h($e['employment_type']) ?></div><?php endif; ?>
        <?php if ($e['description']): ?><div class="entry-desc"><?= nl($e['description']) ?></div><?php endif; ?>
      </div>
      <div class="entry-date"><?= h($e['date_from']) ?><?= $e['date_from']?'–':'' ?><?= !empty($e['is_current'])?'Present':h($e['date_to']) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($edu) && $vis['education']): ?>
  <div class="section">
    <div class="sec-title">Education</div>
    <?php foreach ($edu as $e): if (!$e['degree']) continue; ?>
    <div class="entry">
      <div class="entry-main">
        <div class="entry-title"><?= h($e['degree']) ?></div>
        <div class="entry-sub"><?= h($e['institution']) ?><?= $e['university']?' · '.h($e['university']):'' ?><?= $e['grade']?' · '.h($e['grade']):'' ?></div>
      </div>
      <div class="entry-date"><?= h($e['year_from']) ?><?= $e['year_from']?'–':'' ?><?= h($e['year_to']) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($sk) && $vis['skills']): ?>
  <div class="section">
    <div class="sec-title">Skills</div>
    <div class="skills-wrap">
      <?php foreach ($sk as $s): if (!$s['name']) continue; ?>
      <span class="skill-chip"><?= h($s['name']) ?></span>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if (!empty($ext['certifications']) && $vis['certifications']): ?>
  <div class="section">
    <div class="sec-title">Certifications</div>
    <?php foreach ($ext['certifications'] as $c): if (!$c['name']) continue; ?>
    <div class="entry">
      <div class="entry-main"><div class="entry-title"><?= h($c['name']) ?></div><?php if ($c['issuer']): ?><div class="entry-sub"><?= h($c['issuer']) ?></div><?php endif; ?></div>
      <?php if ($c['date']): ?><div class="entry-date"><?= h($c['date']) ?></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($ext['projects']) && $vis['projects']): ?>
  <div class="section">
    <div class="sec-title">Projects</div>
    <?php foreach ($ext['projects'] as $p): if (!$p['name']) continue; ?>
    <div class="entry" style="display:block;margin-bottom:10px">
      <div class="entry-title"><?= h($p['name']) ?><?= $p['role']?' — '.h($p['role']):'' ?></div>
      <?php if ($p['tech_stack']): ?><div class="entry-sub"><?= h($p['tech_stack']) ?></div><?php endif; ?>
      <?php if ($p['description']): ?><div class="entry-desc"><?= nl($p['description']) ?></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($ext['achievements']) && $vis['achievements']): ?>
  <div class="section">
    <div class="sec-title">Achievements</div>
    <ul style="padding-left:18px">
      <?php foreach ($ext['achievements'] as $a): if (!$a['title']) continue; ?>
      <li style="font-size:.83em;color:#334155;margin-bottom:4px"><?= h($a['title']) ?><?= $a['year']?' ('.$a['year'].')':'' ?><?= $a['description']?': '.h($a['description']):'' ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <?php if (!empty($ext['references']) && $vis['references']): ?>
  <div class="section">
    <div class="sec-title">References</div>
    <div class="two-col">
      <?php foreach ($ext['references'] as $r): if (!$r['name']) continue; ?>
      <div>
        <div class="entry-title"><?= h($r['name']) ?></div>
        <div class="entry-sub"><?= h($r['designation']) ?><?= $r['company']?', '.h($r['company']):'' ?></div>
        <?php if ($r['email']): ?><div style="font-size:.78em;color:#475569"><?= h($r['email']) ?></div><?php endif; ?>
        <?php if ($r['phone']): ?><div style="font-size:.78em;color:#475569"><?= h($r['phone']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
