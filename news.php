<?
require "include/bittorrent.php";

dbconn();
loggedinorreturn();

if (get_user_class() < UC_ADMINISTRATOR)
	stderr($tracker_lang['error'], "������ ��� �������������.");

$action = htmlentities($_GET["action"]);

//   Delete News Item    //////////////////////////////////////////////////////

if ($action == 'delete')
{
	$newsid = (int)$_GET["newsid"];
  if (!is_valid_id($newsid))
  	stderr($tracker_lang['error'],"Invalid news item ID - Code 1.");

  $returnto = htmlspecialchars($_GET["returnto"]);

  $sure = $_GET["sure"];
  if (!$sure)
    stderr("������� �������","�� ������������� ������ ������� ��� �������? �������\n" .
    	"<a href=?action=delete&newsid=$newsid&returnto=$returnto&sure=1>����</a> ���� �� �������.");


  $res2 = sql_query("SELECT * FROM news WHERE id=".sqlesc($newsid)."") or sqlerr(__FILE__, __LINE__);
  $ip2 = mysql_fetch_array($res2);
  $question=format_comment($ip2["subject"]);

		$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER[username]);
		
		/// ����� � ���
		write_log("$CURUSER[username] ������ ������� $question ($newsid)\n", "$user_color","other");
		

  sql_query("DELETE FROM news WHERE id=".sqlesc($newsid)."") or sqlerr(__FILE__, __LINE__);
  sql_query("DELETE FROM comments WHERE torrent=0 and poll=0 and offer=0 and news=".sqlesc($newsid)."") or sqlerr(__FILE__, __LINE__);
  
@unlink(ROOT_PATH."cache/block-news_admins.txt");
//@unlink("cache/block-news_guest.txt"); // ����� ���� �������
@unlink(ROOT_PATH."cache/block-news_users.txt");
  
	if ($returnto != "")
		header("Location: $returnto");
	else
		$warning = "������� <b>�������</b> �������";
}

//   Add News Item    /////////////////////////////////////////////////////////

if ($action == 'add')
{

	$subject = $_POST["subject"];
	if (!$subject)
		stderr($tracker_lang['error'],"���� ������� �� ����� ���� ������!");

	$body = $_POST["body"];
	if (!$body)
		stderr($tracker_lang['error'],"���� ������� �� ����� ���� ������!");

	$added = $_POST["added"];
	if (!$added)
		$added = sqlesc(get_date_time());

  sql_query("INSERT INTO news (userid, added, body, subject) VALUES (".
  	$CURUSER['id'] . ", $added, " . sqlesc($body) . ", " . sqlesc($subject) . ")") or sqlerr(__FILE__, __LINE__);
	if (mysql_affected_rows() == 1)
	{	
		$warning = "������� <b>������� ���������</b>";
		@unlink(ROOT_PATH."cache/block-news_admins.txt");
        @unlink(ROOT_PATH."cache/block-news_guest.txt");
        @unlink(ROOT_PATH."cache/block-news_users.txt");
    }
	else
		stderr($tracker_lang['error'],"������-��� ��������� ���-�� ����������.");
}

//   Edit News Item    ////////////////////////////////////////////////////////

if ($action == 'edit')
{

	$newsid = (int)$_GET["newsid"];

  if (!is_valid_id($newsid))
  	stderr($tracker_lang['error'],"Invalid news item ID - Code 2.");

  $res = sql_query("SELECT * FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) != 1)
	  stderr($tracker_lang['error'], "��� ������� � ����� ID - $newsid.");

	$arr = mysql_fetch_array($res);

  if ($_SERVER['REQUEST_METHOD'] == 'POST')
  {
  	$body = $_POST['body'];
  	$subject = $_POST['subject'];


	$subject = $_POST["subject"];
	if ($subject == "")
		stderr($tracker_lang['error'],"���� ������� �� ����� ���� ������!");

    if ($body == "")
    	stderr($tracker_lang['error'], "���� ������� �� ����� ���� ������!");

    $body = sqlesc($body);

    $subject = sqlesc($subject);

    $editedat = sqlesc(get_date_time());
    $editby = sqlesc($CURUSER[id]);


    sql_query("UPDATE news SET body=$body, editby=$editby, edittime=$editedat, subject=$subject WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

    $returnto = htmlentities($_POST['returnto']);

		if ($returnto != "")
			header("Location: $returnto");
		else
			$warning = "������� <b>�������</b> ���������������";
			
			@unlink(ROOT_PATH."cache/block-news_admins.txt");
            @unlink(ROOT_PATH."cache/block-news_guest.txt"); // ����� ���� �������
            @unlink(ROOT_PATH."cache/block-news_users.txt");
  }
  else
  {
 	 	$returnto = htmlspecialchars($_GET['returnto']);
	  stdhead("�������������� �������");
	  print("<form method=post name=news action=?action=edit&newsid=$newsid>\n");
	  print("<table border=1 cellspacing=0 cellpadding=5>\n");
	  print("<tr><td class=colhead>�������������� �������<input type=hidden name=returnto value=$returnto></td></tr>\n");
	  print("<tr><td>����: <input type=text name=subject maxlength=70 size=50 value=\"" . htmlspecialchars($arr["subject"]) . "\"/></td></tr>");
	  print("<tr><td style='padding: 0px'>");
	  textbbcode("news","body",htmlspecialchars($arr["body"]));
	  //<textarea name=body cols=145 rows=5 style='border: 0px'>" . htmlspecialchars($arr["body"]) . 
	  print("</textarea></td></tr>\n");
	  print("<tr><td align=center><input type=submit value='���������������'></td></tr>\n");
	  print("</table>\n");
	  print("</form>\n");
	  stdfoot();
	  die;
  }
}

//   Other Actions and followup    ////////////////////////////////////////////

stdhead("�������");
if ($warning)
	print("<p><font size=-3>($warning)</font></p>");
print("<form method=post name=news action=?action=add>\n");
print("<table border=1 cellspacing=0 cellpadding=5>\n");
print("<tr><td class=colhead>�������� �������</td></tr>\n");
print("<tr><td>����: <input type=text name=subject maxlength=40 size=50 value=\"" . htmlspecialchars($arr["subject"]) . "\"/></td></tr>");
print("<tr><td style='padding: 0px'>");
textbbcode("news","body","");
//<textarea name=body cols=145 rows=5 style='border: 0px'>
print("</textarea></td></tr>\n");
print("<tr><td align=center><input type=submit value='��������' class=btn></td></tr>\n");
print("</table></form><br /><br />\n");



$res = sql_query("SELECT *,  (SELECT username FROM users WHERE id=news.userid) AS user,
 (SELECT class FROM users WHERE id=news.userid) AS classuser 

 FROM news ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($res) > 0)
{


 	begin_main_frame();
	begin_frame();

	while ($arr = mysql_fetch_array($res))
	{
		$newsid = $arr["id"];
		$body = $arr["body"];
		$subject = $arr["subject"];
	  $userid = $arr["userid"];
	  $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " �����)";

  
    $postername = $arr["user"];
    $posterclass = $arr["classuser"];
 
    if ($postername == "")
    	$by = "���������� [$userid]";
    else
    	$by = "<a href=userdetails.php?id=$userid><b>
		".get_user_class_color($posterclass, $postername)."
		
		</b></a>";

	  print("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>");
    print("<b>���������</b>: $added&nbsp;-&nbsp;$by");
    print(" - <b>[</b><a href=?action=edit&newsid=$newsid>�������������</a><b>]</b>");
    print(" : <b>[</b><a href=?action=delete&newsid=$newsid>�������</a><b>]</b>");
    print("</td></tr></table></p>\n");

	  begin_table(true);
      print("<tr valign=top><td><b>".$subject."</b></td></tr>\n");
	  print("<tr valign=top><td class=comment>".format_comment($body)."</td></tr>\n");
	  end_table();
	}
	end_frame();
	end_main_frame();
}
else
  stdmsg("��������", "�������� ���!");
stdfoot();
die;
?>