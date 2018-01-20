<?php
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

global $tracker_lang;	


$blocktitle = $tracker_lang['main_menu'];

//$res = sql_query("SELECT COUNT(*) AS num_off FROM off_reqs WHERE perform='no'") or sqlerr(__FILE__,__LINE__); 
//$arr = mysql_fetch_array($res);


$bsql=new MySQLCache("SELECT COUNT(*) AS num_off FROM off_reqs WHERE perform='no'", 24*7200, "block_menu_off.txt"); // 24 часа
$arr=$bsql->fetch_assoc();


$num_off = number_format($arr["num_off"]);

//."<a class=\"menu\" href=\"browseday.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'browseday.php' ? "<b>".$tracker_lang['browseday']."</b>":"".$tracker_lang['browseday']."")."</a>"


$content = "
<a class=\"menu\" href=\"index.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'index.php' ? "<b>".$tracker_lang['homepage']."</b>":"".$tracker_lang['homepage']."")."</a>"

."<a class=\"menu\" href=\"browse.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'browse.php' ? "<b>".$tracker_lang['browse']."</b>":"".$tracker_lang['browse']."")."</a>"

."<a class=\"menu\" ".(empty($num_off)? "title=\"Запросы и предложения\"":"title=\"Есть новые невыполненные запросы\"")." href=\"detailsoff.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'detailsoff.php' ? "<b>Запросы | Предлож</b>":"Запросы")." ".(empty($num_off)? "":"(<b>".$num_off."</b>)")."</a>"
         //  ."<a class=\"menu\" href=\"viewoffers.php\">&nbsp;".$tracker_lang['offers']."</a>"
        //   ."<a class=\"menu\" href=\"forums.php\">&nbsp;".$tracker_lang['forum']."</a>"
           
           
           ."<a class=\"menu\" href=\"rules.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'rules.php' ? "<b>".$tracker_lang['rules']."</b>":"".$tracker_lang['rules']."")."</a>"
           
          // ."<a class=\"menu\" href=\"rules.php\">&nbsp;".$tracker_lang['rules']."</a>"
         
		 ."<a class=\"menu\" href=\"faq.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'faq.php' ? "<b>".$tracker_lang['faq']."</b>":"".$tracker_lang['faq']."")."</a>"
       //    ."<a class=\"menu\" href=\"faq.php\">&nbsp;".$tracker_lang['faq']."</a>"
       	 ."<a class=\"menu\" href=\"tags.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'tags.php' ? "<b>".$tracker_lang['tags']."</b>":"".$tracker_lang['tags']."")."</a>"
         //  ."<a class=\"menu\" href=\"tags.php\">&nbsp;".$tracker_lang['tags']."</a>"
         ."<a class=\"menu\" href=\"topten.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'topten.php' ? "<b>".$tracker_lang['topten']."</b>":"".$tracker_lang['topten']."")."</a>"
         
            ."<a class=\"menu\" href=\"newsarchive.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'newsarchive.php' ? "<b>Архив новостей</b>":"Архив новостей")."</a>"
                        
		 //  ."<a class=\"menu\" href=\"topten.php\">&nbsp;".$tracker_lang['topten']."</a>"
          ."<a class=\"menu\" href=\"formats.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'formats.php' ? "<b>".$tracker_lang['formats']."</b>":"".$tracker_lang['formats']."")."</a>"
		  
		 ."<a class=\"menu\" href=\"groups.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'groups.php' ? "<b>Группы</b>":"Группы")."</a>" 
		     ."<a class=\"menu\" href=\"comments_last.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'comments_last.php' ? "<b>Комментарии</b>":"Комментарии")."</a>" 
		  ;
		 
		   //."<a class=\"menu\" href=\"formats.php\">&nbsp;".$tracker_lang['formats']."</a>";

if (get_user_class() >= UC_MODERATOR)
 $content.= "<a class=\"menu\" href=\"log.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'log.php' ? "<b>".$tracker_lang['log']."</b>":"".$tracker_lang['log']."")."</a>";


 $content.= "<a class=\"menu\" href=\"support.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'support.php' ? "<b>Служба техподдержки</b>":"Служба техподдержки")."</a>";


 $content.= "<a class=\"menu\" href=\"useragreement.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'useragreement.php' ? "<b>О движке</b>":"О движке")."</a>";


?>