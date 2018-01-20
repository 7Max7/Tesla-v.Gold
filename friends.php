<?

require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

$userid = (int)$_GET['id'];
$action = $_GET['action'];

if (!$userid)
	$userid = $CURUSER['id'];

if (!is_valid_id($userid))
	stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

if ($userid <> $CURUSER["id"])
	stderr($tracker_lang['error'], $tracker_lang['access_denied']);

$res = sql_query("SELECT * FROM users WHERE id=".sqlesc($userid)."") or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_array($res) or stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

$dt = gmtime() - 300;
$dt = sqlesc(get_date_time($dt));

// action: add -------------------------------------------------------------

if ($action == 'add')
{
	$targetid = (int)$_GET['targetid'];
	$type = $_GET['type'];

  if (!is_valid_id($targetid))
		stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

  if ($type == 'friend')
  {
  	//$table_is = $frag = 'friends';
    //$field_is = 'friendid';
    $blocks=0;
    $list='Друзей';
  }
	elseif ($type == 'block')
  {
	//$table_is = $frag = 'blocks';
    //$field_is = 'blockid';
    $blocks=1;
    $list='Врагов';
  }
	else
  stderr($tracker_lang['error'], "Unknown type.");

  $r = sql_query("SELECT id FROM friends WHERE userid=".sqlesc($userid)." AND friendid=".sqlesc($targetid)." AND blocks=".sqlesc($blocks)."") or sqlerr(__FILE__, __LINE__);
  if (mysql_num_rows($r) == 1)
  stderr($tracker_lang['error'], "Данный пользователь уже существует в списке ".$list.".");

  sql_query("INSERT INTO friends VALUES (0,".sqlesc($userid).", ".sqlesc($targetid).", $blocks)") or sqlerr(__FILE__, __LINE__);
  
  $from=getenv("HTTP_REFERER"); 
  if (!$from)
  $from="friends.php";
  header("Location: $from");
die;
}

// action: delete ----------------------------------------------------------

if ($action == 'delete')
{
	$targetid = (int)$_GET['targetid'];
	//$sure = htmlentities($_GET['sure']);
	$type = htmlentities($_GET['type']);

  if (!is_valid_id($targetid))
  stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

  //if ($type == 'friend')

 // if (!$sure)
   // stderr($tracker_lang['delete']." ".($type == 'friend'?$tracker_lang['friend']:$tracker_lang['block']),sprintf($tracker_lang['you_want_to_delete_x_click_here'],($type == 'friend'?$tracker_lang['friend']:$tracker_lang['block']),"?id=$userid&action=delete&type=$type&targetid=$targetid&sure=1"));

  if ($type == 'friend')
  {
  	$blocks='0';
    sql_query("DELETE FROM friends WHERE userid=".sqlesc($userid)." AND friendid=".sqlesc($targetid)." and blocks=$blocks") or sqlerr(__FILE__, __LINE__);
    if (mysql_affected_rows() == 0)
      stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
    $frag = "friends";
  }
  elseif ($type == 'block')
  {
  	  $blocks='1';
    sql_query("DELETE FROM friends WHERE userid=".sqlesc($userid)." AND friendid=".sqlesc($targetid)." and blocks=$blocks") or sqlerr(__FILE__, __LINE__);
    if (mysql_affected_rows() == 0)
      stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
    $frag = "blocks";
  }
  else
  stderr($tracker_lang['error'], "Неизвестное действие.");

  $from=getenv("HTTP_REFERER"); 
  if (!$from)
  $from="friends.php";
  header("Location: $from");
  die;
  
  
  /*
  ALTER TABLE `tesla`.`friends` DROP INDEX `userfriend` ,
ADD UNIQUE `userfriend` ( `userid` , `friendid` , `blocks` ) 
  
  
  */
  
}


// main body  -----------------------------------------------------------------

stdhead("Мои списки Друзей | Врагов");

/*print("<p><table class=main border=0 cellspacing=0 cellpadding=0>".
"<tr><td class=embedded><h1 style='margin:0px'> Personal lists for $user[username]</h1>$donor$warned$country</td></tr></table></p>\n");*/

print("<table class=main width=100% border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>");

print("<table width=100% border=1 cellspacing=0 cellpadding=5>");
print("<tr><td class=\"rowhead\">".$tracker_lang['friends_list']."</tr></td>");
print("<tr><td class=\"a\">");
$i = 0;

$res = sql_query("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=".sqlesc($userid)." and blocks=0 ORDER BY name") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) == 0)
	$friends = "<em>".$tracker_lang['no_friends'].".</em>";
else
	while ($friend = mysql_fetch_array($res))
	{
    $title = $friend["title"];
		if (!$title)
	    $title = get_user_class_name($friend["class"]);
    $body1 = "<a href=userdetails.php?id=" . $friend['id'] . "><b>" . get_user_class_color($friend["class"], $friend['name']) . "</b></a>" .
    	get_user_icons($friend) . " ($title)<br /><br />" . $tracker_lang['last_seen'] . $friend['last_access'] .
    	"<br /><b>[</b>" . get_elapsed_time(sql_timestamp_to_unix_timestamp($friend[last_access])) . " ".$tracker_lang['ago']."<b>]</b>";
		$body2 = "<br /><b>[</b><font class=small><a href=friends.php?id=$userid&action=delete&type=friend&targetid=" . $friend['id'] . ">".$tracker_lang['delete']."</a></font><b>]</b>" .
			"<br /><b>[</b><a href=message.php?action=sendmessage&amp;receiver=" . $friend['id'] . "><font class=small>Личка</font></a><b>]</b>";
	
	
	if (!file_exists("pic/avatar/$friend[avatar]")){
	$updateset[] = "avatar = ''";
sql_query("UPDATE users SET " . implode(", ", $updateset) . " WHERE id = '$friend[id]'") or sqlerr(__FILE__, __LINE__);

	//	@unlink("pic/avatar/".htmlspecialchars($friend["avatar"]));
    //	$avatar=false;

	$avatar = "pic/default_avatar.gif";
	}
	elseif ($friend["avatar"])
    $avatar = "pic/avatar/".$friend["avatar"];
	else
    $avatar = "pic/default_avatar.gif";
    
    if ($i % 2 == 0)
    	print("<table width=100% style='padding: 0px'><tr><td class=bottom style='padding: 5px' width=50% align=center>");
    else
    	print("<td class=bottom style='padding: 5px' width=50% align=center>");
    print("<table class=main width=100% height=100px>");
    print("<tr valign=top><td width=100 align=center style='padding: 0px'>" .
			($avatar ? "<div style='width:100px;height:100px;overflow: hidden'><img width=\"100\" src=\"$avatar\" /></div>" : ""). "</td><td>\n");
    print("<table class=main>");
    print("<tr><td class=embedded style='padding: 5px' width=80%>$body1</td>\n");
    print("<td class=embedded style='padding: 5px' width=20%>$body2</td></tr>\n");
    print("</table>");
		print("</td></tr>");
		print("</td></tr></table>\n");
    if ($i % 2 == 1)
			print("</td></tr></table>\n");
		else
			print("</td>\n");
		$i++;
	}
if ($i % 2 == 1)
	print("<td class=bottom width=50%>&nbsp;</td></tr></table>\n");
print($friends);
print("</td></tr></table>\n");

$res = sql_query("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=".sqlesc($userid)." and blocks=1 ORDER BY name") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) == 0)
	$blocks = "<em>".$tracker_lang['no_blocked'].".</em>";
	
else
{
	$i = 0;
	$blocks = "<table width=100% cellspacing=0 cellpadding=0>";
	while ($block = mysql_fetch_array($res))
	{
		if ($i % 6 == 0)
			$blocks .= "<tr>";
    	$blocks .= "<td style='border: none; padding: 4px; spacing: 0px;'>
		".("'".$block['last_access']."'">$dt?"<img src=pic/button_online.gif border=0 alt=\"В сети\">":"<img src=pic/button_offline.gif border=0 alt=\"Не в сети\">" )."
		<a href=userdetails.php?id=" . $block['id'] . "&sure=1><b>" . get_user_class_color($block['class'], $block['name']) . "</b></a> [<font class=small><a href=friends.php?id=$userid&action=delete&type=block&targetid=" .$block['id']. ">Снять игнор</a></font>]" .get_user_icons($block) . "</td>";
		if ($i % 6 == 5)
			$blocks .= "</tr>";
		$i++;
	}
	print("</table>\n");
}

print("<br />");
print("<table class=main width=100% border=0 cellspacing=0 cellpadding=5>");
print("<tr><td class=\"rowhead\">".$tracker_lang['blocked_list']."</td></tr>");
print("<tr><td class=\"a\">");
print("$blocks\n");
print("</td></tr></table>\n");
print("</td></tr></table>\n");



print("<br />");

print("<table class=main width=100% border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>");

print("<table width=100% border=1 cellspacing=0 cellpadding=5>");
print("<tr><td class=\"rowhead\">В друзьях у: </tr></td>");
//print("<tr><td class=\"a\">");

$res2= sql_query("SELECT f.userid as id, u.username AS name, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.userid = u.id WHERE friendid=".sqlesc($userid)." and blocks=0 ORDER BY name") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res2) == 0)
	$on_fr = "<table width=100% cellspacing=0 cellpadding=0>
	<tr><td class=\"a\"><em>Нет никого</em>
</td></tr>
	</table>";
else
{


	$i = 0;
	$on_fr = "<table width=100% cellspacing=0 cellpadding=0>";
	while ($on_friend = mysql_fetch_array($res2))
	{
		if ($i % 6 == 0)
			$on_fr.= "<tr>";
    	$on_fr .= "<td class=b style='border: none; padding: 4px; spacing: 0px;'>
		".("'".$on_friend['last_access']."'">$dt?"<img src=pic/button_online.gif border=0 alt=\"В сети\">":"<img src=pic/button_offline.gif border=0 alt=\"Не в сети\">" )."
		<a href=userdetails.php?id=" . $on_friend['id'] . "&sure=1><b>" . get_user_class_color($on_friend['class'], $on_friend['name']) . "</b></a>" .get_user_icons($on_friend) . "
		</td>";
		if ($i % 6 == 5)
			$on_fr .= "</tr>";
		$i++;
	}
	print("</table>\n");
}
if ($i % 2 == 1)
print("</tr></table>\n");
print($on_fr);
print("</td></tr></table>\n");




if (get_user_class() > UC_MODERATOR){
print("<br />");
print("<table class=main width=100% border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>");
print("<table width=100% border=1 cellspacing=0 cellpadding=5>");
print("<tr><td class=\"rowhead\">В списках игнора у: [только для администрации]</tr></td>");
//print("<tr><td class=\"a\">");

$res2= sql_query("SELECT f.userid as id, u.username AS name, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.userid = u.id WHERE friendid=".sqlesc($userid)." and blocks=1 ORDER BY name") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res2) == 0)
	$on_fr = "<table width=100% cellspacing=0 cellpadding=0><tr><td class=b style='border: none; padding: 4px; spacing: 0px;'><em>Нет никого</em>\n";
else
{


	$i = 0;
	$on_fr = "<table width=100% cellspacing=0 cellpadding=0>";
	while ($on_friend = mysql_fetch_array($res2))
	{
		if ($i % 6 == 0)
			$on_fr.= "<tr>";
    	$on_fr .= "<td class=b style='border: none; padding: 4px; spacing: 0px;'>
		".("'".$on_friend['last_access']."'">$dt?"<img src=pic/button_online.gif border=0 alt=\"В сети\">":"<img src=pic/button_offline.gif border=0 alt=\"Не в сети\">" )."
		<a href=userdetails.php?id=" . $on_friend['id'] . "&sure=1><b>" . get_user_class_color($on_friend['class'], $on_friend['name']) . "</b></a>" .get_user_icons($on_friend) . "
		</td>";
		if ($i % 6 == 5)
			$on_fr .= "</tr>";
		$i++;
	}
	print("</table>\n");
}
if ($i % 2 == 1)
print("</tr></table>\n");
print($on_fr);
print("</td></tr></table>\n");
print("</td></tr></table>\n");
}

//print("<p align=\"right\"><a href=users.php><b>К полному списку пользователей</b></a></p>");
stdfoot();
?>