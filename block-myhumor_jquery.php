<?
require_once 'include/bittorrent.php';

header ("Content-Type: text/html; charset=windows-1251");
dbconn(false,true);

  
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

global $CURUSER;

if (empty($CURUSER)){
//@header("Location: ../index.php");
die;
}


?>
<script language="JavaScript" type="text/javascript">
/*<![CDATA[*/
    function karma(id, type, act) {
    jQuery.post("karma.php",{"id":id,"act":act,"type":type},function (response) {
        jQuery("#karma").empty();
        jQuery("#karma").append(response);
    });
    }
/*]]>*/
</script>
<?

//sql_query("ALTER TABLE humor ADD KEY id_uid (id, uid);");
//sql_query("ALTER TABLE karma ADD KEY id_user (id, user);");

$hu=sql_query("SELECT h.id, h.karma,h.txt, h.date, h.uid,
(SELECT COUNT(*) FROM karma WHERE type='humor' AND value = h.id AND user = ".$CURUSER["id"].") AS canrate
 FROM humor AS h
 ORDER BY RAND() LIMIT 1") or sqlerr(__FILE__, __LINE__); 
if(mysql_num_rows($hu)>0){
$res = mysql_fetch_assoc($hu); 


// [img]http://www/image.gif[/img]
///$res['txt'] = preg_replace("/\[img\](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))\[\/img\]/i", "<img border=\"0\" src=\"\\1\" width=\"120\" height=\"120\" alt=\"\" onload=\"NcodeImageResizer.createOn(this);\">", $res['txt']);

///$res['txt'] = preg_replace("/\[img=(http:\/\/[^\s'\"<>]+(\.(gif|jpg|png)))\]/i", "<img border=\"0\" src=\"\\1\" alt=\"\"  width=\"120\" height=\"120\"  onload=\"NcodeImageResizer.createOn(this);\">", $res['txt']);


if (!$CURUSER || $res["canrate"] > 0 || $CURUSER['id'] == $res['uid'])
$karma="<td class=\"a\" align=\"center\"><img src=\"pic/minus-dis.png\" title=\"Вы не можете голосовать\" alt=\"\" /> " . karma($res["karma"]) . " <img src=\"pic/plus-dis.png\" title=\"Вы не можете голосовать\" alt=\"\" />
<center><small>
".($CURUSER['class']>=UC_USER ? " <a title=\"Посмотреть на отдельной страничке\" href=humor.php?id=".$res['id'].">Смотреть полностью</a> : ":"")."
 <a title=\"Есть интереснее?\" href=humor.php>Добавить</a> : ".($CURUSER["class"]<UC_MODERATOR ? "":" <a title=\"Отредактировать данный анек\" href=humor.php?id=" . $res['id'] . "&do=edit>Редактировать</a> : <a title=\"Полностью удалить данный анек\" href=humor.php?id=" . $res['id'] . "&do=delete>Удалить</a> : ")." 
<a title=\"Все анекдоты\" href=humorall.php>Все анекдоты</a> 
</small></center>	
</td>\n";
else
$karma="<td align=\"center\" class=\"a\" id=\"karma\">
<img src=\"pic/minus.png\" style=\"cursor:pointer;\" title=\"Не понравился!\" alt=\"\" onclick=\"javascript: karma('$res[id]', 'humor', 'minus');\" /> " . karma($res["karma"]) . " <img src=\"pic/plus.png\" style=\"cursor:pointer;\" onclick=\"javascript: karma('$res[id]', 'humor', 'plus');\" title=\"Понравился!\" alt=\"\" />
<center><small>
".($CURUSER['class']>=UC_USER ? " <a title=\"Посмотреть на отдельной страничке\" href=humor.php?id=".$res['id'].">Смотреть полностью</a> : ":"")."
 <a title=\"Есть интереснее?\" href=humor.php>Добавить</a> : ".($CURUSER["class"]<UC_MODERATOR ? "":" <a title=\"Отредактировать данный анек\" href=humor.php?id=" . $res['id'] . "&do=edit>Редактировать</a> : <a title=\"Полностью удалить данный анек\" href=humor.php?id=" . $res['id'] . "&do=delete>Удалить</a> : ")." 
<a title=\"Все анекдоты\" href=humorall.php>Все анекдоты</a> 
</small></center>
</td>\n";

$humor=strip_tags($res['txt']);
$humor = str_replace('script', '', $humor);
$humor = str_replace('js', '', $humor);
$humor = str_replace('src=', '', $humor); 

$humor2=$humor;

$humor1=strlen($humor)>450?(substr($humor,0,450)."<a title=\"Анекдот длиннее обычного, нажмите СЮДА, чтобы посмотреть продолжение на другой страничке\" href=humor.php?id=".$res['id'].">&#8658;&#8658;&#8658;</a>"):$humor2;

$date=$res['date'];


echo "<table border=\"0\" width=\"100%\">
<tr>
<td align=\"center\" class=\"b\">
".$humor1."
</td>
</tr>

".$karma."</table>";
}else{
echo "<center>[не найдено]<br><br><b>[</b><a href=humor.php>Добавить свой анекдот</a><b>]</b></center>"; 
}


} else @header("Location: ../index.php");

?>