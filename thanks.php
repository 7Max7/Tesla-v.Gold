<?

require_once("include/bittorrent.php");
dbconn();

loggedinorreturn();
parked();

$userid = $CURUSER["id"];
$torrentid = (int) $_POST["torrentid"];
$touid = (int) $_POST["touid"];

if (empty($torrentid)) {
	stdmsg($tracker_lang["error"], "Пусто.");
}

$ajax = $_POST["ajax"];

if ($ajax == "yes" && !empty($touid)) {

	$sql = sql_query("SELECT id FROM thanks WHERE torrentid = ".sqlesc($torrentid)." and userid=".sqlesc($userid)." and touid=".sqlesc($touid)." limit 1") or sqlerr(__FILE__, __LINE__);
$est_id = mysql_fetch_array($sql);

	if (empty($est_id))	{

	sql_query("INSERT INTO thanks (torrentid, userid, touid) VALUES (".sqlesc($torrentid).", ".sqlesc($userid).", $touid)");
	sql_query("UPDATE users SET bonus = bonus+'0.20' WHERE id=".sqlesc($touid)) or sqlerr(__FILE__, __LINE__);
	}
 
  $count_sql = sql_query("SELECT COUNT(*) FROM thanks WHERE torrentid = ".sqlesc($torrentid)."");
	$count_row = mysql_fetch_array($count_sql);
	$count = $count_row[0];

/*
	if ($count == 0) {
		$thanksby = $tracker_lang['none_yet'];
	} else {
		
		$thanked_sql = sql_query("SELECT thanks.userid, users.username, users.class FROM thanks INNER JOIN users ON thanks.userid = users.id WHERE thanks.torrentid = ".sqlesc($torrentid));
	
		while ($thanked_row = mysql_fetch_assoc($thanked_sql)) {
			if (($thanked_row["userid"] == $CURUSER["id"]) || ($thanked_row["userid"] == $row["owner"]))
			$can_not_thanks = true;
			//list($userid, $username) = $thanked_row;
			$userid = $thanked_row["userid"];
			$username = $thanked_row["username"];
			$class = $thanked_row["class"];
			$thanksby .= "<a href=\"userdetails.php?id=$userid\">".get_user_class_color($class, $username)."</a>, ";
		}
		if ($thanksby)
			$thanksby = substr($thanksby, 0, -2);
	}
	
	$thanksby = "<div id=\"ajax\"><form action=\"thanks.php\" method=\"post\">
	<input type=\"submit\" name=\"submit\" onclick=\"send(); return false;\" value=\"".$tracker_lang['thanks']."\"".($can_not_thanks ? " disabled" : "").">
	<input type=\"hidden\" name=\"torrentid\" value=\"$torrentid\">".$thanksby."
	</form></div>";
*/
	
	$thanksby = "<div id=\"ajax\"><form action=\"thanks.php\" method=\"post\">
	<input type=\"submit\" name=\"submit\" class=\"btn\" onclick=\"send(); return false;\" value=\"".$tracker_lang['thanks']."\" disabled></form></div>";



	header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
	print $thanksby;
}


?>
