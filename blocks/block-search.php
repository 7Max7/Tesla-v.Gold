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
<b>����� �������</b>:
<input type=\"text\" name=\"search\" size=\"18\" /><br /> 
<select name=\"incldead\"> 
<option value=\"0\">��� ����������</option> 
<option value=\"1\">�����������</option> 
<option value=\"2\">�� �����������</option> 
</select> 
<input class=\"btn\" type=\"submit\" value=\"�����!\" /> 
</form> 
</td> 
</tr> 
<b>����� ��������</b>:
*/

$content="
<table align=\"center\" valign=\"top\" cellpadding=\"0\"> 

<tr> 
<td align=\"center\" class=\"b\"> 
<form method=\"get\" action=\"browse.php\"> 
<input type=\"text\" name=\"search\" size=\"18\" value=\"\" />
<select name=\"incldead\">
<option value=\"1\">������� �������</option> 
<option value=\"0\">��������</option>
<option value=\"2\">������ �������</option>
<option value=\"3\">������� ��������</option>
<option value=\"4\">��� �����</option>
<option value=\"6\">���������</option>
<option value=\"7\">��� �����</option>
<option value=\"9\">��� �������</option>
</select>
<input class=\"btn\" type=\"submit\" style=\"width: 100px\" value=\"������!\" />
</form>
</td>
</tr>
</table>";

?>