<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

if (get_user_class() < UC_ADMINISTRATOR)
stderr($tracker_lang['error'], $tracker_lang['access_denied']);


if($_POST){
extract($_POST);
//stdhead("�������� � ������������� - ������ � ���� ��������������");

sql_query("UPDATE users SET downloaded = ".sqlesc($gigs)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__,__LINE__); 


if (!$s){
   @header("Location: $DEFAULTBASEURL/downcheck.php?page=$page&u=$u&doid=$userid&do=yes#$userid");
   } else {
   @header("Location: $DEFAULTBASEURL/downcheck.php?search=$userid&doid=$userid&do=yes");
   }
   die;
}


stdhead("�������� � ������������� ������� �������������");


$allclass = $_GET["u"];
if (!$allclass){
$where = " WHERE class < ".UC_VIP;
$all = "0";
} else {
$where = "";
$all = "1";
}

$search_id = (int) $_GET["search_id"];
$search_name = htmlspecialchars($_GET["search_name"]);

$ss = 0;

if ($search_id) {
$where = " WHERE id = ".$search_id;
$ss = 1;
}

if ($search_name) {
$where = " WHERE username LIKE '".sqlwildcardesc($search_name)."%'";
$ss = 1;
}

$count = get_row_count("users", "".$where."");

if (!$count){ 
print("<div class='error'><b>��� ������������� � ����� ������� ��� ������</b>!<br><br>
       <a href='downcheck.php'>��������� �� �������� ��������</a></div>"); 
stdfoot(); 
die; 
} 

$perpage = 50; // ��  �������� 
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?u=".$all."&amp;" ); 

// �������� ������ �� ���� ������������� 
$res = sql_query("SELECT id, username, class, downloaded, uploaded, modcomment FROM users ".$where." ORDER BY id ASC $limit") or sqlerr(__FILE__, __LINE__); 

print('<div align="center"><table border="0" cellspacing="0" width="100%" cellpadding="4">'); 
print ("<tr><td colspan='8' class='colhead'>�������� � ������������� ������� �������������</td></tr>");

// ����� ������������
print ('<tr><td colspan="8" align="center">
        <form method="GET" action="'.$PHP_SELF.'">
        <b>����� �� ������ ������������</b>:&nbsp;<input type="text" name="search_id" size="10" value="'.$search_id.'">&nbsp;<br />
        <b>����� �� ����� ������������</b>:&nbsp;<input type="text" name="search_name" size="10" maxlength="40" value="'.$search_name.'">&nbsp;<br /><input type="submit" value="�����">
        </form></td></tr>');

print ("<tr><td colspan='8'>"); 

print $pagertop; 

if (!$search){
   print ('<div style="float:right"> 
           <form method="GET" action="'.$PHP_SELF.'"> 
           <b>��� ������</b>: <input type="checkbox" name="u" value="yes" '.(($all) ? "checked" : "").'><input type="submit" value="��"> 
           </form> 
           </div>'); 
} else {
   print ('<div style="float:right"> 
           <a href="downcheck.php">�� �������� ��������</a> 
           </div>');
}

print ("</td></tr>"); 

print('<tr> 
       <td align="center" class="colhead">������������</td> 

       <td align="center" class="colhead"><img height="13" src="pic/ratioall.gif" width="13" border="0" alt="�������"></td> 
       <td align="center" class="colhead"><img border="0" src="pic/megs.gif" width="13" height="12" alt="������ ����������" hspace="4">��</td> 
       <td align="center" class="colhead"><img border="0" src="pic/arrowdown.gif" width="14" height="13" alt="������ �� �������" hspace="4">��</td> 
       <td align="center" class="colhead"><img border="0" src="pic/downtorr.gif" width="13" height="13" alt="������ �� ���������" hspace="4">��</td> 
       <td align="center" class="colhead"><img border="0" src="pic/warned13.gif" width="13" height="13" alt="�������" hspace="4">��</td> 
       <td align="center" class="colhead"><img border="0" src="pic/blocked13.gif" width="13" height="13" alt="����������������" hspace="4"></td> 
       </tr>'); 

$ccolor = 0; // ��������� �������� �������� ���� 

while ($arr = mysql_fetch_array($res)) {

if (!$arr){ 
print("<tr><td colspan='8'><div class='error'><b>��� �������������</b>!</div></td></tr></table>"); 
stdfoot(); 
die; 
} 
   // ������ ����� ���� 
   if (!$ccolor){ 
   $color = "#FFFFFF'"; 
   $ccolor = 1; 
   } else { 
   $color = "#F3F3F3"; 
   $ccolor = 0; 
   }

$uid = $arr['id']; // ���� ������������ 
$uhistory = $arr['modcomment']; // ������� ������������ 
$summ = 0; 

//******************************************������� Softovic'a:***********************************************//
// �������� ��������� �������� 
/*$r = mysql_query("SELECT torrent AS tid FROM snatched WHERE finished='yes' AND userid = $uid") or sqlerr(__FILE__,__LINE__); 

// ���� ����������� 
if (mysql_num_rows($r) > 0) { 
$user_completed = mysql_num_rows($r); // ��������� ��������� 
      
     // ������� ����� �� ���� ��������� ��������� 
     while ($size = mysql_fetch_array($r)) {            
     $restorr = mysql_query("SELECT size FROM torrents WHERE id = ".$size["tid"]) or sqlerr(__FILE__,__LINE__); 
     $storr = mysql_fetch_array($restorr); 
     $summ = $summ + $storr["size"]; // �������� ����� � ������ 
     }
*/
//******************************************������� Softovic'a:***********************************************//

//******************************************��������� ���:***********************************************//
// �������� ��������� �������� 
$r = sql_query("SELECT s.torrent AS tid, t.size FROM snatched AS s INNER JOIN torrents AS t ON t.id = s.torrent WHERE s.finished='yes' AND s.userid = $uid AND t.free = 'no'") or sqlerr(__FILE__,__LINE__); 

// ���� �����������
if (mysql_num_rows($r) > 0) {
    $user_completed = mysql_num_rows($r); // ��������� ��������� 
    // ������� ����� �� ���� ��������� ���������
    while ($size = mysql_fetch_array($r)) {
        $summ = $summ + $size["size"]; // �������� ����� � ������ 
    }
//******************************************��������� ���:***********************************************//

$allsumm = number_format($summ /1024/1024/1024, 2); // ������������� ����� � ���������� 
} else {
$user_completed = "--"; 
$allsumm = "--"; 
}

// ��������� ������ �� ������� ����� 
$user = "<a target='_blank' href=userdetails.php?id=".$arr['id'].">".get_user_class_color($arr["class"], $arr["username"])."</a>";

$downloaded = $arr['downloaded']; // ������� 
$uploaded = $arr['uploaded']; // ��������� 
$downloadedgb = number_format($downloaded /1024/1024/1024, 2); // ������� � �� 

// ��������� ������� 
if ($downloaded){
$ratio = $uploaded / $downloaded; 
$uratio = number_format($ratio, 2); 
$uratio = "<font color=\"".get_ratio_color($uratio)."\">".$uratio."</font>"; 
} else {
$uratio = "inf"; 
}

//������������ ������� � ����������, ��������� � �� � ���������
$what =  number_format(($summ - $downloaded)/1024/1024/1024, 2); // ������� = �������� ���� �������� ��������� ��������� ����� �������� ���������� � �������  

// ������� �������: 
//$what =  $allsumm - $downloadedgb; // ������� = �������� ���� �������� ��������� ��������� ����� �������� ���������� � ������� 

// ������ ���� ������� 
if ($what < 1){ 
$wcolor = ""; 
} elseif ($what >= 1 and $what < 3){ 
$wcolor = 'style="background-color: #FF9933; color:white;"'; 
} elseif ($what >= 3 and $what < 5){ 
$wcolor = 'style="background-color: red; color:white;"'; 
} else { 
$wcolor = 'style="background-color: #CC3300; color:white;"'; 
} 

//$userhistory = "<textarea cols=70 rows=8 readonly>".htmlspecialchars($uhistory)."</textarea>"; // ���� ������� 

$page = (int)$_GET["page"]; // �������� ����� �������� 
$do = $_GET["do"]; // ���������? 
$doid = (int)$_GET["doid"]; // �������� ���� ����� 

// ������������ ������ �����, �������� ���������������� 
if ($do == "yes" and $doid == $uid){ 
$color = "#FFFFB3"; 
} 

// ������� ������� ������������ 
print('<tr style="background-color: '.$color.'"> 
       <td align="center"><a name="'.$uid.'">'.$user.'</a></td> 
       <td align="center"><b>'.$uratio.'</b></td> 
       <td align="center"><b>'.$user_completed.'</b></td> 
       <td align="center"><b>'.$downloadedgb.'</b></td> 
       <td align="center"><b>'.$allsumm.'</b></td> 
       <td align="center" '.$wcolor.'><b>'.$what.'</b></td>'); 
 if ($summ and $downloadedgb !== $allsumm){ 
   print('<td align="center"> 
          <form method="POST" action="downcheck.php"> 
          <input border="0" src="pic/next.png" title="����������������" name="go" width="23" height="17" type="image" onClick="return confirm(\'�� �������?\')"> 
          <input type="hidden" name="page" value="'.$page.'"> 
          <input type="hidden" name="u" value="'.$allclass.'"> 
          <input type="hidden" name="userid" value="'.$uid.'"> 
          <input type="hidden" name="gigs" value="'.$summ.'"> 
          <input type="hidden" name="s" value="'.$ss.'">
          </form></td>'); 
 } else { 
   print('<td align="center"> 
          <img border="0" src="pic/delete.gif" title="����������" width="16" height="16"> 
          </td>');  
 }          
print('</tr>'); 

}

print ("<tr><td colspan='8'>".$pagerbottom."</td></tr>"); 
print ('</table></div>'); 

stdfoot(); 
?> 