<?php
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

$cacheStatFile = "cache/block-topsid.txt";
$expire = 3*60; // 3 минутs
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) {
   $content.=file_get_contents($cacheStatFile);
} else
{
	
$blocktitle = "Десятка лучших";


$seeds_sql = sql_query("SELECT *, COUNT(*) AS tnum FROM peers WHERE seeder = 'yes' GROUP BY userid ORDER BY `tnum` DESC LIMIT 0 , 10");

$content .= "<table width=\"100%\">
<td align=\"center\" class=\"b\" width=\"80%\">Логин</td>
<td align=\"center\" class=\"b\" width=\"20%\">Раздает</td>\n";
$sf=0;
 while($seeds_array = mysql_fetch_array($seeds_sql)) {
  $sa = mysql_fetch_array(sql_query("SELECT username,class FROM users WHERE id = ".$seeds_array['userid']));
  $name = "<a href=\"userdetails.php?id=".$seeds_array['userid']."\">".get_user_class_color($sa["class"], $sa["username"])." ";
  $number = round($seeds_array['tnum']);
$content .= "<tr>
<td align=\"center\" width=\"70%\">$name</td>
<td align=\"center\" width=\"30%\">$number</td></tr>\n";
$sf++;
  }
  if ($sf==0)
{
$content .= "<tr>
<td align=\"center\" width=\"70%\">нет</td>
<td align=\"center\" width=\"30%\">данных</td></tr>\n";
}
$content .= "</table>\n";

}

         $fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 


if (get_user_class() >= UC_SYSOP)
{
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
}
?> 