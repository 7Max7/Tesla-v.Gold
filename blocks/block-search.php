<?
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}

/*
<tr> 
<td> 
<form method=\"get\" action=\"detailsoff.php\"> 
<div align=\"center\"> 
<b>Поиск запроса</b>:
<input type=\"text\" name=\"search\" size=\"18\" /><br /> 
<select name=\"incldead\"> 
<option value=\"0\">Без исключений</option> 
<option value=\"1\">Выполненные</option> 
<option value=\"2\">Не выполненные</option> 
</select> 
<input class=\"btn\" type=\"submit\" value=\"Найти!\" /> 
</form> 
</td> 
</tr> 
<b>Поиск торрента</b>:
*/

$content="
<table align=\"center\" valign=\"top\" cellpadding=\"0\"> 

<tr> 
<td align=\"center\" class=\"b\"> 
<form method=\"get\" action=\"browse.php\"> 
<input type=\"text\" name=\"search\" size=\"18\" value=\"\" />
<select name=\"incldead\">
<option value=\"1\">Включая мертвые</option> 
<option value=\"0\">Активные</option>
<option value=\"2\">Только мертвые</option>
<option value=\"3\">Золотые торренты</option>
<option value=\"4\">Без сидов</option>
<option value=\"6\">Забаненые</option>
<option value=\"7\">Без тегов</option>
<option value=\"9\">Без постера</option>
</select>
<input class=\"btn\" type=\"submit\" style=\"width: 100px\" value=\"Искать!\" />
</form>
</td>
</tr>
</table>";

?>