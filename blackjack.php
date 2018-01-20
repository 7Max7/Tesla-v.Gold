<? 
require_once("include/bittorrent.php"); 
dbconn(false); 
loggedinorreturn(); 
function get_user_name($userid){ 
$r = mysql_query("select username from users where id=$userid"); 
$a = mysql_fetch_array($r); 
return "$a[username]"; 
} 
/*

switch ($cardid) {
/// начало
case '1': { $picture='2p.png'; $points='2'; } break;
case '2': { $picture='3p.png'; $points='3'; } break;
case '3': { $picture='4p.png'; $points='4'; } break;
case '4': { $picture='5p.png'; $points='5'; } break;
case '5': { $picture='6p.png'; $points='6'; } break;
case '6': { $picture='7p.png'; $points='7'; } break;
case '7': { $picture='8p.png'; $points='8'; } break;
case '8': { $picture='9p.png'; $points='9'; } break;
case '9': { $picture='10p.png'; $points='10'; } break;
case '10': { $picture='vp.png'; $points='10'; } break;
case '11': { $picture='dp.png'; $points='10'; } break;
case '12': { $picture='kp.png'; $points='10'; } break;
case '13': { $picture='tp.png'; $points='1'; } break;
case '14': { $picture='2b.png'; $points='2'; } break;
case '15': { $picture='3b.png'; $points='3'; } break;
case '16': { $picture='4b.png'; $points='4'; } break;
case '17': { $picture='5b.png'; $points='5'; } break;
case '18': { $picture='6b.png'; $points='6'; } break;
case '19': { $picture='7b.png'; $points='7'; } break;
case '20': { $picture='8b.png'; $points='8'; } break;
case '21': { $picture='9b.png'; $points='9'; } break;
case '22': { $picture='10b.png'; $points='10'; } break;
case '23': { $picture='vb.png'; $points='10'; } break;
case '24': { $picture='db.png'; $points='10'; } break;
case '25': { $picture='kb.png'; $points='10'; } break;
case '26': { $picture='tb.png'; $points='1'; } break;
case '27': { $picture='2k.png'; $points='2'; } break;
case '28': { $picture='3k.png'; $points='3'; } break;
case '29': { $picture='4k.png'; $points='4'; } break;
case '30': { $picture='5k.png'; $points='5'; } break;
case '31': { $picture='6k.png'; $points='6'; } break;
case '32': { $picture='7k.png'; $points='7'; } break;
case '33': { $picture='8k.png'; $points='8'; } break;
case '34': { $picture='9k.png'; $points='9'; } break;
case '35': { $picture='10k.png'; $points='10'; } break;
case '36': { $picture='vk.png'; $points='10'; } break;
case '37': { $picture='dk.png'; $points='10'; } break;
case '38': { $picture='kk.png'; $points='10'; } break;
case '39': { $picture='tk.png'; $points='1'; } break;
case '40': { $picture='2c.png'; $points='2'; } break;
case '41': { $picture='3c.png'; $points='3'; } break;
case '42': { $picture='4c.png'; $points='4'; } break;
case '43': { $picture='5c.png'; $points='5'; } break;
case '44': { $picture='6c.png'; $points='6'; } break;
case '45': { $picture='7c.png'; $points='7'; } break;
case '46': { $picture='8c.png'; $points='8'; } break;
case '47': { $picture='9c.png'; $points='9'; } break;
case '48': { $picture='10c.png'; $points='10'; } break;
case '49': { $picture='vc.png'; $points='10'; } break;
case '50': { $picture='dc.png'; $points='10'; } break;
case '51': { $picture='kc.png'; $points='10'; } break;
case '52': { $picture='tc.png'; $points='1'; } break;
//конец 
}

*/ 


$mb = 1024*1024*100;//bet size 
if ($_POST["game"]){ 
$cardcountres = mysql_query("select count(id) from cards") or sqlerr(__FILE__, __LINE__); 
$cardcountarr = mysql_fetch_array($cardcountres); 
$cardcount = $cardcountarr[0]; 
if ($_POST["game"] == 'start'){ 

if($CURUSER["uploaded"] < $mb) 
stderr("Извините ".$CURUSER["username"],"У вас нет аплоада ".mksize($mb)); 
$required_ratio = 0.3; 
if ($CURUSER["downloaded"] > 0) 
$ratio = number_format($CURUSER["uploaded"] / $CURUSER["downloaded"], 2); 
else 
if ($CURUSER["uploaded"] > 0) 
$ratio = 999; 
else 
$ratio = 0; 
if($ratio < $required_ratio) 
stderr("Извините ".$CURUSER["username"],"Ваш рейтинг меньше минимальной ставки ".$required_ratio); 
$res = mysql_query("select count(*) from blackjack where userid=$CURUSER[id] and status='waiting'"); 
$arr = mysql_fetch_array($res); 
if ($arr[0] > 0) { 
stderr("Вы должны дождаться, пока кто-нибудь не сыграет с Вами.."); 
}else{ 
$res = mysql_query("select count(*) from blackjack where userid=$CURUSER[id] and status='playing'"); 
$arr = mysql_fetch_array($res); 
if ($arr[0] > 0) 
stderr("Вы еще не завершили предыдущую игру. <form method=post name=form action=$phpself><input type=hidden name=game value=cont><input type=submit value='Вернуться к ней?'></form>");} 
$cardid = rand(1,$cardcount); 
$cardres = mysql_query("select * from cards where id=$cardid") or sqlerr(__FILE__, __LINE__); 
$cardarr = mysql_fetch_array($cardres); 
mysql_query("insert into blackjack (userid, points, cards) values($CURUSER[id], $cardarr[points], $cardid)") or sqlerr(__FILE__, __LINE__); 
stdhead(); 
print("<h1>Привет, <b><a href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]</a></b>. Готов просадить свои mb?!</h1>\n"); 
print("<table cellspacing=0 cellpadding=3 width=600>\n"); 
print("<tr><td colspan=2 cellspacing=0 cellpadding=5 >"); 
print("<form name=blackjack method=post action=$phpself>"); 
print("<table class=message width=100% cellspacing=0 cellpadding=5 bgcolor=white>\n"); 
print("<tr><td align=center><img src=pic/cards/".$cardarr["pic"]." border=0></td></tr>"); 
print("<tr><td align=center><b>Очки = $cardarr[points]</b></td></tr>"); 
print("<tr><td align=center><input type=hidden name=game value=cont><input type=submit value='Еще давай'></td></tr>"); 
print("</table><br>"); 
print("</form>"); 
print("</td></tr></table><br>"); 
stdfoot(); 
} 
elseif ($_POST["game"] == 'cont'){ 

$playeres = mysql_query("select * from blackjack where userid=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
$playerarr = mysql_fetch_array($playeres); 
$showcards = ""; 
$cards = $playerarr["cards"]; 
$usedcards = explode(" ", $cards); 
$arr = array(); 
foreach($usedcards as $array_list) 
$arr[] = $array_list; 
foreach($arr as $card_id) 
{ 
$used_card = mysql_query("SELECT * FROM cards WHERE id='$card_id'") or sqlerr(__FILE__, __LINE__); 
$used_cards = mysql_fetch_array($used_card); 
$showcards .= "<img src=pic/cards/".$used_cards["pic"]." border=0> "; 
$i++; 
} 
$cardid = rand(1,$cardcount); 
while (in_array($cardid, $arr)) 
{ 
$cardid = rand(1,$cardcount); 
} 
$cardres = mysql_query("select * from cards where id=$cardid") or sqlerr(__FILE__, __LINE__); 
$cardarr = mysql_fetch_array($cardres); 
$showcards .= "<img src=pic/cards/".$cardarr["pic"]." border=0> "; 
$points = $playerarr["points"] + $cardarr["points"]; 
$mysqlcards = "$playerarr[cards] $cardid"; 
mysql_query("update blackjack set points=points+$cardarr[points], cards='$mysqlcards' where userid=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
if ($points == 21){ 
$waitres = mysql_query("select count(*) from blackjack where status='waiting'"); 
$waitarr = mysql_fetch_array($waitres); 
if ($waitarr[0] > 0){ 
$r = mysql_query("select * from blackjack where status='waiting' order by date asc LIMIT 1"); 
$a = mysql_fetch_assoc($r); 
if ($a["points"] != 21){ 
$winorlose = "вы Выйграли ".mksize($mb); 
mysql_query("update users set uploaded = uploaded + $mb where id=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
mysql_query("update users set uploaded = uploaded - $mb where id=$a[userid]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set win = win + $mb where userid=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set lost = lost + $mb where userid=$a[userid]") or sqlerr(__FILE__, __LINE__); 
mysql_query("delete from blackjack where userid=$CURUSER[id]"); 
mysql_query("delete from blackjack where userid=$a[userid]"); 
$dt = sqlesc(get_date_time()); 
$msg = sqlesc("Вы проиграли ".mksize($mb)." $CURUSER[username] (у Вас $a[points], у $CURUSER[username] 21 очко). [url=$DEFAULTBASEURL/blackjack.php]Играть еще?[/url]"); 
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $a[userid], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); 
}else{ 
$winorlose = "ничья"; 
} 
stderr("Игра окончена", "у Вас 21 очко, Вашим опонентом был ".get_user_name($a["userid"]).", с $a[points] очками на борту, $winorlose. <a href=blackjack.php>Играть еще?</a>"); 
} 
else{ 
mysql_query("update blackjack set status = 'waiting', date='".get_date_time()."' where userid = $CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
stderr("Игра окончена", "у Вас 21 очко, пока что нет других игроков. Вам придется подождать соперника. Вы получите ЛС о результатах игры"); 
} 
} 
elseif ($points > 21) { 
$waitres = mysql_query("select count(*) from blackjack where status='waiting'"); 
$waitarr = mysql_fetch_array($waitres); 
if ($waitarr[0] > 0){ 
$r = mysql_query("select * from blackjack where status='waiting' order by date asc LIMIT 1"); 
$a = mysql_fetch_assoc($r); 
if ($a["points"] == $points){ 
$winorlose = "Ничья!"; 
mysql_query("delete from blackjack where userid=$CURUSER[id]"); 
mysql_query("delete from blackjack where userid=$a[userid]"); 
$dt = sqlesc(get_date_time()); 
$msg = sqlesc("Вашим соперником был $CURUSER[username], победила дружба! [url=$DEFAULTBASEURL/blackjack.php]Играть еще?[/url]"); 
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $a[userid], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);} 
elseif ($a["points"] > $points){ 
$winorlose = "Вы выйграли ".mksize($mb); 
mysql_query("update users set uploaded = uploaded + $mb where id=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
mysql_query("update users set uploaded = uploaded - $mb where id=$a[userid]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set win = win + $mb where userid=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set lost = lost + $mb where userid=$a[userid]") or sqlerr(__FILE__, __LINE__); 
mysql_query("delete from blackjack where userid=$CURUSER[id]"); 
mysql_query("delete from blackjack where userid=$a[userid]"); 
$dt = sqlesc(get_date_time()); 
$msg = sqlesc("Вы проиграли ".mksize($mb)." $CURUSER[username] (у Вас $a[points], у $CURUSER[username] $points). [url=$DEFAULTBASEURL/blackjack.php]Играть еще?[/url]"); 
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $a[userid], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); 
} 
elseif ($a["points"] < $points){ 
$winorlose = "Вы проиграли ".mksize($mb); 
mysql_query("update users set uploaded = uploaded - $mb where id=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
mysql_query("update users set uploaded = uploaded + $mb where id=$a[userid]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set win = win + $mb where userid=$a[userid]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set lost = lost + $mb where userid=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
mysql_query("delete from blackjack where userid=$CURUSER[id]"); 
mysql_query("delete from blackjack where userid=$a[userid]"); 
$dt = sqlesc(get_date_time()); 
$msg = sqlesc("Вы выйграли ".mksize($mb)." у $CURUSER[username] (у Вас $a[points], у $CURUSER[username] $points). [url=$DEFAULTBASEURL/blackjack.php]Играть еще?[/url] "); 
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $a[userid], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); 
} 
stderr("Игра окончена", "у Вас $points, Вашим соперником был ".get_user_name($a["userid"]).", с $a[points] очками на борту, $winorlose. <a href=blackjack.php>Играть еще?</a>"); 
} 
else 
{ 
mysql_query("update blackjack set status = 'waiting', date='".get_date_time()."' where userid = $CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
stderr("Игра окончена", "у Вас $points, пока что нет других игроков. Вам придется подождать соперника. Вы получите ЛС о результатах игры"); 
} 
} 

else{ 
stdhead(); 
print("<h1>Привет, <b><a href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]</a></b>. Готов просадить свои mb?!</h1>\n"); 
print("<table cellspacing=0 cellpadding=3 width=600>\n"); 
print("<tr><td colspan=2 cellspacing=0 cellpadding=5 >"); 
print("<table class=message width=100% cellspacing=0 cellpadding=5 bgcolor=white>\n"); 
print("<tr><td align=center>$showcards</td></tr>"); 
print("<tr><td align=center><b>Points = $points</b></td></tr>"); 
print("<form name=blackjack method=post action=$phpself>"); 
print("<tr><td align=center><input type=hidden name=game value=cont><input type=submit value='Еще давай'></td></tr>"); 
print("</form>"); 
print("<form name=blackjack method=post action=$phpself>"); 
print("<tr><td align=center><input type=hidden name=game value=stop><input type=submit value='Хватит'></td></tr>"); 
print("</form>"); 
print("</table><br>"); 
print("</td></tr></table><br>"); 
stdfoot(); 
} 
}elseif ($_POST["game"] == 'stop') 
{ 

$playeres = mysql_query("select * from blackjack where userid=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
$playerarr = mysql_fetch_array($playeres); 
$waitres = mysql_query("select count(*) from blackjack where status='waiting'"); 
$waitarr = mysql_fetch_array($waitres); 
if ($waitarr[0] > 0){ 
$r = mysql_query("select * from blackjack where status='waiting' order by date asc LIMIT 1"); 
$a = mysql_fetch_assoc($r); 
if ($a["points"] == $playerarr[points]){ 
$winorlose = "ничья"; 
mysql_query("delete from blackjack where userid=$CURUSER[id]"); 
mysql_query("delete from blackjack where userid=$a[userid]"); 
$dt = sqlesc(get_date_time()); 
$msg = sqlesc("Вашим соперником был $CURUSER[username], победила дружба! [url=$DEFAULTBASEURL/blackjack.php]Играть еще?[/url]"); 
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $a[userid], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);} 
elseif ($a["points"] < $playerarr[points] && $a[points] < 21){ 
$winorlose = "Вы выйграли ".mksize($mb); 
mysql_query("update users set uploaded = uploaded + $mb where id=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
mysql_query("update users set uploaded = uploaded - $mb where id=$a[userid]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set win = win + $mb where userid=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set lost = lost + $mb where userid=$a[userid]") or sqlerr(__FILE__, __LINE__); 
mysql_query("delete from blackjack where userid=$CURUSER[id]"); 
mysql_query("delete from blackjack where userid=$a[userid]"); 
$dt = sqlesc(get_date_time()); 
$msg = sqlesc("Вы проиграли ".mksize($mb)." $CURUSER[username] (у Вас $a[points], у $CURUSER[username] $playerarr[points]). [url=$DEFAULTBASEURL/blackjack.php]Играть еще?[/url]"); 
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $a[userid], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); 
} 
elseif ($a["points"] > $playerarr[points] && $a[points] < 21){ 
$winorlose = "Вы проиграли ".mksize($mb); 
mysql_query("update users set uploaded = uploaded - $mb where id=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
mysql_query("update users set uploaded = uploaded + $mb where id=$a[userid]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set win = win + $mb where userid=$a[userid]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set lost = lost + $mb where userid=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
mysql_query("delete from blackjack where userid=$CURUSER[id]"); 
mysql_query("delete from blackjack where userid=$a[userid]"); 
$dt = sqlesc(get_date_time()); 
$msg = sqlesc("Вы выиграли ".mksize($mb)." у $CURUSER[username] (у Вас $a[points], у $CURUSER[username] $playerarr[points]). [url=$DEFAULTBASEURL/blackjack.php]Играть еще?[/url]"); 
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $a[userid], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); 
} 
elseif ($a["points"] == 21){ 
$winorlose = "Вы проиграли ".mksize($mb); 
mysql_query("update users set uploaded = uploaded - $mb where id=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
mysql_query("update users set uploaded = uploaded + $mb where id=$a[userid]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set win = win + $mb where userid=$a[userid]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set lost = lost + $mb where userid=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
mysql_query("delete from blackjack where userid=$CURUSER[id]"); 
mysql_query("delete from blackjack where userid=$a[userid]"); 
$dt = sqlesc(get_date_time()); 
$msg = sqlesc("Вы выйграли ".mksize($mb)." у $CURUSER[username] (у Вас $a[points], у $CURUSER[username] $playerarr[points]). [url=$DEFAULTBASEURL/blackjack.php]Играть еще?[/url]"); 
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $a[userid], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); 
} 
elseif ($a["points"] < $playerarr[points] && $a[points] > 21){ 
$winorlose = "Вы проиграли ".mksize($mb); 
mysql_query("update users set uploaded = uploaded - $mb where id=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
mysql_query("update users set uploaded = uploaded + $mb where id=$a[userid]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set win = win + $mb where userid=$a[userid]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set lost = lost + $mb where userid=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
mysql_query("delete from blackjack where userid=$CURUSER[id]"); 
mysql_query("delete from blackjack where userid=$a[userid]"); 
$dt = sqlesc(get_date_time()); 
$msg = sqlesc("Вы выйграли ".mksize($mb)." у $CURUSER[username] (у Вас $a[points], у $CURUSER[username] $playerarr[points]). [url=$DEFAULTBASEURL/blackjack.php]Играть еще?[/url]"); 
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $a[userid], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); 
} 
elseif ($a["points"] > $playerarr[points] && $a[points] > 21){ 
$winorlose = "Вы выйграли ".mksize($mb); 
mysql_query("update users set uploaded = uploaded + $mb where id=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
mysql_query("update users set uploaded = uploaded - $mb where id=$a[userid]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set win = win + $mb where userid=$CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
//mysql_query("update casino set lost = lost + $mb where userid=$a[userid]") or sqlerr(__FILE__, __LINE__); 
mysql_query("delete from blackjack where userid=$CURUSER[id]"); 
mysql_query("delete from blackjack where userid=$a[userid]"); 
$dt = sqlesc(get_date_time()); 
$msg = sqlesc("Вы проиграли ".mksize($mb)." $CURUSER[username] (у Вас $a[points], у $CURUSER[username] $playerarr[points]). [url=$DEFAULTBASEURL/blackjack.php]Играть еще?[/url]"); 
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $a[userid], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); 
} 
stderr("Игра окончена", "у Вас $playerarr[points], Вашим соперником был ".get_user_name($a["userid"]).", у него $a[points], $winorlose <a href=blackjack.php>Играть еще?</a>"); 
} 
else 
{ 
mysql_query("update blackjack set status = 'waiting', date='".get_date_time()."' where userid = $CURUSER[id]") or sqlerr(__FILE__, __LINE__); 
stderr("Игра окончена", "у Вас $points, пока что нет других игроков. Вам придется подождать соперника. Вы получите ЛС о результатах игры"); 
} 
} 
} 
else 
{ 
//if (playerexists($CURUSER[id]) == false) 
//mysql_query("insert into casino (userid, win, lost) values($CURUSER[id], 0, 0)") or sqlerr(__FILE__, __LINE__); 
stdhead(); 
print("<h1>21 Очко</h1>\n"); 
print("<table cellspacing=0 cellpadding=3 width=400>\n"); 
print("<tr><td colspan=2 cellspacing=0 cellpadding=5 align=center>"); 
//print("<h3><a href=players.php>Stats</a></h3>"); 
print("<table class=message width=100% cellspacing=0 cellpadding=10 bgcolor=white>\n"); 
print("<tr><td align=left><h3>Правила</h3>Вы должны набрать большее количество очков, чем у оппонента (21)</td></tr>"); 
print("</table><br>"); 
print("<form name=form method=post action=$phpself><input type=hidden name=game value=start><input type=submit class=btn value='Сдать!'>"); 
print("</td></tr></table>"); 

stdfoot(); 
} /*http://bit-torrent.kiev.ua/blackjack_aka-t726/index.html*/
?> 

