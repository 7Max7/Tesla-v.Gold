<? 
require "include/bittorrent.php"; 
gzip();
dbconn(false); 

loggedinorreturn(); 

if (get_user_class() <= UC_MODERATOR) {
attacks_log('msg'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

if (get_user_class() == UC_SYSOP && isset($_POST["delmp"])) {
//	die($_SERVER["PHP_SELF"]);
$link=$_SERVER["PHP_SELF"]."?page=".(int) $_GET["page"];
accessadministration();
sql_query("DELETE FROM messages WHERE id IN (" . implode(", ", array_map("sqlesc", $_POST["delmp"])) . ")
"); //(" . implode(", ", $_POST[delmp]) . ")

@header("Location: $DEFAULTBASEURL$link") or die("Перенаправление на эту же страницу.<script>setTimeout('document.location.href=\"$DEFAULTBASEURL$link\"', 10);</script>");

}


$send=(int) $_GET["send"];
$receiv=(int) $_GET["receiv"];

if (!empty($receiv) && empty($send)){
$on_main="<form action=".$_SERVER["PHP_SELF"]."><input type=hidden><input type=submit value='Главная страничка Всех сообщений' class=btn></form>";
$rclastd=" class=\"a\" ";
$for_pagers="?receiv=".$receiv."&";
$sql_rs="WHERE receiver=".sqlesc($receiv);
}elseif (!empty($send) && empty($receiv)){
$on_main="<form action=".$_SERVER["PHP_SELF"]."><input type=hidden name=action value=on_main><input type=submit value='Главная страничка Всех сообщений' class=btn></form>";
$sclastd=" class=\"a\" ";
$for_pagers="?send=".$send."&";
$sql_rs="WHERE sender=".sqlesc($send);
}else {
$sclastd=" class=\"b\" ";
$rclastd=" class=\"b\" ";
$for_pagers="?";
unset($sql_rs);
unset($send);
unset($receiv);
}


$res2 = sql_query("SELECT COUNT(*) FROM messages $sql_rs"); 
$row = mysql_fetch_array($res2); 
$count = $row[0]; 
$perpage = 60;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"].$for_pagers); 

stdheadchat("Спам контроль; $perpage из $count"); 
echo $pagertop; 

$res = sql_query("SELECT m.msg,m.subject,m.unread, m.receiver,m.sender, m.id, m.added,
s.last_access AS sender_l, s.id AS sender_id ,s.username AS sender_u,s.class AS sender_c,
r.last_access AS receiver_l, r.id AS receiver_id, r.username AS receiver_u, r.class AS receiver_c
FROM messages AS m LEFT JOIN users AS s ON m.sender = s.id  LEFT JOIN users AS r ON m.receiver = r.id $sql_rs ORDER BY m.id DESC $limit") or sqlerr(__FILE__, __LINE__); 
//print($pagerbottom); 
//print("<h1>Спам контроль ($perpage сообщений на страницу)</h1>\n"); 
print("<table border=0 cellspacing=0 cellpadding=5>\n"); 
  
/*
r.last_access AS receiver_a, r.id AS receiver_id ,r.username AS receiver_u,r.class AS receiver_c

*/



if (get_user_class() == UC_SYSOP) {
echo"<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?page=".(int)$_GET["page"]."\">";
}


  print("<tr>
  <td class=a align=center>Отправитель</td>
  <td class=a align=center>Получатель</td>
  <td class=a align=center>UR</td>
  <td class=a align=center>Тема</td>
  <td class=a align=center>Содержание</td>
  <td class=a align=center>Дата</td>
  ".(get_user_class() == UC_SYSOP ? "<td class=a>Удалить</td>":"")."
  </tr>\n");
   while ($arr = mysql_fetch_assoc($res))
  {
    

$re="<br><a href=".$_SERVER["PHP_SELF"]."?receiv=".$arr["receiver_id"]."><img src="."\"/pic/mail-markread.gif\" alt=\"Прочитать все входящие сообщения для ".$arr["receiver_u"]."\"></a>";


    $receiver = "<center><a href=userdetails.php?id=" . $arr["receiver_id"] . "><b>".get_user_class_color($arr["receiver_c"], $arr["receiver_u"])."</b></a>$re</center>";
    
   // $last_access_r = normaltime($arr2["last_access"], true);
    $last_access_r = $arr["receiver_l"];
if ($arr["receiver_l"] == "0000-00-00 00:00:00")
	$last_access_r = $tracker_lang['never'];

/*
$send=(int) $_GET["send"];
$receiv=(int) $_GET["receiv"];
*/

$se="<br><a href=".$_SERVER["PHP_SELF"]."?send=".$arr["sender_id"]."><img src="."\"/pic/mail-send.gif\" alt=\"Прочитать все исходящие сообщения от ".$arr["sender_u"]."\"></a>";

////////////// кто послал сообщение
    if (!$arr["sender_u"]){
    $sender = "<center><font color=red>[<b>id ".$arr["sender_id"]."</b>]</font><br><b>Удален</b>$se</center>";
    }
    else
    $sender = "<center><a href=userdetails.php?id=" . $arr["sender_id"] . "><b>".get_user_class_color($arr["sender_c"], $arr["sender_u"])."</b></a>$se</center>";
    
        if($arr["sender"] == 0)
             $sender = "<center><font color=gray>[<b>System</b>]</font></center>";
    //	if(!$arr3["username"])
         //    $sender = "<center><font color=red>[<b>Удален</b>]</font></center>";
////////////// кто послал сообщение    

    $msg = format_comment($arr["msg"]);
    
   // $last_access_s = normaltime($arr3["last_access"], true);
    $last_access_s = $arr["sender_l"];
if ($arr["sender_l"] == "0000-00-00 00:00:00")
	$last_access_s = $tracker_lang['never'];
	
    $added = $arr["added"]; 
    
    if ($arr["receiver_l"]>$arr["added"])
	$added = "<i>".$added."</i>"; 

    $subject = htmlspecialchars_uni($arr["subject"]); 
   
    if ($CURUSER["id"]==$arr["receiver"] && $arr["sender"]<>0)
    $subject = "<a href=\"message.php?action=sendmessage&receiver=".$arr["sender"]."&replyto=".$arr["id"]."\">".$subject."</a>"; 
     
     if (!$arr["subject"] || $arr["subject"]=="Re:")    $subject = "<b>Тема пуста</b>"; 
    
if ($arr["unread"]<>"yes"){
$newmessageview = "<img  style=\"border:none\" alt=\"Сообщение прочитанно\" title=\"Сообщение прочитанно\" src=\"pic/ok.gif\">
"; 
} else (
$newmessageview = "<img  style=\"border:none\" alt=\"Сообщение не прочитанно\" title=\"Сообщение не прочитанно\" src=\"pic/error.gif\">"
);
///width=\"10%\"


if(
   ($arr["sender_c"]<=$CURUSER["class"] && $arr["receiver_c"]<=$CURUSER["class"]) 
   || 
   ($arr["sender"]==$CURUSER["id"] || $arr["receiver"]==$CURUSER["id"])
   ){
   	
   	
  	
  print("<tr>
  <td $sclastd align=center>$sender<br>$last_access_s</td>
  <td $rclastd align=center>$receiver<br>$last_access_r</td>
  <td class=b>$newmessageview</td>
  <td class=b>$subject</td>
  <td class=b align=left>$msg</td>
  <td class=b align=center>$added</td>"); 
  
if (get_user_class() == UC_SYSOP) {
  if ($_GET["check"] == "yes") {
    echo("<TD class=a align=center><INPUT type=\"checkbox\" checked name=\"delmp[]\" value=\"" . $arr['id'] . "\"><br>".$arr["id"]."</TD>\n</TR>\n"); 
   }
   else { 
    echo("<TD class=a align=center><INPUT type=\"checkbox\" name=\"delmp[]\" value=\"" . $arr['id'] . "\"><br>".$arr["id"]."</TD>\n</TR>\n"); 
   }
}



}


}
  print("</table>"); 


if (get_user_class() == UC_SYSOP) {
?>
<a href="msg.php?&page=<? echo (int) $_GET["page"];?>action=<? echo htmlentities($_get["action"]); ?>&send=<? echo $send; ?>&receiv=<? echo $receiv; ?>&check=yes">Выделить всё</a> | <a href="msg.php?&page=<? echo (int) $_GET["page"];?>action=<? echo htmlentities($_GET["action"]); ?>&send=<? echo $send; ?>&receiv=<? echo $receiv; ?>&check=no">Снять выделение</a><br>
<input type="submit" value="Удалить выделенные сообщения" /> <br><br>
</form>
<?
}
/*
вместо 
46 (queries) - 88.73% (php) - 11.27% (0.0233 => sql) - 1495 КБ (use memory) 
стало
 6 (queries) - 91.72% (php) -  8.28% (0.0176 => sql) - 1476 КБ (use memory) 
*/
echo $on_main;
print($pagerbottom); 
stdfootchat(); 
?>