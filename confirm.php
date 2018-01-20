<?
require_once("include/bittorrent.php");

$id = intval($_GET["id"]);

$md5 = $_GET["secret"];

if (!$id)
die("Неверный id, только цифра может быть.");

dbconn();

$res = sql_query("SELECT passhash, editsecret, status, shelter FROM users WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);

if (empty($row))
die("Не существует такой id пользователя");

if ($row["status"] <> "pending") {
	header("Location: ok.php?type=confirmed");
	exit("Ваш аккаунт уже активирован.");
}

$sec = hash_pad($row["editsecret"]);
$shelter=$row["shelter"];
if ($md5 <> md5($sec))
die("Неверен editsecret пожалуйста проверьте правильность кода");

sql_query("UPDATE users SET status='confirmed', editsecret='' WHERE id=".sqlesc($id)." AND status='pending'");

if (!mysql_affected_rows())
	httperr();

logincookie($id, $row["passhash"], $shelter);

@unlink(ROOT_PATH."cache/block-online.txt");
@unlink(ROOT_PATH."cache/block-traffic.txt");

header("Location: ok.php?type=confirm");
?>