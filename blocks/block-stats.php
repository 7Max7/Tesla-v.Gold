<?php
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}


$cacheStatFile = "cache/block-stats.txt"; 
$expire = 180*60; // 180 минут
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
global $tracker_lang, $ss_uri, $maxusers;

//$shoutbox = number_format(get_row_count("shoutbox"));

//$registered = number_format(get_row_count("users"));
//$unverified = number_format(get_row_count("users", "WHERE status='pending'"));
//$male = number_format(get_row_count("users", "WHERE gender='1'"));
//$female = number_format(get_row_count("users", "WHERE gender='2'"));
//$torrents = number_format(get_row_count("torrents"));
//$dead = number_format(get_row_count("torrents", "WHERE visible='no'"));
//$free = number_format(get_row_count("torrents", "WHERE free='no'"));
//$seeders = get_row_count("peers", "WHERE seeder='yes'");
//$leechers = get_row_count("peers", "WHERE seeder='no'");
//$warned_users = number_format(get_row_count("users", "WHERE warned = 'yes'"));
//$disabled = number_format(get_row_count("users", "WHERE enabled = 'no'"));
//$USER = number_format(get_row_count("users", "WHERE class = ".UC_USER));
//$POWER_USER = number_format(get_row_count("users", "WHERE class = ".UC_POWER_USER));
//$uploaders = number_format(get_row_count("users", "WHERE class = ".UC_UPLOADER));
//$vip = number_format(get_row_count("users", "WHERE class = ".UC_VIP));
//$MODERATOR = number_format(get_row_count("users", "WHERE class = ".UC_MODERATOR));
//$ADMINISTRATOR = number_format(get_row_count("users", "WHERE class = ".UC_ADMINISTRATOR));



$result = sql_query("SELECT COUNT(*) AS all_p,
(SELECT COUNT(*) FROM torrents) AS torrents,
(SELECT COUNT(*) FROM users WHERE status='pending') AS unverified,
(SELECT COUNT(*) FROM users WHERE gender='1') AS male,
(SELECT COUNT(*) FROM users WHERE gender='2') AS female,
(SELECT COUNT(*) FROM peers FORCE INDEX(torrent_seeder) WHERE seeder='yes') AS seeders,
(SELECT COUNT(*) FROM peers FORCE INDEX(torrent_seeder) WHERE seeder='no') AS leechers,
(SELECT COUNT(*) FROM users WHERE warned = 'yes') AS warned_users,
(SELECT COUNT(*) FROM users WHERE enabled = 'no') AS disabled
FROM users") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($result);
///SUM(downloaded) AS totaldl, SUM(uploaded) AS totalul
$torrents=$row["torrents"];
$registered=$row["all_p"];
$unverified=$row["unverified"];
$male=$row["male"];
$female=$row["female"];
$seeders=$row["seeders"];
$leechers=$row["leechers"];
$warned_users=$row["warned_users"];
$disabled=$row["disabled"];



/*
Пользователей	1939
Опытных пользователей	7
Аплоадеров	6
V.I.P	14
Модераторов	2
Администраторов	4



(SELECT COUNT(*) FROM users WHERE class = ".UC_USER.") AS USER,
(SELECT COUNT(*) FROM users WHERE class = ".UC_POWER_USER.") AS POWER_USER,
(SELECT COUNT(*) FROM users WHERE class = ".UC_UPLOADER.") AS uploaders,
(SELECT COUNT(*) FROM users WHERE class = ".UC_VIP.") AS vip,
(SELECT COUNT(*) FROM users WHERE class = ".UC_MODERATOR.") AS MODERATOR,
(SELECT COUNT(*) FROM users WHERE class = ".UC_ADMINISTRATOR.") AS ADMINISTRATOR

$USER = $row["USER"];
$POWER_USER = $row["POWER_USER"];
$uploaders =$row["uploaders"];
$vip = $row["vip"];
$MODERATOR =$row["MODERATOR"];
$ADMINISTRATOR = $row["ADMINISTRATOR"];
*/

$USER=$POWER_USER=$uploaders=$vip=$MODERATOR=$ADMINISTRATOR=0;
$res_u = sql_query("SELECT class FROM users") or sqlerr(__FILE__, __LINE__);
while ($arr_u = mysql_fetch_assoc($res_u)){
$class=$arr_u["class"];

if ($class==UC_USER)
++$USER;

if ($class==UC_POWER_USER)
++$POWER_USER;

if ($class==UC_UPLOADER)
++$uploaders;

if ($class==UC_VIP)
++$vip;

if ($class==UC_MODERATOR)
++$MODERATOR;

if ($class==UC_ADMINISTRATOR)
++$ADMINISTRATOR;

}
/// перерасчет пользователей



/*
(SELECT COUNT(*) FROM torrents WHERE visible='no') AS dead,
(SELECT COUNT(*) FROM torrents WHERE free='yes') AS free,
(SELECT COUNT(*) FROM torrents WHERE multitracker='yes') AS multitracker,

$torrents=$row["torrents"];
$dead=$row["dead"];
$free=$row["free"];
$multitracker=$row["multitracker"];
*/

//// сколько мертвых золотых мультитрекерных
$dead=$free=$multitracker=0;
$res_t = sql_query("SELECT visible,free,multitracker FROM torrents") or sqlerr(__FILE__, __LINE__);
while ($arr_t = mysql_fetch_assoc($res_t)){
	
if ($arr_t["visible"]=="no")
++$dead;

if ($arr_t["free"]=="yes")
++$free;

if ($arr_t["multitracker"]=="yes")
++$multitracker;
//unset($arr_t["multitracker"],$arr_t["free"],$arr_t["visible"]);
}

$file_into = number_format(get_row_count("files"));



$res_m = sql_query("SELECT SUM(f_seeders) AS f_seeders,SUM(f_leechers) AS f_leechers FROM torrents WHERE multitracker='yes'") or sqlerr(__FILE__, __LINE__);
$arr_m = mysql_fetch_assoc($res_m);

$f_seeders = number_format($arr_m["f_seeders"]);
$f_leechers = number_format($arr_m["f_leechers"]);

/*
if ($leechers == 0)
  $ratio = 0;
else
*/
$ratio = round(($seeders+$f_seeders) / ($leechers+$f_leechers) * 100);

$peers = number_format($seeders + $leechers);

$seeders = number_format($seeders);
$leechers = number_format($leechers);




//// сколько скачали раздали
$res_s = sql_query("SELECT SUM(uploaded) AS uploaded,SUM(downloaded) AS downloaded FROM snatched") or sqlerr(__FILE__, __LINE__);
$arr_s = mysql_fetch_assoc($res_s);

$totaldownloaded = $arr_s["downloaded"];
$totaluploaded = $arr_s["uploaded"];
$totaldata = $totaldownloaded+$totaluploaded;
//// сколько скачали раздали




$res = sql_query("SELECT SUM(size), (SELECT COUNT(*) FROM torrents WHERE times_completed>0) AS downtor, (SELECT SUM(hits) FROM torrents) AS hitsstor FROM torrents") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res);

$downtor = number_format($arr["downtor"]);
$hitsstor = number_format($arr["hitsstor"]);

$free_user=$maxusers-$registered;

/// уникальных пользователей и забаненых
$res_b = sql_query("SELECT SUM(invites) AS num_invites, (SELECT COUNT(*) FROM users WHERE forum_com<>'0000-00-00 00:00:00' AND enabled='yes' AND status='confirmed') AS banfor FROM users") or sqlerr(__FILE__, __LINE__);
$arr_b = mysql_fetch_assoc($res_b);
$totalinv = number_format($arr_b["num_invites"]);
$totalbannedf = number_format($arr_b["banfor"]);
/// уникальных пользователей и забаненых

////<br />".$tracker_lang['total'].": $registered
$content .= "<table width=\"100%\" class=\"main\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\"><td align=\"center\">
<table class=\"main\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">

<table width=\"100%\" class=\"main\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">
  <tr>
<td width=\"50%\" align=\"center\" style=\"border: none;\"><table class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">
<tr>
<td class=\"a\">".$tracker_lang['users_registered']."</td><td align=right><img src=\"pic/male.gif\" alt=\"Парни\">$male<img src=\"pic/female.gif\" alt=\"Девушки\">$female</td></tr>

<tr><td class=\"b\">Мест на трекере&nbsp;</td><td align=right><b>$free_user</b> / $maxusers</td></tr>

<tr><td class=\"a\">".$tracker_lang['users_unconfirmed']."</td><td align=right>$unverified</td></tr>
<tr><td class=\"b\">".$tracker_lang['users_warned']."&nbsp;<img src=\"pic/warned.gif\" border=0 align=absbottom></td><td align=right>$warned_users</td></tr>
<tr><td class=\"a\">".$tracker_lang['users_disabled']."&nbsp;<img src=\"pic/disabled.gif\" border=0 align=absbottom></td><td align=right>$disabled</td></tr>

<tr><td class=\"b\">Отключенных от форума&nbsp;</td><td align=right>$totalbannedf</td></tr>

<tr><td class=\"a\">Свободных приглашений&nbsp;</td><td align=right>$totalinv</td></tr>

<tr><td class=\"b\"><font color=\"black\">Пользователей</font></td><td align=right>$USER</td></tr>
<tr><td class=\"a\"><font color=\"#D21E36\">Опытных пользователей</font></td><td align=right>$POWER_USER</td></tr>

<tr><td class=\"b\"><font color=\"#f59555\">".$tracker_lang['users_uploaders']."</font></td><td align=right>$uploaders</td></tr>
<tr><td class=\"a\"><font color=\"#9C2FE0\">".$tracker_lang['users_vips']."</font></td><td align=right>$vip</td></tr>
<tr><td class=\"b\"><font color=\"red\">Модераторов</font></td><td align=right>$MODERATOR</td></tr>
<tr><td class=\"a\"><font color=\"green\">Администраторов</font></td><td align=right>$ADMINISTRATOR</td></tr>
</table></td>

<td width=\"50%\" align=\"center\" style=\"border: none;\"><table class=main border=1 cellspacing=0 cellpadding=5>
<tr>
<td class=\"a\">".$tracker_lang['tracker_torrents']."</td><td align=right><a href=\"browse.php\">$torrents</td></a></tr>

<tr><td class=\"b\">Золотых / Мертвых</td><td align=right><a href=\"browse.php?search=&incldead=3&cat=0\">$free</a> / <a href=\"browse.php?search=&incldead=2&cat=0\">$dead</td></a></td></tr>

<tr><td class=\"a\">Мультитрекерных</td><td align=right><a href=\"browse.php?search=&incldead=10&cat=0\">$multitracker</td></a></tr>

<tr><td class=\"b\">Файлов в торрентах</td><td align=right>$file_into</td></tr>

<tr><td class=\"a\">Скачанных / Взятых</td><td align=right>$downtor / $hitsstor</td></tr>

<tr><td class=\"b\">".$tracker_lang['tracker_peers']."</td><td align=right>$peers</td></tr>";

//f_seeders,f_leechers

$content .= "<tr><td class=\"a\">Пиры Внутр / Внешние&nbsp;&nbsp;<img src=\"pic/upload.gif\" border=0 align=absbottom></td><td align=right>$seeders / $f_seeders</td></tr>
<tr><td class=\"b\">Сиды Внутр / Внешние&nbsp;&nbsp;<img src=\"pic/download.gif\" border=0 align=absbottom></td><td align=right>$leechers / $f_leechers</td></tr>

<tr><td class=\"a\">".$tracker_lang['tracker_seed_peer']."</td><td align=right>$ratio</td></tr>
<tr><td class=b>Раздали <img src=\"pic/upload.gif\" border=0 align=absbottom></td><td align=right>".mksize($totaluploaded)."</td></tr>
<tr><td class=a>Скачали <img src=\"pic/download.gif\" border=0 align=absbottom></td><td align=right>".mksize($totaldownloaded)."</td></tr>
<tr><td class=b>Общий размер раздач</td><td align=right>".mksize($arr['SUM(size)'])."</td></tr>
<tr><td class=a>Трафик</td><td align=right>".mksize($totaldata)."</td></tr>
";


$content .= "</table></td>

</table>
</td></tr></table>";
$fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
 
 if (get_user_class() >= UC_SYSOP)
{
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
}
?>