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
  
  if (!validemail($email)) // �����������
    stderr($tracker_lang['error'], "�� ������ ������ email �����");
    
  $res = sql_query("SELECT * FROM users WHERE email=" . sqlesc($email) . " LIMIT 1") or sqlerr(__FILE__, __LINE__);
  $arr = mysql_fetch_assoc($res) or stderr($tracker_lang['error'], "Email ����� �� ������ � ���� ������.\n");


if ($arr["enabled"] == "no")
stderr($tracker_lang['error'],"������� ������ � ������ $email - ���������! ������������� ������ ����������.");


	$sec = mksecret();

  sql_query("UPDATE users SET editsecret=" . sqlesc($sec) . " WHERE id=" . $arr["id"]) or sqlerr(__FILE__, __LINE__);
  if (!mysql_affected_rows())
	  stderr($tracker_lang['error'], "������ ���� ������. ��������� � ��������������� ������������ ���� ������.");

  $hash = md5($sec . $email . $arr["passhash"] . $sec);
$ip = getip();
  $body = <<<EOD
��, ��� ���-�� ������, ��������� ����� ������ � �������� ��������� � ���� ������� ($email).

������ ��� ������ ��������� � IP ������� {$ip}.

���� ��� ���� �� ��, �������������� ��� ������. ��������� �� ���������.

���� �� ������������� ���� ������, ��������� �� ��������� ������:

$DEFAULTBASEURL/recover.php?id={$arr["id"]}&secret=$hash


����� ���� ��� �� ��� ��������, ��� ������ ����� ������� � ����� ������ ����� ��������� ��� �� E-Mail.

--
$SITENAME
EOD;

	@sent_mail($arr["email"],$SITENAME,$SITEEMAIL,"������������� �������������� ������ �� $SITENAME",$body)
		or stderr($tracker_lang['error'], "���������� ��������� E-mail. ��������� �������� ������������� �� ������.");
	stderr($tracker_lang['success'], "�������������� ������ ���� ����������.\n" .
		" ����� ��������� ����� (������ �����) ��� ������� ������ � ����������� ����������.");
		}
		
		$id_username=(isset($_POST["id_username"]) ? (int)$_POST["id_username"]:""); // id ������������
		$username_id=(isset($_POST["username_id"]) ? htmlspecialchars($_POST["username_id"]):""); // ��� ������������
		
		if (!empty($id_username) || !empty($username_id)){
	//	echo "d";	
	///die($id_username.$username_id);
	  if (!empty($username_id)){
	  	$sql="username=".sqlesc($username_id)."";
	  	$vw="����� ������: $username_id";
	  } else {
	  $sql="id=".sqlesc($id_username)."";
	  $vw="����� id: $id_username";
	}
	  $res = sql_query("SELECT id,enabled, question FROM users WHERE $sql and enabled='yes' AND status = 'confirmed' LIMIT 1") or sqlerr(__FILE__, __LINE__);
      $row = mysql_fetch_array($res);

if (empty($row)) {
stderr($tracker_lang['error'],"������ �� �������� �� $vw. �������� ������ �� ����������� �� ����� ��� ��������.");
}

if ($row["enabled"] == "no") {
stderr($tracker_lang['error'],"������� ������ - ���������! ������������� ������ ����������.");
}

if (empty($row["question"])) {
stderr($tracker_lang['error'],"� ���� ������� ������ ��� ������� � ������. ������������� ������ ����������.");
}

 $question=$row["question"];
    if ($question){
    	
    /// ������ �����
if ($_COOKIE["recover"]<>md5($_SERVER["HTTP_USER_AGENT"].date('H'))){
@header("Location: ../index.php");
die("������� �������, �������� ���������� ����� � �������� �� ��������� �������������� ������.");
}
    	
    logoutcookie();
	stdhead("���� ������ �� ��������� ������ ������������");

	echo "
		<form method=\"post\" action=\"recover.php\">
	<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">
	<tr><td class=\"colhead\" colspan=\"2\">������ ���������� �������� �� �������� ������ ����</td></tr>
	<tr><td align=\"center\"colspan=\"2\"><b title=\"������ ������� ������������\">$question</b></td></tr>
	
	<tr><td class=\"a\" width=\"20%\" align=left><b>������� �����</b>: </td>
	<td class=\"a\"><input type=\"password\" size=\"40\" name=\"do_you_wanna\"></td></tr>
	
	<tr><td colspan=\"2\" align=\"center\">
	<input type=hidden name=userid value=\"".$row["id"]."\">
	<input type=\"submit\" value=\"������������� � �������� ��� ��� ����� ������\"></td></tr>
	</form>
	</table><br>";
	stdfoot();
		}
		}
	$do_you_wanna=(isset($_POST["do_you_wanna"]) ? htmlspecialchars($_POST["do_you_wanna"]):""); // ��� ������������
		if (!empty($do_you_wanna))
		{
	
	 /// ������ �����
if ($_COOKIE["recover"]<>md5($_SERVER["HTTP_USER_AGENT"].date('H'))){
@header("Location: ../index.php");
die("������� �������, �������� ���������� ����� � �������� �� ��������� �������������� ������.");
}
	$userid=htmlspecialchars((int)$_POST["userid"]); // id ������������
			
	  $res_do = sql_query("SELECT id, shelter, rejoin, added, username, class,question FROM users WHERE enabled='yes' AND status = 'confirmed' and id=".sqlesc($userid)."") or sqlerr(__FILE__, __LINE__);
      $row_do = mysql_fetch_array($res_do);
      
 if (empty($row_do["id"])) {
stderr($tracker_lang['error'],"��� ������ � ���������� (��������������) ������������. ������������� ������ ����������.");
}

      if (empty($row_do["question"])) {
stderr($tracker_lang['error'],"� ���� ������� ������ ��� ������� � ������. ������������� ������ ����������.");
}
      $shelter=$row_do["shelter"];
      $questio_n=$row_do["question"];
       $rejoin=$row_do["rejoin"];
	  $row_secret=$row_do["added"];	
	  $update_new=md5($row_secret.$do_you_wanna.$row_secret);
	
	if ($rejoin==$update_new){
	
	/// ���������� � ������� ����� ������ ������������
	 $chpassword = generatePassword();
	  $sec = mksecret();
	$passhash = md5($sec.$chpassword.$sec);
	$updateset[] = "secret = " . sqlesc($sec); /// �����
	$updateset[] = "passhash = " . sqlesc($passhash);
	
	sql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = ".sqlesc($userid)."") or sqlerr(__FILE__,__LINE__);
	
	logincookie($userid, $passhash, $shelter,1); // �������� �� ����
	
	stdhead("��� ����� ������ ��� �����");
	
///	stdhead("���� ������ �� ��������� ������ ������������");
	echo "<script type=\"text/javascript\">
		function highlight(field) {	field.focus();	field.select();	}
        </script>
        
	<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">
	<tr><td class=\"colhead\" colspan=\"2\">���� ����� ������ ����</td></tr>
	<tr><td class=\"b\" align=\"center\"colspan=\"2\">
    <b>�����</b>: <b>".get_user_class_color($row_do["class"], $row_do["username"])."</b><br>
	<b>����� ������</b>: $chpassword <br><br>
	<b>���� �� �����, �� � ����� �����</b>: <input type=\"text\" size=\"20\" value=\"".$row_do["username"]."\" name=\"username\"><br>
	<b>���� �� ������, �� � ����� �����</b>: <input type=\"text\" size=\"20\" value=\"$chpassword\" name=\"password\">
	</td></tr>

	
	<tr><td colspan=\"2\" class=\"a\" align=\"center\"><i>������� �� ����� ������ ���� - � ��� ���������� �������� �� ������.</i>
    </td></tr>

	</table><br>";
	stdfoot();
	}
	else
	{
	  
	stdhead("�������� ������ � ������ ��������");
	
	

///	echo "	<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">	<tr><td class=\"colhead\" colspan=\"2\">��������</td></tr>	<tr><td align=\"center\"colspan=\"2\">�������� ������ �������, ��������� ������� � ��������� �������.		</td></tr>	</table><br>";

echo "
	<form method=\"post\" action=\"recover.php\">
	<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">
	<tr><td class=\"colhead\" colspan=\"2\">�������� ������ �������, ��������� ��������� ������� �����</td></tr>
<tr><td align=\"center\"colspan=\"2\"><b title=\"������ ������� ������������\">$questio_n</b></td></tr>
	
	<tr><td class=\"a\" width=\"20%\" align=left><b>������� �����</b>: </td>
	<td class=\"a\"><input type=\"password\" size=\"40\" name=\"do_you_wanna\"></td></tr>
	
	<tr><td colspan=\"2\" align=\"center\">
	<input type=hidden name=userid value=\"$userid\">
	<input type=\"submit\" value=\"��������� ����� ��������\"></td></tr>
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
	  httperr("��� id");

	$res = sql_query("SELECT username, email, passhash, editsecret FROM users WHERE id = ".sqlesc($id)."");
	$arr = mysql_fetch_array($res) or httperr();

  $email = $arr["email"];

	$sec = hash_pad($arr["editsecret"]);
	if (preg_match('/^ *$/s', $sec))
	  httperr("������ � $sec");
	if ($md5 != md5($sec . $email . $arr["passhash"] . $sec))
		stderr($tracker_lang['error'], "������ �������, ��������� ��������� ������.");

	// generate new password;
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

  $newpassword = "";
  for ($i = 0; $i < 10; $i++)
    $newpassword .= $chars[mt_rand(0, strlen($chars) - 1)];

 	$sec = mksecret();

  $newpasshash = md5($sec . $newpassword . $sec);

	sql_query("UPDATE users SET secret=" . sqlesc($sec) . ", editsecret='', passhash=" . sqlesc($newpasshash) . " WHERE id=".sqlesc($id)." AND editsecret=" . sqlesc($arr["editsecret"]));

	if (!mysql_affected_rows())
		stderr($tracker_lang['error'], "���������� �������� ������ ������������. ��������� ��������� � ��������������� ������������ ���� ������.");

	$userid = $id;
	$chpassword = $newpassword;

  $body = <<<EOD
�� ������ ������� �� �������������� ������, �� ������������� ��� ����� ������.

��� ���� ����� ������ ��� ����� ��������:

    ������������: {$arr["username"]}
    ������:       $newpassword

�� ������ ����� �� ���� ���: $DEFAULTBASEURL/login.php

--
$SITENAME
EOD;

  @sent_mail($email,$SITENAME,$SITEEMAIL,"������ �������� �� $SITENAME",$body)
    or stderr($tracker_lang['error'], "���������� ��������� E-mail. ��������� �������� ������������� �� ������.");
  stderr($tracker_lang['success'], "����� ������ �� �������� ���������� �� E-Mail <b>". htmlspecialchars($email) ."</b>.\n" ."����� ��������� ����� (������ �����) �� �������� ���� ����� ������.");
}
else
{
	 	
    $recnow=md5($_SERVER["HTTP_USER_AGENT"].date('H'));
 	@setcookie("recover", $recnow, "0x7fffffff", "/");
 	/// ������ �����
 	
 	
 	stdhead("�������������� ������");
 	
 	$uid=(isset($_COOKIE["uid"]) ? (int) $_COOKIE["uid"]:"");
	?>
	<style type="text/css">
input.mail{background: url(pic/contact/email.gif) no-repeat;    background-color: #fff;    background-position: 0 50%;    color: #000;    padding-left: 18px;}
.rowhead2 {  font-weight: bold;  text-align: right;}
</style>
	<form method="post" action="recover.php">
	<table width="100%" border="1" cellspacing="0" cellpadding="5">
	<tr><td class="colhead" colspan="2">�������������� ������ ��� ������</td></tr>
	<tr><td align="center"colspan="2">����������� ����� ���� ��� ������������� ������ � ���� ������ ����� ���������� ��� �� �����. <br>�� ������ ������ ����������� ������.</td></tr>
	<tr><td class="a">����������������� email</td>
	<td class="a"><input type="text" size="40" name="email"class="mail"></td></tr>
	<tr><td colspan="2" align="center">
	<input type="submit" value="������������"></td></tr>
	</form>

	</table><br>
	
	<form method="post" action="recover.php">
	<table width="100%" border="1" cellspacing="0" cellpadding="5">
	<tr><td class="colhead" colspan="2">�������������� ������ �� ���������� ������� ������������</td></tr>
	<tr><td align="center"colspan="2">����������� ����� ���� ��� ������������� ������, ���� ������ ����� ����� �� ����� �������� ����� ��������������� ������.</td></tr>
	<tr><td class="a">������� ��� ������������</td>
	<td class="a"><input type="text" size="20" name="username_id"> <i>��������� �������</i></td></tr>
	
	<tr><td class="a">��� ������� id ������������</td>
	<td class="a"><input type="text" size="10" name="id_username" value="<?=$uid;?>"> <i>��������� �������</i></td></tr>
	
	<tr><td colspan="2" align="center">
	<input type="submit" value="������������"></td></tr>
	</form>

	</table><br>
	
	<?
	stdfoot();
}

?>