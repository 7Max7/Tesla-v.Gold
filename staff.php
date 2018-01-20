<?
require "include/bittorrent.php";
gzip();
dbconn();
loggedinorreturn();

stdhead("Администрация");
begin_main_frame();
begin_frame("");
?>

<?
$act = (isset($_GET["act"]) ? $_GET["act"]:"");
if (!$act) {
$dt = gmtime() - 300;
$dt = sqlesc(get_date_time($dt));

if (get_user_class()==UC_SYSOP){
	$sqls=" OR override_class<>'255'";
}

$res = sql_query("SELECT override_class,class,id,username,last_access FROM users WHERE (class>=".UC_VIP." AND class<>".UC_UPLOADER." $sqls) AND status='confirmed' ORDER BY username" ) or sqlerr(__FILE__, __LINE__);

while ($arr = mysql_fetch_assoc($res))
{
//спрятать админы в випы 

if (get_user_class()==UC_SYSOP){
if ($arr['override_class']<>255 && !empty($arr['override_class']))
$arr['class']=$arr['override_class'];

unset($arr['override_class']);
}

if($arr['id']=="92")
{
 $arr['class']=UC_USER; 
}


$staff_table[$arr['class']]=(isset($staff_table[$arr['class']])? $staff_table[$arr['class']]:"")."<td class=embedded><a href=userdetails.php?id=".$arr['id']."><b>".get_user_class_color($arr['class'],$arr['username'])."</b></a></td>
".($CURUSER["class"]>"3" ? "<td class=embedded>".("'".$arr['last_access']."'">$dt?"<img src=".$pic_base_url."button_online.gif border=0 alt=\"В сети\">":"<img src=".$pic_base_url."button_offline.gif border=0 alt=\"Не в сети\">" )."</td>" : "")."
<td class=embedded><a href=message.php?action=sendmessage&amp;receiver=".$arr['id'].">".
"<img src=".$pic_base_url."button_pm.gif border=0 alt=\"Отправить сообщение\" ></a></td>".
" ";

// Show 3 staff per row, separated by an empty column
++$col[$arr['class']];
if ($col[$arr['class']]<=2)
$staff_table[$arr['class']]=$staff_table[$arr['class']]."<td class=embedded>&nbsp;</td>";
else
{
$staff_table[$arr['class']]=$staff_table[$arr['class']]."</tr><tr height=15>";
$col[$arr['class']]=0;
}
}
begin_frame("Администрация сайта " .$SITENAME);
?>

<style type="text/css">
<!--
.style3 {
	font-size: 36px;
	font-family: "Times New Roman", Times, serif;
}
.style4 {
	font-size:32px;
	font-family: "Times New Roman", Times, serif;
	color: #006699;
}
.style5 {
	font-size: 48px;
	font-family: "Times New Roman", Times, serif;
	color: #006699;
}
-->
</style>

<table width="100%" cellspacing="0" cellpadding="0" border="0" width="50%">
<td width="100%" style="white-space: nowrap;" colspan="2" >
<p align="center" class="style5">Хорошо быть<br> Администрацией</p>
<p align="center" class="style4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Администрация &reg;</p>
</td>

</table><br/>


<table width="100%" cellspacing="0">



<? if ($staff_table[UC_SYSOP]){ ?>
<tr><td class="a" colspan="11"><font color="blue"><b>Боссы</b></font></td></tr>
<tr><td class="embedded" colspan="11"><br></td></tr>
<tr height="15">
<?=$staff_table[UC_SYSOP]?>
</tr>
<?}?>


<? if ($staff_table[UC_ADMINISTRATOR]){ ?>
<tr><td class="embedded" colspan="11">&nbsp;</td></tr>
<tr><td class="a" colspan="11"><font color="green"><b>Администраторы</b></font></td></tr>
<tr><td class="embedded" colspan="11"><br></td></tr> 
<tr height=15>
<?=$staff_table[UC_ADMINISTRATOR]?>
</tr>
<?}?>

<? if ($staff_table[UC_MODERATOR]){ ?>
<tr><td class="embedded" colspan="11">&nbsp;</td></tr>

<tr><td class="a" colspan="11"><font color="red"><b>Модераторы</b></font></td></tr>
<tr><td class="embedded" colspan="11"><br></td></tr>
<tr height=15>
<?=$staff_table[UC_MODERATOR]?>
</tr>
<?}?>

<? if ($staff_table[UC_VIP]){ ?>
<tr><td class="embedded" colspan="11">&nbsp;</td></tr>
<tr><td class="a" colspan="11"><font color="#9C2FE0"><b>Випы</b></font></td></tr>
<tr><td class="embedded" colspan="11"><br></td></tr>
<tr height=15>
<?=$staff_table[UC_VIP]?>
</tr>
<?}?>

</table>
<?
end_frame();
}
?>

<? 
if (get_user_class() >=UC_MODERATOR){
begin_frame("Ваши функции");} ?>

<? if (get_user_class() >= UC_SYSOP) { ?>

<table width=100% cellspacing=10 align=center>

<td class="embedded"><form method="get" action="staffmess.php"><input type="submit" value="Масовое ПМ" style='height: 20px; width: 100px'></form></td>
<td class="embedded"><form method="get" action="category.php"><input type="submit" value="Категории" style='height: 20px; width: 100px'></form></td>
<td class="embedded"><form method="get" action="spam.php"><input type="submit" value="Удалить ЛС" style='height: 20px; width: 100px'></form></td>

<td class="embedded"><form method="get" action="bannedemails.php"><input type="submit" value="Бан Емайлов" style='height: 20px; width: 100px'></form></td>

<td class="embedded"><form method="get" action="citydd.php"><input type="submit" value="Города и Страны" style='height: 20px; width: 100px'></form></td>

<tr><td class="embedded"><form method="get" action="changeusername.php"><input type="submit" value="Изменить Имя" style='height: 20px; width: 100px'></form></td>
<td class="embedded"><form method="get" action="friensblockadd.php"><input type="submit" value="Блок Друзья" style='height: 20px; width: 100px'></form></td>
<td class="embedded"><form method="get" action="dump3r.php"><input type="submit" value="Бекап Базы" style='height: 20px; width: 100px'></form></td>
<td class="embedded"><form method="get" action="groups.php"><input type="submit" value="Создать Группу" style='height: 20px; width: 100px'></form></td>
<td class="embedded"><form method="get" action="cheaters.php"><input type="submit" value="Читеры" style='height: 20px; width: 100px'></form></td>

<tr>
<td class="embedded"><form method="get" action="info_cache.php"><input type="submit" value="Функция info()" style='height: 20px; width: 100px'></form></td>
<td class="embedded"><form method="get" action="hacklog.php"><input type="submit" value="XSS Hack" style='height: 20px; width: 100px'></form></td>
<td class="embedded"><form method="get" action="sqlerror.php"><input type="submit" value="SQL Ошибки" style='height: 20px; width: 100px'></form></td>

<td class="embedded"><form method="get" action="monitoring.php"><input type="submit" value="Мониторинг" style='height: 20px; width: 100px'></form></td>

</tr>

</tr>
</table>
<? }

if (get_user_class() >= UC_ADMINISTRATOR) { ?>

<table width=100% cellspacing=10 align=center>
<tr>
<td class=embedded><form method=get action="unco.php"><input type=submit value="Неподтв. юзеры" style='height: 20px; width: 100px'></form></td>


<td class=embedded><form method=get action="viewreport.php"><input type=submit value="Просмотр Жалоб" style='height: 20px; width: 100px'></form></td>

<td class=embedded><form method=get action="bans.php"><input type=submit value="Бан IP" style='height: 20px; width: 100px'></form></td>

<td class=embedded><form method=get action="msg.php"><input type=submit value="Все Сообщения" style='height: 20px; width: 100px'></form></td></tr>

<tr>
<td class=embedded><form method=get action="topten.php"><input type=submit value="Top 10" style='height: 20px; width: 100px'></form></td>

<td class=embedded><form method=get action="bans.php"><input type=submit value="Баны" style='height: 20px; width: 100px'></form></td>

<td class=embedded><form method=get action="online.php"><input type=submit value="Кто Где" style='height: 20px; width: 100px'></form></td>

<td class=embedded><form method=get action=adduser.php><input type=submit value="Добавить юзера" style='height: 20px; width: 100px'></form></td>
</tr>

<tr>
<td class=embedded><form method=get action="downcheck.php"><input type=submit value="Синхронизация" style='height: 20px; width: 100px'></form></td>
<td class=embedded><form method=get action="antichiters.php"><input type=submit value="Сидируют" style='height: 20px; width: 100px'></form></td>

<td class="embedded"><form method="get" action="adminbookmark.php"><input type="submit" value="Подозрения" style='height: 20px; width: 100px'></form></td>

<td class=embedded><form method=get action="delacctadmin.php"><input type=submit value="Удалить юзера" style='height: 20px; width: 100px'></form></td>


</tr>



</table>
<? }

if (get_user_class() >= UC_MODERATOR) { ?>

<table width=100% cellspacing=10 align=center>
<tr>
<td class=embedded><form method=get action=warned.php><input type=submit value="Предупр. юзеры" style='height: 20px; width: 100px'></form></td>

<td class=embedded><form method=get action=makepoll.php><input type=submit value="Создать опрос" style='height: 20px; width: 100px'></form></td>
<td class=embedded><form method=get action=recover.php><input type=submit value="Востан. юзера" style='height: 20px; width: 100px'></form></td>
<td class=embedded><form method=get action=uploaders.php><input type=submit value="Аплоадеры" style='height: 20px; width: 100px'></form></td>
</tr>
<tr>
<td class=embedded><form method=get action=stats.php><input type=submit value="Статистика" style='height: 20px; width: 100px'></form></td>
<td class=embedded><form method=get action=users.php><input type=submit value="Список юзеров" style='height: 20px; width: 100px'></form></td>
<td class=embedded><form method=get action=tags.php><input type=submit value="Теги" style='height: 20px; width: 100px'></form></td>

<td class=embedded><form method=get action=usersearch.php><input type=submit value="Поиск Юзера" style='height: 20px; width: 100px'></form></td>

<td class=embedded><form method=get action=smilies.php><input type=submit value="Смайлы" style='height: 20px; width: 100px'></form></td>
</tr>

<tr>
<td class=embedded><form method=get action=testip.php><input type=submit value="Проверка IP" style='height: 20px; width: 100px'></form></td>
<td class=embedded><form method=get action=ipcheck.php><input type=submit value="Повторные IP" style='height: 20px; width: 100px'></form></td>
<td class=embedded><form method=get action=setclass.php><input type=submit value="Смена Прав" style='height: 20px; width: 100px'></form></td>

<td class=embedded><form method=get action=comments_last.php><input type=submit value="Посл.комменты" style='height: 20px; width: 100px'></form></td>

<td class=embedded><form method=get action=votes.php><input type=submit value="Обзор оценок" style='height: 20px; width: 100px'></form></td>

</tr>



</table>



<br>
<table width=100% cellspacing=3>
<tr>
<? if (get_user_class() >= UC_MODERATOR) { ?>
</tr>
<tr>
<td class=embedded><a href=staff.php?act=users>Пользователи с рейтингом ниже 0.20</a></td>
<td class=embedded>Показать всех пользователей с рейтингом ниже чем 0.20</td>
</tr>
<tr>
<td class=embedded><a href=staff.php?act=banned>Отключенные пользователи</a></td>
<td class=embedded>Показать всех отключенных пользователей</td>
</tr>
<tr>
<td class=embedded><a href=staff.php?act=last>Новые пользователи</a></td>
<td class=embedded>Последние 40 пользователей</td>
</tr>
<tr>
<td class=embedded><a href=log.php>Лог сайта</a></td>
<td class=embedded>Показать что было залито/удалено/и тд</td>
</tr>

<tr><td  class="embedded"><a href="usersearch.php">Административный поиск</a></td></tr>
</table>



<table width=100% cellspacing=3>
<tr>
<td class="embedded">
<form method=get action="users.php">
<b>Поиск</b>: <input type=text size="40" name=search>
<select name=class>
<option value='-'>все</option>
<option value=0>Пользователь</option>
<option value=1>Опытный пользователь</option>
<option value=2>VIP</option>
<option value=3>Заливающий</option>
<option value=4>Модератор</option>
<option value=5>Администратор</option>
<option value=6>Владелец</option>
</select>
<input type=submit value='Искать человека'>
</form>
</td>
</tr>

</table>

<? end_frame(); ?>
<br />
<? if ($act == "users") {
begin_frame("Пользователи с рейтингом ниже 0.20");

echo '<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">';
echo "<tr><td class=colhead align=left>Пользователь</td><td class=colhead>Рейтинг</td><td class=colhead>IP</td><td class=colhead>Зарегистрирован</td><td class=colhead>Последний вход</td><td class=colhead>Скачал</td><td class=colhead>Раздал</td></tr>";


$result = sql_query ("SELECT * FROM users WHERE uploaded / downloaded <= 0.20 AND enabled = 'yes' ORDER BY downloaded DESC ");
if ($row = mysql_fetch_array($result)) {
do {
if ($row["uploaded"] == "0") { $ratio = "Infinity"; }
elseif ($row["downloaded"] == "0") { $ratio = "Infinity"; }
$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
echo "<tr><td>
<a href=userdetails.php?id=".$row["id"].">
<b>".get_user_class_color($row["class"] ,$row["username"])."</b>
</a></td><td><strong>".$ratio."</strong></td><td>".$row["ip"]."</td><td>".$row["added"]."</td><td>".$row["last_access"]."</td><td>".mksize($row["downloaded"])."</td><td>".mksize($row["uploaded"])."</td></tr>";


} while($row = mysql_fetch_array($result));
} else {print "<tr><td colspan=7>Извините, записей не обнаружено!</td></tr>";}
echo "</table>";
end_frame(); }?>

<? if ($act == "last") {
begin_frame("Последние 40 пользователей");

echo '<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">';
echo "<tr><td class=colhead align=left>Пользователь</td><td class=colhead>Рейтинг</td><td class=colhead>IP адресс</td><td class=colhead>Зарегистрирован</td><td class=colhead>Последний&nbsp;раз&nbsp;был&nbsp;</td><td class=colhead>Скачал</td><td class=colhead>Раздал</td></tr>";

$result = sql_query ("SELECT * FROM users WHERE enabled = 'yes' AND status = 'confirmed' ORDER BY added DESC limit 40");
if ($row = mysql_fetch_array($result)) {
do {
if ($row["uploaded"] == "0") { $ratio = "Infinity"; }
elseif ($row["downloaded"] == "0") { $ratio = "Infinity"; }
else {
$ratio = number_format($row["uploaded"] / $row["downloaded"], 3);
$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
}
echo "<tr><td>
<a href=userdetails.php?id=".$row["id"].">
<b>
".get_user_class_color($row["class"], $row["username"])."</b>
</a></td><td><strong>".$ratio."</strong></td>
<td>".$row["ip"]."</td><td>".$row["added"]."</td><td>".$row["last_access"]."</td><td>".mksize($row["downloaded"])."</td><td>".mksize($row["uploaded"])."</td></tr>";


} while($row = mysql_fetch_array($result));
} else {print "<tr><td>Извините, записей не найденно!</td></tr>";}
echo "</table>";
end_frame(); }?>


<? if ($act == "banned") {
begin_frame("Отключенные пользователи");

echo '<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">';
echo "<tr><td class=colhead align=left>Пользователь</td><td class=colhead>Рейтинг</td><td class=colhead>IP</td><td class=colhead>Зарегистрирован</td><td class=colhead>Последний Вход</td><td class=colhead>Скачал</td><td class=colhead>Раздал</td></tr>";
$result = sql_query ("SELECT * FROM users WHERE enabled = 'no' ORDER BY last_access DESC ");
if ($row = mysql_fetch_array($result)) {
do {
if ($row["uploaded"] == "0") { $ratio = "Infinity"; }
elseif ($row["downloaded"] == "0") { $ratio = "Infinity"; }
else {
$ratio = number_format($row["uploaded"] / $row["downloaded"], 3);
$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
}
echo "<tr><td>
<a href=userdetails.php?id=".$row['id'].">
<b>".get_user_class_color($row["class"] ,$row["username"])."</b>
</a>

</td><td><strong>".$ratio."</strong></td><td>".$row["ip"]."</td><td>".$row["added"]."</td><td>".$row["last_access"]."</td><td>".mksize($row["downloaded"])."</td><td>".mksize($row["uploaded"])."</td></tr>";


} while($row = mysql_fetch_array($result));
} else {print "<tr><td colspan=7>Извините, записей не обнаружено!</td></tr>";}
echo "</table>";
end_frame(); } }



}
if (get_user_class() >= UC_USER) {

if (!$act) {
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));

$dti = gmtime() - 1209600; //// 2 недели
$dti = sqlesc(get_date_time($dti));

$res = sql_query("SELECT u.class,u.username,u.id,u.country,u.last_access,u.supportfor,
c.name,c.flagpic
 FROM users AS u INNER JOIN countries AS c ON u.country = c.id 
 WHERE u.last_access > $dti AND u.support='yes' AND u.enabled='yes' AND u.status='confirmed' ORDER BY u.username LIMIT 10") or sqlerr(__FILE__, __LINE__);
 $support3=1;
while ($arr = mysql_fetch_assoc($res))
{
	/*
	8 (queries) - 80.86% (php) - 19.14% (0.0153 => sql) - 1577 КБ (use memory)
	
	8 (queries) - 84.37% (php) - 15.63% (0.0128 => sql) - 1577 КБ (use memory)
	*/

if (!$arr["flagpic"]) {
($arr["name"]="Приднестровье");
($arr["flagpic"]="pmr.gif");}


$firstline .= "<tr height=15>
<td class=embedded><b>$support3</b>.
</td>
<td class=embedded>
<a href=userdetails.php?id=".$arr['id'].">
<b>
<font color=\"#".get_user_rgbcolor($arr['class'], $arr['username'])."\">".$arr['username']."</font>
</b>
</a>
</td>
<td class=embedded> ".("'".$arr['last_access']."'">$dt?"<img src=".$pic_base_url."button_online.gif border=0 alt=\"В сети\">":"<img src=".$pic_base_url."button_offline.gif border=0 alt=\"Не в сети\">" )."</td>".
"<td class=embedded><a href=message.php?action=sendmessage&amp;receiver=".$arr['id'].">"."<img  alt=\"Написать в личку\" src=".$pic_base_url."button_pm.gif border=0></a></td>".
"<td class=embedded><img src=".$pic_base_url."/flag/$arr[flagpic] title=\"$arr[name]\" border=0 width=19 height=12></td>".
"<td class=embedded>".$arr['supportfor']."</td></tr>\n";
$support3++;
}
if ($firstline) {
begin_frame("Первая линия тех поддержки");
if (get_user_class() >= UC_VIP) (
$techpro="В крайнем случае пишем в <a href=\"support.php\">техподдержку</a>.");
?>

<table width=100% cellspacing=0>
<tr>
<td class=embedded colspan=11><center>Общие вопросы лучше задавать этим пользователям. Учтите что они добровольцы, тратящие свое время и силы на помощь вам. Относитесь к ним подобающе. <?=$techpro?></center><br/></td></tr>

<tr>
<td class=embedded width="3"><b>№</b></td>
<td class=embedded width="30"><b>Поможет</b></td>
<td class=embedded width="5"><b>Активен</b></td>
<td class=embedded width="5"><b>ПМ</b></td>
<td class=embedded width="40"><b>Страна</b></td>
<td class=embedded width="200"><b>Описание</b></td>
</tr>

<tr>
<tr><td class=embedded colspan=11><hr></td></tr>
<?=$firstline?>
</tr>
</table>
<?
end_frame();
}}
?>

<?
end_frame();
end_main_frame();
stdfoot();
}
?>