<?php   
if (!defined('BLOCK_FILE')) {   
 Header("Location: ../index.php");   
 exit;   
}   
global $tracker_lang, $CURUSER, $pic_base_url;
$blocktitle = "Последние комментарии";   

if (get_user_class() < UC_MODERATOR) 
$cacheStatFile = "cache/block-lastcomment_user.txt"; 
else
$cacheStatFile = "cache/block-lastcomment_admin.txt"; 
$expire = 2*60; // 2 minutes 
if (file_exists($cacheStatFile) && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{ 

//Параметры для гостей и юзеров (высота блока и скорость прокрутки) 

$x=200;//высота 
$amount=1;//скорость 

$content .= "<center><MARQUEE behavior= \"scroll\" direction= \"up\" width=\"132\"  height=\"".$x."\" scrollamount= \"".$amount."\" scrolldelay= \"90\" onmouseover= \"this.stop()\"  onmouseout='this.start()'>";  

$res = sql_query("SELECT comments.user, comments.id AS cid, comments.torrent, comments.text, users.username, users.id,users.gender, users.class FROM comments LEFT JOIN users ON users.id = comments.user ORDER BY comments.added DESC LIMIT 13") or sqlerr(__FILE__,__LINE__);

while ($row = mysql_fetch_array($res)) {
	
if($row["gender"] == '2') {
$g = "а"; }
else {
$g = ""; }

	
$content .= "<center><div style='padding:0px;'>";//Начало.
$content .= "".($CURUSER?"<a href=details.php?id=$row[torrent]>":"")."".format_comment($row["text"])."".($CURUSER?"</a>":"")."<br />";
$content .= "Добавил$g: ".($CURUSER?"
<a href=userdetails.php?id=$row[id]>":"")."".get_user_class_color($row["class"], $row["username"])."".($CURUSER?"</a>":"")."<br>".($CURUSER?"
<a href=comment.php?action=quote&cid=$row[cid]>
<img src=".$pic_base_url."minipost.gif border=\"0\" alt=\"Цитировать\" title=\"Цитировать\"></a>":"")."".($CURUSER["id"]==$row["user"] || get_user_class()>=UC_MODERATOR?"<a href=comment.php?action=edit&cid=$row[cid]><img src=".$pic_base_url."pen.gif border=\"0\" alt=".$tracker_lang["edit"]." title=".$tracker_lang["edit"]."></a>
<a href=comment.php?action=delete&cid=$row[cid]><img src=".$pic_base_url."warned2.gif border=\"0\" alt=".$tracker_lang["delete"]." title=".$tracker_lang["delete"]."></a>":"")."";
$content .= "</div></center><hr>";//Конец.
}
$content .= "</marquee></center>"; 


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