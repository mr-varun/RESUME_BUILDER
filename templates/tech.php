<?php
// Tech/Dev — Dark header, code-inspired, monospace accents, badge skills
$b=$d['basic'];$edu=$d['education'];$exp=$d['experience'];$sk=$d['skills'];$per=$d['personal'];$ext=$d['extras'];$sty=$d['styling'];$vis=$sty['sections_visible'];
$acc=htmlspecialchars($sty['accent_color']??'#22d3ee',ENT_QUOTES);
$fs=(int)($sty['font_size']??13);$ff=htmlspecialchars($sty['font_family']??'Inter',ENT_QUOTES);
function h($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function nl2($v){return nl2br(h($v));}
$photoSrc=!empty($b['photo'])?(defined('UPLOAD_URL')?UPLOAD_URL.h($b['photo']):'../uploads/photos/'.h($b['photo'])):'';
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title><?=h($b['full_name'])?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html{font-size:<?=$fs?>px}
body{font-family:'<?=$ff?>',monospace,sans-serif;background:#fff;color:#0f172a;line-height:1.55}
.page{max-width:210mm;margin:0 auto;min-height:297mm}
/* Dark header */
.hdr{background:#0f172a;color:#e2e8f0;padding:0;overflow:hidden}
.hdr-inner{padding:24px 32px;display:flex;align-items:flex-start;gap:18px;position:relative}
.hdr-inner::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,<?=$acc?>18 0%,transparent 60%)}
.hdr-photo{width:75px;height:75px;border-radius:6px;object-fit:cover;border:2px solid <?=$acc?>;flex-shrink:0;position:relative;z-index:1}
.hdr-photo-ph{width:75px;height:75px;border-radius:6px;background:<?=$acc?>22;border:2px solid <?=$acc?>;display:flex;align-items:center;justify-content:center;font-size:28px;color:<?=$acc?>;flex-shrink:0;position:relative;z-index:1}
.hdr-text{flex:1;position:relative;z-index:1}
.hdr-prompt{font-family:'JetBrains Mono',monospace;font-size:.7em;color:<?=$acc?>;margin-bottom:3px}
.hdr-prompt::before{content:'> ';opacity:.6}
.hdr-name{font-size:1.9em;font-weight:800;color:#f8fafc;letter-spacing:-.02em;line-height:1;margin-bottom:3px}
.hdr-role{font-family:'JetBrains Mono',monospace;font-size:.75em;color:<?=$acc?>;margin-bottom:10px}
.hdr-links{display:flex;flex-wrap:wrap;gap:6px 14px}
.hdr-link{font-size:.72em;color:#94a3b8;display:flex;align-items:center;gap:4px}
.hdr-link::before{content:'→';color:<?=$acc?>;font-family:monospace}
/* Status bar */
.status-bar{background:#1e293b;padding:6px 32px;display:flex;gap:16px;font-family:'JetBrains Mono',monospace;font-size:.68em;color:#64748b;border-bottom:1px solid <?=$acc?>44}
.status-item{display:flex;align-items:center;gap:4px}
.status-item::before{content:'●';color:<?=$acc?>;font-size:.7em}
/* Body */
.body{padding:24px 32px;display:grid;grid-template-columns:1fr 240px;gap:24px}
/* Sections */
.sec-title{font-family:'JetBrains Mono',monospace;font-size:.72em;font-weight:700;color:<?=$acc?>;margin-bottom:10px;display:flex;align-items:center;gap:6px;margin-top:16px}
.sec-title:first-child{margin-top:0}
.sec-title::before{content:'//';opacity:.5}
.sec-title::after{content:'';flex:1;height:1px;background:<?=$acc?>30}
/* Entries */
.entry{margin-bottom:12px;padding:10px 12px;border-radius:4px;border:1px solid #f1f5f9;background:#fafafa}
.e-top{display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:2px}
.e-title{font-weight:700;font-size:.87em;color:#0f172a}
.e-date{font-family:'JetBrains Mono',monospace;font-size:.68em;color:<?=$acc?>;background:<?=$acc?>12;padding:2px 6px;border-radius:3px;white-space:nowrap;flex-shrink:0}
.e-sub{font-size:.78em;color:#64748b;font-family:'JetBrains Mono',monospace;margin-bottom:3px}
.e-desc{font-size:.78em;color:#475569;line-height:1.65}
/* Skills badges */
.skills-wrap{display:flex;flex-wrap:wrap;gap:5px}
.skill-badge{font-family:'JetBrains Mono',monospace;font-size:.7em;padding:3px 8px;border-radius:3px;border:1px solid <?=$acc?>55;color:<?=$acc?>;background:<?=$acc?>08;font-weight:500}
.skill-badge.expert{background:<?=$acc?>22;border-color:<?=$acc?>}
/* Sidebar */
.sidebar{display:flex;flex-direction:column;gap:0}
.sb-title{font-family:'JetBrains Mono',monospace;font-size:.66em;font-weight:700;color:<?=$acc?>;margin-bottom:8px;margin-top:14px}
.sb-title:first-child{margin-top:0}
.sb-title::before{content:'$ '}
.sb-item{font-size:.74em;color:#475569;margin-bottom:4px;display:flex;gap:5px;align-items:flex-start;word-break:break-all}
.sb-item::before{content:'→';color:<?=$acc?>;font-family:monospace;flex-shrink:0}
.skill-bar-row{margin-bottom:6px}
.skill-bar-lbl{display:flex;justify-content:space-between;font-size:.7em;color:#334155;font-family:'JetBrains Mono',monospace;margin-bottom:3px}
.skill-bar{height:4px;background:#e2e8f0;border-radius:0}
.skill-bar-fill{height:4px;background:<?=$acc?>;border-radius:0}
.obj-text{font-size:.83em;color:#334155;line-height:1.75;margin-bottom:4px;border-left:2px solid <?=$acc?>;padding-left:10px}
.cert-item{font-size:.74em;color:#475569;padding:5px 8px;background:#f8fafc;border-left:2px solid <?=$acc?>;margin-bottom:5px}
.cert-name{font-weight:600;color:#0f172a}
@media print{body{print-color-adjust:exact;-webkit-print-color-adjust:exact}}
</style></head><body><div class="page">

<div class="hdr">
  <div class="hdr-inner">
    <?php if($photoSrc):?><img class="hdr-photo" src="<?=$photoSrc?>" alt=""><?php else:?><div class="hdr-photo-ph"><?=strtoupper(substr($b['full_name']?:'?',0,1))?></div><?php endif;?>
    <div class="hdr-text">
      <div class="hdr-prompt">whoami</div>
      <div class="hdr-name"><?=h($b['full_name']?:'Your Name')?></div>
      <?php if(!empty($exp)):?><div class="hdr-role"><?=h($exp[0]['job_title']??'')?></div><?php endif;?>
      <div class="hdr-links">
        <?php if($b['email']):?><span class="hdr-link"><?=h($b['email'])?></span><?php endif;?>
        <?php if($b['github']):?><span class="hdr-link"><?=h(preg_replace('#https?://(www\.)?#','',$b['github']))?></span><?php endif;?>
        <?php if($b['linkedin']):?><span class="hdr-link"><?=h(preg_replace('#https?://(www\.)?#','',$b['linkedin']))?></span><?php endif;?>
        <?php if($b['website']):?><span class="hdr-link"><?=h(preg_replace('#https?://(www\.)?#','',$b['website']))?></span><?php endif;?>
      </div>
    </div>
  </div>
  <div class="status-bar">
    <?php if($b['phone']):?><span class="status-item"><?=h($b['phone'])?></span><?php endif;?>
    <?php if($b['city']||$b['state']):?><span class="status-item"><?=h(implode(', ',array_filter([$b['city'],$b['state']])))?></span><?php endif;?>
    <?php if(!empty($exp)):?><span class="status-item"><?=count($exp)?> position<?=count($exp)>1?'s':''?></span><?php endif;?>
    <?php if(!empty($sk)):?><span class="status-item"><?=count($sk)?> skills</span><?php endif;?>
  </div>
</div>

<div class="body">
  <div class="col-main">
    <?php if($b['objective']&&$vis['objective']):?>
    <div class="sec-title">about</div>
    <p class="obj-text"><?=nl2($b['objective'])?></p>
    <?php endif;?>
    <?php if(!empty($exp)&&$vis['experience']):?>
    <div class="sec-title">experience</div>
    <?php foreach($exp as $e):if(!$e['job_title'])continue;?>
    <div class="entry">
      <div class="e-top"><span class="e-title"><?=h($e['job_title'])?></span><span class="e-date"><?=h($e['date_from'])?><?=$e['date_from']?' → ':''?><?=!empty($e['is_current'])?'now':h($e['date_to'])?></span></div>
      <div class="e-sub"><?=h($e['company'])?><?=$e['location']?' @ '.h($e['location']):''?></div>
      <?php if($e['description']):?><div class="e-desc"><?=nl2($e['description'])?></div><?php endif;?>
    </div>
    <?php endforeach;?>
    <?php endif;?>
    <?php if(!empty($ext['projects'])&&$vis['projects']):?>
    <div class="sec-title">projects</div>
    <?php foreach($ext['projects'] as $p):if(!$p['name'])continue;?>
    <div class="entry"><div class="e-top"><span class="e-title"><?=h($p['name'])?></span><?php if($p['tech_stack']):?><span class="e-date" style="font-size:.6em;background:#f1f5f9;color:#64748b"><?=h($p['tech_stack'])?></span><?php endif;?></div><?php if($p['description']):?><div class="e-desc"><?=nl2($p['description'])?></div><?php endif;?></div>
    <?php endforeach;?>
    <?php endif;?>
    <?php if(!empty($edu)&&$vis['education']):?>
    <div class="sec-title">education</div>
    <?php foreach($edu as $e):if(!$e['degree'])continue;?>
    <div class="entry"><div class="e-top"><span class="e-title"><?=h($e['degree'])?></span><span class="e-date"><?=h($e['year_to'])?></span></div><div class="e-sub"><?=h($e['institution'])?><?=$e['grade']?' · '.h($e['grade']):''?></div></div>
    <?php endforeach;?>
    <?php endif;?>
  </div>

  <div class="sidebar">
    <?php if(!empty($sk)&&$vis['skills']):?>
    <div class="sb-title">skills</div>
    <?php foreach($sk as $i=>$s):if(!$s['name'])continue;?>
    <div class="skill-bar-row">
      <div class="skill-bar-lbl"><span><?=h($s['name'])?></span></div>
      <div class="skill-bar"><div class="skill-bar-fill" style="width:<?=(int)($s['level']??80)?>%"></div></div>
    </div>
    <?php endforeach;?>
    <?php endif;?>
    <?php if(!empty($ext['certifications'])&&$vis['certifications']):?>
    <div class="sb-title">certifications</div>
    <?php foreach($ext['certifications'] as $c):if(!$c['name'])continue;?><div class="cert-item"><div class="cert-name"><?=h($c['name'])?></div><?php if($c['issuer']||$c['date']):?><div style="color:#64748b"><?=h($c['issuer'])?><?=$c['date']?' · '.h($c['date']):''?></div><?php endif;?></div><?php endforeach;?>
    <?php endif;?>
    <?php if($per['languages']):?>
    <div class="sb-title">languages</div>
    <div class="sb-item"><?=h($per['languages'])?></div>
    <?php endif;?>
    <?php if($per['hobbies']):?>
    <div class="sb-title">interests</div>
    <div class="sb-item"><?=h($per['hobbies'])?></div>
    <?php endif;?>
    <?php if(!empty($ext['achievements'])&&$vis['achievements']):?>
    <div class="sb-title">achievements</div>
    <?php foreach($ext['achievements'] as $a):if(!$a['title'])continue;?><div class="cert-item"><div class="cert-name"><?=h($a['title'])?></div><?php if($a['year']):?><div style="color:#64748b"><?=h($a['year'])?></div><?php endif;?></div><?php endforeach;?>
    <?php endif;?>
  </div>
</div>
</div></body></html>
