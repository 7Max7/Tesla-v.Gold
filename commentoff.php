<?

function bark($msg, $error = true) {
global $tracker_lang;
stdhead(($error ? $tracker_lang['error'] : $tracker_lang['torrent']." ".$tracker_lang['bookmarked']));
  // stdmsg(($error ? $tracker_lang['error'] : $tracker_lang['success']), $msg, ($error ? 'error' : 'success'));
    stdmsg(($error ? "������" : "�������"), $msg, ($error ? 'error' : 'success'));
stdfoot();
exit;
}


require_once("include/bittorrent.php");

$action = htmlentities($_GET["action"]);
dbconn(false);
loggedinorreturn();

if ($action == "add")
{
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $offid = (int) $_POST["tid"];
                if (!is_valid_id($offid))
                    stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
                $res = sql_query("SELECT name FROM off_reqs WHERE id = $offid") or sqlerr(__FILE__,__LINE__);
                $arr = mysql_fetch_array($res);
                if (!$arr)
                    stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
                $name = $arr[0];
                $text = ( htmlspecialchars($_POST["msg"]));
                if (!$text)
                    stderr($tracker_lang['error'], "� ��� ������ �����");
                    
                    
                if (get_user_class() == UC_SYSOP){
                $sender_id = ($_POST['sender'] == 'system' ? 0 : $CURUSER['id']);
              }
              else
               $sender_id = $CURUSER['id'];
                    
                    
                sql_query("INSERT INTO comments (user, offer, added, text, ori_text, ip) VALUES (" .
                $CURUSER["id"] . ",$offid, '" . date("Y-m-d H:i:s", time()) . "', " . sqlesc($text) .
                "," . sqlesc($text) . "," . sqlesc(getip()) . ")");
                $newid = mysql_insert_id();
               
			   
			    sql_query("UPDATE off_reqs SET comments = comments + 1 WHERE id = $offid");
                /////////////////�������� �� ����������/////////////////
                
				$res3 = sql_query("SELECT * FROM checkcomm WHERE checkid = $offid AND offer = 1") or sqlerr(__FILE__,__LINE__);
                $subject = "����� �����������";
                while ($arr3 = mysql_fetch_array($res3)) {
                        $msg = sqlesc("� ������� [url=$DEFAULTBASEURL/detailsoff.php?id=$offid&viewcomm=$newid#comm$newid]".$name."[/url] ��� �������� ����� �����������.");
                        if ($CURUSER[id] <> $arr3[userid])
                                sql_query("INSERT INTO messages (sender, receiver, added, msg, location, subject) VALUES (0, $arr3[userid], NOW(), $msg, 1, '$subject')");
                }

     		//	$subject = sqlesc("����� �����������");
     	//		$msg = sqlesc("� ������� [url=$DEFAULTBASEURL/detailsoff.php?id=$offid&viewcomm=$newid#comm$newid]".$name."[/url] ��� �������� ����� �����������.");
     		//	sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) SELECT 0, userid, NOW(), $msg, 0, $subject FROM checkcomm WHERE checkid = $offid AND offer = 1 AND userid != $CURUSER[id]") or sqlerr(__FILE__,__LINE__);

                /////////////////�������� �� ����������/////////////////
                
                
                header("Location: detailsoff.php?id=$offid&viewcomm=$newid#comm$newid");
                exit();
        }
        $offid = (int) $_GET["tid"];
        if (!is_valid_id($offid))
            stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

        $res = sql_query("SELECT name FROM off_reqs WHERE id = $offid") or sqlerr(__FILE__,__LINE__);
        $arr = mysql_fetch_array($res);
        if (!$arr)
        stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

        stdhead("".$tracker_lang['add_comment']." � \"" . $arr["name"] . "\"");

        $name = (strlen($arr["name"])>40?substr($arr["name"],0,40)."...":$arr["name"]);
        print("<p><form name=\"Form\" method=\"post\" action=\"commentoff.php?action=add\">\n");
        print("<input type=\"hidden\" name=\"tid\" value=\"$offid\"/>\n");
        print("<p align=center><table border=1 cellspacing=\"0\" cellpadding=\"5\">\n");
        echo ("<tr><td class=colhead align=left>".$tracker_lang['add_comment']." � \"" . htmlspecialchars($name) . "\"</td><tr>\n");
        print("<tr><td align=center>\n");
        textbbcode("Form","msg","");
       // print("<p align=center><a href=tags.php target=_blank>��� ����</a>\n");
        print("</td></tr>\n");
        print("<tr><td align=center colspan=2><input type=submit value=\"��������\" class=btn></td></tr></form></table>\n");
        $res = sql_query("SELECT comments.id, text, comments.added, username, users.id as user, users.avatar, users.uploaded, users.downloaded, users.class, users.enabled, users.parked, users.warned, users.donor FROM comments LEFT JOIN users ON comments.user = users.id WHERE offer = $offid ORDER BY comments.id DESC LIMIT 5");
        $allrows = array();
        while ($row = mysql_fetch_array($res))
            $allrows[] = $row;

        if (count($allrows))
        {
                print("<h2>��������� ����������� � �������� �������.</h2>\n");
                commenttable($allrows);
        }
        stdfoot();
        die;
}
elseif ($action == "quote") {
        $commentid = (int) $_GET["cid"];
        if (!is_valid_id($commentid))
                stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
        $res = sql_query("SELECT c.*, o.name, o.id AS oid, u.username FROM comments AS c 
		JOIN off_reqs AS o ON c.offer = o.id JOIN users AS u ON c.user = u.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
        $arr = mysql_fetch_array($res);
        if (!$arr)
                stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
        $text = "[quote=$arr[username]]" . $arr["text"] . "[/quote]\n";
        $offid = $arr["oid"];
        stdhead("�������� ����������� � \"" . $arr["name"] . "\"");
        $name = (strlen($arr["name"])>40?substr($arr["name"],0,40)."...":$arr["name"]);
        print("<form name=form method=\"post\" action=\"commentoff.php?action=add\">\n");
        print("<input type=\"hidden\" name=\"tid\" value=\"$offid\" />\n");
        print("<table border=1 cellspacing=\"0\" cellpadding=\"5\">\n");
        echo ("<tr><td class=colhead align=left>������������� ����������� � \"" . htmlspecialchars($name) . "\"</td><tr>\n");
        print("<tr><td align=center>\n");
        textbbcode("form","msg",htmlspecialchars_uni($text));
       // print("<div align=center><a href=tags.php target=_blank>��� ����</a></div>\n");
        print("</td></tr>\n");
        print("<tr><td align=center colspan=2><input type=submit value=\"��������\"></td></tr></form></table>\n");
        stdfoot();
}
elseif ($action == "edit") {
        $commentid = (int) $_GET["cid"];
        if (!is_valid_id($commentid))
                stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
        $res = sql_query("SELECT c.*, o.name FROM comments AS c 
		JOIN off_reqs AS o ON c.offer = o.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
        $arr = mysql_fetch_array($res);
        if (!$arr) { 
        	//print"sdfs";
                stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
                }
        if ($arr["user"] != $CURUSER["id"] && get_user_class() < UC_MODERATOR)
                stderr($tracker_lang['error'], $tracker_lang['access_denied']);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $text = htmlspecialchars($_POST["msg"]);
                $returnto = htmlentities($_POST["returnto"]);
                if ($text == "")
                        stderr($tracker_lang['error'], $tracker_lang['no_fields_blank']);
                $text = sqlesc($text);
                $editedat = sqlesc(date("Y-m-d H:i:s", time()));;
                sql_query("UPDATE comments SET text=$text, editedat=$editedat, editedby=$CURUSER[id] WHERE id=$commentid") or sqlerr(__FILE__, __LINE__);
                if ($returnto)
                        header("Location: $returnto");
                else
                        header("Location: $DEFAULTBASEURL/"); // change later ----------------------
                die;
        }

        stdhead("������������� ����������� ������� � \"" . $arr["name"] . "\"");
        $name = (strlen($arr["name"])>40?substr($arr["name"],0,40)."...":$arr["name"]);
        print("<form name=Form method=\"post\" action=\"commentoff.php?action=edit&amp;cid=$commentid\">\n");
        print("<input type=\"hidden\" name=\"returnto\" value=\"" . $_SERVER["HTTP_REFERER"] . "\" />\n");
        print("<input type=\"hidden\" name=\"cid\" value=\"$commentid\" />\n");
        print("<table border=1 cellspacing=\"0\" cellpadding=\"5\">\n");
        echo ("<tr><td class=colhead align=left>������������� ����������� � \"" . htmlspecialchars($name) . "\"</td><tr>\n");
        print("<tr><td align=center>\n");
        textbbcode("Form","msg",htmlspecialchars(unesc($arr["text"])));
        print("<p align=center><a href=tags.php target=_blank>��� ����</a>\n");
        print("</td></tr>\n");
        print("<tr><td align=center colspan=2><input type=submit value=\"�������������!\"></td></tr></form></table>\n");
        stdfoot();
        die;
}

elseif ($action == "check" || $action == "checkoff")
{
	$from=getenv("HTTP_REFERER"); 
	
        $tid = (int) $_GET["tid"];
        if (!is_valid_id($tid))
                stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
        $docheck = mysql_fetch_array(sql_query("SELECT COUNT(*) FROM checkcomm WHERE checkid = " . $tid . " AND userid = ". $CURUSER["id"] . " AND offer = 1"));
        if ($docheck[0] > 0 && $action=="check")
                stderr($tracker_lang['error'], "<p>�� ��� ��������� �� ��� �����������.</p><a href=detailsoff.php?id=$tid#startcomments>�����</a>");
        if ($action == "check") {
                sql_query("INSERT INTO checkcomm (checkid, userid, offer) VALUES ($tid, $CURUSER[id], 1)") or sqlerr(__FILE__,__LINE__);
               // stderr($tracker_lang['success'], "<p>������ �� ������� �� ������������� � ����� �����������.</p><a href=$DEFAULTBASEURL/detailsoff.php?id=$tid#startcomments>�����</a>");
               
               
if ($from)
{
@header("Refresh: 3; url=$from") //or die("��������������� <script>setTimeout('document.location.href=\"$from\"', 10);</script>")
;
}
else {
@header("Refresh: 3; url=detailsoff.php?id=$tid#startcomments")// or die("��������������� <script>setTimeout('document.location.href=\"details.php?id=$tid\"', 10);</script>")
;
}
bark("<p>������ �� ������� �� ������������� � ����� ��������.</p><a href=\"$from\">�����</a> ��� � <a href=\"detailsoff.php?id=$tid#startcomments\">�������</a>",false);
  
        }
        else {
                sql_query("DELETE FROM checkcomm WHERE checkid = $tid AND userid = $CURUSER[id] AND offer = 1") or sqlerr(__FILE__,__LINE__);
                
   
if ($from)
{
@header("Refresh: 3; url=$from") //or die("��������������� <script>setTimeout('document.location.href=\"$from\"', 10);</script>")
;
}
else {
@header("Refresh: 3; url=detailsoff.php?id=$tid#startcomments") or die("��������������� <script>setTimeout('document.location.href=\"detailsoff.php?id=$tid#startcomments\"', 10);</script>");
} 
		bark("<p>������ �� �� ������� �� ������������� � ����� �������.</p><a href=$DEFAULTBASEURL/detailsoff.php?id=$tid#startcomments>�����</a>",false);

             ///   stderr($tracker_lang['success'], "<p>������ �� �� ������� �� ������������� � ����� �����������.</p><a href=$DEFAULTBASEURL/detailsoff.php?id=$tid#startcomments>�����</a>");
    
        }

}





elseif ($action == "delete")
{
        if (get_user_class() < UC_MODERATOR)
                stderr($tracker_lang['error'], $tracker_lang['access_denied']);
        $commentid = (int) $_GET["cid"];
        if (!is_valid_id($commentid))
                stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
        $sure = $_GET["sure"];
        if (!$sure) {
                $referer = $_SERVER["HTTP_REFERER"];
                stderr($tracker_lang['delete']." ".$tracker_lang['comment'], sprintf($tracker_lang['you_want_to_delete_x_click_here'],$tracker_lang['comment'],"?action=delete&cid=$commentid&sure=1".($referer ? "&returnto=" . htmlspecialchars($referer) : "")));
        }
        $res = sql_query("SELECT offer FROM comments WHERE id=$commentid") or sqlerr(__FILE__,__LINE__);
        $arr = mysql_fetch_array($res);
        if ($arr)
                $offid = $arr["offer"];


        sql_query("DELETE FROM comments WHERE id=$commentid") or sqlerr(__FILE__,__LINE__);
        if ($offid && mysql_affected_rows() > 0)
                sql_query("UPDATE off_reqs SET comments = comments - 1 WHERE id = $offid");
        $returnto = htmlspecialchars($_GET["returnto"]);
        if ($returnto)
                header("Location: $returnto");
        else
                header("Location: $DEFAULTBASEURL/"); // change later ----------------------
        die;
}

elseif ($action == "vieworiginal") {
        if (get_user_class() < UC_MODERATOR)
                stderr($tracker_lang['error'], $tracker_lang['access_denied']);
        $commentid = (int) $_GET["cid"];
        if (!is_valid_id($commentid))
                stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
        $res = sql_query("SELECT c.*, t.name FROM comments AS c JOIN offers AS t ON c.offer = t.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
        $arr = mysql_fetch_array($res);
        if (!$arr)
                stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
        stdhead("��������");
        print("<table width=500 border=1 cellspacing=0 cellpadding=5>");
        print("<tr><td class=colhead>������������ ���������� ����������� #$commentid</td></tr>");
        print("<tr><td class=comment>\n");
        echo htmlspecialchars($arr["ori_text"]);
        print("</td></tr></table>\n");
        $returnto = htmlentities($_SERVER["HTTP_REFERER"]);
        if ($returnto)
                print("<p><font size=small>(<a href=$returnto>".$tracker_lang['back']."</a>)</font></p>\n");
        stdfoot();
        die;
}
elseif ($action <> "add" && $action <> "quote" && $action <> "edit" && $action <> "check" && $action <> "checkoff" && $action <> "delete" && $action <> "vieworiginal") {
stderr($tracker_lang['error'], "����������� �������� - $action");
die;
}


?>