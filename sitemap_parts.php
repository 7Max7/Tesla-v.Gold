<?
require_once 'include/bittorrent.php';

header ("Content-Type: text/html; charset=windows-1251");
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



$type=(isset($_GET["type"])? $_GET["type"]:"");


if ($type=="all")
echo "<script>setTimeout('document.location.href=\"sitemap_parts.php?type=users\"', 1000);</script>";
elseif ($type=="users")
echo "<script>setTimeout('document.location.href=\"sitemap_parts.php?type=torrent\"', 1000);</script>";
elseif ($type=="torrent")
echo "<script>setTimeout('document.location.href=\"sitemap_parts.php?type=tags\"', 1000);</script>";
elseif ($type=="tags")
echo "<script>setTimeout('document.location.href=\"sitemap_parts.php?type=forums\"', 1000);</script>";
elseif ($type=="forums")
echo "<script>setTimeout('document.location.href=\"sitemap_parts.php?type=browse\"', 1000);</script>";
elseif ($type=="browse")
echo "<script>setTimeout('document.location.href=\"sitemap_parts.php?type=category\"', 1000);</script>";
elseif ($type=="category")
echo "<script>setTimeout('document.location.href=\"sitemap_parts_2.php\"', 1000);</script>";
else
echo "<script>setTimeout('document.location.href=\"sitemap_parts.php?type=all\"', 1000);</script>";
//else
//unset($type);




if (empty($type))
die("<center>
[<a href='sitemap_parts.php?type=all'>sitemap_$parse_owner</a>]

[<a href='sitemap_parts.php?type=users'>sitemap_users</a>]

[<a href='sitemap_parts.php?type=torrent'>sitemap_torrent</a>]

[<a href='sitemap_parts.php?type=category'>sitemap_category</a>]

[<a href='sitemap_parts.php?type=tags'>sitemap_tags</a>]

[<a href='sitemap_parts.php?type=forums'>sitemap_forums</a>]

[<a href='sitemap_parts.php?type=browse'>sitemap_browse</a>]
</center>");



$DEFAULTBASEURL = "http://".htmlspecialchars_uni($_SERVER['HTTP_HOST'])."/";


$num=7; /// в конечном файле не должен превышать более 49980 ссылок
$size=0; /// конечный файл не должен превышать 10485760 (10 МБ)


$txt = '<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

$txt .='
<url>
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
<url><loc>'.$DEFAULTBASEURL.'forums.php</loc>
<lastmod>'.t().'</lastmod>
<changefreq>weekly</changefreq>
<priority>1</priority>
</url>
<url><loc>'.$DEFAULTBASEURL.'license.php</loc>
<lastmod>'.t().'</lastmod>
<changefreq>weekly</changefreq>
<priority>0.90</priority>
</url>
<url><loc>'.$DEFAULTBASEURL.'redir.php</loc>
<lastmod>'.t().'</lastmod>
<changefreq>weekly</changefreq>
<priority>0.50</priority>
</url>
<url><loc>'.$DEFAULTBASEURL.'alltags.php</loc>
<lastmod>'.t().'</lastmod>
<changefreq>weekly</changefreq>
<priority>0.50</priority>
</url>
';

if ($type=="all"){
$size=$size+strlen($txt);

if ($num<"49980"){
$sql = sql_query("SELECT id,added FROM torrents ORDER BY added DESC LIMIT 49000");
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
}}}}

if ($type=="torrent"){
$size=$size+strlen($txt);

if ($num<"49980"){
$sql = sql_query("SELECT id,added FROM torrents ORDER BY id DESC LIMIT 1,49000");
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
}}}}

if ($type=="users"){
if ($num<"49980"){
$sql2 = sql_query("SELECT id FROM users");
while($a2 = mysql_fetch_assoc($sql2)){
if ($size<"10485760"){
$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'userdetails.php?id='.$a2['id'].'</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.75</priority>
</url>';
++$num;
$size=strlen($txt);
}}}}


if ($type=="forums"){

if ($num<"49980"){
$sql2 = sql_query("SELECT id FROM forums WHERE minclassread='0'");
while($a2 = mysql_fetch_assoc($sql2)){
if ($size<"10485760"){
$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'forums.php?action=viewforum&amp;forumid='.$a2['id'].'</loc>
<lastmod>'.t().'</lastmod>
<changefreq>weekly</changefreq>
<priority>0.90</priority>
</url>';
++$num;
$size=strlen($txt);
}}}

if ($num<"49980"){
$sql2 = sql_query("SELECT id FROM topics");
while($a2 = mysql_fetch_assoc($sql2)){
if ($size<"10485760"){
$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'forums.php?action=viewtopic&amp;topicid='.$a2['id'].'</loc>
<lastmod>'.t().'</lastmod>
<changefreq>weekly</changefreq>
<priority>0.90</priority>
</url>';
++$num;
$size=strlen($txt);
}}}


if ($num<"49980"){
$sql2 = sql_query("SELECT id FROM posts");
while($a2 = mysql_fetch_assoc($sql2)){
if ($size<"10485760"){
$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'forums.php?action=viewpost&amp;id='.$a2['id'].'</loc>
<lastmod>'.t().'</lastmod>
<changefreq>weekly</changefreq>
<priority>0.50</priority>
</url>';
++$num;
$size=strlen($txt);
}}}


}


if ($type=="browse"){
if ($num<"49980"){


$res = sql_query("SELECT COUNT(*) FROM torrents LIMIT 49980") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$count = $row[0];

$count=$count/25; // это наши странички

if ($size<"10485760"){

for ($x1=0; $x1<$count; $x1++){

$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?incldead=1&amp;page='.$x1.'</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.50</priority>
</url>';
++$num; /// тут уже не уместно :-D
}

for ($x1=0; $x1<$count; $x1++){

$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?incldead=2&amp;page='.$x1.'</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.50</priority>
</url>';
++$num; /// тут уже не уместно :-D
}


for ($x1=0; $x1<$count; $x1++){

$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?incldead=10&amp;page='.$x1.'</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.50</priority>
</url>';
++$num; /// тут уже не уместно :-D
}

for ($x1=0; $x1<$count; $x1++){

$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?incldead=8&amp;page='.$x1.'</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.50</priority>
</url>';
++$num; /// тут уже не уместно :-D
}


$size=strlen($txt);
}}}


if ($type=="category"){
if ($num<"49980"){


$sql = sql_query("SELECT id FROM categories");
while($a = mysql_fetch_assoc($sql)){
if ($size<"10485760"){
$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?cat='.$a['id'].'</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.70</priority>
</url>';
++$num; /// тут уже не уместно :-D
$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?cat='.$a['id'].'</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.70</priority>
</url>';
++$num; /// тут уже не уместно :-D

$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?cat='.$a['id'].'&amp;incldead=1</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.50</priority>
</url>';
++$num; /// тут уже не уместно :-D

$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?cat='.$a['id'].'&amp;incldead=10</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.50</priority>
</url>';
++$num; /// тут уже не уместно :-D

$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?cat='.$a['id'].'&amp;incldead=2</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.50</priority>
</url>';
++$num; /// тут уже не уместно :-D

$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?cat='.$a['id'].'&amp;incldead=6</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.50</priority>
</url>';
++$num; /// тут уже не уместно :-D

$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?cat='.$a['id'].'&amp;incldead=4</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.50</priority>
</url>';
++$num; /// тут уже не уместно :-D

$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?cat='.$a['id'].'&amp;incldead=8</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.50</priority>
</url>';
++$num; /// тут уже не уместно :-D




$size=strlen($txt);
}}

$sql = sql_query("SELECT id FROM humor LIMIT 20000");
while($a = mysql_fetch_assoc($sql)){
if ($size<"10485760"){
$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'humor.php?id='.$a['id'].'</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.50</priority>
</url>';
++$num; /// тут уже не уместно :-D

$size=strlen($txt);
}}



}}

if ($type=="tags"){
if ($num<"49980"){
$sql = sql_query("SELECT id, name FROM tags LIMIT 49000");
while($a = mysql_fetch_assoc($sql)){
if ($size<"10485760"){
$txt .='
<url>
<loc>'.$DEFAULTBASEURL.'browse.php?tag=' . urlencode($a["name"]) . '</loc>
<lastmod>'.t().'</lastmod>
<changefreq>monthly</changefreq>
<priority>0.70</priority>
</url>';
++$num;
$size=strlen($txt);
}}}}


$txt.='</urlset>';


if ($type=="category")
$filenam="category";

if ($type=="users")
$filenam="users";

if ($type=="torrent")
$filenam="torrent";

if ($type=="tags")
$filenam="tags";

if ($type=="forums")
$filenam="forums";

if ($type=="browse")
$filenam="browse";



if (!empty($filenam) && $type<>"all"){
@file_put_contents(ROOT_PATH."sitemap_".$parse_owner."_".$filenam.".xml",$txt);
$file=ROOT_PATH."sitemap_".$parse_owner."_".$filenam.".xml";
}
if ($type=="all"){
@file_put_contents(ROOT_PATH."sitemap_".$parse_owner.".xml",$txt);
$file=ROOT_PATH."sitemap_".$parse_owner.".xml";
}


echo "<center>";

if (!empty($filenam) && $type<>"all")
echo $DEFAULTBASEURL."sitemap_".$parse_owner."_".$filenam.".xml - ".mksize(filesize($file));
if ($type=="all")
echo $DEFAULTBASEURL."sitemap_".$parse_owner.".xml - ".mksize(filesize($file));

echo "<br>"; 

die("<center>
[<a href='sitemap_parts.php?type=all'>sitemap_$parse_owner</a>]

[<a href='sitemap_parts.php?type=users'>sitemap_users</a>]

[<a href='sitemap_parts.php?type=torrent'>sitemap_torrent</a>]

[<a href='sitemap_parts.php?type=category'>sitemap_category</a>]

[<a href='sitemap_parts.php?type=tags'>sitemap_tags</a>]

[<a href='sitemap_parts.php?type=forums'>sitemap_forums</a>]

[<a href='sitemap_parts.php?type=browse'>sitemap_browse</a>]
</center>");


?>