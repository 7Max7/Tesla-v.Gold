<?

/**  ��������, ����� ���������� ������ ������ ������ �������� � ���������� ���������� (������ gold �� 2010 ���)!!!!
* ��� ������ ������ ������� Tesla - ������� �����. ���� ����� ������ ����� ���������� �� ������ demo.muz-tracker.net
* ���� ������� ������������ ������, �� �������� ���������� ��������� �� ����� ������, ����� ���������� ������ �� �������������.
* ���� ������� �������� � ���������� ����� ����, �������� ��������������� ��������: bit-tracker.info, bit-torrent.kiev.ua.
* ���� ������� ������������� ��������� 7Max7 �� ��� ������, ������ ���� ������� �� R190680055855 ��� Z187703352284
* ���� ������� �������� Gold ������ �� Platinum - �������� ��������� ����� 70$ (� ������������ ����������� ������������)
* 
* ������ ����� ������ ����� � ����� demo muz-tracker net � tesla-tracker.net! ))
* ������� �� �������� � ������ Tesla.
**/


if(!defined('IN_TRACKER'))
  die('Hacking attempt!');


//bot_newuser($CURUSER["username"]); 
    function bot_newuser($username)
    {
                            //��������� ����� ������ ������ ��������� � ���� ��� �� ��������������
        $botid = 92;             //�� ������������ ���� �������� �� �������
     //   $botclass = 2;            //����� ������ ������������ ����
        //$botname = "ViKa";    //��� ������ ����
        
        

$number = 21; // �����
$first = 1; // �� ��������� ������ �������� - 1
$random = mt_rand($first, $number);


if ($random==1)(
$text="�� �� ��� �� ���� �� [b]".$username."[/b]"
);
if ($random==2)(
$text="�, ����� �����, ����� �������� � ������������ [b]".$username."[/b] ���� �������, �� ���� ��� ������."
);
if ($random==3)(
$text="������� ��������, ����� ������������ �� [b]".$username."[/b]."
);
if ($random==4)(
$text="����� � ������� ���� �������, �� ����� [b]".$username."[/b]. �������� ������."
);
if ($random==5)(
$text="� ����� �������, ��� [b]".$username."[/b] ���� ������������, �� � ����, �� � ��� ��� ��� ������������!"
);
if ($random==6)(
$text="�� - ��������, ��������� ���� �� ������ ��� ������ - [b]".$username."[/b] ��� ����� ��������!"
);
if ($random==7)(
$text="������� �� ������, � ������ ���� ������ a.k.a. [b]".$username."[/b]."
);
if ($random==8)(
$text="� �� ����� ����� ����� �������� [b]".$username."[/b] ;-)"
);
if ($random==9)(
$text="������ ������, � �� �������� �� ���������� ��� [b]".$username."[/b] :-P"
);
if ($random==10)(
$text="���������� �������� [b]".$username."[/b], �� ����� ��?!"
);
if ($random==11)(
$text="�� ������� � ����, �� ������ ����, ������ � �� [b]".$username."[/b] ����..."
);
if ($random==12)(
$text="������, ��� ���� � ���� �������������, � ��� ������ ���, �� ���� � ��� �����, ������ ��� ����� ��� ��� [b]".$username."[/b]."
);
if ($random==13)(
$text="��� ������, ��� � ��������??????? -> [b]".$username."[/b]."
);
if ($random==14)(
$text="��������� ���� [b]".$username."[/b] - ��� ����� localhost."
);
if ($random==15)(
$text="�������� ��������������� ����� ��������� ����� �����, �� ����� ������������� ������, � � ��� ����� ������������ [b]".$username."[/b] :D"
);
if ($random==16)(
$text="� � ViKa, � ��������� � ���� �����, ������ [b]".$username."[/b] ;-)"
);
if ($random==17)(
$text="����� ������� ������� ����, � � ������: [b]".$username."[/b] ���� ����."
);
if ($random==18)(
$text="���� ��������� �������� ��� �����������, �� �������� �, �� ��� �� ��� ������ - [b]".$username."[/b]!"
);

if ($random==19)(
$text="����� ������������ [b]".$username."[/b] ������ ������<------:-D"
);


if ($random==20)(
$text="�����������!! � � ��� ������� [b]".$username."[/b], �� ��������� ������� :P"
);
if ($random==21)(
$text="�� ������ �� ������, ���������� ���� [b]".$username."[/b] �������� ���������."
);
    	sql_query("INSERT INTO shoutbox (id, userid, date, text) VALUES ('id='," .  sqlesc($botid) . ", ".time().", " . sqlesc(format_comment($text)) . ")") or sqlerr(__FILE__, __LINE__); 
    };



    function bot_newrelease($id,$name,$releasername,$class)
    {
    	global $DEFAULTBASEURL;

        $botid = 92;             //�� ������������ ���� �������� �� �������
       // $botclass = 2;            //����� ������ ������������ ����
       // $botname = "ViKa";    //��� ������ ����
        
        $text = "����� ������� [url=$DEFAULTBASEURL/details.php?id=".$id."]".$name."[/url] ��� ����� ������������� [b][color=#".get_user_rgbcolor($class, $name)."]".$releasername."[/color][/b]";  
        
		if (!empty($name))
		sql_query("INSERT INTO shoutbox (id, userid, date, text) VALUES ('id='," .  sqlesc($botid) . ", ".time().", " . sqlesc(format_comment($text)) . ")") or sqlerr(__FILE__, __LINE__); 
		
		   }; 
		   
		   
		   /*
		   
		    function bot_birthday($id,$username,$presentclass,$subject)
    {
    	global $DEFAULTBASEURL;
    
                            //��������� ����� ������ ������ ��������� � ���� ��� �� ��������������
        $botid = 92;             //�� ������������ ���� �������� �� �������
        $botclass = 2;            //����� ������ ������������ ����
        $botname = "ViKa";    //��� ������ ����
        
        $text = "$subject ��� ����� ������������� [b][color=#".get_user_rgbcolor($presentclass, $username)."]".$releasername."[/color][/b]";  
        
      	sql_query("INSERT INTO shoutbox (id, userid, date, text) VALUES ('id='," .  sqlesc($botid) . ", ".time().", " . sqlesc($text) . ")") or sqlerr(__FILE__, __LINE__); 
	
		   }; 

		   
		   */
		   
		   


?>