<?php

/**
 * @author 7Max7
 * @copyright 2010
 */

?>
<title>�������� ������ ��� Tesla TT 2010</title>
<style type="text/css">
<!--
body { font-family: Tahoma, Verdana; font-size: 11px; color: #333333; background-color: #ffffff; line-height: 170%; margin: 0px; padding: 0px; background-image: url(images/banner_bg.gif); background-repeat: repeat-x;}
td.rowhead { font-weight: bold; text-align: left; vertical-align: top;}
td.a { background-color : #ececec; padding : 6px; font-family : Verdana, Helvetica, sans-serif; font-size : 8pt; border-style : solid; border-width : 1px 1px 1px;}
td.b { background-color : #f7f7f7; padding : 6px; font-family : Verdana, Helvetica, sans-serif; font-size : 8pt; border-style : solid; border-width : 1px 1px 1px;}
-->
</style>
<?


if (!empty($_POST["password"]) && !empty($_POST["username"])){

$login=$_POST["username"];
$password=md5(md5($_POST["password"]).$_POST["username"]);

$pass_view="�����: <b>".$_POST["username"]."</b> <br>������: <b>".$_POST["password"]."</b>";

$vow='//// ��� ������ ��� ������� �������� � passwords.php <br>
$useraccess_fix_by_imperator = "'.$login.'"; //��� <br>
$passaccess_fix_by_imperator = "'.$password.'"; //������ � md5 ������ <br>
//// ��� ������ ��� ������� �������� � passwords.php';


echo "
<table border=0 align=center cellspacing=0 cellpadding=5>

<tr><td class=a colspan=2 align=center>".$pass_view."<br></td></tr>
<tr><td class=a colspan=2 align=center>".$vow."</td></tr>

<tr><td class=a colspan=2 align=center><a href=creating_adminpas.php>�������� �������</a></td></tr>
</table>";

die;
}





?>
<form method=post action=creating_adminpas.php>
<table border=0 align=center cellspacing=0 cellpadding=5>

<tr><td class=a colspan=2 align=center>�������� ������ ��� �������</td></tr>
<tr><td class=a>������� �����: </td><td class=b><input type=text name=username size=40></td></tr>
<tr><td class=a>������� ������: </td><td class=b><input type=text name=password size=40></td></tr>
<tr><td  class=a colspan=2 align=center><input type=submit value="������������� ������" class=btn></td></tr>
</table>
</form>
