<?
require "include/bittorrent.php";
dbconn(true);

loggedinorreturn;
stdheadchat("Браузера");

if (get_user_class() < UC_ADMINISTRATOR) 
{
attacks_log('useragent'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

  echo "<table class=\"embedded\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">
  <tr><td align=center class=a>
  ".(htmlspecialchars($_GET['fme']) ?"<a href=useragent.php>Обратно</a>":"Файл находится в стадии разработки, данная бета версия только для администраторов.")."
  
  </td></tr></table>
  ";
echo("<script>
function doBlink() {
	var blink = document.all.tags(\"BLINK\")
	for (var i=0; i<blink.length; i++)
		blink[i].style.visibility = blink[i].style.visibility == \"\" ? \"hidden\" : \"\" 
}
function startBlink() {
	if (document.all)
		setInterval(\"doBlink()\",1600)
}
window.onload = startBlink;
function rnd(scale) {
var dd=new Date();      
return((Math.round(Math.abs(Math.sin(dd.getTime()))*10000000000)%scale)); 
}
</script>");

$get_found = htmlspecialchars_uni($_GET['fme']);

if (strlen($get_found)>15) {
unset($get_found);
unset($_GET['fme']);
}
if (!empty($get_found))
{
	$sql_add="WHERE agent LIKE '%".($get_found)."%'";
	$fun="?fme=".htmlspecialchars($_GET['fme'])."&";
}
else
$fun="?";

///LIKE '%X11%'

//sql_query("ALTER TABLE useragent ADD KEY crc32_id (id, crc32);");

$subres = sql_query("SELECT COUNT(*) FROM useragent $sql_add"); 
        $subrow = mysql_fetch_array($subres); 
        $count = $subrow[0]; 

if (empty($count) && !empty($get_found))
die("<br><table><td align=center class=a>
	<form method=\"get\" action=\"useragent.php\"><b>Поиск</b>: 
	<input type=\"text\" ".(htmlspecialchars($_GET['fme'])? "value=".htmlspecialchars($_GET['fme'])."":"")." name=\"fme\" size=\"25\" />	
	<input type=\"submit\" class=\"btn\" value=\"Искать\">
	</form>
	 </td></table><br><b>Данных по поиску:<br> ".htmlspecialchars($get_found)." <br> Ничего не найденно.</b>");

$limited = 40;

 list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "useragent.php$fun", array(lastpagedefault => 1));

       //print("<tr><td>"); 
         print($pagertop); 
      //   print("</td></tr>"); 
      
//$dt = sqlesc(time() - 320);
$dt = sqlesc(get_date_time(gmtime() - 300));
$agent=($num["agent"]);
$result = sql_query("SELECT id,agent,(SELECT id FROM sessions WHERE time>'$dt' and useragent=agent) AS agent_now FROM useragent $sql_add ORDER BY id $limit");

echo "<table cellpadding=4 align=center border=0>";
echo "<tr>";
echo "<td align=center class=colhead>id</td>";
echo "<td align=center class=colhead>Браузер / У кого был-есть</td>";

echo "</tr>";

$all_chislo=0;
while ($num = mysql_fetch_array($result))
{

$n1=$num["id"];
$n=$num["id"].",";
$nn=",".$num["id"].",";
$chislo=0;
$query = mysql_query("SELECT id,username,class FROM users WHERE idagent LIKE '$n' OR idagent LIKE '%$nn%' ORDER BY last_access DESC");

while ($qu = mysql_fetch_array($query))
{
$id=$qu["id"];
$username=$qu["username"];
$class=$qu["class"];


if ($who[$num["id"]])
$who[$num["id"]].=", ";
$who[$num["id"]].= "<a href=userdetails.php?id=$id>".get_user_class_color($class,$username)."</a>";
$chislo++;

}


if (!empty($get_found))
{
$num["agent"] = str_replace($get_found,"[b]".$get_found."[/b]",$num["agent"]);
}
if (!empty($get_found)) {
if (stristr($num["agent"],$get_found))
$a=format_comment($num["agent"]);
}
else
$a=htmlentities($num["agent"]);

//<i>Нет пользователей</i>
echo "<tr><td >".$num["id"]."</td>";
echo "<td>".$a." ".($num["agent_now"] ? ": <BLINK><b>[</b><font color=red><b>сейчас</b></font><b>]</b></BLINK>":"")."<br> ".($chislo==0 ? "":"<b>Всего $chislo</b>: ".$who[$num["id"]])."</td>";
echo "</tr>";
unset($who[$num["id"]]);
unset($num["agent_now"]);
}

echo "</table>";
print($pagerbottom);
print("<br><table><td align=center class=a>

	<form method=\"get\" action=\"useragent.php\"><b>Поиск</b>: 
	<input type=\"text\" ".(htmlspecialchars($_GET['fme'])? "value=".htmlspecialchars($_GET['fme'])."":"")." name=\"fme\" size=\"25\" />	
	<input type=\"submit\" class=\"btn\" value=\"Искать\">
	</form>
	

  </td></table>");
stdfootchat();
mysql_free_result($query);
mysql_free_result($result);
mysql_free_result($subres);
?>