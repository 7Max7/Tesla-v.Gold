<?php
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

global $tracker_lang;

$content = "<a class=\"menu\" href=\"message.php\">&nbsp;".$tracker_lang['inbox']."</a>"
           ."<a class=\"menu\" href=\"message.php?action=viewmailbox&box=-1\">&nbsp;".$tracker_lang['outbox']."</a>";

$blocktitle = "<center>".$tracker_lang['messages']."</center>";
?>