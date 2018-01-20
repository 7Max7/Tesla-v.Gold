<? 
require "include/bittorrent.php";

dbconn(false,true);
header("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

global $CURUSER;

if (empty($CURUSER)) {
//@header("Location: ../index.php");
//die("<script>setTimeout('document.location.href=\"index.php\"', 10);</script>");
die;
}

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{

echo "<style>.effect { FILTER: alpha(opacity=50); -moz-opacity: .50; opacity: .50;}</style>";

$act = (string)$_POST["act"]; /// работает
$user=(int) $_POST["user"]; /// работает

  
   if (empty($user) || empty($act))
   die("Ошибка данных, не выбран id или запрос.");


if ($act=="info") { /// то, что по умолчанию

$csql_ino = sql_query("SELECT info,signature FROM users WHERE id=".sqlesc($user)) or sqlerr(__FILE__,__LINE__); 
$user = mysql_fetch_array($csql_ino);

if ($user["info"])
$view_info=format_comment($user["info"]);
 
if ($user["signature"])
$view_signature=format_comment($user["signature"]);

if ($user["info"] && $user["signature"])
$view_br="<hr>";

if (empty($user["info"]) && empty($user["signature"]))
echo "У пользователя нет подписи.";

echo (isset($view_info) ? $view_info:"").(isset($view_br) ? $view_br:"").(isset($view_signature)? $view_signature:"");

}/// проверено, работает.


elseif ($act=="uploaded") {

$r = sql_query("SELECT torrents.id, torrents.name, torrents.size, (torrents.leechers+torrents.f_leechers) AS leechers, (torrents.seeders+torrents.f_seeders) AS seeders, torrents.added, torrents.category, categories.name AS catname, categories.image AS catimage, categories.id AS catid FROM torrents LEFT JOIN categories ON torrents.category = categories.id WHERE owner=".sqlesc($user)." ORDER BY added DESC LIMIT 300") or sqlerr(__FILE__, __LINE__);
$d0=0;
if (mysql_num_rows($r) > 0) {
$d0=1;
echo "<table width=100% class=main border=0 cellspacing=0 cellpadding=5><tr>
<td class=colhead>".$tracker_lang['type']."</td>
<td class=colhead>".$tracker_lang['name']."</td>
".($use_ttl ? "<td class=colhead align=center>".$tracker_lang['ttl']."</td>" : "")."
<td class=colhead align=\"center\">".$tracker_lang['tracker_leechers']."</td>
<td class=colhead align=\"center\">".$tracker_lang['tracker_seeders']."</td>
<td class=colhead>".$tracker_lang['size']."</td>
</tr>\n";

while ($a = mysql_fetch_assoc($r)) {
$size = mksize($a["size"]);
$ttl = ($ttl_days*24) - floor((gmtime() - sql_timestamp_to_unix_timestamp($a["added"])) / 3600);
if ($ttl == 1) $ttl .= "&nbsp;час"; else $ttl .= "&nbsp;часов";

$cat = "<a href=\"browse.php?cat=$a[catid]\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" src=\"pic/cats/$a[catimage]\" alt=\"$a[catname]\" border=\"0\" /></a>";

echo "<tr>
<td >$cat</td>
<td><a href=\"details.php?id=" . $a["id"] . "&hit=1\"><b>" . $a["name"] . "</b></a></td>
".($use_ttl ? "<td align=center>$ttl</td>" : "")."
<td align=center>$a[seeders]</td>
<td align=center>$a[leechers]</td>
<td align=center>".$size."</td>
</tr>\n";
  }
  echo "</table>";
}
if (empty($d0))
echo "У пользователя нет заливок.";

}///// конец uploaded

elseif ($act=="snatched") {
	

$r = sql_query("SELECT snatched.torrent AS id, snatched.uploaded, snatched.seeder, snatched.downloaded, snatched.startdat, snatched.completedat, snatched.last_action, categories.name AS catname, categories.image AS catimage, categories.id AS catid, torrents.name, (torrents.leechers+torrents.f_leechers) AS leechers, (torrents.seeders+torrents.f_seeders) AS seeders 
FROM snatched
LEFT JOIN torrents ON torrents.id = snatched.torrent JOIN categories ON torrents.category = categories.id 
WHERE snatched.finished='no' AND userid = ".sqlesc($user)." ORDER BY last_action DESC LIMIT 300") or sqlerr(__FILE__,__LINE__);
$d4=0;
if (mysql_num_rows($r) > 0) {
$d4=1;
echo "<table class=\"main\" width=100% border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n" .
  "<tr>
  <td class=\"colhead\">Тип</td>
  <td class=\"colhead\">Название</td>
  <td class=colhead>".$tracker_lang['tracker_seeders']." / ".$tracker_lang['tracker_leechers']."</td>
  <td class=\"colhead\">Раздал</td>
  <td class=\"colhead\">Скачал</td>
  <td class=\"colhead\">Рейтинг</td>
  <td class=\"colhead\">Начал / Закончил</td>
  <td class=\"colhead\">Действие</td>
  <td class=\"colhead\">Пир</td>
  </tr>\n";

while ($a = mysql_fetch_array($r)) {

if ($a["downloaded"] > 0) {
      $ratio = number_format($a["uploaded"] / $a["downloaded"], 3);
      $ratio = "<font color=\"" . get_ratio_color($ratio) . "\">$ratio</font>";
   } else
if ($a["uploaded"] > 0)
$ratio = "Infinity";
	else
$ratio = "---";

$uploaded = mksize($a["uploaded"]);
$downloaded = mksize($a["downloaded"]);

if ($a["seeder"] == 'yes')
$seeder = "<font color=\"green\">Да</font>";
else
$seeder = "<font color=\"red\">Нет</font>";

$cat = "<a href=\"browse.php?cat=$a[catid]\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" src=\"pic/cats/$a[catimage]\" alt=\"$a[catname]\" border=\"0\" /></a>";
echo "<tr>
<td >$cat</td>
<td><a href=\"details.php?id=" . $a["id"] . "\"><b>" . $a["name"] . "</b></a></td>
<td align=\"center\">".$a["leechers"]." / ".$a["seeders"]."</td>
<td align=\"center\">".$uploaded."</td>
<td align=\"center\">".$downloaded."</td>
<td align=\"center\">".$ratio."</td><td align=\"center\">".$a["startdat"]."<br />".$a["completedat"]."</td>
<td align=\"center\">".$a["last_action"]."</td>
<td align=\"center\">".$seeder."</td>\n";
}
echo "</table>";
}

if ($d4==0) {
echo "Нет взятых торрентов, которые не скачали.";
}	

} /// конец snatched

elseif ($act=="completed") {

$r = sql_query("SELECT snatched.torrent AS id, snatched.uploaded, snatched.seeder, snatched.downloaded, snatched.startdat, snatched.completedat, snatched.last_action, categories.name AS catname, categories.image AS catimage, categories.id AS catid, torrents.name, (torrents.leechers+torrents.f_leechers) AS leechers, (torrents.seeders+torrents.f_seeders) AS seeders FROM snatched JOIN torrents ON torrents.id = snatched.torrent JOIN categories ON torrents.category = categories.id WHERE snatched.finished='yes' AND userid = ".sqlesc($user)." ORDER BY torrent DESC LIMIT 300") or sqlerr(__FILE__,__LINE__);
	$d5=0;
if (mysql_num_rows($r) > 0) {
	$d5=1;
echo "<table class=\"main\" width=100% border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n" .
  "<tr>
  <td class=\"colhead\">Тип</td>
  <td class=\"colhead\">Название</td>
  <td class=colhead>".$tracker_lang['tracker_leechers']." / ".$tracker_lang['tracker_seeders']."</td>
  <td class=\"colhead\">Раздал</td>
  <td class=\"colhead\">Скачал</td>
  <td class=\"colhead\">Рейтинг</td>
  <td class=\"colhead\">Начал / Закончил</td>
  <td class=\"colhead\">Действие</td>
  <td class=\"colhead\">Пир</td>
  </tr>\n";
$d=0;
while ($a = mysql_fetch_array($r)) {
$d=1;
if ($a["downloaded"] > 0) {
      $ratio = number_format($a["uploaded"] / $a["downloaded"], 3);
      $ratio = "<font color=\"" . get_ratio_color($ratio) . "\">$ratio</font>";
   } else
if ($a["uploaded"] > 0)
$ratio = "Infinity";
	else
$ratio = "---";

$uploaded = mksize($a["uploaded"]);
$downloaded = mksize($a["downloaded"]);

if ($a["seeder"] == 'yes')
$seeder = "<font color=\"green\">Да</font>";
else
$seeder = "<font color=\"red\">Нет</font>";

$cat = "<a href=\"browse.php?cat=$a[catid]\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" src=\"pic/cats/$a[catimage]\" alt=\"$a[catname]\" border=\"0\" /></a>";
echo "<tr>
<td >$cat</td>
<td><a href=\"details.php?id=" . $a["id"] . "&amp;hit=1\"><b>" . $a["name"] . "</b></a></td>
<td align=\"center\">$a[seeders] / $a[leechers]</td>
<td align=\"center\">$uploaded</td>
<td align=\"center\">$downloaded</td>
<td align=\"center\">$ratio</td><td align=\"center\">$a[startdat]<br />$a[completedat]</td>
<td align=\"center\">$a[last_action]</td>
<td align=\"center\">$seeder</td>\n";
}
echo "</table>";
}

if ($d5==0) {
echo "Список скачанных торрентов - пуст.";
}
	
} /// конец complete

elseif ($act=="leeching") {

$res = sql_query("SELECT torrent, added, uploaded, downloaded, torrents.name AS torrentname, categories.name AS catname, categories.id AS catid, size, image, category, seeders, leechers FROM peers LEFT JOIN torrents ON peers.torrent = torrents.id LEFT JOIN categories ON torrents.category = categories.id WHERE userid = ".sqlesc($user)." AND seeder='no' ORDER BY added DESC LIMIT 300") or sqlerr(__FILE__, __LINE__);
///(torrents.leechers+torrents.f_leechers) AS leechers, (torrents.seeders+torrents.f_seeders) AS seeders, 
  $d1=0;
  while ($arr = mysql_fetch_assoc($res))
  {
  $d1=1;
    if ($arr["downloaded"] > 0)
    {
      $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
      $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
    }
    else
      if ($arr["uploaded"] > 0)
        $ratio = "Infinity";
      else
        $ratio = "---";
    $catid = $arr["catid"];
	$catimage = htmlspecialchars($arr["image"]);
	$catname = htmlspecialchars($arr["catname"]);
	$ttl = ($ttl_days*24) - floor((gmtime() - sql_timestamp_to_unix_timestamp($arr["added"])) / 3600);
	if ($ttl == 1) $ttl .= "&nbsp;час"; else $ttl .= "&nbsp;часов";
	$size = str_replace(" ", "<br />", mksize($arr["size"]));
	$uploaded = str_replace(" ", "<br />", mksize($arr["uploaded"]));
	$downloaded = str_replace(" ", "<br />", mksize($arr["downloaded"]));
	$seeders = number_format($arr["seeders"]);
	$leechers = number_format($arr["leechers"]);
    $leeching_array.="<tr>
	<td style='padding: 0px'><a href=\"browse.php?cat=$catid\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" src=\"pic/cats/$catimage\" alt=\"$catname\" border=\"0\" /></a></td>
	<td><a href=details.php?id=$arr[torrent]&amp;hit=1><b>" . $arr["torrentname"] ."</b></a></td>
	".($use_ttl ? "<td align=center>$ttl</td>" : "")."
	<td align=center>$size</td>
	<td align=right>$seeders</td><td align=right>$leechers</td>
	<td align=center>$uploaded</td>
	<td align=center>$downloaded</td>
	<td align=center>$ratio</td></tr>\n";
  }
  if ($d1==1){
  	
  echo "<table class=main width=100% border=0 cellspacing=0 cellpadding=5><tr>
  <td class=colhead align=left>".$tracker_lang['type']."</td>
  <td class=colhead>".$tracker_lang['name']."</td>
  ".($use_ttl ? "<td class=colhead align=center>".$tracker_lang['ttl']."</td>" : "")."
  <td class=colhead align=center>".$tracker_lang['size']."</td>
  <td class=colhead align=right>".$tracker_lang['tracker_leechers']."</td>
  <td class=colhead align=right>".$tracker_lang['tracker_seeders']."</td>
  <td class=colhead align=center>".$tracker_lang['uploaded']."</td>
  <td class=colhead align=center>".$tracker_lang['downloaded']."</td>
  <td class=colhead align=center>".$tracker_lang['ratio']."</td></tr>\n";
  echo $leeching_array;
  echo"</table>\n";
   }  else
  echo "Пользователь на данный момент не качает торренты."; 
}/// конец leeching

elseif ($act=="seeding") {

$res = sql_query("SELECT torrent, added, uploaded, downloaded, torrents.name AS torrentname, categories.name AS catname, categories.id AS catid, size, image, category, seeders, leechers FROM peers LEFT JOIN torrents ON peers.torrent = torrents.id LEFT JOIN categories ON torrents.category = categories.id WHERE userid = ".sqlesc($user)." AND seeder='yes' ORDER BY added DESC LIMIT 300") or sqlerr(__FILE__, __LINE__);

  $d2=0;
  while ($arr = mysql_fetch_assoc($res))
  {
  $d2=1;
    if ($arr["downloaded"] > 0)
    {
      $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
      $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
    }
    else
      if ($arr["uploaded"] > 0)
        $ratio = "Infinity";
      else
        $ratio = "---";
    $catid = $arr["catid"];
	$catimage = htmlspecialchars($arr["image"]);
	$catname = htmlspecialchars($arr["catname"]);
	$ttl = ($ttl_days*24) - floor((gmtime() - sql_timestamp_to_unix_timestamp($arr["added"])) / 3600);
	if ($ttl == 1) $ttl .= "&nbsp;час"; else $ttl .= "&nbsp;часов";
	$size = str_replace(" ", "<br />", mksize($arr["size"]));
	$uploaded = str_replace(" ", "<br />", mksize($arr["uploaded"]));
	$downloaded = str_replace(" ", "<br />", mksize($arr["downloaded"]));
	$seeders = number_format($arr["seeders"]);
	$leechers = number_format($arr["leechers"]);
    $leeching_array.="<tr>
	<td style='padding: 0px'><a href=\"browse.php?cat=$catid\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" src=\"pic/cats/$catimage\" alt=\"$catname\" border=\"0\" /></a></td>
	<td><a href=details.php?id=$arr[torrent]><b>" . $arr["torrentname"] ."</b></a></td>
	".($use_ttl ? "<td align=center>$ttl</td>" : "")."
	<td align=center>$size</td>
	<td align=right>$seeders</td><td align=right>$leechers</td>
	<td align=center>$uploaded</td>
	<td align=center>$downloaded</td>
	<td align=center>$ratio</td></tr>\n";
  }
  if ($d2==1){
  	
  echo "<table class=main width=100% border=0 cellspacing=0 cellpadding=5><tr>
  <td class=colhead align=left>".$tracker_lang['type']."</td>
  <td class=colhead>".$tracker_lang['name']."</td>
  ".($use_ttl ? "<td class=colhead align=center>".$tracker_lang['ttl']."</td>" : "")."
  <td class=colhead align=center>".$tracker_lang['size']."</td>
  <td class=colhead align=center>".$tracker_lang['tracker_leechers']."</td>
  <td class=colhead align=center>".$tracker_lang['tracker_seeders']."</td>
  <td class=colhead align=center>".$tracker_lang['uploaded']."</td>
  <td class=colhead align=center>".$tracker_lang['downloaded']."</td>
  <td class=colhead align=center>".$tracker_lang['ratio']."</td></tr>\n";
  echo $leeching_array;
  echo"</table>\n";
   }  else
  echo "Пользователь на данный момент не раздает торренты."; 
}/// конец seeding

elseif ($act=="checked") {

$r = sql_query("SELECT torrents.id, torrents.name, torrents.size, (torrents.leechers+torrents.f_leechers) AS leechers, (torrents.seeders+torrents.f_seeders) AS seeders, torrents.added, torrents.category, categories.name AS catname, categories.image AS catimage, categories.id AS catid FROM torrents LEFT JOIN categories ON torrents.category = categories.id WHERE moderatedby=".sqlesc($user)." ORDER BY added DESC LIMIT 300") or sqlerr(__FILE__, __LINE__);
$d3=0;
if (mysql_num_rows($r) > 0) {
$d3=1;
echo "<table width=100% class=main border=0 cellspacing=0 cellpadding=5><tr>
<td class=colhead>".$tracker_lang['type']."</td>
<td class=colhead>".$tracker_lang['name']."</td>
".($use_ttl ? "<td class=colhead align=center>".$tracker_lang['ttl']."</td>" : "")."
<td class=colhead>".$tracker_lang['tracker_leechers']."</td>
<td class=colhead>".$tracker_lang['tracker_seeders']."</td>
<td class=colhead>".$tracker_lang['size']."</td>
</tr>\n";

while ($a = mysql_fetch_assoc($r)) {
$size = mksize($a["size"]);
$ttl = ($ttl_days*24) - floor((gmtime() - sql_timestamp_to_unix_timestamp($a["added"])) / 3600);
if ($ttl == 1) $ttl .= "&nbsp;час"; else $ttl .= "&nbsp;часов";

$cat = "<a href=\"browse.php?cat=$a[catid]\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" src=\"pic/cats/$a[catimage]\" alt=\"$a[catname]\" border=\"0\" /></a>";

echo "<tr>
<td >$cat</td>
<td><a href=\"details.php?id=" . $a["id"] . "&hit=1\"><b>" . $a["name"] . "</b></a></td>
".($use_ttl ? "<td align=center>$ttl</td>" : "")."
<td align=center>$a[seeders]</td>
<td align=center>$a[leechers]</td>
<td align=center>".$size."</td>
</tr>\n";
  }
  echo "</table>";
}
if (empty($d3))
echo "У пользователя нет одобрений на торрент файлы.";

}/// конец checked

elseif ($act=="inviter") {

$it = sql_query("SELECT id, username, class,added,email FROM users AS u WHERE invitedby = ".sqlesc($user)." ORDER BY invitedby");
if (mysql_num_rows($it) >= 1) {

echo "<table align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\"><tr>
<td class=\"colhead\">Пользователь</td>
".(($user==$CURUSER["id"] || get_user_class() >= UC_MODERATOR) ? "<td class=\"colhead\">Почта</td>":"")."
<td class=\"colhead\">Регистрация</td>";
	$invi_num=0;

	while ($inviter = mysql_fetch_array($it)) {

		echo "<tr>
		<td class=\"b\"><a href=\"userdetails.php?id=$inviter[id]\">".get_user_class_color($inviter["class"], $inviter["username"])."</a></td>
".(($user==$CURUSER["id"] || get_user_class() >= UC_MODERATOR) ? "<td class=\"b\">".htmlentities($inviter["email"])."</td>":"")."
<td class=\"b\">".($inviter["added"])."</td>
		</tr>";
		++$invi_num;
		}
    
  
} else die("У пользователя нет приглашенных.");
}
///////////////////
elseif ($act=="out_re" && ($user==$CURUSER["id"] || get_user_class() >= UC_MODERATOR)) {

$it = sql_query("SELECT u.torrentid,u.userid,u.touid,users.username,users.class, torrents.name
FROM thanks AS u 
LEFT JOIN torrents ON torrents.id=u.torrentid
LEFT JOIN users ON users.id=u.touid
WHERE userid = ".sqlesc($user)." ORDER BY u.id DESC
");
if (mysql_num_rows($it) >= 1) {

echo "<table align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\"><tr>
<td class=\"colhead\">Пользователя</td>
<td class=\"colhead\">за Торрент (название)</td>";
	$invi_num=0;
     $del=0;
	while ($inviter = mysql_fetch_assoc($it)) {

if (!empty($inviter["username"])){
echo "<tr>
<td class=\"b\"><a href=\"userdetails.php?id=".$inviter["touid"]."\">".get_user_class_color($inviter["class"], $inviter["username"])."</a>
</td>
<td class=\"b\">".(!empty($inviter["name"]) ? "<a href=\"details.php?id=".$inviter["torrentid"]."\">".$inviter["name"]."</a>":" торрент удален ")."</td>
</tr>";
++$invi_num;
} else {
++$del;
//	unset($inviter["touid"],$inviter["username"],$inviter["class"]);
}
}
echo "</table>";

echo "<table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\"><tr>
<td align=\"center\" class=\"b\"><b>Всего благодарностей</b>: ".number_format($invi_num+$del)." <b>Где, удаленным пользователям</b>: ".number_format($del)."</td></tr>";

///echo "<table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\"><tr><td align=\"center\" class=\"b\"><b>Всего поблагодаривших</b>: ".number_format($invi_num)." <b>Из которых, удаленные пользователи</b>: ".number_format($del)."</td></tr>";
  
  
} else die("Пользователь никого не благодарил.");

} elseif ($act=="in_re" && ($user==$CURUSER["id"] || get_user_class() >= UC_MODERATOR)) {

$it = sql_query("SELECT u.torrentid,u.userid,u.touid,users.username,users.class, torrents.name
FROM thanks AS u 
LEFT JOIN torrents ON torrents.id=u.torrentid
LEFT JOIN users ON users.id=u.userid
WHERE touid = ".sqlesc($user)." ORDER BY u.id DESC
");
if (mysql_num_rows($it) >= 1) {

echo "<table align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\"><tr>
<td class=\"colhead\">Пользователь</td>
<td class=\"colhead\">за Торрент (название)</td>";

$invi_num=0;
$del=0;

while ($inviter = mysql_fetch_assoc($it)) {

if (!empty($inviter["username"])){
echo "<tr>
<td class=\"b\"><a href=\"userdetails.php?id=".$inviter["userid"]."\">".get_user_class_color($inviter["class"], $inviter["username"])."</a>
</td>
<td class=\"b\">".(!empty($inviter["name"]) ? "<a href=\"details.php?id=".$inviter["torrentid"]."\">".$inviter["name"]."</a>":" торрент удален ")."</td>
</tr>";
++$invi_num;
} else {
++$del;
//	unset($inviter["touid"],$inviter["username"],$inviter["class"]);
}
}
echo "</table>";

echo "<table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\"><tr><td align=\"center\" class=\"b\"><b>Всего поблагодаривших</b>: ".number_format($invi_num+$del)." <b>Где, удаленные пользователи</b>: ".number_format($del)."</td></tr>";

} else die("Пользователя никто не благодарил.");
}

elseif ($act=="agent" && ($user==$CURUSER["id"] || get_user_class() >= UC_MODERATOR)) {

$rsi = sql_query("SELECT crc, idagent FROM users WHERE id=".sqlesc($user)) or sqlerr(__FILE__, __LINE__); 
$user = mysql_fetch_assoc($rsi);

if (empty($user["idagent"]))
die("Пользователя не имеет браузеров, видимо недавно зарегистрировался.");


$str = substr($user["idagent"], 0, -1); // урезаю посл символ он же ,
$rs = sql_query("SELECT added,agent,crc32 FROM useragent WHERE id IN (".implode(", ", array($str)).") ORDER BY id DESC LIMIT 500") or sqlerr(__FILE__, __LINE__); 

if (mysql_num_rows($rs) >= 1) {

echo "<table align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\"><tr>
<td class=\"colhead\">#</td>
<td class=\"colhead\">Браузер</td>
".(get_user_class() >= UC_MODERATOR ? "<td align=\"center\" class=\"colhead\">Хеш сумма</td>":"")."
<td align=\"center\" class=\"colhead\">Добавлен</td>";

$invi_num=1;

while ($inviter = mysql_fetch_assoc($rs)) {

echo "<tr>
<td ".($inviter["crc32"]==$user["crc"] ? "class=\"a\"":"class=\"b\"").">".$invi_num."</td>
<td ".($inviter["crc32"]==$user["crc"] ? "class=\"a\"":"class=\"b\"").">".htmlentities($inviter["agent"])."</td>

".(get_user_class() >= UC_MODERATOR ? "<td ".($inviter["crc32"]==$user["crc"] ? "class=\"a\"":"class=\"b\"")." align=\"center\">".$inviter["crc32"]."</td>":"")."

<td ".($inviter["crc32"]==$user["crc"] ? "class=\"a\"":"class=\"b\"")." align=\"center\">".$inviter["added"]."</td>
</tr>";
++$invi_num;
}

echo "</table>";

} else die("Пользователя не имеет браузеров, видимо недавно зарегистрировался.");
}



elseif ($act=="ref" && ($user==$CURUSER["id"] || get_user_class() >= UC_MODERATOR)) {

if ($refer_parse=="1") {

if (empty($refer_day))
$refer_day = 2;

$secs = $refer_day * (86400 * 31); // удаляем старые данные старше N месяцев
$dt = get_date_time(gmtime() - $secs);
sql_query("DELETE FROM referrers WHERE date < ".sqlesc($dt)) or sqlerr(__FILE__,__LINE__);
}
else
die("Фунция рефералов отключена.");

$csdql_ino = sql_query("SELECT ip FROM users WHERE id=".sqlesc($user)) or sqlerr(__FILE__,__LINE__); 
$user_t = mysql_fetch_array($csdql_ino);

$rs = sql_query("SELECT id, parse_url, parse_ref, numb, date, lastdate FROM referrers WHERE uid=".sqlesc($user)." OR (ip=".sqlesc($user_t["ip"])." AND uid='0') GROUP BY parse_ref ORDER BY date DESC LIMIT 500") or sqlerr(__FILE__, __LINE__); 

if (mysql_num_rows($rs) >= 1) {

echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" class=\"main\">";

echo "<tr>
<td class=colhead colspan=\"3\" width=\"100%\">Данные удаляются спустя 2х месяцев (показ последних 500 строк от последнему к первому)</td></tr>";
echo "</table>";

echo "<table align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\"><tr>
<td class=\"colhead\">#</td>
<td class=\"colhead\">Реферальный сайт (откуда пришел пользователь)</td>
<td align=\"center\" class=\"colhead\">Время / Обновление</td>
<td align=\"center\" class=\"colhead\">Количество</td>";

$invi_num=1;

while ($inviter = mysql_fetch_assoc($rs)) {

$inviter["parse_ref"] = str_replace($inviter["parse_url"],"<b>".$inviter["parse_url"]."</b>",$inviter["parse_ref"]);
	
echo "<tr>
<td ".($invi_num%2==0 ? "class=\"b\"":"class=\"a\"").">".$invi_num."</td>
<td ".($invi_num%2==0 ? "class=\"a\"":"class=\"b\"").">".($inviter["parse_ref"])."</td>
<td ".($invi_num%2==0 ? "class=\"b\"":"class=\"a\"")." align=\"center\">".$inviter["date"]."<br>".($inviter["lastdate"]=="0000-00-00 00:00:00" ? "нет":$inviter["lastdate"])."</td>
<td ".($invi_num%2==0 ? "class=\"a\"":"class=\"b\"")." align=\"center\">".$inviter["numb"]."<br></td>
</tr>";
++$invi_num;
}

echo "</table>";

} else die("Пользователя не имеет реферальные входы на этот сайт.");
}






}

/// общая закрывающая 
 else die;
?>