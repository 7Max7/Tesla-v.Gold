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
die("�� �����!");

$nu=rand(5000,25000);

//if (get_user_class() < UC_MODERATOR)
///die("��� �������.");


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
die("C����� � ���� ��� <a href=\"parser_linkadd.php\">�������� ������ ������� ������</a>? <script>setTimeout('document.location.href=\"parser_org.ua.php?page=20\"', 30000);</script>");

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


// ������������� ����������
$fp = fsockopen($host, 80, $errno, $errstr, 30) or die("��� ���������� �������������� � ������. <script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>"); 
// ��������� ���������� ��������� ����������
if ($fp) {
// ��������� HTTP-�������
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
// ���������� HTTP-������ �������

@fwrite($fp, $headers.$data);
$date = "";
while (!feof($fp)){
$line = fgets($fp, 1024);
$date.=$line;

// � ��������� ���� ���������� ������ ����
@list($field, $value) = preg_split('/\s*:\s*/', $line, 2);
// ���������� ��������� ����
if (strtolower($field) === 'set-cookie'){
// ������, ���������� ������ ���� �������� ���� (����������������)
$result[] = array_shift(preg_split('/\s*;\s*/', $value, 2));
}
}

@fclose($fp);
} else die("��� ���������� �������������� � ������. <script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>");


if (count($result)>2){
$sf =ROOT_PATH."cache/toru_session.txt"; 
@unlink($sf);
$fpsf=fopen($sf,"a+"); 
fputs($fpsf,implode("\n",$result)); 
fclose($fpsf);
//echo "<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
//print_r($result); 
echo("<i>�������� ���� ������</i> <script>setTimeout('document.location.href=\"parser_t.php\"', 60000);</script><br>");
}


preg_match('/<div class="l">(.*?)<\/div\>/is', $date, $dnik);

if (!empty($dnik[0]) && !stristr($date,'������ ������'))
echo "��� �����: <b>".strip_tags(trim(str_replace("��� �����", "", $dnik[0])))."</b> -> ��� ��������<br>";


if (stristr($date,'sendpassword') && stristr($date,'������ ������'))
die("�������� ������������ ����, ���������� ������� �� ������. id � ���� $id_page
��������������� �� ��������� �����������...<script>setTimeout('document.location.href=\"parser_tconfig.php\"', 5000);</script>
<hr>$date 
");


// ���������.
preg_match('#<title>(.*)</title>#Uis', $date, $dtitle);
list($title,$namesite) = explode("/ ",$dtitle[1]);
$name=$title;


/// ��� ��������� � ��� ���������, ���� �� ��� ����� � ��������� ���� cat
preg_match_all('#<span class="nav">(.*)</span>#Uis', $date, $cate);


$tagi = trim(strip_tags($cate[0][1]));
$ara = explode("->",$tagi);
//print_r($ara[2]);
///

$category = trim(strip_tags($cate[1][1]));
$category = str_replace("&nbsp;", "", $category);


if ((stristr($category,'������') || stristr($category,'���� ����')) && !stristr($category,'����������'))
$catid="13"; /// ������ / AVI

elseif (stristr($category,'�������'))
$catid="11"; /// �������

elseif (stristr($category,'HD') || stristr($category,'DVD'))
$catid="14"; /// ������ / HDTV / HD / Blu-Ray

elseif (stristr($category,'������'))
$catid="10"; /// ������ / ����������� 

elseif (stristr($category,'����'))
$catid="5"; /// ���� / ��

elseif (stristr($category,'�����'))
$catid="12"; /// ����� 

elseif (stristr($category,'������� ����') || stristr($category,'������� ����'))
$catid="25"; /// �������� / ����

elseif (stristr($category,'����'))
$catid="26"; /// ���� / Windows

elseif (stristr($category,'��������������'))
$catid="22"; /// �������������� ������ 
 	
elseif (stristr($category,'����������'))
$catid="16"; /// �����

elseif (stristr($category,'����������'))
$catid="6"; /// �����������

elseif (stristr($category,'��� � ������'))
$catid="27"; /// PDA / Phone / Android / Palm

elseif (stristr($category,'DVD'))
$catid="15"; /// DVD / ������

else
$catid="4";

//die($catid);



/// ������� ������ ��� ����������.
preg_match("|(download.php.*)\"|U", $date, $li);  
$lnk_torrent=$li[1];
$lnk_torrent = str_replace("download.php?id=", "", $lnk_torrent);
/// ����� �����




/// ������� ������ ��� � �������� � ���
///<td style="padding: 6px; border-top: 1px solid rgb(173, 186, 198);" colspan="2">

preg_match('/<div><span class="postbody">(.*?)<!-- \/\/bt -->/is', $date, $dtbody);
$desc = $dtbody[0];

//print_r($dtbody);


//preg_match('/<form style="padding: 0.8em 0.3em;">(.*?)<\/div>/is', $date, $dtbod);

//print_r($dtbod);


//die($desc);

if (stristr($category,'���') || stristr($category,'�� ��������') || stristr($desc,'�������') || stristr($desc,'erotic') || stristr($desc,'����� ') || stristr($desc,' �����') || $ara[1] == "��������� ����������"){
sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

die("��� ������� ��� �������, ��� �������� ��� �� ��������. ���������������... <script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>");
}


/// ������� ������ � ��������
$desc = preg_replace('/<form.+style="([^>]+)">(.*?)<\/form\>/is', "", $desc);
/// ������� h2 ��� �������� ��������
$desc = preg_replace('/<h2.+style="([^>]+)">(.*?)<\/h2\>/is', "", $desc);
/////////////////////


if (stristr($desc,'����������') && stristr($desc,'������������') && stristr($desc,'��������')){
sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

die("���� ����� ������������� �������. ���������������... <script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>");
}


//@file_put_contents(ROOT_PATH."torrents/txt/$id_page.txt",$desc);


if (empty($desc) && empty($name)) {

if (stristr($date,'�� ����������'))
echo "������ ���� �� ����������! <br>";

if (stristr($date,'���'))
echo "������ ���� �� �������� <br>";

sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "������ �������� � ��������.<br> $date<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
die();
}

///////////////////////////
$ret = mysql_query("SHOW TABLE STATUS LIKE 'torrents'"); 
$row = mysql_fetch_array($ret); 
$next_id = $row['Auto_increment']; 


//////////// ������
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

//////////// ������
//print_r($end_image);





$desc = preg_replace('/<div style="text-align:center">(.*?)<\/div\>/is', "\\1", $desc);
///

$desc = preg_replace('/<span style="text-decoration: underline;">(.*?)<\/span\>/is', "[i]\\1[/i]", $desc);
/// ������� +
$desc = preg_replace('/<span style="font-weight: bold;">(.*?)<\/span\>/is', "[b]\\1[/b]", $desc);
/// ������ +
$desc = preg_replace('/<b class="field">(.*?)<\/b\>/is', "[b]\\1[/b]", $desc);
/// ������� �����
$desc = preg_replace('/<span[^>]style="([^>]+)">(.*?)<\/span\>/is', "\\2", $desc);
/// ������� span's �� �������

/// ����� ������ ������� hmtl ��� �� [spoiler]
$desc = preg_replace('/<div class="spoiler">(.*?)<\/div>/is', "\n[spoiler]\\1\n[/spoiler]", $desc);

/// html ��� span ������������ ��� ������� ����� ������� � ������ 
//$desc = preg_replace('/<b[^>]class="([^>]+)">(.*?)<\/b\>/is', "[b]\\2[/b]", $desc);
$desc = preg_replace('/<span[^>]>(.*?)<\/span\>/is', "\\2", $desc);


//$desc= preg_replace("#<a.*?href=\"(.*?)\".*>(.+?)<\/a>#is", "[1212]\\1[/123]", $desc); /// ��������

/////////////////////////////////////////////////
//$desc = preg_replace('/<(a.*) href=\"(.*?)\">(.*)<\/a>/', "\\3", $desc);
/// ��� ������ ����������
$desc = preg_replace('/<div align="center">(.*?)<\/div\>/is', "\\1", $desc);
$desc = preg_replace('/<font[^>]face="([^>]+)">(.*?)<\/font\>/is', "\\2", $desc);
// ���� ������� �����
$desc = preg_replace('/<font[^>]size="([^>]+)">(.*?)<\/font\>/is', "\\2", $desc);
// ���� ������� ������
//die($desc);

$desc = preg_replace('/<font[^>]style="([^>]+)">(.*?)<\/font\>/is', "\\2", $desc);
// ���� ������� ������
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
//$desc = str_replace("[spoiler]�������� ���������", "[spoiler]", $desc);

$arraysdf=array("$(function() {"," $('input.torrent-rating').rating({","split:2,","callback: tRate"," });","});");

$desc = str_ireplace($arraysdf, "", $desc);
$desc = trim(strip_tags($desc));
$desc = trim(str_replace("\n\n\n", "\n", $desc));
$desc = trim(str_replace("\n\n\n\n", "\n\n", $desc));


$desc = str_replace("[spoiler]�������� ���������", "[spoiler]", $desc); //+
$desc = str_replace("[b]������� �� IMDB:[/b] ", "", $desc);

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

///$desc = str_replace("[b]��������:[/b] \n", "\n", $desc);

$refer="http://".$host.$path;

$infohash=get_torrent($next_id,$lnk_torrent,$cookies,$row_arr["id"],$refer);

if ($infohash==false){
	
sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_t.php\"', 10000);</script>";
die("���������� ��������� ������� .torrent ����. $infohash");
}

//////////////////////////// ��������� ������� - ������ �������
$num_license = get_row_count("license","WHERE info_hash=".sqlesc($infohash));

if (!empty($num_license)){
sql_query("UPDATE tgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_t.php\"', 10000);</script>";
echo("������ ���� ����� ��������� �����, ��������� �������� �������� �����.");
die;
}
//////////////////////////// ��������� ������� - ������ �������



if (empty($desc) || stristr($desc,'��������')){
/// ���� 2 ������ ��������!!!
sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "������ �������� (��������� �������� � ���� ���).<script>setTimeout('document.location.href=\"parser_t.php\"', 2000);</script>";
die();	
}


$visi="yes";

$multut=sqlesc("yes");

$torrent_com = get_date_time() . " ������� ��������� 3 ($host$path).\n";

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


////////////////////////////////////////// ��� ������� �� tesla tt
///////////////// ���� �������� ������ ������� � ����� 1 ����� 2 � ��
/// �������� �������, ����� ���� ������
preg_match('/<div class="spoiler">(.*?)<\/div>/is', $date, $scrins);

$scrins[0] = preg_replace('/<img.*?src="(http:\/\/[^()<>\s]+?)"[^>]*?>/i', "\\1", $scrins[0]);
$scrins[0] = str_replace("�������� ���������", "", $scrins[0]);
$scrins[0] = str_replace("<br>", "\n", $scrins[0]);
$scrins[0] = str_replace("http:", "<br>http:", $scrins[0]);


/// ���������� ��� � ������
//$no_url=array("sendpic","funkyimg.com");
$scriarra = explode("\n",strip_tags($scrins[0]));
if (count($scriarra)>1){
echo "���������� ���������� � �������� (".count($scriarra).")<br>";

//print_r($scrins); //+
////////////////////////////////////////// ��� ������� �� tesla tt
$xpi=1; $xpio=0;

foreach ($scriarra AS $pic) {

$pic=htmlentities(trim($pic));

////////////////////
$araw = array("jpg.html","gif.html","bmp.html","png.html","jpeg.html");
$aran = array("jpg","gif","bmp","png","jpeg");

$pic = trim(str_replace($araw, $aran, $pic));

if ($xpi<5){
/// sendpic � funkyimg.com
if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $pic)){

if (list($width, $height) = @getimagesize($pic)) {
if ($xpi<5 && ($width>=300 && $height>=150 && $width>=$height)){

$updateset[] = "picture$xpi = ".sqlesc($pic);


echo "�������� $pic<br>";
++$xpio;
++$xpi;
}
}
if ($width>=50 && $height>=200 && $width<$height && empty($imagesff))
$imagesff = $pic;


unset($pic);
}
/////else echo "������� ������ $pic � $width � $height<br>";
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
echo "��������������� �������� ...<img src=\"./torrents/images/$ifileimg_new\"><br>";
$updateset[] = "image1 = ".sqlesc($ifileimg_new);

sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = ".$id."") or sqlerr(__FILE__, __LINE__);
}
}
//else
//echo "<img src=\"./torrents/images/$ifileimg\"><br>";

///////////

//echo "����������� ������������� $torrent ($id) <br>";
$copy=copy(ROOT_PATH."torrents/txt/".MD5XA.".torrent",ROOT_PATH."torrents/$id.torrent");

if (!$copy)
echo("�� ���� ����������� ������� � �����");
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

echo "<br>�������� ����� ������ ������� ����� - ������� <a href=\"details.php?id=".$rowr["id"]."\">$torrent</a>. <br>";

}


	
//die(MD5XA);
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");

sql_query("DELETE FROM files WHERE torrent = '$next_id'");

sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
die("������ �������� ������� ��� ���� � ���� ".(empty($id)? " <b>������ ID � ���� (��� �������)</b> $torrent":"")."");
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
echo "������ ��������� : $mycateyrogy <hr>";

}


$updateset[] = "numfiles = " . sqlesc(count($filelist));
$updateset[] = "size = " . sqlesc($size);

$updateset[] = "moderated = 'yes'";
$updateset[] = "moderatedby = ".sqlesc("92");
$updateset[] = "moderatordate = ".sqlesc(get_date_time());

///$updateset[] = "info_hash = " . sqlesc($infohash);
sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = $id") or sqlerr(__FILE__, __LINE__);
echo "<title>$torrent</title>������� ����� � �������� - <a href=\"details.php?id=$id\">$torrent</a> � ".$row_arr["id"].".";

sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_arr["id"]."'") or sqlerr(__FILE__, __LINE__);
}


function get_torrent($id=false,$path,$cookies,$row_id,$refer) {

$nu=rand(12000,100000);

$host = "tfile.ru";

if (!$id){
echo "<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
die("�� id � get_torrent");
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
echo "������������� ���������� c .torrent ������ - $path<br>";
$fp = fsockopen($host, 80, $errno, $errstr, 50) or die("���������� ���....<br>");
//socket_set_timeout($fp,1,0); //��������� ������� (100 - ��� �������)

echo "���������� ���� ... <br>";
//die($host."/forum/download.php?id=$path");
// ��������� ���������� ��������� ����������
if ($fp) {
// ��������� HTTP-�������
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

// ���������� HTTP-������ �������

///echo "�������� ������ <br>";
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
	die("��� ����������� � get ������� ($errstr - $errno)");
}

if (stristr($date,'�����������') && stristr($date,'������ ������') && !stristr($date,'����� ��������� ���'))
die("�������� ������������ ����, ���������� ������� �� ������.");

if (stristr($date,'��� � ��������'))
die("������!");


//die($date);
if (strlen($date)>="724148" || empty($date))
{

//if (!empty($next_id))
//unlink(ROOT_PATH."torrents/".$next_id.".torrent");

sql_query("DELETE FROM files WHERE torrent = '$next_id'");

sql_query("UPDATE tfgrabber SET work='0' WHERE id = '".$row_id."'") or sqlerr(__FILE__, __LINE__);

echo "<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
@unlink(ROOT_PATH."torrents/txt/".MD5XA.".torrent");
die("������ ������ ����� ������� ����� 700 ��, �� ����������.");
}



//die($date);
echo "���������, ��������� ������<br>";
echo "<script>setTimeout('document.location.href=\"parser_t.php\"', 5000);</script>";

list ($data_1,$data_2) = explode("\r\n\r\n", $date);

//die(MD5XA);

$ifilename = ROOT_PATH."torrents/txt/".MD5XA.".torrent";

///file_put_contents($ifilename,$data_2)


if(file_put_contents($ifilename,$data_2) && !empty($data_2)){
echo "������ �� �������� ���� ...<br>";
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
//echo "���������� ������ ������ ��������<br>";

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
die("������ ������ ����� 0 ������.");
}
//die(MD5XA);

//echo "������� � ���� ��� ����������� ������ <br>";
} else {
echo "��������� � ������� �� ���� ������� ������ � .torrent ����.<br>";

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


echo "<br>�����: <b>$seconds</b> ������ - <b>$queries</b> (queries) - <b>$percentphp%</b> (php) - <b>$percentsql%</b> ($time_sql => sql) - $memory �� (use memory) <br>";

$arr = sql_query("SELECT COUNT(*) AS numgrab,(SELECT COUNT(*) AS numgrab FROM tfgrabber WHERE work='0') AS grabost,(SELECT COUNT(*) FROM torrents) AS numtore FROM tfgrabber WHERE work='1'");
  $row_arr = @mysql_fetch_array($arr);
///$procents="<b title=\" ��������� ������ - ".number_format($row_arr["numtore"])."\">����������</b>: ".number_format(100-number_format(100 * (1 - ($row_arr["grabost"] / ($row_arr["grabost"]+$row_arr["numgrab"]))),2),2)."% (<b>��</b>:".number_format($row_arr["grabost"]).":<b>���</b>:".number_format($row_arr["numgrab"]).":<b>��</b>:".number_format($row_arr["grabost"] + $row_arr["numgrab"]).") ������������� � �������: ".number_format($row_arr["grabost"] - $row_arr["numtore"])." ����";

$procents="<b title=\" ��������� ������ - ".@number_format($row_arr["numtore"])."\"><a href=parser_t.php>����������</a></b>: ".@number_format(100-number_format(100 * (1 - ($row_arr["grabost"] / ($row_arr["grabost"]+$row_arr["numgrab"]))),2),2)."% (<b>��</b>:".@number_format($row_arr["grabost"]).":<b>���</b>:".@number_format($row_arr["numgrab"]).":<b>��</b>:".@number_format($row_arr["grabost"] + $row_arr["numgrab"]).") ������������� � ������� (��� ������ �������� $row_a[0])): ".@number_format($row_arr["grabost"] - $row_arr["numtore"])." ����";

if (!empty($row_arr["numgrab"]))
echo "$procents<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
else
echo "$procents<script>setTimeout('document.location.href=\"parser_org.ua.php?page=50\"', $nu);</script>";



?>