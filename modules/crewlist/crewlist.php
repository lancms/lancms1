<?php

$action = $_GET['action'];

$acl_access = acl_access("crewlist", "", $sessioninfo->eventID);

if($acl_access == 'No') die("No access");
if(empty($action)) {
   // add design-file
	$design_head .= '<link href="templates/shared/crewlist.css" rel="stylesheet" type="text/css">';

	$qGetCrewMembers = db_query("SELECT DISTINCT gm.userID FROM ".$sql_prefix."_group_members gm 
		JOIN ".$sql_prefix."_ACLs acl ON acl.groupID=gm.groupID 
		WHERE acl.accessmodule = 'crewlist' AND acl.access != 'No' AND acl.eventID = '$sessioninfo->eventID'");

	$content .= '<table>';
	$content .= "<tr><th>";
	$content .= lang("Name", "crewlist");
	$content .= "</th><th>";
	$content .= lang("Nick", "crewlist");
	$content .= "</th><th>";
	$content .= lang("EMail", "crewlist");
	$content .= "</th><th>";
	$content .= lang("Cellphone", "crewlist");
	$content .= "</th><th>";
	$content .= lang("Access", "crewlist");
	$content .= "</th></tr>";

	$listrowcount = 1;
	while($rGetCrewMembers = db_fetch($qGetCrewMembers))
	{
		$qCrewinfo = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '$rGetCrewMembers->userID'");
		$rCrewinfo = db_fetch($qCrewinfo);
		$content .= "<tr class='crewlistRow".$listrowcount."'><td>";
		$content .= $rCrewinfo->firstName." ".$rCrewinfo->lastName;
		$content .= "</td><td>";
		$content .= $rCrewinfo->nick;
		$content .= "</td><td>";
		$content .= $rCrewinfo->EMail;
		$content .= "</td><td>";
		$content .= $rCrewinfo->cellphone;
		$content .= "</td><td>";

		$qListCrews = db_query("SELECT groupname FROM ".$sql_prefix."_groups JOIN ".$sql_prefix."_group_members 
			ON ".$sql_prefix."_groups.ID=".$sql_prefix."_group_members.groupID WHERE ".$sql_prefix."_group_members.userID = '$rGetCrewMembers->userID' AND eventID = '$sessioninfo->eventID' AND groupType = 'access'");
		unset ($groupnames);
		while($rListCrews = db_fetch($qListCrews))
		{
			$groupnames[] = $rListCrews->groupname;
		} // End while
		foreach ($groupnames as $num => $count)
		{
			$content .= $groupnames[$num];
			$groupcount = count ($groupnames) - 1;
			if ($num < $groupcount)
			{
				$content .= ", ";
			}
		}

		$content .= "</td></tr>";
	
		$listrowcount++;
		if ($listrowcount == 3)
		{
			$listrowcount = 1;
		}
	} // End while
	$content .= "</table>";

} // End empty($action)
