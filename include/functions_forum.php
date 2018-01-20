<?

/**  Внимание, самая популярная сборка движка теперь доступна в бесплатном скачивании (версия gold за 2010 год)!!!!
* Это старая версия проекта Tesla - торрент сайта. Демо новой версии можно посмотреть по ссылке demo.muz-tracker.net
* Если желаете использовать движок, то оставьте пожалуйста копирайта на своих местах, иначе стабильной работы не гарантируется.
* Если желаете поискать и установить новые моды, советуем воспользоваться форумами: bit-tracker.info, bit-torrent.kiev.ua.
* Если желаете поблагодарить владельца 7Max7 за эту сборку, киньте пару монеток на R190680055855 или Z187703352284
* Если желаете обновить Gold версию до Platinum - придется заплатить около 70$ (с последующими бесплатными обновлениями)
* 
* Данная копия движка взята с сайта demo muz-tracker net! ))
* Спасибо за внимание к движку Tesla.
**/



if(!defined('IN_TRACKER') || !defined('IN_FORUM'))
  die('Hacking attempt! functions_forum.');

function unlinks(){
for ($x=0; $x<7; $x++){
@unlink(ROOT_PATH."cache/block-forum_$x.txt");
}
@unlink(ROOT_PATH."cache/forums.main.txt");
}

 
function catch_up() {
global $CURUSER, $readpost_expiry;

$userid = $CURUSER["id"];

//$dt = (time() - $readpost_expiry);
$dt = get_date_time(gmtime() - $readpost_expiry);

$res = sql_query("SELECT t.id, t.lastpost 
FROM topics AS t 
LEFT JOIN posts AS p ON p.id = t.lastpost 
WHERE p.added > ".sqlesc($dt)) or sqlerr(__FILE__, __LINE__);

while ($arr = mysql_fetch_assoc($res)) {

$topicid = $arr["id"];
$postid = $arr["lastpost"];

$r = sql_query("SELECT id,lastpostread FROM readposts WHERE userid=".sqlesc($userid)." and topicid=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($r) == 0)
sql_query("INSERT INTO readposts (userid, topicid, lastpostread) VALUES(".sqlesc($userid).",".sqlesc($topicid).", ".sqlesc($postid).")") or sqlerr(__FILE__, __LINE__);

else
{
$a = mysql_fetch_assoc($r);

if ($a["lastpostread"] < $postid)
sql_query("UPDATE readposts SET lastpostread=".sqlesc($postid)." WHERE id=" . sqlesc($a["id"])) or sqlerr(__FILE__, __LINE__);
}
}

}

  //-------- Returns the minimum read/write class levels of a forum
  function get_forum_access_levels($forumid)  {
  	$forumid=(int)$forumid;
  	
    $res = mysql_query("SELECT minclassread, minclasswrite, minclasscreate FROM forums WHERE id=".sqlesc($forumid)."") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      return false;

    $arr = mysql_fetch_assoc($res);

    return array("read" => $arr["minclassread"], "write" => $arr["minclasswrite"], "create" => $arr["minclasscreate"]);
  }

  //-------- Returns the forum ID of a topic, or false on error

  function get_topic_forum($topicid)  {
    $res = mysql_query("SELECT forumid FROM topics WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      return false;

    $arr = mysql_fetch_row($res);

    return $arr[0];
  }

function update_topic_last_post($topicid,$added,$postold=false)  {
$topicid=(int)$topicid;
  	
$res = sql_query("SELECT id,added FROM posts WHERE topicid=".sqlesc($topicid)." ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

$arr = mysql_fetch_assoc($res);
$postid = $arr["id"];

if (empty($added))
$added=($arr["added"]);

if (!empty($postid))
sql_query("UPDATE topics SET lastpost=".sqlesc($postid).", lastdate=".sqlesc($added)." WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

if (!empty($postold))/// если сообщение последнее удаленное - ВЫШЕ по id
sql_query("UPDATE readposts SET lastpostread=".sqlesc($postid)." WHERE topicid = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
/// обновляем readposts если последнее прочит сообщение НИЖЕ по id последнего.

////sql_query("UPDATE readposts SET lastpostread=".sqlesc($postid)." WHERE lastpostread=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
}

  function get_forum_last_post($forumid)  {
  	$forumid = (int)$forumid;
  	
    $res = mysql_query("SELECT lastpost FROM topics WHERE forumid=".sqlesc($forumid)." ORDER BY lastpost DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    $postid = $arr[0];

    if ($postid)
      return $postid;
    else
      return 0;
  }

function insert_quick_jump_menu($currentforum = false, $users=false)  {
	
global $CURUSER;
/////////////////////
?>
<script>
function forum_online(id) {
jQuery.post("forums.php",{}, function(response) {
		jQuery("#forum_online").html(response);
	}, "html");
setTimeout("forum_online();", 60000);
}
forum_online();
</script>
<?

echo "
<div class=\"tcat_t\">
<div class=\"tcat_r\">
<div class=\"tcat_l\">
<div class=\"tcat_tl\">
<div class=\"tcat_simple\">

<table cellspacing=\"0\" cellpadding=\"0\">
<tr>
<td class=\"tcat_name\">
Кто просматривает форум</td>
</tr>
</table>

<br class=\"tcat_clear\" />
</div>
</div>
</div>
</div>
</div>
 
 
<div class=\"post_body\">
<div class=\"f_dark\">
<span align=\"center\" id=\"forum_online\">Загрузка кто смотрит форум</span>
</div>
</div>
<div class=\"on\">
<div class=\"tcat_b\"><div class=\"tcat_bl\"><div class=\"tcat_br\"></div></div>
</div>
</div><br />";


echo "<div class=\"smallfont\" style=\"white-space:nowrap\">";
$le.="<form method=get action=forums.php? name=jump><input type=hidden name=action value=viewforum>\n";
$le.="Быстрый переход: ";
$le.="<select name=forumid onchange=\"if(this.options[this.selectedIndex].value != -1){ forms['jump'].submit() }\">\n";

    ///$res = mysql_query("SELECT * FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);

   // while ($arr = mysql_fetch_assoc($res))
  //  {
  	
$cache=new MySQLCache("SELECT id,minclassread,name FROM forums ORDER BY name", 24*3600); // 24 часa
while ($arr=$cache->fetch_assoc()){

if ($arr["minclassread"]<=$CURUSER["class"] || ($arr["minclassread"]=="0" && !$CURUSER))
$le.="<option value=" . $arr["id"] . ($currentforum == $arr["id"] ? " selected>" : ">") . $arr["name"] . "\n";
}

$le.="</select>\n";
$le.="<input type=submit value='Вперед!'>\n";
$le.="</form>";
//echo("<table class=\"main\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td class=\"embedded\">\n");
//echo("<div align=\"right\" class=\"success\">$le</div></td></tr></table>\n");

echo $le;
echo"</div><br />";   


}

function insert_compose_frame($id, $newtopic = true, $quote = false)  {
    global $maxsubjectlength, $CURUSER;
    $forum_pic_url=$BASEURL."/pic/forumicons/";

    $maxsubjectlength = (int) $maxsubjectlength;
    if (empty($maxsubjectlength))
    $maxsubjectlength=255;
    
    if (!empty($newtopic))
    {
      $id=(int)$id;
      $res = sql_query("SELECT name FROM forums WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
      $arr = mysql_fetch_assoc($res);

      $forumname = htmlspecialchars($arr["name"]);
      
      if (!empty($forumname))
      echo("<p align=center>Новое сообщение для <a href=?action=viewforum&forumid=$id>$forumname</a> форума</p>\n");
    }
    else
    {
      $res = sql_query("SELECT * FROM topics WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
      $arr = mysql_fetch_assoc($res) or stderr("Ошибка форума", "Такого сообщения не найденно в базе.");

      $subject = htmlspecialchars($arr["subject"]);
      //print("<p align=center>Тема: <a href=?action=viewtopic&topicid=$id>$subject</a></p>");
    }

    begin_frame("Сообщение", true);

    echo("<form name=\"comment\" method=post action=forums.php?action=post>\n");

    if ($newtopic)
    echo("<input type=hidden name=forumid value=$id>\n");
    else
    echo("<input type=hidden name=topicid value=$id>\n");

    begin_table();

    if (!empty($newtopic))
    echo("<tr><td class=rowhead><b>Тема сообщения</b>: </td>
	<td align=left style='padding: 0px'><input type=text size=100 maxlength='$maxsubjectlength' name='subject' style='border: 0px; height: 19px'></td></tr>\n");

    if (!empty($quote))
    {
       $postid = (int)$_GET["postid"];
       
       if (!is_valid_id($postid))
       header("Location: $BASEURL/forums.php");

	   $res = sql_query("SELECT posts.*, users.username,class FROM posts LEFT JOIN users ON posts.userid = users.id WHERE posts.id=".sqlesc($postid)) or sqlerr(__FILE__, __LINE__);

	   if (mysql_num_rows($res) == 0)
	   stderr("Ошибка", "Нет сообщения с таким id.");

	   $arr = mysql_fetch_assoc($res);
    }

if (empty($newtopic)) {

  echo("<tr><td class=colhead align=\"center\" colspan=\"2\"><a name=comments></a><b><center>.::: Добавить сообщение  к теме :::.</b></center></td></tr>");
  echo("<tr><td width=\"100%\" align=\"center\" >");

  echo("<form name=\"comment\" method=\"post\" action=\"forums.php?action=post\">");
  echo("<center><table border=\"0\"><tr><td class=\"clear\">");
  echo("<div align=\"center\">". textbbcode("comment","body",($quote?("[quote=".htmlspecialchars($arr["username"])."]".htmlspecialchars($arr["body"])."[/quote]"):""), 1) ."</div>");
  echo("</td></tr></table></center>");
  echo("</td></tr><tr><td class=\"a\" align=\"center\" colspan=\"2\">");
  echo("<input type=\"submit\" class=btn value=\"Разместить сообщение\" /></form>");
}
else
{
echo("<div align=\"center\">". textbbcode("comment","body",($quote?("[quote=".htmlspecialchars($arr["username"])."]".htmlspecialchars($arr["body"])."[/quote]"):""), 1) ."</div>");
}
end_table();
    
if (!empty($newtopic)) {
echo("<input type=\"submit\" class=btn value=\"Разместить сообщение\" /></form>");
} else
echo("</form>\n");

end_frame();

if (empty($newtopic)){

      $postres = sql_query("SELECT t.*, u.username, u.class, u.last_access, u.ip, u.signatrue,u.signature,u.avatar, u.title, u.enabled, u.warned, u.hiderating,u.uploaded,u.downloaded,u.donor
	  FROM posts as t
	  LEFT JOIN users AS u ON u.id=t.userid 	
	  WHERE t.topicid=".sqlesc($id)." ORDER BY t.id DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);

      begin_frame("<hr>Последние 10 комментариев, от последнего к первому.");

      while ($post = mysql_fetch_assoc($postres)) {
      $postid = $post["id"];
      $posterid = $post["userid"];
      $added = ($post['added']);
      $postername = $post["username"];
      $posterclass = $post["class"];
      
      if (empty($postername) && $posterid<>0){
        $by = "<b>id</b>: $posterid";
      }
      elseif ($posterid==0 && empty($postername)){
      	$by="<i>Сообщение от </i><font color=gray>[<b>System</b>]</font>";
      }
      else
      {
        $title = $post["title"];

        if (empty($title))
          $title = get_user_class_name($post["class"]);
  
        $by = "<a href=".$BASEURL."/userdetails.php?id=".$posterid."><b>" .get_user_class_color($posterclass,  $postername). "</b></a>
		".($post["donor"] == "yes" ? "<img src="."\"".$BASEURL."/pic/star.gif\" alt='Донор'>" : "") . 
		($post["enabled"] == "no" ? "<img src=\""."\"".$BASEURL."/pic/disabled.gif\" alt=\"Этот аккаунт отключен\" style='margin-left: 2px'>" : ($post["warned"] == "yes" ? "<img src=\"".$BASEURL."/pic/warned.gif\" alt=\"Предупрежден\" border=0>" : "")) . "
		".(empty($title) ? " / ".get_user_class_color($posterclass,$title) : "")."";
      }
if ($posterid<>0 && !empty($postername)){
	     if (strtotime($post["last_access"]) > gmtime() - 600) {
			     	$online = "online";
			     	$online_text = "В сети";
			     } else {
			     	$online = "offline";
			     	$online_text = "Не в сети";
			     }
	
		    if ($arr["downloaded"] > 0) {
			    	$ratio = $post['uploaded'] / $post['downloaded'];
			    	$ratio = number_format($ratio, 2);
			    } elseif ($post["uploaded"] > 0) {
			    	$ratio = "Infinity";
			    } else {
			    	$ratio = "---";
			    }

if ($post["hiderating"]=="yes"){
$print_ratio="<b>+100%</b>";
}
else
$print_ratio="<img src=\"".$BASEURL."/pic/upl.gif\" alt=\"Залито\" border=\"0\" width=\"12\" height=\"12\"> ".mksize($post["uploaded"]) ." : <img src=\"".$BASEURL."/pic/down.gif\" alt=\"Скаченно\" border=\"0\" width=\"12\" height=\"12\"> ".mksize($post["downloaded"])." : $ratio";

} else {
unset($print_ratio);
unset($ratio);
unset($online_text);
unset($online);
}
if (!empty($post["avatar"])){
$avatar = "".($CURUSER["id"]==$posterid ? "<a href=\"".$BASEURL."/my.php\"><img alt=\"Аватар, по клику переход в настройки\" title=\"Аватар, по клику переход в настройки\" width=100 height=100 src=\"./pic/avatar/".$post["avatar"]."\"/></a>":"<img width=100 height=100 src=\"".$BASEURL."/pic/avatar/".htmlspecialchars($post["avatar"])."\"/>")."";
}
       $locked = $post["locked"] == "yes";
       $posterid=$post["userid"];

        if ($post["username"]){
        $user="<a href=userdetails.php?id=".$post["userid"].">".get_user_class_color($post["class"], $post["username"])."</a>";
            } else {
          $user="id: ".$post["userid"];
            }
            
        echo("<p class=sub>#" . $post["id"] . " ".($CURUSER["class"]>"3" ? "<img src=\"".$BASEURL."/pic/button_".$online.".gif\" alt=\"".$online_text."\" title=\"".$online_text."\" style=\"position: relative; top: 2px;\" border=\"0\" height=\"14\">" : "").$by
		.($arr["donor"] == "yes" ? "<img src='".$BASEURL."/pic/star.gif' alt='Донор'>" : "")
		.($arr["warned"] == "yes" ? "<img src=\"".$BASEURL."/pic/warned.gif\" alt=\"Предупрежден\">" : "")
		.($arr["enabled"] == "no" ? "<img src=\"".$BASEURL."/pic/warned2.gif\" alt=\"Отключен\">" : "")
		.($posterid<>0 && !empty($postername) ? " :: $print_ratio ":"")

 .($CURUSER["cansendpm"]=='yes' && ($CURUSER["id"]<>$posterid && $posterid<>0 && !empty($postername))? ":: <a href='".$BASEURL."/message.php?action=sendmessage&amp;receiver=".$posterid."'>"."<img src=".$BASEURL."/pic/button_pm.gif border=0 alt=\"Отправить сообщение\" ></a>" : "")."");

         begin_table(true);

        echo("<tr valign=top><td width=100 align=left style='padding: 0px'>" .
        ($avatar ? $avatar : ""). "<br /><div></div></td><td class=comment>" . format_comment($post["body"])
		.(($post["signatrue"]=="yes" && $post["signature"])? "<p valign=down align=down><hr>".format_comment($post["signature"])."</p>": "")."
		</td></tr>
		<td align=center class=a>".($post["added"])."</td>\n");

	echo("<td align=\"right\">"); 

      if (($CURUSER["id"] == $posterid && !$locked) || get_user_class() >= UC_MODERATOR)
      echo("[<a href='".$BASEURL."/forums.php?action=editpost&postid=" . $post["id"] . "'><b>Редактировать</b></a>]");

      if (get_user_class() >= UC_MODERATOR)
      echo(" - [<a href='".$BASEURL."/forums.php?action=deletepost&postid=" . $post["id"] . "'><b>Удалить</b></a>]</td>");

        end_table();
      }
      end_frame();
    }
  insert_quick_jump_menu();
  }
  

function get_cleanup($time=false){
   
// удаление истекших прочитанных постов для каждого юзера...
	
$h = date('H'); // проверяем час
if (($h >= 06)&&($h <= 12)) // расписание
{

$now = time();
$res = mysql_query("SELECT value_u FROM avps WHERE arg = 'fread_posts'") or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$row_time=$row["value_u"];
$now_2day=time()+(86400*3); /// раз в три дня
$dt = get_date_time(gmtime());

if ($row_time<=$now){

global $readpost_expiry;

$dt = (time() - $readpost_expiry);
sql_query("DELETE readposts FROM readposts LEFT JOIN posts ON readposts.lastpostread = posts.id WHERE posts.added < $dt") or sqlerr(__FILE__,__LINE__);
$delete_readpost=mysql_affected_rows();

sql_query("DELETE FROM posts WHERE topicid not in (SELECT id FROM topics WHERE id=posts.topicid)") or sqlerr(__FILE__,__LINE__); 
$delete_posts=mysql_affected_rows();

$numo="Readposts: $delete_readpost; Posts: $delete_posts";

if (empty($row_time)) {
sql_query("INSERT INTO avps (arg, value_u,value_s,value_i) VALUES ('fread_posts',$now_2day,'$numo','$delete_readpost')");
} elseif (!empty($row_time)) {
sql_query("UPDATE avps SET value_u='$now_2day',value_s='$numo', value_i='$delete_readpost' WHERE arg='fread_posts'") or sqlerr(__FILE__,__LINE__);
}
}

}
// удаление истекших прочитанных постов для каждого юзера...

}
   

   
   /// для stdhead (chat) создание meta заголовков
function meta_forum($iforum=FALSE,$ipost=FALSE) {

global $SITENAME,$DEFAULTBASEURL;

$def_ico=$DEFAULTBASEURL;
$def_url=$DEFAULTBASEURL.getenv("REQUEST_URI");

$a = $_GET["action"];

if ($a == "viewpost" && is_valid_id($_GET["id"]))
$ipost = (int) $_GET["id"];

if (empty($_COOKIE["uid"])){

if (!empty($iforum) && empty($ipost)) {
$s=sql_query("SELECT name, description FROM forums WHERE id=".sqlesc($iforum)); 
$meta_bot=mysql_fetch_array($s);
$name=$meta_bot["name"];
$descr=$meta_bot["description"];
}

elseif (empty($iforum) && !empty($ipost)) {

$s=sql_query("SELECT body,(SELECT subject FROM topics WHERE topics.id=posts.topicid LIMIT 1) AS subject FROM posts WHERE id=".sqlesc($ipost)); 
$meta_bot=mysql_fetch_array($s);
$name=$meta_bot["subject"];
$descr=$meta_bot["body"];
} else {
$name="музыкальный трекер, портал музыки, muz-tracker, muz, музыка торрент";
$descr="Музыкальный портал музыки. Встроенный Форум, общение, чаты, конкурсы, викторины, знакомства.";	
}


$descr = strip_tags(format_comment($descr));
$descr = substr($descr,0,250);
$descr = preg_replace('/[^\w]+/i', ' ', $descr);
$descr = trim(preg_replace('/[\r\n\t]/i', ' ', $descr));

//$keywords="$hometext $bodytext";
$keywords=strip_tags($name);
$keywords = preg_replace('/[^\w]+/i', ' ', $keywords);
$keywords=preg_replace("/\s/",",",$keywords); /// заменяем пробелы на ,
$keywords=trim(preg_replace('/[\r\n\t]/i', ',', $keywords));

$keywords = substr($keywords,0,1600);
$keywords = array_unique(explode(",", $keywords));

for ($a=0,$b=7; $a < sizeof($keywords) && $b < 800; $a++) {

if (($c=strlen($keywords[$a]))>3) {

if (empty($key_words)){
$key_words=$keywords[$a]; 
} else {
$key_words=$key_words.", ".$keywords[$a].""; 
}
$b+=$c+2; 
}
}
}


$content = "";
$content.= "<meta http-equiv=\"content-type\" content=\"text/html; charset=windows-1251\"/>\n";
$content.= "<meta name=\"author\" content=\"7Max7\"/>\n";
$content.= "<meta name=\"publisher-url\" Content=\"".($def_url)."\"/>\n";
$content.= "<meta name=\"copyright\" content=\"Tesla Tracker TT (".date("Y").") v.Gold\"/>\n";
$content.= "<meta name=\"generator\" content=\"PhpDesigner см. useragreement.php\"/>\n";

if (!empty($key_words))
$content.= "<meta name=\"keywords\" content=\"".$key_words."\"/>\n";

if (!empty($descr))
$content.= "<meta name=\"description\" content=\"".$descr."\"/>\n";

$content.= "<meta name=\"robots\" content=\"index, follow\"/>\n";
$content.= "<meta name=\"revisit-after\" content=\"7 days\"/>\n";
$content.= "<meta name=\"rating\" content=\"general\"/>\n";
$content.= "<link rel=\"shortcut icon\" href=\"".$def_ico."/pic/favicon.ico\" type=\"image/x-icon\"/>\n";
$content.= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Последние торрент релизы\" href=\"".$def_ico."/rss.php\"/>\n";
//$content.= "<!------------- Конкурентам ПРИИИИВЕЕЕЕД от 7Max7 --------------> ";

return $content;
}
   
   
   
   
function format_comment_light($text=false) {
	global $CURUSER,$BASEURL,$pic_base_url, $lang;	
	$s = $text;
	
	$s = str_replace(";)", ";-)", $s);

	$search = array("'<script[^>]*?>.*?</script>'si","'&(quot|#34|#034|#x22);'i","'&(amp|#38|#038|#x26);'i",);
	$replace = array("","\""," ",);
	$s = preg_replace($search,$replace,$s);

    $site = parse_url($BASEURL, PHP_URL_HOST);

//	if ($xssclean)
//	$s = xss_clean($s);
	// замена ' на пробел
  ///  $s=preg_replace("/'/i"," ",$s );

/*
if (empty($CURUSER)){
$s = preg_replace( "#\[url\s*=\s*\& quot\;\s*(\S+?)\s*\& quot\;\s*\](.*?)\[\/url\]#i" , "\\2", $s );
$s = preg_replace( "#\[url\s*=\s*(\S+?)\s*\](.*?)\[\/url\]#i", "\\2", $s );
//$s = preg_replace( "#\[url\](\S+?)\[/url\]#i", "[b][i]Вы - гость и не можете видеть ссылки. [url=$BASEURL]Зарегистрируйтесь![/url][/i][/b]", $s );
}
*/

    

	// [b]жирный[/b]
	$s = preg_replace("/\[b\]((\s|.)+?)\[\/b\]/is", "<b>\\1</b>", $s);

	// [i]курсив[/i]
	$s = preg_replace("/\[i\]((\s|.)+?)\[\/i\]/is", "<i>\\1</i>", $s);

	// [h]крупный[/h]
	$s = preg_replace("/\[h\]((\s|.)+?)\[\/h\]/is", "<h3>\\1</h3>", $s);

	// [u]подчеркнутый[/u]
	$s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/is", "<u>\\1</u>", $s);

	// [s]зачеркнутый[/s]
	$s = preg_replace("#\[s\](.*?)\[/s\]#si", "<s>\\1</s>", $s);

	// [li]
	$s = preg_replace("#\[li\]#si", "<li>", $s);
	
	// [hr]
	$s = preg_replace("#\[hr\]#si", "<hr>", $s);

	// [br]
	$s = preg_replace("#\[br\]#si", "<br>", $s);
	// [*]
	$s = preg_replace("/\[\*\]/", "<li>", $s);
	   	
	// [img]http://www/image.gif[/img]
	$s = preg_replace("/\[img\](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))\[\/img\]/is", "<img border=\"0\" src=\"\\1\" alt=\"\\1\">", $s);

	// [img=http://www/image.gif]
	$s = preg_replace("/\[img=(http:\/\/[^\s'\"<>]+(\.(gif|jpg|png)))\]/is", "<img border=\"0\" src=\"\\1\" alt=\"\\1\">", $s);

	// [color=blue]Текст[/color]
	$s = preg_replace("/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/is","\\2", $s);

	// [color=#ffcc99]Текст[/color]
	$s = preg_replace("/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]/is","\\2", $s);

  // [url=http://www.example.com]Пример[/url]
         $s = preg_replace("/\[url=(http:\/\/[^()<>\s]+?)\]((\s|.)+?)\[\/url\]/is","<a href=\"\\1\" rel=\"nofollow\" target=\"_self\">\\2</a>", $s);
    
    // [url]http://www.example.com[/url]
        $s = preg_replace("/\[url\](http:\/\/[^()<>\s]+?)\[\/url\]/is","<a href=\"\\1\" rel=\"nofollow\" target=\"_self\">\\1</a>", $s);
	

	// [url]http://www.example.com[/url]
	$s = preg_replace("/\[url\]([^()<>\s]+?)\[\/url\]/is","<a href=\"\\1\" rel=\"nofollow\" target=\"_self\">\\1</a>", $s);
	

	// поставил старые теги влево вправо, пришлось переделывать для этого.
    $s = preg_replace("#\[(left|right|center|justify)\](.*?)\[/\\1\]#is", "\\2", $s);

    $s = preg_replace("#\[align=(left|right|center|justify)\](.*?)\[/align\]#is", "\\2", $s);
	// [size=4]Text[/size] (поставил старый*)
	$s = preg_replace("#\[size=([0-9]+)\](.*?)\[/size\]#si","\\2", $s);

// [font=Arial]Text[/font]
	$s = preg_replace("/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/is","\\2", $s);

	$array=array("javascript","alert","<body","<html");
	$s = str_replace($array, "", $s);

	$s = format_quotes($s);
	$s = format_urls($s);
	
	$s = nl2br($s);

	//linebreak
	$s = wordwrap($s, 97, "\n", 1);

	return $s;
}



function stderr_f($heading = '', $text = '') {
   stdhead_f();
   stdmsg_f($heading, $text, 'error');
   stdfoot_f();
	die;
}


function stdhead_f ($title=false){

global $BASEURL,$CURUSER,$SITENAME;

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

<link rel=\"stylesheet\" type=\"text/css\" href=\"js/style_forums.css\" />
<link rel=\"search\" type=\"application/opensearchdescription+xml\" title=\"Muz-Tracker Форум\" href=\"".$BASEURL."/js/forum.xml\">
<script language=\"javascript\" type=\"text/javascript\" src=\"js/jquery.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"js/forums.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"js/swfobject.js\"></script> 
<script language=\"javascript\" type=\"text/javascript\" src=\"js/functions.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"js/tooltips.js\"></script>
<title>".(empty($title)? "Форум - ".$SITENAME:$title." - ".$SITENAME)."</title>
".meta_forum($forumid)."
</head>	
<body>

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
}


function stdfoot_f ($title=false){

//echo "<div class=\"tcat_b\"><div class=\"tcat_bl\"><div class=\"tcat_br\"></div></div></div><br />";
///echo "</tr></table></body></html>";


global $use_gzip,$queries,$tstart,$querytime;
$gzip=($use_gzip=="yes" && !empty($CURUSER) ? " (gzip ".ini_get('zlib.output_compression_level').")":".");
$seconds = (timer() - $tstart);
$phptime = 	$seconds - $querytime;
$query_time = $querytime;
$percentphp = number_format(($phptime/$seconds) * 100, 2);
$percentsql = number_format(($query_time/$seconds) * 100, 2);
$seconds = 	substr($seconds, 0, 5);
//$memory = round(@memory_get_usage()/1024);
$memory = mksize(round(@memory_get_usage()));
$time_sql=sprintf("%0.4lf",$querytime);
$time_php=sprintf("%0.4lf",$phptime);


echo "<br />
<td>&nbsp;</td>
</tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr></table></td>
<td>&nbsp;</td></tr>

<tr>
<td>&nbsp;</td>
<td align=\"center\"><div class=\"content_bl\"><div class=\"content_br\"><div class=\"content_b\"></div></div></div>

<table cellpadding=\"0\" cellspacing=\"0\" id=\"footer\" align=\"center\">
<br><div style=\"color: #FFFFFF\" align=\"center\" class=\"smallfont\"><b>".VERSION.TBVERSION."<br> Страничка сформированна за <b>".$seconds."</b> секунд".$gzip."</font>
<br><b>".$queries."</b> (queries) - <b>".$percentphp."%</b> (".$time_php." => php) - <b>".$percentsql."%</b> (".$time_sql." => sql) 
".((get_user_class() == UC_SYSOP && $memory) == "yes" ? " - ".$memory." (use memory)":"")."</div>
</tr>
</table></td>
<td>&nbsp;</td>
</tr></table></body></html>"; 

if (DEBUG_MODE && get_user_class() == UC_SYSOP && isset($_COOKIE["debug"]) && $_COOKIE["debug"]=="yes") {
	global $query_stat;
	
		foreach ($query_stat as $key => $value) {
			print("<div style=\"color: #FFFFFF\" align=\"center\">[<b>".($key+1)."</b>] => <b>
			".($value["seconds"] <= 0.0009 ? "<font color=\"#FFFFFF\" title=\"Сверхбыстрый запрос. Время исполнения отличное.\">".$value["seconds"]."</font>":"
		    ".($value["seconds"] >= 0.01 ? "<font color=\"red\" title=\"Рекомендуется оптимизировать запрос. Время исполнения превышает норму.\">".$value["seconds"]."</font>" : "<font color=\"blue\" title=\"Запрос не нуждается в оптимизации. Время исполнения допустимое.\">".$value["seconds"]."</font>" )."
		    ")."
		  </b> [".$value["query"]."]</div>\n");
		}
	debug();
	}
}


function stdmsg_f($heading = '', $text = '', $div = 'success', $htmlstrip = false) {
    if ($htmlstrip) {
        $heading = (trim($heading));
        $text = (trim($text));
    }
  
    echo "<br>
<div class=\"tcat_t\"><div class=\"tcat_r\"><div class=\"tcat_l\"><div class=\"tcat_tl\"><div class=\"tcat_simple\">

<div align=\"center\"><a name=comments></a><b>.::: ".($heading ? "<b>$heading</b>" : "")." :::.</b></div>

<br class=\"tcat_clear\" /></div></div></div></div></div>
<div class=\"post_body\" id=\"collapseobj_forumbit_5\" align=\"center\" style=\"\">
<table cellspacing=\"0\" cellpadding=\"0\" class=\"forums\">";

  echo("<div align=\"center\" class=statist>$text</div>");
    
   echo "</table></div>
<div class=\"off\"><div class=\"tcat_b\"><div class=\"tcat_bl\"><div class=\"tcat_br\"></div></div></div>
</div></div><br>";
 
    
}

 
  function begin_main_frame()  {
    print("<table class=\"main\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" .
      "<tr><td class=\"embedded\">\n");
  }


  function end_main_frame()  {
    print("</td></tr></table>\n");
  }

  function begin_table($fullwidth = false, $padding = 5)  {
    $width = "";
    
    if ($fullwidth)
      $width .= " width=\"100%\"";
    print("<table class=\"main\"$width border=\"1\" cellspacing=\"0\" cellpadding=\"$padding\">\n");
  }

  function end_table()  {
    print("</td></tr></table>\n");
  }
  
   function begin_frame($caption = "", $center = false, $padding = 10)  {
    $tdextra = "";
    
    if ($caption)
      print("<h2>$caption</h2>\n");

    if ($center)
      $tdextra .= " align=\"center\"";

    print("<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"$padding\"><tr><td$tdextra>\n");

  }

  function attach_frame($padding = 10)  {
    print("</td></tr><tr><td style=\"border-top: 0px\">\n");
  }

  function end_frame()  {
    print("</td></tr></table>\n");
  }

function insert_smilies_frame() {
    global $smilies, $DEFAULTBASEURL;

    begin_frame("Смайлы", true);

    begin_table(false, 5);

    print("<tr><td class=\"colhead\">Написание</td><td class=\"colhead\">Смайл</td></tr>\n");

    while (list($code, $url) = each($smilies))
      print("<tr><td>$code</td><td><img src=\"$DEFAULTBASEURL/pic/smilies/$url\"></td>\n");

    end_table();

    end_frame();
}
   
   
   
   
?>