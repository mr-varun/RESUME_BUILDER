<?php
// Executive — Full-width dark header, two-column body with ruled sections
$b=$d['basic'];$edu=$d['education'];$exp=$d['experience'];$sk=$d['skills'];$per=$d['personal'];$ext=$d['extras'];$sty=$d['styling'];$vis=$sty['sections_visible'];
$acc=htmlspecialchars($sty['accent_color']??'#c9a84c',ENT_QUOTES);
$fs=(int)($sty['font_size']??13);$ff=htmlspecialchars($sty['font_family']??'Inter',ENT_QUOTES);
function h($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function nl2($v){return nl2br(h($v));}
$photoSrc=!empty($b['photo'])?(defined('UPLOAD_URL')?UPLOAD_URL.h($b['photo']):'../uploads/photos/'.h($b['photo'])):'';
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title><?=h($b['full_name'])?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html{font-size:<?=$fs?>px}
body{font-family:'<?=$ff?>',sans-serif;background:#fff;color:#1e293b;line-height:1.55}
.page{max-width:210mm;margin:0 auto;background:#fff;min-height:297mm}
/* Header */
.hdr{background:#1a2332;color:#fff;padding:28px 36px 24px;position:relative;overflow:hidden}
.hdr::before{content:'';position:absolute;right:-40px;top:-40px;width:200px;height:200px;background:<?=$acc?>22;border-radius:50%}
.hdr::after{content:'';position:absolute;right:60px;bottom:-60px;width:140px;height:140px;background:<?=$acc?>15;border-radius:50%}
.hdr-inner{display:flex;align-items:flex-start;gap:20px;position:relative;z-index:1}
.hdr-photo{width:90px;height:110px;object-fit:cover;border-radius:4px;border:2px solid <?=$acc?>;flex-shrink:0}
.hdr-photo-placeholder{width:90px;height:110px;background:rgba(255,255,255,.08);border:2px solid <?=$acc?>;border-radius:4px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:2rem;color:<?=$acc?>}
.hdr-text{flex:1}
.hdr-name{font-family:'Merriweather',serif;font-size:2em;font-weight:700;color:#fff;line-height:1.1;margin-bottom:4px}
.hdr-role{font-size:.9em;color:<?=$acc?>;font-weight:500;margin-bottom:12px;letter-spacing:.03em}
.hdr-contacts{display:flex;flex-wrap:wrap;gap:6px 20px}
.hdr-contact-item{display:flex;align-items:center;gap:5px;font-size:.75em;color:rgba(255,255,255,.75)}
.hdr-contact-item svg{width:12px;height:12px;fill:<?=$acc?>;flex-shrink:0}
.accent-bar{height:3px;background:linear-gradient(to right,<?=$acc?>,transparent)}
/* Body */
.body{display:grid;grid-template-columns:2fr 1fr;gap:0}
.col-main{padding:24px 24px 24px 36px;border-right:1px solid #e2e8f0}
.col-side{padding:24px 24px 24px 20px;background:#f8fafc}
.sec-title{font-size:.7em;font-weight:800;text-transform:uppercase;letter-spacing:.14em;color:<?=$acc?>;margin-bottom:10px;display:flex;align-items:center;gap:8px}
.sec-title::after{content:'';flex:1;height:1px;background:#e2e8f0}
.entry{margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid #f1f5f9}
.entry:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
.e-top{display:flex;justify-content:space-between;align-items:flex-start;gap:8px}
.e-title{font-weight:700;font-size:.88em;color:#0f172a}
.e-date{font-size:.74em;background:<?=$acc?>20;color:<?=$acc?>;padding:2px 8px;border-radius:12px;white-space:nowrap;font-weight:600;flex-shrink:0}
.e-sub{font-size:.8em;color:#64748b;margin-top:2px}
.e-desc{font-size:.79em;color:#475569;margin-top:5px;line-height:1.65}
.obj-text{font-size:.84em;color:#334155;line-height:1.75}
/* Sidebar sections */
.side-sec{margin-bottom:18px}
.skill-bar-row{margin-bottom:7px}
.skill-bar-label{display:flex;justify-content:space-between;font-size:.76em;margin-bottom:3px;color:#334155}
.skill-bar{height:5px;background:#e2e8f0;border-radius:3px;overflow:hidden}
.skill-bar-fill{height:100%;background:<?=$acc?>;border-radius:3px}
.side-item{display:flex;align-items:flex-start;gap:7px;font-size:.78em;color:#475569;margin-bottom:5px}
.side-item svg{width:11px;height:11px;fill:#94a3b8;flex-shrink:0;margin-top:2px}
.side-tag{display:inline-block;background:<?=$acc?>18;color:<?=$acc?>;border:1px solid <?=$acc?>44;padding:2px 8px;border-radius:3px;font-size:.72em;font-weight:500;margin:2px 2px 2px 0}
.cert-item{font-size:.78em;color:#334155;margin-bottom:6px;padding-left:10px;border-left:2px solid <?=$acc?>}
@media print{body{print-color-adjust:exact;-webkit-print-color-adjust:exact}}
</style></head><body><div class="page">
<div class="hdr">
  <div class="hdr-inner">
    <?php if($photoSrc):?><img class="hdr-photo" src="<?=$photoSrc?>" alt=""><?php else:?><div class="hdr-photo-placeholder">&#9786;</div><?php endif;?>
    <div class="hdr-text">
      <div class="hdr-name"><?=h($b['full_name']?:'Your Name')?></div>
      <?php if(!empty($exp)):?><div class="hdr-role"><?=h($exp[0]['job_title']??'')?></div><?php endif;?>
      <div class="hdr-contacts">
        <?php if($b['phone']):?><span class="hdr-contact-item"><svg viewBox="0 0 16 16"><path d="M13.5 10.5l-2-2a1 1 0 00-1.4 0l-.8.8a8.5 8.5 0 01-3-3l.8-.8a1 1 0 000-1.4l-2-2A1 1 0 003.7 2L2.5 3.2C1.8 3.9 1.7 5 2.3 5.9a15 15 0 008 8c.9.5 2 .4 2.7-.3L14.2 12.5a1 1 0 00-0.7-2z"/></svg><?=h($b['phone'])?></span><?php endif;?>
        <?php if($b['email']):?><span class="hdr-contact-item"><svg viewBox="0 0 16 16"><path d="M14 3H2a1 1 0 00-1 1v8a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1zm-1.2 2L8 8.5 3.2 5h9.6zM3 11V6.6l4.6 2.9a1 1 0 001 0L13 6.6V11H3z"/></svg><?=h($b['email'])?></span><?php endif;?>
        <?php if($b['city']||$b['state']):?><span class="hdr-contact-item"><svg viewBox="0 0 16 16"><path d="M8 1a5 5 0 00-5 5c0 4 5 9 5 9s5-5 5-9a5 5 0 00-5-5zm0 7a2 2 0 110-4 2 2 0 010 4z"/></svg><?=h(implode(', ',array_filter([$b['city'],$b['state']])))?></span><?php endif;?>
        <?php if($b['linkedin']):?><span class="hdr-contact-item"><svg viewBox="0 0 16 16"><path d="M0 1.1A1.1 1.1 0 011.1 0h13.8A1.1 1.1 0 0116 1.1v13.8A1.1 1.1 0 0114.9 16H1.1A1.1 1.1 0 010 14.9V1.1zm4.8 12.5V6H2.4v7.6h2.4zM3.6 5a1.4 1.4 0 110-2.8A1.4 1.4 0 013.6 5zm9.9 8.6V9.4c0-2-.4-3.6-2.8-3.6-1.1 0-1.9.6-2.2 1.2h-.1V6H6.1v7.6h2.4V9.8c0-1 .2-2 1.5-2s1.4 1.1 1.4 2v3.8h2.1z"/></svg><?=h(preg_replace('#https?://(www\.)?#','',$b['linkedin']))?></span><?php endif;?>
        <?php if($b['github']):?><span class="hdr-contact-item"><svg viewBox="0 0 16 16"><path d="M8 0C3.6 0 0 3.6 0 8c0 3.5 2.3 6.5 5.5 7.6.4.1.5-.2.5-.4v-1.4c-2.2.5-2.7-1-2.7-1-.4-.9-.9-1.2-.9-1.2-.7-.5.1-.5.1-.5.8.1 1.2.8 1.2.8.7 1.2 1.9.9 2.3.7.1-.5.3-.9.5-1.1-1.8-.2-3.6-.9-3.6-4 0-.9.3-1.6.8-2.1-.1-.2-.4-1 .1-2.1 0 0 .7-.2 2.2.8a7.5 7.5 0 014 0c1.5-1 2.2-.8 2.2-.8.5 1.1.2 1.9.1 2.1.5.5.8 1.2.8 2.1 0 3.1-1.9 3.8-3.7 4 .3.3.6.8.6 1.6v2.4c0 .2.1.5.5.4C13.7 14.5 16 11.5 16 8c0-4.4-3.6-8-8-8z"/></svg><?=h(preg_replace('#https?://(www\.)?#','',$b['github']))?></span><?php endif;?>
      </div>
    </div>
  </div>
</div>
<div class="accent-bar"></div>
<div class="body">
  <div class="col-main">
    <?php if($b['objective']&&$vis['objective']):?>
    <div class="side-sec">
      <div class="sec-title">Professional Summary</div>
      <p class="obj-text"><?=nl2($b['objective'])?></p>
    </div>
    <?php endif;?>
    <?php if(!empty($exp)&&$vis['experience']):?>
    <div class="side-sec">
      <div class="sec-title">Experience</div>
      <?php foreach($exp as $e):if(!$e['job_title'])continue;?>
      <div class="entry">
        <div class="e-top"><span class="e-title"><?=h($e['job_title'])?></span><span class="e-date"><?=h($e['date_from'])?><?=$e['date_from']?' – ':''?><?=!empty($e['is_current'])?'Present':h($e['date_to'])?></span></div>
        <div class="e-sub"><?=h($e['company'])?><?=$e['location']?', '.h($e['location']):''?><?=$e['employment_type']?' · '.h($e['employment_type']):''?></div>
        <?php if($e['description']):?><div class="e-desc"><?=nl2($e['description'])?></div><?php endif;?>
      </div>
      <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if(!empty($edu)&&$vis['education']):?>
    <div class="side-sec">
      <div class="sec-title">Education</div>
      <?php foreach($edu as $e):if(!$e['degree'])continue;?>
      <div class="entry">
        <div class="e-top"><span class="e-title"><?=h($e['degree'])?></span><span class="e-date"><?=h($e['year_from'])?><?=$e['year_from']?' – ':''?><?=h($e['year_to'])?></span></div>
        <div class="e-sub"><?=h($e['institution'])?><?=$e['university']?' · '.h($e['university']):''?><?=$e['grade']?' · '.h($e['grade']):''?></div>
      </div>
      <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if(!empty($ext['projects'])&&$vis['projects']):?>
    <div class="side-sec">
      <div class="sec-title">Projects</div>
      <?php foreach($ext['projects'] as $p):if(!$p['name'])continue;?>
      <div class="entry">
        <div class="e-title"><?=h($p['name'])?><?=$p['role']?' — '.h($p['role']):''?></div>
        <?php if($p['tech_stack']):?><div class="e-sub"><?=h($p['tech_stack'])?></div><?php endif;?>
        <?php if($p['description']):?><div class="e-desc"><?=nl2($p['description'])?></div><?php endif;?>
      </div>
      <?php endforeach;?>
    </div>
    <?php endif;?>
  </div>

  <div class="col-side">
    <?php if(!empty($sk)&&$vis['skills']):?>
    <div class="side-sec">
      <div class="sec-title">Skills</div>
      <?php foreach($sk as $s):if(!$s['name'])continue;?>
      <div class="skill-bar-row">
        <div class="skill-bar-label"><span><?=h($s['name'])?></span><span><?=(int)($s['level']??80)?>%</span></div>
        <div class="skill-bar"><div class="skill-bar-fill" style="width:<?=(int)($s['level']??80)?>%"></div></div>
      </div>
      <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if(!empty($ext['certifications'])&&$vis['certifications']):?>
    <div class="side-sec">
      <div class="sec-title">Certifications</div>
      <?php foreach($ext['certifications'] as $c):if(!$c['name'])continue;?>
      <div class="cert-item"><strong><?=h($c['name'])?></strong><?=$c['issuer']?'<br>'.h($c['issuer']):''?><?=$c['date']?' · '.h($c['date']):''?></div>
      <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if(!empty($ext['achievements'])&&$vis['achievements']):?>
    <div class="side-sec">
      <div class="sec-title">Achievements</div>
      <?php foreach($ext['achievements'] as $a):if(!$a['title'])continue;?>
      <div class="cert-item"><?=h($a['title'])?><?=$a['year']?' ('.$a['year'].')':''?></div>
      <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if($per['languages']):?><div class="side-sec"><div class="sec-title">Languages</div><div class="side-item" style="flex-wrap:wrap"><?php foreach(explode(',',$per['languages']) as $lang):if(trim($lang)):?><span class="side-tag"><?=h(trim($lang))?></span><?php endif;endforeach;?></div></div><?php endif;?>
    <?php if($per['hobbies']):?><div class="side-sec"><div class="sec-title">Interests</div><p style="font-size:.78em;color:#475569"><?=h($per['hobbies'])?></p></div><?php endif;?>
    <?php if($b['website']):?><div class="side-sec"><div class="sec-title">Links</div><div class="side-item"><?=h(preg_replace('#https?://(www\.)?#','',$b['website']))?></div></div><?php endif;?>
  </div>
</div>
</div></body></html>
