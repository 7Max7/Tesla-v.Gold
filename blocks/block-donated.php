<?
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}
global $SITENAME;

$euro = "E779619129333";
$dollar = "Z398040743497";
$rub = "R255605710755";


$content = "<center>
������������ ��� <b><span title=\"������ ��������\" style=\"color:blue\">".$SITENAME."</b></span>
<form id=pay name=pay method=\"POST\" action=\"https://merchant.webmoney.ru/lmi/payment.asp\">
<p><b>����������</b>: <input type=\"text\" size=5 name=\"LMI_PAYMENT_AMOUNT\" value=\"5\"><p>
<p><b>������</b>: <select name=\"LMI_PAYEE_PURSE\">
<option value=\"$euro\">����</option>
<option value=\"$dollar\">������</option>
<option value=\"$rub\">�����</option></p>
<input type=\"hidden\" name=\"LMI_PAYMENT_DESC\" value=\"For Muz-Tracker.Com\">
<input type=\"hidden\" name=\"LMI_PAYMENT_NO\" value=\"0\">
<input type=\"hidden\" name=\"LMI_SIM_MODE\" value=\"0\">

<p>	
	<input type=\"submit\" value=\"������������\">
</p>
</form>
</center>";
$blocktitle = "������������";
?>