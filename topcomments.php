<?

require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

stdheadchat("��������� 300 ������������ �������: ");



if (get_user_class() < UC_MODERATOR)
stderr("Error", "Permission denied.");




$res = mysql_query("SELECT *FROM comments  ORDER BY id DESC LIMIT 300") or sqlerr(__FILE__, __LINE__);
print("<h1>��������� 300 ������������ �������: </h1>\n");
  print("<table border=0 cellspacing=0 cellpadding=5>\n");
  print("<tr><td class=colhead align=center>�����</td><td class=colhead align=center>�����</td><td class=colhead align=left>����������</td><td class=colhead align=left>�������</td></tr>\n");
  while ($arr = mysql_fetch_assoc($res))
  {
    
    $res3 = mysql_query("SELECT username, class FROM users WHERE id=" . $arr["user"]) or sqlerr();
    $arr3 = mysql_fetch_assoc($res3);
    $sender = "<center><a href=userdetails.php?id=" . $arr["user"] . ">
	<b>".get_user_class_color($arr3["class"],$arr3["username"])."</b>
	</a></center>";
	
             if( $arr["user"] == 0 )
             $sender = "<font color=red><b>�����������</b></font>";
             
    $text = format_comment($arr["text"]);
     $torrent = format_comment ($arr["torrent"]);
$sure = $_GET["sure"];
$commentid = format_comment ($arr["id"]);

  print("<tr><td>$sender</td><td align=center><a href=$DEFAULTBASEURL/details.php?id=$torrent>id=$torrent</td><td align=left>$text</td><td align=center><a href=comment.php?action=delete&cid=$commentid><b>�������</a></b></td></tr>\n");
  }
  print("</table>");
  print("</table>");
print("<center><p></p></center>\n");

stdfootchat();

?> 

