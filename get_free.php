<?php
require "include/bittorrent.php"; 
dbconn(); 
loggedinorreturn(); 

stdheadchat("������������ ��������"); 

$id = (int) $_GET["id"]; 

$procent = 10; //������� ��������� �� ������� �������� (� ��) ����� ������������ (� �������) 

if (empty($id)) {
  stdmsg($tracker_lang['error'], "�������� ID ��������!"); 
  stdfoot();
  die(); 
} 

$res = sql_query("SELECT id,free,size FROM torrents WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__); 

$torr = mysql_fetch_assoc($res); 

if(!$torr["id"]) {
stdmsg($tracker_lang['error'], "�������� � ID <b>". $id ."</b> �� ����������!"); 
stdfootchat();
die(); 
} 

if ($torr["free"] == "yes") {
stdmsg("������� ��� ���������!", "�������"); 
print("<meta http-equiv=\"refresh\" content=\"3; URL=details.php?id=$id\">"); 
stdfootchat();
die(); 
}

$amount_tot = $torr["size"] / (1024*1024); 
$amount = $amount_tot*$procent/100; 

if ($CURUSER["bonus"] < $amount) {
stdmsg("�����", "� ��� �� ������� �������!"); 
print("<meta http-equiv=\"refresh\" content=\"3; URL=details.php?id=$id\">"); 
stdfootchat();
die(); 
} 

if (!sql_query("UPDATE users SET bonus = bonus - ".sqlesc($amount)." WHERE id = ".sqlesc($CURUSER["id"])."")) {
stdmsg($tracker_lang['error'], "������, ���������� ��� ���!"); 

print("<meta http-equiv=\"refresh\" content=\"3; URL=details.php?id=$id\">"); 
stdfootchat();
die(); 
}

if (!sql_query("UPDATE torrents SET free_who = ".sqlesc($CURUSER["id"])." WHERE id = ".sqlesc($id)."")) {
stdmsg("������", "������, ���������� ��� ���!"); 
print("<meta http-equiv=\"refresh\" content=\"3; URL=details.php?id=$id\">"); 
stdfootchat();
die(); 
}


sql_query("UPDATE torrents SET free = 'yes' WHERE id = ".sqlesc($id)) or sqlerr($sql,__LINE__); 
        
        
echo "<meta http-equiv=\"refresh\" content=\"0; URL=details.php?id=$id\">";

?>