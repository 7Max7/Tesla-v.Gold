<?  
include('include/bittorrent.php');  
gzip();  
dbconn();  
loggedinorreturn(); 

if (get_user_class() < UC_SYSOP)
{
stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

if ($HTTP_POST_VARS["likeid"])  
{
    if ($HTTP_POST_VARS["likeid"] == "" || $HTTP_POST_VARS["id"] == "")
	{
    stderr("Ошибка", "Заполнены не все поля");  
    die;
    }
	$id_sql = (int) $HTTP_POST_VARS["id"];  
    $likeid_sql = (int) $HTTP_POST_VARS["likeid"];  
    $id = sqlesc($id_sql);  
    $likeid = sqlesc($likeid_sql);  

sql_query("UPDATE users SET id=$likeid WHERE id=$id"); 

    if (mysql_errno() == 1062){
       stderr("Ошибка", "Пользователь с таким id уже существует: <b>$likeid</b>!"); 
       die; 
       }
    
$res2 = sql_query("SELECT username,id FROM users WHERE id=" . sqlesc($id)) or sqlerr(__FILE__,__LINE__);
$id_forum = mysql_fetch_array($res2);
$id_re = $id_forum["id"];

$res6 = sql_query("SELECT id FROM torrents WHERE moderated = 'yes' and moderatedby='$id'");

while ($row6 = mysql_fetch_assoc($res6)) {
//die($row6);
$row6=$row6["id"];
//$updateset3[] = "moderated = 'no'";
$updateset3[] = "moderatedby = '$likeid'";
sql_query("UPDATE torrents SET " . implode(", ", $updateset3) . " WHERE id = '$row6'") or sqlerr(__FILE__, __LINE__);
}



 
@sql_query("UPDATE bookmarks SET userid=$likeid WHERE userid=$id"); 
@sql_query("UPDATE checkcomm SET userid=$likeid WHERE userid=$id"); 
@sql_query("UPDATE friends SET userid=$likeid WHERE userid=$id"); 
@sql_query("UPDATE friends SET friendid=$likeid WHERE friendid=$id"); 
@sql_query("UPDATE invites SET inviter=$likeid WHERE inviter=$id"); 
@sql_query("UPDATE messages SET sender=$likeid WHERE sender=$id"); 
@sql_query("UPDATE messages SET receiver=$likeid WHERE receiver=$id"); 
@sql_query("UPDATE messages SET poster=$likeid WHERE poster=$id"); 
@sql_query("UPDATE news SET userid=$likeid WHERE userid=$id"); 
@sql_query("UPDATE peers SET userid=$likeid WHERE userid=$id"); 
@sql_query("UPDATE pollanswers SET userid=$likeid WHERE userid=$id"); 
@sql_query("UPDATE comments SET user=$likeid WHERE user=$id"); 
@sql_query("UPDATE polls SET createby=$likeid WHERE createby=$id"); 
@sql_query("UPDATE ratings SET user=$likeid WHERE user=$id"); 
@sql_query("UPDATE report SET userid=$likeid WHERE userid=$id"); 
@sql_query("UPDATE sessions SET uid=$likeid WHERE uid=$id"); 
@sql_query("UPDATE shoutbox SET userid=$likeid WHERE userid=$id"); 
@sql_query("UPDATE simpaty SET touserid=$likeid WHERE touserid=$id"); 
@sql_query("UPDATE simpaty SET fromuserid=$likeid WHERE fromuserid=$id"); 
@sql_query("UPDATE snatched SET userid=$likeid WHERE userid=$id"); 
@sql_query("UPDATE thanks SET userid=$likeid WHERE userid=$id"); 
@sql_query("UPDATE thanks SET touid=$likeid WHERE touid=$id"); 
@sql_query("UPDATE torrents SET owner=$likeid WHERE owner=$id"); 
@sql_query("UPDATE uploadapp SET userid=$likeid WHERE userid=$id"); 
@sql_query("UPDATE posts SET userid=$likeid WHERE userid=$id");
@sql_query("UPDATE posts SET editedby=$likeid WHERE editedby=$id");

/* /// если есть форум встроенный
 sql_query("UPDATE ".TABLE_PREFIX."users SET id=$likeid WHERE uid=$id") or sqlerr(__FILE__, __LINE__); 
 */
 if ($likeid_sql && $id_sql)
 stderr("Готово", "Cмена с $id_sql на $likeid_sql произошла успешно!"); 
  //  header("Location: $BASEURL/changeuserid.php");  
   die;
}
stdhead("Смена id пользователю");  
?>
<h2>Смена идентификатора</h2>  
<form method=post action=changeuserid.php>  
<table border=1 cellspacing=0 cellpadding=5 width="100%">  
<tr><td class=rowhead>ID Пользователя котому хотим изменить id</td><td><input type=text name=id size=10 id=specialboxes></td></tr>
<tr><td class=rowhead>Присвоим id пользователю</td><td><input type=uploaded name=likeid size=10 id=specialboxs> <input type=submit value="Присвоить" class=btn></td></tr>  
</table>  
</form>  
<?   
stdfoot();   
/// мод был написал 7Max7 за 30 минут 9 мая 2009 года.
?>