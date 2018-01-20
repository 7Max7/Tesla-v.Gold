<?
require_once("include/bittorrent.php");
require_once("include/benc.php");

@ini_set('display_errors', 'On');
@set_time_limit(60);

/**
 * парсер написан спец для граббинга с freetorrents.org.ua
 **/

dbconn();
///loggedinorreturn(); 

global $DEFAULTBASEURL,$CURUSER;

$nu=rand(5000,30000);

if ($CURUSER["class"]<>"6")
die;


if (!is_dir(ROOT_PATH."torrents/txt")){
mkdir(ROOT_PATH."torrents/txt", 0777);
echo "Создание папки временного хранения...<br>";
}

$fileacces=ROOT_PATH."torrents/txt/.htaccess";

if (!file_exists($fileacces)){
	
$fp = @fopen($fileacces,"w");
$content='<Files *.txt>
Deny From All
</Files>

<FilesMatch "\.(php|php3|php4|php5|phps|cgi)$">
Deny From All
</FilesMatch> 

php_value engine off';
@fputs($fp, $content);
@fclose($fp);
echo "Создание файла .htaccess в папке временного хранения...<br>";
}

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

function dict_get_t($d, $k, $t) {
	$dd = $d["value"];
	$v = $dd[$k];
	return $v["value"];
}


/*
--
-- Структура таблицы `torg_grabber`
--

CREATE TABLE IF NOT EXISTS `torg_grabber` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `details` varchar(512) NOT NULL default '',
  `work` int(10) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `one` (`details`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `torg_grabber`
--
*/



////////////////////////



if (!empty($_GET["page"])){
	
$page=(int)$_GET["page"];

$host = "freetorrents.org.ua";

$path="/page/$page/";


$referer = "http://freetorrents.org.ua/";

$cookies = array();

$cookies[] = "dle_user_id=39232;";
$cookies[] = "dle_password=cb3ff9839e0f27b17701615d8faa7735;";
$cookies[] = "dle_newpm=0;";
$cookies[] = "module_online=1;";


if (@file_exists("cache/parser_org.uasession.txt")){
$content=@file_get_contents("cache/parser_org.uasession.txt"); 
$cds = explode("\n",$content);
foreach ($cds as $co) {
if (!empty($co))
$cookies[] = $co.";";
}
}

//var_dump($cookies);

// Устанавливаем соединение
$fp = fsockopen($host, 80, $errno, $errstr, 30) or die("Нет соединения соответственно и данных. <script>setTimeout('document.location.href=\"parser_org.ua.php?page=".($page-1)."\"', 4000);</script>"); 
// Проверяем успешность установки соединения
//stream_set_blocking($fp, 0);
//stream_set_timeout($fp, 3600);
socket_set_timeout($fp,5,0); //Назначаем таймаут (100 - это секунды)

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
$headers.= "Referer: ".$referer."details.php?id=".($int-1)."\r\n";
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

while (!feof($fp)) {

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
} else die("Нет данных. <script>setTimeout('document.location.href=\"parser_org.ua.php?page=".($page-1)."\"', 3000);</script>");


if (count($result) > 1){
$sf =ROOT_PATH."cache/parser_org.uasession.txt"; 
@unlink($sf);
$fpsf=fopen($sf,"a+"); 
fputs($fpsf,implode("\n",$result)); 
fclose($fpsf);
echo "<script>setTimeout('document.location.href=\"parser_org.ua.php?page=".($page-1)."', $nu);</script>"; 
die("Обновлен файл сессий для $host");
}

$num_linksadd=0;
$numdead_links=0;
preg_match_all("#<a href=\"(http:\/\/[^\s'\"<>]+.html)\".*?>(.+?)</a>#is",$date,$patterns); 

$patterns[1]=array_unique($patterns[1]); /// удаляем дубликаты ссылок post

foreach ($patterns[1] as $ksf) {
	
if (!stristr($ksf,'statistics.html') && !stristr($ksf,'/faq/') && !stristr($ksf,'/events/') && !stristr($ksf,'print:page') && stristr($ksf,'freetorrents.org.ua')){

$ksf=str_ireplace("http://freetorrents.org.ua/", "", $ksf);

if (stristr($ksf,'/')){
sql_query("INSERT INTO torg_grabber (details, work) VALUES (".sqlesc($ksf).",".sqlesc("1").")");

if (mysql_insert_id())
++$num_linksadd;
else
++$numdead_links;
}
}
}

if (!empty($num_linksadd))
echo "<u>Добавлено новых ссылок</u>: ".$num_linksadd.". Повторы $numdead_links.<br>";


die ("<script>setTimeout('document.location.href=\"parser_org.ua.php?page=".($page-1)."\"', 1000);</script>"); 

}


//die("нельзя пока скачивать");

////////////////////////










///ORDER BY RAND()  ///details
$idnew=(int)$_GET["id"];
if (!empty($idnew))
$arr = sql_query("SELECT * FROM torg_grabber WHERE id=".sqlesc($idnew)." LIMIT 1");
else
$arr = sql_query("SELECT * FROM torg_grabber WHERE work='1' ORDER BY id DESC LIMIT 1");

$row_arr = mysql_fetch_array($arr);

$host = "freetorrents.org.ua";

$path="/".$row_arr["details"];

//$path="/movies/dvdrip/12833-betmen-pod-krasnim-kolpakom-batman-under-the-red-hood-2010-dvdrip.html";

define('MD5XA', md5($host.$path));


if (empty($row_arr["details"]) && empty($idnew))
die("Cсылок в базе нет <script>setTimeout('document.location.href=\"parser_rutorg.php?page=0\"', 3000);</script>");

@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");

$referer = "http://freetorrents.org.ua/";

$cookies = array();

$cookies[] = "dle_user_id=39232;";
$cookies[] = "dle_password=cb3ff9839e0f27b17701615d8faa7735;";
$cookies[] = "dle_newpm=0;";
$cookies[] = "module_online=1;";


if (@file_exists("cache/parser_org.uasession.txt")){
$content=@file_get_contents("cache/parser_org.uasession.txt"); 
$cds = explode("\n",$content);
foreach ($cds as $co) {
if (!empty($co))
$cookies[] = $co.";";
}
}

//var_dump($cookies);

// Устанавливаем соединение
$fp = fsockopen($host, 80, $errno, $errstr, 30) or die("Нет соединения соответственно и данных. <script>setTimeout('document.location.href=\"parser_org.ua.php\"', $nu);</script>"); 
// Проверяем успешность установки соединения
//stream_set_blocking($fp, 0);
//stream_set_timeout($fp, 3600);
socket_set_timeout($fp,5,0); //Назначаем таймаут (100 - это секунды)


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
$headers.= "Referer: ".$referer."details.php?id=".($int-1)."\r\n";
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

while (!feof($fp)) {

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
} else die("Нет данных. <script>setTimeout('document.location.href=\"parser_org.ua.php\"', $nu);</script>");


if (count($result) > 1){
$sf =ROOT_PATH."cache/parser_org.uasession.txt"; 
@unlink($sf);
$fpsf=fopen($sf,"a+"); 
fputs($fpsf,implode("\n",$result)); 
fclose($fpsf);
echo "<script>setTimeout('document.location.href=\"parser_org.ua.php\"', $nu);</script>"; 
die("Обновлен файл сессий для $host");
}

$num_linksadd=0;
preg_match_all("#<a href=\"(http:\/\/[^\s'\"<>]+.html)\".*?>(.+?)</a>#is",$date,$patterns); 

foreach ($patterns[1] as $ksf) {
	
if (!stristr($ksf,'statistics.html') && !stristr($ksf,'/faq/') && !stristr($ksf,'/events/') && !stristr($ksf,'print:page') && stristr($ksf,'freetorrents.org.ua')){

$ksf=str_ireplace("http://freetorrents.org.ua/", "", $ksf);

if (stristr($ksf,'/'))
sql_query("INSERT INTO torg_grabber (details, work) VALUES (".sqlesc($ksf).",".sqlesc("1").")");

if (mysql_insert_id())
++$num_linksadd;

}
}

if (!empty($num_linksadd))
echo "<u>Добавлено новых ссылок</u>: ".$num_linksadd.".<br>";

if (!empty($idnew))
die ("<script>setTimeout('document.location.href=\"parser_org.ua.php?id=".($idnew+1)."\"', 1000);</script>"); 

preg_match('/<span[^>]+class=\"newstitle\">(.*?)<\/span\>/is', $date, $dtitle);
//print_r($dtitle);
$name=strip_tags($dtitle[1]); // название проверено
//echo $name;
//die;

preg_match('/<table[^>]+class=\"noname2\">(.*?)<\/table\>/is', $date, $dese);
//print_r($dese);
$desc=$dese[0]; /// название проверено





///$desc= preg_replace('/<div align="center">(.*?)<\/div\>/is', "[center]\\1[/center]", $desc);
// битый код тут
$desc= preg_replace('/<font[^>]+face="([^>]+)">(.*?)<\/font\>/is', "\\2", $desc);
// если имеется шрифт
$desc= preg_replace('/<font[^>]+size="([^>]+)">(.*?)<\/font\>/is', "\\2", $desc);
// если имеется размер
$desc= preg_replace('/<font color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])>(.*?)<\/font\>/is', "\\2", $desc);
// если имеется цвет


list ($o,$p) = explode("<hr",$desc);

$desc=$o;


preg_match('/<div[^>]+class=\"news-1\">(.*?)<\/div>/is', $date, $dacat);

//var_dump($dacat);
$dacatanew=$dacat[1];
$categoryname=$dacatanew; // название проверено


preg_match("/<a href=\"[^>]+download.php?+(.*?)\" >/is", $date, $li);  

$lnk_torrent=$li[1]; /// проверено на одном торренте

//echo $lnk_torrent; ///?id=9174
/// полностью http://freetorrents.org.ua/engine/download.php?id=9174
if (empty($lnk_torrent)){
sql_query("UPDATE torg_grabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
echo "<script>setTimeout('document.location.href=\"parser_org.ua.php\"', 1000);</script>";
echo "Не найдена ссылка на торрент файл";
die;
}


///////////////////////////
$ret = mysql_query("SHOW TABLE STATUS LIKE 'torrents'"); 
$row = mysql_fetch_array($ret); 
$next_id = $row['Auto_increment']; 

@unlink(ROOT_PATH."torrents/".$next_id.".torrent");


$infohash=get_torrent($next_id,$lnk_torrent,$cookies,$row_arr["id"],$path);

if ($infohash==false){
	
sql_query("UPDATE torg_grabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_org.ua.php\"', 10000);</script>";
die("Невозможно прочитать внешний .torrent файл. $infohash");
}

//////////////////////////// авторские раздачи - запрет заливки
$num_license = get_row_count("license","WHERE info_hash=".sqlesc($infohash));

if (!empty($num_license)){
sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_org.ua.php\"', 10000);</script>";
echo("Данный файл имеет авторские права, запрещено заливать подобные файлы.");
die;
}
//////////////////////////// авторские раздачи - запрет заливки

if (stristr($name,'DVDRip') || stristr($name,'TS') || stristr($categoryname,'Фильмы') || stristr($name,'HDTVRip') || stristr($name,'BDRip'))
$catid="13"; /// Фильмы / AVI

elseif (stristr($categoryname,'Документальный'))
$catid="22"; /// TV / Документалки 

elseif (stristr($categoryname,'SATRip') || stristr($categoryname,'TVRip'))
$catid="11"; /// Сериалы

elseif (stristr($name,'HDTV') || stristr($name,'Blu-Ray') || stristr($name,'BDRemux'))
$catid="14"; /// Фильмы / HDTV / HD / Blu-Ray

elseif (stristr($categoryname,'Музыка'))
$catid="10"; /// Музыка / Мультимедиа 

elseif (stristr($categoryname,'игры'))
$catid="5"; /// Игры / ПК

elseif (stristr($categoryname,'Аниме'))
$catid="12"; /// Аниме 

elseif (stristr($categoryname,'Оформление') || stristr($name,'PSD') || stristr($name,'JPG'))
$catid="25"; /// Картинки / Фото

elseif (stristr($categoryname,'Программы'))
$catid="26"; /// Софт / Windows

elseif (stristr($categoryname,'Linux'))
$catid="24"; /// Софт / Unix / Linux (0) Показать тэги

elseif (stristr($categoryname,'Мобилка'))
$catid="27"; /// PDA / Phone / Android / Palm

elseif (stristr($categoryname,'DVD5') || stristr($categoryname,'DVD9'))
$catid="15"; /// DVD / Фильмы

elseif (stristr($categoryname,'Клипы') || stristr($categoryname,'Концерты'))
$catid="18"; ///  Клипы / Ролики / Приколы (0) Показать тэги 	

else
$catid="4";




$araw = array("jpg.html","gif.html","bmp.html","png.html","jpeg.html");
$aran = array("jpg","gif","bmp","png","jpeg");

$desc = trim(str_replace($araw, $aran, $desc));

////////////

preg_match('/<div[^>]+align=\"center\">(.*?)<\/div>/is', $date, $dimag);
preg_match('/(<img)\s (src="([a-zA-Z0-9\.;:\/\?&=_|\r|\n]{1,})")/isxmU', $dimag[1], $image);  


//var_dump($image);

//die;
$end_image=$image[3];

$uploaddir = ROOT_PATH."torrents/images/";

if (!empty($end_image)){

$image=@file_get_contents($end_image);
list($width_in, $height_in) = getimagesize($end_image);
}

if (empty($width_in) || empty($height_in)){
$image = false;
$ifileimg = "";
}

if (!empty($image)) {
$ifileimg = $next_id.'.'.end(explode('.',$end_image));

if (!copy($end_image, $uploaddir.$ifileimg)){
@unlink($uploaddir.$ifileimg); /// удаляем если такой же есть
echo "Удаляем картинку она не может скопироваться в папку. <br>";
}

} else
{
	if (filesize($uploaddir.$ifileimg)==0)
	{
		@unlink($uploaddir.$ifileimg); /// удаляем если такой же есть
		echo "Удаляем картинку она пустая. <br>";
	}
}
///////////


$dacatanew=preg_replace('/<(a.*) href=\"(.*?)\">(.*)<\/a>/',"\\3", $dimag[1]);



preg_match_all("/<img.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>/i", $desc, $ddc);
$link_image=array();/// тут собираем все ссылки
$xpio = 1;
foreach ($ddc[1] as $ksf) {
	
	
if ($xpio<5){
//stristr($ksf,"radikal") && 
if (list($width, $height) = @getimagesize($ksf)){

if ($width>=300 && $height>=150 && $width>=$height){
$updateset[] = "picture$xpio = ".sqlesc($ksf);
echo "Добавлен $width и $height - $ksf<br>";
++$xpio;
}
else
{

if ($height>=300 && $width>=150 && $height>=$width){
$newlin = $ksf;
echo "Проверка $width и $height - как на постер<br>";
}


echo "$width и $height - $ksf не подходит!<br>";	
}
}
/*
361 и 500 не подходит!
*/


$link_image[]=$ksf; /// собираем все ссылки
}
}

if (empty($image) && !empty($newlin)) {
$ifileimg = $next_id.'.'.end(explode('.',$newlin));

if (!copy($newlin, $uploaddir.$ifileimg)){
@unlink($uploaddir.$ifileimg); /// удаляем если такой же есть
echo "Удаляем картинку она не может скопироваться в папку. <br>";
}

}



if (empty($desc) || empty($name)){

sql_query("UPDATE torg_grabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
echo "<script>setTimeout('document.location.href=\"parser_org.ua.php\"', $nu);</script>";
unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
die("пустое название или описание");
}
///////////////////////////

if (stristr($name,'camrip') && !stristr($categoryname,'Документальный')) {

sql_query("UPDATE torg_grabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
echo "<script>setTimeout('document.location.href=\"parser_org.ua.php\"', $nu);</script>";
unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
die("camrip!!! ");
}


$name = str_replace("by GENADIY", "", $name);

$name = trim(str_replace("от FreeTorrents-UA", "", $name));


$array=array("<b>","<i>","</b>","</i>","<u>","</u>","<br>","<br><br>","<br />");
$arrna=array("[b]","[i]","[/b]","[/i]","[u]","[/u]","\n","\n","\n");

$desc = str_ireplace($array, $arrna, $desc);

//$desc = str_replace("[b]".$name."[/b]", "", $desc);


$desc= preg_replace("/<a.*?href=\"(http:\/\/[^\s'\"<>]+(\.(gif|jpg|jpeg|png)))\">(.+?)<\/a>/is", "[img]\\1[/img]", $desc);
$desc= preg_replace("/<img.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>/i", "[img]\\1[/img]\n", $desc);


$desc = trim(strip_tags($desc));

//$desc = preg_replace('/<!--(.*?)-->/is', "", $desc);  

//echo format_comment($desc);

//die;

$array2=array("\r","] :",".Скриншоты:","Скриншоты:[img]","\n\n\n","[b]Описание:[/b]\n","\n [img]","[b]Формат:[/b]","[b]Перевод: [/b]","[b]В ролях:[/b]","\n[b]Семпл[/b]\n","[u]Файл[/u]","Ссылка для стороннего скачивания:","[img]http://filestore.com.ua/dl.gif[/img]","[b]File[/b]:","[b]Filesize[/b]: ","[b]Play length[/b]:","[b]Subtitles[/b]: Not Present","[b]Video[/b]:","[b]Audio[/b]:","[b]Релиз от: [/b]by genadiy");

$arrna2=array("\n","]: ",". \n[b]Скриншоты[/b]: \n","[b]Скриншоты[/b]: \n[img]","\n","\n[b]Описание:[/b] ","\n[img]","\n[b]Формат:[/b]","\n[b]Перевод:[/b] ","\n[b]В ролях:[/b]","\n","\n[u]Файл[/u]","","","[u]Файл[/u]:","[b]Размер[/b]: ","[b]Время воспроизведения[/b]:","[b]Субтитры[/b]: нет","[b]Видео[/b]:","[b]Звук[/b]:","");

if (!empty($image))
$desc = str_ireplace("[img]".$end_image."[/img]", "", $desc);

$desc = trim(str_replace($array2, $arrna2, $desc));




if (stristr($categoryname,'игры'))
$desc = str_replace("Системные требования:", "\n[u]Системные требования:[/u]", $desc);


$array3 = array("Финансовая помощь релизеру genadiy","WebMoney Доллар: Z725410223226","WebMoney Гривна: U323397204600","WebMoney Рубль: R823812942900","Заранее спасибо!","[b]IMDb[/b] / [b]КиноПоиск[/b]","[b]WebMoney Доллар[/b]: Z725410223226","[b]WebMoney Гривна[/b]: U323397204600","[b]WebMoney Рубль[/b]: R823812942900");




$desc = trim(str_replace($array3, "", $desc));

$desc = trim(str_replace("Скриншоты[img]", "\n[img]", $desc));



if (count($link_image)){
$imagess=implode("\n", $link_image);
$imagess="\n\n[spoiler=[i]Возможно это скриншоты[/i]]\n".$imagess."\n[/spoiler]";

$desc=$desc.$imagess;
}



$visi="yes";

$multut=sqlesc("yes");

$torrent_com = get_date_time() . " граббер торрентов II ($host".$path.").\n";

$descr=htmlspecialchars_uni($desc);
$torrent=htmlspecialchars_uni($name);

//$fname="Tesla_id$next_id";

if (!empty($int))
$own=$CURUSER["id"];
else
$own="92";

///var_dump($desc);
$ret = sql_query("INSERT INTO torrents (owner, visible, image1, info_hash, name, descr, torrent_com, category, added, last_action, multitracker) VALUES (" . implode(",", array_map("sqlesc", array($own, $visi, $ifileimg, $infohash, $torrent, $descr, $torrent_com, $catid))) . ", '" . get_date_time() . "', '" . get_date_time() . "',".$multut.")"); 

$id = mysql_insert_id();

if ($id){
//echo "Присваиваем идентификатор $torrent ($id) <br>";
$copy=copy(ROOT_PATH."torrents/txt/".MD5XA.".torrent",ROOT_PATH."torrents/$id.torrent");

if (!$copy)
echo("Не могу скопировать торрент в папку");
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
} else {

	
$ar = sql_query("SELECT moderated,moderatedby,owner,descr,id FROM torrents WHERE info_hash=".sqlesc($infohash));
$rowr = mysql_fetch_array($ar);

if ($rowr["moderated"]=="yes" && $rowr["moderatedby"]=="92" && $rowr["owner"]=="92" && $rowr["descr"]<>$descr && $empty){

@unlink(ROOT_PATH."torrents/images/".$ifileimg);

if (!empty($image)) {

if (!empty($rowr["image1"]))
@unlink(ROOT_PATH."torrents/images/".$rowr["image1"]);

$ifileimg = $rowr["id"].'.'.end(explode('.',$end_image));
copy($end_image, $uploaddir.$ifileimg);
$updateset[] = "image1 = ".sqlesc($ifileimg);
}
else
@unlink(ROOT_PATH."torrents/images/".$ifileimg);

$updateset[] = "name = ".sqlesc($torrent);
$updateset[] = "descr = ".sqlesc($descr);


sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = ".$rowr["id"]."") or sqlerr(__FILE__, __LINE__);

echo "<br>Обновлен более старый торрент новым - Успешно <a href=\"details.php?id=".$rowr["id"]."\">$torrent</a>. <br>";

}


//die(MD5XA);
unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");

//sql_query("DELETE FROM files WHERE torrent = '$next_id'");

sql_query("UPDATE torg_grabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_org.ua.php\"', $nu);</script>";
die("ошибка возможно торрент уже есть в базе ".(empty($id)? " <b>Пустое ID с БАЗЫ (при вставке)</b>":"")."");
}

//die(MD5XA);
$file_url=ROOT_PATH."torrents/".$id.".torrent";

if(@file_exists($file_url)){

$dict = bdec_file($file_url, 1024000);
list($info) = dict_check_t($dict, "info");
list($dname, $plen, $pieces) = @dict_check_t($info, "name(string):piece length(integer):pieces(string)");

$filelist = array();
$totallen = @dict_get_t($info, "length", "integer");
if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
} else {
	$flist = @dict_get_t($info, "files", "list");
	$totallen = 0;
	
	if (count($flist)){
	
	foreach ($flist as $file_url) {
		list($ll, $ff) = @dict_check_t($file_url, "length(integer):path(list)");
		$totallen += $ll;
		$ffa = array();
		foreach ($ff as $ffe) {

			$ffa[] = $ffe["value"];
		}
		$ffe = implode("/", $ffa);
		$filelist[] = array($ffe, $ll);
	}
	
	}
}

$dict=@bdec(@benc($dict)); 
@list($info) = @dict_check_t($dict, "info");
$infohash = sha1($info["string"]);
$size=0;
if (!empty($totallen)){
//sql_query("DELETE FROM files WHERE torrent = '$id'");
foreach ($filelist as $file) {
$file[0]=utf8_to_win($file[0]);
$size=$size+$file[1];
//echo $file[1]."<br>";
}
}

$updateset[] = "numfiles = " . sqlesc(count($filelist));
$updateset[] = "size = " . sqlesc($size);

$updateset[] = "moderated = 'yes'";
$updateset[] = "moderatedby = ".sqlesc("92");
$updateset[] = "moderatordate = ".sqlesc(get_date_time());

///$updateset[] = "info_hash = " . sqlesc($infohash);
sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = $id") or sqlerr(__FILE__, __LINE__);
echo "<title>$torrent</title>Успешно залит и обновлен - <a href=\"details.php?id=$id\">$torrent</a> с ".$row_arr["id"].".";

sql_query("UPDATE torg_grabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
}



function get_torrent($id,$path,$cookies,$row_id,$path_orig) {

$nu=rand(12000,100000);

$path="/engine/download.php".$path; // проверено

$host = "freetorrents.org.ua";

/// полностью http://freetorrents.org.ua/engine/download.php?id=9174

if (!$id){
echo "<script>setTimeout('document.location.href=\"parser_org.ua.php\"', $nu);</script>";
die("не id для get_torrent");
}

$referer = "http://freetorrents.org.ua/".$path_orig; /// добавить как нада
//die($path_orig);



// Устанавливаем соединение
//echo "Устанавливаем соединение c .torrent файлом - $path<br>";
$fp = fsockopen($host, 80, $errno, $errstr, 30);
socket_set_timeout($fp,5,0); //Назначаем таймаут (100 - это секунды)

// Проверяем успешность установки соединения

if ($fp) {
// Данные HTTP-запроса
$data = "";
if ($form_vars) {
foreach ($form_vars as $name => $var)
$data .= urlencode($name)."=".urlencode($var)."&";
//$data = substr($data, 0, -1);
$data .= "\r\n\r\n";
}
// Заголовок HTTP-запроса
$headers = "GET $path HTTP/1.0\r\n";
$headers .= "Host: $host\r\n";
$headers .= "Referer: $referer\r\n";
$headers .= "Connection: Close\r\n";
if ($cookies) {
foreach ($cookies as $cookie)
$headercookie .= $cookie." ";
$headercookie = substr($headercookie, 0, -2);
$headers .= "Cookie: ".$headercookie."\r\n";
}
$headers .= "\r\n";

///echo "Отправка данных <br>";
fwrite($fp, $headers.$data);

while (!feof($fp)) {
$line = fgets(($fp), 10024);

$date.=$line;
}
fclose($fp);
}



if (strlen($date)>="724148" || empty($date))
{

//if (!empty($next_id))
//unlink(ROOT_PATH."torrents/".$next_id.".torrent");

sql_query("DELETE FROM files WHERE torrent = '$next_id'");
sql_query("UPDATE torg_grabber SET work='0' WHERE id = '".$row_id."'") or sqlerr(__FILE__, __LINE__);


echo "<script>setTimeout('document.location.href=\"parser_org.ua.php\"', $nu);</script>";
unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
die("ошибка размер файла торрент более 700 КБ, не разрешенно.");
}


///if (stristr($date,'Sorry'))
///die("Возможно учетная запись заблокирована.$date");

//die($date);
//echo "Получение, обработка данных<br>";
list ($data_1,$data_2) = explode("\r\n\r\n", $date);

//die(MD5XA);

$ifilename = ROOT_PATH."torrents/txt/".MD5XA.".torrent";

$sf=$ifilename; 
$fpsf=fopen($sf,"a+"); 
//echo ($data_2);
//die($data_2);
fputs($fpsf,$data_2); 
fclose($fpsf); 
//echo "Сохраняем .torrent файл <br>";


if(@file_exists($ifilename)){

$dict = bdec_file($sf, 1024000);
list($info) = dict_check_t($dict, "info");
list($dname, $plen, $pieces) = @dict_check_t($info, "name(string):piece length(integer):pieces(string)");

$filelist = array();
$totallen = @dict_get_t($info, "length", "integer");
if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
} else {
	$flist = @dict_get_t($info, "files", "list");
	$totallen = 0;
	
	if (count($flist)){
	
	foreach ($flist as $sf) {
		list($ll, $ff) = @dict_check_t($sf, "length(integer):path(list)");
		$totallen += $ll;
		$ffa = array();
		foreach ($ff as $ffe) {
			$ffa[] = $ffe["value"];
		}
		$ffe = implode("/", $ffa);
		$filelist[] = array($ffe, $ll);
	}
	}
}

$dict=@bdec(@benc($dict)); 
@list($info) = @dict_check_t($dict, "info");
$infohash = sha1($info["string"]);
//die($infohash);
$size=0;
if (!empty($totallen)){
//sql_query("DELETE FROM files WHERE torrent = '$id'");
foreach ($filelist as $file) {
$file[0]=utf8_to_win($file[0]);
//echo $file[0]."<br>";
//echo $file[1]."<br>";
$size=$size+$file[1];

sql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).",".sqlesc($file[1]).")") or sqlerr(__FILE__, __LINE__);

}
echo "Обновление файлов внутри торрента успешно.<br>";
}
else
{

///if (!empty($id))
unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
sql_query("DELETE FROM torrents WHERE torrent = '$id'");

sql_query("UPDATE torg_grabber SET work='0' WHERE id = '".$row_id."'") or sqlerr(__FILE__, __LINE__);


echo "<script>setTimeout('document.location.href=\"parser_org.ua.php\"', 5000);</script>";
die("ошибка размер файла 0 размер.");
}
//die(MD5XA);

//echo "Заносим в базу все необходимые данные <br>";
}

if (empty($infohash))
$infohash = false;

return $infohash;
}

@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");

$seconds = (timer() - $tstart);
$phptime = 	$seconds - $querytime;
$query_time = $querytime;
$percentphp = number_format(($phptime/$seconds) * 100, 2);
$percentsql = number_format(($query_time/$seconds) * 100, 2);
$seconds = 	substr($seconds, 0, 8);
$memory = round(memory_get_usage()/1024);
$time_sql=sprintf("%0.4lf",$querytime);


echo "<br>Время: <b>$seconds</b> секунд - <b>$queries</b> (queries) - <b>$percentphp%</b> (php) - <b>$percentsql%</b> ($time_sql => sql) - $memory КБ (use memory) <br>";

$arr = sql_query("SELECT COUNT(*) AS numgrab,(SELECT COUNT(*) AS numgrab FROM torg_grabber WHERE work='0') AS grabost,(SELECT COUNT(*) FROM torrents) AS numtore FROM torg_grabber WHERE work='1'");
$row_arr = mysql_fetch_array($arr);
///$procents="<b title=\" торрентов залито - ".number_format($row_arr["numtore"])."\">Завершенно</b>: ".number_format(100-number_format(100 * (1 - ($row_arr["grabost"] / ($row_arr["grabost"]+$row_arr["numgrab"]))),2),2)."% (<b>ВЗ</b>:".number_format($row_arr["grabost"]).":<b>ОСТ</b>:".number_format($row_arr["numgrab"]).":<b>ВС</b>:".number_format($row_arr["grabost"] + $row_arr["numgrab"]).") Отфильтровано и удалено: ".number_format($row_arr["grabost"] - $row_arr["numtore"])." штук";

$procents="<b title=\" торрентов залито - ".number_format($row_arr["numtore"])."\"><a href=parser_org.ua.php>Завершенно</a></b>: ".number_format(100-number_format(100 * (1 - ($row_arr["grabost"] / ($row_arr["grabost"]+$row_arr["numgrab"]))),2),2)."% (<b>ВЗ</b>:".number_format($row_arr["grabost"]).":<b>ОСТ</b>:".number_format($row_arr["numgrab"]).":<b>ВС</b>:".number_format($row_arr["grabost"] + $row_arr["numgrab"]).") Отфильтровано и удалено: ".number_format($row_arr["grabost"] - $row_arr["numtore"])." штук";

if (empty($int))
echo "$procents<script>setTimeout('document.location.href=\"parser_org.ua.php\"', $nu);</script>";
//else 
//echo "$procents<script>setTimeout('document.location.href=\"premod.php\"', $nu);</script>";



?>