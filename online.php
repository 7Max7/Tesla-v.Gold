<?
include_once("include/bittorrent.php");
dbconn();
//loggedinorreturn(); 
header("Content-Type: text/html; charset=Windows-1251");


if ($CURUSER["shoutbox"] <> '0000-00-00 00:00:00') {
die("Вам запрещено Использовать Чат."); 
}

if (!$CURUSER) 
{
die("Авторизуйтесь на сайте");
}

if (get_date_time(gmtime() - 20) >= $CURUSER["chat_access"]){
sql_query("UPDATE users SET chat_access=".sqlesc(get_date_time())." WHERE id=".$CURUSER['id']."") or sqlerr(__FILE__, __LINE__);
///echo("$da - $us");
}

print "<div id=\"wol\" align=\"left\">";

//echo "<font size = 2><a href=userdetails.php?id=$CURUSER[id] onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]$CURUSER[username]:[/b] '+parent.document.shoutform.shout.value;return false;\" target=_blank>".get_user_class_color($CURUSER["class"], $CURUSER["username"]) . "</a></font></br>";
    
/*
$id2=$CURUSER["id"];

$dt = sqlesc(time() - 300);
//$res = mysql_query("SELECT id, username, class, donor, warned, parked FROM users WHERE last_access >= $dt AND language='english' ORDER BY username")or sqlerr(__FILE__,__LINE__);
$res = sql_query("SELECT s.uid, s.username, s.class FROM sessions AS s WHERE $or url LIKE '%online.php%' and s.time > DATE_SUB(NOW(), INTERVAL 300 SECOND) ORDER BY s.class DESC") or sqlerr(__FILE__,__LINE__);
*/

//$dt = sqlesc(get_date_time(gmtime() - 40));
//$dt = sqlesc(time() - 180);
$limit_id=$CURUSER["id"];
$res = sql_query("SELECT id, username, class FROM users WHERE chat_access > ".sqlesc(get_date_time(gmtime() - 40))." ORDER BY class DESC LIMIT 100") or sqlerr(__FILE__,__LINE__);
///chat_access<>'0000-00-00 00:00:00' and 

$s4etc=0;
$title_who_c="";
while ($arr = mysql_fetch_assoc($res)) {

$id=$arr['id'];


$class=$arr['class'];
$username = $arr['username'];
    
$title_who_c.= "<font size = 2><a href=userdetails.php?id=$id onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]$username:[/b] '+parent.document.shoutform.shout.value;return false;\" target=_blank>".get_user_class_color($arr["class"], $arr["username"]) . "</a></font>
".($id<>$CURUSER["id"] ? "<span  style=\"cursor: pointer;\" title=\"Приват с ".$username."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='privat(".$arr["username"].") '+parent.document.shoutform.shout.value;return false;\">&#8629;</span>":"")."</br>";
$s4etc++;
}

///<a href=\"http://top4trackers.ru/\"><img src=\"http://top4trackers.ru/button.php?u=Maksim777\" alt=\"Рейтинг :: БитТоррент-трекеров\" border=\"0\" /></a>
///<font size = 2><a href=userdetails.php?id=92 onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]ViKa[/b]: '+parent.document.shoutform.shout.value;return false;\" target=_blank>".get_user_class_color("2", "ViKa") . "</a></font> 
print "<b>В чате</b>: $s4etc<br> ".($s4etc==0 ? "Никого нет за последние 60 секунд":$title_who_c)."<br>";


print "</div>";
?> 