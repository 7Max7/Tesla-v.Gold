<?php

/**
 * @author 7Max7
 * @copyright 2010
 */

require ("include/bittorrent.php");

dbconn();



//// ������ �������������� ������� �� ������� �� ������������� � ������ ������� ��� ����������.




if (isset($_GET["gettop"])){

$num = 0;
$numzie = 0;
$res = sql_query("SELECT id, filename, (SELECT sum(filename) FROM torrents)AS sumi FROM torrents ORDER BY filename DESC LIMIT 100") or sqlerr(__FILE__,__LINE__);

while ($row = mysql_fetch_array($res)) {


echo "id: <a href='1im.php?getresize&id=".$row["id"]."'>".$row["id"]."</a> � ������ ".mksize($row["filename"]);
echo "<br>";

++$num;
$numzie = $row["filename"]+$numzie;
$fileall = $row["sumi"];
}

echo "����� ������ � ���� ".mksize($fileall);
echo "<title>".mksize($numzie)."</title>";
echo "<script>setTimeout('document.location.href=\"1im.php?gettop\"', 90000);</script>";

die;
}







if (isset($_GET["getclear"])){
sql_query("UPDATE torrents SET filename=''") or sqlerr(__FILE__, __LINE__);

sql_query("UPDATE torrents SET filename='0' WHERE image1 LIKE '%http%'") or sqlerr(__FILE__, __LINE__);
sql_query("UPDATE torrents SET filename='0' WHERE image1 = ''") or sqlerr(__FILE__, __LINE__);

die("����");
}


if (isset($_GET["get"])){

$num = $numzie = 0;

$res = sql_query("SELECT id, image1, (SELECT COUNT(*) FROM torrents WHERE filename='') AS numi FROM torrents WHERE image1 NOT LIKE '%http%' AND image1<>'' AND filename='' ORDER BY added LIMIT ".(rand(1,10)%2==1 ? "5000,5000":"5000")."") or sqlerr(__FILE__,__LINE__);

while ($row = mysql_fetch_array($res)) {

$image = @file_get_contents(ROOT_PATH."torrents/images/".$row["image1"]);
$imagestr = strlen($image); 

if ($imagestr <= "37853"){
list($width_in, $height_in) = getimagesize(ROOT_PATH."torrents/images/".$row["image1"]);

if (!empty($width_in) && !empty($height_in) && $height_in <="100" && $width_in <="100"){

echo "<b>���������1</b> ";
unlink(ROOT_PATH."torrents/images/".$row["image1"]);
sql_query("UPDATE torrents SET filename='0' WHERE id = ".$row["id"]) or sqlerr(__FILE__, __LINE__);
}

} 
if(empty($image) && empty($imagestr)){
echo "<b>���������2</b> ";
@unlink(ROOT_PATH."torrents/images/".$row["image1"]);
sql_query("UPDATE torrents SET filename='0' WHERE id = ".$row["id"]) or sqlerr(__FILE__, __LINE__);
}


unset($image);

echo "id: ".$row["id"]." � ".$row["image1"]." ������ ".mksize($imagestr)."($imagestr)";
echo "<br>";

$numzie = $imagestr+$numzie;
sql_query("UPDATE torrents SET filename=".sqlesc($imagestr)." WHERE id = ".$row["id"]) or sqlerr(__FILE__, __LINE__);
++$num;
$numi = number_format($row["numi"],1);
}

echo "����� $num - �������� $numi<br>";
echo "<title>".mksize($numzie)."</title>";

if (isset($_GET["get"]) && $num)
echo "<script>setTimeout('document.location.href=\"1im.php?get\"', 10000);</script>";

die;
}









if (isset($_GET["getresize"])){



$id = $_GET["id"];

$res = sql_query("SELECT image1 FROM torrents WHERE id = ".sqlesc($id));
$row = mysql_fetch_array($res);


$Min_width2 = 1000;
$Min_height2 = 500;

$fileurl = ROOT_PATH."torrents/images/".$row["image1"]; /// ������ �����
$fileend = ROOT_PATH."torrents/images/".$id.".png"; /// ���� ��������

if (file_exists($fileurl)){

$imageurl = filesize($fileurl);

$mime = tolower(end(explode('.', $fileurl)));
///mime_content_type �� ��������� ��������, ����� ������������
if ($mime == "jpg" || $mime == "jpeg")
$imag1 = imagecreatefromjpeg($fileurl);
elseif ($mime == "gif")
$imag1 = imagecreatefromgif($fileurl);
elseif ($mime == "png")
$imag1 = imagecreatefrompng($fileurl);
elseif ($mime == "bmp")
$imag1 = imagecreatefromwbmp($fileurl);


if ($imag1){

$width2 = $width = imagesx($imag1);
$height2 = $height = imagesy($imag1);

if ($width > $height) {
 while ($width2 >= $Min_width2) {
    $width2 = $width2/2; 
    $height2 = $height2/2;     
 }
} else {
 while ($height2 >= $Min_height2) {
    $width2 = $width2/2; 
    $height2 = $height2/2;     
 }
}

unlink($fileend);

$imag2 = imagecreatetruecolor($width2, $height2);
imagecopyresampled($imag2, $imag1, 0, 0, 0, 0, $width2, $height2, $width, $height);


$dick = imagepng($imag2,$fileend);


if ($dick && file_exists($fileend)){


$mimurl = end(explode('/', $fileurl));
$mimend = end(explode('/', $fileend));

$imagestr = filesize($fileend);


sql_query("UPDATE torrents SET image1=".sqlesc($mimend).",filename = ".sqlesc($imagestr)." WHERE id = $id") or sqlerr(__FILE__, __LINE__);

echo ("��������� �� $mimurl � $mimend <br>");

echo "������ ��: ".mksize($imageurl)." �����: ".mksize($imagestr)." �����������: ".mksize($imageurl-$imagestr)." <br>";
ImageDestroy($imag2);
}
}

} else die("�� ������ ����, �������� http ������.");
die;
}
///echo "<script>setTimeout('document.location.href=\"1im.php?getresize\"', 30000);</script>";


/*
��������: 10000 ��
�� ��� ��������: 2266 �� (22.66 %)
�������: 6887 ��
MySQL-���� u239980: 847 ��

��������: 10000 ��
�� ��� ��������: 2331 �� (23.31 %)
�������: 6820 ��
MySQL-���� u239980: 849 ��

��������: 10000 ��
�� ��� ��������: 2351 �� (23.51 %)
�������: 6797 ��
MySQL-���� u239980: 852 ��

*/


if (!isset($_GET["getrelink"])){

$idrand = rand(1,10);
$idrand2 = rand(5,20);


//if ($idrand%2==0)
$ordy = "filename DESC";
//else
//$ordy = "added ASC";



$res = sql_query("SELECT id, image1, filename FROM torrents WHERE filename>'0' ORDER BY $ordy LIMIT 3") or sqlerr(__FILE__,__LINE__);
//image1 NOT LIKE '%http%' AND image1<>'' AND 


//sql_query("UPDATE torrents SET filename='0' WHERE image1 LIKE '%http%'") or sqlerr(__FILE__, __LINE__);



while ($row = mysql_fetch_array($res)) {

$file = ROOT_PATH."torrents/images/".$row["image1"];
list($old_width, $old_height) = getimagesize($file); 

if (file_exists($file))
$caimage = imageshack($file);


if ($caimage <> false){

list($new_width, $new_height) = getimagesize($caimage); 

if ($new_width==$old_width && $new_height==$old_height){
unlink($file);
sql_query("UPDATE torrents SET image1=".sqlesc($caimage).", filename='0' WHERE id = ".$row["id"]) or sqlerr(__FILE__, __LINE__);
echo "id: <a href='details.php?id=".$row["id"]."'>".$row["id"]."</a> ��������� ������.";
echo "<br>";
} else
{
echo "id: ".$row["id"]." <b>�� ���������</b> ������ ������� �� ��������� $new_width � $new_width ������ $new_height � $old_height.";
echo "<br>";	
}

} else {
//echo "<script>setTimeout('document.location.href=\"1im.php?id=123\"', 150);</script>";
echo "id: ".$row["id"]." <b>�������</b>";
sql_query("UPDATE torrents SET filename='0' WHERE id = ".$row["id"]) or sqlerr(__FILE__, __LINE__);
}

}

echo "<title>������ � �����</title>";




	global $tracker_lang, $queries, $tstart, $query_stat, $querytime;

	
		foreach ($query_stat as $key => $value) {
			print("<div>[<b>".($key+1)."</b>] => <b>
			".($value["seconds"] <= 0.0009 ? "<font color=\"#0468f1\" title=\"������������ ������. ����� ���������� ��������.\">".$value["seconds"]."</font>":"
		    ".($value["seconds"] >= 0.01 ? "<font color=\"red\" title=\"������������� �������������� ������. ����� ���������� ��������� �����.\">".$value["seconds"]."</font>" : "<font color=\"blue\" title=\"������ �� ��������� � �����������. ����� ���������� ����������.\">".$value["seconds"]."</font>" )."
		    ")."
		  </b> [".$value["query"]."]</div>\n");
		}
	debug();
	
$seconds = (timer() - $tstart);

$seconds = 	number_format($seconds, -1);
$memory = round(memory_get_usage()/1024);
$time_sql=sprintf("%0.4lf",$querytime);

	echo "<br>����� ��������� $seconds - ������ ��� php $memory ��<br>";


//echo "<script language=\"javascript\" type=\"text/javascript\" src=\"js/timerbar.js\"></script>";
if (!isset($_GET["id"]))
echo "<script>setTimeout('document.location.href=\"1im.php\"', 20000);</script>";
else
echo "<script>setTimeout('document.location.href=\"1im.php?id\"', 15000);</script>";


die;
}














	global $tracker_lang, $queries, $tstart, $query_stat, $querytime;

	
		foreach ($query_stat as $key => $value) {
			print("<div>[<b>".($key+1)."</b>] => <b>
			".($value["seconds"] <= 0.0009 ? "<font color=\"#0468f1\" title=\"������������ ������. ����� ���������� ��������.\">".$value["seconds"]."</font>":"
		    ".($value["seconds"] >= 0.01 ? "<font color=\"red\" title=\"������������� �������������� ������. ����� ���������� ��������� �����.\">".$value["seconds"]."</font>" : "<font color=\"blue\" title=\"������ �� ��������� � �����������. ����� ���������� ����������.\">".$value["seconds"]."</font>" )."
		    ")."
		  </b> [".$value["query"]."]</div>\n");
		}
	debug();
	







?>