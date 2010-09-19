<?php
$eventID = $sessioninfo->eventID;
$userID = $sessioninfo->userID;

// quick fix for missing acl
if ($sessioninfo->userID <= 1)
{
	header ('Location: index.php');
	die ();
}

if(!config("enable_ticketorder", $eventID)) die("Ticketorder not enabled");
$action = $_GET['action'];
$ticket = $_GET['ticket'];

if(!isset($action) || $action == "changeOwner" || $action == "changeUser" || $action == "findOwner" || $action == "findUser") {
    // No action set, display tickets

    $qDisplayTickets = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE eventID = '$eventID' AND (owner = '$userID' OR user = '$userID') AND status != 'deleted'");

    if(db_num($qDisplayTickets) != 0) {
        // The user has tickets to this event, display them
        $content .= "<table>";
	$content .= "<tr><th>".lang("Ticketnumber", "ticketorder");
	$content .= "</th><th>".lang("Tickettype", "ticketorder");
	$content .= "</th><th>".lang("Status", "ticketorder");
	$content .= "</th><th>".lang("Map placement", "ticketorder");
	$content .= "</th><th>".lang("User", "ticketorder");
	$content .= "</th><th>".lang("Owner", "ticketorder");


        while($rDisplayTickets = db_fetch($qDisplayTickets)) {
	$content .= "<tr><td>";
	$content .= $rDisplayTickets->ticketID;
	$content .= "</td><td>";
	$qCheckTicketType = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE eventID ='$eventID'
	    AND ticketTypeID = '$rDisplayTickets->ticketType'");
	$rCheckTicketType = db_fetch($qCheckTicketType);
	$content .= $rCheckTicketType->name;
	$content .= "</td><td>";
	$content .= lang($rDisplayTickets->status, "ticketorder");
	$content .= "</td><td>";
	if($rDisplayTickets->status == 'notused' && config("seating_enabled", $sessioninfo->eventID)) {
	    $content .= "<a href=\"?module=seating&ticketID=$rDisplayTickets->ticketID\">";
	    $content .= lang("Place on map", "ticketorder");
	    $content .= "</a>";
	} elseif($rDisplayTickets->status == 'notpaid') {
		$content .= lang("Not paid", "ticketorder");
	} elseif(!config("seating_enabled", $sessioninfo->eventID)) {
		$content .= lang("Seating not enabled yet", "ticketorder");
	} else {
	    $qTicketUsedWhere = db_query("SELECT seatX,seatY FROM ".$sql_prefix."_seatReg_seatings
	    	WHERE ticketID = ".$rDisplayTickets->ticketID);
	    $rTicketUsedWhere = db_fetch($qTicketUsedWhere);
	    $content .= "<a href=\"?module=seating&ticketID=$rDisplayTickets->ticketID&amp;seatX=$rTicketUsedWhere->seatX&amp;seatY=$rTicketUsedWhere->seatY\">";
	    $content .= lang("Update map", "ticketorder");
	    $content .= "</a>";
	}
	$content .= "</td><td>";

	if(($action == "changeUser" || $action == "findUser") && $ticket == $rDisplayTickets->ticketID && ($rDisplayTickets->owner == $sessioninfo->userID || acl_access("seating", "", $sessioninfo->eventID) == 'Admin')) {
		$content .= "<form method=POST action=?module=ticketorder&action=findUser&ticket=$ticket>\n";
		$content .= "<input type=text name=searchUser value='".$_POST['searchUser']."'>\n";
		$content .= "<br><input type=submit value='".lang("Search user", "ticketorder")."'>\n";
		$content .= "</form>\n";
		if($action== "findUser") {
			$search = db_escape($_POST['searchUser']);
			$qFindUser = db_query("SELECT ID FROM ".$sql_prefix."_users
				WHERE nick LIKE '%$search%'
				OR firstName LIKE '%$search%'
				OR lastName LIKE '%$search%'
			");
			if(db_num($qFindUser) > 30 ) {
				$content .= lang("Found to many users matching, please specify", "ticketorder");
			} // Found to many matches on search
			else {
				$content .= "<ul>";
				while($rFindUser = db_fetch($qFindUser)) {
					$content .= "<li><a href=?module=ticketorder&action=doChangeUser&ticket=$ticket&toUser=$rFindUser->ID>";
					$content .= display_username($rFindUser->ID);
					$content .= "</a></li>";
				} // End while rFindUser
				$content .= '</ul>';
			} // End else (db_num())
		} // End if action== FindUser
	} elseif($rDisplayTickets->owner == $sessioninfo->userID && empty($action)) {
		$content .= "<a href=?module=ticketorder&action=changeUser&ticket=$rDisplayTickets->ticketID>";
		$content .= display_username($rDisplayTickets->user);
		$content .= "</a>";
	} else {
		$content .= display_username($rDisplayTickets->user);
	}
	$content .= "</td><td>";
	if(($action == "changeOwner" || $action == "findOwner") && $ticket == $rDisplayTickets->ticketID && ($rDisplayTickets->owner == $sessioninfo->userID || acl_access("seating", "", $sessioninfo->eventID) == 'Admin')) {
		$content .= "<form method=POST action=?module=ticketorder&action=findOwner&ticket=$ticket>\n";
		$content .= "<input type=text name=searchOwner value='".$_POST['searchOwner']."'>\n";
		$content .= "<br><input type=submit value='".lang("Search owner", "ticketorder")."'>\n";
		$content .= "</form>\n";
		if($action== "findOwner") {
			$search = db_escape($_POST['searchOwner']);
			$qFindOwner = db_query("SELECT ID FROM ".$sql_prefix."_users
				WHERE nick LIKE '%$search%'
				OR firstName LIKE '%$search%'
				OR lastName LIKE '%$search%'
			");
			if(db_num($qFindOwner) > 30) {
				$content .= lang("Found to many users matching, please specify", "ticketorder");
			} // Found to many matches on search
			else {
				$content .= "<ul>";
				while($rFindOwner = db_fetch($qFindOwner)) {
					$content .= "<li><a href=?module=ticketorder&action=doChangeOwner&ticket=$ticket&toOwner=$rFindOwner->ID>";
					$content .= display_username($rFindOwner->ID);
					$content .= "</a></li>";
				} // End while rFindOwner
			} // End else (db_num())
		} // End if action== FindOwner
	} elseif($rDisplayTickets->owner == $sessioninfo->userID && empty($action)) {
		$content .= "<a href=?module=ticketorder&action=changeOwner&ticket=$rDisplayTickets->ticketID>";
		$content .= display_username($rDisplayTickets->owner);
		$content .= "</a>";
	} else {
		$content .= display_username($rDisplayTickets->owner);
	}
	$content .= "</td><td>";
	if($rDisplayTickets->paid == 'no' && !in_array($rCheckTicketType->type, array('onsite-visitor', 'onsite-computer'))) {
		$content .= "<a href=\"?module=ticketorder&action=cancelTicket&ticket=$rDisplayTickets->ticketID\">";
		$content .= lang("Cancel ticket", "ticketorder");
		$content .= "</a>";
	} // End if
	elseif($rDisplayTickets->paid == 'yes') {
		$content .= lang("Paid", "ticketorder");
	}
	$content .= "</td></tr>";
        } // End while
        $content .= "</table>";
    } // End if(db_num != 0);

    $qListBuyTickets = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE eventID = '$eventID' AND type IN ('prepaid','preorder') AND active = 1");
    if(db_num($qListBuyTickets) != 0 && db_num($qDisplayTickets) <$maxTicketsPrUser) {
        $content .= "<table>\n";
        while($rListBuyTickets = db_fetch($qListBuyTickets)) {
		if($rListBuyTickets->maxTickets > 1) { // FIXME: Actually limit users from buying more than this
			$qCheckSold = db_query("SELECT COUNT(*) AS amount FROM ".$sql_prefix."_tickets WHERE ticketType = '$rListBuyTickets->ticketTypeID'");
			$rCheckSold = db_fetch($qCheckSold);
			$free_tickets = $rListBuyTickets->maxTickets - $rCheckSold->amount;
			$free_tickets_text = " (".$free_tickets." ".lang("ledige", "ticketorder").") ";
		}
		$content .= "<tr><td>";
		$content .= $rListBuyTickets->name;
		$content .= $free_tickets_text."</td><td>\n\n";
		$content .= "<form method=POST action=?module=ticketorder&action=buyticket&tickettype=$rListBuyTickets->ticketTypeID>\n";
		$content .= "<input name=numTickets value=1>\n";
		$content .= "<input type=submit value='".lang("Buy ticket")."'>\n";
		$content .= "</form>\n\n";
		$content .= "</td></tr>\n";
        } // End while
    } // End if(db_num(qListBuyTickets)
    if(config("enable_reseller", $sessioninfo->eventID)) {
	$content .= "<tr><td>".lang("Ticketcode from reseller", "ticketorder")."</td>\n";
	$content .= "<form method=POST action=?module=ticketorder&action=buyticket>\n";
	$content .= "<td><input type=text name=resellercode size=10>\n";
	$content .= "<input type=submit value='".lang("Claim ticket", "ticketorder")."'>\n";
	$content .= "</form></td></tr>";
    } // End config(enable_reseller)
    $content .= "</table>";
    $content .= "<br /><br />";
    $content .= display_systemstatic("ticketorder");
} // End if !isset($action)

elseif($action == "buyticket" && !empty($_GET['tickettype']) && !empty($_POST['numTickets'])) {
    // Buy tickets
    $numTickets = $_POST['numTickets'];
    $tickettype = $_GET['tickettype'];
    if($numTickets > $maxTicketsPrUser) $numTickets = $maxTicketsPrUser;
    while($numTickets) {
        // Check what type the ticket has
        $qTicketType = db_query("SELECT type FROM ".$sql_prefix."_ticketTypes WHERE ticketTypeID = ".db_escape($tickettype));
        $rTicketType = db_fetch($qTicketType);
        // Check how many tickets the user already has
        $qUserNumTickets = db_query("SELECT COUNT(*) AS count FROM ".$sql_prefix."_tickets WHERE eventID = '$eventID'
	AND (owner = '$sessioninfo->userID' OR user = '$sessioninfo->userID')");
        $rUserNumTickets = db_fetch($qUserNumTickets);
        if($rUserNumTickets->count >= $maxTicketsPrUser) {
		 // Do noting if we've maxed maxTicketsPrUser
		$logmsg['failed_add_maxtickets'] = $rUserNumTickets->count;
	}
        else { // If we have not yet reached maxTicketsPrUser, add the ticket
	if($rTicketType->type == "prepaid") $status = 'notpaid';
	elseif($rTicketType->type == 'preorder') $status = 'notused';

	db_query("INSERT INTO ".$sql_prefix."_tickets SET
	    owner = '$sessioninfo->userID',
	    creator = '$sessioninfo->userID',
	    user = '$sessioninfo->userID',
	    eventID = '$eventID',
	    ticketType = '".db_escape($tickettype)."',
	    status = '$status',
	    createTime = ".time());
        } // End else (maxTicketsPrUser)
        $numTickets--; // Decrease numTickets
    } // End while(numtickets
    $logmsg['tickettype'] = $tickettype;
    $logmsg['numTickets'] = $_POST['numTickets'];
    log_add("ticketorder", "buyticket", serialize($logmsg));
    header("Location: ?module=ticketorder");
} // End action = buyticket
elseif($action == "buyticket" && !empty($_POST['resellercode'])) {
	$code = $_POST['resellercode'];

	$qFindTicket = db_query("SELECT * FROM ".$sql_prefix."_ticketReseller WHERE resellerTicketID = '".db_escape($code)."'");
	$rFindTicket = db_fetch($qFindTicket);
	if(db_num($qFindTicket) == 0) {
		$content .= lang("Could not find that code. Are you sure you typed it correctly?", "ticketorder");
		$content .= "<a href='javascript:history.back()'>".lang("Back", "ticketorder")."</a>\n";
	}
	elseif($rFindTicket->eventID != $sessioninfo->eventID) {
		$content .= lang("Code found, but it's not for this event. Try finding a newer piece for paper with a newer code", "ticketorder");
		$content .= "<a href='javascript:history.back()'>".lang("Back", "ticketorder")."</a>\n";
	}
	elseif($rFindTicket->used == 'yes') {
		$content .= lang("Code found, but it has already been used", "ticketorder");
		$content .= "<a href='javascript:history.back()'>".lang("Back", "ticketorder")."</a>\n";
	}	
	else {
		// Code can be used
		db_query("UPDATE ".$sql_prefix."_ticketReseller SET used = 'yes' WHERE resellerTicketID = '".db_escape($code)."'");
		db_query("INSERT INTO ".$sql_prefix."_tickets SET
			paid= 'yes',
			ticketType = '$rFindTicket->ticketType',
			user = '$sessioninfo->userID',
			owner = '$sessioninfo->userID',
			creator = '$sessioninfo->userID',
			eventID = '$sessioninfo->eventID',
			status = 'notused',
			createTime = '".time()."'");
		header("Location: ?module=ticketorder");
	}
	
} // End elseif action == buyticket && !empty(resellercode)	
elseif($action == "doChangeOwner") {
    $toOwner = $_GET['toOwner'];
    $ticket = $_GET['ticket'];

    
    $qFindTicket = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE ticketID = '".db_escape($ticket)."'");
    $rFindTicket = db_fetch($qFindTicket);

    if($sessioninfo->userID != $rFindTicket->owner && acl_access("seating", "", $sessioninfo->eventID) != 'Admin');
    else {
        db_query("UPDATE ".$sql_prefix."_tickets SET owner = '".db_escape($toOwner)."' WHERE ticketID = '".db_escape($ticket)."'");
    }
    header("Location: ?module=ticketorder");
} // End elseif(action == doChangeOwner)

elseif($action == "doChangeUser") {
    $toOwner = $_GET['toUser'];
    $ticket = $_GET['ticket'];

    
    $qFindTicket = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE ticketID = '".db_escape($ticket)."'");
    $rFindTicket = db_fetch($qFindTicket);

    if($sessioninfo->userID != $rFindTicket->owner && acl_access("seating", "", $sessioninfo->eventID) != 'Admin');
    else {
        db_query("UPDATE ".$sql_prefix."_tickets SET user = '".db_escape($toOwner)."' WHERE ticketID = '".db_escape($ticket)."'");
    }
    header("Location: ?module=ticketorder");
} // End elseif(action == doChangeOwner)

elseif(($action == "cancelTicket" || $action == "doCancelTicket") && isset($_GET['ticket'])) {
	$ticket = $_GET['ticket'];

	$qGetTicketInfo = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE ticketID = '".db_escape($ticket)."'");
	$rGetTicketInfo = db_fetch($qGetTicketInfo);

	$allow_cancel = false;
	
	if(acl_access("ticketadmin", "", $sessioninfo->eventID) == ('Admin' || 'Write')) $allow_cancel = true;
	elseif($sessioninfo->userID == $rGetTicketInfo->owner) $allow_cancel = true;

	if($action == "cancelTicket" && $allow_cancel == true) {
		$content .= lang("Are you sure you wish to delete this ticket?", "ticketorder");
		$content .= "<br><a href=?module=ticketorder>".lang("No, this would be a mistake", "ticketorder")."</a> - ";
		$content .= "<a href=?module=ticketorder&action=doCancelTicket&ticket=$ticket>".lang("Yes, I don't need it", "ticketorder")."</a>";
	} elseif($action == "doCancelTicket" && $allow_cancel == true) {
		
		// Delete the ticket
		db_query("DELETE FROM ".$sql_prefix."_tickets WHERE ticketID = '".db_escape($ticket)."'");
		$logmsg[] = $rGetTicketInfo->ticketID;
		$logmsg[] = $rGetTicketInfo->ticketType;
		$logmsg[] = $rGetTicketInfo->owner;
		$logmsg[] = $rGetTicketInfo->user;
		$logmsg[] = $rGetTicketInfo->status;
		$logmsg[] = $rGetTicketInfo->paid;
		// Delete the seating for this ticket
		$qGetSeating = db_query("SELECT * FROM ".$sql_prefix."_seatReg_seatings WHERE ticketID = '".db_escape($ticket)."'");
		$rGetSeating = db_fetch($qGetSeating);
		$logmsg[] = $rGetSeating->seatX."x".$rGetSeating->seatY."y";
		db_query("DELETE FROM ".$sql_prefix."_seatReg_seatings WHERE ticketID = '".db_escape($ticket)."'");
		log_add("ticketorder", "cancelTicket", serialize($logmsg));
		header("Location: ?module=ticketorder");
	}

} // End elseif action == cancelTicket || action = doCancelTicket
