<?
require "include/bittorrent.php";
dbconn(false);

stdheadchat("Файлы защищенные авторскими правами.");

/// по истечению удаляем запрещенные файлы
if (date('i')%2==0) {

$resf = sql_query("SELECT * FROM license WHERE datetime < DATE_SUB(NOW(), INTERVAL 31 DAY)") or sqlerr(__FILE__,__LINE__);
while ($rowf = mysql_fetch_array($resf)) {
//echo $rowf["name"]." и ".$rowf["datetime"]."<br>";
write_log("Авторский файл ".$rowf["info_hash"]." (".htmlspecialchars_uni($rowf["name"]).") снят системой: время запрета истекло.\n","","torrent");
}
sql_query("DELETE FROM license WHERE datetime < DATE_SUB(NOW(), INTERVAL 31 DAY)") or sqlerr(__FILE__,__LINE__);

}



$searchstr = ($_GET["n"] ? htmlspecialchars(trim($_GET["n"])):false);
$get_type = ($_GET["t"] ? (int) $_GET["t"]:false);

$addparam = "";
$wherea = array();

if ($get_type == 1 && !empty($searchstr)){

$addparam .= "t=1&amp;n=".$searchstr."&amp;";
$searchstr = substr($searchstr, 0, 100); /// хотя 64 вполне устраивает

$wherea[] = "license.info_hash =".sqlesc($searchstr); /// info_hash
}
elseif ($get_type == 2 && !empty($searchstr)){

$addparam .= "t=2&amp;n=".$searchstr."&amp;";
$searchstr = substr($searchstr, 0, 100); /// хотя 64 вполне устраивает

//$q2 = str_replace("."," ",sqlesc("%".sqlwildcardesc($searchstr)."%"));
//$wherea[] = "license.desc LIKE ".$q2;
$wherea[] = "license.desc LIKE '%".$searchstr."%'";
}
elseif (empty($get_type) && !empty($searchstr)){

$addparam .= "t=0&amp;n=".$searchstr."&amp;";
$searchstr = substr($searchstr, 0, 100); /// хотя 100 вполне устраивает

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
<td align=center class="colhead">Название</td>
<td align=center class="colhead">SHA-1 (Хеш-сумма) / Период</td>
<td align=center class="colhead">Внутренние Файлы</td>
</tr>';

echo "<tr><td colspan=\"4\" class=\"b\" align=\"center\"><b>Внимание</b>: Все <b>авторские файлы</b> найденные на сайте удаляются, а <u>название</u> и <u>хешсумма</u> (включая <u>список файлов</u>) заносится в эту таблицу. <br>Если вы Правообладатель, то можете воспользоваться <a href=\"support.php\">этой ссылкой</a> (<u>тема сообщения</u> -> <b>правообладатель</b>).</td></tr>";

if ($get_type == 1)
$viewty = "SHA-1 (Хеш-сумма)";
elseif ($get_type == 2)
$viewty = "Файлам (в описании)";
elseif (empty($get_type))
$viewty = "Названию";

echo "<tr><td colspan=\"4\" class=\"b\" align=\"center\">
<form method=\"get\" action=\"license.php\">
<b>Быстрый поиск</b>: 
<input type=\"text\" id=\"searchinput\" name=\"n\" size=\"64\" class=\"searchgif\" value=\"".htmlspecialchars($searchstr)."\" />

<select name=\"t\">
<option value=\"0\">Названию</option>
<option value=\"1\" ".($get_type == 1 ? " selected" : "").">SHA-1 (Хеш-сумма)</option>
<option value=\"2\" ".($get_type == 2 ? " selected" : "").">Файлам (в описании)</option>
</select>

<input class=\"btn\" type=\"submit\"  style=\"width: 100px\" value=\"Найти\" />
</form>
</td></tr>";

if (empty($count) && empty($where))
echo ("<tr><td colspan=\"4\" class=\"b\" align=\"center\">нет лицензионных файлов</td></tr>");
elseif (!empty($where) && empty($count))
echo ("<tr><td colspan=\"4\" class=\"b\" align=\"center\">Поиск <b>".htmlspecialchars($searchstr)."</b> по <b>".$viewty."</b> провален. <br> Попробуйте изменить тип поиска, по <b>Названию</b>, по <b>SHA-1 (Хеш-сумма)</b>, по <b>Файлам (в описании)</b>.</td></tr>");
elseif (!empty($where) && !empty($count))
echo ("<tr><td colspan=\"4\" class=\"a\" align=\"center\">Поиск <b>".htmlspecialchars($searchstr)."</b> по <b>".$viewty."</b></td></tr>");



$res = sql_query("SELECT *, DATE_ADD(datetime, INTERVAL 31 DAY) AS futime FROM license $where ORDER BY id DESC $limit") or sqlerr(__FILE__,__LINE__);
$num=0;

while ($row = mysql_fetch_array($res)){

$info_hash = $row["info_hash"];

$desc = format_comment($row["desc"]);
if (strlen($desc)>=8000)
$desc = "Длина описания превышает лимит спойлера (более 8 тыс символов).";

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
//////////////////////////// авторские раздачи - запрет заливки
$sqlinfo_hash = $row["info_hash"];
$num_license = get_row_count("torrents","WHERE info_hash=".sqlesc($sqlinfo_hash));

if (!empty($num_license)) {

$tname = "<b>".$tname."</b><br> Найдено и удалено $num_license совпадений.";


$res = sql_query("SELECT id, name, image1 FROM torrents WHERE info_hash=".sqlesc($sqlinfo_hash)) or sqlerr(__FILE__,__LINE__);

while ($row1 = mysql_fetch_array($res)) {

$id_t = $row1["id"];

$tname = htmlspecialchars_uni($row1["name"]);

$reasonstr = "Защищен авторскими правами (license).";

write_log("Торрент $id_t ($tname) был удален системой. Причина: $reasonstr\n", "F25B61","torrent");

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
echo "<td width=\"20%\" align=center ".$td2.">".$desc."<br><small>Осталось: ".get_elapsed_time(sql_timestamp_to_unix_timestamp($row["futime"]))." до удаления.</small></td>";

echo "</tr>";


++$num;
}

echo "<tr><td colspan=\"4\" class=\"b\" align=\"center\"><b>Предупреждение</b>: Все истеченые по времени запрещенные файлы, удаляются из этой таблицы спустя месяц после добавления. <br>Если вы Правообладатель и нужно увеличить время активного действия (запрета файла), то можете воспользоваться <a href=\"support.php\">этой ссылкой</a> (<u>тема сообщения</u> -> <b>правообладатель (увеличить время запрета)</b>).</td></tr>";

  if ($count) {
    echo '<tr><td colspan="4">';
    echo $pagerbottom;
    echo '</td></tr>';
  }

  echo '</tr></table>';


stdfootchat();

?>
