<?

require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();
stdhead("Передача бонусов");
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
     if (empty($_POST["nick"]) && empty($_POST['amount'])  && empty($_POST['msg']))
     {
        stdmsg($tracker_lang['error'], "Пропущенна форма ввода данных. Вернитесь обратно.");
        stdfoot();
        die();
    }
    
    
    if (!is_numeric($_POST["nick"]))
     {
        stdmsg($tracker_lang['error'], "Id пользователя - Не цифра!");
        stdfoot();
        die();
    }
        if (!is_numeric($_POST['amount']))
     {
        stdmsg($tracker_lang['error'], "Перевод бонусов - Не цифра!");
        stdfoot();
        die();
    }
       $nick = (int)$_POST["nick"];
    $amount = (int)$_POST['amount'];
    
    if (empty($nick)) {
        stdmsg($tracker_lang['error'], "Вы не вывели ник получателя!");
         stdfoot();
        die();
    }
//if(!preg_match("/^[a-zA-Zа-яА-Я0-9_\+]+$/", $nick)) {
       //     stdmsg($tracker_lang['error'], "Некорректный ник получателя!");
     //   die();
   // }

    if ($amount<0) {
        stdmsg($tracker_lang['error'], "Вы не вывели количество передаваемых бонусов!");
         stdfoot();
        die();
    }
    
    if (empty($_POST['msg'])) {
        stdmsg($tracker_lang['error'], "Вы забыли написать сообщение о том что передаете бонусы");
         stdfoot();
        die();
    }
    $msg = htmlspecialchars($_POST['msg']);
   // $msg=preg_replace( "/'/i"," ",$msg );
     
     
    $res = sql_query("SELECT * FROM users WHERE `id` = ".sqlesc($nick)."") or sqlerr(__FILE__,__LINE__);
    $receiver = mysql_fetch_assoc($res);
    if(!$receiver['id']) {
        stdmsg($tracker_lang['error'], "Пользователя с id: <b>" . $nick ."</b> не существует!");
         stdfoot();
        die();
    }
    if($receiver['id'] == $CURUSER["id"]) {
            stdmsg($tracker_lang['error'], "Нельзя дарить бонусы себе");
             stdfoot();
        die();
    }
    if($CURUSER["bonus"] < $amount) {
        stdmsg($tracker_lang['error'], "У вас недостаточно бонусов!");
         stdfoot();
        die();
    }
        if (!sql_query("UPDATE users SET bonus = bonus - $amount WHERE id = ".sqlesc($CURUSER["id"]))) {
                stdmsg($tracker_lang['error'], "Не могу обновить бонус! (+)");
                 stdfoot();
                die();
            }
        if (!sql_query("UPDATE users SET bonus = bonus + $amount WHERE id = ".sqlesc($receiver['id']))) {
                stdmsg($tracker_lang['error'], "Не могу обновить бонус! (-)");
                 stdfoot();
                die();
            }

    $subject = sprintf("Пользователь %s подарил вам %d бонусов", $CURUSER["username"], $amount);
    $msg = "Количество ваших бонусов изменено. Пользователь $CURUSER[username] подарил вам $amount бонусов. Его комментарий:\n " . $msg;
    
   sql_query("INSERT INTO `messages` (sender, receiver, added, msg, poster, subject) VALUES (0, $receiver[id], NOW(), '$msg', 0, '$subject')") or sqlerr(__FILE__, __LINE__); 

	
    echo "<script>setTimeout('document.location.href=\"sendbonus.php\"', 1000);</script>";
    stdmsg($tracker_lang['success'], "Бонусы переданы!");
 stdfoot();
} else {


?>

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="4">
<form action="/sendbonus.php" name="sendbonus" method="post">
<tr>
<td align="center" class="a" >Отправка бонусов</td></tr>
<tr>
<td align="center" class="rowhead3" >
<?=$output;?>  <b>Кому (id пользователя)</b>:
<input type="text" name="nick" /></td></tr>

<tr>
<td align="center" class="b" >
<b>Сколько бонусов</b>:  <input type="text" name="amount" /> <i>только число</i><br/></td></tr>
    
  
  <tr>
<td align="center" class="b" >
<b>Сообщение</b>:<br/>
    <textarea cols="100" rows="10" name="msg"></textarea><br />
	<br/></td></tr>
 
 <tr>
<td align="center" class="a" >
  <input type="submit" onClick="send(); return false;" value="Подарить бонус" />
    </form>
	<br/></td></tr>
	
 
  
</table>




<? }
stdfoot();
?>