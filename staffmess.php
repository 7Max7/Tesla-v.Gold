<?

require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

if (get_user_class() <= UC_MODERATOR) {
attacks_log('staffmess'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

//var_dump($_POST);


if ($_SERVER["REQUEST_METHOD"] == "POST"){


$sender_id = ($_POST['sender'] == 'system' ? 0 : $CURUSER['id']);
$dt = sqlesc(get_date_time());
$msg = htmlspecialchars($_POST['msg']);
if (!$msg){
header("Refresh: 5; url=staffmess.php");
stderr($tracker_lang['error'],"Пожалуйста, введите сообщение!");
}

$subject = htmlspecialchars($_POST['subject']);
if (!$subject){
header("Refresh: 5; url=staffmess.php");
stderr($tracker_lang['error'],"Пожалуйста, введите тему!");
}

$clases = $_POST['clases'];
if (empty($clases)){
header("Refresh: 5; url=staffmess.php");
stderr($tracker_lang['error'],"Выберите 1 или более классов для отправки сообщения.");
}
/*$query = sql_query("SELECT id FROM users WHERE class IN (".implode(", ", array_map("sqlesc", $clases)).")");

while ($dat=mysql_fetch_assoc($query)) {
	sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES ($sender_id, $dat[id], '" . get_date_time() . "', " . sqlesc($msg) .", " . sqlesc($subject) .")") or sqlerr(__FILE__,__LINE__);
}*/

$else = $CURUSER["id"];

sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) SELECT $sender_id, id, NOW(), ".sqlesc($msg).", ".sqlesc($subject)." FROM users WHERE id<>$else and class IN (".implode(", ", array_map("sqlesc", $clases)).")") or sqlerr(__FILE__,__LINE__);
$counter = mysql_affected_rows();

$user = $CURUSER["username"];
$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER["username"]);
write_log("Массовое сообщение от пользователя $user (Отправлено $counter сообщений)\n", "$user_color","other");

header("Refresh: 5; url=staffmess.php");

stderr("Успешно", "Отправлено $counter сообщений.");
stdfoot();
die;
}


stdhead("Общее сообщение");

echo "<table class=main width=100% border=0 cellspacing=0 cellpadding=0>";
echo "<tr><td class=b>";

echo "<form method=\"post\" action=\"staffmess.php\" name=\"msg\">";
echo "<table align=\"center\" cellspacing=\"0\" cellpadding=\"0\">";

echo "<tr><td class=\"a\" colspan=\"2\">Общее сообщение всем членам администрации и пользователям</td></tr>";

echo "<tr><td align=\"center\" class=\"b\"><b>Выделите группу кому нужно отправить сообщение</b>: <br />";

echo "<table style=\"border: 0\" width=\"100%\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\"><tr align=\"left\">";

echo "<td><label><input type=\"checkbox\" name=\"clases[]\" value=\"".UC_USER."\"><font color=#000000>".get_user_class_name(UC_USER)."</font></label></td>";

echo "<td><label><input type=\"checkbox\" name=\"clases[]\" value=\"".UC_POWER_USER."\"><font color=#D21E36>".get_user_class_name(UC_POWER_USER)."</font></label></td>";


echo "<td><label><input type=\"checkbox\" name=\"clases[]\" value=\"".UC_VIP."\"><font color=#9C2FE0>".get_user_class_name(UC_VIP)."</font></label></td>";

echo "<td><label><input type=\"checkbox\" name=\"clases[]\" value=\"".UC_UPLOADER."\"><font color=#ffa500>".get_user_class_name(UC_UPLOADER)."</font></label></td></tr><tr>";

echo "<td><label><input type=\"checkbox\" name=\"clases[]\" value=\"".UC_MODERATOR."\"><font color=#ff0000>".get_user_class_name(UC_MODERATOR)."</font></label></td>";

echo "<td><label><input type=\"checkbox\" name=\"clases[]\" value=\"".UC_ADMINISTRATOR."\"><font color=#008000>".get_user_class_name(UC_ADMINISTRATOR)."</font></label></td>";

echo "<td><label><input type=\"checkbox\" name=\"clases[]\" value=\"".UC_SYSOP."\"><font color=#0000ff>".get_user_class_name(UC_SYSOP)."</font></label></td>";


echo "<td style=\"border: 0\">&nbsp;</td>";
echo "<td style=\"border: 0\">&nbsp;</td>";

echo "</tr></table></td></tr>";

echo "<td colspan=\"2\" class=\"a\"><b>Тема</b>: <input name=\"subject\" type=\"text\" size=\"90\"></td></tr>";

echo "<tr><td align=\"center\">";

echo textbbcode("message","msg");
//<textarea name=msg cols=80 rows=15>$body</textarea>

echo "</td></tr>";

echo "<tr><td class=\"a\" colspan=2>
<div align=\"center\"><b>Отправитель:&nbsp;&nbsp;</b>
<label><b>".get_user_class_color($CURUSER['class'], $CURUSER['username'])."</b>
<input name=\"sender\" type=\"radio\" value=\"0\" checked>&nbsp;&nbsp;</label>";

if (get_user_class() > UC_MODERATOR)
echo "<label><font color=gray>[<b>System</b>]</font><input name=\"sender\" type=\"radio\" value=\"system\"></label>";

echo "</div></td></tr>";

echo "<tr><td colspan=2 align=center>
<input type=\"hidden\" name=\"receiver\" value=\"".$receiver."\">
<input type=\"submit\" value=\"Отправить\" class=\"btn\">
</td></tr>";

echo "</table>
</form>
</div></td></tr></table>";

stdfoot();
?>