<?
require "include/bittorrent.php";
dbconn(false);
accessadministration();
loggedinorreturn();
if (get_user_class() < UC_SYSOP)
stderr("��������", "������� ���!");

if ($_POST["prune"]){
@unlink(ROOT_PATH."cache/error_torrent.txt");

@header("Location: $DEFAULTBASEURL/error_torrent.php") or die("��������������� �� index ��������.<script>setTimeout('document.location.href=\"$DEFAULTBASEURL/error_torrent.php\"', 10);</script>");
}


stdheadchat("���������� ������ ������ �������� [1000]");

print "<center><a href=\"hacklog.php\">XSS ������� (ctracker)</a> : <a href=\"info_cache.php\">������� ��������� (info)</a> : <a href=\"sqlerror.php\">SQL ������</a></center><br>";


if(!file_exists(ROOT_PATH."cache/error_torrent.txt"))
{die("���� ��� error_torrent.txt ��� � ������, ������ ������.");
}


echo "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"75%\">"; 
echo "
<table cellpadding=\"4\" cellspacing=\"1\" border=\"0\" style=\"width:100%\">
<td class=\"a\" valign=\"top\">ID</td>
<td class=\"a\" valign=\"top\">IP</td>
<td class=\"a\" valign=\"top\">Passkey / �����</td>
<td class=\"a\" valign=\"top\">�����</td>
<td class=\"a\" valign=\"top\">������</td>
<td class=\"a\" valign=\"top\">�����</td>
";

 $fop = fopen (ROOT_PATH."cache/error_torrent.txt", "r+");
 $lim=1;
while (!feof($fop))
{
$read = fgetss($fop, 1000);
list($empty,$passkey,$time,$ip,$data,$agent) = explode('#',$read);

//$ip = str_replace(" ","",$ip);
if (strlen($passkey)== 32){
$r = mysql_query("SELECT id,username,class FROM users WHERE passkey=".sqlesc($passkey)."") or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_assoc($r);
if (!$user["username"])
$username="<i>�� ������</i>";
else
$username="<a href=userdetails.php?id=" . $user["id"] . ">".get_user_class_color($user["class"] ,$user["username"])."</a>";
} else {
if (empty($passkey)){
$passkey="<b>�� ������</b> (����)";}
$username=" <i>�� ������ ����� => ������� �������</i>";}

if (stristr($data,'������� ��� ���������� �����������')!==false && strlen($passkey)== 32)
{
 $data_s=str_replace("������� ��� ���������� �����������: ","",$data);
 $data_s=(int)(trim($data_s));


$tor = mysql_query("SELECT name FROM torrents WHERE id=".sqlesc($data_s)."") or sqlerr(__FILE__, __LINE__);
$u = mysql_fetch_assoc($tor);
if ($u["name"]){
$torrent="<a title=\"�������� ����� ���� ����� �� ����� �� ����\" href=\"details.php?id=$data_s\">".$u["name"]."</a>";

$data=str_replace($data_s,$torrent,$data);
}
}


if (isset($data)&&isset($ip)){
//bgcolor=".($data ? "lightgray" : "white")."
echo "<tr >
<td class=b>$lim</td>";
echo "<td class=b >$ip</td>";
echo "<td class=b>$passkey<br>$username</td>";
echo "<td class=b><b>[</b>".display_date_time($time)."<b>]</b></td>";
echo "<td class=b>$data</td>";
echo "<td class=b>$agent</td>";

echo " </tr>";}
unset($data);
unset($data_s);
unset($torrent);
$lim++;
}
fclose($fop);





echo"<table width=\"100%\" border=\"0\"><hr>
<form method=\"post\" action=\"error_torrent.php\" name=\"add\">
<input type=\"submit\" name=\"Submit\" value=\"�������� ����������\">
<input type=\"hidden\" name=\"prune\" value=\"prune\">
</form>";


   stdfootchat();
  
?>

