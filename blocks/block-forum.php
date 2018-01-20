<?

if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}

/// версия блока 7Max7 создан для Muz-tracker.net
$blocktitle = ".:: <a title=\"На главную форума\" class=\"altlink_white\" href='forums.php'>Главная</a> :: <a title=\"Поиск выражений на форуме\" class=\"altlink_white\" href='forums.php?action=search'>Поиск на форуме</a> :: <a title=\"Перейти к непрочитанным сообщениям\" class=\"altlink_white\" href='forums.php?action=viewunread'>К непрочитанным сообщениям</a> :: <a title=\"Пометить все сообщения как прочитанные\" class=\"altlink_white\" href='forums.php?action=catchup'>Пометить прочитанным</a> ::.";




?>

<style type="text/css">
<!--
#tabs_f {
    text-align: left;
}
#tabs_f .tab_f {
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
#tabs_f.active {
    border-bottom: none;
    padding-bottom: 5px;
    background: #FAFAFA;
    cursor: default;
}
#tabs_f #body_f {
    border: 1px solid #cecece;
    padding: 5px;
    margin-bottom: 10px;
    background: #FAFAFA;
}
#tabs_f .tab_f_error {
    background:url(../pic/error.gif) repeat-y;
    height: 34px;
    line-height: 34px;
    padding-left: 40px;
}

#tabs_f{padding-top: 7px} 
#tabs_f span{position: relative;border-bottom: 1px solid #FAFAFA !important;top: -1px;-webkit-border-top-left-radius: 4px;-webkit-border-top-right-radius: 4px;-moz-border-radius-topleft: 4px;-moz-border-radius-topright: 4px;border-top-left-radius: 4px;border-top-right-radius: 4px;}
#tabs_f span:hover{background: #FAFAFA;}
.active{color: #C60000;}


table.tt {
    width: 100%;
}
table.tt td {
    padding: 5px;
}
table.tt td.tt {
    background-color: #777;
    padding: 7px;
}

pre {height:100px}
ul {float:left; margin:10px; margin-top:0}
li {cursor:pointer; color:#006; font-weight:700}
li.activ {color:red}
h4 {font-weight:100}
/*.example {display:none}*/
-->

</style>

<script type="text/javascript">

var loading = "<img src=\"pic/loading.gif\" alt=\"Загрузка..\" title=\"Загрузка..\"/>";
jQuery(function() {
    jQuery(".tab_f").click ( function(){
        if(jQuery(this).hasClass("active"))
            return;
        else
        {
            jQuery("#loading").html(loading);
            var act = jQuery(this).attr("id");
            jQuery(this).toggleClass("active");
            jQuery(this).siblings("span").removeClass("active");
            jQuery.post("block-forums_jquery.php",{"act":act},function (response) {
                jQuery("#body_f").empty();
                jQuery("#body_f").append(response);
                jQuery("#loading").empty();
            });
        }
    });
    jQuery('.zebra:even').css({backgroundColor: '#EEEEEE'});
    if(jQuery.browser.msie) {
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


global $CURUSER;

$idu=$CURUSER["class"];
$cacheStatFile = "cache/block-forum_$idu.txt";
$expire = 60*60; // 60 минут 60*60
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) {
   $content.=file_get_contents($cacheStatFile);
} else
{

$content.= ("<div id=\"tabs_f\">\n");
$content.= ("<span class=\"tab_f active\" id=\"0\">Последние комментарии</span>\n");

$content.= ("<span title=\"Важные темы на форуме\" class=\"tab_f\" id=\"5\">Важные</span>\n");

$content.= ("<span title=\"Самые активные темы на форуме (по количество сообщений)\" class=\"tab_f\" id=\"1\">Активные</span>\n");

$content.= ("<span title=\"Скрытые темы (скрываемые на главной страничке форума и в блоке последних комментариев)\" class=\"tab_f\" id=\"2\">Скрытые</span>\n");

$content.= ("<span title=\"Самые просматриваемые темы на форуме по количеству просмотру\" class=\"tab_f\" id=\"3\">Просматриваемые</span>\n");

if ($CURUSER) // пока как тестовой
$content.= ("<span title=\"Последние, вами созданные, темы\" class=\"tab_f\" id=\"4\">Мои</span>\n");

$content.= ("<span id=\"loading\"></span>\n");
$content.= ("<div id=\"body_f\">\n");

///////// форум стандарт

$content.="<table align=center cellpadding=0 cellspacing=0 width=100%><tr> 
<td class=colhead align=left width=70%>&nbsp;Тема сообщения&nbsp;</td>
<td class=colhead align=left width=30%>&nbsp;Категория &nbsp;</td>
<td class=colhead align=center>&nbsp;Ответ / Просмотр</td>
<td class=colhead align=center>&nbsp;Автор&nbsp;</td>
<td class=colhead align=right>&nbsp;Последний&nbsp;</td>
</tr>";


    if ($CURUSER && !empty($CURUSER["class"])) {
        $curuserclass = $CURUSER["class"];
    }  else
    $curuserclass = 1;

    $for = sql_query("SELECT ft.*, ff.name as forumname, ff.description, ff.minclassread,
	(SELECT COUNT(*) FROM posts WHERE topicid=ft.id) AS post_num FROM topics as ft, forums as ff WHERE ff.id=ft.forumid AND ft.visible='yes' AND ff.visible='yes' AND ff.minclassread<=".sqlesc($curuserclass)." ORDER BY lastpost DESC LIMIT 10")or sqlerr(__FILE__, __LINE__);

    $arraytopic=array();

    while ($topicarr = mysql_fetch_assoc($for)) {

    	 $polls_view = ($topicarr["polls"] == "yes" ? " <img width='13' title=\"Данная тема имеет опрос\" src=\"pic/forumicons/polls.gif\">":"");
    	 
    	$posts = $topicarr["post_num"];
        $postsperpage=20;
        $tpages = floor($posts / $postsperpage);

        if ($tpages * $postsperpage != $posts)
          $tpages++;

         if ($tpages > 1)
        {
          $topicpages = " [";

          for ($i = 1; $i <= $tpages; ++$i){
      
	      $topicpages .= "".($i==1 ? "":" ")."<a href=forums.php?action=viewtopic&topicid=$topicarr[id]&page=$i>$i</a>".($i<> $tpages ? "":"")."";
           }
          $topicpages .= "]";
        }
        else
          $topicpages = "";
    	
        $forumname = "<a title=\"".$topicarr["description"]."\" href=\"/forums.php?action=viewforum&amp;forumid=$topicarr[forumid]\">".htmlspecialchars($topicarr["forumname"])."</a>";

        $topicid = $topicarr["id"];
        
        $arraytopic[]=$topicid;


        $topic_userid = $topicarr["userid"];
        $views = $topicarr["views"];
        $sticky = $topicarr["sticky"];
        $lastpost = $topicarr["lastpost"];
     
        $posts=$topicarr["post_num"]; /// 
        $replies = max(0, $posts - 1);

        $res = sql_query("SELECT p.*, la.class AS la_class,la.username AS la_username,
		ow.class AS owner_class,ow.username AS owner_username
		 FROM posts AS p 
		LEFT JOIN users AS la ON la.id=p.userid	
		LEFT JOIN users AS ow ON ow.id='$topic_userid'	
		WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
        $arr = mysql_fetch_assoc($res);
        $postid = $arr["id"];
        $userid = $arr["userid"];
        $added = ($arr["added"]);
         
         if ($arr["la_username"]){
        $username = "<a href='userdetails.php?id=$userid'>".get_user_class_color($arr["la_class"], $arr["la_username"])."</a>";
      } else {
        $username = "id: $useri";
       }

         if ($arr["owner_username"]){
            $author = "<a href='userdetails.php?id=$topic_userid'>".get_user_class_color($arr["owner_class"], $arr["owner_username"])."</a>";
        } else {
            $author = "id: $topic_userid";
        }

        $subject = "<a title=\"".$added."\" href=\"forums.php?action=viewtopic&topicid=$topicid&page=last#$lastpost\">" . format_comment($topicarr["subject"]) . "</a>";


   $fast_answ = "<br>
   <table border='1' cellspacing='0' cellpadding='5' width='100%'>
   <form method=\"get\" action=\"forums.php\">
   <input type=hidden name=\"action\" value=\"reply\">
   <input type=hidden name=\"topicid\" value=$topicid>
   <input title=\"Ответить на данное сообщение\" type=submit value='Ответить' class=btn></form>
   </span><span class='btn'>
   <form method=\"get\" action=\"forums.php\">
   <input type=hidden name=\"action\" value=\"quotepost\">
   <input type=hidden name=\"topicid\" value=\"$topicid\">
   <input type=hidden name=\"postid\" value=\"$lastpost\">
   <input title=\"Процитировать данное сообщение\" type=submit value='Цитата' class=btn></form>
   </table>";

   ///.$fast_answ
$content.="<tr>
<td class=b align=left>".($sticky=="yes" ? "<b>Важная</b>: ":"").$subject.$topicpages.$polls_view."</td>
<td class=b align=left>".$forumname."</td>
<td class=b align=center><small>".$replies." / ".$views."</small></td>
<td class=b align=center>".$author."</td>
<td class=b align=right>".$username." <small>".$added."</small></td>";

       $content.="</tr>";
    }

    
    
   $content.="<tr><td align=\"center\" colspan=\"5\" class=\"b\">";

   $content.="<small>Обновлённых тем в скрытых разделах: ";
    //print("<br>Скрытые форумы: "); /// (за сутки) 
   $schet=0;


$for = sql_query("SELECT tp.id AS topid, tp.subject, tp.lastpost, tp.visible, tp.forumid,
ft.minclassread FROM topics AS tp
LEFT JOIN forums AS ft ON ft.id= tp.forumid
WHERE ft.minclassread<=".sqlesc($curuserclass)." AND (tp.visible='no' OR ft.visible='no') AND tp.lastdate>".sqlesc(get_date_time(gmtime() - 86400*7))." ORDER BY tp.lastdate DESC LIMIT 5") or sqlerr(__FILE__, __LINE__); 
 
    $first = true;
    
 while ($farr = mysql_fetch_assoc($for))  {
    	///AND minclassread<=$curuserclass
    	if ($first)
		$first = false;
		 else $content.=(", ");

        $content.="<a href=\"forums.php?action=viewtopic&topicid=".$farr["topid"]."&page=last#".$farr["lastpost"]."\">" . format_comment($farr["subject"]) . "</a>";
         ++$schet;
    }
    
    if ($schet==0) {
    $content.="нет.";
    }
  
    $content.="</small></td></tr></tr>";

    $content.="</table>\n";


///////// форум стандарт
$content.= ("</div>\n");
$content.= ("</div>\n");

}

$fp = fopen($cacheStatFile,"w");
if($fp) {
fputs($fp, $content); 
fclose($fp); 
}
 

if (get_user_class() >= UC_SYSOP) {
$content.= ("<p align=right><font class=small>Time cache now ".date('H:i:s', filemtime($cacheStatFile)).". Next ".date((time() - $expire) -  filemtime($cacheStatFile))."</font></p>");
}

?>