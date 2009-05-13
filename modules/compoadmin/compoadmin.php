<?php

$acl = acl_access("compoadmin", "", $sessioninfo->eventID);
$action = $_GET['action'];

if(!isset($action)) {
	$qListCompos = db_query("SELECT * FROM ".$sql_prefix."_compos WHERE eventID = '$sessioninfo->eventID'");

	$content .= "<table>";
	while($rListCompos = db_fetch($qListCompos)) {
		$content .= "<tr><td>";
		$content .= $rListCompos->componame;
		$content .= "</td></tr>";
	} // End while

	$content .= "</table>\n";

	$content .= "<br />";
	if($acl == 'Admin') {
		$content .= "<form method=POST action=?module=compoadmin&action=addCompo>\n";
		$content .= "<input type=componame> ".lang("Compo name", "compoadmin");
		$content .= "<br />";
		$content .= "<select name=compotype>\n";
		for($i=0;$i<count($compotype);$i++) {
			$content .= "<option value='$compotype[$i]'>$compotype[$i]</option>\n";
		} // End for
		$content .= "</select>\n\n";
		$content .= "<br /><input type=text name=players size=2> ".lang("Number of players per clan");
		$content .= "<br /><input type=submit value='".lang("Add compo", "compoadmin")."'>";
		$content .= "</form>";
	} // End if acl == Admin


}
