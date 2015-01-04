<?php
$design_head .= '<link href="templates/shared/seatreg_table.css" rel="stylesheet" type="text/css">';
$place_seatX = $_GET['seatX'];
$place_seatY = $_GET['seatY'];


// Get list of seats
$qGetSeats = db_query("SELECT * FROM ".$sql_prefix."_seatReg WHERE eventID = '$sessioninfo->eventID'
        ORDER BY seatY ASC, seatX ASC");

/* -------------------------------------------------------- */
/* START Fullscreen											*/
/* -------------------------------------------------------- */
$url = array('true', 'Fullskjerm-visning');
$fullscreenMode = (isset($_GET['fullscreen']) && $_GET['fullscreen'] == 'true' ? true : false);

// Output css if "fullscreen=true"
if ($fullscreenMode == true) {
	$url = array('false', 'Normal-visning');

	$content .= '<style type="text/css">';
	$content .= file_get_contents(__DIR__ . "/fullscreenview.css");
	$content .= '</style>';
}

// Display link.
$content .= '<div class="right-holder fullscreen-link"><a href="?module=seating&amp;fullscreen=' . $url[0] . '" title="' . _($url[1]) . '">' . _($url[1]) . '</a></div>';
/* -------------------------------------------------------- */
/* END Fullscreen											*/
/* -------------------------------------------------------- */

$content .= '<table id="seatRegTable">';
$content .= "<tr>"; // Begin the first row
while($rGetSeats = db_fetch($qGetSeats)) {
    $seatX = $rGetSeats->seatX;
    $seatY = $rGetSeats->seatY;
    $type = $rGetSeats->type;

    $qGetSeatedUser = db_query("SELECT users.nick AS nick FROM (".$sql_prefix."_users users 
		JOIN ".$sql_prefix."_tickets tickets ON tickets.user=users.ID) JOIN ".$sql_prefix."_seatReg_seatings seatings 
		ON tickets.ticketID=seatings.ticketID 
		WHERE seatings.eventID = '$sessioninfo->eventID'
		AND seatX = '$seatX' 
		AND seatY = '$seatY'");
	$GetSeatedUser = db_fetch($qGetSeatedUser);


    if($seatX == 1 && $seatY != 1) {
        // If we're beginning a new row, end the last row, and begin it
        $content .= "</tr><tr>";
    } // End if seatY == 1 and $seatX != 1

// SELECT osgl_users.nick FROM osgl_users,osgl_seatReg_seatings WHERE osgl_users.ID = osgl_seatReg_seatings.userID AND (osgl_seatReg_seatings.seatX = 3 AND osgl_seatReg_seatings.seatY = );

    if($seatX == $place_seatX && $seatY == $place_seatY) {
        $content .= "<td class=seatCurrentSelected>$GetSeatedUser->nick</td>";
    } // End if currently selected

    else {
        switch($type) {
	case "d":
	    // Type is normal seat
	    if(!empty($GetSeatedUser->nick)) {
		$content .= "<td class=seatNormalUser>";
		$content .= "<a href=\"?module=seating&amp;ticketID=$ticketID&amp;seatX=$seatX&amp;seatY=$seatY\">";
		$content .= $GetSeatedUser->nick;
	    } else {  
		$content .= "<td class=seatNormalUserFree>";
		$content .= "<a href=\"?module=seating&amp;ticketID=$ticketID&amp;seatX=$seatX&amp;seatY=$seatY\">";
		$content .= lang("Free", "seatmap");
	    } // End else

	    $content .= "</a>";
	    $content .= "</td>";
	    break;
	case "p":
	    // Type is password-protected
	    $content .= "<td class=seatPassword>";
	    $content .= "<a href=\"?module=seating&amp;ticketID=$ticketID&amp;seatX=$seatX&amp;seatY=$seatY\">";
	    if(!empty($GetSeatedUser->nick)) $content .= $GetSeatedUser->nick;
	    else {
		if(!empty($rGetSeats->name)) $content .= $rGetSeats->name;
		else $content .= lang("Free", "seatmap");
	    }
	    $content .= "</a>";
	    $content .= "</td>";
	    break;
	case "g":
	    // Type is group-protected
	    $content .= "<td class=seatGroup>";
	    $content .= "<a href=\"?module=seating&amp;ticketID=$ticketID&amp;seatX=$seatX&amp;seatY=$seatY\">";
	    if(!empty($GetSeatedUser->nick)) $content .= $GetSeatedUser->nick;
	    else {
	    	if(!empty($rGetSeats->name)) $content .= $rGetSeats->name;
		else $content .= lang("Free", "seatmap");
	    }
	    $content .= "</a>";
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
	case "n":
		// Type is not opened yet
		$content .= "<td class=seatNormalUser>"._("N/A")."</td>";
		break;
	case "r":
		// Type is right-protected
		$content .= "<td class=seatGroup>";
		$content .= "<a href=\"?module=seating&amp;ticketID=$ticketID&amp;seatX=$seatX&amp;seatY=$seatY\">";
		if(!empty($GetSeatedUser->nick)) $content .= $GetSeatedUser->nick;
		else $content .= _("Free");
		$content .= "</a>";
		$content .= "</td>";
		break;
		
	default:
	    // Unknown type. Just create something
	    $content .= "<td class=seatUnknownCell>$type</td>\n";
        } // End switch
    } // End else

} // End while rGetSeats

$content .= "</table>";

if(!empty($place_seatY) && !empty($seatX) && config("seating_enabled", $sessioninfo->eventID) == 1 && !empty($ticketID)) {
    $qSeatInfo = db_query("SELECT * FROM ".$sql_prefix."_seatReg_seatings WHERE
        eventID = '$sessioninfo->eventID' AND
        seatX = '$place_seatX' AND
        seatY = '$place_seatY'");
    if(db_num($qSeatInfo) == 0) {
        // Noone has taken this seat; take it?
        $qGetSeatInfo = db_query("SELECT * FROM ".$sql_prefix."_seatReg
	WHERE eventID = '$sessioninfo->eventID' AND
	seatX = '$place_seatX' AND
	seatY = '$place_seatY'");
        $rGetSeatInfo = db_fetch($qGetSeatInfo);
        switch($rGetSeatInfo->type) {
	case "d":
	    $content .= lang("This seat is available", "seatmap_table");
	    $content .= "<a href=\"?module=seating&amp;action=takeseat&amp;ticketID=$ticketID&amp;seatX=$place_seatX&amp;seatY=$place_seatY\">";
	    $content .= lang("Take seat", "seatmap_table");
	    $content .= "</a>";
	    break;
	case "p":
	    $content .= lang("This seat is password-protected. If you know the password, you can take it", "seatmap_table");
	    $content .= "<form method=POST action=?module=seating&amp;action=takeseat&amp&ticketID=$ticketID&amp;seatX=$place_seatX&amp;seatY=$place_seatY>";
	    $content .= "<input type=text name=password><input type=submit value='".lang("Take seat", "seatmap_table")."'>\n";
	    $content .= "</form>";
	    break;
	case "g":
	    if(seating_rights($place_seatX, $place_seatY, $ticketID, $sessioninfo->eventID, $password)) {
	        $content .= lang("This seat is protected by group. You are a member of a group with access.", "seatmap_table");
	        $content .= "<br /><a href=\"?module=seating&amp;action=takeseat&amp;ticketID=$ticketID&amp;seatX=$place_seatX&amp;seatY=$place_seatY\">";
	        $content .= lang("Take seat", "seatmap_table");
	        $content .= "</a>";
	    } // End if(seating_rights)
	    else {
	        $content .= lang("This seat is protected by group. You are not member of a group with access. Too bad!", "seatmap_table");
	    } // End else
		break;
	case "r":
		if(seating_rights($place_seatX, $place_seatY, $ticketID, $sessioninfo->eventID, $password)) {
			$content .= _("This seat is protected by a special right. You are a member of a group with access");
			$content .= "<br /><a href=\"?module=seating&amp;action=takeseat&amp;ticketID=$ticketID&amp;seatX=$place_seatX&amp;seatY=$place_seatY\">";
	        $content .= _("Take seat");
	        $content .= "</a>";
		} // End if(seating_rights)
		else $content .= _("This seat is protected by a special right. You are not member of a group with access. Too bad!");
		break;
		 } // End switch
    } // End if db_num() == 0

}
