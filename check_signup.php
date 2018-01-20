<?
require_once("include/bittorrent.php");
dbconn();


function ajaxerr($text){

if (is_numeric($width));
$width=$width;

echo "<div id=\"ajaxerror\" style=\"width: 80%;\">".$text."</div>\n"; 
return; 
}

function ajaxsucc($text){
if (is_numeric($width));
$width=$width;

echo "<div id=\"ajaxsuccess\" style=\"width: 80%;\">".$text."</div>\n"; 
return; 
}


function decode_unicode_url($str) {
    $res = '';

    $i = 0;
    $max = strlen($str) - 6;
    while ($i <= $max) {
        $character = $str[$i];
        if ($character == '%' && $str[$i + 1] == 'u') {
        $value = hexdec(substr($str, $i + 2, 4));
        $i += 6;

        if ($value < 0x0080)
            $character = chr($value);
        else if ($value < 0x0800)
            $character =
                chr((($value & 0x07c0) >> 6) | 0xc0)
                . chr(($value & 0x3f) | 0x80);
        else
            $character =
                chr((($value & 0xf000) >> 12) | 0xe0)
                . chr((($value & 0x0fc0) >> 6) | 0x80)
                . chr(($value & 0x3f) | 0x80);
        } else
            $i++;

        $res .= $character;
    }

    return $res . substr($str, $i);
}

function convert_text($s)
{
 $out = "";

 for ($i=0; $i<strlen($s); $i++)
 {
  $c1 = substr ($s, $i, 1);
  $byte1 = ord ($c1);
  if ($byte1>>5 == 6){
  $i++;
  $c2 = substr ($s, $i, 1);
  $byte2 = ord ($c2);
  $byte1 &= 31;
  $byte2 &= 63;
  $byte2 |= (($byte1 & 3) << 6);
  $byte1 >>= 2;

  $word = ($byte1<<8) + $byte2;
  if ($word==1025) $out .= chr(168);
  elseif ($word==1105) $out .= chr(184);
  elseif ($word>=0x0410 && $word<=0x044F) $out .= chr($word-848);
  else
  {
    $a = dechex($byte1);
    $a = str_pad($a, 2, "0", STR_PAD_LEFT);
    $b = dechex($byte2);
    $b = str_pad($b, 2, "0", STR_PAD_LEFT);
    $out .= "&#x".$a.$b.";";
  }
  }
  else
  {
  $out .= $c1;
  }
 }

 return $out;
}

header ("Content-Type: text/html; charset=windows-1251");

if ($_POST["action"] == "username") {
    
    $wantname = htmlspecialchars($_POST["username"]);
    $wantusername = convert_text(urldecode(decode_unicode_url($wantname)));

        if (strlen($wantusername) > 12){
        ajaxerr("��� ������������ ������ ���� �� ����� 12 ��������");
          die;
          }
        elseif (!validusername($wantusername) && !empty($wantusername)){
          ajaxerr("�������� ��� ������������");
            die;
          }
         elseif(empty($wantusername)){
          ajaxerr("�� ������� ��� ������������");
          die;
          }
          else {
          
    $res = mysql_fetch_row(sql_query("SELECT COUNT(*) FROM users WHERE username = ".sqlesc($wantusername)))  or sqlerr(__FILE__, __LINE__);
	
	}
    if ($res[0] != 0)
        ajaxerr("������������ $wantusername ��� ���������������");
   elseif (empty($wantusername))
       ajaxerr("�� ������� ��� ������������");
    elseif (strlen($wantusername) > 12)
       ajaxerr("��� ������������ ������ ���� �� ����� 12 ��������");
   elseif (!validusername($wantusername))
       ajaxerr("�������� ��� ������������");
    else
        ajaxsucc("�� ������ ������������ ��� ���");
}

if ($_POST["action"] == "password"){
    $wantpass = htmlspecialchars($_POST["password"]);

    $wantpassword = convert_text(urldecode(decode_unicode_url($wantpass)));
    $pagain = htmlspecialchars($_POST["passagain"]);
    $passagain = convert_text(urldecode(decode_unicode_url($pagain)));

    if (empty($wantpassword))
        ajaxerr("������� ������");
    elseif (empty($passagain))
        ajaxerr("������������� ������");
    elseif ($wantpassword != $passagain)
        ajaxerr("������ �� ���������.");
    elseif (strlen($wantpassword) < 7)
        ajaxerr("����������� ����� ������ 7 ��������");
    elseif (strlen($wantpassword) > 40)
        ajaxerr("������������ ����� ������ 40 ��������");
    else
        ajaxsucc("�� ������ ������������ ���� ������");
}

if ($_POST["action"] == "email"){
    $email = htmlentities($_POST["email"]);
    
    
    if (empty($email)){
        ajaxerr("�� ������ e-mail �����");
        die;
		}
    elseif (!validemail($email)){
        ajaxerr("���� e-mail �� ����������� �������, ��������� ���������");
   die;
   } else{

    $res = mysql_fetch_row(sql_query("SELECT COUNT(*) FROM users WHERE email = ".sqlesc($email)))  or sqlerr(__FILE__, __LINE__);
    $res2 = mysql_fetch_row(sql_query("SELECT COUNT(*) FROM bannedemails WHERE email = ".sqlesc($email))) or sqlerr(__FILE__, __LINE__);

}

    if (empty($email))
        ajaxerr("�� ������ e-mail �����");
    elseif ($res2[0] != 0)
    ajaxerr("���� e-mail �������");
    elseif ($res[0] != 0)
        ajaxerr("���� e-mail ����� ��� ���������������");
    elseif (!validemail($email))
        ajaxerr("���� e-mail �� ����������� �������, ��������� ���������");
    else
        ajaxsucc("�� ������ ������������ ���� e-mail �����");
}

if ($_POST["action"] == "invite" && strlen($_POST["invite"]) == 32){

    $invite = htmlentities($_POST["invite"]);

    list($inviter) = mysql_fetch_row(sql_query("SELECT inviter FROM invites WHERE invite = ".sqlesc($invite)))// or sqlerr(__FILE__, __LINE__)
	;


	if (empty($invite))
    ajaxerr("�� ������� �����������");
    
    elseif (strlen($invite) <> 32)
	ajaxerr("�� ����� �� ���������� ��� �����������");
      
	elseif (!$inviter)
	ajaxerr("��� ����������� ��������� ���� ��������������");
  
    else
        ajaxsucc("�� ������ ������������ ��� �����������");
}

	if (!$_POST["action"] == "username" && !$_POST["action"] == "password" && !$_POST["action"] == "email" && !$_POST["action"] == "invite"){
	
	//header("Refresh: 0; url=403.php");
    die();
	}


?>