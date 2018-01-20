<?
if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}
$blocktitle = "Всего лишь заголовки. Информация для новичка (нового пользователя).";


$cacheStatFile = "cache/block-info.txt"; 
$expire = 60*60; // 1 час
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{

global $CURUSER, $SITENAME,$maxusers,$use_email_act;

$registered = (get_row_count("users"));

$torrents = number_format(get_row_count("torrents"));	
$torrents_free = number_format(get_row_count("torrents","WHERE free='yes'"));	
$multitracker = number_format(get_row_count("torrents","WHERE multitracker='yes'"));	

$tags = number_format(get_row_count("tags"));	
$category = number_format(get_row_count("categories"));
$file = number_format(get_row_count("files"));
$humor = number_format(get_row_count("humor"));

$free_slot=$maxusers-$registered;
$registered=number_format($registered);


$content.= "<font face=\"Georgia\" style=\"font-size: 15px;\">
<b>На данный момент у нас</b>: <br>
<b>~</b> около <b>$registered</b> зарегистрированных пользователей;<br>
<b>~</b> около <b>$torrents</b> залитых файлов (из них которые - <b>$torrents_free</b> золотые, <u>не фиксируются системой</u>, и <b>$multitracker</b> мультитрекерные - скорость скачивания ограничивается <u>лишь вашей пропускной способностью канала</u>);<br>
<b>~</b> около <b>$tags</b> меток (тегов) для лучшего и быстрого поиска файлов в системе;<br>
<b>~</b> около <b>$category</b> разделов под категорий файлов;<br>
<b>~</b> около <b>$file</b> файлов в <b>$torrents</b> торрентах;<br>
<b>~</b> около <b>$humor</b> <a href=\"humorall.php\">анекдотов</a> для быстрого просмотра;<br>
<b>~</b> около <b>$free_slot</b> свободных мест для новых аккаунтов;<br>
<center>
<br>Не удивили цифры, см ниже блок <b>Статистика</b>. Все выше сказанное исключительно для посетителей сайта, также и вы можете стать им. Форма регистрации доступна <a href=\"signup.php\">здесь</a> (регистрация абсолютно бесплатна".($use_email_act=="0" ? ", ко всему же <b>без подверждения на почту</b>":"").").<br>
С уважением, Система сайта <b>$SITENAME</b>.</center>
</font>";


    $fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
 
 if (get_user_class() >= UC_SYSOP)
{
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
}


?>
