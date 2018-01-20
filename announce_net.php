<?
if(!defined("IN_ANNOUNCE"))
  die("Hacking attempt!");

/**
 * @author 7Max7
 * @copyright 2010
 */

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

global $announce_net;

/// подключение файла если условие создано
if (empty($announce_net)){
err('',"Функция внешнего скачивания без регистрации - Отключена.",1);
}

gzip();
$agent = htmlentities($_SERVER['HTTP_USER_AGENT']);

foreach (array('info_hash','peer_id','event','ip','localip') as $x) {
	if(isset($_GET[$x]))
	$GLOBALS[$x] = (string)$_GET[$x];
}

foreach (array('port','downloaded','uploaded','left') as $x)
	$GLOBALS[$x] = (float)$_GET[$x];
	/// больше 2 гигов урезка до 2 на 32 битке (проверить слух)


if (get_magic_quotes_gpc()) {
    $info_hash = stripslashes($info_hash);
    $peer_id = stripslashes($peer_id);
}


foreach (array('peer_id','port','downloaded','uploaded','left') as $x)
	if (!isset($x)) err('',"Нет ключа в: ".$x,1);

foreach (array('info_hash','peer_id') as $x)
			if (strlen($GLOBALS[$x]) <> 20)
			err('',"Отсутствует ".$x." (".strlen($_GET[$x])." - ".urlencode($_GET[$x]).")",1);
		//	if (strlen($passkey) <> 32)
		//	err($passkey,"Неверный пасскей: ".strlen($passkey),1);
$ip = getip();
$rsize = 50;

foreach(array('num want', 'numwant', 'num_want') as $k) {
	
	if (isset($_GET[$k])) {
		$rsize = (float) $_GET[$k];
        if($rsize<0)$rsize=0;  
		
		break;
	}
}

if (!$port || $port > 0xffff)
	err('',"Неверный порт: ".$port,1);
	
if (!isset($event))
	$event = '';
	
$seeder = ($left == 0) ? 'yes' : 'no';

dbconn();

$hash = bin2hex($info_hash);
$res = mysql_query('SELECT id, free, moderated, seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts FROM torrents WHERE info_hash = "'.$hash.'" AND multitracker="yes"') or err('',mysql_error()." Line: ".__LINE__,2);
$torrent = mysql_fetch_array($res);
if (!$torrent)
	err('',"Торрент не зарегистрирован на трекере или не мультитрекерный: ".$hash,1);

if ($torrent["banned"]=="yes")
	err('',"Торрент забанен на трекере: ".$torrent["id"],1);

/// добавленно
if ($torrent["moderated"]=="no")
	err('',"Торрент еще непроверен модератором: ".$torrent["id"],1);

$numpeers=$torrent["numpeers"];

$torrentid = $torrent['id'];

$fields = 'seeder, peer_id, ip, port, uploaded, downloaded, last_action, UNIX_TIMESTAMP(NOW()) AS nowts, UNIX_TIMESTAMP(prev_action) AS prevts, UNIX_TIMESTAMP(last_action) AS ts';

$numpeers = $torrent['numpeers'];
$limit = '';

if ($numpeers > $rsize)
$limit = 'ORDER BY last_action LIMIT '.$rsize;
	

$res = mysql_query('SELECT '.$fields.' FROM peers WHERE torrent = '.$torrentid.' '.$limit) or err('',mysql_error().' Line: '.__LINE__,2);


$resp = 'd' . benc_str('interval') . 'i' . $announce_interval . 'e' . benc_str('peers') . (($compact = ($_GET['compact'] == 1)) ? '' : 'l');

$no_peer_id = ($_GET['no_peer_id'] == 1);
unset($self);

while ($row = mysql_fetch_array($res)) {
	if ($row['peer_id'] == $peer_id) {
		$userid = 0;
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

$selfwhere = 'ip = '.sqlesc($ip).' AND agent = '.sqlesc($agent).' AND torrent = '.$torrentid.' AND passkey IS NULL AND peer_id = '.sqlesc($peer_id);

if (!isset($self)) {
	$res = mysql_query('SELECT '.$fields.' FROM peers WHERE '.$selfwhere) or err('',mysql_error().' Line: '.__LINE__,2);
	$row = mysql_fetch_array($res);
	if ($row) {
		$self = $row;
	}
}

$announce_wait = 40;
//if (isset($self) && ($self['prevts'] > ($self['nowts'] - $announce_wait )))
//	err('',"Минимум к автообновлению аннонса - " . $announce_wait . " секунд",1);


$dt = sqlesc(date('Y-m-d H:i:s', time()));
$updateset = array();
//$snatch_updateset = array();
if ($event == 'stopped') {
	if (isset($self)) {

	//	mysql_query('UPDATE snatched SET seeder = "no", connectable = "no" WHERE torrent = '.$torrentid.' AND userid = "0"') or err('',mysql_error()." Line: ".__LINE__,1);

		mysql_query('DELETE FROM peers WHERE '.$selfwhere);
		
		/*
		if (mysql_affected_rows()) {
			if ($self['seeder'] == 'yes')
				$updateset[] = 'seeders = seeders - 1';
			else
				$updateset[] = 'leechers = leechers - 1';
		}
		*/
	}
} else {
	if ($event == 'completed') {
	//	$snatch_updateset[] = "finished = 'yes'";
	//	$snatch_updateset[] = "completedat = $dt";
	//	$snatch_updateset[] = "seeder = 'yes'";
		$updateset[] = 'times_completed = times_completed + 1';

	}
	if (isset($self)) {

		$downloaded2 = max(0, $downloaded - $self['downloaded']);
		$uploaded2 = max(0, $uploaded - $self['uploaded']);
		if ($downloaded2 > 0 || $uploaded2 > 0) {
		//	$snatch_updateset[] = "uploaded = uploaded + $uploaded2";
		//	$snatch_updateset[] = "downloaded = downloaded + $downloaded2";
		//	$snatch_updateset[] = "to_go = $left";
		}
	//	$snatch_updateset[] = "port = $port";
	//	$snatch_updateset[] = "last_action = $dt";
	//	$snatch_updateset[] = "seeder = '$seeder'";
		$prev_action = $self['last_action'];

		mysql_query("UPDATE peers SET uploaded = $uploaded, downloaded = $downloaded, uploadoffset = $uploaded2, downloadoffset = $downloaded2, to_go = $left, last_action = NOW(), prev_action = ".sqlesc($prev_action).", seeder = '$seeder'"
		. ($seeder == "yes" && $self["seeder"] <> $seeder ? ", finishedat = " . time() : "") . ", agent = ".sqlesc($agent)." WHERE $selfwhere") or err('',"Ошибка обновления peers данных.".mysql_error()." Line: ".__LINE__,2);
		
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
			
		
		    if ($row["port"]<>$port || empty($row["port"])){ /// проверить условие
			$sockres = @fsockopen($ip, $port, $errno, $errstr, 3);
			if (!$sockres) {
				$connectable = 'no';
				if ($nc == 'yes')
					err('',"Ваш бит клиент не может соединится с трекером, проверьте порты.");
			} else {
				$connectable = 'yes';
				@fclose($sockres);
			}
			} else
			$connectable = 'yes';


	//	$res = mysql_query('SELECT torrent FROM snatched WHERE torrent = '.$torrentid.' AND userid = "0"') or err('',mysql_error()." Line: ".__LINE__,1);
		
	//	$check = mysql_fetch_array($res);

	//	if (!$check)
	//		mysql_query("INSERT INTO snatched (torrent, userid, port, startdat, last_action) VALUES ($torrentid, 0, $port, $dt, $dt)") or err('',mysql_error()." Line: ".__LINE__,2);

		$ret = mysql_query("INSERT INTO peers (connectable, torrent, peer_id, ip, port, uploaded, downloaded, to_go, started, last_action, seeder, userid, agent, uploadoffset, downloadoffset, passkey) VALUES ('$connectable', $torrentid, " . sqlesc($peer_id) . ", " . sqlesc($ip) . ", $port, $uploaded, $downloaded, $left, NOW(), NOW(), '$seeder', 0, " . sqlesc($agent) . ", $uploaded, $downloaded, '')");
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
mysql_query('UPDATE torrents SET ' . join(", ", $updateset) . ' WHERE id = '.$torrentid) or err('',mysql_error()." Line: ".__LINE__,1);

//if (count($snatch_updateset))
//mysql_query('UPDATE snatched SET ' . join(", ", $snatch_updateset) . ' WHERE torrent = '.$torrentid.' AND userid = '.$userid) or err('',mysql_error().' Line: '.__LINE__,1);

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

$fpsf=@fopen("./cache/announce_net.txt","a+"); 
$ip=getip(); 
@fputs($fpsf,$time."-".$date."\n"); 
@fclose($fpsf); 
}

?>