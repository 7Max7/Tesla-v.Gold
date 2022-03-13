<?php

/**
 * @author 7Max7
 * @copyright 2010
 */

require_once("include/bittorrent.php");
gzip();
dbconn(false);

if (get_user_class() < UC_SYSOP || get_user_class() <> UC_SYSOP){
stderr("Ошибочка", "Дальше еще интереснее"); 
die();
}






///http://tesla-tracker.net/3by.php?id=125


include (ROOT_PATH."include/zip.lib.php");

$numd = (int) $_GET["id"];

$lfirst = 250; /// по сколько упаковываем торрентов

if (empty($numd))
$limit = "1,".$lfirst;
else
$limit = ($numd*$lfirst).",".$lfirst;

$resnum = sql_query("SELECT COUNT(*) AS num FROM torrents") or sqlerr(__FILE__, __LINE__);
$arrnum = mysql_fetch_array($resnum);

$ostal = ($arrnum["num"] - ($numd*$lfirst));
$ostalos = number_format(($ostal/$lfirst),0);


//if ($numd >=$ostalos)
//die("Половина взята <title>ПОЛОВИНА!</title>");


echo "<script>setTimeout('document.location.href=\"3by.php?id=".$numd."\"', 100000);</script>";



echo "<title>$ostal для $ostalos раз</title>";




$res = sql_query("SELECT id,image1 FROM torrents LIMIT $limit") or sqlerr(__FILE__, __LINE__);


if (mysql_num_rows($res) == 0)
die("Все, данных уже нет. Забираем архивы... $limit");

echo "Подготовка $lfirst штук к выборке ...<br>";

while ($arr = mysql_fetch_array($res)){
	
if (!empty($arr["image1"]) && !preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $arr["image1"])){
$aryfile[] = "torrents/images/".$arr["image1"];
}

$aryfile[] = "torrents/".$arr["id"].".torrent";
$id[] = $arr["id"];

}

echo "Расчет данных ...<br>";

$min = min($id);
$max = max($id);

$ziperlin = "torzip/torrents_".$numd."_llm-".$lfirst."_($min-$max).zip";
@unlink($ziperlin);

echo "Добавляем файлы к записи...<br>";

$ziper = new zipfile();
$ziper->addFiles($aryfile);  //array of files
$ziper->output($ziperlin); 


if (file_exists($ziperlin)){

echo "Успешно записано... Размер: <b>".mksize(filesize($ziperlin))."</b><br>";

++$numd;
}

echo "<script>setTimeout('document.location.href=\"3by.php?id=".$numd."\"', 10000);</script>";




?>