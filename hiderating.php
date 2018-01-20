<? 
require "include/bittorrent.php"; 

dbconn(); 
loggedinorreturn(); 

if (get_user_class() < UC_ADMINISTRATOR) 
stderr("Ошибка", "доступа нет."); 

stdheadchat("Пользователи с неограниченным рейтингом"); 
$hiderating = number_format(get_row_count("users", "WHERE hiderating='yes'")); 
begin_frame("Пользователи с неограниченным рейтингом: ($hiderating)", true); 
begin_table(); 

$res = sql_query("SELECT * FROM users WHERE hiderating='yes' ORDER BY hideratinguntil") or sqlerr(); 
$num = mysql_num_rows($res); 
print("<table cellpadding=4 cellspacing=1 border=0 width=100% class=tableinborder id=table1>\n"); 
print("<tr align=center>
 <td class=a width=15%>Ник</td> 
 <td class=a width=10%>Зарегистрирован</td> 
 <td class=a width=15%>Последнее посещение</td>   
 <td class=a width=15%>Уровень</td> 
 <td class=a width=10%>Скачал</td> 
 <td class=a width=10%>Залил</td> 
 <td class=a width=10%>Рейтинг</td> 
 <td class=a width=15%>Снятие</td></tr>\n"); 
for ($i = 1; $i <= $num; $i++) 
{
$arr = mysql_fetch_assoc($res); 
if ($arr['added'] == '0000-00-00 00:00:00') 
  $arr['added'] = '-'; 
if ($arr['last_access'] == '0000-00-00 00:00:00') 
  $arr['last_access'] = '-'; 


if($arr["downloaded"] != 0){ 
$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3); 
} else { 
$ratio="---"; 
} 
$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>"; 
  $uploaded = mksize($arr["uploaded"]); 
  $downloaded = mksize($arr["downloaded"]); 
// $uploaded = str_replace(" ", "<br>", mksize($arr["uploaded"])); 
// $downloaded = str_replace(" ", "<br>", mksize($arr["downloaded"])); 

$added = substr($arr['added'],0,10); 
$last_access = substr($arr['last_access'],0,10); 

$class=get_user_class_name($arr["class"]); 

$hideratinguntil = $arr['hideratinguntil']; 

print("<tr><td align=center class=b><a href=userdetails.php?id=$arr[id]><b>".get_user_class_color($arr["class"] ,$arr["username"])."</b></a>" .($arr["donor"] =="yes" ? "<img src=/pic/star.gif border=0 alt='Donor'>" : "")."</td> 
 <td align=center class=b >$added</td> 
<td align=center class=b >$last_access</td> 
  <td align=center class=b >$class</td> 
  <td align=center class=b>$downloaded</td> 
 <td align=center class=b >$uploaded</td> 
 <td align=center class=b >$ratio</td>"); 
if ($hideratinguntil == '0000-00-00 00:00:00') 
     print("<td class=b>Неограниченное время!</td>\n"); 
   else 
   {
   print("<td align=center class=b>" . normaltime($hideratinguntil,true) . "</td></tr>\n");  
 } 
} 
print("</table>\n"); 
print("<p>$pagemenu<br>$browsemenu</p>"); 

stdfootchat(); 
die; 

?> 