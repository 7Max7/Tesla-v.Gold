<?
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}

global $DEFAULTBASEURL;

$now = get_date_time();


$viewmoney = "15$";

$viewalert = "���� ������� ������ ��������� ����� ���������� � ZimA ��� 7Max7. ���� ������ ����� ".$viewmoney." � �����.";

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
//$content.= "<!----1 ����� (460*60): ".$img1["url"]." � ".$img1["timebegin"]." �� ".$img1["timeend"].">";
$content.= "<a href=\"".$img1["url"]."\"><img title=\"����� �� ����!\" alt=\"����� �� ����!\" rel=\"nofollow\" src=\"".$img1["image"]."\"></a>";
unset($img1["image"]);
}
else
$content.= "<img title=\"����� �������� 468x60 ������� �� ".$viewmoney." � �����.\" alt=\"����� �������� 468x60 ������� �� ".$viewmoney." � �����. Flash �� ��������������.\" style=\"cursor: pointer;\" onclick=\"javascript: alert('".$viewalert."');\" rel=\"nofollow\" src=\"".$imgdemo["image"]."\">";

$content.= "</td>";




$content.= "<td rowspan=\"2\" style=\"width:468px; height:100%\" valign=\"top\" align=\"center\">";

if ($img2["timebegin"] < $now && $img2["timeend"] > $now && !empty($img2["image"])){
//$content.= "<!----2 ����� (460*60): ".$img2["url"]." � ".$img2["timebegin"]." �� ".$img2["timeend"].">";
$content.= "<a href=\"".$img2["url"]."\"><img title=\"����� �� ����!\" alt=\"����� �� ����!\" rel=\"nofollow\" src=\"".$img2["image"]."\"></a>";
unset($img2["image"]);
}
else
$content.= "<img title=\"����� �������� 468x60 ������� �� ".$viewmoney." � �����.\" alt=\"����� �������� 468x60 ������� �� ".$viewmoney." � �����. Flash �� ��������������.\" style=\"cursor: pointer;\" onclick=\"javascript: alert('".$viewalert."');\" rel=\"nofollow\" src=\"".$imgdemo["image"]."\">";

$content.= "</td>";



$content.= "</tr>";
$content.= "</table>";

$blocktitle = "�������� ������ (468x60)";

/*
if (empty($img1["image"]) && empty($img2["image"]))
$blocktitle.= " �������� ��� �����.";
elseif (empty($img2["image"]) && !empty($img1["image"]) || !empty($img2["image"]) && empty($img1["image"]))
$blocktitle.= " �������� ���� �����.";
*/

?>