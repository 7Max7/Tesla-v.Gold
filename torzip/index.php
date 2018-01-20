<?php

/**
 * @author 7Max7
 * @copyright 2010
 */

require_once("../include/bittorrent.php");

dbconn(false);


global $DEFAULTBASEURL;
echo "Проверка файлов: <hr>";



$dh = opendir(ROOT_PATH.'torzip/');
while ($file = readdir($dh)) :
if (preg_match('/^(.+)\.$/si', $file, $matches))
$file = $matches[1];
$fileend = end(explode('.', $file));
if ($fileend == "zip") {

echo "<a href=\"".$DEFAULTBASEURL."/".$file."\">".$file."</a>";


echo " (".mksize(filesize(ROOT_PATH.'torzip/'.$file)).")";

echo "<br>";

}

endwhile;
closedir($dh);



echo "<hr>";

?>
