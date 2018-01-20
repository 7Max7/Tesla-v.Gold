<?php

/**
 * @author 7Max7
 * @copyright 2010
 */




$file = "0nline.php";

$getfile = file_get_contents($file);


//echo $getfile;


preg_match_all("/function ([^(]+)*\(\s*(.*)\s*\)\s*\/*\/*(.*?)/i", $getfile, $db);



$getfile = preg_replace("/function ([^(]+)*\(\s*(.*)\s*\)\s*\/*\/*(.*?)/i", "eval(gzuncompress(gzinflate(base64_decode(((base64_decode('".base64_encode(((base64_encode(gzdeflate(gzcompress(preg_quote('\\2')))))))."')))))))", $getfile);



//$getfile = preg_replace("/function ([^(]+)*\(\s*(.*)\s*\)\s*\/*\/*(.*?)/i", "eval(gzuncompress(gzinflate(base64_decode(((base64_decode('".base64_encode(((base64_encode(gzdeflate(gzcompress(preg_quote('\\2')))))))."')))))))", $getfile);




print_r($db);



//file_put_contents("0nline2.php",$getfile);




/*

/////////////////////////// первый проход кодирования ///////////////////////////
$input=gzcompress($input);
$input=base64_encode(gzdeflate($input));
$input=base64_encode(gzdeflate(gzcompress($input)));

/// если хотим один проход
$s="eval(gzuncompress(gzinflate(base64_decode(gzuncompress(gzinflate(base64_decode('".$input."')))))))";
/////////////////////////// первый проход кодирования ///////////////////////////


*/











?>