<?



require_once("include/bittorrent.php");

dbconn();

loggedinorreturn();

stdhead("�������");

if ($_GET["form"]=='mov')
{
?>


<table width=100% cellspacing=0 cellpadding=2><tr>
<td class=colhead>
<font color="#000000">�������� ��������� ����� ��������</font>
</td></tr>
<td align="left">
<ul>
<br />
<b>CamRip</b> (<b>CAM</b>): ����� ������ ��������. ����� ���������� ������� � ������ ����������. �������� ������ ���������� �� ��������. � ��������� ������� ����� ������ ������ ������������ �.�.�. �������� ����� ������ ������, �������� ������ ���� ����� �������.
<br /><br />
<b>Telesync</b> (<b>TS</b>): ������������ ������c��������� (��������) ������� ������������� �� ������ � ������ ���������� � ������. �������� ����� ������� �����, ��� � ������� ������ (Cam). ���� ������������ �� ������ � ��������� ��� � ������� ���������� ������, �������� ������ ��� ��������� � ������ (��� � �������). ���� ����� ������� ���������� ����� ������� � ��� �����. ��� ������� ���� � ������ ������.


<br /><br />

<b>Screener</b> (<b>SCR</b>): ������ ����� �� ��������. ��� ����� ������������ ������c��������� ������������ ��� ������. �������� ����������� �������� � ����� ������� VHS. ���� ���� ��������, ������ ������ ��� Dolby Surround.
<br /><br />

<b>DVDScreener</b> (<b>SCR</b>): ��� �� �������, ��� � � ������ <b>Screener</b>, �� �� ���-��������. �������� - ��� DVDRip, �� �������� ������ "���������" �������� ������� � �����-������ ��������� ("����������� ���������").
<br /><br />

<b>Workprint</b> (<b>WP</b>):  ������ �������� ��� ��������� �������. ��� ��� ���������� "����-������" ������. ������ ������� � ������� VCD � ������� ������ �� ������ ������ � ����������� ����. ��� ��������������� ������ ������. ��-�� ����� ����� ������� ��. �� ����� ��������, �� ������� ������. ����� ����������� ��������� �����. ������ ����� ���� � �����, ��� ���� ��� �����, � ����� �� �������... ������ ����� ������ ����� �� ������� � ����� ��� � ���� ������ - �� ����� ��� ������������ �������.
<br /><br />

<b>Telecine</b> (<b>TC</b>):  ����� ���������� � ������ ������ �������, ������� ����� ������ � DVD-������. �������� - ����������, �� ����� �������� � ��������������� ������ ("��������" ��������). ���������� �������� �������� � �������� ��� ����� � �����. ����� ���������� ����� � ���������.
<br /><br />

<b>VHSrip</b> - �������� ��������� ������� ������� VHS, ������ �������� �������� ��������.


<br /><br />
<b>TVrip</b> - �������� ������� � �������������� �������, ������ ���������� (�� ���������� � � ������� �������). ����� ��� ����������� �������� ��������� ������ � ���� ��� SATrip �������.
<br /><br />
<b>SATrip</b> - �������� ������� �������� �� ��������, ������ ������� �������� ��� TVrip.
<br /><br />
<b>DVDRip � LDRip</b>: ��� ������ �������� �� DVD ��� Laserdisc. �������� - ����� ������, ���� � ������� �� ���������� ��������� ("�������")
<br /><br />

<u>�������������� ������������</u>
<br /><br />
<b>PS</b>:  Pan and Scan: ������ ��������� ��� �������� ������� ��������. ���������� �� �� ���������� ������. ���� ����� ����� ������������ ��� ��������� ��, �� ����� ������������ ������. ����������� ������� � ���, ����� 1955 ���� ������, ���� �������� � ������� 1,85:1 ( ����������� ������������ � 1,66:1 ). ����������� �������� Cinemascope-������ (2,35:1) ��� �������������� ����. ������� ��������� ����� ������ 1,33:1. ���� ���������� �� �����, �� ����� ��������� ��������. ��� ������� ���: ����� ��������� � ������. ���� �� ������ DVD � ��� ��� ���������� � "�������� ����������", �� �� ������ �������� �� ����, ��� ����� ������� ������� Pan and Scan. ���� �� ������ ���� �����, �� ��������� DVD � �������� "Widescreen".


<br /><br />
<b>STV</b>: Straight To Video - ����� ����� ����� �� DVD/������� ����� ����������. �������� - �������������� DVDrip ��� VHSrip.
<br /><br />
<b>Dubbed</b>: ������������ ���� ������ (�������� ����� ������� �� �������� ���������� � �������� �� ������������ �����)
<br /><br />
<b>Line.Dubbed</b>: ���� ����� ��� � Dubbed, ������ � ���� ������ ���� ��� ���� �� "������" ��� "���������" (Line).
<br /><br />
<b>Mic.Dubbed</b>: ���� ����� ��� � Dubbed, ������ ���� ��� ������� ���������� � ����������.
<br /><br /><br />


<u>������ ����������:</u><br />

<br />
<b>TS</b> = <b>Telesync</b> (������� ����)<br />
<b>TC</b> =<b> Telecine </b>(������� ����)<br />
<b>SCR</b> = <b>Screener</b> (������� ����)   <br />

<b>WS</b> = <b>Widescreen</b>                     <br />
<b>LETTERBOX</b> = ������ ������ ��� <b>Widescreen</b><br />
<b>LIMITED</b> = ����� ��������� � �����, ��� 500 �����������<br />
<b>DC</b> = "Director's Cut"<br />

<b>SE</b> = "Special Edition"   <br />
<b>FS</b> = ����� � Fullscreen, �.�. ������ <br />
<b>PROPER</b> = ���������� ����� ����� ������ ��� ��������� �� ��������� � ����<br />
<b>RECODE</b> = ����� ������������ � ������ ������ ��� ������ ������������ <br />

<b>DUPE</b> = ������ ����� ���� �� ������ ������ �������� ������� (������ ��������� � ������)<br />

<b>RERIP</b> = ����� ��� ������<br />
<b>Subbed</b> = ����� � ������� <br />
<b>WATERMARKED</b> = ��������� �������� ��-������ ��� ��������.<br /><br />

<br />
</ul>
</td>
</table>
<br />


<?
}
if ($_GET["form"]=='all')
{
?>


<table width=100% cellspacing=0 cellpadding=2><tr>
<td class=colhead>
<font color="#000000"><center>�������� ��������� �������� ������</center></font>
</td></tr>
<td align="left">

����� ������������ �������� ��������� �������� ������, ������� �� ������ ������� � ���������, �� ���������� � ���������, �������� �� ����� �������. ���� �� �� ������ ��� ������� ��������� ����, �� ���������� ��� ������, �������� �� ������ ����� �����. ���� �� �� ������ ����� �� ���� ������, �� ������� ��� �� ������.
<br />

<br />

<tr><td class=colhead>
<font color="#000000"><center>������</center></font>
</td></tr>
<td align="left">

<b>.rar .zip .ace .r01 .001</b><br />
<br />
��� ����� ��������������� ���������� �������.<br />
����� ������������� � ������ ��� ���������� ������ � ����� �� ���� ������� ���������.<br />
<br />

����� ������� ��� ������ �� ������ ������������ <a href="http://www.rarsoft.com/download.htm">WinRAR</a> ��� <a href="http://www.powerarchiver.com/download/">PowerArchiver</a>.<br />
<br />
���� ��� ��������� �� ������� ��� ������� .zip ����, ���������� 
<a href="http://www.winzip.com/download.htm">WinZip</a> (���� ������).<br />
<br />
���� ���������� ��������� �� ������� ��� ������� .ace ��� .001 ����, ���������� <a href="http://www.winace.com/">Winace</a> (���� ������).<br />

<br />
<br /> 
<b>.cbr .cbz</b><br />
<br />
������ ��� ���������������� �������. ����� � ����������� .cbr ���������� ������ � ����������� .rar, � ����� � ����������� .cbz - ������ � ����������� .zip . �� ������ �� ��� WinRAR ��� WinZip ����� �� ��������� ������� ��� �����. ���� ����� ���������, ���������� ��������� <a href="http://www.geocities.com/davidayton/CDisplay">
CDisplay</a>.<br />
<br />
<br />
<tr><td class=colhead>
<font color="#000000"><center>�������������� �����</center></font>
</td></tr>
<td align="left">

<b>.avi .mpg. .mpeg .divx .xvid .wmv</b><br />
<br />
��� ������ ����� �����. �� ����� ������� ����� ����� �������, �� �� ����������� ������������ ��������� ���������:
<a href="http://www.inmatrix.com/files/zoomplayer_download.shtml">Zoomplayer</a>,
<a href="http://www.bsplayer.org/">BSPlayer</a>, <a href="http://www.videolan.org/vlc/">VLC media player</a>, <a href="http://softella.com/la/index.ru.htm">Light Alloy</a>
 ��� <a href="http://www.microsoft.com/windows/windowsmedia/default.aspx">Windows Media Player</a>. ����� ��� ����������� ������, ��� ������� ��������������� ������. ����� ����� ������, ��� ����� �� �����������, ��-�� ���������� ������� ������. ��� ����������� ������������ ������ ����������� ��������� <a href="http://www.headbands.com/
gspot/download.html">GSpot</a>. ���� ����������� ����� ��������������� ������:<br />

<br />
� <a href="http://sourceforge.net/project/showfiles.php?group_id=53761&release_id=95213">ffdshow</a> (�������������! (��������� ������ �������: XviD, DivX, 3ivX, mpeg-4))<br />
� <a href="http://nic.dnsalias.com/xvid.html">XviD codec</a><br />
� <a href="http://www.divx.com/divx/">DivX codec</a><br />
� <a href="http://sourceforge.net/project/showfiles.php?group_id=66022&release_id=178906">ac3filter</a> (��� �����)<br />

� <a href="http://tobias.everwicked.com/oggds.htm">Ogg media codec</a> (��� .OGM ������ � ��� �����)<br />
<br />
<br />
<b>.mov</b><br />
<br />
��� ����� ����� �� <a href="http://www.apple.com/quicktime/">QuickTime</a>. ������������ ��������� ��� �� �������� ����� ������� � ����� <a href="http://www.apple.com/quicktime/download/">QuickTime</a>.
���� ����� �������������� ��������, ������� ����� 
 <a href="http://download2.times.lv/master/files/0/Multimedia/Video/quicktimealt140.exe">������</a>.<br />

<br />
<br />
<b>.ra .rm .ram</b><br />
<br />
��� ����� ����� �� <a href="http://www.real.com">Real.com</a>. ��� �� �������� ������������� ������������ �������������� ��������� - <a href=" http://download2.times.lv/master/files/0/Multimedia/Video/realalt130[www.free-codecs.com].exe">Real Alternative</a>.<br />
<br />
<br />
<b>.mp3 .mp2</b><br />
<br />

����������� �����. ����������� � ������� ��������� <a href="http://www.winamp.com/">WinAmp</a>.<br />
<br />
<br />
<b>.ogm .ogg</b><br />
<br />
����������� ��� ����� �����. ���� � ��� ���������� ������ �����, �� ��� �� �������� ������� 
<a href="http://www.winamp.com">WinAmp</a> ��� <a href="http://softella.com/la/index.ru.htm">Light Alloy</a>.<br />
<br />

<br />
<tr><td class=colhead>
<font color="#000000"><center>������ ������</center></font>
</td></tr>
<td align="left">

<b>.bin .cue .iso</b><br />
<br />
��� ����������� ������ CD-������. ����� ����� - ��� ������ ����� CD-�����. ���� ��������� ��������� �������� ���� ������. ����� �������� �� �� CD, � ������� <a href="http://www.ahead.de">Nero</a>
(���� ������) ��� ������������ ��������� ��� ������������ cd-rom, <a href="http://www.daemon-tools.cc/portal/portal.php">Daemon Tools</a>.
<br />

<br />
<b>.ccd .img .sub</b><br />
<br />
��� ������ ��������� <a href="http://www.elby.ch/english/products/clone_cd/index.html"> CloneCD</a>. ����� ��� ��, ��� � .bin .cue .iso.<br />
<br />

<br />
<tr><td class=colhead>
<font color="#000000"><center>������ �����</center></font>
</td></tr>
<td align="left">


<b>.txt .doc</b><br />
<br />
��������� �����. ����� � ����������� .txt ����� ������� � ����� ��������� ���������. ����� � ����������� .doc ����� ������� � ������� Microsoft Word.<br />
<br />
<br />
<b>.nfo</b><br />
<br />
����� � ���� ����������� �������� ���������� � ������, ������� �� �������. ������������� �� ������! ��� ��������� �����, ����� ���������� ascii-art. ������� ����� � ������� Notepad, Wordpad, <a href="http://www.damn.to/software/nfoviewer.html">DAMN NFO Viewer</a>
��� <a href="http://www.ultraedit.com/">UltraEdit</a>.<br />

<br />
<br />
<b>.pdf</b><br />
<br />
����������� � ������� <a href="http://www.adobe.com/products/acrobat/main.html">Adobe Acrobat Reader</a>.<br />
<br />
<br />
<b>.jpg .gif .tga .psd</b><br />
<br />
����������� �����. � �������� �������� ��������, ������� ����� � ������� Adobe
Photoshop ��� ����� ������ ����������� ����������.<br />
<br />

<br />
<b>.sfv</b><br />
<br />
������ ��� �������� ����������� ��������� ������. ��� �������� ����������� ��������� <a href="http://www.traction-software.co.uk/SFVChecker/">
SFVChecker</a> (���� ������) ��� <a href="http://www.big-o-software.com/products/hksfv/">hkSFV</a>.<br />
<br />

<br />

<tr><td class=colhead>
<font color="#000000"><center>������, ����������</center></font>
</td></tr>
<td align="left">

<b><h2 align="center">���� �������� ������ ��� ����������, ���������� � <a href=staff.php><u>�������������</u></a><h2></b>

                </td>
            </table>

<?
}

stdfoot();

?>