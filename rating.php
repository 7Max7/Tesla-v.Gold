<? 
require "include/bittorrent.php"; 
gzip();
dbconn(false); 
loggedinorreturn(); 


//sql_query("DELETE FROM snatched WHERE (SELECT COUNT(*) FROM torrents WHERE torrents.id=torrent)='0'") or sqlerr(__FILE__,__LINE__);



$res2 = sql_query("SELECT COUNT(*) FROM snatched WHERE (SELECT COUNT(*) FROM ratings WHERE torrent=snatched.torrent AND user=".$CURUSER["id"].") <'1' AND snatched.finished='yes' AND userid = ".sqlesc($CURUSER["id"])); 
$row = @mysql_fetch_array($res2); 
$count = $row[0];
$perpage = 50;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"]."?"); 

//// помечаем количество не прооценных торрентов
sql_query("UPDATE users SET unmark = ".sqlesc($count)." WHERE id = ".sqlesc($CURUSER["id"]));
//// помечаем количество не прооценных торрентов

if (empty($count)){
stderr("Внимание", "У вас все скачанные торренты оцененны. Спасибо за внимание к этой страничке.",true); 
die();
}

stdheadchat("Оценить скачанные торренты; $perpage из $count"); 
echo $pagertop; 

$res = sql_query("SELECT snatched.torrent AS id, snatched.startdat, snatched.completedat, categories.name AS catname, categories.image AS catimage, categories.id AS catid, torrents.name, IF(torrents.numratings < 1, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS ratingnum
FROM snatched
JOIN torrents ON torrents.id = snatched.torrent 
JOIN categories ON torrents.category = categories.id WHERE (SELECT COUNT(*) FROM ratings WHERE torrent=snatched.torrent AND user=".$CURUSER["id"].") <'1' AND snatched.finished='yes' AND userid = ".sqlesc($CURUSER["id"])." ORDER BY id DESC $limit") or sqlerr(__FILE__, __LINE__); 

echo "
<script type=\"text/javascript\" src=\"/js/jquery.js\"></script>
<script type=\"text/javascript\" src=\"/js/rating_begin.js\"></script>";


echo("<table border=\"0\" id=\"tesla_tto_rate\">\n"); 

echo "
<style>
.hideit {
    DISPLAY: none;VISIBILITY: hidden;
}
.showit {
	DISPLAY: table;VISIBILITY: visible;
}
.showit_inline {
	DISPLAY: inline;VISIBILITY: visible;
}
.showit_tr {
	DISPLAY: table-row;VISIBILITY: visible;
}
</style>";



echo("<tr>
<td width=\"1%\" class=a align=left>Категория</td>
<td class=a align=left>Название</td>
<td width=\"1%\" align=center class=a> </td>
<td width=\"20%\" align=center class=a>Ваша оценка</td>
<td width=\"4%\" class=a>#</td>
");

while ($arr = mysql_fetch_assoc($res)) {

$category="<a href=\"browse.php?incldead=1&cat=" . $arr["catid"]."\">";

if (!empty($arr["catimage"]))
$category.="<img border=\"0\" src=\"pic/cats/" . htmlentities($arr["catimage"]). "\" alt=\"" . $arr["catname"] . "\" />";
else
$category.=$arr["cat_name"];
$category.="</a>";
		

    $id=$arr["id"];
    
    $name = format_comment($arr["name"]);
	$name="<a href=\"details.php?id=".$arr["id"]."\">".$name."</a>";

   $ratingsum=$arr["ratingnum"];
   
   $startdat=normaltime($arr["startdat"],true);
   $completedat=normaltime($arr["completedat"],true);



  print("<tr>
  <td width=\"1%\" class=b align=left>".$category."</td>
  <td class=b align=left>".$name."</td>
  <td width=\"2%\" align=center class=b> - </td>

<td width=\"25%\" class=\"b\" align=center><div name=\"stelator\" id=\"t".$id."\">
<a href=\"takerate.php?torrentid=".$id."&amp;rate=1&amp;ajax=1\"><img id=\"stea_".$id."_1\" src=\"/pic/ratio/star_off.gif\" width=\"20\" height=\"20\" title=\"1\" alt=\"1\"></a><a href=\"takerate.php?torrentid=".$id."&amp;rate=2&amp;ajax=1\"><img id=\"stea_".$id."_2\" width=\"20\" height=\"20\" src=\"/pic/ratio/star_off.gif\" title=\"2\" alt=\"2\"></a><a href=\"takerate.php?torrentid=".$id."&amp;rate=3&amp;ajax=1\"><img id=\"stea_".$id."_3\" width=\"20\" height=\"20\" src=\"/pic/ratio/star_off.gif\" title=\"3\" alt=\"3\"></a><a href=\"takerate.php?torrentid=".$id."&amp;rate=4&amp;ajax=1\"><img id=\"stea_".$id."_4\" width=\"20\" height=\"20\" src=\"/pic/ratio/star_off.gif\" title=\"4\" alt=\"4\"></a><a href=\"takerate.php?torrentid=".$id."&amp;rate=5&amp;ajax=1\"><img id=\"stea_".$id."_5\" width=\"20\" height=\"20\" src=\"/pic/ratio/star_off.gif\" title=\"5\" alt=\"5\"></a><a href=\"takerate.php?torrentid=".$id."&amp;rate=6&amp;ajax=1\"><img id=\"stea_".$id."_6\" width=\"20\" height=\"20\" src=\"/pic/ratio/star_off.gif\" title=\"6\" alt=\"6\"></a><a href=\"takerate.php?torrentid=".$id."&amp;rate=7&amp;ajax=1\"><img id=\"stea_".$id."_7\"  width=\"20\" height=\"20\" src=\"/pic/ratio/star_off.gif\" title=\"7\" alt=\"7\"></a><a href=\"takerate.php?torrentid=".$id."&amp;rate=8&amp;ajax=1\"><img width=\"20\" height=\"20\" id=\"stea_".$id."_8\"  src=\"/pic/ratio/star_off.gif\" title=\"8\" alt=\"8\"></a><a href=\"takerate.php?torrentid=".$id."&amp;rate=9&amp;ajax=1\"><img id=\"stea_".$id."_9\" width=\"20\" height=\"20\"  src=\"/pic/ratio/star_off.gif\" title=\"9\" alt=\"9\"></a><a href=\"takerate.php?torrentid=".$id."&amp;rate=10&amp;ajax=1\"><img id=\"stea_".$id."_10\"  width=\"20\" height=\"20\" src=\"/pic/ratio/star_off.gif\" title=\"10\" alt=\"10\"></a>
</div>
</td>
<td class=\"a\" id=\"stea_nota_".$id."\" style=\"padding-top: 3px; padding-bottom: 3px; border: 0px;\"></td>
"); ///[Общая оценка ".round($ratingsum)."]
unset($rating);

}
echo "<script type=\"text/javascript\" src=\"js/rating_end.js\"></script>";
echo("</table>"); 


echo $on_main;
print($pagerbottom); 
stdfootchat(); 
?>