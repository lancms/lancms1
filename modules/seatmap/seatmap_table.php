<?php
$design_head .= '<link href="templates/shared/seatreg_table.css" rel="stylesheet" type="text/css">';
$place_seatX = $_GET['seatX'];
$place_seatY = $_GET['seatY'];


// Get list of seats
$qGetSeats = db_query("SELECT * FROM ".$sql_prefix."_seatReg WHERE eventID = '$sessioninfo->eventID'
        ORDER BY seatY ASC, seatX ASC");

$content .= '<table id="seatRegTable">';
$content .= "<tr>"; // Begin the first row
while($rGetSeats = db_fetch($qGetSeats)) {
    $seatX = $rGetSeats->seatX;
    $seatY = $rGetSeats->seatY;
    $type = $rGetSeats->type;

    if($seatX == 1 && $seatY != 1) {
        // If we're beginning a new row, end the last row, and begin it
        $content .= "</tr><tr>";
    } // End if seatY == 1 and $seatX != 1
    $qGetSeatedUser = db_query("SELECT * FROM ".$sql_prefix."_seatReg_seatings WHERE
        eventID = '$sessioninfo->eventID' AND
        seatX = '$seatX' AND
        seatY = '$seatY'");
    $GetSeatedUser = db_fetch($qGetSeatedUser);
    
    if($seatX == $place_seatX && $seatY == $place_seatY) {
        $content .= "<td class=seatCurrentSelected></td>";
    } // End if currently selected

    else {
        switch($type) {
	case "d":
	    // Type is normal seat
	    $content .= "<td class=seatNormalUser>";
	    $content .= "<a href=?module=seating&amp;ticketID=$ticketID&amp;seatX=$seatX&amp;seatY=$seatY>";
	    $content .= "User";
	    $content .= "</a>";
	    $content .= "</td>";
	    break;
	case "p":
	    // Type is password-protected
	    $content .= "<td class=seatPassword>";
	    $content .= "Password";
	    $content .= "</td>";
	    break;
	case "g":
	    // Type is group-protected
	    $content .= "<td class=seatGroup>";
	    $content .= "Group";
	    $content .= "</td>";
	    break;

	// No real type, just defining the fields
	case "w":
	    // Type is wall
	    $content .= "<td class=seatWall></td>";
	    break;
	case "b":
	    // Type is blank/open space
	    $content .= "<td class=seatBlank></td>";
	    break;
	case "o":
	    // Type is opening/door
	    $content .= "<td class=seatDoor></td>";
	    break;
	default:
	    // Unknown type. Just create something
	    $content .= "<td class=seatUnknownCell>$type</td>\n";
        } // End switch
    } // End else

} // End while rGetSeats

$content .= "</table>";

if(!empty($place_seatY) && !empty($seatX)) {
    $qSeatInfo = db_query("SELECT * FROM ".$sql_prefix."_seatReg_seatings WHERE 
        eventID = '$sessioninfo->eventID' AND
        seatX = '$place_seatX' AND
        seatY = '$place_seatY'");
    if(db_num($qSeatInfo) == 0) {
        // Noone has taken this seat; take it?
        $content .= lang("This seat is available", "seatmap_table");
        $content .= "<a href=?module=seating&amp;action=takeseat&amp;ticketID=$ticketID&amp;seatX=$place_seatX&amp;seatY=$place_seatY>";
        $content .= lang("Take seat", "seatmap_table");
        $content .= "</a>";
    } // End if db_num() == 0

}