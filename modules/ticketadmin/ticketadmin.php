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
        $content .= '</th><th>'.lang("Sold tickets of type", "ticketadmin");
        $content .= '</th></tr>';
        while($rListTickets = db_fetch($qListTickets)) {
	$content .= '<tr><td>';
	$content .= $rListTickets->ticketTypeID;
	$content .= '</td><td>';
	$content .= "<a href=?module=ticketadmin&action=editticket&editticket=$rListTickets->ticketTypeID>";
	$content .= $rListTickets->name;
	$content .= '</a></td><td>';
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
    $content .= "<br><br>";
    // add form for adding tickets
    $content .= "<table>";
    $content .= "<tr><td>\n";
    // If we're editing a ticket, display values of ticket, else, addticket
    if($action == "editticket" && isset($_GET['editticket'])) {
        $qGetTicketInfo = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE ticketTypeID = '".db_escape($_GET['editticket'])."'");
        $rGetTicketInfo = db_fetch($qGetTicketInfo);
        $content .= "<form method=POST action=?module=ticketadmin&action=doeditticket&editticket=".$_GET['editticket'].">";
    } // end if action = editticket
    else $content .= "<form method=POST action=?module=ticketadmin&action=addtickettype>\n";
    $content .= "<input type=text name=name value='$rGetTicketInfo->name'>";
    $content .= "</td><td>";
    $content .= lang("Name of ticket", "ticketadmin");
    $content .= "</td></tr><tr><td>";
    $content .= "<input type=text name=price value='$rGetTicketInfo->price'>";
    $content .= "</td><td>";
    $content .= lang("Price of ticket", "ticketadmin");
    $content .= "</td></tr><tr><td>";
    $content .= "<select name=type>";
        $content .= "<option value=prepaid";
        if($rGetTicketInfo->type == 'prepaid') $content .= " selected";
        $content .= ">".lang("Prepaid ticket", "ticketadmin")."</option>";
    
        $content .= "<option value=preorder";
        if($rGetTicketInfo->type == 'preorder') $content .= " selected";
        $content .= ">".lang("Preordered ticket", "ticketadmin")."</option>";

        $content .= "<option value=onsite-computer";
        if($rGetTicketInfo->type == 'onsite-computer') $content .= " selected";
        $content .= ">".lang("Onsite ticket with computer", "ticketadmin")."</option>";

        $content .= "<option value=onsite-visitor";
        if($rGetTicketInfo->type == 'onsite-visitor') $content .= " selected";
        $content .= ">".lang("Onsite ticket without computer", "ticketadmin")."</option>";
    $content .= "</select>";
    $content .= "</td><td>";
    $content .= lang("Type of ticket", "ticketadmin");
    $content .= "</td></tr><tr><td>";
    $content .= "<input type=checkbox name=active value=1";
    if($rGetTicketInfo->active == 1) $content .= " checked";
    $content .= ">";
    $content .= "</td><td>";
    $content .= lang("Tickettype is active?", "ticketadmin");
    $content .= "</td></tr><tr><td>";
    if($action == "editticket") $content .= "<input type=submit value='".lang("Edit tickettype", "ticketadmin")."'>";
    else $content .= "<input type=submit value='".lang("Add tickettype", "ticketadmin")."'>";
    $content .= "</form></td></tr></table>";


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

    header("Location: ?module=ticketadmin");
} // End if action = doeditticket