<?

/**  Внимание, самая популярная сборка движка теперь доступна в бесплатном скачивании (версия gold за 2010 год)!!!!
* Это старая версия проекта Tesla - торрент сайта. Демо новой версии можно посмотреть по ссылке demo.muz-tracker.net
* Если желаете использовать движок, то оставьте пожалуйста копирайта на своих местах, иначе стабильной работы не гарантируется.
* Если желаете поискать и установить новые моды, советуем воспользоваться форумами: bit-tracker.info, bit-torrent.kiev.ua.
* Если желаете поблагодарить владельца 7Max7 за эту сборку, киньте пару монеток на R190680055855 или Z187703352284
* Если желаете обновить Gold версию до Platinum - придется заплатить около 70$ (с последующими бесплатными обновлениями)
* 
* Данная копия движка взята с сайта demo muz-tracker net! ))
* Спасибо за внимание к движку Tesla.
**/


if(!defined('IN_TRACKER') && !defined('IN_ANNOUNCE')){
$ip=getenv("REMOTE_ADDR"); 
$f = @fopen('./cache/passwords.txt', 'a+');
@fwrite($f, $ip."\n");
@fclose($f);
die("В доступе отказано.");
}

$mysql_host_fix_by_imperator ="localhost"; // подключение к

$mysql_db_fix_by_imperator = "tesla"; // имя таблицы в базе

$mysql_user_fix_by_imperator = "root"; // логин к базе
$mysql_pass_fix_by_imperator = "rooting"; // пароль к базе






$mysql_charset_fix_by_imperator = "cp1251"; // желательно не менять.


/*
//// эти данные для админки запишите в passwords.php взято с creating_adminpas.php
$useraccess_fix_by_imperator = "test"; //имя
$passaccess_fix_by_imperator = "9d11724d851dca44f880d239ca2b97fc"; //пароль в md5 дважды
//// эти данные для админки запишите в passwords.php взято с creating_adminpas.php
*/

//// эти данные для админки запишите в passwords.php
$useraccess_fix_by_imperator = "1"; //имя
$passaccess_fix_by_imperator = "15ab8357abeb6eacf1b591a1b5b1aedd"; //пароль в md5 дважды
//// эти данные для админки запишите в passwords.php

/*
$SITEEMAIL=$accountname="";  
$accountpassword=""; 
$smtp_host=""; 
$smtp_port="2525";
*/

?>