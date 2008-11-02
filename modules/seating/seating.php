<?php
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