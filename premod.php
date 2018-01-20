<?
require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

if (get_user_class() < UC_MODERATOR)
  stderr($tracker_lang['error'], "Нет доступа.");

stdhead("Обзор проверки торрентов");

if (get_user_class() >= UC_ADMINISTRATOR){
$where=">=";
}
else
$where="=";

if ($CURUSER["postsperpage"]=="0"){
$limited = 15;
} else {
$limited = (int) $CURUSER["postsperpage"];
}

if (isset($_GET["top"])) {
   echo "<table width=\"100%\" align=\"center\" cellpadding=\"3\"><tr>
   
   <td align=\"center\" ".((!isset($_GET["modded"]) && !isset($_GET["moderator"]) && !isset($_GET["top"])) ? "class=\"a\">":"class=\"b\">")."<a href=\"premod.php\">Непроверенные</a>".((!isset($_GET["modded"]) && !isset($_GET["moderator"]) && !isset($_GET["top"])) ? "<br>Непроверенные торренты [$count]":"")."</td>
   
   <td align=\"center\" ".(isset($_GET["modded"]) ? "class=\"a\"":"class=\"b\"").">
  <a href=\"premod.php?modded\">Проверенные сегодня</a>".(isset($_GET["modded"]) ?"<br>Проверенные торренты [$count]":"")."</td>
   <td align=\"center\" ".(isset($_GET["top"]) ? "class=\"a\"":"class=\"b\"")."><a href=\"premod.php?top\">Топ модераторов</a></td>
   </tr></table><br>";

  echo '<table width="100%" cellpadding="5"><tr>
  <td class="colhead">№</td>
  <td class="colhead">Логин</td>
  <td class="colhead">Проверил</td>
  <td class="colhead">Залил</td>
  </tr>';
  
  /*
  ,
  (SELECT COUNT(*) FROM torrents WHERE moderatedby = users.id and moderated='yes') AS num_t,
  (SELECT COUNT(*) FROM torrents AS t WHERE t.owner=users.id) AS num_s
  */
  
  
  $res = sql_query("SELECT id, username, class
   FROM users WHERE class $where ".UC_MODERATOR." ORDER BY (SELECT COUNT(*) FROM torrents AS t WHERE moderatedby = users.id) DESC")  or sqlerr(__FILE__,__LINE__);
  if (!mysql_num_rows($res))
      echo ("<tr><td colspan=\"3\">Нет статистики</td></tr>");
  else
  {
    $i=1;
    while ($row = mysql_fetch_array($res))
    {
      $num_s = get_row_count("torrents WHERE moderated='yes' AND owner=".sqlesc($row["id"]));
   	  $num_t = get_row_count("torrents WHERE moderated='yes' AND moderatedby=".sqlesc($row["id"]));
   	  
        if ($i%2==0){
	$td1='class="b"';
	$td2='class="a"';
	} else {
    $td1='class="a"';
    $td2='class="b"';
    }
    	
      echo '<tr>
	  <td '.$td1.'>'.$i.'</td>
	  <td '.$td2.'><a href="userdetails.php?id='.$row["id"].'">'.get_user_class_color($row["class"], $row["username"]).'</a></td>
	  <td '.$td1.'>'.number_format($num_t).'
	  </td>
	  <td '.$td2.'>'.number_format($num_s).'</td>
	  </tr>';
      ++$i;
    }
  }
  echo '</tr></table>';
}

elseif (isset($_GET["modded"])){

$h = date('H'); // проверяем час
if ($h>"00") $h=$h;
else $h=0;

$date_utro=get_date_time(gmtime() - $h*3600);

  $count = get_row_count("torrents WHERE moderated='yes' AND moderatordate>=".sqlesc($date_utro));
  
  list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "premod.php?modded&");
 
   echo "<table width=\"100%\" align=\"center\" cellpadding=\"3\"><tr>
   
   <td align=\"center\" ".((!isset($_GET["modded"]) && !isset($_GET["moderator"]) && !isset($_GET["top"])) ? "class=\"a\">":"class=\"b\">")."<a href=\"premod.php\">Непроверенные</a>".((!isset($_GET["modded"]) && !isset($_GET["moderator"]) && !isset($_GET["top"])) ? "<br>Непроверенные торренты [$count]":"")."</td>
   
   <td align=\"center\" ".(isset($_GET["modded"]) ? "class=\"a\"":"class=\"b\"").">
  <a href=\"premod.php?modded\">Проверенные сегодня</a>".(isset($_GET["modded"]) ?"<br>Проверенные торренты [$count]":"")."</td>
   <td align=\"center\" ".(isset($_GET["top"]) ? "class=\"a\"":"class=\"b\"")."><a href=\"premod.php?top\">Топ модераторов</a></td>
   </tr></table><br>";
 
 
  echo '<table width="100%" cellpadding="5"><tr>
  <td class="colhead">Торрент</td>
  <td class="colhead">Загрузил</td>
  <td class="colhead">Проверил</td>
  <td class="colhead">Время проверки</td></tr>';
  $res = sql_query("SELECT torrents.*, users.username, users.class,m.username AS musername, m.class AS mclass  FROM torrents 
  LEFT JOIN users ON torrents.owner = users.id  
  LEFT JOIN users AS m ON torrents.moderatedby = m.id 
  WHERE moderated = 'yes' AND torrents.moderatordate>=".sqlesc($date_utro)." 
  ORDER BY torrents.moderatordate DESC $limit")  or sqlerr(__FILE__,__LINE__);
  
  if (!$count)
      echo ("<tr><td colspan=\"4\">Нет проверенных торрентов за сегодня</td></tr>");
  else
  { $nut=0;
    while ($row = mysql_fetch_array($res)){
    	
    if ($nut%2==0){
	$td1='class="b"';
	$td2='class="a"';
	} else {
    $td1='class="a"';
    $td2='class="b"';
    }

      echo '<tr>
	  <td '.$td2.'><a href="details.php?id='.$row["id"].'">'.$row["name"].'</a></td>
	  <td '.$td1.'><a href="userdetails.php?id='.$row["owner"].'">'.get_user_class_color($row["class"], $row["username"]).'</a></td>
	 <td '.$td2.'><a href="userdetails.php?id='.$row["moderatedby"].'">'.get_user_class_color($row["mclass"], $row["musername"]).'</a></td>
	  <td '.$td1.'>'.$row["moderatordate"].'</td></tr>';

	  ++$nut;
	  }
  }
  if ($count)
  {
    echo '<tr><td colspan="4">';
    echo $pagerbottom;
    echo '</td></tr>';
  }
  echo '</tr></table>';
}
else
{
  $count = get_row_count("torrents WHERE moderated='no'");
  list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "premod.php?");
 
 $arr = sql_query("SELECT COUNT(*) AS numgrab,(SELECT COUNT(*) AS numgrab FROM tgrabber WHERE work='0') AS grabost FROM tgrabber WHERE work='1'");
  $row_arr = mysql_fetch_array($arr);

  echo "<table width=\"100%\" align=\"center\" cellpadding=\"3\"><tr>
   
   <td align=\"center\" ".((!isset($_GET["modded"]) && !isset($_GET["moderator"]) && !isset($_GET["top"])) ? "class=\"a\">":"class=\"b\">")."<a href=\"premod.php\">Непроверенные</a>".((!isset($_GET["modded"]) && !isset($_GET["moderator"]) && !isset($_GET["top"])) ? "<br>Непроверенные торренты [$count]":"")."</td>
   
   <td align=\"center\" ".(isset($_GET["modded"]) ? "class=\"a\"":"class=\"b\"").">
  <a href=\"premod.php?modded\">Проверенные сегодня</a>".(isset($_GET["modded"]) ?"<br>Проверенные торренты [$count]":"")."</td>
   <td align=\"center\" ".(isset($_GET["top"]) ? "class=\"a\"":"class=\"b\"")."><a href=\"premod.php?top\">Топ модераторов</a></td>
   </tr></table><br>";
   
$procents="<b>Завершенно</b>: ".(100-@number_format(100 * (1 - ($row_arr["grabost"] / $row_arr["numgrab"])),2))."%";

   echo "<table width=\"100%\" align=\"center\" cellpadding=\"3\"><tr>
   <td align=\"center\" class=\"a\">Доступно для граббера - ".@number_format($row_arr["numgrab"])." торрентов <br>
   Одобрено для граббера - ".@number_format($row_arr["grabost"])." торрентов.

   </td>
   </tr>
   
   <form method=\"get\" action=\"parser_d.php\">
<tr><td align=\"center\" class=\"b\">Добавить новый внешний файл (введите число): <input type=\"text\" size=\"8\" name=\"id\" style=\"width: 100px; border: 1px solid green\"> <input type=\"submit\" value=\"Скачать и оформить релиз\"/><br>Данные берутся из ссылки, пример http://torrentsmd.com/details.php?id=<b>901371</b> <br> (на примере мы вводим число <b>901371</b> в поле для ввода)
</form></td></tr>
   
   
   </table><br>";
  
  

  echo '<table width="100%" cellpadding="5"><tr>
  <td class="colhead"><b>Торрент / Комментарий</b></td>
  <td align=center class="colhead"><b>Загрузил</b></td>
  <td align=center class="colhead"><b>Время</b></td>
  </tr>';
  
  $res = sql_query("SELECT torrents.*, users.username, users.class FROM torrents 

  LEFT JOIN users ON torrents.owner = users.id  
  WHERE moderated = 'no' GROUP BY  torrents.id  ORDER BY torrents.id $limit")  or sqlerr(__FILE__,__LINE__);
  $num=0;
  if (!mysql_num_rows($res))
      echo ("<tr><td colspan=\"3\">Все торренты проверены</td></tr>");
  else
  {
    while ($row = mysql_fetch_array($res)){
    
    if ($num%2==0){
	$td1='class="b"';
	$td2='class="a"';
	} else {
    $td1='class="a"';
    $td2='class="b"';
    }
    

    echo "<tr>";
    
	echo "<td ".$td2.">
<a href=\"details.php?id=".$row["id"]."\">".$row["name"]."</a>
".(get_user_class() >= UC_MODERATOR ? "<b>[</b><a href=\"edit.php?id=".$row["id"]."\">Редактировать</a><b>]</b> <b>[</b><a href=\"check.php?id=".$row["id"]."\">Одобрить</a><b>]</b>":"")."
".(isset($row["addet"]) ? "<br><br><small>Последний <a href=\"details.php?id=".$row["id"]."&page=last&viewcomm=".$row["uidcom"]."#comm".$row["uidcom"]."\">комментарий</a> от <a href=\"userdetails.php?id=".$row["useid"]."\">".get_user_class_color($row["laclass"], $row["lauser"])."</a> в ".$row["addet"]." <b>тестируется</b></small>":"")."
</td>";

echo "<td align=center ".$td1."><a href=\"userdetails.php?id=".$row["owner"]."\">".get_user_class_color($row["class"], $row["username"])."</a></td>";

	 echo "<td align=center ".$td2.">".$row["added"]."</td>";
	 echo "</tr>";
  unset($row["addet"],$row["laclass"],$row["lauser"],$row["useid"]);
  ++$num;
  }
  }
  
  if ($count)
  {
    echo '<tr><td colspan="3">';
    echo $pagerbottom;
    echo '</td></tr>';
  }
  echo '</tr></table>';
}

stdfoot();

?>
