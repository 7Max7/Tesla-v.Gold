<? 
include_once("include/bittorrent.php");
dbconn(false,true);
stdheadchat("Error 500 (Internal Server Error)"); 
?>
<style type="text/css">
<!--
.style3 {
	font-size: 36px;
	font-family: "Times New Roman", Times, serif;
}
.style4 {
	font-family: "Times New Roman", Times, serif;
	font-size: 16px;
	color: #999999;
}
.style5 {
	font-size: 48px;
	font-family: "Times New Roman", Times, serif;
	color: #006699;
}
.style6 {
	font-size: 28px;
	font-family: "Times New Roman", Times, serif;
	color: #FFFFFF;
}
-->
</style>

<body>
<p align="center" class="style5">Ошибка 500</p>
<p align="center" class="style4">
При обработке запроса на сервере один из его компонентов (например, CGI-программа) выдал аварийный отказ или столкнулся с ошибкой конфигурации</p>

<p align="center" class="style6">Привед Конкурентам!</p>

</body>

<? stdfootchat();?>