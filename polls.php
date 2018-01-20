<?
  require "include/bittorrent.php";
  dbconn(false);
  loggedinorreturn();

$action = htmlentities($_GET["action"]);

  $pollid = (int) $_GET["pollid"];
  $returnto = htmlspecialchars($_GET["returnto"]);

  if ($action == "delete")
  {
  
  	if (get_user_class() < UC_MODERATOR)
  		stderr($tracker_lang['error'], "Доступ Запрещен");
  		
   	if (!is_valid_id($pollid))
			stderr($tracker_lang['error'], "Invalid ID.");
  
 	$sure = htmlspecialchars($_GET["sure"]);
  	if (!$sure)
 	stderr("Удалить опрос","Вы действительно хотите удалить опрос? Нажмите\n" . "<a href=?action=delete&pollid=$pollid&returnto=$returnto&sure=1>сюда</a> если вы уверены.");

	 
  $res2 = sql_query("SELECT * FROM polls WHERE forum='0' AND id=".sqlesc($pollid)."") or sqlerr(__FILE__, __LINE__);
  $ip2 = mysql_fetch_array($res2);
  $question=format_comment($ip2["question"]);
  
		sql_query("DELETE FROM pollanswers WHERE pollid = ".sqlesc($pollid)."") or sqlerr(__FILE__, __LINE__);
		sql_query("DELETE FROM polls WHERE forum='0' AND id = ".sqlesc($pollid)."") or sqlerr(__FILE__, __LINE__);
		sql_query("DELETE FROM comments WHERE torrent=0 and news=0 and offer=0 and poll = ".sqlesc($pollid)."") or sqlerr(__FILE__, __LINE__);
		$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER["username"]);
		
		/// пишем в лог
		write_log("$CURUSER[username] удалил опрос $question ($pollid)\n", "$user_color","other");
		
		
		if ($returnto == "main")
			header("Location: $DEFAULTBASEURL");
		else
			header("Location: $DEFAULTBASEURL/polls.php?deleted=1");
		die;
  }

  $rows = sql_query("SELECT COUNT(*) FROM polls WHERE forum='0'") or sqlerr(__FILE__, __LINE__);
  $row = mysql_fetch_row($rows);
  $pollcount = $row[0];
  
  if ($pollcount == 0)
  	stderr("Извините", "Нет опросов!");
  	
  $polls = sql_query("SELECT *,	 
  (SELECT username FROM users WHERE id=polls.createby) AS createuser,
 (SELECT username FROM users WHERE id=polls.editby) AS edituser FROM polls WHERE forum='0' ORDER BY id DESC LIMIT 1," . ($pollcount - 1 )) or sqlerr(__FILE__, __LINE__);
  stdhead("Прошлые опросы");
  print("<h1>Прошлые опросы</h1>");

    function srt($a,$b)
    {
      if ($a[0] > $b[0]) return -1;
      if ($a[0] < $b[0]) return 1;
      return 0;
    }

  while ($poll = mysql_fetch_assoc($polls))
  {
  /*  $o = array($poll["option0"], $poll["option1"], $poll["option2"], $poll["option3"], $poll["option4"],
    $poll["option5"], $poll["option6"], $poll["option7"], $poll["option8"], $poll["option9"],
    $poll["option10"], $poll["option11"], $poll["option12"], $poll["option13"], $poll["option14"],
    $poll["option15"], $poll["option16"], $poll["option17"], $poll["option18"], $poll["option19"]);
*/
$o = array(format_comment($poll["option0"]), format_comment($poll["option1"]), format_comment($poll["option2"]), format_comment($poll["option3"]), format_comment($poll["option4"]),
    format_comment($poll["option5"]), format_comment($poll["option6"]), format_comment($poll["option7"]), format_comment($poll["option8"]), format_comment($poll["option9"]),
    format_comment($poll["option10"]), format_comment($poll["option11"]), format_comment($poll["option12"]), format_comment($poll["option13"]), format_comment($poll["option14"]),
    format_comment($poll["option15"]), format_comment($poll["option16"]), format_comment($poll["option17"]), format_comment($poll["option18"]), format_comment($poll["option19"]));

if ($poll["edituser"]) {
$b="<b>[</b>Отредактировал $poll[edituser] в $poll[edittime]<b>]</b>";
}
else
$b="";
if ($poll["createuser"]) {
$a="<b>[</b>Создал $poll[createuser] в $poll[added]<b>]</b>";
}
else
$a="";

    print("<p><table width=100% border=1 cellspacing=0 cellpadding=10><tr><td align=center>\n");

    print("<p class=sub>");
    $added = date("Y-m-d",strtotime($poll['added'])) . " (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($poll["added"]))) . " назад)";

    print("$added");

    if (get_user_class() >= UC_ADMINISTRATOR)
    {
    	print(" - [<a href=makepoll.php?action=edit&pollid=$poll[id]><b>Редактировать</b></a>]\n");
			print(" - [<a href=?action=delete&pollid=$poll[id]><b>Удалить</b></a>]\n");
		}
//	print("<a name=$poll[id]>");

		print("<br>$a $b");

		print("</p>\n");

    print("<table class=main border=1 cellspacing=0 cellpadding=5><tr><td class=text>\n");

   // print("<p align=center><b>" . $poll["question"] . "</b></p>");
    print("<p align=center><b>" . format_comment($poll["question"]) . "</b></p>");

    $pollanswers = sql_query("SELECT selection FROM pollanswers WHERE pollid=" . $poll["id"] . " AND  selection < 20") or sqlerr(__FILE__, __LINE__);

    $tvotes = mysql_num_rows($pollanswers);

    $vs = array(); // count for each option ([0]..[19])
    $os = array(); // votes and options: array(array(123, "Option 1"), array(45, "Option 2"))

    // Count votes
    while ($pollanswer = mysql_fetch_row($pollanswers))
      $vs[$pollanswer[0]] += 1;

    reset($o);
    for ($i = 0; $i < count($o); ++$i)
      if ($o[$i])
        $os[$i] = array($vs[$i], $o[$i]);

    // now os is an array like this:
    if ($poll["sort"] == "yes")
    	usort($os, srt);

    print("<table width=100% class=main border=0 cellspacing=0 cellpadding=0>\n");
    $i = 0;
    while ($a = $os[$i])
    {
	  	if ($tvotes > 0)
	  		$p = round($a[0] / $tvotes * 100);
	  	else
				$p = 0;
      if ($i % 2)
        $c = "";
      else
        $c = " bgcolor=#ECE9D8";
      print("<tr><td class=embedded$c>" . $a[1] . "&nbsp;&nbsp;</td><td class=embedded$c>" .
        "<img src=pic/bar_left.gif><img src=pic/bar.gif height=9 width=" . ($p * 3) . "><img src=pic/bar_right.gif> $p%</td></tr>\n");
      ++$i;
    }
    print("</table>\n");
	$tvotes = number_format($tvotes);
    print("<p align=center>Голосов: $tvotes</p>\n");

    print("</td></tr></table>\n");

    print("</td></tr></table></p>\n");

  }

  stdfoot();
?>