<?php

$action = $_GET['action'];

if(acl_access("massmail", "", 1) == 'No') die("No access to this module");


if($action == "massmail") {
	$content .= "<table>";
	$content .= "<form method=POST action=?module=mail&action=sendMail>\n";
	$content .= "<tr><td>";
	$content .= "<select name=toMailList>";
	for($i=0;$i<count($mailList);$i++) {
		$content .= "<option value='$i'>".$mailList[$i]['name']."</option>\n";
	}
	$content .= "</select></td></tr>\n\n";
	$content .= "<tr><td>";
	$content .= "<input type=text name=subject size=40>";
	$content .= "</td></tr>\n\n";
	$content .= "<tr><td>";
	$content .= "<textarea class='mceEditor' name=message></textarea>";

	$content .= "</td></tr>\n\n<tr><td>";
	$content .= "<input type=submit value='".lang("Send Mail", "mail")."'>";
	$content .= "</td></tr>";

	$content .= "</form></table>";
} // End action


elseif($action == "sendMail" && isset($_POST['toMailList'])) {
	$toMailList = $_POST['toMailList'];

	$SQL = $mailList[$toMailList]['SQL'];
	if(empty($SQL)) die("No such group?");

	$qFindUsers = db_query($SQL);
	while($rFindUsers = db_fetch($qFindUsers)) {
		db_query("INSERT INTO ".$sql_prefix."_cronjobs
			SET cronModule = 'MAIL',
			toUser = '$rFindUsers->ID',
			senderID = '$sessioninfo->userID',
			subject = '".db_escape($_POST['subject'])."',
			content = '".db_escape($_POST['message'])."'");
	} // End while
	$log_new['MailListGroup'] = $toMailList;
	$log_new['toMailListSQL'] = $SQL;
	$log_new['message'] = $_POST['message'];
	log_add("mail", "sendmail_mass", serialize($log_new));
	header("Location: ?module=mail&sending=success");
} // End elseif action == sendSMS
