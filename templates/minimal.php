<?php
// Minimal — Swiss design, thin lines, maximum whitespace, no decoration
$b=$d['basic'];$edu=$d['education'];$exp=$d['experience'];$sk=$d['skills'];$per=$d['personal'];$ext=$d['extras'];$sty=$d['styling'];$vis=$sty['sections_visible'];
$acc=htmlspecialchars($sty['accent_color']??'#e11d48',ENT_QUOTES);
$fs=(int)($sty['font_size']??13);$ff=htmlspecialchars($sty['font_family']??'Inter',ENT_QUOTES);
function h($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function nl2($v){return nl2br(h($v));}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title><?=h($b['full_name'])?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html{font-size:<?=$fs?>px}
body{font-family:'<?=$ff?>',sans-serif;background:#fff;color:#18181b;line-height:1.6}
.page{max-width:210mm;margin:0 auto;padding:40px 48px;min-height:297mm}
/* Left accent bar */
.page::before{content:'';position:fixed;top:0;left:0;width:3px;height:100%;background:<?=$acc?>}
/* Header */
.hdr{padding-bottom:24px;margin-bottom:24px;border-bottom:1px solid #e4e4e7}
.name{font-size:2.2em;font-weight:300;letter-spacing:-.02em;color:#09090b;margin-bottom:8px}
.name strong{font-weight:700}
.contact-row{display:flex;flex-wrap:wrap;gap:4px 0;font-size:.78em;color:#71717a}
.contact-row span{padding-right:14px;border-right:1px solid #e4e4e7;margin-right:14px;line-height:1}
.contact-row span:last-child{border-right:none}
/* Sections */
.sec{display:grid;grid-template-columns:160px 1fr;gap:0 28px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid #f4f4f5}
.sec:last-child{border-bottom:none}
.sec-label{font-size:.68em;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:<?=$acc?>;padding-top:3px;line-height:1.3}
.sec-body{min-width:0}
/* Entries */
.entry{margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #f4f4f5}
.entry:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
.entry-row{display:flex;justify-content:space-between;align-items:baseline;gap:12px;margin-bottom:1px}
.entry-title{font-weight:600;font-size:.88em;color:#09090b}
.entry-date{font-size:.73em;color:#a1a1aa;white-space:nowrap;font-weight:400}
.entry-sub{font-size:.8em;color:#71717a;margin-bottom:3px}
.entry-desc{font-size:.79em;color:#52525b;line-height:1.65}
.obj{font-size:.84em;color:#3f3f46;line-height:1.75}
/* Skills */
.skills-inline{display:flex;flex-wrap:wrap;gap:6px}
.skill-tag{font-size:.76em;padding:3px 10px;border:1px solid #e4e4e7;border-radius:2px;color:#3f3f46}
.skill-tag.top{border-color:<?=$acc?>66;color:<?=$acc?>;background:<?=$acc?>08}
/* Two col for small sections */
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:8px 20px}
@media print{body{print-color-adjust:exact;-webkit-print-color-adjust:exact}.page::before{position:fixed;display:block}}
</style></head><body><div class="page">
<div class="hdr">
  <div class="name"><?php $nameParts=explode(' ',trim($b['full_name']?:'Your Name'),2);echo '<strong>'.h($nameParts[0]).'</strong> '.h($nameParts[1]??'')?></div>
  <div class="contact-row">
    <?php if($b['email']):?><span><?=h($b['email'])?></span><?php endif;?>
    <?php if($b['phone']):?><span><?=h($b['phone'])?></span><?php endif;?>
    <?php if($b['city']||$b['state']):?><span><?=h(implode(', ',array_filter([$b['city'],$b['state'],$b['country']])))?></span><?php endif;?>
    <?php if($b['linkedin']):?><span><?=h(preg_replace('#https?://(www\.)?#','',$b['linkedin']))?></span><?php endif;?>
    <?php if($b['github']):?><span><?=h(preg_replace('#https?://(www\.)?#','',$b['github']))?></span><?php endif;?>
    <?php if($b['website']):?><span><?=h(preg_replace('#https?://(www\.)?#','',$b['website']))?></span><?php endif;?>
  </div>
</div>

<?php if($b['objective']&&$vis['objective']):?>
<div class="sec"><div class="sec-label">About</div><div class="sec-body"><p class="obj"><?=nl2($b['objective'])?></p></div></div>
<?php endif;?>

<?php if(!empty($exp)&&$vis['experience']):?>
<div class="sec"><div class="sec-label">Experience</div><div class="sec-body">
<?php foreach($exp as $e):if(!$e['job_title'])continue;?>
<div class="entry">
  <div class="entry-row"><span class="entry-title"><?=h($e['job_title'])?></span><span class="entry-date"><?=h($e['date_from'])?><?=$e['date_from']?' – ':''?><?=!empty($e['is_current'])?'Present':h($e['date_to'])?></span></div>
  <div class="entry-sub"><?=h($e['company'])?><?=$e['location']?', '.h($e['location']):''?></div>
  <?php if($e['description']):?><div class="entry-desc"><?=nl2($e['description'])?></div><?php endif;?>
</div>
<?php endforeach;?>
</div></div>
<?php endif;?>

<?php if(!empty($edu)&&$vis['education']):?>
<div class="sec"><div class="sec-label">Education</div><div class="sec-body">
<?php foreach($edu as $e):if(!$e['degree'])continue;?>
<div class="entry">
  <div class="entry-row"><span class="entry-title"><?=h($e['degree'])?></span><span class="entry-date"><?=h($e['year_from'])?><?=$e['year_from']?' – ':''?><?=h($e['year_to'])?></span></div>
  <div class="entry-sub"><?=h($e['institution'])?><?=$e['grade']?' · '.h($e['grade']):''?></div>
</div>
<?php endforeach;?>
</div></div>
<?php endif;?>

<?php if(!empty($sk)&&$vis['skills']):?>
<div class="sec"><div class="sec-label">Skills</div><div class="sec-body">
<div class="skills-inline">
<?php foreach($sk as $i=>$s):if(!$s['name'])continue;?>
<span class="skill-tag<?=$i<4?' top':''?>"><?=h($s['name'])?></span>
<?php endforeach;?>
</div>
</div></div>
<?php endif;?>

<?php if(!empty($ext['certifications'])&&$vis['certifications']):?>
<div class="sec"><div class="sec-label">Certifications</div><div class="sec-body">
<?php foreach($ext['certifications'] as $c):if(!$c['name'])continue;?>
<div class="entry"><div class="entry-row"><span class="entry-title"><?=h($c['name'])?></span><?php if($c['date']):?><span class="entry-date"><?=h($c['date'])?></span><?php endif;?></div><?php if($c['issuer']):?><div class="entry-sub"><?=h($c['issuer'])?></div><?php endif;?></div>
<?php endforeach;?>
</div></div>
<?php endif;?>

<?php if(!empty($ext['projects'])&&$vis['projects']):?>
<div class="sec"><div class="sec-label">Projects</div><div class="sec-body">
<?php foreach($ext['projects'] as $p):if(!$p['name'])continue;?>
<div class="entry"><div class="entry-title"><?=h($p['name'])?><?=$p['role']?' — '.h($p['role']):''?></div><?php if($p['tech_stack']):?><div class="entry-sub"><?=h($p['tech_stack'])?></div><?php endif;?><?php if($p['description']):?><div class="entry-desc"><?=nl2($p['description'])?></div><?php endif;?></div>
<?php endforeach;?>
</div></div>
<?php endif;?>

<?php if(!empty($ext['achievements'])&&$vis['achievements']):?>
<div class="sec"><div class="sec-label">Achievements</div><div class="sec-body">
<?php foreach($ext['achievements'] as $a):if(!$a['title'])continue;?><div class="entry"><div class="entry-row"><span class="entry-title"><?=h($a['title'])?></span><?php if($a['year']):?><span class="entry-date"><?=h($a['year'])?></span><?php endif;?></div><?php if($a['description']):?><div class="entry-desc"><?=h($a['description'])?></div><?php endif;?></div><?php endforeach;?>
</div></div>
<?php endif;?>

<?php if($per['languages']||$per['hobbies']):?>
<div class="sec"><div class="sec-label">Personal</div><div class="sec-body two-col">
<?php if($per['languages']):?><div><div class="entry-sub">Languages</div><div style="font-size:.8em"><?=h($per['languages'])?></div></div><?php endif;?>
<?php if($per['hobbies']):?><div><div class="entry-sub">Hobbies</div><div style="font-size:.8em"><?=h($per['hobbies'])?></div></div><?php endif;?>
</div></div>
<?php endif;?>

</div></body></html>
