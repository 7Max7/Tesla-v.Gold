<?



require_once("include/bittorrent.php");

dbconn();

loggedinorreturn();

if ($_GET["format"])
{
     if ($_GET["format"]==1)
     header("Location: formatss.php?form=mov");
     elseif ($_GET["format"]==2)
     header("Location: formatss.php?form=all");
 exit();
}


stdhead("Форматы");
?>


<form action="<?=$PHP_SELF;?>" method="get" name="form1">
<table border="1" cellspacing="0" cellpadding="10" width=20%>

<tr><td align="center" colspan="2"><p align=center><select name=format><option value='1' >Форматы видео</option><option value='2' >Форматы файлов</option></select></p><br /><input type="submit" class=btn value="Посмотреть!"></td></tr>
</table>
</form>

<?
stdfoot();

?>