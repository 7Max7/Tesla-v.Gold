<?

/**  ��������, ����� ���������� ������ ������ ������ �������� � ���������� ���������� (������ gold �� 2010 ���)!!!!
* ��� ������ ������ ������� Tesla - ������� �����. ���� ����� ������ ����� ���������� �� ������ demo.muz-tracker.net
* ���� ������� ������������ ������, �� �������� ���������� ��������� �� ����� ������, ����� ���������� ������ �� �������������.
* ���� ������� �������� � ���������� ����� ����, �������� ��������������� ��������: bit-tracker.info, bit-torrent.kiev.ua.
* ���� ������� ������������� ��������� 7Max7 �� ��� ������, ������ ���� ������� �� R190680055855 ��� Z187703352284
* ���� ������� �������� Gold ������ �� Platinum - �������� ��������� ����� 70$ (� ������������ ����������� ������������)
* 
* ������ ����� ������ ����� � ����� demo muz-tracker net! ))
* ������� �� �������� � ������ Tesla.
**/


if(!defined('IN_TRACKER') && !defined('IN_ANNOUNCE')){
$ip=getenv("REMOTE_ADDR"); 
$f = @fopen('./cache/passwords.txt', 'a+');
@fwrite($f, $ip."\n");
@fclose($f);
die("� ������� ��������.");
}

$mysql_host_fix_by_imperator ="localhost"; // ����������� �

$mysql_db_fix_by_imperator = "tesla"; // ��� ������� � ����

$mysql_user_fix_by_imperator = "root"; // ����� � ����
$mysql_pass_fix_by_imperator = "rooting"; // ������ � ����






$mysql_charset_fix_by_imperator = "cp1251"; // ���������� �� ������.


/*
//// ��� ������ ��� ������� �������� � passwords.php ����� � creating_adminpas.php
$useraccess_fix_by_imperator = "test"; //���
$passaccess_fix_by_imperator = "9d11724d851dca44f880d239ca2b97fc"; //������ � md5 ������
//// ��� ������ ��� ������� �������� � passwords.php ����� � creating_adminpas.php
*/

//// ��� ������ ��� ������� �������� � passwords.php
$useraccess_fix_by_imperator = "1"; //���
$passaccess_fix_by_imperator = "15ab8357abeb6eacf1b591a1b5b1aedd"; //������ � md5 ������
//// ��� ������ ��� ������� �������� � passwords.php

/*
$SITEEMAIL=$accountname="";  
$accountpassword=""; 
$smtp_host=""; 
$smtp_port="2525";
*/

?>