<?

require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

if (get_user_class() <= UC_MODERATOR)
{
attacks_log('bans'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}


$remove = (int) $_GET['remove'];
if (!empty($remove)) {
  $res = sql_query("SELECT first, last FROM bans WHERE id=".sqlesc($remove)."") or sqlerr(__FILE__, __LINE__);
  $ip = mysql_fetch_array($res);
  if (!$ip){
  stderr($tracker_lang['error'], "������ �������");
  die;
  }
  
  $first = long2ip($ip["first"]);
  $last = long2ip($ip["last"]);
  

 // die("$first && $last && $ip");
if ($first && $last && $ip) {
  write_log("��� IP ������ ����� $remove (".($first == $last? $first:"� $first �� $last").") ��� ����� ������������� $CURUSER[username].","704ffd","bans");
//die(sd);
sql_query("DELETE FROM bans WHERE id=".sqlesc($remove)."") or sqlerr(__FILE__, __LINE__);
//	header("Location: $_SERVER[REQUEST_URI]");

header("Refresh: 15; url=$BASEURL/bans.php");
stderr("������� ����������� ����� 15 ���", "IP ������ ����� $remove (".($first == $last? $first:"� $first �� $last").") ��� �����.");
die("bans");
}
@unlink(ROOT_PATH."cache/bans_first_last.txt");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && get_user_class() >= UC_ADMINISTRATOR)
{

	$first = trim($_POST["first"]);
	$last = trim($_POST["last"]);
	
	$cbanslength = htmlspecialchars($_POST["banslength"]); /// �� �������� � (int)
    if (strlen($cbanslength)>2 || strlen($cbanslength)<0)
    unset($cbanslength);



	$comment = trim($_POST["comment"]);
	if (!$first || !$last || !$comment)
		stderr($tracker_lang['error'], $tracker_lang['missing_form_data']);
		
	$first = ip2long($first);
	$last = ip2long($last);
	
	if (!is_numeric($first) || !is_numeric($last)) {
	  stderr($tracker_lang['error'], "�� �����, ��������� ���� ������");
	}
	
/// �������� ���� ������ ����� � ����
 $res = sql_query("SELECT id FROM bans WHERE ".sqlesc($first)." >= first AND ".sqlesc($last)." <= last") or sqlerr(__FILE__, __LINE__);
  $ip = mysql_fetch_array($res);
  if ($ip){
  stderr($tracker_lang['error'], "������ - ����� ������ ��� ����");
  }

if ($first == -1 || $last == -1 || empty($last) || empty($first))
stderr($tracker_lang['error'], $tracker_lang['invalid_ip']);


$resb = sql_query("SELECT COUNT(*) AS banua FROM users WHERE ip>=".sqlesc(long2ip($first))." AND ip<=".sqlesc(long2ip($last))." AND class >=".UC_MODERATOR."") or sqlerr(__FILE__, __LINE__);
$ipbe = mysql_fetch_array($resb);

$allusers = get_row_count("users");
$banuaall = get_row_count("users","WHERE ip>=".sqlesc(long2ip($first))." AND ip<=".sqlesc(long2ip($last)));

$tenuproc = $allusers/10;

if (!empty($ipbe["banua"])){

stderr($tracker_lang['error'], "��������, �� � ������ �������� ������ ������ ����� ".$ipbe["banua"]." ip �����(��) �������������.");

} elseif ($banuaall>$tenuproc){

stderr($tracker_lang['error'], "��������, �� ������� �������� �������� ip �����(��) ����������� ����� 10% ($banuaall) �� ���� ������������� ($allusers) ���������.");

}

		if ($cbanslength == 0){
		$time_until = "'0000-00-00 00:00:00'"; 
        } else {
		$until = get_date_time(gmtime() + $cbanslength * 604800);  /// ������
       	$time_until = "'$until'"; 
        }
          
	$comment = sqlesc(htmlspecialchars($comment));
	$added = sqlesc(get_date_time());
	$id_user=sqlesc($CURUSER["id"]);
	sql_query("INSERT INTO bans (added, addedby, first, last, bans_time, comment) VALUES($added, $id_user, $first, $last, $time_until, $comment)") or sqlerr(__FILE__, __LINE__);
	$l=long2ip($last); $f=long2ip($first);
	write_log("IP �����".($f == $l? " $f ��� �������":"� � $f �� $l ���� ��������")." ������������� ".$CURUSER["username"].".","ff3a3a","bans");
	@unlink(ROOT_PATH."cache/bans_first_last.txt");
	header("Location: $DEFAULTBASEURL$_SERVER[REQUEST_URI]");
	die;
}

gzip();
global $use_ipbans;

$res = sql_query("SELECT b.*,u.username,u.class FROM bans AS b 
LEFT JOIN users AS u ON u.id=b.addedby
ORDER BY b.added DESC") or sqlerr(__FILE__, __LINE__);

stdhead($tracker_lang['bans']);


if ($use_ipbans=="0") {
  print("<p align=\"center\"><b>������� ������������ IP-������� - ����������</b></p>\n");
 }
  
if (mysql_num_rows($res) == 0)
  print("<p align=\"center\"><b>������ �� ������� � �������</b></p>\n");
else
{
 
  begin_table(1);
  print("<tr><td width=100% class=\"colhead\" colspan=\"7\">��������� IP ������</td></tr>\n");
  print("<tr><td width=18% class=\"colhead\">��������</td>
  <td class=\"colhead\" align=\"center\">������ IP</td>
  <td class=\"colhead\" align=\"center\">��������� IP</td>
  <td class=\"colhead\" align=\"center\">���</td>
  <td class=\"colhead\" align=\"center\">�����������</td>
  <td width=18% class=\"colhead\" align=\"center\">����� ��</td>
  <td width=8% align=\"center\" class=\"colhead\">����� ���</td>
  </tr>\n");

  while ($arr = mysql_fetch_assoc($res))
  {
  	//$r2 = sql_query("SELECT username,class FROM users WHERE id=$arr[addedby]") or sqlerr(__FILE__, __LINE__);
  	//$a2 = mysql_fetch_assoc($r2);
	$arr["first"] = long2ip($arr["first"]);
	$arr["last"] = long2ip($arr["last"]);
	
	if ($arr["bans_time"]=="0000-00-00 00:00:00"){
	$bansuntil = "<i>����� ����� ���</i>";
	}
	else
	$bansuntil = $arr["bans_time"];
	
print("<tr>
<td >".$arr["added"]."</td>
<td align=\"left\">".$arr["first"]."</td>
<td align=\"left\">".$arr["last"]."</td>
<td align=\"left\"><a href=\"userdetails.php?id=".$arr["addedby"]."\">".get_user_class_color($arr["class"],$arr["username"])."</a></td>
<td align=\"left\">".format_comment($arr["comment"])."</td>
<td align=\"center\">$bansuntil</td>
<td align=\"center\"><a href=\"bans.php?remove=".$arr["id"]."\">��</a></td>
</tr>\n");
  }
  end_table();
}

if (get_user_class() >= UC_ADMINISTRATOR)
{
	//print("<table border=1 cellspacing=0 cellpadding=5>\n");
  print("<br />\n");
  print("<form method=\"post\" action=\"bans.php\">\n");
  begin_table();
	print("<tr><td class=\"colhead\" colspan=\"2\">�������� IP ����� (�)</td></tr>");
	print("<tr><td class=\"rowhead\">������ IP</td><td class=\"a\"><input type=\"text\" name=\"first\" size=\"40\"/></td></tr>\n");
	print("<tr><td class=\"rowhead\">��������� IP</td><td class=\"a\"><input type=\"text\" name=\"last\" size=\"40\"/></td></tr>\n");
	print("<tr><td class=\"rowhead\">�����������</td><td class=\"a\"><input type=\"text\" name=\"comment\" size=\"40\"/></td></tr>\n");
	
	print("<tr><td class=\"rowhead\">�������� ��</td><td class=\"a\">
	
    <select name=banslength>
    <option value=0>�������������� �����</option>
    <option value=1>01 ������</option>
    <option value=2>02 ������</option>
    <option value=3>03 ������</option>
    <option value=4>04 ������</option>
    <option value=5>05 ������</option>
    <option value=6>06 ������</option>
    <option value=7>07 ������</option>
    <option value=8>08 ������</option>
    <option value=9>09 ������</option>
    <option value=10>10 ������</option>
    <option value=11>11 ������</option>
    <option value=12>12 ������</option>
    </select>
	
	
	</td></tr>\n");
	
	print("<tr><td class=\"rowhead\">��������� �����</td><td class=\"a\">".get_date_time()." +- ���� �����</td></tr>\n");
	
	print("<tr><td class=\"b\" align=\"center\" colspan=\"2\"><input type=\"submit\" value=\"��������\" class=\"btn\"/></td></tr>\n");
	end_table();
	print("</form>\n");
}

stdfoot();

?>