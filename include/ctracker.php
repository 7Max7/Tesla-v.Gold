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


# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');


$cracktrack = urldecode($_SERVER['QUERY_STRING']);
$wormprotector = array('chr(', 'chr=', 'chr%20', '%20chr', 'wget%20', '%20wget', 'wget(','cmd=', '%20cmd', 'cmd%20', 'rush=', '%20rush', 'rush%20','union%20', '%20union', 'union(', 'union=', 'echr(', '%20echr', 'echr%20', 'echr=','esystem(', 'esystem%20', 'cp%20', '%20cp', 'cp(', 'mdir%20', '%20mdir', 'mdir(','mcd%20', 'mrd%20', 'rm%20', '%20mcd', '%20mrd', '%20rm','mcd(', 'mrd(', 'rm(', 'mcd=', 'mrd=', 'mv%20', 'rmdir%20', 'mv(', 'rmdir(','chmod(', 'chmod%20', '%20chmod', 'chmod(', 'chmod=', 'chown%20', 'chgrp%20', 'chown(', 'chgrp(','locate%20', 'grep%20', 'locate(', 'grep(', 'diff%20', 'kill%20', 'kill(', 'killall','passwd%20', '%20passwd', 'passwd(', 'telnet%20', 'vi(', 'vi%20','insert%20into', 'select%20', 'nigga(', '%20nigga', 'nigga%20', 'fopen', 'fwrite', 'like%20','$_request', '$_get', '$request', '$get', '.system', 'HTTP_PHP', '&aim', '%20getenv', 'getenv%20','new_password', '&icq','/etc/password','/etc/shadow', '/etc/groups', '/etc/gshadow','HTTP_USER_AGENT', 'HTTP_HOST', '/bin/ps', 'wget%20', 'uname\x20-a', '/usr/bin/id','/bin/echo', '/bin/kill', '/bin/', '/chgrp', '/chown', '/usr/bin', 'g\+\+', 'bin/python','bin/tclsh', 'bin/nasm', 'perl%20', 'traceroute%20', 'ping%20','/usr/X11R6/bin/xterm', 'lsof%20','/bin/mail', '.conf', 'motd%20', 'HTTP/1.', '.inc.php', 'config.php','passwords.php', 'cgi-', '.eml','file\://', 'window.open', '%3C%73%63%72%69%70%74','<script','src=', 'javascript\://','img src', 'img%20src','.jsp','ftp.exe', 'xp_enumdsn', 'xp_availablemedia', 'xp_filelist', 'xp_cmdshell', 'nc.exe', '.htpasswd','servlet', '/etc/passwd', 'wwwacl', '~root', '~ftp', '.js', '.jsp', 'admin_', '.history','bash_history', '.bash_history', '~nobody', 'server-info', 'server-status', 'reboot%20', 'halt%20','powerdown%20', '/home/ftp', '/home/www', 'secure_site, ok', 'chunked', 'org.apache', '/servlet/con','<script', '/robot.txt' ,'/perl' ,'mod_gzip_status', 'db_mysql.inc', '.inc', 'select%20from','select from', '.system', 'drop%20', 'users+where', 'union+select', 'substring((', 'ASCII(', '(select+','getenv', 'http_', '_php', 'php_');

$checkworm = str_replace($wormprotector, '*', $cracktrack);
$checkworm = str_replace($wormprotector, '*', strtolower($cracktrack));

$cracktrack = strtolower($cracktrack);


if ($cracktrack <> $checkworm) {
//die($CURUSER['monitoring']);
//sql_query("UPDATE users SET monitoring = 'yes' WHERE id=" . $CURUSER["id"]);
    /*if ($CURUSER['monitoring']=='yes')
    {
$userid2=$CURUSER["id"];
$sf2 =ROOT_PATH."cache/monitoring_$userid2.txt"; 
$fpsf2=fopen($sf2,"a+"); 
$ip2=getenv("REMOTE_ADDR"); 
$ag2=getenv("HTTP_USER_AGENT"); 
$from2=getenv("HTTP_REFERER"); 
$host2=getenv("REQUEST_URI"); 
$date2 = date("d.m.y"); 
$time2= date("H:i:s"); 
fputs($fpsf2,"Попытка взлома $host2 : $date2:$time2\n"); 
fclose($fpsf2);

    //	die("тут временное правило");
    }*/
        	
$cremotead = $_SERVER['REMOTE_ADDR'];
$cuseragent = $_SERVER['HTTP_USER_AGENT'];

$sf =ROOT_PATH."cache/hacklog.txt";
$fpsf=fopen($sf,"a+"); 
$ip=getip(); 
$ag=getenv("HTTP_USER_AGENT"); 
$from=getenv("HTTP_REFERER"); 
$host=getenv("REQUEST_URI"); 
$date = date("d.m.y"); 
$time= date("H:i:s"); 
$getiing = serialize($_GET?$_GET:"")."||".serialize($_POST?$_POST:"");
fputs($fpsf,"$date#$time#$ip#$ag#$from#$host#$getiing\n"); 

$host=getenv("REQUEST_URI"); 
fclose($fpsf); 

$all="$date#$time#$ip#$ag#$from#$host#$getiing";


if (!empty($war_email))
@sent_mail($war_email,$SITENAME,$SITEEMAIL,$subj,$all,false);	


die("[1045] dbconn: mysql_connect: Access denied (using password: YES)");
}

if ($cracktrack==0)
unset($cracktrack);
if ($checkworm==0)
unset($checkworm);

//if ($checkworm==0 && $cracktrack==0)
//unset($wormprotector); //[85]: wormprotector => 2830

?>