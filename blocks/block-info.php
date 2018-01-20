<?
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}
$blocktitle = "����� ���� ���������. ���������� ��� ������� (������ ������������).";


$cacheStatFile = "cache/block-info.txt"; 
$expire = 60*60; // 1 ���
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{

global $CURUSER, $SITENAME,$maxusers,$use_email_act;

$registered = (get_row_count("users"));

$torrents = number_format(get_row_count("torrents"));	
$torrents_free = number_format(get_row_count("torrents","WHERE free='yes'"));	
$multitracker = number_format(get_row_count("torrents","WHERE multitracker='yes'"));	

$tags = number_format(get_row_count("tags"));	
$category = number_format(get_row_count("categories"));
$file = number_format(get_row_count("files"));
$humor = number_format(get_row_count("humor"));

$free_slot=$maxusers-$registered;
$registered=number_format($registered);


$content.= "<font face=\"Georgia\" style=\"font-size: 15px;\">
<b>�� ������ ������ � ���</b>: <br>
<b>~</b> ����� <b>$registered</b> ������������������ �������������;<br>
<b>~</b> ����� <b>$torrents</b> ������� ������ (�� ��� ������� - <b>$torrents_free</b> �������, <u>�� ����������� ��������</u>, � <b>$multitracker</b> ��������������� - �������� ���������� �������������� <u>���� ����� ���������� ������������ ������</u>);<br>
<b>~</b> ����� <b>$tags</b> ����� (�����) ��� ������� � �������� ������ ������ � �������;<br>
<b>~</b> ����� <b>$category</b> �������� ��� ��������� ������;<br>
<b>~</b> ����� <b>$file</b> ������ � <b>$torrents</b> ���������;<br>
<b>~</b> ����� <b>$humor</b> <a href=\"humorall.php\">���������</a> ��� �������� ���������;<br>
<b>~</b> ����� <b>$free_slot</b> ��������� ���� ��� ����� ���������;<br>
<center>
<br>�� ������� �����, �� ���� ���� <b>����������</b>. ��� ���� ��������� ������������� ��� ����������� �����, ����� � �� ������ ����� ��. ����� ����������� �������� <a href=\"signup.php\">�����</a> (����������� ��������� ���������".($use_email_act=="0" ? ", �� ����� �� <b>��� ������������ �� �����</b>":"").").<br>
� ���������, ������� ����� <b>$SITENAME</b>.</center>
</font>";


    $fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
 
 if (get_user_class() >= UC_SYSOP)
{
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
}


?>
