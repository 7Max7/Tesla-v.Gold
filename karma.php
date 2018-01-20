<?php

require_once("include/bittorrent.php");
dbconn();
header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{
    $id = (int)$_POST['id'];
    $user = (int)$CURUSER['id'];
    $act = (string)$_POST["act"];
    $type = (string)$_POST["type"];

    if (empty($id) || empty($user) || empty($act) || empty($type))
        die("Прямой доступ закрыт");

    if (!in_array($type, array("torrent", "comment", "user","humor")))
        die("Прямой доступ закрыт");

    $canrate = get_row_count("karma", "WHERE type = " . sqlesc($type) . " AND value = ".sqlesc($id)." AND user = ".sqlesc($user)."");

    if ($canrate > 0)
       die("Вы уже голосовали");

    if ($type == "torrent")
        $table = "torrents";
    elseif ($type == "comment")
        $table = "comments";
   elseif ($type == "humor")
        $table = "humor";
    else
        $table = "users";

    if ($act == 'plus')
    {
        sql_query("UPDATE $table SET karma = karma + 1 WHERE id = ".sqlesc($id)."");
        sql_query("INSERT INTO karma (type, value, user, added) VALUES (" . sqlesc($type) . ", ".sqlesc($id).", ".sqlesc($user).", " . time() . ")");
        
        if ($table=="humor"){
sql_query("UPDATE users SET uploaded=uploaded+26214400 WHERE uploaded / downloaded >= 1 AND id=(SELECT uid FROM humor WHERE id=".sqlesc($id).")") or sqlerr(__FILE__,__LINE__);
        }
        
        $show = true;
    }
    elseif ($act == 'minus')
    {
    
        sql_query("UPDATE $table SET karma = karma - 1 WHERE id = ".sqlesc($id)."");
        sql_query("INSERT INTO karma (type, value, user, added) VALUES (" . sqlesc($type) . ", ".sqlesc($id).", ".sqlesc($user).", " . time() . ")");
        
        if ($table=="humor"){
sql_query("UPDATE users SET uploaded=uploaded-13107200 WHERE uploaded / downloaded >= 1 AND id=(SELECT uid FROM humor WHERE id=".sqlesc($id).")") or sqlerr(__FILE__,__LINE__);
        }
        
        $show = true;
    }
   else
       die("Прямой доступ закрыт");

    if ($show)
    {
        $res = sql_query("SELECT karma FROM $table WHERE id = ".sqlesc($id)."");
        $row = mysql_fetch_array($res);
        die("<img src=\"pic/minus-dis.png\" title=\"Вы не можете голосовать\" alt=\"\" />&nbsp;" . karma($row["karma"]) . "&nbsp;<img src=\"pic/plus-dis.png\" title=\"Вы не можете голосовать\" alt=\"\" />");
    }
}
else
    die("Прямой доступ закрыт");

?>