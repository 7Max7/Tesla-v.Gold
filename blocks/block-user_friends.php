<?
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

?>

<script>
function friends_online() {
jQuery.post("block-ufriends_jquery.php",{}, function(response) {
		jQuery("#friends_online").html(response);
	}, "html");

setTimeout("friends_online();", 60000);
}
friends_online();
</script>
<?

$content="<span align=\"center\" id=\"friends_online\">Загрузка вашего списка друзей</span>";

/////////////////////

$blocktitle = "Ваши Друзья";
?>