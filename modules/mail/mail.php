<?php

$action = $_GET['action'];

if($action == "massmail") {
	$content .= "<table>";
	$content .= "<form method=POST action=?module=mail&action=sendMail>\n";
	$content .= "<tr><td>";
	$content .= "<select name=toMailList>";
	for($i=0;$i<count($mailList);$i++) {
		$content .= "<option value='$i'>".$mailList[$i]['name']."</option>\n";
	}
	$content .= "</select></td></tr>";
	$content .= "<tr><td>";
	$content .= "<textarea class='mceEditor' name=message></textarea>";

	$content .= "</td></tr><tr><td>";
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
			toUser = '$rFindUsers->EMail',
			senderID = '$sessioninfo->userID',
			content = '".db_escape($_POST['message'])."'");
	} // End while

	header("Location: ?module=mail&sending=success");
} // End elseif action == sendSMS