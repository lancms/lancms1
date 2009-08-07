<?php

$action = $_GET['action'];
$list = $_GET['list'];
$option = $_GET['option'];

if(!empty($list)) {
	$acl = acl_access("listing", $list, $sessioninfo->eventID);

	if($acl == 'No') die("No access");

}
$globalacl = acl_access("listing", "", $sessioninfo->eventID);


if(!isset($action)) {
	$content .= "<table>";
	for($i=0;$i<count($listingtype);$i++) {
		$content .= "<tr><td>";
		if($listingtype[$i]['option'] == 1) $do_action = 'option';
		else $do_action = 'viewlist';
		$content .= "<a href=?module=listing&action=$do_action&list=$i>";
		$content .= $listingtype[$i]['name']."</a>";
		$content .= "</td></tr>";
	}
	$content .= "</table>";

} // End if(!isset($action))

elseif($action == "option" && isset($list)) {
	$content .= "<form method=GET>\n";
	$content .= "<input type=hidden name='module' value='listing'>\n";
	$content .= "<input type=hidden name='action' value='viewlist'>\n";
	$content .= "<input type=hidden name='list' value='$list'>\n";

	$listtype = $listingtype[$list]['type'];
	
	switch($listtype) {
		case 'yearAttendee':
			$content .= "<input type=text name=option value='".date("Y")."' size=4>";
			break;
	} // End switch

	$content .= "<br />";
	$content .= "<input type=submit value='".lang("Show list", "listing")."'>";
	$content .= "</form>";

} // End action == option

elseif($action == "viewlist" && isset($list)) {
	$SQL = $listingtype[$list]['SQL'];
	$content .= "<table>";
	$qListing = db_query($SQL);

	while($rListing = db_fetch_assoc($qListing)) {
		$content .= "<tr>";
		for($i=0;$i<count($rListing);$i++) {
			$content .= "<td>".$rListing[$i]."</td>\n";
		} // End for

		$content .= "</tr>\n\n";


#		print_r($rListing);
	} // End while

	$content .= "</table>";

} // End elseif
