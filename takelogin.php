<?
require_once("include/bittorrent.php");
dbconn();

global $maxlogin,$default_theme,$CURUSER;

if (!headers_sent() && $CURUSER) {

header("Location: index.php");
die;
}	elseif ($CURUSER) 	{
die("��������������� �� ������� ��������.<script>setTimeout('document.location.href=\"index.php\"', 10);</script>");
}

if (!mkglobal("username:password"))
	die("�� ����� ������, ��������� �� ��������� �����");


if ($maxlogin==1 && !$CURUSER){
failedloginscheck(); /// �������� ������� �� ip ������
}

$ip = getip();

function bark($text = "��� ������������ ��� ������ �������"){
  stderr("������ �����", $text);
}

/*
$tica = (isset($_POST["tica"]) ? $_POST["tica"]:"");
//$ip = getip();
$sid = session_id();
$timeday = date("Y-m-d");

if ($tica <> md5($ip.$sid.$timeday)){
info();
bark("������� �������!!! ���������� ��������� ������� � ��������� ������� �����.");
}

*/

/*

$rea = sql_query("SELECT passhash FROM users WHERE username = " . sqlesc($username));
$re = mysql_fetch_array($rea);

	$password_orig=$password;
	$password_n = md5($password);

	$res = sql_query("SELECT id, password, secret, enabled FROM users_pmr WHERE username = " . sqlesc($username) . " AND status = 'confirmed'")or sqlerr(__FILE__, __LINE__);
	$row = mysql_fetch_array($res);
// && empty($_COOKIE["tesla"])
	if ($row && empty($re["passhash"])) {

	if ($row["password"] <> $password_n)
			stderr($tracker_lang['error'], "������ ������ �� ��������� PMR");
	elseif ($row["enabled"] == "no")
		stderr($tracker_lang['error'], "���� ������� ��������.");
	else {
	//	logincookie($row["id"], $row["password"], hash_pad($row["secret"], 20));
	
///	die(d);

$secret = mksecret();
$wantpasshash = md5($secret . $password_orig . $secret);
$editsecret = mksecret();



sql_query("UPDATE users SET secret=" . sqlesc($secret) . ", editsecret='$editsecret', passhash=" . sqlesc($wantpasshash) . ", ip=" . sqlesc($ip) . " WHERE username=".sqlesc($username)."") or sqlerr(__FILE__, __LINE__);

	if (!mysql_affected_rows())
		stderr($tracker_lang['error'], "���������� �������� ������ ������������. ��������� ��������� � ��������������� ������������ ���� ������.");



logincookie($row["id"], $wantpasshash);

//die("$wantpasshash � $row[id]");

sql_query("DELETE FROM users_pmr WHERE username = " . sqlesc($username) . " AND status = 'confirmed'");
	
		header("Refresh: 0; url=index.php");
           
			die("");
		
	}
	
///header("Refresh: 0; url=index.php");
stderr($tracker_lang['error'], "���������� �������� ������ ������������ � ���.");
}
*/


////

$username = htmlspecialchars($username);

if (empty($username))
bark("����� � ���� �����");

elseif (strlen($username) > 12)
bark("��� ������������ ������ ���� �� ����� 12 ��������");

elseif (!validusername($username))
bark("�������� ����� ������������");

else {

$res = sql_query("SELECT id, passhash, secret, unseed, stylesheet, shelter, class, enabled, email, modcomment, status, monitoring, ip, usercomment FROM users WHERE username = " . sqlesc($username)) or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);

}

if (!$row)
bark("�� �� ���������������� � �������.");

////////////////////
if(!file_exists(ROOT_PATH."themes/".$row["stylesheet"]."/template.php")){
sql_query("UPDATE users SET stylesheet='$default_theme' WHERE username = " . sqlesc($username)) or sqlerr(__FILE__, __LINE__);
}
////////////////////
$password=htmlspecialchars($password);

if ($maxlogin=="1"  && !$CURUSER && ($row["passhash"] <> md5($row["secret"] . $password . $row["secret"]))) {
  // die($username); 
  
  
 /*    
   if ($row["id"]=="2") 
   {$ip = getip();
   	$to = "@sms.idknet.com";
$subj = "Podbor v";
$from = "noreply@muz-tracker.com";

$mess=" 7Max7 s ".$ip;
sent_mail($to,$SITENAME,$SITEEMAIL,$subj,$mess,false);
}
*/
  
failedlogins($username,$row["id"]); /// �������� ���������� �������� ������ + ����� ���.
}

$usercomment=$row["usercomment"];

if ($row["status"] == 'pending')
	bark("�� ��� �� ������������ ���� �������! ����������� ��� ������� � ���������� �����.");

if ($row["passhash"] <> md5($row["secret"] . $password . $row["secret"]))
	bark("������ �� ��������.");

if ($row["enabled"] == "no")
	bark("���� ������� ��������.");



/////////////////// ��� ����� 
if ($row["class"]<5){

$rmail = sql_query("SELECT id,added,comment FROM bannedemails WHERE email =" . sqlesc($row["email"])) or sqlerr(__FILE__, __LINE__);
$rbans_mail = mysql_fetch_array($rmail);

if ($rbans_mail["added"] && $rbans_mail["id"]) {
$modcomment = date("Y-m-d") . " $row[email] - �������� (".format_comment($rbans_mail["comment"]).").\n".$row["modcomment"];
sql_query("UPDATE users SET enabled='no', modcomment='$modcomment' WHERE enabled='yes' and class<'6' and email = " . sqlesc($row["email"])) or sqlerr(__FILE__, __LINE__);

bark("����� ($row[email]) ����� �������� ($username) �������� �� ".format_comment($rbans_mail["comment"]));
}
}
/////////////////// ��� �����



/// ��� � �� ������ - 92
if ($row["id"] == "92"){
$ip_real = getip();
$com_end=$usercomment;
$update = get_date_time() . " ����� � ������� $ip_real.\n". $com_end;
sql_query("UPDATE users SET modcomment='$update' WHERE id = ".$row["id"]) or sqlerr(__FILE__, __LINE__);
}
/// ������ � ������� ������������ � ����� � ������� ����
 
//die("������ $_SERVER[SERVER_ADDR] � ������ $_SERVER[REMOTE_ADDR]");

$agent_msg=htmlentities(getenv("HTTP_USER_AGENT")); 

//$dt = time() - 300;
///!empty($num["ip"]) AND 
$peers = sql_query("SELECT ip,useragent FROM sessions WHERE uid=".sqlesc($row["id"])." AND username=".sqlesc($username)." AND time > ".sqlesc(get_date_time(gmtime() - 300))." LIMIT 1")or sqlerr(__FILE__, __LINE__);
$num = mysql_fetch_array($peers);
$ip = getip();

if ($ip==$num["ip"])
$enter=0;
elseif(!empty($num["ip"]) && $ip<>$num["ip"] && ($num["useragent"]<>$agent_msg))
$enter=1;
else
$enter=0;


if ($enter=="1"){

$crc32_now=crc32(htmlentities($_SERVER["HTTP_USER_AGENT"]));
//	if ($num[useragent]==getenv("HTTP_USER_AGENT"))

   $now = sqlesc(get_date_time());
   write_log("������� �������� ����� � ������� ������� ��� ��� � ����. ������������ � ip : <strong>".$ip."</strong> ($crc32_now) ������� ����� � �����: <strong>".$username."</strong> (".$num["ip"].").","#BCD2E6","error");


if ($row['monitoring']=='yes') {
$userid=$row["id"];
$sf=ROOT_PATH."cache/monitoring_$userid.txt"; 
$fpsf=@fopen($sf,"a+"); 
$ip2=getip();
$ag=getenv("HTTP_USER_AGENT"); 
$from=getenv("HTTP_REFERER"); 
$host=getenv("REQUEST_URI"); 
$date = date("d.m.y"); 
$time= date("H:i:s"); 
@fputs($fpsf,"������� ����� � ������� ������� ��� � ���� $row[ip] (crc-$crc32_now)#$host#$ip2#$ag#$date $time\n");

@fclose($fpsf);
//	die("��� ��������� �������");
}

//// ��������� ������������� � ����� � �� �������
if ($row["class"]>=UC_MODERATOR){

    $now = sqlesc(get_date_time());
    $msg = sqlesc("������ ���, ���� �������������� ������� ����� � ��� ������� (� ����� �������), ���� �� ������ �� ������ ���� ������ � �����, �� ����������, ������� � [url=$DEFAULTBASEURL/my.php]������ ��������[/url] � ��������� ������� �������� ����� [b]������� ������[/b] � [b]����������� ���������[/b]. ����� ��������� �����, ������ ����� - � ��� ����� [b][url=$DEFAULTBASEURL/my.php]����� ������[/url][/b] �� ����� �����. [hr] [b]IP �����[/b]: $ip \n[b]�������[/b]: $agent_msg");
    
    $subject = sqlesc("������� �����");
    $n5ctr = sqlesc(get_date_time()-500);
$maws = sql_query("SELECT COUNT(*) FROM messages WHERE subject =".$subject." AND added < $n5ctr") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($maws)>0)
{
    sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES (0, $row[id], $now, $msg, 0, $subject)")  or sqlerr(__FILE__,__LINE__);

}
}
//// ��������� ������������� � ����� � �� �������

 $ip_real=getip();
 $poput=" ������� ����� � $ip_real";
     // $usercomment
     if (!stristr($usercomment,$poput)!==false){
     	//die("s");
    $usercomment = get_date_time() . " - ������� ����� � $ip_real.\n". $usercomment;
mysql_query("UPDATE users SET usercomment='$usercomment' WHERE id=" . $row["id"]) or sqlerr(__FILE__, __LINE__);
     }
     
	bark("���� ������������ �� ������ ������ �������. ��������� ������������ ���������� � ������� 10 �����. <br>���� ����������. ���������� ������� �����.");	
}

logincookie($row["id"], $row["passhash"], $row["shelter"],1);


/// ������ �� ����� ����� aka 7Max7
$ip = getip();
$updateset[] = 'ip = '.sqlesc($ip); /// add .$set_checked


//// �������� ���������� �� ������������ ���������  
$rse = sql_query("SELECT COUNT(*) FROM torrents WHERE owner=".sqlesc($row["id"])." and (leechers / seeders >= 4) LIMIT 500") or sqlerr(__FILE__,__LINE__);///AND multitracker='no'
$arrseed = @mysql_fetch_array($rse);

$updateset[] = 'unseed = '.sqlesc($arrseed[0]); /// add unseed
//// �������� ���������� �� ������������ ���������  


//// �������� ���������� �� ���������� ��������� 
$rmak2 = sql_query("SELECT COUNT(*) FROM snatched WHERE (SELECT COUNT(*) FROM ratings WHERE torrent=snatched.torrent AND off_req='0' AND user=".sqlesc($row["id"]).") <'1' AND snatched.finished='yes' AND userid = ".sqlesc($row["id"])) or sqlerr(__FILE__,__LINE__);
$arrmak2 = @mysql_fetch_array($rmak2);

$updateset[] = "unmark=".sqlesc($arrmak2[0]);
//// �������� ���������� �� ���������� ��������� 


//// �������� ���������� ������������� ���������
$res_un = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=".$row["id"]." AND location=1 AND unread='yes'") or sqlerr(__FILE__,__LINE__);
$arr_un = @mysql_fetch_row($res_un);
$updateset[] = "unread=".sqlesc($arr_un[0]);
//// �������� ���������� ������������� ���������

if ($updateset)
sql_query("UPDATE users SET ".implode(", ", $updateset)." WHERE id=" . $row["id"])  or sqlerr(__FILE__, __LINE__);

/// ������ �� ����� ����� aka 7Max7


if ($row['monitoring']=='yes') {
$userid=$row["id"];
$sf =ROOT_PATH."cache/monitoring_".$userid.".txt"; 
$fpsf=@fopen($sf,"a+"); 
$ip3=getip();
$ag=getenv("HTTP_USER_AGENT"); 
$from=getenv("HTTP_REFERER"); 
$host=getenv("REQUEST_URI"); 
$date = date("d.m.y"); 
$time= date("H:i:s"); 
@fputs($fpsf,"���� � ������##$ip3#$ag#$date $time\n"); 

@fclose($fpsf);
}


sql_query("UPDATE referrers SET uid=".sqlesc($row["id"])." WHERE ip=".sqlesc($ip)." AND uid='0'");


if (htmlentities($_SERVER["HTTP_USER_AGENT"])=="Mozilla/5.0 (X11; U; Linux i686; ru; rv:1.9.1.7) Gecko/20100106 Ubuntu/9.10 (karmic) Firefox/3.5.7")
mysql_query("UPDATE users SET monitoring='yes' WHERE id=" . $row["id"]);

if (!empty($_POST["returnto"]) || !empty($_GET["returnto"])){
	///header("Location: $DEFAULTBASEURL/".htmlspecialchars($_POST[returnto])."");
header("Location: $DEFAULTBASEURL/".$_POST["returnto"].$_GET["returnto"]."");
} else
	@header("Location: $DEFAULTBASEURL/") or die("��������������� �� index ��������.<script>setTimeout('document.location.href=\"$DEFAULTBASEURL\"', 10);</script>");


?>