<?php
/**
 * Marriage Biodata Template
 * Classic Indian marriage biodata — matches reference design exactly
 * Fields: Rashi, Nakshtra, Gotra, Birth Time, Family, Contact
 */
$b   = $d['basic'];
$edu = $d['education'];
$exp = $d['experience'];
$sk  = $d['skills'];
$per = $d['personal'];
$ext = $d['extras'];
$sty = $d['styling'];
$acc = htmlspecialchars($sty['accent_color'] ?? '#8B0000', ENT_QUOTES);
$fs  = (int)($sty['font_size'] ?? 15);
$ff  = htmlspecialchars($sty['font_family'] ?? 'Times New Roman', ENT_QUOTES);
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
$photoSrc = !empty($b['photo']) ? (defined('UPLOAD_URL') ? UPLOAD_URL . h($b['photo']) : '../uploads/photos/' . h($b['photo'])) : '';
$godLeftImg  = !empty($per['god_left_img'])  ? (defined('UPLOAD_URL') ? UPLOAD_URL . h($per['god_left_img'])  : '../uploads/photos/' . h($per['god_left_img']))  : '';
$godRightImg = !empty($per['god_right_img']) ? (defined('UPLOAD_URL') ? UPLOAD_URL . h($per['god_right_img']) : '../uploads/photos/' . h($per['god_right_img'])) : '';

// Format DOB with birth time
$dobText = '';
if ($per['dob']) {
    $dobText = date('d - F - Y', strtotime($per['dob']));
    if ($per['birth_time']) $dobText .= ' ( ' . h($per['birth_time']) . ' )';
}

// Education: highest qualification
$eduText = '';
foreach ($edu as $e) {
    if ($e['degree']) { $eduText = $e['degree']; break; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bio-Data — <?= h($b['full_name']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700&family=EB+Garamond:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
html { font-size: <?= $fs ?>px; }
body {
  font-family: '<?= $ff ?>', 'Times New Roman', Times, serif;
  background: #fff;
  color: #000;
  -webkit-print-color-adjust: exact;
  print-color-adjust: exact;
}

/* ══════════════════════════════════
   OUTER PAGE — double border look
══════════════════════════════════ */
.page {
  max-width: 210mm;
  margin: 0 auto;
  min-height: 297mm;
  padding: 16px;
  position: relative;
  /* outer border */
  border: 4px solid #1a1a1a;
}
/* Inner border line */
.page::before {
  content: '';
  position: absolute;
  inset: 8px;
  border: 1.5px solid #1a1a1a;
  pointer-events: none;
}
.page-inner {
  padding: 14px 24px 20px;
}

/* ══════════════════════════════════
   HEADER  — 3-column deity + title
══════════════════════════════════ */
.hdr {
  display: grid;
  grid-template-columns: 110px 1fr 110px;
  align-items: center;
  gap: 0;
  margin-bottom: 6px;
}
.hdr-deity {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}
.hdr-deity img {
  width: 88px;
  height: 90px;
  object-fit: contain;
  display: block;
}
.hdr-deity-ph {
  width: 88px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  color: <?= $acc ?>88;
}
.hdr-deity-caption {
  font-size: .72em;
  font-weight: bold;
  text-align: center;
  color: #333;
  line-height: 1.3;
}
.hdr-center {
  text-align: center;
}
.bio-title {
  font-family: 'Cinzel Decorative', 'Times New Roman', serif;
  font-size: 1.9em;
  font-weight: 700;
  text-decoration: underline;
  letter-spacing: .05em;
  color: #1a1a1a;
  text-underline-offset: 4px;
}

/* ══════════════════════════════════
   MAIN DATA TABLE
══════════════════════════════════ */
.data-section {
  margin-bottom: 10px;
}
.section-title {
  font-size: 1em;
  font-weight: bold;
  margin: 14px 0 6px 30px;
  color: #1a1a1a;
}
.data-table {
  width: 100%;
  border-collapse: collapse;
}
.data-table td {
  padding: 5px 8px;
  vertical-align: top;
  line-height: 1.55;
  font-weight: bold;
}
/* Label column */
.data-table td.lbl {
  width: 170px;
  text-align: right;
  padding-right: 0;
  font-weight: bold;
  color: #1a1a1a;
}
/* Colon column */
.data-table td.col {
  width: 30px;
  text-align: center;
  font-weight: bold;
  padding: 5px 4px;
}
/* Value column */
.data-table td.val {
  font-weight: bold;
  color: #1a1a1a;
}

/* Photo box */
.photo-wrap {
  display: flex;
  gap: 0;
}
.photo-wrap .data-table { flex: 1; }
.photo-box {
  width: 100px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  padding-top: 8px;
}
.photo-img {
  width: 90px;
  height: 110px;
  object-fit: cover;
  border: 1.5px solid #555;
  display: block;
}
.photo-placeholder {
  width: 90px;
  height: 110px;
  border: 1.5px dashed #999;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: .7em;
  color: #aaa;
  text-align: center;
  padding: 6px;
  font-weight: normal;
}

/* Contact multi-line address */
.addr-lines {
  line-height: 1.65;
}

/* Ornament divider */
.ornament {
  text-align: center;
  font-size: .85em;
  color: #555;
  margin: 6px 0 0;
  font-style: italic;
  letter-spacing: .05em;
}

@media print {
  body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
  .page { max-width: 100%; border: 4px solid #1a1a1a; min-height: auto; }
}
</style>
</head>
<body>
<div class="page">
<div class="page-inner">

  <!-- ══ Header ══ -->
  <div class="hdr">
    <!-- Left deity -->
    <div class="hdr-deity">
      <?php if ($godLeftImg): ?>
        <img src="<?= $godLeftImg ?>" alt="">
      <?php else: ?>
        <div class="hdr-deity-ph">🙏</div>
      <?php endif; ?>
      <?php if ($per['god_left']): ?>
        <div class="hdr-deity-caption"><?= h($per['god_left']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Center title -->
    <div class="hdr-center">
      <div class="bio-title">BIO-DATA</div>
    </div>

    <!-- Right deity -->
    <div class="hdr-deity">
      <?php if ($godRightImg): ?>
        <img src="<?= $godRightImg ?>" alt="">
      <?php else: ?>
        <div class="hdr-deity-ph">🙏</div>
      <?php endif; ?>
      <?php if ($per['god_right']): ?>
        <div class="hdr-deity-caption"><?= h($per['god_right']) ?></div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ══ Personal Information ══ -->
  <div class="data-section">
    <div class="photo-wrap">
      <table class="data-table">
        <?php
        $rows = [
          'Name'           => $b['full_name'],
          'Date Of Birth'  => $dobText,
          'Birth Place'    => $per['birth_place'],
          'Rashi'          => $per['rashi'],
          'Nakshtra'       => $per['nakshtra'],
          'Height'         => $per['height'],
          'Weight'         => $per['weight'],
          'Gotra'          => $per['gotra'],
          'Manglik'        => $per['manglik'],
          'Education'      => $eduText ?: ($edu[0]['degree'] ?? ''),
          'Caste'          => trim(($per['religion'] ? $per['religion'] . ' ' : '') . $per['caste']),
          'Occupation'     => $per['occupation'] ?: (($exp[0]['job_title'] ?? '') . ($exp[0]['company'] ? ' (' . ($exp[0]['company']) . ')' : '')),
          'Annual Income'  => $per['annual_income'],
          'Land'           => $per['land'],
        ];
        foreach ($rows as $label => $val):
          if (!trim((string)$val)) continue;
        ?>
        <tr>
          <td class="lbl"><?= h($label) ?></td>
          <td class="col">:</td>
          <td class="val"><?= h($val) ?></td>
        </tr>
        <?php endforeach; ?>
      </table>

      <?php if ($photoSrc || true): ?>
      <div class="photo-box">
        <?php if ($photoSrc): ?>
          <img class="photo-img" src="<?= $photoSrc ?>" alt="Photo">
        <?php else: ?>
          <div class="photo-placeholder">Affix Photo Here</div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ══ Family Details ══ -->
  <?php
  $hasFamily = array_filter([
    $per['father_name'], $per['mother_name'], $per['brothers'],
    $per['sisters'], $per['uncle_name'], $per['family_type'], $per['family_status']
  ]);
  ?>
  <?php if ($hasFamily): ?>
  <div class="data-section">
    <div class="section-title">Family Details :</div>
    <table class="data-table">
      <?php
      $familyRows = [
        'Father'      => $per['father_name'] . ($per['father_occupation'] ? ' (' . $per['father_occupation'] . ')' : ''),
        'Mother'      => $per['mother_name'] . ($per['mother_occupation'] ? ' (' . $per['mother_occupation'] . ')' : ''),
        'Brothers'    => $per['brothers'],
        'Sisters'     => $per['sisters'],
        'Uncle'       => $per['uncle_name'] . ($per['uncle_occupation'] ? ' (' . $per['uncle_occupation'] . ')' : ''),
        'Family Type' => $per['family_type'],
        'Family Status' => $per['family_status'],
      ];
      foreach ($familyRows as $label => $val):
        if (!trim((string)$val)) continue;
      ?>
      <tr>
        <td class="lbl"><?= h($label) ?></td>
        <td class="col">:</td>
        <td class="val"><?= h($val) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
  <?php endif; ?>

  <!-- ══ Contact Details ══ -->
  <?php
  $contactPerson  = $per['contact_person'] ?: $per['father_name'] ?: $b['full_name'];
  $fullAddress    = implode("\n", array_filter([
    $per['contact_address'] ?: $b['address'],
    $b['city'],
    $per['taluka'] ? 'Tq : ' . $per['taluka'] : '',
    ($per['district'] ?: $b['state']) ? 'Dist : ' . ($per['district'] ?: $b['state']) : '',
  ]));
  ?>
  <?php if ($b['phone'] || $fullAddress || $contactPerson): ?>
  <div class="data-section">
    <div class="section-title">Contact Details:</div>
    <table class="data-table">
      <?php if ($contactPerson): ?>
      <tr>
        <td class="lbl">Address</td>
        <td class="col">:</td>
        <td class="val">
          <div class="addr-lines">
            <?= h($contactPerson) ?>
            <?php foreach (array_filter(explode("\n", $fullAddress)) as $line): ?>
            <br><?= h(trim($line)) ?>
            <?php endforeach; ?>
          </div>
        </td>
      </tr>
      <?php elseif ($fullAddress): ?>
      <tr>
        <td class="lbl">Address</td>
        <td class="col">:</td>
        <td class="val"><div class="addr-lines"><?= implode('<br>', array_map('h', array_filter(explode("\n", $fullAddress)))) ?></div></td>
      </tr>
      <?php endif; ?>
      <?php if ($b['phone']): ?>
      <tr>
        <td class="lbl">Mobile No</td>
        <td class="col">:</td>
        <td class="val"><?= h($b['phone']) ?></td>
      </tr>
      <?php endif; ?>
      <?php if ($b['email']): ?>
      <tr>
        <td class="lbl">Email</td>
        <td class="col">:</td>
        <td class="val"><?= h($b['email']) ?></td>
      </tr>
      <?php endif; ?>
      <?php if ($per['languages']): ?>
      <tr>
        <td class="lbl">Languages</td>
        <td class="col">:</td>
        <td class="val"><?= h($per['languages']) ?></td>
      </tr>
      <?php endif; ?>
    </table>
  </div>
  <?php endif; ?>

  <!-- ══ Footer blessing text ══ -->
  <?php if ($per['god_left'] || $per['god_right']): ?>
  <div class="ornament">
    <?= $per['god_left'] ? h($per['god_left']) . '&nbsp;&nbsp;&nbsp;&nbsp;' : '' ?>
    <?= $per['god_right'] ? h($per['god_right']) : '' ?>
  </div>
  <?php endif; ?>

</div><!-- /page-inner -->
</div><!-- /page -->
</body>
</html>
