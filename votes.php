<?
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn();

if (get_user_class() < UC_MODERATOR)
    stderr("Ошибка", "Доступ запрещен.");

$res = sql_query("SELECT COUNT(*) FROM ratings ORDER BY user DESC");
$row = mysql_fetch_array($res);
$count = $row[0];

$pea=120;
list($pagertop, $pagerbottom, $limit) = pager($pea, $count, $_SERVER["PHP_SELF"] ."?");

$res = sql_query("SELECT r.*, t.name, u.username, u.class FROM ratings AS r LEFT JOIN torrents AS t ON t.id = r.torrent LEFT JOIN users AS u ON u.id = r.user ORDER BY user $limit") or sqlerr(__FILE__, __LINE__);
stdheadchat("Обзор оценок");
begin_frame("Обзор оценок [$count]");
print("<table width=\"100%\">\n");
print($pagertop);
print("<tr>
<td class=\"a\" align=\"center\">#</td>
<td class=\"a\" align=\"center\">Торрент</td>
<td class=\"a\" align=\"center\">Пользователь</td>
<td class=\"a\" align=\"center\">Оценка</td>
<td class=\"a\" align=\"center\">Дата</td></tr>\n");
if ($count > 0)
{
    $num = 1;
    while($row = mysql_fetch_array($res))
    {
        if (empty($row['name']))
            $name = "Торрент удален: " . $row['torrent'] . "";
        else
            $name = "<a href=\"details.php?id=" . $row['torrent'] . "\">" . $row['name'] . "</a>";
        if (empty($row['username']))
            $user = "Аноним: " . $row['user'] . "";
        else
            $user = "<a href=\"userdetails.php?id=" . $row['user'] . "\">" . get_user_class_color($row['class'], $row['username']) . "</a>";
        print("<tr>
		<td>" . ($num < 10 ? "0$num" : $num) . ".</td>
		<td >$name</td>
		<td align=\"center\">$user</td>
		<td align=\"center\">" . pic_rating_b(10,$row['rating']) . "</td>
		<td align=\"center\">" . normaltime($row['added'],true) . "</td></tr>\n");
        $num++;
    }
    print("<tr><td colspan=\"5\">\n");
    print($pagerbottom);
    print("</td></tr>\n");
}
else
{
    print("<tr><td>Нет оценок.</td></tr>\n");
}
print("</table>\n");
end_frame();
stdfootchat();

?>