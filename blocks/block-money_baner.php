<?
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}

global $DEFAULTBASEURL;

$now = get_date_time();


$viewmoney = "15$";

$viewalert = "Если желаете купить рекламное место обратитесь к ZimA или 7Max7. Цена одного места ".$viewmoney." в месяц.";

$imgdemo = array(
"timebegin" => "2010-10-23 00:00:00",
"timeend" => "2010-10-25 00:00:00",
"image" => $DEFAULTBASEURL."/pic/show/baner_demo.png",
"url" => ""
);


$img1 = array(
"timebegin" => "2010-10-23 00:00:00",
"timeend" => "2010-10-25 00:00:00",
"image" => "",
"url" => ""
);

$img2 = array(
"timebegin" => "2010-10-23 00:00:00",
"timeend" => "2010-10-25 00:00:00",
"image" => "",
"url" => ""
);



$content = "<table widht=\"100%\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">";
$content.= "<tr>";


$content.= "<td rowspan=\"2\" style=\"width:468px; height:100%\" valign=\"top\" align=\"center\">";

if ($img1["timebegin"] < $now && $img1["timeend"] > $now && !empty($img1["image"])){
//$content.= "<!----1 банер (460*60): ".$img1["url"]." с ".$img1["timebegin"]." по ".$img1["timeend"].">";
$content.= "<a href=\"".$img1["url"]."\"><img title=\"Нажми на меня!\" alt=\"Нажми на меня!\" rel=\"nofollow\" src=\"".$img1["image"]."\"></a>";
unset($img1["image"]);
}
else
$content.= "<img title=\"Место размером 468x60 сдается за ".$viewmoney." в месяц.\" alt=\"Место размером 468x60 сдается за ".$viewmoney." в месяц. Flash не поддерживается.\" style=\"cursor: pointer;\" onclick=\"javascript: alert('".$viewalert."');\" rel=\"nofollow\" src=\"".$imgdemo["image"]."\">";

$content.= "</td>";




$content.= "<td rowspan=\"2\" style=\"width:468px; height:100%\" valign=\"top\" align=\"center\">";

if ($img2["timebegin"] < $now && $img2["timeend"] > $now && !empty($img2["image"])){
//$content.= "<!----2 банер (460*60): ".$img2["url"]." с ".$img2["timebegin"]." по ".$img2["timeend"].">";
$content.= "<a href=\"".$img2["url"]."\"><img title=\"Нажми на меня!\" alt=\"Нажми на меня!\" rel=\"nofollow\" src=\"".$img2["image"]."\"></a>";
unset($img2["image"]);
}
else
$content.= "<img title=\"Место размером 468x60 сдается за ".$viewmoney." в месяц.\" alt=\"Место размером 468x60 сдается за ".$viewmoney." в месяц. Flash не поддерживается.\" style=\"cursor: pointer;\" onclick=\"javascript: alert('".$viewalert."');\" rel=\"nofollow\" src=\"".$imgdemo["image"]."\">";

$content.= "</td>";



$content.= "</tr>";
$content.= "</table>";

$blocktitle = "Денежные банеры (468x60)";

/*
if (empty($img1["image"]) && empty($img2["image"]))
$blocktitle.= " Свободны оба места.";
elseif (empty($img2["image"]) && !empty($img1["image"]) || !empty($img2["image"]) && empty($img1["image"]))
$blocktitle.= " Свободно одно Место.";
*/

?>