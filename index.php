<?

if (isset($_GET["info_hash"])){
include_once 'announce.php';
die;
}

require "include/bittorrent.php";
gzip();
dbconn();

if ($CURUSER && $_SERVER["REQUEST_METHOD"] == "POST"){
if ($_POST["gismeteo_id"]){
$gis_ident=(int)$_POST["gismeteo_id"];
$expires = 0x7fffffff;
setcookie("gismeteo", $gis_ident, $expires, "/");
$_COOKIE["gismeteo"]=$gis_ident;
//header("Refresh: 0; url=index.php");
//die("Обновление странички");
}

if ($_POST["gis_reset"]){
$_COOKIE["gismeteo"]="";
setcookie("gismeteo", "", 0x7fffffff, "/");
//header("Refresh: 0; url=index.php");
//die("Обновление странички");
}
}

stdhead($tracker_lang['homepage']);
parse_referer("delcache");
stdfoot();

?>