<?php
$eventID = $sessioninfo->eventID;

if(acl_access("ticketadmin", "", $eventID) != 'Admin') die("You do not have ticketadmin-rights");
$action = $_GET['action'];


if(!isset($action) || $action == "editticket") {

    $qListTickets = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE eventID = '$eventID'");

    if(db_num($qListTickets) != 0 && $action != 'editticket') {
        $content .= '<table>';
        $content .= '<tr><th>'.lang("Ticketnumber", "ticketadmin");
        $content .= '</th><th>'.lang("Ticketname", "ticketadmin");
        $content .= '</th><th>'.lang("Tickettype", "ticketadmin");
        $content .= '</th><th>'.lang("Price", "ticketadmin");
        $content .= '</th><th>'.lang("Sold tickets of type (total/seated/paid)", "ticketadmin");
        $content .= '</th></tr>';
        while($rListTickets = db_fetch($qListTickets)) {
	$content .= "<tr><td>\n";
	$content .= $rListTickets->ticketTypeID;
	$content .= "</td><td>\n";
	$content .= "<a href=\"?module=ticketadmin&amp;action=editticket&amp;editticket=$rListTickets->ticketTypeID\">\n";
	$content .= $rListTickets->name;
	$content .= "</a></td><td>\n";
	$content .= lang($rListTickets->type, "ticketadmin");
	$content .= "</td><td>\n";
	$content .= $rListTickets->price;
	$content .= "</td><td class=tdLink onClick='location.href=\"?module=ticketadmin&action=listTickets&tickettype=$rListTickets->ticketTypeID\"'>\n";
	$qNumTicketsOfType = db_query("SELECT COUNT(*) AS count FROM ".$sql_prefix."_tickets 
	    WHERE eventID = '$eventID' AND ticketType = '$rListTickets->ticketTypeID'");
	$rNumTicketsOfType = db_fetch($qNumTicketsOfType);
	$qNumTicketsUsed = db_query("SELECT COUNT(*) AS count FROM ".$sql_prefix."_tickets
		WHERE eventID = '$eventID' AND ticketType='$rListTickets->ticketTypeID' AND status = 'used'");
	$rNumTicketsUsed = db_fetch($qNumTicketsUsed);
	$qNumTicketsPaid = db_query("SELECT COUNT(*) AS count FROM ".$sql_prefix."_tickets
		WHERE eventID = '$eventID' AND ticketType='$rListTickets->ticketTypeID' AND paid = 'yes'");
	$rNumTicketsPaid = db_fetch($qNumTicketsPaid);
	$content .= $rNumTicketsOfType->count."/".$rNumTicketsUsed->count."/".$rNumTicketsPaid->count;
	$content .= "</td></tr>\n";
        } // End while
        $content .= "</table>\n";
    } // End if db_num() != 0
    $content .= "<br /><br />\n";
    // add form for adding tickets
    $content .= "<table>\n";
    $content .= "<tr><td>\n";
    // If we're editing a ticket, display values of ticket, else, addticket
    if($action == "editticket" && isset($_GET['editticket'])) {
        $qGetTicketInfo = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE ticketTypeID = '".db_escape($_GET['editticket'])."'");
        $rGetTicketInfo = db_fetch($qGetTicketInfo);
        $content .= "<form method=\"post\" action=\"?module=ticketadmin&amp;action=doeditticket&amp;editticket=".$_GET['editticket']."\">\n";
    } // end if action = editticket
    else $content .= "<form method=\"post\" action=\"?module=ticketadmin&amp;action=addtickettype\">\n";
    $content .= "<p class=\"nopad\"><input type=\"text\" name=\"name\" value='$rGetTicketInfo->name' /></p>\n";
    $content .= "</td><td>";
    $content .= lang("Name of ticket", "ticketadmin");
    $content .= "</td></tr><tr><td>\n";
    $content .= "<p class=\"nopad\"><input type=\"text\" name=\"price\" value='$rGetTicketInfo->price' /></p>\n";
    $content .= "</td><td>\n";
    $content .= lang("Price of ticket", "ticketadmin");
    $content .= "</td></tr><tr><td>\n";
    $content .= "<select name=\"type\">\n";
        $content .= "<option value=\"prepaid\"";
        if($rGetTicketInfo->type == 'prepaid') $content .= " selected";
        $content .= ">".lang("Prepaid ticket", "ticketadmin")."</option>\n";
    
        $content .= "<option value=\"preorder\"";
        if($rGetTicketInfo->type == 'preorder') $content .= " selected";
        $content .= ">".lang("Preordered ticket", "ticketadmin")."</option>\n";

        $content .= "<option value=\"onsite-computer\"";
        if($rGetTicketInfo->type == 'onsite-computer') $content .= " selected";
        $content .= ">".lang("Onsite ticket with computer", "ticketadmin")."</option>\n";

        $content .= "<option value=\"onsite-visitor\"";
        if($rGetTicketInfo->type == 'onsite-visitor') $content .= " selected";
        $content .= ">".lang("Onsite ticket without computer", "ticketadmin")."</option>\n";

        $content .= "<option value=\"reseller\"";
        if($rGetTicketInfo->type == 'reseller') $content .= " selected";
        $content .= ">".lang("Prepaid ticket reseller", "ticketadmin")."</option>\n";

    $content .= "</select>\n";
    $content .= "</td><td>\n";
    $content .= lang("Type of ticket", "ticketadmin");
    $content .= "</td></tr><tr><td>\n";
    $content .= "<p class=\"nopad\"><input type=\"checkbox\" name=\"active\" value=\"1\"";
    if($rGetTicketInfo->active == 1) $content .= " checked";
    $content .= " /></p>\n";
    $content .= "</td><td>\n";
    $content .= lang("Tickettype is active?", "ticketadmin");
    $content .= "</td></tr><tr><td>\n";
    if($action == "editticket") $content .= "<input type=\"submit\" value='".lang("Edit tickettype", "ticketadmin")."' />\n";
    else $content .= "<p class=\"nopad\"><input type=\"submit\" value='".lang("Add tickettype", "ticketadmin")."' /></p>\n";
    $content .= "</form></td></tr></table>\n";


} // End if !isset($action)

elseif($action == "addtickettype") {
    $name = db_escape($_POST['name']);
    $price = db_escape($_POST['price']);
    $type = db_escape($_POST['type']);
    if($_POST['active'] == 1) $active = 1;
    else $active = 0;

    db_query("INSERT INTO ".$sql_prefix."_ticketTypes SET 
        name = '$name', 
        price = '$price', 
        type = '$type', 
        active = '$active',
        eventID = '$eventID'");

    $log_new['name'] = $name;
    $log_new['price'] = $price;
    $log_new['type'] = $type;
    $log_new['active'] = $active;
    log_add("ticketadmin", "addTicketType", serialize($log_new));

    header("Location: ?module=ticketadmin");

} // end if action == addtickettype

elseif($action == "doeditticket" && !empty($_GET['editticket'])) {
    $name = db_escape($_POST['name']);
    $price = db_escape($_POST['price']);
    $type = db_escape($_POST['type']);
    if($_POST['active'] == 1) $active = 1;
    else $active = 0;

    db_query("UPDATE ".$sql_prefix."_ticketTypes SET
        name = '$name', 
        price = '$price', 
        type = '$type', 
        active = '$active'
        WHERE ticketTypeID = '".db_escape($_GET['editticket'])."'");
    $log_new['name'] = $name;
    $log_new['price'] = $price;
    $log_new['type'] = $type;
    $log_new['active'] = $active;

    log_add("ticketadmin", "doEditTicket", serialize($log_new));
    header("Location: ?module=ticketadmin");
} // End if action = doeditticket

elseif($action == "listTickets") {
	if(empty($_GET['tickettype'])) $where = '1';
	else $where = 'ticketType = '.$_GET['tickettype'];

	$qGetTickets = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE eventID = '$sessioninfo->eventID' AND $where");
	$content .= "<table>\n";
        $content .= "<tr><th>".lang("Ticketnumber", "ticketorder");
        $content .= "</th><th>".lang("Owner", "ticketorder");
        $content .= "</th><th>".lang("User", "ticketorder");
        $content .= "</th><th>".lang("Status", "ticketorder");
        $content .= "</th><th>".lang("Map placement", "ticketorder");
	$content .= "</th><th>".lang("Paid?", "ticketorder");
	$content .= "</th></tr>\n\n";
	
	while($rGetTickets = db_fetch($qGetTickets)) {
		$content .= "<tr><td>";
		$content .= $rGetTickets->ticketID;
		$content .= "</td><td>";
		$content .= display_username($rGetTickets->owner);
		$content .= "</td><td>";
		$content .= display_username($rGetTickets->user);
		$content .= "</td><td>";
		$content .= lang($rGetTickets->status, "ticketorder");
		$content .= "</td><td>";
		// FIXME: Display link to this seat on map
		$content .= "</td><td>";
		$content .= lang($rGetTickets->paid, "ticketadmin");
		$content .= "</td></tr>\n\n";
	} // End while rGetTickets
	$content .= "</table>";
}
