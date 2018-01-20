<? 
//foreach($_POST as $key=>$value) $$key=$value; 
//foreach($_GET as $key=>$value) $$key=$value;  
//ob_start(); 
require_once("include/bittorrent.php"); 
dbconn(false); 
loggedinorreturn();

stdheadchat("Группы"); 

print("<table width=100% border=0 cellspacing=0 cellpadding=2><tr><td align=center>\n"); 

///////////////////// Удалить группу 
$sure = $_GET['sure']; 
if($sure == "yes") {
if (get_user_class() == UC_SYSOP) {

$del = (int) $_GET['del']; 
sql_query("DELETE FROM groups WHERE id=" .sqlesc($del)) or sqlerr(__FILE__, __LINE__);
echo("Группа удачно удалена! [<a href='". $_SERVER['PHP_SELF'] . "'>Назад</a>]
<script>setTimeout('document.location.href=\"".$_SERVER['PHP_SELF']."\"', 3000);</script> Автообновление"); 
end_frame(); 

@unlink(ROOT_PATH."cache/638fe8da76ba39f630767edc578bef7e.txt"); /// удаляем кеш групп где 3600 сек

stdfootchat(); 
die(); 
} else unset($_GET['sure']);
}


///////////////////// Удалить группу 
$del = (int) $_GET['del']; 
if (!empty($del) && $del<>"0") {
if (get_user_class() == UC_SYSOP){
 
$query = sql_query("SELECT * FROM groups WHERE id=".sqlesc($del)."") or sqlerr(__FILE__, __LINE__); 
$row = mysql_fetch_array($query);
$name=$row["name"];
if($del > 0) {
echo("Уверены что хотите удалить группу ? <b>[</b>$name<b>]</b> <br>( <strong><a href='". $_SERVER['PHP_SELF'] . "?del=$del&sure=yes'>Да</a></strong> / <strong><a href='". $_SERVER['PHP_SELF'] . "'>Нет</a></strong> )"); 
end_frame(); 
stdfootchat(); 
die(); 
}} else unset($_GET['del']);
}///////////////////// Удалить группу 




///////////////////// Редактировать группу \\\\\\\\\\\\\\\\\\\\\\\\\\\\ 
$edited = $_GET['edited']; 
if($edited > 0 && !empty($_POST['group_name']) && !empty($_POST['group_image']) && get_user_class() == UC_SYSOP) {



$id = (int) $_GET['id']; 

$group_name = htmlspecialchars($_POST['group_name']); 
$group_image = htmlspecialchars($_POST['group_image']); 
$comment = htmlspecialchars($_POST['comment']); 
sql_query("UPDATE groups SET name = ".sqlesc($group_name).", image = ".sqlesc($group_image).", comment = ".sqlesc($comment)." WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__); 

echo("<table class=main cellspacing=0 cellpadding=5 width=100%>"); 
echo("<tr><td><div align='center'><strong>Удачно </strong>изменено [<a href='". $_SERVER['PHP_SELF'] . "'>Назад</a> ] <script>setTimeout('document.location.href=\"".$_SERVER['PHP_SELF']."\"', 3000);</script> Автообновление</div></tr>"); 
echo("</table>"); 

@unlink(ROOT_PATH."cache/638fe8da76ba39f630767edc578bef7e.txt"); /// удаляем кеш групп где 3600 сек

end_frame(); 
stdfootchat(); 
die(); 

}
//// редактируем группу
$editid = (int) $_GET['editid']; 
if (!empty($editid) && $editid<>"0") {
if (get_user_class() == UC_SYSOP){

$query = sql_query("SELECT * FROM groups WHERE id=".sqlesc($editid)."") or sqlerr(__FILE__, __LINE__); 
$row = mysql_fetch_array($query);
$fd=$row["comment"];
$name=$row["name"];
$image=$row["image"];
if($editid > 0) {

echo("<form method=post action='" . $_SERVER['PHP_SELF'] . "?id=$editid&edited=1'>"); 
echo("<table class=main cellspacing=0 cellpadding=5 width=50%>"); 
//echo("<div align='center'><input type='hidden' name='edited' value='1'>В данный момент вы изменяете группу <strong>&quot;$name&quot;</strong></div>"); 
echo("<br>"); 
echo("<input type='hidden' name='id' value='$editid'<table class=main cellspacing=0 cellpadding=5 width=50%>"); 
echo("<tr>
<td>Группа: </td>
<td align='left'><input type='text' size=50 name='group_name' value='$name'></td></tr>"); 
echo("<tr>
<td>Фотка группы: </td>
<td align='left'><input type='text' size=50 name='group_image' value='$image'></td></tr>"); 
echo("<tr><td>Описание: </td><td align='right'>
<textarea cols=75 rows=3 name='comment' value='$fd'>$fd</textarea>
</td></tr>"); 
echo("<tr><td></td><td><div align='right'><input type='Submit' value='Изменить Данные'></div></td></tr>"); 
echo("</table></form><br>"); 
end_frame(); 
stdfootchat(); 
die(); 
}}
else
unset($_GET['editid']);
}
////////// конец редактирования




///+++
///////////////////// Добавить группу \\\\\\\\\\\\\\\\\\\\\\\\\\\\ 
$add = htmlspecialchars($_GET['add']); 
if($add == 'true') {
if (get_user_class() == UC_SYSOP){


$group_name = htmlspecialchars($_POST['group_name']); 
$group_image = htmlspecialchars($_POST['group_image']);

if (empty($_POST['group_name']) || empty($_POST['group_image']))
{
  stdmsg("Ошибка", "Пусты данные");
  stdfootchat();
  exit;
}
/*
if (!file_exists("./pic/groups/$group_image"))
{
stdmsg("Ошибка", "Данной картинки нет в папке: $group_image <br><a href=".$_SERVER['PHP_SELF'].">Назад</a>");
stdfootchat();
exit;
}*/


$comment=htmlspecialchars($_POST['comment']);
sql_query("INSERT INTO groups SET name = '$group_name', comment = '$comment', image = '$group_image'") or sqlerr(__FILE__, __LINE__);
echo"Добавленно $group_name и $group_image";
@unlink(ROOT_PATH."cache/638fe8da76ba39f630767edc578bef7e.txt"); /// удаляем кеш групп где 3600 сек

@header("Location:  " . $_SERVER['PHP_SELF'] . "") or die("<script>setTimeout('document.location.href=\"".$_SERVER['PHP_SELF']."\"', 100);</script> Автообновление");

}
else
unset($_GET['add']);
}
///+++

if ($_GET['add']=="yes" && empty($_POST['group_name']) && empty($_POST['group_image'])){
if (get_user_class() == UC_SYSOP) {

echo("<table class=main cellspacing=0 cellpadding=5 width=100%>"); 
echo("<form method='post' action='" . $_SERVER['PHP_SELF'] . "?add=true'>"); 
echo("
<td align=\"center\" class=\"colhead\" colspan=\"2\">Добавить новую группу</td>

<tr><td class=a ><b>Группа</b>: </td><td align='left'><input type='text' size=50 name='group_name'></td></tr>"); 
echo("<tr><td class=a><b>Фотка</b>: </td><td align='left'><input type='text' size=50 name='group_image'><input type='hidden' name='add' value='true'> <i>нранится в папке pic/groups/ изображение</i></td></tr>"); 
echo("<tr><td class=a><b>Описание</b>: </td><td align='left'><textarea cols=50 rows=3 name='comment'></textarea></td></tr>");
echo("<tr><td class=a></td><td><div align='right'><input value='Добавить' type='Submit'></div></td></tr>"); 
echo("</table>"); 

echo("<br>"); 
echo("</form>"); 
if($success == TRUE) {
header("Location:  " . $_SERVER['PHP_SELF'] . "");  
die;
}
end_frame(); 
stdfootchat(); 
die(); 
}
else
unset($_GET['add']);
}
if (empty($_GET['add']) && empty($_GET['editid'])) {
	
$res = sql_query("SELECT COUNT(id) FROM groups");
$row = mysql_fetch_array($res);
$count = $row[0];
$perpage = 25;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "". $_SERVER['PHP_SELF'] . "?");


///////////////////// Список групп \\\\\\\\\\\\\\\\\\\\\\\\\\\\ 
echo("<table width=100% cellspacing=0 cellpadding=5>"); 
print("<td class=\"colhead\" align=\"center\" valign=\"top\">Список всех групп [$count]".(get_user_class() == UC_SYSOP ?"<br><a class='altlink_white' href='". $_SERVER['PHP_SELF'] . "?add=yes'>Нажмите сюда для создания группы</a>":"")."
</td>");
print("</table>"); 


echo("<table width=100% cellspacing=0 cellpadding=5>"); 
print($pagertop);
echo("<td class=\"a\" width=10 align=\"center\">ID:</td>
<td class=\"a\" align=\"center\">Фотка:</td>
<td class=\"a\" align=\"center\">Название:</td>
<td class=\"a\" align=\"center\">Описание:</td>
".(get_user_class() == UC_SYSOP ? "<td class=\"a\" align=\"center\">Действие:</td>":"")."
");

$sql = sql_query("SELECT * FROM groups $limit");
while ($row = mysql_fetch_array($sql)) {
$id = $row['id']; 

$sql2= sql_query("SELECT username,class,id AS t,
(SELECT COUNT(*) FROM torrents WHERE owner=t) AS num_torrent,
(SELECT SUM(size) FROM torrents WHERE owner=t) AS num_size
 FROM users WHERE groups='$id'");
$num[$id]=0;
while ($row2 = mysql_fetch_array($sql2)) {

if ($num_all[$id])
$num_all[$id]=$num_all[$id]+$row2["num_torrent"];
else 
$num_all[$id]=$row2["num_torrent"];

if ($num_size[$id])
$num_size[$id]=$num_size[$id]+$row2["num_size"];
else 
$num_size[$id]=$row2["num_size"];

if ($users[$id])
$users[$id].=", ";
$users[$id].= "<a href=userdetails.php?id=".$row2["t"]." >".get_user_class_color($row2["class"], $row2["username"]) . "</a>".($row2["num_torrent"]?" <a title=\"Количество залитых торрентов\">[".$row2["num_torrent"]."]</a>":"")."";
$num[$id]++;
}
$users[$id]="".($users[$id] ? "<hr>".$users[$id]." <br><b>Залито</b>: $num_all[$id] | <b>Размер</b>: ".mksize($num_size[$id])." | <b>В группе</b>: $num[$id]":"")."";

$name = $row['name']; 
$image = $row['image']; 
$comment = format_comment($row['comment']); 
echo("<tr>
<td width=2% align=\"center\">$id</td>
<td width=10% align=\"center\" width=20%><img src='/pic/groups/$image' alt=\"$name\" border='0' /></td>
<td width=10% align=\"center\">$name</td>
<td align=\"left\">".(empty($comment)? "Описание требует вмешательства":$comment)." $users[$id]</td>
".(get_user_class() == UC_SYSOP ? "<td align=\"center\" width=10% ><a href='". $_SERVER['PHP_SELF'] . "?editid=$id'>Редактировать</a><hr><a href='". $_SERVER['PHP_SELF'] . "?del=$id&group=$name'>Удалить</a></td>":"")."
</tr>"); 

//echo("<textarea cols=100% rows=3 name='comment' readonly >$comment</textarea>"); 
unset($users[$id]);
unset($num_size[$id]);
}


end_frame(); print($pagerbottom);
stdfootchat(); 
}
?>
