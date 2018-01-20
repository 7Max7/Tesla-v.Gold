<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();


if (get_user_class() < UC_ADMINISTRATOR)
 {
attacks_log('adduser'); stderr("Ошибочка", "не туда лезешь, гад..."); die();
}


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["email"]))
		stderr($tracker_lang['error'], $tracker_lang['missing_form_data']);
	if ($_POST["password"] <> $_POST["password2"])
		stderr($tracker_lang['error'], $tracker_lang['password_mismatch']);
	$username = sqlesc(htmlspecialchars($_POST["username"]));
	$password = $_POST["password"];
	$email = sqlesc(htmlspecialchars($_POST["email"]));
	$secret = mksecret();
	$passhash = sqlesc(md5($secret . $password . $secret));
	$secret = sqlesc($secret);
	
    $modcomment3 = date("Y-m-d") . " - был добавлен через админку $CURUSER[username].\n"; 
    $modcomment2 = sqlesc($modcomment3);
    
        $res3 = sql_query("SELECT id,class FROM users WHERE username=$username");
	$arr3 = mysql_fetch_array($res3);
	if ($arr3) {
		$username2 = htmlspecialchars($_POST["username"]);
	$life="<a href=\"userdetails.php?id=".$arr3["id"]."\">".get_user_class_color($arr3["class"], $username2)."</a>";
	//stderr($tracker_lang['error'], $tracker_lang['unable_to_create_account']);
		}

       
    
	sql_query("INSERT INTO users (added, last_access, secret, username, passhash, status, email,modcomment) VALUES(".sqlesc(get_date_time()).", ".sqlesc(get_date_time()).", $secret, $username, $passhash, 'confirmed', $email,$modcomment2)");
	
		
	    if (mysql_errno() == 1062){
       stderr("Ошибка", "Пользователь с таким именем уже существует: <b>$life</b>!"); 
       die; 
       }
	
	$res = sql_query("SELECT id FROM users WHERE username=$username");
	$arr = mysql_fetch_row($res);
//	if (!$arr)
	//	stderr($tracker_lang['error'], $tracker_lang['unable_to_create_account']);
	
//	define ('REGISTER', true);
  //  define ('GROUP', 2);
  //  define ('TYPE', 'add_forum_user');
  //  define ('ACTIVATION', 'no');
	
	$wantpassword = $_POST["password"];
	$wantusername = $_POST["username"];
	$id = $arr[0];
	$timezone = '+2';
	$enabledst = 'yes';
	$ip = getip();
	unset($email);
	$email = trim($_POST["email"]);
//	include_once('./include/community.php');
	header("Location: $DEFAULTBASEURL/userdetails.php?id=$id");
  	write_log("Пользователь ".$wantusername." был добавлен через админку - " . $CURUSER["username"],"000000","tracker");
	
	die;
}
stdhead($tracker_lang['add_user']);
?>
<h1><?=$tracker_lang['add_user'];?></h1>
<form method=post action=adduser.php>
<table border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead><?=$tracker_lang['username'];?></td><td><input type=text name=username size=40></td></tr>
<tr><td class=rowhead><?=$tracker_lang['password'];?></td><td><input type=password name=password size=40></td></tr>
<tr><td class=rowhead><?=$tracker_lang['repeat_password'];?></td><td><input type=password name=password2 size=40></td></tr>
<tr><td class=rowhead>E-mail</td><td><input type=text name=email size=40></td></tr>



<tr><td colspan=2 align=center><input type=submit value="OK" class=btn></td></tr>
</table>
</form>
<? stdfoot(); ?>