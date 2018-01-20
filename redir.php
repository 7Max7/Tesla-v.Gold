<?

/**
 * @author 7Max7
 * @copyright 2010
 */

require ("include/bittorrent.php");
dbconn();

stdheadchat("Переадресация...");

$url = (!empty($_GET["url"])? htmlspecialchars_uni(($_GET["url"])):"");
$from = htmlspecialchars_uni(getenv("HTTP_REFERER"));

$from_site = @parse_url($from, PHP_URL_HOST);

$delay = (isset($_GET["nodelay"]) ? true:false);

$site = @parse_url($url, PHP_URL_HOST);

if ($delay == true) {
@header("Location: ".$url);
die;
} else {

if ($from_site<>$site && !empty($redir_parse) && !empty($from)) {


$h = date('H'); // проверяем час
if (($h >= 10)&&($h <= 12) || ($h >= 22)&&($h <= 24)){
sql_query("DELETE FROM reaway WHERE parse_ref='' OR parse_url=''") or sqlerr(__FILE__,__LINE__);
}


$ip = getip();

$uid = $CURUSER["id"];
if (empty($uid))
$uid = 0;

sql_query("INSERT INTO reaway (parse_url,parse_ref,uid,ip,date,numb,lastdate) VALUES (".sqlesc($url).",".sqlesc($from).",".sqlesc($uid).",".sqlesc($ip).",".sqlesc(get_date_time()).",1,'0000-00-00 00:00:00')");

if (!mysql_insert_id()){
sql_query("UPDATE reaway SET numb=numb+1, lastdate=".sqlesc(get_date_time())." WHERE uid=".sqlesc($uid)." AND ip=".sqlesc($ip)." AND parse_url=".sqlesc($url)." AND parse_ref=".sqlesc($from)) or sqlerr(__FILE__,__LINE__);
}
}


}


if ($from_site==$site || empty($from))
$from = "index.php";





//$url = htmlspecialchars($url);



echo "<table class=\"embedded\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">";

echo "<tr>
<td class=\"a\" align=\"center\" colspan=\"4\">Переадресация</td>
</tr>";

if (preg_match("/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps):\/\/[^()<>\s]+)/i",$url, $matches)) {
echo "<meta http-equiv=\"refresh\" content=\"5; URL=redir.php?nodelay&url=".$url."\">";

echo "<tr>
<td class=\"b\" align=\"center\" colspan=\"4\">Вы покидаете <b><a rel=\"nofollow\" href=\"".$from."\">".$SITENAME."</a></b> и переходите на <b><a rel=\"nofollow\" href=\"redir.php?nodelay&url=".$url."\">".$site."</a></b>.<br><br> 
Вы будете переадресованы через 5 секунд.</td>
</tr>";
$if = true;
} else {

echo "<tr>
<td class=\"b\" align=\"center\" colspan=\"4\">Ссылка не определена.</td>
</tr>";

}

if ($if == true)
echo "<tr>
<td class=\"b\" align=\"center\" colspan=\"4\">
<script language=\"javascript\" type=\"text/javascript\">
var loadedcolor='".(get_user_rgbcolor($CURUSER["class"]))."';var unloadedcolor='';var bordercolor='';var barheight=10; var barwidth=400; var waitTime=5;var ns4=(document.layers)?true:false; var ie4=(document.all)?true:false; var blocksize=(barwidth-2)/waitTime/10;var loaded=0; var PBouter; var PBdone; var PBbckgnd; var Pid=0; var txt='';if(ns4){txt+='<table border=0 cellpadding=0 cellspacing=0><tr><td>';txt+='<ilayer name=\"PBouter\" visibility=\"hide\" height=\"'+barheight+'\" width=\"'+barwidth+'\" onmouseup=\"hidebar()\">';txt+='<layer width=\"'+barwidth+'\" height=\"'+barheight+'\" bgcolor=\"'+bordercolor+'\" top=\"0\" left=\"0\"></layer>';txt+='<layer width=\"'+(barwidth-2)+'\" height=\"'+(barheight-2)+'\" bgcolor=\"'+unloadedcolor+'\" top=\"1\" left=\"1\"></layer>';txt+='<layer name=\"PBdone\" width=\"'+(barwidth-2)+'\" height=\"'+(barheight-2)+'\" bgcolor=\"'+loadedcolor+'\" top=\"1\" left=\"1\"></layer>';txt+='</ilayer>';txt+='</td></tr></table>';}else{ txt+='<div id=\"PBouter\" onmouseup=\"hidebar()\" style=\"position:relative; visibility:hidden; background-color:'+bordercolor+'; width:'+barwidth+'px; height:'+barheight+'px;\">';txt+='<div style=\"position:absolute; top:1px; left:1px; width:'+(barwidth-2)+'px; height:'+(barheight-2)+'px; background-color:'+unloadedcolor+'; font-size:1px;\"></div>';txt+='<div id=\"PBdone\" style=\"position:absolute; top:1px; left:1px; width:0px; height:'+(barheight-2)+'px; background-color:'+loadedcolor+'; font-size:1px;\"></div>';txt+='</div>';}document.write(txt);function incrCount(){ window.status=\"Loading...\"; loaded++; if(loaded<0)loaded=0; if(loaded>=waitTime*10){ clearInterval(Pid); loaded=waitTime*10; setTimeout('hidebar()',100); } resizeEl(PBdone, 0, blocksize*loaded, barheight-2, 0); }function hidebar(){window.status='';}function findlayer(name,doc){ var i,layer; for(i=0;i<doc.layers.length;i++){ layer=doc.layers[i]; if(layer.name==name)return layer; if(layer.document.layers.length>0) if((layer=findlayer(name,layer.document))!=null) return layer; } return null; }function progressBarInit(){ PBouter=(ns4)?findlayer('PBouter',document):(ie4)?document.all['PBouter']:document.getElementById('PBouter'); PBdone=(ns4)?PBouter.document.layers['PBdone']:(ie4)?document.all['PBdone']:document.getElementById('PBdone'); resizeEl(PBdone,0,0,barheight-2,0); if(ns4)PBouter.visibility=\"show\"; else PBouter.style.visibility=\"visible\"; Pid=setInterval('incrCount()',95); }function resizeEl(id,t,r,b,l){ if(ns4){ id.clip.left=l; id.clip.top=t; id.clip.right=r; id.clip.bottom=b; }else id.style.width=r+'px'; } window.onload=progressBarInit; </script>
</td>
</tr>";


echo "</table><br><br>";




stdfootchat();


?>