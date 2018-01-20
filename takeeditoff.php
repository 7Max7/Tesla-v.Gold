<?
require_once("include/bittorrent.php");

dbconn();
loggedinorreturn();


function bark($msg) {
	stderr("Ошибка", $msg);
}
function bark_2($yes,$msg) {
	stderr($yes, $msg);
}


if ($_POST['ful_id'] && $_POST['idi']){
//	print" ".$_POST['idi']."";
	//die();
$idi = (int) $_POST['idi']; //// какой запрос помечаем
$work = (int) $_POST['ful_id']; /// какой запрос выполнен торрентом
$kem=$CURUSER["id"]; /// кем выполнен
if ($_POST['perform']=="yes"){	$sql_yes="yes";}
if ($idi && $work)
{
$res = sql_query("SELECT perform FROM off_reqs WHERE id = ".sqlesc($idi));
$row = mysql_fetch_array($res);
if (!$row) {
	die("Такой запрос не существует!");
	}

//if ($row["perform"])
//{
//bark("Ошибка! Данный запрос уже выполнен.\n");
//}


$res = sql_query("SELECT name FROM torrents WHERE id = ".sqlesc($work));
$row = mysql_fetch_array($res);
if (!$row)
stderr($tracker_lang['error'],"Такой торрент не существует!");

$time=get_date_time();
sql_query("UPDATE off_reqs SET ful_id=".sqlesc($work).", perform='yes', fulfilled=".sqlesc($kem).",data_perf=".sqlesc($time)." WHERE id = ".sqlesc($idi));

 @header ("Refresh: 3; url=detailsoff.php?id=".$idi."");
stderr("Готово! и 3 секунды ожидания", "Данный запрос был помечен как выполнен <a href=\"detailsoff.php?id=".$idi."\">назад автоматически</a>");

//bark("Готово! Данный запрос был помечен как выполнен.\n");
}
@unlink(ROOT_PATH."cache/block_menu_off.txt");
}


//////////////////////// редактирование 

if (!mkglobal("id:name:descr:type"))
    die("missing form data");
    

$id = (int) $id;
if (empty($id))
die("неверный id запроса");
	
$user=$CURUSER["username"];

if ($_POST["reacon"]) /// подтверждение удаления
{
$res = sql_query("SELECT name,owner FROM off_reqs WHERE id = ".sqlesc($id));
$row = mysql_fetch_array($res);
if (!$row)
	stderr($tracker_lang['error'],"Такого запроса не существует!");

if ($CURUSER["id"] <> $row["owner"] && get_user_class() < UC_MODERATOR)
	bark("Вы не владелец! Как такое могло произойти?\n");

$rt = (int) $_POST["reasontype"];
$dup = format_comment ($_POST["dup"]);
$rule = format_comment ($_POST["rule"]);
if (!is_int($rt) || $rt < 1 || $rt > 3)
	bark("Не указана причина или неверные данные для удаления.");
if ($rt == 1)
	$reasonstr = "Мертвяк, выполнен";
elseif ($rt == 2)
{
 if (!$dup) {bark("Вы не вписали ссылку (данные) в поле дупликат.");}
	$reasonstr = "Дупликат" . ($dup ? (" по: ".$dup) : "!");
}
elseif ($rt == 3)
{
  if (!$rule) {bark("Вы не написали пукт правил, которые этот запрос нарушил.");}
  $reasonstr = "Правило: ".$rule;
}
if($row["owner"] == $CURUSER["id"]) {$owne = "Личный";}

  sql_query("DELETE FROM checkcomm WHERE checkid = ".sqlesc($id)." AND userid = $CURUSER[id] AND offer = 1") or sqlerr(__FILE__,__LINE__);
  
  sql_query("DELETE FROM off_reqs WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);

  sql_query("DELETE FROM comments WHERE offer = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);

write_log("$owne Запрос $id ($row[name]) был удален $CURUSER[username]: $reasonstr\n","52A77C","torrent");  

bark_2("Успешно","<h2>Запрос ".$row["name"]." успешно удален.</h2><br>
$reasonstr");
}
/// конец удаления торрента




$res = sql_query("SELECT * FROM off_reqs WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
if (!$row)
	die("Нет такого запроса");


$row_name=$row["name"];

$row_descr=$row["descr"];

if (get_user_class() == UC_SYSOP){
$torrent_com = $_POST["torrent_com"];
}
else
$torrent_com = $row["torrent_com"];


if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
	bark("Вы не собственник этого запроса!\n");

	$cat=$row["category"];
    $cat_user=$CURUSER["catedit"];
    
      if (($cat_user!="" && !stristr("$cat_user", "[cat$cat]")) && ($CURUSER["id"] != $row["owner"]) && get_user_class() == UC_MODERATOR)
{  stderr($tracker_lang['error'],"Вы не можете редактировать этот запрос, Не ваша категория редактирования.");  }
    

    
if ($_POST["name"]=="")
{
	bark("Имя запроса не может быть пустым.");
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
	bark("Вы должны ввести описание!");

if ($_POST["user_no_perf"])
{
$updateset[] = "perform = 'no'";
$updateset[] = "fulfilled = ''";
$updateset[] = "ful_id = ''";	
$torrent_com = get_date_time() . " $user обнулил выполнение.\n". $torrent_com;
}
$updateset[] = "name = " . sqlesc($name);
$updateset[] = "descr = " . sqlesc($descr);
$updateset[] = "category = " . ((int) $type);

//Обновляем дату если требуется
if ($_POST["up_date"] == "yes"){
 $updateset[] = "added = NOW()";
}


if(get_user_class() >= UC_ADMINISTRATOR){
//$updateset[] = "free = '".($_POST["free"]==1 ? 'yes' : 'no')."'";

	
if ($_POST["delete_comment"]=='yes'){

sql_query("DELETE FROM comments WHERE offer= ".sqlesc($id));
//write_log("$user удалил все комментарии в торренте $name ($id)\n", "$user_color","comment");
$torrent_com = get_date_time() . " $user удалил все комментарии запроса.\n". $torrent_com;
}


}

//Сделать релиз анонимным
if (get_user_class() >= UC_ADMINISTRATOR && $_POST["user_reliases_anonim"]){
$updateset[] = "owner = '".($_POST["user_reliases_anonim"]=='1' ? '0' : '')."'";
//write_log("Торрент $name ($id) стал Анонимным - $user\n", "$user_color","torrent");

$torrent_com = get_date_time() . " $user присвоил запросу аноннимность.\n". $torrent_com;
}
  
//Сделать новым автором релиза 
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
   $torrent_com = get_date_time() . " $user присвоил обладателя - $owner_t к запросу.\n". $torrent_com;
    }
}



if (get_user_class() >= UC_MODERATOR && $_POST["torrent_com_zam"])
{
$torrent_com1 = htmlspecialchars($_POST["torrent_com_zam"]);
$torrent_com = get_date_time() . " Заметка от $CURUSER[username]: $torrent_com1\n". $torrent_com;
}


if ($row_descr<>$_POST["descr"] || $row_name<>$_POST["name"])
{
$torrent_com = get_date_time() . " был отредактирован $user.\n". $torrent_com;
}

$updateset[] = "torrent_com = " . sqlesc($torrent_com);

sql_query("UPDATE off_reqs SET " . implode(",", $updateset) . " WHERE id = $id");

/*$name1 = "". format_comment($name) . "";
$name=strlen($name)>60?(substr($name,0,45)."..."):$name1; 


write_log("Торрент $name ($id) был отредактирован $user\n", "$user_color","torrent");
*/
$returl = "detailsoff.php?id=$id";
if (isset($_POST["returnto"]))
	$returl .= "&returnto=" . htmlentities($_POST["returnto"]);

header("Refresh: 1; url=$returl");
?>