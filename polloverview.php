<?
require "include/bittorrent.php"; 
dbconn(false); 
loggedinorreturn(); 

if (!is_numeric($_GET['id'])) stderr($tracker_lang['error'], "Неверный ID"); 

//$action = $_GET["action"]; 
$pollid = (int) $_GET['id']; 
$viewid = (int) $_GET['view']; 
//$returnto = $_GET["returnto"]; 

if (get_user_class() < UC_USER)
  stderr($tracker_lang['error'], "Нет доступа."); 

if (!is_valid_id($_GET['id'])) {
  stderr($tracker_lang['error'], "Неверный ID"); 
} 

stdhead("Обзор опросов"); 

if (isset($_GET['id']) && $viewid==1) {
	

	
	
$sql = sql_query("SELECT *, (SELECT username FROM users WHERE id=polls.createby) AS username FROM polls WHERE forum='0' AND id = {$pollid} ORDER BY id DESC") or sqlerr(__FILE__, __LINE__);

print("<h1>Обзор опроса</h1>\n"); 

print("<p><table width=100% border=1 cellspacing=0 cellpadding=5><tr>\n" . 
"<td class=colhead align=center>ID</td><td class=colhead >Добавлен</td></tr>\n"); 

if (mysql_num_rows($sql) == 0) {
 print("<tr><td colspan=2>Извините...Нет опроса с таким ID!</td></tr></table>"); 
 stdfoot(); 
 exit; 
 } 
  
while ($poll = mysql_fetch_assoc($sql)) 
{
 /*$o = array($poll["option0"], $poll["option1"], $poll["option2"], $poll["option3"], $poll["option4"], 
  $poll["option5"], $poll["option6"], $poll["option7"], $poll["option8"], $poll["option9"], 
  $poll["option10"], $poll["option11"], $poll["option12"], $poll["option13"], $poll["option14"], 
  $poll["option15"], $poll["option16"], $poll["option17"], $poll["option18"], $poll["option19"]); 
  */
  
 
  $o = array(format_comment($poll["option0"]), format_comment($poll["option1"]), format_comment($poll["option2"]), format_comment($poll["option3"]), format_comment($poll["option4"]),
    format_comment($poll["option5"]), format_comment($poll["option6"]), format_comment($poll["option7"]), format_comment($poll["option8"]), format_comment($poll["option9"]),
    format_comment($poll["option10"]), format_comment($poll["option11"]), format_comment($poll["option12"]), format_comment($poll["option13"]), format_comment($poll["option14"]),
    format_comment($poll["option15"]), format_comment($poll["option16"]), format_comment($poll["option17"]), format_comment($poll["option18"]), format_comment($poll["option19"]));


$question=format_comment($poll["question"]);
 $added = date("Y-m-d h-i-s",strtotime($poll['added'])) . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($poll["added"]))) . " назад)"; 
 print("<tr><td align=center>{$poll['id']}</td><td>{$added} - ".$poll['username']."</td></tr>\n"); 
  
}

print("</table><hr>\n"); 
print("<h1>".$question."</h1>\n"); 
print("<table width=100% border=1 cellspacing=0 cellpadding=5><tr><td class=colhead>Опция №</td><td class=colhead>Ответ</td></tr>\n"); 
foreach($o as $key=>$value) { 
 if($value != "") 
 print("<tr><td>{$key}</td><td>{$value}</td></tr>\n"); 
 } 
print("</table><hr>\n"); 
}
//print"<script type=\"text/javascript\" src=\"js/poll.core.js\"></script><link href=\"js/poll.core.css\" type=\"text/css\" rel=\"stylesheet\" /><script type=\"text/javascript\">$(document).ready(function(){loadpoll();});</script><table width=\"100%\" class=\"main\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\"><tr><td class=\"text\" align=\"center\"><div id=\"poll_container\"><div id=\"loading_poll\" style=\"display:none\"></div><noscript><b>Пожалуйста включите показ скриптов</b></noscript></div><br/></td></tr></table>";

if (get_user_class() >= UC_ADMINISTRATOR && $viewid==1)
{

 $sql2 = sql_query("SELECT pollanswers. * , users.username,users.class FROM pollanswers LEFT JOIN users ON users.id = pollanswers.userid WHERE pollid = {$pollid} AND selection < 20 ORDER  BY users.id DESC ") or sqlerr(__FILE__, __LINE__); 

print("<h1>Ответы голосовавших пользователей</h1>\n"); 
print("<p><table width=100% border=1 cellspacing=0 cellpadding=5><tr>\n" . 
"<td class=colhead align=center>Пользователь</td><td class=colhead>Выбор</td></tr>\n"); 

if (mysql_num_rows($sql2) == 0) { 
 print("<tr><td colspan=2><center>Извините...Нет голосовавших пользователей!</center></td></tr>"); 
 //stdfoot(); 
 ///exit; 
}

while ($useras = mysql_fetch_assoc($sql2)) 
{
 $username  = ($useras['username'] ? get_user_class_color($useras['class'] ,$useras['username']) : "Неизвестно"); 
 //$useras['selection']--; 
 print("<tr><td><center>{$username}</center></td><td>{$o[$useras['selection']]}</td></tr>\n"); 
}
print("</table><hr>\n"); 
}

$get_id = (int) $_GET['id'];


$ss = mysql_query("SELECT comment FROM polls WHERE forum='0' AND id = $get_id"); 
$su = mysql_fetch_array($ss);
if ($su["comment"]=="no" && get_user_class() < UC_ADMINISTRATOR){
stderr($tracker_lang['error'], "Для данного опроса комментарии запрещенны.");
die();
}
if ($su["comment"]=="no")
{
$comment="<hr>Пользователям запрещенно комментировать данный опрос, <a href=\"makepoll.php?action=edit&pollid=$get_id \" class=altlink_white>нажмите здесь</a> чтобы отредактировать опрос, иначе для админов и выше.";
}



$subres = mysql_query("SELECT COUNT(*) FROM comments WHERE torrent=0 and news=0 and offer=0 and poll = $get_id"); 
        $subrow = mysql_fetch_array($subres);
        $count = $subrow[0];
       
	   if ($CURUSER["postsperpage"]=="0")
{  $limited = 15; } else {$limited = $CURUSER["postsperpage"];}

	   // $limited = 10;

if (!$count) {
  print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">"); 
  print("<tr><td class=colhead align=\"left\" colspan=\"2\">"); 
  print("<div style=\"float: left; width: auto;\" align=\"left\"> :: Список комментариев к опросу</div>"); 
    
	if ($CURUSER["commentpos"] == 'yes'){
  print("<div align=\"right\"><a href=#comments class=altlink_white>Добавить комментарий</a></div>"); 
  }
  print("</td></tr><tr><td align=\"center\">"); 
  print("Комментариев нет. ".($CURUSER["commentpos"] == 'yes' ? "<a href=#comments>Желаете добавить?</a>" : "").""); 
  print("</td></tr></table><br>"); 

        }
        else {
                list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "polloverview.php?id=$get_id&", array(lastpagedefault => 1)); 

                $subres = sql_query("SELECT pc.id, pc.ip, pc.text, pc.user, pc.added, pc.editedby, pc.editedat, u.avatar, u.warned, ". 
                  "u.username, u.title, u.class, u.enabled, u.donor, u.downloaded, u.signature,u.signatrue, u.uploaded, u.gender, u.last_access, e.username AS editedbyname,
				  	(SELECT class FROM users WHERE id=pc.editedby) AS classbyname 
				  FROM comments AS pc LEFT JOIN users AS u ON pc.user = u.id LEFT JOIN users AS e ON pc.editedby = e.id WHERE torrent=0 and news=0 and offer=0 and poll = " . 
                  "".$pollid." ORDER BY pc.id $limit") or sqlerr(__FILE__, __LINE__); 
                $allrows = array(); 

                while ($subrow = mysql_fetch_array($subres)) 
                        $allrows[] = $subrow; 


         print("<table class=main cellspacing=\"0\" cellPadding=\"5\" width=\"100%\" >"); 
         print("<tr><td class=\"colhead\" align=\"center\" >"); 
         print("<div style=\"float: left; width: auto;\" align=\"left\"> :: Список комментариев</div>"); 
         	if (get_user_class() >= UC_ADMINISTRATOR){
         	$admin="<a href=\"makepoll.php?action=edit&pollid=$get_id \" class=altlink_white>Редактировать опрос</a> : ";
         	}
         	if ($CURUSER["commentpos"] == 'yes'){
		 print("<div align=\"right\">$admin<a href=#comments class=altlink_white>Добавить комментарий</a></div>"); 
		 }
         print("$comment</td></tr>"); 
         print("<tr><td>"); 
         print($pagertop); 
         print("</td></tr>"); 
         print("<tr><td>"); 
         commenttable($allrows,"pollcomment"); 
		 print("</td></tr>"); 
         print("<tr><td>"); 
         print($pagerbottom); 
         print("</td></tr>"); 
         print("</table>"); 
        }
        
        
         if ($CURUSER["commentpos"] == 'yes'){

  print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">"); 
  print("<tr><td class=colhead align=\"left\" colspan=\"2\">$admin<a name=comments>&nbsp;</a><b>:: Добавить комментарий к опросу</b></td></tr>"); 
  print("<tr><td width=\"100%\" align=\"center\" >"); 
  print("<form name=comment method=\"post\" action=\"pollcomment.php?action=add\">"); 
  print("<center><table border=\"0\"><tr><td class=\"clear\">"); 
  print("<div align=\"center\">". textbbcode("comment","text","", 1) ."</div>"); 
  print("</td></tr></table></center>"); 
  print("</td></tr><tr><td  align=\"center\" colspan=\"2\">"); 
  print("<input type=\"hidden\" name=\"pid\" value=\"".$pollid."\"/>"); 
  print("<input type=\"submit\" class=btn value=\"Разместить комментарий\" />"); 
  print("</td></tr></form></table>"); 
 }
 
 
stdfoot(); 
?> 