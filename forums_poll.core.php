<?
require_once("include/bittorrent.php");
dbconn(false,true);	
///loggedinorreturn();

header("Content-Type: text/html; charset=" .$mysql_charset_fix_by_imperator);
//header("Content-Type: text/html; charset=" .$tracker_lang['language_charset']);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

	
	$do = (isset($_POST["action"]) ? $_POST["action"] : "");
	$choice = (isset($_POST["choice"]) ? (int)$_POST["choice"] : 0 );
	$pollId = (isset($_POST["pollId"]) ? (int)$_POST["pollId"] : 0 );////
	$userId = (int) $CURUSER["id"];
	
	$id_topic = (isset($_POST["id"]) ? (int)$_POST["id"] : 0 );
	
	$id_topic=sqlesc($id_topic); /// для запроса
	$id_topid=$id_topic; /// для кеша

	if (!$do == "load" && !$do == "vote" || !$CURUSER){
die("забыли мы тут что??? <script>setTimeout('document.location.href=\"forums.php\"', 3000);</script>" );
//	@header("Refresh: 0; url=403.php");
	}
	
	
	if($do == "load")
	{
	
		//check to see if user voted :)
		$r_check = sql_query("SELECT p.id,p.added,p.question,pa.selection,pa.userid 
		FROM polls AS p 
		LEFT JOIN pollanswers AS pa ON p.id=pa.pollid AND pa.userid=".$userId." AND pa.forum=$id_topic
		WHERE p.forum=$id_topic
		ORDER BY p.id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
		$ar_check = mysql_fetch_assoc($r_check);
		if(mysql_num_rows($r_check) == 1)
		{
	
	$cache=new MySQLCache("select * from polls WHERE forum=$id_topic AND id=".$ar_check["id"]."", 180,"forums_ptop-$id_topid.txt");
    $a_op=$cache->fetch_assoc();
		
		if (empty($a_op["question"]) || empty($a_op["option0"]) || empty($a_op["option1"])){
	sql_query("DELETE FROM polls WHERE forum=$id_topic") or sqlerr(__FILE__, __LINE__);
	sql_query("DELETE FROM pollanswers WHERE forum=$id_topic") or sqlerr(__FILE__, __LINE__);
	sql_query("UPDATE topics SET polls='no' WHERE id=$id_topic") or sqlerr(__FILE__, __LINE__);
		}
		
			
			for($i=0;$i<20;$i++)
				{
					if(!empty($a_op["option$i"]))
					$options[$i] = format_comment($a_op["option$i"]);
				}

	if($ar_check["userid"] == NULL )
			{
				print("<div id=\"poll_title\">".format_comment($ar_check["question"])."</div>\n");
	 
				foreach($options as $op_id=>$op_val)
				{
					print("<div align=\"left\">
					<input type=\"hidden\" value=\"".$id_topid."\" name=\"id\" id=\"id\"/>
					
					<input type=\"radio\" onclick=\"addvote(".$op_id.")\" name=\"choices\" value=\"".$op_id."\" id=\"opt_".$op_id."\" /><label for=\"opt_".$op_id."\">&nbsp;".$op_val."</label></div>\n");
				}
				print("<div align=\"left\"><input type=\"radio\" onclick=\"addvote(255)\" name=\"choices\" value=\"255\" id=\"opt_255\" /><label for=\"opt_255\">&nbsp;Пустой голос! Хочу увидеть результаты</label></div>\n");
				print("<input type=\"hidden\" value=\"\" name=\"choice\" id=\"choice\"/>");
				print("<input type=\"hidden\" value=\"".$ar_check["id"]."\" name=\"pollId\" id=\"pollId\"/>");
				print("<div align=\"center\"><input type=\"button\" value=\"Голосовать\" style=\"display:none;\" id=\"vote_b\" onclick=\"vote();\"/></div>");
			}
			else
			{
				$r = sql_query("SELECT count(id) as count, selection  FROM pollanswers WHERE pollid=".$ar_check["id"]." AND selection < 20 AND forum=$id_topic GROUP BY selection") or sqlerr(__FILE__, __LINE__);
	
				while($a = mysql_fetch_assoc($r))
				{	$total += $a["count"];
		
					$votes[$a["selection"]] = 0+$a["count"];
				}
				
				if ($total==0)
				$total=1;
				
				foreach($options as $k=>$op)
				{	
					$results[] = array(0+$votes[$k],$op);
				}
				
				function srt($a,$b)
				{
				if ($a[0] > $b[0]) return -1;
				if ($a[0] < $b[0]) return 1;
				return 0;
				}
				usort($results, srt);

				print("<div id=\"poll_title\">".format_comment($ar_check["question"])."</div>\n");
						
				print("<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" style=\"border:none\" id=\"results\" class=\"results\">");
				$i= 0;
				foreach($results as $result)
				{
					print("<tr >
					<td align=\"left\" width=\"40%\" style=\"border:none;\">".$result[1]."</td>
					<td style=\"border:none;\" align=\"left\" width=\"60%\" valing=\"middle\">
					<div class=\"bar".($i == 0 ? "max" : "")."\"  name=\"".($result[0] / $total * 100)."\" id=\"poll_result\">&nbsp;</div>
					</td>
					<td style=\"border:none;\">&nbsp;<b>".number_format(($result[0] / $total * 100),2)."%</b></td>
					</tr>\n");
					$i++;
				}
				print("</table>");
			
print("<div align=\"center\"><b>Голосов</b>: ".$total."</div>");
}
}
else {
	
sql_query("DELETE FROM polls WHERE forum=$id_topic") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM pollanswers WHERE forum=$id_topic") or sqlerr(__FILE__, __LINE__);
sql_query("UPDATE topics SET polls='no' WHERE id=$id_topic") or sqlerr(__FILE__, __LINE__);
		
print("Если видите эту ошибку, значить был сбой в запросе, льющий данные в базу данных с $id_topic id значением.");
			
	}
	}
	elseif($do == "vote")
	{
		
		//die("s");
		if ($pollId == 0)
			print(json_encode(array("status" =>0 , "msg"=>"Произошла ошибка. Ваш голос не был принят.")));
		
		else
		{
			$check = mysql_result(mysql_query("SELECT count(id) FROM pollanswers WHERE pollid=".$pollId." AND userid=".$userId." AND forum=$id_topic"));
			
			if($check == 0) {
				@mysql_query("INSERT INTO pollanswers VALUES(0,$pollId, $userId, $choice,$id_topic)");
				if (mysql_affected_rows() != 1){
				print(json_encode(array("status" =>0 , "msg"=>"Ошибка при засчитывании голоса, попробуйте еще раз")));
			//	die("sdfsdfsfsdddddddddddddd");
				}
				else 
				print(json_encode(array("status" =>1)));
			}
			else 
			print(json_encode(array("status" =>0 , "msg"=>"Двойной голос")));
		}
	}

?>