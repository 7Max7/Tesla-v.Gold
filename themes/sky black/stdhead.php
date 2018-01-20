<html><head>
<title><?= $title ?></title>

<link rel="stylesheet" href="./themes/<?=$ss_uri?>/style.css" type="text/css">
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

<table width="92%" border="0" align="center" cellpadding="0" cellspacing="0" class="logo">
  <tr>
    <td rowspan="3" style="border: 0px;"><a href="<?=$DEFAULTBASEURL?>"><img alt="<?=$SITENAME?>" title="<?=$SITENAME?>" src="./themes/<?=$ss_uri;?>/images/banner_logo.gif" width="294" height="129" /></a></td>
    <td rowspan="3" style="border: 0px;"><img src="./themes/<?=$ss_uri;?>/images/banner_nav_start.gif" width="138" height="129" /></td>
    <td style="border: 0px;"><img src="./themes/<?=$ss_uri;?>/images/banner_nav_top.gif" width="472" height="61" /></td>
    <td rowspan="3" style="border: 0px;"><img src="./themes/<?=$ss_uri;?>/images/banner_nav_end.gif" width="21" height="129" /></td>
    <td width="100%" style="border: 0px;">&nbsp;</td>
  </tr>
  <tr>

      <td class="topnav" align="center" style="border: 0px;"><center>
	
<? if ($CURUSER) { ?><a href="browse.php"><font color="#FFFFFF"><?=$tracker_lang['browse'];?></font></a><? } ?><? if ($CURUSER) { ?>
&nbsp;&#8226;&nbsp;<a href="detailsoff.php"><font color="#FFFFFF">Запросы</font></a>
&nbsp;&#8226;&nbsp;<a href="bookmarks.php"><font color="#FFFFFF"><?=$tracker_lang['bookmarks'];?></font></a><? } ?>
<?  if($CURUSER["class"] >= UC_VIP && $CURUSER["uploadpos"]!="no") { ?>&nbsp;&#8226;&nbsp;<a href="upload.php"><font color="#FFFFFF"><?=$tracker_lang['upload'];?></font></a><? } ?>
<?  if($CURUSER && $CURUSER["shoutbox"]=='0000-00-00 00:00:00') { ?>&nbsp;&#8226;&nbsp;<a href="tracker-chat.php"><font color="#FFFFFF">Чат</font></a><? } ?>
<?  if($CURUSER) { ?>&nbsp;&#8226;&nbsp;<a href="forums.php"><font color="#FFFFFF">Форум</font></a><? } ?>
<? if($CURUSER["class"] >= UC_MODERATOR) { ?>&nbsp;&#8226;&nbsp;<a href="log.php"><font color="#FFFFFF">Журнал</font></a><? } ?><? if ($CURUSER) { ?>&nbsp;&#8226;&nbsp;<a href="staff.php"><font color="#FFFFFF"><?=$tracker_lang['staff'];?></font></a><? } ?>
<? if($CURUSER <= UC_USER) { ?><center>
<a href="browse.php"><font color="#FFFFFF"><?=$tracker_lang['browse'];?></font></a>
&nbsp;&#8226;&nbsp;<a href="signup.php"><font color="#FFFFFF"><?=$tracker_lang['signup'];?></font></a><? } ?><? if($CURUSER <= UC_USER) { ?>&nbsp;&#8226;&nbsp;<a href="recover.php"><font color="#FFFFFF"><?=$tracker_lang['recover'];?></font></a></center><? } ?>

	</td>
		
  </tr>
  <tr>
    <td style="border: 0px;"><img src="./themes/<?=$ss_uri?>/images/banner_nav_bottom.gif" width="472" height="28" /></td>
  </tr>
</table>


<!-- /////////// here we go, with the menu //////////// -->

<table class="mainouter" width="100%" align="center" border="0" cellspacing="0" cellpadding="5">

<td valign="top" width="160" style="border: 0px;" valign="top">
<? show_blocks("l");?>	
</td>

<td align="center" valign="top" class="outer" style="padding-top: 5px; padding-bottom: 5px; border: 0px;">

<? show_blocks('b') ?>
<? show_blocks("c") ?>