<?

require_once("include/bittorrent.php");
require_once("include/benc.php");
require_once("include/functions_bot.php"); // подключаем функцию бота

ini_set("upload_max_filesize",$max_torrent_size);

function bark($msg) {
	genbark($msg, $tracker_lang['error']);
}
/// вместо функции ucwords
function mb_ucwords($str){
return mb_convert_case($str, MB_CASE_TITLE, "cp1251");
}


dbconn(); 

loggedinorreturn();
parked();



if (get_user_class() < UC_UPLOADER &&  ($CURUSER["class"]<>UC_VIP))
  die("ха");

foreach(explode(":","descr:type:name") as $v) {
	if (!isset($_POST[$v]))
		bark("missing form data");
}

$replace = array(", ", " , ", " ,");

// Call to undefined function mb_convert_case() - значить подключить posix.so и mbstring.so

$post_tags = str_replace("/", ",", $_POST["tags"]);
$post_tags = str_replace(".", " ", $post_tags);

$tags = trim(str_replace($replace, ",", mb_convert_case(unesc($post_tags), MB_CASE_TITLE,$mysql_charset_fix_by_imperator)));

$tags=tolower(htmlspecialchars_uni($tags));

$f = $_FILES["tfile"];


if (!isset($f))
bark("не указали торрент файл с расширением .torrrent");

$fname = unesc(htmlspecialchars($f["name"]));

if (empty($fname))
bark("Файл не загружен. Пустое имя файла!");

$descr = unesc(htmlspecialchars_uni($_POST["descr"]));

if (empty($descr))
bark("Вы должны ввести описание!");

//$catid = (0 + $_POST["type"]);
$catid = (int) $_POST["type"];

if (!is_valid_id($catid))
bark("Вы должны выбрать категорию, в которую поместить торрент!");
	
if (!validfilename($fname)) {
	
//$fname = $row['name'].$size;
$ru = array("а","б","в","г","д","е","ё","ж","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ц","ч","ш","щ","ъ","ы","ь","э","ю","я","А","Б","В","Г","Д","Е","Ё","Ж","З","И","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х","Ц","Ч","Ш","Щ","Ъ","Ы","Ь","Э","Ю","Я"," ");
$en = array("a","b","v","g","d","e","e","zh","z","i","i","k","l","m","n","o","p","r","s","t","u","f","h","c","ch","sh","sh","","u","","e","iu","ia","A","B","V","G","D","E","E","G","Z","I","K","L","M","N","O","P","R","S","T","U","F","H","C","CH","SH","SH","", "U","","E","IU","IA","_");
$fname = htmlspecialchars(str_replace($ru, $en, $fname)); 
///	bark("Неверное имя файла!");
}

if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
bark("Неверное имя файла (не .torrent).");
	
$shortfname = $torrent = $matches[1];
if (!empty($_POST["name"]))
$torrent = unesc(htmlspecialchars_uni($_POST["name"]));

$tmpname = $f["tmp_name"];
if (!is_uploaded_file($tmpname))
bark("eek немогу перекинуть файл в temp папку для обработки данных");

if (!filesize($tmpname))
bark("Пустой файл торрент!");

$dict = bdec_file($tmpname, $max_torrent_size);
if (!isset($dict))
	bark("Что за хрень ты загружаешь? Это не бинарно-кодированый файл! Слишком много весит для .torrent");

if ($_POST['free'] == 'yes' && get_user_class() >= UC_MODERATOR) {
	$free = "yes";
} else {
	$free = "no";
};

if ($_POST['sticky'] == 'yes' &&  get_user_class() >= UC_ADMINISTRATOR)
    $sticky = "yes";
else
    $sticky = "no";
    
    
if ($_POST['ontop'] == 'yes' &&  get_user_class() > UC_ADMINISTRATOR) {

    $ontop = "yes"; 
} else {
    $ontop = "no";  
};

function dict_check($d, $s) {
	if ($d["type"] <> "dictionary")
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
			if ($dd[$k]["type"] <> $t)
				bark("invalid entry in dictionary");
			$ret[] = $dd[$k]["value"];
		}
		else
			$ret[] = $dd[$k];
	}
	return $ret;
}

function dict_get($d, $k, $t) {
	if ($d["type"] <> "dictionary")
		bark("not a dictionary");
	$dd = $d["value"];
	if (!isset($dd[$k]))
		return;
	$v = $dd[$k];
	if ($v["type"] <> $t)
		bark("invalid dictionary entry type");
	return $v["value"];
}

list($info) = dict_check($dict, "info");
list($dname, $plen, $pieces) = dict_check($info, "name(string):piece length(integer):pieces(string)");

/*if (!in_array($ann, $announce_urls, 1))
	bark("Неверный Announce URL! Должен быть ".$announce_urls[0]);*/


	//http://www.tbdev.net/index.php?showtopic=17851&hl=
//if (strlen($pieces) % 20 != 0)
	//bark("invalid pieces");

if((int)($dict['value']['created by']['strlen']) > 30) // 30 or any number < 512
     bark("created by field too long");


$filelist = array();
$totallen = dict_get($info, "length", "integer");
if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
	$type = "single";
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
            stderr("Ошибка", "В торрентах запрещено держать файлы Thumbs.db!");
            die;
        }
	}
	$type = "multi";
}

$visi="yes";

if (empty($_POST["multitr"])){
$dict['value']['announce']=bdec(benc_str($announce_urls[0]));  // change announce url to local

if (isset($dict['value']['info']['value']['private']))
{
	$dict['value']['info']['value']['private']=bdec('i1e');  // без DHT + Privat
//	$updateset[] = "private = yes";
	//sql_query("UPDATE torrents SET private = 'yes' WHERE id = $id");
$privat_or = 'yes';
}
	else
{
	unset($dict['value']['info']['value']['private']);  // ставим dht

$privat_or = 'no';
}



//$dict['value']['info']['value']['private']=bdec('i1e');  // add private tracker flag
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


$dict=bdec(benc($dict)); // double up on the becoding solves the occassional misgenerated infohash
//$dict['value']['comment.utf-8']=bdec(benc_str("Торрент создан для '$SITENAME'")); // change torrent comment
    $ret = mysql_query("SHOW TABLE STATUS LIKE 'torrents'"); 
    $row = mysql_fetch_array($ret); 
    $next_id = $row['Auto_increment']; 


if (empty($_POST["multitr"])){
$visi="no";
$dict['value']['comment']=bdec(benc_str( "$DEFAULTBASEURL/details.php?id=$next_id")); // change torrent comment
$dict['value']['created by']=bdec(benc_str( "$CURUSER[username]")); // change created by
$dict['value']['publisher']=bdec(benc_str( "$CURUSER[username]")); // change publisher
$dict['value']['publisher.utf-8']=bdec(benc_str( "$CURUSER[username]")); // change publisher.utf-8
$dict['value']['publisher-url']=bdec(benc_str( "$DEFAULTBASEURL/userdetails.php?id=$CURUSER[id]")); // change publisher-url
$dict['value']['publisher-url.utf-8']=bdec(benc_str( "$DEFAULTBASEURL/userdetails.php?id=$CURUSER[id]")); // change publisher-url.utf-8
}

list($info) = dict_check($dict, "info");

$infohash = sha1($info["string"]);


//////////////////////////// авторские раздачи - запрет заливки
$num_license = get_row_count("license","WHERE info_hash=".sqlesc($infohash));

if (!empty($num_license)){
//stderr("Ошибка прав", "Данный файл имеет авторские права, запрещено заливать подобные файлы.");
bark("Данный файл имеет авторские права, запрещено заливать подобные файлы.");
die;
}
//////////////////////////// авторские раздачи - запрет заливки



//////////////////////////////////////////////

$maxfilesize = 512000; // 500kb

$allowed_types = array(
	"image/gif" => "gif",
    "image/png" => "png", 
	"image/jpeg" => "jpeg",
	"image/jpg" => "jpg"
);

/*
foreach($_FILES as $key=>$value){
    echo($key.'</b> => '.$value);
    echo("<br>");
}
*/

if (!empty($_FILES['image0']['name'])) {
	///$y = $x + 1;

	if (!array_key_exists($_FILES['image0']['type'], $allowed_types))
		bark("Неправильный тип картинки! [Разрешенны расширения jpg, png, gif]");

	if (!preg_match('/^(.+)\.(jpg|jpeg|png|gif)$/si', $_FILES['image0']['name']))
		bark("Неверное имя или формат файла (не картинка) [Разрешенны расширения jpg, jpeg, png, gif]");

	if ($_FILES['image0']['size'] > $maxfilesize)
		bark("Неправильный размер файла! Картинка не может быть больше 500kb");
     
	 	// проверка мал размера, как будто шелл
		if ($_FILES['image0']['size'] < "3024")
			bark("Ошибка размера изображения! Картинка - слишком мала для постера");
			
       
	// Update for your own server. Make sure the folder has chmod write permissions. Remember this director
	$uploaddir = ROOT_PATH."torrents/images/";

	// What is the temporary file name?
	$ifile = $_FILES['image0']['tmp_name'];

$image=file_get_contents($_FILES['image0']['tmp_name']);
if (!$image)
bark("Не изображение");
 
validimage($_FILES["image0"]["tmp_name"],"takeupload");

  	$ifilename = $next_id . $x . '.' . end(explode('.', $_FILES['image0']['name']));

	// Upload the file
	$copy = copy($ifile, $uploaddir.$ifilename);

	if (!$copy)
	bark("Ошибка копировании изображения на сервер $y");

//водяной знак

$margin = 7; 

$ifn=$uploaddir.$ifilename; 

//две картинки которые накладываем одна для темного фона другая для светлого 
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
elseif ($_FILES['image0']['type']=="image/jpg" or $_FILES['image0']['type']=="image/jpeg" or $_FILES['image0']['type']=="image/pjpeg") 
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

if ($_FILES["image0"]['type']=="image/png") 
    imagepng($creimg,$ifn); 
elseif ($_FILES["image0"]['type']=="image/jpg" or $_FILES["image0"]['type']=="image/jpeg" or $_FILES["image0"]['type']=="image/pjpeg") 
    imagejpeg($creimg,$ifn); 

    $inames[$x] = $ifilename;  

//водяной знак

	$inames[] = $ifilename;

}


if ($totallen>="3221225472" && $free_from_3GB==1){///3221225472
$free = "yes";
$torrent_com = get_date_time() . " Золото от 3 гигов.\n";
///$torrent_com = $row["torrent_com"];
}


/// альтернативный разбор картинок
if (!empty($_POST["array_picture"])){

$array_picture = explode("\r", $_POST["array_picture"]);

$xpi=1;
foreach ($array_picture AS $pic)
{

if ($xpi<5){

$_POST['picture'.$xpi]=trim($pic);
//echo "$pic <hr>";
++$xpi;
}

}
//die($_POST['picture1']." - 1<br>".$_POST['picture2']." -2<br>".$_POST['picture3']." - 3<br>".$_POST['picture4']." - 4");
}
/// альтернативный разбор картинок


if ($_POST["picture1"]==$_POST["picture2"]&&$_POST["picture3"]==$_POST["picture4"])
{
unset($_POST["picture2"]);
unset($_POST["picture3"]);
unset($_POST["picture4"]);
}

if ($_POST["picture2"]==$_POST["picture1"] || $_POST["picture2"]==$_POST["picture3"] || $_POST["picture2"]==$_POST["picture4"]){
unset($_POST["picture2"]);
}

elseif ($_POST["picture3"]==$_POST["picture1"] || $_POST["picture3"]==$_POST["picture4"]){
unset($_POST["picture3"]);
}
elseif ($_POST["picture4"]==$_POST["picture1"]){
unset($_POST["picture4"]);
}
///////////////
$picture1 = htmlentities($_POST["picture1"]);
if (!empty($picture1)) {
if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $picture1)   && !stristr($picture1,"sendpic.ru") && !stristr($picture1,"funkyimg.com") ){
if(list($width, $height) = @getimagesize($picture1)) {
$pictureset[] = "picture1 = " . sqlesc($picture1);
$pictset[]=$picture1;
}}}
///////////////
$picture2 = htmlentities($_POST["picture2"]);
if (!empty($picture2)) {
if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $picture2)  && !stristr($picture2,"sendpic.ru") && !stristr($picture2,"funkyimg.com") ){
if(list($width, $height) = @getimagesize($picture2)) {
$pictureset[] = "picture2 = " . sqlesc($picture2);
$pictset[]=$picture2;
}}}
////////////
$picture3 = htmlentities($_POST["picture3"]);
if (!empty($picture3)) {
if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $picture3)  && !stristr($picture3,"sendpic.ru") && !stristr($picture3,"funkyimg.com") ){
if(list($width, $height) = @getimagesize($picture3)) {
$pictureset[] = "picture3 = " . sqlesc($picture3);
}}}
///////////////
$picture4 = htmlentities($_POST["picture4"]);
if (!empty($picture4)) {
if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $picture4)  && !stristr($picture4,"sendpic.ru") && !stristr($picture4,"funkyimg.com") ){
if(list($width, $height) = @getimagesize($picture4)) {
$pictureset[] = "picture4 = " . sqlesc($picture4);
$pictset[]=$picture4;
}}}



if (get_user_class() >= UC_MODERATOR) {
	
$ori_web=strip_tags($_POST["webseed"]);

if (strlen($ori_web)<=15 || !preg_match("/^(http(s)?:\/\/)(([^\/]+\.)+)\w{2,}(\/)?.*$/i", $ori_web)){
$webseed_link=sqlesc("");
} else $webseed_link=sqlesc($ori_web);

}
else
$webseed_link=sqlesc("");

$multut=sqlesc(!empty($_POST["multitr"])? "yes":"no");

//$fname="Tesla_id$next_id";

// Replace punctuation characters with spaces

$torrent = htmlspecialchars(str_replace("_", " ", $torrent));
$d_up=$next_id; /// изменяем название торрента в памяти
$ret = sql_query("INSERT INTO torrents (owner, visible, sticky, ontop, info_hash, name, size, numfiles, type, tags, descr, torrent_com, free, image1, category, added, last_action, webseed,multitracker) VALUES (" . implode(",", array_map("sqlesc", array($CURUSER["id"], $visi, $sticky, $ontop, $infohash, $torrent, $totallen, count($filelist), $type, $tags, $descr, $torrent_com, $free, $inames[0], $catid))) . ", '" . get_date_time() . "', '" . get_date_time() . "',".$webseed_link.",".$multut.")");
 
 //searchfield("$shortfname $dname $torrent"), 
if (empty($ret)) {

if (mysql_errno() == 1062){

$res = sql_query("SELECT name, id FROM torrents WHERE info_hash=".sqlesc($infohash))or sqlerr(__FILE__,__LINE__); 
while ($arr = mysql_fetch_assoc($res)){
	$error_torrentname = "<a href=details.php?id=".$arr["id"].">".$arr["name"]."</a>"; 
}

stderr("Ошибка","Такой торрент уже есть на трекере:<br>$error_torrentname");
bark("mysql puked: ".mysql_error());
}
}

$id = mysql_insert_id();
sql_query("INSERT INTO checkcomm (checkid, userid, torrent) VALUES ($id, $CURUSER[id], 1)") or sqlerr(__FILE__,__LINE__);
@sql_query("DELETE FROM files WHERE torrent = $id");
foreach ($filelist as $file) {
$file[0]=utf8_to_win($file[0]);
sql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).",".sqlesc($file[1]).")");
}

if ($picture1 || $picture2 || $picture3 || $picture4){
sql_query("UPDATE torrents SET " . implode(",", $pictureset) . " WHERE id = $id"); /// картинки
}

move_uploaded_file($tmpname, "$torrent_dir/$d_up.torrent");

$fp = fopen("$torrent_dir/$d_up.torrent", "w");
if ($fp)
{
    @fwrite($fp, benc($dict), strlen(benc($dict)));
    @fclose($fp);
}


bot_newrelease($id,$torrent,$CURUSER["username"],$CURUSER["class"]); 

if(get_user_class() >= UC_MODERATOR) {

////// мод тегов //////
$ret = array();
$ptype=(int) $_POST["type"];
$res = sql_query("SELECT name FROM tags WHERE category = ".sqlesc($ptype));
while ($row = mysql_fetch_array($res))
$ret[] = tolower($row["name"]);

$union = array_intersect($ret, explode(",", $tags));
$ununion = array_diff(explode(",", $tags), $ret);

/// Преобразует в верхний регистр первый символ - для исправления неточностей тегов (когда много) ucwords
$tag=tolower($tag);
$ptype=tolower($ptype);

foreach ($union as $tag) {
sql_query("UPDATE tags SET howmuch=howmuch+1 WHERE name LIKE ".sqlesc($tag)) or sqlerr(__FILE__, __LINE__);
}

foreach ($ununion as $tag) {
sql_query("INSERT INTO tags (category, name, howmuch, added) VALUES (".sqlesc($ptype).", ".sqlesc($tag).", 1, ".sqlesc(get_date_time()).")")// or sqlerr(__FILE__, __LINE__)
;
}
////// мод тегов //////

}


///////// конкурс ///////////

if (!empty($CURUSER["groups"]) && stristr($name,'конкурс')){

$res = sql_query("SELECT name FROM groups WHERE id = ".sqlesc($CURUSER["groups"])." LIMIT 1");
while ($row = mysql_fetch_array($res)){
$name_group=$row["name"];
}
$mo=sqlesc(date("Y-m-d") . " - Конкурс групп (релиз от $name_group).\n");
sql_query("UPDATE torrents SET free='yes',torrent_com = CONCAT($mo, torrent_com) WHERE id = $id") or sqlerr(__FILE__, __LINE__);
}
///////// конкурс ///////////



/// если торрент залит администрацией то автоматом и проверен ею же.
if(get_user_class() >= UC_MODERATOR){
$updateset[] = "moderated = 'yes'";
$updateset[] = "moderatedby = ".sqlesc($CURUSER["id"]);
$updateset[] = "moderatordate = ".sqlesc(get_date_time());

sql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id") or sqlerr(__FILE__,__LINE__);
}

if ($privat_or=="no"){
sql_query("UPDATE torrents SET private = 'no' WHERE id = $id");
}



$ip_user = $CURUSER["ip"];
$ip_class = get_user_class_name($CURUSER["class"]);
$user = $CURUSER["username"];
$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER["username"]);

write_log("Торрент номер $id ($torrent) был залит $user\n", "$user_color","torrent");

@unlink(ROOT_PATH."cache/block-last_files.txt");
@unlink(ROOT_PATH."cache/block_cloudstags.txt"); /// удаляем кеш тегов из browse
@unlink(ROOT_PATH."cache/premod.txt"); // для оптимизации запросов, удаляем кеш после обновление его

// очистка тегов если пустое значение или 0
sql_query("DELETE FROM tags WHERE name=''") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM tags WHERE howmuch=0") or sqlerr(__FILE__,__LINE__);
//


$dateupload=date("d.m.y"); 
$timeupload=date("H:i:s"); 
$filelist1=count($filelist);
$razmer=mksize($totallen);

if ($picture1 || $picture2 || $picture3 || $picture4){
$kar="Скриншоты:\n".implode("\r", $pictset)."\n";
}

$c = "Название: $torrent\nХеш: $infohash\nРазмер: $razmer\nКоличество файлов: $filelist1\n Описание:\n$descr\n $kar Залил: $user ($ip_user)\nПрава: $ip_class\nВремя: $dateupload $timeupload";
$fp = fopen("torrents/txt/$id.txt","a+"); //открываем файл
fwrite($fp,"$c"); //записываем всё
fclose($fp); //закрываем

/////////

if (!empty($_POST["multitr"])){
global $announce_urls;

    $tracker_cache = array(); 
    $f_leechers = 0; 
    $f_seeders = 0; 

    foreach($announce_urls as $announce) 
    {
        $response = get_remote_peers($announce, $infohash,true); 
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



header("Location: $DEFAULTBASEURL/details.php?id=$id");

?>