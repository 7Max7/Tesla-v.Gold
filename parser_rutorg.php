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

//// ������� ////
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
//// ������� ////



/*
--
-- ��������� ������� `trutgrab`
--

CREATE TABLE IF NOT EXISTS `trutgrab` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `details` int(100) NOT NULL DEFAULT '0',
  `work` int(10) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `one` (`details`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

--
-- ���� ������ ������� `trutgrab`
--
*/

$host="rutor.org";

if (isset($_GET["page"])){

$idpage = (int) $_GET["page"];

$white = (int) $_GET["white"];

// ������������� ����������
$fp = fsockopen($host, 80, $errno, $errstr, 25) or die("��� ���������� ��� ������ id <script>setTimeout('document.location.href=\"parser_rutorg.php?page=$idpage&white=$white\"', 5000);</script>"); 
// ��������� ���������� ��������� ����������
if ($fp) {
// ��������� HTTP-�������
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
// ���������� HTTP-������ �������
@fwrite($fp, $headers.$data);
$date = "";
while (!feof($fp)){
$line = fgets($fp, 1024);
$date.=$line;
}
@fclose($fp);
}

preg_match('/<table width="100%">(.*?)<\/table>/is', $date, $dbrowse);

echo "���� ������: �������� id �� ���....<br>";

preg_match_all('/<a class="downgif" href="\/download\/(.*?)">/is', $dbrowse[0], $dli); /// ��� ������ ����������
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

echo "��������� <font color=green><b>$numplus</b></font> �� <font color=red><i>$numminus</i></font> ��� ����.<br>";
echo "<title>�:$numplus C:$numminus</title>";

++$idpage;

if (empty($numplus))
++$white;


if (($numminus)>0 && $numplus==0 && $white>2)
echo "������, <b>������ �������� ��������</b> ����� 5 ��� <script>setTimeout('document.location.href=\"parser_rutorg.php\"', 5000);</script>";
else
echo "������, ������������ ����� 5 ��� <script>setTimeout('document.location.href=\"parser_rutorg.php?page=$idpage&white=$white\"', 5000);</script>";

die;
}





$arr = sql_query("SELECT * FROM trutgrab WHERE work='1' ORDER BY id DESC LIMIT 1");
$row_arr = mysql_fetch_array($arr);

$roid = $row_arr["details"];

if (!empty($_GET["idget"]))
$roid = (int) $_GET["idget"];

if (empty($roid)){
die("������������ �� �������� ������ id, ������������ ����� 5 ��� <script>setTimeout('document.location.href=\"parser_rutorg.php?page=0\"', 5000);</script>");

//sql_query("UPDATE `trutgrab` SET `work` = '1'"); 
} else
echo "���� id � ����: $roid <br>";



$path="/torrent/$roid/";

define('MD5XA', md5($host.$path));


//http://www.rutor.org/torrent/65835/

// ������������� ����������
$fp = fsockopen($host, 80, $errno, $errstr, 25) or die("��� ���������� <script>setTimeout('document.location.href=\"parser_rutorg.php\"', 10000);</script>"); 
// ��������� ���������� ��������� ����������
if ($fp) {
// ��������� HTTP-�������
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
// ���������� HTTP-������ �������

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
"�� RollHD", "�� HELLRAiSER", "�� HELLYWOOD", "�� CGInfo", "�� HQ-ViDEO", "�� Shinobi", "�� Infernum", "�� Friends-Club", "�� FRiENDS-Club", "�� DoroshDan", "�� PlanetaUA", "�� ����", "by Veloboss", "by GENADIY", "by HQ-ViDEO", "by BigFan", "�� R.G. PlayBay", "�� RomeoWhite", "by RuUu", "by maksnev82", "�� doberman", "�� Drakula", "by demon�����", "�� FreeHD", "�� kubik v kube", "�� FS-Media", "�� Kvadratmalevicha", "by parole", "by �������� (BigFANGroup)", "by R.G.������4�", "by the sun", "by OZAGIS", "by BigFANGroup", "by Infineon", "�� R.G ReCoding", "by R.G. 48 seconds", "�� DHT-Movies", "by FS-Media", "by Arow Malossi", "by Kino Pirate", "�� LPF TV", "�� Overlord", "�� 0day ", "�� HAZARD'A", "�� R.G Repacker's", "by R.G.R3PacK", "�� CtrlHD", "�� ESiR", "by 4Euro", "�� New-TrackerUA", "�� M.I.B", "�� EuReKa", "�� Linkorn", "�� iolegv", "�� Neo-Sound.ru", "�� SAMLL-RiP", "�� CHDBits", "�� RI4MOND", "�� Smart's studios", "�� EbP", "�� TORRENTSES", "by NK", "by bizarrevic", "by VANGA", "by Basilevs", "�� trexmernii", "by XENON", "by morganvolter", "by HDClub", "�� MadChester ����� � ���� NovaFiLM", "�� EuReKA", "by ELEKTRI4KA", "by 5.1", "by SneGGs", "�� Oleggutsalo", "�� zackary", "by VIDODB", "�� SkyeFilmTV", "by Suprug", "by rp0Mk0cTb", "by SDHQ", "�� Doctor No.", "�� SoftClub", "�� TDiTP", "�� �����", "�� F_i_X", "�� 1001 Cinema", "�� Drakul�", "�� K�Mo", "by KaMo", "by Illusion", "�� Psycho-Share", "by X-DIGITAL (BigFANGroup)", "�� InterfaceLift", "�� KaMo", "by Vladas", "by HA3APET PC", "�� RELOADED", "�� ����", "by An Ocean Wave", "by HA3APET", "�� lenpas", "�� W I", "�� KiLR0Y", "�� games_vandal", "�� EPIDEMZ", "by DESAN", "�� ���������", "�� BaibaKo", "�� HD Tracker", "�� -DaRkY-", "�� lev31071977", "by Kommunist", "�� Smart's Studios", "�� Player1993", "�� Overcast", "�� Ildarec014", "�� Bumblebee", "�� SmallRip", "by games vandal", "�� R.G.", "�� SoftClub/EA Russia", "by Andrjuha-Bratan", "by Shel", "�� ������ CGInfo", "by Mr.legal", "by Megashara", "by Padre mp4", "�� Zhmak", "by DJ ART", "�� FoolMovie", "�� iolegv RuUu", "by boban39", "by stalone", "�� �@�o", "�� AllSeries Info CGInfo", "�� Dime HQ-VIDEO", "�� Geralt iz Rivii", "by HDCiub", "by d_kos", "by doberman", "�� HQ-films", "�� epu2009", "by d kos", "by rus-tracker", "�� Mari", "�� VIDEODB", "by Studdio", "�� FRiENDS Club", "by ��������", "by Tiger", "by Virelic", "by AZBUKASOFTA", "�� Lali", "by trexmernii", "�� Instigator-NN", "by R.G. Beautiful Thieves", "�� ��������", "�� 3m1L", "�� NOLIMITS-TEAM", "�� R.G. \"48 seconds\"", "�� ANDROZZZ", "by djip", "�� boss89", "�� StarTorrent", "�� z10yded", "�� ���", "by martin", "by \"R.G. 48 Seconds\"", "�� ������� Suprug", "by qDan", " - �����AM", "�� MashystiK", "�� JoomlaXTC", "�� Joomlapraise", "�� greys2008 Arkadiy", "�� SkeT", "by stalone ( stalone-2)", "by R.O.T.O.R", "by Fool Movie", "by KVNforAll N!CK", "by KVNforAll Ruuu", "by BigSOUNDgroup", "by yuraflash", "by POkEmON", "by v1nt", "�� HotRabbit", "�� ����777", "�� PsihoShare", "�� �����", "by s0lAr", "�� HeDgEhOt", "�� �osmos", "�� R.G.R3PacK", "by 7lobiN", "by dimich", "�� RAMpage CHG", "�� R.G. CodeRs", "�� �������� (BigFANGroup)", "�� ������", "�� ����� | Remastered", "�� �����������", "�� ����������", "�� ������-�����", "�� ������� & Suprug", "�� �����AM", "�� ������", "�� �����-������", "�� ����� ������", "�� ����� � ����", "�� ������", "�� �������SAT", "�� �����PR Studio", "�� ���NN��", "�� ������", "�� Zona-X", "�� YOOtheme", "�� WiKi)", "�� Wegas Digital", "�� wakal", "�� volka", "�� Voland", "�� VO-Production", "�� Vladislav71", "�� ViP", "�� VelArt", "�� vadi", "�� Ultra", "�� UaTeam", "�� TVShows", "�� Traumer", "�� SuperNatural", "�� StopFilm", "�� Spieler", "�� SMALL-RiP", "�� Shevon", "�� �������", "�� Shepards", "�� sergeorg", "�� Sergei7721", "�� RuUu, iolegv & ANDROZZZ", "�� RuUu (BigFANGroup)", "�� RuUu", "�� RUSSOUND", "�� RP4 CHG", "�� RommyFilm", "�� RocketTheme", "�� RiperAM", "�� RIPER.AM", "�� RelizLab", "�� RadioXyLigaN", "�� R.G.Spieler", "�� R.G. ��������", "�� R.G. ��������", "�� R.G. Repackers Bay", "�� R.G. ReCoding", "�� R.G. Catalyst", "�� P������", "�� Punisher", "�� PowerTracker", "�� Pasha74", "�� ParadiSe", "�� OneFilm", "�� Oday", "�� NovaFilm", "�� NewStudio", "�� NaumJ", "�� Music80", "�� mk2", "�� mi24", "�� Martin", "�� maksnev82", "�� m0j0", "�� Lunatik", "�� Lukavaya", "�� LostFilm.tv", "�� LostFilm", "�� Kosmos", "�� Kino Pirate", "�� Kerob", "�� K1RgH0ff", "�� iolegv-RuUu", "�� Ildarec-014", "�� HQGROUP", "�� HQCLUB", "�� HDReactor", "�� HDGANG", "�� HDCLUB", "�� HD-Zona-X", "�� Hansmer", "�� Gravi-TV", "�� Gellard", "�� G1AR", "�� FreeTorrents-UA", "�� FoC", "�� FLINTFILMS", "�� FinaRGoT", "�� Fenixx)", "�� Fenixx", "�� ExKinoRay.TV", "�� ExKinoRay", "�� ELmusic", "�� E180", "�� Dj Borzyj", "�� Dizell", "�� dima360", "�� DiLLeR", "�� Devlad", "�� DeviL", "�� Demon2901", "�� danis92", "�� Cybertron", "�� Crusader3000", "�� Constin", "�� CMEGroup", "�� CinemaStreet", "�� ChuckSite", "�� cdman", "�� BTT-TEAM", "�� Brux", "�� breznev94(stalone)", "�� BigMOVIEGroup & Hurtom", "�� BigMOVIEGroup", "�� BigFANGroup", "�� BestSound ExKinoRay", "�� Bagvell", "�� AndrewWhite", "�� @PD.&.KAMO", "�� 5 pluh", "�� 2ndra", "�� olmier", "�� LAMPO4KA", "�� 25KADR", "�� PskovLine", "�� SENATORiNFO-TEAM", "�� Kinobomond", "�� WIDDER", "�� ����� � ����", "�� Anton299", "�� bxpx", "�� R.G. PLAGUE", "by MD-TEAM BigFanGroup KVNforAll", "�� HELLWOOD", "�� KalliostroV", "�� Cinema-group info", "�� ELEKTRI4KA", "�� Werdog", "�� Kubik.v.Kube", "�� KVNforAll", "�� ����������", "by ANDROZZZ", "�� @PD KaMo", "�� BUTOVOgroup", "�� BeeFilm", "�� ENGINEER", "�� KiNOFACK", "by Xp_Dron", "�� 1001Cinema", "�� Zt", "by MYDIMKA", "�� Humochka", "by FoolMovie", "�� ����", "by Shituf", "by Ultimat", "�� HDTracker", "�� FORREST", "�� Btpo2l", "by BigFAN", "by Vitek", "by R.G.LanTorrent", "�� GoldenShara Studio", "�� ceo54", "�� MaLLleHbKa", "�� TOP", "by N!CK", "�� Studdio", "�� machete96", "by Tonic-RuUu (BigFANGroup)", "�� Dimasty", "�� ��������", "�� btpo2l", "�� HiDt", "�� zim2001", "�� Nemo24", "by Oleggutsalo", "�� a1chem1st", "�� darktech", "�� Fartuna158", "�� -=ynkas=-", "�� MEGASHARA", "by FEAR", "�� �����", "by Axwel", "�� Zlatan83", "�� FilmGate", "�� Filmgate", "�� ���� BigFANGroup", "�� LTarik", "�� POkEmON", "�� R.G. R3PacK", "�� BigFAN Group", "by DivX (2010) PC", "by EGO team", "by ����", "by KVNforAll Veloboss", "by VIDEODB", "by Kelpie", "by Zicos", "by iolegv-RuUu (BigFANGroup)", "by ~Romych~", "by KVNforAll", "by �������", "by MD-TEAM KVNforAll", "by Igoraky", "by Neofilm", "�� �������.��", "�� AudioZona-X", "�� Running Man", "�� HQRips", "�� ANDROZZZ-RuUu", "by ua2004", "by mdteam", "by Iriy", "�� ��������", "�� Babun", "by DEMIGOD", "by iolegv-RuUu", "�� orchidea777", "by Allep", "by iolegv (BigFANGroup)", "�� Good-Cinema", "�� zmei666", "�� Menyk", "by Alex Smit", "by ANDROZZZ RuUu (BigFANGroup)", "�� YoFilm", "�� blackmambo", "�� Lexor", "�� iriston", "�� genafon", "�� vladvld", "by iolegv RuUu (BigFANGroup)", "�� Yuraflash", "�� Postal", "�� BOGUSH", "by MD-TEAM BigFANGroup KVNforAll", "�� m4r5", "by tak prosto");

$tho = array();

$name = trim(str_ireplace($ine, $tho, $name));

//die($name);


preg_match_all('/<td>(.*?)<\/td>/is', $date, $dltr); /// � $dnik[0] ������ ��������� ��������
$cat_like = strip_tags(utf8_to_win($dltr[0][3]));


if (stristr($name,'DVDRIP') || stristr($cat_like,'������'))
$catid="13"; /// ������ / AVI

elseif (stristr($cat_like,'���������') || stristr($name,'TVRip'))
$catid="22"; /// TV / ������������ 

elseif (stristr($cat_like,'�������'))
$catid="11"; /// �������

//elseif (stristr($name,'seriali'))
//$catid="14"; /// ������ / HDTV / HD / Blu-Ray

elseif (stristr($cat_like,'������') || stristr($name,'MP3') || stristr($name,'FLAC'))
$catid="10"; /// ������ / ����������� 

elseif (stristr($cat_like,'����'))
$catid="5"; /// ���� / ��

elseif (stristr($name,'�����'))
$catid="12"; /// ����� 

elseif (stristr($name,'��������������'))
$catid="6"; /// ����������� 

elseif (stristr($name,'JPEG') || stristr($name,'JPG') || stristr($name,'����') || stristr($name,'HQ'))
$catid="25"; /// �������� / ����

elseif (stristr($cat_like,'����'))
$catid="26"; /// ���� / Windows

//elseif (stristr($name,'seriali'))
//$catid="8"; /// PSP

elseif (stristr($name,'WMV') || stristr($name,'���') || stristr($name,'MP4') || stristr($cat_like,'����'))
$catid="18"; /// ����� / ������ / �������

elseif (stristr($cat_like,'�����') || stristr($name,'PDF'))
$catid="16"; /// �����

elseif (stristr($name,'DVD'))
$catid="15"; /// DVD / ������
 
else
$catid="4";

//echo $cat_like;

//echo " � $catid";





//////////////////////////////////////
preg_match('/<td>(.*?)<\/td>/is', $dnik[0], $dligh); /// � $dnik[0] ������ ��������� ��������
//print_r($dligh);

$desc = utf8_to_win($dligh[0]);

$descorig = $desc;


if (empty($desc) || empty($name)){

sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
echo "�������� ����� � ���� �������� ��� ��������.";
echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', $nu);</script>";
die;
}

if ((stristr($desc,'502') && stristr($desc,'gateway')) || (stristr($name,'bad') && stristr($name,'gateway')) ){

//sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
echo "�������� ������ ��������, � ����� ������, �������� ���������� ����� ���� ������";
echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', 30000);</script>";
die;
}

/**
<a href=\"radikal.ru/ .jpg.html
(http:\/\/[^\s'\"<>]+(\.(jpg|jpeg|gif|png)))
**/
//// ���� ������ �� �������� ��� �� �����������
preg_match_all("/<a href=\"(http:\/\/[^\s'\"<>]+(\.(html)))\" target=\"_blank\">/is", $descorig, $ddc);
//// ���� ������ �� �������� ��� �� �����������
$scriarra = $ddc[1];
//print_r($ddc[1]);
$scriarra = array_unique($scriarra); /// ������� ���������

preg_match_all("/<img.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>/i", $descorig, $ddold);
//print_r($ddold[1]);

if (count($ddold[1])){
foreach ($ddold[1] as $pisql){
$scriarra[] = $pisql;
}
}

$scriarra = array_unique($scriarra); /// ������� ���������

//print_r($ddold[0]);
//die;

if (count($scriarra)){
echo "���������� ���������� � �������� (".count($scriarra).")<br>";

//print_r($scrins); //+
////////////////////////////////////////// ��� ������� �� tesla tt
$xpi=1; $xpio=0;

foreach ($scriarra AS $pic) {

$pic=htmlentities(trim($pic));

////////////////////
$araw = array("jpg.html","gif.html","bmp.html","png.html","jpeg.html");
$aran = array("jpg","gif","bmp","png","jpeg");

$pic = trim(str_ireplace($araw, $aran, $pic));

if ($xpi<5){
/// sendpic � funkyimg.com
if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $pic)){

if (list($width, $height) = @getimagesize($pic)) {
if ($xpi<5 && ($width>=300 && $height>=150 && $width>=$height)){

$updateset[] = "picture$xpi = ".sqlesc($pic);

echo "�������� $pic<br>";
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
/////else echo "������� ������ $pic � $width � $height<br>";
}

}
}
////////////////////// ������ � ��������� //////////////////////





//$desc2 = preg_replace('/<a target=\"_blank\" href=\"(.*)\">(.*?)<\/a>/is', "\\1 !!!", $desc2);

$desc = preg_replace('/<a href="\/tag\/(.*?)" target="_blank">(.*?)<\/a>/si', "\\2", $desc);
///<a href="/tag/4/����������" target="_blank">

//die($desc);

$desc = preg_replace('/<b>(.*?)<\/b\>/is', "[b]\\1[/b]", $desc);
$desc = preg_replace('/<u>(.*?)<\/u\>/is', "[u]\\1[/u]", $desc);


//$desc = preg_replace('/<font color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])>(.*?)<\/font\>/is', "\\2", $desc);


$desc = preg_replace('/<div.*?>(.*?)<\/div\>/is', "\\2", $desc);



$desc = preg_replace('/<textarea+(.*?)>(.*?)<\/textarea>/is', "[spoiler=���������]\\2[/spoiler]", $desc);

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
die("�� id � get_torrent");
}

// ������������� ����������
//echo "������������� ���������� c .torrent ������ - $path<br>";
$fp = fsockopen($host, 80, $errno, $errstr, 25) or die("��� ���������� � get_torrent ������� .... <script>setTimeout('document.location.href=\"parser_rutorg.php\"', 15000);</script>");
socket_set_timeout($fp,5,0); //��������� ������� (100 - ��� �������)

// ��������� ���������� ��������� ����������

if ($fp) {
// ������ HTTP-�������
$data = "";
// ��������� HTTP-�������
$headers = "GET $path HTTP/1.0\r\n";
$headers .= "Host: $host\r\n";
$headers .= "Connection: Close\r\n";
$headers .= "\r\n";

///echo "�������� ������ <br>";
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
die("������ ������ ����� ������� ����� 700 ��, �� ����������.");
}

//die($date);
//echo "���������, ��������� ������<br>";
list ($data_1,$data_2) = explode("\r\n\r\n", $date);

//die($path);

$ifilename = ROOT_PATH."torrents/txt/".MD5XA.".torrent";

$sf=$ifilename; 
$fpsf=fopen($sf,"a+"); 
//echo ($data_2);
//die($data_2);
fputs($fpsf,$data_2); 
fclose($fpsf); 
//echo "��������� .torrent ���� <br>";


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
//die(" ��������� ������� ���� - <a href=http://www.rutor.org/torrent/$row_id/>$row_id</a> ������������� �����.!");
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
echo "���������� � ��� ������ ������ ��������<br>";
}
else
{

///if (!empty($id))
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
sql_query("DELETE FROM torrents WHERE torrent = '$id'");

sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_id."'") or sqlerr(__FILE__, __LINE__);


echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', 5000);</script>";
die("������ ������ ����� 0 ������.");
}
//die(MD5XA);

//echo "������� � ���� ��� ����������� ������ <br>";
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
die("���������� ��������� ������� .torrent ����. $infohash");
}
////
preg_match("/<img.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>/i", $descorig, $ddc);

//print_r($ddc);

if (!empty($poster)){ /// ������� ��������
$end_image=$poster;
$image0=$poster;
} elseif(!empty($ddc[1])){ /// �������������� �������� ���� ������� �� �����
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


echo "��������� ���: $infohash <br>";


//////////////////////////// ��������� ������� - ������ �������
$num_license = get_row_count("license","WHERE info_hash=".sqlesc($infohash));

if (!empty($num_license)){
sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', 10000);</script>";
echo("������ ���� ����� ��������� �����, ��������� �������� �������� �����.");
die;
}
//////////////////////////// ��������� ������� - ������ �������

preg_match_all("/<img.*?src=\"(http:\/\/[^()<>\s]+?)\"[^>]*?>/i", $desc, $ddc);
$link_image=array();/// ��� �������� ��� ������
$xpio = 1;
foreach ($ddc[1] as $ksf) {
	
	
if ($xpio<5){
//stristr($ksf,"radikal") && 
if (list($width, $height) = @getimagesize($ksf)){

if ($width>=300 && $height>=150 && $width>=$height){
$updateset[] = "picture$xpio = ".sqlesc($ksf);
echo "�������� $width � $height - $ksf<br>";
++$xpio;
}
else
{
//echo "$width � $height - $ksf �� ��������!<br>";	
}
}
/*
361 � 500 �� ��������!
*/


$link_image[]=$ksf; /// �������� ��� ������
}
}


$visi="yes";

$multut=sqlesc("yes");

$torrent_com = get_date_time() . " ������� ��������� ($host$path).\n";

/*
$one=array("Season ","Seasons ","Episodes ","Episode "," [spoiler=Trailer]","Screen&#039;S","[/b][/u]","[/b] [/u]","[u][b]","[u] [b]","[spoiler=Screenshot]");
$two=array("C���� ","C����� ","����� ","����� ","[spoiler=Trailer]","���������","","","","","\n[spoiler=���������]");

$desc = trim(str_replace($one, $two, $desc));
*/

if (!empty($end_image))
$desc = str_ireplace("[img]".$end_image."[/img]\n", "", $desc);


$desc = str_ireplace($ine, $tho, $desc);

$desc = str_replace("\n\n\n", "\n", $desc);
$desc = trim(str_replace("\n\n\n\n", "\n\n", $desc));
$desc = preg_replace("/\[url=(http:\/\/[^()<>\s]+?)\]{1,2}\[\/url\]/is", "", $desc); /// ������� ��� url ���� ������ ���� �������
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
echo "��������������� .torrent ...<img src=\"./torrents/images/$ifileimg_new\"><br>";


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
echo "��������������� �������� ...<img src=\"./torrents/images/$ifileimg_new\"><br>";
$updateset[] = "image1 = ".sqlesc($ifileimg_new);

sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = ".$id."") or sqlerr(__FILE__, __LINE__);
}
}
//else
//echo "<img src=\"./torrents/images/$ifileimg\"><br>";

///////////
	
	
	
	
	
//echo "����������� ������������� $torrent ($id) <br>";
$copy=@copy(ROOT_PATH."torrents/txt/".MD5XA.".torrent",ROOT_PATH."torrents/$id.torrent");

if (!$copy){
$copy=@copy(ROOT_PATH."torrents/txt/".MD5XA.".torrent",ROOT_PATH."torrents/$id.torrent");
}

//@unlink($uploaddir.$ifileimg);

if (!$copy){
echo("�� ���� ����������� ������� � �����");

@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', $nu);</script>";
}
} else {

echo "�� ������ id ��� �������<br>";
//die(MD5XA);
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");

sql_query("DELETE FROM files WHERE torrent = '$next_id'");

sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);


$resnex = sql_query("SELECT id,picture1,picture4,descr,image1,name,torrent_com FROM torrents WHERE info_hash=".sqlesc($infohash)) or sqlerr(__FILE__,__LINE__);

$row_nex = mysql_fetch_array($resnex);

if (!empty($row_nex["id"]) && !empty($descr) && $descr<>$row_nex["descr"] && stristr($row_nex["torrent_com"],"������� ��������� ($host$path)")){
$updatedescr[] = "descr = ".sqlesc($descr);
echo "������������� �������� �������� ������ ��� ������� ���� �� ��������� ������� <br>";
sql_query("UPDATE torrents SET " . implode(",", $updatedescr) . " WHERE info_hash=".sqlesc($infohash)) or sqlerr(__FILE__, __LINE__);
}


if ($row_nex["descr"]<$descr && !empty($descr)){

sql_query("UPDATE torrents SET descr=".sqlesc($descr)." WHERE info_hash=".sqlesc($infohash)." AND moderatedby='92'") or sqlerr(__FILE__, __LINE__);
echo "��������� ��������. <br>";
}


if (!empty($row_nex["id"]) && empty($row_nex["picture1"]) && empty($row_nex["picture4"]) && $infohash && count($updateset)){

echo "���������� ����������, ";


if (empty($row_nex["image1"]) && !empty($ifileimg_new)){

$uploaddir = ROOT_PATH."torrents/images/";

if (!empty($ifileimg_new)) {
$ifileimg_ntw = $row_nex["id"].'.'.end(explode('.',$ifileimg_new));
rename("torrents/images/".$ifileimg_new, "torrents/images/".$ifileimg_ntw);
echo "��������������� �������� �� $ifileimg_ntw,";
$updatesetpic[] = "image1 = ".sqlesc($ifileimg_ntw);
}

sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE info_hash=".sqlesc($infohash)." AND moderatedby='92'") or sqlerr(__FILE__, __LINE__);

if (count($updatesetpic) && $infohash){
echo "�������, ";
sql_query("UPDATE torrents SET " . implode(",", $updatesetpic) . " WHERE info_hash=".sqlesc($infohash)." AND moderatedby='92'") or sqlerr(__FILE__, __LINE__);

}
echo " � ����������� �������� (������� ��� ���� � ����)<br>";
}

}


echo "<script>setTimeout('document.location.href=\"parser_rutorg.php\"', $nu);</script>";
die("������ �������� ������� ��� ���� � ���� ".(empty($id)? " <b>������ ID � ���� (��� �������) - <a href=\"details.php?id=$row_nex[id]\">$row_nex[name]</a>":"")."");
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



//////////////// ���� ��������� � ��������� ���� � ������� ////////////////
preg_match_all('/<a href="\/tag\/(.*?)" target="_blank">(.*?)<\/a>/si', $descorig, $dltags); /// � $dnik[0] ������ ��������� ��������
//print_r($dltags[2]);
$tagsarray = $dltags[2];

if (count($tagsarray)){
$tagsql = array();
echo "�������� ���� ��� �������...<br>";


$tagsarray=array_unique($tagsarray); /// ������� ���������

foreach ($tagsarray as $tagi){
	
$tagi = tolower(trim(htmlspecialchars_uni($tagi)));

if (!empty($tagi)){
echo "��������� ���: $tagi <br>";
sql_query("INSERT INTO tags (category, name, howmuch,added) VALUES (".sqlesc($catid).", ".sqlesc($tagi).", 1, ".sqlesc(get_date_time()).")");

sql_query("UPDATE tags SET howmuch=howmuch+1 WHERE name LIKE ".sqlesc($tagi)) or sqlerr(__FILE__, __LINE__);
$tagsql[] = $tagi;
}

}





if (count($tagsql))
$updateset[] = "tags = ".sqlesc(implode(",", $tagsql));
}
//////////////// ���� ��������� � ��������� ���� � ������� ////////////////




///$updateset[] = "info_hash = " . sqlesc($infohash);
sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = $id") or sqlerr(__FILE__, __LINE__);
echo "<title>$torrent</title>������� �����.<br><br>";

sql_query("UPDATE trutgrab SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
}







/////////////// �������� id �� ��������� ������ ///////////////
preg_match('/<div id="index">(.*?)<\/div>/is', $date, $dlids); /// � $dnik[0] ������ ��������� ��������
$razbornwe = ($dlids[0]);

if (!empty($razbornwe)){

echo "���� ���� ������� �������: �������� id �� ���....<br>";
preg_match_all('/<a class="downgif" href="\/download\/(.*?)">/is', $razbornwe, $dli); /// ��� ������ ����������
//<a class="downgif" href="/download/66254">
$cou = $dli[1];

if (count($cou)){
foreach ($cou as $k) {
if (is_valid_id($k)){
sql_query("INSERT INTO trutgrab (details, work) VALUES (".sqlesc($k).",".sqlesc("1").")");
$newid = mysql_insert_id();
if ($newid)
echo "<font color=green><b>".$k."</b></font> <b>���������</b><br>";
else
echo "<font color=red><i>".$k."</i></font> <i>��� � ����</i><br>";
}}}}
/////////////// �������� id �� ��������� ������ ///////////////




if (stristr($torrent,'by ') || stristr($torrent,'�� ')){
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


echo "<br>�����: <b>$seconds</b> ������ - <b>$queries</b> (queries) - <b>$percentphp%</b> (php) - <b>$percentsql%</b> ($time_sql => sql) - $memory �� (use memory) <br>";

$arr = sql_query("SELECT COUNT(*) AS numgrab,(SELECT COUNT(*) FROM trutgrab WHERE work='0') AS grabost FROM trutgrab WHERE work='1'");
$row_arr = mysql_fetch_array($arr);

$procents="<b title=\" ��������� ������ - ".number_format($row_arr["numtore"])."\">����������</b>: ".number_format(100-number_format(100 * (1 - ($row_arr["grabost"] / ($row_arr["grabost"]+$row_arr["numgrab"]))),2),2)."% (<b>��</b>:".number_format($row_arr["grabost"]).":<b>���</b>:".number_format($row_arr["numgrab"]).":<b>��</b>:".number_format($row_arr["grabost"] + $row_arr["numgrab"]).")";

$time = number_format($seconds,-2);


echo "$procents <br> ����� ��������� $time

<script>setTimeout('document.location.href=\"parser_rutorg.php\"', ".$nu.");</script>";

/*
	global $tracker_lang, $queries, $tstart, $query_stat, $querytime;

	
		foreach ($query_stat as $key => $value) {
			print("<div>[<b>".($key+1)."</b>] => <b>
			".($value["seconds"] <= 0.0009 ? "<font color=\"#0468f1\" title=\"������������ ������. ����� ���������� ��������.\">".$value["seconds"]."</font>":"
		    ".($value["seconds"] >= 0.01 ? "<font color=\"red\" title=\"������������� �������������� ������. ����� ���������� ��������� �����.\">".$value["seconds"]."</font>" : "<font color=\"blue\" title=\"������ �� ��������� � �����������. ����� ���������� ����������.\">".$value["seconds"]."</font>" )."
		    ")."
		  </b> [".$value["query"]."]</div>\n");
		}
	debug();
	
*/




?>