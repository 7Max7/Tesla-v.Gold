<?php
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();


if (get_user_class() < UC_SYSOP) 
{
attacks_log('forummanage'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

@unlink(ROOT_PATH."cache/8b5efe4c9a15d0fcacf372b1bee66935.txt");


$action = isset($_GET['action']) ? $_GET['action'] : ''; //if not goto default!


	switch($action) {
					case 'edit': 
					editForum();
					break;
					
					case 'takeedit':
					takeeditForum();
					break;
					
					case 'delete':
					deleteForum();
					break;
					
					case 'takedelete':
					takedeleteForum();
					break;
					
					case 'add':
					addForum();
					break;
					
					case 'takeadd':
					takeaddForum();
					break;
					
					default:
					showForums();
	
	}



function showForums() {
	stdhead("����������������� ������");
	echo "
	<form method=\"get\" action=\"forummanage.php?action=add\">
	<input type=\"hidden\" name=\"action\" value=\"add\">
	<input type=\"submit\" value=\"�������� ����� ���������\" class=\"btn\" />
	</form><br />";
	begin_main_frame();
	echo '<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">';
	echo "<tr>
	<td class='colhead' align='left'>�������� ���������</td>
	<td class='colhead' align='center'>��� / ������</td>
	<td class='colhead' align='center'>����������� ����� ��:</td>
	<td class='colhead' align='center'>��������</td></tr>";
	$result = sql_query ("SELECT * FROM forums ORDER BY sort ASC");
	if ( mysql_num_rows($result) > 0) {
	
		while($row = mysql_fetch_assoc($result)){
			
	$forums = mysql_query("SELECT t.forumid, count( DISTINCT p.topicid ) AS topics, count( * ) AS posts FROM posts p LEFT JOIN topics t ON t.id = p.topicid LEFT JOIN forums f ON f.id = t.forumid WHERE t.forumid='$row[id]' GROUP BY t.forumid");
	while ($forum = mysql_fetch_assoc($forums))
	{
	$topiccount = number_format($forum['topics']);
    $postcount = number_format($forum['posts']);
	}
	if (empty($postcount))
	$postcount=0;
	if (empty($topiccount))
	$topiccount=0;
	
		echo "<tr>
		<td class='b'><a href='forums.php?action=viewforum&amp;forumid=".$row["id"]."'><b>".htmlspecialchars($row["name"])."</b></a><br /><small>".htmlspecialchars($row["description"])."</small></td>
		<td class='a' align=\"center\">$topiccount / $postcount</td>
    	<td class='b' >
		<b>������</b>: <font color=\"#".get_user_rgbcolor($row["minclassread"], $user_name)."\">" . get_user_class_name($row["minclassread"]) . "</font><br>
		<b>������</b>: <font color=\"#".get_user_rgbcolor($row["minclasswrite"], $user_name)."\">" . get_user_class_name($row["minclasswrite"]) . "</font><br>
	 <b>��������</b>: <font color=\"#".get_user_rgbcolor($row["minclasscreate"], $user_name)."\">" . get_user_class_name($row["minclasscreate"]) . "</font></td>
		<td class='a' align='center' style=\"white-space: nowrap;\"><b><a href=\"?action=edit&amp;id=".$row["id"]."\">�������������</a><hr><a href=\"forummanage.php?action=delete&amp;id=".$row["id"]."\"><font color='red'>�������</font></a></b></td></tr>"; 
		 unset($postcount); 
		 unset($topiccount);
	}
	}
	else {
	print "<tr><td>��������, ��� �������!</td></tr>";
	}       
	echo "</table>";
	
	end_main_frame();
	stdfoot();
}

function addForum() {
	global $CURUSER;

	stdhead("���������� ���������");
	
	echo "
	<form method=\"get\" action=\"forummanage.php\">
	<input type=\"submit\" value=\"�������� �������\" class=\"btn\" />
	</form><br />";
	begin_main_frame();
?>

	<form method='post' action="forummanage.php?action=takeadd">
	<table width="100%"  border="0" cellspacing="0" cellpadding="3" align="center">
		<tr align="center">
			<td colspan="2" class='colhead'>�������� ����� ���������</td>
		</tr>
		<tr>
			<td class=b><b>�������� ���������</b></td>
			<td class=a><input name="name" type="text" size="20" maxlength="60" /></td>
		</tr>
		<tr>
			<td class=b><b>�������� ���������</b></td>
			<td class=a><input name="desc" type="text" size="30" maxlength="200" /></td>
		</tr>
		<tr>
			<td class=b><b>����������� ����� �� ������</b></td>
			<td class=a><select name="readclass">
<?php
	$maxclass = get_user_class();
		for ($i = 0; $i <= $maxclass; ++$i)
		print("<option value='$i'" . ($CURUSER["class"] == $i ? " selected='selected'" : "") . ">" . get_user_class_name($i) . "</option>\n");
?>
			</select>
			</td>
		</tr>
		<tr>
			<td class=b><b>����������� ����� �� ������</b></td>
			<td class=a><select name='writeclass'>
<?php
	$maxclass = get_user_class();
		for ($i = 0; $i <= $maxclass; ++$i)
		print("<option value='$i'" . ($CURUSER["class"] == $i ? " selected='selected'" : "") . ">" . get_user_class_name($i) . "</option>\n");
?>
			</select></td>
		</tr>
		<tr>
			<td class=b><b>����������� ����� �� ��������</b></td>
			<td class=a><select name='createclass'>
<?php
	$maxclass = get_user_class();
		for ($i = 0; $i <= $maxclass; ++$i)
		print("<option value='$i'" . ($CURUSER["class"] == $i ? " selected='selected'" : "") . ">" . get_user_class_name($i) . "</option>\n");
?>
			</select></td>
		</tr>
		<tr>
			<td class=b><b>����������</b></td>
			<td class=a><select name="sort">
<?php
	$res = sql_query ("SELECT sort FROM forums");
	$nr = mysql_num_rows($res);
	$maxclass = $nr + 1;
		for ($i = 0; $i <= $maxclass; ++$i)
		print("<option value='$i'>$i </option>\n");
?>
			</select>
   
			</td>
		</tr>
		
 	<tr>
	<td class=b><b>���������� ��������� � �����</b></td>
		<td class=a><input type=radio name=visible value='yes' checked> �� <input type=radio name=visible value='no'/> ��� </td>
		</tr>
 
		<tr align="center">
			<td class=a colspan="2">
			<input type="submit" name="Submit" value="������� ���������" class="btn" /></td>
		</tr>
		</table>
          </form>
<?php
//	end_frame();
	end_main_frame();
	stdfoot();

}

function editForum() {

	stdhead("�������������� ���������");
	
	$id = isset($_GET["id"]) ? (int)$_GET["id"] : stderr("������", "������ �� �������� �� ����� id");
	
	echo "
	<form action=\"forummanage.php\">
	<input type=\"submit\" value=\"� �������, �������\" class=\"btn\" />
	</form><br />";
	
	///begin_frame("Edit Forum");
	$result = sql_query ("SELECT * FROM forums where id = '$id'");
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_assoc($result)){
?>

		<form method="post" action="forummanage.php?action=takeedit">
		<table width="100%"  border="0" cellspacing="0" cellpadding="3" align="center">
		<tr align="center">
			<td colspan="2" class='colhead'>�����������: <?php echo htmlspecialchars($row["name"]);?></td>
		</tr>
		<tr>
			<td class=b><b>�������� ���������</b></td>
			<td class=a><input name="name" type="text" size="30" maxlength="60" value="<?php echo htmlspecialchars($row["name"]);?>" /></td>
		</tr>
		<tr>
			<td class=b><b>�������� ���������</b></td>
			<td class=a><input name="desc" type="text" size="30" maxlength="200" value="<?php echo htmlspecialchars($row["description"]);?>" /></td>
		</tr>
		<tr>
			<td class=b><b>����������� ����� �� ������</b></td>
			<td class=a><select name='readclass'>
<?php
	$maxclass = get_user_class();
		for ($i = 0; $i <= $maxclass; ++$i)
		if( get_user_class_name($i) != "" )
		print("<option value='$i'" . ($row["minclassread"] == $i ? " selected='selected'" : "") . ">" . get_user_class_name($i) . "</option>");
?>
			</select>
			</td>
		</tr>
		<tr>
			<td class=b><b>����������� ����� �� ������</b></td>
			<td class=a><select name='writeclass'>
<?php
	$maxclass = get_user_class();
		for ($i = 0; $i <= $maxclass; ++$i)
		if( get_user_class_name($i) != "" )
		print("<option value='$i'" . ($row["minclasswrite"] == $i ? " selected='selected'" : "") . ">" . get_user_class_name($i)."</option>");
?>
			</select></td>
		</tr>
		<tr>
			<td class=b><b>����������� ����� �� ��������</b></td>
			<td class=a><select name='createclass'>
<?php
	$maxclass = get_user_class();
		for ($i = 0; $i <= $maxclass; ++$i)
		if( get_user_class_name($i) != "" )
		print("<option value='$i'" . ($row["minclasscreate"] == $i ? " selected='selected'" : "") . ">" . get_user_class_name($i)."</option>");
?>
			</select></td>
		</tr>
		<tr>
			<td class=b><b>����������</b></td>
			<td class=a><select name='sort'>
<?php
	$res = sql_query ("SELECT sort FROM forums");
	$nr = mysql_num_rows($res);
	$maxclass = $nr + 1;
		for ($i = 0; $i <= $maxclass; ++$i)
		print("<option value='$i'" . ($row["sort"] == $i ? " selected='selected'" : "") . ">$i</option>");
?>
			</select>
			</td>
		</tr>
 
 	<tr>
	<td class=b><b>���������� ��������� � �����</b></td>
		<td class=a>	    
	   <input type=radio name=visible value='yes' <?=($row["visible"]=="yes" ? "checked":"");?>> �� <input type=radio name=visible value='no' <?=($row["visible"]=="no" ? "checked":"");?>> ���   </td>
		</tr>
 
		<tr align="center">
			<td class=a colspan="2">
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="submit" name="Submit" value="������������� ���������" class="btn" />
			</td>
		</tr>
		</table>
 </form>
<?php
			}
	} 
	else 
	{print "��������, ��� �������!";}      
	
//	end_frame();
///	end_main_frame();
	stdfoot();
}

function takeaddForum() {
	if (!$_POST['name'] && !$_POST['desc']) { 
	header("Location: forummanage.php"); 
	die();
	}

	mysql_query("INSERT INTO forums (sort, name,  description,  minclassread,  minclasswrite, minclasscreate,visible) VALUES(" . 
	sqlesc((int)($_POST['sort'])) . ", " . 
	sqlesc(htmlspecialchars($_POST['name'])). ", " . 
	sqlesc(htmlspecialchars($_POST['desc'])). ", " . 
	sqlesc((int)($_POST['readclass'])). ", " . 
	sqlesc((int)($_POST['writeclass'])) . ", " . 
	sqlesc((int)$_POST['createclass']) . ", " . 
	sqlesc(($_POST['visible']=="yes" ? "yes":"no")) . "
	)");
	
	if(mysql_affected_rows() === 1)
	{
		header("Refresh: 3; url=forummanage.php");
		stderr("�������", "��������� ����������. <a href='forummanage.php'>�������� �������</a>");
	} else {
	header("Refresh: 5; url=forummanage.php");
	stderr("������", "����������� ������. <a href='forummanage.php'>�������� �������</a>");
	}
die();

}

function takeeditForum() {

	if (!$_POST['name'] && !$_POST['desc'] && !$_POST['id']) {
	header("Location: forummanage.php"); 
	die();
	}

	mysql_query("UPDATE forums SET 
	sort = " . sqlesc((int)$_POST['sort']) . ", 
	name = " . sqlesc(htmlspecialchars($_POST['name'])). ", 
	description = " . sqlesc(htmlspecialchars($_POST['desc'])). ",
	minclassread = " . sqlesc((int)$_POST['readclass']) . ",
	 minclasswrite = " . sqlesc((int)$_POST['writeclass']) . ",
	 minclasscreate = " . sqlesc((int)$_POST['createclass']) . ",
	visible = " . sqlesc(($_POST['visible']=="yes" ? "yes":"no")) . "
	 where id = ".sqlesc(((int)$_POST['id']))."");

	if(mysql_affected_rows() === 1){
	header("Refresh: 3; url=forummanage.php?action=edit&id=".htmlspecialchars((int)$_POST['id'])."");
		stderr("�������", "��������� ���������������. <a href='forummanage.php'>� ������� ������</a>");
	} else {
	header("Refresh: 3; url=forummanage.php?action=edit&id=".htmlspecialchars((int)$_POST['id'])."");
	stderr("������", "�� ���� ��������������� ���������, ������ ����� ������ �� �������� <a href='forummanage.php'>� ������� ������</a>");
	}
die();
}

function deleteForum() {

	$id = isset($_GET['id']) ? (int)$_GET['id'] : stderr("������", "�� �����");
	
		
	$res = sql_query("SELECT id FROM topics WHERE forumid=$id");

    if (mysql_num_rows($res) >= 1) {
		stdhead();
		forum_select($id);
		stdfoot();
		exit();
	}
	else
		stderr("��������������", "�� �������, ��� ������ ������� ��� ��������� ������ ������ � ������ �����������? <a href='forummanage.php?action=takedelete&amp;id=$id'>������, ��� �����</a>");
	
}


function takedeleteForum() {

$id = isset($_GET['id']) ? (int)$_GET['id'] : stderr("������", "�� �����");
	
		if(!isset($_POST['deleteall'])) {
			$res = @mysql_query("SELECT id FROM topics WHERE forumid=$id");
			
			if (mysql_num_rows($res) == 0) 
				mysql_query("DELETE FROM forums WHERE id=$id");
							
				mysql_query("DELETE FROM topics WHERE forumid=$id");
			
			
			(mysql_affected_rows() > 0) ? 
		stderr("�������", "��������� ������ �������� <a href='forummanage.php'>� ������� ������</a>") : stderr("������", "������ ������� ��� ��������� ������!");
		}
		else
		{
			$forumid = (isset($_POST['forumid']) && ctype_digit($_POST['forumid'])) ? (int)$_POST['forumid'] : 	stderr("������", "��� ������ ��� ��������� �������");
			
			$res = mysql_query("SELECT id FROM topics WHERE forumid=$id");
			
			if (mysql_num_rows($res) == 0)
				stderr("������ ���", "��� ��� � ���� �������� ������!");
			while($row = mysql_fetch_assoc($res)) 
				$tid[] = $row['id'];
			
			mysql_query("UPDATE topics SET forumid=$forumid WHERE id IN (".join(',' , $tid).")");
			
			if(mysql_affected_rows() > 0)
			
			mysql_query("DELETE FROM forums WHERE id=$id");
				
			(mysql_affected_rows() > 0) ? stderr("������ �����", "��������� ������ ������� ������� <a href='forummanage.php'>�������� � ������� ������</a>") : stderr("������ �������", "������ ������� ���������!");

		}
}


function forum_select($currentforum = 0)
  {
    print("<p align='center'><form method='post' action='forummanage.php?action=takedelete&amp;id=$currentforum' name='jump'>\n");

    print("<input type='hidden' name='deleteall' value='true' />\n");

    print("����� ����������� ����, �������� �� ������ ��������� � ������� ����� �����������: ");

    print("<br><select name='forumid'>\n");

    $res = sql_query("SELECT * FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);

    while ($arr = mysql_fetch_assoc($res))
    {
      if ($arr["id"] == $currentforum)
		continue;
        print("<option value='" . $arr["id"] . ($currentforum == $arr["id"] ? "' selected='selected'>" : "'>") . $arr["name"] . "</option>\n");
    }

    print("</select>\n");

    print("<input type='submit' value='����������� ����...' class='btn' />\n");

    print("</form>\n</p>");
  }

?>