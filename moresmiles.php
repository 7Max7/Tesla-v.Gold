<?
require_once "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

$ss_uri = $CURUSER["stylesheet"];

if (!$ss_uri) {
	$ss_uri = $default_theme;
}
?>

<head>
<script language=javascript>

function SmileIT(smile,form,text){
    window.opener.document.forms[form].elements[text].value = window.opener.document.forms[form].elements[text].value+" "+smile+" ";
    window.opener.document.forms[form].elements[text].focus();
}
</script>
<title>Смайлики</title>
<link rel="stylesheet" href="./themes/$ss_uri/style.css" type="text/css">
</head>

<table width="100%" border=1 cellspacing="2" cellpadding="2">

<tr  align="center">
<?
$ctr=0;
global $smilies;
while ((list($code, $url) = each($smilies))) {
   if ($count % 3==0)
      print("\n<tr>");
      print("<span align=\"center\"><a href=\"javascript: SmileIT('".str_replace("'","\'",$code)."','".htmlentities($_GET["form"])."','".htmlentities($_GET["text"])."')\"><img border=\"0\" src=\"pic/smilies/".$url."\"></a></span>");
      $count++;

   if ($count % 3==0)
      print("\n</tr>");
      $ctr++;
}
unset($smilies);
?>
</tr>
</table>
<div align="center"><b>Смайлов в базе</b>: <? echo $ctr;?> <br>
<a class="altlink_green" href="javascript: window.close()"><? echo Закрыть; ?></a>
</div>
<?
