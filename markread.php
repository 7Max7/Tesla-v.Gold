<? require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();
ob_start();

stdheadchat();

if (sql_query("UPDATE users SET last_checked = " .sqlesc(get_date_time()). " WHERE id = " .sqlesc($CURUSER["id"])) && @setcookie("markview", ""))
   stdmsg("�������", "����� �������� �������� ��� ����������.");
else
   stdmsg("������", "������� ����� ��������� ��������� � �������: ". sqlerr(__FILE__, __LINE__));

stdheadchat();

header("Refresh: 5; url=browse.php");
?>