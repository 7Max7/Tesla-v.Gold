<?php
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

$cacheStatFile = "cache/block-counter.txt"; 
$expire = 180*60; // 180 minutes 
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{

$dt24 = sqlesc(get_date_time(gmtime() - 86400));
$dt7 = sqlesc(get_date_time(gmtime() - 604800));
$dt30 = sqlesc(get_date_time(gmtime() - 2678400));
$dt356 = sqlesc(get_date_time(gmtime() - 31536000));
$dtall = sqlesc(get_date_time(gmtime() - 315360000));
$dt150 = sqlesc(get_date_time((gmtime() - (31536000/2))));

$result = sql_query("SELECT SUM(last_access >= $dt24) AS totalol24, SUM(last_access >= $dt7) AS totalol7, SUM(last_access >= $dt30) AS totalol30, SUM(last_access >= $dt356) AS totalol356, SUM(last_access >= $dtall) AS totalolall  FROM users") or sqlerr(__FILE__, __LINE__);

while ($row = mysql_fetch_array ($result)) {
$totalonline24 = number_format($row["totalol24"],0);
$totalonline7 = number_format($row["totalol7"],0);
$totalonline30 = number_format($row["totalol30"],0);
$totalonline356 = number_format($row["totalol356"],0);
$totalonlineall = number_format($row["totalolall"],0);
}

$result2 = sql_query("SELECT SUM(date >= $dt24) AS totall24, SUM(date >= $dt7) AS totall7, SUM(date >= $dt30) AS totall30, SUM(date >= $dt150) AS totall356 FROM referrers") or sqlerr(__FILE__, __LINE__);

while ($row2 = mysql_fetch_array($result2)) {
$totalnline24 = number_format($row2["totall24"],0);
$totalnline7 = number_format($row2["totall7"],0);
$totalnline30 = number_format($row2["totall30"],0);
$totalnline356 = number_format($row2["totall356"],0);
}

$result3 = sql_query("SELECT SUM(date >= $dt24) AS totall24, SUM(date >= $dt7) AS totall7, SUM(date >= $dt30) AS totall30, SUM(date >= $dt150) AS totall356 FROM reaway") or sqlerr(__FILE__, __LINE__);

while ($row3 = mysql_fetch_array($result3)) {

$ttalnline24 = number_format($row3["totall24"],0);
$ttalnline7 = number_format($row3["totall7"],0);
$ttalnline30 = number_format($row3["totall30"],0);
$ttalnline356 = number_format($row3["totall356"],0);
}


$content .= "<table border=\"0\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\" width=\"100%\">";

$content .= "<tr><td class=\"colhead\" colspan=\"2\" align=\"center\">Пользователей:</td>
<tr><td class=\"a\" align=\"left\">За день</td><td class=\"a\" align=\"right\">$totalonline24</td></tr>
<tr><td class=\"b\" align=\"left\">За неделю</td><td class=\"b\" align=\"right\">$totalonline7</td></tr>
<tr><td class=\"a\" align=\"left\">За месяц</td><td class=\"a\" align=\"right\">$totalonline30</td></tr>
<tr><td class=\"b\" align=\"left\">За год</td><td class=\"b\" align=\"right\">$totalonline356</td></tr>
<tr><td class=\"a\" align=\"left\">За всё время</td><td class=\"a\" align=\"right\">$totalonlineall</td>
<tr><td colspan=\"2\" align=\"center\"></td></tr>
</tr>";

$content .= "<tr><td class=\"colhead\" colspan=\"2\" align=\"center\">Переходов:</td>
<tr><td class=\"a\" align=\"left\">За день</td><td class=\"a\" align=\"right\">$totalnline24</td></tr>
<tr><td class=\"b\" align=\"left\">За неделю</td><td class=\"b\" align=\"right\">$totalnline7</td></tr>
<tr><td class=\"a\" align=\"left\">За месяц</td><td class=\"a\" align=\"right\">$totalnline30</td></tr>
<tr><td class=\"b\" align=\"left\">За полгода</td><td class=\"b\" align=\"right\">$totalnline356</td></tr>
<tr><td colspan=\"2\" align=\"center\"></td></tr>
</tr>";

$content .= "<tr><td class=\"colhead\" colspan=\"2\" align=\"center\">Переадресаций:</td>
<tr><td class=\"a\" align=\"left\">За день</td><td class=\"a\" align=\"right\">$ttalnline24</td></tr>
<tr><td class=\"b\" align=\"left\">За неделю</td><td class=\"b\" align=\"right\">$ttalnline7</td></tr>
<tr><td class=\"a\" align=\"left\">За месяц</td><td class=\"a\" align=\"right\">$ttalnline30</td></tr>
<tr><td class=\"b\" align=\"left\">За полгода</td><td class=\"b\" align=\"right\">$ttalnline356</td></tr>
<tr><td colspan=\"2\" align=\"center\"></td></tr>
</tr>";

$content .= "</table>";

$fp = fopen($cacheStatFile,"w");
if($fp) {
fputs($fp, $content); 
fclose($fp); 
}
}


if (get_user_class() >= UC_SYSOP) {
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
}
?>