<?
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}

global $tracker_lang, $CURUSER,$auto_duploader;

$content.="<center>";


if (is_valid_id($auto_duploader) && !empty($auto_duploader)){

if(($CURUSER["class"] == UC_USER || $CURUSER["class"] == UC_POWER_USER) && $CURUSER["promo_time"]<get_date_time(gmtime() - 86400*$auto_duploader)){
///$content.="<span style=\" BORDER:silver 1px solid;  DISPLAY:block;  MARGIN:2px 1px;  PADDING:2px 2px 2px 6px;  TEXT-DECORATION:none;\">Хотите заливать файлы, Нажмите <a title=\"Если заливок не будет более месяца вам вернут права Пользователя.\" href=\"uploadapp.php\">здесь</a> для получения прав аплоадера (заливающий)</span><br>";

$content.="<a class=\"menu\" href=\"uploadapp.php\"><b>Повысить права</b></a>";
}

}

 
if ($CURUSER["class"] < UC_VIP) {
 $content.="<a class=\"menu\" href=\"uploadapp.php\">Заявка в Аплоудеры</a>";
}

//$content.=" <a class=\"menu\" href=\"support.php\">Тех Поддержка</a>";

if ($CURUSER) 
{
 $content.= "<a class=\"menu\" href=\"blackjack.php\">БлекДжек</a>";
}

$content.= "<a class=\"menu\" href=\"humorall.php\">Анекдоты</a>";
 
 
$content.= "<a title=\"Если хотите заказать права, рейтинг, цвет в нике - вам сюда\" class=\"menu\" href=\"rule.php\">Доп Услуги</a>";


$content.="</center>";
?>