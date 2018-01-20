<?
require_once("include/bittorrent.php");

dbconn();

if ($CURUSER['monitoring']=='yes'){
$userid=$CURUSER["id"];
$sf =ROOT_PATH."cache/monitoring_$userid.txt"; 
$fpsf=fopen($sf,"a+"); 
$ip3=getip();
$ag=getenv("HTTP_USER_AGENT"); 
$from=getenv("HTTP_REFERER"); 
$host=getenv("REQUEST_URI"); 
$date = date("d.m.y"); 
$time= date("H:i:s"); 
//fputs($fpsf,"Выход $ip3 - Браузер: $ag - $date-$time\n\n"); 
fputs($fpsf,"Выход из учетки##$ip3#$ag#$date $time\n"); 

fclose($fpsf);
}

if ($CURUSER["class"]=="6"){
$where[] = "uid = ".sqlesc($CURUSER["id"]);
$where[] = "class = ".sqlesc($CURUSER["class"]);
sql_query("DELETE FROM sessions WHERE ".implode(" AND ", $where));
}


logoutcookie();

//header("Refresh: 0; url=./");
Header("Location: $DEFAULTBASEURL/");

?>