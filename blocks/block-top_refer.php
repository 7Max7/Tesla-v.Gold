<?
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}

// начинаем кешировать
$cacheStatFile = "cache/block-top_refer.txt"; 
$expire = 120*60; // 120 минут на кеш, после обновление 
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
// тело - то, что кешируем - начало
global $refer_parse;

if ($refer_parse==1){
$content .= "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" class=\"table>\""; 

$content .= "<tr><td align=\"center\" class=\"colhead\">Топ 15</td></tr>"; 
$count = number_format(get_row_count("referrers"));

$maxdt = get_date_time(gmtime() - 86400*31);  /// если месяц назад

/*
$site_own = (($_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://").htmlspecialchars_uni($_SERVER['HTTP_HOST']);
$parse_owner = parse_url($site_own, PHP_URL_HOST);
$parse_owner = explode('.', $parse_owner);
*/
//$parse_owner[1] ///AND parse_url NOT LIKE '%".($parse_owner[1])."%' 

$zapros = sql_query("SELECT parse_url, parse_ref, COUNT(*) AS coun FROM referrers
WHERE date > ".sqlesc($maxdt)." GROUP BY parse_url ORDER BY (SELECT COUNT(*) FROM referrers AS rf WHERE rf.parse_url=referrers.parse_url AND date > ".sqlesc($maxdt).") DESC LIMIT 15") or sqlerr(__FILE__, __LINE__); 

$number=0;

while ($row = mysql_fetch_array($zapros)) {

if ($number%2==0) {
$class="a"; $class2="b"; 
} else {
$class="b"; 	
$class2="a";
}

$parse_ref = htmlspecialchars_decode($row["parse_ref"]); 

$parse_url = htmlspecialchars_decode($row["parse_url"]);
//$parse_url = str_replace("www.", "", $parse_url);

$content .= "<tr><td align=\"center\" class=\"".$class2."\"><a title=\"".$row["coun"]." посещений\" rel=\"nofollow\" href=\"".$parse_ref."\">".$parse_url."</a></td></tr>"; 

++$number;
}

if ($number==0) {
$content .= "<tr><td align=\"center\" class=\"$class2\">Данных нет</td></tr>"; 
}
else
$content .= "<tr><td align=\"center\" class=\"colhead\">Всего данных: ".$count."</td></tr>"; 

$content .= "</table>"; 
}
else 
{
$content .= "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" class=\"table>\""; 
$content .= "<tr><td align=\"center\" class=\"$class2\"><b>Функция отключенна</b></td></tr>"; 
$content .= "</table>"; 
}

$blocktitle = "Рефералы";
// тело - то, что кешируем - конец

$fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }

// заканчиваем кешировать

if (get_user_class() >= UC_SYSOP)
{
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
} 

?>