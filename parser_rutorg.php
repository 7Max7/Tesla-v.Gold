<?
//if(!defined('IN_PARSER'))  die('Hacking attempt parser!'); 

require_once("include/bittorrent.php");
require_once("include/benc.php");

@ini_set('display_errors', 'On');
@set_time_limit(60);

dbconn();

/**
 * @author 7Max7
 * @copyright 2010
**/

$nu=rand(2000,8000);
$updateset = array();

//// функции ////
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
//// функции ////



/*
--
-- Структура таблицы `trutgrab`
--

CREATE TABLE IF NOT EXISTS `trutgrab` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `details` int(100) NOT NULL DEFAULT '0',
  `work` int(10) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `one` (`details`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `trutgrab`
--
*/

$host="rutor.org";

if (isset($_GET["page"])){

$idpage = (int) $_GET["page"];

$white = (int) $_GET["white"];

// Устанавливаем соединение
$fp = fsockopen($host, 80, $errno, $errstr, 25) or die("Нет соединения для сборки id <script>setTimeout('document.location.href=\"parser_rutorg.php?page=$idpage&white=$white\"', 5000);</script>"); 
// Проверяем успешность установки соединения
if ($fp) {
// Заголовок HTTP-запроса
$headers = "GET /browse/$idpage/0/0/0 HTTP/1.0\r\n";
$headers.= "Host: $host\r\n";
//$headers.= "Content-type: application/x-www-form-urlencoded\r\n";
//$headers.= "Content-Length: ".strlen($data)."\r\n";
$headers.= "Accept: *\r\n";
$headers.= "Accept-Charset: *\r\n";
$headers.= "Accept-Encoding: binary\r\n";
$headers.= "Accept-Language: ru\r\n";
//$headers.= "Referer: http://login.rutracker.org/forum/login.php\r\n";
$headers.= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.2; ru; rv:1.9.2) Gecko/20100115 Firefox/3.6 WebMoney Advisor\r\n";
$headers.= "Connection: close\r\n\r\n";
// Отправляем HTTP-запрос серверу
@fwrite($fp, $headers.$data);
$date = "";
while (!feof($fp)){
$line = fgets($fp, 1024);
$date.=$line;
}
@fclose($fp);
}

preg_match('/<table width="100%">(.*?)<\/table>/is', $date, $dbrowse);

echo "Есть данные: собираем id от них....<br>";

preg_match_all('/<a class="downgif" href="\/download\/(.*?)">/is', $dbrowse[0], $dli); /// для ссылок внутренних
//<a class="downgif" href="/download/66254">
$cou = $dli[1];

//print_r($cou);
$numminus = $numplus = 0;

if (count($cou)){
foreach ($cou as $k) {
if (is_valid_id($k)){
sql_query("INSERT INTO trutgrab (details, work) VALUES (".sqlesc($k).",".sqlesc("1").")");
$newid = mysql_insert_id();
if ($newid){
	++$numplus;
//echo "<font color=green><b>".$k."</b></font> ";
} else {
	++$numminus;
//echo "<font color=red><i>".$k."</i></font> ";
}
}

//echo "<br>";

}
}

echo "Добавлено <font color=green><b>$numplus</b></font> из <font color=red><i>$numminus</i></font> уже есть.<br>";
echo "<title>Н:$numplus C:$numminus</title>";

++$idpage;

if (empty($numplus))
++$white;


if (($numminus)>0 && $numplus==0 && $white>2)
echo "Готово, <b>теперь собираем торренты</b> через 5 сек <script>setTimeout('document.location.href=\"parser_rutorg.php\"', 5000);</script>";
else
echo "Готово, перелинковка через 5 сек <script>setTimeout('document.location.href=\"parser_rutorg.php?page=$idpage&white=$white\"', 5000);</script>";

die;
}





$arr = sql_query("SELECT * FROM trutgrab WHERE work='1' ORDER BY id DESC LIMIT 1");
$row_arr = mysql_fetch_array($arr);

$roid = $row_arr["details"];

if (!empty($_GET["idget"]))
$roid = (int) $_GET["idget"];

if (empty($roid)){
die("Переключение на страницу сборка id, перелинковка через 5 сек <script>setTimeout('document.location.href=\"parser_rutorg.php?page=0\"', 5000);</script>");

//sql_query("UPDATE `trutgrab` SET `work` = '1'"); 
} else
echo "Взят id с базы: $roid <br>";



$path="/torrent/$roid/";

define('MD5XA', md5($host.$path));


//http://www.rutor.org/torrent/65835/

// Устанавливаем соединение
$fp = fsockopen($host, 80, $errno, $errstr, 25) or die("Нет соединения <script>setTimeout('document.location.href=\"parser_rutorg.php\"', 10000);</script>"); 
// Проверяем успешность установки соединения
if ($fp) {
// Заголовок HTTP-запроса
$headers = "GET /$path HTTP/1.0\r\n";
$headers.= "Host: $host\r\n";
//$headers.= "Content-type: application/x-www-form-urlencoded\r\n";
//$headers.= "Content-Length: ".strlen($data)."\r\n";
$headers.= "Accept: *\r\n";
$headers.= "Accept-Charset: *\r\n";
$headers.= "Accept-Encoding: binary\r\n";
$headers.= "Accept-Language: ru\r\n";
//$headers.= "Referer: http://login.rutracker.org/forum/login.php\r\n";
$headers.= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.2; ru; rv:1.9.2) Gecko/20100115 Firefox/3.6 WebMoney Advisor\r\n";
$headers.= "Connection: close\r\n\r\n";
// Отправляем HTTP-запрос серверу

@fwrite($fp, $headers.$data);
$date = "";
while (!feof($fp)){
$line = fgets($fp, 1024);
$date.=$line;
}

@fclose($fp);
}

preg_match('/<table id="details">(.*?)<\/table>/ius', $date, $dnik);
//////////////////////////////////////

preg_match('/<title>(.*?)<\/title\>/is', $date, $dtitle);

$name=utf8_to_win($dtitle[1]);
$name = trim(str_replace("RuTor.Org :: ", "", $name));


$ine = array(
"от RollHD", "от HELLRAiSER", "от HELLYWOOD", "от CGInfo", "от HQ-ViDEO", "от Shinobi", "от Infernum", "от Friends-Club", "от FRiENDS-Club", "от DoroshDan", "от PlanetaUA", "от Кеши", "by Veloboss", "by GENADIY", "by HQ-ViDEO", "by BigFan", "от R.G. PlayBay", "от RomeoWhite", "by RuUu", "by maksnev82", "от doberman", "от Drakula", "by demonВидео", "от FreeHD", "от kubik v kube", "от FS-Media", "от Kvadratmalevicha", "by parole", "by Хоттабыч (BigFANGroup)", "by R.G.МОСКВИ4И", "by the sun", "by OZAGIS", "by BigFANGroup", "by Infineon", "от R.G ReCoding", "by R.G. 48 seconds", "от DHT-Movies", "by FS-Media", "by Arow Malossi", "by Kino Pirate", "от LPF TV", "от Overlord", "от 0day ", "от HAZARD'A", "от R.G Repacker's", "by R.G.R3PacK", "от CtrlHD", "от ESiR", "by 4Euro", "от New-TrackerUA", "от M.I.B", "от EuReKa", "от Linkorn", "от iolegv", "от Neo-Sound.ru", "от SAMLL-RiP", "от CHDBits", "от RI4MOND", "от Smart's studios", "от EbP", "от TORRENTSES", "by NK", "by bizarrevic", "by VANGA", "by Basilevs", "от trexmernii", "by XENON", "by morganvolter", "by HDClub", "от MadChester Кубик в Кубе NovaFiLM", "от EuReKA", "by ELEKTRI4KA", "by 5.1", "by SneGGs", "от Oleggutsalo", "от zackary", "by VIDODB", "от SkyeFilmTV", "by Suprug", "by rp0Mk0cTb", "by SDHQ", "от Doctor No.", "от SoftClub", "от TDiTP", "от Лаптя", "от F_i_X", "от 1001 Cinema", "от Drakulа", "от KаMo", "by KaMo", "by Illusion", "от Psycho-Share", "by X-DIGITAL (BigFANGroup)", "от InterfaceLift", "от KaMo", "by Vladas", "by HA3APET PC", "от RELOADED", "от Маши", "by An Ocean Wave", "by HA3APET", "от lenpas", "от W I", "от KiLR0Y", "от games_vandal", "от EPIDEMZ", "by DESAN", "от Капуцинов", "от BaibaKo", "от HD Tracker", "от -DaRkY-", "от lev31071977", "by Kommunist", "от Smart's Studios", "от Player1993", "от Overcast", "от Ildarec014", "от Bumblebee", "от SmallRip", "by games vandal", "от R.G.", "от SoftClub/EA Russia", "by Andrjuha-Bratan", "by Shel", "от группы CGInfo", "by Mr.legal", "by Megashara", "by Padre mp4", "от Zhmak", "by DJ ART", "от FoolMovie", "от iolegv RuUu", "by boban39", "by stalone", "от К@Мo", "от AllSeries Info CGInfo", "от Dime HQ-VIDEO", "от Geralt iz Rivii", "by HDCiub", "by d_kos", "by doberman", "от HQ-films", "от epu2009", "by d kos", "by rus-tracker", "от Mari", "от VIDEODB", "by Studdio", "от FRiENDS Club", "by Хоттабыч", "by Tiger", "by Virelic", "by AZBUKASOFTA", "от Lali", "by trexmernii", "от Instigator-NN", "by R.G. Beautiful Thieves", "от Никулина", "от 3m1L", "от NOLIMITS-TEAM", "от R.G. \"48 seconds\"", "от ANDROZZZ", "by djip", "от boss89", "от StarTorrent", "от z10yded", "от КММ", "by martin", "by \"R.G. 48 Seconds\"", "от РиперАМ Suprug", "by qDan", " - РиперAM", "от MashystiK", "от JoomlaXTC", "от Joomlapraise", "от greys2008 Arkadiy", "от SkeT", "by stalone ( stalone-2)", "by R.O.T.O.R", "by Fool Movie", "by KVNforAll N!CK", "by KVNforAll Ruuu", "by BigSOUNDgroup", "by yuraflash", "by POkEmON", "by v1nt", "от HotRabbit", "от Васёк777", "от PsihoShare", "от ФАГОТ", "by s0lAr", "от HeDgEhOt", "от Кosmos", "от R.G.R3PacK", "by 7lobiN", "by dimich", "от RAMpage CHG", "от R.G. CodeRs", "от Хоттабыч (BigFANGroup)", "от Феникс", "от Уокер | Remastered", "от Тракториста", "от Тракторист", "от СИНЕМА-ГРУПП", "от РиперАМ & Suprug", "от РиперAM", "от Лобана", "от Кураж-Бамбей", "от Кураж Бамбей", "от Кубик в Кубе", "от Космос", "от КинозалSAT", "от ДубльPR Studio", "от ДлиNNый", "от Архары", "от Zona-X", "от YOOtheme", "от WiKi)", "от Wegas Digital", "от wakal", "от volka", "от Voland", "от VO-Production", "от Vladislav71", "от ViP", "от VelArt", "от vadi", "от Ultra", "от UaTeam", "от TVShows", "от Traumer", "от SuperNatural", "от StopFilm", "от Spieler", "от SMALL-RiP", "от Shevon", "от ПереЛоЛ", "от Shepards", "от sergeorg", "от Sergei7721", "от RuUu, iolegv & ANDROZZZ", "от RuUu (BigFANGroup)", "от RuUu", "от RUSSOUND", "от RP4 CHG", "от RommyFilm", "от RocketTheme", "от RiperAM", "от RIPER.AM", "от RelizLab", "от RadioXyLigaN", "от R.G.Spieler", "от R.G. Механики", "от R.G. Игроманы", "от R.G. Repackers Bay", "от R.G. ReCoding", "от R.G. Catalyst", "от PиперАМ", "от Punisher", "от PowerTracker", "от Pasha74", "от ParadiSe", "от OneFilm", "от Oday", "от NovaFilm", "от NewStudio", "от NaumJ", "от Music80", "от mk2", "от mi24", "от Martin", "от maksnev82", "от m0j0", "от Lunatik", "от Lukavaya", "от LostFilm.tv", "от LostFilm", "от Kosmos", "от Kino Pirate", "от Kerob", "от K1RgH0ff", "от iolegv-RuUu", "от Ildarec-014", "от HQGROUP", "от HQCLUB", "от HDReactor", "от HDGANG", "от HDCLUB", "от HD-Zona-X", "от Hansmer", "от Gravi-TV", "от Gellard", "от G1AR", "от FreeTorrents-UA", "от FoC", "от FLINTFILMS", "от FinaRGoT", "от Fenixx)", "от Fenixx", "от ExKinoRay.TV", "от ExKinoRay", "от ELmusic", "от E180", "от Dj Borzyj", "от Dizell", "от dima360", "от DiLLeR", "от Devlad", "от DeviL", "от Demon2901", "от danis92", "от Cybertron", "от Crusader3000", "от Constin", "от CMEGroup", "от CinemaStreet", "от ChuckSite", "от cdman", "от BTT-TEAM", "от Brux", "от breznev94(stalone)", "от BigMOVIEGroup & Hurtom", "от BigMOVIEGroup", "от BigFANGroup", "от BestSound ExKinoRay", "от Bagvell", "от AndrewWhite", "от @PD.&.KAMO", "от 5 pluh", "от 2ndra", "от olmier", "от LAMPO4KA", "от 25KADR", "от PskovLine", "от SENATORiNFO-TEAM", "от Kinobomond", "от WIDDER", "от Кубик в кубе", "от Anton299", "от bxpx", "от R.G. PLAGUE", "by MD-TEAM BigFanGroup KVNforAll", "от HELLWOOD", "от KalliostroV", "от Cinema-group info", "от ELEKTRI4KA", "от Werdog", "от Kubik.v.Kube", "от KVNforAll", "от «Киномании»", "by ANDROZZZ", "от @PD KaMo", "от BUTOVOgroup", "от BeeFilm", "от ENGINEER", "от KiNOFACK", "by Xp_Dron", "от 1001Cinema", "от Zt", "by MYDIMKA", "от Humochka", "by FoolMovie", "от КОля", "by Shituf", "by Ultimat", "от HDTracker", "от FORREST", "от Btpo2l", "by BigFAN", "by Vitek", "by R.G.LanTorrent", "от GoldenShara Studio", "от ceo54", "от MaLLleHbKa", "от TOP", "by N!CK", "от Studdio", "от machete96", "by Tonic-RuUu (BigFANGroup)", "от Dimasty", "от Кинконга", "от btpo2l", "от HiDt", "от zim2001", "от Nemo24", "by Oleggutsalo", "от a1chem1st", "от darktech", "от Fartuna158", "от -=ynkas=-", "от MEGASHARA", "by FEAR", "от Шмель", "by Axwel", "от Zlatan83", "от FilmGate", "от Filmgate", "от КГКМ BigFANGroup", "от LTarik", "от POkEmON", "от R.G. R3PacK", "от BigFAN Group", "by DivX (2010) PC", "by EGO team", "by Глюк", "by KVNforAll Veloboss", "by VIDEODB", "by Kelpie", "by Zicos", "by iolegv-RuUu (BigFANGroup)", "by ~Romych~", "by KVNforAll", "by гаврила", "by MD-TEAM KVNforAll", "by Igoraky", "by Neofilm", "от Кинозал.ТВ", "от AudioZona-X", "от Running Man", "от HQRips", "от ANDROZZZ-RuUu", "by ua2004", "by mdteam", "by Iriy", "от Русфильм", "от Babun", "by DEMIGOD", "by iolegv-RuUu", "от orchidea777", "by Allep", "by iolegv (BigFANGroup)", "от Good-Cinema", "от zmei666", "от Menyk", "by Alex Smit", "by ANDROZZZ RuUu (BigFANGroup)", "от YoFilm", "от blackmambo", "от Lexor", "от iriston", "от genafon", "от vladvld", "by iolegv RuUu (BigFANGroup)", "от Yuraflash", "от Postal", "от BOGUSH", "by MD-TEAM BigFANGroup KVNforAll", "от m4r5", "by tak prosto");

$tho = array();

$name = trim(str_ireplace($ine, $tho, $name));

//die($name);


preg_match_all('/<td>(.*?)<\/td>/is', $date, $dltr); /// в $dnik[0] искать связанные торренты
$cat_like = strip_tags(utf8_to_win($dltr[0][3]));


if (stristr($name,'DVDRIP') || stristr($cat_like,'фильмы'))
$catid="13"; /// Фильмы / AVI

elseif (stristr($cat_like,'телевизор') || stristr($name,'TVRip'))
$catid="22"; /// TV / Документалки 

elseif (stristr($cat_like,'Сериалы'))
$catid="11"; /// Сериалы

//elseif (stristr($name,'seriali'))
//$catid="14"; /// Фильмы / HDTV / HD / Blu-Ray

elseif (stristr($cat_like,'Музыка') || stristr($name,'MP3') || stristr($name,'FLAC'))
$catid="10"; /// Музыка / Мультимедиа 

elseif (stristr($cat_like,'Игры'))
$catid="5"; /// Игры / ПК

elseif (stristr($name,'Аниме'))
$catid="12"; /// Аниме 

elseif (stristr($name,'Мультипликация'))
$catid="6"; /// Мультфильмы 

elseif (stristr($name,'JPEG') || stristr($name,'JPG') || stristr($name,'обои') || stristr($name,'HQ'))
$catid="25"; /// Картинки / Фото

elseif (stristr($cat_like,'софт'))
$catid="26"; /// Софт / Windows

//elseif (stristr($name,'seriali'))
//$catid="8"; /// PSP

elseif (stristr($name,'WMV') || stristr($name,'КПК') || stristr($name,'MP4') || stristr($cat_like,'юмор'))
$catid="18"; /// Клипы / Ролики / Приколы

elseif (stristr($cat_like,'книги') || stristr($name,'PDF'))
$catid="16"; /// книги

elseif (stristr($name,'DVD'))
$catid="15"; /// DVD / Фильмы
 
else
$catid="4";

//echo $cat_like;

//echo " и $catid";





//////////////////////////////////////
preg_match('/<td>(.*?)<\/td>/is', $dnik[0], $dligh); /// в $dnik[0] искать связанные торренты
//print_r($dligh);

$desc = utf8_to_win($dligh[0]);

$descorig = $desc;


if (empty($desc) || empty($name)){

sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
echo "Внимание пусто в поле название или описание.";
echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', $nu);</script>";
die;
}

if ((stristr($desc,'502') && stristr($desc,'gateway')) || (stristr($name,'bad') && stristr($name,'gateway')) ){

//sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
echo "Внимание запрос превышен, и выдал ошибку, проверка скачивании через пару секунд";
echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', 30000);</script>";
die;
}

/**
<a href=\"radikal.ru/ .jpg.html
(http:\/\/[^\s'\"<>]+(\.(jpg|jpeg|gif|png)))
**/
//// ищем ссылки от радикала они же изображения
preg_match_all("/<a href=\"(http:\/\/[^\s'\"<>]+(\.(html)))\" target=\"_blank\">/is", $descorig, $ddc);
//// ищем ссылки от радикала они же изображения
$scriarra = $ddc[1];
//print_r($ddc[1]);
$scriarra = array_unique($scriarra); /// удаляем дубликаты

preg_match_all("/<img.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>/i", $descorig, $ddold);
//print_r($ddold[1]);

if (count($ddold[1])){
foreach ($ddold[1] as $pisql){
$scriarra[] = $pisql;
}
}

$scriarra = array_unique($scriarra); /// удаляем дубликаты

//print_r($ddold[0]);
//die;

if (count($scriarra)){
echo "Подготовка скриншотов к проверке (".count($scriarra).")<br>";

//print_r($scrins); //+
////////////////////////////////////////// мод скринов от tesla tt
$xpi=1; $xpio=0;

foreach ($scriarra AS $pic) {

$pic=htmlentities(trim($pic));

////////////////////
$araw = array("jpg.html","gif.html","bmp.html","png.html","jpeg.html");
$aran = array("jpg","gif","bmp","png","jpeg");

$pic = trim(str_ireplace($araw, $aran, $pic));

if ($xpi<5){
/// sendpic и funkyimg.com
if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $pic)){

if (list($width, $height) = @getimagesize($pic)) {
if ($xpi<5 && ($width>=300 && $height>=150 && $width>=$height)){

$updateset[] = "picture$xpi = ".sqlesc($pic);

echo "Добавлен $pic<br>";
++$xpio;
++$xpi;
} elseif($width>=100 && $width<=150 && $height>=150 && $height<=1500 && $width<$height){
$poster = $pic;
}
}
if ($width>=50 && $height>=200 && $width<$height)
$imagesff = $pic;


unset($pic);
}
/////else echo "Удаляем лишний $pic и $width и $height<br>";
}

}
}
////////////////////// постер и скриншоты //////////////////////





//$desc2 = preg_replace('/<a target=\"_blank\" href=\"(.*)\">(.*?)<\/a>/is', "\\1 !!!", $desc2);

$desc = preg_replace('/<a href="\/tag\/(.*?)" target="_blank">(.*?)<\/a>/si', "\\2", $desc);
///<a href="/tag/4/фантастика" target="_blank">

//die($desc);

$desc = preg_replace('/<b>(.*?)<\/b\>/is', "[b]\\1[/b]", $desc);
$desc = preg_replace('/<u>(.*?)<\/u\>/is', "[u]\\1[/u]", $desc);


//$desc = preg_replace('/<font color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])>(.*?)<\/font\>/is', "\\2", $desc);


$desc = preg_replace('/<div.*?>(.*?)<\/div\>/is', "\\2", $desc);



$desc = preg_replace('/<textarea+(.*?)>(.*?)<\/textarea>/is', "[spoiler=Скриншоты]\\2[/spoiler]", $desc);

$desc = preg_replace("/<a.*?href=\"(http:\/\/[^()<>\s]+?(\.(gif|jpg|jpeg|png)))\"[^>]*?>(.*?)<\/a>/i", "[img]\\1[/img]\n", $desc);

$desc = preg_replace("/<a.*?href=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>(.*?)<\/a>/i", "[url=\\1]\\2[/url]\n", $desc);


$desc = preg_replace("/<img.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>/i", "[img]\\1[/img]\n", $desc);

$desc = preg_replace("/<var.*?title=\"(http:\/\/[^()<>\s]+?)\"[^>]*?><\/var>/i", "[img]\\1[/img]\n", $desc);



//$araw = array("jpg.html","gif.html","bmp.html","png.html","jpeg.html");
//$aran = array("jpg","gif","bmp","png","jpeg");

//$desc = str_replace($araw, $aran, $desc);

$desc = trim(strip_tags($desc));

//$desc = trim(str_replace("\n\n\n", "\n", $desc));
//$desc = trim(str_replace("\n\n\n\n", "\n\n", $desc));

///echo format_comment($desc);
///////////////////////////////////////////////////

function get_torrent($id=false,$path,$row_id) {
///$infohash=get_torrent($next_id,$roid,$row_arr["id"]);
$nu=rand(12000,100000);

$path="/download/".$path;

//http://www.rutor.org/download/65835
$referer = "http://www.rutor.org/";
$host = "rutor.org";

if (!$id){
echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', $nu);</script>";
die("не id в get_torrent");
}

// Устанавливаем соединение
//echo "Устанавливаем соединение c .torrent файлом - $path<br>";
$fp = fsockopen($host, 80, $errno, $errstr, 25) or die("Нет подлючения в get_torrent функции .... <script>setTimeout('document.location.href=\"parser_rutorg.php\"', 15000);</script>");
socket_set_timeout($fp,5,0); //Назначаем таймаут (100 - это секунды)

// Проверяем успешность установки соединения

if ($fp) {
// Данные HTTP-запроса
$data = "";
// Заголовок HTTP-запроса
$headers = "GET $path HTTP/1.0\r\n";
$headers .= "Host: $host\r\n";
$headers .= "Connection: Close\r\n";
$headers .= "\r\n";

///echo "Отправка данных <br>";
fwrite($fp, $headers.$data);

while (!feof($fp)) {
$line = fgets(($fp), 1024);

$date.=$line;
}
fclose($fp);
}

if (strlen($date)>="724148" || empty($date))
{

//if (!empty($next_id))
//unlink(ROOT_PATH."torrents/".$next_id.".torrent");

sql_query("DELETE FROM files WHERE torrent = '$next_id'");

sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_id."'") or sqlerr(__FILE__, __LINE__);


echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', $nu);</script>";
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', 20000);</script>";
die("ошибка размер файла торрент более 700 КБ, не разрешенно.");
}

//die($date);
//echo "Получение, обработка данных<br>";
list ($data_1,$data_2) = explode("\r\n\r\n", $date);

//die($path);

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
//if (!empty($dict['value']['info']['value']['private'])){
//die(" Приватный торрент файл - <a href=http://www.rutor.org/torrent/$row_id/>$row_id</a> перепроверить сразу.!");
//}

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
echo "Обновление о кол файлов внутри торрента<br>";
}
else
{

///if (!empty($id))
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
sql_query("DELETE FROM torrents WHERE torrent = '$id'");

sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_id."'") or sqlerr(__FILE__, __LINE__);


echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', 5000);</script>";
die("ошибка размер файла 0 размер.");
}
//die(MD5XA);

//echo "Заносим в базу все необходимые данные <br>";
}

if (empty($infohash))
$infohash = false;

return $infohash;
}



///////////////////////////
$ret = sql_query("SHOW TABLE STATUS LIKE 'torrents'"); 
$row = mysql_fetch_array($ret); 
$next_id = $row['Auto_increment']; 
//$roid

$infohash=get_torrent($next_id,$roid,$row_arr["id"]);

if ($infohash==false){
	
sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', 10000);</script>";
die("Невозможно прочитать внешний .torrent файл. $infohash");
}
////
preg_match("/<img.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>/i", $descorig, $ddc);

//print_r($ddc);

if (!empty($poster)){ /// главный кандидат
$end_image=$poster;
$image0=$poster;
} elseif(!empty($ddc[1])){ /// второстепенный кандидат если главный не пошел
$end_image=$ddc[1];
$image0=$ddc[1];
}

$uploaddir = ROOT_PATH."torrents/images/";
//die($poster);

if (!empty($end_image))
$image=@file_get_contents($end_image);

if (!empty($image) && strlen($image)>=20) {
$ifileimg = $next_id.'.'.end(explode('.',$end_image));
@unlink($uploaddir.$ifileimg);
//copy($image0, $uploaddir.$ifileimg);

$sf2=$uploaddir.$ifileimg; 
$fpsf2=fopen($sf2,"a+"); 
//echo ($data_2);
//die($data_2);
fputs($fpsf2,$image); 
fclose($fpsf2); 
}


echo "Найденный хеш: $infohash <br>";


//////////////////////////// авторские раздачи - запрет заливки
$num_license = get_row_count("license","WHERE info_hash=".sqlesc($infohash));

if (!empty($num_license)){
sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', 10000);</script>";
echo("Данный файл имеет авторские права, запрещено заливать подобные файлы.");
die;
}
//////////////////////////// авторские раздачи - запрет заливки

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


$visi="yes";

$multut=sqlesc("yes");

$torrent_com = get_date_time() . " граббер торрентов ($host$path).\n";

/*
$one=array("Season ","Seasons ","Episodes ","Episode "," [spoiler=Trailer]","Screen&#039;S","[/b][/u]","[/b] [/u]","[u][b]","[u] [b]","[spoiler=Screenshot]");
$two=array("Cезон ","Cезоны ","Серии ","Серия ","[spoiler=Trailer]","Скриншоты","","","","","\n[spoiler=Скриншоты]");

$desc = trim(str_replace($one, $two, $desc));
*/

if (!empty($end_image))
$desc = str_ireplace("[img]".$end_image."[/img]\n", "", $desc);


$desc = str_ireplace($ine, $tho, $desc);

$desc = str_replace("\n\n\n", "\n", $desc);
$desc = trim(str_replace("\n\n\n\n", "\n\n", $desc));
$desc = preg_replace("/\[url=(http:\/\/[^()<>\s]+?)\]{1,2}\[\/url\]/is", "", $desc); /// убираем тег url если внутри него пустота
//echo "$desc";
//die;
$descr=htmlspecialchars_uni($desc);
$torrent=htmlspecialchars_uni(trim($name));

$own="92";
//die($infohash);
///var_dump($desc);


$ret = sql_query("INSERT INTO torrents ( owner, visible, image1, info_hash, name, descr, torrent_com, category, added, last_action, multitracker) VALUES (" . implode(",", array_map("sqlesc", array($own, $visi, $ifileimg, $infohash, $torrent, $descr, $torrent_com, $catid))) . ", '" . get_date_time() . "', '" . get_date_time() . "',".$multut.")"); 

$id = mysql_insert_id();

if ($id){


echo $torrent." id: ".$id;
echo "<br><br>";

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
echo("Не могу скопировать торрент в папку");

@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', $nu);</script>";
}
} else {

echo "не выдано id для раздачи<br>";
//die(MD5XA);
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");

sql_query("DELETE FROM files WHERE torrent = '$next_id'");

sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);


$resnex = sql_query("SELECT id,picture1,picture4,descr,image1,name,torrent_com FROM torrents WHERE info_hash=".sqlesc($infohash)) or sqlerr(__FILE__,__LINE__);

$row_nex = mysql_fetch_array($resnex);

if (!empty($row_nex["id"]) && !empty($descr) && $descr<>$row_nex["descr"] && stristr($row_nex["torrent_com"],"граббер торрентов ($host$path)")){
$updatedescr[] = "descr = ".sqlesc($descr);
echo "Принудительно обновляю описание старых уже залитых этим же граббером раздачу <br>";
sql_query("UPDATE torrents SET " . implode(",", $updatedescr) . " WHERE info_hash=".sqlesc($infohash)) or sqlerr(__FILE__, __LINE__);
}


if ($row_nex["descr"]<$descr && !empty($descr)){

sql_query("UPDATE torrents SET descr=".sqlesc($descr)." WHERE info_hash=".sqlesc($infohash)." AND moderatedby='92'") or sqlerr(__FILE__, __LINE__);
echo "обновлено описание. <br>";
}


if (!empty($row_nex["id"]) && empty($row_nex["picture1"]) && empty($row_nex["picture4"]) && $infohash && count($updateset)){

echo "Обновление скриншотов, ";


if (empty($row_nex["image1"]) && !empty($ifileimg_new)){

$uploaddir = ROOT_PATH."torrents/images/";

if (!empty($ifileimg_new)) {
$ifileimg_ntw = $row_nex["id"].'.'.end(explode('.',$ifileimg_new));
rename("torrents/images/".$ifileimg_new, "torrents/images/".$ifileimg_ntw);
echo "Переименовываем картинку на $ifileimg_ntw,";
$updatesetpic[] = "image1 = ".sqlesc($ifileimg_ntw);
}

sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE info_hash=".sqlesc($infohash)." AND moderatedby='92'") or sqlerr(__FILE__, __LINE__);

if (count($updatesetpic) && $infohash){
echo "постера, ";
sql_query("UPDATE torrents SET " . implode(",", $updatesetpic) . " WHERE info_hash=".sqlesc($infohash)." AND moderatedby='92'") or sqlerr(__FILE__, __LINE__);

}
echo " у заменяющего торрента (который уже есть в базе)<br>";
}

}


echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', $nu);</script>";
die("ошибка возможно торрент уже есть в базе ".(empty($id)? " <b>Пустое ID с БАЗЫ (при вставке) - <a href=\"details.php?id=$row_nex[id]\">$row_nex[name]</a>":"")."");
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
///sql_query("DELETE FROM files WHERE torrent = '$id'");
$ca = array();
foreach ($filelist as $file) {
$file[0]=utf8_to_win($file[0]);
$size=$size+$file[1];
$ca[] = $file[0];
//echo $file[1]."<br>";
}

}


$mycateyrogy = parse_arrray_cat($ca, $size);
if ($mycateyrogy <> false)
$updateset[] = "category = " . sqlesc($mycateyrogy);


$updateset[] = "numfiles = " . sqlesc(count($filelist));
$updateset[] = "size = " . sqlesc($size);

$updateset[] = "moderated = 'yes'";
$updateset[] = "moderatedby = ".sqlesc("92");
$updateset[] = "moderatordate = ".sqlesc(get_date_time());



//////////////// ищем разбираем и вставляем теги в раздачу ////////////////
preg_match_all('/<a href="\/tag\/(.*?)" target="_blank">(.*?)<\/a>/si', $descorig, $dltags); /// в $dnik[0] искать связанные торренты
//print_r($dltags[2]);
$tagsarray = $dltags[2];

if (count($tagsarray)){
$tagsql = array();
echo "Забираем теги для раздачи...<br>";


$tagsarray=array_unique($tagsarray); /// удаляем дубликаты

foreach ($tagsarray as $tagi){
	
$tagi = tolower(trim(htmlspecialchars_uni($tagi)));

if (!empty($tagi)){
echo "Добавляем тег: $tagi <br>";
sql_query("INSERT INTO tags (category, name, howmuch,added) VALUES (".sqlesc($catid).", ".sqlesc($tagi).", 1, ".sqlesc(get_date_time()).")");

sql_query("UPDATE tags SET howmuch=howmuch+1 WHERE name LIKE ".sqlesc($tagi)) or sqlerr(__FILE__, __LINE__);
$tagsql[] = $tagi;
}

}





if (count($tagsql))
$updateset[] = "tags = ".sqlesc(implode(",", $tagsql));
}
//////////////// ищем разбираем и вставляем теги в раздачу ////////////////




///$updateset[] = "info_hash = " . sqlesc($infohash);
sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = $id") or sqlerr(__FILE__, __LINE__);
echo "<title>$torrent</title>Успешно залит.<br><br>";

sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
}







/////////////// собираем id из связанных раздач ///////////////
preg_match('/<div id="index">(.*?)<\/div>/is', $date, $dlids); /// в $dnik[0] искать связанные торренты
$razbornwe = ($dlids[0]);

if (!empty($razbornwe)){

echo "Есть блок похожие раздачи: собираем id от них....<br>";
preg_match_all('/<a class="downgif" href="\/download\/(.*?)">/is', $razbornwe, $dli); /// для ссылок внутренних
//<a class="downgif" href="/download/66254">
$cou = $dli[1];

if (count($cou)){
foreach ($cou as $k) {
if (is_valid_id($k)){
sql_query("INSERT INTO trutgrab (details, work) VALUES (".sqlesc($k).",".sqlesc("1").")");
$newid = mysql_insert_id();
if ($newid)
echo "<font color=green><b>".$k."</b></font> <b>добавлено</b><br>";
else
echo "<font color=red><i>".$k."</i></font> <i>уже в базе</i><br>";
}}}}
/////////////// собираем id из связанных раздач ///////////////




if (stristr($torrent,'by ') || stristr($torrent,'от ')){
$fpsf = fopen("cache/ru_arraybad.txt","a+"); 
fputs($fpsf,$torrent."\n"); 
fclose($fpsf);
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

$arr = sql_query("SELECT COUNT(*) AS numgrab,(SELECT COUNT(*) FROM trutgrab WHERE work='0') AS grabost FROM trutgrab WHERE work='1'");
$row_arr = mysql_fetch_array($arr);

$procents="<b title=\" торрентов залито - ".number_format($row_arr["numtore"])."\">Завершенно</b>: ".number_format(100-number_format(100 * (1 - ($row_arr["grabost"] / ($row_arr["grabost"]+$row_arr["numgrab"]))),2),2)."% (<b>ВЗ</b>:".number_format($row_arr["grabost"]).":<b>ОСТ</b>:".number_format($row_arr["numgrab"]).":<b>ВС</b>:".number_format($row_arr["grabost"] + $row_arr["numgrab"]).")";

$time = number_format($seconds,-2);


echo "$procents <br> Время обработки $time

<script>setTimeout('document.location.href=\"parser_rutorg.php\"', ".$nu.");</script>";

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