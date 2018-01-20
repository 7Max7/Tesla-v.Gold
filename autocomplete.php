<?php
include_once("include/bittorrent.php");
dbconn(false,true);

header("Content-Type: text/html; charset=" .$tracker_lang['language_charset']);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);


$referer = (isset($_SERVER["HTTP_REFERER"]) ? htmlentities($_SERVER["HTTP_REFERER"]):"");
if (!empty($referer))
$parse_site = parse_url($referer, PHP_URL_HOST);

$site_own = (($_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://").htmlspecialchars_uni($_SERVER['HTTP_HOST']);
if (!empty($site_own))
$parse_owner = parse_url($site_own, PHP_URL_HOST);

if ($parse_site<>$parse_owner)
return;

/*
echo $parse_owner;
echo "|";
echo $parse_site;
echo "\n";
*/

$qsearch = htmlspecialchars_uni(utf8_to_win(strip_tags($_GET["q"])));
///htmlspecialchars

if (!$qsearch)
return;


$searchstr = substr($qsearch, 0, 64); /// хотя 64 вполне устраивает
$strl = strlen($searchstr);


if ($strl >= 20)
$desct = "5";
elseif ($strl <= 5)
$desct = "10";
else
$desct = "15";

$q = sqlesc("%".sqlwildcardesc(trim($searchstr))."%");

$res = sql_query("SELECT name, f_seeders, f_leechers FROM torrents WHERE name LIKE {$q} LIMIT $desct") or die;

while ($arr = mysql_fetch_assoc($res)) {
echo htmlspecialchars_uni($arr["name"])."|Пиров: ".$arr["f_seeders"]." Сидов: ".$arr["f_leechers"]."\n";	
}






/*
if (strlen($qsearch) >= 4) {

$qsearch = htmlspecialchars_uni($qsearch);
$qsearch = substr($qsearch, 0, 100); /// 64 вполне устраивает
$qsearch = preg_replace("/\[((\s|.)+?)\]/", "", $qsearch);
$qsearch = preg_replace("/[^\w\x7F-\xFF\s]/", " ", $qsearch);
$qsearch = trim(preg_replace("/\s(\S{1,2})\s/", " ", preg_replace("/\s+\s/", " "," $qsearch ")));
$qsearch = preg_replace("/\s+\s/", " ", $qsearch);

if (!empty($qsearch))
$res = sql_query("SELECT name, f_seeders, f_leechers FROM torrents WHERE MATCH (torrents.name) AGAINST ('" .$qsearch. "') ORDER BY MATCH (torrents.name) AGAINST ('+" .$qsearch. "') DESC LIMIT 15") or sqlerr(__FILE__,__LINE__);
else
die;

} else  {

$searchstr = substr($qsearch, 0, 100); /// хотя 64 вполне устраивает
$q = sqlesc("%".sqlwildcardesc(trim($searchstr))."%");

$res = sql_query("SELECT name, f_seeders, f_leechers FROM torrents WHERE name LIKE {$q} ORDER BY id DESC LIMIT 15") or sqlerr(__FILE__,__LINE__);

}
*/





?>