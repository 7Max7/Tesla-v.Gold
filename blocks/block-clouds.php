<style type="text/css">
.tag_cloud
{padding: 3px; text-decoration: none;
font-family: verdana; }
.tag_cloud:link {
-moz-border-radius-bottomleft:7px;
-moz-border-radius-bottomright:7px;
-moz-border-radius-topleft:7px;
-moz-border-radius-topright:7px;
border:1px solid transparent;
text-decoration:none;}

.tag_cloud:visited { border:1px transparent solid; -moz-border-radius: 7px;}
.tag_cloud:hover { background: #ddd;border:1px #bbb solid; }
.tag_cloud:active { background: #fff; border:1px transparent solid;}
#tag
{
line-height:28px;
font-family:Verdana, Arial, Helvetica, sans-serif;
text-align:justify;
}
</style>
<?


if (!defined('BLOCK_FILE')) { 
 Header("Location: ../index.php"); 
 exit; 
}


global $on_search_log;

if ($on_search_log==1){


$cacheStatFile = "cache/block-clouds.txt";
$expire = 60*60; // 60 минут
if (file_exists($cacheStatFile) && filemtime($cacheStatFile) > (time() - $expire)) {
   $content.=file_get_contents($cacheStatFile);
} else
{
	
	
function tag_info3() {

$offset_result = sql_query("SELECT FLOOR(RAND() * COUNT(*)) AS `offset` FROM `searchcloud` LIMIT 300"); 
$offset_row = mysql_fetch_object( $offset_result ); 
$offset = $offset_row->offset; 
//$result = sql_query("SELECT searchedfor, howmuch FROM `searchcloud` LIMIT $offset, 50"); 
$result = sql_query("SELECT searchedfor, howmuch FROM searchcloud ORDER BY id DESC LIMIT 50"); 

while($row = mysql_fetch_assoc($result)) {
// suck into array 

$arr[$row['searchedfor']] = $row['howmuch']; 
} 
//sort array by key 
ksort($arr); 

return $arr; 
} 

function cloud3() {
//min / max font sizes 
$small = 9; 
$big = 30; 
//get tag info from worker function 
$tags = tag_info3(); 
//amounts 
$minimum_count = min(array_values($tags)); 
$maximum_count = max(array_values($tags)); 
$spread = $maximum_count - $minimum_count; 

if($spread == 0) {$spread = 1;} 

$cloud_html = ''; 

$cloud_tags = array(); 

foreach ($tags as $tag => $count) { 

$size = $small + ($count - $minimum_count) * ($big - $small) / $spread; 
//set up colour array for font colours. 
$colour_array = array('green', 'blue', '#ffa500','purple','#0099FF'); 
//spew out some html malarky! 
$cloud_tags[] = '<a style="color:'.$colour_array[mt_rand(0, 5)].'; font-size: '. floor($size) . 'px' 
. '" class="tag_cloud" href="browse.php?search=' . urlencode($tag) . '&amp;cat=0&amp;incldead=1' 
. '" title="\''.htmlentities($tag ,ENT_QUOTES, "cp1251").'\' был в поиске раз ' . $count . '">' 
. htmlentities($tag,ENT_QUOTES, "cp1251") . '</a>'; 
} 

$cloud_html = join("\n", $cloud_tags) . "\n"; 

return $cloud_html; 
}



$blocktitle = "Что у нас искали";  
$colour_array = array('green', 'blue', 'purple', 'orange', '#0099FF'); 
    

$content .= '<div id="wrapper" style="width:90%;border:0px solid '.$colour_array.';">'; 
$content .= cloud3(); 
$content .= '</div><br /><br />'; 

$fp = fopen($cacheStatFile,"w");
   if($fp)
   {
    fputs($fp, $content); 
    fclose($fp); 
   }
 }


if (get_user_class() >= UC_SYSOP)
{
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
}	 

}
else
$content .= "<p align=center><b>Функция отключенна</b></p>"; 
    
    
?> 