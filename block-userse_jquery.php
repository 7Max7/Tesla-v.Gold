<?
require_once 'include/bittorrent.php';

header ("Content-Type: text/html; charset=windows-1251");
dbconn(false,true);

/**
 * Данный файл проверен на индексы, все запросы быстро выполняются.
**/
  
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

global $CURUSER;

if (empty($CURUSER)){
///@header("Location: ../index.php");
die;
}

$txlst3 = sql_query("SELECT COUNT(*) AS one FROM thanks WHERE touid=" . $CURUSER["id"]) or sqlerr(__FILE__,__LINE__);
$list3 = mysql_fetch_row($txlst3);
$count3 = $list3[0];  

if (!$count3=="0"){
echo "<font color=\"darkgreen\">Cпасибо: " .$count3. " шт</font><br />\n";
}

} else @header("Location: ../index.php");

?>