<?php
// Indian Traditional — The classic Indian resume format
// Bordered page, teal section bars, arrow bullets, education table, declaration
$b  = $d['basic'];
$edu = $d['education'];
$exp = $d['experience'];
$sk  = $d['skills'];
$per = $d['personal'];
$ext = $d['extras'];
$sty = $d['styling'];
$vis = $sty['sections_visible'];
$acc = htmlspecialchars($sty['accent_color'] ?? '#1a6b6b', ENT_QUOTES);
$fs  = (int)($sty['font_size'] ?? 14);
$ff  = htmlspecialchars($sty['font_family'] ?? 'Times New Roman', ENT_QUOTES);
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
function nl2($v) { return nl2br(h($v)); }
$photoSrc = !empty($b['photo']) ? (defined('UPLOAD_URL') ? UPLOAD_URL . h($b['photo']) : '../uploads/photos/' . h($b['photo'])) : '';
$age = $per['dob'] ? (int)((time() - strtotime($per['dob'])) / (365.25 * 86400)) : ($per['age'] ?? '');

// Merge skills text (from skills array or hobbies)
$skillLines = array_filter(array_column($sk, 'name'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Resume — <?= h($b['full_name']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
html { font-size: <?= $fs ?>px; }
body {
  font-family: '<?= $ff ?>', 'Times New Roman', Times, serif;
  background: #fff;
  color: #000;
  line-height: 1.55;
  -webkit-print-color-adjust: exact;
  print-color-adjust: exact;
}

/* ── Outer page border ── */
.page {
  max-width: 210mm;
  margin: 0 auto;
  min-height: 297mm;
  border: 3px double #000;
  padding: 18px 22px 24px;
}

/* ── Document title ── */
.doc-title {
  text-align: center;
  font-size: 1.5em;
  font-weight: bold;
  text-decoration: underline;
  margin-bottom: 14px;
  letter-spacing: .02em;
}

/* ── Header: name + contact ── */
.hdr-table {
  width: 100%;
  margin-bottom: 12px;
  border-collapse: collapse;
}
.hdr-table td {
  padding: 0;
  vertical-align: top;
  font-size: .9em;
}
.hdr-table td:last-child { text-align: right; }
.hdr-name { font-weight: bold; font-size: 1em; }

/* ── Section bar ── */
.sec-bar {
  background: <?= $acc ?>;
  color: #fff;
  font-weight: bold;
  font-size: .9em;
  text-transform: uppercase;
  letter-spacing: .06em;
  padding: 5px 10px;
  margin: 14px 0 10px;
  display: flex;
  align-items: center;
}

/* ── Arrow bullets ── */
.arrow-list {
  list-style: none;
  padding: 0 0 0 8px;
  margin: 0;
}
.arrow-list li {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin-bottom: 5px;
  font-size: .88em;
  line-height: 1.6;
}
.arrow-list li::before {
  content: '➤';
  color: <?= $acc ?>;
  font-size: .85em;
  flex-shrink: 0;
  margin-top: 2px;
}

/* ── Education table ── */
.edu-table {
  width: 100%;
  border-collapse: collapse;
  font-size: .86em;
  margin-bottom: 4px;
}
.edu-table th {
  background: <?= $acc ?>22;
  border: 1px solid #aaa;
  padding: 5px 8px;
  font-weight: bold;
  text-align: center;
  color: #1a1a1a;
}
.edu-table td {
  border: 1px solid #aaa;
  padding: 5px 8px;
  text-align: center;
  vertical-align: middle;
}
.edu-table td:nth-child(3) { text-align: left; }
.edu-table td:nth-child(1) { text-align: center; font-weight: 500; }

/* ── Personal details ── */
.personal-table {
  width: 100%;
  font-size: .88em;
  border-collapse: collapse;
}
.personal-table tr td {
  padding: 4px 6px;
  vertical-align: top;
}
.personal-table td:first-child {
  width: 130px;
  font-weight: 500;
  color: #1a1a1a;
  white-space: nowrap;
}
.personal-table td:nth-child(2) {
  width: 12px;
  color: #333;
  text-align: center;
}
.personal-table .photo-cell {
  vertical-align: top;
  padding: 0 0 0 12px;
  text-align: right;
  width: 110px;
}
.personal-photo {
  width: 90px;
  height: 110px;
  object-fit: cover;
  border: 1px solid #aaa;
  display: block;
}
.personal-photo-box {
  width: 90px;
  height: 110px;
  border: 1px solid #aaa;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: .72em;
  color: #999;
  text-align: center;
  padding: 6px;
}

/* ── Objective / paragraph text ── */
.para-text {
  font-size: .88em;
  line-height: 1.7;
  text-align: justify;
  padding: 2px 4px;
}

/* ── Declaration ── */
.declaration-text {
  font-size: .88em;
  line-height: 1.7;
  margin-bottom: 24px;
}
.sign-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  font-size: .88em;
  margin-top: 28px;
}
.sign-left { line-height: 1.8; }
.sign-right { text-align: right; }

/* ── Work experience table ── */
.work-table {
  width: 100%;
  border-collapse: collapse;
  font-size: .86em;
  margin-bottom: 4px;
}
.work-table td {
  padding: 4px 8px;
  vertical-align: top;
  border-bottom: 1px dashed #ddd;
}
.work-table tr:last-child td { border-bottom: none; }
.work-table td:first-child { width: 100px; font-weight: 600; color: #1a1a1a; white-space: nowrap; }
.work-table td:nth-child(2) { width: 14px; color: #555; }

@media print {
  body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
  .page { border: 3px double #000; max-width: 100%; min-height: auto; }
}
</style>
</head>
<body>
<div class="page">

  <!-- Title -->
  <div class="doc-title">Resume</div>

  <!-- Header -->
  <table class="hdr-table">
    <tr>
      <td>
        Name : <span class="hdr-name"><?= h($b['full_name'] ?: 'Full Name') ?></span><br>
        <?php if ($b['email']): ?>E-mail: <strong><?= h($b['email']) ?></strong><?php endif; ?>
      </td>
      <td>
        <?php if ($b['phone']): ?>Mobile: <strong><?= h($b['phone']) ?></strong><?php endif; ?>
        <?php if ($b['city'] || $b['state']): ?><br><?= h(implode(', ', array_filter([$b['city'], $b['state'], $b['country']]))) ?><?php endif; ?>
        <?php if ($b['linkedin']): ?><br><?= h(preg_replace('#https?://(www\.)?#', '', $b['linkedin'])) ?><?php endif; ?>
      </td>
    </tr>
  </table>

  <!-- Objective -->
  <?php if ($b['objective'] && $vis['objective']): ?>
  <div class="sec-bar">Objective</div>
  <p class="para-text"><?= nl2($b['objective']) ?></p>
  <?php endif; ?>

  <!-- Technical Knowledge / Skills -->
  <?php if (!empty($skillLines) && $vis['skills']): ?>
  <div class="sec-bar">Technical Knowledge</div>
  <ul class="arrow-list">
    <?php
    // Group skills into lines of ~3
    $chunks = array_chunk($skillLines, 3);
    foreach ($chunks as $chunk):
    ?>
    <li><?= h(implode(', ', $chunk)) ?></li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>

  <!-- Academics / Education -->
  <?php if (!empty($edu) && $vis['education']): ?>
  <div class="sec-bar">Academics Information</div>
  <table class="edu-table">
    <thead>
      <tr>
        <th>Qualification</th>
        <th>Board / University</th>
        <th>Institution</th>
        <th>Year</th>
        <th>Percentage / Grade</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($edu as $e): if (!$e['degree']) continue; ?>
      <tr>
        <td><?= h($e['degree']) ?><?= $e['field'] ? '<br><small>' . h($e['field']) . '</small>' : '' ?></td>
        <td><?= h($e['university'] ?: $e['institution']) ?></td>
        <td><?= h($e['institution']) ?><?= $e['description'] ? '<br><small>' . h($e['description']) . '</small>' : '' ?></td>
        <td><?= h($e['year_to'] ?: $e['year_from']) ?></td>
        <td><?= h($e['grade'] ?: '—') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <!-- Work Experience -->
  <?php if (!empty($exp) && $vis['experience']): ?>
  <div class="sec-bar">Work Experience</div>
  <ul class="arrow-list">
    <?php foreach ($exp as $e): if (!$e['job_title']) continue; ?>
    <li>
      <?php if ($e['description']): ?>
        <?= nl2($e['description']) ?>
        <?php if ($e['company'] || $e['date_from'] || $e['date_to']): ?>
        <br><em><?= h($e['job_title']) ?><?= $e['company'] ? ' at <strong>' . h($e['company']) . '</strong>' : '' ?><?= $e['date_from'] ? ', ' . h($e['date_from']) . ($e['date_to'] ? '–' . h($e['date_to']) : '') : '' ?></em>
        <?php endif; ?>
      <?php else: ?>
        <?= $e['date_from'] || $e['date_to'] ? 'Worked as <strong>'.h($e['job_title']).'</strong>' : h($e['job_title']) ?>
        <?= $e['company'] ? ' at <strong>' . h($e['company']) . '</strong>' : '' ?>
        <?= $e['location'] ? ', ' . h($e['location']) : '' ?>
        <?= $e['date_from'] ? ' (' . h($e['date_from']) . (!empty($e['is_current']) ? '–Present' : ($e['date_to'] ? '–' . h($e['date_to']) : '')) . ')' : '' ?>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>

  <!-- Certifications / Co-Curricular -->
  <?php if (!empty($ext['certifications']) && $vis['certifications']): ?>
  <div class="sec-bar">Co-Curricular Activities</div>
  <ul class="arrow-list">
    <?php foreach ($ext['certifications'] as $c): if (!$c['name']) continue; ?>
    <li>Completed <?= $c['issuer'] ? 'certification course in "<strong>' . h($c['name']) . '</strong>" conducted by ' . h($c['issuer']) : '"<strong>' . h($c['name']) . '</strong>"' ?><?= $c['date'] ? '. (' . h($c['date']) . ')' : '' ?></li>
    <?php endforeach; ?>
    <?php foreach ($ext['achievements'] as $a): if (!$a['title']) continue; ?>
    <li><?= h($a['title']) ?><?= $a['description'] ? ' — ' . h($a['description']) : '' ?><?= $a['year'] ? ' (' . h($a['year']) . ')' : '' ?></li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>

  <!-- Projects -->
  <?php if (!empty($ext['projects']) && $vis['projects']): ?>
  <div class="sec-bar">Projects</div>
  <ul class="arrow-list">
    <?php foreach ($ext['projects'] as $p): if (!$p['name']) continue; ?>
    <li>
      <strong><?= h($p['name']) ?></strong><?= $p['role'] ? ' (' . h($p['role']) . ')' : '' ?>
      <?= $p['tech_stack'] ? ' — Tech: ' . h($p['tech_stack']) : '' ?>
      <?= $p['description'] ? '<br>' . nl2($p['description']) : '' ?>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>

  <!-- Skills & Interests -->
  <?php if ($per['hobbies'] || !empty($ext['achievements'])): ?>
  <div class="sec-bar">Skills and Interests</div>
  <ul class="arrow-list">
    <?php if ($per['hobbies']): foreach (array_filter(array_map('trim', explode(',', $per['hobbies']))) as $hb): ?>
    <li><?= h($hb) ?></li>
    <?php endforeach; endif; ?>
    <?php if ($per['languages']): ?>
    <li>Languages known: <?= h($per['languages']) ?></li>
    <?php endif; ?>
  </ul>
  <?php endif; ?>

  <!-- Personal Details -->
  <?php
  $hasPersonal = array_filter([$per['father_name'], $per['mother_name'], $per['dob'], $per['gender'],
    $per['marital_status'], $per['nationality'], $per['languages'], $b['address']]);
  ?>
  <?php if ($hasPersonal && $vis['personal']): ?>
  <div class="sec-bar">Personal Details</div>
  <table style="width:100%;border-collapse:collapse">
    <tr>
      <td style="vertical-align:top">
        <table class="personal-table">
          <?php
          $prows = [
            'Father Name'    => $per['father_name'],
            'Mother Name'    => $per['mother_name'],
            'Date of Birth'  => $per['dob'] ? date('jS M Y', strtotime($per['dob'])) : '',
            'Age'            => $age ? $age . ' Years' : '',
            'Gender'         => $per['gender'],
            'Marital Status' => $per['marital_status'],
            'Religion'       => $per['religion'],
            'Caste'          => $per['caste'],
            'Nationality'    => $per['nationality'],
            'Height'         => $per['height'],
            'Weight'         => $per['weight'],
            'Languages Known'=> $per['languages'],
            'Address'        => implode(', ', array_filter([$b['address'], $b['city'], $b['state'], $b['country']])),
          ];
          foreach ($prows as $label => $val): if (!$val) continue; ?>
          <tr>
            <td><?= h($label) ?></td>
            <td>:</td>
            <td><?= h($val) ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </td>
      <?php if ($photoSrc || true): ?>
      <td class="photo-cell" style="vertical-align:top;padding:0 0 0 12px;text-align:right;width:110px">
        <?php if ($photoSrc): ?>
          <img class="personal-photo" src="<?= $photoSrc ?>" alt="Photo">
        <?php else: ?>
          <div class="personal-photo-box">Affix Photo Here</div>
        <?php endif; ?>
      </td>
      <?php endif; ?>
    </tr>
  </table>
  <?php endif; ?>

  <!-- Declaration -->
  <div class="sec-bar">Declaration</div>
  <p class="declaration-text">
    I hereby declare that the particulars given above are true and correct to the best of my knowledge and belief.
    I promise to always be an asset to my work and organisation.
  </p>
  <div class="sign-row">
    <div class="sign-left">
      Date: &nbsp;&nbsp; <?= date('jS M Y') ?><br>
      Place: &nbsp;<?= h($b['city'] ?: '_____________') ?>
    </div>
    <div class="sign-right">
      Signature<br>
      <br>
      <?= h($b['full_name']) ?>
    </div>
  </div>

</div>
</body>
</html>
