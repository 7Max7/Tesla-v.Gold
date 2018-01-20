<? require_once("include/bittorrent.php");

function bark($msg, $error = true) {
global $tracker_lang;
stdhead(($error ? $tracker_lang['error'] : $tracker_lang['torrent']." ".$tracker_lang['bookmarked']));
  // stdmsg(($error ? $tracker_lang['error'] : $tracker_lang['success']), $msg, ($error ? 'error' : 'success'));
    stdmsg(($error ? "Ошибка" : "Успешно"), $msg, ($error ? 'error' : 'success'));
stdfoot();
exit;
}

$action = htmlentities($_GET["action"]);

dbconn(false);

loggedinorreturn();
parked();

if ($CURUSER["commentpos"] == 'no')
stderr("Ошибка данных", "Вам запрещенно редактировать, создать или удалять комментарии.");

if ($action == "add")
{




  $minutes = 1;
  $limit = 4;
  $res = sql_query("SELECT COUNT(*) FROM comments WHERE user = " .sqlesc($CURUSER["id"]) . " AND added > '".get_date_time(gmtime() - ($minutes * 60))."'") or sqlerr(__FILE__,__LINE__);
  $row = mysql_fetch_row($res);

  if ($row[0] > $limit)
    stderr("Антифлуд защита", "Запрещенно больше $limit сообщений за $minutes минуту.");

	
	
	
  if ($_SERVER["REQUEST_METHOD"] == "POST")
  {
    $torrentid = (int) $_POST["tid"];
	  if (!is_valid_id($torrentid))
			stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
		$res = sql_query("SELECT name, comment_lock FROM torrents WHERE id = " .sqlesc($torrentid) . "") or sqlerr(__FILE__,__LINE__);
		$arr = mysql_fetch_array($res);
	
	 if ($arr["comment_lock"] == 'yes')
stderr("Самому хитрому, пишу - Запрещенно", "Для торрента $arr[name] комментарии заблокированы администрацией.");


		if (!$arr)
		  stderr($tracker_lang['error'], $tracker_lang['no_torrent_with_such_id']);
		$name = $arr[0];
		
	  $text = htmlspecialchars_uni($_POST["text"]);
	  
	// $text = str_replace("\n","",$text);
//	 $text = str_replace("\r","",$text);
	      
	  if (!$text)
			stderr($tracker_lang['error'], $tracker_lang['comment_cant_be_empty']);

  if (get_user_class() == UC_SYSOP){
         $sender_id = ($_POST['sender'] == 'system' ? 0 : $CURUSER['id']);
              }
              else
               $sender_id = $CURUSER['id'];
               

sql_query("INSERT INTO comments (user, torrent, added, text, ori_text, ip) VALUES (" .$sender_id. "," .sqlesc($torrentid) . ", '" . get_date_time() . "', " . sqlesc($text) ."," . sqlesc($text) . "," . sqlesc(getip()) . ")");

$newid = mysql_insert_id();
sql_query("UPDATE torrents SET comments = comments + 1 WHERE id = " .sqlesc($torrentid) . "");



/////////////////СЛЕЖЕНИЕ ЗА КОММЕНТАМИ///////////////// 
$res3 = mysql_query("SELECT * FROM checkcomm WHERE checkid =" .sqlesc($torrentid) . " AND torrent = 1") or sqlerr(__FILE__,__LINE__); 
    while ($arr3 = mysql_fetch_array($res3)) {
   	
$name3 = "". format_comment($arr["name"]) . "";
$name4=strlen($arr["name"])>70?(substr($arr["name"],0,35)."..."):$name3; 

$subject="Новый коммент к $name4";

  if (get_user_class() == UC_SYSOP){
         $sender_name = ($_POST['sender'] == 'system' ? "[color=gray][[b]System[/b]][/color]" : "[color=#".get_user_rgbcolor($CURUSER["class"],$CURUSER["username"])."]".$CURUSER["username"]."[/color]");
              }
              else
               $sender_name = "[color=#".get_user_rgbcolor($CURUSER["class"],$CURUSER["username"])."]".$CURUSER["username"]."[/color]";

$msg = ("Для торрента [url=$DEFAULTBASEURL/details.php?id=$torrentid&viewcomm=$newid#comm$newid]".$name."[/url] $sender_name написал комментарий:[hr] $text [hr]");

if ($_POST['sender'] == 'system'){
write_log("Cообщение от system - ".$CURUSER["username"]." в $name4","808080","comment");
}
    if ($CURUSER["id"] <> $arr3["userid"])
     mysql_query("INSERT INTO messages (sender, receiver, added,subject, msg, poster) VALUES (0, " .sqlesc($arr3["userid"]) . ", NOW(), " .sqlesc($subject) . "," .sqlesc($msg) . ", 0)")or sqlerr(__FILE__,__LINE__); 
    }
/////////////////СЛЕЖЕНИЕ ЗА КОММЕНТАМИ/////////////////  

      @unlink(ROOT_PATH."cache/block-comment.txt");

	  header("Refresh: 0; url=details.php?id=$torrentid&viewcomm=$newid#comm$newid");
	  die;
	}

  $torrentid = (int) $_GET["tid"];
  if (!is_valid_id($torrentid))
		stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

	$res = sql_query("SELECT name FROM torrents WHERE id = " .sqlesc($torrentid) . "") or sqlerr(__FILE__,__LINE__);
	$arr = mysql_fetch_array($res);
	if (!$arr)
	  stderr($tracker_lang['error'], $tracker_lang['no_torrent_with_such_id']);

	stdhead("Добление комментария к \"" . $arr["name"] . "\"");

	print("<p><form name=\"comment\" method=\"post\" action=\"comment.php?action=add\">\n");
	print("<input type=\"hidden\" name=\"tid\" value=\"$torrentid\"/>\n");
?>
	<table class=main border=0 cellspacing=0 cellpadding=3>
	<tr>
	<td class="colhead">
<?   $name1 = "". format_comment($arr["name"]) . "";
     $arr["name"]=strlen($arr["name"])>70?(substr($arr["name"],0,35)."..."):$name1; 

	
	print("".$tracker_lang['add_comment']." к \"" . htmlspecialchars($arr["name"]) . "\"");
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

	$res = sql_query("SELECT comments.id, text, comments.ip, comments.added, username, title, class, users.id as user, users.avatar, users.donor, users.enabled, users.warned, users.parked FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = " .sqlesc($torrentid) . " ORDER BY comments.id DESC LIMIT 5");

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

  $res = sql_query("SELECT c.*, t.name, t.id AS tid, u.username FROM comments AS c LEFT JOIN torrents AS t ON c.torrent = t.id JOIN users AS u ON c.user = u.id WHERE c.id=" .sqlesc($commentid) . "") or sqlerr(__FILE__,__LINE__);
  $arr = mysql_fetch_array($res);
  if (!$arr)
  	stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

$name1 = "". format_comment($arr["name"]) . "";
$arr["name"]=strlen($arr["name"])>70?(substr($arr["name"],0,35)."..."):$name1; 

	
 	stdhead("Добавления комментария к \"" . $arr["name"] . "\"");

	$text = "[quote=$arr[username]]" . $arr["text"] . "[/quote]\n";

	print("<form method=\"post\" name=\"comment\" action=\"comment.php?action=add\">\n");
	print("<input type=\"hidden\" name=\"tid\" value=\"$arr[tid]\" />\n");
?>

	<table class=main border=0 cellspacing=0 cellpadding=3>
	<tr>
	<td class="colhead">
<?
	print("Добавления комментария к \"" . htmlspecialchars($arr["name"]) . "\"");
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

  $res = sql_query("SELECT c.*, t.name, t.id AS tid FROM comments AS c LEFT JOIN torrents AS t ON c.torrent = t.id WHERE c.id=" .sqlesc($commentid) . "") or sqlerr(__FILE__,__LINE__);
  $arr = mysql_fetch_array($res);
  if (!$arr)
  	stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

	if ($arr["user"] <> $CURUSER["id"] && get_user_class() < UC_MODERATOR)
		stderr($tracker_lang['error'], $tracker_lang['access_denied']);

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
	  $text = htmlspecialchars_uni($_POST["text"]);
	 
    $returnto = htmlspecialchars($_POST["returnto"]);

	  if (empty($text))
	  	stderr($tracker_lang['error'], $tracker_lang['comment_cant_be_empty']);

//	  $text = sqlesc($text);

	  $editedat = get_date_time();

	  sql_query("UPDATE comments SET text=" .sqlesc($text) . ", editedat=" .sqlesc($editedat) . ", editedby=".sqlesc($CURUSER["id"])." WHERE id=" .sqlesc($commentid) . "") or sqlerr(__FILE__, __LINE__);

		if ($returnto)
	  	header("Location: $returnto");
		else
		  header("Location: $DEFAULTBASEURL/");      // change later ----------------------
		die;
	}

$name1 = "". format_comment($arr["name"]) . "";
$arr["name"]=strlen($arr["name"])>70?(substr($arr["name"],0,30)."..."):$name1; 

 	stdhead("Редактирование комментария к \"" . htmlspecialchars($arr["name"]) . "\"");
	print("<form method=\"post\" name=\"comment\" action=\"comment.php?action=edit&amp;cid=$commentid\">\n");
	
	print("<input type=\"hidden\" name=\"returnto\" value=\"details.php?id={$arr["tid"]}&amp;viewcomm=$commentid#comm$commentid\" />\n");
	print("<input type=\"hidden\" name=\"cid\" value=\"$commentid\" />\n");
?>

	<table class=main border=0 cellspacing=0 cellpadding=3>
	<tr>
	<td class="colhead">
<?  
   $name1 = "". format_comment($arr["name"]) . "";
   $arr["name"]=strlen($arr["name"])>70?(substr($arr["name"],0,35)."..."):$name1; 

	print("Редактирование комментария к \"" . htmlspecialchars($arr["name"]) . "\"");
?>
	</td>
	</tr>
	<tr>
	<td>
<?
	textbbcode("comment","text",htmlspecialchars($arr["text"]));
?>
	</td></tr></table>

<?

	print("<p><input type=\"submit\" value=\"Отредактировать\" /></p></form>\n");

	stdfoot();
	die;
}



//if ($torrents[0] > 0)
//stderr($tracker_lang['error'], "Неверный идентификатор $tid.");
elseif ($action == "delete")
{
	if (get_user_class() < UC_MODERATOR)
		stderr($tracker_lang['error'], $tracker_lang['access_denied']);

  $commentid = (int) $_GET["cid"];

  if (!is_valid_id($commentid))
		stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

  $sure = $_GET["sure"];

  if (!$sure)  {
		stderr($tracker_lang['delete']." ".$tracker_lang['comment'], sprintf($tracker_lang['you_want_to_delete_x_click_here'],$tracker_lang['comment'],"?action=delete&cid=$commentid&sure=1"));
  }


	$res = sql_query("SELECT user, torrent,
	(SELECT username FROM users WHERE id=comments.user) AS classusername,
	(SELECT name FROM torrents WHERE id=comments.torrent) AS name_torrent
	 	
	FROM comments WHERE id=" .sqlesc($commentid) . "")  or sqlerr(__FILE__,__LINE__);
	$arr = mysql_fetch_array($res);
	if ($arr)
		$torrentid = $arr["torrent"];
	
$ip_user2 = $CURUSER["ip"];
$user2 = $CURUSER["username"];
$user_color2 = get_user_rgbcolor($CURUSER["class"], $CURUSER[username]);
write_log("$user2 удалил коммент $arr[classusername] ($commentid) в торренте $arr[name_torrent] ($torrentid)\n", "$user_color2","comment");



	sql_query("DELETE FROM comments WHERE id=$commentid") or sqlerr(__FILE__,__LINE__);
	
	@unlink(ROOT_PATH."cache/block-comment.txt");
	
	if ($torrentid && mysql_affected_rows() > 0)
		sql_query("UPDATE torrents SET comments = comments - 1 WHERE id = $torrentid");

	list($commentid) = mysql_fetch_row(sql_query("SELECT id FROM comments WHERE torrent = " .sqlesc($torrentid) . " ORDER BY added DESC LIMIT 1"));
//die($torrentid);
    if ($torrentid && $commentid){
	$returnto = "details.php?id=$torrentid&amp;viewcomm=$commentid#comm$commentid";
} elseif ($torrentid && !$commentid) {
	$returnto = "details.php?id=$torrentid";}
	
	if ($returnto)
	  header("Location: $returnto");
	else
	  header("Location: $DEFAULTBASEURL/");      // change later ----------------------
	die;
}
/////////////////СЛЕЖЕНИЕ ЗА КОММЕНТАМИ///////////////// 
elseif ($action == "check" || $action == "checkoff")
{
$tid = (int) $_GET["tid"];
if (!is_valid_id($tid))
                stderr($tracker_lang['error'], "Неверный идентификатор $tid.");
                
@$docheck = mysql_fetch_array(sql_query("SELECT COUNT(*) FROM checkcomm WHERE checkid = " .sqlesc($tid ) . " AND userid = " .sqlesc($CURUSER["id"]) . "AND torrent = 1"));

$from=getenv("HTTP_REFERER"); 
$host=getenv("REQUEST_URI"); 

if ($docheck[0] > 0 && $action=="check") {

if ($from){
@header("Refresh: 3; url=$from")// or die("Перенаправление <script>setTimeout('document.location.href=\"$from\"', 10);</script>")
;
} else {
@header("Refresh: 3; url=details.php?id=$tid") //or die("Перенаправление <script>setTimeout('document.location.href=\"details.php?id=$tid\"', 10);</script>")
;
}
         //stderr("Ошибка связи", " ");
         
         bark($tracker_lang['torrent']." \"\" <p>Вы уже подписаны на этот торрент.</p> <a href=\"$from\">Назад</a> или к <a href=details.php?id=$tid>Торренту</a>",false);


       // @header("Refresh: 3; url=details.php?id=$tid");
	//	die;
		}
if ($action == "check") {
		sql_query("INSERT INTO checkcomm (checkid, userid, torrent) VALUES (" .sqlesc($tid) . ", " .sqlesc($CURUSER["id"]) . ", 1)") or sqlerr(__FILE__,__LINE__);
		
	//	stderr($tracker_lang['success'], "<p>Теперь вы следите за комментариями к этому торренту.</p><a href=\"$from\">Назад</a> или к <a href=\"details.php?id=$tid\">Торренту</a>");
if ($from){
@header("Refresh: 3; url=$from") //or die("Перенаправление <script>setTimeout('document.location.href=\"$from\"', 10);</script>")
;
} else {
@header("Refresh: 3; url=details.php?id=$tid")// or die("Перенаправление <script>setTimeout('document.location.href=\"details.php?id=$tid\"', 10);</script>")
;
}
		bark("<p>Теперь вы следите за комментариями к этому торренту.</p><a href=\"$from\">Назад</a> или к <a href=\"details.php?id=$tid\">Торренту</a>",false);


	//	@header("Refresh: 3; url=details.php?id=$tid");
	//	die;
        }
        else 
		{
		sql_query("DELETE FROM checkcomm WHERE checkid = " .sqlesc($tid) . " AND userid = " .sqlesc($CURUSER["id"]) . " AND torrent = 1") or sqlerr(__FILE__,__LINE__);
		 
if ($from) {
@header("Refresh: 3; url=$from") //or die("Перенаправление <script>setTimeout('document.location.href=\"$from\"', 10);</script>")
;
} else {
@header("Refresh: 3; url=details.php?id=$tid") or die("Перенаправление <script>setTimeout('document.location.href=\"details.php?id=$tid\"', 10);</script>");
} 
		bark("<p>Теперь вы не следите за комментариями к этому торренту.</p><a href=$DEFAULTBASEURL/details.php?id=$tid>Назад</a>",false);

        //   stderr($tracker_lang['success'], "<p>Теперь вы не следите за комментариями к этому торренту.</p><a href=$DEFAULTBASEURL/details.php?id=$tid>Назад</a>");
    //  @header("Refresh: 3; url=details.php?id=$tid");
	//	die;
        }

}
/////////////////СЛЕЖЕНИЕ ЗА КОММЕНТАМИ/////////////////
elseif ($action == "vieworiginal")
{
	if (get_user_class() < UC_MODERATOR)
		stderr($tracker_lang['error'], $tracker_lang['access_denied']);

  $commentid = (int) $_GET["cid"];

  if (!is_valid_id($commentid))
		stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

  $res = sql_query("SELECT c.*, t.name, t.id AS tid FROM comments AS c LEFT JOIN torrents AS t ON c.torrent = t.id WHERE c.id=" .sqlesc($commentid) . "") or sqlerr(__FILE__,__LINE__);
  $arr = mysql_fetch_array($res);
  if (!$arr)
  	stderr($tracker_lang['error'], "Неверный идентификатор $commentid.");

  stdhead("Просмотр оригинала");
  $name1 = "". format_comment($arr["name"]) . "";
$arr["name"]=strlen($arr["name"])>70?(substr($arr["name"],0,40)."..."):$name1; 

  print("<h1>Оригинальное содержание комментария № $commentid</h1><p>
  <h1>к файлу " . $arr["name"] . "\"</h1><p>\n");
  
	print("<table width=500 border=1 cellspacing=0 cellpadding=5>");
  print("<tr><td class=comment>\n");
	echo htmlspecialchars_uni($arr["ori_text"]);
  print("</td></tr></table>\n");

  $returnto = "$DEFAULTBASEURL/details.php?id={$arr["tid"]}&amp;viewcomm=$commentid#comm$commentid";

//	$returnto = "details.php?id=$torrentid&amp;viewcomm=$commentid#$commentid";

	if ($returnto)
 		print("<p><font size=small><a href=$returnto>Назад</a></font></p>\n");

	stdfoot();
	die;
}
else
	stderr($tracker_lang['error'], "Неизвесная комманда");

die;
?>