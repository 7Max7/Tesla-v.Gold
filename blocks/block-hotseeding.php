<? if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}


$cacheStatFile = "cache/block-hotseeding.txt"; 
$expire = 1*60; // 1 ������
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) { 
   $content.=file_get_contents($cacheStatFile); 
} else 
{

$res = sql_query("SELECT t.seeders, t.name, t.seeders,t.leechers,t.image1,t.id,t.free,t.size,t.moderatedby, t.moderated, m.class AS classname, m.username AS classusername, u.username, u.class 
FROM torrents AS t	
LEFT JOIN users AS u ON t.owner = u.id LEFT JOIN users AS m ON t.moderatedby = m.id WHERE leechers > 3 and ((leechers > 0 AND seeders = 0) OR (leechers / seeders >= 4)) ORDER BY leechers LIMIT 12") or sqlerr(__FILE__, __LINE__); /// 
$num = mysql_num_rows($res); 

if ($num > 0) {

 $i=0; 
 $content .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\"><tr>"; 

 while ($row = mysql_fetch_assoc($res))
   {
     if(empty($row["image1"]))
     $row["image1"]="default_torrent.png"; 
         
        if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $row["image1"]))
        $row["image1"] = $row["image1"];
        else
        $row["image1"] = "thumbnail.php?image=".$row["image1"]."&for=block";
 
    ++$i;  

    $content .= "<td align=\"center\" valign=\"top\" width=\"25%\">"; 
    $content .= "<a href=details.php?id=".$row["id"]."><img class=\"glossy\"
 title=\"".$row['name']."\" src=\"".$row["image1"]."\" height=\"140\" width=\"140\"  border=\"0\" /><br>".format_comment($row['name'])."</a>"; 
 
  if ($row["free"]=="yes")
    {
$content .= "<img style=\"border:none\" alt=\"������� ������\" title=\"������� ������\" src=\"pic/freedownload.gif\">";
}

 $content .= "<br><b>�����: ".($row['username'] ? "<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row['class'],$row['username'])."</a>":"��� ������")."</b><br>";
 
 
 if ($row["moderated"] == "yes"){
$content .= "<b>�������: 
".($row["classname"] ? "<a href=\"userdetails.php?id=$row[moderatedby]\">". get_user_class_color($row["classname"], htmlspecialchars_uni($row["classusername"])) . "</a>":"��� ����������")."
</b><br>\n";}

 
 $content .= "<b>������: </b>".mksize($row["size"])."<br>
 <b><font color=\"red\">���</font>/<font color=\"green\">���</font>: <font color=\"red\">".$row['seeders']."</b></font>
 /
 <font color=\"green\"><b>".$row['leechers']."</font></b>"; 
 
       
    $content .= "</td>"; 
    if($i==4) $content .= "</tr><tr>"; 
     if($i==8) $content .= "</tr><tr>"; 
   }
 
$content .= "</tr></table>"; 
}

else
	$content .= "<center>�� ������ ������ ��� hot ���������.</center>\n";

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


?> 