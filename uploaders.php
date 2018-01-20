<?php

require "include/bittorrent.php";
dbconn(true);
/*
(SELECT added FROM torrents WHERE owner=id) AS added,
(SELECT COUNT(*) FROM torrents WHERE owner=id ) AS numnum 

*/

if ($CURUSER['class'] == UC_MODERATOR)
{
stdhead("Аплоадеры");
}
else
stdhead("Заливающие");

loggedinorreturn;

if ($CURUSER['class'] >= UC_MODERATOR)
{
if ($CURUSER['class'] >= UC_ADMINISTRATOR)
{$limit=">= 3";}
else 
$limit="= 3";

$res = sql_query("SELECT COUNT(id) FROM users WHERE status='confirmed' and class $limit ORDER BY username");
$row = mysql_fetch_array($res);
$count = $row[0];

$perpage = 200;

list($pagertop, $pagerbottom, $limit_pe) = pager($perpage, $count, "uploaders.php?");


$query = "SELECT id, username, class, added, uploaded, downloaded, donor, warned FROM users WHERE status='confirmed' and class $limit ORDER BY username $limit_pe";
$result = sql_query($query);
$num = mysql_num_rows($result); // how many uploaders

if ($CURUSER['class'] == UC_MODERATOR)
{
echo "<h3>Информация о аплоадерах</h3><hr>";
echo "<p>У нас <b>" . $count . "</b> аплоадер" . ($num > 1 ? "ов" : "") . "</p>";
}
else 
{
echo "<h3>Информация о всех пользователях</h3><hr>";
echo "<p>У нас <b>" . $count . "</b> пользователей которые могут заливать.</p>";
}
$zerofix = $num - 1; // remove one row because mysql starts at zero

if ($num > 0)
{
echo "<table cellpadding=4 align=center border=1>";
echo "<tr>";print($pagertop);
echo "<td align=center class=colhead>Номер</td>";
echo "<td align=center class=colhead>Пользователь</td>";
echo "<td align=center class=colhead>Раздал&nbsp;/&nbsp;Скачал</td>";
echo "<td align=center class=colhead>Рейтинг</td>";
echo "<td align=center class=colhead>Залил&nbsp;торрентов</td>";
echo "<td align=center class=colhead>Последняя&nbsp;заливка</td>";
echo "<td align=center class=colhead>Личка</td>";
echo "</tr>";

for ($i = 0; $i <= $zerofix; $i++)
{
$id = mysql_result($result, $i, "id");
$username = mysql_result($result, $i, "username");
$class = mysql_result($result, $i, "class");
$added = mysql_result($result, $i, "added");
$uploaded = mksize(mysql_result($result, $i, "uploaded"));
$downloaded = mksize(mysql_result($result, $i, "downloaded"));
$uploadedratio = mysql_result($result, $i, "uploaded");
$downloadedratio = mysql_result($result, $i, "downloaded");
$donor = mysql_result($result, $i, "donor");
$warned = mysql_result($result, $i, "warned");



// get uploader torrents activity
$upperquery = "SELECT added FROM torrents WHERE owner = $id";
$upperresult = mysql_query($upperquery);

$torrentinfo = mysql_fetch_array($upperresult);

$numtorrents = mysql_num_rows($upperresult);


/*
///запрос (випов не трогает)
if ($CURUSER['class'] == UC_SYSOP && $numtorrents=="0")
{
	$maxclass = UC_UPLOADER;
	sql_query("UPDATE users SET class=0 WHERE class <= $maxclass AND id = $id");
}*/


if ($downloaded > 0)
{
$ratio = $uploadedratio / $downloadedratio;
$ratio = number_format($ratio, 3);
$color = get_ratio_color($ratio);
if ($color)
$ratio = "<font color=$color>$ratio</font>";
}
else
if ($uploaded > 0)
$ratio = "Inf.";
else
$ratio = "<b>---</b>";

// get donor
if ($donor == "yes")
$star = "<img src=pic/star.gif>";
else
$star = "";

// get warned
if ($warned == "yes")
$klicaj = "<img src=pic/warned8.gif>";
else
$klicaj = "";

$counter = $i + 1;

echo "<tr>";
echo "<td align=center>$counter</td>";

if ($uploaded=="0.00 КБ"){$uploaded="<font color=green>0.00 КБ</font>";}
if ($downloaded=="0.00 КБ"){$downloaded="<font color=red>0.00 КБ</font>";}

echo "<td align=center><a href=userdetails.php?id=$id>".get_user_class_color($class,$username)."</a> $star $klicaj</td>";
echo "<td align=center>$uploaded / $downloaded</td>";
echo "<td align=center>$ratio</td>";

if ($numtorrents=="0")
{$numtorrents="<font color=red>0</font>";}
echo "<td align=center>$numtorrents торрентов</td>";
if ($numtorrents > 0)
{
$lastadded = mysql_result($upperresult, $numtorrents - 1, "added");
echo "<td align=center>" . get_elapsed_time(sql_timestamp_to_unix_timestamp($lastadded)) . " назад (" . date("d. M Y",strtotime($lastadded)) . ")</td>";
}
else
echo "<td align=center><i>нет</i></td>";
echo "<td align=center><a href=message.php?action=sendmessage&amp;receiver=$id><img border=0 src=pic/button_pm.gif></a></td>";

echo "</tr>";


}
echo "</table>";print($pagerbottom);
}

}

else
stdmsg($tracker_lang['error'],$tracker_lang['access_denied']);

stdfoot();

?>