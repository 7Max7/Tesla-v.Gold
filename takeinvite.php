<?
require "include/bittorrent.php";
dbconn();

if ($CURUSER) {

if (!headers_sent()){
@header("Location: $DEFAULTBASEURL/");
} else
die("Перенаправление...<script>setTimeout('document.location.href=\"$DEFAULTBASEURL\"', 10);</script>");
//stderr($tracker_lang['error'], sprintf($tracker_lang['signup_already_registered'], $SITENAME));
}


function bark($msg) {
	global $tracker_lang,$use_ipbans;
	stdheadchat();
	stdmsg($tracker_lang['error'], $msg, 'error');
	stdfootchat();
	exit;
}

$wantusername=(isset($_POST["wantusername"]) ? htmlspecialchars($_POST["wantusername"]):"");
$passagain=(isset($_POST["passagain"]) ? htmlspecialchars($_POST["passagain"]):"");
$wantpassword=(isset($_POST["wantpassword"]) ? htmlspecialchars($_POST["wantpassword"]):"");
$signup=((isset($_GET["signup"]) && $_GET["signup"]=="yes") ? "yes":"");
$secret=(isset($_POST["secret"]) ? htmlentities($_POST["secret"]):"");


if ($signup=="yes"){

if (empty($wantusername) || empty($wantpassword))
	bark("Поля - Пользователь и Пароль обязательны для заполнения.<br>
	<b>Не вписали</b>: 
	".(empty($wantusername)?"Пользователь ":"")."
	".(empty($wantpassword)?"Пароль ":"")." форму.");


if (strlen($wantusername) > 12)
	bark("Извините, имя пользователя слишком длинное (максимум 12 символов)");

if ($wantpassword <> $passagain)
	bark("Пароли не совпадают! Похоже вы ошиблись. Попробуйте еще.");

if (strlen($wantpassword) < 7)
	bark("Извините, пароль слишком короткий (минимум 7 символов)");

if (strlen($wantpassword) > 40)
	bark("Извините, пароль слишком длинный (максимум 40 символов)");

if ($wantpassword == $wantusername)
	bark("Извините, пароль не может быть такой-же как имя пользователя.");

if (!validusername($wantusername))
	bark("Неверное имя пользователя.");

$psecret_in = (isset($_POST["secret"]) ? htmlentities($_POST["secret"]):"");

$res = sql_query("SELECT id,inviter,email FROM invites WHERE confirmd5 = ".sqlesc($psecret_in)) or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_assoc($res);


if (!validemail($row["email"]))
	bark("Это не похоже на реальный email адрес.");

if (!$row["id"])
bark("Неверное проверочный код с почты ($psecret_in). Перепроверьте данные в письме.");

$email = $row["email"];
$inviter = $row["inviter"];

$secret = mksecret();
$wantpasshash = md5($secret . $wantpassword . $secret);
$editsecret = mksecret();
$status = 'confirmed';
$ip = getip();
$get_gate=get_date_time();

$ret = sql_query("INSERT INTO users (username, ip, passhash, secret, editsecret, email, status, added, invitedby, last_checked) VALUES (" .implode(",", array_map("sqlesc", array($wantusername,$ip, $wantpasshash, $secret, $editsecret, $email, $status))).", '".$get_gate."', '$inviter', '". get_date_time() ."')");

if (!$ret) {
	if (mysql_errno() == 1062)
		bark("Пользователь $wantusername уже зарегистрирован!");
}

$id = mysql_insert_id();


sql_query("DELETE FROM invites WHERE confirmd5 = ".sqlesc($psecret_in))or sqlerr(__FILE__,__LINE__);



write_log("Зарегистрирован новый пользователь $wantusername","000000","tracker");

logincookie($id, $wantpasshash, "ip");



@header("Refresh: 0; url=userdetails.php?id=$id");

die;
}




stdheadchat("Создание новой учетной записи (по приглашению)");


$psecret = (isset($_GET["psecret"]) ? htmlentities($_GET["psecret"]):"");


$res = sql_query("SELECT id,inviter,email FROM invites WHERE confirmd5 = ".sqlesc($psecret)) or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_assoc($res);

if (!empty($row["id"])){

echo "<style type=\"text/css\">
<!--
input.mail
{
    background: url(pic/contact/email.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
}

.rowhead2 {
  font-weight: bold;
  text-align: right;
 
}
input.pass
{
    background: url(pic/contact/pass.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
}
input.login
{
    background: url(pic/contact/login.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
}
input.brt
{
    background: url(pic/brt.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
}
input.skype
{
    background: url(pic/contact/skype_13.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
}
input.icq
{
    background: url(pic/contact/icq_13.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
}
div#ajaxerror { 
    background: #FFDDDD url(/pic/error.gif) no-repeat 5px 50%; 
    padding:5px 5px 5px 24px; 
    text-align:left; 
    font-family:Verdana, Arial, Helvetica, sans-serif; 
    color:#333; 
    font-size:11px; 
    }
div#ajaxsuccess { 
    background: #E7FFCE url(/pic/ok.gif) no-repeat 5px 50%; 
    padding:5px 5px 5px 24px; 
    text-align:left; 
    font-family:Verdana, Arial, Helvetica, sans-serif; 
    color:#333; 
    font-size:11px; 
    }
input.odnoklasniki { background: url(pic/contact/odnoklasniki_13.gif) no-repeat;  background-color: #fff;  background-position: 0 50%;   color: #000;    padding-left: 18px;}
input.vkontakte { background: url(pic/contact/vkontakte_13.gif) no-repeat;    background-color: #fff;    background-position: 0 50%;    color: #000;    padding-left: 18px;}

-->
</style>";

?>
<script language="JavaScript" src="js/ajax.js" type="text/javascript"></script> 
<form method="post" action="takeinvite.php?signup=yes"> 
<table border="1" cellspacing=0 cellpadding="10"> 
<tr valign=top><td align="right" class="heading">Желаемый логин: </td><td align=left><input type="text" size="60" class="login" name="wantusername" id="wantusername" onblur="signup_check('username'); return false;"/><div id="check_username"></div></td></tr> 
<tr valign=top><td align="right" class="heading">Желаете пароль: </td><td align=left><input type="password" size="60" class="pass" name="wantpassword" id="wantpassword"/></td></tr> 
<tr valign=top><td align="right" class="heading"><?=$tracker_lang['signup_password_again'];?></td><td align=left><input type="password" class="pass" size="60" name="passagain" id="passagain" onblur="signup_check('password'); return false;"/><div id="check_password"></div></td></tr> 

<tr><td class="a" colspan="2" align="center">
<input type="hidden" name="secret" value="<?=$psecret;?>"/>
<input type="submit" value="Создать учетную запись" style='height: 25px;width: 200px'/></td></tr>
</table>
</form>

<?
} else {
///http://localhost/takeinvite.php?psecret=6ca7ef116c252fdf620dcecsdfsdfbadce
stdmsg("Ошибка", "Извините, вы указали неправильное приглашение.");
stdfootchat();
}

print("<div id='loading-layer'></div>") 


?>