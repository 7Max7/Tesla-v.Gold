<?php
require "include/bittorrent.php";
gzip();
dbconn();
loggedinorreturn();

if (get_user_class() < UC_SYSOP) {
attacks_log('b_actions'); 
stderr($tracker_lang["error"], $tracker_lang["access_denied"]);
}

accessadministration();

stdhead("������� � ���������");



$actions = (string) $_POST["actions"]; /// ��� �����
$cheKer = $_POST["cheKer"]; /// ��� ������
$ref = htmlspecialchars_uni($_POST["referer"]);

echo "<table class=\"embedded\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">
<tr>
<td class=\"colhead\" align=\"center\" colspan=\"4\">������� � ���������</td>
</tr>
<tr>
<td class=\"a\" align=\"center\">������ � ��������</td>
<td class=\"a\" width=\"25%\" align=\"center\">��������</td>
</tr>";


if (!$cheKer) {
echo "<tr><td class=\"b\" align=\"center\" colspan=\"3\">�� ������� �������� ��� ��������</td></tr>\n";
echo "</table>";
stdfoot();
die;
}

if (!$actions) {
echo "<tr><td class=\"b\" align=\"center\" colspan=\"3\">�� ������� �������� �� ��������� � ���������</td></tr>\n";
echo "</table>";
stdfoot();
die;
}
$updateset = array();
$where = array();
$viewup = "";

if ($actions == "main"){
/// ��������� ��������
$updateset[] = "sticky = 'yes'";
$where = "sticky = 'no'";

$torrent_com = get_date_time() . " ".$CURUSER["username"]." �������� ��������.\n";
$viewup = "���������� ��������";
}

elseif ($actions == "multi"){
/// �������� ����� �������� �����
$updateset[] = "multi_time = '0000-00-00 00:00:00'";
$where = "multi_time <> '0000-00-00 00:00:00'";
	
$viewup = "�������� ����� ���������� ����� �������������";
}

elseif ($actions == "unmain"){
/// ����� ��������
$updateset[] = "sticky = 'no'";
$where = "sticky = 'yes'";

$torrent_com = get_date_time() . " ".$CURUSER["username"]." ���� ��������.\n";	
$viewup = "����� ��������";
}

elseif ($actions == "check"){
/// ��������� ���������
$updateset[] = "moderated = 'yes'";

$updateset[] = "moderatedby = ".sqlesc($CURUSER["id"]);
$updateset[] = "moderatordate = ".sqlesc(get_date_time());

$where = "moderated = 'no'";
$torrent_com = get_date_time() . " ".$CURUSER["username"]." ������� �����.\n";
$viewup = "���������� ���������";
}
elseif ($actions == "uncheck"){
/// ����� ���������
$updateset[] = "moderated = 'no'";

$where = "moderated = 'yes'";
$torrent_com = get_date_time() . " ".$CURUSER["username"]." ���� ���������.\n";
$viewup = "����� ���������";
}
elseif ($actions == "gold"){
/// ��������� ������
$updateset[] = "free = 'yes'";

$where = "free = 'no'";
$viewup = "���������� ������";
}
elseif ($actions == "ungold"){
/// ����� ������
$updateset[] = "free = 'no'";

$where = "free = 'yes'";
$viewup = "����� ������";
}

elseif ($actions == "newdate"){
/// ��������� ����� ����
$updateset[] = "added = NOW()";
$viewup = "���������� ����� ����";

}
elseif ($actions == "anonim"){
/// ��������� ����� ����
$updateset[] = "owner = '0'";
$viewup = "��������� ��� ��������� �����";

$torrent_com = get_date_time() . " ".$CURUSER["username"]." �������� ������ �����������.\n". $torrent_com;
}
elseif ($actions == "movcat") {

$pcat = (int) $_POST["pcat"];

$res_cat = sql_query("SELECT name FROM categories WHERE id = ".sqlesc($pcat)) or sqlerr(__FILE__,__LINE__);
$row_cat = mysql_fetch_array($res_cat);

if (empty($row_cat["name"]) || empty($pcat)){
echo "<tr><td class=\"b\" align=\"center\" colspan=\"3\">��������� ��������� ��� ����������� <u>�� ����������</u>!</td></tr>\n";
echo "</table>";
stdfoot();
die;
}

$res = sql_query("SELECT torrents.id, torrents.name, torrents.category, categories.name AS catname 
FROM torrents
LEFT JOIN categories ON categories.id=torrents.category
WHERE torrents.id IN (".implode (", ", array_map ("sqlesc", $cheKer)).") AND torrents.category<>".sqlesc($pcat)) or sqlerr(__FILE__,__LINE__);

if (mysql_affected_rows() == 0){
echo "<tr><td class=\"b\" align=\"center\" colspan=\"3\">����� ��������� ��� ����������� � ��������� ��������� ��������, ��������, ��� <u>������������ ��������</u> (������� �������) ��� ��� <u>����������</u>.</td></tr>\n";
}

while ($row = mysql_fetch_array($res)) {

$cat=$row["category"];
$catname=$row["catname"];
$tname=$row["name"];

echo "<tr>";
echo "<td class=\"b\" align=\"center\">".$row["id"]." - ".$tname."</td>";
echo "<td width=\"25%\" class=\"b\">����������� �� <s>".$catname."</s> ��������� � <u>".$row_cat["name"]."</u></td>";
echo "</tr>";

}

sql_query("UPDATE torrents SET category=".sqlesc($pcat)." WHERE id IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ")") or sqlerr(__FILE__,__LINE__);

if (!empty($ref))
echo "<tr><td class=\"b\" align=\"center\" colspan=\"3\">
<a href=\"".$ref."\">�������� �������</a>
</td></tr>\n";

echo "</table>";

stdfoot();
die;
}
elseif ($actions == "pravo"){

$res = sql_query("SELECT id, name, info_hash, image1,size FROM torrents WHERE id IN (".implode (", ", array_map ("sqlesc", $cheKer)).")") or sqlerr(__FILE__,__LINE__);

while ($row = mysql_fetch_array($res)) {

$info_hash = $row["info_hash"];

$id_t = $row["id"];

$descki = array();

$resf = sql_query("SELECT * FROM files WHERE torrent=".sqlesc($id_t)) or sqlerr(__FILE__,__LINE__);
while ($rowf = mysql_fetch_array($resf)) {
$descki[] = $rowf["filename"].":".mksize($rowf["size"])."\n";
}

$desc = implode("\n",$descki)."\n<b>����� ������</b>: ".mksize($row["size"]);

$tname = htmlspecialchars_uni($row["name"]);

$desc = "[spoiler=�������� ($id_t)]\n".$desc."[/spoiler]";

sql_query("INSERT INTO license VALUES (0,".sqlesc($tname).",".sqlesc($info_hash).",".sqlesc(get_date_time()).",".sqlesc(unesc($desc)).")");

$nid = mysql_insert_id();
/*
if (empty($nid)) {

$info_concat = sqlesc($info_hash."\n");
$q2 = str_replace("."," ",sqlesc("%".sqlwildcardesc(trim($tname))."%"));
$desc = $desc."\n";

//$res1 = sql_query("SELECT COUNT(*) FROM license WHERE name LIKE {$q2} AND info_hash NOT LIKE '%$info_hash%'") or sqlerr(__FILE__,__LINE__);
//$row2 = mysql_fetch_array($res1);

//if ($row2[0])//, desc = CONCAT_WS($desc, desc) 
sql_query("UPDATE license SET info_hash = CONCAT_WS($info_concat, info_hash) WHERE name LIKE {$q2}") or sqlerr(__FILE__,__LINE__);
}
//else {
//sql_query("UPDATE license SET desc = ".sqlesc($desc)." WHERE id=".sqlesc($nid)) or sqlerr(__FILE__,__LINE__);
//}
*/

unset($desc);

echo "<tr>";
echo "<td class=\"b\" align=\"center\">".$row["id"]." - ".$tname."</td>";
echo "<td width=\"25%\" class=\"b\">".($nid ? "+":"-")." �������� (������ � ������� � '��������' ����������� ������)</td>";
echo "</tr>";

$reasonstr = "������� ���������� ������� (browse).";

$ip_user = $CURUSER["ip"];
$user = $CURUSER["username"];

if (count($cheKer)<=101)
write_log("������� $id_t ($tname) ��� ������ ������������� $user. �������: $reasonstr\n", "F25B61","torrent");

if (count($cheKer)<=101)
@unlink(ROOT_PATH."cache/block-comment.txt");
 
$id_t=$row["id"];

@unlink(ROOT_PATH."torrents/$id_t.torrent");

if (!preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $row["image1"]) && !empty($row["image1"]))
@unlink(ROOT_PATH."torrents/images/".$row["image1"]);
}


sql_query("DELETE FROM torrents WHERE id IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ")") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM snatched WHERE torrent IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ")") or sqlerr(__FILE__,__LINE__);	
sql_query("DELETE FROM ratings WHERE torrent IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ")") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM checkcomm WHERE checkid IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ") AND torrent = 1") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM bookmarks WHERE torrentid IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ")") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM files WHERE torrent IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ")") or sqlerr(__FILE__,__LINE__);

if (!empty($ref))
echo "<tr><td class=\"b\" align=\"center\" colspan=\"3\">
<a href=\"".$ref."\">�������� �������</a>
</td></tr>\n";

echo "</table>";

stdfoot();
die;
}
elseif ($actions == "delete"){

$res = sql_query("SELECT id, name,image1,category,owner FROM torrents WHERE id IN (".implode (", ", array_map ("sqlesc", $cheKer)).")") or sqlerr(__FILE__,__LINE__);

while ($row = mysql_fetch_array($res)) {

$cat=$row["category"];
$cat_user=$CURUSER["catedit"];
$tname=$row["name"];

echo "<tr>";
echo "<td class=\"b\" align=\"center\">".$row["id"]." - ".$tname."</td>";
echo "<td width=\"25%\" class=\"b\">������� (�������)</td>";
echo "</tr>";

$reasonstr = "������ ��� �� �������� ��� ������� (browse).";

$ip_user = $CURUSER["ip"];
$user = $CURUSER["username"];

if (count($cheKer)<=101)
write_log("������� $row[id] ($tname) ��� ������ ������������� $user. �������: $reasonstr\n", "F25B61","torrent");

if (count($cheKer)<=101)
@unlink(ROOT_PATH."cache/block-comment.txt");
 
$id_t=$row["id"];

@unlink(ROOT_PATH."torrents/$id_t.torrent");
if (!preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $row["image1"]) && !empty($row["image1"]))
@unlink(ROOT_PATH."torrents/images/".$row["image1"]);
}

sql_query("DELETE FROM torrents WHERE id IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ")") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM snatched WHERE torrent IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ")") or sqlerr(__FILE__,__LINE__);	
sql_query("DELETE FROM ratings WHERE torrent IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ")") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM checkcomm WHERE checkid IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ") AND torrent = 1") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM bookmarks WHERE torrentid IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ")") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM files WHERE torrent IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ")") or sqlerr(__FILE__,__LINE__);

if (!empty($ref))
echo "<tr><td class=\"b\" align=\"center\" colspan=\"3\">
<a href=\"".$ref."\">�������� �������</a>
</td></tr>\n";

echo "</table>";

stdfoot();
die;
}


if ($actions <> "delete" && $actions <> "movcat" && $actions <> "pravo" && count($updateset)){

$res = sql_query("SELECT * FROM torrents WHERE id IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ") 
".(!empty($where) ? "AND ".$where:"")."
") or sqlerr(__FILE__,__LINE__);

if (mysql_affected_rows() == 0){
echo "<tr><td class=\"b\" align=\"center\" colspan=\"3\">����� ��������� ��������, ��������, ��� <u>������������ ��������</u> (������� �������) ��� ��� <u>�� ����������</u>.</td></tr>\n";
}

while ($row = mysql_fetch_array($res)){

echo "<tr>";
echo "<td class=\"b\" align=\"left\">".$row["id"]." - ".$row["name"]."</td>";
echo "<td width=\"25%\" align=\"center\" class=\"b\">".$viewup."</td>";
echo "</tr>";

}

if (!empty($torrent_com))
$updateset[] = "torrent_com = CONCAT_WS('',".sqlesc($torrent_com).", torrent_com)";

sql_query("UPDATE torrents SET ".implode(",", $updateset)." WHERE id IN (" . implode(", ", array_map("sqlesc", $cheKer)) . ") ".(!empty($where) ? "AND ".$where:"")."
") or sqlerr(__FILE__,__LINE__);


}

if (!empty($ref))
echo "<tr><td class=\"b\" align=\"center\" colspan=\"3\">
<a href=\"".$ref."\">�������� �������</a>
</td></tr>\n";


echo "</table>";

stdfoot();
?>