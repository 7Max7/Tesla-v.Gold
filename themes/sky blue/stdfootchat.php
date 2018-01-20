<? show_blocks('d'); ?>
<? show_blocks('f');?>



</td>
</tr></table>
<div align="center"><img src="/themes/<?=$ss_uri;?>/images/pageEnd.gif" width="925" height="100"/></div>


<?
global $use_gzip;
$gzip=($use_gzip=="yes" && !empty($CURUSER) ? " (gzip ".ini_get('zlib.output_compression_level').")":".");
$seconds = (timer() - $tstart);
$phptime = 	$seconds - $querytime;
$query_time = $querytime;
$percentphp = number_format(($phptime/$seconds) * 100, 2);
$percentsql = number_format(($query_time/$seconds) * 100, 2);
$seconds = 	substr($seconds, 0, 5);
//$memory = round(@memory_get_usage()/1024);
$memory = mksize(round(@memory_get_usage()));
$time_sql=sprintf("%0.4lf",$querytime);
$time_php=sprintf("%0.4lf",$phptime);


 if($CURUSER) (  $queries_staff =("<br><b>$queries</b> (queries) - <b>$percentphp%</b> ($time_php => php) - <b>$percentsql%</b> ($time_sql => sql) ".((get_user_class() == UC_SYSOP && $memory) == "yes" ? " - $memory (use memory)" : "")."")  
 );

print("<table class=\"bottom\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr valign=\"top\">
<td width=\"49%\" class=\"bottom\"><div align=\"center\" class=\"pageEnd\"><b>".VERSION.TBVERSION."<font color=\"#D4D0C8\"><br> Страничка сформирована за <b>$seconds</b> секунд$gzip</font> $queries_staff<div></div>");
print("	</b></div></td></tr></table></body></html>\n");
/*
$sf ="cache/txt_time_for_stdfoot.txt"; 
$fpsf=@fopen($sf,"a+"); 
@fputs($fpsf,basename($_SERVER['SCRIPT_FILENAME']).":".$seconds." (".$time_php."+".$time_sql.")\n"); 
@fclose($fpsf); 
*/
?>