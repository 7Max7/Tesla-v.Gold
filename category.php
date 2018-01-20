<?php

ob_start();
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn();
if (get_user_class() < UC_SYSOP) {
die($tracker_lang['access_denied']);
}
accessadministration();

stdheadchat("Категории и Теги");
print("<table style='border:0;background:transparent;width:100%' cellspacing='0' cellpadding='2'><tr><td align='center' style='border:0;'>\n");

///////////////////// D E L E T E C A T E G O R Y \\\\\\\\\\\\\\\\\\\\\\\\\\\\

$sure = $_GET['sure'];
if($sure == "yes") {
$delid = (int) $_GET['delid'];
$query = "DELETE FROM categories WHERE id=" .sqlesc($delid) . " LIMIT 1";
$sql = sql_query($query);
@unlink(ROOT_PATH."cache/mydetails_category.txt");
begin_frame("Подтверждение удаления", true);
echo("Категория успешно удалена! [ <a href='category.php'>Назад</a> ]");
end_frame();
stdfootchat();
die();
}
$delid = (int) $_GET['delid'];
$name = htmlspecialchars($_GET['cat']);
if($delid > 0) {
begin_frame("Подтверждение удаления", true);
echo("Вы действительно хотите удалить эту категорию? ($name) ( <strong><a href=\"". $_SERVER['PHP_SELF'] . "?delid=$delid&cat=$name&sure=yes\">Да</a></strong> / <strong><a href=\"". $_SERVER['PHP_SELF'] . "\">Нет</a></strong> )");
end_frame();
stdfootchat();
die();

}

///////////////////// D E L E T E    TAG \\\\\\\\\\\\\\\\\\\\\\\\\\\\

$tagsure = $_GET['tagsure'];
if($tagsure == "yes") {
$deltagid = (int) $_GET['deltagid'];
$query = "DELETE FROM tags WHERE id=" .sqlesc($deltagid) . " LIMIT 1";
$sql = sql_query($query);
begin_frame("Подтверждение удаления", true);
echo("Тег успешно удален! [ <a href='category.php'>Назад</a> ]");
echo "<script>setTimeout('document.location.href=\"category.php\"', 3000);</script>";
@unlink(ROOT_PATH."cache/mydetails_category.txt");
end_frame();
stdfootchat();
die();
}
$deltagid = (int) $_GET['deltagid'];
if($deltagid > 0) {
begin_frame("Подтверждение удаления", true);


$deltagid_is=(int) $deltagid; {
 $res2 = sql_query("SELECT * FROM tags WHERE id=$deltagid_is") or sqlerr(__FILE__, __LINE__);
  $ip_name = mysql_fetch_array($res2);
}

echo("Вы действительно хотите удалить этот тег (".$ip_name["name"].") ? ( <strong><a href=\"". $_SERVER['PHP_SELF'] . "?deltagid=$deltagid&tagsure=yes\">Да</a></strong> / <strong><a href=\"". $_SERVER['PHP_SELF'] . "\">Нет</a></strong> )");

end_frame();
stdfootchat();
die();

}

///////////////////// E D I T A C A T E G O R Y \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$edited = $_GET['edited'];
if($edited == 1) {
$id = (int) $_GET['id'];
$cat_name = htmlspecialchars($_GET['cat_name']);
$cat_img = htmlspecialchars($_GET['cat_img']);
$cat_sort = (int) $_GET['cat_sort'];
$query = "UPDATE categories SET
name = ".sqlesc($cat_name).",
image = ".sqlesc($cat_img).",
sort = ".sqlesc($cat_sort)." WHERE id=".sqlesc($id);
$sql = sql_query($query);
if($sql) {
begin_frame("Успешное редактирование", true);
echo("<div align='center'>Ваша категория отредактирована <strong>успешно!</strong> [ <a href='category.php'>Назад</a> ]</div>");
@unlink(ROOT_PATH."cache/mydetails_category.txt");
end_frame();
stdfootchat();
die();
}
}

$editid = (int) $_GET['editid'];
$name = htmlspecialchars($_GET['name']);
$img = htmlspecialchars($_GET['img']);
$sort = (int) $_GET['sort'];
if($editid > 0) {
begin_frame("Редактирование категории", true);
echo("<form name='form1' method='get' action='" . $_SERVER['PHP_SELF'] . "'>");
echo("<table class=main cellspacing=0 cellpadding=5 width=50%>");
echo("<input type='hidden' name='edited' value='1'>");
echo("<input type='hidden' name='id' value='$editid'<table class=main cellspacing=0 cellpadding=5 width=50%>");
echo("<tr><td>Название: </td><td align='right'><input type='text' size=50 name='cat_name' value='$name'></td></tr>");
echo("<tr><td>Картинка: </td><td align='right'><input type='text' size=50 name='cat_img' value='$img'></td></tr>");
echo("<tr><td>Сортировка: </td><td align='right'><input type='text' size=50 name='cat_sort' value='$sort'></td></tr>");
echo("<tr><td></td><td><div align='right'><input type='Submit' value='Редактировать'></div></td></tr>");
echo("</table></form>");
end_frame();
stdfootchat();
die();
}

///////////////////// A D D A N E W C A T E G O R Y \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$add = $_GET['add'];
if($add == 'true') {
$cat_name = htmlspecialchars($_GET['cat_name']);
$cat_img = htmlspecialchars($_GET['cat_img']);
$cat_sort = (int) $_GET['cat_sort'];
$query = "INSERT INTO categories SET
name = ".sqlesc($cat_name).",
image = ".sqlesc($cat_img).",
sort = ".sqlesc($cat_sort);
$sql = sql_query($query);
@unlink(ROOT_PATH."cache/mydetails_category.txt");
if($sql) {
$success = TRUE;
} else {
$success = FALSE;
}
}
begin_frame("Добавить новую категорию", true);
echo("<form name='form1' method='get' action='" . $_SERVER['PHP_SELF'] . "'>");
echo("<table class=main cellspacing=0 cellpadding=5 width=50%>");
echo("<tr><td>Название: </td><td class='a'align='right'><input type='text' size=50 name='cat_name'></td></tr>");
echo("<tr><td>Картинка: </td><td class='a'align='right'><input type='text' size=50 name='cat_img'><input type='hidden' name='add' value='true'></td></tr>");
echo("<tr><td>Сортировка: </td><td class='a'align='right'><input type='text' size=50 name='cat_sort'></td></tr>");
echo("<tr><td colspan=2 style='border:0'><div align='center'><input type='Submit' value='Создать категорию'></div></td></tr>");
echo("</table>");
if($success == TRUE) {
print("<strong>Удачно!</strong>");
}
echo("<br />");
echo("</form>");
end_frame();

///////////////////// A D D A N E W TAG \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$addtag = $_GET['addtag'];
if($addtag == 'true') {

$_GET['tag_name'] = str_ireplace("/", ",", $_GET['tag_name']);

$tag_name = htmlspecialchars($_GET['tag_name']);
$tag_category = (int) $_GET['tag_category'];

if ($tag_category==0) {
	begin_frame("Ошибка в редактировании", true);
echo("<div align='center'>Ошибка, У нас нет категории 0</strong> [ <a href='category.php'>Назад</a> ]</div>");
end_frame();
}
$tag_name=tolower($tag_name);
if ($tag_category<>0) {
$query = "INSERT INTO tags SET name = ".sqlesc($tag_name).", added = ".sqlesc(get_date_time()).", category = ".sqlesc($tag_category);
$sql = sql_query($query);
}
if($sql) {
$success = TRUE;
} else {
$success = FALSE;
}
}
begin_frame("Добавить новый тэг", true);
echo("<form name='form2' method='get' action='" . $_SERVER['PHP_SELF'] . "'>");
echo("<table class=main cellspacing=0 cellpadding=5 width=50%>");
echo("<tr><td>Название: </td>
<td class='a'align='right'><input type='text' size=50 name='tag_name'></td></tr>");
$s = "<select name=\"tag_category\" style='width:100%'>\n<option value=\"0\">(".$tracker_lang['choose'].")</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
echo("<tr><td>Категория: </td><td class='a'align='right'>".$s."</td></tr>");
echo("<tr><td colspan=2 style='border:0'><div align='center'><input type='hidden' name='addtag' value='true'><input type='Submit' value='Создать тэг'></div></td></tr>");
echo("</table>");
if($success == TRUE) {
print("<strong>Удачно!</strong>");
die("<br>Перенаправление на index страницу.<script>setTimeout('document.location.href=\"category.php\"', 10);</script>");
}
echo("<br />");
echo("</form>");
end_frame();

///////////////////// E X I S T I N G C A T E G O R I E S \\\\\\\\\\\\\\\\\\\\\\\\\\\\

begin_frame("Существующие категории", true);
echo("<table class=main cellspacing=0 cellpadding=5>");
echo("
<td align=\"center\" class=\"a\">ID</td>
<td align=\"center\" class=\"a\">Сортировка</td>
<td align=\"center\" class=\"a\">Название</td>
<td align=\"center\" class=\"a\">Картинка</td>
<td align=\"center\" class=\"a\">Просмотр категории</td>
");


$naname = array();

$sql = sql_query("SELECT categories.id, categories.sort, categories.name, categories.image,
(SELECT COUNT(*) FROM torrents WHERE category=categories.id) AS num_t,
(SELECT SUM(size) FROM torrents WHERE category=categories.id) AS upthiss
 FROM categories GROUP BY categories.id") or sqlerr(__FILE__, __LINE__);
// GROUP_CONCAT(tags.name,'<a href=category.php?deltagid=',tags.id,'><img src=pic/warned2.gif></a>' ORDER BY tags.name ASC SEPARATOR ', ') AS tag_name 
while ($row = mysql_fetch_array($sql)) {
	
$id = (int) $row['id'];

	$sql_tags = sql_query("SELECT * FROM tags WHERE category='$id' GROUP BY id ORDER BY howmuch DESC") or sqlerr(__FILE__, __LINE__);
	while ($row_tags = mysql_fetch_array($sql_tags)) {
	
	if ($tag_name[$id])
	$tag_name[$id].=", ";
	
	$tag_name[$id].= "<a title=\"Поиск по ".($row_tags["name"])." тегу\" style=\"font-weight:normal;\" href=\"browse.php?tag=".urlencode($row_tags["name"])."&incldead=1\">".$row_tags["name"]."</a> ".($row_tags["howmuch"]<>0 ?"[".$row_tags["howmuch"]."]":"<b>[".$row_tags["howmuch"]."]</b>")." <a title=\"Удалить этот тег\" href=category.php?deltagid=".$row_tags["id"]."&tagsure=yes><img src=pic/warned2.gif></a>";

if (stristr($row_tags["name"],'+') || stristr($row_tags["name"],':') || stristr($row_tags["name"],'(') || stristr($row_tags["name"],')') || stristr($row_tags["name"],'/') || stristr($row_tags["name"],',') || strlen($row_tags["name"]) <= 2)
$naname[] = $row_tags["id"];

	}
	
$uploaded=mksize($row["upthiss"]);
$sort = $row['sort'];
$name = $row['name'];
$img = $row['image'];
$tag_id = (int) $row['tag_id'];
$tag_categ = $row['tag_categ'];

echo("<tr>
<td align=\"center\" class=\"b\"><strong>$id</strong></td>
<td align=\"center\" class=\"b\"><strong>$sort</strong></td>
<td align=\"center\" class=\"b\"><strong>$name <a title=\"Количество торрентов в данной категории\">[".$row['num_t']."]</a>
 ".($row['num_t']<>0 ? "<a title=\"Общий размер всех залитых торрентов в данной категории\">$uploaded</a>":"")."
 </strong> <br><br><b>[</b><a href='browse.php?incldead=1&cat=$id'>Просмотреть</a><b>]</b></td>
<td align=\"center\" class=\"b\"><img src='$DEFAULTBASEURL/pic/cats/$img' border='0' /></td>
<td align=\"center\" class=\"b\"><div align='center'>
<b>[</b><a href='category.php?editid=$id&name=$name&img=$img&sort=$sort'>Редактировать</a><b>]</b><br>
 <br><b>[</b><a href='category.php?delid=$id&cat=$name&tagsure=yes'>Удалить</a><b>]</b>
 </div></td>
 </tr>");

echo('<tr><td colspan="12" class="a"><b>Тэги:</b> '.$tag_name[$id].'</td></tr>');

}
echo "</table>";
end_frame();
echo "</td></tr></table>";

if (count($naname)){

// очистка тегов если пустое значение или 0 или 1
sql_query("DELETE FROM tags WHERE name=''") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM tags WHERE howmuch<='1'") or sqlerr(__FILE__,__LINE__);
//


sql_query("DELETE FROM tags WHERE id IN (".implode(",", $naname).")") or sqlerr(__FILE__, __LINE__);
}

stdfootchat();

?>