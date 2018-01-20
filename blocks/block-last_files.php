<?
if (!defined('BLOCK_FILE')) {
Header("Location: ../index.php");
exit;
}

?>

<style type="text/css">
<!--
#tabs {
    text-align: left;
}
#tabs .tab {
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
#tabs.active {
    border-bottom: none;
    padding-bottom: 5px;
    background: #FAFAFA;
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
    padding: 7px;
}


.effect {
float: center;
text-align: center;
 border: 1px solid #ccc;
 -moz-border-radius: 3px; /*--CSS3 Rounded Corners--*/
 -webkit-border-radius: 3px; /*--CSS3 Rounded Corners--*/
 display: inline; /*--Gimp Fix aka IE6 Fix--*/
 FILTER: alpha(opacity=50); -moz-opacity: .50; opacity: .50;
}

.example {float:left; margin:20px; border-bottom:#ccc 1px solid; cursor:pointer}
.pics {
    height:  232px;  
    width:   232px;  
    padding: 0;  
    margin:  0;  
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

var loading = "<img src=\"pic/loading.gif\" alt=\"Загрузка..\" />";
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
            jQuery.post("block-last_files_jquery.php",{"user":user,"act":act},function (response) {
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
        if (width > 422)
            jQuery('#profile_right').width(width);
        else
        {
            jQuery('#profile_right').width("422");
            jQuery('#profile_container').width("686");
        }
    }
});

function changeview(id) {
var loading = "";
var id = id;
jQuery("#loading").html(loading);
$.get('/block-last_files_jquery.php',{'view':id },

function(response) {
$('#changeviewt').html(response);
jQuery("#loading").empty();
}, 'html');
}

</script>

<?
global $CURUSER;


       
$idcoo = (!empty($_COOKIE["view"]) ? "1":"0");

if($idcoo == 1)
$div = "<span title=\"Режим показа блока\" id=\"changeviewt\" sytle=\"border: 1px solid #cecece;padding: 5px 10px 5px 10px;background:#ededed;margin-right:5px;line-height: 23px;cursor: pointer;\"><b style=\"cursor: pointer;\" onClick=\"javascript:changeview('0');\">Список</b></span>\n";
else 
$div = "<span title=\"Режим показа блока\" id=\"changeviewt\" sytle=\"border: 1px solid #cecece;padding: 5px 10px 5px 10px;background:#ededed;margin-right:5px;line-height: 23px;cursor: pointer;\"><b style=\"cursor: pointer;\" onClick=\"javascript:changeview('1');\">Таблица</b></span>\n";


if ($CURUSER && $CURUSER["notifs"])
$blocktitle = "Последние <a title=\"Изменены категории просмотра по умолчанию в файле настроек (см файл my.php)\" class=\"altlink_white\" href=\"my.php#notif\"><u>выбранные</u></a> раздачи $div";
else
$blocktitle = "Последние раздачи, вид: $div ".($CURUSER ? ":: (<a title=\"Можно изменить меню показа всех категорий (по умолчанию) на выбранные категории в файле настроек (см файл my.php)\" class=\"altlink_white\" href=\"my.php#notif\">*</a>":"").")";

$blocktitle.= " :: <a class=\"altlink_white\" href=\"browseday.php\">Торренты за сегодня</a>";

$content.= ("<div id=\"tabs\">\n");
$content.= ("<span class=\"tab active\" id=\"info\">Все</span>\n");


$dt = sqlesc(get_date_time(gmtime() - 2*86400));
//$res = sql_query("SELECT id, name FROM categories WHERE (SELECT id FROM torrents WHERE `added` > $dt AND category=categories.id LIMIT 1) IS NOT NULL ORDER BY sort ASC LIMIT 8");
//while ($row = mysql_fetch_array($res)){

///////// cache
$cache=new MySQLCache("SELECT id, name FROM categories WHERE (SELECT id FROM torrents WHERE added > DATE_SUB(NOW(), INTERVAL 2 DAY) AND category=categories.id AND torrents.moderated='yes' LIMIT 1) IS NOT NULL ORDER BY sort ASC LIMIT 8", 3600,"block-last_files.txt"); // час 
while ($row=$cache->fetch_assoc()){

$name=$row["name"];
$id=$row["id"];


if ($CURUSER && $CURUSER["notifs"]){

if (strpos($CURUSER["notifs"], "[cat".$id."]") !==false)
$content.= ("<span title=\"Активность категорий за последние два дня [выбрана категория]\" class=\"tab\" id=\"".$id."\">".$name."</span>\n");

}
else 
$content.= ("<span title=\"Активность категорий за последние два дня\" class=\"tab\" id=\"".$id."\">".$name."</span>\n");
}


$content.= ("<span id=\"loading\"></span>\n");

$content.= ("<div id=\"body\" user=\"by 7Max7 for Pro Tesla TT (2010)\">\n");

/////////

/*$res = sql_query("SELECT t.times_completed,t.f_seeders,t.f_leechers,t.name,t.seeders,t.owner, t.webseed, t.leechers,t.image1,t.id,t.hits, t.views,t.moderatedby, t.moderated,users.username AS us,users.class AS cl,
 m.class AS classname, m.username AS classusername
FROM torrents AS t
LEFT JOIN users ON t.owner = users.id  
LEFT JOIN users AS m ON t.moderatedby = m.id 
WHERE t.banned = 'no' and t.moderated = 'yes' ORDER BY  t.added DESC LIMIT 0 , 8") or sqlerr(__FILE__, __LINE__);
*/

$cache=new MySQLCache("SELECT t.times_completed,t.f_seeders,t.f_leechers,t.name,t.seeders,t.owner, t.tags, t.leechers,t.image1,t.id,t.hits, t.views,t.moderatedby, t.moderated,users.username AS us,users.class AS cl,
 m.class AS classname, m.username AS classusername
FROM torrents AS t
LEFT JOIN users ON t.owner = users.id  
LEFT JOIN users AS m ON t.moderatedby = m.id 
WHERE t.banned = 'no' and t.moderated = 'yes' ORDER BY t.added DESC LIMIT 0 , 8", 600); // час 

//$cace=new MySQLCache($cache, 3*60);
//$num=$cache->fetch_row();

//$num = mysql_num_rows($res);

$num=1;

if ($num > 0) {


if (empty($idcoo)) {

    $content.="<table border=\"1\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"100%\"  class=\"main\" id=\"table\"> ";
    $nc=1;
    
 
    for ($i = 0; $i < $num; ++$i) {
    	
while ($row=$cache->fetch_assoc()){

	   if ($nc == 1) { $content.="<tr>"; }

       $content.="<td>";

$image1 = htmlentities($row["image1"]);

if(empty($image1))
$image1="default_torrent.png";  

$content.="<td align=\"center\" valign=\"top\" width=\"25%\">"; 
$content.="<div id=\"s1\">";

if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image1))
$content.="<a href=details.php?id=".$row["id"]."><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" src=\"".$image1."\" height=\"140\" width=\"140\" alt=\"".htmlspecialchars($row["name"])."\" /><br>".htmlspecialchars_uni($row["name"])."</a>";
else
$content.="<a href=details.php?id=".$row["id"]."><img class=effect onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect'\" src='thumbnail.php?image=".$image1."&for=block' height=\"140\" width=\"140\" alt=\"".htmlspecialchars($row["name"])."\" /><br>".htmlspecialchars_uni($row["name"])."</a>";


$content.="</div>";


if (!empty($row["webseed"])){
$row['seeders']=$row['seeders']+1;
}

$content.='</center><b><div align="center"><font color=red>Раздают: '. ($row['seeders']+$row['f_seeders']) .'  </font> <font color=green>Качают: '. ($row['leechers']+$row['f_leechers']).'</b></div></font>';

if ($row["owner"]==$row["moderatedby"]){

$content.= "<b>Залито и Одобрено:<br> ".($row["us"] ? "<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row["cl"],$row["us"])."</a>":"нет автора")."</b>\n";
}
 else
{
$content.="<b>Залил: ".($row['us'] ?"<a href=userdetails.php?id=" . $row["owner"] . ">".get_user_class_color($row['cl'],$row['us'])."</a>":"нет автора")."

</b><br>";
 
if ($row["moderated"] == "yes"){
$content.="<b>Одобрил:</b> <b><a href=\"userdetails.php?id=$row[moderatedby]\">". get_user_class_color($row["classname"], htmlspecialchars_uni($row["classusername"])) . "	</a></b><br>\n";} else {
$content.="<b>Одобрено:</b> <b>Нет</b><br>\n";
}
}

     
       
$content.= "</td>";
        ++$nc;
        if ($nc == 5) { //$nc=1; 
		$content.="</tr>"; }
      //  if ($nc == 9) { $nc=1; echo"</tr>"; }
    }
}

if ($nc==1)
$content.="<center>Нет раздач на этом трекере...</center>\n";

$content.="</tr></table>";



} else {


$content.="<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" class=\"main\"><tr>"; 

$dp=0;
    	
while ($row=$cache->fetch_assoc()){

if ($dp%2 == 0){
$clasto = "class = 'b'";
$clastf = "class = 'a'";
} else {
$clasto = "class = 'a'";
$clastf = "class = 'b'";
}

$content.="<tr>";
     

$content.="<td align=left ".$clasto."><a href=\"details.php?id=".$row["id"]."\"><b>".htmlspecialchars_uni($row["name"])."</b></a>";
		

if (!empty($row["tags"])) {
$tags[$row["id"]]="";
foreach(explode(",", $row["tags"]) as $tag) {
	
if (!empty($tags[$row["id"]]))
$tags[$row["id"]].=", ";

$tags[$row["id"]].= "<a style=\"font-weight:normal;\" href=\"browse.php?tag=".urlencode($tag)."&incldead=1\">".tolower($tag)."</a>";
}
$tags[$row["id"]]=$tags[$row["id"]];
}
else
$tags[$row["id"]]="не выбраны";

if (!empty($tags[$row["id"]]))
$content.="<br><b>Теги</b>: ".$tags[$row["id"]]." ".((!empty($tags[$row["id"]]) && strlen($tags[$row["id"]])>200) ? "&nbsp; ":"")."";//<br>

$content.="</td>"; 
        
$content.="<td align=center ".$clastf." width=\"10%\"><font color=red><b>". ($row['seeders']+$row['f_seeders']) ."</b></font> / <font color=green><b>". ($row['leechers']+$row['f_leechers'])."</b></font>";
$content.="</td></tr>";
++$dp;
}

$content.="</tr></table>";


}
}


/////////
$content.= ("</div>\n");
$content.= ("</div>\n");

?> 






