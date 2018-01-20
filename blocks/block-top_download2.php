<?php 

if (!defined('BLOCK_FILE')) { 
header("Location: ../index.php"); 
exit; 
} 

$cacheStatFile = "cache/block-top_download2.txt"; 
$expire = 30*60; // 30 минут
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{ 


$res = mysql_query("SELECT * from (SELECT id,category,seeders, torrents.image1, leechers, name, times_completed from torrents ORDER BY times_completed DESC LIMIT 7) as t ORDER BY rand() LIMIT 1") or sqlerr(__FILE__, __LINE__); 

$num = mysql_num_rows($res); 

if ($num > 0) { 
$content .= "<table border=1 cellspacing=0 cellpadding=5 width=100%><tr>"; 

$content .= "<td class=colhead align=center width=15%><b>Сидеров</b></td>"; 
$content .= "<td class=colhead align=center width=15%><b>Личеров</b></td></tr>"; 

for ($i = 0; $i < $num; ++$i) 
{ 
while ($row = mysql_fetch_assoc($res)) { 
$cros = mysql_query("SELECT name, image FROM categories WHERE id=$row[category]"); 
if (mysql_num_rows($cros) == 1) 
{ 
$corr = mysql_fetch_assoc($cros); 
$cat_img = "<img src=$BASEURL/pic/cats/" . $corr[image] . " border=0 alt='$corr[name]'>"; 
} 
if($row[image1]=="")
         $row[image1]="default_torrent.png";  
$content .= "<center><a href=details.php?id=$row[id]><img title=\"$row[name]\" border=0 width=141 src='thumbnail.php?$row[image1]'/></a></center>"; 
$content .= "<td align=center><font color=red>" .number_format($row['seeders'])."</font></td>"; 
$content .= "<td align=center>" .number_format($row['leechers'])."</td></tr>\n"; 
} 
} 
} 
$content .= "</tr></table>"; 


          $fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
?> 
