<?php
$acl_access = acl_access("seatreg_admin", "", $sessioninfo->eventID);

$action = $_GET['action'];
$type = $_POST['type'];

if($acl_access != 'Admin') {
	die("Sorry, you do not have access to this!");
}
if($action == "updateSeat") {
	if($type == "d" OR $type == "w" OR $type == "o" OR $type = "b") {
		$action = "doUpdateSeat";
	} // End if type = "normal"
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
		// Start a new row
		$content .= "<tr>\n";

		// Get the contents of the row; the columns
		$qGetSeatX = db_query("SELECT * FROM ".$sql_prefix."_seatReg
			WHERE eventID = '".$sessioninfo->eventID."'
			AND seatY = '".$rGetSeatY->seatY."'
			ORDER BY seatX ASC");

		while($rGetSeatX = db_fetch($qGetSeatX)) {
			$content .= "<td style='height: 25px; width: 25px' bgcolor=".$rGetSeatX->color;
			$content .= ">";
			$content .= "<input type=checkbox value=1 name=x".$rGetSeatX->seatX."y".$rGetSeatY->seatY.">";
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
		$content .= "<option value=$key>$value</option>\n";
	} // End foreach
	$content .= "</select>";
	$content .= "<br><input type=submit value='".lang("Change seats", "seatadmin")."'>";
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
			break;
		case "g":
			$color = 'green';
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
	} // End switch


	$qFindSeats = db_query("SELECT seatX,seatY FROM ".$sql_prefix."_seatReg
		WHERE eventID = ".$sessioninfo->eventID);
	while($rFindSeats = db_fetch($qFindSeats)) {
		// If this seat is 1 (checked), update it's type
		if($_POST['x'.$rFindSeats->seatX.'y'.$rFindSeats->seatY] == 1) {
			db_query("UPDATE ".$sql_prefix."_seatReg SET
				type = '".db_escape($type)."',
				color = '$color'
				WHERE seatX = $rFindSeats->seatX
				AND seatY = $rFindSeats->seatY
				AND eventID = $sessioninfo->eventID
			");
		} // end if POST = 1

	} // End while rFindSeats

	header("Location: ?module=seatadmin");
} // End if action == updateSeat

