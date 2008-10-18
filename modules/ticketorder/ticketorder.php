<?php
$eventID = $sessioninfo->eventID;
$userID = $sessioninfo->userID;

if(!config("enable_ticketorder", $eventID)) die("Ticketorder not enabled");
$action = $_GET['action'];


if(!isset($action)) {
    // No action set, display tickets

    $qDisplayTickets = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE eventID = '$eventID' AND owner = '$userID'");
    
    if(db_num($qDisplayTickets) != 0) {
        // The user has tickets to this event, display them
        $content .= "<table>";
        while($rDisplayTickets = db_fetch($qDisplayTickets)) {
	$content .= "<tr><td>";
	$content .= $rDisplayTickets->ticketID;
	$content .= "</td><td>";
	$content .= $rDisplayTickets->ticketType;
	$content .= "</td></tr>";
        } // End while
        $content .= "</table>";
    } // End if(db_num != 0);
    
    $qListBuyTickets = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE eventID = '$eventID'");
    if(db_num($qListBuyTickets) != 0) {
        $content .= "<table>\n";
        while($rListBuyTickets = db_fetch($qListBuyTickets)) {
	$content .= "<tr><td>";
	$content .= $rListBuyTickets->name;
	$content .= "</td><td>";
	$content .= "<form method=POST action=?module=ticketorder&action=buyticket&tickettype=$rListBuyTickets->ticketTypeID>";
	$content .= "<input name=numTickets value=1>";
	$content .= "<input type=submit value='".lang("Buy ticket")."'>";
	$content .= "</form>";
	$content .= "</td></tr>";
        } // End while
        $content .= "</table>";
    } // End if(db_num(qListBuyTickets)
} // End if !isset($action)

elseif($action == "buyticket" && !empty($_GET['tickettype']) && !empty($_POST['numTickets'])) {
    // Buy tickets
    $numTickets = $_POST['numTickets']; 
    $tickettype = $_GET['tickettype'];
    if($numTickets > $maxTicketsPrUser) $numTickets = $maxTicketsPrUser;
    while($numTickets) {
        $qUserNumTickets = db_query("SELECT COUNT(*) AS count FROM ".$sql_prefix."_tickets WHERE eventID = '$eventID' 
	AND (owner = '$sessioninfo->userID' OR creator = '$sessioninfo->userID')");
        $rUserNumTickets = db_fetch($qUserNumTickets);
        if($rUserNumTickets->count >= $maxTicketsPrUser); // Do noting if we've maxed maxTicketsPrUser
        else { // If we have not yet reached maxTicketsPrUser, add the ticket
	db_query("INSERT INTO ".$sql_prefix."_tickets SET 
	    owner = '$sessioninfo->userID',
	    creator = '$sessioninfo->userID',
	    eventID = '$eventID',
	    ticketType = '$tickettype',
	    createTime = ".time());
        } // End else (maxTicketsPrUser)
        $numTickets--; // Decrease numTickets
    } // End while(numtickets
    header("Location: ?module=ticketorder");
} // End action = buyticket