<?
require "include/bittorrent.php";
dbconn(false);

loggedinorreturn();

if (get_user_class() <= UC_MODERATOR)
{
stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

$action = (isset($_GET['action']) ? $_GET['action'] : 'showlist');

$id = ((int)$_GET['id']);
$id = (isset($id) ? $id : '');



function check ($id) {
    if (!is_valid_id($id))
        return stderr("Ошибка","Неверный id");
    else
        return true;
}
function safe_query ($query,$id) {
    $query = sprintf("$query WHERE id ='%s'",
    mysql_real_escape_string($id));
    $result = mysql_query($query);
    if (!$result)
        return sqlerr(__FILE__,__LINE__);
    else
        redirect();
}

function redirect($url=false)
{
	if (empty($url))
	$url=$_SERVER["PHP_SELF"];
	
   // if(!headers_sent())
    //     @header("Location : $url");
  //  else
        echo "<script>setTimeout('document.location.href=\"$url\"', 1000);</script>";
    exit;
}

if ($action == 'showlist') {
stdheadchat ("Попытки входов");

$res2 = sql_query("SELECT COUNT(*) FROM loginattempts AS l ORDER BY l.id ASC")or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res2); 
$count = $row[0]; 
$perpage = 200;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?" ); 


$res = sql_query("SELECT 
l.id,l.ip,l.added,l.banned,l.attempts,l.comment,
u.username AS user_name, u.class AS user_class, u.id AS user_id 

 FROM loginattempts AS l 
 LEFT JOIN users AS u ON l.ip = u.ip 
 ORDER BY l.id ASC $limit") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0) {
	   print("<table border=0 cellspacing=0 cellpadding=5 width=100%>\n");
       print("<tr><td align=center colspan=2><b>Ничего не найденно</b></td></tr>\n");
      }
else
{
	print"<table border=0 cellspacing=0 cellpadding=2 width=100%><tr><td align=center class=a>
Данные, которые не забанены, удаляются через 7 дней ".(!empty($count) && get_user_class() == UC_SYSOP ? "
<form method=\"get\" action=\"".$_SERVER["PHP_SELF"]."\" name=\"add\">
<input type=\"hidden\" name=\"action\" value=\"truncate\">
<input type=\"submit\" value=\"Очистить входы\">
</form>":"")."</td></tr></table>";
	echo $pagertop; 
print("<table border=0 cellspacing=0 cellpadding=5 width=100%>\n");

  print("<tr>
  <td class=a>id</td>
  <td class=a align=center>ip адрес : ник</td>
  <td class=a align=center>Зафиксированно</td>".
    "<td class=a align=center>Входов</td>
	<td class=a align=center>Забанен</td>
	<td class=a align=center>Попытка зайти в</td>
	</tr>\n");

  while ($arr = mysql_fetch_assoc($res))
  {

/// перебираем значения из коммент, где все через ,

$nameid=array($arr["comment"]);
$id_array=$arr["id"];
//$nameid=split (',', $arr["comment"]);

//die($nameid);
$r3 = mysql_query("SELECT id,username,class FROM users WHERE id IN (".implode(",",$nameid).")" ) or sqlerr(__FILE__,__LINE__);
$tag_comment=0;

 while ($a3  = mysql_fetch_assoc($r3))
{

$tag_comment++;


if ($username["$id_array"])
{
$username["$id_array"].=", ";
}
$username["$id_array"].="<a href=userdetails.php?id=$a3[id]>".get_user_class_color($a3["class"],$a3["username"]."</a>");

//if ($username2){ $username2.=", ";}
//$username2.="$tag_comment=>$username";
//$hide_username= array($username2); 
}



      //$r2 = sql_query("SELECT id,username,class FROM users WHERE ip=".sqlesc($arr[ip])) or sqlerr(__FILE__,__LINE__);
   // $a2 = mysql_fetch_assoc($r2);    
   /*  
     if (($arr["user_name"]==$a3["username"] && $arr["user_id"]==$a3["id"]) && $tag_comment==1)
     $yes="class=\"b\"";
     else
     $yes="class=\"a\"";
     */
      print("<tr>
	  <td $yes align=>$arr[id]</td>
	  <td $yes align=center>$arr[ip] 
	  " . ($arr["user_id"] ? ": <a href=userdetails.php?id=".$arr["user_id"].">" : "" ) . " 
	  " . ($arr["user_name"] ? 
	  "".get_user_class_color($arr["user_class"],$arr["user_name"])."</a>" : ": <i>не найден ник</i>" ) . "</td>
	  <td $yes align=center>".normaltime($arr[added],true)."</td>
	  <td $yes align=center>$arr[attempts]</td>
	  <td $yes align=center>
	  ".($arr[banned] == "yes" ? "<font color=red><b>да</b></font>
	  ".(get_user_class() > UC_SYSOP ? "<a href=maxlogin.php?action=unban&id=$arr[id]><font color=green>[<b>разбанить</b>]</font></a>":"")."
	  " : "<font color=green><b>нет</b></font>
	  ".(get_user_class() > UC_SYSOP ? "<a href=maxlogin.php?action=ban&id=$arr[id]><font color=red>[<b>забанить</b>]</font></a>":"")."
	  ")."
	  
	  <a OnClick=\"return confirm('Уверенны что хотите удалить данный с этого ip адреса?');\" href=maxlogin.php?action=delete&id=$arr[id]>[<b>удалить</b>]</a></td>
	  
	  <td $yes align=left>
	  ".($tag_comment<>"0" ? "<a title=\"в $tag_comment учетки\">$tag_comment</a>: $username[$id_array]":"$tag_comment: ".$arr["comment"]."")."
	 
	  </td>
	  </tr>\n");
	  	  
 
	  unset($username["$id_array"]);
	  unset($tag_comment); unset($arr["comment"]);

  }
  
}

print("</table>\n");
print($pagerbottom);
}elseif ($action == 'ban' && get_user_class() == UC_SYSOP) {
    check($id);
    stdheadchat("Бан");    
    safe_query("UPDATE loginattempts SET banned = 'yes'",$id);
      redirect();
}elseif ($action == 'unban' && get_user_class() == UC_SYSOP) {
    check($id);
    stdheadchat("Снятие бана");
    safe_query("UPDATE loginattempts SET banned = 'no'",$id);
}elseif ($action == 'delete') {
    check($id);
    stdheadchat("Удаление данных");
    safe_query("DELETE FROM loginattempts",$id);
      redirect();
}elseif ($action == 'truncate' &&  get_user_class() == UC_SYSOP) {
   // check($id);
    stdheadchat("Удаление всех данных");
    sql_query("TRUNCATE loginattempts");
    redirect();
} else
    stderr("Ошибка данных","Тут то и ошибка");



stdfootchat();
?>