<?
require_once("include/bittorrent.php");

dbconn();
loggedinorreturn();


function bark($msg) {
	stderr("������", $msg);
}
function bark_2($yes,$msg) {
	stderr($yes, $msg);
}


if ($_POST['ful_id'] && $_POST['idi']){
//	print" ".$_POST['idi']."";
	//die();
$idi = (int) $_POST['idi']; //// ����� ������ ��������
$work = (int) $_POST['ful_id']; /// ����� ������ �������� ���������
$kem=$CURUSER["id"]; /// ��� ��������
if ($_POST['perform']=="yes"){	$sql_yes="yes";}
if ($idi && $work)
{
$res = sql_query("SELECT perform FROM off_reqs WHERE id = ".sqlesc($idi));
$row = mysql_fetch_array($res);
if (!$row) {
	die("����� ������ �� ����������!");
	}

//if ($row["perform"])
//{
//bark("������! ������ ������ ��� ��������.\n");
//}


$res = sql_query("SELECT name FROM torrents WHERE id = ".sqlesc($work));
$row = mysql_fetch_array($res);
if (!$row)
stderr($tracker_lang['error'],"����� ������� �� ����������!");

$time=get_date_time();
sql_query("UPDATE off_reqs SET ful_id=".sqlesc($work).", perform='yes', fulfilled=".sqlesc($kem).",data_perf=".sqlesc($time)." WHERE id = ".sqlesc($idi));

 @header ("Refresh: 3; url=detailsoff.php?id=".$idi."");
stderr("������! � 3 ������� ��������", "������ ������ ��� ������� ��� �������� <a href=\"detailsoff.php?id=".$idi."\">����� �������������</a>");

//bark("������! ������ ������ ��� ������� ��� ��������.\n");
}
@unlink(ROOT_PATH."cache/block_menu_off.txt");
}


//////////////////////// �������������� 

if (!mkglobal("id:name:descr:type"))
    die("missing form data");
    

$id = (int) $id;
if (empty($id))
die("�������� id �������");
	
$user=$CURUSER["username"];

if ($_POST["reacon"]) /// ������������� ��������
{
$res = sql_query("SELECT name,owner FROM off_reqs WHERE id = ".sqlesc($id));
$row = mysql_fetch_array($res);
if (!$row)
	stderr($tracker_lang['error'],"������ ������� �� ����������!");

if ($CURUSER["id"] <> $row["owner"] && get_user_class() < UC_MODERATOR)
	bark("�� �� ��������! ��� ����� ����� ���������?\n");

$rt = (int) $_POST["reasontype"];
$dup = format_comment ($_POST["dup"]);
$rule = format_comment ($_POST["rule"]);
if (!is_int($rt) || $rt < 1 || $rt > 3)
	bark("�� ������� ������� ��� �������� ������ ��� ��������.");
if ($rt == 1)
	$reasonstr = "�������, ��������";
elseif ($rt == 2)
{
 if (!$dup) {bark("�� �� ������� ������ (������) � ���� ��������.");}
	$reasonstr = "��������" . ($dup ? (" ��: ".$dup) : "!");
}
elseif ($rt == 3)
{
  if (!$rule) {bark("�� �� �������� ���� ������, ������� ���� ������ �������.");}
  $reasonstr = "�������: ".$rule;
}
if($row["owner"] == $CURUSER["id"]) {$owne = "������";}

  sql_query("DELETE FROM checkcomm WHERE checkid = ".sqlesc($id)." AND userid = $CURUSER[id] AND offer = 1") or sqlerr(__FILE__,__LINE__);
  
  sql_query("DELETE FROM off_reqs WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);

  sql_query("DELETE FROM comments WHERE offer = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);

write_log("$owne ������ $id ($row[name]) ��� ������ $CURUSER[username]: $reasonstr\n","52A77C","torrent");  

bark_2("�������","<h2>������ ".$row["name"]." ������� ������.</h2><br>
$reasonstr");
}
/// ����� �������� ��������




$res = sql_query("SELECT * FROM off_reqs WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
if (!$row)
	die("��� ������ �������");


$row_name=$row["name"];

$row_descr=$row["descr"];

if (get_user_class() == UC_SYSOP){
$torrent_com = $_POST["torrent_com"];
}
else
$torrent_com = $row["torrent_com"];


if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
	bark("�� �� ����������� ����� �������!\n");

	$cat=$row["category"];
    $cat_user=$CURUSER["catedit"];
    
      if (($cat_user!="" && !stristr("$cat_user", "[cat$cat]")) && ($CURUSER["id"] != $row["owner"]) && get_user_class() == UC_MODERATOR)
{  stderr($tracker_lang['error'],"�� �� ������ ������������� ���� ������, �� ���� ��������� ��������������.");  }
    

    
if ($_POST["name"]=="")
{
	bark("��� ������� �� ����� ���� ������.");
}

$updateset = array();
$name=$_POST["name"];
$name = preg_replace("/\&/", "and", $name);
$name = htmlspecialchars($name);

$formatup = $_POST["formatdown"]; // mb gb

if (!empty($_POST["numfiles"]))
{
$add2=(int)$_POST["numfiles"];
$updateset[]="numfiles = " . sqlesc($add2)."";
}

if (!empty($_POST["size"]))
{
$add=(int)$_POST["size"];
$updateset[]="size = " . sqlesc($formatup == mb ? ($add * 1048576) : ($add * 1073741824))."";
}


$descr = unesc($_POST["descr"]);
if (!$descr)
	bark("�� ������ ������ ��������!");

if ($_POST["user_no_perf"])
{
$updateset[] = "perform = 'no'";
$updateset[] = "fulfilled = ''";
$updateset[] = "ful_id = ''";	
$torrent_com = get_date_time() . " $user ������� ����������.\n". $torrent_com;
}
$updateset[] = "name = " . sqlesc($name);
$updateset[] = "descr = " . sqlesc($descr);
$updateset[] = "category = " . ((int) $type);

//��������� ���� ���� ���������
if ($_POST["up_date"] == "yes"){
 $updateset[] = "added = NOW()";
}


if(get_user_class() >= UC_ADMINISTRATOR){
//$updateset[] = "free = '".($_POST["free"]==1 ? 'yes' : 'no')."'";

	
if ($_POST["delete_comment"]=='yes'){

sql_query("DELETE FROM comments WHERE offer= ".sqlesc($id));
//write_log("$user ������ ��� ����������� � �������� $name ($id)\n", "$user_color","comment");
$torrent_com = get_date_time() . " $user ������ ��� ����������� �������.\n". $torrent_com;
}


}

//������� ����� ���������
if (get_user_class() >= UC_ADMINISTRATOR && $_POST["user_reliases_anonim"]){
$updateset[] = "owner = '".($_POST["user_reliases_anonim"]=='1' ? '0' : '')."'";
//write_log("������� $name ($id) ���� ��������� - $user\n", "$user_color","torrent");

$torrent_com = get_date_time() . " $user �������� ������� ������������.\n". $torrent_com;
}
  
//������� ����� ������� ������ 
if (get_user_class() >= UC_ADMINISTRATOR && $_POST["release_set_id"]){
    $setid = (int) $_POST["release_set_id"];  
	         
    $updateset[] = "owner = '$setid'";
   
    $res = sql_query("SELECT username FROM users WHERE id = $setid") or sqlerr(__FILE__, __LINE__); 
    $row = mysql_fetch_array($res); 
    if (!$row[username]=="")
    $owner_t="$row[username]";
    else
    $owner_t="[$setid]";

   if ($setid){
   $torrent_com = get_date_time() . " $user �������� ���������� - $owner_t � �������.\n". $torrent_com;
    }
}



if (get_user_class() >= UC_MODERATOR && $_POST["torrent_com_zam"])
{
$torrent_com1 = htmlspecialchars($_POST["torrent_com_zam"]);
$torrent_com = get_date_time() . " ������� �� $CURUSER[username]: $torrent_com1\n". $torrent_com;
}


if ($row_descr<>$_POST["descr"] || $row_name<>$_POST["name"])
{
$torrent_com = get_date_time() . " ��� �������������� $user.\n". $torrent_com;
}

$updateset[] = "torrent_com = " . sqlesc($torrent_com);

sql_query("UPDATE off_reqs SET " . implode(",", $updateset) . " WHERE id = $id");

/*$name1 = "". format_comment($name) . "";
$name=strlen($name)>60?(substr($name,0,45)."..."):$name1; 


write_log("������� $name ($id) ��� �������������� $user\n", "$user_color","torrent");
*/
$returl = "detailsoff.php?id=$id";
if (isset($_POST["returnto"]))
	$returl .= "&returnto=" . htmlentities($_POST["returnto"]);

header("Refresh: 1; url=$returl");
?>