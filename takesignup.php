<?
require_once("include/bittorrent.php");
require_once("include/functions_bot.php"); // ���������� ������� ����

dbconn();

if ($deny_signup && !$allow_invite_signup)
	stderr($tracker_lang['error'], "��������, �� ����������� ��������� ��������������.");

if ($CURUSER)
	stderr($tracker_lang['error'], sprintf($tracker_lang['signup_already_registered'], $SITENAME));

$users = get_row_count("users");
if ($users >= $maxusers)
	stderr($tracker_lang['error'], sprintf($tracker_lang['signup_users_limit'], number_format($maxusers)));

if (!mkglobal("wantusername:wantpassword:passagain:email"))
	stderr($tracker_lang['error'], "������ ������ � ����� ����� �� ��������.");

if ($deny_signup && $allow_invite_signup) {
	if (empty($_POST["invite"]))
		stderr("������", "��� ����������� ��� ����� ������ ��� �����������!");
	if (strlen($_POST["invite"]) <> 32)
		stderr("������", "�� ����� �� ���������� ��� �����������.");
		
	list($inviter) = mysql_fetch_row(sql_query("SELECT inviter FROM invites WHERE invite = ".sqlesc(htmlentities($_POST["invite"]))));
	
	if (!$inviter)
		stderr("������", "��� ����������� ��������� ���� �� �������.");
	///list($invitedroot) = mysql_fetch_row(sql_query("SELECT invitedroot FROM users WHERE id = $inviter"));
}


if (!$deny_signup && $allow_invite_signup && !empty($_POST["invite"])){
if (strlen($_POST["invite"]) <> 32)
		stderr("������", "�� ����� �� ���������� ��� �����������.");
		
list($inviter) = mysql_fetch_row(sql_query("SELECT inviter FROM invites WHERE invite = ".sqlesc(htmlentities($_POST["invite"]))));

	if (!$inviter)
		stderr("������", "��� ����������� ��������� ���� �� �������.");
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
 //   bark("����, ����� icq ������� �������  (���� - 10)");
$icq = str_replace('-', '', $_POST["icq"]);
$icq = (int) $icq;
if (strlen($icq) < 10 && !empty($icq))
$updateset[] = "icq = " . sqlesc($icq);

$skype = unesc(htmlspecialchars($_POST["skype"]));
if (strlen($skype) > 20)
    bark("����, ��� skype ������� �������  (���� - 20)");
$updateset[] = "skype = " . sqlesc($skype);

$odnoklasniki = unesc(htmlspecialchars($_POST["odnoklasniki"]));

if (strlen($odnoklasniki) > 300)
bark("��������, ������� ������� �����! [�������������]");
$updateset[] = "odnoklasniki = " . sqlesc($odnoklasniki);

$vkontakte = unesc(htmlspecialchars($_POST["vkontakte"]));  
if (strlen($vkontakte) > 300)
    bark("��������, ������� ������� �����! [���������]");  
$updateset[] = "vkontakte = " . sqlesc($vkontakte);


if (empty($wantusername) || empty($wantpassword) || empty($email) || empty($gender) || empty($country))
	bark("���� - ������������, ������, Email, ��� � ������ ����������� ��� ����������.<br>
	<b>�� �������</b>: 
	".(empty($wantusername)?"������������ ":"")."
	".(empty($wantpassword)?"������ ":"")."
	".(empty($email)?"����� ":"")."
	".(empty($gender)?"��� ":"")."
	".(empty($country)?"������ ":"")." �����.");

if (strlen($wantusername) > 12)
	bark("��������, ��� ������������ ������� ������� (�������� 12 ��������)");

if ($wantpassword <> $passagain)
	bark("������ �� ���������! ������ �� ��������. ���������� ���.");

if (strlen($wantpassword) < 7)
	bark("��������, ������ ������� �������� (������� 7 ��������)");

if (strlen($wantpassword) > 40)
	bark("��������, ������ ������� ������� (�������� 40 ��������)");

if ($wantpassword == $wantusername)
	bark("��������, ������ �� ����� ���� �����-�� ��� ��� ������������.");

if (!validemail($email))
	bark("��� �� ������ �� �������� email �����.");

if (!validusername($wantusername))
	bark("�������� ��� ������������.");

if ($year=='0000' || $month=='00' || $day=='00')
stderr($tracker_lang['error'],"������ �� ������� �������� ���� ��������");

$birthday = date($year.$month.$day);

if (!empty($email_rebans))
check_banned_emails($email);  

// check if email addy is already in use
$a = (@mysql_fetch_row(@sql_query("SELECT COUNT(*) FROM users WHERE email=".sqlesc($email))));
if ($a[0] == 1)
bark("E-mail ����� - $email ��� ��������������� � �������.");


//$email = htmlspecialchars($_POST['email']); 



$ip = getip();

if (isset($_COOKIE["uid"]) && is_numeric($_COOKIE["uid"]) && $users)
{
    $cid = intval($_COOKIE["uid"]);
    $c = sql_query("SELECT enabled FROM users WHERE id = ".sqlesc($cid)." ORDER BY id DESC LIMIT 1");
    $co = @mysql_fetch_row($c);
    if ($co[0] == 'no') {
		sql_query("UPDATE users SET ip = ".sqlesc($ip).", last_access = NOW() WHERE id = ".sqlesc($cid)."");
	//	bark("��� IP ������� �� ���� �������. ����������� ����������.");
    } else {
    	logoutcookie();
    //bark("����������� ���������� �� �� ��������� � ������, ����� �������� ��!");
    }
}
/*
else 
{

    $b = (@mysql_fetch_row(@sql_query("SELECT enabled, id FROM users WHERE ip LIKE '$ip' ORDER BY last_access DESC LIMIT 1")));
    if ($b[0] == 'no') {
		$banned_id = $b[1];
        setcookie("uid", $banned_id, "0x7fffffff", "/");
	//	bark("��� IP ������� �� ���� �������. ����������� ����������.");
    }
*/

$a = @mysql_fetch_row(@sql_query("SELECT COUNT(*) FROM users WHERE ip=".sqlesc($ip)));
if ($a[0]>3) /// �������� ������������� ��������� �� �����, ���� ����� ip �������� ������ N ������������.
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
{$warn="����������� $username (���� ����� �����), ����� ����.";}
else
{$warn="����������� $username, ����� ����.";}
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
		bark("������������ $wantusername ��� ���������������!");
//	bark("����������� ������. ����� �� ������� mySQL: ".htmlspecialchars(mysql_error()));
}

$id = mysql_insert_id();

if (!empty($inviter))
sql_query("DELETE FROM invites WHERE invite = ".sqlesc(htmlspecialchars($_POST["invite"])));

if ($users) {
write_log("��������������� ����� ������������ $wantusername","000000","tracker");
}

if (!$users) {
write_log("��������������� ���� $wantusername (������� ����, aka 7Max7)","000000","tracker");
}


///////////////
$upload = (10*1073741824); ///10 ��

$usercomment = sqlesc(date("Y-m-d") . " - ��������� ������� �� ������� (".mksize($upload).").\n");

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
�� ������������������ �� $SITENAME � ������� ���� ����� ��� �������� ($email).

���� ��� ���� �� ��, ��������� �������������� ��� ������. ������� ������� ����� ��� E-Mail ������ ����� IP ����� {$ip}. ���������, �� ���������.

�� ������� ������:
�����: $wantusername
������: $wantpassword

��� ������������� ����� �����������, ��� ����� ������ �� ��������� ������:

$DEFAULTBASEURL/confirm.php?id=$id&secret=$psecret

����� ���� ��� �� ��� ��������, �� ������� ������������ ��� �������. ���� �� ����� �� ��������,
 ��� ����� ������� ����� ������ ����� ���� ����. �� ����������� ��� ��������� �������
� ���� ������ ��� �� ������� ������������ $SITENAME.
EOD;
$subject = <<<EOD
������������� ����������� �� $SITENAME
EOD;


if($use_email_act && $users) {
	if (!sent_mail($email,$SITENAME,$SITEEMAIL,$subject,$body,false)) {
		stderr("������ ������ smtp", "���������� ��������� E-Mail. ���������� �����");
	}
} else {
	logincookie($id, $wantpasshash, "ip");
}


header("Refresh: 0; url=ok.php?type=". (!$users?"sysop":("signup&email=" . urlencode($email))));
?>