<?php

$action = $_GET['action'];
$subaction = isset($_GET['subaction']) ? $_GET['subaction'] : null;

$acl_ticket = acl_access("ticketadmin", "", $sessioninfo->eventID);
$acl_seating = acl_access("seating", "", $sessioninfo->eventID);

if($acl_ticket == 'No') die("No access to ticketadmin");

if(!isset($action) || $action == "searchUser")
{
	$search = $_POST['searchUser'];
	$scope = $_POST['scope'];

	if ($scope == 'all')
	{
		$all_checked = 'CHECKED';
	}
	else  // scope == tickets
	{
		$tickets_checked = 'CHECKED';
	}

	$content .= "<h3>"._ ('Arrival')."</h3>\n";

	$content .= "<form method='POST' action='?module=arrival&action=searchUser'>\n";
	$content .= "<table>\n<tr>\n<td>\n";
	$content .= sprintf ("<input type='text' name='searchUser' value='%s' />\n", $search);
	$content .= "</td>\n<td>\n";
	$content .= sprintf ("<input type='submit' value='%s' />\n", _('Search'));
	$content .= "</td>\n</tr>\n<td colspan='2'>\n";
	$content .= sprintf ("<input type='radio' %s name='scope' value='all' /> %s\n", $all_checked, _("Search all users"));
	$content .= "</td>\n</tr>\n<td colspan='2'>\n";
	$content .= sprintf ("<input type='radio' %s name='scope' value='tickets' /> %s\n", $tickets_checked, _("Search users with tickets"));
	$content .= "</td>\n</tr>\n</table>\n";
	$content .= "</form>\n";

	
	// FIXME: this could be done globally and save some typing :-) ($usertable = $sql_prefix."users";)
	$usertable = $sql_prefix."_users";
	$ticketstable = $sql_prefix."_tickets";
	$tickettypestable = $sql_prefix."_ticketTypes";

	$str = db_escape ($search);

	if ($search == "" or empty ($search))
	{
		if ($scope == 'all')
		{
			$usersQ = sprintf ("SELECT nick, firstName, lastName, ID FROM %s WHERE ID > 1 ORDER BY ID", $usertable);
		}
		else // scope == tickets
		{
			$usersQ = sprintf ("SELECT DISTINCT u.nick as nick, u.firstName as firstName, u.lastName as lastName, u.ID as ID FROM %s as u, %s as t WHERE t.eventID=%s AND t.user=u.ID ORDER BY u.ID", $usertable, $ticketstable, $sessioninfo->eventID);
		}
	}
	else
	{
		if ($scope == 'all')
		{
			$usersQ = sprintf ("SELECT nick, firstName, lastName, ID FROM %s WHERE ID > 1 AND 
				(nick LIKE '%%%s%%' OR
				firstName LIKE '%%%s%%' OR
				lastName LIKE '%%%s%%' OR
				CONCAT(firstName, ' ', lastName) LIKE '%%%s%%' OR
				EMail LIKE '%%%s%%') ORDER BY ID
				", $usertable, $str, $str, $str, $str, $str);
		}
		else // scope == tickets
		{
			$usersQ = sprintf ("SELECT DISTINCT u.nick as nick, u.firstName as firstName, u.lastName as lastName, u.ID as ID FROM %s as u, %s as t WHERE t.eventID=%s AND t.user=u.ID AND 
			(u.nick LIKE '%%%s%%' OR
			u.firstName LIKE '%%%s%%' OR
			u.lastName LIKE '%%%s%%' OR
			CONCAT(u.firstName, ' ', u.lastName) LIKE '%%%s%%' OR
			EMail LIKE '%%%s%%'
			) ORDER BY u.ID
			", $usertable, $ticketstable, $sessioninfo->eventID, $str, $str, $str, $str, $str);
		}
	}

	$usersR = db_query ($usersQ);
	$usersC = db_num ($usersR);

	if ($usersC)
	{
		$content .= "<table style='border: solid 1px black; border-collapse: collapse;'>\n";
		$content .= sprintf ("<tr><th>%s</th><th>%s</th><th></th></tr>\n", _('Nick'), _('Name'));
	}

	while ($user = db_fetch ($usersR))
	{
		$ticketactions = "<table>\n";
		$ticketactions .= sprintf ("<tr><td><a href='?module=arrival&action=addTicket&user=%s'>%s</a></td></tr>\n", $user->ID, _('Add new ticket'));
		
		$ticketsQ = sprintf ("SELECT type.name AS name, tickets.ticketID as ticketID, tickets.paid AS paid, tickets.status AS status FROM %s AS tickets, %s AS type WHERE tickets.user=%s AND tickets.eventID=%s AND tickets.ticketType=type.ticketTypeID", $ticketstable, $tickettypestable, $user->ID, $sessioninfo->eventID);
		$ticketsR = db_query ($ticketsQ);
	#	$ticketsC = db_num ($ticketsQ);
		while ($ticket = db_fetch ($ticketsR))
		{
			$ticketCssClass = "";
			if ($ticket->status == 'deleted')
			{
				$ticketCssClass = 'ticket-error';
			}
			elseif ($ticket->paid == 'yes')
			{
				$ticketCssClass = 'ticket-ok';
			}
			elseif ($ticket->paid == 'no')
			{
				$ticketCssClass = 'ticket-uhm';
			}

			$ticketactions .= sprintf ("<tr><td class='%s'><a href='?module=arrival&action=ticketdetail&ticket=%s'>%s</a></td></tr>\n",
				$ticketCssClass, $ticket->ticketID, $ticket->name);
		}


		$ticketactions .= "</table>\n";

		$content .= "<tr>\n";
		$content .= sprintf ('<td style="border: solid 1px black;">%s</td>', $user->nick);
		$content .= sprintf ('<td style="border: solid 1px black;">%s %s</td>', $user->firstName, $user->lastName);
		$content .= sprintf ('<td style="border: solid 1px black;">%s</td>', $ticketactions);
		$content .= "</tr>\n";
	}
	
	if ($usersC)
	{
		$content .= "</table>\n";
	}
	else
	{
		$content .= "<br /><p>";
		$content .= _('Found no matching users');
		$content .= "</p><br />\n";
	}

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

	// display owner
	$content .= "<tr><th>"._("Owner")."</th></tr>\n";
	$content .= "<tr><td>"._("Name");
	$content .= "</td><td>";
	$content .= display_username($rFindTicket->owner); //$rFindUser->firstName." ".$rFindUser->lastName;
	$content .= "</td></tr>\n";

	// display name
	$content .= "<tr><td>&nbsp;</td></tr>\n";
	$content .= "<tr><th>"._("User")."</th></tr>\n";
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
		$content .= "<td class='arrival-actions-common arrival-actions-white tdLink' style='background-color: green;' onClick='location.href=\"?module=arrival&action=marknotpaid&ticket=$ticket\"'>";
		$content .= lang("Paid", "arrival")." (".$rFindTicketType->price.")";
		$content .= "</td>";
	}
	else {
		$content .= "<td class='arrival-actions-common arrival-actions-white tdLink' style='background-color: red;' onClick='location.href=\"?module=arrival&action=markpaid&ticket=$ticket\"'>";
		$content .= lang("Not paid", "arrival")." (".$rFindTicketType->price.")";
		$content .= "</td>";

	} // End paid-status

	$qFindTicketSeating = db_query("SELECT * FROM ".$sql_prefix."_seatReg_seatings WHERE ticketID = '".db_escape($ticket)."'");
	if(db_num($qFindTicketSeating) != 0 && ($acl_seating == 'Write' || $acl_seating == 'Admin')) {
		// Ticket is seated, and user has access to seating
		$rFindTicketSeating = db_fetch($qFindTicketSeating);
		$seatX = $rFindTicketSeating->seatX;
		$seatY = $rFindTicketSeating->seatY;
		$content .= "<td class='arrival-actions-common arrival-actions-white tdLink' style='background-color: green;' onClick='location.href=\"?module=seating&ticketID=$ticket&seatX=$seatX&seatY=$seatY\"'>";
		$content .= lang("Seated", "arrival");
		$content .= "</td>";
	} elseif(db_num($qFindTicketSeating) != 0) {
		// Ticket is seated, and does not have access to seat
		$content .= "<td class='arrival-actions-common' style='background-color: yellow;'>";
		$content .= lang("Seated", "arrival");
		$content .= "</td>";
	} else {
		$content .= "<td class='arrival-actions-common tdLink' style='background-color: red;' onClick='location.href=\"?module=seating&ticketID=$ticket\"'>";
		$content .= lang("Not seated", "arrival");
		$content .= "</td>";
	} // End else
	if($rFindTicket->status != 'deleted' && $acl_ticket == ('Write' || 'Admin')) {
		$content .= "<td class='tdLink arrival-actions-common' style='background-color: orange;'";
		$content .= " onClick='location.href=\"?module=arrival&action=deleteTicket&ticketID=$ticket\"'>";
		$content .= lang("Delete ticket", "arrival");
		$content .= "</td>";
	}

	elseif($rFindTicket->status == 'deleted') {
		$content .= "<td class='arrival-actions-common' style='background-color: red;'>";
		$content .= lang("Deleted", "arrival");
		$content .= "</td>";
	}



	$content .= "</tr></table>\n\n";
	$content .= "<br />\n";
	$userACL = acl_access("userAdmin", "", 1);
	if($userACL == 'Write' || $userACL == 'Admin') $content .= sprintf ("<form method='POST' action='?module=edituserinfo&action=editUserinfo&user=%s'><input type='submit' value='%s' /></form>\n", $rFindTicket->user, _('Edit userinfo'));

	//XXX: Should extra access be needed for changing owner?
	$content .= sprintf ("<form method='POST' action='?module=arrival&action=changeowner&ticket=%d'><input type='submit' value='%s' /></form>\n", $rFindTicket->ticketID, _('Change owner'));

	$content .= sprintf ("<form method='POST' action='?module=arrival&action=changeuser&ticket=%d'><input type='submit' value='%s' /></form>\n", $rFindTicket->ticketID, _('Change user'));
}

elseif($action == "changeowner" && isset($_GET['ticket']))
{
	$ticket = $_GET['ticket'];
	$toUser = isset($_GET['toUser']) ? $_GET['toUser'] : null;
	$query = isset($_POST['query']) ? $_POST['query'] : null;

	if($toUser)
	{
        	db_query("UPDATE ".$sql_prefix."_tickets SET owner = '".db_escape($toUser)."' WHERE ticketID = '".db_escape($ticket)."'");
		header("Location: ?module=arrival&action=ticketdetail&ticket={$ticket}");
	}

	$content .= "<h2>" . _("Search for new owner") . "</h2>\n";

	$content .= "<form method=\"POST\" action=\"?module=arrival&action=changeowner&ticket={$ticket}\">\n";
	$content .= "<input type=\"text\" name=\"query\" value=\"".($query ? $query : "")."\">\n";
	$content .= "<input type=\"submit\" value=\"" . _("Search") ."\">\n";
	$content .= "</form>\n";

	if($query)
	{
		$search = db_escape($query);
		$qFindUser = db_query("SELECT ID FROM ".$sql_prefix."_users
			WHERE nick LIKE '%$search%'
			OR firstName LIKE '%$search%'
			OR lastName LIKE '%$search%'
		");
		if(db_num($qFindUser) > 30 ) {
			$content .= lang("Found to many users matching, please specify", "arrival");
		} // Found to many matches on search
		else {
			$content .= "<ul>";
			while($rFindUser = db_fetch($qFindUser)) {
				$content .= "<li><a href=?module=arrival&action=changeowner&ticket=$ticket&toUser=$rFindUser->ID>";
				$content .= display_username($rFindUser->ID);
				$content .= "</a></li>";
			} // End while rFindUser
			$content .= '</ul>';
		} // End else (db_num())
	}
}

elseif($action == "changeuser" && isset($_GET['ticket']))
{
	$ticket = $_GET['ticket'];
	$toUser = isset($_GET['toUser']) ? $_GET['toUser'] : null;
	$query = isset($_POST['query']) ? $_POST['query'] : null;

	if($toUser)
	{
        	db_query("UPDATE ".$sql_prefix."_tickets SET user = '".db_escape($toUser)."' WHERE ticketID = '".db_escape($ticket)."'");
		header("Location: ?module=arrival&action=ticketdetail&ticket={$ticket}");
	}

	$content .= "<h2>" . _("Search for new user") . "</h2>\n";

	$content .= "<form method=\"POST\" action=\"?module=arrival&action=changeuser&ticket={$ticket}\">\n";
	$content .= "<input type=\"text\" name=\"query\" value=\"".($query ? $query : "")."\">\n";
	$content .= "<input type=\"submit\" value=\"" . _("Search") ."\">\n";
	$content .= "</form>\n";

	if($query)
	{
		$search = db_escape($query);
		$qFindUser = db_query("SELECT ID FROM ".$sql_prefix."_users
			WHERE nick LIKE '%$search%'
			OR firstName LIKE '%$search%'
			OR lastName LIKE '%$search%'
		");
		if(db_num($qFindUser) > 30 ) {
			$content .= lang("Found to many users matching, please specify", "arrival");
		} // Found to many matches on search
		else {
			$content .= "<ul>";
			while($rFindUser = db_fetch($qFindUser)) {
				$content .= "<li><a href=?module=arrival&action=changeuser&ticket=$ticket&toUser=$rFindUser->ID>";
				$content .= display_username($rFindUser->ID);
				$content .= "</a></li>";
			} // End while rFindUser
			$content .= '</ul>';
		} // End else (db_num())
	}
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
