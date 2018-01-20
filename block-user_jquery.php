<?
require_once 'include/bittorrent.php';

header ("Content-Type: text/html; charset=windows-1251");
dbconn(false,true);

/**
 * ƒанный файл проверен на индексы, все запросы быстро выполн€ютс€.
**/

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

global $CURUSER;

if (empty($CURUSER)){
die;
}


if (strtotime($CURUSER['last_access']) < (strtotime(get_date_time()) - 400)){
//// помечаем количество непрочитанных сообщений
$res_un = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=".$CURUSER["id"]." AND location=1 AND unread='yes'") or sqlerr(__FILE__,__LINE__);
$arr_un = @mysql_fetch_row($res_un);

sql_query("UPDATE users SET unread = ".sqlesc($arr_un[0])." WHERE id = ".sqlesc($CURUSER["id"])) or sqlerr(__FILE__,__LINE__);
$CURUSER["unread"]=$arr_un[0];
//// помечаем количество непрочитанных сообщений
}


if (strtotime($CURUSER['last_access']) < (strtotime(get_date_time()) - 180)){

	$uid = $CURUSER['id'];
	$sid = session_id();
	$updateset = array();
	if ($sid)
		$where[] = "sid = ".sqlesc($sid);
	elseif ($uid)
		$where[] = "uid = ".sqlesc($uid);

//	$ctime = time();
	$ctime = get_date_time();
	$updateset[] = "sid = ".sqlesc($sid);
	$updateset[] = "uid = ".sqlesc($uid);
	$updateset[] = "username = ".sqlesc($CURUSER['username']);
	$updateset[] = "class = ".sqlesc($CURUSER['class']);
	$updateset[] = "time = ".sqlesc($ctime);
	
    $s = (($_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://").htmlspecialchars_uni($_SERVER['HTTP_HOST']);
    $site = parse_url($s, PHP_URL_HOST);
    $updateset[] = "host = ".sqlesc($site);


	sql_query("UPDATE sessions SET ".implode(", ", $updateset)." WHERE ".implode(" AND ", $where)) or sqlerr(__FILE__,__LINE__);
///echo "да <br>";


}







///$unread = $arr1[0];
$unread = $CURUSER["unread"];
$newmessage1 = " нов" . ($unread > 1 ? "ых" : "ое"); 
$newmessage2 = " сообщен" . ($unread > 1 ? "ий" : "ие"); 
$newmessage = $newmessage1."<br>".$newmessage2; 


if ($unread){
echo "<span style=\" ".($CURUSER["stylesheet"]=="black_night" ? "":"BACKGROUND-COLOR: #efedef;")."  BORDER:silver 1px solid;  DISPLAY:block;  COLOR:#00f;  MARGIN:2px 1px;  PADDING:2px 2px 2px 6px;  TEXT-DECORATION:none;\"><b><a href=\"message.php?action=new\">” вас <font color=red>$unread</font> $newmessage</a></b></span>";
};



if (empty($unread)){
echo "<span style=\" style=\" ".($CURUSER["stylesheet"]=="black_night" ? "":"BACKGROUND-COLOR: #efedef;")."  BORDER:silver 1px solid;  DISPLAY:block;  COLOR:#00f;  MARGIN:1px 1px;  PADDING:2px 2px 2px 6px;  TEXT-DECORATION:none;\"><a href=\"message.php?action=viewmailbox&box=1\"><img height=\"16px\" style=\"border:none\" title=\"ѕросмотреть вход€щие сообщени€\" src=\"pic/pn_inbox.gif\"></a>&nbsp;&nbsp; <a href=\"message.php?action=viewmailbox&box=-1\"><img height=\"16px\" style=\"border:none\" title=\"ѕросмотреть исход€щие сообщени€\" src=\"pic/pn_sentbox.gif\"></a></span>";
};


} else @header("Location: ../index.php");

?>