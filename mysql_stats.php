<?

require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

if (get_user_class() < UC_SYSOP)
	stderr("������", "������ ��������.");

$GLOBALS["byteUnits"] = array('����', '��', '��', '��', '��', '��', 'E�');

$day_of_week = array('�����������', '�����������', '�������', '�����', '�������', '�������', '�������');
$month = array('������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������');

$datefmt = '%d %B, %Y � %I:%M %p';
$timespanfmt = '%s ����, %s �����, %s ����� %s ������';
////////////////// FUNCTION LIST /////////////////////////

    function formatByteDown($value, $limes = 6, $comma = 0)
    {
        $dh           = pow(10, $comma);
        $li           = pow(10, $limes);
        $return_value = $value;
        $unit         = $GLOBALS['byteUnits'][0];

        for ( $d = 6, $ex = 15; $d >= 1; $d--, $ex-=3 ) {
            if (isset($GLOBALS['byteUnits'][$d]) && $value >= $li * pow(10, $ex)) {
                $value = round($value / ( pow(1024, $d) / $dh) ) /$dh;
                $unit = $GLOBALS['byteUnits'][$d];
                break 1;
            } // end if
        } // end for

        if ($unit != $GLOBALS['byteUnits'][0]) {
            $return_value = number_format($value, $comma, '.', ',');
        } else {
            $return_value = number_format($value, 0, '.', ',');
        }

        return array($return_value, $unit);
    } // end of the 'formatByteDown' function


    function timespanFormat($seconds)
    {
        $return_string = '';
        $days = floor($seconds / 86400);
        if ($days > 0) {
            $seconds -= $days * 86400;
        }
        $hours = floor($seconds / 3600);
        if ($days > 0 || $hours > 0) {
            $seconds -= $hours * 3600;
        }
        $minutes = floor($seconds / 60);
        if ($days > 0 || $hours > 0 || $minutes > 0) {
            $seconds -= $minutes * 60;
        }
        return (string)$days." ���� ". (string)$hours." ����� ". (string)$minutes." ����� ". (string)$seconds." ������ ";
    }


    function localisedDate($timestamp = -1, $format = '')
    {
        global $datefmt, $month, $day_of_week;

        if ($format == '') {
            $format = $datefmt;
        }

        if ($timestamp == -1) {
            $timestamp = time();
        }

        $date = preg_replace('@%[aA]@', $day_of_week[(int)strftime('%w', $timestamp)], $format);
        $date = preg_replace('@%[bB]@', $month[(int)strftime('%m', $timestamp)-1], $date);

        return strftime($date, $timestamp);
    } // end of the 'localisedDate()' function
    
////////////////////// END FUNCTION LIST /////////////////////////////////////


stdhead("��������� Mysql");
echo '<h2>'."\n".'������ ���� ������ (MYSQL)'."\n".'</h2><br>'."\n";

$res = @mysql_query('SHOW STATUS') or Die(mysql_error());
	while ($row = mysql_fetch_row($res)) {
		$serverStatus[$row[0]] = $row[1];
	}
@mysql_free_result($res);
unset($res);
unset($row);

// ������� �������
$res = @mysql_query('SELECT UNIX_TIMESTAMP() - ' . $serverStatus['Uptime']);
$row = mysql_fetch_row($res);
//echo sprintf("Server Status Uptime", timespanFormat($serverStatus['Uptime']), localisedDate($row[0])) . "\n";
?>

	<table id="torrenttable" border="1"><tr><td>

<?
print("Mysql �������� ". timespanFormat($serverStatus['Uptime']) .". ��� ������� ". localisedDate($row[0])) . "\n";
?>

	</td></tr></table>

<?
mysql_free_result($res);
unset($res);
unset($row);
//����� ���������� �������� N01heDc=
$queryStats = array();
$tmp_array = $serverStatus;
	foreach($tmp_array AS $name => $value) {
		if (substr($name, 0, 4) == 'Com_') {
			$queryStats[str_replace('_', ' ', substr($name, 4))] = $value;
			unset($serverStatus[$name]);
		}
	}
unset($tmp_array);
?>

<ul>
    <li>
        <!-- ������ ������� -->
        <b>������ �������: </b> ����� ������ �������� ������� � ������� ���������� �������
        <br />
        <table border="0">
            <tr>
                <td valign="top">
                    <table id="torrenttable" border="0">
                        <tr>
                            <th colspan="2" bgcolor="lightgrey">&nbsp;������&nbsp;</th>
                            <th bgcolor="lightgrey">&nbsp;&nbsp;�� ���&nbsp;</th>
                        </tr>
                        <tr>
                            <td bgcolor="#EFF3FF">&nbsp;���������&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo join(' ', formatByteDown($serverStatus['Bytes_received'])); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo join(' ', formatByteDown($serverStatus['Bytes_received'] * 3600 / $serverStatus['Uptime'])); ?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td bgcolor="#EFF3FF">&nbsp;�������&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo join(' ', formatByteDown($serverStatus['Bytes_sent'])); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo join(' ', formatByteDown($serverStatus['Bytes_sent'] * 3600 / $serverStatus['Uptime'])); ?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td bgcolor="lightgrey">&nbsp;�����&nbsp;</td>
                            <td bgcolor="lightgrey" align="right">&nbsp;<? echo join(' ', formatByteDown($serverStatus['Bytes_received'] + $serverStatus['Bytes_sent'])); ?>&nbsp;</td>
                            <td bgcolor="lightgrey" align="right">&nbsp;<? echo join(' ', formatByteDown(($serverStatus['Bytes_received'] + $serverStatus['Bytes_sent']) * 3600 / $serverStatus['Uptime'])); ?>&nbsp;</td>
                        </tr>
                    </table>
                </td>
                <td valign="top">
                    <table id="torrenttable" border="0">
                        <tr>
                            <th colspan="2" bgcolor="lightgrey">&nbsp;����������&nbsp;</th>
                            <th bgcolor="lightgrey">&nbsp;&oslash;&nbsp;�� ���&nbsp;</th>
                            <th bgcolor="lightgrey">&nbsp;%&nbsp;</th>
                        </tr>
                        <tr>
                            <td bgcolor="#EFF3FF">&nbsp;����������� �������&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo number_format($serverStatus['Aborted_connects'], 0, '.', ','); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo number_format(($serverStatus['Aborted_connects'] * 3600 / $serverStatus['Uptime']), 2, '.', ','); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo ($serverStatus['Connections'] > 0 ) ? number_format(($serverStatus['Aborted_connects'] * 100 / $serverStatus['Connections']), 2, '.', ',') . '&nbsp;%' : '---'; ?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td bgcolor="#EFF3FF">&nbsp;�������� ���������&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo number_format($serverStatus['Aborted_clients'], 0, '.', ','); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo number_format(($serverStatus['Aborted_clients'] * 3600 / $serverStatus['Uptime']), 2, '.', ','); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo ($serverStatus['Connections'] > 0 ) ? number_format(($serverStatus['Aborted_clients'] * 100 / $serverStatus['Connections']), 2 , '.', ',') . '&nbsp;%' : '---'; ?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td bgcolor="lightgrey">&nbsp;�����&nbsp;</td>
                            <td bgcolor="lightgrey" align="right">&nbsp;<? echo number_format($serverStatus['Connections'], 0, '.', ','); ?>&nbsp;</td>
                            <td bgcolor="lightgrey" align="right">&nbsp;<? echo number_format(($serverStatus['Connections'] * 3600 / $serverStatus['Uptime']), 2, '.', ','); ?>&nbsp;</td>
                            <td bgcolor="lightgrey" align="right">&nbsp;<? echo number_format(100, 2, '.', ','); ?>&nbsp;%&nbsp;</td>
</tr></table></td></tr></table></li><br /><li>

        <!-- ������� -->
        <? print("<b>���������� ��������: </b> � ������� ������� - ". number_format($serverStatus['Questions'], 0, '.', ',')." �������� ���� �������� �� ������.\n"); ?>
        <table border="0">
            <tr>
                <td colspan="2">
                    <br />
                    <table id="torrenttable" border="0" align="right">
                        <tr>
                            <th bgcolor="lightgrey">&nbsp;�����&nbsp;</th>
                            <th bgcolor="lightgrey">&nbsp;&oslash;&nbsp;��&nbsp;���&nbsp;</th>
                            <th bgcolor="lightgrey">&nbsp;&oslash;&nbsp;��&nbsp;�����&nbsp;</th>
                            <th bgcolor="lightgrey">&nbsp;&oslash;&nbsp;��&nbsp;������&nbsp;</th>
                        </tr>
                        <tr>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo number_format($serverStatus['Questions'], 0, '.', ','); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo number_format(($serverStatus['Questions'] * 3600 / $serverStatus['Uptime']), 2, '.', ','); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo number_format(($serverStatus['Questions'] * 60 / $serverStatus['Uptime']), 2, '.', ','); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo number_format(($serverStatus['Questions'] / $serverStatus['Uptime']), 2, '.', ','); ?>&nbsp;</td>
</tr></table></td></tr>
<tr>
                <td valign="top">
                    <table id="torrenttable" border="0">
                <tr><th colspan="2" bgcolor="lightgrey">&nbsp;���&nbsp;�������&nbsp;</th>
                <th bgcolor="lightgrey">&nbsp;&oslash;&nbsp;��&nbsp;���&nbsp;</th>
                <th bgcolor="lightgrey">&nbsp;%&nbsp;</th></tr>
<?

$useBgcolorOne = TRUE;
$countRows = 0;
foreach ($queryStats as $name => $value) {


?>
                        <tr>
                            <td bgcolor="#EFF3FF">&nbsp;<? echo htmlspecialchars($name); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo number_format($value, 0, '.', ','); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo number_format(($value * 3600 / $serverStatus['Uptime']), 2, '.', ','); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo number_format(($value * 100 / ($serverStatus['Questions'] - $serverStatus['Connections'])), 2, '.', ','); ?>&nbsp;%&nbsp;</td>
                        </tr>
<?
    $useBgcolorOne = !$useBgcolorOne;
    if (++$countRows == ceil(count($queryStats) / 2)) {
        $useBgcolorOne = TRUE;
?>
                    </table>
                </td>
                <td valign="top">
                    <table id="torrenttable" border="0">
                        <tr>
                            <th colspan="2" bgcolor="lightgrey">&nbsp;���&nbsp;�������&nbsp;</th>
                            <th bgcolor="lightgrey">&nbsp;&oslash;&nbsp;��&nbsp;���&nbsp;</th>
                            <th bgcolor="lightgrey">&nbsp;%&nbsp;</th>
                        </tr>
<?
    }
}
unset($countRows);
unset($useBgcolorOne);
?>
</table></td></tr></table></li>
<?
//Unset used variables
unset($serverStatus['Aborted_clients']);
unset($serverStatus['Aborted_connects']);
unset($serverStatus['Bytes_received']);
unset($serverStatus['Bytes_sent']);
unset($serverStatus['Connections']);
unset($serverStatus['Questions']);
unset($serverStatus['Uptime']);

if (!empty($serverStatus)) {
?>
    <br />
    <li>
        
        <b>������ ��������</b><br />
        <table border="0">
            <tr>
                <td valign="top">
                    <table id="torrenttable" border="0">
                        <tr>
                            <th bgcolor="lightgrey">&nbsp;�������&nbsp;</th>
                            <th bgcolor="lightgrey">&nbsp;��������&nbsp;</th>
                        </tr>
<?  $useBgcolorOne = TRUE;   $countRows = 0; foreach($serverStatus AS $name => $value) { ?>
                        <tr>
                            <td bgcolor="#EFF3FF">&nbsp;<? echo htmlspecialchars(str_replace('_', ' ', $name)); ?>&nbsp;</td>
                            <td bgcolor="#EFF3FF" align="right">&nbsp;<? echo htmlspecialchars($value); ?>&nbsp;</td>
                        </tr>
<?
        $useBgcolorOne = !$useBgcolorOne;
        if (++$countRows == ceil(count($serverStatus) / 3) || $countRows == ceil(count($serverStatus) * 2 / 3)) {
            $useBgcolorOne = TRUE;
?>
                    </table>
                </td>
                <td valign="top">
                    <table id="torrenttable" border="0">
                        <tr>
                            <th bgcolor="lightgrey">&nbsp;�������&nbsp;</th>
                            <th bgcolor="lightgrey">&nbsp;��������&nbsp;</th>
                        </tr>
<? } } unset($useBgcolorOne); ?>
</table></td></tr></table></li>
<? } ?>
</ul>
<? stdfoot(); ?>