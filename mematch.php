<?php

/**
 * @author 7Max7
 * @copyright 2010 for Tesla TT
 */

require ("include/bittorrent.php");
dbconn();

if (get_user_class() < UC_USER)
stderr($tracker_lang['error'], $tracker_lang['access_denied']);

parked();

stdheadchat("��������� �� �������� ��������� ������.");

global $procents;

$idin = array("6","10","11","26");

$my = sql_query("SELECT COUNT(*), cn.torrent FROM snatched AS cn 
LEFT JOIN torrents ON torrents.id=cn.torrent
WHERE cn.userid = ".sqlesc($CURUSER["id"])." AND torrents.category IN (".implode(",", $idin).") AND torrents.added>=DATE_SUB(NOW(), INTERVAL 31 DAY)") or sqlerr(__FILE__, __LINE__);

$myrow = mysql_fetch_array($my);
$count = $myrow[0];

echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" class=\"main\">";

echo "<td align=\"center\" class=\"a\">��������</td><td align=\"center\" class=\"a\">������� (�� ���������� � ��������)</td></tr>";

if (empty($count)){
echo "<tr><td colspan=\"2\" align=\"center\" class=\"b\">��������� ��������� ���.</td></tr>"; 
} else {


echo "<tr><td colspan=\"2\" align=\"center\" class=\"b\">������ ������� ���������� ������� ������� ����� (��� ��������� ������ ����� �� ������� ��������� ���������) �� ��������, ��� ������������ ������ <b>�����</b> ������ (������� ��� ����� ����������� ���������� ������). <br>
<b>������� ������ ��� ���������</b>: ����������, �������, ����������� � ���� ��� Windows.<br>
<b>��������</b>: �������� ������ ��� ������� <b>$procents%</b> �� ���������� ������ (������ ����������). </td></tr>"; 


//$per_list = $CURUSER["torrentsperpage"];
//if (empty($per_list) || $per_list>150)
$per_list = 10;

list($pagertop, $pagerbottom, $limit) = pager($per_list, $count, "mematch.php?");

echo "<tr><td colspan=\"2\" align=\"center\" class=\"a\">$pagertop</td></tr>"; 

$res = sql_query("SELECT cn.torrent, cn.startdat, cn.completedat, torrents.name, torrents.size, torrents.tags, torrents.category AS cat, torrents.added FROM snatched AS cn 
LEFT JOIN torrents ON torrents.id=cn.torrent
WHERE cn.userid = ".sqlesc($CURUSER["id"])." AND torrents.category IN (".implode(",", $idin).") AND torrents.added>=DATE_SUB(NOW(), INTERVAL 31 DAY) ORDER BY torrents.added DESC $limit") or sqlerr(__FILE__, __LINE__);


$dp = 0;

while ($row = mysql_fetch_array($res)){
///cn.finished='yes' AND 

if ($dp%2 == 0){
$clasto = "class = 'b'";
$clastf = "class = 'a'";
} else {
$clasto = "class = 'a'";
$clastf = "class = 'b'";
}

echo "<tr>";

if (!empty($row["tags"])) {
$tags[$row["id"]]="";
foreach(explode(",", $row["tags"]) as $tag) {
	
if (!empty($tags[$row["id"]]))
$tags[$row["id"]].=", ";

$tags[$row["id"]].= "<a style=\"font-weight:normal;\" href=\"browse.php?tag=".urlencode($tag)."&incldead=1\">".tolower($tag)."</a>";
}
$tags[$row["id"]]=$tags[$row["id"]];
}
else
$tags[$row["id"]]="�� �������";


echo "<td align=left ".$clasto." width=\"50%\"><a title=\"� ���������� �������� ".htmlspecialchars($row['name'])."\" href=details.php?id=".$row["torrent"].">".htmlspecialchars_uni($row['name'])."</a> [<a style=\"font-weight:normal;\" href=\"download.php?id=".$row["torrent"]."\">".mksize($row["size"])."</a>]
<br> ������ / ����� (����������): ".$row['startdat']." / ".$row['completedat']."<br>
<b>����</b>: ".$tags[$row["id"]]."
</td>";


$num = 1;

$search = htmlspecialchars_uni($row['name']);
//$search = substr($search, 0, 128); /// 64 ������ ����������

//$searchstr = substr(htmlspecialchars_uni($row['name']), 0, 100); /// ���� 64 ������ ����������

$search = preg_replace("/\(((\s|.)+?)\)/is", "", preg_replace("/\[((\s|.)+?)\]/is", "", $search));

$list = explode(" ", $search);
$ecr = array("(",")","]","[",".","@");
$listrow = array();
$listview = array();
foreach ($list AS $lis){
$idlist = (int) $lis;

if ((strlen($lis)< 2 && ($lis <> str_replace($ecr, '', $lis))) || !empty($idlist)){
//$listrow[] = "-".$lis;
}
elseif (strlen($lis)> 2 && ($lis == str_replace($ecr, '', $lis))){
$listrow[] = "+".$lis;
$listview[] = $lis;
}
//else
//$listrow[] = $lis;

}

$listrow = array_unique($listrow); /// ������� ���������
$listview = array_unique($listview); /// ������� ���������


echo "<td align=left ".$clastf." width=\"50%\"><small>������������ ���� � ������: ".trim(implode(", ", $listview))."</small><br>";



$num4 = 1;

/*
$res_q = sql_query("SELECT name, id, size, 
MATCH (torrents.name) AGAINST ('>" .$search. "< (".trim(implode(" ", $listrow)).")' IN BOOLEAN MODE) AS rel
FROM torrents WHERE torrents.added >= ".sqlesc($row["added"])." AND category = ".sqlesc($row["cat"])." AND id <>".sqlesc($row["torrent"])."  ORDER BY rel DESC LIMIT 15") or sqlerr(__FILE__, __LINE__);
*/

$sql=new MySQLCache("SELECT name, id, size FROM torrents WHERE torrents.added >= ".sqlesc($row["added"])." AND category = ".sqlesc($row["cat"])." AND MATCH (torrents.name) AGAINST ('".trim(implode(" ", $listrow))."' IN BOOLEAN MODE) ORDER BY added DESC LIMIT 15", 2*86400,"details_".md5(trim(implode(" ", $listrow))).".txt"); // ���� 2

//$num = mysql_num_rows($res_q);
$num = 0;
//if (empty($num))
//echo "������� ������ �� ���� ����� �� �������.";

$pogre = array();

//while ($row_q = mysql_fetch_array($res_q)) {

while ($row_q=$sql->fetch_assoc()){

$name1 = preg_replace("/\(((\s|.)+?)\)/is", "", preg_replace("/\[((\s|.)+?)\]/is", "", $row["name"]));
$name2 = preg_replace("/\(((\s|.)+?)\)/is", "", preg_replace("/\[((\s|.)+?)\]/is", "", $row_q["name"]));


$proc = @similar_text($name1, $name2);


if ($proc >= $procents && $row["torrent"]<>$row_q["id"]){
echo $num4." <a title=\"����������� �������� (���������� �� $proc%)\" href=\"details.php?id=".$row_q["id"]."\">".htmlspecialchars_uni($row_q["name"])."</a> [<a title=\"�������\" href=\"download.php?id=".$row_q["id"]."\">".mksize($row_q["size"])."</a>]";

echo "<br>";
$pogre[] = $proc;
++$num4;
++$num;
}
}

if ($num4 == 1){

if ($pogre > $procents)
echo "<i>������ ��� - ����� ��������.</i>";	
else
echo "�������� ������ <b>$proc%</b> - ����� ��������.";	
}

else
echo "�������� ������ � ��������� �� ".@min($pogre)."% �� ".@max($pogre)."%.";


echo "</td>";

unset($search,$row,$listrow);

++$dp;

}

}

echo "<tr><td colspan=\"2\" align=\"center\" class=\"a\">$pagerbottom</td></tr>"; 



echo "</tr></table>";


stdfootchat();


?>