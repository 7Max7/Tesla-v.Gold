<?
if (!defined('BLOCK_FILE')) { Header("Location: ../index.php");   exit;  }

$cacheStatFile = "cache/block-birth.txt";
$expire = 3600*6; // 6 час
if (file_exists($cacheStatFile) && filesize($cacheStatFile)<>0 && filemtime($cacheStatFile) > (time() - $expire)) {
$content.=file_get_contents($cacheStatFile);
} else
{

///0.032222 [SELECT birthday, id, username, class, gender FROM users WHERE enabled='yes' and birthday != '0000-00-00']
///0.013941 [SELECT id, username, class, gender FROM users WHERE birthday LIKE '%-09-10%' LIMIT 10]
$b = 0;

//$res = sql_query("SELECT id, username, class, gender FROM users WHERE birthday LIKE '%".date("-m-d")."%' LIMIT 10") or sqlerr(__FILE__, __LINE__);

$timeday = date("-m-d");

$res = sql_query("SELECT id, username, class, gender FROM users WHERE birthday LIKE '%$timeday%' LIMIT 100") or sqlerr(__FILE__, __LINE__);

while ($arr = mysql_fetch_assoc($res)) {

$username = $arr["username"];

if ($b > 0)
$content .=", ";

$content.="<a href=userdetails.php?id=".$arr["id"].">".get_user_class_color($arr["class"], $arr["username"])."</a>";

if ($arr["gender"] == "2"){
$content .= "<img border=\"0\" alt=\"ƒевушка\" src=\"pic/ico_f.gif\">";
} elseif ($arr["gender"] == "1"){
$content .= "<img border=\"0\" alt=\"ѕарень\" src=\"pic/ico_m.gif\">";
}

++$b;
}



if (empty($b))
$content .="<center>∆дем ждем ...</center>";

$fp = fopen($cacheStatFile,"w");
if($fp) {
fputs($fp, $content);
fclose($fp);
}
}

?>