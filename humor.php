<?
require "include/bittorrent.php";
dbconn();
//loggedinorreturn();
 
parse_referer("empty");


/*
ALTER TABLE `humor` CHANGE `txt` `txt` VARCHAR( 1000 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL;
*/

function bark($msg, $error = true) {
global $tracker_lang;
stdhead();
stdmsg(($error ? $tracker_lang['error'] : $tracker_lang['success']), $msg."<br><b>[</b><a href=\"humor.php\">�������� �������</a><b>]</b>", ($error ? 'error' : 'success'));
stdfoot();
die;
}


stdhead("�������� / �������� �������");

if (isset($_POST['GRI_E']) && $CURUSER){

$hum = htmlspecialchars_uni(strip_tags($_POST['GRI_E']));

if (empty($hum) || strlen($hum) <= 10)
bark("������� �������� �����",true);


$numsql = (substr($num,0,20)."");
$hu = sql_query("SELECT txt FROM humor WHERE txt LIKE '%$numsql%'") or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($hu) == 1)
bark("����� ������� ��� ���� � ���� : ��������������� �� �������� �������. <script>setTimeout('document.location.href=\"/humor.php\"', 5000);</script>",true);

sql_query("INSERT INTO humor (uid,txt) VALUES(" .  sqlesc($CURUSER["id"]) . ", " .  sqlesc($hum) . ")") or sqlerr(__FILE__, __LINE__);


bark("��������� � ����",false);

@header("Refresh: 5; url=humor.php") or die("��������������� �� ��������.<script>setTimeout('document.location.href=\"humor.php\"', 5000);</script>");
die;
}

elseif (isset($_POST['GRI']) && get_user_class() >= UC_MODERATOR) {

$id = (int)$_POST['idi_txt'];

$hum = htmlspecialchars_uni(strip_tags($_POST['GRI']));

sql_query("UPDATE humor SET txt = ".sqlesc($hum)." WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

bark("��������� ��� ������. ������������ �� ����� ��������. <script>setTimeout('document.location.href=\"humor.php?id=$id\"', 5000);</script>",false);

die;
}



$id = (int) $_GET['id'];

if (get_user_class() >= UC_MODERATOR && isset($_GET['do'])){


if ($_GET['do'] == "delete"){

sql_query("DELETE FROM humor WHERE id=".$id." LIMIT 1") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM karma WHERE type='humor' AND value=".$id."") or sqlerr(__FILE__, __LINE__); 

bark("������� ������. ��������������� �� �������� ����� ������ ��������. <script>setTimeout('document.location.href=\"/humor.php\"', 5000);</script>",false);


} elseif ($_GET['do'] == "edit"){

$hu = sql_query("SELECT txt FROM humor WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

if (!mysql_num_rows($hu))
bark("�������� id ��� ������",false);

$res = mysql_fetch_assoc($hu);

$text = htmlspecialchars_uni($res["txt"]);

echo "<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr>

<td align=\"center\" class=\"a\">�������������� ��������</td></tr>
<tr>
<td align=\"center\" class=\"rowhead3\">
<form action=\"humor.php\" method=\"post\">

<style type=\"text/css\"><!--
.En_J {
font-size:11;  
color:#FF00FF;
font-family:Verdana;  
  
padding-top:4; }  

.E_nJ { 
font-size:11;       
font-family:Verdana;
width:70;  
height:21; 
text-align:center; }
--></style>

<center>
<textarea class=\"En_J\" cols=\"100\" rows=\"6\"  name=\"GRI\" onkeypress=\"EnJ_GrI.value=GRI.value.length\" onchange=\"EnJ_GrI.value=GRI.value.length\">".$text."</textarea>
<br>
<input class=\"E_nJ\" disabled onfocus=\"GRI.focus()\" name=\"EnJ_GrI\">
</center>
<input type=\"hidden\" name=\"idi_txt\" value=\"$id\"/>
<input class=\"btn\" value=\"�������� �������\" type=\"submit\">
</form>
<br>

</td>
<tr></table>";
}

}

elseif (!isset($_GET['do']) && isset($_GET['id'])) {


$hu = sql_query("SELECT humor.*, users.username, users.class FROM humor LEFT JOIN users ON users.id=humor.uid WHERE humor.id=".$id." LIMIT 1") or sqlerr(__FILE__, __LINE__);

if ( mysql_num_rows($hu) == 0)
bark("������� �� ������ ��� ������. ���������� ��������� ��������� ������.",true);

$res = mysql_fetch_assoc($hu);
       
echo "<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";

echo "<tr>
<td align=\"center\" class=\"a\" colspan=\"2\"><b>������� � ���� ��� ������</b>: <a title=\"���������� ������ ��� ����� ��������\" href=\"humor.php?id=".$id."\">".$id."</a> (".$res['date'].")</td>
<tr>";

echo "<tr>
<td align=\"center\" class=\"b\">".format_comment($res['txt'])."</td>
<tr>";

echo "<tr>
<td align=\"center\" class=\"a\" colspan=\"2\">
<div style=\"text-align:right;\">
".(!empty($res["username"]) ? "<b>����� �������</b>: <a href=userdetails.php?id=".$res['uid'].">".get_user_class_color($res['class'],$res['username'])."</a>":"����� �������: <u>����������</u>.")."

".(get_user_class() >= UC_MODERATOR ? "<b>[</b><a href=humor.php?id=".$id."&do=edit>�������������</a><b>]</b> <b>[</b><a href=humor.php?id=".$id."&do=delete>�������</a><b>]</b>":"")." </div>
</td>
<tr>";

echo "</table>";

echo "<br>";

echo "<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";

echo "<tr>
<td align=\"center\" class=\"a\" colspan=\"2\"><b>��������� ������������</b>: (����� 100 �������������)</td>
<tr>";

$num_karma = number_format(get_row_count("karma", "WHERE karma.value=".$id));

$hstat = sql_query("SELECT karma.*, users.username, users.class FROM karma LEFT JOIN users ON users.id=karma.user 
WHERE karma.value=".$id." ORDER BY karma.added DESC LIMIT 100") or sqlerr(__FILE__, __LINE__);

echo "<tr><td align=\"center\" class=\"b\">";

$array_date = array();


if (mysql_num_rows($hstat) == 0)
echo "��� ������ � �������������.";

while ($row = mysql_fetch_assoc($hstat)){

$array_date[] = $row["added"];

if ($st == true)
echo ", ";

echo "<a href=userdetails.php?id=".$row['user'].">".get_user_class_color($row['class'],$row['username'])."</a>";
$st = true;
}


echo "</td><tr>";

$min = @min($array_date);
$max = @max($array_date);


echo "<tr>
<td align=\"center\" class=\"a\" colspan=\"2\"><b>����� ".$num_karma." �������".(!empty($num_karma) ? ", ��� ��������� ������</b>: <br> ".get_date_time($min)." - ".get_date_time($max):"</b>.")."</td>
<tr>";

echo "</table>";
echo "<br>";



echo "<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";

if ($CURUSER)
echo "<tr><td align=\"center\" class=\"b\" colspan=\"2\"><a href=\"humor.php\" title=\"���� ������ ���� �������!\"><b>���� ������ ���� �������!</b></td></tr>";

echo "<tr><td align=\"center\" class=\"b\" colspan=\"2\"><a href=\"humorall.php\" title=\"���� ����������� ��� ��������\"><b>���� ����������� ��� ��������!</b></td></tr>";

echo "</table>";
echo "<br>";


} else {


?>
<style type=text/css>
<!--
.En_J {
font-size:11px;
color:#FF00FF;
font-family:Verdana;  
  
padding-top:4px; }  

.E_nJ { 
font-size:11px;       
font-family:Verdana;
width:70%;  
height:21px; 
text-align:center; }
-->
</style>
<?





echo "<br>
<form action=\"humor.php\" method=\"post\">
<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";

echo "
<tr>
<td align=\"center\" class=\"a\" colspan=\"2\">
<b>��������</b>: ������� ������ ����� ����������� �������� � ����.
</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">������� (����� ����) �������� ����� ����� ������ �����������.</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">���� ������� �� ������ ��� �������� �����, )))))) � ������ �����, �� �� �� ����� ������� � � ����, ������� ��� � ������� �����.</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">���� ������� ������� � �������� �� ���� ��� ��������� ������, �� ����� ����� ��������.</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">
�������� <b>1000</b> ��������, <b>BB</b> ���� ���������</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">���������� CapsLock (��� ������������� <u>�������</u>)</td></tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">
��������� ���������</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">
��������� ������������ ��� � ����� ���������� ������ ������������.</td>
</tr>

<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">
�� ������ ����� (����������� ���������� ��� �� ����������)</td>
</tr>

<tr>
<td align=\"center\" class=\"a\" colspan=\"2\">
<b>����������</b> - ��� ����������� <b>25 ��</b> � ������� ��� ������� ������� >=1<br>
<b>�� ����������</b> - � ��� ����������� <b>12,5 ��</b> �� ������� ��� �������� >=1
</td>
</tr>";


echo "<tr>
<td align=\"center\" class=\"b\" colspan=\"2\">

<textarea class=\"En_J\" cols=\"100\" rows=\"6\" name=\"GRI_E\" onkeypress=\"EnJ_GrI.value=GRI_E.value.length\" onchange=\"EnJ_GrI.value=GRI_E.value.length\"></textarea>

<br>
<input class=\"E_nJ\" disabled onfocus=\"GRI_E.focus()\" name=\"EnJ_GrI2\">

<input class=\"btn\" value=\"�������� �������\" type=\"submit\">

</td><tr>";


echo "<tr><td align=\"center\" class=\"b\" colspan=\"2\"><a href=\"humorall.php\" title=\"���� ����������� ��� ��������\"><b>���� ����������� ��� ��������!</b></td></tr>";

echo "</table></form><br>";
}




stdfoot();

    
?> 