<?

/**  Внимание, самая популярная сборка движка теперь доступна в бесплатном скачивании (версия gold за 2010 год)!!!!
* Это старая версия проекта Tesla - торрент сайта. Демо новой версии можно посмотреть по ссылке demo.muz-tracker.net
* Если желаете использовать движок, то оставьте пожалуйста копирайта на своих местах, иначе стабильной работы не гарантируется.
* Если желаете поискать и установить новые моды, советуем воспользоваться форумами: bit-tracker.info, bit-torrent.kiev.ua.
* Если желаете поблагодарить владельца 7Max7 за эту сборку, киньте пару монеток на R190680055855 или Z187703352284
* Если желаете обновить Gold версию до Platinum - придется заплатить около 70$ (с последующими бесплатными обновлениями)
* 
* Данная копия движка взята с сайта demo muz-tracker net и tesla-tracker.net! ))
* Спасибо за внимание к движку Tesla.
**/


if(!defined('IN_TRACKER'))  die('Hacking attempt blocks'); 

function render_blocks($side, $blockfile, $blocktitle, $content, $bid, $bposition, $md5) {
   // global $showbanners; 
   ///$side, $blockfile, $title, $content, $bid, $bposition, 
   // global $foot; 
   global $integrity;
   
        if (!empty($blockfile)) {
        	
            if (@file_exists(ROOT_PATH."blocks/".$blockfile)) {

              if ($integrity==1){
             	$filesize=filesize(ROOT_PATH."blocks/".$blockfile."");
            	$file_time=md5_file(ROOT_PATH."blocks/".$blockfile."");
                $file_md5=md5($filesize.$file_time);
               ///die( $file_md5);
            	if ($md5==$file_md5){
                define('BLOCK_FILE', 1); 
                require (ROOT_PATH."blocks/".$blockfile.""); 
                } else {
 			    $content="<center>Файл $blockfile не прошел проверку целостности!</center>"; 
				}}
				else
				{
				define('BLOCK_FILE', 1); 
                require (ROOT_PATH."blocks/".$blockfile); 
				}
				
              
            } else {
                 $content = "<center>Существует проблема с этим блоком!</center>"; 
            }
			}
        

        if (!((isset ($content) AND !empty ($content)))) { 
            $content = "<center>Нет файла или контент пустой</center>"; 
        }

      switch ($side) {
        case 'b': 
            $showbanners = $content; 
            return null; 

        case 'f': 
            $foot = $content; 
            return null; 

        case 'n': 
            echo $content; 
            return null; 

        case 'p': 
            return $content; 

        case 'o': 
            return $blocktitle." - ".$content; 
      }

//        BeginBlock($blocktitle, $bposition); 
        themesidebox($blocktitle, $content, $bposition); 
//        EndBlock($bposition); 
        return null; 
} 

function themesidebox($title, $content, $pos) {
    global $blockfile, $b_id, $ss_uri; 
    static $bl_mass; 
    //$content = str_replace("'", "'", $content); 
    $func = 'echo'; 
    $func2 = ''; 
    if ($pos == "s" || $pos == "o") {
        if (empty($blockfile)) {
            $bl_name = "fly-block-".$b_id; 
        } else {
            $bl_name = "fly-".str_replace(".php", "", $blockfile); 
        }
    } else {
        if (empty($blockfile)) {
            $bl_name = "block-".$b_id; 
        } else {
            $bl_name = str_replace(".php", "", $blockfile);
        }
    }
    if (!isset($bl_mass[$bl_name])) {
        if (file_exists(ROOT_PATH."themes/".$ss_uri."/html/".$bl_name.".html")) { 
            $bl_mass[$bl_name]['m'] = true;
        } else {
            $bl_mass[$bl_name]['m'] = false;
        }
    }
    if ($bl_mass[$bl_name]['m']) {
        $thefile = addslashes(@file_get_contents(ROOT_PATH."themes/".$ss_uri."/html/".$bl_name.".html")); 
        $thefile = "\$r_file=\"".$thefile."\";"; 
        eval($thefile);
        if ($pos == "o") {
            return $r_file;
        } else {
            echo $r_file;
        }
    } else {
        switch($pos) {
            case 'l': $bl_name ="block-left";
            break;
            case 'r': $bl_name ="block-right";
            break;
            case 'c': $bl_name ="block-center";
            break;
            case 'd': $bl_name ="block-down"; 
            break; 
            default: $bl_name ="block-all"; 
            break; 
        }
        if (!isset($bl_mass[$bl_name])) {
            if (file_exists(ROOT_PATH."themes/".$ss_uri."/html/".$bl_name.".html")) {
                $bl_mass[$bl_name]['m'] = true;
                $f_str = file_get_contents(ROOT_PATH."themes/".$ss_uri."/html/".$bl_name.".html");
                $f_str = 'global $ss_uri, $tracker_lang; '.$func.' "'.addslashes($f_str)." \"".$func2.";";
                $bl_mass[$bl_name]['f'] = create_function('$title, $content', $f_str);
            } else {
                $bl_mass[$bl_name]['m'] = false;
            }
        }
        if ($bl_mass[$bl_name]['m']) {
            if ($pos == "o") {
                return $bl_mass[$bl_name]['f']($title, $content);
            } else {
                $bl_mass[$bl_name]['f']($title, $content);
            }
        } else {
            $bl_name = 'block-all';
            if (!isset($bl_mass[$bl_name])) {
                if (ROOT_PATH.file_exists("themes/".$ss_uri."/html/".$bl_name.".html")) {
                    $bl_mass[$bl_name]['m'] = true;
                    $f_str = file_get_contents(ROOT_PATH."themes/".$ss_uri."/html/".$bl_name.".html");
                    $f_str = 'global $ss_uri, $tracker_lang; '.$func.' "'.addslashes($f_str)." \"".$func2.";";
                    $bl_mass[$bl_name]['f'] = create_function('$title, $content', $f_str);
                } else {
                    $bl_mass[$bl_name]['m'] = false;
                }
            }
            if ($bl_mass[$bl_name]['m']) {
                if ($pos == "o") {
                    return $bl_mass[$bl_name]['f']($title, $content);
                } else {
                    $bl_mass[$bl_name]['f']($title, $content);
                }
            } else {
                echo "<fieldset><legend>".$title."</legend>".$content."</fieldset>";
            }
        }
    }
}

$orbital_blocks = array();

function show_blocks($position) {
    global $CURUSER, $use_blocks, $already_used, $orbital_blocks;

    if ($use_blocks) {

        if (!$already_used) {
   // $blocks_res = mysql_query("SELECT * FROM orbital_blocks WHERE active = 1 ORDER BY weight ASC") or sqlerr(__FILE__,__LINE__); 
 // while ($blocks_row = mysql_fetch_array($blocks_res)) 

///////// cache
$cache2=new MySQLCache("SELECT * FROM orbital_blocks WHERE active = 1 ORDER BY weight ASC", 3*7200, "blocks_all.txt"); // 3 часа
while ($blocks_row=$cache2->fetch_assoc())
///////// cache

                $orbital_blocks[] = $blocks_row; 
            if (!$orbital_blocks) 
                $orbital_blocks = array(); 
            $already_used = true; 
        }

        //$blocks = sql_query("SELECT * FROM orbital_blocks WHERE bposition = ".sqlesc($position)." AND active = 1 ORDER BY weight ASC") or sqlerr(__FILE__,__LINE__); 
        foreach ($orbital_blocks as $block) {
            $bid = $block["bid"];
            $md5 = $block["md5"];

         //	echo $md5."s -";
         //   unset($md5);
   
            $content = $block["content"];
            $title = $block["title"];
            $blockfile = $block["blockfile"];
            $bposition = $block["bposition"];
            if ($position <> $bposition)
                continue;
            $view = $block["view"];
            $which = explode(",", $block["which"]); 
            $module_name = str_replace(".php", "", basename($_SERVER["PHP_SELF"])); 
            if (!(in_array($module_name, $which) || in_array("all", $which) || (in_array("ihome", $which) && $module_name == "index"))) {
                continue; 
            }
if ($view == 0) {
render_blocks($side, $blockfile, $title, $content, $bid, $bposition, $md5); 
}
elseif ($view == 1 && $CURUSER) {
render_blocks($side, $blockfile, $title, $content, $bid, $bposition, $md5); 
}
elseif ($view == 2 && (get_user_class() >= UC_MODERATOR)) {
render_blocks($side, $blockfile, $title, $content, $bid, $bposition, $md5); 
}
elseif ($view == 3 && (!$CURUSER )) {
render_blocks($side, $blockfile, $title, $content, $bid, $bposition, $md5); 
}
elseif ($view == 4 && $CURUSER  && (get_user_class() <> UC_VIP) && (get_user_class() <= UC_ADMINISTRATOR)){
render_blocks($side, $blockfile, $title, $content, $bid, $bposition, $md5); 
}
elseif ($view == 5 && $CURUSER  && (get_user_class() == UC_SYSOP)){
render_blocks($side, $blockfile, $title, $content, $bid, $bposition, $md5); 
}
//unset($md5);
}
}
}
?>