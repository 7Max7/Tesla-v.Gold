<? 
require "include/bittorrent.php"; 
dbconn(false); 
loggedinorreturn(); 

if (get_user_class() < UC_SYSOP) 
{
attacks_log('bannedemails'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}
accessadministration(); 


$remove = (int)$_GET['remove']; 


if ($remove)
{
$res7 = sql_query("SELECT email FROM bannedemails WHERE id = ".sqlesc($remove)."") or sqlerr(__FILE__,__LINE__);
while ($arr7 = mysql_fetch_assoc($res7))

sql_query("DELETE FROM bannedemails WHERE id = ".sqlesc($remove)."") or sqlerr(__FILE__, __LINE__); 
  
if ($arr7["email"]){
$user = $CURUSER["username"];
$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER[username]);
write_log("Бан ".$arr7["email"]." был снят пользавателем $user\n", "$user_color","bans");
}

}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
        $email = trim($_POST["email"]); 
        $comment = trim($_POST["comment"]); 
        if (!$email || !$comment) 
        stderr("Error", "Missing form data."); 
        mysql_query("INSERT INTO bannedemails (added, addedby, comment, email) VALUES(".sqlesc(get_date_time()).", $CURUSER[id], ".sqlesc($comment).", ".sqlesc($email).")") or sqlerr(__FILE__, __LINE__); 
        header("Location: $_SERVER[REQUEST_URI]"); 
        die; 
}

$number=0;
$res = sql_query("SELECT * FROM bannedemails ORDER BY added DESC") or sqlerr(__FILE__, __LINE__); 

stdhead("Бан Емайлов"); 

print("<h1>Список банов</h1>\n"); 

if (mysql_num_rows($res) == 0) 
print("<p align=center><b>Пусто</b></p>\n"); 
else 
{
        print("<table border=1 cellspacing=0 cellpadding=5>\n"); 
        print("<tr><td class=colhead>Поставлен</td><td class=colhead align=left>Email</td>". 
        "<td class=colhead align=left>Кем</td><td class=colhead align=left>Коментарий</td><td class=colhead>Снять</td></tr>\n"); 

        while ($arr = mysql_fetch_assoc($res)) 
        {
$r2 = mysql_query("SELECT username,class FROM users WHERE id = $arr[addedby]") or sqlerr(__FILE__, __LINE__);    
$a2 = mysql_fetch_assoc($r2); 
   
   
                
$r3 = sql_query("SELECT enabled,username,class,id FROM users WHERE email =" .sqlesc($arr["email"]) . " ") or sqlerr(__FILE__, __LINE__); 
      $num=0;
while ($a3 = mysql_fetch_array($r3)) {

if ($col_num)
$col_num.=", ";
$col_num.="<a href=userdetails.php?id=".$a3["id"].">" . get_user_class_color($a3["class"], $a3["username"])."</a>
".($a3["enabled"] == "no" ? "<img src=\"/pic/warned2.gif\" alt=\"Отключен\">" : "")."";
	 $num++; 
}

$col_num=(!empty($num) ? "<hr><a href=\"javascript: klappe_news('a$number')\"><b>Почта принадлежит [$num]</b></a><div id=\"ka$number\" style=\"display: none;\">$col_num</div>": "");
      


	     
print("<tr>
<td>".$arr["added"]."</td>
<td align=left>".$arr["email"]."$col_num</td>
<td align=left><a href=userdetails.php?id=".$arr["addedby"].">" . get_user_class_color($a2["class"], $a2["username"]).""."</a></td>
<td align=left>$arr[comment]</td><td><a href=bannedemails.php?remove=$arr[id]>Снять бан</a></td>
</tr>\n"); 

unset($col_num);
$number++;
}
        
print("</table><br>\n"); 
}

print("<h2>Забанить</h2>\n"); 
print("<table border=1 cellspacing=0 cellpadding=5>\n"); 
print("<form method=\"post\" action=\"bannedemails.php\">\n"); 
print("<tr><td class=rowhead>Email</td><td><input type=\"text\" name=\"email\" size=\"40\"></td>\n"); 
print("<tr><td class=rowhead>Коментарий</td><td><input type=\"text\" name=\"comment\" size=\"40\"></td>\n"); 
print("<tr><td colspan=2>Иcпользуйте *@email.com чтобы забанить весь домен</td></tr>\n"); 
print("<tr><td colspan=2><input type=\"submit\" value=\"Забанить\" class=\"btn\"></td></tr>\n"); 
print("</form>\n</table>\n"); 

stdfoot(); 

?> 