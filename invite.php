<?
require "include/bittorrent.php";
gzip();
dbconn();
loggedinorreturn();


/**
ALTER TABLE `invites` ADD `email` VARCHAR(80) NULL DEFAULT NULL AFTER `invite`;
ALTER TABLE `invites` ADD `confirmd5` VARCHAR(64) NULL DEFAULT NULL AFTER `time_invited`;
**/

stdhead("Приглашения");

$type_as = (isset($_GET["type_as"]) ? unesc($_GET["type_as"]):"");
$new_do = (isset($_GET["new_do"]) ? unesc($_GET["new_do"]):"");


if (!empty($type_as) || !empty($new_do)) {

if ($type_as=="new_as") {

$id = (isset($_GET["id_as"]) ? (int)$_GET["id_as"]:0);

if (!is_numeric($id) || empty($id))
stderr($tracker_lang['error'], $tracker_lang['invalid_id']);


if ($id == 0) {
	$id = $CURUSER["id"];
}

if (get_user_class() <= UC_MODERATOR)
	$id = $CURUSER["id"];

$re = sql_query("SELECT invites FROM users WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
$tes = mysql_fetch_assoc($re);

if ($tes["invites"] <= 0)
bark("У вас больше не осталось приглашений!");


echo "<table class=\"main\" border=\"0\" align=\"center\" cellspacing=\"0\" cellpadding=\"5\" width=100%>
<form name=\"invite\" action=\"invite.php?new_do=yes\" method=\"post\">

<td class=colhead colspan=7>Отправка приглашения на почту, ввод формы.</td></tr>

<tr>
<td class=\"a\"><b>Введите почту</b>:</td>
<td align=left><input name=\"email\" type=\"text\" size=\"50\"></td>
</tr>

<tr>
<td class=\"a\"><b>Введите текст сообщения</b>:</td>
<td align=left><textarea cols=60% maxLength=255 rows=2 name=textarea>Приглашаю...</textarea><br/>Ваш комментарий добавится в письмо вместе с приглашением.</td>
</tr>

<tr><td align=\"center\" colspan=\"2\">
<input type=\"hidden\" name=\"id\" value=\"$id\"/>
<input type=\"hidden\" name=\"new_do\" value=\"yes\"/>
<input type=\"submit\" class=\"btn\" value=\"Отправить приглашение на почту\" /></td></tr>
</td></tr>

</form></table>";


stdfoot();

die;
}
elseif ($new_do=="yes") {

$id = (isset($_POST["id"]) ? (int)$_POST["id"]:0);

if (!is_numeric($id) || empty($id))
stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

if ($id == 0) {
	$id = $CURUSER["id"];
}
$hash  = md5(mt_rand(1, 1000000));

if (get_user_class() <= UC_MODERATOR)
	$id = $CURUSER["id"];

$re = sql_query("SELECT invites,username FROM users WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
$tes = mysql_fetch_assoc($re);

if ($tes["invites"] <= 0)
bark("У вас больше не осталось приглашений!");

$email=(isset($_POST["email"]) ? htmlentities($_POST["email"]):"");
$textarea=(isset($_POST["textarea"]) ? htmlspecialchars($_POST["textarea"]):"");

if (!validemail($email))
stderr($tracker_lang['error'], "Это не похоже на реальный email адрес. Повторите снова.");


$hash  = md5(mt_rand(1, 1000000));
$confirmd5 = md5(mt_rand(1, 1008000000));

$name=$tes["username"];

$body = <<<EOD
Ваш знакомый, логин которого $name, приглашает вас на сайт $SITENAME. Он указал эту почту для приглашения ($email).

Если это не ваша почта или не знаете о чем речь, пожалуйста проигнорируйте это письмо.
 
Указан комментарий к приглашению: $textarea

Для подтверждения вашей регистрации (легкая, состоит из ввода логина и пароля!), вам нужно пройти по следующей ссылке:

$DEFAULTBASEURL/takeinvite.php?psecret=$confirmd5

Ваш пригласительный код: $hash (при легкой регистрации не нужен)

После того как вы это сделаете, вы сможете использовать ваш аккаунт. Мы рекомендуем вам прочитать правила
и ЧаВо, прежде чем вы начнете использовать $SITENAME.
EOD;

$subject = <<<EOD
Приглашение на $SITENAME от $name
EOD;



if (!sent_mail($email,$SITENAME,$SITEEMAIL,$subject,$body,false)) {
stdmsg("Ошибка", "Приглашение не отправлено на почту. Возможно такая почта не существует или на сайте неполадки.");

echo "<script>setTimeout('document.location.href=\"invite.php?id=$id\"', 15000);</script>";
}
else
{
	
sql_query("INSERT INTO invites (inviter, invite, time_invited,email,confirmd5) VALUES (" . implode(", ", array_map("sqlesc", array($id, $hash, get_date_time(),$email,$confirmd5))) . ")") or sqlerr(__FILE__,__LINE__);
sql_query("UPDATE users SET invites = invites - 1 WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

stdmsg("Успешно", "Приглашение отправленно на почту. Сейчас мы вас переадресуем на страницу приглашений.");
echo "<script>setTimeout('document.location.href=\"invite.php?id=$id\"', 5000);</script>";
}


stdfoot();



}

die;
}




$id = (isset($_GET["id"]) ? (int) $_GET["id"]:"0");
$type = (isset($_GET["type"]) ? unesc($_GET["type"]):"");
$invite = (isset($_GET["invite"]) ? htmlentities($_GET["invite"]):"");


function bark($msg) {
	stdmsg("Ошибка", $msg);
      stdfood();
      die;
}

if ($id == 0) {
	$id = $CURUSER["id"];
}

$res = sql_query("SELECT invites FROM users WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);

$inv = mysql_fetch_assoc($res);

if ($inv["invites"] <> 1) {
	$_s = "ний";
} else {
	$_s = "ние";
}


if ($type == 'new') {

$id = (int) $_GET["id"];

if (!is_numeric($id) || !isset($id))
stderr($tracker_lang['error'], $tracker_lang['invalid_id']);


if ($id == 0) {
	$id = $CURUSER["id"];
}

if (get_user_class() <= UC_MODERATOR)
	$id = $CURUSER["id"];

$re = sql_query("SELECT invites FROM users WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
$tes = mysql_fetch_assoc($re);

if ($tes["invites"] <= 0)
	bark("У вас больше не осталось приглашений!");

$hash  = md5(mt_rand(1, 1000000));

sql_query("INSERT INTO invites (inviter, invite, time_invited) VALUES (" . implode(", ", array_map("sqlesc", array($id, $hash, get_date_time()))) . ")") or sqlerr(__FILE__,__LINE__);
sql_query("UPDATE users SET invites = invites - 1 WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

stdmsg("Успешно", "Простое приглашение добавлено. Сейчас мы вас переадресуем на страницу приглашений.");
stdfoot();
echo "<script>setTimeout('document.location.href=\"invite.php?id=$id\"', 3000);</script>";
die;

} elseif ($type == 'del') {

	$ret = sql_query("SELECT * FROM invites WHERE invite = ".sqlesc($invite)) or sqlerr(__FILE__,__LINE__);
	$num = mysql_fetch_assoc($ret);
	if ($num["inviter"]==$id) {
		sql_query("DELETE FROM invites WHERE invite = ".sqlesc($invite)) or sqlerr(__FILE__,__LINE__);
		sql_query("UPDATE users SET invites = invites + 1 WHERE id = ".$CURUSER["id"]) or sqlerr(__FILE__,__LINE__);
		stdmsg("Успешно", "Приглашение удалено. Сейчас мы вас переадресуем на страницу приглашений.");
        stdfoot();
	} else {
            stdmsg("Ошибка", "Вам не разрешено удалять приглашения.");
           stdfoot();
     }
	 echo "<script>setTimeout('document.location.href=\"invite.php?id=$id\"', 1000);</script>";
     die;
  
} else {
	if (get_user_class() <= UC_UPLOADER && !($id == $CURUSER["id"])) {
		bark("У вас нет права видеть приглашения этого пользователя.");
	}

	$rel = sql_query("SELECT COUNT(*) FROM users WHERE invitedby = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
	$arro = mysql_fetch_row($rel);
	$number = $arro[0];

	$ret = sql_query("SELECT id, username, class, status, warned, enabled, donor, email,uploaded,added FROM users WHERE invitedby = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
	$num = mysql_num_rows($ret);
    $num_pend=0;
	
	
	

if ($use_10proc==1){
echo "<table width=100%  class=\"main\" cellspacing=0 cellpadding=5><tr><td class=\"colhead\" colspan=2>Внимание включена опция</td></tr>";

echo "<tr>
<td class=\"b\" align=\"center\" valign=\"top\" width=\"50%\">
Пригласив пользователей, вы будете получать 10% отданного ими трафика каждый день. Всё, что вам нужно сделать - это отправить им приглашение (форма ниже).<br>
Маленькое условие для пригласителя: Быть на сайте раз месяц и не быть забаненым.<br>
Помните, вы в <u>ответе за приглашенного</u>!
</td>
</tr>";

echo "</table><br>";
}


	echo("<table class=\"main\" border=1 width=100% cellspacing=0 cellpadding=5>
	<form method=post action=takeconfirm.php?id=$id>	
	<tr class=tabletitle>
	<td class=colhead colspan=7>Приглашенные пользователи ($number)</td></tr>");

	if(!$num) {
		print("<tr class=tableb><td colspan=7>Вы никого не приглашали. Список пуст.</tr>");
	} else {
		print("<tr class=tableb>
		<td class=\"a\" align=left><b>Логин</b></td>
		<td class=\"a\" align=left><b>Почта</b></td>
		<td class=\"a\" align=center><b>Раздал</b> (вам 10%)</td>
		<td class=\"a\" align=center><b>Статус</b></td>");
		
		if ($CURUSER["id"] == $id || get_user_class() >= UC_SYSOP)
			print("<td class=\"a\" align=center><b>Подтвердить</b></td>");
		print("</tr>");
		
		for ($i = 0; $i < $num; ++$i) {
			$arr = mysql_fetch_assoc($ret);
			if ($arr["status"] == 'pending')
				$user = "<td align=left>$arr[username]</td>";
			else
		  		$user = "<td align=left><a href=userdetails.php?id=$arr[id]>" . get_user_class_color($arr["class"], "$arr[username]") . "</a>" 
  . ($arr["warned"]  == "yes" ? "&nbsp;<img src=pic/warned.gif border=0 alt='Предупрежден'>" : "") 
  . ($arr["enabled"] == "no" ? "&nbsp;<img src=pic/disabled.gif border=0 alt='Отключен'>" : "") 
  . ($arr["donor"]  == "yes" ? "&nbsp;<img src=pic/star.gif border=0 alt='Донор'>" : "")."</td>";
  
  

			print "<tr class=tableb>$user<td>$arr[email]</td>";

           $aold="2010-06-10 16:45:44";
           if ($use_10proc==1){
           $round=round($arr["uploaded"]/10);
           $to10="<b title=\"Время включения фиксирования аплоада $aold\">(".mksize($round).")</b>";
           }

			
			echo "<td align=center>".mksize($arr["uploaded"])." $to10</td>";
			echo "<td align=center>".($arr["status"] == 'confirmed' ? "Подтвержден":"<b>Не подтвержден</b>")."</td>";

			if ($CURUSER["id"] == $id || get_user_class() == UC_SYSOP) {
				print("<td align=center>");
				
				if ($arr["status"] == 'pending'){
					print("<input type=\"checkbox\" name=\"conusr[]\" value=\"" . $arr["id"] . "\" />");
					++$num_pend;
					}
					else {
				print("Подтвержден");
				}
					
				print("</td>");
			}
			print("</tr>");
		}
	}
	if (($CURUSER["id"] == $id || get_user_class() >= UC_SYSOP) && !empty($num_pend)) {
	///	print("<input type=hidden name=email value=$arr[email]>");
		print("<tr class=tableb><td colspan=7 align=right><input type=submit value=\"Подтвердить пользователей\"></td></tr>");
	}
	print("</form></table><br>");

	$rul = sql_query("SELECT COUNT(*) FROM invites WHERE inviter =".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
	$arre = mysql_fetch_row($rul);
	$number1 = $arre[0];
	$rer = sql_query("SELECT inviteid, invite, time_invited,email FROM invites WHERE inviter = ".sqlesc($id)." AND confirmed='no'") or sqlerr(__FILE__,__LINE__);
	$num1 = mysql_num_rows($rer);

	echo "<table  class=\"main\" border=1 width=100% cellspacing=0 cellpadding=5>".
	"<tr class=tabletitle><td class=\"colhead\" colspan=6>Статус созданых приглашений ($number1)</td></tr>";

	if(!$num1) {
		echo "<tr class=tableb><td colspan=6>На данный момент вами не создано ни одного приглашения.</tr>";
	} else {
		echo "<tr class=tableb>
		<td class=\"a\"><b>Код приглашения</b></td>
		<td class=\"a\"><b>Почта (для кого)</b></td>
		<td class=\"a\"><b>Время</b></td>
		<td class=\"a\"><b>Действие</b></td>
		</tr>";
		for ($i = 0; $i < $num1; ++$i) {
			$arr1 = mysql_fetch_assoc($rer);
			echo "<tr class=tableb><td>$arr1[invite]</td>";
			echo "<td>".(empty($arr1["email"]) ? "это простое приглашение":htmlentities($arr1["email"]))."</td>";
			echo "<td>$arr1[time_invited]</td>";
			echo "<td><a href=\"invite.php?invite=$arr1[invite]&type=del\">Удалить</a></td></tr>";
		}
	}
	print("</table><br>");
	
	
	
	
	echo("<table width=100%  class=\"main\" cellspacing=0 cellpadding=5><tr><td class=\"colhead\" colspan=2>Панель новых приглашений".(!empty($inv["invites"]) ? " (доступно ".$inv["invites"].")":"")."</td></tr>");

	
///	":"Лимит <b>для новых</b> приглашений исчерпан.")."
if (!empty($inv["invites"])){
	 echo "<tr>
	<td class=\"b\" align=\"center\" valign=\"top\" width=\"50%\">
	<form method=\"get\" action=\"invite.php\">
	<input type=\"hidden\" name=\"id\" value=\"$id\"/>
	<input type=\"hidden\" name=\"type\" value=\"new\"/>
	<input type=\"submit\" class=\"btn\" value=\"Простое приглашение\">
	</form>
	<br><small>Будет создан обычный пригласительный код, он вводится на регистрационной страничке.</small>
	</td>
	
	<td class=\"b\" align=\"center\" valign=\"top\" width=\"50%\">
	<form method=\"get\" action=\"invite.php\">
	<input type=\"hidden\" name=\"id_as\" value=\"$id\"/>
	<input type=\"hidden\" name=\"type_as\" value=\"new_as\"/>
	<input type=\"submit\"  class=\"btn\" value=\"Приглашение отправленное на почту\">
	</form>
<small>Такой тип приглашения позволяет без проблем зарегистрироватся на сайте, пройти по ссылки из письма (которое пришло на почту) и ввести лишь логин и пароль для новой учетной записи.</small>
	</td>

	</tr>";
	}
	else
	{
	echo "<tr></tr>
	<td class=\"a\">Лимит <b>для новых</b> приглашений исчерпан, попробуйте удалить неиспользуемые уже созданные приглашения.</td>
	<tr></tr>";	
	}
	echo "</table>";


}
stdfoot();

?>