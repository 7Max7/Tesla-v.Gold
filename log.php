<? 
require "include/bittorrent.php"; 
dbconn(false); 
loggedinorreturn(); 


if(isset($_POST["delete"]) && $_POST["delete"] == "delete" && get_user_class() == UC_SYSOP) {

//if (empty($_POST["desact"]) && empty($_POST["remove"]))
//stderr("Ошибочка", "Не выбрали пользователя");

if (!empty($_POST["remove"])){
sql_query("DELETE FROM sitelog WHERE id IN (" . implode(", ", array_map("sqlesc", $_POST["remove"])) . ")") or sqlerr(__FILE__, __LINE__);
}

$returnto=getenv("HTTP_REFERER");
if (!empty($returnto)){
	header("Location: $returnto");
}
else
header("Location: log.php");
}


stdhead("Логи"); 
  
if (get_user_class() < UC_MODERATOR) {
     stdmsg("Ошибка","Доступ в этот раздел закрыт!", error); 
     stdfoot(); 
     die(); 
     }


// 
//мод очистки логов 
// 
if (get_user_class() > UC_ADMINISTRATOR) 
{
$d_tracker = " [<a style='color: red' onmouseover=\"this.style.color='blue'\" onmouseout=\"this.style.color='red'\" onClick=\"return confirm('Вы уверены, что хотите очистить лог трекера?')\" title='Очистить лог!' href='log.php?type_clear=tracker'>D</a>]"; 
$d_bans = " [<a style='color: red' onmouseover=\"this.style.color='blue'\" onmouseout=\"this.style.color='red'\" onClick=\"return confirm('Вы уверены, что хотите очистить лог банов?')\" title='Очистить лог!' href='log.php?type_clear=bans'>D</a>]";  
$d_comment = " [<a style='color: red' onmouseover=\"this.style.color='blue'\" onmouseout=\"this.style.color='red'\" onClick=\"return confirm('Вы уверены, что хотите очистить лог комментов?')\" title='Очистить лог!' href='log.php?type_clear=comment'>D</a>]";  
$d_torrent = " [<a style='color: red' onmouseover=\"this.style.color='blue'\" onmouseout=\"this.style.color='red'\" onClick=\"return confirm('Вы уверены, что хотите очистить лог торрентов?')\" title='Очистить лог!' href='log.php?type_clear=torrent'>D</a>]";  
$d_error = " [<a style='color: red' onmouseover=\"this.style.color='blue'\" onmouseout=\"this.style.color='red'\" onClick=\"return confirm('Вы уверены, что хотите очистить лог ошибок?')\" title='Очистить лог!' href='log.php?type_clear=error'>D</a>]"; 
$d_other = " [<a style='color: red' onmouseover=\"this.style.color='blue'\" onmouseout=\"this.style.color='red'\" onClick=\"return confirm('Вы уверены, что хотите очистить лог разное?')\" title='Очистить лог!' href='log.php?type_clear=other'>D</a>]"; 

if ($on_search_log==1){
$d_search = " [<a style='color: red' onmouseover=\"this.style.color='blue'\" onmouseout=\"this.style.color='red'\" onClick=\"return confirm('Вы уверены, что хотите очистить лог поиска?')\" title='Очистить лог!' href='log.php?type_clear=search'>D</a>]"; }

$d_tfiles = " [<a style='color: red' onmouseover=\"this.style.color='blue'\" onmouseout=\"this.style.color='red'\" onClick=\"return confirm('Вы уверены, что хотите очистить лог поиска?')\" title='Очистить лог!' href='log.php?type_clear=tfiles'>D</a>]";  

$dell_all = "<b>&nbsp;::&nbsp;</b><span style=\"background-color: #FF0000; border: 1px solid #CC0000; padding:2px;\"><a style='color: white' onmouseover=\"this.style.color='#FFFF00'\" onmouseout=\"this.style.color='white'\" onClick=\"return confirm('Вы уверены, что хотите очистить весь лог?')\" title='Очистить лог!' href='log.php?type_clear=dell_all'>ОЧИСТИТЬ ВСЕ</a></span>"; 

}
else 
{ 
$d_tracker = ""; 
$d_bans = ""; 
$d_comment = ""; 
$d_torrent = ""; 
$d_error = ""; 
$d_other = ""; 
$d_search = ""; 
$d_tfiles= ""; 
$dell_all = ""; 
}

//получаем команду и проверяем класс 
//если не Администратор, ошибка! 
  $type_clear = (isset($_GET["type_clear"])? htmlspecialchars($_GET["type_clear"]):""); 
if ($type_clear !=="" && (get_user_class() >= UC_ADMINISTRATOR)) 
{
  if($type_clear == "tracker") 
  {print ("<h1 style='color: white'><span style=\"background-color: #FF0000; border: 1px solid #CC0000; padding:2px;\">Лог трекера очищен</span></h1>");$_GET["type"] = "tracker";$logtype = "<b>трекера</b>";} 
  elseif($type_clear == "bans") 
  {print ("<h1 style='color: white'><span style=\"background-color: #FF0000; border: 1px solid #CC0000; padding:2px;\">Лог банов очищен</span></h1>");$_GET["type"] = "bans";$logtype = "<b>банов</b>";} 
  elseif($type_clear == "comment") 
    {print ("<h1 style='color: white'><span style=\"background-color: #FF0000; border: 1px solid #CC0000; padding:2px;\">Лог комментов очищен</span></h1>");$_GET["type"] = "comment";$logtype = "<b>комментов</b>";}
  elseif($type_clear == "torrent") 
  {print ("<h1 style='color: white'><span style=\"background-color: #FF0000; border: 1px solid #CC0000; padding:2px;\">Лог торрентов очищен</span></h1>");$_GET["type"] = "torrent";$logtype = "<b>торрентов</b>";} 
  elseif($type_clear == "error") 
  {print ("<h1 style='color: white'><span style=\"background-color: #FF0000; border: 1px solid #CC0000; padding:2px;\">Лог ошибок очищен</span></h1>");$_GET["type"] = "error";$logtype = "<b>ошибок</b>";}   
    elseif($type_clear == "other") 
  {print ("<h1 style='color: white'><span style=\"background-color: #FF0000; border: 1px solid #CC0000; padding:2px;\">Лог разное очищен</span></h1>");$_GET["type"] = "other";$logtype = "<b>разное</b>";}   
  
    elseif($type_clear == "tfiles")
  {print ("<h1 style='color: white'><span style=\"background-color: #FF0000; border: 1px solid #CC0000; padding:2px;\">Лог обменника почищен</span></h1>");$_GET["type"] = "tfiles";$logtype = "<b>обменника</b>";}  
  
  elseif($type_clear == "search" && $on_search_log==1)
  {print ("<h1 style='color: white'><span style=\"background-color: #FF0000; border: 1px solid #CC0000; padding:2px;\">Лог поиска очищен</span></h1>");$_GET["type"] = "search";$logtype = "<b>поиска</b>";}   

//чистим лог: 
sql_query("DELETE FROM sitelog WHERE type = ".sqlesc($type_clear)."") or sqlerr(__FILE__, __LINE__); 

//очистка всего лога 
  if($type_clear == "dell_all" && get_user_class() == UC_SYSOP) 
  {
  print ("<h1 style='color: white'><span style=\"background-color: #FF0000; border: 1px solid #CC0000; padding:2px;\">Весь лог был очищен</span></h1>");$_GET["type"] = "tracker";$logtype = "<b>полностью</b>"; 
  sql_query("DELETE FROM sitelog WHERE type IS NOT NULL") or sqlerr(__FILE__, __LINE__); 
  } 
// 

//и пишем в него данные об очистке: 
$logdelluser = "<b>".$CURUSER['username']."</b>"; 
write_log("Лог $logtype был очищен пользователем  $logdelluser","d511ff","$type_clear"); 
} 
elseif ($type_clear !=="" && (get_user_class() < UC_SYSOP))  
{
     stdmsg("Ошибка","Вы не имеете достаточных прав!", error); 
     stdfoot(); 
     die(); 
}
// 


  $type = (isset($_GET["type"])? htmlspecialchars($_GET["type"]):""); 
   if(!$type || $type == 'simp') $type = "tracker"; 
    
     print("<center>"  . 
        ($type == "tracker" || !$type ? "<span style=\"background-color: #FFFF00; border: 1px solid #808080; padding:2px;\"><b>Трекер</b>$d_tracker</span>" : "<span style=\"background-color: #E2E2E2; border: 1px solid #808080; padding:2px;\"><a href=log.php?type=tracker>Трекер</a>$d_tracker</span>") . "&nbsp;" . 
        
        
           ($type == "torrent" ? "<span style=\"background-color: #FFFF00; border: 1px solid #808080; padding:2px;\"><b>Торренты</b>$d_torrent</span>" : "<span style=\"background-color: #E2E2E2; border: 1px solid #808080; padding:2px;\"><a href=log.php?type=torrent>Торренты</a>$d_torrent</span>") . "&nbsp;" . 
        
($type == "tfiles" ? "<span style=\"background-color: #FFFF00; border: 1px solid #808080; padding:2px;\"><b>Обмен</b>$d_tfiles</span>" : "<span style=\"background-color: #E2E2E2; border: 1px solid #808080; padding:2px;\"><a href=log.php?type=tfiles>Обмен</a>$d_tfiles</span>") .
        
        // "$dell_all</center>\n"
""); 
        

if (get_user_class() >= UC_ADMINISTRATOR)
{
	   print(""  . 
($type == "bans" ? "<span style=\"background-color: #FFFF00; border: 1px solid #808080; padding:2px;\"><b>Баны</b>$d_bans</span>" : "<span style=\"background-color: #E2E2E2; border: 1px solid #808080; padding:2px;\"><a href=log.php?type=bans>Баны</a>$d_bans</span>") . "&nbsp;"); 

  print(""  .
($type == "comment" ? "<span style=\"background-color: #FFFF00; border: 1px solid #808080; padding:2px;\"><b>Комменты</b>$d_comment</span>" : "<span style=\"background-color: #E2E2E2; border: 1px solid #808080; padding:2px;\"><a href=log.php?type=comment>Комменты</a>$d_comment</span>") . "&nbsp;" );

  if ($on_search_log==1){
  print("".($type == "search" ? "<span style=\"background-color: #FFFF00; border: 1px solid #808080; padding:2px;\"><b>Поисковик</b>$d_search</span>" : "<span style=\"background-color: #E2E2E2; border: 1px solid #808080; padding:2px;\"><a href=log.php?type=search>Поисковик</a>$d_search</span>") . "&nbsp;");
}
}

if (get_user_class() == UC_SYSOP)
{
print("". 
($type == "error" ? "<span style=\"background-color: #FFFF00; border: 1px solid #808080; padding:2px;\"><b>Ошибки</b>$d_error</span>" : "<span style=\"background-color: #E2E2E2; border: 1px solid #808080; padding:2px;\"><a href=log.php?type=error>Ошибки</a>$d_error</span>") . "&nbsp;" .
      
($type == "other" ? "<span style=\"background-color: #FFFF00; border: 1px solid #808080; padding:2px;\"><b>Разное</b>$d_other</span>" : "<span style=\"background-color: #E2E2E2; border: 1px solid #808080; padding:2px;\"><a href=log.php?type=other>Разное</a>$d_error</span>") . "&nbsp;"  .

($type == "founds" ? "<span style=\"background-color: #FFFF00; border: 1px solid #808080; padding:2px;\"><b><a href=log.php?type=founds>Поиск выражений</a></b></span>" : "<span style=\"background-color: #E2E2E2; border: 1px solid #808080; padding:2px;\"><a href=log.php?type=founds>Поиск выражений</a></span>")  . "&nbsp;" 

);
}


if ($type == "tracker")$printtype = "трекера"; 
if ($type == "bans")$printtype = "банов"; 
if ($type == "tfiles")$printtype = "обмена"; 
if ($type == "comment")$printtype = "комментов"; 
if ($type == "torrent")$printtype = "торрентов"; //search
if ($type == "error")$printtype = "ошибок"; 
if ($type == "other")$printtype = "разное"; 
if ($type == "search" && $on_search_log==1)$printtype = "поиска"; 


if ($type == 'bans' && $CURUSER['class'] < 5) {
	stdmsg("Извините","Данная функция только для админа и выше.");
	stdfoot();
	die();
}
if ($type == 'comment' && $CURUSER['class'] < 5) {
	stdmsg("Извините","Данная функция только для админа и выше.");
	stdfoot();
	die();
}

if ($type == 'error' && $CURUSER['class'] < 6) {
	stdmsg("Извините","Данная функция только для Босса.");
	stdfoot();
	die();
}
if ($type == 'founds' && $CURUSER['class'] < 6) {
	stdmsg("Извините","Данная функция только для Босса.");
	stdfoot();
	die();
}
if ($type == 'other' && $CURUSER['class'] < 6) {
	stdmsg("Извините","Данная функция только для Босса.");
	stdfoot();
	die();
}
if ($type == 'search' && $CURUSER['class'] < 5) {
	
	if ($on_search_log==0){
	stdmsg("Извините","Данная функция отключенна.");
	}else	
	stdmsg("Извините","Данная функция только для админа и выше.");
	stdfoot();
	die();
}
if ($type <> 'search' && $type <> 'tracker' && $type <> 'error' && $type <> 'comment' && $type <> 'torrent'  && $type  <> 'bans'&& $type  <> 'tfiles' &&  $type  <> 'other'&& $type  <> 'founds') {
	stdmsg("Извините","Нет такого типа в логе.");
	stdfoot();
	die();
}

if ($type == "tracker"){
$secs = 62* 86400; /// 2 месяца
/// начало мода автоочистки по типу tracker

$r = sql_query("SELECT * FROM sitelog WHERE " . gmtime() . " - UNIX_TIMESTAMP(added) > $secs and type ='tracker' ") or sqlerr(__FILE__, __LINE__);
	while ($log = mysql_fetch_assoc($r)) 
if ($log)
{
/// делаем старую копию лога в файл (дозапись)
$text=$log["txt"];
$added=normaltime($log["added"],true);
$sf =ROOT_PATH."cache/log_old.txt"; 
$fpsf=fopen($sf,"a+"); 
fputs($fpsf,"$added - $text\n"); 
fclose($fpsf);

sql_query("DELETE FROM sitelog WHERE " . gmtime() . " - UNIX_TIMESTAMP(added) > $secs and type ='tracker' ") or sqlerr(__FILE__, __LINE__); 
/// конец мода автоочистки по типу tracker
}


}
if (isset($_GET["words"]) && $type=='founds')
{
if(strlen($_GET["words"]) > 25){
stderr("Ошибка", "Слишком длинный текст для поиска Max 25");  
die; }

if(strlen($_GET["words"][0]==" ")){
stderr("Ошибка", "Нельзя искать пробел");  
die; }
$words=htmlspecialchars($_GET["words"]);
$words = str_replace('script', '', $words);
$words = str_replace('js', '', $words);
$words = str_replace('src=', '', $words);
$words = str_replace('"', '', $words);

$where="txt LIKE '%$words%'";
}
else
{
$where="type = ".sqlesc($type)."";	
}

  $limit = ($type == 'words' ? "LIMIT 300" : "LIMIT 100"); 
  

  $res = sql_query("SELECT * FROM `sitelog` WHERE $where ORDER BY `added` DESC $limit") or sqlerr(__FILE__, __LINE__); 
  
  if ($type <> 'founds') {
  print("<h1>Логи $printtype</h1>\n"); 
  }
  else {
  print("<br><br>".(isset($words) ? "<h3><b>Поиск по слову</b>: $words </h3>":"").""); 
  }
  

  if (@mysql_num_rows($res) == 0)  {
  		if (isset($words) && $type=='founds') {
    print("<h3>Ничего не найденно</h3><br>\n"); 
    }
    
  	if (!isset($words) && $type<>'founds') {
    print("<h3>Лог файл пустой</h3><br>\n"); 
    }
    
    if ($type == 'founds') {
    print"
    <table align=\"center\" valign=\"top\" cellpadding=\"0\"> 
<tr> 
<td class=\"b\"> 
	<form method=\"get\" action=\"log.php\">
	<input type=\"hidden\" name=\"type\" value=\"founds\">
	<input type=\"text\" name=\"words\" size=\"25\" />
	<input type=\"submit\" class=\"btn\" value=\"Искать\">
	</form>
	
</td> 
</tr> 
</table>";


    }
	}
    
  else 
  {
    print("<table border=1 cellspacing=0 width=100% cellpadding=5>\n"); 
    
    if (get_user_class() == UC_SYSOP) {
    print(" 
    <script language=\"JavaScript\" type=\"text/javascript\">
<!-- Begin
var checkflag = \"false\";
function check(field) {
if (checkflag == \"false\") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = \"true\";
return \"Снять со всех отключение\"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = \"false\";
return \"Выделить всех для отключения\"; }
}

function check2(field) {
if (checkflag == \"false\") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = \"true\";
return \"Снять выделение со всех подозреваемых\"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = \"false\";
return \"Выделить всех для снятия подозрения\"; }
}
// End -->
</script>
    
	<form action=\"log.php\" method=post>\n"); 
 }
    print("<tr><td class=colhead align=left width=\"24%\">Время события</td><td class=colhead align=left>Событие</td>
	".(get_user_class() == UC_SYSOP ? "<td class=colhead width=\"2%\" align=center>
	 <input title=\"Выделить и удалить\" type=\"checkbox\" value=\"Выделить все события\" onclick=\"this.value=check2(this.form.elements['remove[]'])\"/> 
</td>":"")."
</tr>\n"); 
while ($arr = mysql_fetch_assoc($res)) 
{

$arra=array('script','js','src=','<?','?>','alert','layer','msgbox','onload','perl');
$arr["txt"] = str_replace($arra, '', $arr["txt"]);


     // $date = substr($arr['added'], 0, strpos($arr['added'], " ")); 
     // $time = substr($arr['added'], strpos($arr['added'], " ") + 1); 
     $date=normaltime($arr['added'],true);
      print("<tr style=\"color: $arr[color]\"><td>$date</td><td align=left>$arr[txt]</td>
	  ".(get_user_class() == UC_SYSOP ? "<td align=center><input type=\"checkbox\" name=\"remove[]\" value=\"".$arr["id"]."\" id=\"checkbox_tbl_" . $arr['id'] . "\"/></td>":"")."
	  
	  </tr>\n"); 
    }
    print("</table>"); 
     
	if (get_user_class() == UC_SYSOP){
    print ("<table align=\"center\" valign=\"top\" cellpadding=\"0\" width=\"100%\"> 
	<tr>
<td class=\"b\" align=\"center\">
<input type=\"hidden\" name=\"delete\" value=\"delete\"/><input style=\"height: 25px; width:200px\" type=\"submit\" name=\"submit\" value=\"Удалить это событие\"/>
</td>
</tr></form>
</table>");
  }
  
  }
  stdfoot(); 
?> 