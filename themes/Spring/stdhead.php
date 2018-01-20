<? if (!defined('UC_SYSOP')) die('Direct access denied.'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ru">
<head>
<title><?= $title ?></title>

<link rel="stylesheet" href="./themes/<?=$ss_uri?>/style.css" type="text/css"/>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/functions.js"></script>
<script language="javascript" type="text/javascript" src="js/tooltips.js"></script>
<? meta($_GET["id"]); ?>

</head>
<body>
<? $date2 = date("m");
if ($ICE && (($date2=="12") || ($date2=="01") || ($date2=="02"))) 
print SHOW ?>


<div id="logo">
<div align="center" style="position: relative; left: 3px; top: -8px;" id="top_flash">
<embed width="400" height="130" align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowfullscreen="false" allowscriptaccess="sameDomain" name="header_ 2" bgcolor="#333333" wmode="transparent" quality="high" src="./themes/<?=$ss_uri?>/images/birds.swf">
</div>
</div>

<!-- Сриповано специально для Muz-Tracker.Net by 7Max7 для движка Tesla TT v.Gold 2010-->
<? 
echo "<div id=\"vista_toolbar\" align=\"center\">";

echo "<a href=\"".$DEFAULTBASEURL."\">Главная</a>&nbsp;";
echo "<a href=\"".$DEFAULTBASEURL."/browse.php\">Торренты / Файлы</a>&nbsp;";

if ($CURUSER){
echo "<a href=\"".$DEFAULTBASEURL."/detailsoff.php\">Запросы</a>&nbsp;";

echo "<a href=\"".$DEFAULTBASEURL."/bookmarks.php\">".$tracker_lang['bookmarks']."</a>&nbsp;";

if ($CURUSER["class"] >= UC_VIP && $CURUSER["uploadpos"]<>"no")
echo "<a href=\"".$DEFAULTBASEURL."/upload.php\">".$tracker_lang['upload']."</a>&nbsp;";

if ($CURUSER["shoutbox"]=='0000-00-00 00:00:00')
echo "<a href=\"".$DEFAULTBASEURL."/tracker-chat.php\">Чат</a>&nbsp;";
}

echo "<a href=\"".$DEFAULTBASEURL."/forums.php\">Форум</a>&nbsp;";

if ($CURUSER){

if($CURUSER["class"] >= UC_MODERATOR)
echo "<a href=\"".$DEFAULTBASEURL."/log.php\">Журнал</a>&nbsp;";

echo "<a href=\"".$DEFAULTBASEURL."/staff.php\">Администрация</a>&nbsp;";
}
else
{
echo "<a href=\"".$DEFAULTBASEURL."/login.php\">Войти</a>&nbsp;";
echo "<a href=\"".$DEFAULTBASEURL."/recover.php\">".$tracker_lang['recover']."</a>&nbsp;";
echo "<a href=\"".$DEFAULTBASEURL."/signup.php\">".$tracker_lang['signup']."</a>&nbsp;";
echo "<a href=\"".$DEFAULTBASEURL."/support.php\">Служба техподдержки</a>&nbsp;";
}

?>
</div>

<!-- поиск -->
<div style="position: absolute; top: 50px; right: 2%" >
<div style="width: 240px; height: 45px; background: transparent url('./themes/<?=$ss_uri?>/images/search_bg.png');">
<form action="browse.php" method="get">
<input type="text" value="Поиск торрент файлов..." style="height: 19px; width: 168px; background-color: transparent; border: none; position: relative; top: 11px; left: 50px; color:#4a728c" name="search" value="Поиск торрент файлов..." onblur="if(this.value=='') this.value='Поиск торрент файлов...';" onfocus="if(this.value=='Поиск торрент файлов...') this.value='';">&nbsp;
<input type="hidden" value="1" name="incldead"/>
</form>
</div>
<!-- поиск -->

<div style="color: #dbf1fe; text-shadow: 0px 1px 1px #1490d3; font-size: 9px; padding-top: 2px ">
Уважаемые пользователи, поддержите наш сайт.<br> 
Голосуйте раз в день, переходя по <a rel="nofollow" class="wl" href="http://www.toptracker.ru/details.php?id=219">этой </a> и <a rel="nofollow" class="wl" href="http://rustrackers.ru/details.php?id=33">этой</a> ссылке»
</div>

</div>

<table width="100%"  align="center" cellpadding="0" cellspasing="0" style="margin: 0px auto; min-width: 100%" id="content_">

<?
if (!empty($CURUSER) && !empty($CURUSER["unread"])){

$unread = $CURUSER["unread"];
$newmessage1 = " нов" . ($unread > 1 ? "ых" : "ое"); 
$newmessage2 = " сообщен" . ($unread > 1 ? "ий" : "ие"); 
$newmessage = "У вас $unread $newmessage1 $newmessage2"; 

echo "<table cellspacing=\"0\" cellpadding=\"10\" border=\"0\" bgcolor=\"red\">
<tr><td class=\"new_pm_block\"><b><a href=\"message.php?action=new\"><font color=\"white\">".$newmessage."</font></a></b></td></tr>
</table>";

}
?>


<tr><td id="clr">
<table width="100%" align="center" cellpadding="0" cellspasing="0">

<table>
<? 

if (empty($CURUSER))
echo "<table width=\"98%\" align=\"center\" style=\"background: transparent; border: none;\">
<td id=\"sub-left\">&nbsp;</td>
<td id=\"user_bar\">
<div align=\"center\" style=\"position: relative; top: -8px;line-height: 13px\">
<b>Добро пожаловать на торрент трекер ".$SITENAME .".</b>
<br>
<b>Самые новые фильмы, музыка, игры, софт</b>. Работают <u>три</u> <b>парсера</b>, качай - не хочу!  В подарок <b>10 Гб</b> новому пользователю.
<a href=\"signup.php\">Регистрируйтесь</a> и <a href=\"login.php\">Заходите</a>  &#150;  не пожалеете!
</div>
</td><td id=\"sub-right\">&nbsp;</td></tr>
</table>";

else echo "<br><br>";

?>

<table class="mainouter" align="center" width="100%" border="0" cellspacing="0" cellpadding="0">

<td valign="top" width="172" style="border:0;padding:0 1px 0 1px;">
<? show_blocks("l");?>	
</td>




<td align="center" valign="top" class="outer" style="border:0;padding-right:1px;">

<? show_blocks('b') ?>
<? show_blocks("c") ?>