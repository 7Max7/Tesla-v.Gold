<?
require_once("include/bittorrent.php");
require_once("include/benc.php");

@ini_set('display_errors', 'On');
@set_time_limit(60);




dbconn();
///loggedinorreturn(); 

global $DEFAULTBASEURL,$CURUSER;

$nu=rand(5000,25000);




//die("<title>$nu</title><a href=parser.php>клонировать</a><script>setTimeout('document.location.href=\"fixsparser.php\"', $nu);</script>"); 



if ($CURUSER["class"]<>"6")
die("Нет доступа.");



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

///ORDER BY RAND()  ///details

$arr = sql_query("SELECT * FROM tgrabber WHERE work='1' ORDER BY rand() DESC LIMIT 1");
$row_arr = mysql_fetch_array($arr);

$host = "torrentsmd.com";

$int=(int)$_GET["id"];

if (!empty($int))
$row_arr["details"]=$int;



$path="/details.php?id=".$row_arr["details"];

define('MD5XA', md5($host.$path));

if (empty($row_arr["details"]) && empty($int))
die("Cсылок в базе нет <a href=\"parser.php\">Обновить список внешних файлов</a>? <script>setTimeout('document.location.href=\"parser_t.php\"', 3000);</script>");

@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");

$referer = "http://torrentsmd.com/";

$cookies = array();

///Отдано: 309.15 GB, Скачано: 234.96 GB
$cookies[] = "pass=2813d993e17731ecc8c1689d3c03cdaa;";
$cookies[] = "uid=16138;";
$cookies[] = "lang=ru;";




if (@file_exists("cache/parser_session.txt")){
$content=@file_get_contents("cache/parser_session.txt"); 
$cds = explode("\n",$content);
foreach ($cds as $co) {
if (!empty($co))
$cookies[] = $co.";";
}
}

//var_dump($cookies);

// Устанавливаем соединение
$fp = fsockopen($host, 80, $errno, $errstr, 30) or die("Нет соединения соответственно и данных. <script>setTimeout('document.location.href=\"parser_d.php\"', $nu);</script>"); 
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
} else die("Нет соединения соответственно и данных. <script>setTimeout('document.location.href=\"parser_d.php\"', $nu);</script>");

if (count($result) > 0){
$sf =ROOT_PATH."cache/parser_session.txt"; 
unlink($sf);
$fpsf=fopen($sf,"a+"); 
fputs($fpsf,implode("\n",$result)); 
fclose($fpsf);
echo "<script>setTimeout('document.location.href=\"parser_d.php\"', $nu);</script>"; 
die("Обновлен файл сессий");
}


preg_match('#&nbsp;&nbsp;<b>(.*?)</b>,#is', $date, $dnik);

if (empty($dnik[1]))
die("Пожалуйста проверьте куки, не найден ник для авторизации.");
else
echo "Логин в системе:".$dnik[1]."<br>";

preg_match_all('#<h1>(.*) <a href=#Uis', $date, $dtitle);

$name=utf8_to_win($dtitle[1][0]);

///print_r($desk);

preg_match("|(download.php.*)\">|U", $date, $li);  
$lnk_torrent=$li[1];

preg_match('/<td[^>]+style="([^>]+)">(.*?)<\/td\>/is', $date, $dese);

$desc=utf8_to_win($dese[0]);

if (stristr($desc,'Fiier') && stristr($desc,'Descriere') && !stristr($date,'Профиль') && !stristr($date,'Слежение'))
die("Молдавский язык!!! смените язык показа на ориг сайте. <hr>$date<script>setTimeout('document.location.href=\"parser_d.php\"', 550000);</script>");



/////////////// временное правило фильтрация по названию контента ///////////////
if (stristr($name,'Blu-Ray') || stristr($name,'HDTVRip') || stristr($name,'BDRip') || stristr($name,'BDRemux') ||
stristr($name,'720p') || stristr($name,'1080p') || (strstr($name,'DVD') && !stristr($name,'DVDrip')) || stristr($name,'mp3') || stristr($name,'FLAC') || stristr($name,'APE')){
	
echo "Фильтрация контента: Включена.<br>";

} else {


if (empty($int)){

sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_d.php\"', 3000);</script>";
die("Фильтрация контента запретила скачивать данный релиз, перенаправление на другой файл.... <br> $name");
}

}
/////////////// временное правило фильтрация по названию контента ///////////////






///////////////////////////
$ret = mysql_query("SHOW TABLE STATUS LIKE 'torrents'"); 
$row = mysql_fetch_array($ret); 
$next_id = $row['Auto_increment']; 

//@unlink(ROOT_PATH."torrents/".$next_id.".torrent");

$infohash=get_torrent($next_id,$lnk_torrent,$cookies,$row_arr["id"]);

if ($infohash==false){
	
sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_d.php\"', 10000);</script>";
die("Невозможно прочитать внешний .torrent файл. $infohash");
}

//////////////////////////// авторские раздачи - запрет заливки
$num_license = get_row_count("license","WHERE info_hash=".sqlesc($infohash));

if (!empty($num_license)){
sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_d.php\"', 10000);</script>";
echo("Данный файл имеет авторские права, запрещено заливать подобные файлы.");
die;
}
//////////////////////////// авторские раздачи - запрет заливки


if (stristr($name,'DVDRip') || stristr($name,'BDRip') || stristr($name,'HDTVRip'))
$catid="13"; /// Фильмы / AVI

elseif (stristr($name,'TVRip') || stristr($name,'CAM'))
$catid="22"; /// TV / Документалки 

elseif (stristr($name,'SATRip') || stristr($name,'[NewStudio]') || stristr($name,'[Kvadrat]') || stristr($name,'[LostFilm]') || stristr($name,'[NovaFilm]') || stristr($name,'[Studdio]') || stristr($name,'season') || stristr($name,'episode'))
$catid="11"; /// Сериалы

elseif (stristr($name,'HDTV') || stristr($name,'Blu-Ray') || stristr($name,'BDRemux') || stristr($name,'HDTVRip') || stristr($name,'BDRip') || stristr($name,'BDRemux') || stristr($name,'720p') || stristr($name,'1080p'))
$catid="14"; /// Фильмы / HDTV / HD / Blu-Ray

elseif (stristr($name,'/ MP3 /') || stristr($name,'FLAC') || stristr($name,'[Dance]')|| stristr($name,'[Soundtrack]') || stristr($name,'MP3') || stristr($name,'VA - ') || stristr($name,' WMA') || stristr($name,'(VBR)')|| stristr($name,' MP3')|| stristr($name,'(CBR)'))
$catid="10"; /// Музыка / Мультимедиа 

elseif (stristr($name,'FPS') || stristr($name,'RPG')  || stristr($name,'Новый Диск') || stristr($name,'Shooters')   || stristr($name,'Акелла') || stristr($name,'Fighting Games') || stristr($name,'Repack') || stristr($name,'Racing')
|| stristr($name,'RTS'))
$catid="5"; /// Игры / ПК

elseif (stristr($name,'Live Action') || stristr($name,'OVA') || stristr($name,'Manga') || stristr($name,'TV Anime') || stristr($name,'[AniDUB])'))
$catid="12"; /// Аниме 

elseif (stristr($name,'HQ') || stristr($name,'Photos') || stristr($name,'pictures') || stristr($name,'обои') || stristr($name,'Wallpapers') || stristr($name,'PSD') || stristr($name,'JPG')|| stristr($name,'обои'))
$catid="25"; /// Картинки / Фото

elseif (stristr($name,'Freeware') || stristr($name,'Shareware'))
$catid="26"; /// Софт / Windows

elseif (stristr($name,'[PSP]') || stristr($name,'[PS2]') || stristr($name,'[PS3]') || stristr($name,'PSX-PSP') || stristr($name,'(PSP)') || stristr($name,'Xbox 360'))
$catid="8"; /// PSP

elseif (stristr($name,'Mobiles') || stristr($name,'JAVA') || stristr($name,'iPhone'))
$catid="27"; /// PDA / Phone / Android / Palm

elseif (stristr($name,'DVD'))
$catid="15"; /// DVD / Фильмы

else
$catid="4";




////////////////////
$araw = array("jpg.html","gif.html","bmp.html","png.html","jpeg.html");
$aran = array("jpg","gif","bmp","png","jpeg");

$desc = trim(str_replace($araw, $aran, $desc));


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
//echo "$width и $height - $ksf не подходит!<br>";	
}
}
/*
361 и 500 не подходит!
*/


$link_image[]=$ksf; /// собираем все ссылки
}
}
////////////////////


$desc = preg_replace('/<(a.*) href=\"(.*?)\">(.*)<\/a>/', "\\3", $desc);
/// для ссылок внутренних

//$desc = preg_replace('/<div class="sp-wrap">(.*?)<\/div><br>/is', "[spoiler]\\1[/spoiler]", $desc);


///http://localhost/parser_d.php?id=873605



$desc = preg_replace('/<div align="center">(.*?)<\/div\>/is', "\\1", $desc);


$desc = preg_replace('/<font[^>]face="([^>]+)">(.*?)<\/font\>/is', "\\2", $desc);
// если имеется шрифт
$desc = preg_replace('/<font[^>]size="([^>]+)">(.*?)<\/font\>/is', "\\2", $desc);
// если имеется размер


$desc = preg_replace('/<font[^>]style="([^>]+)">(.*?)<\/font\>/is', "\\2", $desc);
// если имеется размер


$desc = str_replace("<b></b>", "", $desc);
$desc = preg_replace('/<font color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])>(.*?)<\/font\>/is', "\\2", $desc);


$desc = preg_replace('/<div class="sp-wrap"><div class="sp-head folded clickable">(.*?)<\/div><div class="sp-body">(.*?)<\/div>/is', "\n[spoiler=\\1]\\2\n[/spoiler]", $desc);
/// для спойлера

$desc = str_replace("</div>", "", $desc);
$desc = str_replace("<div>", "", $desc);


// если имеется цвет

$desc= preg_replace("#<a.*?href=\"(http:\/\/[^\s'\"<>]+(\.(gif|jpg|jpeg|png)))\">(.+?)<\/a>#is", "[img]\\1[/img]", $desc);

$desc= preg_replace("/<embed.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>([^<]*?)<[^>]*?>/i", "[video=\\1]", $desc);





$desc = preg_replace("/<a.*?href=\"(http:\/\/[^()<>\s]+?(\.(gif|jpg|jpeg|png)))\"[^>]*?>(.*?)<\/a>/i", "[img]\\1[/img]\n", $desc);

$desc = preg_replace("/<a.*?href=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>(.*?)<\/a>/i", "[url=\\1]\\2[/url]\n", $desc);


$desc = preg_replace("/<img.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>/i", "[img]\\1[/img]\n", $desc);

$desc = preg_replace("/<var.*?title=\"(http:\/\/[^()<>\s]+?)\"[^>]*?><\/var>/i", "[img]\\1[/img]\n", $desc);






$desc= preg_replace("/<img.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>/i", "[img]\\1[/img]\n", $desc);

$desc= preg_replace("/<var.*?title=\"(http:\/\/[^()<>\s]+?)\"[^>]*?><\/var>/i", "[img]\\1[/img]\n", $desc);




$desc= preg_replace("/<var.*?title=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>([^<]*?)<[^>]*?>/i", "[img]\\1[/img]\n", $desc);


//die($desc);

preg_match_all("/<img.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>/i", $desc, $ddc);
$link_image=array();/// тут собираем все ссылки
foreach ($ddc[1] as $ksf) {
$link_image[]=$ksf; /// собираем все ссылки
}

//die($desc);


if (empty($desc) || empty($name) || stristr($name,'rom’na') || stristr($name,'Rom’nі') || stristr($name,'') || stristr($name,'') || stristr($name,'')
|| stristr($name,'[ITA]')
|| stristr($name,'[Subtitle]')
|| stristr($name,'(ITA)')
|| stristr($name,'(India)')
|| stristr($name,'[India]')
){

sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
echo "<script>setTimeout('document.location.href=\"parser_d.php\"', $nu);</script>";
unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
die("пустое название или описание или в названии есть не читаемые символы");
}


if (stristr($desc,'[b]Язык[/b] : Romn') || stristr($desc,'[b]Язык[/b]: Romn') || stristr($desc,'[b]Язык[/b]: Italiano') || stristr($desc,'[b]Язык[/b]: ITA')  || stristr($desc,'[b]Язык[/b]: Espaol')){

sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
echo "<script>setTimeout('document.location.href=\"parser_d.php\"', $nu);</script>";
unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
die("это молдавский релиз, описание и название не русское! (( ");
}


if (
(stristr($desc,'[b]Язык[/b] : English') || stristr($desc,'[b]Язык[/b]: English')) && (stristr($desc,'[b]Перевод[/b]') && stristr($desc,'Оригинал') && stristr($desc,'(без перевода)')) && ($catid == 15 || $catid == 12 || $catid == 14 || $catid == 22 || $catid == 13)
) {

sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
echo "<script>setTimeout('document.location.href=\"parser_d.php\"', $nu);</script>";
unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
die("это чистый английский релиз (без перевода), описание и название не русское! (( ");
}



///////////////////////////



////////////
preg_match("#<img src=\"./torrents_img/(.*?)\">#si", $desc, $image);  

$end_image=$image[1];
$image0=$referer."torrents_img/".$end_image;
$uploaddir = ROOT_PATH."torrents/images/";

if (!empty($end_image))
$image=file_get_contents($image0);

if (!empty($image)) {
$ifileimg = $next_id.'.'.end(explode('.',$end_image));
@unlink($uploaddir.$ifileimg);
copy($image0, $uploaddir.$ifileimg);
}
///////////



$array=array("<b>","<i>","</b>","</i>","<u>","</u>","<br>","<br />");
$arrna=array("[b]","[i] ","[/b] ","[/i]","[u]","[/u]","\n","\n");

$desc = str_ireplace($array, $arrna, $desc);

$desc = str_replace("[b][/b]", "", $desc);

$desc = trim(strip_tags($desc));

$array2=array(
"\nФайл\n",
"[spoiler=]",
"\n\n",
"\r",
"[b]Trailer[/b] :",
"Screenshot-uri[img]",
"Screenshot-uri",
"[spoiler=Trailer]",
"[spoiler]"
);

$arrna2=array(
"\n[i]Файл[/i]\n",
"[spoiler]",
"\n",
"\n",
"",
"Screenshot-uri\n[img]",
"Скриншоты",
"[spoiler=Трейлер]",
"[spoiler= ]"
);



if (count($link_image)){
$imagess=implode("\n", $link_image);
$imagess="\n\n[i]Возможно это доп. скриншоты[/i]:\n".$imagess;

$desc=$desc.$imagess;
}

$desc = trim(str_replace($array2, $arrna2, $desc));

$array4 = array(
"[img]http://img5.immage.de/130309dd6ae4afa48.png[/img]",
"[img]http://s60.radikal.ru/i169/1005/61/1b3f741d3a9e.jpg[/img]",
"[b]Trailer[/b]:  Trailer",
"[img]http://www.torrentsmd.com/forum_img/6682306_a5e.png[/img]",
"[img]http://img4.glowfoto.com/images/2009/12/26-2339312022T.gif[/img]",
"[b]Release oferit de:[/b] [img]http://www.torrentsmd.com/imagestorage/276559_abd.gif[/img]",
"[b]Release oferit de : [/b] [img]http://www.torrentsmd.com/imagestorage/276559_abd.gif[/img]",
"[img]http://s42.radikal.ru/i097/0905/18/2b7f4e45c4bd.jpg[/img]",
"Release oferit de: [img]http://i48.tinypic.com/rqx9nq.gif[/img]",
"Audiie plcut tuturor!!!",
"[img]http://www.torrentsmd.com/imagestorage/601625_ba4.gif[/img]",
"[img]http://s001.radikal.ru/i196/1001/16/e5ed067392fe.png[/img]",
"[b]Релиз группы: [/b]  [img]http://www.torrentsmd.com/forum_img/7187366_df2.gif[/img]",
"[b]Uploade by:[/b] [img]http://www.torrentsmd.com/imagestorage/1388838_a06.gif[/img]",
"Релиз взят на [img]http://www.torrentsmd.com/imagestorage/544591_aeb.gif[/img]",
"релиз найден &nbsp;[img]http://www.torrentsmd.com/imagestorage/470753_c1f.gif[/img]",
"[img]http://www.torrentsmd.com/imagestorage/223937_c30.gif[/img]",
"[img]http://www.torrentsmd.com/imagestorage/1246978_841.jpg[/img]",
"[b]ENJOY THiS RELEASE AND TURN UP YOUR STEREO![/b]",
"[b]Audiere placuta[/b] [img]http://www.torrentsmd.com/imagestorage/84999_1b6.gif[/img]",
"[img]http://www.torrentsmd.com/imagestorage/446783_218.gif[/img]",
"Взято с: http://www.fshow.ru/",
"[img]http://www.torrentsmd.com/imagestorage/177056_c8b.gif[/img]",
"[b]Релиз группы: [/b]  [img]http://torrentsmd.com/forum_img/6680741_567.gif[/img]",
"[b]Релиз группы: [/b]  [img]http://images.cinemacity.cc/groups/electro.gif[/img]",
"[b]Release oferit de :[/b]  [img]http://www.torrentsmd.com/imagestorage/1019520_d02.gif[/img]",
"[img]http://i43.tinypic.com/3013cih.gif[/img]",
"[b] Релиз группы:  [/b]  [img]http://www.torrentsmd.com/imagestorage/1145589_5fa.gif[/img]",
"[b]Релиз группы: [/b]  [img]http://torrentsmd.com/forum_img/6680741_567.gif[/img]",
"[img]http://i024.radikal.ru/1003/b9/fa6ba74fc378.gif[/img]",
"[b]Релиз от команды:[/b]  [img]http://www.torrentsmd.com/imagestorage/135709_bf8.gif[/img]",
"[img]http://www.torrentsmd.com/imagestorage/940084_412.gif[/img]",
"[img]http://img513.imageshack.us/img513/536/37609890.jpg[/img]",
"[img]http://www.torrentsmd.com/imagestorage/1122365_455.gif[/img]",
"[b]Релиз предоставила команда:[/b]  [img]http://www.torrentsmd.com/imagestorage/204364_e83.gif[/img]",
"[img]http://www.torrentsmd.com/imagestorage/1329275_e3c.png[/img]",
"\n[b]Релиз группы[/b]  [img]http://s006.radikal.ru/i215/1001/b7/417cd4d6f909.gif[/img]",
"[b]Release oferit de:[/b] [img]http://www.torrentsmd.com/imagestorage/276559_abd.gif[/img]",
"[b] Релиз найден: [img]http://www.torrentsmd.com/imagestorage/983323_c1f.gif[/img]\n [/b]",
"[b] Релиз: [img]http://s57.radikal.ru/i156/0809/13/bda50bf26f95.gif[/img]\n [/b]",
"\n[img]http://i014.radikal.ru/1004/70/beb785ba6411.jpg[/img]",
"\n[img]http://s39.radikal.ru/i083/0908/1e/fc1a1df8823e.gif[/img]",
"\n[img]http://s40.radikal.ru/i088/1004/c6/10df24a37d25.png[/img]",
"\n[img]http://s58.radikal.ru/i160/1002/02/7f7af550f1fc.jpg[/img]",
"[img]http://i081.radikal.ru/1005/a3/a6aedac31ac0.jpg[/img]\n",
"[img]http://s42.radikal.ru/i097/0905/18/2b7f4e45c4bd.jpg[/img]",
"[img]http://s60.radikal.ru/i170/1005/ad/7c8042a0e5e2.jpg[/img]",
"[img]http://i39.tinypic.com/w1utcm.gif[/img]",
"[img]http://s50.radikal.ru/i130/1004/80/a81a15099d07.png[/img]",
"[img]http://i47.tinypic.com/x2uzur.gif[/img]",
"[b]Release oferit de:[/b]",
"__",
"[b]Релиз найден :[/b] [img]http://i42.tinypic.com/nb3b55.gif[/img]",
"[img]http://i060.radikal.ru/0912/e9/1bf187692e59.gif[/img]",
"[img]http://imgur.com/iBvdZ.png[/img]",
"[b]Релиз Команды:[img]http://s43.radikal.ru/i101/1001/ed/5197e0d6f882.gif[/img][/b]",
"[img]http://s005.radikal.ru/i212/1001/3a/dbed5a47c8f7.png[/img]",
"[img]http://s42.radikal.ru/i098/1001/9a/82e32774bcbc.gif[/img]",
"[img]http://fc08.deviantart.net/fs40/f/2009/041/7/9/79c80adef5b6381da6508d5e92a299b9.gif[/img]",
"[img]http://i50.tinypic.com/1z1ydeo.png[/img]",
"[img]http://i48.tinypic.com/2evt1jp.png[/img]",
"[b]Релиз найден: [/b]  [img]http://i46.tinypic.com/ezriuc.gif[/img]",
"[b][/b]",
"[img]http://i50.tinypic.com/jpu43p.png[/img]",
"[b]Релиз предоставила команда [/b] ",
"[img]http://i080.radikal.ru/1002/cc/2fec302fbda7.gif[/img]",
"Release oferit de: [img]http://i071.radikal.ru/0911/72/f5a11e1506a6.gif[/img]",
"[img]http://www.torrentsmd.com/imagestorage/1019520_d02.gif[/img]",
"[img]http://i48.tinypic.com/3466k50.png[/img]",
"[img]http://i40.tinypic.com/2zrkg0j.gif[/img]",
"[img]http://s06.radikal.ru/i179/1001/3d/735c21b8c9d0.jpg[/img]",
"[img]http://www.torrentsmd.com/imagestorage/1220630_803.gif[/img]",
"[b]Release Oferit de: [/b]",
"[b]Release oferit de :[/b] [img]http://s004.radikal.ru/i207/1004/6b/a693c78cdee1.gif[/img]",
"[img]http://i42.tinypic.com/2w2jv2x.jpg[/img]",
"Release oferit de: [img]http://s55.radikal.ru/i148/1005/5a/0e4cc0adb991.gif[/img]",
"[img]http://i055.radikal.ru/1002/10/abe9104aef5c.jpg[/img]",
"[b]Релиз найден:[/b]  [img]http://s45.radikal.ru/i107/0907/c6/209b0abc7285.gif[/img]",
"Релиз: [img]http://img227.imageshack.us/img227/9897/222ym2.gif[/img]",
"[img]http://www.torrentsmd.com/imagestorage/84999_1b6.gif[/img]",
"[img]http://i43.tinypic.com/1626x4g.png[/img]",
"[img]http://torrentsmd.com/forum_img/6680741_567.gif[/img]",
"[b]Релиз группы: [/b] [img]http://ifotka.ru/images/bud81dew82i94isotcuu.gif[/img]",
"[b]Click to enlarge[/b]",
"[img]http://interfilm.md/imagebucket/76753_7262F9D1E86C142041.jpg[/img]",
"[img]http://userbars.torrents.md/data/media/4/USB-MaxFiber.png[/img]",
"[img]http://s1b.directupload.net/images/100307/bo6jgy5f.gif[/img]",
"[b]be clickable, click it[/b]",
"[img]http://www.torrentsmd.com/imagestorage/1355395_258.jpg[/img]",
"[img]http://s52.radikal.ru/i136/0908/04/3e4b18a08ec1.gif[/img]",
"[img]http://i024.radikal.ru/0903/ed/f055d0233251.jpg[/img]",
"[img]http://www.torrentsmd.com/imagestorage/1355395_258.jpg[/img]",
"[img]http://xmages.net/out.php/i498161_Draken.png[/img]",
"[img]http://s54.radikal.ru/i144/0905/28/23a9f6a513be.gif[/img]",
"[img]http://i49.tinypic.com/svt92u.png[/img]",
"[b]Релиз :[/b]  [img]http://newstudio.tv//pic/groups/newstudio.gif[/img]",
"[img]http://i49.tinypic.com/28hk4s6.gif[/img]",
"[img]http://www.torrentsmd.com/imagestorage/1266701_6c3.gif[/img]",
"[img]http://www.torrentsmd.com/imagestorage/960256_7b1.png[/img]",
"[img]http://pic.ipicture.ru/uploads/090618/2xOvH4CVeR.gif[/img]",
"[img]http://funkyimg.com/u2/994/969/upped_by_Alex_Curnic_PNG.png[/img]",
"Релиз группы[img]http://www.torrentsmd.com/imagestorage/1154136_5cd.gif[/img]",
"найден на[img]http://www.torrentsmd.com/imagestorage/544591_aeb.gif[/img]",
"[img]http://www.torrentsmd.com/imagestorage/1241413_c85.gif[/img]",
"[img]http://s61.radikal.ru/i173/0902/2b/5640f08017c8.gif[/img]",
"[img]http://j.imagehost.org/0811/ae.gif[/img]",
"[img]http://funkyimg.com/u2/829/103/image28108937.png[/img]",
"[img]http://s59.radikal.ru/i166/1008/b6/5d80afc01e4a.gif[/img]",
"[img]http://s7.directupload.net/images/100818/ldkw4nbp.gif[/img]",
"[img]http://s45.radikal.ru/i107/1007/54/de34d7740ddc.png[/img]",
"[img]http://img1.immage.de/1807529b78a815be8.png[/img]",
"[img]http://i069.radikal.ru/0912/5a/169cf02ae9dd.jpg[/img]",
"[img]http://a.imageshack.us/img375/5180/vqxy1l25is.png[/img]",
"[img]http://img843.imageshack.us/img843/3416/kipana.jpg[/img]",
"[img]http://i48.tinypic.com/30c2i9z.jpg[/img]",
"[img]http://img340.imageshack.us/img340/9403/banner1ec.png[/img]",
"[img]http://i45.tinypic.com/20h0b5g.jpg[/img]",
"[img]http://funkyimg.com/u2/931/977/1g0r89.png[/img]",
"[img]http://www.torrentsmd.com/imagestorage/268970_726.gif[/img]",
"[img]http://www.torrentsmd.com/imagestorage/1331236_a63.png[/img]",
"[img]http://img194.imageshack.us/img194/2120/chillout3d.gif[/img]",
"[img]http://i29.tinypic.com/256bdr9.png[/img]",
"[img]http://s07.radikal.ru/i180/1006/e2/af2ebf11d2fd.gif[/img]",
"[img]http://s002.radikal.ru/i197/1008/b0/e2281545861d.gif[/img]",
"[img]http://funkyimg.com/u2/876/704/MILAN278.png[/img]",
"[img]http://bwtorrents.ru/shara/reliz_group/bwtorrents.gif[/img]",
"[img]http://i36.tinypic.com/11kvyg2.png[/img]",
"[img]http://i35.tinypic.com/2qjw307.jpg[/img]",
"[img]http://i46.tinypic.com/29opvgn.png[/img]",
"[img]http://s001.radikal.ru/i193/1006/72/eaefa05eefc1.png[/img]",
"[img]http://i056.radikal.ru/1004/e0/16e39ad2f040.png[/img]",
"[img]http://img576.imageshack.us/img576/9774/daddybanner.gif[/img]",
"[img]http://i43.tinypic.com/1zal2d.jpg[/img]",
"[img]http://i45.tinypic.com/2yoefci.gif[/img]",
"[img]http://s004.radikal.ru/i207/1002/76/6e320bdf3b19.gif[/img]",
"[img]http://i45.tinypic.com/20h0b5g.gif[/img]",
"[img]http://www.smiles.kolobok.us/user/dycros_01.gif[/img]",
"[img]http://s56.radikal.ru/i154/1008/1a/60a1dacf7f8c.gif[/img]",
"[img]http://funkyimg.com/u2/419/165/ColeaSav.gif[/img]",
"[img]http://funkyimg.com/u2/186/478/ColeaSav.png[/img]",
"[img]http://www.torrentsmd.com/imagestorage/1285551_a95.gif[/img]",
"[img]http://r9.fodey.com/2120/9c79975902594725a34aba5ea54b5058.0.gif[/img]",
"[img]http://funkyimg.com/u2/302/471/6i8qjq.png[/img]",
"[img]http://xmages.net/upload/cc21591d.jpg[/img]",
"[img]http://i.imgur.com/4SfbU.png[/img]",
"[img]http://i8.fastpic.ru/big/2010/0710/eb/938f21930fccc35f9cb14c99c1e3adeb.png[/img]",
"[img]http://s3.directupload.net/images/100624/gz5eznvq.gif[/img]",
"[img]http://s55.radikal.ru/i148/1008/7e/8db6f7ab01d3.gif[/img]",
"[img]http://s56.radikal.ru/i151/1008/0f/ac816f5f728a.png[/img]",
"[img]http://s44.radikal.ru/i105/1007/4c/171ef8901005.gif[/img]",
"[img]http://s42.radikal.ru/i095/1008/b2/906cb15df49d.png[/img]",
"[img]http://s55.radikal.ru/i148/1004/52/a0102b399d0b.jpg[/img]",
"[img]http://img1.immage.de/0608cvovckbannermini.png[/img]",
"[img]http://i46.tinypic.com/ezriuc.gif[/img]",
"[img]http://funkyimg.com/u2/955/052/Malossi.png[/img]",
"[img]http://funkyimg.com/u2/230/539/spectogram.gif[/img]",
"[img]http://i45.tinypic.com/20h0b5g.gif[/img]",
"[img]http://i081.radikal.ru/1006/58/f0bef4824828.jpg[/img]",
"[img]http://xmages.net/upload/cc21591d.jpg[/img]",
"[img]http://i.imgur.com/0QkRC.png[/img]",
"[img]http://s58.radikal.ru/i160/0910/a6/13c40a470ff9.gif[/img]",
"[img]http://www.torrentsmd.com/imagestorage/1110760_68e.gif[/img]",
"[img]http://s006.radikal.ru/i213/1003/32/fba7e253af80.jpg[/img]",
"[img]http://i065.radikal.ru/1007/98/4791cf9f148c.jpg[/img]",
"[b]Release offered by[img]http://i46.tinypic.com/1zx5l45.gif[/img][/b]",
"[b]Release offered by[img]http://i46.tinypic.com/1zx5l45.gif[/img]\n[/b]",
"[img]http://s001.radikal.ru/i196/1005/cd/f9b368cdfdc8.gif[/img]",
"[img]http://s42.radikal.ru/i096/1005/60/9eb25151ad29.gif[/img]",
"[b] Release oferit de:  [img]http://s43.radikal.ru/i100/1002/c3/9b6b3c1ca260.gif[/img]\n[/b]",
"[b]Release oferit de:  [img]http://s43.radikal.ru/i100/1002/c3/9b6b3c1ca260.gif[/img][/b]",
"[img]http://i061.radikal.ru/1002/ff/79fbe804f2f7.png[/img]",
"[b]Релиз группы: [img]http://i50.tinypic.com/1fcqhl.gif[/img]\n[/b]",
"[b]Релиз группы: [img]http://i50.tinypic.com/1fcqhl.gif[/img][/b]",
"[b]Скачать: Сэмпл[/b]",
"[b]Релиз от:[/b]  [img]http://i48.tinypic.com/2le4bxg.gif[/img]",
"[img]http://s58.radikal.ru/i162/1008/3b/02eb5e483d22.gif[/img]",
"[img]http://img4.immage.de/0204285dacf9d42f3fd5e.png[/img]",
"[img]http://s53.radikal.ru/i140/1004/fe/2a5a729972f3.png[/img]",
"Релиз: [img]http://s54.radikal.ru/i146/1003/c2/0b169ade0444.gif[/img]",
"[img]http://img5.immage.de/12056a5c8dfbanerxottabbi4.gif[/img]",
"[b]релиз найден: [/b] [img]http://www.torrentsmd.com/imagestorage/483176_6a5.gif[/img]",
"[b]Скачать:[/b]  [b]Сэмпл[/b]",
"[b]Релиз группы INTERLIVE[/b]",
"[img]http://xmages.net/storage/10/1/0/f/3/upload/b27f30ee.png[/img]",
"[img]http://img217.imageshack.us/img217/6276/19iroslove97.png[/img]",
"[img]http://ipicture.ru/uploads/100620/B6KotTzd1S.png[/img]"
);


$desc = trim(str_ireplace($array4, "", $desc));


$desc = trim(str_replace("\n\n\n", "\n", $desc));

$desc = trim(str_replace("\n\n\n\n", "\n\n", $desc));


//echo format_comment($desc)."<hr>";
//die($desc);


$visi="yes";

$multut=sqlesc("yes");

$torrent_com = get_date_time() . " граббер торрентов (http://torrentsmd.com".$path.").\n";


$one=array("Season ","Seasons ","Episodes ","Episode "," [spoiler=Trailer]","Screen&#039;S","[/b][/u]","[/b] [/u]","[u][b]","[u] [b]","[spoiler=Screenshot]");
$two=array("Cезон ","Cезоны ","Серии ","Серия ","[spoiler=Trailer]","Скриншоты","","","","","\n[spoiler=Скриншоты]");

$desc = trim(str_replace($one, $two, $desc));


$descr=htmlspecialchars_uni($desc);
$torrent=htmlspecialchars_uni(trim($name));

//$fname="Tesla_id$next_id";

if (!empty($int))
$own=$CURUSER["id"];
else
$own="92";

///var_dump($desc);
$ret = sql_query("INSERT INTO torrents ( owner, visible, image1, info_hash, name, descr, torrent_com, category, added, last_action, multitracker) VALUES (" . implode(",", array_map("sqlesc", array($own, $visi, $ifileimg, $infohash, $torrent, $descr, $torrent_com, $catid))) . ", '" . get_date_time() . "', '" . get_date_time() . "',".$multut.")"); 

$id = mysql_insert_id();

if ($id){
	
if ($id<>$next_id){
	
rename("torrents/".$next_id.".torrent", "torrents/".$id.".torrent");

////////////
preg_match("#<img src=\"./torrents_img/(.*?)\">#si", $desc, $image);  
echo "Переименовываем .torrent ...<img src=\"./torrents/images/$ifileimg_new\"><br>";


$end_image=$image[1];
$image0=$referer."torrents_img/".$end_image;
$uploaddir = ROOT_PATH."torrents/images/";

if (!empty($end_image))
$image=file_get_contents($image0);

if (!empty($image)) {
$ifileimg_new = $id.'.'.end(explode('.',$end_image));
//@unlink($uploaddir.$ifileimg);
//copy($image0, $uploaddir.$ifileimg);
rename("torrents/images/".$ifileimg, "torrents/images/".$ifileimg_new);
echo "Переименовываем картинку ...<img src=\"./torrents/images/$ifileimg_new\"><br>";
$updateset[] = "image1 = ".sqlesc($ifileimg_new);

sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = ".$id."") or sqlerr(__FILE__, __LINE__);
}
}
//else
//echo "<img src=\"./torrents/images/$ifileimg\"><br>";

///////////
	
	
	
	
	
//echo "Присваиваем идентификатор $torrent ($id) <br>";
$copy=@copy(ROOT_PATH."torrents/txt/".MD5XA.".torrent",ROOT_PATH."torrents/$id.torrent");

if (!$copy){
$copy=@copy(ROOT_PATH."torrents/txt/".MD5XA.".torrent",ROOT_PATH."torrents/$id.torrent");
}

//@unlink($uploaddir.$ifileimg);

if (!$copy){
die("Не могу скопировать торрент в папку");

@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
echo "<script>setTimeout('document.location.href=\"parser_d.php\"', $nu);</script>";
}
} else {

	
$ar = sql_query("SELECT moderated,moderatedby,owner,descr,id FROM torrents WHERE info_hash=".sqlesc($infohash)."");
$rowr = mysql_fetch_array($ar);

if ($rowr["moderated"]=="yes" && $rowr["moderatedby"]=="92" && $rowr["owner"]=="92" && $rowr["descr"]<>$descr && $empty){

//@unlink(ROOT_PATH."torrents/images/".$ifileimg);

if (!empty($image)) {

if (!empty($rowr["image1"]))
@unlink(ROOT_PATH."torrents/images/".$rowr["image1"]);

$ifileimg = $rowr["id"].'.'.end(explode('.',$end_image));
copy($image0, $uploaddir.$ifileimg);
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
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");

sql_query("DELETE FROM files WHERE torrent = '$next_id'");

sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_d.php\"', $nu);</script>";
die("ошибка возможно торрент уже есть в базе ".(empty($id)? " <b>Пустое ID с БАЗЫ (при вставке)</b>":"")."");
}

//die(MD5XA);
$file_url=ROOT_PATH."torrents/".$id.".torrent";

if(@file_exists($file_url) && $id){

$dict = bdec_file($file_url, 1024000);
list($info) = dict_check_t($dict, "info");
list($dname, $plen, $pieces) = @dict_check_t($info, "name(string):piece length(integer):pieces(string)");

$filelist = array();
$totallen = @dict_get_t($info, "length", "integer");
if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
} else {
	$flist = @dict_get_t($info, "files", "list");
//	if (!isset($flist))
	//	$fileerror=true;
//	if (!@count($flist))
//			$fileerror=true;
	$totallen = 0;
	
	if (count($flist)){
	
	foreach ($flist as $file_url) {
		list($ll, $ff) = @dict_check_t($file_url, "length(integer):path(list)");
		$totallen += $ll;
		$ffa = array();
		foreach ($ff as $ffe) {
		//	if ($ffe["type"] != "string")
			//	$fileerror=true;
			$ffa[] = $ffe["value"];
		}
	//	if (!count($ffa))
	//		$fileerror=true;
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
$ca = array();
///sql_query("DELETE FROM files WHERE torrent = '$id'");
foreach ($filelist as $file) {
$file[0]=utf8_to_win($file[0]);
$size=$size+$file[1];
$ca[] = $file[0];
//echo $file[1]."<br>";
}

$mycateyrogy = parse_arrray_cat($ca, $size);
if ($mycateyrogy <> false){

$updateset[] = "category = " . sqlesc($mycateyrogy);
echo "Точная категория : $mycateyrogy <hr>";

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

sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
}



function get_torrent($id=false,$path,$cookies,$row_id) {

$nu=rand(12000,100000);

$path="/".$path;

$referer = "http://torrentsmd.com/";
$host = "torrentsmd.com";



if (!$id){
echo "<script>setTimeout('document.location.href=\"parser_d.php\"', $nu);</script>";
die("не id в get_torrent");
}


//$host = "torrentsmd.com";
//$path = "/download.php/900350/Den.Pobedy.2007.DivX.DVDRip.avi.torrent";



$referer = "http://torrentsmd.com/";

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

sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_id."'") or sqlerr(__FILE__, __LINE__);


echo "<script>setTimeout('document.location.href=\"parser_d.php\"', $nu);</script>";
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


if(@file_exists($sf)){

$dict = bdec_file($sf, 1024000);
list($info) = dict_check_t($dict, "info");
list($dname, $plen, $pieces) = @dict_check_t($info, "name(string):piece length(integer):pieces(string)");

$filelist = array();
$totallen = @dict_get_t($info, "length", "integer");
if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
} else {
	$flist = @dict_get_t($info, "files", "list");
//	if (!isset($flist))
	//	$fileerror=true;
//	if (!@count($flist))
	//		$fileerror=true;
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
sql_query("DELETE FROM files WHERE torrent = '$id'");
foreach ($filelist as $file) {
$file[0]=utf8_to_win($file[0]);
//echo $file[0]."<br>";
//echo $file[1]."<br>";
$size=$size+$file[1];
sql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).",".sqlesc($file[1]).")") or sqlerr(__FILE__, __LINE__);
}
//echo "Обновление файлов внутри торрента<br>";
}
else
{

///if (!empty($id))
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
sql_query("DELETE FROM torrents WHERE torrent = '$id'");

sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_id."'") or sqlerr(__FILE__, __LINE__);


echo "<script>setTimeout('document.location.href=\"parser_d.php\"', 5000);</script>";
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

sql_query("UPDATE torrents SET category='18' WHERE size<'1221225472' AND name LIKE '%DVD%' LIMIT 10") or sqlerr(__FILE__,__LINE__);

echo "<br>Время: <b>$seconds</b> секунд - <b>$queries</b> (queries) - <b>$percentphp%</b> (php) - <b>$percentsql%</b> ($time_sql => sql) - $memory КБ (use memory) <br>";

$dg = number_format(get_row_count("torrents", "WHERE multitracker='yes' AND moderatedby='92' AND moderated='yes' AND owner='92' AND torrent_com LIKE '%добавлен из 15K%'"));

$arr = sql_query("SELECT COUNT(*) AS numgrab,(SELECT COUNT(*) FROM tgrabber WHERE work='0') AS grabost,(SELECT COUNT(*) FROM torrents WHERE multi_time='0000-00-00 00:00:00' AND multitracker='yes') AS numnot,(SELECT COUNT(*) FROM torrents) AS numtore FROM tgrabber WHERE work='1'");
$row_arr = mysql_fetch_array($arr);
$procents="<b title=\" торрентов залито - ".number_format($row_arr["numtore"])."\">Завершенно</b>: ".number_format(100-number_format(100 * (1 - ($row_arr["grabost"] / ($row_arr["grabost"]+$row_arr["numgrab"]))),2),2)."% (<b>ВЗ</b>:".number_format($row_arr["grabost"]).":<b>ОСТ</b>:".number_format($row_arr["numgrab"]).":<b>ВС</b>:".number_format($row_arr["grabost"] + $row_arr["numgrab"]).") Отфильтровано и удалено: ".number_format($row_arr["grabost"] - $row_arr["numtore"])." штук";

$procents="<b title=\" торрентов залито - ".number_format($row_arr["numtore"])."\"><a href=parser_d.php>Завершенно</a></b>: ".number_format(100-number_format(100 * (1 - ($row_arr["grabost"] / ($row_arr["grabost"]+$row_arr["numgrab"]))),2),2)."% (<b>ВЗ</b>:".number_format($row_arr["grabost"]).":<b>ОСТ</b>:".number_format($row_arr["numgrab"]).":<b>ВС</b>:".number_format($row_arr["grabost"] + $row_arr["numgrab"]).") Проверить нужно на мульти: ".number_format($row_arr["numnot"])." штук (Не тронутые Вики'ны $dg)";

if (empty($int))
echo "$procents<script>setTimeout('document.location.href=\"parser_d.php\"', $nu);</script>";
else 
echo "$procents<script>setTimeout('document.location.href=\"premod.php\"', $nu);</script><hr>";

/*
	global $tracker_lang, $queries, $tstart, $query_stat, $querytime;

	
		foreach ($query_stat as $key => $value) {
			print("<div>[<b>".($key+1)."</b>] => <b>
			".($value["seconds"] <= 0.0009 ? "<font color=\"#0468f1\" title=\"Сверхбыстрый запрос. Время исполнения отличное.\">".$value["seconds"]."</font>":"
		    ".($value["seconds"] >= 0.01 ? "<font color=\"red\" title=\"Рекомендуется оптимизировать запрос. Время исполнения превышает норму.\">".$value["seconds"]."</font>" : "<font color=\"blue\" title=\"Запрос не нуждается в оптимизации. Время исполнения допустимое.\">".$value["seconds"]."</font>" )."
		    ")."
		  </b> [".$value["query"]."]</div>\n");
		}
	debug();
	
*/

?>