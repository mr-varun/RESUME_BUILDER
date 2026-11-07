<?php
// Fresh — Soft gradients, rounded cards, warm pastels, modern approachable look
$b=$d['basic'];$edu=$d['education'];$exp=$d['experience'];$sk=$d['skills'];$per=$d['personal'];$ext=$d['extras'];$sty=$d['styling'];$vis=$sty['sections_visible'];
$acc=htmlspecialchars($sty['accent_color']??'#059669',ENT_QUOTES);
$fs=(int)($sty['font_size']??13);$ff=htmlspecialchars($sty['font_family']??'Inter',ENT_QUOTES);
function h($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function nl2($v){return nl2br(h($v));}
$photoSrc=!empty($b['photo'])?(defined('UPLOAD_URL')?UPLOAD_URL.h($b['photo']):'../uploads/photos/'.h($b['photo'])):'';
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title><?=h($b['full_name'])?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}html{font-size:<?=$fs?>px}
body{font-family:'<?=$ff?>',sans-serif;background:#f0fdf4;color:#1e293b;line-height:1.6;-webkit-font-smoothing:antialiased}
.page{max-width:210mm;margin:0 auto;min-height:297mm;background:#f0fdf4}
/* Soft gradient header */
.hdr{background:linear-gradient(135deg,<?=$acc?> 0%,<?=$acc?>cc 60%,<?=$acc?>88 100%);padding:28px 32px;display:flex;align-items:center;gap:20px;border-radius:0 0 20px 20px;margin-bottom:0;position:relative;overflow:hidden}
.hdr::before{content:'';position:absolute;right:-30px;top:-30px;width:180px;height:180px;background:rgba(255,255,255,.08);border-radius:50%}
.hdr::after{content:'';position:absolute;right:40px;bottom:-50px;width:120px;height:120px;background:rgba(255,255,255,.05);border-radius:50%}
.hdr-photo{width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.6);flex-shrink:0;position:relative;z-index:1}
.hdr-photo-ph{width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.2);border:3px solid rgba(255,255,255,.4);display:flex;align-items:center;justify-content:center;font-size:28px;color:rgba(255,255,255,.8);flex-shrink:0;position:relative;z-index:1}
.hdr-text{flex:1;position:relative;z-index:1}
.hdr-name{font-size:1.9em;font-weight:800;color:#fff;letter-spacing:-.02em;line-height:1.1}
.hdr-role{font-size:.84em;color:rgba(255,255,255,.8);margin-top:3px;margin-bottom:10px}
.hdr-pills{display:flex;flex-wrap:wrap;gap:5px}
.hdr-pill{background:rgba(255,255,255,.2);backdrop-filter:blur(4px);color:rgba(255,255,255,.9);font-size:.72em;padding:3px 10px;border-radius:20px;font-weight:500}
/* Body */
.body{padding:20px 24px;display:grid;grid-template-columns:1fr 220px;gap:16px}
/* Cards */
.card{background:#fff;border-radius:12px;padding:16px 18px;margin-bottom:12px;box-shadow:0 1px 4px rgba(5,150,105,.08)}
.card:last-child{margin-bottom:0}
.card-title{font-size:.72em;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:<?=$acc?>;margin-bottom:10px;display:flex;align-items:center;gap:6px}
.card-title::before{content:'';width:3px;height:14px;background:<?=$acc?>;border-radius:2px}
/* Entries */
.entry{margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #f0fdf4}
.entry:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
.e-top{display:flex;justify-content:space-between;align-items:flex-start;gap:8px}
.e-title{font-weight:700;font-size:.87em;color:#1e293b}
.e-date{font-size:.71em;color:#fff;background:<?=$acc?>;padding:2px 8px;border-radius:10px;white-space:nowrap;flex-shrink:0;font-weight:500}
.e-sub{font-size:.78em;color:#64748b;margin:2px 0}
.e-desc{font-size:.78em;color:#475569;line-height:1.65}
.obj-text{font-size:.84em;color:#374151;line-height:1.75}
/* Sidebar cards */
.s-card{background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:12px;box-shadow:0 1px 4px rgba(5,150,105,.08)}
.s-card:last-child{margin-bottom:0}
.s-card-title{font-size:.68em;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:<?=$acc?>;margin-bottom:8px}
/* Skill bars */
.skill-row{margin-bottom:7px}
.skill-label{display:flex;justify-content:space-between;font-size:.75em;color:#334155;font-weight:500;margin-bottom:3px}
.skill-bar{height:6px;background:#dcfce7;border-radius:3px}
.skill-fill{height:6px;background:<?=$acc?>;border-radius:3px}
.s-item{font-size:.76em;color:#475569;margin-bottom:4px;display:flex;align-items:flex-start;gap:5px}
.s-item::before{content:'✓';color:<?=$acc?>;font-weight:700;flex-shrink:0}
.s-tag{display:inline-block;font-size:.7em;padding:2px 8px;border-radius:10px;background:#dcfce7;color:<?=$acc?>;font-weight:600;margin:2px 2px 2px 0}
@media print{body,html{background:#fff !important;print-color-adjust:exact;-webkit-print-color-adjust:exact}.hdr,.card,.s-card{box-shadow:none}.body{gap:12px}}
</style></head><body><div class="page">

<div class="hdr">
  <?php if($photoSrc):?><img class="hdr-photo" src="<?=$photoSrc?>" alt=""><?php else:?><div class="hdr-photo-ph"><?=strtoupper(substr($b['full_name']?:'?',0,1))?></div><?php endif;?>
  <div class="hdr-text">
    <div class="hdr-name"><?=h($b['full_name']?:'Your Name')?></div>
    <?php if(!empty($exp)):?><div class="hdr-role"><?=h($exp[0]['job_title']??'')?></div><?php endif;?>
    <div class="hdr-pills">
      <?php if($b['email']):?><span class="hdr-pill"><?=h($b['email'])?></span><?php endif;?>
      <?php if($b['phone']):?><span class="hdr-pill"><?=h($b['phone'])?></span><?php endif;?>
      <?php if($b['city']||$b['state']):?><span class="hdr-pill"><?=h(implode(', ',array_filter([$b['city'],$b['state']])))?></span><?php endif;?>
      <?php if($b['linkedin']):?><span class="hdr-pill"><?=h(preg_replace('#https?://(www\.)?#','',$b['linkedin']))?></span><?php endif;?>
    </div>
  </div>
</div>

<div class="body">
  <div class="col-main">
    <?php if($b['objective']&&$vis['objective']):?>
    <div class="card"><div class="card-title">About Me</div><p class="obj-text"><?=nl2($b['objective'])?></p></div>
    <?php endif;?>
    <?php if(!empty($exp)&&$vis['experience']):?>
    <div class="card"><div class="card-title">Experience</div>
    <?php foreach($exp as $e):if(!$e['job_title'])continue;?>
    <div class="entry">
      <div class="e-top"><span class="e-title"><?=h($e['job_title'])?></span><span class="e-date"><?=h($e['date_from'])?><?=$e['date_from']?' – ':''?><?=!empty($e['is_current'])?'Present':h($e['date_to'])?></span></div>
      <div class="e-sub"><?=h($e['company'])?><?=$e['location']?' · '.h($e['location']):''?><?=$e['employment_type']?' · '.h($e['employment_type']):''?></div>
      <?php if($e['description']):?><div class="e-desc"><?=nl2($e['description'])?></div><?php endif;?>
    </div>
    <?php endforeach;?></div>
    <?php endif;?>
    <?php if(!empty($edu)&&$vis['education']):?>
    <div class="card"><div class="card-title">Education</div>
    <?php foreach($edu as $e):if(!$e['degree'])continue;?>
    <div class="entry">
      <div class="e-top"><span class="e-title"><?=h($e['degree'])?></span><span class="e-date"><?=h($e['year_to'])?></span></div>
      <div class="e-sub"><?=h($e['institution'])?><?=$e['grade']?' · '.h($e['grade']):''?></div>
    </div>
    <?php endforeach;?></div>
    <?php endif;?>
    <?php if(!empty($ext['projects'])&&$vis['projects']):?>
    <div class="card"><div class="card-title">Projects</div>
    <?php foreach($ext['projects'] as $p):if(!$p['name'])continue;?><div class="entry"><div class="e-top"><span class="e-title"><?=h($p['name'])?><?=$p['role']?' — '.h($p['role']):''?></span></div><?php if($p['tech_stack']):?><div class="e-sub"><?=h($p['tech_stack'])?></div><?php endif;?><?php if($p['description']):?><div class="e-desc"><?=nl2($p['description'])?></div><?php endif;?></div><?php endforeach;?></div>
    <?php endif;?>
  </div>

  <div class="col-side">
    <?php if(!empty($sk)&&$vis['skills']):?>
    <div class="s-card"><div class="s-card-title">Skills</div>
    <?php foreach($sk as $s):if(!$s['name'])continue;?><div class="skill-row"><div class="skill-label"><span><?=h($s['name'])?></span><span><?=(int)($s['level']??80)?>%</span></div><div class="skill-bar"><div class="skill-fill" style="width:<?=(int)($s['level']??80)?>%"></div></div></div><?php endforeach;?></div>
    <?php endif;?>
    <?php if(!empty($ext['certifications'])&&$vis['certifications']):?>
    <div class="s-card"><div class="s-card-title">Certifications</div>
    <?php foreach($ext['certifications'] as $c):if(!$c['name'])continue;?><div class="s-item"><?=h($c['name'])?><?=$c['issuer']?' — '.h($c['issuer']):''?><?=$c['date']?' ('.$c['date'].')':''?></div><?php endforeach;?></div>
    <?php endif;?>
    <?php if(!empty($ext['achievements'])&&$vis['achievements']):?>
    <div class="s-card"><div class="s-card-title">Achievements</div>
    <?php foreach($ext['achievements'] as $a):if(!$a['title'])continue;?><div class="s-item"><?=h($a['title'])?><?=$a['year']?' ('.$a['year'].')':''?></div><?php endforeach;?></div>
    <?php endif;?>
    <?php if($per['languages']):?><div class="s-card"><div class="s-card-title">Languages</div><?php foreach(explode(',',$per['languages']) as $l):if(trim($l)):?><span class="s-tag"><?=h(trim($l))?></span><?php endif;endforeach;?></div><?php endif;?>
    <?php if($per['hobbies']):?><div class="s-card"><div class="s-card-title">Interests</div><?php foreach(explode(',',$per['hobbies']) as $h2):if(trim($h2)):?><div class="s-item"><?=h(trim($h2))?></div><?php endif;endforeach;?></div><?php endif;?>
    <?php if(!empty($ext['references'])&&$vis['references']):?>
    <div class="s-card"><div class="s-card-title">References</div>
    <?php foreach($ext['references'] as $r):if(!$r['name'])continue;?><div style="margin-bottom:8px;font-size:.77em"><strong style="color:#1e293b"><?=h($r['name'])?></strong><div style="color:#64748b"><?=h($r['designation'])?><?=$r['company']?', '.h($r['company']):''?></div><?php if($r['phone']):?><div style="color:#64748b"><?=h($r['phone'])?></div><?php endif;?></div><?php endforeach;?></div>
    <?php endif;?>
  </div>
</div>
</div></body></html>
