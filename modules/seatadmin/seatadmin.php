<?php
$acl_access = acl_access("seatreg_admin", "", $sessioninfo->eventID);

$action = $_GET['action'];
$type = $_POST['type'];

if($acl_access != 'Admin') {
	die("Sorry, you do not have access to this!");
}

if($action == "updateSeat") {
	if($type == "d" OR $type == "w" OR $type == "o" OR $type == "b") {
		$action = "doUpdateSeat";
	} // End if type = "normal"

	elseif($type == "a") { // type is area, we need to do something about this
		/* FIXME */
	} // End if type = a (area)

	elseif($type == "p" && !isset($_POST['seatpassword'])) { // type is password-protected seat
//		$seatcontent .= "<form method=POST action=?module=seatadmin&amp;action=doUpdateSeat>\n";
		$seatcontent .= "<input type=text name=seatpassword>\n";
		$seatcontent .= "<input type=submit value='".lang("Set password", "seatadmin")."'>";
//		$seatcontent .= "</form>";

	} // End if type = p (password)

	elseif($type == "p" && isset($_POST['seatpassword'])) {
		$action = "doUpdateSeat";
	}

	elseif($type == "g" && !isset($_POST['group'])) { // type is group-protected
		$seatcontent .= "<select name=group>\n";
		$qGroups = db_query("SELECT ID,groupname,groupType FROM ".$sql_prefix."_groups
			WHERE
			(eventID = 1 AND groupType = 'clan')
			OR
			(eventID = $sessioninfo->eventID AND groupType = 'access')
			ORDER BY groupname ASC
			");
		while($rGroups = db_fetch($qGroups)) {
			$seatcontent .= "<option value='$rGroups->ID'>";
			$seatcontent .= $rGroups->groupname." (".$rGroups->groupType.")";
			$seatcontent .= "</option>\n";


		} // End while rGroups
		$seatcontent .= "</select>\n\n\n";
		$seatcontent .= "<input type=submit value='".lang("Set groupaccess", "seatadmin")."'>";
	} // End group-protected

	elseif($type == "g" && isset($_POST['group'])) { // type is group, and group is set
		$action = "doUpdateSeat";
	} // End isset(group)
} // End if action == "updateSeat"

if(!isset($action) || $action == "updateSeat") {
	// No action set... Display the map

	// First, get amount of rows
	$qGetSeatY = db_query("SELECT DISTINCT seatY FROM ".$sql_prefix."_seatReg
		WHERE eventID = '".$sessioninfo->eventID."'
		ORDER BY seatY ASC");

	$content .= "<table border=1>\n\n";
	$content .= "<form method=POST action=?module=seatadmin&amp;action=updateSeat>\n";



	while($rGetSeatY = db_fetch($qGetSeatY)) {
		$seatY = $rGetSeatY->seatY;
		// Start a new row
		$content .= "<tr>\n";

		// Get the contents of the row; the columns
		$qGetSeatX = db_query("SELECT * FROM ".$sql_prefix."_seatReg
			WHERE eventID = '".$sessioninfo->eventID."'
			AND seatY = '".$rGetSeatY->seatY."'
			ORDER BY seatX ASC");

		while($rGetSeatX = db_fetch($qGetSeatX)) {
			$seatX = $rGetSeatX->seatX;
			$content .= "<td style='height: 25px; width: 25px' bgcolor=".$rGetSeatX->color;
			$content .= ">";
			$content .= "<input type=checkbox value=1 name=x".$seatX."y".$seatY;
			if($_POST['x'.$seatX.'y'.$seatY] == 1) $content .= " CHECKED";
			$content .= ">";
			$content .= "</td>\n\n";
		} // End while (rGetSeatX)

		$content .= "</tr>"; // End the row

	} // End while(rGetSeatY)
	$content .= "</table>";

	$content .= "<table>";
	$content .= "<tr><td>";
	// Here we display changes we can make to what we have just selected
	$content .= "<select name=type>";
	foreach($seattype AS $key => $value) {
		$content .= "<option value='$key'";
		if($key == $type) $content .= " selected='selected'";
		$content .= ">$value</option>\n";
	} // End foreach
	$content .= "</select>";
	$content .= "<br><input type=submit value='".lang("Change seats", "seatadmin")."'>";
	$content .= "<br>";
	$content .= $seatcontent; // Add post-seat-content to content

	$content .= "</form>";
	$content .= "</td><td>";
	// Add row
	$content .= "<form method=POST action=?module=seatadmin&amp;action=addrow>\n";
	$content .= "<input type=submit value='".lang("Add row", "seatadmin")."'>\n";
	$content .= "</form>\n\n";

	// Add column
	$content .= "<form method=POST action=?module=seatadmin&amp;action=addcolumn>\n";
	$content .= "<input type=submit value='".lang("Add column", "seatadmin")."'>\n";
	$content .= "</form>\n\n";

	$content .= "</td></tr>";
	$content .= "</table>";



} // End if(!isset($action))


elseif($action == "addrow") {

	// Okey, first, find out how many columns exists
	$qCheckColumns = db_query("SELECT MAX(seatY)+1 AS newColumn FROM ".$sql_prefix."_seatReg
		WHERE eventID = ".$sessioninfo->eventID);
	$rCheckColumns = db_fetch($qCheckColumns);
	$newColumn = $rCheckColumns->newColumn;

	// Second, check how many rows exists, and add a column pr. new cell
	// Probably some better way of doing this, buuut..
	$qCheckRows = db_query("SELECT DISTINCT seatX FROM ".$sql_prefix."_seatReg
		WHERE eventID = ".$sessioninfo->eventID);
	while($rCheckRows = db_fetch($qCheckRows)) {
		db_query("INSERT INTO ".$sql_prefix."_seatReg
			SET seatY = $newColumn,
			seatX = $rCheckRows->seatX,
			eventID = ".$sessioninfo->eventID
			);
	} // End while (rCheckRows)

	// Okey, we've added the new column. Refresh back
	header("Location: ?module=seatadmin");
} // End if action == addcolumn


elseif($action == "addcolumn") {

	// Okey, first, find out how many rows exists
	$qCheckRows = db_query("SELECT MAX(seatX)+1 AS newRow FROM ".$sql_prefix."_seatReg
		WHERE eventID = ".$sessioninfo->eventID);
	$rCheckRows = db_fetch($qCheckRows);
	$newRow = $rCheckRows->newRow;

	// Second, check how many columns exists, and add a row pr. new cell
	// Probably some better way of doing this, buuut..
	$qCheckColumns = db_query("SELECT DISTINCT seatY FROM ".$sql_prefix."_seatReg
		WHERE eventID = ".$sessioninfo->eventID);
	while($rCheckColumns = db_fetch($qCheckColumns)) {
		db_query("INSERT INTO ".$sql_prefix."_seatReg
			SET seatX = $newRow,
			seatY = $rCheckColumns->seatY,
			eventID = ".$sessioninfo->eventID
			);
	} // End while (rCheckColumns)

	// Okey, we've added the new row. Refresh back
	header("Location: ?module=seatadmin");
} // End if action == addrow



elseif($action == "doUpdateSeat") {
	$extra = NULL;
	// Define what action is to be done with these seats
	switch ($type) {
		case "d":
			$color = 'blue';
			break;
		case "w":
			$color = 'black';
			break;
		case "p":
			$color = 'red';
			$extra = $_POST['seatpassword'];
			break;
		case "g":
			$color = 'green';
			$extra = $_POST['group'];
			break;
		case "t":
			$color = 'white';
			break;
		case "a":
			$color = 'green';
			break;
		case "o":
			$color = 'purple';
			break;
		case "b":
			$color = 'white';
			break;
		default:
			$color = 'purple';
			break;
	} // End switch


	$qFindSeats = db_query("SELECT seatX,seatY FROM ".$sql_prefix."_seatReg
		WHERE eventID = ".$sessioninfo->eventID);
	while($rFindSeats = db_fetch($qFindSeats)) {
		// If this seat is 1 (checked), update it's type
		if($_POST['x'.$rFindSeats->seatX.'y'.$rFindSeats->seatY] == 1) {
			db_query("UPDATE ".$sql_prefix."_seatReg SET
				type = '".db_escape($type)."',
				color = '$color',
				extra = '$extra'
				WHERE seatX = $rFindSeats->seatX
				AND seatY = $rFindSeats->seatY
				AND eventID = $sessioninfo->eventID
			");
		} // end if POST = 1

	} // End while rFindSeats

	header("Location: ?module=seatadmin");
} // End if action == updateSeat

