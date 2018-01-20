<?
require_once("include/bittorrent.php");

dbconn(false);
loggedinorreturn();
parked();

$type = (int) $_GET["type"];

$res_cat = sql_query("SELECT name FROM categories WHERE id=".sqlesc($type)." LIMIT 1") or sqlerr(__FILE__, __LINE__);

$arr_cat = mysql_fetch_assoc($res_cat);
$cat_name = htmlspecialchars($arr_cat["name"]);

stdhead($tracker_lang['upload_torrent']." в категорию ".$cat_name);

if (get_user_class() < UC_UPLOADER AND ($CURUSER["class"] <>UC_VIP)){
  stdmsg("Ошибка прав", "Как минимум права аплодера у вас должны быть.");
  stdfoot();
  exit;
}

if (!isset($type) || empty($type)){
  stdmsg($tracker_lang['error'], "Не выбрана категория [через 5 секунд перебросит назад]");
  echo "<script>setTimeout('document.location.href=\"upload.php\"', 5000);</script>";
  stdfoot();
  exit;
}

if ($CURUSER["uploadpos"] == 'no'){
stdmsg("Извините", "Вам было запрещено закачивать торренты.");
stdfoot();
exit;
}

if (strlen($CURUSER['passkey']) <> 32) {
$CURUSER['passkey'] = md5($CURUSER['username'].get_date_time().$CURUSER['passhash']);
sql_query("UPDATE users SET passkey='$CURUSER[passkey]' WHERE id=$CURUSER[id]") or sqlerr(__FILE__, __LINE__);
}
?>

<form name="upload" enctype="multipart/form-data" action="takeupload.php" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$max_torrent_size?>" />
<table class="main" border="1" cellspacing="0" width="100%" cellpadding="5">
<tr><td class="colhead" colspan="2"><?=$tracker_lang['upload_torrent'] ;?> на сайт <?=$SITENAME?> в  категорию <?=$cat_name?>

<script type="text/javascript">
function changeText(text){
document.getElementById('descr').value = text;
}
</script>
</td></tr>
<?
if ($free_from_3GB==1){
print"<tr><td align=\"center\" width=\"99%\" colspan=\"2\" style=\"padding: 15px; font-weight: bold;\" class=\"row\">Если данная раздача будет весить больше или равно 3 гигабайтам, то она является свободной — учитывается только количество отданного, количество скачанного не учитывается a.k.a Золотой торрент.</td></tr>"; 
}

print"<tr><td align=\"center\" width=\"99%\" colspan=\"2\" style=\"padding: 15px;\"><b>Внимание</b>: Если вы скачали торрент файл с другого трекера, и решили залить такой же файл сюда, вы можете напрямую указать <b>тотже</b> торрент файл, который вы <b>уже скачали</b> (не обязательно создавать новый, с оригинального файла будут удалены лишь ваши идентификаторы - пасскеи, чтобы вы не испортили рейтинг на другом трекере).</td></tr>"; 


tr("Мульти-трекер <font color=red>*</font>", "<label><input type=\"checkbox\" ".(!empty($name_link)? "checked":"")." name=\"multitr\" value=\"1\"><i>Разрешить / Запретить подключение внешних сидов и пиров.</i> </label><br>Работает только с обновлением torrent файла (<b>вкладка ниже</b>)", 1);

//tr($tracker_lang['announce_url'], $announce_urls[0], 1);
tr($tracker_lang['torrent_file']." <font color=red>*</font>", "<input type=file name=tfile size=75>
<br>
Максимальный размер .torrent не должен превышать <b>".mksize($max_torrent_size)."</b><br>
Много пробелов в названии файла .torrent может привести к ошибке его скачивания\n", 1);

tr($tracker_lang['torrent_name'], "<input type=\"text\" name=\"name\" value=\"".$dtitle."\"size=\"73\" /><br />".$tracker_lang['taken_from_torrent']."\n", 1);

if (get_user_class() >= UC_MODERATOR){
tr("Вебсид", "<input type=\"text\" name=\"webseed\" size=\"73\" /><br />Если у раздачи, есть прямая  <b>http ссылка</b> укажите ее (русский не поддерживается).<br>
Если ссылка будет указана неправильно, ip адрес сервера забанится клиентом\n", 1);
}


tr("Картинка", "Максимальный размер изображения: <b>".mksize($maximagesize)."</b><br />".
$tracker_lang['avialable_formats'].": <b>gif, jpg, jpeg, png</b>&nbsp&nbsp<br>
<input type=file name=image0 size=75><br /><b>", 1);

tr("Скриншот №1", "<input name=picture1 size=80 \"><br />",1);
tr("Скриншот №2", "<input name=picture2 size=80 \"><br />",1);
tr("Скриншот №3", "<input name=picture3 size=80 \"><br />",1);
tr("Скриншот №4", "<input name=picture4 size=80 \"><br />",1);  


?>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("span.spoiler").hide();
    $('<a class="reveal"><input type=button value="Нажмите здесь, для показа дополнительного меню Скриншотов" style="height: 25px; width: 400px"></form></a>').insertBefore('.spoiler');
    $("a.reveal").click(function(){
    $(this).parents("p").children("span.spoiler").fadeIn(2500);
    $(this).parents("p").children("a.reveal").fadeOut(600);
    });
});
</script>
<?

tr("Скриншоты", "<p><span class=\"spoiler\">
<textarea name=array_picture cols=77 rows=3></textarea>
<span title=\"Кликните сюда для просмотра примера\" style=\"cursor: pointer;\" onclick=\"javascript: show_hide('ss1234321')\"><br><b>За пример</b>: Можно кинуть через ввод несколько ссылок на прямые изображения, тем самым, они автоматически преобразуются в данные (<b>max 4</b>) Скриншот №1, Скриншот №2 и тд</span>
<span id=\"sss1234321\" style=\"display: none;\">
<b>Пример ввода ссылок</b>: <br>
<textarea name=test cols=77 rows=4 readonly>
http://img40.imageshack.us/img40/3423/2dd421a60c145905380.jpg
http://img444.imageshack.us/img444/8184/53e6966b2c245905367.jpg
http://img641.imageshack.us/img641/7958/c4962441e5225905355.jpg
http://img153.imageshack.us/img153/5020/e9fa34b43b3f5905342.jpg
</textarea></span></span>
</span></p>",1);


// мод описания версия 2009 года
$video_ = "[b]Название[/b]: Русское название / Английское (Год выпуска) Качество\\n\\n[u]Информация о фильме[/u]\\n[b]Название: [/b]\\n[b]Оригинальное название: [/b]\\n[b]Год выхода: [/b]\\n [b]Жанр: [/b]\\n[b]Режиссер: [/b]\\n[b]В ролях: [/b]\\n\\n[b]О фильме: [/b]\\n[b]Выпущено: [/b]\\n\\n[b]Продолжительность: [/b]\\n[b]Озвучивание: [/b]\\n\\n[u]Файлы[/u]\\n[b]Формат: [/b]\\n[b]Качество: [/b]\\n[b]Видео: [/b]\\n[b]Аудио: [/b]"; 

$audio_ = "[b]Название[/b]: Исполнитель - Альбом (Год выпуска) Формат файла\\n\\n[u]Информация о музыке: [/u]\\n[b]Исполнитель: [/b]\\n[b]Название альбома: [/b]\\n[b]Год выпуска: [/b]\\n[b]Жанр: [/b]\\n\\n[b]Треклист: [/b]\\n[u]Файлы[/u]\\n[b]Время звучания: [/b]\\n[b]Формат: [/b]\\n[b]Качество: [/b]"; 

$game_ = "[b]В названии[/b]: Русское название / Английское (Год выпуска)\\n\\n[u]Информация об игре[/u]\\n[b]Разработчик: [/b]\\n[b]Издатель: [/b]\\n[b]Год выхода: [/b]\\n[b]Сайт игры: [/b] - По желанию !\\n[b]Жанр: [/b]\\n[b]Язык: [/b]\\n\\n[b]Об игре: [/b]\\n\\n[b]Особенности игры: [/b]\\n\\n[b]Доп.Информация: [/b] - По желанию !\\n\\n[b]Запуск: [/b]\\n\\n[b]Системные требования: [/b]\\n\\n"; 

$soft_ = "[b]Название[/b]: Название (Год выпуска) Язык \\n\\n[u]Информация о программе[/u]\\n[b]Название: [/b]\\n[b]Год выхода: [/b]\\n[b]Версия: [/b]\\n[b]Разработчик: [/b]\\n[b]Платформа: [/b]\\n[b]Таблетка: [/b]\\n[b]Язык: [/b]\\n\\n[b]О программе: [/b]\\n\\n[b]Доп. информация: [/b]\\n\\n[b]Системные требования: [/b] - По собственному желанию !\\n\\n"; 

$image_ = "[b]Название[/b]: Hазвание (Год выпуска)\\n\\n[b]Год выпуска: [/b]\\n[b]Формат: [/b]\\n[b]Размер: [/b]\\n[b]Количество: [/b]\\n\\n[b]Краткое описание: [/b]\\n\\n"; 

$clip_ = "[b]Название[/b]: Название (Год выпуска) Качество\\n\\n[u]Информация о клипах[/u]\\n[b]Исполнитель: [/b]\\n[b]Название альбома: [/b]\\n[b]Год выпуска: [/b]\\n[b]Жанр: [/b]\\n\\n[b]Треклист[/b] - Если  Длинный то прячем под споилер !\\n[b]Продолжительность: [/b]\\n\\n[u]Файлы[/u]\\n[b]Формат: [/b]\\n[b]Качество: [/b]\\n[b]Видео: [/b]\\n[b]Аудио: [/b]\\n\\n"; 

$books_ = "[b]В названии[/b]: Автор - Название (Год издания)\\n\\n[b]Автор: [/b]\\n[u]Название: [/u]\\n[b]Оригинальное название: [/b]\\n[b]Издательство: [/b]\\n[b]Год издания книги: [/b]\\n[b]Жанр: [/b]\\n[b]Язык: [/b]\\n\\n[b]Описание: [/b]\\n\\n[b]Формат: [/b]\\n[b]Количество Страниц: [/b]"; 

$serial_ = "[b]Название[/b]: Русское название / Английское (серия) (Год выпуска) Качество\\n\\n[u]Информация о сериале[/u]\\n[b]Название: [/b]\\n[b]Оригинальное название: [/b]\\n[b]Год выхода: [/b]\\n[b]Жанр: [/b]\\n[b]Режиссер: [/b]\\n[b]В ролях: [/b]\\n\\n[b]О фильме: [/b]\\n\\n[b]Выпущено: [/b]\\n[b]Продолжительность: [/b]\\n[b]Озвучивание: [/b]\\n\\n[u]Файлы[/u]\\n[b]Формат: [/b]\\n[b]Качество: [/b]\\n[b]Видео: [/b]\\n[b]Аудио: [/b]\\n\\n"; 

$w = 80;
$h = 20;

print("<tr><td class=rowhead style='padding: 3px'><center>Выберите ".$tracker_lang['description']."<br><b>"); 

print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$video_."\")' value=Видео>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$audio_."\")' value=Аудио>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$soft_."\")' value=Софт>\n");  
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$game_."\")' value=Игрушки>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$image_."\")' value=Картинки>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$clip_."\")' value=Клипы>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$books_."\")' value=Книги>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$serial_."\")' value=Сериалы>\n");
print("</center></td><td>"); 
// мод описания версия 2009 года 

textbbcode("upload","descr");
print("</td></tr>\n");

if(get_user_class() >= UC_MODERATOR) {
///// мод тегов /////
?>
    <style type="text/css" media="screen">
        code {font:99.9%/1.2 consolas,'courier new',monospace;}
        #from a {margin:2px 2px;font-weight:normal;}
        #tags {width:36em;}
         a.selected {background:#1843f9; color:#e6e6e6; border: 1px #D1D8EC solid;}
        .addition {margint-top:2em; text-align:right;}
    </style>
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/tagto.js"></script>

    <script type="text/javascript">
        (function($){
            $(document).ready(function(){
                $("#from").tagTo("#tags");
            });
        })(jQuery);
    </script>

<?
$s = '<input type="text" id="tags" name="tags">';
$s.= '<div id="from">';
$tags = taggenrelist($type);
if (!$tags)
$s .= "Нет тегов для данной категории. Вы можете добавить собственные, через запятую.";
else  {
foreach ($tags as $row)
$s .= "<a title=\"" . htmlspecialchars($row["name"]) . " клик\" href='#'>" . htmlspecialchars($row["name"]) . "</a>\n";
}
$s .= "</div>Выбирайте <b>теги из списка (кликом по слову)</b>, <b>не повторяйте значения</b> своими же!!!\n";

tr("Тэги", $s, 1);
///// мод тегов /////
}


/*
$s = "<select name=\"type\">\n<option value=\"0\">Выберите из списка</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
tr("Категория", $s, 1);
*/


if(get_user_class() >= UC_MODERATOR)
tr($tracker_lang['golden'], "<label><input type=checkbox name=free value=yes> ".$tracker_lang['golden_descr']."</label>", 1);

if (get_user_class() >= UC_ADMINISTRATOR)
tr("Важный", "<label><input type=\"checkbox\" name=\"sticky\" value=\"yes\">Прикрепить этот торрент (всегда наверху)</label>", 1);
    
//if (get_user_class() > UC_ADMINISTRATOR)
//tr("На главной", "<label><input type=\"checkbox\" name=\"ontop\" value=\"yes\">Разместить на главной</label>", 1);


?>

<script type="text/javascript">
function changeText(text){
document.getElementById('area').value = text;
}
</script>

<tr><td align="center" colspan="2"><input type="hidden" name="type" value="<?=$type?>"><input type="submit" class=btn style="height: 35px; width: 450px;" value="<?=$tracker_lang['upload'];?>" /></td></tr>
</table>
</form>
<? stdfoot(); ?>