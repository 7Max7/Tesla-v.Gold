<?

if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}
print"<!------------- ���� ��� ������� ���������� ��� Tesla TT (2009) - 7Max7 -------------->"; /// 
/*
if ($_POST["gis_edit"])
{
$gis_ident=(int)$_POST["gis_edit"];
$expires = 0x7fffffff;
setcookie("gismeteo", $gis_ident, $expires, "/");
}
*/

if ($_COOKIE["gismeteo"])
{
$id_gis=(int) $_COOKIE["gismeteo"];
//33754 �������
$content.="<p align=\"center\"><script>URL='http://img.gismeteo.ru/flash/120x60_2.swf?city=$id_gis&cset=4';w='120';h='60';value='$id_gis&cset=4';lang='ru'</script><script src='http://informer.gismeteo.ru/flash/fcode.js'></script>

<span style=\"cursor: pointer;\" onclick=\"javascript: show_hide('s_$id_gis')\"><b>[</b>�������������<b>]</b></span>
<span id=\"ss_$id_gis\" style=\"display: none;\">

<form method=\"post\" action=\"index.php\">
<center>
������� ����� <b>[</b>id<b>]</b>:<br>
<input type=\"text\" size=\"10\" value=\"$id_gis\" name=\"gismeteo_id\">
<br><input type=\"submit\" value=\"���������\" >

<input type=\"submit\" name=\"gis_reset\" value=\"����� ���������\" >
</form></center>
</span>
";
$blocktitle = "��������";
}

if (!(int)$_COOKIE["gismeteo"])
{
	///display.html
echo("<form method=\"post\" action=\"index.php\">");
$content.="<p align=\"center\">
������� <b>[</b>id<b>]</b> ������:<br>
<input type=\"text\" size=\"10\" name=\"gismeteo_id\">
<br>
<span style=\"cursor: pointer;\" onclick=\"javascript: show_hide('s_666')\"><b>[</b>�������<b>]</b></span>
<span id=\"ss_666\" style=\"display: none;\"><br>
<b>33829</b> - ��������� (���)<br>
<b>33754</b> - ������� (���)<br>
<b>99300</b> - ������� (���)<br>
<b>33815</b> - �������<br>
<b>33745</b> - ������<br>
<b>33345</b> - ����<br>
<b>33837</b> - ������<br>
<b>34300</b> - �������<br>
<b>27612</b> - ������<br>
<b>26063</b> - ����� ���������<br>
<b>28900</b> - ������<br>
<b>28440</b> - ������������<br>
<b>28367</b> - ������<br>
<b>29947</b> - �����
</center>
</span>
<br>
<input type=\"submit\" value=\"���������\" >
</p></form>";

$blocktitle = "����� ������";
}

?>