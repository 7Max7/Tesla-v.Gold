<?

require "include/bittorrent.php";

gzip();

dbconn(false);

loggedinorreturn();

$userid = (int)$_GET["id"];

if (!is_valid_id($userid)) stderr($tracker_lang['error'], "Invalid ID");

if (get_user_class()< UC_POWER_USER || ($CURUSER["id"] != $userid && get_user_class() < UC_MODERATOR))
	stderr($tracker_lang['error'], "Нет доступа");

$page = (int) $_GET["page"];

$action = htmlentities($_GET["action"]);

//-------- Global variables

$perpage = 25;


//-------- Action: View comments torrent

if ($action == "viewcomments")
{
	$select_is = "COUNT(*)";

	// LEFT due to orphan comments
	$from_is = "comments AS c LEFT JOIN torrents as t ON c.torrent = t.id";

	$where_is = "c.user = $userid";
	$order_is = "c.id DESC";

	$query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is";

	$res = sql_query($query) or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_row($res) or stderr($tracker_lang['error'], "Комментарии не найдены");

	$commentcount = $arr[0];

	//------ Make page menu

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $commentcount, $_SERVER["PHP_SELF"] . "?action=viewcomments&id=$userid&");

	//------ Get user data

	$res = sql_query("SELECT * FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 1)
	{
		$arr = mysql_fetch_assoc($res);

	  $subject = "<a href=userdetails.php?id=$userid>".get_user_class_color($arr["class"],$arr["username"])."</a>" . get_user_icons($arr, true);
	}
	else
	  $subject = "unknown[$userid]";

	//------ Get comments

	$select_is = "t.name, c.torrent AS t_id, c.id, c.added, c.text";

	$query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is $limit";

	$res = sql_query($query) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 0) stderr($tracker_lang['error'], "Комментарии не найдены");

	stdhead("История комментариев");

	print("<h1>История комментариев для $subject</h1>
	<a href=\"userhistory.php?action=viewnewscomment&id=$userid\"><b>Новости</b></a> | <a href=\"userhistory.php?action=viewpollscomment&id=$userid\"><b>Опросы</b></a>
	\n");

	if ($commentcount > $perpage) echo $pagertop;

	//------ Print table

	begin_main_frame();

	begin_frame();

	while ($arr = mysql_fetch_assoc($res))
	{

		$commentid = $arr["id"];

	  $torrent = $arr["name"];

    // make sure the line doesn't wrap
	  if (strlen($torrent) > 55) $torrent = substr($torrent,0,52) . "...";

	  $torrentid = $arr["t_id"];

	  //find the page; this code should probably be in details.php instead

	  $subres = sql_query("SELECT COUNT(*) FROM comments WHERE torrent = $torrentid AND id < $commentid")
	  	or sqlerr(__FILE__, __LINE__);
	  $subrow = mysql_fetch_row($subres);
    $count = $subrow[0];
    $comm_page = floor($count/20);
    $page_url = $comm_page?"&page=$comm_page":"";

	  $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " назад)";

	  print("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
	  <b>Комментарий:&nbsp;</b>#<a href=details.php?id=$torrentid&viewcomm=$commentid#comm$commentid>$commentid</a>
	  <b>Добавлен</b>: ". "$added <b> Торрент: </b>".
	  ($torrent?("<a href=details.php?id=$torrentid>$torrent</a>"):" [Удален] "). "</td></tr></table></p>\n");

	  begin_table(true);

	  $body = format_comment($arr["text"]);

	  print("<tr valign=top><td class=comment>$body</td></tr>\n");

	  end_table();
	}

	end_frame();

	end_main_frame();

	if ($commentcount > $perpage) echo $pagerbottom;

	stdfoot();

	die;
}

//-------- Action: View comments news

if ($action == "viewnewscomment")
{
	$query = "SELECT COUNT(*) FROM comments WHERE user = $userid and torrent=0 and poll=0 and offer=0 ORDER BY id DESC";
	$res = sql_query($query) or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_row($res) or stderr($tracker_lang['error'], "Комментарии к новостям не найдены");
	$commentcount = $arr[0];
	list($pagertop, $pagerbottom, $limit) = pager($perpage, $commentcount, $_SERVER["PHP_SELF"] . "?action=viewnewscomment&id=$userid&");

	//------ Get user data
	$res = sql_query("SELECT * FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 1)
	{
	$arr = mysql_fetch_assoc($res);
	$subject = "<a href=userdetails.php?id=$userid>".get_user_class_color($arr["class"],$arr["username"])."</a>" . get_user_icons($arr, true);
	}
	//------ Get comments
	$query = "SELECT *,
	(SELECT subject FROM news WHERE news=comments.news  LIMIT 1) AS news_subject,
		(SELECT id FROM news WHERE news=comments.news  LIMIT 1) AS news_id
	 FROM comments WHERE user = $userid  and torrent=0 and poll=0 and offer=0 ORDER BY id $limit";
	$res = sql_query($query) or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) == 0) stderr($tracker_lang['error'], "Комментарии к новостям не найдены");

	stdhead("История новостных комментариев");
	print("<h1>История новостных комментариев для $subject</h1>
	<a href=\"userhistory.php?action=viewcomments&id=$userid\"><b>Торрент</b></a> | <a href=\"userhistory.php?action=viewpollscomment&id=$userid\"><b>Опросы</b></a>
	
	\n");

	if ($commentcount > $perpage) echo $pagertop;
	begin_main_frame();
	begin_frame();
	while ($arr = mysql_fetch_assoc($res))
	{
	$commentid = $arr["id"];
    $torrent = $arr["name"];
    if (strlen($torrent) > 55) $torrent = substr($torrent,0,52) . "...";
    $torrentid = $arr["t_id"];
	$subres = sql_query("SELECT COUNT(*) FROM comments WHERE id < $commentid and torrent=0 and poll=0 and offer=0") or sqlerr(__FILE__, __LINE__);
	$subrow = mysql_fetch_row($subres);
    $count = $subrow[0];
    $comm_page = floor($count/20);
    $page_url = $comm_page?"&page=$comm_page":"";

	  $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " назад)";

	  print("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
	  <b>Комментарий: </b>#$commentid <b>Добавлен</b>: $added <b>Новость</b>: <a href=\"newsoverview.php?id=$arr[news_id]\">".format_comment($arr[news_subject])."</a>
	  </td></tr></table></p>\n");

	  begin_table(true);
	  $body = format_comment($arr["text"]);
	  print("<tr valign=top><td class=comment>$body</td></tr>\n");
	  end_table();
	}
	end_frame();
	end_main_frame();

	if ($commentcount > $perpage) echo $pagerbottom;
	stdfoot();
	die;
}


//-------- Action: View comments news

if ($action == "viewpollscomment")
{
	$query = "SELECT COUNT(*) FROM comments WHERE torrent=0 and news=0 and offer=0 and user = $userid ORDER BY id DESC";
	$res = sql_query($query) or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_row($res) or stderr($tracker_lang['error'], "Комментарии к опросам не найдены");
	$commentcount = $arr[0];
	list($pagertop, $pagerbottom, $limit) = pager($perpage, $commentcount, $_SERVER["PHP_SELF"] . "?action=viewpollscomment&id=$userid&");

	//------ Get user data
	$res = sql_query("SELECT * FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 1)
	{
	$arr = mysql_fetch_assoc($res);
	$subject = "<a href=userdetails.php?id=$userid>".get_user_class_color($arr["class"],$arr["username"])."</a>" . get_user_icons($arr, true);
	}
	//------ Get comments
	$query = "SELECT *,
	(SELECT question FROM polls WHERE forum='0' AND id=comments.poll LIMIT 1) AS polls_subject,
		(SELECT id FROM polls WHERE forum='0' AND id=comments.poll LIMIT 1) AS polls_id
	 FROM comments WHERE torrent=0 and news=0 and offer=0 and user = $userid  ORDER BY id $limit";
	$res = sql_query($query) or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) == 0) stderr($tracker_lang['error'], "Комментарии к опросам не найдены");

	stdhead("История опросовых комментариев");
	print("<h1>История опросовых комментариев для $subject</h1>
	<a href=\"userhistory.php?action=viewcomments&id=$userid\"><b>Торрент</b></a> | <a href=\"userhistory.php?action=viewnewscomment&id=$userid\"><b>Новости</b></a>
	\n");

	if ($commentcount > $perpage) echo $pagertop;
	begin_main_frame();
	begin_frame();
	while ($arr = mysql_fetch_assoc($res))
	{
	$commentid = $arr["id"];
    $torrent = $arr["name"];
    if (strlen($torrent) > 55) $torrent = substr($torrent,0,52) . "...";
    $torrentid = $arr["t_id"];
	$subres = sql_query("SELECT COUNT(*) FROM comments WHERE torrent=0 and news=0 and offer=0 and id < $commentid") or sqlerr(__FILE__, __LINE__);
	$subrow = mysql_fetch_row($subres);
    $count = $subrow[0];
    $comm_page = floor($count/20);
    $page_url = $comm_page?"&page=$comm_page":"";

	  $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " назад)";

	  print("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
	  <b>Комментарий: </b>#$commentid <b>Добавлен</b>: $added <b>Опрос</b>: <a href=\"polloverview.php?id=$arr[polls_id]\">".format_comment($arr[polls_subject])."</a>
	  </td></tr></table></p>\n");

	  begin_table(true);
	  $body = format_comment($arr["text"]);
	  print("<tr valign=top><td class=comment>$body</td></tr>\n");
	  end_table();
	}
	end_frame();
	end_main_frame();

	if ($commentcount > $perpage) echo $pagerbottom;
	stdfoot();
	die;
}
//-------- Handle unknown action

if ($action != "")
	stderr($tracker_lang['error'], "Неизвестное действие.");

//-------- Any other case

stderr($tracker_lang['error'], "Неверный или отсутствующий запрос.");

?>