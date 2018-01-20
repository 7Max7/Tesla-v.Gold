<? 
require "include/bittorrent.php";
dbconn(false,true);
stdheadchat("Обнаружен прокси"); 

global $CURUSER;

$ip_real=getip();
$poput="через прокси ".$ip_real;

if (!stristr($CURUSER["usercomment"],$poput)!==false && $CURUSER){
$usercomment = get_date_time() . " - вход через прокси ".$ip_real."\n". $CURUSER["usercomment"];
sql_query("UPDATE users SET usercomment='$usercomment' WHERE id=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
}
/// маловероятно конечно, но все же...

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
</head>

<body>
<p align="center" class="style5">Доступ через прокси запрещен</p>
<p align="center" class="style6">Привед Конкурентам!</p>

</body>
<? 
stdfootchat();
?>