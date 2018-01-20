<?
require_once 'include/bittorrent.php';

header ("Content-Type: text/html; charset=windows-1251");
dbconn(false,true);

/**
 * Данный файл проверен на индексы, все запросы быстро выполняются.
**/
  
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

global $CURUSER,$tracker_lang;

if (empty($CURUSER)){
//@header("Location: ../index.php");
die;
}



/// подумать оставить ли

$check = @mysql_fetch_array(sql_query("SELECT COUNT(*) AS numc FROM checkcomm WHERE userid = ".sqlesc($CURUSER["id"])));
$book= @mysql_fetch_array(sql_query("SELECT COUNT(*) AS numb FROM bookmarks WHERE userid = ".sqlesc($CURUSER["id"])));
    ///FORCE INDEX(userid) 
if (!empty($book["numb"]))
echo "<font color=\"#D72B3D\">Закладки: <a href=\"$DEFAULTBASEURL/bookmarks.php\">".$book["numb"]."</a></font><br>\n";
 
if (!empty($check["numc"]))
echo "<font color=\"#7E9C23\">Слежения: <a href=\"$DEFAULTBASEURL/checkcomm.php\">".$check["numc"]."</a></font><br>\n";



/// подумать оставить ли



$res2 = sql_query("SELECT seeder FROM peers WHERE userid={$CURUSER["id"]}") or sqlerr(__FILE__,__LINE__);

$activeseed = 0;
$activeleech = 0;

while ($peer = mysql_fetch_array($res2))
    if ($peer["seeder"] == "yes")
        $activeseed++;
    else
        $activeleech++;  


if ($activeseed || $activeleech){

echo $tracker_lang['torrents'].": ".(!$activeseed == "0" ? "&nbsp;<img alt=\"".$tracker_lang['seeding']."\" title=\"".$tracker_lang['seeding']."\"  style=\"border:none\"  src=\"./pic/arrowup.gif\">&nbsp;<font color=green><span class=\"smallfont\">".$activeseed."</span></font>" : "")."
".(!$activeleech == "0" ? "&nbsp;<img alt=\"".$tracker_lang['leeching']."\" title=\"".$tracker_lang['leeching']."\"  style=\"border:none\"  src=\"./pic/arrowdown.gif\">&nbsp;<font color=red><span class=\"smallfont\">".$activeleech."</span></font>" : "")." <br>\n";

//$content .= "<font color=\"#FFaa00\">Порт: &nbsp;$str_port</font><br/>\n";
}






if ($CURUSER["class"] >= UC_VIP){

$reo = sql_query("SELECT seeder FROM peers WHERE peers.torrent = (SELECT id FROM torrents WHERE owner=".sqlesc($CURUSER["id"])." AND peers.torrent=torrents.id LIMIT 1) AND peers.userid<>".sqlesc($CURUSER["id"])."") or sqlerr(__FILE__,__LINE__);

$actnoseed = 0;
$actnoleech = 0;

while ($arr_uo = mysql_fetch_array($reo))

if ($arr_uo["seeder"] == "yes")
++$actnoseed;
 else
++$actnoleech;

//echo $actnoseed." | ".$actnoleech;

if (!empty($actnoseed) || !empty($actnoleech)){
echo "<font title=\"Это тестовая функция.\" color=\"#AA55FF\">Релизы</font>: 
".($actnoseed <> "0" ? "&nbsp;<img title=\"На ваших релизах Раздают\" style=\"border:none\" src=\"./pic/arrowup.gif\">&nbsp;<span class=\"smallfont\">".$actnoseed."</span>" : "")."
".($actnoleech <> "0" ? "&nbsp;<img title=\"На ваших релизах Качают\" style=\"border:none\"  src=\"./pic/arrowdown.gif\">&nbsp;<span class=\"smallfont\">".$actnoleech."</span>" : "")." <br>";
}

}




if ($activeseed || $activeleech){

$result = sql_query("SELECT port, connectable FROM peers WHERE userid=".$CURUSER["id"]." LIMIT 1") or sqlerr(__FILE__,__LINE__);
if(mysql_num_rows($result)==0){
echo "<font color=\"#FF6600\">Ваш IP: " . getip() . "</font><br>";
}
else
{
 if($port = mysql_fetch_assoc($result)) {
 if ($port['connectable']=='yes')
 {
 	$color_port="green";
 
 if ($port["port"]) {
echo"<font color=\"#FF6600\">" . getip() . "</font>: <font color=$color_port>".$port["port"]."</font><br>";
}

 //	echo" <img height=\"13px\" style=\"border:none\" alt=\"Соединение по ".$port["port"]." порту\" title=\"Соединение по ".$port["port"]." порту\" src=\"pic/neticon_ok.gif\"> ";
 
 }
 else
 {
 	$color_port="red";
    $str_port=$open_yn;
    
 	if ($port["port"]) {
echo"<font color=\"#FF6600\">" . getip() . "</font>: <font color=$color_port>".$port["port"]."</font><br>";
}

// echo" <img height=\"13px\" style=\"border:none\" alt=\"Соединение блокируется и используется ".$port["port"]." порт\" title=\"Соединение блокируется и используется ".$port["port"]." порт\" src=\"pic/neticon_error.gif\"> ";

}

  


 }
}
} else
echo "<font color=\"#FF6600\">Ваш IP: " . getip() . "</font><br>";






} else @header("Location: ../index.php");

?>