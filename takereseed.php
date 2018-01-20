<? 
require "include/bittorrent.php";

dbconn();
loggedinorreturn();

if (!empty($_GET["torrent"])){
$id = (int) $_GET["torrent"];

if (!is_valid_id($id)){
header("Location: $BASEURL");
die;
}

$res = sql_query("SELECT torrents.seeders, torrents.banned, torrents.leechers, torrents.name, torrents.times_completed, torrents.id, UNIX_TIMESTAMP(torrents.last_reseed) AS lr, categories.name AS cat_name FROM torrents LEFT JOIN categories ON torrents.category = categories.id WHERE torrents.id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);

if (!$row || $row["banned"] == "yes")
	stderr($tracker_lang['error'], $tracker_lang['no_torrent_with_such_id']);

if ($row["times_completed"] == 0)
	stderr($tracker_lang['error'], "Извините, но этот торрент еще никем не скачан.");

if ($row["leechers"] == 0)
	stderr($tracker_lang['error'], "На этой раздаче не нужна помощь т.к. ее никто не качает.");

$dt = time() - 24*3600;

if ($row["lr"] > $dt && get_date_time($row["lr"]) != "0000-00-00 00:00:00")
	stderr($tracker_lang['error'], "Извините, но еще не прошли сутки с прошлого запроса вернутся на раздачу.");

$subject = sqlesc("Помогите раздать {$row["name"]}");

$msg = sqlesc("Здравствуйте! Ваша помощь необходима в раздаче [url=$DEFAULTBASEURL/details.php?id={$id}]{$row["cat_name"]} :: {$row["name"]}[/url] Если вы решили помочь, но уже удалили торрент-файл, можете скачать его [url=$DEFAULTBASEURL/download.php?id=$id]здесь[/url].
Надеюсь на вашу помощь!");

sql_query("INSERT INTO messages (sender, receiver, poster, added, subject, msg) SELECT $CURUSER[id], userid, 0, NOW(), $subject, $msg FROM snatched WHERE torrent = ".sqlesc($id)." AND userid <> $CURUSER[id] AND finished = 'yes'") or sqlerr(__FILE__, __LINE__);

sql_query("UPDATE torrents SET last_reseed = NOW() WHERE id = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);

header("Refresh: 2; url=details.php?id=$id");

stdhead("Позвать скачавших на торрент $row[name]");

stdmsg("Успешно", "Ваш запрос на призыв скачавших выполнен. Ждите результатов в течение суток, иначе повторите запрос.");

stdfoot();
}
else
{
stdhead("Ошибка");
stdmsg("Данный пусты", "Нет id");

stdfoot();
}
?>