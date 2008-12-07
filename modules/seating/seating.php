<?php
config("seating_enabled", $sessioninfo->eventID, 1);
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
    include_once 'modules/seatmap/seatmap.php';

} // End if(!isset($action))


elseif($_GET['action'] == "takeseat") {
    $seatX = $_GET['seatX'];
    $seatY = $_GET['seatY'];
    $ticketID = $_GET['ticketID'];
    $eventID = $sessioninfo->eventID;
    $password = $_POST['password'];


    if(seating_rights($seatX, $seatY, $ticketID, $eventID, $password)) {
        // We have rights to seat that ticket. Update DB
<<<<<<< .mine
        $qTicketInfo = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE ticketID = '".db_escape($ticketID)."'");
        $rTicketInfo = db_fetch($qTicketInfo);

=======
        $qTicketInfo = db_query("SELECT owner FROM ".$sql_prefix."_tickets WHERE ticketID = ".db_escape($ticketID));
        $rTicketInfo = db_fetch($qTicketInfo);
        
>>>>>>> .theirs
        // Check if that ticket is already used
        $qCheckUsedTicket = db_query("SELECT * FROM ".$sql_prefix."_seatReg_seatings WHERE ticketID = '".db_escape($ticketID)."'");
        if(db_num($qCheckUsedTicket) == 0) {
<<<<<<< .mine
			// Ticket has never been used. Insert it
			db_query("INSERT INTO ".$sql_prefix."_seatReg_seatings SET
			    eventID = '".db_escape($eventID)."',
			    ticketID = '".db_escape($ticketID)."',
			    seatX = '".db_escape($seatX)."',
			    seatY = '".db_escape($seatY)."',
			    userID = '".$rTicketInfo->user."'");
			db_query("UPDATE ".$sql_prefix."_tickets SET status = 'used'
		    WHERE ticketID = '".db_escape($ticketID)."'");
    	} // End if ticket does not exist
=======
	// Ticket has never been used. Insert it
	db_query("INSERT INTO ".$sql_prefix."_seatReg_seatings SET
	    eventID = '".db_escape($eventID)."',
	    ticketID = '".db_escape($ticketID)."',
	    seatX = '".db_escape($seatX)."',
	    userID = '".db_escape($rTicketInfo->owner)."',
	    seatY = '".db_escape($seatY)."'");
	db_query("UPDATE ".$sql_prefix."_tickets SET status = 'used' 
	    WHERE ticketID = '".db_escape($ticketID)."'");
        } // End if ticket does not exist
>>>>>>> .theirs
        else {
<<<<<<< .mine
			db_query("UPDATE ".$sql_prefix."_seatReg_seatings SET
		    seatX = '".db_escape($seatX)."',
		    seatY = '".db_escape($seatY)."',
		    userID = '".$rTicketInfo->user."'
		    WHERE ticketID = '".db_escape($ticketID)."'");
=======
	db_query("UPDATE ".$sql_prefix."_seatReg_seatings SET
	    seatX = '".db_escape($seatX)."',
	    seatY = '".db_escape($seatY)."',
	    userID = '".db_escape($rTicketInfo->owner)."'
	    WHERE ticketID = '".db_escape($ticketID)."'");
>>>>>>> .theirs
        } // End else
    } // End if(seating_rights)
<<<<<<< .mine
    header("Location: ?module=seating&seatX=$seatX&seatY=$seatY&ticketID=$ticketID");
=======
    header("Location: ?module=seating&ticketID=$ticketID&seatX=$seatX&seatY=$seatY");
>>>>>>> .theirs
} // End if action == "takeseat"
