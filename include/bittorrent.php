<?

/**  Внимание, самая популярная сборка движка теперь доступна в бесплатном скачивании (версия gold за 2010 год)!!!!
* Это старая версия проекта Tesla - торрент сайта. Демо новой версии можно посмотреть по ссылке demo.muz-tracker.net
* Если желаете использовать движок, то оставьте пожалуйста копирайта на своих местах, иначе стабильной работы не гарантируется.
* Если желаете поискать и установить новые моды, советуем воспользоваться форумами: bit-tracker.info, bit-torrent.kiev.ua.
* Если желаете поблагодарить владельца 7Max7 за эту сборку, киньте пару монеток на R190680055855 или Z187703352284
* Если желаете обновить Gold версию до Platinum - придется заплатить около 70$ (с последующими бесплатными обновлениями)
* 
* Данная копия движка взята с сайта demo muz-tracker net! ))
* Спасибо за внимание к движку Tesla.
**/


// DEFINE IMPORTANT CONSTANTS
define('IN_TRACKER', true);
define('ROOT_PATH', str_replace("include","",dirname(__FILE__)));

// SET PHP ENVIRONMENT
@error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
//@ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_DEPRECATED);
@ini_set('error_reporting', E_ALL & ~E_NOTICE);///  & ~E_NOTICE
@ini_set('display_errors', 'Off');

///@ini_set('allow_url_fopen', 'Off'); /// при мультитрекере нельзя отключать

//@ini_set('zlib.output_compression_level', '9');
//@ini_set('zlib.output_compression', 'On');

///@ini_set('max_execution_time', '1'); ///в htaccess -> php_flag max_execution_time 30 

@ini_set('display_startup_errors', '0');
@ini_set('ignore_repeated_errors', '1');
@ignore_user_abort(1);
@set_time_limit(0);
@set_magic_quotes_runtime(0);
@session_start();
@date_default_timezone_set("Europe/Kiev");

// Variables for Start Time
function timer() {
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}
$tstart = timer(); // Start time


// INCLUDE/REQUIRE BACK-END
require_once(ROOT_PATH.'include/init.php');
require_once(ROOT_PATH.'include/config.php');
require_once(ROOT_PATH.'include/functions.php');
require_once(ROOT_PATH.'include/functions_cache.php');
require_once(ROOT_PATH.'include/blocks.php');
require_once(ROOT_PATH.'include/passwords.php');

// Подключение защиты от xss атак
if ($ctracker == "1")
require_once(ROOT_PATH.'include/ctracker.php');

// LOAD GZIP/OUTPUT BUFFERING
if ($use_gzip == "yes")
gzip();


if (ini_get('register_globals') == '1' || strtolower(ini_get('register_globals')) == 'on')
echo "Отключите regiser_globals в php.ini/.htaccess<br>";

if (ini_get('short_open_tag')=='no')
echo "Включите short_open_tag в php.ini/.htaccess (техническое требование)<br>";

if (!function_exists('imagecreatetruecolor'))
echo "Подключите расширение php с названием gd.so<br>";


define("SHOW", "<script language=\"javascript\" type=\"text/javascript\" src=\"js/snow.js\"></script>");

if (!isset($HTTP_POST_VARS) && isset($_POST)) {
$HTTP_POST_VARS = $_POST; 
$HTTP_GET_VARS = $_GET; 
$HTTP_SERVER_VARS = $_SERVER; 
$HTTP_COOKIE_VARS = $_COOKIE; 
// $HTTP_ENV_VARS = $_ENV; 
$HTTP_POST_FILES = $_FILES; 
}

/*
if (get_magic_quotes_gpc()) {
if (!empty($_GET))
$_GET = strip_magic_quotes($_GET);
if (!empty($_POST))
$_POST = strip_magic_quotes($_POST);
if (!empty($_COOKIE))
$_COOKIE = strip_magic_quotes($_COOKIE);
} 


if (!get_magic_quotes_gpc()) {
    if (is_array($_GET)) {
        while (list($k, $v) = each($_GET)) {
            if (is_array($_GET[$k])) {
                while (list($k2, $v2) = each($_GET[$k])) {
                    $_GET[$k][$k2] = addslashes($v2);
                }
                @reset($_GET[$k]);
            } else {
                $_GET[$k] = addslashes($v);
            }
        }
        @reset($_GET);
    }

    if (is_array($_POST)) {
        while (list($k, $v) = each($_POST)) {
            if (is_array($_POST[$k])) {
                while (list($k2, $v2) = each($_POST[$k])) {
                    $_POST[$k][$k2] = addslashes($v2);
                }
                @reset($_POST[$k]);
            } else {
                $_POST[$k] = addslashes($v);
            } 
        }
        @reset($_POST);
    }

    if (is_array($_COOKIE)) {
        while (list($k, $v) = each($_COOKIE)) {
            if (is_array($_COOKIE[$k])) {
                while (list($k2, $v2) = each($_COOKIE[$k])) {
                    $_COOKIE[$k][$k2] = addslashes($v2);
                }
                @reset($_COOKIE[$k]);
            } else {
                $_COOKIE[$k] = addslashes($v);
            }
        }
        @reset($_COOKIE);
    }
}
*/
//////////// создаем массив зодиака ///////////
$zodiac[] = array("Козерог", "capricorn.gif", "22-12");
$zodiac[] = array("Стрелец", "sagittarius.gif", "23-11");
$zodiac[] = array("Скорпион", "scorpio.gif", "24-10");
$zodiac[] = array("Весы", "libra.gif", "24-09");
$zodiac[] = array("Дева", "virgo.gif", "24-08");
$zodiac[] = array("Лев", "leo.gif", "23-07");
$zodiac[] = array("Рак", "cancer.gif", "22-06");
$zodiac[] = array("Близнецы", "gemini.gif", "22-05");
$zodiac[] = array("Телец", "taurus.gif", "21-04");
$zodiac[] = array("Овен", "aries.gif", "22-03");
$zodiac[] = array("Рыбы", "pisces.gif", "21-02");
$zodiac[] = array("Водолей", "aquarius.gif", "21-01");


////////////// создаем массив смайлов //////////////
$smilies = array(
";-)" => "wink.gif",
":-)" => "gib.gif",
":-(" => "ac.gif",
":smile:" => "gab.gif",
":-D" => "grin.gif",
":lol:" => "bj.gif",
"O_o" => "ai.gif",
":-p" => "ae.gif", 
"8-)" => "af.gif", 
":-:" => "aj.gif", 
":'(" => "ak.gif",
":-x" => "al.gif",
":evil:" => "evil.gif",
":no:" => "no.gif",
":?:" => "question.gif",
":!:" => "excl.gif",
":hmm:" => "hmm.gif",
":hmmm:" => "hmmm.gif",
":idea:" => "idea.gif",
":love:" => "love.gif",
":nuke:" => "nuke.gif",
":thumbsup:" => "thumbsup.gif",
":thumbsdown:" => "thumbsdown.gif",
":tease:" => "tease.gif",
":wall:" => "wall.gif",
":-|" => "an.gif",
":-/" => "ao.gif",
":jokingly:" => "ap.gif",
"]:->" => "aq.gif",
"[:-}" => "ar.gif",
":kissed:" => "as.gif",
":-!" => "at.gif",
":tired:" => "au.gif",
":evil_baby:" => "cv.gif",
":stop:" => "av.gif",
":kissing:" => "aw.gif",
"@}->--" => "ax.gif",
":thumbs_up" => "ay.gif",
":drink:" => "az.gif",
"@=" => "bb.gif",
":help:" => "bc.gif",
":m:" => "bd.gif",
"%)" => "be.gif",
":ok:" => "bf.gif",
":wassup:" => "bg.gif",
":sorry:" => "bh.gif",
":bravo:" => "bi.gif",
":pardon:" => "bk.gif",
":no:" => "bl.gif",
":crazy:" => "bm.gif",
":dont_know:" => "bn.gif",
":dance:" => "bo.gif",
":yahoo:" => "bp.gif",
":blush:" => "ah.gif",
":new_pack:" => "bq.gif",
":tease:" => "br.gif",
":saliva:" => "bs.gif",
":wild:" => "bu.gif",
":training:" => "bv.gif",
":focus:" => "bw.gif",
":hang:" => "bx.gif",
":dance:" => "by.gif",
":dance2:" => "bz.gif",
":mega_shok:" => "ca.gif",
":to_pick_ones_nose:" => "cb.gif",
":yu:" => "cc.gif",
":hunter:" => "cd.gif",
":kuku:" => "ce.gif",
":fuck:" => "cf.gif",
":fan:" => "cg.gif",
":ass:" => "ch.gif",
":locomotive:" => "ci.gif",
":concussion:" => "ck.gif",
":pleasantry:" => "cl.gif",
":disappear:" => "cm.gif",
":suicide:" => "cn.gif",
":pilot:" => "co.gif",
":down:" => "cp.gif",
":energy:" => "cq.gif",
":stinker:" => "cr.gif",
":preved:" => "cs.gif",
":i-m_so_happy:" => "ct.gif", 
":prankster:" => "cu.gif", 
":boast:" => "cw.gif", 
":thank_you:" => "cx.gif", 
":lovers:" => "lovers.gif", 
":shout:" => "cy.gif", 
":victory:" => "cz.gif", 
":wink:" => "da.gif", 
":spiteful:" => "db.gif", 
":this:" => "dd.gif", 
":don-t_mention:" => "de.gif", 
":sarcastic_hand:" => "df.gif", 
":fie:" => "dg.gif", 
":swoon:" => "dh.gif", 
":scare:" => "di.gif", 
":anger:" => "dj.gif", 
":yess:" => "dk.gif", 
":vava:" => "dl.gif", 
":scratch_one-s_head:" => "dm.gif", 
":nono:" => "dn.gif",
":whistle:" => "do.gif", 
":umnik:" => "dp.gif", 
":zoom:" => "dq.gif", 
":heat:" => "dr.gif", 
":declare:" => "ds.gif", 
":idea:" => "dt.gif", 
":on_the_quiet:" => "du.gif", 
":give_heart:" => "dv.gif", 
":give_flowers:" => "dw.gif", 
":friends:" => "dx.gif", 
":punish:" => "dy.gif", 
":porka:" => "dz.gif", 
":party:" => "ea.gif", 
":girl_smile:" => "eb.gif", 
":tender:" => "ec.gif", 
":flirt:" => "ed.gif", 
":curtsey:" => "ee.gif", 
":gogot:" => "ef.gif", 
":girl_wink:" => "eg.gif", 
":girl_blum:" => "eh.gif", 
":girl_hide:" => "ei.gif", 
":girl_crazy:" => "ej.gif", 
":girl_wacko:" => "ek.gif", 
":girl_in_love:" => "el.gif", 
":girl_dance:" => "em.gif", 
":kiss2:" => "en.gif", 
":girl_pinkglassesf:" => "eo.gif", 
":girl_mad:" => "ep.gif", 
":histeric:" => "eq.gif", 
":girl_sigh:" => "er.gif", 
":girl_sad:" => "es.gif", 
":girl_cray:" => "et.gif", 
":girl_cray2:" => "eu.gif", 
":girl_impossible:" => "ev.gif", 
":girl_drink:" => "ew.gif", 
":girl_mirror:" => "ex.gif", 
":nails:" => "ey.gif", 
":girl_hospital:" => "ez.gif", 
":girl_kid:" => "fa.gif", 
":girl_hair_drier:" => "fb.gif", 
":girl_witch:" => "fc.gif", 
":first_movie:" => "fd.gif", 
":slap_in_the_face:" => "fe.gif", 
":friendship:" => "ff.gif", 
":girl_kisses:" => "fg.gif", 
":on_hands:" => "fh.gif", 
":it_is_love:" => "fi.gif", 
":supper_for_a_two:" => "fj.gif", 
":sex_behind:" => "fk.gif", 
":baby1:" => "fm.gif", 
":baby2:" => "fn.gif", 
":baby3:" => "fo.gif", 
":baby4:" => "fp.gif", 
":baby5:" => "fq.gif", 
":music_forge:" => "fr.gif", 
":music_saxophone:" => "fs.gif", 
":music_flute:" => "ft.gif", 
":music_violin:" => "fu.gif", 
":music_piano:" => "fv.gif", 
":music_drums:" => "fw.gif", 
":music_accordion:" => "fx.gif", 
":vinsent:" => "fy.gif", 
":frenk:" => "fx.gif", 
":tommy:" => "ga.gif", 
":big_boss:" => "gb.gif", 
":hi:" => "gc.gif", 
":buba:" => "gd.gif", 
":russian_ru:" => "ge.gif", 
":brunette:" => "gf.gif", 
":girl_devil:" => "gg.gif", 
":girl_werewolf:" => "gh.gif", 
":queen:" => "gi.gif", 
":king:" => "gj.gif",
":beach:" => "gk.gif",
":smoke:" => "gl.gif",
":scenic:" => "gm.gif",
":reader:" => "gn.gif",
":read:" => "go.gif",
":rtfm:" => "gp.gif",
":to_keep_order:" => "gq.gif",
":wizard:" => "gr.gif",
":lazy:" => "gs.gif",
":dental:" => "gt.gif",
":superstition:" => "gu.gif",
":crazy_pilot:" => "gv.gif",
":to_become_senile:" => "gw.gif",
":download:" => "gx.gif",
":telephone:" => "gy.gif",
":diver:" => "gz.gif",
":wake_up:" => "ha.gif", 
":ice_cream:" => "hb.gif",
":journalist:" => "hc.gif", 
":soap_bubbles:" => "hd.gif",
":body_builder:" => "he.gif",
":cup_of_coffee:" => "hf.gif", 
":soccer:" => "hg.gif", 
":swimmer:" => "hh.gif",
":pirate:" => "hi.gif", 
":clown:" => "hj.gif",
":jester:" => "hk.gif", 
":cannibal_drums:" => "hl.gif",
":pioneer:" => "hm.gif", 
":moil:" => "hn.gif",
":paint:" => "ho.gif",
":superman:" => "hp.gif",
":cold:" => "hq.gif",
":illness:" => "hr.gif",
":winner:" => "hs.gif",
":police:" => "ht.gif",
":toilet_plums:" => "hu.gif",
":death:" => "hv.gif",
":zombie:" => "hw.gif", 
":ufo:" => "hx.gif",
":sun:" => "hy.gif", 
":pumpkin_grief:" => "hz.gif",
":pumpkin_smile:" => "ia.gif",
":pooh_go:" => "ib.gif",
":cupidon:" => "cupidgirl.gif",
":oops:" => "eu.gif",
":usall:" => "ew.gif",
":too:" => "ex.gif",
);

// Set this to the line break character sequence of your system
$linebreak = "\r\n";


?>