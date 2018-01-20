<?
require "include/bittorrent.php";

dbconn();
loggedinorreturn();

if (get_user_class() < UC_MODERATOR) {
attacks_log('warned');
stderr($tracker_lang['error'], "Отказано в доступе.");
}

stdhead("Предупрежденные пользователи");
$warned = number_format(get_row_count("users", "WHERE enabled='yes' and warned='yes'"));
begin_frame("Предупрежденные пользователи: ($warned)", true);
begin_table();

$res = sql_query("SELECT * FROM users WHERE warned='yes' AND enabled='yes' ORDER BY (users.uploaded/users.downloaded)") or sqlerr(__FILE__, __LINE__);
$num = mysql_num_rows($res);
print("<table border=1 width=100% cellspacing=0 cellpadding=2><form action=\"nowarn.php\" method=post>\n");
print("<tr align=center><td class=colhead width=90>Пользователь</td>
<td class=colhead width=70>Регистрация</td>
<td class=colhead width=75>Последний вход</td>

<td class=colhead width=70>Закачал</td>
<td class=colhead width=70>Раздал</td>
<td class=colhead width=45>Рейтинг</td>
<td class=colhead width=125>Окончание</td>
".(get_user_class() >= UC_ADMINISTRATOR ? "<td class=colhead width=65>Убрать</td>
<td class=colhead width=65>Отключить</td>":"")."
</tr>\n");
for ($i = 1; $i <= $num; $i++)
{
$arr = mysql_fetch_assoc($res);
if ($arr['added'] == '0000-00-00 00:00:00')
$arr['added'] = '-';
if ($arr['last_access'] == '0000-00-00 00:00:00')
$arr['last_access'] = '-';


if($arr["downloaded"] != 0){
$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
} else {
$ratio="---";
}
$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
$uploaded = mksize($arr["uploaded"]);
$downloaded = mksize($arr["downloaded"]);
// $uploaded = str_replace(" ", "<br />", mksize($arr["uploaded"]));
// $downloaded = str_replace(" ", "<br />", mksize($arr["downloaded"]));

$added = substr($arr['added'],0,10);
$last_access = substr($arr['last_access'],0,10);

print("<tr><td align=center><a href=userdetails.php?id=$arr[id]><b>
".get_user_class_color($arr["class"] ,$arr["username"])."</b>
</a>" .($arr["donor"] =="yes" ? "<img src=pic/star.gif border=0 alt='Донор'>" : "")."</td>
<td align=center>$added</td>
<td align=center>$last_access</td>
<td align=center>$downloaded</td>
<td align=center>$uploaded</td>
<td align=center>$ratio</td>
<td align=center>$arr[warneduntil]</td>
".(get_user_class() >= UC_ADMINISTRATOR ? "

".($arr["class"]<>$CURUSER["class"] ? "
<td bgcolor=\"#008000\" align=center><input type=\"checkbox\" name=\"usernw[]\" value=\"$arr[id]\"></td>
<td bgcolor=\"#FF000\" align=center><input type=\"checkbox\" name=\"desact[]\" value=\"$arr[id]\"></td>":"
<td align=center>ваш класс</td><td align=center>ваш класс</td>")."

":"")."
</tr>\n");
}
if (get_user_class() >= UC_ADMINISTRATOR) {
print("<tr><td colspan=10 align=right><input type=\"submit\" name=\"submit\" value=\"Применить\"></td></tr>\n");
print("<input type=\"hidden\" name=\"nowarned\" value=\"nowarned\"></form>\n");
}
print("</table><p>$pagemenu<br />$browsemenu</p>");

//end_frame();

end_table();
stdfoot();
?>