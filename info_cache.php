<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
if (get_user_class() < UC_SYSOP)
stderr("��������", "������� ���!");
accessadministration();


 
if ($_POST["prune"]){
@unlink(ROOT_PATH."cache/info_cache_stat.txt");
@header("Location: $DEFAULTBASEURL/info_cache.php") or die("��������������� �� ��� �� ��������.<script>setTimeout('document.location.href=\"$DEFAULTBASEURL/info_cache.php\"', 10);</script>");
}
 
stdheadchat("���������� ��������� ����� ������� info();");

print "<center><a href=\"hacklog.php\">XSS ������� (ctracker)</a> : <a href=\"sqlerror.php\">SQL ������</a> : <a href=\"error_torrent.php\">������ ��� �������</a></center><br>";

if(!file_exists(ROOT_PATH."cache/info_cache_stat.txt"))
{die("����� ��������� ������� Info ��� � ������, ������ ������.");
	}

?><tr>
<table cellpadding="4" cellspacing="1" border="0" style="width:100%" class="tableinborder">
<td class="a" valign="top">�����</td>
<td class="a" valign="top">IP</td>
<td class="a" valign="top">�������</td>
<td class="a" valign="top">������</td>
<td class="a" valign="top">��������</td>
</tr>
<? $fop = fopen (ROOT_PATH."cache/info_cache_stat.txt", "r+");
while (!feof($fop))
{
$read = fgets($fop, 1000);
list($date,$time,$ip,$hua,$from,$host) = explode('#',$read);
$ip = str_replace(" ","",$ip);
if (!empty($ip) && $time  && $hua){
$r = mysql_query("SELECT username,class,id FROM users WHERE ip=".sqlesc($ip)."") or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_assoc($r);
if (!$user["username"])
$username2=" <b>�� ������</b>";
else
$username2="<a href=userdetails.php?id=$user[id]>".get_user_class_color($user["class"],$user["username"])."</a>";
unset($user["class"]);unset($user["username"]);unset($user["id"]);
}

$agent = $hua;
if (!empty($agent) && $date) {

$hache = crc32(htmlentities($agent));
$agent = htmlentities($agent);

$result = mysql_query("SELECT id FROM useragent WHERE crc32=$hache ORDER BY id");
$num = mysql_fetch_assoc($result);
if ($num){
$n1=$num["id"];
$n=$num["id"].",";
$nn=",".$num["id"].",";
$chislo=0;
$query = mysql_query("SELECT id,username,class FROM users WHERE idagent LIKE '%$nn%' OR idagent LIKE '$n' ORDER BY last_access DESC LIMIT 10");

while ($qu = mysql_fetch_array($query))
{
$id=$qu["id"];
$username=$qu["username"];
$class=$qu["class"];


if ($who[$num["id"]])
$who[$num["id"]].=", ";
$who[$num["id"]].= "<a href=userdetails.php?id=$id>".get_user_class_color($class,$username)."</a>";
$chislo++;

}

}}


if ($date && $hua){
echo "<td class=b >$date</a></b><br>$time</td>";
echo "<td class=b ><a href=/usersearch.php?n=&rt=0&r=&r2=&st=0&em=&ip=$ip>$ip </a><br>$username2</td></td>";
echo "<td class=b >$hua ".($who[$num["id"]] ? "<br><a title=\"����� 10\">������� ��� �</a>: ".$who[$num["id"]]:"")."</td>";
echo "<td class=b ><a href=$from>$from</td>";
echo "<td class=b ><a href=$host>$host</td>";
echo " </tr>";}
unset($qu["id"]);unset($who[$num["id"]]);
}
fclose($fop);

?>
<table width="100%" border="0">

<?

echo"<hr>
<form method=\"post\" action=\"info_cache.php\" name=\"add\">
<input type=\"submit\" name=\"Submit\" value=\"�������� ����������\">
<input type=\"hidden\" name=\"prune\" value=\"prune\">
</form>";

 stdfootchat(); ?>