<?
require_once("include/bittorrent.php");

//// �������� �� �����.!!!


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
$arrBrowser['ia_archiver'] = "���-����";
$arrBrowser['ArchitextSpider'] = "���-����";
$arrBrowser['Lycos_Spider_(T-Rex)'] = "���-����";
$arrBrowser['Scooter'] = "���-����";
$arrBrowser['InfoSeek'] = "���-����";
$arrBrowser['AltaVista'] = "���-����";
$arrBrowser['Eule-Robot'] = "���-����";
$arrBrowser['SwissSearch'] = "���-����";
$arrBrowser['Checkbot'] = "���-����";
$arrBrowser['Crescent Internet ToolPak'] = "���-����";
$arrBrowser['Slurp'] = "���-����";
$arrBrowser['WiseWire-Widow'] = "���-����";
$arrBrowser['NetAttache'] = "���-����";
$arrBrowser['Web21 CustomCrawl'] = "���-����";
$arrBrowser['BTWebClient'] = "uTorrent RSS";
$arrBrowser['CheckUrl'] = "���-����";
$arrBrowser['LinkLint-checkonly'] = "���-����";
$arrBrowser['Namecrawler'] = "���-����";
$arrBrowser['ZyBorg'] = "���-����";
$arrBrowser['Googlebot'] = "Google Bot";
$arrBrowser['Yandex'] = "Yandex Bot";
$arrBrowser['WebCrawler'] = "���-����";
$arrBrowser['WebCopier'] = "���-����";
$arrBrowser['JBH Agent 2.0'] = "���-����";
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
$default = '�����';

foreach($arrbot as $key => $value) {
if (strpos($botagent, $key) !== false) {
$default = $value;
break;
}
}

$botagent['default'] = $default;


if ($default=="�����")
$default = "<b>".$default."</b>";
else
$default = "<u>".$default."</u>";

return $default;
}



function getSystem($arrSystem,$userAgent) {
$system = '����������';
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
return "�������� �� ��������";
break;

case 'adduser.php':
$sd = "���������� �����";
break;

case 'admincp.php':
$sd = "�������";
break;

case 'anatomy.php':
$sd = "�������� ������";
break;

case 'bans.php':
$sd = "����";
break;

case 'bookmark.php':
$sd = "������� � ��������";
break;

case 'bookmarks.php':
$sd = "��������";
break;

case 'license.php':
$sd = "��������� �����";
break;

case 'browse.php':
case 'showtags.php':
$sd = "�������";
break;

case 'license.php':
$sd = "��������� �����";
break;

case 'browseday.php':
$sd = "�������� �� �������";
break;

case 'comment.php':
$sd = "�����������";
break;
case 'contact.php':
$sd = "�����";
break;

case 'dump3r.php':
$sd = "���� ������, dumper";
break;

case 'report.php':
$sd = "������";
break;

case 'thanks.php':
$sd = "������� �������";
break;

case 'delacctadmin.php':
$sd = "�������� �����";

break;
case 'takelogin.php':
$sd = "������";
break;

case 'takesignup.php':
$sd = "�����������";
break;

case 'tracker-chat.php':
case 'shoutbox.php':
case 'online.php':
$sd = "���";
break;

case 'takeprofedit.php':
case 'my.php':
$sd = "������ ����������";
break;

case 'adduser.php':
$sd = "��������� �����";
break;

case 'delacctadmin.php':
$sd = "�������� �����";
break;

case 'changeusername.php':
$sd = "����� �����";
break;

case 'check_signup.php':
$sd = "����������� (���� ������)";
break;

case 'details.php':
$sd = "������ ��������";
break;

case 'docleanup.php':
$sd = "������� �������";
break;

case 'download.php':
$sd = "��������� �������";
break;

case 'edit.php':
$sd = "�������������� ��������";
break;
case 'faq.php':
$sd = "����";
break;

case 'modtask.php':
$sd = "�������������� ������������";
break;

case 'monitoring.php':
$sd = "�������� �����������";
break;

case 'random.php':
$sd = "����/����/���� �������";
break;

case 'sqlerror.php':
$sd = "�������� sql ������";
break;

case 'statistics.php':
$sd = "����� ����������";
break;

case 'tfiles.php':
$sd = "��������";
break;

case 'detailsoff':
case 'takeeditoff':
$sd = "������� | �����������";
break;

case 'useragent.php':
$sd = "��������";
break;

case 'commentoff.php':
$sd = "���������� ������� � ��������";
break;

case 'humor.php':
case 'humorall.php':
$sd = "��������";
break;

case 'repair_tags.php':
$sd = "��������� ���� ������";
break;

case 'formats.php':
$sd = "������� ������";
break;

case 'info_cache.php':
$sd = "Info ���";
break;

case 'hacklog.php':
$sd = "XSS ����� ���";
break;

case 'downcheck.php':
$sd = "������������� �������";
break;

case 'ustatistics.php':
$sd = "����� �������������� ����������";
break;

case 'takeedit.php':
$sd = "����������� �������";
break;

case 'forums.php':
$sd = "�����";
break;

case 'friends.php':
$sd = "������";
break;

case 'getrss.php':
$sd = "RSS Feed";
break;

case 'rss.php':
$sd = "RSS Feed";
break;

case 'invite.php':
case 'inviteadd.php':
$sd = "�����������";
break;

case 'parser_d.php':
case 'parser.php':
$sd = "������� ������";
break;

case 'remote_gets.php':
$sd = "���������� �����";
break;

case 'ipcheck.php':
$sd = "�������� �� IP";
break;

case 'check.php':
$sd = "��������� ��������";
break;

//   case 'poll.core.php':
//   $sd = "��������";
//  break;

case 'log.php':
$sd = "���� �����";
break;

case 'login.php':
$sd = "�����������";
break;

case 'logout.php':
$sd = "����� � �����";
break;

case 'makepoll.php':
$sd = "�������� ������";
break;

case 'message.php':
$sd = "������ ����";
break;

case 'adminbookmark.php':
$sd = "�������� ������";
break;

case 'sendbonus.php':
$sd = "������� �������";
break;

case 'my.php':
case 'getCities.php':
$sd = "������ ������";
break;

case 'mybonus.php':
$sd = "��� ������";
break;

case 'myinvite.php':
$sd = "��� �������";
break;

case 'mysimpaty.php':
$sd = "��� ��������";
break;

case 'mytorrents.php':
$sd = "��� �������";
break;

case 'news.php':
$sd = "���������� ��������";
break;

case 'nowarn.php':
$sd = "������ ��������������";
break;

case 'offers.php':
$sd = "�����������";
break;

case '0nline.php':
$sd = "��� ������";
break;

case 'polloverview.php':
$sd = "����� �������";
break;

case 'polls.php':
$sd = "������";
break;

case 'recover.php':
$sd = "�������������� ������";
break;

case 'restoreclass.php':
$sd = "�������������� ������";
break;

case 'rules.php':
case 'rule.php':
$sd = "������� �����";
break;

case 'setclass.php':
$sd = "����� ������";
break;

case 'simpaty.php':
$sd = "������� �������";
break;

case 'sitestat.php':
$sd = "���������� �����";
break;

case 'stats.php':
$sd = "���������� �����";
break;

case 'smilies.php':
$sd = "������";
break;

case 'votes.php':
$sd = "�������� ������";
break;

case 'staff.php':
$sd = "�������������";
break;

case 'staffmess.php':
$sd = "�������� ��";
break;

case 'subnet.php':
$sd = "������";
break;

case 'tags.php':
$sd = "bb-����";
break;

case 'testip.php':
$sd = "�������� IP";
break;

case 'testport.php':
$sd = "���� ������";
break;

case 'thanks.php':
$sd = "����������";
break;

case 'topten.php':
$sd = "��� 10";
break;

case 'unco.php':
$sd = "�������. �����";
break;

case 'upload.php':
case 'uploadnext.php':
$sd = "�������� ��������";
break;

case 'premod.php':
$sd = "��������� ��������";
break;

case 'groups.php':
case 'groupsmess.php':
$sd = "������";
break;

case 'parser.php':
$sd = "������ ������";
break;

case 'uploaders.php':
$sd = "������ ����������";
break;

case 'userdetails.php':
$sd = "������� �����";
break;

case 'userhistory.php':
$sd = "������� ������";
break;

case 'checkcomm.php':
$sd = "���� ��������";
break;

case 'mysql_stats.php':
$sd = "���������� �������� mysql";
break;

case 'torrentshistory.php':
$sd = "������� ���������";
break;

case 'users.php':
$sd = "������ �������������";
break;
case 'usersearch.php':
$sd = "����� �������������";
break;

case 'videoformats.php':
$sd = "������� �����";
break;
case 'blackjack.php':
$sd = "���� ��������";
break;
case 'viewoffers.php':
$sd = "�����������";
break;
case 'viewrequests.php':
$sd = "�������";
break;
case 'votesview.php':
$sd = "�����������";
break;
case 'warned.php':
$sd = "��������������� �����";
break;

case 'takeupload.php':
case 'takeuploadnext.php':
$sd = "����������� �������";
break;

case 'takeedit.php':
$sd = "����������� ��������������";
break;

case 'newsoverview.php':
$sd = "�������� ��������";
break;

case 'polloverview.php':
$sd = "�������� �������";
break;

case "signup.php":
$sd = "�����������";
break;

case 'index.php':
$sd = "������� ��������";
break;

case '500.php':
$sd = "Error 500";
break;

case '404.php':
$sd = "404 Not Found";
break;

case '403.php':
$sd = "������ ��������";
break;

case 'proxy.php':
$sd = "������ �.� ������";
break;

case 'msg.php':
$sd = "������������� ���������";
break;

case 'pollcomment.php':
$sd = "��� ��������� � �������";
break;

case 'newscomment.php':
$sd = "��� ��������� � ��������";
break;

case 'ok.php':
$sd = "��������� ��������";
break;

case 'gettorrentdetails.php':
$sd = "������������ ��������";
break;

case 'sqlerror.php':
$sd = "Sql ������";
break;
//��� �� �������?
default:
$sd = "������� ��������";
}
return $sd;
}


dbconn();
loggedinorreturn();




//sql_query("ALTER TABLE `sessions` ADD `host` VARCHAR( 255 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL AFTER `url`");

//sql_query("ALTER TABLE `sessions` CHANGE `time` `time` INT( 11 ) NOT NULL DEFAULT '0'");

$id = (!empty($_GET["view"]) ? "1":"0");


if ($id==0) {
stdhead("��� ������������");
$pagview = "?";
$linkview = "[<a href=\"0nline.php?view=1&\" class=\"altlink_white\">����������� ����� ������</a>]";
$per_list = 50;
} else {
stdheadchat("��� ������������");
$pagview = "?view=1&page=".$page;
$linkview = "[<a href=\"0nline.php?view=0&\" class=\"altlink_white\">������� ����� ������</a>]";
$per_list = 100;
}

if (get_user_class() < UC_ADMINISTRATOR) {
attacks_log('0nline'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}


$secs = 10 * 60;//����� ������� (10 ��������� �����)
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
<td class=\"colhead\" align=\"center\" colspan=\"4\">��� ��� ��������� (������������ �� �����) <br>$linkview</td>
</tr>
<tr>
<td class=\"a\" align=\"center\">������</td>
<td class=\"a\" width=\"25%\" align=\"center\">�����</td>
<td class=\"a\" width=\"25%\" align=\"center\">������������� / �����</td>
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
$read = "[<font color=\"blue\">�� ���</font>]";
}


$slep = "<div class=\"spoiler-wrap\" id=\"".round($i+$i*($i/4*66))."\"><div class=\"spoiler-head folded clickable\">��������� � ������</div>
<div class=\"spoiler-body\" style=\"display: none;\">
<b>�����</b>: ".($user_class <>-1 ? "<a title=\"".$user_ip."\"><b><font color=\"#".get_user_rgbcolor($user_class, $user_name)."\">".get_user_class_name($user_class)."</b></font></a>":$bot)."<br>
<b>�������</b>: ".$brawser['browser']." <br>
<b>��</b>: ".getSystem($arrSystem,$user_agent)."<br>
<b>IP</b>: <a target='_blank' href=\"".$DEFAULTBASEURL."/usersearch.php?ip=".$user_ip."\">". $user_ip."</a><br>
<b>������</b>: ".$user_sid."<br>
<b>��������</b>: ".$user_agent."<br>
<b>���������</b>: ".($spy_url)."
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