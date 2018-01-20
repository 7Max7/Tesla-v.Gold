<?

require_once('include/bittorrent.php');

dbconn(false);
loggedinorreturn();

stdhead("Бонусы пользователя ".$CURUSER['username']);

$exchange = htmlspecialchars($_POST["exchange"]);
$action = htmlspecialchars($_POST["action"]);

$usercomment=$CURUSER["usercomment"];

if($action == "elegor"){
if(get_user_class() < UC_ADMINISTRATOR)// admin access
stderr("Ошибка", "У вас нет прав для доступа на эту страницу");

$update = htmlspecialchars($_POST["update"]);
$delete = htmlspecialchars($_POST["delete"]);
$addnew = htmlspecialchars($_POST["addnew"]);

//------------------ addnew --------------------------------------------------------
if($addnew){

if(get_user_class() < UC_SYSOP)// admin access
stderr("Ошибка", "У вас нет прав для доступа на эту страницу");

$bonus_position = htmlspecialchars($_POST["next"]);
$bonus_title = htmlspecialchars($_POST["bonus_title"]);
$bonus_description = htmlspecialchars($_POST["bonus_description"]);
$bonus_points = (int) $_POST["bonus_points"];
$bonus_art = htmlspecialchars($_POST["bonus_art"]);
$nbyt = htmlspecialchars($_POST["nbyt"]);
$bonus_menge = (int)$_POST["bonus_menge"];

switch($bonus_art){
case 0:
$bonus_art = "traffic";
break;
case 1:
$bonus_art = "invite";
break;
}

switch($nbyt){
case 0:
if($bonus_art <> "invite")
$bonus_menge = $bonus_menge*1024*1024;
break;
case 1:
if($bonus_art <> "invite")
$bonus_menge = $bonus_menge*1024*1024*1024;
break;
case 2:
if($bonus_art <> "invite")
$bonus_menge = $bonus_menge*1024*1024*1024*1024;
break;
}

$missdata = (!$bonus_title || !$bonus_description || !$bonus_points || !is_numeric($bonus_points) || !$bonus_art || !$bonus_menge || !is_numeric($bonus_menge) ? "Missing data:" : "");
if($missdata){
print("<table><tr><td class=main align=left>");

echo $missdata;
if(!$bonus_title)
echo "<li>Укажите название</li>";
if(!$bonus_description)
echo "<li>Укажите описание</li>";
if(!$bonus_points || !is_numeric($bonus_points))
echo "<li>Укажите количество бонусов</li>";
if(!$bonus_art)
echo "<li>Укажите тип (трафик или инвайт)</li>";
if(!$bonus_menge || !is_numeric($bonus_menge))
echo "<li>Укажите награду</li>";

print("</td></tr></table>");
print("<br /><h2>Проверьте, пожалуйста</h2>");
stdfoot();
die();
}

$updbonus[] = sqlesc($bonus_position);
$updbonus[] = sqlesc($bonus_title);
$updbonus[] = sqlesc($bonus_description);
$updbonus[] = sqlesc($bonus_points);
$updbonus[] = sqlesc($bonus_art);
$updbonus[] = sqlesc($bonus_menge);

if(!$missdata)
mysql_query("INSERT INTO mybonus (bonus_position, bonus_title, bonus_description, bonus_points, bonus_art, bonus_menge) VALUES(" . implode(" , ", $updbonus) . ")") or sqlerr(__FILE__, __LINE__);
}
//------------------ end addnew-----------------------------------------------------

//------------------ update --------------------------------------------------------
if($update){
$res = sql_query("SELECT id from mybonus") or sqlerr(__FILE__, __LINE__);
$res2 = mysql_query("SELECT id, bonus_position from mybonus") or sqlerr(__FILE__, __LINE__);
$ress = (mysql_num_rows($res));

while($positions = mysql_fetch_assoc($res2)){
$bonus_position_remote = $positions["bonus_position"];
$remote[] = $bonus_position_remote;

$bonus_position_local = $_POST["bonus_position_".$positions["id"]];
$local[] = $bonus_position_local;
}

sort($local);
sort($remote);
if($local <> $remote){
stderr("Ошибка", "Двойная позиция");
stdfoot();
}

while($updbonus2 = mysql_fetch_assoc($res)){
$bonus_id = htmlspecialchars($_POST["bonus_id_".$updbonus2["id"]]);
$bonus_position = htmlspecialchars($_POST["bonus_position_".$updbonus2["id"]]);
$bonus_title = htmlspecialchars($_POST["bonus_title_".$updbonus2["id"]]);
$bonus_description = htmlspecialchars($_POST["bonus_description_".$updbonus2["id"]]);
$bonus_points = htmlspecialchars($_POST["bonus_points_".$updbonus2["id"]]);
$bonus_art = htmlspecialchars($_POST["bonus_art_".$updbonus2["id"]]);
$nbyt = htmlspecialchars($_POST["nbyt_".$updbonus2["id"]]);
$bonus_menge = htmlspecialchars($_POST["menge_".$updbonus2["id"]]);

switch($bonus_art){
case 0:
$bonus_art = "traffic";
break;
case 1:
$bonus_art = "invite";
break;
}

switch($nbyt){
case 0:
if($bonus_art != "invite")
$bonus_menge = $bonus_menge*1024*1024;
break;
case 1:
if($bonus_art != "invite")
$bonus_menge = $bonus_menge*1024*1024*1024;
break;
case 2:
if($bonus_art != "invite")
$bonus_menge = $bonus_menge*1024*1024*1024*1024;
break;
}

$updbonus[] = "bonus_position = ".sqlesc($bonus_position);
$updbonus[] = "bonus_title = ".sqlesc($bonus_title);
$updbonus[] = "bonus_description = ".sqlesc($bonus_description);
$updbonus[] = "bonus_points = ".sqlesc($bonus_points);
$updbonus[] = "bonus_art = ".sqlesc($bonus_art);
$updbonus[] = "bonus_menge = ".sqlesc($bonus_menge);

if($local == $remote)
mysql_query("UPDATE mybonus SET " . implode(", ", $updbonus) . " WHERE id = ".sqlesc($bonus_id)) or ((mysql_errno() == 1062) ? stderr("Нельзя<br />", "Двойная позиция!") : sqlerr(__FILE__, __LINE__));
}
}
//------------------ end update ----------------------------------------------------

//------------------ delete --------------------------------------------------------
if($delete){
$bonus_ids=(int)$_POST["delete"];

foreach($bonus_ids as $bonus_id){
$check = mysql_query("SELECT bonus_position FROM mybonus WHERE id=".$bonus_id) or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($check) != 1)
stderr("Ошибка", "Неправильный ID $bonus_id");

mysql_query("DELETE FROM mybonus WHERE id=".$bonus_id) or sqlerr(__FILE__, __LINE__);
}


//resort positions
$pos = mysql_query("SELECT bonus_position FROM mybonus") or sqlerr(__FILE__, __LINE__);
$i=1;
while($position=mysql_fetch_assoc($pos)){
$newpos=str_replace ( $position["bonus_position"], $position["bonus_position"], $i);
mysql_query("UPDATE mybonus SET bonus_position=" . str_replace( $position["bonus_position"], $position["bonus_position"], $i) . " WHERE bonus_position=".$position["bonus_position"]) or sqlerr(__FILE__, __LINE__);
$i++; 
}
}
//------------------ end delete ----------------------------------------------------

//------------------ admin control table -------------------------------------------
function bonus_position() {
$return = array();
$result = mysql_query("SELECT bonus_position FROM mybonus ORDER BY bonus_position");
while ($row = mysql_fetch_array($result))
$return[] = $row;
return $return;
}

$res=mysql_query("SELECT * FROM mybonus ORDER BY bonus_position") or sqlerr(__FILE__, __LINE__,__ERROR__);
if(mysql_num_rows($res) == 0)
echo "в базе значений нет";
$next = mysql_num_rows($res)+1;

begin_table();
print("<tr>".
"<form name=\"upd\" action=\"mybonus.php\" method=\"post\">".
"<input type=hidden name=update value=true />".
"<input type=hidden name=action value=elegor />".
"<td colspan=7 class=row2><h2>Изменить настройки</h2></td>".
"</tr>".
"<tr>".
"<td class=row2 align=center valign=top>Опция</td>".
"<td class=row2 align=left valign=top>Название</td>".
"<td class=row2 align=left valign=top>Описание</td>".
"<td class=row2 align=center valign=top>Бонусы</td>".
"<td class=row2 align=center valign=top>Тип</td>".
"<td class=row2 align=center valign=top>Награда</td>".
"<td class=row2 align=center valign=top>Удалить</td>".
"</tr>");

while($bonus = mysql_fetch_assoc($res)){
print("<input type=hidden name=bonus_id_".$bonus["id"]." value=".$bonus["id"]." />");
$menge=$bonus["bonus_menge"];
if($bonus["bonus_art"]=="traffic")
$menge = ($bonus["bonus_menge"]/1024/1024);

print ("<td class=row3align=center valign=top>".
"<select name=\"bonus_position_".$bonus["id"]."\">");
$position = bonus_position();
$catdropdown = "";
$j=0;
$selected = $bonus["bonus_position"];
foreach ($position as $row){
$j++;
$catdropdown .= "<option value=\"" . $row["bonus_position"] . "\"";

if ($j==$bonus["bonus_position"]){
$catdropdown .= " selected=\"selected\"";
$j=$bonus["bonus_position"];}

$catdropdown .= ">" . htmlspecialchars($row["bonus_position"]) . "</option>\n";
}
print ($catdropdown);
print("</select>".
"</td>".
"<td class=row3align=left valign=top><input type=\"text\" name=\"bonus_title_".$bonus["id"]."\" value=\"".$bonus["bonus_title"]."\" /></td>".
"<td class=row3align=left valign=top><textarea name=\"bonus_description_".$bonus["id"]."\" cols=50 rows=3>".$bonus["bonus_description"]."</textarea></td>".
"<td class=row3align=center valign=top><input type=\"text\" name=\"bonus_points_".$bonus["id"]."\" size=\"5\" value=\"".$bonus["bonus_points"]."\" /></td>");
print ("<td class=row3align=left valign=top>".
"<input type=\"radio\" name=\"bonus_art_".$bonus["id"]."\" value=\"0\" ".($bonus["bonus_art"]=="traffic" ? " checked=\"checked\"" : " ") ."onclick=\"javascript: nbytdiv_".$bonus["id"].".style.display = 'block'\" />&nbsp;траффик<br />".
"<input type=\"radio\" name=\"bonus_art_".$bonus["id"]."\" value=\"1\" ".($bonus["bonus_art"]=="invite" ? " checked=\"checked\"" : " ") ."onclick=\"javascript: nbytdiv_".$bonus["id"].".style.display = 'none'\" />&nbsp;приглашение".
"</td>".
"<td class=row3align=center valign=top><input type=\"text\" name=\"menge_".$bonus["id"]."\" size=\"15\" value=\"".$menge."\" />".
"<br />".

"<div disable=\"1\" style=\"display: ".($bonus["bonus_art"] == "invite" ? "none" : "block") ."\" id=\"nbytdiv_".$bonus["id"]."\"><input type=\"radio\" name=\"nbyt_".$bonus["id"]."\" value=\"2\" onclick=\"javascript: document.upd.menge_".$bonus[id].".value = ".($bonus["bonus_menge"]/1024/1024/1024/1024)."\" />TB".
"<input type=\"radio\" name=\"nbyt_".$bonus["id"]."\" value=\"1\" onclick=\"javascript: document.upd.menge_".$bonus[id].".value = ".($bonus["bonus_menge"]/1024/1024/1024)."\" />GB".
"<input type=\"radio\" name=\"nbyt_".$bonus["id"]."\" value=\"0\" checked=\"checked\" onclick=\"javascript: document.upd.menge_".$bonus[id].".value = ".($bonus["bonus_menge"]/1024/1024)."\" />MB</div>".

"</td>".
"<td class=row3align=center><input type=\"checkbox\" name=\"delete[]\" value=\"".$bonus["id"]."\" /></td>".
"</tr>");
}
print("<tr>".
"<td colspan=7 class=row2 align=center><input type=\"submit\" class=\"btn\" value=\"Изменить\" /></td>".
"</form></tr>".
"<tr>".
"<td colspan=7 class=row3style=\"border: none\">&nbsp;</td>".
"</tr>".
"<tr>".
"<td colspan=7 class=row2><h2>Добавить новый</h2></td>".
"</tr>");
print("<tr>".
"<form action=\"mybonus.php\" method=\"post\">".
"<input type=\"hidden\" name=\"addnew\" value=\"true\" />". 
"<input type=\"hidden\" name=\"action\" value=\"elegor\" />". 
"<input type=\"hidden\" name=\"next\" value=\"".$next."\" />". 
"<td class=row2 align=center valign=top>Опция</td>".
"<td class=row2 align=left valign=top>Название</td>".
"<td class=row2 align=left valign=top>Описание</td>".
"<td class=row2 align=center valign=top>Бонусы</td>".
"<td class=row2 align=center valign=top>Тип</td>".
"<td class=row2 colspan=2 align=center valign=top>Награда</td>".
"</tr>");
print ("<tr>".
"<td class=row3align=center valign=top>".$next."</td>".
"<td class=row3align=left valign=top><input type=\"text\" name=\"bonus_title\" /></td>".
"<td class=row3align=left valign=top><textarea name=\"bonus_description\" cols=50 rows=3></textarea></td>".
"<td class=row3align=center valign=top><input type=\"text\" name=\"bonus_points\" size=\"5\" /></td>".
"<td class=row3align=left valign=top>".
"<input type=\"radio\" name=\"bonus_art\" value=\"0\" checked=\"checked\" onclick=\"javascript: nbytdiv.style.display = 'block'\" />&nbsp;траффик<br />".
"<input type=\"radio\" name=\"bonus_art\" value=\"1\" onclick=\"javascript: nbytdiv.style.display = 'none'\" />&nbsp;приглашение".
"<td colspan=2 class=row3align=center valign=top><input type=\"text\" name=\"bonus_menge\" size=\"20\" />".
"<br />".
"<div disable=\"1\" style=\"display: block\" id=\"nbytdiv\">".
"<input type=\"radio\" name=\"nbyt\" value=\"2\" onclick=\"javascript: document.upd.menge.value = (document.upd.menge.value/1024/1024/1024/1024)\" />TB".
"<input type=\"radio\" name=\"nbyt\" value=\"1\" onclick=\"javascript: document.upd.menge.value = (document.upd.menge.value/1024/1024/1024)\" />GB".
"<input type=\"radio\" name=\"nbyt\" value=\"0\" checked=\"checked\" onclick=\"javascript: document.upd.menge.value = (document.upd.menge.value/1024/1024)\" />MB</div>".
"</td>".
"</tr>".
"<td colspan=7 class=row2 align=center><input class=btn type=\"submit\" value=\"Добавить\" /></td>".
"</form></tr>");
end_table();
stdfoot();
die();
}
//------------------ end admin control table ---------------------------------------

// ----------------- exchange ------------------------------------------------------
$user_bonus = ($CURUSER['bonus']);
$user_id = $CURUSER['id'];

if($exchange){
///$user_bonus = htmlspecialchars($_POST["user_bonus"]); /// это и есть баг, повтор выше функции, но проверка через него пошла.
$user_id = htmlspecialchars((int)$_POST["user_id"]);
$take_points = ($_POST["bonus_points"]);
$bonus_art = htmlspecialchars($_POST["bonus_art"]);
$bonus_option = htmlspecialchars($_POST["bonus_option"]);
$bonus_menge = htmlspecialchars($_POST["bonus_menge"]);
$modcomment = $CURUSER['modcomment'];

if($user_bonus >= $take_points){
if($bonus_art == "traffic"){
$bonus_menge_in=mksize($bonus_menge);

$usercomment = sqlesc(gmdate("Y-m-d") . " - Обменял " .$take_points. " бонусов на трафик $bonus_menge_in.\n" .$usercomment);
mysql_query("UPDATE users SET bonus = bonus-".sqlesc($take_points).", uploaded = ".sqlesc($CURUSER["uploaded"]+$bonus_menge).", usercomment = ".$usercomment." WHERE id=".sqlesc($user_id)) or sqlerr(__FILE__, __LINE__);
print("<h2>Обменяно</h2>".
"<br />Удачно обменяно $take_points бонусов на траффик.<br />Возврат на главную страницу произойдет через 5 сек.".
"<p><a href=\"mybonus.php\">Вернутся к страничке обмена Бонусов</a>");
print"<meta http-equiv=\"refresh\" content=\"5; url=mybonus.php\" />";
stdfoot();
die();
}
elseif($bonus_art == "invite"){
$bonus_menge_in=mksize($bonus_menge);
$usercomment = sqlesc(gmdate("Y-m-d") . " - Обменял " .$take_points. " бонусов на инвайты $bonus_menge.\n ".$usercomment);
mysql_query("UPDATE users SET bonus = bonus-".sqlesc($take_points).", invites = ".sqlesc($CURUSER["invites"]+$bonus_menge).", usercomment = ".$usercomment." WHERE id=".sqlesc($user_id)) or sqlerr(__FILE__, __LINE__);
print("<h2>Успешно!</h2>".
"<br />Успешно обменяно $take_points бонусов на приглашения.<br />Возврат на главную страницу произойдет через 5 сек.".
"<p><a href=\"mybonus.php\">Вернутся к страничке обмена Бонусов</a>");
print"<meta http-equiv=\"refresh\" content=\"5; url=mybonus.php\" />";
stdfoot();
die();
}
else{
stderr("Ошибка", "Неверный тип!");
}
}else{
stderr("Ошибка", "Недостаточно для обмена");
}
}
// ------- end exchange ------------------------------------------------------

//--- exchange table ---------
if(get_user_class() >= UC_ADMINISTRATOR){
$aministration = "<form name=\"adminaccenss\" action=\"mybonus.php\" method=\"post\">";
$aministration .= "<input type=\"hidden\" name=\"action\" value=\"elegor\" />";
$aministration .= "<input type=\"submit\" value=\"Администрирование\" />";
$aministration .= "</form>";
}else{
$aministration = "&nbsp;";
}
begin_frame("Пункт обмена Бонусов");

print("<table width=100% cellspacing=\"1\" cellpadding=4 class=\"main\">".
"<tr>".
"<td colspan=4 class=colhead align=\"center\">На вашем счету <b>$user_bonus</b> бонусов
<br>За каждый час сидирования при отдаче выше 1 КБ/сек, Вы получаете $points_per_hour бонус.</td>".
"</tr>".
"<tr>".
"<td class=row2 width=7><b>&nbsp;№&nbsp;</b></td>".
"<td class=row2><b>Описание</b></td>".
"<td class=row2 align=center><b>Цена</b></td>".
"<td class=row2 align=center><b>Обменять</b></td>".
"</tr>");

$res = mysql_query("SELECT * from mybonus order by bonus_position");

while($exchangeable = mysql_fetch_assoc($res)){
print("<tr>".

"<form action=mybonus.php method=\"post\">".
"<input type=\"hidden\" name=\"exchange\" value=\"true\" />".
"<input type=\"hidden\" name=\"bonus_menge\" value=\"".$exchangeable["bonus_menge"]."\" />".
"<input type=\"hidden\" name=\"user_bonus\" value=\"".$user_bonus."\" />".
"<input type=\"hidden\" name=\"user_id\" value=\"".$user_id."\" />".
"<input type=\"hidden\" name=\"bonus_points\" value=\"".$exchangeable["bonus_points"]."\" />".
"<input type=\"hidden\" name=\"bonus_option\" value=\"".$exchangeable["bonus_position"]."\" />".
"<input type=\"hidden\" name=\"bonus_art\" value=\"".$exchangeable["bonus_art"]."\" />".


"<td class=row3valign=top align=center>".$exchangeable["bonus_position"].".</td>".
"<td class=row3valign=top align=left><b>".$exchangeable["bonus_title"]."</b><br />".$exchangeable["bonus_description"]."</td>".
"<td class=row3align=center><b>".$exchangeable["bonus_points"]."</b> (у Вас <font color=green><b>".$user_bonus."</b></font>)</td>");
if($user_bonus >= $exchangeable["bonus_points"])
print("<td class=row3align=center><input type=\"submit\" style=width:80 value=\"Обменять\" /></td></form>");
else
print("<td class=row3align=center><input type=submit class=\"btn\" value=\"Не хватает\" disabled=\"disabled\"/></td></form>");
print("</tr>");
}
print("<tr>".
"<td colspan=4 class=row2 align=center valign=bottom style=\"border: none\">$aministration</td>".
"</tr>". 
"</table><br>");





if ($use_10proc==1){
echo "<table width=100%  class=\"main\" cellspacing=0 cellpadding=5><tr><td class=\"colhead\" colspan=2>Внимание включена опция</td></tr>";

echo "<tr>
<td class=\"b\" align=\"center\" valign=\"top\" width=\"50%\">
Пригласив пользователей, вы будете получать 10% отданного ими трафика каждый день. Всё, что вам нужно сделать - это отправить им приглашение (форма ниже).<br>
Маленькое условие для пригласителя: Быть на сайте раз месяц и не быть забаненым.<br>
Помните, вы в <u>ответе за приглашенного</u>!
</td>
</tr>";

echo "</table><br>";
}


end_frame();


stdfoot();
?> 