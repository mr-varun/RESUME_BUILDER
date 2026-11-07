<?php
// Timeline — Vertical timeline for experience, info sidebar on left
$b=$d['basic'];$edu=$d['education'];$exp=$d['experience'];$sk=$d['skills'];$per=$d['personal'];$ext=$d['extras'];$sty=$d['styling'];$vis=$sty['sections_visible'];
$acc=htmlspecialchars($sty['accent_color']??'#0891b2',ENT_QUOTES);
$fs=(int)($sty['font_size']??13);$ff=htmlspecialchars($sty['font_family']??'Inter',ENT_QUOTES);
function h($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function nl2($v){return nl2br(h($v));}
$photoSrc=!empty($b['photo'])?(defined('UPLOAD_URL')?UPLOAD_URL.h($b['photo']):'../uploads/photos/'.h($b['photo'])):'';
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title><?=h($b['full_name'])?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html{font-size:<?=$fs?>px}
body{font-family:'<?=$ff?>',sans-serif;background:#fff;color:#1e293b;line-height:1.55}
.page{max-width:210mm;margin:0 auto;min-height:297mm;display:flex}
/* Left sidebar */
.sidebar{width:190px;flex-shrink:0;background:#f8fafc;border-right:1px solid #e2e8f0;padding:24px 18px;display:flex;flex-direction:column;gap:0}
.photo{width:70px;height:70px;border-radius:8px;object-fit:cover;border:2px solid <?=$acc?>;margin-bottom:14px;display:block}
.photo-ph{width:70px;height:70px;border-radius:8px;background:<?=$acc?>20;border:2px solid <?=$acc?>;display:flex;align-items:center;justify-content:center;font-size:24px;color:<?=$acc?>;margin-bottom:14px}
.sb-name{font-size:1em;font-weight:700;color:#0f172a;line-height:1.2;margin-bottom:2px}
.sb-role{font-size:.74em;color:<?=$acc?>;font-weight:500;margin-bottom:14px}
.sb-sec-title{font-size:.62em;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#94a3b8;margin:14px 0 7px;padding-bottom:4px;border-bottom:1px solid #e2e8f0}
.sb-item{font-size:.74em;color:#475569;margin-bottom:4px;display:flex;align-items:flex-start;gap:5px;word-break:break-all}
.sb-dot{width:4px;height:4px;background:<?=$acc?>;border-radius:50%;margin-top:6px;flex-shrink:0}
.sb-skill{margin-bottom:6px}
.sb-skill-label{display:flex;justify-content:space-between;font-size:.72em;color:#334155;margin-bottom:3px}
.sb-bar{height:4px;background:#e2e8f0;border-radius:2px}
.sb-bar-fill{height:4px;background:<?=$acc?>;border-radius:2px}
.sb-tag{display:inline-block;font-size:.68em;padding:2px 7px;border-radius:3px;background:<?=$acc?>15;color:<?=$acc?>;margin:2px 2px 2px 0;font-weight:500}
/* Main */
.main{flex:1;padding:24px 24px 24px 28px;overflow:hidden}
.m-sec-title{font-size:.72em;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:<?=$acc?>;margin-bottom:14px;margin-top:16px;display:flex;align-items:center;gap:8px}
.m-sec-title:first-child{margin-top:0}
.m-sec-title::before{content:'';width:4px;height:16px;background:<?=$acc?>;border-radius:2px;flex-shrink:0}
.m-sec-title::after{content:'';flex:1;height:1px;background:#e2e8f0}
/* Timeline */
.timeline{position:relative;padding-left:24px}
.timeline::before{content:'';position:absolute;left:7px;top:6px;bottom:6px;width:2px;background:linear-gradient(to bottom,<?=$acc?>,<?=$acc?>22)}
.tl-item{position:relative;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid #f1f5f9}
.tl-item:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
.tl-dot{position:absolute;left:-24px;top:4px;width:14px;height:14px;border-radius:50%;background:<?=$acc?>;border:2px solid #fff;box-shadow:0 0 0 2px <?=$acc?>;z-index:1}
.tl-date{font-size:.72em;color:<?=$acc?>;font-weight:600;margin-bottom:2px}
.tl-title{font-weight:700;font-size:.88em;color:#0f172a;margin-bottom:1px}
.tl-sub{font-size:.79em;color:#64748b;margin-bottom:3px}
.tl-desc{font-size:.78em;color:#475569;line-height:1.65}
/* Edu grid */
.edu-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.edu-item{background:#f8fafc;border-radius:6px;padding:10px 12px;border-left:3px solid <?=$acc?>}
.edu-deg{font-weight:600;font-size:.83em;color:#0f172a}
.edu-inst{font-size:.76em;color:#64748b;margin-top:2px}
.edu-meta{font-size:.72em;color:<?=$acc?>;font-weight:500;margin-top:2px}
.obj-text{font-size:.83em;color:#334155;line-height:1.75;margin-bottom:2px}
/* Certs/achiev row */
.item-list{display:flex;flex-direction:column;gap:5px}
.item-row{display:flex;justify-content:space-between;align-items:baseline;font-size:.8em;padding:5px 0;border-bottom:1px solid #f1f5f9}
.item-row:last-child{border-bottom:none}
.item-name{font-weight:500;color:#1e293b}
.item-meta{font-size:.9em;color:#94a3b8;white-space:nowrap;margin-left:8px}
@media print{body{print-color-adjust:exact;-webkit-print-color-adjust:exact}}
</style></head><body><div class="page">
<div class="sidebar">
  <?php if($photoSrc):?><img class="photo" src="<?=$photoSrc?>" alt=""><?php else:?><div class="photo-ph">&#9786;</div><?php endif;?>
  <div class="sb-name"><?=h($b['full_name']?:'Your Name')?></div>
  <?php if(!empty($exp)):?><div class="sb-role"><?=h($exp[0]['job_title']??'')?></div><?php endif;?>
  <div class="sb-sec-title">Contact</div>
  <?php foreach(['phone','email'] as $k):if($b[$k]):?><div class="sb-item"><span class="sb-dot"></span><?=h($b[$k])?></div><?php endif;endforeach;?>
  <?php if($b['city']||$b['state']):?><div class="sb-item"><span class="sb-dot"></span><?=h(implode(', ',array_filter([$b['city'],$b['state']])))?></div><?php endif;?>
  <?php if($b['linkedin']):?><div class="sb-item"><span class="sb-dot"></span><?=h(preg_replace('#https?://(www\.)?#','',$b['linkedin']))?></div><?php endif;?>
  <?php if(!empty($sk)&&$vis['skills']):?>
  <div class="sb-sec-title">Skills</div>
  <?php foreach($sk as $s):if(!$s['name'])continue;?>
  <div class="sb-skill">
    <div class="sb-skill-label"><span><?=h($s['name'])?></span><span><?=(int)($s['level']??80)?>%</span></div>
    <div class="sb-bar"><div class="sb-bar-fill" style="width:<?=(int)($s['level']??80)?>%"></div></div>
  </div>
  <?php endforeach;?>
  <?php endif;?>
  <?php if($per['languages']):?><div class="sb-sec-title">Languages</div><?php foreach(explode(',',$per['languages']) as $l):if(trim($l)):?><span class="sb-tag"><?=h(trim($l))?></span><?php endif;endforeach;?><?php endif;?>
  <?php if($per['hobbies']):?><div class="sb-sec-title">Interests</div><div class="sb-item"><?=h($per['hobbies'])?></div><?php endif;?>
</div>

<div class="main">
  <?php if($b['objective']&&$vis['objective']):?>
  <div class="m-sec-title">Summary</div>
  <p class="obj-text"><?=nl2($b['objective'])?></p>
  <?php endif;?>
  <?php if(!empty($exp)&&$vis['experience']):?>
  <div class="m-sec-title">Experience</div>
  <div class="timeline">
    <?php foreach($exp as $e):if(!$e['job_title'])continue;?>
    <div class="tl-item">
      <div class="tl-dot"></div>
      <div class="tl-date"><?=h($e['date_from'])?><?=$e['date_from']?' – ':''?><?=!empty($e['is_current'])?'Present':h($e['date_to'])?></div>
      <div class="tl-title"><?=h($e['job_title'])?></div>
      <div class="tl-sub"><?=h($e['company'])?><?=$e['location']?' · '.h($e['location']):''?><?=$e['employment_type']?' · '.h($e['employment_type']):''?></div>
      <?php if($e['description']):?><div class="tl-desc"><?=nl2($e['description'])?></div><?php endif;?>
    </div>
    <?php endforeach;?>
  </div>
  <?php endif;?>
  <?php if(!empty($edu)&&$vis['education']):?>
  <div class="m-sec-title">Education</div>
  <div class="edu-grid">
    <?php foreach($edu as $e):if(!$e['degree'])continue;?>
    <div class="edu-item">
      <div class="edu-deg"><?=h($e['degree'])?></div>
      <div class="edu-inst"><?=h($e['institution'])?></div>
      <div class="edu-meta"><?=h($e['year_from'])?><?=$e['year_from']?'–':''?><?=h($e['year_to'])?><?=$e['grade']?' · '.h($e['grade']):''?></div>
    </div>
    <?php endforeach;?>
  </div>
  <?php endif;?>
  <?php if(!empty($ext['projects'])&&$vis['projects']):?>
  <div class="m-sec-title">Projects</div>
  <div class="timeline">
    <?php foreach($ext['projects'] as $p):if(!$p['name'])continue;?>
    <div class="tl-item"><div class="tl-dot"></div><div class="tl-title"><?=h($p['name'])?><?=$p['role']?' — '.h($p['role']):''?></div><?php if($p['tech_stack']):?><div class="tl-sub"><?=h($p['tech_stack'])?></div><?php endif;?><?php if($p['description']):?><div class="tl-desc"><?=nl2($p['description'])?></div><?php endif;?></div>
    <?php endforeach;?>
  </div>
  <?php endif;?>
  <?php if(!empty($ext['certifications'])&&$vis['certifications']):?>
  <div class="m-sec-title">Certifications</div>
  <div class="item-list">
    <?php foreach($ext['certifications'] as $c):if(!$c['name'])continue;?><div class="item-row"><span class="item-name"><?=h($c['name'])?><?=$c['issuer']?' — '.h($c['issuer']):''?></span><span class="item-meta"><?=h($c['date'])?></span></div><?php endforeach;?>
  </div>
  <?php endif;?>
  <?php if(!empty($ext['achievements'])&&$vis['achievements']):?>
  <div class="m-sec-title">Achievements</div>
  <div class="item-list">
    <?php foreach($ext['achievements'] as $a):if(!$a['title'])continue;?><div class="item-row"><span class="item-name"><?=h($a['title'])?></span><span class="item-meta"><?=h($a['year'])?></span></div><?php endforeach;?>
  </div>
  <?php endif;?>
</div>
</div></body></html>
