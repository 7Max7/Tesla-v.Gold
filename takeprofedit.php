<?
require_once("include/bittorrent.php");

function bark($msg) {
	stderr("Ошибка данных", $msg);
}

dbconn();


if(isset($_GET['country']) && !empty($CURUSER)) {
header('Content-Type: text/html; charset=windows-1251');

$codecity = (int) $_GET['country'];

$nau=0;
$res = sql_query("SELECT name, id FROM cities WHERE country_id = ".sqlesc($codecity)." ORDER BY name") or sqlerr(__FILE__, __LINE__);
while($row = mysql_fetch_array($res)){

echo "obj.options[obj.options.length] = new Option('".htmlspecialchars($row["name"])."','".$row["id"]."');\n"; 
++$nau;
}

if (empty($row) && $nau==0){
echo "obj.options[obj.options.length] = new Option('Нет городов для выбора','0');\n"; 
}

die;
}

loggedinorreturn();




//if (!mkglobal("oldpassword"))
//	bark("missing form data");

//global $avatarsize, $avatar_max_height,$avatar_max_width;


// $set = array();

$updateset = array();
//$changedemail = 0;
$usercomment=htmlspecialchars_uni($CURUSER["usercomment"]);

$ques=htmlspecialchars($_POST['ques']);
$answ=htmlspecialchars($_POST['answ']);
$resetquest=$_POST['resetquest']==1 ? "1":"0";

if (!empty($ques) && empty($answ) || !empty($answ) && empty($ques)){
 bark("Из данных о секретном вопросе вы не ввели: <b>".(empty($answ) && !empty($ques) ? "ответ на вопрос":"секретный вопрос для ответа")."</b>"); 
}

if ($ques && $answ) {
$row_secret=$CURUSER["added"];


$update_j=md5($row_secret.$answ.$row_secret);

$updateset[] = "rejoin = " . sqlesc($update_j); /// ответ на секретный вопрос с солью
$updateset[] = "question = " . sqlesc($ques); /// наш вопрос без хеша
$usercomment = gmdate("Y-m-d") . " - Установил новый секр. вопрос.\n" . $usercomment; 
}


//// сбиваем секретный вопрос и ответ у пользователя
if ($resetquest==1){
$updateset[] = "rejoin = ''"; /// ответ на секретный вопрос с солью
$updateset[] = "question = ''"; /// наш вопрос без хеша
}


$ip = getip(); 
if (!empty($_POST['oldpassword']) && !empty($_POST['chpassword'])) {

	 if ($CURUSER["passhash"] <> md5($CURUSER["secret"] . $_POST['oldpassword'] . $CURUSER["secret"]))
      bark("Вы ввели неправильный старый пароль.");
           
if ($_POST['chpassword'] <> $_POST['passagain']) {
bark("Новый пароль не совпадает с подтверждением его.");
}
else {

$newpass = $_POST['passagain'];
list($year2, $month2, $day2) = explode('-', $CURUSER["birthday"]);

if ($newpass == ($year2.$month2.$day2) || $newpass == ($day2.$month2.$year2) || $newpass == ($month2.$day2.$year2))
bark("Новый пароль совпадает вашим днем рождения, пожалуйста неиспользуйте эту комбинацию в пароле.");
elseif (strlen($newpass) <= 4)
bark("Новый пароль слишком короткий, пожалуйста используйте <b>более 4 символов</b> в пароле.");
elseif ($newpass == 12345 || $newpass == 54321 || $newpass == 11111 || $newpass == 123456)
bark("Новый пароль слишком простой (<b>наивный</b>), пожалуйста используйте сложные комбинации в пароле.");

$sec = mksecret();
$passhash = md5($sec . $newpass . $sec);
$updateset_pas[] = "secret = " . sqlesc($sec);
$updateset_pas[] = "passhash = " . sqlesc($passhash);

$usercomment = gmdate("Y-m-d") . " - Смена пароля (".$ip.").\n" . $usercomment; 

$body = <<<EOD
Данное письмо копия ваших данных на сайте.  
Вы, или кое кто другой (ip адрес равен $ip) сменил пароль:
Учетная запись: $CURUSER[username]
Введен новый пароль: $_POST[chpassword]
Если есть проблемы с новым паролем или кражи вашего аккаунта, пожалуйста свяжитесь с администратором(ами).

Вход доступен по адресу: $DEFAULTBASEURL/login.php
--- C уважением система сайта $SITENAME ---
EOD;

@sent_mail($CURUSER["email"],$SITENAME,$SITEEMAIL,"Смена пароля для ".$CURUSER["username"],$body,false);
$CURUSER["passhash"] = $passhash;
logincookie($CURUSER["id"], $passhash, $CURUSER["shelter"]);
sql_query("UPDATE users SET " . implode(",", $updateset_pas) . " WHERE id = ".$CURUSER["id"]) or sqlerr(__FILE__,__LINE__);


}

}

if (!empty($_POST["resetpass"])){
      
$chpassword = generatePassword();
$sec = mksecret();
$passhash = md5($sec . $chpassword . $sec);
$updateset[] = "secret = " . sqlesc($sec);
$updateset[] = "passhash = " . sqlesc($passhash);
		
$body = <<<EOD
Вы или кто то другой (ip адрес которого $ip) сбросил пароль.
Учетная запись: $CURUSER[username] 
Новый пароль: $chpassword
Если есть проблемы с новым паролем свяжитесь с администратором.

Вход доступен по адресу: $DEFAULTBASEURL/login.php
--- C уважением система сайта $SITENAME ---
EOD;

if (!sent_mail($CURUSER["email"],$SITENAME,$SITEEMAIL,"Смена пароля",$body,false)) {
bark("Невозможно отправить E-Mail. Если ошибка повторяется, пожалуйста, обратитесь к администратору.");
}
$usercomment = gmdate("Y-m-d") . " - Сброс пароля (".$ip.").\n" . $usercomment; 
}


if ($_POST["acceptpms"]=="yes" || $_POST["acceptpms"]=="friends" || $_POST["acceptpms"]=="no"){
$acceptpms = htmlentities($_POST["acceptpms"]);
}


$deletepms = ($_POST["deletepms"] != "" ? "yes" : "no"); /// yes no
$savepms = ($_POST["savepms"] != "" ? "yes" : "no");/// yes no

/*$pmnotif = $_POST["pmnotif"];
$emailnotif = $_POST["emailnotif"];
$notifs = ($pmnotif == 'yes' ? "[pm]" : "");
$notifs .= ($emailnotif == 'yes' ? "[email]" : "");

*/

$r = sql_query("SELECT id FROM categories") or sqlerr(__FILE__, __LINE__);
$rows = mysql_num_rows($r);
for ($i = 0; $i < $rows; ++$i){
	$a = mysql_fetch_assoc($r);
	if ($_POST["cat$a[id]"] == 'yes')
	  $notifs .= "[cat$a[id]]";
}

//загрузка аватарки
$avatar = $_POST["avatar"]; 
$maxfilesize = $avatarsize; // 50kb 

$allowed_types = array(
	"image/gif" => "gif",
    "image/png" => "png", 
	"image/jpeg" => "jpeg",
	"image/jpg" => "jpg"
); 


if (!empty($_FILES['avatar']['name'])) {
   if (!array_key_exists($_FILES['avatar']['type'], $allowed_types))
      bark("Это расширение файла не входит в доступный список расширений (не картинка)."); 
   if (!preg_match('/^(.+)\.(jpg|jpeg|png|gif)$/si', $_FILES['avatar']['name']))
	bark("Неверное расширение файла (не картинка).");
   list($width, $height) = getimagesize($_FILES['avatar']['tmp_name']);
   
   if ($width > $avatar_max_width || $height > $avatar_max_height) 
    bark("Картинка для аватара не может быть больше <b>".$avatar_max_width."x".$avatar_max_height."</b><br>
	Размеры заливаемой картинки под аватар: <b>".$width."x".$height."</b>
	");

   if ($_FILES['avatar']['size'] > $maxfilesize)
    bark("Картинка для аватара слишком велика! Max размер допустимой картинки: ".mksize($maxfilesize)); 
    
    $image=file_get_contents($_FILES['avatar']['tmp_name']);
    
     validimage($_FILES['avatar']['tmp_name'],"my.php");

     // Where to upload? 
     $uploaddir = ROOT_PATH."pic/avatar/"; 
  
     if (!empty($CURUSER['avatar'])){
		 $del=@unlink($uploaddir.$CURUSER['avatar']);
		}

      $ifilename = $CURUSER['id'].'.'.end(explode('.', $_FILES["avatar"]['name']));
      
     $ifile = $_FILES['avatar']['tmp_name'];

     $copy = copy($ifile, $uploaddir.$ifilename); 
     if (!$copy)
       bark("Ошибка при загрузке файла! Не могу скопировать файл в папку.");
       
      $updateset[] = "avatar = " . sqlesc($ifilename); 
      $usercomment = gmdate("Y-m-d") . " - Локальная загрузка аватара (".$ip.").\n" . $usercomment; 
}


if (!empty($_POST["avatar_inet"])) {

$avatar_inet = htmlentities($_POST["avatar_inet"]);

if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $avatar_inet)){

list($width_in, $height_in) = getimagesize($avatar_inet);

if ($width_in > $avatar_max_width || $height_in > $avatar_max_height)
bark("Картинка для аватара не может быть больше <b>".$avatar_max_width."x".$avatar_max_height."</b><br>
Размеры заливаемой картинки под аватар: <b>".$width_in."x".$height_in."</b>");
else {
		
$image = @file_get_contents($avatar_inet);

$size_in = strlen($image);


if ($size_in > $maxfilesize)
bark("Картинка для аватара слишком велика! Max размер допустимой картинки: ".mksize($maxfilesize)); 

elseif(!empty($image)) {

$uploars = ROOT_PATH."pic/avatar/";

if (!empty($CURUSER["avatar"]))
@unlink($uploars.$CURUSER["avatar"]);


$ifileid = $CURUSER["id"].'.'.end(explode('.',$avatar_inet));

$fiupload = file_put_contents($uploars.$ifileid,$image);

////////
$functions_to_shell = array ("include", "file", "fwrite", "script", "body", "java","fopen", "fread", "require", "exec", "system", "passthru", "eval", "copy" );

foreach ($functions_to_shell as $funct){

if (preg_match("/" . $funct ."+(\\s||)+[(]/", $image)) {
$usercomment=$CURUSER["usercomment"];
$poput=" $user пытался залить shell";

if (!stristr($usercomment,$poput)!==false){
$usercomment = get_date_time() . " пытался залить shell (my).\n". $usercomment;
sql_query("UPDATE users SET usercomment='$usercomment' WHERE id=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
}
write_log("$user пытался залить shell.","$user_color","error");
stderr("Ошибка данных", "Данное изображение нельзя залить на трекер, в нем присутствуют запрещенные исполняемые коды (v2)");
}
}
//////////////
if (!empty($fiupload)){
$updateset[] = "avatar = " . sqlesc($ifileid);
$usercomment = gmdate("Y-m-d") . " - Внешняя загрузка аватара (".$ip.").\n" . $usercomment; 
}
else
$updateset[] = "avatar = " . sqlesc("");
}

}
   
}
}



///////////////////////////////////////////////
$parked = $_POST["parked"]; // yes no

if ($_POST["parked"]== "yes"){
$parked='yes';	
} else {
$parked='no';	
}

if (!empty($_POST["tesla_guard"])){
if ($_POST["tesla_guard"]=="yes") { $psg="ag";} else { $psg="ip"; }
$updateset[] = "shelter = " . sqlesc($psg);
logincookie($CURUSER["id"], $CURUSER["passhash"], $psg);
}

$updateset[] = "parked = " . sqlesc($parked);

if ($_POST["hidecomment"]=="yes" || $_POST["hidecomment"]=="no"){
$hidecomment = $_POST["hidecomment"]; // yes no
$updateset[] = "hidecomment = " . sqlesc($hidecomment);
}

$gender = (int) $_POST["gender"]; // 1 2 3
$updateset[] = "gender =  " . sqlesc($gender);

$year = (int) $_POST["year"]; /// число
$month = (int) $_POST["month"];/// число
$day = (int) $_POST["day"];/// число
$birthday = date("$year.$month.$day");
$updateset[] = "birthday = " . sqlesc($birthday);


if (!empty($_POST['resetpasskey'])){
$usercomment = gmdate("Y-m-d") . " - Сбил себе пасскей (".$CURUSER["passkey"].").\n" . $usercomment; 
$updateset[] = "passkey=''";
$updateset[] = "modcomment = " . sqlesc($modcomment);
}

$info = htmlspecialchars_uni(strip_tags($_POST["info"]));

$re = sql_query("SELECT info, editinfo, signature, signatrue FROM users WHERE id = " . $CURUSER["id"]);
$ro = mysql_fetch_array($re);

//// инфа о себе   
if ($ro['info'] <> $info  && $ro['editinfo']=='no') {
	 bark("Вам запрещенно редактировать биографическую информацию о себе.");
} else {
$updateset[] = "info = " . sqlesc($info);
}
//// инфа о себе  


//// подпись
$signature = htmlspecialchars_uni(strip_tags($_POST["signature"]));

if ($ro['signature'] <> $signature  && $ro['signatrue']=='no') {
bark("Вам запрещенно редактировать подпись о себе.");
} else {
$updateset[] = "signature = " . sqlesc($signature);
}
//// подпись



$stylesheet = htmlspecialchars($_POST["stylesheet"]);

$country = (int) $_POST["country"];

$city = (int) $_POST["city"];  
//die("$country country и $city city");
/*$language = $_POST["language"];

if(!file_exists('./languages/lang_'.$language.'/lang_main.php'))
{
    bark('Выбранный язык в системе отсутствует!');
}  


$updateset[] = "language = " . sqlesc($language);*/

//$timezone = 0 + $_POST["timezone"];
//$dst = ($_POST["dst"] != "" ? "yes" : "no");

$icq = (int) unesc($_POST["icq"]);
if (strlen($icq) > 10)
    bark("Жаль, Номер icq слишком длинный  (Макс - 10)");
$updateset[] = "icq = " . sqlesc($icq);

$skype = unesc($_POST["skype"]);
if (strlen($skype) > 20)
    bark("Жаль, Ваш skype слишком длинный  (Макс - 20)");
$updateset[] = "skype = " . sqlesc(htmlspecialchars($skype));

$odnoklasniki = unesc($_POST["odnoklasniki"]);
if (strlen($odnoklasniki) > 300)
    bark("Извините, слишком длинное слово! [одноклассники]");  
$updateset[] = "odnoklasniki = " . sqlesc(htmlspecialchars($odnoklasniki));

$vkontakte = unesc($_POST["vkontakte"]);
if (strlen($vkontakte) > 300)
    bark("Извините, слишком длинное слово! [вконтакте]");
$updateset[] = "vkontakte = " . sqlesc(htmlspecialchars($vkontakte));




$website = htmlentities($_POST["website"]);
// фикс безопасности
if (preg_match('/^(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?/i', $website))
$updateset[] = "website = " . sqlesc($website);



$torrentsperpage = (int) $_POST["torrentsperpage"];
$postsperpage = (int) $_POST["postsperpage"];

$updateset[] = "torrentsperpage = " . min(100, $torrentsperpage);
$updateset[] = "postsperpage = " . min(100, $postsperpage);

if (!is_numeric($stylesheet) && $CURUSER["stylesheet"]<>$stylesheet && file_exists(ROOT_PATH."themes/".$stylesheet."/template.php")) {

$res2 = sql_query("SELECT COUNT(*) AS num_themes FROM stylesheets WHERE name=".sqlesc($stylesheet)."") or sqlerr(__FILE__, __LINE__);
$name = mysql_fetch_array($res2);
if (!empty($name["num_themes"]))
$updateset[] = "stylesheet = ".sqlesc($stylesheet)."";
}

if (is_valid_id($country))
  $updateset[] = "country = ".sqlesc($country);

if (is_valid_id($city))
 $updateset[] = "city = ".sqlesc($city);


$updateset[] = "acceptpms = " . sqlesc($acceptpms);
$updateset[] = "deletepms = ".sqlesc($deletepms);
$updateset[] = "savepms = ".sqlesc($savepms);
$updateset[] = "notifs = ".sqlesc($notifs);

//$updateset[] = "avatar = " . sqlesc($avatar);
$updateset[] = "usercomment = ".sqlesc(htmlspecialchars_uni($usercomment));

sql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]) or sqlerr(__FILE__,__LINE__);

if (!empty($passhash))
header("Location: $DEFAULTBASEURL/my.php?edited=2");
else
header("Location: $DEFAULTBASEURL/my.php?edited=1");
?>