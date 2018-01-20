<?
require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

if (get_user_class() < UC_MODERATOR) {
stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();}


if ($_POST["delmp"] && get_user_class() >= UC_MODERATOR){

if(isset($_POST["delmp"])) {
sql_query("DELETE FROM comments WHERE id IN (". implode(", ", array_map("sqlesc", $_POST['delmp'])) . ")") or sqlerr(__FILE__, __LINE__); ///(".implode(", ", $_POST['delmp']).")
}

header("Refresh: 0; url=".$_SERVER["PHP_SELF"]."");
die;
}

stdheadchat("Последние комментарии за неделю");

// сортировка
$sort = (int) $_GET["sort"];
switch($sort) {
case '1': $orderby = "comments.added"; break; // По дате комментария
case '2': $orderby = "torrents.id"; break; // По id релиза
case '3': $orderby = "users.username"; break; // По имени юзера
default: $orderby = "comments.added";
}
$abc = (int) $_GET["abc"];
switch($abc) {
case '1': $by = "DESC"; break;
case '2': $by = "ASC"; break;
default: $by = "DESC";
}
// сортировка

$page = (int) $_GET["page"];
$count = get_row_count("comments WHERE torrent != 0 AND added > (NOW() - INTERVAL 7 DAY)");
 
echo("<table width='100%' border='1' cellspacing='0' cellpadding='4'>");
echo("<tr><td class='a' align='center'>Комментарии за неделю ".(get_user_class() >= UC_MODERATOR ? ": <a href=comments_last.php>Последние 40 комментариев</a>":"")."</td></tr>");
if ($count){
?>
<script language="Javascript" type="text/javascript">
var checkflag = "false";var marked_row = new Array;function check(field){if (checkflag == "false"){for (i = 0; i < field.length; i++){field[i].checked = true;}checkflag = "true";}else{for (i = 0; i < field.length; i++){field[i].checked = false;}checkflag = "false";}}
</script>
<?
$perpage = 50;//комментариев на страницу

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?sort=".$sort."&amp;abc=".$abc."&amp;");

$res = sql_query("SELECT comments.*, torrents.id AS tid, torrents.name AS tname, users.id AS userid, 
                  users.class AS uclass, users.username AS username, users.avatar AS usersavatar
                  FROM comments 
                  LEFT JOIN torrents ON torrent = torrents.id  
                  LEFT JOIN users ON user = users.id WHERE torrent !=0  
                  AND comments.added > (NOW() - INTERVAL 7 DAY) 
                  ORDER BY ".$orderby." ".$by.", comments.id DESC $limit") or sqlerr(__FILE__, __LINE__);
echo("<tr><td>");
/*echo("<div style='float: right;'>");
echo $pagertop;
echo("</div>");*/
// сортировка
echo ('<div style="float:left"> 
       <form method="GET" action="'.$PHP_SELF.'">
       <b>Сортировать по</b>: 
       <select name="sort">
       <option value="1" '.(($sort == 1) ? "selected" : "").'>дате размещения</option>
       <option value="2" '.(($sort == 2) ? "selected" : "").'>идентификатору релиза</option>
       <option value="3" '.(($sort == 3) ? "selected" : "").'>имени пользователя</option>
       </select>
       <select name="abc">
       <option value="1" '.(($abc == 1) ? "selected" : "").'>Z-a</option>
       <option value="2" '.(($abc == 2) ? "selected" : "").'>A-z</option>
       </select>
       <input type="submit" value="ОК">
       <input type="hidden" name="page" value="'.$page.'">
       </form></div>');
      
if (get_user_class() >= UC_MODERATOR) {
echo('<div style="float:right">
      <form method="post" action='.$_SERVER["PHP_SELF"].'  name="form1">
      <input type="radio" title="Выбрать все" value="Выбрать все" onClick="this.value=check(document.form1.elements);">
      <input type="submit" value="Удалить выбранное!" onClick="return confirm(\'Вы уверены?\')"></div>');}
echo('</td></tr>');
   
while ($arr = mysql_fetch_assoc($res)) {

// выделение релизов по дням
$day = get_date_time(sql_timestamp_to_unix_timestamp($arr["added"])); // новая дата
$day = (int)date("d", strtotime($day)); // новая дата

echo("<tr><td>");
echo("<table width='100%' border='1' cellspacing='0' cellpadding='4'>");
   
$torrname = $arr['tname'];
/*if (strlen($torrname) > 63){
$torrname = substr($torrname, 0, 60)."...";}*/       
$torrernt = "<a href='details.php?id=".$arr["tid"]."'>".$torrname."</a>";
$user = "<a href='userdetails.php?id=".$arr["userid"]."'>".get_user_class_color($arr["uclass"], htmlspecialchars_uni($arr["username"]))."</a>";
$msg = format_comment($arr["text"]);
$added = normaltime($arr["added"], true);

$avatar = htmlspecialchars_uni($arr["usersavatar"]);
if (!$avatar){
$avatar = "pic/avatar/default_avatar.gif";}
else {
$avatar = "pic/avatar/$avatar";}


//Чекбоксы для удаления комментов
if (get_user_class() >= UC_MODERATOR and $arr["uclass"] <= get_user_class()) {
$checkbox = "<input type=\"checkbox\" name=\"delmp[]\" value=\"".$arr['id']."\" id=\"checkbox_tbl_".$arr['id']."\">";
} else {
$checkbox = "";}
      
echo("<tr><td class='a' colspan='2'>");
echo("<div style='padding: 0px; float: left;'><img src=\"/pic/balloon--arrow.png\">".$arr['id']." &rArr; ".$added." &rArr; ".$user." &rArr; ".$torrernt."</div>");
echo("<div style='padding: 0px; float: right;'>".$checkbox."</div>");       
echo("</td></tr>");
echo("<tr><td width='5%' style='padding: 1px;'><img src=".$avatar." alt='Аватара ".$arr["username"]."' width=\"".$avatar_max_width."\"></td>");
echo("<td valign='top'>".$msg."</td></tr>");
echo("</td></tr></table>");

$oldday = $day; // старая дата
}
} else {
echo("<tr><td align='center'><b><font color='red'>Комментариев для отображения нет...</font></b></td></tr>");}
echo $pagerbottom;
echo("</table>");
stdfootchat();
?> 