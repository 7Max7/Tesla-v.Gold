<?


$month[0]="-";
$month[1]="������";
$month[2]="�������";
$month[3]="�����";
$month[4]="������";
$month[5]="����";
$month[6]="����";
$month[7]="����";
$month[8]="�������";
$month[9]="��������";
$month[10]="�������";
$month[11]="������";
$month[12]="�������";

$week[0]="�����������";
$week[1]="�����������";
$week[2]="�������";
$week[3]="�����";
$week[4]="�������";
$week[5]="�������";
$week[6]="�������";

$gisett=(int)date("w");
$mesnum=(int)date("m");

echo $week[$gisett]." ".date("d")." ".$month[$mesnum]." ".date("Y");

?>


