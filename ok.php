<? 
require_once("include/bittorrent.php");

dbconn();

if (!mkglobal("type")) {
header("Refresh: 2; url=index.php");
	die("�� ������ type");
}

if ($type == "signup" && mkglobal("email")) {
	if (!validemail($email))
		stderr($tracker_lang['error'], "��� �� ������ �� �������� email �����.");
	stdhead($tracker_lang['signup_successful']);
        stdmsg($tracker_lang['signup_successful'],($use_email_act ? sprintf($tracker_lang['confirmation_mail_sent'], htmlspecialchars($email)) : sprintf($tracker_lang['thanks_for_registering'], $SITENAME)));
        @unlink(ROOT_PATH."cache/block-online.txt");
	stdfoot();
}
elseif ($type == "sysop") {
		stdhead($tracker_lang['sysop_activated']);
	if (isset($CURUSER))
		stdmsg($tracker_lang['sysop_activated'],sprintf($tracker_lang['sysop_account_activated'], $DEFAULTBASEURL));
	else
		print("<p>Your account has been activated! However, it appears that you could not be logged in automatically. A possible reason is that you disabled cookies in your browser. You have to enable cookies to use your account. Please do that and then <a href=\"login.php\">log in</a> and try again.</p>\n");
	mkglobal('email');

	stdfoot();
	}
elseif ($type == "confirmed") {
	stdhead($tracker_lang['account_activated']);
	stdmsg($tracker_lang['account_activated'], $tracker_lang['this_account_activated']);
	stdfoot();
}
elseif ($type == "confirm") {


		stdhead("������������� �����������");
		print("<h1>��� ������� ������� �����������!</h1>\n");
		print("<p>��� ������� ������ �����������! �� ������������� �����. ������ �� ������ <a href=\"$DEFAULTBASEURL/\"><b>������� �� �������</b></a> � ������ ������������ ��� �������.</p>\n");
		print("<p>������ ��� ������ ������������ $SITENAME �� ����������� ��� ��������� <a href=\"rules.php\"><b>�������</b></a> � <a href=\"faq.php\"><b>����</b></a>.</p>\n");
		stdfoot();

}
else
	die("���� ������ �� �������� �� ���� ��������?");

?>