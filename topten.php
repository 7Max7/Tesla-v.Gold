<?php
require "include/bittorrent.php";

gzip();
dbconn(false);

global $CURUSER;

if (!$CURUSER){
die("Авторизуйтесь! Перенаправление на страницу ввода данных.<script>setTimeout('document.location.href=\"index.php\"', 1000);</script>");
}



print"<style type=\"text/css\">
table.tabs { background-color: transporent; border-collapse: collapse; border: 0px none; }
table.tabs td { border: 0px none; font: bold 11px Arial; color: #57533C; white-space: nowrap; }
table.tabs td a, table.tabs td a:visited { font-weight: bold; color: #57533C; text-decoration: none; }
table.tabs td a:hover { color: #000000; }
table.tabs td.active { background-image: url('../../pic/tabs/bg_active.gif'); background-color: #000000; color: #FFFFFF; }
table.tabs td.notactive { background-image: url('../../pic/tabs/bg.gif'); color: #57533C; }
table.tabs td.space { width: 100%; background-image: none; text-align: right; }

table.ustats { border-collapse: separate; border: 1px solid #000000; }
table.ustats td { white-space: nowrap; }
table.ustats td.head { background-color: #000000; border: 1px solid #000000; font-weight: bold; font-size: 7pt; color: #FFFFFF; }
table.ustats td.cell   { border: 0px none; border-bottom: 1px solid #DDDDDD; }
table.ustats td.hhcell { background-color: #EEEEF8; border-top: 1px solid #6FDF1B; border-bottom: 1px solid #6FDF1B; border-left: 0px none; border-right: 0px none; color: #000000; }
table.ustats td.hvcell { background-color: #EEEEF8; border-top: 0px none; border-bottom: 1px solid #DDDDDD; border-left: 1px solid #6FDF1B; border-right: 1px solid #6FDF1B; }
table.ustats td.hccell { background-color: #6FDF1B; border-top: 1px solid #F8C71E; border-bottom: 1px solid #F8C71E; border-left: 1px solid #F8C71E; border-right: 1px solid #F8C71E; color: #000000; }
table.ustats td.hhcell a, table.ustats td.hhcell a:visited, table.ustats td.hccell a, table.ustats td.hccell a:visited { font-weight: bold; color: #000000; text-decoration: none; }
table.ustats td.hhcell a:hover, table.ustats td.hccell a:hover { color: #FF0000; }
table.ustats td.foot { background-color: #F5F4EA; border: 1px solid #F5F4EA; text-align: right; color: #000000; }
table.ustats td.foot a, table.ustats td.foot a:visited { font-weight: normal; color: #000000; text-decoration: underline; }
table.ustats td.foot a:hover { color: #FF0000; }
</style>
";

function createtabs($tabs, $activetab, $title = '', $width = '100%')
{
	global $pic_base_url, $SITENAME ;
	
	$result = '';
	$count  = count($tabs);
	$num    = 0;

	if($count)
	{
		$width  = preg_match('/^(\d{1,4})(%|px)*$/', $width) ? ' width="'.$width.'"' : '';
		$result = "\r\n\t<table class=\"tabs\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"".$width.">\r\n\t\t<tr>\r\n";
		
		foreach($tabs as $tabname => $tabtitle)
		{
			++$num;
			
			$curactivetab = ($tabname == $activetab);
			
			if($num == 1)
				$result .= "\t\t\t".($curactivetab ? "<td><img src=\"".$pic_base_url."/tabs/begin_active.gif\" border=\"0\"></td>" : "<td><img src=\"".$pic_base_url."/tabs/begin.gif\" border=\"0\"></td>")."\r\n";
			elseif($curactivetab)
				$result .= "\t\t\t<td><img src=\"".$pic_base_url."/tabs/div_active_in.gif\" border=\"0\"></td>\r\n";
			elseif(!$prevactive)
				$result .= "\t\t\t<td><img src=\"".$pic_base_url."/tabs/div.gif\" border=\"0\"></td>\r\n";
			
			$result .= "\t\t\t".($curactivetab ? "<td class=\"active\">&nbsp;".strip_tags($tabtitle)."&nbsp;</td>" : "<td class=\"notactive\">&nbsp;".$tabtitle."&nbsp;</td>")."\r\n";
			
			if($num == $count)
				$result .= "\t\t\t".($curactivetab ? "<td><img src=\"".$pic_base_url."/tabs/end_active.gif\" border=\"0\"></td>" : "<td><img src=\"".$pic_base_url."/tabs/end.gif\" border=\"0\"></td>")."\r\n";
			elseif($curactivetab)
				$result .= "\t\t\t<td><img src=\"".$pic_base_url."/tabs/div_active_out.gif\" border=\"0\"></td>\r\n";
			
			$prevactive = $curactivetab;
		}
		
		$result .= "\t\t\t<td class=\"space\">".$title."</td>\r\n\t\t</tr>\r\n\t</table>\r\n";
	}
	
	echo $result;
}

function usertable($res, $hcol = '', $width = '100%')
{
	global $CURUSER, $ss_uri;
	
	$width  = preg_match('/^(\d{1,4})(%|px)*$/', $width) ? ' width="'.$width.'"' : '';
?>
	<table border="0" cellspacing="0" cellpadding="5" class="ustats"<?php echo $width; ?>>
		<tr>
			<td class="head">Место</td>
			<td class="head" align="left">Пользователь</td>
			<td class="head" align="right">Раздал</td>
			<td class="head" align="right">Скорость раздачи</td>
			<td class="head" align="right">Скачал</td>
			<td class="head" align="right">Скорость закачки</td>
			<td class="head" align="right">Рейтинг</td>
			<td class="head" align="right">Бонус</td>
			<!--<td class="head" align="right">Спасибо</td>-->
			<td class="head" align="right">Респектов</td>
			<td class="head" align="right">Комм.</td>
			<td class="head" align="left">Зарегистрирован</td>
		</tr>
<?php
	$num = 0;
	while($a = mysql_fetch_assoc($res))
	{
		if($hcol == 'comments' && !$a['commentsnum'])
			continue;
		
		++$num;
		$highlightrow = ($CURUSER["id"] == $a["userid"]);
		
		foreach(explode(':', 'uploaded:upspeed:downloaded:downspeed:ratio:bonus:thanks:simpaty:comments:none') as $col)
		{
			if($highlightrow && $hcol == $col && $hcol != 'none')
				$styleclass[$col] = 'hccell';
			elseif(!$highlightrow && $hcol == $col && $hcol != 'none')
				$styleclass[$col] = 'hvcell';
			elseif($highlightrow && $hcol != $col)
				$styleclass[$col] = 'hhcell';
			else
				$styleclass[$col] = 'cell';
		}
		
		if($a["downloaded"])
		{
			$ratio = $a["uploaded"] / $a["downloaded"];
			$color = get_ratio_color($ratio);
			$ratio = number_format($ratio, 2);
			
			if($color)
				$ratio = "<font color=$color>$ratio</font>";
		}
		else
				$ratio = "Inf.";
		
		$simpaty = $a['simpaty'] < 0 ? '<FONT color="#FF0000">'.$a['simpaty'].'</FONT>' : $a['simpaty'];
		
		if($a['userid'] == $CURUSER['id'])
		{
			$bonus    = '<A href="mybonus.php" class="online">'.$a["bonus"].'</A>';
			$thanks   = $a['thanks'] ? '<A href="mythanks.php" class="online">'.$a['thanks'].'</A>' : $a['thanks'];
			$simpaty  = '<A href="mysimpaty.php" class="online">'.$simpaty.'</A>';
			$comments = $a['commentsnum'] ? '<A href="userhistory.php?action=viewcomments&id='.$a['userid'].'" class="online">'.$a['commentsnum'].'</A>' : $a['commentsnum'];
		}
		elseif(get_user_class() >= UC_MODERATOR)
		{
			$bonus    = $a["bonus"];
			$thanks   = $a['thanks'] ? '<A href="mythanks.php?id='.$a['userid'].'" class="online">'.$a['thanks'].'</A>' : $a['thanks'];
			$simpaty  = '<A href="mysimpaty.php?id='.$a['userid'].'" class="online">'.$simpaty.'</A>';
			$comments = $a['commentsnum'] ? '<A href="userhistory.php?action=viewcomments&id='.$a['userid'].'" class="online">'.$a['commentsnum'].'</A>' : $a['commentsnum'];
		}
		else
		{
			$bonus    = $a["bonus"];
			$thanks   = $a['thanks'];
			$simpaty  = $a['simpaty'];
			$comments = $a['commentsnum'];
		}
		
		if($CURUSER)
			$username = '<a href="userdetails.php?id='.$a["userid"].'">'.get_user_class_color($a["class"], $a["username"]).'</a>';
		else
			$username = '<b>'.get_user_class_color($a["class"], $a["username"]).'</b>';
			
		echo "\t\t<tr>\r\n";
		echo "\t\t\t<td class=\"".$styleclass['none']."\" align=\"center\">$num</td>\r\n";
		echo "\t\t\t<td class=\"".$styleclass['none']."\" align=\"left\">".$username."</td>\r\n";
		echo "\t\t\t<td class=\"".$styleclass['uploaded']."\" align=\"right\">".mksize($a["uploaded"])."</td>\r\n";
		echo "\t\t\t<td class=\"".$styleclass['upspeed']."\" align=\"right\">".mksize($a["upspeed"])."/s</td>\r\n";
		echo "\t\t\t<td class=\"".$styleclass['downloaded']."\" align=\"right\">".mksize($a["downloaded"])."</td>\r\n";
		echo "\t\t\t<td class=\"".$styleclass['downspeed']."\" align=\"right\">".mksize($a["downspeed"])."/s</td>\r\n";
		echo "\t\t\t<td class=\"".$styleclass['ratio']."\" align=\"right\">".$ratio."</td>\r\n";
		echo "\t\t\t<td class=\"".$styleclass['bonus']."\" align=\"right\">".$bonus."</td>\r\n";
		//echo "\t\t\t<td class=\"".$styleclass['thanks']."\" align=\"right\">".$thanks."</td>\r\n";
		echo "\t\t\t<td class=\"".$styleclass['simpaty']."\" align=\"right\">".$simpaty."</td>\r\n";
		echo "\t\t\t<td class=\"".$styleclass['comments']."\" align=\"right\">".$comments."</td>\r\n";
		echo "\t\t\t<td class=\"".$styleclass['none']."\" align=\"left\">".date("Y-m-d",strtotime($a["added"]))." (".get_elapsed_time(sql_timestamp_to_unix_timestamp($a["added"]))." назад)</td>\r\n";
		echo "\t\t</tr>\r\n";
	}
	
	if(!$num)
		echo '<tr><td colspan="12" align="center">Нет записи.</td></tr>';
	
	echo '<tr><td colspan="12" class="foot"><A href="#top" onclick="blur();"><DIV>&#65085;</DIV></A></td></tr>';
	
	echo '</TABLE><BR />';
}

$pu   = (get_user_class() >= UC_POWER_USER);
$lim  = isset($_GET["lim"])  ? (int)$_GET["lim"] : false;
$type = isset($_GET["type"]) ? $_GET["type"]     : false;

if(!$pu || !($lim == 10 || $lim == 100 || $lim == 250))
	$lim = 10;

stdheadchat("Top 10");

//$mainquery = "SELECT users.id as userid, users.username, users.class, users.added, users.uploaded, users.downloaded, users.bonus, users.thanks, users.simpaty, users.uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(users.added)) AS upspeed, users.downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(users.added)) AS downspeed, COUNT(DISTINCT comments.id) AS commentsnum FROM users LEFT JOIN comments ON comments.user = users.id WHERE enabled = 'yes'";
$mainquery = "SELECT users.id as userid, users.username, users.class, users.added, users.uploaded, users.downloaded, users.bonus, users.simpaty, users.uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(users.added)) AS upspeed, users.downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(users.added)) AS downspeed, COUNT(DISTINCT comments.id) AS commentsnum FROM users LEFT JOIN comments ON comments.user = users.id WHERE enabled = 'yes'";

$tops['bonus']      = array('query' => $mainquery.' AND bonus > 0 GROUP BY users.id ORDER BY bonus DESC, username ASC LIMIT ', 'highlightcol' => 'bonus', 'title_rightpart' => 'бонусов');
//$tops['thanks']     = array('query' => $mainquery.' AND thanks > 0 GROUP BY users.id ORDER BY thanks DESC, username ASC LIMIT ', 'highlightcol' => 'thanks', 'title_rightpart' => 'спасибо');
$tops['simpaty']    = array('query' => $mainquery.' AND simpaty != 0 GROUP BY users.id ORDER BY simpaty DESC, username ASC LIMIT ', 'highlightcol' => 'simpaty', 'title_rightpart' => 'респектов');
$tops['comments']   = array('query' => $mainquery.' GROUP BY users.id ORDER BY commentsnum DESC, username ASC LIMIT ', 'highlightcol' => 'comments', 'title_rightpart' => 'флудеров');
$tops['uploaded']   = array('query' => $mainquery.' AND uploaded > 0 GROUP BY users.id ORDER BY uploaded DESC, username ASC LIMIT ', 'highlightcol' => 'uploaded', 'title_rightpart' => 'раздающих');
$tops['downloaded'] = array('query' => $mainquery.' AND downloaded > 0 GROUP BY users.id ORDER BY downloaded DESC, username ASC LIMIT ', 'highlightcol' => 'downloaded', 'title_rightpart' => 'качающих');
$tops['upspeed']    = array('query' => $mainquery.' AND uploaded > 0 GROUP BY users.id ORDER BY upspeed DESC, username ASC LIMIT ', 'highlightcol' => 'upspeed', 'title_rightpart' => 'быстрейших раздающих <font class=small>(среднее, включая период неактивности)</font>');
$tops['downspeed']  = array('query' => $mainquery.' AND downloaded > 0 GROUP BY users.id ORDER BY downspeed DESC, username ASC LIMIT ', 'highlightcol' => 'downspeed', 'title_rightpart' => 'быстрейших качающих <font class=small>(среднее, включая период неактивности)</font>');
$tops['bestshare']  = array('query' => $mainquery.' AND downloaded > 1073741824 AND uploaded > 0 GROUP BY users.id ORDER BY uploaded / downloaded DESC, uploaded DESC LIMIT ', 'highlightcol' => 'ratio', 'title_rightpart' => 'лучших раздающих <font class=small>(минимум 1 GB скачано)</font>');
$tops['worstshare'] = array('query' => $mainquery.' AND downloaded > 1073741824 AND uploaded / downloaded < 1 GROUP BY users.id ORDER BY uploaded / downloaded ASC, downloaded DESC LIMIT ', 'highlightcol' => 'ratio', 'title_rightpart' => 'худших раздающих <font class=small>(минимум 1 GB скачано)</font>');

echo "			<H1>Top 10</H1>
			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"900\">
				<tbody>
					<tr>
						<td class=\"bottom\">
";

foreach($tops as $toptype => $top)
{
	if($toptype != $type)
		$limit = 10;
	else
		$limit = $lim;
	
	$sqlresult = sql_query($top['query'].$limit) or sqlerr(__FILE__, __LINE__);
	
	$tabs['10'] = '<A href="topten.php#'.$toptype.'">Top 10</A>';
	if($pu)
	{
		$tabs['100'] = '<A href="topten.php?type='.$toptype.'&amp;lim=100#'.$toptype.'">Top 100</A>';
		$tabs['250'] = '<A href="topten.php?type='.$toptype.'&amp;lim=250#'.$toptype.'">Top 250</A>';
	}
	
	echo '<A name="'.$toptype.'"></A>';
	createtabs($tabs, $limit, '<DIV style="padding-left: 150px; text-align: left;">Top '.$limit.' '.$top['title_rightpart'].'</DIV>');
	usertable($sqlresult, $top['highlightcol']);
}

echo "
						</td>
					</tr>
				</tbody>
			</table>
			<p class=\"small\">Данные записи сайта $SITENAME</p>
";
stdfootchat();

?>