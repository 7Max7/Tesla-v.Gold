<?php
    // TopTracker.Ru - Скрипт для движков аля TBDev
    // Данный пример разработан для начисление поощрительного бонуса трекерам за оценку вашего трекера на toptracker.ru
    // Для начала вам нужно положить этот скрипт в корень трекера
    // Далее создать в базе mysql новое поле в таблице users где будет учитыватся время голосования ваших юзеров, пример ниже
    // ALTER TABLE users ADD `toptracker` date NOT NULL default '0000-00-00';
    // В ссылку на голосование добавить ID вашего юзера, пример (http://www.toptracker.ru/details.php?id=xxx&userid=xxx) где userid ID вашего юзера
    // Насчет ддос атак на наш сайт вашему сайту это не грозит, сначала идет проверка унас, если все данные сходятся поссылает пост данные на ваш трекер
    // Если будут какие проблемы с данным скриптом, пишите в форму контакта http://www.toptracker.ru/contact.php

	// Проверка на реферер
	// Вместо ххх введите ID своего трекера и прибавьте значение к 36 и 40, к примеру если ваш ид однозначный (37,41) двузначный (38,42) трехзначный (39,43)
	
	/*
	if( substr($_SERVER['HTTP_REFERER'], 0 , 39) != "http://toptracker.ru/details.php?id=219" && substr($_SERVER['HTTP_REFERER'], 0 , 43) != "http://www.toptracker.ru/details.php?id=219")
		die();
*/


$key=$_POST["key"]; 
$ratio = intval($_POST["ratio"]);
$ip = htmlentities($_POST["ip"]);
$userid = intval($_POST["userid"]);
$ref=htmlentities($_SERVER['HTTP_REFERER']);

$sf ="cache/toptracker.txt"; 
$fpsf=@fopen($sf,"a+"); 

@fputs($fpsf,"key:$key ratio:$ratio ip:$ip userid:$userid ref:$ref\n"); 
@fclose($fpsf); 








    // Дополнительная проверка по уникальному ключу, генерируем ключ в админке и вставляем в кавычки
//	if($_POST["key"] != "0RamuF5dydTtEmNwV447fjZ07EWmAu")
//		die();

	// Вознаграждение в мегабайтах
	$MB = 300;
	$MB = 1024*1024*$MB;

	// Возногражение в seedbonus
	$bonus = 5;

    // Проверяем на сущестование пост данных
	if(isset($_POST['userid']) && isset($_POST["ip"]) && isset($_POST['ratio']) && isset($_POST['key'])) {

		$ratio = intval($_POST["ratio"]);
		$ip = htmlentities($_POST["ip"]);
    	$userid = intval($_POST["userid"]);

    	// Если оценка за ваш трекер 5 начисляем бонус пользователю, меняется по вашему усмотрению
		if($ratio == '5') {

			require_once("include/bittorrent.php");
        	dbconn(false);

        	// В моем примере начисляется бонус seedbonus, проверка идет по ID и IP юзера, (INTERVAL 2 HOUR) как вы заметили на топтрекере время GMT+1
        	// Вы можете легко сменить бонус на что вам угодоно к примеру рейтинг, uploaded = uploaded + '".$MB."'
			sql_query("UPDATE users SET toptracker = CURDATE()+INTERVAL 2 HOUR, uploaded = uploaded + '".$MB."' WHERE id = ".sqlesc($userid)." AND ip = ".sqlesc($ip)." AND toptracker < CURDATE()")  or sqlerr(__FILE__,__LINE__);
			
			 
			 if (@mysql_affected_rows()){

			 $msg = sqlesc("Спасибо, что проголосовали за сайт (оценка 5), вам было добавлено 300 мегабайт к отдаче.!\n");
			 $dt = sqlesc(get_date_time(gmtime()));
			 
			 $subject = sqlesc("Оценка 5 на TopTracker");
			 
            sql_query("INSERT INTO messages (sender, receiver, added, msg, poster,subject) VALUES(0,$userid, $dt, $msg, 0,$subject)") or sqlerr(__FILE__, __LINE__);
            }
		}
	}

	exit();

?>