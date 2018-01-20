<?
require_once("include/bittorrent.php");

define('IN_FORUM', true);


/*
if (mysql_num_rows($res))
stderr_f("success","f");
*/





/////////////////
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
dbconn(false);
header("Content-Type: text/html; charset=" .$tracker_lang['language_charset']);

$dt = get_date_time(gmtime() - 300);

$res_s = sql_query("SELECT id, username, class FROM users WHERE forum_access<>'0000-00-00 00:00:00' and forum_access > ".sqlesc($dt)." ORDER BY forum_access DESC") or sqlerr(__FILE__,__LINE__);
// or url LIKE '%$url_e%'
$lastid=0;
$title_who_s="";
while ($ar_r = mysql_fetch_assoc($res_s)) {

$username = $ar_r['username'];
$id_use = $ar_r['id'];

if (!empty($title_who_s))
$title_who_s.=", ";

$title_who_s.= "<a href=\"userdetails.php?id=$id_use\">".get_user_class_color($ar_r["class"], $ar_r["username"]) . "</a>";
++$lastid;

}


//$dt = sqlesc(time() - 300); //180

$res_s = sql_query("SELECT ip FROM sessions WHERE time > ".sqlesc(get_date_time(gmtime() - 300))." AND url LIKE '%forums.php%' AND uid='-1' ORDER BY time DESC") or sqlerr(__FILE__,__LINE__);
// or url LIKE '%$url_e%'
$lastid=0;
//$title_who_s="";
while ($ar_r = mysql_fetch_assoc($res_s)) {

$ip_use = $ar_r['ip'];

if (!empty($title_who_s))
$title_who_s.=", ";

$title_who_s.= $ip_use;
++$lastid;

}

echo $title_who_s;
die;
}
/////////////////


require_once(ROOT_PATH."include/functions_forum.php");
dbconn();
///loggedinorreturn();
get_cleanup();




$content= "<center><font color=\"white\">...</font></center>";

define("LOGO",$content); /// это рекламный блок



if ($CURUSER["on_line"]<="600" && $CURUSER){
//выжидаем 10 минут

if (!stristr($CURUSER["usercomment"],"попытка на форуме")!==false){
$usercomment = get_date_time() . " - возможно спамер (попытка на форуме).\n". $CURUSER["usercomment"];
mysql_query("UPDATE users SET usercomment=".sqlesc($usercomment)." WHERE id=".$CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
}

stderr_f("Антиспам предупреждение", "В доступе отказано. Вы зарегистрировались недавно, пожалуйста повторите попытку позже.");
die;
}

  $action = isset($_GET["action"]) ? htmlentities($_GET["action"]) : '';
  $forum_pic_url = $BASEURL."/pic/forumicons/";
  $maxsubjectlength = 255;
  
// $postsperpage = (int) $CURUSER["postsperpage"];
//if (!$postsperpage) 

	$postsperpage = 25;



  if (!empty($action)){
  
   if ($action == 'edittopic')
  {
  	
  	if (get_user_class() < UC_MODERATOR)
  	  die("Вы не с администрации");

  	$topicid = (int) $_GET['topicid'];

  	if (!is_valid_id($topicid))
  	  die("id не цифра");
  	  
  	$res = sql_query("SELECT * FROM topics WHERE id = ".sqlesc($topicid)."") or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_assoc($res);
  	if (empty($arr)){
  	die("Такой темы не существует в базе данных.");
  	}
  	$topic_name=format_comment($arr["subject"]);
  	stdhead_f("Редактирование топика");


  $modcomment=htmlspecialchars($arr["t_com"]);
  $forums=htmlspecialchars($arr["subject"]);
  $sticky=$arr["sticky"];
  $visible=$arr["visible"];
  $locked=$arr["locked"];
  
  echo("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
  echo("<tr><td class=colhead align=\"center\" colspan=\"2\"><a name=comments></a><b>.::: Администрирование темы :::.</b><br><a class=\"altlink_white\" title=\"Вернутся обратно к показу сообщений в теме\" href=\"".$BASEURL."/forums.php?action=viewtopic&topicid=".$topicid."\">".$topic_name."</a></td></tr>");
  

  echo("<form method=\"post\" action=\"".$BASEURL."/forums.php?action=edittopicmod&topicid=".$topicid."\">");
  echo("<tr><td>");
  echo("<tr><td class=\"a\"><b>Прикрепить тему</b>: 
  <input type=radio name=sticky value='yes' " . ($sticky=="yes" ? " checked" : "") . "> Да <input type=radio name=sticky value='no' " . ($sticky=="no" ? " checked" : "") . "> Нет <i>по умолчанию все темы без прикрепления (без важности)</i></td></tr>");  

  print("<tr><td class=\"a\"><b>Видимая тема</b>: <input type=radio name='visible' value='yes' " . ($visible=="yes" ? " checked" : "") . "> Да <input type=radio name='visible' value='no' " . ($visible=="no" ? " checked" : "") . "> Нет </td></tr>");
  
  echo("<tr><td class=\"a\"><b>Заблокировать тему</b>: <input type=radio name=locked value='yes' " . ($locked=="yes" ? " checked" : "") . "> Да <input type=radio name=locked value='no' " . ($locked=="no" ? " checked" : "") . "> Нет  <i>пользователи не смогут писать сообщения в теме.</i></td></tr>"); 
  
  echo("<tr><td class=\"a\"><b>Переименовать тему</b>: <input type=text name=subject size=60 maxlength='".$maxsubjectlength."' value=\"".$forums."\"></td></tr>"); 
  // }
   
$res = sql_query("SELECT id, name, minclasswrite FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);
while ($arr = mysql_fetch_assoc($res))


if ($arr["id"] <> $forumid && $CURUSER >= $arr["minclasswrite"])
$select.=("<option value=" . $arr["id"] . ">" . $arr["name"] . "\n");

  echo("<tr><td class=\"a\">
     <b>Переместить тему в</b>: <select name=forumid>
	 <option value=0>выбрать из списка</option>".$select."</select>
	 </td></tr>");
  
     
   echo("<tr><td class=\"a\">
   <b>Удалить тему</b>: <input name=\"delete_topic\" value=\"1\" type=\"checkbox\"><i> удаляем полностью тему</i><br>
   <input type=text name=reson size=60 maxlength=$maxsubjectlength value=\"не подходит под правила\"> <i>причина обязательна</i>
   </td></tr>");
   
   echo("<tr><td class=\"a\">История темы <b>$forums</b> и ее прилегающих сообщений [".strlen($modcomment)."]<br>
  <textarea cols=100% rows=6".(get_user_class() < UC_SYSOP ? " readonly" : " name=modcomment").">$modcomment</textarea>
   </td></tr>  
   <tr><td class=\"a\"><b>Добавить заметку</b>: <textarea cols=100% rows=3 name=modcomm></textarea></td></tr>");


  echo("<tr><td align=\"center\" colspan=\"2\">");
  echo("<input type=\"hidden\" value=\"".$topicid."\" name=\"topicid\"/>");
  echo("<input type=\"submit\" class=btn value=\"Выполнить действие\" />");
  echo("</td></tr></table></form><br>");

	  stdfoot_f();
	  die;
  }
  
   if ($action == "catchup")
  {
    catch_up();
    
   /// header("Location: $BASEURL/forums.php");
       header("Refresh: 5; url=$BASEURL/forums.php");
		stderr_f("Успешно автопереход через 5 сек", "Успешно помеченны все сообщения и темы как прочитанны.<br> Нажмите <a href=\"$BASEURL/forums.php\">ЗДЕСЬ</a>, если не хотите ждать.");
		die("catch_up");
// stderr_f("Успешно", "Все сообщения были помеченны как прочитаны.");
  }
 

  if ($action == "forum_fcom" && get_user_class() >= UC_MODERATOR)
  {
   $forumfid=(int)$_POST["forumfid"];
   
  	if (!is_valid_id($forumfid))
    die("id не цифра");
  	  
  	$mod_comment=htmlspecialchars($_POST["modcomment"]);
    $mod_comm=htmlspecialchars($_POST["modcomm"]);

  	$res_fo = sql_query("SELECT f_com FROM forums WHERE id = ".sqlesc($forumfid)."") or sqlerr(__FILE__, __LINE__);
	$arr_for = mysql_fetch_assoc($res_fo);
  	if (empty($arr_for)){
  	die("Такой категории не существует в базе данных.");
  	}
	  else
  	{
  	   	if (get_user_class() == UC_SYSOP){
		$modik = $mod_comment;
        }
    	else
        $modik=$arr_for["f_com"]; /// оригинал комментария категории форума	
  		
  		
	if (!empty($mod_comm)) {
	$modik = date("Y-m-d") . " - Заметка от ".$CURUSER["username"].": ".$mod_comm."\n" . $modik;
   sql_query("UPDATE forums SET f_com =".sqlesc($modik)." WHERE id=".sqlesc($forumfid)."") or sqlerr(__FILE__, __LINE__);
  	}
    }
    
       header("Refresh: 10; url=$BASEURL/forums.php?action=viewforum&forumid=$forumfid");
		stderr_f("Успешно автопереход через 10 сек", "Заметка ".(!empty($mod_comm)? "добавленна":"обновленна").".<br> Нажмите <a href=\"$BASEURL/forums.php?action=viewforum&forumid=".$forumfid."\">ЗДЕСЬ</a>, если не хотите ждать.");
	 die("forum_fcom");
   // die;
   /// header("Location: $BASEURL/forums.php?action=viewforum&forumid=$forumfid");
}
}


  switch($action) {
  
    case 'viewforum':
    {

    $forumid = (int)$_GET["forumid"];

    if (!is_valid_id($forumid))
      header("Location: $BASEURL/forums.php");

    $page = isset($_GET["page"]) ? (int)$_GET["page"] : 0;
    $userid = $CURUSER["id"];

    $res = sql_query("SELECT name, minclassread,description,f_com FROM forums WHERE id=".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);


if (!mysql_num_rows($res)){
stderr_f("Внимание", "В доступе отказано. Такой категории на форуме нет.");
die;
}

    $arr = mysql_fetch_assoc($res);
    $f_com=$arr["f_com"];
    
    $forumname = htmlspecialchars($arr["name"]);
   $description = htmlspecialchars($arr["description"]);
   
    if ($arr["minclassread"]>$CURUSER["class"] && $arr["minclassread"]<>"0"){
     header("Location: $BASEURL/forums.php");
      die();
     }
     
     
 // $perpage = $CURUSER["postsperpage"];
//	if (!$perpage) 
	$perpage = 25;
 ///,(SELECT COUNT(*) FROM topics WHERE topics.forumid=forums.id) AS num_fo 
    $res = sql_query("SELECT COUNT(*) FROM topics WHERE forumid=".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);
   $arr = mysql_fetch_row($res);

   $num = $arr[0];
  $count = $arr[0];
 // $count=$arr["num_fo"];
 // $num=$arr["num_fo"];

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "$BASEURL/forums.php?action=viewforum&forumid=".$forumid."&");
   
    $topicsres = sql_query("SELECT *,(SELECT COUNT(*) FROM posts WHERE posts.topicid=topics.id) AS num_po FROM topics WHERE forumid=".sqlesc($forumid)." ORDER BY sticky, lastpost DESC $limit") or sqlerr(__FILE__, __LINE__);

  ///  stdhead_f("Форум :: Просмотр категории");

    if ($CURUSER && get_date_time(gmtime() - 60) >= $CURUSER["forum_access"]){
	sql_query("UPDATE users SET forum_access = ".sqlesc(get_date_time())." WHERE id = ".sqlesc($CURUSER["id"])."") or sqlerr(__FILE__, __LINE__);
    }
   
    $numtopics = mysql_num_rows($topicsres);


 if (!empty($numtopics)){
    $forum_view1= "<a class=\"altlink_white\" href='$BASEURL/forums.php?action=viewunread'>Непрочитанные темы</a>\n";
}

if ($CURUSER["forum_com"]=="0000-00-00 00:00:00"){
$forum_view2= "<a class=\"altlink_white\" href='$BASEURL/forums.php?action=newtopic&forumid=".$forumid."'>Создать тему в этой категории</a>\n";
}


 /// таблица до коммента
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\"  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">
<html>

<head>
".meta_forum($forumid)."
<link rel=\"stylesheet\" type=\"text/css\" href=\"js/style_forums.css\" />
<link rel=\"search\" type=\"application/opensearchdescription+xml\" title=\"Muz-Tracker Форум\" href=\"".$BASEURL."/js/forum.xml\">
<script language=\"javascript\" type=\"text/javascript\" src=\"js/jquery.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"js/forums.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"js/swfobject.js\"></script> 
<script language=\"javascript\" type=\"text/javascript\" src=\"js/functions.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"js/tooltips.js\"></script>
<title>Форум  - $SITENAME</title>
</head>
  
<table cellpadding=\"0\" cellspacing=\"0\" id=\"main\">
<tr>
<td class=\"main_col1\"><img src=\"/pic/forumicons/clear.gif\" alt=\"\" /></td>
<td class=\"main_col2\"><img src=\"/pic/forumicons/clear.gif\" alt=\"\" /></td>
<td class=\"main_col3\"><img src=\"/pic/forumicons/clear.gif\" alt=\"\" /></td>
</tr>
<tr>
<td>&nbsp;</td>
<td valign=\"top\">
<table cellpadding=\"0\" cellspacing=\"0\" id=\"header\">
<tr>
<td id=\"logo\">".LOGO."</td>

<td class=\"login\">
<div id=\"login_box\"><span class=smallfont>
<div>Здравствуйте, ".($CURUSER ? "<a href='".$BASEURL."/userdetails.php?id=".$CURUSER["id"]."'>".$CURUSER["username"]."</a>
<div>Последнее обновление: <span class=\"time\">".$CURUSER["forum_access"]."</span></div>
".($CURUSER ? "<div>".$newmessage."</div>":"")."
":" для просмотра полной версии данных,  
<div>пожалуйста, <a href='".$BASEURL."/login.php'>авторизуйтесь</a>.
<div>Права просмотра: Гость</div>
</div>
")."</span></div>
</div>
</td>
</tr>
</table>
</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>

<td>
<table cellpadding=\"0\" cellspacing=\"0\" id=\"menu_h\">
<tr>
<td class=\"first\"><a href=\"".$BASEURL."/index.php\">Главная сайта</a></td> 
<td class=\"shad\"><a href=\"".$BASEURL."/browse.php\">Торренты</a></td> 
<td class=\"shad\"><a href=\"".$BASEURL."/forums.php\">Главная форума</a></td>

".($CURUSER ? "<td class=\"shad\"><a href=\"".$BASEURL."/forums.php?action=search\">Поиск</a></td>
<td class=\"shad\"><a href=\"".$BASEURL."/forums.php?action=viewunread\">Непрочитанные комментарии</a></td>
<td class=\"shad\"><a title=\"Поменить все сообщения прочитанными\" href=\"".$BASEURL."/forums.php?action=catchup\">Все как прочитанное</a></td>":"")."

</tr>
</table>
</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>
<td valign=\"top\">
<table cellpadding=\"0\" cellspacing=\"0\" id=\"content_s\">
<tr>
<td class=\"content_col1\"><img src=\"/pic/forumicons/clear.gif\" alt=\"\" /></td>
<td class=\"content_col_left\">&nbsp;</td>
<td class=\"content_col5\"><img src=\"/pic/forumicons/clear.gif\" alt=\"\" /></td>
</tr>
<tr>
<td>&nbsp;</td>
<td valign=\"top\">
<br />";
///<a name=\"poststop\" id=\"poststop\" href=\"".$BASEURL."/forums.php\">Главная страничка форума</a> <hr>


echo "<div class=\"tcat_t\"><div class=\"tcat_r\"><div class=\"tcat_l\"><div class=\"tcat_tl\"><div class=\"tcat_simple\">
<table cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"tcat_name\">
Данный раздел форума посвящен категории \"".$forumname."\" <br class=\"tcat_clear\" /> ".$description." 
".(get_user_class() == UC_SYSOP ? "[<a class=\"altlink_white\" href=\"forummanage.php\">Создать новую категорию</a>]":"")."
</td></tr></table>
<br class=\"tcat_clear\" />
</div></div></div></div></div>";


/// таблица до коммента
$rai13 = sql_query("SELECT f_com FROM forums WHERE id=".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);
$roa13 = mysql_fetch_assoc($rai13);
$comment_f = htmlspecialchars($roa13["f_com"]);


if ($numtopics > 0) {

echo "<div class=\"post_body\" id=\"collapseobj_forumbit_5\" style=\"\">
<table cellspacing=\"0\" cellpadding=\"0\" class=\"forums\">
<tr>
<td class=\"f_thead_1\">Тема</td>
<td class=\"f_thead_2\">Ответов / Просмотров</td>
<td class=\"f_thead_2\">Автор</td>
<td class=\"f_thead_2\">Последний</td>
</tr>";

$da=0;
while ($topicarr = mysql_fetch_assoc($topicsres)) {

$topicid = $topicarr["id"];
$topic_userid = $topicarr["userid"];
$topic_views = $topicarr["views"];
$views = number_format($topic_views);
$locked = $topicarr["locked"] == "yes";
$sticky = $topicarr["sticky"] == "yes";
$polls_view = ($topicarr["polls"] == "yes" ? " <img width=\"13\" title=\"Данная тема имеет опрос\" src=\"pic/forumicons/polls.gif\">":"");

$posts = $topicarr["num_po"];

$replies = max(0, $posts - 1);
$postsperpage=20;
$tpages = floor($posts / $postsperpage);

if ($tpages * $postsperpage != $posts)
++$tpages;

if ($tpages > 1) {
$topicpages = " [";

for ($i = 1; $i <= $tpages; ++$i){
$topicpages .= "".($i==1 ? "":" ")."<a title=\"$i страница\" href=forums.php?action=viewtopic&topicid=$topicid&page=$i>$i</a>".($i<> $tpages ? "":"")."";
}
$topicpages .= "]";
}
else
$topicpages = "";

/*
$res = sql_query("SELECT p.*,t.forumid,f.f_com,t.visible,u.username AS ed_username,
u.class AS ed_class, o.username AS or_username, o.class AS or_class
FROM posts AS p 
LEFT JOIN topics AS t ON t.id=p.topicid
LEFT JOIN forums AS f ON t.forumid=f.id
LEFT JOIN users AS u ON u.id=p.userid
LEFT JOIN users AS o ON o.id=".sqlesc($topic_userid)."
WHERE p.topicid=".sqlesc($topicid)." ORDER BY p.id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
*/


$res = sql_query("SELECT p.*,t.forumid,f.body AS bodypost,t.visible,u.username AS ed_username,
u.class AS ed_class, o.username AS or_username, o.class AS or_class
FROM posts AS p 
LEFT JOIN topics AS t ON t.id=p.topicid
LEFT JOIN posts AS f ON f.topicid=p.topicid 
LEFT JOIN users AS u ON u.id=p.userid
LEFT JOIN users AS o ON o.id=".sqlesc($topic_userid)."
WHERE p.topicid=".sqlesc($topicid)." ORDER BY p.id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);


$arr = mysql_fetch_assoc($res);


$subject_f = $forumname;

$combody_f = strip_tags(preg_replace("/\[((\s|.)+?)\]/is", "", $arr["bodypost"]));

if (strlen($combody_f) >= 255)
$combody_f = substr($combody_f,0,255)." <a title=\"К первому сообщению в этой теме\" href=\"forums.php?action=viewtopic&topicid=$topicid\">....</a>";

$combody_f = ($combody_f); /// какую фильтрацию ?

$lppostid = (int)$arr["id"];
$lppostadd = $arr["added"];
$lpuserid = $arr["userid"];
$lpadded = "<nobr>" . ($arr['added']) . "</nobr>";

if ($arr["ed_username"] && $topic_userid<>0){
$lpusername = "<a href=\"userdetails.php?id=$lpuserid\"><b>".get_user_class_color($arr["ed_class"], $arr["ed_username"])."</b></a>";
}
else
$lpusername = "id: $lpuserid";

if ($arr["or_username"] && $topic_userid<>0){
$lpauthor = "<a href=\"userdetails.php?id=$topic_userid\"><b> ".get_user_class_color($arr["or_class"], $arr["or_username"])."</b></a>";
}
else
$lpauthor = "id: ".$topic_userid;

$r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=".sqlesc($CURUSER["id"])." AND topicid=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_row($r);

$new = ($lppostadd > get_date_time(gmtime() - $readpost_expiry)) ? 1 : 0;

$topicpic = ($locked ? ($new ? "lockednew" : "locked") : ($new ? "unlockednew" : "unlocked"));
$view = ($locked ? ($new ? "Есть новые Непрочитанные комментарии" : "Тема заблокирована") : ($new ? "Есть новые Непрочитанные комментарии" : "В данной теме нет непрочитаных сообщений"));
$subject = ($sticky ? "<b>Важная</b>: " : "") . "<a title=\"".htmlspecialchars($topicarr["subject"])."\" href=\"forums.php?action=viewtopic&topicid=$topicid&page=last\"><b>".format_comment_light($topicarr["subject"])."</b></a>$topicpages";

if ($da%2==1) {
$class="f_row_off";
} else {
$class="f_row_on";	
}

echo "<tr>
<td class=\"".$class."\" width=\"100%\">

<table>
<tr><td align=\"left\" width=\"5%\"><img title=\"$view\" src=\"{$forum_pic_url}{$topicpic}.gif\">
</td>
<td align=\"left\">
".format_comment_light($subject).$polls_view." ".($arr["visible"]=="no"?"[<b>Скрытая Тема</b>]":"")."<br>
<small>".$combody_f."</small>
</td></tr>
</table>

</td>
<td class=\"".$class."\" id=\"f60\"><div class=\"smallfont\">".$replies." / ".$views."</div></td>
<td class=\"".$class."\" id=\"f60\"><div class=\"smallfont\">".$lpauthor."</div></td>
<td class=\"".$class."\" id=\"f60\"><div class=\"smallfont\">".$lpadded."<br>".$lpusername." </div></td>
</tr>";
++$da;
}
 
echo "</table>$pagerbottom</div>
<div class=\"off\"><div class=\"tcat_b\"><div class=\"tcat_bl\"><div class=\"tcat_br\"></div></div></div>
</div><br>";
}
else
echo("</div><div class=\"off\"><div class=\"tcat_b\"><div class=\"tcat_bl\"><div class=\"tcat_br\"></div></div></div></div><br>

<table align='center' cellspacing='0' cellpadding='5' width='100%'>

<table class=\"main\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td class=\"embedded\">
<br><div align=\"center\" class=\"error\"><b>В данной категории нет тем</b></div><br></td></tr></table>\n");

$arr = get_forum_access_levels($forumid);
$maypost = get_user_class() >= $arr["write"] && get_user_class() >= $arr["create"];

if (!$maypost && $CURUSER || ($CURUSER["forum_com"]<>"0000-00-00 00:00:00" && $CURUSER))
echo "<p><h3>У вас нет прав для создания темы в этой категории форума.</h3></p>\n";
/*
    $htmlout .= "<p><table border=0 class=main cellspacing=0 cellpadding=0><tr>\n";
 if (!empty($numtopics)){
    $htmlout .= "<td class=embedded><form method=get action=?><input type=hidden " .
    "name=action value=viewunread><input type=submit value='Непрочитанные темы' class=btn></form></td>\n";
}

    if ($maypost)
      $htmlout .= "<td class=embedded><form method=get action=?><input type=hidden " .
      "name=action value=newtopic><input type=hidden name=forumid " .
      "value=$forumid><input type=submit value='Создать тему в этой категории' class=btn style='margin-left: 10px'></form></td>\n";

    $htmlout .= "</tr></table></p>\n";
*/
    
 ///   echo $htmlout;
   $maxsubjectlength=(int)$maxsubjectlength;
   
  if ($CURUSER["forum_com"]=="0000-00-00 00:00:00" && $CURUSER){
  	
  echo "
<div class=\"tcat_t\"><div class=\"tcat_r\"><div class=\"tcat_l\"><div class=\"tcat_tl\"><div class=\"tcat_simple\">

<div align=\"center\"><a name=comments></a><b>.::: Создать тему для обсуждения :::.</b></div>

<br class=\"tcat_clear\" />
</div></div></div></div></div>
<div class=\"post_body\" id=\"collapseobj_forumbit_5\" align=\"center\" style=\"\">
<table cellspacing=\"0\" cellpadding=\"0\" class=\"forums\">";

  echo("<div align=\"center\"><form name=\"comment\" method=\"post\" action=\"forums.php?action=post\">
  <b>Введите название</b>: <input title=\"Введите обязательно название темы\" type=text size=45 maxlength='".$maxsubjectlength."' name=subject style=\"color: black; border: 1px #FF5500 dashed;  height: 20px\"></div>");
  
  echo("<center><table border=\"0\"><tr><td class=\"clear\">");
  echo("<div align=\"center\">". textbbcode("comment","body","", 1) ."</div>");
 
  echo("</td></tr><tr><td  align=\"center\" class=\"a\" colspan=\"2\">");
  echo("<input type=hidden name=forumid value=\"".$forumid."\">");
  echo("<input type=\"submit\" class=btn value=\"Создать новую тему в категории - ".$forumname."\" />");
  echo("</form>");
  
   echo "</table></div>
<div class=\"off\"><div class=\"tcat_b\"><div class=\"tcat_bl\"><div class=\"tcat_br\"></div></div></div>
</div><br>";
  }


  if (get_user_class() >= UC_MODERATOR) {
  	
  	if (empty($posts)){
  	/// $res1 = sql_query("SELECT f_com FROM forums WHERE id=".sqlesc($forumid)."") or sqlerr(__FILE__, __LINE__);
   /// $arr2 = mysql_fetch_assoc($res1);
    $comment_f=htmlspecialchars($f_com);
  	}
  	
echo("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">
<tr><td class=colhead align=\"center\" colspan=\"2\"><hr><a name=comments></a><b><center>.::: Администрирование категории :::.</b></center></td></tr>
<form method=\"post\" action=\"".$BASEURL."/forums.php?action=forum_fcom\">
<input type=hidden name=forumfid value=\"".$forumid."\">
<tr><td class=\"a\"  align=\"center\">История этой категории <b>".$subject_f."</b> и ее прилегающих тем и сообщений [".strlen($comment_f)."]<br>
<textarea cols=100% rows=6".(get_user_class() < UC_SYSOP ? " readonly" : " name=modcomment").">".$comment_f."</textarea>
</td></tr>
<tr><td class=\"a\" align=\"center\"><b>Добавить заметку</b>: <textarea cols=100% rows=3 name=modcomm></textarea>
<input type=\"submit\" class=btn value=\"Выполнить действие - добавить заметку в категорию ".$subject_f."\" />
</td></tr>
</td></tr>
</form></table>");
}
    insert_quick_jump_menu($forumid);

   stdfoot_f();  
    die;
    }
      exit();
      break;
      
    case 'viewtopic': {
    $topicid = (int) $_GET["topicid"];

    $page = isset($_GET["page"]) ? (int) $_GET["page"]: false;

    if (!is_valid_id($topicid))
      die("Для вас ошибка, неверный id");

    $userid = $CURUSER["id"];
    $res = sql_query("SELECT *,(SELECT COUNT(*) FROM posts WHERE topicid=".sqlesc($topicid).") AS num_com FROM topics WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_assoc($res) or stderr_f("Форум ошибка", "Не найдено сообщение");

    $t_com_arr=$arr["t_com"];
    $locked = ($arr["locked"] == 'yes' ? "Да":"Нет");
    $subject = format_comment($arr["subject"]);
    $sticky = ($arr["sticky"] == "yes" ? "Да":"Нет");
    $forumid = $arr["forumid"];
    $topic_polls = $arr["polls"];
    $views=number_format($arr["views"]);
    $num_com=number_format($arr["num_com"]);
    sql_query("UPDATE topics SET views = views + 1 WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

    $res = sql_query("SELECT * FROM forums WHERE id=".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_assoc($res) or die("Нет такого форума с таким id: ".$forumid);
    $forum = htmlspecialchars($arr["name"]);

  //  if ($CURUSER["class"] < $arr["minclassread"] && ($arr["minclassread"]<2 AND !$CURUSER))
	//	stderr_f("Ошибка", "У вас нет доступа к этой категории на форуме.");
	
   
    if ($arr["minclassread"]>$CURUSER["class"] && !empty($arr["minclassread"])){
  stderr_f("Ошибка прав","Данная категория и ее сообщения недоступны к показу.");
  die;
  }
  
	

    $res = sql_query("SELECT COUNT(*) FROM posts WHERE topicid=".sqlesc($topicid)."") or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_row($res);

    $postcount = $arr[0];

	$perpage = 20;
    $count = $arr[0];   

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "forums.php?action=viewtopic&topicid=".$topicid."&");

if ($CURUSER) {

$unread = $CURUSER["unread"];
$newmessage1 = $unread . " нов" . ($unread > 1 ? "ых" : "ое"); 
$newmessage2 = " сообщен" . ($unread > 1 ? "ий" : "ие"); 
if ($unread)
$newmessage = "<b><a href='".$BASEURL."/message.php?action=new'>У вас ".$newmessage1." ".$newmessage2."</a></b>"; 
else
$newmessage="";
}


//  stdhead_f("Просмотр темы");
	
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\"  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">
<html>
<head>
".meta_forum("",$topicid)."
<link rel=\"stylesheet\" type=\"text/css\" href=\"js/style_forums.css\" />
<link rel=\"search\" type=\"application/opensearchdescription+xml\" title=\"Muz-Tracker Форум\" href=\"".$BASEURL."/js/forum.xml\">
<script language=\"javascript\" type=\"text/javascript\" src=\"js/jquery.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"js/forums.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"js/swfobject.js\"></script> 
<script language=\"javascript\" type=\"text/javascript\" src=\"js/functions.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"js/tooltips.js\"></script>

<title>".strip_tags($subject)." :: ".$forum." - ".$SITENAME."</title>

</head>
  
<table cellpadding=\"0\" cellspacing=\"0\" id=\"main\">
<tr>
<td class=\"main_col1\"><img src=\"/pic/forumicons/clear.gif\" alt=\"\" /></td>
<td class=\"main_col2\"><img src=\"/pic/forumicons/clear.gif\" alt=\"\" /></td>
<td class=\"main_col3\"><img src=\"/pic/forumicons/clear.gif\" alt=\"\" /></td>
</tr>
<tr>
<td>&nbsp;</td>
<td valign=\"top\">
<table cellpadding=\"0\" cellspacing=\"0\" id=\"header\">
<tr>
<td id=\"logo\">".LOGO."</td>

<td class=\"login\">
<div id=\"login_box\"><span class=smallfont>
<div>Здравствуйте, ".($CURUSER ? "<a href='".$BASEURL."/userdetails.php?id=".$CURUSER["id"]."'>".$CURUSER["username"]."</a>
<div>Последнее обновление: <span class=\"time\">".$CURUSER["forum_access"]."</span></div>
".($CURUSER ? "<div>".$newmessage."</div>":"")."
":" для просмотра полной версии данных,  
<div>пожалуйста, <a href='".$BASEURL."/login.php'>авторизуйтесь</a>.
<div>Права просмотра: Гость</div>
</div>
")."</span></div>
</div>
</td>
</tr>
</table>
</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>

<td>
<table cellpadding=\"0\" cellspacing=\"0\" id=\"menu_h\">
<tr>
<td class=\"first\"><a href=\"".$BASEURL."/index.php\">Главная сайта</a></td> 
<td class=\"shad\"><a href=\"".$BASEURL."/browse.php\">Торренты</a></td> 
<td class=\"shad\"><a href=\"".$BASEURL."/forums.php\">Главная форума</a></td>

".($CURUSER ? "<td class=\"shad\"><a href=\"".$BASEURL."/forums.php?action=search\">Поиск</a></td>
<td class=\"shad\"><a href=\"".$BASEURL."/forums.php?action=viewunread\">Непрочитанные комментарии</a></td>
<td class=\"shad\"><a title=\"Поменить все сообщения прочитанными\" href=\"".$BASEURL."/forums.php?action=catchup\">Все как прочитанное</a></td>":"")."

</tr>
</table>
</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>
<td valign=\"top\">
<table cellpadding=\"0\" cellspacing=\"0\" id=\"content_s\">
<tr>
<td class=\"content_col1\"><img src=\"/pic/forumicons/clear.gif\" alt=\"\" /></td>
<td class=\"content_col_left\">&nbsp;</td>
<td class=\"content_col5\"><img src=\"/pic/forumicons/clear.gif\" alt=\"\" /></td>
</tr>
<tr>
<td>&nbsp;</td>
<td valign=\"top\">
<br />";


if ($topic_polls=="yes"){

if ($CURUSER){
?>
<script type="text/javascript" src="<?=$BASEURL;?>/js/forums_poll.core.js"></script>
<link href="<?=$BASEURL;?>/js/poll.core.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">$(document).ready(function(){loadpoll("<?=$topicid;?>");});</script>
<?
echo"<div id=\"poll_container\">
<div id=\"loading_poll\" style=\"display:none\"></div>
<noscript><b>Пожалуйста включите показ скриптов</b></noscript>
</div><hr>";
}
else echo "Опрос виден только для пользователей, которые авторизовались на сайте.";
}

//echo $pagertop;

echo "<div class=\"tcat_t\"><div class=\"tcat_r\"><div class=\"tcat_l\">
<div class=\"tcat_tl\"><div class=\"tcat_submenu\"><span class=smallfont>
<div class=\"tcat_popup\"><b>Просмотров</b>: ".$views."</div>
<div class=\"tcat_popup\" id=\"threadtools\"><b>Комментариев</b>: ".$num_com."</div>
<div class=\"tcat_popup\"><b>Важная</b>: ".$sticky."</div>
<div class=\"tcat_popup\" id=\"threadrating\"><b>Заблокирована</b>: ".$locked."</div></span>";


echo "<table cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"tcat_name\"><b>".$subject." </b>
<br>Категория: <a name=\"poststop\" id=\"poststop\" href=\"".$BASEURL."/forums.php?action=viewforum&forumid=".$forumid."\">".$forum."</a>
".(get_user_class()>= UC_MODERATOR ? "<br><a href='".$BASEURL."/forums.php?action=edittopic&topicid=".$topicid."'>Администрирование темы</a>":"")."
</td></tr>
</table>
<br class=\"tcat_clear\"/></div></div></div></div></div>";


$t_com="<textarea cols=\"115%\" rows=\"5\" readonly>".$t_com_arr."</textarea>";

if (get_user_class()>= UC_MODERATOR)
echo "<div align=\"center\" width=\"100%\"class=\"tcat_t\">
<div class=\"spoiler-wrap\" id=\"115\"><div class=\"spoiler-head folded clickable\">История этого топика (Тестовый режим)</div><div class=\"spoiler-body\" style=\"display: none;\">
$t_com
</div></div>
</div>";

$res = sql_query("SELECT p. *, u.username, u.class, u.last_access, u.ip, u.signatrue,u.forum_com, u.signature,u.avatar, u.title, u.enabled, u.warned, u.hiderating,u.uploaded,u.downloaded,u.donor, e.username AS ed_username,e.class AS ed_class,
(select count(*) FROM posts WHERE userid=p.userid) AS num_topuser
FROM posts p 
LEFT JOIN users u ON u.id = p.userid
LEFT JOIN users e ON e.id = p.editedby
WHERE topicid = ".sqlesc($topicid)." ORDER BY p.id $limit") or sqlerr(__FILE__, __LINE__);
	
	
	if ($CURUSER && get_date_time(gmtime() - 60)>= $CURUSER["forum_access"]){
	sql_query("UPDATE users SET forum_access = ".sqlesc(get_date_time())." WHERE id = ".sqlesc($CURUSER["id"])."") or sqlerr(__FILE__, __LINE__);
    }


    $pc = mysql_num_rows($res);
    $pn = 0;
    $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=".sqlesc($CURUSER["id"])." AND topicid=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
    //$a = mysql_fetch_row($r);
    $a = mysql_fetch_assoc($r);
    $lpr = $a["lastpostread"];
        
    echo "<div class=\"post_body\"><div id=\"posts\">";
    
    $num=1;
    
    
    while ($arr = mysql_fetch_assoc($res)) {

      ++$pn;
      $ed_username = $arr["ed_username"];
      $ed_class= $arr["ed_class"];
      $postid = $arr["id"];
      $posterid = $arr["userid"];
      $added = ($arr['added']);
      $postername = $arr["username"];
      $posterclass = $arr["class"];

      if (empty($postername) && $posterid<>0) {
        $by = "<b>id</b>: $posterid";
      }
      elseif ($posterid==0 && empty($postername)) {
      	$by="<i>Сообщение от </i><font color=gray>[<b>System</b>]</font>";
      }
      else
      {
         $by = "<a href='".$BASEURL."/userdetails.php?id=".$posterid."'><b>" .get_user_class_color($posterclass,  $postername). "</b></a>";
      }
      
  
if ($posterid<>0 && !empty($postername)){
	     if (strtotime($arr["last_access"]) > gmtime() - 600) {
			     	$online = "online";
			     	$online_text = "На форуме";
			     } else {
			     	$online = "offline";
			     	$online_text = "Не на форуме";
			     }
	
		    if ($arr["downloaded"] > 0) {
			    	$ratio = $arr['uploaded'] / $arr['downloaded'];
			    	$ratio = number_format($ratio, 2);
			    } elseif ($arr["uploaded"] > 0) {
			    	$ratio = "Infinity";
			    } else {
			    	$ratio = "---";
			    }

if ($row["hiderating"]=="yes"){
$print_ratio="<b>+100%</b>";
} else
$print_ratio=$ratio;///: $ratio
} else {
unset($print_ratio);
unset($ratio);
unset($online_text);
unset($online);
}


if ($CURUSER["cansendpm"]=='yes' && ($CURUSER["id"]<>$posterid && $posterid<>0 && !empty($postername))){
$cansendpm=" <a href='".$BASEURL."/message.php?action=sendmessage&amp;receiver=".$posterid."'><img src='".$BASEURL."/pic/button_pm.gif' border=0 alt=\"Отправить сообщение\" ></a>";	
}
else {
unset($cansendpm);}


if ($arr["forum_com"]<>"0000-00-00 00:00:00" && !empty($postername)){
$ban="<div><b>Бан до </b>".$arr["forum_com"]."</div>";
} else unset($ban);

//if (get_user_class() >= UC_VIP){
$online_view="<img src=\"".$BASEURL."/pic/button_".$online.".gif\" alt=\"".$online_text."\" title=\"".$online_text."\" style=\"position: relative; top: 2px;\" border=\"0\" height=\"14\">";
//}

//if (get_user_class() > UC_MODERATOR){
$numb_view="<a title=\"Число, под которым это сообщение в базе: ".$postid."\" href='".$BASEURL."/forums.php?action=viewpost&id=".$postid."'>Постоянная ссылка для этого сообщения [<b>$postid</b>]</a>";
//}

if (!empty($arr["avatar"])){
$avatar = ($CURUSER["id"]==$posterid ? "<a href=\"".$BASEURL."/my.php\"><img alt=\"Аватар, по клику переход в настройки\" title=\"Аватар, по клику переход в настройки\" width=80 height=80 src=\"".$BASEURL."/pic/avatar/".$arr["avatar"]."\"/></a>":"<img width=80 height=80 src=\"".$BASEURL."/pic/avatar/".$arr["avatar"]."\"/>");
} else
$avatar = "<img width=80 height=80 src=\"".$BASEURL."/pic/avatar/default_avatar.gif\"/>";


$body = format_comment($arr["body"]);


if (get_user_class()>= UC_MODERATOR && !empty($arr['body_ori']))
$viworiginal = true;
else
$viworiginal = false;



echo("<a name=".$postid.">\n");

if ($pn == $pc){
echo("<a name=last>\n");
}


echo "<div>
<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
<tr>
<td class=\"postbit_top\">
<div class=\"postbit_head\" >
<div class=\"normal\" style=\"float:right\">".$numb_view."</div>
<div class=\"normal\">".normaltime(($arr['added']),true)."</div>
</div>
      
<table cellpadding=\"0\" cellspacing=\"10\" width=\"100%\">
<tr>
<td>".$avatar."</td>
<td nowrap=\"nowrap\">
<div>".$by." ".$online_view.$cansendpm."
</div>
<div class=\"smallfont\">".get_user_class_name($arr["class"])." ".(empty($arr["title"]) ? "": "(".htmlspecialchars($arr["title"]).")")."<br>
".($arr["donor"] == "yes" ? "<img src=\"".$BASEURL."/pic/star.gif\" alt='Донор'>" : "")
.($arr["enabled"] == "no" ? "<img src=\"".$BASEURL."/pic/disabled.gif\" alt=\"Этот аккаунт отключен\" style='margin-left: 2px'>" : ($arr["warned"] == "yes" ? "<img src=\"".$BASEURL."/pic/warned.gif\" alt=\"Предупрежден\" border=0>" : "")) . "
</div>
</td>

<td width=\"100%\">&nbsp;</td>
<td valign=\"top\" nowrap=\"nowrap\" class=\"n_postbit_info\"> 

<table cellpadding=\"0\" cellspacing=\"10\" width=\"100%\">
<tr>
<td valign=\"top\" nowrap=\"nowrap\"><div class=\"smallfont\"></div></td>
<td valign=\"top\" nowrap=\"nowrap\">
<div class=\"smallfont\">
<div><b>Рейтинг</b>:  ".$print_ratio."</div>
<div ><b>Залил</b>: ".mksize($arr["uploaded"]) ." </div>
<div><b>Скачал</b>: ".mksize($arr["downloaded"])."</div>
".$ban."
".($CURUSER? "<b>Сообщений на форуме</b>: <a href=\"".$BASEURL."/forums.php?action=search_post&userid=".$posterid."\" title=\"Поиск всех сообщений у ".$postername."\">".$arr["num_topuser"]."</a>":"")."
</div>
</td></tr></table>

<img src=\"/pic/forumicons/clear.gif\" alt=\"\" width=\"225\" height=\"1\" border=\"0\" />
</td></tr>
</table>
</td>
</tr>
<tr>
<td class=\"alt1\"> 
<hr size=\"1\" style=\"color:; background-color:\" />
<div class=\"pad_12\">
<div class=\"img_rsz\">".$body."</div>

".((is_valid_id($arr['editedby']) AND !empty($arr['editedby']))? "<hr>
<div class=\"post_edited smallfont\">
<em>Последний раз редактировалось ".(get_user_class() >= UC_MODERATOR ? "<a href='".$BASEURL."/userdetails.php?id=".$arr["editedby"]."'><b> ".get_user_class_color($ed_class, $ed_username)." </b></a>":"")." в <span class=\"time\">".($arr['editedat'])."</span>. ".($viworiginal == false ? "<a href='".$BASEURL."/forums.php?action=viewpost&id=".$postid."&ori'>К Оригинальному сообщению.</a>":"")."</em>
</div>
":"")."
      
<div style=\"margin-top: 10px\" align=\"right\">
".(($arr["signatrue"]=="yes" && $arr["signature"]) ? "  <span class=\"smallfont\">".format_comment($arr["signature"])."</span>": "")."
</div>
</div>
</td>
    
    
</tr>
</table>
 <div class=\"pad_12 alt1\" style=\"border-top: 1px solid #ccc;\">
 <table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
  <tr>";

echo "<td class=\"alt2\" align=right><span class=smallfont>";

if (!empty($posterid)){
echo (!empty($posterid) ? "[<a href='".$BASEURL."/forums.php?action=search_post&userid=".$posterid."'><b>Найти все сообщения</b></a>] ":"");
}

if ($CURUSER){
echo (!empty($posterid) ? "[<a href='".$BASEURL."/userdetails.php?id=".$posterid."'><b>Профиль</b></a>] ":"");
}

if ($CURUSER && $CURUSER["id"]<>$posterid){
echo (!empty($posterid) ? "[<a href='".$BASEURL."/message.php?receiver=".$posterid."&action=sendmessage'><b>Послать Сообщение</b></a>] ":"");
}

if ($CURUSER && $CURUSER["id"]<>$posterid){
echo ($posterid<>0 && !empty($postername) && $CURUSER["forum_com"]=="0000-00-00 00:00:00" ? "[<a href='".$BASEURL."/forums.php?action=quotepost&topicid=".$topicid."&postid=".$postid."'><b>Цитировать</b></a>] ":"");
}
  
if (get_user_class() >= UC_MODERATOR)
echo ($arr["ip"] ? "[<a title=\"Искать этот ip адресс в базе данных через административный поиск\" href=\"".$BASEURL."/usersearch.php?ip=".$arr["ip"]."\" ><b>".$arr["ip"]."</b></a>] ":"");
  
if (($CURUSER["forum_com"]=="0000-00-00 00:00:00" && $CURUSER["id"]==$posterid) || get_user_class() >= UC_MODERATOR){
echo "[<a href='".$BASEURL."/forums.php?action=editpost&postid=".$postid."'><b>Редактировать</b></a>] ";
}

if (get_user_class() >= UC_MODERATOR || $CURUSER["id"]==$posterid)
echo "[<a href='".$BASEURL."/forums.php?action=deletepost&postid=".$postid."'><b>Удалить</b></a>] ";

echo "</span></td>";

echo "</tr></table></div></div>";

echo "<div id=\"lastpost\"></div></div></div>";




//die;
    if ($CURUSER){
   /// stdfoot_f();
    $postadd = $arr['added'];

	if (($postid > $lpr) AND ($postadd > get_date_time(gmtime() - $readpost_expiry))) {
	$userid=$CURUSER["id"];
	if ($lpr)
	mysql_query("UPDATE readposts SET lastpostread=".sqlesc($postid)." WHERE userid=".sqlesc($userid)." AND topicid=".sqlesc($topicid)."") or sqlerr(__FILE__, __LINE__);
	else
	mysql_query("INSERT INTO readposts (userid, topicid, lastpostread) ".
	"VALUES(".sqlesc($userid).", ".sqlesc($topicid).", ".sqlesc($postid).")") or sqlerr(__FILE__, __LINE__);
	}
//	if ($postid > $lpr && !empty($lpr))
//	sql_query("UPDATE readposts SET lastpostread=".sqlesc($postid)." WHERE userid=".sqlesc($userid)." AND topicid=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
	
	}
	
	
	++$num;
}
echo "<div class=\"tcat_b\"><div class=\"tcat_bl\"><div class=\"tcat_br\"></div></div></div><br />$pagerbottom<br />";

 $maxsubjectlength=(int)$maxsubjectlength;
   
  if ($CURUSER["forum_com"]=="0000-00-00 00:00:00" && $CURUSER){
  	
  echo "<br>
<div class=\"tcat_t\"><div class=\"tcat_r\"><div class=\"tcat_l\"><div class=\"tcat_tl\"><div class=\"tcat_simple\">
<a style=\"float:right\" href=\"#top\" onclick=\"return toggle_collapse('forumbit_5')\">
<img id=\"collapseimg_forumbit_5\" src=\"nulled_v4/buttons/collapse_tcat.gif\" alt=\"\" class=\"collapse\" />
</a>

<div align=\"center\"><a name=comments></a><b>.::: Добавить сообщение  к теме :::.</b></div>

<br class=\"tcat_clear\" />
</div></div></div></div></div>
<div class=\"post_body\" id=\"collapseobj_forumbit_5\" align=\"center\" style=\"\">
<table cellspacing=\"0\" cellpadding=\"0\" class=\"forums\">";



  echo("<div align=\"center\"><form name=\"comment\" method=\"post\" action=\"".$BASEURL."/forums.php?action=post\"></div>");
  
  echo("<center><table border=\"0\"><tr><td class=\"clear\">");
  echo("<div align=\"center\">". textbbcode("comment","body","", 1) ."</div>");
 
  echo("</td></tr><tr><td  align=\"center\" class=\"a\" colspan=\"2\">");
  echo("<input type=\"hidden\" value=\"".$topicid."\" name=\"topicid\"/>");
  echo("<input type=\"submit\" class=btn value=\"Разместить сообщение\" />");
  echo("</form>");
  
   echo "</table></div>
<div class=\"off\"><div class=\"tcat_b\"><div class=\"tcat_bl\"><div class=\"tcat_br\"></div></div></div>
</div><br>";
  }



insert_quick_jump_menu($forumid,$CURUSER);

 stdfoot_f(); 
    die;
       }
      exit();
      break;
      
    case 'reply':
    case 'quotepost':
    
     /// require_once "forums/forum_reply.php";
     {

    if ($CURUSER["forum_com"]<>"0000-00-00 00:00:00"){
   	
    	if ($CURUSER){
     header("Refresh: 15; url=".$BASEURL."/forums.php");
		stderr_f("Успешно автопереход через 15 сек", "Вам запрещенно писать или создавать или цитировать любое сообщение на форуме до: ".$CURUSER["forum_com"].". В доступе отказано.");
		die;
		}		else		{
		stderr_f("Ошибка данных", "Вам запрещенно писать или создавать или цитировать любое сообщение на форуме пока не авторизовались. В доступе отказано.");
		die;	
		}
      }


if ($action == "reply")  {
    $topicid = (int)$_GET["topicid"];

    if (!is_valid_id($topicid))    
      header("Location: ".$BASEURL."/forums.php");

    stdhead_f("Создание ответа");
    begin_main_frame();
    insert_compose_frame($topicid, false);
    end_main_frame();
    stdfoot_f();
    die;
}

if ($action == "quotepost"){
$topicid = (int)$_GET["topicid"];

if (!is_valid_id($topicid))
header("Location: ".$BASEURL."/forums.php");
   
    stdhead_f("Создание Ответа");
//   begin_main_frame();
    insert_compose_frame($topicid, false, true);
 //   end_main_frame();
    stdfoot_f();
    die;
}
     	///////////////
     }
      exit();
      break;
      
    case 'post': {

    if ($CURUSER["forum_com"]<>"0000-00-00 00:00:00"){
   	
    	if ($CURUSER){
     header("Refresh: 15; url=".$BASEURL."/forums.php");
		stderr_f("Успешно автопереход через 15 сек", "Вам запрещенно писать или создавать или цитировать любое сообщение на форуме до: ".$CURUSER["forum_com"].". В доступе отказано.");
		die;
		}		else		{
		stderr_f("Ошибка данных", "Вам запрещенно писать или создавать или цитировать любое сообщение на форуме пока не авторизовались. В доступе отказано.");
		die;	
		}
      }

if (empty($CURUSER["downloaded"]) && $CURUSER["uploaded"]<="10737418240" && ($CURUSER["class"]<UC_VIP && $CURUSER)){
//выжидаем 20 минут
     
if (!stristr($CURUSER["usercomment"],"попытка два на форуме")!==false){
$usercomment = get_date_time() . " - возможно спамер (попытка два на форуме).\n". $CURUSER["usercomment"];
mysql_query("UPDATE users SET usercomment=".sqlesc($usercomment)." WHERE id=".$CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
}

     stderr_f("Антиспам предупреждение", "В доступе отказано. Вы зарегистрировались недавно -> ни качали, ни отдавали, ни сидировали ничего, пожалуйста повторите попытку позже.");
     die;
}

    $forumid = isset($_POST["forumid"]) ? (int)$_POST["forumid"] : 0;
    $topicid = isset($_POST["topicid"]) ? (int)$_POST["topicid"] : 0;

    if (!is_valid_id($forumid) && !is_valid_id($topicid))
      stderr_f("Ошибка", "Неверный id.");

      
    $newtopic = $forumid > 0;    

    if ($newtopic) {
      $subject = htmlspecialchars(strip_tags($_POST["subject"]));

      if (!$subject)
        stderr_f("Ошибка", "Вы не ввели темы сообщения.");

      if (strlen($subject) > $maxsubjectlength)
        stderr_f("Ошибка", "Тема сообщения имеет лимит - ".$maxsubjectlength." символов.");
    }
    else
      $forumid = get_topic_forum($topicid) or die("Нет такой категории");


    $arr = get_forum_access_levels($forumid) or die("Нет такой категории");

    if (get_user_class() < $arr["write"] || ($newtopic && get_user_class() < $arr["create"]))
      stderr_f("Ошибка", "В доступе отказанно.");

/*
if ($arr["minclassread"]>$CURUSER["class"] && !empty($arr["minclassread"])){
stderr_f("Ошибка прав","Данная категория и ее сообщения недоступны к показу.");
die;
}
*/

    $body = htmlspecialchars($_POST["body"]);
    if (empty($body))
    stderr_f("Ошибка", "Не ввели сообщение.");
  //  $body = sqlesc($body);  


////////////////////// пытаюсь подсчитать кол ссылок и БАН спамеру (жесткое правило фильтрации) 
@preg_match_all("/\[url=(http:\/\/[^()<>\s]+?)\]((\s|.)+?)\[\/url\]/i",$body,$s1);

$numlinksin = count($s1[0]); /// подсчет ссылок
$numlinksout = count(array_unique($s1[0])); /// тот же подсчет но без дубликатов

if ($numlinksin<>$numlinksout && $numlinksin>=2 && stristr($CURUSER["usercomment"],"спамер")!==false) { /// находим разницу

$modcomment = get_date_time() . " - Отключен по причине спамер (ВС $numlinksin, реклама $numlinksout ".($numlinksout==1 ? "сайта":"сайтов").").\n". $CURUSER["usercomment"];

$forumbanutil = get_date_time(gmtime() + 2 * 604800); 
$forum_dur = "2" . " недели" . ($warnfban > 1 ? "и" : "ю");
$modcomment = gmdate("Y-m-d") . " - Форум бан на $forum_dur от SYSTEM (антиспам).\n". $modcomment; 
$modcomment = sqlesc($modcomment);

sql_query("UPDATE users SET usercomment=CONCAT_WS('', $modcomment, usercomment),forum_com=".sqlesc($forumbanutil)." WHERE id=".$CURUSER["id"]) or sqlerr(__FILE__, __LINE__);

$DEFURL = htmlspecialchars_uni($_SERVER['HTTP_HOST']);

$subj = "Сработало правило спама на форуме";
$all = "Пытался написать сообщение на форуме: http://$DEFURL/userdetails.php?id=$CURUSER[id]  - $CURUSER[username] ($CURUSER[email])\n
Истории пользователя: $CURUSER[usercomment] $CURUSER[modcomment]\n
Сообщение: (ВС $numlinksin, реклама $numlinksout ".($numlinksout==1 ? "сайта":"сайтов").")\n
/////////////////////////////////////////////////
$body
/////////////////////////////////////////////////";

if (!empty($war_email))
@sent_mail($war_email,$SITENAME,$SITEEMAIL,$subj,$all,false);	

stderr_f("Антиспам предупреждение", "Сработало правило антиспама, вы забанены на форуме. <br>Если считаете это ошибкой, пожалуйста напишите администрации сайта.");
}
////////////////////// пытаюсь подсчитать кол ссылок и БАН спамеру (жесткое правило фильтрации) 
        
    $userid = $CURUSER["id"];

    
    if ($newtopic) {
    $subject = sqlesc($subject);

      sql_query("INSERT INTO topics (userid, forumid, subject) VALUES($userid, $forumid, $subject)") or sqlerr(__FILE__, __LINE__);

      $topicid = mysql_insert_id() or stderr_f("Ошибка", "Нет такого сообщения");
    
    }
    else
    {
      $res = sql_query("SELECT * FROM topics WHERE id=".sqlesc($topicid)."") or sqlerr(__FILE__, __LINE__);
      $arr = mysql_fetch_assoc($res) or die("Topic id n/a");

      if ($arr["locked"] == 'yes' && get_user_class() < UC_MODERATOR)
        stderr_f("Ошибка", "Эта тема заблокированна.");

      $forumid = $arr["forumid"];
    }

    $added = get_date_time();
    $body = sqlesc($body);

    sql_query("INSERT INTO posts (topicid, forumid, userid, added, body) " .
    "VALUES(".$topicid.", ".$forumid.",".$userid.", ".sqlesc($added).", ".$body.")") or sqlerr(__FILE__, __LINE__);

    $postid = mysql_insert_id() or die("Post id n/a");

    update_topic_last_post($topicid,$added);
      unlinks();

    $headerstr = "Location: ".$BASEURL."/forums.php?action=viewtopic&topicid=".$topicid."&page=last#".$postid;
    if (!empty($newtopic)) {
      	
  header("Refresh: 5; url=".$BASEURL."/forums.php?action=viewtopic&topicid=".$topicid."&page=last");
		stderr_f("Успешно автопереход через 5 сек", "Сообщение добавлено, сейчас будет переадресация к последнему сообщению.<br> Нажмите <a href=\"".$BASEURL."/forums.php?action=viewtopic&topicid=".$topicid."&page=last\">ЗДЕСЬ</a>, если не хотите ждать.");
	 die("newtopic");
}
  else 
       header("Refresh: 5; url=".$BASEURL."/forums.php?action=viewtopic&topicid=".$topicid."&page=last#".$postid."");
		stderr_f("Успешно автопереход через 5 сек", "Сообщение было добавленно. Время добавления: ".($added)."<br> Нажмите <a href=\"".$BASEURL."/forums.php?action=viewtopic&topicid=".$topicid."&page=last#".$postid."\">ЗДЕСЬ</a>, если не хотите ждать.");
		die("catch_up");
}
      exit();
      break;



    case 'newtopic': {
		
///////////////forum_new_topic///////////////
  
    $forumid = (int)$_GET["forumid"];

    if (!is_valid_id($forumid))
      header("Location: ".$BASEURL."/forums.php");

    if ($CURUSER["forum_com"]<>"0000-00-00 00:00:00"){
   	
    	if ($CURUSER){
     header("Refresh: 15; url=".$BASEURL."/forums.php");
		stderr_f("Успешно автопереход через 15 сек", "Вам запрещенно писать или создавать или цитировать любое сообщение на форуме до: ".$CURUSER["forum_com"].". В доступе отказано.");
		die;
		}		else		{
		stderr_f("Ошибка данных", "Вам запрещенно писать или создавать или цитировать любое сообщение на форуме пока не авторизовались. В доступе отказано.");
		die;	
		}
      }
  
	stdhead_f("Новая тема");
    begin_main_frame();
    insert_compose_frame($forumid);
    end_main_frame();
    stdfoot_f();
    die;
///////////////forum_new_topic///////////////
}
        
      
      exit();
      break;
      

case 'search_post':  {
///////////////поиск постов///////////////
  
    $userid = (int)$_GET["userid"];

   if (!is_valid_id($userid))
     header("Location: ".$BASEURL."/forums.php");
     
/*
    if ($CURUSER["forum_com"]<>"0000-00-00 00:00:00"){
   	
    	if ($CURUSER){
     header("Refresh: 15; url=".$BASEURL."/forums.php");
		stderr_f("Успешно автопереход через 15 сек", "Вам запрещенно писать или создавать или цитировать любое сообщение на форуме до: ".$CURUSER["forum_com"].". В доступе отказано.");
		die;
		}		else		{
		stderr_f("Ошибка данных", "Вам запрещенно писать или создавать или цитировать любое сообщение на форуме пока не авторизовались. В доступе отказано.");
		die;	
		}
      }
      */

	stdhead_f("Поиск сообщений");

   $res = sql_query("SELECT COUNT(*) AS count, u.username FROM posts 
   LEFT JOIN users AS u ON u.id=userid
   WHERE userid=".sqlesc($userid)." GROUP BY userid") or sqlerr(__FILE__, __LINE__);
   $arr = mysql_fetch_assoc($res);
   $sear=$arr["username"];

	$perpage = 25;
    $count  = $arr["count"];  

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "forums.php?action=search_post&userid=".$userid."&");


$res = sql_query("SELECT p. *, u.ip, u.signatrue,u.forum_com, u.signature,u.avatar, e.username AS ed_username,e.class AS ed_class,f.name AS for_name,f.description,t.subject
 FROM posts p 
 LEFT JOIN forums AS f ON f.id=p.forumid
 LEFT JOIN topics AS t ON t.id=p.topicid
 LEFT JOIN users u ON u.id = p.userid 
 LEFT JOIN users e ON e.id = p.editedby
 WHERE p.userid=".sqlesc($userid)." ORDER BY p.id $limit") or sqlerr(__FILE__, __LINE__);
      

    print("<table border='1' cellspacing='0' cellpadding='8' width='100%'>
	<tr><td class=colhead><a title=\"Перейти к главным категориям форума\"class=\"altlink_white\" href=".$BASEURL."/forums.php>Главная страничка форума</a> </td></tr></table>\n");
 
 begin_frame("Поиск всех сообщений (".$count.") ".(empty($count) ?"":" у пользователя ".$sear)."", true);

if (!empty($count)){
   echo $pagertop;
   }
  begin_table();
   
    $num=1;
    
    while ($arr = mysql_fetch_assoc($res))  {

      ++$pn;
      $ed_username = $arr["ed_username"];
      $ed_class = $arr["ed_class"];
        $postid = $arr["id"];
       $forum_name= "<a title=\"".(format_comment($arr["description"]))."\" href=\"".$BASEURL."/forums.php?action=viewforum&forumid=".($arr["forumid"])."\">".$arr["for_name"]."</a>";
        
		$topic_name="<a href=\"".$BASEURL."/forums.php?action=viewtopic&topicid=".$arr["topicid"]."#$postid\">".$arr["subject"]."</a>";
      
    
      $posterid = $arr["userid"];
      $added = ($arr['added']);


if ($arr["forum_com"]<>"0000-00-00 00:00:00" && !empty($postername)){
$ban=": <b>Бан до </b>".$arr["forum_com"]."";
}

//if (get_user_class() > UC_MODERATOR){
$numb_view="<a title=\"Число, под которым это сообщение в базе: ".$postid."\" href='".$BASEURL."/forums.php?action=viewpost&id=".$postid."'>Постоянная ссылка для этого сообщения [<b>$postid</b>]</a>";	
//}

if (!empty($arr["avatar"])){
$avatar = ($CURUSER["id"]==$posterid ? "<a href=\"".$BASEURL."/my.php\"><img alt=\"Аватар, по клику переход в настройки\" title=\"Аватар, по клику переход в настройки\" width=100 height=100 src=\"".$BASEURL."/pic/avatar/".$arr["avatar"]."\"/></a>":"<img width=100 height=100 src=\"".$BASEURL."/pic/avatar/".$arr["avatar"]."\"/>");
} else
$avatar = "<img width=100 src=\"".$BASEURL."/pic/avatar/default_avatar.gif\"/>";


echo("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr>");
echo("	  
<td width=\"100\" align=\"center\" class=\"a\"><b>#</b>".$num.$numb_view."
<td class=\"a\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
<tr>
<td class=\"a\" width=\"300\"  align=\"left\">
".$topic_name."
</td>
<td class=\"a\" width=\"300\"  align=\"rigth\">
".$forum_name."
</td>
</table>
<td class=b align=center width=2%><a class=\"altlink_white\" href=#top><img src=\"pic/forumicons/top.gif\" border=0 alt='Наверх'></a></td>
</td></td>
</tr>");

echo("</p>\n");

   
$body = format_comment($arr["body"]);

if (is_valid_id($arr['editedby']) AND $arr['editedat']<>0 AND $arr['editedby']<>0 AND get_user_class() >= UC_MODERATOR || $CURUSER["id"] == $posterid)      {
$body .= "<p align=right><font size=1 class=small>Последний раз редактировалось <a href=userdetails.php?id={$arr['editedby']}><b> ".get_user_class_color($ed_class, $ed_username)." </b></a> в ".($arr['editedat'])."</font></p>\n";
unset($ed_class);
unset($ed_username);
}

echo("<tr valign=top><td width=100 align=left>" .$avatar. "</td><td>".$body."</td><td class=a></td></tr>\n");
		
if (($CURUSER["forum_com"]=="0000-00-00 00:00:00"  && $CURUSER["id"]==$posterid && !$locked) || get_user_class() >= UC_MODERATOR){
$edit=("".($posterid<>0 && !empty($postername) ? " -":"")." [<a href=forums.php?action=editpost&postid=".$postid."><b>Редактировать</b></a>]");
}


if (get_user_class() >= UC_MODERATOR){
$delet=" - [<a href=".$BASEURL."/forums.php?action=deletepost&postid=".$postid."><b>Удалить</b></a>]</td></tr>";

$found=" ".($arr["ip"] ? "[<a title=\"Искать этот ip адресс в базе данных через административный поиск\" href=\"".$BASEURL."/usersearch.php?ip=".$arr["ip"]."\" ><b>".$arr["ip"]."</b></a>] -":"")."";
}

$citat=($posterid<>0 && !empty($postername) && $CURUSER["forum_com"]=="0000-00-00 00:00:00" ? " [<a href=".$BASEURL."/forums.php?action=quotepost&topicid=".$topicid."&postid=".$postid."><b>Цитировать</b></a>]":"");	

if (!$locked || get_user_class() >= UC_MODERATOR){

if (get_user_class() >= UC_MODERATOR){
$strlen="<td class=\"a\" align=\"left\" width=\"20\"><a title=\"Размер данного сообщения\">[".strlen($arr["body"])."]</a></td>";}
}

			
echo "<td width=\"100\" align=\"center\" class=\"a\">".$added."
<td class=\"a\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
<tr>
".$strlen."
<td class=\"a\" nowrap=\"\" width=\"2%\" align=\"right\">
".$found.$citat.$edit.$delet."
</td>


</tr></table></td>
<td class=b align=center width=2%><a class=\"altlink_white\" href=#top><img src=\"".$BASEURL."/pic/forumicons/top.gif\" border=0 alt='Наверх'></a></td>
</td>";

unset($avatar);
unset($edit);
unset($delet);
unset($found);
unset($strlen);
unset($citat);
}
        
if (empty($count)){
echo "<table border=0 cellspacing=0 cellpadding=0><tr>    	  
У этого пользователя сообщений на форуме нет!
</tr>";
}
  end_table();
  end_frame();
  echo $pagerbottom; 
    stdfoot_f();
    die;   
///////////////поиск постов///////////////

}
      exit();
      break;
    
//////////////////////
    
    case 'editpoll': {
///////////////forum_new_topic///////////////
  //?action=editpoll&topics=".$arr["topicid"]."\">
	$pnew = $_POST["new"];
	$topics=(int)$_GET["topics"];
	
    $postid=(int)$_POST["postid"];
	
//	die($topicid);
	if (!is_valid_id($topics))
		stderr_f($tracker_lang['error'],$tracker_lang['invalid_id']);
		
//	$res = sql_query("SELECT COUNT(*) FROM polls WHERE forum=".$topics."")	or sqlerr(__FILE__, __LINE__);
//	if (mysql_num_rows($res) == 0)
//	stderr_f($tracker_lang['error'],"Нет опроса с таким ID.");

	$res = sql_query("SELECT * FROM topics WHERE id=".sqlesc($topics))	or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) == 0)
	stderr_f($tracker_lang['error'],"Нет темы с таким ID.");
	$arrpoll = mysql_fetch_assoc($res);
	
 	if ($CURUSER["id"] <> $arrpoll["userid"] && get_user_class() < UC_MODERATOR)
 	{
 	stderr_f($tracker_lang['error'],"Вы не владелец этой темы.");
 	}

	
  $question = htmlspecialchars($_POST["question"]);
  $option0 = htmlspecialchars($_POST["option0"]);
  $option1 = htmlspecialchars($_POST["option1"]);
  $option2 = htmlspecialchars($_POST["option2"]);
  $option3 = htmlspecialchars($_POST["option3"]);
  $option4 = htmlspecialchars($_POST["option4"]);
  $option5 = htmlspecialchars($_POST["option5"]);
  $option6 = htmlspecialchars($_POST["option6"]);
  $option7 = htmlspecialchars($_POST["option7"]);
  $option8 = htmlspecialchars($_POST["option8"]);
  $option9 = htmlspecialchars($_POST["option9"]);
  $option10 = htmlspecialchars($_POST["option10"]);
  $option11 = htmlspecialchars($_POST["option11"]);
  $option12 = htmlspecialchars($_POST["option12"]);
  $option13 = htmlspecialchars($_POST["option13"]);
  $option14 = htmlspecialchars($_POST["option14"]);
  $option15 = htmlspecialchars($_POST["option15"]);
  $option16 = htmlspecialchars($_POST["option16"]);
  $option17 = htmlspecialchars($_POST["option17"]);
  $option18 = htmlspecialchars($_POST["option18"]);
  $option19 = htmlspecialchars($_POST["option19"]);
  
	 
  if (!$question || !$option0 || !$option1)
    stderr_f($tracker_lang['error'], "Заполните все поля формы!");

  if ($_POST["ready"]=="yes"){
		sql_query("UPDATE polls SET " .		
		"editby = " . sqlesc($CURUSER[id]) . ", " .
		"edittime = " . sqlesc(get_date_time()) . ", " .
		"question = " . sqlesc($question) . ", " .
		"option0 = " . sqlesc($option0) . ", " .
		"option1 = " . sqlesc($option1) . ", " .
		"option2 = " . sqlesc($option2) . ", " .
		"option3 = " . sqlesc($option3) . ", " .
		"option4 = " . sqlesc($option4) . ", " .
		"option5 = " . sqlesc($option5) . ", " .
		"option6 = " . sqlesc($option6) . ", " .
		"option7 = " . sqlesc($option7) . ", " .
		"option8 = " . sqlesc($option8) . ", " .
		"option9 = " . sqlesc($option9) . ", " .
		"option10 = " . sqlesc($option10) . ", " .
		"option11 = " . sqlesc($option11) . ", " .
		"option12 = " . sqlesc($option12) . ", " .
		"option13 = " . sqlesc($option13) . ", " .
		"option14 = " . sqlesc($option14) . ", " .
		"option15 = " . sqlesc($option15) . ", " .
		"option16 = " . sqlesc($option16) . ", " .
		"option17 = " . sqlesc($option17) . ", " .
		"option18 = " . sqlesc($option18) . ", " .
		"option19 = " . sqlesc($option19) . ", " .
		"sort = " . sqlesc() . ", " .
		"comment = " . sqlesc() . " " .
    "WHERE forum=".sqlesc($topics)."") or sqlerr(__FILE__, __LINE__);
    @unlink(ROOT_PATH."cache/forums_ptop-".$topics.".txt");
    
       	sql_query("UPDATE topics SET polls='yes' WHERE id=".sqlesc($topics)) or sqlerr(__FILE__, __LINE__);
		}
		
  if ($pnew=="yes"){
  	
  	  	
  	sql_query("INSERT INTO polls VALUES(0" .
  	", " . sqlesc("") .
  	", " . sqlesc("") .
  	", " . sqlesc("$CURUSER[id]") .
  	", '" . get_date_time() . "'" .
	", " . sqlesc($question) .
    ", " . sqlesc($option0) .
    ", " . sqlesc($option1) .
    ", " . sqlesc($option2) .
    ", " . sqlesc($option3) .
    ", " . sqlesc($option4) .
    ", " . sqlesc($option5) .
    ", " . sqlesc($option6) .
    ", " . sqlesc($option7) .
    ", " . sqlesc($option8) .
    ", " . sqlesc($option9) .
 	", " . sqlesc($option10) .
	", " . sqlesc($option11) .
	", " . sqlesc($option12) .
	", " . sqlesc($option13) .
	", " . sqlesc($option14) .
	", " . sqlesc($option15) .
	", " . sqlesc($option16) .
	", " . sqlesc($option17) .
	", " . sqlesc($option18) .
	", " . sqlesc($option19) . 
	", " . sqlesc("") . 
    ", " . sqlesc("") .
     ", " . sqlesc($topics) .
  	")") or sqlerr(__FILE__, __LINE__);
  	@unlink(ROOT_PATH."cache/forums_ptop-".$topics.".txt");
  	sql_query("UPDATE topics SET polls='yes' WHERE id=".sqlesc($topics)) or sqlerr(__FILE__, __LINE__);
}

header("Location: ".$DEFAULTBASEURL."/forums.php?action=editpost&postid=".$postid);
die;      
}

exit();
break;

//////////////////////
    case 'deletepost':
    case 'editpost':
    case 'deletetopic':
    case 'editpostmod';
    case 'edittopicmod';  
	{
    if ($CURUSER["forum_com"]<>"0000-00-00 00:00:00"){
   	
    	if ($CURUSER){
     header("Refresh: 15; url=".$BASEURL."/forums.php");
		stderr_f("Успешно автопереход через 15 сек", "Вам запрещенно писать или создавать или цитировать любое сообщение на форуме до: ".$CURUSER["forum_com"].". В доступе отказано.");
		die;
		}		else		{
		stderr_f("Ошибка данных", "Вам запрещенно писать или создавать или цитировать любое сообщение на форуме пока не авторизовались. В доступе отказано.");
		die;	
		}
      }
    	

  if ($action == "edittopicmod" && get_user_class() >= UC_MODERATOR)
  {
    $topicid = (int)$_GET["topicid"]; /// какое сообщение

    if (!is_valid_id($topicid))
      die("Не число в edittopicmod");

    //$res = mysql_query("SELECT p.*, f.name AS name_forum, t.t_com,t.forumid FROM posts AS p    LEFT JOIN topics AS t ON t.id=p.topicid	LEFT JOIN forums AS f ON f.id=t.forumid	WHERE p.id=$postid") or sqlerr(__FILE__, __LINE__);

 $res = sql_query("SELECT t.* FROM topics AS t WHERE t.id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res) == 0)
		stderr_f("Ошибка", "Нет темы с таким id $topicid.");

	$arr = mysql_fetch_assoc($res);

 $delete_topic = (int)$_POST['delete_topic'];
 $reson_topic = htmlspecialchars($_POST['reson']);
  
  if ($delete_topic==1) {
  	if (empty($reson_topic))
  	{
  	stderr_f("Ошибка удаления", "Вы не указали причину удаления этой темы.");
  	}
  	else
  	{
  	   $r13 = sql_query("SELECT f_com FROM forums WHERE id=".sqlesc($arr["forumid"])."") or sqlerr(__FILE__, __LINE__);
      $ro13 = mysql_fetch_assoc($r13);

   $mod=date("Y-m-d") . " - $CURUSER[username] удалил тему ".(htmlspecialchars($arr["subject"]))." (".($arr["id"]).") по причине: $reson_topic.\n". $ro13["f_com"];

   mysql_query("UPDATE forums SET f_com=".sqlesc($mod)." WHERE id=".sqlesc($arr["forumid"])."") or sqlerr(__FILE__, __LINE__);
  	
  	mysql_query("DELETE FROM topics WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
	mysql_query("DELETE FROM posts WHERE topicid=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
	mysql_query("DELETE FROM polls WHERE forum=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
	mysql_query("DELETE FROM pollanswers WHERE forum=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
  	//// удаляем посты и тему и опросы и ответы на опросы
  	unlinks(); /// очищаем кеш
	
	    header("Refresh: 5; url=".$BASEURL."/forums.php?action=viewforum&forumid=".$arr["forumid"]."");
		stderr_f("Успешно автопереход через 5 сек", "Удаленна тема с ее сообщениями.<br> Нажмите <a href=\"".$BASEURL."/forums.php?action=viewforum&forumid=".$arr["forumid"]."\">ЗДЕСЬ</a>, если не хотите ждать.");
	 die("edittopicmod");
  	}
  	
  	
  }
 
	if (get_user_class() == UC_SYSOP){
	$modi = htmlspecialchars($_POST["modcomment"]);
    }   else    $modi=$arr["t_com"]; /// оригинал комментария категории форума

   $subject = htmlspecialchars($_POST['subject']);
   if ($subject<>$arr["subject"])   {
   $updateset[] = "subject=".sqlesc($subject)."";
   $modi=date("Y-m-d") . " - ".$CURUSER["username"]." поменял название темы с (".htmlspecialchars($arr["subject"]).") на (".$subject.").\n". $modi;
   }
   
   $locked=$_POST["locked"];
   $lock_arr=$arr["locked"];
   
   if ($locked=="yes" && $lock_arr=="no")  {
   	$updateset[] = "locked = 'yes'";
   	$modi=date("Y-m-d") . " - ".$CURUSER["username"]." заблокировал тему.\n". $modi;
   }
   if ($locked=="no" && $lock_arr=="yes")   {
   	$updateset[] = "locked = 'no'";
   	$modi=date("Y-m-d") . " - ".$CURUSER["username"]." разблокировал тему.\n". $modi;
   }
   



   if ($_POST["visible"]=="no" && $arr["visible"]=="yes")
   {
   	$updateset[] = "visible = 'no'";
   	$modi=date("Y-m-d") . " - $CURUSER[username] скрыл тему.\n". $modi;

   }
   if ($_POST["visible"]=="yes" && $arr["visible"]=="no")
   {
   	$updateset[] = "visible = 'yes'";
   	$modi=date("Y-m-d") . " - $CURUSER[username] вкл показ темы.\n". $modi;
 
   }
  
   $sticky=$_POST["sticky"];
   $sti_arr=$arr["sticky"];
   
    if ($sticky=="yes" && $sti_arr=="no"){
    $updateset[] = "sticky = 'yes'";
        $modi=date("Y-m-d") . " - ".$CURUSER["username"]." прикрепил.\n". $modi;
    }
  if ($sticky=="no" && $sti_arr=="yes"){
    $updateset[] = "sticky = 'no'";
    $modi=date("Y-m-d") . " - ".$CURUSER["username"]." снял важность.\n". $modi;
    }
    
   $forumid=(int) $_POST["forumid"];
   $for_arr=$arr["forumid"];
   if ($forumid<>$for_arr && is_valid_id($forumid) && $forumid<>0){
   	
   $re3 = sql_query("SELECT name FROM forums WHERE id=".sqlesc($forumid)."") or sqlerr(__FILE__, __LINE__);
   $rom = mysql_fetch_assoc($re3);
   
    $ree3 = sql_query("SELECT name FROM forums WHERE id=".sqlesc($for_arr)."") or sqlerr(__FILE__, __LINE__);
   $room = mysql_fetch_assoc($ree3);
   
   $new_f=$rom["name"];
   $now_f=$room["name"];
   if ($new_f){
  $updateset[] = "forumid = ".sqlesc($forumid)."";
  $modi=date("Y-m-d") . " - ".$CURUSER["username"]." переместил тему с (">$now_f.") в (".$new_f.").\n". $modi;
  }
   }
    
 //   $modi=date("Y-m-d") . " - $CURUSER[username] сменил автора сообщения ($postid) $orig_user_post на ".$ro["username"].".\n". $modi;
 
  
    $modcomm=htmlspecialchars($_POST["modcomm"]);
	if (!empty($modcomm))
	$modi = date("Y-m-d") . " - Заметка от ".$CURUSER["username"].": ".$modcomm."\n" . $modi;
	
	$updateset[] = "t_com =".sqlesc($modi)."";	
		
   sql_query("UPDATE topics SET " . implode(", ", $updateset) . " WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
    
   unlinks(); /// очищаем кеш

///$returnto = htmlspecialchars($_POST["returnto"]);
///	$returnto .= "&page=p$postid#$postid";

header("Refresh: 3; url=$BASEURL/forums.php?action=edittopic&topicid=$topicid");
		stderr_f("Успешно", "Обновление без ошибок, автопереход через 3 сек.<br> Нажмите <a href=\"$BASEURL/forums.php?action=edittopic&topicid=$topicid\">ЗДЕСЬ</a>, если не хотите ждать.");
	 die("edittopicmod");
 //////////////////////////////
}
  

 if ($action == "editpostmod" && get_user_class() >= UC_MODERATOR)
  {
    $postid = (int)$_GET["postid"]; /// какое сообщение

    if (!is_valid_id($postid))
      die("Не число в editpostmod");

    $res = mysql_query("SELECT p.*, f.name AS name_forum, t.id AS topic_id,t.t_com,t.forumid FROM posts AS p
    LEFT JOIN topics AS t ON t.id=p.topicid
	LEFT JOIN forums AS f ON f.id=t.forumid
	WHERE p.id=".sqlesc($postid)) or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res) ==0)
			stderr_f("Ошибка", "Нет сообщения с таким id.");

	$arr = mysql_fetch_assoc($res);

	if (get_user_class() == UC_SYSOP)
        {
		$modi = htmlspecialchars($_POST["modcomment"]);
        }
    	else
    $modi=$arr["t_com"]; /// оригинал комментария категории форума

    $forumid=$arr["forumid"];///
   $release = ((int) $_POST['release']);
   
   if (!empty($release) && get_user_class() >= UC_ADMINISTRATOR){
   $re = sql_query("SELECT username FROM users WHERE id=".sqlesc($release)."") or sqlerr(__FILE__, __LINE__);
   $ro = mysql_fetch_assoc($re); 
   
   
   $r1 = sql_query("SELECT username FROM users WHERE id=".sqlesc($arr["userid"])."") or sqlerr(__FILE__, __LINE__);
   $re1 = mysql_fetch_assoc($r1); 
   $orig_user_post=$re1["username"];
   
   
   if (!empty($ro["username"]) && $orig_user_post<>$ro["username"]){
   	
   	///// вычисляем сколько сообщений если одно значить тема обновляет в topics - последн автора=автор оригинальный
    $one_ans = mysql_query("SELECT COUNT(*) FROM posts WHERE topicid=".sqlesc($arr["topicid"])."") or sqlerr(__FILE__, __LINE__);
    $one = mysql_fetch_row($one_ans);
    $topicidi = $one[0];
/// die("$topicid");
    if ($topicidi<=1){
///	die($arr["topic_id"]);
     mysql_query("UPDATE topics SET userid=".sqlesc($release)." WHERE id=".sqlesc($arr["topic_id"])."") or sqlerr(__FILE__, __LINE__);	
    }
	/////////

    mysql_query("UPDATE posts SET userid=".sqlesc($release).", forumid=".sqlesc($forumid)." WHERE id=".sqlesc($postid)."") or sqlerr(__FILE__, __LINE__);
    
    $modi=date("Y-m-d") . " - $CURUSER[username] сменил автора сообщения ($postid) $orig_user_post на ".$ro["username"].".\n". $modi;
    
   }
   }
   
  $set_system = (int)$_POST["set_system"];
  if ($set_system==1 && $arr["userid"]<>0 && get_user_class() >= UC_ADMINISTRATOR)
  {
  /// ставим 0 в id как от системы
  mysql_query("UPDATE posts SET userid=0, forumid=".sqlesc($forumid)." WHERE id=$postid") or sqlerr(__FILE__, __LINE__);
  $modi=date("Y-m-d") . " - $CURUSER[username] присвоил автора сообщения ($postid) - System.\n". $modi;
  }
  
    $modcomm=htmlspecialchars($_POST["modcomm"]);
	if (!empty($modcomm))
	$modi = date("Y-m-d") . " - Заметка от $CURUSER[username]: $modcomm\n" . $modi;
		
    mysql_query("UPDATE topics SET t_com=".sqlesc($modi)." WHERE id=".sqlesc($arr["topicid"])."") or sqlerr(__FILE__, __LINE__);
    
  unlinks(); /// очищаем кеш


		///$returnto = htmlspecialchars($_POST["returnto"]);

		
			///	$returnto .= "&page=p$postid#$postid";
			///	header("Location: forums.php?action=editpost&postid=$postid");
		//	}
		//	else
			
			header("Refresh: 3; url=$BASEURL/forums.php?action=editpost&postid=$postid");
		stderr_f("Успешно", "Сообщение было отредактированно, автопереход через 3 сек.<br> Нажмите <a href=\"$BASEURL/forums.php?action=editpost&postid=$postid\">ЗДЕСЬ</a>, если не хотите ждать.");
	 die("editpostmod");
				
			///	stderr_f("Успешно", "Сообщение было отредактированно.");
								// 	die;
    

 //////////////////////////////
  }
  
  
  
  if ($action == "editpost")
  {
    $postid = (int)$_GET["postid"];

    if (!is_valid_id($postid))
      die("Не число в editpost");

    $res = sql_query("SELECT p.*, t.t_com,t.polls,t.forumid, t.subject,  u.username,u.class
	FROM posts AS p
    LEFT JOIN topics AS t ON t.id=p.topicid
	LEFT JOIN users AS u ON u.id=p.userid 	
	WHERE p.id=".sqlesc($postid)) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 0)
	stderr_f("Ошибка", "Нет сообщения с таким id.");

	$arr = mysql_fetch_assoc($res);
    $forumi=$arr["forumid"];
    $res2 = sql_query("SELECT locked FROM topics WHERE id = " . sqlesc($arr["topicid"])) or sqlerr(__FILE__, __LINE__);
	$arr2 = mysql_fetch_assoc($res2);

 	if (mysql_num_rows($res)==0)
	stderr_f("Ошибка", "Нет категории для этого сообщения.");

		$locked = ($arr2["locked"] == 'yes');

    if (($CURUSER["id"] <> $arr["userid"] || $locked) && get_user_class() < UC_MODERATOR)
      stderr_f("Ошибка прав", "Доступ запрещен");

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
    	$body = htmlspecialchars($_POST['body']);

    	if (empty($body))
    	  stderr_f("Ошибка", "Сообщение не может быть пустым!");

if ($body<>$arr["body"]) {

$editedat = get_date_time();

if (empty($arr["body_orig"]))
$updatbody[] = "body_orig = ".sqlesc(htmlspecialchars($arr["body"]));

$updatbody[] = "forumid = ".sqlesc($forumi);
$updatbody[] = "editedby = ".sqlesc($CURUSER["id"]);
$updatbody[] = "editedat = ".sqlesc($editedat);
$updatbody[] = "body = ".sqlesc($body);

sql_query("UPDATE posts SET " . implode(",", $updatbody) . " WHERE id=".sqlesc($postid)) or sqlerr(__FILE__, __LINE__);

//  sql_query("UPDATE posts SET body=".$body.", editedat=".$editedat.", editedby=".sqlesc($CURUSER["id"]).",forumid=".sqlesc($forumi)." WHERE id=".sqlesc($postid)) or sqlerr(__FILE__, __LINE__);

  unlinks();
}
  
		$returnto = ($_POST["returnto"]);

			if (!empty($returnto))
			{
			 $returnto .= "#$postid"; /// переходим этому сообщению сразу
			
			   header("Refresh: 5; url=$returnto");
		stderr_f("Успешно автопереход через 5 сек", "Сообщение было отредактировано. Время обновления помеченно как: ".($editedat).".<br> Нажмите <a href=\"$returnto\">ЗДЕСЬ</a>, если не хотите ждать.");
		die("editpost");
			
			//	header("Location: $returnto");
			}
			else
			header("Refresh: 5; url=forums.php?action=viewforum&forumid=".$arr["forumid"]."#$postid");
		stderr_f("Успешно автопереход через 5 сек", "Сообщение было отредактировано. Время обновления помеченно как: ".($editedat).".<br> Нажмите <a href=\"forums.php?action=viewforum&forumid=".$arr["forumid"]."#$postid\">ЗДЕСЬ</a>, если не хотите ждать.");
		die("editpost");
			///	stderr_f("Успешно", "Сообщение было отредактированно.");
    }

  stdhead_f("Редактирование сообщения");

  print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
  print("<tr><td class=colhead align=\"center\" colspan=\"2\"><a name=comments></a><b>.::: Редактирование комментария :::.</b><br>
  <a href=\"forums.php?action=viewtopic&topicid=".$arr["topicid"]."\">Вернутся к показу темы</a>
  </td></tr>");
  print("<tr><td width=\"100%\" align=\"center\" >");
  print("<form name=\"comment\" method=\"post\" action=\"forums.php?action=editpost&postid=$postid\">");
  print("<center><table border=\"0\"><tr><td class=\"clear\">");
  print("<div align=\"center\">". textbbcode("comment","body",($arr["body"]), 1) ."</div>");
  print("</td></tr></table></center>");
  print("</td></tr><tr><td  align=\"center\" colspan=\"2\">");
  print("<input type=\"hidden\" value=\"$topicid\" name=\"topicid\"/>
  <input type=hidden name=returnto value=\"forums.php?action=viewtopic&topicid=".$arr["topicid"]."\">");
  print("<input type=\"submit\" class=btn value=\"Редактировать сообщение\" />");
 print("</td></tr></table></form><br>");


    $res1 = mysql_query("SELECT id AS first, (SELECT COUNT(*) FROM posts WHERE topicid=".sqlesc($arr["topicid"]).") AS count FROM posts WHERE topicid=".sqlesc($arr["topicid"])." ORDER BY id ASC LIMIT 1") or sqlerr(__FILE__, __LINE__);

    $arr1 = mysql_fetch_assoc($res1);
    
  	
  	if (($arr["first"]==$postid && $CURUSER["id"]==$arr["userid"]) || get_user_class() >= UC_MODERATOR) {
 

	$res = sql_query("SELECT * FROM polls WHERE forum=".sqlesc($arr["topicid"])) or sqlerr(__FILE__, __LINE__);
	//	stderr_f($tracker_lang['error'],"Нет опроса с таким ID.");
	$poll = mysql_fetch_array($res);

if (empty($poll["id"]) && $arr["polls"]=="no"){
print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
print("<tr><td class=colhead align=\"center\" colspan=\"2\"><a name=comments></a><b><center>.::: Создание опроса для данной темы :::.</b></center></td></tr>");
print("<form method=\"post\" action=\"forums.php?action=editpoll&topics=".$arr["topicid"]."\">");
print("<tr><td>");
	
echo"<table border=0 width=\"100%\" cellspacing=0 cellpadding=5>
<tr><td class=rowhead>Вопрос <font color=red>*</font></td><td align=left><input name=question size=80 maxlength=255 value=".htmlspecialchars($poll['question'])."></td></tr>
<tr><td class=rowhead>Вариант 1 <font color=red>*</font></td><td align=left><input name=option0 size=80 maxlength=255 value=".htmlspecialchars($poll['option0'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 2 <font color=red>*</font></td><td align=left><input name=option1 size=80 maxlength=255 value=".htmlspecialchars($poll['option1'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 3</td><td align=left><input name=option2 size=80 maxlength=255 value=".htmlspecialchars($poll['option2'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 4</td><td align=left><input name=option3 size=80 maxlength=255 value=".htmlspecialchars($poll['option3'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 5</td><td align=left><input name=option4 size=80 maxlength=255 value=".htmlspecialchars($poll['option4'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 6</td><td align=left><input name=option5 size=80 maxlength=255 value=".htmlspecialchars($poll['option5'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 7</td><td align=left><input name=option6 size=80 maxlength=255 value=".htmlspecialchars($poll['option6'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 8</td><td align=left><input name=option7 size=80 maxlength=255 value=".htmlspecialchars($poll['option7'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 9</td><td align=left><input name=option8 size=80 maxlength=255 value=".htmlspecialchars($poll['option8'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 10</td><td align=left><input name=option9 size=80 maxlength=255 value=".htmlspecialchars($poll['option9'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 11</td><td align=left><input name=option10 size=80 maxlength=255 value=".htmlspecialchars($poll['option10'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 12</td><td align=left><input name=option11 size=80 maxlength=255 value=".htmlspecialchars($poll['option11'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 13</td><td align=left><input name=option12 size=80 maxlength=255 value=".htmlspecialchars($poll['option12'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 14</td><td align=left><input name=option13 size=80 maxlength=255 value=".htmlspecialchars($poll['option13'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 15</td><td align=left><input name=option14 size=80 maxlength=255 value=".htmlspecialchars($poll['option14'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 16</td><td align=left><input name=option15 size=80 maxlength=255 value=".htmlspecialchars($poll['option15'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 17</td><td align=left><input name=option16 size=80 maxlength=255 value=".htmlspecialchars($poll['option16'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 18</td><td align=left><input name=option17 size=80 maxlength=255 value=".htmlspecialchars($poll['option17'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 19</td><td align=left><input name=option18 size=80 maxlength=255 value=".htmlspecialchars($poll['option18'])."><br /></td></tr>
<tr><td class=rowhead>Вариант 20</td><td align=left><input name=option19 size=80 maxlength=255 value=".htmlspecialchars($poll['option19'])."><br /></td></tr>

<tr><td class=rowhead>Подтверждение создания</td><td>
<input type=radio name=new value=yes>Да
<input type=radio name=new checked value=no> Нет
</td></tr>
</table>";
 
echo("<tr><td align=\"center\" colspan=\"2\">");
echo("<input type=\"hidden\" value=\"".$postid."\" name=\"postid\"/>");
echo("<input type=\"submit\" class=btn value=\"Создать опрос\" />");
echo("</td></tr></table></form><br>");
}
elseif (!empty($poll["id"]) && $arr["polls"]=="yes")	
{
echo("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
echo("<tr><td class=colhead align=\"center\" colspan=\"2\"><a name=comments></a><b><center>.::: Администрирование опроса для данной темы :::.</b></center></td></tr>");
echo("<form method=\"post\" action=\"forums.php?action=editpoll&topics=".$arr["topicid"]."\">");
echo("<tr><td>");
echo"<table border=0 width=\"100%\" cellspacing=0 cellpadding=5>
<tr><td class=rowhead>Вопрос <font color=red>*</font></td><td align=left><input name=question size=80 maxlength=255 value='".$poll['question']."'><br>".$poll['question']."</td></tr>
<tr><td class=rowhead>Вариант 1 <font color=red>*</font></td><td align=left><input name=option0 size=80 maxlength=255 value='".$poll['option0']."'><br />".$poll['option0']."</td></tr>
<tr><td class=rowhead>Вариант 2 <font color=red>*</font></td><td align=left><input name=option1 size=80 maxlength=255 value='".$poll['option1']."'><br />".$poll['option1']."</td></tr>
<tr><td class=rowhead>Вариант 3</td><td align=left><input name=option2 size=80 maxlength=255 value='".$poll['option2']."'><br />".$poll['option2']."</td></tr>
<tr><td class=rowhead>Вариант 4</td><td align=left><input name=option3 size=80 maxlength=255 value='".$poll['option3']."'><br />".$poll['option3']."</td></tr>
<tr><td class=rowhead>Вариант 5</td><td align=left><input name=option4 size=80 maxlength=255 value='".$poll['option4']."'><br />".$poll['option4']."</td></tr>
<tr><td class=rowhead>Вариант 6</td><td align=left><input name=option5 size=80 maxlength=255 value='".$poll['option5']."'><br />".$poll['option5']."</td></tr>
<tr><td class=rowhead>Вариант 7</td><td align=left><input name=option6 size=80 maxlength=255 value='".$poll['option6']."'><br />".$poll['option6']."</td></tr>
<tr><td class=rowhead>Вариант 8</td><td align=left><input name=option7 size=80 maxlength=255 value='".$poll['option7']."'><br />".$poll['option7']."</td></tr>
<tr><td class=rowhead>Вариант 9</td><td align=left><input name=option8 size=80 maxlength=255 value='".$poll['option8']."'><br />".$poll['option8']."</td></tr>
<tr><td class=rowhead>Вариант 10</td><td align=left><input name=option9 size=80 maxlength=255 value='".$poll['option9']."'><br />".$poll['option9']."</td></tr>
<tr><td class=rowhead>Вариант 11</td><td align=left><input name=option10 size=80 maxlength=255 value='".$poll['option10']."'><br />".$poll['option10']."</td></tr>
<tr><td class=rowhead>Вариант 12</td><td align=left><input name=option11 size=80 maxlength=255 value='".$poll['option11']."'><br />".$poll['option11']."</td></tr>
<tr><td class=rowhead>Вариант 13</td><td align=left><input name=option12 size=80 maxlength=255 value='".$poll['option12']."'><br />".$poll['option12']."</td></tr>
<tr><td class=rowhead>Вариант 14</td><td align=left><input name=option13 size=80 maxlength=255 value='".$poll['option13']."'><br />".$poll['option13']."</td></tr>
<tr><td class=rowhead>Вариант 15</td><td align=left><input name=option14 size=80 maxlength=255 value='".$poll['option14']."'><br />".$poll['option14']."</td></tr>
<tr><td class=rowhead>Вариант 16</td><td align=left><input name=option15 size=80 maxlength=255 value='".$poll['option15']."'><br />".$poll['option15']."</td></tr>
<tr><td class=rowhead>Вариант 17</td><td align=left><input name=option16 size=80 maxlength=255 value='".$poll['option16']."'><br />".$poll['option16']."</td></tr>
<tr><td class=rowhead>Вариант 18</td><td align=left><input name=option17 size=80 maxlength=255 value='".$poll['option17']."'><br />".$poll['option17']."</td></tr>
<tr><td class=rowhead>Вариант 19</td><td align=left><input name=option18 size=80 maxlength=255 value='".$poll['option18']."'><br />".$poll['option18']."</td></tr>
<tr><td class=rowhead>Вариант 20</td><td align=left><input name=option19 size=80 maxlength=255 value='".$poll['option19']."'><br />".$poll['option19']."</td></tr>

<tr><td class=rowhead>Подтверждение замены</td><td>
<input type=radio name=ready value=yes>Да
<input type=radio name=ready checked value=no> Нет
</td></tr>
</table>";
 
echo("<tr><td align=\"center\" colspan=\"2\">");
echo("<input type=\"hidden\" value=\"".$postid."\" name=\"postid\"/>");
echo("<input type=\"submit\" class=btn value=\"Редактировать опрос\" />");
echo("</td></tr></table></form><br>");
}
}
  if (get_user_class() >= UC_MODERATOR) {
  	
  $modcomment=htmlspecialchars($arr["t_com"]);
  $forums=htmlspecialchars($arr["subject"]);
  echo("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
  echo("<tr><td class=colhead align=\"center\" colspan=\"2\"><a name=comments></a><b><center>.::: Администрирование сообщения или темы :::.</b></center></td></tr>");
  
  $user_orig="<a href=\"userdetails.php?id=".$arr["userid"]."\">".get_user_class_color($arr["class"], $arr["username"]) . "</a>";

  echo("<form method=\"post\" action=\"forums.php?action=editpostmod&postid=$postid\">");
  echo("<tr><td>");
 
  if (get_user_class() >= UC_ADMINISTRATOR) {
  echo("<tr><td class=\"a\"><b>Сделать это сообщение от system</b>:  <input type=checkbox name=set_system value=1> <i>собщение данное будет от системы</i></td></tr>");
  
  echo("<tr><td class=\"a\"><b>Присвоить автора (ввести новый id)</b>:  <input type=\"text\" size=\"8\" name=\"release\"> <i> прежний [$user_orig] будет заменен новым</i></td></tr>");
   }
   
   echo("<tr><td class=\"a\">История темы <b>$forums</b> и ее прилегающих сообщений [".strlen($modcomment)."]<br>
  <textarea cols=100% rows=6".(get_user_class() < UC_SYSOP ? " readonly" : " name=modcomment").">$modcomment</textarea>
    </td></tr>  
	<tr><td class=\"a\"><b>Добавить заметку</b>: <textarea cols=100% rows=3 name=modcomm></textarea>
    </td></tr>
  ");

  echo("<tr><td align=\"center\" colspan=\"2\">");
  echo("<input type=\"hidden\" value=\"$topicid\" name=\"topicid\"/>");
  echo("<input type=\"submit\" class=btn value=\"Редактировать\" />");
 echo("</td></tr></table></form><br>");
 }
////
    stdfoot_f();
  	die;
  }

  if ($action == "deletepost")
  {
    $postid = (int) $_GET["postid"];

    $sure = (int) $_GET["sure"];

    if (!is_valid_id($postid))
      die("Не число");


    $res = sql_query("SELECT topicid,userid FROM posts WHERE id=".sqlesc($postid)) or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_assoc($res) or stderr_f("Ошибка", "Не найденно сообщение");

    $topicid = $arr["topicid"];
    $userid=$arr["userid"];
    
   if ($userid<>$CURUSER["id"]){
  
   	if (get_user_class() < UC_MODERATOR)	
	   die("Не ваше сообщение");
   	
   }
   

  if ($userid<>$CURUSER["id"] && get_user_class() < UC_MODERATOR)
   die("Нет прав доступа");


    $res = mysql_query("SELECT id AS first, (SELECT COUNT(*) FROM posts WHERE topicid=".sqlesc($topicid).") AS count FROM posts WHERE topicid=".sqlesc($topicid)." ORDER BY id ASC LIMIT 1") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res);

//die($arr["first"]);
if ($arr["count"] < 2 || $arr["first"]==$postid)
stderr_f("Подтверждение", "Данное сообщение связанно с первой темой, удаление его повлечет удалением темы, вы уверены? \n" . "<a href=forums.php?action=deletetopic&topicid=$topicid&sure=1>да, и жму тут</a> для продолжения.\n");


    $res = mysql_query("SELECT id FROM posts WHERE topicid=".sqlesc($topicid)." AND id < ".sqlesc($postid)." ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) == 0)
			$redirtopost = "";
		else
		{
			$arr = mysql_fetch_row($res);
			//$redirtopost = "&page=p$arr[0]#$arr[0]";
		}

    if (!$sure) {
    stderr_f("Удаление", "Если хотите удалить сообщение, Нажмите \n" . "<a href=forums.php?action=deletepost&postid=$postid&sure=1>здесь</a> иначе вернитесь обратно.");
    }

  
  	$res_fo = sql_query("SELECT t.subject,t.forumid,t.t_com, u.username
	FROM topics AS t
   LEFT JOIN forums AS f ON f.id=t.forumid
   LEFT JOIN users AS u ON u.id=".sqlesc($userid)."
   WHERE t.id = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
	$arr_for = mysql_fetch_assoc($res_fo);
   if (!empty($arr_for)){
   $modiki=$arr_for["t_com"]; /// оригинал комментария категории форума	
   $subject=$arr_for["subject"]; 
   if (empty($arr_for["username"]) && $userid<>0) {
   $usera="user: $userid";
   }
   elseif (empty($arr_for["username"]) && $userid==0) {
   $usera="System";
   } else
   $usera=$arr_for["username"];
   
   $modiki = date("Y-m-d") . " - $CURUSER[username] удалил сообщение ($usera).\n" . $modiki;
   sql_query("UPDATE topics SET t_com =".sqlesc($modiki)." WHERE id=".sqlesc($topicid)."") or sqlerr(__FILE__, __LINE__);
    }
  
    sql_query("DELETE FROM posts WHERE id=".sqlesc($postid)) or sqlerr(__FILE__, __LINE__);

     $added = "0";
    update_topic_last_post($topicid,$added,$postid);
    unlinks();
  
  	header("Refresh: 3; url=$BASEURL/forums.php?action=viewtopic&topicid=$topicid");
		stderr_f("Успешно", "Сообщение было удалено, автопереход через 3 сек.<br> Нажмите <a href=\"$BASEURL/forums.php?action=viewtopic&topicid=$topicid\">ЗДЕСЬ</a>, если не хотите ждать.");
	 die("deletepost");
  }

  if ($action == "deletetopic"){
	$topicid = (int)$_GET["topicid"];
	
	if (!is_valid_id($topicid))
		die("не число");

	$sure = ((int)$_GET["sure"]);
	if ($sure <> "1"){
		die("нет подтверждения на удаление");
	}

  	$res_fo = mysql_query("SELECT t.subject,t.forumid,f.f_com,t.userid
	   FROM topics AS t
   LEFT JOIN forums AS f ON f.id=t.forumid
	  WHERE t.id = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
	$arr_for = mysql_fetch_assoc($res_fo);
	
		if ($CURUSER["id"]<>$arr_for["userid"]){
		
		if (get_user_class() < UC_MODERATOR)
		die("Не ваше сообщение для удаления");
	}
	
  	if (!empty($arr_for))
  	{
  		
    ///die($arr_for["f_com"]);
  		
    $modik=$arr_for["f_com"]; /// оригинал комментария категории форума	
   $subject=$arr_for["subject"]; 
 	$modik = date("Y-m-d") . " - $CURUSER[username] удалил тему $subject ($topicid) и сообщения связ с ней.\n" . $modik;
		$forumfid=$arr_for["forumid"];
   mysql_query("UPDATE forums SET f_com =".sqlesc($modik)." WHERE id=".sqlesc($forumfid)."") or sqlerr(__FILE__, __LINE__);
    }
    

	mysql_query("DELETE FROM topics WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
	mysql_query("DELETE FROM posts WHERE topicid=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
	mysql_query("DELETE FROM polls WHERE forum=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
	mysql_query("DELETE FROM pollanswers WHERE forum=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
			
  unlinks();
  
  if ($forumfid){
  	
  	  	header("Refresh: 3; url=$BASEURL/forums.php?action=viewforum&forumid=$forumfid");
		stderr_f("Успешно", "Тема с сообщениями была удаленна, автопереход через 3 сек.<br> Нажмите <a href=\"$BASEURL/forums.php?action=viewforum&forumid=$forumfid\">ЗДЕСЬ</a>, если не хотите ждать.");
	 die("deletepost");
	}
	else
		@header("Location: forums.php");	
	die;
}
	
	
    	
    }
      exit();
      break;
      
    case 'viewunread':
    {

/////////////// orum_view_unread ////////////////////
    $userid = $CURUSER['id'];

    $maxresults = 25;

	$dt = get_date_time(gmtime() - $readpost_expiry);
		
	$res = mysql_query("SELECT st.views, st.id, st.forumid, st.subject, st.lastpost, u.class,u.username, sp.added,sp.userid,sp.id AS lastposti
	FROM topics AS st 
	LEFT JOIN posts AS sp ON st.lastpost = sp.id 
	LEFT JOIN users AS u ON u.id=sp.userid
  	WHERE st.visible='yes' AND sp.added >".sqlesc($dt)." ORDER BY forumid") or sqlerr(__FILE__, __LINE__);

    stdhead_f();

     echo "<table border='0' cellspacing='0' cellpadding='5' width='100%'>
	<tr><td class=colhead align=center>Темы с непрочитанными сообщениями</td></tr></table>";
    $n = 0;

    $uc = get_user_class();

    while ($arr = mysql_fetch_assoc($res))
    {
      $topicid = $arr['id'];
      $lastposti=$arr['lastposti'];
      $forumid = $arr['forumid'];
       $username = $arr['username'];
         $class = $arr['class'];
         $time = ($arr["added"]);
        $use_id= $arr["userid"];
        
        if ($use_id==0 && empty($username))
        {
        	$user_view="<font color=gray>[System]</font>";
        }
        elseif($use_id<>0 && empty($username))
        {
        $user_view="<b>id</b>: $use_id";
        }
        else
        $user_view="<b><a href=\"userdetails.php?id=".$use_id."\">".get_user_class_color($class, $username)."</a></b>";
        
      $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=".sqlesc($userid)." AND topicid=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

      $a = mysql_fetch_row($r);

      if ($a && $a[0] == $arr['lastpost'])
        continue;

      /// $last=$a[0];
      $r = mysql_query("SELECT name, minclassread FROM forums WHERE id=".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);


///p.added, p.topicid, p.userid, u.username,u.class, t.subject 							FROM posts p							LEFT JOIN users u ON p.userid = u.id							LEFT JOIN topics t ON p.topicid = t.id							WHERE p.id = $lastpostid
      $a = mysql_fetch_assoc($r);

      if ($uc < $a['minclassread'])
        continue;

      $n++;

      if ($n > $maxresults)
        break;

      $forumname = $a['name'];

      if ($n == 1)
      {
        echo("<table border=0 cellspacing=0 width=100% cellpadding=5>\n");
        echo("<tr>
		<td class=colhead align=left>Тема</td>
		<td class=colhead align=left>Категория</td>
		</tr>\n");
      }

      echo("<tr><td align=left><table border=0 cellspacing=0 cellpadding=0><tr>
	  <td class=b>" .
      "<img src=\"{$forum_pic_url}unlockednew.gif\" style='margin-right: 5px'></td>
	  <td class=embedded><a href=forums.php?action=viewtopic&topicid=$topicid&page=last#$lastposti><b>" . format_comment($arr["subject"]) .
      "</b></a><br>Последнее сообщение от: $user_view в $time
	  </td>
	  </tr></table></td>
	  <td align=left class=\"a\"><a href=forums.php?action=viewforum&amp;forumid=$forumid><b>$forumname</b></a></td></tr>\n");
    }
    if ($n > 0)
    {
      print("</table>\n");

      if ($n > $maxresults)
        print("<p><b>Найденно больше чем $maxresults результатов, показаны первые из $maxresults из них.</b></p>\n");

    echo "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
	<tr><td class=colhead align=center><a class=\"altlink_white\" href=forums.php?action=catchup><b>Пометить как прочитанные</b></a></td></tr></table>\n";
    }
    else
     
	 echo "<table class=\"main\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td class=\"embedded\"><div align=\"center\" class=\"error\">Ничего не найденно</div></td></tr></table>";

    stdfoot_f();
    die;
/////////////// view_unread ////////////////////
     }
     
      exit();
      break;
      
    case 'search':
    {
    	
    stdhead_f("Поиск на форуме");

///	echo "<table border='1' cellspacing='0' cellpadding='5' width='100%'><tr><td class=colhead align=center><a class=\"altlink_white\" href=forums.php>На главную форума</a></strong> -  Поиск на форуме [в режиме тестирования]</td></tr></table>";
	
	$keywords = (isset($_GET["keywords"]) ? trim(htmlspecialchars(strip_tags($_GET["keywords"]))) : "");
	
//	if(strlen($keywords) > 35)
//	$keywords=substr($keywords,0,35);
	
	if (!empty($keywords))
	{
		$perpage = 50;
		$page = max(1, 0 + isset($_GET["page"]) ? ((int) $_GET["page"]) : 0);
		$ekeywords = sqlesc($keywords);
		echo("<table border='1' cellspacing='0' cellpadding='5' width='100%'>
	<tr><td class=b align=center>Поиск по \"" . htmlspecialchars($keywords) . "\"</b></td></tr></table>
\n");
		$res = sql_query("SELECT COUNT(*) FROM posts WHERE MATCH (body) AGAINST ($ekeywords)") or sqlerr(__FILE__, __LINE__);
		$arr = mysql_fetch_row($res);
		$hits = $arr[0];
		if ($hits == 0){
		//	print("<p><b>Извините, ничего не найденно.</b></p>");
		echo "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
	<tr><td class=b align=center>Извините, ничего не найденно.</td></tr></table>\n";
	  } else {
	
    $count = $hits;
    $perpage=25;

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "forums.php?action=search&keywords=" . htmlspecialchars($keywords) . "&");

$res = sql_query("SELECT id, topicid,userid,added FROM posts WHERE MATCH (body) AGAINST ($ekeywords) $limit") or sqlerr(__FILE__, __LINE__);
			$num = mysql_num_rows($res);
		//	print("<p>$pagemenu1<br>$pagemenu2</p>");
			echo("<table border=1 cellspacing=0 cellpadding=5 width='100%'>\n");
			echo("<tr>
			<td class=colhead>#</td>
			<td class=colhead align=left>Тема</td>
			<td class=colhead align=center>Категория</td>
			<td class=colhead align=right>Автор</td></tr>\n");
			for ($i = 0; $i < $num; ++$i)
			{
				$post = mysql_fetch_assoc($res);
				$res2 = mysql_query("SELECT forumid, subject FROM topics WHERE id=".sqlesc($post["topicid"])) or
					sqlerr(__FILE__, __LINE__);
				$topic = mysql_fetch_assoc($res2);
				$res2 = mysql_query("SELECT name,minclassread,description FROM forums WHERE id=".sqlesc($topic["forumid"])) or sqlerr(__FILE__, __LINE__);
				$forum = mysql_fetch_assoc($res2);
				if ($forum["name"] == "" || $forum["minclassread"] > $CURUSER["class"])
				{
					--$hits;
					continue;
				}
				$res2 = mysql_query("SELECT username,class FROM users WHERE id=$post[userid]") or
					sqlerr(__FILE__, __LINE__);
				$user = mysql_fetch_assoc($res2);
				if (empty($user["username"])) {
					$user = "id: $post[userid]";
				}	else
				$user="<a href=userdetails.php?id=$post[userid]>".get_user_class_color($user["class"], $user["username"])."</a>";

$forumdescription = htmlspecialchars($forum["description"]);


if ($i%2==0){
$cl1 = "class=f_row_on";
$cl2 = "class=f_row_off";
} else {
$cl2 = "class=f_row_on";
$cl1 = "class=f_row_off";
}

echo("<tr>
<td $cl2>$post[id]</td>
<td $cl1 align=left><a title=\"".($post["added"])."\" href=forums.php?action=viewtopic&amp;topicid=$post[topicid]&amp;page=p$post[id]#$post[id]><b>" . format_comment($topic["subject"]) . "</b></a></td>
<td $cl2 align=center><a href=forums.php?action=viewforum&amp;forumid=$topic[forumid]><b>" . htmlspecialchars($forum["name"]) . "</b></a><br><small>$forumdescription</smals></td>
<td $cl1 align=right><b>$user</b><br> в ".($post["added"])."</td></tr>\n");
			}
			echo("</table>\n");

			echo $pagerbottom;
			
		print("<table border='1' cellspacing='0' cellpadding='5' width='100%'>
	<tr><td class=colhead align=center>Найденно $hits сообщени" . ($hits != 1 ? "ия" : "е") . ".</td></tr></table>");
		///	print("<p><b>Поиск снова</b></p>\n");
		}
	}
	echo("<form method=get action=forums.php?>\n");
	echo("<input type=hidden name=action value=search>\n");
	echo("<table border=1 cellspacing=0 cellpadding=5 width='100%'>\n");
	echo("<tr>
	<td class=a><b>Поиск</b></td>
	<td class=a align=left><input type=text size=55 name=keywords value=\"".htmlspecialchars($keywords)."\"><br>\n" .
	"<font class=small size=-1>Введите одно, два слова для поиска. Больше трех слов система проигнорирует.</font></td></tr>\n");
	print("<tr><td align=center colspan=2><input type=submit value='Поиск!!!' class=btn></td></tr>\n");
	print("</table>\n</form>\n");
	stdfoot_f();
	die;
    	
    }
      exit();
      break;
      
      
//////////////////
      
      
   case 'viewpost': {

///	echo "<table border='1' cellspacing='0' cellpadding='5' width='100%'><tr><td class=colhead align=center><a class=\"altlink_white\" href=forums.php>На главную форума</a></strong> -  Поиск на форуме [в режиме тестирования]</td></tr></table>";

$id = (int) $_GET["id"];

if (!is_valid_id($id)){
stderr_f("Неверные данные","$id (id) - не число! Проверьте вводимые данные.");
die;
}

    $res = sql_query("SELECT * FROM topics WHERE id=(SELECT topicid FROM posts WHERE id=".sqlesc($id).")") or sqlerr(__FILE__,__LINE__);
    $arr = mysql_fetch_assoc($res) or stderr_f("Форум ошибка", "Не найдено сообщение");

    $t_com_arr=$arr["t_com"];
    $locked = ($arr["locked"] == 'yes' ? "Да":"Нет");
    $subject = format_comment($arr["subject"]);
    $sticky = ($arr["sticky"] == "yes" ? "Да":"Нет");
    $forumid = $arr["forumid"];
    $topic_polls = $arr["polls"];
    $views=number_format($arr["views"]);
    
    $num_com = number_format(get_row_count("posts", "WHERE topicid=".sqlesc($arr["id"])));

 //   sql_query("UPDATE topics SET views = views + 1 WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

    $res = sql_query("SELECT * FROM forums WHERE id=(SELECT forumid FROM posts WHERE id=".sqlesc($id).")") or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_assoc($res) or die("Нет форума для id: ".$id);
    $forum = htmlspecialchars($arr["name"]);


    if ($CURUSER)
   $cll = $CURUSER["class"];
   else
   $cll = 0;
   
  if ($arr["minclassread"]<=$cll && !empty($arr["minclassread"])){
  stderr_f("Ошибка прав","Данная категория и ее сообщения недоступны к показу.");
  die;
  }

if (get_user_class()>= UC_MODERATOR && isset($_GET["ori"]))
$viworiginal = true;
else
$viworiginal = false;

$res2 = sql_query("SELECT p. *, u.username, u.class, u.last_access, u.ip, u.signatrue,u.forum_com, u.signature,u.avatar, u.title, u.enabled, u.warned, u.hiderating,u.uploaded,u.downloaded,u.donor, e.username AS ed_username,e.class AS ed_class,
(select count(*) FROM posts WHERE userid=p.userid) AS num_topuser
FROM posts p 
LEFT JOIN users u ON u.id = p.userid
LEFT JOIN users e ON e.id = p.editedby
WHERE p.id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

$count = mysql_num_rows($res2);
if (empty($count))
stderr_f("Ошибка данных","Нет сообщения с таким id ($id).");

$arr = mysql_fetch_assoc($res2);
$topicid = $arr["topicid"];

if ($viworiginal == true)
stdhead_f("Просмотр оригинального сообщения (до первого редактирования)");
else
stdhead_f("Просмотр сообщения");

echo "<div class=\"tcat_t\"><div class=\"tcat_r\"><div class=\"tcat_l\">
<div class=\"tcat_tl\"><div class=\"tcat_submenu\"><span class=smallfont>
<div class=\"tcat_popup\"><b>Просмотров</b>: ".$views."</div>
<div class=\"tcat_popup\" id=\"threadtools\"><b>Комментариев</b>: ".$num_com."</div>
<div class=\"tcat_popup\"><b>Важная</b>: ".$sticky."</div>
<div class=\"tcat_popup\" id=\"threadrating\"><b>Заблокирована</b>: ".$locked."</div>

".($viworiginal == true ? "<div class=\"tcat_popup\" id=\"threadrating\"><b>Оригинальное сообщение</b>: Да</div>":"")."

</span>";


echo "<table cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"tcat_name\">Сообщение из темы: <b><a href=\"".$BASEURL."/forums.php?action=viewtopic&topicid=".$topicid."\">".$subject." </a></b>
<br>Категория: <a name=\"poststop\" id=\"poststop\" href=\"".$BASEURL."/forums.php?action=viewforum&forumid=".$forumid."\">".$forum."</a>
".(get_user_class()>= UC_MODERATOR ? "<br><a href='".$BASEURL."/forums.php?action=edittopic&topicid=".$topicid."'>Администрирование темы</a>":"")."
</td></tr>
</table>
<br class=\"tcat_clear\"/></div></div></div></div></div>";


$t_com="<textarea cols=\"115%\" rows=\"5\" readonly>".$t_com_arr."</textarea>";

if (get_user_class()>= UC_MODERATOR)
echo "<div align=\"center\" width=\"100%\"class=\"tcat_t\">
<div class=\"spoiler-wrap\" id=\"115\"><div class=\"spoiler-head folded clickable\">История этого топика (Тестовый режим)</div><div class=\"spoiler-body\" style=\"display: none;\">
$t_com
</div></div>
</div>";

	if ($CURUSER && get_date_time(gmtime() - 60) >= $CURUSER["forum_access"]){
	sql_query("UPDATE users SET forum_access = ".sqlesc(get_date_time())." WHERE id = ".sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);
    }
        
    echo "<div class=\"post_body\"><div id=\"posts\">";

      $ed_username = $arr["ed_username"];
      $ed_class= $arr["ed_class"];
      $postid = $arr["id"];
      $posterid = $arr["userid"];
      $added = ($arr['added']);
      $postername = $arr["username"];
      $posterclass = $arr["class"];

      if (empty($postername) && $posterid<>0)
      {
        $by = "<b>id</b>: $posterid";
      }
      elseif ($posterid==0 && empty($postername)) {
      	$by="<i>Сообщение от </i><font color=gray>[<b>System</b>]</font>";
      }
      else
      {
         $by = "<a href='".$BASEURL."/userdetails.php?id=".$posterid."'><b>" .get_user_class_color($posterclass,  $postername). "</b></a>";
      }
      
  
if ($posterid<>0 && !empty($postername)){
	     if (strtotime($arr["last_access"]) > gmtime() - 600) {
			     	$online = "online";
			     	$online_text = "На форуме";
			     } else {
			     	$online = "offline";
			     	$online_text = "Не на форуме";
			     }
	
		    if ($arr["downloaded"] > 0) {
			    	$ratio = $arr['uploaded'] / $arr['downloaded'];
			    	$ratio = number_format($ratio, 2);
			    } elseif ($arr["uploaded"] > 0) {
			    	$ratio = "Infinity";
			    } else {
			    	$ratio = "---";
			    }

if ($row["hiderating"]=="yes"){
$print_ratio="<b>+100%</b>";
} else
$print_ratio=$ratio;///: $ratio
} else {
unset($print_ratio);
unset($ratio);
unset($online_text);
unset($online);
}


if ($CURUSER["cansendpm"]=='yes' && ($CURUSER["id"]<>$posterid && $posterid<>0 && !empty($postername))){
$cansendpm=" <a href='".$BASEURL."/message.php?action=sendmessage&amp;receiver=".$posterid."'><img src='".$BASEURL."/pic/button_pm.gif' border=0 alt=\"Отправить сообщение\" ></a>";	
}
else {
unset($cansendpm);}


if ($arr["forum_com"]<>"0000-00-00 00:00:00" && !empty($postername)){
$ban="<div><b>Бан до </b>".$arr["forum_com"]."</div>";
} else unset($ban);

//if (get_user_class() >= UC_VIP){
$online_view="<img src=\"".$BASEURL."/pic/button_".$online.".gif\" alt=\"".$online_text."\" title=\"".$online_text."\" style=\"position: relative; top: 2px;\" border=\"0\" height=\"14\">";
//}

//if (get_user_class() > UC_MODERATOR){
$numb_view="<a title=\"Число, под которым это сообщение в базе: ".$postid."\" href='".$BASEURL."/forums.php?action=viewpost&id=".$postid."'>Постоянная ссылка для этого сообщения [<b>$postid</b>]</a>";	
//}

if (!empty($arr["avatar"])){
$avatar = ($CURUSER["id"]==$posterid ? "<a href=\"".$BASEURL."/my.php\"><img alt=\"Аватар, по клику переход в настройки\" title=\"Аватар, по клику переход в настройки\" width=80 height=80 src=\"".$BASEURL."/pic/avatar/".$arr["avatar"]."\"/></a>":"<img width=80 height=80 src=\"".$BASEURL."/pic/avatar/".$arr["avatar"]."\"/>");
} else
$avatar = "<img width=80 height=80 src=\"".$BASEURL."/pic/avatar/default_avatar.gif\"/>";

if ($viworiginal == true)
$body = format_comment($arr["body_orig"]);
else
$body = format_comment($arr["body"]);

echo("<a name=".$postid.">\n");

if ($pn == $pc){
echo("<a name=last>\n");
}


echo "<div>
<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
<tr>
<td class=\"postbit_top\">
<div class=\"postbit_head\" >
<div class=\"normal\" style=\"float:right\">".$numb_view."</div>
<div class=\"normal\">".normaltime(($arr['added']),true)."</div>
</div>
      
<table cellpadding=\"0\" cellspacing=\"10\" width=\"100%\">
<tr>
<td>".$avatar."</td>
<td nowrap=\"nowrap\">
<div>".$by." ".$online_view.$cansendpm."
</div>
<div class=\"smallfont\">".get_user_class_name($arr["class"])." ".(empty($arr["title"]) ? "": "(".htmlspecialchars($arr["title"]).")")."<br>
".($arr["donor"] == "yes" ? "<img src=\"".$BASEURL."/pic/star.gif\" alt='Донор'>" : "")
.($arr["enabled"] == "no" ? "<img src=\"".$BASEURL."/pic/disabled.gif\" alt=\"Этот аккаунт отключен\" style='margin-left: 2px'>" : ($arr["warned"] == "yes" ? "<img src=\"".$BASEURL."/pic/warned.gif\" alt=\"Предупрежден\" border=0>" : "")) . "
</div>
</td>

<td width=\"100%\">&nbsp;</td>
<td valign=\"top\" nowrap=\"nowrap\" class=\"n_postbit_info\"> 

<table cellpadding=\"0\" cellspacing=\"10\" width=\"100%\">
<tr>
<td valign=\"top\" nowrap=\"nowrap\"><div class=\"smallfont\"></div></td>
<td valign=\"top\" nowrap=\"nowrap\">
<div class=\"smallfont\">
<div><b>Рейтинг</b>:  ".$print_ratio."</div>
<div ><b>Залил</b>: ".mksize($arr["uploaded"]) ." </div>
<div><b>Скачал</b>: ".mksize($arr["downloaded"])."</div>
".$ban."
".($CURUSER? "<b>Сообщений на форуме</b>: <a href=\"".$BASEURL."/forums.php?action=search_post&userid=".$posterid."\" title=\"Поиск всех сообщений у ".$postername."\">".$arr["num_topuser"]."</a>":"")."
</div>
</td></tr></table>

<img src=\"/pic/forumicons/clear.gif\" alt=\"\" width=\"225\" height=\"1\" border=\"0\" />
</td></tr>
</table>
</td>
</tr>
<tr>
<td class=\"alt1\"> 
<hr size=\"1\" style=\"color:; background-color:\" />
<div class=\"pad_12\">
<div class=\"img_rsz\">".$body."</div>

".((is_valid_id($arr['editedby']) AND $arr['editedby']<>0)? "<hr>
<div class=\"post_edited smallfont\">
<em>Последний раз редактировалось ".(get_user_class() >= UC_MODERATOR ? "<a href='".$BASEURL."/userdetails.php?id=".$arr["editedby"]."'><b> ".get_user_class_color($ed_class, $ed_username)." </b></a>":"")." в <span class=\"time\">".($arr['editedat'])."</span>. ".($viworiginal == false ? "<a href='".$BASEURL."/forums.php?action=viewpost&id=".$postid."&ori'>К Оригинальному сообщению.</a>":"")."</em>
</div>
":"")."
      
<div style=\"margin-top: 10px\" align=\"right\">
".(($arr["signatrue"]=="yes" && $arr["signature"]) ? "  <span class=\"smallfont\">".format_comment($arr["signature"])."</span>": "")."
</div>
</div>
</td>


</tr>
</table>
<div class=\"pad_12 alt1\" style=\"border-top: 1px solid #ccc;\">
<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
<tr>";

echo "<td class=\"alt2\" align=right><span class=smallfont>";

if (!empty($posterid)){
echo (!empty($posterid) ? "[<a href='".$BASEURL."/forums.php?action=search_post&userid=".$posterid."'><b>Найти все сообщения</b></a>] ":"");
}

if ($CURUSER){
echo (!empty($posterid) ? "[<a href='".$BASEURL."/userdetails.php?id=".$posterid."'><b>Профиль</b></a>] ":"");
}

if ($CURUSER && $CURUSER["id"]<>$posterid){
echo (!empty($posterid) ? "[<a href='".$BASEURL."/message.php?receiver=".$posterid."&action=sendmessage'><b>Послать Сообщение</b></a>] ":"");
}

if ($CURUSER && $CURUSER["id"]<>$posterid){
echo ($posterid<>0 && !empty($postername) && $CURUSER["forum_com"]=="0000-00-00 00:00:00" ? "[<a href='".$BASEURL."/forums.php?action=quotepost&topicid=".$topicid."&postid=".$postid."'><b>Цитировать</b></a>] ":"");
}
  
if (get_user_class() >= UC_MODERATOR)
echo ($arr["ip"] ? "[<a title=\"Искать этот ip адресс в базе данных через административный поиск\" href=\"".$BASEURL."/usersearch.php?ip=".$arr["ip"]."\" ><b>".$arr["ip"]."</b></a>] ":"");
  
if (($CURUSER["forum_com"]=="0000-00-00 00:00:00" && $CURUSER["id"]==$posterid) || get_user_class() >= UC_MODERATOR){
echo "[<a href='".$BASEURL."/forums.php?action=editpost&postid=".$postid."'><b>Редактировать</b></a>] ";
}

if (get_user_class() >= UC_MODERATOR || $CURUSER["id"]==$posterid)
echo "[<a href='".$BASEURL."/forums.php?action=deletepost&postid=".$postid."'><b>Удалить</b></a>] ";

echo "</span></td>";

echo "</tr></table></div></div>";



	stdfoot_f();
	die;
}
      exit();
      break;
//////////////////////////////     
      
    default:
      std_view();
      break;
  }


function std_view() {

  global $readpost_expiry, $CURUSER,$SITENAME,$BASEURL;
  
  $forum_pic_url=$BASEURL."/pic/forumicons/";

  $added = get_date_time();
 // $forums_res = sql_query("SELECT f.sort, f.id, f.name, f.description, f.minclassread, f.minclasswrite, f.minclasscreate, p.added, p.topicid AS topicidi, p.userid, u.username,u.class, t.subject, top.lastpost, top.lastdate,(SELECT COUNT(*) FROM topics WHERE forumid=f.id) AS numtopics, (SELECT COUNT(*) FROM posts WHERE forumid=f.id) AS numposts   FROM forums AS f LEFT JOIN topics AS top ON top.lastdate = (SELECT MAX(lastdate) FROM topics WHERE forumid=f.id) LEFT JOIN posts AS p ON p.id = top.lastpost LEFT JOIN users u ON p.userid = u.id LEFT JOIN topics t ON p.topicid = t.id   ORDER BY sort, name") or sqlerr(__FILE__, __LINE__);
/// GROUP BY sort, name 
/// LOW_PRIORITY 
//  stdhead_f("Форум [2.76* версия за 18 января 2010]");

  if ($CURUSER && get_date_time(gmtime() - 60) >= $CURUSER["forum_access"]){
	sql_query("UPDATE users SET forum_access = ".sqlesc(get_date_time())." WHERE id = ".sqlesc($CURUSER["id"])."") or sqlerr(__FILE__, __LINE__);
  }

if ($CURUSER) {

$unread = $CURUSER["unread"];
$newmessage1 = $unread . " нов" . ($unread > 1 ? "ых" : "ое"); 
$newmessage2 = " сообщен" . ($unread > 1 ? "ий" : "ие"); 
if ($unread)
$newmessage = "<b><a href='".$BASEURL."/message.php?action=new'>У вас ".$newmessage1." ".$newmessage2."</a></b>"; 
else
$newmessage="";
}

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\"  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">
<html>
<head>
".meta_forum()."
<link rel=\"stylesheet\" type=\"text/css\" href=\"".$BASEURL."/js/style_forums.css\" />
<link rel=\"search\" type=\"application/opensearchdescription+xml\" title=\"Muz-Tracker Форум\" href=\"".$BASEURL."/js/forum.xml\">
<script language=\"javascript\" type=\"text/javascript\" src=\"".$BASEURL."/js/jquery.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"".$BASEURL."/js/forums.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"".$BASEURL."/js/swfobject.js\"></script> 
<script language=\"javascript\" type=\"text/javascript\" src=\"".$BASEURL."/js/functions.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"".$BASEURL."/js/tooltips.js\"></script>

<title>Форум - ".$SITENAME."</title>

</head>
 
<table cellpadding=\"0\" cellspacing=\"0\" id=\"main\">
<tr>
<td class=\"main_col1\"><img src=\"".$forum_pic_url."clear.gif\" alt=\"\" /></td>
<td class=\"main_col2\"><img src=\"".$forum_pic_url."clear.gif\" alt=\"\" /></td>
<td class=\"main_col3\"><img src=\"".$forum_pic_url."clear.gif\" alt=\"\" /></td>
</tr>
<tr>
<td>&nbsp;</td>
<td valign=\"top\">
<table cellpadding=\"0\" cellspacing=\"0\" id=\"header\">
<tr>
<td id=\"logo\">".LOGO."</td>

<td class=\"login\">
<div id=\"login_box\"><span class=smallfont>
<div>Здравствуйте, ".($CURUSER ? "<a href='".$BASEURL."/userdetails.php?id=".$CURUSER["id"]."'>".$CURUSER["username"]."</a>
<div>Последнее обновление: <span class=\"time\">".$CURUSER["forum_access"]."</span></div>
".($CURUSER ? "<div>".$newmessage."</div>":"")."
":" для просмотра полной версии данных,  
<div>пожалуйста, <a href='".$BASEURL."/login.php'>авторизуйтесь</a>.
<div>Права просмотра: Гость</div>
</div>
")."</span></div>
</div>
</td>
</tr>
</table>
</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>

<td>
<table cellpadding=\"0\" cellspacing=\"0\" id=\"menu_h\">
<tr>
<td class=\"first\"><a href=\"".$BASEURL."/index.php\">Главная сайта</a></td> 
<td class=\"shad\"><a href=\"".$BASEURL."/browse.php\">Торренты</a></td> 
<td class=\"shad\"><a href=\"".$BASEURL."/forums.php\">Главная форума</a></td>

".($CURUSER ? "<td class=\"shad\"><a href=\"".$BASEURL."/forums.php?action=search\">Поиск</a></td>
<td class=\"shad\"><a href=\"".$BASEURL."/forums.php?action=viewunread\">Непрочитанные комментарии</a></td>
<td class=\"shad\"><a title=\"Поменить все сообщения прочитанными\" href=\"".$BASEURL."/forums.php?action=catchup\">Все как прочитанное</a></td>":"")."

</tr>
</table>
</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>
<td valign=\"top\">
<table cellpadding=\"0\" cellspacing=\"0\" id=\"content_s\">
<tr>
<td class=\"content_col1\"><img src=\"".$forum_pic_url."clear.gif\" alt=\"\" /></td>
<td class=\"content_col_left\">&nbsp;</td>
<td class=\"content_col5\"><img src=\"".$forum_pic_url."clear.gif\" alt=\"\" /></td>
</tr>
<tr>
<td>&nbsp;</td>
<td valign=\"top\">
<br />
";
///<a name=\"poststop\" id=\"poststop\" href=\"".$BASEURL."/forums.php\">Главная страничка форума</a> <hr>


echo "
<div class=\"tcat_t\"><div class=\"tcat_r\"><div class=\"tcat_l\"><div class=\"tcat_tl\"><div class=\"tcat_simple\">

<table cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"tcat_name\">
<h1>Встроенный Форум сайта ".$SITENAME." </h1><font color=\"white\">(v. 1.5 beta:07.10.10)</font> ".(get_user_class() == UC_SYSOP ? "[<a class=\"altlink_white\" href=\"".$BASEURL."/forummanage.php\">Создать новую категорию</a>]":"")."

</td></tr></table>
<br class=\"tcat_clear\" />
</div></div></div></div></div>
<div class=\"post_body\" id=\"collapseobj_forumbit_5\" style=\"\">
<table cellspacing=\"0\" cellpadding=\"0\" class=\"forums\">
<tr>
<td class=\"f_thead_1\">Статус</td>
<td class=\"f_thead_2\">Категория</td>
</tr>";

$da=0;
$cache=new MySQLCache("SELECT f.sort, f.id, f.name, f.description, f.minclassread, f.minclasswrite, f.minclasscreate, p.added, p.topicid AS topicidi, p.userid, u.username,u.class, t.subject, top.lastpost, top.locked, top.lastdate,(SELECT COUNT(*) FROM topics WHERE forumid=f.id) AS numtopics, (SELECT COUNT(*) FROM posts WHERE forumid=f.id) AS numposts
FROM forums AS f
LEFT JOIN topics AS top ON top.lastdate = (SELECT MAX(lastdate) FROM topics WHERE forumid=f.id AND visible='yes') AND top.visible='yes' 
LEFT JOIN posts AS p ON p.id = top.lastpost 
LEFT JOIN users u ON p.userid = u.id 
LEFT JOIN topics t ON p.topicid = t.id AND t.visible='yes' 
WHERE t.visible='yes' ORDER BY sort, name", 12*3600,"forums.main.txt"); // 12 часов
while ($forums_arr=$cache->fetch_assoc()){
 	
  	/// новые данные с одного запроса  	
  	$topiccount = number_format($forums_arr['numtopics']); 
  	$postcount = number_format($forums_arr['numposts']); 
  	  	 	
    $forumid = $forums_arr["id"];
    $forumname = htmlspecialchars($forums_arr["name"]);
    $forumdescription = htmlspecialchars($forums_arr["description"]);


    if (empty($topiccount))
    $topiccount="нет";
  if (empty($postcount))
    $postcount="нет";

      if ($forums_arr["lastpost"]){
       
      //// новые данные
      $lastpostid = $forums_arr["lastpost"];
      $lasttopicid= $forums_arr["topicidi"];
     $lastposterid = $forums_arr["userid"];
      $lastpostdate = normaltime(($forums_arr["added"]),true);
      $lastposter = htmlspecialchars($forums_arr['username']);
      $lasttopic = format_comment_light($forums_arr['subject']);

///////////////////// доделать

if ($forums_arr["userid"]==0 && !$forums_arr['username']){
$view_user="<font color=gray>[System]</font>";
}
elseif ($forums_arr["userid"]<>0 && !$forums_arr['username']){
$view_user="<i><b>id</b>: ".$forums_arr["userid"]."</i>";
}
else
$view_user="<a href=".$BASEURL."/userdetails.php?id=$lastposterid><b>".get_user_class_color($forums_arr['class'], $lastposter)."</b></a> <a href=\"forums.php?action=search_post&userid=$lastposterid\"><img title=\"Искать все сообщения этого пользователя\" src=\"".$BASEURL."/pic/pm.gif\"></a>";
}

if ($CURUSER){
      $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=".sqlesc($CURUSER['id'])." AND topicid=".sqlesc($lasttopicid)) or sqlerr(__FILE__, __LINE__);
      $a = mysql_fetch_row($r);


	$npostcheck = ($forums_arr['added'] > get_date_time(gmtime() - $readpost_expiry)) ? (!$a OR $lastpostid > $a[0]) : 0;
	}
	
 
if ($da%2==1) {
$class="f_row_off";
}
else {
$class="f_row_on";	
}

if (($CURUSER["class"]>=$forums_arr["minclassread"] && $CURUSER) || ($forums_arr["minclassread"]=="0" && !$CURUSER)){

//$now_2day=time()-86400; /// раз день
$now_2day=get_date_time(gmtime() - 86400);


if ($forums_arr['added']>$now_2day)
$new=true; else $new=false;

$topicpic = ($forums_arr['locked']=="yes" ? ($new ? "lockednew" : "locked") : ($new ? "unlockednew" : "unlocked"));


echo "<tr>
<td class=\"".$class."\"><img title=\"Статус новых сообщений\" src=\"{$forum_pic_url}{$topicpic}.gif\"></td>
<td class=\"".$class."\" id=\"f60\">
<div><a href=\"".$BASEURL."/forums.php?action=viewforum&forumid=$forumid\"><b>$forumname</b></a> <span class=\"smallfont\">(Тем <b>$topiccount</b>, Сообщений <b>$postcount</b>)</span></div>
<div class=\"smallfont\">".$forumdescription."</div>

".(empty($forums_arr["lastpost"]) ? "
<div class=\"last_post_cl\">
<div class=\"smallfont\">
Сообщений нет.
</div>
":"
<div class=\"last_post_cl\">
<div class=\"smallfont\">
Последний пост &#8594; <a href=".$BASEURL."/forums.php?action=viewtopic&topicid=$lasttopicid&page=last#$lastpostid><strong>$lasttopic</strong></a> от $view_user <span class=\"time\">".$lastpostdate."</span>
</div>
")."
</td>
</tr>";

//echo "<tr><td colspan=\"2\" class=\"".$class."\">f</td></tr>";


++$da;
 
unset($view_add,$forumname,$postcount,$topiccount,$forumname);

	
	}
	
  }



/// статистика
if ($CURUSER){

$num_post1=sql_query("SELECT COUNT(*) AS my_user FROM posts WHERE userid='".$CURUSER["id"]."'") or sqlerr(__FILE__, __LINE__); 
$num_p1=mysql_fetch_assoc($num_post1);	

$num_post=sql_query("SELECT COUNT(*) AS cou, (SELECT COUNT(*) FROM posts WHERE editedby>'0000-00-00 00:00:00') AS mod_cou FROM posts") or sqlerr(__FILE__, __LINE__);
$num_p=mysql_fetch_assoc($num_post);

$num_post2=sql_query("SELECT COUNT(*) AS cou_user FROM posts GROUP BY userid") or sqlerr(__FILE__, __LINE__); 
$num_p2=mysql_fetch_assoc($num_post2);

/*
$num_post_cat=sql_query("SELECT	forumid,c.name, COUNT(*) AS pos_numis
FROM posts 
LEFT JOIN forums AS c ON c.id=posts.forumid
WHERE userid='".$CURUSER["id"]."' ORDER BY posts.id limit 1") or sqlerr(__FILE__, __LINE__);
$num_cat_sql=mysql_fetch_assoc($num_post_cat);
*/

$num_p_f=$num_p["cou"];
$num_p_modded=$num_p["mod_cou"];
$num_p_u=$num_p2["cou_user"];
$num_p_m=$num_p1["my_user"];
$num_cat_id=$num_cat_sql["name"];

//// вычитаем дни после реги
   $time_user=sql_timestamp_to_unix_timestamp($CURUSER["added"]);
    $time_now = time();
    if ($time_now>=$time_user)
    $diff = $time_now-$time_user;
    else
    $diff = $time_user-$time_now;

if($diff>=86400){
$day = floor($diff/86400);
} else $day=1;
//// вычитаем дни после реги
$size_to_day=@number_format((1 - $num_p_m/$day),2);
$size_to_all=@number_format(100*($num_p_m/$num_p_f),2);


}

echo "
</table>
</div>
<div class=\"off\">
<div class=\"tcat_b\"><div class=\"tcat_bl\"><div class=\"tcat_br\"></div></div></div>
</div><br>";

if ($CURUSER){
echo "<div class=\"tcat_t\"><div class=\"tcat_r\"><div class=\"tcat_l\"><div class=\"tcat_tl\"><div class=\"tcat_simple\">
<table cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"tcat_name\">
Статистика форума</td></tr></table>

<br class=\"tcat_clear\" />
</div></div></div></div></div>
 
<div class=\"post_body\">
<div class=\"f_dark\">
<b>»</b> Всего Ваших сообщений на форуме: <b>".$num_p_m."</b> | <b>".$size_to_day."</b> сообщений в день.<br/>
       <b>»</b> У вас <b>".$size_to_all."%</b> от всех сообщений на форуме.<br/><br/>
		 <b>» »</b> Всего сообщений на форуме: <b>".$num_p_f."</b> (отредактированых: <b>".$num_p_modded."</b>)<br> 
	 <b>» »</b> Оставлены пользователями: <b>".$num_p_u."</b><br/>
</div>
</div>
<div class=\"on\">
<div class=\"tcat_b\"><div class=\"tcat_bl\"><div class=\"tcat_br\"></div></div></div>
</div><br />";

///echo $htmlout;
}
  insert_quick_jump_menu($forumid);
  
   stdfoot_f(); 
	
    die;
}
?>