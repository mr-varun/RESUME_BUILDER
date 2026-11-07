<?php
// Creative — Bold diagonal left panel, asymmetric layout
$b=$d['basic'];$edu=$d['education'];$exp=$d['experience'];$sk=$d['skills'];$per=$d['personal'];$ext=$d['extras'];$sty=$d['styling'];$vis=$sty['sections_visible'];
$acc=htmlspecialchars($sty['accent_color']??'#7c3aed',ENT_QUOTES);
$fs=(int)($sty['font_size']??13);$ff=htmlspecialchars($sty['font_family']??'Inter',ENT_QUOTES);
function h($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function nl2($v){return nl2br(h($v));}
$photoSrc=!empty($b['photo'])?(defined('UPLOAD_URL')?UPLOAD_URL.h($b['photo']):'../uploads/photos/'.h($b['photo'])):'';
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title><?=h($b['full_name'])?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html{font-size:<?=$fs?>px}
body{font-family:'<?=$ff?>',sans-serif;background:#fff;color:#1e1e2e;line-height:1.55;-webkit-font-smoothing:antialiased}
.page{max-width:210mm;margin:0 auto;min-height:297mm;display:flex;overflow:hidden}
/* Left panel */
.left{width:210px;background:<?=$acc?>;position:relative;flex-shrink:0;display:flex;flex-direction:column;padding:28px 20px;overflow:hidden}
.left::after{content:'';position:absolute;bottom:-1px;right:-60px;width:120px;height:120%;background:#fff;transform:skewX(-8deg)}
.dot-pattern{position:absolute;top:0;left:0;width:100%;height:100%;opacity:.06;background-image:radial-gradient(#fff 1px,transparent 1px);background-size:14px 14px}
.l-photo-wrap{margin-bottom:20px;position:relative;z-index:1;text-align:center}
.l-photo{width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.5);display:block;margin:0 auto}
.l-photo-ph{width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:30px;color:rgba(255,255,255,.5);margin:0 auto}
.l-sec-title{font-size:.65em;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.55);margin:14px 0 7px;position:relative;z-index:1}
.l-item{display:flex;align-items:flex-start;gap:6px;font-size:.74em;color:rgba(255,255,255,.85);margin-bottom:5px;position:relative;z-index:1;word-break:break-all}
.l-item-dot{width:5px;height:5px;background:rgba(255,255,255,.5);border-radius:50%;margin-top:5px;flex-shrink:0}
.skill-pill{display:inline-block;background:rgba(255,255,255,.15);color:rgba(255,255,255,.9);padding:2px 8px;border-radius:20px;font-size:.7em;margin:2px 2px 2px 0;position:relative;z-index:1}
.skill-bar-row{margin-bottom:6px;position:relative;z-index:1}
.skill-bar-lbl{font-size:.72em;color:rgba(255,255,255,.8);margin-bottom:3px}
.skill-bar-bg{height:3px;background:rgba(255,255,255,.2);border-radius:2px}
.skill-bar-fill{height:3px;background:rgba(255,255,255,.8);border-radius:2px}
/* Right */
.right{flex:1;padding:32px 28px 28px 36px;overflow:hidden}
/* Name block */
.r-name{margin-bottom:6px}
.r-name h1{font-size:2.3em;font-weight:900;color:#0f172a;letter-spacing:-.03em;line-height:1}
.r-name h2{font-size:.95em;font-weight:400;color:<?=$acc?>;margin-top:4px;font-style:italic}
.r-divider{width:48px;height:3px;background:<?=$acc?>;border-radius:2px;margin:12px 0}
.obj-text{font-size:.83em;color:#374151;line-height:1.75;max-width:380px;margin-bottom:20px}
/* Section */
.sec{margin-bottom:18px}
.sec-title{font-size:.73em;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:<?=$acc?>;margin-bottom:10px;display:flex;align-items:center;gap:8px}
.sec-title::after{content:'';flex:1;height:1px;background:<?=$acc?>25}
.entry{margin-bottom:10px}
.e-top{display:flex;justify-content:space-between;align-items:baseline;gap:8px}
.e-title{font-weight:700;font-size:.87em;color:#0f172a}
.e-date{font-size:.73em;color:#9ca3af;white-space:nowrap}
.e-sub{font-size:.79em;color:#6b7280;margin:1px 0}
.e-desc{font-size:.78em;color:#4b5563;line-height:1.65;margin-top:3px}
@media print{body{print-color-adjust:exact;-webkit-print-color-adjust:exact}}
</style></head><body><div class="page">
<div class="left">
  <div class="dot-pattern"></div>
  <?php if($photoSrc):?><div class="l-photo-wrap"><img class="l-photo" src="<?=$photoSrc?>" alt=""></div><?php else:?><div class="l-photo-wrap"><div class="l-photo-ph">&#9786;</div></div><?php endif;?>
  <div class="l-sec-title">Contact</div>
  <?php if($b['phone']):?><div class="l-item"><span class="l-item-dot"></span><?=h($b['phone'])?></div><?php endif;?>
  <?php if($b['email']):?><div class="l-item"><span class="l-item-dot"></span><?=h($b['email'])?></div><?php endif;?>
  <?php if($b['city']||$b['state']):?><div class="l-item"><span class="l-item-dot"></span><?=h(implode(', ',array_filter([$b['city'],$b['state']])))?></div><?php endif;?>
  <?php if($b['linkedin']):?><div class="l-item"><span class="l-item-dot"></span><?=h(preg_replace('#https?://(www\.)?#','',$b['linkedin']))?></div><?php endif;?>
  <?php if($b['github']):?><div class="l-item"><span class="l-item-dot"></span><?=h(preg_replace('#https?://(www\.)?#','',$b['github']))?></div><?php endif;?>
  <?php if(!empty($sk)&&$vis['skills']):?>
  <div class="l-sec-title">Skills</div>
  <?php foreach($sk as $s):if(!$s['name'])continue;?>
  <div class="skill-bar-row">
    <div class="skill-bar-lbl"><?=h($s['name'])?></div>
    <div class="skill-bar-bg"><div class="skill-bar-fill" style="width:<?=(int)($s['level']??80)?>%"></div></div>
  </div>
  <?php endforeach;?>
  <?php endif;?>
  <?php if($per['languages']):?><div class="l-sec-title">Languages</div><?php foreach(explode(',',$per['languages']) as $l):if(trim($l)):?><span class="skill-pill"><?=h(trim($l))?></span><?php endif;endforeach;?><?php endif;?>
  <?php if($per['hobbies']):?><div class="l-sec-title">Interests</div><div class="l-item" style="flex-wrap:wrap"><?=h($per['hobbies'])?></div><?php endif;?>
</div>

<div class="right">
  <div class="r-name">
    <h1><?=h($b['full_name']?:'Your Name')?></h1>
    <?php if(!empty($exp)):?><h2><?=h($exp[0]['job_title']??'')?></h2><?php endif;?>
  </div>
  <div class="r-divider"></div>
  <?php if($b['objective']&&$vis['objective']):?><p class="obj-text"><?=nl2($b['objective'])?></p><?php endif;?>
  <?php if(!empty($exp)&&$vis['experience']):?>
  <div class="sec"><div class="sec-title">Work Experience</div>
  <?php foreach($exp as $e):if(!$e['job_title'])continue;?>
  <div class="entry">
    <div class="e-top"><span class="e-title"><?=h($e['job_title'])?></span><span class="e-date"><?=h($e['date_from'])?><?=$e['date_from']?' – ':''?><?=!empty($e['is_current'])?'Present':h($e['date_to'])?></span></div>
    <div class="e-sub"><?=h($e['company'])?><?=$e['location']?' · '.h($e['location']):''?></div>
    <?php if($e['description']):?><div class="e-desc"><?=nl2($e['description'])?></div><?php endif;?>
  </div>
  <?php endforeach;?></div>
  <?php endif;?>
  <?php if(!empty($edu)&&$vis['education']):?>
  <div class="sec"><div class="sec-title">Education</div>
  <?php foreach($edu as $e):if(!$e['degree'])continue;?>
  <div class="entry">
    <div class="e-top"><span class="e-title"><?=h($e['degree'])?></span><span class="e-date"><?=h($e['year_to'])?></span></div>
    <div class="e-sub"><?=h($e['institution'])?><?=$e['grade']?' · '.h($e['grade']):''?></div>
  </div>
  <?php endforeach;?></div>
  <?php endif;?>
  <?php if(!empty($ext['projects'])&&$vis['projects']):?>
  <div class="sec"><div class="sec-title">Projects</div>
  <?php foreach($ext['projects'] as $p):if(!$p['name'])continue;?>
  <div class="entry"><div class="e-title"><?=h($p['name'])?></div><?php if($p['tech_stack']):?><div class="e-sub"><?=h($p['tech_stack'])?></div><?php endif;?><?php if($p['description']):?><div class="e-desc"><?=nl2($p['description'])?></div><?php endif;?></div>
  <?php endforeach;?></div>
  <?php endif;?>
  <?php if(!empty($ext['certifications'])&&$vis['certifications']):?>
  <div class="sec"><div class="sec-title">Certifications</div>
  <?php foreach($ext['certifications'] as $c):if(!$c['name'])continue;?><div class="entry"><div class="e-top"><span class="e-title"><?=h($c['name'])?></span><?php if($c['date']):?><span class="e-date"><?=h($c['date'])?></span><?php endif;?></div><?php if($c['issuer']):?><div class="e-sub"><?=h($c['issuer'])?></div><?php endif;?></div><?php endforeach;?></div>
  <?php endif;?>
</div>
</div></body></html>
