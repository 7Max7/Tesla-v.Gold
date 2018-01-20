<?
require_once("include/bittorrent.php");

dbconn(false);
loggedinorreturn();

if (get_user_class() < UC_SYSOP){
attacks_log('friensblockadd'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}


//// поиск по url из таблицы referer

accessadministration();

function bark($msg) {
stdhead();
stdmsg("Ошибка!", $msg);
stdfoot();
exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER["REQUEST_METHOD"] <> "GET"){

if (($_POST["image"] || $_POST["descr"] || $_POST["url"]) && empty($_POST["action"])){

if (empty($_POST["image"]))
bark("Введите URL адрес кнопки.");

if (!preg_match('#^((http)|(https):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $_POST["image"])){
bark("Неверный URL Кнопки");
}
$image = sqlesc(htmlspecialchars($_POST["image"]));

if (empty($_POST["descr"]))
bark("Введите описание.");
$descr = sqlesc(htmlspecialchars($_POST["descr"]));

if (empty($_POST["url"]))
bark("Введите url сайта.");

if (!preg_match("/^(http(s)?:\/\/)(([^\/]+\.)+)\w{2,}(\/)?.*$/i", $_POST["url"])){
stderr("Ошибка", "Неверный URL Сайта");
}

$url = sqlesc(htmlspecialchars($_POST["url"]));

$added = sqlesc(get_date_time());

$visible=sqlesc($_POST["visible"]=="yes"? "yes":"no");

sql_query("INSERT INTO friendsblock (added,image,descr,url,visible) VALUES($added,$image,$descr,$url,$visible)") or sqlerr(__FILE__, __LINE__);
$user = $CURUSER["username"];
$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER[username]);

write_log("Пользователь $user добавил друга $descr ($url)","$user_color","other");

@unlink(ROOT_PATH."cache/block-friends.txt");

//$id = mysql_insert_id();
//header("Refresh: 0; url=friensblockadd.php");
@header("Location: friensblockadd.php");
}
}

////
$action = htmlentities($_GET["action"]);

if (empty($action))
$action = htmlentities($_POST["action"]);


if ($action == 'delete'){
$id = (int) $_GET["id"];

if (!is_valid_id($id))
stderr("Ошибка","Не верный id");

$sure = $_GET["sure"];

if (!$sure)
stderr("Удалить","Действительно удалить? Жми\n" . "<a href=".htmlentities($_SERVER['PHP_SELF'])."?action=delete&id=$id&sure=1>сюда</a> если уверены.");

$query = sql_query("SELECT descr,url FROM friendsblock WHERE id=".sqlesc($id)."") or sqlerr(__FILE__, __LINE__); 
$query_view = mysql_fetch_array($query);
$descr=$query_view["descr"];
$url=$query_view["url"];

sql_query("DELETE FROM friendsblock WHERE id=".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);

$user = $CURUSER["username"];
$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER[username]);

write_log("Пользователь $user удалил друга $descr ($url)","$user_color","other");

@header("Location: friensblockadd.php");
}

if ($action == 'vi'){
$id = (int) $_GET["id"];
$vis = ($_GET["visible"]=="yes"? "yes":"no");

$ress = mysql_query("SELECT visible FROM friendsblock WHERE id=".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$arrr = mysql_fetch_array($ress);
$visk=$arrr["visible"];

if ($vis<>$visk){
sql_query("UPDATE friendsblock SET visible='$vis' WHERE id=".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
}

@header("Location: friensblockadd.php");
}

if ($action == 'edit'){

$id = (int) $_GET["id"];

if (!is_valid_id($id))
stderr("Ошибка","Не верный id");

$res = sql_query("SELECT * FROM friendsblock WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) != 1)
stderr("Ошибка", "Нет Друзей с ID $id.");

$arr = mysql_fetch_array($res);

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

$image = $_POST['image'];
if ($image == "")
stderr("Ошибка", "Введите URL Кнопки!");

if (!preg_match('#^((http)|(https):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image)){
bark("Неверный URL Кнопки");
}

$descr = trim($_POST['descr']);

if (empty($descr))
stderr("Ошибка", "Введите описание!");

$url = trim($_POST['url']);

if (empty($url))
stderr("Ошибка", "Введите URL Сайта!");

if (!preg_match("/^(http(s)?:\/\/)(([^\/]+\.)+)\w{2,}(\/)?.*$/i", $url)){
stderr("Ошибка", "Неверный URL Сайта");
}

$image = sqlesc(htmlspecialchars_uni($image));
$descr = sqlesc(htmlspecialchars_uni($descr));
$url = sqlesc(htmlspecialchars_uni($url));
 
$visible=sqlesc($_POST["visible"]=="yes"? "yes":"no");

sql_query("UPDATE friendsblock SET image=$image,descr=$descr,url=$url,visible=$visible WHERE id=".sqlesc($id)."") or sqlerr(__FILE__, __LINE__); 

@header("Location: friensblockadd.php");

}
else
{
//$returnto = htmlentities($_GET['returnto']);

stdhead();
begin_frame("Редактировать .:. " . htmlspecialchars($arr["url"]) . "");

print("<form method=post action=friensblockadd.php?action=edit&id=$id>\n");
print("<table width=100% border=0 cellspacing=0 cellpadding=5>
<input type=\"hidden\" name=\"action\" value=\"edit\" />
\n");

print("<tr><td><b>URL Кнопки</b>: </td><td colspan=2 align=left><input type=text size=90 name=image value=\"" . htmlspecialchars_uni($arr["image"]) . "\"><br></tr>\n");
print("<tr><td><b>Описание</b>: </td><td colspan=2 align=left><textarea name=descr rows=4 cols=90>" . htmlspecialchars_uni($arr["descr"]) . "</textarea></tr>\n");
print("<tr><td><b>URL Сайта</b>: </td><td colspan=2 align=left><input type=text size=90 name=url value=\"" . htmlspecialchars_uni($arr["url"]) . "\"><br></tr>\n");
print("<tr><td><b>Видимый в блоке</b>: </td><td colspan=2 align=left><input type=\"radio\" name=\"visible\"" . ($arr["visible"] == "yes" ? " checked" : "") . " value=\"yes\">Да <input type=\"radio\" name=\"visible\"" . ($arr["visible"] == "no" ? " checked" : "") . " value=\"no\">Нет<br /></tr>\n");
print("<tr><td></td>
<td colspan=2 align=left><input type=submit value='Отредактировать' class=btn><br></tr>
\n");

print("</form></table>\n");

end_frame();
stdfoot();
die("Тут ничего нет");
}

@header("Location: friensblockadd.php");
}

//if (!$action == 'edit' && !$action == 'delete')
//stderr("Ошибка", "Непонятное действие для обработки.");


//////////////////
stdhead("Добавить в друзья");
begin_frame("Добавить в друзья"); 
?>
<div align=center>
<form action="friensblockadd.php" method="post">
<table border="0" cellspacing="0" cellpadding="5">
<?
tr("URL Картинки", "<input type=\"text\" name=\"image\" size=\"80\" /><br>пример <a href=http://imageshack.us>http://imageshack.us/baner_logo.gif</a>", 1); 
tr("Описание", "<textarea name=\"descr\" rows=\"10\" cols=\"80\"></textarea>", 1);
tr("URL Сайта", "<input type=\"text\" name=\"url\" size=\"80\" /><br />\n", 1); 
tr("Видимый в блоке", "<input type=\"radio\" name=\"visible\" checked value=\"yes\">Да <input type=\"radio\" name=\"visible\" value=\"no\">Нет\n", 1); 
?>
<tr><td align="center" colspan="2"><input type="submit" value="Добавить!" /></td></tr>
</table>
</form>
<?
end_frame();
$res2 = sql_query("SELECT count(*) FROM friendsblock") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res2);
$url = " .$_SERVER[PHP_SELF]?";
$count = $row[0];
$perpage = 15;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $url);

begin_frame("Редактировать Друзей");

if ($count == 0)
print("<p align=center><b>Извините тут ничего, нет :(</b></p>\n");
else
{
echo ("<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"100%\">");
echo("<tr>
<td class=\"a\" width=\"88px\">Кнопка</td>
<td class=\"a\" align=\"left\">Описание</td>
<td class=\"a\">URL Сайта / Добавлен / Блок</td>
<td class=\"a\" width=\"5%\">Действие</td>
</tr>");


/*
$res = sql_query("SELECT *,
(SELECT COUNT(*) FROM reaway WHERE frie.url=reaway.parse_url) AS count_away,
(SELECT COUNT(*) FROM referrers WHERE frie.url=referrers.parse_url) AS count_ref
FROM friendsblock AS frie ORDER BY frie.added DESC ".$limit) or sqlerr(__FILE__, __LINE__);
*/


$res = sql_query("SELECT * FROM friendsblock ORDER BY added DESC $limit") or sqlerr(__FILE__, __LINE__);
$num = 0;
while ($arr = mysql_fetch_assoc($res)) {

if ($num%2==0){
$cl1 = "class = \"b\"";
$cl2 = "class = \"a\"";
} else {
$cl2 = "class = \"b\"";
$cl1 = "class = \"a\"";
}

$url = htmlspecialchars_uni($arr["url"]);

$p_se = parse_url($url);
//print_r($p_se);


$count_ref = number_format(get_row_count("referrers", "WHERE parse_url = ".sqlesc($p_se["host"])." OR parse_url = ".sqlesc($url).""));

$count_away = number_format(get_row_count("reaway", "WHERE parse_url = ".sqlesc($p_se["host"])." OR parse_url = ".sqlesc($url).""));



echo "<tr>";

echo "<td ".$cl2." valign=\"top\" width=\"88px\">".($arr["visible"]=="yes"? "<a href=friensblockadd.php?action=vi&visible=no&id=" . $arr['id'] . "><img title=\"Скрыть показ в блоке\" src=\"".$arr["image"]."\" border=0></a>":"<a href=friensblockadd.php?action=vi&visible=yes&id=".$arr['id']."><img title=\"Показывать в блоке\" src=\"".$arr["image"]."\" border=0></a>")."</td>";

echo "<td ".$cl1." valign=\"top\" align=\"left\">

<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"100%\">

<tr>
<td class=\"b\" align=\"left\" colspan=\"2\">".htmlspecialchars_uni($arr["descr"])."</td>
</tr>


<tr>
<td class=\"b\" align=\"center\"><small>".(!empty($count_away) ? "<font color=\"green\">Переходы: $count_away</font>":"<font color=\"red\">Переходов нет</font>")."</small></td>

<td class=\"b\" align=\"center\"><small>".(!empty($count_ref) ? "<font color=\"green\">Рефералы: $count_ref</font>":"<font color=\"red\">Рефералов нет</font>")."</small></td>
</tr>

</table>

</td>";

echo "<td ".$cl2." valign=\"top\" align=\"left\">


<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"100%\">

<tr>
<td class=\"b\" align=\"center\" colspan=\"2\"><a href=\"".$url."\">".$url."</a></td>
</tr>


<tr>
<td class=\"b\" width=\"50%\" align=\"center\"><small>".$arr["added"]."</small></td>

<td class=\"b\" width=\"50%\" align=\"center\"><small>".($arr["visible"]=="yes" ? "<font color=\"green\">Показан в блоке</font>":"<font color=\"red\">Скрыт в блоке</font>")."</small></td>

</tr>

</table>

</td>";

echo "<td ".$cl1." valign=\"top\" width=\"5%\" align=\"center\">
<a href=\"friensblockadd.php?action=edit&id=" . $arr['id'] . "\">
<img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/pen.gif\" alt=\"Редактировать\" title=\"Редактировать\"/>
</a> <a href=\"friensblockadd.php?action=delete&id=" . $arr['id'] . "\">
<img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/disabled.gif\" alt=\"Удалить\" title=\"Удалить\"/>
</a>
</td>";

echo "</tr>";

++$num;
}

echo "</table>";

echo $pagerbottom;
}

end_frame();
stdfoot();


?>