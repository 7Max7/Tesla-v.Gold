<?  
include('include/bittorrent.php');  
gzip();  
dbconn();  
loggedinorreturn(); 

if (get_user_class() < UC_SYSOP){
attacks_log('changeusername'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

accessadministration(); 


if ($_POST["username"])  
{
    if ($_POST["username"] == "" || $_POST["id"] == "" || $_POST["id"] == ""){  
    stderr("Ошибка", "Введенны неверные данные или заполнены не все поля.");  
    die; 
    }
     
    $id = sqlesc($_POST["id"]);
    $username = sqlesc(trim($_POST["username"]));  


$ct_r = sql_query("SELECT username, id, class FROM users WHERE id=".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$ct_a = mysql_fetch_array($ct_r);
$real=$ct_a["username"];
if (!$real){
stderr("Ошибка", "Не могу найти имя пользователя с этим id - $id."); 
die;
}

if($CURUSER["class"] == $ct_a["class"] && $CURUSER["id"] <> $id){
stderr("Ошибка", "Права такие же как вас, нельзя редактировать!"); 
die;
}
    

    sql_query("UPDATE users SET username=$username WHERE id=".sqlesc($id).""); 
     
    if (mysql_errno() == 1062){
       stderr("Ошибка", "Пользователь с именем: <b>$username</b> - уже зарегистрирован или вы пытаетесь изменить имя боссу!"); 
       die; 
       }
       
/*
//sql_query("UPDATE ".TABLE_PREFIX."users SET username=".($username)."  WHERE username=".sqlesc($real)."") or sqlerr(__FILE__, __LINE__); 
  
sql_query("UPDATE ".TABLE_PREFIX."forums SET lastposter=".($username)." WHERE lastposter=".sqlesc($real)."") or sqlerr(__FILE__, __LINE__); 

sql_query("UPDATE ".TABLE_PREFIX."posts SET username=".($username)." WHERE username=".sqlesc($real)."") or sqlerr(__FILE__, __LINE__); 

sql_query("UPDATE ".TABLE_PREFIX."threads SET username=".($username)." WHERE username=".sqlesc($real)."") or sqlerr(__FILE__, __LINE__); 

 sql_query("UPDATE ".TABLE_PREFIX."users SET username=".($username)." WHERE username=".sqlesc($real)."") or sqlerr(__FILE__, __LINE__); 
 */

 
 
    header("Location: $BASEURL/userdetails.php?id=$id");  
    die;  
}
stdhead("Сменить имя");  
?>  
<h2>Сменить имя</h2>  
<form method=post action=changeusername.php>  
<table border=1 cellspacing=0 cellpadding=5 width="100%">  
<tr><td class=rowhead>ID Пользователя</td><td><input type=text name=id size=25 id=specialboxes></td></tr>  
<tr><td class=rowhead>Новый ник пользователя</td><td><input type=uploaded name=username size=25 id=specialboxs> <input type=submit value="Сменить" class=btn></td></tr>  
</table>  
</form>  
<?   
stdfoot();   
?>