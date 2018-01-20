<?
require_once("include/bittorrent.php");

dbconn(false);
loggedinorreturn();

$res = sql_query("SELECT override_class, class FROM users WHERE id=".sqlesc($CURUSER['id']));
$row = mysql_fetch_array($res);

if ($row['override_class'] == "255")
stderr($tracker_lang['error'],"Ошибка у вас не возможно восстановить права, вы не меняли и не тестировали новые.");

$override_class = $row['override_class'];
$class = $row['class'];

sql_query("UPDATE users SET class = ".sqlesc($override_class).", override_class = 255 WHERE id = ".$CURUSER['id']) or sqlerr(__FILE__, __LINE__);


$from=getenv("HTTP_REFERER"); 
if (!$from){$from="index.php";}

header("Location: $from");
?>