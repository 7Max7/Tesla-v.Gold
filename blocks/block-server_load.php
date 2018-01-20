<?php

if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}

$cacheStatFile = "cache/block-server_load.txt"; 
$expire = 5*60; // 5 мин
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{


global $tracker_lang;


$blocktitle = $tracker_lang['server_load'];



function get_server_load_b() {
	global $phpver;
	if (strtolower(substr(PHP_OS, 0, 3)) === 'win') {
		return 0;
	} elseif (@file_exists("/proc/loadavg")) {
		$load = @file_get_contents("/proc/loadavg");
		$serverload = explode(" ", $load);
		$serverload[0] = round($serverload[0], 4);
		if(!$serverload) {
			$load = @exec("uptime");
			$load = @split("load averages?: ", $load);
			$serverload = explode(",", $load[1]);
		}
	} else {
		$load = @exec("uptime");
		$load = @split("load averages?: ", $load);
		$serverload = explode(",", $load[1]);
	}
	$returnload = trim($serverload[0]);
	if(!$returnload) {
		$returnload = "Неизвестно";
	}
	return $returnload;
}






$now = time();
$dttime = get_date_time();
$res = sql_query("SELECT arg,value_i,value_u FROM avps WHERE arg = 'load_connected' OR arg = 'load_peers' OR arg = 'load_guest'") or sqlerr(__FILE__,__LINE__);
while ($row = mysql_fetch_array($res)){
$arra[$row["arg"]]["value_u"]=$row["value_u"];
$arra[$row["arg"]]["value_i"]=$row["value_i"];
}




$peers = get_row_count("peers"); 

if ($arra["load_peers"]["value_i"]<$peers) {

sql_query("UPDATE avps SET value_u='$now',value_i='$peers', value_s='$dttime' WHERE arg='load_peers'");

if (mysql_modified_rows()==0)
sql_query("INSERT INTO avps (arg, value_u,value_i,value_s) VALUES ('load_peers',$now,'$peers','$dttime')");

$toppeer = "<br>(Max: <b>".$peers."</b> в ".get_date_time($now).")";
}
elseif(!empty($arra["load_peers"]["value_i"])) {
$toppeer = "<br>(Max: <b>".$arra["load_peers"]["value_i"]."</b> в ".get_date_time($arra["load_peers"]["value_u"]).")";
}


////////////
$connected = mysql_num_rows(sql_query("SELECT userid FROM peers WHERE userid>0 GROUP by userid"));

if ($arra["load_connected"]["value_i"]<$connected) {

sql_query("UPDATE avps SET value_u='$now',value_i='$connected', value_s='$dttime' WHERE arg='load_connected'");

if (mysql_modified_rows()==0)
sql_query("INSERT INTO avps (arg, value_u,value_i,value_s) VALUES ('load_connected',$now,'$connected','$dttime')");

$topconnected = "<br>(Max: <b>".$connected."</b> в ".get_date_time($now).")";
}
elseif(!empty($arra["load_connected"]["value_i"])) {
$topconnected = "<br>(Max: <b>".$arra["load_connected"]["value_i"]."</b> в ".get_date_time($arra["load_connected"]["value_u"]).")";
}

////////////
$connected_guest = mysql_num_rows(sql_query("SELECT userid FROM peers WHERE userid='0'"));

if ($arra["load_guest"]["value_i"]<$connected_guest) {

sql_query("UPDATE avps SET value_u='$now',value_i='$connected_guest', value_s='$dttime' WHERE arg='load_guest'");

if (mysql_modified_rows()==0)
sql_query("INSERT INTO avps (arg, value_u,value_i,value_s) VALUES ('load_guest',$now,'$connected_guest','$dttime')");

$topload_guest = "<br>(Max: <b>".$connected_guest."</b> в ".get_date_time($now).")";
}
elseif(!empty($arra["load_guest"]["value_i"])) {
$topload_guest = "<br>(Max: <b>".$arra["load_guest"]["value_i"]."</b> в ".get_date_time($arra["load_guest"]["value_u"]).")";
}



$avgload = get_server_load_b();
if (strtolower(substr(PHP_OS, 0, 3)) != 'win')
	$percent = $avgload ;
else
	$percent = $avgload;
	
if ($percent <= 50) $pic = "loadbargreen.gif";
elseif ($percent <= 70) $pic = "loadbaryellow.gif";
else $pic = "loadbarred.gif";
	$width = $percent * 4;

$content = "<center>
".($percent<>0 ? "<table class=\"main\" border=\"0\" width=\"402\"><tr><td style=\"padding: 0px; background-repeat: repeat-x\" title=\"Нагрузка: $percent%, Средняя (LA): $avgload\">"
."<img height=\"15\" width=\"$width\" src=\"pic/$pic\" alt=\"Нагрузка: $percent%, Средняя (LA): $avgload\" title=\"Нагрузка: $percent%, Средняя (LA): $avgload\">"
."</td></tr></table><b>Нагрузка: $percent%, Средняя (LA): $avgload</b><br>"."
":"")."
Общее количество подключений: <b>$peers</b> (".($connected+$connected_guest)." сидов) $toppeer<br>
Всего подключено уникальных пользователей: <b>$connected</b> $topconnected<br>
Всего подключено без регистрации гостей: <b>$connected_guest</b> $topload_guest
</center>";

$fp = fopen($cacheStatFile,"w");
if($fp) {
fputs($fp, $content); 
fclose($fp); 
}
}

?>