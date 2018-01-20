<?
require_once("include/bittorrent.php");

dbconn(false);
loggedinorreturn();
parked();

function bark($msg) {
	genbark($msg, $tracker_lang['error']);
}
foreach(explode(":","descr:type:name") as $v) {
	if (isset($_POST[$v]))
{
$descr = unesc($_POST["descr"]);
if (!$descr)
	bark("Вы должны ввести описание!");

$catid = ((int) + $_POST["type"]);
if (!is_valid_id($catid))
	bark("Вы должны выбрать категорию, в которую поместить торрент!");

$shortfname = $torrent = $matches[1];
if (!empty($_POST["name"]))
	$torrent = unesc($_POST["name"]);

$torrent = htmlspecialchars(str_replace("_", " ", $torrent));

$ret = sql_query("INSERT INTO off_reqs 
(owner, name, size, numfiles, descr, category, added) VALUES 
(" . implode(",", array_map("sqlesc", array($CURUSER["id"], $torrent, $totallen, count($filelist), $descr, (int)+$_POST["type"]))) . ", '" . get_date_time() . "')");
if (!$ret) {
	if (mysql_errno() == 1062)
		bark("Такой Запрос | Предложение уже есть на трекере!");
	bark("mysql puked: ".mysql_error());
}
$id = mysql_insert_id();
sql_query("INSERT INTO checkcomm (checkid, userid, torrent) VALUES ($id, $CURUSER[id], 1)") or sqlerr(__FILE__,__LINE__);


header("Location: $DEFAULTBASEURL/detailsoff.php?id=$id");
die;

}
}
stdhead("Загрузка Запроса | Предложения");


if ($CURUSER["class"] <UC_USER)
{
  stdmsg($tracker_lang['error'], $tracker_lang['access_denied']);
  stdfoot();
  exit;
}


?>

<form name="upload" enctype="multipart/form-data" action="uploadoff.php" method="post">

<table border="1" cellspacing="0" cellpadding="5">
<tr><td class="colhead" colspan="2"><a class="altlink_white" href=uploadoff.php>Загрузка Запроса | Предложения на сайт <?=$SITENAME?></a> :: <a class="altlink_white" href=detailsoff.php>Список запросов</a>
<script type="text/javascript">
function changeText(text){
document.getElementById('descr').value = text;
}
</script>
</td></tr>
<?


tr($tracker_lang['torrent_name'], "<input type=\"text\" name=\"name\" size=\"80\" /><br />\n", 1);


// description_mod 
$video_ = "[u]Информация о фильме[/u]\\n[b]Название: [/b]\\n[b]Оригинальное название: [/b]\\n[b]Год выхода: [/b]\\n[b]Жанр: [/b]\\n[b]Режиссер: [/b]\\n[b]В ролях: [/b]\\n\\n[b]О фильме: [/b]\\n\\n[b]Выпущено: [/b]\\n[b]Продолжительность: [/b]\\n[b]Перевод: [/b]\\n[b]Субтитры: [/b]\\n[b]Дополнительно: [/b]\\n\\n[u]Файл[/u]\\n[b]Формат: [/b]\\n[b]Качество: [/b]\\n[b]Видео: [/b]\\n[b]Звук: [/b]\\n[b]Размер: [/b]\\n"; 

$audio_ = "[b]Исполнитель: [/b]\\n[b]Альбом: [/b]\\n[b]Год выхода: [/b]\\n[b]Жанр: [/b]\\n\\n[b][u]Треклист:[/u][/b]\\n\\n\\n[b]Звук: [/b](битрейт, формат)\\n[b]Продолжительность: [/b]\\n"; 

$game_ = "[b]Название: [/b]\\n[b]Год выхода: [/b]\\n[b]Жанр: [/b]\\n[b]Выпущено: [/b]\\n[b]Язык: [/b]\\n\\n[b]Об игре: [/b]\\n\\n\\n[b]Особенности игры: [/b]\\n\\n\\n[b]Системные требования: [/b]\\n\\n"; 

$soft_ = "[b]Название:[/b]\\n[b]Год выпуска:[/b]\\n[b]Платформа:[/b]\\n[b]Язык интерфейса:[/b]\\n[b]Лекарство:[/b]\\n[b]Описание:[/b]\\n[b]Доп. информация:[/b]\\n"; 

$w = 80; 
$h = 20; 

 print("<tr><td class=rowhead style='padding: 3px'><center>Выберите ".$tracker_lang['description']."<br><b>"); 

print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$video_."\")' value=Видео>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$audio_."\")' value=Аудио>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$soft_."\")' value=Софт>\n");  
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$game_."\")' value=Игрушки>\n"); 

print("</center></td><td>"); 
// end description_mod  


textbbcode("upload","descr");
print("</td></tr>\n");

$s = "<select name=\"type\">\n<option value=\"0\">(".$tracker_lang['choose'].")</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
tr($tracker_lang['type'], $s, 1);

?>

<script type="text/javascript">
function changeText(text){
document.getElementById('area').value = text;
}
</script>

<tr><td align="center" colspan="2"><input type="submit" class=btn value="<?=$tracker_lang['upload'];?>" /></td></tr>
</table>
</form>
<?

stdfoot();

?>