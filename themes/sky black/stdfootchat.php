<? show_blocks('d'); ?>
<? show_blocks('f');?>



</td>
</tr></table>
<div align="center"><img src="/themes/<?=$ss_uri;?>/images/pageEnd.gif" width="925" height="100"/></div>

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
 

print("
<table class=\"bottom\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr valign=\"top\">
<td width=\"49%\" class=\"bottom\"><div align=\"center\" class=\"pageEnd\"><b>".VERSION.TBVERSION."<font color=\"#D4D0C8\"><br> Страничка сформирована за <b>$seconds</b> секунд.</font>$queries_staff<div></div>");
print("</b></div></td></tr></table></body></html>\n");
?>