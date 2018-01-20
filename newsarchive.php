<?
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();

stdhead("Все новости ".$SITENAME."");

  $count = get_row_count("news");
  $perpage = 20; //Сколько новостей на страницу

  list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] . "?");
  $resource = sql_query("SELECT news.* ,
   (SELECT username FROM users WHERE id=news.userid) AS addeduser,
   (SELECT username FROM users WHERE id=news.editby) AS user,
   COUNT(comments.id) FROM news LEFT JOIN comments ON comments.news = news.id and torrent=0 and poll=0 and offer=0
   GROUP BY news.id ORDER BY news.added DESC $limit") or sqlerr(__FILE__, __LINE__);

print ("<table border='0' cellspacing='0' width='100%' cellpadding='5'>
        <tr><td class='colhead' align='center'><b>Архив новостей &quot;".$SITENAME."&quot;</b></td></tr>
        ");

if ($count)
{
print ("<tr><td>".$pagertop."</td></tr>");
        
        
   while(list($id, $userid, $added, $editby, $edittime, $body,$subject, $addeduser,$user,$comments) = mysql_fetch_array($resource))
   {

     $date = date("d.m.Y",strtotime($added));
    if ($comments=="")
    $comments=0;
    
    
    if (get_user_class() >= UC_MODERATOR)
     {
if ($user){

$b="<b>[</b>Отредактировал $user в $edittime<b>]</b>";
}
else
$b="";

if ($addeduser)
{
$a="<b>[</b>Создал $addeduser в $added<b>]</b>";
}
else
$a="";
     	}
     	
     	
     print("<tr><td>");
     print("<table border='0' cellspacing='0' width='100%' cellpadding='5'>
            <tr><td class='colhead'>".htmlspecialchars($subject)." $a $b");
     print("</td></tr><tr><td>".format_comment($body)."</td></tr>");
     print("</td></tr>");
     print("<tr><td style='background-color: #F9F9F9'>

            <div style='float:left;'><b>Размещено</b>: ".$added." <b>Комментариев:</b> ".$comments." [<a href=\"newsoverview.php?id=".$id."\">Комментировать</a>]</div>");

     if (get_user_class() >= UC_ADMINISTRATOR)
     {
     print("<div style='float:right;'>
            <font class=\"small\">
            [<a href=\"news.php?action=edit&newsid=".$id."&returnto=".htmlentities($_SERVER['PHP_SELF'])."\">Редактировать</a>]
            [<a onClick=\"return confirm('Удалить эту новость?')\" href=\"news.php?action=delete&newsid=".$id."&returnto=".htmlentities($_SERVER['PHP_SELF'])."\">Удалить</a>]
            </font></div>");
     }
     print("</td></tr></table>");

   }  
   print ("<tr><td>".$pagerbottom."</td></tr>");
}
else
{
print("<tr><td><center><h3>Извините, но новостей нет...</h3></center></td></tr>");
}

print ("</table>");

stdfoot();
?>