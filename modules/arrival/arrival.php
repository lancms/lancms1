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
		(nick LIKE '".db_escape($search)."'
		OR firstName LIKE '".db_escape($search)."'
		OR lastName LIKE '".db_escape($search)."'
		OR CONCAT(firstName, ' ', lastName) LIKE '".db_escape($search)."'
		OR EMail LIKE '".db_escape($search)."')
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
			$content .= tickettype_getname($rFindTickets->ticketType)."</a></font>";
		} // End while rFindTickets
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
		$content .= "<td style='background-color: green;' onClick='location.href=\"?module=arrival&action=marknotpaid&ticket=$ticket\"'>";
		$content .= lang("Paid", "arrival")." (".$rFindTicketType->price.")";
		$content .= "</td>";
	}
	else {
		$content .= "<td style='background-color: red;' onClick='location.href=\"?module=arrival&action=markpaid&ticket=$ticket\"'>";
		$content .= lang("Not paid", "arrival")." (".$rFindTicketType->price.")";
		$content .= "</td>";

	} // End paid-status

	$qFindTicketSeating = db_query("SELECT * FROM ".$sql_prefix."_seatReg_seatings WHERE ticketID = '".db_escape($ticket)."'");
	if(db_num($qFindTicketSeating) != 0 && ($acl_seating == 'Write' || $acl_seating == 'Admin')) {
		// Ticket is seated, and user has access to seating
		$rFindTicketSeating = db_fetch($qFindTicketSeating);
		$seatX = $rFindTicketSeating->seatX;
		$seatY = $rFindTicketSeating->seatY;
		$content .= "<td style='background-color: green;' onClick='location.href=\"?module=seating&ticketID=$ticket&seatX=$seatX&seatY=$seatY\"'>";
		$content .= lang("Seated", "arrival");
		$content .= "</td>";
	} elseif(db_num($qFindTicketSeating) != 0) {
		// Ticket is seated, and does not have access to seat
		$content .= "<td style='background-color: yellow;'>";
		$content .= lang("Seated", "arrival");
		$content .= "</td>";
	} else {
		$content .= "<td style='background-color: red;' onClick='location.href=\"?module=seating&ticketID=$ticket\"'>";
		$content .= lang("Not seated", "arrival");
		$content .= "</td>";
	} // End else


	$content .= "</tr></table>\n\n";
}

elseif($action == "marknotpaid" && isset($_GET['ticket'])) {
	$ticket = $_GET['ticket'];
	
	if($acl_ticket != 'Write' AND $acl_ticket != 'Admin') die("No access to ticket");
	db_query("UPDATE ".$sql_prefix."_tickets SET paid = 'no' WHERE ticketID = '".db_escape($ticket)."'");

	header("Location: ?module=arrival&action=ticketdetail&ticket=$ticket");

}

elseif($action == "markpaid" && isset($_GET['ticket'])) {
        $ticket = $_GET['ticket'];

        if($acl_ticket != 'Write' AND $acl_ticket != 'Admin') die("No access to ticket");
        db_query("UPDATE ".$sql_prefix."_tickets SET paid = 'yes' WHERE ticketID = '".db_escape($ticket)."'");

        header("Location: ?module=arrival&action=ticketdetail&ticket=$ticket");

}

