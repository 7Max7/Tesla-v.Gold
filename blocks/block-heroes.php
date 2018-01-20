<?php
if (!defined('BLOCK_FILE')) {
Header("Location: ../index.php");
exit;
}


$maxnumber = 9; // число max 9
$first = 1; // по умолчанию
$random = mt_rand($first, $maxnumber);



$content .= "<table width=\"100%\" class=\"main\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";

if ($random==1){

$cacheStatFile = "cache/block-heroes_$random.txt"; 
$expire = 60*60; // 60 минут на кеш, после обновление 
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
	
$content .= "
<td class=b colspan=3 align=\"center\">—пасибо</td></tr>";
$thx = sql_query("SELECT *, (SELECT COUNT(*) FROM thanks WHERE users.id = thanks.touid) AS num_thanks FROM users WHERE enabled='yes' ORDER BY num_thanks DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$num = 0;
while ($thx2 = mysql_fetch_array($thx)) {
	
	if ($num%2==0) {	$class="class=\"a\""; $class2="class=\"b\""; } else {
	$class="class=\"b\""; 	$class2="class=\"a\"";
	}
	
    ++$num;
    $id = $thx2["id"];
    $thx3 = $thx2["num_thanks"];
    $content .= "<tr>
	<td $class >$num.</td>
	<td $class align=\"left\"><a href=\"userdetails.php?id=$id\">".get_user_class_color($thx2["class"], $thx2["username"])."</a></td>
	<td $class align=\"center\">$thx3</td></tr>";
}

$fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
}// конец 1

if ($random==2){
$cacheStatFile = "cache/block-heroes_$random.txt"; 
$expire = 60*60; // 60 минут на кеш, после обновление 
if (file_exists($cacheStatFile) && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
$content .= "
<td class=b colspan=3 align=\"center\">Ѕонусы</td></tr>";
$bonus = sql_query("SELECT bonus, id, class, username FROM users WHERE bonus > 1 and enabled='yes' ORDER BY bonus DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$num = 0;
while ($bonus2 = mysql_fetch_array($bonus)) {
		if ($num%2==0) {	$class="class=\"a\""; $class2="class=\"b\""; } else {
	$class="class=\"b\""; 	$class2="class=\"a\"";
	}
	
    ++$num;
    $id = $bonus2["id"];
    $bonus3 = $bonus2["bonus"];
    $content .= "<tr>
	<td $class>$num.</td>
	<td $class align=\"left\"><a href=\"userdetails.php?id=$id\">".get_user_class_color($bonus2["class"], $bonus2["username"])."</a></td>
	<td $class >$bonus3</td></tr>";
}
$fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
}/// конец 2

if ($random==3){
$cacheStatFile = "cache/block-heroes_$random.txt"; 
$expire = 60*60; // 60 минут на кеш, после обновление 
if (file_exists($cacheStatFile) && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
$content .= "
<td class=b colspan=3 align=\"center\">–еспекты</td></tr>";
$sym = sql_query("SELECT * FROM users WHERE enabled='yes' ORDER BY simpaty DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$num = 0;
while ($sym2 = mysql_fetch_array($sym)) {

if ($num%2==0) {	$class="class=\"a\""; $class2="class=\"b\""; } else {
$class="class=\"b\""; 	$class2="class=\"a\"";
}
	
    ++$num;
    $id = $sym2["id"];
    $sym3 = $sym2["simpaty"];
    $content .= "<tr>
	<td $class>$num.</td>
	<td $class align=\"left\"><a href=\"userdetails.php?id=$id\">".get_user_class_color($sym2["class"], $sym2["username"])."</a></td>
	<td $class >$sym3</td></tr>";
}$fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
}/// конец 3


//$content .="</table></td></tr></table>";

if ($random==4){
$cacheStatFile = "cache/block-heroes_$random.txt"; 
$expire = 60*60; // 60 минут на кеш, после обновление 
if (file_exists($cacheStatFile) && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
$content .= "
<td class=b colspan=3 align=\"center\">—казал —пасибо</td></tr>";
$thx3 = sql_query("SELECT id,username,class, (SELECT COUNT(*) FROM thanks WHERE users.id = thanks.userid) AS num_thanks2 FROM users WHERE enabled='yes' ORDER BY num_thanks2 DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$num = 0;
while ($thx33 = mysql_fetch_array($thx3)) {
if ($num%2==0) {	$class="class=\"a\""; $class2="class=\"b\""; } else {
$class="class=\"b\""; 	$class2="class=\"a\"";
}
    ++$num;
    $id33 = $thx33["id"];
    $username33 = $thx33["username"];
    $class33 = $thx33["class"];
    $thx33 = $thx33["num_thanks2"];
 

    $content .= "<tr>
	<td $class>$num.</td>
	<td $class align=\"left\"><a href=\"userdetails.php?id=$id33\">".get_user_class_color($class33, $username33)."</a></td>
	<td $class>$thx33</td></tr>";
}$fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
}/// конец 4

if ($random==5){
$cacheStatFile = "cache/block-heroes_$random.txt"; 
$expire = 60*60; // 60 минут на кеш, после обновление 
if (file_exists($cacheStatFile) && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
$content .= "
<td class=b colspan=3 align=\"center\">ќдобрени€</td></tr>";
$bonus6 = sql_query("SELECT id, class, username,(SELECT COUNT(*) FROM torrents WHERE moderated='yes' and users.id = torrents.moderatedby) AS num_thanks5  FROM users ORDER BY num_thanks5 DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$num= 0;
while ($bonus5 = mysql_fetch_array($bonus6)) {

if ($num%2==0) {	$class="class=\"a\""; $class2="class=\"b\""; } else {
$class="class=\"b\""; 	$class2="class=\"a\"";
}	
    $num++;

      $id55 = $bonus5["id"];
      $class55 = $bonus5["class"];
    $username55 = $bonus5["username"];
 
    $thx55 = $bonus5["num_thanks5"];
    
    $content .= "<tr>
	<td $class>$num.</td>
	<td $class align=\"left\"><a href=\"userdetails.php?id=$id55\">".get_user_class_color($class55, $username55)."</a></td>
	<td $class >$thx55</td></tr>";
}$fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
} /// конец 5

if ($random==6){
$cacheStatFile = "cache/block-heroes_$random.txt"; 
$expire = 45*60; // 60 минут на кеш, после обновление 
if (file_exists($cacheStatFile) && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
$content .= "
<td class=b colspan=3 align=\"center\">—ообщени€ в чате</td></tr>";

$sym3= sql_query("SELECT id, class, username,(SELECT COUNT(*) FROM shoutbox WHERE users.id = shoutbox.userid) AS num_thanks6 FROM users ORDER BY num_thanks6 DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$num = 0;
while ($sym33 = mysql_fetch_array($sym3)) {
	if ($num%2==0) {	$class="class=\"a\""; $class2="class=\"b\""; } else {
$class="class=\"b\""; 	$class2="class=\"a\"";
}	
	
    ++$num;
   $id33 = $sym33["id"];
      $class33 = $sym33["class"];
    $username33 = $sym33["username"];
 
    $thx33 = $sym33["num_thanks6"];
    
    
    $content .= "<tr>
	<td $class>$num.</td>
	<td $class align=\"left\"><a href=\"userdetails.php?id=$id33\">".get_user_class_color($class33, $username33)."</a></td>
	<td $class>$thx33</td></tr>";
}$fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
}/// конец 6

//$content .= "</table></td>";



if ($random==7){
$cacheStatFile = "cache/block-heroes_$random.txt"; 
$expire = 60*60; // 60 минут на кеш, после обновление 
if (file_exists($cacheStatFile) && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
$content .= "

<td class=b colspan=3 align=\"center\">ѕрин€тые сообщени€</td></tr>";
$thx444 = sql_query("SELECT id,username,class, (SELECT COUNT(*) FROM messages WHERE users.id = messages.receiver) AS num_thanks4 FROM users WHERE enabled='yes' ORDER BY num_thanks4 DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$num = 0;
while ($thx4 = mysql_fetch_array($thx444)) {
	if ($num%2==0) {	$class="class=\"a\""; $class2="class=\"b\""; } else {
$class="class=\"b\""; 	$class2="class=\"a\"";
}	
    ++$num;
    $id4 = $thx4["id"];
    $username4 = $thx4["username"];
    $class4 = $thx4["class"];
    $thx4 = $thx4["num_thanks4"];
 

    $content .= "<tr>
	<td $class>$num.</td>
	<td $class align=\"left\" ><a href=\"userdetails.php?id=$id4\">".get_user_class_color($class4, $username4)."</a></td>
	<td $class>$thx4</td></tr>";
}$fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
} //// конец 7

if ($random==8){
	$cacheStatFile = "cache/block-heroes_$random.txt"; 
$expire = 60*60; // 60 минут на кеш, после обновление 
if (file_exists($cacheStatFile) && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
$content .= "
<td class=b colspan=3 align=\"center\">ѕолученные сообщени€</td></tr>";
$thx777 = sql_query("SELECT id,username,class, (SELECT COUNT(*) FROM messages WHERE users.id = messages.sender) AS num_thanks77 FROM users WHERE enabled='yes' ORDER BY num_thanks77 DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$num = 0;
while ($thx77 = mysql_fetch_array($thx777)) {
	if ($num%2==0) {	$class="class=\"a\""; $class2="class=\"b\""; } else {
$class="class=\"b\""; 	$class2="class=\"a\"";
}
    ++$num;
    $id77 = $thx77["id"];
    $username77 = $thx77["username"];
    $class77= $thx77["class"];
    $thx77 = $thx77["num_thanks77"];
 

    $content .= "<tr>
	<td $class>$num.</td>
	<td $class align=\"left\"><a href=\"userdetails.php?id=$id77\">".get_user_class_color($class77, $username77)."</a></td>
	<td  $class>$thx77</td></tr>";
}$fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
}//// конец 8

if ($random==9){
	$cacheStatFile = "cache/block-heroes_$random.txt"; 
$expire = 60*60; // 60 минут на кеш, после обновление 
if (file_exists($cacheStatFile) && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{
$content .= "
<td class=b colspan=3 align=\"center\">«акладки</td></tr>";

$sym81= sql_query("SELECT id, class, username,(SELECT COUNT(*) FROM bookmarks WHERE users.id = bookmarks.userid) AS num_thanks88 FROM users ORDER BY num_thanks88 DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$num = 0;
while ($sym8 = mysql_fetch_array($sym81)) {

if ($num%2==0) {	$class="class=\"a\""; $class2="class=\"b\""; } else {
$class="class=\"b\""; 	$class2="class=\"a\"";
}
    ++$num;
   $id88 = $sym8["id"];
      $class88 = $sym8["class"];
    $username88 = $sym8["username"];
 
    $thx88 = $sym8["num_thanks88"];
    
    
    $content .= "<tr>
	<td $class >$num.</td>
	<td $class align=\"left\"><a href=\"userdetails.php?id=$id88\">".get_user_class_color($class88, $username88)."</a></td>
	<td $class >$thx88</td></tr>";
}$fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
}/// конец 9

$content .= "</table>";









//$content .="</table></td></tr></table>";






$blocktitle = "Ќаши герои [$random]";

/*

        $fp = fopen($cacheStatFile,"w");
   if($fp)
   {
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
 */
 

if (get_user_class() >= UC_SYSOP)
{
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
}
?> 