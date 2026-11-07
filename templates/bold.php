<?php
// Bold — High contrast, oversized typography, color blocking, strong visual impact
$b=$d['basic'];$edu=$d['education'];$exp=$d['experience'];$sk=$d['skills'];$per=$d['personal'];$ext=$d['extras'];$sty=$d['styling'];$vis=$sty['sections_visible'];
$acc=htmlspecialchars($sty['accent_color']??'#dc2626',ENT_QUOTES);
$fs=(int)($sty['font_size']??13);$ff=htmlspecialchars($sty['font_family']??'Inter',ENT_QUOTES);
function h($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function nl2($v){return nl2br(h($v));}
$photoSrc=!empty($b['photo'])?(defined('UPLOAD_URL')?UPLOAD_URL.h($b['photo']):'../uploads/photos/'.h($b['photo'])):'';
$nameParts=array_filter(explode(' ',trim($b['full_name']?:'Your Name')));
$firstName=array_shift($nameParts)?:'Your';
$lastName=implode(' ',$nameParts);
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title><?=h($b['full_name'])?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}html{font-size:<?=$fs?>px}
body{font-family:'<?=$ff?>',sans-serif;background:#fff;color:#0a0a0a;line-height:1.5}
.page{max-width:210mm;margin:0 auto;min-height:297mm;overflow:hidden}
/* Hero header */
.hero{position:relative;overflow:hidden;background:#0a0a0a;padding:28px 36px 0}
.hero-bg-text{position:absolute;right:-20px;top:-20px;font-size:10em;font-weight:900;color:rgba(255,255,255,.03);letter-spacing:-.05em;line-height:1;pointer-events:none;white-space:nowrap}
.hero-inner{display:flex;align-items:flex-end;gap:0;position:relative;z-index:1}
.hero-text{flex:1;padding-bottom:0}
.hero-fn{font-size:3em;font-weight:900;color:#fff;letter-spacing:-.03em;line-height:.9;text-transform:uppercase}
.hero-ln{font-size:3em;font-weight:900;color:<?=$acc?>;letter-spacing:-.03em;line-height:.9;text-transform:uppercase}
.hero-role{font-size:.8em;font-weight:500;color:rgba(255,255,255,.55);margin-top:8px;letter-spacing:.05em;text-transform:uppercase}
.hero-photo{width:110px;height:130px;object-fit:cover;flex-shrink:0;filter:grayscale(20%)}
.hero-photo-ph{width:110px;height:130px;background:<?=$acc?>33;display:flex;align-items:center;justify-content:center;font-size:3rem;color:<?=$acc?>;flex-shrink:0}
/* Accent bar with contacts */
.contact-bar{background:<?=$acc?>;padding:8px 36px;display:flex;flex-wrap:wrap;gap:4px 18px}
.c-item{font-size:.72em;color:rgba(255,255,255,.9);display:flex;align-items:center;gap:4px;font-weight:500}
.c-item::before{content:'›';font-size:1.1em}
/* Body */
.body{display:grid;grid-template-columns:3fr 2fr;gap:0}
.col-main{padding:24px 24px 24px 36px}
.col-side{padding:24px 24px 24px 16px;background:#f7f7f7;border-left:1px solid #e5e7eb}
/* Sections main */
.m-sec{margin-bottom:20px}
.m-sec-title{font-size:.78em;font-weight:900;text-transform:uppercase;letter-spacing:.12em;color:<?=$acc?>;margin-bottom:12px;display:flex;align-items:center;gap:10px}
.m-sec-title .bar{width:28px;height:3px;background:<?=$acc?>}
.entry{margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #f0f0f0}
.entry:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
.e-row{display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:2px}
.e-title{font-weight:800;font-size:.88em;color:#0a0a0a;letter-spacing:-.01em}
.e-date{font-size:.7em;font-weight:700;color:#fff;background:#0a0a0a;padding:2px 7px;border-radius:0;white-space:nowrap;flex-shrink:0;letter-spacing:.05em}
.e-sub{font-size:.78em;color:#6b7280;margin-bottom:3px;font-weight:500}
.e-desc{font-size:.78em;color:#374151;line-height:1.65}
.obj-text{font-size:.84em;color:#374151;line-height:1.75;border-left:3px solid <?=$acc?>;padding-left:12px}
/* Sidebar */
.s-sec{margin-bottom:16px}
.s-sec-title{font-size:.68em;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:#0a0a0a;margin-bottom:8px;padding-bottom:4px;border-bottom:2px solid #0a0a0a}
.skill-item{margin-bottom:7px}
.skill-name{font-size:.77em;font-weight:600;color:#0a0a0a;margin-bottom:3px}
.skill-bar{height:6px;background:#e5e7eb;border-radius:0}
.skill-fill{height:6px;background:<?=$acc?>;border-radius:0}
.edu-item{margin-bottom:8px;padding:8px 10px;border-left:3px solid <?=$acc?>;background:#fff}
.edu-deg{font-weight:700;font-size:.82em;color:#0a0a0a}
.edu-sub{font-size:.74em;color:#6b7280}
.badge{display:inline-block;font-size:.7em;font-weight:700;padding:3px 8px;background:#0a0a0a;color:#fff;border-radius:0;margin:2px 2px 2px 0;letter-spacing:.02em}
.badge.accent{background:<?=$acc?>}
.ach-item{font-size:.77em;color:#374151;margin-bottom:5px;padding-left:10px;border-left:2px solid <?=$acc?>}
@media print{body{print-color-adjust:exact;-webkit-print-color-adjust:exact}}
</style></head><body><div class="page">

<div class="hero">
  <div class="hero-bg-text"><?=strtoupper(substr($b['full_name']?:'NAME',0,4))?></div>
  <div class="hero-inner">
    <div class="hero-text">
      <div class="hero-fn"><?=h(strtoupper($firstName))?></div>
      <div class="hero-ln"><?=h(strtoupper($lastName))?></div>
      <?php if(!empty($exp)):?><div class="hero-role"><?=h($exp[0]['job_title']??'')?></div><?php endif;?>
    </div>
    <?php if($photoSrc):?><img class="hero-photo" src="<?=$photoSrc?>" alt=""><?php else:?><div class="hero-photo-ph"><?=strtoupper(substr($firstName,0,1))?></div><?php endif;?>
  </div>
</div>

<div class="contact-bar">
  <?php if($b['email']):?><span class="c-item"><?=h($b['email'])?></span><?php endif;?>
  <?php if($b['phone']):?><span class="c-item"><?=h($b['phone'])?></span><?php endif;?>
  <?php if($b['city']||$b['state']):?><span class="c-item"><?=h(implode(', ',array_filter([$b['city'],$b['state']])))?></span><?php endif;?>
  <?php if($b['linkedin']):?><span class="c-item"><?=h(preg_replace('#https?://(www\.)?#','',$b['linkedin']))?></span><?php endif;?>
  <?php if($b['github']):?><span class="c-item"><?=h(preg_replace('#https?://(www\.)?#','',$b['github']))?></span><?php endif;?>
</div>

<div class="body">
  <div class="col-main">
    <?php if($b['objective']&&$vis['objective']):?><div class="m-sec"><div class="m-sec-title"><span class="bar"></span>About</div><p class="obj-text"><?=nl2($b['objective'])?></p></div><?php endif;?>
    <?php if(!empty($exp)&&$vis['experience']):?>
    <div class="m-sec"><div class="m-sec-title"><span class="bar"></span>Experience</div>
    <?php foreach($exp as $e):if(!$e['job_title'])continue;?><div class="entry"><div class="e-row"><span class="e-title"><?=h($e['job_title'])?></span><span class="e-date"><?=h($e['date_from'])?><?=$e['date_from']?' – ':''?><?=!empty($e['is_current'])?'Now':h($e['date_to'])?></span></div><div class="e-sub"><?=h($e['company'])?><?=$e['location']?' · '.h($e['location']):''?></div><?php if($e['description']):?><div class="e-desc"><?=nl2($e['description'])?></div><?php endif;?></div><?php endforeach;?>
    </div><?php endif;?>
    <?php if(!empty($ext['projects'])&&$vis['projects']):?>
    <div class="m-sec"><div class="m-sec-title"><span class="bar"></span>Projects</div>
    <?php foreach($ext['projects'] as $p):if(!$p['name'])continue;?><div class="entry"><div class="e-row"><span class="e-title"><?=h($p['name'])?></span><?php if($p['tech_stack']):?><span style="font-size:.68em;color:#9ca3af;font-weight:500"><?=h($p['tech_stack'])?></span><?php endif;?></div><?php if($p['description']):?><div class="e-desc"><?=nl2($p['description'])?></div><?php endif;?></div><?php endforeach;?>
    </div><?php endif;?>
  </div>
  <div class="col-side">
    <?php if(!empty($sk)&&$vis['skills']):?>
    <div class="s-sec"><div class="s-sec-title">Skills</div>
    <?php foreach($sk as $s):if(!$s['name'])continue;?><div class="skill-item"><div class="skill-name"><?=h($s['name'])?></div><div class="skill-bar"><div class="skill-fill" style="width:<?=(int)($s['level']??80)?>%"></div></div></div><?php endforeach;?></div>
    <?php endif;?>
    <?php if(!empty($edu)&&$vis['education']):?>
    <div class="s-sec"><div class="s-sec-title">Education</div>
    <?php foreach($edu as $e):if(!$e['degree'])continue;?><div class="edu-item"><div class="edu-deg"><?=h($e['degree'])?></div><div class="edu-sub"><?=h($e['institution'])?></div><div class="edu-sub"><?=h($e['year_to'])?><?=$e['grade']?' · '.h($e['grade']):''?></div></div><?php endforeach;?></div>
    <?php endif;?>
    <?php if(!empty($ext['certifications'])&&$vis['certifications']):?>
    <div class="s-sec"><div class="s-sec-title">Certifications</div>
    <?php foreach($ext['certifications'] as $c):if(!$c['name'])continue;?><div class="ach-item"><strong><?=h($c['name'])?></strong><?=$c['issuer']?'<br>'.h($c['issuer']):''?><?=$c['date']?' · '.h($c['date']):''?></div><?php endforeach;?></div>
    <?php endif;?>
    <?php if(!empty($ext['achievements'])&&$vis['achievements']):?>
    <div class="s-sec"><div class="s-sec-title">Achievements</div>
    <?php foreach($ext['achievements'] as $a):if(!$a['title'])continue;?><div class="ach-item"><?=h($a['title'])?><?=$a['year']?' ('.$a['year'].')':''?></div><?php endforeach;?></div>
    <?php endif;?>
    <?php if($per['languages']):?><div class="s-sec"><div class="s-sec-title">Languages</div><?php foreach(explode(',',$per['languages']) as $l):if(trim($l)):?><span class="badge"><?=h(trim($l))?></span><?php endif;endforeach;?></div><?php endif;?>
  </div>
</div>
</div></body></html>
