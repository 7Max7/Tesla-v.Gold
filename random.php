<?
require_once("include/bittorrent.php");
dbconn();
//loggedinorreturn();

function bark($msg) {
	stderr("Ошибка", $msg);
}

if (!is_numeric($_GET["id"]))
bark("id торрента не может быть пустым или же равнятся букве или символу, разрешенны только числа");

if ($_GET["option"]<>"random" && $_GET["option"]<>"next" && $_GET["option"]<>"prev")
die;


$id = (int) $_GET["id"];


if ($id && $_GET["option"]=="random") {

////// альтернативный php рандом (при 100k и более в таблице - rand тормозит)
$num = rand(1,10);
$num2 = rand(2,9);

if ($num2%2==0)
$desc = "DESC";
else
$desc = "ASC";

if ($num%2==0) {

if ($num<=3)
$where = "ORDER BY added";
elseif ($num>6)
$where = "ORDER BY moderated";
else
$where = "ORDER BY multi_time";

} else {

if ($num>=8)
$where = "ORDER BY info_hash";
elseif ($num<=3)
$where = "ORDER BY owner";
else
$where = "ORDER BY moderatedby";

}

$rse = sql_query("SELECT id FROM torrents ".$where." ".$desc." LIMIT ".rand(60,400)) or sqlerr(__FILE__,__LINE__);
while ($arrseed = mysql_fetch_array($rse))
$updateset[] = $arrseed["id"];

$random_query = sql_query("SELECT id AS random FROM torrents WHERE id IN (".implode(",", $updateset).") ORDER BY rand() LIMIT 1") or sqlerr(__FILE__, __LINE__); 
$random = mysql_fetch_array($random_query);
$next_prev=$random["random"];
////// альтернативный php рандом (при 100k и более в таблице - rand тормозит)

/*
$random_query = sql_query("SELECT id AS random FROM torrents ORDER BY rand() DESC LIMIT 1") or sqlerr(__FILE__, __LINE__); 
$random = mysql_fetch_array($random_query);
$next_prev=$random["random"];
*/

}

if ($id && $_GET["option"]=="next") {
	$next_query = sql_query("SELECT MIN(id) AS nextid FROM torrents WHERE id > ".sqlesc($id)." LIMIT 1") or sqlerr(__FILE__, __LINE__); 
    $next = mysql_fetch_array($next_query);
	$next_prev=$next["nextid"];
}

if ($id && $_GET["option"]=="prev") {
$pre_query = sql_query("SELECT MAX(id) AS preid FROM torrents WHERE id < ".sqlesc($id)." LIMIT 1") or sqlerr(__FILE__, __LINE__); 
    $next = mysql_fetch_array($pre_query); 
    $next_prev=$next["preid"];
}
///random.php?id=182?option=next

if (empty($next_prev) || $next_prev==$id) {

$pre_query = sql_query("SELECT id FROM torrents ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__, __LINE__); 
$next = mysql_fetch_array($pre_query); 
$next_prev=$next["id"];	

if ($next_prev==$id){
$pre_query = sql_query("SELECT id FROM torrents ORDER BY added ASC LIMIT 1") or sqlerr(__FILE__, __LINE__); 
$next = mysql_fetch_array($pre_query); 
$next_prev=$next["id"];	
}

}


header("Location: details.php?id=$next_prev");
?>