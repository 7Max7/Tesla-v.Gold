<? 
if (!defined('BLOCK_FILE')) { 
    header("Location: ../index.php"); 
    exit; 
} 





global $tracker_lang; 

$today_word = "Сегодня"; 

list($today, $mounth, $year) = explode("-", date("j-m-Y")); 

$day = mktime(0, 0, 0, $mounth, 1, $year); 
$dayofweek = date("w", $day); 
$back = ($dayofweek + 6) % 7; 
$day -= 86400 * $back; 
$day_tmp = $day; 

$mounth_name = date ("F"); 
if (array_key_exists ('my_months_'.strtolower ($mounth_name), $tracker_lang)) 
{ 
    $mounth_name = @$tracker_lang['my_months_'.strtolower ($mounth_name)]; 
} 

$content = '<table align="center" border="0" cellpadding="3" cellspacing="0" class="clborder" width="100%"> 
<thead><tr> 
    <td colspan="7" style="border-bottom: 0px"><strong>» '.$mounth_name.date (" Y").'</strong></td> 
</tr><tr>'; 

for ($i=0; $i<7; $i++) 
{ 
    $content .= '<td class="colhead">'.substr (date ('D', $day_tmp), 0, 1).'</td>'; 
    $day_tmp += 86400; 
} 

$content .= '</tr></thead> 
<tbody>'; 

while (true) 
{ 
    $content .= '<tr>'; 
    for ($i=0; $i<7; $i++) 
    { 
        $date = date('j', $day); 
        $mounth_tmp = date("m", $day); 
        $content .= '<td'; 

        if ($today == $date) 
            $content .= ' class="cl3" title="'.$today_word.'"'; 
        elseif ($mounth != $mounth_tmp) 
            $content .= ' class="cl2"'; 
        else 
            $content .= ' class="cl1"'; 

        $content .= '>'.$date.'</td>'; 
        $day += 86400; 
    } 
    $content .= '</tr>'; 
     
    if ($mounth != $mounth_tmp) 
        break; 
} 
$content .= '</tbody></table>'; 
?> 