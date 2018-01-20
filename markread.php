<? require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();
ob_start();

stdheadchat();

if (sql_query("UPDATE users SET last_checked = " .sqlesc(get_date_time()). " WHERE id = " .sqlesc($CURUSER["id"])) && @setcookie("markview", ""))
   stdmsg("Успешно", "Новые торренты отмечены как прочитаные.");
else
   stdmsg("Ошибка", "Отметка новых торрентов произошла с ошибкой: ". sqlerr(__FILE__, __LINE__));

stdheadchat();

header("Refresh: 5; url=browse.php");
?>