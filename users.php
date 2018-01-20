<? 
require "include/bittorrent.php"; 
dbconn(); 
loggedinorreturn(); 

// Опции сортировки 
//$column = ''; 
//$ascdesc = ''; 

switch($_GET['sort']) {
case 'uname': $orderby = "username"; $order = "uname"; break; // По имени 
case 'added': $orderby = "added"; $order = "added"; break; // По дате регистрации 
case 'visit': $orderby = "last_access"; $order = "visit"; break; // Дате посещения 
case 'ratio': $orderby = "(uploaded/downloaded)"; $order = "ratio"; break; // Рейтингу 
case 'gender': $orderby = "gender"; $order = "gender"; break; // Полу 
case 'uclass': $orderby = "class"; $order = "uclass"; break; // Званию 
case 'country': $orderby = "country"; $order = "country"; break; // Стране 
case 'bonus': $orderby = "bonus"; $order = "bonus"; break; // Бонусу 
case 'ip': $orderby = "ip"; $order = "ip"; break; // IP 
case 'enabled': $orderby = "enabled"; $order = "enabled"; break; // Бану 
case 'warned': $orderby = "warned"; $order = "warned"; break; // Предупреждению 
case 'donor': $orderby = "donor"; $order = "donor"; break; // заморозка закачки 
default: $orderby = "username"; $order = "uname"; break; 
}

switch($_GET["abc"]) { 
case 'asc': $ascdesc = "ASC"; $linkascdesc = "asc"; break; 
case 'desc': $ascdesc = "DESC"; $linkascdesc = "desc"; break; 
default: $ascdesc = "ASC"; $linkascdesc = "asc"; break; 
} 
// Опции сортировки 

$search = htmlentities(trim($_GET['search'])); 
$class = (int)$_GET['class']; 
if ($class == '-' || !is_valid_user_class($class)) 
    $class = ''; 

if ($search != '' || $class) {
    $query = "username LIKE '%" . sqlwildcardesc("$search") . "%' AND status='confirmed'"; 
    if ($search) 
        $q = "search=" . htmlspecialchars($search); 
} else { 
    $letter = trim($_GET["letter"]); 
    if (strlen($letter) > 1) 
        die; 

    if ($letter <> "" && strpos("0123456789abcdefghijklmnopqrstuvwxyzабвгдеёжзийклмнопрстуфхцчшщъыьэюя", $letter) === false)
        $letter = "a";
    $query = ( $letter <> "" ? "username LIKE '$letter%' AND " : "") . "status='confirmed'"; 
    if ($letter == "")
    $q = "letter=$letter"; 
}

if (is_valid_user_class($class)) { 
    $query .= " AND class = $class"; 
    $q .= ($q ? "&amp;" : "") . "class=$class"; 
} 

stdhead("Пользователи"); 

print("<h2>Список пользователей сайта $SITENAME</h2><br>\n"); 

print("<form method=\"get\" action=\"users.php\">\n"); 
print("<b>Поиск</b>:&nbsp;<input type=\"text\" size=\"30\" name=\"search\" value=\"".htmlspecialchars($search)."\">\n"); 
print("<select name=\"class\">\n"); 
print("<option value=\"-\">Все классы пользователей</option>\n"); 
for ($i = 0;;++$i) { 
if ($c = get_user_class_name($i)) 
    print("<option value=\"$i\"" . (is_valid_user_class($class) && $class == $i ? " selected" : "") . ">$c</option>\n"); 
else 
    break; 
} 
print("</select>\n"); 
print("<input type=\"submit\" value=\"Вперед\">\n"); 
print("<input type=\"hidden\" name=\"abc\" value=\"".$linkascdesc."\">"); 
print("<input type=\"hidden\" name=\"sort\" value=\"".$order."\">"); 
print("</form>\n"); 

print("<p>\n"); 

print("<center>\n");  
for ($i = 0; $i < 32; ++$i)      
{  
    $l = chr($i - 32);  
    if ($l == $letter)  
    print("<b><font color=#FF0000>$l</font></b>\n");  
    else  
    print("<a href=?letter=$l&amp;abc=$linkascdesc&amp;sort=$order><b>$l</b></a>\n");  
}  
print("<br>\n");  
for ($i = 128; $i < 155; ++$i)      
{  
    $l = chr($i - 32);  
    if ($l == $letter)  
    print("<b><font color=#FF0000>$l</font></b>\n");  
    else  
    print("<a href=?letter=$l&amp;abc=$linkascdesc&amp;sort=$order><b>$l</b></a>\n");  
}  
print("<br>\n");  

for ($i = 48; $i < 58; ++$i)      
{  
    $l = chr($i);  
    if ($l == $letter)  
    print("<b><font color=#FF0000>$l</font></b>\n");  
    else  
    print("<a href=?letter=$l&amp;abc=$linkascdesc&amp;sort=$order><b>$l</b></a>\n");  
}              
print("</center>\n"); 

print("</p>\n"); 

$page = (int)$_GET['page']; 
$perpage = 50; 

$res = sql_query("SELECT COUNT(*) FROM users WHERE $query") or sqlerr(__FILE__, __LINE__); 
$arr = mysql_fetch_row($res); 
$pages = floor($arr[0] / $perpage); 

if ($pages * $perpage < $arr[0]) 
++$pages; 

if ($page < 1) 
$page = 1; 
else 
if ($page > $pages) 
$page = $pages; 

for ($i = 1; $i <= $pages; ++$i) 
if ($i == $page) 
$pagemenu .= "<b><u><font color='#757575'>$i</font></u></b>\n"; 
else 
$pagemenu .= "<a href=\"users.php?$q&page=$i&amp;abc=$linkascdesc&amp;sort=$order\"><b>$i</b></a>\n"; 

if ($page == 1) 
$browsemenu .= "<b><font color='#757575'>&lt;&lt; Пред</font></font></b>"; 
else 
$browsemenu .= "<a href=\"users.php?$q&page=".($page - 1)."&amp;abc=$linkascdesc&amp;sort=$order\"><b>&lt;&lt; Пред</b></a>"; 

$browsemenu .= "&nbsp;&nbsp;".$pagemenu."&nbsp;&nbsp;"; 

if ($page == $pages or !$pages) 
$browsemenu .= "<b><font color='#757575'>След &gt;&gt;</font></b>"; 
else 
$browsemenu .= "<a href=\"users.php?$q&page=".($page + 1)."&amp;abc=$linkascdesc&amp;sort=$order\"><b>След &gt;&gt;</b></a>"; 

print("<div style='padding-top:0px;'>$browsemenu</div>"); 
print("<div style='padding:5px;'>[&nbsp;<a href='users.php' ><b>все пользователи сайта</b></a>&nbsp;]</div>"); 



?> 
<div align="center"> 
<table class="main" border="0" cellspacing="0" width="100%" cellpadding="3"> 
<tr><td width="50%" style="border:none;"> 
<form method="GET" action="<?=$PHP_SELF;?>"> 
<nobr> 
<b>Сортировка по</b>: 
<select name="sort"> 
<option value="uname" <?=(($order == "uname") ? "selected" : "")?>>Имени пользователя</option> 
<? 
if(get_user_class() >= UC_MODERATOR){ 
?> 
<option value="added" <?=(($order == "added") ? "selected" : "")?>>Дате регистрации</option> 
<option value="visit" <?=(($order == "visit") ? "selected" : "")?>>Дате посещения</option> 
<option value="ratio" <?=(($order == "ratio") ? "selected" : "")?>>Рейтингу</option> 
<option value="gender" <?=(($order == "gender") ? "selected" : "")?>>Полу</option> 
<?}?> 
<option value="uclass" <?=(($order == "uclass") ? "selected" : "")?>>Классу</option> 
<option value="country" <?=(($order == "country") ? "selected" : "")?>>Стране</option> 
<? 
if(get_user_class() == UC_SYSOP){ 
?>  
<option value="bonus" <?=(($order == "bonus") ? "selected" : "")?>>Бонусу</option> 
<option value="ip" <?=(($order == "ip") ? "selected" : "")?> >Адресу ip</option> 

<?} if(get_user_class() >= UC_MODERATOR){?> 
<option value="enabled" <?=(($order == "enabled") ? "selected" : "")?>>Забаненные</option> 

<option value="warned" <?=(($order == "warned") ? "selected" : "")?>>Предупрежденные</option> 
<option value="donor" <?=(($order == "donor") ? "selected" : "")?>>Закачка заморожена</option> 
<?}?> 
</select> 
<input type="hidden" name="letter" value="<?=$letter?>"><input type="hidden" name="class" value="<?=$class?>"><input type="hidden" name="abc" value="<?=$linkascdesc?>"><input type="hidden" name="page" value="<?=$page?>"><input type="submit" value="Сортировать"> 
</nobr> 
</form> 
</td> 

<td width="50%" align="right"  style="border:none;"> 
<form method="GET" action="<?=$PHP_SELF;?>"> 
<nobr> 
<b>Порядок</b>: 
A-z<input type="radio" name="abc" value="asc" <?=(($linkascdesc == "asc") ? "checked" : "")?>> 
&nbsp;Z-a<input type="radio" name="abc" value="desc" <?=(($linkascdesc == "desc") ? "checked" : "")?>><input type="hidden" name="letter" value="<?=$letter?>"><input type="hidden" name="class" value="<?=$class?>"><input type="hidden" name="sort" value="<?=$order?>"><input type="hidden" name="page" value="<?=$page?>"><input type="submit" value="Изменить"> 
</nobr> 
</form> 
</td></tr></table></div> 
<? 

//$offset = ($page * $perpage) - $perpage; 



$offset = ceil(($page * $perpage) - $perpage);
if ($offset < 0)
$offset = 0;



$res = sql_query("SELECT u.*, c.name, c.flagpic FROM users AS u LEFT JOIN countries AS c ON c.id = u.country WHERE $query ORDER BY ".$orderby." ".$ascdesc." LIMIT $offset,$perpage") or sqlerr(__FILE__, __LINE__); 
$num = mysql_num_rows($res); 

$bonushead = $iphead = $dayshead = ""; 
if(get_user_class() == UC_SYSOP){ 
$bonushead = "<td class=\"colhead\" align=\"center\">Бонус</td>"; 
$iphead = "<td class=\"colhead\" align=\"center\">ip</td>"; 
$dayshead = "<td class=\"colhead\" align=\"center\">Дн.</td>";  
} 

print("<table border=\"1\" class=\"main\" cellspacing=\"0\" cellpadding=\"5\">\n"); 
print("<tr><td class=\"colhead\" align=\"center\">Имя</td><td class=\"colhead\" align=\"center\">&nbsp;&nbsp;Зарегистрирован&nbsp;&nbsp;</td><td class=\"colhead\" align=\"center\">&nbsp;&nbsp;Последний&nbsp;вход&nbsp;&nbsp;</td><td class=\"colhead\" align=\"center\">Рейтинг</td><td class=\"colhead\" align=\"center\">Пол</td><td class=\"colhead\"  align=\"center\">Класс</td><td class=\"colhead\" align=\"center\">Страна</td>".$bonushead."".$iphead."".$dayshead."</tr>\n"); 
for ($i = 0; $i < $num; ++$i) 
{ 
$arr = mysql_fetch_assoc($res); 
if ($arr['country'] > 0) { 
$country = "<td style=\"padding: 0px\" align=\"center\"><img src=\"pic/flag/$arr[flagpic]\" alt=\"$arr[name]\" title=\"$arr[name]\"></td>"; 
} 
else 
    $country = "<td align=\"center\">---</td>"; 
if ($arr['added'] == '0000-00-00 00:00:00') 
    $arr['added'] = '-'; 
if ($arr['last_access'] == '0000-00-00 00:00:00') 
    $arr['last_access'] = '-'; 
    
    
    if ((get_user_class() < UC_MODERATOR) && $arr["class"] > UC_ADMINISTRATOR)(
    $arr['last_access'] = 'Босс всегда с вами ;)');
    
    

    
if ($arr["downloaded"] > 0) {
    $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2); 
    
if (($arr["uploaded"] / $arr["downloaded"]) > 100)
    $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2); 
$ratio = "<font color=\"" . get_ratio_color($ratio) . "\">$ratio</font>"; 
}
else 
    if ($arr["uploaded"] > 0)
        $ratio = "Inf."; 
    else 
        $ratio = "------"; 

if ($arr["gender"] == "1") $gender = "<img src=\"".$pic_base_url."male.gif\" alt=\"Парень\" style=\"margin-left: 4pt\">"; 
elseif ($arr["gender"] == "2") $gender = "<img src=\"".$pic_base_url."female.gif\" alt=\"Девушка\" style=\"margin-left: 4pt\">"; 

if(get_user_class() == UC_SYSOP){
  if ($arr["bonus"] < 100){ 
  $bonus = "<td>".$arr["bonus"]."</td>"; 
  }elseif($arr["bonus"] < 200 ){ 
  $bonus = "<td><font color='red'>".$arr["bonus"]."</font></td>"; 
  } else { 
  $bonus = "<td><b><font color='red'>".$arr["bonus"]."</font></b></td>"; 
  } 

if ($arr['end_of_code']){ 
   $days_to_stop = round(($arr['end_of_code'] - TIMENOW)/ONEDAY); 
   } else { 
   $days_to_stop = "-/-"; 
   } 

   if ($arr['end_of_code'] and $days_to_stop <=3){ 
   $days = "<td><b><font color='red'>".$days_to_stop."</font></b></td>"; 
   } else { 
   $days = "<td><b>".$days_to_stop."</b></td>"; 
   }
$ip = "<td>".$arr["ip"]."</td>"; 
$colspan = 10; 
} else { 
$bonus = $ip = $days = ""; 
$colspan = 7; 
} 

$warned = $stopped = $stopped_pict = ""; 
if (get_user_class() >= UC_MODERATOR){ 
 if ($arr["warned"] == "yes"){  
 $warned = "<img alt=\"Предупрежден\" src=\"pic/warned.gif\">";  
 }

 if ($arr["enabled"] == "no" or $arr["stopped"] == "yes"){  
 $stopped = "style='text-decoration: line-through;'";  
 $stopped_pict = "<img alt=\"Заблокирован\" src=\"pic/disabled.gif\">"; 
 }
}
/*
if ($arr["donor"] == "yes") {
$adddonator = "<img alt=\"закачка &quot;заморожена&quot;\" src=\"pic/attention.gif\">";  
} else { 
$adddonator = ""; 
}
*/


switch($arr["class"]) {
case '0': $pic_class = "<center><font color=\"#".get_user_rgbcolor($arr["class"], $arr["class"])."\">".get_user_class_name($arr["class"])."</font></center>"; break; // Юзер 
case '1': $pic_class = "<center><font color=\"#".get_user_rgbcolor($arr["class"], $arr["class"])."\">".get_user_class_name($arr["class"])."</font></center>"; break; // Опытный юзер 
case '2': $pic_class = "<center><font color=\"#".get_user_rgbcolor($arr["class"], $arr["class"])."\">".get_user_class_name($arr["class"])."</font></center>"; break; // Вип 
case '3': $pic_class = "<center><font color=\"#".get_user_rgbcolor($arr["class"], $arr["class"])."\">".get_user_class_name($arr["class"])."</font></center>"; break; // Аплоадер 
case '4': $pic_class = "<center><font color=\"#".get_user_rgbcolor($arr["class"], $arr["class"])."\">".get_user_class_name($arr["class"])."</font></center>"; break; // Модератор 
case '5': $pic_class = "<center><font color=\"#".get_user_rgbcolor($arr["class"], $arr["class"])."\">".get_user_class_name($arr["class"])."</font></center>"; break; // Админ 
case '6': $pic_class = "<center><font color=\"#".get_user_rgbcolor($arr["class"], $arr["class"])."\">".get_user_class_name($arr["class"])."</font></center>"; break; // Сисоп 
} 


  if ($arr["profhide"] == "")
  { //Если профайл не скрыт, показываем всем 
  print("<tr><td align=\"left\"><a ".$stopped." href=\"userdetails.php?id=$arr[id]\"><b>".get_user_class_color($arr["class"], $arr["username"])."</b></a> ".$stopped_pict.$warned.$adddonator."" .($arr["donated"] == 1 ? "<img src=\"pic/star.gif\" border=\"0\" alt=\"Донор\">" : "")."</td>" . 
        "<td>$arr[added]</td><td>$arr[last_access]</td><td>$ratio</td><td>$gender</td>". 
        "<td align=\"left\">".$pic_class."</td>".$country."".$bonus."".$ip."".$days."</tr>\n"); 
  } elseif ($CURUSER["id"] == $arr["id"] or (get_user_class() >= UC_MODERATOR && $arr["class"] < 5)){ 
  print("<tr title='Профиль пользователя скрыт от просмотра' style='background-color: #FFF2F2;'> 
         <td align=\"left\"><a ".$stopped." href=\"userdetails.php?id=$arr[id]\"><b>".get_user_class_color($arr["class"], $arr["username"])."</b></a>".$stopped_pict.$warned.$adddonator."" .($arr["donated"] > 0 ? "<img src=\"pic/star.gif\" border=\"0\" alt=\"Donor\">" : "")."</td>" . 
        "<td>$arr[added]</td><td>$arr[last_access]</td><td>$ratio</td><td>$gender</td>". 
        "<td align=\"left\">".$pic_class."</td>".$country."".$bonus."".$ip."".$days."</tr>\n"); 
  } 
  else 
  {
  print("<tr><td align=\"left\">".$stopped_pict.$warned.$adddonator.$silentban_pic."".($arr["title"] != "" ? "<sup><span title=\"".$arr["title"]."\" style=\"color: purple;\">{$arr['title']}</span><br></sup>" : "")."<a ".$stopped." href=\"userdetails.php?id=$arr[id]\"><b>".get_user_class_color($arr["class"], $arr["username"])."</b></a>" .($arr["donated"] > 0 ? "<img src=\"pic/star.gif\" border=\"0\" alt=\"Donor\">" : "")."</td>" . 
        "<td colspan='4' align=\"center\" style='background-color: #FFF2F2;'><b><font color='red'>Пользователь предпочел скрыть эту информацию</font></b></td>". 
        "<td align=\"left\">".$pic_class."</td>".$country."".$bonus."".$ip."".$days."</tr>\n");   
  }

} 

 if (get_user_class() == UC_SYSOP){ 
 $colspan = 10; 
 } else { 
 $colspan = 7; 
 } 

if (!$num){ 
print("<tr><td colspan='$colspan' style='color: red' align='center'><b>Извините, но по вашему запросу ничего не найдено...</b> <p><a href='users.php'>Список всех пользователей сайта</a></p></td></tr>"); 
} 

$registered = number_format(get_row_count("users")); 
print("<tr><td colspan='$colspan' style='background-color: #EBEBEB' align='center'><b>Всего зарегистрировано пользователей на сайте: <font color='red'>".$registered."</font></b></td></tr>"); 
print("</table>\n"); 

//print("<p>$pagemenu<br />$browsemenu</p>"); 
print("<div style='padding-top:5px;'>$browsemenu</div>"); 
print("<div style='padding:5px;'>[&nbsp;<a href='users.php'><b>все пользователи сайта</b></a>&nbsp;]</div>"); 

stdfoot(); 
?> 