<?
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();

if (!mkglobal("id"))
stderr("Ошибка данных", "Проверьте вводимые данные в id запросе.");

$id = (int) $id;

if (!is_valid_id($id) || empty($id))
stderr("Ошибка данных", "Нет торрент файла. Проверьте вводимые данные в id запросе.");



/////////////////
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

header("Content-Type: text/html; charset=" .$tracker_lang['language_charset']);

$id = (int)$_POST['id'];

if (empty($id))
die;

//$dt = sqlesc(time() - 300);
$url="/details.php?id=$id";
$url_e="/edit.php?id=$id";
$res_s = sql_query("SELECT DISTINCT uid, username, class FROM sessions WHERE uid<>-1 and time > ".sqlesc(get_date_time(gmtime() - 300))." and url='$url_e' ORDER BY time DESC") or sqlerr(__FILE__,__LINE__);
// or url LIKE '%$url_e%'
$lastid=0;
while ($ar_r = mysql_fetch_assoc($res_s)) {
$username = $ar_r['username'];
$id_use = $ar_r['uid'];
if ($title_who_s)
$title_who_s.=", ";
$title_who_s.= "<b><a href=\"userdetails.php?id=$id_use\">".get_user_class_color($ar_r["class"], $ar_r["username"]) . "</a></b>";
   	$lastid++;
}

if ($lastid<>0){
echo $title_who_s;
} else echo "Нет никого";

die;
}
/////////////////




$res = sql_query("SELECT *, b.class AS classname, b.username AS classusername FROM torrents AS t
LEFT JOIN users AS b ON t.moderatedby = b.id
WHERE t.id = $id") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
if (!$row)
stderr("Ошибка данных", "Нет торрент файла с таким идентификатором. Возможно удален или перемещен.");


stdhead("Редактирование торрент данных - \"" . $row["name"] . "\"");

if ((get_user_class() <= UC_UPLOADER) && $row["moderated"] == "yes")
stderr("Ошибка прав доступа", "Данный торрент был проверен администрацией и теперь запрещенно редактировать его.");


if ($CURUSER["catedit"] && get_user_class() == UC_MODERATOR && $CURUSER["id"] <> $row["owner"]) {
$cat=$row["category"];
$cat_user=$CURUSER["catedit"];

if (!empty($cat_user) && !stristr($cat_user, "[cat$cat]"))
stderr($tracker_lang['error'],"Вы не можете редактировать этот торрент, Не ваша категория редактирования."); 
}


if (!isset($CURUSER) || ($CURUSER["id"] <> $row["owner"] && get_user_class() < UC_MODERATOR)) {
stdmsg($tracker_lang['error'],"Вы не можете редактировать этот торрент.");
} else {



if ($row["stopped"] == "yes" && get_user_class()< UC_SYSOP) {
$subres = sql_query("SELECT id FROM snatched WHERE startdat<".sqlesc($row["stop_time"])." and torrent='$id' and userid=".sqlesc($CURUSER["id"])) or sqlerr(__FILE__,__LINE__);///completedat<>'0000-00-00 00:00:00'
$snatch = mysql_fetch_array($subres);
$snatched=$snatch["id"];
}

echo ("<form name=\"edit\" method=\"post\" action=\"takeedit.php\" enctype=\"multipart/form-data\">\n");
echo ("<input type=\"hidden\" name=\"id\" value=\"$id\">\n");

echo ("<table class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");
echo ("<tr><td class=\"colhead\" colspan=\"2\">Редактирование торрент данных</td></tr>");
 
/*
if ($lastid<>0){
print("<tr><td align=\"right\"><b>Редактируют это сейчас: $lastid</b></td><td align=\"left\">$title_who_s</td></tr>\n");
}
*/






 


/////////////////////

?>

<script>
function edit_online(id) {
jQuery.post("edit.php",{"id":id}, function(response) {
		jQuery("#edit_online").html(response);
	}, "html");

setTimeout("edit_online('<?=$id;?>');", 30000);
}
edit_online('<?=$id;?>');
</script>
<?

echo"<tr><td align=\"right\"><b>Редактируют это сейчас: </b></td><td align=\"left\"><span align=\"center\" id=\"edit_online\">Загрузка кто редактирует данный торрент</span></td></tr>";

/////////////////////
 
$edit_link = ($row["stopped"] == "yes" && empty($snatched) &&  get_user_class() < UC_SYSOP ? $row["name"]:"<a class=\"index\" href=\"download.php?id=$id\"><b><span title='Нажмите сюда чтобы скачать файл'>".$row["name"]."</span></b></a>");

tr("Название",$edit_link,1);
 

 
 
if (get_user_class() >= UC_MODERATOR){

echo '<script>
function adjective_ax() {
jQuery.post("block-details_ajax.php" , {adject:"yes",tid:"'.$id.'"}, function(response) {
jQuery("#adjective_ax").html(response);
}, "html");
setTimeout("adjective_ax();", 240666);
}
adjective_ax();
</script>';

tr("Похожие файлы", "<div id=\"adjective_ax\">Загрузка похожих файлов...</div>", 1);  
}

         	
tr("Мульти-трекер <font color=red>*</font>", "<label><input type=radio name=multitr value=yes" .($row["multitracker"] == "yes" ? " checked" : "").">Да </label><label><input type=radio name=multitr value=no" .($row["multitracker"] == "no" ? " checked" : "").">Нет </label><i>Разрешить / Запретить подключение внешних сидов и пиров (для обеспечения max скорости скачивания)</i> <br>Работает только с обновлением torrent файла (<b>вкладка ниже</b>)", 1);

tr($tracker_lang['torrent_file']." <font color=red>*</font>", "<input type=file name=tfile size=80><br>
Максимальный размер .torrent не должен превышать <b>".mksize($max_torrent_size)."</b>\n", 1);

tr("Приватный", "".($row["private"] == "yes" ? "Да <b>[</b>Отключены функции: DHT, Обмен пирами, Поиск локальных пиров<b>]</b>" : "Нет <b>[</b>Включены функции: DHT, Обмен пирами, Поиск локальных пиров<b>]</b>")."", 1);
	
tr($tracker_lang['torrent_name'], "<input type=\"text\" name=\"name\" value=\"" .htmlspecialchars($row["name"]). "\" size=\"80\" /> <i>символов max 255</i>", 1);

$image0_inet = strip_tags(preg_replace("/\[((\s|.)+?)\]/is", "", $row["image1"])); /// чистм от мусора [] и тд
$image0_inet = htmlentities($image0_inet); ///внешняя ссылка на постер

if (!preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image0_inet))
unset($image0_inet);

tr("Картинка", "
<table cellspacing=\"3\" cellpadding=\"0\" width=\"100%\" border=\"0\">

<tr>
<td align=\"center\" class=\"b\" colspan=2><label>
<input title=\"Данные о постере не изменятся. Все останется без изменений.\" type=\"radio\" name=\"img1action\" value=\"keep\" checked>Оставить без изменений &nbsp&nbsp</label>
<label><input title=\"Постер затерется полностью, торрент релиз останется без него.\" type=\"radio\" name=\"img1action\" value=\"delete\">Удалить постер / ссылку&nbsp&nbsp</label>
<label><input title=\"Обновить / Добавить постер или картинку - Обязательно выделите это меню если вы хотите добавить или изменить значение постера, иначе ничего не сохранится.\" type=\"radio\" name=\"img1action\" value=\"update\">Обновить / Добавить&nbsp&nbsp</label>
</td>
</tr>

<tr>
<td align=\"left\" class=\"a\"colspan=\"2\">Локальная загрузка (выбор и заливка с компьютера)
".(empty($image0_inet) && !empty($row["image1"]) ? "<b>активировано</b>":"")."
</td>
</tr>

<tr>
<td align=\"left\" class=\"b\">
<input type=\"file\" name=\"image0\" size=\"80\"></td>
</tr>

<tr>
<td align=\"left\" class=\"a\"colspan=2>Внешняя ссылка (выбор ссылки для постера с интернета)
".(!empty($image0_inet) && !empty($row["image1"]) ? "<b>активировано</b>":"")."
</td>
</tr>

<tr>
<td align=\"left\" class=\"b\"><input type=\"text\" maxLength=\"128\" size=\"80\" name=\"image0_inet\" value=\"".$image0_inet."\"></td>
</tr>

<tr>
<td align=\"center\" class=\"a\" colspan=2>Max размер постера (в локальной или прямой ссылки): <b>".mksize($maximagesize)."</b></td>
</tr>

<tr>
<td align=\"left\" class=\"b\" colspan=2>
<label><input type=checkbox name='truncacheimg' value=yes> Очистить кеш эскизов постера </label><i>(используется в browse,details,block's)</i>
".(empty($image0_inet) ? "<br>
<label><input type=checkbox name='imgimove' value=yes> Переместить постер с сервера на внешнюю прямую ссылку</label> 
(в стадии доработки!) ":"")."
</td>
</tr>

</table>", 1);

tr("Скриншот №1", "<input title=\"Внимание: Не принимаются изображения с таких хостингов как - sendpic.ru и funkyimg.com\" name=picture1 value=\"".htmlentities($row["picture1"])."\" size=80\">",1);
tr("Скриншот №2", "<input title=\"Внимание: Не принимаются изображения с таких хостингов как - sendpic.ru и funkyimg.com\" name=picture2 value=\"".htmlentities($row["picture2"])."\" size=80\">",1);
tr("Скриншот №3", "<input title=\"Внимание: Не принимаются изображения с таких хостингов как - sendpic.ru и funkyimg.com\" name=picture3 value=\"".htmlentities($row["picture3"])."\" size=80\">",1);
tr("Скриншот №4", "<input title=\"Внимание: Не принимаются изображения с таких хостингов как - sendpic.ru и funkyimg.com\" name=picture4 value=\"".htmlentities($row["picture4"])."\" size=80\">",1);  


?>

<script type="text/javascript">
$(document).ready(function() {
    $("span.spoiler").hide();
    $('<a class="reveal"><input type=button class=btn value="Нажмите здесь, для показа дополнительного меню Скриншотов" style="height: 25px; width: 400px"></form></a>').insertBefore('.spoiler');
    $("a.reveal").click(function(){
    $(this).parents("p").children("span.spoiler").fadeIn(2500);
    $(this).parents("p").children("a.reveal").fadeOut(600);
    });
});
</script>
<?


tr("Скриншоты", "<p><span class=\"spoiler\">
<textarea name=array_picture cols=77 rows=3></textarea>
<span title=\"Кликните сюда для просмотра примера\" style=\"cursor: pointer;\" onclick=\"javascript: show_hide('ss1234321')\"><br><b>Внимание</b>: Разрешаются теги [img] и другие [теги внутри] фильтруются. <br><b>За пример</b>: Можно кинуть через ввод несколько ссылок на прямые изображения, тем самым, они автоматически преобразуются в данные (<b>max 4</b>) Скриншот №1, Скриншот №2 и тд</span>
<span id=\"sss1234321\" style=\"display: none;\">
<b>Пример ввода ссылок</b>: <br>
<textarea name=test cols=77 rows=4 readonly>
http://img40.imageshack.us/img40/3423/2dd421a60c145905380.jpg
http://img641.imageshack.us/img641/7958/c4962441e5225905355.jpg
[img]http://img444.imageshack.us/img444/8184/53e6966b2c245905367.jpg[/img]
[img]http://img153.imageshack.us/img153/5020/e9fa34b43b3f5905342.jpg[/img]
</textarea></span></span>
</span></p>",1);  



if (get_user_class() >= UC_MODERATOR)
$view_size="<br>[".strlen($row["descr"])."]";

if (!empty($row["descr"]))
print("<tr><td align=\"right\" valign=\"top\"><b>".$tracker_lang['description']."".$view_size."</b></td><td>");

textbbcode("edit","descr",htmlspecialchars($row["descr"]));

print("</td></tr>\n");

	$s = "<select name=\"type\">\n";
	$cats = genrelist();
	foreach ($cats as $subrow) {
		$s .= "<option value=\"" . $subrow["id"] . "\"";
		if ($subrow["id"] == $row["category"])
			$s .= " selected=\"selected\"";
		$s .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
	}

	$s .= "</select>\n";
	
tr("Категория", $s, 1);
	
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
    <script type="text/javascript" src="js/tagto.js"></script>

    <script type="text/javascript">
        (function($){
            $(document).ready(function(){
                $("#from").tagTo("#tags");
            });
        })(jQuery);
    </script>

<?
if (!$row["tags"]){
$ta = "Нет тегов для данной категории. Вы можете добавить собственные, через запятую.";}

$s = '<input type="hidden" name="oldtags" value="' . tolower($row["tags"]) . '"><input type="text" id="tags" name="tags" value="'.tolower($row["tags"]).'"><br>'.$ta.'';
$s .= '<div id="from">';
$tags = taggenrelist($row["category"]);
foreach ($tags as $tag)
$s .= "<a ".(stristr($row["descr"],$tag["name"]) ? "style=\"text-decoration:underline;\"":"")." title=\"" . htmlspecialchars(tolower($tag["name"]))." клик\" href='#'>" . htmlspecialchars(tolower($tag["name"])) . "</a>\n";
	
//$s .= "<a title=\"" . htmlspecialchars(tolower($tag["name"]))." клик\" href='#'>" . htmlspecialchars(tolower($tag["name"])) . "</a>\n";


$s .= "</div>Выбирайте <b>теги из списка (кликом по слову)</b>, <b>не повторяйте значения</b> своими же!!!\n";
tr("Тэги", $s, 1);
///// мод тегов /////


}

//	if ($row["banned"] == "no")
tr("Видимый", "
<input type=radio name=visible value=yes" .($row["visible"] == "yes" ? " checked" : "").">Да <input type=radio name=visible value=no" .($row["visible"] == "no" ? " checked" : "").">Нет 
<b>Видимый на главной</b><br /><i>Обратите внимание, что торрент автоматически станет видимым когда появиться раздающий и автоматически перестанет быть видимым (станет мертвяком) когда не будет раздающего некоторое время</i>.", 1);
	
if(get_user_class() >= UC_ADMINISTRATOR){
   	///strtotime(get_date_time()) - * 86400)
   	////display_date_time()
   	 //   list($viponly, $day) = explode(':', $row["viponly"]);
   	
   	    if (!empty($row["viponly"])) {
   	    $day_today=display_date_time($row["viponly"]);
   	    $day_selec_2=" <b>Истекает: ".$day_today."</b>";
        }
   	    else
   	    {
   	    $day_selec = "<select name=day_s><option value=\"0\">Сколько дней</option>\n";
        $i = "1";
        while($i <= 31) {
                $day_selec .= "<option value=" .$i. ">".$i."</option>\n";
                ++$i;
        }
        $day_selec.="</select>\n";
   	    }

tr("Только для VIP", "<label><input type=radio name=vip_only value=yes" .(!empty($row["viponly"]) ? " checked" : "").">Да </label><label><input type=radio name=vip_only value=no" .(empty($row["viponly"]) ? " checked" : "").">Нет </label> ".$day_selec."  
<br /><i>Специальная раздача для vip аккаунтов, окончание действия раздачи определяется в днях, по истечению снимается значение - Только для Vip Скачивания.</i>".$day_selec_2, 1);
}
	
	
	$banned_view=htmlspecialchars($row["banned_reason"]);
	if(get_user_class() >= UC_ADMINISTRATOR)
	tr("Забанен", "<label><input type=radio name=banned value=yes" .($row["banned"] == "yes" ? " checked" : "").">Да </label><label><input type=radio name=banned value=no" .($row["banned"] == "no" ? " checked" : "").">Нет </label><i>Торрент остановит свою деятельность, скачивание будет невозможным</i>.<br>		
    <input type=\"text\" size=\"73\" name=\"banned_reason\" ".($row["banned"] == "yes" && !empty($row["banned_reason"]) ? "value=\"$banned_view\"":"")."> <i>Причина бана</i>", 1);
    
	if(get_user_class() >= UC_MODERATOR)
    tr("Золотая раздача", "<label><input type=\"checkbox\" name=\"free\"" . (($row["free"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" /><b>Золотая раздача</b></label> <i>(считаеться только раздача, скачка не учитываеться)</i>", 1);
    
    if(get_user_class() >= UC_ADMINISTRATOR)
        tr("Важный", "<label><input type=radio name=sticky value=yes" .($row["sticky"] == "yes" ? " checked" : "").">Да </label><label><input type=radio name=sticky value=no" .($row["sticky"] == "no" ? " checked" : "").">Нет </label><i>Прикрепить этот торрент (всегда наверху)</i>", 1);
     
if(get_user_class() >= UC_ADMINISTRATOR)
tr("Приостановить раздачу", "<label><input type=radio name=stopped value=yes" .($row["stopped"] == "yes" ? " checked" : "").">Да </label><label><input type=radio name=stopped value=no" .($row["stopped"] == "no" ? " checked" : "").">Нет  </label>
".($row["stop_time"]<>"0000-00-00 00:00:00" && $row["stopped"] == "yes" ? "<b>Остановленна в ".$row["stop_time"]."</b> ".(get_user_class() == UC_SYSOP ? "<b>[</b><span style=\"color:blue;font-weight: bold;\">искл для боссов</span><b>]</b>":"")."<br>":"")."
<i>Раздача будет живой, докачают или помогут в сидировании только те, кто скачал ранее (".$row["stop_time"]." указанной даты) торрент файл, иначе взять или скачать файл торрент не получится, будет отказанно в доступе.</i>", 1);
	/* if(get_user_class() > UC_ADMINISTRATOR)   
        tr("Рекомендуемый", "<input type=\"checkbox\" name=\"ontop\"" . (($row["ontop"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"yes\" /> Выставить торрент на главной (в блок)", 1); 
     */   
    
if (get_user_class() >= UC_MODERATOR){
$webs=strip_tags($row["webseed"]);
tr("Вебсид", "<input type=\"text\" name=\"webseed\" size=\"73\" value=\"$webs\"/><br />Если у раздачи, есть прямая  <b>http ссылка</b> укажите ее (русский не поддерживается).<br>
Если ссылка будет указана неправильно, ip адрес сервера с ссылкой забанится клиентом.\n", 1);
}
     
	 if(get_user_class() >= UC_MODERATOR)
        tr("Новая дата", "<label><input type=\"checkbox\" name=\"up_date\" value=\"yes\"><b>Обновить дату размещения релиза на сегодняшнюю</b></label><br><i>Обновляйте дату только при необходимости! К примеру, вы существенно изменили релиз (заменили торрент-файл, добавили новые серии и т.п.). При обновлении даты релиз будет поднят вверх страницы всех релизов</i>.", 1);  

	 if(get_user_class() >= UC_ADMINISTRATOR)
print("<tr><td align=\"right\"  valign=\"top\" class=\"heading\">Анонимный релиз</td><td colspan=\"2\" align=\"left\"><label><input type=checkbox name=user_reliases_anonim value=1><i>Торрент станет без автора (без релизера)</i></label></td></tr>\n");

	  if(get_user_class() >= UC_ADMINISTRATOR)
print("<tr><td align=\"right\" valign=\"top\" class=\"heading\">Присвоить релиз</td><td colspan=\"2\" align=\"left\">Введите идентификатор (<b>id</b>) пользователя: <input type=\"text\" size=\"8\" name=\"release_set_id\"></tr></td>");

	 if(get_user_class() >= UC_ADMINISTRATOR)
        tr("Удалить комментарии", "<label><input type=\"checkbox\" name=\"delete_comment\" value=\"yes\"> <i>Все комментарии данного торрента будут удалены.</i></label><br>", 1);  
	
	 if(get_user_class() >= UC_MODERATOR)
        tr("Пересчитать файлы", "<label><input type=\"checkbox\" name=\"checks_files\" value=\"yes\"> <i>Перерасчет данных из .torrent файла (обновится и количество файлов)</i></label><br>", 1);  
		
	 if(get_user_class() >= UC_MODERATOR)
	    tr("Заблокировать комментарии", "<label><input type=radio name=lock_comments value=yes" .($row["comment_lock"] == "yes" ? " checked" : "").">Да </label><label><input type=radio name=lock_comments value=no" .($row["comment_lock"] == "no" ? " checked" : "").">Нет </label> <i>Запретить написание комментариев к торренту</i>", 1);


if (get_user_class() >= UC_MODERATOR){

$torrent_com = htmlspecialchars($row["torrent_com"]);

	print("<tr><td align=\"right\"  valign=\"top\" class=\"heading\">История торрента <br>[".strlen($torrent_com)."]</td><td colspan=2 align=left><textarea cols=75 rows=6".(get_user_class() < UC_SYSOP ? " readonly" : " name=torrent_com").">$torrent_com</textarea></td></tr>\n");

	print("<tr><td align=\"right\"  valign=\"top\" class=\"heading\">Добавить заметку</td><td colspan=2 align=left><textarea cols=75 rows=3 name=torrent_com_zam></textarea></td></tr>\n");
}


 if(get_user_class() >= UC_ADMINISTRATOR){
 	
 	if ($row["moderated"]=="no") {
    tr("Одобрить", "<label><input type=\"checkbox\" name=\"moded\" value=\"yes\"> проверка одобрения будет поставлена или</label> <a title=\"Второй вариант снятия одобрения без сохранения данных на этой странице\" href=\"check.php?id=$id\"><b>[<font color=\"red\">Одобрить</font>]</b></a><br>", 1); 
     } else
	tr("Снять одобрение", "<label><input type=\"checkbox\" name=\"moded\" value=\"no\"> проверка одобрения будет снята </label><b>".($row["classusername"] ? "<a href=\"userdetails.php?id=$row[moderatedby]\">".get_user_class_color($row["classname"], $row["classusername"])."</a>" : "id [$row[moderatedby]]")."</b> в <b>$row[moderatordate]</b> или <a title=\"Второй вариант снятия одобрения без сохранения данных на этой странице\" href=\"checkdelete.php?id=$id\"<b>[<font color=\"red\">Удалить</font>]</b></a><br>", 1); 
		}

/*
if(get_user_class() >= UC_MODERATOR){
$dt_multi = get_date_time(gmtime() - 86400); // день

if ($row["multi_time"]<$dt_multi && $row["multitracker"]=="no"){
global $announce_urls;
require_once(ROOT_PATH.'include/benc.php');

    $tracker_cache = array(); 
    $f_leechers = 0; 
    $f_seeders = 0; 

    foreach($announce_urls as $announce) 
    {
        $response = get_remote_peers($announce, $row['info_hash'],true); 
        if($response['state']=='ok'){
        $tracker_cache[] = $response['tracker'].':'.($response['leechers'] ? $response['leechers'] : 0).':'.($response['seeders'] ? $response['seeders'] : 0).':'.($response['downloaded'] ? $response['downloaded'] : 0); 
            // $f_leechers += $response['leechers']; 
            // $f_seeders += $response['seeders']; 
            if ($f_leechers < $response['leechers'])
            $f_leechers = $response['leechers'];
            
            if ($f_seeders < $response['seeders'])
            $f_seeders = $response['seeders']; 
        }
        else 
            $tracker_cache[] = $response['tracker'].':'.$response['state'];
    }

    $fpeers = $f_seeders + $f_leechers;
    $tracker_cache = implode("\n",$tracker_cache);
    $updatef = array();
    $updatef[] = "f_trackers = ".sqlesc($tracker_cache);
    $updatef[] = "f_leechers = ".sqlesc($f_leechers);
    $updatef[] = "f_seeders = ".sqlesc($f_seeders);
    $updatef[] = "multi_time = ".sqlesc(get_date_time());
    $updatef[] = "visible = ".sqlesc(!empty($fpeers) ? 'yes':'no');
    sql_query("UPDATE torrents SET " . implode(",", $updatef) . " WHERE id = $id");
    //implode(",", $updatef)
    
   $row["f_seeders"]=$f_seeders; $row["f_leechers"]=$f_leechers;

}

$veria=number_format($row["f_leechers"]+$row["f_seeders"]-$row["leechers"]-$row["seeders"]);
if ($row["multitracker"]=="no" && $veria>2){

tr("Прирост пиров ".$tracker_lang['multitracker'], "<b><font color=\"".linkcolor($row["f_seeders"])."\">".$row["f_seeders"]."</font></b> ".$tracker_lang['seeders_l'].", <b><font color=\"".linkcolor($row["f_leechers"])."\">".$row["f_leechers"]."</font></b> ".$tracker_lang['leechers_l']." = <b>" . ($row["f_seeders"] + $row["f_leechers"]) . "</b> ".$tracker_lang['peers_l']." <br><i>Возможно этот торрент будет иметь ".($row["f_seeders"] + $row["f_leechers"])." пир(ов) в мультитрекерной раздаче.</i><br>
<label><input type=\"checkbox\" name=\"multivalue\" value=\"yes\"> Переключить тип раздачи на мультитрекер. </label><i>(прирост ".$veria." пиров)</i>", 1);
}

*/

 	if ($row["multitracker"]=="yes" && $row["moderatedby"]=="92") {
    tr("Переодобрить", "<label><input type=\"checkbox\" name=\"recheck\" value=\"yes\"> Одобрение торрента будет переписано на вас </label>(<b>При условии</b>: релизер <font color=\"red\"><b>ViKa</b></font> + <font color=\"blue\"><b>Мультитрекер</b></font>)", 1); 
//}

}

		
  tr("Удаление", "<label><input type=\"checkbox\" name=\"reacon\" value=\"1\"/>
  &nbsp;<b>Удалить раздачу по причине</b>:</label><br>
  <label><input name=\"reasontype\" type=\"radio\" value=\"1\">&nbsp;<b>Мертвяк:</b> 0 пиров, 0 сидов = 0 соединений  <i>(нет качающих)</i></label><br>
  <input name=\"reasontype\" type=\"radio\" value=\"2\">&nbsp;<b>Дупликат</b>: <input type=\"text\" size=\"40\" name=\"dup\">&nbsp;<i>указать id похожего релиза</i><br>
<input name=\"reasontype\" type=\"radio\" value=\"3\">&nbsp;<b>Правила</b>: <input type=\"text\" size=\"40\" name=\"rule\">&nbsp;<i>вписать нарушение</i><br>
<input name=\"reasontype\" type=\"radio\" value=\"4\">&nbsp;<b>Лицензионный</b>: &nbsp;<i>файл защищен авторскими правами</i>
", 1);


	print("<tr><td class=\"b\" colspan=\"2\" align=\"center\">
	<input type=\"submit\" class=\"btn\" value=\"Отредактровать\" style=\"height: 25px; width: 120px\">
	<input type=reset class=\"btn\" value=\"Обратить изменения\" style=\"height: 25px; width: 120px\"></form>
	<form method=\"post\" action=\"details.php?id=$id\"><input type=\"submit\" class=\"btn\" value=\"Вернуться к показу\" style='height: 25px; width: 120px'></form>
	</td></tr>\n");

	print("</table>\n");

}

stdfoot();
?>