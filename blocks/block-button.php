<?
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}
global $CURUSER, $SITENAME;

$blocktitle = "��������";


if ($CURUSER) 
{
 $site = "<center>���� ���� <a title=\"������ ���������� ����������!\"href=\"support.php\">����</a> ?</center>";
}


$content=  "
<!---����������� ����������! �� �������� :lol:--->
<div align=\"center\">
<a href=\"http://tesla-tracker.net\" target=\"_blank\"><img alt=\"��������������� ��� ������!\" src=\"http://img3.imageshack.us/img3/7658/muztracker.gif\"/></a><br/><br/>
BB ���:<br/>
<textarea readonly rows=\"4\" cols=\"20\">
[url=http://tesla-tracker.net][img]http://img3.imageshack.us/img3/7658/muztracker.gif[/img][/url]</textarea><br/>
Html ���:<br/>
<textarea readonly rows=\"8\" cols=\"20\">
<a target=\"_blank\" href=\"http://tesla-tracker.net\"><img src=\"http://img3.imageshack.us/img3/7658/muztracker.gif\" alt=\"��������������� ��� ������\"></a>
</textarea>
$site 
</div>";

//$content .= "<center><img src=\"counter/counter.php\" width=88 height=31 border=0></center>";

 
?>