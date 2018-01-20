<?php

/**
 * @author 7Max7
 * @copyright 2010
 */
require "include/bittorrent.php"; 
gzip();
dbconn(false); 



$host = "fishki.net";

if (isset($_GET["id"])){

$page = (int) $_GET["id"];

if ($fp = fsockopen($host, 80, $errno, $errstr, 30)) {
// Заголовок HTTP-запроса
$headers = "GET /?page=$page HTTP/1.0\r\n";
$headers.= "Host: $host\r\n";
$headers.= "Content-type: application/x-www-form-urlencoded\r\n";
$headers.= "Content-Length: ".strlen($data)."\r\n";
$headers.= "Accept: *\r\n";
$headers.= "Accept-Charset: *\r\n";
$headers.= "Accept-Encoding: binary\r\n";
$headers.= "Accept-Language: ru\r\n";
$headers.= "Referer: http://$host$path \r\n";
$headers.= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.2; ru; rv:1.9.2) Gecko/20100115 Firefox/3.6 WebMoney Advisor\r\n";
$headers.= "Connection: Close\r\n\r\n";


fwrite($fp, $headers.$data);

$date="";
// Получаем страничку
while (!feof($fp)) {
$line = fgets($fp, 1024);
$date.=$line;
}
fclose($fp);

}

preg_match_all("/<a href=\"http:\/\/fishki.net\/(.*)\" target=\"_blank\">Еще анекдоты. Читать дальше.<\/a>/is", $date, $out); 

preg_match("|(comment.php.*)\" target=\"_blank\">Еще анекдоты. Читать дальше.<\/a>|U", $date, $out);  
//print_r($out);
if ($out[1]){
$link = $out[1];
$link = trim(str_replace("comment.php?id=", "", $link));

if (is_valid_id($link)){

echo $link."<br>";

$sfile = ROOT_PATH."cache/humor_array.txt";
$file = fopen($sfile,"a+"); 
fputs($file,$link."\n"); 
fclose($file); 

//die("Дальше сбор ...<script>setTimeout('document.location.href=\"fishki.php?page=".($page+1)."\"', 1000);</script>");
}


}
preg_match("/<div class=\"information\">/is", $date, $trud);  


if (stristr($date,'существует') && stristr($date,'такой') && !empty($trud))
echo "Неудачен, такой страницы не существует. <br>";
else
die("дальше поиск...<script>setTimeout('document.location.href=\"fishki.php?id=".($page+1)."\"', 1000);</script>");

}




$sfile = ROOT_PATH."cache/humor_array.txt";

if (file_exists($sfile)){

$content = file_get_contents($sfile); 

$link = explode("\n",$content);

$link=array_unique($link); /// удаляем дубликаты

$min = min($link);
$max = min($link);

}


if (!count($link) || $min<>$max)
die(" готово сбор удачен весь, в базу залито все. ");




foreach ($link as $kfs) {

if ($kfs<>$min)
$linki[]=$kfs; /// собираем все ссылки
//echo "$kfdsf <br>";
}

$sfile = ROOT_PATH."cache/humor_array.txt";
if (count($linki)){
unlink($sfile);
$file = fopen($sfile,"a+"); 
fputs($file,implode("\n", $linki)); 
fclose($file); 
} else
unlink($sfile);

if (empty($min))
die(" перенаправление ..<script>setTimeout('document.location.href=\"fishki.php\"', 1000);</script>");



$path = "/comment.php?id=$min";

if ($fp = fsockopen($host, 80, $errno, $errstr, 30)) {
// Заголовок HTTP-запроса
$headers = "GET $path HTTP/1.0\r\n";
$headers.= "Host: $host\r\n";
$headers.= "Content-type: application/x-www-form-urlencoded\r\n";
$headers.= "Content-Length: ".strlen($data)."\r\n";
$headers.= "Accept: *\r\n";
$headers.= "Accept-Charset: *\r\n";
$headers.= "Accept-Encoding: binary\r\n";
$headers.= "Accept-Language: ru\r\n";
$headers.= "Referer: http://$host$path \r\n";
$headers.= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.2; ru; rv:1.9.2) Gecko/20100115 Firefox/3.6 WebMoney Advisor\r\n";
$headers.= "Connection: Close\r\n\r\n";


fwrite($fp, $headers.$data);

$date="";
// Получаем страничку
while (!feof($fp)) {
$line = fgets($fp, 1024);
$date.=$line;
}
fclose($fp);

}

preg_match("/<div class=\"topic\">(.*?)<\/div>/is", $date, $out);  
//print_r($out);

preg_match("/<h1>Анекдоты<\/h1>/is", $date, $true);  

if (!empty($true[0])){

$opisanie = preg_replace('/<h1>Анекдоты<\/h1>/is', "", $out[0]);
$opisanie = preg_replace('/<div class="bottombar">(.*)<\/div>/is', "", $opisanie);

$opisanie = strip_tags($opisanie);

//$opisanie = trim(str_replace("\n\n", "", $opisanie));


$list = explode("\r\n\r\n",$opisanie);

foreach ($list as $anec){

$anec = htmlspecialchars_uni(trim($anec));
echo $anec;

$numsql = (substr($anec,0,20)."");
$hu = sql_query("SELECT txt FROM humor WHERE txt LIKE '%$numsql%'") or sqlerr(__FILE__, __LINE__);
if (!mysql_num_rows($hu) && strlen($anec) > 10){
sql_query("INSERT INTO humor (uid,txt) VALUES(" .  sqlesc("92") . ", " .  sqlesc($anec) . ")") or sqlerr(__FILE__, __LINE__);
echo " - <b>добавлен</b>";
} else 
echo " - <u>не добавлен</u>";

echo "<hr>";

}



}

die(" переключение ..<script>setTimeout('document.location.href=\"fishki.php\"', 1000);</script>");










?>