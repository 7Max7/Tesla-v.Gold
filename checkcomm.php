<? 
require_once("include/bittorrent.php"); 
dbconn(false); 
loggedinorreturn(); 

parked();

if (get_user_class() < UC_USER)
	stderr($tracker_lang['error'], $tracker_lang['access_denied']);



if (isset($_POST["delete"]) && $_POST["delete"]=="allchecks"){

$from=getenv("HTTP_REFERER"); 
$host=getenv("REQUEST_URI"); 

if ($from){
@header("Refresh: 3; url=$from");
} else {
@header("Refresh: 3; url=details.php?id=$tid");
}

sql_query("DELETE FROM checkcomm WHERE userid=".$CURUSER["id"]) or sqlerr(__FILE__,__LINE__);

stdhead("Успешно очищен список слежений");

stdmsg("Успешно", "Очищен список слежений. Автообновление страницы...", 'success');
stdfoot();
exit;
}


stdhead("Список слежений за торрент релизами");

$count_res = mysql_query("SELECT COUNT(*) FROM checkcomm WHERE userid = $CURUSER[id]"); 
$count_row = mysql_fetch_array($count_res); 
$count = $count_row[0]; 
if (!$count) {
    stdmsg($tracker_lang['error'],"Список слежений пуст, нет ни одного подписанного релиза."); 
    stdfoot(); 
    die(); 
}
$perpage = 30;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "checkcomm.php?");



echo "<table class=\"main\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">
<tr><td class=\"colhead\" align=\"center\" colspan=\"12\">Список слежений [<a class=\"altlink_white\" href=\"bookmarks.php\">Список закладок</a>]</td></tr>";

print("<tr><td class=\"index\" colspan=\"12\">");
print($pagertop);
print("</td></tr>");



print("<tr>
<td width=\"2%\" class=\"colhead\">&nbsp;№</td>
<td width=\"73%\" class=\"colhead\">".$tracker_lang['name']."</td>
<td width=\"25%\" class=\"colhead\">Последний комментарий</td>
</tr>"); 
$res = sql_query("SELECT * FROM checkcomm WHERE userid = $CURUSER[id] ORDER BY id DESC $limit") or sqlerr(__FILE__, __LINE__); 
while ($arr = mysql_fetch_assoc($res)) {
$id = $arr['checkid']; 


if ($arr['torrent']<>0){
$resu = mysql_query("SELECT user, added from comments where torrent = $id and offer = 0 ORDER BY added DESC limit 1") or sqlerr(__FILE__,__LINE__);
$rowu = mysql_fetch_assoc($resu);
if (!$rowu["user"]) {
$lastcommentd = "<center><i>комментариев нет</i></center>";
} else {
$res2 = mysql_query("SELECT username, class FROM users WHERE id = $rowu[user]") or sqlerr(__FILE__, __LINE__); 
$row2 = mysql_fetch_assoc($res2); 
$lastcommentd = "от ".($row2["username"]? "<a class=index href=userdetails.php?id=$rowu[user]>".get_user_class_color($row2["class"] ,$row2["username"])."</a>":"<b>id: $rowu[user]</b>")." &nbsp; <a title=\"Просмотреть сообщение\" class=\"index\" href=details.php?id=$id&hit=1&tocomm=1><img border=0 src=\"pic/pm.gif\"></a></br>$rowu[added]"; }
$res1 = sql_query("SELECT name from torrents where id = $id") or sqlerr(__FILE__, __LINE__); 
$row1 = mysql_fetch_assoc($res1); 

$link = "".($row1["name"] ? "<a href=\"details.php?id=$id&hit=1\">$row1[name]</a></br><font color=blue>Торрент файл</font> | <a class=index href=comment.php?action=checkoff&amp;tid=$id>Отключить слежение</a></i>":"Нет файла </br><font color=blue>Торрент файл</font> | <a class=index href=comment.php?action=checkoff&amp;tid=$id>Отключить слежение</a>")."";

//$link = "<a href=\"detailsoff.php?id=$id\">$rowa[anounce]</a></br><i>$typen | <a class=\"index\" href=commentoff.php?action=checkoff&amp;tid=$id><font color=black>Отключить слежение</font></a></i>";

print("<tr>
<td width=\"2%\"><img src=\"pic/pn_inbox.gif\" alt=\"Нет новых комментарий\" border=\"0\" /></td>
<td width=\"78%\">$link</td>
<td width=\"20%\">$lastcommentd</td>
</tr>"); 




}

if ($arr['offer']<>0){
$resu2 = mysql_query("SELECT user, added FROM comments WHERE offer = $id and torrent=0 ORDER BY added DESC limit 1") or sqlerr(__FILE__,__LINE__);
$rowu2 = mysql_fetch_assoc($resu2);
if (!$rowu2["user"]) {
$lastcommentd = "<center><i>комментариев нет</i></center>";
} else {
$res2 = mysql_query("SELECT username, class FROM users WHERE id = $rowu2[user]") or sqlerr(__FILE__, __LINE__); 
$row2 = mysql_fetch_assoc($res2); 
$lastcommentd = "от ".($row2["username"]? "<a class=index href=userdetails.php?id=$rowu2[user]>".get_user_class_color($row2["class"] ,$row2["username"])."</a>":"<b>id: $rowu2[user]</b>")." &nbsp; <a title=\"Просмотреть сообщение\" class=\"index\" href=details.php?id=$id&hit=1&tocomm=1><img border=0 src=\"pic/img-resized.png\"></a></br>$rowu2[added]"; }
$res1 = sql_query("SELECT name FROM off_reqs where id = $id") or sqlerr(__FILE__, __LINE__); 
$row1 = mysql_fetch_assoc($res1); 

$link = "<a href=\"detailsoff.php?id=$id&hit=1\">$row1[name]</a></br><font color=red>Запрос</font> | <a class=index href=commentoff.php?action=checkoff&amp;tid=$id>Отключить слежение</a>";

//$link = "<a href=\"detailsoff.php?id=$id\">$rowa[anounce]</a></br><i>$typen | <a class=\"index\" href=commentoff.php?action=checkoff&amp;tid=$id><font color=black>Отключить слежение</font></a></i>";

print("<tr>
<td width=\"2%\"><img src=\"pic/pn_inbox.gif\" alt=\"Нет новых комментарий\" border=\"0\" /></td>
<td width=\"78%\">$link</td>
<td width=\"20%\">$lastcommentd</td>
</tr>"); 




}

}


print("<tr><td class=\"index\" colspan=\"12\">");
print($pagerbottom);
print("</td></tr>");

if ($count>"100"){
	 echo "<tr><td class=\"index\" colspan=\"12\" align=\"center\">
	<form method=\"post\" action=\"checkcomm.php\">
	<input type=\"hidden\" name=\"delete\" value=\"allchecks\"/>
	<input type=\"submit\" value=\"Нажмите здесь, если хотите Почистить все слежения за торрент релизами\">
	</form>
	</td>
	</tr>";
}


print("</table>");


//end_table(); 
//end_frame(); 
stdfoot(); 
?> 