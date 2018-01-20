<? 
require_once("include/bittorrent.php");
gzip();
dbconn(false);

///loggedinorreturn();
 
parse_referer("empty");

//if (get_user_class() < UC_USER)
//	stderr($tracker_lang['error'], $tracker_lang['access_denied']);
parked();


global $DEFAULTBASEURL, $CURUSER, $SITENAME, $tracker_lang;

$file=basename($_SERVER['SCRIPT_FILENAME']);

$torrentsperpage = $CURUSER["torrentsperpage"];
if (empty($torrentsperpage) || $torrentsperpage>150)
$torrentsperpage = 25;

stdheadchat("Активные релизы в сети");

$mark=0;

if ($CURUSER){
$checkin=array(); $book=array();
$res_t1 = sql_query("SELECT checkid FROM checkcomm WHERE userid = ".sqlesc($CURUSER["id"])." AND torrent='1' AND offer='0'") or sqlerr(__FILE__, __LINE__);

while ($arr_c1 = mysql_fetch_assoc($res_t1)){
$checkin[$arr_c1["checkid"]] = 1;
}
/// работает нижнее
$res_t2 = sql_query("SELECT torrentid FROM bookmarks WHERE userid = ".sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);

while ($arr_c2 = mysql_fetch_assoc($res_t2)){
$book[$arr_c2["torrentid"]] = 1;
}
}


echo '<table class="embedded" cellspacing="0" cellpadding="5" width="100%">
<tr><td class="colhead" align="center" colspan="12">Активныe релизы в сети <br> Время загрузки данной страницы зависит от количества релизов (их подсчета и сортировки)</td></tr>';

print("<tr><td class=\"a\" colspan=\"12\" align=\"center\">");

echo "<div class=\"spoiler-wrap\" id=\"5700\"><div class=\"spoiler-head folded clickable\">Быстрый выбор категорий</div><div class=\"spoiler-body\" style=\"display: none;\">";

echo '<table class="embedded" cellspacing="0" cellpadding="5" width="100%">';

$cats = genrelist();
$i = 0;
foreach ($cats as $cat) {
$catsperrow = 5;
print(($i && $i % $catsperrow == 0) ? "</tr><tr>" : "");
print("<td class=\"bottom\" style=\"padding-bottom: 2px;padding-left: 7px\">
<a class=\"catlink\" title=\"" . htmlspecialchars($cat["name"]) . " - $cat[num_torrent] торрента(ов)\" href=\"#$cat[id]\">" . htmlspecialchars($cat["name"]) . "</a> (".$cat["num_torrent"].")
</td>\n");
++$i;
}
echo '</table>';

echo "</div></div>";
echo "</td></tr>";
echo '</table>';

?>
<style>.effect {FILTER: alpha(opacity=60); -moz-opacity: .60; opacity: .60;}</style>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript">
function getDetails(tid, bu, picid, formid) {

var det = document.getElementById('details_'+tid);
var pic = document.getElementById(picid);
var form = document.getElementById(formid);

if(!det.innerHTML) {
var ajax = new tbdev_ajax();
ajax.onShow ('');
var varsString = "";
ajax.requestFile = "<?=$DEFAULTBASEURL; ?>/gettorrentdetails.php";
ajax.setVar("tid", tid);
ajax.method = 'POST';
ajax.element = 'details_'+tid;
ajax.sendAJAX(varsString); 
pic.src = bu + "/pic/tabs/end_active.gif"; form.value = "minus"; 
} else  
det.innerHTML = '';
pic.src = bu + "/pic/tabs/end.gif"; form.value = "plus"; 
}
</script>


<? if (!empty($CURUSER)){ ?>

<script type="text/javascript">
function bookmark(id, type, page) {
var loading = "";
var id = id; var type = type;
jQuery("#loading").html(loading);
$.post('<?=$DEFAULTBASEURL; ?>/bookmark.php',{'id':id , 'type':type , 'page':page},

function(response) {
$('#bookmark_'+id).html(response);
jQuery("#loading").empty();
}, 'html');
}

function checmark(id, type, page,twopage) {
var loading = "";
var id = id; var type = type;
jQuery("#loading").html(loading);
$.post('<?=$DEFAULTBASEURL; ?>/bookmark.php',{'id':id , 'type':type , 'page':page, 'twopage':twopage},

function(response) {
$('#checmark_'+id).html(response);
jQuery("#loading").empty();
}, 'html');
}
</script>
<? } ?>

<div id="loading-layer" style="display:none;font-family: Verdana;font-size: 11px;width:200px;height:50px;background:#FFF;padding:10px;text-align:center;border:1px solid #000">
     <div style="font-weight:bold" id="loading-layer-text">Загрузка данных. Пожалуйста, подождите...</div><br />
     <img src="pic/loading.gif" border="0" />
</div>

<?

echo '<table class="embedded" cellspacing="0" cellpadding="5" width="100%">';

print("<tr>\n");

echo "<td class=\"colhead\" align=\"center\">".$tracker_lang["type"]."</td>";

echo "<td class=\"colhead\" align=\"left\">".$tracker_lang["name"]."</td>";

echo "<td class=\"colhead\" align=\"center\"><img src=\"pic/browse/nimberfiles.gif\" border=\"0\"></td>";

echo "<td class=\"colhead\" align=\"center\"><img src=\"pic/browse/comments.gif\" border=\"0\"></td>";

echo "<td class=\"colhead\" align=\"center\"><img src=\"pic/browse/size_file.gif\" border=\"0\"></td>";

echo "<td class=\"colhead\" align=\"center\"><img src=\"pic/browse/up.gif\" border=\"0\"> <img src=\"pic/browse/down.gif\" border=\"0\"></td>";

echo("<td class=\"colhead\" align=\"center\">".$tracker_lang['uploadeder']."</td>\n");

echo("</tr>\n");



/*
$res_cat = sql_query("SELECT c.id as catid,c.image as catimage,c.name as catname, MAX(t.added) AS last, COUNT(DISTINCT t.id) AS n_t
FROM categories as c 
LEFT JOIN torrents as t ON t.category = c.id 

GROUP BY c.id ORDER BY last DESC") or sqlerr(__FILE__, __LINE__);
*/
//while ($cat = mysql_fetch_array($res_cat)) {

/*
$res_cat=new MySQLCache("SELECT c.id as catid,c.image as catimage,c.name as catname, MAX(t.added) AS last, COUNT(DISTINCT t.id) AS n_t
FROM categories as c 
LEFT JOIN torrents as t ON t.category = c.id 
GROUP BY c.id ORDER BY last DESC", 6*7200, "browselight.txt"); // 6 часов

while ($cat=$res_cat->fetch_assoc()){
*/

$res_cat=new MySQLCache("SELECT id AS catid, name AS catname, image AS catimage 
FROM categories 
WHERE (SELECT id FROM torrents WHERE added > DATE_SUB(NOW(), INTERVAL 31 DAY) AND category=categories.id AND torrents.moderated='yes' LIMIT 1) IS NOT NULL ORDER BY catname DESC", 6*7200,"browselight.txt"); // 6 часов 
while ($cat=$res_cat->fetch_assoc()){

print("<tr><td class=colhead colspan=\"12\"><b>Категория</b>: " . $cat['catname']. " <a name=\"".$cat['catid']."\"></a></td>");


$res = sql_query("SELECT torrents.id, torrents.multitracker, torrents.viponly, (torrents.f_leechers) AS leechers, (torrents.f_seeders) AS seeders, torrents.free, torrents.name, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.sticky, torrents.owner, users.username, users.class 
FROM torrents
LEFT JOIN users ON torrents.owner = users.id
WHERE torrents.category=".sqlesc($cat["catid"])." AND torrents.moderated='yes' ORDER BY seeders DESC LIMIT 10") or sqlerr(__FILE__,__LINE__);

while ($row = mysql_fetch_assoc($res)) {

$id = $row["id"];
echo("<tr".($row["sticky"] == "yes" ? " class=\"highlight\"" : "").">\n");

echo("<tr>");

echo("<td align=\"center\" class=\"b\" rowspan=2 width=2% style=\"padding: 5px\">");  

if (!empty($cat["catimage"])){
	
echo("<img border=\"0\" class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" src=\"".$DEFAULTBASEURL."/pic/cats/".$cat["catimage"]."\" alt=\"" . $cat["catname"] . "\" />");	
}
else
echo($cat["catname"]);

echo("</td>\n");


$dispname = htmlspecialchars_uni($row['name']);

echo "<td colspan=\"12\" align=\"left\">";


echo "<a href=\"details.php?id=".$id."\"><div title=\"Просмотреть $dispname\" align=\"left\" valign=\"down\" style=\"font-weight: bold;font-family:Trebushet MS, Verdana, Tahoma, sans serif;margin-top:12px;padding:2px 5px;-moz-border-radius-bottomright:7px;-moz-border-radius-topright:7px;padding:2px 10px;background:#E1E1E1\">";

echo ((isset($row["sticky"]) && $row["sticky"] == "yes") ? "<b>Важный</b>: " : "")."".$dispname."\n";
 
echo "</div></a>";

if ($CURUSER){

//if (get_user_class() <= UC_MODERATOR)
//echo("<a href=\"download.php?id=$id\"><img src=\"".$DEFAULTBASEURL."/pic/download.gif\" border=\"0\" alt=\"".$tracker_lang['download']."\" title=\"".$tracker_lang['download']."\"></a>\n");
//else 
echo "<a href=\"download.php?id=$id\"><div title=\"Скачать $dispname\" align=\"right\" valign=\"down\" style=\"font-family:Trebushet MS, Verdana, Tahoma, sans serif;font-style:oblique;margin-top:12px;padding:2px 5px;-moz-border-radius-bottomleft:7px;-moz-border-radius-topleft:7px;padding:2px 10px;background:#D2D2D2\">$dispname</div></a>";//-moz-border-radius-topleft:7px;

}


echo("</td></tr><tr>");
echo("<td class=\"row2\" align=\"left\">");  

echo "<img style=\"cursor:pointer;\" src=\"".$DEFAULTBASEURL."/pic/tabs/end.gif\" id=\"warnpic$id\" onClick=\"getDetails('$id','$DEFAULTBASEURL','warnpic$id','$id')\" alt=\"Предпросмотр описания\" title=\"Предпросмотр описания\"/> ";

if ($row["free"]=="yes")
echo "<img src=\"".$DEFAULTBASEURL."/pic/freedownload.gif\" title=\"".$tracker_lang['golden']."\" alt=\"".$tracker_lang['golden']."\">";	

if (!empty($row["viponly"]))
echo "<img  border=\"0\" width=\"18px\" alt=\"Данная раздача только для VIP пользователей\" title=\"Данная раздача только для VIP пользователей\" src=\"".$DEFAULTBASEURL."/pic/vipbig.gif\">";


if ($CURUSER){

if (empty($checkin[$id]))
echo "<span style=\"cursor: pointer;\" id=\"checmark_".$row['id']."\"><a onclick=\"checmark('$id', 'add' , 'check', 'browse');\"><img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/head2_2.gif\" alt=\"Включить слежение\" title=\"Добавить в слежения\" /></a> <span id=\"loading\"></span></span>";
else
echo "<span style=\"cursor: pointer;\" id=\"checmark_".$row['id']."\"><a onclick=\"checmark('$id', 'del' , 'check', 'browse');\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" border=\"0\" src=\"".$DEFAULTBASEURL."/pic/head2_2.gif\" alt=\"Отключить слежение\" title=\"Убрать с закладок\" /></a>  <span id=\"loading\"></span></span>";
 

if(empty($book[$id]))
echo " <span style=\"cursor: pointer;\" id=\"bookmark_".$row['id']."\"><a onclick=\"bookmark('$id', 'add' , 'browse');\"><img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/bookmark.gif\" alt=\"Добавить в закладки\" title=\"Добавить в закладки\" /></a>   <span id=\"loading\"></span></span>";
else
echo " <span style=\"cursor: pointer;\" id=\"bookmark_".$row['id']."\"><a onclick=\"bookmark('$id', 'del' , 'browse');\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" border=\"0\" src=\"".$DEFAULTBASEURL."/pic/bookmark.gif\" alt=\"Убрать с закладок\" title=\"Убрать с закладок\" /></a>  <span id=\"loading\"></span></span>";

}

if ((isset($row["owner"]) && $CURUSER["id"] == $row["owner"]) || get_user_class() >= UC_MODERATOR)
$owned = 1;
else
$owned = 0;

if ($owned)
echo("<a href=\"edit.php?id=$row[id]\"><img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/pen.gif\" alt=\"".$tracker_lang['edit']."\" title=\"".$tracker_lang['edit']."\" /></a>\n");


if (checknewnorrent($row["id"], $row["added"]) && $CURUSER) {
echo "<b><font color=\"red\" size=\"1\">[новый]</font></b> ";
++$mark;
}


/*
if ($row["tags"]){
$tags[$row["id"]]="";
foreach(explode(",", $row["tags"]) as $tag) {
	
if (!empty($tags[$row["id"]]))
$tags[$row["id"]].=", ";

$tags[$row["id"]].= "<a style=\"font-weight:normal;\" href=\"browse.php?tag=".urlencode($tag)."&incldead=1\">".tolower($tag)."</a>";
}
$tags[$row["id"]]=$tags[$row["id"]];
}
else
$tags[$row["id"]]="не выбраны";


if (!empty($tags[$row["id"]]))
echo("<b>Теги</b>: ".$tags[$row["id"]]." ".(strlen($tags[$row["id"]])>200 ? "&nbsp; ":"")."");//<br>
*/
//print("<b>[</b>".normaltime($row["added"],true)."<b>]</b>");


//else 
//{

//print("<b>Добавлен</b>: ".timesec($row["added"])."");

/*
if ($variant <> "mytorrents" && $variant <> "bookmarks" && $CURUSER ) {
echo "<br>";
echo pic_rating_b(10,$row["rating"]);
}
*/

//}


print("</td>\n");


echo("<td class=\"row2\" align=\"center\">".$row["numfiles"]."</td>\n");

	
echo("<td class=\"a\" align=\"center\"><b><a href=\"details.php?id=$id&page=last#startcomments\">" . $row["comments"] . "</a></b></td>\n");

echo ("<td ".($_GET["sort"]=="5"? "class=\"b\"":"class=\"row2\"")." align=\"center\">".mksize($row["size"])."</td>\n");

echo ("<td class=\"a\" align=\"center\">");       
echo ("<b><font color=\"".linkcolor($row["seeders"])."\">".($row["seeders"])."</font></b>\n");
echo (" | ");
echo ("<b><font color=\"".linkcolor(number_format($row["leechers"])). "\">" .number_format($row["leechers"]). "</font></b>\n");
echo ("</td>");



print("<td class=\"a\" align=\"center\">" . (isset($row["username"]) ? ("<a href=\"userdetails.php?id=".$row["owner"] . "\"><b>".get_user_class_color($row["class"], $row["username"]) . "</b></a>") : "<i>без автора</i>")."</td>\n");

print("</tr><tr><td colspan=10><span id=\"details_" .$id. "\"></span></td>");  

print("</tr>\n");
unset($row);
}


if ($CURUSER && !empty($mark))
print("<tr><td class=\"a\" colspan=\"12\" align=\"center\"><a href=\"markread.php\"><b>пометить все торренты прочитаными</b></a></td></tr>");

}
print("</table></table>");



stdfootchat(); 

?>