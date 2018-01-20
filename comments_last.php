<?
require "include/bittorrent.php";

dbconn(false);
loggedinorreturn();
stdheadchat("Последние 100 комментариев");

echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"block\">
<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\"><tr>
<td align=\"center\" class=\"a\">Последние 40 комментариев : <a href=comments_week.php>За последнюю неделю</a></tr>
<table border=\"0\" cellspacing=\"0\" width=\"100%\" cellpadding=\"3\">
<tr>
<td class=colhead>Торрент раздача</td>
<td class=colhead>Комментарий</td>
<td class=colhead>Автор последнего коммента</td>
</tr>";

$res = sql_query("SELECT *,tor.name AS toname,users.username AS usname, users.class AS usclass
FROM comments 
LEFT JOIN torrents AS tor ON tor.id=comments.torrent
LEFT JOIN users ON users.id=comments.user
WHERE comments.torrent IS NOT NULL AND tor.name IS NOT NULL 
ORDER BY comments.added DESC limit 100") or sqlerr(__FILE__, __LINE__);
$nau = 0;
while ($row = mysql_fetch_array($res)) {

if ($nau%2==0){
$classa = "class = \"b\"";
$classb = "class = \"a\"";
} else {
$classb = "class = \"b\"";
$classa = "class = \"a\"";
}

$title = htmlspecialchars_uni($row['toname']);
$desc = trim($row['text']);
$id = $row['torrent'];

if (empty($row["usname"])){
$row["usname"] = "<font color='red'>Гость<font>";
}

echo "<tr>";

echo "<td ".$classa."><a target=\"_blank\" href=\"details.php?id=".$id."\" title=\"".$title."\">".$title."</a></br></td>";

echo "<td ".$classb.">".format_comment($desc)."</td>";

echo "<td ".$classa."><a target=\"_blank\" href=\"userdetails.php?id=".$row["user"]."\">".get_user_class_color($row["usclass"],$row["usname"])."</a></td>";

echo "</tr>";
++$nau;
}

echo "</table></form></td>
</tr></table>
</td></tr></table>";

//echo "<br><FORM><INPUT TYPE=\"button\" VALUE=\"Назад на предыдущую страницу\" onClick=\"history.back()\"></FORM>";

stdfootchat();
?> 