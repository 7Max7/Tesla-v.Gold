<?php 
if (!defined('BLOCK_FILE')) { 
 Header("Location: ../index.php"); 
 exit; 
} 


$cacheStatFile = "cache/block-users_today.txt"; 
$expire = 60*60; // 60 minutes 
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{ 



//visited today 

$res = mysql_query("SELECT id, username, class FROM users WHERE last_login<>'0000-00-00 00:00:00' and UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(last_access) < UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(" .date("Ymd000000"). ") ORDER BY last_access") or sqlerr(__FILE__, __LINE__); 

 
while ($arr = mysql_fetch_assoc($res))  

{
    if ($todayactive) 
        $todayactive .= ", ";  

    if ($CURUSER) {  
        $todayactive .= "<a href=userdetails.php?id=" . $arr["id"] . ">".get_user_class_color($arr["class"], $arr["username"])."</a></a>";  
    } else {  
        $todayactive .= "<a href=userdetails.php?id=" . $arr["id"] . ">".get_user_class_color($arr["class"], $arr["username"])."</a></a>";  
    }

    $usersactivetoday++;  
} 


if ($usersactivetoday>30){
$content .="
<center> 
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" width=\"100%\"><tr><td class=\"embedded\">

<span style=\"cursor: pointer;\" onclick=\"javascript: show_hide('s_1689')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pics_1689\" title=\"Показать\"></span>&nbsp;"
."<span style=\"cursor: pointer;\" onclick=\"javascript: show_hide('s_1689')\">Нажми [$usersactivetoday] Сюда</span>\n"	
."<span id=\"ss_1689\" style=\"display: none;\">$todayactive</span>";

	$content .= "<hr></td></tr></table></center>\n";
}
else
{
$content .="<center> 
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" width=\"100%\"><tr><td class=\"embedded\">
Сегодня: $usersactivetoday <hr>
$todayactive <hr></td></tr></table></center>\n";
}






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