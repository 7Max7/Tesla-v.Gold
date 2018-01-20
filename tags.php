<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

function insert_tag($name, $description, $syntax, $example, $remarks)
{
	$result = format_comment($example);
	print("<p align=center><b>$name</b></p>\n");
	print("<table class=main width=100% border=1 cellspacing=0 cellpadding=5>\n");
	print("<tr valign=top><td class=a width=25%>��������:</td><td class=a >$description\n");
	print("<tr valign=top><td>���������:</td><td><tt>$syntax</tt>\n");
	print("<tr valign=top><td >������:</td><td><tt>$example</tt>\n");
	print("<tr valign=top><td>���������:</td><td>$result\n");
	if ($remarks != "")
		print("<tr><td class=a>����������:</td><td class=a>$remarks\n");
	print("</table>\n");
}

stdhead("����");

begin_frame("��� ���� �����");
$test = $_POST["test"];
?>
<p><center><b><?=$SITENAME?></b> ������������ ������� ���������� <b>BB �����</b> ������� �� ������ ������������ ��� ��������� ����� ������ � ������.</center></p>
<?
if (get_user_class() >= UC_POWER_USER){
	?>
<center><form method=post action=?>
<textarea name=test cols=60 rows=3><? print($test ? htmlspecialchars($test) : "")?></textarea><br>
<input type=submit value="��������� ���� ���!" style='height: 23px; margin-left: 5px'>
</form></center>
<?}?>
<?

if ((get_user_class() >= UC_POWER_USER) && ($test != ""))
  print("<p><hr>" . format_comment($test) . "<hr></p>\n");

if (get_user_class() <= UC_MODERATOR and $test != ""){

$user = $CURUSER["username"];
$user_ip1=getenv("REMOTE_ADDR"); 
$user_data="$user (".$user_ip1.")"; 



$sf =ROOT_PATH."cache/tags.txt"; 
$fpsf=fopen($sf,"a+"); 
$date = date("d.m.y"); 
$time= date("H:i:s"); 
//fputs($fpsf,"-------����� �������� $date: $time-----\n\n"); 
fputs($fpsf,"$date:$time $user_data:\n ".$test."\n---------------\n"); 

fclose($fpsf);




}


insert_tag(
	"������",
	"������ ����� ������.",
	"[b]�����[/b]",
	"[b]���� ����� ������.[/b]",
	""
);

insert_tag(
	"������",
	"������ ����� ���������.",
	"[i]�����[/i]",
	"[i]���� ����� ���������.[/i]",
	""
);

insert_tag(
	"�������������",
	"������ ����� ������������.",
	"[u]�����[/u]",
	"[u]���� ����� ������������.[/u]",
	""
);

insert_tag(
	"������������",
	"������ ����� �����������.",
	"[s]�����[/s]",
	"[s]���� ����� �����������.[/s]",
	""
);

insert_tag(
	"���� (� 1)",
	"������ ���� ������.",
	"[color=<i>Color</i>]<i>�����</i>[/color]",
	"[color=red]���� ����� �������.[/color]",
	"����� ������ ���� �� ���� �����, � ������� blue red green pink black white"
);

insert_tag(
	"���� (� 2)",
	"������ ���� ������.",
	"[color=#<i>RGB</i>]<i>�����</i>[/color]",
	"[color=#ff0000]���� ����� �������.[/color]",
	"RGB ���� ������ ���� 6-�� ������� ������� ����������������� ������� ���������."
);

insert_tag(
	"������",
	"��������� ������ ������.",
	"[size=<i>n</i>]<i>�����</i>[/size]",
	"[size=18]��� 18 ������.[/size]",
	"����� ������ ���� � ��������� �� 1 (���������) �� 100 (�������). ������ �� ��������� - 8."
);

insert_tag(
	"�����",
	"��������� ����� ��� ������.",
	"[font=<i>Font</i>]<i>�����</i>[/font]",
	"[font=Impact]��������[/font]",
	""
);

insert_tag(
	"������ (� 1)",
	"������� ������.",
	"[url]<i>������</i>[/url]",
	"[url]http://www.example.com/[/url]",
	""
);

insert_tag(
	"������ (� 2)",
	"������� ������ � ���������.",
	"[url=<i>URL</i>]<i>����� �����������</i>[/url]",
	"[url=http://www.example.com/]������[/url]",
	""
);

insert_tag(
	"�������� (� 1)",
	"������� ��������.",
	"[img]<i>������]</i>[/img]",
	"[img]http://www.rambler.ru/i/logos/friends.gif[/img]",
	"������ ������ ������������� �� .gif, .jpg ��� .png </b>"
);

insert_tag(
	"�������� (� ������� �� ����)",
	"������� ��������.",
	"[url=<i>������</i>][img]<i>������ �� ����</i>[/img][/url]",
	"[url=http://www.rambler.ru/][img]http://www.rambler.ru/i/logos/friends.gif[/img][/url]",
	"������ ������ ������������� �� .gif, .jpg ��� .png </b>"
);



insert_tag(
	"������� (������� �����)",
	"������� ���������.",
	"[spoiler]<i>��� �����</i>[/spoiler]",
	"[spoiler=��������]������ ��� �����, � ���� ���?![/spoiler]",
	"�� 5 ��������� ������ ������������, ��� ��������, ��� �� ��������. </b>"
);

insert_tag(
	"������ ������ ��������",
	"������� Light ���������.",
	"[light=�����]��� �����[/light]",
	"[light=�����]��� �����[/light]",
	"�������: light ������ �� �������� bb ���� ������ ������ ������ (� ������������ ����������)</b>"
);



insert_tag(
	"������ (� 1)",
	"������� ������.",
	"[quote]<i>������������ �����</i>[/quote]",
	"[quote]���� ������ ����� ������ ����.[/quote]",
	""
);

insert_tag(
	"������ (� 2)",
	"������� ������.",
	"[quote=<i>Author</i>]<i>������������ �����</i>[/quote]",
	"[quote=7Max7]���� ������ ����� ������ ����.[/quote]",
	""
);

insert_tag(
	"������",
	"������� ������.",
	"[li]<i>�����</i>",
	"[li] ����������� 1\n[li] ����������� 2",
	""
);

insert_tag(
	"������",
	"������� �������������� �����.",
	"[hr]",
	"[hr] ����� [hr]",
	""
);


insert_tag(
	"�� ���� (me)",
	"������� �� ����.",
	"[me]<i>�����</i>",
	"[me] ���� �� ���� (me)",
	""
);


insert_tag(
	"����� �������",
	"�����",
	"[pi]��� � ���� �����",
	"[pi]��� � ���� �����",
	"������� ��� BR (���������� ������� �� ������ �������)"
);



insert_tag(
	"������ bb ��� (�������������������)",
	"�����",
	"[bb]��� � ������ <b>bb ���</b>[/bb]",
	"[bb]��� � ������ [b]bb ���[/b][/bb]",
	"������� ��� BR (���������� ������� �� ������ �������)"
);

insert_tag(
	"� ������",
	"����� ��� �������� � ������.",
	"[center]<i>�����</i>[/center]",
	"[center] ����������� [/center]",
	""
);

insert_tag(
	"�����",
	"����� ��� �������� �����.",
	"[left]<i>�����</i>[/left]",
	"[left] ����� �����[/left]",
	""
);

insert_tag(
	"������",
	"����� ��� �������� �����.",
	"[right]<i>�����</i>[/right]",
	"[right] ������ ����� [/right]",
	""
);

insert_tag(
	"������������ �����",
	"�����",
	"[pre]��� ����� �� ������� ����������, ���������� (enter) �� ������ ������, ��������� � ���� ��������.
[/pre]",
	"[pre]��� ����� �� ������� ����������, ���������� (enter) 
	 �� ������ ������,    ��������� � ���� ��������
	 ;-)
[/pre]",
	"����� ��� ��������� �����,  ����� ��� � word'e."
);

insert_tag(
	"��������� ����������",
	"�����",
	"[highlight]��� �����[/highlight]",
	"[highlight]��� �����[/highlight]",
	"��� ������ ��������� ����������� �����"
);

insert_tag(
	"����� ��������� ������",
	"�����",
	"[mcom=#FFD42A:#002AFF]��� ��� ������� <b>����</b> �� ����� ������� <b>����</b>[/mcom]",
	"[mcom=#FFD42A:#002AFF]��� ��� ������� ����� �� ����� ������� ����[/mcom]",
	"����� ����� ��� ������� ����"
);

insert_tag(
	"������� ������ ��� ��������� �����",
	"�����",
	"[hideback]������� ������ ��� ��������� �����[/hideback]",
	"[hideback]������� ������ ��� ��������� �����[/hideback]",
	"�������� ����� ��� ��������� ����� ����������"
);

insert_tag(
	"����� ������ ������",
	"����� ��� �������� ",
	"[legend]��������� ������ �����[/legend]",
	"[legend]��������� ������ �����[/legend]",
	"��������� ����� ������ ������."
);

insert_tag(
	"����� ������ ������ � �������",
	"����� ��� �������� ",
	"[legend=������ ����]��������� ������ ����� � �������[/legend]",
	"[legend=������ ����]��������� ������ ����� � �������[/legend]",
	"��������� ����� ������ ������."
);

insert_tag(
	"�������� ������",
	"����� ��� �������� ",
	"[marquee]�������� ������[/marquee]",
	"[marquee]�������� ������[/marquee]",
	"��� ����� ������"
);


/*
insert_tag(
	"�����",
	"������������� ����� ������.",
	"[audio]������ �� �����[/audio]",
	"[audio]http://allsiemens.com/mp3/files/1.mp3[/audio]",
	"����������� ������ ������ ������������� �� .mp3"
);
*/

insert_tag(
	"���� ��������",
	"������������� ���� ������.",
	"[flash]������ �� �����[/flash]",
	"[flash]http://www.flashpark.ru/files/cupgame.swf[/flash]",
	"����������� ������ ������ ������������� �� .swf �������� ��������� �������, � ������ �������� ����: [flash=300:50]��� ������[/flash]"
);

insert_tag(
	"����� � youtube",
	"������������� ������ � ����� youtube.",
	"[video=������ �� ���� youtube]",
	"[video=http://www.youtube.com/watch?v=4HmeA_vHjzY]",
	"����������� � ������ ������ ���� watch?v= � id �����"
);





end_frame();

stdfoot();
?>