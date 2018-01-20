<?
require_once ("include/bittorrent.php");
gzip();

dbconn();
loggedinorreturn();
parked();


if (strtotime($CURUSER['last_access']) > (strtotime(get_date_time()) - 300)){
//// помечаем количество непрочитанных сообщений
$res_un = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=".$CURUSER["id"]." AND location=1 AND unread='yes'") or sqlerr(__FILE__,__LINE__);
$arr_un = @mysql_fetch_row($res_un);
if ($CURUSER["unread"]<>$arr_un[0])
sql_query("UPDATE users SET unread = ".sqlesc($arr_un[0])." WHERE id = ".sqlesc($CURUSER["id"])) or sqlerr(__FILE__,__LINE__);
//// помечаем количество непрочитанных сообщений
}


// Define constants
define('PM_DELETED',0); // Message was deleted
define('PM_INBOX',1); // Message located in Inbox for reciever
define('PM_SENTBOX',-1); // GET value for sent box

$delete_friend = "<b>[</b>Удалить из друзей<b>]</b>";
$add_friend = "<b>[</b>Добавить в друзья<b>]</b>";

// Determine action
$action = (isset($_GET['action'])? (string) $_GET['action']:"");
if (empty($action))
{
        $action = (isset($_POST['action'])? (string) $_POST['action']:"");
        if (empty($action))
        {
                $action = 'viewmailbox';
        }
}

if ($action == "new") {
	       
		$next = sql_query("SELECT id FROM messages WHERE receiver=".sqlesc($CURUSER['id'])." AND location=1 AND unread='yes' ORDER BY added ASC LIMIT 1") or sqlerr(__FILE__,__LINE__);
		
        $next_row = mysql_fetch_assoc($next);
        if ($next_row){
        	if (get_user_class() < UC_MODERATOR){
        
           @header ("Refresh: 3; url=$DEFAULTBASEURL/message.php?action=viewmessage&id=".$next_row["id"]."");
           stderr("3 секунды ожидания", "Перенаправление на новое сообщение! <a href=\"message.php?action=viewmessage&id=".$next_row["id"]."\">[от старых до новых]</a>");
        
        }
        else
        @header("Location: $DEFAULTBASEURL/message.php?action=viewmessage&id=".$next_row["id"]."");
        
        //print(" [ <a href=\"message.php?action=viewmessage&id=".$next_row["id"]."\">К следующему непрочитанному сообщению</a> ]");
        } else
        @header("Location: $DEFAULTBASEURL/message.php");
		
}
// начало просмотр почтового ящика
if ($action == "viewmailbox") {
	
        // Get Mailbox Number
        $mailbox = (isset($_GET['box'])? (int) $_GET['box']:"");
        if (empty($mailbox))
        {
                $mailbox = PM_INBOX;
        }
                if ($mailbox == PM_INBOX)
                {
                        $mailbox_name = "Входящие сообщения";
                }
                else
                {
                        $mailbox_name = "Отправленные сообщения";
                }

stdhead($mailbox_name); 
	
$perpage = $CURUSER["postsperpage"];

if (empty($perpage) || $perpage>=150)
$perpage = 25;

if ($mailbox <> PM_SENTBOX) {
$res = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . sqlesc($CURUSER['id']) . " AND location=" . sqlesc($mailbox) . " ORDER BY id DESC");
} else
$res = sql_query("SELECT COUNT(*) FROM messages WHERE sender=" . sqlesc($CURUSER['id']) . " AND saved='yes' ORDER BY id DESC");

$row = mysql_fetch_array($res);

list($pagertop, $pagerbottom, $limit) = pager($perpage, $row[0], "message.php?action=viewmailbox&box=$mailbox&");

		?>
        <script language="Javascript" type="text/javascript">
        <!-- Begin
        var checkflag = "false";
        var marked_row = new Array;
        function check(field) {
                if (checkflag == "false") {
                        for (i = 0; i < field.length; i++) {
                                field[i].checked = true;}
                                checkflag = "true";
                        }
                else {
                        for (i = 0; i < field.length; i++) {
              field[i].checked = false; }
                                checkflag = "false";
                        }
                }
       //  End -->
        </script>
        <script language="javascript" type="text/javascript" src="js/functions.js"></script>
        
		<table border="1" cellspacing="0" cellpadding="5" width="100%">
	<tr><td class="colhead" align="center"><?=$mailbox_name?></td></tr></table>
         
		<table border="4" cellpadding="4" cellspacing="0" width="100%">
        <tr class="colhead">
        <td colspan="6" align="right" class="colhead">
<form action="message.php" method="get">
        <input type="hidden" name="action" value="viewmailbox"><?=$tracker_lang['go_to'];?>: <select name="box">
        <option value="1"<?=($mailbox <> PM_INBOX ? " selected" : "")?>><?=$tracker_lang['inbox'];?></option>
        <option value="-1"<?=($mailbox <> PM_SENTBOX ? " selected" : "")?>><?=$tracker_lang['outbox'];?></option>
        </select> <input type="submit" class="btn" value="<?=$tracker_lang['go_go_go'];?>"></form>
  
        </td>
        </tr>
        </table>
        <?=$pagertop?>
        
        <table border="0" cellpadding="4" cellspacing="0" width="100%">
        <form action="message.php" method="post" name="form1">
        <input type="hidden" name="action" value="moveordel">
        <tr>
        <td width="2%" class="colhead"></td>
        <td width="41%" class="colhead">Тема сообщения</td>
        <?
        if ($mailbox == PM_INBOX )
                print ("<TD width=\"45%\" class=\"colhead\">".$tracker_lang['sender']."</TD>");
        else
                print ("<TD width=\"45%\" class=\"colhead\">".$tracker_lang['receiver']."</TD>");
        ?>
        <td width="10%" class="colhead">Время</td>
        <td width="2%" align="center" class="colhead"><input type="checkbox" title="<?=$tracker_lang['mark_all'];?>" value="<?=$tracker_lang['mark_all'];?>" onclick="this.value=check(document.form1.elements);"></td>
        </tr>
        <? 


		
		if ($mailbox <> PM_SENTBOX) {
                $res = sql_query("SELECT m.*, u.class, u.last_access, u.username AS sender_username, s.blocks AS sfid, r.id AS rfid FROM messages m LEFT JOIN users u ON m.sender = u.id LEFT JOIN friends r ON r.userid = {$CURUSER["id"]} AND r.friendid = m.receiver 
			LEFT JOIN friends s ON s.userid = {$CURUSER["id"]} AND s.friendid = m.sender 
	 
			WHERE receiver=" . sqlesc($CURUSER['id']) . " AND location=" . sqlesc($mailbox) . " ORDER BY id DESC $limit") or sqlerr(__FILE__,__LINE__);
        } else {
                $res = sql_query("SELECT m.*, u.class, u.last_access, u.username AS receiver_username, s.blocks AS sfid, r.id AS rfid FROM messages m LEFT JOIN users u ON m.receiver = u.id LEFT JOIN friends r ON r.userid = {$CURUSER["id"]} AND r.friendid = m.receiver LEFT JOIN friends s ON s.userid = {$CURUSER["id"]} AND s.friendid = m.receiver WHERE sender=" . sqlesc($CURUSER['id']) . " AND saved='yes' ORDER BY id DESC $limit") or sqlerr(__FILE__,__LINE__);
        }
        if (mysql_num_rows($res) == 0) {
                echo("<TD colspan=\"6\" align=\"center\">".$tracker_lang['no_messages'].".</TD>\n");
        }
        else
        {
        	
        	$num=0;
                while ($row = mysql_fetch_assoc($res))
                {
if ($num % 2 == 0){$td=" class=b";}else{$td=" class=a";}

if (isset($row["sender_username"]))
$send_user=$row["sender_username"];
else
$send_user="";

$lastseen = ($row["last_access"]);
if ($row["last_access"] == "0000-00-00 00:00:00")
	$lastseen = $tracker_lang['never'];
	
	if ((get_user_class() < UC_MODERATOR) && ($row["class"]) > UC_ADMINISTRATOR)(
$lastseen = "Босс всегда с вами ;)");

else {
  $lastseen .= " (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($row["last_access"])) . " ".$tracker_lang['ago'].")";
}

                        // Get Sender Username
if ($row['sender'] <> 0) {
$username = "<A href=\"userdetails.php?id=".$row['sender']."\">".get_user_class_color($row["class"],$send_user)."</A>";

 $id = $row['sender'];
 $friend = $row['sfid'];

if ($send_user){

 if ($row["sfid"]=="0")
    $blocking[$id]="[<a title=\"Убрать из списка друзей\" href=\"friends.php?action=delete&type=friend&targetid=$id\">Убрать из друзей</a>]\n";
  elseif($row["sfid"]=="1")
    $blocking[$id]="[<a title=\"Больше не блокировать сообщения от этого пользователя\" href=\"friends.php?action=delete&type=block&targetid=$id\">Убрать из игнора</a>]\n";
  else
{
$blocking[$id]="[<a title=\"Добавить в список друзей\" href=\"friends.php?action=add&type=friend&targetid=$id\">В друзья</a>] ";
$blocking2[$id]="[<a title=\"Блокировать сообщения от этого пользователя\" href=\"friends.php?action=add&type=block&targetid=$id\">В игнор</a>]\n";
}
} else {
$username="<i><b>id</b>: ".$row['sender']." (был удален)</i>";
unset($lastseen);

if (isset($blocking[$id])) unset($blocking[$id]);
if (isset($blocking2[$id])) unset($blocking2[$id]);
}
	   
}
                        else {
                        	 $id = 0;
                        	$username = "<font color=gray>[<b>System</b>]</font>";
                        	
                        	if (isset($blocking[$id]))
							unset($blocking[$id]);
							
							if (isset($blocking2[$id]))
							unset($blocking2[$id]);
							
                        	  unset($lastseen);
                        }
                        // Get Receiver Username
                        if ($row['receiver'] <> 0 ) {
                        	

if (isset($row["receiver_username"])){
	
$receiver = "<A href=\"userdetails.php?id=" . $row['receiver'] . "\">".get_user_class_color($row["class"] ,$row["receiver_username"])."	</A>";

if (isset($row['receiver']))
$id_r = $row['receiver'];
else $id_r=0;

if (isset($row['rfid']))
$friend = $row['rfid'];
else
$friend=0;
	
if ($row["sfid"]=="0")
    $blockin[$id_r]="[<a title=\"Убрать из списка друзей\" href=\"friends.php?action=delete&type=friend&targetid=$id_r\">Убрать из друзей</a>]\n";
  elseif($row["sfid"]=="1")
    $blockin[$id_r]="[<a title=\"Больше не блокировать сообщения от этого пользователя\" href=\"friends.php?action=delete&type=block&targetid=$id_r\">Убрать из игнора</a>]\n";
  else
{
$blockin[$id_r]="[<a title=\"Добавить в список друзей\" href=\"friends.php?action=add&type=friend&targetid=$id_r\">В друзья</a>] ";
$blockin2[$id_r]="[<a title=\"Блокировать сообщения от этого пользователя\" href=\"friends.php?action=add&type=block&targetid=$id_r\">В игнор</a>]\n";
}
} else {
$receiver="<i><b>id</b>: ".$row['receiver']." (был удален)</i>";
unset($lastseen);

if (!empty($blockin2[isset($id_r)?$id_r:0])) unset($blockin2[$id_r]);
if (!empty($blockin[isset($id_r)?$id_r:0])) unset($blockin[$id_r]);
}
}
                        else {
                        	$id_r = 0;
                                $receiver = "<font color=gray>[<b>System</b>]</font>";
                                unset($blockin2[$id_r]); unset($blockin[$id_r]);
                                unset($lastseen);
                        }
                        $subject = htmlspecialchars_uni($row['subject']);
                        if (strlen($subject) <= 0) {
                                $subject = $tracker_lang['no_subject'];
                        }
                       
					    if ($row['unread'] == 'yes') {
                                echo("<TR>\n<TD $td><IMG src=\"pic/pn_inboxnew.gif\" alt=\"Не прочитанное\"></TD>\n");
                        }
                        else {
                                echo("<TR>\n<TD $td><IMG src=\"pic/pn_inbox.gif\" alt=\"Прочитанное\"></TD>\n");
                        }


echo("<TD $td><A href=\"message.php?action=viewmessage&amp;id=" . $row['id'] . "\">" . $subject . "</A> ".($mailbox <> PM_SENTBOX ? "<a href=\"message.php?action=sendmessage&receiver=".$row['sender']."&replyto=" . $row['id'] . "\"><b>[</b>Ответить<b>]</b></a>":"")."</TD>\n");
                        
                        /*	<form action=\"message.php\" method=\"get\">
        <input type=\"hidden\" name=\"action\" value=\"sendmessage\">
         <input type=\"hidden\" name=\"receiver\" value=\"".$row['sender']."\">
            <input type=\"hidden\" name=\"replyto\" value=\"".$row['id']."\">
		<input type=\"submit\" class=\"btn\" value=\"Ответить\"></form>
		*/
                        if ($mailbox <> PM_SENTBOX) {
                            echo("<TD $td>$username ".(isset($blocking[$id])?$blocking[$id]:"")." ".(isset($blocking2[$id])?$blocking2[$id]:"")." [<a title=\"История сообщений\" href=\"message.php?action=viewmsg&id=$id\">История</a>]<br>".(isset($lastseen)?$lastseen:"")."</TD>\n");
                        }
                        else {
                            echo("<TD $td>$receiver ".(isset($blockin[$id_r])?$blockin[$id_r]:"")." ".(isset($blockin2[$id_r])?$blockin2[$id_r]:"")." [<a title=\"История сообщений\" href=\"message.php?action=viewmsg&id=$id_r\">История</a>]<br>".(isset($lastseen)?$lastseen:"")."</TD>\n");
                        }
                        echo("<TD $td>" . display_date_time(strtotime($row['added'])) . "</TD>\n");
                        echo("<TD $td><INPUT type=\"checkbox\" name=\"messages[]\" title=\"".$tracker_lang['mark']."\" value=\"" . $row['id'] . "\" id=\"checkbox_tbl_" . $row['id'] . "\"></TD>\n</TR>\n");
                        $num++;
                }
        }
        ?>
        <tr class="colhead">
        <td colspan="6" align="right" class="colhead">
        <input type="hidden" name="box" value="<?=$mailbox?>">
        <input type="submit" class="btn" name="delete" title="<?=$tracker_lang['delete_marked_messages'];?>" value="<?=$tracker_lang['delete'];?>" onClick="return confirm('<?=$tracker_lang['sure_mark_delete'];?>')">
        <input type="submit" class="btn" name="markread" title="<?=$tracker_lang['mark_as_read'];?>" value="<?=$tracker_lang['mark_read'];?>" onClick="return confirm('<?=$tracker_lang['sure_mark_read'];?>')"></form>
        </td>
        </tr>
        </form>
        
        </table>
        <?=$pagerbottom?>
        <div align="left"><img src="pic/pn_inboxnew.gif" alt="Непрочитанные" /> <?=$tracker_lang['mail_unread_desc'];?><br />
        <img src="pic/pn_inbox.gif" alt="Прочитанные" /> <?=$tracker_lang['mail_read_desc'];?></div>
        <?
        stdfoot();
}
// конец просмотр почтового ящика


// начало просмотр тела сообщения
if ($action == "viewmessage") {

        $pm_id = (int) $_GET['id'];
        if (!$pm_id)
        {
                stderr($tracker_lang['error'], "У вас нет прав для просмотра этого сообщения.");
        }
        // Get the message
        $res = sql_query('SELECT * FROM messages WHERE id=' . sqlesc($pm_id) . ' AND (receiver=' . sqlesc($CURUSER['id']) . ' OR (sender=' . sqlesc($CURUSER['id']). ' AND saved=\'yes\')) LIMIT 1') or sqlerr(__FILE__,__LINE__);
      
        if (mysql_num_rows($res) == 0)
        {
                stderr($tracker_lang['error'],"Такого сообщения не существует.");
        }
        // Prepare for displaying message
        $message = mysql_fetch_assoc($res);
        if ($message['sender'] == $CURUSER['id'])
        {
                // Display to
                $res2 = sql_query("SELECT username,class FROM users WHERE id=" . sqlesc($message['receiver'])) or sqlerr(__FILE__,__LINE__);
                $sender = mysql_fetch_array($res2);
                $sender = "<a href=\"userdetails.php?id=".$message['receiver']."\">".get_user_class_color($sender["class"] ,$sender["username"])."</a> [<a title=\"История сообщений\" href=\"message.php?action=viewmsg&id=".$message['receiver']."\">История</a>]";
                $reply = "";
                $from = "Кому";
        }
        else
        {
                $from = "От кого";
                if ($message['sender'] == 0)
                {
                        $sender = "<font color=gray>[<b>System</b>]</font>";
                        $reply = "";
                }
                else
                {
                        $res2 = sql_query("SELECT username,class FROM users WHERE id=" . sqlesc($message['sender'])) or sqlerr(__FILE__,__LINE__);
                        $sender = mysql_fetch_array($res2);
                        $sender = "<a href=\"userdetails.php?id=" . $message['sender'] . "\">
				".get_user_class_color($sender["class"] ,$sender["username"])."
						</a> [<a title=\"История сообщений\" href=\"message.php?action=viewmsg&id=".$message['sender']."\">История</a>]";
                        $reply = " [ <A href=\"message.php?action=sendmessage&amp;receiver=" . $message['sender'] . "&amp;replyto=" . $pm_id . "\">Ответить</A> ]";
                }
        }

$body = format_comment($message['msg']);
$added = display_date_time(strtotime($message['added']))." <br>".get_elapsed_time(sql_timestamp_to_unix_timestamp($message["added"])) . " ".$tracker_lang['ago']."";
        
if (get_user_class() >= UC_MODERATOR && $message['sender'] == $CURUSER['id']) {
$unread = ($message['unread'] == 'yes' ? "<SPAN style=\"color: #FF0000;\">[<b>Новое</b>]</A>" : "");
}
else
{
$unread = "";
}
$subject = format_comment($message['subject']);

        if (empty($subject)){
        $subject = "Без темы";
        }

		$next = sql_query("SELECT id FROM messages WHERE id<>" . sqlesc($pm_id) . " AND receiver=".sqlesc($CURUSER['id'])." AND location=1 AND unread='yes' LIMIT 1") or sqlerr(__FILE__,__LINE__);
		
        $next_row = mysql_fetch_assoc($next);
        if ($next_row){
        $nextview=" [ <a href=\"message.php?action=viewmessage&id=".$next_row["id"]."\">К следующему непрочитанному сообщению</a> ]";
        }
        // Mark message unread
        sql_query("UPDATE messages SET unread='no' WHERE id=" . sqlesc($pm_id) . " AND receiver=" . sqlesc($CURUSER['id']) . " LIMIT 1");
        $unde=mysql_affected_rows();
        
        if (!empty($unde))
         sql_query("UPDATE users SET unread=unread-1 WHERE id=".sqlesc($CURUSER["id"])) or sqlerr(__FILE__,__LINE__);
        // Display message
        stdhead("Личное Сообщение (Тема: $subject)"); ?>
        <table class=main width="660" border="0" cellpadding="4" cellspacing="0">
        <tr>
        <td width="50%" class="colhead"><?=$from?></td>
        <td width="50%" class="colhead">Дата отправки</td>
        </tr>
        <tr>
        <td class="a"><?=$sender?></td>
        <td class="a"><?=$added?>&nbsp;&nbsp;<?=$unread?></td>
        </tr>
        <tr><td class="b" colspan="2"><b>Тема</b>: <?=$subject?></td></tr>
        <tr>
        <td class="b" colspan="2"><?=$body?></td>
        </tr>
        <tr>
        <td class="a" align="right" colspan=2><?=(isset($nextview)? $nextview:"")?> [ <a href="message.php?action=deletemessage&id=<?=$pm_id?>">Удалить</a> ]<?=$reply?> [ <a href="message.php?action=forward&id=<?=$pm_id?>">Переслать</a> ]</td>
        </tr>
        </table>
		<?
        stdfoot();
}
// конец просмотр тела сообщения


// начало просмотр посылка сообщения
if ($action == "sendmessage") {

$receiver = (int) $_GET["receiver"];

if ($receiver==0)
stderr($tracker_lang['error'], "Системе нельзя написать сообщение.");
                
                
if (!is_valid_id($receiver))
stderr($tracker_lang['error'], "Неверное ID получателя");
                
if ($receiver == $CURUSER["id"])
stderr("Ошибка связи", "Сообщения самому себе посылать нельзя.");
                
                
if ($CURUSER["cansendpm"] == 'no') {
stdhead("Отправка сообщений запрещенна.");
stdmsg("Извините", "Вам было запрещено писать лс.");
stdfoot();
exit;
}

        $replyto = (int) $_GET["replyto"];
        if ($replyto && !is_valid_id($replyto))
                stderr($tracker_lang['error'], "Неверное ID сообщения");

        $auto = (isset($_GET["auto"])? $_GET["auto"]:"");
        $std = (isset($_GET["std"])? $_GET["std"]:"");

        if (($auto || $std ) && get_user_class() < UC_MODERATOR)
                stderr($tracker_lang['error'], "Досступ запрещен.");

        $res = sql_query("SELECT * FROM users WHERE id=$receiver") or sqlerr(__FILE__,__LINE__);
        $user = mysql_fetch_assoc($res);
        if (!$user)
                stderr($tracker_lang['error'], "Пользователя с таким ID не существует.");
        if ($auto)
                $body = $pm_std_reply[$auto];
        if ($std)
                $body = $pm_template[$std][1];

        if ($replyto) {
                $res = sql_query("SELECT * FROM messages WHERE id=$replyto") or sqlerr(__FILE__, __LINE__);
                $msga = mysql_fetch_assoc($res);
                if ($msga["receiver"] != $CURUSER["id"])
                        stderr($tracker_lang['error'], "Вы пытаетесь ответить не на свое сообщение!");

                $res = sql_query("SELECT username FROM users WHERE id=" . $msga["sender"]) or sqlerr(__FILE__, __LINE__);
                
                
$qwho_free = sql_query("SELECT users.username, users.class, users.gender FROM users WHERE id = ".$msga["sender"]."") or sqlerr(__FILE__,__LINE__); 
$who_name = mysql_fetch_assoc($qwho_free); 

if(mysql_num_rows($qwho_free)){
if($who_name["gender"] == '2') { 
$g = "а"; } 
else { 
$g = ""; } 
}
 // мой мод ответа, цитируя сообщение девушки

$usra = mysql_fetch_assoc($res);
$body = "[spoiler=$usra[username] писал$g]".htmlspecialchars($msga['msg'])."[/spoiler]\n";
// Change
$subject = "Re: " . htmlspecialchars($msga['subject']);
// End of Change
       
       if ($msga["unread"]=="yes"){
      /// фикс если не читаешь сообщение а сразу отвечаешь на него
	   sql_query("UPDATE messages SET unread='no' WHERE id=" . sqlesc($replyto) . " AND receiver=" . sqlesc($CURUSER['id']) . " LIMIT 1");
	   }
        }

        stdhead("Отсылка сообщений", false);
        $sub= "Re: ".$msga["id"];
        ?>
        <table class=main border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
        <form name=message method=post action=message.php>
        <input type=hidden name=action value=takemessage>
        <table class=message cellspacing=0 cellpadding=5>
        <tr><td colspan=2 class=colhead>Сообщение для <a class=altlink_white href=userdetails.php?id=<?=$receiver?>><?=$user["username"]?></a></td></tr>

        <TR>
        <TD colspan="2" class="b"><B>Тема:&nbsp;&nbsp;</B>
<INPUT name="subject" type="text" size="60" value="<?=$sub?>" onclick="if (this.value == 'Введите название темы') this.value = '';" onblur="if (this.value == '') this.value = '<?=$sub?>';"
size="22" autocomplete="off" ondblclick="suggest(event.keyCode,this.value);" onkeyup="suggest(event.keyCode,this.value);" onkeypress="return noenter(event.keyCode);" value=""  value="<?=$subject?>" maxlength="255"></TD>

        <tr><td class="a" <?=$replyto?" colspan=2":""?>>
        <?
        textbbcode("message","msg","$body");
        ?>
        </td></tr>
        <tr>
        <? if ($replyto) { ?>
        <td align=center class="a"><label><input type=checkbox name='delete' value='yes' <?=$CURUSER['deletepms'] == 'yes'?"checked":""?>>Удалить сообщение после ответа
        <input type=hidden name=origmsg value=<?=$replyto?>></label></td>
        <? } ?>
        <td align=center class="a"><label><input type=checkbox name='save' value='yes' <?=$CURUSER['savepms'] == 'yes'?"checked":""?>>Сохранить сообщение в отправленных</label></td></tr>
        
        
<?
if (get_user_class() >= UC_MODERATOR)
echo "<tr><td align=\"center\" colspan=\"2\" class=\"a\"><label><input type=\"checkbox\" name=\"typemsg\" value=\"yes\">Копию сообщения отправить на почту ввиде письма</label></td></tr>";
?>
        
        <tr><td class="b" <?=$replyto?" colspan=2":""?> align=center>
		
		<? if (get_user_class() == UC_SYSOP) {?>
		<div align="center"><b>Отправитель:&nbsp;&nbsp;</b>
<label><b><?=get_user_class_color($CURUSER['class'], $CURUSER['username'])?></b>
<input name="sender" type="radio" value="self" checked></label>&nbsp;&nbsp;
<label><font color=gray>[<b>System</b>]</font><input name="sender" type="radio" value="system"></label>
</div><? }?>

<br>
<input type=submit class="btn" style="width: 200px" value="Отправить сообщение" class=btn></td></tr>
</table>
<input type=hidden name=receiver value=<?=$receiver?>>
</form>
</div></td></tr></table>
<?
stdfoot();
}
// конец посылка сообщения


// начало прием посланного сообщения
if ($action == 'takemessage') {

        $receiver = $_POST["receiver"];
        $origmsg = $_POST["origmsg"];
        $save = $_POST["save"];
        $returnto = htmlentities($_POST["returnto"]);
        if (!is_valid_id($receiver) || ($origmsg && !is_valid_id($origmsg)))
                stderr($tracker_lang['error'],"Неверный ID");
        $msg = htmlspecialchars_uni($_POST["msg"]);
        
        if (empty($msg))
        stderr($tracker_lang['error'],"Пожалуйста введите сообщение!");
        $subject =  htmlspecialchars_uni($_POST['subject']);
        if (!$subject)
                stderr($tracker_lang['error'],"Пожалуйста введите тему сообщения!");
        // Change
        $save = ($save == 'yes') ? "yes" : "no";
        // End of Change
        $res = sql_query("SELECT email, acceptpms, notifs, parked, last_access,icq, UNIX_TIMESTAMP(last_access) as la FROM users WHERE id=$receiver") or sqlerr(__FILE__, __LINE__);
        $user = mysql_fetch_assoc($res);
        if (!$user)
                stderr($tracker_lang['error'], "Нет пользователя с таким ID $receiver.");

        if ($user["parked"] == "yes")
                stderr($tracker_lang['error'], "Этот аккаунт припаркован.");
                
        if (get_user_class() < UC_MODERATOR) {
                if ($user["acceptpms"] == "yes")
                {
                        $res2 = sql_query("SELECT * FROM friends WHERE blocks=1 and userid=$receiver AND friendid=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
                        if (mysql_num_rows($res2) == 1)
                              //  sttderr("Отклонено", "Этот пользователь добавил вас в черный список.");
                                //  stderr($tracker_lang['error'], "Сообщение нельзя отправить.");
                                 stderr("Отклонено", "Этот пользователь не принимает сообщения.");
                }
                elseif ($user["acceptpms"] == "friends")
                {
                        $res2 = sql_query("SELECT * FROM friends WHERE blocks=0 and userid=$receiver AND friendid=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
                        if (mysql_num_rows($res2) <> 1)
                                 stderr("Отклонено", "Этот пользователь принимает сообщение только из списка своих друзей");
                }
                elseif ($user["acceptpms"] == "no")
                                 stderr("Отклонено", "Этот пользователь не принимает сообщения.");
        }
        if (get_user_class() == UC_SYSOP){
         $sender_id = ($_POST['sender'] == 'system' ? 0 : $CURUSER['id']);
              }
              else
               $sender_id = $CURUSER['id'];





/*
if ($receiver=="2"){
	
	
$dt = gmtime() - 600;
$dt = sqlesc(get_date_time($dt));

$res = sql_query("SELECT last_access FROM users WHERE id='2' AND last_access>$dt");
$arr2 = mysql_fetch_assoc($res);
if (!$arr2["last_access"]){
	
$to = "@sms.idknet.com";
$subj = "$subject";
$from = $accountname;

$mess=": ot ".$CURUSER["username"];
sent_mail($to,$SITENAME,$SITEEMAIL,$subj,$mess,false);
}
}
*/


sql_query("INSERT INTO messages (poster, sender, receiver, added, msg, subject, saved, location) VALUES(" . $sender_id . ", " . $sender_id . ",$receiver,'" . get_date_time() . "', " . sqlesc($msg) . ", " . sqlesc($subject) . ", " . sqlesc($save) . ", 1)") or sqlerr(__FILE__, __LINE__);
$sended_id = mysql_insert_id();

if (!empty($sended_id))
sql_query("UPDATE users SET unread=unread+'1' WHERE id = ".sqlesc($receiver)) or sqlerr(__FILE__,__LINE__);

$typemsg = $_POST["typemsg"];
if (!empty($typemsg) && get_user_class() >= UC_MODERATOR){

$now_time = get_date_time();  /// сейчас время

$subj = "Cообщение на $SITENAME: $subject";
$mess = "--------------------тема сообщения----------------------------
".$subject."
--------------------сообщение----------------------------
".$msg."
------------------------------------------------
Чтобы прочитать оригинальное сообщение, пройдите по ссылке: $DEFAULTBASEURL/message.php?action=viewmessage&id=".$sended_id."
------------------------------------------------
Страничка с вашем именем доступна по адресу: $DEFAULTBASEURL/userdetails.php?id=$receiver
------------------------------------------------
Войти со своими данными: $DEFAULTBASEURL/login.php
Восстановить пароль: $DEFAULTBASEURL/recover.php
Регистрация: $DEFAULTBASEURL/signup.php
------------------------------------------------
--> время отправка письма - $now_time
---> С ув. Система сайта $SITENAME";

@sent_mail($user["email"],$SITENAME,$SITEEMAIL,$subj,$mess,false);
}


//	die(f);

/*
$dt = sqlesc(get_date_time(gmtime() - 300));
if ($user['last_access']>$dt && !empty($user["icq"])){
//	die(f);
$icq=$user["icq"];
$use=$CURUSER["username"];
$clear_mess=strip_tags($msg);
$message="Здравствуйте, $use послал вам личное сообщение: 
$clear_mess

Пройдите по ссылке ниже, чтобы ответить на него $DEFAULTBASEURL/message.php?action=sendmessage&receiver=$sender_id&replyto=$sended_id

..::функция icq оповещения является тестовой::..
";
message_to_icq($icq, $message);
}
*/

/*
        if (strpos($user['notifs'], '[pm]') !== false) {
                $username = $CURUSER["username"];
                $usremail = $user["email"];
$body = <<<EOD
$username послал вам личное сообщение!

Пройдите по ссылке ниже, чтобы его прочитать.

$DEFAULTBASEURL/message.php?action=viewmessage&id=$sended_id

--- если пришло это письмо, значить вы включили оповещение о новых сообщениях ---
$SITENAME
EOD;
                $subj = "Вы получили новое ЛС от ".$username."!"; 
                sent_mail($usremail,$SITENAME,$SITEEMAIL,$subj,$body,false);
        }
        */
        
        
        $delete = (int)$_POST["delete"];
        if ($origmsg)
        {
                if ($delete == "yes")
                {
                        // Make sure receiver of $origmsg is current user
                        $res = sql_query("SELECT * FROM messages WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);
                        if (mysql_num_rows($res) == 1)
                        {
                                $arr = mysql_fetch_assoc($res);
                                if ($arr["receiver"] != $CURUSER["id"])
                                        stderr($tracker_lang['error'],"Вы пытаетесь удалить не свое сообщение!");
                                if ($arr["saved"] == "no")
                                        sql_query("DELETE FROM messages WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);
                                elseif ($arr["saved"] == "yes")
                                        sql_query("UPDATE messages SET location = '0' WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);
                        }
                }
                if (!$returnto)
                        $returnto = "$DEFAULTBASEURL/message.php";
        }
        if ($returnto) {
                header("Location: $returnto");
                die;
        }
        else {
                header ("Refresh: 2; url=message.php");
                stderr($tracker_lang['success'] , "Сообщение было успешно отправлено!");
        }


}
// конец прием посланного сообщения


//начало массовая рассылка
if ($action == 'mass_pm') {
        if (get_user_class() <= UC_MODERATOR)
                stderr($tracker_lang['error'], $tracker_lang['access_denied']);
       // $n_pms = $_POST['n_pms'];
        $n_pms = (int) $_POST['n_pms'];
        $pmees = htmlspecialchars_uni($_POST['pmees']);
        $auto = htmlspecialchars_uni($_POST['auto']);

        if ($auto)
                $body=$mm_template[$auto][1];

        stdhead("Отсылка сообщений", false);
        
      //  <input type=hidden name=returnto value=<?=$_SERVER["HTTP_REFERER"]?>
      
      
        <table class=main border=0 cellspacing=0 cellpadding=0>
        <tr><td class=embedded><div align=center>
        <form method=post action=<?=$_SERVER['PHP_SELF']?> name=message>
        <input type=hidden name=action value=takemass_pm>
        <? if ($_SERVER["HTTP_REFERER"]) { ?>
     
	   <input type=hidden name=returnto value="<?=htmlentities($_SERVER["HTTP_REFERER"]);?>">
	
        <? } ?>
        <table border=1 cellspacing=0 cellpadding=5>
        <tr><td class=colhead colspan=2>Массовая рассылка для <?=$n_pms?> пользовате<?=($n_pms>1?"лей":"ля")?></td></tr>
        <tr>
        <td colspan="2"><b>Тема:&nbsp;&nbsp;</b>
        <input name="subject" type="text" size="60" maxlength="255"></td>
        </tr>
        <tr><td colspan="2"><div align="center">
        <?=textbbcode("message","msg","$body");?>
        </div></td></tr>
        <tr><td colspan="2"><div align="center"><b>Комментарий:&nbsp;&nbsp;</b>
        <input name="comment" type="text" size="70">
        </div></td></tr>
        <tr><td><div align="center"><b>От:&nbsp;&nbsp;</b>
        <?=$CURUSER['username']?>
        <input name="sender" type="radio" value="self" checked>
        &nbsp; <font color=gray>[<b>System</b>]</font>
        <input name="sender" type="radio" value="system">
        </div></td>
        <td><div align="center"><b>Вписать статистику в Историю пользователя:</b>&nbsp;<input name="snap" type="checkbox" value="1">
         </div></td></tr>
        <tr><td colspan="2" align=center><input type=submit value="Послать!" class=btn>
        </td></tr></table>
        <input type=hidden name=pmees value="<?=$pmees?>">
        <input type=hidden name=n_pms value=<?=$n_pms?>>
        </form><br /><br />
        </div>
        </td>
        </tr>
        </table>
        <?
        stdfoot();

}
//конец массовая рассылка


//начало прием сообщений из массовой рассылки
if ($action == 'takemass_pm') {
        if (get_user_class() <= UC_MODERATOR)
                stderr($tracker_lang['error'], $tracker_lang['access_denied']);
        $msg =  htmlspecialchars_uni($_POST["msg"]);
        if (!$msg)
                stderr($tracker_lang['error'],"Пожалуйста введите сообщение.");
        $sender_id = ($_POST['sender'] == 'system' ? 0 : $CURUSER['id']);
        $from_is = unesc($_POST['pmees']);
        // Change
        $subject = htmlspecialchars_uni($_POST['subject']);
        $query = "INSERT INTO messages (sender, receiver, added, msg, subject, location, poster) ". "SELECT $sender_id, u.id, '" . get_date_time(time()) . "', " .
        sqlesc($msg) . ", " . sqlesc($subject) . ", 1, $sender_id " . $from_is;
        // End of Change
        sql_query($query) or sqlerr(__FILE__, __LINE__);
        $n = mysql_affected_rows();
        $n_pms = htmlspecialchars($_POST['n_pms']);
        $comment = htmlspecialchars($_POST['comment']);
        $snapshot = htmlspecialchars($_POST['snap']);
        // add a custom text or stats snapshot to comments in profile
        if ($comment || $snapshot)
        {
                $res = sql_query("SELECT u.id, u.uploaded, u.downloaded, u.modcomment ".$from_is) or sqlerr(__FILE__, __LINE__);
                if (mysql_num_rows($res) > 0)
                {
                        $l = 0;
                        while ($user = mysql_fetch_array($res))
                        {
                                unset($new);
                                $old = $user['modcomment'];
                                if ($comment)
                                        $new = $comment;
                                        if ($snapshot)
                                        {
                                                $new .= ($new?"\n":"") . "MMed, " . date("Y-m-d") . ", " .
                                                "UL: " . mksize($user['uploaded']) . ", " .
                                                "DL: " . mksize($user['downloaded']) . ", " .
                                                "r: " . (($user['downloaded'] > 0)?($user['uploaded']/$user['downloaded']) : 0) . " - " .
                                                ($_POST['sender'] == "system"?"System":$CURUSER['username']);
                                        }
                                        $new .= $old?("\n".$old):$old;
                                        sql_query("UPDATE users SET modcomment = " . sqlesc($new) . ",unread=unread+1 WHERE id = " . $user['id']) or sqlerr(__FILE__, __LINE__);
                                        if (mysql_affected_rows())
                                                $l++;
                        }
                }
        }else
        
        $res = sql_query("SELECT u.id ".$from_is) or sqlerr(__FILE__, __LINE__);
        if (mysql_num_rows($res) > 0){
        while ($user = mysql_fetch_array($res)){
        sql_query("UPDATE users SET unread=unread+1 WHERE id = ".$user['id']) or sqlerr(__FILE__, __LINE__);
        }
		}
                                        
        header ("Refresh: 3; url=message.php");
        stderr($tracker_lang['success'], (($n_pms > 1) ? "$n сообщений из $n_pms было" : "Сообщение было")." успешно отправлено!" . ($l ? " $l комментарий(ев) в профиле " . (($l>1) ? "были" : " был") . " обновлен!" : ""));
}
//конец прием сообщений из массовой рассылки


//начало перемещение, помечание как прочитанного
if ($action == "moveordel") {
        $pm_id = (int) $_POST['id'];
        $pm_box = (int) $_POST['box'];
        $pm_messages = $_POST['messages'];
        if ($_POST['move']) {
                if ($pm_id) {
                        // Move a single message
                        sql_query("UPDATE messages SET location=" . sqlesc($pm_box) . ", saved = 'yes' WHERE id=" . sqlesc($pm_id) . " AND receiver=" . $CURUSER['id'] . " LIMIT 1")or sqlerr(__FILE__, __LINE__);
                }
                else {
                        // Move multiple messages
                        sql_query("UPDATE messages SET location=" . sqlesc($pm_box) . ", saved = 'yes' WHERE id IN (" . implode(", ", array_map("sqlesc", array_map("intval", $pm_messages))) . ') AND receiver=' . $CURUSER['id'])or sqlerr(__FILE__, __LINE__);
                }
                // Check if messages were moved
                if (@mysql_affected_rows() == 0) {
                        stderr($tracker_lang['error'], "Не возможно переместить сообщения!");
                }
                header("Location: message.php?action=viewmailbox&box=" . $pm_box);
                exit();
        }
        elseif ($_POST['delete']) {
                if ($pm_id) {
                        // Delete a single message
                        $res = sql_query("SELECT * FROM messages WHERE id=" . sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
                        $message = mysql_fetch_assoc($res);
                        if ($message['receiver'] == $CURUSER['id'] && $message['saved'] == 'no') {
                                sql_query("DELETE FROM messages WHERE id=" . sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
                        }
                        elseif ($message['sender'] == $CURUSER['id'] && $message['location'] == PM_DELETED) {
                                sql_query("DELETE FROM messages WHERE id=" . sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
                        }
                        elseif ($message['receiver'] == $CURUSER['id'] && $message['saved'] == 'yes') {
                                sql_query("UPDATE messages SET location=0 WHERE id=" . sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
                        }
                        elseif ($message['sender'] == $CURUSER['id'] && $message['location'] != PM_DELETED) {
                                sql_query("UPDATE messages SET saved='no' WHERE id=" . sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
                        }
                } else {
                        // Delete multiple messages
                        if (is_array($pm_messages))
                        foreach ($pm_messages as $id) {
                                $res = sql_query("SELECT * FROM messages WHERE id=" . sqlesc((int) $id));
                                $message = mysql_fetch_assoc($res);
                                if ($message['receiver'] == $CURUSER['id'] && $message['saved'] == 'no') {
                                        sql_query("DELETE FROM messages WHERE id=" . sqlesc((int) $id)) or sqlerr(__FILE__,__LINE__);
                                }
                                elseif ($message['sender'] == $CURUSER['id'] && $message['location'] == PM_DELETED) {
                                        sql_query("DELETE FROM messages WHERE id=" . sqlesc((int) $id)) or sqlerr(__FILE__,__LINE__);
                                }
                                elseif ($message['receiver'] == $CURUSER['id'] && $message['saved'] == 'yes') {
                                        sql_query("UPDATE messages SET location=0 WHERE id=" . sqlesc((int) $id)) or sqlerr(__FILE__,__LINE__);
                                }
                                elseif ($message['sender'] == $CURUSER['id'] && $message['location'] != PM_DELETED) {
                                        sql_query("UPDATE messages SET saved='no' WHERE id=" . sqlesc((int) $id)) or sqlerr(__FILE__,__LINE__);
                                }
                        }
                }
                // Check if messages were moved
                if (@mysql_affected_rows() == 0) {
                        stderr($tracker_lang['error'],"Сообщение не может быть удалено!");
                }
                else {
                        header("Location: message.php?action=viewmailbox&box=" . $pm_box);
                        exit();
                }
        }
        elseif ($_POST["markread"]) {
                //помечаем одно сообщение
                if ($pm_id) {
                        sql_query("UPDATE messages SET unread='no' WHERE id = " . sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
                }
                //помечаем множество сообщений
                else {
                		if (is_array($pm_messages))
                        foreach ($pm_messages as $id) {
                                $res = sql_query("SELECT * FROM messages WHERE id=" . sqlesc((int) $id));
                                $message = mysql_fetch_assoc($res);
                                sql_query("UPDATE messages SET unread='no' WHERE id = " . sqlesc((int) $id)) or sqlerr(__FILE__,__LINE__);
                        }
                }
                // Проверяем, были ли помечены сообщения
                if (@mysql_affected_rows() == 0) {
                        stderr($tracker_lang['error'], "Сообщение не может быть помечено как прочитанное! ");
                }
                else {
                        header("Location: message.php?action=viewmailbox&box=" . $pm_box);
                        exit();
                }
        }

stderr($tracker_lang['error'],"Нет действия.");
}
//конец перемещение, помечание как прочитанного


//начало пересылка
if ($action == "forward") {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                // Display form
                $pm_id = (int) $_GET['id'];

                // Get the message
                $res = sql_query('SELECT * FROM messages WHERE id=' . sqlesc($pm_id) . ' AND (receiver=' . sqlesc($CURUSER['id']) . ' OR sender=' . sqlesc($CURUSER['id']) . ') LIMIT 1') or sqlerr(__FILE__,__LINE__);


if ($CURUSER["cansendpm"] == 'no') { 	
stdhead("Отправка сообщений запрещенна");
stdmsg("Извините", "Вам было запрещено писать лс."); 
stdfoot(); 
exit; 
}  
                if (!$res) {
                        stderr($tracker_lang['error'], "У вас нет разрешения пересылать это сообщение.");
                }
                if (mysql_num_rows($res) == 0) {
                        stderr($tracker_lang['error'], "У вас нет разрешения пересылать это сообщение.");
                }
                $message = mysql_fetch_assoc($res);

                // Prepare variables
                $subject = "Fwd: " . htmlspecialchars($message['subject']);
                $from = $message['sender'];
                $orig = $message['receiver'];

                $res = sql_query("SELECT username,class FROM users WHERE id=" . sqlesc($orig) . " OR id=" . sqlesc($from)) or sqlerr(__FILE__,__LINE__);

                $orig2 = mysql_fetch_assoc($res);
                $orig_name = "<A href=\"userdetails.php?id=" . $from . "\">
				".get_user_class_color($orig2['class'] ,$orig2['username'])."
				</A>";
                if ($from == 0) {
                        $from_name = "<font color=gray>[<b>System</b>]</font>";
                        $from2['username'] = "<font color=gray>[<b>System</b>]</font>";
                }
                else {
                        $from2 = mysql_fetch_array($res);
                        $from_name = "<A href=\"userdetails.php?id=" . $from . "\">
						".get_user_class_color($from2['class'] ,$from2['username'])."
						</A>";
                }
              $sub= "Fwd: ".$message['id'];
                $body = "-------- Оригинальное сообщение от " . $from2['username'] . ": --------<BR>" . format_comment($message['msg']);

                stdhead("Пересылка сообщения: ".$subject);?>

                <FORM action="message.php" method="post">
                <INPUT type="hidden" name="action" value="forward">
                <INPUT type="hidden" name="id" value="<?=$pm_id?>">
                <TABLE border="0" cellpadding="4" cellspacing="0" width=100% >
                <TR><TD class="colhead" colspan="2"><?=$subject?></TD></TR>
                <TR>
                <TD class="b">Кому:</TD>
                <TD class="a"><INPUT type="text" name="to" value="Введите имя" size="83"></TD>
                </TR>
                <TR>
                <TD class="b">Оригинальный<BR>отправитель:</TD>
                <TD class="b"><?=$orig_name?></TD>
                </TR>
                <TR>
                <TD class="b">От:</TD>
                <TD class="b"><?=$from_name?></TD>
                </TR>
                <TR>
                <TD class="b">Тема:</TD>
                <TD class="a"><INPUT type="text"  name="subject" value="<?=$sub?>" onclick="if (this.value == 'Введите название темы') this.value = '';" onblur="if (this.value == '') this.value = '<?=$sub?>';" autocomplete="off" ondblclick="suggest(event.keyCode,this.value);" onkeyup="suggest(event.keyCode,this.value);" onkeypress="return noenter(event.keyCode);" value=""  value="<?=$subject?>" maxlength="255" size="83"></TD>
                </TR>
                <TR>
                <TD class="b">Сообщение:</TD>
                <TD class="b"><TEXTAREA name="msg" cols="80" rows="8"></TEXTAREA><BR><?=$body?></TD>
                </TR>
                <TR>
                <TD class="a" colspan="2" align="center"><label>Сохранить сообщение <INPUT type="checkbox" name="save" value="1"<?=$CURUSER['savepms'] == 'yes'?" checked":""?>></label>&nbsp;<br><br><INPUT type="submit" class="btn" style="width: 200px" value="Переслать данное сообщение"></TD>
                </TR>
                </TABLE>
                </FORM><?
                stdfoot();
        }

        else {

                // Forward the message
                $pm_id = (int) $_POST['id'];

                // Get the message
                $res = sql_query('SELECT * FROM messages WHERE id=' . sqlesc($pm_id) . ' AND (receiver=' . sqlesc($CURUSER['id']) . ' OR sender=' . sqlesc($CURUSER['id']) . ') LIMIT 1') or sqlerr(__FILE__,__LINE__);
                if (!$res) {
                        stderr($tracker_lang['error'], "У вас нет разрешения пересылать это сообщение.");
                }

                if (mysql_num_rows($res) == 0) {
                        stderr($tracker_lang['error'], "У вас нет разрешения пересылать это сообщение.");
                }

                $message = mysql_fetch_assoc($res);
                $subject = (string) $_POST['subject'];
                $username = strip_tags($_POST['to']);

                // Try finding a user with specified name

                $res = sql_query("SELECT id FROM users WHERE LOWER(username)=LOWER(" . sqlesc($username) . ") LIMIT 1");
                if (!$res) {
                        stderr($tracker_lang['error'], "Пользователя, с таким именем не существует.");
                }
                if (mysql_num_rows($res) == 0) {
                        stderr($tracker_lang['error'], "Пользователя, с таким именем не существует.");
                }

                $to = mysql_fetch_array($res);
                $to = $to[0];

                // Get Orignal sender's username
                if ($message['sender'] == 0) {
                        $from = "<font color=gray>[<b>System</b>]</font>";
                }
                else {
                        $res = sql_query("SELECT * FROM users WHERE id=" . sqlesc($message['sender'])) or sqlerr(__FILE__,__LINE__);
                        $from = mysql_fetch_assoc($res);
                        $from = $from['username'];
                }
                $body = (string) $_POST['msg'];
                $body .= "\n-------- Оригинальное сообщение от " . $from . ": --------\n" . $message['msg'];
                $save = (int) $_POST['save'];
                if ($save) {
                        $save = 'yes';
                }
                else {
                        $save = 'no';
                }

                //Make sure recipient wants this message
                if (get_user_class() < UC_MODERATOR) {
                        if ($from["acceptpms"] == "yes") {
                                $res2 = sql_query("SELECT * FROM friends WHERE blocks=1 and userid=$to AND blockid=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
                                if (mysql_num_rows($res2) == 1)
                       stderr("Отклонено", "Этот пользователь не принимает сообщения.");
                        }
                        elseif ($from["acceptpms"] == "friends") {
                                $res2 = sql_query("SELECT * FROM friends WHERE blocks=0 and userid=$to AND friendid=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
                                if (mysql_num_rows($res2) <> 1)
                                        stderr("Отклонено", "Этот пользователь принимает сообщение только из списка своих друзей.");
                        }

                        elseif ($from["acceptpms"] == "no")
                                stderr("Отклонено", "Этот пользователь не принимает сообщения.");
                }
                
                                
                sql_query("INSERT INTO messages (poster, sender, receiver, added, subject, msg, location, saved) VALUES(" . $CURUSER["id"] . ", " . $CURUSER["id"] . ", $to, '" . get_date_time() . "', " . sqlesc($subject) . "," . sqlesc($body) . ", " . sqlesc(PM_INBOX) . ", " . sqlesc($save) . ")") or sqlerr(__FILE__, __LINE__);
                        stderr("Удачно", "Сообщение было переслано.");
        }
}
//конец пересылка


//начало удаление сообщения
if ($action == "deletemessage") {
        $pm_id = (int) $_GET['id'];

        // Delete message
        $res = sql_query("SELECT * FROM messages WHERE id=" . sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
        if (!$res) {
                stderr($tracker_lang['error'],"Сообщения с таким ID не существует.");
        }
        if (mysql_num_rows($res) == 0) {
                stderr($tracker_lang['error'],"Сообщения с таким ID не существует.");
        }
        $message = mysql_fetch_assoc($res);
        if ($message['receiver'] == $CURUSER['id'] && $message['saved'] == 'no') {
                $res2 = sql_query("DELETE FROM messages WHERE id=" . sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
        }
        elseif ($message['sender'] == $CURUSER['id'] && $message['location'] == PM_DELETED) {
                $res2 = sql_query("DELETE FROM messages WHERE id=" . sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
        }
        elseif ($message['receiver'] == $CURUSER['id'] && $message['saved'] == 'yes') {
                $res2 = sql_query("UPDATE messages SET location=0 WHERE id=" . sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
        }
        elseif ($message['sender'] == $CURUSER['id'] && $message['location'] != PM_DELETED) {
                $res2 = sql_query("UPDATE messages SET saved='no' WHERE id=" . sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
        }
        if (!$res2) {
                stderr($tracker_lang['error'],"Невозможно удалить сообщение.");
        }
        if (mysql_affected_rows() == 0) {
                stderr($tracker_lang['error'],"Невозможно удалить сообщение, cкорее всего оно не ваше.");
        }
        else {
                header("Location: message.php?action=viewmailbox&id=" . $message['location']);
                exit();
        }
}
//конец удаление сообщения

//начало перемещение, помечание как прочитанного
if ($action == "viewmsg") {

$pm_id = (int) $_GET['id']; /// id пользователя с кем хотим переписка для показа

if (get_user_class() > UC_MODERATOR)
$pm_out = (int) $_GET['out']; /// если хотим кого то просмотреть.

if (empty($pm_out))
$pm_out = $CURUSER["id"];

/////////////
$reins = sql_query("SELECT username,class FROM users WHERE id = ".sqlesc($pm_id)) or sqlerr(__FILE__,__LINE__);
$rein = mysql_fetch_assoc($reins); /// к кому обращение

if (!empty($rein["username"])){
$namein = "<a href=userdetails.php?id=".$pm_id.">" . get_user_class_color($rein["class"], $rein["username"])."</a>";
$namein_t = $rein["username"];
}
else
$namein = $namein_t = "id: ".$pm_id."";


/////////////
$reouts = sql_query("SELECT username,class FROM users WHERE id = ".sqlesc($pm_out)) or sqlerr(__FILE__,__LINE__);
$reout = mysql_fetch_assoc($reouts); /// к кому обращение

if (!empty($reout["username"])){
$nameout = "<a href=userdetails.php?id=".$pm_out.">" . get_user_class_color($reout["class"], $reout["username"])."</a>";
$nameout_t = $reout["username"];
}
else 
$nameout = $nameout_t = "id: ".$pm_out."";


stdhead("История переписки");

if ($pm_id == $pm_out)
stderr($tracker_lang['error'], "Попытка не пытка, ты хоть попробовал...");

if (empty($pm_id))
stderr($tracker_lang['error'], "Жаль, а Сообщений для показа нет.");


$res = sql_query("SELECT COUNT(*) FROM messages WHERE ((receiver = ".sqlesc($pm_id)." AND sender = ".sqlesc($pm_out).") OR (sender = ".sqlesc($pm_id)." AND receiver = ".sqlesc($pm_out)."))");

$row = mysql_fetch_array($res);

if (empty($row[0]))
stderr($tracker_lang['error'], "Жаль, а Сообщений для показа нет.");

//stderr($tracker_lang['error'], "У вас нет прав для просмотра этого сообщения.");

$perpage = $CURUSER["postsperpage"];

if (empty($perpage) || $perpage>=150)
$perpage = 25;

list($pagertop, $pagerbottom, $limit) = pager($perpage, $row[0], "message.php?action=viewmsg&id=$pm_id&out=$pm_out&");


$msgs = sql_query("SELECT id, added, sender, receiver, unread, subject, msg, saved, poster, location FROM messages WHERE ((receiver = ".sqlesc($pm_id)." AND sender = ".sqlesc($pm_out).") OR (sender = ".sqlesc($pm_id)." AND receiver = ".sqlesc($pm_out).")) ORDER BY added $limit") or sqlerr(__FILE__,__LINE__);

echo "<table class=main width=\"100%\" border=\"0\" cellpadding=\"4\" cellspacing=\"0\">";

echo "<tr>
<td width=\"50%\" class=\"colhead\">Отправитель / Получатель</td>
<td width=\"50%\" class=\"colhead\">Время отправки</td>
</tr>";

echo "<tr><td class=\"b\" colspan=\"2\">$pagertop</td></tr>";

while ($msg = mysql_fetch_array($msgs)) {

$id = $msg["id"];

$subject = htmlspecialchars_uni($msg['subject']);

$body = format_comment($msg['msg']);

$added = display_date_time(strtotime($msg['added']))."<br>".get_elapsed_time(sql_timestamp_to_unix_timestamp($msg["added"])) . " ".$tracker_lang['ago']."";

//(прочитано)
echo "<tr><td class=\"colhead\" colspan=\"2\">$id ".($msg['unread']=="yes" ? " (непрочитанное)":"")." ".(($msg['saved']=="yes" && !empty($msg['poster'])) ? " сообщение сохранено у ".($msg["poster"]==$pm_id ? $namein_t:$nameout_t):"")."</td></tr>";

echo "<tr>";

echo "<td class=\"a\">Отправитель ".($msg["sender"]==$pm_id ?$namein:$nameout)." / Получатель ".($msg["receiver"]==$pm_id ? $namein:$nameout)."</td>";

echo "<td class=\"a\">$added</td>
</tr>";

echo "<tr><td class=\"b\" colspan=\"2\"><b>Тема</b>: $subject</td></tr>
<tr>
<td class=\"b\" colspan=\"2\">$body</td>
</tr>";

}
echo "<tr><td class=\"b\" colspan=\"2\">$pagerbottom</td></tr>";

echo "</table>";
stdfoot();

}



/*
	  if ($CURUSER) {
  $msgs = sql_query("SELECT location, sender, receiver, unread, saved FROM messages WHERE receiver = " . $CURUSER["id"] . " OR sender = " . $CURUSER["id"]) or sqlerr(__FILE__,__LINE__);
$CURUSER['unread']=$CURUSER['messages']=$CURUSER['outmessages']=0;
while ($message = mysql_fetch_array($msgs)) {
        if ($message["unread"] && $message["location"] == 1 && $message["receiver"] == $CURUSER["id"])
            $CURUSER['unread']++;
        if ($message["location"] == 1 && $message["receiver"] == $CURUSER["id"])
            $CURUSER['messages']++;
        if ($message["saved"] && $message["location"] != 0 && $message["sender"] == $CURUSER["id"])
            $CURUSER['outmessages']++;
    }
    if (get_user_class()>=UC_MODERATOR) $CURUSER['unchecked']=(int)@mysql_result(sql_query("SELECT COUNT(*) FROM torrents WHERE moderatedby=0"),0);
	  }
*/








?>