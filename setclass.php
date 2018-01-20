<?
require "include/bittorrent.php";

dbconn(false);

loggedinorreturn();

// The following line may need to be changed to UC_MODERATOR if you don't have Forum Moderators
if ($CURUSER['class'] < UC_MODERATOR) die("Не администрация сайта"); // No acces to below this rank
if ($CURUSER['override_class'] <> 255) die("Не 255 тест права"); // No access to an overridden user class either - just in case

if (isset($_GET['action']) == 'editclass')
{
	
//$returnto=htmlspecialchars($_GET["returnto"]);
//htmlentities
$returnto=($_GET["returnto"]);

$newclass = (int) $_GET['class'];
//$returnto = htmlentities($_GET['returnto']);

$maxclass = get_user_class() - 1;

if($newclass>$maxclass){
stderr($tracker_lang['error'],"невозможно поставить права выше ваших базовых.");}


sql_query("UPDATE users SET override_class=class, class=" .sqlesc($newclass). " WHERE id = ".$CURUSER['id']); 

if (!empty($returnto) && htmlspecialchars($_GET['vol'])=="yes"){
	header("Location: $returnto");
}
else
header("Location: $DEFAULTBASEURL");

   die();
}

// HTML Code to allow changes to current class
stdhead("Смена класса");

$returlink=getenv("HTTP_REFERER");

if (!stristr($returlink,$_SERVER["PHP_SELF"]) || $returlink<>$DEFAULTBASEURL) {
$returview = str_replace($DEFAULTBASEURL."/","",$returlink);
} else {
unset($returlink);
unset($returview);
}

print("<form method=get action=\"setclass.php\">
<input type=hidden name=\"action\" value=\"editclass\">
<input type=hidden name=\"returnto\" value=\"$returlink\"/>
<table width=300 border=2 cellspacing=5 cellpadding=5>
<tr><td><b>Класс</b>:</td><td align=left>
<select name=class>
");

$maxclass = get_user_class() - 1;
for ($i = 0; $i <= $maxclass; ++$i)

print("<option value=$i" .">" . get_user_class_name($i) . "\n");
print("</select></td></tr></td></tr>
".($returlink && $returview ? "<tr><td colspan=3 align=center>
<b>Вернутся к предыдущему файлу</b>: <input type=\"checkbox\" name=\"vol\" checked value=\"yes\" ><br>$returview
</td></tr>":"")."
<tr><td colspan=3 align=center>
<input type=submit class=btn value=\"Сменить Права\"></td></tr>
</form></table><br />");

stdfoot();
?>