<?
require_once("include/bittorrent.php");

dbconn(false);
loggedinorreturn();
parked();

$type = (int) $_GET["type"];

$res_cat = sql_query("SELECT name FROM categories WHERE id=".sqlesc($type)." LIMIT 1") or sqlerr(__FILE__, __LINE__);

$arr_cat = mysql_fetch_assoc($res_cat);
$cat_name = htmlspecialchars($arr_cat["name"]);

stdhead($tracker_lang['upload_torrent']." � ��������� ".$cat_name);

if (get_user_class() < UC_UPLOADER AND ($CURUSER["class"] <>UC_VIP)){
  stdmsg("������ ����", "��� ������� ����� �������� � ��� ������ ����.");
  stdfoot();
  exit;
}

if (!isset($type) || empty($type)){
  stdmsg($tracker_lang['error'], "�� ������� ��������� [����� 5 ������ ���������� �����]");
  echo "<script>setTimeout('document.location.href=\"upload.php\"', 5000);</script>";
  stdfoot();
  exit;
}

if ($CURUSER["uploadpos"] == 'no'){
stdmsg("��������", "��� ���� ��������� ���������� ��������.");
stdfoot();
exit;
}

if (strlen($CURUSER['passkey']) <> 32) {
$CURUSER['passkey'] = md5($CURUSER['username'].get_date_time().$CURUSER['passhash']);
sql_query("UPDATE users SET passkey='$CURUSER[passkey]' WHERE id=$CURUSER[id]") or sqlerr(__FILE__, __LINE__);
}
?>

<form name="upload" enctype="multipart/form-data" action="takeupload.php" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$max_torrent_size?>" />
<table class="main" border="1" cellspacing="0" width="100%" cellpadding="5">
<tr><td class="colhead" colspan="2"><?=$tracker_lang['upload_torrent'] ;?> �� ���� <?=$SITENAME?> �  ��������� <?=$cat_name?>

<script type="text/javascript">
function changeText(text){
document.getElementById('descr').value = text;
}
</script>
</td></tr>
<?
if ($free_from_3GB==1){
print"<tr><td align=\"center\" width=\"99%\" colspan=\"2\" style=\"padding: 15px; font-weight: bold;\" class=\"row\">���� ������ ������� ����� ������ ������ ��� ����� 3 ����������, �� ��� �������� ��������� � ����������� ������ ���������� ���������, ���������� ���������� �� ����������� a.k.a ������� �������.</td></tr>"; 
}

print"<tr><td align=\"center\" width=\"99%\" colspan=\"2\" style=\"padding: 15px;\"><b>��������</b>: ���� �� ������� ������� ���� � ������� �������, � ������ ������ ����� �� ���� ����, �� ������ �������� ������� <b>�����</b> ������� ����, ������� �� <b>��� �������</b> (�� ����������� ��������� �����, � ������������� ����� ����� ������� ���� ���� �������������� - �������, ����� �� �� ��������� ������� �� ������ �������).</td></tr>"; 


tr("������-������ <font color=red>*</font>", "<label><input type=\"checkbox\" ".(!empty($name_link)? "checked":"")." name=\"multitr\" value=\"1\"><i>��������� / ��������� ����������� ������� ����� � �����.</i> </label><br>�������� ������ � ����������� torrent ����� (<b>������� ����</b>)", 1);

//tr($tracker_lang['announce_url'], $announce_urls[0], 1);
tr($tracker_lang['torrent_file']." <font color=red>*</font>", "<input type=file name=tfile size=75>
<br>
������������ ������ .torrent �� ������ ��������� <b>".mksize($max_torrent_size)."</b><br>
����� �������� � �������� ����� .torrent ����� �������� � ������ ��� ����������\n", 1);

tr($tracker_lang['torrent_name'], "<input type=\"text\" name=\"name\" value=\"".$dtitle."\"size=\"73\" /><br />".$tracker_lang['taken_from_torrent']."\n", 1);

if (get_user_class() >= UC_MODERATOR){
tr("������", "<input type=\"text\" name=\"webseed\" size=\"73\" /><br />���� � �������, ���� ������  <b>http ������</b> ������� �� (������� �� ��������������).<br>
���� ������ ����� ������� �����������, ip ����� ������� ��������� ��������\n", 1);
}


tr("��������", "������������ ������ �����������: <b>".mksize($maximagesize)."</b><br />".
$tracker_lang['avialable_formats'].": <b>gif, jpg, jpeg, png</b>&nbsp&nbsp<br>
<input type=file name=image0 size=75><br /><b>", 1);

tr("�������� �1", "<input name=picture1 size=80 \"><br />",1);
tr("�������� �2", "<input name=picture2 size=80 \"><br />",1);
tr("�������� �3", "<input name=picture3 size=80 \"><br />",1);
tr("�������� �4", "<input name=picture4 size=80 \"><br />",1);  


?>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("span.spoiler").hide();
    $('<a class="reveal"><input type=button value="������� �����, ��� ������ ��������������� ���� ����������" style="height: 25px; width: 400px"></form></a>').insertBefore('.spoiler');
    $("a.reveal").click(function(){
    $(this).parents("p").children("span.spoiler").fadeIn(2500);
    $(this).parents("p").children("a.reveal").fadeOut(600);
    });
});
</script>
<?

tr("���������", "<p><span class=\"spoiler\">
<textarea name=array_picture cols=77 rows=3></textarea>
<span title=\"�������� ���� ��� ��������� �������\" style=\"cursor: pointer;\" onclick=\"javascript: show_hide('ss1234321')\"><br><b>�� ������</b>: ����� ������ ����� ���� ��������� ������ �� ������ �����������, ��� �����, ��� ������������� ������������� � ������ (<b>max 4</b>) �������� �1, �������� �2 � ��</span>
<span id=\"sss1234321\" style=\"display: none;\">
<b>������ ����� ������</b>: <br>
<textarea name=test cols=77 rows=4 readonly>
http://img40.imageshack.us/img40/3423/2dd421a60c145905380.jpg
http://img444.imageshack.us/img444/8184/53e6966b2c245905367.jpg
http://img641.imageshack.us/img641/7958/c4962441e5225905355.jpg
http://img153.imageshack.us/img153/5020/e9fa34b43b3f5905342.jpg
</textarea></span></span>
</span></p>",1);


// ��� �������� ������ 2009 ����
$video_ = "[b]��������[/b]: ������� �������� / ���������� (��� �������) ��������\\n\\n[u]���������� � ������[/u]\\n[b]��������: [/b]\\n[b]������������ ��������: [/b]\\n[b]��� ������: [/b]\\n [b]����: [/b]\\n[b]��������: [/b]\\n[b]� �����: [/b]\\n\\n[b]� ������: [/b]\\n[b]��������: [/b]\\n\\n[b]�����������������: [/b]\\n[b]�����������: [/b]\\n\\n[u]�����[/u]\\n[b]������: [/b]\\n[b]��������: [/b]\\n[b]�����: [/b]\\n[b]�����: [/b]"; 

$audio_ = "[b]��������[/b]: ����������� - ������ (��� �������) ������ �����\\n\\n[u]���������� � ������: [/u]\\n[b]�����������: [/b]\\n[b]�������� �������: [/b]\\n[b]��� �������: [/b]\\n[b]����: [/b]\\n\\n[b]��������: [/b]\\n[u]�����[/u]\\n[b]����� ��������: [/b]\\n[b]������: [/b]\\n[b]��������: [/b]"; 

$game_ = "[b]� ��������[/b]: ������� �������� / ���������� (��� �������)\\n\\n[u]���������� �� ����[/u]\\n[b]�����������: [/b]\\n[b]��������: [/b]\\n[b]��� ������: [/b]\\n[b]���� ����: [/b] - �� ������� !\\n[b]����: [/b]\\n[b]����: [/b]\\n\\n[b]�� ����: [/b]\\n\\n[b]����������� ����: [/b]\\n\\n[b]���.����������: [/b] - �� ������� !\\n\\n[b]������: [/b]\\n\\n[b]��������� ����������: [/b]\\n\\n"; 

$soft_ = "[b]��������[/b]: �������� (��� �������) ���� \\n\\n[u]���������� � ���������[/u]\\n[b]��������: [/b]\\n[b]��� ������: [/b]\\n[b]������: [/b]\\n[b]�����������: [/b]\\n[b]���������: [/b]\\n[b]��������: [/b]\\n[b]����: [/b]\\n\\n[b]� ���������: [/b]\\n\\n[b]���. ����������: [/b]\\n\\n[b]��������� ����������: [/b] - �� ������������ ������� !\\n\\n"; 

$image_ = "[b]��������[/b]: H������� (��� �������)\\n\\n[b]��� �������: [/b]\\n[b]������: [/b]\\n[b]������: [/b]\\n[b]����������: [/b]\\n\\n[b]������� ��������: [/b]\\n\\n"; 

$clip_ = "[b]��������[/b]: �������� (��� �������) ��������\\n\\n[u]���������� � ������[/u]\\n[b]�����������: [/b]\\n[b]�������� �������: [/b]\\n[b]��� �������: [/b]\\n[b]����: [/b]\\n\\n[b]��������[/b] - ����  ������� �� ������ ��� ������� !\\n[b]�����������������: [/b]\\n\\n[u]�����[/u]\\n[b]������: [/b]\\n[b]��������: [/b]\\n[b]�����: [/b]\\n[b]�����: [/b]\\n\\n"; 

$books_ = "[b]� ��������[/b]: ����� - �������� (��� �������)\\n\\n[b]�����: [/b]\\n[u]��������: [/u]\\n[b]������������ ��������: [/b]\\n[b]������������: [/b]\\n[b]��� ������� �����: [/b]\\n[b]����: [/b]\\n[b]����: [/b]\\n\\n[b]��������: [/b]\\n\\n[b]������: [/b]\\n[b]���������� �������: [/b]"; 

$serial_ = "[b]��������[/b]: ������� �������� / ���������� (�����) (��� �������) ��������\\n\\n[u]���������� � �������[/u]\\n[b]��������: [/b]\\n[b]������������ ��������: [/b]\\n[b]��� ������: [/b]\\n[b]����: [/b]\\n[b]��������: [/b]\\n[b]� �����: [/b]\\n\\n[b]� ������: [/b]\\n\\n[b]��������: [/b]\\n[b]�����������������: [/b]\\n[b]�����������: [/b]\\n\\n[u]�����[/u]\\n[b]������: [/b]\\n[b]��������: [/b]\\n[b]�����: [/b]\\n[b]�����: [/b]\\n\\n"; 

$w = 80;
$h = 20;

print("<tr><td class=rowhead style='padding: 3px'><center>�������� ".$tracker_lang['description']."<br><b>"); 

print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$video_."\")' value=�����>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$audio_."\")' value=�����>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$soft_."\")' value=����>\n");  
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$game_."\")' value=�������>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$image_."\")' value=��������>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$clip_."\")' value=�����>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$books_."\")' value=�����>\n"); 
print("<br><input style=\"width: ".$w."px; height: ".$h."px\" type=button onclick='changeText(\"".$serial_."\")' value=�������>\n");
print("</center></td><td>"); 
// ��� �������� ������ 2009 ���� 

textbbcode("upload","descr");
print("</td></tr>\n");

if(get_user_class() >= UC_MODERATOR) {
///// ��� ����� /////
?>
    <style type="text/css" media="screen">
        code {font:99.9%/1.2 consolas,'courier new',monospace;}
        #from a {margin:2px 2px;font-weight:normal;}
        #tags {width:36em;}
         a.selected {background:#1843f9; color:#e6e6e6; border: 1px #D1D8EC solid;}
        .addition {margint-top:2em; text-align:right;}
    </style>
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/tagto.js"></script>

    <script type="text/javascript">
        (function($){
            $(document).ready(function(){
                $("#from").tagTo("#tags");
            });
        })(jQuery);
    </script>

<?
$s = '<input type="text" id="tags" name="tags">';
$s.= '<div id="from">';
$tags = taggenrelist($type);
if (!$tags)
$s .= "��� ����� ��� ������ ���������. �� ������ �������� �����������, ����� �������.";
else  {
foreach ($tags as $row)
$s .= "<a title=\"" . htmlspecialchars($row["name"]) . " ����\" href='#'>" . htmlspecialchars($row["name"]) . "</a>\n";
}
$s .= "</div>��������� <b>���� �� ������ (������ �� �����)</b>, <b>�� ���������� ��������</b> ������ ��!!!\n";

tr("����", $s, 1);
///// ��� ����� /////
}


/*
$s = "<select name=\"type\">\n<option value=\"0\">�������� �� ������</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
tr("���������", $s, 1);
*/


if(get_user_class() >= UC_MODERATOR)
tr($tracker_lang['golden'], "<label><input type=checkbox name=free value=yes> ".$tracker_lang['golden_descr']."</label>", 1);

if (get_user_class() >= UC_ADMINISTRATOR)
tr("������", "<label><input type=\"checkbox\" name=\"sticky\" value=\"yes\">���������� ���� ������� (������ �������)</label>", 1);
    
//if (get_user_class() > UC_ADMINISTRATOR)
//tr("�� �������", "<label><input type=\"checkbox\" name=\"ontop\" value=\"yes\">���������� �� �������</label>", 1);


?>

<script type="text/javascript">
function changeText(text){
document.getElementById('area').value = text;
}
</script>

<tr><td align="center" colspan="2"><input type="hidden" name="type" value="<?=$type?>"><input type="submit" class=btn style="height: 35px; width: 450px;" value="<?=$tracker_lang['upload'];?>" /></td></tr>
</table>
</form>
<? stdfoot(); ?>