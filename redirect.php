<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

if (get_user_class() <= UC_MODERATOR)
 stderr("Извините", "Доступа нет!");


$h = date('H'); // проверяем час
if (($h >= 19)&&($h <= 21) || ($h >= 08)&&($h <= 10)){

global $redir_parse, $redir_day;
if ($redir_parse=="1") {

if (empty($redir_day))
$redir_day = 2;

$secs = $redir_day * (86400 * 31); // удаляем старые данные старше N месяцев

$dt = get_date_time(gmtime() - $secs);
sql_query("DELETE FROM reaway WHERE date < ".sqlesc($dt)) or sqlerr(__FILE__,__LINE__);
}

}

$d = (empty($_GET["d"]) ? "":$_GET["d"]);
$id = (empty($_GET["ass"]) ? "":$_GET["ass"]);
$r = (!is_valid_id($_GET["r"]) ? "":$_GET["r"]);

if (!empty($d) && !empty($id) && is_valid_id($id)) {


if ($d=="one"){
stdheadchat("Удаление переходов с базы id: $id");

$a = sql_query("SELECT parse_url FROM reaway WHERE id=".sqlesc($id)." LIMIT 1") or sqlerr(__FILE__, __LINE__);
$a2 = mysql_fetch_assoc($a);
$count = $a2["parse_url"];

$ass = sql_query("SELECT min(id) AS mini FROM reaway AS ref WHERE ref.parse_url=(SELECT parse_url FROM reaway WHERE id=".sqlesc($id)." LIMIT 1) AND ref.id<>".sqlesc($id)." ORDER BY ref.id DESC") or sqlerr(__FILE__, __LINE__);
$sqass = mysql_fetch_assoc($ass);
if (!empty($sqass["mini"]))
$r=$sqass["mini"];


if (!$count) {
stdmsg("Извините", "Нет в базе $id");
echo "<script>setTimeout('document.location.href=\"".$_SERVER['PHP_SELF']."".($r<>$id ? "?id=".$r:"")."\"', 5000);</script>";
stdfootchat();
die;
}
else
{
sql_query("DELETE FROM reaway WHERE id=".sqlesc($id));
stdmsg("Готово", "Удален <b>$count</b>");
echo "<script>setTimeout('document.location.href=\"".$_SERVER['PHP_SELF']."".($r<>$id ? "?id=".$r:"")."\"', 5000);</script>";
stdfootchat();
die;
}
}
elseif ($d=="all"){

stdheadchat("Удаление переходов с базы id: $id");

$a = sql_query("SELECT parse_url FROM reaway WHERE id=".sqlesc($id)." LIMIT 1") or sqlerr(__FILE__, __LINE__);
$a2 = mysql_fetch_assoc($a);
$count = $a2["parse_url"];

if (!$count) {
stdmsg("Извините", "Нет в базе переходов для удаления по id $id");
echo "<script>setTimeout('document.location.href=\"".$_SERVER['PHP_SELF']."\"', 5000);</script>";
stdfootchat();
die;
}
else 
{
sql_query("DELETE FROM reaway WHERE parse_url=".sqlesc($count))or sqlerr(__FILE__, __LINE__);
stdmsg("Готово", "Удалены все переходы по сайту <b>$count</b>");
echo "<script>setTimeout('document.location.href=\"".$_SERVER['PHP_SELF']."\"', 5000);</script>";
stdfootchat();
die;
}


}
else
echo "<script>setTimeout('document.location.href=\"".$_SERVER['PHP_SELF']."\"', 1000);</script>";



}


$id = (empty($_GET["id"]) ? "":$_GET["id"]);

if (!empty($id) && is_valid_id($id)) {
$count = get_row_count("reaway","WHERE parse_url=(SELECT parse_url FROM reaway WHERE id=".sqlesc($id)." LIMIT 1)");

if (!empty($count)){

$ass = sql_query("SELECT min(id) AS mini,(SELECT parse_url FROM reaway WHERE id=".sqlesc($id)." LIMIT 1) AS parse_url FROM reaway AS ref WHERE ref.parse_url=(SELECT parse_url FROM reaway WHERE id=".sqlesc($id)." LIMIT 1) ORDER BY ref.id DESC") or sqlerr(__FILE__, __LINE__);
$sqass = mysql_fetch_assoc($ass);
$mini=$sqass["mini"];


stdheadchat("Просмотр перехода с ".$sqass["parse_url"]);
}
else
{
stdheadchat("Нет значения в базе для ".$id);
stdmsg("Извините", "В базе нет значений для ".$id);
echo "<script>setTimeout('document.location.href=\"".$_SERVER['PHP_SELF']."\"', 3000);</script>";
stdfootchat();
exit;
}

echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" class=\"embedded\">";

echo "<tr>
<td class=colhead colspan=\"3\" width=\"100%\">Главная <a href=\"redirect.php\" class=\"altlink_white\">Переходов</a><br>Просмотр переходных ссылок по ".$sqass["parse_url"]." (".$count.")</td></tr>";
echo "</table>";

$limited = 500;

list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "redirect.php?id=".$id."&"); 


$a = sql_query("SELECT * FROM reaway AS ref WHERE ref.parse_url=(SELECT parse_url FROM reaway WHERE id=".sqlesc($id)." LIMIT 1) ORDER BY ref.id DESC $limit") or sqlerr(__FILE__, __LINE__);
$c=1;

echo $pagertop;

echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" class=\"embedded\">";

echo "<tr>
<td class=colhead width=\"5%\">#</td>
<td class=colhead align=\"left\">Основная ссылка (отсюда произошел переход)</td>
<td class=colhead width=\"20%\" align=\"left\">ip адрес / количество</td>
<td class=colhead width=\"20%\">Добавление / Обновление</td>
</tr>\n";


while($sqlrow = mysql_fetch_assoc($a)){

if (!empty($sqlrow["uid"])){
$ara = sql_query("SELECT username,class FROM users WHERE id=".sqlesc($sqlrow["uid"])." ORDER BY last_access DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($ara);
}

if ($c%2==0) {
$class="a"; $class2="b"; 
} else {
$class="b"; 	
$class2="a";
}

$parse_url=htmlspecialchars_decode($sqlrow['parse_url']);

$parse_ref=htmlspecialchars_decode($sqlrow['parse_ref']);

///<a href='http://".$S['refsite']."/' target='_blank'>".$S['refsite']."</a>

$parse_ref = str_replace($parse_url,"<b><a href='".$parse_ref."' target='_blank'>".$parse_url."</a></b>",$parse_ref);


echo "
<tr align=center>
<td class=".$class." width=\"5%\">".$sqlrow['id']."</td>
<td class=".$class2." align=\"left\">".$parse_ref."
<div align=\"right\">".(!empty($sqlrow["uid"]) ? "Вход от <a href=\"userdetails.php?id=".$sqlrow['uid']."\">
".get_user_class_color($row['class'],$row['username'])."</a> ":"")."
<a href=\"redirect.php?d=one&ass=".$sqlrow['id']."&r=".$mini."\"><font color=red alt=\"Удалить этот переход\">X</font></a>
</div>
</td>
<td class=".$class." width=\"20%\" align=\"left\"><a href=\"usersearch.php?ip=".$sqlrow['ip']."\">".$sqlrow['ip']."</a> <b>(".$sqlrow['numb'].")</b></td>
<td class=".$class2." width=\"20%\">".normaltime($sqlrow["date"],true)." <br>
".($sqlrow["lastdate"]=="0000-00-00 00:00:00" ? "обновления нет":normaltime($sqlrow["lastdate"],true))."</td>
</tr>\n";
++$c;

unset($row['class'],$row['username']);
}

echo "</table>";
echo $pagerbottom;
stdfootchat();
	
die;
}


///ALTER TABLE `reaway` ADD INDEX `count` ( `parse_url` ) 


stdheadchat("ТОП сайтов, по которым прошла переадресация");

$count = get_row_count("reaway");

//$a = sql_query("SHOW INDEXES FROM reaway WHERE Key_name = 'count'") or sqlerr(__FILE__, __LINE__);

//$sq= mysql_fetch_assoc($a);

//print_r($sq);
//$count=$sq["Cardinality"];

if (!$count) {
stdmsg("Извините", "В базе нет значений");
stdfootchat();
exit;
}


echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" class=\"embedded\">";

echo "<tr>
<td class=colhead colspan=\"3\" width=\"100%\">Главная Переходов <br>Все переходы по ссылкам, включая первое / последнее вхождение, ip адреса пользователей и количество уходов.</td></tr>";

if (empty($redir_parse))
echo "<tr><td align=\"center\" class=\"a\" colspan=\"3\" width=\"100%\">Функция записи переходов отключена.</td></tr>";


echo "</table>";


$limited = 300;

list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "redirect.php?"); 

echo $pagerbottom;

echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" class=\"embedded\">";
echo "<tr>
<td class=colhead width=\"10%\"># в базе</td>
<td class=colhead>Переходной сайт (прошли по ссылке)</td>
<td class=colhead width=\"20%\">Дата добавления</td>
</tr>\n";

$a = sql_query("SELECT *,(SELECT COUNT(*) FROM reaway WHERE reaway.parse_url=ref.parse_url) AS vhozh FROM reaway AS ref GROUP BY ref.parse_url ORDER BY ref.id DESC $limit") or sqlerr(__FILE__, __LINE__);
$c=1;

while($sqlrow = mysql_fetch_assoc($a)){

if ($c%2==0) {
$class="a"; $class2="b"; 
} else {
$class="b"; 	
$class2="a";
}

$parse_url=htmlspecialchars_decode($sqlrow['parse_url']);

echo "
<tr align=center>
<td class=".$class." width=\"10%\">".$sqlrow['id']."</td>
<td class=".$class2.">".$parse_url." <a href=\"redirect.php?id=".$sqlrow['id']."\" class=\"altlink_white\">(".$sqlrow['vhozh'].")</a> 
<div align=\"right\">
<a href=\"redirect.php?d=all&ass=".$sqlrow['id']."\"><font color=red alt=\"Удалить с базы все переходы по этому сайту\">X</font></a>
</div>

</td>
<td class=".$class." width=\"20%\">".normaltime($sqlrow["date"],true)."</td>
</tr>\n";
++$c;
}

echo "</table>";

stdfootchat();

?> 