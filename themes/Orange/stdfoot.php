<? show_blocks('d') ?>
<? show_blocks('f') ?>

<td valign="top" width="155">
<?
	show_blocks('r');
?>

</td>

<?
$seconds = (timer() - $tstart);
$phptime = 	$seconds - $querytime;
$query_time = $querytime;
$percentphp = number_format(($phptime/$seconds) * 100, 2);
$percentsql = number_format(($query_time/$seconds) * 100, 2);
$seconds = 	substr($seconds, 0, 8);
$memory = round(memory_get_usage()/1024);
$time_sql=sprintf("%0.4lf",$querytime);

 if($CURUSER) (  $queries_staff =("<br><b>$queries</b> (queries) - <b>$percentphp%</b> (php) - <b>$percentsql%</b> ($time_sql => sql) ".((get_user_class() == UC_SYSOP && $memory) == "yes" ? " - $memory КБ (use memory)" : "")."")  
 );

	print("</td></tr></table>\n");
	print("<table id=\"footer\" width=\"100%\"  border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#EBEBEB\">
      <tr align=\"left\" valign=\"top\">
       
        <td width=\"100%\" align=\"center\" valign=\"middle\"><div align=\"center\">
               
                <p align=\"center\"><font class=\"small\">".VERSION.TBVERSION."<br><span class=\"footer\">
				Страничка сформирована за <b>$seconds</b> секунд.$queries2 $queries_staff</span></font></p>
    </table>\n");
	print("</body></html>\n");
?>