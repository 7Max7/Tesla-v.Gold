<?

require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();
stdhead("�������� �������");
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
     if (empty($_POST["nick"]) && empty($_POST['amount'])  && empty($_POST['msg']))
     {
        stdmsg($tracker_lang['error'], "���������� ����� ����� ������. ��������� �������.");
        stdfoot();
        die();
    }
    
    
    if (!is_numeric($_POST["nick"]))
     {
        stdmsg($tracker_lang['error'], "Id ������������ - �� �����!");
        stdfoot();
        die();
    }
        if (!is_numeric($_POST['amount']))
     {
        stdmsg($tracker_lang['error'], "������� ������� - �� �����!");
        stdfoot();
        die();
    }
       $nick = (int)$_POST["nick"];
    $amount = (int)$_POST['amount'];
    
    if (empty($nick)) {
        stdmsg($tracker_lang['error'], "�� �� ������ ��� ����������!");
         stdfoot();
        die();
    }
//if(!preg_match("/^[a-zA-Z�-��-�0-9_\+]+$/", $nick)) {
       //     stdmsg($tracker_lang['error'], "������������ ��� ����������!");
     //   die();
   // }

    if ($amount<0) {
        stdmsg($tracker_lang['error'], "�� �� ������ ���������� ������������ �������!");
         stdfoot();
        die();
    }
    
    if (empty($_POST['msg'])) {
        stdmsg($tracker_lang['error'], "�� ������ �������� ��������� � ��� ��� ��������� ������");
         stdfoot();
        die();
    }
    $msg = htmlspecialchars($_POST['msg']);
   // $msg=preg_replace( "/'/i"," ",$msg );
     
     
    $res = sql_query("SELECT * FROM users WHERE `id` = ".sqlesc($nick)."") or sqlerr(__FILE__,__LINE__);
    $receiver = mysql_fetch_assoc($res);
    if(!$receiver['id']) {
        stdmsg($tracker_lang['error'], "������������ � id: <b>" . $nick ."</b> �� ����������!");
         stdfoot();
        die();
    }
    if($receiver['id'] == $CURUSER["id"]) {
            stdmsg($tracker_lang['error'], "������ ������ ������ ����");
             stdfoot();
        die();
    }
    if($CURUSER["bonus"] < $amount) {
        stdmsg($tracker_lang['error'], "� ��� ������������ �������!");
         stdfoot();
        die();
    }
        if (!sql_query("UPDATE users SET bonus = bonus - $amount WHERE id = ".sqlesc($CURUSER["id"]))) {
                stdmsg($tracker_lang['error'], "�� ���� �������� �����! (+)");
                 stdfoot();
                die();
            }
        if (!sql_query("UPDATE users SET bonus = bonus + $amount WHERE id = ".sqlesc($receiver['id']))) {
                stdmsg($tracker_lang['error'], "�� ���� �������� �����! (-)");
                 stdfoot();
                die();
            }

    $subject = sprintf("������������ %s ������� ��� %d �������", $CURUSER["username"], $amount);
    $msg = "���������� ����� ������� ��������. ������������ $CURUSER[username] ������� ��� $amount �������. ��� �����������:\n " . $msg;
    
   sql_query("INSERT INTO `messages` (sender, receiver, added, msg, poster, subject) VALUES (0, $receiver[id], NOW(), '$msg', 0, '$subject')") or sqlerr(__FILE__, __LINE__); 

	
    echo "<script>setTimeout('document.location.href=\"sendbonus.php\"', 1000);</script>";
    stdmsg($tracker_lang['success'], "������ ��������!");
 stdfoot();
} else {


?>

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="4">
<form action="/sendbonus.php" name="sendbonus" method="post">
<tr>
<td align="center" class="a" >�������� �������</td></tr>
<tr>
<td align="center" class="rowhead3" >
<?=$output;?>  <b>���� (id ������������)</b>:
<input type="text" name="nick" /></td></tr>

<tr>
<td align="center" class="b" >
<b>������� �������</b>:  <input type="text" name="amount" /> <i>������ �����</i><br/></td></tr>
    
  
  <tr>
<td align="center" class="b" >
<b>���������</b>:<br/>
    <textarea cols="100" rows="10" name="msg"></textarea><br />
	<br/></td></tr>
 
 <tr>
<td align="center" class="a" >
  <input type="submit" onClick="send(); return false;" value="�������� �����" />
    </form>
	<br/></td></tr>
	
 
  
</table>




<? }
stdfoot();
?>