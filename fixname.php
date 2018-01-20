<?
require "include/bittorrent.php";
dbconn(false);
stdheadchat("Починка названий и категорий");




 $rezult = sql_query("SELECT id, added,subject FROM news") or sqlerr(__FILE__, __LINE__);
 $myrow = mysql_fetch_array($rezult);

 do
 {
 $n = 1;        //  месяц с котророго необходимо начать (здесь январь)
 $e = 12;        //  месяц которым нужно закончить (здесь декабрь)
    $sm = rand($n+$k, $e+$k);
		$sm = $sm - $k;
	 if ($sm < 10)
	  {
	    $sm = "0".$sm;
	  }
 $timestamp = mktime(0,0,0,$sm,15,2010);	  
 $cday = date("t", $timestamp);	  
	    $day = rand(1+$k, $cday+$k);
		$day = $day - $k;
	 if ($day < 10)
	  {
	    $day = "0".$day;
	  }
    $h = rand(0+$k, 23+$k);
	$h = $h - $k;
	 if ($h < 10)
	  {
	    $h = "0".$h;
	  }
    $m = rand(0+$k, 59+$k);
	$m = $m - $k;
	 if ($m < 10)
	  {
	    $m = "0".$m;
	  }
    $s = rand(0+$k, 59+$k);
	$s = $s - $k;
	 if ($s < 10)
	  {
	    $s = "0".$s;
	  }
    $dat = "2010-".$sm."-".$day." ".$h.":".$m.":".$s;

	sql_query("UPDATE news SET added=".sqlesc($dat)." WHERE id=".sqlesc($myrow["id"])) or sqlerr(__FILE__, __LINE__);
	echo "Обновляю дату на <b>$dat</b> в новости: <b>".$myrow["subject"]."</b><br>";

 }

while($myrow = mysql_fetch_array($rezult)); 



die;



/*
require "include/benc.php";

$sql1 = sql_query("SELECT name, id FROM torrents WHERE multitracker='yes' ORDER BY id DESC LIMIT 100") or sqlerr(__FILE__, __LINE__);

while ($res = mysql_fetch_assoc($sql1)){

$ifilename = ROOT_PATH."torrents/".$res["id"].".torrent";

if(!file_exists($ifilename))
echo "Нет файла ".$res["id"]." <br>";

else {
$dict = bdec_file($ifilename, 1024000);

//$dict=@bdec(@benc($dict)); 
@list($info) = @dict_check_t($dict, "info");
$infohash = sha1($info["string"]);
//echo "$infohash <br>";

if (!empty($dict['value']['info']['value']['private'])){
echo ("Приватный торрент файл - <a href=details.php?id=".$res["id"].">".$res["id"]."</a> <br>");
}

//print_r($dict);


}

}

die(" конец .");

*/





//sql_query("ALTER TABLE `torrents` ADD `checkpeers` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `added`") or sqlerr(__FILE__,__LINE__);


/*
sql_query("UPDATE torrents SET category='13' WHERE category='14' AND (torrent_com LIKE '%dvdrip%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

$zatronyto = mysql_affected_rows();

echo "Обновлено $zatronyto строк.";
die;




sql_query("UPDATE torrents SET multi_time = '0000-00-00 00:00:00' WHERE visible='no' AND multi_time <> '0000-00-00 00:00:00' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

$zatronyto = mysql_affected_rows();

echo "Обновлено $zatronyto строк.";
die;
*/


/*
$sql = sql_query("SELECT id, category, name, size FROM torrents WHERE category='4' ORDER BY rand() LIMIT 10000") or sqlerr(__FILE__, __LINE__);

while ($res = mysql_fetch_assoc($sql)){
	
$ca = array();
$sql1 = sql_query("SELECT torrent, filename FROM files WHERE torrent=".$res["id"]) or sqlerr(__FILE__, __LINE__);

while ($res1 = mysql_fetch_assoc($sql1))
$ca[] = $res1["filename"];

$mycateyrogy = parse_arrray_cat($ca, $res["size"]);


if ($mycateyrogy!=false && $mycateyrogy<>$res["category"]){

echo "<a href=\"details.php?id=".$res["id"]."\">".$res["name"]."</a> и ".$res["category"];
echo " - нужная категория <b>$mycateyrogy</b>";

sql_query("UPDATE torrents SET category=".sqlesc($mycateyrogy)." WHERE id=".$res["id"]) or sqlerr(__FILE__,__LINE__);

echo "<br>";
}



unset($ca,$mycateyrogy);

}


echo "<script>setTimeout('document.location.href=\"fixname.php\"', 10000);</script>"; 



*/



sql_query("UPDATE torrents SET category='27' WHERE category<>'27' AND (name LIKE '%КПК%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 24<br>";



sql_query("UPDATE torrents SET category='10' WHERE category='4' AND (descr LIKE '%mp3%' AND descr LIKE '%формат%') AND 
(name LIKE '%Дискография%' OR name LIKE '%Discography%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 24<br>";



sql_query("UPDATE torrents SET category='10' WHERE category='4' AND (name LIKE '%FLAC%' OR name LIKE '%APE%' OR name LIKE '%ALAC%' OR name LIKE '%/WAV/%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 24<br>";


sql_query("UPDATE torrents SET category='10' WHERE category='4' AND (torrent_com LIKE '%(CBR)%' OR torrent_com LIKE '%(VBR)%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 25<br>";


sql_query("UPDATE torrents SET category='10' WHERE category='4' AND (name LIKE '%WMA%' OR name LIKE '%MP3%' OR name LIKE '%AAC%' OR name LIKE '%саундтрек%'OR name LIKE '%soundtrack%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 26<br>";


sql_query("UPDATE torrents SET category='10' WHERE category='4' AND (torrent_com LIKE '%WMA%' OR torrent_com LIKE '%APE%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 27<br>";





/*
////////////////////// для разное ///////////////////////////

sql_query("UPDATE torrents SET category='22' WHERE category='4' AND (name LIKE '%TVRip%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 5<br>";




sql_query("UPDATE torrents SET category='14' WHERE category='4' AND (name LIKE '%Blu-Ray%' OR name LIKE '%Blue-ray%' OR name LIKE '%HDTV%' OR name LIKE '%HDTVRip%' OR name LIKE '%HDRip%' OR name LIKE '%BRRip%' OR name LIKE '%BDRip%' OR name LIKE '%HDRip%' OR name LIKE '%BDRemux%' OR name LIKE '%720p%' OR name LIKE '%1080p%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 6<br>";



sql_query("UPDATE torrents SET category='13' WHERE category='4' AND (name LIKE '%VHSRip%' OR name LIKE '%DVDrip%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 8<br>";


sql_query("UPDATE torrents SET category='16' WHERE category='4' AND (name LIKE '%FB2%' OR name LIKE '%TXT%' OR name LIKE '%DjVu%' OR name LIKE '%PDF%' OR name LIKE '%rtf%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 9<br>";


sql_query("UPDATE torrents SET category='5' WHERE category='4' AND (name LIKE '%(%' AND name LIKE '%)%' AND name LIKE '%PC%') AND size>'1221225472' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 10<br>";

sql_query("UPDATE torrents SET category='26' WHERE category='4' AND (name LIKE '%(%' AND name LIKE '%)%' AND name LIKE '%PC%') AND size<'121225472' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 11<br>";


sql_query("UPDATE torrents SET category='18' WHERE category='4' AND (name LIKE '%(%' AND name LIKE '%)%' AND name LIKE '%MOV%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 12<br>";


sql_query("UPDATE torrents SET category='10' WHERE category='4' AND (name LIKE '%losless%' OR name LIKE '%mp3%' OR name LIKE '%M4A%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 7<br>";



sql_query("UPDATE torrents SET category='16' WHERE descr LIKE '%О аудио книге%' AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 1<br>";


sql_query("UPDATE torrents SET category='15' WHERE (torrent_com LIKE '%DVD-5%' OR torrent_com LIKE '%DVD5%' OR torrent_com LIKE '%DVD9%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 2<br>";


sql_query("UPDATE torrents SET category='8' WHERE category<>'8' AND (name LIKE '%PSP%' OR name LIKE '%PS2%' OR name LIKE '%XBOX360%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 3<br>";

sql_query("UPDATE torrents SET category='14' WHERE category='4' AND (name LIKE '%BD Remux%' OR name LIKE '%BluRay%' OR torrent_com LIKE '%Blu-Ray%' OR torrent_com LIKE '%HDTVRip%' OR torrent_com LIKE '%BDRip%' OR torrent_com LIKE '%BDRemux%' OR torrent_com LIKE '%720p%' OR torrent_com LIKE '%1080p%' OR torrent_com LIKE '%1080i%' OR name LIKE '%1080i%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);


echo "Обновлено ".mysql_affected_rows()." строк. 4<br>";

sql_query("UPDATE torrents SET category='22' WHERE category='4' AND (name LIKE '%SATRip%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 14<br>";

sql_query("UPDATE torrents SET category='11' WHERE (descr LIKE '%LostFilm%' OR descr LIKE '%NovaFilm%' OR descr LIKE '%Kravec%' OR descr LIKE '%Кравец%' OR descr LIKE '%GSGroup %' OR descr LIKE '%Kuraj-Bambey%' OR descr LIKE '%Кураж-Бамбей%' OR descr LIKE '%Кубик в Кубе%'OR descr LIKE '%KvadratMalevicha%') and category<>'11' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 15<br>";

sql_query("UPDATE torrents SET category='11' WHERE (torrent_com LIKE '%LostFilm%' OR torrent_com LIKE '%NovaFilm%' OR torrent_com LIKE '%Studdio%' OR name LIKE '%NewStudio%' OR torrent_com LIKE '%Kvadrat%') AND category<>'11' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 16<br>";


sql_query("UPDATE torrents SET category='8' WHERE (torrent_com LIKE '%PS2%' OR torrent_com LIKE '%PS3%' OR torrent_com LIKE '%PSP%' OR torrent_com LIKE '%PSX-PSP%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 19<br>";


sql_query("UPDATE torrents SET category='26' WHERE category='4' AND name LIKE '%РС%' AND name LIKE '%Multi%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 19.2<br>";

sql_query("UPDATE torrents SET category='26' WHERE category='4' AND name LIKE '%PC%' AND name LIKE '%Repack%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 19.3<br>";


sql_query("UPDATE torrents SET category='1' WHERE category='4' AND (name LIKE '%MDF%' OR name LIKE '%ISO%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 19.4<br>";


sql_query("UPDATE torrents SET category='27' WHERE category='4' AND torrent_com LIKE '%iPhone%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 20<br>";


sql_query("UPDATE torrents SET category='5' WHERE category='4' AND name LIKE '%Софтклаб%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 20.2<br>";


sql_query("UPDATE torrents SET category='12' WHERE category='4' AND (torrent_com LIKE '%anime%' OR torrent_com LIKE '%AniDUB%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 21<br>";


sql_query("UPDATE torrents SET category='15' WHERE category='4' AND size>'1221225472' AND (name LIKE '%DVD-9%' OR name LIKE '%DVD-5%' OR name LIKE '%DVD9%' OR name LIKE '%DVD5%' OR name LIKE '%DVD%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 23.2<br>";


sql_query("UPDATE torrents SET category='10' WHERE (name LIKE '%(OST)' OR name LIKE '%.OST' OR torrent_com LIKE '%OST)' OR torrent_com LIKE '%OST-%' OR torrent_com LIKE '%OST -%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 30<br>";


sql_query("UPDATE torrents SET category='16' WHERE (name LIKE '%ebook%' OR name LIKE '%уроки%' OR name LIKE '%TXT%' OR name LIKE '%RTF%' OR name LIKE '%OCR%' OR name LIKE '%FB2%' OR name LIKE '%CHM%' OR name LIKE '%PDF%' OR name LIKE '%журнал%' OR name LIKE '%Djvu %' OR name LIKE '%doc%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 31<br>";


////////////////////// для разное ///////////////////////////
*/













/*
sql_query("UPDATE torrents SET category='16' WHERE descr LIKE '%О аудио книге%' AND category<>'16' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 1<br>";




sql_query("UPDATE torrents SET category='15' WHERE (torrent_com LIKE '%DVD-5%' OR torrent_com LIKE '%DVD5%' OR torrent_com LIKE '%DVD9%') AND category<>'15' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 2<br>";





sql_query("UPDATE torrents SET category='8' WHERE category<>'8' AND (name LIKE '%PSP%' OR name LIKE '%PS2%' OR name LIKE '%XBOX360%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);



echo "Обновлено ".mysql_affected_rows()." строк. 3<br>";





sql_query("UPDATE torrents SET category='14' WHERE category<>'14' AND (name LIKE '%BD Remux%' OR name LIKE '%BluRay%' OR torrent_com LIKE '%Blu-Ray%' OR torrent_com LIKE '%HDTVRip%' OR torrent_com LIKE '%BDRip%' OR torrent_com LIKE '%BDRemux%' OR torrent_com LIKE '%720p%' OR torrent_com LIKE '%1080p%' OR torrent_com LIKE '%1080i%' OR name LIKE '%1080i%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);


echo "Обновлено ".mysql_affected_rows()." строк. 4<br>";













sql_query("UPDATE torrents SET category='25' WHERE category='4' AND (name LIKE '%GIF%' OR name LIKE '%PNG%' OR name LIKE '%JPG%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 13<br>";








sql_query("UPDATE torrents SET category='22' WHERE category='4' AND (name LIKE '%SATRip%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 14<br>";


sql_query("UPDATE torrents SET category='22' WHERE (descr LIKE '%видео%' AND descr LIKE '%курс%') and category<>'22' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 14.2<br>";


sql_query("UPDATE torrents SET category='11' WHERE (descr LIKE '%LostFilm%' OR descr LIKE '%NovaFilm%' OR descr LIKE '%Kravec%' OR descr LIKE '%Кравец%' OR descr LIKE '%GSGroup %' OR descr LIKE '%Kuraj-Bambey%' OR descr LIKE '%Кураж-Бамбей%' OR descr LIKE '%Кубик в Кубе%'OR descr LIKE '%KvadratMalevicha%') and category<>'11' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 15<br>";



sql_query("UPDATE torrents SET category='11' WHERE (torrent_com LIKE '%LostFilm%' OR torrent_com LIKE '%NovaFilm%' OR torrent_com LIKE '%Studdio%' OR name LIKE '%NewStudio%' OR torrent_com LIKE '%Kvadrat%') AND category<>'11' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 16<br>";



sql_query("UPDATE torrents SET category='11' WHERE name LIKE '%season%' AND name LIKE '%episode%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 17<br>";


sql_query("UPDATE torrents SET category='10' WHERE torrent_com LIKE '%[Dance]%' OR torrent_com LIKE '%[Soundtrack]%' OR name LIKE '%MP3%' OR name LIKE '%VA -%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 18<br>";


sql_query("UPDATE torrents SET category='8' WHERE torrent_com LIKE '%PS2%' OR torrent_com LIKE '%PS3%' OR torrent_com LIKE '%PSP%' OR torrent_com LIKE '%PSX-PSP%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 19<br>";





sql_query("UPDATE torrents SET category='26' WHERE category<>'26' AND name LIKE '%РС%' AND name LIKE '%Multi%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 19.2<br>";




sql_query("UPDATE torrents SET category='26' WHERE category<>'26' AND name LIKE '%PC%' AND name LIKE '%Repack%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 19.3<br>";



sql_query("UPDATE torrents SET category='1' WHERE category<>'1' AND (name LIKE '%MDF%' OR name LIKE '%ISO%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 19.4<br>";

sql_query("UPDATE torrents SET category='27' WHERE category<>'27' AND torrent_com LIKE '%iPhone%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 20<br>";


sql_query("UPDATE torrents SET category='5' WHERE category<>'5' AND name LIKE '%Софтклаб%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 20.2<br>";


sql_query("UPDATE torrents SET category='12' WHERE category<>'12' AND torrent_com LIKE '%AniDUB%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 21<br>";


sql_query("UPDATE torrents SET category='25' WHERE category<>'25' AND name LIKE '%обои%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 22<br>";


sql_query("UPDATE torrents SET category='18' WHERE category<>'18' AND size<'1221225472' AND name LIKE '%DVD%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 23<br>";


sql_query("UPDATE torrents SET category='15' WHERE category<>'15' AND size>'1221225472' AND (name LIKE '%DVD-9%' OR name LIKE '%DVD-5%' OR name LIKE '%DVD9%' OR name LIKE '%DVD5%' OR name LIKE '%DVD%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 23.2<br>";




sql_query("UPDATE torrents SET category='10' WHERE category<>'10' AND name LIKE '%soundtrack%' OR name LIKE '%cаундтрэк%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 28<br>";


sql_query("UPDATE torrents SET category='10' WHERE category<>'10' AND name LIKE '%OST%' AND name LIKE '%cаундтрэк%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 29<br>";


sql_query("UPDATE torrents SET category='10' WHERE (name LIKE '%(OST)' OR name LIKE '%.OST' OR torrent_com LIKE '%OST)' OR torrent_com LIKE '%OST-%' OR torrent_com LIKE '%OST -%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 30<br>";


sql_query("UPDATE torrents SET category='16' WHERE (name LIKE '%ebook%' OR name LIKE '%уроки%' OR name LIKE '%TXT%' OR name LIKE '%RTF%' OR name LIKE '%OCR%' OR name LIKE '%FB2%' OR name LIKE '%CHM%' OR name LIKE '%PDF%' OR name LIKE '%журнал%' OR name LIKE '%Djvu %' OR name LIKE '%doc%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 31<br>";

 
sql_query("UPDATE torrents SET category='16' WHERE (descr LIKE '%Отсканированные%' AND descr LIKE '%страниц%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 32<br>";


sql_query("UPDATE torrents SET category='10' WHERE category<>'10' AND (descr LIKE '%Битрэйт%' AND descr LIKE '%kbps%' AND descr LIKE '%Формат%' AND descr LIKE '%Жанр%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 33<br>";


sql_query("UPDATE torrents SET category='10' WHERE category<>'10' AND (name LIKE '%MP3%' AND descr LIKE '%О музыке%' AND descr LIKE '%Треклист%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 34<br>";


sql_query("UPDATE torrents SET category='10' WHERE category<>'10' AND (descr LIKE '%Tracklist%' AND descr LIKE '%Genre%' AND descr LIKE '%Quality%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 35<br>";


sql_query("UPDATE torrents SET category='11' WHERE (name LIKE '%сезон%' OR name LIKE '%серия%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 36<br>";


sql_query("UPDATE torrents SET category='11' WHERE (name LIKE '%сезон%' OR name LIKE '%серии%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 37<br>";





sql_query("UPDATE torrents SET category='27' WHERE (torrent_com LIKE '%Mobiles%' OR torrent_com LIKE '%[Java]%') AND category<>'27' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "Обновлено ".mysql_affected_rows()." строк. 38<br>";


die;
*/



$sql = sql_query("SELECT name,id FROM torrents WHERE category <> '11' AND name LIKE '%[%' AND torrent_com NOT LIKE '%Фильтрация%' AND owner='92' LIMIT 10000") or sqlerr(__FILE__, __LINE__);

while ($res = mysql_fetch_assoc($sql)){

$one=array("Season ","Seasons ","Episodes ","Episode "," [spoiler=Trailer]");
$two=array("Cезон ","Cезоны ","Серии ","Серия ","[spoiler=Трейлер]");

$name = str_replace($one, $two, $res["name"]);


$name = str_replace("от от ", "от ", $name);


$one_t = array(
"от RollHD", "от HELLRAiSER", "от HELLYWOOD", "от CGInfo", "от HQ-ViDEO", "от Shinobi", "от Infernum", "от Friends-Club", "от FRiENDS-Club", "от DoroshDan", "от PlanetaUA", "от Кеши", "by Veloboss", "by GENADIY", "by HQ-ViDEO", "by BigFan", "от R.G. PlayBay", "от RomeoWhite", "by RuUu", "by maksnev82", "от doberman", "от Drakula", "by demonВидео", "от FreeHD", "от kubik v kube", "от FS-Media", "от Kvadratmalevicha", "by parole", "by Хоттабыч (BigFANGroup)", "by R.G.МОСКВИ4И", "by the sun", "by OZAGIS", "by BigFANGroup", "by Infineon", "от R.G ReCoding", "by R.G. 48 seconds", "от DHT-Movies", "by FS-Media", "by Arow Malossi", "by Kino Pirate", "от LPF TV", "от Overlord", "от 0day ", "от HAZARD'A", "от R.G Repacker's", "by R.G.R3PacK", "от CtrlHD", "от ESiR", "by 4Euro", "от New-TrackerUA", "от M.I.B", "от EuReKa", "от Linkorn", "от iolegv", "от Neo-Sound.ru", "от SAMLL-RiP", "от CHDBits", "от RI4MOND", "от Smart's studios", "от EbP", "от TORRENTSES", "by NK", "by bizarrevic", "by VANGA", "by Basilevs", "от trexmernii", "by XENON", "by morganvolter", "by HDClub", "от MadChester Кубик в Кубе NovaFiLM", "от EuReKA", "by ELEKTRI4KA", "by 5.1", "by SneGGs", "от Oleggutsalo", "от zackary", "by VIDODB", "от SkyeFilmTV", "by Suprug", "by rp0Mk0cTb", "by SDHQ", "от Doctor No.", "от SoftClub", "от TDiTP", "от Лаптя", "от F_i_X", "от 1001 Cinema", "от Drakulа", "от KаMo", "by KaMo", "by Illusion", "от Psycho-Share", "by X-DIGITAL (BigFANGroup)", "от InterfaceLift", "от KaMo", "by Vladas", "by HA3APET PC", "от RELOADED", "от Маши", "by An Ocean Wave", "by HA3APET", "от lenpas", "от W I", "от KiLR0Y", "от games_vandal", "от EPIDEMZ", "by DESAN", "от Капуцинов", "от BaibaKo", "от HD Tracker", "от -DaRkY-", "от lev31071977", "by Kommunist", "от Smart's Studios", "от Player1993", "от Overcast", "от Ildarec014", "от Bumblebee", "от SmallRip", "by games vandal", "от R.G.", "от SoftClub/EA Russia", "by Andrjuha-Bratan", "by Shel", "от группы CGInfo", "by Mr.legal", "by Megashara", "by Padre mp4", "от Zhmak", "by DJ ART", "от FoolMovie", "от iolegv RuUu", "by boban39", "by stalone", "от К@Мo", "от AllSeries Info CGInfo", "от Dime HQ-VIDEO", "от Geralt iz Rivii", "by HDCiub", "by d_kos", "by doberman", "от HQ-films", "от epu2009", "by d kos", "by rus-tracker", "от Mari", "от VIDEODB", "by Studdio", "от FRiENDS Club", "by Хоттабыч", "by Tiger", "by Virelic", "by AZBUKASOFTA", "от Lali", "by trexmernii", "от Instigator-NN", "by R.G. Beautiful Thieves", "от Никулина", "от 3m1L", "от NOLIMITS-TEAM", "от R.G. \"48 seconds\"", "от ANDROZZZ", "by djip", "от boss89", "от StarTorrent", "от z10yded", "от КММ", "by martin", "by \"R.G. 48 Seconds\"", "от РиперАМ Suprug", "by qDan", " - РиперAM", "от MashystiK", "от JoomlaXTC", "от Joomlapraise", "от greys2008 Arkadiy", "от SkeT", "by stalone ( stalone-2)", "by R.O.T.O.R", "by Fool Movie", "by KVNforAll N!CK", "by KVNforAll Ruuu", "by BigSOUNDgroup", "by yuraflash", "by POkEmON", "by v1nt", "от HotRabbit", "от Васёк777", "от PsihoShare", "от ФАГОТ", "by s0lAr", "от HeDgEhOt", "от Кosmos", "от R.G.R3PacK", "by 7lobiN", "by dimich", "от RAMpage CHG", "от R.G. CodeRs", "от Хоттабыч (BigFANGroup)", "от Феникс", "от Уокер | Remastered", "от Тракториста", "от Тракторист", "от СИНЕМА-ГРУПП", "от РиперАМ & Suprug", "от РиперAM", "от Лобана", "от Кураж-Бамбей", "от Кураж Бамбей", "от Кубик в Кубе", "от Космос", "от КинозалSAT", "от ДубльPR Studio", "от ДлиNNый", "от Архары", "от Zona-X", "от YOOtheme", "от WiKi)", "от Wegas Digital", "от wakal", "от volka", "от Voland", "от VO-Production", "от Vladislav71", "от ViP", "от VelArt", "от vadi", "от Ultra", "от UaTeam", "от TVShows", "от Traumer", "от SuperNatural", "от StopFilm", "от Spieler", "от SMALL-RiP", "от Shevon", "от ПереЛоЛ", "от Shepards", "от sergeorg", "от Sergei7721", "от RuUu, iolegv & ANDROZZZ", "от RuUu (BigFANGroup)", "от RuUu", "от RUSSOUND", "от RP4 CHG", "от RommyFilm", "от RocketTheme", "от RiperAM", "от RIPER.AM", "от RelizLab", "от RadioXyLigaN", "от R.G.Spieler", "от R.G. Механики", "от R.G. Игроманы", "от R.G. Repackers Bay", "от R.G. ReCoding", "от R.G. Catalyst", "от PиперАМ", "от Punisher", "от PowerTracker", "от Pasha74", "от ParadiSe", "от OneFilm", "от Oday", "от NovaFilm", "от NewStudio", "от NaumJ", "от Music80", "от mk2", "от mi24", "от Martin", "от maksnev82", "от m0j0", "от Lunatik", "от Lukavaya", "от LostFilm.tv", "от LostFilm", "от Kosmos", "от Kino Pirate", "от Kerob", "от K1RgH0ff", "от iolegv-RuUu", "от Ildarec-014", "от HQGROUP", "от HQCLUB", "от HDReactor", "от HDGANG", "от HDCLUB", "от HD-Zona-X", "от Hansmer", "от Gravi-TV", "от Gellard", "от G1AR", "от FreeTorrents-UA", "от FoC", "от FLINTFILMS", "от FinaRGoT", "от Fenixx)", "от Fenixx", "от ExKinoRay.TV", "от ExKinoRay", "от ELmusic", "от E180", "от Dj Borzyj", "от Dizell", "от dima360", "от DiLLeR", "от Devlad", "от DeviL", "от Demon2901", "от danis92", "от Cybertron", "от Crusader3000", "от Constin", "от CMEGroup", "от CinemaStreet", "от ChuckSite", "от cdman", "от BTT-TEAM", "от Brux", "от breznev94(stalone)", "от BigMOVIEGroup & Hurtom", "от BigMOVIEGroup", "от BigFANGroup", "от BestSound ExKinoRay", "от Bagvell", "от AndrewWhite", "от @PD.&.KAMO", "от 5 pluh", "от 2ndra", "от olmier", "от LAMPO4KA", "от 25KADR", "от PskovLine", "от SENATORiNFO-TEAM", "от Kinobomond", "от WIDDER", "от Кубик в кубе", "от Anton299", "от bxpx", "от R.G. PLAGUE", "by MD-TEAM BigFanGroup KVNforAll", "от HELLWOOD", "от KalliostroV", "от Cinema-group info", "от ELEKTRI4KA", "от Werdog", "от Kubik.v.Kube", "от KVNforAll", "от «Киномании»", "by ANDROZZZ", "от @PD KaMo", "от BUTOVOgroup", "от BeeFilm", "от ENGINEER", "от KiNOFACK", "by Xp_Dron", "от 1001Cinema", "от Zt", "by MYDIMKA", "от Humochka", "by FoolMovie", "от КОля", "by Shituf", "by Ultimat", "от HDTracker", "от FORREST", "от Btpo2l", "by BigFAN", "by Vitek", "by R.G.LanTorrent", "от GoldenShara Studio", "от ceo54", "от MaLLleHbKa", "от TOP", "by N!CK", "от Studdio", "от machete96", "by Tonic-RuUu (BigFANGroup)", "от Dimasty", "от Кинконга", "от btpo2l", "от HiDt", "от zim2001", "от Nemo24", "by Oleggutsalo", "от a1chem1st", "от darktech", "от Fartuna158", "от -=ynkas=-", "от MEGASHARA", "by FEAR", "от Шмель", "by Axwel", "от Zlatan83", "от FilmGate", "от Filmgate", "от КГКМ BigFANGroup", "от LTarik", "от POkEmON", "от R.G. R3PacK", "от BigFAN Group", "by DivX (2010) PC", "by EGO team", "by Глюк", "by KVNforAll Veloboss", "by VIDEODB", "by Kelpie", "by Zicos", "by iolegv-RuUu (BigFANGroup)", "by ~Romych~", "by KVNforAll", "by гаврила", "by MD-TEAM KVNforAll", "by Igoraky", "by Neofilm", "от Кинозал.ТВ", "от AudioZona-X", "от Running Man", "от HQRips", "от ANDROZZZ-RuUu", "by ua2004", "by mdteam", "by Iriy", "от Русфильм", "от Babun", "by DEMIGOD", "by iolegv-RuUu", "от orchidea777", "by Allep", "by iolegv (BigFANGroup)", "от Good-Cinema", "от zmei666", "от Menyk", "by Alex Smit", "by ANDROZZZ RuUu (BigFANGroup)", "от YoFilm", "от blackmambo", "от Lexor", "от iriston", "от genafon", "от vladvld", "by iolegv RuUu (BigFANGroup)", "от Yuraflash", "от Postal", "от BOGUSH", "by MD-TEAM BigFANGroup KVNforAll", "от m4r5", "by tak prosto",
"от RollHD",
"от Smart&#039;s studios",
"от CRiSC (HDBits internal)",
"от HELLRAiSER",
"от HELLYWOOD",
"от CGInfo",
"от HQ-ViDEO",
"от Shinobi",
"от Infernum",
"от Friends-Club",
"от FRiENDS-Club",
"от DoroshDan",
"от PlanetaUA",
"от Кеши",
"by Veloboss",
"by GENADIY",
"by HQ-ViDEO",
"by BigFan",
"от R.G. PlayBay",
"от FRiENDS-Club",
"от RomeoWhite",
"by RuUu",
"by maksnev82",
"от doberman",
"от Drakula",
"by demonВидео",
"от FreeHD",
"от kubik v kube",
"от FS-Media",
"от Kvadratmalevicha",
"by parole",
"by Хоттабыч (BigFANGroup)",
"by R.G.МОСКВИ4И",
"by the sun",
"by OZAGIS",
"by BigFANGroup",
"by Infineon",
"by Fool Movie",
"by KVNforAll N!CK",
"by KVNforAll Ruuu",
"by BigSOUNDgroup",
"by yuraflash",
"by Veloboss",
"by GENADIY",
"by POkEmON",
"от LPF TV",
"by R.G. Beautiful Thieves",
"от DHT-Movies",
"by v1nt",
"от HotRabbit",
"от Васёк777",
"от PsihoShare",
"от ФАГОТ",
"by s0lAr",
"от HeDgEhOt",
"от Кosmos",
"от R.G.R3PacK",
"by 7lobiN",
"by dimich",
"by maksnev82",
"от HAZARD'A",
"от PlanetaUA",
"от RAMpage CHG",
"от R.G. CodeRs",
"от PlanetaUA",
"от Хоттабыч (BigFANGroup)",
"от Феникс",
"от Уокер | Remastered",
"от Тракториста",
"от Тракторист",
"от СИНЕМА-ГРУПП",
"от РиперАМ & Suprug",
"от РиперAM",
"от Лобана",
"от Кураж-Бамбей",
"от Кураж Бамбей",
"от Кубик в Кубе",
"от Космос",
"от КинозалSAT",
"от ДубльPR Studio",
"от ДлиNNый",
"от Архары",
"от Zona-X",
"от z10yded",
"от YOOtheme",
"от WiKi)",
"от Wegas Digital",
"от wakal",
"от volka",
"от Voland",
"от VO-Production",
"от Vladislav71",
"от ViP",
"от VelArt",
"от vadi",
"от Ultra",
"от UaTeam",
"от TVShows",
"от Traumer",
"от SuperNatural",
"от StopFilm",
"от Spieler",
"от SMALL-RiP",
"от Shevon",
"от ПереЛоЛ",
"от Shepards",
"от sergeorg",
"от Sergei7721",
"от RuUu, iolegv & ANDROZZZ",
"от RuUu (BigFANGroup)",
"от RuUu",
"от RUSSOUND",
"от RP4 CHG",
"от RommyFilm",
"от RocketTheme",
"от RiperAM",
"от RIPER.AM",
"от RelizLab",
"от RadioXyLigaN",
"от R.G.Spieler",
"от R.G. Механики",
"от R.G. Игроманы",
"от R.G. Repackers Bay",
"от R.G. ReCoding",
"от R.G. Catalyst",
"от PиперАМ",
"от Punisher",
"от PowerTracker",
"от Pasha74",
"от ParadiSe",
"от OneFilm",
"от Oday",
"от NovaFilm",
"от NOLIMITS-TEAM",
"от NewStudio",
"от NaumJ",
"от Music80",
"от mk2",
"от mi24",
"от Martin",
"от maksnev82",
"от m0j0",
"от Lunatik",
"от Lukavaya",
"от LostFilm.tv",
"от LostFilm",
"от Kosmos",
"от Kino Pirate",
"от Kerob",
"от K1RgH0ff",
"от iolegv-RuUu",
"от Ildarec-014",
"от HQGROUP",
"от HQCLUB",
"от HQ-ViDEO",
"от HELLYWOOD",
"от HDReactor",
"от HDGANG",
"от HDCLUB",
"от HD-Zona-X",
"от HAZARD&#039;A",
"от Hansmer",
"от Gravi-TV",
"от Gellard",
"от G1AR",
"от FRiENDS-Club",
"от FreeTorrents-UA",
"от FoC",
"от FLINTFILMS",
"от FinaRGoT",
"от Fenixx)",
"от Fenixx",
"от ExKinoRay.TV",
"от ExKinoRay",
"от ELmusic",
"от E180",
"от Dj Borzyj",
"от Dizell",
"от dima360",
"от DiLLeR",
"от Devlad",
"от DeviL",
"от Demon2901",
"от danis92",
"от Cybertron",
"от Crusader3000",
"от Constin",
"от CMEGroup",
"от CinemaStreet",
"от ChuckSite",
"от CGInfo",
"от cdman",
"от BTT-TEAM",
"от Brux",
"от breznev94(stalone)",
"от BigMOVIEGroup & Hurtom",
"от BigMOVIEGroup",
"от BigFANGroup",
"от BestSound ExKinoRay",
"от Bagvell",
"от AndrewWhite",
"от @PD.&.KAMO",
"от 5 pluh",
"от 2ndra",
"от РиперAM",
"от Pasha74",
"от olmier",
"от LAMPO4KA",
"от 25KADR",
"от PskovLine",
"от SENATORiNFO-TEAM",
"от HeDgEhOt",
"от Kinobomond",
"от WIDDER",
"от Кубик в кубе",
"от Anton299",
"от bxpx",
"от R.G. PLAGUE",
"by MD-TEAM BigFanGroup KVNforAll",
"от HELLWOOD",
"от KalliostroV",
"от Cinema-group info",
"от ELEKTRI4KA",
"от Werdog",
"от Kubik.v.Kube",
"от KVNforAll",
"от «Киномании»",
"by ANDROZZZ",
"от RomeoWhite",
"от @PD KaMo",
"от BUTOVOgroup",
"от Smart's studios",
"от BeeFilm",
"от ENGINEER",
"от KiNOFACK",
"by Xp_Dron",
"от 1001Cinema",
"от R.G. PlayBay",
"от Zt",
"by MYDIMKA",
"от Humochka",
"by FoolMovie",
"от КОля",
"by Shituf",
"by Ultimat",
"от HDTracker",
"от FORREST",
"от Btpo2l",
"by BigFAN",
"by Vitek",
"by R.G.LanTorrent",
"от GoldenShara Studio",
"от ceo54",
"от MaLLleHbKa",
"от TOP",
"by N!CK",
"от Studdio",
"от machete96",
"by Tonic-RuUu (BigFANGroup)",
"от Dimasty",
"от Кинконга",
"от btpo2l",
"от Кураж-Бамбей",
"от HiDt",
"от zim2001",
"от Nemo24",
"by Oleggutsalo",
"от a1chem1st",
"от darktech",
"от Fartuna158",
"от -=ynkas=-",
"от MEGASHARA",
"by FEAR",
"от Шмель",
"by Axwel",
"от Zlatan83",
"от FilmGate",
"от Filmgate",
"от Кураж-Бамбей",
"от КГКМ BigFANGroup",
"от LTarik",
"от POkEmON",
"от R.G. R3PacK",
"от BigFAN Group",
"by DivX (2010) PC",
"by EGO team",
"by Глюк",
"by s0lAr",
"by KVNforAll Veloboss",
"by KVNforAll N!CK",
"by VIDEODB",
"by Kelpie",
"by HQ-ViDEO",
"by Zicos",
"by iolegv-RuUu (BigFANGroup)",
"by ~Romych~",
"by KVNforAll",
"by гаврила",
"by MD-TEAM KVNforAll",
"by Igoraky",
"by Neofilm",
"от Кинозал.ТВ",
"от AudioZona-X",
"от Running Man",
"от HQRips",
"от ANDROZZZ-RuUu",
"by ua2004",
"от Dizell",
"by mdteam",
"by Iriy",
"от Русфильм",
"от Babun",
"by DEMIGOD",
"by iolegv-RuUu",
"от orchidea777",
"от iolegv RuUu",
"от Smart's studios",
"by Allep",
"by iolegv (BigFANGroup)",
"от Good-Cinema",
"by MD-TEAM BigFanGroup KVNforAll",
"от zmei666",
"от Menyk",
"by Alex Smit",
"by ANDROZZZ RuUu (BigFANGroup)",
"от YoFilm",
"от blackmambo",
"от Lexor",
"от iriston",
"от HELLYWOOD",
"от genafon",
"от vladvld",
"by iolegv RuUu (BigFANGroup)",
"от Yuraflash",
"от Postal",
"от BOGUSH",
"by MD-TEAM BigFANGroup KVNforAll",
"от m4r5",
"by tak prosto",
"от Kroha32",
"by Загрызун",
"от Саныча",
"ot othfilm",
"(Кураж-Бамбей)",
"от iolegv RuUu",
"от Dwteam",
"by Jey-Shock",
"от Кураж-Бамбей",
"от Новый звук",
"-Kuraj-Bambey",
"by AleMak",
"by iolegv",
"от 1001 Cinema",
"от  Кураж-Бамбей",
"(BigFANGroup)",
"by FS-Media",
"от serj69",
"(от maksnev82 )",
"от 1001синема",
"iolegv-RuUu",
"от -=CARBON=-",
"от Квадрат Малевича",
"by El.dar",
"mi24",
"от ESiR",
"() FB2",
"от bigfree-torrents",
"[озвучка Кураж-Бамбей]",
"[озвучка ]",
"- fanatus",
"от Kinozal",
"by LGFs",
"()",
"FB2",
"Релиз .TV",
"by DaNsteR",
"от tonic-Хоттабыч",
"(дубляж ТНТ)",
"- 1001cinema",
"[tfile.ru]"
);

$one_t = array_unique($one_t);


$name = str_ireplace($one_t, "", $name);



$one_tt=array("Calitate","Video","Sunet","Fiier","Despre emisiune","Traducere","Durata");
$one_td=array("Качество","Видео","Звук","Файл","О передаче","Перевод","Длительность");
$name = str_ireplace($one_tt, $one_td, $name);





$name_orig = trim(htmlspecialchars_uni(preg_replace("/\[([0-9_-]+(.*?))\]/is", "(\\1)", $name)));

$name = trim(htmlspecialchars_uni(preg_replace("/\[([0-9_-]+(.*?))\]/is", "(\\1)", $name)));
$name_clear = trim(htmlspecialchars_uni(preg_replace("/\[((\s|.)+?)\]/", "", $name)));


if (stristr($name_clear,'[') && !stristr($name_clear,']'))
$name_clear = $name_clear."]";

echo "Было: ".$name_orig;
echo "<br>";
echo "Стало: ".($name_orig==$name_clear ? $name_clear:"<b>$name_clear</b>");
echo "<br>";

if ($name_orig<>$name_clear && strlen($name_clear) >= 12)
$torrecomment = sqlesc(date("Y-m-d") . " - Фильтрация названия ($name_orig).\n");
else
$torrecomment = sqlesc(date("Y-m-d") . " - Фильтрация названия успешна.\n");



//if ($name_orig<>$name_clear)
sql_query("UPDATE torrents SET name=".sqlesc($name_clear).", torrent_com = CONCAT_WS('',$torrecomment, torrent_com) WHERE id=".sqlesc($res["id"])) or sqlerr(__FILE__, __LINE__);
}



	global $CURUSER, $ss_uri, $tracker_lang, $queries, $tstart, $query_stat, $querytime;
	
	
		foreach ($query_stat as $key => $value) {
			print("<div>[<b>".($key+1)."</b>] => <b>
			".($value["seconds"] <= 0.0009 ? "<font color=\"#0468f1\" title=\"Сверхбыстрый запрос. Время исполнения отличное.\">".$value["seconds"]."</font>":"
		    ".($value["seconds"] >= 0.01 ? "<font color=\"red\" title=\"Рекомендуется оптимизировать запрос. Время исполнения превышает норму.\">".$value["seconds"]."</font>" : "<font color=\"blue\" title=\"Запрос не нуждается в оптимизации. Время исполнения допустимое.\">".$value["seconds"]."</font>" )."
		    ")."
		  </b> [".$value["query"]."]</div>\n");
		}

die;
?>