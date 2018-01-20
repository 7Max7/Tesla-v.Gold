<?
require_once("include/bittorrent.php");

dbconn();

header("Content-Type: text/html; charset=" .$tracker_lang['language_charset']);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

/*if (!$CURUSER)
 {
stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}*/

$tid = (int) $_POST["tid"];

if (!is_valid_id($tid))
die("Неверный идентификатор ".$tid);


if (get_user_class() >= UC_MODERATOR) {
$add=", torrent_com";
}
else
{
$add=", info_hash, multitracker, multi_time ";
}

$query = sql_query("SELECT name, descr, image1 ".(isset($add) ? $add:"")." FROM torrents WHERE id = ".sqlesc($tid));
$row = mysql_fetch_array($query);

if(empty($row))
die("Нет торрента с таким id $tid");

echo("<table cellpadding='7' width='98%' border='0' align='center'>
<style>.effect {FILTER: alpha(opacity=50); -moz-opacity: .50; opacity: .50;}</style>
<tr><td valign='top' align='left'>");


$det = $row["descr"];

/*
$pos_img_tag = strpos($det, "[img]", 1);
//$pos_img_tag = strpos($det, "[img]");
if($pos_img_tag > 0)
{
    $det_cut = substr($det, 0, -(strlen($det) - $pos_img_tag));
    print(format_comment($det_cut));
}
else
*/
echo(format_comment($det));


if (get_user_class() >= UC_MODERATOR && !empty($row["torrent_com"])){

$torrent_com = htmlspecialchars($row["torrent_com"]);
echo("<br><textarea cols=100% rows=8 readonly>$torrent_com</textarea>\n");
}


echo("</td><td valign='middle' align='right' width='185'>");

//$img_tor = (preg_match("/http:/",$row["image1"] ? $row["image1"] : "thumbnail.php?image=$row[image1]&for=getdetals");

if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $row["image1"]))
$img_tor = $row["image1"]; 
else
$img_tor = "thumbnail.php?image=".htmlentities($row["image1"])."&for=getdetals"; 


if (!empty($row["image1"]))
$img = "<img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" border='0' width='180' title='" .$row["name"]. "' src='" .$img_tor. "' />";
else
$img = "<img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" border='0' width='180' title='" .$row["name"]. "' src='torrents/images/default_torrent.png'/>";
echo("<center>" .$img. "</center>");

echo("</td></tr></table><br />");


$dt_multi = get_date_time(gmtime() - (2*$multihours)*3600); //43200

if (get_user_class() < UC_MODERATOR && $row["multi_time"]<$dt_multi && $row["multitracker"]=="yes"){
global $announce_urls;
///echo $row["multi_time"]." и ".$dt_multi." и ".$row["multitracker"];
require_once ROOT_PATH."include/benc.php";

    $tracker_cache = array(); 
    $f_leechers = 0; 
    $f_seeders = 0; 
    $announce_list=$announce_urls;
    foreach($announce_list as $announce) 
    {
        $response = get_remote_peers($announce, $row["info_hash"],true); 
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
    sql_query("UPDATE torrents SET " . implode(",", $updatef) . " WHERE id = $tid");
    //implode(",", $updatef)
}


?> 