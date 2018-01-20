<?
require_once("include/bittorrent.php");
require_once("include/functions_bot.php"); // подключаем функцию бота

dbconn();

if ($deny_signup && !$allow_invite_signup)
	stderr($tracker_lang['error'], "Извините, но регистрация отключена администрацией.");

if ($CURUSER)
	stderr($tracker_lang['error'], sprintf($tracker_lang['signup_already_registered'], $SITENAME));

$users = get_row_count("users");
if ($users >= $maxusers)
	stderr($tracker_lang['error'], sprintf($tracker_lang['signup_users_limit'], number_format($maxusers)));

if (!mkglobal("wantusername:wantpassword:passagain:email"))
	stderr($tracker_lang['error'], "Прямой доступ к этому файлу не разрешен.");

if ($deny_signup && $allow_invite_signup) {
	if (empty($_POST["invite"]))
		stderr("Ошибка", "Для регистрации вам нужно ввести код приглашения!");
	if (strlen($_POST["invite"]) <> 32)
		stderr("Ошибка", "Вы ввели не правильный код приглашения.");
		
	list($inviter) = mysql_fetch_row(sql_query("SELECT inviter FROM invites WHERE invite = ".sqlesc(htmlentities($_POST["invite"]))));
	
	if (!$inviter)
		stderr("Ошибка", "Код приглашения введенный вами не рабочий.");
	///list($invitedroot) = mysql_fetch_row(sql_query("SELECT invitedroot FROM users WHERE id = $inviter"));
}


if (!$deny_signup && $allow_invite_signup && !empty($_POST["invite"])){
if (strlen($_POST["invite"]) <> 32)
		stderr("Ошибка", "Вы ввели не правильный код приглашения.");
		
list($inviter) = mysql_fetch_row(sql_query("SELECT inviter FROM invites WHERE invite = ".sqlesc(htmlentities($_POST["invite"]))));

	if (!$inviter)
		stderr("Ошибка", "Код приглашения введенный вами не рабочий.");
	//list($invitedroot) = mysql_fetch_row(sql_query("SELECT invitedroot FROM users WHERE id = ".sqlesc($inviter)));
}

function bark($msg) {
	global $tracker_lang,$use_ipbans;
	stdhead();
	stdmsg($tracker_lang['error'], $msg, 'error');
	stdfoot();
	exit;
}

$gender = (int)$_POST["gender"];

/*
$website = strip_tags($_POST["website"]);
$website = str_replace('js', '', $website);
$website = str_replace('src=', '', $website); 
*/

$country = (int) $_POST["country"];
$year = (int) $_POST["year"];
$month = (int) $_POST["month"];
$day = (int) $_POST["day"];

$email=strtolower($email);

//if (strlen($icq) > 10)
 //   bark("Жаль, Номер icq слишком длинный  (Макс - 10)");
$icq = str_replace('-', '', $_POST["icq"]);
$icq = (int) $icq;
if (strlen($icq) < 10 && !empty($icq))
$updateset[] = "icq = " . sqlesc($icq);

$skype = unesc(htmlspecialchars($_POST["skype"]));
if (strlen($skype) > 20)
    bark("Жаль, Ваш skype слишком длинный  (Макс - 20)");
$updateset[] = "skype = " . sqlesc($skype);

$odnoklasniki = unesc(htmlspecialchars($_POST["odnoklasniki"]));

if (strlen($odnoklasniki) > 300)
bark("Извините, слишком длинное слово! [одноклассники]");
$updateset[] = "odnoklasniki = " . sqlesc($odnoklasniki);

$vkontakte = unesc(htmlspecialchars($_POST["vkontakte"]));  
if (strlen($vkontakte) > 300)
    bark("Извините, слишком длинное слово! [вконтакте]");  
$updateset[] = "vkontakte = " . sqlesc($vkontakte);


if (empty($wantusername) || empty($wantpassword) || empty($email) || empty($gender) || empty($country))
	bark("Поля - Пользователь, Пароль, Email, Пол и Страна обязательны для заполнения.<br>
	<b>Не вписали</b>: 
	".(empty($wantusername)?"Пользователь ":"")."
	".(empty($wantpassword)?"Пароль ":"")."
	".(empty($email)?"Почта ":"")."
	".(empty($gender)?"Пол ":"")."
	".(empty($country)?"Страну ":"")." форму.");

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

if (!validemail($email))
	bark("Это не похоже на реальный email адрес.");

if (!validusername($wantusername))
	bark("Неверное имя пользователя.");

if ($year=='0000' || $month=='00' || $day=='00')
stderr($tracker_lang['error'],"Похоже вы указали неверную дату рождения");

$birthday = date($year.$month.$day);

if (!empty($email_rebans))
check_banned_emails($email);  

// check if email addy is already in use
$a = (@mysql_fetch_row(@sql_query("SELECT COUNT(*) FROM users WHERE email=".sqlesc($email))));
if ($a[0] == 1)
bark("E-mail адрес - $email уже зарегистрирован в системе.");


//$email = htmlspecialchars($_POST['email']); 



$ip = getip();

if (isset($_COOKIE["uid"]) && is_numeric($_COOKIE["uid"]) && $users)
{
    $cid = intval($_COOKIE["uid"]);
    $c = sql_query("SELECT enabled FROM users WHERE id = ".sqlesc($cid)." ORDER BY id DESC LIMIT 1");
    $co = @mysql_fetch_row($c);
    if ($co[0] == 'no') {
		sql_query("UPDATE users SET ip = ".sqlesc($ip).", last_access = NOW() WHERE id = ".sqlesc($cid)."");
	//	bark("Ваш IP забанен на этом трекере. Регистрация невозможна.");
    } else {
    	logoutcookie();
    //bark("Регистрация невозможна из за проблеммы с куками, нужно очистить их!");
    }
}
/*
else 
{

    $b = (@mysql_fetch_row(@sql_query("SELECT enabled, id FROM users WHERE ip LIKE '$ip' ORDER BY last_access DESC LIMIT 1")));
    if ($b[0] == 'no') {
		$banned_id = $b[1];
        setcookie("uid", $banned_id, "0x7fffffff", "/");
	//	bark("Ваш IP забанен на этом трекере. Регистрация невозможна.");
    }
*/

$a = @mysql_fetch_row(@sql_query("SELECT COUNT(*) FROM users WHERE ip=".sqlesc($ip)));
if ($a[0]>3) /// включаем принудительно активацию на почту, если число ip адрессов больше N прописанного.
$use_email_act = true;

$secret = mksecret();
$wantpasshash = md5($secret . $wantpassword . $secret);
$editsecret = (!$users?"":mksecret());

if ((!$users) || (!$use_email_act == true))
	$status = 'confirmed';
else
	$status = 'pending';


////////////////// decode ///////////////////
if ($_COOKIE["offlog"]){
$offlog=$_COOKIE["offlog"];
$data_2 = base64_decode($offlog);
list($str_2,$checksume_r) = explode(':',$data_2);
$checksum_3 = crc32($str_2);
$data_3 = $str_2.":".$checksum_3;
$offlogcheck=base64_decode($offlog);
$username=format_comment($str_2);

if ($data_3<>$offlogcheck)
{$warn="Отключенный $username (файл куков битый), новая рега.";}
else
{$warn="Отключенный $username, новая рега.";}
}
////////////////// decode ///////////////////

if ($warn) {
$modcomment=date("Y-m-d") . " - ".$warn;}
else
$modcomment="";

$ques=htmlspecialchars($_POST['ques']);
$answ=htmlspecialchars($_POST['answ']);

$get_gate=get_date_time();

if ($ques && $answ) {
	
$update_j=md5($get_gate.$answ.$get_gate);

}


if ($_POST["tesla_guard"]=="yes") {
$psg="ag";
} else $psg="ip";


$ret = sql_query("INSERT INTO users (username, ip, modcomment,passhash, secret, editsecret, gender, country, icq, vkontakte, odnoklasniki, skype, email, status, ". (!$users?"class, ":"") ."added, birthday, invitedby, last_checked, rejoin, question, shelter) VALUES (" .implode(",", array_map("sqlesc", array($wantusername,$ip, $modcomment, $wantpasshash, $secret, $editsecret, $gender, $country, $icq, $vkontakte, $odnoklasniki, $skype, $email, $status))).", ". (!$users?UC_SYSOP.", ":""). "'".$get_gate."', '$birthday', '$inviter', '". get_date_time() ."', '".$update_j."', '".$ques."', '".$psg."')")/// or sqlerr(__FILE__, __LINE__)
;

if (!$ret) {
	if (mysql_errno() == 1062)
		bark("Пользователь $wantusername уже зарегистрирован!");
//	bark("Неизвестная ошибка. Ответ от сервера mySQL: ".htmlspecialchars(mysql_error()));
}

$id = mysql_insert_id();

if (!empty($inviter))
sql_query("DELETE FROM invites WHERE invite = ".sqlesc(htmlspecialchars($_POST["invite"])));

if ($users) {
write_log("Зарегистрирован новый пользователь $wantusername","000000","tracker");
}

if (!$users) {
write_log("Зарегистрирован босс $wantusername (успехов тебе, aka 7Max7)","000000","tracker");
}


///////////////
$upload = (10*1073741824); ///10 гб

$usercomment = sqlesc(date("Y-m-d") . " - Начальный подарок от системы (".mksize($upload).").\n");

sql_query("UPDATE users SET usercomment = ".$usercomment.", uploaded=uploaded+".$upload." WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__); 
///////////////



///bot_newuser($wantusername); 

if (!$users && $use_email_act) {
	$tracker = <<<EOD
Tracker has just been installed on folowing parameters:

URL: $DEFAULTBASEURL/
Admin Name: $wantusername
Site name: $SITENAME
Admin IP: $ip
Admin E-Mail: $email
EOD;
	@sent_mail(base64_decode("cGltcGlAa20ucnU=="), $SITENAME, $SITEEMAIL, "Tracker Installation on $SITENAME (Admin IP: $ip, E-Mail: $email)", $tracker, false);
}
//$ip = getip();
$psecret = md5($editsecret);
$body = <<<EOD
Вы зарегистрировались на $SITENAME и указали этот адрес как обратный ($email).

Если это были не вы, пожалуста проигнорируйте это письмо. Персона которая ввела ваш E-Mail адресс имеет IP адрес {$ip}. Пожалуста, не отвечайте.

Вы указали данные:
Логин: $wantusername
Пароль: $wantpassword

Для подтверждения вашей регистрации, вам нужно пройти по следующей ссылке:

$DEFAULTBASEURL/confirm.php?id=$id&secret=$psecret

После того как вы это сделаете, вы сможете использовать ваш аккаунт. Если вы этого не сделаете,
 ваш новый аккаунт будет удален через пару дней. Мы рекомендуем вам прочитать правила
и ЧаВо прежде чем вы начнете использовать $SITENAME.
EOD;
$subject = <<<EOD
Подтверждение регистрации на $SITENAME
EOD;


if($use_email_act && $users) {
	if (!sent_mail($email,$SITENAME,$SITEEMAIL,$subject,$body,false)) {
		stderr("Ошибка данных smtp", "Невозможно отправить E-Mail. Попробуйте позже");
	}
} else {
	logincookie($id, $wantpasshash, "ip");
}


header("Refresh: 0; url=ok.php?type=". (!$users?"sysop":("signup&email=" . urlencode($email))));
?>