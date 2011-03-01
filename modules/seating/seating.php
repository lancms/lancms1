<?php
#config("seating_enabled", $sessioninfo->eventID, 1);
$action = $_GET['action'];
$ticketID = $_GET['ticketID'];

// Check if we own the ticket, are the user, or if we have event-seating-rights
// Check if config(event_seating) is enabled
$qCheckTicketAccessInfo = db_query("SELECT COUNT(*) AS amount FROM ".$sql_prefix."_tickets WHERE (
        owner LIKE ".$sessioninfo->userID." OR
        user LIKE ".$sessioninfo->userID.") AND
        ticketID = '".db_escape($ticketID)."'");
$rCheckTicketAccessInfo = db_fetch($qCheckTicketAccessInfo);

$acl_event_seating = acl_access("seating", "", $sessioninfo->eventID);
$config_seating_enabled = config("seating_enabled", $sessioninfo->eventID);

if(($acl_event_seating == "Admin" ||
    $acl_event_seating == "Write" ||
    $rCheckTicketAccessInfo->amount == 1 ) &&
    $config_seating_enabled == TRUE) {

    $allow_seating = TRUE;
} // End if
else {
    $allow_seating = FALSE;
} // End else

if(!isset($action)) {
#	$seatX = $_GET['seatX'];
#	$seatY = $_GET['seatY'];
	include_once 'modules/seatmap/seatmap.php';

	if(!empty($_GET['seatX']) && !empty($_GET['seatY'])) {
        	// Display information about the seat

		$qFindSeatings = db_query("SELECT * FROM ".$sql_prefix."_seatReg_seatings 
			WHERE seatX = '".db_escape($_GET['seatX'])."'
			AND seatY = '".db_escape($_GET['seatY'])."'
			AND eventID = '$sessioninfo->eventID'");
		if(db_num($qFindSeatings) > 0) {
			$rFindSeatings = db_fetch($qFindSeatings);
			$qFindTicket = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE ticketID = '$rFindSeatings->ticketID'");
			$rFindTicket = db_fetch($qFindTicket);
#			$qFindUser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '$rFindTicket->user'");
#			$rFindUser = db_fetch($qFindUser);
			$content .= user_profile($rFindTicket->user);
#			$content .= $rFindUser->firstName." ".$rFindUser->lastName." ".lang("a.k.a.", "seating")." ".$rFindUser->nick;
#			if($sessioninfo->userID != 1 && $rFindUser->EMail != '') {
#				$qCheckMailSetting = db_query("SELECT * FROM ".$sql_prefix."_userPreferences WHERE name = 'allowViewMail' AND userID = '$rFindTicket->user'");
#				$rCheckMailSetting = db_fetch($qCheckMailSetting);
#				if($rCheckMailSetting->value == 'on') $content .= "<br />".lang("Contact this user: ", "seating").$rFindUser->EMail;
#			} // End if sessioninfo->userID != 1
		} // End db_num > 0	
	} // End !empty

	$content .= "<br /><br />";
	$content .= display_systemstatic("seatmap");

} // End if(!isset($action))


elseif($_GET['action'] == "takeseat") {
    $seatX = $_GET['seatX'];
    $seatY = $_GET['seatY'];
    $ticketID = $_GET['ticketID'];
    $eventID = $sessioninfo->eventID;
    $password = $_POST['password'];
    $newlog['ticketID'] = $ticketID;
    $newlog['seatX'] = $seatX;
    $newlog['seatY'] = $seatY;
    $newlog['password'] = $password;


    if(seating_rights($seatX, $seatY, $ticketID, $eventID, $password)) {
        // We have rights to seat that ticket. Update DB
        $qTicketInfo = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE ticketID = '".db_escape($ticketID)."'");
        $rTicketInfo = db_fetch($qTicketInfo);

        // Check if that ticket is already used
        $qCheckUsedTicket = db_query("SELECT * FROM ".$sql_prefix."_seatReg_seatings WHERE ticketID = '".db_escape($ticketID)."'");
        if(db_num($qCheckUsedTicket) == 0) {
			// Ticket has never been used. Insert it
			db_query("INSERT INTO ".$sql_prefix."_seatReg_seatings SET
			    eventID = '".db_escape($eventID)."',
			    ticketID = '".db_escape($ticketID)."',
		    seatX = '".db_escape($seatX)."',
		    seatY = '".db_escape($seatY)."'");
			db_query("UPDATE ".$sql_prefix."_tickets SET status = 'used'
		    WHERE ticketID = '".db_escape($ticketID)."'");
        } // End if ticket does not exist
        else {
			db_query("UPDATE ".$sql_prefix."_seatReg_seatings SET
		    seatX = '".db_escape($seatX)."',
		    seatY = '".db_escape($seatY)."'
		    WHERE ticketID = '".db_escape($ticketID)."'");
        } // End else
	log_add("seating", "takeseat", serialize($newlog));
    } // End if(seating_rights)
    else {
	// Failed seating_rights()
	log_add("seating", "failedTakeseat", serialize($newlog));
    } // End else
    header("Location: ?module=seating&seatX=$seatX&seatY=$seatY&ticketID=$ticketID");
} // End if action == "takeseat"

