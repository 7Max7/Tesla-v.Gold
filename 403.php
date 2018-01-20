<? 
include_once("include/bittorrent.php");
dbconn(false,true);
stdheadchat("Error 403 Доступ запрещен"); 
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
<p align="center" class="style5">Доступ запрещен</p>
<p align="center" class="style4">Ваш IP-адрес заблокирован за нарушение правил трекера.</p>
<p align="center" class="style6">Привед Конкурентам!</p>
</body>


<? stdfootchat();?>