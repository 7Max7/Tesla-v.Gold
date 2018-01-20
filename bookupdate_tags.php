<?
require "include/bittorrent.php";
header ("Content-Type: text/html; charset=windows-1251");

dbconn(false);


function convert_text($s){
$out = "";
for ($i=0; $i<strlen($s); $i++) {
$c1 = substr ($s, $i, 1);
$byte1 = ord ($c1);
if ($byte1>>5 == 6) // 110x xxxx, 110 prefix for 2 bytes unicode
{ 
$i++;
$c2 = substr ($s, $i, 1);
$byte2 = ord ($c2);
$byte1 &= 31; // remove the 3 bit two bytes prefix
$byte2 &= 63; // remove the 2 bit trailing byte prefix
$byte2 |= (($byte1 & 3) << 6); // last 2 bits of c1 become first 2 of c2
$byte1 >>= 2; // c1 shifts 2 to the right
$word = ($byte1<<8) + $byte2;
if ($word==1025) $out .= chr(168);                    // ?
elseif ($word==1105) $out .= chr(184);                // ?
elseif ($word>=0x0410 && $word<=0x044F) $out .= chr($word-848); // ?-? ?-?
else {
$a = dechex($byte1);
$a = str_pad($a, 2, "0", STR_PAD_LEFT);
$b = dechex($byte2);
$b = str_pad($b, 2, "0", STR_PAD_LEFT);
$out .= "&#x".$a.$b.";";
}} else  {
$out .= $c1;
 }
}
return $out;
}

global $CURUSER;

if (empty($CURUSER))
die("Авторизуйстесь");

$text = htmlspecialchars(strip_tags(convert_text($_POST['text'])));

$id = (int) $_POST['id'];

if (empty($text))
die("Текст пуст ".$id);

if (!is_valid_id($id))
die("Неверный идентификатор ".$id);
 
$datamy = sql_query("SELECT id FROM bookmarks WHERE torrentid = ".sqlesc($id)." AND userid=".sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);
$mtags = mysql_fetch_assoc($datamy);

if (empty($mtags["id"]))
die("Этого торрент файла у вас нет в закладках ($id).");
else 
{
sql_query("UPDATE bookmarks SET mytags=".sqlesc(trim($text))." WHERE id=".sqlesc($mtags["id"])) or sqlerr(__FILE__, __LINE__);

echo "Заметки обновлены как: ".trim($text);
}

?>