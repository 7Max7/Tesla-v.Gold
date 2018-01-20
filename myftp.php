<? 

/**
 * @author 7Max7
 * @copyright phpDesigner 2009
 */

require "include/bittorrent.php";
dbconn(false);


if (get_user_class() < UC_SYSOP){
stderr("Ошибочка", "Дальше еще интереснее"); 
die();
}


$cache_f=ROOT_PATH."cache/";


if($_POST["delete"] == "delete" && !empty($_POST["remove"])) {

foreach($_POST["remove"] as $file_cache) { /// разлаживаем массив
$file_unlink=@htmlentities($file_cache); /// фильтруем данные
//echo $cache_f.$file_unlink."<br>"; /// проверка на вывод
unlink($cache_f.$file_unlink); /// удаляем
}

header("Location: myftp.php"); /// перенаправляем
}

function end_chmod($dir) {
	if (file_exists($dir)) {
		
/// первый вариант проверки атрибутов
$pdir = decoct(fileperms($dir));
$per = substr($pdir, 3);
		
/*	
/// второй вариант проверки атрибутов
$per = substr(decoct(fileperms($dir)),strlen(decoct(fileperms($dir)))-3,strlen(decoct(fileperms($dir))));
*/
	  if ($per<>"777")
	  	return "<font color=red>".$per."</font>";
		else
		return "<font color=green>".$per."</font>";
}}

stdhead("My FTP Cache");

if(!empty($_GET["file_view"])) {
$file_view=@htmlentities($_GET["file_view"]); /// фильтруем данные

if (!file_exists($cache_f.$file_view)){
stderr($tracker_lang['error'], "Такого файла нет <a href=\"myftp.php\">Вернутся назад</a>");
}

$handle = @fopen($cache_f.$file_view, "r");
$contents = @fread($handle, filesize($cache_f.$file_view));
@fclose($handle);

$size=mksize(filesize($cache_f.$file_view));

if (strlen($contents)>1000)
$stl_size=40;
elseif (strlen($contents)>=250 && strlen($contents)<=1000)
$stl_size=round(strlen($contents)/200)+10;
else
$stl_size=10;

print ("<table align=\"center\" cellpadding=\"0\" width=\"100%\">

<tr><td width=\"100%\" class=\"b\" align=\"center\">
<b>Файл для открытия</b> (только для чтения): ".$file_view."<br>
<b>Полный путь</b>: ".$cache_f.$file_view."<br>
<b>Размер</b>: ".$size."<hr>
<textarea cols=100% rows=$stl_size readonly >$contents</textarea>
</td></tr>

<tr><td class=\"b\" align=\"center\">
<form action=\"myftp.php\">
<input style=\"height: 25px; width:200px\" type=\"submit\" value=\"Вернутся назад\"/>
</form>
</td></tr>
</table>");

stdfoot();
die;
//header("Location: myftp.php"); /// перенаправляем
}


echo("<script language=\"JavaScript\" type=\"text/javascript\">
<!-- Begin
var checkflag = \"false\";
function check(field) {
if (checkflag == \"false\") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = \"true\";
return \"Снять со всех отключение\"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = \"false\";
return \"Выделить всех для отключения\"; }
}
function check2(field) {
if (checkflag == \"false\") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = \"true\";
return \"Снять выделение со всех подозреваемых\"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = \"false\";
return \"Выделить всех для снятия подозрения\"; }
}
// End -->
</script>");

echo "<form action=\"myftp.php\" method=post>"; 

echo "<table border=0 cellspacing=0 cellpadding=5 width=\"100%\">";
echo "<tr><td class=colhead>Файл </td><td  align=center  class=colhead>Права</td><td  align=center class=colhead>
<input title=\"Выделить и удалить все\" type=\"checkbox\" value=\"Выделить все файлы\" onclick=\"this.value=check2(this.form.elements['remove[]'])\"/> 
</td></tr>";


$dh = opendir($cache_f);
$num=0;
while ($file = @readdir($dh)):
if (preg_match('/^(.+)\.$/si', $file, $matches))
$file = $matches[1];
if ($file<>".htaccess" AND $file<>"/" AND $file<>"."){
$file=htmlentities($file);
$filetime=get_date_time(filemtime($cache_f.$file));
$size=mksize(filesize($cache_f.$file));
 
echo "<tr>
<td align=left class=b><b><a href=\"myftp.php?file_view=$file\">".$file."</a></b><br><b>Создан</b>: ".$filetime." <b>Размер</b>: ".$size."</td>
<td align=center width=7%>".end_chmod($cache_f.$file)."</td>
<td align=center width=5%><input type=\"checkbox\" name=\"remove[]\" value=\"".$file."\" id=\"checkbox_tbl_" .$file. "\"/>
</td>
</tr>";

$num++;
}
endwhile;
@closedir($dh);

echo "</table>";

print ("<table align=\"center\" valign=\"top\" cellpadding=\"0\" width=\"100%\">
<tr><td class=\"b\" align=\"center\">Количество файлов: ".$num."<br>
<input type=\"hidden\" name=\"delete\" value=\"delete\"/><input style=\"height: 25px; width:200px\" type=\"submit\" name=\"submit\" value=\"Удалить выделеный файл\"/>
</td></tr></form>
</table>");

stdfoot();

?>