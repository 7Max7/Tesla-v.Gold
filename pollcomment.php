<? 
require_once("include/bittorrent.php"); 

$action = (string) (htmlentities($_GET["action"])); 

dbconn(false); 

loggedinorreturn(); 
parked(); 

if ($CURUSER["commentpos"] == 'no')
stderr("Ошибка данных", "Вам запрещенно редактировать, создать или удалять комментарии.");


if ($action == "add") 
{
  if ($_SERVER["REQUEST_METHOD"] == "POST") 
  {
    $pid = (int) $_POST["pid"]; 
     if (!is_valid_id($pid)) 
     stderr($tracker_lang['error'], $tracker_lang['invalid_id']); 


$ss = mysql_query("SELECT comment FROM polls WHERE forum='0' AND id = $pid"); 
$su = mysql_fetch_array($ss);
if ($su["comment"]=="no" && get_user_class() < UC_ADMINISTRATOR){
stderr($tracker_lang['error'], "Для данного опроса комментарии запрещенны.");
die();
}
   
    
      $text = htmlspecialchars_uni((string)$_POST["text"]); 
      
      if (!$text)
            stderr($tracker_lang['error'], $tracker_lang['comment_cant_be_empty']); 

      mysql_query("INSERT INTO comments (user, poll, added, text, ori_text, ip) VALUES (" . 
          $CURUSER["id"] . ",$pid, '" . get_date_time() . "', " . sqlesc($text) . 
           "," . sqlesc($text) . "," . sqlesc(getip()) . ")") or sqlerr(__FILE__, __LINE__);
            //   mysql_query("UPDATE users SET bonus=bonus+10 WHERE id =".$CURUSER['id']); 
          
           $newid = mysql_insert_id(); 
@unlink(ROOT_PATH."cache/7a3dc3d8ffa625ae6aebaf672c73be6b.txt");
      header("Refresh: 0; url=polloverview.php?id=$pid&viewcomm=$newid#comm$newid"); 
      die; 
    }

  $pid = (int)$_GET["pid"]; 
  if (!is_valid_id($pid)) 
        stderr($tracker_lang['error'], $tracker_lang['invalid_id']); 


    stdhead("Добление комментария к опросу"); 

    print("<p><form name=\"comment\" method=\"post\" action=\"pollcomment.php?action=add\">\n"); 
    print("<input type=\"hidden\" name=\"pid\" value=\"$pid\"/>\n"); 
?> 
    <table class="main" border="0" cellspacing="0" cellpadding="3"> 
    <tr> 
    <td class="colhead"> 
<? 
    print("".$tracker_lang['add_comment']." к опросу"); 
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

    $res = sql_query("SELECT comments.id, text, comments.ip, comments.added, username, title, class, users.id as user, users.avatar, users.donor, users.enabled, users.warned, users.parked FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent=0 and news=0 and offer=0 and poll = $pid ORDER BY comments.id DESC") or sqlerr(__FILE__, __LINE__);

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


  $res = sql_query("SELECT pc.*, p.id AS pid, u.username FROM comments AS pc 
  LEFT JOIN polls AS p ON pc.poll = p.id AND p.forum='0'
  JOIN users AS u ON pc.user = u.id 
  WHERE torrent=0 and news=0 and offer=0 and pc.id=".sqlesc($commentid)."") or sqlerr(__FILE__,__LINE__); 
  $arr = mysql_fetch_array($res); 
  if (!$arr) 
      stderr($tracker_lang['error'], $tracker_lang['invalid_id']); 

$ss = mysql_query("SELECT comment FROM polls WHERE forum='0' AND id = $arr[pid]"); 
$su = mysql_fetch_array($ss);
if ($su["comment"]=="no" && get_user_class() < UC_ADMINISTRATOR){
stderr($tracker_lang['error'], "Для данного опроса комментарии запрещенны.");
die();
}


     stdhead("Добавления комментария к опросу"); 

    $text = "[quote=$arr[username]]" . $arr["text"] . "[/quote]\n"; 

    print("<form method=\"post\" name=\"comment\" action=\"pollcomment.php?action=add\">\n"); 
    print("<input type=\"hidden\" name=\"pid\" value=\"$arr[pid]\" />\n"); 
?> 

    <table class="main" border="0" cellspacing="0" cellpadding="3"> 
    <tr> 
    <td class="colhead"> 
<? 
    print("Добавления комментария к опросу"); 
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
elseif ($action == "edit") {

  $commentid = (int) $_GET["cid"]; 
  if (!is_valid_id($commentid)) 
   stderr($tracker_lang['error'], $tracker_lang['invalid_id']); 

  $res = sql_query("SELECT pc.*, p.id AS pid FROM comments AS pc 
  LEFT JOIN polls AS p ON pc.poll = p.id AND p.forum='0'
  WHERE torrent=0 and news=0 and offer=0 and pc.id=".sqlesc($commentid)."") or sqlerr(__FILE__,__LINE__); 
  $arr = mysql_fetch_array($res); 
  if (!$arr) 
      stderr($tracker_lang['error'], $tracker_lang['invalid_id']); 

    if ($arr["user"] <> $CURUSER["id"] && get_user_class() < UC_MODERATOR) 
        stderr($tracker_lang['error'], $tracker_lang['access_denied']); 

$ss = mysql_query("SELECT comment FROM polls WHERE forum='0' AND id = $arr[pid]"); 
$su = mysql_fetch_array($ss);
if ($su["comment"]=="no" && get_user_class() < UC_ADMINISTRATOR){
stderr($tracker_lang['error'], "Для данного опроса комментарии запрещенны.");
die();
}


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    	
     $text = htmlspecialchars_uni($_POST["text"]); 
    $returnto = htmlentities($_POST["returnto"]); 

      if (empty($text))
          stderr($tracker_lang['error'], $tracker_lang['comment_cant_be_empty']); 
          
      $text = sqlesc($text); 

      $editedat = sqlesc(get_date_time()); 

      sql_query("UPDATE comments SET text=$text, editedat=$editedat, editedby=$CURUSER[id] WHERE torrent=0 and news=0 and offer=0 and id=".sqlesc($commentid)."") or sqlerr(__FILE__, __LINE__); 
@unlink(ROOT_PATH."cache/7a3dc3d8ffa625ae6aebaf672c73be6b.txt");
        if ($returnto) 
          header("Location: $returnto"); 
        else 
          header("Location: $DEFAULTBASEURL/");      // change later ---------------------- 
        die; 
    }

     stdhead("Редактирование комментария к опросу"); 

    print("<form method=\"post\" name=\"comment\" action=\"pollcomment.php?action=edit&amp;cid=$commentid\">\n"); 
    print("<input type=\"hidden\" name=\"returnto\" value=\"polloverview.php?id={$arr["pid"]}&amp;viewcomm=$commentid#comm$commentid\" />\n"); 
    print("<input type=\"hidden\" name=\"cid\" value=\"$commentid\" />\n"); 
?> 

    <table class="main" border="0" cellspacing="0" cellpadding="3"> 
    <tr> 
    <td class="colhead"> 
<? 
    print("Редактирование комментария к опросу"); 
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

  if (empty($sure))  {
        stderr($tracker_lang['delete']." ".$tracker_lang['comment'], sprintf($tracker_lang['you_want_to_delete_x_click_here'],$tracker_lang['comment'],"?action=delete&cid=$commentid&sure=1")); 
  }

    $res = sql_query("SELECT poll,user,
    (SELECT username FROM users WHERE id=comments.user) AS classusername,
	(SELECT question FROM polls WHERE forum='0' AND id=comments.poll) AS name_poll
	FROM comments WHERE torrent=0 and news=0 and offer=0 and id=".sqlesc($commentid)."")  or sqlerr(__FILE__,__LINE__); 
    $arr = mysql_fetch_array($res); 
    
    if (!empty($arr))
        $pid = $arr["poll"]; 

$ss = mysql_query("SELECT comment FROM polls WHERE forum='0' AND id = $pid"); 
$su = mysql_fetch_array($ss);
if ($su["comment"]=="no" && get_user_class() < UC_ADMINISTRATOR){
stderr($tracker_lang['error'], "Для данного опроса комментарии запрещенны, а их удаление тем более.");
die();
}


    sql_query("DELETE FROM comments WHERE torrent=0 and news=0 and offer=0 and id=".sqlesc($commentid)) or sqlerr(__FILE__,__LINE__); 

    list($commentid) = mysql_fetch_row(sql_query("SELECT id FROM comments WHERE torrent=0 and news=0 and offer=0 and poll = $pid ORDER BY added DESC LIMIT 1")); 


$ip_user2 = $CURUSER["ip"];
$user2 = $CURUSER["username"];
$user_color2 = get_user_rgbcolor($CURUSER["class"], $CURUSER[username]);
write_log("$user2 удалил коммент $arr[classusername] ($commentid) в опросе $arr[name_poll] ($pid)\n", "$user_color2","comment");


    $returnto = "polloverview.php?id=$pid&amp;viewcomm=$commentid#comm$commentid"; 

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

  $res = sql_query("SELECT pc.*, p.id AS pid FROM comments AS pc 
  LEFT JOIN polls AS p ON pc.poll = p.id AND p.forum='0'
  WHERE torrent=0 and news=0 and offer=0 and pc.id=".sqlesc($commentid)."") or sqlerr(__FILE__,__LINE__); 
  $arr = mysql_fetch_array($res); 
  if (!$arr) 
      stderr($tracker_lang['error'], "Неверный идентификатор $commentid."); 

$ss = mysql_query("SELECT comment FROM polls WHERE forum='0' AND id = $arr[pid]"); 
$su = mysql_fetch_array($ss);
if ($su["comment"]=="no" && get_user_class() < UC_ADMINISTRATOR){
stderr($tracker_lang['error'], "Для данного опроса комментарии запрещенны а также и просмотр.");
die();
}


  stdhead("Просмотр оригинала"); 
  print("<h1>Оригинальное содержание комментария №$commentid</h1><p>\n"); 
    print("<table width=500 border=1 cellspacing=0 cellpadding=5>"); 
  print("<tr><td class=comment>\n"); 
    echo format_comment($arr["ori_text"]); 
  print("</td></tr></table>\n"); 

  $returnto = "polloverview.php?id={$arr["pid"]}&amp;viewcomm=$commentid#comm$commentid"; 

//    $returnto = "details.php?id=$torrentid&amp;viewcomm=$commentid#$commentid"; 

    if ($returnto) 
         print("<p><font size=small><a href=$returnto>Назад</a></font></p>\n"); 

    stdfoot(); 
    die; 
} 
else 
    stderr($tracker_lang['error'], "Неизвестное значение."); 

die; 
?> 