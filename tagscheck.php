<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

if (get_user_class() < UC_ADMINISTRATOR)
 {
attacks_log('tagscheck'); stderr("Ошибочка", "Только для Боссов..."); die();
}

stdheadchat("Повторяющиеся Теги раздач");


$deltagid = (isset($_GET['deltagid']) ? (int)$_GET['deltagid']:"");
if (!empty($deltagid)){
	
   $ros = sql_query("SELECT name FROM tags WHERE id='".$deltagid."' LIMIT 1") or sqlerr(__FILE__, __LINE__);
   $num2 = mysql_fetch_assoc($ros);
	$num3=$num2["name"];
	
$query = "DELETE FROM tags WHERE id=" .sqlesc($deltagid);
$sql = sql_query($query);
begin_frame("Удаление", true);
echo("Тег <b>$num3</b> успешно удален!");
echo "<script>setTimeout('document.location.href=\"". $_SERVER['PHP_SELF'] . "\"', 3000);</script>";
end_frame();
stdfoot();
die();
}




$coun = sql_query("SELECT count(*) FROM tags") or sqlerr(__FILE__, __LINE__);
$coun1 = mysql_fetch_row($coun);
$un = $coun1[0];

begin_frame("Всего Тегов $un из них Повторяющиеся Теги:");
begin_table();

if (get_user_class() > UC_ADMINISTRATOR)
{
$res = sql_query("SELECT count(*) AS dupl, name FROM tags GROUP BY name ORDER BY dupl DESC, category") or sqlerr(__FILE__, __LINE__);


  print("
 <tr align=center >
<td class=a width=10%>ID</td>
<td class=a width=10%>Счетчик</td>
<td class=a width=60%>Название</td>
<td class=a width=50%>Категория</td>
 </tr>\n");
 $uc = 0;
  while($ras = mysql_fetch_assoc($res)) {
        if ($ras["dupl"] <= 1)
          break;
          
          if (!empty($ip))
          $ip=$ip;
          else
          $ip="";
          
        if ($ip <> $ras['name']) {
          $ros = sql_query("SELECT category, howmuch, category, id,
          (SELECT name FROM categories WHERE id=tags.category) AS catname
		  
		   FROM tags WHERE name='".$ras['name']."' ORDER BY category,name") or sqlerr(__FILE__, __LINE__);
          $num2 = mysql_num_rows($ros);
          if ($num2 > 1) {
                $uc++;
            while($arr = mysql_fetch_assoc($ros)) {
   
 //  $zvez[$ras[name]]="*";
if (isset($catizm) && $catizm == $arr["catname"]) {
$arrid="<b>".$arr["id"]."</b>";	
$arr["howmuch"]="<b>".$arr["howmuch"]."</b>";
$arr["catname"]="<b>".$arr["catname"]."</b>";
$clas=" bgcolor=\"#DDDDDD\"";
    	}
    	else {
    		$arrid=$arr["id"];	
    	$clas = "";
    	}
    	
 if ($uc%2 == 0)
$utc2 = "";
 else
$utc2 = " bgcolor=\"#DDDDDD\"";


 
print("<tr$clas>
<td align=center>$arrid</td>
<td align=center>$arr[howmuch]</td>
<td align=center>$ras[name] : <a href=\"". $_SERVER['PHP_SELF'] . "?deltagid=$arr[id]\">Удалить</a></td>
<td align=center>$arr[catname]</td>
</tr>\n");
                  $ip = (isset($arr["name"]) ? $arr["name"]:"");
                  $catizm = $arr["catname"];
                }
          }
        }
  }
}
end_frame();
end_table();

stdfoot();
?>
