<?php
// Corporate — Navy top bar, three-info header row, two-column formal layout
$b=$d['basic'];$edu=$d['education'];$exp=$d['experience'];$sk=$d['skills'];$per=$d['personal'];$ext=$d['extras'];$sty=$d['styling'];$vis=$sty['sections_visible'];
$acc=htmlspecialchars($sty['accent_color']??'#1e3a5f',ENT_QUOTES);
$fs=(int)($sty['font_size']??13);$ff=htmlspecialchars($sty['font_family']??'Inter',ENT_QUOTES);
function h($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function nl2($v){return nl2br(h($v));}
$photoSrc=!empty($b['photo'])?(defined('UPLOAD_URL')?UPLOAD_URL.h($b['photo']):'../uploads/photos/'.h($b['photo'])):'';
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title><?=h($b['full_name'])?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}html{font-size:<?=$fs?>px}
body{font-family:'<?=$ff?>',sans-serif;background:#fff;color:#1e293b;line-height:1.55}
.page{max-width:210mm;margin:0 auto;min-height:297mm}
/* Top navy bar */
.hdr{background:<?=$acc?>;color:#fff;padding:20px 32px;display:flex;align-items:center;gap:18px}
.hdr-photo{width:72px;height:72px;border-radius:4px;object-fit:cover;border:2px solid rgba(255,255,255,.3);flex-shrink:0}
.hdr-photo-ph{width:72px;height:72px;border-radius:4px;background:rgba(255,255,255,.1);border:2px solid rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:26px;color:rgba(255,255,255,.6);flex-shrink:0}
.hdr-text{flex:1}
.hdr-name{font-size:1.7em;font-weight:800;letter-spacing:-.01em;color:#fff}
.hdr-role{font-size:.82em;color:rgba(255,255,255,.7);margin-top:2px;font-weight:400}
/* Info bar */
.info-bar{background:#1a3254;display:flex;justify-content:center;gap:0;flex-wrap:wrap}
.info-bar-item{display:flex;align-items:center;gap:6px;padding:9px 20px;font-size:.74em;color:rgba(255,255,255,.75);border-right:1px solid rgba(255,255,255,.1)}
.info-bar-item:last-child{border-right:none}
.info-bar-item span{color:#fff;font-weight:500}
/* Gold accent line */
.accent-line{height:3px;background:linear-gradient(to right,<?=$acc?>,#60a5fa,<?=$acc?>)}
/* Body */
.body{display:grid;grid-template-columns:3fr 2fr;gap:0;padding:0}
.col-main{padding:22px 28px 22px 32px;border-right:1px solid #e2e8f0}
.col-side{padding:22px 20px 22px 20px;background:#f8fafc}
/* Sections */
.sec-title{font-size:.68em;font-weight:800;text-transform:uppercase;letter-spacing:.14em;color:<?=$acc?>;margin-bottom:10px;padding-bottom:5px;border-bottom:2px solid <?=$acc?>}
.entry{margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #f1f5f9}
.entry:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
.e-row{display:flex;justify-content:space-between;align-items:flex-start;gap:8px}
.e-title{font-weight:700;font-size:.88em;color:#0f172a;line-height:1.3}
.e-date{font-size:.72em;color:#fff;background:<?=$acc?>;padding:1px 7px;border-radius:2px;white-space:nowrap;flex-shrink:0;font-weight:500}
.e-sub{font-size:.79em;color:#64748b;margin:2px 0}
.e-desc{font-size:.78em;color:#475569;line-height:1.65}
.obj-text{font-size:.84em;color:#334155;line-height:1.75}
/* Sidebar */
.sb-sec{margin-bottom:16px}
.sb-title{font-size:.66em;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:<?=$acc?>;margin-bottom:8px;padding-bottom:4px;border-bottom:1px solid #e2e8f0}
.skill-bar-row{margin-bottom:7px}
.skill-label{display:flex;justify-content:space-between;font-size:.76em;color:#334155;margin-bottom:3px}
.skill-bar{height:5px;background:#e2e8f0;border-radius:3px}
.skill-bar-fill{height:5px;background:<?=$acc?>;border-radius:3px}
.side-item{font-size:.77em;color:#475569;margin-bottom:4px;display:flex;align-items:flex-start;gap:5px}
.side-item::before{content:'▸';color:<?=$acc?>;flex-shrink:0;margin-top:1px}
.edu-item{margin-bottom:8px;padding:8px 10px;background:#fff;border-radius:4px;border-left:3px solid <?=$acc?>}
.edu-deg{font-weight:600;font-size:.82em;color:#0f172a}
.edu-sub{font-size:.74em;color:#64748b;margin-top:1px}
.cert-item{font-size:.76em;color:#475569;margin-bottom:6px;padding-left:10px;border-left:2px solid <?=$acc?>44}
.cert-name{font-weight:500;color:#1e293b}
@media print{body{print-color-adjust:exact;-webkit-print-color-adjust:exact}}
</style></head><body><div class="page">

<div class="hdr">
  <?php if($photoSrc):?><img class="hdr-photo" src="<?=$photoSrc?>" alt=""><?php else:?><div class="hdr-photo-ph">&#9786;</div><?php endif;?>
  <div class="hdr-text">
    <div class="hdr-name"><?=h($b['full_name']?:'Your Name')?></div>
    <?php if(!empty($exp)):?><div class="hdr-role"><?=h($exp[0]['job_title']??'')?><?=$exp[0]['employment_type']?' · '.h($exp[0]['employment_type']??''):''?></div><?php endif;?>
  </div>
</div>

<div class="info-bar">
  <?php if($b['email']):?><div class="info-bar-item">Email <span><?=h($b['email'])?></span></div><?php endif;?>
  <?php if($b['phone']):?><div class="info-bar-item">Phone <span><?=h($b['phone'])?></span></div><?php endif;?>
  <?php if($b['city']||$b['state']):?><div class="info-bar-item">Location <span><?=h(implode(', ',array_filter([$b['city'],$b['state']])))?></span></div><?php endif;?>
  <?php if($b['linkedin']):?><div class="info-bar-item">LinkedIn <span><?=h(preg_replace('#https?://(www\.)?#','',$b['linkedin']))?></span></div><?php endif;?>
</div>
<div class="accent-line"></div>

<div class="body">
  <div class="col-main">
    <?php if($b['objective']&&$vis['objective']):?>
    <div style="margin-bottom:18px"><div class="sec-title" style="margin-bottom:8px">Professional Summary</div><p class="obj-text"><?=nl2($b['objective'])?></p></div>
    <?php endif;?>
    <?php if(!empty($exp)&&$vis['experience']):?>
    <div style="margin-bottom:18px"><div class="sec-title">Professional Experience</div>
    <?php foreach($exp as $e):if(!$e['job_title'])continue;?>
    <div class="entry">
      <div class="e-row"><span class="e-title"><?=h($e['job_title'])?></span><span class="e-date"><?=h($e['date_from'])?><?=$e['date_from']?' – ':''?><?=!empty($e['is_current'])?'Present':h($e['date_to'])?></span></div>
      <div class="e-sub"><?=h($e['company'])?><?=$e['location']?', '.h($e['location']):''?></div>
      <?php if($e['description']):?><div class="e-desc"><?=nl2($e['description'])?></div><?php endif;?>
    </div>
    <?php endforeach;?></div>
    <?php endif;?>
    <?php if(!empty($ext['projects'])&&$vis['projects']):?>
    <div style="margin-bottom:18px"><div class="sec-title">Key Projects</div>
    <?php foreach($ext['projects'] as $p):if(!$p['name'])continue;?><div class="entry"><div class="e-row"><span class="e-title"><?=h($p['name'])?><?=$p['role']?' — '.h($p['role']):''?></span></div><?php if($p['tech_stack']):?><div class="e-sub"><?=h($p['tech_stack'])?></div><?php endif;?><?php if($p['description']):?><div class="e-desc"><?=nl2($p['description'])?></div><?php endif;?></div><?php endforeach;?></div>
    <?php endif;?>
    <?php if(!empty($ext['achievements'])&&$vis['achievements']):?>
    <div><div class="sec-title">Achievements</div>
    <?php foreach($ext['achievements'] as $a):if(!$a['title'])continue;?><div class="entry"><div class="e-row"><span class="e-title"><?=h($a['title'])?></span><?php if($a['year']):?><span class="e-date"><?=h($a['year'])?></span><?php endif;?></div><?php if($a['description']):?><div class="e-desc"><?=h($a['description'])?></div><?php endif;?></div><?php endforeach;?></div>
    <?php endif;?>
  </div>

  <div class="col-side">
    <?php if(!empty($sk)&&$vis['skills']):?>
    <div class="sb-sec"><div class="sb-title">Core Skills</div>
    <?php foreach($sk as $s):if(!$s['name'])continue;?><div class="skill-bar-row"><div class="skill-label"><span><?=h($s['name'])?></span><span><?=(int)($s['level']??80)?>%</span></div><div class="skill-bar"><div class="skill-bar-fill" style="width:<?=(int)($s['level']??80)?>%"></div></div></div><?php endforeach;?></div>
    <?php endif;?>
    <?php if(!empty($edu)&&$vis['education']):?>
    <div class="sb-sec"><div class="sb-title">Education</div>
    <?php foreach($edu as $e):if(!$e['degree'])continue;?><div class="edu-item"><div class="edu-deg"><?=h($e['degree'])?></div><div class="edu-sub"><?=h($e['institution'])?></div><div class="edu-sub"><?=h($e['year_from'])?><?=$e['year_from']?' – ':''?><?=h($e['year_to'])?><?=$e['grade']?' · '.h($e['grade']):''?></div></div><?php endforeach;?></div>
    <?php endif;?>
    <?php if(!empty($ext['certifications'])&&$vis['certifications']):?>
    <div class="sb-sec"><div class="sb-title">Certifications</div>
    <?php foreach($ext['certifications'] as $c):if(!$c['name'])continue;?><div class="cert-item"><div class="cert-name"><?=h($c['name'])?></div><div style="color:#64748b;font-size:.9em"><?=h($c['issuer'])?><?=$c['date']?' · '.h($c['date']):''?></div></div><?php endforeach;?></div>
    <?php endif;?>
    <?php if($per['languages']):?><div class="sb-sec"><div class="sb-title">Languages</div><?php foreach(explode(',',$per['languages']) as $l):if(trim($l)):?><div class="side-item"><?=h(trim($l))?></div><?php endif;endforeach;?></div><?php endif;?>
    <?php if($per['hobbies']):?><div class="sb-sec"><div class="sb-title">Interests</div><div class="side-item" style="flex-wrap:wrap;line-height:1.6"><?=h($per['hobbies'])?></div></div><?php endif;?>
    <?php if(!empty($ext['references'])&&$vis['references']):?>
    <div class="sb-sec"><div class="sb-title">References</div>
    <?php foreach($ext['references'] as $r):if(!$r['name'])continue;?><div class="cert-item"><div class="cert-name"><?=h($r['name'])?></div><div style="color:#64748b;font-size:.9em"><?=h($r['designation'])?><?=$r['company']?', '.h($r['company']):''?></div><div style="color:#64748b;font-size:.85em"><?=h($r['phone'])?></div></div><?php endforeach;?></div>
    <?php endif;?>
  </div>
</div>
</div></body></html>
