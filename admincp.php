<?
require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

if (get_user_class() < UC_SYSOP || get_user_class() <> UC_SYSOP){
attacks_log('admincp'); 
stderr("Ошибочка", "Дальше еще интереснее"); 
die();
}


accessadministration();

stdhead("Панель администратора");


$admin_file = "admincp";


////////////// core
$op = (!isset($_REQUEST['op'])) ? "Main" : htmlentities($_REQUEST['op']);

if (get_magic_quotes_gpc()) {
	if (!empty($_GET))    { $_GET    = strip_magic_quotes($_GET);    }
	if (!empty($_POST))   { $_POST   = strip_magic_quotes($_POST);   }
	if (!empty($_COOKIE)) { $_COOKIE = strip_magic_quotes($_COOKIE); }
}


foreach ($_GET as $key => $value)
	$GLOBALS[$key] = $value;
foreach ($_POST as $key => $value)
	$GLOBALS[$key] = $value;

//foreach ($_COOKIE as $key => $value)
//	$GLOBALS[$key] = $value;
	

//////////////

////////// функшион
function end_chmod($dir, $chm) {
	if (file_exists($dir) && intval($chm)) {
		#chmod($dir, "0".$chm."");
		$pdir = decoct(fileperms($dir));
		$per = substr($pdir, 3);
		if ($per != $chm) return "".$dir." не имеет нужных разрешений для записи на сервере.<br />Установите нужные атрибуты CHMOD - ".$chm."";
	}
}
/////////////////////




function BuildMenu($url, $title, $image = '') {
	global $counter;
	$image_link = "/pic/admin/$image";
	echo "<td align=\"center\" class=\"b\" valign=\"top\" width=\"15%\" style=\"border: none;\"><a href=\"$url\" title=\"$title\">".($image != '' ? "<img src=\"$image_link\" border=\"0\" alt=\"$title\" title=\"$title\">" : "")."<br><b>$title</b></a></td>";
	if ($counter == 5) {
		echo "</tr><tr>";
		$counter = 0;
	} else {
		++$counter;
	}
}

switch ($op) {

case "Main":
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\"><tr><td class=\"a\" colspan=\"6\">Панель Боссов</td></tr>";

BuildMenu("".$admin_file.".php?op=addtheme", "Темы", "theme.png");
BuildMenu("dump3r.php", "Бэкап БД", "db_up.png");
BuildMenu("".$admin_file.".php?op=BlocksAdmin", "Блоки и баннеры", "block.png");
BuildMenu("".$admin_file.".php?op=StatusDB", "База данных", "db.png");
BuildMenu("docleanup.php", "Выполнить клинап", "db_process.png");
BuildMenu("".$admin_file.".php?op=iUsers", "Смена пароля", "password.png");

echo "<tr><td align=\"center\" class=\"a\" width=\"100%\" colspan=\"6\">&nbsp;</td></tr>";
echo "</table>";

echo "<br>";




$df = disk_free_space(ROOT_PATH);
$dt = disk_total_space(ROOT_PATH);


echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\"><tr><td align=\"center\" colspan=\"2\" class=\"b\">Информация о сервере</td></tr>";

echo "<tr>
<td class=\"a\">Оставшееся место / Занято (на VDS|VPS данные неверны)</td><td align=\"right\" class=\"b\">".mksize($df)." / ".mksize($dt)."</td></tr>
<tr>
<td class=\"b\">Версия PHP</td><td align=\"right\" class=\"a\">".phpversion()."</td></tr>
<tr>
<td class=\"a\">Версия MYSQL</td><td align=\"right\" class=\"b\">".mysql_get_client_info()."</td></tr>
<tr><td colspan=\"2\" class=\"b\">".mysql_stat()."</td></tr>";
echo "</tr></table><br>";



$content.= "<br><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\"><tr><td class=\"a\" colspan=\"6\">Остальные функции</td></tr>";
$content.= "<td align=\"center\" class=\"b\" width=\"100%\" style=\"border: none;\">";


$expire = 24*60*60; // 60 минут на кеш, после обновление 

////////// хаки по xss
$cachesql = "cache/hacklog.txt";
if (!file_exists($cachesql)){
$view_sql="<a href=\"hacklog.php\">XSS</a>";
}
elseif ((time() - $expire) <= filemtime($cachesql) && filesize($cachesql)<>0){
$view_sql="<a href=\"hacklog.php\"><span style=\"color: red;\" title=\"Новые данные (менее дня назад)\">XSS</span></a>";
}
else {
$view_sql="<a href=\"hacklog.php\"><span style=\"color: green;\" title=\"Существует файл (в течении дня не был обновлен или равен нулю)\">XSS</span></a>";
}
////////// хаки по xss

////////// функция доступа по info
$cacheinfo = "cache/info_cache_stat.txt";
if (!file_exists($cacheinfo)){
$view_info="<a href=\"info_cache.php\">Info</a>";
}
elseif ((time() - $expire) <= filemtime($cacheinfo) && filesize($cacheinfo)<>0){
$view_info="<a href=\"info_cache.php\"><span style=\"color: red;\" title=\"Новые данные (менее дня назад)\">Info</span></a>";
}
else {
$view_info="<a href=\"info_cache.php\"><span style=\"color: green;\" title=\"Существует файл (в течении дня не был обновлен или равен нулю)\">Info</span></a>";
}
////////// функция доступа по info


////////// ошибки mysql
$cacheerror = "cache/sqlerror.txt";
if (!file_exists($cacheerror)){
$view_error_sql="<a href=\"sqlerror.php\">SQL</a>";
}
elseif ((time() - $expire) <= filemtime($cacheerror) && filesize($cacheerror)<>0){
$view_error_sql="<a href=\"sqlerror.php\"><span style=\"color: red;\" title=\"Новые данные (менее дня назад)\">SQL</span></a>";
}
else {
$view_error_sql="<a href=\"sqlerror.php\"><span style=\"color: green;\" title=\"Существует файл (в течении дня не был обновлен или равен нулю)\">SQL</span></a>";
}
////////// ошибки mysql

////////// ошибки торрента
$cachebit = "cache/error_torrent.txt";
if (!file_exists($cachebit)){
$view_bit="<a href=\"error_torrent.php\">Bit</a>";
}
elseif ((time() - $expire) <= filemtime($cachebit) && filesize($cachebit)<>0){
$view_bit="<a href=\"error_torrent.php\"><span style=\"color: red;\" title=\"Новые данные (менее дня назад)\">Bit</span></a>";
}
else {
$view_bit="<a href=\"error_torrent.php\"><span style=\"color: green;\" title=\"Существует файл (в течении дня не был обновлен или равен нулю)\">Bit</span></a>";
}
////////// ошибки торрента

$content.= "<center>";
$content.= "<b><font color=\"#0000FF\">-=Босс=-</font><b> <br>";
$content.= "<a href=\"admincp.php\">Админка</a> | <a href=\"admincp.php?op=BlocksAdmin\">Блоки</a> | ";
$content.= "<a href=\"admincp.php?op=iUsers\">Паc</a> | <a href=\"changeusername.php\">Имя</a> | <a href=\"changeuserid.php\">Ид</a> | ";
$content.= "<a href=\"friensblockadd.php\">Друзья</a> | <a href=\"groups.php\">Группы</a>  | ";
$content.= "<a href=\"dump3r.php\">База Данных</a> | ";
$content.= "<a href=\"cheaters.php\">Читеры</a> | ";
$content.= "$view_sql:$view_info:$view_error_sql:$view_bit | ";
$content.= "<a href=\"tagscheck.php\">Двойные</a> | <a href=\"repair_tags.php\">Процед</a> | ";
$content.= "<a href=\"unco.php\">Неподтв.юзеры</a> | ";
$content.= "<a href=\"bans.php\">Бан IP</a> | <a href=\"bannedemails.php\">Почты</a> | ";
$content.= "<a href=\"category.php\">Категории</a> | ";

$content.= "<br><b><font color=\"green\">-=Администратор=-</font><b> <br>";
$content.= "<a href=\"0nline.php\">Онлайн</a> | <a href=\"useragent.php\">Агент</a> | ";
$content.= "<a href=\"adduser.php\">Добавить юзера</a> | ";
$content.= "<a href=\"msg.php\">Сообщения</a> | <a href=\"statistics.php\">Стата</a> | ";
$content.= "<a href=\"maxlogin.php\">Входы</a> | <a href=\"referer.php\">Рефералы</a> | ";
$content.= "<a href=\"adminbookmark.php\">Подозреваемые</a> | ";
$content.= "<a href=\"downcheck.php\">Cинхронизация</a> | ";
$content.= "<a href=\"viewreport.php\">Жалобы</a> | ";
$content.= "<a href=\"warned.php\">Предупр.</a> | <a href=\"hiderating.php\">Безгран.</a> | ";
$content.= "Массовое <a href=\"staffmess.php\">ЛС</a> | <a href=\"groupsmess.php\">Группе</a></a> | ";
$content.= "<a href=\"testip.php\">Проверка IP </a> | <a href=\"testport.php\">Порта</a> | ";
$content.= "<br><b><font color=\"red\">-=Модератор=-</font><b> <br> ";
$content.= "<a href=\"usersearch.php\">Поиск юзера</a> | ";
$content.= "<a href=\"staff.php?act=last\">Новые юзеры</a> | ";
$content.= "<a href=\"users.php\">Юзеры</a> | <a href=\"premod.php\">Премод</a> | ";
$content.= "<a href=\"antichiters.php\">Скорости</a> | <a href=\"seeders.php\">Кол-во</a> | ";
$content.= "<a href=\"smilies.php\">Смайлы</a> | <a href=\"tags.php\">Теги</a> | ";
$content.= "<a href=\"ipcheck.php\">Двойные IP</a> | <a href=\"emailcheck.php\">Мыла</a> | ";
$content.= "<a href=\"stats.php\">Стати-ка</a> | <a href=\"uploaders.php\">Аплодеры</a> | ";
$content.= "<a href=\"topten.php\">TOP10</a> | <a href=\"votes.php\">Оценки</a> | ";
$content.= "<a href=\"file_descr.php\">Описания</a> | ";

$content.= "</center>";
$content.="</td><tr><td align=\"center\" class=\"a\" width=\"100%\" colspan=\"6\">&nbsp;</td></tr></table>";
echo $content;
		
	break;



	case "addtheme":
{
	
function addtheme($name, $action, $id) {
	global $admin_file;

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		    @unlink(ROOT_PATH."cache/my_style.txt");
  
            if ($action == "add") {

            if (is_dir(ROOT_PATH."themes/".$name)){
            $name = sqlesc($name);
			sql_query("INSERT INTO stylesheets (`name`, `uri`) VALUES($name, $name)");
			$next_id = mysql_insert_id();
			if ($next_id)
            stdmsg("Успешно", "Тема успешно <b>добавлена</b>");
            else
            stdmsg("Успешно", "Тема <b>существует</b>, нельзя добавить такую же");
            }
            else
            stdmsg("Ошибка данных", "Тема успешно <u>недобавлена</u>");
            
            header("Refresh: 3; url=".$admin_file.".php?op=addtheme");
            }
            
            if ($action == "delete") {
            $id = sqlesc((int)$id);
            
            if (!empty($id)) {
            sql_query("DELETE FROM stylesheets WHERE id=$id") or sqlerr(__FILE__,__LINE__);
            stdmsg("Успешно", "Тема удалена успешно");
            header("Refresh: 3; url=".$admin_file.".php?op=addtheme");
            }
   
            }
    }
    else {

echo "<form method=\"post\" action=\"".$admin_file.".php?op=addtheme\">
<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">
<tr><td class=\"colhead\" colspan=\"2\">Добавить тему</td></tr>";

echo "<tr>
<td class=\"a\" width=20%>Введите Название</td>
<td class=\"a\"><input name=\"name\" type=\"text\" style=\"width:150\"> При добавление, папка темы проверяется на валидность!</td>
</tr>";

echo "<tr>
<td class=\"b\" colspan=\"2\" align=\"center\"><input type=\"submit\" class=\"btn\" name=\"isub\" value=\"Добавить новую тему\"></td></tr>
</table>
<input type=\"hidden\" name=\"op\" value=\"addtheme\" />
<input type=\"hidden\" name=\"action\" value=\"add\" />
</form>";

echo "<br>";

global $default_theme;

echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">";

echo "<tr><td class=\"colhead\" colspan=\"3\">Существующие темы</td></tr>";

echo "<tr>
<td class=colhead align=center>#</td>
<td class=colhead align=center>Название папки / Активность</td>
<td class=colhead align=center>Удалить</td>
</tr>";

$res = sql_query("SELECT id, name FROM stylesheets ORDER BY id") or sqlerr(__FILE__,__LINE__);

while ($row = mysql_fetch_array($res)){
	
$sql = sql_query("SELECT COUNT(*) FROM users WHERE stylesheet=".sqlesc($row["name"])) or sqlerr(__FILE__,__LINE__);
$r = mysql_fetch_array($sql);

echo "<tr>
<td align=center>".$row['id']."</td>
<td align=center><b>".$row['name']."</b> ( ".number_format($r[0])." пользователя (ей) )
".($default_theme==$row['name'] ? "по умолчанию в config'e":"")."
</td>
<td align=center><form method=\"post\" action=\"".$admin_file.".php?op=addtheme\"><input type=\"submit\" class=\"btn\" value=\"Удалить\"><input type=\"hidden\" name=\"op\" value=\"addtheme\"><input type=\"hidden\" name=\"action\" value=\"delete\"><input type=\"hidden\" name=\"id\" value=\"".$row['id']."\"></td></tr>";
}


echo "</table>";
echo "<br>";
}
}
switch ($op) {
	case "addtheme":
	addtheme($name, $action, $id);
	break;
}
}
break;
	
	
	case "BlocksAdmin":
	case "BlocksNew":
	case "BlocksFile":
	case "BlocksFileEdit":
	case "BlocksAdd":
	case "BlocksEdit":
	case "Blockschecks":
	case "BlocksEditSave":
	case "BlocksChange":
	case "BlocksDelete":
	case "BlocksFixweight":
	case "BlocksShow":
	case "BlocksChecksAll":
	case "BlocksOrder": 

{

$allowed_modules = array(
	"admincp" => "Админка",
	"0nline" => "Кто Где",
	"staff" => "Персонал",
	"browse" => "Торренты",
	"browseday" => "Торренты сегодня",
	"upload" => "Загрузить",
	"details" => "Детали торрента",
	"my" => "Панель управ.",
	"userdetails" => "Профиль",
	"detailsoff" => "Запросы",
	"forums" => "Форум",
	"log" => "Журнал",
	"faq" => "ЧаВо",
	"rules" => "Правила",
	"message" => "Личка",
	"recover" => "Восстан. пароля",
	"signup" => "Регистрация",
	"login" => "Вход",
	"mybonus" => "Мой Бонус",
	"invite" => "Приглашения",
	"bookmarks" => "Закладки",
	"humorall" => "Анекдоты",
	"support" => "Техподдержка",
	"license" => "Авторские баны",
	"redir" => "Переходы",
	"useragreement" => "О движке"
);

function BlocksNavi() {
	global $admin_file,$integrity;
	echo "<h2>Управление блоками [ <a class=\"altlink_white\" href=\"".$admin_file.".php?op=BlocksAdmin\">Главная блоки</a> | <a class=\"altlink_white\" href=\"".$admin_file.".php?op=BlocksNew\">Добавить новый блок</a> ".($integrity==1 ?"| <a class=\"altlink_white\" href=\"".$admin_file.".php?op=BlocksChecksAll\">Отметить целостность файлов</a>":"")." ]</h2> "
//	." | <a href=\"".$admin_file.".php?op=BlocksFile\">Добавить новый файловый блок</a>"
//	." | <a href=\"".$admin_file.".php?op=BlocksFileEdit\">Редактировать блок</a> ]"
;
}

function BlocksAdmin() {
	global $admin_file,$integrity;
	BlocksNavi();
	echo "<p /><table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\"><tr align=\"center\">"
	."<td class=\"colhead\">№</td>
	<td class=\"colhead\">Заголовок</td><td class=\"colhead\">Позиция</td>
	<td colspan=\"2\" class=\"colhead\">Положение</td>
	<td class=\"colhead\">Статус</td>
	<td class=\"colhead\">Кто видит</td>
	<td class=\"colhead\">Функции</td>
	</tr>";
    $oldbposition = "";
	$result = sql_query("SELECT a.bid, a.title, a.bposition, a.weight, a.active, a.blockfile, a.view, a.expire, a.action, a.md5, b.bid, b.bposition, b.weight, c.bid, c.bposition, c.weight 
	FROM orbital_blocks AS a 
	LEFT JOIN orbital_blocks AS b ON (b.bposition = a.bposition AND b.weight = a.weight-1) 
	LEFT JOIN orbital_blocks AS c ON (c.bposition = a.bposition AND c.weight = a.weight+1) 
	ORDER BY a.bposition, a.weight") or sqlerr(__FILE__,__LINE__);
	while (list($bid, $title, $bposition, $weight, $active, $blockfile, $view, $expire, $action, $md5, $con1, $bposition1, $weight1, $con2, $bposition2, $weight2) = mysql_fetch_row($result)) {
	
		
		if ($c%2==0) { $class="a"; $class2="b"; } else {$class="b"; $class2="a"; }
		
				
      if ($oldbposition<>$bposition && !empty($c))
	  echo "<tr><td class=\"colhead\" colspan=\"9\"></tr><tr>";
		
		if (($expire && $expire < time()) || (!$active && $expire)) {
			if ($action == "d") {
				sql_query("UPDATE orbital_blocks SET active='0', expire='0' WHERE bid='$bid'");
			} elseif ($action == "r") {
				sql_query("DELETE FROM orbital_blocks WHERE bid='$bid'");
			}
	@unlink(ROOT_PATH."cache/blocks_all.txt"); /// удаляем кеш блоков где 3600 сек
		}
	////////////
		@chmod($blockfile, 0555);
/////////////
		$weight_minus = $weight - 1;
		$weight_plus = $weight + 1;
		
if(!file_exists("blocks/$blockfile")){
$view_colore="<font color=\"red\">$blockfile</font>";
} else
$view_colore="<font color=\"green\">$blockfile</font>";

	   $filesize=@filesize("blocks/".$blockfile);
       $file_time=@md5_file("blocks/".$blockfile);
       $file_md5=md5($filesize.$file_time);
       //	Blockschecks($bid);  

if ($integrity==1){
if ($file_md5<>$md5) {
$check_file="<a href=\"".$admin_file.".php?op=Blockschecks&bid=$bid\"><img src=\"./pic/button_offline.gif\" title=\"Файл не прошел проверку целостности\" border=\"0\"></a>";
} else
$check_file="<img src=\"./pic/button_online.gif\" title=\"Файл прошел проверку целостности\" border=\"0\">";
}
		echo "<tr>
		<td class=\"".$class2."\" align=\"center\">$bid</td>
		<td class=\"".$class."\">".$title."<br>".$view_colore."</td>";
		
	
	    $oldbposition = $bposition;
	   
		if ($bposition == "l") {
			$bposition = "Левый блок";
		} elseif ($bposition == "r") {
			$bposition = "Правый блок";
		} elseif ($bposition == "c") {
			$bposition = "Центральный блок вверх";
		} elseif ($bposition == "d") {
			$bposition = "Центральный блок вниз";
		} elseif ($bposition == "b") {
			$bposition = "Верхний баннер";
		} elseif ($bposition == "f") {
			$bposition = "Нижний баннер";
		}

	
	
	/*
		if ($bposition == "l") {
			$bposition = "<img src=\"./pic/admin/left.gif\" border=\"0\" alt=\"Левый блок\" title=\"Левый блок\"> Слева";
		} elseif ($bposition == "r") {
			$bposition = "Справа <img src=\"./pic/admin/right.gif\" border=\"0\" alt=\"Правый блок\" title=\"Правый блок\">";
		} elseif ($bposition == "c") {
			$bposition = "<img src=\"./pic/admin/right.gif\" border=\"0\" alt=\"Центральный блок\" title=\"Центральный блок\">&nbsp;По центру вверху&nbsp;<img src=\"./pic/admin/left.gif\" border=\"0\" alt=\"Центральный блок\" title=\"Центральный блок\">";
		} elseif ($bposition == "d") {
			$bposition = "<img src=\"./pic/admin/right.gif\" border=\"0\" alt=\"Центральный блок\" title=\"Центральный блок\">&nbsp;По центру внизу&nbsp;<img src=\"./pic/admin/left.gif\" border=\"0\" alt=\"Центральный блок\" title=\"Центральный блок\">";
		} elseif ($bposition == "b") {
			$bposition = "<img src=\"./pic/admin/up.gif\" border=\"0\" alt=\"Баннер\" title=\"Баннер\">&nbsp;Верхний баннер&nbsp;<img src=\"./pic/admin/up.gif\" border=\"0\" alt=\"Баннер\" title=\"Баннер\">";
		} elseif ($bposition == "f") {
			$bposition = "<img src=\"./pic/admin/down.gif\" border=\"0\" alt=\"Баннер\" title=\"Баннер\">&nbsp;Нижний баннер&nbsp;<img src=\"./pic/admin/down.gif\" border=\"0\" alt=\"Баннер\" title=\"Баннер\">";
		}
		*/
		

		echo "<td class=\"".$class2."\" align=\"center\">".$bposition."</td>";
		
		echo "<td class=\"".$class."\" align=\"center\">".$weight."</td>";
		
		
		echo "<td class=\"".$class2."\" align=\"center\">";
		
		if ($con1) 
		echo "<a href=\"".$admin_file.".php?op=BlocksOrder&weight=$weight&bidori=$bid&weightrep=$weight_minus&bidrep=$con1\"><img src=\"./pic/admin/up.gif\" alt=\"Переместить вверх\" title=\"Переместить вверх\" border=\"0\"></a> ";
		
		if ($con2)
		echo "<a href=\"".$admin_file.".php?op=BlocksOrder&weight=$weight&bidori=$bid&weightrep=$weight_plus&bidrep=$con2\"><img src=\"./pic/admin/down.gif\" alt=\"Переместить вниз\" title=\"Переместить вниз\" border=\"0\"></a>";
		
		echo"</td>";


		$block_act = $active;
		if ($active == 1) {
			$active = "<font color=\"#009900\">Вкл.</font>";
			$change = "title=\"Выкл.\" name=\"block_$bid\"><img src=\"./pic/admin/inactive.gif\" border=\"0\" alt=\"Выкл.\"></a>";
		} elseif ($active == 0) {
			$active = "<font color=\"#FF0000\">Выкл.</font>";
			$change = "title=\"Вкл.\"name=\"block_$bid\"><img src=\"./pic/admin/activate.gif\" border=\"0\" alt=\"Вкл.\"></a>";
		}
		
		echo "<td class=\"".$class."\" align=\"center\">".$active." ".(!empty($check_file) ? "<br>".$check_file:"")."</td>";
		if ($view == 0) {
			$who_view = "<font color=\"#009900\">Все посетители и гости</font>";
		} elseif ($view == 1) {
			$who_view = "<font color=\"#ffa500\">Только зарегистрированные <br>пользователи</font>";
		} elseif ($view == 2) {
			$who_view = "<font color=\"red\">Только администрация</font>";
		} elseif ($view == 3) {
			$who_view = "<i>Только гости</i>";
		} elseif ($view == 4) {
			$who_view = "<font color=\"#9c2fe0\">Все пользователи кроме Випов</font>";
		} elseif ($view == 5) {
			$who_view = "<font color=\"blue\">Только Боссы</font>";
		}
		echo "<td class=\"".$class2."\" align=\"center\">".$who_view."</td>";
		echo "<td class=\"".$class."\" align=\"center\"><a href=\"".$admin_file.".php?op=BlocksEdit&bid=$bid\" title=\"Редактировать\"><img src=\"./pic/admin/edit.gif\" border=\"0\" alt=\"Редактировать\"></a> <a href=\"".$admin_file.".php?op=BlocksChange&bid=".$bid."\" ".$change;
		echo " <a href=\"".$admin_file.".php?op=BlocksDelete&bid=$bid\" OnClick=\"return DelCheck(this, 'Удалить &quot;$title&quot;?');\" title=\"Удалить\"><img src=\"./pic/admin/delete.gif\" border=\"0\" alt=\"Удалить\"></a>";
		///	if ($block_act == 0) 
	echo " <a href=\"".$admin_file.".php?op=BlocksShow&bid=$bid\" title=\"Показать\"><img src=\"./pic/admin/show.gif\" border=\"0\" alt=\"Показать\"></a>";
	++$c;
	}

	if (mysql_num_rows($result) == 0)
	echo "<tr><td class=\"colhead\" colspan=\"9\">Нет блоков.";
	
	echo "</td></tr></table><br>";
//<center>[ <a href=\"".$admin_file.".php?op=BlocksFixweight\">Зафиксировать позицию и положение блоков</a> ]</center>

}

function BlocksNew() {
	global $admin_file;
	BlocksNavi();
	echo "<h2>Добавить новый блок</h2>"
	."<form action=\"".$admin_file.".php\" method=\"post\">"
	."<table width=\"100%\" border=\"0\" align=\"center\">"
	."<tr><td class=\"a\"><b>Заголовок</b>:</td><td><input type=\"text\" name=\"title\" size=\"65\" style=\"width:400px\" maxlength=\"60\"></td></tr>"
	."<tr><td class=\"a\"><b>Имя файла</b>:</td><td>"
	."<select name=\"blockfile\" style=\"width:400px\">"
	."<option name=\"blockfile\" value=\"\" selected>Нет</option>";
	$handle = opendir("blocks");
	while ($file = readdir($handle)) {
		if (preg_match("/^block\-(.+)\.php/", $file, $matches)) {
			$found = str_replace("_", " ", $matches[1]);
			if (mysql_num_rows(sql_query("SELECT * FROM orbital_blocks WHERE blockfile=".sqlesc($file))) == 0) echo "<option value=\"$file\">".$found."</option>\n";
		}
	}
	closedir($handle);
	echo "</select></td></tr>"
//	."<tr><td>Содержание:</td><td><textarea name=\"content\" cols=\"65\" rows=\"15\" style=\"width:400px\"></textarea></td></tr>"
	."<tr><td class=\"a\"><b>Позиция</b>:</td><td><select name=\"bposition\" style=\"width:400px\">"
	."<option name=\"bposition\" value=\"l\">Слева</option>"
	."<option name=\"bposition\" value=\"c\">По центру вверху</option>"
	."<option name=\"bposition\" value=\"d\">По центру внизу</option>"
	."<option name=\"bposition\" value=\"r\">Справа</option>"
	."<option name=\"bposition\" value=\"b\">Верхний баннер</option>"
	."<option name=\"bposition\" value=\"f\">Нижний баннер</option>"
	."</select></td></tr>";
	echo "<tr><td class=\"a\"><b>Отображать блок в модулях</b>:</td>
	
	<td class=\"b\" align=\"center\">
	
	<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" align=\"left\" style=\"width:400px\"><tr>";
	echo "<td class=\"a\"><input type=\"checkbox\" name=\"blockwhere[]\" value=\"ihome\"></td>
	<td class=\"b\">Главная</td>";
	global $allowed_modules;
	$a = 1;
	foreach ($allowed_modules as $name => $title) {
		++$i;
	//	$title = str_replace("_", " ", $title);
		$title = preg_replace("/_/", " ", $title);

		echo "<td class=\"a\"><input type=\"checkbox\" name=\"blockwhere[]\" value=\"".$name."\"></td>
		<td class=\"b\">".$title."</td>";
		if ($a == 2) {
			echo "</tr><tr>";
			$a = 0;
		} else {
			++$a;
		}
	}
	echo "</tr><tr>
	<td class=\"a\"><input type=\"checkbox\" name=\"blockwhere[]\" value=\"all\"></td>
	<td><b>Во всех модулях</b></td>
	<td class=\"a\"><input type=\"checkbox\" name=\"blockwhere[]\" value=\"home\"></td>
	<td><b>Только на главной</b></td>
	<td class=\"a\"><input type=\"checkbox\" name=\"blockwhere[]\" value=\"infly\"></td>
	<td><b>Свободный блок</b></td>
	</tr></table></td>
	</tr>";
	echo "<tr><td><b>Включить?</b></td><td><input type=\"radio\" name=\"active\" value=\"1\" checked>Да &nbsp;&nbsp; <input type=\"radio\" name=\"active\" value=\"0\">Нет</td></tr>"
	."<tr><td><b>Время работы, в днях</b>:</td><td><input type=\"text\" name=\"expire\" maxlength=\"3\" value=\"0\" size=\"65\" style=\"width:400px\"></td></tr>"
	."<tr><td><b>После истечения</b>:</td><td><select name=\"action\" style=\"width:400px\">"
	."<option name=\"action\" value=\"d\">Выкл.</option>"
	."<option name=\"action\" value=\"r\">Удалить</option></select></td></tr>"
	."<tr><td><b>Кто это будет видеть?</b></td><td><select name=\"view\" style=\"width:400px\">"
	."<option value=\"0\" >Все посетители и гости</option>"
	."<option value=\"1\" >Только пользователи</option>"
	."<option value=\"2\" >Только администрация</option>"
	."<option value=\"3\" >Только гости</option>"
	."<option value=\"4\" >Все пользователи кроме Випов и Админов</option>"
	."<option value=\"5\" >Только для Боссов</option>"
	."</select></td></tr>"
	."<tr><td colspan=\"2\" align=\"center\"><br /><input type=\"hidden\" name=\"op\" value=\"BlocksAdd\"><input type=\"submit\" class=\"btn\" value=\"Создать блок\"></td></tr></table></form>";
}

function BlocksFile() {
	global $admin_file;
	BlocksNavi();
	echo "<h2>Добавить новый файловый блок</h2>"
	."<form action=\"".$admin_file.".php\" method=\"post\">"
	."<table border=\"0\" align=\"center\">"
	."<tr><td>Имя файла:</td><td><input type=\"text\" name=\"bf\" size=\"65\" style=\"width:400px\" maxlength=\"200\">"
	."<tr><td>Тип:</td><td><input type=\"radio\" name=\"flag\" value=\"php\" checked>PHP &nbsp;&nbsp; <input type=\"radio\" name=\"flag\" value=\"html\">HTML</td></tr>"
	."<tr><td colspan=\"2\" align=\"center\"><br /><input type=\"hidden\" name=\"op\" value=\"BlocksbfEdit\">"
	."<input type=\"submit\" class=\"btn\" value=\"Создать блок\"></td></tr></table></form>";
}

function BlocksOrder($weightrep,$weight,$bidrep,$bidori) {
	global $admin_file;
	$result = sql_query("UPDATE orbital_blocks SET weight='$weight' WHERE bid='$bidrep'");
	$result2 = sql_query("UPDATE orbital_blocks SET weight='$weightrep' WHERE bid='$bidori'");
	@unlink(ROOT_PATH."cache/blocks_all.txt"); /// удаляем кеш блоков где 3600 сек
	@header("Location: ".$admin_file.".php?op=BlocksAdmin#block_$bidori");
}

function Blockschecks($bid) {
	global $admin_file;
	$row = mysql_fetch_array(sql_query("SELECT blockfile FROM orbital_blocks WHERE bid=".sqlesc($bid)));
	$blockfile=$row["blockfile"];
	
	if ($blockfile){		
	$filesize=filesize("blocks/".$blockfile."");
	$file_time=md5_file("blocks/".$blockfile."");
    $file_md5=md5($filesize.$file_time);
    	
	$result = sql_query("UPDATE orbital_blocks SET md5='$file_md5' WHERE bid='$bid'");
	@unlink(ROOT_PATH."cache/blocks_all.txt"); /// удаляем кеш блоков где 3600 сек
	@Header("Location: ".$admin_file.".php?op=BlocksAdmin#block_$bid");
	}	else	{
	@Header("Location: ".$admin_file.".php?op=BlocksAdmin#no_file");
	}
}

function BlocksChecksAll($id){
	global $admin_file;
	$r=sql_query("SELECT blockfile, bid FROM orbital_blocks");

	while ($row=mysql_fetch_array($r)){
	$blockfile=$row["blockfile"];
	$bid=$row["bid"];
	$filesize=filesize("blocks/".$blockfile."");
	$file_time=md5_file("blocks/".$blockfile."");
    $file_md5=md5($filesize.$file_time);
    
	$result = sql_query("UPDATE orbital_blocks SET md5='$file_md5' WHERE bid='$bid'");
	}
	@unlink(ROOT_PATH."cache/blocks_all.txt"); /// удаляем кеш блоков где 3600 сек
	@Header("Location: ".$admin_file.".php?op=BlocksAdmin#ready");
}


function BlocksAdd($title, $bposition, $active, $blockfile, $view, $expire, $action) {
	global $admin_file;

	list($weight) = mysql_fetch_row(sql_query("SELECT weight FROM orbital_blocks WHERE bposition=".sqlesc($bposition)." ORDER BY weight DESC"));
	
	++$weight;

	$btime = "";
	if (!empty($blockfile)) {
	///	$url = "";
		if (empty($title)) {
			$array = array("block-",".php","_");
			$title = str_replace($array, "", $title);
		}
	}

	if (empty($blockfile) && empty($title)) {
		stdmsg("Ошибка", "Блок не может быть пустым!", 'error');
	} else {
	
		if (empty($expire) || $expire == 0 || !is_valid_id($expire)) {
		$expire = 0;
		} else {
		$expire = time() + ($expire * 86400);
		}

		if (isset($_POST['blockwhere'])) {
			$blockwhere = $_POST['blockwhere'];
			$which = "";
			$which = (in_array("all", $blockwhere)) ? "all" : $which;
			$which = (in_array("home", $blockwhere)) ? "home" : $which;
			
			if (empty($which)) {
				while(list($key, $val) = each($blockwhere)) {
					$which .= "{$val},";
				}
			}
		}
		
		$filesize=filesize("blocks/".$blockfile."");  
		$file_time=md5_file("blocks/".$blockfile."");
        $md5=md5($filesize.$file_time);

  ///title 	bposition 	weight 	active 	time 	blockfile 	md5 	view 	expire 	action 	which
		sql_query("INSERT INTO orbital_blocks VALUES (NULL, ".implode(", ", array_map("sqlesc", array($title, $bposition, $weight, $active, $btime, $blockfile, $md5, $view, $expire, $action, $which))).")") or sqlerr(__FILE__,__LINE__);

        $id = mysql_insert_id();
		@unlink(ROOT_PATH."cache/blocks_all.txt"); /// удаляем кеш блоков где 3600 сек
		Header("Location: ".$admin_file.".php?op=BlocksAdmin#block_$id");
	}
}

function BlocksEdit($bid) {
	global $admin_file;
	BlocksNavi();

	list($title, $bposition, $weight, $active, $blockfile, $view, $expire, $action, $which) = mysql_fetch_row(sql_query("SELECT title, bposition, weight, active, blockfile, view, expire, action, which FROM orbital_blocks WHERE bid=".sqlesc($bid)));


	echo "<h2>Блок: $title ".(empty($type)? "":$type)."</h2>"
	."<form action=\"".$admin_file.".php\" method=\"post\">"
	."<table width=\"100%\" border=\"0\" align=\"center\">"
	."<tr><td class=\"a\"><b>Заголовок</b>:</td><td><input type=\"text\" name=\"title\" maxlength=\"50\" size=\"65\" style=\"width:400px\" value=\"$title\"></td></tr>";
	if (!empty($blockfile)) {
		echo "<tr><td class=\"a\"><b>Имя файла</b>:</td><td><select name=\"blockfile\" style=\"width:400px\">";
		$dir = opendir("blocks");
		while ($file = readdir($dir)) {
			if (preg_match("/^block\-(.+)\.php/", $file, $matches)) {
				$found = str_replace("_", " ", $matches[1]);
				$selected = ($blockfile == $file) ? "selected" : "";
				echo "<option value=\"$file\" $selected>".$found."</option>";
			}
		}
		closedir($dir);
	} else {
	//	echo "<tr><td>Содержание:</td><td><textarea name=\"content\" cols=\"65\" rows=\"15\" style=\"width:400px\">$content</textarea></td></tr>";
	}
	$oldposition = $bposition;
	echo "<input type=\"hidden\" name=\"oldposition\" value=\"$oldposition\">";
	$sel1 = ($bposition == "l") ? "selected" : "";
	$sel2 = ($bposition == "c") ? "selected" : "";
	$sel3 = ($bposition == "r") ? "selected" : "";
	$sel4 = ($bposition == "d") ? "selected" : "";
	$sel5 = ($bposition == "b") ? "selected" : "";
	$sel6 = ($bposition == "f") ? "selected" : "";
	echo "<tr><td class=\"a\"><b>Позиция</b>:</td><td><select name=\"bposition\" style=\"width:400px\">"
	."<option name=\"bposition\" value=\"l\" $sel1>Слева</option>"
	."<option name=\"bposition\" value=\"c\" $sel2>По центру вверху</option>"
	."<option name=\"bposition\" value=\"d\" $sel4>По центру внизу</option>"
	."<option name=\"bposition\" value=\"r\" $sel3>Справа</option>"
	."<option name=\"bposition\" value=\"b\" $sel5>Верхний баннер</option>"
	."<option name=\"bposition\" value=\"f\" $sel6>Нижний баннер</option>"
	."</select></td></tr>";
	echo "<tr><td class=\"a\"><b>Отображать блок в модулях</b>:</td>
	<td align=\"center\" class=\"b\">
	<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" align=\"left\" style=\"width:400px\"><tr>";
	$where_mas = explode(",", $which);
	$cel = ($where_mas[0] == "ihome") ? " checked" : "";
	echo "<td class=\"a\"><input type=\"checkbox\" name=\"blockwhere[]\" value=\"ihome\"$cel></td>
	<td class=\"b\">Главная</td>";
	global $allowed_modules;
	$a = $i = 1;
	foreach ($allowed_modules as $name => $title) {
		++$i;
		$cel = "";
		foreach ($where_mas as $key => $val) {
			if ($val == $name) $cel = " checked";
		}
		$title = str_replace("_", " ", $title);
		echo "<td class=\"a\"><input type=\"checkbox\" name=\"blockwhere[]\" value=\"".$name."\"$cel></td>
		<td class=\"b\">".$title."</td>";
		if ($a == 2) {
			echo "</tr><tr>";
			$a = 0;
		} else {
			++$a;
		}
	}
	$where_mas = explode(",", $which);
    $cel = "";
    $hel = "";
	switch ($where_mas[0]) {
		case "all":
		$cel = " checked";
		break;
		case "home":
		$hel = " checked";
		break;
		case "infly":
		$fel = " checked";
		break;
	}
	echo "</tr><tr>
	<td class=\"a\"><input type=\"checkbox\" name=\"blockwhere[]\" value=\"all\" ".(empty($cel)? "":$cel)."></td>
	<td><b>Во всех модулях</b></td>
	<td class=\"a\"><input type=\"checkbox\" name=\"blockwhere[]\" value=\"home\" ".(empty($hel)? "":$hel)."></td>
	<td><b>Только на главной</b></td>
	<td class=\"a\"><input type=\"checkbox\" name=\"blockwhere[]\" value=\"infly\" ".(empty($fel)? "":$fel)."></td>
	<td><b>Свободный блок</b></td>
	</tr></table>
	</td></tr>";
	$sel1 = ($active == 1) ? "checked" : "";
	$sel2 = ($active == 0) ? "checked" : "";
	if (!empty($expire)){
		$newexpire = 0;
		$oldexpire = $expire;
		$expire = intval(($expire - time()) / 3600);
		$exp_day = $expire / 24;
		$expire_text = "<input type=\"hidden\" name=\"expire\" value=\"".$oldexpire."\"><b>Осталось</b>: ".$expire." часы (".substr($exp_day,0,5)." дней)";
	} else {
		$newexpire = 1;
		$expire_text = "<input type=\"text\" name=\"expire\" value=\"0\" maxlength=\"3\" size=\"65\" style=\"width:400px\">";
	}
	$selact1 = ($action == "d") ? "selected" : "";
	$selact2 = ($action == "r") ? "selected" : "";
	echo "<tr>
	<td class=\"a\"><b>Включить?</b></td><td><input type=\"radio\" name=\"active\" value=\"1\" $sel1>Да &nbsp;&nbsp;"
	."<input type=\"radio\" name=\"active\" value=\"0\" $sel2>Нет</td></tr>"
	."<tr><td class=\"a\"><b>Время работы, в днях</b>:</td>
	<td>$expire_text</td></tr>"
	."<tr><td class=\"a\"><b>После истечения</b>:</td>
	<td><select name=\"action\" style=\"width:400px\">"
	."<option name=\"action\" value=\"d\" $selact1>Выкл.</option>"
	."<option name=\"action\" value=\"r\" $selact2>Удалить</option></select></td></tr>";
	$sel1 = ($view == 0) ? "selected" : "";
	$sel2 = ($view == 1) ? "selected" : "";
	$sel3 = ($view == 2) ? "selected" : "";
	$sel4 = ($view == 3) ? "selected" : "";
	$sel5 = ($view == 4) ? "selected" : "";
	$sel6 = ($view == 5) ? "selected" : "";
	echo "</td></tr>
	<tr><td class=\"a\"><b>Кто это будет видеть?</b></td>
	<td><select name=\"view\" style=\"width:400px\">"
	."<option value=\"0\" $sel1>Все посетители</option>"
	."<option value=\"1\" $sel2>Только пользователи</option>"
	."<option value=\"2\" $sel3>Только администраторы</option>"
	."<option value=\"3\" $sel4>Только анонимы</option>"
	."<option value=\"4\" $sel5>Все пользователи кроме Випов и Админов</option>"
	."<option value=\"5\" $sel6>Только для Боссов</option>"
	."</select></td></tr></table><br>"
	."<center><input type=\"hidden\" name=\"bid\" value=\"$bid\">"
	."<input type=\"hidden\" name=\"newexpire\" value=\"$newexpire\">"
	."<input type=\"hidden\" name=\"weight\" value=\"$weight\">"
	."<input type=\"hidden\" name=\"op\" value=\"BlocksEditSave\">"
	."<input type=\"submit\" class=\"btn\" value=\"Сохранить настройки блока\"></form></center>";
}

function BlocksEditSave($newexpire, $bid, $title, $oldposition, $bposition, $active, $weight, $blockfile, $view, $expire, $action) {
	global $db, $admin_file;


	if (isset($_POST['blockwhere'])) {
		$blockwhere=$_POST['blockwhere'];
		$which = "";
		$which = (in_array("all", $blockwhere)) ? "all" : $which;
		$which = (in_array("home", $blockwhere)) ? "home" : $which;
		if ($which == "") {
			echo $which;
			while(list($key, $val) = each($blockwhere)) {
				$which .= "{$val},";
			}
		}
		sql_query("UPDATE orbital_blocks SET which=".sqlesc($which)." WHERE bid=".sqlesc($bid));
	} else {
		sql_query("UPDATE orbital_blocks SET which='' WHERE bid=".sqlesc($bid));
	}
	
		if ($oldposition <> $bposition) {
			$result5 = sql_query("SELECT bid FROM orbital_blocks WHERE weight>=".sqlesc($weight)." AND bposition=".sqlesc($bposition));
			$fweight = $weight;
			$oweight = $weight;
			while (list($nbid) = mysql_fetch_row($result5)) {
				++$weight;
				sql_query("UPDATE orbital_blocks SET weight=".sqlesc($weight)." WHERE bid=".sqlesc($nbid)) or sqlerr(__FILE__,__LINE__);
			}
			$result6 = sql_query("SELECT bid FROM orbital_blocks WHERE weight>".sqlesc($oweight)." AND bposition=".sqlesc($oldposition)) or sqlerr(__FILE__,__LINE__);
			while (list($obid) = mysql_fetch_row($result6)) {
				sql_query("UPDATE orbital_blocks SET weight=".sqlesc($oweight)." WHERE bid=".sqlesc($obid));
				++$oweight;
			}
			list($lastw) = mysql_fetch_row(sql_query("SELECT weight FROM orbital_blocks WHERE bposition=".sqlesc($bposition)." ORDER BY weight DESC LIMIT 0,1"));
			if ($lastw <= $fweight) {
				++$lastw;
				
				$filesize=filesize("blocks/".$blockfile."");  $file_time=md5_file("blocks/".$blockfile."");
    
	            $file_md5=md5($filesize.$file_time);
	            
				sql_query("UPDATE orbital_blocks SET title=".sqlesc($title).", bposition=".sqlesc($bposition).", weight=".sqlesc($lastw).", active=".sqlesc($active).", md5=".sqlesc($file_md5).", blockfile=".sqlesc($blockfile).", view=".sqlesc($view)." WHERE bid=".sqlesc($bid)) or sqlerr(__FILE__,__LINE__);
			} else {
				
				$filesize=filesize("blocks/".$blockfile."");  $file_time=md5_file("blocks/".$blockfile."");
$file_md5=md5($filesize.$file_time);

				sql_query("UPDATE orbital_blocks SET title=".sqlesc($title).", bposition=".sqlesc($bposition).", weight=".sqlesc($fweight).", active=".sqlesc($active).", md5=".sqlesc($file_md5).", blockfile=".sqlesc($blockfile).", view=".sqlesc($view)." WHERE bid=".sqlesc($bid)) or sqlerr(__FILE__,__LINE__);
			}
		} else {

$filesize=filesize("blocks/".$blockfile."");  $file_time=md5_file("blocks/".$blockfile."");
$file_md5=md5($filesize.$file_time);
		
if (empty($expire))
$expire = 0;

if ($newexpire == 1 && !empty($expire))
$expire = time() + ($expire * 86400);


$result8 = sql_query("UPDATE orbital_blocks SET title=".sqlesc($title).", bposition=".sqlesc($bposition).", weight=".sqlesc($weight).", md5=".sqlesc($file_md5).", active=".sqlesc($active).", blockfile=".sqlesc($blockfile).", view=".sqlesc($view).", expire=".sqlesc($expire).", action=".sqlesc($action)." WHERE bid=".sqlesc($bid)) or sqlerr(__FILE__,__LINE__);
		}
		
        @unlink(ROOT_PATH."cache/blocks_all.txt"); /// удаляем кеш блоков где 3600 сек

		Header("Location: ".$admin_file.".php?op=BlocksEdit&bid=$bid");
}

function BlocksShow($bid) {
	global $db, $admin_file;
	BlocksNavi();
	list($bid, $title, $bposition, $blockfile,$md5) = mysql_fetch_row(sql_query("SELECT bid, title, bposition, blockfile, md5 FROM orbital_blocks WHERE bid=".sqlesc($bid)));
	echo "<p />";
	/// $content = пусто
	render_blocks($bposition, $blockfile, $title,  $content, $bid, 'c',$md5);
	echo "<h4>[ <a href=\"".$admin_file.".php?op=BlocksChange&bid=$bid\">Включить</a> | <a href=\"".$admin_file.".php?op=BlocksEdit&bid=$bid\">Редактировать</a>";
	echo " | <a href=\"".$admin_file.".php?op=BlocksDelete&bid=$bid\" OnClick=\"return DelCheck(this, 'Удалить &quot;$title&quot;?');\">Удалить</a>";
	echo " | <a href=\"".$admin_file.".php?op=BlocksAdmin\">Главная</a> ]</h4>";
}

function BlocksFileEdit() {
	global $admin_file;
	BlocksNavi();
	echo "<h2>Редактировать блок</h2>"
	."<form action=\"".$admin_file.".php\" method=\"post\">"
	."<table border=\"0\" align=\"center\">"
	."<tr><td>Имя файла:</td><td>"
	."<select name=\"bf\" style=\"width:400px\">";
	$handle = opendir("blocks");
	while ($file = readdir($handle)) {
		if (preg_match("/^block\-(.+)\.php/", $file, $matches)) {
			$found = str_replace("-", " ", $matches[1]);
			if (mysql_num_rows(sql_query("SELECT * FROM orbital_blocks WHERE blockfile=".sqlesc($file))) > 0) echo "<option value=\"$file\">$found</option>\n";
		}
	}
	closedir($handle);
	echo "</select></td></tr>"
	."<tr><td colspan=\"2\" align=\"center\"><input type=\"hidden\" name=\"op\" value=\"BlocksbfEdit\"><input type=\"submit\" class=\"btn\" value=\"Редактировать блок\"></td></tr></table></form>";
}

function BlocksChange($bid, $ok=0) {
	global $admin_file;
//	$bid = intval($bid);
	$row = mysql_fetch_array(sql_query("SELECT active FROM orbital_blocks WHERE bid=".sqlesc($bid)));
	$active = intval($row['active']);
	if (($ok) || ($active == 0)) {
		if ($active == 0) {
			$active = 1;
		} elseif ($active == 1) {
			$active = 0;
		}
		$result = sql_query("UPDATE orbital_blocks SET active='$active' WHERE bid='$bid'");
		@unlink(ROOT_PATH."cache/blocks_all.txt"); /// удаляем кеш блоков где 3600 сек
		
if (!headers_sent()) {
Header("Location: ".$admin_file.".php?op=BlocksAdmin");
die;
} else {
die("Перенаправление на страницу ввода данных.<script>setTimeout('document.location.href=\"".$admin_file.".php?op=BlocksAdmin\"', 10);</script>");
}
		
	
	} else {
		list($title, $active) = mysql_fetch_row(sql_query("SELECT title, active FROM orbital_blocks WHERE bid=".sqlesc($bid)));
		if ($active == 0) {
			echo "<center>Активировать блок \"$title\"?<br /><br />";
		} else {
			echo "<center>Деактивировать блок \"$title\"?<br /><br />";
		}
		echo "[ <a href=\"".$admin_file.".php?op=BlocksChange&bid=$bid&ok=1\">Да</a> | <a href=\"".$admin_file.".php?op=BlocksAdmin\">Нет</a> ]</center>";
	}
	if ($_GET["ok"]==1)
		@Header("Location: ".$admin_file.".php?op=BlocksAdmin");
}

function BlocksbfEdit() {
	global $db, $admin_file;
	if ($_REQUEST['bf'] != "") {
		$bf = $_REQUEST['bf'];
		if (isset($_POST['flag'])) {
			$flaged = $_POST['flag'];
			$bf = str_replace("block-", "",$bf);
			$bf = str_replace(".php", "",$bf);
			$bf = 'block-'.$bf.'.php';
		} else {
			$bfstr = file_get_contents('blocks/'.$bf);
			if (strpos($bfstr,'BLOCKHTML') === false) {
				$flaged = 'php';
				preg_match("/<\?php.*if.*\(\!defined\(\'BLOCK_FILE\'\)\).*exit;.*?}(.*)\?>/is", $bfstr, $out);
				unset($out[0]);
			} else {
				$flaged = 'html';
				preg_match("/<<<BLOCKHTML(.*)BLOCKHTML;/is", $bfstr, $out);
				unset($out[0]);
			}
		}
		BlocksNavi();
		$permtest = end_chmod("blocks", 777);
		if ($permtest)
			stdmsg("Ошибка", $permtest, 'error');
		echo "<h2>Блок: $bf</h2>"
		."<form action=\"".$admin_file.".php\" method=\"post\">"
		."<table border=\"0\" align=\"center\">"
	//	."<tr><td>Содержание:</td><td><textarea wrap=\"virtual\" name=\"blocktext\" cols=\"65\" rows=\"25\" style=\"width:400px\">".$out[1]."</textarea></td></tr>"
		."<tr><td colspan=\"2\" align=\"center\"><br /><input type=\"hidden\" name=\"bf\" value=\"".$bf."\">"
		."<input type=\"hidden\" name=\"flag\" value=\"".$flaged."\">"
		//."<input type=\"hidden\" name=\"op\" value=\"BlocksbfSave\">"
		."<input type=\"submit\" class=\"btn\" value=\"Сохранить\"> <input type=\"button\" value=\"Назад\" onClick=\"javascript:history.go(-1)\"></td></tr></table></form>";
	} else {
		
if (!headers_sent()) {
Header("Location: ".$admin_file.".php?op=BlocksFile");
die;
} else {
die("Перенаправление на страницу ввода данных.<script>setTimeout('document.location.href=\"".$admin_file.".php?op=BlocksFile\"', 10);</script>");
}

	}
}


switch($op) {
	case "BlocksAdmin":
	BlocksAdmin();
	break;
	
	case "BlocksNew":
	BlocksNew();
	break;
	
	case "BlocksFile":
	BlocksFile();
	break;
	
	case "BlocksFileEdit":
	BlocksFileEdit();
	break;
	
	case "BlocksAdd":
	BlocksAdd($title, $bposition, $active, $blockfile, $view, $expire, $action);
	break;
	
	case "BlocksEdit":
	BlocksEdit($bid);
	break;
	
	case "Blockschecks":
	Blockschecks($bid);
	break;
	
	case "BlocksEditSave":
	BlocksEditSave($newexpire, $bid, $title, $oldposition, $bposition, $active, $weight, $blockfile, $view, $expire, $action);
	break;
	
	case "BlocksChange":
	BlocksChange($bid, $ok, $de);
	break;
	
	case "BlocksDelete": {
	$bid = intval($_REQUEST['bid']);
	list($bposition, $weight) = mysql_fetch_row(sql_query("SELECT bposition, weight FROM orbital_blocks WHERE bid=".sqlesc($bid)));
	$result = sql_query("SELECT bid FROM orbital_blocks WHERE weight>'$weight' AND bposition='$bposition'");
	while (list($nbid) = mysql_fetch_row($result)) {
		sql_query("UPDATE orbital_blocks SET weight='$weight' WHERE bid='$nbid'");
			@unlink(ROOT_PATH."cache/blocks_all.txt"); /// удаляем кеш блоков где 3600 сек
		$weight++;
	}
	sql_query("DELETE FROM orbital_blocks WHERE bid='$bid'");
	
	@unlink(ROOT_PATH."cache/blocks_all.txt"); /// удаляем кеш блоков где 3600 сек
	 
	Header("Location: ".$admin_file.".php?op=BlocksAdmin");
	}
	break;
	
	case "BlocksOrder":
	BlocksOrder($weightrep, $weight, $bidrep, $bidori);
	break;
	
	case "BlocksFixweight":
	BlocksFixweight();
	break;
	
	case "BlocksShow":
	BlocksShow($bid);
	break;
	
	case "BlocksChecksAll":
	BlocksChecksAll($bid);
	break;
	
	case "BlocksbfEdit":
	BlocksbfEdit();
	break;
	
}

}
break;
		
		
case "StatusDB": {


function StatusDB() {
	
	global $admin_file;
	
	include("include/passwords.php"); 
	define ('DB_NAMEFIX',$mysql_db_fix_by_imperator);
$mysql_user_fix_by_imperator=DB_NAMEFIX;
	



$result = sql_query("SHOW TABLES FROM ".($mysql_user_fix_by_imperator));
	
echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" align=\"center\">

<form method=\"post\" action=\"".$admin_file.".php\">
<tr>";

echo "<td class=\"a\"><select name=\"datatable[]\" size=\"10\" multiple=\"multiple\" style=\"width:300px\">";

while (list($name) = mysql_fetch_array($result)){
$count = number_format(get_row_count($name));
echo "<option value=\"".trim($name)."\" SELECTED >".$name." (".$count.")</option>";//
}

echo "</select><br><i>При жалении, можно выделить таблицу (ы)</i></td>";

echo "<td clas=\"b\">";

echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\">";

echo "<tr>
<td class=\"a\" valign=\"top\"><input type=\"radio\" name=\"type\" value=\"Optimize\" checked></td>
<td class=\"b\">Оптимизация базы данных<br /><font class=\"small\">Производя оптимизацию базы данных, Вы уменьшаете её размер и соответственно с этим ускоряете её работу. Рекомендуется использовать данную функцию минимум один раз в неделю.</font></td>
</tr>";

echo "<tr>
<td class=\"a\" valign=\"top\"><input type=\"radio\" name=\"type\" value=\"Repair\"></td>
<td class=\"b\">Ремонт базы данных<br /><font class=\"small\">При неожиданной остановке MySQL сервера, во время выполнения каких-либо действий, может произойти повреждение структуры таблиц базы данных, использование этой функции произведёт ремонт повреждённых таблиц.</font></td>
</tr>";

echo "</table></td></tr>";

echo "<input type=\"hidden\" name=\"op\" value=\"StatusDB\">
<tr>
<td colspan=\"2\" align=\"center\">
<input type=\"submit\" class=\"btn\" value=\"Выполнить выделенное действие\">
</td>
</tr></form>";

$res = sql_query("SELECT * FROM avps");

echo "<tr><td colspan=\"2\" align=\"center\" class=\"colhead\"><b>Последнее системное</b></td>";

while ($r= mysql_fetch_array($res)) {

echo "<tr>
<td colspan=\"2\" align=\"left\"><b>".$r["arg"]."</b> (<i>".$r["value_s"]."</i>) ".display_date_time($r["value_u"])." -- <b>".$r["value_i"]."</b>
</td>";	
}

echo "</table><br>";

if ($_POST['type'] == "Optimize") {

$dateba = $_POST["datatable"];

//print_r($dateba);
echo "<br>";

echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
<tr>
<td class=\"b\" align=\"center\" colspan=\"4\">Оптимизация базы данных: ".$mysql_db_fix_by_imperator."</td>
</tr>
<tr>
<td class=\"colhead\" align=\"center\">Таблица (Размер)</td>
<td class=\"colhead\" width=\"20%\" align=\"center\">Статус</td>
<td class=\"colhead\" width=\"30%\" align=\"center\">Накладные расходы</td>
</tr>";

$i = 0;
$result = sql_query("SHOW TABLE STATUS FROM ".$mysql_db_fix_by_imperator) or sqlerr(__FILE__, __LINE__);

while ($row = mysql_fetch_array($result)) {

if (in_array($row[0],$dateba)){
if ($i%2 == 1){
$clastd1 = "class=\"b\"";
$clastd2 = "class=\"a\"";
}else{
$clastd2 = "class=\"b\"";
$clastd1 = "class=\"a\"";
}

$total = $row['Data_length'] + $row['Index_length'];
$totaltotal += $total;
$free = ($row['Data_free']) ? $row['Data_free'] : 0;
$totalfree += $free;


sql_query("OPTIMIZE TABLE ".$row[0]) or sqlerr(__FILE__, __LINE__);

echo "<tr>
<td ".$clastd1."><b>".$row[0]."</b> (".mksize($total).")</td>
<td ".$clastd2." align=\"center\">".(empty($free) ? "<font color=\"#FF0000\">Не нуждается</font>" : "<font color=\"#009900\">Оптимизирована</font>")."</td>
<td ".$clastd1." align=\"center\">".mksize($free)."</td>
</tr>";

++$i;
}}

echo "<tr><td class=\"b\" align=\"center\" colspan=\"4\">Оптимизация базы данных: ".$mysql_db_fix_by_imperator."<br>Общий размер базы данных: ".mksize($totaltotal)."<br>В базе данных таблиц: ".$i."<br>Общие накладные расходы: ".mksize($totalfree)."</td></tr>";
		
echo "</table>";

} elseif ($_POST['type'] == "Repair") {

echo "<br>";

echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
<tr>
<td class=\"b\" align=\"center\" colspan=\"4\">Ремонт всех таблиц в базе данных: ".$mysql_db_fix_by_imperator."</td>
</tr>
<tr>
<td class=\"colhead\">Таблица (Размер)</td>
<td class=\"colhead\" align=\"center\">Статус</td>
</tr>";

$result = sql_query("SHOW TABLE STATUS FROM ".$mysql_db_fix_by_imperator);

while ($row = mysql_fetch_array($result)) {

$total = $row['Data_length'] + $row['Index_length'];
$totaltotal += $total;

if ($i%2 == 1){
$clastd1 = "class=\"b\"";
$clastd2 = "class=\"a\"";
}else{
$clastd2 = "class=\"b\"";
$clastd1 = "class=\"a\"";
}

$rresult = sql_query("REPAIR TABLE ".$row[0]) or sqlerr(__FILE__, __LINE__);
			
echo "<tr>
<td ".$clastd1."><b>".$row[0]."</b> (".mksize($total).")</td>
<td ".$clastd2." align=\"center\">".(empty($rresult) ? "<font color=\"#FF0000\">Ошибка, исправлена</font>" : "<font color=\"#009900\">Успешно, без ошибок</font>")."</td>
</tr>";

++$i;
}

echo "<tr><td class=\"b\" align=\"center\" colspan=\"4\">Ремонт всех таблиц в базе данных: ".$mysql_db_fix_by_imperator."<br>Общий размер базы данных: ".mksize($totaltotal)."</td></tr>";


echo "</table>";

}
	unset($mysql_user_fix_by_imperator,$DB_NAMEFIX);
	/////////////$DB_NAMEFIX ???
}

switch ($op) {
	case "StatusDB":
	StatusDB();
	break;
}

}
break;
	
	
	

case "iUsers": {

function iUsers($iname, $idname,$ipass, $imail) {
	global $admin_file,$CURUSER;
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$updateset = array();

  if ($CURUSER["id"]<>$idname)
  $class="class < ".$CURUSER["class"]." and ";
  
    if ($CURUSER["username"]<>$iname)
  $class="class < ".$CURUSER["class"]." and ";
  
          //  if($iname=="7Max7") stderr("Ошибочка вышла","ну типа незя менять");

          //  if($iname=="Tesla") stderr("Ошибочка вышла","ну типа незя менять");

		if (!empty($ipass)) {
			$secret = mksecret();
			$hash = md5($secret.$ipass.$secret);
			$updateset[] = "secret = ".sqlesc($secret);
			$updateset[] = "passhash = ".sqlesc($hash);
		}
		$idname=(int)$idname;
		if (!empty($iname)){
		$name_id="username = ".sqlesc($iname)."";
		}
		elseif(empty($iname) && !empty($idname)){
		$name_id="id = ".sqlesc($idname)."";
		}
		elseif(!empty($iname) && !empty($idname))
		{
		$name_id="id = ".sqlesc($idname)." or username = ".sqlesc($iname)."";
		}
		
		if (!empty($imail) && validemail($imail)){
			$updateset[] = "email = ".sqlesc($imail);
			}
			
		
		if (count($updateset) && (!empty($iname) || !empty($idname)))
		  {
		$res2 = sql_query("SELECT username,class,id FROM users WHERE $class id=".sqlesc($idname)." or username=".sqlesc($iname)."") or sqlerr(__FILE__, __LINE__);
         $s = mysql_fetch_array($res2);
         $username="<a href=userdetails.php?id=$s[id]>" . get_user_class_color($s["class"], $s["username"]).""."</a>";
	   	$res = sql_query("UPDATE users SET ".implode(", ", $updateset)." WHERE $name_id") or sqlerr(__FILE__,__LINE__);
		}
		
		if (mysql_modified_rows() < 1 || (empty($iname) && empty($idname)))
			stdmsg("Ошибка", "Смена пароля завершилась неудачей! 
		".(empty($iname) && empty($idname) ? "Ничего не заполнено в имени или id пользователя":" Возможно указано несуществующее имя пользователя. <br><a href=".$admin_file.".php?op=iUsers><b>Вернутся обратно</b></a>")."", "error");
		else
			stdmsg("Изменения пользователя прошло успешно", "
			".(!empty($iname) ? "Имя пользователя: ".($s["username"]==$iname ? "$username":"$iname")."":"")."
		    ".(!empty($idname) ? "ID пользователя: ".($s["id"]==$idname ? "$username":"$idname")."":"")."
			".(!empty($hash) ? "<br />Новый пароль: ".$ipass : "").(!empty($imail) ? "<br />Новая почта: ".$imail : "")."<br><a href=".$admin_file.".php?op=iUsers><b>Вернутся обратно</b></a>");
	} else {
		echo "<form method=\"post\" action=\"".$admin_file.".php?op=iUsers\">"
		."<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">"
		."<tr><td class=\"colhead\" colspan=\"2\">Смена пароля</td></tr>"
		."<tr>"
		."<td><b>Пользователь</b></td>"
		."<td><input name=\"iname\" type=\"text\"> или  <b>ID</b> <input name=\"idname\" type=\"text\"></td>"
		."</tr>"
		."<tr>"
		."<td><b>Новый пароль</b></td>"
		."<td><input name=\"ipass\" type=\"password\"></td>"
		."</tr>"
		."<tr>"
		."<td><b>Новая почта</b></td>"
		."<td><input name=\"imail\" type=\"text\"> <i>оставьте пустым если не хотите менять</i></td>"
		."</tr>"
		."<tr><td colspan=\"2\" align=\"center\"><input class=\"btn\" type=\"submit\" name=\"isub\" value=\"Выполнить\"></td></tr>"
		."</table>"
		."<input type=\"hidden\" name=\"op\" value=\"iUsers\" />"
		."</form><br>";
	}
}

switch ($op) {
	case "iUsers":
	iUsers($iname, $idname,$ipass, $imail);
	break;
}
}
	break;
	
	
	default:
	//echo "А";
	break;
	

	//	$dir = opendir("admin/modules");
	//	while ($file = readdir($dir)) {
	//		if (preg_match("/(\.php)$/is", $file) && $file != "." && $file != "..") require_once("admin/modules/".$file."");
	//	}
//	break;
}


stdfoot();

?>