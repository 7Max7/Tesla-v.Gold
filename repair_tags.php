<?
require "include/bittorrent.php";

dbconn(false);


loggedinorreturn();

stdheadchat("��������� � �������");

if (get_user_class() <= UC_ADMINISTRATOR)  {
attacks_log('repair_tags'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

accessadministration();

echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" align=\"center\">"
	."<form method=\"get\" action=\"repair_tags.php\">"
	."<tr><td>"
	."<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\">"
	
	."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" value=\"updatestr\" ".(empty($_GET['type']) || $_GET['type'] == "updatestr" ? "checked":"")."></td><td><b>1)</b> ������� ���� ����� � ������ �������.</td></tr>"
	
	."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "trun_cloud" ? "checked":"")."  value=\"trun_cloud\"></td><td><b>2)</b> ��������� ��� ������ ���� ����� browse.php</td></tr>"
		
	."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "giga" ? "checked":"")."  value=\"giga\"></td><td><b>3)</b> ��������� ���� ������������� ��� 10 GB (������� �� ����)</td></tr>"
		
	."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "update" ? "checked":"")."  value=\"update\"></td><td><b>4)</b> ������������ ��� (�� ��������) �������� (������ ���� ��� ������).</td></tr>"
	
		
	."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "update_log" ? "checked":"")."  value=\"update_log\"></td><td><b>5)</b> �� ��� �������� ������� (��� ������ ��������) � ����������� � tracker ���</td></tr>"
	
		."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "bur13" ? "checked":"")."  value=\"bur13\"></td><td><b>6)</b> ����� ���������� � �������� �� ����� ��� ������������, ������� ������ 13 ���.</td></tr>"
		
   ."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "timage" ? "checked":"")."  value=\"timage\"></td><td><b>7)</b> ������� ��� ����������� � ����� images, ������� ��� � ���� � ���������</td></tr>"
		
	."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "torrent_file" ? "checked":"")."  value=\"torrent_file\"></td><td><b>8)</b> ������� ��� �������� � ����� torrents, ������� ��� � ����</td></tr>"
		

."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "avat_exis" ? "checked":"")."  value=\"avat_exis\"></td><td><b>9)</b> ��������� ���������� �� ������� � ������������� � ����� pic/avatar/ ����� ������� ��.</td></tr>"
		
."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "avat_base" ? "checked":"")."  value=\"avat_base\"></td><td><b>10)</b> ��������� ���������� �� ������� � ������������� � ����, ����� �������� �������� �������</td></tr>"
		
   ."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "image_exis" ? "checked":"")."  value=\"image_exis\"></td><td><b>11)</b> ��������� ���������� �� ����������� � ���������, ����� ������ - �������� �� ������������ �������� � ����</td></tr>"
	
	."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "tor_exis" ? "checked":"")." value=\"tor_exis\"></td><td><b>12)</b> ��������� ���������� �� ����� .torrent � ���������, ����� ������ - �������� ��������� + � �������� ��� ����� ����������</td></tr>"

	."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "updateset" ? "checked":"")."  value=\"updateset\"></td><td><b>13)</b> ���������� ���������� ��������� �� ���� �����.</td></tr>"
	
	."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "updatemessage" ? "checked":"")."  value=\"updatemessage\"></td><td><b>14)</b> ����������� ���������� ��������� � ���� �������������.</td></tr>"

	."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "times_completed" ? "checked":"")."  value=\"times_completed\"></td><td><b>15)</b> ����������� ���������� ������ ��� ������� ��������.</td></tr>"

	."<tr><td valign=\"top\"><input type=\"radio\" name=\"type\" ".($_GET['type'] == "thumbjpg" ? "checked":"")."  value=\"thumbjpg\"></td><td><b>16)</b> ������� ��� ������ ��� ������ � ������� ���������.</td></tr></table>"
	."</td></tr>"
	
	."<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"��������� ��������\"></td></tr></form></table><br><br>";



	if ($_GET['type'] == "giga") {
		


$res7 = sql_query("SELECT id,class,modcomment,username,uploaded FROM users WHERE id <> 92 and enabled='yes'") or sqlerr(__FILE__,__LINE__);
	while ($row7 = mysql_fetch_assoc($res7)) {
		$id7 = $row7["id"]; 
		$name7 = $row7["username"]; 
		$uploaded = $row7["uploaded"];
        $modcomment_row = $row7["modcomment"];

if ($id7){
$ten=mksize("10737418240");/// ����� 10 
$t_add=$row7["uploaded"]+10737418240;
$ten2=mksize($t_add);
$name7=get_user_class_color($row7["class"],$row7["username"]);
print"$name7 - ���� ".mksize($uploaded)." ����� $ten2 <br>";

$modcomment = date("Y-m-d") . " - ������� �� ���� $ten.\n". $modcomment_row;
$updateset10[] = "modcomment= " . sqlesc($modcomment);
$updateset10[] = "uploaded= " . sqlesc($t_add);
sql_query("UPDATE users SET " . implode(", ", $updateset10) . " WHERE id = '$id7'") or sqlerr(__FILE__, __LINE__);
}
}


		echo "������� ���������� ������ � �������.";
		

		
	} 
	
	if ($_GET['type'] == "update") {
		

//sql_query("UPDATE torrents SET tags=''") or sqlerr(__FILE__, __LINE__);

$res7 = sql_query("SELECT * FROM tags WHERE category <> 11 and category <> 22 and category <>16") or sqlerr(__FILE__,__LINE__);

while ($row7 = mysql_fetch_assoc($res7)) {

$id7 = $row7["id"]; 

$howmuch7 = $row7["howmuch"];
$tags = ($row7["name"]);
$category = $row7["category"];
  
//$q = str_replace(" ",".",sqlesc("%".sqlwildcardesc(trim($tags))."%"));
  
$res9 = sql_query("SELECT id,tags AS nametags FROM torrents WHERE category=".sqlesc($category)." AND tags NOT LIKE ('%$tags%') AND descr LIKE ('%$tags%') LIMIT 10") or sqlerr(__FILE__,__LINE__);

while ($arr9 = mysql_fetch_assoc($res9)){

$arr9_id=$arr9["id"];
$nametags=$arr9["nametags"];

if (!empty($nametags))
$tagsql=$nametags.",".tolower($tags);
else
$tagsql=tolower($tags);



//$ptype=tolower($arr9["category"]);
echo "<b>".$arr9_id."</b> �������� ��� - <b>".$tagsql."</b><br>";

$updateset10[] = "tags = " . sqlesc($tagsql);

if (strlen($tagsql) >=3 ){
sql_query("UPDATE torrents SET " . implode(", ", $updateset10) . " WHERE id = '$arr9_id'") or sqlerr(__FILE__, __LINE__);

sql_query("UPDATE tags SET howmuch=howmuch+1 WHERE id=".sqlesc($id7)) or sqlerr(__FILE__, __LINE__);
}

unset($tagsql);
unset($nametags);
unset($updateset10,$arr9_id);
}

unset($tags);
unset($category);
}


		echo "$sf ���� ���� ������� ����������.";
		

		
	}
	elseif ($_GET['type'] == "updateset") {
		
		// ���������� ������� ����������� �� index.php

///sql_query('UPDATE tags AS t SET t.howmuch = (SELECT COUNT(*) FROM torrents AS ts WHERE ts.tags LIKE CONCAT(\'%\', t.name, \'%\'))');

	
$res6 = sql_query("SELECT * FROM tags") or sqlerr(__FILE__,__LINE__);

	while ($row6 = mysql_fetch_assoc($res6)) {
		$id6 = $row6["id"]; 
		$name6 = $row6["name"]; 
		$howmuch6 = $row6["howmuch"];
$unverified = number_format(get_row_count("torrents", "WHERE tags LIKE '%" . sqlwildcardesc($name6) . "%'"));
if ($howmuch6!=$unverified){
//write_log("�� ���� $name6 ���� - $howmuch6 ����� - $unverified","","tracker"); 
print"� <strong>$name6</strong> ���� - <b>$howmuch6</b> ����� - <b>$unverified</b><br>"; 

$updateset[] = "howmuch = ".sqlesc($unverified);
sql_query("UPDATE tags SET " . implode(", ", $updateset) . " WHERE id = '$id6'") or sqlerr(__FILE__, __LINE__);
unset($updateset);
}
}

			echo "���������� ��� ������� ��������";
	}
	
	
		elseif ($_GET['type'] == "bur13") {
		

$r = sql_query("SELECT * FROM users WHERE birthday>='0000-00-00' and enabled='yes'") or sqlerr(__FILE__, __LINE__);
	while ($user = mysql_fetch_assoc($r)) 
{

if ($user[birthday] != "0000-00-00")
{        //$current = date("Y-m-d", time());
        $current = date("Y-m-d", time() + $CURUSER['tzoffset'] * 60);
        list($year2, $month2, $day2) = explode('-', $current);
        $birthday = $user["birthday"];
        $birthday = date("Y-m-d", strtotime($birthday));
        list($year1, $month1, $day1) = explode('-', $birthday);
        if($month2 < $month1)
        {
         $age = $year2 - $year1 - 1;
        }
        if($month2 == $month1)
        {
                if($day2 < $day1)
                {
                        $age = $year2 - $year1 - 1;
                }
                else
                {
                        $age = $year2 - $year1;
                }
        }
        if($month2 > $month1)
        {
                $age = $year2 - $year1;
        }

}
if ($age<="12") {

print("".$user["username"]." - <b>$birthday</b> [<b>$age</b>] - ������ ������� �� �����, � ��������.<br>\n");


        $USER = sqlesc("92"); // ViKa
		$comment="������ 13 ��� ($user[username])";
		$email= $user["email"];

        mysql_query("INSERT INTO bannedemails (added, addedby, comment, email) VALUES(".sqlesc(get_date_time()).", $USER, ".sqlesc($comment).", ".sqlesc($email).")") ; 


$torrent_com = get_date_time() . " - �������� � ������� system �� �������: ������� ������ 13 ���.\n". $user["modcomment"];
$updateset[] = "enabled='no'";
$updateset[] = "modcomment = '$torrent_com'";

$id6=$user[id];

sql_query("UPDATE users SET " . implode(", ", $updateset) . " WHERE id = '$id6'") or sqlerr(__FILE__, __LINE__)
;
}

}


print("<tr><td align=\"center\"class=\"a\"> ����� </td></tr>\n");
	}
	
	
	elseif ($_GET['type'] == "avat_exis") {



$dh = opendir('pic/avatar/');
while ($file = readdir($dh)) :
$file_orig=$file;
if (preg_match('/^(.+)\.$/si', $file, $matches))
$file = $matches[1];

if (
((stristr($file,'png')) || ((stristr($file,'gif')) || (stristr($file,'bmp')) || (stristr($file,'jpg'))))
&& 
!stristr($file,'default_avatar.gif')

)
{
  $res = sql_query("SELECT username,id,class FROM users WHERE avatar='$file'") or sqlerr(__FILE__, __LINE__);
  $ip = mysql_fetch_array($res);
  if (!$ip){
  $file_name="<b><font color=\"red\">$file - ������ ����</font></b>";
@unlink(ROOT_PATH."pic/avatar/$file");
  }
  else
  $file_name="<font color=\"blue\">$file - <a href=\"userdetails.php?id=$ip[id]\">
  " . get_user_class_color($ip["class"], $ip[username])."</a> - ok</font>";
  }
print "$file_name<br>"; 
endwhile;

closedir($dh);

	}

elseif ($_GET['type'] == "thumbjpg") {
$number=0;
$dh = opendir('torrents/thumbnail/');
while ($file = readdir($dh)) :
$file_orig=$file;
if (preg_match('/^(.+)\.$/si', $file, $matches))
$file = $matches[1];

if (((stristr($file,'png')) || ((stristr($file,'gif')) || (stristr($file,'bmp')) || (stristr($file,'jpg'))))
&& !stristr($file,'.htaccess')){
if ($file_name){
$file_name=($file_name + filesize("torrents/thumbnail/".$file));
} else {
$file_name=filesize("torrents/thumbnail/".$file);
}
@unlink(ROOT_PATH."torrents/thumbnail/".$file);
$number++;
}
endwhile;
closedir($dh);

print "������� <b>$number</b> ��� �������<br>����� ������ ���� ������: ".(mksize($file_name)).""; 




$dh = opendir('torrents/txt/');
while ($file = readdir($dh)) :
$file_orig=$file;
if (preg_match('/^(.+)\.$/si', $file, $matches))
$file = $matches[1];

if (stristr($file,'torrent') || (stristr($file,'txt'))){
if ($file_name){
$file_name=($file_name + filesize("torrents/txt/".$file));
} else {
$file_name=filesize("torrents/txt/".$file);
}
@unlink(ROOT_PATH."torrents/txt/".$file);
$number++;
}

endwhile;

print "<hr>������� <b>$number</b> ��������� ������ �� txt �����<br>����� ������ ���� ������: ".(mksize($file_name)).""; 


closedir($dh); 



	}
	
	elseif ($_GET['type'] == "avat_base") {

 $res = sql_query("SELECT avatar, class,username,id FROM users WHERE avatar!=''") or sqlerr(__FILE__, __LINE__);
  $ip = mysql_fetch_array($res);

	while ($row6 = mysql_fetch_assoc($res)) {

  $image1=$row6["avatar"];
 $username=$row6["username"];
 $id=$row6["id"];
  $class=$row6["class"];
  if(!file_exists("pic/avatar/$image1"))
{
print"��� <a href=\"userdetails.php?id=$id\">" . get_user_class_color($class, $username)."</a> ��� ����� -> $image1<br>";

sql_query("UPDATE users SET avatar= '' WHERE id = '$id'") or sqlerr(__FILE__, __LINE__);
}



}
  

print "$file_name<br>���� ������������� ���� �������� ������ � ���� �� �������"; 

	}
	
	
	
	elseif ($_GET['type'] == "timage") {

$dh = opendir('torrents/images/');
while ($file = readdir($dh)) :
$file_orig=$file;
if (preg_match('/^(.+)\.$/si', $file, $matches))
$file = $matches[1];

if ( (!stristr($file,'default_torrent.png')) &&  (stristr($file,'png')) || ((stristr($file,'gif')) || (stristr($file,'bmp')) || (stristr($file,'jpg'))))
{
  $res = sql_query("SELECT name,id FROM torrents WHERE image1='$file'") or sqlerr(__FILE__, __LINE__);
  $ip = mysql_fetch_array($res);
  if (!$ip){
  $file_name="<b><font color=\"red\">$file - ������ ����</font></b>";
unlink(ROOT_PATH."torrents/images/$file");
  }
  else
  $file_name="<font color=\"blue\">$file - <a href=\"details.php?id=$ip[id]\">$ip[name]</a> - ok</font>";
  }
print "$file_name<br>"; 
endwhile;

closedir($dh); 

}
	
	
	
		elseif ($_GET['type'] == "torrent_file") {

$dh = opendir('torrents/');
while ($file = readdir($dh)) :
$file_orig=$file;
preg_match('/^(.+)\.torrent$/si', $file, $matches);
$file = $matches[1];

if (stristr($file,"[up]") !== false || stristr($file,"[ta]") !== false){
$sql_where="save_as='$file'";
} else {
$file=(int)$file;
$sql_where="id='$file'";
}
if ($file)
{
  $res = mysql_query("SELECT name,id FROM torrents WHERE $sql_where") or sqlerr(__FILE__, __LINE__);
  $ip = mysql_fetch_array($res);
  if (!$ip){
  $file_name="<b><font color=\"red\">$file - ������ ���� $file_orig</font></b>";
  @unlink(ROOT_PATH."torrents/$file_orig");
//print("$file_name");
print "$file_name<br>"; 
unset($file_name);unset($file_orig);
  }
 // else
 // $file_name="<font color=\"blue\">$file - </font> <a href=\"details.php?id=$ip[id]\">$ip[name]</a> - <font color=\"blue\">$ip[id]</font>";

  }

endwhile;

closedir($dh); 
 
	}
	
	
	
	
	elseif ($_GET['type'] == "update_log") {
		
$res6 = sql_query("SELECT * FROM sitelog WHERE txt LIKE '%��� ������ ��������%' and type='bans' ") or sqlerr(__FILE__,__LINE__);

	while ($row6 = mysql_fetch_assoc($res6)) {
		$id6 = $row6["id"]; 
	//	$name6 = $row6["name"]; 
	//	$howmuch6 = $row6["howmuch"];



$updateset[] = "type = 'tracker'";
sql_query("UPDATE sitelog SET " . implode(", ", $updateset) . " WHERE id = '$id6'") or sqlerr(__FILE__, __LINE__);
}

			echo "�� ��� � tracker ��� ������� ����������.";
	}
	
	elseif ($_GET['type'] == "updatestr") {
		
		
	$res3 = sql_query("SELECT name, id FROM tags WHERE id > '0'") or sqlerr(__FILE__,__LINE__);
	if (mysql_num_rows($res3)) {
		
		while ($arr3 = mysql_fetch_array($res3)) {
			$arrr1=$arr3["name"];
	
			$arrr=tolower($arr3["name"]);
         	$arrr_id=$arr3["id"];
         	
		$updateset[] = "name = ".sqlesc($arrr);

	sql_query("UPDATE tags SET " . implode(", ", $updateset) . " WHERE id = $arrr_id") or sqlerr(__FILE__, __LINE__);
	unset($updateset);
		}
		}
/// ��� ���������
	$res4 = sql_query("SELECT tags, id FROM torrents") or sqlerr(__FILE__,__LINE__);
	if (mysql_num_rows($res4)) {
		
		while ($arr4 = mysql_fetch_array($res4)) {
			$arrr2=$arr4["tags"];
	
			$arrr2=tolower($arr4["tags"]);
         	$arrr_id2=$arr4["id"];
         	
		$updateset2[] = "tags = ".sqlesc($arrr2);

	sql_query("UPDATE torrents SET " . implode(", ", $updateset2) . " WHERE id = $arrr_id2") or sqlerr(__FILE__, __LINE__);
		unset($updateset2);
	
		}}
		
		

			echo "��� ���� ������� ���� ����������� � ������ �������";
	}

	elseif ($_GET['type'] == "image_exis") {
		if (stristr($file,"[up]") !== false || stristr($file,"[ta]") !== false){
$sql_where="save_as='$file'";
} else {
$file=(int)$file;
$sql_where="id='$file'";
}
$res6 = sql_query("SELECT image1,id, name FROM torrents") or sqlerr(__FILE__,__LINE__);

	while ($row6 = mysql_fetch_assoc($res6)) {
	$id6 = $row6["id"]; 
	$name6 = $row6["name"]; 
	$image1 = $row6["image1"]; 
	

	
if(!preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image1) && !file_exists("torrents/images/".$image1)) {

print"��� �������� <a href=\"details.php?id=$id6\">$name6</a> ��� ����� - $image1<br>";
	
$updateset[] = "image1 = ''";
sql_query("UPDATE torrents SET " . implode(", ", $updateset) . " WHERE id = '$id6'") or sqlerr(__FILE__, __LINE__);
}


}
	print"<b>��� ����������� ���������� �� ���� � ���������</b>";


	}
		elseif ($_GET['type'] == "tor_exis") {
	
	
	

$res6 = sql_query("SELECT id, name,descr,torrent_com FROM torrents WHERE moderated = 'yes' LIMIT 15000") or sqlerr(__FILE__,__LINE__);

	while ($row6 = mysql_fetch_assoc($res6)) {
	$id6 = $row6["id"]; 
	$name6 = $row6["name"]; 
	$descr6 = $row6["descr"]; 
	$torrent_com = $row6["torrent_com"]; 
	
$descr6 = preg_replace("/\'/", "", $descr6);

$descr6 = htmlspecialchars($descr6);
$id_6=$id6;

if(!file_exists("torrents/$id_6.torrent"))
{
print"��� �������� <a href=\"details.php?id=$id6\">$name6</a> ��� ����� - $id6.torrent<br>";

$descr6 = "[size=18][center][b][color=Red]��� ����� .torrent � �������, ����������� ���![/color] a.k.a [color=Blue]System[/color][/b][/center][/size]\n".$descr6;

$torrent_com = get_date_time() . " - System ����� ��������� (��� torrent �����).\n". $torrent_com;


$updateset[] = "torrent_com = '$torrent_com'";
$updateset[] = "moderated = 'no'";
$updateset[] = "moderatedby = ''";
$updateset[] = "descr = '$descr6'";


sql_query("UPDATE torrents SET " . implode(", ", $updateset) . " WHERE id = '$id6'") or sqlerr(__FILE__, __LINE__)
;
}


}

	
	
	
	print"<b>��� ����� .torrent ���������� � ���������</b> ����� - 150";


	}
	
	elseif ($_GET['type'] == "trun_cloud") {
{
sql_query("TRUNCATE TABLE searchcloud");
sql_query("DELETE FROM sitelog WHERE type='search'");
print("��������� ��� ����� ������");
}

}

elseif ($_GET['type'] == "updatemessage") {

$sq=sql_query("SELECT id,username,unread,(SELECT COUNT(*) FROM messages WHERE receiver=users.id AND location=1 AND unread='yes') AS num_unread FROM users") or sqlerr(__FILE__, __LINE__); 
while ($row=mysql_fetch_array($sq)){

if ($row["num_unread"]<>$row["unread"]){
echo "��������� � <b>".$row["username"]."</b> ����: ".$row["unread"]." �����:  ".$row["num_unread"]."";
echo "<br>";

sql_query("UPDATE users SET unread=".sqlesc($row["num_unread"])." WHERE id=" . $row["id"])  or sqlerr(__FILE__, __LINE__);
}
}
}
elseif ($_GET['type'] == "times_completed") {




$res = sql_query("SELECT id, times_completed, (SELECT COUNT(*) FROM snatched WHERE torrents.id=torrent and finished='yes') AS nusnat FROM torrents") or sqlerr(__FILE__,__LINE__);
while ($arr = mysql_fetch_assoc($res)) {

if (isset($arr["nusnat"]))
$arr["nusnat"]=$arr["nusnat"];
else
$arr["nusnat"]=0;

if (isset($arr["times_completed"]))
$arr["times_completed"]=$arr["times_completed"];
else
$arr["times_completed"]=0;

if ($arr["times_completed"]<>$arr["nusnat"]){
echo "���� <b>".$arr["times_completed"]."</b> ����� <b>".$arr["nusnat"]."</b> � id ".$arr["id"]."<br>";

sql_query("UPDATE torrents SET times_completed=".sqlesc($arr["nusnat"])." WHERE id=" . $arr["id"])  or sqlerr(__FILE__, __LINE__);

}
}

}



///chmod("/somedir/somefile", 0755);  // ������������, ������ ������




stdfootchat(); 
?>