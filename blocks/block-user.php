<?
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

if (fuckIE()==true){
$browser = 'Internet Explorer'; 

$content.="<center><i title=\"������� ������� �� �����, ����� ����������.\">����������, �� �����������</i> <b title=\"� ��������� IE �������� ��������� � ������ ��������, �� ����������� ��������� ��� �� ������. � �������: Opera, Mozilla, Safari.\">".$browser."</b></center><br>";
}

print"<style type=\"text/css\">
<!--
input.pass { background: url(pic/contact/pass.gif) no-repeat; background-color: #fff;  background-position: 0 50%; color: #000; padding-left: 18px; }
input.login { background: url(pic/contact/login.gif) no-repeat;  background-color: #fff; background-position: 0 50%; color: #000; padding-left: 18px; }
-->
</style>";

global $CURUSER, $DEFAULTBASEURL, $tracker_lang, $maxloginattempts;
$ip = getip();

if ($CURUSER) {

$uped = mksize($CURUSER['uploaded']);
$downed = mksize($CURUSER['downloaded']);

if ($CURUSER["downloaded"] > 0) {
$ratio = $CURUSER['uploaded'] / $CURUSER['downloaded'];
$ratio = number_format($ratio, 3);
$color = get_ratio_color($ratio);
if ($color)
$ratio = "<font color=$color>$ratio</font>";
}
else
if ($CURUSER["uploaded"] > 0)
$ratio = "Inf.";
else
$ratio = "---";

?>
<style>
.effect {FILTER: alpha(opacity=50); -moz-opacity: .50; opacity: .70;}
</style>
<?

$content .= "<center><a href=\"$DEFAULTBASEURL/my.php\"><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" src=\"$DEFAULTBASEURL/pic/avatar/" . ( $CURUSER["avatar"] ? $CURUSER["avatar"] : 	"default_avatar.gif") . "\" width=\"100\" alt=\"".$tracker_lang['click_on_avatar']."\" title=\"".$tracker_lang['click_on_avatar']."\" border=\"0\" /></a></center>";

if ($CURUSER && $CURUSER["override_class"]<>"255")
$content .="<center><nobr><b>[</b>������� �����<b>]</b><br><b>" . get_user_class_color($CURUSER["override_class"], get_user_class_name($CURUSER['override_class']))."</b></nobr></center><br>";

?>

<script>
function user_online() {
jQuery.post("block-user_jquery.php" , {} , function(response) {
		jQuery("#user_online").html(response);
	}, "html");
setTimeout("user_online();", 60000);
}
user_online();
</script>
<?

$content.='<nobr><div align="center" id="user_online">...</div></nobr>';


//$content .= ($newmessageview? "<center>$newmessageview</center>":"");
//$content .= "��� UID: ".$CURUSER['id']."<br>";
if ($CURUSER['username']<>"�������@��")
$content .= "�����: &nbsp;".get_user_class_color($CURUSER['class'] ,get_user_class_name($CURUSER['class']))."";

/*$content .= "<font color=\"#0670ca\">".$tracker_lang['ratio'].":</font>&nbsp;$ratio<br />
<font color=\"green\">".$tracker_lang['uploaded'].":</font>&nbsp;$uped<br />
<font color=\"red\">".$tracker_lang['downloaded'].":</font>&nbsp;$downed<br />
";*/


if ($CURUSER["class"] >= UC_MODERATOR and ($CURUSER['override_class'] == 255)) {
 	$content .= "<a href=\"$DEFAULTBASEURL/setclass.php\"><img style=\"border:none\" alt=\"�������������� ������ �����\" src=\"pic/forum.gif\"></a>";
}

if ($CURUSER['override_class'] <> 255 && $CURUSER){
$content .= "<a href=\"$DEFAULTBASEURL/restoreclass.php\"><img style=\"border:none\" title=\"".$tracker_lang['lower_class']."\" src=\"pic/megs.gif\"></a>";
}
 
if ($CURUSER["hiderating"] == "yes") {

  $content .= "<div align=left>
  <font color=1900D1>�������: </font><font color=ff1ac6><b>+100%</b></font><br>";
    $hideratinguntil = $CURUSER['hideratinguntil']; 
      
	     /*
	if ($hideratinguntil == '0000-00-00 00:00:00')
       { 
       	$content .= "<font color=104706>- �� ������� �����!!!<font color=660000>)</font>\n";
		   }
     
    else 
       {
       $content .= "�� <font color=#ff8ce3>$hideratinguntil</font></b><br>"; 
       }
	   */
}

else

$content .= "<br>
<font color=1900D1>�������: ".$ratio."</font><br>
<font color=green>������: ".$uped."</font><br>
<font color=darkred>������: ".$downed."</font><br>

".($CURUSER["bonus"] == "0.00" ? "<font color=\"#0670ca\">������:&nbsp;$CURUSER[bonus]</font>" : "<font color=\"#0670ca\">������:&nbsp;<a  title=\"������� ���� ��� ������ ����� �������\" href=\"$DEFAULTBASEURL/mybonus.php\">$CURUSER[bonus]</a></font><br/>")."\n";

///////////////

?>

<script>
function usersen_online() {
jQuery.post("block-userse_jquery.php" , {} , function(response) {
		jQuery("#usersen_online").html(response);
	}, "html");
setTimeout("usersen_online();", 180000);
}
usersen_online();
</script>
<?

$content.='<div id="usersen_online"></div>';

///////////////

if (!empty($CURUSER["invites"])){
$content .= "<font color=\"DarkCyan\">�����������: <a href=\"$DEFAULTBASEURL/invite.php\">" .$CURUSER["invites"]. "</a> ��</font><br />\n";
}

if (!empty($CURUSER["unmark"])){
$content .= "<font title=\"�� ������� �����, �� �� ������� �� (�� ���� ������).\"  color=\"#A0522D\">�������: <a href=\"".$DEFAULTBASEURL."/rating.php\">" .$CURUSER["unmark"]. "</a> ��</font><br />\n";
}

if (!empty($CURUSER["unseed"])){
$content .= "<font title=\"���� ����� ������, � �� �� ������� ���������, �������� ������������� ���� �� ��������.\"  color=\"#FF552A\">�������������: <a href=\"$DEFAULTBASEURL/upload.php\">" .$CURUSER["unseed"]. "</a> ��</font><br />\n";
}

if (empty($CURUSER["question"])&& empty($CURUSER["rejoin"])){
$content .= "<font title=\"������������ ��� �������������� ������ �� �����\" color=\"purple\">������. ������: <a title=\"��������� ���� ��������� ������ � ����� � ���� ��������\" href=\"$DEFAULTBASEURL/my.php#question\">���</a></font><br>\n";
}

if ($CURUSER["num_warned"]<>"0"){

for($i = 0; $i < $CURUSER["num_warned"]; $i++)
{
if ($i==0) $i2="������";
if ($i==1) $i2="������";
if ($i==2) $i2="������";
if ($i==3) $i2="�������� (�������������)";
if ($i==4) $i2="����� (��������� ���������)";


$img .= "<a href=\"mywarned.php\"  target=\"_blank\"><img src=\"$DEFAULTBASEURL/pic/warned.gif\" style=\"border:none\" alt=\"$i2 ������� ��������������\" title=\"$i2 ������� ��������������\"></a>"; 
}
$num_warned_view= "<center>$img</center>\n";
}


?>

<script>
function userseed_online() {
jQuery.post("block-userseed_jquery.php" , {} , function(response) {
		jQuery("#userseed_online").html(response);
	}, "html");
setTimeout("userseed_online();", 180000);
}
userseed_online();
</script>
<?

$content.='<div id="userseed_online"></div>';



/*
if ($CURUSER["class"] >= UC_MODERATOR){
///////////////

?>

<script>
function usermod_online() {
jQuery.post("block-usermod_jquery.php" , {} , function(response) {
		jQuery("#usermod_online").html(response);
	}, "html");
setTimeout("usermod_online();", 90000);
}
usermod_online();
</script>
<?


$content.='<div id="usermod_online"></div>';
};
*/


$content.="<center><form method=\"post\" action=\"\" name=\"jump\"><select name=id_styles onchange=\"if(this.options[this.selectedIndex].value != -1){forms['jump'].submit()}\">\n";
 	
$cache=new MySQLCache("SELECT * FROM stylesheets ORDER by id ASC", 86400,"my_style.txt"); 
while ($arr=$cache->fetch_assoc()){

$content.="<option value=" . $arr["id"] . ($CURUSER["stylesheet"] == $arr["name"] ? " selected>" : ">") . $arr["name"] . "\n";
}
$content.="</select>\n";
$content.="</form></center>";



$content .= $num_warned_view."<center><form method=\"post\" action=\"logout.php\"><input onClick=\"return confirm('���������� ������? ���������� �����������.')\" type=\"submit\" style=\"width: 100px\" class=\"btn\" value=\"".$tracker_lang['logout']."\"></form></center>";
}
else
{
global $maxlogin;
if ($maxlogin==1) {
$remaining=remaining();
{
if ($remaining>"0") {
$rema = "<b>[$remaining]</b> <= <b>[$maxloginattempts]</b>";
}
else
$rema = "<b>��� ".getip()." ������� <br> ���� � ����� ip ������ ��������</b>";
$content = "<center>$rema</center>";
}
}

//������� �����: <br>
if (!empty($_GET["returnto"]))
$returnto = htmlspecialchars($_GET["returnto"]);

if (isset($returnto))
$return_inp="<input type=\"hidden\" name=\"returnto\" value=\"" . htmlentities($returnto) . "\" />";

$agent = $_SERVER["HTTP_USER_AGENT"];
$hache = crc32(htmlentities($agent));

if (!empty($ip) && !empty($_COOKIE["PHPSESSID"]) && !empty($hache)){

$dti = get_date_time(gmtime() - 1209600);//// 2 ������

$du = sql_query("SELECT id,username,class FROM users WHERE crc=".sqlesc($hache)." AND ip=".sqlesc($ip)." AND last_access > ".sqlesc($dti)." LIMIT 1") or sqlerr(__FILE__,__LINE__);
$rodu = mysql_fetch_array($du); 
$value = " value=\"".$rodu['username']."\"";
$login = "<a href=\"userdetails.php?id=".$rodu["id"]."\">".get_user_class_color($rodu["class"], $rodu["username"])."</a>";
}

$content = "<center>$rema<form name=\"mainForm\" id=\"o_O\" method=\"post\" action=\"takelogin.php\"><br />
<b>".$tracker_lang['username']."</b>: ".$login."<br />
<input id=\"nickname\" type=\"text\" size=18 name=\"username\" ".$value." class=\"login\"/><br />
<b>".$tracker_lang['password']."</b>: <br />
<input id=\"password\" type=\"password\" size=18 name=\"password\" class=\"pass\" /><br />$return_inp<br />
<input type=\"submit\" class=\"btn\" value=\"������� ����!\">
</form>
<br />
<a class=\"menu\" href=\"signup.php\">".$tracker_lang['signup']."</a>
<a class=\"menu\" href=\"recover.php\">� ����� ������!</a>
</center>";

}

$blocktitle = "<center>".$tracker_lang['welcome_back'].( $CURUSER ? "<a class=\"copyright\" href=\"$DEFAULTBASEURL/userdetails.php?id=" . $CURUSER["id"] . "\">" . $CURUSER["username"]. "</a>" : "�����" );?>