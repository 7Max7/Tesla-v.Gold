<?

if (!defined('BLOCK_FILE')) {   
 Header("Location: ../index.php");   
 exit;   
}

$blocktitle = "ѕрикрепленные релизы (важные)";   

?>
<style>
*  { margin: 0; padding: 0; }
a, a img { border: 0; text-decoration: none; outline: 0; }
#page-wrap { width: 760px; padding: 15px; background: white; margin: 0 auto 50px; position: relative; }
#col1{ width: 49%; float: left; margin: 0 0 20px 0; }
#col2 { width: 49%; float: right; }
h5{ font: 15px Georgia, Serif; text-align: center; }
pre  { font: 13px/1.8 Monaco, MonoSpace; margin: 0 0 15px 0; }
ul{ margin: 0 0 25px 25px; }
ul li { font: 15px Georgia, Serif; margin: 0 0 8px 0; }
#dl { position: absolute; top: 10px; right: 0; background: black; color: white; -moz-border-radius: 5px; -webkit-border-radius: 5px; padding: 3px 6px; }
#dl:hover    { background: #666; }
.image       { position: relative; margin-bottom: 20px; width: 100%; }
.image h2    { position: absolute; top: 220px; left: 0; width: 100%; }
.image h2 span        { color: white; font: bold 30px/40px Helvetica, Sans-Serif; letter-spacing: -1px; background: rgb(0, 0, 0); background: rgba(0, 0, 0, 0.7); padding: 6px 8px; }
.image h2 span.spacer          { padding: 0 2px; background: none; }
#textSlide   { padding: 10px 30px; }
#textSlide h3 { font: 20px Georgia, Serif; }
#textSlide h4 { text-transform: uppercase; font: 15px Georgia, Serif; margin: 10px 0; }
#textSlide ul { list-style: disc; margin: 0 0 0 25px; }
#textSlide ul li { display: list-item; }
#quoteSlide { padding: 30px; }
#quoteSlide blockquote { font: italic 24px/1.5 Georgia, Serif; text-align: center; color: #444; margin: 0 0 10px 0; }
#quoteSlide p { text-align: center; }
.anythingSlider { width: 760px; height: 360px; position: relative; margin: 0 auto 15px; }
.anythingSlider .wrapper { width: 680px; overflow: auto; height: 341px; margin: 0 40px; position: absolute; top: 0; left: 0; }
.anythingSlider .wrapper ul  { width: 9999px; list-style: none; position: absolute; top: 0; left: 0; background: #eee; border-top: 3px solid #e0a213; border-bottom: 3px solid #e0a213; margin: 0; }
.anythingSlider ul li { display: block; float: left; padding: 0; height: 317px; width: 680px; margin: 0; }
.anythingSlider .arrow { display: block; height: 200px; width: 67px; background: url(/pic/arrows.png) no-repeat 0 0; text-indent: -9999px; position: absolute; top: 65px; cursor: pointer; }
.anythingSlider .forward { background-position: 0 0; right: -20px; }
.anythingSlider .back { background-position: -67px 0; left: -20px; }
.anythingSlider .forward:hover { background-position: 0 -200px; }
.anythingSlider .back:hover  { background-position: -67px -200px; }
#thumbNav  { position: relative; top: 323px; text-align: center; }
#thumbNav a  { color: black; font: 11px/18px; Georgia, Serif; display: inline-block; padding: 2px 8px; height: 18px; margin: 0 5px 0 0; background: #c58b04 url(/pic/cellshade.png) repeat-x; text-align: center; -moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px; -webkit-border-bottom-right-radius: 5px; -webkit-border-bottom-left-radius: 5px; }
#thumbNav a:hover { background-image: none; }
#thumbNav a.cur { background: #e0a213; }
#start-stop  { background: green; background-image: url(/pic/cellshade.png); background-repeat: repeat-x; color: white; padding: 2px 5px; width: 40px; text-align: center; position: absolute; right: 45px; top: 323px; -moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px; -webkit-border-bottom-right-radius: 5px; -webkit-border-bottom-left-radius: 5px; }
#start-stop.playing { background-color: red; }
#start-stop:hover { background-image: none; }
.anythingSlider .wrapper ul ul { position: static; margin: 0; background: none; overflow: visible; width: auto; border: 0; }
.anythingSlider .wrapper ul ul li { float: none; height: auto; width: auto; background: none; }
</style>


<script type="text/javascript" src="js/jquery.js"></script>
<script src="js/jquery.anythingslider.js" type="text/javascript"></script>

<script type="text/javascript">
function formatText(index, panel) {
return index + "";
}
$(function () {
$('.anythingSlider').anythingSlider({
easing: "easeInOutExpo",        // Anything other than "linear" or "swing" requires the easing plugin
autoPlay: true,                 // This turns off the entire FUNCTIONALY, not just if it starts running or not.
delay: 5000,                    // How long between slide transitions in AutoPlay mode
startStopped: false,            // If autoPlay is on, this can force it to start stopped
animationTime: 600,             // How long the slide transition takes
hashTags: true,                 // Should links change the hashtag in the URL?
buildNavigation: true,          // If true, builds and list of anchor links to link to each slide
pauseOnHover: true,             // If true, and autoPlay is enabled, the show will pause on hover
startText: "—тарт",             // Start text
stopText: "—топ",               // Stop text
navigationFormatter: formatText       // Details at the top of the file on this use (advanced use)
});
$("#slide-jump").click(function(){
$('.anythingSlider').anythingSlider(6);
});
});
</script>

<?

$content .= "<div class=\"anythingSlider\">";
$content .= "<div class=\"wrapper\">";

$content .= "<ul>";

$res = sql_query("SELECT * FROM torrents WHERE sticky='yes' ORDER BY added DESC LIMIT 10") or sqlerr(__FILE__,__LINE__);

while ($row = mysql_fetch_array($res)) {

$content.= "<li>\n";
$content.= "<div id=\"quoteSlide\">\n"; ///если внутри текст - показывать div тег



$combody_f = htmlspecialchars(preg_replace("/\[((\s|.)+?)\]/is", "", ($row["descr"])));

if (strlen($combody_f) >= 512)
$combody_f = substr($combody_f,0,512);

$image1 = $row["image1"];

if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image1))
$content.="<img width=\"250px\" style=\"float: right; margin: 0pt 0pt 2px 5px;\" alt=\"".htmlspecialchars_uni($row["name"])."\" src=\"".$image1."\">";/// картинка идет первой
else
$content.="<img width=\"250px\" style=\"float: right; margin: 0pt 0pt 2px 5px;\" alt=\"".htmlspecialchars_uni($row["name"])."\" src=\"/torrents/images/".$image1."\">"; /// картинка идет первой


$content .= "<b><a href=\"details.php?id=".$row["id"]."\" title=\"ѕросмотреть полностью описание релиза\">".htmlspecialchars_uni($row["name"])."</a></b><br>";
/// <a href=\"download.php?id=".$row["id"]."\">—качать</a>
$content .= $combody_f; /// вывод описани€


$content.= "</div>\n";///если внутри текст - показывать div тег
$content.= "</li>\n";

}


             

$content .= "</ul>";
$content .= "</div>";
$content .= "</div>";


?>