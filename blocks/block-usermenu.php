<?php

if (!defined('BLOCK_FILE')) {
header("Location: ../index.php");
exit;
}

global $CURUSER,  $tracker_lang;


///       <a class=\"menu\" href=\"log.php\">&nbsp;".(basename($_SERVER['SCRIPT_FILENAME']) == 'log.php' ? "<b>".$tracker_lang['log']."</b>":"".$tracker_lang['log']."")."</a>

$content = "
<a class=\"menu\" href=\"my.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'my.php' ? "<b>".$tracker_lang['my']."</b>":"".$tracker_lang['my']."")."</a>"


           ."<a class=\"menu\" href=\"userdetails.php?id=".$CURUSER["id"]."\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'userdetails.php' ? "<b>".$tracker_lang['profile']."</b>":"".$tracker_lang['profile']."")."</a>"
         
	."<a class=\"menu\" href=\"bookmarks.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'bookmarks.php' ? "<b>".$tracker_lang['bookmarks']."</b>":"".$tracker_lang['bookmarks']."")."</a>"
		//   ."<a class=\"menu\" href=\"bookmarks.php\">".$tracker_lang['bookmarks']."</a>"
    ."<a class=\"menu\" href=\"checkcomm.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'checkcomm.php' ? "<b>Лист подписки</b>":"Лист подписки")."</a>"
    
    	."<a class=\"menu\" href=\"mematch.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'mematch.php' ? "<b>Похожести</b>":"Похожести")."</a>"
    
           //."<a class=\"menu\" href=\"checkcomm.php\">Лист подписки</a>"
         ."<a class=\"menu\" href=\"mybonus.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'mybonus.php' ? "<b>".$tracker_lang['my_bonus']."</b>":$tracker_lang['my_bonus'])."</a>"
          // ."<a class=\"menu\" href=\"mybonus.php\">".$tracker_lang['my_bonus']."</a>"
         ."<a class=\"menu\" href=\"mysimpaty.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'mysimpaty.php' ? "<b>Мои респекты</b>":"Мои респекты")."</a>" 
		//   ."<a class=\"menu\" href=\"mysimpaty.php\">Мои респекты</a>"
              ."<a class=\"menu\" href=\"invite.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'invite.php' ? "<b>".$tracker_lang['invite']."</b>":"".$tracker_lang['invite']."")."</a>" 
         //  ."<a class=\"menu\" href=\"invite.php\">".$tracker_lang['invite']."</a>"
               ."<a class=\"menu\" href=\"users.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'users.php' ? "<b>".$tracker_lang['users']."</b>":"".$tracker_lang['users']."")."</a>" 
		 //  ."<a class=\"menu\" href=\"users.php\">".$tracker_lang['users']."</a>"
               ."<a class=\"menu\" href=\"smilies.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'smilies.php' ? "<b>".$tracker_lang['smilies']."</b>":"".$tracker_lang['smilies']."")."</a>" 
		  // ."<a class=\"menu\" href=\"smilies.php\">".$tracker_lang['smilies']."</a>"
		  
		        ."<a class=\"menu\" href=\"tfiles.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'tfiles.php' ? "<b>Обменник</b>":"Обменник")."</a>" 
		        
		  
                ."<a class=\"menu\" href=\"sendbonus.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'sendbonus.php' ? "<b>".$tracker_lang['send_bonus']."</b>":"".$tracker_lang['send_bonus']."")."</a>" 
	//	   ."<a class=\"menu\" href=\"sendbonus.php\">".$tracker_lang['send_bonus']."</a>"
                 ."<a class=\"menu\" href=\"friends.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'friends.php' ? "<b>".$tracker_lang['personal_lists']."</b>":"".$tracker_lang['personal_lists']."")."</a>"  
        //   ."<a class=\"menu\" href=\"friends.php\">".$tracker_lang['personal_lists']."</a>"
                 ."<a class=\"menu\" href=\"subnet.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'subnet.php' ? "<b>".$tracker_lang['neighbours']."</b>":"".$tracker_lang['neighbours']."")."</a>" 
		 //  ."<a class=\"menu\" href=\"subnet.php\">".$tracker_lang['neighbours']."</a>"
                 ."<a class=\"menu\" href=\"mytorrents.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'mytorrents.php' ? "<b>".$tracker_lang['my_torrents']."</b>":"".$tracker_lang['my_torrents']."")."</a>" 
		 //  ."<a class=\"menu\" href=\"mytorrents.php\">".$tracker_lang['my_torrents']."</a>"
          
                  ."<a class=\"menu\" href=\"getrss.php\">".(basename($_SERVER['SCRIPT_FILENAME']) == 'getrss.php' ? "<b>Новости RSS</b>":"Новости RSS")."</a>" 
                  
                          
                     
		 //   ."<a class=\"menu\" href=\"getrss.php\">Лента новостей rss</a>"
           
          // ."<a class=\"menu\" href=\"logout.php\">".$tracker_lang['logout']."!</a>"
		  ;

$blocktitle = "<center>".$tracker_lang['user_menu']."</center>";





?>