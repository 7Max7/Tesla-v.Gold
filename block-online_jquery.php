<?
require_once 'include/bittorrent.php';

header ("Content-Type: text/html; charset=windows-1251");
dbconn(false,true);

/**
 * Данный файл проверен на индексы, все запросы быстро выполняются.
**/

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

global $CURUSER, $use_sessions;

//SELECT s.uid, s.username, s.class FROM sessions AS s WHERE s.time > $dt  AND s.inviz==0 ORDER BY s.class DESC

//$hide = array(1,2,3,4); // those IDs will not be shown online
//sql_query("SELECT id, username, class, warned, donor, enabled FROM users WHERE id NOT IN (".join(",",$hide).") AND UNIX_TIMESTAMP(last_access) >= UNIX_TIMESTAMP(DATE_SUB(NOW(),INTERVAL 5 MINUTE)) ORDER BY class DESC, username");  


$title_who = array();

//$dt = sqlesc(time() - 180); //180
//$dt = sqlesc(get_date_time(gmtime() - 180));
///[6] =>  0.000392   [SELECT DISTINCT s.uid, s.username, s.class, s.url FROM sessions AS s WHERE s.time > 1251798782 ORDER BY s.class DESC]
//sql_query("ALTER TABLE sessions ADD KEY sid_id (sid, uid);");

if ($use_sessions)
$result = sql_query("SELECT DISTINCT s.uid, s.username, s.class, s.url FROM sessions AS s WHERE s.time > ".sqlesc(get_date_time(gmtime() - 180))." ORDER BY s.class DESC") or sqlerr(__FILE__, __LINE__);
else
$result = sql_query("SELECT DISTINCT u.id, u.username, u.class FROM users AS u WHERE u.last_access > ".sqlesc(get_date_time(gmtime() - 180))." GROUP BY u.username ORDER BY u.class DESC") or sqlerr(__FILE__, __LINE__);
//// если отключить сессии будут проблеммы с , $url
$lastid=$users=$total=$SYSOP=$allusers=$admin=$moder=$vip=$upload=$power_user=$guests=0;

$who_online="";
while (list($uid, $uname, $class, $url) = mysql_fetch_row($result)) {
if($lastid==$uid && $lastid!=-1)
    continue;
$lastid=$uid;
//инвиз 

$inviz=false;
switch($uname)
{
  case "Тя":
  $inviz=true;
 break;
}
if ($inviz)
	continue;
//инвиз 

/*
	".(stristr($url,'shoutbox.php')==false ? "": "border-bottom: 1px solid rgb(0, 187, 0);")."
	
*/

   if (stristr($url,"receiver=".$CURUSER["id"]."&replyto")) {
   	$title_mes[$uname] = "<img border=\"0\" src=\"./pic/balloon--arrow.png\" title=\"Вам пишет сообщение\"/>";
    }


    if (!empty($uname)) {
    	if (stristr($url,'shoutbox.php')) {
   	
   		$title_who[] = "<a href=\"userdetails.php?id=".$uid."\">".get_user_class_color($class, $uname,1).
		   (isset($title_mes[$uname]) ? $title_mes[$uname]:"")."</a>";
    }
	else
	 	$title_who[] = "<a href=\"userdetails.php?id=".$uid."\">".get_user_class_color($class, $uname).
		   (isset($title_mes[$uname]) ? $title_mes[$uname]:"")."</a>";
	}


    if ($class >= UC_USER) {
        ++$allusers; 
   }


     if ($class == UC_SYSOP) {
        ++$SYSOP; 
    
    } elseif ($class == UC_ADMINISTRATOR){ 
        ++$admin; 
    } elseif ($class == UC_MODERATOR) { 
        ++$moder; 
    }  elseif ($class == UC_VIP) { 
        ++$vip; 
    } elseif ($class == UC_UPLOADER) { 
        ++$upload; 
    } elseif ($class == UC_POWER_USER) { 
        ++$power_user; 
    } elseif ($class == UC_USER) { 
        ++$users; 
    } elseif ($uid == -1) {
       ++$guests;
    }

    ++$total;
  
	if (empty($uname))
		continue;
	else
		$who_online .= $title_who;

}
if (empty($SYSOP))  $SYSOP = 0; 
if (empty($admin))  $admin = 0; 
if (empty($moder))  $moder = 0; 
if (empty($vip))  $vip = 0; 
if (empty($upload)) $upload = 0; 
if (empty($guests)) $guests = 0; 
if (empty($power_user))  $power_user = 0; 
if (empty($users))   $users = 0; 
if (empty($total))   $total = 0; 




$a2=new MySQLCache("SELECT id, username, class FROM users WHERE status='confirmed' ORDER BY added DESC LIMIT 1", 60*60,"block-online.txt"); // час 
$a=$a2->fetch_assoc();

// тело - то, что кешируем - начало
//$a = mysql_fetch_array(sql_query("SELECT id, username, class FROM users WHERE status='confirmed' ORDER BY added DESC LIMIT 1"));

if ($CURUSER)
	$latestuser = "<a href=userdetails.php?id=" . $a["id"] . " class=\"online\">".get_user_class_color($a["class"],$a["username"])."</a>";
else
	$latestuser = get_user_class_color($a["class"],$a["username"]);

echo "<table border=\"0\" width=\"100%\"><tr valign=\"middle\"><td align=\"left\" class=\"embedded\"><b>Последний пользователь: </b> $latestuser<hr></td></tr></table>\n";



// начинаем кешировать
$cacheStatFile2 = "cache/block-online_record.txt"; 
$expire2 = 2*60; // 1 минут на кеш, после обновление 
if (file_exists($cacheStatFile2) && filesize($cacheStatFile2)<>0 && filesize($cacheStatFile2)<>0 && filesize($cacheStatFile2)<>0 && filemtime($cacheStatFile2) > (time() - $expire2)) { 
   $record_content=file_get_contents($cacheStatFile2); 
} else 
{
$all=$total-$guests;
// тело - то, что кешируем - начало
$how = mysql_fetch_assoc(sql_query("SELECT value_i,value_u FROM avps WHERE arg = 'much_on' LIMIT 1"));
$record = $how["value_i"];
$now = time();
$as="Guests: $guests";
if (empty($how["value_u"])) {sql_query("INSERT INTO avps (arg, value_s, value_i,value_u) VALUES('much_on', '$as', '$all','$now')") or sqlerr(__FILE__, __LINE__);}

if($record <$all && !empty($how["value_u"])) { sql_query("UPDATE avps SET value_i ='$all', value_s ='$as',value_u='$now' WHERE arg = 'much_on'") or sqlerr(__FILE__, __LINE__); }

$record_time = display_date_time($how["value_u"]);

if (empty($record)){$record=$all;}

$record_content = "<tr><td class=\"embedded\">
<img alt = \"Рекорд одновременного посещения зафиксирован $record_time\" src=\"pic/buddylist.gif\"></td>
<td width=\"90%\" class=\"embedded\"> Рекорд: $record</td>
</tr>\n"; 

// Рекорд одновременного посещения зафиксирован $record_time

// тело - то, что кешируем - конец
$fp2 = fopen($cacheStatFile2,"w");
   if($fp2)
   { 
    fputs($fp2, $record_content); 
    fclose($fp2); 
   }
 }



if (get_user_class() >= UC_ADMINISTRATOR){
$in_online = "<a href=\"0nline.php\">В сети</a>";

}else
$in_online = "В сети";

if ($allusers>=30){
echo"<span style=\"cursor: pointer;\" onclick=\"javascript: show_hide('s_1920')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pics_1920\" title=\"Показать\"></span>&nbsp;"
."<span style=\"cursor: pointer;\" onclick=\"javascript: show_hide('s_1920')\">Кто в сети [$allusers]\n"	
."<span id=\"ss_1920\" style=\"display: none;\">".@implode(", ", $title_who)."</span></span>";

	echo "<hr>\n";
}


if (count($title_who)&& $allusers<30) {

	echo "<table border=\"0\" width=\"100%\"><tr valign=\"middle\"><td align=\"left\" class=\"embedded\"><b>Кто онлайн: </b><hr></td></tr><tr><td class=\"embedded\">".@implode(", ", $title_who)."<hr></td></tr></table>\n";

} elseif (empty($allusers)) {
	echo "<table border=\"0\" width=\"100%\"><tr valign=\"middle\"><td align=\"center\" class=\"embedded\"><center>Нет пользователей за последние 3 минуты.</center><hr></td></tr></table>\n";
	
}


echo "<table border=\"0\" width=\"100%\"><tr valign=\"middle\"><td colspan=\"2\" align=\"left\" class=\"embedded\"><b>$in_online: </b></td></tr>\n";

	

echo "<tr><td class=\"embedded\"><img src=\"pic/info/sysop.gif\"></td><td width=\"90%\" class=\"embedded\"><font color=\"#".get_user_rgbcolor(UC_SYSOP,"")."\">Боссов:</font> $SYSOP</td></tr>\n"; 
echo "<tr><td class=\"embedded\"><img src=\"pic/info/admin.gif\"></td><td width=\"90%\" class=\"embedded\"><font color=\"#".get_user_rgbcolor(UC_ADMINISTRATOR,"")."\">Админов:</font> $admin</td></tr>\n"; 
echo "<tr><td class=\"embedded\"><img src=\"pic/info/moder.gif\"></td><td width=\"90%\" class=\"embedded\"><font color=\"#".get_user_rgbcolor(UC_MODERATOR,"")."\">Модеров:</font> $moder</td></tr>\n"; 
echo "<tr><td class=\"embedded\"><img src=\"pic/info/vip.gif\"></td><td width=\"90%\" class=\"embedded\"><font color=\"#".get_user_rgbcolor(UC_VIP,"")."\">Vip's:</font> $vip</td></tr>\n"; 
echo "<tr><td class=\"embedded\"><img src=\"pic/info/uploader.gif\"></td><td width=\"90%\" class=\"embedded\"><font color=\"#".get_user_rgbcolor(UC_UPLOADER,"")."\">Аплоадеров:</font> $upload</td></tr>\n";
echo "<tr><td class=\"embedded\"><img src=\"pic/info/power_user.gif\"></td><td width=\"90%\" class=\"embedded\"><font color=\"#".get_user_rgbcolor(UC_POWER_USER,"")."\">Опыт. юзеров: </font> $power_user</td></tr>\n";  
echo "<tr><td class=\"embedded\"><img src=\"pic/info/member.gif\"></td><td width=\"90%\" class=\"embedded\"><font color=\"#".get_user_rgbcolor(UC_USER,"")."\">Пользователей: $users</td></tr>\n"; 
echo "<tr><td class=\"embedded\"><img src=\"pic/info/guest.gif\"></td><td width=\"90%\" class=\"embedded\">Гостей: $guests</td></tr>\n"; 

echo "<tr><td class=\"embedded\"><img src=\"pic/info/group.gif\"></td><td width=\"90%\" class=\"embedded\">Всего: $total</td></tr>

$record_content

</tr></table>\n"; 


} else @header("Location: ../index.php");

?>