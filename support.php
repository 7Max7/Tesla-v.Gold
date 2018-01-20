<?php

require_once("include/bittorrent.php");
dbconn();
global $CURUSER;

stdheadchat("Служба техподдержки сайта");

$sendwho = 0;/// 1 посылаем на почту боссам, иначе 0 посылаем в ЛС на сайте.

if ($_GET["send"]) {

//if ($_GET["send"]==1)
//stdmsg("Успешно", "Данные на почту отправлены. <a href=\"$BASEURL/support.php\">Вернутся назад</a>");
//else
stdmsg("Успешно", "Сообщение отправлено администрации. <a href=\"$BASEURL/support.php\">Вернутся назад</a>");

stdfootchat();
die;
}

if (count($_POST) || $_SERVER["REQUEST_METHOD"] == "POST") {

$tica = (isset($_POST["tica"]) ? $_POST["tica"]:"");
$ip = getip();
$sid = session_id();
$timeday = date("Y-m-d");

if ($tica <> md5($ip.$sid.$timeday)){

stdmsg("АнтиБот система", "Пожалуйста вернитесь обратно и повторите попытку снова.");

stdfootchat();
die;
}


$username = htmlspecialchars(strip_tags($_POST["username"]));/// имя пользователя если - не авторизован
$text = htmlspecialchars_uni($_POST["text"]); /// текст
$subject = htmlspecialchars_uni($_POST["subject"]); /// текст
$ip = getip(); 
$now = get_date_time();

if (empty($subject) || empty($text)) {
stdmsg("Ошибка данных", "Пусто в поле тема или текст сообщения. <a href=\"$BASEURL/support.php\">Вернутся назад</a>");
stdfootchat();
die;
}


if (!empty($CURUSER))
$username = $CURUSER["username"]." [".$CURUSER["id"]."] (авторизованный пользователь)";
elseif (empty($username) && empty($CURUSER))
$username = "аноним (не представился пользователь)";

$subj = $SITENAME.": ".$subject;

$all = "Тема сообщения: ".$subject."\n\nТекст: ".$text."\n\nip адрес: ".$ip."\nВводимое имя: ".$username."\n\nВремя события: ".$now."\n\nCвязь с админстрацией - $BASEURL/support.php (отсюда пришло письмо) \nСистема сайта ".$SITENAME.".";/// текст сообщения



if ($subject == "Владельцу движка Tesla TT v.Gold" || stristr($subject,"Tesla")) {

if (sent_mail("maksim7777Max7@rambler.ru",$SITENAME,$SITEEMAIL,$subj,$all,false)) {
stdmsg("Успешно", "Данные отправлены владельцу движка Tesla TT v.Gold на почту. Ждите ответа... <a href=\"$BASEURL/support.php\">Вернутся назад</a>");
stdfootchat();
die;
}

}


if ($sendwho == 1) { /// на почту отправляем

$res = sql_query("SELECT email FROM users WHERE class=".UC_SYSOP."") or sqlerr(__FILE__, __LINE__);

while ($ip = @mysql_fetch_array($res)){

if (!sent_mail($ip["email"],$SITENAME,$SITEEMAIL,$subj,$all,false)) {
stdmsg("Ошибка данных", "Не могу отправить письмо на почту. <a href=\"$BASEURL/support.php\">Вернутся назад</a>");
stdfootchat();
die;
}

}


}
else
{

$res = sql_query("SELECT id FROM users WHERE class=".UC_SYSOP) or sqlerr(__FILE__, __LINE__);

while ($ip = @mysql_fetch_array($res)){
	
$now = sqlesc(get_date_time());
$msg = sqlesc($all);
$subject = sqlesc($subj);
sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES (0, ".sqlesc($ip["id"]).", $now, $msg, 0, $subject)")  or sqlerr(__FILE__,__LINE__);

}

}



header("Refresh: 5; url=$BASEURL/support.php?send=".($sendwho == 1 ? "1":"2")."");
stdmsg("Операция", "Отправляем данные. <a href=\"$BASEURL/support.php\">Вернутся назад</a>");
stdfootchat();
die;
}

?>

<script type="text/javascript" language="javascript">
$(document).ready(function () {
	$("select[name=subject]").change(function () {

	if ($(this).val() == "Пожертвование на сайте") {
		$("textarea[name=text]").empty();
		$("textarea[name=text]").append('Кошелёк: \nСумма: \nВремя пожертвования:');
	} else if ($(this).val() == "Проблемы на сайте") {
		$("textarea[name=text]").empty();
		$("textarea[name=text]").append('Страница  (укажите полный путь): \nСкриншот: \nМои действия (что я делал / сделал):');
	} else if ($(this).val() == "Вопросы о раздачах") {
		$("textarea[name=text]").empty();
		$("textarea[name=text]").append('Раздача (укажите полный путь): \nВопрос:');
	} else if ($(this).val() == "Правообладатель") {
		$("textarea[name=text]").empty();
		$("textarea[name=text]").append('Раздача (укажите полный путь): \nСкан документа: \nE-mail правообладателя: \nИная информация: \nТекст, в сопровождении удаляемой информации: ');
	} else if ($(this).val() == "Реклама на сайте") {
		$("textarea[name=text]").empty();
		$("textarea[name=text]").append('Реклама: \n[b][u]Ваши контакты:[/u][/b]\nE-mail: \nICQ: \nSkype: ');
	} else if ($(this).val() == "Владельцу движка Tesla TT v.Gold") {
		$("textarea[name=text]").empty();
		$("textarea[name=text]").append('----------->\nОфициальным владельцем движка Tesla TT v.Gold является 7Max7 [icq: 225454228] (при поддержке Imperator)\n----------->\n\nУкажите ваши намерения ниже, покупка копии, мода, идея для создания (подробнее вопрос): \n\nCвязь с вами: ');
	}
	 else {
		$("textarea[name=text]").empty();
	}
	});
});
</script>

<? 


echo "<form method=\"post\" id=\"o_O\" action=\"support.php\" name=\"comment\">
<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" style=\"border-collapse: collapse\">
<tr>
<td class=\"colhead\" colspan=\"2\" align=\"center\">Служба техподдержки (Форма связи с Администрацией)</td>
</tr>";

echo "<tr>
<td colspan=\"2\" class=\"a\" align=\"center\"><b>Внимание</b>: Письма (сообщения) отправляются мгновенно, если никто вам не ответил или прошло больше дня (три дня max время неактивности) напишите пожалуйста снова, здесь же.<br> Иначе, судя по теме сообщения (если флуд, спам, реклама ресурса - не обрабатывается), письмо будет выполнено по приоритетности внутреннего запроса. Письма с <u>Матами</u> и <u>другими видами оскорбления</u> (как личное так и публичное) - удаляются безвозвратно. Несоблюдение правил, <u>вы как отправитель и пользователь</u>, можете быть забаненным системой или администрацией сайта как по ip адрессу так и по email.<br><br><center>Спасибо за внимание, Система сайта ".$SITENAME."</center></td></tr>";

echo "<tr>
<td class=\"b\">Ваше имя: </td>
<td class=\"b\">
<input name=\"username\" type=\"text\" ".($CURUSER ? " value=\"".$CURUSER["username"]."\" disabled ":"")." size=\"40\"/>
</td>
</tr>";

echo "<tr>
<td class=\"b\">Тип сообщения: </td>
<td class=\"b\">
<select size=\"1\" name=\"subject\">
<option selected>Общие вопросы</option>
<option>Реклама на сайте</option>
<option>Проблемы на сайте</option>
<option>Вопросы о раздачах</option>
<option>Пожертвование на сайте</option>
<option>Правообладатель</option>
<option>Владельцу движка Tesla TT v.Gold</option>
</select>
</td>
</tr>";

echo "<tr>
<td colspan=\"2\" class=\"a\" align=\"center\">Ваше сообщение:</td></tr>";
	

echo "<tr>
<td colspan=\"2\" align=\"center\">";

echo textbbcode("comment", "text","",1);

echo "</td></tr>";


echo "<tr>
<td colspan=\"2\" align=\"center\">
<input type=\"submit\" class=\"btn\" value=\"Отправить сообщение\" />
<input type=\"reset\" class=\"btn\" value=\"Очистить форму сообщения\"/></td>
</tr>";

echo "</form></table>";



stdfootchat();
?>