<?
require_once("include/bittorrent.php");
require_once("include/benc.php");

@ini_set('display_errors', 'On');
@set_time_limit(100);

dbconn();
///loggedinorreturn(); 

define('IN_PARSER', true);


global $DEFAULTBASEURL,$CURUSER;

if ($CURUSER["class"]<>"6")
die("Не админ!");

$nu=rand(5000,25000);

//if (get_user_class() < UC_MODERATOR)
///die("Нет доступа.");


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
//sql_query("DELETE FROM `tfgrabber` WHERE category='1171' OR  category='162' OR  category='365' OR  category='164' OR  category='362' OR  category='364' OR  category='844' OR  category='987' OR  category='370' OR  category='110'") or sqlerr(__FILE__, __LINE__);


$arr = sql_query("SELECT details, id FROM tfgrabber WHERE work='1' LIMIT 1")or sqlerr(__FILE__, __LINE__);
$row_arr = @mysql_fetch_array($arr);

sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

$id_page = $row_arr["details"];

if (!empty($_GET["idget"]))
$id_page = (int) $_GET["idget"];




//$id_page = "291051";


$host="tfile.ru";
$path="/forum/viewtopic.php?t=".$id_page;

define('MD5XA', md5($host.$path));

if (empty($row_arr["details"]) && empty($int))
die("Cсылок в базе нет <a href=\"parser_linkadd.php\">Обновить список внешних файлов</a>? <script>setTimeout('document.location.href=\"parser_org.ua.php?page=20\"', 30000);</script>");

@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");

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
$fp = fsockopen($host, 80, $errno, $errstr, 30) or die("Нет соединения соответственно и данных. <script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>"); 
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
} else die("Нет соединения соответственно и данных. <script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>");


if (count($result)>2){
$sf =ROOT_PATH."cache/toru_session.txt"; 
@unlink($sf);
$fpsf=fopen($sf,"a+"); 
fputs($fpsf,implode("\n",$result)); 
fclose($fpsf);
//echo "<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
//print_r($result); 
echo("<i>Обновлен файл сессий</i> <script>setTimeout('document.location.href=\"parser_t.php\"', 60000);</script><br>");
}


preg_match('/<div class="l">(.*?)<\/div\>/is', $date, $dnik);

if (!empty($dnik[0]) && !stristr($date,'Забыли пароль'))
echo "Ваш логин: <b>".strip_tags(trim(str_replace("нет новых", "", $dnik[0])))."</b> -> Все впорядке<br>";


if (stristr($date,'sendpassword') && stristr($date,'Забыли пароль'))
die("Возможно неправильные куки, пожалуйста введите их заново. id с базы $id_page
Перенаправление на страничку авторизации...<script>setTimeout('document.location.href=\"parser_tconfig.php\"', 5000);</script>
<hr>$date 
");


// проверено.
preg_match('#<title>(.*)</title>#Uis', $date, $dtitle);
list($title,$namesite) = explode("/ ",$dtitle[1]);
$name=$title;


/// тут категории и под категории, ищем по ним слова и вписываем свои cat
preg_match_all('#<span class="nav">(.*)</span>#Uis', $date, $cate);


$tagi = trim(strip_tags($cate[0][1]));
$ara = explode("->",$tagi);
//print_r($ara[2]);
///

$category = trim(strip_tags($cate[1][1]));
$category = str_replace("&nbsp;", "", $category);


if ((stristr($category,'Фильмы') || stristr($category,'Наше кино')) && !stristr($category,'мультфильм'))
$catid="13"; /// Фильмы / AVI

elseif (stristr($category,'Сериалы'))
$catid="11"; /// Сериалы

elseif (stristr($category,'HD') || stristr($category,'DVD'))
$catid="14"; /// Фильмы / HDTV / HD / Blu-Ray

elseif (stristr($category,'Музыка'))
$catid="10"; /// Музыка / Мультимедиа 

elseif (stristr($category,'Игры'))
$catid="5"; /// Игры / ПК

elseif (stristr($category,'Аниме'))
$catid="12"; /// Аниме 

elseif (stristr($category,'рабочий стол') || stristr($category,'рабочий стол'))
$catid="25"; /// Картинки / Фото

elseif (stristr($category,'Софт'))
$catid="26"; /// Софт / Windows

elseif (stristr($category,'Документальные'))
$catid="22"; /// Документальные фильмы 
 	
elseif (stristr($category,'Литература'))
$catid="16"; /// кинги

elseif (stristr($category,'мультфильм'))
$catid="6"; /// мультфильмы

elseif (stristr($category,'КПК и Мобилы'))
$catid="27"; /// PDA / Phone / Android / Palm

elseif (stristr($category,'DVD'))
$catid="15"; /// DVD / Фильмы

else
$catid="4";

//die($catid);



/// находим ссылку для скачивания.
preg_match("|(download.php.*)\"|U", $date, $li);  
$lnk_torrent=$li[1];
$lnk_torrent = str_replace("download.php?id=", "", $lnk_torrent);
/// вывод число




/// находим нужное тег и описание в нем
///<td style="padding: 6px; border-top: 1px solid rgb(173, 186, 198);" colspan="2">

preg_match('/<div><span class="postbody">(.*?)<!-- \/\/bt -->/is', $date, $dtbody);
$desc = $dtbody[0];

//print_r($dtbody);


//preg_match('/<form style="padding: 0.8em 0.3em;">(.*?)<\/div>/is', $date, $dtbod);

//print_r($dtbod);


//die($desc);

if (stristr($category,'Ищу') || stristr($category,'На удаление') || stristr($desc,'эротика') || stristr($desc,'erotic') || stristr($desc,'порно ') || stristr($desc,' эроти') || $ara[1] == "ожидающие публикации"){
sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

die("это запросы или эротика, ищу торренты или На удаление. Перенаправление... <script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>");
}


/// убираем оценки в описании
$desc = preg_replace('/<form.+style="([^>]+)">(.*?)<\/form\>/is', "", $desc);
/// убираем h2 тег красного названия
$desc = preg_replace('/<h2.+style="([^>]+)">(.*?)<\/h2\>/is', "", $desc);
/////////////////////


if (stristr($desc,'Цитировать') && stristr($desc,'Пожаловаться') && stristr($desc,'страницы')){
sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

die("этот релиз неправильного формата. Перенаправление... <script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>");
}


//@file_put_contents(ROOT_PATH."torrents/txt/$id_page.txt",$desc);


if (empty($desc) && empty($name)) {

if (stristr($date,'не существует'))
echo "Данная тема не существует! <br>";

if (stristr($date,'ищу'))
echo "Данная тема по запросам <br>";

sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "Пустое описание и название.<br> $date<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
die();
}

///////////////////////////
$ret = mysql_query("SHOW TABLE STATUS LIKE 'torrents'"); 
$row = mysql_fetch_array($ret); 
$next_id = $row['Auto_increment']; 


//////////// постер
preg_match('/<img.+?src="(http:\/\/[^()<>\s]+?)"[^>]*?>/is', $desc, $image);  

$end_image=trim($image[1]);
$uploaddir = ROOT_PATH."torrents/images/";

if (empty($end_image) && !empty($imagesff))
$end_image=$imagesff;

if (!empty($end_image)){
$image=@file_get_contents($end_image);

if (!empty($image)) {
$ifileimg = $next_id.'.'.end(explode('.',$end_image));
@unlink($uploaddir.$ifileimg);
@file_put_contents($uploaddir.$ifileimg,$image);
}
}

//////////// постер
//print_r($end_image);





$desc = preg_replace('/<div style="text-align:center">(.*?)<\/div\>/is', "\\1", $desc);
///

$desc = preg_replace('/<span style="text-decoration: underline;">(.*?)<\/span\>/is', "[i]\\1[/i]", $desc);
/// курсивы +
$desc = preg_replace('/<span style="font-weight: bold;">(.*?)<\/span\>/is', "[b]\\1[/b]", $desc);
/// жирный +
$desc = preg_replace('/<b class="field">(.*?)<\/b\>/is', "[b]\\1[/b]", $desc);
/// убираем цвета
$desc = preg_replace('/<span[^>]style="([^>]+)">(.*?)<\/span\>/is', "\\2", $desc);
/// убираем span's со стилями

/// иначе меняем спойлер hmtl тег на [spoiler]
$desc = preg_replace('/<div class="spoiler">(.*?)<\/div>/is', "\n[spoiler]\\1\n[/spoiler]", $desc);

/// html тег span используется для жирного подче зачекрн и цветов 
//$desc = preg_replace('/<b[^>]class="([^>]+)">(.*?)<\/b\>/is', "[b]\\2[/b]", $desc);
$desc = preg_replace('/<span[^>]>(.*?)<\/span\>/is', "\\2", $desc);


//$desc= preg_replace("#<a.*?href=\"(.*?)\".*>(.+?)<\/a>#is", "[1212]\\1[/123]", $desc); /// работает

/////////////////////////////////////////////////
//$desc = preg_replace('/<(a.*) href=\"(.*?)\">(.*)<\/a>/', "\\3", $desc);
/// для ссылок внутренних
$desc = preg_replace('/<div align="center">(.*?)<\/div\>/is', "\\1", $desc);
$desc = preg_replace('/<font[^>]face="([^>]+)">(.*?)<\/font\>/is', "\\2", $desc);
// если имеется шрифт
$desc = preg_replace('/<font[^>]size="([^>]+)">(.*?)<\/font\>/is', "\\2", $desc);
// если имеется размер
//die($desc);

$desc = preg_replace('/<font[^>]style="([^>]+)">(.*?)<\/font\>/is', "\\2", $desc);
// если имеется размер
$desc = str_replace("<b></b>", "", $desc);
$desc = preg_replace('/<font color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])>(.*?)<\/font\>/is', "\\2", $desc);
$desc= preg_replace("#<a.*?href=\"(http:\/\/[^\s'\"<>]+(\.(gif|jpg|jpeg|png)))\">(.+?)<\/a>#is", "[img]\\1[/img]", $desc);
$desc= preg_replace("/<embed.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>([^<]*?)<[^>]*?>/i", "[video=\\1]", $desc);
$desc= preg_replace("/<img.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>/i", "[img]\\1[/img]\n", $desc);
$desc= preg_replace("/<var.*?title=\"(http:\/\/[^()<>\s]+?)\"[^>]*?><\/var>/i", "[img]\\1[/img]\n", $desc);
$desc= preg_replace("/<var.*?title=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>([^<]*?)<[^>]*?>/i", "[img]\\1[/img]\n", $desc);

$array=array("<b>","<i>","</b>","</i>","<u>","</u>","<br>","<hr>");
$arrna=array("[b]","[i] ","[/b] ","[/i]","[u]","[/u]","\n","\n");

$desc = str_ireplace($array, $arrna, $desc);
//$desc = str_replace("[b][/b]", "", $desc);
//$desc = str_replace("[spoiler]Показать скриншоты", "[spoiler]", $desc);

$arraysdf=array("$(function() {"," $('input.torrent-rating').rating({","split:2,","callback: tRate"," });","});");

$desc = str_ireplace($arraysdf, "", $desc);
$desc = trim(strip_tags($desc));
$desc = trim(str_replace("\n\n\n", "\n", $desc));
$desc = trim(str_replace("\n\n\n\n", "\n\n", $desc));


$desc = str_replace("[spoiler]Показать скриншоты", "[spoiler]", $desc); //+
$desc = str_replace("[b]Рейтинг на IMDB:[/b] ", "", $desc);

$arraysdf=array("$(function() {"," $('input.torrent-rating').rating({","split:2,","callback: tRate"," });","});");

$desc = str_ireplace($arraysdf, "", $desc);

$desc = trim(strip_tags($desc));

$desc = trim(str_replace("\n\n\n", "\n", $desc));

$desc = trim(str_replace("\n\n\n\n", "\n", $desc));

//print_r($updateset);

//echo format_comment($desc)."<hr>";

//echo $desc;
//die;

if (!empty($image) && !empty($end_image)) {
$desc = str_replace("[img]".$end_image."[/img]", "", $desc); //+
}

///$desc = str_replace("[b]Описание:[/b] \n", "\n", $desc);

$refer="http://".$host.$path;

$infohash=get_torrent($next_id,$lnk_torrent,$cookies,$row_arr["id"],$refer);

if ($infohash==false){
	
sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_t.php\"', 10000);</script>";
die("Невозможно прочитать внешний .torrent файл. $infohash");
}

//////////////////////////// авторские раздачи - запрет заливки
$num_license = get_row_count("license","WHERE info_hash=".sqlesc($infohash));

if (!empty($num_license)){
sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_t.php\"', 10000);</script>";
echo("Данный файл имеет авторские права, запрещено заливать подобные файлы.");
die;
}
//////////////////////////// авторские раздачи - запрет заливки



if (empty($desc) || stristr($desc,'Страницы')){
/// если 2 пустое описание!!!
sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "Пустое описание (повторная проверка в след раз).<script>setTimeout('document.location.href=\"parser_t.php\"', 2000);</script>";
die();	
}


$visi="yes";

$multut=sqlesc("yes");

$torrent_com = get_date_time() . " граббер торрентов 3 ($host$path).\n";

$descr=htmlspecialchars_uni(trim($desc));
$torrent=htmlspecialchars_uni($name);


///echo format_comment($descr);


//$fname="Tesla_id$next_id";

if (!empty($int))
$own=$CURUSER["id"];
else
$own="92";

///var_dump($desc);


if (!empty($ara[2])){

$tags = str_replace("/", ", ", $ara[2]);

}
else
$tags = '';


$ret = sql_query("INSERT INTO torrents (owner, visible, image1, info_hash, name, descr, tags, torrent_com, category, added, last_action, multitracker) VALUES (" . implode(",", array_map("sqlesc", array($own, $visi, $ifileimg, $infohash, $torrent, $descr, $tags, $torrent_com, $catid))) . ", '" . get_date_time() . "', '" . get_date_time() . "',".$multut.")"); 

$id = mysql_insert_id();


////////////////////////////////////////// мод скринов от tesla tt
///////////////// если заливать ссылки скринов в скрин 1 скрин 2 и тд
/// выбираем спойлер, после ищем скрины
preg_match('/<div class="spoiler">(.*?)<\/div>/is', $date, $scrins);

$scrins[0] = preg_replace('/<img.*?src="(http:\/\/[^()<>\s]+?)"[^>]*?>/i', "\\1", $scrins[0]);
$scrins[0] = str_replace("Показать скриншоты", "", $scrins[0]);
$scrins[0] = str_replace("<br>", "\n", $scrins[0]);
$scrins[0] = str_replace("http:", "<br>http:", $scrins[0]);


/// переделать все в массив
//$no_url=array("sendpic","funkyimg.com");
$scriarra = explode("\n",strip_tags($scrins[0]));
if (count($scriarra)>1){
echo "Подготовка скриншотов к проверке (".count($scriarra).")<br>";

//print_r($scrins); //+
////////////////////////////////////////// мод скринов от tesla tt
$xpi=1; $xpio=0;

foreach ($scriarra AS $pic) {

$pic=htmlentities(trim($pic));

////////////////////
$araw = array("jpg.html","gif.html","bmp.html","png.html","jpeg.html");
$aran = array("jpg","gif","bmp","png","jpeg");

$pic = trim(str_replace($araw, $aran, $pic));

if ($xpi<5){
/// sendpic и funkyimg.com
if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $pic)){

if (list($width, $height) = @getimagesize($pic)) {
if ($xpi<5 && ($width>=300 && $height>=150 && $width>=$height)){

$updateset[] = "picture$xpi = ".sqlesc($pic);


echo "Добавлен $pic<br>";
++$xpio;
++$xpi;
}
}
if ($width>=50 && $height>=200 && $width<$height && empty($imagesff))
$imagesff = $pic;


unset($pic);
}
/////else echo "Удаляем лишний $pic и $width и $height<br>";
}


}

/////echo "--------- <br>";
/////////////
}

//die;
//print_r($updateset);
//die;


if ($id){


if ($id<>$next_id){
	
rename("torrents/".$next_id.".torrent", "torrents/".$id.".torrent");

////////////

if (!empty($ifileimg)) {
$ifileimg_new = $id.'.'.end(explode('.',$ifileimg));
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
$copy=copy(ROOT_PATH."torrents/txt/".MD5XA.".torrent",ROOT_PATH."torrents/$id.torrent");

if (!$copy)
echo("Не могу скопировать торрент в папку");
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
} else {

	
$ar = sql_query("SELECT * FROM torrents WHERE info_hash=".sqlesc($infohash)."");
$rowr = mysql_fetch_array($ar);

if ($rowr["moderated"]=="yes" && $rowr["moderatedby"]=="92" && $rowr["owner"]=="92" && $rowr["descr"]<>$descr && $nothin){

@unlink(ROOT_PATH."torrents/images/".$ifileimg);

if (!empty($image)) {

if (!empty($rowr["image1"]))
@unlink(ROOT_PATH."torrents/images/".$rowr["image1"]);

$ifileimg = $rowr["id"].'.'.end(explode('.',$end_image));
@copy($image0, $uploaddir.$ifileimg);
$updateset[] = "image1 = ".sqlesc($ifileimg);
}
else
@unlink(ROOT_PATH."torrents/images/".$ifileimg);

$updateset[] = "name = ".sqlesc($torrent);
$updateset[] = "descr = ".sqlesc($descr);
$updateset[] = "category = ".sqlesc($catid);

sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = ".$rowr["id"]."") or sqlerr(__FILE__, __LINE__);

echo "<br>Обновлен более старый торрент новым - Успешно <a href=\"details.php?id=".$rowr["id"]."\">$torrent</a>. <br>";

}


	
//die(MD5XA);
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");

sql_query("DELETE FROM files WHERE torrent = '$next_id'");

sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
die("ошибка возможно торрент уже есть в базе ".(empty($id)? " <b>Пустое ID с БАЗЫ (при вставке)</b> $torrent":"")."");
}

//die(MD5XA);
$file_url=ROOT_PATH."torrents/".$id.".torrent";

if(file_exists($file_url) && $id){

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

//$dict=@bdec(@benc($dict)); 
//@list($info) = @dict_check_t($dict, "info");
//$infohash = sha1($info["string"]);
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

}

$mycateyrogy = parse_arrray_cat($ca, $size);
if ($mycateyrogy <> false){

$updateset[] = "category = " . sqlesc($mycateyrogy);
echo "Точная категория : $mycateyrogy <hr>";

}


$updateset[] = "numfiles = " . sqlesc(count($filelist));
$updateset[] = "size = " . sqlesc($size);

$updateset[] = "moderated = 'yes'";
$updateset[] = "moderatedby = ".sqlesc("92");
$updateset[] = "moderatordate = ".sqlesc(get_date_time());

///$updateset[] = "info_hash = " . sqlesc($infohash);
sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = $id") or sqlerr(__FILE__, __LINE__);
echo "<title>$torrent</title>Успешно залит и обновлен - <a href=\"details.php?id=$id\">$torrent</a> с ".$row_arr["id"].".";

sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
}


function get_torrent($id=false,$path,$cookies,$row_id,$refer) {

$nu=rand(12000,100000);

$host = "tfile.ru";

if (!$id){
echo "<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
die("не id в get_torrent");
}


/*
$image=file_get_contents("http://".$host."/forum/download.php?id=".$path);

//copy(ROOT_PATH."torrents/txt/".MD5XA.".torrent",ROOT_PATH."torrents/$id.torrent");

if (!empty($image)) {
$ifileimg = $next_id.'.'.end(explode('.',$end_image));
//copy($image, $uploaddir.$ifileimg);
file_put_contents(ROOT_PATH."torrents/txt/".MD5XA.".torrent",$image.$ifileimg);
}

*/


//die($path);
echo "Устанавливаем соединение c .torrent файлом - $path<br>";
$fp = fsockopen($host, 80, $errno, $errstr, 50) or die("соединения нет....<br>");
//socket_set_timeout($fp,1,0); //Назначаем таймаут (100 - это секунды)

echo "соединение есть ... <br>";
//die($host."/forum/download.php?id=$path");
// Проверяем успешность установки соединения
if ($fp) {
// Заголовок HTTP-запроса
$headers = "GET /forum/download.php?id=$path HTTP/1.0\r\n";
$headers.= "Host: $host\r\n";
$headers.= "Content-type: application/x-www-form-urlencoded\r\n";
$headers.= "Accept: *\r\n";
$headers.= "Accept-Charset: *\r\n";
$headers.= "Accept-Encoding: binary\r\n";
$headers.= "Accept-Language: ru\r\n";
$headers.= "Referer: ".$refer."\r\n";
$headers.= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.2; ru; rv:1.9.2) Gecko/20100115 Firefox/3.6 WebMoney Advisor\r\n";

if ($cookies) {
foreach ($cookies as $cookie)
$headercookie .= $cookie." ";
$headercookie = substr($headercookie, 0, -2);
$headers .= "Cookie: ".$headercookie."\r\n";
}
//$headers .= "\r\n";
$headers.= "Content-Length: ".strlen($data)."\r\n";
$headers.= "Connection: Close\r\n";
$headers .= "\r\n";

// Отправляем HTTP-запрос серверу

///echo "Отправка данных <br>";
@fputs($fp, $headers.$data);
$date="";
while (!feof($fp)) {
$line = fgets(($fp), 1024);
$date.=$line;
}
@fclose($fp);
}
else
{
	die("Нет подключения в get торрент ($errstr - $errno)");
}

if (stristr($date,'Регистрация') && stristr($date,'Забыли пароль') && !stristr($date,'Новых сообщений нет'))
die("Возможно неправильные куки, пожалуйста введите их заново.");

if (stristr($date,'код с картинки'))
die("каптча!");


//die($date);
if (strlen($date)>="724148" || empty($date))
{

//if (!empty($next_id))
//unlink(ROOT_PATH."torrents/".$next_id.".torrent");

sql_query("DELETE FROM files WHERE torrent = '$next_id'");

sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_id."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
die("ошибка размер файла торрент более 700 КБ, не разрешенно.");
}



//die($date);
echo "Получение, обработка данных<br>";
echo "<script>setTimeout('document.location.href=\"parser_t.php\"', 5000);</script>";

list ($data_1,$data_2) = explode("\r\n\r\n", $date);

//die(MD5XA);

$ifilename = ROOT_PATH."torrents/txt/".MD5XA.".torrent";

///file_put_contents($ifilename,$data_2)


if(file_put_contents($ifilename,$data_2) && !empty($data_2)){
echo "Данные об торренте есть ...<br>";
$dict = bdec_file($ifilename, 1024000);

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
list($info) = @dict_check_t($dict, "info");
$infohash = sha1($info["string"]);

$size=0;

if (!empty($infohash)){
sql_query("DELETE FROM files WHERE torrent = '$id'");
foreach ($filelist as $file) {
$file[0]=utf8_to_win($file[0]);
//echo $file[0]."<br>";
//echo $file[1]."<br>";
$size=$size+$file[1];
sql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).",".sqlesc($file[1]).")");
}
//echo "Обновление файлов внутри торрента<br>";

$updateset[] = "numfiles = " . sqlesc(count($filelist));
$updateset[] = "size = " . sqlesc($size);

$updateset[] = "moderated = 'yes'";
$updateset[] = "moderatedby = ".sqlesc("92");
$updateset[] = "moderatordate = ".sqlesc(get_date_time());

///$updateset[] = "info_hash = " . sqlesc($infohash);
sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = $id") or sqlerr(__FILE__, __LINE__);
}
else
{

///if (!empty($id))
unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
sql_query("DELETE FROM torrents WHERE torrent = '$id'");

sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_id."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_t.php\"', 5000);</script>";
die("ошибка размер файла 0 размер.");
}
//die(MD5XA);

//echo "Заносим в базу все необходимые данные <br>";
} else {
echo "Завершено с ошибкой не могу вписать данные в .torrent файл.<br>";

sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_id."'") or sqlerr(__FILE__, __LINE__);
echo "<script>setTimeout('document.location.href=\"parser_t.php\"', 20000);</script>";
}

if (empty($infohash))
$infohash = false;

return $infohash;
}


//@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");

$seconds = (timer() - $tstart);
$phptime = 	$seconds - $querytime;
$query_time = $querytime;
$percentphp = number_format(($phptime/$seconds) * 100, 2);
$percentsql = number_format(($query_time/$seconds) * 100, 2);
$seconds = 	substr($seconds, 0, 8);
$memory = round(memory_get_usage()/1024);
$time_sql=sprintf("%0.4lf",$querytime);

$ar = sql_query("SELECT COUNT(*) FROM tfgrabber WHERE work='2'");
$row_a = @mysql_fetch_array($ar);


echo "<br>Время: <b>$seconds</b> секунд - <b>$queries</b> (queries) - <b>$percentphp%</b> (php) - <b>$percentsql%</b> ($time_sql => sql) - $memory КБ (use memory) <br>";

$arr = sql_query("SELECT COUNT(*) AS numgrab,(SELECT COUNT(*) AS numgrab FROM tfgrabber WHERE work='0') AS grabost,(SELECT COUNT(*) FROM torrents) AS numtore FROM tfgrabber WHERE work='1'");
  $row_arr = @mysql_fetch_array($arr);
///$procents="<b title=\" торрентов залито - ".number_format($row_arr["numtore"])."\">Завершенно</b>: ".number_format(100-number_format(100 * (1 - ($row_arr["grabost"] / ($row_arr["grabost"]+$row_arr["numgrab"]))),2),2)."% (<b>ВЗ</b>:".number_format($row_arr["grabost"]).":<b>ОСТ</b>:".number_format($row_arr["numgrab"]).":<b>ВС</b>:".number_format($row_arr["grabost"] + $row_arr["numgrab"]).") Отфильтровано и удалено: ".number_format($row_arr["grabost"] - $row_arr["numtore"])." штук";

$procents="<b title=\" торрентов залито - ".@number_format($row_arr["numtore"])."\"><a href=parser_t.php>Завершенно</a></b>: ".@number_format(100-number_format(100 * (1 - ($row_arr["grabost"] / ($row_arr["grabost"]+$row_arr["numgrab"]))),2),2)."% (<b>ВЗ</b>:".@number_format($row_arr["grabost"]).":<b>ОСТ</b>:".@number_format($row_arr["numgrab"]).":<b>ВС</b>:".@number_format($row_arr["grabost"] + $row_arr["numgrab"]).") Отфильтровано и удалено (где пустое описание $row_a[0])): ".@number_format($row_arr["grabost"] - $row_arr["numtore"])." штук";

if (!empty($row_arr["numgrab"]))
echo "$procents<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
else
echo "$procents<script>setTimeout('document.location.href=\"parser_org.ua.php?page=50\"', $nu);</script>";



?>