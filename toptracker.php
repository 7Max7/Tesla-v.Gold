<?php
    // TopTracker.Ru - ������ ��� ������� ��� TBDev
    // ������ ������ ���������� ��� ���������� �������������� ������ �������� �� ������ ������ ������� �� toptracker.ru
    // ��� ������ ��� ����� �������� ���� ������ � ������ �������
    // ����� ������� � ���� mysql ����� ���� � ������� users ��� ����� ���������� ����� ����������� ����� ������, ������ ����
    // ALTER TABLE users ADD `toptracker` date NOT NULL default '0000-00-00';
    // � ������ �� ����������� �������� ID ������ �����, ������ (http://www.toptracker.ru/details.php?id=xxx&userid=xxx) ��� userid ID ������ �����
    // ������ ���� ���� �� ��� ���� ������ ����� ��� �� ������, ������� ���� �������� ����, ���� ��� ������ �������� ��������� ���� ������ �� ��� ������
    // ���� ����� ����� �������� � ������ ��������, ������ � ����� �������� http://www.toptracker.ru/contact.php

	// �������� �� �������
	// ������ ��� ������� ID ������ ������� � ��������� �������� � 36 � 40, � ������� ���� ��� �� ����������� (37,41) ���������� (38,42) ����������� (39,43)
	
	/*
	if( substr($_SERVER['HTTP_REFERER'], 0 , 39) != "http://toptracker.ru/details.php?id=219" && substr($_SERVER['HTTP_REFERER'], 0 , 43) != "http://www.toptracker.ru/details.php?id=219")
		die();
*/


$key=$_POST["key"]; 
$ratio = intval($_POST["ratio"]);
$ip = htmlentities($_POST["ip"]);
$userid = intval($_POST["userid"]);
$ref=htmlentities($_SERVER['HTTP_REFERER']);

$sf ="cache/toptracker.txt"; 
$fpsf=@fopen($sf,"a+"); 

@fputs($fpsf,"key:$key ratio:$ratio ip:$ip userid:$userid ref:$ref\n"); 
@fclose($fpsf); 








    // �������������� �������� �� ����������� �����, ���������� ���� � ������� � ��������� � �������
//	if($_POST["key"] != "0RamuF5dydTtEmNwV447fjZ07EWmAu")
//		die();

	// �������������� � ����������
	$MB = 300;
	$MB = 1024*1024*$MB;

	// ������������� � seedbonus
	$bonus = 5;

    // ��������� �� ������������ ���� ������
	if(isset($_POST['userid']) && isset($_POST["ip"]) && isset($_POST['ratio']) && isset($_POST['key'])) {

		$ratio = intval($_POST["ratio"]);
		$ip = htmlentities($_POST["ip"]);
    	$userid = intval($_POST["userid"]);

    	// ���� ������ �� ��� ������ 5 ��������� ����� ������������, �������� �� ������ ����������
		if($ratio == '5') {

			require_once("include/bittorrent.php");
        	dbconn(false);

        	// � ���� ������� ����������� ����� seedbonus, �������� ���� �� ID � IP �����, (INTERVAL 2 HOUR) ��� �� �������� �� ���������� ����� GMT+1
        	// �� ������ ����� ������� ����� �� ��� ��� ������� � ������� �������, uploaded = uploaded + '".$MB."'
			sql_query("UPDATE users SET toptracker = CURDATE()+INTERVAL 2 HOUR, uploaded = uploaded + '".$MB."' WHERE id = ".sqlesc($userid)." AND ip = ".sqlesc($ip)." AND toptracker < CURDATE()")  or sqlerr(__FILE__,__LINE__);
			
			 
			 if (@mysql_affected_rows()){

			 $msg = sqlesc("�������, ��� ������������� �� ���� (������ 5), ��� ���� ��������� 300 �������� � ������.!\n");
			 $dt = sqlesc(get_date_time(gmtime()));
			 
			 $subject = sqlesc("������ 5 �� TopTracker");
			 
            sql_query("INSERT INTO messages (sender, receiver, added, msg, poster,subject) VALUES(0,$userid, $dt, $msg, 0,$subject)") or sqlerr(__FILE__, __LINE__);
            }
		}
	}

	exit();

?>