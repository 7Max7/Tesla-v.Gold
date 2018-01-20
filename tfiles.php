<?
require ("include/bittorrent.php");

dbconn();
loggedinorreturn();
//parked();

$ip_user = $CURUSER["ip"];
$user = $CURUSER["username"];
$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER["username"]);
///////////

function readframe($file) {
	
  if (! ($f = fopen($file, 'rb')) )// die("Невозможно открыть файл " . $file)
  ;
   $res['filesize'] = filesize($file);
   do {
       while (fread($f,1) <> Chr(255)) { // Find the first frame       
         if (feof($f))  stderr("Ошибка проверки файла", "Файл не звук | видео. В доступе заливки отказано!");
      }
       fseek($f, ftell($f) - 1); // back up one byte

       $frameoffset = ftell($f);

       $r = fread($f, 4);
         
        $bits = sprintf("%'08b%'08b%'08b%'08b", ord($r{0}), ord($r{1}), ord($r{2}), ord($r{3}));
    }
   while (!$bits[8] and !$bits[9] and !$bits[10]); // 1st 8 bits true from the while   

   // Detect VBR header
   if ($bits[11] == 0) {
      if (($bits[24] == 1) && ($bits[25] == 1)) {
            $vbroffset = 9; // MPEG 2.5 Mono
      } else {
           $vbroffset = 17; // MPEG 2.5 Stereo
      }
   } else if ($bits[12] == 0) {
       if (($bits[24] == 1) && ($bits[25] == 1)) {
           $vbroffset = 9; // MPEG 2 Mono
       } else {
            $vbroffset = 17; // MPEG 2 Stereo
       }
   } else {
        if (($bits[24] == 1) && ($bits[25] == 1)) {
           $vbroffset = 17; // MPEG 1 Mono
       } else {
            $vbroffset = 32; // MPEG 1 Stereo
       }
    }

   fseek($f, ftell($f) + $vbroffset);
   $r = fread($f, 4);

    switch ($r) {
        case 'Xing':
            $res['encoding_type'] = 'VBR';
        case 'VBRI':
       default:
           if ($vbroffset != 32) {
               // VBRI Header is fixed after 32 bytes, so maybe we are looking at the wrong place.
              fseek($f, ftell($f) + 32 - $vbroffset);
             $r = fread($f, 4);

               if ($r != 'VBRI') {
                   $res['encoding_type'] = 'CBR';
                  break;
              }
           } else {
               $res['encoding_type'] = 'CBR';
               break;
           }
           $res['encoding_type'] = 'VBR';
    }
 
    fclose($f);
 
    if ($bits[11] == 0) {
       $res['mpeg_ver'] = "2.5";
        $bitrates = array(
            '1' => array(0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256, 0),
            '2' => array(0,  8, 16, 24, 32, 40, 48,  56,  64,  80,  96, 112, 128, 144, 160, 0),
            '3' => array(0,  8, 16, 24, 32, 40, 48,  56,  64,  80,  96, 112, 128, 144, 160, 0),
                 );
    } else if ($bits[12] == 0) {
        $res['mpeg_ver'] = "2";
        $bitrates = array(
            '1' => array(0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256, 0),
            '2' => array(0,  8, 16, 24, 32, 40, 48,  56,  64,  80,  96, 112, 128, 144, 160, 0),
            '3' => array(0,  8, 16, 24, 32, 40, 48,  56,  64,  80,  96, 112, 128, 144, 160, 0),
                 );
    } else {
        $res['mpeg_ver'] = "1";
        $bitrates = array(
            '1' => array(0, 32, 64, 96, 128, 160, 192, 224, 256, 288, 320, 352, 384, 416, 448, 0),
            '2' => array(0, 32, 48, 56,  64,  80,  96, 112, 128, 160, 192, 224, 256, 320, 384, 0),
            '3' => array(0, 32, 40, 48,  56,  64,  80,  96, 112, 128, 160, 192, 224, 256, 320, 0),
                 );
    }
     
    $layer = array(
        array(0,3),
        array(2,1),
              );
    $res['layer'] = $layer[$bits[13]][$bits[14]];
     
    if ($bits[15] == 0) {
        // It's backwards, if the bit is not set then it is protected.
        $res['crc'] = true;
    }
 
    $bitrate = 0;
    if ($bits[16] == 1) $bitrate += 8;
    if ($bits[17] == 1) $bitrate += 4;
    if ($bits[18] == 1) $bitrate += 2;
    if ($bits[19] == 1) $bitrate += 1;
    $res['bitrate'] = $bitrates[$res['layer']][$bitrate];
 
    $frequency = array(
        '1' => array(
            '0' => array(44100, 48000),
            '1' => array(32000, 0),
                ),
        '2' => array(
            '0' => array(22050, 24000),
            '1' => array(16000, 0),
                ),
        '2.5' => array(
            '0' => array(11025, 12000),
            '1' => array(8000, 0),
                  ),
          );
    $res['frequency'] = $frequency[$res['mpeg_ver']][$bits[20]][$bits[21]];
 
    $mode = array(
        array('Stereo', 'Joint Stereo'),
        array('Dual Channel', 'Mono'),
             );
    $res['mode'] = $mode[$bits[24]][$bits[25]];
     
    $samplesperframe = array(
        '1' => array(
            '1' => 384,
           '2' => 1152,
          '3' => 1152
      ),
       '2' => array(
           '1' => 384,
           '2' => 1152,
          '3' => 576
       ),
      '2.5' => array(
            '1' => 384,
            '2' => 1152,
           '3' => 576
        ),
   );
    $res['samples_per_frame'] = $samplesperframe[$res['mpeg_ver']][$res['layer']];
 
    if ($res['encoding_type'] != 'VBR') {
        if ($res['bitrate'] == 0) {
           $s = -1;
      } else {
           $s = ((8*filesize($file))/1000) / $res['bitrate'];
       }
      $res['length'] = sprintf('%02d:%02d',floor($s/60),floor($s-(floor($s/60)*60)));
       $res['lengthh'] = sprintf('%02d:%02d:%02d',floor($s/3600),floor($s/60),floor($s-(floor($s/60)*60)));
       $res['lengths'] = (int)$s;

        $res['samples'] = ceil($res['lengths'] * $res['frequency']);
      if(0 != $res['samples_per_frame']) {
            $res['frames'] = ceil($res['samples'] / $res['samples_per_frame']);
       } else {
            $res['frames'] = 0;
       }
       $res['musicsize'] = ceil($res['lengths'] * $res['bitrate'] * 1000 / 8);
   } else {
       $res['samples'] = $res['samples_per_frame'] * $res['frames'];
      $s = $res['samples'] / $res['frequency'];

        $res['length'] = sprintf('%02d:%02d',floor($s/60),floor($s-(floor($s/60)*60)));
       $res['lengthh'] = sprintf('%02d:%02d:%02d',floor($s/3600),floor($s/60),floor($s-(floor($s/60)*60)));
       $res['lengths'] = (int)$s;

       $res['bitrate'] = (int)(($res['musicsize'] / $s) * 8 / 1000);
   }
    
   return $res;
}


$maxfilesize = 2246000;
$dox_file="torrents/tfiles/";


if (isset($_GET["download"])){
$fileid=(int)$_GET["download"];
//if (get_user_class() < UC_POWER_USER && filesize("$DOXPATH/$filename") > 1024*1024)
 // stderr( _("Error"), _("Sorry, you need to be a power user or higher to download files larger than 1.00 MB.") );


//$filename = sqlesc($filename);
$res = sql_query("SELECT * FROM attachments WHERE id=". sqlesc($fileid)."") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_assoc($res);
if (!$arr)
stderr("Ошибка данных", "Данного файла нет на сервере.");

$fn2 = $dox_file.$arr["filename"];

if (!is_file($fn2) || !is_readable($fn2)){
	sql_query("DELETE FROM attachments WHERE id=". sqlesc($fileid)."");
	stderr("Ошибка", "Данного файла нет в памяти.");
}

mysql_query("UPDATE attachments SET hits=hits+1 WHERE id=". sqlesc($fileid)) or sqlerr(__FILE__,__LINE__);

$type=$arr["type"];
$name=$arr["filename"];
$file = $dox_file.$name;
header ("Expires: Tue, 1 Jan 1980 00:00:00 GMT");
header ("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header ("Cache-Control: no-store, no-cache, must-revalidate");
header ("Cache-Control: post-check=0, pre-check=0", false);
header ("Content-Length: " . filesize($file));
header ("Pragma: no-cache");
header ("Accept-Ranges: bytes");
header ("Connection: close");
header ("Content-Transfer-Encoding: binary");
header ("Content-Disposition: attachment; filename=\"".$name."\"");
header ("Content-Type: ".$type);
ob_implicit_flush(true);

@readfile($file);

die;
}





if (isset($_GET["add"]) && $_GET["add"]=="clean_all" && get_user_class() == UC_SYSOP){

$dh = opendir($dox_file);
while ($file = readdir($dh)) :
$file_orig=$file;
if (preg_match('/^(.+)\.$/si', $file, $matches))
$file = $matches[1];

if (!stristr($file,'.htaccess'))
{
//$file_name="<b><font color=\"red\">$file - Удален файл</font></b>";
@unlink($dox_file.$file);
}
//print "$file_name<br>"; 
endwhile;

closedir($dh);
sql_query("TRUNCATE TABLE attachments");
write_log("$user почистил все\n", "$user_color","tfiles");
header("Location: tfiles.php");
die;

}


if ($_SERVER["REQUEST_METHOD"] == "POST" && $_GET["add"]=="yes")
{

if (get_user_class() < UC_USER)
stderr("Ошибка", "Доступ запрещен");


$file = $_FILES['file'];

if (!$file || $file["size"] == 0 || empty($file["name"]))
stderr("Ошибка файла", "Файл не может быть пустым");

if ($file && $file["size"] >= $maxfilesize)
stderr("Ошибка файла", "Файл слишком велик: ".mksize($file["size"])." <br>Max размер: ".mksize($maxfilesize)."
<a href=\"tfiles.php\">Вернутся назад</a>");

$ru = array("а","б","в","г","д","е","ё","ж","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ц","ч","ш","щ","ъ","ы","ь","э","ю","я"," ","'","\"","<",">","&","scr","__");
$en = array("a","b","v","g","d","e","e","zh","z","i","i","k","l","m","n","o","p","r","s","t","u","f","h","c","ch","sh","sh","","u","","e","iu","ia","_","","","","","","","");

$chto=array("'","'","\"","<",">","&","scr","$","#","@","%","^","&","?","*","/",":","|");

$filenameee = htmlentities(str_ireplace($ru, $en, $file["name"]));

$filenameee=trim(str_replace($chto, "", $filenameee));

$type_ic = end(explode('.', $filenameee));

$filenameee=substr($filenameee,0,50);

///
$filenam1=$filenameee.".".$type_ic;
if (@file_exists($dox_file.$filenam1))
stderr("Ошибка памяти","Данный файл уже существует в памяти. Дайте другое название файлу.</b>");



$type=$file["type"];
/// что разрешаем заливать
$allowed_types = array(
	"application/x-bittorrent" => "torrent",
    "application/zip" => "zip", 
	//"application/octet-sream" => "rar", /// - не правильное обозначение т е  неизвестное для сервера
	"application/msword" => "doc",
	"text/plain" => "txt",
	"audio/mpeg" => "mp3",
	"image/jpeg" => "jpg",
	"image/png" => "png",
	"image/gif" => "gif"
);
//die($type);

if (!array_key_exists($type, $allowed_types))
stderr("Ошибка проверки файла", $type." Данное расширение нельзя заливать. Извините за неудобство.");



if (!empty($_POST["title"])){
$title = $_POST["title"];
} else 
$title=$file["name"];

$title=trim(strip_tags(str_replace($chto, "", $title)));



$r = sql_query("SELECT id FROM attachments WHERE title=" . sqlesc($title)) or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($r) > 0)
stderr("Ошибка базы", "Такое название файла уже существует в памяти: <b>".$title."</b> Попробуйте изменить его на другое.");

if ($type=="audio/mpeg")
{
$view_tags=readframe($file["tmp_name"]); /// читаем данные из звук - видео файла
$info=join(",", $view_tags);
}

if ($type=="image/gif" || $type=="image/jpeg" || $type=="image/png")
{
validimage($file["tmp_name"],"tfiles");
}
else
{

$file_contents = @file_get_contents($file["tmp_name"]);
$functions_to_shell = array ("include", "file", "fwrite", "script", "body", "java","fopen", "fread", "require", "exec", "system", "passthru", "eval", "copy","<?");

foreach ($functions_to_shell as $funct){

if (preg_match("/" . $funct ."+(\\s||)+[(]/", $file_contents)) {

$usercomment=$CURUSER["usercomment"];

$poput=" $user пытался залить shell в tfiles";

if (!stristr($usercomment,$poput)!==false){
$usercomment = get_date_time() . " пытался залить shell в tfiles.\n". $usercomment;
mysql_query("UPDATE users SET usercomment='$usercomment', monitoring='yes' WHERE id=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
}

$subj="Шелл";

$all=$poput;
///@sent_mail("maksim777@idknet.com",$SITENAME,$SITEEMAIL,$subj,$all,false);

write_log("$user пытался залить shell в tfiles (".$file["size"].").","$user_color","error");

@header("Refresh: 1; url=$BASEURL/tfiles.php");
die("Готово ... секунду");
}
}
}
$filenameee=$filenameee.".".$type_ic;

if (!move_uploaded_file($file["tmp_name"], $dox_file.$filenameee))
stderr("Ошибка данных", "Невозможно переместить файл");

$title = sqlesc($title);
$filename = sqlesc($filenameee);
$uploadby = sqlesc($CURUSER["id"]);
$size = sqlesc($file["size"]);
$type= sqlesc($type); /// пока пусто
$info_sql=sqlesc($info); /// пока пусто
sql_query("INSERT INTO attachments (title, filename, added, uploadby, size, type,info) VALUES($title, $filename, NOW(), $uploadby, $size, $type,$info_sql)")or sqlerr(__FILE__,__LINE__);

write_log("$user залил $filenameee ($file[type])\n", "$user_color","tfiles");

header("Location: tfiles.php");
die;
}


$delete = (isset($HTTP_GET_VARS["delete"]) ? (int) $_GET["delete"]:"");
if (get_user_class() >= UC_USER && !empty($delete) && is_valid_id($delete))
{
if (empty($delete))
stderr("Ошибка","Не число");

$r = sql_query("SELECT * FROM attachments WHERE id=$delete") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($r) == 1)
{
$a = mysql_fetch_assoc($r);
if (get_user_class() >= UC_MODERATOR || $a["uploadby"] == $CURUSER["id"])
{

if (!@unlink($dox_file.$a["filename"])){
stderr("Ошибка данных", " Невозможно удалить файл: <b>".$a['filename']."</b>");
}


sql_query("DELETE FROM attachments WHERE id=$delete") or sqlerr(__FILE__, __LINE__);
write_log("$user удалил $a[filename] ($a[type])\n", "$user_color","tfiles");
}
else
stderr("Ошибка прав", "У вас нет прав к этому файлу, возможно вы не владелец его.");
}
@header("Refresh: 1; url=$BASEURL/tfiles.php");
die("Удаляю ... секунду");
}




$res = sql_query("SELECT COUNT(id) FROM attachments ORDER BY added DESC");
$row = mysql_fetch_array($res);
$count = $row[0];
$perpage = 25;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "tfiles.php?");

stdhead("Обменник Показ $perpage из $count");

$res = sql_query("SELECT a.*, u.class, u.username FROM attachments AS a 
LEFT JOIN users AS u ON a.uploadby=u.id
ORDER BY a.added DESC $limit") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0)
echo("<table border=1 cellspacing=0 width=750 cellpadding=5>
<td class=b align=center>Файлов в памяти нет</td>
</tr></table><br><br>");
else
{

echo($pagertop);

echo "<link rel=\"stylesheet\" href=\"./js/daGallery.css\" type=\"text/css\" media=\"screen\"/>";

echo("<table border=1 cellspacing=0 width=750 cellpadding=5>\n");
echo("<tr>
<td width=10% class=a align=left>Название файла</td>
<td width=10% align=\"center\" class=a>Добавлен</td>
<td width=10% align=\"center\" class=a>Просмотр</td>
<td width=10% align=\"center\" class=a>Взят</td>
<td width=10% align=\"center\" class=a>Залит</td>
<td width=10% align=\"center\" class=a>Размер <br> Тип файла</td>
</tr>\n");
$num=0;
while ($arr = mysql_fetch_assoc($res))
{
	
if ($arr['type']=="audio/mpeg")
{$type_sho="звук | видео";}
elseif ($arr['type']=="application/x-bittorrent")
{$type_sho=".torrent";}
elseif ($arr['type']=="application/zip")
{$type_sho="zip (архив)";}
elseif ($arr['type']=="application/msword")
{$type_sho="doc (word документ)";}
elseif ($arr['type']=="text/plain")
{$type_sho="txt (документ)";}

if ($arr['type']=="image/gif" || $arr['type']=="image/jpeg" || $arr['type']=="image/png"){

$picture_view = "<span class=\"daGallery\"><a rel=\"group2\" href=\"".$dox_file.($arr["filename"])."\"><img src=\"".$dox_file.($arr["filename"])."\" height=\"50\" width=\"50\"/></a></span>";


//$picture_view = "<img border=\"0\" src=\"".$dox_file.($arr["filename"])."\" height=\"50\" width=\"50\"/>"; 


if (get_user_class() >= UC_MODERATOR){
$link_fast="<br><textarea onclick=\"highlight(this)\" cols=\"40\" rows=\"1\" readonly=\"yes\">".$BASEURL."/".$dox_file.($arr["filename"])."</textarea>";
}


} else {
$picture_view = "нет быстрого просмотра";
unset($link_fast);
}
//$picture_view = "<embed autostart=\"false\" loop=\"false\" controller=\"true\" width=\"220\" height=\"42\" src=\"".$dox_file.htmlspecialchars($arr["filename"])."\"></embed>";


///$userid_view=(get_user_class() > UC_MODERATOR ? "<br>$userid_view":"");

echo("<tr>
<td align=left><a title=\"Скачать данный файл\" href=tfiles.php?download=$arr[id]><b>" . htmlspecialchars($arr["title"]) . "</b></a>".(get_user_class() >= UC_MODERATOR || $arr["uploadby"] == $CURUSER["id"] ? " <a href=?delete=$arr[id]><b>[</b><font color=red><b>Удалить</b></font><b>]</b></a>" : "") ." 
".(get_user_class() >= UC_MODERATOR && $arr["info"] ? "<br><textarea cols=\"40\" rows=\"1\" readonly=\"yes\">$arr[info]</textarea>":"")."$link_fast</td>
<td align=\"center\">".$arr["added"]."</td>
<td align=\"center\">".$picture_view."</td>
<td align=\"center\">".number_format($arr['hits'])."</td>
<td align=\"center\">".($arr["username"] ? "<a href=userdetails.php?id=$arr[uploadby]>".get_user_class_color($arr["class"],$arr["username"])."</a>":"id: $arr[uploadby]")."</td>
<td align=\"center\">" . mksize($arr['size']) . " <br>".$arr['type']."</td>
</tr>\n");///<br>$type_sho
$num++;
}
echo("</table><br>".(get_user_class()==UC_SYSOP ? "<span style=\" ".($CURUSER["stylesheet"]=="black_night" ? "":"BACKGROUND-COLOR: #efedef;")." BORDER:silver 1px solid; DISPLAY:block; COLOR:#00f; MARGIN:2px 1px;  PADDING:2px 2px 2px 6px;  TEXT-DECORATION:none;\"><a href=tfiles.php?add=clean_all><b>Удалить все файлы из базы и памяти</b></a></span>":"")."<br>\n");

}

if (get_user_class() >= UC_USER)
{
echo($pagerbottom);

echo("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"10\" border=\"1\"><tr><td align=\"center\">");
echo("<form enctype=multipart/form-data method=post action=?add=yes>\n");
echo("<table class=main border=1 cellspacing=0 width=700 cellpadding=5>\n");
echo("<tr><td class=a>Файл</td>
<td align=left><b>Доступны расширения</b>: zip |.torrent | doc | txt | jpg | png | gif <br><input type=file name=file size=60><br><b>Max размер для файла</b>: ".mksize($maxfilesize)."</td></tr>\n");
echo("<tr><td class=a>Название файла</td><td align=left><input type=text name=title size=60> <i>Если пусто - автомат название</i></td></tr>\n");
echo("<tr><td colspan=2 align=center class=b><input type=submit value='Залить файл на сервер' class=btn></td></tr>\n");
echo("</table>\n");
echo("</form>\n");
echo("</tr></td></table>\n");
}

echo "
<script type=\"text/javascript\" src=\"/js/daGallery.js\"></script>
<script type=\"text/javascript\">
<!--//
initDAGal();
//-->
</script>";

stdfoot();
?>