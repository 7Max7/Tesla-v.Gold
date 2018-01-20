
<? show_blocks('d');?>
<td valign="top" width="160">
<? show_blocks('r'); ?>

</td></tr></table></td>
</table></table></table>
</td>

<td valign="top" width="155">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="19"><img src="themes/<?=$ss_uri?>/images/footer_02.gif" width="19" height="40"></td>
    <td background="themes/<?=$ss_uri?>/images/footer_03.gif">&nbsp;</td>
    <td width="11"><img src="themes/<?=$ss_uri?>/images/footer_05.gif" width="11" height="40"></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
  
  

    <td width="19" background="themes/<?=$ss_uri?>/images/footer_07.jpg">&nbsp;</td>
    <td background="themes/<?=$ss_uri?>/images/footer_08.gif">
	
	

	<div style ="font-size: xx-small;" align ="center">
    
<table class="bottom" width="100%" border="0" cellspacing="0" cellpadding="0"><tr valign="top">
<td width="49%" class="bottom"><div align="center">

<b>

	<?
	show_blocks('f');
?>
<?
///->7/08/2008 7Max7 for multsity.com: icq - 225454228 
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
	print("<table class=\"embedded\" width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">
	<tr valign=\"top\">\n");
	print("<tr>
	<td width=\"49%\" class=\"topnav\">
	<div align=\"center\">".VERSION.TBVERSION."<br>
	<span style='font-weight:bold; color:#627EB7;'>Страничка сформирована за <b>$seconds</b> секунд.\n
	$queries_staff	</span></div></td></tr>\n");

    print("</tr></table>");
	print("</html>\n");
?>
</div></td>
</tr></table>
</div></td>
<td width="11" background="themes/<?=$ss_uri?>/images/footer_09.gif">&nbsp;</td>
 </tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>    <td width="19"><img src="themes/<?=$ss_uri?>/images/footer_11.jpg" width="19" height="30"></td>
    <td background="themes/<?=$ss_uri?>/images/footer_15.gif">&nbsp;</td>
    <td width="134"><a  href="<?=$DEFAULTBASEURL?>" target="_blank"><img title="Диночке" src="themes/<?=$ss_uri?>/images/footer_13.gif" width="134" height="30" border="0"></a></td>
    <td background="themes/<?=$ss_uri?>/images/footer_15.gif"></td>
	<td width="30"><img src="themes/<?=$ss_uri?>/images/footer_16.gif" width="30" height="30"></td>

  </tr></td>
</table>