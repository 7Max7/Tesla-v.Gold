<?


require "include/bittorrent.php";



gzip();

dbconn();


stdhead("�������");

begin_main_frame();

?>

<? begin_frame("����� �������"); ?>
<ul>
<li>������ ������� <b>�� �������� ����������</b> � ����������� ��� ���������� ����� ��� ���������� �������������� ������� ������ �� <b>�������� ������������</b> �� <b> <font color="#<?=get_user_rgbcolor(UC_MODERATOR,"")?>">����������</font></b> (<b><font color="#<?=get_user_rgbcolor(UC_ADMINISTRATOR,"")?>">��������������</font></b> � <b> <font color="#<?=get_user_rgbcolor(UC_SYSOP,"")?>">�����</font></b> - ��� ����, ��� ������� ���������������,- ��������� �� ������ ����������). ���� ��� �� �������� ��� ������� � �� ������ ��� ���� ������ ������� - �� ������ ������ ������� ���� ����������� ���� � ������ ��� ���, ��� ��� ��������, ��� ������� �� ���.</li>
<li><b>����� ������������� - ����� ��� ������������� �������!</b> � ���� ������� ��� ���������� - <?=$SITENAME?> �������� ������� ���������� ��������, � ��� �������� ������������ ������������� ����������� ������� ������� �����.</li>

<li>���� ������������� ������ � ����� ������� ������������� � ����� �� �� ����������,�� ������ �������� � �������� �������� ��������� ����� ����)</li>

<li>�������� ���������� ������ ������� �������� �������������� (<img src="pic/warned.gif"> ). C�������� �������������� ������ ������� ����� �������� ����, �� ������ �� ����, ����� 5 �������������� ������� ������������ �����������. (���������� ����� ������������ �������������� �� ������������ ����������, �� �������, ���������� � ������ ������ ��� �� ������� �������������). � ���������� ������������ ����� ������ ������ � �������.</li>
<li>� ������ ����������� ��������� ������, ���������� ����� <b>������������</b>, ����� <b>��������</b>, ����� <b>�������</b> �� �����.</li>
<li>�� ������������� ��� ���� ��������� ��������� (���� ������� �������� Ip ������ �� �������) - �� ��������� ��������� ���� ���� ������ � ��� ����� ��������� �������� ������ ����. ������ ���������, ��� �� ������ ;)</li>
<li>������� �� ������ � �� <?=$SITENAME?> ������ �� ������������ <b>������� �����</b>. ������ ����� �� ������ ������������ ������ � ������ ������� �������������. � ������ ���������� ������� ��������� ���������, ����������� ����������� ���������, �������� �� �������� �� ������� ������ ������������ � ���������.
<li>�� ���� ����� ������������ � ����������, ������������ �� ��������� ������ ������ �������. �� ����� ������ ��� �������� ������ � ����� ������� � ����������� � ������ �����. ����� ������� ������ ������� �� ������ �������� � �������� ��������� �� <a href=forums.php>������</a>, ��� � <a href=support.php>������������</a>, ��� ������� �������� ������������� ������ � <b>������� ���������</b>, ��������� ������� ������ ��������� icq, skype, jabber � ���� ��������.
<li>����������� ��� ����������� ������ <b>�������</b> email �����. �� �� ����������� ��� ���������� �������� ������ <u>Mail.ru</u> ��� <u>hotmail.com</u> ��-�� ������������ �� ��� ����������� �������� ����-�����. ���� �� ����� �� ��������� ������ � �������������� � �����������/���������������, �������� <a href=./faq.php>����</a> ��� ����������� ������ �������� ������. � ���� ������� �� �������� ���, ��� ��� �������� �����, ����� ��� ����� ������ ���� ������������ ����������, �� ����� ������������ �� � ����� ������ ����� � ������� �� ����� �������� ������� �����. �� ����������� ��� ����� ������� �������������� ������ ��� ����� (����� <a href=http://www.icq.com>ICQ</a>,<a href=http://www.qip.ru>QIP</a>, ��� � <a href=http://www.skype.com>Skype</a>) � ���� � ��� ������, ���� ��������� ����� �������������. </li>

<? end_frame(); ?>
<? begin_frame("������� ������� - <font color=#004E98>������ �� ������!</font>"); ?>
<ul>
<li>������ � ������ ������ ������� ����� �� ��������� - ��� ������ �������� �� ��������� <i>antileech</i> (������� ��������, ���������� �������� � �������� ������������� �� �������) �������. �� ��� �� ������, ��� �� �� ����� �������� �� �����.</li>
<li>�� ���������, �� ������ ���� <b>����� ������������ �� 2-40 ��������� ��������</b>.
<li>������������� ����� ��������� � ��������� � ����� ������������� � �� �������� ������������ ����������� ������������ ������ "�� ���������". �� �������� ��� �� ��� �� ������ ��������� � ������ �������������, ��������� � � �������������.</li>
<li>����������� ����������� "������/�����" ����� 1, �� ���� ������� ������ - ������� � �����. ���������� ������������ ���, ���� ������ ������ � ����������� ����� �� ����� �������, ��� ������, � �� ����� ������: �� ���������� ��� ���������� ������ ��� ����� ������! � ������, ���� �� �������� ������������ ����� (���������� ������ �����) �� �������, ���������� ����������� �� �������, ���� ���� ��� ������� ���� 1 - ������� �� ������� ����-��, ������ �� ������� ���.</li>
<li>���� � ��� ��������� ����� �������� �� ����������� (������ ����������� ���������� ���� ����������, �� �� ������ ����������� � �������, �������� ����� ������� ����� ������), �������� ���� <a href=./faq.php>����</a> - �� ������ � ��� ��� ����������� ��� ����������. � ������ ������������� �����-���� �������� �� ���������� �������, ������� ���������� �� � �������������, � � ����������, ��������� ������� ������ ��������� ��� ������������ � �������.</li>
<? end_frame(); ?>
<? begin_frame("������� ��������������� ���������"); ?>
<? begin_frame(); ?>
<center><b>�����������, ���������� ���������� �������� �/��� ����������������, ���������� � ������� - ������ �� ��������, � �� �� ������ ����!</b></center>
<? end_frame(); ?>
<ul>
<br>
<i>����������� ����������� �� ��������� ��������, ����� ������ ����� ����. ��-������, ���� � ��� �������� �� ��, ��� ����. ������� � ���, ��� ���� � ��� ������� �� ������������, � ������� � �.�. ���� �� ������ �������� ����������, ��� ����� ���� � ���-�� ��������� PR-���. � ������� ���� � ������, ������� ������������ ������������� � �������� ������� ������. �� � ������ �������� � ����, ������. ������ ���� � ������� �������� ����� ������������� � � ����� ����������� ����.</i>

<li>������� ������������ ���������, �������� � ������� ������� ��� ���� �����: (1) ��������� ���� <i>��������</i> � <i>�������������</i> ����������, (2) ������ ������������ ��� <i>���������� ����������� ������</i> ������������ ������� ��� ������,(3) �������� <i>���������� ����������</i>, ����������� � �������.
<li>���� �� �� �������� ������������ � �������, �� ������������� ��.
<li>�������� ������� ����� ����� ������������� �������� � ������������. (��������� �� �����)
<li>��������� ����� � ���� -(��� � ����������� �����������, ��������� - ������� ���� ��������!)
<li>��������� ������ �� �����-�������. (����� ������ ���������� ��� ��� ���� ����, ��� ��������������)
<li>������� �������� �������, ������ � ������ <b>���������</b> �� ����������� ���������������� ��.
<li>�������, ������� �� ����� ������� �� � ���� ���� �� ������������� ��� �������������� ����� ������ �������� (�����������): ���� ��� �� �������� �������, ���������� �������� ���-���� ���������� ��� � ������ �����.</li>
<? end_frame(); ?>
<? begin_frame("������������ � �������� - <font color=#004E98>������������ ������� ��������� �������������� ��������</font>"); ?>
<ul>
<li>��������� ������� .gif, .jpg � .png.</li>
<li>������������� ���������: <b><?=$avatar_max_width;?> * <?=$avatar_max_height;?> ��������</b> � ������ � �� �������� ����� 50 K�.</li>
<li>�� ����������� �������������� ��������� (� ������: ����������� � ������������ ���������, ���������, ������������ ����������, ������� � �����������). ������������? �������� <a href=staff.php>�������������</a>.</li>
<li>�� ����������� ��������� ����������� ���� ������ �����������, ��� ����������� �����.</li>
<? end_frame(); ?>

<? if (get_user_class() >= UC_UPLOADER) { ?>

<? begin_frame("������� ��������"); ?>

<ul> 
<? begin_frame(); ?>
<center><b><font color=red>�������� �� ���������� � �������� ����� ���� ������� ��� ������</font></b></center>
<? end_frame(); ?>


<li><font color="#CC3300">������ ��� �������� �������, �������������� ������� �� �������. �������� ��������� �������� ����������� �������� ������,
����������� ��� ������������, ����� ���������.</font></li>
<li>����������� ������� ������ �� ������ ������. �������, ���������� ��������� ������� ����� ���������.
� �������� ���������� ����������� ������� � �������� ����������� ������� � ��������������� ������������ � ����������� �������
(�������� � �.�.).</li>
<li>C��������� ������� <font color=red><b>�� ������</b></font> ��������� <u>���������������� ��� ���������� ����� ������ ����������.</u>
<b>����� ������� ��������� ��� ��������������!</b></li>
<li>��� ��������� ������� (������������� ���� ��� �������������� ����������) ������� �������������� ���������� ���� �������
�� �������� (� �������, ���������� ����� � �������, �������� ������ � ����������, ������������������� ��������� � �.�.).
<b>����� ������ ������: ����� ��������� �������� �� ����� � �������, ������ � �.�. - ���������� ������� ���� ����� �� ����������� �
������������ ���������. ��������� ����� ����� ����������� ���������� � ���������� �������.</b></li>
<li><b>� �������� ������� ����������� ������� ���������� (��� ����� ������� - �� ������ ���� 4; ��� ����� � ��� - �� ����� 2-� ).</b><br>
- ��������� � ������� � ������� ����� ������ ��������������� ����������� ������� �����.<br>
<b>- ������ ������� �� ����� ��������, ��� ������� ����������� ����� ������ �������� <font color="#0000FF">500px</font>, � ��� ������� ����������� ������������ ������ �������.</b><br>
- ������� ������ ��������� �������� ���� �� ������.</li>
<li>�������� ������� ����� ������ ���� �������� ������ ��������� � �� ���������:<br>
 - ���� ���������� �� � ���������� ���������;<br>
 - �������� � ��������;<br>
 - ������ �� ��������� �������, �� ����������� ������ �� ������������ ������� ������;</li>
<li>���� ������� �� ����� ����������� � ������� 14 ����, ���� ���, ��� ���������� �������� ��� �������.</li>
<li>��� ����� ������ ���� � ������������ �������. (iso �����, avi �����).</li>
<li>���-������ ������ ���������� ������ (� �������� �����)*�����* ��� *����*.</li>
<li>������������ �������, ��� ��������� � �����, ���������� ������ � ����� � ������. �� �������������, ��� ����� ��� �����.</li>
<li>������� ���� ������� ������ ���������� �� ������� ��� <b>� ������� 24 �����</b> (������ ������� �����)</li>
<li>�� ����� ������� ���� ������ � ������� ����, ���� ����� �������� readme ��� � �������� �������-�����.</li>
<li>����� ����������, ��� �������� ��� ������ ��������.</li><br>
<b><font color=red>�����������:</font></b>
<li> ��������� � ��������� ��� ����������   �������, ����������� �  ������ (RAR, ZIP, ...),
� ������ (ISO, MDF, NRG, ...) � ������ ������������ ������ ���   ���������������� ������������ �  ����������� �������. 
���������� - ������� ������� (ISO, MDF, NRG, ...) ��������� ������ �   �������� ���� � ����.</li>
<li>������� ����� � <b>youtube</b>, <b>vkontakte</b> � �.�. ������ ����� �� ������ ���� ���� 2 ��.(���������� ������������ �����)</li>
<li>C������ ���� ������ � ������� ����, ���� ����� �������� readme ��� � �������� �������-�����.</li>
<li>��������� ��� ���������� �������, �� ������� �������������� ��������� ��������, �� ����������� ��������� ��������:<br>
- ���������� ������ � ��������<br>
- �������������� � ������� ������, ��� �������� �������� ����� �������� ����������������<br>
- ������, ���� ���� ������� ��������, � �������� �����, �, � �������� ���������� (� ���������� �����������), � ���������� ��������.
������������� ������� � ������� ��� �������� ��������� ��������� �������.</li>
<li>��������� ����� �� ������.<br>
��� ��, ���� �� �� ������ ��������� ���� �������� �������������, ����������� ���������� ����� ������� �� ���.</li>

<br>
<hr>
<center>���� � ��� ���� ���������� ������� ������ <a href=staff.php>����</a>, �������. ������ ������ ������ ��� �������� <span title="�������������" style="color: green;"><b>Solyris</b></span></center>


<? end_frame(); ?>

<? }  { ?>

<? begin_frame("������ �� $SITENAME"); ?>
<br />
<table border=0 cellspacing=3 cellpadding=0>
<tr>
	<td class=embedded  valign=top>&nbsp;<b><font color="#<?=get_user_rgbcolor(UC_USER,"")?>">������������</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>�������, ���������� <font color="#<?=get_user_rgbcolor(UC_USER,"")?>">������������</font> ������� (<b>�������� �� �����!</b>)</td></tr>
<tr>
	<td class=embedded  valign=top>&nbsp; <b><font color="#<?=get_user_rgbcolor(UC_POWER_USER,"")?>">�������&nbsp;������������</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>������ ������������� ����������� (� ��������) ��� ������ � <font color="#<?=get_user_rgbcolor(UC_USER,"")?>">�������������</font>, ��� ������� ������� �� ����� 4 ������, ��� ����� ����� 25 GB � ����� ������� 1.05. <font color="#<?=get_user_rgbcolor(UC_MODERATOR,"")?>">���������</font> ����� ������� ��������� ���� ������ �� ���������� ��������������� ���������� �������.(<b>�������� �� �����!</b>)</td>
</tr>

<tr>
	<td class=embedded  valign=top>&nbsp; <b><font color="#<?=get_user_rgbcolor(UC_VIP,"")?>">VIP</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>�������, ����������� <u>����������</u> ��� ������ <u>������</u> �����, � �� ������ ���, ��� �����.</td>
</tr>

<tr>
	<td class=embedded  valign=top>&nbsp; <b><font color="#<?=get_user_rgbcolor(UC_UPLOADER,"")?>">�������</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>������������ � ������ ��������� �� <?=$SITENAME?>. ������������� <font color="#<?=get_user_rgbcolor(UC_ADMINISTRATOR,"")?>">����������������</font> � <font color="#<?=get_user_rgbcolor(UC_MODERATOR,"")?>">������������</font>. ���� ���������� ���������? ������ � ������, �� �����������, ����� �� ��������.</td>
</tr>
<tr>
	<td class=embedded  valign=top>&nbsp; <b><font color="#<?=get_user_rgbcolor(UC_MODERATOR,"")?>">���������</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>����������� �������������� � ����� ������� �����������.</td>
</tr>
<tr>
	<td class=embedded  valign=top>&nbsp; <b><font color="#<?=get_user_rgbcolor(UC_ADMINISTRATOR,"")?>">�������������</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>���� ���������������� ����������.</td>
	</tr>
<tr>
	
	<td class=embedded  valign=top>&nbsp; <b><font color="#<?=get_user_rgbcolor(UC_SYSOP,"")?>">����</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>� ���� ����� �� �������, <font color="#<?=get_user_rgbcolor(UC_ADMINISTRATOR,"")?>">�������������</font> ��� �� �������.</td>
	
	
</tr>
</table>
<br />
<?
	end_frame();
	begin_frame("������� ������������� - <font color=#004E98>��������� �� �����!</font>");
?>
<ul>
<li>�� ������� ������� <b>���</b>!
<li>������ ������������! ����� ������������(��) ���� �� ������������ (��� ��� ���� �� �����).</li>
<li>���������� <u>����������</u> ����� "��� �����" ������ ���������� ����.</li>
<li>����������� ���� ������ ���� ���-�� ���������.</li>
<li>���� �� ������� ����, �������� �������� ���������� ������ �� �� �������.</li>
<li>������ ��� ��������� �������, �������� ���/�� �� � ���� ��� �������, ���������� �� ������������� ���� �� 1-2 ������.</li>
<li>�� ���������� ������� ������������ ���� �� ��� ��� �� ���� ������ ����-�� 4 ������.</li>
<li><b>������</b> ���������� ������� (� ���� �����������) ������ �� �������� / ������������ ������������.</li>


<li>��������� <?=$SITENAME?> ������ ���������� �� ����� �� ���� ��� <b>1 ��� � 3 ���</b>. ���� �� �� ����� ���� ������� �� ������ �������� ���� ���������� �����,��������� ��������������.</li>
<li>��������� � ���������� ����� ������������ ������������� �� ������� ���������.���� �� �� ������ ��������� �������,��� ������ ����� ���������.</li>
<li>��������� ������ ����� ������������ ���� (<b>BB ����</b>).</li>
<li>���� ����� ����������� ���������� ���� ������������������ �� ����� �� ����� 10 ������ � ����� ��� ������� <b>5 ��������������� ������</b>.
<li>��������� �� ����� ���� ������ <b>16 ���</b>.</li>
<li>���� �� �������� ���� ��� ��������� "������" ������ � ��� ���� �� ��������������� (�� ��������� ��), �� ������ ��������. ������ 3-5 ������. ������ ���� ����� ���������, �� �����.</li>
<li>����� ���������. ���� �������� ������� ������� �������,������ �� �������� �������� ������������ �� ��������.</li>
<li>��������� �� ������ ������� �������������. ���� ����� ������, ������ ����.</li>
<li>���� ������������ ����� ���������� ���������� ������ �������������, ��� ������������� �������</li> ��������������. ��� ��������� ��������� ����������.</li>


<br/>

<?
	end_frame();
	
	begin_frame("����������� ����������� - <font color=#004E98>����� ��� ���������� ��� ����������?</font>");
?>
<ul>
<li>�� ������ ������� � ������������� ����� � ������.</li>
<li>�� ������ ������� � ������������� ��������, �������, �����������, ������.</li>
<li>�� ������ ������� � ������������� ������� �������������.</li>
<li>�� ������ ���������, ������������� �������������.</li>
<li>�� ������ ������������� ������ VIP'�� � ������� ����, ��� ���� ������ �������.</li>
<li>�� ������ ������ ������ ���������� � �������������.</li>
<li>�� ������ ��������� ���������� � ������������� (��� ������ ����������� � ���������������).</li>
<li>�� ������ ��������� ������ ������-��� �� ��� ������ ��� ��� �����������. ;)</li>







<? end_frame(); ?>

<?
	end_frame();
	

		begin_frame("������� �����������������");
?>
<ul>
<li>"�������" �� ���������� ����������� � �������������.</li>

<li>���������� �� <u>�������</u> ������������� � ������ ���������� ���������, ����� ���������. ������ ���������� ������� ��� �����.</li>

<li>������ ��� ��������� ��� ������� �������, �������� ���/�� �� � ���� ��� �������, ���������� �� ������������� ���� �� ���� ������.</li>


<li>������������� <?=$SITENAME?> ������ ���������� �� ����� �� ���� ��� <b>1 ��� � 2-5 ���</b>.</li>
<li>��������� � �������������� ����� ������������ ������������� �� ������� ���������. ���� �� �� ������ ������ � ������ ��� � �����, �� ��� ������ ����� ���������.</li>


<br/>
<? end_frame(); ?>

<?

	

		begin_frame("����������� �� ������");
?>
 <ul>
<li> ���������� ���������� ������ � ����� ����� (��������� ���������� ����� ��������, ������ ����������� � ����������� ������������, ������, ��� � ��������� �����������, ��� � ���������� ���������� ���). ��������� ������ ��������� ������������ ����� �������.</li>
<li>������������ � ������� �� ������ ����������� ������ (��������� ��� ���� ��������� �����).</li>
<li>���������� �������������, ������������ ���-�������, ����������.</li>
<li>��������� ���� � ���������, �� ���������� ������� � ����: "��������", "��������" � �� ��������.
<li>��������� ������� � ���� ������ ��������� � �� (������ ���������).</li>
<li>��������� �������, ������������ � ����������� ���������; ���������������� ���������, ����������, ������, ��������� � ������ ����, ������������� � ������������� ��������, ������� ������ � ��������.</li>
<li>��������� �� ������ ������� ����, ��������, ���� ���������� ������� �� ������ ������ (������������ ����� �������� ������ �� �������������� ����������, �������� �������� ������ ��� �������� ���������/������, ������).</li>
<li>������������ � ������� �� ������ ����, �������� �� ������������� ��������, � �����, �������� �� �������������� (��������); ��������� ������������ ��������� �������� ����� � �������������� �������.
����������: ��� ���������� ������� ��������� ���������� ����� ��������������� ����������� ����������� http://www.translit.ru</li>
<li>�������������� �������������� [�����] ������ � ���������� (� ��� ����� ���������� �������������� � ���������� �������� ����� - ��� ���������������� ����� ����������� � ���������������. ������������ ����� �������� ������� ������ ���� ����� ������, ��������� ������� ������, �� ������� ���� ������������� �������� �������� ������ �������������).</li>
<li>�������������� �������������� �������� ��������� ���� (�������������� Caps Lock).</li>
<li>�������� ����������� ��������� � ��������� �������� ��� ����������� ���������� ��� ��������������. �������� ������, �� ��������� � ��������� ����������, ����� ��������� ��� ���������� ���������� �� ����� ��� � ������ ���������. ���� �� ���������� ��� ������, ��� �����, �� ������ ���������, �� ������������, ��������� ������ ��������� �������������� ��������� � �����������. �������� ������� ��������� �������������. ��� ������� �������� ������������� � �������������.</li>
<li>������������� ��� ������� � ����� ���������� ��������� � �������, ����������� ������������ � ����������������.</li>




<br/>

<?
	end_frame();
	begin_frame("����� ��������� ������� (�����)");
?>
<ul>
<li>�������������� ������� ��������� �� ��������� <b>175</b> ���� (�������), ��� ������� ������������� � ������� �������������.
<li>����������� ������ ��������� �� ��������� <b>2� ������� (���������� �� 93 ����)</b>, ��� ������� ������������� � ������� �������������.</li>
<li>������� ������������ �� ������� ����������� <b>���� ���</b> � �� ������ ���� �������� ���� ��������.</li>
<li>����������� �������� ���������� ��������������, ������� ��� �������, ������� �������� ������� � ������ ��� ���, � ���������� ��� �� � ����� ������������� ���.</li>
<li>����� ������� ������ ����� �������� ���������� ������ �� ip � �� ����� ������������.</li>
<li>�� �� ����� - ����� �����, - ������� ��� �����, ������� � ��� (�������������)</li>
<li>����������� ��������, ��� ��������� ���� �����. � ��������� ������������� <?=$SITENAME?> </li>

<br>

<p align=right><font size=1 color=#004E98><b>������� ��������������� 4.01.2009 (00:55 GMT+2)</b></font></p>

<? }
end_main_frame();


stdfoot(); ?>