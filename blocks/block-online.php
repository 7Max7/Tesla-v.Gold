<?
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

?>

<script>
function index_online() {
jQuery.post("block-online_jquery.php" , {} , function(response) {
		jQuery("#index_online").html(response);
	}, "html");
setTimeout("index_online();", 90000);
}
index_online();
</script>
<?

$content= '<div align="center" id="index_online">Загрузка списка кто в сети</div>';

?>