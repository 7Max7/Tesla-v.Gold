<?
require_once("include/bittorrent.php");
dbconn();

stdhead("���� � �������");

echo "<style type=\"text/css\">
<!--
input.pass {
    background: url(pic/pass.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
}

.rowhead2 {
  font-weight: bold;
  text-align: right;
 
}

input.login {
    background: url(pic/login.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
}
.error2 {
	padding: 10px;
	margin-top: 5px;
	margin-bottom: 10px;
	margin-top: 5px;
	border: 1px dotted red;
}
-->
</style>";


global $maxlogin;
if ($maxlogin==1) {
failedloginscheck();
}


if (!empty($_GET["returnto"])) {
$returnto = htmlentities($_GET["returnto"]);
$site_own = (($_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://").htmlspecialchars_uni($_SERVER['HTTP_HOST']);

if (!$_GET["nowarn"])
echo "<table border=\"0\" cellpadding=\"5\" width=\"100%\"><tr>
<td colspan=\"2\" class=\"b\" align=\"center\">
<div class=\"error2\">� ��������� ��������, ������� �� ��������� ���������� <b>�������� ������ �������� � �������</b>.<br />����� ��������� ����� �� ������ �������������� �� ���������� ��������: <br>
<input type=\"text\" size=\"100\" name=\"fJf\" value=\"".$site_own."/".$returnto."\" readonly style=\"width: 80%;\"/>
</div>
</td>
</tr>
</table><br>";
}

?>
<table border="0" cellpadding="5" width="100%">

<form method="post" id="o_O" action="takelogin.php">
<td class="colhead" colspan="2" align="center"><b>��������</b>: ��� ��������� ����� ������������� cookies.</td>

<tr><td class="b">������������:</td><td class="a" align="left"><input type="text" size="50" name="username" style="width: 200px; border: 1px solid green" class="login" value="��� ������������" onclick="if (this.value == '��� ������������') this.value = '';" onblur="if (this.value == '') this.value = '��� ������������';"/></td></tr>

<tr><td class="b">������:</td><td class="a" align="left"><input type="password" size="50" name="password" style="width: 200px; border: 1px solid green" class="pass"/></td></tr>
<?

if (isset($returnto))
echo("<input type=\"hidden\" name=\"returnto\" value=\"" . $returnto . "\" />\n");

?>
<tr><td class="b" colspan="2" align="center"><input type="submit" style="width: 200px;" value="��������� ������"/></td></tr>
</form>

</table>
<br>

<?

stdfoot();

?>