<?
require_once("include/bittorrent.php");
require_once("include/benc.php");

function bark($msg) {
	stderr("������", $msg);
}
function bark_2($yes,$msg) {
	stderr($yes, $msg);
}

// Call to undefined function mb_convert_case() - ������� ���������� posix.so � mbstring.so


/// ������ ������� ucwords
function mb_ucwords($str){
return mb_convert_case($str, MB_CASE_TITLE, "cp1251");
}

function my_ucwords2($string) {
        $string = ucwords(strtolower($string));
        $string = preg_replace_callback("/( [ a-zA-Z]{1}')([a-zA-Z0-9]{1})/s",create_function('$matches','return $matches[1].strtoupper($matches[2]);'),$string);
        return $string;
} 

////////////////////////////////////////////////
function uploadimage($x, $imgname, $tid) {

	$maxfilesize = 512000;

	$allowed_types = array(
	"image/gif" => "gif",
    "image/png" => "png", 
	"image/jpeg" => "jpeg",
	"image/jpg" => "jpg"
	// Add more types here if you like
	);

	if (!empty($_FILES['image0']['name'])) {

	//	$y = $x + 1;

		// Is valid filetype?
if (!array_key_exists($_FILES['image0']['type'], $allowed_types))
    bark("������������ ��� ��������! [���������� ���������� jpg, png, gif]");

if (!preg_match('/^(.+)\.(jpg|jpeg|png|gif)$/si', $_FILES['image0']['name']))
    bark("�������� ��� ��� ������ ����� (�� ��������) [���������� ���������� jpg, jpeg, png, gif]"); 

		// Is within allowed filesize?
		if ($_FILES['image0']['size'] > $maxfilesize)
			bark("������ ������� �����������! �������� $y - �� ������ ���� ������ <b>".mksize($maxfilesize)."</b>");

	// �������� ��� �������, ��� ����� ����
		if ($_FILES['image0']['size'] < "3024")
			bark("������ ������� �����������! �������� - ������� ���� ��� �������");
			
	// Make sure is same as on takeupload.php
        $uploaddir = ROOT_PATH."torrents/images/";

        // What is the temporary file name?
    $ifile = $_FILES['image0']['tmp_name'];         
   
    $image = file_get_contents($_FILES['image0']['tmp_name']);
    
 if (!$image)
  bark("��� ������ ������");
 
    validimage($_FILES['image0']['tmp_name'],"takeedit");
 
 		// By what filename should the tracker associate the image with?
		//$ifilename = $tid . $x . substr($_FILES['image0']['name'], strlen($_FILES['image0']['name'])-4, 4);
		
		if (!empty($imgname)) {
			$del = @unlink(ROOT_PATH."torrents/images/$imgname");
		}

		
      	$ifilename = $tid.'.'.end(explode('.', $_FILES['image0']['name']));

		$copy = copy($ifile, $uploaddir.$ifilename);

		if (!$copy)
			bark("������ ������� �����������! - �������� $y");


		/*
		
		
$end=end(explode('.', $_FILES['image0']['name']));
//������ ��������� ��� �����, ������� �� ������
$file_s="";
if ($end=="jpg" || $end=="jpeg"){
$cop = imagecreatefromJpeg($ifile);
} elseif ($end=="png"){
$cop = imagecreatefrompng($ifile);
} elseif ($end=="gif"){
$cop = imagecreatefromgif($ifile);
}

$size = getimagesize($ifile);

$max_width='640';
$max_height='480';
///print_r($size);
//��������� ����� ������� ��������
$width=$size[0];
$height=$size[1];

$x_ratio = $max_width / $width;
$y_ratio = $max_height / $height;
if ( ($width <= $max_width) && ($height <= $max_height) )
{
$tn_width = $width;
$tn_height = $height;
}
else if (($x_ratio * $height) < $max_height)
{
$tn_height = $x_ratio * $height;
$tn_width = $max_width;
}
else
{
$tn_width = $y_ratio * $width;
$tn_height = $max_height;
}
//������ ����������� ��� � ����� �����������


$trumb = imagecreatetruecolor($tn_width, $tn_height);
$image=imagecopyresized($trumb, $cop, 0, 0, 0, 0, $tn_width, $tn_height, $width,$height);

ImageJpeg($trumb,$uploaddir.$ifilename,100);
ImageDestroy($trumb);

ImageDestroy($cop);
*/




  //������� ����

$margin = 7; 

$ifn=$uploaddir.$ifilename; 

$watermark_image_light = ROOT_PATH.'pic/watermark_light.png'; 
$watermark_image_dark =  ROOT_PATH.'pic/watermark_dark.png'; 


list($image_width, $image_height) = getimagesize($ifn); 


list($watermark_width, $watermark_height) = getimagesize($watermark_image_light); 

$watermark_x = $image_width - $margin - $watermark_width; 
$watermark_y = $image_height - $margin - $watermark_height; 

$watermark_x2 = $watermark_x + $watermark_width; 
$watermark_y2 = $watermark_y + $watermark_height; 

if ($watermark_x < 0 OR $watermark_y < 0 OR 
    $watermark_x2 > $image_width OR $watermark_y2 > $image_height OR 
    $image_width < $min_image OR $image_height < $min_image) 
    { 
       return; 
    } 

$test123 = imagecreatetruecolor(1, 1); 
if ($_FILES['image0']['type']=="image/gif") 
    $creimg=imagecreatefromgif($ifn); 
elseif ($_FILES['image0']['type']=="image/png") 
    $creimg=imagecreatefrompng($ifn); 
elseif ($_FILES['image0']['type']=="image/jpg" or $_FILES['image0']['type']=="image/jpeg") 
    $creimg=imagecreatefromjpeg($ifn); 

imagecopyresampled($test123, $creimg, 0, 0, $watermark_x, $watermark_y, 1, 1, $watermark_width, $watermark_height); 
$rgb = imagecolorat($test123, 0, 0); 

$r = ($rgb >> 16) & 0xFF; 
$g = ($rgb >> 8) & 0xFF; 
$b = $rgb & 0xFF; 
     
$max = min($r, $g, $b); 
$min = max($r, $g, $b); 
$lightness = (double)(($max + $min) / 510.0); 
imagedestroy($test123); 

$watermark_image = ($lightness < 0.5) ? $watermark_image_light : $watermark_image_dark; 
$watermark = imagecreatefrompng($watermark_image); 
imagealphablending($creimg, TRUE); 
imagealphablending($watermark, TRUE); 
imagecopy($creimg, $watermark, $watermark_x, $watermark_y, 0, 0,$watermark_width, $watermark_height); 

imagedestroy($watermark); 

if ($_FILES['image0']['type']=="image/png") 
    imagepng($creimg,$ifn); 
elseif ($_FILES['image0']['type']=="image/jpg" or $_FILES['image0']['type']=="image/jpeg")
    imagejpeg($creimg,$ifn);  
    
    //������� ����

	return $ifilename;

	}

}
////////////////////////////////////////////////

function dict_check($d, $s) {
	if ($d["type"] != "dictionary")
		bark("not a dictionary");
	$a = explode(":", $s);
	$dd = $d["value"];
	$ret = array();
	foreach ($a as $k) {
		unset($t);
		if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
			$k = $m[1];
			$t = $m[2];
		}
		if (!isset($dd[$k]))
			bark("dictionary is missing key(s)");
		if (isset($t)) {
			if ($dd[$k]["type"] != $t)
				bark("invalid entry in dictionary");
			$ret[] = $dd[$k]["value"];
		}
		else
			$ret[] = $dd[$k];
	}
	return $ret;
}

function dict_get($d, $k, $t) {
	if ($d["type"] != "dictionary")
		bark("not a dictionary");
	$dd = $d["value"];
	if (!isset($dd[$k]))
		return;
	$v = $dd[$k];
	if ($v["type"] != $t)
		bark("invalid dictionary entry type");
	return $v["value"];
}


if (!mkglobal("id:name:descr:type"))
    die("missing form data");
    

$id = (int) $id;
if (!$id)
	die("�������� id ��������");
	

dbconn();
//setlocale(LC_ALL, 'ru_RU.CP1251');

loggedinorreturn();


if ($_POST["reacon"]) /// ������������� ��������
{
$res = sql_query("SELECT name, owner, seeders, image1, tags, moderated, moderatedby, info_hash FROM torrents WHERE id = ".sqlesc($id));
$row = mysql_fetch_array($res);
if (!$row)
	stderr($tracker_lang['error'],"����� ������� �� ����������!");


$tname=$row["name"];
if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
	bark("�� �� ��������! ��� ����� ����� ���������?\n");
	
$rt = (int) $_POST["reasontype"];
$dup = htmlspecialchars_uni($_POST["dup"]);
$rule = htmlspecialchars_uni($_POST["rule"]);

if (!is_int($rt) || $rt < 1 || $rt > 4)
	bark("�� ������� ������� ��� �������� ������ ��� ��������.");
if ($rt == 1)
	$reasonstr = "������� (��� ��������)";
elseif ($rt == 2) {
 if (!$dup) {bark("�� �� ������� ������ (������) � ���� ��������.");}
	$reasonstr = "��������" . ($dup ? (" ��: ".$dup) : "!");
}
elseif ($rt == 3) {
  if (!$rule) {bark("�� �� �������� ���� ������, ������� ���� ������� �������.");}
  $reasonstr = "��������� ������: ".$rule;
}
elseif ($rt == 4) {
// if (!$rule) {bark("�� �� �������� ���� ������, ������� ���� ������� �������.");}
$reasonstr = "��������� ������: ������� ���������� �������.";
  
$info_hash = $row["info_hash"];

$descki = array();

$resf = sql_query("SELECT * FROM files WHERE torrent=".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
while ($rowf = mysql_fetch_array($resf)) {
$descki[] = $rowf["filename"].":".mksize($rowf["size"])."\n";
}

$desc = implode("\n",$descki)."\n<b>����� ������</b>: ".mksize($row["size"]);

$tname = htmlspecialchars_uni($row["name"]);

$desc = "[spoiler=�������� ($id)]\n".$desc."[/spoiler]";

sql_query("INSERT INTO license VALUES (0,".sqlesc($tname).",".sqlesc($info_hash).",".sqlesc(get_date_time()).",".sqlesc(unesc($desc)).")");
}

deletetorrent($id);

if ($row["image1"]) {
 $img1 = ROOT_PATH."torrents/images/".htmlspecialchars($row["image1"]);
 @unlink($img1);
}

$tags = explode(",", $row["tags"]);

foreach ($tags as $tag) {
		@sql_query("UPDATE tags SET howmuch=howmuch-1 WHERE name LIKE ".sqlesc($tag)) or sqlerr(__FILE__, __LINE__);
	}


if($row["owner"] <> $CURUSER["id"] && !empty($row["owner"])) {

$res = sql_query("SELECT username FROM users WHERE id=".sqlesc($row["owner"])." and enabled='yes'");
$row2 = mysql_fetch_array($res);
if ($row2["username"]){
	
    $now = sqlesc(get_date_time());
    $msg = sqlesc("���� ������� $tname ($id) ���� ������� �������������  $CURUSER[username] ($reasonstr)");
    $subject = sqlesc("������� $id ($tname) ��� ������");
    sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES (0, ".sqlesc($row["owner"]).", $now, $msg, 0, $subject)")  or sqlerr(__FILE__,__LINE__);
}

}

if($row["owner"] == $CURUSER["id"]) {$owne = "������";}
write_log("$owne ������� $id ($tname) ��� ������ $CURUSER[username]: $reasonstr\n","","torrent");  
@unlink(ROOT_PATH."cache/block-comment.txt");
@unlink(ROOT_PATH."cache/block-last_files.txt");
bark_2("�������","<h2>������� ".$row["name"]." ������� ������.</h2><br>
$reasonstr");
}
/// ����� �������� ��������




$res = sql_query("SELECT owner, viponly, descr, multitracker, f_leechers, f_seeders, name, sticky, stopped, banned, banned_reason, torrent_com, category,image1,comment_lock,picture1,picture2,picture3,picture4, moderated, moderatedby, webseed, u.username FROM torrents 
LEFT JOIN users AS u ON u.id=torrents.moderatedby
WHERE torrents.id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
if (!$row)
	die("��� ������ ������� �����");

$sticky=$row["sticky"];
$stopped=$row["stopped"];
$moderated=$row["moderated"];
$row_name=htmlspecialchars_uni($row["name"]);
$row_image=htmlspecialchars($row["image1"]);
$row_descr=htmlspecialchars_uni($row["descr"]);

if (get_user_class() == UC_SYSOP){
$torrent_com = $_POST["torrent_com"];
}
else
$torrent_com = $row["torrent_com"];

$image11=$row["image1"];

$lock_comments = $row["comment_lock"];
$banned=$row["banned"];
if ($CURUSER["id"] <> $row["owner"] && get_user_class() < UC_MODERATOR)
	bark("�� �� ����������� ����� ��������!\n");

	$cat=$row["category"];
    $cat_user=$CURUSER["catedit"];
 
if ((!empty($cat_user) && !stristr($cat_user, "[cat$cat]")) && ($CURUSER["id"] <> $row["owner"]) && get_user_class() == UC_MODERATOR){
stderr($tracker_lang['error'],"�� �� ������ ������������� ���� �������, �� ���� ��������� ��������������.");  
}    

if ($_POST["banned"]=="yes" && empty($_POST["banned_reason"]) && $row["banned"]=="no") {
  bark("�� �� ����� ������� ����.");
    }
    
if (empty($_POST["name"]))
{
	bark("��� �������� �� ����� ���� ������.");
}

$updateset = array();

$fname = $row["filename"];
preg_match('/^(.+)\.torrent$/si', $fname, $matches);
$shortfname = $matches[1];
//$dname = $row["save_as"];


function cache_img($id,$name) {
$ima_typee = end(explode('.',$name));

@unlink(ROOT_PATH."torrents/thumbnail/".$id."details.".$ima_typee);
@unlink(ROOT_PATH."torrents/thumbnail/".$id."browse.".$ima_typee);
@unlink(ROOT_PATH."torrents/thumbnail/".$id."block.".$ima_typee);
@unlink(ROOT_PATH."torrents/thumbnail/".$id."beta.".$ima_typee);
@unlink(ROOT_PATH."torrents/thumbnail/".$id."getdetals.".$ima_typee);
}

// picturemod
$img1action = $_POST['img1action'];

$image0_inet = strip_tags(preg_replace("/\[((\s|.)+?)\]/is", "", $_POST['image0_inet'])); /// ����� �� ������ [] � ��
$image0_inet = htmlentities($image0_inet); ///������� ������ �� ������

if ($img1action == "update"){


	if (!empty($_FILES['image0']['name'])) {
    ///��������� �������� �������
	$updateset[] = "image1 = " .sqlesc(uploadimage(0, $row["image1"], $id));
	
    } elseif (!empty($image0_inet) && empty($_FILES['image0']['name']) && preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image0_inet)){

   
    list($width, $height) = getimagesize($image0_inet);
    
    if (!empty($width) && !empty($height)){ /// �������� ���������� http ������

    $image = @file_get_contents($image0_inet); /// �������� ��� �������� ��� �������� ��
    $size_in = strlen($image); // ��������� ����� aka ������ ��

    if (empty($image))
    bark("����������� ��� ������� ����� ��� �� ����������! ����: $image0_inet"); 
    
    if ($size_in > $maximagesize)
    bark("����������� ��� ������� ������� ������! Max ���������� ������: ".mksize($maximagesize)); 
    
    
    if (!empty($row["image1"]) && !preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $row["image1"])) {
   	/// ������� ���� ��� �� http ������
	@unlink(ROOT_PATH."torrents/images/".$row["image1"]); /// ������� ������ ������ � �������
	}
	
    $caimage = imageshack($image0_inet);
    
    if ($caimage <> false)
    $updateset[] = "image1 = " .sqlesc($caimage);

    }
    }

cache_img($id,$row["image1"]);

} elseif ($img1action == "delete") {


	if (!empty($row["image1"])) {
	
	@unlink(ROOT_PATH."torrents/images/".htmlspecialchars($row["image1"]));
	$updateset[] = "image1 = ''";
		
	cache_img($id,$row["image1"]);
	}
}

if (!empty($_POST["imgimove"]) && !empty($row["image1"]) && !preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $row["image1"]) && file_exists(ROOT_PATH."torrents/images/".htmlspecialchars($row["image1"]))){

$caimage = imageshack(ROOT_PATH."torrents/images/".htmlspecialchars($row["image1"]));

if ($caimage <> false){
$updateset[] = "image1 = " .sqlesc($caimage);
unlink(ROOT_PATH."torrents/images/".htmlspecialchars($row["image1"]));
cache_img($id,$row["image1"]);
}
}


/// �������������� ������ ��������
if (!empty($_POST["array_picture"])){
	
$postarpic = htmlspecialchars_uni(preg_replace("/\[((\s|.)+?)\]/is", "", $_POST["array_picture"]));

$array_picture = explode("\r", $postarpic);

$xpi=1;
foreach ($array_picture AS $pic) {
if ($xpi<5){
$_POST['picture'.$xpi]=trim($pic);
++$xpi;
}
}
}
/// �������������� ������ ��������


 /// ���������� ��� � ������
$apicfor=array();

if (!empty($_POST["picture1"]))
$apicfor[]=$_POST["picture1"];
if (!empty($_POST["picture2"]))
$apicfor[]=$_POST["picture2"];
if (!empty($_POST["picture3"]))
$apicfor[]=$_POST["picture3"];
if (!empty($_POST["picture4"]))
$apicfor[]=$_POST["picture4"];

$apicrow=array();

if (!empty($row["picture1"]))
$apicrow[]=$row["picture1"];
if (!empty($row["picture2"]))
$apicrow[]=$row["picture2"];
if (!empty($row["picture3"]))
$apicrow[]=$row["picture3"];
if (!empty($row["picture4"]))
$apicrow[]=$row["picture4"];


$apicfor=array_unique($apicfor); /// ������� ��������� ������ post
$apicrow=$apicrow; /// �� ������� �����...

//$no_url=array("sendpic","funkyimg.com");

$xpi=1; $xpio=0;
foreach ($apicfor AS $pic) {
$pic=htmlentities($pic);

if ($xpi<5 && array_diff($apicfor,$apicrow)){
/// sendpic � funkyimg.com
if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $pic) && !stristr($pic,"sendpic.ru") && !stristr($pic,"funkyimg.com") ){

if(list($width, $height) = @getimagesize($pic)) {
$updateset[] = "picture$xpi = ".sqlesc($pic);
} else {
$updateset[] = "picture$xpi = ''";
}

++$xpio;
++$xpi;
}
unset($pic);
}
}

//echo "<br> ����� ".count($apicfor)." <br>";
if (count($apicfor)==3){
 $updateset[] = "picture4 = ''";
}
elseif (count($apicfor)==2){
$updateset[] = "picture3 = ''";
$updateset[] = "picture4 = ''";	
}
elseif (count($apicfor)==1){
$updateset[] = "picture2 = ''";
$updateset[] = "picture3 = ''";	
$updateset[] = "picture4 = ''";	
}
elseif (count($apicfor)==0){
$updateset[] = "picture1 = ''";
$updateset[] = "picture2 = ''";
$updateset[] = "picture3 = ''";	
$updateset[] = "picture4 = ''";	
}
//print_r($updateset);
//die;
// picturemod array for Tesla TT


if (isset($_FILES["tfile"]) && !empty($_FILES["tfile"]["name"]))
	$update_torrent = true;

if (!empty($update_torrent)) {
	$f = $_FILES["tfile"];
	$fname = unesc(htmlspecialchars($f["name"]));
	if (empty($fname))
		bark("���� �� ��������. ������ ��� �����!");
	if (!validfilename($fname))
		bark("�������� ��� �����!");
	if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
		bark("�������� ��� ����� (�� .torrent)");
		
	$tmpname = $f["tmp_name"];
	
	if (!is_uploaded_file($tmpname))
		bark("eek");
	
	if (!filesize($tmpname))
		bark("������ ����!");
		
	$dict = bdec_file($tmpname, $max_torrent_size);
	if (!isset($dict))
		bark("��� �� ����� �� ����������? ��� �� �������-����������� ����!");
		
	list($info) = dict_check($dict, "info");
	list($dname, $plen, $pieces) = dict_check($info, "name(string):piece length(integer):pieces(string)");
	
	//http://www.tbdev.net/index.php?showtopic=17851&hl=
	//if (strlen($pieces) % 20 != 0)
	//	bark("invalid pieces");
		
if((int)($dict['value']['created by']['strlen']) > 30) // 30 or any number < 512
     bark("created by field too long");


	$filelist = array();
	$totallen = dict_get($info, "length", "integer");
	if (isset($totallen)) {
		$filelist[] = array($dname, $totallen);
		$torrent_type = "single";
	} else {
		$flist = dict_get($info, "files", "list");
		if (!isset($flist))
			bark("missing both length and files");
		if (!count($flist))
			bark("no files");
		$totallen = 0;
		foreach ($flist as $fn) {
			list($ll, $ff) = dict_check($fn, "length(integer):path(list)");
			$totallen += $ll;
			$ffa = array();
			foreach ($ff as $ffe) {
				if ($ffe["type"] != "string")
					bark("filename error");
				$ffa[] = $ffe["value"];
			}
			if (!count($ffa))
				bark("filename error");
			$ffe = implode("/", $ffa);
			$filelist[] = array($ffe, $ll);
		if ($ffe == 'Thumbs.db')
	        {
	            stderr("������", "� ��������� ��������� ������� ����� Thumbs.db!");
	            die;
	        }
		}
		$torrent_type = "multi";
	}
	
	/// ���� ��� ���������� - ������ ������ � �������� �����
    if ($_POST["multitr"]=="no"){
	
	$dict['value']['announce']=bdec(benc_str($announce_urls[0]));  // change announce url to local

	if (isset($dict['value']['info']['value']['private']))	{
	$dict['value']['info']['value']['private']=bdec('i1e');  // ��� DHT + Privat
	sql_query("UPDATE torrents SET private = 'yes' WHERE id = $id");
	}
	else
	{
	unset($dict['value']['info']['value']['private']);  // ������ dht
	sql_query("UPDATE torrents SET private = 'no' WHERE id = $id");
	}

//	unset($dict['value']['info']['value']['private']);  // ������ dht
	$dict['value']['info']['value']['source']=bdec(benc_str( "[$DEFAULTBASEURL] $SITENAME")); // add link for bitcomet users
	unset($dict['value']['announce-list']); // remove multi-tracker capability
	unset($dict['value']['nodes']); // remove cached peers (Bitcomet & Azareus)
	unset($dict['value']['info']['value']['crc32']); // remove crc32
	unset($dict['value']['info']['value']['ed2k']); // remove ed2k
	unset($dict['value']['info']['value']['md5sum']); // remove md5sum
	unset($dict['value']['info']['value']['sha1']); // remove sha1
	unset($dict['value']['info']['value']['tiger']); // remove tiger
	unset($dict['value']['azureus_properties']); // remove azureus properties
	}
	////
	
	$dict=bdec(benc($dict)); // double up on the becoding solves the occassional misgenerated infohash
	
	/// ���� ��� ���������� - ������ ������ � �������� �����
	if ($_POST["multitr"]=="no"){
	//$dict['value']['comment']=bdec(benc_str( "������� ������ ��� '$SITENAME'")); // change torrent comment
	$dict['value']['comment']=bdec(benc_str( "$DEFAULTBASEURL/details.php?id=$id")); // change torrent comment to URL
	$dict['value']['created by']=bdec(benc_str( "$CURUSER[username]")); // change created by
	$dict['value']['publisher']=bdec(benc_str( "$CURUSER[username]")); // change publisher
	$dict['value']['publisher.utf-8']=bdec(benc_str( "$CURUSER[username]")); // change publisher.utf-8
	$dict['value']['publisher-url']=bdec(benc_str( "$DEFAULTBASEURL/userdetails.php?id=$CURUSER[id]")); // change publisher-url
	$dict['value']['publisher-url.utf-8']=bdec(benc_str( "$DEFAULTBASEURL/userdetails.php?id=$CURUSER[id]")); // change publisher-url.utf-8
	}
    ///

	list($info) = dict_check($dict, "info");

	$infohash = sha1($info["string"]);


//////////////////////////// ��������� ������� - ������ �������
$num_license = get_row_count("license","WHERE info_hash=".sqlesc($infohash));

if (!empty($num_license)){
stderr("������ ����", "������ ���� ����� ��������� �����, ��������� �������� �������� �����.");
die;
}
//////////////////////////// ��������� ������� - ������ �������


@unlink(ROOT_PATH."torrents/".$id.".torrent");

///	$d_up=$id."[ta]"; /// �������� �������� �������� � ������
	
	move_uploaded_file($tmpname, ROOT_PATH."torrents/".$id.".torrent");

	$fp = fopen(ROOT_PATH."torrents/".$id.".torrent", "w");
	if ($fp) {
	    @fwrite($fp, benc($dict), strlen(benc($dict)));
	    fclose($fp);
	}

	$updateset[] = "info_hash = " . sqlesc($infohash);

//  $fname="Tesla_id".$id;
//	$updateset[] = "filename = " . sqlesc($fname);
///	$updateset[] = "save_as = " . sqlesc($d_up);
	///////////
	$updateset[] = "numfiles = " . sqlesc(count($filelist));
/////////////
	@sql_query("DELETE FROM files WHERE torrent = $id");
	foreach ($filelist as $file) {
		$file[0]=utf8_to_win($file[0]);
		@sql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).",".$file[1].")");
	}
}

$name = preg_replace("/\&/", "and", $name);
$name = htmlspecialchars_decode($name);



if(get_user_class() >= UC_MODERATOR) {

if ($_POST["oldtags"]<>$_POST["tags"]){
///// ��� ����� /////
$replace = array(", ", " , ", " ,");
$ptype=(int) $_POST["type"];
///$tags = trim(str_replace($replace, ",", mb_convert_case(unesc($_POST["tags"]), MB_CASE_TITLE, $mysql_charset_fix_by_imperator)));

$_POST["tags"] = str_ireplace("/", ",", $_POST["tags"]);
$_POST["tags"] = str_ireplace(".", " ", $_POST["tags"]);

$tags = trim(str_replace($replace, ",", tolower(unesc(htmlspecialchars_uni($_POST["tags"])))));
$oldtags = tolower(unesc(htmlspecialchars_uni($_POST["oldtags"])));

$un = array_diff(explode(",", $tags), explode(",", $oldtags));
$un2 = array_diff(explode(",", $oldtags), explode(",", $tags));

$ret = array();
$res = sql_query("SELECT name FROM tags WHERE category = ".sqlesc($ptype));
while ($row = mysql_fetch_array($res))
	$ret[] = tolower($row["name"]);

$union = array_intersect($ret, $un);
$ununion = array_diff($un, $ret);

/// ����������� � ������� ������� ������ ������ - ��� ����������� ����������� ����� (����� �����)

$tag=tolower($tag);
$ptype=tolower($ptype);

foreach ($union as $tag) {
sql_query("UPDATE tags SET howmuch=howmuch+1 WHERE name LIKE ".sqlesc($tag)) or sqlerr(__FILE__, __LINE__);
}

foreach ($un2 as $tag) {
sql_query("UPDATE tags SET howmuch=howmuch-1 WHERE name LIKE ".sqlesc($tag)) or sqlerr(__FILE__, __LINE__);
}

foreach ($ununion as $tag) {
sql_query("INSERT INTO tags (category, name, howmuch,added) VALUES (".sqlesc($ptype).", ".sqlesc($tag).", 1, ".sqlesc(get_date_time()).")") or sqlerr(__FILE__, __LINE__);
}
	
@unlink(ROOT_PATH."cache/block_cloudstags.txt");
///// ��� ����� /////
}
}


///////////
$ip_user = $CURUSER["ip"];
$user = $CURUSER["username"];
$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER["username"]);
///////////


$descr = unesc(htmlspecialchars_uni($_POST["descr"]));
if (!$descr)
	bark("�� ������ ������ ��������!");

$updateset[] = "name = " . sqlesc($name);

if(get_user_class() >= UC_MODERATOR && $_POST["oldtags"]<>$_POST["tags"]) {
$updateset[] = "tags = " . sqlesc($tags);
}

//$updateset[] = "search_text = " . sqlesc(htmlspecialchars("$shortfname $dname $torrent"));
$updateset[] = "descr = " . sqlesc($descr);

$updateset[] = "category = " . ((int) $type);

if (get_user_class() >= UC_ADMINISTRATOR) {


	if ($_POST["banned"]=="yes" && $banned=="no") {
		
		$banned_reason=$_POST["banned_reason"];
		
		$updateset[] = "banned_reason = " . sqlesc(htmlspecialchars($banned_reason));
		$updateset[] = "banned = 'yes'";
		
		$torrent_com = get_date_time() . " $user ������� ������� (".htmlspecialchars($banned_reason).") .\n". $torrent_com;
	}

	if ($_POST["banned"]=="no" && $banned=="yes") {
				
	//	$banned_reason=$_POST["banned_reason"];
		$updateset[] = "banned = 'no'";
		
	$torrent_com = get_date_time() . " $user �������� �������.\n". $torrent_com;
	}


/// ������� ����
if (!empty($_POST["truncacheimg"])){

$ima_type = end(explode('.',$row_image));

$one=ROOT_PATH."torrents/thumbnail/".$id."details.".$ima_type;
$two=ROOT_PATH."torrents/thumbnail/".$id."block.".$ima_type;
$tre=ROOT_PATH."torrents/thumbnail/".$id."beta.".$ima_type;
$four=ROOT_PATH."torrents/thumbnail/".$id."getdetals.".$ima_type;
$five=ROOT_PATH."torrents/thumbnail/".$id."browse.".$ima_type;


$feimg=filesize($one)+filesize($two)+filesize($tre)+filesize($four)+filesize($five);

if (!empty($feimg)){
@unlink($one);
@unlink($two);
@unlink($tre);
@unlink($four);
@unlink($five);

$torrent_com = get_date_time() . " $user ������� ��� (".mksize($feimg).").\n". $torrent_com;
};
}



	if ($_POST["sticky"] == "yes" && $sticky=="no") {
	
	             $updateset[] = "sticky = 'yes'";
	       	 $torrent_com = get_date_time() . " $user �������� ��������.\n". $torrent_com;
	    		}
        
	if ($_POST["sticky"] == "no" && $sticky=="yes") {
         
			$updateset[] = "sticky = 'no'";
    $torrent_com = get_date_time() . " $user ���� ��������.\n". $torrent_com;

		    }
		    
		  //  die($stopped);
		  if ($_POST["stopped"] == "no" && $stopped=="yes") {
      $updateset[] = "stopped = 'no'";
     $torrent_com = get_date_time() . " $user ���������� �������.\n". $torrent_com;
		    }
		  if ($_POST["stopped"] == "yes" && $stopped=="no") {
         $updateset[] = "stopped = 'yes'";
      $updateset[] = "stop_time = NOW()";
      $torrent_com = get_date_time() . " $user ������������ ������� ����� (�� ����� �������).\n". $torrent_com;
		    }
		    }


//��������� ���� ���� ���������
if ($_POST["up_date"] == "yes" && get_user_class() >= UC_MODERATOR){
 $updateset[] = "added = NOW()";
}


if (get_user_class() > UC_ADMINISTRATOR) {
  
	if ($_POST["ontop"] == "yes") 
            $updateset[] = "ontop = 'yes'"; 
        else 
            $updateset[] = "ontop = 'no'";  
}

if (get_user_class() >= UC_MODERATOR) {
	
	if ($_POST["lock_comments"] == "yes"  and $lock_comments!='yes')
	{
	$updateset[] = "comment_lock = 'yes'"; 
	}
	if ($_POST["lock_comments"] == "no"  and $lock_comments!='no')
	{
    $updateset[] = "comment_lock = 'no'";  
    }
    
    
$veria=number_format($row["f_leechers"]+$row["f_seeders"]);
if ($row["multitracker"]=="no" && $veria>1 && !empty($_POST["multivalue"])){
$torrent_com = get_date_time() . " $CURUSER[username] ������� ��� ������� �� ������������ (������� ".$veria." �����).\n". $torrent_com;
$updateset[] = "multitracker = 'yes'";
}
    
    
    
    
}

if(get_user_class() >= UC_MODERATOR){
$updateset[] = "free = '".($_POST["free"]==1 ? 'yes' : 'no')."'";






if ($_POST["recheck"]=="yes" && $row["multitracker"]=="yes"){
$torrent_com = get_date_time() . " $CURUSER[username] ����������� ����� (ViKa).\n". $torrent_com;
$updateset[] = "moderated = 'yes'";
$updateset[] = "moderatedby = ".sqlesc($CURUSER["id"]);
$updateset[] = "moderatordate = ".sqlesc(get_date_time());
}


if (!empty($_POST["checks_files"]) && $_POST["checks_files"]=="yes"){

$file_url=ROOT_PATH."torrents/".$id.".torrent";

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
	if (!isset($flist))
		$fileerror=true;
	if (!@count($flist))
			$fileerror=true;
	$totallen = 0;
	
	foreach ($flist as $file_url) {
		list($ll, $ff) = @dict_check_t($file_url, "length(integer):path(list)");
		$totallen += $ll;
		$ffa = array();
		foreach ($ff as $ffe) {
			if ($ffe["type"] != "string")
				$fileerror=true;
			$ffa[] = $ffe["value"];
		}
		if (!count($ffa))
			$fileerror=true;
		$ffe = implode("/", $ffa);
		$filelist[] = array($ffe, $ll);
	}
	
}

$dict=@bdec(@benc($dict)); 
@list($info) = @dict_check_t($dict, "info");
$infohash = sha1($info["string"]);

if (!empty($totallen)){
sql_query("DELETE FROM files WHERE torrent = '$id'");
foreach ($filelist as $file) {
$file[0]=utf8_to_win($file[0]);
//echo $file[0]."<br>";
//echo $file[1]."<br>";
sql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).",".sqlesc($file[1]).")") or sqlerr(__FILE__, __LINE__);
}

}
$updateset[] = "numfiles = " . sqlesc(count($filelist));
$torrent_com = get_date_time() . " $CURUSER[username] �����. ���������� ������ (+).\n". $torrent_com;
} else {
$torrent_com = get_date_time() . " $CURUSER[username] �����. ���������� ������ (����������� .torrent).\n". $torrent_com;
}

}


}

if(get_user_class() >= UC_ADMINISTRATOR){
	
//// ��� �������
	if ($_POST["vip_only"] == "yes" && empty($row["viponly"])){
	$day=(int)$_POST["day_s"]; /// ���
	$s_day_sql=(time() + $day* 86400);
	$reday=display_date_time($s_day_sql);
///	$all_data="1:".$s_day_sql;
	
	if (!empty($day)){
	$updateset[] = "viponly = ".sqlesc($s_day_sql).""; 
	$torrent_com = get_date_time() . " $user ������� vip ������� ($reday).\n". $torrent_com;
    }
	}

	if ($_POST["vip_only"] == "no" && !empty($row["viponly"])){
	$updateset[] = "viponly = ".sqlesc(""); 
	$torrent_com = get_date_time() . " $user �������� vip �������.\n". $torrent_com;
    }
//// ��� �������
    
	
	
//$updateset[] = "free = '".($_POST["free"]==1 ? 'yes' : 'no')."'";

	
if ($_POST["delete_comment"]=='yes'){

sql_query("DELETE FROM comments WHERE torrent = ".sqlesc($id));
write_log("$user ������ ��� ����������� � �������� $name ($id)\n", "$user_color","comment");

$torrent_com = get_date_time() . " $user ������ ��� ����������� ��������.\n". $torrent_com;
}

////////// ��������� ��������
if ($_POST["moded"]=="yes" && $moderated=="no"){
$updateset[] = "moderated = 'yes'";
$updateset[] = "moderatedby = ".sqlesc($CURUSER["id"]);
$updateset[] = "moderatordate = ".sqlesc(get_date_time());
$torrent_com = get_date_time() . " $CURUSER[username] �������.\n". $torrent_com;
@unlink(ROOT_PATH."cache/block-last_files.txt"); // ��� ����������� ��������, ������� ��� ����� ���������� ���
@unlink(ROOT_PATH."cache/premod.txt"); // ��� ����������� ��������, ������� ��� ����� ���������� ���

}
if ($_POST["moded"]=="no" && $moderated=="yes"){
$torrent_com = get_date_time() . " $CURUSER[username] ���� ��������� $row[username].\n". $torrent_com;
$updateset[] = "moderated = 'no'";
@unlink(ROOT_PATH."cache/premod.txt"); // ��� ����������� ��������, ������� ��� ����� ���������� ���
}
////////// ��������� ��������

}


if ($row["banned"] == "yes")
$_POST["visible"] = 0;  

$updateset[] = "visible = '" . ($_POST["visible"]=='yes' ? "yes" : "no") . "'";


//$updateset[] = "moderated = 'yes'";
//$updateset[] = "moderatedby = ".sqlesc($CURUSER["id"]);


if (get_user_class() >= UC_ADMINISTRATOR){

//������� ����� ���������
if (!empty($_POST["user_reliases_anonim"])){
$updateset[] = "owner = '0'";
//write_log("������� $name ($id) ���� ��������� - $user\n", "$user_color","torrent");

$torrent_com = get_date_time() . " $user �������� ������ �����������.\n". $torrent_com;
}

//������� ����� ������� ������ 
if (!empty($_POST["release_set_id"])){

    $setid = (int) $_POST["release_set_id"];  
	         
    $updateset[] = "owner = '$setid'";
    $res = sql_query("SELECT username FROM users WHERE id = $setid") or sqlerr(__FILE__, __LINE__); 
    $row = mysql_fetch_array($res); 
    if (!empty($row["username"]))
    $owner_t=$row["username"];
    else
    $owner_t="[$setid]";

   if ($setid){
   $torrent_com = get_date_time() . " $user �������� ���������� - $owner_t � ������.\n". $torrent_com;
    }
}

}

/*
if(get_user_class() >= UC_MODERATOR && $row["moderatedby"]==0)
{
$updateset[] = "moderated = 'yes'";
$updateset[] = "moderatedby = ".sqlesc($CURUSER["id"]);
$updateset[] = "moderatordate = ".sqlesc(get_date_time());

$torrent_com = get_date_time() . " $CURUSER[username] �������.\n". $row["torrent_com"];
$updateset[] = "torrent_com = " . sqlesc($torrent_com);

sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = ".sqlesc($id)."");
@unlink(ROOT_PATH."cache/block-last_files.txt"); // ��� ����������� ��������, ������� ��� ����� ���������� ���

}
*/

if (get_user_class() >= UC_MODERATOR && $_POST["torrent_com_zam"]){
$torrent_com1 = htmlspecialchars($_POST["torrent_com_zam"]);
$torrent_com = get_date_time() . " ������� �� $CURUSER[username]: $torrent_com1\n". $torrent_com;
}


if ($row_descr<>$_POST["descr"] || $row_name<>$_POST["name"]) {
$torrent_com = get_date_time() . " ��� �������������� $user.\n". $torrent_com;
}


//����������� � ������� ����� 
if ($totallen){
$updateset[] = "size = '$totallen'"; 
}
//  


$updateset[] = "torrent_com = " . sqlesc($torrent_com);


$ori_web=strip_tags($_POST["webseed"]);

if (get_user_class() >= UC_MODERATOR && $row["webseed"]<>$ori_web) {

if (strlen($ori_web)<=15 && preg_match("/^(http(s)?:\/\/)(([^\/]+\.)+)\w{2,}(\/)?.*$/i", $ori_web)){
$updateset[] = "webseed = ''";	
} else
$updateset[] = "webseed = " . sqlesc($ori_web);	


} else unset($ori_web);

if ($update_torrent){
$updateset[]="multitracker=".sqlesc($_POST["multitr"]=="yes" ? "yes":"no");
}

sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = $id");

if ($_POST["multitr"]=="yes" && !empty($update_torrent)){

global $announce_urls;

$sql = sql_query("SELECT info_hash FROM torrents WHERE id=$id"); 
while($torrent = mysql_fetch_array($sql)) {
    $tracker_cache = array(); 
    $f_leechers = 0; 
    $f_seeders = 0; 

    foreach($announce_urls as $announce) 
    {
        $response = get_remote_peers($announce, $torrent['info_hash'],true); 
        if($response['state']=='ok'){
        $tracker_cache[] = $response['tracker'].':'.($response['leechers'] ? $response['leechers'] : 0).':'.($response['seeders'] ? $response['seeders'] : 0).':'.($response['downloaded'] ? $response['downloaded'] : 0); 
            // $f_leechers += $response['leechers']; 
            // $f_seeders += $response['seeders']; 
            if ($f_leechers < $response['leechers'])
            $f_leechers = $response['leechers'];
            
            if ($f_seeders < $response['seeders'])
            $f_seeders = $response['seeders']; 
        }
        else 
            $tracker_cache[] = $response['tracker'].':'.$response['state'];
    }
    
    $fpeers = $f_seeders + $f_leechers;
    $tracker_cache = implode("\n",$tracker_cache);
    $updatef = array();
    $updatef[] = "f_trackers = ".sqlesc($tracker_cache);
    $updatef[] = "f_leechers = ".sqlesc($f_leechers);
    $updatef[] = "f_seeders = ".sqlesc($f_seeders);
    $updatef[] = "multi_time = ".sqlesc(get_date_time());
    $updatef[] = "visible = ".sqlesc(!empty($fpeers) ? 'yes':'no');
    sql_query("UPDATE torrents SET " . implode(",", $updatef) . " WHERE id = $id");
    //implode(",", $updatef)

}
   
	//die($announce_list);	
}

/*
if (!empty($CURUSER["groups"]) && stristr($name,'�������')){

$res = sql_query("SELECT free FROM torrents WHERE id = ".sqlesc($id));
while ($row = mysql_fetch_array($res))
{
	$name_tor=$row["free"];
}
if ($name_tor=='no') {

$res = sql_query("SELECT name FROM groups WHERE id = ".sqlesc($CURUSER["groups"]));
while ($row = mysql_fetch_array($res))
{
	$name_group=$row["name"];
}
$mo=sqlesc(date("Y-m-d") . " - ������� ����� (����� �� $name_group).\n");
sql_query("UPDATE torrents SET free='yes',torrent_com = CONCAT($mo, torrent_com) WHERE id = $id") or sqlerr(__FILE__, __LINE__);
}

}
*/

$returl = "details.php?id=$id";
if (isset($_POST["returnto"]))
	$returl .= "&returnto=" . htmlentities($_POST["returnto"]);

header("Refresh: 1; url=$returl");
?>