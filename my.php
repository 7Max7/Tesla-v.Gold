<?
require_once("include/bittorrent.php");

dbconn(false);
loggedinorreturn();
stdhead($tracker_lang['my_my']);

echo "
<style type=\"text/css\">
input.pass
{background: url(pic/pass.gif) no-repeat; background-color: #fff; background-position: 0 50%; color: #000;
padding-left: 18px;}
.rowhead2 { font-weight: bold; text-align: right;}
input.skype { background: url(pic/contact/skype_13.gif) no-repeat; background-color: #fff; background-position: 0 50%; color: #000;  padding-left: 18px;}
input.icq { background: url(pic/contact/icq_13.gif) no-repeat;  background-color: #fff;  background-position: 0 50%;   color: #000;    padding-left: 18px;}
input.odnoklasniki { background: url(pic/contact/odnoklasniki_13.gif) no-repeat;  background-color: #fff;  background-position: 0 50%;   color: #000;    padding-left: 18px;}
input.vkontakte { background: url(pic/contact/vkontakte_13.gif) no-repeat;    background-color: #fff;    background-position: 0 50%;    color: #000;    padding-left: 18px;}
</style>

<script type=\"text/javascript\" src=\"js/city_country.js\"></script>  
<script type=\"text/javascript\">  
var ajax = new Array();  
function getCityList(sel){
var countryCode = sel.options[sel.selectedIndex].value; 
document.getElementById('city').options.length = 0;    // Empty city select box 
if(countryCode.length>0){
var index = ajax.length;
ajax[index] = new sack();
ajax[index].requestFile = 'takeprofedit.php?country='+countryCode;
// Specifying which file to get 
ajax[index].onCompletion = function(){ createCities(index) };
// Specify function that will be executed after file has been found
ajax[index].runAJAX();        
// Execute AJAX function 
}
}

function createCities(index) {
    var obj = document.getElementById('city'); 
    eval(ajax[index].response);    // Executing the response from Ajax as Javascript code     
}
</script>  ";


if (isset($_GET["edited"])) {
	
if ($_GET["edited"]==1)
print("<h1>Ваш профайл успешно обновлен!</h1>\n");
	
if ($_GET["edited"]==2)
print("<h1>Ваш пароль успешно изменен! <br>Копия данных отправлена на почту.</h1>\n");

//if (isset($_GET["mailsent"]))
//print("<h2>".$tracker_lang['my_mail_sent']."</h2>\n");

$clasb=" class=\"a\"";
$clasa=" class=\"b\"";
$clastd=" class=\"a\"";
}
else {
print("<h1>Добро пожаловать <br>В настройки аккаунта: <a href=userdetails.php?id=$CURUSER[id]>".get_user_class_color($CURUSER["class"], $CURUSER["username"])."</a></h1>\n");
$clasa=" class=\"a\"";
$clasb=" class=\"b\"";
$clastd=" class=\"b\" ";
}
?>

<table border="1" cellspacing="0" cellpadding="10" align="center">
<tr>
<td <?=$clasa;?> align="center" width="25%"><a href="bookmarks.php"><b>Мои Закладки</b></a></td>
<td <?=$clasb;?> align="center" width="25%"><a href="mytorrents.php"><b><?=$tracker_lang['my_torrents'];?></b></a></td>
<td <?=$clasb;?> align="center" width="25%"><a href="checkcomm.php"><b>Лист подписки</b></a></td>
<td <?=$clasa;?> align="center" width="25%"><a href="friends.php"><b>Список Друзей/Врагов</b></a></td>
</tr>
<tr>
<td <?=$clastd;?> colspan="4">
<form  name="upload" enctype="multipart/form-data" method="post" action="takeprofedit.php">
<table border="1" cellspacing="0" cellpadding="5">
<?

$ss_r=new MySQLCache("SELECT * FROM stylesheets ORDER by id ASC", 86400,"my_style.txt"); 
//$ss_r = sql_query("SELECT * FROM stylesheets ORDER by id ASC") or sqlerr(__FILE__, __LINE__);
$ss_sa = array();
//while ($ss_a = mysql_fetch_array($ss_r)) {
while ($ss_a=$ss_r->fetch_assoc()){
  $ss_id = $ss_a["id"];
  $ss_name = $ss_a["name"];
  $ss_sa[$ss_name] = $ss_id;
}
reset($ss_sa);
$stylesheets="";
while (list($ss_name, $ss_id) = each($ss_sa)) {
  if ($ss_name == $CURUSER["stylesheet"]) $ss = " selected"; else $ss = "";
  $stylesheets .= "<option value=\"$ss_name\"$ss>".$ss_name."</option>\n";
}


$select_contry_city = "<select id=\"country\" name=\"country\" onchange=\"getCityList(this)\">"; 
$select_contry_city .= "<option value=\"0\">Выбрать страну</option>"; 
$countries = sql_query("SELECT id,name FROM countries ORDER BY name") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($countries) > 0){
while($lista = mysql_fetch_array($countries)) { 
$select_contry_city .= "<option value=".$lista['id']."" . ($CURUSER["country"] == $lista['id'] ? " selected" : "") . ">".$lista['name']."</option>\n"; 
}
}
$select_contry_city .= "</select>"; 

$select_contry_city .= " / <select id=\"city\" name=\"city\">"; 
$cities = sql_query("SELECT ci.id, ci.name FROM cities ci INNER JOIN countries co ON ci.country_id = co.id WHERE co.id = ".$CURUSER['country']." ORDER BY ci.name") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($cities) > 0){
while($lista = mysql_fetch_array($cities)) {
$select_contry_city .= "<option value=".$lista['id']."" . ($CURUSER['city'] == $lista['id'] ? " selected" : "") . ">".$lista['name']."</option>\n"; 
}
} else {
$select_contry_city .= "<option value=0>Нет выбранного города</option>\n"; 
}

$select_contry_city .= "</select>"; 

/*

$dir = opendir('languages');
        $lang = array();
        while ( $file = readdir($dir) ) {
                if (preg_match('#^lang_#i', $file) && !is_file($dir . '/' . $file) && !is_link($dir . '/' . $file)) {
                        $filename = trim(str_replace("lang_","", $file));
                        $displayname = preg_replace("/^(.*?)_(.*)$/", "\\1 [ \\2 ]", $filename);
                        $displayname = preg_replace("/\[(.*?)_(.*)\]/", "[ \\1 - \\2 ]", $displayname);
                        $lang[$displayname] = $filename;
                }
        }
        closedir($dir);
        @asort($lang);
        @reset($lang);

        $lang_select = '<select name="language">';
        while ( list($displayname, $filename) = @each($lang) ) {
                $selected = ((strtolower($CURUSER["language"]) == strtolower($filename) ) ? ' selected="selected"' : '');
                $lang_select .= '<option value="' . $filename . '"' . $selected . '>' . ucwords($displayname) . '</option>';
        }
        $lang_select .= '</select>';*/

function format_tz($a)
{
	$h = floor($a);
	$m = ($a - floor($a)) * 60;
	return ($a >= 0?"+":"-") . (strlen(abs($h)) > 1?"":"0") . abs($h) .
		":" . ($m==0?"00":$m);
}


if (($CURUSER['downloaded']>=4065082210) && get_user_class() < UC_MODERATOR && $CURUSER["added"]<get_date_time(gmtime() - 1209600) && $CURUSER["hiderating"]=="no" && ($CURUSER['uploaded'] / $CURUSER['downloaded']<1)){ /// две недели

$ratio = $CURUSER['uploaded'] / $CURUSER['downloaded'];
$ratio_warn = $CURUSER['uploaded'] / ($CURUSER['downloaded']+5368709120);
$ratio = number_format($ratio, 3);
$ratio_warn = number_format($ratio_warn, 3);


//$warn=($ratio_warn <= "0.3" ? "<b title=\"Вы зарегистрированны более 2 недель, при условии рейтинга меньше 0.3 система автоматически отключает вас. Причина: Вы не раздаете скаченные файлы или не сидируете вовсе.\">После 5 GB скачивания - вы будете отключенны</b>":"");

//if ($ratio<="0.3"){
//$usercomment = gmdate("Y-m-d") . " - Отключен системой, рейтинг менее 0.3.\n".$CURUSER["usercomment"]; 
/// отключаем
//sql_query("UPDATE users SET usercomment = ".sqlesc($usercomment)." AND enabled='no' WHERE id = " . $CURUSER["id"]) or sqlerr(__FILE__,__LINE__);
//}
if ($ratio_warn <= "0.3"){
tr("<a name=\"warned\"><font color=red>Предупреждение</font></a>","<span style=\"BACKGROUND-COLOR: red; BORDER:silver 1px solid;  DISPLAY:block;  COLOR:WHITE;  MARGIN:2px 1px;  PADDING:2px 2px 2px 6px;  TEXT-DECORATION:none;\">Сейчас у вас рейтинг <b> $ratio</b>, если вы скачаете еще 5 Гб, - то будете отключенны системой, по причине - плохой рейтинг (меньше 0.3) <br><b>Выход</b>: Начать сидировать файлы тем самым раздавать файлы и получать бонусы за счет их (Бонусы можно обменять на аплоад - <a class=\"copyright\" href=\"mybonus.php\">здесь</a>) </span>",1);
}
}


tr($tracker_lang['my_allow_pm_from'],
"<label><input type=radio name=acceptpms" . ($CURUSER["acceptpms"] == "yes" ? " checked" : "") . " value=\"yes\">Все <b>[</b>исключая блокированных<b>]</b></label><br />
<label><input type=radio name=acceptpms" .  ($CURUSER["acceptpms"] == "friends" ? " checked" : "") . " value=\"friends\">Только друзей</label><br />
<label><input type=radio name=acceptpms" .  ($CURUSER["acceptpms"] == "no" ? " checked" : "") . " value=\"no\">Только администрации</label>",1);

tr($tracker_lang['my_parked'],
"<label><input type=\"radio\" name=\"parked\"" . ($CURUSER["parked"] == "yes" ? " checked" : "") . " value=\"yes\">".$tracker_lang['yes']."</label>
<label><input type=\"radio\" name=\"parked\"" . ($CURUSER["parked"] == "no" ? " checked" : "") . " value=\"no\">".$tracker_lang['no']."</label>
<br /><font class=\"small_text\">".$tracker_lang['my_you_can_park'].".</font>"
,1);


tr("Сообщения","<table><tr>\n
<td class=bottom style='padding-right: 5px'><label>
".$tracker_lang['my_delete_after_reply']."<input type=checkbox name=deletepms".($CURUSER["deletepms"] == "yes" ? " checked" : "") . "></label></td>
<td class=bottom style='padding-right: 5px'>&nbsp;<label>
".$tracker_lang['my_sentbox']."<input type=checkbox name=savepms".($CURUSER["savepms"] == "yes" ? " checked" : ""). "></label></td>
</tr></table>
",1);


tr("Защита от кражи данных (куки)", "<label><input type=\"radio\" name=\"tesla_guard\"" . ($CURUSER["shelter"] == "ag" ? " checked" : "") . " value=\"yes\">Использовать по браузеру</label>
<label><input type=\"radio\" name=\"tesla_guard\"" . ($CURUSER["shelter"] == "ip" ? " checked" : "") . " value=\"no\">Использовать по ip адресу</label><br />
<br /><font class=\"small_text\"><b>Подсказка</b>: Если у вас Динамический ip адрес можете использовать защиту по браузеру, иначе для статических адресов - по ip. Если куки неверны - вас выбьет с сайта, после чего, вы должны заново войти через ввод ника и пароля. Подробнее <a href=\"http://ru.wikipedia.org/wiki/HTTP_cookie#.D0.92.D0.B7.D0.BB.D0.BE.D0.BC_.D0.BA.D1.83.D0.BA.D0.B8\">о краже куков</a></font>",1);

tr("Скрыть комментарии","<label><input type=\"radio\" name=\"hidecomment\"" . ($CURUSER["hidecomment"] == "yes" ? " checked" : "") . " value=\"yes\">".$tracker_lang['yes']."</label>
<label><input type=\"radio\" name=\"hidecomment\"" . ($CURUSER["hidecomment"] == "no" ? " checked" : "") . " value=\"no\">".$tracker_lang['no']."</label>
<br /><font class=\"small_text\">Если не желаете просматривать комментарии в описании торрентов, то включите эту опцию (будет отключен показ).</font>",1);


$r=new MySQLCache("SELECT id,name FROM categories ORDER BY sort", 86400,"mydetails_category.txt"); 
//$r = sql_query("SELECT id,name FROM categories ORDER BY sort") or sqlerr(__FILE__, __LINE__);
$categories = "<b>Все категории трекера</b>:<br />\n";
//if (mysql_num_rows($r) > 0)
//{
	$categories .= "<a name=\"notif\"></a><table><tr>\n";
	$i = 0;
//	while ($a = mysql_fetch_assoc($r))

while ($a=$r->fetch_assoc()) {
	  $categories .=  ($i && $i % 2 == 0) ? "</tr><tr>" : "";
	  $categories .= "<td class=bottom style='padding-right: 5px'><label><input name=cat$a[id] type=\"checkbox\" " . (strpos($CURUSER['notifs'], "[cat$a[id]]") !== false ? " checked" : "") . " value='yes'>&nbsp;" . htmlspecialchars($a["name"]) . "</label></td>\n";
	  ++$i;
	}
	$categories .= "</tr>
	<tr><td class=b align=\"center\" colspan=\"2\">Только эти <u>Выбранные категории</u> будут показаны в блоке Последние файлы и на страничке Торренты, все остальные скроются из виду.</td></tr>
	</table>\n";
//}
/*tr($tracker_lang['my_email_notify'], "<input type=checkbox name=pmnotif" . (strpos($CURUSER['notifs'], "[pm]") !== false ? " checked" : "") . " value=yes> Уведомить меня при получении ЛС<br />\n" .
	 "<input type=checkbox name=emailnotif" . (strpos($CURUSER['notifs'], "[email]") !== false ? " checked" : "") . " value=yes> Уведомить меня при размещении торрента в одной <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; из следующих предпочитаемых категорий.\n"
   , 1);*/

tr($tracker_lang['my_default_browse'],$categories,1);

tr($tracker_lang['my_style'], "<select name=stylesheet>\n$stylesheets\n</select>",1);

tr("Страна/Город", "".$select_contry_city."",1);  

//tr($tracker_lang['my_language'], $lang_select ,1);
tr($tracker_lang['load_avatar'], "

<table cellspacing=\"3\" cellpadding=\"0\" width=\"100%\" border=\"0\">

<tr>
<td align=\"center\" class=\"a\"colspan=2>Локальная загрузка</td>
</tr>
<tr>
<td align=\"left\" class=\"b\"><input type=\"file\" name=\"avatar\" size=\"80\"><br /><i>Выбрав картинку для аватара у себя на компьютере, можно залить ее сюда, через это меню заливки.</i></td>
</tr>

<tr>
<td align=\"center\" class=\"a\"colspan=2>Внешняя загрузка</td>
</tr>
<tr>
<td align=\"left\" class=\"b\"><input maxLength=\"128\" size=\"80\" name=\"avatar_inet\"><br /><i>Если картинка для аватара залита на файлообменник или на сайт, то можно здесь ввести полный путь к ней. Система сама скачает и установит на ее ваш аккаунт.</i></td>
</tr>

<tr>
<td align=\"center\" class=\"a\" colspan=2>Размеры аватарки должны быть не более <b>".$avatar_max_width."</b>x<b>".$avatar_max_height."</b> пикселей. <br>Максимальный размер файла: <b>".mksize($avatarsize)."</b></td>
</tr>

</table>",1);



tr($tracker_lang['my_gender'],
"<label><input type=radio name=gender" . ($CURUSER["gender"] == "1" ? " checked" : "") . " value=1>".$tracker_lang['my_gender_male']."</label>
<label><input type=radio name=gender" .  ($CURUSER["gender"] == "2" ? " checked" : "") . " value=2>".$tracker_lang['my_gender_female']."</label>"
,1);

///////////////// BIRTHDAY MOD /////////////////////
$birthday = $CURUSER["birthday"];
$birthday = date("Y-m-d", strtotime($birthday));
list($year1, $month1, $day1) = explode('-', $birthday);
if ($CURUSER["birthday"] == "0000-00-00") {
        $year .= "<select name=year><option value=\"0000\">".$tracker_lang['my_year']."</option>\n";
        $i = "1950";
        while($i <= (date('Y',time())-13)) {
                $year .= "<option value=" .$i. ">".$i."</option>\n";
                $i++;
        }
        $year .= "</select>\n";
        $birthmonths = array(
        "01" => $tracker_lang['my_months_january'],
        "02" => $tracker_lang['my_months_february'],
        "03" => $tracker_lang['my_months_march'],
        "04" => $tracker_lang['my_months_april'],
        "05" => $tracker_lang['my_months_may'],
        "06" => $tracker_lang['my_months_june'],
        "07" => $tracker_lang['my_months_jule'],
        "08" => $tracker_lang['my_months_august'],
        "09" => $tracker_lang['my_months_september'],
        "10" => $tracker_lang['my_months_october'],
        "11" => $tracker_lang['my_months_november'],
        "12" => $tracker_lang['my_months_december'],
        );
        $month = "<select name=\"month\"><option value=\"00\">".$tracker_lang['my_month']."</option>\n";
        foreach ($birthmonths as $month_no => $show_month)
        {
                $month .= "<option value=$month_no>$show_month</option>\n";
        }
        $month .= "</select>\n";
        $day .= "<select name=day><option value=\"00\">".$tracker_lang['my_day']."</option>\n";
        $i = 1;
        while ($i <= 31) {
                if($i < 10) {
                        $day .= "<option value=0".$i. ">0".$i."</option>\n";
                } else {
                        $day .= "<option value=".$i.">".$i."</option>\n";
                }
                $i++;
        }
        $day .="</select>\n";
        tr($tracker_lang['my_birthdate'], $year . $month . $day ,1);
}

if($CURUSER["birthday"] <> "0000-00-00") {
        tr($tracker_lang['my_birthdate'],"<b><input type=hidden name=year value=$year1>$year1<input type=hidden name=month value=$month1>.$month1<input type=hidden name=day value=$day1>.$day1</b>",1);
}
///////////////// BIRTHDAY MOD /////////////////////

print("<tr><td class=\"a\" colspan=\"2\" align=left><b><center>Система мгновенных сообщений</center></b></td></tr>\n");

tr(" ", "    <table cellspacing=\"3\" cellpadding=\"0\" width=\"100%\" border=\"0\">
            <tr>
        <td align=\"center\" style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\" colspan=2>
        ".$tracker_lang['my_contact_descr'].":</td>
      </tr>
      <tr>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        ".$tracker_lang['my_contact_icq']."<br />
        
        <input maxLength=\"10\" size=\"40\" class=\"icq\" name=\"icq\" value=\"" . $CURUSER["icq"] . "\" ></td>
        
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        ".$tracker_lang['my_contact_skype']."<br />
     
        <input maxLength=\"20\" size=\"40\" class=\"skype\" name=\"skype\" value=\"" . $CURUSER["skype"] . "\" ></td>      </tr>
      <tr>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        Вконтакте: <br />
        
<input type=\"text\" maxLength=\"300\" class=\"vkontakte\" name=\"vkontakte\" size=\"40\" value=\"" . htmlspecialchars($CURUSER["vkontakte"]) . "\" />
</td>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        Одноклассники:<br />
  
  <input type=\"text\" maxLength=\"300\" class=\"odnoklasniki\" name=\"odnoklasniki\" size=\"40\" value=\"" . htmlspecialchars($CURUSER["odnoklasniki"]) . "\" />
  </td>
      </tr>
  
    </table>",1);
    
/*
//мод предупреждений
print("<tr><td class=\"rowhead\">Уровень<br>предупреждений</td><td align=\"left\">");
for($i = 0; $i < $user["num_warned"]; $i++)
{
$img .= "<a href=\"mywarned.php\"  target=\"_blank\"><img src=\"pic/star_warned.gif\" alt=\"Уровень предупреждений\" title=\"Уровень предупреждений\"></a>";
}
if (!$img)
$img = "Нет предупреждений";
print("$img <a href=\"mywarned.php\">Купить помилование за аплоад</a></td></tr>\n");
*/

tr($tracker_lang['my_website'], "<input type=\"text\" name=\"website\" size=50 value=\"" . htmlspecialchars($CURUSER["website"]) . "\" /> ", 1);
tr($tracker_lang['my_torrents_per_page'], "<input type=text size=10 name=torrentsperpage value=$CURUSER[torrentsperpage]> <b>[</b>0 = установки по умолчанию<b>]</b>",1);
//tr($tracker_lang['my_topics_per_page'], "<input type=text size=10 name=topicsperpage value=$CURUSER[topicsperpage]> <b>[</b>0 = установки по умолчанию<b>]</b>",1);
tr($tracker_lang['my_messages_per_page'], "<input type=text size=10 name=postsperpage value=$CURUSER[postsperpage]> <b>[</b>0 = установки по умолчанию<b>]</b>",1);


if (get_user_class() > UC_MODERATOR){
    if (strlen($CURUSER["usercomment"])>450)
	$stl_size2=round(strlen($CURUSER["usercomment"])/450)+7;
	else
	$stl_size2=5;
	
tr("Последние события ".(get_user_class() > UC_MODERATOR ? "<br>[".strlen($CURUSER["usercomment"])."]":""), "<textarea cols=100% rows=$stl_size2 readonly >" . htmlspecialchars($CURUSER["usercomment"]) . "</textarea><br />Все ваши последние действия на сайте, обмен, неудачные входы, смена контрольного вопроса и тп.", 1);	
}

   if (strlen($CURUSER["info"])>450)
	$stl_size=round(strlen($CURUSER["info"])/450)+8;
	else
	$stl_size=6;

tr($tracker_lang['my_info']."".(get_user_class() > UC_MODERATOR ? "<br>[".strlen($CURUSER["info"])."]":""), "<textarea cols=100% rows=$stl_size ".($CURUSER["editinfo"]=="no" ? " readonly" : " name=info").">" . $CURUSER["info"] . "</textarea><br />Показывается на вашей публичной странице. Может содержать <a href=tags.php target=_new>bb коды</a>, html не поддерживается.", 1);


tr("Подпись".(get_user_class() > UC_MODERATOR ? "<br>[".strlen($CURUSER["signature"])."]":""), "<textarea cols=100% maxLength=255 rows=4 ".($CURUSER["signatrue"]=="no" ? " readonly" : " name=signature").">" . $CURUSER["signature"] . "</textarea><br/>Ваша подпись в комментариях. Max символов - <b>255</b>. Может содержать <a href=tags.php target=_new>bb коды</a>.", 1);

/*

tr($tracker_lang['my_userbar'], "<img src=\"torrentbar/bar.php/".$CURUSER["id"].".png\" border=\"0\"><br />".$tracker_lang['my_userbar_descr'].":<br /><input type=\"text\" size=75 value=\"[url=$DEFAULTBASEURL][img]$DEFAULTBASEURL/torrentbar/bar.php/".$CURUSER["id"].".png[/img][/url]\" readonly />",1);

*/

/*tr($tracker_lang['my_mail'], "<input type=\"text\" name=\"email\" size=50 value=\"" . htmlspecialchars($CURUSER["email"]) . "\" />", 1);
print("<tr><td colspan=\"2\" align=left><b>Примечание:</b> Если вы смените ваш Email адрес, то вам придет запрос о подтверждении на ваш новый Email-адрес. Если вы не подтвердите письмо, то Email адрес не будет изменен.</td></tr>\n");*/
tr("Сменить пасскей","<label><input type=checkbox name=resetpasskey value=1 /> <b>[</b>Вы должны перекачать все активные торренты после смены пасскея<b>]</b></label>", 1);

if (strlen($CURUSER['passkey']) <> 32) {
	$CURUSER['passkey'] = md5($CURUSER['username'].get_date_time().$CURUSER['passhash']);
	sql_query("UPDATE users SET passkey='$CURUSER[passkey]' WHERE id=$CURUSER[id]");
}
tr("Мой пасскей","<b>$CURUSER[passkey]</b>", 1);

///<input type=checkbox name=passkey_ip" . ($CURUSER["passkey_ip"] != "" ? " checked" : "") . ">
tr("Секретный вопрос<a name=\"question\"></a>", "
".(empty($CURUSER["question"]) && empty($CURUSER["rejoin"]) ? "<input title=\"Ввелите ваш секретный вопрос\" type=\"text\" name=\"ques\" size=70 ".(empty($CURUSER["question"])? "":"readonly")." value=\"" . htmlspecialchars($CURUSER["question"]) . "\" /> <i>до 255 символов</i><br><i>введите свой личный секретный вопрос</i> <br>
<input  title=\"Введите ваш ответ на секретный вопрос\"  type=\"text\" name=\"answ\" size=70 /> <i>до 255 символов</i><br><i>ответ на секретный вопрос, <b>знать должны только вы</b> иначе мы не гарантируем сохранность ваших данных (ответ хешируется, т.е. раскодировать его нереально)</i>":"<input title=\"Ваш секретный вопрос\" type=\"text\" size=70 ".(empty($CURUSER["question"])? "":"readonly")." value=\"" . htmlspecialchars($CURUSER["question"]) . "\" /> <br><i>выше - ваш секретный вопрос</i>
")."", 1);
if (!empty($CURUSER["question"]) && !empty($CURUSER["rejoin"])){
tr("Сброс секретного вопроса<a name=\"reser_question\"></a>","<label><input type=checkbox name=resetquest value=1 /> <b>[</b>Это действие удалит ваш старый секретный вопрос, используйте только для смены вопроса и ответа<b>]</b></label>",1);
}

tr("Сброс пароля", "<input name=\"resetpass\" value=\"1\" type=\"checkbox\" /> <b>[</b>Если забыли свой пароль, то поставьте галочку на сброс, новый пароль придет на почту моментально<b>]</b>", 1);


tr("Изменение пароля <a name=\"reser_password\"></a>", "
<input type=\"password\" class=\"pass\" name=\"oldpassword\" size=\"50\"/> <i>ваш старый пароль</i><br>
<input type=\"password\" class=\"pass\" name=\"chpassword\" size=\"50\" /> <i>введите новый пароль</i><br>
<input type=\"password\" class=\"pass\" name=\"passagain\" size=\"50\" /> <i>подтвердите новый пароль снова</i><br>
<i>Пожалуйста не используйте простые комбинации слов или букв. Топ подбираемых паролей: <b>qwerty</b>, <b>password</b>, а также <b>год рождения</b> пользователя.</i>", 1);
///tr("Старый пароль", "", 1);

//tr("Новый пароль", "<input type=\"password\" name=\"chpassword\" size=\"50\" />", 1);
//tr("Подтверждение пароля", "<input type=\"password\" name=\"passagain\" size=\"50\" />", 1);


echo "<tr><td colspan=\"2\" class=\"b\" align=\"center\"><input onClick=\"return confirm('Уверены, что хотите отредактировать свой профиль?')\" class=\"btn\" type=\"submit\" value=\"Подтвердить изменения\" style='height: 25px; width:200px'/> <input class=\"btn\" type=\"reset\" value=\"Сбросить изменения\" style='height: 25px; width:200px'/></td></tr>";

echo "</table>
</form>
</td>
</tr>
</table>";

stdfoot();
?>