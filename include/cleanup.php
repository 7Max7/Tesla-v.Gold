<?
if(!defined('IN_TRACKER'))
  die('� ���� ��� �� ���?!');


function docleanup() {
global $torrent_dir, $signup_timeout, $max_dead_torrent_time, $use_ttl, $autoclean_interval, $points_per_cleanup, $ttl_days, $tracker_lang, $SITENAME, $dead_torrent_time, $use_dead_torrent, $maxlogin, $fixed_bonus, $use_ipbans, $readpost_expiry, $announce_urls,$auto_duploader,$DEFAULTBASEURL;


@set_time_limit(0);
@ignore_user_abort(1);


//////////////// �������� ���� ������ �� �����

$now = time();
$res = sql_query("SELECT value_u FROM avps WHERE arg = 'che�k_torrent'") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$row_time=$row["value_u"];
$now_2day=time()+(86400*88888888); /// ��� � ��� ���
$dt = get_date_time(gmtime());

if ($row_time<=$now){

//// ��� ���������
	do {
			
		$res = sql_query("SELECT id FROM torrents") or sqlerr(__FILE__,__LINE__);
		$ar = array();
		while ($row = mysql_fetch_array($res)) {
			$id = $row[0];
			$ar[$id] = 1;
		}

        //// ���� ��� ��������� ������� �������
		if (!count($ar))
			break;

       //// ���� ���������� ������� ����� �  ���������� ������� �������
		$dp = @opendir($torrent_dir);
		if (!$dp)
			break;

        ///// ������� ������
		$ar2 = array();
		/// ����� ���� ���������
		while (($file = readdir($dp)) !== false) {
			
             /// ������ �� �������� ��� ���������� �� save_as - > ��������� $id[up] ��� $id[ta] � .torrent
             /// ����� �������� �������� ����� ������ � ��������
           // $file=preg_replace("/[up]+/","", $file);
         ///   $file=preg_replace("/[ta]+/","", $file);

			//// �������� ����� .torrent (PS ������ ����� ����� �� �����)
			if (!preg_match('/^(\d+)\.torrent$/', $file, $m))
				continue;


            /// ����������� ��������
			$id = $m[1];
			$ar2[$id] = 1;

			//// ���� ���� ������ ��� ���� �� �������� ����� - ����������
			if (isset($ar[$id]) && $ar[$id])
				continue;
		//	if (stristr($file,"[up]") !== false || stristr($file,"[ta]") !== false)
		//		continue;
			
			$ff = $torrent_dir . "/$file";
        @unlink($ff);
        ///ROOT_PATH.

		}
		/// ��������� ����� ���������
		closedir($dp);
        //// ����������� ������� ���� ��� ������ � �����
		if (!count($ar2))
			break;
        ///// ������� ������ delids
		$delids = array();
		/// ����������� ������ ������� ������� � �������� �� ������ �������� $ar || $ar2
		foreach (array_keys($ar) as $k) {
			
			//// ���� ���� ������ 2 ������� - ����������
			if (isset($ar2[$k]) && $ar2[$k])
				continue;
			/// ��������� $k �������� ������� ������� � �������� ������� $delids
			$delids[] = $k;
			/// ������� �������� � ������� ������� ������� 
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

/// ��������� � 

if (empty($row_time)) {
sql_query("INSERT INTO avps (arg, value_u,value_i,value_s) VALUES ('che�k_torrent',$now_2day,'$numo','$dt')");
} elseif (!empty($row_time)) {
sql_query("UPDATE avps SET value_u='$now_2day',value_i='$numo', value_s='$dt' WHERE arg='che�k_torrent'");
}
}
//////////////// �������� ���� ������ �� �����



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
		//// ���������
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
//echo "$id - leechers=$seeders[leechers] � seeders=$seeders[seeders]<br>";

$update[] = "leechers = ".(!empty($seeders["leechers"])? $seeders["leechers"]:"0");
$update[] = "seeders = ".(!empty($seeders["seeders"])? $seeders["seeders"]:"0");
$update[] = "checkpeers = ".sqlesc(get_date_time());
sql_query("UPDATE torrents SET ".implode(", ", $update)." WHERE id=".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
unset($update);
}
	
	
	
	
	

$h = date('H'); // ��������� ���
if (($h >= 21 )&&($h <= 23)) // ���������� �� ���� �����
{
	

//// �������� ������ ��������� 
if ($use_dead_torrent){
		$dt2 = sqlesc(get_date_time(gmtime() - ($dead_torrent_time * 86400)));
		$res3 = sql_query("SELECT id, name,image1,tags,last_action FROM torrents WHERE seeders = '0' and leechers='0' and times_completed='0' and f_seeders='0' and f_leechers='0' and last_action <= $dt2") or sqlerr(__FILE__,__LINE__);
		while ($arr3 = mysql_fetch_assoc($res3)) {

         $time_del="(���. ".get_elapsed_time(sql_timestamp_to_unix_timestamp($arr3["last_action"]))." �����)";

			@unlink(ROOT_PATH."torrents/".$arr3["id"].".torrent");
			sql_query("DELETE FROM torrents WHERE id=$arr3[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM snatched WHERE torrent=$arr3[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM peers WHERE torrent=$arr3[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM comments WHERE torrent=$arr3[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM files WHERE torrent=$arr3[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM ratings WHERE torrent=$arr3[id]") or sqlerr(__FILE__,__LINE__);
			sql_query("DELETE FROM checkcomm WHERE checkid=$arr3[id] AND torrent = 1") or sqlerr(__FILE__,__LINE__);
         	sql_query("DELETE FROM bookmarks WHERE torrentid=$arr3[id]") or sqlerr(__FILE__,__LINE__);
  	
			write_log("������� $arr3[id] ($arr3[name]) ������ ��������: ��� ����� ����� ������ ".$time_del."","","torrent");

             if (!preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $arr3["image1"]) && !empty($arr3["image1"]))
            @unlink(ROOT_PATH."torrents/images/".$arr3["image1"]);	   	

		
		$tags = explode(",", $arr3["tags"]);

foreach ($tags as $tag) {
		@sql_query("UPDATE tags SET howmuch=howmuch-1 WHERE name LIKE ".sqlesc($tag)) or sqlerr(__FILE__, __LINE__);
	}
	}
}
}/// ����������

$h = date('H'); // ��������� ���
if (($h >= 13 )&&($h <= 18)) // ���������� �� ����
{

// ��� ����� ���������� (�� ���� �� �������� ��������), ��� 7Max7
	    $secs = 276*86400;
		$dt = sqlesc(get_date_time(gmtime() - $secs));
		$maxclass = UC_POWER_USER;
		$res = sql_query("SELECT id,username, email,(SELECT id FROM bannedemails WHERE email=users.email) AS useremail FROM users WHERE enabled='no' AND class <= $maxclass AND last_access < $dt") or sqlerr(__FILE__,__LINE__);
		while ($arr = mysql_fetch_assoc($res)) 
		if (!$arr["useremail"]) {
		$USER = sqlesc("92"); // ViKa
		$comment="��� �� ���� �� �������� ($arr[username])";
		$email= $arr["email"];

        sql_query("INSERT INTO bannedemails (added, addedby, comment, email) VALUES(".sqlesc(get_date_time()).", $USER, ".sqlesc($comment).", ".sqlesc($email).")") ; 

         write_log("����������� ������� $arr[username] ($arr[email]) ��� ������� �� ����� ��������.","","bans");
		}



//�������� ���������� ������������� ������
$secs = 365*86400; // 1 ���
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
write_log("���������� ������������ $arr[username] ��� ������ ��������.","888888","tracker"); 
else 
write_log("����������� ������������ $arr[username] ��� ������ ��������.","724f4f","tracker"); 

}




} /// ����������




$h = date('i'); // ��������� ������
if (($h >= 00 )&&($h <= 40)) // ���������� �����
{
//���������� ��������������� ������������� (� ��� � ���� 5 �����) 
        $res = sql_query("SELECT id, username, modcomment,num_warned FROM users WHERE num_warned > 4 AND class <= 5 and enabled = 'yes'") or sqlerr(__FILE__,__LINE__); 
        $num = mysql_num_rows($res);         
        while ($arr = mysql_fetch_assoc($res)) {
         $modcom = sqlesc(date("Y-m-d") . " - �������� �������� (".$arr["num_warned"]." => ��������������) " . "\n". $arr["modcomment"]);
        sql_query("UPDATE users SET enabled = 'no' WHERE id = ".$arr["id"]) or sqlerr(__FILE__, __LINE__); 
        sql_query("UPDATE users SET modcomment = ".$modcom." WHERE id = ".$arr["id"]) or sqlerr(__FILE__, __LINE__); 
        write_log("������������ ".$arr["username"]." ��� �������� �������� (".$arr["num_warned"]." ��������������)","","bans");
        }



//�������� �������������� ������������� ������
       $secs = 365*86400; // 175 ���� (��� ����)
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
		 write_log("�������� ��������������� ������������ $arr[username] ��������.","","tracker");
		}
		}
} /// ���������� ��������

$h = date('H'); // ��������� ���
if (($h >= 05 )&&($h <= 10)) // ���������� �� ����
{
/////////// ������������� ��������� ��� ������������ ������� ///////////
$res = sql_query("SELECT id FROM users WHERE hiderating='yes' AND hideratinguntil <> '0000-00-00 00:00:00' AND hideratinguntil < NOW()") or sqlerr(__FILE__, __LINE__); 
if (mysql_num_rows($res) > 0) 
{
$length = sqlesc(get_date_time());   

$res = sql_query("SELECT id,modcomment FROM users WHERE hiderating = 'yes'  AND hideratinguntil <> '0000-00-00 00:00:00' AND hideratinguntil < $length") or sqlerr(__FILE__,__LINE__);   
while ($arr = mysql_fetch_assoc($res)) {
            $modcomment = htmlspecialchars($arr["modcomment"]);   
            $modcomment = date("Y-m-d") . " - �������������� ������� �������� ��������, ����� �������!\n". $modcomment;
            $modcom = sqlesc($modcomment);   
           sql_query("UPDATE users SET hiderating = 'no', hideratinguntil = '0000-00-00 00:00:00', modcomment = $modcom WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);   
            $msg = sqlesc("�������������� ������� �������� ��������, ����� �������!\n");
            sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); 
} }
/////////// ������������� ��������� ��� ������������ ������� ///////////


////////END BIRTHDAY GIFT///////  
$dny=0;
$maxdt = sqlesc(get_date_time(gmtime() - 86400*7));  /// ���� �������� ������ �����
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

$subject = sqlesc("� ��� ��������");
$msg = sqlesc("������������� ������� �� ���� ���� ������� $SITENAME ����������� ��� � ��� ��������!!! [color=#".get_user_rgbcolor($arr2["class"],$username)."]".$username."[/color], ����� ���� �������� ������� � $present ������� �� ���. ������� ������� �� ���� �������� � ����� �������."); 

$modcomment = htmlspecialchars($arr2["modcomment"]);
$usercomment = gmdate("Y-m-d") . " - ������� - ������ ($present) �� ��.\n". $usercomment;
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


} /// ����� ����������

$h = date('H'); // ��������� ���
if (($h >= 06 )&&($h <= 10)) // ���������� �� ����
{
//������� ��� ���������� ��������� ��������� ������ 31 ���� 
$secs_system = 31*86400; // ���������� ���� 
$dt_system = sqlesc(get_date_time(gmtime() - $secs_system)); // ������� ����� ���������� ���� 
sql_query("DELETE FROM messages WHERE sender = '0' AND unread = 'no' AND added < $dt_system") or sqlerr(__FILE__, __LINE__); 

//������� ��� ���������� ��������� ������ 60 ���� 
$secs_all = 60*86400; // ���������� ���� 
$dt_all = sqlesc(get_date_time(gmtime() - $secs_all)); // ������� ����� ���������� ���� 
sql_query("DELETE FROM messages WHERE unread = 'no' AND added < $dt_all") or sqlerr(__FILE__, __LINE__);


// �������� ���������������� �������������
	$deadtime = TIMENOW - $signup_timeout;
	$res = sql_query("SELECT username, id FROM users WHERE status = 'pending' AND added < FROM_UNIXTIME($deadtime) AND last_login < FROM_UNIXTIME($deadtime) AND last_access < FROM_UNIXTIME($deadtime)") or sqlerr(__FILE__,__LINE__);
	if (mysql_num_rows($res) > 0) {
		while ($arr = mysql_fetch_array($res)) {
	sql_query("DELETE FROM users WHERE id = ".sqlesc($arr["id"]));
		
		write_log("������������ $arr[username] ��� ������ �������� (��������������� �����)","","tracker"); 
		}
		}
}


///uploadoffset ������� ����� �� ������ ������ � uploaded �����
if ($fixed_bonus=="1") {$fixed=" uploadoffset > 0 and ";}

// ���������� ������� �� ������� (������)
sql_query("UPDATE users SET bonus = bonus + $points_per_cleanup WHERE users.id IN (SELECT userid FROM peers WHERE $fixed seeder = 'yes')") or sqlerr(__FILE__,__LINE__);


// ���������� ������� ����������� �� index.php
if (basename($_SERVER['SCRIPT_FILENAME']) == 'index.php'){
sql_query("UPDATE users SET seed_line = seed_line + $autoclean_interval WHERE id IN (SELECT DISTINCT userid FROM peers WHERE $fixed seeder = 'yes')") or sqlerr(__FILE__, __LINE__);
}




	// ������ �������� �������������� 
	$now = sqlesc(get_date_time());
	$modcomment = sqlesc(date("Y-m-d") . " - �������������� ����� �������� �� ��������.\n");
	$msg = sqlesc("���� �������������� ����� �� ��������. ������������ ������ �� �������� �������������� � ��������� �������� �������.\n");
	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) SELECT 0, id, $now, $msg, 0 FROM users WHERE warned='yes' AND warneduntil < NOW() AND warneduntil <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);
	sql_query("UPDATE users SET warned='no', num_warned=num_warned-1, warneduntil = '0000-00-00 00:00:00', modcomment = CONCAT($modcomment, modcomment) WHERE warned='yes' AND warneduntil < NOW() AND warneduntil <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);


$h = date('i'); // ��������� ������
if (($h >= 29 )&&($h <= 40)) // ���������� �����
{

sql_query("UPDATE torrents SET viponly ='' WHERE viponly<=".sqlesc(time()))or sqlerr(__FILE__,__LINE__); 

if (is_valid_id($auto_duploader) && !empty($auto_duploader)){

///// �������� ��� ���������� ������� �� ������ ������ � ������� ������
    $dt = get_date_time(gmtime() - 86400*14);  /// ���� �������� ��� ������ �����
    $dt_p = get_date_time(gmtime() - 86400*$auto_duploader);  /// ���� ���� ��������� 3 ������ �����
	$res = sql_query("SELECT id,username FROM users WHERE (SELECT max(id) FROM torrents WHERE ".sqlesc($dt)."<=added AND owner=users.id LIMIT 1) IS NULL AND class = ".UC_UPLOADER." AND enabled = 'yes' AND added<".sqlesc($dt)." AND promo_time<".sqlesc($dt_p)) or sqlerr(__FILE__,__LINE__);
 
    while ($arr = mysql_fetch_assoc($res)) {
    $id=$arr["id"];
    $now = sqlesc(get_date_time());
///	$msg = sqlesc("�� ���� ����-�������� � ����� [b]��������[/b] �� ����� [b]������������[/b]. �������: �� ��� ��� ����� ������� ������ ���� �������.");
///	$subject = sqlesc("�� ���� �������� (��� ������� ������ ������)");
	$modcomment = sqlesc(date("Y-m-d") . " - ������� �� ������ ".$tracker_lang["class_user"]." ��������. �������: ��������� ������� ����� 2x �������.\n");
///	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject)  VALUES (0,$id,$now,$msg,$id,$subject)") or sqlerr(__FILE__,__LINE__);
   
    
	sql_query("UPDATE users SET class = ".UC_USER.", promo_time=" . sqlesc(get_date_time()).", usercomment = CONCAT(".$modcomment.", usercomment) WHERE class = ".UC_UPLOADER." AND override_class=255 and enabled='yes' AND id=".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
     }
///	sql_query("UPDATE users SET class = ".UC_USER." WHERE class = ".UC_UPLOADER." AND override_class=255 and enabled='no'") or sqlerr(__FILE__,__LINE__);///,enabled='yes'
///// �������� ��� ���������� ������� �� ������ ������ � ������� ������
}


// ������ �������� ����� ���
	$now = sqlesc(get_date_time());
	$modcomment = sqlesc(date("Y-m-d") . " - ����� ��� ���� �������� �� ��������.\n");
	$msg = sqlesc("��� ��� �� ������ ��� ���� �������� �� ��������. ������������ ������ �� �������� ����� ����� �� ������������� � ��������� �������� �������.\n");
	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) SELECT 0, id, $now, $msg, 0 FROM users WHERE forum_com < NOW() AND enabled='yes' AND forum_com <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);
	sql_query("UPDATE users SET forum_com = '0000-00-00 00:00:00', modcomment = CONCAT(".$modcomment.", modcomment) WHERE forum_com < NOW() AND enabled='yes' AND forum_com <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);


// ������ �������� ��� �����
	$now = sqlesc(get_date_time());
	$modcomment = sqlesc(date("Y-m-d") . " - ��� �� ��� ���� �������� �� ��������.\n");
	$msg = sqlesc("��� ��� � ���� ��� ���� �������� �� ��������. ������������ ������ �� �������� ����� ����� �� ������������� � ��������� �������� �����.\n");
	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) SELECT 0, id, $now, $msg, 0 FROM users WHERE shoutbox < NOW() AND enabled='yes' AND shoutbox <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);
	sql_query("UPDATE users SET shoutbox = '0000-00-00 00:00:00', modcomment = CONCAT($modcomment, modcomment) WHERE shoutbox < NOW() AND enabled='yes' AND shoutbox <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);




/*

// ������������� � ������� �����
	$limit = 25*1024*1024*1024;
	$minratio = 1.05;
	$maxdt = sqlesc(get_date_time(gmtime() - 86400*28));
	$now = sqlesc(get_date_time());
	$msg = sqlesc("���� ������������, �� ���� ����-�������� �� ����� [b]������� ������������[/b].");
	$subject = sqlesc("�� ���� ��������");
	$modcomment = sqlesc(date("Y-m-d") . " - ������� �� ������ \"".$tracker_lang["class_power_user"]."\" ��������.\n");
	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) SELECT 0, id, $now, $msg, 0, $subject FROM users WHERE class = 0 AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND added < $maxdt AND override_class=255 and enabled='yes'") or sqlerr(__FILE__,__LINE__);
	sql_query("UPDATE users SET class = ".UC_POWER_USER.", modcomment = CONCAT($modcomment, modcomment) WHERE class = ".UC_USER." AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND added < $maxdt AND override_class=255 and enabled='yes'") or sqlerr(__FILE__,__LINE__);


// ������������� � ������������
	$minratio = 0.95;
	$now = sqlesc(get_date_time());
	$msg = sqlesc("�� ���� ����-�������� � ����� [b]������� ������������[/b] �� ����� [b]������������[/b] ������, ��� ��� ������� ���� ���� [b]{$minratio}[/b].");
	$subject = sqlesc("�� ���� ��������");
	$modcomment = sqlesc(date("Y-m-d") . " - ������� �� ������ \"".$tracker_lang["class_user"]."\" ��������.\n");
	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) SELECT 0, id, $now, $msg, 0, $subject FROM users WHERE class = 1 AND uploaded / downloaded < $minratio AND override_class=255 and enabled='yes'") or sqlerr(__FILE__,__LINE__);
	sql_query("UPDATE users SET class = ".UC_USER.", modcomment = CONCAT($modcomment, modcomment) WHERE class = ".UC_POWER_USER." AND uploaded / downloaded < $minratio AND override_class=255 and enabled='yes'") or sqlerr(__FILE__,__LINE__);

*/



}//// ����� ����������


// �������� ������ ��������� (���� ������������ TTL �� � config)
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
			write_log("������� $arr[id] ($arr[name]) ��� ������ �������� (������ ��� $ttl_days ����)","","torrent");
		}
	}

// ������� ������
$dt = get_date_time(gmtime() - 1200);
sql_query("DELETE FROM sessions WHERE time < ".sqlesc($dt)) or sqlerr(__FILE__,__LINE__);


/*
$dsession=mysql_affected_rows();

sql_query("INSERT INTO avps (arg, value_u,value_s,value_i) VALUES ('delsession','1268653902','����� ������ �������','$dsession')");

if (!mysql_insert_id()){
sql_query("UPDATE avps SET value_i=value_i+'$dsession' WHERE arg='delsession'") or sqlerr(__FILE__,__LINE__);
}
*/



$h = date('H'); // ��������� ���
if (($h >= 00 )&&($h <= 06)) // ����������
{


//// �������� ���������� � ������ ����������
$res = sql_query("SELECT name, id FROM off_reqs WHERE perform='yes' AND (SELECT id FROM torrents WHERE torrents.id=off_reqs.ful_id) IS NULL") or sqlerr(__FILE__,__LINE__);
while ($row = mysql_fetch_array($res)) {

sql_query("DELETE FROM checkcomm WHERE checkid = ".sqlesc($row["id"])." AND offer = 1") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM off_reqs WHERE id = ".sqlesc($row["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM comments WHERE offer = ".sqlesc($row["id"])) or sqlerr(__FILE__,__LINE__);

write_log("������ ".$row["id"]." (".htmlspecialchars($row["name"]).") ��� ������ ��������: �������, �������� (������� �� ������)\n","52A77C","torrent"); 
}
//// �������� ���������� � ������ ����������


//// �������� �������� ��������
$res = sql_query("SELECT name, id FROM off_reqs WHERE perform='yes' AND data_perf < DATE_SUB(NOW(), INTERVAL 62 DAY)");
while ($row = mysql_fetch_array($res)) {

sql_query("DELETE FROM checkcomm WHERE checkid = ".sqlesc($row["id"])." AND offer = 1") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM off_reqs WHERE id = ".sqlesc($row["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM comments WHERE offer = ".sqlesc($row["id"])) or sqlerr(__FILE__,__LINE__);

write_log("������ ".$row["id"]." (".htmlspecialchars($row["name"]).") ��� ������ ��������: �������, ��������\n","52A77C","torrent");  
}
//// �������� �������� ��������



// ������ ����� �� ip
if ($use_ipbans==1){

$ban_sql=sql_query("SELECT id,comment,first,last,bans_time FROM bans WHERE bans_time<>'0000-00-00 00:00:00' AND bans_time < NOW()") or sqlerr(__FILE__,__LINE__);	
while ($ban_res = mysql_fetch_assoc($ban_sql)){
$ban_id=$ban_res["id"];
$first = long2ip($ban_res["first"]);
$last = long2ip($ban_res["last"]);
$comment = "�������: ".format_comment($ban_res["comment"])." �� ".$ban_res["bans_time"]."";
write_log("��� IP ������ ����� $ban_id (".($first == $last? $first:"$first - $last").") ��� ����� ��������. $comment.","704FFD","bans");
sql_query("DELETE FROM bans WHERE bans_time<>'0000-00-00 00:00:00' AND bans_time < NOW()") or sqlerr(__FILE__,__LINE__);
}
@unlink(ROOT_PATH."cache/bans_first_last.txt");
 /// sql_query("INSERT INTO sitelog (added, color, txt, type) SELECT $now, E5E5E5, bans FROM bans WHERE forum_com < NOW() AND enabled='yes' AND forum_com <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);	
}
// ������ ����� �� ip



// ������� ���� �� 31 ����
$secons_time = 31 * 86400; /// ���������� ���� 
$clear_day = time() - $secons_time;
sql_query("DELETE FROM shoutbox WHERE date < ".sqlesc($clear_day)) or sqlerr(__FILE__,__LINE__);


    if ($maxlogin=="1") {
    // ������� ������ ������� ������
    $secs = 7*86400; // ������ 7 ����
    $dt = sqlesc(get_date_time(gmtime() - $secs));
    sql_query("DELETE FROM loginattempts WHERE banned='no' AND added < $dt") or sqlerr(__FILE__,__LINE__);
    }
    

    
    
sql_query("DELETE FROM messages WHERE saved='no' and poster='0' and receiver not in (SELECT id FROM users WHERE id=messages.receiver)") or sqlerr(__FILE__, __LINE__);

sql_query("DELETE FROM messages WHERE saved='no' and poster='0' and sender not in (SELECT id FROM users WHERE id=messages.sender)") or sqlerr(__FILE__, __LINE__);
///$delete_receiv=mysql_affected_rows();


}

//sql_query("UPDATE torrents SET free='yes' WHERE size>=1073741824 ") or sqlerr(__FILE__, __LINE__);


/*


// ���������� ������� ����������� �� index.php
if (basename($_SERVER['SCRIPT_FILENAME']) <> 'index.php'){
sql_query('UPDATE tags AS t SET t.howmuch = (SELECT COUNT(*) FROM torrents AS ts WHERE ts.tags LIKE CONCAT(\'%\', t.name, \'%\'))')or sqlerr(__FILE__,__LINE__); // AND ts.category = t.category

} else {
sql_query('UPDATE tags AS t SET t.howmuch = (SELECT COUNT(*) FROM torrents AS ts WHERE name<>"" AND ts.tags LIKE CONCAT(\'%\', t.name, \'%\')) ORDER BY RAND() LIMIT 30')or sqlerr(__FILE__,__LINE__); // AND ts.category = t.category
}


*/


$h = date('H'); // ��������� ���
if (($h >= 13)&&($h <= 14)) // ����������
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
	
	
	
	
	

///////// ������������ �������

$h = date('H'); // ��������� ���
if (($h >= 01)&&($h <= 03)) // ����������
{

$now = time();
$res = sql_query("SELECT value_u FROM avps WHERE arg = 'sql_optimize'");
$row = mysql_fetch_array($res);
$row_time=$row["value_u"];
$now_2day=time()+(86400*7); /// ��� � ������
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

///////// ������������ �������













/*
if (date('d')=="31" && date('m')=="12") // ���������� �� ���� �����
{
$res = sql_query("SELECT id,usercomment,gender,username FROM users WHERE enabled='yes' AND status = 'confirmed' LIMIT 100") or sqlerr(__FILE__, __LINE__); 
// $id=0;
while ($arr = mysql_fetch_assoc($res)) {

$modcomment = htmlspecialchars($arr["usercomment"]);   
$user = htmlspecialchars($arr["username"]);   
///33285996544
if (!stristr($modcomment,"������� �� ����������")!==false || empty($modcomment)){
$modcomment = date("Y-m-d") . " - ������� �� ���������� (31GB).\n". $modcomment;
$dt = sqlesc(get_date_time(gmtime()));
$modcom = sqlesc($modcomment);   
sql_query("UPDATE users SET uploaded = uploaded+'33285996544', usercomment = $modcom WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);   
$subject = sqlesc("C ����� �����!!! ��� ��������� �� ����.\n");
$msg=sqlesc("�������".($arr["gender"]=="2"? "��":"��")." $user. � ����� ������ ����, ������������� ����� Muz-Tracker ����� ��� ���������, �� ������ ������������ ������� - 31 �������� ������� (������) � ������ ������ ��������. \n\n������� �� �������� � ����� �����, ���� ���������� ���������� [color=Magenta]ViKa[/color].\n");
sql_query("INSERT INTO messages (sender, receiver, added, subject,msg, poster) VALUES(92, $arr[id], $dt, $subject,$msg, 0)") or sqlerr(__FILE__, __LINE__); 
//++$id;
}
unset($modcomment);
}
//echo $id;
}
*/

@unlink(ROOT_PATH."cache/browse_genrelist.txt");





$maxdt = get_date_time(gmtime() - 62*86400);  /// ��� ������

$sq=sql_query("SELECT id,username,email,last_login,class,ip,(SELECT COUNT(*) FROM messages WHERE receiver=users.id AND location=1 AND unread='yes') AS unread FROM users WHERE last_access<".sqlesc($maxdt)." AND enabled='yes' AND status='confirmed' AND notif_access='0000-00-00 00:00:00' AND class<".sqlesc(UC_SYSOP)." LIMIT 1") or sqlerr(__FILE__, __LINE__); 
while ($r_bot=mysql_fetch_array($sq)){
$now_time = get_date_time();  /// ������ �����
$usern=$r_bot["username"];
$unred=(!empty($r_bot["unread"]) ? "� ���� �������, � ��� ��������� ����� ".number_format($r_bot["unread"])." ����� ������������� ���������.":"");
$last_login=$r_bot["last_login"];
$email=$r_bot["email"];
$ip=$r_bot["ip"];
$class=get_user_class_name($r_bot["class"]);
$id=$r_bot["id"];

$body = <<<EOD
������������ $usern. ��� ������� �� ����� $SITENAME, ��������� � ���������� ������ (��� ����������).

��������� ����������: $last_login
��������� ip �����: $ip
$unred

------------------------------------------------
����� ��� �����: $usern
������������������ �����: $email
����� �� �����: $class
��������� � ����� ������ �������� �� ������: $DEFAULTBASEURL/userdetails.php?id=$id

------------------------------------------------
����� �� ������ �������: $DEFAULTBASEURL/login.php
������������ ������: $DEFAULTBASEURL/recover.php
����� �����������: $DEFAULTBASEURL/signup.php

���� ���� �������� � ������� ��� ������ �� ����, ��������� � ��������������� ��� ������������� �����.
--> ����� �������� ������ - $now_time
---> � ��. ������� ����� $SITENAME
EOD;
///echo $body;
if (sent_mail($email,$SITENAME,$SITEEMAIL,"����������� � ������������ �������� �� $SITENAME",$body,false))
sql_query("UPDATE users SET notif_access=".sqlesc($now_time)." WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
}











$h = date('H'); // ��������� ���
if (($h >= 21)&&($h <= 22)) // ����������
{
	
$res = sql_query("SELECT value_u FROM avps WHERE arg = 'rand_price'") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$row_time=$row["value_u"];
$now_2day=time()+(86400); /// ��� � ����
$now = time();
$dt = get_date_time(gmtime());

if ($row_time<=$now){

$maxdt = sqlesc(get_date_time(gmtime() - 86400*14));  /// ���� ��� ������  ����� 2 ������ �����
$numo=0;
$sq=sql_query("SELECT id,username,gender FROM users WHERE enabled='yes' AND status='confirmed' AND parked='no' AND  id<>'92' AND last_access >= $maxdt ORDER BY RAND() LIMIT 1") or sqlerr(__FILE__, __LINE__);

while ($r_rand=mysql_fetch_array($sq)){

$id=$r_rand["id"];
$now = sqlesc(get_date_time());

$subject = sqlesc("������� �� ������� (���������)");

$upload = (10*1073741824); ///10 ��
$ten = mksize($upload);

$msg = sqlesc(($r_rand["gender"]=="2"? "��������� ":"��������� ")."[b]".$r_rand["username"]."[/b], ������� ���������� ��������, ������� ������� ���, �������������� �������: +$ten � ������� (������).\n ������� ����� [u]".$SITENAME."[/u].");

$usercomment = sqlesc(date("Y-m-d") . " - ��������� ������� �� ������� ($ten).\n");

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