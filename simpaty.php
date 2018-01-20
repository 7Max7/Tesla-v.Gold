<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

//$return = $_SERVER['HTTP_REFERER'];
$return = urlencode($_SERVER['HTTP_REFERER']);

$valid_actions = array('add', 'addforum','delete');
$action = ( in_array($_GET['action'], $valid_actions) ? htmlspecialchars($_GET['action']) : '');


if (!$action) {
	    stdheadchat();
        stdmsg("Ошибка", "не выбранно действие");
	  stdfootchat();
     	die;}

// action: add -------------------------------------------------------------
if ($action == 'add') {
        if ($CURUSER["warned"] == 'yes') {
                stderr($tracker_lang['error'], "У вас предупреждение и вы не можете ставить людям респекты.");
        }
        $current_time = get_date_time();
        $targetid = intval($_GET['targetid']);
        $resp_type = (isset($_GET['good'])?1:0);
        $type = htmlentities($_GET['type']);

        if ($type)
        {
        $type_found = str_replace('torrent', '', $type);
        $type_id=(int) $type_found;
        
        if ($type_id) {
        $r2 = sql_query("SELECT id FROM torrents WHERE id=".sqlesc($type_id)." AND owner = ".sqlesc($targetid)) or sqlerr(__FILE__, __LINE__);
        if (mysql_num_rows($r2) == 0) {
                stderr ($tracker_lang['error'],"Данный торрент не принадлежит этому пользователю. Проверьте данные.");
        }
        }
        //	$id_torrent="";
        }
      if (!stristr($type,'torrent'))
        stderr ($tracker_lang['error'],"Неизвесное действие. Проверьте данные.");
        


        if (!is_valid_id($targetid)) {
                stderr($tracker_lang['error'], "Неправильный ID $targetid.");
        }
        if (get_row_count("users", "WHERE id = ".sqlesc($targetid)) == 0)
        		stderr($tracker_lang['error'],"Такого пользователя не существует!");
        if ($CURUSER["id"] == $targetid) {
                stderr($tracker_lang['error'],"Вы не можете давать респект или антиреспект себе.");
        }

        $r = sql_query("SELECT id FROM simpaty WHERE touserid=".sqlesc($targetid)." AND type =". sqlesc($type) . " AND fromuserid = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
        if (mysql_num_rows($r) == 1) {
                stderr ($tracker_lang['error'],"Вы уже давали респект за это действие этому пользователю.");
        }

        if (isset($_POST["description"]) && trim($_POST["description"]) == '') {
                stderr($tracker_lang['error'], "Комментарий не может быть пустым.");
        }
        
        /*
          <form action=\"" . $_SERVER["PHP_SELF"] . "?action=add&amp;" . ($resp_type == 1?'good':'bad') . "&amp;type=$type&amp;targetid=$targetid\" method=\"post\">
        
                */
        
        if (!isset($_POST["description"])) {
        stderr("","<p>Напишите причину, по которой вы выдаете " . ($resp_type == 1?"респект":"антиреспект") . " пользователю:</p>
      
      
        <form action=\"" . $_SERVER["PHP_SELF"] . "?action=add&amp;" . ($resp_type == 1?'good':'bad') . "&amp;type=".htmlspecialchars($type)."&amp;targetid=$targetid\" method=\"post\">
        
        
        <input type=text name=description maxlength=300 size=100></textarea>
		".(isset($_GET["returnto"]) ? "<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($_GET["returnto"]) . "\" />\n" : "").
        "<input type=submit value=".($resp_type == 1?"Респект":"Антиреспект").">
        </form>");
        }
        
        sql_query ('INSERT INTO simpaty VALUES (0, ' . $targetid . ', ' . $CURUSER['id'] . ', ' . sqlesc($CURUSER['username']) . ', ' . ($resp_type==0?1:0) . ', ' . ($resp_type==1?1:0) . ', ' . sqlesc($type) . ', ' . sqlesc($current_time) . ', ' . sqlesc(htmlspecialchars($_POST["description"])) . ')') or sqlerr(__FILE__, __LINE__);
        if ($resp_type == 1) {
                sql_query("UPDATE users SET simpaty = simpaty + 1 WHERE id = ".sqlesc($targetid)."") or sqlerr(__FILE__, __LINE__);
        } else {
                sql_query("UPDATE users SET simpaty = simpaty - 1 WHERE id = ".sqlesc($targetid)."") or sqlerr(__FILE__, __LINE__);
        }
      
		$msg = sqlesc("Пользователь [url=$DEFAULTBASEURL/userdetails.php?id=" . $CURUSER['id'] ."]" . $CURUSER['username'] . "[/url] поставил вам " . ($resp_type == 1?"респект":"антиреспект") . " за [url=$DEFAULTBASEURL/details.php?id=" . $type_id ."]торрент[/url] в репутацию со следующим сообщением: \n[quote]" . htmlspecialchars($_POST["description"]) . "[/quote]"); 
		
		sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES (0, ".sqlesc($targetid).", NOW(), $msg, 0, \"Уведомление об изменении репутации\")");
      

		if (isset($_POST["returnto"])) {
			$returl = htmlentities($_POST["returnto"]);
			header("Refresh: 2; url=$returl");
		}
        stdheadchat(($resp_type == 1?"Респект":"Антиреспект") . " добавлен");
        stdmsg($tracker_lang['success'],"<p>Пользователь успешно получил " . ($resp_type == 1?"респект":"антиреспект") . " от вас.</p>
		".(isset($_POST["returnto"]) ? "Сейчас вы будете переадресованы на страницу, откуда вы пришли." : "")
		);
        
        if (isset($_POST["returnto"])) {
        	print("<p><a href=\"".htmlentities($_POST["returnto"])."\">Нажмите сюда, если вы не были переадресованы</a></p>");
        }
        stdfootchat();
}

if ($action == 'delete') {
        if(get_user_class() < UC_SYSOP) {
                stderr($tracker_lang['error'], "У вас нет прав на удаление респектов.");
        }
        $respect_id = intval($_GET['respect_id']);
        $respect_type = htmlspecialchars($_GET['respect_type']);
        $touserid = intval($_GET['touserid']);
        sql_query ("DELETE FROM simpaty WHERE id = ".sqlesc($respect_id)."") or sqlerr(__LINE__,__FILE__);
        sql_query ("UPDATE users SET simpaty = simpaty " .($respect_type=='bad'?"+1":"-1") . " WHERE id = ".sqlesc($touserid)."") or sqlerr(__LINE__,__FILE__);
        /*if (mysql_affected_rows != 1) {
        	stderr($tracker_lang['error'], "Не могу удалить ".($respect_type == 'good'?"респект":"антиреспект").".");
        }*/
        if (isset($_GET["returnto"])) {
        	$returl = htmlspecialchars($_GET["returnto"]);
			header("Refresh: 2; url=$returl");
        };
        stdheadchat();
        stdmsg($tracker_lang['success'], "<p>".($respect_type == 'good'?"Респект":"Антиреспект")." удален успешно.</p>".(isset($_GET["returnto"]) ? "Сейчас вы будете переадресованы на страницу, откуда вы пришли." : ""));
        if (isset($_GET["returnto"])) {
        	print("<p><a href=\"".htmlspecialchars($_GET["returnto"])."\">Нажмите сюда, если вы не были переадресованы</a></p>");
        }
        stdfootchat();
        die();
}
?>