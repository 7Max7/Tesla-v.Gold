<?php

if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}


// начинаем кешировать
$cacheStatFile = "cache/block-top_bonus.txt"; 
$expire = 20*60; // 20 минут на кеш, после обновление 
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{ 
// тело - то, что кешируем - начало


$content .= "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" class=\"tablica>\""; 

$bonusss = sql_query("SELECT bonus, id, class, username FROM users WHERE bonus > 1 ORDER BY bonus DESC LIMIT 13") or sqlerr(__FILE__, __LINE__); 
while ($bonuss = mysql_fetch_array($bonusss)) { 
    $id = $bonuss["id"]; 
       $bonus = $bonuss["bonus"]; 
    $content .= "<tr><td align=\"center\" class=\"row3\"><a href=\"userdetails.php?id=$id\">".get_user_class_color($bonuss["class"], $bonuss["username"])."</a></td><td align=\"center\" class=\"row3\">$bonus</td></tr>"; 
} 

$content .= "</table>"; 
$blocktitle = "Топ 13 бонусов"; 








// тело - то, что кешируем - конец

$fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }

// заканчиваем кешировать

if (get_user_class() >= UC_SYSOP)
{
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
} 

?>