<?
require_once 'include/bittorrent.php';

header("Content-Type: text/html; charset=windows-1251");
dbconn(false,true);

function t($t=false){
    if(!$t) return date('c'); //2004-02-12T15:19:21+00:00
    return date('c',strtotime($t));
}
/// создаем карту сайта
  
if (get_user_class() < UC_SYSOP) {
   stdmsg("Ошибка","Доступ в этот раздел закрыт!", error); 
   stdfoot(); 
  die(); 
}

$site_own = (($_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://").htmlspecialchars_uni($_SERVER['HTTP_HOST']);
$parse_owner = parse_url($site_own, PHP_URL_HOST);

$parse_owner = end(explode('.', $parse_owner));

$num_id = (int) $_GET["id"];


$lfirst = 49000;



if (empty($num_id))
$limit = "1,".$lfirst;
else
$limit = ($num_id*$lfirst).",".$lfirst;

//die($limit);



$sql = sql_query("SELECT id,added FROM torrents ORDER BY id DESC LIMIT $limit");/// 49000,98000  - 98000,147000

if (mysql_num_rows($sql) == 0)
die("Готово, больше в базе нет значений. <script>setTimeout('document.location.href=\"premod.php\"', 5000);</script>");


$DEFAULTBASEURL = "http://".htmlspecialchars_uni($_SERVER['HTTP_HOST'])."/";


$num=7; /// в конечном файле не должен превышать более 49980 ссылок
$size=0; /// конечный файл не должен превышать 10485760 (10 МБ)


$txt = '<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

$txt .='<url>
<loc>'.$DEFAULTBASEURL.'browse.php</loc>
<lastmod>'.t().'</lastmod>
<changefreq>hourly</changefreq>
<priority>1</priority></url>
<url>
<loc>'.$DEFAULTBASEURL.'</loc>
<lastmod>'.t().'</lastmod>
<changefreq>hourly</changefreq>
<priority>1</priority>
</url>
';


$size=$size+strlen($txt);

if ($num<"49000"){

while($a = mysql_fetch_assoc($sql)){
if ($size<"10485760"){
$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'details.php?id='.$a['id'].'</loc>
<lastmod>'.t($a['added']).'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.80</priority>
</url>';
++$num;
$size=strlen($txt);
}}}

$txt.='</urlset>';


@file_put_contents(ROOT_PATH."sitemap_".$parse_owner."_torrent".(empty($num_id) ? "":"_".$num_id).".xml",$txt);

$file=ROOT_PATH."sitemap_".$parse_owner."_torrent".(empty($num_id) ? "":"_".$num_id).".xml";


echo $DEFAULTBASEURL."sitemap_".$parse_owner."_torrent".(empty($num_id) ? "":"_".$num_id).".xml - ".mksize(filesize($file));


echo "<br>"; 

++$num_id;

echo "<script>setTimeout('document.location.href=\"sitemap_parts_2.php?id=".($num_id)."\"', 1000);</script>";


?>