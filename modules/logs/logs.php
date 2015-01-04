<?php

// FIXME: using nonexistant acl "logview" to make this globaladmin-only until we get global acls working
if (acl_access ("logview", "", $sessioninfo->eventID) != 'No')
{
	$action = $_GET['action'];
	$design_head .= "<link rel='stylesheet' type='text/css' href='templates/shared/logs.css' />";

	if (!isset ($action))
	{
		// FIXME: Hardcoding number of logentries to show:
		$num_of_rows = 30;

		$content .= "<h2>".lang ("Last 30 logentries", "logs")."</h2>";
		$content .= "<table class='logs'>";
		$content .= "<tr>";
		$content .= "<th>".lang ("Log", "logs")."#</th>";
		$content .= "<th>".lang ("Timestamp", "logs")."</th>";
		$content .= "<th>".lang ("User", "logs")."</th>";
		$content .= "<th>".lang ("Logmodule", "logs")."</th>";
		$content .= "<th>".lang ("Logtype", "logs")."</th>";
		$content .= "<th>".lang ("IP", "logs")." / ".lang ("Host", "logs")."</th>";
		$content .= "<th>".lang ("URL", "logs")."</th>";
		$content .= "</tr>";

		$logquery = sprintf ('SELECT ID, userID, INET_NTOA(userIP) as userIP, userHost, eventID, logModule, logFunction, logTextNew, logTextOld, logURL, logTime FROM %s_logs ORDER BY ID DESC LIMIT %s', $sql_prefix, $num_of_rows);
		$logresult = db_query ($logquery);

		$numrow = 1;
		while ($log = db_fetch ($logresult))
		{
			$userip = $log->userIP;
			if (!empty($log->userHost) && $log->userHost != "NULL")
			{
				$userip .= " (".$log->userHost.")";
			}

			$content .= "<tr class='logrow".$numrow."' onClick='location.href=\"index.php?module=logs&action=details&id=".$log->ID."\"' >";

			$content .= "<td>".$log->ID."</td>";
			$content .= "<td>".$log->logTime."</td>";
			$content .= "<td>".display_username($log->userID)."</td>";
#			$content .= "<td>".lang ("log_".$log->logModule."__". $log->logFunction, "logs")."</td>";
			$m = $log->logModule;
			$f = $log->logFunction;
			$mod = NULL;
			$func = NULL;
			switch($m) {
				case "arrival":
					$mod = lang("Arrival");
					if($f == "deleteTicket") $func = lang("Ticket deleted");
					elseif($f == "doAddOnsiteTicket") $func = lang("Onsite ticket added");
					elseif($f == "marknotpaid") $func = lang("Ticket marked not paid");
					elseif($f == "markpaid") $func = lang("Ticket marked paid");
					break;
				case "edituser":
					$mod = lang("Edituser");
					if($f == "doEditPreference") $func = lang("Edit preference");
					elseif($f == "doEditUserinfo") $func = lang("Edit userinfo");
					elseif($f == "setNewPass") $func = lang("New password set");
					break;
				case "eventadmin":
					$mod = lang("Eventadmin");
					if($f == "addAccessGroup") $func = lang("New accessgroup added");
					elseif($f == "doChangeRight") $func = lang("Groupright change");
					elseif($f == "doConfig") $func = lang("Configuration change");
					break;
				case "FAQ":
					$mod = lang("FAQ");
					if($f == "addFAQ") $func = lang("FAQ added");
					elseif($f == "deleteFAQ") $func = lang("FAQ deleted");
					break;
				case "forum":
					$mod = lang("Forum");
					if($f == "newpost") $func = lang("New post added");
					break;
				case "globaladmin":
					$mod = lang("Globaladmin");
					if($f == "doAddEvent") $func = lang("Event added");
					elseif($f == "setPublic") $func = lang("Set event public");
					break;
				case "globalconfig":
					$mod = lang("Globalconfig");
					if($f == "doConfig") $func = lang("Global configuration change");
					break;
				case "groups":
					$mod = lang("Groups");
					if($f == "addmember") $func = lang("Member added");
					elseif($f == "changeGroupRights") $func = lang("Changed grouprights");
					elseif($f == "clanCreate") $func = lang("Clan created");
					break;
				case "installer":
					$mod = "installer";
					if($f == "install/upgrade") $func = lang("System upgrade");
					break;
				case "login":
					$mod = lang("Login");
					if($f == "failed_password") $func = lang("Failed password login");
					elseif($f == "login") $func = lang("Successful login");
					elseif($f == "logout") $func = lang("Logout");
					elseif($f == "success") $func = lang("Successful login");
					break;
				case "mail":
					$mod = lang("Mail");
					if($f == "sendmail_mass") $func = lang("Mail sent (mass)");
					break;
				case "news":
					$mod = lang("News");
					if($f == "addArticle") $func = lang("Add new article");
					elseif($f == "edit") $func = lang("Article edit");
					break;
				case "register":
					$mod = lang("User registration");
					if($f == "anonymous") $func = lang("Anonymous registered new user");
					elseif($f == "registered") $func = lang("User registered new user");
					break;
				case "reseller":
					$mod = lang("Reseller");
					if($f == "addTicket") $func = lang("addTicket");
					break;
				case "seatadmin":
					$mod = lang("Seatadmin");
					if($f == "addcolumn") $func = lang("Seatmap column added");
					elseif($f == "addrow") $func = lang("Seatmap row added");
					elseif($f == "doresetmap") $func = lang("Seatmap reset");
					elseif($f == "doUpdateSeat") $func = lang("Seat updated");
					break;
				case "static":
					$mod = lang("Static");
					if($f == "addNewACL") $func = lang("New ACL");
					elseif($f == "editpage") $func = lang("Page edited");
					elseif($f == "newpage") $func = lang("New page added");
					elseif($f == "newsystem") $func = lang("New system-message added");
					elseif($f == "updatesystem") $func = lang("Systemmessage edited");
					break;
				case "ticketadmin":
					$mod = lang("Ticketadmin");
					if($f == "addTicketType") $func = lang("New tickettype added");
					elseif($f == "doEditTicket") $func = lang("Tickettype changed");
					break;
				case "ticketorder":
					$mod = lang("Ticketorder");
					if($f == "buyticket") $func = lang("Ticket bought");
					elseif($f == "cancelTicket") $func = lang("Ticket canceled");
					break;
				case "wannabe":
					$mod = lang("Wannabe");
					if($f == "doApplication") $func = lang("Apply");
					break;
				case "wannabeadmin":
					$mod = lang("Wannabeadmin");
					if($f == "doChangeComment") $func = lang("Change comment");
					break;
				case "sleepers":
					$mod = lang("Sleepers");
					if($f == "addsleeper") $func = lang("Add sleeper");
					if($f == "removesleeper") $func = lang("Remove sleeper");
					break;
				case "infoscreens":
					$mod = _("Infoscreens");
					if($f == "addScreen") $func = _("Add screen");
					if($f == "queueAdd") $func = _("Add to queue");
					if($f == "queueRemove") $func = _("Remove from queue");
					if($f == "newSlide") $func = _("New slide");
					if($f == "editSlide") $func = _("Edit slide");
					if($f == "rmSlide") $func = _("Remove slide");
					if($f == "rmScreen") $func = _("Remove screen");
					break;
				case "SMS":
					$mod = _("SMS");
					if($f == "sendSMS") $func = _("SMS sent");
					break;
				case "kioskadmin":
					$mod = _("Kiosk");
					if($f == "markcreditpaid") $func = _("Marked credit as paid");
					if($f == "addWare") $func = _("New item added");
					if($f == "addWareType") $func = _("New item category added");
					if($f == "doEditWare") $func = _("Item edited");
					break;
			} // End switch
			if(empty($mod)) $mod = lang("Unknown module")." ".$m;
			if(empty($func)) $func = lang("Unknown function")." ".$f;
			$content .= "<td>".$mod."</td>";
			$content .= "<td>".$func."</td>";
			$content .= "<td>".$userip."</td>";
			$content .= "<td>".$log->logURL."</td>";
			$content .= "</tr>";

			$numrow++;
			if ($numrow == 3)
			{
				$numrow = 1;
			}
		}
		$content .= "</table>";
	}
	elseif ($action == 'details')
	{
		$logid = $_GET['id'];
		if (empty ($logid) or !is_numeric ($logid) or !log_get ($logid))
		{
			header ('Location: index.php?module=logs');
			die ();
		}
		else
		{
			$log = log_get ($logid);

			$userip = long2ip ($log->userIP);
			if (!empty ($log->userHost))
			{
				$userip .= " (".$log->userHost.")";
			} // End if(!empty)

			$content .= "<h2>".lang ("Details for logentry ")." #".$logid."</h2>";
			$content .= "<a href='javascript:history.back()'>Back</a>";
			$content .= "<table>";
			$content .= "<tr class='logrow1'><th>".lang ("User", "logs")."</th><td>".display_username($log->userID)."</td></tr>";
			$content .= "<tr class='logrow2'><th>".lang ("Timestamp", "logs")."</th><td>".$log->logTime."</td></tr>";
			$content .= "<tr class='logrow1'><th>".lang ("IP", "logs")." / ".lang ("Host", "logs")."</th><td>".$userip."</td></tr>";
			$content .= "<tr class='logrow2'><th>".lang ("URL", "logs")."</th><td>".$log->logURL."</td></tr>";
			$content .= "<tr class='logrow1'><th>".lang ("Logmodule", "logs")."</th><td>".$log->logModule."</td></tr>";
			$content .= "<tr class='logrow2'><th>".lang ("Logfunction", "logs")."</th><td>".$log->logFunction."</td></tr>";
			$content .= "</table>";

			$content .= "<table><tr><td>";
			if(!empty($log->logTextNew) && $log->logTextNew != NULL) {
				$logNew = unserialize($log->logTextNew);

				$row = 1;
				$content .= "<table>";
				if(!empty($logNew)) foreach($logNew AS $type => $value) {

					$content .= "<tr class='logrow$row'><th>".$type."</th><td>".htmlentities($value)."</th></tr>";
					$row++;
					if($row == 3) $row = 1;
				} // End foreach logNew
				$content .= "</table>";
			} // End if(!empty(logTextNew)
			$content .= "</td><td>";

			if(!empty($log->logTextOld) && $log->logTextOld != NULL) {
				$logOld = unserialize($log->logTextOld);

				$row = 1;
				$content .= "<table>";
				if(!empty($logOld)) foreach($logOld AS $type => $value) {

					$content .= "<tr class='logrow$row'><th>".$type."</th><td>".$value."</th></tr>";
					$row++;
					if($row == 3) $row = 1;
				} // End foreach logOld
				$content .= "</table>";


			} // end if(!empty(logTextOld)
			$content .= "</td></tr></table>";
		} // End else
	} // End elseif action == details
}
else
{
	die ('No access');
}
