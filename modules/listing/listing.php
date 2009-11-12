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
	if($listingtype[$list]['displaymode'] == 'CSV') {
		$hide_smarty = 1;
		$format = "CSV";
	}
	$content .= "<table>";
	$qListing = db_query($SQL);
	$column_count = db_num_fields ($qListing);
	$content .= "<tr>";
	for ($column_num = 0;$column_num < $column_count;$column_num++)
	{
	        $field_name = db_field_name($qListing, $column_num);
		$content .= "<th>".lang($field_name, "listing")."</th>";
	}
	$content .= "</tr>";
	while($rListing = db_fetch_assoc($qListing)) {
		$content .= "<tr>";
		foreach($rListing AS $name => $value) {
			$content .= "<td>".$value."</td>";
		} // End for

		$content .= "</tr>";


#		print_r($rListing);
	} // End while

	$content .= "</table>";

	if($hide_smarty == 1) {
		## FIXME: Should add some replaces to convert tables to CSV
		$content = str_replace("<table>", "", $content);
		$content = str_replace("</td>", "", $content);
		$content = str_replace("</tr>", "", $content);
		$content = str_replace("</th>", "", $content);
		$content = str_replace("<tr>", "\n", $content);
		$content = str_replace("<td>", ";", $content);
		$content = str_replace("<th>", ";", $content);
		$content = str_replace("\n;", "\n", $content);
		echo $content;
	}

} // End elseif
