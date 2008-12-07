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

    $qGetSeatedUser = db_query("SELECT ".$sql_prefix."_users.nick AS nick FROM ".$sql_prefix."_users,".$sql_prefix."_seatReg_seatings WHERE
	    	".$sql_prefix."_users.ID = ".$sql_prefix."_seatReg_seatings.userID AND
	        eventID = '$sessioninfo->eventID' AND
	        seatX = '$seatX' AND
	        seatY = '$seatY'");
	$GetSeatedUser = db_fetch($qGetSeatedUser);


    if($seatX == 1 && $seatY != 1) {
        // If we're beginning a new row, end the last row, and begin it
        $content .= "</tr><tr>";
    } // End if seatY == 1 and $seatX != 1
<<<<<<< .mine









=======
// SELECT osgl_users.nick FROM osgl_users,osgl_seatReg_seatings WHERE osgl_users.ID = osgl_seatReg_seatings.userID AND (osgl_seatReg_seatings.seatX = 3 AND osgl_seatReg_seatings.seatY = ); 

    $qGetSeatedUser = db_query("SELECT ".$sql_prefix."_users.nick FROM ".$sql_prefix."_users, ".$sql_prefix."_seatReg_seatings WHERE 
        ".$sql_prefix."_users.ID = ".$sql_prefix."_seatReg_seatings.userID AND 
        (eventID = '$sessioninfo->eventID' AND
        seatX = '$seatX' AND
        seatY = '$seatY')");
    $GetSeatedUser = db_fetch($qGetSeatedUser);
    
>>>>>>> .theirs
    if($seatX == $place_seatX && $seatY == $place_seatY) {
        $content .= "<td class=seatCurrentSelected>$GetSeatedUser->nick</td>";
    } // End if currently selected

    else {
        switch($type) {
	case "d":
	    // Type is normal seat
	    $content .= "<td class=seatNormalUser>";
	    $content .= "<a href=?module=seating&amp;ticketID=$ticketID&amp;seatX=$seatX&amp;seatY=$seatY>";
<<<<<<< .mine
	    if(db_num($qGetSeatedUser) == 0) $content .= "User";
	    else $content .= $GetSeatedUser->nick;
=======
	    if(!empty($GetSeatedUser->nick)) $content .= $GetSeatedUser->nick;
	    else $content .= "User";
>>>>>>> .theirs
	    $content .= "</a>";
	    $content .= "</td>";
	    break;
	case "p":
	    // Type is password-protected
	    $content .= "<td class=seatPassword>";
	    $content .= "<a href=?module=seating&amp;ticketID=$ticketID&amp;seatX=$seatX&amp;seatY=$seatY>";
	    if(!empty($GetSeatedUser->nick)) $content .= $GetSeatedUser->nick;
	    else $content .= "Password";
	    $content .= "</a>";
	    $content .= "</td>";
	    break;
	case "g":
	    // Type is group-protected
	    $content .= "<td class=seatGroup>";
	    $content .= "<a href=?module=seating&amp;ticketID=$ticketID&amp;seatX=$seatX&amp;seatY=$seatY>";
	    if(!empty($GetSeatedUser->nick)) $content .= $GetSeatedUser->nick;
	    else $content .= "Group";
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
	default:
	    // Unknown type. Just create something
	    $content .= "<td class=seatUnknownCell>$type</td>\n";
        } // End switch
    } // End else

} // End while rGetSeats

$content .= "</table>";

if(!empty($place_seatY) && !empty($seatX) && config("seating_enabled", $sessioninfo->eventID) == 1) {
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
	    $content .= "<a href=?module=seating&amp;action=takeseat&amp;ticketID=$ticketID&amp;seatX=$place_seatX&amp;seatY=$place_seatY>";
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
	        $content .= lang("This seat is protected by group. You are a member of a group with access. Halleluja!", "seatmap_table");
	        $content .= "<a href=?module=seating&amp;action=takeseat&amp;ticketID=$ticketID&amp;seatX=$place_seatX&amp;seatY=$place_seatY>";
	        $content .= lang("Take seat", "seatmap_table");
	        $content .= "</a>";
	    } // End if(seating_rights)
	    else {
	        $content .= lang("This seat is protected by group. You are not member of a group with access. Too bad!", "seatmap_table");
	    } // End else
        } // End switch
    } // End if db_num() == 0

}
