<?

require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

stdheadchat("Последние 300 комментариев трекера: ");



if (get_user_class() < UC_MODERATOR)
stderr("Error", "Permission denied.");




$res = mysql_query("SELECT *FROM comments  ORDER BY id DESC LIMIT 300") or sqlerr(__FILE__, __LINE__);
print("<h1>Последние 300 комментариев трекера: </h1>\n");
  print("<table border=0 cellspacing=0 cellpadding=5>\n");
  print("<tr><td class=colhead align=center>Автор</td><td class=colhead align=center>Адрес</td><td class=colhead align=left>Содержимое</td><td class=colhead align=left>Удалить</td></tr>\n");
  while ($arr = mysql_fetch_assoc($res))
  {
    
    $res3 = mysql_query("SELECT username, class FROM users WHERE id=" . $arr["user"]) or sqlerr();
    $arr3 = mysql_fetch_assoc($res3);
    $sender = "<center><a href=userdetails.php?id=" . $arr["user"] . ">
	<b>".get_user_class_color($arr3["class"],$arr3["username"])."</b>
	</a></center>";
	
             if( $arr["user"] == 0 )
             $sender = "<font color=red><b>Неизвестный</b></font>";
             
    $text = format_comment($arr["text"]);
     $torrent = format_comment ($arr["torrent"]);
$sure = $_GET["sure"];
$commentid = format_comment ($arr["id"]);

  print("<tr><td>$sender</td><td align=center><a href=$DEFAULTBASEURL/details.php?id=$torrent>id=$torrent</td><td align=left>$text</td><td align=center><a href=comment.php?action=delete&cid=$commentid><b>Стереть</a></b></td></tr>\n");
  }
  print("</table>");
  print("</table>");
print("<center><p></p></center>\n");

stdfootchat();

?> 

