<?php

require_once("include/bittorrent.php");
dbconn();
global $CURUSER;

stdheadchat("������ ������������ �����");

$sendwho = 0;/// 1 �������� �� ����� ������, ����� 0 �������� � �� �� �����.

if ($_GET["send"]) {

//if ($_GET["send"]==1)
//stdmsg("�������", "������ �� ����� ����������. <a href=\"$BASEURL/support.php\">�������� �����</a>");
//else
stdmsg("�������", "��������� ���������� �������������. <a href=\"$BASEURL/support.php\">�������� �����</a>");

stdfootchat();
die;
}

if (count($_POST) || $_SERVER["REQUEST_METHOD"] == "POST") {

$tica = (isset($_POST["tica"]) ? $_POST["tica"]:"");
$ip = getip();
$sid = session_id();
$timeday = date("Y-m-d");

if ($tica <> md5($ip.$sid.$timeday)){

stdmsg("������� �������", "���������� ��������� ������� � ��������� ������� �����.");

stdfootchat();
die;
}


$username = htmlspecialchars(strip_tags($_POST["username"]));/// ��� ������������ ���� - �� �����������
$text = htmlspecialchars_uni($_POST["text"]); /// �����
$subject = htmlspecialchars_uni($_POST["subject"]); /// �����
$ip = getip(); 
$now = get_date_time();

if (empty($subject) || empty($text)) {
stdmsg("������ ������", "����� � ���� ���� ��� ����� ���������. <a href=\"$BASEURL/support.php\">�������� �����</a>");
stdfootchat();
die;
}


if (!empty($CURUSER))
$username = $CURUSER["username"]." [".$CURUSER["id"]."] (�������������� ������������)";
elseif (empty($username) && empty($CURUSER))
$username = "������ (�� ������������ ������������)";

$subj = $SITENAME.": ".$subject;

$all = "���� ���������: ".$subject."\n\n�����: ".$text."\n\nip �����: ".$ip."\n�������� ���: ".$username."\n\n����� �������: ".$now."\n\nC���� � ������������� - $BASEURL/support.php (������ ������ ������) \n������� ����� ".$SITENAME.".";/// ����� ���������



if ($subject == "��������� ������ Tesla TT v.Gold" || stristr($subject,"Tesla")) {

if (sent_mail("maksim7777Max7@rambler.ru",$SITENAME,$SITEEMAIL,$subj,$all,false)) {
stdmsg("�������", "������ ���������� ��������� ������ Tesla TT v.Gold �� �����. ����� ������... <a href=\"$BASEURL/support.php\">�������� �����</a>");
stdfootchat();
die;
}

}


if ($sendwho == 1) { /// �� ����� ����������

$res = sql_query("SELECT email FROM users WHERE class=".UC_SYSOP."") or sqlerr(__FILE__, __LINE__);

while ($ip = @mysql_fetch_array($res)){

if (!sent_mail($ip["email"],$SITENAME,$SITEEMAIL,$subj,$all,false)) {
stdmsg("������ ������", "�� ���� ��������� ������ �� �����. <a href=\"$BASEURL/support.php\">�������� �����</a>");
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
stdmsg("��������", "���������� ������. <a href=\"$BASEURL/support.php\">�������� �����</a>");
stdfootchat();
die;
}

?>

<script type="text/javascript" language="javascript">
$(document).ready(function () {
	$("select[name=subject]").change(function () {

	if ($(this).val() == "������������� �� �����") {
		$("textarea[name=text]").empty();
		$("textarea[name=text]").append('������: \n�����: \n����� �������������:');
	} else if ($(this).val() == "�������� �� �����") {
		$("textarea[name=text]").empty();
		$("textarea[name=text]").append('��������  (������� ������ ����): \n��������: \n��� �������� (��� � ����� / ������):');
	} else if ($(this).val() == "������� � ��������") {
		$("textarea[name=text]").empty();
		$("textarea[name=text]").append('������� (������� ������ ����): \n������:');
	} else if ($(this).val() == "���������������") {
		$("textarea[name=text]").empty();
		$("textarea[name=text]").append('������� (������� ������ ����): \n���� ���������: \nE-mail ���������������: \n���� ����������: \n�����, � ������������� ��������� ����������: ');
	} else if ($(this).val() == "������� �� �����") {
		$("textarea[name=text]").empty();
		$("textarea[name=text]").append('�������: \n[b][u]���� ��������:[/u][/b]\nE-mail: \nICQ: \nSkype: ');
	} else if ($(this).val() == "��������� ������ Tesla TT v.Gold") {
		$("textarea[name=text]").empty();
		$("textarea[name=text]").append('----------->\n����������� ���������� ������ Tesla TT v.Gold �������� 7Max7 [icq: 225454228] (��� ��������� Imperator)\n----------->\n\n������� ���� ��������� ����, ������� �����, ����, ���� ��� �������� (��������� ������): \n\nC���� � ����: ');
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
<td class=\"colhead\" colspan=\"2\" align=\"center\">������ ������������ (����� ����� � ��������������)</td>
</tr>";

echo "<tr>
<td colspan=\"2\" class=\"a\" align=\"center\"><b>��������</b>: ������ (���������) ������������ ���������, ���� ����� ��� �� ������� ��� ������ ������ ��� (��� ��� max ����� ������������) �������� ���������� �����, ����� ��.<br> �����, ���� �� ���� ��������� (���� ����, ����, ������� ������� - �� ��������������), ������ ����� ��������� �� �������������� ����������� �������. ������ � <u>������</u> � <u>������� ������ �����������</u> (��� ������ ��� � ���������) - ��������� ������������. ������������ ������, <u>�� ��� ����������� � ������������</u>, ������ ���� ���������� �������� ��� �������������� ����� ��� �� ip ������� ��� � �� email.<br><br><center>������� �� ��������, ������� ����� ".$SITENAME."</center></td></tr>";

echo "<tr>
<td class=\"b\">���� ���: </td>
<td class=\"b\">
<input name=\"username\" type=\"text\" ".($CURUSER ? " value=\"".$CURUSER["username"]."\" disabled ":"")." size=\"40\"/>
</td>
</tr>";

echo "<tr>
<td class=\"b\">��� ���������: </td>
<td class=\"b\">
<select size=\"1\" name=\"subject\">
<option selected>����� �������</option>
<option>������� �� �����</option>
<option>�������� �� �����</option>
<option>������� � ��������</option>
<option>������������� �� �����</option>
<option>���������������</option>
<option>��������� ������ Tesla TT v.Gold</option>
</select>
</td>
</tr>";

echo "<tr>
<td colspan=\"2\" class=\"a\" align=\"center\">���� ���������:</td></tr>";
	

echo "<tr>
<td colspan=\"2\" align=\"center\">";

echo textbbcode("comment", "text","",1);

echo "</td></tr>";


echo "<tr>
<td colspan=\"2\" align=\"center\">
<input type=\"submit\" class=\"btn\" value=\"��������� ���������\" />
<input type=\"reset\" class=\"btn\" value=\"�������� ����� ���������\"/></td>
</tr>";

echo "</form></table>";



stdfootchat();
?>