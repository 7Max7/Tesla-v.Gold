<?php
require_once("include/bittorrent.php");

gzip();
dbconn(false);

/////////////////
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) == 'XMLHttpRequest') {

header("Content-Type: text/html; charset=" .$tracker_lang['language_charset']);

$id = (int)$_POST['id'];

if (empty($id))
die;

///$dt = sqlesc(time() - 300);
$url="/details.php?id=$id";
$res_s = sql_query("SELECT DISTINCT uid, username, class,ip FROM sessions WHERE time > ".sqlesc(get_date_time(gmtime() - 300))." and url='$url' ORDER BY time DESC") or sqlerr(__FILE__,__LINE__);
// or url LIKE '%$url_e%'
$lastid=0;
$title_who_s="";
while ($ar_r = mysql_fetch_assoc($res_s)) {
$username = $ar_r['username'];
$id_use = $ar_r['uid'];
if ($title_who_s)
$title_who_s.=", ";
$title_who_s.=($id_use && $username ? "<a href=\"userdetails.php?id=$id_use\">".get_user_class_color($ar_r["class"], $ar_r["username"]) . "</a>":$ar_r['ip']);
   	++$lastid;
}

if ($lastid<>0){
echo $title_who_s;
} else echo "Нет никого";

die;
}
/////////////////
parse_referer();

//loggedinorreturn();

/* /// для cleanup
$torrents = array();

$res = sql_query("SELECT torrent, seeder, COUNT(*) AS c FROM peers GROUP BY torrent, seeder") or sqlerr(__FILE__,__LINE__);
while ($row = mysql_fetch_assoc($res)) {
if ($row["seeder"] == "yes")
$key = "seeders";
else
$key = "leechers";
$torrents[$row["torrent"]][$key] = $row["c"];
}

$fields = explode(":", "leechers:seeders");
//	print_r($torrents);
$update = array();
foreach ($torrents as $id => $seeders) {
//echo "$id - leechers=$seeders[leechers] и seeders=$seeders[seeders]<br>";

$update[] = "leechers = " . (!empty($seeders["leechers"])? $seeders["leechers"]:"0");
$update[] = "seeders = " . (!empty($seeders["seeders"])? $seeders["seeders"]:"0");
		
sql_query("UPDATE torrents SET ".implode(", ", $update)." WHERE id=".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
unset($update);
}
*/ /// для cleanup



$id = (int) (isset($_GET["id"])? $_GET["id"]:0);

if (!is_valid_id($id) || empty($id))
stderr("Ошибка данных", "Нет торрент файла. Проверьте вводимые данные в id запросе.");

if (isset($_GET['lock_comments']) && get_user_class() >= UC_MODERATOR) {
   if (isset($_GET['lock_comments']) == 'yes')
   $mysq="yes";
   elseif (isset($_GET['lock_comments']) == 'no')  {
  	$mysq="no";
   }
    sql_query("UPDATE torrents SET comment_lock = ".sqlesc($mysq)." WHERE id = $id");
}
///IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating,



$res = sql_query("SELECT torrents.*,IF(torrents.numratings < 1, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS ratingsum, b.class AS classname, b.username AS classusername, categories.name AS cat_name, users.username, users.class, users.groups FROM torrents
LEFT JOIN categories ON torrents.category = categories.id 
LEFT JOIN users ON torrents.owner = users.id 
LEFT JOIN users AS b ON torrents.moderatedby = b.id
WHERE torrents.id = $id")  or sqlerr(__FILE__, __LINE__);

$row = mysql_fetch_array($res);


if (!empty($row["free_who"])) {

$res_g = sql_query("SELECT username AS free_username, class AS free_class,gender AS free_gender FROM users WHERE id = ".sqlesc($row["free_who"]))  or sqlerr(__FILE__, __LINE__);
$row_g = mysql_fetch_array($res_g);
$free_username=$row_g["free_username"];
$free_gender=$row_g["free_gender"];
$free_class=$row_g["free_class"];

}


$size_to_go=$row["size"];
$ratingsum=$row["ratingsum"];


if (checknewnorrent($id, $row["added"]) && $CURUSER) {
$COOKIE = $_COOKIE['markview'];
@setcookie("markview", ($COOKIE ? $COOKIE. "-" .$id : $id),0x7fffffff,"/");
}


$owned = $moderator = 0;
      if (get_user_class() >= UC_MODERATOR)
           $owned = $moderator = 1;
      else if ($CURUSER["id"] <> $row["owner"])
           $owned = 1;

//if ($_GET["page"])
//header("Location: $DEFAULTBASEURL/details.php?id=$id&page=".$_GET["page"]."#pagestart");

if (!$row || ($row["banned"] == "yes" && !$moderator)) {
if ($row["banned"] == "yes")
stderr("Доступ запрещен", "Торрент <b>".$row["name"]."</b> Забанен!");
else 
stderr("Ошибка данных", "Нет торрент файла с таким идентификатором. Возможно удален или перемещен.");
}
else {



/*
// ALTER TABLE `torrents` ADD `checkpeers` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `added`;

//// перекидываем из cleanup код под каждый торрент
$dt_multi = get_date_time(gmtime() - 600); // каждые 10 минут

if ($row["checkpeers"]<$dt_multi){

$torrents = array();
$res_cle = sql_query("SELECT seeder, COUNT(*) AS c FROM peers WHERE torrent=".sqlesc($id)." GROUP BY torrent, seeder") or sqlerr(__FILE__,__LINE__);
while ($row_cle = mysql_fetch_assoc($res_cle)) {

if ($row_cle["seeder"] == "yes")
$key = "seeders";
else
$key = "leechers";
$torrents[$id][$key] = $row_cle["c"];
}


$res_cle = sql_query("SELECT COUNT(*) AS c FROM comments WHERE id=".sqlesc($id)." GROUP BY torrent") or sqlerr(__FILE__,__LINE__);
while ($row_cle = mysql_fetch_assoc($res_cle)) {
$torrents[$id]["comments"] = $row_cle["c"];
}

$fields = explode(":", "comments:leechers:seeders");
$res_cle = sql_query("SELECT seeders, leechers, comments FROM torrents WHERE id=".sqlesc($id)) or sqlerr(__FILE__,__LINE__);

while ($row_cle = mysql_fetch_assoc($res_cle)) {
$torr = (isset($torrents[$id]) ? $torrents[$id]:""); 

foreach ($fields as $field) {
if (!isset($torr[$field]))
$torr[$field] = 0;
}

$update = array();
foreach ($fields as $field) {
$update[] = $field." = " . $torr[$field];
/// вносим сразу изменения в просмотр
if ($field=="leechers")
$row["leechers"]=$torr[$field];
elseif ($field=="seeders")
$row["seeders"]=$torr[$field];
/// вносим сразу изменения в просмотр
}

if (count($update)){

$update[] = "checkpeers=".sqlesc(get_date_time());
sql_query("UPDATE torrents SET " . implode(", ", $update) . " WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
}
}
//// перекидываем из cleanup код под каждый торрент
//print_r($update);
sql_query("UPDATE torrents SET seeders='0', leechers='0' WHERE checkpeers='0000-00-00 00:00:00' LIMIT 100") or sqlerr(__FILE__,__LINE__);
}
*/



if ($row["category"] == "4" || !$CURUSER){

$ca = array();
$sql1 = sql_query("SELECT torrent, filename FROM files WHERE torrent = ".sqlesc($id)." LIMIT 200") or sqlerr(__FILE__, __LINE__);

while ($res1 = mysql_fetch_assoc($sql1))
$ca[] = $res1["filename"];

$mycateyrogy = parse_arrray_cat($ca, $row["size"]);

if ($mycateyrogy<>false && $mycateyrogy<>$row["category"])
sql_query("UPDATE torrents SET category=".sqlesc($mycateyrogy)." WHERE id=".sqlesc($id)) or sqlerr(__FILE__,__LINE__);

//if ($CURUSER)
//@header("Location: $DEFAULTBASEURL/details.php?id=$id#newcat");
}







if ($CURUSER && $row["moderated"] == "yes")
sql_query("UPDATE torrents SET views = views + 1 WHERE id = ".sqlesc($id));

if (isset($_GET["tocomm"]))
header("Location: $DEFAULTBASEURL/details.php?id=$id&page=0#startcomments");


  	
	       if ($CURUSER)
           stdhead(htmlspecialchars_uni($row["name"]). " ".(!empty($row["cat_name"])? " :.: ".$row["cat_name"]."":""));
           else
           stdheadchat(htmlspecialchars_uni($row["name"]));
            
           if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
                $owned = 1;
           else
                $owned = 0;

           $spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

           $s=$row["name"];
if (isset($_GET["report"]) && $CURUSER)
stdmsg("Успешно", "Жалоба отправлена администрации");
elseif (isset($_GET["alreadyreport"]) && $CURUSER) {
stdmsg("Ошибка", "Вы уже отправляли жалобу на этот торрент");
}
elseif(isset($_GET["ownreport"]) && $CURUSER) {
stdmsg("Ошибка", "Вы не можете подать жалобу на собственную раздачу");
}

if(isset($CURUSER)) {

//слежение за комментариями
	
$resour= sql_query("SELECT COUNT(*) AS numc,(SELECT COUNT(*) AS numb FROM bookmarks WHERE userid=$CURUSER[id] and torrentid=$id LIMIT 1) AS numb FROM checkcomm WHERE userid = $CURUSER[id] AND checkid = $id AND torrent = 1 LIMIT 1
 ") or sqlerr(__FILE__, __LINE__);

//$resour= sql_query("SELECT b.id, c.checkid FROM checkcomm AS c LEFT JOIN bookmarks AS b ON c.userid = b.userid and c.checkid = b.torrentid WHERE c.userid = $CURUSER[id] AND c.checkid = $id AND c.torrent = 1") or sqlerr(__FILE__, __LINE__);
$arr_res = mysql_fetch_array($resour);

$checkcomm=$arr_res["numc"];
$bookcomm=$arr_res["numb"];
//print"$checkcomm и $bookcomm";
?>

<script type="text/javascript">
function bookmark(id, type, page) {
var loading = "";
var id = id; var type = type;
jQuery("#loading").html(loading);
$.post('/bookmark.php',{'id':id , 'type':type , 'page':page},

function(response) {
$('#bookmark_'+id).html(response);
jQuery("#loading").empty();
}, 'html');
}

function checmark(id, type, page,twopage) {
var loading = "";
var id = id; var type = type;
jQuery("#loading").html(loading);
$.post('/bookmark.php',{'id':id , 'type':type , 'page':page, 'twopage':twopage},

function(response) {
$('#checmark_'+id).html(response);
jQuery("#loading").empty();
}, 'html');
}
</script>
<?

 
if (empty($checkcomm))
$check = " <span id=\"checmark_".$row['id']."\"><a class=\"altlink_white\" href=\"#\" onclick=\"checmark('$row[id]', 'add' , 'check', 'details');\"><b>Включить слежение</b></a></span> ";
else
$check = " <span id=\"checmark_".$row['id']."\"><a class=\"altlink_white\" href=\"#\" onclick=\"checmark('$row[id]', 'del' , 'check', 'details');\"><b>Отключить слежение</b></a></span> ";

 
if (empty($bookcomm))
$books = " <span id=\"bookmark_".$row['id']."\"><a class=\"altlink_white\" href=\"#\" onclick=\"bookmark('$row[id]', 'add' , 'details');\"><b>Добавить в закладки</b></a></span>";
else
$books = " <span id=\"bookmark_".$row['id']."\"><a class=\"altlink_white\" href=\"#\" onclick=\"bookmark('$row[id]', 'del' , 'details');\"><b>Убрать с закладок</b></a></span>";

}
}

 
if (!$CURUSER){
?>

<script type="text/javascript">
$(document).ready(function() {

    $("span.spoiler").hide();

     $('<a class="reveal"> <form ><input type=button value="Активировать Панель Входа" style="height: 25px; width: 100%"></form></a> ').insertBefore('.spoiler');

    $("a.reveal").click(function(){
        $(this).parents("p").children("span.spoiler").fadeIn(2500);
        $(this).parents("p").children("a.reveal").fadeOut(600);
    });

});
</script>

<?

print"<style type=\"text/css\">
<!--
input.pass { background: url(pic/contact/pass.gif) no-repeat; background-color: #fff;  background-position: 0 50%; color: #000; padding-left: 18px; }
input.login { background: url(pic/contact/login.gif) no-repeat;  background-color: #fff; background-position: 0 50%; color: #000; padding-left: 18px; }
-->
</style>";

$content = "<form name=mainForm method=\"post\" action=\"takelogin.php\">
<b>".$tracker_lang['username']."</b>: <input id=\"nickname\" type=\"text\" size=18 name=\"username\" class=\"login\"/>
<b>".$tracker_lang['password']."</b>: <input id=\"password\" type=\"password\" size=18 name=\"password\" class=\"pass\" /><input type=\"hidden\" name=\"returnto\" value=\"details.php?id=" . $row["id"]."\" />
<input type=\"submit\" class=\"btn\" name=doSend value=\"Пустите меня!\">
</form>";
}
//echo "<link rel=\"stylesheet\" href=\"./js/daGallery.css\" type=\"text/css\" media=\"screen\"/>"; 

echo("<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\" class=\"main\">\n");
echo("<tr><td class=\"colhead\" colspan=\"2\">

<div style=\"float: left; width: auto;\">:: Детали торрента </div>
".($CURUSER && !empty($check) ? "<div align=\"right\">".($check."::".$books)."</div>":"")." 
</td></tr>");// $bookmarks
	   
$url = "edit.php?id=" . $row["id"];

$name7 = htmlspecialchars_uni($row['name']);
///$name7 = preg_replace("/\[([0-9_-]+(.*?))\]/is", "<b>[</b>\\1]", $name7);


if ($row["stopped"] == "yes" && get_user_class()< UC_SYSOP){
$subres = sql_query("SELECT id FROM snatched WHERE startdat<".sqlesc($row["stop_time"])." and torrent='$id' and userid=".sqlesc($CURUSER["id"])) or sqlerr(__FILE__,__LINE__);///completedat<>'0000-00-00 00:00:00'
$snatch = mysql_fetch_array($subres);
$snatched=$snatch["id"];
}

if ( (get_user_class() < UC_MODERATOR) && ($row["moderated"] == "no") && ($CURUSER["id"] <> $row["owner"])) {
$s = "<b><span title='Данный торрент должен проверить модератор, после появится возможность его скачать'>".$row["name"]."</span></b>";
}
elseif ($row["stopped"] == "yes" && empty($snatched) && get_user_class()< UC_SYSOP) {
$s = "<b><span title='Данный файл приостановлен, время остановки ".$row["stop_time"]."'>".$row["name"]."</span></b>";
} else {
$s = "

".(($CURUSER || (!empty($announce_net) && $row["multitracker"]=="yes" && !$CURUSER))? "<a class=\"index\" href=\"download.php?id=$id\"><b><span title='Нажмите сюда чтобы скачать файл'>".$row["name"]."<span></b></a> 
".(get_user_class()< UC_MODERATOR ? "<a href=\"download.php?id=$id\"><b><span style=\"color: red;\" title='Нажмите сюда чтобы скачать файл'>Скачать</span></b></a>":"")."

":"<b>".$name7."</b>")."";
}

//".((strpos(htmlentities(getenv("HTTP_USER_AGENT")), 'bot'))!== false ? "": "<a href=\"login.php?returnto=details.php?id=$id\"><span style=\"color: red; padding: 3px; font-weight: bold;\" title=\"Чтобы скачать данный файл, Авторизуйтесь на сайте\">Чтобы скачать -> Авторизуйтесь на сайте!</span></a>")."")."


if ($row["free"] == "yes") {
$golden_torrent = "<img style=\"border:none\" alt=\"Торрент Золото\" title=\"Торрент Золотко\" src=\"pic/freedownload.gif\">";

$golden_torrent_text = "<tr><td align=\"center\" width=\"99%\" colspan=\"2\" style=\"font-weight: bold;\" class=\"row\">Данная раздача является свободной — учитывается только количество отданного, количество скачанного не учитывается.</td></tr>"; 
}

if ($row["moderated"] == "no" && get_user_class() <= UC_MODERATOR && $CURUSER){
$moderated_torrent_text = "<tr><td align=\"center\" width=\"99%\" colspan=\"2\" style=\"padding: 15px; font-weight: bold;\" class=\"row\">Данная раздача еще не проверена (не одобрена) администрацией, пожалуйста подождите проверки данного файла. Пока не возможно раздавать (сидировать) торрент, статус в utorrent программе будет - Красный.</td></tr>"; 
}


//// заметки jquery
if (!empty($bookcomm) && $CURUSER){
?>

<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">

$(document).ready(function () {
 
 var id=<?=$id;?>;
 
    function slideout() {
        setTimeout(function () {
            $("#response").slideUp("slow", function () {});
        },
        5000);
    }

    $(".inlineEdit").bind("click", updateText);

    var OrigText, NewText;

    $(".save").live("click", function () {

        $("#loading").fadeIn('slow');

        NewText = $(this).siblings("form").children(".edit").val();
      //  var id = $(this).parent().attr("id");
        var data = 'id=' + id + '&text=' + NewText;

        $.post("bookupdate_tags.php", data, function (response) {
            $("#response").html(response);
            $("#response").slideDown('slow');
            slideout();
            $("#loading").fadeOut('slow');

        });

        $(this).parent().html(NewText).removeClass("selected").bind("click", updateText);

    });

    $(".revert").live("click", function () {
        $(this).parent().html(OrigText).removeClass("selected").bind("click", updateText);
    });

    function updateText() {
        $('span').removeClass("inlineEdit");
        OrigText = $(this).html();
        $(this).addClass("selected").html('Данный торрент у вас в закладках, если вы хотите добавить свои заметки к нему, можно их вписать тут:<br><form ><textarea class="edit">' + OrigText + '</textarea></form> <a title="Сохранить изменения" href="#" class="save"><img src="pic/add.gif" border="0"/></a> выбери действие <a href="#" title="Отменить изменения" class="revert"><img src="pic/delete.gif" border="0"/></a>').unbind('click', updateText);
    }
});
</script>
<style>
.edit {
	width: 100%;
		margin:2px;
}
span {
	width: 100%;
}
span:hover {
	cursor:pointer;
}
span.selected:hover {
	background-image:none;
}
span.selected {
	padding: 10px;
	width: 100%;
}
form {
	width: 100%;
}
.save, .btnCancel {
	margin:0px 0px 0 5px;
}
#response {
	display:none;
	padding:10px;
	background-color:#9F9;
}
#loading {
	display:none;
}
</style>
<?

/**
ALTER TABLE `bookmarks` ADD `mytags` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `torrentid`;
*/

$datamy = sql_query("SELECT mytags FROM bookmarks WHERE torrentid = ".sqlesc($id)." AND userid=".sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);
$mtags = mysql_fetch_assoc($datamy);
$mytags=$mtags["mytags"];

if (empty($mytags))
$viewtags="У вас не записаны теги к этому торренту, пожалуйста кликните сюда.";

if (!empty($mytags)){
$viewtags=htmlspecialchars(strip_tags($mytags));
}

echo "<tr><td align=\"center\" width=\"99%\" colspan=\"2\" class=\"row\">
<div id=\"response\"></div>
<span style=\"padding: 15px; font-weight: bold;\" class=\"inlineEdit\" id=\"1\">".$viewtags."</span>
</td></tr>";


}
//// заметки jquery

if ($row["multitracker"] == "yes" &&  ($row["f_seeders"] + $row["f_leechers"]) < 2) {
echo "<tr><td align=\"center\" width=\"99%\" colspan=\"2\" style=\"font-weight: bold;\" class=\"b\">Данная раздача является мультитрекерной (<a title=\"Прочитать статью о мультитрекерности\" rel=\"nofollow\" href=\"redir.php?url=http://ru.wikipedia.org/wiki/%D0%9C%D1%83%D0%BB%D1%8C%D1%82%D0%B8%D1%82%D1%80%D0%B5%D0%BA%D0%B5%D1%80\">?</a>) — учтите, количество раздающих может не совпадать с количеством пиров на сайте (также зависит от настроек клиента uTorrent, настройки -> Включить DHT, Обмен пирами и Поиск лок-х пиров). <br>Пример: 0 раздающих, N качающих = N пиров - где N колеблится от 1 до бесконечности, при  0 раздающих и 1 качающих - правильно считать раздачу <a title=\"(не работающая ссылка или раздача которая скачивается не полностью и программа не работает на компьютере скачавшего)\">мертвой</a>.</td></tr>"; 
}


if ($row["stopped"] == "yes" && $row["stop_time"]<>"0000-00-00 00:00:00"){
$stopped_torrent_text = "<tr><td align=\"center\" width=\"99%\" colspan=\"2\" style=\"padding: 15px; font-weight: bold;\" class=\"row\">Данная раздача приостановленна, повторно скачать и помочь в сидировании могут те, которые взяли торрент файл <span style=\"color:red\">до <b>".$row["stop_time"]."</b></span> указанного числа. ".(get_user_class() == UC_SYSOP ?"<br>[<span style=\"color:blue\">исключение для боссов</span>]":"<br>")." Извините за неудобства.</td></tr>"; 
}

//////////////////// проверка битых файлов если число файлов = 0 ////////////////////
if (empty($row["numfiles"]) || empty($row["size"])){
require_once("include/benc.php");

$ifilename = ROOT_PATH."torrents/".$row["id"].".torrent";

if (file_exists($ifilename)){

$dict = bdec_file($ifilename, 1024000);
list($info) = dict_check($dict, "info");
list($dname, $plen, $pieces) = @dict_check_t($info, "name(string):piece length(integer):pieces(string)");

$filelist = array();
$totallen = @dict_get_t($info, "length", "integer");
if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
} else {
	$flist = @dict_get_t($info, "files", "list");
	$totallen = 0;
	
	if (count($flist)){
	
	foreach ($flist as $sf) {
		list($ll, $ff) = @dict_check_t($sf, "length(integer):path(list)");
		$totallen += $ll;
		$ffa = array();
		foreach ($ff as $ffe) {
			$ffa[] = $ffe["value"];
		}
		$ffe = implode("/", $ffa);
		$filelist[] = array($ffe, $ll);
	}
	}
}
//print_r($filelist);

$size = 0;
sql_query("DELETE FROM files WHERE torrent = ".$row["id"]);

foreach ($filelist as $file) {
$file[0] = utf8_to_win($file[0]);
$size = $size+$file[1];

sql_query("INSERT INTO files (torrent, filename, size) VALUES (".$row["id"].", ".sqlesc($file[0]).",".sqlesc($file[1]).")");
}

sql_query("UPDATE torrents SET numfiles=".sqlesc(count($filelist)).", size=".sqlesc($size)." WHERE id=".sqlesc($row["id"])) or sqlerr(__FILE__,__LINE__);

}

}
//////////////////// проверка битых файлов если число файлов = 0 ////////////////////


if ($row["banned"] == "yes" && $CURUSER) {
print "<tr><td align=\"center\" width=\"99%\" colspan=\"2\" style=\"padding: 15px; font-weight: bold;\" class=\"error\">Торрент Забанен по Причине: ".htmlspecialchars($row["banned_reason"])."</td></tr>";
}


if (!empty($row["viponly"])) {
	
$day_re=display_date_time($row["viponly"]);

$now = time();
if ($row["viponly"]<=$now){
//sql_query("UPDATE torrents SET viponly='' WHERE id=".sqlesc($id)."") or sqlerr(__FILE__,__LINE__);

sql_query("UPDATE torrents SET viponly ='' WHERE viponly<=".sqlesc(time())."")or sqlerr(__FILE__,__LINE__); 
/// выполняем общий запрос
}
///#F5F5F5
print "<tr><td align=\"center\" width=\"99%\" colspan=\"2\" style=\"color:#000000; padding: 15px; font-weight: bold;background-color:#F5DDDD;  border: 1px #F5F5F5 solid;\" class=\"row\">Данная раздача доступна только для VIP пользователей, у вас будет возможность скачать данный файл не позже <span style=\"color:green\"><b>".$day_re."</b></span> указанного числа.</td></tr>";
}

	$cat=$row["category"];
    $cat_user=$CURUSER["catedit"];


if ((get_user_class() <= UC_UPLOADER) && ($row["moderated"] == "yes")
||  ($cat_user<>"" && !stristr($cat_user, "[cat$cat]") && get_user_class() == UC_MODERATOR) && $CURUSER["id"] <> $row["owner"]
	)
	{
    $edit_link="<b>[</b>".$tracker_lang['edit']."<b>]</b>";	
    }
    else
	{
    $edit_link="<a title=\"Кликай не жмись!\" href=\"$url\"><b>[</b>".$tracker_lang['edit']."<b>]</b></a>";	
    }
    

if ($CURUSER["id"] == $row["owner"])
$CURUSER["downloadpos"] = "yes";

if ($CURUSER["downloadpos"] <> "no"){


if(!empty($row["viponly"]) AND (get_user_class() <> UC_VIP AND get_user_class() < UC_MODERATOR)) {

tr("Скачать", "Эта раздача только для VIP пользователей.");

}
else
{
 if ($owned)
 $s.= (isset($golden_torrent)?$golden_torrent:"").$spacer.$edit_link;
 
 tr ("Название", $s, 1, 1, "10%");
}
 
 /* if (empty($CURUSER) && (strpos(htmlentities(getenv("HTTP_USER_AGENT")), 'bot'))!== true)
 $s.="<br><p><span class=\"spoiler\"><noscript><b>Пожалуйста включите показ скриптов</b></noscript>".$content."</p>";
 */
}
else
tr("Скачать", "Вам была отключенна функция скачивать торренты.");


if (!empty($row["webseed"]) && get_user_class() >= UC_MODERATOR) {
$ori_web=strip_tags($row["webseed"]);
$site_webs = parse_url($row["webseed"], PHP_URL_HOST);

tr("Внешний файл", "<a title=\"Это webseed раздающий, Прямая ссылка для скачивания.\" href=\"".$ori_web."\">$site_webs</a>",1);
}

print((isset($stopped_torrent_text)?$stopped_torrent_text:"").(isset($moderated_torrent_text)?$moderated_torrent_text:"").(isset($golden_torrent_text)?$golden_torrent_text:""));
	
           function hex_esc($matches) {
                return sprintf("%02x", ord($matches[0]));
           }

if ($CURUSER) {
//tr($tracker_lang['info_hash'], $row["info_hash"]);
// tr("Хэш релиза", preg_replace_callback('/./s', "hex_esc", hash_pad($row["info_hash"])));
/// взято выше с пре 6 v 3. преобразует в nex формат

tr("Магнет-ссылка", "<a href=\"magnet:?xt=urn:btih:".$row['info_hash']."&dn=".$row['name']."&tr=".$announce_urls[0]."?passkey=".$CURUSER['passkey']."\">".$row['info_hash']."</a>",1);
  }
  
  

	if ((get_user_class() < UC_MODERATOR) && ($row["moderated"] == "no")) {
		    print("<tr><td align=\"right\"><b>Проверен</b></td>");
            print("<td align=\"left\"><b>Нет</b></td>\n");
			}
       
	      if ((get_user_class() < UC_MODERATOR) && $row["moderated"] == "yes" && $CURUSER){
          
		  print("<tr><td align=\"right\"><b>Проверен</b></td><td align=\"left\"><b>".($row["classusername"] ? "<a href=\"userdetails.php?id=$row[moderatedby]\">".get_user_class_color($row["classname"], $row["classusername"])."</a>" : "id [$row[moderatedby]]")."</b> в <b>$row[moderatordate]</b> ".(isset($sysop)?$sysop:"")."</td></tr>\n");
}



     		if ((get_user_class() > UC_MODERATOR) && $row["moderated"] == "yes")(
		$sysop = " <a href=checkdelete.php?id=$id><b>[<font color=\"red\">Удалить</font>]</b></a>");
		
		if (get_user_class() >= UC_MODERATOR) {
            print("<tr><td align=\"right\"><b>Проверен</b></td>");

            if ($row["moderated"] == "no")

            {
           // 	if ($CURUSER["id"] == $row["owner"])
          //  print("<td align=\"left\"><b>это твой торрент</b>$sysop</td>\n");

          //  else

            print("<td align=\"left\"><a href=check.php?id=$id><b>Одобрить</b></a>
			<i>[Необходимо одобрить торрент иначе пользователи не смогут его скачать]</i></td>\n");}

            else
                print("<td align=\"left\"><b>".($row["classusername"] ? "<a href=\"userdetails.php?id=$row[moderatedby]\">".get_user_class_color($row["classname"], $row["classusername"])."</a>" : "id [$row[moderatedby]]")."</b> в <b>$row[moderatordate]</b> $sysop</td></tr>\n");

        }
                ///// мод тегов /////
                $tags="";
                foreach(explode(",", $row["tags"]) as $tag) {
                $tags .= "<a style=\"font-weight:normal;\" href=\"browse.php?tag=".urlencode($tag)."&incldead=1\">".tolower($tag)."</a>, ";

if ($row["category"]=="100000"){ /// если категория мультимедиа
//$tag_up=toupper($tag);

$row["descr"]=str_replace($tag, "[url=$DEFAULTBASEURL/browse.php?tag=".urlencode($tag)."&incldead=1]".$tag."[/url]", $row["descr"]);
//$row["descr"]=str_ireplace("$tag_up", "[url=$DEFAULTBASEURL/browse.php?tag=".urlencode($tag)."&incldead=1]".$tag."[/url]", $row["descr"]);
}

}

if ($tags)
$tags = substr($tags, 0, -2);

if ($row["tags"])
tr("Тэги", $tags, 1);
else
tr("Тэги", "[".$tracker_lang['no_choose']."]");



if (!empty($row["image1"])) {
$image0 = htmlentities($row["image1"]);

if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image0)) {

$img1 = "<span class=\"daGallery\"><a title=\"".htmlspecialchars_uni($row["name"])."\" rel=\"group2\" href=\"".$image0."\"><img border='0' src=\"".$image0."\"/></a></span>";

} else {
	
if (!file_exists("torrents/images/".$image0)) {
sql_query("UPDATE torrents SET image1='' WHERE id = '$id'") or sqlerr(__FILE__, __LINE__);
} else {
$img1 = "<span class=\"daGallery\"><a rel=\"group2\" href=\"torrents/images/".$image0."\"><img title=\"".htmlspecialchars_uni($row["name"])."\" border='0' src='thumbnail.php?image=".$image0."&for=details'/></a></span>";
}


}

	


if (!empty($img1))
tr($tracker_lang['images'], $img1, 1);
}



if (get_user_class() >= UC_MODERATOR)
$view_size="<br>[".strlen($row["descr"])."]";


if (!empty($row["descr"]))
tr($tracker_lang['description'].(isset($view_size)?$view_size:""), format_comment($row["descr"]), 1, 1);
 
 ///echo "<tr><td width=\"99%\" colspan=\"2\">".format_comment($row["descr"])."</td></tr>";
 
if (!empty($row["picture1"]))
$picture1 = "<a rel=\"group2\" href=".htmlentities($row["picture1"])." title=\"".htmlspecialchars_uni($row["name"])."\" ><img  border=\"\" src=".htmlentities($row["picture1"])." width=\"150\"/></a>"; 
/////////////////
if (!empty($row["picture2"]))
$picture2 = "<a rel=\"group2\" href=".htmlentities($row["picture2"])." title=\"".htmlspecialchars_uni($row["name"])."\" ><img border=\"\" src=".htmlentities($row["picture2"])." width=\"150\"/></a>"; 
/////////////////
if (!empty($row["picture3"]))
$picture3 = "<a rel=\"group2\" href=".htmlentities($row["picture3"])." title=\"".htmlspecialchars_uni($row["name"])."\" ><img border=\"\" src=".htmlentities($row["picture3"])." width=\"150\"/></a>"; 
/////////////////
if (!empty($row["picture4"]))
$picture4 = "<a rel=\"group2\" href=".htmlentities($row["picture4"])." title=\"".htmlspecialchars_uni($row["name"])."\" ><img border=\"\" src=".htmlentities($row["picture4"])." width=\"150\"/></a>"; 


if (!empty($row["picture1"]) || !empty($row["picture2"]) || !empty($row["picture3"]) || !empty($row["picture4"])) {
tr("Скриншоты", "<span class=\"daGallery\">".(isset($picture1)?$picture1:"").(isset($picture2)?$picture2:"").(isset($picture3)?$picture3:"").(isset($picture4)?$picture4:"")."</span>", 1, 1);
}


echo "<script type=\"text/javascript\" src=\"/js/jquery.js\"></script>
<script type=\"text/javascript\" src=\"/js/daGallery.js\"></script>
<script type=\"text/javascript\">
<!--//
initDAGal();
//-->
</script>";




$torrent_com = htmlspecialchars($row["torrent_com"]);
if (get_user_class() >= UC_MODERATOR && !empty($torrent_com)){

   if (strlen($torrent_com)>180)
	$stl_size=round(strlen($torrent_com)/180)+4;
	else
	$stl_size=3;

	print("<tr><td align=right><b>История торрента <br>[".strlen($torrent_com)."]</b></td><td colspan=2 align=left><textarea cols=75 rows=".$stl_size." readonly>$torrent_com</textarea></td></tr>\n");
}


echo '<script>
function adjective_ax() {
jQuery.post("block-details_ajax.php" , {adject:"yes",tid:"'.$id.'"}, function(response) {
jQuery("#adjective_ax").html(response);
}, "html");
setTimeout("adjective_ax();", 180000);
}
adjective_ax();
</script>';

tr("Похожие файлы", "<div id=\"adjective_ax\">Загрузка похожих файлов...</div>");



  /*
    $csto=array(" - ","%","&","?","\"","'","*","$","^","#","@","!",">","<","(","!)","=","+","/","|");
    $name = strlen($row["name"])>18?(substr($row["name"],0,18).""):$row["name"];
    $name = preg_replace("#\([0-9]{4}\)#is","",preg_replace("#\[(.+?)\]#is","",$name ));
    $name = str_replace($csto, " ", preg_replace("# ([a-zA-Z0-9]{1,5})Rip#is","",$name));
    $name = preg_replace("#\([0-9]{1,4}.+[0-9]{1,4}\)#is","",preg_replace("#by (.+?)$#is","",$name));
    $name = trim(sqlwildcardesc(htmlspecialchars(preg_replace("#[\.,\\/\?\(\)\!\`\~]#is","",$name))));
    $name = str_replace(" ","%",$name);

    $sql = sql_query("SELECT name, id, size FROM torrents WHERE name LIKE ('%".$name."%') AND id <>'".$row["id"]."' ORDER BY added DESC LIMIT 8") or sqlerr(__FILE__,__LINE__);
    $num_p=0;
    $ono="";
    while($t = mysql_fetch_array($sql)) {

    if (!empty($ono))
    $ono.="<br>";
    
    $ono.="<a href=\"details.php?id=".$t['id']."\">".htmlspecialchars_uni($t['name'])."</a> [".mksize($t["size"])."]";
// ".(get_user_class() >= UC_MODERATOR ? "<a href=edit.php?id=".$t['id']."><b>[</b>Редактировать<b>]</b></a>":"")."
++$num_p;
}

 if ($num_p<>0)
 tr("Похожие", $ono);

*/
          
		   	if (isset($row["cat_name"]))
                tr("Категория", $row["cat_name"]."");
           else
                tr("Категория", "(".$tracker_lang['no_choose'].")");
                

		   if ($row["visible"] == "no")
           tr($tracker_lang['visible'], "<b>".$tracker_lang['no']."</b> (".$tracker_lang['dead'].")", 1);
            
if ($row["multitracker"]=="no")
tr("Приватный", "".($row["private"] == "yes" ? "Да <b>[</b>Отключены функции: DHT, Обмен пирами, Поиск локальных пиров<b>]</b>" : "Нет <b>[</b>Включены функции: DHT, Обмен пирами, Поиск локальных пиров<b>]</b>")."", 1);

if ($moderator)
tr($tracker_lang['banned'], ($row["banned"] == 'no' ? $tracker_lang['no'] : $tracker_lang['yes']) );

tr($tracker_lang['seeder'], $tracker_lang['seeder_last_seen']." ".get_elapsed_time(sql_timestamp_to_unix_timestamp($row["last_action"])) . "".$tracker_lang['ago']);


tr($tracker_lang['size'],mksize($row["size"]) . " (" . number_format($row["size"]) . " ".$tracker_lang['bytes'].")");


if (get_user_class() >= UC_UPLOADER) {
///GROUP BY torrent
$data = sql_query("SELECT sum(uploaded) AS datau, sum(downloaded) AS datad FROM snatched WHERE torrent  = '$id' LIMIT 2000") or sqlerr(__FILE__, __LINE__);
 $a = mysql_fetch_assoc($data);

 $datad = "<b>Скачанно</b>: ".mksize($a["datad"])." "; 
 $datau = " <b>Залито</b>: ".mksize($a["datau"]); 
 
if (!empty($a["datau"]) || !empty($a["datad"])) {
tr("Трафик", $datad.$datau,1);  
}
}
        
        
///////////////
        


if ($row["seeders"] + $row["leechers"]>=1 && $CURUSER){

$suql = sql_query("SELECT downloadoffset, uploadoffset, UNIX_TIMESTAMP(last_action) AS la, UNIX_TIMESTAMP(prev_action) AS pa FROM peers WHERE torrent = '$id'") or sqlerr(__FILE__, __LINE__);

while ($e = mysql_fetch_array($suql)) {

if (isset($down_off)){
$secs = max(10, ($e["la"]) - $e["pa"]);
$down_off=($e["downloadoffset"] / $secs)+$down_off;
} else {
$secs = max(10, ($e["la"]) - $e["pa"]);
$down_off=($e["downloadoffset"] / $secs);
}

if (isset($up_off)){
$secs = max(10, ($e["la"]) - $e["pa"]);
$up_off=($e["uploadoffset"] / $secs)+$up_off;
} else {
$secs = max(10, ($e["la"]) - $e["pa"]);
$up_off=($e["uploadoffset"] / $secs);
}

///echo mksize($down_off) . "/s";
}
tr("Общая Скорость", "<b>Скачивания</b>: ".mksize($down_off)."/сек <b>Отдачи</b>: ".mksize($up_off)."/сек", 1);
}


///////////////




if ($CURUSER) {
 
if ($row["ratingsum"]<>0){
$row_rating=round($row["ratingsum"]/$row["numratings"],1);
}
else
$row_rating=0;


///$xres = sql_query("SELECT rating, added, (SELECT COUNT(*) FROM ratings WHERE torrent = $id) AS gol FROM ratings WHERE torrent = '".$id."' AND user = " . $CURUSER["id"])  or sqlerr(__FILE__, __LINE__);

if (get_user_class() >= UC_MODERATOR){
?>
<script type="text/javascript">
function getraticho(tid) { var det = document.getElementById('raticho_'+tid);
if(!det.innerHTML) {
     var ajax = new tbdev_ajax();
     ajax.onShow ('');
     var varsString = "";
     ajax.requestFile = "block-details_ajax.php";
     ajax.setVar("tid", tid);
      ajax.setVar("raticho", "yes");
     ajax.method = 'POST';
     ajax.element = 'raticho_'+tid;
     ajax.sendAJAX(varsString); } else  det.innerHTML = '';}
</script>
<?
}


$xres = sql_query("SELECT COUNT(*) AS gol,r.rating, r.added
FROM ratings
LEFT JOIN ratings AS r ON r.torrent = '".$id."' AND r.user = ".$CURUSER["id"]."
WHERE ratings.torrent = '".$id."'");

$xrow = @mysql_fetch_array($xres);

$rating="<table border=\"0\" id=\"tesla_tto_rate\">
<tr>
<td>
".pic_rating_b(10,$ratingsum)."
</td>
<td class=\"a\" style=\"padding-top: 5px; padding-bottom: 5px; border: 0px;\">".

(!empty($xrow["added"])? "<b>Общая оценка</b>: ".round($ratingsum,1)." <b>Голосовавших</b>:  ".round($xrow["gol"])." <br><b>Ваша оценка</b> ".($xrow["rating"])." <b>Время</b>: " . $xrow["added"] . "":"<b>Общая оценка</b>: ".round($ratingsum,1)." <b>Голосовавших</b>:  ".round($xrow["gol"])."<br> <small>Для оценки торрента, вам необходимо скачать его, после зайти <a href=\"rating.php\">сюда</a>.</small>").(get_user_class() >= UC_MODERATOR && !empty($xrow["gol"]) ? "<br><small><a title=\"Список от последнего к первому\" style=\"cursor: pointer;\" onclick=\"getraticho('" .$id. "');\">[Показать / Скрыть <b>Кто голосовал</b>]</a></small>":"")."
</td></tr></table>";


tr($tracker_lang['rating'], $rating.(get_user_class() >= UC_MODERATOR && !empty($xrow["gol"])? "<span id=\"raticho_" .$id. "\"></span>":""), 1);
}

tr($tracker_lang['added'], normaltime($row["added"], true));
tr($tracker_lang['views'], $row["views"]);
tr($tracker_lang['hits'], $row["hits"]);
tr($tracker_lang['snatched'], $row["times_completed"] . " ".$tracker_lang['times']);


// группы

if ($row["groups"]<>"0"){
$id_gro=(int) $row["groups"];
$groupe_cache=new MySQLCache("SELECT image,name FROM groups WHERE id=".sqlesc($id_gro)."", 86400,"details_groups-".$id_gro.".txt"); // кеш один день
$name_gre=$groupe_cache->fetch_assoc();

if (!empty($name_gre["image"]))
print("<tr><td align=right><b>Релиз группы</b></td><td>
<a href=\"browse.php?search=&stype=0&gr=".$id_gro."&incldead=1&cat=0\"><img title=\"Поиск всех релизов залитые группой - ".htmlspecialchars($name_gre["name"])."\" src='pic/groups/".$name_gre["image"]."'></a>
</td></tr>\n");
// группы
//echo $row["groups"];
}


$uprow = (isset($row["username"]) ? "<a href=userdetails.php?id=" . $row["owner"] . ">" .get_user_class_color($row["class"], $row["username"]. "</a>") : "<i>Анонимный пользователь или удален [".$row["owner"]."]</i>");
/*
           if ($owned)
                $uprow .= " $spacer<$editlink><b>[".$tracker_lang['edit']."]</b></a>";
*/

if ($CURUSER["id"] <> $row["owner"]){
tr($tracker_lang['uploaded'], $uprow.'&nbsp;
'.($CURUSER && !empty($row["owner"]) ? '<a href="simpaty.php?action=add&amp;good&amp;targetid=' . $row["owner"] . '&amp;type=torrent' . $id . '&amp;returnto=' . urlencode($_SERVER["REQUEST_URI"]) . '" title="'.$tracker_lang['respect'].'"><img src="pic/thum_good.gif" border="0" alt="'.$tracker_lang['respect'].'" title="'.$tracker_lang['respect'].'" /></a>&nbsp;&nbsp;<a href="simpaty.php?action=add&amp;bad&amp;targetid='.$row["owner"].'&amp;type=torrent' . $id . '&amp;returnto=' . urlencode($_SERVER["REQUEST_URI"]) . '" title="'.$tracker_lang['antirespect'].'"><img src="pic/thum_bad.gif" border="0" alt="'.$tracker_lang['antirespect'].'" title="'.$tracker_lang['antirespect'].'" /></a>':'').'', 1);
}



?>
<script type="text/javascript">
function getlist(tid) { var det = document.getElementById('list_'+tid);
if(!det.innerHTML) {
     var ajax = new tbdev_ajax();
     ajax.onShow ('');
     var varsString = "";
     ajax.requestFile = "block-details_ajax.php";
     ajax.setVar("tid", tid);
      ajax.setVar("list", "yes");
     ajax.method = 'POST';
     ajax.element = 'list_'+tid;
     ajax.sendAJAX(varsString); } else  det.innerHTML = '';}
</script>
<div id="loading-layer" style="display:none;font-family: Verdana;font-size: 11px;width:180px;height:45px;background:#FFF;padding:10px;text-align:center;border:1px solid #000">
     <div style="font-weight:bold" id="loading-layer-text">Загрузка данных. Пожалуйста, подождите...</div><br />
     <img title="Загрузка" src="pic/loading.gif" border="0" />
</div>
<?
tr ($tracker_lang['files'],"".$row["numfiles"] . " ".$tracker_lang['files_l']." ".($CURUSER ? "<a style=\"cursor: pointer;\" onclick=\"getlist('" .$id. "');\">[Показать / Скрыть список]</a><div id=\"list_" .$id. "\"></div>":"")."",1);

         	
          //  if (!empty($row["webseed"])){
            //	$row["seeders"]=$row["seeders"]+1;
            	
          //      $site_Web = parse_url($row["webseed"], PHP_URL_HOST);
          //  	
          //  	$link_vieweb="[<a title=\"Постоянный webseed раздающий -> $site_Web\">Вебсид</a>]";
          //  } else
         //   unset($link_vieweb);
            
if ($CURUSER){
?>
<script type="text/javascript">
function getseed(tid) { 
var det = document.getElementById('seed_'+tid);
if(!det.innerHTML) {
var ajax = new tbdev_ajax();
ajax.onShow ('');
var varsString = "";
ajax.requestFile = "block-details_ajax.php";
ajax.setVar("tid", tid);
ajax.setVar("seeders", "yes");
ajax.method = 'POST';
ajax.element = 'seed_'+tid;
ajax.sendAJAX(varsString); 
} else  det.innerHTML = '';
}
     
function getmt(tid) {
var det = document.getElementById('mt_'+tid);
if(!det.innerHTML) {
var ajax = new tbdev_ajax();
ajax.onShow ('');
var varsString = "";
ajax.requestFile = "block-details_ajax.php";
ajax.setVar("tid", tid);
ajax.setVar("multi", "yes");
ajax.method = 'POST';
ajax.element = 'mt_'+tid;
ajax.sendAJAX(varsString); 
} else  det.innerHTML = '';
}

</script>

<?
}

/*
if (($row["f_seeders"] + $row["f_leechers"])==0 || ($row["seeders"] + $row["leechers"])<>0) {
tr($tracker_lang['downloading'], "<b><font color=\"".linkcolor($row["seeders"])."\">".($row["seeders"])."</font></b> ".$tracker_lang['seeders_l'].", <b><font color=\"".linkcolor($row["leechers"])."\">".($row["leechers"])."</font></b> ".$tracker_lang['leechers_l']." = <b>" . ($row["seeders"] + $row["leechers"]) . "</b> ".$tracker_lang['peers_l']." ".($row["seeders"] + $row["leechers"]<>0 ? "
".($CURUSER ? "&nbsp;<a style=\"cursor: pointer;\" onclick=\"getseed('" .$id. "');\">[Показать / Скрыть список]</a><div id=\"seed_" .$id. "\"></div>":""):(isset($link_vieweb)?$link_vieweb:""))."", 1);
///.(isset($link_vieweb)?$link_vieweb:"")
}
*/

echo '<script>
function multion() {
jQuery.post("block-details_ajax.php" , {list:"on",tid:"'.$id.'"}, function(response) {
jQuery("#multion").html(response);
}, "html");
setTimeout("multion();", 120000);
}
multion();
</script>';

tr($tracker_lang['thistracker'], "<div id=\"multion\">Загрузка / Обновление данных о сидах...</div>");


/*
$dt_multi = get_date_time(gmtime() - $multihours*3600); // умножаем количество часов на секунды
//// перепроверить
if ($row["multi_time"]<$dt_multi && $row["multitracker"]=="yes" && !empty($multihours)){
global $announce_urls;
require_once(ROOT_PATH.'include/benc.php');

 $tracker_cache = array(); 
    $f_leechers = 0; 
    $f_seeders = 0; 
    $announce_list=$announce_urls;
    foreach($announce_list as $announce) 
    {
        $response = get_remote_peers($announce, $row['info_hash'],true); 
        if($response['state']=='ok'){
          $tracker_cache[] = $response['tracker'].':'.($response['leechers'] ? $response['leechers'] : 0).':'.($response['seeders'] ? $response['seeders'] : 0).':'.($response['downloaded'] ? $response['downloaded'] : 0); 
            // $f_leechers += $response['leechers']; 
            // $f_seeders += $response['seeders']; 
            if ($f_leechers < $response['leechers'])
            $f_leechers = $response['leechers'];
            
            if ($f_seeders < $response['seeders'])
            $f_seeders = $response['seeders']; 
        }
        else 
            $tracker_cache[] = $response['tracker'].':false'; 
    }
    $fpeers = $f_seeders + $f_leechers;
    $tracker_cache = implode("\n",$tracker_cache);
    $updatef = array();
    $updatef[] = "f_trackers = ".sqlesc($tracker_cache);
    $updatef[] = "f_leechers = ".sqlesc($f_leechers);
    $updatef[] = "f_seeders = ".sqlesc($f_seeders);
    $updatef[] = "multi_time = ".sqlesc(get_date_time());
    $updatef[] = "visible = ".sqlesc(!empty($fpeers) ? 'yes':'no');
    sql_query("UPDATE torrents SET " . implode(",", $updatef) . " WHERE id = $id");
    //implode(",", $updatef)

$row["f_seeders"]=$f_seeders;
$row["f_leechers"]=$f_leechers;
$row["multi_time"]=$now;
$row["f_trackers"]=$tracker_cache;
}

///list($tracker,$checka)=explode(":",$trackersss);///.":".($checka=="false"?"ошибка":"успешно")
$view_trackers="<fieldset><legend>Внешние аннонсы за ".($row["multi_time"])."</legend>".str_replace("\n", "<br>", $row["f_trackers"])."
<hr><a style=\"cursor: pointer;\" onclick=\"getmt('" .$id. "');\">[Обновить список / Закрыть]</a><div id=\"mt_" .$id. "\"></div>
</fieldset>";


tr($tracker_lang['multitracker'], ($row["multitracker"]=="yes" ? "<b><font color=\"".linkcolor($row["f_seeders"])."\">".$row["f_seeders"]."</font></b> ".$tracker_lang['seeders_l'].", <b><font color=\"".linkcolor($row["f_leechers"])."\">".$row["f_leechers"]."</font></b> ".$tracker_lang['leechers_l']." = <b>" . ($row["f_seeders"] + $row["f_leechers"]) . "</b> ".$tracker_lang['peers_l']."<br>".$view_trackers:"Отключен")."", 1);
///<b>[</b>автообновление раз в 12 часов<b>]</b>
*/


echo '<script>
function multioff() {
jQuery.post("block-details_ajax.php" , {list:"off",tid:"'.$id.'"}, function(response) {
jQuery("#multioff").html(response);
}, "html");
setTimeout("multioff();", 360000);
}
multioff();
</script>';

tr($tracker_lang['multitracker'], "<div id=\"multioff\">Загрузка / Обновление мультитрекер данных...</div>");



       
if (!empty($row["times_completed"]) && $CURUSER){
?>
<script type="text/javascript">
function getcho(tid) { var det = document.getElementById('chosna_'+tid);
if(!det.innerHTML) {
     var ajax = new tbdev_ajax();
     ajax.onShow ('');
     var varsString = "";
     ajax.requestFile = "block-details_ajax.php";
     ajax.setVar("tid", tid);
      ajax.setVar("cho", "yes");
     ajax.method = 'POST';
     ajax.element = 'chosna_'+tid;
     ajax.sendAJAX(varsString); } else  det.innerHTML = '';}
</script>

<?			 	
                
				
if ($row["seeders"] == 0 || ($row["leechers"] / $row["seeders"] >= 2))

if ($row["leechers"] + $row["seeders"]<=2)
$reseed_button = "<form action=\"takereseed.php\"><input type=\"hidden\" name=\"torrent\" value=\"$id\" /><input type=\"submit\" class=\"btn\" value=\"Позвать скачавших\" /></form>";

tr("Скачавшие", "&nbsp;<a style=\"cursor: pointer;\"  onclick=\"getcho('" .$id. "');\">[Показать / Скрыть список]</a><div id=\"chosna_" .$id. "\"></div>".$reseed_button, 1);
				
}
				
if ($CURUSER["class"]>=UC_MODERATOR && !empty($row["hits"])){

?>
<script type="text/javascript">
function getsnatched(tid) { var det = document.getElementById('snatlist_'+tid);
if(!det.innerHTML) {
var ajax = new tbdev_ajax();
ajax.onShow ('');
var varsString = "";
ajax.requestFile = "block-details_ajax.php";
ajax.setVar("tid", tid);
ajax.setVar("snatlist", "yes");
ajax.method = 'POST';
ajax.element = 'snatlist_'+tid;
ajax.sendAJAX(varsString); 
} else  det.innerHTML = '';}
</script>
<?

tr("Кто взял", " &nbsp;<a style=\"cursor: pointer;\"  onclick=\"getsnatched('" .$id. "');\">[Показать / Скрыть список]</a><div id=\"snatlist_" .$id. "\"></div>", 1);
}

if ($CURUSER){
?>
<script type="text/javascript">
function getabout(tid) { var det = document.getElementById('abot_'+tid);
if(!det.innerHTML) {
var ajax = new tbdev_ajax();
ajax.onShow ('');
var varsString = "";
ajax.requestFile = "block-details_ajax.php";
ajax.setVar("tid", tid);
ajax.setVar("multi", "no");
ajax.method = 'POST';
ajax.element = 'abot_'+tid;
ajax.sendAJAX(varsString); 
} else  det.innerHTML = '';}
</script>

<?


tr("О торренте", "&nbsp;<a style=\"cursor: pointer;\" onclick=\"getabout('" .$id. "');\">[Показать данные о торренте]</a><div id=\"abot_" .$id. "\"></div>", 1);
//<a href=\"torrent_info.php?id=$id\">".$tracker_lang['show_data']."</a>
}


//пожаловаться	
if (($CURUSER["id"] <> $row["owner"]) && get_user_class() < UC_MODERATOR && $CURUSER){
$torrentid = (int) $_GET["id"];
                    $report_sql = sql_query("SELECT userid FROM report WHERE torrentid = $torrentid");
                    $report_row = mysql_fetch_assoc($report_sql);
                    if ($CURUSER["id"] <> $row["owner"] AND $report_row["userid"] <> $CURUSER["id"])
                    tr("Пожаловаться", "<form method=\"post\" action=\"report.php?id=".$row['id']."\">&nbsp;<input name=motive cols=40 value=\"Ваша Причина\">&nbsp;<input type=\"submit\" value=\"Отправить\" /><input type=\"hidden\" name=\"torrentid\" value=\"$torrentid\"></form>", 1);  
}

//пожаловаться	   
		
		

			   
///позолотить раздачу
if ($row["free"] == 'no' && $CURUSER) {
$procent = 10; //сколько процентов из размера торрента (в мб) стоит озолачивание (в бонусах) 
$needbonustot = $row["size"] / (1024*1024); 
$needbonuspr = $needbonustot*$procent/100; 
$needbonus = round($needbonuspr, 2); 

/*if($CURUSER[bonus] >= $needbonus) {
    $getfree = "<font color=\"green\">Вы можете <img src=pic/freedownload.gif> раздачу. Нужно бонусов:</font> <b>$needbonus</b> <input type=\"button\" value=\"Позолотить\" onclick=\"location='get_free.php?id=$id'\">"; 
    } 
    else {
    $getfree = "<font color=\"red\">У вас не хватает бонусов для <img src=pic/freedownload.gif> Нужно бонусов:</font> <b>$needbonus</b>"; 
    }
print("<tr><td class=\"rowhead\">Позолотить</td><td align=\"left\">$getfree</td></tr>\n"); 
} 
*/

if (get_user_class() <= UC_MODERATOR  &&  ($CURUSER["bonus"] >= $needbonus)){ 
print("<tr><td align=\"right\"><b>Позолотить</b></td><td align=\"left\"><font color=\"green\">Вы можете <img src=pic/freedownload.gif> раздачу. Нужно бонусов:</font> <b>$needbonus</b> <input  class=\"btn\" type=\"button\" value=\"Позолотить\" onclick=\"location='get_free.php?id=$id'\"></td></tr>\n");    
}

}

if ($row["free_who"] <> '0' && $row["free"] == 'yes') {

//$qwho_free = sql_query("SELECT users.username, users.class, users.gender FROM users WHERE id = ".sqlesc($row[free_who])."") or sqlerr(__FILE__,__LINE__); 
//$who_name = mysql_fetch_assoc($qwho_free); 

//if(mysql_num_rows($qwho_free)){
	

	
//	die($who_name["free_gender"]);
if($free_gender == '2') {
$g = "а"; 
}
else {
$g = ""; 
}

if ($free_username)
$who = "<a href=\"userdetails.php?id=$row[free_who]\">" .get_user_class_color($free_class, $free_username). "</a>"; 
else
$who = "<i>[неизвестен]</i>"; 

}else{
$g = ""; 

///sql_query("UPDATE torrents SET free_who = '0' WHERE id = ".sqlesc($id))or sqlerr(__FILE__, __LINE__);
}

if ($row["free_who"] <> '0' && $row["free"] == 'yes'){
print("<tr><td align=right><b>Позолотить</b></td><td align=\"left\"><font color=\"orange\"><b>Торрент уже <img src=pic/freedownload.gif></b> Позолотил".$g.":</font> ".$who."</td></tr>\n"); 

}
///позолотить раздачу					   

if ($CURUSER){

$torrentid = (int) $_GET["id"];

$thanked_sql = sql_query("SELECT thanks.userid, users.username, users.class FROM thanks INNER JOIN users ON thanks.userid = users.id WHERE thanks.torrentid = $torrentid");
$count = mysql_num_rows($thanked_sql);

if ($count == 0) {
     $thanksby = $tracker_lang['none_yet'];
} else {

     //$thanked_sql = sql_query("SELECT thanks.userid, users.username FROM thanks INNER JOIN users ON thanks.userid = users.id WHERE thanks.torrentid = $torrentid");
     $num_s=0;
     $thanksby="";
     while ($thanked_row = mysql_fetch_assoc($thanked_sql)) {
          if ($thanked_row["userid"] == $CURUSER["id"])
               $can_not_thanks = true;
          $userid = $thanked_row["userid"];
          $username = $thanked_row["username"];
          $class = $thanked_row["class"];
          $thanksby .= "<a href=\"userdetails.php?id=$userid\">".get_user_class_color($class, $username)."</a>, ";
          $num_s++;
     }
     if ($thanksby)
          $thanksby = substr($thanksby, 0, -2);
}
if ($row["owner"] == $CURUSER["id"])
$can_not_thanks = true;
$thanksby = "<div id=\"ajax\">
".(isset($can_not_thanks)==true? "".$thanksby."":"<form action=\"thanks.php\" method=\"post\">
<input type=\"submit\" class=\"btn\" name=\"submit\" onclick=\"send(); return false;\" value=\"".$tracker_lang['thanks']."\"".(isset($can_not_thanks) == true ? " disabled" : "").">
<input type=\"hidden\" name=\"touid\" value=\"$row[owner]\">
<input type=\"hidden\" name=\"torrentid\" value=\"$torrentid\">".$thanksby."
</form>")."

</div>";
?>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript">
function send() {
     var ajax = new tbdev_ajax();
     ajax.onShow ('');
     var varsString = "";
     ajax.requestFile = "thanks.php";
     ajax.setVar("touid", <?=$row["owner"];?>);
     ajax.setVar("torrentid", <?=$torrentid;?>);
     ajax.setVar("ajax", "yes");
     ajax.method = 'POST';
     ajax.element = 'ajax';
     ajax.sendAJAX(varsString);
}
</script>

<?

tr($tracker_lang['said_thanks'].": ".(isset($num_s)?$num_s:"")."",$thanksby,1);

}

/*
$dt = sqlesc(time() - 180);
$url="/details.php?id=$torrentid";
$url_e="/edit.php?id=$torrentid";
$res_s = sql_query("SELECT DISTINCT uid, username, class FROM sessions WHERE uid<>-1 and time > $dt and url LIKE '$url' ORDER BY time DESC") or sqlerr(__FILE__,__LINE__);
// or url LIKE '%$url_e%'
$lastid=0;
while ($ar_r = mysql_fetch_assoc($res_s)) {

    $username = $ar_r['username'];
    $id_use = $ar_r['uid'];
if ($title_who_s)
$title_who_s.=", ";

   	$title_who_s.= "<a href=\"userdetails.php?id=$id_use\">".get_user_class_color($ar_r["class"], $ar_r["username"]) . "</a>";
   	$lastid++;

}
     if ($lastid<>0){
print("<tr><td align=\"right\"><b>Просматривают сейчас: $lastid</b></td><td align=\"left\">$title_who_s</td></tr>\n");    
     }
     */

/////////////////////
?>

<script>
function details_online(id) {
jQuery.post("details.php",{"id":id}, function(response) {
		jQuery("#details_online").html(response);
	}, "html");
setTimeout("details_online('<?=$id;?>');", 60000);
}
details_online('<?=$id;?>');
</script>
<?

echo"
<tr><td align=\"right\"><b>Просматривают это сейчас: </b></td><td align=\"left\">
<span align=\"center\" id=\"details_online\">Загрузка кто смотрит данный торрент</span>
</td></tr>";

///////////////////// 
     





print("</table></p>\n");

     //   }
		
	/*	else {
                stdhead($tracker_lang['comments_for']." \"" . $row["name"] . "\"");
                print("<h1>".$tracker_lang['comments_for']." <a href=details.php?id=$id>" . $row["name"] . "</a></h1>\n");
        }
*/

/*
// вперед назад
$pre_query = sql_query("SELECT MAX(id) AS preid FROM torrents WHERE id < $id") or sqlerr(__FILE__, __LINE__); 
    $pre = mysql_fetch_array($pre_query); 
$next_query = sql_query("SELECT MIN(id) AS nextid FROM torrents WHERE id > $id") or sqlerr(__FILE__, __LINE__); 
    $next = mysql_fetch_array($next_query); 
    print("<b>-†- Страничка Торрента -†-</b><br>".(isset($pre["preid"]) ? "<a href='details.php?id=".$pre["preid"]."'><b>« Предыдущая -</b></a>" : "<span style=\"color:#AAAAAA;font-weight:bold\"><b></b></span>").
	"†"
	.(isset($next["nextid"]) ? "<a href='details.php?id=".$next["nextid"]."'><b>- Следующая »</b></a>" : "<span style=\"color:#AAAAAA;font-weight:bold\"><b></b></span>")."<br><br>"); 
    // вперед назад
*/

// вперед назад

echo "<noindex><p align=\"center\"><b>-†- <a title=\"Случайный торрент\" rel=\"nofollow\" href=\"random.php?id=$id&option=random\"> &#191;?</a> -†-</b><br>
<b><a title=\"Предыдущий торрент\" rel=\"nofollow\" href=\"random.php?id=$id&option=prev\">« Предыдущая -</a></b>
<b>† страничка †</b>
<b><a title=\"Следущий торрент\" rel=\"nofollow\" href=\"random.php?id=$id&option=next\">- Следующая »</a></b></p></noindex><br>";



if ($CURUSER["hidecomment"]<>"yes" && $CURUSER) {
        print("<p><a name=\"startcomments\"></a></p>\n");

        $subres = mysql_query("SELECT COUNT(*) FROM comments WHERE torrent = $id");
        $subrow = mysql_fetch_array($subres);
        $count = $subrow[0];

if ($CURUSER["postsperpage"]=="0")
{  $limited = 15; } else {$limited = (int) $CURUSER["postsperpage"];}

if (!$count) {

  print("<table style=\"margin-top: 2px;\"  class=\"main\" cellpadding=\"5\" width=\"100%\">");
  print("<tr><td class=a align=\"left\" colspan=\"2\">");
  print("<div style=\"float: left; width: auto;\" align=\"left\"><a name=\"#pagestart\"></a> :: Список комментариев</div>");

  if ($row["comment_lock"] == 'no')
  {
  	if($CURUSER["commentpos"] == 'yes') {
  print("<div align=\"right\"><a href=#comments>Добавить комментарий</a></div>");
  }
  print("</td></tr><tr><td align=\"center\">");
   
  print("Комментариев нет. 
  ".($CURUSER["commentpos"] == 'yes' ? "<br>".$CURUSER['username'].", Желаешь <a href=#comments>Добавить?</a>":"")."
  ");
  print("</td></tr></table><br>");


  print("<table class=\"main\" style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
  print("<tr><td class=colhead align=\"left\" colspan=\"2\"> <a name=comments>&nbsp;</a><b>Без комментариев</b></td></tr>");}
  print("<tr><td align=\"center\" >");
  

if (get_user_class() >= UC_MODERATOR)  
print "<br><font size=\"-2\"><a class=altlink href=?id=$id&lock_comments=".($row['comment_lock'] == 'no' ? "yes>" : "no>раз")."блокировать комментарии</a></font>";
 
if ($row['comment_lock'] == 'no') {

if ($CURUSER["commentpos"] == 'yes'){
 
  print("<form name=comment method=\"post\" action=\"comment.php?action=add\">"); 
  print("<center><table border=\"0\"><tr><td class=\"clear\">"); 
print("<div align=\"center\">". textbbcode("comment","text","", 1) ."</div>"); 
print("</td></tr></table></center>"); 
  print("</td></tr><tr><td  align=\"center\" colspan=\"2\">"); 
  print("<input type=\"hidden\" name=\"tid\" value=\"$row[id]\"/>"); 
  
  	 if (get_user_class() == UC_SYSOP) {
	  print("<div align=\"center\"><b>Отправитель:&nbsp;&nbsp;</b>
<b>
".get_user_class_color($CURUSER['class'], $CURUSER['username'])."</b>
<input name=\"sender\" type=\"radio\" value=\"self\" checked>&nbsp;&nbsp;
<font color=gray>[<b>System</b>]</font>
<input name=\"sender\" type=\"radio\" value=\"system\"><br>");
}

  print("<input type=\"submit\" class=btn value=\"Разместить комментарий\" />"); 
  print("</td></tr></form>"); 
}
else
print("<div align=\"center\">Вам запрещенно создавать, редактировать, удалять комментарии</div>"); 

  print("</table>"); 
if ($CURUSER["commentpos"] == 'yes')
  {
	$commentbar = "<p align=center><a class=index href=comment.php?action=add&amp;tid=$id>Добавить коментарий</a></p>\n";
	}
}
else {
  print("<center><table border=\"0\"><tr><td>"); 
  print("<div align=\"center\"><b>Для этого торрента комментарии заблокированы администрацией</b></div>"); 
  print("</td></tr></table></center>"); 
  print("</td></tr><tr><td  align=\"center\" colspan=\"2\">"); 
  print("</td></tr></table>"); 
}


        }
        else {
        	
                list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "details.php?id=$id&", array("lastpagedefault" => ceil($count / $limited)) );
// 0.001029   стало >  0.000921

                $subres = sql_query("SELECT c.id, c.ip, c.text, c.user, c.added, c.editedby, c.editedat, u.avatar, u.warned, u.username, u.enabled, u.title, u.class, u.signature,u.signatrue, u.donor, u.support,u.hiderating, u.supportfor, u.downloaded, u.uploaded, u.last_access, e.username AS editedbyname, e.class AS classbyname FROM comments AS c LEFT JOIN users AS u ON c.user = u.id LEFT JOIN users AS e ON c.editedby = e.id WHERE torrent = " .
                  "$id ORDER BY c.id $limit") or sqlerr(__FILE__, __LINE__);
                $allrows = array();
                while ($subrow = mysql_fetch_array($subres))
                        $allrows[] = $subrow;

         print("<table class=main cellspacing=\"0\" cellPadding=\"5\" width=\"100%\" >");
         print("<tr><td class=\"colhead\" align=\"center\" >");
         print("<div style=\"float: left; width: auto;\" align=\"left\"> :: Список комментариев</div>");
       
	     if ($CURUSER["commentpos"] == 'yes'){
         print("<div align=\"right\"><a href=#comments class=altlink_white>Добавить комментарий</a></div>");
         }
         print("</td></tr>");

         print("<tr><td>");
         print($pagertop);
         print("</td></tr>");
         print("<tr><td>");
                 commenttable($allrows);
         print("</td></tr>");
         print("<tr><td>");
         print($pagerbottom);
         print("</td></tr>");
         print("</table>");



  

  if ($CURUSER["commentpos"] == 'yes'){
  print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
  print("<tr><td class=colhead align=\"center\" colspan=\"2\"><a name=comments></a><b><center>.::: Добавить комментарий к торренту :::.</b></center></td></tr>");
  print("<tr><td width=\"100%\" align=\"center\" >");
  //print("Ваше имя: ");
  //print("".$CURUSER['username']."<p>");
  print("<form name=comment method=\"post\" action=\"comment.php?action=add\">");
  print("<center><table border=\"0\"><tr><td class=\"clear\">");
  print("<div align=\"center\">". textbbcode("comment","text","", 1) ."</div>");
  print("</td></tr></table></center>");
  print("</td></tr><tr><td  align=\"center\" colspan=\"2\">");
  print("<input type=\"hidden\" name=\"tid\" value=\"$id\"/>");
  
  	 if (get_user_class() == UC_SYSOP) {
	  print("<div align=\"center\"><b>Отправитель:&nbsp;&nbsp;</b>
<b>
".get_user_class_color($CURUSER['class'], $CURUSER['username'])."</b>
<input name=\"sender\" type=\"radio\" value=\"self\" checked>&nbsp;&nbsp;

<font color=gray>[<b>System</b>]</font>
<input name=\"sender\" type=\"radio\" value=\"system\"><br>");

}
  
  print("<input type=\"submit\" class=btn value=\"Разместить комментарий\" />");
  
	
	
	
  print("</td></tr></form></table>");
 }
        }

}
elseif($CURUSER["hidecomment"]=="yes" && $CURUSER)
{
  print("<br><center><table border=\"0\"><tr><td>"); 
  print("<div align=\"center\"><b>Вы предпочли скрыть все комментарии в торрентах</b><br>Для изменения опции, пройдите <a title='Меню настроек аккаута' href='my.php'>сюда</a>.</div>"); 
  print("</td></tr></table></center>"); 

}



/*
if ($_GET["all"]){
$res = sql_query("SELECT torrent,rating FROM ratings");
while ($row = mysql_fetch_array($res)){
sql_query("UPDATE ratings SET rating = ".sqlesc($row["rating"]*2)." WHERE torrent = ".sqlesc($row["torrent"]));
}}
*/



/*
/// было 
14 (queries) - 89.00% (php) - 11.00% (0.0102 => sql) - 1917 КБ (use memory) - 0.000830
/// стало
14 (queries) - 87.02% (php) - 12.98% (0.0105 => sql) - 1917 КБ (use memory) - 0.001205
*/
  if ($CURUSER)
  stdfoot();
    else
   stdfootchat();


?>