<?
require_once("include/bittorrent.php");

dbconn(false);
loggedinorreturn();
parked();

stdhead($tracker_lang['upload_torrent']);

if (get_user_class() < UC_UPLOADER AND ($CURUSER["class"] !=UC_VIP))
{
  stdmsg($tracker_lang['error'], $tracker_lang['access_denied']);
  stdfoot();
  exit;
}


$h = date('i'); // проверяем минуты
if (($h >= 10 )&&($h <= 30) && get_user_class() >= UC_MODERATOR) // расписание минут
{
if ($free_from_3GB==1)
sql_query("UPDATE torrents SET free='yes' WHERE size>='3221225472'") or sqlerr(__FILE__,__LINE__);

sql_query("UPDATE torrents SET visible='no' WHERE visible='yes' AND multitracker='yes' AND (f_seeders+f_leechers)='0'") or sqlerr(__FILE__,__LINE__);

sql_query("UPDATE torrents SET visible='yes' WHERE visible='no' AND multitracker='yes' AND (f_seeders+f_leechers)>='1'") or sqlerr(__FILE__,__LINE__);

sql_query("UPDATE torrents SET f_seeders='0',f_leechers='0' WHERE multitracker='no'") or sqlerr(__FILE__,__LINE__);

// очистка тегов если пустое значение или 0
sql_query("DELETE FROM tags WHERE name=''") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM tags WHERE howmuch=0") or sqlerr(__FILE__,__LINE__);
//

}

//$id_u=$CURUSER["id"];
$res = sql_query("SELECT id, name, owner,sticky, seeders, leechers FROM torrents WHERE owner=".sqlesc($CURUSER["id"])." and (leechers / seeders >= 4) AND multitracker='no' ORDER BY leechers LIMIT 500") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0) {

echo "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\"><tr>
<h2>Пожалуйста просидируйте свои торрент файлы</h2><tr>
<td align=\"center\" class=\"b\">";

while ($arr = mysql_fetch_assoc($res)) {
				
echo "".($arr["sticky"] == "yes" ? "<b>Важный</b>: " : "")."
		
		<b><a href=\"details.php?id=".$arr['id']."\" alt=\"".$arr['name']."\" title=\"".$arr['name']."\">".format_comment($arr['name'])."</a></b>
		<b>[</b>Раздают: <b><font color=\"green\">".number_format($arr['seeders'])."</font></b> 
		Качают: <b><font color=\"red\">".number_format($arr['leechers'])."</font></b><b>]</b>
		<br />\n";
}

echo "</font></b></td></tr></tr></table><br>";
}

$chislo=mysql_num_rows($res);

if ($CURUSER["unseed"]<>$chislo)
sql_query("UPDATE users SET unseed = ".sqlesc($chislo)." WHERE id = ".sqlesc($CURUSER["id"])) or sqlerr(__FILE__,__LINE__);


begin_frame("Выберите категорию раздачи");
?>


<table class="main" border="1" align="center" cellspacing="0" cellpadding="5">
<tr><td align="center" colspan="2" style="border:0;"><b>Внимание</b>: Если вы скачали торрент файл с другого трекера, и решили залить такой же файл сюда, вы можете напрямую указать <b>тотже</b> торрент файл, который вы <b>уже скачали</b> (не обязательно создавать новый, с оригинального файла будут удалены лишь ваши идентификаторы - пасскеи, чтобы вы не испортили рейтинг на другом трекере).</td></tr>
</table>


<div align=center>
<form name="upload" action="uploadnext.php" method="get">
<table border="1" cellspacing="0" cellpadding="5">
<?

$s = "<select name=\"type\">\n<option value=\"0\">(".$tracker_lang['choose'].")</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
echo $s;

?>
<tr><td align="center" colspan="2" style="border:0;">

<input type="submit" class=btn value="Далее к оформлению релиза" /></td></tr>
</table>
</form>
<?
end_frame();
print("<br>");
stdfoot();

?>