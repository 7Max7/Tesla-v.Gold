<?
require "include/bittorrent.php";
dbconn(false);

stdheadchat("����� ���������� ���������� �������.");

/// �� ��������� ������� ����������� �����
if (date('i')%2==0) {

$resf = sql_query("SELECT * FROM license WHERE datetime < DATE_SUB(NOW(), INTERVAL 31 DAY)") or sqlerr(__FILE__,__LINE__);
while ($rowf = mysql_fetch_array($resf)) {
//echo $rowf["name"]." � ".$rowf["datetime"]."<br>";
write_log("��������� ���� ".$rowf["info_hash"]." (".htmlspecialchars_uni($rowf["name"]).") ���� ��������: ����� ������� �������.\n","","torrent");
}
sql_query("DELETE FROM license WHERE datetime < DATE_SUB(NOW(), INTERVAL 31 DAY)") or sqlerr(__FILE__,__LINE__);

}



$searchstr = ($_GET["n"] ? htmlspecialchars(trim($_GET["n"])):false);
$get_type = ($_GET["t"] ? (int) $_GET["t"]:false);

$addparam = "";
$wherea = array();

if ($get_type == 1 && !empty($searchstr)){

$addparam .= "t=1&amp;n=".$searchstr."&amp;";
$searchstr = substr($searchstr, 0, 100); /// ���� 64 ������ ����������

$wherea[] = "license.info_hash =".sqlesc($searchstr); /// info_hash
}
elseif ($get_type == 2 && !empty($searchstr)){

$addparam .= "t=2&amp;n=".$searchstr."&amp;";
$searchstr = substr($searchstr, 0, 100); /// ���� 64 ������ ����������

//$q2 = str_replace("."," ",sqlesc("%".sqlwildcardesc($searchstr)."%"));
//$wherea[] = "license.desc LIKE ".$q2;
$wherea[] = "license.desc LIKE '%".$searchstr."%'";
}
elseif (empty($get_type) && !empty($searchstr)){

$addparam .= "t=0&amp;n=".$searchstr."&amp;";
$searchstr = substr($searchstr, 0, 100); /// ���� 100 ������ ����������

$q2 = str_replace("."," ",sqlesc("%".sqlwildcardesc($searchstr)."%"));

$wherea[] = "license.name LIKE ".$q2;
}

if (count($wherea))
$where = implode(" AND ", $wherea);

//print_r($wherea);

if (!empty($where))
$where = "WHERE ".$where;

$res = sql_query("SELECT COUNT(*) FROM license $where") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$count = $row[0];

if ($addparam{strlen($addparam)-1} != ";" && strlen($addparam)>5) // & = &amp;
$addparam = $addparam . "&";

list($pagertop, $pagerbottom, $limit) = pager(50, $count, "license.php?".$addparam);

echo '<table width="100%" cellpadding="5"><tr>
<td width="3%" class="colhead">#</td>
<td align=center class="colhead">��������</td>
<td align=center class="colhead">SHA-1 (���-�����) / ������</td>
<td align=center class="colhead">���������� �����</td>
</tr>';

echo "<tr><td colspan=\"4\" class=\"b\" align=\"center\"><b>��������</b>: ��� <b>��������� �����</b> ��������� �� ����� ���������, � <u>��������</u> � <u>��������</u> (������� <u>������ ������</u>) ��������� � ��� �������. <br>���� �� ���������������, �� ������ ��������������� <a href=\"support.php\">���� �������</a> (<u>���� ���������</u> -> <b>���������������</b>).</td></tr>";

if ($get_type == 1)
$viewty = "SHA-1 (���-�����)";
elseif ($get_type == 2)
$viewty = "������ (� ��������)";
elseif (empty($get_type))
$viewty = "��������";

echo "<tr><td colspan=\"4\" class=\"b\" align=\"center\">
<form method=\"get\" action=\"license.php\">
<b>������� �����</b>: 
<input type=\"text\" id=\"searchinput\" name=\"n\" size=\"64\" class=\"searchgif\" value=\"".htmlspecialchars($searchstr)."\" />

<select name=\"t\">
<option value=\"0\">��������</option>
<option value=\"1\" ".($get_type == 1 ? " selected" : "").">SHA-1 (���-�����)</option>
<option value=\"2\" ".($get_type == 2 ? " selected" : "").">������ (� ��������)</option>
</select>

<input class=\"btn\" type=\"submit\"  style=\"width: 100px\" value=\"�����\" />
</form>
</td></tr>";

if (empty($count) && empty($where))
echo ("<tr><td colspan=\"4\" class=\"b\" align=\"center\">��� ������������ ������</td></tr>");
elseif (!empty($where) && empty($count))
echo ("<tr><td colspan=\"4\" class=\"b\" align=\"center\">����� <b>".htmlspecialchars($searchstr)."</b> �� <b>".$viewty."</b> ��������. <br> ���������� �������� ��� ������, �� <b>��������</b>, �� <b>SHA-1 (���-�����)</b>, �� <b>������ (� ��������)</b>.</td></tr>");
elseif (!empty($where) && !empty($count))
echo ("<tr><td colspan=\"4\" class=\"a\" align=\"center\">����� <b>".htmlspecialchars($searchstr)."</b> �� <b>".$viewty."</b></td></tr>");



$res = sql_query("SELECT *, DATE_ADD(datetime, INTERVAL 31 DAY) AS futime FROM license $where ORDER BY id DESC $limit") or sqlerr(__FILE__,__LINE__);
$num=0;

while ($row = mysql_fetch_array($res)){

$info_hash = $row["info_hash"];

$desc = format_comment($row["desc"]);
if (strlen($desc)>=8000)
$desc = "����� �������� ��������� ����� �������� (����� 8 ��� ��������).";

$tname = htmlspecialchars_uni($row["name"]);


$id_t = $row["id"];

if ($num%2==0){
$td1='class="b"';
$td2='class="a"';
} else {
$td1='class="a"';
$td2='class="b"';
}

if ($CURUSER)
$info_hash = "<a target=\"_blank\" href=\"browse.php?search=".$info_hash."&stype=4&gr=0&incldead=1&cat=0\">".$info_hash."</a>";

if ($CURUSER)
$tname = "<a target=\"_blank\" href=\"browse.php?search=".$tname."&stype=0&gr=0&incldead=1&cat=0\">".$tname."</a>";

$num_license = 0;
//////////////////////////// ��������� ������� - ������ �������
$sqlinfo_hash = $row["info_hash"];
$num_license = get_row_count("torrents","WHERE info_hash=".sqlesc($sqlinfo_hash));

if (!empty($num_license)) {

$tname = "<b>".$tname."</b><br> ������� � ������� $num_license ����������.";


$res = sql_query("SELECT id, name, image1 FROM torrents WHERE info_hash=".sqlesc($sqlinfo_hash)) or sqlerr(__FILE__,__LINE__);

while ($row1 = mysql_fetch_array($res)) {

$id_t = $row1["id"];

$tname = htmlspecialchars_uni($row1["name"]);

$reasonstr = "������� ���������� ������� (license).";

write_log("������� $id_t ($tname) ��� ������ ��������. �������: $reasonstr\n", "F25B61","torrent");

$id_t=$row["id"];

@unlink(ROOT_PATH."torrents/$id_t.torrent");

if (!preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $row["image1"]) && !empty($row["image1"]))
@unlink(ROOT_PATH."torrents/images/".$row["image1"]);	 

sql_query("DELETE FROM torrents WHERE id=".sqlesc($id_t)) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM snatched WHERE torrent=".sqlesc($id_t)) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM ratings WHERE torrent=".sqlesc($id_t)) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM checkcomm WHERE checkid=".sqlesc($id_t)." AND torrent = 1") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM bookmarks WHERE torrentid=".sqlesc($id_t)) or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM files WHERE torrent=".sqlesc($id_t)) or sqlerr(__FILE__,__LINE__);

}
}
unset($sqlinfo_hash);

echo "<tr>";

echo "<td width=\"3%\" align=center ".$td1.">".$id_t."</td>";
echo "<td width=\"25%\" align=center ".$td2.">".$tname."</td>";
echo "<td width=\"15%\" align=center ".$td1.">".$info_hash."<br>".$row["datetime"]." - ".$row["futime"]."</td>";
echo "<td width=\"20%\" align=center ".$td2.">".$desc."<br><small>��������: ".get_elapsed_time(sql_timestamp_to_unix_timestamp($row["futime"]))." �� ��������.</small></td>";

echo "</tr>";


++$num;
}

echo "<tr><td colspan=\"4\" class=\"b\" align=\"center\"><b>��������������</b>: ��� ��������� �� ������� ����������� �����, ��������� �� ���� ������� ������ ����� ����� ����������. <br>���� �� ��������������� � ����� ��������� ����� ��������� �������� (������� �����), �� ������ ��������������� <a href=\"support.php\">���� �������</a> (<u>���� ���������</u> -> <b>��������������� (��������� ����� �������)</b>).</td></tr>";

  if ($count) {
    echo '<tr><td colspan="4">';
    echo $pagerbottom;
    echo '</td></tr>';
  }

  echo '</tr></table>';


stdfootchat();

?>
