<? 
require "include/bittorrent.php";

dbconn();
loggedinorreturn();

$res = sql_query("SELECT id, name FROM categories ORDER BY name") or sqlerr(__FILE__, __LINE__);
while($cat = mysql_fetch_assoc($res))
$catoptions .= "<input type=\"checkbox\" name=\"cat[]\" value=\"$cat[id]\" ".(strpos($CURUSER['notifs'], "[cat$cat[id]]") !== false ? " checked" : "") . "/>$cat[name]<br />";
$category[$cat['id']] = $cat['name'];

stdhead("����� ��������");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
$link = "$DEFAULTBASEURL/rss.php";
if ($_POST['feed'] == "dl")
$query[] = "feed=dl";

if (isset($_POST['cat']))
	$query[] = htmlspecialchars("cat=".implode(',', $_POST['cat']));
else {

/*stdmsg($tracker_lang['error'],"�� ������ ������� ���������!");
stdfoot();
die();*/
}
if ($_POST['login'] == "passkey")
$query[] = "passkey=".$CURUSER["passkey"];

$queries = @implode("&", htmlspecialchars_uni($query));

if ($queries)
$link .= "?".$queries;

stdmsg($tracker_lang['success'], "����������� ���� ����� � ����� ��������� ��� ������ RSS: <br /><a href=$link>$link</a>");
stdfoot();
die("���!!!");
}
?>
<form method="post" action="getrss.php">
<table border="1" cellspacing="1" cellpadding="5">
<tr>
<td class="rowhead">���������:
</td>
<td><?=$catoptions?>
<span class="small">���� �� �� �������� ��������� ��� ���������,<br /> ��� ����� ������ ������ �� ��� ���������.</span>
</td>
</tr>
<tr>
<td class="rowhead">��� ������ � rss:
</td>
<td>
<input type="radio" name="feed" value="web" checked />������ �� ��������<br>
<input type="radio" name="feed" value="dl" />������ �� ����������
</td>
</tr>
<tr>
<td class="rowhead">��� ������:
</td>
<td>
<input type="radio" name="login" value="cookie" />�������� (cookies)<br>
<input type="radio" name="login" value="passkey" checked />�������������� (passkey)
</td>
</tr>
<tr>
<td colspan="2" align="center">
<button type="submit">������������� rss ������</button>
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