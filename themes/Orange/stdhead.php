<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ru">
<head>
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
<table width="100%" class="clear" align="center" border="0" cellspacing="0" cellpadding="0" style="background: transparent;">


<td  class="embedded" width="100%" background="./themes/<?=$ss_uri;?>/images/logobg.png">
 <center>  
<a href="<?=$DEFAULTBASEURL?>"><img style="border: none" alt="<?=$SITENAME?>" title="<?=$SITENAME?>" src="./themes/<?=$ss_uri;?>/images/logo.png" /></a></center>
</td>

</td>

</table>

<!-- Top Navigation Menu for unregistered-->
<table class=clear width="100%" align="center" border="0" cellspacing="0" cellpadding="2"><tr>
<td align="center" class="clear" width="80%" height="36px" style="margin: 0; border: 8; background:url(themes/<?=$ss_uri;?>/images/menu.png);">&nbsp;


<a href="<?=$DEFAULTBASEURL;?>/">
<strong><font color="#000000"><?=$tracker_lang['homepage'];?></font></strong></a>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="browse.php"><strong><font color="#000000"><?=$tracker_lang['browse'];?></strong></font></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="bookmarks.php"><strong><font color="#000000"><?=$tracker_lang['bookmarks'];?></strong></font></a>
<? } ?>
<?  if($CURUSER["class"] >= UC_VIP && $CURUSER["uploadpos"]!="no") { ?>
&nbsp;&#8225;&nbsp;
<a href="upload.php"><strong><font color="#000000"><?=$tracker_lang['upload'];?></strong></font></a>
<? } ?>

<?  if($CURUSER && $CURUSER["shoutbox"]=='0000-00-00 00:00:00') { ?>
&nbsp;&#8225;&nbsp;
<a href="tracker-chat.php"><strong><font color="#000000">Чат</strong></font></a>
<? } ?>
<?  if($CURUSER["class"] >= UC_USER) { ?>
&nbsp;&#8225;&nbsp;
<a href="forums.php"><strong><font color="#000000">Форум</strong></font></a>
<? } ?>
<?  if($CURUSER["class"] >= UC_MODERATOR) { ?>
&nbsp;&#8225;&nbsp;
<a href="log.php"><strong><font color="#000000">Журнал</font></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="rules.php"><strong><font color="#000000"><?=$tracker_lang['rules'];?></strong></font></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="faq.php"><strong><font color="#000000"><?=$tracker_lang['faq'];?></strong></font></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="staff.php"><strong><font color="#000000"><?=$tracker_lang['staff'];?></font></strong></a>
<? } ?>

<!-------------  для не вошедших в систему ------------------->
<? if($CURUSER <= UC_USER) { ?>
&nbsp;&#8225;&nbsp;
<a href="signup.php"><strong><font color="#000000"><?=$tracker_lang['signup'];?></font></strong></a>
<? } ?>

<? if($CURUSER <= UC_USER) { ?>
&nbsp;&#8225;&nbsp;
<a href="recover.php"><strong><font color="#000000"><?=$tracker_lang['recover'];?></font></strong></a>
<? } ?>
<!-------------  для не вошедших в систему ------------------->

</td></tr>
</table>


<?

$w = "width=\"100%\"";
//if ($_SERVER["REMOTE_ADDR"] == $_SERVER["SERVER_ADDR"]) $w = "width=984";

?>
<table class="mainouter" align="center" <?=$w; ?> border="1" cellspacing="0" cellpadding="5">

<!------------- MENU ------------------------------------------------------------------------>

<? $fn = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1); ?>

<td valign="top" width="155">
<? show_blocks("l");?>

</td>

<td align="center" valign="top" class="outer" style="padding-top: 5px; padding-bottom: 5px">
<? show_blocks('b');?>
<? show_blocks('c');?>