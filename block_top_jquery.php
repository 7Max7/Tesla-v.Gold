<? 
require "include/bittorrent.php";

dbconn(false,false);
header("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{

?>
<style>
.effects {FILTER: alpha(opacity=50); -moz-opacity: .50; opacity: .50;}
</style>
<?

$id = (string) $_POST["act"];

if (!$CURUSER) die;

$arra=array("view","intop","outtop","snat","mark");

//if (!array_search($id,$arra))
//die($id);

/// топ просмотров
if ($id=="view"){
	
$res = sql_query("SELECT t.times_completed,t.f_seeders,t.f_leechers,t.name,t.seeders,t.owner, t.webseed, t.leechers,t.image1,t.id,t.hits, t.views,t.moderatedby, t.moderated,users.username AS us,users.class AS cl,
 m.class AS classname, m.username AS classusername
FROM torrents AS t
LEFT JOIN users ON t.owner = users.id
LEFT JOIN users AS m ON t.moderatedby = m.id
WHERE t.banned = 'no' and t.moderated = 'yes' and t.added > DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY t.views DESC LIMIT 0 , 8") or sqlerr(__FILE__, __LINE__);

$num=1;
$numi=0;

if ($num > 0) {
echo "<table border=\"1\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"100%\" class=\"main\" id=\"table\"> ";
    $nc=1;
    
for ($i = 0; $i < $num; ++$i) {
	
while ($row=mysql_fetch_array($res)){
///while ($row=$cache->fetch_assoc()){	
$num=1;

if ($nc == 1) { $content.="<tr>"; }
echo "<td>";


echo "<td align=\"center\" valign=\"top\" width=\"25%\">"; 

$image1 = htmlentities($row["image1"]);

if(empty($image1))
$image1="default_torrent.png";  

if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image1))
echo "<a href=details.php?id=".$row["id"]."><img class=effects onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effects'\" title=\"".htmlspecialchars($row["name"])."\" src=\"".$image1."\" height=\"140\" width=\"140\"/><br>".htmlspecialchars_uni($row["name"])."</a>";
else
echo "<a href=details.php?id=".$row["id"]."><img class=effects onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effects'\" title=\"".htmlspecialchars($row["name"])."\" src=\"thumbnail.php?image=".$image1."&for=block\" height=\"140\" width=\"140\"/><br>".htmlspecialchars_uni($row["name"])."</a>";

if (!empty($row["webseed"])){
$row['seeders']=$row['seeders']+1;
}

echo '<b><div align="center"><font color=blue>Просмотров: '. round($row['views']) .'  </font><br><font color=red>Раздают: '. ($row['seeders']+$row['f_seeders']) .'  </font> <font color=green>Качают: '. ($row['leechers']+$row['f_leechers']).'</b></div></font>';

if ($row["owner"]==$row["moderatedby"]){

echo "<b>Залито и Одобрено:<br> ".($row["us"] ? "<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row["cl"],$row["us"])."</a>":"нет автора")."</b>\n";
}
 else
{
echo "<b>Залил: ".($row['us'] ?"<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row['cl'],$row['us'])."</a>":"нет автора")."

</b><br>";
 
if ($row["moderated"] == "yes"){
echo "<b>Одобрил:</b> <b><a href=\"userdetails.php?id=$row[moderatedby]\">". get_user_class_color($row["classname"], htmlspecialchars_uni($row["classusername"])) . "	</a></b><br>\n";} else {
echo "<b>Одобрено:</b> <b>Нет</b><br>\n";
}
}     
       
echo "</td>";
        ++$nc;
        if ($nc == 5) { $nc=1; echo "</tr>"; }
    ++$numi;
	  //  if ($nc == 9) { $nc=1; echo"</tr>"; }
    }
}
echo "</tr></table>";

}

if ($numi==0)
die("<center>Нет просмотров за последнюю неделю.</center>\n");	
}

/// топ  внутренний скачиваний
elseif ($id=="intop"){
$res = sql_query("SELECT t.times_completed,t.name,t.seeders,t.owner, t.webseed, t.leechers,t.image1,t.id,t.hits, t.views,t.moderatedby, t.moderated,users.username AS us,users.class AS cl,
 m.class AS classname, m.username AS classusername
FROM torrents AS t
LEFT JOIN users ON t.owner = users.id
LEFT JOIN users AS m ON t.moderatedby = m.id
WHERE t.banned = 'no' and t.moderated = 'yes' and t.multitracker='no' and (t.seeders+t.leechers)>=1 and t.added > DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY t.times_completed DESC LIMIT 0 , 8") or sqlerr(__FILE__, __LINE__);

$num=1;
$numi=0;

if ($num > 0) {
echo "<table border=\"1\" cellspacing=\"0\" style=\"border-collapse: collapse\" class=\"main\" width=\"100%\" id=\"table\"> ";
$nc=1;

for ($i = 0; $i < $num; ++$i) {

while ($row=mysql_fetch_array($res)){
///while ($row=$cache->fetch_assoc()){	
$num=1;

if ($nc == 1) { $content.="<tr>"; }
echo "<td>";

echo "<td align=\"center\" valign=\"top\" width=\"25%\">"; 

$image1 = htmlentities($row["image1"]);

if(empty($image1))
$image1="default_torrent.png";  

if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image1))
echo "<a href=details.php?id=".$row["id"]."><img class=effects onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effects'\" src=\"".$image1."\" title=\"".htmlspecialchars($row["name"])."\" height=\"140\" width=\"140\"/><br>".htmlspecialchars_uni($row["name"])."</a>";
else
echo "<a href=details.php?id=".$row["id"]."><img class=effects onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effects'\" title=\"".htmlspecialchars($row["name"])."\" src=\"thumbnail.php?image=".$image1."&for=block\" height=\"140\" width=\"140\"/><br>".htmlspecialchars_uni($row["name"])."</a>";


if (!empty($row["webseed"])){
$row['seeders']=$row['seeders']+1;
}

echo '<b><div align="center"><font color=blue>Скачан: '. round($row['times_completed']) .'  </font><br><font color=red>Раздают: '. ($row['seeders']) .'  </font> <font color=green>Качают: '. ($row['leechers']).'</b></div></font>';

if ($row["owner"]==$row["moderatedby"]){

echo "<b>Залито и Одобрено:<br> ".($row["us"] ? "<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row["cl"],$row["us"])."</a>":"нет автора")."</b>\n";
}
 else
{
echo "<b>Залил: ".($row['us'] ?"<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row['cl'],$row['us'])."</a>":"нет автора")."

</b><br>";
 
if ($row["moderated"] == "yes"){
echo "<b>Одобрил:</b> <b><a href=\"userdetails.php?id=$row[moderatedby]\">". get_user_class_color($row["classname"], $row["classusername"]) . "	</a></b><br>\n";} else {
echo "<b>Одобрено:</b> <b>Нет</b><br>\n";
}
}

echo "</td>";
        ++$nc;
        if ($nc == 5) { $nc=1; echo "</tr>"; }
    ++$numi;
	  //  if ($nc == 9) { $nc=1; echo"</tr>"; }
    }
}
echo "</tr></table>";

}

if ($numi==0)
die("<center>Нет внутренних топов за последнюю неделю.</center>\n");	

}


/// топ внешних
elseif ($id=="outtop"){
$res = sql_query("SELECT t.times_completed,t.name,t.owner, t.webseed, t.f_seeders,t.f_leechers,t.image1,t.id,t.hits, t.views,t.moderatedby, t.moderated,users.username AS us,users.class AS cl,
 m.class AS classname, m.username AS classusername
FROM torrents AS t
LEFT JOIN users ON t.owner = users.id
LEFT JOIN users AS m ON t.moderatedby = m.id
WHERE t.banned = 'no' and t.moderated = 'yes' and (t.f_seeders+t.f_leechers)>=1 and t.multitracker='yes' and t.added > DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY t.times_completed DESC LIMIT 0 , 8") or sqlerr(__FILE__, __LINE__);

$num=1;
$numi=0;

if ($num > 0) {
echo "<table border=\"1\" cellspacing=\"0\" style=\"border-collapse: collapse\" class=\"main\" width=\"100%\" id=\"table\"> ";
    $nc=1;
    
for ($i = 0; $i < $num; ++$i) {
	
while ($row=mysql_fetch_array($res)){
///while ($row=$cache->fetch_assoc()){	
$num=1;

if ($nc == 1) { $content.="<tr>"; }
echo "<td>";

echo "<td align=\"center\" valign=\"top\" width=\"25%\">"; 

$image1 = htmlentities($row["image1"]);

if(empty($image1))
$image1="default_torrent.png";  

if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image1))
echo "<a href=details.php?id=".$row["id"]."><img class=effects onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effects'\" title=\"".htmlspecialchars($row["name"])."\" src=\"".$image1."\" height=\"140\" width=\"140\"/><br>".htmlspecialchars_uni($row["name"])."</a>";
else
echo "<a href=details.php?id=".$row["id"]."><img class=effects onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effects'\" title=\"".htmlspecialchars($row["name"])."\" src=\"thumbnail.php?image=".$image1."&for=block\" height=\"140\" width=\"140\"/><br>".htmlspecialchars_uni($row["name"])."</a>";

if (!empty($row["webseed"])){
$row['seeders']=$row['seeders']+1;
}

echo '<b><div align="center"><font color=blue>Скачан: '. round($row['times_completed']) .'  </font><br><font color=red>Раздают: '. ($row['f_seeders']) .'  </font> <font color=green>Качают: '. ($row['f_leechers']).'</b></div></font>';

if ($row["owner"]==$row["moderatedby"]){

echo "<b>Залито и Одобрено:<br> ".($row["us"] ? "<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row["cl"],$row["us"])."</a>":"нет автора")."</b>\n";
}
 else
{
echo "<b>Залил: ".($row['us'] ? "<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row['cl'],$row['us'])."</a>":"нет автора")."

</b><br>";
 
if ($row["moderated"] == "yes"){
echo "<b>Одобрил:</b> <b><a href=\"userdetails.php?id=$row[moderatedby]\">". get_user_class_color($row["classname"], $row["classusername"]) . "	</a></b><br>\n";} else {
echo "<b>Одобрено:</b> <b>Нет</b><br>\n";
}
}     
       
echo "</td>";
        ++$nc;
        if ($nc == 5) { $nc=1; echo "</tr>"; }
    ++$numi;
	  //  if ($nc == 9) { $nc=1; echo"</tr>"; }
    }
}
echo "</tr></table>";

}

if ($numi==0)
die("<center>Нет внешних топов за последнюю неделю.</center>\n");	

}
/// топ внешних
elseif ($id=="snat"){
$res = sql_query("SELECT t.times_completed,t.name,t.owner, t.webseed, t.f_seeders,t.f_leechers,t.seeders,t.leechers,t.image1,t.id,t.hits, t.views,t.moderatedby, t.moderated,users.username AS us,users.class AS cl,
 m.class AS classname, m.username AS classusername
FROM torrents AS t
LEFT JOIN users ON t.owner = users.id
LEFT JOIN users AS m ON t.moderatedby = m.id
WHERE t.banned = 'no' and t.moderated = 'yes' and (t.f_seeders+t.f_leechers+t.seeders+t.leechers)>=1 and t.added > DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY t.hits DESC LIMIT 0 , 8") or sqlerr(__FILE__, __LINE__);

$num=1;
$numi=0;

if ($num > 0) {
echo "<table border=\"1\" cellspacing=\"0\" style=\"border-collapse: collapse\" class=\"main\" width=\"100%\" id=\"table\">";
    $nc=1;
    
for ($i = 0; $i < $num; ++$i) {
	
while ($row=mysql_fetch_array($res)){
///while ($row=$cache->fetch_assoc()){	
$num=1;

if ($nc == 1) { $content.="<tr>"; }
echo "<td>";

echo "<td align=\"center\" valign=\"top\" width=\"25%\">"; 

$image1 = htmlentities($row["image1"]);

if(empty($image1))
$image1="default_torrent.png";  

if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image1))
echo "<a href=details.php?id=".$row["id"]."><img class=effects onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effects'\" title=\"".htmlspecialchars($row["name"])."\" src=\"".$image1."\" height=\"140\" width=\"140\"/><br>".htmlspecialchars_uni($row["name"])."</a>";
else
echo "<a href=details.php?id=".$row["id"]."><img class=effects onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effects'\" title=\"".htmlspecialchars($row["name"])."\" src=\"thumbnail.php?image=".$image1."&for=block\" height=\"140\" width=\"140\"/><br>".htmlspecialchars_uni($row["name"])."</a>";

if (!empty($row["webseed"])){
$row['seeders']=$row['seeders']+1;
}

echo '<b><div align="center"><font color=blue>Взяли раз: '. round($row['hits']) .' </font><br><font color=red>Раздают: '. round($row['f_seeders']+$row['seeders']) .' </font> <font color=green>Качают: '. round($row['f_leechers']+$row['leechers']).'</b></div></font>';

if ($row["owner"]==$row["moderatedby"]){

echo "<b>Залито и Одобрено:<br> ".($row["us"] ? "<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row["cl"],$row["us"])."</a>":"нет автора")."</b>\n";
}
 else
{
echo "<b>Залил: ".($row['us'] ?"<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row['cl'],$row['us'])."</a>":"нет автора")."

</b><br>";
 
if ($row["moderated"] == "yes"){
echo "<b>Одобрил:</b> <b><a href=\"userdetails.php?id=$row[moderatedby]\">". get_user_class_color($row["classname"], $row["classusername"]) . "	</a></b><br>\n";} else {
echo "<b>Одобрено:</b> <b>Нет</b><br>\n";
}
}     
       
echo "</td>";
        ++$nc;
        if ($nc == 5) { $nc=1; echo "</tr>"; }
    ++$numi;
	  //  if ($nc == 9) { $nc=1; echo"</tr>"; }
    }
}
echo "</tr></table>";

}

if ($numi==0)
die("<center>Ни один торрент не был взят за последнюю неделю.</center>\n");	

}

/// топ оценок
elseif ($id=="mark"){
	
$res = sql_query("SELECT t.numratings,t.ratingsum,t.f_seeders,t.f_leechers,t.name,t.seeders,t.owner, t.webseed, t.leechers,t.image1,t.id,t.moderatedby, t.moderated,users.username AS us,users.class AS cl,
 m.class AS classname, m.username AS classusername
FROM torrents AS t
LEFT JOIN users ON t.owner = users.id
LEFT JOIN users AS m ON t.moderatedby = m.id
WHERE t.banned = 'no' and t.moderated = 'yes' and t.added > DATE_SUB(NOW(), INTERVAL 7 DAY) AND (t.ratingsum > t.numratings) AND ratingsum IS NOT NULL
ORDER BY t.ratingsum DESC LIMIT 0 , 8") or sqlerr(__FILE__, __LINE__);

$num=1;
$numi=0;

if ($num > 0) {
echo "<table border=\"1\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"100%\" class=\"main\" id=\"table\">";
$nc=1;
    
for ($i = 0; $i < $num; ++$i) {
	
while ($row=mysql_fetch_array($res)){
///while ($row=$cache->fetch_assoc()){	
$num=1;

if ($nc == 1) {
$content.="<tr>"; 
}
echo "<td>";

echo "<td align=\"center\" valign=\"top\" width=\"25%\">"; 

$image1 = htmlentities($row["image1"]);

if(empty($image1))
$image1="default_torrent.png";  

if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image1))
echo "<a href=details.php?id=".$row["id"]."><img class=effects onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effects'\" title=\"".htmlspecialchars($row["name"])."\" src=\"".$image1."\" height=\"140\" width=\"140\"/><br>".htmlspecialchars_uni($row["name"])."</a>";
else
echo "<a href=details.php?id=".$row["id"]."><img class=effects onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effects'\" title=\"".htmlspecialchars($row["name"])."\" src=\"thumbnail.php?image=".$image1."&for=block\" height=\"140\" width=\"140\"/><br>".htmlspecialchars_uni($row["name"])."</a>";

if (!empty($row["webseed"])){
$row['seeders']=$row['seeders']+1;
}

echo '<b><div align="center"><font color=blue>Средняz оценка: '. round($row["ratingsum"]/$row["numratings"],1) .'  </font><br><font color=red>Раздают: '. ($row['seeders']+$row['f_seeders']) .'  </font> <font color=green>Качают: '. ($row['leechers']+$row['f_leechers']).'</b></div></font>';

if ($row["owner"]==$row["moderatedby"]){

echo "<b>Залито и Одобрено:<br> ".($row["us"] ? "<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row["cl"],$row["us"])."</a>":"нет автора")."</b>\n";
}
 else
{
echo "<b>Залил: ".($row['us'] ?"<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row['cl'],$row['us'])."</a>":"нет автора")."

</b><br>";
 
if ($row["moderated"] == "yes"){
echo "<b>Одобрил:</b> <b><a href=\"userdetails.php?id=$row[moderatedby]\">". get_user_class_color($row["classname"], $row["classusername"]) . "	</a></b><br>\n";} else {
echo "<b>Одобрено:</b> <b>Нет</b><br>\n";
}
}
       
echo "</td>";
        ++$nc;
        if ($nc == 5) { $nc=1; echo "</tr>"; }
    ++$numi;
	  //  if ($nc == 9) { $nc=1; echo"</tr>"; }
    }
}
echo "</tr></table>";

}

if ($numi==0)
die("<center>Нет оцененных раздач за последнюю неделю.</center>\n");	
}

else die("Не выбрана категория для просмотра.");

}

?>
