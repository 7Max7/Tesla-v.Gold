<?
require_once 'include/bittorrent.php';

header ("Content-Type: text/html; charset=windows-1251");
dbconn(false,true);

  
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

global $CURUSER,$use_sessions;

if (empty($CURUSER)){
///@header("Location: ../index.php");
die("Гостям запрещено просматривать блок ботов.");
}


if ($use_sessions) {

$google = $yandex = $yahoo = $mailru = $msn = $rambler = $mainLink = $setLinks = $linkfeed = $aport = $turtle = $other = 0;

// выборка за последние 5 минут (60*5)
$result = sql_query("SELECT useragent FROM sessions WHERE time > ".sqlesc(get_date_time(gmtime() - 300))." AND uid='-1'") or sqlerr(__FILE__, __LINE__);

while ($row = mysql_fetch_array($result)) {

$useragent = $row["useragent"];
//$ip = $row["ip"];

if (stristr($useragent,"Googlebot"))
++$google;
elseif (stristr($useragent,"Yandex"))
++$yandex;
elseif (stristr($useragent,"yahoo"))
++$yahoo;
elseif (stristr($useragent,"msnbot") || stristr($useragent,"Bing"))
++$msn;
elseif (stristr($useragent,"Rambler"))
++$rambler;
elseif (stristr($useragent,"SetLinks"))
++$setLinks;
elseif (stristr($useragent,"IEMB3; mlbot") || stristr($useragent,"mlbot"))
++$mainLink;
elseif (stristr($useragent,"linkfeed"))
++$linkfeed;
elseif (stristr($useragent,"AportWorm"))
++$aport;
elseif (stristr($useragent,"turtle"))
++$turtle;
elseif (stristr($useragent,"Mail.Ru/"))
++$mailru;
elseif (stristr($useragent,"bot") || stristr($useragent,"CommentReader") || stristr($useragent,"feedfetcher"))
++$other;
}


echo "<table border=\"0\" width=\"100%\">\n";

//echo "<tr><td colspan=\"2\" align=\"center\" class=\"colhead\">Индексирующие<br></td></tr>\n";

if ($google)
echo "<tr><td colspan=\"2\" class=\"b\"><img width=\"16px\" src=\"http://www.google.com/favicon.ico\"> <b><font color=\"#3165E3\">G</font><font color=\"#EE2C36\">o</font><font color=\"#F5A30A\">o</font><font color=\"#2C5CD4\">g</font><font color=\"#01A315\">l</font><font color=\"#EA2533\">e</font></b>: $google</td></tr>\n";

if ($yandex)
echo "<tr><td colspan=\"2\" class=\"a\"><img width=\"16px\" src=\"http://yandex.st/lego/2.2.8/common/block/b-service-icon/_ico/b-service-icon_serp.ico\"> <b><font color=\"#FF9696\">Я</font>ндекс</b>: $yandex</td></tr>\n";

if ($rambler)
echo "<tr><td colspan=\"2\" class=\"b\"><img width=\"16px\" src=\"http://www.rambler.ru/favicon.ico?ver=3\"> <b>Rambler</b>: $rambler</td></tr>\n";

if ($yahoo)
echo "<tr><td colspan=\"2\" class=\"a\"><img width=\"16px\" src=\"http://img819.imageshack.us/img819/1926/yahoo32.png\"> <b><font color=\"#7B0099\">Yahoo</font></b>: $yahoo</td></tr>\n";

if ($msn)
echo "<tr><td colspan=\"2\" class=\"b\"><img width=\"16px\" src=\"http://hp.msn.com/global/c/hpv10/favicon.ico\"> <b><font color=\"#3C7FAF\">MSN/Live</font></b>: $msn</td></tr>\n";

if ($aport)
echo "<tr><td colspan=\"2\" class=\"a\"><img width=\"16px\" src=\"http://img707.imageshack.us/img707/4056/lightaportlogoc8050445.png\"> <b>Aport</b>: $aport</td></tr>\n";

if ($turtle)
echo "<tr><td colspan=\"2\" class=\"b\"><img width=\"16px\" src=\"http://images.turtle.ru/img/favicon.jpg\"> <b>Turtle</b>: $turtle</td></tr>\n";

if ($mailru)
echo "<tr><td colspan=\"2\" class=\"b\"><img width=\"16px\" src=\"http://img.imgsmail.ru/r/favicon.ico\"> <b><font color=\"#FFAA00\">@</font>Mail<font color=\"#FFAA00\">.ru</font></b>: $mailru</td></tr>\n";


//echo "<br><tr><td colspan=\"2\" align=\"center\" class=\"colhead\">Рекламные<br></td></tr>\n";
if ($google && $yandex && $yahoo && $mailru && $rambler && $aport)
echo "<tr><td colspan=\"2\" align=\"center\"><br></td></tr>\n";


if ($mainLink)
echo "<tr><td colspan=\"2\" class=\"a\"><a href=\"http://www.mainlink.ru/?partnerid=62510\"><img width=\"16px\" src=\"http://mainlink.ru/favicon.ico\"></a> <b>MainLink</b>: $mainLink</td></tr>\n";

if ($setLinks)
echo "<tr><td colspan=\"2\" class=\"b\"><a href=\"http://www.setlinks.ru/?pid=75461\"><img width=\"16px\" src=\"http://www.setlinks.ru/favicon.ico\"></a> <b>SetLinks</b>: $setLinks</td></tr>\n";

if ($linkfeed)
echo "<tr><td colspan=\"2\" class=\"a\"><a href=\"http://www.linkfeed.ru/70922\"><img width=\"16px\" src=\"http://www.linkfeed.ru/favicon.ico\"></a> <b>Linkfeed</b>: $linkfeed</td></tr>\n";


if ($linkfeed || $setLinks || $mainLink)
echo "<tr><td colspan=\"2\" align=\"center\"><br></td></tr>\n";

if ($other)
echo "<tr><td colspan=\"2\" class=\"b\"><b>Разное</b>: $other</td></tr>\n";


if (!$google && !$yandex && !$yahoo && !$mailru && !$msn && !$rambler && !$mainLink && !$setLinks && !$linkfeed && !$aport && !$turtle && !$other)
echo "<tr><td colspan=\"2\" class=\"b\" align=\"center\">нет данных</td></tr>\n";

echo "</tr></table>\n"; 

}
else

echo "<table border=\"0\" width=\"100%\"><tr valign=\"middle\"><td align=\"center\" class=\"embedded\">Функция сессий отключена, чтобы просматрировать данный блок активируйте ее.</td></tr></table>\n";
	





} else @header("Location: ../index.php");

?>