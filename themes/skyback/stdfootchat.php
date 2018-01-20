<? if (!defined('UC_SYSOP')) die(); ?>

<? show_blocks('d'); ?>
<? show_blocks('f');?>

<div class="clrfix"></div>
</div>


</div>






</div>


<div class="clr"></div></div>
</div></div>



<div class="footer"><div><div align="center">

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

print("<br>".VERSION.TBVERSION."<br> Страничка сформирована за <b>$seconds</b> секунд $gzip $queries_staff");

?>
</div></div>
</div>


</div>
</div>
</body>
</html>