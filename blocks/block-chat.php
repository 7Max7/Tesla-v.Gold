<?php
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}
$blocktitle = "��������� (� 20-00 �� 23-00 ��� ��� �������)";

global $tracker_lang, $CURUSER;

?>

<?

$content .= "<form action=\"shoutbox.php\" method=\"post\" name=\"shoutform\" onsubmit=\"return sendShout(this);\">"; 
if($CURUSER){

$content .= "<table cellspacing=0 cellpadding=5 height=200 width=100% >";
$content .= "<tr>
<td><div class=\"editorinput\"id=\"shoutbox\" style=\"overflow: auto; height: 200px; width: 100%; padding-top: 0cm\">��������...</div>

</div><hr width=100%>
";
}
$content .="</table><center><input type=text name=shout size=100% /><input type=submit value=��������� /></center><center><a href=javascript:winop()>��� ��������</a></center></td></tr></form>";
?>
<div id="loading-layer" style="display: none; font-family: Lucida Sans Unicode; font-size: 11px; width: 200px; height: 50px; background: #EDFCEF; padding: 10px; text-align: center; border: 1px solid #000">
    <div style="font-weight:bold" id="loading-layer-text"><font color="red"> ��������. ����������, ���������...</font></div><br />
    <img src="pic/loading.gif" border="0" />
</div>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript">
<!--

function winop()
{
windop = window.open("moresmiles.php?form=shoutform&text=shout","mywin","height=500,width=600,resizable=no,scrollbars=yes");
}

function sendShout(formObj) {

    /*if (postingShout) {
        alert('�������� ���������...')
        return false
    }*/

    Shout = formObj.shout.value

    if (Shout.replace(/ /g, '') == '') {
        alert('�� ������ ����� ���������!')
        return false
    }

    sb_Clear();

    var ajax = new tbdev_ajax();
    ajax.onShow ('');
    //ajax.onShow = function() { };
    var varsString = "";
    ajax.requestFile = "shoutbox.php";
    ajax.setVar("do", "shout");
    ajax.setVar("shout", escape(Shout));
    ajax.method = 'GET';
    ajax.element = 'shoutbox';
    ajax.sendAJAX(varsString);

    return false
}

function getShouts() {

    var ajax = new tbdev_ajax();
    ajax.onShow = function() { };
    var varsString = "";
    ajax.requestFile = "shoutbox.php";
    ajax.method = 'GET';
    ajax.element = 'shoutbox';
    ajax.sendAJAX(varsString);
    setTimeout("getShouts();", 300);

    return false

}


function sb_Clear() {
    document.forms["shoutform"].shout.value = ''
    return true;
}

function getWOL() {

    var ajax = new tbdev_ajax();
    ajax.onShow = function() { };
    var varsString = "";
    ajax.requestFile = "online.php";
    ajax.method = 'GET';
    ajax.setVar("wol", 1);
    ajax.element = 'wol';
    ajax.sendAJAX(varsString);
    setTimeout("getWOL();", 10000);
    return false
}

function deleteShout(id) {

    if (confirm("�� ����� ������ ������� ��� ���������?")) {
        var ajax = new tbdev_ajax();
        ajax.onShow = function() { };
        var varsString = "";
        ajax.requestFile = "shoutbox.php";
        ajax.setVar("do", "delete");
        ajax.setVar("id", id);
        ajax.method = 'GET';
        ajax.element = 'shoutbox';
        ajax.sendAJAX(varsString);
    }
    
    return false

}
getShouts();

stdfoot();
-->
</script>