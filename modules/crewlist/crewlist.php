<?php

$action = $_GET['action'];

$acl_access = acl_access("crewlist", "", $sessioninfo->eventID);

if($acl_access == 'No') die("No access");
if(empty($action)) {
	$qGetCrewMembers = db_query("SELECT DISTINCT gm.userID FROM ".$sql_prefix."_group_members gm 
		JOIN ".$sql_prefix."_ACLs acl ON acl.groupID=gm.groupID 
		WHERE acl.accessmodule = 'crewlist' AND acl.access != 'No'");

	$content .= '<table>';
	while($rGetCrewMembers = db_fetch($qGetCrewMembers)) {
		$qCrewinfo = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '$rGetCrewMembers->userID'");
		$rCrewinfo = db_fetch($qCrewinfo);
		$content .= "<tr><td>";
		$content .= $rCrewinfo->firstName." ".$rCrewinfo->lastName;
		$content .= "</td><td>";
		$content .= $rCrewinfo->nick;
		$content .= "</td><td>";
		$content .= $rCrewinfo->EMail;
		$content .= "</td><td>";
		$content .= $rCrewinfo->cellphone;
		$content .= "</td></tr>";
	} // End while
	$content .= "</table>";

} // End empty($action)
