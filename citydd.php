<?

require_once("include/bittorrent.php"); 
dbconn(false); 
loggedinorreturn(); 

if (get_user_class() <= UC_ADMINISTRATOR) 
 {
attacks_log('citydd'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}
accessadministration();

stdhead("Города и Страны"); 
print("<h1>Города и Страны</h1>\n"); 
print("</br>"); 
print("<table width=90% border=1 cellspacing=0 cellpadding=2><tr><td align=center>\n"); 

///////////////////// Удалить город \\\\\\\\\\\\\\\\\\\\\\\\\\\\ 

$sure = $_GET['sure']; 
if($sure == "yes") {
$delid = (int) $_GET['delid']; 
$query = "DELETE FROM cities WHERE id=" .sqlesc($delid) . " LIMIT 1"; 
$sql = sql_query($query) or sqlerr(__FILE__, __LINE__); 
echo("<strong>Город успешно был удалён! </strong>[ <a href='citydd.php'>На главную</a> ]"); 
end_frame(); 
stdfoot(); 
die(); 
}

$delid = (int)$_GET['delid']; 
$name = htmlspecialchars($_GET['name']); 
if($delid > 0) {
echo("Вы уверены что хотите удалить этот город? (<strong>$name</strong>) ( <strong><a href='". $_SERVER['PHP_SELF'] . "?delid=$delid&name=$name&sure=yes'>Да!</a></strong> / <strong><a href='". $_SERVER['PHP_SELF'] . "'>Нет!</a></strong> )"); 
end_frame(); 
stdfoot(); 
die(); 
}

///////////////////// Редактировать город \\\\\\\\\\\\\\\\\\\\\\\\\\\\ 
$edited = $_GET['edited']; 
if($edited == 1) {
$id = (int)$_GET['id']; 
$country_name = htmlspecialchars_uni($_GET['country_name']); 
$country_id = (int)$_GET['country_id']; 
$query = "UPDATE cities SET name = ".sqlesc($country_name).", country_id = ".sqlesc($country_id)." WHERE id=".sqlesc($id); 
$sql = sql_query($query) or sqlerr(__FILE__, __LINE__);
if($sql) { 
echo("<table class=main cellspacing=0 cellpadding=5 width=80%>"); 
echo("<tr><td><div align='center'><strong>Успешно изменено! </strong>[ <a href='citydd.php'>На главную</a> ]</div></tr>"); 
echo("</table>"); 
end_frame(); 
stdfoot(); 
die(); 
}
}

$editid = $_GET['editid']; 
$id = (int)$_GET['id']; 
$name = htmlspecialchars_uni($_GET['name']); 
$country_id = (int) $_GET['country_id']; 

$ct_r = sql_query("SELECT id,name FROM countries") or sqlerr(__FILE__, __LINE__);
while ($ct_a = mysql_fetch_array($ct_r)) 
$countries .= "<option value=$ct_a[id]" . ($country_id == $ct_a['id'] ? " selected" : "") . ">$ct_a[name]</option>\n"; 

if($editid > 0) {
echo("<form name='form1' method='get' action='" . $_SERVER['PHP_SELF'] . "'>"); 
echo("<table class=main cellspacing=0 cellpadding=5 width=70%>"); 
echo("<div align='center'><input type='hidden' name='edited' value='1'>Вы редактируете город <strong> $name</strong></div>"); 
echo("<br>"); 
echo("<input type='hidden' name='id' value='$editid'<table class=main cellspacing=0 cellpadding=5 width=50%>"); 
echo("<tr><td>Город: </td><td align='right'><input type='text' size=52 name='country_name' value='$name'></td></tr>"); 
echo("<tr><td>Страна:</td><td align='right'><select name=country_id>$countries</select></td></tr>"); 
echo("<tr><td></td><td><div align='right'><input type='Submit'></div></td></tr>"); 
echo("</table></form>"); 
end_frame(); 
stdfoot(); 
die(); 
} 

///////////////////// Добавить новый город \\\\\\\\\\\\\\\\\\\\\\\\\\\\ 

$ct_r = sql_query("SELECT id,name FROM countries ORDER BY name")  or sqlerr(__FILE__, __LINE__); 
while ($ct_a = mysql_fetch_array($ct_r)) 
$countries .= "<option value=$ct_a[id]" . ($CURUSER["country"] == $ct_a['id'] ? " selected" : "") . ">$ct_a[name]</option>\n"; 

$add = $_GET['add']; 
if($add == 'true') {
$country_name = htmlspecialchars_uni($_GET['country_name']); 
$country_id = (int)$_GET['country_id']; 
$query = "INSERT INTO cities SET name = ".sqlesc($country_name).", country_id = ".sqlesc($country_id).""; 
$sql = sql_query($query); 
if($sql) {
$success = TRUE; 
} else {
$success = FALSE; 
}
}

print("<strong>Добавить город:</strong>"); 
print("<br />"); 
print("<br />"); 
echo("<form name='form1' method='get' action='" . $_SERVER['PHP_SELF'] . "'>"); 
echo("<table class=main cellspacing=0 cellpadding=5 width=50%>"); 
echo("<tr><td>Город: </td><td align='right'><input type='text' size=52 name='country_name'></td></tr>"); 
echo("<tr><td>Страна: </td><td align='right'><select name=country_id>$countries</select><input type='hidden' name='add' value='true'></td></tr>"); 
echo("<tr><td></td><td><div align='right'><input type='Submit'></div></td></tr>"); 
echo("</table>");

if($success == TRUE) {
header("Location:  " . $_SERVER['PHP_SELF'] . ""); 
}

echo("<br>"); 
echo("</form>"); 

///////////////////// Список городов \\\\\\\\\\\\\\\\\\\\\\\\\\\\ 
print("<br />"); 
print("<br />"); 
$res = sql_query("SELECT COUNT(*) FROM cities") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res); 
$count = $row[0]; 
$perpage = 100; 
$limit = 100; 
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?" . "&" ); 
print($pagertop); 


echo("<br><table class=main cellspacing=0 cellpadding=5>"); 
echo("<td>Город:</td><td>Страна:</td><td>Редактировать:</td><td>Удалить:</td>"); 
$query = "SELECT * FROM cities ORDER BY ID DESC $limit"; 
$sql = sql_query($query) or sqlerr(__FILE__, __LINE__);
while ($row = mysql_fetch_array($sql)) {
$ID = $row['ID']; 
$name = $row['name']; 
$country_id = $row['country_id']; 

$ct_r = sql_query("SELECT id,name,flagpic FROM countries WHERE id=$country_id") or sqlerr(__FILE__, __LINE__);
while ($ct_a = mysql_fetch_array($ct_r)) 
$country_city = "<img src=/pic/flag/$ct_a[flagpic] alt=\"$ct_a[name]\" style='margin-left: 8pt'>\n"; 

echo("<tr><td><strong>$name</strong></td> <td>$country_city</td> <td><a href='" . $PHP_SELF['$_SERVER'] . "citydd.php?editid=$ID&name=$name&country_id=$country_id'><div align='center'><img src='$BASEURL/pic/multipage.gif' border='0' class=special /></a></div></td> <td><div align='center'><a href='" . $PHP_SELF['$_SERVER'] . "citydd.php?delid=$ID&name=$name'><img src='$BASEURL/pic/warned2.gif' border='0' class=special align='center' /></a></div></td></tr>"); 
}


end_frame(); 

end_frame(); 
stdfoot(); 
die;
?> 