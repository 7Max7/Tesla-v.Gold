<? 
require "include/bittorrent.php";

dbconn(false,false);
header("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{
	
global $CURUSER;

if (empty($CURUSER))
die;

$id = (string)$_POST["act"];


if ($id=="vtors"){

$limit = 15;

    echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" class=\"main\"><tr>"; 
  // echo "<td align=\"center\" class=\"colhead\">Название</td><td align=\"center\" class=\"colhead\">Время</td><td align=\"center\" class=\"colhead\">Сообщил(а)</td></tr>"; 
   
$dp=0;
	//ON users.id = c.user WHERE torrents.visible='yes'GROUP BY torrent
    $result = sql_query("SELECT c.id,c.torrent,torrents.name,torrents.category,torrents.viponly,torrents.tags,torrents.visible,IF(torrents.numratings < 1, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS ratingsum, dcom,c.user, users.username,users.class FROM (SELECT comments.id,comments.user,comments.torrent,IF(comments.added>comments.editedat,comments.added,comments.editedat) AS dcom FROM comments WHERE comments.torrent>0 ORDER BY dcom DESC) AS c JOIN torrents ON torrents.id = c.torrent JOIN users ON users.id = c.user  GROUP BY torrent ORDER BY dcom DESC LIMIT $limit") or sqlerr(__FILE__, __LINE__);

    while($row = mysql_fetch_assoc($result))
	 {

	
$ratingsum=$row["ratingsum"];

if ($dp%2 == 0){
$clasto = "class = 'b'";
$clastf = "class = 'a'";
} else {
$clasto = "class = 'a'";
$clastf = "class = 'b'";
}

/*
	if ($row["tags"]){
	foreach(explode(",", $row["tags"]) as $tag) {
	
     if ($tags[$row["id"]])
     $tags[$row[id]].=", ";
     
    $tags[$row["id"]].= "<a style=\"font-weight:normal;\" href=\"browse.php?tag=".urlencode($tag)."&incldead=1\">".tolower($tag)."</a>";
}
    $tags_view[$row["id"]]="<br><small><b>Теги</b>: ".$tags[$row["id"]]."</small>";
}
else
$tags_view[$row["id"]]="<br><small><b>Теги</b>: не выбраны</small>";





//".(get_user_class() >= UC_MODERATOR ? "<a href=edit.php?id=$row[torrent]>
	//	<img style=\"border:none\" alt=\"Редактировать\" title=\"Редактировать\" src=\"pic/pen.gif\"></a>":"")."


	 $visible = $row["visible"];
     if ($visible=="yes") 
    {
$dead = "<img style=\"border:none\" alt=\"Видимый (сидируется)\" title=\"Видимый (сидируется)\" src=\"pic/ok.gif\">
"; }
else (
$dead = "<img style=\"border:none\" alt=\"Мертвый (не сидируется)\" title=\"Мертвый (не сидируется)\" src=\"pic/error.gif\">"
);



*/


        // $row["name"] = format_comment($row['name']);
      //  $row["name"]=strlen($row["name"])>70?(substr($row["name"],0,60)."..."):$name7; 
 ///<b>Категория</b>: <a href=\"browse.php?incldead=1&cat=$name_id\">$name_cat</a> 
 ///<br>".pic_rating_b(10,$ratingsum)."
        echo "<tr>";
        echo "<td align=left ".$clasto."><a title=\"".htmlspecialchars($row['name'])."\" href=details.php?id=".$row["torrent"]."&viewcomm=".$row["id"]."#comm".$row["id"].">	<b>".htmlspecialchars_uni($row['name'])."
		".(!empty($row["viponly"]) ? "<img  border=\"0\" width=\"15px\" alt=\"Данная раздача только для VIP пользователей\" title=\"Данная раздача только для VIP пользователей\" src=\"pic/vipbig.gif\"> ":"")."
		</b></a></td>";
		///".$tags_view[$row["id"]]."
       ///".$dead." 
		
        echo "<td align=center ".$clastf.">".normaltime($row["dcom"], true)."</td>"; 
        echo "<td align=center ".$clasto."><a href=userdetails.php?id=".$row["user"].">".get_user_class_color($row["class"],$row["username"])."</a>";
        echo "</td></tr>";
        ++$dp;
    }
    
    if ($dp==0)
     echo "<td align=center>ничего нет</td>";
    

    echo "</tr></table>";

} elseif ($id=="vcoms"){


$limit = 15;

echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" class=\"main\"><tr>"; 
   /// echo "<td align=\"center\" class=\"colhead\">Название</td><td align=\"center\" class=\"colhead\">Время</td><td align=\"center\" class=\"colhead\">Сообщил(а)</td></tr>"; 

$result = sql_query("SELECT c.id,c.offer, off_reqs.name,off_reqs.category,off_reqs.perform,categories.name AS namecat,
dcom,c.user, users.username,users.class FROM (SELECT comments.id,comments.user,comments.offer,IF(comments.added>comments.editedat,comments.added,comments.editedat) AS dcom
FROM comments WHERE comments.offer>0 ORDER BY dcom DESC) AS c 
JOIN off_reqs ON off_reqs.id = c.offer 
JOIN categories ON categories.id = off_reqs.category
JOIN users ON users.id = c.user 
GROUP BY offer ORDER BY dcom DESC LIMIT $limit") or sqlerr(__FILE__, __LINE__);
$do=0;
while($row = mysql_fetch_assoc($result)) {

if ($do%2 == 0){
$clasto = "class = 'b'";
$clastf = "class = 'a'";
} else {
$clasto = "class = 'a'";
$clastf = "class = 'b'";
}

//".(get_user_class() >= UC_MODERATOR ? "<a href=edit.php?id=$row[torrent]>
	//	<img style=\"border:none\" alt=\"Редактировать\" title=\"Редактировать\" src=\"pic/pen.gif\"></a>":"")."

        

      //  $row["name"]=strlen($row["name"])>70?(substr($row["name"],0,60)."..."):$name7; 
 ///<b>Категория</b>: <a href=\"browse.php?incldead=1&cat=$name_id\">$name_cat</a> 
         $unda="<b>Выполнен</b>: ".($row['perform']=="yes"?"Да":"Нет");
         
        echo "<tr>";
        echo "<td align=left ".$clasto."><a title=\"".htmlspecialchars($row['name'])."\" href=\"detailsoff.php?id=".$row["offer"]."&viewcomm=".$row["id"]."#comm".$row["id"]."\"><b>".htmlspecialchars_uni($row['name'])."</b></a>";
		
	///	echo "<br><small><b>Категория</b>: <a href=\"detailsoff.php?incldead=0&cat=".$row['category']."\">".($row["namecat"])."</a></small>";
		
		echo "</td>";
        
        echo "<td align=center ".$clastf.">".normaltime($row["dcom"], true)."</td>"; 
        echo "<td align=center ".$clasto."><a href=userdetails.php?id=".$row["user"].">".get_user_class_color($row["class"],$row["username"])."</a>";
        echo "</td></tr>";
        ++$do;
    }
    
    if ($do==0)
    echo "<td align=center class=b>Ничего нет <br><a href=detailsoff.php>Список запросов</a> :: <a href=uploadoff.php>Создать запрос</a></td>";
    
    echo "</tr></table>";

} elseif ($id=="vpols"){

$limit = 15;

    echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" class=\"main\"><tr>"; 
   /// echo "<td align=\"center\" class=\"colhead\">Название</td><td align=\"center\" class=\"colhead\">Время</td><td align=\"center\" class=\"colhead\">Сообщил(а)</td></tr>"; 

$result = sql_query("SELECT	c.id,c.poll,polls.question,	dcom,c.user, users.username,users.class 
FROM (SELECT comments.id,comments.user,comments.poll,IF(comments.added>comments.editedat,comments.added,comments.editedat) AS dcom FROM comments WHERE comments.poll>0
ORDER BY dcom DESC) AS c 
JOIN polls ON polls.id = c.poll AND polls.forum=0
JOIN users ON users.id = c.user 
GROUP BY poll ORDER BY dcom DESC LIMIT $limit") or sqlerr(__FILE__, __LINE__);
$rp=0;
while($row = mysql_fetch_assoc($result)) {

if ($rp%2 == 0){
$clasto = "class = 'b'";
$clastf = "class = 'a'";
} else {
$clasto = "class = 'a'";
$clastf = "class = 'b'";
}

        /// $row["question"] = format_comment($row['question']);
         
        echo "<tr>";
        echo "<td align=left ".$clasto."><a href=polloverview.php?id=".$row["poll"]."&viewcomm=".$row["id"]."#comm".$row["id"]."><b>".format_comment($row['question'])."</b></a>
		</td>";
        
        echo "<td align=center".$clastf.">".normaltime($row["dcom"], true)."</td>"; 
        echo "<td align=center".$clasto."><a href=userdetails.php?id=".$row["user"].">".get_user_class_color($row["class"],$row["username"])."</a>";
        echo "</td></tr>";
        ++$rp;
    }
    if ($rp==0)
     echo "<td align=center>ничего нет</td>";
    

    echo "</tr></table>";

} elseif ($id=="vnews"){


$limit = 15;

    echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" class=\"main\"><tr>"; 
   /// echo "<td align=\"center\" class=\"colhead\">Название</td><td align=\"center\" class=\"colhead\">Время</td><td align=\"center\" class=\"colhead\">Сообщил(а)</td></tr>"; 

$result = sql_query("SELECT	c.id,c.news,news.subject,dcom,c.user, users.username,users.class 
FROM (SELECT comments.id,comments.user,comments.news,IF(comments.added>comments.editedat,comments.added,comments.editedat) AS dcom
FROM comments WHERE comments.news>0
ORDER BY dcom DESC) AS c 
JOIN news ON news.id = c.news
JOIN users ON users.id = c.user 
GROUP BY news ORDER BY dcom DESC LIMIT $limit") or sqlerr(__FILE__, __LINE__);
$af=0;
while($row = mysql_fetch_assoc($result)) {

if ($af%2 == 0){
$clasto = "class = 'b'";
$clastf = "class = 'a'";
} else {
$clasto = "class = 'a'";
$clastf = "class = 'b'";
}	

        
        echo "<tr>";
        echo "<td align=left ".$clasto."><a href=newsoverview.php?id=".$row["news"]."&viewcomm=".$row["id"]."#comm".$row["id"]."><b>".format_comment($row['subject'])."</b></a>
		</td>";
        
        echo "<td align=center ".$clastf.">".normaltime($row["dcom"], true)."</td>"; 
        echo "<td align=center ".$clasto."><a href=userdetails.php?id=".$row["user"].">".get_user_class_color($row["class"],$row["username"])."</a>";
        echo "</td></tr>";
        ++$af;
    }
    if ($af==0)
     echo "<td align=center>ничего нет</td>";
    

    echo "</tr></table>";
}


}

?>
