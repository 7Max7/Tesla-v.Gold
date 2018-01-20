<?

/**
 * @author 7Max7
 * @copyright 2009
 */

require_once('include/bittorrent.php'); 
dbconn(); 

@ini_set('display_errors', 'On');

if (get_user_class() < UC_USER){
stderr("Ошибочка", "Дальше еще интереснее");
die();
}
$dt_multi = sqlesc(get_date_time(gmtime() - 86400*14));///86400

if ($_GET["jd1"]){
sql_query("UPDATE torrents SET visible='no' WHERE visible='yes' AND last_action < $dt_multi AND (f_seeders+f_leechers)='0'") or sqlerr(__FILE__,__LINE__);

sql_query("UPDATE torrents SET visible='yes' WHERE visible='no' AND (f_seeders+f_leechers)>'0'") or sqlerr(__FILE__,__LINE__);
die;
}

if ($_GET["jd"]){
$jd="?jd=1";
$where = "multitracker = 'yes' AND multi_time<$dt_multi AND (f_leechers+f_seeders)<=1";
$limit = "LIMIT 10";
} else {
$where = "multitracker = 'yes' AND multi_time='0000-00-00 00:00:00'";
$limit = "LIMIT 25";
}


if (!$_GET["delete"]){

$sqlk = sql_query("SELECT COUNT(*) FROM torrents WHERE $where") or sqlerr(__FILE__,__LINE__);
$toent = mysql_fetch_array($sqlk);

echo "<title>Всего ".$toent[0]."</title>";



global $announce_urls;

require_once(ROOT_PATH.'include/benc.php');
$dsdf=0;
$dt_multi = sqlesc(get_date_time(gmtime() - 36000000));///86400
///0000-00-00 00:00:00 ///multi_time<$dt_multi  ORDER BY (f_seeders + f_leechers) ASC 
$sql = sql_query("SELECT id, info_hash,name FROM torrents WHERE $where ORDER BY ".(rand(2,8)%2==0 ? "added":"info_hash")." ".(rand(1,5)%2==0 ? "DESC":"ASC")." $limit") or sqlerr(__FILE__,__LINE__);
while($torrent = mysql_fetch_array($sql)) {
    $tracker_cache = array(); 
    $f_leechers = 0; 
    $f_seeders = 0; 

    echo "[".get_date_time()."] ";
    foreach($announce_urls as $announce) 
    {
        $response = get_remote_peers($announce, $torrent['info_hash'],true); 
	
        if($response['state']=='ok') 
        {
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
    sql_query("UPDATE torrents SET " . implode(",", $updatef) . " WHERE id = ".$torrent["id"]);
    //implode(",", $updatef)
    
	echo htmlspecialchars($torrent["name"]).": f_seeders = $f_seeders f_leechers = $f_leechers"; 
    echo "<br>"; 
++$dsdf;
}


$h = date('i'); // проверяем минуты
if (($h >= 00 )&&($h <= 10)) // расписание минут
{
sql_query("UPDATE torrents SET visible='no' WHERE visible='yes' AND multitracker='yes' AND (f_seeders+f_leechers)='0'") or sqlerr(__FILE__,__LINE__);

sql_query("UPDATE torrents SET visible='yes' WHERE visible='no' AND multitracker='yes' AND (f_seeders+f_leechers)>='1'") or sqlerr(__FILE__,__LINE__);

sql_query("UPDATE torrents SET f_seeders='0',f_leechers='0' WHERE multitracker='no'") or sqlerr(__FILE__,__LINE__);
}

}


if ($_GET["delete"]){
$dt_mult = sqlesc(get_date_time(gmtime() - 86400*31));///86400
$naum = 0;
$slk = sql_query("SELECT * FROM torrents WHERE multitracker = 'yes' AND multi_time<>'0000-00-00 00:00:00' AND (f_seeders+f_leechers)<=2 AND moderatedby='92' AND visible='no' AND owner='92' AND times_completed<=2 AND (seeders+leechers)=0 AND multi_time<$dt_mult AND torrent_com LIKE '%freetorrents.org%' LIMIT 1000") or sqlerr(__FILE__,__LINE__);
while ($to = mysql_fetch_array($slk)){

sql_query("DELETE FROM torrents WHERE id=".sqlesc($to["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM snatched WHERE torrent=".sqlesc($to["id"])) or sqlerr(__FILE__,__LINE__);	
sql_query("DELETE FROM ratings WHERE torrent=".sqlesc($to["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM checkcomm WHERE checkid=".sqlesc($to["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM bookmarks WHERE torrentid=".sqlesc($to["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM files WHERE torrent=".sqlesc($to["id"])) or sqlerr(__FILE__,__LINE__);

unlink(ROOT_PATH."torrents/$to[id].torrent");

if (!preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $to["image1"]) && !empty($to["image1"]))
unlink(ROOT_PATH."torrents/images/".$to["image1"]);

echo htmlspecialchars($to["name"]).": f_seeders = $to[f_seeders] f_leechers = $to[f_leechers] <br>"; 
++$naum;
}


if ($naum)
echo "<br><b>Удалено $naum неактивных торрентов</b>.<br>";
else
echo "<br><b>0 неактивных торрентов</b>.<br>";



sql_query("UPDATE torrents SET visible='no' WHERE visible='yes' AND multitracker='yes' AND (f_seeders+f_leechers)='0'") or sqlerr(__FILE__,__LINE__);

sql_query("UPDATE torrents SET visible='yes' WHERE visible='no' AND multitracker='yes' AND (f_seeders+f_leechers)>='1'") or sqlerr(__FILE__,__LINE__);

sql_query("UPDATE torrents SET f_seeders='0',f_leechers='0' WHERE multitracker='no'") or sqlerr(__FILE__,__LINE__);
die;
}


if (date('i')%2==0)
@unlink(ROOT_PATH."cache/block-last_files.txt");

if ($_GET["jd"])
echo "<script>setTimeout('document.location.href=\"remote_gets.php$jd\"', 15000);</script>";
else
echo "<script>setTimeout('document.location.href=\"remote_gets.php$jd\"', 15000);</script>";


 
?>