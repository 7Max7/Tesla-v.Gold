<?
require "include/bittorrent.php";

dbconn(false);
//loggedinorreturn();

function bark($msg) {
	global $tracker_lang;
	stdhead($tracker_lang['error']);
	stdmsg($tracker_lang['error'], $msg);
	stdfoot();
	exit;
}
parse_referer();

$id = (int)$_GET["id"];

if (!is_valid_id($id))
  bark($tracker_lang['invalid_id']);

///[[4] =>  0.003551  

if (get_user_class() >= UC_MODERATOR) {
	$sql_add=",(SELECT count(*) FROM posts WHERE userid = $id) AS forumposts";	
}

$thanks_sql=",(SELECT count(*) FROM thanks WHERE userid = $id) AS userid_t,(SELECT count(*) FROM thanks WHERE touid = $id) AS touid_t";

$r = sql_query("SELECT * $sql_add $thanks_sql FROM users WHERE id=$id") or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_array($r) or bark("��� ������������ � ����� ID $id.");


if ($user["id"]=="92")
$user["class"]="5";

if ($user["status"] == "pending")
bark("��� ������� ������ ��� �� �������������, ������ �� �����������.");

///13 (queries) - 76.45% (0.0643 => php) - 23.55% (0.0198 => sql) - 2.62 �� (use memory)
/// 9 (queries) - 87.70% (0.0680 => php) - 12.30% (0.0095 => sql) - 2.53 �� (use memory) �����
/*
g.name AS g_name, g.image AS g_image
FROM users 
LEFT JOIN groups AS g ON g.id=users.groups
*/

// ���� �������� �� ������ � ip ������ �����
/*
if ($user["ip"] && (get_user_class() >= UC_MODERATOR || $user["id"] == $CURUSER["id"])) {
  $ip = $user["ip"];
  $dom = @gethostbyaddr($user["ip"]);
  if ($dom == $user["ip"] || @gethostbyname($dom) != $user["ip"])
    $addr = $ip;
  else
  {
    $dom = strtoupper($dom);
    $domparts = explode(".", $dom);
    $domain = $domparts[count($domparts) - 2];
    if ($domain == "COM" || $domain == "CO" || $domain == "NET" || $domain == "NE" || $domain == "ORG" || $domain == "OR" )
      $l = 2;
    else
      $l = 1;
    $addr = "$ip ($dom)";
  }
}
*/

if (($user["ip"] && get_user_class() >= UC_MODERATOR) || $user["id"] == $CURUSER["id"]) {
  $addr = $user["ip"]; 
}

if ($user["last_login"] == "0000-00-00 00:00:00")
	$last_login = '����������';
else
	$last_login = normaltime($user["last_login"], true)." (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["last_login"])) . " ".$tracker_lang['ago'].")";
	
	
if ($user["added"] == "0000-00-00 00:00:00")
	$joindate = 'N/A';
else
	$joindate = normaltime($user["added"], true)." (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["added"])) . " ".$tracker_lang['ago'].")";
	
	
$lastseen = normaltime($user["last_access"], true);
if ($user["last_access"] == "0000-00-00 00:00:00")
$lastseen = $tracker_lang['never'];

if ((get_user_class() < UC_MODERATOR) && ($user["class"]) > UC_ADMINISTRATOR)(
$lastseen = "���� ������ � ���� ;)");

else {
  $lastseen .= " (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["last_access"])) . " ".$tracker_lang['ago'].")";
}


if (get_user_class() >= UC_ADMINISTRATOR) {

 $result = sql_query("SELECT DISTINCT port, ip, agent FROM peers WHERE userid=".$user["id"]." LIMIT 10") or sqlerr(__FILE__,__LINE__);//, connectable

if(mysql_num_rows($result)==0)
 $torrent_port = "";

else {
$numip = 0;
while ($port = mysql_fetch_assoc($result))  {

if ($torrent_port)
$torrent_port.="<br>";

if ($user["ip"]<>$port['ip'])
$port['ip'] = "<b>".$port['ip']."</b>";


//if ($port['connectable']=='no')
$torrent_port.=$port['ip'].":".$port['port']." ".(isset($port['agent']) ? "(".$port['agent'].")":"")."";
//else
//$torrent_port="[".$port['ip'].":<a alt=\"������� ���� �������� ����������\" title=\"������� ���� �������� ����������\">".$port['port']."</a>]";
++$numip;
}
}
}


if ($user["country"]<>"0" || $user["city"]<>"0") {

$u=(int)$user["city"];
$c=(int)$user["country"];

$sql_rec=", g.name AS g_name, g.image AS g_image";

$res = sql_query("SELECT co.name, co.flagpic, ci.name AS cit_name FROM countries AS co LEFT JOIN cities AS ci ON ci.id=$u and ci.country_id=co.id WHERE co.id=$c") or sqlerr(); 
//if (mysql_num_rows($res) == 1) 
//{
  $arr = mysql_fetch_assoc($res); 
  //$country = "<img src=/pic/flag/$arr[flagpic] alt=\"$arr[name]\" style='margin-left: 8pt'>"; 
  $country = "<img src=/pic/flag/".$arr["flagpic"]." alt=\"".$arr["name"]."".($arr["cit_name"]=="" ? "" : " - ".$arr["cit_name"]."")."\" style='margin-left: 8pt'>"; 
  $countryy = $arr["name"]; 
  $city = $arr["cit_name"];
  
  
  
 // $groups = "<a title=\"� ������ ���� �����\" href=\"groups.php\"><img src='./pic/groups/".$arr["g_image"]."' alt=".$arr["g_name"]."></a>";   
//}
}

/*
if ($user["groups"]<>"0"){

// ������
$res = sql_query("SELECT name,image FROM groups WHERE id=$user[groups] LIMIT 1") or sqlerr();  
if (mysql_num_rows($res) == 1)   
{
 $arr = mysql_fetch_assoc($res);   
 $groups = "<img src=/pic/groups/$arr[image] alt=$arr[name]> ";   
}

}
*/

//if ($user["donor"] == "yes") $donor = "<td class=embedded><img src=pic/starbig.gif alt='Donor' style='margin-left: 4pt'></td>";
//if ($user["warned"] == "yes") $warned = "<td class=embedded><img src=pic/warned.gif alt='Warned' style='margin-left: 4pt'></td>";

if ($user["gender"] == "1") $gender = "<img src=\"".$pic_base_url."male.gif\" alt=\"������\" title=\"������\">";
elseif ($user["gender"] == "2") $gender = "<img src=\"".$pic_base_url."female.gif\" alt=\"�������\" title=\"�������\">";
elseif ($user["gender"] <> "0") $gender = "<img src=\"".$pic_base_url."smilies/question.gif\" alt='�/�' title=\"�����������\">";

////// ������ ��� �������� ��� ����� //////
if ($user["birthday"] <> "0000-00-00")
{
        //$current = date("Y-m-d", time());
        $current = date("Y-m-d", time() + 60);
        list($year2, $month2, $day2) = explode('-', $current);
        $birthday = $user["birthday"];
        $birthday = date("Y-m-d", strtotime($birthday));
        list($year1, $month1, $day1) = explode('-', $birthday);
        if($month2 < $month1)
        {
                $age = $year2 - $year1 - 1;
        }
        if($month2 == $month1)
        {
                if($day2 < $day1)
                {
                        $age = $year2 - $year1 - 1;
                }
                else
                {
                        $age = $year2 - $year1;
                }
        }
        if($month2 > $month1)
        {
                $age = $year2 - $year1;
        }

}
////// ������ ��� �������� ��� ����� //////


if ($user["id"]==2 AND !empty($ds))
{
$las = normaltime("2009-11-10 13:28:31", true)." (" . get_elapsed_time(sql_timestamp_to_unix_timestamp("2009-11-10 13:28:06")) . " �����)";

bark("�������� ������������� � $las <br>
<object width=\"150\" height=\"68\" id=\"mju\">
<param name=\"allowScriptAccess\" value=\"sameDomain\" />
<param name=\"swLiveConnect\" value=\"true\" />
<param name=\"movie\" value=\"mju.swf\" />
<param name=\"flashvars\" value=\"swf/playlist=playlist.mpl&auto_run=false&repeat _one=false&shuffle=false\" />
<param name=\"loop\" value=\"false\" />
<param name=\"menu\" value=\"false\" />
<param name=\"quality\" value=\"high\" />
<param name=\"wmode\" value=\"transparent\" />
<embed src=\"swf/mju.swf\" flashvars=\"playlist=swf/playlistt9.mpl&auto_run=true&repeat_one=false&shuff le=false\" loop=\"false\" menu=\"false\" quality=\"high\" wmode=\"transparent\" bgcolor=\"#ffffff\" width=\"150\" height=\"68\" name=\"mju\" allowScriptAccess=\"sameDomain\" swLiveConnect=\"true\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
</object>
");

}
stdhead("�������� ������� " . $user["username"]);

if ($CURUSER["id"] <> $user["id"] && $CURUSER["cansendpm"]=="yes") {
$r = sql_query("SELECT id, blocks FROM friends WHERE userid=".sqlesc($CURUSER["id"])." AND friendid = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$sql_row = mysql_fetch_array($r);
 
 if ($sql_row["blocks"]=="0")
    $friending="[<a href=\"friends.php?action=delete&type=friend&targetid=$id\">������ �� ������</a>]\n";
  elseif($sql_row["blocks"]=="1")
    $blocking="[<a href=\"friends.php?action=delete&type=block&targetid=$id\">������ �� ������������</a>]\n";
  else
{
$friending="[<a href=\"friends.php?action=add&type=friend&targetid=$id\">�������� � ������</a>]<br>";
$blocking="[<a href=\"friends.php?action=add&type=block&targetid=$id\">�������� � �������������</a>]\n";
}
}



$t9=($user["username"]=="��"? " a.k.a ".get_user_class_color($user["class"],"7Max7",(isset($italik)?$italik:"")):"");


$enabled = $user["enabled"] == 'yes';
print("<table class=\"main\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"1000%\"><tr><td class=\"b\" align=\"center\"><h1 style=\"margin:0px\">
".get_user_class_color($user["class"],$user["username"],($user["chat_access"]>get_date_time(gmtime() - 40))) .$t9. get_user_icons($user, true)." ".$country."</h1> ".(!empty($friending) ||!empty($blocking) ? $friending.$blocking:"")."</td></tr></table>\n");

if (!$enabled)
print("<p><b>���� ������� ��������</b></p>\n");
  
if ($user["monitoring"]=='yes' && get_user_class() == UC_SYSOP)
print("<p><b><a href='monitoring.php?id=".$user["id"]."'>�� ���� ��������� ������ �������</a></b></p>\n");

begin_main_frame();

echo "<table width=100% border=0 cellspacing=0 cellpadding=5>";

if ($CURUSER){
/////////////////////
?>
<script>
function detail_online(id) {
jQuery.post("block-online_details_jquery.php",{"id":id}, function(response) {
		jQuery("#detail_online").html(response);
	}, "html");

setTimeout("detail_online('<?=$user["id"];?>');", 30000);
}
detail_online('<?=$user["id"];?>');
</script>
<?

echo"
<tr><td class=rowhead><b>�������������</b></td><td align=\"left\">
<span align=\"center\" id=\"detail_online\">�������� ��� ������������� ������� ".$user["username"]."</span>
</td></tr>";
/////////////////////
}


echo("<tr><td class=rowhead>���������������</td><td align=left>$joindate</td></tr>");

if ($CURUSER)
echo("<tr><td class=rowhead>����������</td><td align=left>$lastseen</td></tr>");

if (($CURUSER["id"] == $user["id"]) || get_user_class() >= UC_MODERATOR){
print("<tr><td class=rowhead>��������� ����</td><td align=left>$last_login</td></tr>");
}


if ($CURUSER["id"] == $user["id"] || get_user_class() >= UC_MODERATOR) {

$lastchat = normaltime($user["chat_access"], true);
if ($user["chat_access"] == "0000-00-00 00:00:00")
	$lastchat = $tracker_lang['never'];
	
echo "<tr><td class=rowhead>�.����� � ����</td><td align=left>$lastchat ".($user["chat_access"] <> "0000-00-00 00:00:00" ? "(" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["chat_access"])) . " ".$tracker_lang['ago'].")":"")."</td></tr>";
}

if ($CURUSER["id"] == $user["id"] || get_user_class() >= UC_MODERATOR) {

$lastchat = normaltime($user["forum_access"], true);
if ($user["forum_access"] == "0000-00-00 00:00:00")
	$lastchat = $tracker_lang['never'];
	
echo "<tr><td class=rowhead>�.����� �� ������</td><td align=left>$lastchat ".($user["forum_access"] <> "0000-00-00 00:00:00" ? "(" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["forum_access"])) . " ".$tracker_lang['ago'].")":"")."</td></tr>";
}

if ($CURUSER["id"] == $user["id"] || get_user_class() >= UC_MODERATOR){
echo ("<tr><td class=rowhead>������ �������</td><td align=left>".get_seed_time($user["on_line"])."</td></tr>");
}

if ($CURUSER["id"] == $user["id"] || get_user_class() >= UC_MODERATOR){
echo ("<tr><td class=rowhead>�����������</td><td align=left>".get_seed_time($user["seed_line"])."</td></tr>");
}


if (get_user_class() >= UC_SYSOP && !empty($user["passkey"])) {
echo("<tr><td class=\"rowhead\">Passkey</td><td align=left>".$user["passkey"]."</a> 
<b>[</b>".$user["last_checked"]."<b>]</b> <b>[</b><a href='downcheck.php?search_id=".$id."'>����������������</a><b>]</b>
</td></tr>");
}

if (($CURUSER["id"] == $user["id"]) || get_user_class() >= UC_MODERATOR){
if ((($CURUSER["id"] <> $user["id"]) && get_user_class() <= UC_ADMINISTRATOR) && ($user["class"]==UC_SYSOP))
{$user[email]="<i>����� ������</i>";}
else 
{$user['email'] = "$user[email]";}

echo("<tr><td class=\"rowhead\">�����</td><td align=\"left\">".protectmail($user["email"])."</a></td></tr>\n");
}


if ($CURUSER["id"] == $user["id"] || $CURUSER["class"] >= UC_MODERATOR){
if (!empty($addr) || !empty($torrent_port))
echo("<tr><td class=\"rowhead\">IP ����� <br>�� �����/� ��������</td><td align=\"left\">".$addr." ".(!empty($torrent_port) ? "<br>".$torrent_port:"")."</td></tr>\n");
}

if ($user['username']<>"�������@��")
echo("<tr><td class=\"rowhead\">�����</td><td align=\"left\"><b>" . get_user_class_color($user["class"], get_user_class_name($user["class"])) . (!empty($user["title"]) ? " / 
 ".get_user_class_color($user["class"],$user["title"]) : "") . "</b></td></tr>\n");
 
// print("<tr><td class=\"rowhead\">�����</td><td align=\"left\"><b><font color=\"#".get_user_rgbcolor($user["class"],$user["username"])."\">" . get_user_class_name($user["class"]) . (!empty($user["title"])? " / <font color=\"#".get_user_rgbcolor($user["class"],$user["username"])."\"> ".format_comment($user["title"])."</font>" : "") . "</font></b></td></tr>\n");
 

if (($CURUSER["id"] == $user["id"]) && $CURUSER["override_class"]<>255){
print("<tr><td class=\"rowhead\">������� �����</td><td align=\"left\">
<b>" . get_user_class_color($user["override_class"], get_user_class_name($user["override_class"]))."</b> <b>[</b>��������� �����<b>]</b></td></tr>\n");
}

if ($user["override_class"]<>255 && $CURUSER["class"] > UC_ADMINISTRATOR){
print("<tr><td class=\"rowhead\">������� �����</td><td align=\"left\">
<b>" . get_user_class_color($user["override_class"], get_user_class_name($user["override_class"]))."</b> <b>[</b>��������� �����<b>]</b></td></tr>\n");
}

if ($user["num_warned"]<>"0" && $CURUSER) {
//��� �������������� 
echo("<tr><td class=\"rowhead\">�������<br>��������������</td><td align=\"left\">"); 
for($i = 0; $i < $user["num_warned"]; $i++) 
{
$img .= "<a href=\"mywarned.php\"  target=\"_blank\"><img src=\"".$pic_base_url."warned.gif\" style=\"border:none\" alt=\"������� ��������������\" title=\"������� ��������������\"></a>"; 
}
if (!$img) 
$img = "��� ��������������"; 
echo("$img</td></tr>\n");  // �������� ��� ������ �� �����, ���� �������� ����, ���� ���� �� ..
}

// � ���� ��������	
$dayUpload   = $user["uploaded"];
$dayDownload = $user["downloaded"];

$seconds = mkprettytime(strtotime("now") - strtotime($user["added"]));
$days = explode("d ", $seconds);
/// ������ ����� ���� ����, � � ��������� ���������� �����
if(sizeof($days) > 1) {
$dayUpload   = $user["uploaded"] / $days[0];
$dayDownload = $user["downloaded"] / $days[0];
}
  
$dayUp=mksize($dayUpload);
$dayDown=mksize($dayDownload);

if($CURUSER["class"] >= UC_ADMINISTRATOR) {
$dayviewup = " <b>[</b><a title=\"������ �������� ���������� �� �������, �� ��� ����������� ����� ������� now ����� � ������� �������� ������� ����� � ����\">� ����:</a> $dayUp<b>]</b>";
$dayviewdown = " <b>[</b><a title=\"������ �������� ���������� �� �������, �� ��� ����������� ����� ������� now ����� � ������� �������� ������ ����� � ����\">� ����:</a> $dayDown<b>]</b>";
}

// � ���� ��������	


// ������� - 100
$upload_all = mksize($user["uploaded"]); 
$down_all = mksize($user['downloaded']); 


if ($user["hiderating"] <> "yes" && ($user["downloaded"]<>"0" ||$user["uploaded"]<>"0")){

echo("<tr>
<td class=rowhead>������</td><td align=left>".$upload_all.(!empty($upped_per_day)?"<b>".$upped_per_day."</b>":"").$dayviewup."</td></tr><tr>
<td class=rowhead>�������</td><td align=left>".$down_all.(!empty($down_per_day)?"<b>".$down_per_day."</b>":"").$dayviewdown."</td>
</tr>\n"); 

if ($user["downloaded"] > 0){
  $sr = $user["uploaded"] / $user["downloaded"];
  if ($sr >= 4) $s = "w00t"; 
  else if ($sr >= 2) $s = "grin";  
  else if ($sr >= 1) $s = "gib";  
  else if ($sr >= 0.5) $s = "noexpression";  
  else if ($sr >= 0.25) $s = "ac";  
  else  $s = "cry";  

  $sr = floor($sr * 1000) / 1000;  

  $sr = "<tr><td class=rowhead style='vertical-align: middle'>�������</td><td class=tablea align=left valign=center style='padding-top: 1px; padding-bottom: 0px'><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><font color=" . get_ratio_color($sr) . ">" . number_format($sr, 3) . "</font></td><td class=embedded>&nbsp;&nbsp;<img src=/pic/smilies/$s.gif></td></tr></table></td></tr>\n";
  }

echo (isset($sr) ? $sr:""); 

}
if ($user["hiderating"]=="yes"){
echo("<tr><td class=\"rowhead\">�������</td><td align=left><font color=ff1ac6><b>+100%</b></font>
<b>[</b>����������� ���!<b>]</b>
</td></tr>\n"); 
}
// ������� - 100


if (get_user_class() >= UC_MODERATOR && !empty($user["bonus"])) {
echo ("<tr><td class=\"rowhead\">������</td><td align=left>".$user["bonus"]."</a></td></tr>");
}

//if ($CURUSER["id"] == $user["id"] || get_user_class() >= UC_MODERATOR) {
if ($user["stylesheet"]){
echo ("<tr><td class=\"rowhead\">������</td><td align=left>".$user["stylesheet"]."</a></td></tr>");
}
//}


if ($user["invites"]<>"0")
echo ("<tr><td class=\"rowhead\">�����������</td><td align=left><a href=\"invite.php?id=$id\">".$user["invites"]."</a></td></tr>");

/// ����� ������������ ��������� - USERNAME
if ($user["invitedby"] <> 0) {
$inviter = mysql_fetch_assoc(sql_query("SELECT username, class FROM users WHERE id = ".sqlesc($user["invitedby"])));

if ($inviter){
echo("<tr><td class=\"rowhead\">���������</td><td align=\"left\"><a href=\"userdetails.php?id=$user[invitedby]\">".get_user_class_color($inviter["class"], $inviter["username"])."</a></td></tr>");
}}

if (($user["icq"] || $user["vkontakte"] || $user["odnoklasniki"] || $user["skype"]) && $CURUSER) {

    if ($user["icq"])
    $icq_view = "<img src=\"http://web.icq.com/whitepages/online?icq=$user[icq]&amp;img=5\" alt=\"icq\" border=\"0\" /> $user[icq]<br>";
   
    if ($user["skype"])
    $skype_view = "<img src=\"pic/contact/skype.gif\" alt=\"skype\" border=\"0\" /> $user[skype]<br>";
        
         
		 
		$vkontakte=htmlspecialchars($user["vkontakte"]);
        if (is_numeric($vkontakte)) {
        $vkontak="http://vkontakte.ru/id$vkontakte";
        }
        if (stristr($vkontakte,'http://vkontakte.ru/id')) {
        $vkontak="$vkontakte";
        }
        if (!stristr($vkontakte,'http://') && !is_numeric($vkontakte)) {
        $vkontak="http://vkontakte.ru/$vkontakte";
        }
        

       if ($vkontakte)
    $vkontakte_view = "<img src=\"pic/contact/vkontakte.gif\" alt=\"���������\" border=\"0\" /> <a href=\"$vkontak\" title=\"�� ��������� ������������\" target=\"_blank\">� � ��������</a><br>";
            
    $odnoklasniki=htmlspecialchars($user["odnoklasniki"]);
    if ($odnoklasniki)
    $odnoklasniki_view = "<img src=\"pic/contact/odnoklasniki.gif\" alt=\"odnoklasniki\" border=\"0\" /> $odnoklasniki";
    
    
	print("<tr><td class=\"rowhead\">�����</td><td align=\"left\">".
	(!empty($icq_view)?$icq_view:"").
	(!empty($skype_view)?$skype_view:"").
	(!empty($vkontakte_view)?$vkontakte_view:"").
	(!empty($odnoklasniki_view)?$odnoklasniki_view:"")."	
	</td></tr>\n");
    }
 
 if (stripos($user["website"],'http://')!==true)
 $site="http://$user[website]";
 
 if (stripos($user["website"],'http://')!==false)
  $site = $user["website"];


if ($user["website"])
echo ("<tr><td class=\"rowhead\">����</td><td align=\"left\"><a href=\"$site\" target=\"_blank\">$site</a></td></tr>\n");

if ($user["avatar"])
echo ("<tr><td class=\"rowhead\">������</td><td align=left><img src=\"/pic/avatar/" . htmlspecialchars($user["avatar"]) . "\"></td></tr>\n");


echo ("<tr><td class=\"rowhead\">���</td><td align=\"left\">$gender</td></tr>\n");

if($user["birthday"]<>'0000-00-00') {
      //  print("<tr><td class=\"rowhead\">�������</td><td align=\"left\">$age</td></tr>\n");
        $birthday = date("d.m.Y", strtotime($birthday));
    
        print("<tr><td class=\"rowhead\">���� ��������</td><td align=\"left\"><b>$birthday</b> [<b>".agetostr($age)."</b>]</td></tr>\n");

$month_of_birth = substr($user["birthday"], 5, 2);
        $day_of_birth = substr($user["birthday"], 8, 2);
        for($i = 0; $i < count($zodiac); $i++) {
                if (($month_of_birth == substr($zodiac[$i][2], 3, 2)))  {
                        if ($day_of_birth >= substr($zodiac[$i][2], 0, 2)) {
                                $zodiac_img = $zodiac[$i][1];
                                $zodiac_name = $zodiac[$i][0];
                        }
                        else {
                                if ($i == 11) {
                                        $zodiac_img = $zodiac[0][1];
                                        $zodiac_name = $zodiac[0][0];
                                }
                                else {
                                        $zodiac_img = $zodiac[$i+1][1];
                                        $zodiac_name = $zodiac[$i+1][0];
}}}}

echo("<tr><td class=\"rowhead\">���� �������</td><td align=\"left\"><img src=\"pic/zodiac/" . $zodiac_img . "\" alt=\"" . $zodiac_name . "\" title=\"" . $zodiac_name . "\"></td></tr>\n");

}

if (!empty($countryy) ||!empty($city))
  echo("<tr><td class=\"rowhead\">����� ����������</td><td align=\"left\">".($countryy ? "<b>������</b>: ".$countryy :"")." ".($city ? "<b>�����</b>: ".$city:"")."</td></tr>\n");  
  
if ($user['simpaty'] <> 0) {
        if ((get_user_class() >= UC_MODERATOR && $user['class'] < get_user_class()) || $user['id'] == $CURUSER['id']) {
                $simpaty = ($user['simpaty'] > 0?'<img src="pic/thum_good.gif" border="0">&nbsp;<a href="mysimpaty.php?id=' . $user['id'] . '">' . $user['simpaty'] . '</a>':'<img src="pic/thum_bad.gif" border="0">&nbsp;<a href="mysimpaty.php?id=' . $user['id'] . '">' . $user['simpaty'] . '</a>');
        }
        else {
                $simpaty = ($user['simpaty'] > 0?'<img src="pic/thum_good.gif">&nbsp;' . $user['simpaty']:'<img src="pic/thum_bad.gif">&nbsp;' . $user['simpaty']);
        }
} 

if ($user['simpaty']<>0) {
print("<tr><td class=\"rowhead\">���������</td><td align=\"left\">$simpaty</td></tr>\n");
};

if ($user["gender"] == "2") $se = "a";
    
//$txlst = sql_query("SELECT COUNT(*) FROM thanks WHERE userid=" . $user[id]) or sqlerr();;
//$list1 = mysql_fetch_row($txlst);
// $count1 = $list1[0];

 //$txlst3 = sql_query("SELECT COUNT(*) FROM thanks WHERE touid=" . $user[id]) or sqlerr();;
//$list3 = mysql_fetch_row($txlst3);
 //$count3 = $list3[0];



$count1=$user["userid_t"];
$count3=$user["touid_t"];

if ($count1 <> 0)
print("<tr valign=top><td class=rowhead>������������".(!empty($se)?$se:"")."</td><td align=left>".$count1." ���(�)</td></tr>\n");
if ($count3 <> 0)
print("<tr valign=top><td class=rowhead>�������������</td><td align=left>".$count3." ���(�)</td></tr>\n");  


if (get_user_class() >= UC_MODERATOR) {
	
$torrentcomments=$pollcomments=$newscomments=$offercomments=0;

$res = sql_query("SELECT c.torrent,c.offer,c.news,c.poll FROM comments AS c WHERE c.user = " . $user["id"]) or sqlerr(__FILE__, __LINE__);
while ($arr3 = mysql_fetch_array($res)){
  
  if ($arr3["torrent"])
  ++$torrentcomments;
 
  if ($arr3["poll"])
  ++$pollcomments;
  
  if ($arr3["news"])
  ++$newscomments;
  
  if ($arr3["offer"])
  ++$offercomments;
  }
  $postforums = $user["forumposts"];

echo("<tr><td class=\"rowhead\">�����������</td>");

if ($torrentcomments <> 0)
$torrentcomments2 = " ��������: <a href=\"userhistory.php?action=viewcomments&id=$id\"><b>$torrentcomments</b></a>";
else $torrentcomments2="";
if ($pollcomments <> 0)
$pollcomments2 = " ������: <a href=\"userhistory.php?action=viewpollscomment&id=$id\"><b>$pollcomments</b></a>";
else $pollcomments2="";
if ($newscomments <> 0)
$newscomments2 = " �������: <a href=\"userhistory.php?action=viewnewscomment&id=$id\"><b>$newscomments</b></a>";
else $newscomments2="";
if ($postforums <> 0)
$postforums2=" �����: <a href=\"forums.php?action=search_post&userid=$id\"><b>$postforums</b></a>";
else $postforums2="";
if ($offercomments <> 0)
$offercomments2=" �������: <a><b>$offercomments</b></a>";
else $offercomments2="";

if (empty($torrentcomments) && empty($pollcomments) &&  empty($newscomments) && empty($offercomments2) && empty($postforums))
$clear_all = " <b>��� �� ������ �������� � ��������, ��������, ������ ��� ���� � ������.</b>";

echo("<td align=\"left\">".$torrentcomments2.$pollcomments2.$newscomments2.$postforums2.$offercomments2.(!empty($clear_all)?$clear_all:"")."</tr></td></tr>\n");
}


// ������

if ($user["groups"]<>"0"){

$id_gro=(int) $user["groups"];

$groupe_cache=new MySQLCache("SELECT image,name FROM groups WHERE id=".sqlesc($id_gro)."", 86400,"details_groups-".$id_gro.".txt"); // ��� ���� ���� (������������ � details � � userdetails - ���)
$name_gre=$groupe_cache->fetch_assoc();

if (!empty($name_gre["image"]))
echo("<tr><td class=\"rowhead\"><b>�������� ������</b></td><td class=tablea align=left>

".($CURUSER ? "<a href=\"groups.php\"><img title=\"".htmlspecialchars($name_gre["name"])."\" src='pic/groups/".$name_gre["image"]."'></a>":"<img title=\"".htmlspecialchars($name_gre["name"])."\" src='pic/groups/".$name_gre["image"]."'>")."

</td></tr>\n"); 
// ������
//echo $row["groups"];
}

/*
///������
if (isset($arr["g_name"]))
print("<tr><td class=\"rowhead\"><b>���� ������:</b></td><td class=tablea align=left>$groups</td></tr>\n"); 
///������
*/



//������������
if ($CURUSER && ($CURUSER["id"] <> $id && get_user_class() < UC_MODERATOR)){
//$report_sql = sql_query("SELECT id FROM report WHERE usertid = $id AND torrentid=0");
//$report_row = mysql_fetch_assoc($report_sql);
print("<tr><td class=\"rowhead\"><b>�����������:</b></td><td align=left>
<form method=\"post\" action=\"report.php?id=".$id."\"><input name=motive cols=40 value=\"\">&nbsp;<input type=\"submit\" value=\"���������\" /><input type=\"hidden\" name=\"usertid\" value=\"$id\"></form></td></tr>\n");  
}
//������������




if (get_user_class() <= UC_MODERATOR && $user["class"]==UC_MODERATOR && $CURUSER){
//$r = sql_query("SELECT id,name FROM categories ORDER BY sort") or sqlerr(__FILE__, __LINE__);
$r=new MySQLCache("SELECT id,name FROM categories ORDER BY sort", 86400,"mydetails_category.txt"); 
$i=0;
//if (mysql_num_rows($r) > 0)
//{
print("<tr><td class=\"rowhead\">�� ����������</td><td colspan=\"2\"> "); 
print"<table><tr>\n";

//	while ($a = mysql_fetch_assoc($r))
while ($a=$r->fetch_assoc())
{
$prin.=($i && $i % 2 == 0) ? "</tr><tr>" : "";
$prin.=(stristr($user["catedit"], "[cat$a[id]]") !== false ? "<td><b><a title=\"��������� ����������\" href=\"browse.php?incldead=1&cat=$a[id]\">" .htmlspecialchars($a["name"]) . "</a></b></td>" : "") . "\n";
 ++$i;	  
}
if (empty($user["catedit"]))
$prin="<b>��� ���������</b>";

echo $prin."</tr></table>\n";
//print("</td></tr>\n");
}

//}

if ($CURUSER){
?>

<style type="text/css">
<!--
#tabs {
    text-align: center;
}
#tabs .tab {
    border: 1px solid #cecece;
    padding: 5px 10px 5px 10px;
    background:#ededed;
    margin-right:5px;
    line-height: 23px;
    cursor: pointer;
    font-weight: bold;
}
#tabs.active {
    border-bottom: none;
    padding-bottom: 10px;
    background: #F5F5F5;
    cursor: default;
}
#tabs #body {
    border: 1px solid #cecece;
    padding: 5px;
    margin-bottom: 10px;
    background: #FAFAFA;
}
#tabs .tab_error {
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
    background-color: #777;
    padding: 5px;
}
-->
</style>

<script type="text/javascript">
var loading = "<br><br><img src=\"pic/loading.gif\" alt=\"��������..\" />";
jQuery(function() {
    jQuery(".tab").click ( function(){
        if(jQuery(this).hasClass("active"))
            return;
        else
        {
            jQuery("#loading").html(loading);
            var user = jQuery("#body").attr("user");
            var act = jQuery(this).attr("id");
            jQuery(this).toggleClass("active");
            jQuery(this).siblings("span").removeClass("active");
            jQuery.post("block-details_jquery.php",{"user":user,"act":act},function (response) {
                jQuery("#body").empty();
                jQuery("#body").append(response);
                jQuery("#loading").empty();
            });
        }
    });
    jQuery('.zebra:even').css({backgroundColor: '#EEEEEE'});
    if(jQuery.browser.msie)
    {
        width = jQuery('#profile_right h2').width();
        if (width > 600)
            jQuery('#profile_right').width(width);
        else
        {
            jQuery('#profile_right').width("600");
            jQuery('#profile_container').width("686");
        }
    }
});
</script>

<?
echo "<tr valign=\"top\"><td colspan=2 align=center>\n";

echo "<div id=\"tabs\">\n";
echo "<span title=\"������� ��������\" class=\"tab\" id=\"uploaded\">�������</span>\n";

echo "<span title=\"������ �������� � �� ���������\" class=\"tab\" id=\"snatched\">������</span>\n";
echo "<span title=\"������ ��������� ��������\" class=\"tab\" id=\"completed\">���������</span>\n";

echo "<span title=\"����������� ��������\" class=\"tab\" id=\"leeching\">������</span>\n";

echo "<span title=\"����������� ��������\" class=\"tab\" id=\"seeding\">�������</span>\n";
echo "<span title=\"����������� ��������\" class=\"tab\" id=\"checked\">�����������</span>\n";
//echo "<br><br>";
echo "<span class=\"tab active\" id=\"info\">�������</span>\n";

if ($user["id"]==$CURUSER["id"] || get_user_class() >= UC_MODERATOR){
echo "<span title=\"������������ ������������\" class=\"tab\" id=\"inviter\">������������</span>\n";
echo "<span title=\"��� ������������ ����� ������������ �� ������\" class=\"tab\" id=\"in_re\">��� ������������</span>\n";
echo "<span title=\"���� ������������ ���� ������������ �� ������\" class=\"tab\" id=\"out_re\">���� ������������</span>\n";
echo "<span title=\"��� ��������, �������� ������������ �����������\" class=\"tab\" id=\"agent\">��������</span>\n";

if ($refer_parse=="1")
echo "<span title=\"��� ����������� ������, � ������� ������������ ������ �� ����\" class=\"tab\" id=\"ref\">�������</span>\n";
}
echo "<span id=\"loading\"></span>\n";
echo "<div id=\"body\" user=\"".$id."\">\n";


if ($user["info"])
$view_info=format_comment($user["info"]);
 
if ($user["signature"])
$view_signature=format_comment($user["signature"]);

if ($user["info"] && $user["signature"])
$view_br="<hr>";

if (empty($user["info"]) && empty($user["signature"]))
echo "� ������������ ��� �������.";

//echo $view_info.$view_br.$view_signature;

echo (isset($view_info) ? $view_info:"").(isset($view_br) ? $view_br:"").(isset($view_signature)? $view_signature:"");

echo "</div>";
echo "</div>";

echo "</td></tr>";
 
}
 else 
{
echo "<tr valign=\"top\"><td colspan=2 align=center>\n";
 	
if ($user["info"])
$view_info=format_comment($user["info"]);

if ($user["signature"])
$view_signature=format_comment($user["signature"]);

if ($user["info"] && $user["signature"])
$view_br="<hr>";

if (empty($user["info"]) && empty($user["signature"]))
echo "� ������������ ��� �������.";

echo $view_info.$view_br.$view_signature;
 	
echo "</td></tr>";
 	
}
 /*
if ($user["info"])
 print("<tr valign=\"top\"><td align=\"left\" colspan=\"2\" class=\"b\" >" . format_comment($user["info"]) ."</td></tr>\n");
 
if ($user["signature"])
 print("<tr valign=\"top\"><td align=\"left\" colspan=\"2\" class=\"b\" >" . format_comment($user["signature"]) ."</td></tr>\n");
*/

if ($CURUSER["id"] <> $user["id"]){
//$r2 = sql_query("SELECT blocks FROM friends WHERE userid = $user[id] AND friendid = $CURUSER[id]") or sqlerr(__FILE__,__LINE__);
//$bl = mysql_fetch_array($r2);


	if (get_user_class() >= UC_MODERATOR)
  	$showpmbutton = 1;
	elseif ($user["acceptpms"] == "yes") {
	//	���� ���� ���� �� �� ���������� ������
	$showpmbutton = ($sql_row["blocks"]=="1" ? 0 : 1);
	} elseif ($user["acceptpms"] == "friends") {
	$showpmbutton = ($sql_row["blocks"]=="0" ? 1 : 0);
	}

if ($showpmbutton && $CURUSER["cansendpm"]=='yes')
echo ("<tr><td colspan=2 align=center><form method=\"get\" action=\"message.php\"> 
<input type=\"hidden\" name=\"receiver\" value=" .$user["id"] . "> 
<input type=\"hidden\" name=\"action\" value=\"sendmessage\"> 
<input type=submit class=\"btn\" value=\"".(get_user_class() >= UC_MODERATOR ? "��������� ��������� / ������ �� �����":"��������� ���������")."\" style=\"width: 300px\"> 
</form></td></tr>");
}
echo("</table>\n");

if ((get_user_class() >= UC_MODERATOR && ($user["class"] < get_user_class()||($CURUSER["id"] == $user["id"] && $user["class"] = get_user_class())) && $user["override_class"]==255) || ((get_user_class() == UC_SYSOP and $user["override_class"]<=5)))
{

$hides=getenv("HTTP_REFERER");
if (!empty($hides))
$hide="hide";
else 
$hide="none";


echo "
<script type=\"text/javascript\">
$(document).ready(function() { 

    $(\"span.spoiler\").$hide();
    $('<a class=\"reveal\"> <form ><input type=button class=btn value=\"������� �����, ��� �������������� ������������\" style=\"height: 50px; width: 100%\"></form></a> ').insertBefore('.spoiler');

    $(\"a.reveal\").click(function(){
        $(this).parents(\"p\").children(\"span.spoiler\").fadeIn(2500);
        $(this).parents(\"p\").children(\"a.reveal\").fadeOut(600);
    });
});
</script>";


  begin_frame("�������������� ������� ������ ".$user["username"]."" , true);
  
  print("<p><span class=\"spoiler\"><noscript><b>���������� �������� ����� ��������</b></noscript>");
  
  print("<form method=\"post\" action=\"modtask.php\">\n");
//  print("<input type=\"hidden\" name=\"action\" value=\"edituser\">\n");
  print("<input type=\"hidden\" name=\"userid\" value=\"$id\">\n");
//  print("<input type=\"hidden\" name=\"returnto\" value=\"userdetails.php?id=$id\">\n");
  print("<table class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");
  

  if (get_user_class() > UC_MODERATOR){
  	
  	$infouser = htmlspecialchars($user["info"]);

    print("<tr><td class=rowhead>���������� <br>[".strlen($infouser)."]</td><td colspan=2 align=left><textarea cols=100% rows=5 name=info>$infouser</textarea></td></tr>\n");
       
     print("<tr><td class=\"rowhead\">�������������� ����</td><td colspan=\"2\" align=\"left\">
		<input type=radio name=editinfo value=yes" .($user["editinfo"]=="yes" ? " checked" : "") . ">�� <input type=radio name=editinfo value=no" .($user["editinfo"]=="no" ? " checked" : "") . ">���</td></tr>\n");
		
 	$signature = htmlspecialchars($user["signature"]);
   print("<tr><td class=rowhead>������ ������� <br>[".strlen($signature)."]</td><td colspan=2 align=left><textarea cols=100% rows=4 name=signature>$signature</textarea></td></tr>\n");

   print("<tr><td class=\"rowhead\">�������������� ������ �������</td><td colspan=\"2\" align=\"left\">
		<input type=radio name=signatrue value=yes" .($user["signatrue"]=="yes" ? " checked" : "") . ">�� <input type=radio name=signatrue value=no" .($user["signatrue"]=="no" ? " checked" : "") . ">���</td></tr>\n");


// ������ �������




$re = sql_query("SELECT SUM(upthis) AS upthiss  FROM cheaters WHERE userid=$user[id]") or sqlerr();
$ro = mysql_fetch_assoc($re); 
$TotalUploadedTB=mksize($ro["upthiss"]); 

if ($ro["upthiss"]!="")
{
print("<tr><td class=\"rowhead\">��������</td><td colspan=\"2\" align=\"left\">�������� �������� � �������� �������: <b>$TotalUploadedTB</b> 
".($CURUSER["class"]==6 ? "<b>[</b><a href=\"cheaters.php\">�����������</a><b>]</b><br>
".($user["uploaded"]>=$ro["upthiss"] ? "<input name=\"reschitup\" value=\"1\" type=\"checkbox\"><i>����� ������� � ������ ��������</b>":"")."":"(������ ���� ����� � ������� �������)")."</td></tr>\n");
}

//die("$user[uploaded]");
  }


  print("<tr><td class=\"rowhead\">���������</td><td colspan=\"2\" align=\"left\"><input type=\"text\" size=\"40\" name=\"title\" value=\"" . htmlspecialchars($user["title"]) . "\"> <i>�� 30 �������� max</i></tr>\n");
  
  print("<tr><td class=\"rowhead\">Web c���</td><td colspan=\"2\" align=\"left\"><input type=\"text\" size=\"40\" name=\"website\" value=\"" . htmlspecialchars($user["website"]) . "\"> <i>�� 40 �������� max</i></tr>\n");


 /*
 if ($CURUSER["class"] >= UC_ADMINISTRATOR){
   print("<tr><td class=\"rowhead\">������</td><td colspan=\"2\" align=\"left\"><input type=\"file\" name=\"avatar\" size=\"80\"></tr>\n");
    }
    */
    
    
  print("<tr><td class=\"rowhead\">�������� ������</td><td colspan=\"2\" align=\"left\"><input name=\"resetavatar\" value=\"1\" type=\"checkbox\"><i> ��������� ������� ������ � ���� ������</i></td></tr>\n");

  print("<tr><td class=\"rowhead\">�������� ����</td><td colspan=\"2\" align=\"left\"><input name=\"resetthemes\" value=\"yes\" type=\"checkbox\"><i> ���������� ���� �� ��������� [$default_theme]</i></td></tr>\n");
  
  if ($CURUSER["class"] > UC_MODERATOR){
  	
  	$ss_r=new MySQLCache("SELECT * FROM stylesheets ORDER by id ASC", 86400,"my_style.txt"); 
//$ss_r = sql_query("SELECT * FROM stylesheets ORDER by id ASC") or sqlerr(__FILE__, __LINE__);
$ss_sa = array();
//while ($ss_a = mysql_fetch_array($ss_r)) {
while ($ss_a=$ss_r->fetch_assoc()){
  $ss_id = $ss_a["id"];
  $ss_name = $ss_a["name"];
  $ss_sa[$ss_name] = $ss_id;
}
reset($ss_sa);
$stylesheets="";
while (list($ss_name, $ss_id) = each($ss_sa)) {
  if ($ss_name == $user["stylesheet"]) $ss = " selected"; else $ss = "";
  $stylesheets .= "<option value=\"$ss_name\"$ss>$ss_name</option>\n";
}

  print("<tr><td class=\"rowhead\">����� ���� ����������</td><td colspan=\"2\" align=\"left\"><select name=stylesheet>\n".$stylesheets."\n</select><i> ������� ����� ���� [".$user["stylesheet"]."]</i></td></tr>\n");
 	
	 }

if ($CURUSER["class"] == UC_SYSOP){
  print("<tr><td class=\"rowhead\">�������� ������</td><td colspan=\"2\" align=\"left\"><input name=\"resetsession\" value=\"1\" type=\"checkbox\"><i> ��������� ������� ������ � ���� ������</i></td></tr>\n");
}
	// we do not want mods to be able to change user classes or amount donated...
	if ($CURUSER["class"] < UC_ADMINISTRATOR)
	  print("<input type=\"hidden\" name=\"donor\" value=\"$user[donor]\">\n");
	else {
	  print("<tr><td class=\"rowhead\">�����</td><td colspan=\"2\" align=\"left\"><input type=\"radio\" name=\"donor\" value=\"yes\"" .($user["donor"] == "yes" ? " checked" : "").">�� <input type=\"radio\" name=\"donor\" value=\"no\"" .($user["donor"] == "no" ? " checked" : "").">���</td></tr>\n");


/// ������
$groups = "<option value=0>--- �� � ������ ----</option>\n";
//$res_groups = mysql_query("SELECT id,name FROM groups ORDER BY id") or die("������ FROM groups");
//while ($row_groups = mysql_fetch_array($res_groups)) 

$cache2=new MySQLCache("SELECT id,name FROM groups ORDER BY id", 86400); /// �� ���� ���

while ($row_groups=$cache2->fetch_assoc()) 

$groups .= "<option value=$row_groups[id]" . ($user["groups"] == $row_groups['id'] ? " selected" : "") . ">$row_groups[name]</option>\n";

if (get_user_class() >= UC_ADMINISTRATOR){

print("<tr><td class=\"rowhead\">������� ���</td>
		 <td colspan=\"2\" align=\"left\">        
        <input type=\"text\" size=\"20\" name=\"username\">
		<i> ����� ���� ������������, max �� 17 ��������</i>
		</td></tr>");  


if ($user["class"]=="4"){

///$r = sql_query("SELECT id,name FROM categories ORDER BY sort") or sqlerr(__FILE__, __LINE__);
$r=new MySQLCache("SELECT id,name FROM categories ORDER BY sort", 86400,"mydetails_category.txt");
$categories = "<b>��� ��������� �������</b>:<br />\n";
//if (mysql_num_rows($r) > 0)
//{
	print("<tr><td class=\"rowhead\">�� ����������</td>
 <td colspan=\"2\" align=\"left\"> ");
 
print"<table><tr>\n";
	$i = 0;
//	while ($a = mysql_fetch_assoc($r))
	while ($a=$r->fetch_assoc()){
	  print ($i && $i % 2 == 0) ? "</tr><tr>" : "";
	  print "<td class=bottom style='padding-right: 5px'><label><input name=cat$a[id] type=\"checkbox\" " . (strpos($user["catedit"], "[cat$a[id]]") !== false ? " checked" : "") . " value='yes'>&nbsp;" . htmlspecialchars($a["name"]) . "</label></td>\n";
	  ++$i;
	}
print"</tr></table>\n";
print("</td></tr>\n");
//}
}

print("<tr><td class=\"rowhead\">�������� �������</td>
		  <td colspan=\"2\" align=\"left\"> 
          <input type=checkbox name=reset_user_ratio value=1>
		  <i> ��������� ������� ���������� ���������� � ��������� �� �����</i>
		  </td></tr>\n");		
}

if (get_user_class() > UC_ADMINISTRATOR)
{

          
print("<tr><td class=\"rowhead\">�������� ���������</td>
		  <td colspan=\"2\" align=\"left\"> 
          <input type=checkbox name=reset_moderatedby value=1>
		  <i> ��������� ������� ��� �������� ��������� [������]</i>
		  </td></tr>\n");

print("<tr><td class=\"rowhead\">����� �������</td>
		  <td colspan=\"2\" align=\"left\"> 
          <input type=checkbox name=reset_user_torrents value=1>
		  <i> ��� �������� ������� � ��������� ����� ��������� � ����</i>
		  </td></tr>\n");
	
print("<tr><td class=\"rowhead\">��������� ������</td>
		  <td colspan=\"2\" align=\"left\"> 
          <input type=checkbox name=user_reliases_anonim value=1>
		  <i> ��� ������� �������� ���� ������������� ������ ��� ������</i>
		  </td></tr>\n");
	
print("<tr><td class=\"rowhead\">��������� �����</td> 
       	 <td colspan=\"2\" align=\"left\">������� ������������� (<b>id</b>) ������������� ��������:
       <input type=\"text\" size=\"8\" name=\"release_set_id\"></tr></td> 
      ");
print("<tr><td class=\"rowhead\">�������� �����</td> 
       	 <td colspan=\"2\" align=\"left\">������� ������������� (<b>id</b>) ������������� ��������:
	<input type=\"text\" size=\"8\" name=\"release_unset_id\"></tr></td>
		");

}
if (get_user_class() >= UC_ADMINISTRATOR){
print("<tr><td class=\"rowhead\">���� ������</td><td class=tablea colspan=2 align=left><select name=groups>\n$groups\n</select><i> ����� ����� � �������� � � ����</i></tr>\n");
}
/// ������
		  	
		print("<tr><td class=\"rowhead\">�������</td><td colspan=\"2\" align=\"left\"><input type=radio name=uploadpos value=yes" .($user["uploadpos"]=="yes" ? " checked" : "") . ">�� <input type=radio name=uploadpos value=no" .($user["uploadpos"]=="no" ? " checked" : "") . ">���</td></tr>\n");
        print("<tr><td class=\"rowhead\">����������</td><td colspan=\"2\" align=\"left\"><input type=radio name=downloadpos value=yes" .($user["downloadpos"]=="yes" ? " checked" : "") . ">�� <input type=radio name=downloadpos value=no" .($user["downloadpos"]=="no" ? " checked" : "") . ">���</td></tr>\n");

if (get_user_class() >= UC_ADMINISTRATOR){
	
	
 print("<tr><td class=rowhead>�������� ��</td><td colspan=2 align=left><input type=radio name=cansendpm value=yes" .($user["cansendpm"]=="yes" ? " checked" : "") . ">�� <input type=radio name=cansendpm value=no" .($user["cansendpm"]=="no" ? " checked" : "") . ">���</td></tr>\n");
 
 print("<tr><td class=rowhead>�������� ������������</td><td colspan=2 align=left><input type=radio name=commentpos value=yes" .($user["commentpos"]=="yes" ? " checked" : "") . ">�� <input type=radio name=commentpos value=no" .($user["commentpos"]=="no" ? " checked" : "") . ">���</td></tr>\n");
 
  print("<tr><td class=rowhead>�������� ��� <br> �� ������</td><td colspan=2 align=left>
 ".($user["forum_com"]<>"0000-00-00 00:00:00" ? "��� �� <b>".$user["forum_com"]."</b> <input type=checkbox name=forum_pos_off value=1><i>����� ��������� ��� �� ������</i>":"
 ��������� ����������� �������� ��������� �� <select name=\"forum_pos\">
    <option value=\"0\">��������</option>
    <option value=\"1\">1 ������</option>
    <option value=\"2\">2 ������</option>
    <option value=\"3\">3 ������</option>
    <option value=\"4\">4 ������</option>
    <option value=\"5\">5 ������</option>
    <option value=\"6\">6 ������</option>
    <option value=\"7\">7 ������</option>
    <option value=\"8\">8 ������</option>
    <option value=\"9\">9 ������</option>
    <option value=\"10\">10 ������</option>
    <option value=\"11\">11 ������</option>
    <option value=\"12\">12 ������</option>
    <option value=\"64\">����� ����� ���</option>
    </select>
 ")."  
  </td></tr>\n");
}

   print("<tr><td class=\"rowhead\">������ � ����</td><td colspan=\"2\" align=\"left\">
   
    ".($user["shoutbox"]<>"0000-00-00 00:00:00" ? "��������� �� <b>".$user["shoutbox"]."</b> <input type=checkbox name=chat_pos_off value=1><i>����� ��� �� ���</i>":"
 ��������� ����������� �������� ��������� �� <select name=\"schoutboxpos\">
    <option value=\"0\">��������</option>
    <option value=\"1\">1 ����</option>
    <option value=\"2\">2 ���</option>
    <option value=\"3\">3 ���</option>
    <option value=\"4\">4 ���</option>
    <option value=\"5\">5 ����</option>
    <option value=\"6\">6 ����</option>
    <option value=\"7\">7 ����</option>
    <option value=\"14\">2 ������</option>
    <option value=\"21\">3 ������</option>
    <option value=\"28\">4 ������</option>
    <option value=\"35\">5 ������</option>
    <option value=\"42\">6 ������</option>
    <option value=\"49\">7 ������</option>
    <option value=\"65535\">����� ����� ���</option>
    </select>
 ")."
</td></tr>\n");
}
	   
	   /// ������� - 100 - ����������� ��� (Update)
		if (get_user_class() >= UC_SYSOP) {
    $hiderating = $user["hiderating"] == "yes"; 
     print("<tr><td class=\"rowhead\" " . (!$hiderating ? " rowspan=2": "") . "><b>������ �������</b></td> 
     <td class=tablea align=left width=20%>" . ( $hiderating ? "<input name=hiderating value='yes' type=radio checked>�� <input name=hiderating value='no' type=radio>���" : "���" ) ."</td>"); 
    if ($hiderating)
    {
        $hideratinguntil = $user['hideratinguntil']; 
        if ($hideratinguntil == '0000-00-00 00:00:00') 
            print("<td class=tablea align=center>(<b>��������� �������������� �����!</b>)</td></tr>\n"); 
        else 
        { 
            print("<td class=tablea align=center>�� $hideratinguntil"); 
            print(" (�������� " . mkprettytime(strtotime($hideratinguntil) - time()) . ")</td></tr>\n"); 
       } 
    }
  else
  {
    print("<td class=table>������ ������� �� <select name=hidelength>
    \n");
    print("<option value=0>�� ���������</option>\n");
    print("<option value=1>01 �����</option>\n");
    print("<option value=2>02 ������</option>\n");
    print("<option value=3>03 ������</option>\n");
    print("<option value=4>04 ������</option>\n");
    print("<option value=5>05 �������</option>\n");
    print("<option value=6>06 �������</option>\n");
    print("<option value=7>07 �������</option>\n");
    print("<option value=8>08 �������</option>\n");
    print("<option value=9>09 �������</option>\n");
    print("<option value=10>10 �������</option>\n");
    print("<option value=11>11 �������</option>\n");
    print("<option value=12>12 �������</option>\n");
    print("<option value=255>�������������� �����</option>\n");
    print("</select></td></tr>\n");
    print("<tr>");
   print("</select></tr></td></tr>\n");
  }
  
  
  if ($user["override_class"]<>255){
    print("<tr><td class=\"rowhead\">�������� ���� �����</td><td colspan=\"2\" align=\"left\"><input name=\"resettestclass\" value=\"yes\" type=\"checkbox\"></td></tr>\n");
  }
  
}

	   
    
  if ($CURUSER["id"] <> $user["id"])
   {
	if (get_user_class() == UC_MODERATOR && $user["class"] > UC_UPLOADER)
	  print("<input type=\"hidden\" name=\"class\" value=\"$user[class]\">\n");
	else
	{
	  print("<tr><td class=\"rowhead\">�����</td><td colspan=\"2\" align=\"left\"><select name=\"class\">\n");
	  if (get_user_class() == UC_SYSOP)
	  	$maxclass = UC_ADMINISTRATOR;
	  elseif (get_user_class() == UC_MODERATOR)
	    $maxclass = UC_UPLOADER;
	    
	// �������� 
	elseif (get_user_class() == UC_ADMINISTRATOR)
	    $maxclass = UC_MODERATOR;
	    	// �������� 
	
	  else
	    $maxclass = get_user_class() - 1;
	  for ($i = 0; $i <= $maxclass; ++$i)
	    print("<option value=\"$i\"" . ($user["class"] == $i ? " selected" : "") . ">$prefix" . get_user_class_name($i) . "\n");
	  print("</select>
	  ".($user["promo_time"]=="0000-00-00 00:00:00" ? "":" � ".$user["promo_time"])."
	  <input title=\"��������� ����� �� system\" type=radio name=ls_system value=yes" .($CURUSER["class"]>"5" ? " checked" : "") . ">System <input title=\"��������� ����� �� ���\" type=radio name=ls_system value=no" .($CURUSER["class"]<="5"? " checked" : "") . ">" . get_user_class_color($CURUSER["class"], $CURUSER["username"])."
	   </td></tr>\n");
	}
   }
   
	print("<tr><td class=\"rowhead\">�������� ���� ��������</td><td colspan=\"2\" align=\"left\"><input type=\"radio\" name=\"resetb\" value=\"yes\">�� <input type=\"radio\" name=\"resetb\" value=\"no\" checked>���</td></tr>\n");
	
	
	print("<tr><td class=\"rowhead\">������� �����������</td><td colspan=\"2\" align=\"left\"><input type=radio name=parked value=yes" .($user["parked"]=="yes" ? " checked" : "") . ">�� <input type=radio name=parked value=no" .($user["parked"]=="no" ? " checked" : "") . ">���</td></tr>\n");
	
		print("<tr><td class=\"rowhead\">������ �����������</td><td colspan=\"2\" align=\"left\"><input type=radio name=hidecomment value=yes" .($user["hidecomment"]=="yes" ? " checked" : "") . ">�� <input type=radio name=hidecomment value=no" .($user["hidecomment"]=="no" ? " checked" : "") . ">���</td></tr>\n");
	
	
	
	$supportfor = htmlspecialchars($user["supportfor"]);

	print("<tr><td class=rowhead>��� ���������</td><td colspan=2 align=left><input type=radio name=support value=yes" .($user["support"] == "yes" ? " checked" : "").">�� <input type=radio name=support value=no" .($user["support"] == "no" ? " checked" : "").">��� <i>(���� ������� �������� ���������)</i></td></tr>\n");
	print("<tr><td class=rowhead>�������� ���������: <br>[".strlen($supportfor)."]</td><td colspan=2 align=left><textarea cols=100% rows=4 name=supportfor>".$supportfor."</textarea></td></tr>\n");
	
	
	if ($user["notif_access"]<>"0000-00-00 00:00:00")
	echo ("<tr><td class=rowhead>����������</td><td colspan=2 align=left>��������� ������ �� ����� <i>(� ������������ ��������..)</i> ���� ����������: <b>".$user["notif_access"]."</b>.</td></tr>\n");
		
		$usercomment = htmlspecialchars($user["usercomment"]);
	if (get_user_class() > UC_MODERATOR && !empty($usercomment)){

    if (strlen($usercomment)>450)
	$stl_size2=round(strlen($usercomment)/450)+7;
	else
	$stl_size2=7;
	
	print("<tr><td class=rowhead>������� � ������������ <br>[".strlen($usercomment)."]</td><td colspan=2 align=left><textarea cols=100% rows=$stl_size2".(get_user_class() < UC_SYSOP ? " readonly" : " name=usercomment").">$usercomment</textarea></td></tr>\n");
	}
	
    $modcomment = htmlspecialchars($user["modcomment"]);
    if (strlen($modcomment)>450)
	$stl_size=round(strlen($modcomment)/450)+7;
	else
	$stl_size=7;
	
	print("<tr><td class=rowhead>������� ������������ <br>[".strlen($modcomment)."]</td><td colspan=2 align=left><textarea cols=100% rows=$stl_size".(get_user_class() < UC_SYSOP ? " readonly" : " name=modcomment").">$modcomment</textarea></td></tr>\n");
	print("<tr><td class=rowhead>�������� �������</td><td colspan=2 align=left><textarea cols=100% rows=3 name=modcomm></textarea></td></tr>\n");

if (get_user_class() > UC_MODERATOR){
//����������
$bookmcomment = htmlspecialchars($user["bookmcomment"]);
    if (strlen($bookmcomment)>250)
	$stl_book=round(strlen($bookmcomment)/250)+5;
	else
	$stl_book=5;

print("<tr><td class=rowhead>���������� <br>[".strlen($bookmcomment)."]</td><td colspan=2 align=left><input type=radio name=addbookmark value=yes" .($user["addbookmark"] == "yes" ? " checked" : "").">�� <input type=radio name=addbookmark value=no" .($user["addbookmark"] == "no" ? " checked" : "").">��� 
<br><textarea cols=100% rows=$stl_book name=bookmcomment>$bookmcomment</textarea></td>
</tr>\n");
//print("<tr><td class=rowhead>������� ����������:</td><td  colspan=2 align=left><textarea cols=60 rows=6 name=bookmcomment>$bookmcomment</textarea></td></tr>\n");  
//����������
}

	$warned = $user["warned"] == "yes";
 	print("<tr><td class=\"rowhead\"" . (!$warned ? " rowspan=\"2\"": "") . ">��������������</td>
 	<td align=\"left\" width=\"20%\">" .
  ( $warned  ? "<input name=\"warned\" value=\"yes\" type=\"radio\" checked>�� <input name=\"warned\" value=\"no\" type=\"radio\">���" : "���" ) ."</td>");

	if ($warned) {
		$warneduntil = $user['warneduntil'];
		if ($warneduntil == '0000-00-00 00:00:00')
    		print("<td align=\"center\">�� ������������� ����</td></tr>\n");
		else {
    		print("<td align=\"center\">�� $warneduntil");
	    	print(" (" . mkprettytime(strtotime($warneduntil) - gmtime()) . " ��������)</td></tr>\n");
 	    }
  } else {
    print("<td>������������ �� <select name=\"warnlength\">\n");
    print("<option value=\"0\">------</option>\n");
    print("<option value=\"1\">1 ������</option>\n");
    print("<option value=\"2\">2 ������</option>\n");
    print("<option value=\"4\">4 ������</option>\n");
    print("<option value=\"8\">8 ������</option>\n");
    print("<option value=\"255\">������������</option>\n");
    print("</select></td></tr>\n");
    print("<tr><td colspan=\"2\" align=\"left\">����������� � �����: <input type=\"text\" size=\"60\" name=\"warnpm\">
	<input title=\"��� ����������� � �����\" name=\"warn_msg\" value=\"1\" type=\"checkbox\">
	</td></tr>");
  }
    
  if (5-$user["num_warned"]>0)
  $mes_warn = "������� <b>".(5-$user["num_warned"])."</b> �������������� ������������ ������������� � ��������� $autoclean_interval ���."; 

  print("<tr><td class=\"rowhead\">��������<br>��������������</td><td colspan=\"2\" align=\"left\"><img src=\"pic/plus.gif\" id=\"warnpic\" onClick=\"togglepic('$DEFAULTBASEURL','warnpic','warnchange')\" style=\"cursor: pointer;\">&nbsp;<input type=\"text\" name=\"amountwarn\" size=\"3\" />&nbsp;$mes_warn</td></tr>");
  
?>
<script type="text/javascript">
function togglepic(bu, picid, formid)
{
    var pic = document.getElementById(picid);
    var form = document.getElementById(formid);
  if(pic.src == bu + "/pic/plus.gif")
    { pic.src = bu + "/pic/minus.gif"; form.value = "minus"; }
  else
	{ pic.src = bu + "/pic/plus.gif"; form.value = "plus"; }
}
</script>
<?

if ($user["hiderating"] <> "yes") {

  echo("<tr><td class=\"rowhead\">�������� �������</td><td align=\"left\"><img src=\"pic/plus.gif\" id=\"uppic\" onClick=\"togglepic('$DEFAULTBASEURL','uppic','upchange')\" style=\"cursor: pointer;\">&nbsp;<input type=\"text\" name=\"amountup\" size=\"10\" /><td>\n<select name=\"formatup\">\n<option value=\"mb\">��������</option>\n<option value=\"gb\">��������</option></select> <i>�������� / ������ ���������� �������� �������</i> </td></tr>");
  
  echo("<tr><td class=\"rowhead\">�������� ������</td><td align=\"left\"><img src=\"pic/plus.gif\" id=\"downpic\" onClick=\"togglepic('$DEFAULTBASEURL','downpic','downchange')\" style=\"cursor: pointer;\">&nbsp;<input type=\"text\" name=\"amountdown\" size=\"10\" /><td>\n<select name=\"formatdown\">\n<option value=\"mb\">��������</option>\n<option value=\"gb\">��������</option></select> <i>�������� / ������ ���������� ������������ �������</i> </td></tr>");

}
  
  
//�����������
 print("<tr><td class=\"rowhead\">�������� ���������� �����������</td><td align=\"left\"><img src=\"pic/plus.gif\" id=\"invitepic\" onClick=\"togglepic('$DEFAULTBASEURL','invitepic','invitechange')\" style=\"cursor: pointer;\">&nbsp;<input type=\"text\" name=\"amountinvite\" size=\"10\" /><td>\n <i>�������� / ������ ���������� �����������</i> </td></tr>");
//�����������


  print("<tr><td class=\"rowhead\">�������� passkey</td><td colspan=\"2\" align=\"left\"><input name=\"resetkey\" value=\"1\" type=\"checkbox\"><i> ������ ������ �� ����� (�������� ���������� ��������)</i></td></tr>\n");

  
//������ ����� � ������ 

if ($CURUSER["class"] == UC_SYSOP){


	print("<tr><td class=\"rowhead\">���������</td>
	  <td colspan=\"3\" align=\"left\">
	  
	  <input type=\"checkbox\" name=\"trun_inbox\"><i> ��� �������� ��������� </i>&nbsp; <input type=\"checkbox\" name=\"trun_outbox\"><i> ��� ������������ ��������� </i>&nbsp; <br>
	
	  <input type=\"checkbox\" name=\"trun_news\"><i> ��� ��������� ����������� </i>&nbsp; <input type=\"checkbox\" name=\"trun_polls\"><i> ��� �����. �������� </i>&nbsp; <br>

 <input type=\"checkbox\" name=\"trun_torrent\"><i> ��� ������� ����������� </i>&nbsp; <input type=\"checkbox\" name=\"trun_reqoff\"><i> ��� ������ ����������� </i>&nbsp; <br>
	  </td></tr>");




print("<tr><td class=\"rowhead\">�������� ������</td><td colspan=\"2\" align=\"left\"><input name=\"resetpass\" value=\"1\" type=\"checkbox\"><i> �� ����� ���������� ����� 15 ���������� ������</i></td></tr>\n");

print("<tr><td class=\"rowhead\">����������</td><td colspan=\"2\" align=\"left\">
<input type=radio name=monitoring value=yes" .($user["monitoring"]=="yes" ? " checked" : "") . ">��<input type=radio name=monitoring value=no" .($user["monitoring"]=="no" ? " checked" : "") . ">��� 
".($user["monitoring"]=="no" ? "<input type=\"text\" name=\"monitoring_reason\" size=\"35\" /> <i>������� �������� </i>" : "")."
</td></tr>\n");


print("<tr><td class=\"rowhead\">�������� ������</td>
		  <td colspan=\"2\" align=\"left\"> 
          <input type=checkbox name=prune_monitoring value=yes>
		  <i> ������ ����� �� ������� ������������ [��������� ������]</i>
		  </td></tr>\n");
}

  if ($CURUSER["id"]<>$user["id"]) {
    print("<tr><td class=\"rowhead\" rowspan=\"2\">�������</td><td colspan=\"2\" align=\"left\"><input name=\"enabled\" value=\"yes\" type=\"radio\"" . ($enabled ? " checked" : "") . ">�� <input name=\"enabled\" value=\"no\" type=\"radio\"" . (!$enabled ? " checked" : "") . ">���
".(get_user_class() == UC_SYSOP ? " +<input name=\"desysteml\" value=\"yes\" type=\"checkbox\"> �������� (������) ������� ����� $autoclean_interval ���":"")."
</td></tr>\n");

    if ($enabled)
    	print("<tr><td colspan=\"2\" align=\"left\">������� ����������:&nbsp;<input type=\"text\" name=\"disreason\" size=\"60\" /></td></tr>");
	else
		print("<tr><td colspan=\"2\" align=\"left\">������� ���������:&nbsp;<input type=\"text\" name=\"enareason\" size=\"60\" /></td></tr>");

		}


if ($CURUSER["class"] < UC_ADMINISTRATOR)
echo "<input type=\"hidden\" name=\"deluser\">";
  else
echo "<tr><td class=\"rowhead\">�������� ��������</td>
<td colspan=\"2\" align=\"left\">
<input type=\"checkbox\" name=\"deluser\"> <i>��������� ������� ������� ������</i><br>
<input title=\"�������� ������ ��� ������� ���� (�� �������� �������)\" type=\"checkbox\" checked name=\"banemailu\"> <i>�������� email (����� ��������� ����������� �� ������ �����)</i><br>
<input type=\"text\" name=\"delreason\" size=\"60\" />&nbsp;<i>������� ��������</i><br>
<b>��������</b>: �������� <u>������ � ������� ������</u>, �� ����������� ���������� �������, ��� ����� �������� � <u>������������</u>, ����������, <u>������</u> ���������� � ��������� ���� �����������.
</td></tr>";


  print("</td></tr>");
  print("<tr><td colspan=\"3\" align=\"center\"><input type=\"submit\" class=\"btn\" value=\"�������������\"></td></tr>\n");
  print("</table>\n");
  //print("<input type=\"hidden\" id=\"upchange\" name=\"upchange\" value=\"plus\"><input type=\"hidden\" id=\"downchange\" name=\"downchange\" value=\"plus\">\n");
    
    if ($user["hiderating"] <> "yes") {
   
    echo("<input type=\"hidden\" id=\"upchange\" name=\"upchange\" value=\"plus\"><input type=\"hidden\" id=\"downchange\" name=\"downchange\" value=\"plus\">");
    
	echo("<input type=\"hidden\" id=\"warnchange\" name=\"warnchange\" value=\"plus\">\n"); 
    
	
    }
  echo("<input type=\"hidden\" id=\"invitechange\" name=\"invitechange\" value=\"plus\">\n");
  echo("</form></span></p>\n");
  end_frame();
}
end_main_frame();
stdfoot(); 
?>