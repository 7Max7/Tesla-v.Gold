<?
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
///info();


//$ajax = (!empty($_POST["ajax"]) ? 1:0)
$torrentid = (int) $_POST["torrentid"];

$rating= round(((int) $_POST["rate"]));




if ($_POST["ajax"]==1){

/*
$sf =ROOT_PATH."/1.txt"; 
$fpsf=fopen($sf,"a+"); 
$ag=getenv("HTTP_USER_AGENT"); 
$from=getenv("HTTP_REFERER"); 
$host=getenv("REQUEST_URI"); 
$date = date("d.m.y"); 
$time= date("H:i:s"); 
$getiing = serialize($_GET?$_GET:"")."||".serialize($_POST?$_POST:"");
fputs($fpsf,"$rate и $getiing\n"); 
fclose($fpsf); 
*/


if (empty($torrentid)) die;

$off_reqtid = ($_POST["off_req"]=="yes"? "yes":"no");
//$rating = (float) $_POST["rating"];

if (!empty($torrentid) && $off_reqtid<>"yes") { /// если торрент ///

/*
if ($_POST["ajax"] <> "yes") {
    stdmsg($tracker_lang["error"], "Что вы тут забыли ??!");
	exit();
}
*/

$chk = mysql_fetch_array(sql_query("SELECT torrent FROM ratings WHERE user=" . sqlesc($CURUSER["id"]) . " AND torrent=" . sqlesc($torrentid) . " LIMIT 1"));

if ($chk) {    die;   }

sql_query("INSERT INTO ratings (torrent, user, rating, added) VALUES (" . sqlesc($torrentid) . ", " . sqlesc($CURUSER["id"]) . ", " . sqlesc($rating) . ", NOW())")or sqlerr(__FILE__, __LINE__);

sql_query("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + " . sqlesc($rating) . " WHERE id = " . sqlesc($torrentid) ."")or sqlerr(__FILE__, __LINE__);

sql_query("UPDATE users SET unmark = unmark-1 WHERE id = " . sqlesc($CURUSER["id"]))or sqlerr(__FILE__, __LINE__);

/*
/// создаем найвероятнейшее условие хотя бы раз в час
$rmak2 = sql_query("SELECT COUNT(*) FROM snatched WHERE (SELECT COUNT(*) FROM ratings WHERE torrent=snatched.torrent AND off_req='0' AND user=".sqlesc($CURUSER["id"]).") <'1' AND snatched.finished='yes' AND userid = ".sqlesc($CURUSER["id"])) or sqlerr(__FILE__,__LINE__);
$arrmak2 = @mysql_fetch_array($rmak2);

if ($CURUSER["unmark"]<>$arrmak2[0])
sql_query("UPDATE users SET unmark = ".sqlesc($arrmak2[0])." WHERE id = ".sqlesc($CURUSER["id"])) or sqlerr(__FILE__,__LINE__);
*/
}
elseif (!empty($torrentid) && $off_reqtid=="yes") { /// если запрос ///
/*
if ($_POST["ajax"] <> "yes") {
    stdmsg($tracker_lang["error"], "Что вы тут забыли ?!!");
	exit();
}*/

$chk = mysql_fetch_array(sql_query("SELECT off_req FROM ratings WHERE user=" .sqlesc($CURUSER["id"]) . " AND off_req=" . sqlesc($torrentid) . " LIMIT 1"));
if ($chk) {
   die;
}

sql_query("INSERT INTO ratings (off_req, user, rating, added) VALUES (" . sqlesc($torrentid) . ", " . $CURUSER["id"] . ", " . sqlesc($rating) . ", NOW())")or sqlerr(__FILE__, __LINE__);
sql_query("UPDATE off_reqs SET numratings = numratings + 1, ratingsum = ratingsum + " . sqlesc($rating) . " WHERE id = " . sqlesc($torrentid) ."")or sqlerr(__FILE__, __LINE__);
}


}
else
stdmsg($tracker_lang["error"], "Что вы тут забыли ?!!!!");




?>