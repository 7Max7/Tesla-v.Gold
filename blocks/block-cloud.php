<?
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}
$blocktitle = "Облако тэгов";

function cloud2() {
//min / max font sizes
$small = 10;
$big = 32;
//get tag info from worker function
$tags = tag_info();
//amounts
$minimum_count = @min(array_values($tags));
$maximum_count = @max(array_values($tags));
$spread = $maximum_count - $minimum_count;

if($spread == 0) {$spread = 1;}

$cloud_html = '';

$cloud_tags = array();

foreach ($tags as $tag => $count) {

$size = $small + ($count - $minimum_count) * ($big - $small) / $spread;
//set up colour array for font colours.
$colour_array = array('#003EFF', '#0000FF', '#7EB6FF', '#0099CC', '62B1F6');
//spew out some html malarky!
$cloud_tags[] = '<a style="font-weight:normal; color:'.$colour_array[mt_rand(0, 5)].'; font-size: '. floor($size) . 'px'
. '" class="tag_cloud" href="browse.php?tag=' . urlencode($tag) . '&cat=0&incldead=1'
. '" title="Тэг \''.htmlentities($tag ,ENT_QUOTES, "cp1251").'\' отмечен в ' . $count . ' торрентах">'
. htmlentities($tag,ENT_QUOTES, "cp1251") . '</a>('.$count.')';
}

$cloud_html = join("\n", $cloud_tags) . "\n";

return $cloud_html;
}

$content = '
<style type="text/css">
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

$content .= '<div id="wrapper">';
$content .= '<p id="tag">'.cloud2().'</p>';
$content .= '</div>';

?>