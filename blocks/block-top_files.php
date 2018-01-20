<?
if (!defined('BLOCK_FILE')) {
Header("Location: ../index.php");
exit;
}

/**
 * @author 7Max7
 * @copyright 2010
 */
 
 
$blocktitle = "Недельный Топ 8 раздач";
?>

<style type="text/css">
<!--

.effect2 {FILTER: alpha(opacity=50); -moz-opacity: .50; opacity: .50;}

#tabstwho {
    text-align: left;
}

#tabstwho{padding-top: 7px} 
#tabstwho span{position: relative;border-bottom: 1px solid #FAFAFA !important;top: -1px;-webkit-border-top-left-radius: 4px;-webkit-border-top-right-radius: 4px;-moz-border-radius-topleft: 4px;-moz-border-radius-topright: 4px;border-top-left-radius: 4px;border-top-right-radius: 4px;}
#tabstwho span:hover{background: #FAFAFA;}
.activey{color: #C60000;}

#tabstwho .tabstwho {
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
#tabstwho.activey {
    border-bottom: none;
    padding-bottom: 5px;
    background: #FAFAFA;
    cursor: default;
}
#tabstwho #bodyy {
    border: 1px solid #cecece;
    padding: 5px;
    margin-bottom: 10px;
    background: #FAFAFA;
}
#tabstwho .tabstwho_error {
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

.example {float:left; margin:20px; border-bottom:#ccc 1px solid; cursor:pointer}
.pics {
    height:  232px;  
    width:   232px;  
    padding: 0;  
    margin:  0;  
}
 
.pics img {
    padding: 15px;  
    border:  1px solid #ccc;  
    background-color: #eee;  
    width:  200px; 
    height: 200px; 
    top:  0; 
    left: 0 
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
    jQuery(".tabstwho").click ( function(){
        if(jQuery(this).hasClass("activey"))
            return;
        else
        {
            jQuery("#loading").html(loading);
            var user = jQuery("#bodyy").attr("user");
            var act = jQuery(this).attr("id");
            jQuery(this).toggleClass("activey");
            jQuery(this).siblings("span").removeClass("activey");
            jQuery.post("block_top_jquery.php",{"user":user,"act":act},function (response) {
                jQuery("#bodyy").empty();
                jQuery("#bodyy").append(response);
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
</script>

<?

$content.= "<div id=\"tabstwho\">\n";
$content.= "<span title=\"Активность раздачь за последнюю неделю по Топ просмотрам.\" class=\"tabstwho activey\" id=\"view\">Топ Просмотров</span>\n";

$content.= "<span title=\"Активность раздачь за последнюю неделю по Топ Мультитрекерности и скачиваний их.\" class=\"tabstwho\" id=\"intop\">Топ Внутренних</span>\n";

$content.= "<span title=\"Активность раздачь за последнюю неделю по Топ Обычным раздачам без Мультитрекерности.\" class=\"tabstwho\" id=\"outtop\">Топ Внешних</span>\n";

$content.= "<span title=\"Активность раздачь за последнюю неделю по Топ Взятию торрент файла.\"  class=\"tabstwho\" id=\"snat\">Топ Взят</span>\n";

$content.= "<span title=\"Активность раздачь за последнюю неделю по Топ Взятию торрент файла.\"  class=\"tabstwho\" id=\"mark\">Топ Оценок</span>\n";

$content.= "<span id=\"loading\"></span>\n";
$content.= "<div id=\"bodyy\" user=\"by 7Max7 for Pro Tesla TT (2010)\">\n";



/*
$res = sql_query("SELECT t.times_completed,t.f_seeders,t.f_leechers,t.name,t.seeders,t.owner, t.webseed, t.leechers,t.image1,t.id,t.hits, t.views,t.moderatedby, t.moderated,users.username AS us,users.class AS cl,
 m.class AS classname, m.username AS classusername
FROM torrents AS t
LEFT JOIN users ON t.owner = users.id
LEFT JOIN users AS m ON t.moderatedby = m.id
WHERE t.banned = 'no' and t.moderated = 'yes' and t.added > DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY t.views DESC LIMIT 0 , 8") or sqlerr(__FILE__, __LINE__);
*/

///////// cache
$cache=new MySQLCache("SELECT t.times_completed,t.f_seeders,t.f_leechers,t.name,t.seeders,t.owner, t.webseed, t.leechers,t.image1,t.id,t.hits, t.views,t.moderatedby, t.moderated,users.username AS us,users.class AS cl,
 m.class AS classname, m.username AS classusername
FROM torrents AS t
LEFT JOIN users ON t.owner = users.id
LEFT JOIN users AS m ON t.moderatedby = m.id
WHERE t.banned = 'no' and t.moderated = 'yes' and t.added > DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY t.views DESC LIMIT 0 , 8", 30*60,"block-top_files.txt"); // 30 минут



$num=1;
$numi=0;

if ($num > 0) {

$content.="<table border=\"1\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"100%\" class=\"main\" id=\"table\"> ";
$nc=1;
    
for ($i = 0; $i < $num; ++$i) {
	
//while ($row=mysql_fetch_array($res)){
while ($row=$cache->fetch_assoc()){
$num=1;

if ($nc == 1) { $content.="<tr>"; }
$content.="<td>";

$image1 = htmlentities($row["image1"]);
if(empty($image1))
$image1="default_torrent.png";  


$content.="<td align=\"center\" valign=\"top\" width=\"25%\">"; 

if (preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $image1))
$content.="<a href=details.php?id=".$row["id"]."><img class=\"effect2\" onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect2'\" src=\"".$image1."\" height=\"140\" width=\"140\"/><br>".format_comment($row["name"])."</a>";
else
$content.="<a href=details.php?id=".$row["id"]."><img class=\"effect2\" onmouseover=\"this.className='effect1'\" onmouseout=\"this.className='effect2'\" src=\"thumbnail.php?image=".$image1."&for=block\" height=\"140\" width=\"140\"/><br>".format_comment($row["name"])."</a>";


if (!empty($row["webseed"])){
$row['seeders']=$row['seeders']+1;
}

$content.='<b><div align="center"><font color=blue>Просмотров: '. round($row['views']) .'  </font><br><font color=red>Раздают: '. ($row['seeders']+$row['f_seeders']) .'  </font> <font color=green>Качают: '. ($row['leechers']+$row['f_leechers']).'</b></div></font>';

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
        if ($nc == 5) { $nc=1; $content.="</tr>"; }
    ++$numi;
	  //  if ($nc == 9) { $nc=1; echo"</tr>"; }
    }
}
$content.="</tr></table>";

}

if ($numi==0)
$content.="<center>Нет просмотров на сайте за последнюю неделю.</center>\n";

/////////
$content.= ("</div>\n");
$content.= ("</div>\n");

?>