<?php
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn();

if (get_user_class() < UC_ADMINISTRATOR) {
attacks_log('statistics'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

stdheadchat("Центр глобальной статистики");
$base_url = "$DEFAULTBASEURL/statistics.php";

?>               

<table width="100%" align="center" id='torrenttable' border='0'>

<tr>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=reg'>Статистика Регистраций</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=rate'>Статистика Оценок</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=offers'>Статистика Запросов | Предложений</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=msg'>Статистика Сообщений</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=log'>Статистика Журнала</a></td>
</tr>
<tr>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=bans'>Статистика Банов по ip</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=bansmail'>Статистика Банов по Почте</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=comm'>Статистика Комментариев</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=new'>Статистика Новостей</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=poll'>Статистика Опросов</a></td>
</tr>
<tr>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=humor'>Статистика Анекдотов</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=login'>Статистика Попыток Входа</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=simpaty'>Статистика Симпатий</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=refer'>Статистика Рефералов</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=away'>Статистика Переходов</a></td>
</tr>
<tr>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=tags'>Статистика Тегов (меток)</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=cloud'>Статистика Поисковых слов</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=useragent'>Статистика Браузеров</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=tfiles'>Статистика Обменника</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=snatched'>Статистика Скачиваний</a></td>
</tr>


<tr>
<td class='a' align="center" colspan="8">Статистика Форума</td>
</tr>

<tr>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=topics'>Обновления Тем</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=post'>Написания Постов</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=editpost'>Редактирования постов</a></td>
</tr>


<tr>
<td class='a' align="center" colspan="8">Статистика функций торрентов (включая проверок)</td>
</tr>

<tr>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=multi_time'>Мультитрекера</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=checkpeers'>Пиров и комментариев</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=last_action'>Последней активности</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=last_reseed'>Просьб просидировать</a></td>
<td class='b'>&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=stop_time'>Вип раздач</a></td>
</tr>

<tr>
<td class='b' colspan="8">&nbsp;<img src='pic/slider_knob.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='statistics.php?act=stats&code=torr'>Заливка Торрентов</a></td>
</tr>

</table>
<br />
<?





function begin_desc($mode=false) {

if ($mode == 'reg')
$table = 'Статистика регистраций';
elseif ($mode == 'rate')
$table = 'Статистика оценок торрентов';
elseif ($mode == 'post')
$table = 'Статистика создания тем и сообщений на форуме';
elseif ($mode == 'editpost')
$table = 'Статистика отредактированных тем и сообщений на форуме';
elseif ($mode == 'away')
$table = 'Статистика переходов (уходов по ссылкам)';
elseif ($mode == 'msg')
$table = 'Статистика личных сообщений';
elseif ($mode == 'bansmail')
$table = 'Статистика Банов по почте';
elseif ($mode == 'topics')
$table = 'Статистика Обнволения тем на форуме';
elseif ($mode == 'snatched')
$table  = 'Статистика Взятых и Последущих скачиваний торрента';
elseif ($mode == 'useragent')
$table = 'Статистика Браузеров';
elseif ($mode == 'cloud')
$table = 'Статистика Поисковых слов';
elseif ($mode == 'refer')
$table = 'Статистика Рефералов';
elseif ($mode == 'log')
$table = 'Статистика Журнала';
elseif ($mode == 'torr')
$table  = 'Статистика торрентов';
elseif ($mode == 'tags')
$table = 'Статистика добавленных тегов';
elseif ($mode == 'bans')
$table  = 'Статистика банов';
elseif ($mode == 'comm')
$table = 'Статистика комментариев';
elseif ($mode == 'new')
$table = 'Статистика новостей';
elseif ($mode == 'poll')
$table = 'Статистика опросов';
elseif ($mode == 'simpaty')
$table = 'Статистика симпатий';
elseif ($mode == 'offers')
$table = 'Статистика предложений';
elseif ($mode == 'tfiles')
$table = 'Статистика обменника';
elseif ($mode == 'humor')
$table = 'Статистика анекдотов';
elseif ($mode == 'login')
$table = 'Статистика попыток входов';
elseif ($mode == 'multi_time')
$table = 'Статистика проверки последнего доступа к внешним аннонсам (взятие количество пиров)';
elseif ($mode == 'checkpeers')
$table = 'Статистика проверки Пиров и комментариев для торрентов (как в cleanup)';
elseif ($mode == 'last_action')
$table = 'Статистика проверки Последней активности торрентов (когда сидируют - помечается последним этим же доступом)';
elseif ($mode == 'stop_time')
$table = 'Статистика времени Vip Раздачи (время - когда заканчивается срок этой раздачи)';
elseif ($mode == 'last_reseed')
$table = 'Статистика Просьб встать на раздачу (просидировать торрент)';

else
$table = "Не найдено описание для $mode действия.";

return $table;
}




function start_form($hiddens = false, $name='theAdminForm', $js = false) {
global $base_url;

$form = "<form action='{$base_url}' method='post' name='$name' $js>";

if (is_array($hiddens)) {
foreach ($hiddens as $k => $v) {
$form.= "\n<input type='hidden' name='{$v[0]}' value='{$v[1]}'>";
}
}

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

$tmp_in = array_merge( $_GET, $_POST );

foreach ($tmp_in as $k => $v) {
unset($$k);
}

$month_names = array( 1 => 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',  'Июль', 'Август', 'Сентябрь' , 'Октябрь', 'Ноябрь', 'Декабрь');

if(isset($tmp_in['code']) && $tmp_in['code'] != "") {

switch($tmp_in['code']) {
case 'show_reg':
result_screen('reg');
break;

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

case 'show_editpost':
result_screen('editpost');
break;

case 'editpost':
main_screen('editpost');
break;

case 'show_away':
result_screen('away');
break;

case 'away':
main_screen('away');
break;

case 'show_topics':
result_screen('topics');
break;

case 'topics':
main_screen('topics');
break;

case 'show_post':
result_screen('post');
break;

case 'post':
main_screen('post');
break;

case 'show_bansmail':
result_screen('bansmail');
break;

case 'bansmail':
main_screen('bansmail');
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

case 'show_useragent':
result_screen('useragent');
break;

case 'useragent':
main_screen('useragent');
break;

case 'show_cloud':
result_screen('cloud');
break;

case 'cloud':
main_screen('cloud');
break;

case 'show_tags':
result_screen('tags');
break;

case 'tags':
main_screen('tags');
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

case 'show_bans':
result_screen('bans');
break;

case 'bans':
main_screen('bans');
break;

case 'show_comm':
result_screen('comm');
break;

case 'comm':
main_screen('comm');
break;

case 'show_new':
result_screen('new');
break;

case 'new':
main_screen('new');
break;

case 'show_poll':
result_screen('poll');
break;

case 'poll':
main_screen('poll');
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

case 'show_login':
result_screen('login');
break;

case 'login':
main_screen('login');
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


case 'show_multi_time':
result_screen('multi_time');
break;

case 'multi_time':
main_screen('multi_time');
break;


case 'show_checkpeers':
result_screen('checkpeers');
break;

case 'checkpeers':
main_screen('checkpeers');
break;


case 'show_last_action':
result_screen('last_action');
break;

case 'last_action':
main_screen('last_action');
break;

case 'show_last_reseed':
result_screen('last_reseed');
break;

case 'last_reseed':
main_screen('last_reseed');
break;


case 'show_stop_time':
result_screen('stop_time');
break;

case 'stop_time':
main_screen('stop_time');
break;


default:
main_screen('reg');
break;
}
}



function result_screen($mode='reg') {
global $month_names;

$page_title = "<h2>Результаты центра статистики</h2>";

$page_detail = "&nbsp;";

//-----------------------------------------

if (!checkdate($_POST['to_month'],$_POST['to_day'],$_POST['to_year'])) {
die("'Дата от:' неверное время, пожалуйста проверьте и попробуйте снова");
}

if (!checkdate($_POST['from_month'], $_POST['from_day'], $_POST['from_year'])) {
die("'Дата до:' неверное время, пожалуйста проверьте и попробуйте снова");
}

//-----------------------------------------

$to_time   = mktime(12 ,0 ,0 ,$_POST['to_month'], $_POST['to_day'], $_POST['to_year']);
$from_time = mktime(12 ,0 ,0 ,$_POST['from_month'], $_POST['from_day'], $_POST['from_year']);
//$sql_date_to = date("Y-m-d",$to_time);
//$sql_date_from = date("Y-m-d",$from_time);

$human_to_date   = getdate($to_time);
$human_from_date = getdate($from_time);

if ($mode == 'reg') {
$sql_table = 'users';
$sql_field = 'added';
}
else if ($mode == 'rate') {
$sql_table = 'ratings';
$sql_field = 'added';
}
else if ($mode == 'post') {
$sql_table = 'posts';
$sql_field = 'added';
}
else if ($mode == 'editpost') {
$sql_table = 'posts';
$sql_field = 'editedat';
}
else if ($mode == 'away') {
$sql_table = 'reaway';
$sql_field = 'date';
}
else if ($mode == 'msg') {
$sql_table = 'messages';
$sql_field = 'added';
}
else if ($mode == 'bansmail') {
$sql_table = 'bannedemails';
$sql_field = 'added';
}
else if ($mode == 'snatched') {
$sql_table = 'snatched';
$sql_field = 'startdat';
}
else if ($mode == 'useragent') {
$sql_table = 'useragent';
$sql_field = 'added';
}
else if ($mode == 'topics') {
$sql_table = 'topics';
$sql_field = 'lastdate';
}
else if ($mode == 'cloud'){
$sql_table = 'searchcloud';
$sql_field = 'added';
}
else if ($mode == 'refer') {
$sql_table = 'referrers';
$sql_field = 'date';
}
else if ($mode == 'log') {
$sql_table = 'sitelog';
$sql_field = 'added';
}
else if ($mode == 'torr') {
$sql_table = 'torrents';
$sql_field = 'added';
}
else if ($mode == 'multi_time') {
$sql_table = 'torrents';
$sql_field = 'multi_time';
}
else if ($mode == 'checkpeers') {
$sql_table = 'torrents';
$sql_field = 'checkpeers';
}
else if ($mode == 'last_action') {
$sql_table = 'torrents';
$sql_field = 'last_action';
}
else if ($mode == 'last_reseed') {
$sql_table = 'torrents';
$sql_field = 'last_reseed';
}
else if ($mode == 'stop_time') {
$sql_table = 'torrents';
$sql_field = 'stop_time';
}

else if ($mode == 'tags') {
$sql_table = 'tags';
$sql_field = 'added';
}
else if ($mode == 'bans') {
$sql_table = 'bans';
$sql_field = 'added';
}
else if ($mode == 'comm') {
$sql_table = 'comments';
$sql_field = 'added';
}
else if ($mode == 'new'){
$sql_table = 'news';
$sql_field = 'added';
}
else if ($mode == 'poll'){
$sql_table = 'polls';
$sql_field = 'added';
}
else if ($mode == 'simpaty'){
$sql_table = 'simpaty';
$sql_field = 'respect_time';
}
else if ($mode == 'offers'){
$sql_table = 'off_reqs';
$sql_field = 'added';
}
else if ($mode == 'tfiles'){
$sql_table = 'attachments';
$sql_field = 'added';
}
else if ($mode == 'humor'){
$sql_table = 'humor';
$sql_field = 'date';
}
else if ($mode == 'login'){
$sql_table = 'loginattempts';
$sql_field = 'added';
}

$table = begin_desc($mode);


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
WHERE UNIX_TIMESTAMP({$sql_field}) > '{$from_time}'
AND UNIX_TIMESTAMP({$sql_field}) < '{$to_time}'
GROUP BY result_time
ORDER BY {$sql_field} {$sortby}";

$res = sql_query($sqlq) or sqlerr(__FILE__,__LINE__);
/*        $res = @mysql_query( "SELECT UNIX_TIMESTAMP(MAX(added)) as result_maxdate,
COUNT(*) as result_count,
".$sql_scale."(".$sql_field.") AS result_time
FROM ".$sql_table."
WHERE added > '".$sql_date_from."'
AND added < '".$sql_date_to."'
GROUP BY result_time
ORDER BY ".$sql_field); */


$running_total = 0;
$max_result    = 0;

$results       = array();
//$td_header = array();
//$td_header[] = array( "Date"    , "20%" );
//$td_header[] = array( "Result"  , "70%" );
//$td_header[] = array( "Count"   , "10%" );
if ($_POST['timescale']=="daily"){$view="Ежедневная";}
if ($_POST['timescale']=="weekly"){$view="Еженедельная";}
if ($_POST['timescale']=="monthly"){$view="Ежемесячная";}

$html = $page_title."<br />
<table width=\"100%\" align=\"center\" id=torrenttable border=1><tr><td colspan=3 class='b' align=\"center\">".$view." <b>".$table ."</b> от ({$human_from_date['mday']} {$month_names[$human_from_date['mon']]} {$human_from_date['year']} до "." {$human_to_date['mday']} {$month_names[$human_to_date['mon']]} {$human_to_date['year']})</td></tr>\n";

if (mysql_num_rows($res)) {

while ($row = mysql_fetch_assoc($res)) {

if ( $row['result_count'] >  $max_result ) {
$max_result = $row['result_count'];
}

$running_total += $row['result_count'];

$results[] = array(
'result_maxdate' => $row['result_maxdate'],
'result_count'=> $row['result_count'],
'result_time'=> $row['result_time'],
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
$date = "Неделя #".strftime("%W", $data['result_maxdate'])."<br />" . date( $php_date, $data['result_maxdate'] );
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
$html .= "<tr><td class=\"b\" align=\"center\"><h3>Нет результатов для показа.</h3></td></tr>\n" ;
}

print $html."</table>\n<br />";
}




function main_screen($mode='reg') {
global $month_names;

$page_title = "Центр статистики";

$page_detail = "Пожалуйста определите даты и другие опции внизу. <br>";

$page_detail.= "Будет показано: <b>".begin_desc($mode)."</b>";


$form_code = 'show_'.$mode;

$old_date = getdate(time() - (3600 * 24 * 90));
$new_date = getdate(time() + (3600 * 24));

$html =  "<table width=\"100%\" align=\"center\" id=torrenttable border=1>";
$html .=  "<tr><td class=\"b\">$page_detail</td></tr>";
$html .= start_form( array( 1 => array( 'code'  , $form_code  ),2 => array( 'act'   , 'stats'     ),));

$html .= "<tr><td class=\"a\"><b>Дата от </b>" .form_dropdown( "from_month" , make_month(), $old_date['mon']  ).'&nbsp;&nbsp;'.form_dropdown( "from_day"   , make_day()  , $old_date['mday'] ).'&nbsp;&nbsp;'.form_dropdown( "from_year"  , make_year() , $old_date['year'] )."<br /></td></tr>";

$html .= "<tr><td class=\"a\"><b>Дата до </b>" .form_dropdown( "to_month" , make_month(), $new_date['mon']  ).'&nbsp;&nbsp;'.form_dropdown( "to_day"   , make_day()  , $new_date['mday'] ).'&nbsp;&nbsp;'.form_dropdown( "to_year"  , make_year() , $new_date['year'] ) ."<br /></td></tr>";

if ($mode <> 'views') {
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