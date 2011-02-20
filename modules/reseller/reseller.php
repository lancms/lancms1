<?php

$action = $_GET['action'];

$acl = acl_access("reseller", "", $sessioninfo->eventID);

if($acl == 'No') die("No access to reseller");

if(!isset($action)) {
	$qFindTickets = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE type='reseller' AND active = 1 AND eventID = '$sessioninfo->eventID'");
	$content .= "<table>\n";
	while($rFindTickets = db_fetch($qFindTickets)) {
		if(acl_access("reseller", $rFindTickets->ticketTypeID, $sessioninfo->eventID) != 'No') {
			$content .= "<tr><td>";
			$content .= $rFindTickets->name;
			$content .= "</td><td>";
			$content .= "<form method=POST action=?module=reseller&action=addTicket&type=$rFindTickets->ticketTypeID>";
			$content .= "<input size=2 type=text name=amount value=1>\n";
			$content .= "<input type=submit value='Add tickets'>";
			$content .= "</form>\n";
			$content .= "</td></tr>\n\n";
		} // End if acl_access(reseller, ticketTypeID)
	} // End while
	$content .= "</table>\n\n";

	$qFindSoldTickets = db_query("SELECT * FROM ".$sql_prefix."_ticketReseller WHERE eventID = '$sessioninfo->eventID'");
	$content .= "<table>";
	while($rFindSoldTickets = db_fetch($qFindSoldTickets)) {
		if($rFindSoldTickets->used == 'no') $bgcolor = 'red';
		else $bgcolor = 'green';
		$content .= "<tr bgcolor='$bgcolor'><td>";
		$content .= $rFindSoldTickets->resellerTicketID;
		$content .= "</td><td>";
		$content .= $rFindSoldTickets->resellerID;
		$content .= "</td><td>";
		$content .= date("Y/m/d H:i", $rFindSoldTickets->saleTime);
		$content .= "</td></tr>";
	} // End while
	$content .= "</table>";

}

elseif($action == "addTicket" && !empty($_GET['type'])) {
	$amount = $_POST['amount'];
	$type = $_GET['type'];
	if(acl_access("reseller", $type, $sessioninfo->eventID) == 'No') die("No access to this ticketType");
	while($amount) {

		$md5 = md5(rand(0,10000));
		$string = strtoupper(substr($md5, 0, 10));

		$qCheckAlreadyUsed = db_query("SELECT * FROM ".$sql_prefix."_ticketReseller WHERE resellerTicketID = '$string'");
		if(db_num($qCheckAlreadyUsed) == 0) {
			// Key is not already used, use it
			db_query("INSERT INTO ".$sql_prefix."_ticketReseller 
				SET resellerTicketID = '$string',
				ticketType = '".db_escape($type)."',
				eventID = '$sessioninfo->eventID',
				resellerID = '$sessioninfo->userID',
				saleTime = '".time()."'
			");

			$content .=  "<h1>".$string."</h1><br />";
			$amount--;
		} // End if
	} // End while

	$log_new['type'] = $type;
	$log_new['amount'] = $amount;

	log_add("reseller", "addTicket", serialize($log_new));

} // End addTicket
