<?
require_once("include/bittorrent.php");

dbconn(false);
loggedinorreturn();

stdhead("Список закладок");

$res = sql_query("SELECT COUNT(*) FROM bookmarks WHERE userid = ".sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
$count = $row[0];

$from=getenv("HTTP_REFERER"); 
$host=getenv("REQUEST_URI"); 

if (empty($count)) {
	stderr("Данные пусты", "У вас нет закладок<br>
	<p>На <a href=\"index.php\">главную</a> или же <a href=\"$from\">вернутся откуда пришли</a>?</p>
	");

}

else {
?>
<table class="embedded" cellspacing="0" cellpadding="5" width="100%">
<tr><td class="colhead" align="center" colspan="12">Список закладок [<a class="altlink_white" href="checkcomm.php">Список слежений</a>]</td></tr>
<?
if (!empty($_GET['sort']) && !empty($_GET['type'])) {

$column = '';
$ascdesc = '';

switch($_GET['sort']) {
case '1': $column = "name"; break;
case '2': $column = "numfiles"; break;
case '3': $column = "comments"; break;
case '4': $column = "added"; break;
case '5': $column = "size"; break;
//case '6': $column = "times_completed"; break;
case '7': $column = "seeders"; break;
case '8': $column = "leechers"; break;
case '9': $column = "owner"; break;
case '10': $column = "moderatedby"; break;
default: $column = "id"; break;
}

switch($_GET['type']) {
case 'asc': $ascdesc = "ASC"; $linkascdesc = "asc"; break;
case 'desc': $ascdesc = "DESC"; $linkascdesc = "desc"; break;
default: $ascdesc = "DESC"; $linkascdesc = "desc"; break;
}

$orderby = "ORDER BY 
".($column=="seeders" ? " (torrents.seeders+torrents.f_seeders) ":"
".($column=="leechers" ? "(torrents.leechers+torrents.f_leechers) ":"torrents." . $column . "")."
")." " . $ascdesc;

$pagerlink = "sort=" . intval($_GET['sort']) . "&type=" . $linkascdesc . "&";
} else {
$orderby = "ORDER BY torrents.added DESC";//torrents.sticky ASC, 
$pagerlink = "";
}

$perpage = 25;

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "bookmarks.php?");
///  0.002014   
//   0.001783  
$res = sql_query("SELECT bookmarks.id AS bookmarkid, bookmarks.mytags, users.username, users.class, users.id AS owner, torrents.id, torrents.name, torrents.type, torrents.comments, torrents.tags,(torrents.leechers+torrents.f_leechers) AS leechers, (torrents.seeders+torrents.f_seeders) AS seeders, IF(torrents.numratings < 1, NULL, ROUND(torrents.ratingsum / torrents.numratings)) AS rating, torrents.numfiles, torrents.added, torrents.size, torrents.views, torrents.visible, torrents.free, torrents.hits, torrents.times_completed, torrents.category 
FROM bookmarks 
INNER JOIN torrents ON bookmarks.torrentid = torrents.id 
LEFT JOIN users ON torrents.owner = users.id 
WHERE bookmarks.userid = ".sqlesc($CURUSER["id"])." ".$orderby." ".$limit) or sqlerr(__FILE__, __LINE__);

print("<tr><td class=\"index\" colspan=\"12\">");
print($pagertop);
print("</td></tr>");
torrenttable($res, "bookmarks");
print("<tr><td class=\"index\" colspan=\"12\">");
print($pagerbottom);
print("</td></tr>");
print("</table>");
}

stdfoot();

?>