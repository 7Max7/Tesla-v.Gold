<?

if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}
print"<!------------- Блок был написан специально для Tesla TT (2009) - 7Max7 -------------->"; /// 
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
//33754 рыбница
$content.="<p align=\"center\"><script>URL='http://img.gismeteo.ru/flash/120x60_2.swf?city=$id_gis&cset=4';w='120';h='60';value='$id_gis&cset=4';lang='ru'</script><script src='http://informer.gismeteo.ru/flash/fcode.js'></script>

<span style=\"cursor: pointer;\" onclick=\"javascript: show_hide('s_$id_gis')\"><b>[</b>дополнительно<b>]</b></span>
<span id=\"ss_$id_gis\" style=\"display: none;\">

<form method=\"post\" action=\"index.php\">
<center>
Введите новый <b>[</b>id<b>]</b>:<br>
<input type=\"text\" size=\"10\" value=\"$id_gis\" name=\"gismeteo_id\">
<br><input type=\"submit\" value=\"Применить\" >

<input type=\"submit\" name=\"gis_reset\" value=\"Сбить настройки\" >
</form></center>
</span>
";
$blocktitle = "Информер";
}

if (!(int)$_COOKIE["gismeteo"])
{
	///display.html
echo("<form method=\"post\" action=\"index.php\">");
$content.="<p align=\"center\">
Введите <b>[</b>id<b>]</b> города:<br>
<input type=\"text\" size=\"10\" name=\"gismeteo_id\">
<br>
<span style=\"cursor: pointer;\" onclick=\"javascript: show_hide('s_666')\"><b>[</b>примеры<b>]</b></span>
<span id=\"ss_666\" style=\"display: none;\"><br>
<b>33829</b> - Тирасполь (ПМР)<br>
<b>33754</b> - Рыбница (ПМР)<br>
<b>99300</b> - Бендеры (ПМР)<br>
<b>33815</b> - Кишинев<br>
<b>33745</b> - Бельцы<br>
<b>33345</b> - Киев<br>
<b>33837</b> - Одесса<br>
<b>34300</b> - Харьков<br>
<b>27612</b> - Москва<br>
<b>26063</b> - Санкт Петербург<br>
<b>28900</b> - Самара<br>
<b>28440</b> - Екатеринбург<br>
<b>28367</b> - Тюмень<br>
<b>29947</b> - Бийск
</center>
</span>
<br>
<input type=\"submit\" value=\"Применить\" >
</p></form>";

$blocktitle = "Выбор города";
}

?>