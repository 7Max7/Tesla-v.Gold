<?
	show_blocks('d');
	

?>

</td>

<td width="170" valign="top" class="brd">
<?
	show_blocks('r');
?>
</td>


</tr></table>





</td></tr>

<tr>



<td class="footer">

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"><tr><td class="footer">
<br>
<?
	show_blocks('f');
?>

<div align="center">

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

print("	".VERSION.TBVERSION." <br>Страничка сформирована за <b>$seconds</b> секунд.");
	
  if($CURUSER) (  
  print("<br><b>$queries</b> (queries) - <b>$percentphp%</b> (php) - <b>$percentsql%</b> (sql) $queries_staff")
  );

?>

</div>
</td><td class="footer">



</td></tr></table>

</td></tr></table>


</td>
<td class="brd" background="./themes/<?=$ss_uri;?>/images/table2_05.png">&nbsp;</td></tr><tr><td class="brd"><img src="./themes/<?=$ss_uri;?>/images/table2_06.png" width="20" height="22"></td><td class="brd" background="./themes/<?=$ss_uri;?>/images/table2_07.gif">&nbsp;</td><td class="brd"><img src="./themes/<?=$ss_uri;?>/images/table2_08.png" width="20" height="22"></td></tr></table>
    
</td><td class="brd" background="./themes/<?=$ss_uri;?>/images/table1_2.png">&nbsp;</td></tr><tr><td class="brd" width="11" height="49"><img src="./themes/<?=$ss_uri;?>/images/table1_left1.png" width="11" height="49"></td><td align="center" class="brd" background="./themes/<?=$ss_uri;?>/images/table1_center2.png"><a href="#top"><img src="./themes/<?=$ss_uri;?>/images/top.png" alt="TOP" width="111" height="42" border="0" /></a></td>
<td class="brd" width="11" height="49"><img src="./themes/<?=$ss_uri;?>/images/table1_right1.png" width="11" height="49"></td></tr></table></body></html>