<?

require "include/bittorrent.php";
dbconn();
loggedinorreturn();
accessadministration();
stdhead("Не подтвержденные пользователи");
begin_main_frame();
begin_frame("Не подтвержденные пользователи");


if (get_user_class() < UC_ADMINISTRATOR) {
attacks_log('unco'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

if ($_POST["action"] == "confirmuser") {
	$userid = (int) $_POST["userid"];

	if (!is_valid_id($userid))
		stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
	$updateset[] = "status = " . sqlesc($confirm);
	$updateset[] = "last_login = ".sqlesc(get_date_time());
	$updateset[] = "last_access = ".sqlesc(get_date_time());
	//print("UPDATE users SET " . implode(", ", $updateset) . " WHERE id=$userid");
	sql_query("UPDATE users SET " . implode(", ", $updateset) . " WHERE id = $userid") or sqlerr(__FILE__, __LINE__);

	header("Location: $DEFAULTBASEURL/unco.php");
	die;
}

$res = sql_query("SELECT * FROM users WHERE status='pending' ORDER BY username" ) or sqlerr(__FILE__, __LINE__);
if( mysql_num_rows($res) != 0 )
{
print'<br /><table width=100% border=1 cellspacing=0 cellpadding=5>';
print'<tr>';
print'<td class=rowhead><center>Логин</center></td>';
print'<td class=rowhead><center>IP</center></td>';
print'<td class=rowhead><center>Почта</center></td>';

print'<td class=rowhead><center>Регистрация</center></td>';
print'<td class=rowhead><center>Активировать?</center></td>';
print'<td class=rowhead><center>Подтвердить</center></td>';
print'</tr>';
while($row = mysql_fetch_assoc($res))
{
$id = $row['id'];
print'<tr><form method=post action=unco.php>';
print'<input type=hidden name=\'action\' value=\'confirmuser\'>';
print("<input type=hidden name='userid' value='$id'>");
print("<input type=hidden name='returnto' value='unco.php'>");
print'<a href="userdetails.php?id=' . $row['id'] . '"><td><center>'.get_user_class_color($row['class'], $row['username']).'</center></td></a>';
print'<td align=center><a target="_blank" href="/usersearch.php?n=&rt=0&r=&r2=&st=0&em=&ip='.$row['ip'].'">
' . $row['ip'] . '</a></td>';
print'<td align=center>' . $row['email'] . '</td>';
print'<td align=center>' . $row['added'] . '</td>';
print'<td align=center><select name=confirm><option value=pending>Нет</option><option value=confirmed>Да</option></select></td>';
print'<td align=center><input type=submit value="OK" style=\'height: 20px; width: 40px\'>';
print'</form></tr>';
}
print '</table>';
}
else
{
	print 'Нет не подтвержденных пользователей...';
}

end_frame();
end_main_frame();
stdfoot();
?>