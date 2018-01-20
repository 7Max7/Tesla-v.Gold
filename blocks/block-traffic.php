<?
if (!defined('BLOCK_FILE')) {
    header("Location: ../index.php");
    exit;
}


$cacheStatFile = "cache/block-traffic.txt"; 
$expire = 120*60; // 2 часа
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
	

$blocktitle = "Траффик Трекера";


global $tracker_lang;

$con = sql_query("SELECT userid FROM peers WHERE userid>'0' GROUP by userid");
$connected = mysql_num_rows($con);	

$con2 = sql_query("SELECT COUNT(*) FROM peers  GROUP by torrent");
$seedeing = number_format(mysql_num_rows($con2));	
//$seedeing = number_format(get_row_count("peers", "GROUP by torrent")); 


$connectguest = number_format(get_row_count("peers","WHERE userid='0'"));

//request to db 
$res = sql_query("SELECT SUM(uploaded) FROM users") or sqlerr(__FILE__, __LINE__); 
$arr = mysql_fetch_assoc($res); 
//calculate dates 
$datebegin = "2008-12-01"; //change this date to birthday of your tracker 
$seconds_from_begin = date('U', strtotime($datebegin)); 
$seconds_now = date('U'); 
$days_from_begin_tmp = ($seconds_now - $seconds_from_begin) / 86400; 
$days_from_begin = $days_from_begin_tmp; 
//calculate total 
$middle = $arr['SUM(uploaded)'] / $days_from_begin;
$middle = mksize($middle);
$TotalUploadedTB=mksize($arr['SUM(uploaded)']);
//request to db for totalFileSize
//$res = sql_query("SELECT SUM(size) FROM torrents WHERE visible='yes' and banned='no' and moderated='yes' and multitracker='no'") or sqlerr(__FILE__, __LINE__); 
$res = sql_query("SELECT SUM(size) FROM torrents") or sqlerr(__FILE__, __LINE__); 
$sumsize = mysql_fetch_assoc($res);
$registered = number_format(get_row_count("users")); 


//$redss = sql_query("SELECT COUNT(*) FROM torrents WHERE visible='yes' and multitracker='yes'") or sqlerr(__FILE__, __LINE__); 
//$sze = mysql_fetch_assoc($redss);

$outor = number_format(get_row_count("torrents", "WHERE multitracker='yes'")); 

//$ingtor=number_format($sze["intor"]);
//$outor=number_format($sze["outor"]);


$peers = number_format(get_row_count("peers")); 


$re = sql_query("SELECT SUM(f_seeders) AS f_seeders,SUM(f_leechers) AS f_leechers FROM torrents FORCE INDEX(category_visible) WHERE multitracker='yes'") or sqlerr(__FILE__, __LINE__); 
$suze = mysql_fetch_assoc($re); 

$f_seeders=number_format($suze["f_seeders"]);
$f_leechers=number_format($suze["f_leechers"]);

$sred=number_format($suze["f_leechers"]/$suze["f_seeders"]);

///<a href=topten.php></a>
$content = "<center>Всего траффика <b>".$TotalUploadedTB."</b> за <b>".$days_from_begin."</b> дней. <br>В среднем в день по <b>".$middle."</b>. 
<br>Суммарный объём раздаваемых файлов: <b title=\"Без Мультитрекерности\">".mksize($sumsize['SUM(size)'])."</b>.
<br>Пользователей: <b>".$registered."</b>. 
<br>Сидируется <b>".$seedeing."</b> торрент на <b>".$peers."</b> подключениях, где пользователи: <b>".$connected."</b> уникальные и <b>".$connectguest."</b> без регистрации.<br>
<br> <i>Мультираздачи</i><hr>
Торренты: <b>".$outor."</b><br>
Пиры: <b>".$f_seeders."</b> <br>
Сиды: <b>".$f_leechers."</b><br>
</center>" ; 


$fp = fopen($cacheStatFile,"w");
   if($fp)   {
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
 
 if (get_user_class() >= UC_SYSOP)
{
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
}

?>