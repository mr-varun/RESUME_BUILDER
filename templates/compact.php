<?php
// Compact — Maximum density, fits extensive content on one page
$b=$d['basic'];$edu=$d['education'];$exp=$d['experience'];$sk=$d['skills'];$per=$d['personal'];$ext=$d['extras'];$sty=$d['styling'];$vis=$sty['sections_visible'];
$acc=htmlspecialchars($sty['accent_color']??'#1d4ed8',ENT_QUOTES);
$fs=(int)($sty['font_size']??12);$ff=htmlspecialchars($sty['font_family']??'Inter',ENT_QUOTES);
function h($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function nl2($v){return nl2br(h($v));}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title><?=h($b['full_name'])?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}html{font-size:<?=$fs?>px}
body{font-family:'<?=$ff?>',sans-serif;background:#fff;color:#111827;line-height:1.45}
.page{max-width:210mm;margin:0 auto;padding:20px 28px;min-height:297mm}
/* Compact header — single line */
.hdr{border-bottom:2px solid <?=$acc?>;padding-bottom:8px;margin-bottom:10px}
.hdr-name{font-size:1.6em;font-weight:800;color:#030712;display:inline;margin-right:10px}
.hdr-role{font-size:.8em;font-weight:500;color:<?=$acc?>;display:inline}
.hdr-contacts{display:flex;flex-wrap:wrap;gap:2px 0;margin-top:4px;font-size:.72em;color:#6b7280}
.hdr-contacts span{padding-right:10px;border-right:1px solid #d1d5db;margin-right:10px}
.hdr-contacts span:last-child{border-right:none;padding-right:0}
/* Section */
.sec{margin-bottom:8px}
.sec-hdr{font-size:.66em;font-weight:800;text-transform:uppercase;letter-spacing:.12em;background:<?=$acc?>;color:#fff;padding:2px 8px;display:inline-block;margin-bottom:5px;border-radius:1px}
/* Compact entry */
.entry{margin-bottom:5px;padding-bottom:5px;border-bottom:1px solid #f3f4f6}
.entry:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
.e-line1{display:flex;justify-content:space-between;align-items:baseline;gap:6px}
.e-title{font-weight:700;font-size:.84em;color:#030712}
.e-date{font-size:.7em;color:<?=$acc?>;font-weight:600;white-space:nowrap;flex-shrink:0}
.e-line2{font-size:.76em;color:#6b7280;margin-bottom:1px}
.e-desc{font-size:.75em;color:#374151;line-height:1.55}
.obj{font-size:.8em;color:#374151;line-height:1.6}
/* Two column layout for lower sections */
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:0 20px}
/* Skills inline */
.skills-row{display:flex;flex-wrap:wrap;gap:3px;margin-top:2px}
.skill-tag{font-size:.71em;padding:1px 7px;border:1px solid <?=$acc?>55;border-radius:2px;color:<?=$acc?>;font-weight:500;background:<?=$acc?>08}
/* Dense table for personal info */
.dense-table{font-size:.74em;color:#374151;display:grid;grid-template-columns:110px 1fr;gap:2px 8px}
.dense-label{color:#9ca3af;font-weight:500}
@media print{body{print-color-adjust:exact;-webkit-print-color-adjust:exact}}
</style></head><body><div class="page">

<div class="hdr">
  <div><span class="hdr-name"><?=h($b['full_name']?:'Your Name')?></span><?php if(!empty($exp)):?><span class="hdr-role"><?=h($exp[0]['job_title']??'')?></span><?php endif;?></div>
  <div class="hdr-contacts">
    <?php if($b['email']):?><span><?=h($b['email'])?></span><?php endif;?>
    <?php if($b['phone']):?><span><?=h($b['phone'])?></span><?php endif;?>
    <?php if($b['city']||$b['state']):?><span><?=h(implode(', ',array_filter([$b['city'],$b['state']])))?></span><?php endif;?>
    <?php if($b['linkedin']):?><span><?=h(preg_replace('#https?://(www\.)?#','',$b['linkedin']))?></span><?php endif;?>
    <?php if($b['github']):?><span><?=h(preg_replace('#https?://(www\.)?#','',$b['github']))?></span><?php endif;?>
    <?php if($b['website']):?><span><?=h(preg_replace('#https?://(www\.)?#','',$b['website']))?></span><?php endif;?>
  </div>
</div>

<?php if($b['objective']&&$vis['objective']):?>
<div class="sec"><div class="sec-hdr">Summary</div><p class="obj"><?=nl2($b['objective'])?></p></div>
<?php endif;?>

<?php if(!empty($exp)&&$vis['experience']):?>
<div class="sec"><div class="sec-hdr">Experience</div>
<?php foreach($exp as $e):if(!$e['job_title'])continue;?>
<div class="entry">
  <div class="e-line1"><span class="e-title"><?=h($e['job_title'])?>, <?=h($e['company'])?><?=$e['location']?', '.h($e['location']):''?></span><span class="e-date"><?=h($e['date_from'])?><?=$e['date_from']?' – ':''?><?=!empty($e['is_current'])?'Present':h($e['date_to'])?></span></div>
  <?php if($e['description']):?><div class="e-desc"><?=nl2($e['description'])?></div><?php endif;?>
</div>
<?php endforeach;?></div>
<?php endif;?>

<?php if(!empty($sk)&&$vis['skills']):?>
<div class="sec"><div class="sec-hdr">Skills</div><div class="skills-row"><?php foreach($sk as $s):if(!$s['name'])continue;?><span class="skill-tag"><?=h($s['name'])?></span><?php endforeach;?></div></div>
<?php endif;?>

<div class="two-col">
<?php if(!empty($edu)&&$vis['education']):?>
<div class="sec"><div class="sec-hdr">Education</div>
<?php foreach($edu as $e):if(!$e['degree'])continue;?>
<div class="entry">
  <div class="e-line1"><span class="e-title"><?=h($e['degree'])?></span><span class="e-date"><?=h($e['year_to'])?></span></div>
  <div class="e-line2"><?=h($e['institution'])?><?=$e['grade']?' · '.h($e['grade']):''?></div>
</div>
<?php endforeach;?></div>
<?php endif;?>

<?php if(!empty($ext['certifications'])&&$vis['certifications']):?>
<div class="sec"><div class="sec-hdr">Certifications</div>
<?php foreach($ext['certifications'] as $c):if(!$c['name'])continue;?><div class="entry"><div class="e-line1"><span class="e-title"><?=h($c['name'])?></span><?php if($c['date']):?><span class="e-date"><?=h($c['date'])?></span><?php endif;?></div><?php if($c['issuer']):?><div class="e-line2"><?=h($c['issuer'])?></div><?php endif;?></div><?php endforeach;?></div>
<?php endif;?>
</div>

<?php if(!empty($ext['projects'])&&$vis['projects']):?>
<div class="sec"><div class="sec-hdr">Projects</div>
<?php foreach($ext['projects'] as $p):if(!$p['name'])continue;?><div class="entry"><div class="e-line1"><span class="e-title"><?=h($p['name'])?><?=$p['role']?' — '.h($p['role']):''?></span><?php if($p['tech_stack']):?><span class="e-date" style="color:#6b7280;font-size:.68em;background:#f9fafb"><?=h($p['tech_stack'])?></span><?php endif;?></div><?php if($p['description']):?><div class="e-desc"><?=nl2($p['description'])?></div><?php endif;?></div><?php endforeach;?></div>
<?php endif;?>

<?php if(!empty($ext['achievements'])&&$vis['achievements']):?>
<div class="sec"><div class="sec-hdr">Achievements</div>
<div class="two-col">
<?php foreach($ext['achievements'] as $a):if(!$a['title'])continue;?><div class="entry"><div class="e-line1"><span class="e-title"><?=h($a['title'])?></span><?php if($a['year']):?><span class="e-date"><?=h($a['year'])?></span><?php endif;?></div><?php if($a['description']):?><div class="e-desc"><?=h($a['description'])?></div><?php endif;?></div><?php endforeach;?>
</div></div>
<?php endif;?>

<?php $showPer=array_filter([$per['dob'],$per['gender'],$per['marital_status'],$per['nationality'],$per['languages'],$per['hobbies']]);if($showPer&&$vis['personal']):?>
<div class="sec"><div class="sec-hdr">Personal</div>
<div class="dense-table">
<?php $rows=['DOB'=>$per['dob']?date('d/m/Y',strtotime($per['dob'])):'','Gender'=>$per['gender'],'Marital'=>$per['marital_status'],'Nationality'=>$per['nationality'],'Languages'=>$per['languages'],'Hobbies'=>$per['hobbies']];foreach($rows as $l=>$v):if(!$v)continue;?><span class="dense-label"><?=h($l)?></span><span><?=h($v)?></span><?php endforeach;?>
</div></div>
<?php endif;?>

<?php if(!empty($ext['references'])&&$vis['references']):?>
<div class="sec"><div class="sec-hdr">References</div>
<div class="two-col">
<?php foreach($ext['references'] as $r):if(!$r['name'])continue;?><div class="entry"><span class="e-title"><?=h($r['name'])?></span><div class="e-line2"><?=h($r['designation'])?><?=$r['company']?', '.h($r['company']):''?></div><?php if($r['email']||$r['phone']):?><div style="font-size:.72em;color:#6b7280"><?=h($r['email'])?><?=$r['email']&&$r['phone']?' · ':''?><?=h($r['phone'])?></div><?php endif;?></div><?php endforeach;?>
</div></div>
<?php endif;?>

</div></body></html>
