<?
require_once("include/bittorrent.php");

function bark($msg) {
	stdhead();
	stdmsg("Ошибка", $msg);
      stdfoot();
      die;
}

dbconn();
loggedinorreturn();

if (!empty($_POST["torrentid"])){

$id=(int) $_POST["torrentid"];

if (!$id)
bark("Неправильный ID");

$userid = $CURUSER["id"];
$torrentid = (int) $_POST["torrentid"];

if(empty($_POST["motive"]))
bark("Причина не может быть пустой");

if (!is_valid_id($torrentid))
bark("Неправильный ID");

$motive = htmlspecialchars($_POST["motive"]);

$reason = sqlesc("".$motive."");  


if (isset($userid) && isset($torrentid))
{
    $owntorrentquery = sql_query("SELECT null FROM torrents WHERE id = '$torrentid' and owner = '$userid'") or sqlerr(__FILE__,__LINE__);

    $owntorrentrow = mysql_fetch_object($owntorrentquery);

    if($owntorrentrow)
    {
        header("Location: $BASEURL/details.php?id=$torrentid&ownreport=1");
                die();
    }
    
$random_query = sql_query("SELECT name FROM torrents WHERE id = '$torrentid'") or sqlerr(__FILE__, __LINE__); 
$random = mysql_fetch_array($random_query);

$subject = sqlesc("Подана жалоба");
$now = sqlesc(get_date_time());
$msg = sqlesc("Пользователем [b][url=$DEFAULTBASEURL/userdetails.php?id=".$CURUSER["id"]."]".$CURUSER["username"]."[/url][/b] была подана жалоба на торрент 
[b][url=$DEFAULTBASEURL/details.php?id=".$id."]".$random[name]."[/url][/b]
\nПричина: ".$motive."");

    $alreadythankquery = mysql_query("SELECT NULL FROM report WHERE torrentid = '$torrentid' and userid = '$userid'") or sqlerr(__FILE__,__LINE__);
    $alreadythankrow = mysql_fetch_object($alreadythankquery);

    if (!$alreadythankrow)
    {
        mysql_query("INSERT INTO report (torrentid, userid, motive, added) VALUES ($torrentid, $userid, $reason, NOW())") or sqlerr(__FILE__,__LINE__);  


$idus=$CURUSER["id"];

      ///  mysql_query("INSERT INTO messages (sender, receiver, added, msg, subject, poster) SELECT $idus, id, $now, $msg, $subject, 0 FROM users WHERE class = ".UC_MODERATOR."") or sqlerr(__FILE__,__LINE__);


        header("Location: $DEFAULTBASEURL/details.php?id=$torrentid&report=1");
                die();
    }
    else
    {
        header("Location: $BASEURL/details.php?id=$torrentid&alreadyreport=1");
                die();
    }
}

} 


elseif (!empty($_POST["usertid"]) && $_POST["usertid"]<>$CURUSER["id"])
{
	
	
	
	


$id=(int) $_POST["usertid"]; 

if (!$id)
bark("Неправильный ID");
$userid = $CURUSER["id"];
$torrentid = (int) $_POST["usertid"];

if(empty($_POST["motive"]))
bark("Причина не может быть пустой");

if (!is_valid_id($torrentid))
bark("Неправильный ID");

$motive = trim(strip_tags(htmlspecialchars($_POST["motive"])));

$reason = sqlesc("".$motive."");  

$res1 = sql_query("SELECT username, class,enabled FROM users WHERE id = $torrentid") or sqlerr(__FILE__, __LINE__); 
$row1 = mysql_fetch_array($res1); 
if (!$row1){
bark("На трекере нет такого пользователя с таким id: $torrentid");
} else {
	
if ($row1["class"]>="5"){
bark("Нельзя подать жалобу на администрацию сайта.");
}

if ($row1["enabled"]=="no"){
bark("Нельзя подать жалобу на отключенного пользователя.");
}


$username_id="[color=#".get_user_rgbcolor($row1["class"],$row1["username"])."]".$row1["username"]."[/color]";
$username_curuser="[color=#".get_user_rgbcolor($CURUSER["class"],$CURUSER["username"])."]".$CURUSER["username"]."[/color]";

$subject = sqlesc("Подана жалоба на юзера");
$now = sqlesc(get_date_time());
$msg = sqlesc("Пользователем [b][url=$DEFAULTBASEURL/userdetails.php?id=".$CURUSER["id"]."]".$username_curuser."[/url][/b] была подана жалоба на [b][url=$DEFAULTBASEURL/userdetails.php?id=".$id."]".$username_id."[/url][/b] в [b][url=$DEFAULTBASEURL/viewreport.php]Список Жалоб[/url][/b]\n\nПричина: ".$motive." \n");

if (isset($userid) && isset($torrentid)) {

$alread = sql_query("SELECT COUNT(*) AS numb FROM report WHERE torrentid = '0' and userid='$userid' LIMIT 8") or sqlerr(__FILE__,__LINE__);
$toent = mysql_fetch_array($alread);

if (!empty($toent["numb"]) && $toent["numb"]>=5) {

header("Refresh: 15; url=$DEFAULTBASEURL/userdetails.php?id=$torrentid");
stdhead("Жалоба отправленна");
stdmsg("Ошибка", "На этого пользователя уже есть жалобы (".$toent["numb"]."), пожалуйста повторите попытку позже или напишите сообщение администрации (в крайнем случае).");
stdfoot();
die();
}


sql_query("INSERT INTO report (usertid, userid, motive, added) VALUES ($torrentid, $userid, $reason, NOW())") or sqlerr(__FILE__,__LINE__);  
                        
//sql_query("INSERT INTO messages (sender, receiver, added, msg, subject, poster) SELECT 0, id, $now, $msg, $subject, 0 FROM users WHERE class = ".UC_ADMINISTRATOR."") or sqlerr(__FILE__,__LINE__);

$username_curuser="<color=#".get_user_rgbcolor($row1["class"],$row1["username"]).">".$row1["username"]."</color>";
/// header("Location: $DEFAULTBASEURL/userdetails.php?id=$torrentid");
header("Refresh: 5; url=$DEFAULTBASEURL/userdetails.php?id=$torrentid");
stdhead("Жалоба отправленна");
stdmsg("Готово", "Успешно отправленна от вас жалоба на ".$username_curuser." администрации. Перенаправление назад.");
stdfoot();
die();
}}
	
}
else bark("Чего хотим не знаем ?");

?>