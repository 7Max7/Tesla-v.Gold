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
<h1><a title="������� ��������" href="/" class="thide">&nbsp;</a></h1>

		
<div class="loginblock">
<div class="loginenter">

<h1>		
<form action="browse.php" method="get">
<input type="text" value="����� ������� ������..." style="height: 19px; width: 168px; background-color: transparent; border: none; position: relative; top: 11px; left: 50px; color:#4a728c" name="search" value="����� ������� ������..." onblur="if(this.value=='') this.value='����� ������� ������...';" onfocus="if(this.value=='����� ������� ������...') this.value='';">&nbsp;
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
echo "<a title=\"�������\" href=\"".$DEFAULTBASEURL."\">�������</a>";
echo "<a title=\"�������� / �����\" href=\"".$DEFAULTBASEURL."/browse.php\">�������� / �����</a>";

if ($CURUSER){
echo "<a title=\"�������\" href=\"".$DEFAULTBASEURL."/detailsoff.php\">�������</a>";

echo "<a title=\"".$tracker_lang['bookmarks']."\" href=\"".$DEFAULTBASEURL."/bookmarks.php\">".$tracker_lang['bookmarks']."</a>";

if ($CURUSER["class"] >= UC_VIP && $CURUSER["uploadpos"]<>"no")
echo "<a title=\"".$tracker_lang['upload']."\" href=\"".$DEFAULTBASEURL."/upload.php\">".$tracker_lang['upload']."</a>";

if ($CURUSER["shoutbox"]=='0000-00-00 00:00:00')
echo "<a title=\"���\" href=\"".$DEFAULTBASEURL."/tracker-chat.php\">���</a>";
}

echo "<a title=\"�����\" href=\"".$DEFAULTBASEURL."/forums.php\">�����</a>";

if ($CURUSER){

if($CURUSER["class"] >= UC_MODERATOR)
echo "<a title=\"������\"  href=\"".$DEFAULTBASEURL."/log.php\">������</a>";

echo "<a title=\"�������������\" href=\"".$DEFAULTBASEURL."/staff.php\">�������������</a>";
}
else
{
echo "<a title=\"�����\" href=\"".$DEFAULTBASEURL."/login.php\">�����</a>";
echo "<a title=\"".$tracker_lang['recover']."\" href=\"".$DEFAULTBASEURL."/recover.php\">".$tracker_lang['recover']."</a>";
echo "<a title=\"".$tracker_lang['signup']."\" href=\"".$DEFAULTBASEURL."/signup.php\">".$tracker_lang['signup']."</a>";
echo "<a title=\"������ ������������\" href=\"".$DEFAULTBASEURL."/support.php\">������ ������������</a>";
}
?>


</div>
</div>
		
<!-- ��������� ���������� ��� Muz-Tracker.Net by 7Max7 ��� ������ Tesla TT v.Gold 2010-->

</div></div>


<div class="wrp4">

<div class="leftColumn">


<? show_blocks('b') ?>
<? show_blocks("c") ?>
