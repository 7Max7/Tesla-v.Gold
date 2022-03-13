<?

/**  Внимание, самая популярная сборка движка теперь доступна в бесплатном скачивании (версия gold за 2010 год)!!!!
* Это старая версия проекта Tesla - торрент сайта. Демо новой версии можно посмотреть по ссылке demo.muz-tracker.net
* Если желаете использовать движок, то оставьте пожалуйста копирайта на своих местах, иначе стабильной работы не гарантируется.
* Если желаете поискать и установить новые моды, советуем воспользоваться форумами: bit-tracker.info, bit-torrent.kiev.ua.
* Если желаете поблагодарить владельца 7Max7 за эту сборку, киньте пару монеток на R190680055855 или Z187703352284
* Если желаете обновить Gold версию до Platinum - придется заплатить около 70$ (с последующими бесплатными обновлениями)
* 
* Данная копия движка взята с сайта demo muz-tracker net и tesla-tracker.net! ))
* Спасибо за внимание к движку Tesla.
**/


if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

require_once(ROOT_PATH.'include/functions_global.php');


function strip_magic_quotes($arr) {
	foreach ($arr as $k => $v) {
		if (is_array($v)) {
			$arr[$k] = strip_magic_quotes($v);
			} else {
			$arr[$k] = stripslashes($v);
			}
	}
	return $arr;
}


function local_user() {
	return $_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"];
}

function mysql_modified_rows () {
    $info_str = mysql_info();
    $a_rows = mysql_affected_rows();
    //ereg("Rows matched: ([0-9]*)", $info_str, $r_matched);
    preg_match("/Rows matched: ([0-9]*)/", $info_str, $r_matched);
    return ($a_rows < 1)?(isset($r_matched[1]) && $r_matched[1]? $r_matched[1]:0):$a_rows;
}

function sql_query($query) {
	global $queries, $query_stat, $querytime;
	++$queries;
	$query_start_time = timer(); // Start time
	$result = @mysql_query($query);
	$query_end_time = timer(); // End time
	$query_time = ($query_end_time - $query_start_time);
	$querytime = $querytime + $query_time;
	$query_time = substr($query_time, 0, 8);
	$query_stat[] = array("seconds" => $query_time, "query" => $query);
	return $result;
}


function accessadministration() {

global $CURUSER;

$config_file='include/passwords.php'; 
$check=fopen($config_file,'r'); 
$contents=fread($check,filesize($config_file)); 
fclose($check); 
if (filesize($config_file) > 10) {
include($config_file); 
define ('USERACCSESS',$useraccess_fix_by_imperator); 
define ('PASSACCSESS',$passaccess_fix_by_imperator); 
}

if (get_user_class() < UC_SYSOP || !isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']<>USERACCSESS || md5(md5($_SERVER['PHP_AUTH_PW']).$_SERVER['PHP_AUTH_USER'])<>PASSACCSESS){

header("WWW-Authenticate: Basic realm=\"Administration\""); 
header("HTTP/1.0 401 Unauthorized");

$host=getenv("REQUEST_URI"); $ip=getenv("REMOTE_ADDR"); 
// write_log("Попытка входа ".$CURUSER["username"]." с неправильным вводом админ. пароля. Ip: <strong>$ip</strong>. Файл: <strong>".$host."</strong>","#BCD2E6","error"); 
stdheadchat();

//echo "<b>логин введен неверно</b>: ".$_SERVER['PHP_AUTH_USER']."<br>";
//echo "<b>пароль введен неверно</b>: ".$_SERVER['PHP_AUTH_PW'];
//echo "<hr>";

print("".$CURUSER["username"]." попытка не пытка. <br><br><h3>Доступ Запрещен!</h3><br><br><br>");
stdfootchat();die;
}
@setcookie("debug", "yes", "0x7fffffff", "/");
unset($passaccess_fix_by_imperator,$useraccess_fix_by_imperator);
}


//  $lightmode = true - откл сессии и баны (откл)
function dbconn($autoclean = false, $lightmode = false) {
	global $mysql_host_fix_by_imperator, $mysql_user_fix_by_imperator, $mysql_pass_fix_by_imperator, $mysql_db_fix_by_imperator, $mysql_charset_fix_by_imperator;

	if (!mysql_connect($mysql_host_fix_by_imperator, $mysql_user_fix_by_imperator, $mysql_pass_fix_by_imperator))
///	if (!mysql_pconnect($mysql_host_fix_by_imperator, $mysql_user_fix_by_imperator, $mysql_pass_fix_by_imperator,MYSQL_CLIENT_COMPRESS))
	{
	  switch (mysql_errno())
	  {
	  case 2003:
	  {
	  	 die("Нет соединения с базой данных, проверьте mysql параметры [" . mysql_error()."]");
	  }
	  	
	  case 1040:
	  case 2002:
if ($_SERVER['REQUEST_METHOD'] == "GET"){
die("<html><head><meta http-equiv='refresh' content=\"20 $_SERVER[REQUEST_URI]\"></head><body><table border='0' width='100%' height='100%'><tr><td><h3 align='center'>на данный момент База Данных перегружена. Попытка подключится через 10 секунд будет автоматически...</h3></td></tr></table></body></html>");
} else {
print"<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\">
<meta http-equiv=\"Content-Style-Type\" content=\"text/css\"><title></title>
<style type=\"text/css\">	body { min-width: 760px; color: #000000; background: #E3E3E3; font: 16px Verdana; }
.msg { margin: 20%; text-align: center; background: #EFEFEF; border: 1px solid #B7C0C5; }
</style></head><body>
<div class=\"msg\"><p style=\"margin: 1em 0;\">Извините, в данный момент сервер перегружен.</p>	<p style=\"margin: 1em 0;\">Попробуйте зайти через несколько минут. <br>C ув администрация Muz-Tracker.net</p>
</div></body>";
die();
}
        default: die("[" . mysql_errno() . "] dbconn: mysql_connect: " . mysql_error());
      }
    
	}


	mysql_select_db($mysql_db_fix_by_imperator) or die("dbconn: mysql_select_db: " + mysql_error());

	mysql_query("SET NAMES ".$mysql_charset_fix_by_imperator);
    //mysql_query("SET max_heap_table_size = 23777216");
    //mysql_query("SET max_tmp_tables = 64");
  
    /// SQL_BIG_TABLES = 1 - местом хранения всех временных таблиц будет диск
    
 	userlogin($lightmode);

	if (basename($_SERVER['SCRIPT_FILENAME']) == 'index.php' && date('i')%2==0)
	register_shutdown_function("autoclean");

	register_shutdown_function("mysql_close");
    
	unset($GLOBALS["useraccess_fix_by_imperator"],$GLOBALS["passaccess_fix_by_imperator"],$GLOBALS["mysql_pass_fix_by_imperator"],$GLOBALS["mysql_user_fix_by_imperator"],$GLOBALS["mysql_db_fix_by_imperator"],$GLOBALS["mysql_host_fix_by_imperator"]);
}

function userlogin($lightmode=false) {
global $SITE_ONLINE, $default_language, $tracker_lang, $use_lang, $use_ipbans;

unset($GLOBALS["CURUSER"]);

$ip = getip(); 
$nip = ip2long($ip);
        
/*
if (($_SERVER["SERVER_ADDR"] <> $_SERVER["REMOTE_ADDR"] && !preg_match("/bot/i", $_SERVER["HTTP_USER_AGENT"])) && $SITE_ONLINE=="local_user") {
header("HTTP/1.1 301 Moved Permanently");
header("Location: http://www.muz-tracker.net");
exit();
}
*/

if ($use_ipbans && !$lightmode) {
//die(" тут баны");
//$bans_sql = sql_query("SELECT first, last FROM bans FORCE INDEX(first_last)") or sqlerr(__FILE__,__LINE__);
///while ($ban = mysql_fetch_assoc($bans_sql)){

$bans_sql=new MySQLCache("SELECT first, last FROM bans FORCE INDEX(first_last)", 24*7200, "bans_first_last.txt"); // 24 часа
$timax=0;
while ($ban=$bans_sql->fetch_assoc()){
$bans[] = $ban;
++$timax;
}
if (!empty($timax)){
foreach ($bans as $ban )
if ($nip >= $ban["first"] && $nip <= $ban["last"]) {

$res = sql_query("SELECT bans_time FROM bans WHERE $nip >= first AND $nip <= last") or sqlerr(__FILE__, __LINE__);

$comment = mysql_fetch_assoc($res);
if (!empty($comment["bans_time"])){
$comment = "<br>до: ".format_comment($comment["bans_time"]);
header("HTTP/1.0 403 Forbidden");
print("<title>Muz-Tracker - Бан для $ip</title>
<script><!--
var tit = document.title;
var c = 0;
function writetitle()
{
document.title = tit.substring(0,c);
if(c==tit.length)
{
c = 0;
setTimeout(\"writetitle()\", 30000)
}
else
{
c++;
setTimeout(\"writetitle()\", 50)
}
}
writetitle()
// --></script><style type=\"text/css\">
<!--
.style4 {font-family: \"Times New Roman\", Times, serif;font-size: 16px;color: #999999;}
.style5 {font-size: 48px;font-family: \"Times New Roman\", Times, serif;color: #006699;}
.style6 {font-size: 28px;font-family: \"Times New Roman\", Times, serif;color: #FFFFFF;}
-->
</style>
<Script Language=\"JavaScript\">
  function initArray()
  {this.length = initArray.arguments.length
   for (var i = 0; i < this.length; i++)
   this[i+1] = initArray.arguments[i]}
  colorList = new initArray(\"ffffff\", \"eeeeee\", \"dddddd\", \"cccccc\",
	\"bbbbbb\", \"aaaaaa\", \"999999\", \"888888\", \"777777\", \"666666\",
	\"555555\", \"444444\", \"333333\", \"222222\", \"111111\", \"000000\",
	\"000000\", \"111111\", \"222222\", \"333333\", \"444444\", \"555555\",
	\"666666\", \"777777\", \"888888\", \"999999\", \"aaaaaa\", \"bbbbbb\",
	\"cccccc\", \"dddddd\", \"eeeeee\", \"ffffff\");
  var currentColor = 1;
  function backgroundChanger()
  {document.bgColor = colorList[currentColor];
   if (currentColor++ < 32)
     setTimeout(\"backgroundChanger()\", 5);
   else
     return}
  backgroundChanger();
</Script>
<script LANGUAGE=\"JavaScript\">
var sizes = new Array(\"0px\", \"1px\", \"2px\",\"4px\", \"8px\");
sizes.pos = 0;
function rubberBand()
{
var el = document.all.elastic;
if (null == el.direction)
el.direction = 1;
else if ((sizes.pos > sizes.length - 2) ||
(0 == sizes.pos))
el.direction *= -1;
el.style.letterSpacing = sizes[sizes.pos += el.direction];
}
</script>
<body onload=\"window.tm = setInterval('rubberBand()', 100);\">
<h1 class=\"style6\" align=\"center\">Поздравляем!!!</h1>
<h1 class=\"style4\" id=\"elastic\" align=\"center\">Вы забанены</h1>
</head>
<body>
<p align=\"center\" class=\"style5\">Ваш ip адрес заблокирован $comment</p>
<p align=\"center\" class=\"style6\">Привед Конкурентам!</p>
</body>\n");
die;
}}
}}


if (!$SITE_ONLINE || empty($_COOKIE["uid"]) || empty($_COOKIE["pass"])) {

logoutcookie();
if ($use_lang)
include_once(ROOT_PATH.'languages/lang_' . $default_language . '/lang_main.php');
user_session();
return;
}

if (strlen($_COOKIE["pass"]) <> 32) {
logoutcookie();
include_once(ROOT_PATH.'languages/lang_' . $default_language . '/lang_main.php');
user_session();
return;
}
$id = (int) $_COOKIE["uid"];

//$res = sql_query("SELECT * FROM users WHERE enabled='yes' AND status = 'confirmed' AND id = '$id'") or sqlerr(__FILE__, __LINE__);
$res = sql_query("SELECT * FROM users WHERE id = '$id'") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);

////////////////// encode ///////////////////
if ($row["enabled"]=="no") {
$str = $row["username"];
$checksum = crc32($str);
$data = $str.":".$checksum;
$cookie = base64_encode($data);
@setcookie("offlog", $cookie, "0x7fffffff", "/");
}
////////////////// encode ///////////////////


/// защита от кражи куков aka 7Max7
        if ($row["shelter"]=="ag"){
        $she_md5=$row["crc"];
		} else {
        $she_md5=$row["ip"];
        }
        $tesla_ip = "T".md5($row["id"]+$she_md5+$row["id"]);

		if ($_COOKIE["tesla"]<>$tesla_ip){
		logoutcookie();
		include_once(ROOT_PATH.'languages/lang_' . $default_language . '/lang_main.php');
		user_session();
		return;
    	}
/// защита от кражи куков aka 7Max7
 

   		/// очищаем куки, вдруг остались после dump3r | phpmyadmin
        if (!empty($_COOKIE["sxd"]) || !empty($_COOKIE["pmaPass-1"])){
       	if (!preg_match("/dump3r/i", $_SERVER["SCRIPT_FILENAME"])){
        setcookie("sxd");  setcookie("phpMyAdmin"); 
		setcookie("pmaUser-1");  setcookie("pmaPass-1");
		}
        }

        if (empty($row) || $row["enabled"]=="no" || $row["status"]<>"confirmed"){
        logoutcookie();
        if ($use_lang)
        include_once(ROOT_PATH.'languages/lang_' . $default_language . '/lang_main.php');
        user_session();
        return;
        }
	
	$sec = hash_pad($row["secret"]);
	if ($_COOKIE["pass"] !== $row["passhash"]) {
		logoutcookie();
		if ($use_lang)
			include_once(ROOT_PATH.'languages/lang_' . $default_language . '/lang_main.php');
		user_session();
		return;
	}
	

    $updateset = array();

    if ($ip <> $row['ip'])
    $updateset[] = 'ip = '.sqlesc($ip); /// add .$set_checked

    if (isset($row["id"]) && strtotime($row['last_access']) < (strtotime(get_date_time()) - 300)){

//// помечаем количество не прооценных торрентов
if ($row['last_checked'] < $row['last_access']){
/// создаем найвероятнейшее условие хотя бы раз в час
$rmak2 = sql_query("SELECT COUNT(*) FROM snatched WHERE (SELECT COUNT(*) FROM ratings WHERE torrent=snatched.torrent AND off_req='0' AND user=".sqlesc($row["id"]).") <'1' AND snatched.finished='yes' AND userid = ".sqlesc($row["id"])) or sqlerr(__FILE__,__LINE__);
$arrmak2 = @mysql_fetch_array($rmak2);

///if ($row["unmark"]<>$arrmak2[0])
//sql_query("UPDATE users SET unmark = ".sqlesc($arrmak2[0])." WHERE id = ".sqlesc($row["id"])) or sqlerr(__FILE__,__LINE__);
$updateset[] = "unmark=".sqlesc($arrmak2[0]);

//// помечаем количество не прооценных торрентов  	

if (date('i')%2==1){
//// помечаем количество не сидированных торрентов  
$rse = sql_query("SELECT COUNT(*) FROM torrents WHERE owner=".sqlesc($row["id"])." AND (leechers / seeders >= 4) LIMIT 500") or sqlerr(__FILE__,__LINE__);///AND multitracker='no'
$arrseed = @mysql_fetch_array($rse);

//if ($CURUSER["unseed"]<>$arrseed[0])
//sql_query("UPDATE users SET unmark = ".sqlesc($arrseed[0])." WHERE id = ".sqlesc($CURUSER["id"])) or sqlerr(__FILE__,__LINE__);
$updateset[] = "unseed=".sqlesc($arrseed[0]);
//// помечаем количество не сидированных торрентов  
}
}
    $updateset[] = "on_line=on_line+300";
    $updateset[] = "last_access = " . sqlesc(get_date_time());

   // if ($row['promo_time']=="0000-00-00 00:00:00")
   /// $updateset[] = "promo_time = " . sqlesc(get_date_time());

	// старые торренты как проверенные
    // оффлайн-минуты, после которых сбрасывается время последней проверки новых торрентов юзера

    $dt = get_date_time(gmtime() - 30*60);/// 30 минут
    if($row['last_access'] < $dt){
    $updateset[] ="last_checked = ".sqlesc($row['last_access']);
    setcookie("markview", "", 0x7fffffff, "/");
    }



    /// снятие истекших чат банов
    if ($row['shoutbox'] < get_date_time() && $row['shoutbox']<>"0000-00-00 00:00:00") {
	$now = sqlesc(get_date_time());
	$modcomment = (date("Y-m-d") . " - Бан на чат снят системой по таймауту.\n");
	$msg = sqlesc("Ваш бан в чате был снят системой по таймауту. Постарайтесь больше не получать таких услуг от администрации и следовать правилам сайта.\n");

	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, ".$row["id"].", ".$now.", ".$msg.", 0)") or sqlerr(__FILE__,__LINE__);

	  $updateset[] = "modcomment=".sqlesc($modcomment.$row['modcomment']);
	  $updateset[] = "shoutbox=".sqlesc("0000-00-00 00:00:00");

     }

    //// помечаем количество непрочитанных сообщений
    $res_un = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=".$row["id"]." AND location=1 AND unread='yes'") or sqlerr(__FILE__,__LINE__);///FORCE INDEX(receiver) 
    $arr_un = @mysql_fetch_row($res_un);
    $updateset[] = "unread=".sqlesc($arr_un[0]);
    //// помечаем количество непрочитанных сообщений
 
	 }

    if (count($updateset))
sql_query("UPDATE users SET ".implode(", ", $updateset)." WHERE id=" . $row["id"]) or sqlerr(__FILE__,__LINE__);

$row['ip'] = $ip;

if ($row['monitoring']=='yes' && getenv("REQUEST_URI")<>'/' && !preg_match("/poll.core/i", $_SERVER["SCRIPT_FILENAME"]) && !preg_match("/online/i", $_SERVER["SCRIPT_FILENAME"]) && !preg_match("/shoutbox/i", $_SERVER["SCRIPT_FILENAME"]))
{
$userid=$row["id"];
if (empty($userid))
$userid=0;

$sf =ROOT_PATH."cache/monitoring_$userid.txt"; 

$fpsf=fopen($sf,"a+"); 
$ip=getip(); 
$ag=htmlentities(getenv("HTTP_USER_AGENT")); 
$from=htmlentities(getenv("HTTP_REFERER")); 
$host=htmlentities(getenv("REQUEST_URI")); 

$date = date("d.m.y"); 
$time= date("H:i:s"); 
$getiing = serialize($_GET?$_GET:"")."||".serialize($_POST?$_POST:"");
//$getiing=htmlentities($getiing);
fputs($fpsf,"#$host #$ip # #$date $time #$getiing\n"); 
fclose($fpsf);

///// ниже можно закоментировать
include (ROOT_PATH."include/zip.lib.php");
if (@filesize($sf) >= 2000000 && @filesize($sf)<= 3000000) /// если больше 2000000 байт - 2 метра
{
	
$dsf=date('H-i-s');
$ziper_newname=ROOT_PATH."cache/$dsf-monitoring_$userid.zip";
$ziper = new zipfile();
$ziper->addFiles(array($sf));  //array of files
$ziper->output($ziper_newname); 
@unlink ($sf);
} elseif (@filesize($sf) >= 3000000) @unlink($sf);
/// удаляю в конце файл который упаковал

///// выше можно закоментировать
}

///	if ($row['override_class'] <>"255")
//	$row['class'] = $row['override_class']; // Override class and save in GLOBAL array below.
//	$row['override']=$row['class']; 

	
if (!empty($_POST["id_styles"]) && is_valid_id($_POST["id_styles"])){
$id=(int)$_POST["id_styles"];

$dus = sql_query("SELECT uri FROM stylesheets WHERE id=".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
$ru = mysql_fetch_array($dus);
if (!empty($ru["uri"]) && @file_exists("themes/".htmlspecialchars($ru["uri"])."/stdhead.php")){
sql_query("UPDATE users SET stylesheet=".sqlesc($ru["uri"])." WHERE id = ".$row["id"]) or sqlerr(__FILE__, __LINE__);
$row["stylesheet"]=$ru["uri"];
}
}
	
	$GLOBALS["CURUSER"] = $row;

	///unset($GLOBALS["$row"]);
	if ($use_lang)
		include_once(ROOT_PATH.'languages/lang_' . $row['language'] . '/lang_main.php');

	if (!$lightmode)
		user_session();

}

function logincookie($id, $passhash, $shelter, $updatedb = 1, $expires = 0x7fffffff) {

	/// защита от кражи куков aka 7Max7
	$crc32_now=crc32(htmlentities($_SERVER["HTTP_USER_AGENT"]));
	
	if ($shelter=="ag"){
    $she_md5=$crc32_now;
	} else
    $she_md5=getip();
    
    $tesla_ip = "T".md5($id+$she_md5+$id);
	setcookie("tesla", $tesla_ip, $expires, "/");
	setcookie("uid", $id, $expires, "/");
	setcookie("pass", $passhash, $expires, "/");

    if ($updatedb) {
    global $CURUSER;
    	
    if (!empty($crc32_now)) {
$agent = htmlentities($_SERVER["HTTP_USER_AGENT"]);
$hache = crc32($agent);

$time = sqlesc(get_date_time());

$ret2=sql_query("SELECT crc,idagent FROM users WHERE id=".sqlesc($id)."") or sqlerr(__FILE__,__LINE__);
$row_user = mysql_fetch_array($ret2); 
$uid=$id;

if (empty($row_user["crc"]) || $row_user["crc"]==0)
{
sql_query("INSERT INTO useragent (crc32, agent, added) VALUES (".sqlesc($hache).",".sqlesc($agent).",".sqlesc(get_date_time()).")");

$next_id=mysql_insert_id();
sql_query("UPDATE users SET crc=".sqlesc($hache).",idagent=".sqlesc($next_id.",")." WHERE id=".sqlesc($uid)."") or sqlerr(__FILE__,__LINE__);
} elseif ($row_user["crc"]<>$hache) {
$du = sql_query("SELECT id FROM useragent WHERE crc32=".sqlesc($hache)."") or sqlerr(__FILE__,__LINE__);
$rodu = mysql_fetch_array($du); $f_id = $rodu['id']; 

if (isset($f_id) && !stristr($row_user["idagent"],$f_id.",")){
$new_ids=$row_user["idagent"]."".$f_id.",";
sql_query("UPDATE users SET crc=".sqlesc($hache).",idagent=".sqlesc($new_ids)." WHERE id=".sqlesc($uid)."") or sqlerr(__FILE__,__LINE__);
}
elseif (!isset($f_id)){
sql_query("INSERT INTO useragent (crc32, agent, added) VALUES (".sqlesc($hache).",".sqlesc($agent).",".sqlesc(get_date_time()).")");

$new_ids2=$row_user["idagent"]."".mysql_insert_id().",";

if (stristr($row_user["idagent"],mysql_insert_id().",")){
$new_ids2=$row_user["idagent"];
}

sql_query("UPDATE users SET crc=".sqlesc($hache).",idagent=".sqlesc($new_ids2)." WHERE id=".sqlesc($uid)."") or sqlerr(__FILE__,__LINE__);
} else {
sql_query("UPDATE users SET crc=".sqlesc($hache)." WHERE id=".sqlesc($uid)."") or sqlerr(__FILE__,__LINE__);
}}
}


sql_query("UPDATE users SET last_login = NOW() WHERE id = $id") or sqlerr(__FILE__, __LINE__); 
    }
    
}

function logoutcookie() {
    setcookie("tesla", "", 0x7fffffff, "/");
	setcookie("uid", "", 0x7fffffff, "/");
	setcookie("pass", "", 0x7fffffff, "/");
}


function user_session() {
	global $CURUSER, $use_sessions, $anti_httpdoss;

	if (!$use_sessions || $CURUSER["id"]==92)
	return;

	$ip = getip();
	$url = htmlspecialchars_uni(getenv("REQUEST_URI"));

	if (empty($CURUSER)) {
		$uid = -1;
		$username = '';
		$class = -1;
	} else {
		$uid = $CURUSER['id'];
		$username = $CURUSER['username'];
		$class = $CURUSER['class'];
	}
	
//	$past = time() - 300;
	$past = get_date_time(gmtime() - 300);
	$sid = session_id();
	
	$where = array();
	$updateset = array();
///SHOW FULL FIELDS FROM `sessions` ;
	if ($sid)
		$where[] = "sid = ".sqlesc($sid);
	elseif ($uid)
		$where[] = "uid = ".$uid;
	else
		$where[] = "ip = ".sqlesc($ip);

//	$ctime = time();
	$ctime = get_date_time();
	
	$agent = htmlentities($_SERVER["HTTP_USER_AGENT"]);
	$hache = crc32($agent);
	
	$updateset[] = "sid = ".sqlesc($sid);
	$updateset[] = "uid = ".sqlesc($uid);
	$updateset[] = "username = ".sqlesc($username);
	$updateset[] = "class = ".sqlesc($class);
    $updateset[] = "time = ".sqlesc($ctime);
	$updateset[] = "url = ".sqlesc($url);
	$updateset[] = "ip = ".sqlesc($ip);
	
    $s = (($_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://").htmlspecialchars_uni($_SERVER['HTTP_HOST']);
    $site = parse_url($s, PHP_URL_HOST);
    $updateset[] = "host = ".sqlesc($site);

	if ($CURUSER["crc"]<>$hache)
	$updateset[] = "useragent = ".sqlesc($agent);

	$from=getenv("HTTP_REFERER"); 
   
    if ($url == "/")
	$url = "/index.php";

	if (count($updateset) && (stripos($from, $url)!=TRUE))
	sql_query("UPDATE sessions SET ".implode(", ", $updateset)." WHERE ".implode(" AND ", $where));
	// желательно без вывода ошибок, т к ниже задействует код



if (mysql_modified_rows()==0){
sql_query("INSERT INTO sessions (sid, uid, username, class, ip, time, url, host, useragent) VALUES (".implode(", ", array_map("sqlesc", array($sid, $uid, $username, $class, $ip, $ctime, $url, $site, $agent))).")");



///// анти http флуд защита
if (!empty($anti_httpdoss) && !preg_match("/BTWebClient/i", $_SERVER["HTTP_USER_AGENT"])) {
$dt1 = sqlesc(get_date_time(gmtime() - 60));
$num_sesson = number_format(get_row_count("sessions", "WHERE time > ".sqlesc($dt1)." and ip=".sqlesc($ip)." LIMIT 100"));

if ($num_sesson>=4 and $num_sesson<=10){
die("<b>Антибот система</b>: Ограничьте количество одновременных соединений. <br>Сейчас у вас открыто <b>".$num_sesson."</b> сессий с <b>".get_date_time(gmtime() - 60)." - ".get_date_time()." авто: 20 сек</b>");	
$dt2 = sqlesc(get_date_time(gmtime() - 1200));
sql_query("DELETE FROM sessions WHERE time > ".sqlesc($dt2)." and ip='$ip' and class='-1' ");	

} elseif ($num_sesson>=100){
sql_query("DELETE FROM sessions WHERE ip=".sqlesc($ip));
die("<b>Антибот система</b>: Ограничьте количество одновременных соединений. <br>Сейчас у вас открыто <b>".$num_sesson."</b> сессий с <b>".get_date_time(gmtime() - 60)." - ".get_date_time()." авто: 20 сек</b>");
}

}
///// анти http флуд защита

// очистка сессии
sql_query("DELETE FROM sessions WHERE time < ".sqlesc(get_date_time(gmtime() - 1200))." LIMIT 300") or sqlerr(__FILE__,__LINE__);
// очистка сессии
    }
	
if ($CURUSER && !empty($agent)) {
$agent = $_SERVER["HTTP_USER_AGENT"];
$hache = crc32(htmlentities($agent));
$agent = htmlentities($agent);
$time = sqlesc(get_date_time());

if (empty($CURUSER["crc"]) || $CURUSER["crc"]==0){
sql_query("INSERT INTO useragent (crc32, agent, added) VALUES (".sqlesc($hache).",".sqlesc($agent).",".sqlesc(get_date_time()).")");

$next_id=mysql_insert_id();

sql_query("UPDATE users SET crc=".sqlesc($hache).",idagent=".sqlesc($next_id.",")." WHERE id=".sqlesc($uid)."") or sqlerr(__FILE__,__LINE__);
}
elseif ($CURUSER["crc"]<>$hache){

$du = sql_query("SELECT id FROM useragent WHERE crc32=".sqlesc($hache)."") or sqlerr(__FILE__,__LINE__);
$rodu = mysql_fetch_array($du); 
$f_id = $rodu['id']; 

if (isset($f_id) && !stristr($CURUSER["idagent"],$f_id.",")){
$new_ids=$CURUSER["idagent"]."".$f_id.",";
sql_query("UPDATE users SET crc=".sqlesc($hache).",idagent=".sqlesc($new_ids)." WHERE id=".sqlesc($uid)."") or sqlerr(__FILE__,__LINE__);
}
elseif (!isset($f_id)){

sql_query("INSERT INTO useragent (crc32, agent, added) VALUES (".sqlesc($hache).",".sqlesc($agent).",".sqlesc(get_date_time()).")");

$new_ids2=$CURUSER["idagent"]."".mysql_insert_id().",";

if (stristr($CURUSER["idagent"],$row2['id'].",")){
$new_ids2=$CURUSER["idagent"];
}

sql_query("UPDATE users SET crc=".sqlesc($hache).",idagent=".sqlesc($new_ids2)." WHERE id=".sqlesc($uid)."") or sqlerr(__FILE__,__LINE__);
}
else {
sql_query("UPDATE users SET crc=".sqlesc($hache)." WHERE id=".sqlesc($uid)."") or sqlerr(__FILE__,__LINE__);
}

}
}


////// автосмена прав если выше 6
	if (get_user_class() > UC_SYSOP) {
	$culass2=$CURUSER["class"];
    $modcomm2=$CURUSER["usercomment"];
	$modcomment2 = date("Y-m-d") . " - Автосмена прав с $culass2 на 0.\n" . $modcomm2;

	$updateset2[] = "usercomment = '$modcomment2'";
	$updateset2[] = "class = '0'";
	$updateset2[] = "monitoring='yes'";
    $updateset2[] = "promo_time = " . sqlesc(get_date_time());
	sql_query("UPDATE users SET " . implode(", ", $updateset2) . " WHERE id = $uid") or sqlerr(__FILE__, __LINE__);
	
	@header("Location: $DEFAULTBASEURL/index.php");
	die("}0{o}0{"); /// die - просто так
	}

}


function get_server_load() {
	global $tracker_lang, $phpver;
	if (strtolower(substr(PHP_OS, 0, 3)) === 'win') {
		
	
		
		if (class_exists("COM")) {
			if ($phpver=="4") {
				$wmi = new COM("WinMgmts:\\\\.");
				$cpus = $wmi->InstancesOf("Win32_Processor");
				$cpuload = 0;
				$i = 0;
				while ($cpu = $cpus->Next()) {
					$cpuload += $cpu->LoadPercentage;
					$i++;
				}
				$cpuload = round($cpuload / $i, 2);
				return "$cpuload";
			} else {
				$wmi = new COM("WinMgmts:\\\\.");
				$cpus = $wmi->InstancesOf("Win32_Processor");
				$cpuload = 0;
				$i = 0;
				foreach ($cpus as $cpu) {
					$cpuload += $cpu->LoadPercentage;
					$i++;
				}
				$cpuload = round($cpuload / $i, 2);
				return "$cpuload";
			}
		} else
			return $tracker_lang["unknown"];
			
		
			return 0;
	} elseif (@file_exists("/proc/loadavg")) {
		$load = @file_get_contents("/proc/loadavg");
		$serverload = explode(" ", $load);
		$serverload[0] = round($serverload[0], 4);
		if(!$serverload) {
			$load = @exec("uptime");
			$load = explode("load averages?: ", $load);
			$serverload = explode(",", $load[1]);
		}
	} else {
		$load = @exec("uptime");
		$load = explode("load averages?: ", $load);
		$serverload = explode(",", $load[1]);
	}
	$returnload = trim($serverload[0]);
	if(!$returnload) {
		$returnload = $tracker_lang["unknown"];
	}
	return $returnload;
}

function unesc($x) {
	if (get_magic_quotes_gpc())
		return stripslashes($x);
	return strip_tags($x);
}

/*function gzip2() {
	if (@extension_loaded('zlib') && @ini_get('zlib.output_compression') != '1' && @ini_get('output_handler') != 'ob_gzhandler' && $use_gzip == "yes") {
		@ob_start('ob_gzhandler');
	}
}*/

function gzip() {
    
    if (!preg_match("/login/i", $_SERVER["SCRIPT_FILENAME"]) && !preg_match("/signup/i", $_SERVER["SCRIPT_FILENAME"]) && !preg_match("/recover/i", $_SERVER["SCRIPT_FILENAME"])){
    	
    global $use_gzip;
    static $already_loaded; /// берем данные для проверки
   
    if (extension_loaded('zlib') && ini_get('zlib.output_compression') <>'1' && ini_get('output_handler') <> 'ob_gzhandler' && $use_gzip=='yes' && !$already_loaded) {
        @ob_start('ob_gzhandler');
        $already_loaded = true; /// помечаем что использовали
    } else {
      @ob_start();   //Страница, которую вы пытаетесь просмотреть, не может быть показана, так как она использует неверную или неподдерживаемую форму компрессии.
       }
       
       }
}

// IP Валидность только как функция проверки правильности ip адресса
function validip($ip) {
	if (!empty($ip) && $ip == long2ip(ip2long($ip)))
	{
		// reserved IANA IPv4 addresses
		// http://www.iana.org/assignments/ipv4-address-space
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

		foreach ($reserved_ips as $r) {
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}

function validip_pmr($ip=false) {
	
	if (empty($ip))
	$ip=getip();
	
	if (!empty($ip) && $ip == long2ip(ip2long($ip)))
	{
		$reserved_ips = array (
				array('80.94.240.0','80.94.255.255'),
				array('77.235.96.0','77.235.127.255'),
				array('62.221.64.0','62.221.127.255'),
				array('95.153.64.0','95.153.127.255'),
				array('217.19.208.0','217.19.223.255')
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

function autoclean() {
	global $autoclean_interval;

	$now = time();
	$docleanup = 0;

	$res = sql_query("SELECT value_u FROM avps WHERE arg = 'lastcleantime'");
	$row = mysql_fetch_array($res);
	
	if (empty($row)) {
		sql_query("INSERT INTO avps (arg, value_u) VALUES ('lastcleantime',$now)");
		return;
	}
	$ts = $row[0];
	if ($ts + $autoclean_interval > $now)
		return;

	sql_query("UPDATE avps SET value_u=$now WHERE arg='lastcleantime' AND value_u = $ts");
	
	if (!mysql_affected_rows())	
    return;
	
	require_once(ROOT_PATH.'include/cleanup.php');

	docleanup();
}

function mksize($bytes) {
 if ($bytes < 1000 * 1024)
		return number_format($bytes / 1024, 2) . " КБ";
	elseif ($bytes < 1000 * 1048576)
		return number_format($bytes / 1048576, 2) . " МБ";
	elseif ($bytes < 1000 * 1073741824)
		return number_format($bytes / 1073741824, 2) . " ГБ";
	else
		return number_format($bytes / 1099511627776, 2) . " ТБ";
}

function mksizeint($bytes) {
		$bytes = max(0, $bytes);
		if ($bytes < 1000)
				return floor($bytes) . " Байт";
		elseif ($bytes < 1000 * 1024)
				return floor($bytes / 1024) . " кб";
		elseif ($bytes < 1000 * 1048576)
				return floor($bytes / 1048576) . " МБ";
		elseif ($bytes < 1000 * 1073741824)
				return floor($bytes / 1073741824) . " ГБ";
		else
				return floor($bytes / 1099511627776) . " ТБ";
}

function deadtime() {
	global $announce_interval;
	return time() - floor($announce_interval * 1.3);
}

function timesec($input, $time = false) {
    $search = array('January','February','March','April','May','June','July','August','September','October','November','December');
    $replace = array('Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа','Сентября','Октября','Ноября','Декабря');
    $seconds = strtotime($input);
  //  if ($time == true)
     //   $data = date("j F Y в H:i:s", $seconds);
  //  else
        $data = date("H:i:s", $seconds);
    $data = str_replace($search, $replace, $data);
    return $data;
}


function normaltime($input, $time = false) {
    $search = array('January','February','March','April','May','June','July','August','September','October','November','December');
    $replace = array('Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа','Сентября','Октября','Ноября','Декабря');
    $seconds = strtotime($input);
    if ($time == true)
        $data = date("j F Y в H:i:s", $seconds);
    else
        $data = date("j F Y", $seconds);
    $data = str_replace($search, $replace, $data);
    return $data;
}

function normaltime2($input, $time = false) {
     return $input;
}


function mkprettytime($s) {
    if ($s < 0)
	$s = 0;
    $t = array();
    foreach (array("60:sec","60:min","24:hour","0:day") as $x) {
		$y = explode(":", $x);
		if ($y[0] > 1) {
		    $v = $s % $y[0];
		    $s = floor($s / $y[0]);
		} else
		    $v = $s;
	$t[$y[1]] = $v;
    }

    if ($t["day"])
	return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    if ($t["hour"])
	return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
	return sprintf("%d:%02d", $t["min"], $t["sec"]);
}

function mkglobal($vars) {
	if (!is_array($vars))
		$vars = explode(":", $vars);
	foreach ($vars as $v) {
		if (isset($_GET[$v]))
			$GLOBALS[$v] = unesc($_GET[$v]);
		elseif (isset($_POST[$v]))
			$GLOBALS[$v] = unesc($_POST[$v]);
		else
			return 0;
	}
	return 1;
}

function tr($x, $y, $noesc=0, $prints = true, $width = "", $relation = '') {
	if ($noesc)
		$a = $y;
	else {
		$a = htmlspecialchars_uni($y);
		$a = str_replace("\n", "<br />\n", $a);
	}
	if ($prints) {
	  $print = "<td width=\"". $width ."\" class=\"heading\" valign=\"top\" align=\"right\">$x</td>";
	  $colpan = "align=\"left\"";
	} else {
		$colpan = "colspan=\"2\"";
	}

	print("<tr".( $relation ? " relation=\"$relation\"" : "").">$print<td valign=\"top\" $colpan>$a</td></tr>\n");
}

function validfilename($name) {
	return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}


function validimage($file_path,$blank) {

global $CURUSER;

if (!@getimagesize ($file_path)){
stderr("Ошибка данных", "Это не изображение!");
}

if ($CURUSER){
$user = $CURUSER["username"];
$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER["username"]);    
} else 
stderr("Ошибка данных", "Не авторизованы!");

        $file_contents = @file_get_contents($file_path);

        $functions_to_shell = array ("include", "file", "fwrite", "script", "body", "java","fopen", "fread", "require", "exec", "system", "passthru", "eval", "copy" );
        foreach ($functions_to_shell as $funct){
       if (preg_match("/" . $funct ."+(\\s||)+[(]/", $file_contents)) {
       	
      $usercomment=$CURUSER["usercomment"];
       	 
     $poput=" $user пытался залить shell";
     if (!stristr($usercomment,$poput)!==false){
     $usercomment = get_date_time() . " пытался залить shell.\n". $usercomment;
mysql_query("UPDATE users SET usercomment='$usercomment' WHERE id=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
     }
       	
    write_log("$blank, $user пытался залить shell.","$user_color","error");
        stderr("Ошибка данных", "Данное изображение нельзя залить на трекер, в нем присутствуют запрещенные исполняемые коды (v2)");
}
}
        
}

function validemail($email) {
  if (preg_match("%^[A-Za-z0-9._-](([_\.\-]?[a-zA-Z0-9._-]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z])+$%", $email)) {
        return true;
    }
    else 
  return false;
}

function check_url($url) {
    if (preg_match("/^((www.)?([\w, -]+.)(com|net|org|info|biz|spb\.ru|msk\.ru|com\.ru|org\.ru|net\.ru|ru|su|us|bz|ws))$/", $url)) {
        return true;
    }
    return false;
}


function sent_mail($to,$fromname,$fromemail,$subject,$body,$multiple=false,$multiplemail='') {
	global $SITENAME,$SITEEMAIL,$smtptype,$smtp,$smtp_host,$smtp_port,$smtp_from,$smtpaddress,$accountname,$accountpassword;
	# Sent Mail Function v.05 by xam (This function to help avoid spam-filters.)
	
	$parse_url = end(explode('.', parse_url($DEFAULTBASEURL, PHP_URL_HOST)));
    if ($parse_url == "localhost")
	return false;
	
      $result = true;
	if ($smtptype == 'default') {
		@mail($to, $subject, $body, "From: $SITEEMAIL") or $result = false;
	} elseif ($smtptype == 'advanced') {
	# Is the OS Windows or Mac or Linux?
	if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
		$eol="\r\n";
		$windows = true;
	}
	elseif (strtoupper(substr(PHP_OS,0,3)=='MAC'))
		$eol="\r";
	else
		$eol="\n";
	$mid = md5(getip() . $fromname);
	$name = $_SERVER["SERVER_NAME"];
	$headers .= "From: $fromname <$fromemail>".$eol;
	$headers .= "Reply-To: $fromname <$fromemail>".$eol;
	$headers .= "Return-Path: $fromname <$fromemail>".$eol;
	$headers .= "Message-ID: <$mid thesystem@$name>".$eol;
	$headers .= "X-Mailer: PHP v".phpversion().$eol;
    $headers .= "MIME-Version: 1.0".$eol;
    $headers .= "Content-type: text/plain; charset=windows-1251".$eol;
    $headers .= "X-Sender: PHP".$eol;
    if ($multiple)
    	$headers .= "Bcc: $multiplemail.$eol";
	if ($smtp == "yes") {
		ini_set('SMTP', $smtp_host);
		ini_set('smtp_port', $smtp_port);
		if ($windows)
			ini_set('sendmail_from', $smtp_from);
		}

    	@mail($to, $subject, $body, $headers) or $result = false;

    	ini_restore(SMTP);
		ini_restore(smtp_port);
		if ($windows)
			ini_restore(sendmail_from);
	} elseif ($smtptype == 'external') {

		require_once(ROOT_PATH.'include/functions_smtp.lib.php');
		$mail = new smtp;
        $mail->debug(false);
		$mail->open($smtp_host, $smtp_port);
		if (!empty($accountname) && !empty($accountpassword))
		$mail->auth($accountname, $accountpassword);
		$mail->from($SITEEMAIL);
		$mail->to($to);
		$mail->subject($subject);
		$mail->body($body);
		$result = $mail->send();
		$mail->close();
	} else
		$result = false;

	return $result;
}

function sqlesc($value) {

$d = array("CREATE","UPDATE","DROP","INSERT","DATABASE","javascript","SUBSTRING","FIND_IN_SET");

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

function sqlwildcardesc($x) {
	return str_replace(array("%","_"), array("\\%","\\_"), mysql_real_escape_string($x));
}
		
function array_size($arr) {
    ob_start();
    print_r($arr);
    $mem = ob_get_contents();
    ob_end_clean();
    $mem = preg_replace("/\n +/", "", $mem);
    $mem = strlen($mem);
    return $mem;
}

function debug($value=false) {
if (isset($_GET['debug']))
{
echo("<center>");
$num=1;
foreach($GLOBALS as $key=>$value){
    $memEstimate = array_size($value);
    if ($memEstimate>="1000")
    $memEstimate="<b><font color=\"red\">".$memEstimate."</b></font>";
    else
    $memEstimate="<font color=\"blue\">".$memEstimate."</font>";
    echo("[<b>$num</b>]: <b>".$key.'</b> => '.$memEstimate);
    echo("<br>");
    $num++;
}
echo("</center>");
}
}

function stdhead($title = "", $msgalert = true) {

global $CURUSER, $SITE_ONLINE,$ICE, $SITENAME, $DEFAULTBASEURL, $ss_uri, $tracker_lang, $default_theme;

if (isset($_GET['copyright'])){
 print "<title>Копирайт сайта $SITENAME</title>
<style>body {background:#0000aa;color:#ffffff;font-family:courier;font-size:12pt;text-align:center;margin:100px;} blink {color:yellow;} p {margin:30px 100px;text-align:left;} a,a:hover {color:inherit;font:inherit;} .menu {text-align:center;margin-top:50px;} </style>
</head><body>
<p><center>Сайт поднят на движке под названием <b>Tesla Tracker (TT)</b> © 2010 года. Владельцами являются <b>7Max7</b> и <b>Imperator</b></center><br/>
* Любое копирование движка в целях продажи без ведома создателя запрещенно.<br />
* Любое изменение движка в целях работы трекера без ведома создателя запрещенно.</p>
Если присутствует копирайт значить есть запрос чистки <blink>_</blink>
<hr><blink><font color=white>Предновогодняя цена 60$</font></blink>
</div></body>";
die;
}

// Closed site notice //
$CLOSE_NOTICE = '<title>Сайт временно отключен</title>
<LINK href="pic/favicon.ico" type="image/x-icon" rel="shortcut Icon"><link rel="stylesheet" href="pic/error/css.css" type="text/css"><div align=center><table width="1000" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#194731" height="100%"><tr><td width="125"><img src="pic/error/up_left.gif" alt="" width="125" height="51"></td><td height="51" width="750"><img src="pic/error/the_way.jpg" width="750" height="51"></td><td width="125"><img src="pic/error/up_right.gif" width="125" height="51"></td></tr><tr><td background="pic/error/left_bgx.jpg" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%"><tr><td background="pic/error/up_left2.jpg" align="center" height="417" valign="top">&nbsp;</td></tr></table></td><td valign="top" align="center" bgcolor="#66756E" height="100%"><table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%"><tr><td><img src="pic/error/logo.jpg" width="750" height="118" border="0"></td></tr><tr><td height="100%"><table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%"><tr><td bgcolor="#001F10" width="2" valign="top"><img src="pic/error/pixel.gif" alt="" width="2" height="1"></td><td bgcolor="#96A79D" width="1" valign="top"><img src="pic/error/pixel.gif" alt="" width="1" height="1"></td><td bgcolor="#728079" valign="top" align="center" height="100%"><table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%"><b>Сайт находится на текущей реконструкции, после завершения всех работ сайт будет открыт.<br/><br/>Приносим вам свои извиняшки за доставленные неудобства.<br></table></td><td bgcolor="#001F10" width="2" valign="top"><img src="pic/error/pixel.gif" alt="" width="2" height="1"></td></tr></table></td></tr></table></td><td background="pic/error/right_bgx.jpg" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%"><tr><td background="/pic/error/up_right2.jpg" align="center" valign="top">&nbsp;</td></tr></table></td></tr><tr><td width="125"><img src="/pic/error/down_left.gif" alt="" width="125" height="116"></td><td height="116" background="/pic/error/down.jpg" align="center" valign="bottom" style="padding-bottom: 25px"></td><td width="125"><img src="/pic/error/down_right.gif" alt="" width="125" height="116"></td></tr></table></div>
';
// end notice //

	if (!$SITE_ONLINE)
		die($CLOSE_NOTICE);

    if (!headers_sent()){
	header("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
	header("X-Powered-by: Tesla Tracker TT (2010) [Предновогодняя] - www.muz-tracker.net");
	header("X-Chocolate-to: ICQ 225454228 (7Max7)");
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");
	}
			if (empty($title))
			$title = $SITENAME;
		else
			$title = htmlspecialchars_uni($title)." - ".$SITENAME;

		$ss_uri=$CURUSER["stylesheet"];
	
	if (!$CURUSER || empty($CURUSER["stylesheet"]))	{
	$ss_uri = $default_theme;
	}
    else
    $ss_uri=$CURUSER["stylesheet"];

	@require_once(ROOT_PATH."themes/" . htmlentities($ss_uri) . "/template.php");
	@require_once(ROOT_PATH."themes/" . htmlentities($ss_uri) . "/stdhead.php");

} // stdhead


//для чата
function stdheadchat($title = "", $msgalert = true) {
	global $CURUSER, $SITE_ONLINE,$ICE, $SITENAME, $DEFAULTBASEURL, $ss_uri, $tracker_lang, $default_theme;

if (isset($_GET['copyright'])){
 print "<title>Копирайт сайта $SITENAME</title>
<style>body {background:#0000aa;color:#ffffff;font-family:courier;font-size:12pt;text-align:center;margin:100px;} blink {color:yellow;} p {margin:30px 100px;text-align:left;} a,a:hover {color:inherit;font:inherit;} .menu {text-align:center;margin-top:50px;} </style>
</head><body>
<p><center>Сайт поднят на движке под названием <b>Tesla Tracker (TT)</b> © 2010 года. Владельцами являются <b>7Max7</b> и <b>Imperator</b></center><br/>
* Любое копирование движка в целях продажи без ведома создателя запрещенно.<br />
* Любое изменение движка в целях работы трекера без ведома создателя запрещенно.</p>
Если присутствует копирайт значить есть запрос чистки <blink>_</blink>
<hr><blink><font color=white>Предновогодняя цена 60$</font></blink>
</div></body>";
die;
}

    if (!headers_sent()){
	header("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
	header("X-Powered-by: Tesla Tracker TT (2010) [Предновогодняя] - www.muz-tracker.net");
	header("X-Chocolate-to: ICQ 225454228 (7Max7)");
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");
	}
		
		if (empty($title))
			$title = $SITENAME;
		else
			$title = htmlspecialchars_uni($title)." - ".$SITENAME;

	
	$ss_uri=$CURUSER["stylesheet"];
	
	if (!$CURUSER || empty($CURUSER["stylesheet"]))	{
	$ss_uri = $default_theme;
	}
    else
    $ss_uri=$CURUSER["stylesheet"];

	@require_once(ROOT_PATH."themes/" . htmlentities($ss_uri) . "/template.php");
	@require_once(ROOT_PATH."themes/" . htmlentities($ss_uri) . "/stdheadchat.php");

} // stdheadchat


function stdfoot() {
	global $CURUSER, $ss_uri, $tracker_lang, $queries, $tstart, $query_stat, $querytime;
	
    @require_once(ROOT_PATH."themes/" . htmlentities($ss_uri) . "/template.php");
	@require_once(ROOT_PATH."themes/" . htmlentities($ss_uri) . "/stdfoot.php");
		
if ((DEBUG_MODE) && count($query_stat) && get_user_class() == UC_SYSOP && isset($_COOKIE["debug"]) && $_COOKIE["debug"]=="yes") {
	
		foreach ($query_stat as $key => $value) {
			print("<div>[<b>".($key+1)."</b>] => <b>
			".($value["seconds"] <= 0.0009 ? "<font color=\"#0468f1\" title=\"Сверхбыстрый запрос. Время исполнения отличное.\">".$value["seconds"]."</font>":"
		    ".($value["seconds"] >= 0.01 ? "<font color=\"red\" title=\"Рекомендуется оптимизировать запрос. Время исполнения превышает норму.\">".$value["seconds"]."</font>" : "<font color=\"blue\" title=\"Запрос не нуждается в оптимизации. Время исполнения допустимое.\">".$value["seconds"]."</font>" )."
		    ")."
		  </b> [".$value["query"]."]</div>\n");
		}
	debug();
	}
}

function stdfootchat() {
	global $CURUSER, $ss_uri, $tracker_lang,$queries, $tstart, $query_stat, $querytime;

	@require_once(ROOT_PATH."themes/" . htmlentities($ss_uri) . "/template.php");
	@require_once(ROOT_PATH."themes/" . htmlentities($ss_uri) . "/stdfootchat.php");
			
if ((DEBUG_MODE) && count($query_stat) && get_user_class() == UC_SYSOP && isset($_COOKIE["debug"]) && $_COOKIE["debug"]=="yes") {
	
		foreach ($query_stat as $key => $value) {
			print("<div>[<b>".($key+1)."</b>] => <b>
			".($value["seconds"] <= 0.0009 ? "<font color=\"#0468f1\" title=\"Сверхбыстрый запрос. Время исполнения отличное.\">".$value["seconds"]."</font>":"
		    ".($value["seconds"] >= 0.01 ? "<font color=\"red\" title=\"Рекомендуется оптимизировать запрос. Время исполнения превышает норму.\">".$value["seconds"]."</font>" : "<font color=\"blue\" title=\"Запрос не нуждается в оптимизации. Время исполнения допустимое.\">".$value["seconds"]."</font>" )."
		    ")."
		  </b> [".$value["query"]."]</div>\n");
		}
	debug();
	}
}

function genbark($x,$y) {
	stdhead($y);
	print("<h2>" . htmlspecialchars_uni($y) . "</h2>\n");
	print("<p>" . htmlspecialchars_uni($x) . "</p>\n");
	stdfoot();
	exit();
}

function mksecret($length = 20) {
$set = array("a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J","k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T","u","U","v","V","w","W","x","X","y","Y","z","Z","1","2","3","4","5","6","7","8","9");
	$str="";
	for($i = 1; $i <= $length; $i++)
	{
		$ch = rand(0, count($set)-1);
		$str .= $set[$ch];
	}
	return $str;
}

function httperr($code = 404) {
	$sapi_name = php_sapi_name();
	if ($sapi_name == 'cgi' OR $sapi_name == 'cgi-fcgi') {
	header('Status: 404 Not Found');
	} else {
    header('HTTP/1.1 404 Not Found');
	}
	die;
}

function gmtime() {
	return strtotime(get_date_time());
}

function loggedinorreturn($nowarn = false) {
	global $CURUSER, $DEFAULTBASEURL;
	
$url=$DEFAULTBASEURL."/login.php?returnto=" . htmlentities(basename($_SERVER["REQUEST_URI"]));

if (!$CURUSER) {

if (!headers_sent()) {
header("Location: $DEFAULTBASEURL/login.php?returnto=" . htmlentities(basename($_SERVER["REQUEST_URI"])).($nowarn ? "&nowarn=1" : ""));
die;
} else {
die("Перенаправление на страницу ввода данных.<script>setTimeout('document.location.href=\"$url\"', 100);</script>");
}

} elseif (isset($CURUSER) && strtotime($CURUSER['last_access']) < (strtotime(get_date_time()) - 250)) {
/// защита от кражи куков aka 7Max7
if ($CURUSER["shelter"]=="ag"){
$she_md5=crc32(htmlentities($_SERVER["HTTP_USER_AGENT"]));
} else {
$she_md5=getip();
}
$tesla_ip = "T".md5($CURUSER["id"]+$she_md5+$CURUSER["id"]);

if ($_COOKIE["tesla"]<>$tesla_ip){
logoutcookie;

die("Перенаправление на страницу ввода данных.<script>setTimeout('document.location.href=\"$url\"', 10);</script>");
}
/// защита от кражи куков aka 7Max7
}

}
//// удалить изображение
function deletetorrent($id) {
    global $torrent_dir;
    $id=(int)$id;

    sql_query("DELETE FROM torrents WHERE id = $id");
    sql_query("DELETE FROM bookmarks WHERE torrentid = $id");  
    sql_query("DELETE FROM snatched WHERE torrent = $id");
    sql_query("DELETE FROM report WHERE torrentid= $id");
    sql_query("DELETE FROM thanks WHERE torrentid= $id");
    sql_query("DELETE FROM checkcomm WHERE checkid= $id AND torrent = 1");
    
    foreach(explode(".","peers.files.comments.ratings") as $x)
    sql_query("DELETE FROM $x WHERE torrent = $id");
        
    @unlink(ROOT_PATH.$torrent_dir."/".$id.".torrent");
}

function pager($rpp, $count, $href, $opts = array()) {
	$pages = ceil($count / $rpp);

	if (empty($opts["lastpagedefault"]))
		$pagedefault = 0;
	else {
		$pagedefault = ceil($count / $rpp);
		if ($pagedefault < 0)
			$pagedefault = 0;
	}

	if (!empty($_GET["page"])) {

  $page = (int) $_GET["page"];
		if ($page < 0)
		$page = $pagedefault;
}
	else
		$page = $pagedefault;
		
	if (!empty($_GET["page"]) AND $_GET["page"]=="last"){
		$pagedefault = ceil($count / $rpp);
		$page = $pagedefault;
	}
		

	if ($page==0) {
		$pagipage=1;
	$start = $page  * $rpp;
	}
	else 
	{
	$pagipage = $page;
	$start = ($page -1) * $rpp;
	}

    if ($pages<>0){
    	
   	
	if (fuckIE()) {
	$pagerr ='<div class="paginator" id="paginator1"></div>
	<div class="paginator_pages">Всего: '.$pages. ' стр.</div>
	<script type="text/javascript">
	window.onload = function(){
		pag1 = new Paginator(\'paginator1\', '.$pages.', 20, "'. ($pagipage ) .'", "'.$href.'page=");
		pag2 = new Paginator(\'pag2\', '.$pages.', 20, "'. ($pagipage ) .'", "'.$href.'page=");
		Paginator.resizePaginator(pag1);
		Paginator.resizePaginator(pag2);
	}
	</script>';

	$pagerre ='<div id="dataa">
	<div class="paginator" id="pag2"></div>
	<div class="paginator_pages">Количество страниц: '.$pages. ' | На страничку: '.$rpp.' | Данных: '.$count.' </div>';

	} else {
/// для мозилы

/*	pag1 = new Paginator(\'paginator1\', '.$pages.', 20, "'. ($pagipage ) .'", "'.$href.'page="); - убрал поставил window.onload ниже
*/
	$pagerr ='<div class="paginator" id="paginator1"></div>
	<div class="paginator_pages">Количество страниц: '.$pages. ' | На страничку: '.$rpp.' | Данных: '.$count.' </div>
	<script type="text/javascript">
	window.onload = function(){
	pag1 = new Paginator(\'paginator1\', '.$pages.', 20, "'. ($pagipage ) .'", "'.$href.'page=");
	pag2 = new Paginator(\'pag2\', '.$pages.', 20, "'. ($pagipage ) .'", "'.$href.'page=");
	Paginator.resizePaginator(pag1);
	Paginator.resizePaginator(pag2);
	}
	</script>';

	$pagerre ='<div class="paginator" id="pag2"></div>
	<div class="paginator_pages">Количество страниц: '.$pages. ' | На страничку: '.$rpp.' | Данных: '.$count.' </div>
	<script type="text/javascript">
		pag2 = new Paginator(\'pag2\', '.$pages.', 20, "'. ($pagipage ) .'", "'.$href.'page=");
	</script></div>';
}

	return array($pagerr, $pagerre, "LIMIT $start,$rpp");
	}
}

function fuckIE() {
$user_agent = htmlentities($_SERVER['HTTP_USER_AGENT']);
$browserIE = false;
if (stristr($user_agent, 'MSIE 7.0')) $browserIE = true;
if (stristr($user_agent, 'MSIE 6.0')) $browserIE = true;
if (stristr($user_agent, 'MSIE 5.0')) $browserIE = true;
return $browserIE;
}

//// статистика посещений задается через 
function info() {
$sf =ROOT_PATH."cache/info_cache_stat.txt"; 
$fpsf=@fopen($sf,"a+"); 
$ip=getip(); 
$ag=getenv("HTTP_USER_AGENT"); 
$from=getenv("HTTP_REFERER"); 
$host=getenv("REQUEST_URI"); 
$date = date("d.m.y"); 
$time= date("H:i:s"); 
@fputs($fpsf,"$date#$time#$ip#$ag#$from#$host\n"); 
@fclose($fpsf); 
}


function downloaderdata($res) {
	$rows = array();
	$ids = array();
	$peerdata = array();
	while ($row = mysql_fetch_assoc($res)) {
		$rows[] = $row;
		$id = $row["id"];
		$ids[] = $id;
		$peerdata[$id] = array("downloaders" => 0, "seeders" => 0, "comments" => 0);
	}

	if (count($ids)) {
		$allids = implode(",", $ids);
		$res = sql_query("SELECT COUNT(*) AS c, torrent, seeder FROM peers WHERE torrent IN ($allids) GROUP BY torrent, seeder");
		while ($row = mysql_fetch_assoc($res)) {
			if ($row["seeder"] == "yes")
				$key = "seeders";
			else
				$key = "downloaders";
			$peerdata[$row["torrent"]][$key] = $row["c"];
		}
		$res = sql_query("SELECT COUNT(*) AS c, torrent FROM comments WHERE torrent IN ($allids) GROUP BY torrent");
		while ($row = mysql_fetch_assoc($res)) {
			$peerdata[$row["torrent"]]["comments"] = $row["c"];
		}
	}

	return array($rows, $peerdata);
}

function commenttable($rows, $redaktor = "comment") {
	global $CURUSER, $avatar_max_width,$DEFAULTBASEURL;

		
	$count = 0;
	foreach ($rows as $row)	{
			    if ($row["downloaded"] > 0) {
			    	$ratio = $row['uploaded'] / $row['downloaded'];
			    	$ratio = number_format($ratio, 2);
			    } elseif ($row["uploaded"] > 0) {
			    	$ratio = "Infinity";
			    } else {
			    	$ratio = "---";
			    }
			     if (strtotime($row["last_access"]) > gmtime() - 600) {
			     	$online = "online";
			     	$online_text = "В сети";
			     } else {
			     	$online = "offline";
			     	$online_text = "Не в сети";
			     }

	   print("<table width=100% border=1 cellspacing=0 cellpadding=3>");
	   print("<tr><td class=a align=\"left\" colspan=\"2\" height=\"24\">");

    if (isset($row["username"]))
		{
			$title = $row["title"];
			if ($title == ""){
				$title = get_user_class_name($row["class"]);
			}else{
				$title = htmlspecialchars_uni($title);
			}

if ($row["hiderating"]=="yes") {
$print_ratio="<b>+100%</b>";
}
else
$print_ratio="<img src=\"".$DEFAULTBASEURL."/pic/upl.gif\" alt=\"Залито\" border=\"0\" width=\"12\" height=\"12\"> ".mksize($row["uploaded"]) ." : <img src=\"".$DEFAULTBASEURL."/pic/down.gif\" alt=\"Скаченно\" border=\"0\" width=\"12\" height=\"12\"> ".mksize($row["downloaded"])." : $ratio";

			   print("".($CURUSER["class"]>"3" ? "<img src=\"".$DEFAULTBASEURL."/pic/button_".$online.".gif\" alt=\"".$online_text."\" title=\"".$online_text."\" style=\"position: relative; top: 2px;\" border=\"0\" height=\"14\">" : "")."
			   "." <a name=comm". $row["id"]." href=".$DEFAULTBASEURL."/userdetails.php?id=" . $row["user"] . " class=altlink_white ><b>". get_user_class_color($row["class"], htmlspecialchars_uni($row["username"])) . "</b></a> ::"
		       .($row["donor"] == "yes" ? "<img src='".$DEFAULTBASEURL."/pic/star.gif' alt='Донор'>" : "")
		       .($row["warned"] == "yes" ? "<img src=\"".$DEFAULTBASEURL."/pic/warned.gif\" alt=\"Предупрежден\">" : "") 
			   .($row["enabled"] == "no" ? "<img src=\"".$DEFAULTBASEURL."/pic/warned2.gif\" alt=\"Отключен\">" : "") 
			   .$title." :: ".$print_ratio." :: "
               .($CURUSER["cansendpm"]=='yes' && ($CURUSER["id"]<>$row['user'])? " <a href='".$DEFAULTBASEURL."/message.php?action=sendmessage&amp;receiver=".$row['user']."'>"."<img src=pic/button_pm.gif border=0 alt=\"Отправить сообщение\" ></a>" : "")." \n");
		   } else {
          print("".($row["user"] == "0" ? "<font color=gray>[<b>System</b>]</font>" : "Этот пользователь был Удален [$row[id]]")." \n");
	       }

////аватары
$avatar = (!empty($row["avatar"]) ? htmlspecialchars_uni($DEFAULTBASEURL."/pic/avatar/".$row["avatar"]) : "");
    if (empty($avatar)){$avatar = $DEFAULTBASEURL."/pic/default_avatar.gif"; }
////аватары

  	$text = format_comment($row["text"]);
  	
	if ($CURUSER) {
	$text = str_replace("$CURUSER[username]","<font color=#".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"]).">$CURUSER[username]</font>",$text);
	}
	if ($row["editedby"])	{
	if ($CURUSER["id"] == $row["editedby"] || get_user_class() >= UC_MODERATOR)
	 {
	       $text .= "<p align=right><font size=1 class=small>Последний раз редактировалось <a href=userdetails.php?id=$row[editedby]><b>
		   ".get_user_class_color($row["classbyname"] ,$row["editedbyname"])."
		   </b></a> в $row[editedat]</font></p>\n";
	}
	 }
		print("</td></tr>");
		print("<tr valign=top>\n");
	
		print("<td style=\"padding: 0px; width: 5%;\" align=\"center\">
		".($CURUSER["id"]==$row["user"] ? "<a href=\"my.php\"><img alt=\"Аватар, по клику переход в настройки\" title=\"Аватар, по клику переход в настройки\" border=\"0\" src=$avatar width=\"$avatar_max_width\"></a>":"<img src=$avatar width=\"$avatar_max_width\">")."
        ".($row["support"]== "yes" ? htmlspecialchars_uni($row["supportfor"]) : "")."
		</td>\n");
		print("<td width=100% class=text>");

		print($text."".(($row["signatrue"]=="yes" && $row["signature"])? "<p valign=down align=down><hr>".format_comment($row["signature"])."</p>": "")."
		</td>\n");
		print("</tr>\n");
		print("<tr><td class=a align=\"center\" colspan=\"2\">");
		print"<div style=\"float: left; width: auto;\">[" . normaltime($row["added"],true) . "]</div>";
	
		print("<div align=\"right\">".(get_user_class() >= UC_MODERATOR ? "".($row["ip"] ? "[<a href=\"".$DEFAULTBASEURL."/usersearch.php?ip=$row[ip]\" >".$row["ip"]."</a>] " : "Неизвестен " ) : "")
		.($row["user"] == "0" ? "" : "".($CURUSER["commentpos"] == 'yes' ? "[<a href=\"".$DEFAULTBASEURL."/".$redaktor.".php?action=quote&amp;cid=$row[id]\" >Цитата</a>]":"")."")
		    .($row["editedby"] && get_user_class() >= UC_MODERATOR ? "
		    ".($CURUSER["commentpos"] == 'yes' ? "[<a href=\"".$DEFAULTBASEURL."/".$redaktor.".php?action=vieworiginal&amp;cid=$row[id]\" >Оригинал</a>]":"")."
			" : "")
			.($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "
			".($CURUSER["commentpos"] == 'yes' ? "[<a href=".$DEFAULTBASEURL."/".$redaktor.".php?action=edit&amp;cid=$row[id] >Изменить</a>]":"")."
			" : "")
		    .(get_user_class() >= UC_MODERATOR ? "
		    ".($CURUSER["commentpos"] == 'yes' ? "[<a href=\"".$DEFAULTBASEURL."/".$redaktor.".php?action=delete&amp;cid=$row[id]\" >Удалить</a>]":"")."
			" : "")."
		</td></tr>");
		print("</table><br>");
  }

}

function utf8_to_win($str,$to = "w") {

$outstr="";
$recode=array();
$recode[k]=array(0x2500,0x2502,0x250c,0x2510,0x2514,0x2518,0x251c,0x2524,0x252c,0x2534,0x253c,0x2580,0x2584,0x2588,0x258c,0x2590,0x2591,0x2592,0x2593,0x2320,0x25a0,0x2219,0x221a,0x2248,0x2264,0x2265,0x00a0,0x2321,0x00b0,0x00b2,0x00b7,0x00f7,0x2550,0x2551,0x2552,0x0451,0x2553,0x2554,0x2555,0x2556,0x2557,0x2558,0x2559,0x255a,0x255b,0x255c,0x255d,0x255e,0x255f,0x2560,0x2561,0x0401,0x2562,0x2563,0x2564,0x2565,0x2566,0x2567,0x2568,0x2569,0x256a,0x256b,0x256c,0x00a9,0x044e,0x0430,0x0431,0x0446,0x0434,0x0435,0x0444,0x0433,0x0445,0x0438,0x0439,0x043a,0x043b,0x043c,0x043d,0x043e,0x043f,0x044f,0x0440,0x0441,0x0442,0x0443,0x0436,0x0432,0x044c,0x044b,0x0437,0x0448,0x044d,0x0449,0x0447,0x044a,0x042e,0x0410,0x0411,0x0426,0x0414,0x0415,0x0424,0x0413,0x0425,0x0418,0x0419,0x041a,0x041b,0x041c,0x041d,0x041e,0x041f,0x042f,0x0420,0x0421,0x0422,0x0423,0x0416,0x0412,0x042c,0x042b,0x0417,0x0428,0x042d,0x0429,0x0427,0x042a);

$recode[w]=array(0x0402,0x0403,0x201A,0x0453,0x201E,0x2026,0x2020,0x2021,0x20AC,0x2030,0x0409,0x2039,0x040A,0x040C,0x040B,0x040F,0x0452,0x2018,0x2019,0x201C,0x201D,0x2022,0x2013,0x2014,0x0000,0x2122,0x0459,0x203A,0x045A,0x045C,0x045B,0x045F,0x00A0,0x040E,0x045E,0x0408,0x00A4,0x0490,0x00A6,0x00A7,0x0401,0x00A9,0x0404,0x00AB,0x00AC,0x00AD,0x00AE,0x0407,0x00B0,0x00B1,0x0406,0x0456,0x0491,0x00B5,0x00B6,0x00B7,0x0451,0x2116,0x0454,0x00BB,0x0458,0x0405,0x0455,0x0457,0x0410,0x0411,0x0412,0x0413,0x0414,0x0415,0x0416,0x0417,0x0418,0x0419,0x041A,0x041B,0x041C,0x041D,0x041E,0x041F,0x0420,0x0421,0x0422,0x0423,0x0424,0x0425,0x0426,0x0427,0x0428,0x0429,0x042A,0x042B,0x042C,0x042D,0x042E,0x042F,0x0430,0x0431,0x0432,0x0433,0x0434,0x0435,0x0436,0x0437,0x0438,0x0439,0x043A,0x043B,0x043C,0x043D,0x043E,0x043F,0x0440,0x0441,0x0442,0x0443,0x0444,0x0445,0x0446,0x0447,0x0448,0x0449,0x044A,0x044B,0x044C,0x044D,0x044E,0x044F);

$recode[i]=array(0x0080,0x0081,0x0082,0x0083,0x0084,0x0085,0x0086,0x0087,0x0088,0x0089,0x008A,0x008B,0x008C,0x008D,0x008E,0x008F,0x0090,0x0091,0x0092,0x0093,0x0094,0x0095,0x0096,0x0097,0x0098,0x0099,0x009A,0x009B,0x009C,0x009D,0x009E,0x009F,0x00A0,0x0401,0x0402,0x0403,0x0404,0x0405,0x0406,0x0407,0x0408,0x0409,0x040A,0x040B,0x040C,0x00AD,0x040E,0x040F,0x0410,0x0411,0x0412,0x0413,0x0414,0x0415,0x0416,0x0417,0x0418,0x0419,0x041A,0x041B,0x041C,0x041D,0x041E,0x041F,0x0420,0x0421,0x0422,0x0423,0x0424,0x0425,0x0426,0x0427,0x0428,0x0429,0x042A,0x042B,0x042C,0x042D,0x042E,0x042F,0x0430,0x0431,0x0432,0x0433,0x0434,0x0435,0x0436,0x0437,0x0438,0x0439,0x043A,0x043B,0x043C,0x043D,0x043E,0x043F,0x0440,0x0441,0x0442,0x0443,0x0444,0x0445,0x0446,0x0447,0x0448,0x0449,0x044A,0x044B,0x044C,0x044D,0x044E,0x044F,0x2116,0x0451,0x0452,0x0453,0x0454,0x0455,0x0456,0x0457,0x0458,0x0459,0x045A,0x045B,0x045C,0x00A7,0x045E,0x045F);

$recode[a]=array(0x0410,0x0411,0x0412,0x0413,0x0414,0x0415,0x0416,0x0417,0x0418,0x0419,0x041a,0x041b,0x041c,0x041d,0x041e,0x041f,0x0420,0x0421,0x0422,0x0423,0x0424,0x0425,0x0426,0x0427,0x0428,0x0429,0x042a,0x042b,0x042c,0x042d,0x042e,0x042f,0x0430,0x0431,0x0432,0x0433,0x0434,0x0435,0x0436,0x0437,0x0438,0x0439,0x043a,0x043b,0x043c,0x043d,0x043e,0x043f,0x2591,0x2592,0x2593,0x2502,0x2524,0x2561,0x2562,0x2556,0x2555,0x2563,0x2551,0x2557,0x255d,0x255c,0x255b,0x2510,0x2514,0x2534,0x252c,0x251c,0x2500,0x253c,0x255e,0x255f,0x255a,0x2554,0x2569,0x2566,0x2560,0x2550,0x256c,0x2567,0x2568,0x2564,0x2565,0x2559,0x2558,0x2552,0x2553,0x256b,0x256a,0x2518,0x250c,0x2588,0x2584,0x258c,0x2590,0x2580,0x0440,0x0441,0x0442,0x0443,0x0444,0x0445,0x0446,0x0447,0x0448,0x0449,0x044a,0x044b,0x044c,0x044d,0x044e,0x044f,0x0401,0x0451,0x0404,0x0454,0x0407,0x0457,0x040e,0x045e,0x00b0,0x2219,0x00b7,0x221a,0x2116,0x00a4,0x25a0,0x00a0);

$recode[d]=$recode[a];

$recode[m]=array(0x0410,0x0411,0x0412,0x0413,0x0414,0x0415,0x0416,0x0417,0x0418,0x0419,0x041A,0x041B,0x041C,0x041D,0x041E,0x041F,0x0420,0x0421,0x0422,0x0423,0x0424,0x0425,0x0426,0x0427,0x0428,0x0429,0x042A,0x042B,0x042C,0x042D,0x042E,0x042F,0x2020,0x00B0,0x00A2,0x00A3,0x00A7,0x2022,0x00B6,0x0406,0x00AE,0x00A9,0x2122,0x0402,0x0452,0x2260,0x0403,0x0453,0x221E,0x00B1,0x2264,0x2265,0x0456,0x00B5,0x2202,0x0408,0x0404,0x0454,0x0407,0x0457,0x0409,0x0459,0x040A,0x045A,0x0458,0x0405,0x00AC,0x221A,0x0192,0x2248,0x2206,0x00AB,0x00BB,0x2026,0x00A0,0x040B,0x045B,0x040C,0x045C,0x0455,0x2013,0x2014,0x201C,0x201D,0x2018,0x2019,0x00F7,0x201E,0x040E,0x045E,0x040F,0x045F,0x2116,0x0401,0x0451,0x044F,0x0430,0x0431,0x0432,0x0433,0x0434,0x0435,0x0436,0x0437,0x0438,0x0439,0x043A,0x043B,0x043C,0x043D,0x043E,0x043F,0x0440,0x0441,0x0442,0x0443,0x0444,0x0445,0x0446,0x0447,0x0448,0x0449,0x044A,0x044B,0x044C,0x044D,0x044E,0x00A4);

$and=0x3F;
for ($i=0;$i<strlen($str);$i++) {
$letter=0x0;
$octet=array();
$octet[0]=ord($str[$i]);
$octets=1;
$andfirst=0x7F;

if (($octet[0]>>1)==0x7E) {
$octets=6;
$andfirst=0x1;
} elseif (($octet[0]>>2)==0x3E) {
$octets=5;
$andfirst=0x3;
} elseif (($octet[0]>>3)==0x1E) {
$octets=4;
$andfirst=0x7;
} elseif (($octet[0]>>4)==0xE) {
$octets=3;
$andfirst=0xF;
} elseif (($octet[0]>>5)==0x6) {
$octets=2;
$andfirst=0x1F;
}

$octet[0]=$octet[0] & $andfirst;
$octet[0]=$octet[0] << ($octets-1)*6;
$letter+=$octet[0];

for ($j=1;$j<$octets;$j++) {
$i++;
$octet[$j]=ord($str[$i]) & $and;
$octet[$j]=$octet[$j] << ($octets-1-$j)*6;
$letter+=$octet[$j];
}

if ($letter<0x80) {
$outstr.=chr($letter);
} else {
if (in_array($letter,$recode[$to])) {
$outstr.=chr(array_search($letter,$recode[$to])+128);
}
}
}

return($outstr);
}



function searchfield($s) {
	return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}

function genrelist() {
	$ret = array();
//$res = sql_query("SELECT id, name FROM categories ORDER BY sort ASC");
//while ($row = mysql_fetch_array($res))

///////// cache
$cache2=new MySQLCache("SELECT id, name,(SELECT COUNT(*) FROM torrents WHERE categories.id=torrents.category) AS num_torrent FROM categories ORDER BY sort ASC", 86400,"browse_genrelist.txt"); // день
while ($row=$cache2->fetch_assoc())
///////// cache

$ret[] = $row;
return $ret;
}

function taggenrelist($cat) {
	$ret = array();
	$res = sql_query("SELECT id, name FROM tags WHERE category=$cat ORDER BY name ASC") or sqlerr(__FILE__, __LINE__); 
	while ($row = mysql_fetch_array($res))
		$ret[] = $row;
	return $ret;
}

function tag_info() {
//$result = sql_query("SELECT DISTINCT name, howmuch FROM tags WHERE howmuch > 0 ORDER BY id DESC");
//while($row = mysql_fetch_assoc($result)) {


///////// cache
$cache=new MySQLCache("SELECT DISTINCT name, howmuch FROM tags WHERE howmuch > 0 and name IS NOT NULL ORDER BY howmuch DESC LIMIT 80", 2*3600,"block_cloudstags.txt"); // 2 часа
while ($row=$cache->fetch_assoc()){
///////// cache

$arr[$row['name']] = $row['howmuch'];
}

@ksort($arr);
return $arr;
}

function cloud() {
//min / max font sizes
$small = 10;
$big = 32;
//get tag info from worker function
$tags = tag_info();
//amounts
$minimum_count = @min(array_values($tags));
$maximum_count = @max(array_values($tags));
$spread = $maximum_count - $minimum_count;

if($spread == 0) {$spread = 1;}

$cloud_html = '';

$cloud_tags = array();

foreach ($tags as $tag => $count) {

$size = $small + ($count - $minimum_count) * ($big - $small) / $spread;
//set up colour array for font colours.
$colour_array = array('#003EFF', '#0000FF', '#7EB6FF', '#0099CC', '62B1F6');
//spew out some html malarky!
$cloud_tags[] = '<a style="font-weight:normal; color:'.$colour_array[mt_rand(0, 5)].'; font-size: '. floor($size) . 'px'
. '" class="tag_cloud" href="browse.php?tag=' . urlencode($tag) . '&cat=0&incldead=1'
. '" title="Тэг \''.htmlentities($tag ,ENT_QUOTES, "cp1251").'\' отмечен в ' . $count . ' торрентах">'
. htmlentities($tag,ENT_QUOTES, "cp1251") . '</a>('.$count.')';
}

$cloud_html = join("\n", $cloud_tags) . "\n";
return $cloud_html;
}

function linkcolor($num) {
	if (empty($num))
		return "red";
//	if ($num == 1)
//		return "yellow";
	return "green";
}

function writecomment($userid, $comment) {
	$res = sql_query("SELECT modcomment FROM users WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_assoc($res);

	$modcomment = date("d-m-Y") . " - " . $comment . "" . ($arr["modcomment"] != "" ? "\n" : "") . "$arr[modcomment]";
	$modcom = sqlesc($modcomment);

	return sql_query("UPDATE users SET modcomment = $modcom WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
}

function checknewnorrent($id, $added)  {
    global $CURUSER;

      $coo_new = (isset($_COOKIE["markview"])?$_COOKIE["markview"]:"");

      $diff_add = preg_replace("/[^0-9]/", "", $added);
      $diff_lch = preg_replace("/[^0-9]/", "", $CURUSER["last_checked"]);
      
      if($diff_add > $diff_lch){
       
	   if($coo_new){
        if(explode("-", $coo_new)){
           if(!in_array($id, explode("-", $coo_new)))
           $new_t = true;
           
        }  else  {
           if($coo_new != $id)
           $new_t = true;
        }
        }
         else
        $new_t = true;
      }
      else
     $new_t = false;

     return $new_t;
}


function pic_rating_b($all=10,$first=false) {
$end=$all-$first;
$prating="";

for ($x1=0; $x1<$all; $x1++){
	
if ($x1<$first && $all<=10)
$prating.="<img src=\"pic/ratio/star_on.gif\" width=\"20\" height=\"20\" border=\"0\"/>";
elseif ($x1>$first && $all<=10)
$prating.="<img src=\"pic/ratio/star_off.gif\" width=\"20\" height=\"20\" border=\"0\"/>"; 
else
$prating.="<img src=\"pic/ratio/star_off.gif\" width=\"20\" height=\"20\" border=\"0\"/>"; 
}

return $prating;
}


function torrenttable($res, $variant = "index") {

global $DEFAULTBASEURL, $CURUSER, $use_wait, $use_ttl, $ttl_days, $SITENAME, $tracker_lang, $announce_net;

$mark=0;
$cat=array();
$file=basename($_SERVER['SCRIPT_FILENAME']);


/*
$res_cat = sql_query("SELECT id, name, image FROM categories") or sqlerr(__FILE__, __LINE__);

while ($arr_cat = mysql_fetch_assoc($res_cat)){
$cat_sql[$arr_cat["id"]] = array("name"=>$arr_cat["name"], "image"=>$arr_cat["image"]);
}
*/


$res_cat=new MySQLCache("SELECT id, name, image FROM categories", 24*7200, "browse_cat_array.txt"); // 24 часа
while ($arr_cat=$res_cat->fetch_assoc()){
$cat_sql[$arr_cat["id"]] = array("name"=>$arr_cat["name"], "image"=>$arr_cat["image"]);
}


if ($CURUSER){

$checkin=array(); $book=array();

$res_t1 = sql_query("SELECT checkid FROM checkcomm WHERE userid = ".sqlesc($CURUSER["id"])." AND torrent='1' AND offer='0'") or sqlerr(__FILE__, __LINE__);

while ($arr_c1 = mysql_fetch_assoc($res_t1)){
$checkin[$arr_c1["checkid"]] = 1;
}
///print_r($checkin);

/// работает нижнее
$res_t2 = sql_query("SELECT torrentid FROM bookmarks WHERE userid = ".sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);

while ($arr_c2 = mysql_fetch_assoc($res_t2)){
$book[$arr_c2["torrentid"]] = 1;
}


}



?>
<style>.effect {FILTER: alpha(opacity=60); -moz-opacity: .60; opacity: .60;}</style>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript">
function getDetails(tid, bu, picid, formid) {

var det = document.getElementById('details_'+tid);
var pic = document.getElementById(picid);
var form = document.getElementById(formid);

if(!det.innerHTML) {
     var ajax = new tbdev_ajax();
     ajax.onShow ('');
     var varsString = "";
     ajax.requestFile = "<?=$DEFAULTBASEURL; ?>/gettorrentdetails.php";
     ajax.setVar("tid", tid);
     ajax.method = 'POST';
     ajax.element = 'details_'+tid;
     ajax.sendAJAX(varsString); 
     pic.src = bu + "/pic/minus.gif"; form.value = "minus"; 
	 } else  
	 det.innerHTML = '';
	 pic.src = bu + "/pic/plus.gif"; form.value = "plus"; 
	 }
</script>


<? if (!empty($CURUSER)){ ?>

<script type="text/javascript">
function bookmark(id, type, page) {
var loading = "";
var id = id; var type = type;
jQuery("#loading").html(loading);
$.post('<?=$DEFAULTBASEURL; ?>/bookmark.php',{'id':id , 'type':type , 'page':page},

function(response) {
$('#bookmark_'+id).html(response);
jQuery("#loading").empty();
}, 'html');
}

function checmark(id, type, page,twopage) {
var loading = "";
var id = id; var type = type;
jQuery("#loading").html(loading);
$.post('<?=$DEFAULTBASEURL; ?>/bookmark.php',{'id':id , 'type':type , 'page':page, 'twopage':twopage},

function(response) {
$('#checmark_'+id).html(response);
jQuery("#loading").empty();
}, 'html');
}
</script>
<? } ?>

<? if (get_user_class() == UC_SYSOP && $variant == "index") {

?>
     <script language="Javascript" type="text/javascript">
        <!-- Begin
        var checkflag = "false";
        var marked_row = new Array;
        function check(field) {
                if (checkflag == "false") {
                        for (i = 0; i < field.length; i++) {
                                field[i].checked = true;}
                                checkflag = "true";
                        }
                else {
                        for (i = 0; i < field.length; i++) {
              field[i].checked = false; }
                                checkflag = "false";
                        }
                }
  //  End -->
        </script>
<? } ?>

<div id="loading-layer" style="display:none;font-family: Verdana;font-size: 11px;width:200px;height:50px;background:#FFF;padding:10px;text-align:center;border:1px solid #000">
     <div style="font-weight:bold" id="loading-layer-text">Загрузка данных. Пожалуйста, подождите...</div><br />
     <img src="pic/loading.gif" border="0" />
</div>
<?

  if ($use_wait)
  if (($CURUSER["class"] < UC_VIP) && $CURUSER) {
		  $gigs = $CURUSER["uploaded"] / (1024*1024*1024);
		  $ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
		  if ($ratio < 0.5 || $gigs < 5) $wait = 48;
		  elseif ($ratio < 0.65 || $gigs < 6.5) $wait = 24;
		  elseif ($ratio < 0.8 || $gigs < 8) $wait = 12;
		  elseif ($ratio < 0.95 || $gigs < 9.5) $wait = 6;
		  else $wait = 0;
  }

print("<tr>\n");

$count_get = 0;

foreach ($_GET as $get_name => $get_value) {

$get_name = mysql_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_name)));

$get_value = mysql_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_value)));

if ($get_name != "sort" && $get_name != "type") {
if ($count_get > 0) {
$oldlink = $oldlink . "&" . $get_name . "=" . $get_value;
} else {
$oldlink = (isset($oldlink)?$oldlink:"") . $get_name . "=" . $get_value;
}
++$count_get;
}

}

if ($count_get > 0) {
$oldlink = $oldlink . "&";
}

if (isset($_GET['sort']) == "1") {
if ($_GET['type'] == "desc") {
$link1 = "asc";
} else {
$link1 = "desc";
}
}

if (isset($_GET['sort']) == "2") {
if ($_GET['type'] == "desc") {
$link2 = "asc";
} else {
$link2 = "desc";
}
}

if (isset($_GET['sort']) == "3") {
if ($_GET['type'] == "desc") {
$link3 = "asc";
} else {
$link3 = "desc";
}
}

if (isset($_GET['sort']) == "4") {
if ($_GET['type'] == "desc") {
$link4 = "asc";
} else {
$link4 = "desc";
}
}

if (isset($_GET['sort']) == "5") {
if ($_GET['type'] == "desc") {
$link5 = "asc";
} else {
$link5 = "desc";
}
}

if (isset($_GET['sort']) == "6") {
if ($_GET['type'] == "desc") {
$link6 = "asc";
} else {
$link6 = "desc";
}
}

if (isset($_GET['sort']) == "7") {
if ($_GET['type'] == "desc") {
$link7 = "asc";
} else {
$link7 = "desc";
}
}

if (isset($_GET['sort']) == "8") {
if ($_GET['type'] == "desc") {
$link8 = "asc";
} else {
$link8 = "desc";
}
}

if (isset($_GET['sort']) == "9") {
if ($_GET['type'] == "desc") {
$link9 = "asc";
} else {
$link9 = "desc";
}
}

if (isset($_GET['sort']) == "10") {
if ($_GET['type'] == "desc") {
$link10 = "asc";
} else {
$link10 = "desc";
}
}

if (isset($_GET['sort']) == "11") {
if ($_GET['type'] == "desc") {
$link11 = "asc";
} else {
$link11 = "desc";
}
}
if (isset($_GET['sort']) == "12") {
if ($_GET['type'] == "desc") {
$link12 = "asc";
} else {
$link12 = "desc";
}
}

if (!isset($link1)) { $link1 = "asc"; } // for torrent name
if (!isset($link2)) { $link2 = "desc"; }
if (!isset($link3)) { $link3 = "desc"; }
if (!isset($link4)) { $link4 = "desc"; }
if (!isset($link5)) { $link5 = "desc"; }
if (!isset($link6)) { $link6 = "desc"; } // по типу (категории)
if (!isset($link7)) { $link7 = "desc"; }
if (!isset($link8)) { $link8 = "desc"; }
if (!isset($link9)) { $link9 = "desc"; }
if (!isset($link10)) { $link10 = "desc"; }
if (!isset($link11)) { $link11 = "desc"; } // по одобрению
if (!isset($link12)) { $link12 = "desc"; } // по одобрению

echo "<td class=\"colhead\" align=\"center\">
<a title=\"Сортировка по типу\" href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=6&type=".(isset($link6)?$link6:"")."\" class=\"altlink_white\">".$tracker_lang["type"]."</a></td>";

echo "<td class=\"colhead\" align=\"left\">
<a title=\"Сортировка по названию\" href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=1&type=".$link1."\" class=\"altlink_white\">".$tracker_lang["name"]."</a>
/ 
<a title=\"Сортировка по времени добавления\" href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=4&type=".$link4."\" class=\"altlink_white\">".$tracker_lang['added']."</a>
/
<a title=\"Сортировка по времени одобрения\" href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=11&type=".$link11."\" class=\"altlink_white\">Проверен</a>
/
<a title=\"Сортировка по времени обновления внешних пиров мультитрекера\" href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=12&type=".$link11."\" class=\"altlink_white\">Мультитрекер</a>
</td>";

if (isset($wait))
	print("<td class=\"colhead\" align=\"center\">".$tracker_lang['wait']."</td>\n");

if ($variant == "mytorrents")
	print("<td class=\"colhead\" align=\"center\">".$tracker_lang['visible']."</td>\n");


echo "<td class=\"colhead\" align=\"center\">
<a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=2&type=".(isset($link2)?$link2:"")."\" class=\"altlink_white\"><img title=\"Сортировка по количеству файлов\" src=\"pic/browse/nimberfiles.gif\" border=\"0\">
</a>
</td>";

echo "<td class=\"colhead\" align=\"center\">
<a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=3&type=".(isset($link3)?$link3:"")."\" class=\"altlink_white\">
<img title=\"Сортировка по количеству комментов\" src=\"pic/browse/comments.gif\" border=\"0\">
</a></td>";

if ($use_ttl)
echo "<td class=\"colhead\" align=\"center\">".$tracker_lang['ttl']."</td>";


echo "<td class=\"colhead\" align=\"center\">
<a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=5&type=".(isset($link5)?$link5:"")."\">
<img title=\"Сортировка по размеру\" src=\"pic/browse/size_file.gif\" border=\"0\">
</a></td>";

echo "<td class=\"colhead\" align=\"center\">
<a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=7&type=".(isset($link7)?$link7:"")."\">
<img title=\"Сортировка по количесту пиров\" src=\"pic/browse/up.gif\" border=\"0\">
</a> <a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=8&type=".(isset($link8)?$link8:"")."\"><img title=\"Сортировка по количесту сидов\" src=\"pic/browse/down.gif\" border=\"0\"></a>
</td>";


if ($variant == "index" || $variant == "bookmarks")
echo("<td class=\"colhead\" align=\"center\"><a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=9&type={$link9}\" class=\"altlink_white\">".$tracker_lang['uploadeder']."</a></td>\n");

	
if ($variant == "index") 
echo("<td class=\"colhead\" align=\"center\">
<a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=10&type=".(isset($link10)?$link10:"")."\" class=\"altlink_white\">Проверен</a>
</td>");

if (get_user_class() == UC_SYSOP && $variant == "index")
echo("<form method=\"post\" name=\"yepi\" action=\"b_actions.php\">");

if (get_user_class() == UC_SYSOP && $variant == "index")
echo("<td class=\"colhead\" align=\"center\"><input type=\"checkbox\" title=\"Выделить все\" value=\"Выделить все>\" onclick=\"this.value=check(document.yepi.elements);\"></td>\n");

if ($variant == "bookmarks")
echo("<td class=\"colhead\" align=\"center\">".$tracker_lang['delete']."</td>\n");

echo("</tr>\n");

//echo("<tbody id=\"highlighted\">");


if ($variant == "bookmarks")
echo("<form method=\"post\" action=\"takedelbookmark.php\">");

$numcat = array();
$numid = array();
while ($row = mysql_fetch_assoc($res)) {

$id = $row["id"];
$numid[]=$row["id"];
$numcat[]=$row["category"];
$sizeall+=$row["size"];

$sizegoldtor+=($row["free"]=="yes" ? $row["size"]:0);
$numpeers +=($row["seeders"]+$row["leechers"]);

echo("<tr".((isset($row["sticky"]) && $row["sticky"] == "yes") ? " class=\"highlight\"" : "").">\n");


// выделение релизов по дням
$day_added = $row['added'];
$day_show = strtotime($day_added);
$thisdate = date('Y-m-d',$day_show);

///$numtorrents = number_format(get_row_count("torrents", "WHERE added LIKE '%".$thisdate." %'"));

if ($thisdate==(isset($prevdate)?$prevdate:"")){
$cleandate = '';
} else {
$day_added = ''.date('Торренты, добавленные вl, j M Y года', strtotime($row['added'])); // You can change this to something else
$cleandate = "<tr><td colspan=15 class=colhead><b>".$day_added."</b></td></tr>\n"; // This also...
}

$prevdate = $thisdate;

$man = array('Jan' => 'Января','Feb' => 'Февраля','Mar' => 'Марта','Apr' => 'Апреля','May' => 'Мая','Jun' => 'Июня','Jul' => 'Июля','Aug' => 'Августа','Sep' => 'Сентября','Oct' => 'Октября','Nov' => 'Ноября','Dec' => 'Декабря');

foreach($man as $eng => $ger){
  $cleandate = str_replace($eng, $ger,$cleandate);
}

$dag = array('Mon' => ' Понедельник','Tues' => 'о Вторник','Wednes' => ' Среду','Thurs' => ' Четверг','Fri' => ' Пятницу','Satur' => ' Субботу','Sun' => ' Воскресенье');

foreach($dag as $eng => $ger){
    $cleandate = str_replace($eng.'day', $ger.'',$cleandate);
}

if (isset($row["sticky"]) && $row["sticky"] == "no") // delete this line if you dont have sticky torrents or you want to display the addate for them also
if(!isset($_GET['sort']) && !isset($_GET['d'])){
   echo $cleandate."\n";
}
// выделение релизов по дням  



	//	print("<td align=\"center\" style=\"padding: 0px\">");
		
echo("<tr>");
echo("<td align=\"center\" ".($_GET["sort"]=="6" ? "class=\"a\"":"class=\"b\"")." rowspan=2 width=2% style=\"padding: 5px\">");  

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
		echo("</td>\n");



$dispname = htmlspecialchars_uni($row['name']);


echo ("<td colspan=\"12\" class=\"b\" align=\"left\"><div style=\"cursor:pointer;\" align=\"left\" onDblClick=\"getDetails('$id','$DEFAULTBASEURL','warnpic$id','$id')\" alt=\"Предпросмотр описания\" title=\"Предпросмотр описания\">".((isset($row["sticky"]) && $row["sticky"] == "yes") ? "<b>Важный</b>: " : "")." <a style=\"font-weight:bolder;\" href=\"details.php?id=".$id."\">".$dispname."</a>\n");

if (checknewnorrent($row["id"], $row["added"]) && $CURUSER) {
echo "<b><font color=\"red\" size=\"1\">[новый]</font></b> ";
++$mark;
}
 
if ($row["free"]=="yes")
echo "<img src=\"".$DEFAULTBASEURL."/pic/freedownload.gif\" title=\"".$tracker_lang['golden']."\" alt=\"".$tracker_lang['golden']."\">";	

if (!empty($row["viponly"]))
echo "<img  border=\"0\" width=\"18px\" alt=\"Данная раздача только для VIP пользователей\" title=\"Данная раздача только для VIP пользователей\" src=\"".$DEFAULTBASEURL."/pic/vipbig.gif\">";


echo("</div></td></tr><tr>");

echo("<td width=\"50%\" ".($_GET["sort"]=="4" || $_GET["sort"]=="1" || $_GET["sort"]=="11" || $_GET["sort"]=="12" ? "class=\"b\"":"class=\"a\"")." align=\"left\">");  


echo "<table cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">";

echo "<tr><td colspan=\"2\" ".($_GET["sort"]=="4" || $_GET["sort"]=="1" || $_GET["sort"]=="11" || $_GET["sort"]=="12" ? "class=\"b\"":"class=\"a\"").">";

if ($variant == "bookmarks" && $CURUSER){

if (empty($row["mytags"]))
$viewtags=" Пусто";

if (!empty($row["mytags"])){
$viewtags=htmlspecialchars_uni(strip_tags($row["mytags"]));
}

print("<b title=\"Ваши заметки по этому торренту\">Заметки</b>: ".$viewtags."<br>");
unset($viewtags);
}

if (!empty($row["tags"])) {
$tags[$row["id"]]="";
foreach(explode(",", $row["tags"]) as $tag) {

$numtags[] = $tag;

if (!empty($tags[$row["id"]]))
$tags[$row["id"]].=", ";

$tags[$row["id"]].= "<a style=\"font-weight:normal;\" href=\"browse.php?tag=".urlencode($tag)."&incldead=1\">".tolower($tag)."</a>";
}
$tags[$row["id"]]=$tags[$row["id"]];
}
else
$tags[$row["id"]]="не выбраны";

if (!empty($tags[$row["id"]]))
echo("<b>Теги</b>: ".$tags[$row["id"]]." ".((!empty($tags[$row["id"]]) && strlen($tags[$row["id"]])>200) ? "&nbsp; ":"")."");//<br>


if (isset($row["banned"]) && $row["banned"] == "yes"){
$banned_view=(isset($row["banned_reason"])? htmlspecialchars($row["banned_reason"]):"");
echo "<br>";
echo get_user_class() < UC_MODERATOR ? "Забанен.": "<b>Забанен по причине</b>: ".(empty($banned_view) ? "не указанна" : $banned_view);
}

echo "</td>";

echo "<td align=\"center\" width=\"15%\">";
//".($_GET["sort"]=="4" || $_GET["sort"]=="1" || $_GET["sort"]=="11" || $_GET["sort"]=="12" ? "class=\"b\"":"class=\"a\"")."

if ($variant <> "bookmarks" && $CURUSER){

if (empty($checkin[$id]))
echo "<span style=\"cursor: pointer;\" id=\"checmark_".$row['id']."\"><a onclick=\"checmark('$id', 'add' , 'check', 'browse');\"><img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/head2_2.gif\" alt=\"Включить слежение\" title=\"Добавить в слежения\" /></a> <span id=\"loading\"></span></span> ";
else
echo "<span style=\"cursor: pointer;\" id=\"checmark_".$row['id']."\"><a onclick=\"checmark('$id', 'del' , 'check', 'browse');\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" border=\"0\" src=\"".$DEFAULTBASEURL."/pic/head2_2.gif\" alt=\"Отключить слежение\" title=\"Убрать с закладок\" /></a>  <span id=\"loading\"></span></span> ";
 

if(empty($book[$id]))
echo " <span style=\"cursor: pointer;\" id=\"bookmark_".$row['id']."\"><a onclick=\"bookmark('$id', 'add' , 'browse');\"><img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/add.gif\" alt=\"Добавить в закладки\" title=\"Добавить в закладки\" /></a>   <span id=\"loading\"></span></span> ";
else
echo " <span style=\"cursor: pointer;\" id=\"bookmark_".$row['id']."\"><a onclick=\"bookmark('$id', 'del' , 'browse');\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" border=\"0\" src=\"".$DEFAULTBASEURL."/pic/minus.gif\" alt=\"Убрать с закладок\" title=\"Убрать с закладок\" /></a>  <span id=\"loading\"></span></span> ";

}


if ((isset($row["owner"]) && $CURUSER["id"] == $row["owner"]) || get_user_class() >= UC_MODERATOR)
$owned = 1;
else
$owned = 0;

if ($owned)
echo("<a href=\"edit.php?id=$row[id]\"><img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/pen.gif\" alt=\"".$tracker_lang['edit']."\" title=\"".$tracker_lang['edit']."\"/></a>\n");

echo "<br>";

if ($CURUSER || (!empty($announce_net) && $row["multitracker"]=="yes" && !$CURUSER))
echo "<a href=\"download.php?id=$id\" alt=\"".$tracker_lang['download']." ".$dispname."\" title=\"".$tracker_lang['download']." ".$dispname."\"><b>скачать</b></a>";
else
echo "<a href=\"download.php?id=$id\" alt=\"".$tracker_lang['download']." ".$dispname."\" title=\"".$tracker_lang['download']." ".$dispname."\"><s><b>скачать</b></s></a>";

echo "</td></tr>";

echo "</table>";


//else 
//{

//print("<b>Добавлен</b>: ".timesec($row["added"])."");

/*
if ($variant <> "mytorrents" && $variant <> "bookmarks" && $CURUSER ) {
echo "<br>";
echo pic_rating_b(10,$row["rating"]);
}
*/

//}



	print("</td>\n");

		if ($variant == "mytorrents") {
			print("<td class=\"row2\" align=\"center\">");
			if ($row["visible"] == "no")
				print("<font color=\"red\"><b>".$tracker_lang['no']."</b></font>");
			else
				print("<font color=\"green\">".$tracker_lang['yes']."</font>");
			print("</td>\n");
		}

		if (isset($row["type"]) == "single")
		echo("<td class=\"row2\" align=\"center\">".$row["numfiles"]."</td>\n");
		else {
		echo("<td ".($_GET["sort"]=="2"? "class=\"b\"":"class=\"row2\"")." align=\"center\"><b>".$row["numfiles"]."</b></td>\n");
		}

	
		echo("<td ".($_GET["sort"]=="3"? "class=\"b\"":"class=\"row2\"")." align=\"center\"><b><a href=\"details.php?id=$id&page=last#startcomments\">" . $row["comments"] . "</a></b></td>\n");


//		print("<td align=center><nobr>" . str_replace(" ", "<br />", $row["added"]) . "</nobr></td>\n");
				$ttl = ($ttl_days*24) - floor((gmtime() - sql_timestamp_to_unix_timestamp($row["added"])) / 3600);
				if ($ttl == 1) $ttl .= " час"; else $ttl .= "&nbsp;часов";
		if ($use_ttl)
			print("<td class=\"row2\" align=\"center\">$ttl</td>\n");
	//	print("<td align=\"center\">" . str_replace(" ", "<br />", mksize($row["size"])) . "</td>\n");

        echo ("<td ".($_GET["sort"]=="5"? "class=\"b\"":"class=\"row2\"")." align=\"center\">".mksize($row["size"])."</td>\n");



		echo ("<td ".($_GET["sort"]=="7" || $_GET["sort"]=="8"? "class=\"b\"":"class=\"row2\"")." align=\"center\">");       
		echo ("<b><font color=\"".linkcolor($row["seeders"])."\">".($row["seeders"])."</font></b>\n");
		echo (" | ");
		 echo ("<b><font color=\"".linkcolor(number_format($row["leechers"])). "\">" .number_format($row["leechers"]). "</font></b>\n");
		echo ("</td>");
       
       $numseed+=$row["seeders"];
       $numleech+=$row["leechers"];
       
		if ($variant == "index" || $variant == "bookmarks")
			print("<td ".($_GET["sort"]=="9"? "class=\"b\"":"class=\"row2\"")." align=\"center\">" . (isset($row["username"]) ? ("<a href=\"userdetails.php?id=".$row["owner"] . "\"><b>".get_user_class_color($row["class"], $row["username"]) . "</b></a>") : "<i>без автора</i>")."</td>\n");

		if ($variant == "bookmarks")
			print ("<td class=\"row2\" align=\"center\"><input type=\"checkbox\" name=\"delbookmark[]\" value=\"" . $row["bookmarkid"] . "\" /></td>");



		if ((get_user_class() > UC_MODERATOR) && isset($row["moderated"]) && $row["moderated"] == "yes")(
		$sysop = "<br>[<b><a href=checkdelete.php?id=$id>Удалить</a></b>]");
		
		if ((get_user_class() >= UC_MODERATOR) && $variant == "index") {
		
		    if ($row["moderated"] == "no") {

            print("<td ".($_GET["sort"]=="10"? "class=\"b\"":"class=\"row2\"")." align=\"center\">
			<a class=\"hover\" href=check.php?id=$id><b>Одобрить</b></a>
			</td>\n");
			}

            else

            print("<td ".($_GET["sort"]=="10"? "class=\"b\"":"class=\"row2\"")." align=\"center\"><b>
			".($row["classusername"] ? "<a href=\"".$DEFAULTBASEURL."/userdetails.php?id=$row[moderatedby]\">".get_user_class_color($row["classname"], $row["classusername"])."</a>" : "id [".$row["moderatedby"]."]")."
           </b>".$sysop."</td>\n");
				}

	if ((get_user_class() <= UC_UPLOADER) && $variant == "index"){
		
		    if ($row["moderated"] == "no")
            print("<td ".($_GET["sort"]=="10"? "class=\"b\"":"class=\"row2\"")." align=\"center\">
			<b>нет</b></td>\n");
            else
            print("<td ".($_GET["sort"]=="10"? "class=\"b\"":"class=\"row2\"")." align=\"center\"><b>
     	".($row["classusername"] ? "<a href=\"".$DEFAULTBASEURL."/userdetails.php?id=$row[moderatedby]\">".get_user_class_color($row["classname"], $row["classusername"])."</a>" : "id [$row[moderatedby]]")."
				</b></td>\n");
       }
		
		if ((get_user_class() == UC_SYSOP) && $variant == "index")
			print("<td class=\"row2\" align=\"center\"><input type=\"checkbox\" id=\"checkbox_tbl_" . $id . "\" name=\"cheKer[]\" value=\"" . $id . "\" /></td>\n");


print("</tr><tr><td colspan=\"10\" class=\"a\" style=\"margin: 0px; padding: 0px;\"><span id=\"details_" .$id. "\"></span></td>");  

print("</tr>\n");
$oldday = isset($day); // старая дата
}
//print("</tbody>");  
	
	
/*
$sizeall+=$row["size"];
$sizegolds+=$row["size"];
$sizegoldtor+=($row["free"]=="yes" ? $row["size"]:0);
$numpeers +=($row["seeders"]+$row["leechers"]);
*/
$numcat = @array_unique($numcat);
$numtags = @array_unique($numtags);


if ($CURUSER) {

if (count($numid)){

$ncurnum = 0;
$sql = sql_query("SELECT uploaded, downloaded FROM peers WHERE userid=".sqlesc($CURUSER["id"])." AND torrent IN (".implode(",",$numid).")") or sqlerr(__FILE__,__LINE__);

while ($ressql = mysql_fetch_assoc($sql)){
$ncurup+=$ressql["uploaded"];
$ncurdo+=$ressql["downloaded"];
++$ncurnum;
}

$snurnum = 0;
$sql2 = sql_query("SELECT uploaded, downloaded FROM snatched WHERE userid=".sqlesc($CURUSER["id"])." AND torrent IN (".implode(",",$numid).")") or sqlerr(__FILE__,__LINE__);

while ($ressql2 = mysql_fetch_assoc($sql2)){
$sncurup+=$ressql2["uploaded"];
$snurdo+=$ressql2["downloaded"];
++$snurnum;
}


}


//$numid

print("<tr><td class=\"a\" colspan=\"12\" align=\"center\">
<table width=\"100%\">
<tr>
<td class=\"b\" width=\"50%\" colspan=\"2\" align=\"center\">Данная статистика основна на таблице: Скачал и Раздал, Сейчас раздаю. <br>Выборка введется за счет <b>показанных на данной странице</b> релизов, другие же не показываются!</td>
</tr>
<tr>
<td class=\"b\" width=\"50%\" align=\"center\">
<b>Сидирование раздач</b> (Раздали / Скачали): ".number_format($ncurnum)." (".mksize($ncurup)." / ".mksize($ncurdo).")<br>
<b>Скачанные раздачи</b> (Раздали / Скачали): ".number_format($snurnum)." (".mksize($sncurup)." / ".mksize($snurdo).")<br>
Информация в статистику будет дополнятся по мере тестирования предыдущих.<br>
</td>

<td class=\"b\" width=\"50%\" align=\"center\">
<b>Размер раздач</b> (Обычных / Золотых): ".mksize($sizeall)." (".mksize($sizeall-$sizegoldtor)." / ".mksize($sizegoldtor).")<br>
<b>Количество Нодов</b> (Сидов / Пиров): ".($numseed+$numleech)." (".$numseed." / ".$numleech.")<br>
<b>Количество уникальных меток</b>: ".count($numtags)."<br>
<b>Количество уникальных категорий</b>: ".count($numcat)."<br>
</td>
</tr>
</table>
</td></tr>");
}
		
	if ($variant == "index" && $CURUSER && !empty($mark))
		print("<tr><td class=\"a\" colspan=\"12\" align=\"center\"><a href=\"markread.php\"><b>пометить все торренты прочитаными</b></a></td></tr>");

	//print("</table>\n");

	if ($variant == "index" && get_user_class() == UC_SYSOP) {
	
echo "<script language=\"JavaScript\" type=\"text/javascript\">
<!--
function changeSubmenu(form, si) {
while(form.pcat.length > 0) {
form.pcat.remove(form.pcat.length - 1);
}
var e=document.createElement('option');
e.text=\"выберите категорию для перемещения\";
e.value='0';
try {
form.pcat.add(e,null);
} catch(ex) { 
form.pcat.add(e);
}";

echo "if(form.actions.options[form.actions.options.selectedIndex].value == 'movcat') { \n";

$cats = genrelist();
foreach ($cats as $row)
echo "var e=document.createElement('option');
e.text='".htmlspecialchars($row["name"])."';
e.value='".$row["id"]."';
try {
form.pcat.add(e,null);
} catch(ex) {
form.pcat.add(e);
}\n";

echo "}else {
e.text=\"только для выбора категории\";
e.value='';
}
for(i = 0; i < form.pcat.length; i++) {
if (form.pcat.options[i].value == si) {
form.pcat.options.selectedIndex = i;
}
}
}
//--></script>";
	
	
print("<tr><td align=\"right\" colspan=\"12\">
<select name=\"actions\" onchange=\"changeSubmenu(this.form, 0)\">\n
<option selected>выбрать действие</option>
<option value=\"gold\">поставить золото</option>
<option value=\"ungold\">снять золото</option>
<option value=\"check\">поставить одобрение</option>
<option value=\"uncheck\">снять одобрение</option>
<option value=\"main\">поставить важность</option>
<option value=\"unmain\">снять важность</option>
<option value=\"newdate\">поставить сегодняшнюю дату</option>
<option value=\"anonim\">убрать автора, aka анонимность</option>
<option value=\"pravo\">удалить и пометить авторством</option>
<option value=\"movcat\">переместить в категорию</option>
<option value=\"delete\">удалить</option>
<option value=\"multi\">сбросить время обновления пиров</option>
</select>\n
<select name=\"pcat\">\n
<option value=\"0\">только для выбора категории</option>
</select>\n

<input type=\"hidden\" name=\"referer\" value=\"".ADDREFLINK."\">
<input type=\"submit\" class=\"btn\" onClick=\"return confirm('Вы уверены, что хотите выполнить данное действие?')\" value=\"Выполнить\">\n
</td></tr>\n");
}

	if ($variant == "bookmarks")
		print("<tr><td colspan=\"12\" align=\"right\"><input type=\"submit\" value=\"".$tracker_lang['delete']."\"></td></tr>\n");

	if ($variant == "index" || $variant == "bookmarks") {
		if (get_user_class() == UC_SYSOP) {
			print("</form>\n");
		}
	}

	return $rows;
}

function torrenttable_old($res, $variant = "index") {

global $DEFAULTBASEURL, $CURUSER, $use_wait, $use_ttl, $ttl_days, $SITENAME, $tracker_lang, $announce_net;

$mark=0;
$cat=array();
$file=basename($_SERVER['SCRIPT_FILENAME']);


$res_cat = sql_query("SELECT id, name, image FROM categories") or sqlerr(__FILE__, __LINE__);

while ($arr_cat = mysql_fetch_assoc($res_cat)){
$cat_sql[$arr_cat["id"]] = array("name"=>$arr_cat["name"], "image"=>$arr_cat["image"]);
}

/*
$res_cat=new MySQLCache("SELECT id, name, image FROM categories", 24*7200, "browse_cat_array.txt"); // 24 часа
while ($arr_cat=$res_cat->fetch_assoc()){
$cat_sql[$arr_cat["id"]] = array("name"=>$arr_cat["name"], "image"=>$arr_cat["image"]);
}
*/

if ($CURUSER){

$checkin=array(); $book=array();

$res_t1 = sql_query("SELECT checkid FROM checkcomm WHERE userid = ".sqlesc($CURUSER["id"])." AND torrent='1' AND offer='0'") or sqlerr(__FILE__, __LINE__);

while ($arr_c1 = mysql_fetch_assoc($res_t1)){
$checkin[$arr_c1["checkid"]] = 1;
}
///print_r($checkin);

/// работает нижнее
$res_t2 = sql_query("SELECT torrentid FROM bookmarks WHERE userid = ".sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);

while ($arr_c2 = mysql_fetch_assoc($res_t2)){
$book[$arr_c2["torrentid"]] = 1;
}


}

///	".($CURUSER["id"] ? " bs.id AS bookcomm, ch.checkid AS checkcomm,":"")."

/*
		".($CURUSER["id"] ? "LEFT JOIN bookmarks AS bs ON bs.userid = $CURUSER[id] and bs.torrentid = torrents.id 
		LEFT JOIN checkcomm AS ch ON ch.userid = $CURUSER[id] AND ch.checkid = torrents.id AND ch.torrent = 1
		":"")."
*/


?>
<style>.effect {FILTER: alpha(opacity=60); -moz-opacity: .60; opacity: .60;}
#block{}

.div1 {
width: 90%; float: right; 
background:#E1E1E1;
padding:3px;
-moz-border-radius-bottomleft:7px;
-moz-border-radius-bottomright:7px;
-moz-border-radius-topleft:25px;
-moz-border-radius-topright:7px;
text-decoration:none;
}

.div2 {
border:1px solid #d1d1d1;
background:#D2D2D2;
padding:3px;
-moz-border-radius-bottomleft:0px;
-moz-border-radius-bottomright:0px;
-moz-border-radius-topleft:7px;
-moz-border-radius-topright:7px;
text-decoration:none;
}

.div3 {
width: 20%; float: right; 
background:#D2D2D2;
padding:5px;
-moz-border-radius-bottomleft:7px;
-moz-border-radius-bottomright:7px;
-moz-border-radius-topleft:7px;
-moz-border-radius-topright:7px;
text-decoration:none;
}

.div4 {
height:100%;
margin:0px;
width: 20%; float: right; 
background:#D2D2D2;
padding:3px;
-moz-border-radius-bottomleft:70px;
-moz-border-radius-bottomright:7px;
-moz-border-radius-topleft:7px;
-moz-border-radius-topright:0px;
text-decoration:none;
}

.div5 {
border:none;
background:#D2D2D2;
padding:3px;
-moz-border-radius-bottomleft:0px;
-moz-border-radius-bottomright:0px;
-moz-border-radius-topleft:7px;
-moz-border-radius-topright:7px;
text-decoration:none;
}

.div6 {
width: 80%;  float: inherit; 
background:#D2D2D2;
padding:3px;
-moz-border-radius-bottomleft:7px;
-moz-border-radius-bottomright:0px;
-moz-border-radius-topleft:0px;
-moz-border-radius-topright:0px;
border:1px solid transparent;
text-decoration:none;
}

.div7 {
width: 90%; float: right; 
background:#E1E1E1;
padding:3px;
-moz-border-radius-bottomleft:25px;
-moz-border-radius-bottomright:0px;
-moz-border-radius-topleft:0px;
-moz-border-radius-topright:7px;
border:1px solid transparent;
text-decoration:none;
}
</style>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript">
function getDetails(tid, bu, picid, formid) {

var det = document.getElementById('details_'+tid);
var pic = document.getElementById(picid);
var form = document.getElementById(formid);

if(!det.innerHTML) {
     var ajax = new tbdev_ajax();
     ajax.onShow ('');
     var varsString = "";
     ajax.requestFile = "<?=$DEFAULTBASEURL; ?>/gettorrentdetails.php";
     ajax.setVar("tid", tid);
     ajax.method = 'POST';
     ajax.element = 'details_'+tid;
     ajax.sendAJAX(varsString); 
     pic.src = bu + "/pic/minus.gif"; form.value = "minus"; 
	 } else  
	 det.innerHTML = '';
	 pic.src = bu + "/pic/plus.gif"; form.value = "plus"; 
	 }
</script>


<? if (!empty($CURUSER)){ ?>

<script type="text/javascript">
function bookmark(id, type, page) {
var loading = "";
var id = id; var type = type;
jQuery("#loading").html(loading);
$.post('<?=$DEFAULTBASEURL; ?>/bookmark.php',{'id':id , 'type':type , 'page':page},

function(response) {
$('#bookmark_'+id).html(response);
jQuery("#loading").empty();
}, 'html');
}

function checmark(id, type, page,twopage) {
var loading = "";
var id = id; var type = type;
jQuery("#loading").html(loading);
$.post('<?=$DEFAULTBASEURL; ?>/bookmark.php',{'id':id , 'type':type , 'page':page, 'twopage':twopage},

function(response) {
$('#checmark_'+id).html(response);
jQuery("#loading").empty();
}, 'html');
}
</script>
<? } ?>

<? if (get_user_class() == UC_SYSOP && $variant == "index") {

?>
     <script language="Javascript" type="text/javascript">
        <!-- Begin
        var checkflag = "false";
        var marked_row = new Array;
        function check(field) {
                if (checkflag == "false") {
                        for (i = 0; i < field.length; i++) {
                                field[i].checked = true;}
                                checkflag = "true";
                        }
                else {
                        for (i = 0; i < field.length; i++) {
              field[i].checked = false; }
                                checkflag = "false";
                        }
                }
  //  End -->
        </script>
<? } ?>

<div id="loading-layer" style="display:none;font-family: Verdana;font-size: 11px;width:200px;height:50px;background:#FFF;padding:10px;text-align:center;border:1px solid #000">
     <div style="font-weight:bold" id="loading-layer-text">Загрузка данных. Пожалуйста, подождите...</div><br />
     <img src="pic/loading.gif" border="0" />
</div>
<?

  if ($use_wait)
  if (($CURUSER["class"] < UC_VIP) && $CURUSER) {
		  $gigs = $CURUSER["uploaded"] / (1024*1024*1024);
		  $ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
		  if ($ratio < 0.5 || $gigs < 5) $wait = 48;
		  elseif ($ratio < 0.65 || $gigs < 6.5) $wait = 24;
		  elseif ($ratio < 0.8 || $gigs < 8) $wait = 12;
		  elseif ($ratio < 0.95 || $gigs < 9.5) $wait = 6;
		  else $wait = 0;
  }

print("<tr>\n");

$count_get = 0;

foreach ($_GET as $get_name => $get_value) {

$get_name = mysql_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_name)));

$get_value = mysql_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_value)));

if ($get_name != "sort" && $get_name != "type") {
if ($count_get > 0) {
$oldlink = $oldlink . "&" . $get_name . "=" . $get_value;
} else {
$oldlink = (isset($oldlink)?$oldlink:"") . $get_name . "=" . $get_value;
}
++$count_get;
}

}

if ($count_get > 0) {
$oldlink = $oldlink . "&";
}

if (isset($_GET['sort']) == "1") {
if ($_GET['type'] == "desc") {
$link1 = "asc";
} else {
$link1 = "desc";
}
}

if (isset($_GET['sort']) == "2") {
if ($_GET['type'] == "desc") {
$link2 = "asc";
} else {
$link2 = "desc";
}
}

if (isset($_GET['sort']) == "3") {
if ($_GET['type'] == "desc") {
$link3 = "asc";
} else {
$link3 = "desc";
}
}

if (isset($_GET['sort']) == "4") {
if ($_GET['type'] == "desc") {
$link4 = "asc";
} else {
$link4 = "desc";
}
}

if (isset($_GET['sort']) == "5") {
if ($_GET['type'] == "desc") {
$link5 = "asc";
} else {
$link5 = "desc";
}
}

if (isset($_GET['sort']) == "6") {
if ($_GET['type'] == "desc") {
$link6 = "asc";
} else {
$link6 = "desc";
}
}

if (isset($_GET['sort']) == "7") {
if ($_GET['type'] == "desc") {
$link7 = "asc";
} else {
$link7 = "desc";
}
}

if (isset($_GET['sort']) == "8") {
if ($_GET['type'] == "desc") {
$link8 = "asc";
} else {
$link8 = "desc";
}
}

if (isset($_GET['sort']) == "9") {
if ($_GET['type'] == "desc") {
$link9 = "asc";
} else {
$link9 = "desc";
}
}

if (isset($_GET['sort']) == "10") {
if ($_GET['type'] == "desc") {
$link10 = "asc";
} else {
$link10 = "desc";
}
}

if (isset($_GET['sort']) == "11") {
if ($_GET['type'] == "desc") {
$link11 = "asc";
} else {
$link11 = "desc";
}
}
if (isset($_GET['sort']) == "12") {
if ($_GET['type'] == "desc") {
$link12 = "asc";
} else {
$link12 = "desc";
}
}

if (!isset($link1)) { $link1 = "asc"; } // for torrent name
if (!isset($link2)) { $link2 = "desc"; }
if (!isset($link3)) { $link3 = "desc"; }
if (!isset($link4)) { $link4 = "desc"; }
if (!isset($link5)) { $link5 = "desc"; }
if (!isset($link6)) { $link6 = "desc"; } // по типу (категории)
if (!isset($link7)) { $link7 = "desc"; }
if (!isset($link8)) { $link8 = "desc"; }
if (!isset($link9)) { $link9 = "desc"; }
if (!isset($link10)) { $link10 = "desc"; }
if (!isset($link11)) { $link11 = "desc"; } // по одобрению
if (!isset($link12)) { $link12 = "desc"; } // по одобрению

echo "<td class=\"colhead\" align=\"center\">
<a title=\"Сортировка по типу\" href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=6&type=".(isset($link6)?$link6:"")."\" class=\"altlink_white\">".$tracker_lang["type"]."</a></td>";

echo "<td class=\"colhead\" align=\"left\">
<a title=\"Сортировка по названию\" href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=1&type=".$link1."\" class=\"altlink_white\">".$tracker_lang["name"]."</a>
/ 
<a title=\"Сортировка по времени добавления\" href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=4&type=".$link4."\" class=\"altlink_white\">".$tracker_lang['added']."</a>
/
<a title=\"Сортировка по времени одобрения\" href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=11&type=".$link11."\" class=\"altlink_white\">Проверен</a>
/
<a title=\"Сортировка по времени обновления внешних пиров мультитрекера\" href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=12&type=".$link11."\" class=\"altlink_white\">Мультитрекер</a>
</td>";

if (isset($wait))
	print("<td class=\"colhead\" align=\"center\">".$tracker_lang['wait']."</td>\n");

if ($variant == "mytorrents")
	print("<td class=\"colhead\" align=\"center\">".$tracker_lang['visible']."</td>\n");


echo "<td class=\"colhead\" align=\"center\">
<a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=2&type=".(isset($link2)?$link2:"")."\" class=\"altlink_white\"><img title=\"Сортировка по количеству файлов\" src=\"pic/browse/nimberfiles.gif\" border=\"0\">
</a>
</td>";

echo "<td class=\"colhead\" align=\"center\">
<a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=3&type=".(isset($link3)?$link3:"")."\" class=\"altlink_white\">
<img title=\"Сортировка по количеству комментов\" src=\"pic/browse/comments.gif\" border=\"0\">
</a></td>";

if ($use_ttl)
echo "<td class=\"colhead\" align=\"center\">".$tracker_lang['ttl']."</td>";


echo "<td class=\"colhead\" align=\"center\">
<a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=5&type=".(isset($link5)?$link5:"")."\">
<img title=\"Сортировка по размеру\" src=\"pic/browse/size_file.gif\" border=\"0\">
</a></td>";

echo "<td class=\"colhead\" align=\"center\">
<a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=7&type=".(isset($link7)?$link7:"")."\">
<img title=\"Сортировка по количесту пиров\" src=\"pic/browse/up.gif\" border=\"0\">
</a> <a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=8&type=".(isset($link8)?$link8:"")."\"><img title=\"Сортировка по количесту сидов\" src=\"pic/browse/down.gif\" border=\"0\"></a>
</td>";


if ($variant == "index" || $variant == "bookmarks")
echo("<td class=\"colhead\" align=\"center\"><a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=9&type={$link9}\" class=\"altlink_white\">".$tracker_lang['uploadeder']."</a></td>\n");

	
if ($variant == "index") 
echo("<td class=\"colhead\" align=\"center\">
<a href=\"".$file."?".(isset($oldlink)?$oldlink:"")."sort=10&type=".(isset($link10)?$link10:"")."\" class=\"altlink_white\">Проверен</a>
</td>");

if (get_user_class() == UC_SYSOP && $variant == "index")
echo("<form method=\"post\" name=\"yepi\" action=\"b_actions.php\">");

if (get_user_class() == UC_SYSOP && $variant == "index")
echo("<td class=\"colhead\" align=\"center\"><input type=\"checkbox\" title=\"Выделить все\" value=\"Выделить все>\" onclick=\"this.value=check(document.yepi.elements);\"></td>\n");

if ($variant == "bookmarks")
echo("<td class=\"colhead\" align=\"center\">".$tracker_lang['delete']."</td>\n");

echo("</tr>\n");

//echo("<tbody id=\"highlighted\">");


if ($variant == "bookmarks")
echo("<form method=\"post\" action=\"takedelbookmark.php\">");

while ($row = mysql_fetch_assoc($res)) {

$id = $row["id"];
echo("<tr>\n");
//".($row["sticky"] == "yes" ? "" : "")."

// выделение релизов по дням
$day_added = $row['added'];
$day_show = strtotime($day_added);
$thisdate = date('Y-m-d',$day_show);

///$numtorrents = number_format(get_row_count("torrents", "WHERE added LIKE '%".$thisdate." %'"));

if ($thisdate==(isset($prevdate)?$prevdate:"")){
$cleandate = '';
} else {
$day_added = ''.date('Торренты, добавленные вl, j M Y года', strtotime($row['added'])); // You can change this to something else
$cleandate = "<tr><td colspan=15 class=colhead><b>".$day_added."</b></td></tr>\n"; // This also...
}

$prevdate = $thisdate;

$man = array('Jan' => 'Января','Feb' => 'Февраля','Mar' => 'Марта','Apr' => 'Апреля','May' => 'Мая','Jun' => 'Июня','Jul' => 'Июля','Aug' => 'Августа','Sep' => 'Сентября','Oct' => 'Октября','Nov' => 'Ноября','Dec' => 'Декабря');

foreach($man as $eng => $ger){
  $cleandate = str_replace($eng, $ger,$cleandate);
}

$dag = array('Mon' => ' Понедельник','Tues' => 'о Вторник','Wednes' => ' Среду','Thurs' => ' Четверг','Fri' => ' Пятницу','Satur' => ' Субботу','Sun' => ' Воскресенье');

foreach($dag as $eng => $ger){
    $cleandate = str_replace($eng.'day', $ger.'',$cleandate);
}

if (isset($row["sticky"]) && $row["sticky"] == "no") // delete this line if you dont have sticky torrents or you want to display the addate for them also
if(!isset($_GET['sort']) && !isset($_GET['d'])){
   echo $cleandate."\n";
}
// выделение релизов по дням  



	//	print("<td align=\"center\" style=\"padding: 0px\">");
		

//	$dispname = $row["name"];
$dispname = htmlspecialchars_uni($row['name']);


echo "<td colspan=\"13\" align=\"left\"style=\"margin: 0px; padding: 0px;\">";
// style=\"margin: 0px; padding: 0px;\"
 //echo "<div align=\"left\" valign=\"top\" width=\"10%\">";
 
 /*
  $cat_id=$row["category"];
	  
		if (!empty($cat_sql[$cat_id]["name"])) {///$row["cat_name"]

            $chstr = (!empty($_GET["search"])? "&search=".htmlspecialchars($_GET["search"]):"");

			echo("<a href=\"browse.php?incldead=1&cat=" . $row["category"].$chstr."\">");
			
			if (isset($cat_sql[$cat_id]["image"]) && !empty($cat_sql[$cat_id]["image"])){
				
				echo("<img align=\"left\" border=\"0\" src=\"".$DEFAULTBASEURL."/pic/cats/" . $cat_sql[$cat_id]["image"] . "\" alt=\"" . $cat_sql[$cat_id]["name"] . "\" />");
	
			}
			else
				echo($cat_sql[$cat_id]["name"]);
				
			echo("</a>");
		}
		else
			echo("-");
			*/
		
		//	echo "</div>";


echo " <a title=\"Просмотреть детали $dispname\" href=\"details.php?id=".$id."\"><div 
".($row["sticky"] == "yes" ? "class=\"div7\"" : "class=\"div1\"")." align=\"left\" id=\"block\">
".((isset($row["sticky"]) && $row["sticky"] == "yes") ? "<b>Важный</b>: " : "")." <b>".$dispname."</b>\n";
    
echo "</div></a>";


echo "<div class=\"div2\"align=\"left\" id=\"block\"><img style=\"cursor:pointer;\" src=\"".$DEFAULTBASEURL."/pic/plus.gif\" width=\"13px\" id=\"warnpic$id\" onClick=\"getDetails('$id','$DEFAULTBASEURL','warnpic$id','$id')\" alt=\"Предпросмотр описания\" title=\"Предпросмотр описания\"/><div align=\"rigth\" class=\"div5\" id=\"block\"></div>
</div>";


echo "<div align=\"right\" class=\"div4\" id=\"block\">";


	//check if the torrent is new, by qwertzuiop
if (checknewnorrent($row["id"], $row["added"]) && $CURUSER) {
echo "<b><font color=\"red\" size=\"1\">[новый]</font></b>&nbsp;";
++$mark;
}
elseif((get_date_time(gmtime() + 86400) >= $row["added"]) && !$CURUSER)
echo "<b title=\"Добавлен сегодня\"><font color=\"red\" size=\"1\">[новый]</font></b>&nbsp;";

 
if ($row["free"]=="yes")
echo "<img src=\"".$DEFAULTBASEURL."/pic/freedownload.gif\" title=\"".$tracker_lang['golden']."\" alt=\"".$tracker_lang['golden']."\">&nbsp;";	

if (!empty($row["viponly"]))
echo "<img  border=\"0\" width=\"18px\" alt=\"Данная раздача только для VIP пользователей\" title=\"Данная раздача только для VIP пользователей\" src=\"".$DEFAULTBASEURL."/pic/vipbig.gif\">&nbsp;";


if ($variant <> "bookmarks" && $CURUSER){

if (empty($checkin[$id]))
echo "<span style=\"cursor: pointer;\" id=\"checmark_".$row['id']."\"><a onclick=\"checmark('$id', 'add' , 'check', 'browse');\"><img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/head2_2.gif\" alt=\"Включить слежение\" title=\"Добавить в слежения\" /></a><span id=\"loading\"></span></span>";
else
echo "<span style=\"cursor: pointer;\" id=\"checmark_".$row['id']."\"><a onclick=\"checmark('$id', 'del' , 'check', 'browse');\"><img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/head2_2.gif\" alt=\"Отключить слежение\" title=\"Убрать с закладок\" /></a><span id=\"loading\"></span></span>";
 

if(empty($book[$id]))
echo " <span style=\"cursor: pointer;\" id=\"bookmark_".$row['id']."\"><a onclick=\"bookmark('$id', 'add' , 'browse');\"><img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/add.gif\" alt=\"Добавить в закладки\" title=\"Добавить в закладки\" /></a><span id=\"loading\"></span></span>";
else
echo " <span style=\"cursor: pointer;\" id=\"bookmark_".$row['id']."\"><a onclick=\"bookmark('$id', 'del' , 'browse');\"><img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/delete.gif\" alt=\"Убрать с закладок\" title=\"Убрать с закладок\" /></a><span id=\"loading\"></span></span>";

}


//if (get_user_class() >= UC_ADMINISTRATOR)
//echo("<a href=\"download.php?id=$id\"><img src=\"".$DEFAULTBASEURL."/pic/download.gif\" border=\"0\" alt=\"".$tracker_lang['download']."\" title=\"".$tracker_lang['download']."\"></a>\n");

if ((isset($row["owner"]) && $CURUSER["id"] == $row["owner"]) || get_user_class() >= UC_MODERATOR)
$owned = 1;
else
$owned = 0;

if ($owned)
echo("<a href=\"edit.php?id=$row[id]\"><img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/pen.gif\" alt=\"".$tracker_lang['edit']."\" title=\"".$tracker_lang['edit']."\" /></a>&nbsp;\n");



$banned_view=(isset($row["banned_reason"])? htmlspecialchars($row["banned_reason"]):"");
if (isset($row["banned"]) && $row["banned"] == "yes")
//print("<b>[</b>".normaltime($row["added"],true)."<b>]</b>");

//print("<br>");
print("".(get_user_class() < UC_MODERATOR ? "Забанен.": "<b>Забанен по причине</b>: 
".($banned_view=="" ? "не указанна" : $banned_view)."
")."");

echo "&nbsp;";
//else 
//{

//print("<b>Добавлен</b>: ".timesec($row["added"])."");

/*
if ($variant <> "mytorrents" && $variant <> "bookmarks" && $CURUSER ) {
echo "<br>";
echo pic_rating_b(10,$row["rating"]);
}
*/

//}
echo "</div>";

$chstr = (!empty($_GET["search"])? "&search=".htmlspecialchars($_GET["search"]):"");

echo "<a title=\"Искать все файлы в этой категории\" href=\"browse.php?incldead=1&cat=" . $row["category"].$chstr."\"><div align=\"left\" class=\"div3\" id=\"block\">".$cat_sql[$row["category"]]["name"]."</div></a>";

/*
if ($variant == "bookmarks" && $CURUSER){

if (empty($row["mytags"]))
$viewtags=" Пусто";

if (!empty($row["mytags"])){
$viewtags=htmlspecialchars_uni(strip_tags($row["mytags"]));
}

print("<b title=\"Ваши заметки по этому торренту\">Заметки</b>: ".$viewtags."&nbsp;");
unset($viewtags);
}
*/


if ($CURUSER || (!empty($announce_net) && $row["multitracker"]=="yes" && !$CURUSER))
echo "<a href=\"download.php?id=$id\" alt=\"".$tracker_lang['download']." ".$dispname."\" title=\"".$tracker_lang['download']." ".$dispname."\"><div class=\"div6\" align=\"center\" id=\"block\"><b>скачать</b></div></a>";
else
echo "<a href=\"download.php?id=$id\" alt=\"".$tracker_lang['download']." ".$dispname."\" title=\"".$tracker_lang['download']." ".$dispname."\"><div class=\"div6\" align=\"center\" id=\"block\">авторизация и скачать</div></a>";

echo "<div id=\"details_" .$id. "\"></div>";

echo "</td></tr><tr>";


echo("<td colspan=\"2\" ".($_GET["sort"]=="4" || $_GET["sort"]=="1" || $_GET["sort"]=="11" || $_GET["sort"]=="12" ? "class=\"b\"":"class=\"row2\"")." align=\"left\">");  



if ($row["tags"]){
$tags[$row["id"]]="";
foreach(explode(",", $row["tags"]) as $tag) {
	
if (!empty($tags[$row["id"]]))
$tags[$row["id"]].=", ";

$tags[$row["id"]].= "<a style=\"font-weight:normal;\" href=\"browse.php?tag=".urlencode($tag)."&incldead=1\">".tolower($tag)."</a>";
}
$tags[$row["id"]]=$tags[$row["id"]];
}
else
$tags[$row["id"]]="не выбраны";


if (!empty($tags[$row["id"]]))
echo("<b>Теги</b>: ".$tags[$row["id"]]." ".(strlen($tags[$row["id"]])>200 ? "&nbsp; ":"")."");//<br>



	               if (isset($wait)){
				$elapsed = floor((gmtime() - strtotime($row["added"])) / 3600);
				if ($elapsed < $wait)
				{
				  $color = dechex(floor(127*($wait - $elapsed)/48 + 128)*65536);
				  print("<td class=\"row2\" align=\"center\"><nobr><font color=\"$color\">" . number_format($wait - $elapsed) . " h</font></nobr></td>\n");
				}
				else
				  print("<td class=\"row2\" align=\"center\"><nobr>".$tracker_lang['no']."</nobr></td>\n");
		}

	print("</td>\n");

		if ($variant == "mytorrents") {
			print("<td class=\"row2\" align=\"center\">");
			if ($row["visible"] == "no")
				print("<font color=\"red\"><b>".$tracker_lang['no']."</b></font>");
			else
				print("<font color=\"green\">".$tracker_lang['yes']."</font>");
			print("</td>\n");
		}

		if (isset($row["type"]) == "single")
		echo("<td class=\"row2\" align=\"center\">".$row["numfiles"]."</td>\n");
		else {
		echo("<td ".($_GET["sort"]=="2"? "class=\"b\"":"class=\"row2\"")." align=\"center\"><b>".$row["numfiles"]."</b></td>\n");
		}

	
		echo("<td ".($_GET["sort"]=="3"? "class=\"b\"":"class=\"row2\"")." align=\"center\"><b><a href=\"details.php?id=$id&page=last#startcomments\">" . $row["comments"] . "</a></b></td>\n");


//		print("<td align=center><nobr>" . str_replace(" ", "<br />", $row["added"]) . "</nobr></td>\n");
				$ttl = ($ttl_days*24) - floor((gmtime() - sql_timestamp_to_unix_timestamp($row["added"])) / 3600);
				if ($ttl == 1) $ttl .= " час"; else $ttl .= "&nbsp;часов";
		if ($use_ttl)
			print("<td class=\"row2\" align=\"center\">$ttl</td>\n");
	//	print("<td align=\"center\">" . str_replace(" ", "<br />", mksize($row["size"])) . "</td>\n");

        echo ("<td ".($_GET["sort"]=="5"? "class=\"b\"":"class=\"row2\"")." align=\"center\">".mksize($row["size"])."</td>\n");



		echo ("<td ".($_GET["sort"]=="7" || $_GET["sort"]=="8"? "class=\"b\"":"class=\"row2\"")." align=\"center\">");       
		echo ("<b><font color=\"".linkcolor($row["seeders"])."\">".($row["seeders"])."</font></b>\n");
		echo (" | ");
		 echo ("<b><font color=\"".linkcolor(number_format($row["leechers"])). "\">" .number_format($row["leechers"]). "</font></b>\n");
		echo ("</td>");


		if ($variant == "index" || $variant == "bookmarks")
			print("<td ".($_GET["sort"]=="9"? "class=\"b\"":"class=\"row2\"")." align=\"center\">" . (isset($row["username"]) ? ("<a href=\"userdetails.php?id=".$row["owner"] . "\"><b>".get_user_class_color($row["class"], $row["username"]) . "</b></a>") : "<i>без автора</i>")."</td>\n");

		if ($variant == "bookmarks")
			print ("<td class=\"row2\" align=\"center\"><input type=\"checkbox\" name=\"delbookmark[]\" value=\"" . $row["bookmarkid"] . "\" /></td>");



		if ((get_user_class() > UC_MODERATOR) && isset($row["moderated"]) && $row["moderated"] == "yes")(
		$sysop = "<br>[<b><a href=checkdelete.php?id=$id>Удалить</a></b>]");
		
		if ((get_user_class() >= UC_MODERATOR) && $variant == "index") {
		
		    if ($row["moderated"] == "no") {

            print("<td ".($_GET["sort"]=="10"? "class=\"b\"":"class=\"row2\"")." align=\"center\">
			<a class=\"hover\" href=check.php?id=$id><b>Одобрить</b></a>
			</td>\n");
			}

            else

            print("<td ".($_GET["sort"]=="10"? "class=\"b\"":"class=\"row2\"")." align=\"center\"><b>
			".($row["classusername"] ? "<a href=\"".$DEFAULTBASEURL."/userdetails.php?id=$row[moderatedby]\">".get_user_class_color($row["classname"], $row["classusername"])."</a>" : "id [".$row["moderatedby"]."]")."
           </b>".$sysop."</td>\n");
				}

	if ((get_user_class() <= UC_UPLOADER) && $variant == "index"){
		
		    if ($row["moderated"] == "no")
            print("<td ".($_GET["sort"]=="10"? "class=\"b\"":"class=\"row2\"")." align=\"center\">
			<b>нет</b></td>\n");
            else
            print("<td ".($_GET["sort"]=="10"? "class=\"b\"":"class=\"row2\"")." align=\"center\"><b>
     	".($row["classusername"] ? "<a href=\"".$DEFAULTBASEURL."/userdetails.php?id=$row[moderatedby]\">".get_user_class_color($row["classname"], $row["classusername"])."</a>" : "id [$row[moderatedby]]")."
				</b></td>\n");
       }
		
		if ((get_user_class() == UC_SYSOP) && $variant == "index")
			print("<td class=\"row2\" align=\"center\"><input type=\"checkbox\" id=\"checkbox_tbl_" . $id . "\" name=\"cheKer[]\" value=\"" . $id . "\" /></td>\n");


//print("</tr><tr><td colspan=10></td>");  

print("</tr>\n");
$oldday = isset($day); // старая дата
}
//print("</tbody>");  
	
	

	if ($variant == "index" && $CURUSER && !empty($mark))
		print("<tr><td class=\"a\" colspan=\"12\" align=\"center\"><a href=\"markread.php\"><b>пометить все торренты прочитаными</b></a></td></tr>");

	//print("</table>\n");

	if ($variant == "index" && get_user_class() == UC_SYSOP) {
	
echo "<script language=\"JavaScript\" type=\"text/javascript\">
<!--
function changeSubmenu(form, si) {
while(form.pcat.length > 0) {
form.pcat.remove(form.pcat.length - 1);
}
var e=document.createElement('option');
e.text=\"выберите категорию для перемещения\";
e.value='0';
try {
form.pcat.add(e,null);
} catch(ex) { 
form.pcat.add(e);
}";

echo "if(form.actions.options[form.actions.options.selectedIndex].value == 'movcat') { \n";

$cats = genrelist();
foreach ($cats as $row)
echo "var e=document.createElement('option');
e.text='".htmlspecialchars($row["name"])."';
e.value='".$row["id"]."';
try {
form.pcat.add(e,null);
} catch(ex) {
form.pcat.add(e);
}\n";

echo "}else {
e.text=\"только для выбора категории\";
e.value='';
}
for(i = 0; i < form.pcat.length; i++) {
if (form.pcat.options[i].value == si) {
form.pcat.options.selectedIndex = i;
}
}
}
//--></script>";


print("<tr><td align=\"right\" colspan=\"12\">
<select name=\"actions\" onchange=\"changeSubmenu(this.form, 0)\">\n
<option selected>выбрать действие</option>
<option value=\"gold\">поставить золото</option>
<option value=\"ungold\">снять золото</option>
<option value=\"check\">поставить одобрение</option>
<option value=\"uncheck\">снять одобрение</option>
<option value=\"main\">поставить важность</option>
<option value=\"unmain\">снять важность</option>
<option value=\"newdate\">поставить сегодняшнюю дату</option>
<option value=\"anonim\">убрать автора, aka анонимность</option>
<option value=\"pravo\">удалить и пометить авторством</option>
<option value=\"movcat\">переместить в категорию</option>
<option value=\"delete\">удалить</option>
</select>\n
<select name=\"pcat\">\n
<option value=\"0\">только для выбора категории</option>
</select>\n

<input type=\"hidden\" name=\"referer\" value=\"".ADDREFLINK."\">
<input type=\"submit\" class=\"btn\" onClick=\"return confirm('Вы уверены, что хотите выполнить данное действие?')\" value=\"Выполнить\">\n
</td></tr>\n");
}

	if ($variant == "bookmarks")
		print("<tr><td colspan=\"12\" align=\"right\"><input type=\"submit\" value=\"".$tracker_lang['delete']."\"></td></tr>\n");

	if ($variant == "index" || $variant == "bookmarks") {
		if (get_user_class() == UC_SYSOP) {
			print("</form>\n");
		}
	}

	return $rows;
}

function hash_pad($hash) {
	return str_pad($hash, 20);
}

function hash_where($name, $hash) {
	$shhash = preg_replace('/ *$/s', "", $hash);
	return "($name = " . sqlesc($hash) . " OR $name = " . sqlesc($shhash) . ")";
}

function get_user_icons($arr, $big = false) {
		if ($big) {
				$donorpic = "starbig.gif";
				$warnedpic = "warned.gif";
				$disabledpic = "disabled.gif";
				$style = "style='margin-left: 4pt'";
		} else {
				$donorpic = "star.gif";
				$warnedpic = "warned.gif";
				$disabledpic = "disabled.gif";
			//	$parkedpic = "parked.gif";
				$style = "style=\"margin-left: 2pt\"";
		}
		$pics = $arr["donor"] == "yes" ? "<img src=\"pic/$donorpic\" alt='Наш Вкладчик Донор!' border=\"0\" $style>" : "";
		if ($arr["enabled"] == "yes")
		
		$pics .= $arr["num_warned"] >= "1" || $arr["warned"] == "yes" ? "<img src=pic/$warnedpic alt=\"Предупрежден\" border=0 $style>" : "";
		
		else
		
		$pics .= "<img src=\"pic/$disabledpic\" alt=\"Отключен\" border=\"0\" $style>\n";
		
		if (isset($arr["shoutbox"]))
		$pics .= ($arr["shoutbox"] <> "0000-00-00 00:00:00" ? "<img src=\"pic/warning.gif\" alt=\"Бан на доступ к чату до ".$arr["shoutbox"]."\" border=\"0\" $style>" : "");
		
		if (isset($arr["forum_com"]))
		$pics .= $arr["forum_com"] <> "0000-00-00 00:00:00" ? "<img src=\"pic/warning.gif\" alt=\"Бан на доступ к форуму до ".$arr["forum_com"]."\" border=\"0\" $style>" : "";
		

		$pics .= $arr["parked"] == "yes" ? "<img src=pic/head2_2.gif alt=\"Припаркован аккаунт\" border=\"0\" $style>" : "";
		return $pics;
}

function parked() {
	   global $CURUSER;
	   if ($CURUSER["parked"] == "yes")
		  stderr($tracker_lang['error'], "Ваш аккаунт припаркован.");
}



//Генератор паролей
function generatePassword($length = 15) {
$set = array("a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J","k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T","u","U","v","V","w","W","x","X","y","Y","z","Z","1","2","3","4","5","6","7","8","9");
	$str="";
	for($i = 1; $i <= $length; $i++)
	{
		$ch = rand(0, count($set)-1);
		$str.= $set[$ch];
	}
	return $str;
}
//Генератор паролей

/// проверить
function location($url, $time = 0) {
	//global $DEFAULTBASEURL;
	$url = str_replace ( ">", urlencode ( ">" ), str_replace ( "<", urlencode ( "<" ), $url ) );
	//if (! preg_match ( "#http://(.*?)#si", $url ))
	//	$url = $DEFAULTBASEURL . '/' . $url;
	if (! @header ( (! $time ? "Location: " : "Refresh: " . $time . ", url=") . $url ))
		print ( '<META HTTP-EQUIV="Refresh" CONTENT="' . $time . ';url=' . $url . '/">' );
}


function cache_clean($all) {

$dh = opendir(ROOT_PATH.'cache/');
while ($file = readdir($dh)) :
if (preg_match('/^(.+)\.$/si', $file, $matches))
$file = $matches[1];
if ( stristr($file,'txt') && !stristr($file,'log_old.txt') && !stristr($file,'sqlerror.txt') && !stristr($file,'hacklog.txt') && !stristr($file,'monitoring_') && !stristr($file,'info_cache_stat') && !stristr($file,'chat_log.txt')&& !stristr($file,'error_torrent.txt')&& !stristr($file,'list.txt') )
{ @unlink(ROOT_PATH."cache/$file");}
endwhile;
closedir($dh);

}

function parse_referer($cache=false) {
global $refer_parse, $CURUSER;

/// рефер данные
$referer = (isset($_SERVER["HTTP_REFERER"]) ? htmlentities($_SERVER["HTTP_REFERER"]):"");
if (!empty($referer))
$parse_site = parse_url($referer, PHP_URL_HOST);

/// собственные данные о сайте
$site_own = (($_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://").htmlspecialchars_uni($_SERVER['HTTP_HOST']);
if (!empty($site_own))
$parse_owner = parse_url($site_own, PHP_URL_HOST);
//$parse_owner = str_replace("www.","", $parse_owner);
/// сравниваем данные
if(!empty($refer_parse) && !empty($parse_site) && !stristr($parse_site,$parse_owner) && ($parse_owner<>$parse_site)){

$ip = getip();
$ref = ($referer);

$uid = $CURUSER["id"];
if (empty($uid))
$uid = 0;

sql_query("INSERT INTO referrers (parse_url,parse_ref,uid,ip,date,numb,lastdate) VALUES (".sqlesc($parse_site).",".sqlesc($ref).",".sqlesc($uid).",".sqlesc($ip).",".sqlesc(get_date_time()).",1,'0000-00-00 00:00:00')");

if (!mysql_insert_id()){
sql_query("UPDATE referrers SET numb=numb+1,lastdate=".sqlesc(get_date_time())." WHERE uid=".sqlesc($uid)." AND ip=".sqlesc($ip)." AND parse_url=".sqlesc($parse_site)." AND parse_ref=".sqlesc($ref)) or sqlerr(__FILE__,__LINE__);
}

if (date('i')>=30 && date('i')<=40)
@unlink(ROOT_PATH."cache/block-top_refer.txt");
}

}


function failedloginscheck () {
global $maxloginattempts;
$total = 0;
$ip = sqlesc(getip());
$Query = sql_query("SELECT SUM(attempts) FROM loginattempts WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
list($total) = mysql_fetch_array($Query);
if ($total >= $maxloginattempts) {
sql_query("UPDATE loginattempts SET banned = 'yes' WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
stderr("Вход заблокирован!", "Вы использовали <b>максимум количество раз - ".$maxloginattempts."</b> с вводом неправильных данных<br>Сейчас ваш <b>".getip()."</b> забанен.");
}}

function failedlogins ($user,$id) {
$usermy=$user;
//	die($usermy);
$a_comment="$user,";
$a_id="$id,";
$ip = sqlesc(getip());
$ip_log = getip();
$added = sqlesc(get_date_time());
$a = (mysql_fetch_row(sql_query("select COUNT(*) from loginattempts where ip=$ip"))) or sqlerr(__FILE__, __LINE__);
//die($a[0]);
$upd=remaining();
if($upd==1) {
global $maxloginattempts;

  $type = sqlesc("bans");
  $color = sqlesc("6e7d8f");
  $text = sqlesc("Пользователь заблокировал свой $ip_log. Причина: Попытки входа - $maxloginattempts");
  $added = sqlesc(get_date_time());
  sql_query("INSERT INTO sitelog (added, color, txt, type) VALUES($added, $color, $text, $type)");
}

if ($a[0] == 0) {
sql_query("INSERT INTO loginattempts (ip, added, attempts, comment) VALUES ($ip,$added,1,'$id')") or sqlerr(__FILE__, __LINE__);
}
else
{
$update[] = "attempts = attempts + 1";
$update[] = "comment = CONCAT(".sqlesc($a_id)." ,comment)";
sql_query("UPDATE loginattempts SET " . implode(", ", $update) . " where ip=$ip") or sqlerr(__FILE__, __LINE__);
}

$upd2=$upd-1;
stderr("Ошибка входа", "Пароль к учетной записи ($user) неверен<br> Если не помните пароль, попробуйте <b><a href=recover.php>восстановить</a></b> его.<br>
Осталось попыток входа: <b>".$upd2."</b>");
}

function remaining () {
global $maxloginattempts;
$total = 0;
$ip = sqlesc(getip());
$Query = sql_query("SELECT SUM(attempts) FROM loginattempts WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
list($total) = mysql_fetch_array($Query);
$remaining = $maxloginattempts - $total;
/*if ($remaining <= 2 )
$remaining = "<font color=red size=4>".$remaining."</font>";
else
$remaining = "<font color=green size=4>".$remaining."</font>";
*/
return $remaining;
}


function str($input,$html=false) {
    $input = trim($input); 
//// \x00, \n, \r, \, ', " and \x1a 
    $input = str_replace(x00,"x00",$input); 
    //$input = str_replace(x1a,"x1a",$input); 
    $input = str_replace("`","`",$input); 

    $input = str_replace("\\","",$input); 
if(!$html)    $input = htmlspecialchars($input); 
    $input = str_replace("`","`",$input); 
    $input = str_replace("'","'",$input); 
    $input = str_replace("'","&#x27;",$input); 
if(!$html)    $input = str_replace(">","&gt;",$input); 
if(!$html)    $input = str_replace("<","&lt;",$input); 

    $input = str_ireplace("&amp;","&",$input); 
    return $input; 
}

function get_seed_time($st) {
$secs = $st;
$mins = floor($st / 60);
$hours = floor($mins / 60);
$days = floor($hours / 24);
$week = floor($days / 7);
$month = floor($week / 4);

if ($month > 0) {
$week_elapsed = floor(($st - ($month * 4 * 7 * 24 * 60 * 60)) / (7 * 24 * 60 * 60));
$days_elapsed = floor(($st - ($week * 7 * 24 * 60 * 60)) / (24 * 60 * 60));
$hours_elapsed = floor(($st - ($days * 24 * 60 * 60)) / (60 * 60));
$mins_elapsed = floor(($st - ($hours * 60 * 60)) / 60);
$secs_elapsed = floor($st - $mins * 60);
return "$month мес. $week_elapsed нед. $days_elapsed дн. $hours_elapsed час.";
}
if ($week > 0) {
$days_elapsed = floor(($st - ($week * 7 * 24 * 60 * 60)) / (24 * 60 * 60));
$hours_elapsed = floor(($st - ($days * 24 * 60 * 60)) / (60 * 60));
$mins_elapsed = floor(($st - ($hours * 60 * 60)) / 60);
$secs_elapsed = floor($st - $mins * 60);
return "$week нед. $days_elapsed дн. $hours_elapsed час. $mins_elapsed мин.";
}
if ($days > 0) {
$hours_elapsed = floor(($st - ($days * 24 * 60 * 60)) / (60 * 60));
$mins_elapsed = floor(($st - ($hours * 60 * 60)) / 60);
$secs_elapsed = floor($st - $mins * 60);
return "$days дн. $hours_elapsed час. $mins_elapsed мин.";
}
if ($hours > 0) {
$mins_elapsed = floor(($st - ($hours * 60 * 60)) / 60);
$secs_elapsed = floor($st - $mins * 60);
return "$hours час. $mins_elapsed мин. ".($secs_elapsed==0?"":"$secs_elapsed сек");
}
if ($mins > 0) {
$secs_elapsed = floor($st - $mins * 60);
return "$mins мин. ".($secs_elapsed==0?"":"$secs_elapsed сек");
}
if ($secs > 0) {
return "$secs секунд.";
}
return "Время не зафиксированно";
}



function karma($karma) {
    if ($karma == 0)
        $color = "#000000";
    elseif ($karma < 0)
        $color = "#FF0000";
    elseif ($karma > 0 && $karma < 10)
    {
        $color = "#000080";
        $karma = "+$karma";
    }
    elseif ($karma > 10)
    {
        $color = "#008000";
        $karma = "+$karma";
    }
    return "<font style=\"color:$color;vertical-align:top;font-size:13px;\">$karma</font>";
}




//// функции для старого php4

if(!function_exists("stripos")) {
 function stripos($haystack, $needle, $offset = 0) {
  return strpos(strtolower($haystack), strtolower($needle), $offset);
 }
}

if(!function_exists("str_ireplace")){
  function str_ireplace($search,$replace,$subject){
    $token = chr(1);
    $haystack = strtolower($subject);
    $needle = strtolower($search);
    while (($pos=strpos($haystack,$needle))!==FALSE){
      $subject = substr_replace($subject,$token,$pos,strlen($search));
      $haystack = substr_replace($haystack,$token,$pos,strlen($search));
    }
    $subject = str_replace($token,$replace,$subject);
    return $subject;
  }
}


if(!function_exists("memory_get_usage")){
function memory_get_usage(){
     $pid = @getmypid();
     @exec("ps -o rss -p $pid", $output);
     return $output[1] *1024;
}
}

if (!function_exists('highlight')) {
	function highlight($search, $subject, $hlstart = '<b><font color=red>', $hlend = '</font></b>')
	{
		$srchlen = strlen($search);    // lenght of searched string
		if ($srchlen == 0)
			return $subject;
		
		$find = $subject;
		while ($find = stristr($find, $search)) // find $search text in $subject -case insensitiv
		{
			$srchtxt = substr($find,0,$srchlen);    // get new search text
			$find = substr($find,$srchlen);
			$subject = str_replace($srchtxt, $hlstart.$srchtxt.$hlend, $subject);    // highlight founded case insensitive search text
		}
		
		return $subject;
	}
}
//// функции для старого php4




function meta($id_torrent = false) {

global $SITENAME,$DEFAULTBASEURL;

$def_ico=$DEFAULTBASEURL; ///http://localhost
$def_url=$DEFAULTBASEURL.getenv("REQUEST_URI"); ///http://localhost/


if (empty($_COOKIE["uid"])){

if (basename($_SERVER['SCRIPT_FILENAME']) == 'details.php') {
$id=$_GET["id"];

if (!empty($id)){
$s=sql_query("SELECT name,descr FROM torrents WHERE id=".sqlesc($id)); 
$meta_bot=mysql_fetch_array($s);
}
$name=$meta_bot["name"];
$descr=$meta_bot["descr"];
}
elseif (basename($_SERVER['SCRIPT_FILENAME']) == 'userdetails.php') {
$id=$_GET["id"];
if (!empty($id)){
$s=sql_query("SELECT username,info FROM users WHERE id=".sqlesc($id)); 
$meta_bot=mysql_fetch_array($s);
}
$name=$meta_bot["username"];
$descr=$meta_bot["info"];
}
elseif (basename($_SERVER['SCRIPT_FILENAME']) == 'browse.php') {

if (!empty($_GET["tag"])){
$q = str_replace(" ",".",sqlesc("%".sqlwildcardesc(trim($_GET["tag"]))."%"));
$s=sql_query("SELECT name FROM categories WHERE id=(SELECT id FROM tags WHERE tag LIKE {$q2} LIMIT 1)"); 
$meta_bot=mysql_fetch_array($s);
$name=$_GET["tag"];
$descr=$meta_bot["name"];
} elseif (!empty($_GET["cat"])){
$idcat=(int)$_GET["cat"];
if (!empty($idcat)){
$s=sql_query("SELECT name FROM categories WHERE id=".sqlesc($idcat)); 
$meta_bot=mysql_fetch_array($s);
}
//$name=$_GET["tag"];
$name=$meta_bot["name"];
} else {
$name="Торрент файлы";
$descr="Список торрент релизов отсортированых по времени добавления";
}

}
else
{
$name = "музыкальный трекер, портал музыки, muz-tracker, muz, музыка, торрент, портал, mp3";
$descr = "Музыкальный трекер. Бесплатно скачать фильмы, игры, программы и другие полезности без всяких ограничений.";
}

$desc = str_replace("\n\n", " ", $desc);
$descr = strip_bbcode($descr);
$descr = strip_tags($descr);
//$descr = preg_replace('/[^\w]+/i', ' ', $descr); /// замена . , за пробел
$descr = trim(preg_replace('/[\r\n\t]/i', ' ', $descr)); /// замена табуляций
$descr = substr($descr,0,255);

if (basename($_SERVER['SCRIPT_FILENAME']) == 'index.php')
$descr = "Музыкальный трекер. Общение, чаты, конкурсы, викторины, знакомства. С нашего трекера можно качать бесплатно фильмы, игры, программы и другие полезности без всяких ограничений.";

//$keywords="$hometext $bodytext";
$keywords = strip_tags($name);

if (basename($_SERVER['SCRIPT_FILENAME']) <> 'index.php'){
$keywords = preg_replace('/[^\w]+/i', ' ', $keywords);
$keywords = preg_replace("/\s/",",",$keywords); /// заменяем пробелы на ,
$keywords = trim(preg_replace('/[\r\n\t]/i', ',', $keywords));
}

$keywords = preg_replace("/,\s/",",",$keywords); /// заменяем пробелы на ,

$keywords = substr($keywords,0,1600);
$keywords = array_unique(explode(",", $keywords));

for ($a=0,$b=7; $a < sizeof($keywords) && $b < 800; $a++) {

if (($c=strlen($keywords[$a]))>3) {

if (empty($key_words)){
$key_words=$keywords[$a]; 
} else {
$key_words=$key_words.", ".$keywords[$a].""; 
}
$b+=$c+2; 
}
}
}


$content = "";
$content.= "<meta http-equiv=\"content-type\" content=\"text/html; charset=windows-1251\">\n";

$site_own = (($_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://").htmlspecialchars_uni($_SERVER['HTTP_HOST']);
$parse_owner = parse_url($site_own, PHP_URL_HOST);

$parse_owner = end(explode('.', $parse_owner));

$content.= "<meta name=\"author\" content=\"7Max7\"/>\n";
$content.= "<meta name=\"publisher-url\" Content=\"".($def_url)."\"/>\n";
$content.= "<meta name=\"copyright\" content=\"Tesla Tracker TT v.Gold (".date("Y").")\"/>\n";
$content.= "<meta name=\"generator\" content=\"PhpDesigner см. useragreement.php\"/>\n";

if (!empty($key_words))
$content.= "<meta name=\"keywords\" content=\"".$key_words."\"/>\n";

if (!empty($descr))
$content.= "<meta name=\"description\" content=\"".$descr."\"/>\n";

$content.= "<meta name=\"robots\" content=\"all\"/>\n";
$content.= "<meta name=\"revisit-after\" content=\"1 days\"/>\n";
$content.= "<meta name=\"rating\" content=\"general\"/>\n";
$content.= "<link rel=\"shortcut icon\" href=\"".$def_ico."/pic/favicon.ico\" type=\"image/x-icon\"/>\n";
$content.= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Последние торрент релизы\" href=\"".$def_ico."/rss.php\"/>\n";

$content.= "<!-------------  Конкурентам ПРИИИИВЕЕЕЕД от 7Max7 --------------> \n";

echo $content;
}
/// для stdhead (chat) создание meta заголовков

function protectmail($s=false) {
	$result = '';
	$s = "mailto: ".$s;
	for($i=0; $i< strlen($s); $i++)
	{
		 $result .= '&#'.ord(substr($s, $i, 1)).';';
	}
	return $result;
}

function file_ungzip($fromFile){
	$zp = @gzopen($fromFile, "r");
	while(!@gzeof($zp)) { $string .= @gzread($zp, 4096); }
	@gzclose($zp);
	return $string;
}

function repair_table($table=false){
	
include("include/passwords.php"); 

if (!empty($table)){
sql_query("REPAIR TABLE ".$table." EXTENDED") or sqlerr(__FILE__,__LINE__);
sql_query("REPAIR TABLE ".$table." USE_FRM") or sqlerr(__FILE__,__LINE__);
}
else
{
$result = sql_query("SHOW TABLE STATUS FROM ".$mysql_db_fix_by_imperator);

while ($row = mysql_fetch_array($result))
sql_query("REPAIR TABLE ".$row["Name"]) or sqlerr(__FILE__,__LINE__);
}
}

?>