<? 

require_once("include/bittorrent.php");

dbconn();
//loggedinorreturn();
//parked();


function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
   
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
   
    $bytes /= pow(1024, $pow); 
   
    return round($bytes, $precision) . '' . $units[$pow]; 
}

function announce_list($announce_urls) {
  for ($i = 0; $i < count($announce_urls); $i++) {
  	
 	if (!empty($announce_urls[$i])) {
    $list[$i][$i] = bdec(benc_str(trim($announce_urls[$i])));
    $list[$i]= bdec(benc_list($list[$i]));
    }
  }
  return bdec(benc_list($list));
}

if (@ini_get('output_handler') == 'ob_gzhandler' AND @ob_get_length() !== false){	
// if output_handler = ob_gzhandler, turn it off and remove the header sent by PHP
	@ob_end_clean();
	header('Content-Encoding:');
}

/*if (!preg_match(':^/(\d{1,10})/(.+)\.torrent$:', $_SERVER["PATH_INFO"], $matches))
	httperr();*/

$id = (int) $_GET["id"];
if (!is_numeric($id))
	stderr($tracker_lang['error'],$tracker_lang['invalid_id']);



/// рефер данные
$referer = (isset($_SERVER["HTTP_REFERER"]) ? htmlspecialchars_uni($_SERVER["HTTP_REFERER"]):"");
if (!empty($referer))
$parse_site = parse_url($referer, PHP_URL_HOST);

/// собственные данные о сайте
$site_own = (($_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://").htmlspecialchars_uni($_SERVER['HTTP_HOST']);
if (!empty($site_own))
$parse_owner = parse_url($site_own, PHP_URL_HOST);

//$parse_owner = str_replace("www.","", $parse_owner);
/// сравниваем данные
if(empty($refer_parse) || ($parse_owner<>$parse_site)){

parse_referer();
stderr($tracker_lang['error'],"<b>Внимание</b>: Прямое скачивание файла запрещено. Вернитесь пожалуйста обратно к описанию <a href=\"details.php?id=".$id."\">релиза</a> и повторите попытку снова.");

}

/// рефер данные



global $announce_urls, $announce_net;

if (empty($announce_net) && !$CURUSER)
stderr($tracker_lang['error'],"Функция скачивания без регистрации - Отключена.");


if ($CURUSER["downloadpos"]=="no" && empty($announce_net)) {
	stderr($tracker_lang['error'],"Вам запрещенно скачивать торренты");	
}
	
$res = sql_query("SELECT multi_time, multitracker, viponly, name, size, stopped, stop_time, webseed, f_trackers FROM torrents WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($res);

if (!$row)
stderr($tracker_lang['error'], "В базе нет названия торрента к такому id");

if (!empty($announce_net) && $row["multitracker"]=="no" && !$CURUSER)
stderr($tracker_lang['error'],"Cкачивание без регистрации возможно только для мультитрекерных раздач.");


// создаем массив проверенных аннонсов
$arrayrow = explode("\n",$row["f_trackers"]);

/// создаем массив "битых" аннонсов по :0:0:0 данным
for($i = 0, $c = count($arrayrow); $i < $c; $i++) {

if (!preg_match("|(:0:0:0)|U", $arrayrow[$i], $li)){
unset($arrayrow[$i]);
} else
$arrayrow[$i] = trim(str_replace(":0:0:0","",$arrayrow[$i]));
}

/// перебираем оригинал аннонсов для удаления "битых"
for($iu = 0, $cu = count($announce_urls); $iu < $cu; $iu++) {

foreach ($arrayrow as $b=>$c) {

if (stristr($announce_urls[$iu],$c)){
unset($announce_urls[$iu]);
//echo "$c <br>";
}
}

}

//print_r($announce_urls);
//die;

////////// приостановка искл босс
if ($row["stopped"]=="yes" && $row["stop_time"]<>"0000-00-00 00:00:00" && get_user_class() < UC_SYSOP){

$subres = sql_query("SELECT id FROM snatched WHERE startdat<".sqlesc($row["stop_time"])." and torrent='$id' and userid=".sqlesc($CURUSER["id"])) or sqlerr(__FILE__,__LINE__);///completedat<>'0000-00-00 00:00:00'
$snatch = mysql_fetch_array($subres);
$snatched=$snatch["id"];

if (empty($snatched)){
stderr($tracker_lang['error'], "Эта раздача приостановленна. <br>Cкачать повторно могут лишь пользователи, взявшие торрент файл до <b>".$row["stop_time"]."</b> указанного времени.");
}

}
////////// приостановка искл босс


if (!empty($row["viponly"])){
//list($viponly, $day) = explode(':', $row["viponly"]);
$reday=display_date_time($row["viponly"]);


if (get_user_class() <> UC_VIP AND get_user_class() < UC_MODERATOR) {

stderr($tracker_lang['error'], "Эта раздача только для Vip пользователей. <br>Возможность скачать файл будет лишь <b>".$reday."</b> числа.");
}
}



$size=formatBytes($row["size"]);
$fname = $row['name']."^(".$size.")";

$ru = array("а","б","в","г","д","е","ё","ж","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ц","ч","ш","щ","ъ","ы","ь","э","ю","я","А","Б","В","Г","Д","Е","Ё","Ж","З","И","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х","Ц","Ч","Ш","Щ","Ъ","Ы","Ь","Э","Ю","Я"," ");
$en = array("a","b","v","g","d","e","e","zg","z","i","i","k","l","m","n","o","p","r","s","t","y","f","h","c","ch","sh","zh","","u","","e","iu","ia","A","B","V","G","D","E","E","Zg","Z","I","K","L","M","N","O","P","R","S","T","Y","F","H","C","Ch","Sh","Zh","", "U","","E","IU","Ia","_");

$fname = str_replace($ru, $en, $fname); 

$rui = array(",",".",";","'","\"","/",":","&","~");

$fname = str_replace($rui, "_", $fname); 

$fname = substr($fname, 0, 128);

$name = $fname."_$SITENAME.torrent";  


$fn = $torrent_dir."/".$id.".torrent";


if (!$row || !is_file($fn) || !is_readable($fn)){

stderr($tracker_lang['error'], $tracker_lang['unable_to_read_torrent']);

}
sql_query("UPDATE torrents SET hits = hits + 1 WHERE id = ".sqlesc($id));

require_once "include/benc.php";




if ($CURUSER){

if (strlen($CURUSER["passkey"]) <> 32) {
	$CURUSER["passkey"] = md5($CURUSER["username"].get_date_time().$CURUSER["passhash"]);
	sql_query("UPDATE users SET passkey=".sqlesc($CURUSER["passkey"])." WHERE id=".sqlesc($CURUSER["id"]));
}

//die($row["f_trackers"]);
//////////////////////// временное правило
$dt_multi = get_date_time(gmtime() - 43200); // пол дня
if ($row["multitracker"]=="yes" && $row["multi_time"]<$dt_multi){

$sql = sql_query("SELECT info_hash FROM torrents WHERE id=$id"); 
while($torrent = mysql_fetch_array($sql)) {
    $tracker_cache = array(); 
    $f_leechers = 0; 
    $f_seeders = 0; 

    foreach($announce_urls as $announce) 
    {
        $response = get_remote_peers($announce, $torrent['info_hash'],true); 
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
}
}
//////////////////////// временное правило

}






$dict = bdec_file($fn, (1024*1024));


//$dict['value']['announce']['value'] = $announce_urls[0]."?passkey=$CURUSER[passkey]";//"$DEFAULTBASEURL/announce.php?passkey=$CURUSER[passkey]";

if ($row["multitracker"]=="yes") {


if ($CURUSER)
$announce_urls[0]=$announce_urls[0]."?passkey=".$CURUSER["passkey"];


$dict['value']['announce-list'] = announce_list($announce_urls);
} elseif ($CURUSER && $row["multitracker"]=="no") {

$dict['value']['comment']=bdec(benc_str("Торрент создан для '$SITENAME'")); // change torrent comment
$dict['value']['comment']=bdec(benc_str("$DEFAULTBASEURL/details.php?id=$id")); // change torrent comment to URL
unset($dict['value']['announce-list']); // remove multi-tracker capability
$dict['value']['announce']['value'] = $announce_urls[0]."?passkey=$CURUSER[passkey]";//"$DEFAULTBASEURL/announce.php?passkey=$CURUSER[passkey]";
}

$dict['value']['announce']['string'] = strlen($dict['value']['announce']['value']).":".$dict['value']['announce']['value'];
$dict['value']['announce']['strlen'] = strlen($dict['value']['announce']['string']);

$webseed=strip_tags($row["webseed"]);
if (!empty($webseed) && strlen($webseed)>=15){
$dict['value']['url-list']=bdec(benc_str($webseed)); // добавление webseed http прямой ссылкой
}


header ("Expires: Tue, 1 Jan 1980 00:00:00 GMT");
header ("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header ("Cache-Control: no-store, no-cache, must-revalidate");
header ("Cache-Control: post-check=0, pre-check=0", false);
header ("Pragma: no-cache");
header ("Accept-Ranges: bytes");
header ("Connection: close");
header ("Content-Transfer-Encoding: binary");
header ("Content-Disposition: attachment; filename=\"".$name."\"");
header ("Content-Type: application/x-bittorrent");
ob_implicit_flush(true);

print(benc($dict));

?>