<?
require "include/bittorrent.php";
dbconn(false);

loggedinorreturn();
if (get_user_class() < UC_MODERATOR)
stderr("��������", "������� ���! ������ ��� �������������.");

stdheadchat("�������� ��������� ������ � ������� ���������");


$view = ($_GET["view"]=="all"? $_GET["view"]:"");
$id = (int)$_GET["id"];


if ($view){
	$numu=0;
$dh = opendir('./torrents/txt/');
while ($file = readdir($dh)) :
$file_orig=$file;
if (preg_match('/^(.+)\.$/si', $file, $matches))
$file = $matches[1];


if (stristr($file,'txt'))
{

$file = str_ireplace(".txt","",$file);
$time=normaltime(get_date_time(filemtime("./torrents/txt/".$file_orig)));

$file22="<a title=\"$file ����� ����� $time\" href=\"". $_SERVER['PHP_SELF'] . "?id=$file\">$file</a>";
$numu++;
}
if ($file_name)
$file_name.=", ";
$file_name.=$file22;

endwhile;

closedir($dh);
$file_name = str_ireplace(", "," ",$file_name);
///print"$file_name";
}


echo "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"75%\"><tr><td align=center class=b>
".($view ? "������ ���� ������ - $numu":"<form method=\"get\" action=\"". $_SERVER['PHP_SELF'] . "\">
������� id ������� ��� �������� ������� ��������: <input type=\"text\" value=".htmlspecialchars((int)$_GET['id'])." name=\"id\" size=\"20\" /> <input type=\"submit\" class=\"btn\" value=\"������ ��������\">
</form>
<form method=\"get\" action=\"". $_SERVER['PHP_SELF'] . "\"><input type=\"hidden\" name=\"view\" value=\"all\"><input type=\"submit\" class=\"btn\" value=\"����������� ��� �����\"></form>
")."</td></tr></table><br>"; 

if ($view && $id){
echo "<table cellpadding=\"4\" cellspacing=\"1\" border=\"0\" style=\"width:100%\">";
echo "<tr><td class=a>$file_name</td></tr>";
}
elseif ($view && empty($id)){
echo "<table cellpadding=\"4\" cellspacing=\"1\" border=\"0\" style=\"width:100%\">";
echo "<tr><td class=a>$file_name</td></tr>";
}


if ($id && empty($view)){

if(!file_exists("./torrents/txt/$id.txt"))
{die("��������� ���� ��� ��������: $id - ��� � ������, ������ ������.");
}

echo "<table cellpadding=\"4\" cellspacing=\"1\" border=\"0\" style=\"width:100%\">";

$fop = fopen ("./torrents/txt/$id.txt", "r+");

while (!feof($fop))
{
$read = htmlspecialchars(fgets($fop, 1000));
//$read = fgets($fop, 1000);

/*
$read = str_ireplace("��������","<b>��������</b>",$read);
$read = str_ireplace("���","<b>���</b>",$read);
$read = str_ireplace("������","<b>������</b>",$read);
$read = str_ireplace("���������� ������","<b>���������� ������</b>",$read);
$read = str_ireplace("��������","<b>��������</b>",$read);
$read = str_ireplace("�����","<b>�����</b>",$read);
$read = str_ireplace("�����","<b>�����</b>",$read);
$read = str_ireplace("�����","<b>�����</b>",$read);
*/
echo "<tr bgcolor=white><td class=tablea >$read</td></tr>";
}
fclose($fop);
}

stdfootchat();
?>