<?
require_once("include/bittorrent.php");

//// проверен на время.!!!


//////////// Array ///////////////
$arrSystem['Windows 3.1'] = "Windows 3.1";
$arrSystem['Win16'] = "Windows 3.1";
$arrSystem['16bit'] = "Windows 3.1";
$arrSystem['Win32'] = "Windows 95";
$arrSystem['32bit'] = "Windows 95";
$arrSystem['Win 32'] = "Windows 95";
$arrSystem['Win95'] = "Windows 95";
$arrSystem['Windows 95/NT'] = "Windows 95";
$arrSystem['Win98'] = "Windows 98";

$arrSystem['Windows 95'] = "Windows 95";
$arrSystem['Windows 98'] = "Windows 98";
$arrSystem['Windows NT 5.0'] = "Windows 2000";
$arrSystem['Windows NT 5.1'] = "Windows XP";
$arrSystem['Windows NT 5.2'] = "Windows XP (64 bit)";

$arrSystem['Windows NT 6.0'] = "Windows Vista";
$arrSystem['WinVI']= "Windows Vista";

$arrSystem['Windows NT 6.1'] = "Windows Seven";

$arrSystem['Windows NT'] = "Windows NT";
$arrSystem['WinNT'] = "Windows NT";
$arrSystem['Windows ME'] = "Windows ME";
$arrSystem['Windows CE'] = "Windows CE";
$arrSystem['Windows'] = "Windows 95";
$arrSystem['Mac_68000'] = "Macintosh";
$arrSystem['Mac_PowerPC'] = "Macintosh";
$arrSystem['Mac_68K'] = "Macintosh";
$arrSystem['Mac_PPC'] = "Macintosh";
$arrSystem['Macintosh'] = "Macintosh";
$arrSystem['IRIX'] = "Unix";
$arrSystem['SunOS'] = "Unix";
$arrSystem['AIX'] = "Unix";
$arrSystem['Linux'] = "Unix";
$arrSystem['HP-UX'] = "Unix";
$arrSystem['SCO_SV'] = "Unix";
$arrSystem['FreeBSD'] = "Unix";
$arrSystem['BSD/OS'] = "Unix";
$arrSystem['OS/2'] = "OS/2";
$arrSystem['WebTV/1.0'] = "WebTV/1.0";
$arrSystem['WebTV/1.2'] = "WebTV/1.2";
$arrBrowser['Lynx'] = "Lynx";
$arrBrowser['libwww-perl'] = "Lynx";
$arrBrowser['ia_archiver'] = "Веб-сайт";
$arrBrowser['ArchitextSpider'] = "Веб-сайт";
$arrBrowser['Lycos_Spider_(T-Rex)'] = "Веб-сайт";
$arrBrowser['Scooter'] = "Веб-сайт";
$arrBrowser['InfoSeek'] = "Веб-сайт";
$arrBrowser['AltaVista'] = "Веб-сайт";
$arrBrowser['Eule-Robot'] = "Веб-сайт";
$arrBrowser['SwissSearch'] = "Веб-сайт";
$arrBrowser['Checkbot'] = "Веб-сайт";
$arrBrowser['Crescent Internet ToolPak'] = "Веб-сайт";
$arrBrowser['Slurp'] = "Веб-сайт";
$arrBrowser['WiseWire-Widow'] = "Веб-сайт";
$arrBrowser['NetAttache'] = "Веб-сайт";
$arrBrowser['Web21 CustomCrawl'] = "Веб-сайт";
$arrBrowser['BTWebClient'] = "uTorrent RSS";
$arrBrowser['CheckUrl'] = "Веб-сайт";
$arrBrowser['LinkLint-checkonly'] = "Веб-сайт";
$arrBrowser['Namecrawler'] = "Веб-сайт";
$arrBrowser['ZyBorg'] = "Веб-сайт";
$arrBrowser['Googlebot'] = "Google Bot";
$arrBrowser['Yandex'] = "Yandex Bot";
$arrBrowser['WebCrawler'] = "Веб-сайт";
$arrBrowser['WebCopier'] = "Веб-сайт";
$arrBrowser['JBH Agent 2.0'] = "Веб-сайт";
///////////// Array end //////////////

$arrbot['BTWebClient'] = "uTorrent RSS";
$arrbot['Googlebot'] = "Google Bot";
$arrbot['Yandex'] = "Yandex Bot";
$arrbot['Yahoo'] = "Yahoo Bot";
$arrbot['search.msn.com/msnbot.htm'] = "MSN Bot";
$arrbot['Rambler'] = "Rambler Bot";

$arrbot['Mail.Ru/1.0'] = "Mail.Ru";


$arrbot['SetLinks'] = "SetLinks Bot";
$arrbot['IEMB3; mlbot'] = "MainLink Bot";
$arrbot['mlbot'] = "MainLink Bot";

$arrbot['DotBot/1.1'] = "Dotnetdotcom.org";
$arrbot['Feedfetcher-Google'] = "Google Feed";
$arrbot['Subscribe.Ru/1.0'] = "Subscribe.Ru";
$arrbot['Nigma.ru/3.0'] = "Nigma Bot";
$arrbot['lmspider/Nutch'] = "Nuance.com";

$arrbot['MJ12bot/v'] = "Majestic12.co.uk";

$arrbot['Sogou web spider/4.'] = "Sogou.com";

$arrbot['FeedDemon/3.0'] = "Feeddemon.com";

$arrbot['AdsBot-Google'] = "AdsBot-Google";
$arrbot['AdsBot-Google-Mobile'] = "AdsBot-Google-Mobile";

$arrbot['Twiceler-0.'] = "Cuil.com";

$arrbot['R6_FeedFetcher'] = "Radian6.com";
$arrbot['AportWorm'] = "Aport.ru";


function getbot($arrbot,$botagent) {
$default = 'Гость';

foreach($arrbot as $key => $value) {
if (strpos($botagent, $key) !== false) {
$default = $value;
break;
}
}

$botagent['default'] = $default;


if ($default=="Гость")
$default = "<b>".$default."</b>";
else
$default = "<u>".$default."</u>";

return $default;
}



function getSystem($arrSystem,$userAgent) {
$system = 'Неизвестна';
foreach($arrSystem as $key => $value) {
if (strpos($userAgent, $key) !== false) {
$system = $value;
break;
}
}
return $system;
}

function getBrowser($arrBrowser,$userAgent) {
$version = "";
$browser = 'N/A';
if (($pos = strpos($userAgent, 'Opera')) !== false) {
$browser = 'Opera';
$pos += 6;
if ((($posEnd = strpos($userAgent, ';', $pos)) !== false) || (($posEnd = strpos($userAgent, ' ', $pos)) !== false))
$version = trim(substr($userAgent, $pos, $posEnd - $pos));
}
elseif (($pos = strpos($userAgent, 'MSIE')) !== false) {
$browser = 'Internet Explorer';
$posEnd = strpos($userAgent, ';', $pos);
if ($posEnd !== false) {
$pos += 4;
$version = trim(substr($userAgent, $pos, $posEnd - $pos));
}
}
elseif (((strpos($userAgent, 'Gecko')) !== false) && ((strpos($userAgent, 'Netscape')) === false)) {
$browser = 'Mozilla';
if (($pos = strpos($userAgent, 'rv:')) !== false) {
$posEnd = strpos($userAgent, ')', $pos);
if ($posEnd !== false) {
$pos += 3;
$version = trim(substr($userAgent, $pos, $posEnd - $pos));
}
}
}
elseif ((strpos($userAgent, ' I;') !== false) || (strpos($userAgent, ' U;') !== false) || (strpos($userAgent, ' U ;') !== false) || (strpos($userAgent, ' I)') !== false) || (strpos($userAgent, ' U)') !== false))
{
$browser = 'Netscape Navigator';
if (($pos = strpos($userAgent, 'Netscape6')) !== false) {
$pos += 10;
$version = trim(substr($userAgent, $pos, strlen($userAgent) - $pos));
}
else
{
if (($pos = strpos($userAgent, 'Mozilla/')) !== false) {
if (($posEnd = strpos($userAgent, ' ', $pos)) !== false) {
$pos += 8;
$version = trim(substr($userAgent, $pos, $posEnd - $pos));
}
}
}
}
else
{
foreach($arrBrowser as $key => $value) {
if (strpos($userAgent, $key) !== false) {
$browser = $value;
break;
}
}
}
$userAgentArr['browser'] = $browser;
$userAgentArr['version'] = $version;
return $userAgentArr;
}


function Spy_lang($op){
switch ($op) {

default:
return "Страница не опознана";
break;

case 'adduser.php':
$sd = "Добавление юзера";
break;

case 'admincp.php':
$sd = "Админка";
break;

case 'anatomy.php':
$sd = "анатомия сессии";
break;

case 'bans.php':
$sd = "Баны";
break;

case 'bookmark.php':
$sd = "Торрент в закладки";
break;

case 'bookmarks.php':
$sd = "Закладки";
break;

case 'license.php':
$sd = "Авторские файлы";
break;

case 'browse.php':
case 'showtags.php':
$sd = "Раздачи";
break;

case 'license.php':
$sd = "Авторские файлы";
break;

case 'browseday.php':
$sd = "Торренты за сегодня";
break;

case 'comment.php':
$sd = "Комментарии";
break;
case 'contact.php':
$sd = "Связь";
break;

case 'dump3r.php':
$sd = "База данных, dumper";
break;

case 'report.php':
$sd = "Жалобы";
break;

case 'thanks.php':
$sd = "Говорит спасибо";
break;

case 'delacctadmin.php':
$sd = "Удаление юзера";

break;
case 'takelogin.php':
$sd = "Входит";
break;

case 'takesignup.php':
$sd = "Регистрация";
break;

case 'tracker-chat.php':
case 'shoutbox.php':
case 'online.php':
$sd = "Чат";
break;

case 'takeprofedit.php':
case 'my.php':
$sd = "Панель управления";
break;

case 'adduser.php':
$sd = "Добавляет Юзера";
break;

case 'delacctadmin.php':
$sd = "Удаление юзера";
break;

case 'changeusername.php':
$sd = "Смена имени";
break;

case 'check_signup.php':
$sd = "Регистрация (ввод данных)";
break;

case 'details.php':
$sd = "Детали торрента";
break;

case 'docleanup.php':
$sd = "Очистка трекера";
break;

case 'download.php':
$sd = "Скачивает торрент";
break;

case 'edit.php':
$sd = "Редактирование торрента";
break;
case 'faq.php':
$sd = "ЧаВо";
break;

case 'modtask.php':
$sd = "Редактирование пользователя";
break;

case 'monitoring.php':
$sd = "Просмотр мониторинга";
break;

case 'random.php':
$sd = "Пред/След/Случ торрент";
break;

case 'sqlerror.php':
$sd = "Просмотр sql ошибок";
break;

case 'statistics.php':
$sd = "Центр Статистики";
break;

case 'tfiles.php':
$sd = "Обменник";
break;

case 'detailsoff':
case 'takeeditoff':
$sd = "Запросы | Предложения";
break;

case 'useragent.php':
$sd = "Браузеры";
break;

case 'commentoff.php':
$sd = "Добавления комента к запросам";
break;

case 'humor.php':
case 'humorall.php':
$sd = "Анекдоты";
break;

case 'repair_tags.php':
$sd = "Процедуры Теги Чистка";
break;

case 'formats.php':
$sd = "Форматы файлов";
break;

case 'info_cache.php':
$sd = "Info лог";
break;

case 'hacklog.php':
$sd = "XSS Атаки лог";
break;

case 'downcheck.php':
$sd = "Синхронизация закачек";
break;

case 'ustatistics.php':
$sd = "Центр индивидуальной статистики";
break;

case 'takeedit.php':
$sd = "Редактирует торрент";
break;

case 'forums.php':
$sd = "Форум";
break;

case 'friends.php':
$sd = "Друзья";
break;

case 'getrss.php':
$sd = "RSS Feed";
break;

case 'rss.php':
$sd = "RSS Feed";
break;

case 'invite.php':
case 'inviteadd.php':
$sd = "Приглашения";
break;

case 'parser_d.php':
case 'parser.php':
$sd = "Парсинг файлов";
break;

case 'remote_gets.php':
$sd = "Обновление пиров";
break;

case 'ipcheck.php':
$sd = "Двойники по IP";
break;

case 'check.php':
$sd = "Одобрение торрента";
break;

//   case 'poll.core.php':
//   $sd = "Голосует";
//  break;

case 'log.php':
$sd = "Логи сайта";
break;

case 'login.php':
$sd = "Авторизация";
break;

case 'logout.php':
$sd = "Выход с сайта";
break;

case 'makepoll.php':
$sd = "Создание опроса";
break;

case 'message.php':
$sd = "Личный ящик";
break;

case 'adminbookmark.php':
$sd = "Закладки админа";
break;

case 'sendbonus.php':
$sd = "Посылка бонусов";
break;

case 'my.php':
case 'getCities.php':
$sd = "Личная панель";
break;

case 'mybonus.php':
$sd = "Мои бонусы";
break;

case 'myinvite.php':
$sd = "Мои инвайты";
break;

case 'mysimpaty.php':
$sd = "Мои респекты";
break;

case 'mytorrents.php':
$sd = "Мои раздачи";
break;

case 'news.php':
$sd = "Добавление новостей";
break;

case 'nowarn.php':
$sd = "Снятие предупреждений";
break;

case 'offers.php':
$sd = "Предложения";
break;

case '0nline.php':
$sd = "Кто онлайн";
break;

case 'polloverview.php':
$sd = "Обзор опросов";
break;

case 'polls.php':
$sd = "Опросы";
break;

case 'recover.php':
$sd = "Восстановление пароля";
break;

case 'restoreclass.php':
$sd = "Восстановление класса";
break;

case 'rules.php':
case 'rule.php':
$sd = "Правила сайта";
break;

case 'setclass.php':
$sd = "смена класса";
break;

case 'simpaty.php':
$sd = "Сказать спасибо";
break;

case 'sitestat.php':
$sd = "Статистика сайта";
break;

case 'stats.php':
$sd = "Статистика сайта";
break;

case 'smilies.php':
$sd = "Смайлы";
break;

case 'votes.php':
$sd = "Просмотр оценок";
break;

case 'staff.php':
$sd = "Администрация";
break;

case 'staffmess.php':
$sd = "Массовое лс";
break;

case 'subnet.php':
$sd = "Соседи";
break;

case 'tags.php':
$sd = "bb-теги";
break;

case 'testip.php':
$sd = "Проверка IP";
break;

case 'testport.php':
$sd = "Тест портов";
break;

case 'thanks.php':
$sd = "Благодарит";
break;

case 'topten.php':
$sd = "Топ 10";
break;

case 'unco.php':
$sd = "Неподтв. юзеры";
break;

case 'upload.php':
case 'uploadnext.php':
$sd = "Загрузка торрента";
break;

case 'premod.php':
$sd = "Модерация торентов";
break;

case 'groups.php':
case 'groupsmess.php':
$sd = "Группы";
break;

case 'parser.php':
$sd = "Парсит ссылки";
break;

case 'uploaders.php':
$sd = "Список аплоадеров";
break;

case 'userdetails.php':
$sd = "Профайл юзера";
break;

case 'userhistory.php':
$sd = "История постов";
break;

case 'checkcomm.php':
$sd = "Лист подписки";
break;

case 'mysql_stats.php':
$sd = "Статистика запросов mysql";
break;

case 'torrentshistory.php':
$sd = "История торрентов";
break;

case 'users.php':
$sd = "Список пользователей";
break;
case 'usersearch.php':
$sd = "Поиск пользователей";
break;

case 'videoformats.php':
$sd = "Форматы видео";
break;
case 'blackjack.php':
$sd = "Игра БлекДжек";
break;
case 'viewoffers.php':
$sd = "Предложения";
break;
case 'viewrequests.php':
$sd = "Запросы";
break;
case 'votesview.php':
$sd = "Голосования";
break;
case 'warned.php':
$sd = "Предупрежденные юзеры";
break;

case 'takeupload.php':
case 'takeuploadnext.php':
$sd = "Продолжение заливки";
break;

case 'takeedit.php':
$sd = "Продолжение редактирования";
break;

case 'newsoverview.php':
$sd = "Просмотр новостей";
break;

case 'polloverview.php':
$sd = "Просмотр опросов";
break;

case "signup.php":
$sd = "Регистрации";
break;

case 'index.php':
$sd = "Главная страница";
break;

case '500.php':
$sd = "Error 500";
break;

case '404.php':
$sd = "404 Not Found";
break;

case '403.php':
$sd = "Доступ запрещен";
break;

case 'proxy.php':
$sd = "Запрет ч.з прокси";
break;

case 'msg.php':
$sd = "Просматривает сообщения";
break;

case 'pollcomment.php':
$sd = "Доб сообщения к опросам";
break;

case 'newscomment.php':
$sd = "Доб сообщения к новостям";
break;

case 'ok.php':
$sd = "Активация аккаунта";
break;

case 'gettorrentdetails.php':
$sd = "Предпросмотр торрента";
break;

case 'sqlerror.php':
$sd = "Sql ошибки";
break;
//имя не найдено?
default:
$sd = "Главная страница";
}
return $sd;
}


dbconn();
loggedinorreturn();




//sql_query("ALTER TABLE `sessions` ADD `host` VARCHAR( 255 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL AFTER `url`");

//sql_query("ALTER TABLE `sessions` CHANGE `time` `time` INT( 11 ) NOT NULL DEFAULT '0'");

$id = (!empty($_GET["view"]) ? "1":"0");


if ($id==0) {
stdhead("Где пользователь");
$pagview = "?";
$linkview = "[<a href=\"0nline.php?view=1&\" class=\"altlink_white\">Расширенный режим показа</a>]";
$per_list = 50;
} else {
stdheadchat("Где пользователь");
$pagview = "?view=1&page=".$page;
$linkview = "[<a href=\"0nline.php?view=0&\" class=\"altlink_white\">Обычный режим показа</a>]";
$per_list = 100;
}

if (get_user_class() < UC_ADMINISTRATOR) {
attacks_log('0nline'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}


$secs = 10 * 60;//Время выборки (10 последних минут)
//$dt = time() - $secs;
$dt = sqlesc(get_date_time(gmtime() - $secs));
$res = sql_query("SELECT COUNT(*) FROM sessions WHERE time > ".$dt);
$row = mysql_fetch_array($res);
$count = $row[0];



list($pagertop, $pagerbottom, $limit) = pager($per_list, $count, "0nline.php".$pagview);

$res = sql_query("SELECT s.url, s.host, s.sid, s.uid,s.username, s.class, s.ip, s.useragent, s.time, (SELECT COUNT(*)FROM sessions WHERE useragent=s.useragent AND ip=s.ip) AS col
FROM sessions AS s
WHERE s.time > ".$dt." ORDER BY s.username ASC ".$limit)or sqlerr(__FILE__, __LINE__);

echo "<table class=\"embedded\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">
".$pagertop."
<tr>
<td class=\"colhead\" align=\"center\" colspan=\"4\">Кто где находится (пользователи на сайте) <br>$linkview</td>
</tr>
<tr>
<td class=\"a\" align=\"center\">Данные</td>
<td class=\"a\" width=\"25%\" align=\"center\">Время</td>
<td class=\"a\" width=\"25%\" align=\"center\">Просматривает / Домен</td>
</tr>";

if (empty($count))
echo "<tr><td class=\"index\" colspan=\"3\">".$tracker_lang['nothing_found']."</td></tr>\n";


$i=20;


while ($row = mysql_fetch_array($res)) {

$spy_url = $row["url"];
$user_sid = $row["sid"];
$user_id = $row["uid"];
$user_name = $row["username"];
$user_class = $row["class"];
$user_ip = $row["ip"];
$user_agent = $row["useragent"];
$user_time = $row["time"];
$col_g = $row["col"];
$host = $row["host"];

$spy_urlse = basename($spy_url);
$res_list = explode(".php", $spy_urlse);

$brawser = getBrowser($arrBrowser,$user_agent);
$bot = getbot($arrbot,$user_agent);
$read = "";

if($CURUSER['id'] == $user_id){
$read = "[<font color=\"blue\">Вы тут</font>]";
}


$slep = "<div class=\"spoiler-wrap\" id=\"".round($i+$i*($i/4*66))."\"><div class=\"spoiler-head folded clickable\">Подробнее о сессии</div>
<div class=\"spoiler-body\" style=\"display: none;\">
<b>Права</b>: ".($user_class <>-1 ? "<a title=\"".$user_ip."\"><b><font color=\"#".get_user_rgbcolor($user_class, $user_name)."\">".get_user_class_name($user_class)."</b></font></a>":$bot)."<br>
<b>Браузер</b>: ".$brawser['browser']." <br>
<b>Ос</b>: ".getSystem($arrSystem,$user_agent)."<br>
<b>IP</b>: <a target='_blank' href=\"".$DEFAULTBASEURL."/usersearch.php?ip=".$user_ip."\">". $user_ip."</a><br>
<b>Сессия</b>: ".$user_sid."<br>
<b>Детально</b>: ".$user_agent."<br>
<b>Страничка</b>: ".($spy_url)."
</div>
</div>";


if($user_class <> -1)
echo "<tr><td ".($i%2==0 ? "class=\"b\"":"class=\"a\"")."><a target='_blank' href=\"userdetails.php?id=".$user_id."\">".get_user_class_color($user_class, $user_name)."</a> 
  ".$slep."</td>";
else
echo "<tr><td ".($i%2==0 ? "class=\"b\"":"class=\"a\"").">".$bot." <a target='_blank' href=\"http://htmlweb.ru/analiz/whois_ip.php?ip=".$user_ip."\">$user_ip</a> ".($col_g ? "(".$col_g.") ":"")."".$slep."</td>";

echo "<td ".($i%2==0 ? "class=\"a\"":"class=\"b\"")." align=\"center\">".($user_time)."</td>";


$host = str_replace("www.muz-tracker.net", "<font color=\"blue\">www.muz-tracker.net</font>", $host);
$host = str_replace("www.muz-trackers.ru", "<font color=\"red\">www.muz-trackers.ru</font>", $host);

		
echo "<td width=\"25%\" ".($i%2==0 ? "class=\"b\"":"class=\"a\"").">";
echo "<a target='_blank' href=\"".$spy_url."\">".Spy_lang($res_list[0].".php")."</a> ".$read."<br><div align=\"right\">".$host."</div>";
echo "</td>";

echo "</tr>";

unset($spy_url, $user_sid, $user_id, $user_name, $user_class, $user_ip, $user_agent, $user_time,$col_g);
++$i;
}

echo "</table>";
echo $pagerbottom;

if ($id==0)
stdfoot();
else
stdfootchat();

?>