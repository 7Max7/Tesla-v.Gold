<?
require_once("include/bittorrent.php");

dbconn();
loggedinorreturn();
if (get_user_class() < UC_ADMINISTRATOR) 
 {
attacks_log('docleanup'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

if ($_GET['action'] == "unlink") {

stdhead("������� ��� �����");

cache_clean("a");

stdmsg("������", "������� ���� ��������� �������.");

stdfoot();
die;
}


require_once(ROOT_PATH.'include/cleanup.php');

$s_s = $queries;
docleanup();
$s_e = $queries;

$num=$s_e - $s_s;
$dt = get_date_time(gmtime());
sql_query("UPDATE avps SET value_i='$num',value_s='$dt' WHERE arg='lastcleantime'");

stdhead("������� �������");

if (get_user_class() == UC_SYSOP){
$admin="<br><form method=\"get\" action=\"docleanup.php\">
<input type=\"submit\" name=\"action\" value=\"unlink\"/> ������� ��� �������.
</form>";
}



stdmsg("������", "������� ��������� �������. �� ������� ������������ ".($s_e - $s_s)." ������(��). $admin");

stdfoot();
?>