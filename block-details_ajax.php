<?
require_once("include/bittorrent.php");

dbconn(false,true);	
header("Content-Type: text/html; charset=" .$tracker_lang['language_charset']);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

$id = (isset($_POST["tid"]) ? (int) $_POST["tid"]:"");
$multi=(isset($_POST["multi"]) ? (string)$_POST["multi"]:"");

$list = (isset($_POST["list"]) ? (string)$_POST["list"]:"");
$seed=(isset($_POST["seeders"]) ? (string)$_POST["seeders"]:"");
$sna_cho=(isset($_POST["snatlist"]) ? (string)$_POST["snatlist"]:"");
$rati_cho=(isset($_POST["raticho"]) ? (string)$_POST["raticho"]:"");
$snatlist=(isset($_POST["cho"]) ? (string)$_POST["cho"]:"");

$adject=(isset($_POST["adject"]) ? (string)$_POST["adject"]:"");

global $procents;

if($adject == "yes") {

if (empty($id))
die("id ������.");

sleep(2);

$sql = sql_query("SELECT name FROM torrents WHERE id = ".sqlesc($id)." LIMIT 1") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($sql);

$search = htmlspecialchars_uni($row["name"]);

$search = preg_replace("/\(((\s|.)+?)\)/is", "", preg_replace("/\[((\s|.)+?)\]/is", "", $search));

$sear = array("'","\"","%","$");
$search = str_replace($sear, " ", $search);


$list = explode(" ", $search);
$ecr = array("(",")","]","[");
$listrow = array();
$listview = array();
foreach ($list AS $lis){
$idlist = (int) $lis;

if (strlen($lis)<=2 || !empty($idlist)){
//$listrow[] = "-".$lis;
}
elseif (strlen($lis)>=3){
$listrow[] = "+".$lis;
}
//else
//$listrow[] = $lis;

}

$listrow = array_unique($listrow); /// ������� ���������
//print_r($listrow);


if (strlen($search) >= 4){ /// ������ ������ ������ ������ 3 �������� (�� ������ mysql)

$sql=new MySQLCache("SELECT name, id, size FROM torrents WHERE MATCH (torrents.name) AGAINST ('".trim(implode(" ", $listrow))."' IN BOOLEAN MODE) ORDER BY added DESC LIMIT 15", 2*86400,"details_".md5(trim(implode(" ", $listrow))).".txt"); // ���� 3
}
else
die("����� ��������, ������� �������� ������ ��� ������.");

//print_r($listrow);

$num_p = 0;
$nut = 0;
//if (mysql_affected_rows() == 0)
//die("<i>������ ����, ������� ������ ���.</i>");

//while($t = mysql_fetch_array($sql)) {
$pogre = array();

while ($t=$sql->fetch_assoc()){


$name1 = $search;
$name2 = preg_replace("/\(((\s|.)+?)\)/is", "", preg_replace("/\[((\s|.)+?)\]/is", "", $t["name"]));

$proc = @similar_text($name1, $name2);

if ($id<>$t["id"] && $proc >= $procents){

if ($num_p==1)
echo "<br>";
    
echo "<a href=\"details.php?id=".$t['id']."\">".htmlspecialchars_uni($t['name'])."</a> ".($CURUSER ? "<a title=\"�������, ����� ������� ����\" href=\"download.php?id=".$t['id']."\">[".mksize($t["size"])."]</a>":"[".mksize($t["size"])."]")."";

$pogre[] = $proc;
$num_p = 1;
++$nut;

}


}

if (empty($nut)){

if ($proc > $procents)
die("<i>������ ��� - ����� ��������. </i>");
else
die("<i>�������� ������ $proc% ������ �������� $procents% - ����� ��������. </i>");

}
else 
die("<br><small>�������� <a href=\"browse.php?search=".trim(implode(" ", $listrow))."\" title=\"������� � ������� ������.\">������ � ���������</a> �� ".@min($pogre)."% �� ".@max($pogre)."%.</small>");

}

global $CURUSER,$announce_urls,$multihours;

if (empty($CURUSER) && $list <> "off" && $list <> "on")
die;


function dict_check_t($d, $s) {
	$a = explode(":", $s);
	$dd = $d["value"];
	$ret = array();
	foreach ($a as $k) {
		unset($t);
		if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
			$k = $m[1];
			$t = $m[2];
		}
		if (isset($t)) {
			$ret[] = $dd[$k]["value"];
		}
		else
			$ret[] = $dd[$k];
	}
	return $ret;
}

function dict_get_t($d, $k, $t) {
	$dd = $d["value"];
	$v = $dd[$k];
	return $v["value"];
}


function dltable($name, $arr, $torrent) {

global $CURUSER, $tracker_lang;
//$s = "<b>" . count($arr) . " $name</b>\n";
if (!count($arr)){
$s.= "<tr><td class=a colspan=11 align=\"center\">".($name <> $tracker_lang['details_seeding'] ? "��������� (������): 0":"������� (������): 0")."</td></tr>\n";
return $s;
}

$s.= "\n";
$s.= "<tr><td class=a colspan=11 align=\"center\">".($name <> $tracker_lang['details_seeding'] ? "��������� (������): ".count($arr):"������� (������): ".count($arr))."</td></tr>\n";


$now = time();
$moderator = (isset($CURUSER) && get_user_class() >= UC_MODERATOR);
$mod = get_user_class() >= UC_MODERATOR;

foreach ($arr as $e) {

$s .= "<tr>\n";

if ($e["username"])
$s .= "<td><a href=\"userdetails.php?id=$e[userid]\"><b>".get_user_class_color($e["class"], $e["username"])."</b></a>".($mod ? "&nbsp;[<span title=\"{$e["ip"]}\" style=\"cursor: pointer\">IP</span>]" : "")."</td>\n";
else
$s .= "<td>" . ($mod ? $e["ip"] : preg_replace('/\.\d+$/', ".xxx", $e["ip"])) . "</td>\n";
    $secs = max(10, ($e["la"]) - $e["pa"]);
    $revived = $e["revived"] == "yes";
    
    if (empty($e["port"]))
    $e["port"]="no";
    
    if (get_user_class() <= UC_MODERATOR) {

    	$s .= "<td align=\"center\">" . ($e["connectable"] == "yes" ? "<span style=\"color: green; cursor: help;\" title=\"���� ������. ���� ��� ����� ����������� � ������ ����.\">".$tracker_lang['yes']."</span>" : "<span style=\"color: red; cursor: help;\" title=\"���� ������. ������������� ��������� ��������� Firewall'� � ������.\">".$tracker_lang['no']."</span>") . "</td>\n";
    }
    else {
  	$s .= "<td align=\"center\">" . ($e["connectable"] == "yes" ? "<span style=\"color: green; cursor: help;\" title=\"���� ������. ���� ��� ����� ����������� � ������ ����.\">".$e["port"]."</span>" : "<span style=\"color: red; cursor: help;\" title=\"���� ������. ������������� ��������� ��������� Firewall'� � ������.\">".$e["port"]."</span>") . "</td>\n";
     }
        
$s .= "<td align=\"right\"><nobr>" . mksize($e["uploaded"]) . "</nobr></td>\n";
$s .= "<td align=\"right\"><nobr>" . mksize($e["uploadoffset"] / $secs) . "/s</nobr></td>\n";
$s .= "<td align=\"right\"><nobr>" . mksize($e["downloaded"]) . "</nobr></td>\n";
           //if ($e["seeder"] == "no")
$s .= "<td align=\"right\"><nobr>" . mksize($e["downloadoffset"] / $secs) . "/s</nobr></td>\n";
           /*else
                $s .= "<td align=\"right\"><nobr>" . mksize($e["downloadoffset"] / max(1, $e["finishedat"] - $e["st"])) . "/s</nobr></td>\n";*/
if ($e["downloaded"]) {
$ratio = floor(($e["uploaded"] / $e["downloaded"]) * 1000) / 1000;
$s .= "<td align=\"right\"><font color=" . get_ratio_color($ratio) . ">" . number_format($ratio, 3) . "</font></td>\n";
} else

if ($e["uploaded"])
$s .= "<td align=\"right\">Inf.</td>\n";
else
$s .= "<td align=\"right\">---</td>\n";
$s .= "<td align=\"right\">" . sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"]))) . "</td>\n";
$s .= "<td align=\"right\">" . mkprettytime($now - $e["st"]) . "</td>\n";
$s .= "<td align=\"right\">" . mkprettytime($now - $e["la"]) . "</td>\n";
$s .= "<td align=\"left\">" . htmlspecialchars(getagent($e["agent"], $e["peer_id"])) . "</td>\n";
$s .= "</tr>\n";
}
return $s;
}

/*if (!$CURUSER)
 {
stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}*/


if ($multi=="yes" && get_user_class() >= UC_MODERATOR){
	
require_once ROOT_PATH."include/benc.php";

$sql = sql_query("SELECT info_hash FROM torrents WHERE id=$id") or sqlerr(__FILE__,__LINE__);
while($torrent = mysql_fetch_array($sql)) {

    $tracker_cache = array();
    $f_leechers = 0;
    $f_seeders = 0;
    
    foreach($announce_urls as $announce) {
        $response = get_remote_peers($announce, $torrent['info_hash'],true);
    
        if($response['state']=='ok'){
         $tracker_cache[] = $response['tracker'].':'.($response['leechers'] ? $response['leechers'] : 0).':'.($response['seeders'] ? $response['seeders'] : 0).':'.($response['downloaded'] ? $response['downloaded'] : 0); 
           // $f_leechers += $response['leechers']; 
            //$f_seeders += $response['seeders']; 
            if ($f_leechers < $response['leechers']) /// ��� ����� max ��������
            $f_leechers = $response['leechers'];
            
            if ($f_seeders < $response['seeders']) /// ��� ����� max ��������
            $f_seeders = $response['seeders']; 
        }
        else 
            $tracker_cache[] = $response['tracker'].':'.$response['state']; 
    }

    $fpeers = $f_seeders + $f_leechers;
    $tracker_cache = implode("\n",$tracker_cache);
    $updatef = array();
    $updatef[] = "f_trackers = ".sqlesc($tracker_cache);
    $updatef[] = "f_leechers = ".sqlesc($f_leechers);
    $updatef[] = "f_seeders = ".sqlesc($f_seeders);
    $updatef[] = "multi_time = ".sqlesc(get_date_time());
    $updatef[] = "visible = ".sqlesc(!empty($fpeers) ? 'yes':'no');
    sql_query("UPDATE torrents SET " . implode(",", $updatef) . " WHERE id = $id");
    //implode(",", $updatef)
    die("<b>����� ������ � �����</b>: <br>".str_replace("\n", "<br>", $tracker_cache));
}

}
elseif ($multi == "no"){

require_once ROOT_PATH."include/benc.php";

$res = sql_query("SELECT name FROM torrents WHERE id = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($res);

$fn = $torrent_dir."/".$id.".torrent";

if (!$row || !is_file($fn) || !is_readable($fn))
die("������ ������� ������� ����.");;

$dict = bdec_file($fn, (1024*1024));
list($info) = dict_check_t($dict, "info");

echo "<fieldset><legend>���������� ������ �� ��������</legend>";

echo "<b>���</b>: ".sha1($info["string"])."<br>";
echo "<b>������� ������</b>: ".get_date_time($dict["value"]["creation date"]["value"])." (".normaltime(get_date_time($dict["value"]["creation date"]["value"]),true).")<br>";

echo "<b>���������</b>: ".(!empty($dict['value']['info']['value']['private']) ? "<font color=red><b>��</b></font>":"<font color=green><b>���</b></font> [DHT, ����� ������, ����� ��������� �����]")."<br>";

echo "<b>��������� ��������</b>: ".$dict["value"]["created by"]["value"]."<br>";
echo "<b>��������� ��������</b>: ".$dict["value"]["encoding"]["value"]."<br>";

if (!empty($dict["value"]['comment']))
echo "<b>���������� � �������-�����</b>: ".htmlspecialchars($dict["value"]['comment']["value"])."<br>";

echo "<b>������ ��������</b>: ".mksize($dict['value']['info']['value']['piece length']['value'])."<br>";
echo "<b>������ .torrent</b>: ".mksize(filesize($fn))."<br>";

if (!empty($dict['value']['info']['value']['length']['value']))
echo "<b>������ ������ ������ ��������</b>: ".mksize($dict['value']['info']['value']['length']['value'])."<br>";

//echo ($dict["value"]["info"]["value"]["pieces"]["strlen"]); - ������

//echo ($dict['value']['info']['value']['piece length']['value']/$dict["value"]["info"]["value"]["pieces"]["strlen"]);


echo "</fieldset>";
}

if ($list == "off"){
/// ��������������� �������
$sres = sql_query("SELECT info_hash,f_seeders,f_leechers,multi_time,f_trackers,multitracker FROM torrents WHERE id = $id");
$row = mysql_fetch_array($sres);

$dt_multi = get_date_time(gmtime() - $multihours*3600); // �������� ���������� ����� �� �������
//// ������������
if ($row["multi_time"]<$dt_multi && $row["multitracker"]=="yes" && !empty($multihours)){
//global $announce_urls;
require_once(ROOT_PATH.'include/benc.php');

$tracker_cache = array();
$f_leechers = 0;
$f_seeders = 0;

foreach($announce_urls as $announce) {
$response = get_remote_peers($announce, $row['info_hash'],true); 
if($response['state']=='ok'){
$tracker_cache[] = $response['tracker'].':'.($response['leechers'] ? $response['leechers'] : 0).':'.($response['seeders'] ? $response['seeders'] : 0).':'.($response['downloaded'] ? $response['downloaded'] : 0); 
// $f_leechers += $response['leechers']; 
// $f_seeders += $response['seeders']; 
if ($f_leechers < $response['leechers'])
$f_leechers = $response['leechers'];

if ($f_seeders < $response['seeders'])
$f_seeders = $response['seeders']; 
}
else 
$tracker_cache[] = $response['tracker'].':'.$response['state']; 
}


    $fpeers = $f_seeders + $f_leechers;
    $tracker_cache = implode("\n",$tracker_cache);
    $updatef = array();
    $updatef[] = "f_trackers = ".sqlesc($tracker_cache);
    $updatef[] = "f_leechers = ".sqlesc($f_leechers);
    $updatef[] = "f_seeders = ".sqlesc($f_seeders);
    $updatef[] = "multi_time = ".sqlesc(get_date_time());
    $updatef[] = "visible = ".sqlesc(!empty($fpeers) ? 'yes':'no');
    sql_query("UPDATE torrents SET " . implode(",", $updatef) . " WHERE id = $id");
    //implode(",", $updatef)

$row["f_seeders"] = $f_seeders;
$row["f_leechers"] = $f_leechers;
$row["multi_time"] = get_date_time();
$row["f_trackers"] = $tracker_cache;
}

///list($tracker,$checka)=explode(":",$trackersss);///.":".($checka=="false"?"������":"�������")
$view_trackers = "<fieldset><legend>������� ������� �� ".($row["multi_time"])."</legend>".str_replace("\n", "<br>", $row["f_trackers"])."
".($CURUSER ? "<hr><a style=\"cursor: pointer;\" onclick=\"getmt('" .$id. "');\">[�������� ������ / �������]</a><div id=\"mt_" .$id. "\"></div>":"")."</fieldset>";

echo ($row["multitracker"]=="yes" ? "<b><font color=\"".linkcolor($row["f_seeders"])."\">".$row["f_seeders"]."</font></b> ".$tracker_lang['seeders_l'].", <b><font color=\"".linkcolor($row["f_leechers"])."\">".$row["f_leechers"]."</font></b> ".$tracker_lang['leechers_l']." = <b>" . ($row["f_seeders"] + $row["f_leechers"]) . "</b> ".$tracker_lang['peers_l']."<br>".$view_trackers:"��������");
///<b>[</b>�������������� ��� � 12 �����<b>]</b>

//print_r($_POST);
}
elseif ($list == "on"){

$sql = sql_query("SELECT seeders, leechers, checkpeers FROM torrents WHERE id = ".sqlesc($id)." LIMIT 1") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($sql);



//// �������� ����� ������� ������
//// ������������ �� cleanup ��� ��� ������ �������
$dt_multi = get_date_time(gmtime() - 600); // ������ 10 �����

if ($row["checkpeers"]<$dt_multi) {

$torrents = array();
$res_cle = sql_query("SELECT seeder, COUNT(*) AS c FROM peers WHERE torrent=".sqlesc($id)." GROUP BY torrent, seeder") or sqlerr(__FILE__,__LINE__);
while ($row_cle = mysql_fetch_assoc($res_cle)) {

if ($row_cle["seeder"] == "yes")
$key = "seeders";
else
$key = "leechers";
$torrents[$id][$key] = $row_cle["c"];
}


$res_cle = sql_query("SELECT COUNT(*) AS c FROM comments WHERE id=".sqlesc($id)." GROUP BY torrent") or sqlerr(__FILE__,__LINE__);
while ($row_cle = mysql_fetch_assoc($res_cle)) {
$torrents[$id]["comments"] = $row_cle["c"];
}

$fields = explode(":", "comments:leechers:seeders");
$res_cle = sql_query("SELECT seeders, leechers, comments FROM torrents WHERE id=".sqlesc($id)) or sqlerr(__FILE__,__LINE__);

while ($row_cle = mysql_fetch_assoc($res_cle)) {
$torr = (isset($torrents[$id]) ? $torrents[$id]:""); 

foreach ($fields as $field) {
if (!isset($torr[$field]))
$torr[$field] = 0;
}

$update = array();
foreach ($fields as $field) {
$update[] = $field." = " . $torr[$field];
/// ������ ����� ��������� � ��������
if ($field=="leechers")
$row["leechers"]=$torr[$field];
elseif ($field=="seeders")
$row["seeders"]=$torr[$field];
/// ������ ����� ��������� � ��������
}

if (count($update)){

$update[] = "checkpeers=".sqlesc(get_date_time());
sql_query("UPDATE torrents SET " . implode(", ", $update) . " WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
}
}
//// ������������ �� cleanup ��� ��� ������ �������
sql_query("UPDATE torrents SET seeders='0', leechers='0' WHERE checkpeers='0000-00-00 00:00:00' LIMIT 10") or sqlerr(__FILE__,__LINE__);
}
//// �������� ����� ������� ������

echo "<b><font color=\"".linkcolor($row["seeders"])."\">".($row["seeders"])."</font></b> ".$tracker_lang['seeders_l'].", <b><font color=\"".linkcolor($row["leechers"])."\">".($row["leechers"])."</font></b> ".$tracker_lang['leechers_l']." = <b>" . ($row["seeders"] + $row["leechers"]) . "</b> ".$tracker_lang['peers_l']." 
".(($row["seeders"] + $row["leechers"])<>0 && $CURUSER ? "&nbsp;<a style=\"cursor: pointer;\" onclick=\"getseed('" .$id. "');\">[�������� / ������ ������]</a><div id=\"seed_" .$id. "\"></div>":"");

//print_r($_POST);

}
elseif ($list == "yes"){

echo "<table class=main border=\"1\" cellspacing=0 cellpadding=\"5\">\n";

$sres = sql_query("SELECT numfiles FROM torrents WHERE id = $id");
$srow = mysql_fetch_array($sres);

$subres = sql_query("SELECT * FROM files WHERE torrent = $id ORDER BY filename ASC");

echo "<tr>
<td class=colhead>����</td>
<td class=colhead align=right>������</td>
</tr>\n";

$s4e=0;
while ($subrow = mysql_fetch_array($subres)) {
if ($s4e % 2 == 0)	{$clas_t_d="class=\"a\"";
} else {
$clas_t_d="class=\"b\"";}
echo "<tr>
<td $clas_t_d>" . $subrow["filename"] ."</td>
<td $clas_t_d align=\"right\">" . mksize($subrow["size"]) . "</td>
</tr>\n";
++$s4e;
}

if (empty($srow["numfiles"]) && !empty($s4e)){
//echo "<tr><td colspan=\"3\" class=\"b\" align=\"center\">���������� ������...</td></tr>";
sql_query("UPDATE torrents SET numfiles=".sqlesc($s4e)." WHERE id=".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
echo "<tr><td colspan=\"3\" class=\"b\" align=\"center\">������ � ���������� ������ ���������</td></tr>";
}


if (empty($s4e)){

$ifilename = ROOT_PATH."torrents/".$id.".torrent";

if(@file_exists($ifilename)){

$dict = bdec_file($ifilename, 1024000);
list($info) = dict_check_t($dict, "info");
list($dname, $plen, $pieces) = @dict_check_t($info, "name(string):piece length(integer):pieces(string)");

$filelist = array();
$totallen = @dict_get_t($info, "length", "integer");
if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
} else {
	$flist = @dict_get_t($info, "files", "list");
	$totallen = 0;
	
	if (count($flist)){
	
	foreach ($flist as $sf) {
		list($ll, $ff) = @dict_check_t($sf, "length(integer):path(list)");
		$totallen += $ll;
		$ffa = array();
		foreach ($ff as $ffe) {
			$ffa[] = $ffe["value"];
		}
		$ffe = implode("/", $ffa);
		$filelist[] = array($ffe, $ll);
	}
	}
}

$dict=@bdec(@benc($dict)); 
@list($info) = @dict_check_t($dict, "info");
$infohash = sha1($info["string"]);
//die($infohash);
$size=0;
if (!empty($totallen)){
sql_query("DELETE FROM files WHERE torrent = '$id'");

foreach ($filelist as $file) {
$file[0]=utf8_to_win($file[0]);
$size=$size+$file[1];

echo "<tr>
<td $clas_t_d>" . $file[0] ."</td>
<td $clas_t_d align=\"right\">" . mksize($file[1]) . "</td>
</tr>\n";

sql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).",".sqlesc($file[1]).")");
}
}
}

}

echo "</table>\n";

} elseif ($seed=="yes"){

$res = sql_query("SELECT size FROM torrents WHERE torrents.id = $id")  or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);

                $downloaders = array();
                $seeders = array();
                $subres = sql_query("SELECT seeder, finishedat, downloadoffset, uploadoffset, peers.ip, port, peers.uploaded, peers.downloaded, to_go, UNIX_TIMESTAMP(started) AS st, connectable, agent, peer_id, UNIX_TIMESTAMP(last_action) AS la, UNIX_TIMESTAMP(prev_action) AS pa, userid, users.username, users.class FROM peers LEFT JOIN users ON peers.userid = users.id WHERE torrent = $id") or sqlerr(__FILE__, __LINE__);
//   SUM(uploadoffset / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(prev_action))) AS upspeed,
                while ($subrow = mysql_fetch_array($subres)) {
                      if ($subrow["seeder"] == "yes")
                           $seeders[] = $subrow;
                      else
                           $downloaders[] = $subrow;
                }

                function leech_sort($a,$b) {
                      if ( isset( $_GET["usort"] ) ) return seed_sort($a,$b);
                      $x = $a["to_go"];
                      $y = $b["to_go"];
                      if ($x == $y)
                           return 0;
                      if ($x < $y)
                           return -1;
                      return 1;
                }
                function seed_sort($a,$b) {
                      $x = $a["uploaded"];
                                $y = $b["uploaded"];
                                if ($x == $y)
                                        return 0;
                                if ($x < $y)
                                        return 1;
                                return -1;
                        }
usort($seeders, "seed_sort");
usort($downloaders, "leech_sort");

echo "<table width=100% class=main border=0 cellspacing=0 cellpadding=5>\n";

echo "<tr><td class=colhead>".$tracker_lang['user']."</td>" .
"<td class=colhead align=center>".$tracker_lang['port_open']."</td>".
"<td class=colhead align=right>".$tracker_lang['uploaded']."</td>".
"<td class=colhead align=right>".$tracker_lang['ul_speed']."</td>".
"<td class=colhead align=right>".$tracker_lang['downloaded']."</td>" .
"<td class=colhead align=right>".$tracker_lang['dl_speed']."</td>" .
"<td class=colhead align=right>".$tracker_lang['ratio']."</td>" .
"<td class=colhead align=right>".$tracker_lang['completed']."</td>" .
"<td class=colhead align=right>".$tracker_lang['connected']."</td>" .
"<td class=colhead align=right>".$tracker_lang['idle']."</td>" .
"<td class=colhead align=left>".$tracker_lang['client']."</td></tr>\n";

echo dltable($tracker_lang['details_seeding'], $seeders, $row);
echo dltable($tracker_lang['details_leeching'], $downloaders, $row);

echo "</table>\n";
}
elseif ($snatlist=="yes" && $CURUSER["class"]>=UC_MODERATOR)
{
echo "<table class=main border=\"1\" cellspacing=0 cellpadding=\"5\">\n";

$res = sql_query("SELECT size FROM torrents WHERE id = $id")  or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);

$size_to_go=$row["size"];

$subres = sql_query("SELECT s.seeder,s.uploaded,s.downloaded,s.startdat,s.last_action,s.port,s.to_go,s.completedat,s.finished,s.connectable, u.username,  u.class, u.id FROM snatched AS s
INNER JOIN users AS u ON s.userid = u.id  WHERE s.finished='yes' AND s.torrent = $id ORDER BY s.startdat") or sqlerr(__FILE__,__LINE__);
echo"<tr>
<td align=\"center\" class=colhead>�����/����</td>
<td align=\"center\" class=colhead>�����</td>
<td align=\"center\" class=colhead>������</td>
<td align=\"center\" class=colhead>��������</td>
<td align=\"center\" class=colhead>������</td>
<td align=\"center\" class=colhead>��������</td>
<td align=\"center\" class=colhead>��������</td>
<td align=\"center\" class=colhead align=right>��������</td>
</tr>\n";
					  $s4et=0;
                      while ($subrow = mysql_fetch_array($subres)) {
                  $seeder = ($subrow["seeder"]=="yes" ? "<b>���</b>": "<i>���</i>");	
                $who = (isset($subrow["username"]) ? ("<a href=userdetails.php?id=" .$subrow["id"] . ">" .get_user_class_color($subrow["class"],  $subrow["username"]). "</a>") : "<i>[".$subrow["id"]."]</i>");
                
                $s_port= ($subrow["connectable"] == "yes" ? "<span style=\"color: green; cursor: help;\" title=\"���� ������. ���� ��� ����� ����������� � ������ ����.\">".$subrow["port"]."</span>" : "<span style=\"color: red; cursor: help;\" title=\"���� ������. ������������� ��������� ��������� Firewall'� � ������.\">".$subrow["port"]."</span>");
 						
                      	if ($subrow["finished"]=="yes" || $subrow["seeder"]=="yes")
                      	$finish="<b>��</b>"; 	else	$finish="<i>���</i>";
                      	
                      	if ($subrow["completedat"]=="0000-00-00 00:00:00")
                      	$completedat="<i>���</i>"; 	
					    else
						$completedat=$subrow["completedat"];
                      	
                      
                      	if ($subrow["seeder"]=="no"){
        
                      	if ($subrow["to_go"]>"0"){
                      	$size_togo=number_format(100 * (1 - $subrow["to_go"]/$size_to_go),1);
                      	} else
                      $size_togo="0";
                      } else
                      $size_togo="100";
                      
if ($s4et % 2 == 0)	{$clas_td="class=\"a\"";} else {$clas_td="class=\"b\"";	}

echo "<tr>
<td $clas_td align=\"center\">" . $who ." <br>". $s_port."</td>
<td $clas_td align=\"center\">" . mksize($subrow["uploaded"]) . "</td>
<td $clas_td align=\"center\">" . mksize($subrow["downloaded"]) . "</td>
<td $clas_td align=\"center\">" . $size_togo . "% </td>
<td $clas_td align=\"center\">" . $subrow["startdat"] . "</td>
<td $clas_td align=\"center\">" . $subrow["last_action"] . "</td>
<td $clas_td align=\"center\">" . $completedat . "</td>
<td $clas_td align=\"center\">" . $finish . " / $seeder</td>
</tr>\n";
					 $s4et++;
}
echo "</table>\n";
     	  
}
elseif ($sna_cho=="yes"){
$res = sql_query("SELECT users.id, users.username, users.title, users.uploaded, users.downloaded, users.donor, users.enabled, users.warned, users.last_access, users.class, snatched.startdat, snatched.last_action, snatched.completedat, snatched.seeder, snatched.userid, snatched.uploaded AS sn_up, snatched.downloaded AS sn_dn FROM snatched INNER JOIN users ON snatched.userid = users.id 
WHERE snatched.finished='no' AND snatched.torrent =" . sqlesc($id) . " ORDER BY users.class DESC LIMIT 200") or sqlerr(__FILE__,__LINE__);

echo "<table width=100% class=main border=1 cellspacing=0 cellpadding=5>\n";
echo "<tr>
<td class=colhead align=center>�����</td>
<td class=colhead align=center>������</td>
<td class=colhead align=center>������</td>
<td class=colhead align=center>�������</td>
<td class=colhead align=center>����� / ��������</td>
<td class=colhead align=center>��������</td>
<td class=colhead align=center>��������</td>
</tr>";
                    $s4etik=0;
					while ($arr = mysql_fetch_assoc($res)) {
					if ($s4etik % 2 == 0)	{$clas_tdi="class=\"a\"";} else {$clas_tdi="class=\"b\"";	}
						//start Global
						if ($arr["downloaded"] > 0) {
						        $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
								  $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
						}
						else if ($arr["uploaded"] > 0)
						$ratio = "Inf.";
						else
						$ratio = "---";
						$uploaded = mksize($arr["uploaded"]);
						$downloaded = mksize($arr["downloaded"]);
						//start torrent
						if ($arr["sn_dn"] > 0) {
								$ratio2 = number_format($arr["sn_up"] / $arr["sn_dn"], 2);
								$ratio2 = "<font color=" . get_ratio_color($ratio2) . ">$ratio2</font>";
						}
						else
							if ($arr["sn_up"] > 0)
								$ratio2 = "Inf.";
							else
								$ratio2 = "---";
						$uploaded2 = mksize($arr["sn_up"]);
						$downloaded2 = mksize($arr["sn_dn"]);
						//end
				//	$highlight = $CURUSER["id"] == $arr["id"] ? " bgcolor=white" : "";;
$snatched_small[] = "<a href=userdetails.php?id=".$arr["userid"].">".get_user_class_color($arr["class"], $arr["username"])." <b>[</b><font color=" . get_ratio_color($ratio) . ">".$ratio."</font><b>]</b></a>";
echo "<tr".$highlight.">
<td ".$clas_tdi." align=center><a href=userdetails.php?id=".$arr["userid"].">".get_user_class_color($arr["class"], $arr["username"])."</a>".get_user_icons($arr)."</td>
<td ".$clas_tdi." align=center><nobr>".$uploaded."&nbsp;������<br><u>".$uploaded2."&nbsp;�������</u></nobr></td>
<td ".$clas_tdi." align=center><nobr>".$downloaded."&nbsp;������<br><u>".$downloaded2."&nbsp;�������</u></nobr></td>
<td ".$clas_tdi." align=center><nobr>".$ratio."&nbsp;������<br><u>".$ratio2."&nbsp;�������</u></nobr></td>
<td ".$clas_tdi." align=center><nobr>" .$arr["startdat"] . "<br />" . $arr["completedat"] . "</nobr></td>
<td ".$clas_tdi." align=center><nobr>" .$arr["last_action"] . "</nobr></td>
<td ".$clas_tdi." align=center><nobr>" .($arr["seeder"] == "yes" ? "<b><font color=green>��</font>" : "<font color=red>���</font></b>") ."</nobr></td>
</tr>\n"; $s4etik++;
}
echo"</table>\n";
}
elseif ($rati_cho=="yes" && get_user_class() >= UC_MODERATOR){

$res = sql_query("SELECT rat.user,rat.rating,rat.added,us.username,us.class FROM ratings AS rat
LEFT JOIN users AS us ON us.id=rat.user
WHERE rat.torrent =" . sqlesc($id) . " ORDER BY rat.added DESC LIMIT 200") or sqlerr(__FILE__,__LINE__);
	    
echo "<table width=100% class=main border=1 cellspacing=0 cellpadding=5>\n";
echo "<tr>
<td class=colhead align=center>�����</td>
<td class=colhead align=center>����� ������</td>
<td class=colhead align=center>������</td>
</tr>";
$s4etik=0;
while ($arr = mysql_fetch_assoc($res)) {

if ($s4etik % 2 == 0)	{$clas_tdi="class=\"a\"";} else {$clas_tdi="class=\"b\"";}

echo "<tr>
<td $clas_tdi align=center><a href=userdetails.php?id=".$arr["user"].">".get_user_class_color($arr["class"], $arr["username"])."</a></td>
<td $clas_tdi align=center>".$arr["added"]."</td>
<td $clas_tdi align=center>".pic_rating_b(10,$arr["rating"])."</td>
</tr>\n"; 
++$s4etik;
}

if ($s4etik==0)
echo"<tr>
<td align=center>���</td>
<td align=center>���</td>
<td align=center><nobr> ��� </nobr></td>
</tr>\n";


echo"</table>\n";
}elseif ($rati_cho=="thank" && $CURUSER){

$thanked_sql = sql_query("SELECT thanks.userid, users.username, users.class FROM thanks INNER JOIN users ON thanks.userid = users.id WHERE thanks.torrentid = $id") or sqlerr(__FILE__, __LINE__);

$tru = false;
while ($thanked_row = mysql_fetch_assoc($thanked_sql)) {
	
if ($tru==true)
echo ", ";

$userid = $thanked_row["userid"];
$username = $thanked_row["username"];
$class = $thanked_row["class"];

echo "<a href=\"userdetails.php?id=$userid\">".get_user_class_color($class, $username)."</a>";
$tru = true;
++$num_s;
}


//print_r($_POST);
}
else die;




?> 