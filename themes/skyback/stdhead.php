<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<? if (!defined('UC_SYSOP')) die(); ?>
<head>
<title><?= $title ?></title>

<link rel="stylesheet" href="./themes/<?=$ss_uri?>/style.css" type="text/css"/>

<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/swfobject.js"></script> 
<script language="javascript" type="text/javascript" src="js/functions.js"></script>
<script language="javascript" type="text/javascript" src="js/glossy.js"></script>
<script language="javascript" type="text/javascript" src="js/tooltips.js"></script>

<? meta($_GET["id"]); ?>
</head>
<body>

<? $date2 = date("m");
if ($ICE && (($date2=="12") || ($date2=="01") || ($date2=="02"))) 
print SHOW; ?>


<div class="pagebg">
<div class="wrapper">

<div class="header">
<h1><a title="Главная страница" href="/" class="thide">&nbsp;</a></h1>

		
<div class="loginblock">
<div class="loginenter">

<h1>		
<form action="browse.php" method="get">
<input type="text" value="Поиск торрент файлов..." style="height: 19px; width: 168px; background-color: transparent; border: none; position: relative; top: 11px; left: 50px; color:#4a728c" name="search" value="Поиск торрент файлов..." onblur="if(this.value=='') this.value='Поиск торрент файлов...';" onfocus="if(this.value=='Поиск торрент файлов...') this.value='';">&nbsp;
<input type="hidden" value="1" name="incldead"/>
</form>
</h1>

<h3><?=$title?></h3>
</div>
</div>
</div>

<div class="topmenu"><div><div>

<div id="menuhead">


<?
echo "<a title=\"Главная\" href=\"".$DEFAULTBASEURL."\">Главная</a>";
echo "<a title=\"Торренты / Файлы\" href=\"".$DEFAULTBASEURL."/browse.php\">Торренты / Файлы</a>";

if ($CURUSER){
echo "<a title=\"Запросы\" href=\"".$DEFAULTBASEURL."/detailsoff.php\">Запросы</a>";

echo "<a title=\"".$tracker_lang['bookmarks']."\" href=\"".$DEFAULTBASEURL."/bookmarks.php\">".$tracker_lang['bookmarks']."</a>";

if ($CURUSER["class"] >= UC_VIP && $CURUSER["uploadpos"]<>"no")
echo "<a title=\"".$tracker_lang['upload']."\" href=\"".$DEFAULTBASEURL."/upload.php\">".$tracker_lang['upload']."</a>";

if ($CURUSER["shoutbox"]=='0000-00-00 00:00:00')
echo "<a title=\"Чат\" href=\"".$DEFAULTBASEURL."/tracker-chat.php\">Чат</a>";
}

echo "<a title=\"Форум\" href=\"".$DEFAULTBASEURL."/forums.php\">Форум</a>";

if ($CURUSER){

if($CURUSER["class"] >= UC_MODERATOR)
echo "<a title=\"Журнал\"  href=\"".$DEFAULTBASEURL."/log.php\">Журнал</a>";

echo "<a title=\"Администрация\" href=\"".$DEFAULTBASEURL."/staff.php\">Администрация</a>";
}
else
{
echo "<a title=\"Войти\" href=\"".$DEFAULTBASEURL."/login.php\">Войти</a>";
echo "<a title=\"".$tracker_lang['recover']."\" href=\"".$DEFAULTBASEURL."/recover.php\">".$tracker_lang['recover']."</a>";
echo "<a title=\"".$tracker_lang['signup']."\" href=\"".$DEFAULTBASEURL."/signup.php\">".$tracker_lang['signup']."</a>";
echo "<a title=\"Служба техподдержки\" href=\"".$DEFAULTBASEURL."/support.php\">Служба техподдержки</a>";
}
?>


</div>
</div>
		
<!-- Сриповано специально для Muz-Tracker.Net by 7Max7 для движка Tesla TT v.Gold 2010-->

</div></div>


<div class="wrp4">

<div class="leftColumn">


<? show_blocks('b') ?>
<? show_blocks("c") ?>
