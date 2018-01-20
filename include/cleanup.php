<?
if(!defined('IN_TRACKER'))
  die('А Куда это мы прём?!');


function docleanup() {
global $torrent_dir, $signup_timeout, $max_dead_torrent_time, $use_ttl, $autoclean_interval, $points_per_cleanup, $ttl_days, $tracker_lang, $SITENAME, $dead_torrent_time, $use_dead_torrent, $maxlogin, $fixed_bonus, $use_ipbans, $readpost_expiry, $announce_urls,$auto_duploader,$DEFAULTBASEURL;


@set_time_limit(0);
@ignore_user_abort(1);


//////////////// проверка всех файлов на винте

$now = time();
$res = sql_query("SELECT value_u FROM avps WHERE arg = 'cheсk_torrent'") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$row_time=$row["value_u"];
$now_2day=time()+(86400*88888888); /// раз в три дня
$dt = get_date_time(gmtime());

if ($row_time<=$now){

//// что выполняем
	do {
			
		$res = sql_query("SELECT id FROM torrents") or sqlerr(__FILE__,__LINE__);
		$ar = array();
		while ($row = mysql_fetch_array($res)) {
			$id = $row[0];
			$ar[$id] = 1;
		}

        //// если нет торрентов вернуть обратно
		if (!count($ar))
			break;

       //// если невозможно открыть папку с  торрентами вернуть обратно
		$dp = @opendir($torrent_dir);
		if (!$dp)
			break;

        ///// создаем массив
		$ar2 = array();
		/// читам файл торрентов
		while (($file = readdir($dp)) !== false) {
			
             /// убираю из названия мои дополнения по save_as - > выписываю $id[up] или $id[ta] к .torrent
             /// иначе значение проверки будет ложным и удалится
           // $file=preg_replace("/[up]+/","", $file);
         ///   $file=preg_replace("/[ta]+/","", $file);

			//// выделяем файлы .torrent (PS скорее всего числа до точки)
			if (!preg_match('/^(\d+)\.torrent$/', $file, $m))
				continue;


            /// присваиваем значения
			$id = $m[1];
			$ar2[$id] = 1;

			//// если есть массив что выше до открытия папки - продолжаем
			if (isset($ar[$id]) && $ar[$id])
				continue;
		//	if (stristr($file,"[up]") !== false || stristr($file,"[ta]") !== false)
		//		continue;
			
			$ff = $torrent_dir . "/$file";
        @unlink($ff);
        ///ROOT_PATH.

		}
		/// закрываем папку торрентов
		closedir($dp);
        //// возращаемся обратно если нет файлов в папке
		if (!count($ar2))
			break;
        ///// создаем массив delids
		$delids = array();
		/// разлаживаем массив первого запроса к проверке со вторым запросом $ar || $ar2
		foreach (array_keys($ar) as $k) {
			
			//// если есть массив 2 запроса - продолжаем
			if (isset($ar2[$k]) && $ar2[$k])
				continue;
			/// вписываем $k значение первого запроса в значение массива $delids
			$delids[] = $k;
			/// снимаем значение с первого запроса массива 
			unset($ar[$k]);
		}
   if (count($delids))
  	sql_query("DELETE FROM torrents WHERE id IN (" . join(",", $delids) . ")") or sqlerr(__FILE__,__LINE__);


		$res = sql_query("SELECT torrent FROM peers GROUP BY torrent") or sqlerr(__FILE__,__LINE__);
		$delids = array();
		while ($row = mysql_fetch_array($res)) {
			$id = $row[0];
			if (isset($ar[$id]) && $ar[$id])
				continue;
			$delids[] = $id;
		}
		if (count($delids))
			sql_query("DELETE FROM peers WHERE torrent IN (" . join(",", $delids) . ")") or sqlerr(__FILE__,__LINE__);

		$res = sql_query("SELECT torrent FROM files GROUP BY torrent") or sqlerr(__FILE__,__LINE__);
		$delids = array();
		while ($row = mysql_fetch_array($res)) {
			$id = $row[0];
			if ($ar[$id])
				continue;
			$delids[] = $id;
		}
		if (count($delids))
			sql_query("DELETE FROM files WHERE torrent IN (" . join(", ", $delids) . ")") or sqlerr(__FILE__,__LINE__);
	} while (0);

/// выполнили и 

if (empty($row_time)) {
sql_query("INSERT INTO avps (arg, value_u,value_i,value_s) VALUES ('cheсk_torrent',$now_2day,'$numo','$dt')");
} elseif (!empty($row_time)) {
sql_query("UPDATE avps SET value_u='$now_2day',value_i='$numo', value_s='$dt' WHERE arg='cheсk_torrent'");
}
}
//////////////// проверка всех файлов на винте



	$deadtime = deadtime();
	sql_query("DELETE FROM peers WHERE last_action < FROM_UNIXTIME($deadtime)") or sqlerr(__FILE__,__LINE__);

	$deadtime = deadtime();
	sql_query("UPDATE snatched SET seeder = 'no' WHERE seeder = 'yes' AND last_action < FROM_UNIXTIME($deadtime)");

///	$deadtime -= $max_dead_torrent_time;
///	sql_query("UPDATE torrents SET visible='no' WHERE visible='yes' AND last_action < FROM_UNIXTIME($deadtime) AND (f_seeders+f_leechers)='0'") or sqlerr(__FILE__,__LINE__);




/*
	$deadtime -= $max_dead_torrent_time;
	sql_query("UPDATE torrents SET visible='no' WHERE visible='yes' AND last_action < FROM_UNIXTIME($deadtime)") or sqlerr(__FILE__,__LINE__);

	sql_query("UPDATE torrents SET visible='yes' WHERE visible='no' AND multitracker='yes' AND f_seeders+f_leechers>'0'") or sqlerr(__FILE__,__LINE__);
*/

//	sql_query("UPDATE torrents SET visible='no' WHERE leechers+seeders = '0' AND visible='yes'");

/*
	$torrents = array();
	$res = sql_query("SELECT torrent, seeder, COUNT(*) AS c FROM peers GROUP BY torrent, seeder") or sqlerr(__FILE__,__LINE__);
	while ($row = mysql_fetch_assoc($res)) {
		if ($row["seeder"] == "yes")
			$key = "seeders";
		else
			$key = "leechers";
		$torrents[$row["torrent"]][$key] = $row["c"];
	}

	$res = sql_query("SELECT torrent, COUNT(*) AS c FROM comments GROUP BY torrent") or sqlerr(__FILE__,__LINE__);
	while ($row = mysql_fetch_assoc($res)) {
		$torrents[$row["torrent"]]["comments"] = $row["c"];
	}

	$fields = explode(":", "comments:leechers:seeders");
	$res = sql_query("SELECT id, seeders, leechers, comments FROM torrents") or sqlerr(__FILE__,__LINE__);
	while ($row = mysql_fetch_assoc($res)) {
		$id = $row["id"];
		$torr = (isset($torrents[$id]) ? $torrents[$id]:""); 
		//// проверить
		foreach ($fields as $field) {
			if (!isset($torr[$field]))
				$torr[$field] = 0;
		}
		$update = array();
		foreach ($fields as $field) {
			if ($torr[$field] <> $row[$field])
				$update[] = "$field = " . $torr[$field];
		}
		if (count($update))
			sql_query("UPDATE torrents SET " . implode(", ", $update) . " WHERE id = $id") or sqlerr(__FILE__,__LINE__);
	}
*/


$torrents = array();

$res = sql_query("SELECT torrent, seeder, COUNT(*) AS c FROM peers GROUP BY torrent, seeder") or sqlerr(__FILE__,__LINE__);
while ($row = mysql_fetch_assoc($res)) {
if ($row["seeder"] == "yes")
$key = "seeders";
else
$key = "leechers";
$torrents[$row["torrent"]][$key] = $row["c"];
}

$fields = explode(":", "leechers:seeders");
//	print_r($torrents);
$update = array();
foreach ($torrents as $id => $seeders) {
//echo "$id - leechers=$seeders[leechers] и seeders=$seeders[seeders]<br>";

$update[] = "leechers = ".(!empty($seeders["leechers"])? $seeders["leechers"]:"0");
$update[] = "seeders = ".(!empty($seeders["seeders"])? $seeders["seeders"]:"0");
$update[] = "checkpeers = ".sqlesc(get_date_time());
sql_query("UPDATE torrents SET ".implode(", ", $update)." WHERE id=".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
unset($update);
}
	
	
	
	
	

$h = date('H'); // проверяем час
if (($h >= 21 )&&($h <= 23)) // расписание на позд вечер
{
	

//// удаление старых торрентов 
if ($use_dead_torrent){
		$dt2 = sqlesc(get_date_time(gmtime() - ($dead_torrent_time * 86400)));
		$res3 = sql_query("SELECT id, name,image1,tags,last_action FROM torrents WHERE seeders = '0' and leechers='0' and times_completed='0' and f_seeders='0' and f_leechers='0' and last_action <= $dt2") or sqlerr(__FILE__,__LINE__);
		while ($arr3 = mysql_fetch_assoc($res3)) {

         $time_del="(акт. ".get_elapsed_time(sql_timestamp_to_unix_timestamp($arr3["last_action"]))." назад)";

			@unlink(ROOT_PATH."torrents/".$arr3["id"].".torrent");
			sql_query("DELETE FROM torrents WHERE id=$arr3[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM snatched WHERE torrent=$arr3[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM peers WHERE torrent=$arr3[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM comments WHERE torrent=$arr3[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM files WHERE torrent=$arr3[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM ratings WHERE torrent=$arr3[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM checkcomm WHERE checkid=$arr3[id] AND torrent = 1") or sqlerr(__FILE__,__LINE__);
         	sql_query("DELETE FROM bookmarks WHERE torrentid=$arr3[id]") or sqlerr(__FILE__,__LINE__);
  	
			write_log("Торрент $arr3[id] ($arr3[name]) удален системой: нет сидов пиров скачек ".$time_del."","","torrent");

             if (!preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $arr3["image1"]) && !empty($arr3["image1"]))
            @unlink(ROOT_PATH."torrents/images/".$arr3["image1"]);	   	

		
		$tags = explode(",", $arr3["tags"]);

foreach ($tags as $tag) {
		@sql_query("UPDATE tags SET howmuch=howmuch-1 WHERE name LIKE ".sqlesc($tag)) or sqlerr(__FILE__, __LINE__);
	}
	}
}
}/// расписание

$h = date('H'); // проверяем час
if (($h >= 13 )&&($h <= 18)) // расписание на обед
{

// бан после отключения (за день до удаления аккаунта), мод 7Max7
	    $secs = 276*86400;
		$dt = sqlesc(get_date_time(gmtime() - $secs));
		$maxclass = UC_POWER_USER;
		$res = sql_query("SELECT id,username, email,(SELECT id FROM bannedemails WHERE email=users.email) AS useremail FROM users WHERE enabled='no' AND class <= $maxclass AND last_access < $dt") or sqlerr(__FILE__,__LINE__);
		while ($arr = mysql_fetch_assoc($res)) 
		if (!$arr["useremail"]) {
		$USER = sqlesc("92"); // ViKa
		$comment="Бан за день до удаления ($arr[username])";
		$email= $arr["email"];

        sql_query("INSERT INTO bannedemails (added, addedby, comment, email) VALUES(".sqlesc(get_date_time()).", $USER, ".sqlesc($comment).", ".sqlesc($email).")") ; 

         write_log("Отключенный аккаунт $arr[username] ($arr[email]) был забанен по почте системой.","","bans");
		}



//удаление неактивных пользователей старше
$secs = 365*86400; // 1 год
$dt = sqlesc(get_date_time(gmtime() - $secs));
$maxclass = UC_POWER_USER;
$res = sql_query("SELECT id, username, avatar, enabled FROM users WHERE parked='no' AND override_class=255 AND status='confirmed' AND class <= $maxclass AND last_access < $dt AND last_access <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);

while ($arr = mysql_fetch_assoc($res)) {
		
sql_query("DELETE FROM users WHERE id = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM messages WHERE receiver = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM karma WHERE user = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM friends WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM friends WHERE friendid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM bookmarks WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM invites WHERE inviter = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM peers WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM readposts WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM report WHERE userid = ".sqlesc($arr["id"])." OR usertid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM simpaty WHERE fromuserid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM pollanswers WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM shoutbox WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM ratings WHERE user = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM snatched WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM thanks WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM checkcomm WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);

if (!empty($arr["avatar"])) {
@unlink(ROOT_PATH."pic/avatar/".$arr["avatar"]);
}
@unlink(ROOT_PATH."cache/monitoring_$userid.txt");

if ($arr["enabled"]=="yes")
write_log("Неактивный пользователь $arr[username] был удален системой.","888888","tracker"); 
else 
write_log("Отключенный пользователь $arr[username] был удален системой.","724f4f","tracker"); 

}




} /// расписание




$h = date('i'); // проверяем минуты
if (($h >= 00 )&&($h <= 40)) // расписание минут
{
//отключение предупрежденных пользователей (у тех у кого 5 звезд) 
        $res = sql_query("SELECT id, username, modcomment,num_warned FROM users WHERE num_warned > 4 AND class <= 5 and enabled = 'yes'") or sqlerr(__FILE__,__LINE__); 
        $num = mysql_num_rows($res);         
        while ($arr = mysql_fetch_assoc($res)) {
         $modcom = sqlesc(date("Y-m-d") . " - Отключен системой (".$arr["num_warned"]." => предупреждений) " . "\n". $arr["modcomment"]);
        sql_query("UPDATE users SET enabled = 'no' WHERE id = ".$arr["id"]) or sqlerr(__FILE__, __LINE__); 
        sql_query("UPDATE users SET modcomment = ".$modcom." WHERE id = ".$arr["id"]) or sqlerr(__FILE__, __LINE__); 
        write_log("Пользователь ".$arr["username"]." был отключен системой (".$arr["num_warned"]." предупреждений)","","bans");
        }



//удаление припаркованных пользователей старше
       $secs = 365*86400; // 175 дней (пол года)
       $dt = sqlesc(get_date_time(gmtime() - $secs));
       $maxclass = UC_POWER_USER;
       $res = sql_query("SELECT id,username,avatar FROM users WHERE parked='yes' AND override_class=255 AND status='confirmed' AND class <= $maxclass AND last_access < $dt");
       if (mysql_num_rows($res) > 0) {
       	while ($arr = mysql_fetch_array($res)) {
       		
sql_query("DELETE FROM users WHERE id = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM messages WHERE receiver = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM friends WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM friends WHERE friendid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM bookmarks WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM invites WHERE inviter = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM peers WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM simpaty WHERE fromuserid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM checkcomm WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
		
		$avatar=$arr["avatar"];
		if ($avatar)
		@unlink(ROOT_PATH."pic/avatar/".$avatar);
		@unlink(ROOT_PATH."cache/monitoring_$userid.txt");
		 write_log("Удаление припаркованного пользователя $arr[username] системой.","","tracker");
		}
		}
} /// расписание минутное

$h = date('H'); // проверяем час
if (($h >= 05 )&&($h <= 10)) // расписание на утро
{
/////////// Автоматически отключает без ограниченный рейтинг ///////////
$res = sql_query("SELECT id FROM users WHERE hiderating='yes' AND hideratinguntil <> '0000-00-00 00:00:00' AND hideratinguntil < NOW()") or sqlerr(__FILE__, __LINE__); 
if (mysql_num_rows($res) > 0) 
{
$length = sqlesc(get_date_time());   

$res = sql_query("SELECT id,modcomment FROM users WHERE hiderating = 'yes'  AND hideratinguntil <> '0000-00-00 00:00:00' AND hideratinguntil < $length") or sqlerr(__FILE__,__LINE__);   
while ($arr = mysql_fetch_assoc($res)) {
            $modcomment = htmlspecialchars($arr["modcomment"]);   
            $modcomment = date("Y-m-d") . " - Безграниченный рейтинг отключён системой, время истекло!\n". $modcomment;
            $modcom = sqlesc($modcomment);   
           sql_query("UPDATE users SET hiderating = 'no', hideratinguntil = '0000-00-00 00:00:00', modcomment = $modcom WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);   
            $msg = sqlesc("Безграниченный рейтинг отключён системой, время истекло!\n");
            sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); 
} }
/////////// Автоматически отключает без ограниченный рейтинг ///////////


////////END BIRTHDAY GIFT///////  
$dny=0;
$maxdt = sqlesc(get_date_time(gmtime() - 86400*7));  /// если добавлен неделю назад
$timeday = date("-m-d");
$dt = sqlesc(get_date_time);  /// now

$res2 = sql_query("SELECT id, username, added, class, usercomment FROM users WHERE added < $maxdt AND birthday LIKE '%$timeday%' AND bday_present='no'") or sqlerr(__FILE__, __LINE__); 

while ($arr2 = mysql_fetch_assoc($res2)) {

$username = $arr2["username"]; 
$id = $arr2["id"]; 

$presentclass = $arr2["class"];
if($presentclass <= UC_USER) 
$present = "20.0"; 
elseif($presentclass == UC_POWER_USER) 
$present = "30.0"; 
elseif ($presentclass == UC_VIP) 
$present = "40.0"; 
elseif ($presentclass == UC_UPLOADER) 
$present = "50.0"; 
elseif ($presentclass >= UC_MODERATOR) 
$present = "60.0";

$subject = sqlesc("С Днём рождения");
$msg = sqlesc("Администрация трекера от лица всей команды $SITENAME поздравляет Вас с днём рождения!!! [color=#".get_user_rgbcolor($arr2["class"],$username)."]".$username."[/color], прими этот скромный подарок в $present бонусов от нас. Большое Спасибо за твое внимание к этому трекеру."); 

$modcomment = htmlspecialchars($arr2["modcomment"]);
$usercomment = gmdate("Y-m-d") . " - Подарок - бонусы ($present) на ДР.\n". $usercomment;
$modcom = sqlesc($usercomment);

sql_query("UPDATE users SET bonus = bonus + $present, bday_present = 'yes', usercomment = $modcom WHERE enabled='yes' and id = $arr2[id]") or sqlerr(__FILE__, __LINE__); 
sql_query("INSERT INTO messages (sender, receiver, added, msg, subject,poster) VALUES(0, $arr2[id], $dt, $msg, $subject,0)") or sqlerr(__FILE__, __LINE__);

unset($usercomment,$modcom);

+$dny;
}

if ($dny>0)
@unlink(ROOT_PATH."cache/block-birth.txt");

/////////RESET BIRTHDAY/////////// 
$currentdate = date("Y-m-d", time() + 60);
list($year1, $month1, $day1) = explode('-', $currentdate);
$res4 = sql_query("SELECT * FROM users WHERE enabled='yes' and bday_present ='yes' AND birthday > $currentdate") or sqlerr(__FILE__, __LINE__);
while ($arr4 = mysql_fetch_assoc($res4)) {
$birthday = date($arr4["birthday"]); 
$id = $arr4["id"];
list($year2, $month2, $day2) = explode('-', $birthday); 
if ($month1 > $month2){
sql_query("UPDATE users SET bday_present = 'no' WHERE enabled='yes' and id = $arr4[id]") or sqlerr(__FILE__, __LINE__);
}
}
/////////RESET BIRTHDAY/////////// 
////////END BIRTHDAY GIFT///////  


} /// конец расписания

$h = date('H'); // проверяем час
if (($h >= 06 )&&($h <= 10)) // расписание на утро
{
//Удаляем все прочтенные системные сообщения старше 31 дней 
$secs_system = 31*86400; // Количество дней 
$dt_system = sqlesc(get_date_time(gmtime() - $secs_system)); // Сегодня минус количество дней 
sql_query("DELETE FROM messages WHERE sender = '0' AND unread = 'no' AND added < $dt_system") or sqlerr(__FILE__, __LINE__); 

//Удаляем ВСЕ прочтенные сообщения старше 60 дней 
$secs_all = 60*86400; // Количество дней 
$dt_all = sqlesc(get_date_time(gmtime() - $secs_all)); // Сегодня минус количество дней 
sql_query("DELETE FROM messages WHERE unread = 'no' AND added < $dt_all") or sqlerr(__FILE__, __LINE__);


// удаление неподтвержденных пользователей
	$deadtime = TIMENOW - $signup_timeout;
	$res = sql_query("SELECT username, id FROM users WHERE status = 'pending' AND added < FROM_UNIXTIME($deadtime) AND last_login < FROM_UNIXTIME($deadtime) AND last_access < FROM_UNIXTIME($deadtime)") or sqlerr(__FILE__,__LINE__);
	if (mysql_num_rows($res) > 0) {
		while ($arr = mysql_fetch_array($res)) {
	sql_query("DELETE FROM users WHERE id = ".sqlesc($arr["id"]));
		
		write_log("Пользователь $arr[username] был удален системой (неподтвержденая почта)","","tracker"); 
		}
		}
}


///uploadoffset сколько залил на данный момент а uploaded всего
if ($fixed_bonus=="1") {$fixed=" uploadoffset > 0 and ";}

// обновление бонусов за раздачу (аннонс)
sql_query("UPDATE users SET bonus = bonus + $points_per_cleanup WHERE users.id IN (SELECT userid FROM peers WHERE $fixed seeder = 'yes')") or sqlerr(__FILE__,__LINE__);


// обновление времени сидирования на index.php
if (basename($_SERVER['SCRIPT_FILENAME']) == 'index.php'){
sql_query("UPDATE users SET seed_line = seed_line + $autoclean_interval WHERE id IN (SELECT DISTINCT userid FROM peers WHERE $fixed seeder = 'yes')") or sqlerr(__FILE__, __LINE__);
}




	// снятие истекших предупреждений 
	$now = sqlesc(get_date_time());
	$modcomment = sqlesc(date("Y-m-d") . " - Предупреждение снято системой по таймауту.\n");
	$msg = sqlesc("Ваше предупреждение снято по таймауту. Постарайтесь больше не получать предупреждений и следовать правилам трекера.\n");
	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) SELECT 0, id, $now, $msg, 0 FROM users WHERE warned='yes' AND warneduntil < NOW() AND warneduntil <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);
	sql_query("UPDATE users SET warned='no', num_warned=num_warned-1, warneduntil = '0000-00-00 00:00:00', modcomment = CONCAT($modcomment, modcomment) WHERE warned='yes' AND warneduntil < NOW() AND warneduntil <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);


$h = date('i'); // проверяем минуты
if (($h >= 29 )&&($h <= 40)) // расписание минут
{

sql_query("UPDATE torrents SET viponly ='' WHERE viponly<=".sqlesc(time()))or sqlerr(__FILE__,__LINE__); 

if (is_valid_id($auto_duploader) && !empty($auto_duploader)){

///// понижаем тех аплоадеров которые не залили релизы в течении месяца
    $dt = get_date_time(gmtime() - 86400*14);  /// если добавлен две недели назад
    $dt_p = get_date_time(gmtime() - 86400*$auto_duploader);  /// если посл повышение 3 недели назад
	$res = sql_query("SELECT id,username FROM users WHERE (SELECT max(id) FROM torrents WHERE ".sqlesc($dt)."<=added AND owner=users.id LIMIT 1) IS NULL AND class = ".UC_UPLOADER." AND enabled = 'yes' AND added<".sqlesc($dt)." AND promo_time<".sqlesc($dt_p)) or sqlerr(__FILE__,__LINE__);
 
    while ($arr = mysql_fetch_assoc($res)) {
    $id=$arr["id"];
    $now = sqlesc(get_date_time());
///	$msg = sqlesc("Вы были авто-понижены с ранга [b]Аплоадер[/b] до ранга [b]Пользователь[/b]. Причина: От вас нет новых релизов больше двух месяцев.");
///	$subject = sqlesc("Вы были понижены (нет заливок больше месяца)");
	$modcomment = sqlesc(date("Y-m-d") . " - Понижен до уровня ".$tracker_lang["class_user"]." системой. Причина: Последняя заливка менее 2x месяцев.\n");
///	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject)  VALUES (0,$id,$now,$msg,$id,$subject)") or sqlerr(__FILE__,__LINE__);
   
    
	sql_query("UPDATE users SET class = ".UC_USER.", promo_time=" . sqlesc(get_date_time()).", usercomment = CONCAT(".$modcomment.", usercomment) WHERE class = ".UC_UPLOADER." AND override_class=255 and enabled='yes' AND id=".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
     }
///	sql_query("UPDATE users SET class = ".UC_USER." WHERE class = ".UC_UPLOADER." AND override_class=255 and enabled='no'") or sqlerr(__FILE__,__LINE__);///,enabled='yes'
///// понижаем тех аплоадеров которые не залили релизы в течении месяца
}


// снятие истекших форум бан
	$now = sqlesc(get_date_time());
	$modcomment = sqlesc(date("Y-m-d") . " - Форум бан снят системой по таймауту.\n");
	$msg = sqlesc("Ваш бан на форуме был снят системой по таймауту. Постарайтесь больше не получать таких услуг от администрации и следовать правилам трекера.\n");
	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) SELECT 0, id, $now, $msg, 0 FROM users WHERE forum_com < NOW() AND enabled='yes' AND forum_com <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);
	sql_query("UPDATE users SET forum_com = '0000-00-00 00:00:00', modcomment = CONCAT(".$modcomment.", modcomment) WHERE forum_com < NOW() AND enabled='yes' AND forum_com <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);


// снятие истекших чат банов
	$now = sqlesc(get_date_time());
	$modcomment = sqlesc(date("Y-m-d") . " - Бан на чат снят системой по таймауту.\n");
	$msg = sqlesc("Ваш бан в чате был снят системой по таймауту. Постарайтесь больше не получать таких услуг от администрации и следовать правилам сайта.\n");
	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) SELECT 0, id, $now, $msg, 0 FROM users WHERE shoutbox < NOW() AND enabled='yes' AND shoutbox <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);
	sql_query("UPDATE users SET shoutbox = '0000-00-00 00:00:00', modcomment = CONCAT($modcomment, modcomment) WHERE shoutbox < NOW() AND enabled='yes' AND shoutbox <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);




/*

// автоповышение в опытные юзера
	$limit = 25*1024*1024*1024;
	$minratio = 1.05;
	$maxdt = sqlesc(get_date_time(gmtime() - 86400*28));
	$now = sqlesc(get_date_time());
	$msg = sqlesc("Наши поздравления, вы были авто-повышены до ранга [b]Опытный пользователь[/b].");
	$subject = sqlesc("Вы были повышены");
	$modcomment = sqlesc(date("Y-m-d") . " - Повышен до уровня \"".$tracker_lang["class_power_user"]."\" системой.\n");
	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) SELECT 0, id, $now, $msg, 0, $subject FROM users WHERE class = 0 AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND added < $maxdt AND override_class=255 and enabled='yes'") or sqlerr(__FILE__,__LINE__);
	sql_query("UPDATE users SET class = ".UC_POWER_USER.", modcomment = CONCAT($modcomment, modcomment) WHERE class = ".UC_USER." AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND added < $maxdt AND override_class=255 and enabled='yes'") or sqlerr(__FILE__,__LINE__);


// автопонижение в пользователи
	$minratio = 0.95;
	$now = sqlesc(get_date_time());
	$msg = sqlesc("Вы были авто-понижены с ранга [b]Опытный пользователь[/b] до ранга [b]Пользователь[/b] потому, что ваш рейтинг упал ниже [b]{$minratio}[/b].");
	$subject = sqlesc("Вы были понижены");
	$modcomment = sqlesc(date("Y-m-d") . " - Понижен до уровня \"".$tracker_lang["class_user"]."\" системой.\n");
	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) SELECT 0, id, $now, $msg, 0, $subject FROM users WHERE class = 1 AND uploaded / downloaded < $minratio AND override_class=255 and enabled='yes'") or sqlerr(__FILE__,__LINE__);
	sql_query("UPDATE users SET class = ".UC_USER.", modcomment = CONCAT($modcomment, modcomment) WHERE class = ".UC_POWER_USER." AND uploaded / downloaded < $minratio AND override_class=255 and enabled='yes'") or sqlerr(__FILE__,__LINE__);

*/



}//// конец расписания


// удаление старых торрентов (если используется TTL см в config)
	if ($use_ttl) {
		$dt = sqlesc(get_date_time(gmtime() - ($ttl_days * 86400)));
		$res = sql_query("SELECT id, name FROM torrents WHERE added < $dt") or sqlerr(__FILE__,__LINE__);
		while ($arr = mysql_fetch_assoc($res)) {
	
			@unlink(ROOT_PATH."torrents/".$arr["id"].".torrent");
			sql_query("DELETE FROM torrents WHERE id=$arr[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM snatched WHERE torrent=$arr[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM peers WHERE torrent=$arr[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM comments WHERE torrent=$arr[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM files WHERE torrent=$arr[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM ratings WHERE torrent=$arr[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM checkcomm WHERE checkid=$arr[id] AND torrent = 1") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM bookmarks WHERE torrentid=$arr[id]") or sqlerr(__FILE__,__LINE__);
			write_log("Торрент $arr[id] ($arr[name]) был удален системой (старше чем $ttl_days дней)","","torrent");
		}
	}

// очистка сессии
$dt = get_date_time(gmtime() - 1200);
sql_query("DELETE FROM sessions WHERE time < ".sqlesc($dt)) or sqlerr(__FILE__,__LINE__);


/*
$dsession=mysql_affected_rows();

sql_query("INSERT INTO avps (arg, value_u,value_s,value_i) VALUES ('delsession','1268653902','Всего сессий удалено','$dsession')");

if (!mysql_insert_id()){
sql_query("UPDATE avps SET value_i=value_i+'$dsession' WHERE arg='delsession'") or sqlerr(__FILE__,__LINE__);
}
*/



$h = date('H'); // проверяем час
if (($h >= 00 )&&($h <= 06)) // расписание
{


//// удаление выполненых с битыми торрентами
$res = sql_query("SELECT name, id FROM off_reqs WHERE perform='yes' AND (SELECT id FROM torrents WHERE torrents.id=off_reqs.ful_id) IS NULL") or sqlerr(__FILE__,__LINE__);
while ($row = mysql_fetch_array($res)) {

sql_query("DELETE FROM checkcomm WHERE checkid = ".sqlesc($row["id"])." AND offer = 1") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM off_reqs WHERE id = ".sqlesc($row["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM comments WHERE offer = ".sqlesc($row["id"])) or sqlerr(__FILE__,__LINE__);

write_log("Запрос ".$row["id"]." (".htmlspecialchars($row["name"]).") был удален системой: Мертвяк, выполнен (торрент не найден)\n","52A77C","torrent"); 
}
//// удаление выполненых с битыми торрентами


//// удаление истекших запросов
$res = sql_query("SELECT name, id FROM off_reqs WHERE perform='yes' AND data_perf < DATE_SUB(NOW(), INTERVAL 62 DAY)");
while ($row = mysql_fetch_array($res)) {

sql_query("DELETE FROM checkcomm WHERE checkid = ".sqlesc($row["id"])." AND offer = 1") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM off_reqs WHERE id = ".sqlesc($row["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM comments WHERE offer = ".sqlesc($row["id"])) or sqlerr(__FILE__,__LINE__);

write_log("Запрос ".$row["id"]." (".htmlspecialchars($row["name"]).") был удален системой: Мертвяк, выполнен\n","52A77C","torrent");  
}
//// удаление истекших запросов



// снятие банов по ip
if ($use_ipbans==1){

$ban_sql=sql_query("SELECT id,comment,first,last,bans_time FROM bans WHERE bans_time<>'0000-00-00 00:00:00' AND bans_time < NOW()") or sqlerr(__FILE__,__LINE__);	
while ($ban_res = mysql_fetch_assoc($ban_sql)){
$ban_id=$ban_res["id"];
$first = long2ip($ban_res["first"]);
$last = long2ip($ban_res["last"]);
$comment = "Причина: ".format_comment($ban_res["comment"])." до ".$ban_res["bans_time"]."";
write_log("Бан IP адреса номер $ban_id (".($first == $last? $first:"$first - $last").") был убран системой. $comment.","704FFD","bans");
sql_query("DELETE FROM bans WHERE bans_time<>'0000-00-00 00:00:00' AND bans_time < NOW()") or sqlerr(__FILE__,__LINE__);
}
@unlink(ROOT_PATH."cache/bans_first_last.txt");
 /// sql_query("INSERT INTO sitelog (added, color, txt, type) SELECT $now, E5E5E5, bans FROM bans WHERE forum_com < NOW() AND enabled='yes' AND forum_com <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);	
}
// снятие банов по ip



// очистка чата за 31 дней
$secons_time = 31 * 86400; /// количество дней 
$clear_day = time() - $secons_time;
sql_query("DELETE FROM shoutbox WHERE date < ".sqlesc($clear_day)) or sqlerr(__FILE__,__LINE__);


    if ($maxlogin=="1") {
    // удаляем старые попытки входов
    $secs = 7*86400; // старше 7 дней
    $dt = sqlesc(get_date_time(gmtime() - $secs));
    sql_query("DELETE FROM loginattempts WHERE banned='no' AND added < $dt") or sqlerr(__FILE__,__LINE__);
    }
    

    
    
sql_query("DELETE FROM messages WHERE saved='no' and poster='0' and receiver not in (SELECT id FROM users WHERE id=messages.receiver)") or sqlerr(__FILE__, __LINE__);

sql_query("DELETE FROM messages WHERE saved='no' and poster='0' and sender not in (SELECT id FROM users WHERE id=messages.sender)") or sqlerr(__FILE__, __LINE__);
///$delete_receiv=mysql_affected_rows();


}

//sql_query("UPDATE torrents SET free='yes' WHERE size>=1073741824 ") or sqlerr(__FILE__, __LINE__);


/*


// обновление времени сидирования на index.php
if (basename($_SERVER['SCRIPT_FILENAME']) <> 'index.php'){
sql_query('UPDATE tags AS t SET t.howmuch = (SELECT COUNT(*) FROM torrents AS ts WHERE ts.tags LIKE CONCAT(\'%\', t.name, \'%\'))')or sqlerr(__FILE__,__LINE__); // AND ts.category = t.category

} else {
sql_query('UPDATE tags AS t SET t.howmuch = (SELECT COUNT(*) FROM torrents AS ts WHERE name<>"" AND ts.tags LIKE CONCAT(\'%\', t.name, \'%\')) ORDER BY RAND() LIMIT 30')or sqlerr(__FILE__,__LINE__); // AND ts.category = t.category
}


*/


$h = date('H'); // проверяем час
if (($h >= 13)&&($h <= 14)) // расписание
{


$now = time();
$res = sql_query("SELECT value_u FROM avps WHERE arg = 'cleancache'");
$row = mysql_fetch_array($res);
$row_time=$row["value_u"];
$now_2day=time()+86400*3;
$dt = get_date_time(gmtime());


if ($row_time<$now){

$dh = opendir(ROOT_PATH.'cache/');
$num=0;
while ($file = readdir($dh)) :
if (preg_match('/^(.+)\.$/si', $file, $matches))
$file = $matches[1];
if (strlen($file) >= 30){
@unlink(ROOT_PATH."cache/$file"); //print "cache/$file <br>$num";
$num++;
}
endwhile;
closedir($dh);

if (!$row_time && $num<>0) {
sql_query("INSERT INTO avps (arg, value_u,value_i,value_s) VALUES ('cleancache',$now_2day,'$num','$dt')");
} elseif ($num<>0 && $row_time) {
sql_query("UPDATE avps SET value_u='$now_2day',value_i='$num', value_s='$dt' WHERE arg='cleancache'") or sqlerr(__FILE__,__LINE__);
}
	}
	}
	
	
	
	
	

///////// оптимизируем таблицы

$h = date('H'); // проверяем час
if (($h >= 01)&&($h <= 03)) // расписание
{

$now = time();
$res = sql_query("SELECT value_u FROM avps WHERE arg = 'sql_optimize'");
$row = mysql_fetch_array($res);
$row_time=$row["value_u"];
$now_2day=time()+(86400*7); /// раз в неделю
$dt = get_date_time(gmtime());

if ($row_time<=$now){
global $prefix;

include_once(ROOT_PATH."include/passwords.php"); 

$result = sql_query("SHOW TABLE STATUS FROM ".$mysql_db_fix_by_imperator."");
$numo=0;
while ($row = mysql_fetch_array($result)) {
$free = $row['Data_free'] ? $row['Data_free'] : 0;
 if ($free){
sql_query("OPTIMIZE TABLE ".$row["Name"]."") or sqlerr(__FILE__,__LINE__);
$numo++;
}
sql_query("REPAIR TABLE ".$row["Name"]."") or sqlerr(__FILE__,__LINE__);

}
/*
		$result = sql_query("SHOW TABLE STATUS FROM ".$mysql_db_fix_by_imperator."");
		while ($row = mysql_fetch_array($result)) {
			$total = $row['Data_length'] + $row['Index_length'];
			$totaltotal += $total;
			$i++;
			$rresult = sql_query("REPAIR TABLE ".$row[0]."");
		}
*/

if (empty($row_time) && $numo<>0) {
sql_query("INSERT INTO avps (arg, value_u,value_i,value_s) VALUES ('sql_optimize',$now_2day,'$numo','$dt')");
} elseif ($numo<>0 && !empty($row_time)) {
sql_query("UPDATE avps SET value_u='$now_2day',value_i='$numo', value_s='$dt' WHERE arg='sql_optimize'") or sqlerr(__FILE__,__LINE__);
}
}

}

///////// оптимизируем таблицы













/*
if (date('d')=="31" && date('m')=="12") // расписание на позд вечер
{
$res = sql_query("SELECT id,usercomment,gender,username FROM users WHERE enabled='yes' AND status = 'confirmed' LIMIT 100") or sqlerr(__FILE__, __LINE__); 
// $id=0;
while ($arr = mysql_fetch_assoc($res)) {

$modcomment = htmlspecialchars($arr["usercomment"]);   
$user = htmlspecialchars($arr["username"]);   
///33285996544
if (!stristr($modcomment,"Подарок от Снегурочки")!==false || empty($modcomment)){
$modcomment = date("Y-m-d") . " - Подарок от Снегурочки (31GB).\n". $modcomment;
$dt = sqlesc(get_date_time(gmtime()));
$modcom = sqlesc($modcomment);   
sql_query("UPDATE users SET uploaded = uploaded+'33285996544', usercomment = $modcom WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);   
$subject = sqlesc("C Новым Годом!!! Вам подарочек от меня.\n");
$msg=sqlesc("Уважаем".($arr["gender"]=="2"? "ая":"ый")." $user. В честь Нового Года, Администрация сайта Muz-Tracker дарит вам маленький, но весьма существенный подарок - 31 гигабайт аплоада (отдачи) к вашему общему рейтингу. \n\nСпасибо за внимание к этому сайту, Ваша Новогодняя Снегурочка [color=Magenta]ViKa[/color].\n");
sql_query("INSERT INTO messages (sender, receiver, added, subject,msg, poster) VALUES(92, $arr[id], $dt, $subject,$msg, 0)") or sqlerr(__FILE__, __LINE__); 
//++$id;
}
unset($modcomment);
}
//echo $id;
}
*/

@unlink(ROOT_PATH."cache/browse_genrelist.txt");





$maxdt = get_date_time(gmtime() - 62*86400);  /// два месяца

$sq=sql_query("SELECT id,username,email,last_login,class,ip,(SELECT COUNT(*) FROM messages WHERE receiver=users.id AND location=1 AND unread='yes') AS unread FROM users WHERE last_access<".sqlesc($maxdt)." AND enabled='yes' AND status='confirmed' AND notif_access='0000-00-00 00:00:00' AND class<".sqlesc(UC_SYSOP)." LIMIT 1") or sqlerr(__FILE__, __LINE__); 
while ($r_bot=mysql_fetch_array($sq)){
$now_time = get_date_time();  /// сейчас время
$usern=$r_bot["username"];
$unred=(!empty($r_bot["unread"]) ? "С того времени, у вас появилось около ".number_format($r_bot["unread"])." новых непрочитанных сообщений.":"");
$last_login=$r_bot["last_login"];
$email=$r_bot["email"];
$ip=$r_bot["ip"];
$class=get_user_class_name($r_bot["class"]);
$id=$r_bot["id"];

$body = <<<EOD
Здравствуйте $usern. Ваш аккаунт на сайте $SITENAME, находится в неактивном режиме (нет активности).

Последняя активность: $last_login
Последний ip адрес: $ip
$unred

------------------------------------------------
Логин для входа: $usern
Зарегистрированная почта: $email
Права на сайте: $class
Страничка с вашем именем доступна по адресу: $DEFAULTBASEURL/userdetails.php?id=$id

------------------------------------------------
Войти со своими данными: $DEFAULTBASEURL/login.php
Восстановить пароль: $DEFAULTBASEURL/recover.php
Новая регистрация: $DEFAULTBASEURL/signup.php

Если есть проблемы с паролем или входом на сайт, свяжитесь с администратором или техподдержкой сайта.
--> время отправка письма - $now_time
---> С ув. Система сайта $SITENAME
EOD;
///echo $body;
if (sent_mail($email,$SITENAME,$SITEEMAIL,"Уведомление о неактивности аккаунта на $SITENAME",$body,false))
sql_query("UPDATE users SET notif_access=".sqlesc($now_time)." WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
}











$h = date('H'); // проверяем час
if (($h >= 21)&&($h <= 22)) // расписание
{
	
$res = sql_query("SELECT value_u FROM avps WHERE arg = 'rand_price'") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$row_time=$row["value_u"];
$now_2day=time()+(86400); /// раз в день
$now = time();
$dt = get_date_time(gmtime());

if ($row_time<=$now){

$maxdt = sqlesc(get_date_time(gmtime() - 86400*14));  /// если пос доступ  менее 2 недели назад
$numo=0;
$sq=sql_query("SELECT id,username,gender FROM users WHERE enabled='yes' AND status='confirmed' AND parked='no' AND  id<>'92' AND last_access >= $maxdt ORDER BY RAND() LIMIT 1") or sqlerr(__FILE__, __LINE__);

while ($r_rand=mysql_fetch_array($sq)){

$id=$r_rand["id"];
$now = sqlesc(get_date_time());

$subject = sqlesc("Подарок от системы (случайный)");

$upload = (10*1073741824); ///10 гб
$ten = mksize($upload);

$msg = sqlesc(($r_rand["gender"]=="2"? "Уважаемая ":"Уважаемый ")."[b]".$r_rand["username"]."[/b], методом случайного перебора, система выбрала вас, соответственно подарок: +$ten к заливке (аплоад).\n Система сайта [u]".$SITENAME."[/u].");

$usercomment = sqlesc(date("Y-m-d") . " - Случайный подарок от системы ($ten).\n");

sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUE (0, $id, $now, $msg, 0, $subject)") or sqlerr(__FILE__,__LINE__);

sql_query("UPDATE users SET usercomment = CONCAT_WS('',$usercomment, usercomment), unread=unread+1,uploaded=uploaded+".$upload." WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__); 
++$numo;
}

if (empty($row_time)) {
sql_query("INSERT INTO avps (arg, value_u,value_i,value_s) VALUES ('rand_price',$now_2day,'$numo','$numo')");
} elseif (!empty($row_time)) {
sql_query("UPDATE avps SET value_u='$now_2day',value_i='$numo', value_s='$numo' WHERE arg='rand_price'");
}
}


}



unset($h);
}
?>