<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
if (get_user_class() < UC_MODERATOR) stderr($tracker_lang['error'], "Permission denied");

global $use_ipbans;

if ($use_ipbans=="0") {
  stderr("������ ������", "������� ������������ IP-������� - ����������."); }

  
function validip2($ip) {
	if (!empty($ip))
	{
				$reserved_ips = array (
				array('0.0.0.0','255.255.255.255')
		);

		foreach ($reserved_ips as $r)
		{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
	$ip = $_POST["ip"];
else
	$ip = $_GET["ip"];
if (validip2($ip))
{
	$nip = ip2long($ip);
	if ($nip == -1)
	  stderr($tracker_lang['error'], "Bad IP.");
	$res = sql_query("SELECT * FROM bans FORCE INDEX(first_last) WHERE $nip >= first AND $nip <= last") or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) == 0)
	  stderr("���������", "IP ����� <b>$ip</b> �� �������.");
	else
	{
	  $banstable = "<table class=main border=0 cellspacing=0 cellpadding=5>\n" .
	    "<tr><td class=colhead>������</td><td class=colhead>���������</td><td class=colhead>�����������</td></tr>\n";
	  while ($arr = mysql_fetch_assoc($res))
	  {
	    $first = long2ip($arr["first"]);
	    $last = long2ip($arr["last"]);
	    $comment = htmlspecialchars($arr["comment"]);
	    $banstable .= "<tr><td>$first</td><td>$last</td><td>$comment</td></tr>\n";
	  }
	  $banstable .= "</table>\n";
	  stderr("���������", "<table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded style='padding-right: 5px'><img src=pic/smilies/excl.gif></td><td class=embedded>IP ����� <b>$ip</b> �������:</td></tr></table><p>$banstable</p>");
	}
}


stdhead("�������� ����������� IP");

?>
<h1>��������� ���������� IP �����</h1>
<form method=post action=testip.php>
<table border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead>IP �����</td><td><input type=text name=ip></td></tr>
<tr><td colspan=2 align=center><input type=submit class=btn value='OK'></td></tr>
</form>
</table>

<?
stdfoot();
?>