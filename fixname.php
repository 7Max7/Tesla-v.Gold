<?
require "include/bittorrent.php";
dbconn(false);
stdheadchat("������� �������� � ���������");




 $rezult = sql_query("SELECT id, added,subject FROM news") or sqlerr(__FILE__, __LINE__);
 $myrow = mysql_fetch_array($rezult);

 do
 {
 $n = 1;        //  ����� � ��������� ���������� ������ (����� ������)
 $e = 12;        //  ����� ������� ����� ��������� (����� �������)
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
	echo "�������� ���� �� <b>$dat</b> � �������: <b>".$myrow["subject"]."</b><br>";

 }

while($myrow = mysql_fetch_array($rezult)); 



die;



/*
require "include/benc.php";

$sql1 = sql_query("SELECT name, id FROM torrents WHERE multitracker='yes' ORDER BY id DESC LIMIT 100") or sqlerr(__FILE__, __LINE__);

while ($res = mysql_fetch_assoc($sql1)){

$ifilename = ROOT_PATH."torrents/".$res["id"].".torrent";

if(!file_exists($ifilename))
echo "��� ����� ".$res["id"]." <br>";

else {
$dict = bdec_file($ifilename, 1024000);

//$dict=@bdec(@benc($dict)); 
@list($info) = @dict_check_t($dict, "info");
$infohash = sha1($info["string"]);
//echo "$infohash <br>";

if (!empty($dict['value']['info']['value']['private'])){
echo ("��������� ������� ���� - <a href=details.php?id=".$res["id"].">".$res["id"]."</a> <br>");
}

//print_r($dict);


}

}

die(" ����� .");

*/





//sql_query("ALTER TABLE `torrents` ADD `checkpeers` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `added`") or sqlerr(__FILE__,__LINE__);


/*
sql_query("UPDATE torrents SET category='13' WHERE category='14' AND (torrent_com LIKE '%dvdrip%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

$zatronyto = mysql_affected_rows();

echo "��������� $zatronyto �����.";
die;




sql_query("UPDATE torrents SET multi_time = '0000-00-00 00:00:00' WHERE visible='no' AND multi_time <> '0000-00-00 00:00:00' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

$zatronyto = mysql_affected_rows();

echo "��������� $zatronyto �����.";
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

echo "<a href=\"details.php?id=".$res["id"]."\">".$res["name"]."</a> � ".$res["category"];
echo " - ������ ��������� <b>$mycateyrogy</b>";

sql_query("UPDATE torrents SET category=".sqlesc($mycateyrogy)." WHERE id=".$res["id"]) or sqlerr(__FILE__,__LINE__);

echo "<br>";
}



unset($ca,$mycateyrogy);

}


echo "<script>setTimeout('document.location.href=\"fixname.php\"', 10000);</script>"; 



*/



sql_query("UPDATE torrents SET category='27' WHERE category<>'27' AND (name LIKE '%���%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 24<br>";



sql_query("UPDATE torrents SET category='10' WHERE category='4' AND (descr LIKE '%mp3%' AND descr LIKE '%������%') AND 
(name LIKE '%�����������%' OR name LIKE '%Discography%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 24<br>";



sql_query("UPDATE torrents SET category='10' WHERE category='4' AND (name LIKE '%FLAC%' OR name LIKE '%APE%' OR name LIKE '%ALAC%' OR name LIKE '%/WAV/%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 24<br>";


sql_query("UPDATE torrents SET category='10' WHERE category='4' AND (torrent_com LIKE '%(CBR)%' OR torrent_com LIKE '%(VBR)%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 25<br>";


sql_query("UPDATE torrents SET category='10' WHERE category='4' AND (name LIKE '%WMA%' OR name LIKE '%MP3%' OR name LIKE '%AAC%' OR name LIKE '%���������%'OR name LIKE '%soundtrack%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 26<br>";


sql_query("UPDATE torrents SET category='10' WHERE category='4' AND (torrent_com LIKE '%WMA%' OR torrent_com LIKE '%APE%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 27<br>";





/*
////////////////////// ��� ������ ///////////////////////////

sql_query("UPDATE torrents SET category='22' WHERE category='4' AND (name LIKE '%TVRip%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 5<br>";




sql_query("UPDATE torrents SET category='14' WHERE category='4' AND (name LIKE '%Blu-Ray%' OR name LIKE '%Blue-ray%' OR name LIKE '%HDTV%' OR name LIKE '%HDTVRip%' OR name LIKE '%HDRip%' OR name LIKE '%BRRip%' OR name LIKE '%BDRip%' OR name LIKE '%HDRip%' OR name LIKE '%BDRemux%' OR name LIKE '%720p%' OR name LIKE '%1080p%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 6<br>";



sql_query("UPDATE torrents SET category='13' WHERE category='4' AND (name LIKE '%VHSRip%' OR name LIKE '%DVDrip%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 8<br>";


sql_query("UPDATE torrents SET category='16' WHERE category='4' AND (name LIKE '%FB2%' OR name LIKE '%TXT%' OR name LIKE '%DjVu%' OR name LIKE '%PDF%' OR name LIKE '%rtf%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 9<br>";


sql_query("UPDATE torrents SET category='5' WHERE category='4' AND (name LIKE '%(%' AND name LIKE '%)%' AND name LIKE '%PC%') AND size>'1221225472' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 10<br>";

sql_query("UPDATE torrents SET category='26' WHERE category='4' AND (name LIKE '%(%' AND name LIKE '%)%' AND name LIKE '%PC%') AND size<'121225472' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 11<br>";


sql_query("UPDATE torrents SET category='18' WHERE category='4' AND (name LIKE '%(%' AND name LIKE '%)%' AND name LIKE '%MOV%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 12<br>";


sql_query("UPDATE torrents SET category='10' WHERE category='4' AND (name LIKE '%losless%' OR name LIKE '%mp3%' OR name LIKE '%M4A%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 7<br>";



sql_query("UPDATE torrents SET category='16' WHERE descr LIKE '%� ����� �����%' AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 1<br>";


sql_query("UPDATE torrents SET category='15' WHERE (torrent_com LIKE '%DVD-5%' OR torrent_com LIKE '%DVD5%' OR torrent_com LIKE '%DVD9%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 2<br>";


sql_query("UPDATE torrents SET category='8' WHERE category<>'8' AND (name LIKE '%PSP%' OR name LIKE '%PS2%' OR name LIKE '%XBOX360%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 3<br>";

sql_query("UPDATE torrents SET category='14' WHERE category='4' AND (name LIKE '%BD Remux%' OR name LIKE '%BluRay%' OR torrent_com LIKE '%Blu-Ray%' OR torrent_com LIKE '%HDTVRip%' OR torrent_com LIKE '%BDRip%' OR torrent_com LIKE '%BDRemux%' OR torrent_com LIKE '%720p%' OR torrent_com LIKE '%1080p%' OR torrent_com LIKE '%1080i%' OR name LIKE '%1080i%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);


echo "��������� ".mysql_affected_rows()." �����. 4<br>";

sql_query("UPDATE torrents SET category='22' WHERE category='4' AND (name LIKE '%SATRip%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 14<br>";

sql_query("UPDATE torrents SET category='11' WHERE (descr LIKE '%LostFilm%' OR descr LIKE '%NovaFilm%' OR descr LIKE '%Kravec%' OR descr LIKE '%������%' OR descr LIKE '%GSGroup %' OR descr LIKE '%Kuraj-Bambey%' OR descr LIKE '%�����-������%' OR descr LIKE '%����� � ����%'OR descr LIKE '%KvadratMalevicha%') and category<>'11' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 15<br>";

sql_query("UPDATE torrents SET category='11' WHERE (torrent_com LIKE '%LostFilm%' OR torrent_com LIKE '%NovaFilm%' OR torrent_com LIKE '%Studdio%' OR name LIKE '%NewStudio%' OR torrent_com LIKE '%Kvadrat%') AND category<>'11' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 16<br>";


sql_query("UPDATE torrents SET category='8' WHERE (torrent_com LIKE '%PS2%' OR torrent_com LIKE '%PS3%' OR torrent_com LIKE '%PSP%' OR torrent_com LIKE '%PSX-PSP%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 19<br>";


sql_query("UPDATE torrents SET category='26' WHERE category='4' AND name LIKE '%��%' AND name LIKE '%Multi%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 19.2<br>";

sql_query("UPDATE torrents SET category='26' WHERE category='4' AND name LIKE '%PC%' AND name LIKE '%Repack%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 19.3<br>";


sql_query("UPDATE torrents SET category='1' WHERE category='4' AND (name LIKE '%MDF%' OR name LIKE '%ISO%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 19.4<br>";


sql_query("UPDATE torrents SET category='27' WHERE category='4' AND torrent_com LIKE '%iPhone%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 20<br>";


sql_query("UPDATE torrents SET category='5' WHERE category='4' AND name LIKE '%��������%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 20.2<br>";


sql_query("UPDATE torrents SET category='12' WHERE category='4' AND (torrent_com LIKE '%anime%' OR torrent_com LIKE '%AniDUB%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 21<br>";


sql_query("UPDATE torrents SET category='15' WHERE category='4' AND size>'1221225472' AND (name LIKE '%DVD-9%' OR name LIKE '%DVD-5%' OR name LIKE '%DVD9%' OR name LIKE '%DVD5%' OR name LIKE '%DVD%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 23.2<br>";


sql_query("UPDATE torrents SET category='10' WHERE (name LIKE '%(OST)' OR name LIKE '%.OST' OR torrent_com LIKE '%OST)' OR torrent_com LIKE '%OST-%' OR torrent_com LIKE '%OST -%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 30<br>";


sql_query("UPDATE torrents SET category='16' WHERE (name LIKE '%ebook%' OR name LIKE '%�����%' OR name LIKE '%TXT%' OR name LIKE '%RTF%' OR name LIKE '%OCR%' OR name LIKE '%FB2%' OR name LIKE '%CHM%' OR name LIKE '%PDF%' OR name LIKE '%������%' OR name LIKE '%Djvu %' OR name LIKE '%doc%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 31<br>";


////////////////////// ��� ������ ///////////////////////////
*/













/*
sql_query("UPDATE torrents SET category='16' WHERE descr LIKE '%� ����� �����%' AND category<>'16' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 1<br>";




sql_query("UPDATE torrents SET category='15' WHERE (torrent_com LIKE '%DVD-5%' OR torrent_com LIKE '%DVD5%' OR torrent_com LIKE '%DVD9%') AND category<>'15' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 2<br>";





sql_query("UPDATE torrents SET category='8' WHERE category<>'8' AND (name LIKE '%PSP%' OR name LIKE '%PS2%' OR name LIKE '%XBOX360%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);



echo "��������� ".mysql_affected_rows()." �����. 3<br>";





sql_query("UPDATE torrents SET category='14' WHERE category<>'14' AND (name LIKE '%BD Remux%' OR name LIKE '%BluRay%' OR torrent_com LIKE '%Blu-Ray%' OR torrent_com LIKE '%HDTVRip%' OR torrent_com LIKE '%BDRip%' OR torrent_com LIKE '%BDRemux%' OR torrent_com LIKE '%720p%' OR torrent_com LIKE '%1080p%' OR torrent_com LIKE '%1080i%' OR name LIKE '%1080i%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);


echo "��������� ".mysql_affected_rows()." �����. 4<br>";













sql_query("UPDATE torrents SET category='25' WHERE category='4' AND (name LIKE '%GIF%' OR name LIKE '%PNG%' OR name LIKE '%JPG%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 13<br>";








sql_query("UPDATE torrents SET category='22' WHERE category='4' AND (name LIKE '%SATRip%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 14<br>";


sql_query("UPDATE torrents SET category='22' WHERE (descr LIKE '%�����%' AND descr LIKE '%����%') and category<>'22' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 14.2<br>";


sql_query("UPDATE torrents SET category='11' WHERE (descr LIKE '%LostFilm%' OR descr LIKE '%NovaFilm%' OR descr LIKE '%Kravec%' OR descr LIKE '%������%' OR descr LIKE '%GSGroup %' OR descr LIKE '%Kuraj-Bambey%' OR descr LIKE '%�����-������%' OR descr LIKE '%����� � ����%'OR descr LIKE '%KvadratMalevicha%') and category<>'11' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 15<br>";



sql_query("UPDATE torrents SET category='11' WHERE (torrent_com LIKE '%LostFilm%' OR torrent_com LIKE '%NovaFilm%' OR torrent_com LIKE '%Studdio%' OR name LIKE '%NewStudio%' OR torrent_com LIKE '%Kvadrat%') AND category<>'11' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 16<br>";



sql_query("UPDATE torrents SET category='11' WHERE name LIKE '%season%' AND name LIKE '%episode%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 17<br>";


sql_query("UPDATE torrents SET category='10' WHERE torrent_com LIKE '%[Dance]%' OR torrent_com LIKE '%[Soundtrack]%' OR name LIKE '%MP3%' OR name LIKE '%VA -%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 18<br>";


sql_query("UPDATE torrents SET category='8' WHERE torrent_com LIKE '%PS2%' OR torrent_com LIKE '%PS3%' OR torrent_com LIKE '%PSP%' OR torrent_com LIKE '%PSX-PSP%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 19<br>";





sql_query("UPDATE torrents SET category='26' WHERE category<>'26' AND name LIKE '%��%' AND name LIKE '%Multi%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 19.2<br>";




sql_query("UPDATE torrents SET category='26' WHERE category<>'26' AND name LIKE '%PC%' AND name LIKE '%Repack%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 19.3<br>";



sql_query("UPDATE torrents SET category='1' WHERE category<>'1' AND (name LIKE '%MDF%' OR name LIKE '%ISO%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 19.4<br>";

sql_query("UPDATE torrents SET category='27' WHERE category<>'27' AND torrent_com LIKE '%iPhone%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 20<br>";


sql_query("UPDATE torrents SET category='5' WHERE category<>'5' AND name LIKE '%��������%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 20.2<br>";


sql_query("UPDATE torrents SET category='12' WHERE category<>'12' AND torrent_com LIKE '%AniDUB%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 21<br>";


sql_query("UPDATE torrents SET category='25' WHERE category<>'25' AND name LIKE '%����%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 22<br>";


sql_query("UPDATE torrents SET category='18' WHERE category<>'18' AND size<'1221225472' AND name LIKE '%DVD%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 23<br>";


sql_query("UPDATE torrents SET category='15' WHERE category<>'15' AND size>'1221225472' AND (name LIKE '%DVD-9%' OR name LIKE '%DVD-5%' OR name LIKE '%DVD9%' OR name LIKE '%DVD5%' OR name LIKE '%DVD%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 23.2<br>";




sql_query("UPDATE torrents SET category='10' WHERE category<>'10' AND name LIKE '%soundtrack%' OR name LIKE '%c��������%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 28<br>";


sql_query("UPDATE torrents SET category='10' WHERE category<>'10' AND name LIKE '%OST%' AND name LIKE '%c��������%' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 29<br>";


sql_query("UPDATE torrents SET category='10' WHERE (name LIKE '%(OST)' OR name LIKE '%.OST' OR torrent_com LIKE '%OST)' OR torrent_com LIKE '%OST-%' OR torrent_com LIKE '%OST -%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 30<br>";


sql_query("UPDATE torrents SET category='16' WHERE (name LIKE '%ebook%' OR name LIKE '%�����%' OR name LIKE '%TXT%' OR name LIKE '%RTF%' OR name LIKE '%OCR%' OR name LIKE '%FB2%' OR name LIKE '%CHM%' OR name LIKE '%PDF%' OR name LIKE '%������%' OR name LIKE '%Djvu %' OR name LIKE '%doc%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 31<br>";

 
sql_query("UPDATE torrents SET category='16' WHERE (descr LIKE '%���������������%' AND descr LIKE '%�������%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 32<br>";


sql_query("UPDATE torrents SET category='10' WHERE category<>'10' AND (descr LIKE '%�������%' AND descr LIKE '%kbps%' AND descr LIKE '%������%' AND descr LIKE '%����%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 33<br>";


sql_query("UPDATE torrents SET category='10' WHERE category<>'10' AND (name LIKE '%MP3%' AND descr LIKE '%� ������%' AND descr LIKE '%��������%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 34<br>";


sql_query("UPDATE torrents SET category='10' WHERE category<>'10' AND (descr LIKE '%Tracklist%' AND descr LIKE '%Genre%' AND descr LIKE '%Quality%') LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 35<br>";


sql_query("UPDATE torrents SET category='11' WHERE (name LIKE '%�����%' OR name LIKE '%�����%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 36<br>";


sql_query("UPDATE torrents SET category='11' WHERE (name LIKE '%�����%' OR name LIKE '%�����%') AND category='4' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 37<br>";





sql_query("UPDATE torrents SET category='27' WHERE (torrent_com LIKE '%Mobiles%' OR torrent_com LIKE '%[Java]%') AND category<>'27' LIMIT 10000") or sqlerr(__FILE__,__LINE__);

echo "��������� ".mysql_affected_rows()." �����. 38<br>";


die;
*/



$sql = sql_query("SELECT name,id FROM torrents WHERE category <> '11' AND name LIKE '%[%' AND torrent_com NOT LIKE '%����������%' AND owner='92' LIMIT 10000") or sqlerr(__FILE__, __LINE__);

while ($res = mysql_fetch_assoc($sql)){

$one=array("Season ","Seasons ","Episodes ","Episode "," [spoiler=Trailer]");
$two=array("C���� ","C����� ","����� ","����� ","[spoiler=�������]");

$name = str_replace($one, $two, $res["name"]);


$name = str_replace("�� �� ", "�� ", $name);


$one_t = array(
"�� RollHD", "�� HELLRAiSER", "�� HELLYWOOD", "�� CGInfo", "�� HQ-ViDEO", "�� Shinobi", "�� Infernum", "�� Friends-Club", "�� FRiENDS-Club", "�� DoroshDan", "�� PlanetaUA", "�� ����", "by Veloboss", "by GENADIY", "by HQ-ViDEO", "by BigFan", "�� R.G. PlayBay", "�� RomeoWhite", "by RuUu", "by maksnev82", "�� doberman", "�� Drakula", "by demon�����", "�� FreeHD", "�� kubik v kube", "�� FS-Media", "�� Kvadratmalevicha", "by parole", "by �������� (BigFANGroup)", "by R.G.������4�", "by the sun", "by OZAGIS", "by BigFANGroup", "by Infineon", "�� R.G ReCoding", "by R.G. 48 seconds", "�� DHT-Movies", "by FS-Media", "by Arow Malossi", "by Kino Pirate", "�� LPF TV", "�� Overlord", "�� 0day ", "�� HAZARD'A", "�� R.G Repacker's", "by R.G.R3PacK", "�� CtrlHD", "�� ESiR", "by 4Euro", "�� New-TrackerUA", "�� M.I.B", "�� EuReKa", "�� Linkorn", "�� iolegv", "�� Neo-Sound.ru", "�� SAMLL-RiP", "�� CHDBits", "�� RI4MOND", "�� Smart's studios", "�� EbP", "�� TORRENTSES", "by NK", "by bizarrevic", "by VANGA", "by Basilevs", "�� trexmernii", "by XENON", "by morganvolter", "by HDClub", "�� MadChester ����� � ���� NovaFiLM", "�� EuReKA", "by ELEKTRI4KA", "by 5.1", "by SneGGs", "�� Oleggutsalo", "�� zackary", "by VIDODB", "�� SkyeFilmTV", "by Suprug", "by rp0Mk0cTb", "by SDHQ", "�� Doctor No.", "�� SoftClub", "�� TDiTP", "�� �����", "�� F_i_X", "�� 1001 Cinema", "�� Drakul�", "�� K�Mo", "by KaMo", "by Illusion", "�� Psycho-Share", "by X-DIGITAL (BigFANGroup)", "�� InterfaceLift", "�� KaMo", "by Vladas", "by HA3APET PC", "�� RELOADED", "�� ����", "by An Ocean Wave", "by HA3APET", "�� lenpas", "�� W I", "�� KiLR0Y", "�� games_vandal", "�� EPIDEMZ", "by DESAN", "�� ���������", "�� BaibaKo", "�� HD Tracker", "�� -DaRkY-", "�� lev31071977", "by Kommunist", "�� Smart's Studios", "�� Player1993", "�� Overcast", "�� Ildarec014", "�� Bumblebee", "�� SmallRip", "by games vandal", "�� R.G.", "�� SoftClub/EA Russia", "by Andrjuha-Bratan", "by Shel", "�� ������ CGInfo", "by Mr.legal", "by Megashara", "by Padre mp4", "�� Zhmak", "by DJ ART", "�� FoolMovie", "�� iolegv RuUu", "by boban39", "by stalone", "�� �@�o", "�� AllSeries Info CGInfo", "�� Dime HQ-VIDEO", "�� Geralt iz Rivii", "by HDCiub", "by d_kos", "by doberman", "�� HQ-films", "�� epu2009", "by d kos", "by rus-tracker", "�� Mari", "�� VIDEODB", "by Studdio", "�� FRiENDS Club", "by ��������", "by Tiger", "by Virelic", "by AZBUKASOFTA", "�� Lali", "by trexmernii", "�� Instigator-NN", "by R.G. Beautiful Thieves", "�� ��������", "�� 3m1L", "�� NOLIMITS-TEAM", "�� R.G. \"48 seconds\"", "�� ANDROZZZ", "by djip", "�� boss89", "�� StarTorrent", "�� z10yded", "�� ���", "by martin", "by \"R.G. 48 Seconds\"", "�� ������� Suprug", "by qDan", " - �����AM", "�� MashystiK", "�� JoomlaXTC", "�� Joomlapraise", "�� greys2008 Arkadiy", "�� SkeT", "by stalone ( stalone-2)", "by R.O.T.O.R", "by Fool Movie", "by KVNforAll N!CK", "by KVNforAll Ruuu", "by BigSOUNDgroup", "by yuraflash", "by POkEmON", "by v1nt", "�� HotRabbit", "�� ����777", "�� PsihoShare", "�� �����", "by s0lAr", "�� HeDgEhOt", "�� �osmos", "�� R.G.R3PacK", "by 7lobiN", "by dimich", "�� RAMpage CHG", "�� R.G. CodeRs", "�� �������� (BigFANGroup)", "�� ������", "�� ����� | Remastered", "�� �����������", "�� ����������", "�� ������-�����", "�� ������� & Suprug", "�� �����AM", "�� ������", "�� �����-������", "�� ����� ������", "�� ����� � ����", "�� ������", "�� �������SAT", "�� �����PR Studio", "�� ���NN��", "�� ������", "�� Zona-X", "�� YOOtheme", "�� WiKi)", "�� Wegas Digital", "�� wakal", "�� volka", "�� Voland", "�� VO-Production", "�� Vladislav71", "�� ViP", "�� VelArt", "�� vadi", "�� Ultra", "�� UaTeam", "�� TVShows", "�� Traumer", "�� SuperNatural", "�� StopFilm", "�� Spieler", "�� SMALL-RiP", "�� Shevon", "�� �������", "�� Shepards", "�� sergeorg", "�� Sergei7721", "�� RuUu, iolegv & ANDROZZZ", "�� RuUu (BigFANGroup)", "�� RuUu", "�� RUSSOUND", "�� RP4 CHG", "�� RommyFilm", "�� RocketTheme", "�� RiperAM", "�� RIPER.AM", "�� RelizLab", "�� RadioXyLigaN", "�� R.G.Spieler", "�� R.G. ��������", "�� R.G. ��������", "�� R.G. Repackers Bay", "�� R.G. ReCoding", "�� R.G. Catalyst", "�� P������", "�� Punisher", "�� PowerTracker", "�� Pasha74", "�� ParadiSe", "�� OneFilm", "�� Oday", "�� NovaFilm", "�� NewStudio", "�� NaumJ", "�� Music80", "�� mk2", "�� mi24", "�� Martin", "�� maksnev82", "�� m0j0", "�� Lunatik", "�� Lukavaya", "�� LostFilm.tv", "�� LostFilm", "�� Kosmos", "�� Kino Pirate", "�� Kerob", "�� K1RgH0ff", "�� iolegv-RuUu", "�� Ildarec-014", "�� HQGROUP", "�� HQCLUB", "�� HDReactor", "�� HDGANG", "�� HDCLUB", "�� HD-Zona-X", "�� Hansmer", "�� Gravi-TV", "�� Gellard", "�� G1AR", "�� FreeTorrents-UA", "�� FoC", "�� FLINTFILMS", "�� FinaRGoT", "�� Fenixx)", "�� Fenixx", "�� ExKinoRay.TV", "�� ExKinoRay", "�� ELmusic", "�� E180", "�� Dj Borzyj", "�� Dizell", "�� dima360", "�� DiLLeR", "�� Devlad", "�� DeviL", "�� Demon2901", "�� danis92", "�� Cybertron", "�� Crusader3000", "�� Constin", "�� CMEGroup", "�� CinemaStreet", "�� ChuckSite", "�� cdman", "�� BTT-TEAM", "�� Brux", "�� breznev94(stalone)", "�� BigMOVIEGroup & Hurtom", "�� BigMOVIEGroup", "�� BigFANGroup", "�� BestSound ExKinoRay", "�� Bagvell", "�� AndrewWhite", "�� @PD.&.KAMO", "�� 5 pluh", "�� 2ndra", "�� olmier", "�� LAMPO4KA", "�� 25KADR", "�� PskovLine", "�� SENATORiNFO-TEAM", "�� Kinobomond", "�� WIDDER", "�� ����� � ����", "�� Anton299", "�� bxpx", "�� R.G. PLAGUE", "by MD-TEAM BigFanGroup KVNforAll", "�� HELLWOOD", "�� KalliostroV", "�� Cinema-group info", "�� ELEKTRI4KA", "�� Werdog", "�� Kubik.v.Kube", "�� KVNforAll", "�� ����������", "by ANDROZZZ", "�� @PD KaMo", "�� BUTOVOgroup", "�� BeeFilm", "�� ENGINEER", "�� KiNOFACK", "by Xp_Dron", "�� 1001Cinema", "�� Zt", "by MYDIMKA", "�� Humochka", "by FoolMovie", "�� ����", "by Shituf", "by Ultimat", "�� HDTracker", "�� FORREST", "�� Btpo2l", "by BigFAN", "by Vitek", "by R.G.LanTorrent", "�� GoldenShara Studio", "�� ceo54", "�� MaLLleHbKa", "�� TOP", "by N!CK", "�� Studdio", "�� machete96", "by Tonic-RuUu (BigFANGroup)", "�� Dimasty", "�� ��������", "�� btpo2l", "�� HiDt", "�� zim2001", "�� Nemo24", "by Oleggutsalo", "�� a1chem1st", "�� darktech", "�� Fartuna158", "�� -=ynkas=-", "�� MEGASHARA", "by FEAR", "�� �����", "by Axwel", "�� Zlatan83", "�� FilmGate", "�� Filmgate", "�� ���� BigFANGroup", "�� LTarik", "�� POkEmON", "�� R.G. R3PacK", "�� BigFAN Group", "by DivX (2010) PC", "by EGO team", "by ����", "by KVNforAll Veloboss", "by VIDEODB", "by Kelpie", "by Zicos", "by iolegv-RuUu (BigFANGroup)", "by ~Romych~", "by KVNforAll", "by �������", "by MD-TEAM KVNforAll", "by Igoraky", "by Neofilm", "�� �������.��", "�� AudioZona-X", "�� Running Man", "�� HQRips", "�� ANDROZZZ-RuUu", "by ua2004", "by mdteam", "by Iriy", "�� ��������", "�� Babun", "by DEMIGOD", "by iolegv-RuUu", "�� orchidea777", "by Allep", "by iolegv (BigFANGroup)", "�� Good-Cinema", "�� zmei666", "�� Menyk", "by Alex Smit", "by ANDROZZZ RuUu (BigFANGroup)", "�� YoFilm", "�� blackmambo", "�� Lexor", "�� iriston", "�� genafon", "�� vladvld", "by iolegv RuUu (BigFANGroup)", "�� Yuraflash", "�� Postal", "�� BOGUSH", "by MD-TEAM BigFANGroup KVNforAll", "�� m4r5", "by tak prosto",
"�� RollHD",
"�� Smart&#039;s studios",
"�� CRiSC (HDBits internal)",
"�� HELLRAiSER",
"�� HELLYWOOD",
"�� CGInfo",
"�� HQ-ViDEO",
"�� Shinobi",
"�� Infernum",
"�� Friends-Club",
"�� FRiENDS-Club",
"�� DoroshDan",
"�� PlanetaUA",
"�� ����",
"by Veloboss",
"by GENADIY",
"by HQ-ViDEO",
"by BigFan",
"�� R.G. PlayBay",
"�� FRiENDS-Club",
"�� RomeoWhite",
"by RuUu",
"by maksnev82",
"�� doberman",
"�� Drakula",
"by demon�����",
"�� FreeHD",
"�� kubik v kube",
"�� FS-Media",
"�� Kvadratmalevicha",
"by parole",
"by �������� (BigFANGroup)",
"by R.G.������4�",
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
"�� LPF TV",
"by R.G. Beautiful Thieves",
"�� DHT-Movies",
"by v1nt",
"�� HotRabbit",
"�� ����777",
"�� PsihoShare",
"�� �����",
"by s0lAr",
"�� HeDgEhOt",
"�� �osmos",
"�� R.G.R3PacK",
"by 7lobiN",
"by dimich",
"by maksnev82",
"�� HAZARD'A",
"�� PlanetaUA",
"�� RAMpage CHG",
"�� R.G. CodeRs",
"�� PlanetaUA",
"�� �������� (BigFANGroup)",
"�� ������",
"�� ����� | Remastered",
"�� �����������",
"�� ����������",
"�� ������-�����",
"�� ������� & Suprug",
"�� �����AM",
"�� ������",
"�� �����-������",
"�� ����� ������",
"�� ����� � ����",
"�� ������",
"�� �������SAT",
"�� �����PR Studio",
"�� ���NN��",
"�� ������",
"�� Zona-X",
"�� z10yded",
"�� YOOtheme",
"�� WiKi)",
"�� Wegas Digital",
"�� wakal",
"�� volka",
"�� Voland",
"�� VO-Production",
"�� Vladislav71",
"�� ViP",
"�� VelArt",
"�� vadi",
"�� Ultra",
"�� UaTeam",
"�� TVShows",
"�� Traumer",
"�� SuperNatural",
"�� StopFilm",
"�� Spieler",
"�� SMALL-RiP",
"�� Shevon",
"�� �������",
"�� Shepards",
"�� sergeorg",
"�� Sergei7721",
"�� RuUu, iolegv & ANDROZZZ",
"�� RuUu (BigFANGroup)",
"�� RuUu",
"�� RUSSOUND",
"�� RP4 CHG",
"�� RommyFilm",
"�� RocketTheme",
"�� RiperAM",
"�� RIPER.AM",
"�� RelizLab",
"�� RadioXyLigaN",
"�� R.G.Spieler",
"�� R.G. ��������",
"�� R.G. ��������",
"�� R.G. Repackers Bay",
"�� R.G. ReCoding",
"�� R.G. Catalyst",
"�� P������",
"�� Punisher",
"�� PowerTracker",
"�� Pasha74",
"�� ParadiSe",
"�� OneFilm",
"�� Oday",
"�� NovaFilm",
"�� NOLIMITS-TEAM",
"�� NewStudio",
"�� NaumJ",
"�� Music80",
"�� mk2",
"�� mi24",
"�� Martin",
"�� maksnev82",
"�� m0j0",
"�� Lunatik",
"�� Lukavaya",
"�� LostFilm.tv",
"�� LostFilm",
"�� Kosmos",
"�� Kino Pirate",
"�� Kerob",
"�� K1RgH0ff",
"�� iolegv-RuUu",
"�� Ildarec-014",
"�� HQGROUP",
"�� HQCLUB",
"�� HQ-ViDEO",
"�� HELLYWOOD",
"�� HDReactor",
"�� HDGANG",
"�� HDCLUB",
"�� HD-Zona-X",
"�� HAZARD&#039;A",
"�� Hansmer",
"�� Gravi-TV",
"�� Gellard",
"�� G1AR",
"�� FRiENDS-Club",
"�� FreeTorrents-UA",
"�� FoC",
"�� FLINTFILMS",
"�� FinaRGoT",
"�� Fenixx)",
"�� Fenixx",
"�� ExKinoRay.TV",
"�� ExKinoRay",
"�� ELmusic",
"�� E180",
"�� Dj Borzyj",
"�� Dizell",
"�� dima360",
"�� DiLLeR",
"�� Devlad",
"�� DeviL",
"�� Demon2901",
"�� danis92",
"�� Cybertron",
"�� Crusader3000",
"�� Constin",
"�� CMEGroup",
"�� CinemaStreet",
"�� ChuckSite",
"�� CGInfo",
"�� cdman",
"�� BTT-TEAM",
"�� Brux",
"�� breznev94(stalone)",
"�� BigMOVIEGroup & Hurtom",
"�� BigMOVIEGroup",
"�� BigFANGroup",
"�� BestSound ExKinoRay",
"�� Bagvell",
"�� AndrewWhite",
"�� @PD.&.KAMO",
"�� 5 pluh",
"�� 2ndra",
"�� �����AM",
"�� Pasha74",
"�� olmier",
"�� LAMPO4KA",
"�� 25KADR",
"�� PskovLine",
"�� SENATORiNFO-TEAM",
"�� HeDgEhOt",
"�� Kinobomond",
"�� WIDDER",
"�� ����� � ����",
"�� Anton299",
"�� bxpx",
"�� R.G. PLAGUE",
"by MD-TEAM BigFanGroup KVNforAll",
"�� HELLWOOD",
"�� KalliostroV",
"�� Cinema-group info",
"�� ELEKTRI4KA",
"�� Werdog",
"�� Kubik.v.Kube",
"�� KVNforAll",
"�� ����������",
"by ANDROZZZ",
"�� RomeoWhite",
"�� @PD KaMo",
"�� BUTOVOgroup",
"�� Smart's studios",
"�� BeeFilm",
"�� ENGINEER",
"�� KiNOFACK",
"by Xp_Dron",
"�� 1001Cinema",
"�� R.G. PlayBay",
"�� Zt",
"by MYDIMKA",
"�� Humochka",
"by FoolMovie",
"�� ����",
"by Shituf",
"by Ultimat",
"�� HDTracker",
"�� FORREST",
"�� Btpo2l",
"by BigFAN",
"by Vitek",
"by R.G.LanTorrent",
"�� GoldenShara Studio",
"�� ceo54",
"�� MaLLleHbKa",
"�� TOP",
"by N!CK",
"�� Studdio",
"�� machete96",
"by Tonic-RuUu (BigFANGroup)",
"�� Dimasty",
"�� ��������",
"�� btpo2l",
"�� �����-������",
"�� HiDt",
"�� zim2001",
"�� Nemo24",
"by Oleggutsalo",
"�� a1chem1st",
"�� darktech",
"�� Fartuna158",
"�� -=ynkas=-",
"�� MEGASHARA",
"by FEAR",
"�� �����",
"by Axwel",
"�� Zlatan83",
"�� FilmGate",
"�� Filmgate",
"�� �����-������",
"�� ���� BigFANGroup",
"�� LTarik",
"�� POkEmON",
"�� R.G. R3PacK",
"�� BigFAN Group",
"by DivX (2010) PC",
"by EGO team",
"by ����",
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
"by �������",
"by MD-TEAM KVNforAll",
"by Igoraky",
"by Neofilm",
"�� �������.��",
"�� AudioZona-X",
"�� Running Man",
"�� HQRips",
"�� ANDROZZZ-RuUu",
"by ua2004",
"�� Dizell",
"by mdteam",
"by Iriy",
"�� ��������",
"�� Babun",
"by DEMIGOD",
"by iolegv-RuUu",
"�� orchidea777",
"�� iolegv RuUu",
"�� Smart's studios",
"by Allep",
"by iolegv (BigFANGroup)",
"�� Good-Cinema",
"by MD-TEAM BigFanGroup KVNforAll",
"�� zmei666",
"�� Menyk",
"by Alex Smit",
"by ANDROZZZ RuUu (BigFANGroup)",
"�� YoFilm",
"�� blackmambo",
"�� Lexor",
"�� iriston",
"�� HELLYWOOD",
"�� genafon",
"�� vladvld",
"by iolegv RuUu (BigFANGroup)",
"�� Yuraflash",
"�� Postal",
"�� BOGUSH",
"by MD-TEAM BigFANGroup KVNforAll",
"�� m4r5",
"by tak prosto",
"�� Kroha32",
"by ��������",
"�� ������",
"ot othfilm",
"(�����-������)",
"�� iolegv RuUu",
"�� Dwteam",
"by Jey-Shock",
"�� �����-������",
"�� ����� ����",
"-Kuraj-Bambey",
"by AleMak",
"by iolegv",
"�� 1001 Cinema",
"��  �����-������",
"(BigFANGroup)",
"by FS-Media",
"�� serj69",
"(�� maksnev82 )",
"�� 1001������",
"iolegv-RuUu",
"�� -=CARBON=-",
"�� ������� ��������",
"by El.dar",
"mi24",
"�� ESiR",
"() FB2",
"�� bigfree-torrents",
"[������� �����-������]",
"[������� ]",
"- fanatus",
"�� Kinozal",
"by LGFs",
"()",
"FB2",
"����� .TV",
"by DaNsteR",
"�� tonic-��������",
"(������ ���)",
"- 1001cinema",
"[tfile.ru]"
);

$one_t = array_unique($one_t);


$name = str_ireplace($one_t, "", $name);



$one_tt=array("Calitate","Video","Sunet","Fiier","Despre emisiune","Traducere","Durata");
$one_td=array("��������","�����","����","����","� ��������","�������","������������");
$name = str_ireplace($one_tt, $one_td, $name);





$name_orig = trim(htmlspecialchars_uni(preg_replace("/\[([0-9_-]+(.*?))\]/is", "(\\1)", $name)));

$name = trim(htmlspecialchars_uni(preg_replace("/\[([0-9_-]+(.*?))\]/is", "(\\1)", $name)));
$name_clear = trim(htmlspecialchars_uni(preg_replace("/\[((\s|.)+?)\]/", "", $name)));


if (stristr($name_clear,'[') && !stristr($name_clear,']'))
$name_clear = $name_clear."]";

echo "����: ".$name_orig;
echo "<br>";
echo "�����: ".($name_orig==$name_clear ? $name_clear:"<b>$name_clear</b>");
echo "<br>";

if ($name_orig<>$name_clear && strlen($name_clear) >= 12)
$torrecomment = sqlesc(date("Y-m-d") . " - ���������� �������� ($name_orig).\n");
else
$torrecomment = sqlesc(date("Y-m-d") . " - ���������� �������� �������.\n");



//if ($name_orig<>$name_clear)
sql_query("UPDATE torrents SET name=".sqlesc($name_clear).", torrent_com = CONCAT_WS('',$torrecomment, torrent_com) WHERE id=".sqlesc($res["id"])) or sqlerr(__FILE__, __LINE__);
}



	global $CURUSER, $ss_uri, $tracker_lang, $queries, $tstart, $query_stat, $querytime;
	
	
		foreach ($query_stat as $key => $value) {
			print("<div>[<b>".($key+1)."</b>] => <b>
			".($value["seconds"] <= 0.0009 ? "<font color=\"#0468f1\" title=\"������������ ������. ����� ���������� ��������.\">".$value["seconds"]."</font>":"
		    ".($value["seconds"] >= 0.01 ? "<font color=\"red\" title=\"������������� �������������� ������. ����� ���������� ��������� �����.\">".$value["seconds"]."</font>" : "<font color=\"blue\" title=\"������ �� ��������� � �����������. ����� ���������� ����������.\">".$value["seconds"]."</font>" )."
		    ")."
		  </b> [".$value["query"]."]</div>\n");
		}

die;
?>