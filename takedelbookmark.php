<? 
require_once("include/bittorrent.php"); 

function bark($msg) {
stdhead();
stdmsg($tracker_lang['error'], $msg); 
stdfoot();
exit;
}

dbconn(); 
loggedinorreturn(); 

if (empty($_POST["delbookmark"])) 
       bark("Ничего не выбрано");

foreach ($_POST["delbookmark"] as $del) {

$id=(int)$del;
if (!empty($id))
$res2 = sql_query("SELECT id, userid FROM bookmarks WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__); 

while ($arr = mysql_fetch_assoc($res2)) {
 if (($arr["userid"] == $CURUSER["id"]) || (get_user_class() >= UC_MODERATOR)) 
sql_query("DELETE FROM bookmarks WHERE id = ".$arr["id"]) or sqlerr(__FILE__, __LINE__); 
       else 
bark("Вы пытаетесь удалить не свою закладку!"); 
}

}


header("Refresh: 0; url=" . htmlspecialchars($_SERVER['HTTP_REFERER'])."");
?> 