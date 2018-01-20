<?php
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}


global $CURUSER;
/*echo("<script type=\"text/javascript\" src=js/blink.js></script>"
 );
*/
$h = date('H'); // ��������� ���
if (($h > 6 )&&($h < 12)) {$view_time="������ ����";}
if (($h >= 12 )&&($h < 18)) {$view_time="������ ����";}
if (($h >= 18 )&&($h <= 23)) {$view_time="������ �����";}
if (($h >= 00 )&&($h <= 6)) {$view_time="������ ����";}

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
$expire = 24*60*60; // 60 ����� �� ���, ����� ���������� 

////////// ���� �� xss
$cachesql = "cache/hacklog.txt";
if (!file_exists($cachesql)){
$view_sql="<a href=\"hacklog.php\">XSS</a>";
}
elseif ((time() - $expire) <= filemtime($cachesql) && filesize($cachesql)<>0){
$view_sql="<a href=\"hacklog.php\"><span style=\"color: red;\" title=\"����� ������ (����� ��� �����)\">XSS</span></a>";
}
else {
$view_sql="<a href=\"hacklog.php\"><span style=\"color: green;\" title=\"���������� ���� (� ������� ��� �� ��� �������� ��� ����� ����)\">XSS</span></a>";
}
////////// ���� �� xss

////////// ������� ������� �� info
$cacheinfo = "cache/info_cache_stat.txt";
if (!file_exists($cacheinfo)){
$view_info="<a href=\"info_cache.php\">Info</a>";
}
elseif ((time() - $expire) <= filemtime($cacheinfo) && filesize($cacheinfo)<>0){
$view_info="<a href=\"info_cache.php\"><span style=\"color: red;\" title=\"����� ������ (����� ��� �����)\">Info</span></a>";
}
else {
$view_info="<a href=\"info_cache.php\"><span style=\"color: green;\" title=\"���������� ���� (� ������� ��� �� ��� �������� ��� ����� ����)\">Info</span></a>";
}
////////// ������� ������� �� info


////////// ������ mysql
$cacheerror = "cache/sqlerror.txt";
if (!file_exists($cacheerror)){
$view_error_sql="<a href=\"sqlerror.php\">SQL</a>";
}
elseif ((time() - $expire) <= filemtime($cacheerror) && filesize($cacheerror)<>0){
$view_error_sql="<a href=\"sqlerror.php\"><span style=\"color: red;\" title=\"����� ������ (����� ��� �����)\">SQL</span></a>";
}
else {
$view_error_sql="<a href=\"sqlerror.php\"><span style=\"color: green;\" title=\"���������� ���� (� ������� ��� �� ��� �������� ��� ����� ����)\">SQL</span></a>";
}
////////// ������ mysql

////////// ������ ��������
$cachebit = "cache/error_torrent.txt";
if (!file_exists($cachebit)){
$view_bit="<a href=\"error_torrent.php\">Bit</a>";
}
elseif ((time() - $expire) <= filemtime($cachebit) && filesize($cachebit)<>0){
$view_bit="<a href=\"error_torrent.php\"><span style=\"color: red;\" title=\"����� ������ (����� ��� �����)\">Bit</span></a>";
}
else {
$view_bit="<a href=\"error_torrent.php\"><span style=\"color: green;\" title=\"���������� ���� (� ������� ��� �� ��� �������� ��� ����� ����)\">Bit</span></a>";
}
////////// ������ ��������

$content.= "<b><font color=\"#0000FF\">-=����=-</font></b><br>";

$content.= "<a href=\"admincp.php\">�������</a> : <a href=\"admincp.php?op=BlocksAdmin\">�����</a><br>";
$content.= "<a href=\"admincp.php?op=iUsers\">��c</a> : <a href=\"changeusername.php\">���</a> : <a href=\"changeuserid.php\">��</a><br>";
$content.= "<a href=\"friensblockadd.php\">������</a> : <a href=\"groups.php\">������</a> <br>";

if ($CURUSER["username"]=="��" || $CURUSER["username"]=="7Max7" || $CURUSER["id"]=="2") {
$content.= "<a href=\"dump3r.php\">���� ������</a><br>";
}

$content.= "<a href=\"cheaters.php\">������</a><br>";
$content.= "$view_sql:$view_info:$view_error_sql:$view_bit<br>";
$content.= "<a href=\"tagscheck.php\">�������</a> : <a href=\"repair_tags.php\">������</a><br>";
$content.= "<a href=\"unco.php\">�������.�����</a><br>";
$content.= "<a href=\"bans.php\">��� IP</a> : <a href=\"bannedemails.php\">�����</a><br>";
$content.= "<a href=\"category.php\">���������</a><br>";
$content.= "<a href=\"sitemap_parts.php\">Sitemap</a><br>";
}



if ($CURUSER["class"] >= UC_ADMINISTRATOR) {
$content.= "<b><font color=\"green\">-=�������������=-</font></b><br>";
$content.= "<a href=\"0nline.php\">������</a> : <a href=\"useragent.php\">�����</a><br>";
$content.= "<a href=\"adduser.php\">�������� �����</a><br>";
$content.= "<a href=\"msg.php\">���������</a><br>";

$content.= "<a title=\"����� ���������� ����������\" href=\"statistics.php\">�����</a> : <a title=\"����� �������������� ����������\" href=\"ustatistics.php\">�������</a><br>";
$content.= "<a href=\"maxlogin.php\">�����</a><br>";
$content.= "<a href=\"redirect.php\">��������</a> : <a href=\"referer.php\">��������</a><br>";
$content.= "<a href=\"adminbookmark.php\">�������������</a><br>";
$content.= "<a href=\"downcheck.php\">C������������</a><br>";

$report = number_format(get_row_count("report"));
if (!empty($report))
$content.= "<a title=\"���������� ����� $report\" href=\"viewreport.php\">������ (".$report.")</a><br>";

$content.= "<a href=\"warned.php\">�������.</a> : <a href=\"hiderating.php\">�������.</a><br>";
$content.= "�������� <a href=\"staffmess.php\">��</a> : <a href=\"groupsmess.php\">������</a></a><br>";
$content.= "<a href=\"testip.php\">�������� IP </a> : <a href=\"testport.php\">�����</a><br>";
}


if ($CURUSER["class"] >= UC_MODERATOR) {
$content.= "<b><font color=\"red\">-=���������=-</font></b><br>";
$content.= "<a href=\"usersearch.php\">����� �����</a><br>";

if ($CURUSER['override_class'] <> 255) {
$content .= "<a href=\"$DEFAULTBASEURL/restoreclass.php\">���c�������� �����</a><br>";
}
else
{
$content.= "<a href=\"setclass.php\">���� ����� ������</a><br>";
}
$content.= "<a href=\"staff.php?act=last\">����� �����</a><br>";
$content.= "<a href=\"users.php\">�����</a> : <a href=\"premod.php\">������</a><br>";
$content.= "<a href=\"antichiters.php\">��������</a> : <a href=\"seeders.php\">���-��</a><br>";
$content.= "<a href=\"smilies.php\">������</a> : <a href=\"tags.php\">����</a><br>";
$content.= "<a href=\"ipcheck.php\">������� IP</a> : <a href=\"emailcheck.php\">����</a><br>";
$content.= "<a href=\"stats.php\">�����-��</a> : <a href=\"uploaders.php\">��������</a><br>";
$content.= "<a href=\"topten.php\">TOP10</a> : <a href=\"votes.php\">������</a><br>";
$content.= "<a href=\"file_descr.php\">��������</a><br>";
//$content.= "<a href=\"remote_gets.php\">�������� ����</a><br>";
}

$content.= "<a href=\"license.php\">��������� �������</a><br>";


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