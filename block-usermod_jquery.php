<?
require_once 'include/bittorrent.php';

header ("Content-Type: text/html; charset=windows-1251");
dbconn(false,true);

  
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

global $CURUSER;

if (empty($CURUSER) || $CURUSER["class"] < UC_MODERATOR){
//@header("Location: ../index.php");
die;
}


$txlst3 = sql_query("SELECT COUNT(*) FROM torrents WHERE moderated='no'") or sqlerr(__FILE__,__LINE__);
$list3 = mysql_fetch_row($txlst3);
$count3 = $list3[0];  

/*
$pre_sql=new MySQLCache("SELECT COUNT(*)AS nu FROM torrents WHERE moderated='no' AND banned='no'", 5*60, "premod.txt"); // 24 ����

$row_pre=$pre_sql->fetch_assoc();

$count3=(isset($row_pre["nu"])? $row_pre["nu"]:0);
*/


if (!empty($count3)){

echo "<center><form method=\"get\" action=\"premod.php\"><input title=\"����� ��������� ����� $count3 ������� ������\" type=\"submit\" style=\"width: 130px\" class=\"btn\" value=\"��� ��������� $count3\"></form></center>";

//echo "<br>";
}

$txlst3 = sql_query("SELECT COUNT(*) FROM torrents WHERE multitracker = 'yes' AND multi_time='0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);
$list3 = mysql_fetch_row($txlst3);
$count3 = $list3[0];
/*
$pre_sql=new MySQLCache("SELECT COUNT(*)AS nu FROM torrents WHERE multitracker = 'yes' AND multi_time='0000-00-00 00:00:00'", 3*60, "premod_multi.txt"); // 24 ����

$row_pre=$pre_sql->fetch_assoc();

$count3=(isset($row_pre["nu"])? $row_pre["nu"]:0);
*/


if (!empty($count3)){

echo "<center><form method=\"get\" action=\"remote_gets.php\"><input title=\"����� ��������� ������� ���� � $count3 ��������� ����� ����� ����� �� ���������� ������� ������� �������.\" type=\"submit\" style=\"width: 130px\" class=\"btn\" value=\"��� ����� � $count3\"></form></center>";

//echo "<br>";
}


} else @header("Location: ../index.php");

?>