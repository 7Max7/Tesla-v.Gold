<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ru">
<head>
<title><?= $title ?></title>

<link rel="stylesheet" href="./themes/<?=$ss_uri;?>/style.css" type="text/css"/>
<link rel="stylesheet" type="text/css" href="./themes/<?=$ss_uri?>/dropdown/dropdown.css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/functions.js"></script>
<script language="javascript" type="text/javascript" src="js/tooltips.js"></script>
<script language="javascript" type="text/javascript" src="./themes/<?=$ss_uri?>/dropdown/js/dropdown.js">
</script>
<? meta($_GET["id"]); ?>
</head>
<body>
<?

$date2 = date("m");
if ($ICE && (($date2=="12") || ($date2=="01") || ($date2=="02"))) 
print SHOW ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="5" rowspan="3" background="./themes/<?=$ss_uri;?>/images/left.gif"><img src="./themes/<?=$ss_uri;?>/images/spacer.gif" width="5" height="1"></td>
    <td valign="top">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="12"><img src="./themes/<?=$ss_uri;?>/images/top_l.gif" width="12" height="41"></td>
        <td background="./themes/<?=$ss_uri;?>/images/top_c.gif" align="center"><div class="topnav">
        
<!-- наше меню -->
<table width="100%" border="0" cellspacing="0" cellpadding="2"><tr>
<div class="chromestyle" id="chromemenu">
<ul>
 
<li><a href="index.php"><font color="#FFFFFF">&nbsp;&curren;&nbsp; Главная</font></a></li>

<? if (get_user_class() >= UC_USER) { ?><li>
<a href="forums.php"><font color="#FFFFFF">&nbsp;&curren;&nbsp; Форум</font></a></li> <? } ?>

<? if ($CURUSER && $CURUSER["shoutbox"]=='0000-00-00 00:00:00') { ?><li>
<a href="tracker-chat.php"><font color="#FFFFFF">&nbsp;&curren;&nbsp;  Чат</font></a></li> <? } ?>


<? if($CURUSER <= UC_USER) { ?>
<li>&nbsp;&curren;&nbsp;<a href="signup.php"><font color="#FFFFFF"><?=$tracker_lang['signup'];?></font></a>
&nbsp;&curren;&nbsp;<a href="recover.php"><font color="#FFFFFF"><?=$tracker_lang['recover'];?></font></a></li>
<? } ?>

<? if($CURUSER) { ?>
<li><a href="#" rel="dropmenu1"><font color="#FFFFFF"> &nbsp;&curren;&nbsp; Торренты</font></a></li>
<? } ?>
<? if (get_user_class() >= UC_MODERATOR) { ?>
<li><a  href="#" rel="dropmenu2"><font color="#FFFFFF">&nbsp;&curren;&nbsp; Журнал</font></a></li><? } ?>

 
<? if (get_user_class() >= UC_POWER_USER) { ?><li>
<a href="staff.php"><font color="#FFFFFF">&nbsp;&curren;&nbsp; Администрация</font></a></li> <? } ?>

</ul>
</div>

<div id="dropmenu1" class="dropmenudiv">
<? if (get_user_class() >= UC_VIP) { ?> <a href="upload.php">Залить файл торрент</a> <? } ?>

<a href="browse.php">Список Торрентов</a>
<a href="viewrequests.php">Запросы</a>
<a href="viewoffers.php">Предложений</a>

<a href="browse.php?search=&incldead=2&cat=0">Мертвые</a>

<a href="browse.php?search=&incldead=3&cat=0">Золотые</a>
<a href="browse.php?search=&incldead=4&cat=0">Без Сидов</a>
</div>

<div id="dropmenu2" class="dropmenudiv">
<a href="log.php?type=tracker">Трекер</a>
<a href="log.php?type=release">Релизы</a>
<a href="log.php?type=torrent">Торренты</a>

<? if (get_user_class() >= UC_SYSOP) { ?> <a href="log.php?type=error">Ошибки</a> <? } ?>
<? if (get_user_class() >= UC_SYSOP) { ?> <a href="log.php?type=bans">Баны</a> <? } ?>

</div>


<script type="text/javascript">

cssdropdown.startchrome("chromemenu")

</script></tr>
</table>


<td width="12"><img src="./themes/<?=$ss_uri;?>/images/top_r.gif" width="12" height="41"></td>
</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr> 
   
   

   
  </tr>
  
</table>
 </td>
    <td width="5" rowspan="3" background="./themes/<?=$ss_uri;?>/images/right.gif"><img src="./themes/<?=$ss_uri;?>/images/spacer.gif" width="5" height="1"></td>
  </tr>
  <tr>
    <td valign="top">


<!-- /////////// here we go, with the menu //////////// -->

<table width="100%" border="0" cellspacing="0" cellpadding="5">
<tr>

<td valign="top" style="padding-top: 5px; padding-bottom: 5px" align="center">
<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
    <td width="16"><img src="./themes/<?=$ss_uri;?>/images/n_h_l.gif" width="16" height="14"></td>
    <td background="./themes/<?=$ss_uri;?>/images/n_h_c.gif"><img src="./themes/<?=$ss_uri;?>/images/spacer.gif" width="1" height="14"></td>
    <td width="16"><img src="./themes/<?=$ss_uri;?>/images/n_h_r.gif" width="16" height="14"></td></tr><tr>
    <td background="./themes/<?=$ss_uri;?>/images/n_l.gif">&nbsp;</td><td bgcolor="#f2f2f2" valign="top" align="center"> 
<? show_blocks('b');?>
<? show_blocks('c');?>