<?
require "include/bittorrent.php";
dbconn(true);
loggedinorreturn();

function bark($msg) {
genbark($msg, "Ошибка!");
}



if (get_user_class() < UC_ADMINISTRATOR)
 {
attacks_log('adminbookmark'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

if (!empty($_POST["book"])) {
		$res=sql_query("SELECT * FROM users WHERE id IN (" . implode(", ", array_map("sqlesc", $_POST["book"])) . ") ") or sqlerr(__FILE__, __LINE__);
		$user = mysql_fetch_array($res);
		$username = $user["username"];
		$email=$user["email"];
		$class=$user["class"];
	//		die($class);
		if ($username  && $CURUSER["class"]<>$class && $CURUSER["class"]>$class){
		
		sql_query("DELETE FROM users WHERE id IN (" . implode(", ", array_map("sqlesc", $_POST["book"])) . ") ") or sqlerr(__FILE__, __LINE__);
		sql_query("DELETE FROM messages WHERE receiver IN (" . implode(", ", array_map("sqlesc", $_POST["book"])) . ") ") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM friends WHERE userid IN (" . implode(", ", array_map("sqlesc", $_POST["book"])) . ") ") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM friends WHERE friendid IN (" . implode(", ", array_map("sqlesc", $_POST["book"])) . ") ") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM bookmarks WHERE userid IN (" . implode(", ", array_map("sqlesc", $_POST["book"])) . ") ") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM invites WHERE inviter IN (" . implode(", ", array_map("sqlesc", $_POST["book"])) . ") ") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM peers WHERE userid IN (" . implode(", ", array_map("sqlesc", $_POST["book"])) . ") ") or sqlerr(__FILE__,__LINE__);

		sql_query("DELETE FROM simpaty WHERE fromuserid IN (" . implode(", ", array_map("sqlesc", $_POST["book"])) . ") ") or sqlerr(__FILE__,__LINE__);

		sql_query("DELETE FROM checkcomm WHERE userid IN (" . implode(", ", array_map("sqlesc", $_POST["book"])) . ") ") or sqlerr(__FILE__,__LINE__);

		sql_query("DELETE FROM sessions WHERE uid IN (" . implode(", ", array_map("sqlesc", $_POST["book"])) . ") ") or sqlerr(__FILE__,__LINE__);
		
			$avatar=$user["avatar"];
		if ($avatar)
		@unlink(ROOT_PATH."pic/avatar/$avatar");
		@unlink(ROOT_PATH."cache/monitoring_$userid.txt");
		
		$deluserid=$CURUSER["username"];
		write_log("Пользователь $username был удален пользователем $deluserid. Причина: был в Подозрении","590000","bans");
		}
header("Refresh: 0; url=adminbookmark.php"); 
die;
	}


stdhead("Закладки администратора");
//begin_main_frame();
$addbookmark = number_format(get_row_count("users WHERE addbookmark='yes'"));
$res2 = sql_query("SELECT count(id) FROM users WHERE addbookmark='yes'") or die(mysql_error());
$row = mysql_fetch_array($res2);
$url = " .$_SERVER[PHP_SELF]?";
$count = $row[0];
$perpage = 20;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $url);
//if ($count == !0)
//echo $pagerbottom;
if ($count == 0) {
	stderr($tracker_lang['error'], "Нет подозреваемых");
}
else
{
begin_frame("Общее количество подозреваемых: $addbookmark", true);
?>
<form method="post" action="adminbookmark.php">
<?


print("<table cellpadding=2 cellspacing=1 border=0 style=width:100%><tr>

<td class=a align=center><b>Пользователь</b></td>
<td class=a align=center><b>Подозрение</b></td>
<td class=a align=center><b>Залил</b></td>
<td class=a align=center><b>Скачал</b></td>
<td class=a align=center><b>Рейтинг</b></td>
<td class=a align=center><b>Удалить</b></td></tr>\n");
 }
$res=mysql_query("SELECT id,username,class, bookmcomment,added,uploaded,downloaded FROM users WHERE addbookmark='yes' ORDER BY id DESC $limit") or sqlerr(__FILE__,__LINE__);

while ($arr = @mysql_fetch_assoc($res)) {
if($arr["downloaded"] != 0){
$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
} else {
$ratio="---";
}
$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
$uploaded = mksize($arr["uploaded"]);
$downloaded = mksize($arr["downloaded"]);
$uploaded = str_replace(" ", "<br>", mksize($arr["uploaded"]));
$downloaded = str_replace(" ", "<br>", mksize($arr["downloaded"]));
echo "<tr>
<td class=b align=\"center\"><b><a href=userdetails.php?id=" . $arr[id] . ">".
"".get_user_class_color($arr["class"] ,$arr["username"])."</b></td>
<td class=b>" . $arr["bookmcomment"] . "</a></td>
<td class=b align=\"center\">" .  $uploaded . "</td></a></td>
<td class=b align=\"center\">" .$downloaded. "</td>
<td class=b align=\"center\">$ratio</td>

<td class=b align=\"center\">
".($CURUSER["class"]<>$arr["class"] && $CURUSER["class"]>$arr["class"] ? "<input type=\"checkbox\" name=\"book[]\" value=\"" . $arr["id"] . "\" />":"нельзя")."

</td>
</tr>";
}

if ($count == !0)
print("<tr><td colspan=7 align=right><input type=submit value='Удалить пользователя!' /></td></tr></form>\n");
 
end_frame();
end_main_frame();
if ($count == !0)
echo $pagerbottom; 

stdfoot(); 

?>