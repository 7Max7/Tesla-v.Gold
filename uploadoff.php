<?
require_once("include/bittorrent.php");

dbconn(false);
loggedinorreturn();
parked();

function bark($msg) {
	genbark($msg, $tracker_lang['error']);
}
foreach(explode(":","descr:type:name") as $v) {
	if (isset($_POST[$v]))
{
$descr = unesc($_POST["descr"]);
if (!$descr)
	bark("�� ������ ������ ��������!");

$catid = ((int) + $_POST["type"]);
if (!is_valid_id($catid))
	bark("�� ������ ������� ���������, � ������� ��������� �������!");

$shortfname = $torrent = $matches[1];
if (!empty($_POST["name"]))
	$torrent = unesc($_POST["name"]);

$torrent = htmlspecialchars(str_replace("_", " ", $torrent));

$ret = sql_query("INSERT INTO off_reqs 
(owner, name, size, numfiles, descr, category, added) VALUES 
(" . implode(",", array_map("sqlesc", array($CURUSER["id"], $torrent, $totallen, count($filelist), $descr, (int)+$_POST["type"]))) . ", '" . get_date_time() . "')");
if (!$ret) {
	if (mysql_errno() == 1062)
		bark("����� ������ | ����������� ��� ���� �� �������!");
	bark("mysql puked: ".mysql_error());
}
$id = mysql_insert_id();
sql_query("INSERT INTO checkcomm (checkid, userid, torrent) VALUES ($id, $CURUSER[id], 1)") or sqlerr(__FILE__,__LINE__);


header("Location: $DEFAULTBASEURL/detailsoff.php?id=$id");
die;

}
}
stdhead("�������� ������� | �����������");


if ($CURUSER["class"] <UC_USER)
{
  stdmsg($tracker_lang['error'], $tracker_lang['access_denied']);
  stdfoot();
  exit;
}


?>

<form name="upload" enctype="multipart/form-data" action="uploadoff.php" method="post">

<table border="1" cellspacing="0" cellpadding="5">
<tr><td class="colhead" colspan="2"><a class="altlink_white" href=uploadoff.php>�������� ������� | ����������� �� ���� <?=$SITENAME?></a> :: <a class="altlink_white" href=detailsoff.php>������ ��������</a>
<script type="text/javascript">
function changeText(text){
document.getElementById('descr').value = text;
}
</script>
</td></tr>
<?


tr($tracker_lang['torrent_name'], "<input type=\"text\" name=\"name\" size=\"80\" /><br />\n", 1);


// description_mod 
$video_ = "[u]���������� � ������[/u]\\n[b]��������: [/b]\\n[b]������������ ��������: [/b]\\n[b]��� ������: [/b]\\n[b]����: [/b]\\n[b]��������: [/b]\\n[b]� �����: [/b]\\n\\n[b]� ������: [/b]\\n\\n[b]��������: [/b]\\n[b]�����������������: [/b]\\n[b]�������: [/b]\\n[b]��������: [/b]\\n[b]�������������: [/b]\\n\\n[u]����[/u]\\n[b]������: [/b]\\n[b]��������: [/b]\\n[b]�����: [/b]\\n[b]����: [/b]\\n[b]������: [/b]\\n"; 

$audio_ = "[b]�����������: [/b]\\n[b]������: [/b]\\n[b]��� ������: [/b]\\n[b]����: [/b]\\n\\n[b][u]��������:[/u][/b]\\n\\n\\n[b]����: [/b](�������, ������)\\n[b]�����������������: [/b]\\n"; 

$game_ = "[b]��������: [/b]\\n[b]��� ������: [/b]\\n[b]����: [/b]\\n[b]��������: [/b]\\n[b]����: [/b]\\n\\n[b]�� ����: [/b]\\n\\n\\n[b]����������� ����: [/b]\\n\\n\\n[b]��������� ����������: [/b]\\n\\n"; 

$soft_ = "[b]��������:[/b]\\n[b]��� �������:[/b]\\n[b]���������:[/b]\\n[b]���� ����������:[/b]\\n[b]���������:[/b]\\n[b]��������:[/b]\\n[b]���. ����������:[/b]\\n"; 

$w = 80; 
$h = 20; 

 print("<tr><td class=rowhead style='padding: 3px'><center>�������� ".$tracker_lang['description']."<br><b>"); 

print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$video_."\")' value=�����>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$audio_."\")' value=�����>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$soft_."\")' value=����>\n");  
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$game_."\")' value=�������>\n"); 

print("</center></td><td>"); 
// end description_mod  


textbbcode("upload","descr");
print("</td></tr>\n");

$s = "<select name=\"type\">\n<option value=\"0\">(".$tracker_lang['choose'].")</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
tr($tracker_lang['type'], $s, 1);

?>

<script type="text/javascript">
function changeText(text){
document.getElementById('area').value = text;
}
</script>

<tr><td align="center" colspan="2"><input type="submit" class=btn value="<?=$tracker_lang['upload'];?>" /></td></tr>
</table>
</form>
<?

stdfoot();

?>