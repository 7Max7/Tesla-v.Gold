<?
if (!defined('BLOCK_FILE')) { 
 Header("Location: ../index.php"); 
 exit; 
} 

global $CURUSER;


if (!$CURUSER){
$cacheStatFile = "cache/block-techpo_guest.txt"; 
$expire = 30*60; // 30 minutes 
}
if ($CURUSER){
$cacheStatFile = "cache/block-techpo.txt"; 
$expire = 20*60; // 20 minutes 
}


$dti = gmtime() - 1209600; //// 2 недели
$dti = sqlesc(get_date_time($dti));

if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
	
$res = sql_query("SELECT * FROM users WHERE last_access > $dti AND support='yes' AND status='confirmed' ORDER BY username LIMIT 10") or sqlerr(__FILE__, __LINE__);
while ($arr = mysql_fetch_assoc($res))
{
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));
$supporttitle = $arr['supportfor'];
$icq2 = $arr['icq'];

if ($arr['support']) 
($support2++); 

if (strlen($icq2) >= 5)(
$icq = "<img src=\"http://web.icq.com/whitepages/online?icq=$icq2&amp;img=5\" alt=\"$icq2\" border=\"0\" />" );
else
$icq = "";

$support3="<font color=blue>$support2</font>";

if ($CURUSER){
$support .= "<a href=userdetails.php?id=".$arr['id']." >".get_user_class_color($arr["class"], $arr["username"])."</a>

<a $supporttitle href=message.php?action=sendmessage&amp;receiver=".$arr['id'].">"."
<img alt=\"$supporttitle\" title=\"$supporttitle\" src=\"pic/editor/quote.gif\" border=0 >
</a>".("'".$arr['last_access']."'">$dt?"<img src=pic/button_online.gif border=0 alt=\"Пока, что на трекере\">":"<img src=pic/button_offline.gif border=0 alt=\"Не на трекере\">")."
<br>
\n";
}
if (!$CURUSER){
$support .= "
<font color=\"#".get_user_rgbcolor($arr["class"], $arr["username"])."\">".$arr["username"]."</font>
"."
<img alt=\"$supporttitle\" title=\"$supporttitle\" src=\"pic/editor/quote.gif\" border=0 >
".("'".$arr['last_access']."'">$dt?"<img src=pic/button_online.gif border=0 alt=\"Пока, что на трекере\">":"<img src=pic/button_offline.gif border=0 alt=\"Не на трекере\">")."
<br>
\n"; 
}



}

$blocktitle = "Техподдержка";
if (!$support2==0)( 

$content .= "<center> 
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" width=\"100%\"><tr>
<td class=\"embedded\"><center><b>В поддержке $support3:</b></center>" 
."<div align='right'>".$support."</div></td></tr></table></center>"
);
else 
$content .= "
<center><i>нет желающих</i></center>";
 



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