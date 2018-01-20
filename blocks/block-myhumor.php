<? if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}
$blocktitle = "Случайный Анекдот";
?>

<script>
function hum_online() {
jQuery.post("block-myhumor_jquery.php" , {} , function(response) {
		jQuery("#hum_online").html(response);
	}, "html");
setTimeout("hum_online();", 90000);
}
hum_online();
</script>
<?
$content.='<div align="center" id="hum_online">Загрузка, секундочку.</div>';
?>