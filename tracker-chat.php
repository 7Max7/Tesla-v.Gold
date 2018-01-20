<?php
require_once("include/bittorrent.php");

dbconn(false);
loggedinorreturn();


if ($CURUSER["shoutbox"] <> '0000-00-00 00:00:00'){
stdhead("Внимание");
stdmsg("Oшибка", "Вам запрещено использовать чат. Бан до <b>".$CURUSER["shoutbox"]."</b>");
stdfoot();
die; 
}



function stdheadchat2($title = "") {
	global $CURUSER, $SITE_ONLINE,$ICE, $FUNDS, $SITENAME, $DEFAULTBASEURL, $ss_uri, $tracker_lang, $default_theme;

	if (is_numeric($CURUSER["stylesheet"])) 
	{
     	$ss_a = @mysql_fetch_array(@sql_query("SELECT uri FROM stylesheets WHERE id = " . $CURUSER["stylesheet"]));
		if (!is_numeric($ss_a))
		{
		$uri=$ss_a["uri"];
		$userid=$CURUSER["id"];
		sql_query("UPDATE users SET stylesheet='$uri' WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
     	$CURUSER["stylesheet"]=$uri;
        header("Location: $DEFAULTBASEURL/index.php");
		}
		else
		$CURUSER["stylesheet"] = $default_theme;
		$uri=$ss_a["uri"];
		$userid=$CURUSER["id"];
		sql_query("UPDATE users SET stylesheet='$default_theme' WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
	}
	
	$ss_uri=$CURUSER["stylesheet"];
	
	if (!$CURUSER || !$CURUSER["stylesheet"])
	{
	$ss_uri = $default_theme;
	}
    else
    $ss_uri=$CURUSER["stylesheet"];

	require_once("themes/" . $ss_uri . "/template.php");
	//require_once("themes/" . $ss_uri . "/stdheadchat.php");


echo "
<title>$SITENAME :.:  Основной чат [версия 3.5 за 29 мая] ТЕСТИРОВАНИЕ привата!</title>
<link rel=\"stylesheet\" href=\"./themes/$ss_uri/style.css\" type=\"text/css\">
<script language=\"javascript\" type=\"text/javascript\" src=\"js/jquery.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"js/functions.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"js/tooltips.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"js/swfobject.js\"></script>
<link rel=\"shortcut icon\" href=\"/pic/favicon.ico\" type=\"image/x-icon\"/>";
} // stdhead

stdheadchat2();


///$edit_color = ($_GET["color"]=='yes' ? "yes":"");
//if ($edit_color=="yes"){
	





?>
<div align="center" id="ka778" style="display: block;">
<style>

#vtoolbar {
line-height:24px;
list-style:none;
margin:0;
background: transparent;
}

#vtoolbar a {
line-height:24px;
text-decoration:none;
display:inline;
padding:0 10px 0 10px;
}

#vtoolbar a{
line-height:24px;
box-shadow: 0px 0px 3px #2e85ab;
-moz-box-shadow: 0px 0px 5px #2e85ab;
-webkit-box-shadow: 0px 0px 3px #2e85ab;
-webkit-border-radius: 5px;
-moz-border-radius: 5px;
border-radius: 5px;
border: 1px solid #cfe2ee;
background: url(/themes/Spring/images/right.png) repeat-x center;
}

#vtoolbar a:hover{
line-height:24px;
box-shadow: 0px 0px 3px #2e85ab;
-moz-box-shadow: 0px 0px 5px #2e85ab;
-webkit-box-shadow: 0px 0px 3px #2e85ab;
-webkit-border-radius: 5px;
-moz-border-radius: 5px;
border-radius: 5px;
border: 1px solid #2e85ab;
background: url(/themes/Spring/images/right.png) repeat-x center;
}
</style>

<div id="vtoolbar" align="center">
<a href="<?=$DEFAULTBASEURL;?>/">
<strong><?=$tracker_lang['homepage'];?></strong></a>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="browse.php"><strong><?=$tracker_lang['browse'];?></strong></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="bookmarks.php"><strong><?=$tracker_lang['bookmarks'];?></strong></a>
<? } ?>
<?  if($CURUSER["class"] >= UC_VIP && $CURUSER["uploadpos"]!="no") { ?>
&nbsp;&#8225;&nbsp;
<a href="upload.php"><strong><?=$tracker_lang['upload'];?></strong></a>
<? } ?>

<?  if($CURUSER && $CURUSER["schoutboxpos"]!="no") { ?>
&nbsp;&#8225;&nbsp;
<a href="tracker-chat.php"><strong>Чат</strong></a>
<? } ?>

<?  if($CURUSER["class"] >= UC_USER) { ?>
&nbsp;&#8225;&nbsp;
<a href="forums.php"><strong>Форум</strong></a>
<? } ?>

<?  if($CURUSER["class"] >= UC_MODERATOR) { ?>
&nbsp;&#8225;&nbsp;
<a href="log.php"><strong>Журнал</a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="rules.php"><strong><?=$tracker_lang['rules'];?></strong></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="faq.php"><strong><?=$tracker_lang['faq'];?></strong></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8225;&nbsp;
<a href="staff.php"><strong><?=$tracker_lang['staff'];?></strong></a>
<? } ?>
</div>
</div>
<?

///print"<center>Время остановилось aka Дьявол 12 сентября 2009 года.</center>";


/*
if ($CURUSER["class"]>="4" && $CURUSER["username"]<>"Cleonsi"){


if (!empty($_POST["ona"]))
{

$ip_real=$_POST["ona"];
$poput=" Указанно: $ip_real";

if (!stristr($CURUSER["usercomment"],$poput)!==false){

$usercomment = get_date_time() . " - Указанно: $ip_real.\n". $CURUSER["usercomment"];
mysql_query("UPDATE users SET usercomment='$usercomment' WHERE id=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);

}
die("Спасибо за ответ. (3 секунды и автоматически перенаправит в чат))<script>setTimeout('document.location.href=\"tracker-chat.php\"', 3000);</script>");
}

$poput=" Указанно";
if (!stristr($CURUSER["usercomment"],$poput)!==false){
	echo"<br><br><body bgcolor=#F5F4EA>
	<meta http-equiv=\"expires\" content=\"0\">
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\" />
    <center>
	<form method=post action=tracker-chat.php>Уважаемая $CURUSER[username], Вы не указали <b>размер груди</b>, пожалуйста введите цифру, 0 - 1 - 2 или 3 :  <input name=ona value=\"\" size=10\">
  	<input type=submit name=save value=\"Подтверждаю, и жму тут\" class=btn>
	</form></center>
	</body>";
	die;
}

}
*/


if (isset($_GET["humor"]) && $_GET["humor"]=="yes"){
?>

<script>
function hum_online() {
jQuery.post("block-myhumor_jquery.php" , {} , function(response) {
		jQuery("#hum_online").html(response);
	}, "html");
setTimeout("hum_online();", 90000);
}
hum_online();
</script>
<?
echo '<div align="center" id="hum_online">Загрузка, секундочку.</div>';
}


print("<form action=\"shoutbox.php\" method=\"post\" name=\"shoutform\" onsubmit=\"return sendShout(this);\" class=\"expose\" >");
print("<table class=\"main\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">");

if ($CURUSER["stylesheet"]=="Monic2")
$themeclass="color: #990000;";
?>
<style type="text/css"> 
.small {font-size: 8pt; font-family: tahoma; } 
.date {font-size: 7pt;  <?=$themeclass;?> }
</style> 
<tr>
<td align="center" class ="b" colspan="2" style="white-space: nowrap" width="100%" >

<input title="Внутренние смайлы" type="button" class="btn" value="Смайлы" onclick="javascript: klappe_news('a666')"/>
<input title="Показать - Скрыть меню вверху" class="btn" type="button" value="Меню Панель" onclick="javascript: klappe_news('a778')"/>

<input type="text" onselect="FieldName(this, this.name)" onclick="FieldName(this, this.name)" onkeyup="FieldName(this, this.name)" title="Вводи свой текст, лимит 1000 символов" name="shout" style="width: 50%" maxlength="1000" />

<?
if (isset($_GET["humor"]))
$_GET["humor"]=$_GET["humor"];
else
$_GET["humor"]="no";

$get=($_GET["humor"]<>"yes"?"?humor=yes":"");

echo "<a href=\"tracker-chat.php".$get."\"><input title=\"Показать панель анекдотов\" class=\"btn\" type=\"button\" value=\"Анекдот\"/></a>";
?>

<input title="Показать BB панель" type="button" class="btn" value="BB коды" onclick="javascript: klappe_news('a777')"/>
<input type="submit" class="btn" title="Кликай не бойся" value="Отправить" />

<div align="center" id="ka666" style="display: none;">
<?

$count=0;
global $smilies;


while ($count++<30 && list($code, $url) = each($smilies)) {
   
  $s.="<img onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value=parent.document.shoutform.shout.value+' " . htmlspecialchars_uni($code) . " ';return false;\" style=\"font-weight: italic;\" border=\"0\" src=\"pic/smilies/".$url."\">";
  ///    $count++;

}
unset($smilies);
echo $s;
echo "<br><input title=\"Открыть окно всех смайлов\"  type=\"button\" value=\"Показать все смайлы!\" onclick=\"javascript:winop()\"/>";

?><br>
</div>

<div align="center" id="ka777" style="display: none;">

<script type="text/javascript" language="JavaScript">
var SelField = document.shoutform.shoutform;
var TxtFeld  = document.shoutform.shoutform;

var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));

var is_moz = 0;
var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);

function StoreCaret(text) {
	if (text.createTextRange) {
		text.caretPos = document.selection.createRange().duplicate();
	}
}
function FieldName(text, which) {
	if (text.createTextRange) {
		text.caretPos = document.selection.createRange().duplicate();
	}
	if (which != "") {
		var Field = eval("document.shoutform."+which);
		SelField = Field;
		TxtFeld  = Field;
	}
}

function AddSelectedText(Open, Close) {
	if (SelField.createTextRange && SelField.caretPos && Close == '\n') {
		var caretPos = SelField.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? Open + Close + ' ' : Open + Close;
		SelField.focus();
	} else if (SelField.caretPos) {
		SelField.caretPos.text = Open + SelField.caretPos.text + Close;
	} else {
		SelField.value += Open + Close;
		SelField.focus();
	}
}
function InsertCode(code, info, type, error) {
	if (code == 'name') {
		AddSelectedText('[b]' + info + '[/b]', '\n');
	} else if (code == 'url' || code == 'mail') {
		if (code == 'url') var url = prompt(info, 'http://');
		if (code == 'mail') var url = prompt(info, '');
		if (!url) return alert(error);
		if ((clientVer >= 4) && is_ie && is_win) {
			selection = document.selection.createRange().text;
			if (!selection) {
				var title = prompt(type, type);
				AddSelectedText('[' + code + '=' + url + ']' + title + '[/' + code + ']', '\n');
			} else {
				AddSelectedText('[' + code + '=' + url + ']', '[/' + code + ']');
			}
		} else {
			mozWrap(TxtFeld, '[' + code + '=' + url + ']', '[/' + code + ']');
		}
	} else if (code == 'color' || code == 'font' || code == 'size') {
		if ((clientVer >= 4) && is_ie && is_win) {
			AddSelectedText('[' + code + '=' + info + ']', '[/' + code + ']');
		} else if (TxtFeld.selectionEnd && (TxtFeld.selectionEnd - TxtFeld.selectionStart > 0)) {
			mozWrap(TxtFeld, '[' + code + '=' + info + ']', '[/' + code + ']');
		}
	} else if (code == 'li' || code == 'hr') {
		if ((clientVer >= 4) && is_ie && is_win) {
			AddSelectedText('[' + code + ']', '');
		} else {
			mozWrap(TxtFeld, '[' + code + ']', '');
		}
	} else {
		if ((clientVer >= 4) && is_ie && is_win) {
			var selection = false;
			selection = document.selection.createRange().text;
			if (selection && code == 'quote') {
				AddSelectedText('[' + code + ']' + selection + '[/' + code + ']', '\n');
			} else {
				AddSelectedText('[' + code + ']', '[/' + code + ']');
			}
		} else {
			mozWrap(TxtFeld, '[' + code + ']', '[/' + code + ']');
		}
	}
}


function mozWrap(txtarea, open, close)
{
        var selLength = txtarea.textLength;
        var selStart = txtarea.selectionStart;
        var selEnd = txtarea.selectionEnd;
        if (selEnd == 1 || selEnd == 2)
                selEnd = selLength;

        var s1 = (txtarea.value).substring(0,selStart);
        var s2 = (txtarea.value).substring(selStart, selEnd)
        var s3 = (txtarea.value).substring(selEnd, selLength);
        txtarea.value = s1 + open + s2 + close + s3;
        txtarea.focus();
        return;
}

language=1;
richtung=1;
var DOM = document.getElementById ? 1 : 0, 
opera = window.opera && DOM ? 1 : 0, 
IE = !opera && document.all ? 1 : 0, 
NN6 = DOM && !IE && !opera ? 1 : 0; 
var ablauf = new Date();
var jahr = ablauf.getTime() + (365 * 24 * 60 * 60 * 1000);
ablauf.setTime(jahr);
var richtung=1;
var isChat=false;
NoHtml=true;
NoScript=true;
NoStyle=true;
NoBBCode=true;
NoBefehl=false;


setZustand();
function keks(Name,Wert){
	document.cookie = Name+"="+Wert+"; expires=" + ablauf.toGMTString();
}
function changeNoTranslit(Nr){
	if(document.trans.No_translit_HTML.checked)NoHtml=true;else{NoHtml=false}
	if(document.trans.No_translit_BBCode.checked)NoBBCode=true;else{NoBBCode=false}
	keks("NoHtml",NoHtml);keks("NoScript",NoScript);keks("NoStyle",NoStyle);keks("NoBBCode",NoBBCode);
}
function changeRichtung(r){
	richtung=r;keks("TransRichtung",richtung);setFocus()
}
function setFocus(){
	TxtFeld.focus();
}
function repl(t,a,b){
	var w=t,i=0,n=0;
	while((i=w.indexOf(a,n))>=0){
		t=t.substring(0,i)+b+t.substring(i+a.length,t.length);	
		w=w.substring(0,i)+b+w.substring(i+a.length,w.length);
		n=i+b.length;
		if(n>=w.length){
			break;
		}
	}
	return t;
}

</script>



	<div class="editorbutton" OnClick="InsertCode('b')"><img title="Жирный текст" src="pic/editor/bold.gif"></div>
	<div class="editorbutton" OnClick="InsertCode('i')"><img title="Наклонный текст" src="pic/editor/italic.gif"></div>
	<div class="editorbutton" OnClick="InsertCode('u')"><img title="Подчеркнутый текст" src="pic/editor/underline.gif"></div>
	<div class="editorbutton" OnClick="InsertCode('s')"><img title="Перечеркнутый текст" src="pic/editor/striket.gif"></div>
	<div class="editorbutton" OnClick="InsertCode('li')"><img title="Маркированный список" src="pic/editor/li.gif"></div>
	<div class="editorbutton" OnClick="InsertCode('hr')"><img title="Разделительная линия" src="pic/editor/hr.gif"></div>
	<div class="editorbutton" OnClick="InsertCode('left')"><img title="Выравнивание по левому краю" src="pic/editor/left.gif"></div>
	<div class="editorbutton" OnClick="InsertCode('center')"><img title="Выравнивание по центру" src="pic/editor/center.gif"></div>
	<div class="editorbutton" OnClick="InsertCode('right')"><img title="Выравнивание по правому краю" src="pic/editor/right.gif"></div>
	<div class="editorbutton" OnClick="InsertCode('justify')"><img title="Выравнивание по ширине" src="pic/editor/justify.gif"></div>

<div class="editorbutton" OnClick="InsertCode('php')"><img title="PHP-Код" src="pic/editor/php.gif"></div>

<div class="editorbutton" OnClick="InsertCode('spoiler')"><img title="Спойлер" src="pic/editor/spoiler.gif"></div>
<div class="editorbutton" OnClick="InsertCode('quote')"><img title="Цитировать" src="pic/editor/quote.gif"></div>
<div class="editorbutton" OnClick="InsertCode('url','Введите полный адрес','Введите описание','Вы не указали адрес!')"><img title="Вставить ссылку" src="pic/editor/url.gif"></div>

<div class="editorbutton" OnClick="InsertCode('mail','Введите полный адрес','Введите описание','Вы не указали адрес!')"><img title="Вставить E-Mail" src="pic/editor/mail.gif"></div>

<div class="editorbutton" OnClick="InsertCode('img')"><img title="Вставить картинку" src="pic/editor/img.gif"></div>

<div class="editorbutton" OnClick="InsertCode('flash')"><img title="Flash" src="pic/editor/flash.gif"></div>

<div class="editorbutton" OnClick="InsertCode('audio')"><img title="Аудио" src="pic/editor/audio.gif"></div>


<select class="editorinput" tabindex="1" style="font-size:10px;" name="font" onChange="InsertCode('font',this.options[this.selectedIndex].value)">
<option value="Arial">Arial</option>
<option value="Book Antiqua">Antiqua</option>
<option value="Century Gothic">Century</option>
<option value="Comic Sans MS">Comic</option>
<option value="Courier New">Courier</option>
<option value="Fixedsys">Fixedsys</option>
<option value="Franklin Gothic Medium">Franklin</option>
<option value="Garamond">Garamond</option>
<option value="Georgia">Georgia</option>
<option value="Impact">Impact</option>
<option value="Lucida Console">Lucida</option>
<option value="Microsoft Sans Serif">Sans Serif</option>
<option value="Palatino Linotype">Palatino</option>
<option value="System">System</option>
<option value="Tahoma">Tahoma</option>
<option value="Times New Roman">Times New</option>
<option value="Trebuchet MS">Trebuchet</option>
<option value="Verdana">Verdana</option>
</select>
	

<select class="editorinput" tabindex="1" style="font-size:10px;" name="color" onChange="InsertCode('color',this.options[this.selectedIndex].value)">
<option style="color: black" value="Black">Черный</option>
<option style="color: sienna" value="Sienna">Охра</option>
<option style="color: Beige" value="Beige">Бежевый</option>
<option style="color: darkolivegreen" value="DarkOliveGreen">Олив. Зеленый</option>
<option style="color: darkgreen" value="DarkGreen">Т. Зеленый</option>
<option style="color: Cornflower" value="Cornflower">Васильковый</option>
<option style="color: darkslateblue" value="DarkSlateBlue">Гриф.-синий</option>
<option style="color: navy" value="Navy">Темно-синий</option>
<option style="color: MidnightBlue" value="MidnightBlue">Полу.-синий</option>
<option style="color: indigo" value="Indigo">Индиго</option>
<option style="color: darkslategray" value="DarkSlateGray">Синевато-серый</option>
<option style="color: darkred" value="DarkRed">Т. Красный</option>
<option style="color: darkorange" value="DarkOrange">Т. Оранжевый</option>
<option style="color: olive" value="Olive">Оливковый</option>
<option style="color: green" value="Green">Зеленый</option>
<option style="color: DarkCyan" value="DarkCyan">Темный циан</option>
<option style="color: CadetBlue" value="CadetBlue">Серо-синий</option>
<option style="color: Aquamarine" value="Aquamarine">Аквамарин</option>
<option style="color: teal" value="Teal">Морской волны</option>
<option style="color: blue" value="Blue">Голубой</option>
<option style="color: slategray" value="SlateGray">Синевато-серый</option>
<option style="color: dimgray" value="DimGray">Тускло-серый</option>
<option style="color: red" value="Red">Красный</option>
<option style="color: Chocolate" value="Chocolate">Шоколадный</option>
<option style="color: Firebrick" value="Firebrick">Кирпичный</option>
<option style="color: Saddlebrown" value="SaddleBrown">Кож.коричневый</option>
<option style="color: yellowgreen" value="YellowGreen">Желт-Зеленый</option>
<option style="color: seagreen" value="SeaGreen">Океан. Зеленый</option>
<option style="color: mediumturquoise" value="MediumTurquoise">Бирюзовый</option>
<option style="color: royalblue" value="RoyalBlue">Голубой Корол.</option>
<option style="color: purple" value="Purple">Липовый</option>
<option style="color: gray" value="Gray">Серый</option>
<option style="color: magenta" value="Magenta">Пурпурный</option>
<option style="color: orange" value="Orange">Оранжевый</option>
<option style="color: yellow" value="Yellow">Желтый</option>
<option style="color: Gold" value="Gold">Золотой</option>
<option style="color: Goldenrod" value="Goldenrod">Золотистый</option>
<option style="color: lime" value="Lime">Лимонный</option>
<option style="color: cyan" value="Cyan">Зел.-голубой</option>
<option style="color: deepskyblue" value="DeepSkyBlue">Т.Неб.-голубой</option>
<option style="color: darkorchid" value="DarkOrchid">Орхидея</option>
<option style="color: silver" value="Silver">Серебристый</option>
<option style="color: pink" value="Pink">Розовый</option>
<option style="color: wheat" value="Wheat">Wheat</option>
<option style="color: lemonchiffon" value="LemonChiffon">Лимонный</option>
<option style="color: palegreen" value="PaleGreen">Бл. Зеленый</option>
<option style="color: paleturquoise" value="PaleTurquoise">Бл. Бирюзовый</option>
<option style="color: lightblue" value="LightBlue">Св. Голубой</option>
<option style="color: plum" value="Plum">Св. Розовый</option>
<option style="color: white" value="White">Белый</option>
</select>
	

<select class="editorinput" tabindex="1" style="font-size:10px;" name="size" onChange="InsertCode('size',this.options[this.selectedIndex].value)">
	<option value="6">Размер 6</option>
	<option value="8">Размер 8</option>
	<option value="10">Размер 10</option>
	<option value="12">Размер 12</option>
	<option value="14">Размер 14</option>
	<option value="16">Размер 16</option>
	<option value="18">Размер 18</option>
	<option value="20">Размер 20</option>
	<option value="22">Размер 22</option>
	<option value="24">Размер 24</option>
	</select>

</div>



</td>
</tr>
<tr>


<td style="border: none;">
    <div id="shoutbox" style="overflow: auto; height: auto; width: auto;"><font size="-2">
<img title="Загрузка сообщений, Ждите..." src="pic/loading.gif" border="0" /></font></div>
</td>


<td style="border: none;" valign="top" width="10%">
<div id="wol" style="overflow: auto; height: auto; width: auto;"><font size="-2">
<img title="Загрузка кто в сети, Ждите..." src="pic/loading.gif" border="0" />
        </font></div><br />
        
<?
if ($CURUSER["class"] >= UC_MODERATOR){
///////////////

?>

<script>
function usermod_online() {
jQuery.post("block-usermod_jquery.php" , {} , function(response) {
		jQuery("#usermod_online").html(response);
	}, "html");
setTimeout("usermod_online();", 90000);
}
usermod_online();
</script>

<div align=center id="usermod_online"></div>
<?
}
?>
  
</td>
</tr>

<?
print("</table>");

/*<script type=\"text/javascript\" src=\"js/plugins.js\"></script>
<script type=\"text/javascript\">
$(function() {
  $(\"form.expose\").click(function() {
    $(this).expose({
      color: '#ccc',
      onLoad: function() {
        this.getExposed().css({backgroundColor: null});    
      }, 
      onClose: function() {
        this.getExposed().css({backgroundColor: null});   
      }
    });   
  });
});
</script>*/
print("</form>");
end_frame();

?>

<script language="javascript">
function InsertSmilie(texttoins) {
    document.shoutform.shout.value = document.shoutform.shout.value+' '+texttoins+' ';
    document.shoutform.shout.focus();
    return false;
}
</script>

<script language="javascript">
<!--
function winop()
{
windop = window.open("moresmiles.php?form=shoutform&text=shout","mywin","height=500,width=450,resizable=no,scrollbars=yes");
}
function winoptag()
{
windop = window.open("tags.php?form=shbox&text=shbox_text","mywin","height=500,width=450,resizable=no,scrollbars=yes");
}
function winopar()
{
windop = window.open("shoutbox.php?form=shbox&text=shbox_text","mywin","height=500,width=450,resizable=no,scrollbars=yes");
}
//-->
</script>


<div id="loading-layer" style="display: none; font-family: Lucida Sans Unicode; font-size: 11px; width: 200px; height: 100px; background: #EDFCEF; padding: 10px; text-align: center; border: 1px solid #000">
    <div style="font-weight:bold" id="loading-layer-text">
	<font color="red"> Загрузка. Пожалуйста, подождите...</font></div><br />
    <img src="pic/loading.gif" border="0" />
    
</div>


<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript">
<!--
function sendShout(formObj) {

    /*if (postingShout) {
        alert('Отправка сообщения...')
        return false
    }*/

    Shout = formObj.shout.value
    if (Shout.replace(/ /g, '') == '') {
        alert('Вы должны вести сообщение!')
        return false
    }
    sb_Clear();
    var ajax = new tbdev_ajax();
    ajax.onShow ('');
    //ajax.onShow = function() { };
    var varsString = "";
    ajax.requestFile = "shoutbox.php";
    ajax.setVar("do", "shout");
    ajax.setVar("shout", escape(Shout));
    ajax.method = 'GET';
    ajax.element = 'shoutbox';
    ajax.sendAJAX(varsString);

    return false
}

function getShouts() {
    var ajax = new tbdev_ajax();
    ajax.onShow = function() { };
    var varsString = "";
    ajax.requestFile = "shoutbox.php";
    ajax.method = 'GET';
    ajax.element = 'shoutbox';
    ajax.sendAJAX(varsString);
    setTimeout('getShouts()', 18000);
    return false

}

function getWOL() {
    var ajax = new tbdev_ajax();
    ajax.onShow = function() { };
    var varsString = "";
    ajax.requestFile = "online.php";
    ajax.method = 'GET';
    ajax.setVar("wol", 1);
    ajax.element = 'wol';
    ajax.sendAJAX(varsString);
    setTimeout('getWOL()', 30000);
    return false
}

function sb_Clear() {
    document.forms["shoutform"].shout.value = ''
    return true;
}

function deleteShout(id) {
{
        var ajax = new tbdev_ajax();
        ajax.onShow = function() { };
        var varsString = "";
        ajax.requestFile = "shoutbox.php";
        ajax.setVar("do", "delete");
        ajax.setVar("id", id);
        ajax.method = 'GET';
        ajax.element = 'shoutbox';
        ajax.sendAJAX(varsString);
    }
    return false
}

getShouts();
getWOL();
-->
</script>

<?
$seconds = (timer() - $tstart);
$phptime = 	$seconds - $querytime;
$query_time = $querytime;
$percentphp = number_format(($phptime/$seconds) * 100, 2);
$percentsql = number_format(($query_time/$seconds) * 100, 2);
$seconds = 	substr($seconds, 0, 8);
$memory = round(memory_get_usage()/1024);
$time_sql=sprintf("%0.4lf",$querytime);
?>

<script type="text/javascript">
$(document).ready(function() {
$("span.spoiler").hide();
$('<a class="reveal"> <form ><input type=button value="<? echo"Страничка сформирована за $seconds секунд";?>" style="height: 25px; width: 100%"></form></a> ').insertBefore('.spoiler');
$("a.reveal").click(function(){
$(this).parents("h").children("span.spoiler").fadeIn(2500);
$(this).parents("h").children("a.reveal").fadeOut(600);
});
});
</script>

<?
print("<h><span class=\"spoiler\"><noscript><b>Пожалуйста включите показ скриптов</b></noscript>");
print "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr>
<td align=\"center\" class=\"rowhead2\">&nbsp;
".VERSION.TBVERSION." <center><object width=\"150\" height=\"68\" id=\"mju\">
<param name=\"allowScriptAccess\" value=\"sameDomain\" />
<param name=\"swLiveConnect\" value=\"true\" />
<param name=\"movie\" value=\"mju.swf\" />
<param name=\"flashvars\" value=\"swf/playlist=playlist.mpl&auto_run=false&repeat _one=false&shuffle=false\" />
<param name=\"loop\" value=\"false\" />
<param name=\"menu\" value=\"false\" />
<param name=\"quality\" value=\"high\" />
<param name=\"wmode\" value=\"transparent\" />
<embed src=\"swf/mju.swf\" flashvars=\"playlist=swf/playlist.mpl&auto_run=false&repeat_one=false&shuff le=false\" loop=\"false\" menu=\"false\" quality=\"high\" wmode=\"transparent\" bgcolor=\"#ffffff\" width=\"150\" height=\"68\" name=\"mju\" allowScriptAccess=\"sameDomain\" swLiveConnect=\"true\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
</object>
</center>";

if($CURUSER) {
$queries_staff =("<br><b>$queries</b> (queries) - <b>$percentphp%</b> (php) - <b>$percentsql%</b> ($time_sql => sql) ".((get_user_class() == UC_SYSOP && $memory) == "yes" ? " - $memory КБ (use memory)" : "").""); 
}

print(($CURUSER["class"]>=3 ? "Страничка сформирована за <b>$seconds</b> секунд.":""));
	
if($CURUSER["class"]>=3) {
print($queries_staff);
}
  
  
echo "<script type=\"text/javascript\">teasernet_blockid = 169160;teasernet_padid = 56150;</script><script type=\"text/javascript\" src=\"http://comunicazio.com/block.js\"></script>";

print"</td></table></tr>";

print"</span></h>";
  
  ?>