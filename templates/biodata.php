<?php
// Biodata template — Traditional format (marriage / government)
$b   = $d['basic'];
$edu = $d['education'];
$exp = $d['experience'];
$sk  = $d['skills'];
$per = $d['personal'];
$ext = $d['extras'];
$sty = $d['styling'];
$vis = $sty['sections_visible'];
$acc = htmlspecialchars($sty['accent_color'] ?? '#7c2d12', ENT_QUOTES);
$fs  = (int)($sty['font_size'] ?? 13);
$ff  = htmlspecialchars($sty['font_family'] ?? 'Georgia', ENT_QUOTES);
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
$age = $per['dob'] ? (int)((time() - strtotime($per['dob'])) / (365.25 * 86400)) : ($per['age'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Biodata — <?= h($b['full_name']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html{font-size:<?= $fs ?>px}
body{font-family:'<?= $ff ?>',serif;background:#fff;color:#1e293b;line-height:1.7}
.page{max-width:210mm;margin:0 auto;padding:28px 36px;background:#fff;border:2px solid <?= $acc ?>}
.bd-header{text-align:center;background:<?= $acc ?>;color:#fff;padding:20px 24px;margin:-28px -36px 24px;position:relative}
.bd-header h1{font-size:1.4em;font-weight:700;letter-spacing:.05em;text-transform:uppercase}
.bd-header p{font-size:.82em;opacity:.85;margin-top:2px}
.photo-box{position:absolute;right:24px;top:50%;transform:translateY(-50%)}
.photo-box img{width:90px;height:110px;object-fit:cover;border:3px solid rgba(255,255,255,.5);display:block}
.photo-placeholder{width:90px;height:110px;background:rgba(255,255,255,.15);border:2px dashed rgba(255,255,255,.4);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.6);font-size:.72em;text-align:center;padding:8px}
.section{margin-bottom:18px}
.sec-title{background:<?= $acc ?>22;color:<?= $acc ?>;font-size:.8em;font-weight:700;text-transform:uppercase;letter-spacing:.1em;padding:5px 12px;border-left:4px solid <?= $acc ?>;margin-bottom:10px}
.info-table{width:100%;border-collapse:collapse}
.info-table tr td{padding:5px 8px;font-size:.85em;vertical-align:top}
.info-table tr td:first-child{font-weight:600;color:#374151;width:38%;white-space:nowrap}
.info-table tr td:nth-child(2){color:#374151;width:4%;text-align:center}
.info-table tr:nth-child(even) td{background:#f8fafc}
.edu-entry,.exp-entry{margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid #e2e8f0}
.edu-entry:last-child,.exp-entry:last-child{border-bottom:none}
.edu-deg{font-weight:600;font-size:.88em;color:#0f172a}
.edu-inst{font-size:.8em;color:#475569}
.sign-area{display:flex;justify-content:space-between;margin-top:28px;padding-top:16px;border-top:1px solid <?= $acc ?>44}
.sign-box{text-align:center;font-size:.78em;color:#374151}
.sign-line{width:120px;border-top:1px solid #374151;margin:28px auto 4px}
@media print{body{print-color-adjust:exact;-webkit-print-color-adjust:exact}}
</style>
</head>
<body>
<div class="page">
  <!-- Header -->
  <div class="bd-header">
    <div class="photo-box">
      <?php if (!empty($b['photo'])): ?>
        <img src="<?= defined('UPLOAD_URL') ? UPLOAD_URL . h($b['photo']) : '../uploads/photos/' . h($b['photo']) ?>" alt="Photo">
      <?php else: ?>
        <div class="photo-placeholder">Attach Photo Here</div>
      <?php endif; ?>
    </div>
    <h1>Biodata</h1>
    <p><?= h($b['full_name'] ?: 'Full Name') ?></p>
  </div>

  <!-- Personal Details -->
  <div class="section">
    <div class="sec-title">Personal Details</div>
    <table class="info-table">
      <?php
      $rows = [
        'Full Name'       => $b['full_name'],
        'Date of Birth'   => $per['dob'] ? date('d F Y', strtotime($per['dob'])) : '',
        'Age'             => $age ? $age . ' years' : '',
        'Gender'          => $per['gender'],
        'Marital Status'  => $per['marital_status'],
        'Religion'        => $per['religion'],
        'Caste'           => $per['caste'],
        'Nationality'     => $per['nationality'],
        'Height'          => $per['height'],
        'Weight'          => $per['weight'],
        "Father's Name"   => $per['father_name'],
        "Mother's Name"   => $per['mother_name'],
        'Languages Known' => $per['languages'],
        'Hobbies'         => $per['hobbies'],
      ];
      foreach ($rows as $label => $val):
        if (!$val) continue;
      ?>
      <tr><td><?= h($label) ?></td><td>:</td><td><?= h($val) ?></td></tr>
      <?php endforeach; ?>
    </table>
  </div>

  <!-- Contact Details -->
  <div class="section">
    <div class="sec-title">Contact Details</div>
    <table class="info-table">
      <?php
      $crows = [
        'Phone'    => $b['phone'],
        'Email'    => $b['email'],
        'Address'  => implode(', ', array_filter([$b['address'],$b['city'],$b['state'],$b['country']])),
        'LinkedIn' => $b['linkedin'],
        'Website'  => $b['website'],
      ];
      foreach ($crows as $label => $val): if (!$val) continue; ?>
      <tr><td><?= h($label) ?></td><td>:</td><td><?= h($val) ?></td></tr>
      <?php endforeach; ?>
    </table>
  </div>

  <?php if (!empty($edu) && $vis['education']): ?>
  <div class="section">
    <div class="sec-title">Educational Qualification</div>
    <?php foreach ($edu as $e): if (!$e['degree']) continue; ?>
    <div class="edu-entry">
      <div class="edu-deg"><?= h($e['degree']) ?><?= $e['year_to']?' ('.$e['year_to'].')':'' ?><?= $e['grade']?' — '.h($e['grade']):'' ?></div>
      <div class="edu-inst"><?= h($e['institution']) ?><?= $e['university']?' · '.h($e['university']):'' ?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($exp) && $vis['experience']): ?>
  <div class="section">
    <div class="sec-title">Professional Experience</div>
    <?php foreach ($exp as $e): if (!$e['job_title']) continue; ?>
    <div class="exp-entry">
      <div class="edu-deg"><?= h($e['job_title']) ?> — <?= h($e['company']) ?></div>
      <div class="edu-inst"><?= h($e['date_from']) ?><?= $e['date_from']?' to ':'' ?><?= !empty($e['is_current'])?'Present':h($e['date_to']) ?><?= $e['location']?' · '.h($e['location']):'' ?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($sk) && $vis['skills']): ?>
  <div class="section">
    <div class="sec-title">Skills</div>
    <div style="padding:4px 8px;font-size:.85em;color:#374151">
      <?= h(implode(' · ', array_filter(array_column($sk, 'name')))) ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($b['objective'] && $vis['objective']): ?>
  <div class="section">
    <div class="sec-title">Objective / Profile</div>
    <div style="padding:4px 8px;font-size:.85em;color:#374151;line-height:1.75"><?= nl2br(h($b['objective'])) ?></div>
  </div>
  <?php endif; ?>

  <!-- Declaration -->
  <div class="section">
    <div class="sec-title">Declaration</div>
    <p style="font-size:.82em;color:#475569;padding:0 8px">I hereby declare that the information furnished above is true and correct to the best of my knowledge and belief.</p>
  </div>

  <!-- Signature -->
  <div class="sign-area">
    <div class="sign-box">
      <div class="sign-line"></div>
      <div>Place &amp; Date</div>
    </div>
    <div class="sign-box">
      <div class="sign-line"></div>
      <div>Signature<br><?= h($b['full_name']) ?></div>
    </div>
  </div>
</div>
</body>
</html>
