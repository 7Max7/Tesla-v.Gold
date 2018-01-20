<? 
require_once("include/bittorrent.php"); 

$action = htmlentities($_GET["action"]); 

dbconn(false); 

loggedinorreturn(); 
parked(); 

if ($CURUSER["commentpos"] == 'no')
stderr("Ошибка данных", "Вам запрещенно редактировать, создать или удалять комментарии.");


if ($action == "add") 
{
  if ($_SERVER["REQUEST_METHOD"] == "POST") 
  {
    $nid = (int) $_POST["nid"]; 
    
      $text = htmlspecialchars_uni($_POST["text"]); 
      
      if (!$text)
            stderr($tracker_lang['error'], $tracker_lang['comment_cant_be_empty']); 

      mysql_query("INSERT INTO comments (user, news, added, text, ori_text, ip) VALUES (" . 
          $CURUSER["id"] . ", ".sqlesc($nid).", '" . get_date_time() . "', " . sqlesc($text) . 
           "," . sqlesc($text) . "," . sqlesc(getip()) . ")") or sqlerr(__FILE__,__LINE__);
            
           $newid = mysql_insert_id(); 
             //  mysql_query("UPDATE users SET bonus=bonus+10 WHERE id =".$CURUSER['id']); 

@unlink(ROOT_PATH."cache/block-news_users.txt"); // для оптимизации запросов, удаляем кеш после обновление его
@unlink(ROOT_PATH."cache/block-news_admins.txt"); // для оптимизации запросов, удаляем кеш после обновление его

      header("Refresh: 0; url=newsoverview.php?id=$nid&viewcomm=$newid#comm$newid"); 
      die; 
    }

  $nid = (int) $_GET["nid"]; 
  if (!is_valid_id($nid)) 
        stderr($tracker_lang['error'], $tracker_lang['invalid_id']); 


    stdhead("Добление комментария к новости"); 

    print("<p><form name=\"comment\" method=\"post\" action=\"newscomment.php?action=add\">\n"); 
    print("<input type=\"hidden\" name=\"nid\" value=\"$nid\"/>\n"); 
?> 
    <table class="main" border="0" cellspacing="0" cellpadding="3"> 
    <tr> 
    <td class="colhead"> 
<? 
    print("".$tracker_lang['add_comment']." к новости"); 
?> 
    </td> 
    </tr> 
    <tr> 
    <td> 
<? 
    textbbcode("comment","text",""); 
?> 
    </td></tr></table> 
<? 
    //print("<textarea name=\"text\" rows=\"10\" cols=\"60\"></textarea></p>\n"); 
    print("<p><input type=\"submit\" value=\"Добавить\" /></p></form>\n"); 

    $res = sql_query("SELECT comments.id, text, comments.ip, comments.added, username, title, class, users.id as user, users.avatar, users.donor, users.enabled, users.warned, users.parked FROM comments LEFT JOIN users ON comments.user = users.id WHERE news = ".sqlesc($nid)." and torrent=0 and poll=0 and offer=0 ORDER BY comments.id DESC"); 

    $allrows = array(); 
    while ($row = mysql_fetch_array($res)) 
      $allrows[] = $row; 

    if (count($allrows)) { 
      print("<h2>Последние комментарии, в обратном порядке</h2>\n"); 
      commenttable($allrows); 
    } 

  stdfoot(); 
    die; 
} 
elseif ($action == "quote") 
{
  $commentid = (int) $_GET["cid"]; 
  if (!is_valid_id($commentid)) 
        stderr($tracker_lang['error'], $tracker_lang['invalid_id']); 

  $res = sql_query("SELECT nc.*, n.id AS nid, u.username FROM comments AS nc LEFT JOIN news AS n ON nc.news = n.id JOIN users AS u ON nc.user = u.id WHERE nc.id=".sqlesc($commentid)." and torrent=0 and poll=0 and offer=0") or sqlerr(__FILE__,__LINE__); 
  $arr = mysql_fetch_array($res); 
  
  if (!$arr) 
      stderr($tracker_lang['error'], $tracker_lang['invalid_id']); 

     stdhead("Добавления комментария к новости"); 

    $text = "[quote=$arr[username]]" . $arr["text"] . "[/quote]\n"; 

    print("<form method=\"post\" name=\"comment\" action=\"newscomment.php?action=add\">\n"); 
    print("<input type=\"hidden\" name=\"nid\" value=\"$arr[nid]\" />\n"); 
?> 

    <table class="main" border="0" cellspacing="0" cellpadding="3"> 
    <tr> 
    <td class="colhead"> 
<? 
    print("Добавления комментария к новости"); 
?> 
    </td> 
    </tr> 
    <tr> 
    <td> 
<? 
    textbbcode("comment","text",htmlspecialchars_uni($text)); 
?> 
    </td></tr></table> 

<? 

    print("<p><input type=\"submit\" value=\"Добавить\" /></p></form>\n"); 

    stdfoot(); 

} 
elseif ($action == "edit") 
{ 
  $commentid = (int) $_GET["cid"]; 
  
  if (!is_valid_id($commentid)) 
        stderr($tracker_lang['error'], $tracker_lang['invalid_id']); 

  $res = sql_query("SELECT nc.*, n.id AS nid FROM comments AS nc LEFT JOIN news AS n ON nc.news = n.id WHERE nc.id=".sqlesc($commentid)." and nc.torrent=0 and nc.poll=0 and nc.offer=0") or sqlerr(__FILE__,__LINE__); 
  $arr = mysql_fetch_array($res); 
  if (!$arr) 
      stderr($tracker_lang['error'], $tracker_lang['invalid_id']); 

    if ($arr["user"] <> $CURUSER["id"] && get_user_class() < UC_MODERATOR) 
        stderr($tracker_lang['error'], $tracker_lang['access_denied']); 

    if ($_SERVER["REQUEST_METHOD"] == "POST") { 
   	
      $text = htmlspecialchars_uni($_POST["text"]); 
    $returnto = htmlentities($_POST["returnto"]); 

      if (empty($text))
          stderr($tracker_lang['error'], $tracker_lang['comment_cant_be_empty']); 
          
      $text = sqlesc($text); 

      $editedat = sqlesc(get_date_time()); 

      sql_query("UPDATE comments SET text=$text, editedat=$editedat, editedby=$CURUSER[id] WHERE id=$commentid and torrent=0 and poll=0 and offer=0") or sqlerr(__FILE__, __LINE__); 

        if ($returnto) 
          header("Location: $returnto"); 
        else 
          header("Location: $DEFAULTBASEURL/");      // change later ---------------------- 
        die; 
    }

     stdhead("Редактирование комментария к новости"); 

    print("<form method=\"post\" name=\"comment\" action=\"newscomment.php?action=edit&amp;cid=$commentid\">\n"); 
    print("<input type=\"hidden\" name=\"returnto\" value=\"newsoverview.php?id={$arr["nid"]}&amp;viewcomm=$commentid#comm$commentid\" />\n"); 
    print("<input type=\"hidden\" name=\"cid\" value=\"$commentid\" />\n"); 
?> 

    <table class="main" border="0" cellspacing="0" cellpadding="3"> 
    <tr> 
    <td class="colhead"> 
<? 
    print("Редактирование комментария к новости"); 
?> 
    </td> 
    </tr> 
    <tr> 
    <td> 
<? 
    textbbcode("comment","text",htmlspecialchars_uni($arr["text"])); 
?> 
    </td></tr></table> 

<? 

    print("<p><input type=\"submit\" value=\"Отредактировать\" /></p></form>\n"); 

    stdfoot(); 
    die; 
} 

elseif ($action == "delete") { 

    if (get_user_class() < UC_MODERATOR) 
        stderr($tracker_lang['error'], $tracker_lang['access_denied']); 

  $commentid = (int) $_GET["cid"]; 

  if (!is_valid_id($commentid)) 
        stderr($tracker_lang['error'], $tracker_lang['invalid_id']); 

  $sure = $_GET["sure"]; 

  if (!$sure) {
        stderr($tracker_lang['delete']." ".$tracker_lang['comment'], sprintf($tracker_lang['you_want_to_delete_x_click_here'],$tracker_lang['comment'],"?action=delete&cid=$commentid&sure=1")); 
  } 


    $res = sql_query("SELECT news, user,	
	(SELECT username FROM users WHERE id=comments.user) AS classusername,
	(SELECT subject FROM news WHERE id=comments.news) AS name_news
    FROM comments WHERE id=".sqlesc($commentid)." and torrent=0 and poll=0 and offer=0")  or sqlerr(__FILE__,__LINE__); 
    $arr = mysql_fetch_array($res); 
    if ($arr) 
        $nid = $arr["news"]; 

$ip_user2 = $CURUSER["ip"];
$user2 = $CURUSER["username"];
$user_color2 = get_user_rgbcolor($CURUSER["class"], $CURUSER[username]);
write_log("$user2 удалил коммент $arr[classusername] ($commentid) в новостях $arr[name_news] ($nid)\n", "$user_color2","comment");


    sql_query("DELETE FROM comments WHERE id=".sqlesc($commentid)." and torrent=0 and poll=0 and offer=0") or sqlerr(__FILE__,__LINE__); 
    
@unlink(ROOT_PATH."cache/block-news_users.txt"); // для оптимизации запросов, удаляем кеш после обновление его
@unlink(ROOT_PATH."cache/block-news_admins.txt"); // для оптимизации запросов, удаляем кеш после обновление его

    list($commentid) = mysql_fetch_row(sql_query("SELECT id FROM scomments WHERE news = $nid and torrent=0 and poll=0 and offer=0 ORDER BY added DESC LIMIT 1")); 

    $returnto = "newsoverview.php?id=$nid&amp;viewcomm=$commentid#comm$commentid"; 

    if ($returnto) 
      header("Location: $returnto"); 
    else 
      header("Location: $DEFAULTBASEURL/");      // change later ---------------------- 
    die; 
} 
elseif ($action == "vieworiginal") { 

    if (get_user_class() < UC_MODERATOR) 
        stderr($tracker_lang['error'], $tracker_lang['access_denied']); 

  $commentid = (int) $_GET["cid"]; 

  if (!is_valid_id($commentid)) 
        stderr($tracker_lang['error'], $tracker_lang['invalid_id']); 

  $res = sql_query("SELECT nc.*, n.id AS nid FROM comments AS nc LEFT JOIN news AS n ON nc.news = n.id WHERE nc.id=".sqlesc($commentid)." and nc.torrent=0 and nc.poll=0 and nc.offer=0") or sqlerr(__FILE__,__LINE__); 
  $arr = mysql_fetch_array($res); 
  if (!$arr) 
      stderr($tracker_lang['error'], "Неверный идентификатор $commentid."); 

  stdhead("Просмотр оригинала"); 
  print("<h1>Оригинальное содержание комментария № $commentid</h1><p>\n"); 
    print("<table width=500 border=1 cellspacing=0 cellpadding=5>"); 
  print("<tr><td class=comment>\n"); 
    echo htmlspecialchars_uni($arr["ori_text"]); 
  print("</td></tr></table>\n"); 

  $returnto = "newsoverview.php?id={$arr["nid"]}&amp;viewcomm=$commentid#comm$commentid"; 

//    $returnto = "details.php?id=$torrentid&amp;viewcomm=$commentid#$commentid"; 

    if ($returnto) 
         print("<p><font size=small><a href=$returnto>Назад</a></font></p>\n"); 

    stdfoot(); 
    die; 
}
else  stderr($tracker_lang['error'], "Неизвестная ошибка???"); 
die; 
?> 