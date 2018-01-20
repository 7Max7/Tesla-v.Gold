<?
if (!defined('BLOCK_FILE')) {
Header("Location: ../index.php");
exit;
}
global $CURUSER;

$cat_user=$CURUSER["catedit"];

$cat_user=str_replace("cat", "",$cat_user);
$cat_user=str_replace("][", ",", $cat_user);
$cat_user=str_replace("[", "", $cat_user);
$cat_user=str_replace("]", "", $cat_user);

if ($CURUSER["catedit"] && $CURUSER["class"]=="4"){
$hide = array($cat_user); // those IDs will not be shown online
$where = "and category IN (".join(",",$hide).") "; 
$view_warn="[с вашими выбранными категориями: $cat_user]";
}


$blocktitle = "Файлы без одобрения $view_warn";

// alter table torrents add index mod_no (moderated);
///0.040761 - было
// 0.000726  стало
$res = sql_query("SELECT torrents. * FROM torrents LEFT JOIN users ON torrents.owner = users.id  WHERE banned = 'no' and  moderated = 'no' $where ORDER BY id DESC LIMIT 0 , 16") or sqlerr(__FILE__, __LINE__) 
or 
sql_query("SELECT torrents. * FROM torrents LEFT JOIN users ON torrents.owner = users.id  WHERE banned = 'no' and  moderated = 'no' ORDER BY  id  DESC LIMIT 0 , 16") or sqlerr(__FILE__, __LINE__);;
$num = mysql_num_rows($res);
if ($num > 0) {
    $content .= "<table border=\"1\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"100%\" id=\"table\"> ";
    $nc=1;
    
     
 
    for ($i = 0; $i < $num; ++$i) {
    while ($row = mysql_fetch_assoc($res)) {
        if ($nc == 1) { $content .= "<tr>"; }
        $content .= "<td>";
        

         if (empty($row["image1"]))
          $simg = "default_torrent.png";
         elseif (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $row["image1"]))
         $simg = $row["image1"];
         else
          $simg = "thumbnail.php?image=".$row["image1"]."&for=block";
         
    $i++;  


    $content .= "<td align=\"center\" valign=\"top\" width=\"25%\">"; 
    $content .= "<a href=details.php?id=$row[id]><img class=\"glossy\" title=\"".$row['name']."\"  src=\"$simg\" \" height=\"140\" width=\"140\"  border=\"0\" /></a><br>"; 
 

$content .= '</center><b><div align="center"><font color=red>Раздают: '. $row['seeders'] .'  </font> <font color=green>Качают: '. $row['leechers'] .'</b></div></font>';


$content .= "<center>
<b>Скачан: $row[times_completed]</b>";

  if ($row["free"]=="yes")
    {
$content .= ".<img style=\"border:none\" alt=\"Золото\" title=\"Золото\" src=\"pic/freedownload.gif\">.";
}
else $content .= ".:::.";

$content .= "<b>Взят: $row[hits]</b><br>
<font color=blue><b>Просмотров: $row[views]</b></font>

</center>";
       
   if ($row["moderated"] == "yes"){
$content .= "<b>Одобрил:</b> <b><a href=\"userdetails.php?id=$row[moderatedby]\">". get_user_class_color($row["classname"], htmlspecialchars_uni($row["classusername"])) . "	</a></b><br>\n";}


     
       
$content .= "</td>";
        ++$nc;
        if ($nc == 5) { $nc=1; $content .= "</tr>"; }
    }
}
$content .= "</tr></table>";
}

else
	$content .= "<center>Нет файлов без одобрения...</center>\n";





?> 