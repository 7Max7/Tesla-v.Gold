<? 
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn();
stdhead("Мои торренты");


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

")."

 " . $ascdesc;
$pagerlink = "sort=" . intval($_GET['sort']) . "&type=" . $linkascdesc . "&";
} else {
$orderby = "ORDER BY torrents.sticky ASC, torrents.added DESC";
$pagerlink = "";
}

$where = "WHERE owner = " . $CURUSER["id"] . " AND banned <> 'yes'";
$res = sql_query("SELECT COUNT(*) FROM torrents $where") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
$count = $row[0];

if (!$count) {
	stdmsg($tracker_lang['error'], "Вы не загружали торренты на этот трекер.");
	stdfoot();
	die();
}
else {
?>
<table class="embedded" cellspacing="0" cellpadding="3" width="100%">
<tr><td class="colhead" align="center" colspan="12">Мои залитые торренты</td></tr>
<?

	list($pagertop, $pagerbottom, $limit) = pager(20, $count, "mytorrents.php?");

	$res = sql_query("SELECT torrents.type, torrents.comments, (torrents.leechers+torrents.f_leechers) AS leechers, torrents.tags, (torrents.seeders+torrents.f_seeders) AS seeders, IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.id, torrents.name, numfiles, added, size, views, visible, free, hits, times_completed, category FROM torrents 
".$where." ".$orderby." ".$limit) or sqlerr(__FILE__, __LINE__);

	print("<tr><td class=\"index\" colspan=\"12\">");
	print($pagertop);
	print("</td></tr>");

	torrenttable($res, "mytorrents");

	print("<tr><td class=\"index\" colspan=\"12\">");
	print($pagerbottom);
	print("</td></tr>");

	print("</table>");

}

stdfoot();

?>