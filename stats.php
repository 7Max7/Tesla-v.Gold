<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

if (get_user_class() < UC_MODERATOR)
stderr($tracker_lang['error'], $tracker_lang['access_denied']);

stdhead("Статистика (на модерации)");




?>

<style type="text/css" media=screen>
<!--
a.colheadlink:link,a.colheadlink:visited {
	font-weight: bold;
	color: #ffffff;
	text-decoration: none;
}

a.colheadlink:hover {
	text-decoration: underline;
}
-->
</style>

<?
begin_main_frame();

$res = sql_query("SELECT COUNT(*) FROM torrents") or sqlerr(__FILE__, __LINE__);
$n = mysql_fetch_row($res);
$n_tor = $n[0];

$res = sql_query("SELECT COUNT(*) FROM peers") or sqlerr(__FILE__, __LINE__);
$n = mysql_fetch_row($res);
$n_peers = $n[0];

$uporder = urlencode(isset($_GET['uporder'])?$_GET['uporder']:0);
$catorder = urlencode(isset($_GET["catorder"])?$_GET["catorder"]:0);

if ($uporder == "lastul")
$orderby = "last DESC, name";
elseif ($uporder == "torrents")
$orderby = "n_t DESC, name";
elseif ($uporder == "peers")
$orderby = "n_p DESC, name";
else
$orderby = "name";

$query = "SELECT u.class, u.username AS name, MAX(t.added) AS last, COUNT(DISTINCT t.id) AS n_t, COUNT(p.id) as n_p
	FROM users as u 
	LEFT JOIN torrents as t ON u.id = t.owner 
	LEFT JOIN peers as p ON t.id = p.torrent WHERE u.class = 3
	GROUP BY u.id UNION SELECT u.id, u.username AS name, MAX(t.added) AS last, COUNT(DISTINCT t.id) AS n_t, COUNT(p.id) as n_p
	FROM users as u 
	LEFT JOIN torrents as t ON u.id = t.owner 
	LEFT JOIN peers as p ON t.id = p.torrent WHERE u.class > 3
	GROUP BY u.id ORDER BY $orderby";

$res = sql_query($query) or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($res) == 0)
stdmsg("Извините", "Нет заливающих.");
else
{
	begin_frame("Статистика заливающих", true);
	print("<table width=\"100%\"><tr>\n
	<td class=colhead><a href=\"stats.php?uporder=uploader&amp;catorder=$catorder\" class=colheadlink>Заливающий</a></td>\n
	<td class=colhead><a href=\"stats.php?uporder=lastul&amp;catorder=$catorder\" class=colheadlink>Последняя заливка</a></td>\n
	<td class=colhead align=center><a href=\"stats.php?uporder=torrents&amp;catorder=$catorder\" class=colheadlink>Торрентов</a></td>\n
	<td class=colhead align=center>Завершено</td>\n
	<td class=colhead align=center><a href=\"stats.php?uporder=peers&amp;catorder=$catorder\" class=colheadlink>Пиров</a></td>\n
	<td class=colhead align=center>Завершено</td>\n
	</tr>\n");
	while ($uper = mysql_fetch_array($res))
	{
		print("<tr><td>".get_user_class_color($uper['class'],$uper['name'])."</td>\n");
		print("<td " . (!empty($uper['last'])? (">".normaltime($uper['last'],true)." <br><small>(".get_elapsed_time(sql_timestamp_to_unix_timestamp($uper['last']))." назад)</small>") : "align=center><i>нет данных</i>") . "</td>\n");
		print("<td align=center>" . $uper['n_t'] . "</td>\n");
		print("<td align=center>" . ($n_tor > 0?number_format(100 * $uper['n_t']/$n_tor,1)."%":"<i>нет данных</i>") . "</td>\n");
		print("<td align=center>" . $uper['n_p']."</td>\n");
		print("<td align=center>" . ($n_peers > 0?number_format(100 * $uper['n_p']/$n_peers,1)."%":"<i>нет данных</i>") . "</td></tr>\n");
	}
	print('</table>');
	end_frame();
}

if ($n_tor == 0)
stdmsg("Извините", "Данные по категориям отсутствуют!");
else
{
	if ($catorder == "lastul")
	$orderby = "last DESC, c.name";
	elseif ($catorder == "torrents")
	$orderby = "n_t DESC, c.name";
	elseif ($catorder == "peers")
	$orderby = "n_p DESC,c.name";
	else
	$orderby = "c.name";

		$tree = array();

		$query = mysql_query('SELECT id, name FROM categories ORDER BY sort ASC');
	//	if (empty($query)) return $tree;

		$nodes = array();
		$keys = array();
		while (($node = mysql_fetch_assoc($query)))
		{
			$nodes[$node['id']] =& $node; //заполняем список веток записями из БД
			$keys[] = $node['id']; //заполняем список ключей(ID)
			unset($node);
		}
		mysql_free_result($query);


/*
		foreach ($keys as $key)
		{
				if (isset($nodes[$nodes[$key]]))
				{
					if (!isset($nodes[$nodes[$key]]['nodes']))
					$nodes[$nodes[$key]]['nodes'] = array(); 

					$nodes[$nodes[$key]]['nodes'][] =& $nodes[$key];
			}
		}
*/



	$res = sql_query("SELECT c.id as catid,c.name as catname, MAX(t.added) AS last, COUNT(DISTINCT t.id) AS n_t, COUNT(p.id) AS n_p
	FROM categories as c LEFT JOIN torrents as t ON t.category = c.id LEFT JOIN peers as p
	ON t.id = p.torrent GROUP BY c.id ORDER BY $orderby") or sqlerr(__FILE__, __LINE__);

	begin_frame("Активность категорий", true);
	
	print("<table width=\"100%\" border=\"1\"><tr><td class=colhead><a href=\"stats.php?uporder=$uporder&amp;catorder=category\" class=colheadlink>Категория</a></td>
	<td class=colhead><a href=\"stats.php?uporder=$uporder&amp;catorder=lastul\" class=colheadlink>Последняя заливка</a></td>
	<td class=colhead><a href=\"stats.php?uporder=$uporder&amp;catorder=torrents\" class=colheadlink>Торрентов</a></td>
	<td class=colhead>Завершено</td>
	<td class=colhead><a href=\"stats.php?uporder=$uporder&amp;catorder=peers\" class=colheadlink>Пиров</a></td>
	<td class=colhead>Завершено</td></tr>\n");
	while ($cat = mysql_fetch_array($res))
	{
		print("<tr><td class=rowhead>" . $cat['catname']. "</b></a></td>");
		print("<td " . (!empty($cat['last'])? (">".normaltime($cat['last'],true)." <br>
		<small>(".get_elapsed_time(sql_timestamp_to_unix_timestamp($cat['last']))." назад)</small> ") : "align = center> нет данных ") ."</td>");
		print("<td align=center>" . $cat['n_t'] . "</td>");
		print("<td align=center>" . number_format(100 * $cat['n_t']/$n_tor,1) . "%</td>");
		print("<td align=center>" . $cat['n_p'] . "</td>");
		print("<td align=center>" . ($n_peers > 0?number_format(100 * $cat['n_p']/$n_peers,1)."%":"---") . "</td>\n");
	}
	print ('</table>');
	end_frame();
}

end_main_frame();
stdfoot();

?>