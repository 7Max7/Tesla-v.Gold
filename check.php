<?
require_once("include/bittorrent.php");

if (!mkglobal("id"))
    die("��� id");

$id = (int) $id;
if (!$id)
    die("������");

dbconn();
loggedinorreturn();

$res = sql_query("SELECT torrents.name, torrents.moderated,torrents.owner,torrents.torrent_com, torrents.category, torrents.owner FROM torrents WHERE torrents.id = ".sqlesc($id)."")
   or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);

if ($row["moderated"]=="yes") {
stdhead(); 
stderr("O�����", "������ ������� ��� ��������."); 
stdfoot(); 
exit;
}

$cat=$row["category"];
$cat_user=$CURUSER["catedit"];

if ((get_user_class() == UC_MODERATOR) && (!empty($cat_user) && !stristr($cat_user, "[cat$cat]")) && $CURUSER["id"] <> $row["owner"]) {
stdhead(); 
stderr("O�����", "��� ������� �� �� ����� ���������."); 
stdfoot(); 
exit;
}


/*
if ($CURUSER["id"] == $row["owner"]){
	stdhead(); 
stderr("O�����", "��� ��� �������, ����� ��� ��������."); 
stdfoot(); 
exit;
}
*/



$updateset = array();

if (get_user_class() <= UC_VIP)
stderr($tracker_lang['error'], "������������� ������ ���������� ��������"); 
   

    
if(get_user_class() >= UC_MODERATOR){
$updateset[] = "moderated = 'yes'";
$updateset[] = "moderatedby = ".sqlesc($CURUSER["id"]);
$updateset[] = "moderatordate = ".sqlesc(get_date_time());

$torrent_com = get_date_time() . " $CURUSER[username] �������.\n". $row["torrent_com"];
$updateset[] = "torrent_com = " . sqlesc($torrent_com);

sql_query("UPDATE torrents SET " . implode(",", $updateset) . " WHERE id = ".sqlesc($id)."");
@unlink(ROOT_PATH."cache/block-last_files.txt"); // ��� ����������� ��������, ������� ��� ����� ���������� ���

}
$returl = getenv("HTTP_REFERER");

@unlink(ROOT_PATH."cache/premod.txt"); // ��� ����������� ��������, ������� ��� ����� ���������� ���

if (isset($_POST["returnto"]))
    $returl .= "&returnto=" . htmlentities($_POST["returnto"]);

if (!$returl)
    $returl = "browse.php";

header("Refresh: 0; url=$returl");
?>