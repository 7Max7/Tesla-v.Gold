<?php

if (!defined('BLOCK_FILE')) {   
 Header("Location: ../index.php");   
 exit;   
}

$blocktitle = "Название";   

?>
<style>

a#prevpromo, a#nextpromo, a#playmain { position: absolute; z-index: 2; }
a.prevPromo, a.nextPromo { display:block; width:23px; height:26px; background:url(../pic/tabs/nav_promo_prev.png) no-repeat; float:left; margin:0px; cursor:pointer;}
a.prevPromo:hover, a.nextPromo:hover, a.play:hover, a.pause:hover  { background-position: 0px -26px; }
a.nextPromo { background-image:url(../pic/tabs/nav_promo_next.png); clear:right; }
a.play, a.pause { display:block; width:22px; height:26px; background:url(../pic/tabs/nav_promo_play.png) no-repeat; float:left; margin:0px; cursor:pointer; font-size:1px;}
a.pause { background-image:url(../pic/tabs/nav_promo_pause.png); }

div#main_scrollable, div#news_scrollable { float:left; }
div#main_scrollable { position:relative; overflow:hidden; width: 918px; height: 200px;}
div#main_scrollable div.items { width:20000em; position:absolute; clear:both; }
div#main_scrollable div.items .item { float:left; width: 918px; height: 200px; }
div#main_scrollable div.image { width: 35%; float: left;}
div#main_scrollable div.context { width: 520px; float: right; padding: 1px 20px 5px;}
div#main_scrollable h2 { font-size: 27px; line-height: 24px; margin: 0px; padding: 0px; font-weight: normal;}
div#main_scrollable div.text { margin-top: 10px; line-height: 15px;}
div#main_scrollable div.detail { text-align: right; font-weight: bold;}
div#main_scrollable div.yellow div.context { color: #000;}
div#main_scrollable div.yellow h2 { color: #005aab;}
div#main_scrollable div.white2 h2 { color: #005aab;}
div#main_scrollable div.gray { background: #EAEAEA;}
div#main_scrollable div.gray h2 { color: #005aab;}
/*div#main_scrollable div.white2 div.context { color: #005aab;}*/
div#main_scrollable div.yellow a { color: #005aab;}

a.prevNews, a.nextNews { display:block; width:8px; height:232px; background:url(../pic/tabs/nav_main_prev.png) no-repeat; float:left; margin:0px 1px; cursor:pointer; font-size:1px; }
a.prevNews:hover, a.nextNews:hover { background-position: 0px -232px; }
a.nextNews { background-image:url(../pic/tabs/nav_main_next.png); clear:right; }

div#news_scrollable { position:relative; overflow:hidden; width: 680px; height:232px; padding: 0; }
div#news_scrollable div.items { width:20000em; position:absolute; clear:both; }
div#news_scrollable div.items .item { float:left; width: 216px; padding: 12px 6px 8px 2px; height: 210px; margin-right: 1px; border: solid 1px #cfcfcf; background: #f6f6f6; }

div#news_scrollable .item img { border-left: solid 4px #ffda1a; padding: 0px 1px; }
div#news_scrollable .item .title { font-size: 11px; font-weight: bold; margin: 0px 10px 16px 12px; }

div#news_scrollable .item .detail { color: #005aab; margin: 10px 14px; }
div#news_scrollable .item  .detail a { padding: 2px; background: #ffd61c; color: #000; text-decoration: none; }

</style>

<script src="../js/jquery.tools.min.js"></script>

<script>
$(function() {
$("div#main_scrollable").scrollable({ size: 1,next: 'a.nextPromo', prev: 'a.prevPromo',interval: 10000,loop: true});
$("div#news_scrollable").scrollable({ size: 3,nextPage: 'a.nextNews', prevPage: 'a.prevNews'});
});
$(document).ready(function(){

$("a#prevpromo").css({left: $('div#main_scrollable').offset().left + 838, top:$('div#main_scrollable').offset().top + 160});
$("a#playmain").css({left: $('div#main_scrollable').offset().left + 861, top:$('div#main_scrollable').offset().top + 160});
$("a#nextpromo").css({left: $('div#main_scrollable').offset().left + 883, top:$('div#main_scrollable').offset().top + 160});

$("a#playmain").click(function(){
if($(this).attr('class') == 'play'){
$(this).removeClass('play');
$(this).addClass('pause');							
} else {
$(this).removeClass('pause');
$(this).addClass('play');
}
return false;
})
});	
$(window).resize(function(){
$("a#prevpromo").css({left: $('div#main_scrollable').offset().left + 838, top:$('div#main_scrollable').offset().top + 24});
$("a#playmain").css({left: $('div#main_scrollable').offset().left + 861, top:$('div#main_scrollable').offset().top + 24});
$("a#nextpromo").css({left: $('div#main_scrollable').offset().left + 883, top:$('div#main_scrollable').offset().top + 24});
});
</script>
<?


$content .= "<div id=\"content\">
<a id=\"prevpromo\" class=\"prevPromo\"></a>			 				 
<div id=\"main_scrollable\">
<div class=\"items\">";

$res = sql_query("SELECT * FROM torrents WHERE sticky='yes' ORDER BY added DESC LIMIT 10") or sqlerr(__FILE__,__LINE__);
$num = 0;
while ($row = mysql_fetch_array($res)) {


if (!empty($num))
$content .= "<div class=\"item white\">";
else
$content .= "<div class=\"item white2\">";


$combody_f = htmlspecialchars(preg_replace("/\[((\s|.)+?)\]/is", "", ($row["descr"])));

if (strlen($combody_f) >= 455)
$combody_f = substr($combody_f,0,455);


$image1 = $row["image1"];

$content .= "<div class=\"image\">";
if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image1))
$content.="<a href=details.php?id=".$row["id"]."><img src=\"".$image1."\" width=\"250px\" alt=\"".htmlspecialchars($row["name"])."\" /></a>";
else
$content.="<a href=details.php?id=".$row["id"]."><img src='thumbnail.php?image=".$image1."&for=block' width=\"250px\" alt=\"".htmlspecialchars($row["name"])."\" /></a>";


$content .= "</div>";


$content .= "<div class=\"context\"><b>".htmlspecialchars_uni($row["name"])."</b><div class=\"text\">".$combody_f."</div>";

$content .= "<div class=\"detail\"><a href=\"download.php?id=".$row["id"]."\">Скачать релиз</a> / <a href=\"details.php?id=".$row["id"]."\">Подробно об релизе</a></div>";


$content .= "</div></div>";


++$num;
}


$content .= "</div></div>
<a id=\"playmain\" class=\"play\" style=\"left: 906px; top: 157px;\"></a>
<a id=\"nextpromo\" class=\"nextPromo disabled\" style=\"left: 928px; top: 157px;\"></a>
<div class=\"clearboth\">
</div></div>";



?>