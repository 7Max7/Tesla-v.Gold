<? 
require_once("include/bittorrent.php");
gzip();
dbconn(false);
setlocale(LC_ALL, 'ru_RU.CP1251');
///loggedinorreturn();
 
parse_referer("empty");

//if (get_user_class() < UC_USER)
//	stderr($tracker_lang['error'], $tracker_lang['access_denied']);



$ograni4itel = 500; /// включить ограничение страниц до 1 тысячи к показу.



parked();
$cats = genrelist();




if (isset($_GET["incldead"]))
$get_incldead = (int) $_GET["incldead"];

if (isset($_GET["s"]))
$get_s = (int) $_GET["s"];




$check = (int) (isset($_GET["check"])?$_GET["check"]:""); // выбор типа 1 или 2

$searchstr = unesc(isset($_GET["search"])? htmlspecialchars_uni(strip_tags($_GET["search"])):"");

$get_type=(isset($_GET["stype"]) ? (int)$_GET["stype"]:"");


$cleansearchstr = htmlspecialchars($searchstr);

if (empty($cleansearchstr))
unset($cleansearchstr);

$tagstr = unesc(isset($_GET["tag"])?strip_tags($_GET["tag"]):"");
$cleantagstr = htmlspecialchars($tagstr);

if (empty($cleantagstr))
unset($cleantagstr);


$groupetr = unesc(isset($_GET["gr"])?$_GET["gr"]:0);
$cleangroupe = (int) $groupetr;

if (empty($cleangroupe))
unset($cleangroupe);
else
{
$groupe_cache=new MySQLCache("SELECT image,name FROM groups WHERE id=".sqlesc($cleangroupe)."", 86400,"details_groups-".$cleangroupe.".txt"); // кеш один день
$name_gre=$groupe_cache->fetch_assoc();
}

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

$orderby = "ORDER BY 

".($column=="seeders" ? " (torrents.seeders+torrents.f_seeders) ":"

".($column=="leechers" ? "(torrents.leechers+torrents.f_leechers) ":"torrents." . $column . "")."

")."

 " . $ascdesc;
$pagerlink = "sort=" . intval($_GET['sort']) . "&type=" . $linkascdesc . "&";
} else {
//$orderby = "ORDER BY torrents.added DESC";
$orderby = "ORDER BY torrents.added DESC";///torrents.sticky ASC, 
$pagerlink = "";
}

$addparam = "";
$wherea = array();
$wherecatina = array();

if (!empty($_GET["incldead"]))
$_GET["incldead"]=$_GET["incldead"];
else
unset($_GET["incldead"]);


if ($_GET["incldead"] == 1){
        $addparam .= "incldead=1&amp;";
     //   if (!isset($CURUSER) || get_user_class() < UC_ADMINISTRATOR)
     //   $wherea[] = "banned <> 'yes'";
}
elseif ($_GET["incldead"] == 2){
        $addparam .= "incldead=2&amp;";
        $wherea[] = "visible = 'no'";
    //    $wherea[] = "banned = 'no'";
}
elseif ($_GET["incldead"] == 3){
        $addparam .= "incldead=3&amp;";
        $wherea[] = "free = 'yes'";
     // $wherea[] = "visible = 'yes'";
    //    $wherea[] = "banned = 'no'";
}
elseif ($_GET["incldead"] == 4){
        $addparam .= "incldead=4&amp;";
        $wherea[] = "sticky='yes'";
}
elseif ($_GET["incldead"] == 5){
        $addparam .= "incldead=5&amp;";
     // $wherea[] = "free = 'yes'";
        $wherea[] = "moderated = 'no'";
}
elseif ($_GET["incldead"] == 6){
        $addparam .= "incldead=6&amp;";
    //  $wherea[] = "free = 'yes'";
        $wherea[] = "banned = 'yes'";
}
elseif ($_GET["incldead"] == 7){
        $addparam .= "incldead=7&amp;";
    //  $wherea[] = "free = 'yes'";
        $wherea[] = "tags = ''";
}
elseif ($_GET["incldead"] == 8 && get_user_class() > UC_USER){
        $addparam .= "incldead=8&amp;";
        $wherea[] = "owner = '$CURUSER[id]'";
}
elseif ($_GET["incldead"] == 9){
        $addparam .= "incldead=9&amp;";
        $wherea[] = "image1 = ''";
}
elseif ($_GET["incldead"] == 10){
        $addparam .= "incldead=10&amp;";
        $wherea[] = "multitracker = 'yes'";
}
elseif ($_GET["incldead"] == 11){
        $addparam .= "incldead=11&amp;";
        $wherea[] = "picture1 <>''";
        $wherea[] = "picture4 <>''";
}
elseif ($_GET["incldead"] == 13){
        $addparam .= "incldead=13&amp;";
        $wherea[] = "(f_seeders+f_leechers)=0";
        $wherea[] = "multitracker='no'";
       // $wherea[] = "multi_time<>'0000-00-00 00:00:00'";
       // $wherea[] = "moderatedby='92'";
       // $wherea[] = "owner='92'";
        $wherea[] = "(seeders+leechers)=0";
}
//else 
//$wherea[] = "visible = 'yes'";

if (empty($_GET["incldead"]))
$addparam .= "incldead=0&amp;";

if (isset($get_s))
$addparam .= "s=".$get_s."&amp;";

$category = (int)(isset($_GET["cat"])?$_GET["cat"]:"");


if ($category && $CURUSER) {
$todayactive="";
$res9 = sql_query("SELECT username,id,catedit FROM users WHERE class='".UC_MODERATOR."' and enabled='yes'") or sqlerr(__FILE__,__LINE__);
	while ($arr = mysql_fetch_assoc($res9))  {
	
    $cat="[cat$category]";
    $username = $arr['username'];
    $catedit = $arr['catedit'];
    $id = $arr['id'];

    if (!empty($todayactive))
    $todayactive.=", ";



    if (stristr($catedit, $cat) && !empty($catedit)){
    $todayactive.= "<b><a href=userdetails.php?id=$id >".get_user_class_color(UC_MODERATOR, $username) . "</a></b><a title=\"Категория выбрана для модератора\">*</a>";
    } else {
   $todayactive.= "<b><a href=userdetails.php?id=$id >".get_user_class_color(UC_MODERATOR,$username) . "</a></b>";
    }
    

}

//$todayactive = str_ireplace(", ,", ", ", $todayactive);


$name_class=$todayactive;
}

if (isset($_GET["all"])<>1)
unset($_GET["all"]);
else {
$all = (isset($_GET["all"])?$_GET["all"]:"");
}

if (empty($all))
        if (empty($_GET) && $CURUSER["notifs"])
        {
          $all = True;
          foreach ($cats as $cat)
          {
            $all &= $cat["id"];
            if (strpos($CURUSER["notifs"], "[cat" . $cat["id"] . "]") !== False)
            {
              $wherecatina[] = $cat["id"];
              $addparam.= "c$cat[id]=1&amp;";
            }
          }
        }
        elseif ($category)
        {
          if (!is_valid_id($category))
            stderr($tracker_lang['error'], "Invalid category ID.");
          $wherecatina[] = $category;
          $addparam.= "cat=$category&amp;";
        }
        else
        {
          $all = True;
          foreach ($cats as $cat)
          {
            $all &= (isset($_GET["c$cat[id]"])? $_GET["c$cat[id]"]:"");
            if (isset($_GET["c$cat[id]"]))
            {
              $wherecatina[] = $cat["id"];
              $addparam.= "c$cat[id]=1&amp;";
            }
          }
        }

if (!empty($all))
{
  $wherecatina = array();
//  $addparam = "";
}

if (count($wherecatina) > 1)
        $wherecatin = implode(",",$wherecatina);
elseif (count($wherecatina) == 1)
        $wherea[] = "category = ".$wherecatina[0];

$wherebase = $wherea;

if (isset($cleansearchstr))
{

if ($on_search_log==1) {
// вводимые теги

$seconds = 60*60*168; // нелеля
$dt = get_date_time(gmtime() - $seconds);
$searchcloud = sqlesc($cleansearchstr); 

if ($check<>2 && $CURUSER["added"]<$dt && $CURUSER["uploaded"]<>0 && $CURUSER["downloaded"]<>0){
@unlink(ROOT_PATH."cache/block-clouds.txt"); // для оптимизации запросов, удаляем кеш после обновление его


$time = sqlesc(get_date_time());
$r = mysql_fetch_array(sql_query("SELECT COUNT(*) FROM searchcloud WHERE searchedfor = $searchcloud"), MYSQL_NUM); 
$a = $r[0]; 
if ($a)
sql_query("UPDATE searchcloud SET howmuch = howmuch + 1 WHERE searchedfor = $searchcloud"); 
else 
sql_query("INSERT INTO searchcloud (searchedfor, howmuch, added) VALUES ($searchcloud, 1, $time)");  
// вводимые теги
}}



$searchstr = substr($searchstr, 0, 100); /// хотя 64 вполне устраивает

$q = sqlesc("%".sqlwildcardesc(trim($searchstr))."%");
$q2 = str_replace("."," ",sqlesc("%".sqlwildcardesc(trim($searchstr))."%"));


$search = htmlspecialchars_uni($searchstr);
$search = substr($search, 0, 128); /// 64 вполне устраивает


$search = preg_replace("/\[((\s|.)+?)\]/", "", $search);

if (empty($get_s))
$search = preg_replace("/[^\w\x7F-\xFF\s]/", " ", $search);

//$search = trim(preg_replace("/\s(\S{1,2})\s/", " ", preg_replace("/\s+\s/", " "," $search ")));
$search = preg_replace("/\s+\s/", " ", $search);


$list = explode(" ", $search);
$ecr = array("(",")","]","[");
$listrow = array();
$listview = array();
foreach ($list AS $lis){
$idlist = (int) $lis;

if ((strlen($lis)<=2 && ($lis <> str_replace($ecr, '', $lis))) || !empty($idlist)){
//$listrow[] = "-".$lis;
}
elseif (strlen($lis)>=3 && ($lis == str_replace($ecr, '', $lis))){
$listrow[] = "+".$lis;
$listview[] = $lis;
}
//else
//$listrow[] = $lis;

}

$listrow = array_unique($listrow); /// удаляем дубликаты
$listview = array_unique($listview); /// удаляем дубликаты






   // 	$wherea[] = "torrents.descr LIKE '%" . sqlwildcardesc($searchss) . "%'";
        if ($get_type=="1" && $CURUSER){
  //   $wherea[] = "torrents.descr LIKE {$q2}"; /// OR torrents.descr LIKE {$q2}
         $wherea[]= "MATCH (torrents.descr) AGAINST ('".trim(implode(" ", $listrow))."' IN BOOLEAN MODE)";
        }
        elseif($get_type=="3" && get_user_class() >= UC_MODERATOR){
      // $wherea[] = "torrents.torrent_com LIKE {$q}"; /// OR torrents.descr LIKE {$q2}
       $wherea[]= "MATCH (torrents.torrent_com) AGAINST ('".trim(implode(" ", $listrow))."' IN BOOLEAN MODE)";
         }
         elseif($get_type=="4"){
        $wherea[] = "torrents.info_hash = ".sqlesc((string) $searchstr); /// OR torrents.info_hash
         }
         elseif ($get_type==2 && $CURUSER && empty($cleantagstr))
        {

$fisql=array();

if (strlen($search) >= 4)
$rs = sql_query("SELECT torrent FROM files WHERE MATCH (files.filename) AGAINST ('" .$search. "') GROUP BY torrent DESC LIMIT 100") or sqlerr(__FILE__, __LINE__); 
else
$rs = sql_query("SELECT torrent FROM files WHERE filename LIKE {$q2} GROUP BY torrent DESC LIMIT 100") or sqlerr(__FILE__, __LINE__); 

while ($ari = mysql_fetch_array($rs))
$fisql[]=$ari["torrent"];

if (implode($fisql)){
$wherea[]= "torrents.id IN (".(implode(", ",$fisql)).")"; /// OR torrents.name LIKE {$q2}

}
else {
	
if (strlen($search) >= 4 && empty($get_s) || empty($get_s)){
$wherea[]= "MATCH (torrents.name) AGAINST ('".trim(implode(" ", $listrow))."' IN BOOLEAN MODE)";
//$wherea[]= "MATCH (torrents.name) AGAINST ('" .$search. "')";
//$orderby = "ORDER BY MATCH (torrents.name) AGAINST ('" .$search. "') DESC";
}
elseif (get_user_class() >= UC_MODERATOR && !empty($get_s))
$wherea[]= "torrents.name LIKE {$q2}"; /// OR torrents.name LIKE {$q2}

}

}
else

if (strlen($search) >= 4 && empty($get_s) || empty($get_s)){
//$wherea[]= "MATCH (torrents.name) AGAINST ('>" .$search. "< (".trim(implode(" ", $listrow)).")' IN BOOLEAN MODE)";
$wherea[]= "MATCH (torrents.name) AGAINST ('".trim(implode(" ", $listrow))."' IN BOOLEAN MODE)";
//$wherea[]= "MATCH (torrents.name) AGAINST ('" .$search. "')";
}
elseif (get_user_class() >= UC_MODERATOR && !empty($get_s))
$wherea[]= "torrents.name LIKE {$q2}"; /// OR torrents.name LIKE {$q2}


       
$addparam.= "stype=" . urlencode($get_type) . "&amp;"; /// проверить   

if (!empty($searchstr))
$addparam .= "search=" . urlencode($searchstr) . "&amp;";


if ($check<>2 && $CURUSER["added"]<$dt && $CURUSER["uploaded"]<>0 && $CURUSER["downloaded"]<>0){
	
if (!$CURUSER){
$user_data=getenv("REMOTE_ADDR"); 
}
if ($CURUSER){
$user = $CURUSER["username"];
$user_ip1=getenv("REMOTE_ADDR"); 
//$user_data="$user (".$user_ip1.")"; 
$user_data="$user"; 
}

$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER["username"]);
write_log("$user_data искал $searchcloud\n", "$user_color","search");
}

}


if (!empty($cleantagstr)) {
//$wherea[] = "torrents.tags LIKE '%" . sqlwildcardesc($tagstr) . "%'";
//echo $tagstr;

$tagstr = htmlspecialchars_uni($tagstr);
$tagstr = substr($tagstr, 0, 128); /// 64 вполне устраивает

$tagstr = preg_replace("/\[((\s|.)+?)\]/", "", $tagstr);
$tagstr = preg_replace("/[^\w\x7F-\xFF\s]/", " ", $tagstr);
$tagstr = preg_replace("/\s+\s/", " ", $tagstr);
$tagstr = str_replace("/", ' ', $tagstr);

$list = explode(" ", $tagstr);

$tagstr_ar = array();

foreach ($list AS $lis){

if (strlen($lis)>=3)
$tagstr_ar[] = "+".$lis;

}

$tagstr_ar = array_unique($tagstr_ar); /// удаляем дубликаты


if (count($tagstr_ar)){
//$wherea[] = "MATCH (torrents.tags) AGAINST ('" . sqlwildcardesc($tagstr) . "')";
$wherea[]= "MATCH (torrents.tags) AGAINST ('".trim(implode(" ", $tagstr_ar))."' IN BOOLEAN MODE)";
}

$addparam.= "tag=" . urlencode($tagstr) . "&";
}

if (!empty($cleangroupe)) {
		$wherea[] = "torrents.owner=(SELECT id FROM users WHERE groups=".sqlesc($cleangroupe)." LIMIT 1)";
        $addparam.= "gr=".urlencode($cleangroupe)."&";
}

$where = implode(" AND ", $wherea);

if (isset($wherecatin))
$where .= ($where ? " AND " : "") . " category IN (" . $wherecatin . ")";

if (!empty($where))
$where = "WHERE $where";

$res = sql_query("SELECT COUNT(*) FROM torrents $where") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$count = $row[0];
$num_torrents = $count;


if (empty($count) && isset($cleansearchstr)) {
        $wherea = $wherebase;
        //$orderby = "ORDER BY id DESC";
        $searcha = explode(" ", $cleansearchstr);
        $sc = 0;
        foreach ($searcha as $searchss) {
                if (strlen($searchss) <= 1)
                        continue;
                $sc++;
                if ($sc > 5)
                        break;
                $ssa = array();
                
         if ($get_type=="1" && $CURUSER){
        $ssa[] = "torrents.descr LIKE '%" . sqlwildcardesc($searchss) . "%'";
        } else
		$ssa[] = "torrents.name LIKE '%" . sqlwildcardesc($searchss) . "%'";
        }
        if ($sc)
		{
         $where = implode(" AND ", $wherea);
          if (!empty($where))
         $where = "WHERE $where";
       //  $res = sql_query("SELECT COUNT(*) FROM torrents $where");
        // $row = mysql_fetch_array($res);
       //  $count = $row[0];
        }
}




$torrentsperpage = $CURUSER["torrentsperpage"];
if (empty($torrentsperpage) || $torrentsperpage>150)
$torrentsperpage = 25;

if ($_GET["incldead"] == 13)
$torrentsperpage = 500;

if ($count)
{
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
        define('ADDREFLINK', "browse.php?".$addparam);
        
        list($pagertop, $pagerbottom, $limit) = pager($torrentsperpage, $count, "browse.php?" . $addparam);
        $query = "SELECT torrents.id, torrents.moderated, torrents.multitracker, torrents.moderatedby, torrents.moderatordate, torrents.viponly, torrents.category, torrents.tags, (torrents.leechers+torrents.f_leechers) AS leechers, (torrents.seeders+torrents.f_seeders) AS seeders, torrents.free,torrents.banned, torrents.banned_reason, torrents.name, torrents.size, torrents.added, torrents.comments, torrents.numfiles,  torrents.sticky, torrents.owner, b.class AS classname, b.username AS classusername, users.username, users.class FROM torrents
		LEFT JOIN users ON torrents.owner = users.id
		LEFT JOIN users AS b ON torrents.moderatedby = b.id
		 $where $orderby $limit";
       /// $res = sql_query($query) or sqlerr(__FILE__,__LINE__);
       
         if (empty($ograni4itel) || ($_GET["page"]<=$ograni4itel && $ograni4itel))
         $res = sql_query($query) or die("Внимание, сервер подключенный к базе данных временно не работает. Повторите попытку позже.");

		
		/**
		 
		 	$query2 = "SELECT torrents.id FROM torrents $where $orderby $limit";
		$copy_res=sql_query($query2) or sqlerr(__FILE__,__LINE__);
		
	while ($sd = mysql_fetch_assoc($copy_res)) 
	$s[]=$sd["id"];
	
$resour= sql_query("SELECT COUNT(*) AS numc,(SELECT COUNT(*) AS numb FROM bookmarks WHERE userid=$CURUSER[id] and torrentid IN (".(implode(", ",$s)).") ) AS numb FROM checkcomm WHERE userid = $CURUSER[id] AND checkid IN (".(implode(", ",$s)).") AND torrent = 1") or sqlerr(__FILE__, __LINE__);	
	
while ($arr_res = mysql_fetch_array($resour)){

$checkcomm=$arr_res["numc"];
$bookcomm=$arr_res["numb"];

$fisql[$ari["id"]]=$ari["name"];
}
		 
		 */
		 
		 
		//die("Перенаправление на страницу 500.<script>setTimeout('document.location.href=\"500.php\"', 10);</script>");;
}
/*
/// было
9 (queries) - 91.22% (php) - 8.78% (0.0119 => sql) - 1606 КБ (use memory) 
/// стало
9 (queries) - 93.14% (php) - 6.86% (0.0117 => sql) - 1606 КБ (use memory)
*/
//else
//unset($res);

if ($check==2 && $CURUSER){
$vari="в описаниях";
}
else
$vari="в названиях";

if (isset($cleansearchstr))
stdheadchat("Результаты поиска $vari по "." \"$searchstr\"");

if (isset($cleantagstr)){

stdheadchat("Результаты поиска по тэгу: " . htmlspecialchars($tagstr));
}

if (!empty($cleangroupe) && !empty($name_gre["name"])) {
stdheadchat("Результаты поиска по групе: " . ($name_gre["name"]));
}

else

stdheadchat($tracker_lang['browse']);

?>

<style type="text/css" media=screen>
a.catlink:link, a.catlink:visited{ text-decoration: none;}
a.catlink:hover {border-top: dashed 1px #c3c5c6;padding: 0px;}
</style>

<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<div id="loading-layer" style="display:none;font-family: Verdana;font-size: 11px;width:200px;height:50px;background:#FFF;padding:10px;text-align:center;border:1px solid #000">
     <div style="font-weight:bold" id="loading-layer-text">Загрузка. Пожалуйста, подождите...</div><br />
     <img src="pic/loading.gif" border="0" />
</div>



<table class="embedded" cellspacing="0" cellpadding="5" width="100%">
<tr><td class="colhead" align="center" colspan="12"><a href="browse.php" class="altlink_white">Список всех торрентов</a> или к <a href="browseday.php" class="altlink_white">списку торрентов за сегодня</a>.</td></tr>
<tr><td colspan="12" align="center">


<table class="embedded" align="center">
<tr>
<td class="bottom">
        <table class="bottom">
        <tr>

<?
$i = 0;
foreach ($cats as $cat) {
        $catsperrow = 5;
        print(($i && $i % $catsperrow == 0) ? "</tr><tr>" : "");
        print("<td class=\"bottom\" style=\"padding-bottom: 2px;padding-left: 7px\"><input name=\"c$cat[id]\" type=\"checkbox\" " . (in_array($cat["id"],$wherecatina) ? "checked " : "") . "value=\"1\">
        ".(in_array($cat["id"],$wherecatina) ? "<a class=\"catlink\" title=\"" . htmlspecialchars($cat["name"]) . " - $cat[num_torrent] торрента(ов)\" href=\"browse.php?incldead=1&cat=$cat[id]\">" . htmlspecialchars($cat["name"]) . "</a>" : "<b><a class=\"catlink\" title=\"" . htmlspecialchars($cat["name"]) . "\" href=\"browse.php?incldead=1&cat=".$cat["id"]."\">" . htmlspecialchars($cat["name"]) . "</a></b> (".$cat["num_torrent"].")")."

		<span style=\"cursor: pointer;\" onclick=\"javascript: show_tags(".$cat["id"].");\"><img border=\"0\" src=\"pic/tags.gif\" title=\"Показать тэги\"></span></td></td>\n");
        ++$i;
}

$alllink = "<div align=\"left\">(<a href=\"browse.php?all=1\"><b>".$tracker_lang['show_all']."</b></a>)</div>";

$ncats = count($cats);
$nrows = @ceil($ncats/$catsperrow);
$lastrowcols = $ncats % $catsperrow;

if (!empty($lastrowcols)) {
if ($catsperrow - $lastrowcols <> 1) {
print("<td class=\"bottom\" rowspan=\"" . ($catsperrow  - $lastrowcols - 1) . "\">&nbsp;</td>");
} /// свернуть
}
?>
</tr>
</table>
        
<div id="tags"></div>
  
</td>
</tr>

<tr><td class="a" align="center">

<script type='text/javascript' src='js/jquery.autocomplete.js'></script>

<style>
.ac_results {padding: 0px;border: 1px solid black;	background-color: white;overflow: hidden;z-index: 99999;}
.ac_results ul {width: 100%;list-style-position: outside;list-style: none;padding: 0;margin: 0;}
.ac_results li {margin: 0px;padding: 2px 5px;cursor: default;display: block;/*width: 100%;*/font: menu; font-size: 12px;line-height: 16px;overflow: hidden;}
.ac_loading {background: white url('pic/loading.gif') right center no-repeat;}
.ac_odd {background-color: #eee;}
.ac_over {background-color: #0A246A;color: white;}
</style>

<script type="text/javascript">
$().ready(function() {

	function log(event, data, formatted) {
		$("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
	}
	
	function formatItem(row) {
		return row[0] + " (<strong>" + row[1] + "</strong>)";
	}
	function formatResult(row) {
		return row[0].replace(/(<.+?>)/gi, '');
	}
	

	$("#suggest").autocomplete('autocomplete.php', {
		width: 700,
		max: 15,
		multiple: false,
		matchContains: true,
		formatItem: formatItem,
		formatResult: formatResult
	});

	
	$(":text, textarea").result(log).next().click(function() {
		$(this).prev().search();
	});

	$("#suggest").result(function(event, data, formatted) {
		var hidden = $(this).parent().next().find(">:input");
		hidden.val( (hidden.val() ? hidden.val() + ";" : hidden.val()) + data[1]);
	});

	$("#scrollChange").click(changeScrollHeight);
	
	$("#clear").click(function() {
		$(":input").unautocomplete();
	});
});


</script>

<form method="get" action="browse.php">

<?

echo "<b>Поиск</b>: <input type=\"text\" id=\"suggest\" name=\"search\" size=\"100\" class=\"searchgif\" value=\"".htmlspecialchars($searchstr)."\" /> ";


echo "<select name=\"s\">";
echo "<option value=\"0\">Релевантный поиск</option>";

echo "<option value=\"1\" ".(!empty($get_type) ?" DISABLED":"")." ".($get_s == 1 ? " selected" : "").">Точный поиск</option>";


echo "</select>";
echo "<br>";


?>


По 
<select name="stype">
<option value="0">Названиям</option>

<? print(!empty($CURUSER) ? "
<option value=\"1\" ".($get_type == 1 ? " selected" : "").">Описаниям</option>
<option value=\"2\" ".($get_type == 2 ? " selected" : "").">Файлам</option>

".(get_user_class() >= UC_MODERATOR ? "
<option value=\"3\" ".($get_type == 3 ? " selected" : "").">Историям</option>
":"")."
<option value=\"4\" ".($get_type == 4 ? " selected" : "").">SHA-1 (info_hash)</option>
" : ""); ?>

</select>

<? 
$res_cat=new MySQLCache("SELECT id, name, image FROM groups", 24*7200, "browse_gr_array.txt"); // 24 часа
while ($arr_cat=$res_cat->fetch_assoc()){
$cat_sql[] = array("id"=>$arr_cat["id"], "name"=>$arr_cat["name"], "image"=>$arr_cat["image"]);
}

echo "<select name=\"gr\">
<option value=\"0\">Все группы</option>";

$grdropdown = "";
foreach ($cat_sql as $cat) {
$grdropdown .= "<option value=\"" . $cat["id"]. "\"";
if ($cat["id"] == $_GET["gr"])
$grdropdown .= " selected=\"selected\"";
$grdropdown .= ">" . ($cat["name"]) . "</option>\n";
}

echo $grdropdown;

echo "</select>";
?>


<?=$tracker_lang['in'];?> 

<select name="incldead">
<option value="0"><?=$tracker_lang['active'];?></option>
<option value="1" <? print((isset($get_incldead)?$get_incldead:"") == 1 ? " selected" : ""); ?>><?=$tracker_lang['including_dead'];?></option>
<option value="2"<? print((isset($get_incldead)?$get_incldead:"") == 2 ? " selected" : ""); ?>><?=$tracker_lang['only_dead'];?></option>
<option value="3"<? print((isset($get_incldead)?$get_incldead:"") == 3 ? " selected" : ""); ?>><?=$tracker_lang['golden_torrents'];?></option>
<option value="4"<? print((isset($get_incldead)?$get_incldead:"") == 4 ? " selected" : ""); ?>>Важных (прикрепленных)</option>
<option value="5"<? print((isset($get_incldead)?$get_incldead:"") == 5 ? " selected" : ""); ?>>Не проверенные</option>
<option value="6"<? print((isset($get_incldead)?$get_incldead:"") == 6 ? " selected" : ""); ?>>Забаненные</option>
<option value="7"<? print((isset($get_incldead)?$get_incldead:"") == 7 ? " selected" : ""); ?>>Без тегов</option>

<? if (get_user_class() > UC_USER){ ?>
<option value="9"<? print((isset($get_incldead)?$get_incldead:"") == 9 ? " selected" : ""); ?>>Без постера</option>
<? } ?>

<? if (get_user_class() > UC_USER){ ?>
<option value="8"<? print((isset($get_incldead)?$get_incldead:"") == 8 ? " selected" : ""); ?>>Ваши торренты</option>
<? } ?>

<option value="10"<? print((isset($get_incldead)?$get_incldead:"") == 10 ? " selected" : ""); ?>>Мультитрекерные</option>
<option value="11"<? print((isset($get_incldead)?$get_incldead:"") == 11 ? " selected" : ""); ?>>Со скриншотами</option>

</select>
<select name="cat">
<option value="0">По умолчанию все</option>
<?

if (!empty($_GET["cat"]))
$_GET["cat"]=$_GET["cat"];
else
$_GET["cat"]=0;

//$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
$catdropdown .= "<option value=\"" . $cat["id"] . "\"";
if ($cat["id"] == $_GET["cat"])
$catdropdown .= " selected=\"selected\"";
$catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
}

echo $catdropdown;
?>
</select>

<input class="btn" type="submit"  style="width: 100px" value="<?=$tracker_lang['search'];?>!" />
<br/>
</form>
</td></tr></table>

<?


if (isset($cleansearchstr) && $get_type==2 && !implode($fisql)){
print("<tr><td class=\"b\" colspan=\"12\">Результат поиска в файлах \"" . htmlspecialchars($searchstr) . "\"</td></tr>\n");

print("<tr><td align=\"center\" class=\"b\" colspan=\"12\"><h3>Поиск в файлах провален, включен дополнительный поиск по названиям торрент файлов.</h3></td></tr>\n");
}

if (isset($cleantagstr)){

if (count($tagstr_ar))
echo "<tr><td class=\"a\" colspan=\"12\">Результаты поиска по тэгу: <b>".htmlspecialchars($tagstr)."</b></td></tr>\n";


}


if (isset($cleansearchstr)){

if (count($listview))
$veiew = "<br>Предпочтение слов в поиске: <b>".trim(implode(", ", $listview))."</b>.";

print("<tr><td class=\"a\" colspan=\"12\">Результат поиска $vari по <b>" . htmlspecialchars($searchstr) . "</b> ".$veiew."</td></tr>\n");

}
if (!empty($cleangroupe) && !empty($name_gre["name"])) {
print("<tr><td class=\"a\" colspan=\"12\">Результаты поиска по группе: <b>".($name_gre["name"])."</b></td></tr>\n");
}

if (!empty($_GET["gr"]) && empty($cleangroupe)) {
print("<tr><td class=\"a\" colspan=\"12\">Поиск по группам был упущен, в связи с неправильными входыми данными.</td></tr>\n");
}

print("</td></tr>");


  if (isset($name_class)) {
        print("<tr><td class=\"b\" colspan=\"12\">");
        print("Данную категорию торрентов могут редактировать модераторы: ".$name_class);
        print("</td></tr>");
		}

if ($num_torrents) {
	
 if (empty($ograni4itel) || ($_GET["page"]<=$ograni4itel && $ograni4itel)){

        print("<tr><td class=\"index\" colspan=\"12\">");
        print($pagertop);
        print("</td></tr>");

        torrenttable($res, "index");

        print("<tr><td class=\"index\" colspan=\"12\">");
        print($pagerbottom);
        print("</td></tr>");
        
   }    else

echo ("<tr><td align=\"center\" class=\"index\" colspan=\"12\"><h3>Внимание: Техническое ограничение показа.</h3> <br><b>Количество страниц ограничено до $ograni4itel штук к показу. Пожалуйста попробуйте фильтры и более точные фразы к поиску. <br> Попытка к ".((int) $_GET["page"])." странице неудачна. </b></td></tr>\n");
                //print("<p>Попробуйте изменить запрос поиска.</p>\n");
        




}
else {
        if (isset($cleansearchstr)) {

                print("<tr><td align=\"center\" class=\"index\" colspan=\"12\"><h3>По данному критерию ничего не найденно.</h3> <br><b>Попробуйте изменить тип поиска, с активные на включая мертрые и т.п.</b></td></tr>\n");
                //print("<p>Попробуйте изменить запрос поиска.</p>\n");
        }
        else {
                print("<tr><td align=\"center\" class=\"index\" colspan=\"12\"><h3>".$tracker_lang['nothing_found']."</h3></td></tr>\n");
                //print("<p>Извините, данная категория пустая.</p>\n");
        }
}




print("</table></table>");
///sql_query("UPDATE torrents SET free='no' WHERE owner = '92' AND moderated='yes' AND multitracker='yes' AND size<'3220370996'") or sqlerr(__FILE__, __LINE__);
stdfootchat(); 

?>