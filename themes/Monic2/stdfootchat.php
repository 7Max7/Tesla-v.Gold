<? show_blocks('d') ?>
<? show_blocks('f') ?>
</td> 
    <td background="./themes/Monic2/images/n_r.gif">&nbsp;</td></tr>
	<tr>
    <td width="16"><img src="./themes/Monic2/images/n_f_l.gif" width="16" height="14"></td>
    <td background="./themes/Monic2/images/n_f_c.gif"><img src="./themes/Monic2/images/spacer.gif" width="1" height="14"></td>
    <td width="16"><img src="./themes/Monic2/images/n_f_r.gif" width="16" height="14"></td></tr>
	
	</table>
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
	print("<table class=\"bottom\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr valign=\"top\">\n");
	print("<td width=\"49%\" class=\"btitle\"><div align=\"center\">".VERSION.TBVERSION."<br>Страничка сформирована за <b>$seconds</b> секунд.\n$queries_staff</font></b></div></td>\n");
	print("</tr></table>\n");
	print("</body></html>\n");
?>