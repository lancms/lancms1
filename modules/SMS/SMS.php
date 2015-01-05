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

	$content .= "<h2>"._("Create SMS")."</h2>";

	$content .= "<table>";
	$content .= "<form method='POST' action='?module=SMS&action=previewSMS'>\n";
	$content .= "<tr><td>";
	$content .= _("Select recipient(s):")." <select name='toSmsList'>";
	for($i=0;$i<count($smsList);$i++) {
		$content .= "<option value='$i'>".$smsList[$i]['name']."</option>\n";
	}
	$content .= "</select></td></tr>";
	$content .= "<tr><td>";
	# FIXME? Hardcoded textarea width and height
	$content .= "<textarea style='margin-top: 10px; width: 300px; height: 200px;' onkeyup='checkLength(\"message\", \"count\");' onkeydown='checkLength(\"message\", \"count\");' id='message' name='message'></textarea>";

	$content .= "</td></tr><tr><td>";
	$content .= "<input type='submit' value='"._("Preview SMS")."'>";
	$content .= "</td></tr>";

	$content .= "<tr><th>";
	$content .= _("Number of characters entered:")." ";
	$content .= "<span id='count'>0</span>";
	$content .= "</th></tr>";

	$content .= "</form></table>";
} // End action

elseif ($action == "previewSMS" && isset ($_POST['toSmsList']))
{
	$toSmsList = $_POST['toSmsList'];
	$SQL = $smsList[$toSmsList]['SQL'];
	$msgcontent = $_POST['message'];
	if (empty ($SQL))
	{
		# FIXME: die ()
		die ("No such group?");
	}
	$qCellphone = db_query ($SQL);
	
	if (!db_num($qCellphone))
	{
		# FIXME: die ()
		die ("No users in group..?");
	}
	

	$content .= "<h2>"._("Preview SMS")."</h2>\n";

	$content .= "<h3>"._("Recipients")."</h3>\n";
	
	$content .= "<p>"._("Sends to group:")." <strong>".$smsList[$toSmsList]['name']."</strong></p>\n";

	$content .= "<table>\n";
	$content .= "<tr>\n";
	$content .= "<th>"._("Username")."</th>";
	$content .= "<th>"._("Name")."</th>";
	$content .= "<th>"._("Number")."</th>";
	$content .= "</tr>\n";

	$rownum = 1;
	while ($rCellphone = db_fetch ($qCellphone))
	{
		if ($rownum == 3)
		{
			$rownum = 1;
		}
		
		$qUser = db_query ("SELECT * FROM ".$sql_prefix."_users WHERE ID='".db_escape($rCellphone->ID)."'");
		$rUser = db_fetch ($qUser);

		$content .= "<tr class='row".$rownum."'>\n";
		$content .= "<td>".$rUser->nick."</td>\n";
		$content .= "<td>".$rUser->firstName." ".$rUser->lastName."</td>\n";
		$content .= "<td>".$rCellphone->cellphone."</td>\n";
		$content .= "</tr>\n";

		$rownum++;
		unset ($rUser);
		unset ($qUser);
	}
	$content .= "</table>\n";
	
	$content .= "<h3>"._("Message content")."</h3>\n";
	# FIXME? Hardcoded textarea width and height
	$content .= "<textarea style='width: 300px; height: 200px;' disabled>".htmlentities($msgcontent)."</textarea>\n";
	$content .= "<p>"._("Number of characters entered:")." ".strlen($msgcontent)."</p>\n";

	$content .= "<form method='POST' action='?module=SMS&action=sendSMS'>\n";
	$content .= "<input type='button' onClick='javascript:history.back()' value='"._("Back")."' />\n";
	$content .= "<input type='submit' value='"._("Send SMS")."' />\n";
	$content .= "<input type='hidden' name='toSmsList' value='".$_POST['toSmsList']."' />\n";
	$content .= "<input type='hidden' name='message' value='".$_POST['message']."' />\n";
	$content .= "</form>\n";
}
	



elseif($action == "sendSMS" && isset($_POST['toSmsList'])) {
	$toSmsList = $_POST['toSmsList'];

	$SQL = $smsList[$toSmsList]['SQL'];
	if(empty($SQL))
	{
		# FIXME: die ()
		die("No such group?");
	}

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

# FIXME: less than usefull error message
else echo "???";
