<?
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

?>

<script>
function bots_online() {
jQuery.post("block-bot_online_jquery.php",{}, function(response) {
		jQuery("#bots_online").html(response);
	}, "html");
setTimeout("bots_online();", 60000);
}
bots_online();
</script>
<?

$content = "<span align=\"center\" id=\"bots_online\">Загрузка списка ботов</span>";

$blocktitle="Бот система";
/////////////////////


/*
global $CURUSER,$use_sessions;

if (empty($CURUSER)){
///@header("Location: ../index.php");
die("Гостям запрещено просматривать блок ботов.");
}

$blocktitle="Индексирующие боты";

if ($use_sessions) {

$google = $yandex = $yahoo = $msn = $rambler = $mainLink = $setLinks = $linkfeed = $aport = $turtle = 0;

$dt = sqlesc(time() - 180); //180

$result = sql_query("SELECT ip,useragent FROM sessions WHERE time > DATE_SUB(NOW(), INTERVAL 180 SECOND) AND uid='-1'") or sqlerr(__FILE__, __LINE__);

while ($row = mysql_fetch_array($result)) {

$useragent = $row["useragent"];
$ip = $row["ip"];

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
elseif (stristr($useragent,"IEMB3; mlbot"))
++$mainLink;
elseif (stristr($useragent,"linkfeed"))
++$linkfeed;
elseif (stristr($useragent,"linkfeed"))
++$aport;
elseif (stristr($useragent,"turtle"))
++$turtle;
}

$content.="<table border=\"0\" width=\"100%\">\n";

//if ($google)
$content.= "<tr><td class=\"embedded\"><img width=\"16px\" src=\"http://www.google.com/favicon.ico\"></td><td width=\"100%\" class=\"embedded\"><b><font color=\"#3165E3\">G</font><font color=\"#EE2C36\">o</font><font color=\"#F5A30A\">o</font><font color=\"#2C5CD4\">g</font><font color=\"#01A315\">l</font><font color=\"#EA2533\">e</font></b>: $google</td></tr>\n";

//if ($yandex)
$content.= "<tr><td class=\"embedded\"><img width=\"16px\" src=\"http://yandex.st/lego/2.2.8/common/block/b-service-icon/_ico/b-service-icon_serp.ico\"></td><td width=\"100%\" class=\"embedded\">
<b><font color=\"#FF9696\">Я</font>ндекс</b>: $yandex</td></tr>\n";

//if ($rambler)
$content.= "<tr><td class=\"embedded\"><img width=\"16px\" src=\"http://www.rambler.ru/favicon.ico?ver=3\"></td><td width=\"100%\" class=\"embedded\"><b>Rambler</b>: $rambler</td></tr>\n";

//if ($yahoo)
$content.= "<tr><td class=\"embedded\"><img width=\"16px\" src=\"http://img819.imageshack.us/img819/1926/yahoo32.png\"></td><td width=\"100%\" class=\"embedded\"><b><font color=\"#7B0099\">Yahoo</font></b>: $yahoo</td></tr>\n";

//if ($msn)
$content.= "<tr><td class=\"embedded\"><img width=\"16px\" src=\"http://hp.msn.com/global/c/hpv10/favicon.ico\"></td><td width=\"100%\" class=\"embedded\"><b><font color=\"#3C7FAF\">MSN/Live</font></b>: $msn</td></tr>\n";

//if ($aport)
$content.= "<tr><td class=\"embedded\"><a href=\"http://www.linkfeed.ru/70922\"><img width=\"16px\" src=\"http://img707.imageshack.us/img707/4056/lightaportlogoc8050445.png\"></a></td><td width=\"100%\" class=\"embedded\"><b>Aport</b>: $aport</td></tr>\n";

if ($turtle)
$content.= "<tr><td class=\"embedded\"><a href=\"http://www.linkfeed.ru/70922\"><img width=\"16px\" src=\"http://images.turtle.ru/img/favicon.jpg\"></a></td><td width=\"100%\" class=\"embedded\"><b>Turtle</b>: $turtle</td></tr>\n";


$content.= "<tr><td colspan=\"2\" class=\"embedded\"><br></td></tr>\n";


//if ($mainLink)
$content.= "<tr><td class=\"embedded\"><a href=\"http://www.mainlink.ru/?partnerid=62510\"><img width=\"16px\" src=\"http://mainlink.ru/favicon.ico\"></a></td><td width=\"100%\" class=\"embedded\"><b>MainLink</b>: $mainLink</td></tr>\n";

//if ($setLinks)
$content.= "<tr><td class=\"embedded\"><a href=\"http://www.setlinks.ru/?pid=75461\"><img width=\"16px\" src=\"http://www.setlinks.ru/favicon.ico\"></a></td><td width=\"100%\" class=\"embedded\"><b>SetLinks</b>: $setLinks</td></tr>\n";

//if (linkfeed)
$content.= "<tr><td class=\"embedded\"><a href=\"http://www.linkfeed.ru/70922\"><img width=\"16px\" src=\"http://www.linkfeed.ru/favicon.ico\"></a></td><td width=\"100%\" class=\"embedded\"><b>Linkfeed</b>: $linkfeed</td></tr>\n";

$content.= "</tr></table>\n"; 

}
else

$content.= "<table border=\"0\" width=\"100%\"><tr valign=\"middle\"><td align=\"center\" class=\"embedded\">Функция сессий отключена, чтобы просматрировать данный блок активируйте ее.</td></tr></table>\n";
*/


?>