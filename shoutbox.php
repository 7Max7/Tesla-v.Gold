<?

/*
// ��������� ������� �����
$start_time = microtime();
// ��������� ������� � ������������
//(���������� ���������� ��������� ������ �������-������)
$start_array = explode(" ",$start_time);
// ��� � ���� ��������� �����
$start_time = $start_array[1] + $start_array[0];
*/
$on_vika=true;



require_once("include/bittorrent.php");
///require_once("include/init.php");
dbconn();
///loggedinorreturn();
header("Content-Type: text/html; charset=Windows-1251");

//die("��������� ����� 10 �����.");
if ($CURUSER["shoutbox"] <> '0000-00-00 00:00:00'){
die("��������� ������������ ��� �� <b>".$CURUSER["shoutbox"]."</b>");
}

if (!$CURUSER){
die("�������������");
}


function decode_unicode_url($str) {
$res = '';    $i = 0;
$max = strlen($str) - 6;
while ($i <= $max) {
$character = $str[$i];
if ($character == '%' && $str[$i + 1] == 'u') {
$value = hexdec(substr($str, $i + 2, 4));
$i += 6;
if ($value < 0x0080) // 1 byte: 0xxxxxxx
$character = chr($value);
else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
$character =chr((($value & 0x07c0) >> 6) | 0xc0).chr(($value & 0x3f) | 0x80);
else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
$character =chr((($value & 0xf000) >> 12) | 0xe0).chr((($value & 0x0fc0) >> 6) | 0x80).chr(($value & 0x3f) | 0x80);
} else $i++;
$res .= $character;
}
return $res . substr($str, $i);
}

function convert_text($s){
$out = "";
for ($i=0; $i<strlen($s); $i++) {
$c1 = substr ($s, $i, 1);
$byte1 = ord ($c1);
if ($byte1>>5 == 6) // 110x xxxx, 110 prefix for 2 bytes unicode
{ 
$i++;
$c2 = substr ($s, $i, 1);
$byte2 = ord ($c2);
$byte1 &= 31; // remove the 3 bit two bytes prefix
$byte2 &= 63; // remove the 2 bit trailing byte prefix
$byte2 |= (($byte1 & 3) << 6); // last 2 bits of c1 become first 2 of c2
$byte1 >>= 2; // c1 shifts 2 to the right
$word = ($byte1<<8) + $byte2;
if ($word==1025) $out .= chr(168);                    // ?
elseif ($word==1105) $out .= chr(184);                // ?
elseif ($word>=0x0410 && $word<=0x044F) $out .= chr($word-848); // ?-? ?-?
else {
$a = dechex($byte1);
$a = str_pad($a, 2, "0", STR_PAD_LEFT);
$b = dechex($byte2);
$b = str_pad($b, 2, "0", STR_PAD_LEFT);
$out .= "&#x".$a.$b.";";
}} else  {
$out .= $c1;
 }
}
return $out;
}


if (strlen($_GET["shout"])>=1000)
$_GET["shout"] = substr($_GET["shout"],0,1000);


$botid = 92; /// id ������ ����

$init_bonus=1; /// ������� ���� ������ �� 1 ����� ��� ���������
$first_answ=180; // ��������� 3 ������ �� �������
$medium_answ=180; // ������� ����� ��� ������� �������� (��������)
$prome=30; /// �������� ����� �������� ����� �������� 30 ���
$hint_time_f=30; /// �������� ����� ����� 1 ���������
$hint_time_t=30; /// �������� ����� ����� 2 ���������


$sq_zad=sql_query("SELECT vi.*, v.question,v.answer FROM victorina AS vi
LEFT JOIN vquestions AS v ON v.id=vi.question_id
WHERE vi.id=1") or sqlerr(__FILE__, __LINE__); 
$zad_bot=mysql_fetch_array($sq_zad);

$id_zad=$zad_bot["question_id"];
$begin_time_zad=$zad_bot["begin_time"];
$end_time_zad=$zad_bot["end_time"];
$num=$zad_bot["num"];
$all_bonus=$zad_bot["all_bonus"]; /// ������� ����� ������� ����������
$question_zad=$zad_bot["question"]; // ������
$answer_zad=tolower($zad_bot["answer"]); // ����� �� ����
$ansstr_zad="[".strlen($zad_bot["answer"])." ����(�)]"; /// ������� ��������


$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $first_answ); /// ��������� 3 ������
$quest_id=mt_rand(1, $zad_bot["numque"]);

$quest_id2=mt_rand(2, $zad_bot["numque"]);

//// ��������� ����� �������� � ���� ���� ��� �����
if (empty($zad_bot["answer"]) && $zad_bot["enable"]=="on"){
sql_query("UPDATE victorina SET hint='first', enable='on', begin_time=".sqlesc($begin_time).", end_time=".sqlesc($end_time).", question_id=".sqlesc($quest_id2).", num=num+1 WHERE id=1");
//header("Refresh: 0; url=tracker-chat.php");
die("����������� ������ ������� � id $quest_id2 <script>setTimeout('document.location.href=shoutbox.php', 3);</script>");
}
//// ��������� �������� � ���� ���� ��� �����


$shout = str_replace("+", "%2B", (isset($_GET["shout"]) ? $_GET["shout"]:""));
$shout=convert_text(urldecode(decode_unicode_url($shout)));


if ($end_time_zad <= get_date_time() && $zad_bot["enable"]=="wait" && empty($shout)){

if ($zad_bot["hint"]=="first"){

$word=$answer_zad;
$strlen=strlen($word);
$str = $word;
$strlen_min2=$strlen-1;
//$strlast = substr($str, -1); /// ���� �����
$strfirst = substr($str, 0, 1); /// ������ �����
$view="[b]".$strfirst."[/b]";
for ($x=0; $x<$strlen_min2; $x++){
$view.="#";
}
///$view.="[b]".$strlast."[/b]";

$newshout = "[color=gray]��������� #1/2[/color]: $view (+$hint_time_f ���)";
$date = time()+1; 

$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $hint_time_f); /// ��������� ��� ������
sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 
	
sql_query("UPDATE victorina SET enable='wait', end_time=" . sqlesc($end_time) . ", hint='second' WHERE id=1");	
}
elseif ($zad_bot["hint"]=="second"){

$word=$answer_zad;
$strlen=strlen($word);
$str = $word;
$strlen_min2=$strlen-2;
$strlast = substr($str, -1); /// ������ �����
$strfirst = substr($str, 0, 1); /// ���� �����
$view2="[b]".$strfirst."[/b]";
for ($x=0; $x<$strlen_min2; $x++){
$view2.="#";
}
$view2.="[b]".$strlast."[/b]";

$newshout = "[color=gray]��������� #2/2[/color]: $view2 (+$hint_time_t ���)";
$date = time()+1; 

$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $hint_time_t); /// ��������� ��� ������
sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 
	
sql_query("UPDATE victorina SET enable='wait', end_time=".sqlesc($end_time).", hint='third' WHERE id=1");
}

elseif ($zad_bot["hint"]=="third"){
/////// ��������� ��������� ������ ��������� �����
$newshout = "[color=gray]���������� �����[/color]: [b]$answer_zad [/b] | ��������� ��������� (�������: $num. ���������� �������: $all_bonus)";
$date = time()+5; 
sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 

sql_query("UPDATE victorina SET enable='off', hint='first', num='0',all_bonus='0.00', question_id='0' WHERE id=1") or sqlerr(__FILE__, __LINE__); 	
}

unset($shout);
}
/////// ��������� ��������� ������ 3 (���� 2) ������ �� ����������


if ($begin_time_zad <= get_date_time() && $zad_bot["enable"]=="on"){
$newshout = "[color=Red]������[/color]: $question_zad $ansstr_zad";
$date = time(); 

sql_query("INSERT INTO shoutbox (userid, date, text, ouserid, answer) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . "," . sqlesc($answer_zad).")") or sqlerr(__FILE__, __LINE__); 
	
sql_query("UPDATE victorina SET enable='wait',num='0' WHERE id=1") or sqlerr(__FILE__, __LINE__); 
}
/////// ���������� ������ �������������

if ($begin_time_zad <= get_date_time() && $zad_bot["enable"]=="new"){
$newshout = "[color=green]������[/color]: $question_zad $ansstr_zad";
$date = time(); 

sql_query("INSERT INTO shoutbox (userid, date, text, ouserid, answer) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . "," . sqlesc($answer_zad).")") or sqlerr(__FILE__, __LINE__); 
sql_query("UPDATE victorina SET enable='wait' WHERE id=1") or sqlerr(__FILE__, __LINE__); 	
}
/////// ���������� ������ �������������


if (isset($_GET["do"]) && $_GET["do"] == "shout") {

$_GET["shout"] = str_replace("+", "%2B", $_GET["shout"]);
    $shout = convert_text(urldecode(decode_unicode_url($_GET["shout"])));
    if ($shout == "/prune" && get_user_class() >= UC_ADMINISTRATOR) {
        mysql_query("TRUNCATE TABLE shoutbox");
        die("��� ��������� ���� ���������.");
    }



///// ��������� �������������� ��������� �� ����� '���������'
if ($zad_bot["hint"]=="first" && $zad_bot["enable"]=="wait" && $shout=="���������"){

$word=$answer_zad;
$strlen=strlen($word);
$str = $word;
$strlen_min2=$strlen-1;
//$strlast = substr($str, -1); /// ���� �����
$strfirst = substr($str, 0, 1); /// ������ �����
$view="[b]".$strfirst."[/b]";
for ($x=0; $x<$strlen_min2; $x++){
$view.="#";
}
///$view.="[b]".$strlast."[/b]";

$newshout = "[color=gray]��������� #1/2[/color]: $view (+$hint_time_f ���) ��������: [color=#".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"])."]".$CURUSER["username"]."[/color]";
$date = time(); 

$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $hint_time_f); 
sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 
	
sql_query("UPDATE victorina SET enable='wait', end_time=" . sqlesc($end_time) . ", hint='second' WHERE id=1");	

unset($shout);
}
elseif ($zad_bot["hint"]=="second" && $zad_bot["enable"]=="wait" && $shout=="���������"){

$word=$answer_zad;
$strlen=strlen($word);
$str = $word;
$strlen_min2=$strlen-2;
$strlast = substr($str, -1); /// ������ �����
$strfirst = substr($str, 0, 1); /// ���� �����
$view2="[b]".$strfirst."[/b]";
for ($x=0; $x<$strlen_min2; $x++){
$view2.="#";
}
$view2.="[b]".$strlast."[/b]";

$newshout = "[color=gray]��������� #2/2[/color]: $view2 (+$hint_time_t ���) ��������: [color=#".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"])."]".$CURUSER["username"]."[/color]";
$date = time(); 

$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $hint_time_t);
sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 
	
sql_query("UPDATE victorina SET enable='wait', end_time=".sqlesc($end_time).", hint='third' WHERE id=1");
unset($shout);
}
///// ��������� �������������� ��������� �� ����� '���������'
$razmer_otveta=strlen($zad_bot["answer"]); /// ������ ������ + 2 ������� ������� ����
$razmer_now=strlen($shout); /// ���� ������ ��������� ������������

if ($razmer_now<=($razmer_otveta+2) && @stristr(tolower($shout),$answer_zad)!== false && $zad_bot["enable"]=="wait"){
	

if ($zad_bot["hint"]=="first"){
$bonus=$init_bonus;
}elseif ($zad_bot["hint"]=="second"){
$bonus=$init_bonus/2;	
$hinti=" + 1 ���������";
}elseif ($zad_bot["hint"]=="third"){
$bonus=$init_bonus/4;	
$hinti=" + 2 ���������";
}


$newshout = "[color=green]���������� �����[/color]: [b] $answer_zad [/b] ��� ������������ [color=#".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"])."]".$CURUSER["username"]."[/color] ($bonus �����$hinti). ��������� ������ ����� $prome ���.";

$date = time();
sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 

sql_query("UPDATE users SET bonus=bonus+$bonus WHERE id = " . $CURUSER["id"]) or sqlerr(__FILE__,__LINE__);

////////////////////// ������ ����� ������ ////////////////
$sq=sql_query("SELECT enable,(SELECT COUNT(*) FROM vquestions) as num_que FROM victorina WHERE id=1") or sqlerr(__FILE__, __LINE__); 
$r_bot=mysql_fetch_array($sq);
//enum('off','wait','on')

$launcher=$CURUSER["id"];

$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $medium_answ);
$quest_id=mt_rand(1, $r_bot["num_que"]);
//// ��������� �������� � ����
$time_s = get_date_time();
sql_query("UPDATE victorina SET enable='new', all_bonus=all_bonus+'$bonus', hint='first', num=num+1, end_time=".sqlesc($end_time).", begin_time=".sqlesc($begin_time).",  question_id=".sqlesc($quest_id)." WHERE id=1") or sqlerr(__FILE__,__LINE__);
sql_query("UPDATE vquestions SET last_check=".sqlesc($time_s)." WHERE id=".sqlesc($id_zad)."") or sqlerr(__FILE__,__LINE__);
////////////////////// ������ ����� ������ ////////////////
}

//// �������� ��������� ���������
if ($shout=='���������' || $shout=='�����' || $shout=='����' && get_user_class()>= UC_MODERATOR) {

$sq=sql_query("SELECT enable,(SELECT COUNT(*) FROM vquestions) as num_que FROM victorina WHERE id=1") or sqlerr(__FILE__, __LINE__); 
$r_bot=mysql_fetch_array($sq);
//enum('off','wait','on')

if ($shout=='����' && get_user_class()>= UC_MODERATOR){
	
$newshout = "��������� ���������: [color=#".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"])."]".$CURUSER["username"]."[/color]".($r_bot["enable"]=="on" ? ".":" | [color=gray]���������� ����� ���[/color]: [b]".$answer_zad."[/b] (�������: $num, ����� ���������� �������: $all_bonus)")."";
$date = time()+5; 

sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__);

$tesla_tracker=1; /// 1 ���� ������ ������ Tesla TT 2009 ����� ������� 0

if ($tesla_tracker){
//// ������� ��������� ������ � ������ ����� ��� ����������, � � ��� ����� ������������
if (strtotime($CURUSER['last_access']) < (strtotime(get_date_time()) - 250)){
$num_av  = get_row_count("vquestions");
}
if (empty($num_av) && empty($zad_bot["numque"])){
$num_av="130540";
}
elseif(empty($num_av) && !empty($zad_bot["numque"])){
$num_av=$zad_bot["numque"]; // ���������� ��������� ��������
}}
else
$num_av  = get_row_count("vquestions");

sql_query("UPDATE victorina SET hint='first', enable='off', num='0', all_bonus='0.00', numque='$num_av' WHERE id=1");
unset($shout);
}
elseif ($shout=='����' &&  get_user_class()< UC_MODERATOR){
die("�� �� ������ ���������� ���������. <script>setTimeout('document.location.href=shoutbox.php', 4);</script>");
}

$launcher=$CURUSER["id"];
$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $first_answ); /// ��������� 3 ������
$quest_id=mt_rand(1, $r_bot["num_que"]);

//// ��������� �������� � ����
if ($r_bot["enable"]=="off" && $shout<>'����'){
sql_query("UPDATE victorina SET hint='first', enable='on', begin_time=".sqlesc($begin_time).", end_time=".sqlesc($end_time).", question_id=".sqlesc($quest_id).", num='0' WHERE id=1");


$newshout = "��������� ��������: [color=#".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"])."]".$CURUSER["username"]."[/color] ����� $prome ��� �������� ������, ������ ������ � �������.";
$date = time()+5; 

sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 
}

//// ���� ����� ��� � ���� �������� � ���������
if (empty($r_bot["enable"]) && $shout<>'����'){
sql_query("INSERT INTO victorina (hint, question_id, begin_time, end_time,enable,num) VALUES (".implode(", ", array_map("sqlesc", array('first', $quest_id, $begin_time, $end_time, "on","0"))).")") or sqlerr(__FILE__,__LINE__);
}
//// ���� ����� ��� � ���� �������� � ���������



unset($shout);
}    
//// �������� ��������� ���������


  if (!empty($shout)) {
  	
if (@stristr($shout,'(bot)')!== false &&  get_user_class()> UC_MODERATOR)
{
	$sender = 92;
	$shout = str_replace("(bot)","",$shout);
	$osender = $CURUSER["id"];
}
elseif (@stristr($shout,'(system)')!== false &&  get_user_class()>= UC_SYSOP)
{
	$sender = 0;
    $shout = str_replace("(system)","",$shout);
    $osender = $CURUSER["id"];
}
else
{
	$shout = str_replace("/prune","",$shout);
   	$shout = str_replace("(system)","",$shout);
   	$shout = str_replace("(bot)","",$shout);
   	$sender = $CURUSER["id"];
   	$osender = $CURUSER["id"];
}  
    	
        ///$shout = preg_replace("/\/me /", $CURUSER["username"]." ", $shout);
      //  $shout = preg_replace("/\[img]/", "[ img ]", $shout);

        $shout = preg_replace("/\<?/", "", $shout);
      
        $shout = preg_replace("/\eval/", "", $shout);
        $shout = preg_replace("/\base64/", "", $shout);
        $shout = preg_replace("/\SRC=/", "", $shout);
  $shout = htmlspecialchars_uni($shout);
        
       /// $shout = preg_replace("'\[img\].*?\[/img\]'si","",$shout);
        
        $datee = time();


sql_query("INSERT INTO shoutbox (date,  text, userid, ouserid) VALUES (".implode(", ", array_map("sqlesc", array($datee, format_comment($shout), $sender, $osender))).")") or sqlerr(__FILE__,__LINE__);



} else
print("<script>alert('������� ���������');</script>");

} elseif (isset($_GET["do"]) && $_GET["do"] == "delete" && get_user_class() >=UC_MODERATOR && is_valid_id($_GET["id"])) {
$id = (int) $_GET["id"];
sql_query("DELETE FROM shoutbox WHERE id = $id") or sqlerr(__FILE__,__LINE__);
//header("Refresh: 0; url=tracker-chat.php");
//die;
}
elseif (isset($_GET['edit']) && get_user_class() > UC_MODERATOR && is_valid_id($_GET['edit']))
{
        $sql=sql_query("SELECT s.id, s.text,users.username, users.id as uid, users.class 
		FROM shoutbox AS s LEFT JOIN users ON s.userid = users.id 
		WHERE s.id=".sqlesc($_GET['edit'])) or sqlerr(__FILE__, __LINE__); 
        $res=mysql_fetch_array($sql);
        if (!empty($res)) {
if ($res["username"]) {
$user="<a href=\"userdetails.php?id=$res[uid]\">".get_user_class_color($res["class"], $res["username"])."</a>";}else
{$user="<font color=gray>[<b>SysteM</b>]</font>";}
        
	echo"<body bgcolor=#F5F4EA>
	<meta http-equiv=\"expires\" content=\"0\">
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\" />
   <form method=post action=shoutbox.php>  ������������� ����� ��������� $user ��� �������: ".$res['id']."<br>
   <textarea rows=4 readonly cols=80>".htmlspecialchars($res['text'])."</textarea><br>
   
   <input type=hidden name=id value=".(int)$res['id'].">
   <textarea name=text rows=4 cols=80 id=specialbox>".htmlspecialchars($res['text'])."</textarea><br>
	<input type=submit name=save value=\"�������� ���������\" class=btn>
    </form>
    
  	<form action=tracker-chat.php><input type=submit value=\"������� � ���\" class=btn>
    </form>
	</body>";
        
        die;
        }
else
header("Refresh: 0; url=tracker-chat.php");
die;
}
elseif (isset($_POST['text']) && get_user_class() > UC_MODERATOR && is_valid_id($_POST['id']))
{
        $text = trim(htmlspecialchars_uni($_POST['text']));
        $id = (int)$_POST['id'];
        if(strlen($text) > 1000) die("������� ������� �����");
       	$text=preg_replace("'\[img\].*?\[/img\]'si","",$text);
        if (isset($text) && isset($id) && is_valid_id($id))
sql_query("UPDATE shoutbox SET text = ".sqlesc($text)." WHERE id=".sqlesc($id));
header("Refresh: 0; url=tracker-chat.php");
die;
}

$unread = $CURUSER["unread"];
if ($unread){
$newmessageview = "
<table width=\"30%\" align=\"right\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr>
<td align=\"center\" class=\"a\" ><a title=\"".$CURUSER["username"]." ����� ����!!!\" href=\"message.php?action=new\">� ��� ����� ���������</a></td>
<tr></table>";
echo $newmessageview;
};

//if ($CURUSER["class"]>=4)// ���������
//{
//$victo = "<small>����� - ��� ���������: ���� - ����������.</small>";	
//}


$limit_id=$CURUSER["id"];
/*
$res3 = sql_query("SELECT friendid FROM friends WHERE blocks=1 and userid=$limit_id") or sqlerr(__FILE__, __LINE__);
while ($arr3 = mysql_fetch_array($res3)) {
if ($blockid2)
$blockid2.=",";
$blockid2.=$arr3["friendid"];
}

if ($blockid2){
$hide = array($blockid2); // those IDs will not be shown online
$where = "WHERE userid NOT IN (".join(",",$hide).") "; 
}
*/
if (get_user_class() < UC_MODERATOR){
$limit="35";
}
else
$limit="50";


$res = sql_query("SELECT shoutbox.*, users.username, users.id as uid, users.class,users.num_warned,
o.username AS ousername, o.class AS oclass
FROM shoutbox 
LEFT JOIN users ON shoutbox.userid = users.id
LEFT JOIN users AS o ON shoutbox.ouserid = o.id
ORDER BY date DESC LIMIT $limit") or sqlerr(__FILE__,__LINE__);


print("<table border=0 cellpadding=0 cesllspacing=0 width='100%'>");
while ($arr = mysql_fetch_array($res)) {

$username = $arr["username"];

if (!empty($arr["num_warned"])){
$pic="<img src=\"/pic/warned.gif\" alt=\"������������\">";
}

$arr["text"] = ($arr["text"]);

//$arr["text"] = htmlspecialchars($arr["text"]);


//$arr["text"] = str_replace("$CURUSER[username]","<font style='color: #".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"])."'>$CURUSER[username]</font>",$arr["text"]);


//$arr["text"] = preg_replace("/\[((\s|.)+?)\]/", "<a onclick=\"parent.document.shoutform.shout.value=parent.document.shoutform.shout.value+' [\\1] ';return false;\" style=\"cursor: crosshair;\"><b style='color: gray;'>[\\1]</b></a>", $arr["text"]);

$ousername = $arr["ousername"];
$oclass = $arr["oclass"];
$answer_sql = $arr["answer"];


	$cellclass = "";
	
	if (stristr($arr["text"], $CURUSER["username"]." :" ) || $arr["userid"]==$CURUSER["id"]) {
	//	$cellclass = "bgcolor=\"#ECECEC\"";
	}
elseif (stristr($arr["text"],"<font style='color: #".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"])."'>$CURUSER[username]</font>")){
	$cellclass = "bgcolor=\"#f0f0f0\"";
	}
	
	///if (stristr($arr["text"], "privat(".$CURUSER["username"].")") && $arr["userid"]==$CURUSER["id"]){
	if (stristr($arr["text"], "privat(") && $arr["userid"]==$CURUSER["id"]){
	$cellclass = "bgcolor=\"#F1F0ED\"";
	}
	elseif (stristr($arr["text"], "privat(".$CURUSER["username"].")") && $arr["userid"]<>$CURUSER["id"]){
	$cellclass = "bgcolor=\"#E9E9E6\";";
	}
//else	$cellclass = "";





if (stristr($arr["text"], "privat(".$CURUSER["username"].")")
|| (!stristr($arr["text"], "privat(".$CURUSER["username"].")") && ($arr["userid"]==$CURUSER["id"]))
) {

print("<tr><td style='vertical-align: top;' $cellclass>");


if ($CURUSER["class"]>=UC_MODERATOR)
echo "<span title=\"�������\" onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer; color: red; font-weight: bold; text-decoration: underline\"><img src=\"pic/warned2.gif\" style=\"border: 0px;\" /></span> ";

if ($CURUSER["class"] > UC_MODERATOR)
echo "<a href=shoutbox.php?edit=".$arr["id"]."><img src=\"pic/forum.gif\" width=\"12px\" border=0></a> ";


echo "<span class=\"date\"title=\"".normaltime(get_date_time($arr["date"]),true)."\" onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value=parent.document.shoutform.shout.value+'[".strftime("%H:%M:%S",$arr["date"])."] ';return false;\" style=\"cursor: crosshair; font-weight: italic;\"class=\"date\">[".strftime("%H:%M:%S",$arr["date"])."]</span> ";


if (stristr($arr["text"], "privat(".$CURUSER["username"].")")!==false)
echo "<span style=\"background: #E9E9E6;\" title=\"�������� ������ � ���� ��� �����\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='privat(".$username.") '+parent.document.shoutform.shout.value;return false;\">������ �� </span>";


$uspm="";
if (preg_match("/privat\(([^()<>\s]+?)\)/i",$arr["text"]) && ($arr["userid"]==$CURUSER["id"])){
	
preg_match('/privat\((.*?)\)/is',$arr["text"],$uspm_);
$uspm=$uspm_[1];


echo "<span ".(!empty($uspm) ? "onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='privat($uspm) '+parent.document.shoutform.shout.value;return false;\"":"")." style=\"background: #F9F9F9;\">������ � </span>";
}

if (empty($uspm))
echo "<a href=\"userdetails.php?id=".$arr["uid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$username."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($arr["class"], $arr["username"])."</a>: ";


if (!empty($uspm))
echo "<span onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='privat($uspm) '+parent.document.shoutform.shout.value;return false;\" style=\"background: #F9F9F9;\">$uspm</span>: ";

if (!empty($arr["num_warned"]))
echo "<img src=\"/pic/warned.gif\" alt=\"������������\">";


//$arr["text"] = preg_replace("/privat\((.*)\)/is","", $arr["text"]);
$arr["text"] = str_replace("privat(".$CURUSER["username"].")","",$arr["text"]);
$arr["text"] = str_replace("privat(".$uspm.")","",$arr["text"]);


echo $arr["text"];
 
if ($CURUSER["class"]==UC_SYSOP){

if (($ousername<>$username && $arr["uid"]=="92"))
echo "<a title=\"�� ����� ViKa �������\">[</a><a href=\"userdetails.php?id=".$arr["ouserid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$ousername."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($oclass, $ousername) . "</a><a title=\"�� ����� ViKa �������\">]</a> ";

if ($arr["uid"]==0 && !$arr["username"])
echo "<a title=\"�� ����� SysteM �������\">[</a><a href=\"userdetails.php?id=".$arr["ouserid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$ousername."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($oclass, $ousername) . "</a><a title=\"�� ����� SysteM �������\">]</a>";

if (!empty($answer_sql))
echo "<font title=\"����� ��� ������\" style='color: blue'>[</font>".$answer_sql."<font  title=\"����� ��� ������\" style='color: blue'>]</font> ";

echo "<br>";
}


echo ("</td></tr>\n" );
} /// ����� ���� �� ���������


elseif (!preg_match("/privat\(([^()<>\s]+?)\)/i",$arr["text"])) {

print("<tr><td style='vertical-align: top;' $cellclass>");


if (get_user_class() >=UC_MODERATOR)
echo "<span title=\"�������\" onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer; color: red; font-weight: bold; text-decoration: underline\"><img src=\"pic/warned2.gif\" style=\"border: 0px;\" /></span> ";

if (get_user_class() > UC_MODERATOR)
echo "<a href=shoutbox.php?edit=".$arr["id"]."><img src=\"pic/forum.gif\" width=\"12px\" border=0></a> ";

echo "<span class=\"date\"title=\"".normaltime(get_date_time($arr["date"]),true)."\" onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value=parent.document.shoutform.shout.value+'[".strftime("%H:%M:%S",$arr["date"])."] ';return false;\" style=\"cursor: crosshair; font-weight: italic;\"class=\"date\">[".strftime("%H:%M:%S",$arr["date"])."]</span> ";

echo "<a href=\"userdetails.php?id=".$arr["uid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$username."[/b]: '+parent.document.shoutform.shout.value;return false;\">
".get_user_class_color($arr["class"], $arr["username"])."</a>: ";

if (!empty($arr["num_warned"]))
echo "<img src=\"/pic/warned.gif\" alt=\"������������\">";

//$arr["text"] = preg_replace("/privat\((.*)\)/is","", $arr["text"]);
$arr["text"] = str_replace("privat(".$CURUSER["username"].")","",$arr["text"]);
$arr["text"] = str_replace("privat(".$uspm.")","",$arr["text"]);



echo $arr["text"];
 
if ($CURUSER["class"]==UC_SYSOP){

if (($ousername<>$username && $arr["uid"]=="92"))
echo "<a title=\"�� ����� ViKa �������\">[</a><a href=\"userdetails.php?id=".$arr["ouserid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$ousername."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($oclass, $ousername) . "</a><a title=\"�� ����� ViKa �������\">]</a> ";

if ($arr["uid"]==0 && !$arr["username"])
echo "<a title=\"�� ����� SysteM �������\">[</a><a href=\"userdetails.php?id=".$arr["ouserid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$ousername."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($oclass, $ousername) . "</a><a title=\"�� ����� SysteM �������\">]</a>";

if (!empty($answer_sql))
echo "<font title=\"����� ��� ������\" style='color: blue'>[</font>".$answer_sql."<font  title=\"����� ��� ������\" style='color: blue'>]</font> ";

echo "<br>";
}


echo ("</td></tr>\n" );
} ///


/// ��� ����� ����
if (preg_match("/privat\(([^()<>\s]+?)\)/i",$arr["text"]) && !stristr($arr["text"], "privat(".$CURUSER["username"].")") && ($arr["userid"]<>$CURUSER["id"]) && (get_user_class() == UC_SYSOP || $CURUSER["id"]=="1654")) {

print("<tr><td style='vertical-align: top;' $cellclass>");

echo "<span title=\"�������\" onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer; color: red; font-weight: bold; text-decoration: underline\"><img src=\"pic/warned2.gif\" style=\"border: 0px;\" /></span> ";

echo "<a href=shoutbox.php?edit=".$arr["id"]."><img src=\"pic/forum.gif\" width=\"12px\" border=0></a> ";

echo "<span class=\"date\"title=\"".normaltime(get_date_time($arr["date"]),true)."\" onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value=parent.document.shoutform.shout.value+'[".strftime("%H:%M:%S",$arr["date"])."] ';return false;\" style=\"cursor: crosshair; font-weight: italic;\"class=\"date\">[".strftime("%H:%M:%S",$arr["date"])."]</span> ";


$uspm="";
if (preg_match("/privat\(([^()<>\s]+?)\)/i",$arr["text"])){
	
preg_match('/privat\((.*?)\)/is',$arr["text"],$uspm_);
$uspm=$uspm_[1];

echo "<span style=\"background: #F9F9F9; font-weight: bold;\">����� ������ </span>";
}

echo "<a href=\"userdetails.php?id=".$arr["uid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$username."[/b]: '+parent.document.shoutform.shout.value;return false;\">
".get_user_class_color($arr["class"], $arr["username"])."</a> ";
//("/�������� \[(.*?)\]/"

if (!empty($uspm))
echo "<b>� <span onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='privat($uspm) '+parent.document.shoutform.shout.value;return false;\" style=\"background: #F9F9F9;\">$uspm</span></b>: ";

//$arr["text"] = preg_replace("/privat\((.*)\)/is","", $arr["text"]);

$arr["text"] = str_replace("privat(".$CURUSER["username"].")","",$arr["text"]);
$arr["text"] = str_replace("privat(".$uspm.")","",$arr["text"]);

echo $arr["text"];

if (($ousername<>$username && $arr["uid"]=="92"))
echo "<a title=\"�� ����� ViKa �������\">[</a><a href=\"userdetails.php?id=".$arr["ouserid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$ousername."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($oclass, $ousername) . "</a><a title=\"�� ����� ViKa �������\">]</a> ";

if ($arr["uid"]==0 && !$arr["username"])
echo "<a title=\"�� ����� SysteM �������\">[</a><a href=\"userdetails.php?id=".$arr["ouserid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$ousername."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($oclass, $ousername) . "</a><a title=\"�� ����� SysteM �������\">]</a>";

if (!empty($answer_sql))
echo "<font title=\"����� ��� ������\" style='color: blue'>[</font>".$answer_sql."<font  title=\"����� ��� ������\" style='color: blue'>]</font> ";

echo "<br>";

echo ("</td></tr>\n" );
	
}



}
echo "</table>";
         /*  
		 
		 <font color=\"#".get_user_rgbcolor($arr["class"], $arr["username"])."\">
 ".($arr["text"])."</font>
		 
		 */
		 
		 
 if (
 
 (stristr($shout,'�����') !== false)
|| (stristr($shout,'����') !== false)
|| (stristr($shout,'���') !== false)
|| (stristr($shout,'�����') !== false)
|| (stristr($shout,'�����') !== false)
//|| (stristr($shout,'����') !== false)
 || (stristr($shout,'����') !== false)
  || (stristr($shout,'����') !== false)
    || (stristr($shout,'�����') !== false)
        || (stristr($shout,'����') !== false)
 )
 
{
$userid=$CURUSER["id"];
$num=$CURUSER["num_warned"]+1;
$modcomment = sqlesc(date("Y-m-d") . " - +1 �������������� �� ViKa � ����.\n");
sql_query("UPDATE users SET num_warned = $num, modcomment = CONCAT($modcomment, modcomment) WHERE class <4 and id = $userid");

if ($CURUSER["num_warned"]>"4"){
$userid=$CURUSER["id"];
$num2=$CURUSER["num_warned"];
$modcomment = sqlesc(date("Y-m-d") . " - ������ $num2 �������������� ViKa (���)\n");
sql_query("UPDATE users SET enabled='no', modcomment = CONCAT($modcomment, modcomment) WHERE class <4 and id = $userid");	
}
}


   /*       
 
 	if ((stristr($shout,'[b]ViKa[/b]') !== false)&&($shout!=="[b]ViKa[/b]:") || 
	 (
(stristr($shout,'������ ����') !== false) || (stristr($shout,'����� ����') !== false) || (stristr($shout,'�������� ����') !== false) || (stristr($shout,'hi all') !== false) || (stristr($shout,'������ ����') !== false)
|| (stristr($shout,'�����') !== false)
|| (stristr($shout,'����') !== false)
|| (stristr($shout,'���') !== false)
|| (stristr($shout,'�����') !== false)
|| (stristr($shout,'�����') !== false)
|| (stristr($shout,'����') !== false)
|| (stristr($shout,'�����') !== false)
|| (stristr($shout,'����') !== false)
|| (stristr($shout,'����') !== false)
|| (stristr($shout,'�����') !== false)
|| (stristr($shout,'����') !== false)

     )
	 ) {
 		
		 
		 
		 	
 		$bot="[b]ViKa[/b]:";
//////////////////////////
if ((stristr($shout,'��������� �������') !== false)&& $CURUSER["class"]>="4"){

$res = mysql_query("SELECT name,owner,added,(SELECT username FROM users WHERE id=torrents.owner) AS classusername  FROM torrents WHERE  banned = 'no' ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0 )
while ($arr = mysql_fetch_assoc($res)) {
{
$owned="[b]��������� �������[/b]: ".htmlspecialchars($arr["name"])." ��� ����� ".$arr["classusername"]." � ".$arr["added"];
}}}
else
{
$owned="��������, ������ ��� ������������� �����.";
}


if ((stristr($shout,'��������� ��������') !== false)&& $CURUSER["class"]>="4"){

$res = mysql_query("SELECT name,id FROM torrents WHERE  banned = 'no' and moderated='no' ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0 )
while ($arr = mysql_fetch_assoc($res)) {
{
$owned_moderat="[b]��������� �� ����������� �������[/b]: [url=$DEFAULTBASEURL/details.php?id=".$arr['id']."]".htmlspecialchars($arr["name"])."[/url]. ���������� ��������� ���, ����� ������������ ����� ����������.";
}}}
else
{
$owned_moderat="��������, ������ ��� ������������� �����.";
}



//////////////////////////
if ((stristr($shout,'��� � ����') !== false)&& $CURUSER["class"]>="4"){

$dt = sqlesc(get_date_time(gmtime() - 40));
//$dt = sqlesc(time() - 180);

$res = sql_query("SELECT id, username, class FROM users WHERE chat_access<>'0000-00-00 00:00:00' and chat_access > $dt ORDER BY class DESC LIMIT 100") or sqlerr(__FILE__,__LINE__);

$s4etc=0;
while ($arr = mysql_fetch_assoc($res)) {


$lastid=$uid;

$class=$arr['class'];
$username = $arr['username'];
  if ($title_who_c)
  $title_who_c.= ", ";
$title_who_c.= "[url=$DEFAULTBASEURL/userdetails.php?id=".$arr['id']."]".$arr["username"]. "[/url]";
$s4etc++;
}
}




/////////////////////////
if (stristr($shout,'��� �������') !== false){
$userid=$CURUSER["id"];
$res = mysql_query("SELECT info FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0 )
while ($arr = mysql_fetch_assoc($res)) {
{ 
	if ($arr["info"]==""){
$info="���� ������� �����";}
else
$info="[i]���� ������� ����[/i]:\n".htmlspecialchars_uni($arr["info"]);
}}
}
/////////////////////////
if (stristr($shout,'���� �������') !== false){
$userid=$CURUSER["id"];
$res = mysql_query("SELECT info FROM users WHERE id=92") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0 )
while ($arr = mysql_fetch_assoc($res)) {
{ 
	if ($arr["info"]==""){
$vikainfo="��� ������� �����";}
else
$vikainfo="[i]��� ������� ����[/i]:\n".htmlspecialchars_uni($arr["info"]);
}}
}
/////////////////////////

/////////////////////////
if ((stristr($shout,'������� ���') !== false) || (stristr($shout,'������ ���') !== false))
{
	if ($CURUSER['class']>=5){
  mysql_query("TRUNCATE TABLE shoutbox");}  
}

/////////////////////////
if (stristr($shout,'��������� �������') !== false){
if ($CURUSER['class']==6){
	

	//��������� ������� �����
$mtime = microtime();
//��������� ������� � ������������
$mtime = explode(" ",$mtime);
//���������� ���� ����� �� ������ � �����������
$mtime = $mtime[1] + $mtime[0];
//���������� ��������� ����� � ����������
$tstart = $mtime; 



require_once('include/cleanup.php');

$s_s = $queries;
docleanup();
$s_e = $queries;


		//������ ��� �� �� �����, ����� �������� ������� �����
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
//���������� ����� ��������� � ������ ����������
$tend = $mtime;
//��������� �������
$totaltime = ($tend - $tstart);
//������� �� �����
}}


 		 if ((stristr($shout,'��� ���������') !== false) && $CURUSER["class"]>="4"){
$res1 = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location=1 AND unread='yes'") or sqlerr(__FILE__,__LINE__);
$arr1 = mysql_fetch_row($res1);
$unread = $arr1[0];
$newmessage1 = $unread . " ���" . ($unread > 1 ? "��" : "��"); 
$newmessage2 = " �������" . ($unread > 1 ? "��" : "��"); 
$newmessage = "$newmessage1 $newmessage2"; 

    if ($unread)
    {

$unread2="[b][url=$DEFAULTBASEURL/message.php] � ��� $newmessage [/url][/b]";

    }
    else 
    
    $unread2="[b]� ���� ��� ����� ���������[/b]";
    
	
	}
	else
	  {
    $unread2="������, �� ��� ������� ������ ��� �������������";
	}
	;
    
                  switch ($shout)
                  {
                  	                  			
    
	              	   case $shout == (stristr($shout,'������� ���') !== false):
                      case $shout == (stristr($shout,'������ ���') !== false):
                  /// $a="".$CURUSER['username']." ".($CURUSER['class']>="5" ? "������, ��������� ���� ���." : " ��� ?")
;           $a="".$CURUSER['username']." ".($CURUSER['class']>="5" ? " ��� :P" : " ��� ?")
;          
                     $b=$a;          
                    break;	
						  
						  
					 case $shout == (stristr($shout,'�����') !== false):	  	
                  	 case $shout == (stristr($shout,'����') !== false):
                     case $shout == (stristr($shout,'����') !== false):
                     case $shout == (stristr($shout,'�����') !== false):
                     case $shout == (stristr($shout,'������') !== false):
                     case $shout == (stristr($shout,'���') !== false):
                      case $shout == (stristr($shout,'�����') !== false):
                      case $shout == (stristr($shout,'����') !== false):
                         case $shout == (stristr($shout,'����') !== false):
                            case $shout == (stristr($shout,'����') !== false):
                              case $shout == (stristr($shout,'�����') !== false):
                                  case $shout == (stristr($shout,'�����') !== false):
                      case $shout == (stristr($shout,'����') !== false):
                    
                    
                    $a="".$CURUSER['username']." ".($CURUSER['class']>"4" ? " �� ����� ���, ������������� ������ ���� ��������." : ($CURUSER[num_warned]>"4" ? "� ���� ����� $CURUSER[num_warned] ��������������, ����� ���� ����� ���� �������� �������, ����.":"+1 ($num �� 5) � ������ ������ ��������������, �� �������� ������!"))
;          
                     $b=$a;          
                    break;
                    
                     case $shout == (stristr($shout,'��������� �������') !== false):
                    
                    $a="".$CURUSER['username']." ".($CURUSER['class']=="6" ? "������� ��������� �������. �� ������� ������������ ".($s_e - $s_s)." �������� ($totaltime)" : " �� �� ����, �������� ��� ���������.")
;          
                     $b=$a;          
                    break;
                  	
                  	
                 case (stristr($shout,'���')!==FALSE):
                     $a="".$CURUSER['username']." :unsure: � ���� ���� ?";          
                     $b="".$CURUSER['username']." ��������";          
                    break;
                            
					case $shout == "$bot ���";
					case $shout == "$bot ���";
					case (stristr($shout,'������')!==FALSE):
					case (stristr($shout,'�� ���')!==FALSE):
                    $a="".$CURUSER['username']." ��� �����, ��� :P";          
                    $b="".$CURUSER['username']." �������� �� ����, ������";          
                    break;
                    
             
                  case $shout == "$bot ��";
				  case $shout == "$bot ��";
                  $a="".$CURUSER['username']." ��� :P";          
                  $b="".$CURUSER['username']." ��� �������";          
                    break;
                   
                  
				   case (stristr($shout,'���?')!==FALSE):
					case (stristr($shout,'���???')!==FALSE):
                    $a="".$CURUSER['username']." ��� ��� ��!!!";          
                   $b="".$CURUSER['username']." ��!!!";          
                    break;
                    
                                      
			case (stristr($shout,'��')!==FALSE):
                     $a="".$CURUSER['username']."  :lol:";          
                     $b="".$CURUSER['username']." ;-)";          
                    break;


                        case (stristr($shout,'����')!==FALSE):
                     $a="".$CURUSER['username']."  ��� � ����� ���� TOPILSKY_11";          
                     $b="".$CURUSER['username']." � ������ �� ���!!! � ������������!!!";          
                    break;


		     case (stristr($shout,'�����')!==FALSE):
                     $a="".$CURUSER['username']."   �� :-p  ";          
                     $b="".$CURUSER['username']." �� ��� :-p ";
                    break;


                       
				   case (stristr($shout,'���')!==FALSE):
				   $a="".$CURUSER['username']." ���������";          
                   $b="".$CURUSER['username']." ��� ����������";          
                    break;
                  
                    
                    
				   case $shout == "$bot ip";
			       $a="".$CURUSER['username']." ��� ip ����� ".$CURUSER['ip']."";          
                   $b="".$CURUSER['username']." ��� ip ����� ".$CURUSER['ip']."";          
                    break;
                   
                     case $shout == "$bot �������";
			       $a="".$CURUSER['username']." ��� ������� ����� ".getenv("HTTP_USER_AGENT")."";          
                   $b=$a;          
                    break;
                    
                   
                   case $shout == (stristr($shout,'��� ���������') !== false):
			       $a="".$CURUSER['username']." $unread2";          
                    $b=$a;          
                    break;
                    
                      case $shout == (stristr($shout,'��������� �������') !== false):
			       $a="".$CURUSER['username']." $owned";          
                  $b=$a;          
                    break;
                    
                   case $shout == (stristr($shout,'��������� ��������') !== false):
			       $a="".$CURUSER['username']." $owned_moderat";          
                  $b=$a;          
                    break;
                    
                   
                       case $shout == (stristr($shout,'��� �������') !== false):
			       $a="".$CURUSER['username']." $info";          
                     $b=$a;           
                    break;
                   
                  
                      case $shout == (stristr($shout,'���� �������') !== false):
			       $a="".$CURUSER['username']." $vikainfo";          
                     $b=$a;           
                    break;
              
                     
                                   
                            
                      case $shout == (stristr($shout,'��� � ����') !== false):
			       $a="".($CURUSER['class']>="4" ? "� ���� $s4etc: $title_who_c" : "".$CURUSER['username']." ��� ?")."";          
                     $b=$a;           
                    break;
                    
                  
                    case $shout == (stristr($shout,'������� ���') !== false):
                    case $shout == (stristr($shout,'������ ���') !== false):
                    $a="".$CURUSER['username']." ".($CURUSER['class']>="5" ? "������, ��������� ���� ���." : " ��� ?")
;          
                     $b=$a;          
                    break;	
              
			   
                    case $shout == "$bot ���";
				    case $shout == "$bot ���";
                    $a="".$CURUSER['username']." �� :D";          
                    $b="".$CURUSER['username']." ��� ��.";          
                    break;
                                      
                   
                    case $shout == (stristr($shout,'����?') !== false):
                    $a="".$CURUSER['username']." ���� :D";          
                    $b="".$CURUSER['username']."...";          
                    break;
                   
                      case $shout == (stristr($shout,'���������') !== false):
                       case $shout == (stristr($shout,'����������') !== false):
                    $a="".$CURUSER['username']." �������������� � ���������� ������!";          
                    $b="".$CURUSER['username']." ��� � ����������, ��� ����������� :P";          
                    break;
                   
                    case $shout == (stristr($shout,'....') !== false):
                    $a="".$CURUSER['username']." ... �������";          
                     $b="".$CURUSER['username']." � ��� �� ������ � ����� ��������� ?.";          
                    break;
                    
                    
                      case $shout == (stristr($shout,'�����������') !== false):
                    $a="".$CURUSER['username']." �� � ����� :-)";          
                     $b="".$CURUSER['username']." �� ��";          
                    break;
                    
                    
                      case $shout == (stristr($shout,'������������') !== false):
                      case $shout == (stristr($shout,'����������') !== false):
                      
                    $a="".$CURUSER['username']." � �� ������, ��� ����� ������ ? :D";          
                     $b="".$CURUSER['username']." �� ����, ������� ����� �������� ������� �� :P";          
                    break;
                    
                    
                         case $shout == (stristr($shout,'��� ���') !== false):
                                           
                    $a="".$CURUSER['username']." �� ���������� :D";          
                     $b="".$CURUSER['username']." ��� ���� ��� :P";          
                    break;
                    
                   case $shout == (stristr($shout,'�� �������') !== false):
                                           
                    $a="".$CURUSER['username']." � ��� ?";          
                     $b="".$CURUSER['username']." � �� �� ������ ����� :)";          
                    break;
                    
                        case $shout == (stristr($shout,'������ ���') !== false):
                      case $shout == (stristr($shout,'����� ���') !== false):
                      
                    $a="".$CURUSER['username']." ���� ����� � ���...";          
                     $b="".$CURUSER['username']." �� �������.";          
                    break;
                    
                    
                          case $shout == (stristr($shout,'������?') !== false):
                   ///   case $shout == (stristr($shout,'����� ���') !== false):
                      
                    $a="".$CURUSER['username']." ������ ������.";          
                     $b="".$CURUSER['username']." �������� ��� ���, ����� ���������.";          
                    break;
                    
                   
				   case $shout == (stristr($shout,':lol:') !== false):
                  
                    $a="".$CURUSER['username']." ��� ���� ...";          
                     $b="".$CURUSER['username']." .... ����� ���";          
                    break;
                    
                    
                     
					case $shout == (stristr($shout,'�� �� ������?') !== false):
                 	case $shout == (stristr($shout,'��� �������?') !== false):
                 
                    $a="".$CURUSER['username']." ���� ����� � ��� ��������:P";          
                     $b="".$CURUSER['username']." � ������� ���������� ������� � ��������������� ������� � ����� ����� ������� ���������� � �������� ����� ���� � �� ��������� �� ���� ����, ����? ";          
                    break;
                    
                    			case $shout == (stristr($shout,'��� ���') !== false):
                 //	case $shout == (stristr($shout,'��� �������?') !== false):
                 
                    $a="".$CURUSER['username']." ��� ��� � ������ � ������";          
                     $b=$a;          
                    break;
                    
                    
                    case $shout == (stristr($shout,'���� �����������') !== false):
                    case $shout == (stristr($shout,'����������� ��') !== false):
                    $a="".$CURUSER['username']." ���� �������� �������� � ���� :whistle:";          
                     $b="".$CURUSER['username']." ����� ��� ������, ��� ��������� ���? � �� ������ ������� �� ������� :innocent:";          
                    break;
                    
                    
                    case $shout == (stristr($shout,'��� ����') !== false):
                    case $shout == (stristr($shout,'��� ��') !== false):
                    $a="".$CURUSER['username']." ��������";          
                     $b="".$CURUSER['username']." ����������� :yes:";          
                    break;
                    
                    case $shout == (stristr($shout,'������?') !== false):
             
                    $a="".$CURUSER['username']." ��� ������� �� �����....����� ���";          
                     $b="".$CURUSER['username']." ����� ���.";          
                    break;
                    
                    
                    
                       case $shout == (stristr($shout,'���� ���� �����') !== false):
             
                    $a="".$CURUSER['username']." ���, �� ����� �� ����.";          
                     $b="".$CURUSER['username']." ����� �� �����, �������.";          
                    break;
                    
                    
                    
                      case $shout == (stristr($shout,'����������?') !== false):
                    case $shout == (stristr($shout,'��������?') !== false):
                    $a="".$CURUSER['username']." ����������";          
                     $b="".$CURUSER['username']." ��� ��� ������, �� ���� � ���������� � �����.";          
                    break;
                    
                    case $shout == (stristr($shout,'��� ����� DJFeel?') !== false):
                    case $shout == (stristr($shout,'������� ���') !== false):
                    $a="".$CURUSER['username']." ���������� DJ ��������� ������� [�������� �������� ������] :D";          
                     $b="".$CURUSER['username']." [url=$DEFAULTBASEURL/userdetails.php?id=25917]���� ?[/url] ����� �� ����.";          
                    break;
                    
                    
                     case $shout == (stristr($shout,'�������') !== false):
                    case $shout == (stristr($shout,'��������') !== false):
                    $a="".$CURUSER['username']." � �� ������� � ���� :P";          
                     $b="".$CURUSER['username']." ���, � ��� ��� ����� ���� ����� ������ ���?!";          
                    break;
                    
                    
                    
                    case $shout == "$bot :P":
                                          
                    $a="".$CURUSER['username']." ������ ��, ���������� ��� ���� :P";          
                    $b="".$CURUSER['username']." ������ ���� �������, ���� � � ������ �����!";          
                    break;
                    
                    
                     case $shout == (stristr($shout,'��������') !== false):
                    $a="".$CURUSER['username']." �� � �����, � ���?";          
                    $b="".$CURUSER['username']." ���.";          
                    break;
                    
                                         
                    case $shout == (stristr($shout,'� �����') !== false):
                    $a="".$CURUSER['username']." ��� ��� ;-)";          
                     $b="".$CURUSER['username']." ����.";          
                    break;
                    
                    
                    case $shout == (stristr($shout,'��!') !== false):
                    $a="".$CURUSER['username']." ��� ��� ��?";          
                     $b="".$CURUSER['username']." ���� ���� �� ������� :D";          
                    break;
                    
                    
                    case $shout == (stristr($shout,'������� ��') !== false):
                    $a="".$CURUSER['username']." �������������";          
                     $b="".$CURUSER['username']." ����.";          
                    break;
                    
                    case $shout == (stristr($shout,'��� ������ ?') !== false):
                    $a="".$CURUSER['username']." ������� ����� ����� ��! � �� ����.";          
                     $b="".$CURUSER['username']." ������.";          
                    break;
                    
                        
                    case $shout == (stristr($shout,'�������?') !== false):
                    case $shout == (stristr($shout,'���������?') !== false):
                    $a="".$CURUSER['username']." � ����� ���� ����� ���� ������� ����� �� ������� ;-)";                        $b="".$CURUSER['username']." �� ����� ��������";          
                    break;
                    
                     case $shout == (stristr($shout,'������ ����?') !== false):
                    case $shout == (stristr($shout,'������� ��� ���?') !== false):
                    $a="".$CURUSER['username']." ���, � ��� �� ?";                       
					$b="".$CURUSER['username']." � ����?.";          
                    break;
                    
                    case $shout == (stristr($shout,'���� �����') !== false):
                    case $shout == (stristr($shout,'���� �����') !== false):
                    case $shout == (stristr($shout,'���� ���') !== false):
                    
                    $a="".$CURUSER['username']." �������� ��� ������ ���� (v. Alfa)";          
                    $b="".$CURUSER['username']." ����";          
                    break;
                    
                    
                     case $shout == (stristr($shout,'������') !== false):
                     case $shout == (stristr($shout,'�����') !== false):
                     case $shout == (stristr($shout,'�����') !== false):
                     case $shout == (stristr($shout,'��������') !== false):
           
                     $a="".$CURUSER['username']." ��������.";          
                     $b="".$CURUSER['username']." ������ :)";          
                    break;
                    
                    
                    
                     case $shout == (stristr($shout,'������ ����') !== false):
                     case $shout == (stristr($shout,'����� ����') !== false):
                     case $shout == (stristr($shout,'�������� ���') !== false):
                     case $shout == (stristr($shout,'����� ���') !== false):
                     case $shout == (stristr($shout,'��������') !== false):
                     case $shout == (stristr($shout,'hi all') !== false):
                     case $shout == (stristr($shout,'����� ���') !== false):
           
                     $a="".$CURUSER['username']." � ���� ������ ;-)";          
                     $b="".$CURUSER['username']." �������� :)";          
                     break;
                     
                              
                    
                      case $shout == (stristr($shout,'�������') !== false):
                     case $shout == (stristr($shout,'���������') !== false):
                     case $shout == (stristr($shout,'������') !== false):
                     case $shout == (stristr($shout,'�������') !== false):
                     case $shout == (stristr($shout,'������') !== false):
                
                     $a="".$CURUSER['username']." :-)";          
                     $b="".$CURUSER['username']." �-)";          
                     break;
                     
                    
                     case $shout == (stristr($shout,'�� ��') !== false):
       
                     $a="".$CURUSER['username']." � ������� ��� ��� �������� ����, �������?";          
                     $b="".$CURUSER['username']." � ���� �������...";          
                     break;
                     
                    
                   
                     case $shout == (stristr($shout,'���� ���') !== false):
                     case $shout == (stristr($shout,'������� ���') !== false):
                     case $shout == (stristr($shout,'�������') !== false):
           
                     $a="".$CURUSER['username']." �� ������� � ������� ����� ���������� :P";          
                    $b="".$CURUSER['username']." � ���� �����?.";          
                    break;
                   
                   
                    case $shout == (stristr($shout,'����!') !== false):
                    case $shout == (stristr($shout,'�����!') !== false):
                     case $shout == (stristr($shout,'��!!!') !== false):
           
                     $a="".$CURUSER['username']." �� �� ��� ���� �����! ��� �������";          
                    $b="".$CURUSER['username']." �� ����";          
                    break;
                   
                   
                   
                     case $shout == (stristr($shout,'�������!') !== false):
                 
                     $a="".$CURUSER['username']." �� ��� ������� �� ;-) � �������������.";          
                    $b="".$CURUSER['username']." ���� ���";          
                    break;
                   
                   
                     case $shout == (stristr($shout,'�����') !== false):
                     case $shout == (stristr($shout,'�������') !== false):
                     case $shout == (stristr($shout,'�������') !== false):
                     case $shout == (stristr($shout,'�������') !== false):
           
                    $a="".$CURUSER['username']." � ���� �� �����, ���.";          
                    $b="".$CURUSER['username']." ���������� ��� ��.";          
                    break;
                   
                     case $shout == (stristr($shout,'���� ������?') !== false):
                     case $shout == (stristr($shout,'���� ���?') !== false):
                     case $shout == (stristr($shout,'���� ���������?') !== false):
                     case $shout == (stristr($shout,'�������?') !== false):
  
                    $a="".$CURUSER['username']." ��, ��� ������������� �����, � �� ���� �����!";          
                    $b="".$CURUSER['username']." �� :)";          
                    break;
                   
                   
                    case $shout == (stristr($shout,'�� �������?') !== false):
                    case $shout == (stristr($shout,'�� �������?') !== false):
           
                    $a="".$CURUSER['username']." ��� ���� � ������ :lol:";          
                    $b="".$CURUSER['username']." � ���� ������ ��?";          
                    break;
                   
                    
					case $shout == (stristr($shout,'������?') !== false):
                    
                    $a="".$CURUSER['username']." ������� �� ������";          
                    $b="".$CURUSER['username']." �����������";          
                    break;
                   
                   
                    case $shout == (stristr($shout,'�� ���� �������') !== false):
                    case $shout == (stristr($shout,'�� �������') !== false):
           
                    $a="".$CURUSER['username']." ��� ����� ���� ������� �������� � �����.";          
                    $b="".$CURUSER['username']." �� ���� ����� � � �� ���� ��";          
                    break;
                   
                   case $shout == (stristr($shout,'�� ������?') !== false):
                   case $shout == (stristr($shout,'�� �����?') !== false):
           
                    $a="".$CURUSER['username']." ���...������ �� ��� ���, ����� ���������";          
                    $b="".$CURUSER['username']." ��� � ��� :lol:.";          
                    break;
                   
                   case $shout == (stristr($shout,'����!') !== false):
                   case $shout == (stristr($shout,'�������') !== false):
                   case $shout == (stristr($shout,'��������') !== false):
           
                    $a="".$CURUSER['username']." �� ��� ��������! ��� ��!";          
                    $b="".$CURUSER['username']." �� �������� ��� ��� ��� ������, ������.";          
                    break;
                   
                   
                        case $shout == (stristr($shout,'������?') !== false):
                  
                    $a="".$CURUSER['username']." ������? Oo!";          
                    $b="".$CURUSER['username']." �� ��� ������� ���� ������?";          
                    break;
                   
                     case $shout == (stristr($shout,'��� ���') !== false):
                  case $shout == (stristr($shout,'��� ��') !== false):
                    $a="".$CURUSER['username']." ��� ��";          
                    $b="".$CURUSER['username']." ��� ����� ��� � ��� ))";          
                    break;
                  
                    case $shout == (stristr($shout,'����� ���� �����') !== false):
                           
                    $a="".$CURUSER['username']." ���, � ���� ���� ����� �� ������ �������� :D";          
                    $b="".$CURUSER['username']." ����� ��, ���� �� ���.";          
                    break;
                   
                     case $shout == (stristr($shout,'��������') !== false):
                     case $shout == (stristr($shout,'������') !== false):
                      case $shout == (stristr($shout,'�����') !== false):
                     case $shout == (stristr($shout,'���') !== false):
                       case $shout == (stristr($shout,'������ ���') !== false):
                      
                 
                 
                    $a="".$CURUSER['username']." �� ��� ������, � ���� �� ������,�������� � ��.";          
                    $b="".$CURUSER['username']." ����������, �� ���� ��� �������� �� ����.";          
                    break;
                                      
                    case $shout == (stristr($shout,'��� �������?') !== false):
                    case $shout == (stristr($shout,'�� �������?') !== false):
                    case $shout == (stristr($shout,'��� �������?') !== false):
           
                    $a="".$CURUSER['username']." :hmm: ������� ���� � ����, ��� ��� ����� ;-)";          
                    $b="".$CURUSER['username']." ���� ��������� ����";          
                    break;
                   
                    case $shout == (stristr($shout,'�������') !== false):
                    case $shout == (stristr($shout,'��� �����') !== false):
           
                    $a="".$CURUSER['username']." ��, ������� ����� ��� ��������.";          
                    $b="".$CURUSER['username']." ��� �������!";          
                    break;
                   
                    
					case $shout == (stristr($shout,'� ����?') !== false):
                             
                    $a="".$CURUSER['username']." ���� � ��� ���� �����";          
                    $b="".$CURUSER['username']." ��� �����, ��� ������ ��?";          
                    break;
                   
                    case $shout == (stristr($shout,'������ ���') !== false):
                    case $shout == (stristr($shout,'�� ������ �������') !== false):
           
                    $a="".$CURUSER['username']." ������ ������ ��� �� ������, ����� ���� ���� ����, �����.";          
                    $b="".$CURUSER['username']." ���, ��� ����������� ��� �����";          
                    break;
                   
                    case $shout == (stristr($shout,'�_�') !== false):
                    case $shout == (stristr($shout,'o_o') !== false):
                    $a="".$CURUSER['username']." O_o �� �������� � �� ���� ����� �������� ��� ����������� ��! ��� � �� ����?! ";  
                    $b="".$CURUSER['username']." O_o � ?";          
                    break;
                   
                   
                     case $shout == (stristr($shout,'����') !== false):
                     case $shout == (stristr($shout,'�������') !== false):
                     case $shout == (stristr($shout,'������') !== false):
           
                     $a="".$CURUSER['username']." ������� :P";          
                     $b="".$CURUSER['username']." ��� ;)";          
                    break;
                     
                     
					 case $shout == (stristr($shout,'�� ��������') !== false):
                     case $shout == (stristr($shout,'����� ��������') !== false):
           
                     $a="".$CURUSER['username']." �� ���� �������� ��������!";          
                     $b="".$CURUSER['username']." � ��� �� ��������? ���� � ����� ���������!";          
                    break;
                     
                     case $shout == (stristr($shout,'�� ������?') !== false):
                     $a="".$CURUSER['username']." � ����� ����!";          
                     $b="".$CURUSER['username']." � ����� �, � �����...";          
                    break;
                     
                     
                                           
                     case $shout == (stristr($shout,'�������') !== false):
                     case $shout == (stristr($shout,'������') !== false):
                     case $shout == (stristr($shout,'���') !== false):
                     case $shout == (stristr($shout,'�����') !== false):
           
                     $a="".$CURUSER['username']." �� �� �� ��� ;-)";          
                     $b="".$CURUSER['username']." ������ ������ ;-)";          
                    break;
                     
                      case $shout == (stristr($shout,'���� �����?') !== false):
                     case $shout == (stristr($shout,'����� �����') !== false):
           
                     $a="".$CURUSER['username']." ���, �� ����� ������ �� �������� �����";          
                     $b="".$CURUSER['username']." ����, � ������� ������";          
                    break;
                     
                     
                         case $shout == (stristr($shout,'�� ����') !== false):
                     case $shout == (stristr($shout,'�� ����') !== false):
           
                     $a="".$CURUSER['username']." ���� ���� ���, ����������!";          
                     $b="".$CURUSER['username']." ��� �� ����, ��� ����� ��� �� ����";          
                    break;
                     
                     
                     case $shout == (stristr($shout,'������') !== false):
                     case $shout == (stristr($shout,'������� ��') !== false):
           
                     $a="".$CURUSER['username']." ����  :blush:";          
                     $b="".$CURUSER['username']." ��� � �������";          
                    break;
                     
                     case $shout == (stristr($shout,'���� � ���') !== false):
                     $a="".$CURUSER['username']." �� ���� � � ���� ���� ������� � ��� :P ������� �� �������.";                  $b="".$CURUSER['username']." �� ����� ��";          
                    break;
                     
                     case $shout == (stristr($shout,'�����?') !== false):
                     $a="".$CURUSER['username']." �� �����."; 
                    $b="".$CURUSER['username']." ��";          
                    break;
                    
                      case $shout == (stristr($shout,'�����?') !== false):
                     $a="".$CURUSER['username']." �� �����.";          
                     $b="".$CURUSER['username']." ����� )))";          
                    break;
                     
                     
					 case $shout == (stristr($shout,'������?') !== false):
                     $a="".$CURUSER['username']." ���, ������� ���������� ��� ����������, ������...";                           $b="".$CURUSER['username']." �� ����";          
                     break;
                     
                     
                     
					 case $shout == (stristr($shout,':blush:') !== false):
                     $a="".$CURUSER['username']." ��������� �������� ����������� ������ :blush:";                              $b="".$CURUSER['username']." �������� ���� ��� ����, ���� ������ �� �������....";          
                    break; 
					 
					 case $shout == (stristr($shout,'�� �� �����') !== false):
                     $a="".$CURUSER['username']." � ������ �� ���! ���������� :P";                                              $b="".$CURUSER['username']." ��� ����, ���";          
                    break; 
					                   
					 case $shout == (stristr($shout,'��� ���') !== false):
                     case $shout == (stristr($shout,'����� ���') !== false):
                     case $shout == (stristr($shout,'������� ����?') !== false):
                     case $shout == (stristr($shout,'���� �����?') !== false):
                     case $shout == (stristr($shout,'���� ���?') !== false):
           
                     $a="".$CURUSER['username']." ��� ������, ������� � ���� � ������ �� �������!";                             $b="".$CURUSER['username']." � ���, ��� ���?";          
                     break;
                     
                     
                     case $shout == (stristr($shout,'����� ���� ������') !== false):
                     case $shout == (stristr($shout,'���� ������') !== false):
                               
                     $a="".$CURUSER['username']." ��, ��������� �����, �� ���� �������, ������� ���� 27 ������ �����, ����� ��� �� 40, ���� ������ ��������, ������ ��� alfa ��� �� �����. �� ������������ �������� �� �������� �� ������� ������� ����. ��� �� ��������, �����.";          
                     $b="".$CURUSER['username']." Alfa ������ - 27 �����";          
                    break;
                     
                     case $shout == (stristr($shout,':)') !== false):
                     case $shout == (stristr($shout,':-)') !== false):
               
                     $a="".$CURUSER['username']." ;-) �������"; 
					 $b="".$CURUSER['username']." �� ������ ������ ��� ��?";          
                    break;         
                    
                     
                     
                    case $shout == (stristr($shout,'��������') !== false):
                              
                     $a="".$CURUSER['username']." � �� ����, �� ����?! ";          
                     $b="".$CURUSER['username']." �� ������� �� ���, ��� ����.";          
                    break;;
                     
                     
					 case $shout == (stristr($shout,'�����...') !== false):
                     $a="".$CURUSER['username']." ��� �����.";          
                     $b="".$CURUSER['username']." :)";          
                    break;
                     
                     
                     case $shout == (stristr($shout,'����?') !== false);
                    $a="".$CURUSER['username']." ���������, � � ����?";          
                   $b="".$CURUSER['username']." ������";          
                    break;
                   
       
                    	case $shout == "$bot �������";
                    $a="".$CURUSER['username']." ������ :)";          
                 $b="".$CURUSER['username']." � ���� :)";          
                    break;
                   
                   
                   
                       case $shout == (stristr($shout,'��� ���� ���������') !== false);
                    $a="".$CURUSER['username']." 7Max7 ��� �� ������ Tesla TT 2009 ����";          
                   $b="".$CURUSER['username']." ���� ��� ��� ))";          
                    break;
                   
                    case $shout == (stristr($shout,'��') !== false);
                    $a="".$CURUSER['username']." �� ��������, � ��� �� ����! :P";          
                   $b="".$CURUSER['username']." ��������� ���������";          
                    break;
                   
            
                   
                   case $shout == (stristr($shout,'�� ���') !== false);
                   case $shout == (stristr($shout,'�� ���') !== false);
                   case $shout == "$bot ���?";
                    $a="".$CURUSER['username']." �� ��� �!";          
                    $b="".$CURUSER['username']." ���.";          
                    break;
                   
                      case $shout == (stristr($shout,'����') !== false):
                    case $shout == (stristr($shout,'����') !== false):
                    $a="".$CURUSER['username']." ������...";          
                   $b="".$CURUSER['username']." ����!!!";          
                    break;
                   
                    case $shout == (stristr($shout,';-)') !== false);
                    case $shout == (stristr($shout,';)') !== false):
                    $a="".$CURUSER['username']." �� ���������� ��� :P ��������";          
                   $b="".$CURUSER['username']." ������ ����� ������� �� ���������";          
                    break;                 
                   
                    case $shout == (stristr($shout,'��� ��������?') !== false);
                    case $shout == (stristr($shout,'��� �����?') !== false):
                    $a="".$CURUSER['username']." ������ ���� ��� ;-)";          
                  $b="".$CURUSER['username']." ����, ��� ��� ...";          
                    break;   
                   
                      case $shout == (stristr($shout,'������ ��� �����') !== false);
                      case $shout == (stristr($shout,'������ ����') !== false);
                   $a="".$CURUSER['username']." ��� �����������";          
                  $b="".$CURUSER['username']." ����� ���� �������, ����� �������";          
                    break;   
                   
                   
                   case $shout == (stristr($shout,'�����') !== false);
                   $a="".$CURUSER['username']." � �� ��� ������ ��.";          
                   $b="".$CURUSER['username']." �� � ���� ����������� ����� ���� ����";          
                    break;  
                   
                     case $shout == (stristr($shout,'����') !== false);
                     case $shout == (stristr($shout,'�����') !== false);
                     $a="".$CURUSER['username']." ������ �� ������ :lol:";          
                   $b="".$CURUSER['username']." ��������� ���� ��������))";          
                    break;
                   
                      case $shout == (stristr($shout,'������') !== false);
                     case $shout == (stristr($shout,'��������') !== false);
                     $a="".$CURUSER['username']." ��� ��� ������ � �� �����! :D"; 
					 $b="".$CURUSER['username']." ������ ������, ��� � �����";          
                    break;         
                   break;  
                   
                      case $shout == (stristr($shout,'����') !== false);
                     $a="".$CURUSER['username']." �� ���� ����, � ������ ����� � �������� �������� :kiss: ";                    $b="".$CURUSER['username']." ����? ������?";          
                    break; 
                   
                      case $shout == (stristr($shout,'�������..') !== false);
                     $a="".$CURUSER['username']." �� �� �� ����� ... :D ";                   
					 $b="".$CURUSER['username']." �������� ��������� :D";          
                    break;
                   
                  
				      case $shout == (stristr($shout,'������?') !== false);
                     $a="".$CURUSER['username']." �� ������ ...";          
                    $b="".$CURUSER['username']." ������ ������";          
                    break;
                          
			   case $shout == (stristr($shout,'� �����') !== false);
                     $a="".$CURUSER['username']." ��� ��";          
                    $b="".$CURUSER['username']." � ��� ����� ��������� ������?";          
                    break;
                          
                          
					case $shout == (stristr($shout,'��� �� ������ � 7Max7') !== false);
                     $a="".$CURUSER['username']." � ����� �������...";
					 $b="".$CURUSER['username']." ���� �� ������ �� �����.";          
                    break;          
                   
					
						case $shout == (stristr($shout,'������') !== false);
							case $shout == (stristr($shout,'������?') !== false);
                     $a="".$CURUSER['username']." ��� ��������, ������ ��� �� � ���� ������ :P";
					 $b="".$CURUSER['username']." ��� :D";          
                    break;          
                   
					
						case $shout == (stristr($shout,'����� ��') !== false);
                     $a="".$CURUSER['username']." ���� �� ������, ".$CURUSER['username']." ����� ��?";
					 $b="".$CURUSER['username']." ��� �������, ���� �����������?!";          
                    break;          
                   
					
					
					case $shout == (stristr($shout,'���� ������ ������') !== false);
                     $a="".$CURUSER['username']." ������ ������ ��� ����, ���������� ��������, ����������� �������� � ���������� ������ ������ ����� � �����. � ����� � �� ����((";  
					 $b="".$CURUSER['username']." ������ ������ ��� Assassin, �������� �������, ��� �� ���������, ��� � ���������, ��� ��������. ����� ���� DIABOLIK.";          
                    break;        
                   
					
					
						    case $shout == (stristr($shout,'������?') !== false);
                     $a="".$CURUSER['username']." � ����� ������ ������������� ����� �������!";          
                    $b="".$CURUSER['username']." ���� ��������";          
                    break;
					
					
						    case $shout == (stristr($shout,'� �����') !== false);
                     $a="".$CURUSER['username']." ������ ��� ����� ... ���";          
                     $b="".$CURUSER['username']." ����� ������";          
                    break; 
					
						    case $shout == (stristr($shout,'��� �� ������ � Shantel') !== false);
                     $a="".$CURUSER['username']." ����...���...";          
                     $b="".$CURUSER['username']." ���� �� ������ �� �����.";          
                    break;
						              
                      case $shout == (stristr($shout,'������') !== false);
             
                      case $shout == (stristr($shout,'����') !== false);
                      case $shout == (stristr($shout,'����') !== false);
                      case $shout == (stristr($shout,'������') !== false);
                       case $shout == (stristr($shout,'�������') !== false);
                     $a="".$CURUSER['username']." ���";          
                     $b="".$CURUSER['username']." ��";          
                    break;;  
                     
                                        
					case $shout == (stristr($shout,'��� ��') !== false):
					case $shout == (stristr($shout,'����� ��') !== false):
					case $shout == (stristr($shout,'� ����') !== false):
					
                    $a="".$CURUSER['username']." �� ����� :P";          
                    $b="".$CURUSER['username']." ��� ���!";          
                    break;
                    
                    
                    case $shout == (stristr($shout,'�� ���') !== false):
					case $shout == (stristr($shout,'���� ���') !== false):
				
                    $a="".$CURUSER['username']." :rant: �� ���� �";  
					$b="".$CURUSER['username']." ������� ����� :P";          
                    break;
                    
					case $shout == (stristr($shout,'��������?') !== false):
                    $a="".$CURUSER['username']." ��� ������� �� ������"; 
					$b="".$CURUSER['username']." ������ ���� ��������� ������";           
                    break;
                    
                    case $shout == (stristr($shout,'�������') !== false):
                    case $shout == (stristr($shout,'����') !== false):
                    $a="".$CURUSER['username']." �� �� ������ �� :D"; 
					$b="".$CURUSER['username']." � ������� ������!";            
                    break;
                    
                    case $shout == (stristr($shout,'��.') !== false):
                    case $shout == (stristr($shout,'� �') !== false):
                    $a="".$CURUSER['username']." � ��� �����, ��� ������ �������� ���������? :P";
					$b="".$CURUSER['username']." ��� ����� ���� �� ��������";           
                    break;
                    
                    case $shout == (stristr($shout,'��������?') !== false):
                    case $shout == (stristr($shout,'�������?') !== false):
                    case $shout == (stristr($shout,'�������?') !== false):
                    case $shout == (stristr($shout,'����������?') !== false):
                    $a="".$CURUSER['username']." ���, � ����� ����� ������...";
					$b="".$CURUSER['username']." ������� ����, ������.";         
				    break;
                    
                    
                    case $shout == (stristr($shout,'��?') !== false):
                    $a="".$CURUSER['username']." ������� ��� :P";   
					$b="".$CURUSER['username']." ��� ������, ����� �� � ��� ������.";        
                    break;
                    
                         case $shout == (stristr($shout,'����') !== false):
                    $a="".$CURUSER['username']." ���� �� �����������, � �� ������� ��!"; 
					$b="".$CURUSER['username']." ����� �� ���������, � �� ���� �����.";          
                    break;
                    
                    
					case $shout == (stristr($shout,'������') !== false):
                    case $shout == (stristr($shout,'������') !== false):
                                                  
                    $a="".$CURUSER['username']." ������ ��..."; 
					$b="".$CURUSER['username']." �����, ����� ��������.";            
                    break;
                    
                    
                      case $shout == (stristr($shout,'���� 1 2 3') !== false):
                    $a="".$CURUSER['username']." ���� 4 5 6 ������ �������"; 
					$b="".$CURUSER['username']." ����� 4 5 6";          
                    break;
                    
                    
                    case $shout == (stristr($shout,'����� ����') !== false):
                    case $shout == (stristr($shout,'����� �����') !== false):
                    $a="".$CURUSER['username']." ��������.";   
					$b="".$CURUSER['username']." ����� �����";       
                    break;
                     
                     case $shout == (stristr($shout,'���������') !== false):
                    case $shout == (stristr($shout,'������') !== false):
                    $a="".$CURUSER['username']." :-O";  
					$b="".$CURUSER['username']." ���� ��� :-O";           
                    break;
                     
                     
                        case $shout == (stristr($shout,'�� �������') !== false):
                    case $shout == (stristr($shout,'�� �������������') !== false):
                    case $shout == (stristr($shout,'�� �����������') !== false):
                    case $shout == (stristr($shout,'�����������') !== false):
                    $a="".$CURUSER['username']." :blush: ������";    
					$b="".$CURUSER['username']." ����� ��� �����?";         
                    break;
                    
                           case $shout == (stristr($shout,'���������') !== false):
                    case $shout == (stristr($shout,'��������') !== false):
                    case $shout == (stristr($shout,'������� ����') !== false):
                    $a="".$CURUSER['username']." :shifty: ���";  
					$b="".$CURUSER['username']." �����";         
                    break;
                    
                    
                    
					case $shout == (stristr($shout,'!!!!!') !== false):
                    $a="".$CURUSER['username']." �� �� �� �������� ���� :D!!!!";  
					$b="".$CURUSER['username']." ����� ������������� !!!!";           
                    break;
                    
                    case $shout == (stristr($shout,'����') !== false):
                   $a="".$CURUSER['username']." ������, ���� �� ��������";  
					$b="".$CURUSER['username']." ��� � ��������.";           
                    break;
                   
                                                
                    
            //  case $shout == (strstr($shout,'��') !== false);
                 //   $a="".$CURUSER['username']." ����� �� ��, � �� ��� ����� ��� ������� :D";          
                //   $b="".$CURUSER['username']." ������, �� ����� � 2009 ����, ����� �� �� �������, � ������ ����������";          
                //    break;
                 
             // default: 
        //    $a="..."; $b=".../";
                  }
                 

 	 if (!$a==""){

 	$newshout = array($a,$b);
shuffle($newshout);

$newshout = $newshout[0];
$date = time()+15; 

sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 
}

 }
 */
 
 
 
 
 
 
 /*
 $end_time = microtime();
$end_array = explode(" ",$end_time);
$end_time = $end_array[1] + $end_array[0];
// �������� �� ��������� ������� ���������
$time = $end_time - $start_time;
// ������
$date=get_date_time();

$time = substr($time, 0, 8);
$memory = round(memory_get_usage()/1024);

echo "<hr><b>$time</b>";
 */
 

?>