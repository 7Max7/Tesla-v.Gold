<?php 

require "include/bittorrent.php"; 
dbconn(false); 
loggedinorreturn(); 

if (!is_numeric($_GET['id'])) stderr($tracker_lang['error'], "Неверный ID"); 

$newsid = (int) $_GET['id']; 
$get_id =  (int) $_GET['id'];


if (get_user_class() < UC_USER)
  stderr($tracker_lang['error'], "Нет доступа."); 
   
if (!is_valid_id($_GET['id'])) {
  stderr($tracker_lang['error'], "Неверный ID"); 
}

stdhead("Комментирование новости"); 


if (isset($newsid)) {
  
$sql = sql_query("SELECT * FROM news WHERE id = ".sqlesc($newsid)." ORDER BY id") or sqlerr(__FILE__, __LINE__); 

///print("<h1>Обзор Новости</h1>"); 
print("<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr>
<td class=\"colhead\">Новость и ее комментарии</td>\n"); 

if (mysql_num_rows($sql) == 0) {
 print("<tr><td colspan=2>Извините...Нет новости с таким ID!</td></tr></table>"); 
 stdfoot(); 
 exit; 
 }
 
$news = mysql_fetch_assoc($sql);

 print("<tr><td colspan=2> 
 <div class=\"spoiler-wrap\" id=\"".$news['id']."\"><div class=\"spoiler-head folded clickable\">".$news['added'].": ".$news['subject']."</div><div class=\"spoiler-body\" style=\"display: none;\">".format_comment($news['body'])."</div></div>
 </td></tr>\n"); 

print("</table><br />\n"); 

$subres = mysql_query("SELECT COUNT(*) FROM comments WHERE news = ".sqlesc($get_id)." and torrent=0 and poll=0 and offer=0"); 
        $subrow = mysql_fetch_array($subres); 
        $count = $subrow[0]; 

        //$limited = 10; 
        if ($CURUSER["postsperpage"]=="0")
{  $limited = 15; } else {$limited = (int)$CURUSER["postsperpage"];}


if (!$count) {

  print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">"); 
  print("<tr><td class=colhead align=\"left\" colspan=\"2\">"); 
  print("<div style=\"float: left; width: auto;\" align=\"left\"> :: Список комментариев к новости</div>"); 
	if ($CURUSER["commentpos"] == 'yes'){
  print("<div align=\"right\"><a href=#comments class=altlink_white>Добавить комментарий</a></div>"); 
  }
  print("</td></tr><tr><td align=\"center\">"); 
   print("Комментариев нет. ".($CURUSER["commentpos"] == 'yes' ? "<a href=#comments>Желаете добавить?</a>" : "").""); 
  print("</td></tr></table><br>"); 

        }
        else {
        
 list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "newsoverview.php?id=$get_id&", array(lastpagedefault => 1)); 

                $subres = sql_query("SELECT nc.id, nc.ip, nc.text, nc.user, nc.added, nc.editedby, nc.editedat, u.avatar, u.warned, ". 
                  "u.username, u.enabled, u.title, u.class, u.donor,u.signature,u.signatrue, u.downloaded, u.uploaded, u.gender, u.last_access, e.username AS editedbyname,
				  
				  	(SELECT class FROM users WHERE id=nc.editedby) AS classbyname 
				   FROM comments AS nc LEFT JOIN users AS u ON nc.user = u.id LEFT JOIN users AS e ON nc.editedby = e.id WHERE news = " . 
                  "".$newsid."  and torrent=0 and poll=0 and offer=0 ORDER BY nc.id $limit") or sqlerr(__FILE__, __LINE__); 
                $allrows = array(); 

                while ($subrow = mysql_fetch_array($subres)) 
                        $allrows[] = $subrow; 




         print("<table class=main cellspacing=\"0\" cellPadding=\"5\" width=\"100%\" >"); 
         print("<tr><td class=\"colhead\" align=\"center\" >"); 
         print("<div style=\"float: left; width: auto;\" align=\"left\"> :: Список комментариев</div>"); 
          
		  if ($CURUSER["commentpos"] == 'yes'){
		 print("<div align=\"right\"><a href=#comments class=altlink_white>Добавить комментарий</a></div>"); 
		 }
		 
         print("</td></tr>"); 

         print("<tr><td>"); 
         print($pagertop); 
         print("</td></tr>"); 
         
         print("<tr><td>"); 
         commenttable($allrows,"newscomment"); 
         print("</td></tr>"); 
         
         print("<tr><td>"); 
         print($pagerbottom); 
         print("</td></tr>"); 
         print("</table>"); 
        }

   if ($CURUSER["commentpos"] == 'yes'){

 print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">"); 
  print("<tr><td class=colhead align=\"left\" colspan=\"2\">  <a name=comments>&nbsp;</a><b>:: Добавить комментарий к новости</b></td></tr>"); 
  print("<tr><td width=\"100%\" align=\"center\" >"); 
  //print("Ваше имя: "); 
  //print("".$CURUSER['username']."<p>"); 
  print("<form name=news method=\"post\" action=\"newscomment.php?action=add\">"); 
  print("<center><table border=\"0\"><tr><td class=\"clear\">"); 
  print("<div align=\"center\">". textbbcode("news","text","", 1) ."</div>"); 
  print("</td></tr></table></center>"); 
  print("</td></tr><tr><td  align=\"center\" colspan=\"2\">"); 
  print("<input type=\"hidden\" name=\"nid\" value=\"".$newsid."\"/>"); 
  print("<input type=\"submit\" class=btn value=\"Разместить комментарий\" />"); 
  print("</td></tr></form></table>"); 

}

}

stdfoot(); 
?> 