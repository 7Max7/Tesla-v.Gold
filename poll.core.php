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
	$pollId = (isset($_POST["pollId"]) ? (int)$_POST["pollId"] : 0 );
	$userId = (int) $CURUSER["id"];
	
		
	if (!$do == "load" && !$do == "vote" || !$CURUSER){
    die("Авторизуйтесь." );
//	@header("Refresh: 0; url=403.php");
	}
	
	
	if($do == "load"){

		//check to see if user voted :)
		$r_check = sql_query("SELECT p.id,p.added,p.sort,p.question,pa.selection,pa.userid FROM polls AS p 
		LEFT JOIN pollanswers AS pa ON p.id=pa.pollid AND pa.userid=".$userId." AND pa.forum='0'
		WHERE p.forum='0'
		ORDER BY p.id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
		$ar_check = mysql_fetch_assoc($r_check);
		
		if(mysql_num_rows($r_check) == 1) {
			
		//	$r_op = mysql_query("select * from polls WHERE id=".$ar_check["id"]) or sqlerr();
		//	$a_op = mysql_fetch_assoc($r_op);
		
	
	$cache=new MySQLCache("select * from polls WHERE forum='0' AND id=".$ar_check["id"]."", 180);
      $a_op=$cache->fetch_assoc();
		
			
			for($i=0;$i<20;$i++) {
					if(!empty($a_op["option$i"]))
					$options[$i] = format_comment($a_op["option$i"]);
				}

	if($ar_check["userid"] == NULL ){
	
echo ("<div id=\"poll_title\">".format_comment($ar_check["question"])."</div>\n");
	 
foreach($options as $op_id=>$op_val) {
echo("<div align=\"left\"><input type=\"radio\" onclick=\"addvote(".$op_id.")\" name=\"choices\" value=\"".$op_id."\" id=\"opt_".$op_id."\" /><label for=\"opt_".$op_id."\">&nbsp;".$op_val."</label></div>\n");
}
echo("<div  align=\"left\"><input type=\"radio\" onclick=\"addvote(255)\" name=\"choices\" value=\"255\" id=\"opt_255\" /><label for=\"opt_255\">&nbsp;Пустой голос! Хочу увидеть результаты</label></div>\n");
echo("<input type=\"hidden\" value=\"\" name=\"choice\" id=\"choice\"/>");
echo("<input type=\"hidden\" value=\"".$ar_check["id"]."\" name=\"pollId\" id=\"pollId\"/>");
echo("<div align=\"center\"><input type=\"button\" value=\"Голосовать\" class=\"btn\" style=\"display:none; width: 200px\" id=\"vote_b\" onclick=\"vote();\"/></div>");
}
else
{
$r = sql_query("SELECT count(id) as count , selection  FROM pollanswers WHERE pollid=".$ar_check["id"]." AND selection < 20 AND forum='0' GROUP BY selection") or sqlerr(__FILE__, __LINE__);
$total="";
while($a = mysql_fetch_assoc($r)) {
$total+=$a["count"];
$votes[$a["selection"]] = 0+$a["count"];
}


	
				if ($total==0)
				$total=1;
				
				foreach($options as $k=>$op) {
		        $results[] = array(0+(isset($votes[$k])?$votes[$k]:0),$op);
				}
				
				function srt($a,$b) {
				if ($a[0] > $b[0]) return -1;
				if ($a[0] < $b[0]) return 1;
				return 0;
				}

				if ($ar_check["sort"] == "yes")				
				usort($results, "srt");

echo("<div id=\"poll_title\">".format_comment($ar_check["question"])."</div>\n");
						
echo("<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" style=\"border:none\" id=\"results\" class=\"results\">");

$i = 0;

///max($votes) - max значение в голосовании
///in_array($maxid, $result[0])
$maxid = max($votes); /// в чем подвох????
foreach($results as $result) {
echo("<tr>
<td align=\"left\" width=\"40%\" style=\"border:none;\">".$result[1]."</td>
<td style=\"border:none;\" align=\"left\" width=\"60%\" valing=\"middle\">
<div class=\"bar".(($i==0 && $ar_check["sort"] == "yes") ? "max" : "")."\" name=\"".($result[0] / $total * 100)."\" id=\"poll_result\">&nbsp;</div>
</td>
<td style=\"border:none;\">&nbsp;<b>".number_format(($result[0] / $total * 100),2)."%</b></td>
</tr>\n");
++$i;
}
echo("</table>");
				
	//	$comments = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM pollcomments WHERE poll = ".$ar_check["id"]));
       // $comments_color ="$comments[0]";

if ($a_op["comment"]<>"no"){
	$cache2=new MySQLCache("SELECT COUNT(*) FROM comments WHERE torrent=0 and news=0 and offer=0 and poll = ".$ar_check["id"]."", 60);
    $comments=$cache2->fetch_row();
	$comments_color ="$comments[0]";
}

          	$modop= "<b>[</b><a href=\"makepoll.php?action=edit&pollid=$ar_check[id]&returnto=main\">Редактировать</a><b>]</b> - <b>[</b><a  href=\"polls.php?action=delete&pollid=$ar_check[id]&returnto=main\">Удалить</a><b>]</b>";
          	
				
				echo("<div align=\"center\"><b>Голосов</b>: ".$total."  ".($a_op["comment"]<>"no" ?"<b>Комментариев</b>: $comments_color":"")."</div>  <br><div align=\"right\">".(get_user_class() >= UC_MODERATOR ? " ".$modop." - " : "")."
				 ".($a_op["comment"]=="no" ? "<b>[</b><a title=\"Комментирование отключенно\">Комментировать</a><b>]</b>":"<b>[</b><a href=\"polloverview.php?id=".$ar_check["id"]."\">Комментировать</a><b>]</b>")."</div>");
}
		}else 
			echo("Нет опросов");
			
	
	}
	elseif($do == "vote") {
	
		if ($pollId == 0)
			echo(json_encode(array("status" =>0 , "msg"=>"Произошла ошибка. Ваш голос не был принят.")));
		else
		{
			
			$check = mysql_result(mysql_query("SELECT count(id) FROM pollanswers WHERE pollid=".$pollId." AND forum='0' AND userid=".$userId.""));
			
			
/*
$check=$check1["num"];		
$sf =ROOT_PATH."cache/polls.txt"; 
$fpsf=fopen($sf,"a+"); 
fputs($fpsf,"userId-$userId:pollId-$pollId:choice-$choice:do-$do:check-$check\n"); 
fclose($fpsf); 
*/
			
			
			if($check == 0) {
				mysql_query("INSERT INTO pollanswers VALUES(0,$pollId, $userId, $choice,0)") or sqlerr(__FILE__, __LINE__);
				if (mysql_affected_rows() != 1)
				echo(json_encode(array("status" =>0 , "msg"=>"Ошибка при засчитывании голоса, попробуйте еще раз")));
				else 
				echo(json_encode(array("status" =>1)));
			}
			else 
			echo(json_encode(array("status" =>0 , "msg"=>"Двойной голос")));
		}
	}

?>