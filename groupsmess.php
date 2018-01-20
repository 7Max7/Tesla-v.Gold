<?
require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

if (get_user_class() <= UC_MODERATOR)
{
attacks_log('groupsmess'); 
stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

if (!empty($_POST['msg']) && !empty($_POST['subject'])){

//if ($_GET["id_groups"]==0){	stderr($tracker_lang['error'], "Ммм id равно 0 ? У нас нет группы из пустоты.");}

if ($_SERVER["REQUEST_METHOD"] <> "POST")
 stderr($tracker_lang['error'], "Шутник!");
 
$groups = (int)$_POST['gropus'];

$sender_id = ($_POST['sender'] == 'system' ? 0 : $CURUSER['id']);

$else=($_POST['sender'] == 'system' ? 92 : $CURUSER['id']);

$dt = sqlesc(get_date_time());
$msg = htmlspecialchars($_POST['msg']);
if (!$msg)
stderr($tracker_lang['error'],"Пожалуста, введите сообщение!");

$subject = htmlspecialchars($_POST['subject']);
if (!$subject)
stderr($tracker_lang['error'],"Пожалуста, введите тему!");



$res2 = sql_query("SELECT name FROM groups WHERE id=".sqlesc($groups)) or sqlerr(__FILE__, __LINE__); 
$row2 = mysql_fetch_array($res2);
$nameg=htmlspecialchars($row2["name"]);
if (empty($nameg)){
stderr($tracker_lang['error'],"Пожалуста, выберите группу, которая существует!");
}

$msg=$msg."[hr]Данное сообщение было отправленно всем кто в группе [b] $nameg [/b]";

sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) SELECT $sender_id, id, NOW(), ".sqlesc($msg).", ".sqlesc($subject)." FROM users WHERE id<>$else AND enabled='yes' AND status = 'confirmed' AND groups=".sqlesc($groups)) or sqlerr(__FILE__,__LINE__);
$counter = mysql_affected_rows();

if (!empty($counter))
sql_query("UPDATE users SET unread=unread+1 WHERE id<>$else AND enabled='yes' AND status = 'confirmed' AND groups=".sqlesc($groups)) or sqlerr(__FILE__, __LINE__);


$user = $CURUSER["username"];
$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER[username]);
write_log("Массовое сообщение группе от $user ($counter штук)\n", "$user_color","other");

header("Refresh: 5; url=groupsmess.php");

stderr("Успешно", "Отправлено $counter сообщений группе $nameg");
stdfoot();
die;
}


if (!empty($_GET["id_groups"])){


$groups=(int)$_GET["id_groups"];
$num_groups=0;
$res = sql_query("SELECT u.id, u.username, u.class, g.name FROM users AS u
LEFT JOIN groups AS g ON g.id=groups
WHERE u.enabled='yes' AND u.status = 'confirmed' AND u.groups=".sqlesc($groups)."") or sqlerr(__FILE__, __LINE__); 
while ($row1 = mysql_fetch_array($res))
{
if ($user)
$user.=", ";

$user.= "<a href=userdetails.php?id=" . $row1["id"] . ">".get_user_class_color($row1["class"], $row1["username"])."</a></a>";
$num_groups++;
$name=$row1["name"];
}

if ($num_groups==0)
{
stderr($tracker_lang['error'], "В группе <b>$name</b> нулевая активность пользователей, т е В ней никого нет.");
}

stdhead("Общее сообщение для групп", false);
?>
<table class=main width=100% border=0 cellspacing=0 cellpadding=0>
<tr><td class=b>


<table align=center cellspacing=0 cellpadding=0>
<form method=post name=message action=groupsmess.php>

<tr><td class="a" align=center colspan="2">Общее сообщение всем пользвателям группы: <?=$name;?></td></tr>
<tr>
<td align=left class=b><b>Количество писем для отправки</b>:  <?=$num_groups;?><br />
<b>Получатели этого сообщения</b>: 
  <table style="border: 0" width="100%" cellpadding="0" cellspacing="0">
    <tr>
       <?=$user;?>
       <td style="border: 0">&nbsp;</td>
      </tr>
    </table>
  </td>
</tr>
<td colspan="2" class="a"><b>Тема</b>:
<input name="subject" type="text" size="70"></td>
</tr>
<tr><td align="center">
<?textbbcode("message","msg",$body);?>

</td></tr>
<tr>
<td class="a" colspan=2><div align="center"><b>Отправитель:&nbsp;&nbsp;</b>
<b><?=get_user_class_color($CURUSER['class'], $CURUSER['username'])?></b>
<input name="sender" type="radio" value="self" checked>&nbsp;&nbsp;

<? if (get_user_class() > UC_MODERATOR) {?>
<font color=gray>[<b>System</b>]</font>
<input name="sender" type="radio" value="system">
<? }?>

</div></td></tr>
<tr><td colspan=2 align=center>
<input type="hidden" name="gropus" value="<?=$groups?>"/>
<input type=submit value="Отправить групповое сообщение" class=btn></td></tr>
</table>
<input type=hidden name=receiver value=<?=$receiver?>>
</form>

 </div></td></tr></table>
<?

}
else
{
	
	
stdhead("Отправка писем по группам");

begin_frame("Выберите группу из списка кому хотим отправить");
?>
<div align=center>
<form name="groups" action="groupsmess.php" method="get">
<table border="1" cellspacing="0" cellpadding="5">
<?

$s = "<select name=\"id_groups\">\n<option value=\"0\">(".$tracker_lang['choose'].")</option>\n";

$res = sql_query("SELECT id, name FROM groups ORDER BY id  ASC");
while ($row1 = mysql_fetch_array($res))
{
$s .= "<option value=\"" . $row1["id"] . "\">" . htmlspecialchars($row1["name"]) . "</option>\n";
}
$s .= "</select>\n";
echo $s;

?>
<tr><td align="center" colspan="2" style="border:0;"><input type="submit" class=btn value="Группа выбрана, жму Далее" /></td></tr>
</table>
</form>
<?
end_frame();
print("<br>");
	
}
stdfoot();
?>