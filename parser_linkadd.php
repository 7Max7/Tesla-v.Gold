<?
require_once("include/bittorrent.php");
//require_once("include/benc.php");

@ini_set('display_errors', 'On');
@set_time_limit(60);

dbconn();
///loggedinorreturn(); 
define('IN_PARSER', true);
global $DEFAULTBASEURL,$CURUSER;

$nu=rand(1000,5000);
$debug=0;


$dg = number_format(get_row_count("tfgrabber"));


echo "<title>$dg</title>";
//if (get_user_class() < UC_MODERATOR)
///die("Нет доступа.");
sql_query("DELETE FROM `tfgrabber` WHERE category='1171' OR  category='162' OR  category='365' OR  category='164' OR  category='362' OR  category='364' OR  category='844' OR  category='987' OR  category='370' OR  category='110'") or sqlerr(__FILE__, __LINE__);


$id_page=(!empty($_GET["page"]) ? $_GET["page"]:"0");
$des=$_GET["d"];

if ($des==1)
$de="&o=oldes";
elseif ($des==2)
$de="&o=newest";
else
$de="";

if (isset($_GET["page"])) {

$host="tfile.ru";
$path="/forum/ssearch.php?start=".$id_page.$des;

$referer = $host.$path;

$cookies = array();

//require_once('parser_tconfig.php');


if (@file_exists("cache/toru_session.txt")){
$content=@file_get_contents("cache/toru_session.txt"); 
$cds = explode("\n",$content);
foreach ($cds as $co) {
if (!empty($co))
$cookies[] = $co.";";
}
}


// Устанавливаем соединение
$fp = fsockopen($host, 80, $errno, $errstr, 30) or die("Нет соединения соответственно и данных. <script>setTimeout('document.location.href=\"parser_linkadd.php\"', $nu);</script>"); 
// Проверяем успешность установки соединения
if ($fp) {
// Заголовок HTTP-запроса
$headers = "GET $path HTTP/1.0\r\n";
$headers.= "Host: $host\r\n";
$headers.= "Content-type: application/x-www-form-urlencoded\r\n";
$headers.= "Content-Length: ".strlen($data)."\r\n";
$headers.= "Accept: *\r\n";
$headers.= "Accept-Charset: *\r\n";
$headers.= "Accept-Encoding: binary\r\n";
$headers.= "Accept-Language: ru\r\n";
$headers.= "Referer: ".$host."\r\n";
$headers.= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.2; ru; rv:1.9.2) Gecko/20100115 Firefox/3.6 WebMoney Advisor\r\n";
$headers.= "Connection: Close\r\n";

if ($cookies) {
foreach ($cookies as $cookie)
$headercookie .= $cookie." ";
$headercookie = substr($headercookie, 0, -2);
$headers .= "Cookie: ".$headercookie."\r\n";
}
$headers .= "\r\n";
// Отправляем HTTP-запрос серверу

@fwrite($fp, $headers.$data);
$date = "";
while (!feof($fp)){
$line = fgets($fp, 1024);
$date.=$line;

// В заголовке меня интерисуют только куки
@list($field, $value) = preg_split('/\s*:\s*/', $line, 2);
// Запоминаем найденную куку
if (strtolower($field) === 'set-cookie'){
// Точнее, запоминаем только само значение куки (недекодированное)
$result[] = array_shift(preg_split('/\s*;\s*/', $value, 2));
}
}

@fclose($fp);
} else die("Нет соединения соответственно и данных. <script>setTimeout('document.location.href=\"parser_linkadd.php\"', $nu);</script>");

preg_match('/<div class="l">(.*?)<\/div\>/is', $date, $dnik);

if (stristr($dnik[0],'регистрация'))
die("Перенаправление. <script>setTimeout('document.location.href=\"parser_tconfig.php\"', $nu);</script>");

if (!empty($dnik[0]))
echo "Ваш логин: <b>".strip_tags(trim(str_replace("нет новых", "", $dnik[0])))."</b> -> Все впорядке<br>";


if (count($result)>2){
$sf =ROOT_PATH."cache/toru_session.txt"; 
@unlink($sf);
$fpsf=fopen($sf,"a+"); 
fputs($fpsf,implode("\n",$result)); 
fclose($fpsf);
//echo "<script>setTimeout('document.location.href=\"parser_linkadd.php\"', $nu);</script>";
//print_r($result); 
echo("<i>Обновлен файл сессий</i><br>");
}


if (stristr($date,'Регистрация') && stristr($date,'Забыли пароль') && !stristr($date,'Новых сообщений нет'))
die("Возможно неправильные куки, пожалуйста введите их заново. <script>setTimeout('document.location.href=\"parser_tconfig.php\"', $nu);</script>");


preg_match('/<table id="topics">(.*?)<\/table\>/is', $date, $dtable);


$table = $dtable[0];

preg_match_all("|(/forum/viewforum.php?.*?)\">|U", $table, $links_f);  

//print_r($links_f[0]);

$numf=0; $linkif=array();
foreach ($links_f[0] as $lin) {
if (!stristr($lin,'#') && !empty($lin)) {
$array=array("/forum/viewforum.php?f=");
$lin = str_replace($array, "", $lin);
list ($id,$hz) = explode("\">",$lin);
//echo $id."<br>";
$linkif[$numf]=$id;	
++$numf;
}
}



preg_match_all("|(/forum/viewtopic.php?.*?)\">|U", $table, $links);  

//print_r($links);
$numtor=0;
foreach ($links[0] as $lin) {

if (!stristr($lin,'#') && !empty($lin)) {
	
$array=array("/forum/viewtopic.php?t=","\">");
$lin = str_replace($array, "", $lin);
	
sql_query("INSERT INTO tfgrabber (details, category, work) VALUES (".sqlesc($lin).",".sqlesc($linkif[$numtor]).",".sqlesc("1").")");
if (mysql_insert_id()){
echo "id <font color=green><b>".$lin."</b> ($linkif[$numtor])</font> <b>добавлен</b><br>";
} else {
echo "id <font color=red><i>".$lin."</i> ($linkif[$numtor])</font> <i>уже в базе</i><br>";
}
++$numtor;
}


/// $lin - id нашего торрента
/// $linkif[$numtor] - категория музыка напр


}


if ($debug==1) echo $date;

if (count($numtor)<>0 && $id_page<1100)

echo "<script>setTimeout('document.location.href=\"parser_linkadd.php?page=".($id_page+25)."&d=$de\"', $nu);</script>";
elseif($id_page>=1100) {
	


$res = sql_query("SELECT category FROM tfgrabber WHERE category > ".sqlesc($f)." AND category<>'1171' AND category<>'162' AND category<>'365' AND category<>'164' AND category<>'362' AND category<>'364' AND category<>'366' AND category<>'366' AND category<>'370' AND category<>'110' AND category<>'844' AND category<>'987' ORDER BY category ASC LIMIT 1") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($res);

$f=$row["category"];
echo "<br> Переключение на категории! Идет поиск первой $f...<br>";

if ($debug==1) echo $date;

echo "<script>setTimeout('document.location.href=\"parser_linkadd.php?fpage=0&f=$f\"', $nu);</script>";
//echo "<script>setTimeout('document.location.href=\"parser_linkadd.php\"', $nu);</script>";
}
}















$id_page=(!empty($_GET["fpage"]) ? $_GET["fpage"]:"0");

if (isset($_GET["fpage"])) {
	
$f=(!empty($_GET["f"]) ? $_GET["f"]:"0");

$host="tfile.ru";
$path="/forum/viewforum.php?f=$f&start=".$id_page;


$referer = $host.$path;

$cookies = array();
//require_once('parser_tconfig.php');


if (@file_exists("cache/toru_session.txt")){
$content=@file_get_contents("cache/toru_session.txt"); 
$cds = explode("\n",$content);
foreach ($cds as $co) {
if (!empty($co))
$cookies[] = $co.";";
}
}
///http://localhost/parser_linkadd.php?fpage=0&f=0

// Устанавливаем соединение
$fp = fsockopen($host, 80, $errno, $errstr, 30) or die("Нет соединения соответственно и данных. <script>setTimeout('document.location.href=\"parser_linkadd.php\"', $nu);</script>"); 
// Проверяем успешность установки соединения
if ($fp) {
// Заголовок HTTP-запроса
$headers = "GET $path HTTP/1.0\r\n";
$headers.= "Host: $host\r\n";
$headers.= "Content-type: application/x-www-form-urlencoded\r\n";
$headers.= "Content-Length: ".strlen($data)."\r\n";
$headers.= "Accept: *\r\n";
$headers.= "Accept-Charset: *\r\n";
$headers.= "Accept-Encoding: binary\r\n";
$headers.= "Accept-Language: ru\r\n";
$headers.= "Referer: ".$host."\r\n";
$headers.= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.2; ru; rv:1.9.2) Gecko/20100115 Firefox/3.6 WebMoney Advisor\r\n";
$headers.= "Connection: Close\r\n";

if ($cookies) {
foreach ($cookies as $cookie)
$headercookie .= $cookie." ";
$headercookie = substr($headercookie, 0, -2);
$headers .= "Cookie: ".$headercookie."\r\n";
}
$headers .= "\r\n";
// Отправляем HTTP-запрос серверу

@fwrite($fp, $headers.$data);
$date = "";
while (!feof($fp)){
$line = fgets($fp, 1024);
$date.=$line;

// В заголовке меня интерисуют только куки
@list($field, $value) = preg_split('/\s*:\s*/', $line, 2);
// Запоминаем найденную куку
if (strtolower($field) === 'set-cookie'){
// Точнее, запоминаем только само значение куки (недекодированное)
$result[] = array_shift(preg_split('/\s*;\s*/', $value, 2));
}
}

@fclose($fp);
} else die("Нет соединения соответственно и данных. <script>setTimeout('document.location.href=\"parser_linkadd.php\"', $nu);</script>");


preg_match('/<div class="l">(.*?)<\/div\>/is', $date, $dnik);

if (stristr($dnik[0],'регистрация'))
die("Перенаправление. <script>setTimeout('document.location.href=\"parser_tconfig.php\"', $nu);</script>");


if (!empty($dnik[0]))
echo "Ваш логин: <b>".strip_tags(trim(str_replace("нет новых", "", $dnik[0])))."</b> -> Все впорядке<br>";


$res = sql_query("SELECT category FROM tfgrabber WHERE category > ".sqlesc($f)." AND category<>'1171' AND category<>'162' AND category<>'365' AND category<>'164' AND category<>'362' AND category<>'364' AND category<>'366' AND category<>'366' AND category<>'370' AND category<>'110' AND category<>'844' AND category<>'987' ORDER BY category ASC LIMIT 1") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($res);

$fnwxt=$row["category"];
echo "<a href=\"parser_linkadd.php?fpage=0&f=$fnwxt\">к следующей категории????</a><br>";


if (count($result)>2){
$sf =ROOT_PATH."cache/toru_session.txt"; 
@unlink($sf);
$fpsf=fopen($sf,"a+"); 
fputs($fpsf,implode("\n",$result)); 
fclose($fpsf);
//print_r($result); 
echo("<i>Обновлен файл сессий</i><br>");
}

if (stristr($date,'Регистрация') && stristr($date,'Забыли пароль') && !stristr($date,'Новых сообщений нет'))
die("Возможно неправильные куки, пожалуйста введите их заново. <script>setTimeout('document.location.href=\"parser_tconfig.php\"', $nu);</script>");

$table = $dtable[0];
preg_match_all("|(/forum/viewtopic.php?.*?)\">|U", $date, $links);  
$tr = false;
$numtor=0;
foreach ($links[0] as $lin) {

if (!stristr($lin,'#') && !empty($lin)) {

$array=array("/forum/viewtopic.php?t=","\">","class=\"s_help\" title=\"Помощь","\"");
$lin = str_replace($array, "", $lin);
	
sql_query("INSERT INTO tfgrabber (details, category, work) VALUES (".sqlesc($lin).",".sqlesc($f).",".sqlesc("1").")");

if (mysql_insert_id()){
echo "id <font color=green><b>".$lin."</b> ($f)</font> <b>добавлен</b><br>";
$tr = true;
} else {
echo "id <font color=red><b>".$lin."</b> ($f)</font> <i>уже в базе</i><br>";
}
++$numtor;
}
}

if ($numtor==0 || $id_page>1000 || ($id_page>=50 && $tr==false)) {

$res = sql_query("SELECT category FROM tfgrabber WHERE category > ".sqlesc($f)." AND category<>'1171' AND category<>'162' AND category<>'365' AND category<>'164' AND category<>'362' AND category<>'364' AND category<>'366' AND category<>'366' AND category<>'370' AND category<>'110' AND category<>'844' AND category<>'987' ORDER BY category ASC LIMIT 1") or sqlerr(__FILE__, __LINE__);


$row = mysql_fetch_assoc($res);

$f=$row["category"];
echo "<br> Переключение на новую категорию! Идет поиск $f...<br>";


if ($debug==1)
echo $date;

//die("проверь$f!");
echo "<script>setTimeout('document.location.href=\"parser_linkadd.php?fpage=0&f=$f\"', ".($nu/2).");</script>";
} else {
	

if ($debug==1)
echo $date;

echo "<script>setTimeout('document.location.href=\"parser_linkadd.php?fpage=".($id_page+25)."&f=$f\"', $nu);</script>";

}
}




if (count($_GET)==0)
die("Забыл ввести страничку? <a href=\"parser_linkadd.php?page=0\">жми сюда значить</a> или <a href=\"parser_linkadd.php?fpage=0&f=4\">по категориям</a> ");



?>