<?
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

$cacheStatFile = "cache/block-helpseed.txt"; 
$expire = 60*10; // 10 minutes 
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{

global $tracker_lang;

$content .= "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\"><tr>
<td class=\"text\">";

$res = sql_query("SELECT t.id, t.name, t.owner,t.sticky, t.moderatedby, (t.seeders+t.f_seeders) AS seeders, (t.f_leechers+t.leechers) AS leechers, o.username, o.class AS userclass, m.class AS classname, m.username AS classusername
FROM torrents AS t
LEFT JOIN users AS o ON o.id=t.owner
LEFT JOIN users AS m ON m.id=t.moderatedby
 WHERE (leechers / seeders >= 5) ORDER BY leechers DESC LIMIT 20") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0) {
	


	while ($arr = mysql_fetch_assoc($res)) {
					
				if ($arr["moderatedby"])
{$moder="Проверен: ".($arr["classusername"] ? "<b><a href=\"userdetails.php?id=".$arr["moderatedby"]."\">". get_user_class_color($arr["classname"], $arr["classusername"]) . "</a></b>":"<i>Аноним</i>")."";}
			
				if ($arr["owner"]&&$arr["userclass"]&&$arr["username"])
{$owner="Залил: <b><a href=\"userdetails.php?id=".$arr["owner"]."\">". get_user_class_color($arr["userclass"], $arr["username"]) . "</a></b>";}
else 
{$owner="Залил: <i>Аноним</i> ";}			
	
	
		
		$torrname = $arr['name'];
		if (strlen($torrname) > 120)
		$torrname = substr($torrname, 0, 120) . "...";
		
		$content .= "".($arr["sticky"] == "yes" ? "<b>Важный</b>: " : "")."
		
		<b><a href=\"details.php?id=".$arr['id']."&hit=1\" alt=\"".$arr['name']."\" title=\"".$arr['name']."\">".format_comment($torrname)."</a></b>
		<b>[</b>Раздают: <b><font color=\"green\">".number_format($arr['seeders'])."</font></b> 
		Качают: <b><font color=\"red\">".number_format($arr['leechers'])."</font></b><b>]</b>
		 <b>[</b>$owner $moder<b>]</b><br />\n";
	}
} else
	$content .= "<b> ".$tracker_lang['no_need_seeding']." </b>\n";
$content .= "</font>
</b>
</td></tr></table>";




          $fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 }
 
if (get_user_class() >= UC_SYSOP)
{
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
}

///[8] =>  0.015191   [SELECT id, name, owner,sticky, moderatedby, seeders, leechers, (SELECT username FROM users WHERE id=torrents.owner) AS username, (SELECT class FROM users WHERE id=torrents.owner) AS userclass, (SELECT class FROM users WHERE id=torrents.moderatedby) AS classname, (SELECT username FROM users WHERE id=torrents.moderatedby) AS classusername FROM torrents WHERE (leechers > 0 AND seeders = 0) OR (leechers / seeders >= 4) ORDER BY leechers DESC LIMIT 20]

///[8] =>  0.014200  [SELECT t.id, t.name, t.owner,t.sticky, t.moderatedby, t.seeders, t.leechers, o.username, o.class AS userclass, m.class AS classname, m.username AS classusername FROM torrents AS t LEFT JOIN users AS o ON o.id=t.owner LEFT JOIN users AS m ON m.id=t.moderatedby WHERE (t.leechers > 0 AND t.seeders = 0) OR (t.leechers / t.seeders >= 4) ORDER BY t.leechers DESC LIMIT 25]
?>