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
        stdmsg("������", "�� �������� ��������");
	  stdfootchat();
     	die;}

// action: add -------------------------------------------------------------
if ($action == 'add') {
        if ($CURUSER["warned"] == 'yes') {
                stderr($tracker_lang['error'], "� ��� �������������� � �� �� ������ ������� ����� ��������.");
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
                stderr ($tracker_lang['error'],"������ ������� �� ����������� ����� ������������. ��������� ������.");
        }
        }
        //	$id_torrent="";
        }
      if (!stristr($type,'torrent'))
        stderr ($tracker_lang['error'],"���������� ��������. ��������� ������.");
        


        if (!is_valid_id($targetid)) {
                stderr($tracker_lang['error'], "������������ ID $targetid.");
        }
        if (get_row_count("users", "WHERE id = ".sqlesc($targetid)) == 0)
        		stderr($tracker_lang['error'],"������ ������������ �� ����������!");
        if ($CURUSER["id"] == $targetid) {
                stderr($tracker_lang['error'],"�� �� ������ ������ ������� ��� ����������� ����.");
        }

        $r = sql_query("SELECT id FROM simpaty WHERE touserid=".sqlesc($targetid)." AND type =". sqlesc($type) . " AND fromuserid = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
        if (mysql_num_rows($r) == 1) {
                stderr ($tracker_lang['error'],"�� ��� ������ ������� �� ��� �������� ����� ������������.");
        }

        if (isset($_POST["description"]) && trim($_POST["description"]) == '') {
                stderr($tracker_lang['error'], "����������� �� ����� ���� ������.");
        }
        
        /*
          <form action=\"" . $_SERVER["PHP_SELF"] . "?action=add&amp;" . ($resp_type == 1?'good':'bad') . "&amp;type=$type&amp;targetid=$targetid\" method=\"post\">
        
                */
        
        if (!isset($_POST["description"])) {
        stderr("","<p>�������� �������, �� ������� �� ������� " . ($resp_type == 1?"�������":"�����������") . " ������������:</p>
      
      
        <form action=\"" . $_SERVER["PHP_SELF"] . "?action=add&amp;" . ($resp_type == 1?'good':'bad') . "&amp;type=".htmlspecialchars($type)."&amp;targetid=$targetid\" method=\"post\">
        
        
        <input type=text name=description maxlength=300 size=100></textarea>
		".(isset($_GET["returnto"]) ? "<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($_GET["returnto"]) . "\" />\n" : "").
        "<input type=submit value=".($resp_type == 1?"�������":"�����������").">
        </form>");
        }
        
        sql_query ('INSERT INTO simpaty VALUES (0, ' . $targetid . ', ' . $CURUSER['id'] . ', ' . sqlesc($CURUSER['username']) . ', ' . ($resp_type==0?1:0) . ', ' . ($resp_type==1?1:0) . ', ' . sqlesc($type) . ', ' . sqlesc($current_time) . ', ' . sqlesc(htmlspecialchars($_POST["description"])) . ')') or sqlerr(__FILE__, __LINE__);
        if ($resp_type == 1) {
                sql_query("UPDATE users SET simpaty = simpaty + 1 WHERE id = ".sqlesc($targetid)."") or sqlerr(__FILE__, __LINE__);
        } else {
                sql_query("UPDATE users SET simpaty = simpaty - 1 WHERE id = ".sqlesc($targetid)."") or sqlerr(__FILE__, __LINE__);
        }
      
		$msg = sqlesc("������������ [url=$DEFAULTBASEURL/userdetails.php?id=" . $CURUSER['id'] ."]" . $CURUSER['username'] . "[/url] �������� ��� " . ($resp_type == 1?"�������":"�����������") . " �� [url=$DEFAULTBASEURL/details.php?id=" . $type_id ."]�������[/url] � ��������� �� ��������� ����������: \n[quote]" . htmlspecialchars($_POST["description"]) . "[/quote]"); 
		
		sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES (0, ".sqlesc($targetid).", NOW(), $msg, 0, \"����������� �� ��������� ���������\")");
      

		if (isset($_POST["returnto"])) {
			$returl = htmlentities($_POST["returnto"]);
			header("Refresh: 2; url=$returl");
		}
        stdheadchat(($resp_type == 1?"�������":"�����������") . " ��������");
        stdmsg($tracker_lang['success'],"<p>������������ ������� ������� " . ($resp_type == 1?"�������":"�����������") . " �� ���.</p>
		".(isset($_POST["returnto"]) ? "������ �� ������ �������������� �� ��������, ������ �� ������." : "")
		);
        
        if (isset($_POST["returnto"])) {
        	print("<p><a href=\"".htmlentities($_POST["returnto"])."\">������� ����, ���� �� �� ���� ��������������</a></p>");
        }
        stdfootchat();
}

if ($action == 'delete') {
        if(get_user_class() < UC_SYSOP) {
                stderr($tracker_lang['error'], "� ��� ��� ���� �� �������� ���������.");
        }
        $respect_id = intval($_GET['respect_id']);
        $respect_type = htmlspecialchars($_GET['respect_type']);
        $touserid = intval($_GET['touserid']);
        sql_query ("DELETE FROM simpaty WHERE id = ".sqlesc($respect_id)."") or sqlerr(__LINE__,__FILE__);
        sql_query ("UPDATE users SET simpaty = simpaty " .($respect_type=='bad'?"+1":"-1") . " WHERE id = ".sqlesc($touserid)."") or sqlerr(__LINE__,__FILE__);
        /*if (mysql_affected_rows != 1) {
        	stderr($tracker_lang['error'], "�� ���� ������� ".($respect_type == 'good'?"�������":"�����������").".");
        }*/
        if (isset($_GET["returnto"])) {
        	$returl = htmlspecialchars($_GET["returnto"]);
			header("Refresh: 2; url=$returl");
        };
        stdheadchat();
        stdmsg($tracker_lang['success'], "<p>".($respect_type == 'good'?"�������":"�����������")." ������ �������.</p>".(isset($_GET["returnto"]) ? "������ �� ������ �������������� �� ��������, ������ �� ������." : ""));
        if (isset($_GET["returnto"])) {
        	print("<p><a href=\"".htmlspecialchars($_GET["returnto"])."\">������� ����, ���� �� �� ���� ��������������</a></p>");
        }
        stdfootchat();
        die();
}
?>