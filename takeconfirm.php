<? 
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();

$id = (int) $_GET["id"];
if (!is_valid_id($id))
	stderr("������", "� ��� ����� ����� �� ������...");
	
if (isset($_POST["conusr"])){
	
foreach ($_POST["conusr"] as $conusr)
{
$conu=(int)$conusr;

if (!empty($conu))
sql_query("UPDATE users SET status = 'confirmed' WHERE id=".sqlesc($conu)." AND status = 'pending'".( get_user_class() < UC_SYSOP ? " AND invitedby = $CURUSER[id]" : "")) or sqlerr(__FILE__,__LINE__);
}

}

else
	header("Location: invite.php?id=$id");

header("Refresh: 0; url=invite.php?id=$id");
?>