<?php
$eventID = $sessioninfo->eventID;

if(acl_access("ticketadmin", "", $eventID) != 'Admin') die("You do not have ticketadmin-rights");
$action = $_GET['action'];


if(!isset($action)) {

    $qListTickets = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE eventID = '$eventID'");

    if(db_num($qListTickets) != 0) {
        $content .= '<table>';
        $content .= '<tr><th>'.lang("Ticketnumber", "ticketadmin");
        $content .= '</th><th>'.lang("Ticketname", "ticketadmin");
        $content .= '</th><th>'.lang("Tickettype", "ticketadmin");
        $content .= '</th><th>'.lang("Price", "ticketadmin");
        $content .= '</th><th>'.lang("Sold tickets of type", "ticketadmin");
        $content .= '</th></tr>';
        while($rListTickets = db_fetch($qListTickets)) {
	$content .= '<tr><td>';
	$content .= $rListTickets->ticketTypeID;
	$content .= '</td><td>';
	$content .= $rListTickets->name;
	$content .= '</td><td>';
	$content .= lang($rListTickets->type, "ticketadmin");
	$content .= '</td><td>';
	$content .= $rListTickets->price;
	$content .= '</td><td>';
	$qNumTicketsOfType = db_query("SELECT COUNT(*) AS count FROM ".$sql_prefix."_tickets 
	    WHERE eventID = '$eventID' AND ticketType = '$rListTickets->ticketTypeID'");
	$rNumTicketsOfType = db_fetch($qNumTicketsOfType);
	$content .= $rNumTicketsOfType->count;
	$content .= '</td></tr>';
        } // End while
        $content .= '</table>';
    } // End if db_num() != 0

} // End if !isset($action)