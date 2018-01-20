<?php 

require_once("include/bittorrent.php"); 
dbconn(); 
loggedinorreturn(); 

if (get_user_class() < UC_MODERATOR)
    stderr($tracker_lang['error'], $tracker_lang['access_denied']); 

stdhead("Пользователи со всеми скоростями отдачи"); 

$cheaters = sql_query("SELECT p.torrent AS tid, t.name AS tname, p.ip, p.port, s.uploaded, s.downloaded, s.to_go, p.seeder, p.agent, p.peer_id, p.userid, u.username, u.class, u.enabled, u.warned, u.donor, (p.uploadoffset / (UNIX_TIMESTAMP(p.last_action) - UNIX_TIMESTAMP(p.prev_action))) AS upspeed, (p.downloadoffset / (UNIX_TIMESTAMP(p.last_action) - UNIX_TIMESTAMP(p.prev_action))) AS downspeed FROM peers AS p INNER JOIN users AS u ON u.id = p.userid INNER JOIN torrents AS t ON t.id = p.torrent INNER JOIN snatched AS s ON (s.userid = p.userid AND s.torrent = p.torrent) WHERE u.enabled = 'yes' ORDER BY upspeed DESC LIMIT 50") or sqlerr(__FILE__,__LINE__); 
if (mysql_num_rows($cheaters) > 0) {
	
	
	if (get_user_class() > UC_MODERATOR)
	{
		$viewport="<td class=\"colhead\">Порт</td>";
	}
	
    print("<table cellpadding=\"3\" cellspacing=\"0\" width=\"100%\">"); 
    print("<tr>
	<td class=\"colhead\">Юзер</td>
	<td class=\"colhead\">Торрент</td>
	<!--<td class=\"colhead\">IP&nbsp;/&nbsp;Порт</td>-->
	<td class=\"colhead\">Раздал</td>
	<td class=\"colhead\">Скачал</td>
	<td class=\"colhead\">Осталось</td>
	<td class=\"colhead\">Раздача</td>
	<td class=\"colhead\">Закачка</td><td class=\"colhead\">Сид</td>
$viewport
	<!--<td class=\"colhead\">Агент</td>-->
	<td class=\"colhead\">Peer_id</td>
	</tr>"); 
    while ($cheater = mysql_fetch_array($cheaters)) {
        list($tid, $tname, $ip, $port, $uploaded, $downloaded, $left, $seeder, $agent, $peer_id, $userid, $username, $class, $enabled, $warned, $donor, $upspeed, $downspeed) = $cheater; 
        list($uploaded, $downloaded, $left, $upspeed, $downspeed) = array_map("mksize", array($uploaded, $downloaded, $left, $upspeed, $downspeed)); 
    
    
    if ($uploaded_num < $uploaded && !$uploaded=="0")
    $uploaded_view="<b>$uploaded</b>";
    else
    $uploaded_view=$uploaded;
      
     if ($downloaded_num < $downloaded && !$downloaded=="0")
    $downloaded_view="<b>$downloaded</b>";
    else
    $downloaded_view=$downloaded;
    
     if ($left_num < $left && !$left=="0")
    $left_view="<b>$left</b>";
    else
    $left_view=$left;
    
     if ($upspeed_num < $upspeed && !$upspeed=="0")
    $upspeed_view="<b>$upspeed</b>";
    else
    $upspeed_view=$upspeed;
    
	if ($downspeed_num < $downspeed && !$downspeed=="0")
    $downspeed_view="<b>$downspeed</b>";
    else
    $downspeed_view=$downspeed;
    
    
      if (!$port)     {$port="---";}
      
	if (get_user_class() > UC_MODERATOR)
	{
	
		$port_in="<td align=\"center\">$port</td>";
	}
        
        if ($seeder == "yes") 
            $is_seed = "<span style=\"color: green\">Да</span>"; 
        else 
            $is_seed = "<span style=\"color: red\">Нет</span>"; 
        if (strlen($tname) > 50) 
            $tname = substr($tname, 0, 50)."..."; 
        $peer_id = substr($peer_id, 0, 7); 
        
       
        print("<tr>
		<td><nobr><a href=\"userdetails.php?id=$userid\">".get_user_class_color($class, $username)."</a>".get_user_icons(array("enabled" => $enabled, "donor" => $donor, "warned" => $warned))."</nobr></td>
		<td><nobr><a href=\"details.php?id=$tid\">$tname</a></nobr></td>
		<!--<td>$ip:$port</td>-->
		<td><nobr>$uploaded_view</nobr></td>
		<td><nobr>$downloaded_view</nobr></td>
		<td><nobr>$left_view</nobr></td>
		<td><nobr>$upspeed_view/s</nobr></td>
		<td><nobr>$downspeed_view/s</nobr></td>
		<td align=\"center\">$is_seed</td>
	$port_in
		<!--<td><nobr>$agent</nobr></td>-->
		<td>$peer_id</td>
		</tr>"); 
		
		
	   

	    $downloaded_num=$downloaded_num;
	
		$uploaded_num=$uploaded;
		
				$left_num=$left;
					$downspeed_num=$downspeed;
			$upspeed_num=$upspeed;
    }
    print("</table>"); 
}
else
	print("<br><b>Нет раздач или закачек на данный момент.</b>");






stdfoot(); 

?> 