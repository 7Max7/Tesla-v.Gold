<?
if (!defined('UC_SYSOP'))
	die('Direct access denied.');
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ru">
<head>
<title><?= $title ?></title>
<link rel="stylesheet" href="./themes/<?=$ss_uri;?>/style.css" type="text/css">

<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/swfobject.js"></script> 
<script language="javascript" type="text/javascript" src="js/functions.js"></script>
<script language="javascript" type="text/javascript" src="js/glossy.js"></script>
<script language="javascript" type="text/javascript" src="js/tooltips.js"></script>
<? meta($_GET["id"]); ?>
</head>


<body>
<?

$date2 = date("m");
if ($ICE && (($date2=="12") || ($date2=="01") || ($date2=="02"))) 
print SHOW ?>
<!-- START NEW YEAR 2009 Theme - Design by © WSMYN -->

<table style="background:transparent; border:none;" width="100%" border="0" cellspacing="0" cellpadding="0" align="center"><tr><td class="brd" width="11" height="49"><img src="./themes/<?=$ss_uri;?>/images/table1_left.png" width="11" height="49"></td>

<td class="brd" background="./themes/<?=$ss_uri;?>/images/table1_center1.png">

<div align="center" class="welcome">

<a href="<?=$DEFAULTBASEURL;?>"><img src="./themes/<?=$ss_uri;?>/images/baner_top.png" alt="<?=$SITENAME?>" width="378" height="50" border="0" /></a>
</div>
</td>



<td class="brd" width="11" height="49"><img src="./themes/<?=$ss_uri;?>/images/table1_right.png" width="11" height="49"></td>
</tr><tr><td class="brd" background="./themes/<?=$ss_uri;?>/images/table1_1.png">&nbsp;</td><td class="brd" background="./themes/<?=$ss_uri;?>/images/table1_center1.png">
    
<table style="background:transparent; border:none;" width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="brd" width="20" height="22"><img src="./themes/<?=$ss_uri;?>/images/table2_01.png" width="20" height="22"></td><td class="brd" background="./themes/<?=$ss_uri;?>/images/table2_02.gif"><img src="./themes/<?=$ss_uri;?>/images/spacer.gif" width="1" height="22"></td><td class="brd" width="20" height="22"><img src="./themes/<?=$ss_uri;?>/images/table2_03.png" width="20" height="22"></td></tr><tr><td class="brd" background="./themes/<?=$ss_uri;?>/images/table2_04.png">&nbsp;</td><td class="brd" bgcolor="#f0f2f4" valign="top">

<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>




    <td class="brd" valign="top">
    
   



<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="menu_l">&nbsp;</td>
<td align="center" class="menu_c">&nbsp;

<a href="<?=$DEFAULTBASEURL;?>/">
<strong><font color="#FFFFFF"><?=$tracker_lang['homepage'];?></font></strong></a>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="browse.php"><strong><font color="#FFFFFF"><?=$tracker_lang['browse'];?></strong></font></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="bookmarks.php"><strong><font color="#FFFFFF"><?=$tracker_lang['bookmarks'];?></strong></font></a>
<? } ?>
<?  if($CURUSER["class"] >= UC_VIP && $CURUSER["uploadpos"]!="no") { ?>
&nbsp;&#8225;&nbsp;
<a href="upload.php"><strong><font color="#FFFFFF"><?=$tracker_lang['upload'];?></strong></font></a>
<? } ?>

<?  if($CURUSER && $CURUSER["shoutbox"]=='0000-00-00 00:00:00') { ?>
&nbsp;&#8225;&nbsp;
<a href="tracker-chat.php"><strong><font color="#FFFFFF">Чат</strong></font></a>
<? } ?>

<?  if($CURUSER["class"] >= UC_USER) { ?>
&nbsp;&#8225;&nbsp;
<a href="forums.php"><strong><font color="#FFFFFF">Форум</strong></font></a>
<? } ?>

<?  if($CURUSER["class"] >= UC_MODERATOR) { ?>
&nbsp;&#8225;&nbsp;
<a href="log.php"><strong><font color="#FFFFFF">Журнал</font></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="rules.php"><strong><font color="#FFFFFF"><?=$tracker_lang['rules'];?></strong></font></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="faq.php"><strong><font color="#FFFFFF"><?=$tracker_lang['faq'];?></strong></font></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="staff.php"><strong><font color="#FFFFFF"><?=$tracker_lang['staff'];?></font></strong></a>
<? } ?>

<!-------------  для не вошедших в систему ------------------->
<? if($CURUSER <= UC_USER) { ?>
&nbsp;&#8225;&nbsp;
<a href="signup.php"><strong><font color="#FFFFFF"><?=$tracker_lang['signup'];?></font></strong></a>
<? } ?>

<? if($CURUSER <= UC_USER) { ?>
&nbsp;&#8225;&nbsp;
<a href="recover.php"><strong><font color="#FFFFFF"><?=$tracker_lang['recover'];?></font></strong></a>
<? } ?>
<!-------------  для не вошедших в систему ------------------->

</td>
<td class="menu_r"></td>
</tr>
</table>


</td></tr><tr>
<td valign="top" class="brd">

    <table width="100%" border="0" cellspacing="0" cellpadding="0">

	<tr>
	<br>
<td width="170" valign="top" class="brd">
<? show_blocks("l"); ?>
</td>


<td align="center" class="brd" valign="top" style="padding-left:5px; padding-right:5px;">
<? show_blocks('b') ?>
<? show_blocks('c') ?>