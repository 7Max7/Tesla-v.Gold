<?
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}
global $CURUSER ,$tracker_lang;


$blocktitle = $tracker_lang['poll']." - ".(get_user_class() >= UC_MODERATOR ? "[<a class=\"altlink_white\" href=\"makepoll.php\"><b>".$tracker_lang['create']."</b></a>] - " : "")."[<a class=\"altlink_white\" href=\"polls.php\"><b>Прошлые опросы</b></a>]";

if ($CURUSER)
{
?>
<script type="text/javascript" src="js/poll.core.js"></script>

<script type="text/javascript">
$(document).ready(
function(){
loadpoll();
}
);
</script>
<?

if (fuckIE()==false){

 $content.="
<table width=\"100%\" class=\"main\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">
<tr>
<td class=\"text\" align=\"center\">
<div id=\"poll_container\">
<div id=\"loading_poll\" style=\"display:none\"></div>
<noscript>
<b>Пожалуйста включите показ скриптов</b>
</noscript>
</div>
<br/>
</td>
</tr>
</table> ";
}
else

$content="<center>Пожалуйста, поменяйте браузер, Internet Explorer не стабильно работает с этим сайтом.</center>"; 


}
else
{
$content .= "<table class=\"main\" align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\"><tr><td class=\"text\">";
$content .= "<div align=\"center\"><h3>Для гостя опросы отключены</h3></div>\n";
$content .= "</td></tr></table>";
}

?>