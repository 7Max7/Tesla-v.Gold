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
<?

$date2 = date("m");
if ($ICE && (($date2=="12") || ($date2=="01") || ($date2=="02"))) 
print SHOW ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="128"><img src="themes/<?=$ss_uri?>/images/header_01.gif" width="128" height="12"></td>
    <td background="themes/<?=$ss_uri?>/images/header_02.gif"><img src="themes/<?=$ss_uri?>/images/header_02.gif" width="3" height="12"></td>
    <td width="171"><img src="themes/<?=$ss_uri?>/images/header_04.gif" width="171" height="12"></td>
    <td background="themes/<?=$ss_uri?>/images/header_02.gif"><img src="themes/<?=$ss_uri?>/images/header_02.gif" width="3" height="12"></td>
    <td width="126"><img src="themes/<?=$ss_uri?>/images/header_06.gif" width="126" height="12"></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td  valign="left" width="128"><img src="themes/<?=$ss_uri?>/images/header_08.gif" width="128" height="75"></td>
    <td background="themes/<?=$ss_uri?>/images/header_09.gif" width="30%"></td>
    <td valign="center" background="themes/<?=$ss_uri?>/images/header_09.gif" width="300"><img src="themes/<?=$ss_uri?>/images/logo_11.gif" width="268" height="75"></td>
    <td valign="top" background="themes/<?=$ss_uri?>/images/header_09.gif" width="30%"></td>
    <td width="126"><img src="themes/<?=$ss_uri?>/images/header_11.gif" width="126" height="75"></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="117"><img src="themes/<?=$ss_uri?>/images/header4_13.gif" width="117" height="32"></td>
    <td background="themes/<?=$ss_uri?>/images/header_16.gif">&nbsp;</td>
    <td width="119"><img src="themes/<?=$ss_uri?>/images/header_17.gif" width="119" height="32"></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="31">
  <tr>
   


    <td background="themes/<?=$ss_uri?>/images/header_212.gif">&nbsp;</td>
    <td align="left" width="21"><img src="themes/<?=$ss_uri?>/images/header_21.gif" width="21" height="31"></td>
    <td background="themes/<?=$ss_uri?>/images/header_23.gif" width="100%">
<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0"><tr>

<td align="center" class="topnav">&nbsp;<a href="<?=$DEFAULTBASEURL;?>/">
<strong><font color="#FFFFFF"><?=$tracker_lang['homepage'];?></font></strong></a>
<? if ($CURUSER) { ?>
&nbsp;&#8226;&nbsp;
<a href="browse.php"><strong><font color="#FFFFFF"><?=$tracker_lang['browse'];?></strong></font></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8226;&nbsp;
<a href="bookmarks.php"><strong><font color="#FFFFFF"><?=$tracker_lang['bookmarks'];?></strong></font></a>
<? } ?>


<?  if($CURUSER["class"] >= UC_VIP && $CURUSER["uploadpos"]!="no") { ?>
&nbsp;&#8226;&nbsp;
<a href="upload.php"><strong><font color="#FFFFFF"><?=$tracker_lang['upload'];?></strong></font></a>
<? } ?>


<?  if($CURUSER && $CURUSER["shoutbox"]=='0000-00-00 00:00:00') { ?>
&nbsp;&#8226;&nbsp;
<a href="tracker-chat.php"><strong><font color="#FFFFFF">Чат</strong></font></a>
<? } ?>

<?  if($CURUSER) { ?>
&nbsp;&#8226;&nbsp;
<a href="forums.php"><strong><font color="#FFFFFF">Форум</strong></font></a>
<? } ?>


<?  if($CURUSER["class"] >= UC_MODERATOR) { ?>
&nbsp;&#8226;&nbsp;
<a href="log.php"><strong><font color="#FFFFFF">Журнал</font></a>
<? } ?>

<? if ($CURUSER) { ?>
&nbsp;&#8226;&nbsp;
<a href="rules.php"><strong><font color="#FFFFFF"><?=$tracker_lang['rules'];?></strong></font></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8226;&nbsp;

<a href="faq.php"><strong><font color="#FFFFFF"><?=$tracker_lang['faq'];?></strong></font></a>
<? } ?>

<? if ($CURUSER) { ?>

&nbsp;&#8226;&nbsp;
<a href="staff.php"><strong><font color="#FFFFFF"><?=$tracker_lang['staff'];?></font></strong></a>
<? } ?>

<!-------------  для не вошедших в систему ------------------->
<? if($CURUSER <= UC_USER) { ?>
&nbsp;&#8226;&nbsp;
<a href="signup.php"><strong><font color="#FFFFFF"><?=$tracker_lang['signup'];?></font></strong></a>
<? } ?>

<? if($CURUSER <= UC_USER) { ?>
&nbsp;&#8226;&nbsp;
<a href="recover.php"><strong><font color="#FFFFFF"><?=$tracker_lang['recover'];?></font></strong></a>
<? } ?>
<!-------------  для не вошедших в систему ------------------->

</td></tr>
</table>
     </td>
    <td width="18"><img src="themes/<?=$ss_uri?>/images/header_26.gif" width="18" height="31"></td>
	<td background="themes/<?=$ss_uri?>/images/header_212.gif">&nbsp;</td>

	
  </tr>
</table>

<table width="100%"  cellpadding="0" cellspacing="0" border="0" align="center">


<!------------- ///7Max7 for multsity.com: icq - 225454228 ------------------->


<td align="center" valign="top" class="outer" style="padding-top: 5px; padding-bottom: 5px">

<?
	show_blocks('b');
?>
          
<table width="99%" border="0" cellspacing="0" cellpadding="0"align="center">
  
   
<td style ="background-color: #0F0F0F; valign="top" align="center"">
<table width="99%" border="0" align="center" cellpadding="0" cellspacing="0"></td>


<!-------------  7/08/2008 7Max7 for multsity.com: icq - 225454228 ------------------->
<td width="80%" class="story2" valign="top">

</table>
</td></table>
<? show_blocks('c');?>