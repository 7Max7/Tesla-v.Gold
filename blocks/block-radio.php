<?

if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}
if ($CURUSER["class"]>3)
$auto="true";
else
$auto="false";

$content .= "<object width=\"150\" height=\"68\" id=\"mju\">
<param name=\"allowScriptAccess\" value=\"sameDomain\" />
<param name=\"swLiveConnect\" value=\"true\" />
<param name=\"movie\" value=\"mju.swf\" />
<param name=\"flashvars\" value=\"swf/playlist=playlist.mpl&auto_run=false&repeat _one=false&shuffle=false\" />
<param name=\"loop\" value=\"false\" />
<param name=\"menu\" value=\"false\" />
<param name=\"quality\" value=\"high\" />
<param name=\"wmode\" value=\"transparent\" />
<embed src=\"swf/mju.swf\" flashvars=\"playlist=swf/playlist.mpl&auto_run=false&repeat_one=false&shuff le=false\" loop=\"false\" menu=\"false\" quality=\"high\" wmode=\"transparent\" bgcolor=\"#ffffff\" width=\"150\" height=\"68\" name=\"mju\" allowScriptAccess=\"sameDomain\" swLiveConnect=\"true\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
</object>";

$blocktitle = "Радио";
?> 