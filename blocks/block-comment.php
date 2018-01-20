<?

if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

$blocktitle = "Последние комментарии пользователей";

?>

<style type="text/css">
<!--
#tabo {
    text-align: left;
}
#tabo .tabg {
    border: 1px solid #cecece;
    padding: 5px 10px 5px 10px;
  /*
    background:#ededed;
    */
    margin-right:5px;
    line-height: 23px;
    cursor: pointer;
    font-weight: bold;
}

 #tabo{padding-top: 7px} 
 #tabo span{position: relative;border-bottom: 1px solid #FAFAFA !important;top: -1px;-webkit-border-top-left-radius: 4px;-webkit-border-top-right-radius: 4px;-moz-border-radius-topleft: 4px;-moz-border-radius-topright: 4px;border-top-left-radius: 4px;border-top-right-radius: 4px;}
 #tabo span:hover{background: #FAFAFA;}
.active{color: #C60000;}

#tabo.actives {
    border-bottom: none;
    padding-bottom: 5px;
    background: #FAFAFA;
    cursor: default;
}
#tabo #body_com {
    border: 1px solid #cecece;
    padding: 5px;
    margin-bottom: 10px;
    background: #FAFAFA;
}
#tabo .tabg_error {
    background:url(../pic/error.gif) repeat-y;
    height: 34px;
    line-height: 34px;
    padding-left: 40px;
}
table.tt {
    width: 100%;
}
table.tt td {
    padding: 5px;
}
table.tt td.tt {
  /*  background-color: #777;*/
    padding: 7px;
}
.example {float:left; margin:20px; border-bottom:#ccc 1px solid; cursor:pointer}
.pics {
    height:  232px;  
    width:   232px;  
    padding: 0;  
    margin:  0;  
}
 
.pics img {
    padding: 15px;  
    border:  1px solid #ccc;  
    background-color: #eee;  
    width:  200px; 
    height: 200px; 
    top:  0; 
    left: 0 
}
pre {height:100px}

ul {float:left; margin:10px; margin-top:0}
li {cursor:pointer; color:#006; font-weight:700}
li.activ {color:red }
h4 {font-weight:100}
/*.example {display:none}*/
-->
</style>

<script type="text/javascript">

var loading = "<img src=\"pic/loading.gif\" alt=\"Загрузка..\" />";
jQuery(function() {
    jQuery(".tabg").click ( function(){
        if(jQuery(this).hasClass("actives"))
            return;
        else
        { 
            jQuery("#loading").html(loading);
    
            var act = jQuery(this).attr("id");
            jQuery(this).toggleClass("active");
            jQuery(this).siblings("span").removeClass("active");
            
            
            jQuery(this).siblings("span").removeClass("actives");
            jQuery('.actives').css({backgroundColor: '#EEEEEE'});
            jQuery.post("block-com_jquery.php",{"act":act},function (response) {
                jQuery("#body_com").empty();
                jQuery("#body_com").append(response);
                jQuery("#loading").empty();
            });
        }
    });

    if(jQuery.browser.msie)
    {
        width = jQuery('#profile_right h2').width();
        if (width > 422)
            jQuery('#profile_right').width(width);
        else
        {
            jQuery('#profile_right').width("422");
            jQuery('#profile_container').width("686");
        }
    }
});
</script>

<?

$cacheStatFile = "cache/block-comment.txt";
$expire = 120*60; // 120 минут 120*60
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) {
   $content.=file_get_contents($cacheStatFile);
} else
{
	//<!----Привем Конкурентам, от 7Max7 для Muz-Tracker.Net 2010----!>\n
$content.= "<div id=\"tabo\">";
$content.= "<span title=\"Комментарии по Торрентам\" class=\"tabg actives\" id=\"vtors\">Торренты</span>\n";
$content.= "<span title=\"Комментарии по Запросам\" class=\"tabg\" id=\"vcoms\">Запросы / Предложения</span>\n";
$content.= "<span title=\"Комментарии по Опросам\" class=\"tabg\" id=\"vpols\">Опросы</span>\n";
$content.= "<span title=\"Комментарии по Новостям\" class=\"tabg\" id=\"vnews\">Новости</span>\n";
$content.= "<span id=\"loading\"></span>\n";
$content.= "<div id=\"body_com\">\n";

/////////

$limit = 15;

    $content .= "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" class=\"main\"><tr>"; 
   // $content .= "<td align=\"center\" class=\"colhead\">Название</td><td align=\"center\" class=\"colhead\">Время</td><td align=\"center\" class=\"colhead\">Сообщил(а)</td></tr>"; 
   

	//ON users.id = c.user WHERE torrents.visible='yes'GROUP BY torrent
    $result = sql_query("SELECT c.id,c.torrent,torrents.name,torrents.category,IF(torrents.numratings < 1, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS ratingsum, dcom,c.user,users.username,users.class FROM (SELECT comments.id,comments.user,comments.torrent,IF(comments.added>comments.editedat,comments.added,comments.editedat) AS dcom FROM comments WHERE comments.torrent>0 ORDER BY dcom DESC) AS c JOIN torrents ON torrents.id = c.torrent JOIN users ON users.id = c.user GROUP BY torrent ORDER BY dcom DESC LIMIT $limit") or sqlerr(__FILE__, __LINE__);
$viw=0;
 while($row = mysql_fetch_assoc($result)) {

$ratingsum=$row["ratingsum"];

if ($viw%2 == 0){
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

        $name7 = htmlspecialchars_uni($row['name']);
        ///$row["name"]=strlen($row["name"])>70?(substr($row["name"],0,60)."..."):$name7; 
       // $row["name"]= format_comment($row['name']);
 ///<b>Категория</b>: <a href=\"browse.php?incldead=1&cat=$name_id\">$name_cat</a> 

 ///<br>".pic_rating_b(10,$ratingsum)."
        $content .= "<tr>";
        $content .= "<td align=left ".$clasto.">".$dead."<a href=details.php?id=".$row["torrent"]."&viewcomm=".$row["id"]."#comm".$row["id"].">	<b><span title='".$name7."'>".$name7."</span>".(!empty($row["viponly"]) ? "<img  border=\"0\" width=\"15px\" alt=\"Данная раздача только для VIP пользователей\" title=\"Данная раздача только для VIP пользователей\" src=\"pic/vipbig.gif\"> ":"")."</b></a> </td>";
		//".$tags_view[$row["id"]]." 
        $content .= "<td align=center ".$clastf.">".normaltime($row["dcom"], true)."</td>"; 
        $content .= "<td align=center ".$clasto."><a href=userdetails.php?id=".$row["user"].">".get_user_class_color($row["class"],$row["username"])."</a>";
        $content .= "</td></tr>\n";
        ++$viw;
        }

     if ($viw==0)
     $content.= "<td align=center class=b>ничего нет</td>";


    $content.= "</tr></table>";




/////////
$content.= "</div>";
$content.= "</div>";


}

         $fp = fopen($cacheStatFile,"w");
   if($fp)
   { 
    fputs($fp, $content); 
    fclose($fp); 
   }
 





?> 