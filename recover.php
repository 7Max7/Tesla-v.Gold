<?

require "include/bittorrent.php";
dbconn();


global $CURUSER,$maxlogin;

if (!empty($CURUSER)){
@header("Location: ../index.php");
die();
}

if ($maxlogin==1){
failedloginscheck();
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{

	
	
  $email = (isset($_POST["email"]) ? htmlentities($_POST["email"]):"");
  
  if (!empty($email)){
  
  if (!validemail($email)) // исправленно
    stderr($tracker_lang['error'], "Вы должны ввести email адрес");
    
  $res = sql_query("SELECT * FROM users WHERE email=" . sqlesc($email) . " LIMIT 1") or sqlerr(__FILE__, __LINE__);
  $arr = mysql_fetch_assoc($res) or stderr($tracker_lang['error'], "Email адрес не найден в базе данных.\n");


if ($arr["enabled"] == "no")
stderr($tracker_lang['error'],"Учетная запись с почтой $email - отключена! Востановление пароля невозможно.");


	$sec = mksecret();

  sql_query("UPDATE users SET editsecret=" . sqlesc($sec) . " WHERE id=" . $arr["id"]) or sqlerr(__FILE__, __LINE__);
  if (!mysql_affected_rows())
	  stderr($tracker_lang['error'], "Ошибка базы данных. Свяжитесь с администратором относительно этой ошибки.");

  $hash = md5($sec . $email . $arr["passhash"] . $sec);
$ip = getip();
  $body = <<<EOD
Вы, или кто-то другой, запросили новый пароль к аккаунту связаному с этим адресом ($email).

Запрос был послан человеком с IP адресом {$ip}.

Если это были не вы, проигнорируйте это письмо. Пожалуста не отвечайте.

Если вы подтверждаете этот запрос, перейдите по следующей ссылке:

$DEFAULTBASEURL/recover.php?id={$arr["id"]}&secret=$hash


После того как вы это сделаете, ваш пароль будет сброшен и новый пароль будет отправлен вам на E-Mail.

--
$SITENAME
EOD;

	@sent_mail($arr["email"],$SITENAME,$SITEEMAIL,"Подтверждение восстановления пароля на $SITENAME",$body)
		or stderr($tracker_lang['error'], "Невозможно отправить E-mail. Пожалуста сообщите администрации об ошибке.");
	stderr($tracker_lang['success'], "Подтверждающее письмо было отправлено.\n" .
		" Через несколько минут (обычно сразу) вам прийдет письмо с дальнейшими указаниями.");
		}
		
		$id_username=(isset($_POST["id_username"]) ? (int)$_POST["id_username"]:""); // id пользователя
		$username_id=(isset($_POST["username_id"]) ? htmlspecialchars($_POST["username_id"]):""); // ник пользователя
		
		if (!empty($id_username) || !empty($username_id)){
	//	echo "d";	
	///die($id_username.$username_id);
	  if (!empty($username_id)){
	  	$sql="username=".sqlesc($username_id)."";
	  	$vw="этому логину: $username_id";
	  } else {
	  $sql="id=".sqlesc($id_username)."";
	  $vw="этому id: $id_username";
	}
	  $res = sql_query("SELECT id,enabled, question FROM users WHERE $sql and enabled='yes' AND status = 'confirmed' LIMIT 1") or sqlerr(__FILE__, __LINE__);
      $row = mysql_fetch_array($res);

if (empty($row)) {
stderr($tracker_lang['error'],"Ничего не найденно по $vw. Возможно аккаут не активирован по почте или отключен.");
}

if ($row["enabled"] == "no") {
stderr($tracker_lang['error'],"Учетная запись - отключена! Востановление пароля невозможно.");
}

if (empty($row["question"])) {
stderr($tracker_lang['error'],"У этой учетной записи нет вопроса и ответа. Востановление пароля невозможно.");
}

 $question=$row["question"];
    if ($question){
    	
    /// против ботов
if ($_COOKIE["recover"]<>md5($_SERVER["HTTP_USER_AGENT"].date('H'))){
@header("Location: ../index.php");
die("Антибот система, включите сохранения куков и пройдите на страничку Восстановления пароля.");
}
    	
    logoutcookie();
	stdhead("Ввод ответа на секретный вопрос пользователя");

	echo "
		<form method=\"post\" action=\"recover.php\">
	<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">
	<tr><td class=\"colhead\" colspan=\"2\">Теперь пожалуйста ответьте на заданный вопрос ниже</td></tr>
	<tr><td align=\"center\"colspan=\"2\"><b title=\"Вопрос данного пользователя\">$question</b></td></tr>
	
	<tr><td class=\"a\" width=\"20%\" align=left><b>Введите ответ</b>: </td>
	<td class=\"a\"><input type=\"password\" size=\"40\" name=\"do_you_wanna\"></td></tr>
	
	<tr><td colspan=\"2\" align=\"center\">
	<input type=hidden name=userid value=\"".$row["id"]."\">
	<input type=\"submit\" value=\"Сгенерировать и показать мне мой новый пароль\"></td></tr>
	</form>
	</table><br>";
	stdfoot();
		}
		}
	$do_you_wanna=(isset($_POST["do_you_wanna"]) ? htmlspecialchars($_POST["do_you_wanna"]):""); // ник пользователя
		if (!empty($do_you_wanna))
		{
	
	 /// против ботов
if ($_COOKIE["recover"]<>md5($_SERVER["HTTP_USER_AGENT"].date('H'))){
@header("Location: ../index.php");
die("Антибот система, включите сохранения куков и пройдите на страничку Восстановления пароля.");
}
	$userid=htmlspecialchars((int)$_POST["userid"]); // id пользователя
			
	  $res_do = sql_query("SELECT id, shelter, rejoin, added, username, class,question FROM users WHERE enabled='yes' AND status = 'confirmed' and id=".sqlesc($userid)."") or sqlerr(__FILE__, __LINE__);
      $row_do = mysql_fetch_array($res_do);
      
 if (empty($row_do["id"])) {
stderr($tracker_lang['error'],"Нет данных о включенном (подтвержденном) пользователе. Востановление пароля невозможно.");
}

      if (empty($row_do["question"])) {
stderr($tracker_lang['error'],"У этой учетной записи нет вопроса и ответа. Востановление пароля невозможно.");
}
      $shelter=$row_do["shelter"];
      $questio_n=$row_do["question"];
       $rejoin=$row_do["rejoin"];
	  $row_secret=$row_do["added"];	
	  $update_new=md5($row_secret.$do_you_wanna.$row_secret);
	
	if ($rejoin==$update_new){
	
	/// генерируем и создаем новый пароль пользователю
	 $chpassword = generatePassword();
	  $sec = mksecret();
	$passhash = md5($sec.$chpassword.$sec);
	$updateset[] = "secret = " . sqlesc($sec); /// верно
	$updateset[] = "passhash = " . sqlesc($passhash);
	
	sql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = ".sqlesc($userid)."") or sqlerr(__FILE__,__LINE__);
	
	logincookie($userid, $passhash, $shelter,1); // автовход на сайт
	
	stdhead("Ваш новый пароль для входа");
	
///	stdhead("Ввод ответа на секретный вопрос пользователя");
	echo "<script type=\"text/javascript\">
		function highlight(field) {	field.focus();	field.select();	}
        </script>
        
	<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">
	<tr><td class=\"colhead\" colspan=\"2\">Ваши новые данные ниже</td></tr>
	<tr><td class=\"b\" align=\"center\"colspan=\"2\">
    <b>Логин</b>: <b>".get_user_class_color($row_do["class"], $row_do["username"])."</b><br>
	<b>Новый пароль</b>: $chpassword <br><br>
	<b>Этот же логин, но в форме ввода</b>: <input type=\"text\" size=\"20\" value=\"".$row_do["username"]."\" name=\"username\"><br>
	<b>Этот же пароль, но в форме ввода</b>: <input type=\"text\" size=\"20\" value=\"$chpassword\" name=\"password\">
	</td></tr>

	
	<tr><td colspan=\"2\" class=\"a\" align=\"center\"><i>Кликнув по любой ссылке выше - у вас произойдет автовход на трекер.</i>
    </td></tr>

	</table><br>";
	stdfoot();
	}
	else
	{
	  
	stdhead("Неверные данные о пароле проверка");
	
	

///	echo "	<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">	<tr><td class=\"colhead\" colspan=\"2\">Ошибочка</td></tr>	<tr><td align=\"center\"colspan=\"2\">Извините данные неверны, вернитесь обратно и повторите попытку.		</td></tr>	</table><br>";

echo "
	<form method=\"post\" action=\"recover.php\">
	<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">
	<tr><td class=\"colhead\" colspan=\"2\">Извините данные неверны, вернитесь повторите попытку снова</td></tr>
<tr><td align=\"center\"colspan=\"2\"><b title=\"Вопрос данного пользователя\">$questio_n</b></td></tr>
	
	<tr><td class=\"a\" width=\"20%\" align=left><b>Введите ответ</b>: </td>
	<td class=\"a\"><input type=\"password\" size=\"40\" name=\"do_you_wanna\"></td></tr>
	
	<tr><td colspan=\"2\" align=\"center\">
	<input type=hidden name=userid value=\"$userid\">
	<input type=\"submit\" value=\"Повторить ранее действия\"></td></tr>
	</form>
	</table><br>";
    $ip = getip();
    $added = sqlesc(get_date_time());
    
   
    
    
    $update[] = "attempts = attempts + 1";
    $update[] = "comment = CONCAT(".sqlesc($userid.",").",comment)";
    sql_query("UPDATE loginattempts SET " . implode(", ", $update) . " where ip=".sqlesc($ip)) or sqlerr(__FILE__, __LINE__);
    
    if (!mysql_affected_rows()){
     sql_query("INSERT INTO loginattempts (ip, added, attempts, comment) VALUES ('$ip',$added,'yes','$userid')") or sqlerr(__FILE__, __LINE__);
 }
 
	stdfoot();
	}
	}
		
	
}
elseif(!empty($_GET["secret"]) && !empty($_GET["id"]))
{

//	if (!preg_match(':^/(\d{1,10})/([\w]{32})/(.+)$:', $_SERVER["PATH_INFO"], $matches))
//	  httperr();

//	$id = 0 + $matches[1];
//	$md5 = $matches[2];

	$id = (int) $_GET["id"];
  $md5 = $_GET["secret"];

	if (!$id)
	  httperr("Нет id");

	$res = sql_query("SELECT username, email, passhash, editsecret FROM users WHERE id = ".sqlesc($id)."");
	$arr = mysql_fetch_array($res) or httperr();

  $email = $arr["email"];

	$sec = hash_pad($arr["editsecret"]);
	if (preg_match('/^ *$/s', $sec))
	  httperr("Ошибка в $sec");
	if ($md5 != md5($sec . $email . $arr["passhash"] . $sec))
		stderr($tracker_lang['error'], "Данные неверны, проверьте написание ссылки.");

	// generate new password;
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

  $newpassword = "";
  for ($i = 0; $i < 10; $i++)
    $newpassword .= $chars[mt_rand(0, strlen($chars) - 1)];

 	$sec = mksecret();

  $newpasshash = md5($sec . $newpassword . $sec);

	sql_query("UPDATE users SET secret=" . sqlesc($sec) . ", editsecret='', passhash=" . sqlesc($newpasshash) . " WHERE id=".sqlesc($id)." AND editsecret=" . sqlesc($arr["editsecret"]));

	if (!mysql_affected_rows())
		stderr($tracker_lang['error'], "Невозможно обновить данные пользователя. Пожалуста свяжитесь с администратором относительно этой ошибки.");

	$userid = $id;
	$chpassword = $newpassword;

  $body = <<<EOD
По вашему запросу на восстановление пароля, мы сгенерировали вам новый пароль.

Вот ваши новые данные для этого аккаунта:

    Пользователь: {$arr["username"]}
    Пароль:       $newpassword

Вы можете войти на сайт тут: $DEFAULTBASEURL/login.php

--
$SITENAME
EOD;

  @sent_mail($email,$SITENAME,$SITEEMAIL,"Данные аккаунта на $SITENAME",$body)
    or stderr($tracker_lang['error'], "Невозможно отправить E-mail. Пожалуста сообщите администрации об ошибке.");
  stderr($tracker_lang['success'], "Новые данные по аккаунту отправлены на E-Mail <b>". htmlspecialchars($email) ."</b>.\n" ."Через несколько минут (обычно сразу) вы получите ваши новые данные.");
}
else
{
	 	
    $recnow=md5($_SERVER["HTTP_USER_AGENT"].date('H'));
 	@setcookie("recover", $recnow, "0x7fffffff", "/");
 	/// против ботов
 	
 	
 	stdhead("Восстановление пароля");
 	
 	$uid=(isset($_COOKIE["uid"]) ? (int) $_COOKIE["uid"]:"");
	?>
	<style type="text/css">
input.mail{background: url(pic/contact/email.gif) no-repeat;    background-color: #fff;    background-position: 0 50%;    color: #000;    padding-left: 18px;}
.rowhead2 {  font-weight: bold;  text-align: right;}
</style>
	<form method="post" action="recover.php">
	<table width="100%" border="1" cellspacing="0" cellpadding="5">
	<tr><td class="colhead" colspan="2">Восстановление логина или пароля</td></tr>
	<tr><td align="center"colspan="2">Используйте форму ниже для востановления пароля и ваши данные будут отправлены вам на почту. <br>Вы должны будете подтвердить запрос.</td></tr>
	<tr><td class="a">Зарегистрированый email</td>
	<td class="a"><input type="text" size="40" name="email"class="mail"></td></tr>
	<tr><td colspan="2" align="center">
	<input type="submit" value="Восстановить"></td></tr>
	</form>

	</table><br>
	
	<form method="post" action="recover.php">
	<table width="100%" border="1" cellspacing="0" cellpadding="5">
	<tr><td class="colhead" colspan="2">Восстановление пароля по секретному вопросу пользователя</td></tr>
	<tr><td align="center"colspan="2">Используйте форму ниже для востановления пароля, если данные будут верны вы сразу получите новый сгенерированный пароль.</td></tr>
	<tr><td class="a">Введите ник пользователя</td>
	<td class="a"><input type="text" size="20" name="username_id"> <i>первичный вариант</i></td></tr>
	
	<tr><td class="a">Или введите id пользователя</td>
	<td class="a"><input type="text" size="10" name="id_username" value="<?=$uid;?>"> <i>вторичный вариант</i></td></tr>
	
	<tr><td colspan="2" align="center">
	<input type="submit" value="Восстановить"></td></tr>
	</form>

	</table><br>
	
	<?
	stdfoot();
}

?>