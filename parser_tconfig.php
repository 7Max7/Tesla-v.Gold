<?
//if(!defined('IN_PARSER'))  die('Hacking attempt parser!'); 

require_once("include/bittorrent.php");
//require_once("include/benc.php");

@ini_set('display_errors', 'On');
@set_time_limit(60);

dbconn();

/**
 * @author 7Max7
 * @copyright 2010
 */

$host="tfile.ru";

$data = "username=7Max7&password=5j8iaf5tg7&autologin=1";

// Устанавливаем соединение
$fp = fsockopen($host, 80, $errno, $errstr, 30) or die("Нет соединения"); 
// Проверяем успешность установки соединения
if ($fp) {
// Заголовок HTTP-запроса
$headers = "POST /login/ HTTP/1.0\r\n";
$headers.= "Host: $host\r\n";
$headers.= "Content-type: application/x-www-form-urlencoded\r\n";
$headers.= "Content-Length: ".strlen($data)."\r\n";
$headers.= "Accept: *\r\n";
$headers.= "Accept-Charset: *\r\n";
$headers.= "Accept-Encoding: binary\r\n";
$headers.= "Accept-Language: ru\r\n";
$headers.= "Referer: ".$host."\r\n";
$headers.= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.2; ru; rv:1.9.2) Gecko/20100115 Firefox/3.6 WebMoney Advisor\r\n";
$headers.= "Connection: keep-alive\r\n\r\n";
$headers.= $data."";
// Отправляем HTTP-запрос серверу

@fwrite($fp, $headers.$data);
$date = "";
while (!feof($fp)){
$line = fgets($fp, 1024);
$date.=$line;

// В заголовке меня интерисуют только куки
@list($field, $value) = preg_split('/\s*:\s*/', $line, 2);
// Запоминаем найденную куку
if (strtolower($field) === 'set-cookie'){
// Точнее, запоминаем только само значение куки (недекодированное)
$result[] = array_shift(preg_split('/\s*;\s*/', $value, 2));
}

}

@fclose($fp);
}

if (count($result) > 0){
$sf =ROOT_PATH."cache/toru_session.txt"; 
@unlink($sf);
$fpsf=fopen($sf,"a+"); 
fputs($fpsf,implode("\n",$result)); 
@fclose($fpsf);
//echo "<script>setTimeout('document.location.href=\"parser_t.php\"', $nu);</script>";
//print_r($result); 
echo("Авторизация успешна....<i>Обновлен файл сессий</i> <script>setTimeout('document.location.href=\"parser_t.php\"', 5000);</script><br>");
}


echo $date;
?>