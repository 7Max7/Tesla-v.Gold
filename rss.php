<?
require "include/bittorrent.php";
dbconn();

$creat_rss_mxl = false; /// создавать rss.xml файл при входе в основной rss.php ????

$passkey = (empty($_GET["passkey"]) ? "":htmlspecialchars($_GET["passkey"]));

if (strlen($passkey) <> 32 && !empty($passkey))
exit("Неверный пасскей. Пожалуйста проверьте данные");

if (!empty($passkey)) {
$user = mysql_fetch_row(sql_query("SELECT COUNT(*) FROM users WHERE passkey = ".sqlesc($passkey)));
if (empty($user[0]))
exit();
}
//else
//loggedinorreturn();

///////// cache
$cache2=new MySQLCache("SELECT id, name,(SELECT COUNT(*) FROM torrents WHERE categories.id=torrents.category) AS num_torrent FROM categories ORDER BY sort ASC", 86400,"browse_genrelist.txt"); // день
while ($cat=$cache2->fetch_assoc())
$category[$cat['id']] = $cat['name'];
///////// cache

/*
// name a category
$res = mysql_query("SELECT id, name FROM categories");
while($cat = mysql_fetch_assoc($res))
$category[$cat['id']] = $cat['name'];
*/


$DESCR = "RSS рассылка";

// by category ? patch
$cat_get = (empty($_GET['cat']) ? "": htmlspecialchars($_GET['cat']));

if (!empty($cat_get)) {

$cats_backup = explode(",", $cat_get);
$cats = array();

foreach ($cats_backup as $ca){
if (is_valid_id($ca))
$cats[] = $ca;
}

if (count($cats))
$where = "WHERE category IN (".implode(", ", $cats).")";
}

header("Content-Type: application/xml");

$content = "<?xml version=\"1.0\" encoding=\"windows-1251\" ?>\n<rss version=\"0.91\">\n<channel>\n" .
"<title>" . $SITENAME . "</title>\n<link>" . $DEFAULTBASEURL . "</link>\n<description>" . $DESCR . "</description>\n" .
"<language>en-usde</language>\n<copyright>Copyright © 2006 " . $SITENAME . "</copyright>\n<webMaster>" . $SITEEMAIL . "</webMaster>\n" .
"<image><title><![CDATA[" . $SITENAME . "]]></title>\n<url>" . $DEFAULTBASEURL . "/pic/favicon.ico</url>\n<link>" . $DEFAULTBASEURL . "</link>\n" .
"<width>16</width>\n<height>16</height>\n<description><![CDATA[" . $DESCR . "]]></description>\n<generator><![CDATA[7Max7 for Tesla]]></generator>\n</image>\n";


$res = sql_query("SELECT id,name,descr,size,category,(seeders+f_seeders) AS seeders,(leechers+f_leechers) AS leechers,added,image1 FROM torrents ".(!empty($where) ? $where:"")." ORDER BY added DESC LIMIT 15") or sqlerr(__FILE__, __LINE__);

///////// cache
$cache3=new MySQLCache("SELECT id,name,descr,size,category,(seeders+f_seeders) AS seeders,(leechers+f_leechers) AS leechers,added,image1 FROM torrents ".(!empty($where) ? $where:"")." ORDER BY added DESC LIMIT 15", 60*30,"rss_".md5($where).".txt"); // день
while ($row=$cache3->fetch_assoc()){
///////// cache

//while ($row = mysql_fetch_array($res)){

if (isset($_GET["feed"]) && $_GET["feed"] == "dl")
$link = "$DEFAULTBASEURL/download.php?id=$id";
else
$link = "$DEFAULTBASEURL/details.php?id=$id";


$id = $row["id"];
$name = $row["name"];
$descr = $row["descr"];
$size = $row["size"];
$cat = $row["category"];
$seeders = $row["seeders"];
$leechers = $row["leechers"];
$added = $row["added"];
$image1 = $row["image1"];


if($seeders <> 1){
$s = "их";
$aktivs="$seeders раздающий($s)";
}
else
$aktivs="нет раздающих";


if ($leechers <> 1){
$l = "ий";
$aktivl="$leechers качающих($l)";
}
else
$aktivl="нет качающих";


/*

if ($seeders >= 1 && $leechers >= 1){
$spd = sql_query("SELECT (t.size * t.times_completed + SUM(p.downloaded)) / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS totalspeed 
FROM torrents AS t 
LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND p.torrent = '$id' GROUP BY t.id ORDER BY added ASC LIMIT 15") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_assoc($spd);
$totalspeed = mksize($a["totalspeed"]) . "/сек";
}
else
$totalspeed = "нет траффика";

*/

// output of all data
$content.= "<item><title><![CDATA[" . $name . " : ".$category[$cat]."]]></title>\n<link>" . $link . "</link>\n<description><![CDATA[
".(empty($image1)? "": "
".(preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image1)  ? "<img border='0' src='$image1'/>":"<img border='0' src='thumbnail.php?image=$image1&for=details'/>")."
")." <br> \n<b>Категория</b>: " . $category[$cat] . "<br>  \n <b>Размер</b>: " . mksize($size) . "<br> \n <b>Статус</b>: " . $aktivs . " и " . $aktivl . "<br> \n <b>Добавлен</b>: " . $added . "<br> \n <b>Описание</b>:\n " . format_comment($descr) . "\n]]></description>\n</item>\n";
}
///<b>Скорость</b>: " . $totalspeed . "<br> \n 

$content.= "</channel>\n</rss>\n";

echo $content;

if ($creat_rss_mxl == true && !count($cats) && empty($passkey)){
$rsscache = ROOT_PATH."rss.xml";
/// каждые 12 часов новая запись в файл rss.xml
if (file_exists($rsscache) && filesize($rsscache) && filemtime($rsscache) > (time() - (3600*12))){
@file_put_contents($rsscache,$content);
} elseif (filesize($rsscache) == 0)
@file_put_contents($rsscache,$content);
}




?>