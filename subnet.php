<?

require "include/bittorrent.php";
dbconn();
loggedinorreturn();


if (get_user_class() == UC_SYSOP) {

/*if (!$_GET["id"]) {
stderr("Ошибка", "Не верен id пользователя!");
}*/


$userid = (int) $_GET["id"];
if (!$userid) {
header("Location: subnet.php?id=".$CURUSER["id"]."");
//stderr("Ошибка", "Не указан id пользователя!");
}

$res = mysql_query("SELECT id, username, class, ip FROM users WHERE id =".sqlesc($userid)."") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_array($res);
if (!$arr){
stderr("Ошибка", "Пользователь не найден!");
}

$userid = $arr["id"];
$username = $arr["username"];
$userip = $arr["ip"];

$mask = "255.255.255.0";
$tmpip = explode(".",$userip);
$ip = $tmpip[0].".".$tmpip[1].".".$tmpip[2].".0";
$regex = "/^(((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))(\.\b|$)){4}$/";
if (substr($mask,0,1) == "/"){
   $n = substr($mask, 1, strlen($mask) - 1);
   if (!is_numeric($n) or $n < 0 or $n > 32){
   stderr("Ошибка", "Неверная маска подсети.");
   } else {
   $mask = long2ip(pow(2,32) - pow(2,32-$n));
   }
} elseif (!preg_match($regex, $mask)){
stderr("Ошибка", "Неверная маска подсети.");
}

stdhead("Сетевые соседи для ".$username);

// ссылки вперед - назад
?>
<script language="JavaScript">
<!--
function FP_swapImg() {//v1.0
 var doc=document,args=arguments,elm,n; doc.$imgSwaps=new Array(); for(n=2; n<args.length;
 n+=2) { elm=FP_getObjectByID(args[n]); if(elm) { doc.$imgSwaps[doc.$imgSwaps.length]=elm;
 elm.$src=elm.src; elm.src=args[n+1]; } }
}

function FP_preloadImgs() {//v1.0
 var d=document,a=arguments; if(!d.FP_imgs) d.FP_imgs=new Array();
 for(var i=0; i<a.length; i++) { d.FP_imgs[i]=new Image; d.FP_imgs[i].src=a[i]; }
}

function FP_getObjectByID(id,o) {//v1.0
 var c,el,els,f,m,n; if(!o)o=document; if(o.getElementById) el=o.getElementById(id);
 else if(o.layers) c=o.layers; else if(o.all) el=o.all[id]; if(el) return el;
 if(o.id==id || o.name==id) return o; if(o.childNodes) c=o.childNodes; if(c)
 for(n=0; n<c.length; n++) { el=FP_getObjectByID(id,c[n]); if(el) return el; }
 f=o.forms; if(f) for(n=0; n<f.length; n++) { els=f[n].elements;
 for(m=0; m<els.length; m++){ el=FP_getObjectByID(id,els[n]); if(el) return el; } }
 return null;
}

function FP_swapImgRestore() {//v1.0
 var doc=document,i; if(doc.$imgSwaps) { for(i=0;i<doc.$imgSwaps.length;i++) {
  var elm=doc.$imgSwaps[i]; if(elm) { elm.src=elm.$src; elm.$src=null; } } 
  doc.$imgSwaps=null; }
}
// -->
</script>
<?
    $needid = sql_query("(SELECT id FROM users WHERE id < ".sqlesc($userid)." ORDER BY id DESC LIMIT 1) UNION (SELECT id FROM users WHERE id > ".sqlesc($userid)." ORDER BY id ASC LIMIT 1)") or sqlerr(__FILE__, __LINE__);   
    list($previd) = mysql_fetch_array($needid); 
    list($nextid) = mysql_fetch_array($needid);
    
    if ($previd){
    $prevpict = "<a href='subnet.php?id=".$previd."'><img hspace='3' border='0' src='pic/prev.png' width='14' height='17' alt='Предыдущий' align='absmiddle' id='img3' onmouseout='FP_swapImgRestore()' onmouseover='FP_swapImg(1,1,/*id*/\"img3\",/*url*/\"pic/prevnot.png\")'></a>";
    } else {
    $prevpict = "<img hspace='3' border='0' src='pic/prevnot.png' width='14' height='17' alt='Пусто' align='absmiddle'>";
    }

    if ($nextid){
    $nextpict = "<a href='subnet.php?id=".$nextid."'><img hspace='3' border='0' src='pic/next.png' width='14' height='17' alt='Следующий' align='absmiddle' id='img4' onmouseout='FP_swapImgRestore()' onmouseover='FP_swapImg(1,1,/*id*/\"img4\",/*url*/\"pic/nextnot.png\")'></a>";  
    } else {
    $nextpict = "<img hspace='3' border='0' src='pic/nextnot.png' width='14' height='17' alt='Пусто' align='absmiddle'>";
    }
// ссылки вперед - назад


$res = sql_query("SELECT id, username, class, last_access, added FROM users WHERE id <> ".$userid." AND INET_ATON(ip) & INET_ATON('$mask') = INET_ATON('$ip') & INET_ATON('$mask')") or sqlerr(__FILE__, __LINE__);
$num = mysql_num_rows($res);

print("<table border='1' cellspacing='0' cellpadding='5'>");
print("<tr><td class='colhead' align='center' colspan='4'>");

print('<div align="center">
       <table border="0" cellspacing="0" cellpadding="0" style="border: none; background: transparent;" width="100%">
       <tr>
       <td width="20" style="border: none;">'.$prevpict.'</td>
       <td style="border: none;" align="center"><b><span style="font-size: 10pt;">::&nbsp;Сетевые соседи для <a href="userdetails.php?id='.$userid.'" target="_blank">'.get_user_class_color($arr["class"],$arr["username"]).'</a>&nbsp;::</span></b></td>
       <td width="20" align="right" style="border: none;">'.$nextpict.'</td>
       </tr>
       </table>
       </div>');
print("</td></tr>");
print("<tr><td colspan='4'><center>Пользователи, от которых скорость может быть выше, чем от других пиров</center></td></tr>");

print("<tr>");
print("<td class='colhead' align='center'>Пользователь</td>
       <td class='colhead' align='center'>Зарегистрирован</td>
       <td class='colhead' align='center'>Последний доступ</td>
       <td class='colhead' align='center'>IP</td>");
print("</tr>");

if (!$num){
print("<tr><td align='center' colspan='4'><font color='red'><b>Сетевых соседей для ".$username."  не обнаружено</b></font></td></tr>");
} else {

  while($arr=mysql_fetch_assoc($res)){

  print("<tr>");
  print("<td align='center'><a href=userdetails.php?id=".$arr["id"].">".get_user_class_color($arr["class"], $arr["username"])."</a></td>");
  print("<td>".$arr["added"]."</td>");
  print("<td>".$arr[last_access]."</td>");
  print("<td>".$tmpip[0].".".$tmpip[1].".".$tmpip[2].".*</td>");
  print("</tr>");
  
  }
}

print("</table>");
stdfoot();
}
else {

function ratios($up,$down, $color = True)
{
if ($down > 0)
{
$r = number_format($up / $down, 2);
if ($color)
$r = "<font color=".get_ratio_color($r).">$r</font>";
}
else
if ($up > 0)
$r = "Inf.";
else
$r = "---";
return $r;
}
$mask = "255.255.255.0";
$tmpip = explode(".",$CURUSER["ip"]);
$ip = $tmpip[0].".".$tmpip[1].".".$tmpip[2].".0";
$regex = "/^(((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))(\.\b|$)){4}$/";
if (substr($mask,0,1) == "/")
{
$n = substr($mask, 1, strlen($mask) - 1);
if (!is_numeric($n) or $n < 0 or $n > 32)
{
stdmsg($tracker_lang['error'], "Неверная маска подсети.");
stdfoot();
die();
}
else
$mask = long2ip(pow(2,32) - pow(2,32-$n));
}
elseif (!preg_match($regex, $mask))
{
stdmsg("Оишбка", "Неверная маска подсети.");
stdfoot();
die();
}
$res = sql_query("SELECT id, username, class, last_access, added, uploaded, downloaded FROM users WHERE enabled='yes' AND status='confirmed' AND id <> $CURUSER[id] AND INET_ATON(ip) & INET_ATON('$mask') = INET_ATON('$ip') & INET_ATON('$mask')") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res)){
stdhead("Сетевые соседи");

print("<table border=1 cellspacing=0 cellpadding=5>\n");
print("<tr><td class=colhead align=center colspan=8>:: Сетевые соседи ::</td></tr><tr><td colspan=8>Эти пользователи ваши сетевые соседи, что означает что вы получите от них скорость выше.</td></tr>");
print("<tr><td class=colhead align=left>Пользователь</td>
<td class=colhead>Раздал</td><td class=colhead>Скачал</td>
<td class=colhead>Рейтинг</td><td class=colhead>Зарегистрирован</td>
<td class=colhead>Последний доступ</td><td class=colhead align=left>Класс</td>
<td class=colhead>IP</td></tr>\n");
while($arr=mysql_fetch_assoc($res)){
print("<tr><td align=left><b><a href=userdetails.php?id=$arr[id]>".get_user_class_color($arr["class"], $arr["username"])."</a></b></td>
<td>".mksize($arr["uploaded"])."</td>
<td>".mksize($arr["downloaded"])."</td>
<td>".ratios($arr["uploaded"],$arr["downloaded"])."</td>
<td>$arr[added]</td><td>$arr[last_access]</td>
<td align=left>".get_user_class_name($arr["class"])."</td>
<td>".$tmpip[0].".".$tmpip[1].".".$tmpip[2].".*</td></tr>\n");
}
print("</table>");
stdfoot();}
else
stderr("Информация","Сетевых соседей не обнаружено.");
}
?>