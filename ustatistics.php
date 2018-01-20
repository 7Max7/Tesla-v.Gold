<?php
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn();

if (get_user_class() < UC_ADMINISTRATOR) {
attacks_log('statistics'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

stdheadchat("Центр индивидуальной статистики");
$base_url = "$DEFAULTBASEURL/ustatistics.php";

$id = $_GET["id"];
if (empty($id) && !empty($_POST["id"]))
$id = $_POST["id"];

define('ID', $id);

//var_dump($_POST);

if (!is_valid_id($id) && !$_POST){

echo "<table width=\"100%\" align=\"center\" id='torrenttable' border='0'>";

echo "<tr><td class='b'><form method=\"get\" action=\"ustatistics.php\">

<b>Впишите id пользователя</b>: <input type=\"text\" id=\"searchinput\" name=\"id\" size=\"20\" class=\"searchgif\" value=\"".$CURUSER["id"]."\"/>
<input class=\"btn\" type=\"submit\" style=\"width: 300px\" value=\"Показать панель статистики\" />
</form>

</td>
</tr>";



echo "<tr><td class='b'>";

$rese = sql_query("SELECT id, username FROM users WHERE class >= ".UC_MODERATOR." ORDER BY username") or sqlerr(__FILE__,__LINE__);

echo "<form method=\"get\" action=\"ustatistics.php\" name=\"jump\">\n";
echo "<b>Быстрый поиск из списка Администрация</b>: ";
echo "<select name=\"id\" onchange=\"if(this.options[this.selectedIndex].value != -1){ forms['jump'].submit() }\">\n";

while ($arre = mysql_fetch_assoc($rese))
echo "<option value=\"" . $arre["id"]."\">".$arre["username"]."</option>\n";

echo "</select>\n";
echo "<input type=\"submit\" class=\"btn\" style=\"width: 300px\" value=\"Показать панель статистики\">\n";
echo "</form>";

echo "</td></tr>";




echo "</table>";
stdfootchat();
die;
}

$res = sql_query("SELECT id, username, class FROM users WHERE id = ".sqlesc(ID))  or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$username = "<a href=\"userdetails.php?id=".$row["id"]."\">".get_user_class_color($row["class"], $row["username"])."</a>";

if ($row){
echo "<table width=\"100%\" align=\"center\" id='torrenttable' border='0'>
<tr>
<td class='b'><a href=\"ustatistics.php\">Центр индивидуальной статистики</a> по $username для различных ситуаций на трекере.</td>
</tr>
</table>
<br />";
} else {

echo "<form method=\"get\" action=\"ustatistics.php\">
<table width=\"100%\" align=\"center\" id='torrenttable' border='0'>
<tr>
<td class='b'>Не найден логин для этого id, Впишите id пользователь заново: <input type=\"text\" id=\"searchinput\" name=\"id\" size=\"20\" class=\"searchgif\" value=\"".$CURUSER["id"]."\" />
<input class=\"btn\" type=\"submit\"  style=\"width: 300px\" value=\"Показать панель статистики\" />
</form>
</td>
</tr>
</table>
<br />";	
}


echo "<table width=\"100%\" align=\"center\" id='torrenttable' border='0'>
<tr>

<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='ustatistics.php?act=stats&code=rate&id=".$id."'>Статистика Оценок</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='ustatistics.php?act=stats&code=offers&id=".$id."'>Статистика Запросов | Предложений</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='ustatistics.php?act=stats&code=msg&id=".$id."'>Статистика Сообщений</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='ustatistics.php?act=stats&code=torr&id=".$id."'>Статистика Торентов</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='ustatistics.php?act=stats&code=check&id=".$id."'>Статистика Одобрений</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='ustatistics.php?act=stats&code=comm&id=".$id."'>Статистика Комментариев</a></td>
</tr>

<tr>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='ustatistics.php?act=stats&code=humor&id=".$id."'>Статистика Анекдотов</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='ustatistics.php?act=stats&code=simpaty&id=".$id."'>Статистика Симпатий</a></td>		
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='ustatistics.php?act=stats&code=log&id=".$id."'>Статистика Журнала</a></td>	
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='ustatistics.php?act=stats&code=refer&id=".$id."'>Статистика Рефералов</a></td>	
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='ustatistics.php?act=stats&code=tfiles&id=".$id."'>Статистика Обменника</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='ustatistics.php?act=stats&code=snatched&id=".$id."'>Статистика Скачиваний</a></td>
</tr>
</table>
<br />";


function start_form($hiddens="", $name='theAdminForm', $js="") {
global $base_url;

$form = "<form action='{$base_url}' method='post' name='$name' $js>";

if (is_array($hiddens))
{
foreach ($hiddens as $k => $v) {
$form .= "\n<input type='hidden' name='{$v[0]}' value='{$v[1]}'>";
}
}

$form .= "\n<input type='hidden' name='id' value='".ID."'>";

return $form;

}


function form_dropdown($name, $list=array(), $default_val="", $js="", $css="") {

if ($js != "") {
$js = ' '.$js.' ';
}

if ($css != "") {
$css = ' class="'.$css.'" ';
}

$html = "<select name='$name'".$js." $css class='dropdown'>\n";

foreach ($list as $k => $v) {

$selected = "";

if (($default_val != "") and ($v[0] == $default_val)) {
$selected = ' selected';
}

$html .= "<option value='".$v[0]."'".$selected.">".$v[1]."</option>\n";
}

$html .= "</select>\n\n";

return $html;
}

function end_form($text = "", $js = "", $extra = "") {
$html    = "";
$colspan = "";
$td_colspan = 0;

if ($text != "") {
if ($td_colspan > 0) {
$colspan = " colspan='".$td_colspan."' ";
}

$html .= "<tr><td align='center' class='b'".$colspan."><input type='submit' class='btn' value='$text'".$js." id='button' accesskey='s'>{$extra}</td></tr>\n";
}

$html .= "</form>";

return $html;
}

$month_names = array();

$tmp_in = array_merge($_GET, $_POST );

foreach ($tmp_in as $k => $v) {
unset($$k);
}

$month_names = array( 1 => 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',  'Июль', 'Август', 'Сентябрь' , 'Октябрь', 'Ноябрь', 'Декабрь');

if(isset($tmp_in['code']) && $tmp_in['code'] != "") {

switch($tmp_in['code']) {


case 'show_rate':
result_screen('rate');
break;

case 'rate':
main_screen('rate');
break;

case 'show_post':
result_screen('post');
break;

case 'post':
main_screen('post');
break;


case 'show_snatched':
result_screen('snatched');
break;

case 'snatched':
main_screen('snatched');
break;

case 'show_refer':
result_screen('refer');
break;

case 'refer':
main_screen('refer');
break;


case 'show_log':
result_screen('log');
break;

case 'log':
main_screen('log');
break;

case 'show_msg':
result_screen('msg');
break;

case 'msg':
main_screen('msg');
break;

case 'show_torr':
result_screen('torr');
break;

case 'torr':
main_screen('torr');
break;

case 'show_check':
result_screen('check');
break;

case 'check':
main_screen('check');
break;

case 'show_comm':
result_screen('comm');
break;

case 'comm':
main_screen('comm');
break;

case 'show_tfiles':
result_screen('tfiles');
break;

case 'tfiles':
main_screen('tfiles');
break;

case 'show_humor':
result_screen('humor');
break;

case 'humor':
main_screen('humor');
break;

case 'show_simpaty':
result_screen('simpaty');
break;

case 'simpaty':
main_screen('simpaty');
break;

case 'show_offers':
result_screen('offers');
break;

case 'offers':
main_screen('offers');
break;

default:
main_screen('tfiles');
break;
}
}




function result_screen($mode='tfiles') {
global $month_names;

$page_title = "<h2>Результаты центра статистики</h2>";
$page_detail = "&nbsp;";

if (!checkdate($_POST['to_month'],$_POST['to_day'],$_POST['to_year'])) {
die("'Дата от:' неверное время, пожалуйста проверьте и попробуйте снова");
}

if (!checkdate($_POST['from_month'], $_POST['from_day'], $_POST['from_year'])) {
die("'Дата до:' неверное время, пожалуйста проверьте и попробуйте снова");
}

$to_time   = mktime(12 ,0 ,0 ,$_POST['to_month']   ,$_POST['to_day']   ,$_POST['to_year']  );
$from_time = mktime(12 ,0 ,0 ,$_POST['from_month'] ,$_POST['from_day'] ,$_POST['from_year']);
//$sql_date_to = date("Y-m-d",$to_time);
//$sql_date_from = date("Y-m-d",$from_time);

$human_to_date   = getdate($to_time);
$human_from_date = getdate($from_time);


$res = sql_query("SELECT id, username, class FROM users WHERE id = ".sqlesc(ID)."")  or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$username = "<a href=\"userdetails.php?id=".$row["id"]."\">".get_user_class_color($row["class"], $row["username"])."</a>";

if ($mode == 'rate') {
$table = 'Статистика оценок торрентов для';

$sql_table = 'ratings';
$wherer = "user = ".ID;
$sql_field = 'added';

$page_detail = "Рейтинг торрентов от ";
}

else if ($mode == 'msg') {
$table  = 'Статистика личных сообщений';

$sql_table = 'messages';
$wherer = "(sender = ".ID." OR receiver = ".ID.")";
$sql_field = 'added';

$page_detail = "Показывает количество отправленных - полученных сообщений для";
}

else if ($mode == 'snatched') {
$table = 'Статистика Взятых и Последующего скачивания этого торрента для';

$sql_table = 'snatched';
$wherer = "userid = ".ID."";
$sql_field = 'startdat';

$page_detail = "Показывает количество взятых торрентов для";
}

else if ($mode == 'refer') {
$table = 'Статистика Рефералов с';

$sql_table = 'referrers';
$wherer = "uid = ".ID."";
$sql_field = 'date';

$page_detail = "Показывает количество рефералов для";
}
else if ($mode == 'log') {
$table = 'Статистика Журнала для';

$sql_table = 'sitelog';
$wherer = "txt LIKE '%".$row["username"]."%'"; /// тут имя
$sql_field = 'added';

$page_detail = "Показывает количество добавленных событий в журнале по поиску логина";
}
else if ($mode == 'torr') {
$table = 'Статистика залитых торрентов';

$sql_table = 'torrents';
$wherer = "owner = ".ID."";
$sql_field = 'added';

$page_detail = "Показывает количество залитых торрентов для";
}
else if ($mode == 'check') {
$table = 'Статистика одобрений для';

$sql_table = 'torrents';
$wherer = "	moderatedby = ".ID."";
$sql_field = 'moderatordate';

$page_detail = "Показывает количество одобрений ";
}

else if ($mode == 'comm') {
$table = 'Статистика комментариев от';

$sql_table = 'comments';
$wherer = "	user = ".ID."";
$sql_field = 'added';

$page_detail = "Показывает количество комментарий к торрентам от";
}

else if ($mode == 'simpaty'){
$table = 'Статистика симпатий (принятых и отправленых)';

$sql_table = 'simpaty';
$wherer = "(touserid = ".ID." OR fromuserid = ".ID.")";
$sql_field = 'respect_time';

$page_detail = "Показывает количество симпатий (общих)";
}


else if ($mode == 'offers'){
$table = 'Статистика предложений от';

$sql_table = 'off_reqs';
$wherer = "(owner = ".ID." OR fulfilled = ".ID.")";
$sql_field = 'added';

$page_detail = "Показывает количество предложений или запросов ";
}

else if ($mode == 'tfiles'){
$table = 'Статистика обменника в заливке от';

$sql_table = 'attachments';
$wherer = "uploadby = ".ID."";
$sql_field = 'added';

$page_detail = "Показывает количество залитых файлов в файлоообменник от";
}
else if ($mode == 'humor'){
$table = 'Статистика анекдотов';

$sql_table = 'humor';
$wherer = "uid = ".ID."";
$sql_field = 'date';

$page_detail = "Показывает количество залитых анекдотов от";
}

if (!empty($wherer))
$wherer = "AND $wherer";
else
$wherer = "";

switch ($_POST['timescale']) {
case 'daily':
$sql_date = "%w %U %m %Y";
$php_date = "F jS - Y";
//$sql_scale = "DAY";
break;

case 'monthly':
$sql_date = "%m %Y";
$php_date = "F Y";
//$sql_scale = "MONTH";
break;

default:
// weekly
$sql_date = "%U %Y";
$php_date = " [F Y]";
//$sql_scale = "WEEK";
break;
}

$sortby = isset($_POST['sortby']) ? mysql_real_escape_string($_POST['sortby']) : "";
//$sortby = sqlesc($sortby);
$sqlq = "SELECT UNIX_TIMESTAMP(MAX({$sql_field})) as result_maxdate,
COUNT(*) as result_count,
DATE_FORMAT({$sql_field},'{$sql_date}') AS result_time
FROM {$sql_table}
WHERE UNIX_TIMESTAMP({$sql_field}) > '{$from_time}' $wherer
AND UNIX_TIMESTAMP({$sql_field}) < '{$to_time}'
GROUP BY result_time
ORDER BY {$sql_field} {$sortby}";

$res = @sql_query($sqlq);
/*        $res = @mysql_query( "SELECT UNIX_TIMESTAMP(MAX(added)) as result_maxdate,
COUNT(*) as result_count,
".$sql_scale."(".$sql_field.") AS result_time
FROM ".$sql_table."
WHERE added > '".$sql_date_from."'
AND added < '".$sql_date_to."'
GROUP BY result_time
ORDER BY ".$sql_field); */


$running_total = 0;
$max_result = 0;

$results = array();
//$td_header = array();
//$td_header[] = array( "Date"    , "20%" );
//$td_header[] = array( "Result"  , "70%" );
//$td_header[] = array( "Count"   , "10%" );
if ($_POST['timescale']=="daily"){$view="Ежедневная";}
if ($_POST['timescale']=="weekly"){$view="Еженедельная";}
if ($_POST['timescale']=="monthly"){$view="Ежемесячная";}



$html = $page_title."<br />
<table width=\"100%\" align=\"center\" id=torrenttable border=1><tr><td colspan=3 class='b'>".$view." ".$table ." от ({$human_from_date['mday']} {$month_names[$human_from_date['mon']]} {$human_from_date['year']} до "." {$human_to_date['mday']} {$month_names[$human_to_date['mon']]} {$human_to_date['year']})<br />{$page_detail} - $username</td></tr>\n";

if (mysql_num_rows($res)) {

while ($row = mysql_fetch_assoc($res)) {

if ( $row['result_count'] >  $max_result ) {
$max_result = $row['result_count'];
}

$running_total += $row['result_count'];

$results[] = array(
'result_maxdate'  => $row['result_maxdate'],
'result_count'    => $row['result_count'],
'result_time'     => $row['result_time'],
);

}
$srednee = number_format ($running_total / mysql_num_rows($res),0);

foreach($results as $pOOp => $data) {

$img_width = intval( ($data['result_count'] / $max_result) * 100 - 20);

if ($img_width < 1) {
$img_width = 1;
}

$img_width .= '%';

if ($_POST['timescale'] == 'weekly') {
$date = "Неделя #".strftime("%W", $data['result_maxdate'])."<br />" . date($php_date, $data['result_maxdate']);
}
else
{
// $date = date($php_date, $data['result_maxdate'] );
$date = normaltime(get_date_time($data['result_maxdate']),true); 
}

$html .= "<tr>
<td class=b width=30%>" .$date . "</td>
<td class=a width=70%><img src='themes/black_night/images/bar_left.gif' border='0' width='4' height='11' align='middle' alt=''><img src='themes/black_night/images/bar.gif' border='0' width='$img_width' height='11' align='middle' alt=''><img src='themes/black_night/images/bar_right.gif' border='0' width='4' height='11' align='middle' alt=''>
</td>
<td align=right class=b width=5%>".$data['result_count']."</td>
</tr>\n";
}
$html .= '<tr><td colspan=3 class=b>&nbsp;'. "<div align='center'>Всего: <b>".$running_total."</b> Среднее: <b>".$srednee."</b></div></td></tr>\n";
}
else
{
$html .= "<tr><td class=\"a\" align=\"center\">Нет результатов, данные пусты. <br> Попробуйте другой вариант для поиска.</td></tr>\n" ;
}

print $html."</table>\n<br />";
}




function main_screen($mode='tfiles') {
global $month_names;

$page_title = "Центр статистики";

$page_detail = "Пожалуйста определите даты и другие опции внизу.<br><b>Примечание</b>: Статистика основывается на данных, находящихся сейчас в базе. Она не учитывает удаленные аккаунты, сообщения итд.";

if ($mode == 'rate')
{
$form_code = 'show_rate';

$table     = 'Rating Statistics';
}    

else if ($mode == 'msg')
{
$form_code = 'show_msg';

$table     = 'PM Statistics';
}

else if ($mode == 'torr')
{
$form_code = 'show_torr';

$table     = 'Torrent Statistics';
}

else if ($mode == 'check')
{
$form_code = 'show_check';

$table     = 'Torrent Statistics';
}

else if ($mode == 'snatched')
{
$form_code = 'show_snatched';

$table     = 'Torrent Statistics';
}


else if ($mode == 'refer')
{
$form_code = 'show_refer';

$table     = 'Torrent Statistics';
}
else if ($mode == 'comm')
{
$form_code = 'show_comm';

$table     = 'Comment Statistics';
}

else if ($mode == 'tfiles')
{
$form_code = 'show_tfiles';

$table     = 'Статистика обменника';
}

else if ($mode == 'humor')
{
$form_code = 'show_humor';

$table     = 'Статистика Анекдотов';
}

else if ($mode == 'log')
{
$form_code = 'show_log';

$table     = 'Статистика Журнала';
}

else if ($mode == 'simpaty')
{
$form_code = 'show_simpaty';

$table     = 'Статистика симпатий';
}


else if ($mode == 'offers')
{
$form_code = 'show_offers';

$table     = 'Offers Statistics';
}

$old_date = getdate(time() - (3600 * 24 * 90));
$new_date = getdate(time() + (3600 * 24));

$html =  "<table width=\"100%\" align=\"center\" id=torrenttable border=1>";
$html .= "<tr><td class=\"b\">$page_detail</td></tr>";

$html .= start_form(array( 1 => array( 'code'  , $form_code  ),2 => array( 'act', 'stats'),));

$html .= "<tr><td class=\"a\"><b>Дата от </b>" .form_dropdown( "from_month" , make_month(), $old_date['mon']  ).'&nbsp;&nbsp;'.form_dropdown( "from_day"   , make_day()  , $old_date['mday'] ).'&nbsp;&nbsp;'.form_dropdown( "from_year"  , make_year() , $old_date['year'] )."<br /></td></tr>";

$html .= "<tr><td class=\"a\"><b>Дата до </b>" .form_dropdown( "to_month" , make_month(), $new_date['mon']  ).'&nbsp;&nbsp;'.form_dropdown( "to_day"   , make_day()  , $new_date['mday'] ).'&nbsp;&nbsp;'.form_dropdown( "to_year"  , make_year() , $new_date['year'] ) ."<br /></td></tr>";

if ($mode != 'views') {
$html .= "<tr><td class=\"a\"><b>Временная шкала</b> " .form_dropdown( "timescale" , array( 0 => array( 'daily', 'Ежедневно'), 1 => array( 'weekly', 'Еженедельно' ), 2 => array( 'monthly', 'Ежемесячно' ) ) ) ."<br /></td></tr>";
}

$html .= "<tr><td class=\"a\"><b>Сортировка</b> " .form_dropdown( "sortby" , array( 0 => array( 'asc', 'Сначала старые даты'), 1 => array( 'desc', 'Сначала новые даты' ) ), 'desc' ) ."<br /></td></tr>";

$html .= end_form("Показать статистику")."</table>";

print $html;
}

function make_year() {
$time_now = getdate();

$return = array();

$start_year = 2002;

$latest_year = intval($time_now['year']);

if ($latest_year == $start_year)
{
$start_year -= 1;
}

for ( $y = $start_year; $y <= $latest_year; $y++ )
{
$return[] = array( $y, $y);
}

return $return;
}

//-----------------------------------------

function make_month() {
global $month_names;
reset($month_names);
$return = array();

for ( $m = 1 ; $m <= 12; $m++ )
{
$return[] = array( $m, $month_names[$m] );
}

return $return;
}


function make_day() {
$return = array();

for ( $d = 1 ; $d <= 31; $d++ )
{
$return[] = array( $d, $d );
}

return $return;
}

stdfootchat();
?>