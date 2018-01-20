<?
require "include/bittorrent.php";
dbconn();
//loggedinorreturn();
 
parse_referer("empty");


/*
ALTER TABLE `humor` CHANGE `txt` `txt` VARCHAR( 1000 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL;
*/

function bark($msg, $error = true) {
global $tracker_lang;
stdhead();
stdmsg(($error ? $tracker_lang['error'] : $tracker_lang['success']), $msg."<br><b>[</b><a href=\"humor.php\">Вернутся Обратно</a><b>]</b>", ($error ? 'error' : 'success'));
stdfoot();
die;
}


stdhead("Добавить / Просмотр анекдот");

if (isset($_POST['GRI_E']) && $CURUSER){

$hum = htmlspecialchars_uni(strip_tags($_POST['GRI_E']));

if (empty($hum) || strlen($hum) <= 10)
bark("Слишком короткий текст",true);


$numsql = (substr($num,0,20)."");
$hu = sql_query("SELECT txt FROM humor WHERE txt LIKE '%$numsql%'") or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($hu) == 1)
bark("Такой анекдот уже есть в базе : Перенаправление на страницу обратно. <script>setTimeout('document.location.href=\"/humor.php\"', 5000);</script>",true);

sql_query("INSERT INTO humor (uid,txt) VALUES(" .  sqlesc($CURUSER["id"]) . ", " .  sqlesc($hum) . ")") or sqlerr(__FILE__, __LINE__);


bark("Добавлено в базу",false);

@header("Refresh: 5; url=humor.php") or die("Перенаправление на страницу.<script>setTimeout('document.location.href=\"humor.php\"', 5000);</script>");
die;
}

elseif (isset($_POST['GRI']) && get_user_class() >= UC_MODERATOR) {

$id = (int)$_POST['idi_txt'];

$hum = htmlspecialchars_uni(strip_tags($_POST['GRI']));

sql_query("UPDATE humor SET txt = ".sqlesc($hum)." WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

bark("Обновлено без ошибок. Перелинковка на показ анекдота. <script>setTimeout('document.location.href=\"humor.php?id=$id\"', 5000);</script>",false);

die;
}



$id = (int) $_GET['id'];

if (get_user_class() >= UC_MODERATOR && isset($_GET['do'])){


if ($_GET['do'] == "delete"){

sql_query("DELETE FROM humor WHERE id=".$id." LIMIT 1") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM karma WHERE type='humor' AND value=".$id."") or sqlerr(__FILE__, __LINE__); 

bark("Успешно удален. Перенаправление на страницу ввода нового анекдота. <script>setTimeout('document.location.href=\"/humor.php\"', 5000);</script>",false);


} elseif ($_GET['do'] == "edit"){

$hu = sql_query("SELECT txt FROM humor WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

if (!mysql_num_rows($hu))
bark("Неверный id или удален",false);

$res = mysql_fetch_assoc($hu);

$text = htmlspecialchars_uni($res["txt"]);

echo "<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr>

<td align=\"center\" class=\"a\">Редактирование Анекдота</td></tr>
<tr>
<td align=\"center\" class=\"rowhead3\">
<form action=\"humor.php\" method=\"post\">

<style type=\"text/css\"><!--
.En_J {
font-size:11;  
color:#FF00FF;
font-family:Verdana;  
  
padding-top:4; }  

.E_nJ { 
font-size:11;       
font-family:Verdana;
width:70;  
height:21; 
text-align:center; }
--></style>

<center>
<textarea class=\"En_J\" cols=\"100\" rows=\"6\"  name=\"GRI\" onkeypress=\"EnJ_GrI.value=GRI.value.length\" onchange=\"EnJ_GrI.value=GRI.value.length\">".$text."</textarea>
<br>
<input class=\"E_nJ\" disabled onfocus=\"GRI.focus()\" name=\"EnJ_GrI\">
</center>
<input type=\"hidden\" name=\"idi_txt\" value=\"$id\"/>
<input class=\"btn\" value=\"Изменить Анекдот\" type=\"submit\">
</form>
<br>

</td>
<tr></table>";
}

}

elseif (!isset($_GET['do']) && isset($_GET['id'])) {


$hu = sql_query("SELECT humor.*, users.username, users.class FROM humor LEFT JOIN users ON users.id=humor.uid WHERE humor.id=".$id." LIMIT 1") or sqlerr(__FILE__, __LINE__);

if ( mysql_num_rows($hu) == 0)
bark("Анекдот не найден или удален. Пожалуйста проверьте введенные данные.",true);

$res = mysql_fetch_assoc($hu);
       
echo "<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";

echo "<tr>
<td align=\"center\" class=\"a\" colspan=\"2\"><b>Анекдот в базе под числом</b>: <a title=\"Постоянная ссылка для этого анекдота\" href=\"humor.php?id=".$id."\">".$id."</a> (".$res['date'].")</td>
<tr>";

echo "<tr>
<td align=\"center\" class=\"b\">".format_comment($res['txt'])."</td>
<tr>";

echo "<tr>
<td align=\"center\" class=\"a\" colspan=\"2\">
<div style=\"text-align:right;\">
".(!empty($res["username"]) ? "<b>Залит автором</b>: <a href=userdetails.php?id=".$res['uid'].">".get_user_class_color($res['class'],$res['username'])."</a>":"Залит автором: <u>Неизвестно</u>.")."

".(get_user_class() >= UC_MODERATOR ? "<b>[</b><a href=humor.php?id=".$id."&do=edit>Редактировать</a><b>]</b> <b>[</b><a href=humor.php?id=".$id."&do=delete>Удалить</a><b>]</b>":"")." </div>
</td>
<tr>";

echo "</table>";

echo "<br>";

echo "<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";

echo "<tr>
<td align=\"center\" class=\"a\" colspan=\"2\"><b>Последние голосовавшие</b>: (лимит 100 пользователей)</td>
<tr>";

$num_karma = number_format(get_row_count("karma", "WHERE karma.value=".$id));

$hstat = sql_query("SELECT karma.*, users.username, users.class FROM karma LEFT JOIN users ON users.id=karma.user 
WHERE karma.value=".$id." ORDER BY karma.added DESC LIMIT 100") or sqlerr(__FILE__, __LINE__);

echo "<tr><td align=\"center\" class=\"b\">";

$array_date = array();


if (mysql_num_rows($hstat) == 0)
echo "Нет данных о пользователях.";

while ($row = mysql_fetch_assoc($hstat)){

$array_date[] = $row["added"];

if ($st == true)
echo ", ";

echo "<a href=userdetails.php?id=".$row['user'].">".get_user_class_color($row['class'],$row['username'])."</a>";
$st = true;
}


echo "</td><tr>";

$min = @min($array_date);
$max = @max($array_date);


echo "<tr>
<td align=\"center\" class=\"a\" colspan=\"2\"><b>Всего ".$num_karma." голосов".(!empty($num_karma) ? ", где временной период</b>: <br> ".get_date_time($min)." - ".get_date_time($max):"</b>.")."</td>
<tr>";

echo "</table>";
echo "<br>";



echo "<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";

if ($CURUSER)
echo "<tr><td align=\"center\" class=\"b\" colspan=\"2\"><a href=\"humor.php\" title=\"Хочу залить свой анекдот!\"><b>Хочу залить свой анекдот!</b></td></tr>";

echo "<tr><td align=\"center\" class=\"b\" colspan=\"2\"><a href=\"humorall.php\" title=\"Хочу просмотреть все анекдоты\"><b>Хочу просмотреть все анекдоты!</b></td></tr>";

echo "</table>";
echo "<br>";


} else {


?>
<style type=text/css>
<!--
.En_J {
font-size:11px;
color:#FF00FF;
font-family:Verdana;  
  
padding-top:4px; }  

.E_nJ { 
font-size:11px;       
font-family:Verdana;
width:70%;  
height:21px; 
text-align:center; }
-->
</style>
<?





echo "<br>
<form action=\"humor.php\" method=\"post\">
<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";

echo "
<tr>
<td align=\"center\" class=\"a\" colspan=\"2\">
<b>Внимание</b>: Немного правил перед добавлением анекдота в базу.
</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">Длинные (много букв) анекдоты очень редко бывают популярными.</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">Если анекдот не смешён без конечных гыыыы, )))))) и прочих лолок, то он не будет смешным и с ними, впрочем как и постить баяны.</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">Если анекдот состоит в основном из мата или чрезмерно пошлый, он будет удалён системно.</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">
Максимум <b>1000</b> символов, <b>BB</b> коды отключёны</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">Отключайте CapsLock (нет необходимости <u>КРИЧАТЬ</u>)</td></tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">
Дубликаты удаляются</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">
Авторство записывается как и время добавления самого пользователя.</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">
За каждый Голос (Голосование Понравился или Не понравился)</td>
</tr>

<tr>
<td align=\"center\" class=\"a\" colspan=\"2\">
<b>Понравился</b> - вам начисляется <b>25 МБ</b> к Заливке при условии Рейтинг >=1<br>
<b>Не понравился</b> - у вас вычисляется <b>12,5 МБ</b> от Заливки при Рейтинге >=1
</td>
</tr>";


echo "<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">

<textarea class=\"En_J\" cols=\"100\" rows=\"6\" name=\"GRI_E\" onkeypress=\"EnJ_GrI.value=GRI_E.value.length\" onchange=\"EnJ_GrI.value=GRI_E.value.length\"></textarea>

<br>
<input class=\"E_nJ\" disabled onfocus=\"GRI_E.focus()\" name=\"EnJ_GrI2\">

<input class=\"btn\" value=\"Добавить Анекдот\" type=\"submit\">

</td><tr>";


echo "<tr><td align=\"center\" class=\"b\" colspan=\"2\"><a href=\"humorall.php\" title=\"Хочу просмотреть все анекдоты\"><b>Хочу просмотреть все анекдоты!</b></td></tr>";

echo "</table></form><br>";
}




stdfoot();

    
?> 