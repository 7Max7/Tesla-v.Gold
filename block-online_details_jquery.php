<?
require_once 'include/bittorrent.php';

header ("Content-Type: text/html; charset=windows-1251");
dbconn(false,true);

  
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

$id=(int)$_POST["id"];

if ($id==0)
die("Ошибка данных, id равно 0.");

if (!$CURUSER)
die;

//////////////////////////////
//$dt = sqlesc(time() - 180);

$url="/userdetails.php?id=".$id;

$res_s = sql_query("SELECT DISTINCT uid, username, class,ip FROM sessions WHERE time > ".sqlesc(get_date_time(gmtime() - 180))." and url='$url' ORDER BY time DESC") or sqlerr(__FILE__,__LINE__);
// or url LIKE '%$url_e%'
$lastid=0;
$title_who_s="";
while ($ar_r = mysql_fetch_assoc($res_s)) {

$username = $ar_r['username'];
$id_use = $ar_r['uid'];

if ($title_who_s)
$title_who_s.=", ";

if (!empty($username))
$title_who_s.="<a href=\"userdetails.php?id=$id_use\">".get_user_class_color($ar_r["class"], $ar_r["username"]) . "</a>";
else 
$title_who_s.=$ar_r['ip'];

++$lastid;

}
echo $title_who_s;
if ($lastid==0)
echo"Никто не просматривает данный профиль.";

} else @header("Location: ../index.php");

?>