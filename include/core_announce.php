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



if(!defined("IN_ANNOUNCE"))
  die("Hacking attempt!");

@error_reporting(E_ALL & ~E_NOTICE);
@ini_set('error_reporting', E_ALL & ~E_NOTICE);
@ini_set('display_errors', '1');
@ini_set('display_startup_errors', '0');
@ini_set('ignore_repeated_errors', '1');
@ignore_user_abort(1);
@set_time_limit(0);
@set_magic_quotes_runtime(0);

//define('ROOT_PATH', dirname(dirname(__FILE__))."/");
define ('ROOT_PATH', str_replace("include","",dirname(__FILE__)));

include_once(ROOT_PATH.'/include/init.php');
include_once(ROOT_PATH.'/include/benc.php');
require_once(ROOT_PATH.'/include/config.php');
require_once(ROOT_PATH.'/include/passwords.php');

///require_once(ROOT_PATH.'/include/functions_cache.php');


function auto_enter_cheater($userid, $rate, $upthis, $diff, $torrentid, $client, $ip, $last_up,$numpeers) {
mysql_query("INSERT INTO cheaters (added, userid, client, rate, beforeup, upthis, timediff, userip, numpeers, torrentid) VALUES(".sqlesc(get_date_time()).", ".sqlesc($userid).", ".sqlesc($client).", ".sqlesc($rate).", ".sqlesc($last_up).", ".sqlesc($upthis).", ".sqlesc($diff).", ".sqlesc($ip).", ".sqlesc($numpeers).", ".sqlesc($torrentid).")") or err('','from cheaters: ' + mysql_error(),1);
}

function validip_pmr($ip) {
	if (!empty($ip) && $ip == long2ip(ip2long($ip))) {
		$reserved_ips = array (
				array('80.94.240.0','80.94.255.255'),
				array('77.235.96.0','77.235.127.255'),
				array('62.221.64.0','62.221.127.255'),
				array('95.153.64.0','95.153.127.255')
			//	array('217.19.208.0','217.19.223.255')
		);

		foreach ($reserved_ips as $r) {
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}


function err($passkey,$msg = false,$errorty = false) {
global $error_bit;

if ($error_bit==1 || empty($errorty))
$lost=true;
elseif ($error_bit==2 && $errorty==2)
$lost=true;
elseif (empty($error_bit))
$lost=false;

if (!empty($msg) && $lost==true){

$sf ="cache/error_torrent.txt"; 

$fpsf=@fopen($sf,"a+"); 
$ip = getip();
$time=get_date_time(gmtime());
$agent = htmlentities($_SERVER['HTTP_USER_AGENT']);
$time=time();
@fputs($fpsf,"#$passkey#$time#$ip#$msg#$agent\n"); 
@fclose($fpsf);

if ($errorty==2)
$msg="Ошибка в запросе к базе данных. Обратитесь к администратору.";
}

benc_resp(array("failure reason" => array(type => "string", value => $msg)));
exit();
}

function benc_resp($d) {
	benc_resp_raw(benc(array(type => "dictionary", value => $d)));
}

function benc_resp_raw($x) {
	header("Content-Type: text/plain");
	header("Pragma: no-cache");
	echo($x);
}

function get_date_time($timestamp = 0) {
	if ($timestamp)
		return date("Y-m-d H:i:s", $timestamp);
	else
		return date("Y-m-d H:i:s");
}

function gmtime() {
    return strtotime(get_date_time());
}

function strip_magic_quotes($arr) {
	foreach ($arr as $k => $v)
	{
	 if (is_array($v))
	  { $arr[$k] = strip_magic_quotes($v); }
	 else
	  { $arr[$k] = stripslashes($v); }
	}

	return $arr;
}

function mksize($bytes) {
	if ($bytes < 1000 * 1024)
		return number_format($bytes / 1024, 2) . " kB";
	elseif ($bytes < 1000 * 1048576)
		return number_format($bytes / 1048576, 2) . " MB";
	elseif ($bytes < 1000 * 1073741824)
		return number_format($bytes / 1073741824, 2) . " GB";
	else
		return number_format($bytes / 1099511627776, 2) . " TB";
}

function emu_getallheaders() {
   foreach($_SERVER as $name => $value)
	   if(substr($name, 0, 5) == 'HTTP_')
		   $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
   return $headers;
}

function portblacklisted($port) {
	if ($port >= 411 && $port <= 413)
		return true;
	if ($port >= 6881 && $port <= 6889)
		return true;
	if ($port == 1214)
		return true;
	if ($port >= 6346 && $port <= 6347)
		return true;
	if ($port == 4662)
		return true;
	if ($port == 6699)
		return true;
	return false;
}

function validip($ip) {
	if (!empty($ip) && $ip == long2ip(ip2long($ip)))
	{
				$reserved_ips = array (
				array('0.0.0.0','2.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
		);

		foreach ($reserved_ips as $r)
		{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}

function getip() {
	
	/* 
	// при кривом определении ip адреса - раскоментировать
	// Code commented due to possible hackers/banned users to fake their ip with http headers
	if (isset($_SERVER)) {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && validip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	} else {
		if (getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR'))) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP'))) {
			$ip = getenv('HTTP_CLIENT_IP');
		} else {
			$ip = getenv('REMOTE_ADDR');
		 }
	}
	*/
	$ip = getenv('REMOTE_ADDR');

	return $ip;
}

function dbconn() {
    global $mysql_host_fix_by_imperator, $mysql_user_fix_by_imperator, $mysql_pass_fix_by_imperator, $mysql_db_fix_by_imperator;
    if (!mysql_connect($mysql_host_fix_by_imperator, $mysql_user_fix_by_imperator, $mysql_pass_fix_by_imperator))
    {
        err('','dbconn: mysql_connect: ' . mysql_error(),2);
    }
   
    mysql_query("SET character_set_client=cp1251");
    mysql_query("SET character_set_connection=cp1251");
    mysql_query("SET character_set_results=cp1251");
    mysql_select_db($mysql_db_fix_by_imperator) or err('','dbconn: mysql_select_db: ' + mysql_error(),2);
    register_shutdown_function("mysql_close");

}

function sqlesc($value) {

$d = array("SHOW","UNION","SELECT","CREATE","UPDATE","DELETE","DROP","INSERT","DATABASE","javascript","SUBSTRING","BENCHMARK","FIND_IN_SET");

$value = str_ireplace($d, "", $value);

    // Stripslashes
   /*if (get_magic_quotes_gpc()) {
       $value = stripslashes($value);
   }*/
   // Quote if not a number or a numeric string
   if (!is_numeric($value)) {
       $value = "'" . mysql_real_escape_string($value) . "'";
   }
   return $value;
}

function hash_pad($hash) {
    return str_pad($hash, 20);
}

function hash_where($name, $hash) {
    $shhash = preg_replace('/ *$/s', "", $hash);
    return "($name = " . sqlesc($hash) . " OR $name = " . sqlesc($shhash) . ")";
}

function unesc($x) {
	if (get_magic_quotes_gpc())
		return stripslashes($x);
//return $x;
return strip_tags($x);
}

function gzip2() {
	if (@extension_loaded('zlib') && @ini_get('zlib.output_compression') <> '1' && @ini_get('output_handler') <> 'ob_gzhandler') {
		@ob_start('ob_gzhandler');
	}
}

function gzip() {
    	
    global $use_gzip;
    
    if ($use_gzip=="yes"){
    
    static $already_loaded; /// берем данные для проверки
   
    if (extension_loaded('zlib') && ini_get('zlib.output_compression') <>'1' && ini_get('output_handler') <> 'ob_gzhandler' && $use_gzip=='yes' && !$already_loaded) {
        @ob_start('ob_gzhandler');
        $already_loaded = true; /// помечаем что использовали
    } else {
      @ob_start();   //Страница, которую вы пытаетесь просмотреть, не может быть показана, так как она использует неверную или неподдерживаемую форму компрессии.
       }
	   }
}


?>