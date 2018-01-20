<?
require "include/bittorrent.php";
gzip();

// 0 - Без показа debug действия; 1 - SПоказать вывод и sql запрос 2 - Показать только sql запрос
$DEBUG_MODE = 0;

dbconn();
loggedinorreturn();

if (get_user_class() < UC_MODERATOR) {
attacks_log('usersearch'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

$timenow = "Пример даты: ".date("Y-m-d")." (сегодня), где ".date("Y")." - год, ".date("m")." - месяц, ".date("d")." - день.";


function mkdate($date){
  if (strpos($date,'-'))
  	$a = explode('-', $date);
  elseif (strpos($date,'/'))
  	$a = explode('/', $date);
  else
  	return 0;
  for ($i=0;$i<3;$i++)
  	if (!is_numeric($a[$i]))
    	return 0;
    if (checkdate($a[1], $a[2], $a[0]))
    	return  date ("Y-m-d", mktime (0,0,0,$a[1],$a[2],$a[0]));
    else
			return 0;
}



function ratios($up,$down, $color = True) {

if ($down > 0) {
$r = number_format($up / $down, 2);
if ($color)
$r = "<font color=".get_ratio_color($r).">$r</font>";
}
else
if ($up > 0)
$r = "Inf";
else
$r = "---";
return $r;
}

// checks for the usual wildcards *, ? plus mySQL ones
function haswildcard($text){
if (strpos($text,'*') === False && strpos($text,'?') === False && strpos($text,'%') === False && strpos($text,'_') === False)
return False;
else
return True;
}


stdheadchat("Административный поиск");

if ($_GET['h'])
echo "<table cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">
<tr>
<td valign=\"center\" class=\"b\"><b>Инструкция по применению в поиске значений</b>: <br>
<li>Пустые поля будут проигнорированы</li>
<li>Шаблоны * и ? могут быть использованы в Имени, Почта и Комментариях, так-же и в нескольких значениях разделенными пробелами (т.е. 'wyz Max*' в Имени выведет обоих пользователей
'wyz' и тех у которых имена начинаються на 'Max'. Похожим образом может быть использована '~' для отрицания, т.е. '~alfiest' в комментариях ограничит поиск пользователей
к тем у которых нету выражения 'alfiest' в их комментариях).</li>
<li>Поле Рейтинг принимает 'Inf' и '---' наравне с числовыми значениями.</li>
<li>Маска подсети может быть введена или в десятично точечной или CIDR записи
(т.е. 255.255.255.0 то-же самое что и /24).</li>
<li>Раздал и Скачал вводиться в GB.</li>
<li>Поиск с мультитекстовыми данными будут игнорированны, пожалуйста не используйте их.</li>
<li>'Только активных' ограничивает поиск к тем пользователям которые сейчас что-то качают или раздают,
'Отключенные IP' к тем чьи IP отключены.</li>
<li>The 'p' columns in the results show partial stats, that is, those
of the torrents in progress.</li>
</td></tr></table>";


$highlight = " class=\"a\"";

echo "<form method=get action=\"".$_SERVER["PHP_SELF"]."\">
<table cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">";


if (!$_GET['h'])
echo "<tr><td valign=\"center\" colspan=\"10\" class=\"b\"><a href='".$_SERVER["PHP_SELF"]."?h=1'>Инструкция по применению в поиске значений</a> &nbsp;-&nbsp; <a href='".$_SERVER["PHP_SELF"]."'>Сбросить поля</a></td></tr>";

echo "<tr>";

echo "<td valign=\"middle\" class=\"a\">Имя:</td>";

echo "<td ".($_GET['n'] ? $highlight:"")."><input name=\"n\" type=\"text\" value=\"".htmlspecialchars($_GET['n'])."\" size=\"15\"></td>";


echo "<td valign=\"middle\" class=\"a\">Рейтинг:</td>";

echo "<td ".($_GET['r']?$highlight:"").">";

echo "<select name=\"rt\">";
$options = array("равен","выше","ниже","между числами");

for ($i = 0; $i < count($options); $i++){
echo "<option value=".$i." ".(($_GET['rt']==$i)?"selected":"").">".$options[$i]."</option>\n";
}
echo "</select>";

echo " <input name=\"r\" title=\"Введите первое число, разделитель точка. Пример: 2.1\" type=\"text\" value=\"".htmlspecialchars($_GET['r'])."\" size=\"5\" maxlength=\"4\">";
echo " <input name=\"r2\" title=\"Введите второе число, разделитель точка. Пример: 2.1\" type=\"text\" value=\"".htmlspecialchars($_GET['r2'])."\" size=\"5\" maxlength=\"4\">";

echo "</td>";


echo "<td valign=\"middle\" class=\"a\">Статус:</td>";

echo "<td ".($_GET['st']?$highlight:"")."><select name=\"st\">";

$options = array("не имеет значения","Подтвержден","Не подтвержден");
for ($i = 0; $i < count($options); $i++){
echo "<option value=".$i." ".($_GET['st']==$i ? "selected":"").">".$options[$i]."</option>\n";
}
echo "</select>";
echo "</td>";



echo "</tr>";

echo "<tr>";

echo "<td valign=\"middle\" class=\"a\">Почта:</td>";

echo "<td ".($_GET['em'] ? $highlight:"").">";

if (get_user_class() > UC_ADMINISTRATOR)
echo "<input name=\"em\" type=\"text\" value=\"".htmlspecialchars($_GET['em'])."\" size=\"15\">";
else 
echo "<b>скрыта</b>";

echo "</td>";

echo "<td valign=\"middle\" class=\"a\">IP:</td>";

echo "<td ".($_GET['ip']?$highlight:"")."><input title=\"Введите ip адрес. Пример: ".$CURUSER["ip"]."\" name=\"ip\" type=\"text\" value=\"".htmlspecialchars($_GET['ip'])."\" size=\"15\" maxlength=\"15\"></td>";

echo "<td valign=\"middle\" class=\"a\">Отключен:</td>";

echo "<td ".($_GET['as'] ? $highlight:"").">";

echo "<select name=\"as\">";
$options = array("не имеет значения","Нет","Да");
for ($i = 0; $i < count($options); $i++){
echo "<option value=".$i." ".($_GET['as']==$i ? "selected":"").">".$options[$i]."</option>\n";
}
 
echo "</select>";

echo "</td>";
echo "</tr>";

echo "<tr>";
echo "<td valign=\"middle\" class=\"a\">Истории событий:</td>";

echo "<td ".($_GET['co']?$highlight:"")."><input name=\"co\" type=\"text\" value=\"".htmlspecialchars($_GET['co'])."\" size=\"30\"></td>";

echo "<td valign=\"middle\" class=\"a\">Маска:</td>";

echo "<td ".($_GET['ma']?$highlight:"")."><input title=\"Введите маску подсети, в десятично точечной или CIDR записи. Пример: 255.255.255.0 (тоже самое но в CIDR записи: /24)\" name=\"ma\" type=\"text\" value=\"".htmlspecialchars($_GET['ma'])."\" size=\"15\" maxlength=\"17\"></td>";

echo "<td valign=\"center\" class=\"a\">Класс:</td>";

echo "<td ".($_GET['c'] <> 1 ? $highlight:"").">";

echo "<select name=\"c\"><option value=\"1\">не имеет значения</option>";
  
if (!is_valid_id($_GET['c']))
$class = '';

$class = (int) $_GET['c'];
  
for ($i = 2;;++$i) {
if ($c = get_user_class_name($i-2))
echo "<option value=".$i.($class && $class == $i ? " selected" : "").">".$c."</option>\n";
else
break;
}

echo "</select>";
echo "</td>";
echo "</tr>";

echo "<tr>";

echo "<td valign=\"middle\" class=\"a\">Регистрация:</td>";

echo "<td ".($_GET['d'] ? $highlight:"").">";

echo "<select name=\"dt\">";

$options = array("в","раньше","после","между числами");
for ($i = 0; $i < count($options); $i++){
echo "<option value=".$i." ".($_GET['dt']==$i ? "selected":"").">".$options[$i]."</option>\n";
}
echo "</select> ";

echo "<input title=\"Начало даты, ".$timenow."\" name=\"d\" type=\"text\" value=\"".htmlspecialchars($_GET['d'])."\" size=\"12\" maxlength=\"12\"> ";
echo "<input title=\"Конец даты, ".$timenow."\" name=\"d2\" type=\"text\" value=\"".htmlspecialchars($_GET['d2'])."\" size=\"12\" maxlength=\"12\">";

echo "</td>";


echo "<td valign=\"middle\" class=\"a\">Раздал:</td>";

echo "<td ".($_GET['ul'] ? $highlight:"").">";

echo "<select name=\"ult\" id=\"ult\">";
$options = array("ровно","больше","меньше","между числами:");
for ($i = 0; $i < count($options); $i++){
echo "<option value=".$i." ".($_GET['ult']==$i ? "selected":"").">".$options[$i]."</option>\n";
}
echo "</select>";

echo " <input name=\"ul\" title=\"Введите число, разделитель точка. Пример: 2.5\" type=\"text\" id=\"ul\" size=\"6\" maxlength=\"7\" value=\"".htmlspecialchars($_GET['ul'])."\">";
echo " <input name=\"ul2\" title=\"Введите число, разделитель точка. Пример: 2.5\" type=\"text\" id=\"ul2\" size=\"6\" maxlength=\"7\" value=\"".htmlspecialchars($_GET['ul2'])."\">";
echo "</td>";

echo "<td valign=\"middle\" class=\"a\">Донор:</td>";

echo "<td ".($_GET['do']?$highlight:"").">";

echo "<select name=\"do\">";

$options = array("не имеет значения","Да","Нет");
for ($i = 0; $i < count($options); $i++){
echo "<option value=$i ".(((int)$_GET['do']=="$i")?"selected":"").">".$options[$i]."</option>\n";
}

echo "</select>";
echo "</td>";
echo "</tr>";

echo "<tr>";

echo "<td valign=\"middle\" class=\"a\">Когда был:</td>";

echo "<td ".($_GET['ls']?$highlight:"").">";
echo "<select name=\"lst\">";

$options = array("в","раньше","после","между");
for ($i = 0; $i < count($options); $i++){
echo "<option value=".$i." ".($_GET['lst']==$i ? "selected":"").">".$options[$i]."</option>\n";
}

echo "</select>";

echo " <input name=\"ls\" title=\"Начало даты, ".$timenow."\"  type=\"text\" value=\"".htmlspecialchars($_GET['ls'])."\" size=\"12\" maxlength=\"10\">";
echo " <input name=\"ls2\" title=\"Конец даты, ".$timenow."\"  type=\"text\" value=\"".htmlspecialchars($_GET['ls2'])."\" size=\"12\" maxlength=\"10\">";

echo "</td>";

echo "<td valign=\"middle\" class=\"a\">Скачал:</td>";

echo "<td ".($_GET['dl']?$highlight:"").">";

echo "<select name=\"dlt\" id=\"dlt\">";

$options = array("ровно","больше","меньше","между числами:");
for ($i = 0; $i < count($options); $i++){
echo "<option value=".$i." ".($_GET['dlt']==$i ? "selected":"").">".$options[$i]."</option>\n";
}

echo "</select>";

echo " <input name=\"dl\" title=\"Введите число, разделитель точка. Пример: 2.5\" type=\"text\" id=\"dl\" size=\"6\" maxlength=\"7\" value=\"".htmlspecialchars($_GET['dl'])."\">";
echo " <input title=\"Введите число, разделитель точка. Пример: 2.5\" name=\"dl2\" type=\"text\" id=\"dl2\" size=\"6\" maxlength=\"7\" value=\"".htmlspecialchars($_GET['dl2'])."\">";

echo "</td>";

echo "<td valign=\"middle\" class=\"a\">Предупреждение:</td>";

echo "<td ".($_GET['w']?$highlight:"").">";

echo "<select name=\"w\">";
$options = array("не имеет значения","Да","Нет");
for ($i = 0; $i < count($options); $i++){
echo "<option value=".$i." ".($_GET['w']==$i ? "selected":"").">".$options[$i]."</option>\n";
}
echo "</select>";

echo "</td>";
echo "</tr>";
echo "<tr> ";
echo "<td valign=\"middle\" class=\"a\">Уровень<br/>предупреждения:</td>";

echo "<td ".($_GET['as1']?$highlight:"").">";
echo "<select name=\"as1\">";



$options = array("не имеет значения","1 звезда","2 звезды","3 звезды","4 звезды","5 звезд"); 

for ($i = 0; $i < count($options); $i++) {
echo "<option value=".$i." ".($_GET['as1']==$i ? "selected":"").">".$options[$i]."</option>\n"; 
}

echo "</select>";
echo "</td>";

echo "<td valign=\"middle\" class=\"a\">Активный:</td>";
echo "<td ".($_GET['ac'] ? $highlight:"")."><input name=\"ac\" type=\"checkbox\" value=\"1\" ".($_GET['ac']?"checked":"")."></td>";

echo "<td valign=\"middle\" class=\"a\">Забаненный: </td>";
echo "<td ".($_GET['dip'] ? $highlight:"")."><input name=\"dip\" type=\"checkbox\" value=\"1\" ".($_GET['dip']?"checked":"")."></td>";

echo "</tr>";

echo "<tr><td colspan=\"6\" align=center><input type=\"submit\" class=\"btn\" style=\"height: 25px; width:300px\" value=\"Искать пользователя (ей)\"/></td></tr>";

echo "</table>";
echo "<br /><br />";
echo "</form>";



if (count($_GET) > 0 && !$_GET['h']) {
	// name
//  $names = explode(' ',trim($_GET['n']));
   $names = explode(' ',trim(htmlspecialchars($_GET['n'])));  
  if ($names[0] !== "")  {
		foreach($names as $name){
	  	if (substr($name,0,1) == '~') {
      	if ($name == '~') continue;
   	    $names_exc[] = substr($name,1);
      }
	    else
	    	$names_inc[] = $name;
	  }
    if (is_array($names_inc)) {
	  	$where_is .= isset($where_is)?" AND (":"(";
	    foreach($names_inc as $name) {
      	if (!haswildcard($name))
	        $name_is .= (isset($name_is)?" OR ":"")."u.username = ".sqlesc($name);
	      else
	      {
	        $name = str_replace(array('?','*'), array('_','%'), $name);
	        $name_is .= (isset($name_is)?" OR ":"")."u.username LIKE ".sqlesc($name);
	      }
	    }
      $where_is .= $name_is.")";
      unset($name_is);
	  }
    if (is_array($names_exc)) {
	  	$where_is .= isset($where_is)?" AND NOT (":" NOT (";
	  	
	    foreach($names_exc as $name) {
	    	if (!haswildcard($name))
	      	$name_is .= (isset($name_is)?" OR ":"")."u.username = ".sqlesc($name);
	      else
	      {
	      	$name = str_replace(array('?','*'), array('_','%'), $name);
	        $name_is .= (isset($name_is)?" OR ":"")."u.username LIKE ".sqlesc($name);
	      }
	    }
      $where_is .= $name_is.")";
	  }

	  $q .= ($q ? "&amp;" : "") . "n=".urlencode(trim(htmlspecialchars($_GET['n'])));  
  }
  // email
  if (get_user_class() > UC_ADMINISTRATOR) {

   $emaila = explode(' ', trim(htmlspecialchars($_GET['em'])));  
  if ( $emaila[0] !== "") {
  	$where_is .= isset($where_is)?" AND (":"(";
    foreach($emaila as $email) {
	  	if (strpos($email,'*') === False && strpos($email,'?') === False
	    		&& strpos($email,'%') === False)
	    {
    	if (!validemail($email)) {
	        stdmsg($tracker_lang['error'], "Неправильный E-mail.");
	        stdfootchat();
	      	die();
	      }
	      $email_is .= (isset($email_is)?" OR ":"")."u.email =".sqlesc($email);
      }
      else
      {
	    	$sql_email = str_replace(array('?','*'), array('_','%'), $email);
	      $email_is .= (isset($email_is)?" OR ":"")."u.email LIKE ".sqlesc($sql_email);
	    }
    }
		$where_is .= $email_is.")";
    //$q .= ($q ? "&amp;" : "") . "em=".urlencode(trim($_GET['em']));
     $q .= ($q ? "&amp;" : "") . "em=".urlencode(trim(htmlspecialchars($_GET['em'])));  
  }
}
  //class
  // NB: the c parameter is passed as two units above the real one
  $class = (int) $_GET['c'] - 2;
	if (is_valid_id($class + 1)) {
  	$where_is .= (isset($where_is)?" AND ":"")."u.class=$class";
    $q .= ($q ? "&amp;" : "") . "c=".($class+2);
  }
  // IP
  //$ip = trim($_GET['ip']);
   $ip = trim(htmlspecialchars($_GET['ip']));  
  if ($ip) {
  	$regex = "/^(((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))(\.\b|$)){4}$/";
    if (!preg_match($regex, $ip)) {
    	stdmsg($tracker_lang['error'], "Неверный IP.");
    	stdfootchat();
    	die();
    }
    $mask = trim($_GET['ma']);
    if ($mask == "" || $mask == "255.255.255.255")
    	$where_is .= (isset($where_is)?" AND ":"")."u.ip = '$ip'";
    else
    {
    	if (substr($mask,0,1) == "/") {
      	$n = substr($mask, 1, strlen($mask) - 1);
        if (!is_numeric($n) or $n < 0 or $n > 32)
        {
        	stdmsg($tracker_lang['error'], "Неверная маска подсети.");
        	stdfootchat();
          die();
        }
        else
	      	$mask = long2ip(pow(2,32) - pow(2,32-$n));
      }
      elseif (!preg_match($regex, $mask)) {
				stdmsg($tracker_lang['error'], "Неверная маска подсети.");
				stdfootchat();
	      die();
      }
      $where_is .= (isset($where_is)?" AND ":"")."INET_ATON(u.ip) & INET_ATON('$mask') = INET_ATON('$ip') & INET_ATON('$mask')";
      $q .= ($q ? "&amp;" : "") . "ma=$mask";
    }
    $q .= ($q ? "&amp;" : "") . "ip=$ip";
  }
  
  // ratio
   $ratio = trim(htmlspecialchars($_GET['r'])); 
  if ($ratio) {
  	if ($ratio == '---') {
    	$ratio2 = "";
      $where_is .= isset($where_is)?" AND ":"";
      $where_is .= " u.uploaded = 0 and u.downloaded = 0";
    }
    elseif (strtolower(substr($ratio,0,3)) == 'inf') {
    	$ratio2 = "";
      $where_is .= isset($where_is)?" AND ":"";
      $where_is .= " u.uploaded > 0 and u.downloaded = 0";
    } else {
    	if (!is_numeric($ratio) || $ratio < 0) {
      	stdmsg($tracker_lang['error'], "Неверный рейтинг.");
      	stdfootchat();
        die();
      }
      $where_is .= isset($where_is)?" AND ":"";
      $where_is .= " (u.uploaded/u.downloaded)";
          $ratiotype = (int) $_GET['rt'];  

      $q .= ($q ? "&amp;" : "") . "rt=$ratiotype";
      if ($ratiotype == "3")  {

      	 $ratio2 = trim(htmlspecialchars($_GET['r2'])); 
        if(!$ratio2) {
        	stdmsg($tracker_lang['error'], "Нужны два рейтинга для этого типа поиска.");
        	stdfootchat();
          die();
        }

        if (!is_numeric($ratio2) or $ratio2 < $ratio) {
        	stdmsg($tracker_lang['error'], "Плохой второй рейтинг.");
        	stdfootchat();
        	die();
        }
        
        $where_is .= " BETWEEN $ratio and $ratio2";
        $q .= ($q ? "&amp;" : "") . "r2=$ratio2";
      }
      elseif ($ratiotype == "2")
      	$where_is .= " < $ratio";
      elseif ($ratiotype == "1")
      	$where_is .= " > $ratio";
      else
      	$where_is .= " BETWEEN ($ratio - 0.004) and ($ratio + 0.004)";
    }
    $q .= ($q ? "&amp;" : "") . "r=$ratio";
  }



  $comments = explode(' ',trim(htmlspecialchars($_GET['co']))); 
  if ($comments[0] !== "") {
		foreach($comments as $comment) {

	    if (substr($comment,0,1) == '~') {
      	if ($comment == '~') continue;
   	    $comments_exc[] = substr($comment,1);
      }
      else
	    	$comments_inc[] = $comment;
	  }
    if (is_array($comments_inc)) {
	  	$where_is .= isset($where_is)?" AND (":"(";
	  	
	    foreach($comments_inc as $comment) {
	    if (!haswildcard($comment))
		$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc("%".$comment."%");
        else
        {
	      	$comment = str_replace(array('?','*'), array('_','%'), $comment);
	        $comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc($comment);
        }
      }
      
      $where_is .= $comment_is.")";
      unset($comment_is);
    }
    if (is_array($comments_exc)) {
	  	$where_is .= isset($where_is)?" AND NOT (":" NOT (";
	    foreach($comments_exc as $comment) {
	    	if (!haswildcard($comment))
		    	$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc("%".$comment."%");
        else
        {
	      	$comment = str_replace(array('?','*'), array('_','%'), $comment);
	        $comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc($comment);
	      }
      }
      $where_is .= $comment_is.")";
	  }
    $q .= ($q ? "&amp;" : "") . "co=".htmlentities(trim($_GET['co']));
  }
  $unit = 1073741824;		// 1GB
  

    $ul = trim((int)$_GET['ul']);  
  if ($ul) {
  	if (!is_numeric($ul) || $ul < 0) {
    	stdmsg($tracker_lang['error'], "Неправильное количество залитой информации.");
    	stdfootchat();
      die();
    }
    $where_is .= isset($where_is)?" AND ":"";
    $where_is .= " u.uploaded ";
    $ultype = (int)$_GET['ult'];
    $q .= ($q ? "&amp;" : "") . "ult=$ultype";
    
    if ($ultype == "3") {
	     $ul2 = (int) $_GET['ul2'];
	     
    	if(!$ul2) {
      	stdmsg($tracker_lang['error'], "Нужны два количества залитой информации для этого типа поиска.");
      	stdfootchat();
        die();
      }
      
      if (!is_numeric($ul2) or $ul2 < $ul) {
      	stdmsg($tracker_lang['error'], "Неправильный второй параметр залитой информации.");
      	stdfootchat();
        die();
      }
      
      $where_is .= " BETWEEN ".$ul*$unit." and ".$ul2*$unit;
      $q .= ($q ? "&amp;" : "") . "ul2=$ul2";
    }
    elseif ($ultype == "2")
    	$where_is .= " < ".$ul*$unit;
    elseif ($ultype == "1")
    	$where_is .= " >". $ul*$unit;
    else
    	$where_is .= " BETWEEN ".($ul - 0.004)*$unit." and ".($ul + 0.004)*$unit;
    $q .= ($q ? "&amp;" : "") . "ul=$ul";
  }


  $dl = (int)$_GET['dl']; 

  if ($dl) {
  	if (!is_numeric($dl) || $dl < 0) {
    	stdmsg($tracker_lang['error'], "Неправильное количество скачанной информации.");
    	stdfootchat();
      die();
    }
    $where_is .= isset($where_is)?" AND ":"";
    $where_is .= " u.downloaded ";
    $dltype =  (int)($_GET['dlt']);
    $q .= ($q ? "&amp;" : "") . "dlt=$dltype";
    
    if ($dltype == "3") {

    $dl2 = (int)$_GET['dl2'];
    
      if(!$dl2) {
      	stdmsg($tracker_lang['error'], "Нужны два количества скачанной информации для этого типа поиска.");
      	stdfootchat();
        die();
      }
      
      if (!is_numeric($dl2) or $dl2 < $dl) {
      	stdmsg($tracker_lang['error'], "Неправильный второй параметр скачанной информации.");
      	stdfootchat();
        die();
      }
      $where_is .= " BETWEEN ".$dl*$unit." and ".$dl2*$unit;
      $q .= ($q ? "&amp;" : "") . "dl2=$dl2";
    }
    elseif ($dltype == "2")
    	$where_is .= " < ".$dl*$unit;
    elseif ($dltype == "1")
     	$where_is .= " > ".$dl*$unit;
    else
     	$where_is .= " BETWEEN ".($dl - 0.004)*$unit." and ".($dl + 0.004)*$unit;
    $q .= ($q ? "&amp;" : "") . "dl=$dl";
  }
  // date joined
  $date = trim($_GET['d']);
  if ($date) {
  	if (!$date = mkdate($date)) {
    	stdmsg($tracker_lang['error'], "Неправильная дата.");
    	stdfootchat();
      die();
    }


$q .= ($q ? "&amp;" : "") . "d=$date";
$datetype = (int)$_GET['dt'];
$q .= ($q ? "&amp;" : "") . "dt=$datetype";

if ($datetype == "0")
    
    // For mySQL 4.1.1 or above use instead
    // $where_is .= (isset($where_is)?" AND ":"")."DATE(added) = DATE('$date')";
    $where_is .= (isset($where_is)?" AND ":"")."(UNIX_TIMESTAMP(added) - UNIX_TIMESTAMP('$date')) BETWEEN 0 and 86400";
    else
    {
      $where_is .= (isset($where_is)?" AND ":"")."u.added ";
      if ($datetype == "3") {
        $date2 = mkdate(trim($_GET['d2']));
        if ($date2) {
          if (!$date = mkdate($date)) {
            stdmsg($tracker_lang['error'], "Неправильная дата.");
            stdfootchat();
            die();
          }
          $q .= ($q ? "&amp;" : "") . "d2=$date2";
          $where_is .= " BETWEEN '$date' and '$date2'";
        }
        else
        {
          stdmsg($tracker_lang['error'], "Нужны две даты для этого типа поиска.");
          stdfootchat();
          die();
        }
      }
      elseif ($datetype == "1")
        $where_is .= "< '$date'";
      elseif ($datetype == "2")
        $where_is .= "> '$date'";
    }
  }
	// date last seen
  $last = trim($_GET['ls']);
  if ($last) {
  	if (!$last = mkdate($last)) {
    	stdmsg($tracker_lang['error'], "Неправильная дата.");
    	stdfootchat();
      die();
    }
    
    $q .= ($q ? "&amp;" : "") . "ls=$last";
    $lasttype = (int)$_GET['lst'];
    $q .= ($q ? "&amp;" : "") . "lst=$lasttype";
    if ($lasttype == "0")
    // For mySQL 4.1.1 or above use instead
    // $where_is .= (isset($where_is)?" AND ":"")."DATE(added) = DATE('$date')";
    	$where_is .= (isset($where_is)?" AND ":"")."(UNIX_TIMESTAMP(last_access) - UNIX_TIMESTAMP('$last')) BETWEEN 0 and 86400";
    else
    {
    	$where_is .= (isset($where_is)?" AND ":"")."u.last_access ";
      if ($lasttype == "3") {
      	$last2 = mkdate(trim($_GET['ls2']));
        if ($last2) {
        	$where_is .= " BETWEEN '$last' and '$last2'";
	        $q .= ($q ? "&amp;" : "") . "ls2=$last2";
        }
        else
        {
        	stdmsg($tracker_lang['error'], "Вторая дата неверна.");
        	stdfootchat();
        	die();
        }
      }
      elseif ($lasttype == "1")
    		$where_is .= "< '$last'";
      elseif ($lasttype == "2")
      	$where_is .= "> '$last'";
    }
  }
  // status
  $status = (int)$_GET['st'];
  if ($status) {
  	$where_is .= ((isset($where_is))?" AND ":"");
    if ($status == "1")
    	$where_is .= "u.status = 'confirmed'";
    else
    	$where_is .= "u.status = 'pending'";
    $q .= ($q ? "&amp;" : "") . "st=$status";
  }
  // account status
  $accountstatus = (int)$_GET['as'];
  if ($accountstatus) {
  	$where_is .= (isset($where_is))?" AND ":"";
    if ($accountstatus == "1")
    	$where_is .= " u.enabled = 'yes'";
    else
    	$where_is .= " u.enabled = 'no'";
    $q .= ($q ? "&amp;" : "") . "as=$accountstatus";
  }
  //donor
	$donor = (int)$_GET['do'];
  if ($donor) {
		$where_is .= (isset($where_is))?" AND ":"";
    if ($donor == 1)
    	$where_is .= " u.donor = 'yes'";
    else
    	$where_is .= " u.donor = 'no'";
    $q .= ($q ? "&amp;" : "") . "do=$donor";
  }
  //warned
	$warned =  (int)$_GET['w'];
  if ($warned) {
		$where_is .= (isset($where_is))?" AND ":"";
    if ($warned == 1)
    	$where_is .= " u.warned = 'yes'";
    else
    	$where_is .= " u.warned = 'no'";
    $q .= ($q ? "&amp;" : "") . "w=$warned";
  }
  $num_warned = (int) $_GET['as1'] - 1; 
    if (is_valid_id($num_warned + 1)) {
      $where_is .= (isset($where_is)?" AND ":"")."u.num_warned=$num_warned"; 
    $q .= ($q ? "&amp;" : "") . "as1=".($num_warned+1); 
  }  
 
// disabled IP
  
$disabled = htmlspecialchars($_GET['dip']); 
if ($disabled) {
$distinct = "DISTINCT ";
$join_is .= " LEFT JOIN users AS u2 ON u.ip = u2.ip";
$where_is .= ((isset($where_is))?" AND ":"")."u2.enabled = 'no'";
$q .= ($q ? "&amp;" : "") . "dip=$disabled";
}

 // active

$active = (int)$_GET['ac'];  
if ($active == "1") {
$distinct = "DISTINCT ";
$join_is .= " LEFT JOIN peers AS p ON u.id = p.userid";
$q .= ($q ? "&amp;" : ""). "ac=$active";
}
 
$from_is = "users AS u".$join_is;
$distinct = isset($distinct)?$distinct:"";
$queryc = "SELECT COUNT(".$distinct."u.id) FROM ".$from_is.(($where_is == "")?"":" WHERE $where_is ");
$querypm = "FROM ".$from_is.(($where_is == "")?" ":" WHERE $where_is ");
$select_is = "u.id, u.username,u.class, u.email, u.status, u.added, u.last_access, u.ip, u.class, u.uploaded, u.downloaded, u.donor, u.modcomment, u.usercomment, u.enabled, u.warned, u.num_warned";
$query = "SELECT ".$distinct." ".$select_is." ".$querypm;


if ($DEBUG_MODE > 0) {
stdmsg("Запрос подсчета",$queryc);
echo "<BR><BR>";
stdmsg("Поисковый запрос",$query);
echo "<BR><BR>";
stdmsg("URL ",$q);
if ($DEBUG_MODE == 2)
die();
echo "<BR><BR>";
}



$res = sql_query($queryc) or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);
  
$count = $arr[0];
  
$q = isset($q)?($q."&amp;"):"";
$perpage = 30;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"]."?".$q);
$query.= $limit;

$res = sql_query($query) or sqlerr(__FILE__, __LINE__);

$num = 0;

if (mysql_num_rows($res) == 0)
stdmsg("Внимание","Пользователь не был найден.");
else {

if ($count > $perpage)
echo $pagertop;
  		
echo "<table width=\"100%\" cellspacing=0 cellpadding=5>\n";

echo "<tr>
<td class=colhead align=center>Логин</td>
<td class=colhead align=center>Рейтинг</td>
<td class=colhead align=center>IP</td>
<td class=colhead align=center>Почта</td>
<td class=colhead align=center>Регистрация</td>
<td class=colhead align=center>Последний доступ</td>
<td class=colhead align=center>Потвержден</td>
<td class=colhead align=center>Вкл/Выкл</td>
<td class=colhead>R</td>
<td class=colhead>Залито*</td>
<td class=colhead>Скачано*</td>
</tr>";

while ($user = mysql_fetch_array($res)) {

if ($num%2==0){
$cla1 = " class=\"b\" ";
$cla2 = " class=\"a\" ";
} else {
$cla2 = " class=\"b\" ";
$cla1 = " class=\"a\" ";
}


if ($user['added'] == '0000-00-00 00:00:00')    	
$user['added'] = '---';
if ($user['last_access'] == '0000-00-00 00:00:00')
$user['last_access'] = '---';
      	
if ($user['ip']) {
$nip = ip2long($user['ip']);
$auxres = sql_query("SELECT COUNT(*) FROM bans WHERE $nip >= first AND $nip <= last") or sqlerr(__FILE__, __LINE__);
$array = mysql_fetch_row($auxres);

if ($array[0] == 0)
$ipstr = "<a title=\"Искать этот ip в таблице (откроется в новом окне)\" target='_blank' href=\"$DEFAULTBASEURL/usersearch.php?n=&rt=0&r=&r2=&st=0&em=&ip=".$user['ip']."\">".$user['ip']."</a>";
}
else
$ipstr = "---";
      	
$auxres = sql_query("SELECT SUM(uploaded) AS pul, SUM(downloaded) AS pdl FROM peers WHERE userid = " . $user['id']) or sqlerr(__FILE__, __LINE__);
$array = mysql_fetch_array($auxres);
$pul = $array['pul'];
$pdl = $array['pdl'];
$n_posts = $n[0];
      
     
//  $auxres = sql_query("SELECT COUNT(id) FROM comments WHERE user = ".$user['id']) or sqlerr(__FILE__, __LINE__);
// Use LEFT JOIN to exclude orphan comments
// $auxres = sql_query("SELECT COUNT(c.id) FROM comments AS c LEFT JOIN torrents as t ON c.torrent = t.id WHERE c.user = '".$user['id']."'") or sqlerr(__FILE__, __LINE__);
// $n = mysql_fetch_row($auxres);
//  $n_comments = $n[0];
    
if ((get_user_class() <= UC_ADMINISTRATOR) && $user['class'] > UC_ADMINISTRATOR)(
$user['email'] = '<i>Почта скрыта</i>');
else {
$user['email'] = "<b>".$user['email']."</b>";
}

if ($user['enabled']=="yes"){
$user['enabled']="включен"; 
} else {
$user['enabled']="<b>выключен</b>"; 
}

if ($user['status']=="confirmed"){
$user['status']="подтвержден"; 
} else {
$user['status']="<b>не активирован</b>";
}
	
echo "<tr>
<td ".$cla1."><b><a href=\"userdetails.php?id=".$user['id']."\">".get_user_class_color($user['class'], $user['username'])."</td>
<td ".$cla2." align=\"center\">".ratios($user['uploaded'], $user['downloaded'])."</td>
<td ".$cla1." align=\"center\">".$ipstr."</td>
<td ".$cla2." align=\"center\">".$user['email']."</td>
<td ".$cla1.">".$user['added']."</td>
<td ".$cla2.">".$user['last_access']."</td>
<td ".$cla1.">".$user['status']."</td>
<td ".$cla2.">".$user['enabled']."</td>
<td ".$cla1.">".ratios($pul,$pdl)."</td>
<td ".$cla2.">".mksize($pul)."</td>
<td ".$cla1.">".mksize($pdl)."</td>
</tr>\n";

$modcomment = htmlspecialchars($user["modcomment"]);
$usercomment = htmlspecialchars($user["usercomment"]);

echo "<tr><td style=\"padding:1px;\" colspan=\"11\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"10\">";

echo "<td style=\"padding:1px;\"  class=\"b\" width=\"50%\" align=\"center\">
<div class=\"spoiler-wrap\" id=\"ad_".$user['id']."\"><div class=\"spoiler-head folded clickable\">Админская история событий: ".$user['username']."</div><div class=\"spoiler-body\" style=\"display: none;\"><textarea cols=90% rows=20 readonly>".$modcomment."</textarea></div></div></td>";

if (get_user_class() > UC_MODERATOR)
echo "<td  style=\"padding:1px;\" class=\"b\" width=\"50%\" align=\"center\"><div class=\"spoiler-wrap\" id=\"us_".$user['id']."\"><div class=\"spoiler-head folded clickable\">Пользовательская история событий: ".$user['username']."</div><div class=\"spoiler-body\" style=\"display: none;\"><textarea cols=90% rows=20 readonly>".$usercomment."</textarea></div></div></td>";

echo "</table></td></tr>\n";

++$num;
}

echo "</table>";

if ($count > $perpage)
echo $pagerbottom;


	
if (get_user_class() >= UC_ADMINISTRATOR){
echo "<br /><br />
<form method=\"post\" action=\"message.php\">
<table border=\"1\" width=\"100%\" cellpadding=\"5\" cellspacing=\"0\">
<tr>
<td class=\"b\">Рассылка сообщений найденным пользователям</td>
<td class=\"b\"><input name=\"pmees\" type=\"hidden\" value=\"".$querypm."\" size=\"10\">
<input name=\"PM\" type=\"submit\" value=\"Отправить массовое сообщение\" class=\"btn\">
<input name=\"n_pms\" type=\"hidden\" value=\"".$count."\" size=\"10\">
<input name=\"action\" type=\"hidden\" value=\"mass_pm\" size=\"10\">
</td>
</tr>
</table>
</form>";
}


}

}

echo $pagemenu."<br />".$browsemenu;

stdfootchat();
die;
?>