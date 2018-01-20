<?

require_once("include/bittorrent.php");
dbconn();

$ip = getip();

if ($deny_signup && !$allow_invite_signup)
	stderr($tracker_lang['error'], "Извините, но регистрация отключена администрацией.");

if ($CURUSER) {

if (!headers_sent()){
@header("Location: $DEFAULTBASEURL/");
} else
die("Перенаправление...<script>setTimeout('document.location.href=\"$DEFAULTBASEURL\"', 10);</script>");
//stderr($tracker_lang['error'], sprintf($tracker_lang['signup_already_registered'], $SITENAME));
}

echo "<style type=\"text/css\">
<!--
input.mail {
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
input.pass {
    background: url(pic/contact/pass.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
}
input.login {
    background: url(pic/contact/login.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
}
input.brt {
    background: url(pic/brt.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
}
input.skype {
    background: url(pic/contact/skype_13.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
}
input.icq {
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
	
list($users) = mysql_fetch_array(sql_query("SELECT COUNT(id) FROM users"));
if ($users >= $maxusers)
	stderr($tracker_lang['error'], sprintf($tracker_lang['signup_users_limit'], number_format($maxusers)));


if (isset($_POST["agree"]) && $_POST["agree"] != "yes") {
stdhead("Правила трекера");
?>
<br/>

<fieldset class="fieldset">
<legend><b>Правила трекера</b></legend>
<form method="post" action="signup.php">
<table cellpadding="4" cellspacing="0" border="0" style="width:100%" class="table">
<tr>
<td class="a">Для продолжения регистрации, Вы должны согласиться со следующими правилами:</td></tr>
<tr>
  <td class="b" style="font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif">
<div class="page" style="border-right: thin inset; padding-right: 6px; border-top: thin inset; padding-left: 6px; padding-bottom: 6px; overflow: auto; border-left: thin inset; padding-top: 1px; border-bottom: thin inset; height: 300px">


<p>Регистрация на трекере абсолютно бесплатна! Настоятельно рекомендуем ознакомиться с правилами нашего проекта.
Если вы согласны со всеми условиями, поставьте галочку рядом с 'Я согласен' и нажмите 'Регистрация'.
Если вы передумали регистрироваться, нажмите <a href="<?=$DEFAULTBASEURL;?>">здесь</a>, чтобы вернуться на главную страницу.</p>

<p>Хотя модераторы и администраторы, обслуживающие <?=$SITENAME;?>, стараются удалять все оскорбительные и некорректные
сообщения из трекера, все равно все сообщения просмотреть невозможно. Сообщения отражают точку зрения только
автора, но не администрации трекера, соответственно только автор несет ответственность за содержание сообщения.</p>
<p>Соглашаясь с нашими правилами, вы обязуетесь выполнять требования трекера в целом, а также требования законодательства РФ.</p>
<p>Администрация трекера оставляет за собой право удалять, изменять, переносить или закрывать любую тему или сообщение по своему усмотрению.</p>

<p>Да, если мы заметим, что вы обманываете трекер (рейтинг) или пытаетесь взломать его своими методами, мы (администрация) в праве забанить мыло (точно) и ip адрес (в крайних случаях).</p>
</div>
</td></tr>
<tr><td class="a">
<div>

<input type="checkbox" name="rulesverify" value="yes"> <?=$tracker_lang['signup_i_have_read_rules'];?><br />
<input type="checkbox" name="faqverify" value="yes"> <?=$tracker_lang['signup_i_will_read_faq'];?><br />
<input type="checkbox" name="ageverify" value="yes"> <?=$tracker_lang['signup_i_am_13_years_old_or_more'];?><br />


<label>
<input class="tablea" type="checkbox" name="agree" value="yes">
<input type="hidden" name="do" value="register">
<strong>Я буду придерживатся установленным правилам, посещая <?=$SITENAME;?>.</strong>
</label>
</div>
</td></tr>
</table>
</fieldset>
<center>
<input class="tableinborder" type="submit" value="Продолжить Регистрацию"/>
</center>
</form>
<?
stdfoot();
die;
}

stdhead($tracker_lang['signup_signup']);

if (isset($_POST["agree"]) && $_POST["agree"] == "yes"){
// make sure user agrees to everything...
if (empty($_POST["rulesverify"]) || empty($_POST["faqverify"]) || empty($_POST["ageverify"]))
stderr($tracker_lang['error'], "Извините, вы не подходите для того что-бы стать пользователем этого сайта. <b><a href=signup.php>Вернутся Обратно</a></b><br><b>Вы не согласились на</b>: 
".(empty($_POST["rulesverify"]) ? "Я прочитал(а) правила.":"")."
".(empty($_POST["faqverify"]) ? "Я прочитаю ЧаВо, прежде чем задавать вопросы.":"")."
".(empty($_POST["ageverify"]) ? "Мне 13 лет или больше.":"")."
");

}

$countries = "<option value=\"0\">".$tracker_lang['signup_not_selected']."</option>\n";

$ct_r = sql_query("SELECT id, name FROM countries ORDER BY name") or sqlerr(__FILE__, __LINE__);
while ($ct_a = mysql_fetch_array($ct_r))
$countries .= "<option value=\"".$ct_a["id"]."\" " . (!validip_pmr($ip) && $ct_a["id"]=="103" ? " selected" : "") .">".$ct_a["name"]."</option>\n";


echo "<script language=\"JavaScript\" src=\"js/ajax.js\" type=\"text/javascript\"></script>";
echo "<form method=\"post\" action=\"takesignup.php\">";
echo "<table border=\"0\" cellspacing=0 cellpadding=\"10\">";

if ($deny_signup && $allow_invite_signup)
echo "<tr><td align=\"center\" colspan=\"2\" class=\"b\"><fieldset class=\"fieldset\"><legend><b>Внимание</b></legend><b><font color=\"red\">Регистрация без приглашения невозможна!</font></b></fieldset></td></tr>";

echo "<tr><td align=\"center\" colspan=\"2\" class=\"b\"><fieldset class=\"fieldset\"><legend><b>Внимание</b></legend><b>Для правильной регистрации активизируйте куки!</b><br></fieldset></td></tr>";

///<br/><b><font color=\"red\">Если на данном сайте есть проблеммы с регистрацией, а именно с куками, - то пожалуйста очистите все куки данного сайта и попробуйте снова зарегистрироваться. </font></b>


echo "<tr><td align=\"right\" class=\"b\">".$tracker_lang['signup_username']."</td><td class=\"a\" align=\"left\"><input type=\"text\" size=\"60\" class=\"login\" name=\"wantusername\" id=\"wantusername\" onblur=\"signup_check('username'); return false;\"/><div id=\"check_username\"></div></td></tr>";

echo "<tr><td align=\"right\" class=\"b\">".$tracker_lang['signup_password']."</td><td class=\"a\" align=\"left\">
<input type=\"password\" size=\"60\" class=\"pass\" name=\"wantpassword\" id=\"wantpassword\"/>
</td></tr>";

echo "<tr><td align=\"right\" class=\"b\">".$tracker_lang['signup_password_again']."</td><td class=\"a\" align=\"left\"><input type=\"password\" class=\"pass\" size=\"60\" name=\"passagain\" id=\"passagain\" onblur=\"signup_check('password'); return false;\"/><div id=\"check_password\"></div></td></tr>";

echo "<tr><td align=\"right\" class=\"b\">Почта (email)</td><td class=\"a\" align=\"left\">
<input type=\"text\" size=\"60\" name=\"email\" class=\"mail\" id=\"email\" onblur=\"signup_check('email'); return false;\"/><div id=\"check_email\"></div> 
".(!empty($email_rebans) ? "<br>
<fieldset class=\"fieldset\"><legend><b>У нас допустимы почтовые домена : </b></legend>
<li><b>... @rambler.ru</b>
<li><b>... @gmail.com</b>
<li><b>... @live.ru</b>
<li><b>... @mail.ru</b>
<li><b>... @idknet.com</b></span>
</fieldset<br><i>Другие же будут блокироватся.</i>":"")."
</td></tr>";

echo "<tr><td align=\"right\" class=\"b\">".$tracker_lang['signup_gender']."</td><td class=\"a\" align=left><label><input type=radio name=gender value=1>Парень<img src=pic/male.gif></label><label><input type=radio name=gender value=2>Девушка<img src=pic/female.gif></label></td></tr>";

$year = "<select name=year><option value=\"0000\">".$tracker_lang['my_year']."</option>\n";
$i = "1950"; /// минимальный возраст
while ($i <= (date('Y',time())-13)) {
	$year .= "<option value=" .$i. ">".$i."</option>\n";
	$i++;
}
$year .= "</select>\n";
$birthmonths = array(
"01" => $tracker_lang['my_months_january'],
"02" => $tracker_lang['my_months_february'],
"03" => $tracker_lang['my_months_march'],
"04" => $tracker_lang['my_months_april'],
"05" => $tracker_lang['my_months_may'],
"06" => $tracker_lang['my_months_june'],
"07" => $tracker_lang['my_months_jule'],
"08" => $tracker_lang['my_months_august'],
"09" => $tracker_lang['my_months_september'],
"10" => $tracker_lang['my_months_october'],
"11" => $tracker_lang['my_months_november'],
"12" => $tracker_lang['my_months_december'],
);
$month = "<select name=\"month\"><option value=\"00\">".$tracker_lang['my_month']."</option>\n";
foreach ($birthmonths as $month_no => $show_month) {
	$month .= "<option value=$month_no ".(date('m')==$month_no ? "selected":"").">$show_month</option>\n";
}

$month .= "</select>\n";
$day = "<select name=day><option value=\"00\">".$tracker_lang['my_day']."</option>\n";
$i = 1;
while ($i <= 31) {
	if ($i < 10) {
		$day .= "<option value=0".$i." ".(date('j')==$i ? "selected":"").">0".$i."</option>\n";
	} else {
		$day .= "<option value=".$i." ".(date('j')==$i ? "selected":"").">".$i."</option>\n";
	}
	$i++;
}
$day .="</select> <i>обязательно</i>\n";

echo "<tr><td align=\"right\" class=\"b\">".$tracker_lang['my_birthdate']."</td><td class=\"a\" align=left>".$year.$month.$day."</td></tr>";

echo "<tr><td align=\"right\" class=\"b\">".$tracker_lang['my_country']."</td><td class=\"a\" align=left><select name=country>\n$countries\n</select> <i>обязательно</i></td></tr>";

echo "<tr><td align=\"right\" class=\"b\">Защита от кражи данных (куки)</td><td class=\"a\" align=left><label><input type=\"radio\" name=\"tesla_guard\" value=\"yes\">Использовать по браузеру </label><label><input type=\"radio\" name=\"tesla_guard\" checked value=\"no\">Использовать по ip адресу </label><br /><br /><font class=\"small_text\"><b>Подсказка</b>: Если у вас Динамический ip адрес можете использовать защиту по браузеру, иначе для статических адресов - по ip. Если куки неверны - вас выбьет с сайта, после чего, вы должны заново войти через ввод ника и пароля. Подробнее <a rel=\"nofollow\" href=\"redir.php?url=http://ru.wikipedia.org/wiki/HTTP_cookie#.D0.92.D0.B7.D0.BB.D0.BE.D0.BC_.D0.BA.D1.83.D0.BA.D0.B8\">о краже куков</a></font></td></tr>";


/*
tr($tracker_lang['signup_contact'], "<table cellspacing=\"3\" cellPadding=\"0\" width=\"100%\" border=\"0\">
          <tr>
        <td align=\"center\" class=\"a\" style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\" colspan=2>".$tracker_lang['my_contact_descr'].":</td>
      </tr>
      <tr>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        ".$tracker_lang['my_contact_icq']."<br />
        
        <input maxLength=\"10\" size=\"40\" class=\"icq\" name=\"icq\"></td>
        
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        ".$tracker_lang['my_contact_skype']."<br />
     
        <input maxLength=\"20\" size=\"40\" class=\"skype\" name=\"skype\"></td>        

      </tr>
      
      <tr>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        Вконтакте: <br />
        
<input type=\"text\" maxLength=\"300\" class=\"vkontakte\" name=\"vkontakte\" size=\"40\"/>
</td>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        Одноклассники:<br />
  
  <input type=\"text\" maxLength=\"300\" class=\"odnoklasniki\" name=\"odnoklasniki\" size=\"40\" />
  </td>
      </tr>
    </table>",1);
    */
    
    
 //   $site = @parse_url($DEFAULTBASEURL, PHP_URL_HOST);
//tr($tracker_lang['my_website'], "<input type=\"text\" name=\"website\" size=\"40\" value=\"$site\" />", 1);

/*if ($use_captcha) {
	include_once("include/captcha.php");
	$hash = create_captcha();
	tr("Код подтверждения", "<input type=\"text\" name=\"imagestring\" size=\"20\" value=\"\" /><p>Пожалуйста, введите текст изображенный на картинке внизу.<br />Этот процесс предотвращает автоматическую регистрацию.</p><img src=\"captcha.php?imagehash=$hash\" alt=\"Captcha\" /><br /><font color=\"red\">Код чувствителен к регистру</font><input type=\"hidden\" name=\"imagehash\" value=\"$hash\" />", 1);
}*/



if ($allow_invite_signup) {
echo "<tr><td align=\"right\" class=\"b\">Приглашение</td><td align=left class=\"a\"><input type=\"text\" size=\"60\" class=\"invite\" name=\"invite\" maxlength=\"32\" size=\"32\" id=\"invite\" onblur=\"signup_check('invite'); return false;\"/>".($deny_signup && $allow_invite_signup ? " <i>обязательно</i> ":"")."<div id=\"check_invite\"></div></td></tr> ";
}

echo "<tr><td align=\"right\" class=\"b\">Секретный вопрос</td><td class=\"a\" align=left><input title=\"Введите ваш секретный вопрос\" type=\"text\" name=\"ques\" size=70 /> <i>до 255 символов</i><br><i>введите свой личный секретный вопрос</i><br><input  title=\"Введите ваш ответ на секретный вопрос\"  type=\"text\" name=\"answ\" size=70 /> <i>до 255 символов</i><br><i>ответ на секретный вопрос, <b>знать должны только вы</b> иначе мы не гарантируем сохранность ваших данных (ответ хешируется, т.е. раскодировать его нереально)</i></td></tr>";

echo "<tr><td class=\"a\" colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"".$tracker_lang['signup_signup']."\" style='height: 25px;width: 200px'></td></tr>";

echo "</table></form><br>";


echo "<div id='loading-layer'></div>";

stdfoot();

?>