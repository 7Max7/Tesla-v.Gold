<?


$month[0]="-";
$month[1]="Января";
$month[2]="Февраля";
$month[3]="Марта";
$month[4]="Апреля";
$month[5]="Майя";
$month[6]="Июня";
$month[7]="Июля";
$month[8]="Августа";
$month[9]="Сентября";
$month[10]="Октября";
$month[11]="Ноября";
$month[12]="Декабря";

$week[0]="Воскресенье";
$week[1]="Понедельник";
$week[2]="Вторник";
$week[3]="Среда";
$week[4]="Четверг";
$week[5]="Пятница";
$week[6]="Суббота";

$gisett=(int)date("w");
$mesnum=(int)date("m");

echo $week[$gisett]." ".date("d")." ".$month[$mesnum]." ".date("Y");

?>


