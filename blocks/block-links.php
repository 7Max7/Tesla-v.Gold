<?
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}

global $tracker_lang, $CURUSER,$auto_duploader;

$content.="<center>";


if (is_valid_id($auto_duploader) && !empty($auto_duploader)){

if(($CURUSER["class"] == UC_USER || $CURUSER["class"] == UC_POWER_USER) && $CURUSER["promo_time"]<get_date_time(gmtime() - 86400*$auto_duploader)){
///$content.="<span style=\" BORDER:silver 1px solid;  DISPLAY:block;  MARGIN:2px 1px;  PADDING:2px 2px 2px 6px;  TEXT-DECORATION:none;\">������ �������� �����, ������� <a title=\"���� ������� �� ����� ����� ������ ��� ������ ����� ������������.\" href=\"uploadapp.php\">�����</a> ��� ��������� ���� ��������� (����������)</span><br>";

$content.="<a class=\"menu\" href=\"uploadapp.php\"><b>�������� �����</b></a>";
}

}

 
if ($CURUSER["class"] < UC_VIP) {
 $content.="<a class=\"menu\" href=\"uploadapp.php\">������ � ���������</a>";
}

//$content.=" <a class=\"menu\" href=\"support.php\">��� ���������</a>";

if ($CURUSER) 
{
 $content.= "<a class=\"menu\" href=\"blackjack.php\">��������</a>";
}

$content.= "<a class=\"menu\" href=\"humorall.php\">��������</a>";
 
 
$content.= "<a title=\"���� ������ �������� �����, �������, ���� � ���� - ��� ����\" class=\"menu\" href=\"rule.php\">��� ������</a>";


$content.="</center>";
?>