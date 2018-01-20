<?
require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();
///accessadministration(); 

function puke($text = "Вы что то здесь забыли?") {
	global $tracker_lang; 
	stderr($tracker_lang['error'], $text);
}

if (get_user_class() < UC_MODERATOR) {
attacks_log('modtask'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die(); 
}



	
	$userid = (int) $_POST["userid"]; /// число
	$title = htmlspecialchars(strip_tags($_POST["title"]));
	

	$enabled = (!empty($_POST["enabled"]) ? $_POST["enabled"]:""); /// yes no
	$warned = (!empty($_POST["warned"])? $_POST["warned"]:"");  /// yes no
	$warnlength = (int) $_POST["warnlength"]; /// число
	$warnpm = htmlspecialchars($_POST["warnpm"]);  /// причина предупреждения
	
	
	$uploadtoadd = $_POST["amountup"]; //plus minus
	$downloadtoadd=  $_POST["amountdown"];//plus minus
	$formatup = $_POST["formatup"]; // mb gb
	$formatdown = $_POST["formatdown"];  // mb gb


   //слоты и приглашения
     $invitetoadd= (int) $_POST["amountinvite"]; /// число
     $mpinvite = $_POST["invitechange"]; // plus munis
	 //слоты и приглашения


	$mpup = ($_POST["upchange"]=="plus" ? "plus":"minus"); // plus minus
	$mpdown = ($_POST["downchange"]=="plus" ? "plus":"minus"); //plus minus
	
//	die(" $mpup  и $mpdown");
	
	$support = htmlspecialchars($_POST["support"]); // yes no
	
	if ($CURUSER["class"]>=UC_ADMINISTRATOR){
    $groups = (int) $_POST["groups"]; /// ID 
    }
    
	$supportfor = htmlspecialchars_uni($_POST["supportfor"]); /// убираем спец символы
	$modcomm = htmlspecialchars_uni($_POST["modcomm"]); /// убираем спец символы
	
	$deluser = (!empty($_POST["deluser"])?$_POST["deluser"]:""); 
    $reset_user_ratio = (!empty($_POST["reset_user_ratio"])? (int) $_POST["reset_user_ratio"]:0); /// 1 0
    $user_reliases_anonim = (!empty($_POST["user_reliases_anonim"])? (int) $_POST["user_reliases_anonim"]:0); /// 1 0
    $reset_user_torrents = (!empty($_POST["reset_user_torrents"])? (int) $_POST["reset_user_torrents"]:0); /// 1 0
    
    $release_set_id = (!empty($_POST["release_set_id"]) ? (int) $_POST["release_set_id"]:""); /// число
    
    $release_unset_id = (!empty($_POST["release_unset_id"]) ? (int) $_POST["release_unset_id"]:""); /// число
	$class = (!empty($_POST["class"])? (int) $_POST["class"]:0); // число
	
	if ($class>UC_SYSOP) /// шлем на кто хочет выше 6 класса права
	stderr($tracker_lang['error'], "А вы уверенны, что хотите класс $class ?");
		
	if (!is_valid_id($userid) || !is_valid_user_class($class))
	stderr($tracker_lang['error'], "Неверный идентификатор пользователя или класса.");
	
	if (empty($_POST["supportfor"]) && $support=='yes')
	stderr($tracker_lang['error'], "Пожалуйста впишите данные в Описание Поддержки.");
	
	 if (empty($_POST["bookmcomment"]) && $_POST["addbookmark"]=='yes')
     stderr($tracker_lang['error'], "Вы не указали причину подозрения.");


/// выполняем запрос	
	$res = sql_query("SELECT avatar,stylesheet, email, ip, info, editinfo, signature, signatrue, donor, parked, warned, enabled, username, shoutbox, cansendpm, commentpos, override_class, class, modcomment, usercomment, invites, uploadpos, downloadpos, passhash, passkey, uploaded, downloaded, num_warned, hiderating, hidecomment, monitoring, forum_com FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
 

	$arr = mysql_fetch_assoc($res) or stderr("Ошибка данных", "Нет пользователя с таким id.");
	
if (get_user_class()<=UC_SYSOP && $arr["override_class"]=="6")
  stderr($tracker_lang['error'], "Вы не можете редактировать данного пользователя, его базовый класс : ".get_user_class_name($arr["override_class"])."");

    $real=$arr["username"];
	$curenabled = $arr["enabled"];
	$parked = $arr["parked"];  
	$hidecomment = $arr["hidecomment"];  
    $shoutbox = $arr["shoutbox"];

	if (get_user_class()<UC_ADMINISTRATOR)
        {
        $donor = $arr["donor"];
        $uploadpos = $arr["uploadpos"];
        $downloadpos = $arr["downloadpos"];
 
        $infouser = $arr["info"];
      
        
        }
        else
        {
        $infouser=  htmlspecialchars_uni(strip_tags($_POST["info"]));
        $signature=  htmlspecialchars_uni(strip_tags($_POST["signature"]));
        $donor = ($_POST["donor"]=="yes"?"yes":"no"); /// yes no
        $uploadpos = $_POST["uploadpos"];/// yes no
        $downloadpos = $_POST["downloadpos"];/// yes no

        // Рейтинг - 100
        $hiderating = (!empty($_POST["hiderating"])? (string) $_POST["hiderating"]:""); /// yes no
        $hidelength = (int) $_POST["hidelength"];  
        // Рейтинг - 100 
        }

        $curuploadpos = $arr["uploadpos"];
        $curdownloadpos = $arr["downloadpos"];
        $curclass = $arr["class"];
        $curwarned = $arr["warned"];
        // Рейтинг - 100 
        $curhiderating = $arr["hiderating"];
    
      	if (get_user_class() == UC_SYSOP) {
		$modcomment = htmlspecialchars_uni($_POST["modcomment"]);
        }
    	else
		$modcomment = $arr["modcomment"];
		$usercomment = $arr["usercomment"];
		
		if (get_user_class() > UC_MODERATOR){
        $addbookmark = (string) $_POST["addbookmark"]; /// yes no
        $bookmcomment = htmlspecialchars_uni(strip_tags($_POST["bookmcomment"]));
        
    	$updateset[] = "bookmcomment = " . sqlesc($bookmcomment);
        $updateset[] = "addbookmark = " . sqlesc($addbookmark);  
        }
        
         // поднимать выше своего статуса <- НЕЛЬЗЯ -> редактировать оДинаковых по статусу.
        if($CURUSER['id'] <> $userid) {
        if ($curclass >= get_user_class() || $class >= get_user_class()) 
        puke('Не туда лезешь, тут уже патченно...'); 
		}
    

    if (get_user_class() == UC_SYSOP) {

        	if (!empty($_POST['desysteml'])){
		    $dt = sqlesc(get_date_time(gmtime() - 111*86400));
          	/// автозачистка системой как неактивный \ отключенный 
        	$updateset[] = "override_class = 255";
        	$updateset[] = "last_access = ".sqlesc($dt);
        	$updateset[] = "class = " . sqlesc(0);
        	$modcomment = date("Y-m-d") . " поставлен ультиматум - " . $CURUSER['username'] ." удалится аккаунт в течении 600 секунд с 13 по 18 часов дня.\n".$modcomment;
        	}

          if (!empty($_POST['resettestclass'])=="yes" && $arr["override_class"]<>255)
             {
             $updateset[] = "override_class = 255";
             $updateset[] = "class = " . sqlesc($arr["override_class"]);
             $updateset[] = "promo_time = " . sqlesc(get_date_time());
             }
             
        	
        	//сбросс пасса
   	      if (!empty($_POST['resetpass'])) 
             {
                $chpassword = generatePassword();
                $sec = mksecret();
		    $passhash = md5($sec . $chpassword . $sec);
		    $updateset[] = "secret = " . sqlesc($sec);
		    $updateset[] = "passhash = " . sqlesc($passhash);
		    $modcomment = date("Y-m-d") . " - Пароль сброшен пользователем " . $CURUSER['username'] ."\n".$modcomment;
$body = <<<EOD
Ваш пароль был сброшен.

Ваши новые данные для аккаунта:

Пользователь: {$arr["username"]}
Новый пароль: $chpassword

Войти на сайт: $DEFAULTBASEURL/login.php
Если есть проблемы с новым паролем свяжитесь с администратором или техподдержкой сайта.
--
$SITENAME
EOD;
   if (!sent_mail($arr["email"],$SITENAME,$SITEEMAIL,"Смена пароля",$body,false)) {
   die("Невозможно отправить E-Mail. Попробуйте позже"); 
   }
         } //сбросс пасса
         
         
         if ($arr["monitoring"]=='no' && $_POST['monitoring']=='yes' && empty($_POST['monitoring_reason']))
		  {
		  //	accessadministration(); 
          	stderr("Ошибка связи", "Причина для мониторинга не может быть пустой.");
          }
          
         
          if ($arr["monitoring"]=='no' && $_POST['monitoring']=='yes' && $_POST['monitoring_reason']!='')
		  {
accessadministration(); 
$sf=ROOT_PATH."cache/monitoring_$userid.txt"; 
$fpsf=fopen($sf,"a+"); 
$date = date("d.m.y"); 
$time= date("H:i:s"); 
fputs($fpsf,"Начало. Причина: ".format_comment($_POST['monitoring_reason'])."##".$arr["ip"]."##$date $time\n"); 

fclose($fpsf);

          $updateset[] = "monitoring = " . sqlesc(yes);   
          }
          
          if ($arr["monitoring"]=='yes' && $_POST['monitoring']=='no')
		  {
		  accessadministration(); 
		  $sf =ROOT_PATH."cache/monitoring_$userid.txt"; 
$fpsf=fopen($sf,"a+"); 
$date = date("d.m.y"); 
$time= date("H:i:s"); 
//fputs($fpsf,"-------конец слежения $date: $time-----\n\n"); 
fputs($fpsf,"Конец мониторинга.##".$arr["ip"]."##$date: $time\n"); 

fclose($fpsf);

		  $updateset[] = "monitoring = " . sqlesc(no);   
          }
          
          if (!empty($_POST['prune_monitoring'])=='yes')
		  {
		  accessadministration(); 
          @unlink(ROOT_PATH."cache/monitoring_$userid.txt");
		  }
		}
		

if (!empty($_POST['resetsession']) && get_user_class() == UC_SYSOP) {
   	accessadministration(); 
sql_query("DELETE FROM sessions WHERE ip = ".sqlesc($arr["ip"])." OR uid = ".sqlesc($userid)."") or sqlerr(__FILE__,__LINE__);
   }
   
 
 // инвайты
  	if($invitetoadd > 0) {
		if ($mpinvite == "plus")
 	      	$newinvite = $arr["invites"]+$invitetoadd;
		else
			$newinvite = $arr["invites"]-$invitetoadd;
		if ($newinvite < 0)
			stderr($tracker_lang['error'], "Вы хотите отнять у пользователя приглашений больше чем у него есть!");
		$updateset[] = "invites = $newinvite";
		$modcomment = date("Y-m-d") . " - $CURUSER[username] ".($mpinvite == "plus" ? "добавил " : "отнял ").$invitetoadd."  приглашения(-ий).\n". $modcomment;
	}
// инвайты 


/// сбить день рождение
$resetb = $_POST["resetb"]; /// yes
if ($resetb=='yes')
$updateset[] = "birthday = '0000-00-00'";
 
// Отнимаем разницу между накруткой и общим залитым ранее.
if (!empty($_POST["reschitup"]) && get_user_class() >= UC_ADMINISTRATOR){
$re = sql_query("SELECT SUM(upthis) AS upthiss  FROM cheaters WHERE userid=$userid") or sqlerr();
$ro = mysql_fetch_assoc($re); 
$totaluploaded=mksize($ro["upthiss"]);
$otherer=$arr["uploaded"]-$ro["upthiss"];
$modcomment = gmdate("Y-m-d") . " - " . $CURUSER['username'] . ", снял разницу $totaluploaded в накрутке рейтинга.\n" . $modcomment;
sql_query("UPDATE users SET uploaded = " . sqlesc($otherer)."  WHERE id = $userid") or sqlerr();
$arr["uploaded"]=$otherer;
	}



	if($uploadtoadd > 0) {
		if ($mpup == "plus")
			$newupload = $arr["uploaded"] + ($formatup == "mb" ? ($uploadtoadd * 1048576) : ($uploadtoadd * 1073741824));
		else
			$newupload = $arr["uploaded"] - ($formatup == "mb" ? ($uploadtoadd * 1048576) : ($uploadtoadd * 1073741824));
			
		if ($newupload < 0 && $mpup == "minus")
			stderr($tracker_lang['error'], "Вы хотите отнять у пользователя отданого больше чем у него есть!");
			
		$updateset[] = "uploaded = $newupload";
		$modcomment = date("Y-m-d") . " - $CURUSER[username] ".($mpup == "plus" ? "добавил " : "отнял ").$uploadtoadd.($formatup == "mb" ? " МБ" : " ГБ")." к раздаче.\n". $modcomment;
	}

	if($downloadtoadd > 0) {
		if ($mpdown == "plus")
			$newdownload = $arr["downloaded"] + ($formatdown == "mb" ? ($downloadtoadd * 1048576) : ($downloadtoadd * 1073741824));
		else
			$newdownload = $arr["downloaded"] - ($formatdown == "mb" ? ($downloadtoadd * 1048576) : ($downloadtoadd * 1073741824));
			
		if ($newdownload < 0 && $mpdown == "minus")
			stderr($tracker_lang['error'], "Вы хотите отнять у пользователя скачаного больше чем у него есть!");
			
		$updateset[] = "downloaded = $newdownload";
		$modcomment = date("Y-m-d") . " - $CURUSER[username] ".($mpdown == "plus" ? "добавил " : "отнял ").$downloadtoadd.($formatdown == "mb" ? " MB" : " GB")." к скачаному.\n". $modcomment;
	}

	
	$warndown = $_POST["warnchange"]; // plus minus
    $warntoadd=  (int) $_POST["amountwarn"]; // число
     
    if($warntoadd > 0) {
        if ($warndown == "plus") 
            $newwarn = $arr["num_warned"] + $warntoadd; 
        else 
            $newwarn = $arr["num_warned"] - $warntoadd; 
        if ($newwarn < 0)
            stderr($tracker_lang['error'], "Вы хотите отнять у пользователя предупреждений больше чем у него есть!"); 
        $updateset[] = "num_warned = $newwarn"; 
        $modcomment = date("Y-m-d") . " - $CURUSER[username] ".($warndown == "plus" ? "добавил " : "отнял ").$warntoadd ." к предупреждениям.\n". $modcomment; 
    }
	


if ($curclass <> $class && $userid <> $CURUSER['id']) {
       
	   if ($_POST["ls_system"]=="yes") {
	   $sender_id="0";
	   $system_ls="системой";
	   }
	   else {
	   $sender_id=$CURUSER['id'];
	   $system_ls="пользователем [color=#".get_user_rgbcolor($CURUSER["class"],$CURUSER["username"])."]$CURUSER[username][/color]";
	   }
	    // оповещаем пользователя
        $what = ($class > $curclass ? "повышены" : "понижены");
        $msg = sqlesc("Вы были [b] $what [/b] до класса [color=#".get_user_rgbcolor($class,$username)."]" . get_user_class_name($class) . "[/color] $system_ls.");
        $added = sqlesc(get_date_time());
        $subject = sqlesc("Вы были $what");
        sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES($sender_id, $userid, $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
        
        $updateset[] = "class = $class";
        $updateset[] = "promo_time = " . sqlesc(get_date_time());
        $what = ($class > $curclass ? "Повышен" : "Понижен");
        $modcomment = date("Y-m-d") . " - $what до класса " . get_user_class_name($class) . " пользователем $CURUSER[username].\n". $modcomment;
    }


$chatban=(int) $_POST["schoutboxpos"];
if (!empty($chatban) && $shoutbox=="0000-00-00 00:00:00"){

 if ($chatban == 65535) {
$modcomment = gmdate("Y-m-d") . " - Поставлен бан на чат на много много лет от " . $CURUSER['username'] . ".\n" . $modcomment;

$chatbanutil = get_date_time(gmtime() + 86400 * $chatban);
$updateset[] = "shoutbox = '$chatbanutil'"; 

} else {

$chatbanutil = get_date_time(gmtime() + 86400 * $chatban);
$forum_dur = $chatban . " " . ($chatban > 1 ? "дней(я)" : "день");
$modcomment = gmdate("Y-m-d") . " - Бан на чат на $forum_dur от " . $CURUSER['username'] .".\n". $modcomment; 
$updateset[] = "shoutbox = '$chatbanutil'"; 
    }
}

if (!empty($_POST["chat_pos_off"])) {
$modcomment = gmdate("Y-m-d") . " - Сбросил бан в чате - " . $CURUSER['username'] . ".\n" . $modcomment;

$chatbanutil = get_date_time(gmtime() + 86400 * $chatban);
$updateset[] = "shoutbox = '0000-00-00 00:00:00'";
}

// запрет написания лс и комментов
if (get_user_class() >= UC_ADMINISTRATOR){
	
	
	
/*
$avatar = $_POST["avatar"]; 
if ($avatar){
$maxfilesize = $avatarsize; // 50kb 

$allowed_types = array(
	"image/gif" => "gif",
    "image/png" => "png", 
	"image/jpeg" => "jpg",
	"image/jpg" => "jpg" 

); 

die($_FILES["avatar"]["type"]);
   if (!array_key_exists($_FILES['avatar']['type'], $allowed_types))
       stderr($tracker_lang['error'], "Неверное имя файла (не картинка)."); 
   if (!preg_match('/^(.+)\.(jpg|png|gif)$/si', $_FILES['avatar']['name']))
	 stderr($tracker_lang['error'], "Неверное расширение файла (не картинка).");
   list($width, $height) = getimagesize($_FILES['avatar']['tmp_name']);
  // if ($width > $avatar_max_width || $height > $avatar_max_height)
  //  bark("Аватар не может быть больше ".$avatar_max_width."x".$avatar_max_height);
  // if ($_FILES['aavatar']['size'] > $maxfilesize)
   // bark("Слишком большой объем аватара! Аватар должен быть меньше чем 50kb"); 
    $image=file_get_contents($_FILES['avatar']['tmp_name']);
    validimage($_FILES['avatar']['tmp_name'],"my.php");
     
     $uploaddir = "pic/avatar/"; 
  //   $id=$CURUSER["id"];
    // $res = sql_query("SELECT avatar FROM users WHERE id = $id")or die("Неверный запрос!!");
//	 $row = mysql_fetch_array($res);
     if ($arr["avatar"])
           {
		 @unlink($uploaddir.$arr["avatar"]);
        	}

     $ifilename = $userid.substr($_FILES["avatar"]["name"],  strlen($_FILES["avatar"]['name'])-4,4); 
     $ifile = $_FILES['avatar']['tmp_name']; 
     $copy = copy($ifile, $uploaddir.$ifilename); 
     if (!$copy)
        stderr($tracker_lang['error'],"Ошибка при загрузке файла!");
       die($ifilename);
      $updateset[] = "avatar = " . sqlesc($ifilename); 
      
	  ///ставим авик и на форум
      if ($ifile){
     sql_query("UPDATE ".TABLE_PREFIX."users SET avatar='/pic/avatar/".$ifilename."'
	 , avatartype= 'upload', avatardimensions='".$width."|".$height."'
	 WHERE uid = ".sqlesc($userid)) or sqlerr(__FILE__,__LINE__);
	 }
	 

}
	*/
	
	
	
	
	
	
	if ($_POST["cansendpm"]){
	$curcansendpm = $arr["cansendpm"];  
	$cansendpm = $_POST["cansendpm"];

      if ($cansendpm == 'yes' && $curcansendpm=='no')  
            {
           	$updateset[] = "cansendpm = 'yes'";
            $modcomment = gmdate("Y-m-d") . " - Вкл возможность отправки лс - " . $CURUSER['username'] . ".\n" . $modcomment;  
             }
      if ($cansendpm == 'no' && $curcansendpm=='yes')  
            {	
            $updateset[] = "cansendpm = 'no'";
            $modcomment = gmdate("Y-m-d") . " - Откл возможность отправки лс - " . $CURUSER['username'] . ".\n" . $modcomment;  
            }
}

	if ($_POST["commentpos"]){
	$arrcomment = $arr["commentpos"];  
	$commentpos = $_POST["commentpos"];

      if ($commentpos == 'yes' && $arrcomment=='no')  
            {
           	$updateset[] = "commentpos = 'yes'";
            $modcomment = gmdate("Y-m-d") . " - Вкл возможность отправки комментов - " . $CURUSER['username'] . ".\n" . $modcomment;  
             }
      if ($commentpos == 'no' && $arrcomment=='yes')
            {
            $updateset[] = "commentpos = 'no'";
            $modcomment = gmdate("Y-m-d") . " - Откл возможность отправки комментов - " . $CURUSER['username'] . ".\n" . $modcomment;  
            }
}



////


// Обнулить рейтинг
 if (!empty($_POST["reset_user_ratio"])){
    mysql_query("UPDATE users SET uploaded = 0, downloaded = 0 WHERE id = $userid") 
	or sqlerr(__FILE__, __LINE__); 
	$modcomment = date("Y-m-d") . " - $CURUSER[username] обнулил рейтинг.\n". $modcomment;
}

}



// Обнулить одобрения
 if (get_user_class() > UC_ADMINISTRATOR && !empty($_POST["reset_moderatedby"])){
 
 	$res6 = sql_query("SELECT id FROM torrents WHERE moderated = 'yes' and moderatedby='$userid'");

	while ($row6 = mysql_fetch_assoc($res6)) {
 	//die($row6);
 	$row6=$row6["id"];

	$updateset3[] = "moderated = 'no'";
    $updateset3[] = "moderatedby = ''";
 	
sql_query("UPDATE torrents SET " . implode(", ", $updateset3) . " WHERE id = '$row6'") or sqlerr(__FILE__, __LINE__);
}
	$modcomment = date("Y-m-d") . " - $CURUSER[username] обнулил все одобрения.\n". $modcomment;
}

//Удалить скаченные/отданные торренты 
if (!empty($_POST["reset_user_torrents"])){
    sql_query("DELETE FROM snatched WHERE userid = $userid") or sqlerr(__FILE__,__LINE__);
	$modcomment = date("Y-m-d") . " - $CURUSER[username] сбил все скач-отдан торренты.\n". $modcomment; 
}
// смена имени
if (get_user_class() >= UC_ADMINISTRATOR && !empty($_POST["username"])) {
if ($_POST["username"][0]==" "){
stderr("Ошибка", "Имя начинается с пробела или состоит из пробелов!");  
die; 
}

if (!empty($_POST["username"])){
	
	$p_name=htmlspecialchars($_POST["username"]);
	
	if (!validusername($p_name))
    stderr("Ошибка", "Неверный логин пользователя.");  
    elseif (strlen($p_name) > 12)
    stderr("Ошибка", "Логин пользователя не должен быть более 12 символов.");  

    $username = sqlesc($p_name); 
    
    $res = sql_query("SELECT username FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__); 
    $row = mysql_fetch_array($res); 
    $modcomment = date("Y-m-d") . " - Переименован из ".$row['username']." в ".$p_name." пользователем " . $CURUSER['username'].".\n" . $modcomment; 

    if ($row['username'] == $_POST['username']){ 
    stderr("Ошибка", "Этот пользователь уже имеет имя <b>".$_POST['username']."</b>.");  
    die; 
    }

    sql_query("UPDATE users SET username=$username WHERE id = $userid"); 
    if (mysql_errno() == 1062){ 
    stderr("Ошибка", "Пользователь с именем: <b>".$_POST['username']."</b> - уже зарегистрирован!"); 
    die; 
    }



	}
	}
	
	
	if (get_user_class() >= UC_ADMINISTRATOR)
	 {
	 	

$r = sql_query("SELECT id FROM categories") or sqlerr(__FILE__, __LINE__);
$rows = mysql_num_rows($r);
for ($i = 0; $i < $rows; ++$i)
{
	$a = mysql_fetch_assoc($r);
	if (!empty($_POST["cat$a[id]"]) == 'yes')
	  $catedit.= "[cat$a[id]]";
}
     if (!empty($catedit))
	$updateset[] = "catedit = '$catedit'";
}
	
	
	
	
//Сделать релизы пользователя анонимными 
if (!empty($_POST["user_reliases_anonim"])){
    sql_query("UPDATE torrents SET owner = 0 WHERE owner = $userid") or sqlerr(__FILE__,__LINE__); 
    $modcomment = date("Y-m-d") . " - $CURUSER[username] сделал заливки пользователя анонимными.\n". $modcomment;
}
if (!empty($_POST["user_reliases_anonim"]) && !empty($_POST["release_unset_id"])){ 
    stderr("Ошибка", "Вы уже сделали <b>все релизы пользователя</b> анонимными!");  
    die; 
    }

if ((!empty($_POST["release_set_id"]) and !empty($_POST["release_unset_id"])) && ($_POST["release_set_id"] == $_POST["release_unset_id"])){ 
    stderr("Ошибка", "Вы указали <b>одинаковые id</b> релизов!");  
    die; 
    }
//Сделать новым автором релиза 
if ($_POST["release_set_id"]){
    $setid = (int) $_POST["release_set_id"];                       
    $res = sql_query("SELECT id FROM torrents WHERE id = $setid") or sqlerr(__FILE__, __LINE__); 
    $row = mysql_fetch_array($res); 
    $modcomment = date("Y-m-d") . " - $CURUSER[username] присвоил торрент id=$setid этому юзеру.\n". $modcomment;
    if (!$row){ 
    stderr("Ошибка", "Торрента с указанным id: <b>$setid</b> - не существует!");  
    die; 
    }
    sql_query("UPDATE torrents SET owner = $userid WHERE id = $setid") or sqlerr(__FILE__,__LINE__); 
}
//Сделать анонимом в релизе 
if ($_POST["release_unset_id"])
{ 
    $unsetid = (int) $_POST["release_unset_id"];                       
    $res = sql_query("SELECT id, owner FROM torrents WHERE id = $unsetid AND owner = $userid") or sqlerr(__FILE__, __LINE__); 
    $row = mysql_fetch_array($res); 
    if ($row["$userid"] = ""){
    stderr("Ошибка", "Торрента с указанным id: <b>$unsetid</b> - не существует!");  
    die; 
    }
    if ($row["owner"] != $userid){ 
    stderr("Ошибка", "Торрент id: <b>$unsetid</b> - не принадлежит этому пользователю или уже анонимен!");    die; 
    }
    sql_query("UPDATE torrents SET owner = 0 WHERE id = $unsetid") or sqlerr(__FILE__,__LINE__); 
    $modcomment = date("Y-m-d") . " - $CURUSER[username] сделал автора анонимным в id=$unsetid релизе.\n". $modcomment;
}


if (get_user_class() > UC_MODERATOR){

if (isset($_POST["editinfo"])){
	$arr_editinfo = $arr["editinfo"];
    $editinfo = $_POST["editinfo"];                       
if ($editinfo=='yes' && $arr_editinfo=='no')
	{
    $updateset[] = "editinfo = 'yes'";
	$modcomment = date("Y-m-d") . " - $CURUSER[username] разрешил редактировать инфу пользователю.\n". $modcomment;
	}
if ($editinfo=='no' && $arr_editinfo=='yes')
    {
	$updateset[] = "editinfo = 'no'";
	$modcomment = date("Y-m-d") . " - $CURUSER[username] запретил редактировать инфу пользователю.\n". $modcomment;
	}
}

if (isset($_POST["signatrue"])){

if ($arr["signatrue"]=='yes' && $_POST["signatrue"]=='no')
	{
    $updateset[] = "signatrue = 'no'";
	$modcomment = date("Y-m-d") . " - $CURUSER[username] запретил редактировать подпись пользователю.\n". $modcomment;
	}
if ($arr["signatrue"]=='no' && $_POST["signatrue"]=='yes')
    {
	$updateset[] = "signatrue = 'yes'";
	$modcomment = date("Y-m-d") . " - $CURUSER[username] разрешил редактировать подпись пользователю.\n". $modcomment;
	}
	//die($arr["signatrue"]);
}



}


$num_warned = 1 + $arr["num_warned"]; //мод кол-ва предупреждений 

	if ($warned && $curwarned != $warned) 
	{
		$updateset[] = "warned = " . sqlesc($warned);
		$updateset[] = "warneduntil = '0000-00-00 00:00:00'";
		$subject = sqlesc("Ваше предупреждение снято");
		if ($warned == 'no')
		{
			$modcomment = date("Y-m-d") . " - Предупреждение снял пользователь " . $CURUSER['username'] . ".\n". $modcomment;
			$msg = sqlesc("Ваше предупреждение снял пользователь " . $CURUSER['username'] . "."
			."\n\n Желаете проверить ваш [url=$DEFAULTBASEURL/mywarned.php]Уровень предупреждений[/url] ?");
		
		sql_query("UPDATE users SET num_warned=num_warned-1 WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__,__LINE__);
		
		}
		if (!$_POST["warn_msg"]){
		$added = sqlesc(get_date_time());
		sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES (0, $userid, $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
		}

	}
	elseif ($warnlength)
	 {
		if (strlen($warnpm) == 0)
			stderr($tracker_lang['error'], "Вы должны указать причину по которой ставите предупреждение!");
	/*	if ($warnlength == 255) {
			$modcomment = date("Y-m-d") . " - Предупрежден пользователем " . $CURUSER['username'] . ".\nПричина: $warnpm\n" . $modcomment;
			$msg = sqlesc("Вы получили [url=$DEFAULTBASEURL/rules.php#warning]предупреждение[/url] на неограниченый срок от $CURUSER[username]" . ($warnpm ? "\n\nПричина: $warnpm" : ""));
			$updateset[] = "warneduntil = '0000-00-00 00:00:00'";
		} else {
			$warneduntil = get_date_time(gmtime() + $warnlength * 604800);
			$dur = $warnlength . " недел" . ($warnlength > 1 ? "и" : "ю");
			$msg = sqlesc("Вы получили [url=$DEFAULTBASEURL/rules.php#warning]предупреждение[/url] на $dur от пользователя " . $CURUSER['username'] . ($warnpm ? "\n\nПричина: $warnpm" : ""));
			$modcomment = date("Y-m-d") . " - Предупрежден на $dur пользователем " . $CURUSER['username'] .	".\nПричина: $warnpm\n" . $modcomment;
			$updateset[] = "warneduntil = '$warneduntil'";
		}*/
		
		
		
		if ($warnlength == 255) {
            $modcomment = date("Y-m-d") . " - Предупрежден неограниченно " . $CURUSER['username'] . ".\nПричина: $warnpm\n" . $modcomment; 
            $msg = sqlesc("Вы получили предупреждение на неограниченый срок" . ($warnpm ? "\nПричина: $warnpm" : "")
			."\n\n[b]PS[/b]: [i]Если у вас будет больше 5 предупреждений ([url=$DEFAULTBASEURL/mywarned.php]Уровень предупреждений[/url]) то вас автоматически отключит система.[/i]");
			
			$updateset[] = "warneduntil = '0000-00-00 00:00:00'"; 
            $updateset[] = "num_warned = '$num_warned'"; 
        } else 
		{
			$warneduntil = get_date_time(gmtime() + $warnlength * 604800); 
            $dur = $warnlength . " недел" . ($warnlength > 1 ? "и" : "ю"); 
            $msg = sqlesc("Вы получили предупреждение на $dur". ($warnpm ? "\nПричина: $warnpm" : "")
			."\n\n[b]PS[/b]: [i]Если у вас будет больше 5 предупреждений ([url=$DEFAULTBASEURL/mywarned.php]Уровень предупреждений[/url]) то вас автоматически отключит система.[/i]");
            $modcomment = date("Y-m-d") . " - Предупрежден на $dur " . $CURUSER['username'] . ".\nПричина: $warnpm\n" . $modcomment; 
            $updateset[] = "warneduntil = '$warneduntil'"; 
            $updateset[] = "num_warned = '$num_warned'";
        }
        
		if (!$_POST["warn_msg"]){
 		$added = sqlesc(get_date_time());
 		$subject = sqlesc("Вы получили предупреждение");
		sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES (0, $userid, $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
			}
		$updateset[] = "warned = 'yes'";
	}


// заливка доступ
if ($uploadpos <> $curuploadpos) {
if ($uploadpos == 'yes')
{
 $modcomment = gmdate("Y-m-d") . " - Закачка торрентов разрешена пользователем " . $CURUSER['username'] . ".\n" . $modcomment;
    $msg = sqlesc("Ваш бан на закачивание новых торрентов был снят. Вы снова можете закачивать торренты.");
//write_log("<font color=red>Пользователю <b>$username</b> было разрешено закачивать новые торренты пользователем <b><a href=userdetails.php?id=" . $CURUSER[id] . ">$CURUSER[username]</a></b>.</font>");
$added = sqlesc(get_date_time());
mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
else
{
 $modcomment = gmdate("Y-m-d") . " - Запрещено закачивать торренты пользователем " . $CURUSER['username'] . ".\n" . $modcomment;
    $msg = sqlesc("Вам запретили закачивать торренты, скорее всего этот бан вы получили за неправильное оформление торрентов.");
//write_log("<font color=orange><b>Пользователю <u>$username</u> было запрещено закачивать новые торренты пользователем <a href=userdetails.php?id=" . $CURUSER[id] . ">$CURUSER[username]</a>.</font></b>");
$added = sqlesc(get_date_time());
mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
}
// заливка доступ



if (get_user_class() >= UC_MODERATOR){
	
	
	$post_parked = $_POST["parked"];

      if ($post_parked == 'yes' && $parked=='no')  
            {
           	$updateset[] = "parked = 'yes'";
            $modcomment = gmdate("Y-m-d") . " - припаркован пользователем " . $CURUSER['username'] . ".\n" . $modcomment;  
             }
      if ($post_parked == 'no' && $parked=='yes')
	        {	
            $updateset[] = "parked = 'no'";
            $modcomment = gmdate("Y-m-d") . " - снята припарковка пользователем " . $CURUSER['username'] . ".\n" . $modcomment;  
            }



 //// скрытие комментариев в торрентах
	$post_hidecomment = $_POST["hidecomment"];

      if ($post_hidecomment == 'yes' && $hidecomment=='no')  
            {
           	$updateset[] = "hidecomment = 'yes'";
             }
      if ($post_hidecomment == 'no' && $hidecomment=='yes')
	        {	
            $updateset[] = "hidecomment = 'no'";
            }

}


// Рейтинг - 100
if (!empty($hiderating) && $curhiderating <> $hiderating){
	$updateset[] = "hiderating = " . sqlesc($hiderating); 
    $updateset[] = "hideratinguntil = '0000-00-00 00:00:00'"; 
    if ($hiderating == 'no') 
      { 
     $modcomment = gmdate("Y-m-d") . " - Без ограниченный рейтинг отключил " . $CURUSER['username'] . ".\n". $modcomment; 
      }
   }
    elseif (!empty($hidelength))
  {
    if ($hidelength == 255) 
    {
      $modcomment = gmdate("Y-m-d") . " - Без ограниченный рейтинг включил " . $CURUSER['username'] . ".\n" . $modcomment; 

$updateset[] = "hideratinguntil = '0000-00-00 00:00:00'"; 
        /// убрал деление на ноль если скачанно 0 байт
    }
    else 
    {
$hideratinguntil = get_date_time(gmtime() + $hidelength * 2678400); 
$dur = $hidelength . " месяц" . ($hidelength > 1 && $hidelength < 5? "а" : "ев"); 
$modcomment = gmdate("Y-m-d") . " - Без ограниченный рейтинг на $dur от " . $CURUSER['username'] .".\n". $modcomment; 
$updateset[] = "hideratinguntil = '$hideratinguntil'"; 
    }
    $updateset[] = "hiderating = 'yes'"; 
  }
  
  
  //// форум бан
  
//$_POST["forum_pos"] == 'yes' && $arr["forum_com"]=='no'
$warnfban=(int) $_POST["forum_pos"];
if ($warnfban<>0 && $arr["forum_com"]=="0000-00-00 00:00:00"){
      if ($warnfban == 64) 
    {
      $modcomment = gmdate("Y-m-d") . " - Поставлен бан на много много лет от " . $CURUSER['username'] . ".\n" . $modcomment;
$forumbanutil = get_date_time(gmtime() + 777 * 666666); 
$updateset[] = "forum_com = '$forumbanutil'"; 
    }
    else 
    {
$forumbanutil = get_date_time(gmtime() + $warnfban * 604800); 
$forum_dur = $warnfban . " недел" . ($warnfban > 1 ? "и" : "ю");
$modcomment = gmdate("Y-m-d") . " - Форум бан на $forum_dur от " . $CURUSER['username'] .".\n". $modcomment; 
$updateset[] = "forum_com = '$forumbanutil'"; 
    }
  }
  
  $fban=(!empty($_POST["forum_pos_off"])?(int) $_POST["forum_pos_off"]:0);
  if ($fban==1 && $arr["forum_com"]<>"0000-00-00 00:00:00")
  {
$modcomment = gmdate("Y-m-d") . " - Форум бан снял " . $CURUSER['username'] . ".\n" . $modcomment;
$updateset[] = "forum_com = '0000-00-00 00:00:00'"; 	
  }
  unset($fban); unset($warnfban);
  //// форум бан
  
  
  
  
  if ($CURUSER["id"]<>$userid) {
  	
	if ($enabled <> $curenabled) {
		$modifier = (int) $CURUSER['id'];
		if ($enabled == 'yes') {
			$nowdate = sqlesc(get_date_time());
			if (!isset($_POST["enareason"]) || empty($_POST["enareason"]))
				puke("Введите причину почему вы включаете пользователя!");
			$enareason = htmlspecialchars($_POST["enareason"]);
			$modcomment = date("Y-m-d") . " - Включен пользователем " . $CURUSER['username'] . ".\nПричина: $enareason\n" . $modcomment;
			//Пишем бан в лог 
            $res=@sql_query("SELECT * FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__); 
            $user = mysql_fetch_array($res); 
            $username = $user["username"]; 
            write_log("$username был Включен пользователем ".$CURUSER['username'].". Причина: $enareason","006800","bans");  
			
		} else {
			$date = sqlesc(get_date_time());
			$dateline = sqlesc(time());
			if (!isset($_POST["disreason"]) || empty($_POST["disreason"]))
				puke("Введите причину почему вы отключаете пользователя!");
			$disreason = htmlspecialchars($_POST["disreason"]);
			$modcomment = date("Y-m-d") . " - Отключен пользователем " . $CURUSER['username'] . ".\nПричина: $disreason\n" . $modcomment;
			
			//Пишем бан в лог 
            $res=@sql_query("SELECT * FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__); 
            $user = mysql_fetch_array($res); 
            $username = $user["username"]; 
            write_log("$username был Отключен пользователем ".$CURUSER['username'].". Причина: $disreason","bb3939","bans"); 
//  
		}
	}

	$updateset[] = "enabled = " . sqlesc($enabled);
	
	}
	
////////////////
	if (get_user_class() > UC_ADMINISTRATOR)
	 {
	if (!empty($_POST["trun_torrent"]))	{/// комментарии торрентов
	$num_torrent_t = number_format(get_row_count("comments", "WHERE offer='0' and torrent<>'0' and user = ".sqlesc($userid)));
    if (!empty($num_torrent_t)){
	sql_query("DELETE FROM comments WHERE offer='0' and torrent<>'0' and user = ".sqlesc($userid));
	
	$modcomment = date("Y-m-d") . " - $CURUSER[username] почистил все торрент комментарии ($num_torrent_t).\n" . $modcomment;
	}}
	
	if (!empty($_POST["trun_reqoff"])){ /// комментарии запросов
	$num_reqoff_t = number_format(get_row_count("comments", "WHERE torrent='0' and offer<>'0' and user = ".sqlesc($userid)));  
	if (!empty($num_reqoff_t)){
	sql_query("DELETE FROM comments WHERE torrent='0' and offer<>'0' and user = ".sqlesc($userid));
	$modcomment = date("Y-m-d") . " - $CURUSER[username] почистил все запрос комментарии ($num_reqoff_t).\n" . $modcomment;
	}}
	
	if (!empty($_POST["trun_news"])){ /// комментарии новостные
	$num_news_t = number_format(get_row_count("comments", "WHERE user = ".sqlesc($userid)." and torrent=0 and poll=0 and offer=0")); 
	if (!empty($num_news_t)){
	sql_query("DELETE FROM comments WHERE torrent=0 and poll=0 and offer=0 and user = ".sqlesc($userid));
	$modcomment = date("Y-m-d") . " - $CURUSER[username] почистил все новостные комментарии ($num_news_t).\n" . $modcomment;
	}}
	
	if (!empty($_POST["trun_polls"])){ /// комментарии опросов
	$num_polls_t = number_format(get_row_count("comments", "WHERE torrent=0 and news=0 and offer=0 and user=".sqlesc($userid))); 
	if (!empty($num_polls_t)){
	sql_query("DELETE FROM comments WHERE torrent=0 and news=0 and offer=0 and user = ".sqlesc($userid));
	$modcomment = date("Y-m-d") . " - $CURUSER[username] почистил все опрос комментарии ($num_polls_t).\n" . $modcomment;
	}}
	
	if (!empty($_POST["trun_inbox"])){ /// входящие
	$num_inbox_t = number_format(get_row_count("messages", "WHERE location=1 and receiver=".sqlesc($userid))); 	
	if (!empty($num_inbox_t)){
	sql_query("DELETE FROM messages WHERE location=1 and receiver=".sqlesc($userid));
	$modcomment = date("Y-m-d") . " - $CURUSER[username] почистил все входящие лс ($num_inbox_t).\n" . $modcomment;
	}}
	
	if (!empty($_POST["trun_outbox"])){ /// исходящие
	$num_outbox_t = number_format(get_row_count("messages", "WHERE saved='yes' AND sender=".sqlesc($userid))); 	
	if (!empty($num_outbox_t)){
	sql_query("DELETE FROM messages WHERE saved='yes' AND sender=".sqlesc($userid));
	$modcomment = date("Y-m-d") . " - $CURUSER[username] почистил все исходящие лс ($num_outbox_t).\n" . $modcomment;
	}}
	}
////////////////
	
	$stylesheet=(string)$_POST["stylesheet"]; 
	
	
  if (!is_numeric($stylesheet) && $arr["stylesheet"]<>$stylesheet) {
   $res2 = sql_query("SELECT COUNT(*) AS num_themes FROM stylesheets WHERE name=".sqlesc($stylesheet)."") or sqlerr (__FILE__, __LINE__);
$name = mysql_fetch_array($res2);
if (!empty($name["num_themes"]))
$updateset[] = "stylesheet = ".sqlesc($stylesheet);
}


    $updateset[] = "uploadpos = " . sqlesc($uploadpos);
    $updateset[] = "downloadpos = " . sqlesc($downloadpos);
    
    if (isset($infouser))
    $updateset[] = "info = " . sqlesc($infouser);
    
    if (isset($signature))
    $updateset[] = "signature = " . sqlesc($signature);
    
    if (isset($donor))
	$updateset[] = "donor = " . sqlesc($donor);
	
	if (isset($supportfor))
	$updateset[] = "supportfor = " . sqlesc($supportfor);
	$updateset[] = "support = " . sqlesc($support);
	
	// группы
    if ($CURUSER["class"] >=UC_ADMINISTRATOR && !empty($groups)) {
    $updateset[] = "groups = ".sqlesc($groups); 
    }
	// группы

	$updateset[] = "title = " . sqlesc($title);
	

$website = htmlentities(strip_tags($_POST["website"]));
// фикс безопасности
//if (preg_match('/^(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?/i', $website))
$updateset[] = "website = " . sqlesc($website);



	if (!empty($_POST['resetthemes'])=='yes')	{
    $updateset[] = "stylesheet = " . sqlesc($default_theme);
    $modcomment = date("Y-m-d") . " - $CURUSER[username] сбросил тему на $default_theme.\n" . $modcomment;
    }


	if (!empty($modcomm))
		$modcomment = date("Y-m-d") . " - Заметка от $CURUSER[username]: $modcomm\n" . $modcomment;
		
	$updateset[] = "modcomment = " . sqlesc(htmlspecialchars_uni($modcomment));
	if (!empty($_POST['resetkey'])) {
		$passkey = md5($CURUSER['username'].get_date_time().$CURUSER['passhash']);
		$updateset[] = "passkey = " . sqlesc($passkey);
	}
//аватарка
	if (!empty($_POST['resetavatar']))
    $updateset[] = "avatar = " . sqlesc("");


$usercom = htmlspecialchars_uni($_POST["usercomment"]);
if (get_user_class() > UC_MODERATOR && $usercomment<>$usercom){
$updateset[] = "usercomment = " . sqlesc($usercom);
}

	sql_query("UPDATE users SET	" . implode(", ", $updateset) . " WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
	
	 $delreason = htmlspecialchars($_POST["delreason"]);
	
	if (!empty($_POST["deluser"]) && empty($delreason))
	stderr($tracker_lang['error'], "Вы не указали причину по которой удаляете пользователя!");
	
	if (!empty($_POST["deluser"]) && !empty($delreason)) {

		$res=sql_query("SELECT * FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
		$user = mysql_fetch_array($res);
		$username = $user["username"];
		$email=$user["email"];

		if ($user["username"]==$CURUSER["username"]){
write_log("Пользователь $username пытался удалить себя. Причина: $delreason","590000","error");      stderr($tracker_lang['error'], "Ваше суицидное поведение было записано в журнал. В удалении <b>отказанно</b>.");
		}
		
		sql_query("DELETE FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
		sql_query("DELETE FROM messages WHERE receiver = $userid") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM friends WHERE userid = $userid") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM friends WHERE friendid = $userid") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM bookmarks WHERE userid = $userid") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM invites WHERE inviter = $userid") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM peers WHERE userid = $userid") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM simpaty WHERE fromuserid = $userid") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM checkcomm WHERE userid = $userid") or sqlerr(__FILE__,__LINE__);
		sql_query("DELETE FROM sessions WHERE uid = $userid") or sqlerr(__FILE__,__LINE__);
		
		$avatar=$user["avatar"];
		if (!empty($avatar))
		@unlink(ROOT_PATH."pic/avatar/$avatar");
		@unlink(ROOT_PATH."cache/monitoring_$userid.txt");
		
		
		$deluserid=$CURUSER["username"];
		
		$USER = sqlesc($CURUSER["id"]);
		$comment="$delreason ($username)";
		$email= $arr["email"];
	
        if (!empty($_POST["banemailu"])){
        sql_query("INSERT INTO bannedemails (added, addedby, comment, email) VALUES(".sqlesc(get_date_time()).",92, ".sqlesc($comment).", ".sqlesc($arr[email]).")"); 
		}
		
		write_log("Пользователь $username ($arr[email]) был удален пользователем $deluserid. Причина: $delreason","590000","bans");
		
		stderr("Успешно", "Пользователь удален, все необходимые данные были записанны в журнал.");
	} else {
	///	$returnto = htmlentities($_POST["returnto"]);
		header("Refresh: 0; url='userdetails.php?id=".$userid."'");
		die;
	}


puke();

?>