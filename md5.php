<?php

require_once("include/bittorrent.php");
dbconn(false,true);

/** Для Движка Tesla TT v.Gold версия.
****Скрытая форма передачи каптчи, проверка Антибот системы.****
**/

$ip = getip();
$sid = session_id();
$timeday = date("Y-m-d");

echo md5($ip.$sid.$timeday);

?>