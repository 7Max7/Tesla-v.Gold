<?


require "include/bittorrent.php";

dbconn(false);

loggedinorreturn();

if (!isset($_GET["id"]))
       $userid = $CURUSER["id"];
elseif (get_user_class() < UC_MODERATOR && $_GET["id"] != $CURUSER["id"])
        stderr($tracker_lang['error'], $tracker_lang['access_denied']);
elseif (get_user_class() >= UC_MODERATOR || $_GET["id"] == $CURUSER["id"])
        $userid = (int) $_GET["id"];

if (!is_valid_id($userid))
        stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

stdhead("Мои респекты");

        $count_res = sql_query("SELECT COUNT(*) FROM simpaty WHERE touserid = ".sqlesc($userid)."");
		$count_row = mysql_fetch_array($count_res);
		$count = $count_row[0];

if (!$count) {
stdmsg("Извините", "У этого пользователя нет респектов.");
stdfoot();
die();
} else {
?>
<table class="embedded" cellspacing="0" cellpadding="5" width="100%">
<tr><td class="colhead" align="center" colspan="12">Список респектов</td></tr>
<?
        unset ($admin);
        if (get_user_class()>=UC_ADMINISTRATOR) {
                $admin = true;
		}
        list($pagertop, $pagerbottom, $limit) = pager(30, $count, "mysimpaty.php?", array(lastpagedefault => 0));
		print("<tr><td class=\"index\" colspan=\"12\">");
		print($pagertop);
		print("</td></tr>");
?>
<tr>
<td width=10 class=colhead>#</td>
<td width=20 class=colhead>Тип</td>
<td class=colhead>От кого</td>
<td class=colhead>За что</td>
<td class=colhead>Причина</td>
<td class=colhead align=center width="10%">Дата</td>
<?
if ($admin)
	print("<td class=colhead width=5></td>");
?>
</tr>
<?

        $res = sql_query("SELECT *,	
		(SELECT class FROM users WHERE id=fromuserid) AS classname		
		 FROM simpaty WHERE touserid = ".sqlesc($userid)." ORDER BY respect_time DESC $limit") or sqlerr(__FILE__, __LINE__);
        while ($arr = mysql_fetch_assoc($res)) {
                $respect_id = $arr["id"];
                $touserid = $arr["touserid"];
                
                $torrentname=$arr["torrentname"];
                
                $respect_type = ($arr["bad"]==1?"bad":"good");
                $i++;
                if (substr($arr["type"],0,7) == 'torrent') {
                        $from_what = '<a href="details.php?id=' . substr($arr["type"],7,strlen($arr["type"])) . '&hit=1">' . "Торрент" . '</a>';
                }
                elseif (substr($arr["type"],0,7) == 'comment') {
                        preg_match("/comment[a-z]{0,3}([0-9]*)_id([0-9]*)/", $arr["type"], $matches);
                        $comment_id = $matches[1];
                        $id = $matches[2];
                        if (substr($arr["type"],0,10)=="commentreq") $filename = "requests.php";
                        elseif (substr($arr["type"],0,10)=="commentoff") $filename = "offers.php";
                        else $filename = "details.php";
                        $from_what = '<a href="' . $filename . '?id=' . $id . '&page=' . $page . '&viewcomm=' . $comment_id . '#comm' . $comment_id . '">' . "Комментарий" . '</a>';
                }
                
             //  elseif (substr($arr["type"],0,7) == 'request')
                  //      $from_what = '<a href="requests.php?id=' . substr($arr["type"],7,strlen($arr["type"])) . '">' . "Запрос" . '</a>';
             //   elseif (substr($arr["type"],0,5) == 'offer')
                   //     $from_what = '<a href="offers.php?id=' . substr($arr["type"],5,strlen($arr["type"])) . '">' . "Предложение" . '</a>';
                /*$tracker_template->assign_block_vars('simpaty_section.switch_count.simpaty_row', array(
                        'TOUSERID' => $touserid,
                        'RESPECT_ID' => $respect_id,
                        'RESPECT_TYPE' => $respect_type,
                        'NUMBER' => $i + (30 * (int)$_GET["page"]),
                        'TYPE' => $arr["good"]==1?'<img src="pic/thum_good.gif" alt="' . "Респект" . '" title="' . "Респект" . '">':'<img src="pic/thum_bad.gif" alt="' . "Антиреспект" . '" title="' . "Антиреспект" . '">',
                        'FROM_USERID' => $arr["fromuserid"],
                        'FROM_USERNAME' => $arr["fromusername"],
                        'FROM_WHAT' => $from_what,
                        'DESCRIPTION' => $arr["description"],
                        'DATE' => str_replace("\" \"", "<br />", display_date_time(strtotime($arr["respect_time"]), $CURUSER["tzoffset"])))
                );*/
                $number = $i + (30 * (int)$_GET["page"]);
                $type = $arr["good"]==1?'<img src="pic/thum_good.gif" alt="' . "Респект" . '" title="' . "Респект" . '">':'<img src="pic/thum_bad.gif" alt="' . "Антиреспект" . '" title="' . "Антиреспект" . '">';
                $fromuserid = $arr["fromuserid"];
                $fromusername = $arr["fromusername"];
                $description = $arr["description"];
                $respect_time = $arr["respect_time"];
               // $fromuserclass =$arr["classname"];
$fromusercolor=get_user_class_color($arr["classname"],$arr["fromusername"]);
?>
<tr>
<td><?=$number;?></td>
<td><?=$type;?></td>
<td><a href="userdetails.php?id=<?=$fromuserid;?>">
<?=$fromusercolor;?>


</a></td>
<td><?=$from_what;?></td>
<td><?=$description;?></td>
<td align="center"><?=$respect_time;?></td>
<?
if ($admin)
	print('<td><a href="simpaty.php?action=delete&amp;respect_id='.$respect_id.'&amp;touserid='.$touserid.'&amp;respect_type='.$respect_type.'&amp;returnto='.htmlentities($_SERVER["REQUEST_URI"]).'"><img src="pic/warned2.gif" border="0" /></a></td>');
?>
</tr>
<?
        }
}

print("<tr><td class=\"index\" colspan=\"12\">");
print($pagerbottom);
print("</td></tr>");

print("</table>");

stdfoot();

?>