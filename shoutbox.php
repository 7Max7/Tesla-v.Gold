<?

/*
// считываем текущее время
$start_time = microtime();
// разделяем секунды и миллисекунды
//(становятся значениями начальных ключей массива-списка)
$start_array = explode(" ",$start_time);
// это и есть стартовое время
$start_time = $start_array[1] + $start_array[0];
*/
$on_vika=true;



require_once("include/bittorrent.php");
///require_once("include/init.php");
dbconn();
///loggedinorreturn();
header("Content-Type: text/html; charset=Windows-1251");

//die("включится через 10 минут.");
if ($CURUSER["shoutbox"] <> '0000-00-00 00:00:00'){
die("Запрещено Использовать Чат до <b>".$CURUSER["shoutbox"]."</b>");
}

if (!$CURUSER){
die("Авторизуйтесь");
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


$botid = 92; /// id нашего бота

$init_bonus=1; /// сколько даем бонуса за 1 ответ без подсказок
$first_answ=180; // добавляем 3 минуты на вопросы
$medium_answ=180; // среднее время для обычных вопросов (интервал)
$prome=30; /// интервал между вставкой новых вопросов 30 сек
$hint_time_f=30; /// оставшее время после 1 подсказки
$hint_time_t=30; /// оставшее время после 2 подсказки


$sq_zad=sql_query("SELECT vi.*, v.question,v.answer FROM victorina AS vi
LEFT JOIN vquestions AS v ON v.id=vi.question_id
WHERE vi.id=1") or sqlerr(__FILE__, __LINE__); 
$zad_bot=mysql_fetch_array($sq_zad);

$id_zad=$zad_bot["question_id"];
$begin_time_zad=$zad_bot["begin_time"];
$end_time_zad=$zad_bot["end_time"];
$num=$zad_bot["num"];
$all_bonus=$zad_bot["all_bonus"]; /// сколько всего бонусов засчитанно
$question_zad=$zad_bot["question"]; // вопрос
$answer_zad=tolower($zad_bot["answer"]); // ответ на него
$ansstr_zad="[".strlen($zad_bot["answer"])." букв(ы)]"; /// сколько символов


$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $first_answ); /// добавляем 3 минуты
$quest_id=mt_rand(1, $zad_bot["numque"]);

$quest_id2=mt_rand(2, $zad_bot["numque"]);

//// обновляем снова значение в базе если все пусто
if (empty($zad_bot["answer"]) && $zad_bot["enable"]=="on"){
sql_query("UPDATE victorina SET hint='first', enable='on', begin_time=".sqlesc($begin_time).", end_time=".sqlesc($end_time).", question_id=".sqlesc($quest_id2).", num=num+1 WHERE id=1");
//header("Refresh: 0; url=tracker-chat.php");
die("Воссоздание нового вопроса с id $quest_id2 <script>setTimeout('document.location.href=shoutbox.php', 3);</script>");
}
//// обновляем значение в базе если все пусто


$shout = str_replace("+", "%2B", (isset($_GET["shout"]) ? $_GET["shout"]:""));
$shout=convert_text(urldecode(decode_unicode_url($shout)));


if ($end_time_zad <= get_date_time() && $zad_bot["enable"]=="wait" && empty($shout)){

if ($zad_bot["hint"]=="first"){

$word=$answer_zad;
$strlen=strlen($word);
$str = $word;
$strlen_min2=$strlen-1;
//$strlast = substr($str, -1); /// посл буква
$strfirst = substr($str, 0, 1); /// первая буква
$view="[b]".$strfirst."[/b]";
for ($x=0; $x<$strlen_min2; $x++){
$view.="#";
}
///$view.="[b]".$strlast."[/b]";

$newshout = "[color=gray]Подсказка #1/2[/color]: $view (+$hint_time_f сек)";
$date = time()+1; 

$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $hint_time_f); /// добавляем две минуты
sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 
	
sql_query("UPDATE victorina SET enable='wait', end_time=" . sqlesc($end_time) . ", hint='second' WHERE id=1");	
}
elseif ($zad_bot["hint"]=="second"){

$word=$answer_zad;
$strlen=strlen($word);
$str = $word;
$strlen_min2=$strlen-2;
$strlast = substr($str, -1); /// первая буква
$strfirst = substr($str, 0, 1); /// посл буква
$view2="[b]".$strfirst."[/b]";
for ($x=0; $x<$strlen_min2; $x++){
$view2.="#";
}
$view2.="[b]".$strlast."[/b]";

$newshout = "[color=gray]Подсказка #2/2[/color]: $view2 (+$hint_time_t сек)";
$date = time()+1; 

$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $hint_time_t); /// добавляем две минуты
sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 
	
sql_query("UPDATE victorina SET enable='wait', end_time=".sqlesc($end_time).", hint='third' WHERE id=1");
}

elseif ($zad_bot["hint"]=="third"){
/////// отключаем викторину спустя некоторое время
$newshout = "[color=gray]Правильный ответ[/color]: [b]$answer_zad [/b] | Викторина отключена (Ответов: $num. Зачисленно бонусов: $all_bonus)";
$date = time()+5; 
sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 

sql_query("UPDATE victorina SET enable='off', hint='first', num='0',all_bonus='0.00', question_id='0' WHERE id=1") or sqlerr(__FILE__, __LINE__); 	
}

unset($shout);
}
/////// отключаем викторину спустя 3 (факт 2) минуты не активности


if ($begin_time_zad <= get_date_time() && $zad_bot["enable"]=="on"){
$newshout = "[color=Red]Вопрос[/color]: $question_zad $ansstr_zad";
$date = time(); 

sql_query("INSERT INTO shoutbox (userid, date, text, ouserid, answer) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . "," . sqlesc($answer_zad).")") or sqlerr(__FILE__, __LINE__); 
	
sql_query("UPDATE victorina SET enable='wait',num='0' WHERE id=1") or sqlerr(__FILE__, __LINE__); 
}
/////// показываем вопрос пользователям

if ($begin_time_zad <= get_date_time() && $zad_bot["enable"]=="new"){
$newshout = "[color=green]Вопрос[/color]: $question_zad $ansstr_zad";
$date = time(); 

sql_query("INSERT INTO shoutbox (userid, date, text, ouserid, answer) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . "," . sqlesc($answer_zad).")") or sqlerr(__FILE__, __LINE__); 
sql_query("UPDATE victorina SET enable='wait' WHERE id=1") or sqlerr(__FILE__, __LINE__); 	
}
/////// показываем вопрос пользователям


if (isset($_GET["do"]) && $_GET["do"] == "shout") {

$_GET["shout"] = str_replace("+", "%2B", $_GET["shout"]);
    $shout = convert_text(urldecode(decode_unicode_url($_GET["shout"])));
    if ($shout == "/prune" && get_user_class() >= UC_ADMINISTRATOR) {
        mysql_query("TRUNCATE TABLE shoutbox");
        die("Все сообщения были почищенны.");
    }



///// выполняем принудительную подсказку по слову 'подсказка'
if ($zad_bot["hint"]=="first" && $zad_bot["enable"]=="wait" && $shout=="подсказка"){

$word=$answer_zad;
$strlen=strlen($word);
$str = $word;
$strlen_min2=$strlen-1;
//$strlast = substr($str, -1); /// посл буква
$strfirst = substr($str, 0, 1); /// первая буква
$view="[b]".$strfirst."[/b]";
for ($x=0; $x<$strlen_min2; $x++){
$view.="#";
}
///$view.="[b]".$strlast."[/b]";

$newshout = "[color=gray]Подсказка #1/2[/color]: $view (+$hint_time_f сек) Вызванна: [color=#".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"])."]".$CURUSER["username"]."[/color]";
$date = time(); 

$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $hint_time_f); 
sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 
	
sql_query("UPDATE victorina SET enable='wait', end_time=" . sqlesc($end_time) . ", hint='second' WHERE id=1");	

unset($shout);
}
elseif ($zad_bot["hint"]=="second" && $zad_bot["enable"]=="wait" && $shout=="подсказка"){

$word=$answer_zad;
$strlen=strlen($word);
$str = $word;
$strlen_min2=$strlen-2;
$strlast = substr($str, -1); /// первая буква
$strfirst = substr($str, 0, 1); /// посл буква
$view2="[b]".$strfirst."[/b]";
for ($x=0; $x<$strlen_min2; $x++){
$view2.="#";
}
$view2.="[b]".$strlast."[/b]";

$newshout = "[color=gray]Подсказка #2/2[/color]: $view2 (+$hint_time_t сек) Вызванна: [color=#".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"])."]".$CURUSER["username"]."[/color]";
$date = time(); 

$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $hint_time_t);
sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 
	
sql_query("UPDATE victorina SET enable='wait', end_time=".sqlesc($end_time).", hint='third' WHERE id=1");
unset($shout);
}
///// выполняем принудительную подсказку по слову 'подсказка'
$razmer_otveta=strlen($zad_bot["answer"]); /// нужный размер + 2 символа добавил ниже
$razmer_now=strlen($shout); /// ориг размер сообщения пользователя

if ($razmer_now<=($razmer_otveta+2) && @stristr(tolower($shout),$answer_zad)!== false && $zad_bot["enable"]=="wait"){
	

if ($zad_bot["hint"]=="first"){
$bonus=$init_bonus;
}elseif ($zad_bot["hint"]=="second"){
$bonus=$init_bonus/2;	
$hinti=" + 1 подсказка";
}elseif ($zad_bot["hint"]=="third"){
$bonus=$init_bonus/4;	
$hinti=" + 2 подсказки";
}


$newshout = "[color=green]Правильный ответ[/color]: [b] $answer_zad [/b] дал пользователь [color=#".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"])."]".$CURUSER["username"]."[/color] ($bonus бонус$hinti). Следующий вопрос через $prome сек.";

$date = time();
sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 

sql_query("UPDATE users SET bonus=bonus+$bonus WHERE id = " . $CURUSER["id"]) or sqlerr(__FILE__,__LINE__);

////////////////////// задаем новый вопрос ////////////////
$sq=sql_query("SELECT enable,(SELECT COUNT(*) FROM vquestions) as num_que FROM victorina WHERE id=1") or sqlerr(__FILE__, __LINE__); 
$r_bot=mysql_fetch_array($sq);
//enum('off','wait','on')

$launcher=$CURUSER["id"];

$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $medium_answ);
$quest_id=mt_rand(1, $r_bot["num_que"]);
//// обновляем значение в базе
$time_s = get_date_time();
sql_query("UPDATE victorina SET enable='new', all_bonus=all_bonus+'$bonus', hint='first', num=num+1, end_time=".sqlesc($end_time).", begin_time=".sqlesc($begin_time).",  question_id=".sqlesc($quest_id)." WHERE id=1") or sqlerr(__FILE__,__LINE__);
sql_query("UPDATE vquestions SET last_check=".sqlesc($time_s)." WHERE id=".sqlesc($id_zad)."") or sqlerr(__FILE__,__LINE__);
////////////////////// задаем новый вопрос ////////////////
}

//// пытаемся запустить викторину
if ($shout=='викторина' || $shout=='старт' || $shout=='стоп' && get_user_class()>= UC_MODERATOR) {

$sq=sql_query("SELECT enable,(SELECT COUNT(*) FROM vquestions) as num_que FROM victorina WHERE id=1") or sqlerr(__FILE__, __LINE__); 
$r_bot=mysql_fetch_array($sq);
//enum('off','wait','on')

if ($shout=='стоп' && get_user_class()>= UC_MODERATOR){
	
$newshout = "Викторину остановил: [color=#".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"])."]".$CURUSER["username"]."[/color]".($r_bot["enable"]=="on" ? ".":" | [color=gray]Правильный ответ был[/color]: [b]".$answer_zad."[/b] (ответов: $num, всего зачисленно бонусов: $all_bonus)")."";
$date = time()+5; 

sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__);

$tesla_tracker=1; /// 1 если версия движка Tesla TT 2009 Иначе ставьте 0

if ($tesla_tracker){
//// пытаюсь выполнить запрос в редкое время для обновления, т е как можно маловероятно
if (strtotime($CURUSER['last_access']) < (strtotime(get_date_time()) - 250)){
$num_av  = get_row_count("vquestions");
}
if (empty($num_av) && empty($zad_bot["numque"])){
$num_av="130540";
}
elseif(empty($num_av) && !empty($zad_bot["numque"])){
$num_av=$zad_bot["numque"]; // присваиваю последнее значение
}}
else
$num_av  = get_row_count("vquestions");

sql_query("UPDATE victorina SET hint='first', enable='off', num='0', all_bonus='0.00', numque='$num_av' WHERE id=1");
unset($shout);
}
elseif ($shout=='стоп' &&  get_user_class()< UC_MODERATOR){
die("Вы не можете остановить викторину. <script>setTimeout('document.location.href=shoutbox.php', 4);</script>");
}

$launcher=$CURUSER["id"];
$begin_time = get_date_time(gmtime() + $prome);
$end_time=get_date_time(gmtime() + $first_answ); /// добавляем 3 минуты
$quest_id=mt_rand(1, $r_bot["num_que"]);

//// обновляем значение в базе
if ($r_bot["enable"]=="off" && $shout<>'стоп'){
sql_query("UPDATE victorina SET hint='first', enable='on', begin_time=".sqlesc($begin_time).", end_time=".sqlesc($end_time).", question_id=".sqlesc($quest_id).", num='0' WHERE id=1");


$newshout = "Викторину запустил: [color=#".get_user_rgbcolor($CURUSER["class"], $CURUSER["username"])."]".$CURUSER["username"]."[/color] через $prome сек появится вопрос, будьте готовы к ответам.";
$date = time()+5; 

sql_query("INSERT INTO shoutbox (userid, date, text,ouserid) VALUES (" .  sqlesc($botid) . ", ".$date.", " . sqlesc(format_comment($newshout)) . ", " . sqlesc($botid) . ")") or sqlerr(__FILE__, __LINE__); 
}

//// если вдруг нет в базе значений о викторине
if (empty($r_bot["enable"]) && $shout<>'стоп'){
sql_query("INSERT INTO victorina (hint, question_id, begin_time, end_time,enable,num) VALUES (".implode(", ", array_map("sqlesc", array('first', $quest_id, $begin_time, $end_time, "on","0"))).")") or sqlerr(__FILE__,__LINE__);
}
//// если вдруг нет в базе значений о викторине



unset($shout);
}    
//// пытаемся запустить викторину


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
print("<script>alert('Введите сообщение');</script>");

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
   <form method=post action=shoutbox.php>  Редактировать текст сообщения $user под номером: ".$res['id']."<br>
   <textarea rows=4 readonly cols=80>".htmlspecialchars($res['text'])."</textarea><br>
   
   <input type=hidden name=id value=".(int)$res['id'].">
   <textarea name=text rows=4 cols=80 id=specialbox>".htmlspecialchars($res['text'])."</textarea><br>
	<input type=submit name=save value=\"Обновить сообщение\" class=btn>
    </form>
    
  	<form action=tracker-chat.php><input type=submit value=\"Обратно в чат\" class=btn>
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
        if(strlen($text) > 1000) die("Слишком длинный текст");
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
<td align=\"center\" class=\"a\" ><a title=\"".$CURUSER["username"]." нажми сюда!!!\" href=\"message.php?action=new\">У вас новое сообщение</a></td>
<tr></table>";
echo $newmessageview;
};

//if ($CURUSER["class"]>=4)// модератор
//{
//$victo = "<small>старт - вкл викторину: стоп - остановить.</small>";	
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
$pic="<img src=\"/pic/warned.gif\" alt=\"Предупрежден\">";
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
echo "<span title=\"Удалить\" onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer; color: red; font-weight: bold; text-decoration: underline\"><img src=\"pic/warned2.gif\" style=\"border: 0px;\" /></span> ";

if ($CURUSER["class"] > UC_MODERATOR)
echo "<a href=shoutbox.php?edit=".$arr["id"]."><img src=\"pic/forum.gif\" width=\"12px\" border=0></a> ";


echo "<span class=\"date\"title=\"".normaltime(get_date_time($arr["date"]),true)."\" onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value=parent.document.shoutform.shout.value+'[".strftime("%H:%M:%S",$arr["date"])."] ';return false;\" style=\"cursor: crosshair; font-weight: italic;\"class=\"date\">[".strftime("%H:%M:%S",$arr["date"])."]</span> ";


if (stristr($arr["text"], "privat(".$CURUSER["username"].")")!==false)
echo "<span style=\"background: #E9E9E6;\" title=\"Выделить приват в поле для ввода\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='privat(".$username.") '+parent.document.shoutform.shout.value;return false;\">Приват от </span>";


$uspm="";
if (preg_match("/privat\(([^()<>\s]+?)\)/i",$arr["text"]) && ($arr["userid"]==$CURUSER["id"])){
	
preg_match('/privat\((.*?)\)/is',$arr["text"],$uspm_);
$uspm=$uspm_[1];


echo "<span ".(!empty($uspm) ? "onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='privat($uspm) '+parent.document.shoutform.shout.value;return false;\"":"")." style=\"background: #F9F9F9;\">Приват к </span>";
}

if (empty($uspm))
echo "<a href=\"userdetails.php?id=".$arr["uid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$username."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($arr["class"], $arr["username"])."</a>: ";


if (!empty($uspm))
echo "<span onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='privat($uspm) '+parent.document.shoutform.shout.value;return false;\" style=\"background: #F9F9F9;\">$uspm</span>: ";

if (!empty($arr["num_warned"]))
echo "<img src=\"/pic/warned.gif\" alt=\"Предупрежден\">";


//$arr["text"] = preg_replace("/privat\((.*)\)/is","", $arr["text"]);
$arr["text"] = str_replace("privat(".$CURUSER["username"].")","",$arr["text"]);
$arr["text"] = str_replace("privat(".$uspm.")","",$arr["text"]);


echo $arr["text"];
 
if ($CURUSER["class"]==UC_SYSOP){

if (($ousername<>$username && $arr["uid"]=="92"))
echo "<a title=\"От имени ViKa написал\">[</a><a href=\"userdetails.php?id=".$arr["ouserid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$ousername."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($oclass, $ousername) . "</a><a title=\"От имени ViKa написал\">]</a> ";

if ($arr["uid"]==0 && !$arr["username"])
echo "<a title=\"От имени SysteM написал\">[</a><a href=\"userdetails.php?id=".$arr["ouserid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$ousername."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($oclass, $ousername) . "</a><a title=\"От имени SysteM написал\">]</a>";

if (!empty($answer_sql))
echo "<font title=\"Показ для боссов\" style='color: blue'>[</font>".$answer_sql."<font  title=\"Показ для боссов\" style='color: blue'>]</font> ";

echo "<br>";
}


echo ("</td></tr>\n" );
} /// внизу если не приватное


elseif (!preg_match("/privat\(([^()<>\s]+?)\)/i",$arr["text"])) {

print("<tr><td style='vertical-align: top;' $cellclass>");


if (get_user_class() >=UC_MODERATOR)
echo "<span title=\"Удалить\" onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer; color: red; font-weight: bold; text-decoration: underline\"><img src=\"pic/warned2.gif\" style=\"border: 0px;\" /></span> ";

if (get_user_class() > UC_MODERATOR)
echo "<a href=shoutbox.php?edit=".$arr["id"]."><img src=\"pic/forum.gif\" width=\"12px\" border=0></a> ";

echo "<span class=\"date\"title=\"".normaltime(get_date_time($arr["date"]),true)."\" onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value=parent.document.shoutform.shout.value+'[".strftime("%H:%M:%S",$arr["date"])."] ';return false;\" style=\"cursor: crosshair; font-weight: italic;\"class=\"date\">[".strftime("%H:%M:%S",$arr["date"])."]</span> ";

echo "<a href=\"userdetails.php?id=".$arr["uid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$username."[/b]: '+parent.document.shoutform.shout.value;return false;\">
".get_user_class_color($arr["class"], $arr["username"])."</a>: ";

if (!empty($arr["num_warned"]))
echo "<img src=\"/pic/warned.gif\" alt=\"Предупрежден\">";

//$arr["text"] = preg_replace("/privat\((.*)\)/is","", $arr["text"]);
$arr["text"] = str_replace("privat(".$CURUSER["username"].")","",$arr["text"]);
$arr["text"] = str_replace("privat(".$uspm.")","",$arr["text"]);



echo $arr["text"];
 
if ($CURUSER["class"]==UC_SYSOP){

if (($ousername<>$username && $arr["uid"]=="92"))
echo "<a title=\"От имени ViKa написал\">[</a><a href=\"userdetails.php?id=".$arr["ouserid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$ousername."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($oclass, $ousername) . "</a><a title=\"От имени ViKa написал\">]</a> ";

if ($arr["uid"]==0 && !$arr["username"])
echo "<a title=\"От имени SysteM написал\">[</a><a href=\"userdetails.php?id=".$arr["ouserid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$ousername."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($oclass, $ousername) . "</a><a title=\"От имени SysteM написал\">]</a>";

if (!empty($answer_sql))
echo "<font title=\"Показ для боссов\" style='color: blue'>[</font>".$answer_sql."<font  title=\"Показ для боссов\" style='color: blue'>]</font> ";

echo "<br>";
}


echo ("</td></tr>\n" );
} ///


/// для босса всех
if (preg_match("/privat\(([^()<>\s]+?)\)/i",$arr["text"]) && !stristr($arr["text"], "privat(".$CURUSER["username"].")") && ($arr["userid"]<>$CURUSER["id"]) && (get_user_class() == UC_SYSOP || $CURUSER["id"]=="1654")) {

print("<tr><td style='vertical-align: top;' $cellclass>");

echo "<span title=\"Удалить\" onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer; color: red; font-weight: bold; text-decoration: underline\"><img src=\"pic/warned2.gif\" style=\"border: 0px;\" /></span> ";

echo "<a href=shoutbox.php?edit=".$arr["id"]."><img src=\"pic/forum.gif\" width=\"12px\" border=0></a> ";

echo "<span class=\"date\"title=\"".normaltime(get_date_time($arr["date"]),true)."\" onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value=parent.document.shoutform.shout.value+'[".strftime("%H:%M:%S",$arr["date"])."] ';return false;\" style=\"cursor: crosshair; font-weight: italic;\"class=\"date\">[".strftime("%H:%M:%S",$arr["date"])."]</span> ";


$uspm="";
if (preg_match("/privat\(([^()<>\s]+?)\)/i",$arr["text"])){
	
preg_match('/privat\((.*?)\)/is',$arr["text"],$uspm_);
$uspm=$uspm_[1];

echo "<span style=\"background: #F9F9F9; font-weight: bold;\">Чужой Приват </span>";
}

echo "<a href=\"userdetails.php?id=".$arr["uid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$username."[/b]: '+parent.document.shoutform.shout.value;return false;\">
".get_user_class_color($arr["class"], $arr["username"])."</a> ";
//("/приватно \[(.*?)\]/"

if (!empty($uspm))
echo "<b>к <span onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='privat($uspm) '+parent.document.shoutform.shout.value;return false;\" style=\"background: #F9F9F9;\">$uspm</span></b>: ";

//$arr["text"] = preg_replace("/privat\((.*)\)/is","", $arr["text"]);

$arr["text"] = str_replace("privat(".$CURUSER["username"].")","",$arr["text"]);
$arr["text"] = str_replace("privat(".$uspm.")","",$arr["text"]);

echo $arr["text"];

if (($ousername<>$username && $arr["uid"]=="92"))
echo "<a title=\"От имени ViKa написал\">[</a><a href=\"userdetails.php?id=".$arr["ouserid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$ousername."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($oclass, $ousername) . "</a><a title=\"От имени ViKa написал\">]</a> ";

if ($arr["uid"]==0 && !$arr["username"])
echo "<a title=\"От имени SysteM написал\">[</a><a href=\"userdetails.php?id=".$arr["ouserid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$ousername."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($oclass, $ousername) . "</a><a title=\"От имени SysteM написал\">]</a>";

if (!empty($answer_sql))
echo "<font title=\"Показ для боссов\" style='color: blue'>[</font>".$answer_sql."<font  title=\"Показ для боссов\" style='color: blue'>]</font> ";

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
 
 (stristr($shout,'пизда') !== false)
|| (stristr($shout,'сука') !== false)
|| (stristr($shout,'хуй') !== false)
|| (stristr($shout,'пидар') !== false)
|| (stristr($shout,'хуйло') !== false)
//|| (stristr($shout,'ебан') !== false)
 || (stristr($shout,'ебал') !== false)
  || (stristr($shout,'жопа') !== false)
    || (stristr($shout,'соска') !== false)
        || (stristr($shout,'ебло') !== false)
 )
 
{
$userid=$CURUSER["id"];
$num=$CURUSER["num_warned"]+1;
$modcomment = sqlesc(date("Y-m-d") . " - +1 предупреждение от ViKa в чате.\n");
sql_query("UPDATE users SET num_warned = $num, modcomment = CONCAT($modcomment, modcomment) WHERE class <4 and id = $userid");

if ($CURUSER["num_warned"]>"4"){
$userid=$CURUSER["id"];
$num2=$CURUSER["num_warned"];
$modcomment = sqlesc(date("Y-m-d") . " - болеее $num2 предупреждений ViKa (чат)\n");
sql_query("UPDATE users SET enabled='no', modcomment = CONCAT($modcomment, modcomment) WHERE class <4 and id = $userid");	
}
}


   /*       
 
 	if ((stristr($shout,'[b]ViKa[/b]') !== false)&&($shout!=="[b]ViKa[/b]:") || 
	 (
(stristr($shout,'привет всем') !== false) || (stristr($shout,'прива всем') !== false) || (stristr($shout,'приветик всем') !== false) || (stristr($shout,'hi all') !== false) || (stristr($shout,'привед всем') !== false)
|| (stristr($shout,'пизда') !== false)
|| (stristr($shout,'сука') !== false)
|| (stristr($shout,'хуй') !== false)
|| (stristr($shout,'пидар') !== false)
|| (stristr($shout,'хуйло') !== false)
|| (stristr($shout,'ебан') !== false)
|| (stristr($shout,'гавно') !== false)
|| (stristr($shout,'ебал') !== false)
|| (stristr($shout,'жопа') !== false)
|| (stristr($shout,'соска') !== false)
|| (stristr($shout,'ебло') !== false)

     )
	 ) {
 		
		 
		 
		 	
 		$bot="[b]ViKa[/b]:";
//////////////////////////
if ((stristr($shout,'последний торрент') !== false)&& $CURUSER["class"]>="4"){

$res = mysql_query("SELECT name,owner,added,(SELECT username FROM users WHERE id=torrents.owner) AS classusername  FROM torrents WHERE  banned = 'no' ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0 )
while ($arr = mysql_fetch_assoc($res)) {
{
$owned="[b]Последний торрент[/b]: ".htmlspecialchars($arr["name"])." был залит ".$arr["classusername"]." в ".$arr["added"];
}}}
else
{
$owned="извините, только для администрации сайта.";
}


if ((stristr($shout,'проверить торренты') !== false)&& $CURUSER["class"]>="4"){

$res = mysql_query("SELECT name,id FROM torrents WHERE  banned = 'no' and moderated='no' ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0 )
while ($arr = mysql_fetch_assoc($res)) {
{
$owned_moderat="[b]Последний не проверенный торрент[/b]: [url=$DEFAULTBASEURL/details.php?id=".$arr['id']."]".htmlspecialchars($arr["name"])."[/url]. Пожалуйста проверьте его, чтобы пользователь начал сидировать.";
}}}
else
{
$owned_moderat="извините, только для администрации сайта.";
}



//////////////////////////
if ((stristr($shout,'кто в чате') !== false)&& $CURUSER["class"]>="4"){

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
if (stristr($shout,'моя подпись') !== false){
$userid=$CURUSER["id"];
$res = mysql_query("SELECT info FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0 )
while ($arr = mysql_fetch_assoc($res)) {
{ 
	if ($arr["info"]==""){
$info="ваша подпись пуста";}
else
$info="[i]Ваша подпись ниже[/i]:\n".htmlspecialchars_uni($arr["info"]);
}}
}
/////////////////////////
if (stristr($shout,'твоя подпись') !== false){
$userid=$CURUSER["id"];
$res = mysql_query("SELECT info FROM users WHERE id=92") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0 )
while ($arr = mysql_fetch_assoc($res)) {
{ 
	if ($arr["info"]==""){
$vikainfo="моя подпись пуста";}
else
$vikainfo="[i]Моя подпись ниже[/i]:\n".htmlspecialchars_uni($arr["info"]);
}}
}
/////////////////////////

/////////////////////////
if ((stristr($shout,'почисть чат') !== false) || (stristr($shout,'очисть чат') !== false))
{
	if ($CURUSER['class']>=5){
  mysql_query("TRUNCATE TABLE shoutbox");}  
}

/////////////////////////
if (stristr($shout,'выполнить очистку') !== false){
if ($CURUSER['class']==6){
	

	//Считываем текущее время
$mtime = microtime();
//Разделяем секунды и миллисекунды
$mtime = explode(" ",$mtime);
//Составляем одно число из секунд и миллисекунд
$mtime = $mtime[1] + $mtime[0];
//Записываем стартовое время в переменную
$tstart = $mtime; 



require_once('include/cleanup.php');

$s_s = $queries;
docleanup();
$s_e = $queries;


		//Делаем все то же самое, чтобы получить текущее время
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
//Записываем время окончания в другую переменную
$tend = $mtime;
//Вычисляем разницу
$totaltime = ($tend - $tstart);
//Выводим не экран
}}


 		 if ((stristr($shout,'мои сообщения') !== false) && $CURUSER["class"]>="4"){
$res1 = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location=1 AND unread='yes'") or sqlerr(__FILE__,__LINE__);
$arr1 = mysql_fetch_row($res1);
$unread = $arr1[0];
$newmessage1 = $unread . " нов" . ($unread > 1 ? "ых" : "ое"); 
$newmessage2 = " сообщен" . ($unread > 1 ? "ий" : "ие"); 
$newmessage = "$newmessage1 $newmessage2"; 

    if ($unread)
    {

$unread2="[b][url=$DEFAULTBASEURL/message.php] у вас $newmessage [/url][/b]";

    }
    else 
    
    $unread2="[b]у тебя нет новых сообщений[/b]";
    
	
	}
	else
	  {
    $unread2="извини, но эта функция только для администрации";
	}
	;
    
                  switch ($shout)
                  {
                  	                  			
    
	              	   case $shout == (stristr($shout,'почисть чат') !== false):
                      case $shout == (stristr($shout,'очисть чат') !== false):
                  /// $a="".$CURUSER['username']." ".($CURUSER['class']>="5" ? "готово, почистила весь чат." : " ааа ?")
;           $a="".$CURUSER['username']." ".($CURUSER['class']>="5" ? " Нет :P" : " ааа ?")
;          
                     $b=$a;          
                    break;	
						  
						  
					 case $shout == (stristr($shout,'гавно') !== false):	  	
                  	 case $shout == (stristr($shout,'сука') !== false):
                     case $shout == (stristr($shout,'пизд') !== false):
                     case $shout == (stristr($shout,'тварь') !== false):
                     case $shout == (stristr($shout,'сосать') !== false):
                     case $shout == (stristr($shout,'хуй') !== false):
                      case $shout == (stristr($shout,'пидар') !== false):
                      case $shout == (stristr($shout,'ебан') !== false):
                         case $shout == (stristr($shout,'ебал') !== false):
                            case $shout == (stristr($shout,'жопа') !== false):
                              case $shout == (stristr($shout,'гавно') !== false):
                                  case $shout == (stristr($shout,'соска') !== false):
                      case $shout == (stristr($shout,'ебло') !== false):
                    
                    
                    $a="".$CURUSER['username']." ".($CURUSER['class']>"4" ? " не нужно так, администрация должна быть вежливой." : ($CURUSER[num_warned]>"4" ? "У тебя около $CURUSER[num_warned] предупреждений, через пару минут тебя отключит система, Поки.":"+1 ($num из 5) к твоему уровню предупреждений, не матерись больше!"))
;          
                     $b=$a;          
                    break;
                    
                     case $shout == (stristr($shout,'выполнить очистку') !== false):
                    
                    $a="".$CURUSER['username']." ".($CURUSER['class']=="6" ? "Очистка завершена успешно. На очистку использовано ".($s_e - $s_s)." запросов ($totaltime)" : " вы не босс, извините вам отказанно.")
;          
                     $b=$a;          
                    break;
                  	
                  	
                 case (stristr($shout,'Бот')!==FALSE):
                     $a="".$CURUSER['username']." :unsure: с чего взял ?";          
                     $b="".$CURUSER['username']." обижаешь";          
                    break;
                            
					case $shout == "$bot бот";
					case $shout == "$bot Бот";
					case (stristr($shout,'пупсик')!==FALSE):
					case (stristr($shout,'ты бот')!==FALSE):
                    $a="".$CURUSER['username']." сам такой, гад :P";          
                    $b="".$CURUSER['username']." посмотри на себя, малышь";          
                    break;
                    
             
                  case $shout == "$bot да";
				  case $shout == "$bot Да";
                  $a="".$CURUSER['username']." неа :P";          
                  $b="".$CURUSER['username']." нет конечно";          
                    break;
                   
                  
				   case (stristr($shout,'Кто?')!==FALSE):
					case (stristr($shout,'Кто???')!==FALSE):
                    $a="".$CURUSER['username']." как кто ТЫ!!!";          
                   $b="".$CURUSER['username']." ТЫ!!!";          
                    break;
                    
                                      
			case (stristr($shout,'гы')!==FALSE):
                     $a="".$CURUSER['username']."  :lol:";          
                     $b="".$CURUSER['username']." ;-)";          
                    break;


                        case (stristr($shout,'секс')!==FALSE):
                     $a="".$CURUSER['username']."  нет я отдам тока TOPILSKY_11";          
                     $b="".$CURUSER['username']." я больше не дам!!! я девственница!!!";          
                    break;


		     case (stristr($shout,'убери')!==FALSE):
                     $a="".$CURUSER['username']."   гы :-p  ";          
                     $b="".$CURUSER['username']." на фиг :-p ";
                    break;


                       
				   case (stristr($shout,'мдя')!==FALSE):
				   $a="".$CURUSER['username']." эммммдемс";          
                   $b="".$CURUSER['username']." мда правильнее";          
                    break;
                  
                    
                    
				   case $shout == "$bot ip";
			       $a="".$CURUSER['username']." ваш ip равен ".$CURUSER['ip']."";          
                   $b="".$CURUSER['username']." ваш ip равен ".$CURUSER['ip']."";          
                    break;
                   
                     case $shout == "$bot браузер";
			       $a="".$CURUSER['username']." ваш браузер равен ".getenv("HTTP_USER_AGENT")."";          
                   $b=$a;          
                    break;
                    
                   
                   case $shout == (stristr($shout,'мои сообщения') !== false):
			       $a="".$CURUSER['username']." $unread2";          
                    $b=$a;          
                    break;
                    
                      case $shout == (stristr($shout,'последний торрент') !== false):
			       $a="".$CURUSER['username']." $owned";          
                  $b=$a;          
                    break;
                    
                   case $shout == (stristr($shout,'проверить торренты') !== false):
			       $a="".$CURUSER['username']." $owned_moderat";          
                  $b=$a;          
                    break;
                    
                   
                       case $shout == (stristr($shout,'моя подпись') !== false):
			       $a="".$CURUSER['username']." $info";          
                     $b=$a;           
                    break;
                   
                  
                      case $shout == (stristr($shout,'твоя подпись') !== false):
			       $a="".$CURUSER['username']." $vikainfo";          
                     $b=$a;           
                    break;
              
                     
                                   
                            
                      case $shout == (stristr($shout,'кто в чате') !== false):
			       $a="".($CURUSER['class']>="4" ? "В чате $s4etc: $title_who_c" : "".$CURUSER['username']." ааа ?")."";          
                     $b=$a;           
                    break;
                    
                  
                    case $shout == (stristr($shout,'почисть чат') !== false):
                    case $shout == (stristr($shout,'очисть чат') !== false):
                    $a="".$CURUSER['username']." ".($CURUSER['class']>="5" ? "готово, почистила весь чат." : " ааа ?")
;          
                     $b=$a;          
                    break;	
              
			   
                    case $shout == "$bot нет";
				    case $shout == "$bot Нет";
                    $a="".$CURUSER['username']." да :D";          
                    $b="".$CURUSER['username']." ууу да.";          
                    break;
                                      
                   
                    case $shout == (stristr($shout,'чего?') !== false):
                    $a="".$CURUSER['username']." сего :D";          
                    $b="".$CURUSER['username']."...";          
                    break;
                   
                      case $shout == (stristr($shout,'интересно') !== false):
                       case $shout == (stristr($shout,'интерестно') !== false):
                    $a="".$CURUSER['username']." Интересоваться в библиотеке будешь!";          
                    $b="".$CURUSER['username']." иди в библиотеку, там интересуйся :P";          
                    break;
                   
                    case $shout == (stristr($shout,'....') !== false):
                    $a="".$CURUSER['username']." ... взаимно";          
                     $b="".$CURUSER['username']." и что за тишина в твоих пробеллах ?.";          
                    break;
                    
                    
                      case $shout == (stristr($shout,'Красотулька') !== false):
                    $a="".$CURUSER['username']." да я такая :-)";          
                     $b="".$CURUSER['username']." хи хи";          
                    break;
                    
                    
                      case $shout == (stristr($shout,'познакомимся') !== false):
                      case $shout == (stristr($shout,'подружимся') !== false):
                      
                    $a="".$CURUSER['username']." а ты уверен, что этого хочешь ? :D";          
                     $b="".$CURUSER['username']." не хочу, слишком много вопросов задаешь ты :P";          
                    break;
                    
                    
                         case $shout == (stristr($shout,'так его') !== false):
                                           
                    $a="".$CURUSER['username']." не подстрекай :D";          
                     $b="".$CURUSER['username']." или тебя так :P";          
                    break;
                    
                   case $shout == (stristr($shout,'не красиво') !== false):
                                           
                    $a="".$CURUSER['username']." и что ?";          
                     $b="".$CURUSER['username']." а ты не смотри тогда :)";          
                    break;
                    
                        case $shout == (stristr($shout,'глупцы они') !== false):
                      case $shout == (stristr($shout,'глупы они') !== false):
                      
                    $a="".$CURUSER['username']." есть такое в них...";          
                     $b="".$CURUSER['username']." не отрицаю.";          
                    break;
                    
                    
                          case $shout == (stristr($shout,'будешь?') !== false):
                   ///   case $shout == (stristr($shout,'глупы они') !== false):
                      
                    $a="".$CURUSER['username']." будешь будешь.";          
                     $b="".$CURUSER['username']." предложи еще раз, вдруг передумаю.";          
                    break;
                    
                   
				   case $shout == (stristr($shout,':lol:') !== false):
                  
                    $a="".$CURUSER['username']." вот ржун ...";          
                     $b="".$CURUSER['username']." .... думай еще";          
                    break;
                    
                    
                     
					case $shout == (stristr($shout,'же ты занята?') !== false):
                 	case $shout == (stristr($shout,'что делаешь?') !== false):
                 
                    $a="".$CURUSER['username']." тебе скажи и сам захочешь:P";          
                     $b="".$CURUSER['username']." я защищаю докторскую степень с психологическим уклоном в жизнь людей которые общающются с реальным ботом чате и их поведение за счет него, ясно? ";          
                    break;
                    
                    			case $shout == (stristr($shout,'дай ему') !== false):
                 //	case $shout == (stristr($shout,'что делаешь?') !== false):
                 
                    $a="".$CURUSER['username']." дай дай а возьми и забери";          
                     $b=$a;          
                    break;
                    
                    
                    case $shout == (stristr($shout,'чего издеваешься') !== false):
                    case $shout == (stristr($shout,'издеваешься ты') !== false):
                    $a="".$CURUSER['username']." хочу привлечь внимание к себе :whistle:";          
                     $b="".$CURUSER['username']." может мне скучно, что запретишь мне? я же ничего плохого не сделала :innocent:";          
                    break;
                    
                    
                    case $shout == (stristr($shout,'так себе') !== false):
                    case $shout == (stristr($shout,'так се') !== false):
                    $a="".$CURUSER['username']." ясненько";          
                     $b="".$CURUSER['username']." понятненько :yes:";          
                    break;
                    
                    case $shout == (stristr($shout,'любишь?') !== false):
             
                    $a="".$CURUSER['username']." век конечно он нужен....ответ Нет";          
                     $b="".$CURUSER['username']." Ответ неа.";          
                    break;
                    
                    
                    
                       case $shout == (stristr($shout,'макс тебя любит') !== false):
             
                    $a="".$CURUSER['username']." нет, он любят не меня.";          
                     $b="".$CURUSER['username']." другу он любит, девушку.";          
                    break;
                    
                    
                    
                      case $shout == (stristr($shout,'встречатся?') !== false):
                    case $shout == (stristr($shout,'увидемся?') !== false):
                    $a="".$CURUSER['username']." обойдешься";          
                     $b="".$CURUSER['username']." ага тот случай, не буду я встречатся с тобой.";          
                    break;
                    
                    case $shout == (stristr($shout,'кто такой DJFeel?') !== false):
                    case $shout == (stristr($shout,'руденко фас') !== false):
                    $a="".$CURUSER['username']." Знаменитый DJ Александр Руденко [пиарщина хвалится хлебом] :D";          
                     $b="".$CURUSER['username']." [url=$DEFAULTBASEURL/userdetails.php?id=25917]Этот ?[/url] тогда не знаю.";          
                    break;
                    
                    
                     case $shout == (stristr($shout,'вредина') !== false):
                    case $shout == (stristr($shout,'врединка') !== false):
                    $a="".$CURUSER['username']." я не вредина я Вика :P";          
                     $b="".$CURUSER['username']." ммм, а что мне будет если скажу Правда это?!";          
                    break;
                    
                    
                    
                    case $shout == "$bot :P":
                                          
                    $a="".$CURUSER['username']." оторву же, останешься без него :P";          
                    $b="".$CURUSER['username']." язычек вижу рабочий, тебе б в нужное русло!";          
                    break;
                    
                    
                     case $shout == (stristr($shout,'красотка') !== false):
                    $a="".$CURUSER['username']." да я такая, и что?";          
                    $b="".$CURUSER['username']." спс.";          
                    break;
                    
                                         
                    case $shout == (stristr($shout,'в обиде') !== false):
                    $a="".$CURUSER['username']." мне пох ;-)";          
                     $b="".$CURUSER['username']." хихи.";          
                    break;
                    
                    
                    case $shout == (stristr($shout,'эй!') !== false):
                    $a="".$CURUSER['username']." гей что ли?";          
                     $b="".$CURUSER['username']." тебе лишь бы погееть :D";          
                    break;
                    
                    
                    case $shout == (stristr($shout,'столько же') !== false):
                    $a="".$CURUSER['username']." прикольненько";          
                     $b="".$CURUSER['username']." ясно.";          
                    break;
                    
                    case $shout == (stristr($shout,'что делать ?') !== false):
                    $a="".$CURUSER['username']." займись делом каким то! а не мною.";          
                     $b="".$CURUSER['username']." сидеть.";          
                    break;
                    
                        
                    case $shout == (stristr($shout,'учишься?') !== false):
                    case $shout == (stristr($shout,'работаешь?') !== false):
                    $a="".$CURUSER['username']." я всего лишь учусь быть хорошим ботом на трекере ;-)";                        $b="".$CURUSER['username']." не имеет значение";          
                    break;
                    
                     case $shout == (stristr($shout,'знаешь меня?') !== false):
                    case $shout == (stristr($shout,'слышала обо мне?') !== false):
                    $a="".$CURUSER['username']." неа, а кто ты ?";                       
					$b="".$CURUSER['username']." в нете?.";          
                    break;
                    
                    case $shout == (stristr($shout,'тебя зовут') !== false):
                    case $shout == (stristr($shout,'тебя звать') !== false):
                    case $shout == (stristr($shout,'твое имя') !== false):
                    
                    $a="".$CURUSER['username']." Виктория или просто Вика (v. Alfa)";          
                    $b="".$CURUSER['username']." Вика";          
                    break;
                    
                    
                     case $shout == (stristr($shout,'привет') !== false):
                     case $shout == (stristr($shout,'Даров') !== false):
                     case $shout == (stristr($shout,'прива') !== false):
                     case $shout == (stristr($shout,'приветик') !== false):
           
                     $a="".$CURUSER['username']." Приветик.";          
                     $b="".$CURUSER['username']." Привет :)";          
                    break;
                    
                    
                    
                     case $shout == (stristr($shout,'привет всем') !== false):
                     case $shout == (stristr($shout,'прива всем') !== false):
                     case $shout == (stristr($shout,'приветик сем') !== false):
                     case $shout == (stristr($shout,'даров сем') !== false):
                     case $shout == (stristr($shout,'приветик') !== false):
                     case $shout == (stristr($shout,'hi all') !== false):
                     case $shout == (stristr($shout,'ривед сем') !== false):
           
                     $a="".$CURUSER['username']." и тебе Привет ;-)";          
                     $b="".$CURUSER['username']." Приветик :)";          
                     break;
                     
                              
                    
                      case $shout == (stristr($shout,'отлично') !== false):
                     case $shout == (stristr($shout,'нормально') !== false):
                     case $shout == (stristr($shout,'хорошо') !== false):
                     case $shout == (stristr($shout,'хреново') !== false):
                     case $shout == (stristr($shout,'фигасе') !== false):
                
                     $a="".$CURUSER['username']." :-)";          
                     $b="".$CURUSER['username']." Ж-)";          
                     break;
                     
                    
                     case $shout == (stristr($shout,'ку ку') !== false):
       
                     $a="".$CURUSER['username']." и сколько лет мне осталось жить, кукушка?";          
                     $b="".$CURUSER['username']." и куку вылечат...";          
                     break;
                     
                    
                   
                     case $shout == (stristr($shout,'тебе лет') !== false):
                     case $shout == (stristr($shout,'сколько лет') !== false):
                     case $shout == (stristr($shout,'возраст') !== false):
           
                     $a="".$CURUSER['username']." не красиво у девушки такое спрашивать :P";          
                    $b="".$CURUSER['username']." а тебе зачем?.";          
                    break;
                   
                   
                    case $shout == (stristr($shout,'нада!') !== false):
                    case $shout == (stristr($shout,'нужно!') !== false):
                     case $shout == (stristr($shout,'да!!!') !== false):
           
                     $a="".$CURUSER['username']." да по шее тебе нужно! как кролика";          
                    $b="".$CURUSER['username']." не нада";          
                    break;
                   
                   
                   
                     case $shout == (stristr($shout,'счастье!') !== false):
                 
                     $a="".$CURUSER['username']." не мое счастье то ;-) а администарции.";          
                    $b="".$CURUSER['username']." кому что";          
                    break;
                   
                   
                     case $shout == (stristr($shout,'милая') !== false):
                     case $shout == (stristr($shout,'милашка') !== false):
                     case $shout == (stristr($shout,'милочка') !== false):
                     case $shout == (stristr($shout,'милачка') !== false):
           
                    $a="".$CURUSER['username']." я тебе не милая, гад.";          
                    $b="".$CURUSER['username']." разогнался ишь ты.";          
                    break;
                   
                     case $shout == (stristr($shout,'есть парень?') !== false):
                     case $shout == (stristr($shout,'есть муж?') !== false):
                     case $shout == (stristr($shout,'есть половинка?') !== false):
                     case $shout == (stristr($shout,'замужем?') !== false):
  
                    $a="".$CURUSER['username']." Да, вся администрация сайта, я их всех люблю!";          
                    $b="".$CURUSER['username']." да :)";          
                    break;
                   
                   
                    case $shout == (stristr($shout,'ты девушка?') !== false):
                    case $shout == (stristr($shout,'ты девочка?') !== false):
           
                    $a="".$CURUSER['username']." нет блин я парень :lol:";          
                    $b="".$CURUSER['username']." к чему вопрос то?";          
                    break;
                   
                    
					case $shout == (stristr($shout,'вместе?') !== false):
                    
                    $a="".$CURUSER['username']." мечтать не вредно";          
                    $b="".$CURUSER['username']." размечтался";          
                    break;
                   
                   
                    case $shout == (stristr($shout,'не будь стервой') !== false):
                    case $shout == (stristr($shout,'не стервой') !== false):
           
                    $a="".$CURUSER['username']." Мне всего лишь хочется поиграть с тобой.";          
                    $b="".$CURUSER['username']." не будь гадом и я не буду ею";          
                    break;
                   
                   case $shout == (stristr($shout,'ты парень?') !== false):
                   case $shout == (stristr($shout,'ты пацан?') !== false):
           
                    $a="".$CURUSER['username']." мдя...смотри на ник мой, после спрашивай";          
                    $b="".$CURUSER['username']." нет я бот :lol:.";          
                    break;
                   
                   case $shout == (stristr($shout,'тихо!') !== false):
                   case $shout == (stristr($shout,'помолчи') !== false):
                   case $shout == (stristr($shout,'замолкни') !== false):
           
                    $a="".$CURUSER['username']." Ты мне поговори! Ишь ты!";          
                    $b="".$CURUSER['username']." не указывай мне что мне делать, козлик.";          
                    break;
                   
                   
                        case $shout == (stristr($shout,'выйдеш?') !== false):
                  
                    $a="".$CURUSER['username']." кккуда? Oo!";          
                    $b="".$CURUSER['username']." ты что увидеть меня хочешь?";          
                    break;
                   
                     case $shout == (stristr($shout,'или нет') !== false):
                  case $shout == (stristr($shout,'или да') !== false):
                    $a="".$CURUSER['username']." или да";          
                    $b="".$CURUSER['username']." все через или у вас ))";          
                    break;
                  
                    case $shout == (stristr($shout,'скоро тебя купят') !== false):
                           
                    $a="".$CURUSER['username']." угу, я рада моей копии на других трекерах :D";          
                    $b="".$CURUSER['username']." клево же, уйду от вас.";          
                    break;
                   
                     case $shout == (stristr($shout,'солнышко') !== false):
                     case $shout == (stristr($shout,'солнце') !== false):
                      case $shout == (stristr($shout,'солнц') !== false):
                     case $shout == (stristr($shout,'зая') !== false):
                       case $shout == (stristr($shout,'любовь моя') !== false):
                      
                 
                 
                    $a="".$CURUSER['username']." ты мой козлик, я тебе не солнце,солнышко и тд.";          
                    $b="".$CURUSER['username']." козличенок, не нада так говорить на меня.";          
                    break;
                                      
                    case $shout == (stristr($shout,'что делаешь?') !== false):
                    case $shout == (stristr($shout,'че делаешь?') !== false):
                    case $shout == (stristr($shout,'чем маешься?') !== false):
           
                    $a="".$CURUSER['username']." :hmm: отвечаю тебе в чате, что еще делаю ;-)";          
                    $b="".$CURUSER['username']." сижу удивляюсь тебе";          
                    break;
                   
                    case $shout == (stristr($shout,'малышка') !== false):
                    case $shout == (stristr($shout,'мой малыш') !== false):
           
                    $a="".$CURUSER['username']." хм, послать может или обедется.";          
                    $b="".$CURUSER['username']." тсс малышкО!";          
                    break;
                   
                    
					case $shout == (stristr($shout,'а чему?') !== false):
                             
                    $a="".$CURUSER['username']." тому о чем речь пошла";          
                    $b="".$CURUSER['username']." сам понял, что сказал то?";          
                    break;
                   
                    case $shout == (stristr($shout,'просто так') !== false):
                    case $shout == (stristr($shout,'да просто спросил') !== false):
           
                    $a="".$CURUSER['username']." ничего просто так не бывает, всему есть свои идеи, мысли.";          
                    $b="".$CURUSER['username']." угу, уже договаривай всю мысль";          
                    break;
                   
                    case $shout == (stristr($shout,'о_о') !== false):
                    case $shout == (stristr($shout,'o_o') !== false):
                    $a="".$CURUSER['username']." O_o да посмотрю я на тебя таким взглядом как испугаешься ты! Что я не могу?! ";  
                    $b="".$CURUSER['username']." O_o и ?";          
                    break;
                   
                   
                     case $shout == (stristr($shout,'ясно') !== false):
                     case $shout == (stristr($shout,'понятно') !== false):
                     case $shout == (stristr($shout,'хорошо') !== false):
           
                     $a="".$CURUSER['username']." надеюсь :P";          
                     $b="".$CURUSER['username']." ага ;)";          
                    break;
                     
                     
					 case $shout == (stristr($shout,'ты странная') !== false):
                     case $shout == (stristr($shout,'очень странная') !== false):
           
                     $a="".$CURUSER['username']." На себя посмотри СУЩЕСТВО!";          
                     $b="".$CURUSER['username']." а сам не странный? если с ботом общаешься!";          
                    break;
                     
                     case $shout == (stristr($shout,'ты откуда?') !== false):
                     $a="".$CURUSER['username']." с Земли блин!";          
                     $b="".$CURUSER['username']." с земли я, с земли...";          
                    break;
                     
                     
                                           
                     case $shout == (stristr($shout,'спасибо') !== false):
                     case $shout == (stristr($shout,'пасибо') !== false):
                     case $shout == (stristr($shout,'спс') !== false):
                     case $shout == (stristr($shout,'сенкс') !== false):
           
                     $a="".$CURUSER['username']." Да не за что ;-)";          
                     $b="".$CURUSER['username']." будешь должен ;-)";          
                    break;
                     
                      case $shout == (stristr($shout,'есть фотка?') !== false):
                     case $shout == (stristr($shout,'скинь фотку') !== false):
           
                     $a="".$CURUSER['username']." неа, но очень похожа на джессику альбу";          
                     $b="".$CURUSER['username']." есть, в профиле смотри";          
                    break;
                     
                     
                         case $shout == (stristr($shout,'на твой') !== false):
                     case $shout == (stristr($shout,'на тебя') !== false):
           
                     $a="".$CURUSER['username']." Оооо даже так, оближешься!";          
                     $b="".$CURUSER['username']." угу на меня, так сразу все на меня";          
                    break;
                     
                     
                     case $shout == (stristr($shout,'мничка') !== false):
                     case $shout == (stristr($shout,'умничка ты') !== false):
           
                     $a="".$CURUSER['username']." знаю  :blush:";          
                     $b="".$CURUSER['username']." ага я умничка";          
                    break;
                     
                     case $shout == (stristr($shout,'тебя в опу') !== false):
                     $a="".$CURUSER['username']." по сути и я тебя могу послать в опу :P совести не хватает.";                  $b="".$CURUSER['username']." да пошел ты";          
                    break;
                     
                     case $shout == (stristr($shout,'опять?') !== false):
                     $a="".$CURUSER['username']." да опять."; 
                    $b="".$CURUSER['username']." да";          
                    break;
                    
                      case $shout == (stristr($shout,'снова?') !== false):
                     $a="".$CURUSER['username']." да снова.";          
                     $b="".$CURUSER['username']." опять )))";          
                    break;
                     
                     
					 case $shout == (stristr($shout,'шутишь?') !== false):
                     $a="".$CURUSER['username']." нет, сказала раздевайся так раздевайся, козлик...";                           $b="".$CURUSER['username']." да шучу";          
                     break;
                     
                     
                     
					 case $shout == (stristr($shout,':blush:') !== false):
                     $a="".$CURUSER['username']." поддержку компанию посмущаемся вместе :blush:";                              $b="".$CURUSER['username']." смущайся тебе это идет, если только ты девушка....";          
                    break; 
					 
					 case $shout == (stristr($shout,'ну ты даешь') !== false):
                     $a="".$CURUSER['username']." я ничего не даю! Обойдешься :P";                                              $b="".$CURUSER['username']." даю жару, даю";          
                    break; 
					                   
					 case $shout == (stristr($shout,'дай асю') !== false):
                     case $shout == (stristr($shout,'скинь асю') !== false):
                     case $shout == (stristr($shout,'номерок есть?') !== false):
                     case $shout == (stristr($shout,'есть аська?') !== false):
                     case $shout == (stristr($shout,'есть ася?') !== false):
           
                     $a="".$CURUSER['username']." тот случай, общаюсь в чате и только на трекере!";                             $b="".$CURUSER['username']." а что, это ася?";          
                     break;
                     
                     
                     case $shout == (stristr($shout,'скажи свою версию') !== false):
                     case $shout == (stristr($shout,'твоя версия') !== false):
                               
                     $a="".$CURUSER['username']." хм, интересно самой, но могу сказать, сделали меня 27 января утром, минут так за 40, если верить алфавиту, версия так alfa что ли будет. Не искуственный интелект но ответить на простые вопросы могу. Спс за внимание, чмоки.";          
                     $b="".$CURUSER['username']." Alfa версия - 27 числа";          
                    break;
                     
                     case $shout == (stristr($shout,':)') !== false):
                     case $shout == (stristr($shout,':-)') !== false):
               
                     $a="".$CURUSER['username']." ;-) взаимно"; 
					 $b="".$CURUSER['username']." на словах слабак что ль?";          
                    break;         
                    
                     
                     
                    case $shout == (stristr($shout,'козликом') !== false):
                              
                     $a="".$CURUSER['username']." я же шутя, ты чего?! ";          
                     $b="".$CURUSER['username']." не введись на все, что пишу.";          
                    break;;
                     
                     
					 case $shout == (stristr($shout,'ладно...') !== false):
                     $a="".$CURUSER['username']." ага ладно.";          
                     $b="".$CURUSER['username']." :)";          
                    break;
                     
                     
                     case $shout == (stristr($shout,'дела?') !== false);
                    $a="".$CURUSER['username']." Нормально, а у тебя?";          
                   $b="".$CURUSER['username']." путево";          
                    break;
                   
       
                    	case $shout == "$bot неплохо";
                    $a="".$CURUSER['username']." бывает :)";          
                 $b="".$CURUSER['username']." я рада :)";          
                    break;
                   
                   
                   
                       case $shout == (stristr($shout,'кто твой создатель') !== false);
                    $a="".$CURUSER['username']." 7Max7 его же движок Tesla TT 2009 года";          
                   $b="".$CURUSER['username']." Макс кто еще ))";          
                    break;
                   
                    case $shout == (stristr($shout,'хз') !== false);
                    $a="".$CURUSER['username']." не матерись, я про хз твое! :P";          
                   $b="".$CURUSER['username']." матерится плооохооо";          
                    break;
                   
            
                   
                   case $shout == (stristr($shout,'ты где') !== false);
                   case $shout == (stristr($shout,'ты тут') !== false);
                   case $shout == "$bot тут?";
                    $a="".$CURUSER['username']." да тут я!";          
                    $b="".$CURUSER['username']." тут.";          
                    break;
                   
                      case $shout == (stristr($shout,'Поки') !== false):
                    case $shout == (stristr($shout,'Пока') !== false):
                    $a="".$CURUSER['username']." Прощай...";          
                   $b="".$CURUSER['username']." Пока!!!";          
                    break;
                   
                    case $shout == (stristr($shout,';-)') !== false);
                    case $shout == (stristr($shout,';)') !== false):
                    $a="".$CURUSER['username']." не подмигивай мне :P засранец";          
                   $b="".$CURUSER['username']." смотри чтобы мигалка не сломалась";          
                    break;                 
                   
                    case $shout == (stristr($shout,'что сделаешь?') !== false);
                    case $shout == (stristr($shout,'что будет?') !== false):
                    $a="".$CURUSER['username']." забаню тебя тут ;-)";          
                  $b="".$CURUSER['username']." убью, что еще ...";          
                    break;   
                   
                      case $shout == (stristr($shout,'покажи вою фотку') !== false);
                      case $shout == (stristr($shout,'покажи себя') !== false);
                   $a="".$CURUSER['username']." ага разбежалась";          
                  $b="".$CURUSER['username']." скинь свою сначало, после подумаю";          
                    break;   
                   
                   
                   case $shout == (stristr($shout,'врешь') !== false);
                   $a="".$CURUSER['username']." я не вру вообще то.";          
                   $b="".$CURUSER['username']." не в моей компетенции врать кому либо";          
                    break;  
                   
                     case $shout == (stristr($shout,'хочу') !== false);
                     case $shout == (stristr($shout,'хочет') !== false);
                     $a="".$CURUSER['username']." хотеть не вредно :lol:";          
                   $b="".$CURUSER['username']." отвалится если захочешь))";          
                    break;
                   
                      case $shout == (stristr($shout,'молчиш') !== false);
                     case $shout == (stristr($shout,'притихла') !== false);
                     $a="".$CURUSER['username']." хех тот случай я не такая! :D"; 
					 $b="".$CURUSER['username']." просто занята, вот и молчу";          
                    break;         
                   break;  
                   
                      case $shout == (stristr($shout,'тебя') !== false);
                     $a="".$CURUSER['username']." не нада меня, я вообще белое и пушистое существо :kiss: ";                    $b="".$CURUSER['username']." меня? Уверен?";          
                    break; 
                   
                      case $shout == (stristr($shout,'наконец..') !== false);
                     $a="".$CURUSER['username']." да уж на конец ... :D ";                   
					 $b="".$CURUSER['username']." дождался миленький :D";          
                    break;
                   
                  
				      case $shout == (stristr($shout,'знаешь?') !== false);
                     $a="".$CURUSER['username']." не знаешь ...";          
                    $b="".$CURUSER['username']." знаешь знаешь";          
                    break;
                          
			   case $shout == (stristr($shout,'с какой') !== false);
                     $a="".$CURUSER['username']." той же";          
                    $b="".$CURUSER['username']." а что много вариантов ответа?";          
                    break;
                          
                          
					case $shout == (stristr($shout,'что ты знаешь о 7Max7') !== false);
                     $a="".$CURUSER['username']." я лучше помолчу...";
					 $b="".$CURUSER['username']." знаю но ничего не скажу.";          
                    break;          
                   
					
						case $shout == (stristr($shout,'правду') !== false);
							case $shout == (stristr($shout,'правда?') !== false);
                     $a="".$CURUSER['username']." это неправда, верить вам не в кого просто :P";
					 $b="".$CURUSER['username']." аха :D";          
                    break;          
                   
					
						case $shout == (stristr($shout,'дурак да') !== false);
                     $a="".$CURUSER['username']." твой же вопрос, ".$CURUSER['username']." дурак да?";
					 $b="".$CURUSER['username']." сам подумай, чего спрашиваешь?!";          
                    break;          
                   
					
					
					case $shout == (stristr($shout,'твой черный список') !== false);
                     $a="".$CURUSER['username']." первая жертва это асид, издевается надомной, прикалуется понижает и дописывает разные плохие слова в титле. в обиде я не него((";  
					 $b="".$CURUSER['username']." вторая жертва это Assassin, обзывает роботом, гад он последний, еще и сиреневый, как девченка. Вслед идет DIABOLIK.";          
                    break;        
                   
					
					
						    case $shout == (stristr($shout,'любишь?') !== false);
                     $a="".$CURUSER['username']." я люблю только администрацию этого трекера!";          
                    $b="".$CURUSER['username']." нада подумать";          
                    break;
					
					
						    case $shout == (stristr($shout,'я думал') !== false);
                     $a="".$CURUSER['username']." хорошо что думал ... хех";          
                     $b="".$CURUSER['username']." думай дальше";          
                    break; 
					
						    case $shout == (stristr($shout,'что ты знаешь о Shantel') !== false);
                     $a="".$CURUSER['username']." Дина...она...";          
                     $b="".$CURUSER['username']." знаю да ничего не скажу.";          
                    break;
						              
                      case $shout == (stristr($shout,'викуль') !== false);
             
                      case $shout == (stristr($shout,'Вика') !== false);
                      case $shout == (stristr($shout,'вика') !== false);
                      case $shout == (stristr($shout,'Викуся') !== false);
                       case $shout == (stristr($shout,'викулик') !== false);
                     $a="".$CURUSER['username']." ась";          
                     $b="".$CURUSER['username']." ау";          
                    break;;  
                     
                                        
					case $shout == (stristr($shout,'иди ты') !== false):
					case $shout == (stristr($shout,'пошла на') !== false):
					case $shout == (stristr($shout,'в баню') !== false):
					
                    $a="".$CURUSER['username']." не пойду :P";          
                    $b="".$CURUSER['username']." сам иди!";          
                    break;
                    
                    
                    case $shout == (stristr($shout,'ты моя') !== false):
					case $shout == (stristr($shout,'вика моя') !== false):
				
                    $a="".$CURUSER['username']." :rant: не твоя я";  
					$b="".$CURUSER['username']." надейся умник :P";          
                    break;
                    
					case $shout == (stristr($shout,'ответишь?') !== false):
                    $a="".$CURUSER['username']." нет конечно не отвечу"; 
					$b="".$CURUSER['username']." отвечу если попросишь хорошо";           
                    break;
                    
                    case $shout == (stristr($shout,'козочка') !== false):
                    case $shout == (stristr($shout,'коза') !== false):
                    $a="".$CURUSER['username']." ну ну козлик же :D"; 
					$b="".$CURUSER['username']." О великий козлик!";            
                    break;
                    
                    case $shout == (stristr($shout,'тд.') !== false):
                    case $shout == (stristr($shout,'т д') !== false):
                    $a="".$CURUSER['username']." и так далее, что тяжело написать полностью? :P";
					$b="".$CURUSER['username']." так далее пиши не сокращай";           
                    break;
                    
                    case $shout == (stristr($shout,'погуляем?') !== false):
                    case $shout == (stristr($shout,'выйдешь?') !== false):
                    case $shout == (stristr($shout,'вободна?') !== false):
                    case $shout == (stristr($shout,'встретимся?') !== false):
                    $a="".$CURUSER['username']." нет, я очень очень занята...";
					$b="".$CURUSER['username']." закатай губу, малышь.";         
				    break;
                    
                    
                    case $shout == (stristr($shout,'ну?') !== false):
                    $a="".$CURUSER['username']." баранки гну :P";   
					$b="".$CURUSER['username']." уже ничего, после ну я все забыла.";        
                    break;
                    
                         case $shout == (stristr($shout,'дура') !== false):
                    $a="".$CURUSER['username']." чего ты обзываешься, я же девушка ну!"; 
					$b="".$CURUSER['username']." лучше не обзывайся, а то хуже будет.";          
                    break;
                    
                    
					case $shout == (stristr($shout,'ПривеД') !== false):
                    case $shout == (stristr($shout,'привед') !== false):
                                                  
                    $a="".$CURUSER['username']." медвед ты..."; 
					$b="".$CURUSER['username']." Ладно, скажу Привееет.";            
                    break;
                    
                    
                      case $shout == (stristr($shout,'тест 1 2 3') !== false):
                    $a="".$CURUSER['username']." тест 4 5 6 прошел успешно"; 
					$b="".$CURUSER['username']." уррра 4 5 6";          
                    break;
                    
                    
                    case $shout == (stristr($shout,'очень жаль') !== false):
                    case $shout == (stristr($shout,'очень жалко') !== false):
                    $a="".$CURUSER['username']." возможно.";   
					$b="".$CURUSER['username']." агась жалко";       
                    break;
                     
                     case $shout == (stristr($shout,'трахнемся') !== false):
                    case $shout == (stristr($shout,'трахну') !== false):
                    $a="".$CURUSER['username']." :-O";  
					$b="".$CURUSER['username']." даже так :-O";           
                    break;
                     
                     
                        case $shout == (stristr($shout,'ты расивая') !== false):
                    case $shout == (stristr($shout,'ты сногшибальная') !== false):
                    case $shout == (stristr($shout,'ты божественна') !== false):
                    case $shout == (stristr($shout,'хорошенькая') !== false):
                    $a="".$CURUSER['username']." :blush: угумсь";    
					$b="".$CURUSER['username']." разве это плохо?";         
                    break;
                    
                           case $shout == (stristr($shout,'присмотрю') !== false):
                    case $shout == (stristr($shout,'прослежу') !== false):
                    case $shout == (stristr($shout,'следить буду') !== false):
                    $a="".$CURUSER['username']." :shifty: ага";  
					$b="".$CURUSER['username']." удачи";         
                    break;
                    
                    
                    
					case $shout == (stristr($shout,'!!!!!') !== false):
                    $a="".$CURUSER['username']." да ты на чувствах вижу :D!!!!";  
					$b="".$CURUSER['username']." какой эмоциональный !!!!";           
                    break;
                    
                    case $shout == (stristr($shout,'буду') !== false):
                   $a="".$CURUSER['username']." будешь, куда ты денешься";  
					$b="".$CURUSER['username']." ага я согласна.";           
                    break;
                   
                                                
                    
            //  case $shout == (strstr($shout,'вы') !== false);
                 //   $a="".$CURUSER['username']." давай на ты, я же как никак Бот Трекера :D";          
                //   $b="".$CURUSER['username']." Сударь, мы живем в 2009 году, можно на ты перейти, с вашего позволения";          
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
// вычитаем из конечного времени начальное
$time = $end_time - $start_time;
// запись
$date=get_date_time();

$time = substr($time, 0, 8);
$memory = round(memory_get_usage()/1024);

echo "<hr><b>$time</b>";
 */
 

?>