<?
require_once 'include/bittorrent.php';

/**
 * @author 7Max7
 * @copyright 2010
 */


dbconn(false);
///loggedinorreturn();

header("Content-Type: text/html; charset=".$tracker_lang['language_charset']);
global $CURUSER,$DEFAULTBASEURL;

if(empty($CURUSER))
die("Авторизуйтесь");
	

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

	    function bark($msg, $error = true) {
        global $tracker_lang;
        echo $msg;
        exit;
    }

    $id = (int)$_POST['id'];
    $type = ($_POST['type']=="add" ? "add":"del");
	$page  = (string)$_POST['page'];
   	$twopage  = (string)$_POST['twopage'];
   	
  if (!is_valid_id($id))
  die("Неверный идентификатор ".$id);

 // if ($CURUSER["id"]<>$id)
//  die("Не себе меняете данные");


//var_dump($_POST);
    if (!isset($type) && !isset($page))
    die($tracker_lang['torrent_not_selected']);
		
	//Если пользователь не авторизирован	

     if($type == 'add') {

     $arr = @mysql_fetch_array(sql_query("SELECT COUNT(*) AS numi FROM torrents WHERE id = ".sqlesc($id)));
   
   if (empty($arr["numi"]))
   die("Нет торрента $id");
   
  
      /// для закладок
  if (!empty($twopage)){
  	
  $arrc = @mysql_fetch_array(sql_query("SELECT COUNT(*) AS numch FROM checkcomm WHERE checkid = " .sqlesc($id) . " AND userid = " .sqlesc($CURUSER["id"]) . " AND torrent = 1"));
  	
  	if (empty($arrc["numch"])){
  	sql_query("INSERT INTO checkcomm (checkid, userid, torrent) VALUES (" .sqlesc($id) . ", " .sqlesc($CURUSER["id"]) . ", 1)") or sqlerr(__FILE__,__LINE__);
 	}

  	if($twopage == "details")
	echo "<a style=\"cursor: pointer;\" class=\"altlink_white\" href=\"javascript:checmark('$id', 'del', 'check','details');\"><b>Отключить слежение</b></a>";
else
	echo "<span style=\"cursor: pointer;\" id=\"checmark_".$row['id']."\"><a onclick=\"checmark('$id', 'del' , 'check','browse');\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" border=\"0\" src=\"".$DEFAULTBASEURL."/pic/head2_2.gif\" alt=\"Отключить слежение\" title=\"Отключить слежение\" /></a></span> ";
	die;
 	
  }
 else {

  $arr2 = @mysql_fetch_array(sql_query("SELECT COUNT(*) AS num FROM bookmarks WHERE userid = ".sqlesc($CURUSER['id'])." AND torrentid = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__));
  

    if (!empty($arr2["num"]))
    die("Уже есть в закладках");

    sql_query("INSERT INTO bookmarks (userid, torrentid) VALUES (".sqlesc($CURUSER['id']).", ".sqlesc($id).")");

	    //Выводим(зависит от типа)
        if($page == "details")
			echo "<a style=\"cursor: pointer;\" class=\"altlink_white\" href=\"javascript:bookmark('$id', 'del', 'details');\"><b>Убрать из закладок</b></a>";
			
		else
	echo "<img style=cursor: pointer; border=\"0\" src=\"".$DEFAULTBASEURL."/pic/delete.gif\" title=\"Убрать из закладок\" onclick=\"bookmark('$id', 'del' , 'browse');\" />";	
        die;
 }
	 
 }
     

     if($type == 'del') {
 
     $arr = @mysql_fetch_array(sql_query("SELECT COUNT(*) AS numi FROM torrents WHERE id = ".sqlesc($id)));
   
   if (empty($arr["numi"]))
   die("Нет торрента $id");
  
  

  if (!empty($twopage)){
  	
	$arrc = @mysql_fetch_array(sql_query("SELECT COUNT(*) AS numch FROM checkcomm WHERE checkid = " .sqlesc($id) . " AND userid = " .sqlesc($CURUSER["id"]) . " AND torrent = 1"));

  if (!empty($arrc["numch"])){
	sql_query("DELETE FROM checkcomm WHERE checkid = " .sqlesc($id) . " AND userid = " .sqlesc($CURUSER["id"]) . " AND torrent = 1") or sqlerr(__FILE__,__LINE__);
 	}
 	
 	      if($twopage == "details")
		 echo "<a style=\"cursor: pointer;\" class=\"altlink_white\" href=\"javascript:checmark('$id', 'add', 'check','details');\"><b>Включить слежение</b></a>";
		else
		echo "<span style=\"cursor: pointer;\" id=\"checmark_".$row['id']."\"><a onclick=\"checmark('$id', 'add', 'check','browse');\"><img border=\"0\" src=\"".$DEFAULTBASEURL."/pic/head2_2.gif\" alt=\"Включить слежение\" title=\"Включить слежение\" /></a></span>";
 	
 
  		die;
  		
  	  } else {
  
  
  
  $arr2 = @mysql_fetch_array(sql_query("SELECT COUNT(*) AS num FROM bookmarks WHERE userid = ".sqlesc($CURUSER['id'])." AND torrentid = ".sqlesc($id)));
  
    if (empty($arr2["num"]))
    die("Нет в закладках");
    
    sql_query("DELETE FROM bookmarks WHERE torrentid=".sqlesc($id)." AND userid=".sqlesc($CURUSER['id'])) or sqlerr(__FILE__,__LINE__);


		//Выводим(зависит от типа)
        if($page == "details")
			echo "<a style=\"cursor: pointer;\" class=\"altlink_white\" href=\"javascript:bookmark('$id', 'add' , 'details');\"><b>Добавить в закладки</b></a><span id=\"loading\"></span>";

		else
			echo "<img style=cursor: pointer; src=\"".$DEFAULTBASEURL."/pic/add.gif\"  border=\"0\" title=\"Добавить в закладки\" onclick=\"bookmark('$id', 'add' , 'browse');\"/>";		
        die();

}

}
}
?>