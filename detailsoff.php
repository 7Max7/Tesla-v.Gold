<?
require_once("include/bittorrent.php");

gzip();
dbconn();
loggedinorreturn();
parked();

//[4] => 0.000852 [SELECT off_reqs.id, off_reqs.category, off_reqs.name, off_reqs.size, off_reqs.added, off_reqs.comments, off_reqs.numfiles, off_reqs.owner, off_reqs.ful_id, IF(off_reqs.numratings < 1, NULL, ROUND(off_reqs.ratingsum / off_reqs.numratings, 1)) AS rating, categories.name AS cat_name, categories.image AS cat_pic, ch.id AS checkcomm,groups.image, u.username AS user_n, u.class AS user_c, u.id AS user_i, torrents.name AS tor_name, off_reqs.fulfilled,off_reqs.perform,off_reqs.data_perf, users.username, users.class FROM off_reqs LEFT JOIN categories ON off_reqs.category = categories.id LEFT JOIN users ON off_reqs.owner = users.id LEFT JOIN groups ON users.groups = groups.id LEFT JOIN users AS u ON off_reqs.fulfilled = u.id LEFT JOIN torrents ON torrents.id = off_reqs.ful_id LEFT JOIN checkcomm AS ch ON ch.userid = 2 AND ch.checkid = off_reqs.id AND ch.offer = 1 ORDER BY off_reqs.id DESC LIMIT 0,15]









function torrenttable_of($res, $variant = "index") {
		global $pic_base_url, $CURUSER, $SITENAME, $tracker_lang;

?>
<style>
.effect {FILTER: alpha(opacity=50); -moz-opacity: .50; opacity: .50;}
</style>
<?

$res_cat=new MySQLCache("SELECT id, name, image FROM categories", 24*7200, "browse_cat_array.txt"); // 24 ����
while ($arr_cat=$res_cat->fetch_assoc()){
$cat_sql[$arr_cat["id"]] = array("name"=>$arr_cat["name"], "image"=>$arr_cat["image"]);
}

$checkin=array();

$res_t1 = sql_query("SELECT checkid FROM checkcomm WHERE userid = ".sqlesc($CURUSER["id"])." AND offer = 1") or sqlerr(__FILE__, __LINE__);

while ($arr_c1 = mysql_fetch_assoc($res_t1)){
$checkin[$arr_c1["checkid"]] = 1;
}
///print_r($checkin);


print("<tr>\n");

$count_get = 0;
$oldlink="";
foreach ($_GET as $get_name => $get_value) {

$get_name = mysql_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_name)));

$get_value = mysql_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_value)));

if ($get_name != "sort" && $get_name != "type") {
if ($count_get > 0) {
$oldlink = $oldlink . "&" . $get_name . "=" . $get_value;
} else {
$oldlink = $oldlink . $get_name . "=" . $get_value;
}
$count_get++;
}

}

if ($count_get > 0) {
$oldlink = $oldlink . "&";
}


if ($_GET['sort'] == "1") {
if ($_GET['type'] == "desc") {
$link1 = "asc";
} else {
$link1 = "desc";
}
}

if ($_GET['sort'] == "2") {
if ($_GET['type'] == "desc") {
$link2 = "asc";
} else {
$link2 = "desc";
}
}

if ($_GET['sort'] == "3") {
if ($_GET['type'] == "desc") {
$link3 = "asc";
} else {
$link3 = "desc";
}
}

if ($_GET['sort'] == "4") {
if ($_GET['type'] == "desc") {
$link4 = "asc";
} else {
$link4 = "desc";
}
}

if ($_GET['sort'] == "5") {
if ($_GET['type'] == "desc") {
$link5 = "asc";
} else {
$link5 = "desc";
}
}

if ($_GET['sort'] == "7") {
if ($_GET['type'] == "desc") {
$link7 = "asc";
} else {
$link7 = "desc";
}
}

if ($_GET['sort'] == "8") {
if ($_GET['type'] == "desc") {
$link8 = "asc";
} else {
$link8 = "desc";
}
}

if ($_GET['sort'] == "9") {
if ($_GET['type'] == "desc") {
$link9 = "asc";
} else {
$link9 = "desc";
}
}

if ($_GET['sort'] == "10") {
if ($_GET['type'] == "desc") {
$link10 = "asc";
} else {
$link10 = "desc";
}
}

if (empty($link1)) { $link1 = "asc"; } // for torrent name
if (empty($link2)) { $link2 = "desc"; }
if (empty($link3)) { $link3 = "desc"; }
if (empty($link4)) { $link4 = "desc"; }
if (empty($link5)) { $link5 = "desc"; }
if (empty($link7)) { $link7 = "desc"; }
if (empty($link8)) { $link8 = "desc"; }
if (empty($link9)) { $link9 = "desc"; }
if (empty($link10)) { $link10 = "desc"; }

?>
<td class="colhead" align="center"><?=$tracker_lang['type'];?></td>
<td class="colhead" align="left"><a href="detailsoff.php?<? print $oldlink; ?>sort=1&type=<? print $link1; ?>" class="altlink_white"><?=$tracker_lang['name'];?></a> / <a href="detailsoff.php?<? print $oldlink; ?>sort=4&type=<? print $link4; ?>" class="altlink_white"><?=$tracker_lang['added'];?></a></td>

<td class="colhead" align="center"><a href="detailsoff.php?<? print $oldlink; ?>sort=2&type=<? print $link2; ?>" class="altlink_white">
<img title="���������� �� ���������� ������" src="pic/browse/nimberfiles.gif" border="0">
</a></td>
<td class="colhead" align="center"><a href="detailsoff.php?<? print $oldlink; ?>sort=3&type=<? print $link3; ?>" class="altlink_white">
<img title="���������� �� ���������� ���������" src="pic/browse/comments.gif" border="0">
</a></td>

<td class="colhead" align="center"><a href="detailsoff.php?<? print $oldlink; ?>sort=5&type=<? print $link5; ?>" >
<img title="���������� �� �������" src="pic/browse/size_file.gif" border="0">
</a></td>

<td class="colhead" align="center">���������</td>
<?

if ($variant == "index" || $variant == "bookmarks")
	print("<td class=\"colhead\" align=\"center\"><a href=\"detailsoff.php?{$oldlink}sort=9&type={$link9}\" class=\"altlink_white\">��������</a></td>\n");

if ($variant == "bookmarks")
	print("<td class=\"colhead\" align=\"center\">������ ������</td>\n");

print("</tr>\n");

print("<tbody id=\"highlighted\">");

	while ($row = mysql_fetch_assoc($res)) {
		$id = $row["id"];
		print("<tr>\n");


// ��������� ������� �� ����

/** Make some date varibles **/
$day_added = $row['added'];
$day_show = strtotime($day_added);
$thisdate = date('Y-m-d',$day_show);

///$numtorrents = number_format(get_row_count("torrents", "WHERE added LIKE '%".$thisdate." %'"));


/** If date already exist, disable $cleandate varible **/
if(isset($prevdate) && $thisdate==$prevdate){
$cleandate = '';

/** If date does not exist, make some varibles **/
}else{
$day_added = ''.date('�������, ����������� �l, j M Y ����', strtotime($row['added'])); // You can change this to something else
$cleandate = "<tr><td colspan=15 class=colhead><b>$day_added</b></td></tr>\n"; // This also...
}

/** Prevent that "torrents added..." wont appear again with the same date **/
$prevdate = $thisdate;

$man = array('Jan' => '������','Feb' => '�������','Mar' => '�����','Apr' => '������','May' => '���','Jun' => '����','Jul' => '����','Aug' => '�������','Sep' => '��������','Oct' => '�������','Nov' => '������','Dec' => '�������');

foreach($man as $eng => $ger){
    $cleandate = str_replace($eng, $ger,$cleandate);
}

$dag = array(
    'Mon' => ' �����������',
    'Tues' => '� �������',
    'Wednes' => ' �����',
    'Thurs' => ' �������',
    'Fri' => ' �������',
    'Satur' => ' �������',
    'Sun' => ' �����������'
);

foreach($dag as $eng => $ger){
    $cleandate = str_replace($eng.'day', $ger.'',$cleandate);
}
/** If torrents not listed by added date **/
if (isset($row["sticky"]) && $row["sticky"] == "no") // delete this line if you dont have sticky torrents or you want to display the addate for them also
if(!$_GET['sort'] && !$_GET['d']){
   echo $cleandate."\n";
}

// ��������� ������� �� ����  



	//	print("<td align=\"center\" style=\"padding: 0px\">");
		
		print("<tr>");
print("<td align=\"center\" class=\"b\" rowspan=2 width=2% style=\"padding: 5px\">");  
		
	
	  $cat_id=$row["category"];
	  
		if (!empty($cat_sql[$cat_id]["name"])) {///$row["cat_name"]

            $chstr = (!empty($_GET["search"])? "&search=".htmlspecialchars($_GET["search"]):"");

			echo("<a href=\"browse.php?incldead=1&cat=" . $row["category"].$chstr."\">");
			
			if (isset($cat_sql[$cat_id]["image"]) && !empty($cat_sql[$cat_id]["image"])){
				
				if ($_GET["sort"]=="6")
				echo("<img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/cats/" . $cat_sql[$cat_id]["image"] . "\" alt=\"" . $cat_sql[$cat_id]["name"] . "\" />");
				else
				echo("<img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/cats/" . $cat_sql[$cat_id]["image"] . "\" alt=\"" . $cat_sql[$cat_id]["name"] . "\" />");	
			}
			else
				echo($cat_sql[$cat_id]["name"]);
				
			echo("</a>");
		}
		else
			echo("-");
			
		print("</td>\n");

		
		$dispname = htmlspecialchars_uni($row['name']);
       //$dispname=strlen($dispname)>70?(substr($dispname,0,50)."..."):$dispname; 
		

print("<td colspan=\"12\" class=\"b\" align=\"left\">
  ".($CURUSER ? "<a href=\"detailsoff.php?" : "")."");


		print(" ".($CURUSER ? "id=$id" : "")." ");
		if ($variant == "index" || $variant == "bookmarks")
			print(" ".($CURUSER ? "&amp;hit=1" : "")." ");
	
	
		print("".($CURUSER ? "\">" : "")."<b><span title='".$row["name"]."'>".$dispname."<span></b>".($CURUSER ? "</a>" : "")."\n");

$checkcomm=$row["checkcomm"];
if (empty($checkin[$id]))(
 $check = "<a href=\"commentoff.php?action=check&amp;tid=$row[id]\"><img border=\"0\" src=\"pic/head2_2.gif\" alt=\"�������� ��������\" title=\"�������� ��������\" /></a>") ;
 else
 $check = "<a href=\"commentoff.php?action=checkoff&amp;tid=$row[id]\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" border=\"0\" src=\"pic/head2_2.gif\" alt=\"��������� ��������\" title=\"��������� ��������\" /></a>";


if ($variant == "bookmarks" && $CURUSER)
print("$check\n");

		if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
			$owned = 1;
		else
			$owned = 0;
///u.username AS user_n, u.class AS user_c, u.id AS user_i, torrents.name AS tor_name
		if ($owned)
			print("<a href=\"detailsoff.php?id=$row[id]&edit=1\"><img border=\"0\" src=\"pic/pen.gif\" alt=\"".$tracker_lang['edit']."\" title=\"".$tracker_lang['edit']."\" /></a>   ".($row["perform"]=="yes" ? "<b>����������� �������</b>: <a href=\"details.php?id=$row[ful_id]\">".$row["tor_name"]."</a>" : "")."\n");

print("</td></tr><tr>");
print("<td class=\"row2\" align=\"left\" class=\"small\">");  
print("<b>��������</b>: ".timesec($row["added"])."");

/*
$rpic = ratingpic($row["rating"]);
if ($row["rating"])
print"&nbsp; <b>������</b>: ".$row["rating"]."";
else
print"&nbsp; <b>������</b>: ���";
*/
///u.username AS user_n, u.class AS user_c, u.id AS user_i, torrents.name AS tor_name

print("".($row["perform"]=="yes" ? "<br><b>����� ����������</b>: ".$row["data_perf"]."" : "")." \n");
print("</td>\n");
print("<td class=\"row2\" align=\"center\">" . $row["numfiles"] . "</td>\n");
	
			if (!$row["comments"])
			print("<td class=\"row2\" align=\"center\">" . $row["comments"] . "</td>\n");
		else {
			if ($variant == "index")
				print("<td class=\"row2\" align=\"center\"><b>
				".($CURUSER ? "<a href=\"detailsoff.php?id=$id&amp;hit=1&amp;tocomm=1\">" : "")."
							
				" . $row["comments"] . "
				
				".($CURUSER ? "</a>" : "")."
				</b></td>\n");
			else
				print("<td class=\"row2\" align=\"center\"><b><a href=\"detailsoff.php?id=$id&amp;page=0#startcomments\">" . $row["comments"] . "</a></b></td>\n");
		}
	//	print("<td align=\"center\">" . str_replace(" ", "<br />", mksize($row["size"])) . "</td>\n");

print("<td class=\"row2\" align=\"center\">" . str_replace(" ", " ", mksize($row["size"])) . "</td>\n");
/// off_reqs.fulfilled,off_reqs.perform,
///u.username AS user_n, u.class AS user_c, u.id AS user_i, torrents.name AS tor_name
print("<td class=\"row2\" align=\"center\">");
print("".($row["perform"]=="yes" ? "<a href=\"userdetails.php?id=" . $row["user_i"]. "\"><b>" . get_user_class_color($row["user_c"], $row["user_n"]) . "</b></a>" : "���")." \n");
print("</td>");

		if ($variant == "index" || $variant == "bookmarks")
			print("<td class=\"row2\" align=\"center\">" . (isset($row["username"]) ? ("
			<a href=\"userdetails.php?id=" . $row["owner"] . "\"><b>" . get_user_class_color($row["class"], htmlspecialchars_uni($row["username"])) . "</b></a>
			") : "<i>��� ������</i>") . "</td>\n");


$id_gro = (int) $row["groups"];
$groupe_cache=new MySQLCache("SELECT image,name FROM groups WHERE id=".sqlesc($id_gro)."", 86400,"details_groups-".$id_gro.".txt"); // ��� ���� ����
$row=$groupe_cache->fetch_assoc();


			print ("<td class=\"row2\" align=\"center\">".($row["image"] ? "<a title=\"� ������ ���� �����\" href=\"groups.php\"><img src=pic/groups/" . $row["image"] . ">":"���")."</a></td>");





print("</tr>\n");
$oldday = (isset($day) ? $day:""); // ������ ����
}
print("</tbody>");  

return (isset($rows) ? $rows:"");
}


$id = (isset($_GET["id"])? (int) $_GET["id"]:"");

if (!isset($id) || !$id)
    
{
	
print"<style type=\"text/css\">
<!--
input.searchgif{
background:#FFFFFF url(pic/browse/search.gif) no-repeat scroll 0 50%;
color:#000000;
padding-left:18px;
}
-->
</style>";
//if (get_user_class() < UC_USER)
//	stderr($tracker_lang['error'], $tracker_lang['access_denied']);


$cats = genrelist();

$check = (isset($_GET["check"])? (int) $_GET["check"]:""); // ����� ���� 1 ��� 2
if ($check==2 && $CURUSER){
$searchstr = unesc(isset($_GET["search_descr"]) ? htmlspecialchars_uni(strip_tags($_GET["search_descr"])):"");
} else {
$searchstr = unesc(isset($_GET["search"]) ? htmlspecialchars_uni(strip_tags($_GET["search"])):"");
}

$cleansearchstr = htmlspecialchars($searchstr);
if (empty($cleansearchstr))
unset($cleansearchstr);

if (isset($_GET['sort']))
$_GET['sort']=$_GET['sort'];
else
$_GET['sort']="";

if (isset($_GET['type']))
$_GET['type']=$_GET['type'];
else
$_GET['type']="";


if ($_GET['sort'] && $_GET['type']) {

$column = '';
$ascdesc = '';

switch($_GET['sort']) {
case '1': $column = "name"; break;

case '3': $column = "comments"; break;
case '4': $column = "added"; break;

case '9': $column = "owner"; break;

default: $column = "id"; break;
}

switch($_GET['type']) {
case 'asc': $ascdesc = "ASC"; $linkascdesc = "asc"; break;
case 'desc': $ascdesc = "DESC"; $linkascdesc = "desc"; break;
default: $ascdesc = "DESC"; $linkascdesc = "desc"; break;
}

$orderby = "ORDER BY off_reqs." . $column . " " . $ascdesc;
$pagerlink = "sort=" . intval($_GET['sort']) . "&type=" . $linkascdesc . "&";
} else {
$orderby = "ORDER BY off_reqs.id DESC";
$pagerlink = "";
}

$addparam = "";
$wherea = array();
$wherecatina = array();

if (isset($_GET['incldead']))
$_GET['incldead']=$_GET['incldead'];
else
unset($_GET['incldead']);


if ($_GET["incldead"]==1)
{
        $addparam .= "incldead=1&amp;";
        $wherea[] = "perform = 'yes'";
      //  $wherea[] = "banned = 'no'";
}
elseif ($_GET["incldead"] == 2)
{
        $addparam .= "incldead=2&amp;";
       $wherea[] = "perform = 'no'";
}
elseif ($_GET["incldead"] == 3 && $SA)
{
        $addparam .= "incldead=3&amp;";
     $wherea[] = "owner = '".($CURUSER["id"])."'";
}
else
$addparam .= "incldead=0&amp;";


$category = (isset($_GET["cat"]) ? (int)$_GET["cat"]:"");

$all = (isset($_GET["all"]) ? $_GET["all"]:"");

if (!$all)

        if (!empty($category))
        {
          if (is_valid_id($category)){
            //stderr($tracker_lang['error'], "Invalid category ID.");
         $wherecatina[] = $category;
          $addparam .= "cat=$category&amp;";
          }
        }
        else
        {
          $all = True;
          foreach ($cats as $cat)
          {
            $all &= (isset($_GET["c$cat[id]"]) ? $_GET["c$cat[id]"]:"");
            if (!empty($_GET["c$cat[id]"]))
            {
              $wherecatina[] = $cat["id"];
              $addparam .= "c$cat[id]=1&amp;";
            }
          }
        }

if ($all)
{
  $wherecatina = array();
  $addparam = "";
}

if (!empty($searchstr)){
   // 	$wherea[] = "torrents.descr LIKE '%" . sqlwildcardesc($searchss) . "%'";
        if ($check=="2" && $CURUSER){
        $wherea[] = "off_reqs.descr LIKE '%" . sqlwildcardesc($searchstr) . "%'";
        }
        else 
        {
        $wherea[] = "off_reqs.name LIKE '%" . sqlwildcardesc($searchstr) . "%'";
        }
        $addparam .= "search=" . urlencode($searchstr) . "&amp;";
}

//$wherea=array_unique($wherea);


if (count($wherecatina) > 1)
        $wherecatin = implode(",",$wherecatina);
elseif (count($wherecatina) == 1)
        $wherea[] = "off_reqs.category = '".$wherecatina[0]."'";

$wherebase = $wherea;

$where = implode(" AND ", $wherea);

if (!empty($wherecatin))
$where .= ($where ? " AND " : "") . "off_reqs.category IN (" . $wherecatin . ")";

if (!empty($where))
$where = "WHERE $where";

$res = sql_query("SELECT COUNT(*) FROM off_reqs $where") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$count = $row[0];
$num_torrents = $count;

if (!$count && isset($cleansearchstr)) {
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
                

        if ($check=="2" && $CURUSER){
        $sesql="descr";
        }
        else
        $sesql="name";
        
	///	$ssa[] = "off_reqs.$sesql LIKE '%" . sqlwildcardesc($searchss) . "%'";
        }
      //  if ($sc) 
	//	{
      //   $where = implode(" AND ", $wherea);
        //  if (!empty($where))
      //   $where = "WHERE $where";
       //  $res = sql_query("SELECT COUNT(*) FROM off_reqs $where");
       //  $row = mysql_fetch_array($res);
        // $count = $row[0];
      //  }
}

$torrentsperpage = (int)$CURUSER["torrentsperpage"];
if (!$torrentsperpage)
        $torrentsperpage = 25;

if ($count)
{
    if ($addparam != "") {
 if ($pagerlink != "") {
  if ($addparam{strlen($addparam)-1} != ";") { // & = &amp;
    $addparam = $addparam . "&" . $pagerlink;
  } else {
    $addparam = $addparam . $pagerlink;
  }
 }
    } else {
 $addparam = $pagerlink;
    }
        list($pagertop, $pagerbottom, $limit) = pager($torrentsperpage, $count, "details.php?" . $addparam);
$query = "SELECT off_reqs.id, off_reqs.category, off_reqs.name, off_reqs.size, off_reqs.added, off_reqs.comments, off_reqs.numfiles, off_reqs.owner, off_reqs.ful_id,
IF(off_reqs.numratings < 1, NULL, ROUND(off_reqs.ratingsum / off_reqs.numratings, 1)) AS rating, u.username AS user_n, u.class AS user_c, u.id AS user_i, torrents.name AS tor_name, off_reqs.fulfilled,off_reqs.perform,off_reqs.data_perf,
users.username, users.groups, users.class FROM off_reqs
LEFT JOIN users ON off_reqs.owner = users.id
LEFT JOIN users AS u ON off_reqs.fulfilled = u.id 
LEFT JOIN torrents ON torrents.id = off_reqs.ful_id
$where $orderby $limit";

$res = sql_query($query) or sqlerr(__FILE__,__LINE__);
		
		//die("��������������� �� �������� 500.<script>setTimeout('document.location.href=\"500.php\"', 10);</script>");;
}


if ($check==2 && $CURUSER){
$vari="� ���������";
}
else
$vari="� ���������";

if (isset($cleansearchstr))    
stdheadchat("���������� ������ $vari �� "." \"$searchstr\"");

else

stdheadchat("������� | �����������");

?>

<style type="text/css" media=screen>

  a.catlink:link, a.catlink:visited{
                text-decoration: none;
        }
  a.catlink:hover {
	border-top: dashed 1px #c3c5c6;
	padding: 0px;
        }
</style>


<table class="embedded" cellspacing="0" cellpadding="5" width="100%">
<tr><td class="colhead" align="center" colspan="12"><a class="altlink_white" href=detailsoff.php>������ ��������</a> :: <a class="altlink_white" href=uploadoff.php>������ ������ ������</a></td></tr>
<tr><td colspan="12">

<table class="bottom" align="center">
<tr>
<td class="b">���� ����� ���� ������� ���� �� ������ ����� ��� ������, ����� ��� ������, �������� <a href=uploadoff.php>������</a> � �����������, ��� �� ��� ������ � ������ �� ����.</td>
</tr>
</table>


<form method="get" action="detailsoff.php">
<table class="embedded" align="center">
<tr>
<td class="bottom">
<table class="bottom">
<tr>
</tr>
</table>


</td>
</tr>
</form>
<tr><td class="a">
<form method="get" action="detailsoff.php">
<center>

<?
if (!$check)
$checked="checked";
if ($check=="1")
$checked_1="checked";
if ($CURUSER) {
print"<label><input name=\"check\" ".(isset($checked) ? $checked:"")." ".(isset($checked_1)? $checked_1:"")." type=\"radio\" value=\"1\">&nbsp;
�� �������� ��������:</label>
<input type=\"text\" id=\"searchinput\" name=\"search\" size=\"40\" class=\"searchgif\" value=\"".((isset($check) && $check==2) ? "":htmlspecialchars($searchstr))."\" />
";
}
else
print"
�� �������� ��������:
<input type=\"text\" id=\"searchinput\" name=\"search\" size=\"40\" class=\"searchgif\" value=\"".((isset($check) && $check==2) ? "":htmlspecialchars($searchstr))."\" />
";
?>


<?=$tracker_lang['in'];?> 
<select name="incldead">
<option value="0">��� �������</option>
<option value="1" <? print($_GET["incldead"] == 1 ? " selected" : ""); ?>>�����������</option>
<option value="2" <? print($_GET["incldead"] == 2 ? " selected" : ""); ?>>�� �����������</option>

</select>
<select name="cat">
<option value="0">�� ��������� ���</option>
<?


//$cats = genrelist();
if (isset($_GET["cat"]))
$_GET["cat"]=$_GET["cat"];
else
$_GET["cat"]="";

$catdropdown = "";
foreach ($cats as $cat) {
$catdropdown .= "<option value=\"" . $cat["id"] . "\"";
if ($cat["id"] == $_GET["cat"])
$catdropdown .= " selected=\"selected\"";
$catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
}

?>
<?= $catdropdown ?>
</select>

<?
if ($check=="2") {
$checked_2="checked";
$viewcheck=htmlspecialchars($searchstr);
}
if ($CURUSER) {
print"</br>
<label><input name=\"check\" ".(isset($checked_2) ? $checked_2:"")." type=\"radio\" value=\"2\">&nbsp;
�� �������� ��������:</label>
<input type=\"text\" id=\"searchinput\" name=\"search_descr\" size=\"40\" value=\"".(isset($viewcheck)? $viewcheck:"")."\" class=\"searchgif\"/> 
";
}
?>

<input class="btn" type="submit" value="<?=$tracker_lang['search'];?>!" />
</br></br>

</center>
</form>

</td></tr></table>

<?


if (isset($cleansearchstr))
print("<tr><td class=\"colhead\" colspan=\"12\">��������� ������ $vari ��� \"" . htmlspecialchars($searchstr) . "\"</td></tr>\n");

print("</td></tr>");

/*
  if ($name_class) {
        print("<tr><td class=\"index\" colspan=\"12\">");
        print("<b>������ ��������� ��������� ����� ������������� ����������</b>: $name_class");
        print("</td></tr>");}
*/
if (!empty($count)){
	
	  
	
        print("<tr><td class=\"index\" colspan=\"12\">");
        print($pagertop);
        print("</td></tr>");

        torrenttable_of($res, "bookmarks");

        print("<tr><td class=\"index\" colspan=\"12\">");
        print($pagerbottom);
        print("</td></tr>");

}

        if (isset($cleansearchstr)) {
                print("<tr><td align=\"center\" class=\"index\" colspan=\"12\">�� ������� �������� ������ �� ��������. <br><b>���������� �������� ��� ������.</b></td></tr>\n");
                //print("<p>���������� �������� ������ ������.</p>\n");
        } elseif (empty($count)) {
                print("<tr><td align=\"center\" class=\"index\" colspan=\"12\">�� ������� �������� ������ �� ��������. <br><b>���������� �������� ��� ������.</b></td></tr>\n");
                //print("<p>���������� �������� ������ ������.</p>\n");
        }

print("</table></table>");

stdfootchat(); 
	
	die();
	
}




$action = (int) $_GET['edit'];
if ($action=="1")
{



$res = sql_query("SELECT * FROM off_reqs WHERE id = $id") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
if (!$row)
die("����� ������ �� ����������!");

stdhead("�������������� ������� | ����������� \"" . $row["name"] . "\"");


    if ($CURUSER["catedit"] && get_user_class() == UC_MODERATOR && $CURUSER["id"] <> $row["owner"])
        {
        	$cat=$row["category"];
        	$cat_user=$CURUSER["catedit"];
        	
        	  // die ($cat);
         // die ($CURUSER["catedit"]);
            if ($cat_user!="" && !stristr("$cat_user", "[cat$cat]"))
            {
           	stderr($tracker_lang['error'],"�� �� ������ ������������� ���� ������, �� ���� ��������� ��������������."); 
            }
          
        }



if (!isset($CURUSER) || ($CURUSER["id"] <> $row["owner"] && get_user_class() < UC_MODERATOR)) {
	stdmsg($tracker_lang['error'],"�� �� ������ ������������� ���� ������.");
} else {
	

$url_e="/detailsoff.php?id=$id&edit=1";
$res_s = sql_query("SELECT DISTINCT uid, username, class FROM sessions WHERE uid<>-1 and time > ".sqlesc(get_date_time(gmtime() - 180))." and url='$url_e' ORDER BY time DESC") or sqlerr(__FILE__,__LINE__);
// or url LIKE '%$url_e%'
$lastid=0;
while ($ar_r = mysql_fetch_assoc($res_s)) {

$username = $ar_r['username'];
$id_use = $ar_r['uid'];
if ($title_who_s)
$title_who_s.=", ";
$title_who_s.= "<a href=\"userdetails.php?id=$id_use\">".get_user_class_color($ar_r["class"], $ar_r["username"]) . "</a> ".($ar_r['uid']==$row["owner"] ? "<b>[</b>������<b>]</b>":"")."";
   	$lastid++;
}

if (get_user_class() >= UC_MODERATOR){
	
	$body2 = $row["name"];
    $name = strlen($row["name"])>22?(substr($row["name"],0,22).""):$body2;
	    	

	$name = preg_replace("#\([0-9]{4}\)#is","",preg_replace("#\[(.+?)\]#is","",$name ));
    $name = str_replace(" - ", " ", preg_replace("# ([a-zA-Z0-9]{1,5})Rip#is","",$name));
    $name = preg_replace("#\([0-9]{1,4}.+[0-9]{1,4}\)#is","",preg_replace("#by (.+?)$#is","",$name));
  //  $name = trim(sqlwildcardesc(htmlspecialchars(preg_replace("#[\.,\\/\?\(\)\!\`\~]#is","",$name))));
    $name = str_replace(" ","%",$name);
//die($name);
    $sql = sql_query("SELECT off_req.name, off_req.numfiles,off_req.views,off_req.owner,off_req.added,off_req.id, off_req.size, categories.name AS cat_name, categories.id AS cat_id, s.username,s.class FROM off_reqs AS off_req LEFT JOIN categories ON off_req.category = categories.id  LEFT JOIN users AS s ON off_req.owner = s.id WHERE off_req.name LIKE '%".$name."%' AND off_req.id <>".$row['id']." LIMIT 10") or sqlerr(__FILE__,__LINE__);
	 $sc=0;
    while($t = mysql_fetch_array($sql)) {

	 if (get_user_class() >= UC_MODERATOR)(
	$mod = " <a href=detailsoff.php?id=".$t['id']."&edit=1><b>[</b><font color=\"green\">�������������</font><b>]</b></a>");

    $ono="<b>����� ������</b>: ".$t["numfiles"]." <b>����������</b>: ".$t["views"]."<br>
	<b>��� �����</b>: <a href=userdetails.php?id=" .$t["owner"] . ">" .get_user_class_color($t["class"],  $t["username"]). "</a> <b>���� �������</b>: ".normaltime($t["added"],true)." $mod<br>";
    $t_likes.= ($t_likes ? "<br>" : "")."
	<a title=\"������ �� ����� �� - ".($sc+1)." �������\" href=\"detailsoff.php?id=".$t['id']."\">".format_comment($t['name'])."</a> <b>[</b><a title=\"�������� ��� ����� �� ���� ���������\" href=\"browse.php?incldead=1&cat=".$t['cat_id']."\">".$t['cat_name']."</a><b>]</b> <b>������</b>: ".mksize($t['size'])."
	<b>[</b><a title=\"�� ������� - �� ������� ��������� ����������\" href=\"javascript: klappe_news('a$t[id]')\">���������</a><b>]</b>
<div id=\"ka$t[id]\" style=\"display: none;\">$ono</div>";
$sc++;
}
}


	print("<form name=\"edit\" method=post action=takeeditoff.php enctype=multipart/form-data>\n");
	print("<input type=\"hidden\" name=\"id\" value=\"$id\">\n");
	if (isset($_GET["returnto"]))
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($_GET["returnto"]) . "\" />\n");
	print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");
	print("<tr><td class=\"colhead\" colspan=\"2\">������������� ������ | �����������: " . $row["name"] . "</td></tr>");
	
//$fname = $row["filename"]; 
//$fname = explode(".torrent", $fname); 
//$fnamenew = $fname[0].".[$SITENAME].torrent";  

  
tr ("��������",$row["name"],1);
 
if ($lastid<>0){
print("<tr><td align=\"right\"><b>����������� ��� ������: $lastid</b></td><td align=\"left\">$title_who_s</td></tr>\n");
}

if($t_likes && get_user_class() > UC_MODERATOR){
tr("������� �����: $sc �� <a title=\"����� �� 10 ��������� ��� ������� �������� �����\">10</a>", "$t_likes", 1);
}


/*	tr($tracker_lang['torrent_file'], "<input type=file name=tfile size=80>
	<br>
	������������ ������ .torrent �� ������ ��������� <b>".mksize($max_torrent_size)."</b>\n", 1);

	tr("���������", "".($row["private"] == "yes" ? "�� <b>[</b>��������� �������: DHT, ����� ������, ����� ��������� �����<b>]</b>" : "��� <b>[</b>�������� �������: DHT, ����� ������, ����� ��������� �����<b>]</b>")."", 1);
	
	*/
	tr($tracker_lang['torrent_name'], "<input type=\"text\" name=\"name\" value=\"" . $row["name"] . "\" size=\"80\" />", 1);

if ((strpos($row["descr"], "<") === false) || (strpos($row["descr"], "&lt;") !== false))
  $c = "";
else
  $c = " checked";

//	<a title=\"���������\" class=\"editorbutton\" OnClick=\"RowsTextarea('area',1)\"><img title=\"��������� ����\" src=\"pic/editor/plus.gif\"></a><a title=\"���������\" class=\"editorbutton\" OnClick=\"RowsTextarea('area',0)\"\"><img title=\"��������� ����\" src=\"pic/editor/minus.gif\"></a>


	print("<tr><td align=\"right\" valign=\"top\"><b>".$tracker_lang['description']."</b></td><td>");
	textbbcode("edit","descr",htmlspecialchars($row["descr"]));
	print("</td></tr>\n");

	$s = "<select name=\"type\">\n";
	$cats = genrelist();
	foreach ($cats as $subrow) {
		$s .= "<option value=\"" . $subrow["id"] . "\"";
		if ($subrow["id"] == $row["category"])
			$s .= " selected=\"selected\"";
		$s .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
	}

	$s .= "</select>\n";
	
	tr("���", $s, 1);
	
	 if(get_user_class() >= UC_ADMINISTRATOR)
        tr("����� ����", "
        <input type=\"checkbox\" name=\"up_date\" value=\"yes\"><b>�������� ���� ���������� ������� �� �����������</b><br>���������� ���� ������ ��� �������������! � �������, �� ����������� �������� ������(�������� �������-����, �������� ����� ����� � �.�.). ��� ���������� ���� ������ ����� ������ ����� �������� ���� ��������.", 1);  


        tr("������", "
         <input type=\"text\" size=\"10\" name=\"size\"> <select name=\"formatdown\">\n<option value=\"mb\">��������</option>\n<option value=\"gb\">��������</option></select>
        <b>������� ��������� ������ �������������� �����</b>.", 1); 
         
		 tr("����� ������", "<input type=\"text\" size=\"10\" name=\"numfiles\"> <b>������� �������� ���������� ������</b>.", 1); 
        
	 if(get_user_class() >= UC_ADMINISTRATOR)
print("<tr><td align=\"right\"  valign=\"top\" class=\"heading\">��������� ������</td><td colspan=\"2\" align=\"left\"><input type=checkbox name=user_reliases_anonim value=1>������ ������ ��� ������ (��� ������)</td></tr>\n");

        
	 if(get_user_class() >= UC_ADMINISTRATOR)
print("<tr><td align=\"right\"  valign=\"top\" class=\"heading\">����� ���������� �������</td><td colspan=\"2\" align=\"left\"><input type=checkbox name=user_no_perf value=1>��������� � ���������� ������� ���������</td></tr>\n");
		  
		  
		  
		  
	  if(get_user_class() >= UC_ADMINISTRATOR)
print("<tr>
        <td align=\"right\" valign=\"top\" class=\"heading\">��������� ������</td><td colspan=\"2\" align=\"left\">������� ������������� (<b>id</b>) ������������: <input type=\"text\" size=\"8\" name=\"release_set_id\"></tr></td>");

	 if(get_user_class() >= UC_ADMINISTRATOR)
        tr("������� �����������", "<input type=\"checkbox\" name=\"delete_comment\" value=\"yes\"> ��� ����������� ������� �������� ����� �������.<br>", 1);  
		

if (get_user_class() >= UC_MODERATOR){

$torrent_com = htmlspecialchars($row["torrent_com"]);
	print("<tr><td align=\"right\"  valign=\"top\" class=\"heading\">������� ��������</td><td colspan=2 align=left><textarea cols=75 rows=6".(get_user_class() < UC_SYSOP ? " readonly" : " name=torrent_com").">$torrent_com</textarea></td></tr>\n");

	print("<tr><td align=\"right\"  valign=\"top\" class=\"heading\">�������� �������</td><td colspan=2 align=left><textarea cols=75 rows=3 name=torrent_com_zam></textarea></td></tr>\n");
}



  tr("��������", "
   <input type=\"checkbox\" name=\"reacon\" value=\"1\"/>
  &nbsp;<b>������� ������ �� �������</b>:<br>
  <input name=\"reasontype\" type=\"radio\" value=\"1\">&nbsp;<b>�������, ��������<br>
  <input name=\"reasontype\" type=\"radio\" value=\"2\">&nbsp;<b>��������</b>: <input type=\"text\" size=\"40\" name=\"dup\">&nbsp;<i>������� id �������� �������</i><br>
 <input name=\"reasontype\" type=\"radio\" value=\"3\">&nbsp;<b>�������</b>: <input type=\"text\" size=\"40\" name=\"rule\">&nbsp;<i>������� ���������</i><br>
  ", 1);


	print("<tr><td class=\"a\" colspan=\"2\" align=\"center\">

	<input type=\"submit\" value=\"��������������\" style=\"height: 25px; width: 120px\">
	<input type=reset value=\"�������� ���������\" style=\"height: 25px; width: 120px\"></form>
	
	<form method=\"post\" action=\"detailsoff.php?id=$id\"><input type=\"submit\" value=\"��������� � ������\" style='height: 25px; width: 120px'></form></td></tr>\n");
	
	print("</table>\n");
	print("\n");

stdfoot();
die;
}




}




$res = sql_query("SELECT off_reqs.category, off_reqs.numratings, off_reqs.name,  IF(off_reqs.numratings < $minvotes, NULL, ROUND(off_reqs.ratingsum / off_reqs.numratings, 1)) AS rating, off_reqs.owner, off_reqs.descr, off_reqs.torrent_com,  off_reqs.size, off_reqs.added, off_reqs.views, off_reqs.id, off_reqs.data_perf,
 off_reqs.numfiles, categories.name AS cat_name, users.username, groups.image,users.class,
 off_reqs.fulfilled,off_reqs.perform,off_reqs.ful_id,
 u.username AS user_n, u.class AS user_c, u.id AS user_i, torrents.id AS tor_id,torrents.name AS tor_name
 FROM off_reqs 
LEFT JOIN categories ON off_reqs.category = categories.id 
LEFT JOIN users ON off_reqs.owner = users.id 
LEFT JOIN users AS u ON off_reqs.fulfilled = u.id 
LEFT JOIN groups ON users.groups = groups.id 
LEFT JOIN torrents ON torrents.id = off_reqs.ful_id

WHERE off_reqs.id = $id")  or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);

$free_class=$row["free_class"];
$free_username=$row["free_username"];
$free_gender=$row["free_gender"];

$size_to_go=$row["size"];

$owned = $moderator = 0;
      if (get_user_class() >= UC_MODERATOR)
           $owned = $moderator = 1;
      else if ($CURUSER["id"] != $row["owner"])
           $owned = 1;

//if ($_GET["page"])
//header("Location: $DEFAULTBASEURL/detailsoff.php?id=$id&page=".$_GET["page"]."#pagestart");

if (!$row || ($row["banned"] == "yes" && !$moderator))
{
if ($row["banned"] == "yes")
stderr("������ ��������", "������� <b>".$row["name"]."</b> �������!");
else 
stderr("������", "��� ������� � ������ ID");
}
else {

if ($_GET["id"]) 
{
sql_query("UPDATE off_reqs SET views = views + 1 WHERE id = $id");
}

if ($_GET["hit"] && !$_GET["tocomm"]) 
{
if ($_GET["tocomm"])
header("Location: $DEFAULTBASEURL/detailsoff.php?id=$id&page=0#startcomments");
else
header("Location: $DEFAULTBASEURL/detailsoff.php?id=$id");
exit();
}
print"
<link rel=\"stylesheet\" href=\"js/lightbox.css\" type=\"text/css\" media=\"screen\" />
<link rel=\"stylesheet\" href=\"js/starbox.css\" type=\"text/css\" media=\"screen\" />
"; 

    //  if (isset($_GET["page"])) {
           stdhead("������ | ����������� " . $row["name"] . "\"");

           if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
                $owned = 1;
           else
                $owned = 0;

           $spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

           $prive = "";
           $s=$row["name"];




//�������� �� �������������

$resour= sql_query("SELECT checkid FROM checkcomm WHERE userid = $CURUSER[id] AND checkid = $id AND offer = 1") or sqlerr(__FILE__, __LINE__);
$arr_res = mysql_fetch_array($resour);

$checkcomm=$arr_res["checkid"];

//print"$checkcomm � $bookcomm";

 if (!$checkcomm)(
 $check = "<a class=\"altlink_white\" href=commentoff.php?action=check&amp;tid=$id><b>�������� ��������</a>") ;
 else
 $check = "<a class=\"altlink_white\" href=commentoff.php?action=checkoff&amp;tid=$id>��������� ��������</a>";
 
 

           print("<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");
           print("<tr><td class=\"colhead\" colspan=\"2\">
<div style=\"float: left; width: auto;\">:: ������ ������� | ����������� :: <a class=\"altlink_white\" href=uploadoff.php>������ ������ ������</a> :: <a class=\"altlink_white\" href=detailsoff.php>��� �������</a> </div>
		   <div align=\"right\">$check</div>
		   </td></tr>");// $bookmarks
		   
		   
           $url = "detailsoff.php?id=" . $row["id"]."&edit=1";
           if (isset($_GET["returnto"])) {
                $addthis = "&amp;returnto=" . htmlspecialchars($_GET["returnto"]);
                $url .= $addthis;
                $keepget .= $addthis;
           }

// ������ � ����� ��������
//$fname = $row["filename"]; 
//$fname = explode(".torrent", $fname); 
//$fnamenew = $fname[0].".[$SITENAME].torrent";  

$name7 = "". format_comment($row['name']) . "";
//$row["name"]=strlen($row["name"])>70?(substr($row["name"],0,70)."..."):$name7; 



	$cat=$row["category"];
    $cat_user=$CURUSER["catedit"];


if ((get_user_class() <= UC_UPLOADER) ||  ($cat_user<>"" && !stristr("$cat_user", "[cat$cat]") && get_user_class() == UC_MODERATOR) && $CURUSER["id"] <> $row["owner"]
	)
	{
    $edit_link="<b>[</b>".$tracker_lang['edit']."<b>]</b>";	
    }
    else
	{
    $edit_link="<a title=\"������ �� �����!\" href=\"$url\"><b>[</b>".$tracker_lang['edit']."<b>]</b></a>";	
    }
    
//print "<tr><td align=\"center\" width=\"99%\" colspan=\"2\" style=\"padding: 15px; font-weight: bold;\" class=\"success\">������ ���� ��������� � ������ ����������.</td></tr>";



 $s = "$row[name] $spacer $edit_link";
 tr ("<nobr>��������</nobr>", $s, 1, 1, "10%");

           if (!empty($row["descr"]))
           tr($tracker_lang['description'], format_comment($row["descr"]), 1, 1);
                      
$torrent_com = htmlspecialchars($row["torrent_com"]);
if (get_user_class() >= UC_MODERATOR && !$torrent_com==""){


	print("<tr><td align=right><b>������� �������</b></td><td colspan=2 align=left><textarea cols=75 rows=6 readonly>$torrent_com</textarea></td></tr>\n");
}
           
		   	if (isset($row["cat_name"]))
                tr("���������", $row["cat_name"]."");
           else
                tr("���������", "(".$tracker_lang['no_choose'].")");
                
           tr($tracker_lang['size'], 
		   "".(empty($row["size"])? "�� ������":mksize($row["size"]) . " (" . number_format($row["size"]) . " ".$tracker_lang['bytes'].")"));

         //[$size_to_go] 
           tr("������", 
		   "".(empty($row["numfiles"])? "�� �������":$row["numfiles"]));




          $s = "";
        if ($CURUSER && $sgd) {

                $xres = sql_query("SELECT off_req, added FROM ratings WHERE off_req = $id AND user = " . $CURUSER["id"])  or sqlerr(__FILE__, __LINE__);
                $xrow = mysql_fetch_array($xres);
  
               if (!isset($xrow))
              $xrow=true;
                if ($CURUSER["id"]==$row["owner"])
                 $xrow=true;
            
            $s .= "<div id=\"ajax_vote\" style=\"font-family:Arial,Helvetica,sans-serif;font-size:0.8em;\"></div>";
            $s .= "<script language=\"javascript\" type=\"text/javascript\" src=\"js/ajax.js\"></script>
			<script language=\"javascript\" type=\"text/javascript\">";
            $s .= "new Starbox('ajax_vote', " . (float) $row["rating"] . ", {";
            $s .= " onRate:function(element, memo) {";
            $s .= " var ajax = new tbdev_ajax();";
            $s .= "ajax.method = 'POST';";
            $s .= "ajax.requestFile = 'takerate.php';";
            $s .= "ajax.setVar('torrentid', " . $id . ");";
            $s .= "ajax.setVar('rating', memo['rated']);";
            $s .= "ajax.setVar('ajax', 'yes');";
            $s .= "ajax.setVar('off_req', 'yes');";
            $s .= "ajax.sendAJAX();";
            $s .= "},";
            $s .= "className:'pointy',";
            $s .= "indicator:'" . $tracker_lang["rating"] . " #{average} " . $tracker_lang['from'] . " #{max} ( #{total} " . $tracker_lang[(total == 1 ?'voted_one':'voted_more')] . ")',";
            $s .= "max:5,";
            $s .= "buttons:10,";
            $s .= "total:" . (int) $row["numratings"] . ",";
            $s .= "rated:" . (!isset($CURUSER) || $xrow?"true":"false") . ",";
            $s .= "rerate:false});";
            $s .= "</script>";
        
    tr($tracker_lang['rating'], $s, 1);
		}
	
         
           

           tr($tracker_lang['added'], normaltime($row["added"], true));
           tr($tracker_lang['views'], $row["views"]);
      
         //  tr($tracker_lang['snatched'], $row["times_completed"] . " ".$tracker_lang['times']);

           $keepget = "";
        
if ($row["perform"]=="no" && empty($row["full_id"])) {
print("<tr><td align=right><b>���������</b></td><td>
	<form name=\"edit\" method=post action=takeeditoff.php>\n");
print("<input type=\"hidden\" name=\"idi\" value=\"".$row["id"]."\">
	 ������� id �������� <input type=\"text\" size=\"10\" name=\"ful_id\">
	<input type=\"submit\" value=\"��������� ������\" ></form>
	</td></tr>\n");
} else {
$username_a="<a href=userdetails.php?id=" . $row["user_i"] . ">" .get_user_class_color($row["user_c"], $row["user_n"]). "</a>";
///  u.username AS user_n, u.class AS user_c, u.id AS user_i
$link_torrent="".($row["tor_name"] ?"<a href=details.php?id=" . $row["tor_id"] . ">".$row["tor_name"]."</a>":"ID: " . $row["full_id"] . " <b>[</b>������� �� ������ ��� ������<b>]</b>")."";
print("<tr><td align=right><b>��������</b></td><td><b>�������</b>:  $link_torrent <br><b>�������������</b>: $username_a <br><b>�����</b>: ".normaltime($row["data_perf"], true)."</td></tr>\n");

}


print(isset($row["username"]) ? ("<tr><td align=right><b>����� �������</b></td><td><a href=userdetailsoff.php?id=" . $row["owner"] . ">" .get_user_class_color($row["class"],  htmlspecialchars($row["username"])). "</a>") : "<tr><td align=right><b>����� �������</b></td><td><i>������ [".$row["owner"]."]</i></td></tr>");

// ������
           if ($row["image"])
print("<tr><td align=right><b>������ ������</b></td><td><img src=pic/groups/" . $row["image"] . "></td></tr>\n");
// ������


//$dt = sqlesc(time() - 180);
$url="/detailsoffoff.php?id=$torrentid";
$url_e="/editoff.php?id=$torrentid";
$res_s = sql_query("SELECT DISTINCT uid, username, class FROM sessions WHERE uid<>-1 and time > ".sqlesc(get_date_time(gmtime() - 180))." and url LIKE '$url' ORDER BY time DESC") or sqlerr(__FILE__,__LINE__);
// or url LIKE '%$url_e%'
$lastid=0;
while ($ar_r = mysql_fetch_assoc($res_s)) {

    $username = $ar_r['username'];
    $id_use = $ar_r['uid'];
if ($title_who_s)
$title_who_s.=", ";

   	$title_who_s.= "<a href=\"userdetailsoff.php?id=$id_use\">".get_user_class_color($ar_r["class"], $ar_r["username"]) . "</a>";
   	$lastid++;

}
     if ($lastid<>0){
print("<tr><td align=\"right\"><b>������������� ������: $lastid</b></td><td align=\"left\">$title_who_s</td></tr>\n");    
     }
     
     
                print("</table></p>\n");


 /*   print("<b>-�- <a title='��������� �������' href='random.php?id=$id&option=random'>��������� ��������</a> -�-</b><br>
	<b><a title='���������� �������' href='random.php?id=$id&option=prev'>� ���������� -</a></b>
	 <b>�</b>
	<b><a title='�������� �������' href='random.php?id=$id&option=next'>- ��������� �</a></b><br>"); 

*/


if ($CURUSER["hidecomment"]<>"yes") {
        print("<p><a name=\"startcomments\"></a></p>\n");

        $subres = sql_query("SELECT COUNT(*) FROM comments WHERE offer = $id");
        $subrow = mysql_fetch_array($subres);
        $count = $subrow[0];

if ($CURUSER["postsperpage"]=="0")
{  $limited = 15; } else {$limited = (int) $CURUSER["postsperpage"];}

if (!$count) {

  print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
  print("<tr><td class=a align=\"left\" colspan=\"2\">");
  print("<div style=\"float: left; width: auto;\" align=\"left\"><a name=\"#pagestart\"></a> :: ������ ������������</div>");

  if ($row["comment_lock"] == 'no')
  {
  	if($CURUSER["commentpos"] == 'yes') {
  print("<div align=\"right\"><a href=#comments>�������� �����������</a></div>");
  }
  print("</td></tr><tr><td align=\"center\">");
   
  print("������������ ���. 
  ".($CURUSER["commentpos"] == 'yes' ? "<br>".$CURUSER['username'].", ������� <a href=#comments>��������?</a>":"")."
  ");
  print("</td></tr></table><br>");


  print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
  print("<tr><td class=colhead align=\"left\" colspan=\"2\"> <a name=comments>&nbsp;</a><b>��� ������������</b></td></tr>");}
  print("<tr><td align=\"center\" >");
  


if ($CURUSER["commentpos"] == 'yes'){
 
  print("<form name=comment method=\"post\" action=\"commentoff.php?action=add\">"); 
  print("<center><table border=\"0\"><tr><td class=\"clear\">"); 
print("<div align=\"center\">".textbbcode("comment","msg","", 1) ."</div>"); 
print("</td></tr></table></center>"); 
  print("</td></tr><tr><td  align=\"center\" colspan=\"2\">"); 
  print("<input type=\"hidden\" name=\"tid\" value=\"$row[id]\"/>"); 
  
  	 if (get_user_class() == UC_SYSOP) {
	  print("<div align=\"center\"><b>�����������:&nbsp;&nbsp;</b>
<b>
".get_user_class_color($CURUSER['class'], $CURUSER['username'])."</b>
<input name=\"sender\" type=\"radio\" value=\"self\" checked>&nbsp;&nbsp;
<font color=gray>[<b>System</b>]</font>
<input name=\"sender\" type=\"radio\" value=\"system\"><br>");
}

  print("<input type=\"submit\" class=btn value=\"���������� �����������\" />"); 
  print("</td></tr></form>"); 
}
else
print("<div align=\"center\">��� ���������� ���������, �������������, ������� �����������</div>"); 

  print("</table>"); 
if ($CURUSER["commentpos"] == 'yes')
  {
	$commentbar = "<p align=center><a class=index href=commentoff.php?action=add&amp;tid=$id>�������� ����������</a></p>\n";
	}
        }
        else {
        	
                list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "detailsoff.php?id=$id&", array(lastpagedefault => ceil($count / $limited)) );
// 0.001029   ����� >  0.000921

                $subres = sql_query("SELECT c.id, c.ip, c.text, c.user, c.added, c.editedby, c.editedat, u.avatar, u.warned, u.username, u.enabled, u.title, u.class, u.signature,u.signatrue, u.donor, u.support,u.hiderating, u.supportfor, u.downloaded, u.uploaded, u.last_access, e.username AS editedbyname, e.class AS classbyname FROM comments AS c LEFT JOIN users AS u ON c.user = u.id LEFT JOIN users AS e ON c.editedby = e.id 
				WHERE offer = $id ORDER BY c.id $limit") or sqlerr(__FILE__, __LINE__);
                $allrows = array();
                while ($subrow = mysql_fetch_array($subres))
                        $allrows[] = $subrow;

         print("<table class=main cellspacing=\"0\" cellPadding=\"5\" width=\"100%\" >");
         print("<tr><td class=\"colhead\" align=\"center\" >");
         print("<div style=\"float: left; width: auto;\" align=\"left\"> :: ������ ������������</div>");
       
	     if ($CURUSER["commentpos"] == 'yes'){
         print("<div align=\"right\"><a href=#comments class=altlink_white>�������� �����������</a></div>");
         }
         print("</td></tr>");

         print("<tr><td>");
         print($pagertop);
         print("</td></tr>");
         print("<tr><td>");
                 commenttable($allrows,"commentoff");
         print("</td></tr>");
         print("<tr><td>");
         print($pagerbottom);
         print("</td></tr>");
         print("</table>");



  

  if ($CURUSER["commentpos"] == 'yes'){
  print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
  print("<tr><td class=colhead align=\"center\" colspan=\"2\"><a name=comments></a><b><center>.::: �������� ����������� � �������� :::.</b></center></td></tr>");
  print("<tr><td width=\"100%\" align=\"center\" >");
  //print("���� ���: ");
  //print("".$CURUSER['username']."<p>");
  print("<form name=comment method=\"post\" action=\"commentoff.php?action=add\">");
  print("<center><table border=\"0\"><tr><td class=\"clear\">");
  print("<div align=\"center\">". textbbcode("comment","msg","", 1) ."</div>");
  print("</td></tr></table></center>");
  print("</td></tr><tr><td  align=\"center\" colspan=\"2\">");
  print("<input type=\"hidden\" name=\"tid\" value=\"$id\"/>");
  
  	 if (get_user_class() == UC_SYSOP) {
	  print("<div align=\"center\"><b>�����������:&nbsp;&nbsp;</b>
<b>
".get_user_class_color($CURUSER['class'], $CURUSER['username'])."</b>
<input name=\"sender\" type=\"radio\" value=\"self\" checked>&nbsp;&nbsp;

<font color=gray>[<b>System</b>]</font>
<input name=\"sender\" type=\"radio\" value=\"system\"><br>");

}
  
  print("<input type=\"submit\" class=btn value=\"���������� �����������\" />");
  
	
	
	
  print("</td></tr></form></table>");
 }
        }

}
else 
{
  print("<br><center><table border=\"0\"><tr><td>"); 
  print("<div align=\"center\"><b>�� ��������� ������ ��� ����������� � ���������</b><br>��� ��������� �����, �������� <a title='���� �������� �������' href='my.php'>����</a>.</div>"); 
  print("</td></tr></table></center>"); 

}


}
/*
/// ���� 
14 (queries) - 89.00% (php) - 11.00% (0.0102 => sql) - 1917 �� (use memory) - 0.000830
/// �����
14 (queries) - 87.02% (php) - 12.98% (0.0105 => sql) - 1917 �� (use memory) - 0.001205
*/
stdfoot();

?>