<?
define ('IN_ANNOUNCE', true);
define ('ROOT_PATH', str_replace("include","",dirname(__FILE__)));

$cheat_on = 1;/// включить слежение за читерами
$debug_time = false; /// or false

if ($debug_time==true){
// считываем текущее время
$start_time = microtime();
// разделяем секунды и миллисекунды
//(становятся значениями начальных ключей массива-списка)
$start_array = explode(" ",$start_time);
// это и есть стартовое время
$start_time = $start_array[1] + $start_array[0];
}

require_once(ROOT_PATH.'/include/core_announce.php');

gzip();
$agent = htmlentities($_SERVER['HTTP_USER_AGENT']);

if((strpos($agent, 'Opera')!== false)||(strpos($agent, 'MSIE')!== false)||(strpos($agent, 'Gecko')!== false)||(strpos($at, 'Netscape')!== false)||(strpos($at, ' I;')!== false)||(strpos($at, ' U;')!== false)||(strpos($at, 'Mozilla/')!== false))
 die("Просмотр аннонса запрещен!");
 
if (preg_match("/^Mozilla\\//i", $agent) || preg_match("/^Opera\\//i", $agent) || preg_match("/^Links/i", $agent) || preg_match("/^Lynx\\//i", $agent))
err($passkey,"Нельзя скачивать торренты через браузер: ".$agent,1);


foreach (array('passkey','info_hash','peer_id','event','ip','localip') as $x) {
	if(isset($_GET[$x]))
	$GLOBALS[$x] = (string)$_GET[$x];
	//	$GLOBALS[$x] = '' . $_GET[$x];
}

/// подключение файла если условие создано
if ($announce_net == 1 && empty($passkey)){
include 'announce_net.php'; /// внешний аннонс подключения
die;
}

foreach (array('port','downloaded','uploaded','left') as $x)
	$GLOBALS[$x] = (float)$_GET[$x];
	/// больше 2 гигов урезка до 2 на 32 битке (проверить слух)

if (get_magic_quotes_gpc()) {
    $info_hash = stripslashes($info_hash);
    $peer_id = stripslashes($peer_id);
}

foreach (array('passkey','info_hash','peer_id','port','downloaded','uploaded','left') as $x)
	if (!isset($x)) err($passkey,"Нет ключа в: ".$x,1);

foreach (array('info_hash','peer_id') as $x)
			if (strlen($GLOBALS[$x]) != 20)
			err($passkey,"Отсутствует ".$x." (".strlen($_GET[$x])." - ".urlencode($_GET[$x]).")",1);

if (empty($passkey) || strlen($passkey) <> 32)
err($passkey,"Неверный пасскей",1);

$ip = getip();
$rsize = 50;

foreach(array('num want', 'numwant', 'num_want') as $k) {
	if (isset($_GET[$k]))
	{
		//$rsize = 0 + $_GET[$k];
		
		$rsize = (float) $_GET[$k];
        if($rsize<0)$rsize=0;  
		
		break;
	}
}

if (!$port || $port > 0xffff)
	err($passkey,"Неверный порт: ".$port,1);
if (!isset($event))
	$event = '';
$seeder = ($left == 0) ? 'yes' : 'no';

if (function_exists('getallheaders'))
	$headers = getallheaders();
else
	$headers = emu_getallheaders();
	
if (isset($headers['Cookie']) || isset($headers['Accept-Language']) || isset($headers['Accept-Charset']))
	err($passkey,"Анти-чит: Вы не можете использовать этот агент",1);

/// не даем доступ к ratiomaster для старых версий
if ("text/html, */*" == $_SERVER["HTTP_ACCEPT"] || "Close" == $_SERVER["HTTP_CONNECTION"] && "gzip, deflate" <> $_SERVER["HTTP_ACCEPT_ENCODING"]){
err($passkey,"Анти-чит: Вы не можете использовать этот агент",1);
}


dbconn();
$sql = mysql_query("SELECT id, invitedby, uploaded, downloaded, hiderating, class, parked, downloadpos, added FROM users WHERE passkey = " . sqlesc($passkey)) or err($passkey,mysql_error().' Line: '.__LINE__,2);

//if (mysql_affected_rows() == 0)
if (mysql_num_rows($sql) == 0)
err($passkey,"Неизвестный пасскей. Пожалуйста перекачайте торрент файл с ".$DEFAULTBASEURL,1);

$ustesla = mysql_fetch_array($sql);


$hash = bin2hex($info_hash);
$res = mysql_query('SELECT id, banned, stopped, stop_time, free, moderated, seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts FROM torrents WHERE info_hash = "'.$hash.'"') or err($passkey,mysql_error()." Line: ".__LINE__,2);
$torrent = mysql_fetch_array($res);
if (!$torrent)
	err($passkey,"Торрент не зарегистрирован на трекере: ".$hash,1);

if ($torrent["banned"]=="yes")
	err($passkey,"Торрент забанен на трекере: ".$torrent["id"],1);

//// приостановка торрента, искл для босса
if ($torrent["stopped"]=="yes" && $torrent["stop_time"]<>"0000-00-00 00:00:00"){

$subres = mysql_query("SELECT s.id, u.class,u.id AS udi FROM users AS u LEFT JOIN snatched AS s ON s.startdat<".sqlesc($torrent["stop_time"])." and s.torrent=".$torrent["id"]." and s.userid=u.id WHERE u.passkey=".sqlesc($passkey)) or err($passkey,mysql_error()." Line: ".__LINE__,2); ///completedat<>'0000-00-00 00:00:00'
$snatch = mysql_fetch_array($subres);

if (empty($snatch["id"]) && $snatch["class"]<"6"){
err($passkey,"Закачка этого торрента приостановленна. Извините за неудобства.",1);
}
//err($passkey," id $snatch[id] - u.class $snatch[class]  и id $snatch[udi] ");
}
//// приостановка торрента, искл для босса


	/// добавленно
if ($torrent["moderated"]=="no")
	err($passkey,"Торрент еще непроверен модератором: ".$torrent["id"],1);

$numpeers=$torrent["numpeers"];

$torrentid = $torrent['id'];
$fields = 'seeder, peer_id, ip, port, uploaded, downloaded, userid, last_action, UNIX_TIMESTAMP(NOW()) AS nowts, UNIX_TIMESTAMP(prev_action) AS prevts, UNIX_TIMESTAMP(last_action) AS ts';
$numpeers = $torrent['numpeers'];
$limit = '';

if ($numpeers > $rsize)
$limit = 'ORDER BY last_action LIMIT '.$rsize;
	
//if ($numpeers > $rsize)
//	$limit = 'ORDER BY RAND() LIMIT '.$rsize;	
	
$res = mysql_query('SELECT '.$fields.' FROM peers WHERE torrent = '.$torrentid.' '.$limit) or err($passkey,mysql_error().' Line: '.__LINE__,2);



$resp = 'd' . benc_str('interval') . 'i' . $announce_interval . 'e' . benc_str('peers') . (($compact = ($_GET['compact'] == 1)) ? '' : 'l');
$no_peer_id = ($_GET['no_peer_id'] == 1);
unset($self);
while ($row = mysql_fetch_array($res)) {
	if ($row['peer_id'] == $peer_id) {
		$userid = $row['userid'];
		$self = $row;
		continue;
	}
	if($compact) {
		$peer_ip = explode('.', $row["ip"]);
		$plist .= pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3]). pack("n*", (int) $row["port"]);
	} else {
		$resp .= 'd' .
			benc_str('ip') . benc_str($row['ip']) .
			(!$no_peer_id ? benc_str("peer id") . benc_str($row["peer_id"]) : '') .
			benc_str('port') . 'i' . $row['port'] . 'e' . 'e';
	}
}
$resp .= ($compact ? benc_str($plist) : '') . (substr($peer_id, 0, 4) == '-BC0' ? "e7:privatei1ee" : "ee");

//$selfwhere = 'torrent = '.$torrentid.' AND peer_id = '.sqlesc($peer_id).' AND passkey = '.sqlesc($passkey).'';
$selfwhere = 'ip = '.sqlesc($ip).' AND agent = '.sqlesc($agent).' AND torrent = '.$torrentid.' AND passkey='.sqlesc($passkey).' AND peer_id = '.sqlesc($peer_id);

if (!isset($self)) {
	$res = mysql_query('SELECT '.$fields.' FROM peers WHERE '.$selfwhere) or err($passkey,mysql_error().' Line: '.__LINE__,2);
	$row = mysql_fetch_array($res);
	if ($row) {
		$userid = $row['userid'];
		$self = $row;
	}
}

$announce_wait = 20;
if (isset($self) && ($self['prevts'] > ($self['nowts'] - $announce_wait )))
	err($passkey,"Минимум к автообновлению аннонса - " . $announce_wait . " секунд",1);

if (!isset($self)) {

/// started / начало закачки

$userid = (int) $ustesla['id'];

if ($ustesla["class"]<4 && $ustesla["hiderating"]=="no" && ($ustesla['downloaded']>=4065082210) && ($ustesla['uploaded']>=61082210) && $ustesla["added"]<get_date_time(gmtime() - 1209600) && $event<>'started') {
$ratio_t = $ustesla['uploaded'] / $ustesla['downloaded'];
$ratio_t = number_format($ratio_t, 3);

if ($ratio_t < "0.3"){
err($passkey,"Рейтинг ".$ratio_t." (меньше 0.3) с ним запрещенно скачивать торренты.",1);
}
}

	
/*
$passkey_ip = $ustesla['passkey_ip'];
if ($passkey_ip != '' && getip() != $passkey_ip)
err($passkey,"Неавторизованный ip адрес для этого пасскея!');
*/

}

else {

/// продолжение / конец закачки

    $hiderating = $ustesla["hiderating"];
	$invitedby = $ustesla['invitedby'];

    $upthis = ($hiderating == 'yes') ? 0 : max(0, $uploaded - $self["uploaded"]);
    $downthis = ($hiderating == 'yes' || $torrent['free'] == 'yes') ? 0 : max(0, $downloaded - $self["downloaded"]);

    mysql_query('UPDATE users SET uploaded = uploaded + '.$upthis.', downloaded = downloaded + '.$downthis.' WHERE id='.$userid) or err($passkey,"Ошибка в обновлении данных таблицы users",2);

    $haulf = round($upthis/10); // 10% аплоада кто пригласил (см config.php)
     $time = get_date_time(gmtime() - 31*86400);
    if (!empty($haulf) && !empty($invitedby) && $use_10proc==1)
    mysql_query("UPDATE users SET uploaded = uploaded + ".$haulf." WHERE id=".sqlesc($invitedby)." AND enabled='yes' AND last_access > ".sqlesc($time));


/*
$upload_speed = $upthis / max(10, (strtotime($self['last_action']) - $self['prevts']));
if ($upload_speed > 5 * 1048576){
err($passkey,"Слишком высокая скорость для пользователя: ".number_format($upload_speed / 1048576, 5)." Мб/сек");
}
*/

/*
if (validip_pmr($ip)){
$upload_speed=""; /// для внешки
} else {
$upload_speed=""; /// для пмр
}
*/

$upload_speed = $upthis / max(10, (strtotime($self['last_action']) - $self['prevts']));


        if (!empty($cheat_on)){

        if ($numpeers<=1)// && ($upload_speed > 51512)
        {
        //ловим читеров при скорости отдачи в 500 КБ при условии сидов и пиров <=1
        $endtime = time();
        $starttime = strtotime($self['last_action']);
        $diff = ($endtime - $starttime);
        $rate = ($upthis / ($diff + 1));
      if ($rate > 500555)
        {
                $rate = mksize($rate);
                $client = $agent;
                $userip = getip();
 auto_enter_cheater($userid, $rate, $upthis, $diff, $torrentid, $client, $userip, $last_up,$numpeers);
       }
        }

        
         // проверяем скорость (1048576 метр был по умолчанию)
		if($upthis > 512512 && $numpeers>1)
        {
        //ловим читеров при скорости отдачи в 512 КБ	
        //ищем различия
        $endtime = time();
        $starttime = strtotime($self['last_action']);
        $diff = ($endtime - $starttime);
        //Normalise to prevent divide by zero.
        $rate = ($upthis / ($diff + 1));
        // если больше указанного
        if ($rate > 200555)
        {
          //err($passkey,"выполнился $rate ");
                $rate = mksize($rate);
                $client = $agent;
                $userip = getip();

auto_enter_cheater($userid, $rate, $upthis, $diff, $torrentid, $client, $userip, $last_up,$numpeers);
           
           }
          }
        
        }
        
}




$dt = sqlesc(date('Y-m-d H:i:s', time()));
$updateset = array();
$snatch_updateset = array();
if ($event == 'stopped') {
	if (isset($self)) {
		mysql_query('UPDATE snatched SET seeder = "no", connectable = "no" WHERE torrent = '.$torrentid.' AND userid = '.$userid) or err($passkey,mysql_error()." Line: ".__LINE__,1);
		mysql_query('DELETE FROM peers WHERE '.$selfwhere);
		if (mysql_affected_rows()) {
			if ($self['seeder'] == 'yes')
				$updateset[] = 'seeders = seeders - 1';
			else
				$updateset[] = 'leechers = leechers - 1';
		}
	}
} else {
	if ($event == 'completed') {
		$snatch_updateset[] = "finished = 'yes'";
		$snatch_updateset[] = "completedat = $dt";
		$snatch_updateset[] = "seeder = 'yes'";
		$updateset[] = 'times_completed = times_completed + 1';

		/// проверить как будет себя ввести но нет необходимости в запросе.
	///	 mysql_query('UPDATE users SET unmark = unmark + 1 WHERE id='.$userid) or err($passkey,"Ошибка в обновлении данных таблицы users",2);

	}
	if (isset($self)) {
/*
	$res=mysql_query('SELECT to_go, downloaded FROM snatched WHERE torrent = '.$torrentid.' AND userid = '.$userid) or err($passkey,mysql_error().' Line: '.__LINE__);
	$row = mysql_fetch_array($res);
	$left=$row["to_go"]+$row["downloaded"]; /// идея
*/
		$downloaded2 = max(0, $downloaded - $self['downloaded']);
		$uploaded2 = max(0, $uploaded - $self['uploaded']);
		if ($downloaded2 > 0 || $uploaded2 > 0) {
			$snatch_updateset[] = "uploaded = uploaded + $uploaded2";
			$snatch_updateset[] = "downloaded = downloaded + $downloaded2";
			$snatch_updateset[] = "to_go = $left";
		}
		$snatch_updateset[] = "port = $port";
		$snatch_updateset[] = "last_action = $dt";
		$snatch_updateset[] = "seeder = '$seeder'";
		$prev_action = $self['last_action'];
		mysql_query("UPDATE peers SET uploaded = $uploaded, downloaded = $downloaded, uploadoffset = $uploaded2, downloadoffset = $downloaded2, to_go = $left, last_action = NOW(), prev_action = ".sqlesc($prev_action).", seeder = '$seeder'"
		. ($seeder == "yes" && $self["seeder"] <> $seeder ? ", finishedat = " . time() : "") . ", agent = ".sqlesc($agent)." WHERE $selfwhere") or err($passkey,"Ошибка обновления peers данных.".mysql_error()." Line: ".__LINE__,2);
		if (mysql_affected_rows() && $self['seeder'] <> $seeder) {
			if ($seeder == 'yes') {
				$updateset[] = 'seeders = seeders + 1';
			//	$updateset[] = 'leechers = leechers - 1';
				$updateset[] = 'leechers = IF(leechers > 0, leechers - 1, 0)';

			} else {
				$updateset[] = 'seeders = IF(seeders > 0, seeders - 1, 0)';
			//	$updateset[] = 'seeders = seeders - 1';
				$updateset[] = 'leechers = leechers + 1';
			}
		}
	} else {
     	//	if ($ustesla['enabled'] == 'no')
		//	err($passkey,"Ваш аккаунт отключен!');
		
			if ($ustesla['parked'] == 'yes')
			err($passkey,"Ваш аккаунт припаркован, см настройки в my.php!",1);
		
			/// добавленно
			if ($ustesla['downloadpos'] == 'no')
			err($passkey,"Вам отключили скачивание торрентов!",1);
			
		
	//	if (portblacklisted($port))
		//	err($passkey,"Порт '.$port.' в черном списке.",1);
	//	else {
		    if ($row["port"]<>$port || empty($row["port"])){ /// проверить условие
			$sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
			if (!$sockres) {
				$connectable = 'no';
				if ($nc == 'yes')
					err($passkey,"Ваш бит клиент не может соединится с трекером, проверьте порты.");
			} else {
				$connectable = 'yes';
				@fclose($sockres);
			}
			} else
			$connectable = 'yes';
			
	//	}

		$res = mysql_query('SELECT torrent, userid FROM snatched WHERE torrent = '.$torrentid.' AND userid = '.$userid) or err($passkey,mysql_error()." Line: ".__LINE__,1);
		$check = mysql_fetch_array($res);
		if (!$check)
			mysql_query("INSERT INTO snatched (torrent, userid, port, startdat, last_action) VALUES ($torrentid, $userid, $port, $dt, $dt)") or err($passkey,mysql_error()." Line: ".__LINE__,2);
			
		$ret = mysql_query("INSERT INTO peers (connectable, torrent, peer_id, ip, port, uploaded, downloaded, to_go, started, last_action, seeder, userid, agent, uploadoffset, downloadoffset, passkey) VALUES ('$connectable', $torrentid, " . sqlesc($peer_id) . ", " . sqlesc($ip) . ", $port, $uploaded, $downloaded, $left, NOW(), NOW(), '$seeder', $userid, " . sqlesc($agent) . ", $uploaded, $downloaded, " . sqlesc($passkey) . ")");
		if ($ret) {
			if ($seeder == 'yes')
				$updateset[] = 'seeders = seeders + 1';
			else
				$updateset[] = 'leechers = leechers + 1';
		}
	}
}
if ($seeder == 'yes') {
	if ($torrent['banned']<>'yes')
		$updateset[] = 'visible = \'yes\'';
	$updateset[] = 'last_action = NOW()';
}

if (count($updateset))
mysql_query('UPDATE torrents SET ' . join(", ", $updateset) . ' WHERE id = '.$torrentid) or err($passkey,mysql_error()." Line: ".__LINE__,1);

if (count($snatch_updateset))
mysql_query('UPDATE snatched SET ' . join(", ", $snatch_updateset) . ' WHERE torrent = '.$torrentid.' AND userid = '.$userid) or err($passkey,mysql_error().' Line: '.__LINE__,1);

if ($_SERVER["HTTP_ACCEPT_ENCODING"] == "gzip" && $use_gzip=="yes") {
	header("Content-Encoding: gzip");
	echo gzencode(benc_resp_raw($resp), 9, FORCE_GZIP);
} else
	benc_resp_raw($resp);
	


if ($debug_time==true){
$end_time = microtime();
$end_array = explode(" ",$end_time);
$end_time = $end_array[1] + $end_array[0];
// вычитаем из конечного времени начальное
$time = $end_time - $start_time;
// запись
$date=get_date_time();

$time = substr($time, 0, 8);
$memory = round(memory_get_usage()/1024);

$fpsf=@fopen("./cache/announce.txt","a+"); 
$ip=getip(); 
@fputs($fpsf,$time."-".$date."\n"); 
@fclose($fpsf); 
}

?>