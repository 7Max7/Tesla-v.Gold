<?
define('IN_ANNOUNCE', true);
define('ROOT_PATH', str_replace("include","",dirname(__FILE__)));

require_once(ROOT_PATH.'/include/core_announce.php');

dbconn(false);

$agent = htmlentities($_SERVER['HTTP_USER_AGENT']);
if((strpos($agent, 'Opera')!== false)||(strpos($agent, 'MSIE')!== false)||(strpos($agent, 'Gecko')!== false)||(strpos($at, 'Netscape')!== false)||(strpos($at, ' I;')!== false)||(strpos($at, ' U;')!== false)||(strpos($at, 'Mozilla/')!== false))
die("Просмотр аннонса запрещен!");

if (empty($scrape))
err("","Функция сцепки (scrape) отключена системно.",1);

if (preg_match("/^Mozilla\\//i", $agent) || preg_match("/^Opera\\//i", $agent) || preg_match("/^Links/i", $agent) || preg_match("/^Lynx\\//i", $agent))
err($passkey,"Нельзя скачивать торренты через браузер: ".$agent,1);

if (empty($_SERVER['QUERY_STRING']))
err($passkey,"Данных нет или торрент не зарегистрирован на трекере. (scrape)",2);


function hash_where_arr($name, $hash_arr) {
    global $db;
    $new_hash_arr = array();
    for ($i = 0; $i < sizeof($hash_arr); ++$i) {
        $new_hash_arr[] = sqlesc(bin2hex(urldecode($hash_arr[$i])));
    }
    return $name . ' IN (' . implode(', ', $new_hash_arr) . ' )';
}

preg_match_all('/info_hash=([^&]*)/i', $_SERVER['QUERY_STRING'], $info_hash_array);
$fields = 'info_hash, name, times_completed AS completed, seeders, leechers';


if (sizeof($info_hash_array[1]))
$sql = 'SELECT ' . $fields . ' FROM torrents WHERE '.hash_where_arr('info_hash', $info_hash_array[1]).' LIMIT 300';
else 
$sql = 'SELECT ' . $fields . ' FROM torrents ORDER BY id LIMIT 300';


if (!($result = mysql_query($sql))) {
    err($passkey,'scrape sql error: '.mysql_error(),2);
}

if ($row = mysql_fetch_assoc($result)) {
    $r = 'd' . benc_str('files') . 'd';
        do {
            $r .= '20:' . pack("H*", ($row['info_hash'])) . 'd' .
                      benc_str('complete') . 'i' . $row['seeders'] . 'e' .
                      benc_str('downloaded') . 'i' . $row['completed'] . 'e' .
                      benc_str('incomplete') . 'i' . $row['leechers'] . 'e' .
                      'e';
        }
        while ($row = mysql_fetch_assoc($result));
        $r .= benc_str('flags') . 'd' . benc_str('min_request_interval') . 'i' . $announce_interval . 'ee';
        $r .= 'ee';
}
else {
err("","Торрент не зарегистрирован на трекере: ".$info_hash_array." (scrape)",1);
}

mysql_free_result($result);

benc_resp_raw($r);

?> 