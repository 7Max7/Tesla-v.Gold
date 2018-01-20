<?
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}

$cacheStatFile = "cache/block-friends.txt";
$expire = 180*60; // 180 минут
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) {
   $content.=file_get_contents($cacheStatFile);
} else
{

$data="";
$res = sql_query("SELECT * FROM friendsblock WHERE visible='yes' ORDER BY RAND() DESC") or sqlerr(__FILE__, __LINE__);
while ($arr = mysql_fetch_assoc($res)) {
$data.="<a href=\"redir.php?url=$arr[url]\"target=\"_blank\" rel=\"nofollow\"><img alt=\"$arr[url]\" src=\"$arr[image]\" width=88 height=31 border=0 title=\"$arr[descr]\"></a><br><br>"; 
}

$content="<table align=\"center\" border=\"0\" cellpadding=\"3\" cellspacing=\"0\" width=\"100%\"> 
<tr><td align=\"center\">
<iframe align=\"center\" width=\"100%\" scrolling=\"no\" height=\"200\" frameborder=\"0\" onmouseout=\"stp=0\" onmouseover=\"stp=1\" name=\"xex\"></iframe>
<script>xex.document.write('<center>$data</center>');xex.document.close();var stp=0,alt=1,c1=0,c2=-1,dir=1;function sw(){if(!stp) {  alt=alt?0:1; var st=xex.document.body.scrollTop; if(alt)c2=st; else c1=st;if(c1!=c2) xex.scrollBy(0,dir); else { if(dir==1)dir=-1; else dir=1; c1=0; c2=-1}}}setInterval(sw,40);</script>
</td></tr></table>";

  $fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }


if (get_user_class() >= UC_SYSOP)
{
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
}
 
?> 