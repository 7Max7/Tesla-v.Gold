<?
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

global $CURUSER,$tracker_lang;

$blocktitle = $tracker_lang['news'].(get_user_class() >= UC_ADMINISTRATOR ? " - [<a class=\"altlink_white\" href=\"news.php\"><b>".$tracker_lang['create']."</b></a>]" : "")."
".($CURUSER ? " - [<a class=\"altlink_white\" href=\"newsarchive.php\"><b>Архив Новостей</b></a>]":"");


if (!$CURUSER){
  $cacheStatFile = "cache/block-news_guest.txt"; 
     $expire = 180*60; // 180  minutes 
} elseif ($CURUSER && get_user_class() < UC_ADMINISTRATOR){
  $cacheStatFile = "cache/block-news_users.txt"; 
   $expire=60*60; // 60 minutes 
} else {
 $cacheStatFile = "cache/block-news_admins.txt"; 
 $expire=30*60; // 30 minutes 
}


if (file_exists($cacheStatFile) && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{


$resource = sql_query("SELECT *, (SELECT COUNT(*) FROM comments WHERE news = news.id) AS comme FROM news ORDER BY added DESC LIMIT 3") or sqlerr(__FILE__, __LINE__);


if (mysql_num_rows($resource)) {

    $content .= "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\">";
    $show=true;
    while($array = mysql_fetch_array($resource)) {

   list ($data,$time) = explode(" ", $array["added"]);


if ($array['added']>get_date_time(gmtime() - 86400*2) && $show==true)// 2 дня
{
$content .=format_comment($array['body']);
$show=false;
}
else
{
$content .="<div class=\"spoiler-wrap\" id=\"".$array["id"]."\"><div class=\"spoiler-head folded clickable\">".$data.": ".$array['subject']."</div><div class=\"spoiler-body\" style=\"display: none;\">".format_comment($array['body'])." </div></div>";
}


   if ($CURUSER){
    $content .="<div align=\"center\"><b>Комментариев</b>: ".$array["comme"]."</div><div align=\"right\">".(get_user_class() >= UC_ADMINISTRATOR ? "<b>[</b><a href=\"news.php?action=edit&newsid=".$array['id']."&returnto=".htmlentities($_SERVER['PHP_SELF'])."\">Редактировать</a><b>]</b> - <b>[</b><a href=\"news.php?action=delete&newsid=".$array['id']."&returnto=".htmlentities($_SERVER['PHP_SELF'])."\">Удалить</a><b>]</b> - ":"")."
	 <b>[</b><a href=\"newsoverview.php?id=".$array['id']."\">Комментировать</a><b>]</b></div>";
    }
	else 
	$content .="<br>";

	}

	$content .= "</table>\n";
} else {
	$content .= "<table class=\"main\" align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\"><tr><td>";
	$content .= "<div align=\"center\"><h3>".$tracker_lang['no_news']."</h3></div>\n";
	$content .= "</td></tr></table>";
}



       $fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
 
if (get_user_class() >= UC_SYSOP) {
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
}

?>