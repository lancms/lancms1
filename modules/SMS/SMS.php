<?php

$acl_access = acl_access("sendSMS", "", 1);

if($acl_access != 'Write' && $acl_access != 'Admin') die("No access to SMS");

$action = $_GET['action'];

if(empty($action)) {

	$design_head .= "<script type='text/javascript'>
			  function checkLength(from, where) {
						 var len = document.getElementById(from).value.length;
						 document.getElementById(where).innerHTML = len;
							}  
						</script>
						 ";

	$content .= "<table>";
	$content .= "<form method='POST' action='?module=SMS&action=sendSMS'>\n";
	$content .= "<tr><td>";
	$content .= "<select name='toSmsList'>";
	for($i=0;$i<count($smsList);$i++) {
		$content .= "<option value='$i'>".$smsList[$i]['name']."</option>\n";
	}
	$content .= "</select></td></tr>";
	$content .= "<tr><td>";
	$content .= "<textarea onkeyup='checkLength(\"message\", \"count\");' onkeydown='checkLength(\"message\", \"count\");' id='message' name='message'></textarea>";

	$content .= "</td></tr><tr><td>";
	$content .= "<input type='submit' value='".lang("Send SMS", "SMS")."'>";
	$content .= "</td></tr>";

	$content .= "<tr><th>";
	$content .= _("Number of characters entered:")." ";
	$content .= "<span id='count'>0</span>";
	$content .= "</th></tr>";

	$content .= "</form></table>";
} // End action


elseif($action == "sendSMS" && isset($_POST['toSmsList'])) {
	$toSmsList = $_POST['toSmsList'];

	$SQL = $smsList[$toSmsList]['SQL'];
	if(empty($SQL)) die("No such group?");

	$qFindUsers = db_query($SQL);
	while($rFindUsers = db_fetch($qFindUsers)) {
		db_query("INSERT INTO ".$sql_prefix."_cronjobs
			SET cronModule = 'SMS',
			toUser = '$rFindUsers->cellphone',
			senderID = '$sessioninfo->userID',
			content = '".db_escape($_POST['message'])."'");
	} // End while
	$log_new['toListName'] = $smsList[$toSmsList]['name'];
	$log_new['message'] = $_POST['message'];
	log_add("SMS", "sendSMS", serialize($log_new));
	header("Location: ?module=SMS&sending=success");
} // End elseif action == sendSMS

else echo "???";
