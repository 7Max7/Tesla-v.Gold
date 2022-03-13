<?

/**  Внимание, самая популярная сборка движка теперь доступна в бесплатном скачивании (версия gold за 2010 год)!!!!
* Это старая версия проекта Tesla - торрент сайта. Демо новой версии можно посмотреть по ссылке demo.muz-tracker.net
* Если желаете использовать движок, то оставьте пожалуйста копирайта на своих местах, иначе стабильной работы не гарантируется.
* Если желаете поискать и установить новые моды, советуем воспользоваться форумами: bit-tracker.info, bit-torrent.kiev.ua.
* Если желаете поблагодарить владельца 7Max7 за эту сборку, киньте пару монеток на R190680055855 или Z187703352284
* Если желаете обновить Gold версию до Platinum - придется заплатить около 70$ (с последующими бесплатными обновлениями)
* 
* Данная копия движка взята с сайта demo muz-tracker net и tesla-tracker.net! ))
* Спасибо за внимание к движку Tesla.
**/

if(!defined('IN_TRACKER'))
  die('Hacking attempt!');


function get_user_class_color($class, $username, $italik = false)
{
  global $tracker_lang;
 switch ($username)
 {

   case '7Max7':
   return "<span style=\"color:red ".($italik? "; border-bottom: 1px solid #0f4806; ":"")."\">7</span><span style=\"color:#0f6cee".($italik? "; border-bottom: 1px solid #0f4806; ":"")."\" title=\"Макси\">Max</span><span style=\"color:red".($italik? "; border-bottom: 1px solid #0f4806; ":"")."\">7</span>";
   break;

  
 }
 


 switch ($class)
  {
    case UC_SYSOP:
       return "<span style=\"color:blue ".($italik? "; border-bottom: 1px solid #0f4806; ":"")."\" title=\"".$tracker_lang['class_sysop']."\">" . $username . "</span>";
      break;
    case UC_ADMINISTRATOR:
      return "<span style=\"color:green ".($italik? "; border-bottom: 1px solid #0f4806; ":"")."\" title=\"".$tracker_lang['class_administrator']."\">" . $username . "</span>";
      break;
    case UC_MODERATOR:
      return "<span style=\"color:red ".($italik? "; border-bottom: 1px solid #0f4806; ":"")."\" title=\"".$tracker_lang['class_moderator']."\">" . $username . "</span>";
      break;
     case UC_UPLOADER:
      return "<span style=\"color:#f59555 ".($italik? "; border-bottom: 1px solid #0f4806; ":"")."\" title=\"".$tracker_lang['class_uploader']."\">" . $username . "</span>";
      break;
     case UC_VIP:
      return "<span style=\"color:#9C2FE0 ".($italik? "; border-bottom: 1px solid #0f4806; ":"")."\" title=\"".$tracker_lang['class_vip']."\">" . $username . "</span>";
      break;
     case UC_POWER_USER:
      return "<span style=\"color:#D21E36 ".($italik? "; border-bottom: 1px solid #0f4806; ":"")."\" title=\"".$tracker_lang['class_power_user']."\">" . $username . "</span>";
      break;
     case UC_USER:
      return "<span style=\"color:black ".($italik? "; border-bottom: 1px solid #0f4806; ":"")."\"title=\"".$tracker_lang['class_user']."\">" . $username . "</span>";
       break;
   }
  return $username;
 }


function get_user_rgbcolor($class, $username=false)
{
  global $tracker_lang;
 switch ($username)
 {
 	/// меняем цвета
 	
  
  case 'Тя': /// кому
    return "008000"; /// на
 break;
  

 }

 switch ($class)
  {
    case UC_SYSOP:
      return "0000ff";
      break;
    case UC_ADMINISTRATOR:
      return "008000";
      break;
    case UC_MODERATOR:
      return "ff0000";
      break;
     case UC_UPLOADER:
      return "f59555";
      break;
     case UC_VIP:
      return "9C2FE0";
      break;
     case UC_POWER_USER:
      return "D21E36";
      break;
     case UC_USER:
      return "000000";
       break;
   }
  return $username;
  
 }


function attacks_log($file) {
   global $CURUSER;
   //, $DEFAULTBASEURL;
 //  $ip=getenv("REMOTE_ADDR"); 
 $ip=getip();
   write_log("Попытка взлома. Пользователь: <strong>".$CURUSER['username']."</strong> с Ip: <strong>$ip</strong> Файл: <strong>".$file.".php</strong>","#BCD2E6","error");
   /*
   $subject = sqlesc('Попытка взлома');
   $now = sqlesc(get_date_time());
   $msg = sqlesc('Попытка взлома. Несанкционированный запрос административного файла. Пользователь: [url='.$DEFAULTBASEURL.'/userdetails.php?id='.$CURUSER['id'].']'.$CURUSER['username'].'[/url]. Файл: [b]'.$file.'.php[/b]');
   sql_query("INSERT INTO messages (sender, receiver, added, msg, subject, poster) SELECT 0, id, $now, $msg, $subject, 0 FROM users WHERE class >= ".UC_SYSOP."") or sqlerr(__FILE__,__LINE__);
   */
   }



function display_date_time($timestamp = 0 , $tzoffset = 0){
        return date("Y-m-d H:i:s", $timestamp + ($tzoffset * 60));
}

function cut_text ($txt, $car) {
	while(strlen($txt) > $car) {
	      return substr($txt, 0, $car) . "...";
	}
	return $txt;
}

function textbbcode($form, $name, $content="") {

if (preg_match("/upload/i", $_SERVER["SCRIPT_FILENAME"]))
{ $col="18";}
elseif (preg_match("/edit/i", $_SERVER["SCRIPT_FILENAME"]))
{ 
$col="38";
}
else
$col="11";
	

?>

<script language="javascript" type="text/javascript" src="js/bbcode.js"></script>

<style>
.editbutton { cursor: pointer; padding: 2px 1px 0px 5px; }
</style>
<table cellpadding="0" cellspacing="0" align="сenter">

<tr>
<td class="b">

<div>
			
<div align="center">
<select name="fontFace" class="editbutton">
<option style="font-family: Verdana" value="-1" selected="selected">Шрифт:</option>
<option style="font-family: Courier" value="Courier">&nbsp;Courier</option>
<option style="font-family: Courier New" value="Courier New">&nbsp;Courier New</option>
<option style="font-family: monospace" value="monospace">&nbsp;monospace</option>
<option style="font-family: Fixedsys" value="Fixedsys">&nbsp;Fixedsys</option>
<option style="font-family: Arial" value="Arial">&nbsp;Arial</option>
<option style="font-family: Comic Sans MS" value="Comic Sans MS">&nbsp;Comic Sans</option>
<option style="font-family: Georgia" value="Georgia">&nbsp;Georgia</option>
<option style="font-family: Tahoma" value="Tahoma">&nbsp;Tahoma</option>
<option style="font-family: Times New Roman" value="Times New Roman">&nbsp;Times</option>
<option style="font-family: serif" value="serif">&nbsp;serif</option>
<option style="font-family: sans-serif" value="sans-serif">&nbsp;sans-serif</option>
<option style="font-family: cursive" value="cursive">&nbsp;cursive</option>
<option style="font-family: fantasy" value="fantasy">&nbsp;fantasy</option>
<option style="font-family: Book Antiqua" value="Book Antiqua">&nbsp;Antiqua</option>
<option style="font-family: Century Gothic" value="Century Gothic">&nbsp;Century Gothic</option>
<option style="font-family: Franklin Gothic Medium" value="Franklin Gothic Medium">&nbsp;Franklin</option>
<option style="font-family: Garamond" value="Garamond">&nbsp;Garamond</option>
<option style="font-family: Impact" value="Impact">&nbsp;Impact</option>
<option style="font-family: Lucida Console" value="Lucida Console">&nbsp;Lucida</option>
<option style="font-family: Palatino Linotype" value="Palatino Linotype">&nbsp;Palatino</option>
<option style="font-family: Trebuchet MS" value="Trebuchet MS">&nbsp;Trebuchet</option>
</select>
&nbsp;
<select name="codeColor" class="editbutton">
<option style="color: black; background: #fff;" value="black" selected="selected">Цвет шрифта:</option>
<option style="color: black" value="Black">&nbsp;Черный</option>
<option style="color: sienna" value="Sienna">&nbsp;Охра</option>
<option style="color: Beige" value="Beige">&nbsp;Бежевый</option>
<option style="color: darkolivegreen" value="DarkOliveGreen">&nbsp;Олив. Зеленый</option>
<option style="color: darkgreen" value="DarkGreen">&nbsp;Т. Зеленый</option>
<option style="color: Cornflower" value="Cornflower">&nbsp;Васильковый</option>
<option style="color: darkslateblue" value="DarkSlateBlue">&nbsp;Гриф.-синий</option>
<option style="color: navy" value="Navy">&nbsp;Темно-синий</option>
<option style="color: MidnightBlue" value="MidnightBlue">&nbsp;Полу.-синий</option>
<option style="color: indigo" value="Indigo">&nbsp;Индиго</option>
<option style="color: darkslategray" value="DarkSlateGray">&nbsp;Синевато-серый</option>
<option style="color: darkred" value="DarkRed">&nbsp;Т. Красный</option>
<option style="color: darkorange" value="DarkOrange">&nbsp;Т. Оранжевый</option>
<option style="color: olive" value="Olive">&nbsp;Оливковый</option>
<option style="color: green" value="Green">&nbsp;Зеленый</option>
<option style="color: DarkCyan" value="DarkCyan">&nbsp;Темный циан</option>
<option style="color: CadetBlue" value="CadetBlue">&nbsp;Серо-синий</option>
<option style="color: Aquamarine" value="Aquamarine">&nbsp;Аквамарин</option>
<option style="color: teal" value="Teal">&nbsp;Морской волны</option>
<option style="color: blue" value="Blue">&nbsp;Голубой</option>
<option style="color: slategray" value="SlateGray">&nbsp;Синевато-серый</option>
<option style="color: dimgray" value="DimGray">&nbsp;Тускло-серый</option>
<option style="color: red" value="Red">&nbsp;Красный</option>
<option style="color: Chocolate" value="Chocolate">&nbsp;Шоколадный</option>
<option style="color: Firebrick" value="Firebrick">&nbsp;Кирпичный</option>
<option style="color: Saddlebrown" value="SaddleBrown">&nbsp;Кож.коричневый</option>
<option style="color: yellowgreen" value="YellowGreen">&nbsp;Желт-Зеленый</option>
<option style="color: seagreen" value="SeaGreen">&nbsp;Океан. Зеленый</option>
<option style="color: mediumturquoise" value="MediumTurquoise">&nbsp;Бирюзовый</option>
<option style="color: royalblue" value="RoyalBlue">&nbsp;Голубой Корол.</option>
<option style="color: purple" value="Purple">&nbsp;Липовый</option>
<option style="color: gray" value="Gray">&nbsp;Серый</option>
<option style="color: magenta" value="Magenta">&nbsp;Пурпурный</option>
<option style="color: orange" value="Orange">&nbsp;Оранжевый</option>
<option style="color: yellow" value="Yellow">&nbsp;Желтый</option>
<option style="color: Gold" value="Gold">&nbsp;Золотой</option>
<option style="color: Goldenrod" value="Goldenrod">&nbsp;Золотистый</option>
<option style="color: lime" value="Lime">&nbsp;Лимонный</option>
<option style="color: cyan" value="Cyan">&nbsp;Зел.-голубой</option>
<option style="color: deepskyblue" value="DeepSkyBlue">&nbsp;Т.Неб.-голубой</option>
<option style="color: darkorchid" value="DarkOrchid">&nbsp;Орхидея</option>
<option style="color: silver" value="Silver">&nbsp;Серебристый</option>
<option style="color: pink" value="Pink">&nbsp;Розовый</option>
<option style="color: wheat" value="Wheat">&nbsp;Wheat</option>
<option style="color: lemonchiffon" value="LemonChiffon">&nbsp;Лимонный</option>
<option style="color: palegreen" value="PaleGreen">&nbsp;Бл. Зеленый</option>
<option style="color: paleturquoise" value="PaleTurquoise">&nbsp;Бл. Бирюзовый</option>
<option style="color: lightblue" value="LightBlue">&nbsp;Св. Голубой</option>
<option style="color: plum" value="Plum">&nbsp;Св. Розовый</option>
<option style="color: white" value="White">&nbsp;Белый</option>
</select>
&nbsp;
<select name="codeSize" class="editbutton">
	<option value="12" selected="selected">Размер шрифта:</option>
	<option value="9" class="em">Маленький</option>
	<option value="10">&nbsp;size=10</option>
	<option value="11">&nbsp;size=11</option>
	<option value="12" class="em" disabled="disabled">Обычный</option>
	<option value="14">&nbsp;size=14</option>
	<option value="16">&nbsp;size=16</option>
	<option value="18" class="em">Большой</option>

	<option value="20">&nbsp;size=20</option>
	<option value="22">&nbsp;size=22</option>
	<option value="24" class="em">Огромный</option>
</select>
&nbsp;

<select name="codeAlign" class="editbutton">
   <option value="" selected="selected">Выравнивание:</option>
   <option value="left">&nbsp;По левому краю</option>
   <option value="right">&nbsp;По правому краю</option>
   <option value="center">&nbsp;По центру</option>
   <option value="justify">&nbsp;По ширине</option>
</select>
</div>


<div align="center">

<input class="btn" type="button" value="&#8212;" name="codeHR" title="Горизонтальная линия (Ctrl+8)" style="font-weight: bold; width: 26px;" />
<input class="btn" type="button" value="&para;" name="codeBR" title="Новая строка" style="width: 26px;" />

<input class="btn" type="button" value="Спойлер" name="codeSpoiler" title="Спойлер (Ctrl+S)" style="width: 70px;" />

<input class="btn" type="button" value=" B " name="codeB" title="Жирный текст (Ctrl+B)" style="font-weight: bold; width: 30px;" />
<input class="btn" type="button" value=" i " name="codeI" title="Наклонный текст (Ctrl+I)" style="width: 30px; font-style: italic;" />
<input class="btn" type="button" value=" u " name="codeU" title="Подчеркнутый текст (Ctrl+U)" style="width: 30px; text-decoration: underline;" />
<input class="btn" type="button" value=" s " name="codeS" title="Перечеркнутый текст" style="width: 30px; text-decoration: line-through;" />

<input class="btn" type="button" value=" BB " name="codeBB" title="Чистый bb код (Неотформатированный) (Ctrl+N)" style="font-weight: bold; width: 30px;" />

<input class="btn" type="button" value=" PRE " name="codePRE" title="Преформатный текст (Ctrl+P)" style="width: 40px;" />

<input class="btn" type="button" value=" HTEXT " name="codeHT" title="Скрытие текста при наведение показ (Ctrl+H)" style="width: 60px;" />

<input class="btn" type="button" value=" Marquee " name="codeMG" title="Бегающая строка (Ctrl+M)" style="width: 70px;" />

<input class="btn" type="button" value="Цитата" name="codeQuote" title="Цитирование (Ctrl+Q)" style="width: 60px;" />
<input class="btn" type="button" value="Img" name="codeImg" title="Картинка (Ctrl+R)" style="width: 40px;" />

<?  if (basename($_SERVER['SCRIPT_FILENAME']) == 'edit.php' || basename($_SERVER['SCRIPT_FILENAME']) == 'uploadnext.php'){ $disab1="disabled=\"disabled\""; } ?>

<input class="btn" type="button"  <?=$disab1;?>  value="Цитировать выделение" name="quoteselected" title="Цитировать выделенный текст" style="width: 165px;" onmouseout="bbcode.refreshSelection(false);" onmouseover="bbcode.refreshSelection(true);" onclick="bbcode.onclickQuoteSel();" />&nbsp;


<?  if (basename($_SERVER['SCRIPT_FILENAME']) <> 'edit.php' && basename($_SERVER['SCRIPT_FILENAME']) <> 'uploadnext.php'){ 	$disab="disabled=\"disabled\""; } ?>

<input class="btn" type="button" value="Скрытый" <?=$disab;?> name="codeHIDE" title="Скрытый Текст, пока не прокомментируешь раздачу" style="width: 70px;" />


<input class="btn" type="button" value="URL" name="codeUr" title="URL ссылка" style="width: 40px; text-decoration: underline;" />

<input class="btn" type="button" value="PHP" name="codeCode" title="PHP код (Ctrl+K)" style="width: 46px;" />

<input class="btn" type="button" value="Flash" name="codeFlash" title="Flash анимания (Ctrl+F)" style="width: 50px;" />

<input class="btn" type="button" value="&#8226;" name="codeOpt" title="Маркированый список (Ctrl+0)" style="width: 30px;" />

<input class="btn" type="button" value="Рамка I" name="codeLG1" title="Рамка вокруг текста (Ctrl+1)" style="width: 65px;" />

<input class="btn" type="button" value="Рамка II" name="codeLG2" title="Рамка вокруг текста с цитатой (Ctrl+2)" style="width: 65px;" />

<input class="btn" type="button" value="highlight" name="codeHIG" title="Подсветка синтаксиса" style="width: 60px;" />



<input class="btn" type="button" value=" Смайлы " name="Smailes" title="Смайлы (окно всех смайлов)" style="width: 60px;" onclick="window.open('moresmiles.php?form=<?=$form;?>&text=<?=$name;?>', 'height=500,width=450,resizable=no,scrollbars=yes'); return false;"/>&nbsp;

</div>

<?  if (basename($_SERVER['SCRIPT_FILENAME']) <> 'forums.php'){ ?>
<script type="text/javascript">
(function($) {
var textarea, staticOffset; 
var iLastMousePos = 0;
var iMin = 32;
var grip;
$.fn.TextAreaResizer = function() {
return this.each(function() {
textarea = $(this).addClass('processed'), staticOffset = null;
$(this).wrap('<div class="resizable-textarea"><span></span></div>')
.parent().append($('<div class="grippie"></div>').bind("mousedown",{el: this} , startDrag));
var grippie = $('div.grippie', $(this).parent())[0];
grippie.style.marginRight = (grippie.offsetWidth - $(this)[0].offsetWidth) +'px';
});	};

function startDrag(e) {
textarea = $(e.data.el);
textarea.blur();
iLastMousePos = mousePosition(e).y;
staticOffset = textarea.height() - iLastMousePos;
textarea.css('opacity', 0.25);
$(document).mousemove(performDrag).mouseup(endDrag);
return false;
}

function performDrag(e) {
var iThisMousePos = mousePosition(e).y;
var iMousePos = staticOffset + iThisMousePos;
if (iLastMousePos >= (iThisMousePos)) {
iMousePos -= 5;
}
iLastMousePos = iThisMousePos;
iMousePos = Math.max(iMin, iMousePos);
textarea.height(iMousePos + 'px');
if (iMousePos < iMin) {
endDrag(e);
}
return false;
}

function endDrag(e) {
$(document).unbind('mousemove', performDrag).unbind('mouseup', endDrag);
textarea.css('opacity', 1);
textarea.focus();
textarea = null;
staticOffset = null;
iLastMousePos = 0;
}
function mousePosition(e) {
return { x: e.clientX + document.documentElement.scrollLeft, y: e.clientY + document.documentElement.scrollTop };
};
})(jQuery);

$(document).ready(function() {
$('textarea.resizable:not(.processed)').TextAreaResizer();
});
</script>

<style>
div.grippie {
	background:#EEEEEE url("/pic/grippie.png") no-repeat scroll center 2px;
	border-color:#DDDDDD;
	border-style:solid;
	border-width:0pt 1px 1px;
	cursor:s-resize;
	height:9px;
	overflow:hidden;
}
</style>
<? } ?>


<textarea class="resizable" id="area" name="<?=$name;?>" style="width:100%;" rows="<?=$col;?>" onfocus  = "storeCaret(this);"onselect = "storeCaret(this);"	onclick  = "storeCaret(this);"onkeyup  = "storeCaret(this);"><?=$content;?></textarea>

<script type="text/javascript">
var bbcode = new BBCode(document.<?=$form;?>.<?=$name;?>);
var ctrl = "ctrl";
bbcode.addTag("codeB", "b", null, "B", ctrl);
bbcode.addTag("codeBB", "bb", null, "N", ctrl);
bbcode.addTag("codePRE", "pre", null, "P", ctrl);
bbcode.addTag("codeHT", "hideback", null, "H", ctrl);
bbcode.addTag("codeMG", "marquee", null, "M", ctrl);
bbcode.addTag("codeLG1", "legend", null, "1", ctrl);
bbcode.addTag("codeLG2", function(e) { var v=e.value; e.selectedIndex=0; return "legend=Заголовок" }, "/legend", "2", ctrl);
bbcode.addTag("codeHIDE", "hide", null, "", ctrl);
bbcode.addTag("codeHIG", "highlight", null, "", ctrl);
bbcode.addTag("codeI", "i", null, "I", ctrl);
bbcode.addTag("codeU", "u", null, "U", ctrl);
bbcode.addTag("codeS", "s", null, "", ctrl);
bbcode.addTag("codeQuote", "quote", null, "Q", ctrl);
bbcode.addTag("codeImg", "img", null, "R", ctrl);
bbcode.addTag("codeUr", "url=введите ссылку", "/url", "", ctrl);
bbcode.addTag("codeCode", "php", null, "K", ctrl);
bbcode.addTag("codeFlash", "flash", null, "F", ctrl);
bbcode.addTag("codeOpt", "li", "", "0", ctrl);
bbcode.addTag("codeHR","hr", "", "8", ctrl);
bbcode.addTag("codeBR","br", "", "", ctrl);
bbcode.addTag("codeSpoiler", "spoiler", null, "S",  ctrl);
bbcode.addTag("fontFace", function(e) { var v=e.value; e.selectedIndex=0; return "font="+v+"" }, "/font");
bbcode.addTag("codeColor", function(e) { var v=e.value; e.selectedIndex=0; return "color="+v }, "/color");
bbcode.addTag("codeSize", function(e) { var v=e.value; e.selectedIndex=0; return "size="+v }, "/size");
bbcode.addTag("codeAlign", function(e) { var v=e.value; e.selectedIndex=0; return "align="+v }, "/align");
</script>


</div>

</td>
</tr>
</table>

<?
}

function get_row_count($table, $suffix=false)
{
 if ($suffix)
   $suffix = $suffix;
   
  $r = sql_query("SELECT COUNT(*) FROM ".$table." ".$suffix) or sqlerr(__FILE__,__LINE__);
  $a = mysql_fetch_row($r) or die();
  return $a[0];
}

/*function stdmsg($heading = '', $text = '') {
	print("<table class=\"main\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td class=\"embedded\">\n");
	if ($heading)
		print("<h2>$heading</h2>\n");
	print("<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\"><tr><td class=\"text\">\n");
	print($text . "</td></tr></table></td></tr></table>\n");
}*/

function stdmsg($heading = '', $text = '', $div = 'success', $htmlstrip = false) {
    if ($htmlstrip) {
        $heading = htmlspecialchars_uni(trim($heading));
        $text = htmlspecialchars_uni(trim($text));
    }
    print("<table class=\"main\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td class=\"embedded\">\n");
    print("<div class=\"$div\">".($heading ? "<b>$heading</b><br />" : "")."$text</div></td></tr></table>\n");
}

function stderr($heading = '', $text = '') {
	stdhead();
	stdmsg($heading, $text, 'error');
	stdfoot();
	die;
}

/*
function sqlerr($file = '', $line = '') {
	global $queries;
	print("<table border=\"0\" bgcolor=\"blue\" align=\"left\" cellspacing=\"0\" cellpadding=\"10\" style=\"background: blue\">" .
	"<tr><td class=\"embedded\"><font color=\"white\"><h1>Ошибка в SQL</h1>\n" .
	"<b>Ответ от сервера MySQL: " . htmlspecialchars_uni(mysql_error()) . ($file != '' && $line != '' ? "<p>в $file, линия $line</p>" : "") . "<p>Запрос номер $queries.</p></b></font></td></tr></table>");
	die;
}*/

function sqlerr($file = false, $line = false) {

global $CURUSER,$queries,$war_email; //,$queries

$repair = false;
/// подавляем ошибки чтобы не показать кеш папку
$fp = @fopen(ROOT_PATH."cache/sqlerror.txt", "a+");

$time = get_date_time(); 
$file = getenv("REQUEST_URI");
$getiing = serialize($_GET?$_GET:"")."||".serialize($_POST?$_POST:"");

if (!empty($file))
$file_view="в файле $file,";
if (!empty($line))
$line_view="линия $line";

$mysql_error = mysql_error();

$error = $time." " . $mysql_error." ".$file_view." ".$line_view." ".($CURUSER["username"] ? get_user_class_name($CURUSER["class"]).": ".$CURUSER["username"].". IP: ".$CURUSER["ip"]:"");
  
if (@file_exists(ROOT_PATH."cache/sqlerror.txt") && !stristr($mysql_error,"doesn't exist")){
$open = file_get_contents(ROOT_PATH."cache/sqlerror.txt");


if (stristr($mysql_error,"interrupted"))
unset($war_email);


if (!stristr($open,$mysql_error." ".$file_view." ".$line_view)){
$all = "$error \r\n Ошибка в SQL <b>Ответ от сервера MySQL</b>: " . $mysql_error . ((!empty($file) && !empty($line)) ? " в <b>".$file."</b>, линия <b>".$line."</b> " : "") . " Запрос номер ".$queries;

if ($_SERVER["SERVER_ADDR"] <> $_SERVER["REMOTE_ADDR"] && !empty($war_email))
sent_mail($war_email,$SITENAME,$SITEEMAIL,"Ошибка запроса на сайте ".$SITENAME,$all,false);	

@fputs($fp,"\r$error#$getiing\n"); /// записываем если таких данных еще нет в файле.
}
}

@fclose($fp);

if (stristr($mysql_error,"repair")){
$repair = true;

preg_match("/\\'(.*?)\\'/is", $mysql_error, $tablep);
$table = $tablep[1];
if (!empty($table))
repair_table($table);
else
repair_table();
//die($table);
}

if (get_user_class() > UC_MODERATOR || !stristr($mysql_error,"doesn't exist"))
stdmsg("Ошибка в SQL", "<b>Ответ от сервера MySQL</b>: " . $mysql_error . ((!empty($file) && !empty($line)) ? " в <b>".$file."</b>, линия <b>".$line."</b>" : "") . " Запрос номер <b>".$queries."</b>. ".(!empty($all) ? "<br> Ошибка включена в протокол.":"")."",(($repair==true || empty($all)) ? "success":"error"));
else
echo("Запрос не выполнен, ошибка.");	

stdfoot();
die;
}


function get_date_time($timestamp = 0) {
	if ($timestamp)
		return date("Y-m-d H:i:s", $timestamp);
	else
		return date("Y-m-d H:i:s");
}

function encodehtml($s, $linebreaks = true) {
	$s = str_replace("<", "&lt;", str_replace("&", "&amp;", $s));
	if ($linebreaks)
		$s = nl2br($s);
	return $s;
}

function get_dt_num() {
	return date("YmdHis");
}

function format_quotes($s) {
//	global $lang;
	preg_match_all('/\\[quote.*?\\]/', $s, $result, PREG_PATTERN_ORDER);
	$openquotecount = count($openquote = $result[0]);
   preg_match_all('/\\[\/quote\\]/', $s, $result, PREG_PATTERN_ORDER);
	$closequotecount = count($closequote = $result[0]);

   if ($openquotecount != $closequotecount) 
   return $s;
	$openval = array();
	$pos = -1;
   foreach($openquote as $val)
   $openval[] = $pos = strpos($s,$val,$pos+1);
   $closeval = array();
   $pos = -1;

   foreach($closequote as $val)
    $closeval[] = $pos = strpos($s,$val,$pos+1);


   for ($i=0; $i < count($openval); $i++)
	if ($openval[$i] > $closeval[$i]) 
	return $s;

	$s = str_replace("[quote]","<fieldset><legend><font class=\"editorinput\">Цитата</font></legend><br>",$s);
	$s = preg_replace("/\\[quote=(.+?)\\]/","<fieldset><legend><font class=\"editorinput\">\\1 писал</font></legend>", $s);
	$s = str_replace("[/quote]","</fieldset>",$s);
	return $s;
}

// Цитата
function encode_quote($text) {
	$start_html = "<div align=\"center\"><div style=\"width: 85%; overflow: auto\">"
	."<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\" align=\"center\" class=\"bgcolor4\">"
	."<tr bgcolor=\"FFE5E0\"><td><font class=\"block-title\">Цитата</font></td></tr><tr class=\"bgcolor1\"><td>";
	$end_html = "</td></tr></table></div></div>";
	$text = preg_replace("#\[quote\](.*?)\[/quote\]#si", "".$start_html."\\1".$end_html."", $text);
	return $text;
}

// Авторская цитата
function encode_quote_from($text) {
	$start_html = "<div align=\"center\"><div style=\"width: 85%; overflow: auto\">"
	."<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\" align=\"center\" class=\"bgcolor4\">"
	."<tr bgcolor=\"FFE5E0\"><td><font class=\"block-title\">\\1 писал</font></td></tr><tr class=\"bgcolor1\"><td>";
	$end_html = "</td></tr></table></div></div>";
	$text = preg_replace("#\[quote=(.+?)\](.*?)\[/quote\]#si", "".$start_html."\\2".$end_html."", $text);
	return $text;
}

function clearbb($str) {

//$strs=array("[b]","[/b]","[i]","[/i]","[img]","[/img]","[spoiler]","[/spoiler]","[u]","[/u]","[s]","[/s]","[h]","[/h]");
//$str = str_replace($strs, '', $str);

$str = preg_replace("/\[((\s|.)+?)\]/is", "", $str);

return $str;
}
   
function regex_php_tag($php=false) {

$o = array("&#036;","&quot;","&#092;","&lt;","&gt;","&#39;","&#33;","&amp;","[php]","[/php]");
$w = array("$","\"","\\\\","<",">","'","!","&","","");

$php = str_replace($o,$w,$php);
//$php = str_replace("\n\r","",$php);

$code = stripslashes($php);
$md5 = md5($code);

$code = "<?php\n".trim($code)."\n?>";
$code = highlight_string($code,true);
$code = "<div style=\"margin: 3px 15px 15px;\"><div style=\"margin: 0px; padding: 3px; border: 1px inset; width: 500px; height: 150px; overflow: auto;\"><code style=\"white-space: nowrap;\">
".substr($code,79);
$code = substr($code,0,-70)."</code></div></div>";

return "<!--php md5: $md5-->{$code}<!--php md5: $md5-->";
}

function agetostr($age) {
   if(($age>=5) && ($age<=14)) $str = "лет";
   else {
   $num = $age - (floor($age/10)*10);

   if($num == 1) { $str = "год"; }
   elseif($num == 0) { $str = "лет"; }
   elseif(($num>=2) && ($num<=4)) { $str = "года"; }
   elseif(($num>=5) && ($num<=9)) { $str = "лет"; }
   }
   return $age . " " . $str ;
}

function tostr($age) {
  // if(($age>=5) && ($age<=14)) $str = "лет";
   //else {
   $num = $age - (floor($age/10)*10);
   $num2 = (floor($age/10)*10);

   if($num == 1) { $str = "раз"; }
 //  elseif($num == 0) { $str = "раз"; }
   elseif(($num>=2) && ($num<=4)) { $str = "раза"; }
   elseif(($num>=5) && ($num<=20)) { $str = "раз"; }
 //  }
   return $age . " " . $str ;
}


function get_user_id() {
  global $CURUSER;
  return $CURUSER["id"];
}

function comment_hide($text) {
    $html = "<div align=\"left\"><div style=\"width: 100%; overflow: auto\">" 
    ."<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\" align=\"center\" class=\"bgcolor4\">" 
    ."<tr bgcolor=\"red\"><td><b>Скрытый текст</b></td></tr>
	<tr class=\"bgcolor1\">
	<td>" 
    ."<center><color=\"red\">.:: Необходимо оставить комментарий, чтобы увидеть скрытый текст ::.</color></center>
	</td></tr></table></div></div>"; 
	
    $start_html = "<div align=\"left\"><div style=\"width: 85%; overflow: auto\">" 
    ."<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\" align=\"center\" class=\"bgcolor4\">" 
    ."<tr bgcolor=\"pink\"><td><font class=\"rowhead\">Скрытый текст</font></td></tr><tr class=\"bgcolor1\"><td>"; 

    $end_html = "</td></tr></table></div></div>";
    
    if (basename($_SERVER['SCRIPT_FILENAME']) == 'details.php'){
    $id = (int) $_GET["id"];
    $res = sql_query("SELECT COUNT(*) FROM comments WHERE torrent=".$id." and user=".$CURUSER["id"]); 
    $row = mysql_fetch_array($res);
    }

    
    if (get_user_class() >= UC_MODERATOR or $row[0])
    $text = preg_replace("#\[hide\](.*?)\[/hide\]#si", "".$start_html."\\1".$end_html."", $text); 
    else 
    $text = preg_replace("#\[hide\](.*?)\[/hide\]#si", "".$html."", $text); 

    return $text; 
}

function format_urls($s) {

return preg_replace("/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps):\/\/[^()<>\s]+)/i","\\1<a rel=\"nofollow\" href=\"redir.php?url=\\2\">\\2</a>", $s);
}

function format_comment($text, $strip_html = true, $xssclean = false) {

	global $smilies,$CURUSER,$BASEURL;	
	$s = $text;
	
	$host = basename($_SERVER['SCRIPT_FILENAME']);


 //// если два домена иначе убрать!
    $site = parse_url($BASEURL, PHP_URL_HOST);
    
    if ($site=="tesla-tracker.net")
    $s = str_replace("www.muz-trackers.ru", "tesla-tracker.net", $s);
    //unset($site);
 //// если два домена иначе убрать!


	$s = str_replace(";)", ";-)", $s);
///	$s = str_replace("&amp;", "", $s);
//	if ($strip_html)
///	$s = htmlspecialchars_uni($s);



/// ПЕРЕРОВЕРИТЬ!!!
	$search = array("'<script[^>]*?>.*?</script>'si","'&(quot|#34|#034|#x22);'i","'&(amp|#38|#038|#x26);'i",);
	$replace = array("","\"","&",);
	$s = preg_replace($search,$replace,$s);
/// ПЕРЕРОВЕРИТЬ!!!

    $counter=0;
    $match_count = preg_match_all("#\[bb\](.*?)\[/bb\]#si", $s, $matches);

    if ($match_count) {

    for ($mout = 0; $mout < $match_count; ++$mout){

    $s_html = "<div style=\"width: 95%; overflow: auto\" align=\"center\">
	<table width=\"100%\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\" align=\"center\">
	<tr><td colspan=\"2\" class=\"a\"><font class=\"block-title\">bb code (исходный bb код)</font></td></tr>
	<tr><td  class=\"b\">";
    $e_html = "</td></tr></table></div>";

         $t_b = str_replace("\n", "<br>", $matches[1][$mout]);
         $add_text[]= $s_html.$t_b.$e_html;
         ++$counter;
         }
    }


//	if ($xssclean)
//	$s = xss_clean($s);
	// замена ' на пробел
  ///  $s=preg_replace("/'/i"," ",$s );

/*
if (empty($CURUSER)){
$s = preg_replace( "#\[url\s*=\s*\& quot\;\s*(\S+?)\s*\& quot\;\s*\](.*?)\[\/url\]#i" , "\\2", $s );
$s = preg_replace( "#\[url\s*=\s*(\S+?)\s*\](.*?)\[\/url\]#i", "\\2", $s );
//$s = preg_replace( "#\[url\](\S+?)\[/url\]#i", "[b][i]Вы - гость и не можете видеть ссылки. [url=$BASEURL]Зарегистрируйтесь![/url][/i][/b]", $s );
}
*/

	// [b]жирный[/b]
	$s = preg_replace("/\[b\]((\s|.)+?)\[\/b\]/is", "<b>\\1</b>", $s);

//	if (preg_match("#\[code\](.*?)\[/code\]#si", $s)) $s = encode_code($s);
	
	if ($host=="message.php" || $host=="details.php") {
	if (preg_match("#\[php\](.*?)\[/php\]#si", $s)) 
	$s = regex_php_tag($s);
	}
	
	// [i]курсив[/i]
	$s = preg_replace("/\[i\]((\s|.)+?)\[\/i\]/is", "<i>\\1</i>", $s);

	// [h]крупный[/h]
	$s = preg_replace("/\[h\]((\s|.)+?)\[\/h\]/is", "<h3>\\1</h3>", $s);

	// [u]подчеркнутый[/u]
	$s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/is", "<u>\\1</u>", $s);

	// [s]зачеркнутый[/s]
	$s = preg_replace("#\[s\](.*?)\[/s\]#si", "<s>\\1</s>", $s);

	// [li]
	$s = preg_replace("#\[li\]#si", "<li>", $s);
	
	// [hr]
	$s = preg_replace("#\[hr\]#si", "<hr>", $s);

	// [br]
	$s = preg_replace("#\[br\]#si", "<br>", $s);
	// [*]
	$s = preg_replace("/\[\*\]/", "<li>", $s);
	   	
///////////////////

	// [audio]http://allsiemens.com/mp3/files/1.mp3[/audio]
	$s = preg_replace("/\[audio\]([^()<>\s]+?)\[\/audio\]/is","<embed autostart=\"false\" loop=\"false\" controller=\"true\" width=\"220\" height=\"42\" src=\\1></embed>", $s);
	   

	// [img]http://www/image.gif[/img]
	$s = preg_replace("/\[img\](http:\/\/[^\s'\"<>]+(\.(jpg|jpeg|gif|png)))\[\/img\]/is", "<img border=\"0\" src=\"\\1\" alt=\"\\1\">", $s);

	// [img=http://www/image.gif]
	$s = preg_replace("/\[img=(http:\/\/[^\s'\"<>]+(\.(gif|jpeg|jpg|png)))\]/is", "<img border=\"0\" src=\"\\1\" alt=\"\\1\">", $s);

	// [color=blue]Текст[/color]
	$s = preg_replace("/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/is","<font color=\\1>\\2</font>", $s);

	// [color=#ffcc99]Текст[/color]
	$s = preg_replace("/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]/is","<font color=\\1>\\2</font>", $s);

  // [url=http://www.example.com]Пример[/url]
    if ($host=="shoutbox.php")
        $s = preg_replace("/\[url=(http:\/\/[^()<>\s]+?)\]((\s|.)+?)\[\/url\]/is","<a href=\"redir.php?url=\\1\" rel=\"nofollow\" target=\"_blank\">\\2</a>", $s);
    else {

   	$s = preg_replace("/\[url=(http:\/\/$site.+?)\]((\s|.)+?)\[\/url\]/is","<a title=\"Локальная ссылка\" href=\"\\1\" rel=\"nofollow\">\\2</a>", $s);
   	
    $s = preg_replace("/\[url=(http:\/\/[^()<>\s]+?)\]((\s|.)+?)\[\/url\]/is","<a title=\"Внешняя ссылка\" href=\"redir.php?url=\\1\" rel=\"nofollow\" target=\"_self\">\\2</a>", $s);
}

    // [url]http://www.example.com[/url]
    if ($host=="shoutbox.php")
    $s = preg_replace("/\[url\](http:\/\/[^()<>\s]+?)\[\/url\]/is","<a href=\"redir.php?url=\\1\" rel=\"nofollow\" target=\"_blank\">\\1</a>", $s);
    else {

    $s = preg_replace("/\[url\](http:\/\/$site.+?)\[\/url\]/is","<a href=\"\\1\" title=\"Локальная ссылка\" rel=\"nofollow\">\\1</a>", $s);
    $s = preg_replace("/\[url\](http:\/\/[^()<>\s]+?)\[\/url\]/is","<a title=\"Внешняя ссылка\" href=\"redir.php?url=\\1\" rel=\"nofollow\" target=\"_self\">\\1</a>", $s);

	}

	
	// поставил старые теги влево вправо, пришлось переделывать для этого.
    $s = preg_replace("#\[(left|right|center|justify)\](.*?)\[/\\1\]#is", "<div align=\"\\1\">\\2</div>", $s);

    $s = preg_replace("#\[align=(left|right|center|justify)\](.*?)\[/align\]#is", "<div align=\"\\1\">\\2</div>", $s);

	// [size=4]Text[/size] (поставил старый*)
	$s = preg_replace("#\[size=([0-9]+)\](.*?)\[/size\]#si","<span style=\"font-size: \\1\">\\2</span>", $s);

// [font=Arial]Text[/font]
	$s = preg_replace("/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/is","<font face=\"\\1\">\\2</font>", $s);

	// защита символов и тегов стандартных
//	$s = preg_replace( "#<(\s+?)?s(\s+?)?c(\s+?)?r(\s+?)?i(\s+?)?p(\s+?)?t#is", "&lt;script", $s );
//	$s = preg_replace( "#<(\s+?)?/(\s+?)?s(\s+?)?c(\s+?)?r(\s+?)?i(\s+?)?p(\s+?)?t#is", "&lt;/script", $s );
	
	
	$array=array("javascript","alert","<body","<html");
	$s = str_replace($array, "", $s);
	
	$s = format_quotes($s);
	

	
//	$s = format_urls($s);
	
//	$s = preg_replace("/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps):\/\/[^()<>\s]+)/i","\\1<a rel=\"nofollow\" href=\"redir.php?url=\\2\">\\2</a>", $s);
	$s = preg_replace("/(\\n)((http|ftp|https|ftps):\/\/[^()<>\s]+)/is","\\1<a title=\"Внешняя ссылка (не вписанная в тег URL)\" rel=\"nofollow\" href=\"redir.php?url=\\2\">\\2</a>", $s);

	
	
    if ($host=='details.php'){
    if (preg_match("#\[hide\](.*?)\[/hide\]#si", $s)) 
	$s = comment_hide($s); 
    }
 
	$s = nl2br($s);

	// [pre]Preformatted[/pre]	
	$s = preg_replace("/\[pre\](.*?)\[\/pre\]/is", "<pre>".htmlspecialchars('\\1')."</pre>", $s);

	// [nfo]NFO-preformatted[/nfo]
///	$s = preg_replace("/\[nfo\](.*?)\[\/nfo\]/is", "<tt><nobr><font face='MS Linedraw' size='2' style='font-size: 10pt; line-height: 10pt'>\\1</font></nobr></tt>", $s);		

	// [highlight]Highlight text[/highlight]
    $s = preg_replace("/\[highlight\]((\s|.)+?)\[\/highlight\]/is", "<table border=0 cellspacing=0 cellpadding=1>"."<tr><td bgcolor=white><b>\\1</b></td></tr>"."</table>", $s);

	$s = preg_replace("/\[hideback\]((\s|.)+?)\[\/hideback\]/is", "<span onmouseout=\"this.style.color='#DDDDDD';\" onmouseover=\"this.style.color='#002AFF';\" style=\"background: #DDDDDD none repeat scroll 0% 0%;font-weight: bold; font-size: small;  color: #DDDDDD; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; cursor: help;\">\\1</span>", $s);

    $s = preg_replace("/\[legend=\s*((\s|.)+?)\s*\]((\s|.)+?)\[\/legend\]/is", "<fieldset><legend>\\1</legend>\\3</fieldset>", $s);  

    $s = preg_replace("/\[legend\]\s*((\s|.)+?)\s*\[\/legend\]\s*/is","<fieldset>\\1</fieldset>", $s);  //<legend></legend>

  	// [light]Highlight text[/light]
    $s = preg_replace("/\[light=([a-zA-Zа-яА-Я0-9_]+)\]((\s|.)+?)\[\/light\]/is", "
	<script language=\"JavaScript1.2\">
<!--var ns6=document.getElementById&&!document.all?1:0 var head=\"display:''\"  var folder='' function expandit(curobj){ folder=ns6?curobj.nextSibling.nextSibling.style:document.all[curobj.sourceIndex+1].style if (folder.display==\"none\") folder.display=\"\" else folder.display=\"none\"}//-->
</script><center><h3 style=\"cursor:hand\" onClick=\"expandit(this)\">\\1</h3><span style=\"display:none\" style=&{head};>\\2</span></center>", $s);


  // [marquee]Marquee[/marquee]
    $s = preg_replace("/\[marquee\]((\s|.)+?)\[\/marquee\]/is", "<marquee behavior=\"alternate\">\\1</marquee>", $s);

    //[hr=#ffffff] [hr=red]
   // $s = preg_replace("/\[hr=((#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])|([a-zA-z]+))\]/i", "<hr color=\"\\1\"/>", $s);

    //[flash=320x240]http://somesite.com/test.swf[/flash]
    $s = preg_replace("/\[flash=(\d{1,3}):(\d{1,3})\]((www.|http:\/\/|https:\/\/)[^\s]+(\.swf))\[\/flash\]/is","<param name=movie value=\\3/><embed width=\\1 height=\\2 src=\\3></embed>", $s, 3);

    //[flash]http://somesite.com/test.swf[/flash]
    $s = preg_replace("/\[flash]((www.|http:\/\/|https:\/\/)[^\s'\"<>&]+(\.swf))\[\/flash\]/is","<param name=movie value=\\1/><embed width=470 height=310 src=\\1></embed>", $s, 3);

    // [mcom=#FFD42A:#002AFF]Text[/mcom]
	$s = preg_replace("/\[mcom=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9]):(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/mcom\]/is","<div style=\"background-color: \\1; color: \\2; font-weight: bold; font-size: small;\">\\3</div>", $s);

	// [pi]
    $s = str_replace ("[pi]", "<br><center>&nbsp;--------------<b>Линия отрыва</b>--------------</center><br>", $s);

	// [me]
    $s = str_replace ("[me]", "<script type=\"text/javascript\" src=js/blink.js></script><blink><font title='Имею мнение, и не оспоришь!!!' color=red>IMHO</font></blink>&nbsp;", $s);

	// Maintain spacing
//	$s = str_replace("  ", " &nbsp;", $s);
	
	//linebreak
	$s = wordwrap($s, 100, "\n", 1);

    // YouTube Vids [video=http://www.youtube.com/watch?v=4HmeA_vHjzY]
//    $s = preg_replace("/\[video=[^\s'\"<>]*youtube.com.*v=([^\s'\"<>]+)\]/ims", "<object width=\"500\" height=\"410\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1\"></param><embed src=\"http://www.youtube.com/v/\\1\" type=\"application/x-shockwave-flash\" width=\"500\" height=\"410\"></embed></object>", $s);
   
/// youtube тег video
$s = preg_replace("/\[video=(http:\/\/www.youtube[^()<>\s]+?)\]/is", "<object width='640' height='505'><param name=movie value='\\1&hl=ru&fs=1&'></param><param name='allowFullScreen' value='true'></param><param name='allowscriptaccess' value='always'></param><embed src='\\1&hl=ru&fs=1&' type='application/x-shockwave-flash' allowscriptaccess='always' allowfullscreen='true' width='640' height='505'></embed></object>", $s);


    // Google Vids
    $s = preg_replace("/\[video=[^\s'\"<>]*video.google.com.*docid=(-?[0-9]+).*\]/ims", "<embed style=\"width:500px; height:410px;\" id=\"VideoPlayback\" align=\"middle\" type=\"application/x-shockwave-flash\" src=\"http://video.google.com/googleplayer.swf?docId=\\1\" allowScriptAccess=\"sameDomain\" quality=\"best\" bgcolor=\"#ffffff\" scale=\"noScale\" wmode=\"window\" salign=\"TL\" FlashVars=\"playerMode=embedded\"></embed>", $s);


//[spoiler]наш спойлер[/spoiler] 

        global $nummatch;
 
        while(preg_match("/\[spoiler=\s*((\s|.)+?)\s*\]((\s|.)+?)\[\/spoiler\]/is",$s)) {
        $s = preg_replace("/\[spoiler=\s*((\s|.)+?)\s*\]((\s|.)+?)\[\/spoiler\]/is",   
        "<div class=\"spoiler-wrap\" id=\"$nummatch\"><div class=\"spoiler-head folded clickable\">".trim('\\1')."</div><div class=\"spoiler-body\" style=\"display: none;\">".trim('\\3')."</div></div>", $s,1);
        ++$nummatch;
        if($nummatch>100) break; 
		}

        while(preg_match("/\[spoiler\]\s*((\s|.)+?)\s*\[\/spoiler\]\s*/is",$s)) {
        $s = preg_replace("/\[spoiler\]\s*((\s|.)+?)\s*\[\/spoiler\]\s*/is",   
        "<div class=\"spoiler-wrap\" id=\"$nummatch\"><div class=\"spoiler-head folded clickable\">Спойлер (нажмите для просмотра содержимого)</div><div class=\"spoiler-body\" style=\"display: none;\">".trim('\\1')."</div></div>", $s,1);  
        ++$nummatch;
        if($nummatch>100) break;
		}


if ($host == 'details.php' || $host == 'shoutbox.php' || $host == 'polloverview.php' || $host == 'newsoverview.php' || $host == 'forums.php' || $host == 'message.php'){
$num=0;

reset($smilies);
while (list($code, $url) = each($smilies))


if ($host=="shoutbox.php"){
$s = str_replace($code, "<img title=\"Кликни по смайлу чтобы добавить в поле для ввода!\" border=\"0\" onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value=parent.document.shoutform.shout.value+' " . htmlspecialchars_uni($code) . " ';return false;\" style=\"font-weight: italic;\" src=\"pic/smilies/$url\" alt=\"" . htmlspecialchars_uni($code) . "\">", $s); 
/*
++$num;  /// изменил с 1 на 5 
if($num>50) break; 
*/
}
else
{
$s = str_replace($code, "<img border=\"0\" src=\"pic/smilies/$url\">", $s);

//++$num;  /// изменил с 1 на 5 
//if($num>100) break; 
}



}

     if ($counter>0) {
      $is=0;
      while($is<$counter) {
      $s = preg_replace("#\[bb\](.*?)\[/bb\]#si", $add_text[$is], $s,1);
       ++$is;
      }
      }

 //if (basename($_SERVER['SCRIPT_FILENAME']) == 'details.php')
// $s=clearbb($s);

	return $s;
	unset($smilies,$s);    //unset($privatesmilies);
}

function get_user_class() {
  global $CURUSER;
  return $CURUSER["class"];
}

function get_user_class_name($class) {
  global $tracker_lang;
  switch ($class) {
    case UC_USER: return $tracker_lang['class_user'];

    case UC_POWER_USER: return $tracker_lang['class_power_user'];

    case UC_VIP: return $tracker_lang['class_vip'];

    case UC_UPLOADER: return $tracker_lang['class_uploader'];

    case UC_MODERATOR: return $tracker_lang['class_moderator'];

    case UC_ADMINISTRATOR: return $tracker_lang['class_administrator'];

    case UC_SYSOP: return $tracker_lang['class_sysop'];
  }
  return "";
}

function is_valid_user_class($class) {
  return is_numeric($class) && floor($class) == $class && $class >= UC_USER && $class <= UC_SYSOP;
}

function is_valid_id($id) {
  return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}

function sql_timestamp_to_unix_timestamp($s) {
  return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
}

  function get_ratio_color($ratio) {
    if ($ratio < 0.1) return "#ff0000";
    if ($ratio < 0.2) return "#ee0000";
    if ($ratio < 0.3) return "#dd0000";
    if ($ratio < 0.4) return "#cc0000";
    if ($ratio < 0.5) return "#bb0000";
    if ($ratio < 0.6) return "#aa0000";
    if ($ratio < 0.7) return "#990000";
    if ($ratio < 0.8) return "#880000";
    if ($ratio < 0.9) return "#770000";
    if ($ratio < 1) return "#660000";
    return "#000000";
  }



  function get_slr_color($ratio) {
    if ($ratio < 0.025) return "#ff0000";
    if ($ratio < 0.05) return "#ee0000";
    if ($ratio < 0.075) return "#dd0000";
    if ($ratio < 0.1) return "#cc0000";
    if ($ratio < 0.125) return "#bb0000";
    if ($ratio < 0.15) return "#aa0000";
    if ($ratio < 0.175) return "#990000";
    if ($ratio < 0.2) return "#880000";
    if ($ratio < 0.225) return "#770000";
    if ($ratio < 0.25) return "#660000";
    if ($ratio < 0.275) return "#550000";
    if ($ratio < 0.3) return "#440000";
    if ($ratio < 0.325) return "#330000";
    if ($ratio < 0.35) return "#220000";
    if ($ratio < 0.375) return "#110000";
    return "#000000";
  }

  function get_comm_color($stepen) {
  if ($stepen < 5) return "#2e0101";
  if ($stepen < 10) return "#610000";
  if ($stepen < 15) return "#960505";
  if ($stepen < 20) return "#930000";
  if ($stepen < 25) return "#460088";
  if ($stepen < 25) return "#460088";
  if ($stepen < 25) return "#460088";
  if ($stepen < 30) return "#000388";
  if ($stepen < 35) return "#0005c5";
  if ($stepen < 40) return "#0208ff";
  if ($stepen < 50) return "#3802ff";
  if ($stepen < 60) return "#7902ff";
  if ($stepen < 70) return "#af02ff";
  if ($stepen < 80) return "#ff02f0";
  if ($stepen < 90) return "#ff029d";
  if ($stepen <> 100) return "#ff023e";

 
  //  return "#000000";
  }

function write_log($text, $color = "transparent", $type = "tracker") {
  $type = sqlesc($type);
  $color = sqlesc($color);
  $text = sqlesc($text);
  $added = sqlesc(get_date_time());
  sql_query("INSERT INTO sitelog (added, color, txt, type) VALUES($added, $color, $text, $type)");
}

function check_banned_emails ($email) {
$allowed_emails=array( 
"rambler.ru"=>1,
"gmail.com"=>2,
"live.ru"=>3,
"mail.ru"=>4,
"idknet.com"=>5,
"yandex.ru"=>6
); 

    $expl = explode("@", $email); 
    $wildemail = "*@".$expl[1]; 
    if($expl[2]!="")
     stderr("Ошибка!","Неверный емайл!", false);    
    if (!array_key_exists($expl[1], $allowed_emails))
      stderr("Ошибка!","Этот емайл адресс забанен!<br /><br /><strong>Разрешены почты на доменах</strong>:<br>@rambler.ru<br>@gmail.com<br>@live.ru<br>@mail.ru<br>@idknet.com", false); 
    $res = mysql_query("SELECT id, comment FROM bannedemails WHERE email = ".sqlesc($email)." OR email = ".sqlesc($wildemail)."") or sqlerr(__FILE__, __LINE__); 
    if ($arr = mysql_fetch_assoc($res)) 
    stderr("Ошибка!","Этот емайл адресс забанен!<br /><br /><strong>Причина</strong>: $arr[comment]", false); 
}

function random_color() {
	$allsymb="0123456789ABCDEF";
	$color="#";
	for($i=0;$i<6;$i=$i) 
	{
		$n=rand (0,15);
		if ($n) 
		{
			$color.= $allsymb[$n];
			$i++;
		}
	}
	return $color;
	/// возращаем код цвета с #
}

function mtime($time, $ntime, $end1, $end2, $end3) {
	$mtime = "" . $time;
	$time = $mtime [strlen ( $mtime ) - 1];
	$time2 = $mtime [strlen ( $mtime ) - 2] . $mtime [strlen ( $mtime ) - 1];
	return $mtime . " " . $ntime . ($time > 1 && $time < 5 && ! ($time2 >= 10 && $time2 < 20) ? $end1 : ($time >= 5 || $time == 0 || $time2 >= 10 && $time2 < 20 ? $end2 : ($time == 1 ? $end3 : "")));
}


function get_elapsed_time($U,$showseconds=true){
    if(!$U) return "N/A";
    $N = time();
    if ($N>=$U)
    $diff = $N-$U;
    else
    $diff = $U-$N;
    //year (365 days) = 31536000
    //month (30 days) = 2592000
    //week = 604800
    //day = 86400
    //hour = 3600

    if($diff>=31536000){
        $Iyear = floor($diff/31536000);
        $diff = $diff-($Iyear*31536000);
    }
    if($diff>=2629800){    //2592000 seconds in month with 30 days
        $Imonth = floor($diff/2629800);
        $diff = $diff-($Imonth*2629800);
    }
    if($diff>=604800){
        $Iweek = floor($diff/604800);
        $diff = $diff-($Iweek*604800);
    }
    if($diff>=86400){
        $Iday = floor($diff/86400);
        $diff = $diff-($Iday*86400);
    }
    if($diff>=3600){
        $Ihour = floor($diff/3600);
        $diff = $diff-($Ihour*3600);
    }
    if($diff>=60){
        $Iminute = floor($diff/60);
        $diff = $diff-($Iminute*60);
    }
    if($diff>0){
        $Isecond = floor($diff);
    }

    $j = " ";

    $ret = "";

    if(isset($Iyear)) $ret .= $Iyear." ".rusdate($Iyear,'year').$j;
    if(isset($Imonth)) $ret .= $Imonth ." ".rusdate($Imonth ,'month').$j;
    if(isset($Iweek)) $ret .= $Iweek ." ".rusdate($Iweek ,'week').$j;
    if(isset($Iday)) $ret .= $Iday ." ".rusdate($Iday ,'day').$j;
    
    
    if ((empty($Imonth) && empty($Iyear))){
   	
    if(isset($Ihour)) $ret .= $Ihour ." ".rusdate($Ihour ,'hour').$j;
    if(isset($Iminute)) $ret .= $Iminute ." ".rusdate($Iminute ,'minute').$j;
    
    
    //    if($showseconds==false && $Iminute<1)$Iminute=0;
    if($showseconds==false && $Iminute<1 && $Ihour<1 && $Iday<1 && $Iweek<1 && $Imonth<1 && $Iyear<1)
	return rusdate(0 ,'minute');

    if ((!empty($Isecond) OR !empty($ret)) AND $showseconds==true){
        if(!empty($ret) AND !isset($Isecond))$Isecond=0;
        $ret .= $Isecond ." ".rusdate($Isecond ,'second').$j;
    }
    
    }
    
    return $ret;
}

function rusdate($num,$type){
    $rus = array (
        "year"    => array( "лет", "год", "года", "года", "года", "лет", "лет", "лет", "лет", "лет"),
        "month"  => array( "месяцев", "месяц", "месяца", "месяца", "месяца", "месяцев", "месяцев", "месяцев", "месяцев", "месяцев"),
        "week"  => array( "недель", "неделю", "недели", "недели", "недели", "недель", "недель", "недель", "недель", "недель"),
        "day"   => array( "дней", "день", "дня", "дня", "дня", "дней", "дней", "дней", "дней", "дней"),
        "hour"    => array( "часов", "час", "часа", "часа", "часа", "часов", "часов", "часов", "часов", "часов"),
        "minute" => array( "минут", "минуту", "минуты", "минуты", "минуты", "минут", "минут", "минут", "минут", "минут"),
        "second" => array( "секунд", "секунду", "секунды", "секунды", "секунды", "секунд", "секунд", "секунд", "секунд", "секунд"),
    );

    $num = intval($num);
    if (10 < $num && $num < 20) return $rus[$type][0];
    return $rus[$type][$num % 10];
}



function toupper($content) {
$content = strtr($content, "абвгдеёжзийклмнорпстуфхцчшщъьыэюя","АБВГДЕЁЖЗИЙКЛМНОРПСТУФХЦЧШЩЪЬЫЭЮЯ");
return strtoupper($content);
}


function tolower($content) {
$content = strtr($content, "АБВГДЕЁЖЗИЙКЛМНОРПСТУФХЦЧШЩЪЬЫЭЮЯ","абвгдеёжзийклмнорпстуфхцчшщъьыэюя");
//return strtolower($content);
return mb_strtolower($content, 'cp1251');
}

function check_images($file){
    $h1count = preg_match_all('/(<img)\s (src="([a-zA-Z0-9\.;:\/\?&=_|\r|\n]{1,})")/isxmU',$file,$patterns);
    $imagesarray = array();
    array_push($imagesarray,$patterns[3]);
    array_push($imagesarray,$patterns[0]);
    
  $images = $imagesarray[0];
  $imagecodes = $imagesarray[1];
  if ($images)
    foreach ($images as $key => $image) {
      if (!@getimagesize($image)) {$bb[] = $imagecodes[$key]; $html[] = $image; }
    }
 if ($bb)
 $code = str_replace($bb,$html,$code);
 return $file;
}


function StdDecodePeerId($id_data, $id_name){
$version_str = "";
for ($i=0; $i<=strlen($id_data); $i++){
$c = $id_data[$i];
if ($id_name=="BitTornado" || $id_name=="ABC") {
if ($c!='-' && ctype_digit($c)) $version_str .= "$c.";
elseif ($c!='-' && ctype_alpha($c)) $version_str .= (ord($c)-55).".";
else break;
}
elseif($id_name=="BitComet"||$id_name=="BitBuddy"||$id_name=="Lphant"||$id_name=="BitPump"||$id_name=="BitTorrent Plus! v2") {
if ($c != '-' && ctype_alnum($c)){
$version_str .= "$c";
if($i==0) $version_str = intval($version_str) .".";
}
else{
$version_str .= ".";
break;
}
}
else {
if ($c != '-' && ctype_alnum($c)) $version_str .= "$c.";
else break;
}
}
$version_str = substr($version_str,0,strlen($version_str)-1);
return "$id_name $version_str";
}
function MainlineDecodePeerId($id_data, $id_name){
$version_str = "";
for ($i=0; $i<=strlen($id_data); $i++){
$c = $id_data[$i];
if ($c != '-' && ctype_alnum($c)) $version_str .= "$c.";
}
$version_str = substr($version_str,0,strlen($version_str)-1);
return "$id_name $version_str";
}
function DecodeVersionString ($ver_data, $id_name){
$version_str = "";
$version_str .= intval(ord($ver_data[0]) + 0).".";
$version_str .= intval(ord($ver_data[1])/10 + 0);
$version_str .= intval(ord($ver_data[1])%10 + 0);
return "$id_name $version_str";
}

function getagent($httpagent, $peer_id="") {
// if($peer_id!="") $peer_id=hex2bin($peer_id);
if(substr($peer_id,0,3)=='-AX') return StdDecodePeerId(substr($peer_id,4,4),"BitPump"); # AnalogX BitPump
if(substr($peer_id,0,3)=='-BB') return StdDecodePeerId(substr($peer_id,3,5),"BitBuddy"); # BitBuddy
if(substr($peer_id,0,3)=='-BC') return StdDecodePeerId(substr($peer_id,4,4),"BitComet"); # BitComet
if(substr($peer_id,0,3)=='-BS') return StdDecodePeerId(substr($peer_id,3,7),"BTSlave"); # BTSlave
if(substr($peer_id,0,3)=='-BX') return StdDecodePeerId(substr($peer_id,3,7),"BittorrentX"); # BittorrentX
if(substr($peer_id,0,3)=='-CT') return "Ctorrent $peer_id[3].$peer_id[4].$peer_id[6]"; # CTorrent
if(substr($peer_id,0,3)=='-KT') return StdDecodePeerId(substr($peer_id,3,7),"KTorrent"); # KTorrent
if(substr($peer_id,0,3)=='-LT') return StdDecodePeerId(substr($peer_id,3,7),"libtorrent"); # libtorrent
if(substr($peer_id,0,3)=='-LP') return StdDecodePeerId(substr($peer_id,4,4),"Lphant"); # Lphant
if(substr($peer_id,0,3)=='-MP') return StdDecodePeerId(substr($peer_id,3,7),"MooPolice"); # MooPolice
if(substr($peer_id,0,3)=='-MT') return StdDecodePeerId(substr($peer_id,3,7),"Moonlight"); # MoonlightTorrent
if(substr($peer_id,0,3)=='-PO') return StdDecodePeerId(substr($peer_id,3,7),"PO Client"); #unidentified clients with versions
if(substr($peer_id,0,3)=='-QT') return StdDecodePeerId(substr($peer_id,3,7),"Qt 4 Torrent"); # Qt 4 Torrent
if(substr($peer_id,0,3)=='-RT') return StdDecodePeerId(substr($peer_id,3,7),"Retriever"); # Retriever
if(substr($peer_id,0,3)=='-S2') return StdDecodePeerId(substr($peer_id,3,7),"S2 Client"); #unidentified clients with versions
if(substr($peer_id,0,3)=='-SB') return StdDecodePeerId(substr($peer_id,3,7),"Swiftbit"); # Swiftbit
if(substr($peer_id,0,3)=='-SN') return StdDecodePeerId(substr($peer_id,3,7),"ShareNet"); # ShareNet
if(substr($peer_id,0,3)=='-SS') return StdDecodePeerId(substr($peer_id,3,7),"SwarmScope"); # SwarmScope
if(substr($peer_id,0,3)=='-SZ') return StdDecodePeerId(substr($peer_id,3,7),"Shareaza"); # Shareaza
if(preg_match("/^RAZA ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches)) return "Shareaza $matches[1]";
if(substr($peer_id,0,3)=='-TN') return StdDecodePeerId(substr($peer_id,3,7),"Torrent.NET"); # Torrent.NET
if(substr($peer_id,0,3)=='-TR') return StdDecodePeerId(substr($peer_id,3,7),"Transmission"); # Transmission
if(substr($peer_id,0,3)=='-TS') return StdDecodePeerId(substr($peer_id,3,7),"TorrentStorm"); # Torrentstorm
if(substr($peer_id,0,3)=='-UR') return StdDecodePeerId(substr($peer_id,3,7),"UR Client"); # unidentified clients with versions
if(substr($peer_id,0,3)=='-UT') return StdDecodePeerId(substr($peer_id,3,7),"uTorrent"); # uTorrent
if(substr($peer_id,0,3)=='-XT') return StdDecodePeerId(substr($peer_id,3,7),"XanTorrent"); # XanTorrent
if(substr($peer_id,0,3)=='-ZT') return StdDecodePeerId(substr($peer_id,3,7),"ZipTorrent"); # ZipTorrent
if(substr($peer_id,0,3)=='-bk') return StdDecodePeerId(substr($peer_id,3,7),"BitKitten"); # BitKitten
if(substr($peer_id,0,3)=='-lt') return StdDecodePeerId(substr($peer_id,3,7),"libTorrent"); # libTorrent
if(substr($peer_id,0,3)=='-pX') return StdDecodePeerId(substr($peer_id,3,7),"pHoeniX"); # pHoeniX
if(substr($peer_id,0,2)=='BG') return StdDecodePeerId(substr($peer_id,2,4),"BTGetit"); # BTGetit
if(substr($peer_id,2,2)=='BM') return DecodeVersionString(substr($peer_id,0,2),"BitMagnet"); # BitMagnet
if(substr($peer_id,0,2)=='OP') return StdDecodePeerId(substr($peer_id,2,4),"Opera"); # Opera
if(substr($peer_id,0,4)=='270-') return "GreedBT 2.7.0"; # GreedBT
if(substr($peer_id,0,4)=='271-') return "GreedBT 2.7.1"; # GreedBT 2.7.1
if(substr($peer_id,0,4)=='346-') return "TorrentTopia"; # TorrentTopia
if(substr($peer_id,0,3)=='-AR') return "Arctic Torrent"; # Arctic (no way to know the version)
if(substr($peer_id,0,3)=='-G3') return "G3 Torrent"; # G3 Torrent
if(substr($peer_id,0,6)=='BTDWV-') return "Deadman Walking"; # Deadman Walking
if(substr($peer_id,5,7)=='Azureus') return "Azureus 2.0.3.2"; # Azureus 2.0.3.2
if(substr($peer_id,0,8 )=='PRC.P---') return "BitTorrent Plus! II"; # BitTorrent Plus! II
if(substr($peer_id,0,8 )=='S587Plus') return "BitTorrent Plus!"; # BitTorrent Plus!
if(substr($peer_id,0,7)=='martini') return "Martini Man"; # Martini Man
if(substr($peer_id,4,6)=='btfans') return "SimpleBT"; # SimpleBT
if(substr($peer_id,3,9)=='SimpleBT?') return "SimpleBT"; # SimpleBT
if(preg_match("/MFC_Tear_Sample/", $httpagent)) return "SimpleBT";
if(substr($peer_id,0,5)=='btuga') return "BTugaXP"; # BTugaXP
if(substr($peer_id,0,5)=='BTuga') return "BTuga"; # BTugaXP
if(substr($peer_id,0,5)=='oernu') return "BTugaXP"; # BTugaXP
if(substr($peer_id,0,10)=='DansClient') return "XanTorrent"; # XanTorrent
if(substr($peer_id,0,16)=='Deadman Walking-') return "Deadman"; # Deadman client
if(substr($peer_id,0,8 )=='XTORR302') return "TorrenTres 0.0.2"; # TorrenTres
if(substr($peer_id,0,7)=='turbobt') return "TurboBT ".(substr($peer_id,7,5)); # TurboBT
if(substr($peer_id,0,7)=='a00---0') return "Swarmy"; # Swarmy
if(substr($peer_id,0,7)=='a02---0') return "Swarmy"; # Swarmy
if(substr($peer_id,0,7)=='T00---0') return "Teeweety"; # Teeweety
if(substr($peer_id,0,7)=='rubytor') return "Ruby Torrent v".ord($peer_id[7]); # Ruby Torrent
if(substr($peer_id,0,5)=='Mbrst') return MainlineDecodePeerId(substr($peer_id,5,5),"burst!"); # burst!
if(substr($peer_id,0,4)=='btpd') return "BT Protocol Daemon ".(substr($peer_id,5,3)); # BT Protocol Daemon
if(substr($peer_id,0,8 )=='XBT022--') return "BitTorrent Lite"; # BitTorrent Lite based on XBT code
if(substr($peer_id,0,3)=='XBT') return StdDecodePeerId(substr($peer_id,3,3), "XBT"); # XBT Client
if(substr($peer_id,0,4)=='-BOW') return StdDecodePeerId(substr($peer_id,4,5),"Bits on Wheels"); # Bits on Wheels
if(substr($peer_id,1,2)=='ML') return MainlineDecodePeerId(substr($peer_id,3,5),"MLDonkey"); # MLDonkey
if(substr($peer_id,0,8 )=='AZ2500BT') return "AzureusBitTyrant 1.0/1";
if($peer_id[0]=='A') return StdDecodePeerId(substr($peer_id,1,9),"ABC"); # ABC
if($peer_id[0]=='R') return StdDecodePeerId(substr($peer_id,1,5),"Tribler"); # Tribler
if($peer_id[0]=='M'){
if(preg_match("/^Python/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
return MainlineDecodePeerId(substr($peer_id,1,7),"Mainline"); # Mainline BitTorrent with version
}
if($peer_id[0]=='O') return StdDecodePeerId(substr($peer_id,1,9),"Osprey Permaseed"); # Osprey Permaseed
if($peer_id[0]=='S'){
if(preg_match("/^BitTorrent\/3.4.2/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
return StdDecodePeerId(substr($peer_id,1,9),"Shad0w"); # Shadow's client
}
if($peer_id[0]=='T'){
if(preg_match("/^Python/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
return StdDecodePeerId(substr($peer_id,1,9),"BitTornado"); # BitTornado
}
if($peer_id[0]=='U') return StdDecodePeerId(substr($peer_id,1,9),"UPnP"); # UPnP NAT Bit Torrent
# Azureus / Localhost
if(substr($peer_id,0,3)=='-AZ') {
if(preg_match("/^Localhost ([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches)) return "Localhost $matches[1]";
if(preg_match("/^BitTorrent\/3.4.2/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
if(preg_match("/^Python/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
return StdDecodePeerId(substr($peer_id,3,7),"Azureus");
}
if(preg_match("/Azureus/", $peer_id)) return "Azureus 2.0.3.2";
# BitComet/BitLord/BitVampire/Modded FUTB BitComet
if(substr($peer_id,0,4)=='exbc' || substr($peer_id,1,3)=='UTB'){
if(substr($peer_id,0,4)=='FUTB') return DecodeVersionString(substr($peer_id,4,2),"BitComet Mod1");
elseif(substr($peer_id,0,4)=='xUTB') return DecodeVersionString(substr($peer_id,4,2),"BitComet Mod2");
elseif(substr($peer_id,6,4)=='LORD') return DecodeVersionString(substr($peer_id,4,2),"BitLord");
elseif(substr($peer_id,6,3)=='---' && DecodeVersionString(substr($peer_id,4,2),"BitComet")=='BitComet 0.54') return "BitVampire";
else return DecodeVersionString(substr($peer_id,4,2),"BitComet");
}
# Rufus
if(substr($peer_id,2,2)=='RS'){
for ($i=0; $i<=strlen(substr($peer_id,4,9)); $i++){
$c = $peer_id[$i+4];
if (ctype_alnum($c) || $c == chr(0)) $rufus_chk = true;
else break;
}
if ($rufus_chk) return DecodeVersionString(substr($peer_id,0,2),"Rufus"); # Rufus
}
# BitSpirit
if(substr($peer_id,14,6)=='HTTPBT' || substr($peer_id,16,4)=='UDP0') {
if(substr($peer_id,2,2)=='BS') {
if($peer_id[1]==chr(0)) return "BitSpirit v1";
if($peer_id[1]== chr(2)) return "BitSpirit v2";
}
return "BitSpirit";
}
#BitSpirit
if(substr($peer_id,2,2)=='BS') {
if($peer_id[1]==chr(0)) return "BitSpirit v1";
if($peer_id[1]==chr(2)) return "BitSpirit v2";
return "BitSpirit";
}
# eXeem beta
if(substr($peer_id,0,3)=='-eX') {
$version_str = "";
$version_str .= intval($peer_id[3],16).".";
$version_str .= intval($peer_id[4],16);
return "eXeem $version_str";
}
if(substr($peer_id,0,2)=='eX') return "eXeem"; # eXeem beta .21
if(substr($peer_id,0,12)==(chr(0)*12) && $peer_id[12]==chr(97) && $peer_id[13]==chr(97)) return "Experimental 3.2.1b2"; # Experimental 3.2.1b2
if(substr($peer_id,0,12)==(chr(0)*12) && $peer_id[12]==chr(0) && $peer_id[13]==chr(0)) return "Experimental 3.1"; # Experimental 3.1
//if(substr($peer_id,0,12)==(chr(0)*12)) return "Mainline (obsolete)"; # Mainline BitTorrent (obsolete)
//return "$httpagent [$peer_id]";
return "Неизвестный клиент";
}


/// функция закрытия незакрытых тегов для html
function closetags($text,$subst=false){

if ($subst && !empty($subst))	
$text=substr($text,0, $subst);

//обрезка последнего слова
$text = substr($text, 0, strrpos($text," "));
$text = preg_replace("/<[^>]*$/i", "", $text);
//закрытие тегов
//preg_match_all("/<[^a-z>/]*([a-z]{1,50})/i",$text,$otags);
preg_match_all("/<[^a-zA-Z>\/]*([a-zA-Z]{1,50})/i",$text,$otags);
if(count($otags[0])>0){
$fotags=array(); $fctags=array();
///preg_match_all("/<[ t]*/[^a-z]*([a-z]{1,50})/i",$text,$ctags);
preg_match_all("/<[ t]*\/[^a-z]*([a-z]{1,50})/i",$text,$ctags);
foreach($otags[1] as $otag){
$otag = strtolower($otag);
if(isset($fotags[$otag]))$fotags[$otag]++;
else $fotags[$otag] = 1;
}
foreach($ctags[1] as $ctag){
$ctag = strtolower($ctag);
if(isset($fctags[$ctag]))$fctags[$ctag]++;
else $fctags[$ctag] = 1;
}
while(list($tag, $cnt) = each($fotags)){
$fctags[$tag] = isset($fctags[$tag])?$fctags[$tag]:0;
$text.=str_repeat("",abs($fctags[$tag] - $cnt));
}
}

///$ost ="yandex.</b>tu tut <b>asdas. asfdfsdfsdfsdf";
///echo closetags($ost);

return $text;
}


/// проверка логина на правильность
function validusername($username) {
	
     if (empty($username))
	  return false;

        $allowedchars = "@abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_ "."абвгдеёжзиклмнопрстуфхшщэюяьъчйцыАБВГДЕЁЖЗИКЛМНОПРСТУФХШЩЭЮЯЬЪЧЙЦЫ";
        for ($i = 0; $i < strlen($username); ++$i)
          if (strpos($allowedchars, $username[$i]) === false)
            return false;
        return true;
}

function strip_bbcode($message, $stripquotes = false, $fast_and_dirty = false, $showlinks = true)
{
    $find = array();
    $replace = array();

    if ($stripquotes)
    {
        // [quote=username] and [quote]
        $message = strip_quotes($message);
    }

    // a really quick and rather nasty way of removing vbcode
    if ($fast_and_dirty)
    {
        // any old thing in square brackets
        $find[] = '#\[.*/?\]#siU';
        $replace[] = '';

        $message = preg_replace($find, $replace, $message);
    }
    // the preferable way to remove vbcode
    else
    {
        // simple links
        $find[] = '#\[(email|url)=("??)(.+)\\2\]\\3\[/\\1\]#siU';
        $replace[] = '\3';

        // named links
        $find[] = '#\[(email|url)=("??)(.+)\\2\](.+)\[/\\1\]#siU';
        $replace[] = ($showlinks ? '\4 (\3)' : '\4');

        // replace links (and quotes if specified) from message
        $message = preg_replace($find, $replace, $message);

        // strip out all other instances of [x]...[/x]
        while(preg_match_all('#\[(\w+?)(?>[^\]]*?)\](.*)(\[/\1\])#siU', $message, $regs))
        {
            foreach($regs[0] AS $key => $val)
            {
                $message  = str_replace($val, $regs[2]["$key"], $message);
            }
        }
        $message = str_replace('[*]', ' ', $message);
    }

    return trim($message);
}

function imageshack($link) {

if (stristr($link, 'imageshack.us'))
return $link;

$cookies = array();

$cookies[] = "isUSER=maksim7777Max7;";
$cookies[] = "myimages=c6cda60ef7f64746dcef3fcdb4275ab2;";
$cookies[] = "lang=en;";
$cookies[] = "is_uuid=b5e22174f6e74a7f9f2e3964ec352c51;";
$cookies[] = "myid=4507786;";
$cookies[] = "isvisitor=c6cda60ef7f64746dcef3fcdb4275ab2;";
$cookies[] = "gallery_token=af5945ac2ace19fa8cb509e1ff47f2f2;";

if (@file_exists("cache/imageshack.txt")){
$content=@file_get_contents("cache/imageshack.txt"); 
$cds = explode("\n",$content);
foreach ($cds as $co) {
if (!empty($co))
$cookies[] = $co;
}
}

$cookies = array_unique($cookies);

$socket = @fsockopen("post.imageshack.us", 80, $errno, $errstr, 15);

if (!$socket)
return false;

$file = $link;

$mime = tolower(end(explode('.', $file)));
///mime_content_type не стабильно работает, пишем альтернативу
if ($mime == "jpg" || $mime == "jpeg")
$mimeview = "image/jpeg";
elseif ($mime == "gif")
$mimeview = "image/gif";
elseif ($mime == "png")
$mimeview = "image/png";
else
return false;

$file_get = file_get_contents($file);

if (empty($file_get))
return false;

//разделитель
$boundary = md5(uniqid(time()));

if ($cookies) {
$headcookies = "";
foreach ($cookies as $cookie)
$headercookie .= $cookie." ";
$headercookie = substr($headercookie, 0, -2);
$headcookies.= "Cookie: ".$headercookie."\r\n";
}
$headcookies.= "\r\n";


$headers1 = "--".$boundary."\r\n";
$headers1.= "Content-Disposition: form-data; name=\"tags\"\r\n\r\n";
$headers1.= urlencode("muz-tracker")."\r\n";

$headers2.= "--".$boundary."\r\n";
$headers2.= "Content-Disposition: form-data; name=\"fileupload\";";
$headers2.= "filename=\"".end(explode('/', $file))."\"\r\n";
$headers2.= "Content-Type: ".$mimeview."\r\n";
$headers2.= "Content-Transfer-Encoding: binary\r\n\r\n";

$headers2.= $file_get;
$headers2.= "\r\n";

$headers = "POST / HTTP/1.1\r\n";
$headers.= "Host: post.imageshack.us\r\n";
$headers.= "User-agent:Opera 10.00\r\n";

$headers.= "Connection: close\r\n";

$headers.= "Content-Type: multipart/form-data; boundary=".$boundary."\r\n";
$headers.= "Content-length: ".(strlen($headers1)+strlen($headers2)+strlen($headcookies))."\r\n";
$headers.= "Accept:*/*\r\n\r\n";

$headers.= $headcookies;

$headers.= $headers2;
$headers.= $headers1;

$headers.= "--".$boundary."--\r\n\r\n";

fwrite($socket, $headers);

$answer = '';
while(!feof($socket)){
$answer= fgets($socket, 4096);

// В заголовке меня интерисуют только куки
@list($field, $value) = preg_split('/\s*:\s*/', $answer, 2);
// Запоминаем найденную куку
if (strtolower($field) === 'set-cookie'){
// Точнее, запоминаем только само значение куки (недекодированное)
$result[] = array_shift(preg_split('/\s*;\s*/', $value, 2));
}

if (strtolower($field) === 'location'){
// Точнее, запоминаем только само значение куки (недекодированное)
$location = array_shift(preg_split('/\s*;\s*/', $value, 2));
}

//echo $answer;
}
fclose($socket);

sleep(1);

if (!empty($location)){
/// находим локашион ссылку перенаправления

$site = parse_url($location);

$host = $site["host"];
$path = $site["path"]."?".$site["query"];

$path = str_replace("__","",$path);

$socket_i = @fsockopen($host, 80, $errno, $errstr, 10);

if (!$socket_i)
return false;

$headers = "POST $path HTTP/1.1\r\n";
$headers.= "Host: $host\r\n";
$headers.= "User-agent:Opera 10.00\r\n";

$headers.= "Connection: close\r\n";

$headers.= "Content-length: ".strlen($headcookies)."\r\n";
$headers.= "Accept:*/*\r\n\r\n";

$headers.= $headcookies;

$headers.= "\r\n\r\n";

fwrite($socket_i, $headers);

$answer = '';
while(!feof($socket_i)){
$answer.= fgets($socket_i, 4096);
}

preg_match("/\[img\](http:\/\/[^\s'\"<>]+(\.(jpg|jpeg|gif|png)))\[\/img\]/is", $answer, $scr);

$scrimage = $scr[1];

fclose($socket_i);
} else
return false;

if (!empty($scrimage) && preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $scrimage)) {
return $scrimage;
}
else
return false;

$cookies = array_unique($cookies);

if (count($result) > 0){
$sf = ROOT_PATH."cache/imageshack.txt"; 
@unlink($sf);
$fpsf=@fopen($sf,"a+"); 
@fputs($fpsf,implode(";\n",$result)); 
@fclose($fpsf);
}

}


function parse_arrray_cat($array, $size = false) {

$arr = array();

foreach ($array as $k){
$arr[] = tolower(end(explode('.', $k)));
//echo end(explode('.', $k))." ";
}

$dybarra = array_unique($arr);

/// при одном массиве и одном значении
if (count($dybarra) == 1){

$end = $dybarra[0];

////// если всего МНОГО значений а формат только одного значения //////
if (count($arr) > count($dybarra)){

if ($end == "jpg" || $end == "png" || $end == "bmp" || $end == "gif")
return "25";

elseif ($end == "mp3" || $end == "dts")
return "10";

elseif ($end == "mkv" && $size >= "4686903296")
return "14";///Фильмы / HDTV / HD / Blu-Ray

elseif ($end == "apk")
return "27";/// aka Android OS


}

if ($end == "mpls" || $end == "clpi")
	return "14";
elseif ($end == "djvu" || $end == "pdf")
	return "16";
elseif ($end == "ts")
	return "14";///Фильмы / HDTV / HD / Blu-Ray
elseif ($end == "cso") /// cso - PS
	return "14";///PSP / PS2 / PS3 / Xbox 

/// при одном массиве и более двух значении
} elseif(count($dybarra) >= 2){

if ((array_search("cue",$dybarra) && array_search("wav",$dybarra)) || (array_search("dts",$dybarra) && array_search("md5",$dybarra)))
return "10";///Музыка / Мультимедиа

elseif (array_search("mp3",$dybarra) && (array_search("m3u",$dybarra) || array_search("plc",$dybarra) || array_search("pls",$dybarra) || array_search("m3u8",$dybarra)))
return "10";///Музыка / Мультимедиа

elseif (in_array("log",$dybarra) && in_array("cue",$dybarra) && (in_array("flac",$dybarra) || in_array("ape",$dybarra)))
return "10";///Музыка / Мультимедиа

elseif (in_array("ifo",$dybarra) && in_array("vob",$dybarra) && in_array("bup",$dybarra))
return "15";///DVD / Фильмы

elseif (in_array("clpi",$dybarra) && in_array("bdmv",$dybarra) && in_array("m2ts",$dybarra))
return "14";///Фильмы / HDTV / HD / Blu-Ray

elseif (in_array("nrg",$dybarra) || in_array("iso",$dybarra) || (in_array("mdf",$dybarra) && in_array("mds",$dybarra)))
return "1";///Образы CD/DVD/HD

}

/// при пустом массиве
return false;
}


/**
* Если желаете использовать движок то оставьте пожалуйста копирайта на своих местах, иначе стабильной работы не гарантируется. Спасибо за внимание к движку Tesla.
*/

define ("VERSION", "<b>.:$SITENAME <a href=\"http://tesla-tracker.net/\" class=\"copyright\" alt=\"Сайт поднят на движке под названием Tesla Tracker (TT) © ".date("Y")." года. Владельцами являются 7Max7.\" title=\"Сайт поднят на движке под названием Tesla Tracker (TT) © 2010 года. Владельцами являются 7Max7.\"/>©</a> 2010 года v.Gold:.<b>");
define ("TBVERSION", "");

?>