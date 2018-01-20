<?
require "include/bittorrent.php";
dbconn(false);

loggedinorreturn();

if (get_user_class() < UC_SYSOP) 
 {
attacks_log('hacklog'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

accessadministration();
 
if ($_POST["prune"]){
@unlink(ROOT_PATH."cache/hacklog.txt");
@header("Location: $DEFAULTBASEURL/hacklog.php") or die("Перенаправление на эту же страницу.<script>setTimeout('document.location.href=\"$DEFAULTBASEURL/hacklog.php\"', 10);</script>");
}

stdheadchat("XSS Попытки взлома (ctracker)");


print "<a href=\"info_cache.php\">Функция посещения (info)</a> : <a href=\"sqlerror.php\">SQL ошибки</a> : <a href=\"error_torrent.php\">Ошибки бит клиента</a><br><br>";

if(!file_exists(ROOT_PATH."cache/hacklog.txt"))
{die("Файла XSS атак нет в памяти, видимо удален.");
	}


?>
<tr>
<table cellpadding="4" cellspacing="1" border="0" style="width:100%" class="tableinborder">
<td class="a" valign="top">Время</td>
<td class="a" valign="top">IP</td>
<td class="a" valign="top">Браузер</td>
<td class="a" valign="top">Откуда</td>
<td class="a" valign="top">Страница</td>
</tr>


<?
$fop = fopen (ROOT_PATH."cache/hacklog.txt", "r+");

while (!feof($fop))
{
$read = fgets($fop, 1000);
list($date,$time,$ip,$hua,$from,$host,$evant) = explode('#',$read);


	$events = explode("||",$evant);
	$get = nl2br(htmlspecialchars(print_r(unserialize($events[0]),true)));
	$post = nl2br(htmlspecialchars(print_r(unserialize($events[1]),true)));
////	$event = nl2br(htmlspecialchars(print_r($events[2],true)));
//	$event .= "<hr>".$events[3];
//	$ref = $events[3];



$ip = str_replace(" ","",$ip);
if ($ip && $date){
$r = mysql_query("SELECT id,username,class FROM users WHERE ip=".sqlesc($ip)."") or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_assoc($r);
if (!$user["username"])
$use="<b>не найден</b>";
else
$use="<a href=userdetails.php?id=$user[id]>".get_user_class_color($user["class"],$user["username"])."</a>";

}
$agent=$hua;
if (!empty($agent) && $date) {

$hache = crc32(htmlentities($agent));
$agent = htmlentities($agent);

$result = sql_query("SELECT id FROM useragent WHERE crc32=$hache ORDER BY id");
$num = mysql_fetch_assoc($result);
if ($num){
$n1=$num["id"];
$n=$num["id"].",";
$nn=",".$num["id"].",";
$chislo=0;
$query = mysql_query("SELECT id,username,class FROM users WHERE idagent LIKE '%$nn%' OR idagent LIKE '$n' ORDER BY last_access DESC");

while ($qu = mysql_fetch_array($query))
{
$id=$qu["id"];
$username=$qu["username"];
$class=$qu["class"];


if ($who[$num["id"]])
$who[$num["id"]].=", ";
$who[$num["id"]].= "<a href=userdetails.php?id=$id>".get_user_class_color($class,$username)."</a>";
$chislo++;

}
}}

if (!empty($date)){
echo "<td class=b >$date</a></b><br>$time</td>";
echo "<td class=b ><a href=/usersearch.php?n=&rt=0&r=&r2=&st=0&em=&ip=$ip>$ip</a><br>$use</td>";
echo "<td class=b >".htmlentities($hua)." ".($who[$num["id"]] ? "<br><b>Браузер был у</b>: ".$who[$num["id"]]."":"")."</td>";
echo "<td class=b ><a href=$from>$from</td>";
echo "<td class=b >".htmlentities($host)." <hr>$get <br>$post</td>";
echo "</tr>";
}
unset($username);unset($who[$num["id"]]);unset($use,$get,$post);
}

fclose($fop);

?>
<table width="100%" border="0">
<?


echo"<hr>
<form method=\"post\" action=\"hacklog.php\" name=\"add\">
<input type=\"submit\" name=\"Submit\" value=\"Очистить статистику\">
<input type=\"hidden\" name=\"prune\" value=\"prune\">
</form>";


 stdfootchat() ?>