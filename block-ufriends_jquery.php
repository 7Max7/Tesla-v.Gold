<?
require_once 'include/bittorrent.php';

header("Content-Type: text/html; charset=windows-1251");
dbconn(false,true);
  
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

global $CURUSER;

if (empty($CURUSER))
die;

$title_who = array();

//$dt = sqlesc(time() - 180);
$dt = sqlesc(get_date_time(gmtime() - 180));
$result = sql_query("SELECT DISTINCT s.uid, s.username, s.class, s.url, f.blocks
FROM sessions AS s
LEFT JOIN friends AS f ON f.friendid = s.uid 
WHERE f.friendid = s.uid  and f.userid='$CURUSER[id]' and friendid=s.uid and s.time > $dt and f.blocks='0' GROUP BY s.uid ORDER BY s.class DESC") or sqlerr(__FILE__, __LINE__);
$total=0;

while ($row=mysql_fetch_assoc($result))
{
	
$uname=$row["username"];
$class=$row["class"];
$url=$row["url"];
$uid=$row["uid"];


   if (stristr($url,"receiver=".$CURUSER["id"])) {
   	$title_mes[$uname] = "<img border=\"0\" src=\"./pic/balloon--arrow.png\" title=\"Вам пишет сообщение\"/>";
    }


    if (!empty($uname)) {
    	if (stristr($url,'shoutbox.php')) {
   	$title_who[] = "<a href=\"userdetails.php?id=".$uid."\">".get_user_class_color($class, $uname,1)."$title_mes[$uname]";
    }
	else
	 	$title_who[] = "<a href=\"userdetails.php?id=".$uid."\">".get_user_class_color($class, $uname)."$title_mes[$uname]";
	}


      $total++;

}


if (count($title_who)) {

echo "<table border=\"0\" width=\"100%\"><tr valign=\"middle\"><td align=\"left\" class=\"embedded\">Друзья в онлайн [$total]: <hr></td></tr><tr><td class=\"embedded\">".@implode(", ", $title_who)."";

} else {

echo "<table border=\"0\" width=\"100%\"><tr valign=\"middle\"><td align=\"center\" class=\"embedded\"><center>Нет <b><a title=\"К списку друзей\" href=\"friends.php\">друзей</a></b> в сети за последние 3 минуты.</center>\n";

}

echo "</tr></table>\n"; 


} else @header("Location: ../index.php");
 


?>

