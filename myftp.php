<? 

/**
 * @author 7Max7
 * @copyright phpDesigner 2009
 */

require "include/bittorrent.php";
dbconn(false);


if (get_user_class() < UC_SYSOP){
stderr("��������", "������ ��� ����������"); 
die();
}


$cache_f=ROOT_PATH."cache/";


if($_POST["delete"] == "delete" && !empty($_POST["remove"])) {

foreach($_POST["remove"] as $file_cache) { /// ����������� ������
$file_unlink=@htmlentities($file_cache); /// ��������� ������
//echo $cache_f.$file_unlink."<br>"; /// �������� �� �����
unlink($cache_f.$file_unlink); /// �������
}

header("Location: myftp.php"); /// ��������������
}

function end_chmod($dir) {
	if (file_exists($dir)) {
		
/// ������ ������� �������� ���������
$pdir = decoct(fileperms($dir));
$per = substr($pdir, 3);
		
/*	
/// ������ ������� �������� ���������
$per = substr(decoct(fileperms($dir)),strlen(decoct(fileperms($dir)))-3,strlen(decoct(fileperms($dir))));
*/
	  if ($per<>"777")
	  	return "<font color=red>".$per."</font>";
		else
		return "<font color=green>".$per."</font>";
}}

stdhead("My FTP Cache");

if(!empty($_GET["file_view"])) {
$file_view=@htmlentities($_GET["file_view"]); /// ��������� ������

if (!file_exists($cache_f.$file_view)){
stderr($tracker_lang['error'], "������ ����� ��� <a href=\"myftp.php\">�������� �����</a>");
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
<b>���� ��� ��������</b> (������ ��� ������): ".$file_view."<br>
<b>������ ����</b>: ".$cache_f.$file_view."<br>
<b>������</b>: ".$size."<hr>
<textarea cols=100% rows=$stl_size readonly >$contents</textarea>
</td></tr>

<tr><td class=\"b\" align=\"center\">
<form action=\"myftp.php\">
<input style=\"height: 25px; width:200px\" type=\"submit\" value=\"�������� �����\"/>
</form>
</td></tr>
</table>");

stdfoot();
die;
//header("Location: myftp.php"); /// ��������������
}


echo("<script language=\"JavaScript\" type=\"text/javascript\">
<!-- Begin
var checkflag = \"false\";
function check(field) {
if (checkflag == \"false\") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = \"true\";
return \"����� �� ���� ����������\"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = \"false\";
return \"�������� ���� ��� ����������\"; }
}
function check2(field) {
if (checkflag == \"false\") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = \"true\";
return \"����� ��������� �� ���� �������������\"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = \"false\";
return \"�������� ���� ��� ������ ����������\"; }
}
// End -->
</script>");

echo "<form action=\"myftp.php\" method=post>"; 

echo "<table border=0 cellspacing=0 cellpadding=5 width=\"100%\">";
echo "<tr><td class=colhead>���� </td><td  align=center  class=colhead>�����</td><td  align=center class=colhead>
<input title=\"�������� � ������� ���\" type=\"checkbox\" value=\"�������� ��� �����\" onclick=\"this.value=check2(this.form.elements['remove[]'])\"/> 
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
<td align=left class=b><b><a href=\"myftp.php?file_view=$file\">".$file."</a></b><br><b>������</b>: ".$filetime." <b>������</b>: ".$size."</td>
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
<tr><td class=\"b\" align=\"center\">���������� ������: ".$num."<br>
<input type=\"hidden\" name=\"delete\" value=\"delete\"/><input style=\"height: 25px; width:200px\" type=\"submit\" name=\"submit\" value=\"������� ��������� ����\"/>
</td></tr></form>
</table>");

stdfoot();

?>