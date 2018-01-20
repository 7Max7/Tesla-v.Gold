<? 
require_once("include/bittorrent.php");
gzip();
dbconn(false);
setlocale(LC_ALL, 'ru_RU.CP1251');
///loggedinorreturn();
 
parse_referer("empty");

//if (get_user_class() < UC_USER)
//	stderr($tracker_lang['error'], $tracker_lang['access_denied']);

parked();

stdheadchat("Торренты залитые сегодня");



if (!empty($_GET['sort']) && !empty($_GET['type'])) {

$column = '';
$ascdesc = '';

switch($_GET['sort']) {
case '1': $column = "name"; break;
case '2': $column = "numfiles"; break;
case '3': $column = "comments"; break;
case '4': $column = "added"; break;
case '5': $column = "size"; break;
case '6': $column = "category"; break;
case '7': $column = "seeders"; break;
case '8': $column = "leechers"; break;
case '9': $column = "owner"; break;
case '10': $column = "moderated"; break;
case '11': $column = "moderatordate"; break; // времени проверки
case '12': $column = "multi_time"; break; // времени мультитрекера
default: $column = "id"; break;
}

switch($_GET['type']) {
case 'asc': $ascdesc = "ASC"; $linkascdesc = "asc"; break;
case 'desc': $ascdesc = "DESC"; $linkascdesc = "desc"; break;
default: $ascdesc = "DESC"; $linkascdesc = "desc"; break;
}

$orderby = "ORDER BY ".($column=="seeders" ? " (torrents.seeders+torrents.f_seeders) ":"".($column=="leechers" ? "(torrents.leechers+torrents.f_leechers) ":"torrents." . $column . "")."")." " . $ascdesc;
$pagerlink = "sort=" . intval($_GET['sort']) . "&type=" . $linkascdesc . "&";
} else {
$orderby = "ORDER BY torrents.added DESC";
//$orderby = "ORDER BY torrents.sticky ASC, torrents.added DESC";
$pagerlink = "";
}

$addparam = "";

$timeday = date("Y-m-d 00:00:00");

$wherea = array();
$wherea[] = "torrents.added >= '$timeday'"; /// нам нужны торренты за сегодня


if ($CURUSER["notifs"]) {
$cats = genrelist();
$wherecatina = array();
$all = True;
foreach ($cats as $cat) {
$all &= $cat["id"];
if (strpos($CURUSER["notifs"], "[cat" . $cat["id"] . "]") !== False) {
$wherecatina[] = $cat["id"];
$addparam.= "c$cat[id]=1&amp;";
}
}
}

if (count($wherecatina) > 1)
$wherecatin = implode(",",$wherecatina);
elseif (count($wherecatina) == 1)
$wherea[] = "torrents.category = ".$wherecatina[0];


$where = implode(" AND ", $wherea);

if (!empty($where))
$where = "WHERE $where";

$res = sql_query("SELECT COUNT(*) FROM torrents $where") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$count = $row[0];
$num_torrents = $count;


$torrentsperpage = $CURUSER["torrentsperpage"];
if (empty($torrentsperpage) || $torrentsperpage>150)
$torrentsperpage = 25;


if ($count) {
    if (!empty($addparam)) {
 if (!empty($pagerlink)) {
  if ($addparam{strlen($addparam)-1} != ";") { // & = &amp;
    $addparam = $addparam . "&" . $pagerlink;
  } else {
    $addparam = $addparam . $pagerlink;
  }
 }
    } else {
 $addparam = $pagerlink;
    }
    
    
// 0.085834 - весь
// 0.074816 - без кат


// 0.052522 - без слеж и заклад


///categories.name AS cat_name, categories.image AS cat_pic, 
    ///torrents.times_completed, torrents.filename,
define('ADDREFLINK', "browseday.php?".$addparam);

list($pagertop, $pagerbottom, $limit) = pager($torrentsperpage, $count, "browseday.php?" . $addparam);


$res = sql_query("SELECT torrents.id, torrents.moderated, torrents.multitracker, torrents.moderatedby, torrents.moderatordate, torrents.viponly, torrents.category, torrents.tags, (torrents.leechers+torrents.f_leechers) AS leechers, (torrents.seeders+torrents.f_seeders) AS seeders, torrents.free,torrents.banned, torrents.banned_reason, torrents.name, torrents.size, torrents.added, torrents.comments, torrents.numfiles,  torrents.sticky, torrents.owner, b.class AS classname, b.username AS classusername, users.username, users.class FROM torrents
LEFT JOIN users ON torrents.owner = users.id
LEFT JOIN users AS b ON torrents.moderatedby = b.id
$where $orderby $limit") or sqlerr(__FILE__,__LINE__);

}



?>

<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<div id="loading-layer" style="display:none;font-family: Verdana;font-size: 11px;width:200px;height:50px;background:#FFF;padding:10px;text-align:center;border:1px solid #000">
     <div style="font-weight:bold" id="loading-layer-text">Загрузка. Пожалуйста, подождите...</div><br />
     <img src="pic/loading.gif" border="0" />
</div>
<?

echo '<table class="embedded" cellspacing="0" cellpadding="5" width="100%">
<tr><td class="colhead" align="center" colspan="12"><a href="browse.php" class="altlink_white">Список всех торрентов</a> или к <a href="browseday.php" class="altlink_white">списку торрентов за сегодня</a>.</td></tr>
<tr><td colspan="12">
<table class="embedded" align="center">';

echo '<tr><td class="a" align="center">Общая численность всех залитых сегодня торрентов - '.number_format($count).' штук (a).</td></tr>';

if ($CURUSER["notifs"])
echo '<tr><td class="b" align="center">Внимание: Включен фильтр категорий для показа (см <a href="my.php#notif">файл my.php</a>).</td></tr>';

echo '</table>';

print("</td></tr>");



if ($num_torrents) {

echo "<tr><td class=\"index\" colspan=\"12\">";
echo $pagertop;
echo "</td></tr>";
torrenttable($res, "index");
echo "<tr><td class=\"index\" colspan=\"12\">";
echo $pagerbottom;
echo "</td></tr>";

}
else

echo "<tr><td align=\"center\" class=\"b\" colspan=\"12\">".$tracker_lang['nothing_found']." за сегодня. <br>Время считывания: ".$timeday."</td></tr>\n";


echo "</table></table>";

stdfootchat(); 

?>