<?

/**  Внимание, самая популярная сборка движка теперь доступна в бесплатном скачивании (версия gold за 2010 год)!!!!
* Это старая версия проекта Tesla - торрент сайта. Демо новой версии можно посмотреть по ссылке demo.muz-tracker.net
* Если желаете использовать движок, то оставьте пожалуйста копирайта на своих местах, иначе стабильной работы не гарантируется.
* Если желаете поискать и установить новые моды, советуем воспользоваться форумами: bit-tracker.info, bit-torrent.kiev.ua.
* Если желаете поблагодарить владельца 7Max7 за эту сборку, киньте пару монеток на R190680055855 или Z187703352284
* Если желаете обновить Gold версию до Platinum - придется заплатить около 70$ (с последующими бесплатными обновлениями)
* 
* Данная копия движка взята с сайта demo muz-tracker net и tesla-tracker.net! ))
* Спасибо за внимание к движку Tesla.
**/


if(!defined('IN_TRACKER') && !defined('IN_ANNOUNCE'))
  die("Hacking attempt init!");

if (!function_exists("htmlspecialchars_uni")) {
	function htmlspecialchars_uni($message) {
		
		$message = @stripslashes($message);
	///	$message = preg_replace("#&(?!\#[0-9]+;)#si", "", $message); // Fix & but allow unicode

     	$search = array("'<script[^>]*?>.*?</script>'si","'&(quot|#34|#034|#x22);'i","'&(amp|#38|#038|#x26);'i");
    	$replace = array("","\""," ");
	    $message = preg_replace($search,$replace,$message);
	    
	    $sear = array("&quot;","&amp;","-gt;","&nbsp; ");
	    $message = str_replace($sear, "", $message);
	    
		return $message;
	}
}

// Константы
define ('TIMENOW', time());

$url = explode('/', htmlspecialchars_uni($_SERVER['PHP_SELF']));  
array_pop($url);
$DEFAULTBASEURL = (($_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://").htmlspecialchars_uni($_SERVER['HTTP_HOST']).implode('/', $url);
$BASEURL = $DEFAULTBASEURL;

$announce_urls = array();

if (basename($_SERVER['SCRIPT_FILENAME'])=="download.php" || basename($_SERVER['SCRIPT_FILENAME'])=="details.php"){
$announce_urls[] = "$DEFAULTBASEURL/announce.php";
$announce_urls[] = "http://tracker.openbittorrent.com:80/announce";
}

//$announce_urls[] = "http://tracker.publicbt.com:80/announce";
//$announce_urls[] = "http://announce.freeexchange.ru:2710/announce";
//$announce_urls[] = "http://announce.opensharing.ru:2710/announce";

//$announce_urls[] = "udp://tracker.publicbt.com:80/announce";


//$announce_urls[] = "http://bigtorrent.org:2710/announce";
//$announce_urls[] = "http://tr1.kinozal.tv/announce.php?passkey=74e76ef68365aa56f7899461d59a7907";

//if (basename($_SERVER['SCRIPT_FILENAME'])=="parser_org.ua.php")
//$announce_urls[] = "http://freetorrents.org.ua:2710/announce";
$announce_urls[] = "http://bt.rutor.org:2710/announce";
//$announce_urls[] = "http://tracker.torrentbay.to:6969/announce";
//$announce_urls[] = "http://tracker.torrentbox.com:2710/announce";
//$announce_urls[] = "http://tracker.anime-miako.to:6969/announce";
//$announce_urls[] = "http://tracker.torrent.to:2710/announce";
//$announce_urls[] = "http://rt.beeretracker.net/announce.php";
//$announce_urls[] = "http://ix3.rutracker.net/ann?uk=w5OiLN0kEB";
//$announce_urls[] = "http://bt.pornolab.net/ann?uk=Eca1OVnhKD";


/*
http://freetorrents.org.ua:2710/announce
http://bt.rutor.org:2710/announce
http://denis.stalker.h3q.com:6969/announce
http://announce.opensharing.org:2710/announce
http://rt.beeretracker.net/announce.php
http://tracker.prq.to/announce
http://tracker.openbittorrent.com/announce
http://tracker.ilibr.org:80/announce
http://tracker.publicbt.com:80/announce
http://tracker.openbittorrent.com:80/announce
http://the.illusionist.tracker.prq.to:80/announce
http://bt1.the9.com:6969/announce
http://tv.tracker.prq.to/announce
http://tracker.torrent.to:2710/announce
http://tracker.openbittorrent.com/announce
http://tracker.openbittorrent.com:80/announce
http://tracker.publicbt.com:80/announce
http://tracker.ilibr.org:6969/announce
http://exodus.desync.com:6969/announce
http://trackertdt.com/announce.php
http://tracker.mightynova.com:4315/announce
http://tracker.torrentbay.to:6969/announce
http://opentracker.fr333dom.com:6666/announce
http://tracker.mightynova.com/announce
http://sombarato.org:6969/announce
http://movie-seedbox.info:6969/announce
http://retracker.local/announce
*/


// DEFINE Трекер Группы
define ("UC_USER", 0);
define ("UC_POWER_USER", 1);
define ("UC_VIP", 2);
define ("UC_UPLOADER", 3);
define ("UC_MODERATOR", 4);
define ("UC_ADMINISTRATOR", 5);
define ("UC_SYSOP", 6);

?>