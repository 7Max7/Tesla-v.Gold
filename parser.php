<?
require_once("include/bittorrent.php");

@ini_set('display_errors', 'Off');
@ini_set('allow_url_fopen', 'On'); /// при мультитрекере нельзя отключать

//gzip();
dbconn();
//loggedinorreturn(); 

if ($CURUSER["id"]<>"2")
die;

//sql_query("UPDATE torrents SET moderated = 'yes',moderatordate=".sqlesc(get_date_time()).",moderatedby = ".sqlesc("92")." WHERE moderated = 'no' AND owner = '92'") or sqlerr(__FILE__, __LINE__);

/*
--
-- Структура таблицы `tgrabber`
--

CREATE TABLE IF NOT EXISTS `tgrabber` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `details` int(100) NOT NULL DEFAULT '0',
  `work` int(10) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `one` (`details`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `tgrabber`
--
*/



$beg_page=0;
$end_page=1600;

$id=(int)$_GET["id"];

if (!$id)
$id=0;


//if ($id<$beg_page || $id>$end_page)
//die("конец списка от  $beg_page - $id - до $end_page");


$host = "torrentsmd.com";
$path = "/browse.php?page=$id";

///// куки
$cookies = array();
if (file_exists(ROOT_PATH."cache/parser_session.txt")){
$content=file_get_contents(ROOT_PATH."cache/parser_session.txt"); 
$cookies[] = $content.";"; //
}

/*
$cookies[] = "pass=7f23bd4cb9e1876fa57f253888f1ff17;";
$cookies[] = "uid=188729;";
$cookies[] = "lang=ru;";


$cookies[] = "pass=4bb407ae4253056cec5231752ae7215a;";
$cookies[] = "uid=63887;";
$cookies[] = "lang=ru;";
*/


///Отдано: 309.15 GB, Скачано: 234.96 GB
$cookies[] = "pass=2813d993e17731ecc8c1689d3c03cdaa;";
$cookies[] = "uid=16138;";
$cookies[] = "lang=ru;";




//$bite = 0;
//if (!empty($_POST["biti"]))
$bite = (int) $_GET["bite"];

if ($bite>=2)
die("Сбор удачен, перенаправление на страницу скачивания.<script>setTimeout('document.location.href=\"parser_d.php\"', 1000);</script>");

// Устанавливаем соединение
$fp = fsockopen($host, 80, $errno, $errstr, 30) or die(" ошибка тут ");
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
$headers.= "Referer: http://$host$path \r\n";
$headers.= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.2; ru; rv:1.9.2) Gecko/20100115 Firefox/3.6 WebMoney Advisor\r\n";
$headers.= "Connection: Close\r\n";

if ($cookies) {
$headercookie="";
foreach ($cookies as $cookie)
$headercookie .= $cookie." ";
$headercookie = substr($headercookie, 0, -2);
$headers .= "Cookie: ".$headercookie."\r\n";
}
$headers .= "\r\n";
// Отправляем HTTP-запрос серверу

fwrite($fp, $headers.$data);

$date="";
// Получаем страничку
while (!feof($fp)) {
$line = fgets($fp, 1024);

// В заголовке меня интерисуют только куки
@list($field, $value) = preg_split('/\s*:\s*/', $line, 2);
// Запоминаем найденную куку
if (strtolower($field) === 'set-cookie'){
// Точнее, запоминаем только само значение куки (недекодированное)
$result[] = array_shift(preg_split('/\s*;\s*/', $value, 2));
}

$date.=$line;
}
fclose($fp);

if (count($result) > 0){
$sf =ROOT_PATH."cache/parser_session.txt"; 
unlink($sf);
$fpsf=fopen($sf,"a+"); 
fputs($fpsf,implode("\n",$result)); 
fclose($fpsf);
echo "<script>setTimeout('document.location.href=\"parser_d.php\"', 5000);</script>"; 
die("Обновлен файл сессий");
}

//echo "Открыто соединение <br>";
}
else
{
echo $errno."".$errstr;	
}

//die(($date));
preg_match_all("|href=(details.php.+.*)>|U", $date, $out);  

///var_dump($date);

$array_no=array("href=","=&gt; ","&amp;toseeders=1>","&amp;todlers=1>","&toseeders=1>","&todlers=1>","&amp;todlers=1","&amp;toseeders=1");

$link=array();/// тут собираем все ссылки

foreach ($out as $k) {
foreach ($k as $kfs) {

$kfdsf = str_replace($array_no, '', $kfs);

$link[]=$kfdsf; /// собираем все ссылки
//echo "$kfdsf <br>";
}
}

$link=array_unique($link); /// удаляем дубликаты

$file = 0;

foreach ($link as $kil) {

$array_no=array("details.php?id=","details.php&amp;id=");

$kil = str_replace($array_no, '', $kil);

///sql_query("UPDATE tgrabber SET work='1' WHERE details = ".sqlesc($kil)."") or sqlerr(__FILE__, __LINE__);

sql_query("INSERT INTO tgrabber (details, work) VALUES (".sqlesc($kil).",".sqlesc("1").")");
$newid = mysql_insert_id();
if ($newid){
echo "<font color=green><b>".$kil."</b></font> <b>добавлено</b><br>";
$file = 0;
} else {
echo "<font color=red><i>".$kil."</i></font> <i>уже в базе</i><br>";
$file = 1;
}
}
///var_dump($link);


if (!empty($file))
++$bite;
else
$bite = 0;


echo "<script>setTimeout('document.location.href=\"parser.php?id=".($id+1)."&bite=$bite\"', 10000);</script>";







?>