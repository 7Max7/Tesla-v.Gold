<?
require_once("include/bittorrent.php");

if (!mkglobal("id"))
    die("Нет id");

$id = (int) $id;
if (!$id)
    die("Нет id");

dbconn();
loggedinorreturn();

if (get_user_class() <= UC_MODERATOR) 
{
attacks_log('checkdelete'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

$res = sql_query("SELECT torrent_com,moderated,owner,category, moderatedby,(SELECT username FROM users WHERE id=moderatedby) AS username FROM torrents WHERE torrents.id = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);

if ($row["moderated"]=="no")
 {
stdhead(); 
stderr("Oшибка", "Данный торрент не проверен и так."); 
stdfoot(); 
exit;
}

	$cat=$row["category"];
    $cat_user=$CURUSER["catedit"];

 if ((get_user_class() == UC_MODERATOR) && (!empty($cat_user) && !stristr("$cat_user", "[cat$cat]")) && $CURUSER["id"] <> $row["owner"])
{
stdhead(); 
stderr("Oшибка", "Это торрент не из вашей категории."); 
stdfoot(); 
exit;
}


$torrent_com = get_date_time() . " $CURUSER[username] снял одобрение $row[username].\n". $row["torrent_com"];

$updateset = array();
$updateset[] = "torrent_com = " . sqlesc($torrent_com);
$updateset[] = "moderated = 'no'";

sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = $id");

$returl = getenv("HTTP_REFERER");
if (isset($_POST["returnto"]))
    $returl .= "&returnto=" . htmlentities($_POST["returnto"]);
if (empty($returl))
    $returl = "browse.php";

header("Refresh: 0; url=$returl");
?>