<?

require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();

if (get_user_class() < UC_ADMINISTRATOR)
 {
attacks_log('sqlerror'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

accessadministration(); 

if ($_POST["prune"] && get_user_class() == UC_SYSOP){
@unlink(ROOT_PATH."cache/sqlerror.txt");
@header("Location: $DEFAULTBASEURL/sqlerror.php") or die("��������������� �� ��� �� ��������.<script>setTimeout('document.location.href=\"$DEFAULTBASEURL/sqlerror.php\"', 10);</script>");
}


stdheadchat("SQL ������");
print "<center><a href=\"hacklog.php\">XSS ������� (ctracker)</a> : <a href=\"info_cache.php\">������� ��������� (info)</a> : <a href=\"error_torrent.php\">������ ��� �������</a></center><br>";


if(!file_exists(ROOT_PATH."cache/sqlerror.txt"))
{die("����� SQL ������ ��� � ������, ������ ������.");
}


echo"<table width=100% border=0 cellspacing=0 cellpadding=5 align=center>
<td class=colhead align=left>�</td>
<td class=colhead align=center>�������� ������</td></tr>";


$fop = fopen (ROOT_PATH."cache/sqlerror.txt", "r+");
$line_num=0;
while (!feof($fop))
{
$read = fgets($fop, 100000);

list($hua,$even) = explode('#',$read);


if (!empty($hua)){

	$events = explode("||",$even);
	$get = nl2br(htmlspecialchars(print_r(unserialize($events[0]),true)));
	$post = nl2br(htmlspecialchars(print_r(unserialize($events[1]),true)));	


echo"<tr bgcolor=".($line_num % 2 == 0 ? "lightgray" : "white").">
<td class=a align=left>#<b>{$line_num}</b></td>
<td  class=b  align=left>" . htmlspecialchars($hua).(!empty($get)?"<br><b>GET</b>: ".$get:"")." ".(!empty($post)?"<br><b>POST</b>: ".$post:"")."</td>
</tr>";
++$line_num;
unset($get,$post);
}
}

echo"</table>";



if ($line_num<>0){
echo"<hr>
<form method=\"post\" action=\"sqlerror.php\" name=\"add\">
<input type=\"submit\" name=\"Submit\" value=\"�������� ����������\">
<input type=\"hidden\" name=\"prune\" value=\"prune\">
</form>";
}
stdfootchat();
?>