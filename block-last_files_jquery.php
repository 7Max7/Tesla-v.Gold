<? 
require "include/bittorrent.php";

dbconn(false,true);
header("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

if (isset($_GET['view'])){
$viewid = (empty($_GET['view']) ? "0":"1");
       
if($viewid == 1){
setcookie("view", "1", 0x7fffffff, "/");
echo ("<span title=\"Режим показа блока\" id=\"changeviewt\" sytle=\"border: 1px solid #cecece;padding: 5px 10px 5px 10px;background:#ededed;margin-right:5px;line-height: 23px;cursor: pointer;\"><b style=\"cursor: pointer;\" onClick=\"javascript:changeview('0');\">Список</b></span>\n");
} else {
setcookie("view", "0", 0x7fffffff, "/");
echo ("<span title=\"Режим показа блока\" id=\"changeviewt\" sytle=\"border: 1px solid #cecece;padding: 5px 10px 5px 10px;background:#ededed;margin-right:5px;line-height: 23px;cursor: pointer;\"><b style=\"cursor: pointer;\" onClick=\"javascript:changeview('1');\">Таблица</b></span>\n");
}
die;
}

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{
	
	
?>
<style>
.effect {
float: center;

text-align: center;
 border: 1px solid #ccc;
 -moz-border-radius: 3px; /*--CSS3 Rounded Corners--*/
 -webkit-border-radius: 3px; /*--CSS3 Rounded Corners--*/
 display: inline; /*--Gimp Fix aka IE6 Fix--*/
 FILTER: alpha(opacity=50); -moz-opacity: .50; opacity: .50;
}
</style>
 <?
///echo  "<script type=\"text/javascript\" src=\"js/glossy.js\"></script>";
	
$idcoo = (!empty($_COOKIE["view"]) ? "1":"0");

   $id = (int) $_POST["act"];

  if (!empty($id))
  $sas="t.category=".sqlesc($id)." AND";

$res = sql_query("SELECT t.times_completed,t.f_seeders,t.f_leechers,t.name,t.seeders,t.owner, t.tags, t.leechers,t.image1,t.id,t.hits, t.views,t.moderatedby, t.moderated,users.username AS us,users.class AS cl,
 m.class AS classname, m.username AS classusername
FROM torrents AS t
LEFT JOIN users ON t.owner = users.id
LEFT JOIN users AS m ON t.moderatedby = m.id
WHERE $sas t.banned = 'no' and t.moderated = 'yes' ORDER BY t.added DESC LIMIT 0 , ".(empty($idcoo) ? "8":"16")."") or sqlerr(__FILE__, __LINE__);
$num = mysql_num_rows($res);

if ($num > 0) {

if ($idcoo == 0){

    echo"<table border=\"0\" cellspacing=\"0\" width=\"100%\" class=\"main\" id=\"table\"> ";
    
    $nc=1;
    
    for ($i = 0; $i < $num; ++$i) {
    while ($row = mysql_fetch_assoc($res)) {

        if ($nc == 1) { echo"<tr>"; }

        echo"<td>";

$image1 = htmlentities($row["image1"]);

if(empty($image1))
$image1="default_torrent.png";  


echo"<td align=\"center\" valign=\"top\" width=\"25%\">"; 



if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image1))
echo "<a href=\"details.php?id=".$row["id"]."\"><img class=\"effect\" onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" src='".$image1."'\" height=\"140\" width=\"140\" border=\"0\" /><br>".htmlspecialchars_uni($row["name"])."</a>"; 
else
echo "<a href=\"details.php?id=".$row["id"]."\"><img class=\"effect\" onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" src='thumbnail.php?image=".$image1."&for=block'\" height=\"140\" width=\"140\" border=\"0\" /><br>".htmlspecialchars_uni($row["name"])."</a>"; 






if (!empty($row["webseed"])){
$row['seeders']=$row['seeders']+1;
}

echo'<b><div align="center"><font color=red>Раздают: '. ($row['seeders']+$row['f_seeders']) .'  </font> <font color=green>Качают: '. ($row['leechers']+$row['f_leechers']).'</b></div></font>';

if ($row["owner"]==$row["moderatedby"]){

echo "<b>Залито и Одобрено:<br> ".($row["us"] ? "<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row["cl"],$row["us"])."</a>":"нет автора")."</b>\n";
}
else
{
echo"<b>Залил: ".($row['us'] ?"<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row['cl'],$row['us'])."</a>":"нет автора")."</b><br>";

if ($row["moderated"] == "yes"){
echo"<b>Одобрил:</b> <b><a href=\"userdetails.php?id=$row[moderatedby]\">". get_user_class_color($row["classname"], ($row["classusername"])) . "	</a></b><br>\n";} else {
echo"<b>Одобрено:</b> <b>Нет</b><br>\n";
}
}
unset($row["moderated"],$row['us'],$row["classusername"],$row["classname"],$row["owner"],$row["moderatedby"]);

     
       
echo "</td>";
        ++$nc;
        if ($nc == 5) { $nc=1; echo"</tr>"; }
      //  if ($nc == 9) { $nc=1; echo"</tr>"; }
    }
}
echo"</tr></table>";

} else {

echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" class=\"main\"><tr>"; 

$dp=0;

while ($row = mysql_fetch_assoc($res)) {

if ($dp%2 == 0){
$clasto = "class = 'b'";
$clastf = "class = 'a'";
} else {
$clasto = "class = 'a'";
$clastf = "class = 'b'";
}

echo "<tr>";
     

echo "<td align=left ".$clasto."><a href=\"details.php?id=".$row["id"]."\"><b>".htmlspecialchars_uni($row["name"])."</b></a>";
		

if (!empty($row["tags"])) {
$tags[$row["id"]]="";
foreach(explode(",", $row["tags"]) as $tag) {
	
if (!empty($tags[$row["id"]]))
$tags[$row["id"]].=", ";

$tags[$row["id"]].= "<a style=\"font-weight:normal;\" href=\"browse.php?tag=".urlencode($tag)."&incldead=1\">".tolower($tag)."</a>";
}
$tags[$row["id"]]=$tags[$row["id"]];
}
else
$tags[$row["id"]]="не выбраны";

if (!empty($tags[$row["id"]]))
echo("<br><b>Теги</b>: ".$tags[$row["id"]]." ".((!empty($tags[$row["id"]]) && strlen($tags[$row["id"]])>200) ? "&nbsp; ":"")."");//<br>


echo "</td>"; 
        
echo "<td align=center ".$clastf." width=\"10%\"><font color=red><b>". ($row['seeders']+$row['f_seeders']) ."</b></font> / <font color=green><b>". ($row['leechers']+$row['f_leechers'])."</b></font>";
echo "</td></tr>";
++$dp;
    }

    echo "</tr></table>";
}


}




else

echo"<center>Нет раздач на этом трекере...</center>\n";

}

?>
