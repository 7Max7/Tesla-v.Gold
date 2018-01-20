<?

if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

function tag_info2() {

$result = sql_query("SELECT date,text FROM shoutbox WHERE id > 0 ORDER BY id DESC limit 10") or sqlerr(__FILE__, __LINE__);

while($row = mysql_fetch_assoc($result)) {
// suck into array
$arr[$row['date']] = $row['text'];
}
//sort array by key
ksort($arr);

return $arr;
}

function cloud3d() {
//min / max font sizes
$small = 7;
$big = 20;
//get tag info from worker function
$tags = tag_info2();
//amounts
$minimum_count = min(array_values($tags));
$maximum_count = max(array_values($tags));
$spread = $maximum_count - $minimum_count;

if($spread == 0) {$spread = 1;}

$cloud_html = '';

$cloud_tags = array();
$i = 0;
if ($tags)
foreach ($tags as $tag => $count) {

$size = $small + ($count - $minimum_count) * ($big - $small) / $spread;

//spew out some html malarky!
$cloud_tags[] = "<a href='browse.php?tag=" . $tag . "%26amp;cat=0%26amp;incldead=1' style='font-size:". floor($size) . "px;'>"
. htmlentities($tag,ENT_QUOTES, "cp1251") . "(".$count.")</a>";
$cloud_links[] = "<br/><a href='browse.php?tag=" . $tag . "&cat=&incldead=1' style='font-size:". floor($size) . "px;'>$tag</a><br/>";
$i++;
}
$cloud_links[$i-1].="Ваш браузер не поддерживает flash!";
$cloud_html[0] = join("", $cloud_tags);
$cloud_html[1] = join("", $cloud_links);


return $cloud_html;
}

function cloud2 ($style = '',$name = '', $color='',$bgcolor='',$width='',$height='',$speed='',$size='') {
  $tagsres = array();
  $tagsres = cloud3d();
  $tags = $tagsres[0];
  $links = $tagsres[1];
if (!$style) $style = '<style type="text/css">
.tag_cloud
{padding: 3px; text-decoration: none;
font-family: verdana; }
.tag_cloud:link { color: #0099FF; text-decoration:none;border:1px transparent solid;}
.tag_cloud:visited { color: #00CCFF; border:1px transparent solid;}
.tag_cloud:hover { color: #0000FF; background: #ddd;border:1px #bbb solid; }
.tag_cloud:active { color: #0000FF; background: #fff; border:1px transparent solid;}
#tag
{
line-height:28px;
font-family:Verdana, Arial, Helvetica, sans-serif;
text-align:justify;
}
</style>';

  $cloud_html = $style.'<div id="wrapper"><p id="tag">
  <script type="text/javascript" src="js/swfobject.js"></script>
<div id="'.($name?$name:"wpcumuluswidgetcontent").'">'.$links.'</div>
<script type="text/javascript">
var rnumber = Math.floor(Math.random()*9999999);
var widget_so = new SWFObject("swf/tagcloud.swf?r="+rnumber, "tagcloudflash", "'.($width?$width:"100%").'", "'.($height?$height:"100%").'", "'.($size?$size:"9").'", "'.($bgcolor?$bgcolor:"#fafafa").'");
widget_so.addParam("allowScriptAccess", "always");
widget_so.addVariable("tcolor", "'.($color?$color:"0x0054a6").'");
widget_so.addVariable("tspeed", "'.($speed?$speed:"250").'");
widget_so.addVariable("distr", "true");
widget_so.addVariable("mode", "tags");
widget_so.addVariable("tagcloud", "<span>'.$tags.'</span>");
widget_so.write("'.($name?$name:"wpcumuluswidgetcontent").'");
</script></p></div>';
return $cloud_html;
}

//$blocktitle = "Теги!!!:)";

$content = cloud2();
//$content .='<br/><div align="center">[<a href="alltags.php">Большие теги</a>]</div>'

?> 