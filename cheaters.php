<?
require ("include/bittorrent.php");
dbconn();
loggedinorreturn();



accessadministration();
if (get_user_class() < UC_SYSOP) {
attacks_log('cheaters'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}


if(!empty($_POST["nowarned"]) && $_POST["nowarned"] == "nowarned"){

if (empty($_POST["desact"]) && empty($_POST["remove"]))
stderr("��������", "�� ������� ������������");

if (!empty($_POST["remove"]))
{
foreach ($_POST["remove"] as $chit){
	
$cho=(int)$chit;
if (!empty($cho))
sql_query("DELETE FROM cheaters WHERE id=".sqlesc($cho)) or sqlerr(__FILE__, __LINE__);
}
}

if ($_POST["desact"])
{
////// ���� ������ �����
$res1 = sql_query("SELECT * FROM cheaters WHERE userid IN (" . implode(", ", array_map("sqlesc", $_POST["desact"])) . ")") or sqlerr(__FILE__, __LINE__); ///(" . implode(", ", $_POST["desact"]) . ")
$arr1 = mysql_fetch_array($res1);

$uppd = mksize($arr1["upthis"]);
$rate = $arr1["rate"];
$timediff = $arr1["timediff"];
$cheater = "�����: $uppd ��������: $rate/��� �����: $timediff ������";
////// ���� ������ � ���������� � ������������
$res = mysql_query("SELECT modcomment FROM users WHERE id IN (" . implode(", ", array_map("sqlesc", $_POST["desact"])) . ")") or sqlerr(__FILE__, __LINE__); ///(" . implode(", ", $_POST["desact"]) . ")
$arr = mysql_fetch_assoc($res);
$modcomment2 = $arr["modcomment"];
$modcomment = date("Y-m-d") . " - ������� ��� ����� ($cheater).\n".$modcomment2;

$updateset[] = "enabled = 'no'";
$updateset[] = "modcomment = '$modcomment'";

if (!empty($_POST["desact"]))
{
$maxclass = UC_ADMINISTRATOR;
////// ��������� ��� � ����� �������
sql_query("UPDATE users SET " . implode(", ", $updateset) . " WHERE class <= $maxclass AND id IN (" . implode(", ", array_map("sqlesc", $_POST["desact"])) . ")") or sqlerr(__FILE__, __LINE__); 
//(" . implode(", ", $_POST["desact"]) . ")
}
}

header("Refresh: 0; url=cheaters.php");
}





stdhead("���������� �������");

begin_main_frame();
begin_frame("���� ��������� ������ � ���� ������", true);
/*
56 (queries) - 79.08% (php) - 20.92% (0.0198 => sql) - 1473 �� (use memory)

 8 (queries) - 89.10% (php) - 10.90% (0.0056 => sql) - 1450 �� (use memory)
*/
$limit2="60";
$res = sql_query("SELECT COUNT(*) FROM cheaters LIMIT $limit2") or sqlerr();
$row = mysql_fetch_array($res);

if (!$row[0]){
print "��� �������";
end_frame();
end_main_frame();
stdfoot();
die;}

$count = $row[0];

list($pagertop, $pagerbottom, $limit) = pager($limit2, $count, "cheaters.php?");
print("<table border=0 width=\"100%\" cellspacing=0 cellpadding=0><tr><td align=left>$pagertop</td></tr></table><br />");
// end

?>
<form action="cheaters.php" method=post>
<script language="JavaScript" type="text/javascript">
<!-- Begin
var checkflag = "false";
function check(field) {
if (checkflag == "false") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = "true";
return "����� �� ���� ����������"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = "false";
return "�������� ���� ��� ����������"; }
}

function check2(field) {
if (checkflag == "false") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = "true";
return "����� ��������� �� ���� �������������"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = "false";
return "�������� ���� ��� ������ ����������"; }
}
// End -->
</script>

<?

print("<table width=\"100%\">\n");

print("<td class=\"a\" width=10 align=center valign=middle>#</td>
<td class=\"a\">��������� ���������� � ������������ � ��� ������ � ���������</td>
<td class=\"a\" width=10 align=center valign=middle>���������</td>
<td class=\"a\" width=10 align=center valign=middle>���������</td>");

$res = sql_query("SELECT c.id,c.added,c.userid,c.torrentid,c.client,c.rate,c.beforeup,c.upthis,c.timediff,c.userip,c.numpeers,u.id AS id_user, u.username, u.class, u.downloaded, u.uploaded, u.enabled,u.warned,t.name FROM cheaters AS c INNER JOIN users AS u ON c.userid = u.id INNER JOIN torrents AS t ON c.torrentid = t.id ORDER BY c.added DESC $limit") or sqlerr(__FILE__, __LINE__);
while ($arr = mysql_fetch_assoc($res))
{

if($arr["downloaded"] > 0)
{
$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
}
else
{
$ratio = "<a title=\"�������\">---</a>";
}
$ratio = "<font color=" . get_ratio_color($ratio) . "><a title=\"�������\">$ratio</a></font>";


$uppd = mksize($arr["upthis"]);


$cheater = "
������� ������: <a target='_blank' href=\"userdetails.php?id=$arr[id_user]\">".get_user_class_color($arr["class"], $arr["username"])."</a> [$ratio]
<br />�����: <b>$uppd</b>
<br />��������: <b>$arr[rate]/���</b>
<br />�����������������: <b>$arr[timediff] ������</b>
<br />���-������: <b>$arr[client]</b>
<br />Ip �����: <b><a target='_blank' href=\"$DEFAULTBASEURL/usersearch.php?n=&rt=0&r=&r2=&st=0&em=&ip=$arr[userip]\">$arr[userip]</a></b>
<br />������� ����: 
<a target='_blank' href=\"details.php?id=$arr[torrentid]\">$arr[name]</a>
<br />�����+�����: ".$arr["numpeers"]."
";

print("<tr><td ".($arr["numpeers"]==1?"class=\"b\"":"class=\"a\"")." width=\"10\" align=center>$arr[id]</td>");
print("<td class=\"tableb\" align=left>� <a href=\"javascript: klappe_news('a$arr[id]')\">$arr[added]</a> ��� ��������  <a target='_blank' href=\"userdetails.php?id=$arr[id_user]\">".get_user_class_color($arr["class"], $arr["username"])."</a> 
".($arr["enabled"] == "no" ? "<img src=\"/pic/warned2.gif\" alt=\"��������\">" : "")."
".($arr["warned"] == "yes" ? "<img src=\"/pic/warned9.gif\" alt=\"������������\">" : "")."
");
print("<div id=\"ka$arr[id]\" style=\"display: none;\">$cheater</div></td>");
print("<td align=\"center\" class=\"tableb\" valign=\"top\" width=10><input type=\"checkbox\" name=\"desact[]\" value=\"" . $arr["id_user"] . "\"/></td>");
print("<td align=\"center\" class=\"tableb\" valign=\"top\" width=10><input type=\"checkbox\" name=\"remove[]\" value=\"" . $arr["id"] . "\"/></td></tr>");
}
if (get_user_class() >= UC_ADMINISTRATOR)
{
?>
<tr>
<td  colspan="4" align="center">
<input type="button" value="�������� ���� ��� ����������" onclick="this.value=check(this.form.elements['desact[]'])"/> <input type="button" value="�������� ���� ��� ������ ����������" onclick="this.value=check2(this.form.elements['remove[]'])"/> <input type="hidden" name="nowarned" value="nowarned"/><input type="submit" name="submit" value="���������"/>
</td>
</tr>
</table></form>
<? }
print("<br /><table border=0 width=\"100%\" cellspacing=0 cellpadding=0><tr><td align=left>$pagerbottom</td></tr></table>");
// end

end_frame();
end_main_frame();
stdfoot();
die;

?>