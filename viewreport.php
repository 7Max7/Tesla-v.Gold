<? 
require_once("include/bittorrent.php"); 
dbconn(); 
loggedinorreturn(); 

if (get_user_class() < UC_ADMINISTRATOR){
    stderr("Ошибка", "У Вас нет прав для просмотра этой страницы."); 
    }

//Удалить все жалобы 
if ($_POST['deleteall'] && get_user_class() == UC_SYSOP) {
accessadministration();
sql_query("TRUNCATE TABLE report") or sqlerr(__FILE__,__LINE__); 
  header("Location: viewreport.php");
die;
}
//


//Удалить выбранные жалобы 
if ($_POST['delete'] && $_POST['reports'] && get_user_class() <= UC_SYSOP) {
   $reports = $_POST['reports']; 

   foreach ($reports as $id) {
   sql_query("DELETE FROM report WHERE id=" . sqlesc((int) $id)) or sqlerr(__FILE__,__LINE__); 
   }
   header("Location: viewreport.php");
die;
}
// 

stdhead("Просмотр жалоб на раздачи"); 

$res = sql_query("SELECT COUNT(id) FROM report ORDER BY added DESC");
$row = mysql_fetch_array($res);
$count = $row[0];
$perpage = 25;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "viewreport.php?");

//$count = get_row_count("report"); 
if (!$count) {
$empty = 0; 
} else {
$empty = 1; 
}

?> 
        <script language="Javascript" type="text/javascript"> 
        <!-- Begin 
        var checkflag = "false"; 
        var marked_row = new Array; 
        function check(field) { 
                if (checkflag == "false") { 
                        for (i = 0; i < field.length; i++) { 
                                field[i].checked = true;} 
                                checkflag = "true"; 
                        } 
                else { 
                        for (i = 0; i < field.length; i++) { 
                                field[i].checked = false; } 
                                checkflag = "false"; 
                        } 
                } 
                //  End --> 
        </script> 

<?
if (!empty($empty)){
print("<form action=\"viewreport.php\" method=\"post\" name=\"form1\"> ");
}

print($pagertop);
?>

  <table border="0" cellspacing="0" width="100%" cellpadding="3"> 
    <tr> 
      <td class=colhead><center>id&nbsp;</center></td> 
       <td class=colhead><center>Дата&nbsp;поступления</center></td> 
      <td class=colhead><center>Жалоба&nbsp;от</center></td> 
      <td class=colhead><center>Жалоба&nbsp;на&nbsp;</center></td> 
      <td class=colhead><center>Причина&nbsp;жалобы</center></td> 
      <? if (!empty($empty)){ 
      	print(" <td class=colhead><center><INPUT type=\"checkbox\" title=\"Выбрать все\" value=\"Выбрать все\" onClick=\"this.value=check(document.form1.elements);\"></center></td> ");
	  } ?>
    </tr> 

<?

if ($empty){

   $res = sql_query("SELECT r.*, u1.class AS class_u1,u1.username AS username_u1, 
   u2.class AS class_u2,u2.username AS username_u2, t.name
   FROM report AS r 
   LEFT JOIN users AS u1 ON u1.id = r.userid
   LEFT JOIN users AS u2 ON u2.id = r.usertid
   LEFT JOIN torrents AS t ON t.id = r.torrentid
   
   ORDER BY r.added DESC $limit") or sqlerr(__FILE__, __LINE__); 
   while ($row = mysql_fetch_array($res)) {

   $reportid = $row["id"]; 
   $torrentid = $row["torrentid"]; 
   $userid = $row["userid"]; 
   $motive = $row["motive"]; 
   $added = normaltime($row["added"],true);  

  // $res1 = sql_query("SELECT username, class FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__); 
  // $row1 = mysql_fetch_array($res1); 

   $username = $row["username_u1"]; 
   $userclass = $row["class_u1"]; 

   if ($username == ""){ 
   $username = "<b><font color='red'>Аноним<font></b>"; 
   }

   //$res2 = sql_query("SELECT id, name FROM torrents WHERE id = $torrentid") or sqlerr(__FILE__, __LINE__); 
  // $row2 = mysql_fetch_array($res2); 

   if ($row["id"] && $row["name"] && empty($row["username_u2"])){
      $torrentname = $row["name"]; 
      $torrenturl = "<a href='details.php?id=$torrentid'>$torrentname</a>"; 
      } elseif ($row["id"] && empty($row["name"]) && empty($row["username_u2"])) {
      $torrenturl = "<b><font color='red'>торрент удален<font></b>"; 
      }
         
	  if ($row["id"] && empty($row["name"]) && !empty($row["username_u2"])){
      $username_u2 = $row["username_u2"]; 
       $class_u2 = $row["class_u2"]; 
      $torrenturl = "<b><a href='userdetails.php?id=".$row["usertid"]."'>".get_user_class_color($class_u2,  $username_u2)."</a></b>"; 
      } elseif ($row["id"] && empty($row["name"]) && !empty($row["username_u2"])) {
      $torrenturl = "<b><font color='red'>пользователь не найден<font></b>"; 
      }
      
      


      print ("<tr> 
        <td align='center'>$reportid</td> 
        <td align='center'>$added</td> 
        <td align='center'><b><a href='userdetails.php?id=$userid'>".get_user_class_color($userclass, $username)."</a></b></td> 
        <td align='center'>$torrenturl</td> 
        <td align='center'>$motive</td> 
        <td align='center'> <INPUT type=\"checkbox\" name=\"reports[]\" title=\"Выбрать\" value=\"".$reportid."\" id=\"checkbox_tbl_".$reportid."\"></td></tr>"); 

   }

}
else 
{ 
print("<tr><td align='center' colspan='6'>Нет ни одной жалобы на раздачи...</td></tr>"); 
} 

?>

<tr> 
<td class=colhead colspan="6">
<? if (!empty($empty)){
print("<div align=right> 
<input type=\"submit\" name=\"delete\" value=\"Удалить выбранное\" onClick=\"return confirm('Вы уверены?')\"> 
</div>");} ?>
</td> 
</tr>
</table>

<? if (!empty($empty)){
print("</form>$pagerbottom");} 

if (!empty($empty) && get_user_class() == UC_SYSOP){
print("<span style=\" ".($CURUSER["stylesheet"]=="black_night" ? "":"BACKGROUND-COLOR: #efedef;")." BORDER:silver 1px solid; DISPLAY:block; COLOR:#00f; MARGIN:2px 1px;  PADDING:2px 2px 2px 6px;  TEXT-DECORATION:none;\"><form action=\"viewreport.php\" method=\"post\"><input type=\"hidden\" name=\"deleteall\" value=\"deleteall\"><input type=\"submit\" value=\"Удалить все жалобы с базы\" onClick=\"return confirm('Вы уверены?')\"></form></span>");
}
?>

<? 
stdfoot(); 

?> 

