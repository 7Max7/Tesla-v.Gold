<?
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
stdheadchat('Проверка Порта');

if (get_user_class() < UC_MODERATOR)
 {
attacks_log('testport'); stderr("Ошибочка", "Только для Администрации..."); die();
}

$ip = $port = "";
if (get_user_class() >= UC_MODERATOR)
$ip = ($_POST['ip'] ? (long2ip(ip2long($_POST['ip']))) : (long2ip(ip2long($_GET['ip']))));
$port = ($_POST['port'] ? ((int)$_POST['port']) : ((int)$_GET['port']));

if (empty($ip))
$ip = $CURUSER["ip"];

if ($port)
{
$fp = @fsockopen ($ip, $port, $errno, $errstr, 10);
if (!$fp)
{
print ("<table width=40% id=torrenttable class=tableoutborder cellspacing=1 cellpadding=5><br><tr>".
"<td class=tabletitle align=center><b>Порт:</b></td></tr><tr><td class=tableb><font color=darkred><br><center><b>IP: $ip , Port: $port Open!</b></center><br></font></td></tr><tr><td class=tableb><center><form action=\"testport.php\" method=\"post\"><INPUT TYPE=\"BUTTON\" VALUE=\"Проверить\" /></form></center></td></tr></table");
}
else
{
print ("<table width=40% id=torrenttable class=tableoutborder cellspacing=1 cellpadding=5><br><tr>".
"<td class=tabletitle align=center><b>Порт:</b></td></tr><tr><td class=tableb><font color=darkgreen><br><center><b>IP: $ip , Port: $port Closed!</b></center><br></font></td></tr><tr><td class=tableb><center><form action=\"testport.php\" method=\"post\"><INPUT TYPE=\"BUTTON\" VALUE=\"Проверить\" /></form></center></td></tr></table>");
}
}
else
{
print("<table width=40% id=torrenttable class=tableoutborder cellspacing=1 cellpadding=5><br><tr>".
"<td class=tabletitle align=center><b>Порт:</b></td>".
"</tr>");
print("</table>");
print ("<form method=post action=testport.php>");
print ("<table width=\"40%\" border=1 cellspacing=0 cellpadding=5>");
print ("<tr><td class=tableb><center>Номер Порта:<center></td><td class=tableb><center><input type=text name=port></center></td></tr>");
if (get_user_class() >= UC_MODERATOR)
print ("<tr><td class=tableb><center>IP (Оставить пустым если ваш ип):<center></td><td class=tableb><center><input type=text name=ip></center></td></tr>");
print ("<tr><td class=tableb colspan=2><center><input type=submit class=btn value='Проверить'></center></td></tr>");
print ("</form>");
print ("</table>");
}
stdfootchat();
?> 