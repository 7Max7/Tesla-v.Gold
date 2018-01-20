<?
require "include/bittorrent.php";
dbconn(false);

loggedinorreturn();

if (get_user_class() < UC_SYSOP)
stderr("Извините", "Доступа нет!");
accessadministration();

/// добавить повторные ip и очистку - отключение функции контроля
stdheadchat("Мониторинг данных [в стадии дополнений]");


$res2 = sql_query("SELECT id, username, class FROM users WHERE monitoring='yes' ORDER BY id") or sqlerr(__FILE__, __LINE__); 
while ($arr2 = mysql_fetch_assoc($res2))  
{
   if ($monitor_active)
   $monitor_active .= ", ";
   $monitor_active .= "<a href=monitoring.php?id=" . $arr2["id"] . ">".get_user_class_color($arr2["class"], $arr2["username"])."</a> <a href=userdetails.php?id=" . $arr2["id"] . "><img src=pic/buddylist.gif alt=\"Показать данные о пользователе\"></a>";
    $user_monitor_active++;
}

echo "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\"><tr><td>"."<b>В мониторинге (<font color=red>".$user_monitor_active."</font>)</b>: ".$monitor_active."</td></tr></table>"; 


$id = (int)$_GET["id"];
if ($id){

  
$r = sql_query("SELECT * FROM users WHERE id=$id and monitoring='yes'") or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_array($r) or die("<h2>Данный пользователь ($id) не в списках мониторинга.</h2>");

if(!file_exists(ROOT_PATH."cache/monitoring_$id.txt"))
{die("Файл для $user[username] нет в памяти, видимо удален.");
}

///////////////// формируем данные о пользователе
if ($user[last_login] == "0000-00-00 00:00:00")
	$last_login = 'Неизвестно';
else
	$last_login = normaltime($user[last_login], true)." (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["last_login"])) . " ".$tracker_lang['ago'].")";
	

if ($user[added] == "0000-00-00 00:00:00")
	$joindate = 'N/A';
else
	$joindate = normaltime($user[added], true)." (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["added"])) . " ".$tracker_lang['ago'].")";
	

$lastseen = normaltime($user["last_access"], true);
if ($user["last_access"] == "0000-00-00 00:00:00")
$lastseen = $tracker_lang['never'];
	
$lastseen.= " (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["last_access"])) . " ".$tracker_lang['ago'].")";


$lastchat = normaltime($user["chat_access"], true);
if ($user["chat_access"] == "0000-00-00 00:00:00")
$lastchat = $tracker_lang['never'];


$data_user[$id]="<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"center\" class=\"b\">
<b>Зарегистрирован</b>: $joindate <b>Последний вход</b>: $last_login<br>
<b>Активность</b>: $lastseen 
<b>Посл. время в чате</b>: $lastchat ".($user["chat_access"] <> "0000-00-00 00:00:00" ? "
(" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["chat_access"])) . " ".$tracker_lang['ago'].")":"")."
</td></tr></table>";
///////////////// формируем данные о пользователе

echo $data_user[$id];

echo "<table cellpadding=\"4\" cellspacing=\"1\" border=\"0\" width=\"100%\" class=\"tableinborder\">
<td class=\"tabletitle\" valign=\"top\">Данные</td>
<td class=\"tabletitle\" valign=\"top\">Запрос</td>
<td class=\"tabletitle\" valign=\"top\">IP</td>
<td class=\"tabletitle\" valign=\"top\">Браузер</td>
<td class=\"tabletitle\" valign=\"top\">Время</td>";

 $fop = fopen (ROOT_PATH."cache/monitoring_$id.txt", "r+");
 $num=0;
while (!feof($fop))
{
$read = fgets($fop, 1000);
list($data,$host, $ip,$ag,$time,$evant) = explode('#',$read);


	$events = explode("||",$evant);
	$get = nl2br(htmlspecialchars(print_r(unserialize($events[0]),true)));
	$post = nl2br(htmlspecialchars(print_r(unserialize($events[1]),true)));
////	$event = nl2br(htmlspecialchars(print_r($events[2],true)));
	$event .= "<hr>".$events[3];
//	$ref = $events[3];


$decode_url=urldecode($host);

$decode = "<hr>$decode_url";

echo "<tr bgcolor=".($data ? "lightgray" : "white").">
<td class=tablea >$data $get $post</td>";

echo "<td class=tablea >
".($host ? "<a href=$DEFAULTBASEURL$host>$host</a> $ref":"")."
</td>";
echo "<td class=tablea >$ip</td>";
echo "<td class=tablea >$ag</td>";
echo "<td class=tablea >$time</td>";
echo " </tr>";
 $num++;
 unset($data_user);
}
fclose($fop);
}

else
{
  echo("<h2>".$tracker_lang['invalid_id']."</h2><br>");
  
   stdfootchat();
  }
?>


<?
/*
print("<form method=\"post\" action=\"monitoring.php\">\n");
  
print("<tr><td class=\"rowhead\">Мониторинг</td><td colspan=\"2\" align=\"left\">
<input type=radio name=monitoring value=yes" .($user["monitoring"]=="yes" ? " checked" : "") . ">Да<input type=radio name=monitoring value=no" .($user["monitoring"]=="no" ? " checked" : "") . ">Нет 
".($user["monitoring"]=="no" ? "<input type=\"text\" name=\"monitoring_reason\" size=\"35\" /> <i>Причина</i><br><i>слежение за всеми запросами и данными о входе в спец файл</i>" : "")."
</td></tr>\n");

print("<tr><td class=\"rowhead\">Очистить журнал</td>
		  <td colspan=\"2\" align=\"left\"> 
          <input type=checkbox name=prune_monitoring value=yes>
		  <i> чистка логов по данному пользователю</i>
		  </td></tr>\n");



  print("<tr><td colspan=\"3\" align=\"center\"><input type=\"submit\" class=\"btn\" value=\"Подтверждение\"></td></tr>\n");
*/
 stdfootchat(); ?>