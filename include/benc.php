<?

/**  Внимание, самая популярная сборка движка теперь доступна в бесплатном скачивании (версия gold за 2010 год)!!!!
* Это старая версия проекта Tesla - торрент сайта. Демо новой версии можно посмотреть по ссылке demo.muz-tracker.net
* Если желаете использовать движок, то оставьте пожалуйста копирайта на своих местах, иначе стабильной работы не гарантируется.
* Если желаете поискать и установить новые моды, советуем воспользоваться форумами: bit-tracker.info, bit-torrent.kiev.ua.
* Если желаете поблагодарить владельца 7Max7 за эту сборку, киньте пару монеток на R190680055855 или Z187703352284
* Если желаете обновить Gold версию до Platinum - придется заплатить около 70$ (с последующими бесплатными обновлениями)
* 
* Данная копия движка взята с сайта demo muz-tracker net! ))
* Спасибо за внимание к движку Tesla.
**/


if(!defined('IN_TRACKER') && !defined('IN_ANNOUNCE'))
die('Direct access denied. Benc'); 

function benc($obj) {
	if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
		return;
	$c = $obj["value"];
	switch ($obj["type"]) {
		case "string":
			return benc_str($c);
		case "integer":
			return benc_int($c);
		case "list":
			return benc_list($c);
		case "dictionary":
			return benc_dict($c);
		default:
			return;
	}
}

function benc_str($s) {
	return strlen($s) . ":$s";
}

function benc_int($i) {
	return "i" . $i . "e";
}

function benc_list($a) {
	$s = "l";
	foreach ($a as $e) {
		$s .= benc($e);
	}
	$s .= "e";
	return $s;
}

function benc_dict($d) {
	$s = "d";
	$keys = array_keys($d);
	sort($keys);
	foreach ($keys as $k) {
		$v = $d[$k];
		$s .= benc_str($k);
		$s .= benc($v);
	}
	$s .= "e";
	return $s;
}

function bdec_file($f, $ms) {
	$fp = fopen($f, "rb");
	if (!$fp)
		return;
	$e = fread($fp, $ms);
	fclose($fp);
	return bdec($e);
}

function bdec($s) {
	if (preg_match('/^(\d+):/', $s, $m)) {
		$l = $m[1];
		$pl = strlen($l) + 1;
		$v = substr($s, $pl, $l);
		$ss = substr($s, 0, $pl + $l);
		if (strlen($v) <> $l)
			return;
		return array('type' => "string", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
	}
	if (preg_match('/^i(\d+)e/', $s, $m)) {
		$v = $m[1];
		$ss = "i" . $v . "e";
		if ($v === "-0")
			return;
		if ($v[0] == "0" && strlen($v) <> 1)
			return;
		return array('type' => "integer", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
	}
	switch ($s[0]) {
		case "l":
			return bdec_list($s);
		case "d":
			return bdec_dict($s);
		default:
			return;
	}
}

function bdec_list($s) {
	if ($s[0] <> "l")
		return;
	$sl = strlen($s);
	$i = 1;
	$v = array();
	$ss = "l";
	for (;;) {
		if ($i >= $sl)
			return;
		if ($s[$i] == "e")
			break;
		$ret = bdec(substr($s, $i));
		if (!isset($ret) || !is_array($ret))
			return;
		$v[] = $ret;
		$i += $ret["strlen"];
		$ss .= $ret["string"];
	}
	$ss .= "e";
	return array('type' => "list", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
}

function bdec_dict($s) {
	if ($s[0] <> "d")
		return;
	$sl = strlen($s);
	$i = 1;
	$v = array();
	$ss = "d";
	for (;;) {
		if ($i >= $sl)
			return;
		if ($s[$i] == "e")
			break;
		$ret = bdec(substr($s, $i));
		if (!isset($ret) || !is_array($ret) || $ret["type"] <> "string")
			return;
		$k = $ret["value"];
		$i += $ret["strlen"];
		$ss .= $ret["string"];
		if ($i >= $sl)
			return;
		$ret = bdec(substr($s, $i));
		if (!isset($ret) || !is_array($ret))
			return;
		$v[$k] = $ret;
		$i += $ret["strlen"];
		$ss .= $ret["string"];
	}
	$ss .= "e";
	return array('type' => "dictionary", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
}





if (!function_exists("dict_check_t")) {
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
}

if (!function_exists("dict_get_t")) {
function dict_get_t($d, $k, $t) {
	$dd = $d["value"];
	$v = $dd[$k];
	return $v["value"];
}
}

if (!function_exists("dict_check")) {
function dict_check($d, $s) {
	if ($d["type"] != "dictionary")
		bark("not a dictionary");
	$a = explode(":", $s);
	$dd = $d["value"];
	$ret = array();
	foreach ($a as $k) {
		unset($t);
		if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
			$k = $m[1];
			$t = $m[2];
		}
		if (!isset($dd[$k]))
			bark("dictionary is missing key(s)");
		if (isset($t)) {
			if ($dd[$k]["type"] != $t)
				bark("invalid entry in dictionary");
			$ret[] = $dd[$k]["value"];
		}
		else
			$ret[] = $dd[$k];
	}
	return $ret;
}
}

if (!function_exists("dict_get")) {
function dict_get($d, $k, $t) {
	if ($d["type"] != "dictionary")
		bark("not a dictionary");
	$dd = $d["value"];
	if (!isset($dd[$k]))
		return;
	$v = $dd[$k];
	if ($v["type"] != $t)
		bark("invalid dictionary entry type");
	return $v["value"];
}
}













function get_remote_peers($url, $info_hash, $timeout = 30, $method = 'scrape') {

$timeout = 30;
$tstart = timer(); // Start time

$orurl = $url;

if ($method == "announce") {
$options = array("info_hash" => pack("H*", $info_hash),
"peer_id" => urlencode('-UT2000-%00F'),
"port" => rand(88, 65535),
"uploaded" => rand(100, 10000),
"no_peer_id" => 1,
"downloaded" => rand(100, 10000),
"compact" => 1,
"left" => 1,
"numwant" => 9999);
} else {
$options = array("info_hash" => pack("H*", $info_hash));
}

//if (empty($url) && !empty($orurl))
//$url = $orurl;

$parser_url = @parse_url($url);

$host = $parser_url['host'];
$port = (empty($parser_url['port']) ? "80":$parser_url['port']);
$scheme = $parser_url['scheme'];

if ($method == "scrape")
$parser_url['path'] = str_replace('announce', 'scrape', $parser_url['path']);

if (empty($url) && empty($host))
return array('tracker' => $host, 'state' => 'no_host');

$http_path = $parser_url['path'];
$query_explode = explode('&', $parser_url['query']);

$new_query=array();
foreach (array_filter($query_explode) as $array_value) {
list($key, $value) = explode('=', $array_value);
$new_query[$key] = $value;
}

$http_query = htmlspecialchars_decode(http_build_query(@array_merge($new_query, $options)));
$opts = array($scheme => array('method' => 'GET','header' => 'User-Agent: uTorrent/2000','timeout' => $timeout));

$req_uri = $scheme.'://'.$host.":".$port.$http_path.($http_query ? '?'.$http_query : '');

/// проверка существования функций file_get_contents и доступ к внешке
if (function_exists('file_get_contents') && ini_get('allow_url_fopen') == 1) {
$context = @stream_context_create($opts);
$result = @file_get_contents($req_uri, false, $context);
}
/// проверка существования функций curl
elseif (function_exists('curl_init')) {

if ($ch = @curl_init()) {
@curl_setopt($ch, CURLOPT_URL, $req_uri);
@curl_setopt($ch, CURLOPT_PORT, $port);
@curl_setopt($ch, CURLOPT_HEADER, false);
@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
@curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
@curl_setopt($ch, CURLOPT_BUFFERSIZE, 1000);
@curl_setopt($ch, CURLOPT_USERAGENT, 'uTorrent/2000');

$result = @curl_exec($ch);
@curl_close($ch);
}

}
/// проверка существования функций fsockopen (вз за стандарт))
elseif (function_exists('fsockopen')){

$fp = @fsockopen($host, $port, $errno, $errstr, $timeout);

if ($fp) {
$headers = "GET ".$http_path.(!empty($http_query) ? '?'.$http_query : '')." HTTP/1.0\r\n";
$headers.= "Host: ".$host."\r\n";
$headers.= "Connection: close\r\n";
$headers.= "User-Agent: uTorrent/1820;Windows XP;\r\n\r\n";
@fputs($fp, $headers);

$hdata = '';
while (!feof($fp)) {
$hdata.= @fgets($fp, 512);
}

@fclose($fp);

if ($hdata) {
list ($clear,$result) = explode("\r\n\r\n", $hdata);
//echo $result;
}
}

}

if ($result == "d5:filesdee" && $method == 'announce')
return array('tracker' => $host, 'state' => 'false');

if (empty($result) && !empty($orurl)) {

if ($method == 'scrape')
return get_remote_peers($orurl, $info_hash, $timeout, "announce"); 
elseif (number_format((timer() - $tstart), 0) >= $timeout)
return array('tracker' => $host, 'state' => 'timeout');

}

/// преобразование в "torrent" массив из строки данных
$result = @bdec($result); 
//print_r($result);

if (!is_array($result) && $method=="announce")
return array('tracker' => $host, 'state' => 'false');

/*
if (!is_array($result))
return array('tracker' => $host, 'state' => 'no_result_'.$method);
*/

if ($method == 'scrape') {

if (!empty($result['value']['files']['value'])) {

$peersarray = array_shift($result['value']['files']['value']);

return array('tracker' => $host,
'seeders' => (isset($peersarray['value']['complete']['value']) ? (int) $peersarray['value']['complete']['value']:"0"),
'leechers' => (isset($peersarray['value']['incomplete']['value']) ? (int) $peersarray['value']['incomplete']['value']:"0"),
'downloaded' => (isset($peersarray['value']['downloaded']['value']) ? (int) $peersarray['value']['downloaded']['value']:"0"),
'state' => 'ok');

}
else
return get_remote_peers($orurl, $info_hash, $timeout,"announce");
}

elseif($method == 'announce') {

return array('tracker' => $host,
'seeders' => (is_array($result['value']['peers']['value'])? count($result['value']['peers']['value']):(int) $result['value']['peers']['value']), 
'leechers' => (is_array($result['value']['incomplete']['value'])? count($result['value']['incomplete']['value']):(int) $result['value']['incomplete']['value']),
'downloaded' => (is_array($result['value']['downloaded']['value'])? count($result['value']['downloaded']['value']):(int) $result['value']['downloaded']['value']), 
'state' => 'ok');

/// решение проблемы иерогливов в int - strlen($result['value']['']['value'])/6) WTF???
}

}   

?>