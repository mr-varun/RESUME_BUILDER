<?php
// Elegant — Centered serif design with decorative dividers
$b=$d['basic'];$edu=$d['education'];$exp=$d['experience'];$sk=$d['skills'];$per=$d['personal'];$ext=$d['extras'];$sty=$d['styling'];$vis=$sty['sections_visible'];
$acc=htmlspecialchars($sty['accent_color']??'#92400e',ENT_QUOTES);
$fs=(int)($sty['font_size']??14);$ff=htmlspecialchars($sty['font_family']??'Georgia',ENT_QUOTES);
function h($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function nl2($v){return nl2br(h($v));}
$photoSrc=!empty($b['photo'])?(defined('UPLOAD_URL')?UPLOAD_URL.h($b['photo']):'../uploads/photos/'.h($b['photo'])):'';
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title><?=h($b['full_name'])?></title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html{font-size:<?=$fs?>px}
body{font-family:'<?=$ff?>',Georgia,serif;background:#fff;color:#292524;line-height:1.65;-webkit-font-smoothing:antialiased}
.page{max-width:210mm;margin:0 auto;padding:44px 52px;min-height:297mm}
/* Header – centered */
.hdr{text-align:center;margin-bottom:28px}
.hdr-photo{width:88px;height:88px;border-radius:50%;object-fit:cover;border:2px solid <?=$acc?>66;margin:0 auto 16px;display:block}
.hdr-photo-ph{width:88px;height:88px;border-radius:50%;background:<?=$acc?>12;border:2px solid <?=$acc?>44;display:flex;align-items:center;justify-content:center;font-size:32px;color:<?=$acc?>;margin:0 auto 16px}
.hdr-name{font-family:'Cormorant Garamond',Georgia,serif;font-size:2.6em;font-weight:600;letter-spacing:.04em;color:#1c1917;margin-bottom:6px}
.hdr-role{font-family:'Inter',sans-serif;font-size:.8em;font-weight:400;letter-spacing:.2em;text-transform:uppercase;color:<?=$acc?>;margin-bottom:12px}
.hdr-contacts{display:flex;justify-content:center;flex-wrap:wrap;gap:4px 0;font-family:'Inter',sans-serif;font-size:.76em;color:#78716c}
.hdr-contacts span{padding:0 12px;border-right:1px solid #d6d3d1}
.hdr-contacts span:last-child{border-right:none}
/* Decorative divider */
.divider{display:flex;align-items:center;gap:10px;margin:18px 0;text-align:center}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:linear-gradient(to right,transparent,<?=$acc?>55,transparent)}
.divider-ornament{color:<?=$acc?>;font-size:1.1em;line-height:1}
/* Section */
.sec{margin-bottom:20px}
.sec-title{font-family:'Cormorant Garamond',serif;font-size:1.1em;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:<?=$acc?>;text-align:center;margin-bottom:12px}
/* Two column */
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:0 40px}
/* Entry */
.entry{margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #f5f5f4}
.entry:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
.e-top{display:flex;justify-content:space-between;align-items:baseline;gap:8px;margin-bottom:1px}
.e-title{font-weight:600;font-size:.9em;color:#1c1917;font-family:'Cormorant Garamond',serif;font-size:1em}
.e-date{font-family:'Inter',sans-serif;font-size:.72em;color:<?=$acc?>;font-weight:400;white-space:nowrap;font-style:italic}
.e-sub{font-family:'Inter',sans-serif;font-size:.78em;color:#78716c;margin-bottom:3px}
.e-desc{font-size:.84em;color:#57534e;line-height:1.7;text-align:justify}
.obj-text{font-size:.88em;color:#57534e;line-height:1.8;text-align:justify}
/* Skills */
.skills-row{display:flex;justify-content:center;flex-wrap:wrap;gap:8px}
.skill-chip{padding:3px 14px;border:1px solid <?=$acc?>44;border-radius:20px;font-family:'Inter',sans-serif;font-size:.74em;color:<?=$acc?>;background:<?=$acc?>08}
/* Personal table */
.personal-grid{display:grid;grid-template-columns:1fr 1fr;gap:6px 28px}
.pers-row{display:flex;gap:6px;font-size:.8em}
.pers-label{color:#78716c;min-width:80px;flex-shrink:0;font-family:'Inter',sans-serif;font-size:.9em}
.pers-value{color:#292524;font-weight:500}
@media print{body{print-color-adjust:exact;-webkit-print-color-adjust:exact}}
</style></head><body><div class="page">

<div class="hdr">
  <?php if($photoSrc):?><img class="hdr-photo" src="<?=$photoSrc?>" alt=""><?php elseif($b['full_name']):?><div class="hdr-photo-ph"><?=strtoupper(substr($b['full_name'],0,1))?></div><?php endif;?>
  <div class="hdr-name"><?=h($b['full_name']?:'Your Name')?></div>
  <?php if(!empty($exp)):?><div class="hdr-role"><?=h($exp[0]['job_title']??'')?></div><?php endif;?>
  <div class="hdr-contacts">
    <?php if($b['email']):?><span><?=h($b['email'])?></span><?php endif;?>
    <?php if($b['phone']):?><span><?=h($b['phone'])?></span><?php endif;?>
    <?php if($b['city']||$b['state']):?><span><?=h(implode(', ',array_filter([$b['city'],$b['state']])))?></span><?php endif;?>
    <?php if($b['linkedin']):?><span><?=h(preg_replace('#https?://(www\.)?#','',$b['linkedin']))?></span><?php endif;?>
  </div>
</div>

<?php if($b['objective']&&$vis['objective']):?>
<div class="divider"><span class="divider-ornament">✦</span></div>
<div class="sec"><div class="sec-title">Professional Summary</div><p class="obj-text"><?=nl2($b['objective'])?></p></div>
<?php endif;?>

<?php if(!empty($exp)&&$vis['experience']):?>
<div class="divider"><span class="divider-ornament">✦</span></div>
<div class="sec"><div class="sec-title">Career History</div>
<?php foreach($exp as $e):if(!$e['job_title'])continue;?>
<div class="entry">
  <div class="e-top"><span class="e-title"><?=h($e['job_title'])?> &mdash; <?=h($e['company'])?></span><span class="e-date"><?=h($e['date_from'])?><?=$e['date_from']?' to ':''?><?=!empty($e['is_current'])?'Present':h($e['date_to'])?></span></div>
  <?php if($e['location']||$e['employment_type']):?><div class="e-sub"><?=h($e['location'])?><?=$e['employment_type']?' · '.h($e['employment_type']):''?></div><?php endif;?>
  <?php if($e['description']):?><div class="e-desc"><?=nl2($e['description'])?></div><?php endif;?>
</div>
<?php endforeach;?></div>
<?php endif;?>

<?php if(!empty($edu)&&$vis['education']):?>
<div class="divider"><span class="divider-ornament">✦</span></div>
<div class="sec"><div class="sec-title">Education</div>
<div class="two-col">
<?php foreach($edu as $e):if(!$e['degree'])continue;?>
<div class="entry">
  <div class="e-top"><span class="e-title"><?=h($e['degree'])?></span><span class="e-date"><?=h($e['year_to'])?></span></div>
  <div class="e-sub"><?=h($e['institution'])?><?=$e['grade']?' · '.h($e['grade']):''?></div>
</div>
<?php endforeach;?>
</div></div>
<?php endif;?>

<?php if(!empty($sk)&&$vis['skills']):?>
<div class="divider"><span class="divider-ornament">✦</span></div>
<div class="sec"><div class="sec-title">Areas of Expertise</div>
<div class="skills-row">
<?php foreach($sk as $s):if(!$s['name'])continue;?><span class="skill-chip"><?=h($s['name'])?></span><?php endforeach;?>
</div></div>
<?php endif;?>

<?php if(!empty($ext['certifications'])&&$vis['certifications']):?>
<div class="divider"><span class="divider-ornament">✦</span></div>
<div class="sec"><div class="sec-title">Certifications &amp; Courses</div>
<div class="two-col">
<?php foreach($ext['certifications'] as $c):if(!$c['name'])continue;?><div class="entry"><div class="e-top"><span class="e-title"><?=h($c['name'])?></span><?php if($c['date']):?><span class="e-date"><?=h($c['date'])?></span><?php endif;?></div><?php if($c['issuer']):?><div class="e-sub"><?=h($c['issuer'])?></div><?php endif;?></div><?php endforeach;?>
</div></div>
<?php endif;?>

<?php $showPer = array_filter([$per['dob'],$per['gender'],$per['marital_status'],$per['nationality'],$per['languages'],$per['hobbies']]); ?>
<?php if($showPer&&$vis['personal']):?>
<div class="divider"><span class="divider-ornament">✦</span></div>
<div class="sec"><div class="sec-title">Personal Profile</div>
<div class="personal-grid">
  <?php $rows=['Date of Birth'=>$per['dob']?date('d F Y',strtotime($per['dob'])):'','Gender'=>$per['gender'],'Marital Status'=>$per['marital_status'],'Nationality'=>$per['nationality'],'Languages'=>$per['languages'],'Hobbies'=>$per['hobbies']]; ?>
  <?php foreach($rows as $lbl=>$val):if(!$val)continue;?><div class="pers-row"><span class="pers-label"><?=h($lbl)?></span><span class="pers-value"><?=h($val)?></span></div><?php endforeach;?>
</div></div>
<?php endif;?>

<?php if(!empty($ext['references'])&&$vis['references']):?>
<div class="divider"><span class="divider-ornament">✦</span></div>
<div class="sec"><div class="sec-title">References</div>
<div class="two-col">
<?php foreach($ext['references'] as $r):if(!$r['name'])continue;?><div class="entry"><div class="e-title"><?=h($r['name'])?></div><div class="e-sub"><?=h($r['designation'])?><?=$r['company']?', '.h($r['company']):''?></div><?php if($r['email']):?><div style="font-size:.76em;color:#78716c;font-family:'Inter',sans-serif"><?=h($r['email'])?></div><?php endif;?></div><?php endforeach;?>
</div></div>
<?php endif;?>

</div></body></html>
