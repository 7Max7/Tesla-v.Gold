<? 

require "include/bittorrent.php"; 
gzip();
dbconn(false); 

parse_referer("empty");

//loggedinorreturn(); 

/*
if (get_user_class() < UC_MODERATOR) 
{
attacks_log('humorall'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}
*/

if (get_user_class() > UC_MODERATOR && isset($_POST["delmp"])) {

$link="humorall.php?page=".(int) $_GET["page"];
//accessadministration();
sql_query("DELETE FROM humor WHERE id IN (" . implode(", ", array_map("sqlesc", $_POST["delmp"])) . ")
") or sqlerr(__FILE__, __LINE__); //(" . implode(", ", $_POST["delmp"]) . ")

sql_query("DELETE FROM karma WHERE type='humor' AND value IN (" . implode(", ", array_map("sqlesc", $_POST["delmp"])) . ")") or sqlerr(__FILE__, __LINE__); 

@header("Location: $DEFAULTBASEURL/$link") or die("��������������� �� ��� �� ��������.<script>setTimeout('document.location.href=\"$DEFAULTBASEURL/$link\"', 10);</script>");
}

if (!empty($_GET["tu"])){
$where = "h.karma";
$wheretu="?tu=untop&";
$descsort="<a href=\"".$_SERVER["PHP_SELF"]."\">&#8659;���������� �� ������� ����������&#8659;</a>";
} else {
$where = "h.id";
$wheretu="?";
$descsort="<a href=\"".$_SERVER["PHP_SELF"]."?tu=untop\">&#8659;���������� �� �������&#8659;</a>";
}

$res2 = sql_query("SELECT COUNT(*) FROM humor")or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res2); 
$count = $row[0]; 
$perpage = 60;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"].$wheretu); 

stdheadchat("�������� ������������� [����� $perpage �� $count ����������]"); 

echo $pagertop; 

$idu = $CURUSER["id"];

$res = sql_query("SELECT h.id,h.karma,h.uid,h.date,h.txt,u.class,u.username ".($CURUSER? ",(SELECT COUNT(*) FROM karma WHERE type='humor' AND value = h.id AND user = $idu) AS canrate":"")." FROM humor AS h LEFT JOIN users AS u ON h.uid = u.id ORDER BY $where DESC $limit") or sqlerr(__FILE__, __LINE__); 

echo ("<table border=0 cellspacing=0 cellpadding=5>\n"); 
  
if (get_user_class() > UC_MODERATOR)
echo "<form method=\"post\" action=\"humorall.php\" name=\"form1\">";


echo ("<tr>
<td class=a align=center>����� $descsort</td>
<td class=a align=center>�����</td>
<td class=a align=center>�������</td>
".(get_user_class() > UC_MODERATOR ? "<td class=a><input type=\"checkbox\" title=\"".$tracker_lang['mark_all']."\" value=\"".$tracker_lang['mark_all']."\" onclick=\"this.value=check(document.form1.elements);\"></td>":"<td class=a>ID</td>")."
</tr>\n");

while ($arr = mysql_fetch_assoc($res)) {

////////////// ��� �������
if (!$arr["username"])
$sender = "<center><font color=red>[<b>id ".$arr["uid"]."</b>]</font><br><b>������</b></center>";
else
$sender = "<center><a href=userdetails.php?id=" . $arr["uid"] . "><b>".get_user_class_color($arr["class"], $arr["username"])."</b></a></center>";

$txt = htmlspecialchars_uni($arr["txt"]);
$date = $arr["date"]; 

?>
<script language="JavaScript" type="text/javascript">
function karma(id, type, act) {
jQuery.post("karma.php",{"id":id,"act":act,"type":type},function (response) {
jQuery("#karma" + id).empty();
jQuery("#karma" + id).append(response);
});
}
</script>

<?
if (get_user_class() > UC_MODERATOR){
?>
<script language="Javascript" type="text/javascript">
var checkflag = "false";
var marked_row = new Array;
function check(field) {
if (checkflag == "false") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = "true";
}
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = "false";
}
}
</script>
<?
}


if ($CURUSER){
if ($arr["canrate"] > 0)// || || $arr["uid"] == $CURUSER["id"]
$karma2="<span><img src=\"pic/minus-dis.png\" title=\"�� �� ������ ����������\" alt=\"\" /> " . karma($arr["karma"]) . " <img src=\"pic/plus-dis.png\" title=\"�� �� ������ ����������\" alt=\"\" /></span>\n";
else
$karma2="<span id=\"karma$arr[id]\"><img src=\"pic/minus.png\" style=\"cursor:pointer;\" title=\"��������� �����\" alt=\"\" onclick=\"javascript: karma('$arr[id]', 'humor', 'minus');\" /> " . karma($arr["karma"]) . " <img src=\"pic/plus.png\" style=\"cursor:pointer;\" onclick=\"javascript: karma('$arr[id]', 'humor', 'plus');\" title=\"��������� �����\" alt=\"\" /></span>\n";
}

echo ("<tr>
<td class=b>".$txt."</td>
<td class=b align=center>".$date."
<div style=\"text-align:center;\">
<a href=humor.php?id=".$arr['id']."><img style=\"border:none\" title=\"����������� �� ��������� ��������� �� ����������� �����������\" src=\"pic/mail-markread.gif\"></a> ".(get_user_class() >= UC_MODERATOR ? "<a href=humor.php?id=".$arr['id']."&do=edit><img style=\"border:none\" title=\"�������������\" src=\"pic/mail-create.gif\"></a> <a href=humor.php?id=".$arr['id']."&do=delete><img style=\"border:none\" title=\"�������\" src=\"pic/delete.gif\"></a>":"")."
</div>
</td>
<td align=\"center\" id=\"karma\">$sender $karma2</td>
");

if (get_user_class() > UC_MODERATOR)
echo ("<td class=a align=center><INPUT type=\"checkbox\" name=\"delmp[]\" value=\"".$arr['id']."\"><br>
".$arr["id"]."</td>\n"); 
else
echo ("<td class=b align=center>".$arr['id']."</td>");


}

echo ("</tr>"); 



if (get_user_class() > UC_MODERATOR)
echo "<tr><td class=b align=center colspan=\"3\">".$arr['id']."<input type=\"submit\" class=\"btn\" value=\"������� ���������� ���������\"/></td></tr>";

echo "</table>";

echo "</form><br>";


echo $pagerbottom;

stdfootchat(); 
?>