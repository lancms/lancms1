<?php

$action = $_GET['action'];

$acl_ticket = acl_access("ticketadmin", "", $sessioninfo->eventID);
$acl_seating = acl_access("seating", "", $sessioninfo->eventID);

if($acl_ticket == 'No') die("No access to ticketadmin");

if(!isset($action) || $action == "searchUser") {

	$search = $_POST['searchUser'];
	$content .= "<form method=POST action=?module=arrival&action=searchUser>\n";
	$content .= "<input type=text name=searchUser value='$search'>\n ";
	$content .= "<input type=submit value='".lang("Search user")."'>";
	$content .= "</form>";

		$qFindUser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE
			(nick LIKE '%".db_escape($search)."%'
			OR firstName LIKE '%".db_escape($search)."%'
			OR lastName LIKE '%".db_escape($search)."%'
			OR CONCAT(firstName, ' ', lastName) LIKE '%".db_escape($search)."%'
			OR EMail LIKE '%".db_escape($search)."%')
			AND ID != 1");
	$content .= "<table>\n";
	$listrowcount = 1;
	while($rFindUser = db_fetch($qFindUser)) {
		$content .= "<tr class='listRow".$listrowcount."'><td>";
		$content .= display_username($rFindUser->ID);
		$content .= "</td><td>";
		$qFindTickets = db_query("SELECT * FROM ".$sql_prefix."_tickets
			WHERE eventID = '$sessioninfo->eventID'
			AND user = '$rFindUser->ID'");
		while($rFindTickets = db_fetch($qFindTickets)) {
			if($rFindTickets->paid == 'yes')
				$style = 'green';
			else $style = 'orange';

			$content .= "<li><font style='background-color: $style;'>";
			$content .= "<a href=?module=arrival&action=ticketdetail&ticket=$rFindTickets->ticketID>";
			$content .= tickettype_getname($rFindTickets->ticketType)."</a></font></li>";
		} // End while rFindTickets
		$content .= "<li><a href=?module=arrival&action=addTicket&user=$rFindUser->ID>".lang("Add new ticket", "arrival")."</a></li>";
		$content .= "</td></tr>";

		$listrowcount++;
                if ($listrowcount == 3)
                {
                        $listrowcount = 1;
                }


	} // End while
	$content .= "</table>";
}

elseif($action == "ticketdetail" && isset($_GET['ticket'])) {
	$ticket = $_GET['ticket'];
	$qFindTicket = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE ticketID = '".db_escape($ticket)."'");
	$rFindTicket = db_fetch($qFindTicket);
	$qFindUser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '$rFindTicket->user'");
	$rFindUser = db_fetch($qFindUser);

	$content .= "<a href=?module=arrival>".lang("Back to search", "arrival")."</a>\n";

	$content .= "<table>\n";

	$content .= "<tr><td>".lang("Ticket", "arrival");
	$qFindTicketType = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE ticketTypeID = '$rFindTicket->ticketType'");
	$rFindTicketType = db_fetch($qFindTicketType);
	$content .= "</td><td>";
	$content .= $rFindTicketType->name." (".$rFindTicketType->type.")";
	$content .= "<br />";
	$content .= $rFindTicketType->price;
	$content .= "</td></tr>\n";


	// display name
	$content .= "<tr><td>".lang("Name", "arrival");
	$content .= "</td><td>";
	$content .= display_username($rFindUser->ID); //$rFindUser->firstName." ".$rFindUser->lastName;
	$content .= "</td></tr>\n";

	// Address/postnumber
	$content .= "<tr><td>".lang("Adress", "arrival");
	$content .= "</td><td>";
	$content .= $rFindUser->street;
	$content .= " (".$rFindUser->postNumber.")";
	$content .= "</td></tr>\n";

	$content .= "<tr><td>".lang("E-mail", "arrival");
	$content .= "</td><td>";
	$content .= $rFindUser->EMail;
	$content .= "</td></tr>\n";

	$content .= "<tr><td>".lang("Birthday", "arrival");
	$content .= "</td><td>";
	$content .= $rFindUser->birthDay."/".$rFindUser->birthMonth." ".$rFindUser->birthYear;
	$content .= "</td></tr>\n";

	$content .= "</table>\n";
	$content .= "<table>\n";

	$content .= "<tr>";
	if($rFindTicket->paid == 'yes') {
		$content .= "<td class=tdLink style='background-color: green;' onClick='location.href=\"?module=arrival&action=marknotpaid&ticket=$ticket\"'>";
		$content .= lang("Paid", "arrival")." (".$rFindTicketType->price.")";
		$content .= "</td>";
	}
	else {
		$content .= "<td class=tdLink style='background-color: red;' onClick='location.href=\"?module=arrival&action=markpaid&ticket=$ticket\"'>";
		$content .= lang("Not paid", "arrival")." (".$rFindTicketType->price.")";
		$content .= "</td>";

	} // End paid-status

	$qFindTicketSeating = db_query("SELECT * FROM ".$sql_prefix."_seatReg_seatings WHERE ticketID = '".db_escape($ticket)."'");
	if(db_num($qFindTicketSeating) != 0 && ($acl_seating == 'Write' || $acl_seating == 'Admin')) {
		// Ticket is seated, and user has access to seating
		$rFindTicketSeating = db_fetch($qFindTicketSeating);
		$seatX = $rFindTicketSeating->seatX;
		$seatY = $rFindTicketSeating->seatY;
		$content .= "<td class=tdLink style='background-color: green;' onClick='location.href=\"?module=seating&ticketID=$ticket&seatX=$seatX&seatY=$seatY\"'>";
		$content .= lang("Seated", "arrival");
		$content .= "</td>";
	} elseif(db_num($qFindTicketSeating) != 0) {
		// Ticket is seated, and does not have access to seat
		$content .= "<td style='background-color: yellow;'>";
		$content .= lang("Seated", "arrival");
		$content .= "</td>";
	} else {
		$content .= "<td class=tdLink style='background-color: red;' onClick='location.href=\"?module=seating&ticketID=$ticket\"'>";
		$content .= lang("Not seated", "arrival");
		$content .= "</td>";
	} // End else
	if($rFindTicket->status != 'deleted' && $acl_ticket == ('Write' || 'Admin')) {
		$content .= "<td class=tdLink style='background-color: orange;'";
		$content .= " onClick='location.href=\"?module=arrival&action=deleteTicket&ticketID=$ticket\"'>";
		$content .= lang("Delete ticket", "arrival");
		$content .= "</td>";
	}

	elseif($rFindTicket->status == 'deleted') {
		$content .= "<td style='background-color: red;'>";
		$content .= lang("Deleted", "arrival");
		$content .= "</td>";
	}



	$content .= "</tr></table>\n\n";
}

elseif($action == "marknotpaid" && isset($_GET['ticket'])) {
	$ticket = $_GET['ticket'];

	if($acl_ticket != 'Write' AND $acl_ticket != 'Admin') die("No access to ticket");
	$qFindTicket = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE ticketID = '".db_escape($ticket)."'");
	$rFindTicket = db_fetch($qFindTicket);
	$qFindTicketType = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE ticketTypeID = '$rFindTicket->ticketType'");
	$rFindTicketType = db_fetch($qFindTicketType);

	if($rFindTicketType->type == 'prepaid') {
		db_query("UPDATE ".$sql_prefix."_tickets SET paid = 'no', status = 'notpaid' WHERE ticketID = '".db_escape($ticket)."'");
	} else {
		db_query("UPDATE ".$sql_prefix."_tickets SET paid = 'no' WHERE ticketID = '".db_escape($ticket)."'");
	}
	$newlog['newstatus'] = 'notpaid';
	$newlog['ticketID'] = $ticket;
	$oldlog['oldstatus'] = 'paid';
	log_add("arrival", "marknotpaid", serialize($newlog), serialize($oldlog));

	header("Location: ?module=arrival&action=ticketdetail&ticket=$ticket");

}

elseif($action == "markpaid" && isset($_GET['ticket'])) {
        $ticket = $_GET['ticket'];

        if($acl_ticket != 'Write' AND $acl_ticket != 'Admin') die("No access to ticket");
        $qFindTicket = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE ticketID = '".db_escape($ticket)."'");
        $rFindTicket = db_fetch($qFindTicket);
        $qFindTicketType = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE ticketTypeID = '$rFindTicket->ticketType'");
        $rFindTicketType = db_fetch($qFindTicketType);

        if($rFindTicketType->type == 'prepaid') {
                db_query("UPDATE ".$sql_prefix."_tickets SET paid = 'yes', paidTime = '".time()."', status = 'notused' WHERE ticketID = '".db_escape($ticket)."'");
        } else {
                db_query("UPDATE ".$sql_prefix."_tickets SET paid = 'yes', paidTime = '".time()."' WHERE ticketID = '".db_escape($ticket)."'");
        }

	$newlog['newstatus'] = 'paid';
	$newlog['ticketID'] = $ticket;
	$oldlog['oldstatus'] = 'notpaid';
	log_add("arrival", "markpaid", serialize($newlog), serialize($oldlog));
        header("Location: ?module=arrival&action=ticketdetail&ticket=$ticket");

}

elseif($action == "addTicket" && isset($_GET['user'])) {
	$user = $_GET['user'];

	$qGetTickets = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE eventID = '$sessioninfo->eventID'
		AND active = 1
		AND type LIKE 'onsite%'");

	$content .= "<form method=POST action=?module=arrival&action=doAddTicket&user=$user>";
	$content .= "<select name=tickettype>\n";
	while($rGetTickets = db_fetch($qGetTickets)) {
		$content .= "<option value='$rGetTickets->ticketTypeID'>$rGetTickets->name</option>\n";
	}
	$content .= "</select>\n";
	$content .= "<input type=submit value='".lang("Add ticket to user", "arrival")."'>";
	$content .= "</form>";

}

elseif($action == "doAddTicket" && isset($_GET['user'])) {
	$user = $_GET['user'];
	$ticketType = $_POST['tickettype'];

	$qCheckTicketType = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE eventID = '$sessioninfo->eventID'
		AND ticketTypeID = '".db_escape($ticketType)."'");
	if(db_num($qCheckTicketType) == 1) {
		db_query("INSERT INTO ".$sql_prefix."_tickets SET
			ticketType = '".db_escape($ticketType)."',
			eventID = '$sessioninfo->eventID',
			owner = '".db_escape($user)."',
			user = '".db_escape($user)."',
			createTime = '".time()."',
			creator = '$sessioninfo->userID'");
		$qFindTicket = db_query("SELECT * FROM ".$sql_prefix."_tickets
			WHERE eventID = '$sessioninfo->eventID'
			AND user = '".db_escape($user)."'
			ORDER BY createTime DESC LIMIT 0,1");
		$rFindTicket = db_fetch($qFindTicket);
		$newlog[] = $sessioninfo->userID;
		$newlog[] = $user;
		$newlog[] = $ticketType;

		log_add("arrival", "doAddOnsiteTicket", serialize($newlog));
		header("Location: ?module=arrival&action=ticketdetail&ticket=$rFindTicket->ticketID");
	} // End if db_num

} // End action = doAddTicket

elseif($action == "deleteTicket" && isset($_GET['ticketID']) && $acl_ticket == ('Write' || 'Admin')) {
	$ticket = $_GET['ticketID'];

	$qGetSeating = db_query("SELECT * FROM ".$sql_prefix."_seatReg_seatings WHERE ticketID = '".db_escape($ticket)."'");
	$rGetSeating = db_fetch($qGetSeating);
	$qGetTicket = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE ticketID = '".db_escape($ticket)."'");
	$rGetTicket = db_fetch($qGetTicket);

	$lognew['ticketID'] = $ticket;

	$logold['ticketStatus'] = $rGetTicket->status;
	$logold['seatX'] = $rGetSeating->seatX;
	$logold['seatY'] = $rGetSeating->seatY;

	db_query("DELETE FROM ".$sql_prefix."_seatReg_seatings WHERE ticketID = '".db_escape($ticket)."'");
	db_query("UPDATE ".$sql_prefix."_tickets SET status = 'deleted' WHERE ticketID = '".db_escape($ticket)."'");
	log_add("arrival", "deleteTicket", serialize($lognew), serialize($logold));

	header("Location: ?module=arrival&action=ticketdetail&ticket=$ticket");

}
