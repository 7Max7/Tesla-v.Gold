<? 
require "include/bittorrent.php";

dbconn(false,false);
header("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

/**
 * Данный файл проверен на индексы, все запросы быстро выполняются.
**/

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{

global $CURUSER;

if (empty($CURUSER))
die;

$id = (int)$_POST["act"];

if (empty($id)){

echo "<table align=center cellpadding=0 cellspacing=0 width=100%>
<tr><td align=\"center\" colspan=\"5\" class=\"a\">Показывается раздел <b>Последние комментарии</b> форума [10]</td></tr>
</table>";
///////// форум стандарт

echo "<table align=center cellpadding=0 cellspacing=0 width=100%><tr> 
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
	(SELECT COUNT(*) FROM posts WHERE topicid=ft.id) AS post_num FROM topics as ft, forums as ff WHERE ff.id=ft.forumid AND ft.visible='yes' AND ff.visible='yes' AND ff.minclassread<=$curuserclass ORDER BY lastpost DESC LIMIT 10")or sqlerr(__FILE__, __LINE__);
    $arraytopic=array();
    while ($topicarr = mysql_fetch_assoc($for))
    {
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
    	
        $forumname = "<a title=\"".$topicarr["description"]."\" href=\"/forums.php?action=viewforum&amp;forumid=$topicarr[forumid]\">".format_comment($topicarr["forumname"])."</a>";

        $topicid = $topicarr["id"];
        
        $arraytopic[]=$topicid;


        $topic_userid = $topicarr["userid"];
        $views = $topicarr["views"];
        $sticky = $topicarr["sticky"];
        $lastpost = $topicarr["lastpost"];
     
        $posts=$topicarr["post_num"]; /// 
        $replies = max(0, $posts - 1);
        

        $res = mysql_query("SELECT p.*, la.class AS la_class,la.username AS la_username,
		ow.class AS owner_class,ow.username AS owner_username
		 FROM posts AS p 
		LEFT JOIN users AS la ON la.id=p.userid	
		LEFT JOIN users AS ow ON ow.id='$topic_userid'	
		WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
        $arr = mysql_fetch_assoc($res);
        $postid = $arr["id"];
        $userid = $arr["userid"];
        $added = display_date_time($arr["added"]);
         
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


echo "<tr>
<td class=b align=left>".($sticky=="yes" ? "<b>Важная</b>: ":"").$subject.$topicpages.$polls_view."</td>
<td class=b align=left>".$forumname."</td>
<td class=b align=center><small>".$replies." / ".$views."</small></td>
<td class=b align=center>".$author."</td>
<td class=b align=right>".$username." <small>".$added."</small></td>";

echo "</tr>";
}

    
    
echo "<tr><td align=\"center\" colspan=\"5\" class=\"b\">";

echo "<small>Обновлённых тем в скрытых разделах: ";
    //print("<br>Скрытые форумы: "); /// (за сутки) 
$schet=0;


$for = mysql_query("SELECT tp.id AS topid, tp.subject, tp.lastpost, tp.visible, tp.forumid,
ft.minclassread FROM topics AS tp
LEFT JOIN forums AS ft ON ft.id= tp.forumid
WHERE ft.minclassread<=".sqlesc($curuserclass)." AND (tp.visible='no' OR ft.visible='no') AND  tp.lastdate>".sqlesc(strtotime(get_date_time()) - 86400*7)." ORDER BY  tp.lastdate DESC LIMIT 5") or sqlerr(__FILE__, __LINE__); 
    
$first = true;
while ($farr = mysql_fetch_assoc($for)) {
    	///AND minclassread<=$curuserclass
if ($first)
$first = false;
else echo (", ");

echo "<a href=\"forums.php?action=viewtopic&topicid=".$farr["topid"]."&page=last#".$farr["lastpost"]."\"><b>" . htmlspecialchars($farr["subject"]) . "</b></a>";
         ++$schet;
    }
    
if ($schet==0) {
echo "нет.";
}
  
echo "</small></td></tr></tr>";

echo "</table>\n";


///////// форум стандарт
}
elseif ($id == "2"){

///////// форум стандарт


echo "<table align=center cellpadding=0 cellspacing=0 width=100%>
<tr><td align=\"center\" colspan=\"5\" class=\"a\">Показывается раздел <b>Скрытые темы</b> форума [20]</td></tr>
</table>";


echo "<table align=center cellpadding=0 cellspacing=0 width=100%><tr> 
<td class=colhead align=left width=70%>&nbsp;Тема сообщения&nbsp;</td>
<td class=colhead align=left width=30%>&nbsp;Категория &nbsp;</td>
<td class=colhead align=center>&nbsp;Сообщений / Просмотр</td>
<td class=colhead align=center>&nbsp;Автор&nbsp;</td>
<td class=colhead align=right>&nbsp;Последний&nbsp;</td>
</tr>";



    if ($CURUSER && !empty($CURUSER["class"])) {
        $curuserclass = $CURUSER["class"];
    }  else
    $curuserclass = 1;

    $for = sql_query("SELECT ft.*, ff.name as forumname, ff.description, ff.minclassread,
	(SELECT COUNT(*) FROM posts WHERE topicid=ft.id) AS post_num 
	FROM topics as ft, forums as ff WHERE ff.id=ft.forumid AND ft.visible='no' AND ff.minclassread<=$curuserclass ORDER BY id DESC LIMIT 20")or sqlerr(__FILE__, __LINE__);
    $arraytopic=array();
    while ($topicarr = mysql_fetch_assoc($for))
    {
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
    	
        $forumname = "<a title=\"".$topicarr["description"]."\" href=\"/forums.php?action=viewforum&amp;forumid=$topicarr[forumid]\">".format_comment($topicarr["forumname"])."</a>";

        $topicid = $topicarr["id"];
        
        $arraytopic[]=$topicid;


        $topic_userid = $topicarr["userid"];
        $views = $topicarr["views"];
        $sticky = $topicarr["sticky"];
        $lastpost = $topicarr["lastpost"];
     
        $posts=$topicarr["post_num"]; /// 
       /// $replies = max(0, $posts - 1);
         $replies = $posts;

        $res = mysql_query("SELECT p.*, la.class AS la_class,la.username AS la_username,
		ow.class AS owner_class,ow.username AS owner_username
		 FROM posts AS p 
		LEFT JOIN users AS la ON la.id=p.userid	
		LEFT JOIN users AS ow ON ow.id='$topic_userid'	
		WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
        $arr = mysql_fetch_assoc($res);
        $postid = $arr["id"];
        $userid = $arr["userid"];
        $added = display_date_time($arr["added"]);
         
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


echo "<tr>
<td class=b align=left>".($sticky=="yes" ? "<b>Важная</b>: ":"").$subject.$topicpages.$polls_view."</td>
<td class=b align=left>".$forumname."</td>
<td class=b align=center><small>".$replies." / ".$views."</small></td>
<td class=b align=center>".$author."</td>
<td class=b align=right>".$username." <small>".$added."</small></td>";

echo "</tr>";
}


echo "</table>\n";


///////// форум стандарт
}
elseif ($id == "1"){

///////// форум стандарт


echo "<table align=center cellpadding=0 cellspacing=0 width=100%>
<tr><td align=\"center\" colspan=\"5\" class=\"a\">Показывается раздел <b>Активные темы</b> форума [15]</td></tr>
</table>";


echo "<table align=center cellpadding=0 cellspacing=0 width=100%>
<tr> 
<td class=colhead align=left width=70%>&nbsp;Тема сообщения&nbsp;</td>
<td class=colhead align=left width=30%>&nbsp;Категория &nbsp;</td>
<td class=colhead align=center>&nbsp;Сообщений / Просмотр</td>
<td class=colhead align=center>&nbsp;Автор&nbsp;</td>
<td class=colhead align=right>&nbsp;Последний&nbsp;</td>
</tr>";



    if ($CURUSER && !empty($CURUSER["class"])) {
        $curuserclass = $CURUSER["class"];
    }  else
    $curuserclass = 1;

    $for = sql_query("SELECT ft.*, ff.name as forumname, ff.description, ff.minclassread,
	(SELECT COUNT(*) FROM posts WHERE topicid=ft.id) AS post_num 
	FROM topics as ft, forums as ff 
	WHERE ff.id=ft.forumid AND ft.visible='yes' AND ff.minclassread<=$curuserclass 
	ORDER BY post_num DESC LIMIT 15")or sqlerr(__FILE__, __LINE__);
    $arraytopic=array();
    while ($topicarr = mysql_fetch_assoc($for))
    {
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
    	
        $forumname = "<a title=\"".$topicarr["description"]."\" href=\"/forums.php?action=viewforum&amp;forumid=$topicarr[forumid]\">".format_comment($topicarr["forumname"])."</a>";

        $topicid = $topicarr["id"];
        
        $arraytopic[]=$topicid;


        $topic_userid = $topicarr["userid"];
        $views = $topicarr["views"];
        $sticky = $topicarr["sticky"];
        $lastpost = $topicarr["lastpost"];
     
        $posts=$topicarr["post_num"]; /// 
       /// $replies = max(0, $posts - 1);
         $replies = $posts;

        $res = mysql_query("SELECT p.*, la.class AS la_class,la.username AS la_username,
		ow.class AS owner_class,ow.username AS owner_username
		 FROM posts AS p 
		LEFT JOIN users AS la ON la.id=p.userid	
		LEFT JOIN users AS ow ON ow.id='$topic_userid'	
		WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
        $arr = mysql_fetch_assoc($res);
        $postid = $arr["id"];
        $userid = $arr["userid"];
        $added = display_date_time($arr["added"]);
         
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


echo "<tr>
<td class=b align=left>".($sticky=="yes" ? "<b>Важная</b>: ":"").$subject.$topicpages.$polls_view."</td>
<td class=b align=left>".$forumname."</td>
<td class=b align=center><small>".$replies." / ".$views."</small></td>
<td class=b align=center>".$author."</td>
<td class=b align=right>".$username." <small>".$added."</small></td>";

echo "</tr>";
}


echo "</table>\n";


///////// форум стандарт
}
elseif ($id == "3"){

///////// форум стандарт
echo "<table align=center cellpadding=0 cellspacing=0 width=100%>
<tr><td align=\"center\" colspan=\"5\" class=\"a\">Показывается раздел <b>Просматриваемые темы</b> форума [20]</td></tr>
</table>";

echo "<table align=center cellpadding=0 cellspacing=0 width=100%><tr> 
<td class=colhead align=left width=70%>&nbsp;Тема сообщения&nbsp;</td>
<td class=colhead align=left width=30%>&nbsp;Категория &nbsp;</td>
<td class=colhead align=center>&nbsp;Сообщений / Просмотр</td>
<td class=colhead align=center>&nbsp;Автор&nbsp;</td>
<td class=colhead align=right>&nbsp;Последний&nbsp;</td>
</tr>";



    if ($CURUSER && !empty($CURUSER["class"])) {
        $curuserclass = $CURUSER["class"];
    }  else
    $curuserclass = 1;

    $for = sql_query("SELECT ft.*, ff.name as forumname, ff.description, ff.minclassread,
	(SELECT COUNT(*) FROM posts WHERE topicid=ft.id) AS post_num 
	FROM topics as ft, forums as ff 
	WHERE ff.id=ft.forumid AND ft.visible='yes' AND ff.minclassread<=$curuserclass 
	ORDER BY views DESC LIMIT 20")or sqlerr(__FILE__, __LINE__);
    $arraytopic=array();
    while ($topicarr = mysql_fetch_assoc($for))
    {
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
    	
        $forumname = "<a title=\"".$topicarr["description"]."\" href=\"/forums.php?action=viewforum&amp;forumid=$topicarr[forumid]\">".format_comment($topicarr["forumname"])."</a>";

        $topicid = $topicarr["id"];
        
        $arraytopic[]=$topicid;


        $topic_userid = $topicarr["userid"];
        $views = $topicarr["views"];
        $sticky = $topicarr["sticky"];
        $lastpost = $topicarr["lastpost"];
     
        $posts=$topicarr["post_num"]; /// 
       /// $replies = max(0, $posts - 1);
         $replies = $posts;

        $res = mysql_query("SELECT p.*, la.class AS la_class,la.username AS la_username,
		ow.class AS owner_class,ow.username AS owner_username
		 FROM posts AS p 
		LEFT JOIN users AS la ON la.id=p.userid	
		LEFT JOIN users AS ow ON ow.id='$topic_userid'	
		WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
        $arr = mysql_fetch_assoc($res);
        $postid = $arr["id"];
        $userid = $arr["userid"];
        $added = display_date_time($arr["added"]);
         
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


echo "<tr>
<td class=b align=left>".($sticky=="yes" ? "<b>Важная</b>: ":"").$subject.$topicpages.$polls_view."</td>
<td class=b align=left>".$forumname."</td>
<td class=b align=center><small>".$replies." / ".$views."</small></td>
<td class=b align=center>".$author."</td>
<td class=b align=right>".$username." <small>".$added."</small></td>";

echo "</tr>";
}


echo "</table>\n";


///////// форум стандарт
}
elseif ($id == "4"){

///////// форум стандарт
echo "<table align=center cellpadding=0 cellspacing=0 width=100%>
<tr><td align=\"center\" colspan=\"5\" class=\"a\">Показывается раздел <b>Личные (вами созданные) темы</b> форума [25]</td></tr>
</table>";

echo "<table align=center cellpadding=0 cellspacing=0 width=100%><tr> 
<td class=colhead align=left width=70%>&nbsp;Тема сообщения&nbsp;</td>
<td class=colhead align=left width=30%>&nbsp;Категория &nbsp;</td>
<td class=colhead align=center>&nbsp;Сообщений / Просмотр</td>
<td class=colhead align=center>&nbsp;Автор&nbsp;</td>
<td class=colhead align=right>&nbsp;Последний&nbsp;</td>
</tr>";



    if ($CURUSER && !empty($CURUSER["class"])) {
        $curuserclass = $CURUSER["class"];
    }  else
    $curuserclass = 1;

    $for = sql_query("SELECT ft.*, ff.name as forumname, ff.description, ff.minclassread,
	(SELECT COUNT(*) FROM posts WHERE topicid=ft.id) AS post_num 
	FROM topics as ft, forums as ff 
	WHERE ff.id=ft.forumid AND ft.visible='yes' AND ff.minclassread<=$curuserclass AND ft.userid=".sqlesc($CURUSER["id"])."
	ORDER BY ft.id DESC LIMIT 25")or sqlerr(__FILE__, __LINE__);
    $arraytopic=array();
    while ($topicarr = mysql_fetch_assoc($for))
    {
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
    	
        $forumname = "<a title=\"".$topicarr["description"]."\" href=\"/forums.php?action=viewforum&amp;forumid=$topicarr[forumid]\">".format_comment($topicarr["forumname"])."</a>";

        $topicid = $topicarr["id"];
        
        $arraytopic[]=$topicid;


        $topic_userid = $topicarr["userid"];
        $views = $topicarr["views"];
        $sticky = $topicarr["sticky"];
        $lastpost = $topicarr["lastpost"];
     
        $posts=$topicarr["post_num"]; /// 
       /// $replies = max(0, $posts - 1);
         $replies = $posts;

        $res = mysql_query("SELECT p.*, la.class AS la_class,la.username AS la_username,
		ow.class AS owner_class,ow.username AS owner_username
		 FROM posts AS p 
		LEFT JOIN users AS la ON la.id=p.userid	
		LEFT JOIN users AS ow ON ow.id='$topic_userid'	
		WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
        $arr = mysql_fetch_assoc($res);
        $postid = $arr["id"];
        $userid = $arr["userid"];
        $added = display_date_time($arr["added"]);
         
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


echo "<tr>
<td class=b align=left>".($sticky=="yes" ? "<b>Важная</b>: ":"").$subject.$topicpages.$polls_view."</td>
<td class=b align=left>".$forumname."</td>
<td class=b align=center><small>".$replies." / ".$views."</small></td>
<td class=b align=center>".$author."</td>
<td class=b align=right>".$username." <small>".$added."</small></td>";

echo "</tr>";
}


echo "</table>\n";


///////// форум стандарт
}
elseif ($id == "5"){

///////// форум стандарт
echo "<table align=center cellpadding=0 cellspacing=0 width=100%>
<tr><td align=\"center\" colspan=\"5\" class=\"a\">Показывается раздел <b>Важные темы</b> форума [20]</td></tr>
</table>";

echo "<table align=center cellpadding=0 cellspacing=0 width=100%><tr> 
<td class=colhead align=left width=70%>&nbsp;Тема сообщения&nbsp;</td>
<td class=colhead align=left width=30%>&nbsp;Категория &nbsp;</td>
<td class=colhead align=center>&nbsp;Сообщений / Просмотр</td>
<td class=colhead align=center>&nbsp;Автор&nbsp;</td>
<td class=colhead align=right>&nbsp;Последний&nbsp;</td>
</tr>";



    if ($CURUSER && !empty($CURUSER["class"])) {
        $curuserclass = $CURUSER["class"];
    }  else
    $curuserclass = 1;

    $for = sql_query("SELECT ft.*, ff.name as forumname, ff.description, ff.minclassread,
	(SELECT COUNT(*) FROM posts WHERE topicid=ft.id) AS post_num 
	FROM topics as ft, forums as ff 
	WHERE ff.id=ft.forumid AND ft.visible='yes' AND ff.minclassread<=$curuserclass AND ft.sticky='yes'
	ORDER BY ft.id DESC LIMIT 20")or sqlerr(__FILE__, __LINE__);
    $arraytopic=array();
    while ($topicarr = mysql_fetch_assoc($for))
    {
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
    	
        $forumname = "<a title=\"".$topicarr["description"]."\" href=\"/forums.php?action=viewforum&amp;forumid=$topicarr[forumid]\">".format_comment($topicarr["forumname"])."</a>";

        $topicid = $topicarr["id"];
        
        $arraytopic[]=$topicid;


        $topic_userid = $topicarr["userid"];
        $views = $topicarr["views"];
        $sticky = $topicarr["sticky"];
        $lastpost = $topicarr["lastpost"];
     
        $posts=$topicarr["post_num"]; /// 
       /// $replies = max(0, $posts - 1);
         $replies = $posts;

        $res = mysql_query("SELECT p.*, la.class AS la_class,la.username AS la_username,
		ow.class AS owner_class,ow.username AS owner_username
		 FROM posts AS p 
		LEFT JOIN users AS la ON la.id=p.userid	
		LEFT JOIN users AS ow ON ow.id='$topic_userid'	
		WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
        $arr = mysql_fetch_assoc($res);
        $postid = $arr["id"];
        $userid = $arr["userid"];
        $added = display_date_time($arr["added"]);
         
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


echo "<tr>
<td class=b align=left>".($sticky=="yes" ? "<b>Важная</b>: ":"").$subject.$topicpages.$polls_view."</td>
<td class=b align=left>".$forumname."</td>
<td class=b align=center><small>".$replies." / ".$views."</small></td>
<td class=b align=center>".$author."</td>
<td class=b align=right>".$username." <small>".$added."</small></td>";

echo "</tr>";
}


echo "</table>\n";


///////// форум стандарт
}
else 
print_r($_POST);


}

?>
