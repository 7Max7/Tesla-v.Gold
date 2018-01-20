<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

if (get_user_class() < UC_ADMINISTRATOR)
stderr($tracker_lang['error'], $tracker_lang['access_denied']);


if($_POST){
extract($_POST);
//stdhead("Проверка и синхронизация - Запрос в базу Автообновление");

sql_query("UPDATE users SET downloaded = ".sqlesc($gigs)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__,__LINE__); 


if (!$s){
   @header("Location: $DEFAULTBASEURL/downcheck.php?page=$page&u=$u&doid=$userid&do=yes#$userid");
   } else {
   @header("Location: $DEFAULTBASEURL/downcheck.php?search=$userid&doid=$userid&do=yes");
   }
   die;
}


stdhead("Проверка и синхронизация закачек пользователей");


$allclass = $_GET["u"];
if (!$allclass){
$where = " WHERE class < ".UC_VIP;
$all = "0";
} else {
$where = "";
$all = "1";
}

$search_id = (int) $_GET["search_id"];
$search_name = htmlspecialchars($_GET["search_name"]);

$ss = 0;

if ($search_id) {
$where = " WHERE id = ".$search_id;
$ss = 1;
}

if ($search_name) {
$where = " WHERE username LIKE '".sqlwildcardesc($search_name)."%'";
$ss = 1;
}

$count = get_row_count("users", "".$where."");

if (!$count){ 
print("<div class='error'><b>Нет пользователей с таким номером или именем</b>!<br><br>
       <a href='downcheck.php'>Вернуться на страницу проверки</a></div>"); 
stdfoot(); 
die; 
} 

$perpage = 50; // На  страницу 
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?u=".$all."&amp;" ); 

// получаем данные из базы пользователей 
$res = sql_query("SELECT id, username, class, downloaded, uploaded, modcomment FROM users ".$where." ORDER BY id ASC $limit") or sqlerr(__FILE__, __LINE__); 

print('<div align="center"><table border="0" cellspacing="0" width="100%" cellpadding="4">'); 
print ("<tr><td colspan='8' class='colhead'>Проверка и синхронизация закачек пользователей</td></tr>");

// Поиск пользователя
print ('<tr><td colspan="8" align="center">
        <form method="GET" action="'.$PHP_SELF.'">
        <b>Поиск по номеру пользователя</b>:&nbsp;<input type="text" name="search_id" size="10" value="'.$search_id.'">&nbsp;<br />
        <b>Поиск по имени пользователя</b>:&nbsp;<input type="text" name="search_name" size="10" maxlength="40" value="'.$search_name.'">&nbsp;<br /><input type="submit" value="Найти">
        </form></td></tr>');

print ("<tr><td colspan='8'>"); 

print $pagertop; 

if (!$search){
   print ('<div style="float:right"> 
           <form method="GET" action="'.$PHP_SELF.'"> 
           <b>Все классы</b>: <input type="checkbox" name="u" value="yes" '.(($all) ? "checked" : "").'><input type="submit" value="ОК"> 
           </form> 
           </div>'); 
} else {
   print ('<div style="float:right"> 
           <a href="downcheck.php">На страницу проверки</a> 
           </div>');
}

print ("</td></tr>"); 

print('<tr> 
       <td align="center" class="colhead">Пользователь</td> 

       <td align="center" class="colhead"><img height="13" src="pic/ratioall.gif" width="13" border="0" alt="Рейтинг"></td> 
       <td align="center" class="colhead"><img border="0" src="pic/megs.gif" width="13" height="12" alt="Скачал количество" hspace="4">Шт</td> 
       <td align="center" class="colhead"><img border="0" src="pic/arrowdown.gif" width="14" height="13" alt="Скачал по профилю" hspace="4">Гб</td> 
       <td align="center" class="colhead"><img border="0" src="pic/downtorr.gif" width="13" height="13" alt="Скачал по торрентам" hspace="4">Гб</td> 
       <td align="center" class="colhead"><img border="0" src="pic/warned13.gif" width="13" height="13" alt="Разница" hspace="4">Гб</td> 
       <td align="center" class="colhead"><img border="0" src="pic/blocked13.gif" width="13" height="13" alt="Синхронизировать" hspace="4"></td> 
       </tr>'); 

$ccolor = 0; // начальное значение счетчика фона 

while ($arr = mysql_fetch_array($res)) {

if (!$arr){ 
print("<tr><td colspan='8'><div class='error'><b>Нет пользователей</b>!</div></td></tr></table>"); 
stdfoot(); 
die; 
} 
   // меняем цвета фона 
   if (!$ccolor){ 
   $color = "#FFFFFF'"; 
   $ccolor = 1; 
   } else { 
   $color = "#F3F3F3"; 
   $ccolor = 0; 
   }

$uid = $arr['id']; // айди пользователя 
$uhistory = $arr['modcomment']; // история пользователя 
$summ = 0; 

//******************************************Вариант Softovic'a:***********************************************//
// выбираем скачанные торренты 
/*$r = mysql_query("SELECT torrent AS tid FROM snatched WHERE finished='yes' AND userid = $uid") or sqlerr(__FILE__,__LINE__); 

// если скачивалось 
if (mysql_num_rows($r) > 0) { 
$user_completed = mysql_num_rows($r); // кличество скачанных 
      
     // считаем сумму по всем скачанным торрентам 
     while ($size = mysql_fetch_array($r)) {            
     $restorr = mysql_query("SELECT size FROM torrents WHERE id = ".$size["tid"]) or sqlerr(__FILE__,__LINE__); 
     $storr = mysql_fetch_array($restorr); 
     $summ = $summ + $storr["size"]; // итоговая сумма в байтах 
     }
*/
//******************************************Вариант Softovic'a:***********************************************//

//******************************************Переделка Юны:***********************************************//
// выбираем скачанные торренты 
$r = sql_query("SELECT s.torrent AS tid, t.size FROM snatched AS s INNER JOIN torrents AS t ON t.id = s.torrent WHERE s.finished='yes' AND s.userid = $uid AND t.free = 'no'") or sqlerr(__FILE__,__LINE__); 

// если скачивалось
if (mysql_num_rows($r) > 0) {
    $user_completed = mysql_num_rows($r); // кличество скачанных 
    // считаем сумму по всем скачанным торрентам
    while ($size = mysql_fetch_array($r)) {
        $summ = $summ + $size["size"]; // итоговая сумма в байтах 
    }
//******************************************Переделка Юны:***********************************************//

$allsumm = number_format($summ /1024/1024/1024, 2); // окончательная сумма в гигабайтах 
} else {
$user_completed = "--"; 
$allsumm = "--"; 
}

// формируем ссылку на профайл юзера 
$user = "<a target='_blank' href=userdetails.php?id=".$arr['id'].">".get_user_class_color($arr["class"], $arr["username"])."</a>";

$downloaded = $arr['downloaded']; // скачано 
$uploaded = $arr['uploaded']; // загружено 
$downloadedgb = number_format($downloaded /1024/1024/1024, 2); // скачано в Гб 

// формируем рейтинг 
if ($downloaded){
$ratio = $uploaded / $downloaded; 
$uratio = number_format($ratio, 2); 
$uratio = "<font color=\"".get_ratio_color($uratio)."\">".$uratio."</font>"; 
} else {
$uratio = "inf"; 
}

//Рассчитываем разницу в килобайтах, переводим в Гб и округляем
$what =  number_format(($summ - $downloaded)/1024/1024/1024, 2); // Разница = значению всех размеров скачанных торрентов минус значение скачанного в профиле  

// Считаем разницу: 
//$what =  $allsumm - $downloadedgb; // Разница = значению всех размеров скачанных торрентов минус значение скачанного в профиле 

// задаем цвет разницы 
if ($what < 1){ 
$wcolor = ""; 
} elseif ($what >= 1 and $what < 3){ 
$wcolor = 'style="background-color: #FF9933; color:white;"'; 
} elseif ($what >= 3 and $what < 5){ 
$wcolor = 'style="background-color: red; color:white;"'; 
} else { 
$wcolor = 'style="background-color: #CC3300; color:white;"'; 
} 

//$userhistory = "<textarea cols=70 rows=8 readonly>".htmlspecialchars($uhistory)."</textarea>"; // Поле истории 

$page = (int)$_GET["page"]; // получаем номер страницы 
$do = $_GET["do"]; // выполнено? 
$doid = (int)$_GET["doid"]; // получаем айди юзера 

// Подсвечиваем строку юзера, которого синхронизировали 
if ($do == "yes" and $doid == $uid){ 
$color = "#FFFFB3"; 
} 

// выводим таблицу пользователя 
print('<tr style="background-color: '.$color.'"> 
       <td align="center"><a name="'.$uid.'">'.$user.'</a></td> 
       <td align="center"><b>'.$uratio.'</b></td> 
       <td align="center"><b>'.$user_completed.'</b></td> 
       <td align="center"><b>'.$downloadedgb.'</b></td> 
       <td align="center"><b>'.$allsumm.'</b></td> 
       <td align="center" '.$wcolor.'><b>'.$what.'</b></td>'); 
 if ($summ and $downloadedgb !== $allsumm){ 
   print('<td align="center"> 
          <form method="POST" action="downcheck.php"> 
          <input border="0" src="pic/next.png" title="Синхронизировать" name="go" width="23" height="17" type="image" onClick="return confirm(\'Вы уверены?\')"> 
          <input type="hidden" name="page" value="'.$page.'"> 
          <input type="hidden" name="u" value="'.$allclass.'"> 
          <input type="hidden" name="userid" value="'.$uid.'"> 
          <input type="hidden" name="gigs" value="'.$summ.'"> 
          <input type="hidden" name="s" value="'.$ss.'">
          </form></td>'); 
 } else { 
   print('<td align="center"> 
          <img border="0" src="pic/delete.gif" title="Недоступно" width="16" height="16"> 
          </td>');  
 }          
print('</tr>'); 

}

print ("<tr><td colspan='8'>".$pagerbottom."</td></tr>"); 
print ('</table></div>'); 

stdfoot(); 
?> 