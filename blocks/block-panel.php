<?php
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}


global $CURUSER;
/*echo("<script type=\"text/javascript\" src=js/blink.js></script>"
 );
*/
$h = date('H'); // проверяем час
if (($h > 6 )&&($h < 12)) {$view_time="Доброе Утро";}
if (($h >= 12 )&&($h < 18)) {$view_time="Добрый День";}
if (($h >= 18 )&&($h <= 23)) {$view_time="Добрый Вечер";}
if (($h >= 00 )&&($h <= 6)) {$view_time="Доброй Ночи";}

$blocktitle = $view_time;

$cacheStatFile = "cache/block-class_".$CURUSER["class"].".txt"; 

if ($CURUSER["class"] == UC_SYSOP)
$expire = 10*60; // 10 minutes 
else
$expire = 30*60; // 30 minutes 

if (file_exists($cacheStatFile) && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
	

$content.= "<center>";

if ($CURUSER["class"] == UC_SYSOP) {
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

$content.= "<b><font color=\"#0000FF\">-=Босс=-</font></b><br>";

$content.= "<a href=\"admincp.php\">Админка</a> : <a href=\"admincp.php?op=BlocksAdmin\">Блоки</a><br>";
$content.= "<a href=\"admincp.php?op=iUsers\">Паc</a> : <a href=\"changeusername.php\">Имя</a> : <a href=\"changeuserid.php\">Ид</a><br>";
$content.= "<a href=\"friensblockadd.php\">Друзья</a> : <a href=\"groups.php\">Группы</a> <br>";

if ($CURUSER["username"]=="Тя" || $CURUSER["username"]=="7Max7" || $CURUSER["id"]=="2") {
$content.= "<a href=\"dump3r.php\">База Данных</a><br>";
}

$content.= "<a href=\"cheaters.php\">Читеры</a><br>";
$content.= "$view_sql:$view_info:$view_error_sql:$view_bit<br>";
$content.= "<a href=\"tagscheck.php\">Двойные</a> : <a href=\"repair_tags.php\">Процед</a><br>";
$content.= "<a href=\"unco.php\">Неподтв.юзеры</a><br>";
$content.= "<a href=\"bans.php\">Бан IP</a> : <a href=\"bannedemails.php\">Почты</a><br>";
$content.= "<a href=\"category.php\">Категории</a><br>";
$content.= "<a href=\"sitemap_parts.php\">Sitemap</a><br>";
}



if ($CURUSER["class"] >= UC_ADMINISTRATOR) {
$content.= "<b><font color=\"green\">-=Администратор=-</font></b><br>";
$content.= "<a href=\"0nline.php\">Онлайн</a> : <a href=\"useragent.php\">Агент</a><br>";
$content.= "<a href=\"adduser.php\">Добавить юзера</a><br>";
$content.= "<a href=\"msg.php\">Сообщения</a><br>";

$content.= "<a title=\"Центр глобальной статистики\" href=\"statistics.php\">Общая</a> : <a title=\"Центр индивидуальной статистики\" href=\"ustatistics.php\">Индивид</a><br>";
$content.= "<a href=\"maxlogin.php\">Входы</a><br>";
$content.= "<a href=\"redirect.php\">Переходы</a> : <a href=\"referer.php\">Рефералы</a><br>";
$content.= "<a href=\"adminbookmark.php\">Подозреваемые</a><br>";
$content.= "<a href=\"downcheck.php\">Cинхронизация</a><br>";

$report = number_format(get_row_count("report"));
if (!empty($report))
$content.= "<a title=\"Количество жалоб $report\" href=\"viewreport.php\">Жалобы (".$report.")</a><br>";

$content.= "<a href=\"warned.php\">Предупр.</a> : <a href=\"hiderating.php\">Безгран.</a><br>";
$content.= "Массовое <a href=\"staffmess.php\">ЛС</a> : <a href=\"groupsmess.php\">Группе</a></a><br>";
$content.= "<a href=\"testip.php\">Проверка IP </a> : <a href=\"testport.php\">Порта</a><br>";
}


if ($CURUSER["class"] >= UC_MODERATOR) {
$content.= "<b><font color=\"red\">-=Модератор=-</font></b><br>";
$content.= "<a href=\"usersearch.php\">Поиск юзера</a><br>";

if ($CURUSER['override_class'] <> 255) {
$content .= "<a href=\"$DEFAULTBASEURL/restoreclass.php\">Восcтановить права</a><br>";
}
else
{
$content.= "<a href=\"setclass.php\">Тест Смена Класса</a><br>";
}
$content.= "<a href=\"staff.php?act=last\">Новые юзеры</a><br>";
$content.= "<a href=\"users.php\">Юзеры</a> : <a href=\"premod.php\">Премод</a><br>";
$content.= "<a href=\"antichiters.php\">Скорости</a> : <a href=\"seeders.php\">Кол-во</a><br>";
$content.= "<a href=\"smilies.php\">Смайлы</a> : <a href=\"tags.php\">Теги</a><br>";
$content.= "<a href=\"ipcheck.php\">Двойные IP</a> : <a href=\"emailcheck.php\">Мыла</a><br>";
$content.= "<a href=\"stats.php\">Стати-ка</a> : <a href=\"uploaders.php\">Аплодеры</a><br>";
$content.= "<a href=\"topten.php\">TOP10</a> : <a href=\"votes.php\">Оценки</a><br>";
$content.= "<a href=\"file_descr.php\">Описания</a><br>";
//$content.= "<a href=\"remote_gets.php\">Обновить Пиры</a><br>";
}

$content.= "<a href=\"license.php\">Авторские раздачи</a><br>";


$content.= "</center>";

$fp = fopen($cacheStatFile,"w");
if($fp){
fputs($fp, $content); 
fclose($fp); 
}
}


?>
<script>
function usermod_online() {
jQuery.post("block-usermod_jquery.php" , {} , function(response) {
		jQuery("#usermod_online").html(response);
	}, "html");
setTimeout("usermod_online();", 90000);
}
usermod_online();
</script>
<?

$content.='<div id="usermod_online"></div>';

?>