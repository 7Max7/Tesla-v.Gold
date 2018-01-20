<?php
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

echo"
<style type=\"text/css\">
<!--
.gallerycontainer{
position: relative;
/*Add a height attribute and set to largest image's height to prevent overlaying*/
}

.thumbnail img{
border: 1px solid white;
margin: 0 5px 5px 0;
}

.thumbnail:hover{
background-color: transparent;
}

.thumbnail:hover img{
border: 1px solid blue;
}

.thumbnail span{ /*CSS for enlarged image*/
position: absolute;
background-color: lightyellow;
padding: 5px;
left: -1000px;
border: 1px dashed gray;
visibility: hidden;
color: black;
text-decoration: none;
}

.thumbnail span img{ /*CSS for enlarged image*/
border-width: 0;
padding: 2px;
}

.thumbnail:hover span{ /*CSS for enlarged image*/
visibility: visible;
top: 0;
left: 165px; /*position where enlarged image should offset horizontally */
z-index: 50;
}  


-->
</style>";


//выбираем сидов
function dltable($arr, $torrent)
{
        global $CURUSER, $tracker_lang;
        $s = "<b>" . count($arr) . " $name</b>\n";
        if (!count($arr))
                return $s;
        $s .= "\n";
        $now = time();
        $moderator = (isset($CURUSER) && get_user_class() >= UC_MODERATOR);
        $mod = get_user_class() >= UC_MODERATOR;
        foreach ($arr as $e) {
                // user/ip/port
                // check if anyone has this ip
                if ($e["username"])
                  $s .= "<a href=\"userdetails.php?id=$e[userid]\"><b>".get_user_class_color($e["class"], $e["username"])."</b></a>".($mod ? "&nbsp;[<span title=\"{$e["ip"]}\" style=\"cursor: pointer\">IP</span>]" : "").",\n";
                else
                  $s .= "" . ($mod ? $e["ip"] : preg_replace('/\.\d+$/', ".xxx", $e["ip"])) . ",\n";
                $secs = max(10, ($e["la"]) - $e["pa"]);
                $revived = $e["revived"] == "yes";
        }
        return $s;
}
//конец отбора
$res1 = sql_query("SELECT COUNT(*) FROM torrents WHERE banned = 'no'");
$row1 = mysql_fetch_array($res1);
$count = $row1[0];
$blocktitle = "Новые поступления".(get_user_class() >= UC_USER ? "<font class=\"small\"> - [<a class=\"altlink\" href=\"upload.php\"><b>Загрузить</b></a>]  </font>" : "<font class=\"small\"> - (торренты без сидов - не отображаются!)</font>");
$content .= "<table cellspacing=\"0\" cellpadding=\"5\" width=\"100%\"><tr><td id=\"centerCcolumn\">";
if (!$count) {
    $content .= "Нет торрентов на трекере...";
} else {
  ///  include "include/codecs.php";
  $video_codec = array(
	1 => "H.263",
	2 => "H.264",
	3 => "VPx",
	4 => "DivX 3.x",
	5 => "DivX 4.x",
	6 => "DivX 5.x",
	7 => "DivX 6.x",
	8 => "XviD",
	9 => "MPEG 1",
	10 => "MPEG 2 SVCD",
	11 => "MPEG 2 DVD",
	12 => "ASF",
	13 => "WMV"

);

$audio_codec = array(
	1 => "MP3",
	2 => "MP3 Pro",
	3 => "AC3",
	4 => "AC3 2.0",
	5 => "AC3 5.1",
	6 => "WMA",
	7 => "AAC",
	8 => "OGG",
	9 => "MP2"
);

$audio_lang = array(
	1 => "Русский",
	2 => "Английский",
	3 => "Украинский",
	4 => "Немецкий",
	5 => "Польский",
	6 => "Китайский",
	7 => "Японский"
);

$audio_trans = array(
	1 => "Без перевода",
	2 => "Дублированный",
	3 => "Профессиональный",
	4 => "Многоголосый закадровый",
	5 => "Двухголосый закадровый",
	6 => "Одноголосый закадровый"
);

$release_quality = array(
	1 => "HD DVD",
	2 => "HDTV",
	3 => "HDTVRip",
	4 => "DVD-9",
	5 => "DVD-5",
	6 => "DVDRip",
	7 => "DVDScr",
	8 => "Scr",
	9 => "SatRip",
	10 => "TVRip",
	11 => "TC",
	12 => "Super-TS",
	13 => "TS",
	14 => "CAM"
);
    $perpage = 10;
    list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] . "?" );
    $res = sql_query("SELECT torrents.*, categories.id AS catid FROM torrents LEFT JOIN categories ON torrents.category = categories.id  WHERE banned = 'no' ORDER BY id DESC $limit") or sqlerr(__FILE__, __LINE__);
    $content .= $pagertop;
    $content .= "</td></tr>";
    while ($release = mysql_fetch_array($res)) {
        $catid = $release["catid"];
        $catname = $release["catname"];
        $catimage = $release["catimage"];
                $torname = $release["name"]; 
        $descr=$release["descr"];
                $uprow = (isset($release["username"]) ? ("<a href=userdetails.php?id=" . $release["owner"] . ">" . htmlspecialchars($release["username"]) . "</a>") : "<i>Скоро сделаем и будет видно :)</i>");
        if (strlen($descr) > 100) 
            $descr = substr($descr, 0, 1000) . "...";

        $content .= "<tr><td>";
        $content .= "<table width=\"100%\" class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">";
        $content .= "<tr>";
        $content .= "<td class=\"colhead\" colspan=\"2\" align=left>";
        $content .= "".htmlspecialchars($torname)."";
                if ($release["free"] == 'yes')
                    $content .= "<img src=pic/freedownload.gif border=0 />";
$content .= "<a href=\"details.php?id=$release[id]\" alt=\"$release[name]\" title=\"$release[name]\"><b> -=[Детали]=-</b></a>";
        $content .= "</font></td>";
        $content .= "</tr>";
if ($release["image1"] != "")
$img1 = "<div class=\"gallerycontainer\"><a class=\"thumbnail\" href=\"torrents/images/$release[image1]\"><img width=\"180\" border='0' src=torrents/images/$release[image1] /><span><img src=torrents/images/$release[image1] /></span></a>";
        $content .= "<tr valign=\"top\"><td align=\"center\" width=\"160\">";
            $content .= "$img1";
        $content .= "</td>";
        $content .= "<td><div align=\"left\">
            ".format_comment($descr)."
            </div></td>";

        $content .= "</tr>";
        $content .= "</table>";
        $content .= "</td></tr>";
    } 
    $content .= "<tr><td>";
    $content .= $pagerbottom;
    $content .= "</td></tr>";
}
$content .= "</table>";
?> 