<?php

require_once("include/bittorrent.php");
dbconn(false,true);

/** ��� ������ Tesla TT v.Gold ������.
****������� ����� �������� ������, �������� ������� �������.****
**/

$ip = getip();
$sid = session_id();
$timeday = date("Y-m-d");

echo md5($ip.$sid.$timeday);

?>