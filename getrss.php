<? 
require "include/bittorrent.php";

dbconn();
loggedinorreturn();

$res = sql_query("SELECT id, name FROM categories ORDER BY name") or sqlerr(__FILE__, __LINE__);
while($cat = mysql_fetch_assoc($res))
$catoptions .= "<input type=\"checkbox\" name=\"cat[]\" value=\"$cat[id]\" ".(strpos($CURUSER['notifs'], "[cat$cat[id]]") !== false ? " checked" : "") . "/>$cat[name]<br />";
$category[$cat['id']] = $cat['name'];

stdhead("Лента новостей");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
$link = "$DEFAULTBASEURL/rss.php";
if ($_POST['feed'] == "dl")
$query[] = "feed=dl";

if (isset($_POST['cat']))
	$query[] = htmlspecialchars("cat=".implode(',', $_POST['cat']));
else {

/*stdmsg($tracker_lang['error'],"Вы должны выбрать категорию!");
stdfoot();
die();*/
}
if ($_POST['login'] == "passkey")
$query[] = "passkey=".$CURUSER["passkey"];

$queries = @implode("&", htmlspecialchars_uni($query));

if ($queries)
$link .= "?".$queries;

stdmsg($tracker_lang['success'], "Используйте этот адрес в вашей программе для чтения RSS: <br /><a href=$link>$link</a>");
stdfoot();
die("Бац!!!");
}
?>
<form method="post" action="getrss.php">
<table border="1" cellspacing="1" cellpadding="5">
<tr>
<td class="rowhead">Категории:
</td>
<td><?=$catoptions?>
<span class="small">если вы не выберете категории для просмотра,<br /> вам будет выдана ссылка на все категории.</span>
</td>
</tr>
<tr>
<td class="rowhead">Тип ссылки в rss:
</td>
<td>
<input type="radio" name="feed" value="web" checked />Ссылка на страницу<br>
<input type="radio" name="feed" value="dl" />Ссылка на скачивание
</td>
</tr>
<tr>
<td class="rowhead">Тип логина:
</td>
<td>
<input type="radio" name="login" value="cookie" />Стандарт (cookies)<br>
<input type="radio" name="login" value="passkey" checked />Альтернативный (passkey)
</td>
</tr>
<tr>
<td colspan="2" align="center">
<button type="submit">Сгенерировать rss ссылку</button>
</td>
</tr>
</table>
</form>

<?
stdfoot();


/*
<form action="http://id6.ru/getrss.php" method="POST">
<input type="text" name="cat[1]" value="&lt;script&gt;alert(/XSS/)&lt;/script&gt;">
<input type="submit">
</form>
*/
?>