<?php

$action = $_GET['action'];

$acl_access = acl_access("crewlist", "", $sessioninfo->eventID);

if($acl_access == 'No') die("No access");
if(empty($action)) {
   // add design-file

	$qGetCrewMembers = db_query("SELECT DISTINCT gm.userID FROM ".$sql_prefix."_group_members gm 
		JOIN ".$sql_prefix."_ACLs acl ON acl.groupID=gm.groupID
		WHERE acl.accessmodule = 'crewlist' AND acl.access != 'No' AND acl.eventID = '$sessioninfo->eventID'");

	$totalcrewmembers = db_num($qGetCrewMembers);

	$content .= "<h2>"._("Crewlist")."</h2>\n";

	$content .= '<table class="table">';
	$content .= "<thead><tr><th>";
	$content .= lang("Name", "crewlist");
	$content .= "</th><th>";
	$content .= lang("Nick", "crewlist");
	$content .= "</th><th>";
	$content .= lang("EMail", "crewlist");
	$content .= "</th><th>";
	$content .= lang("Cellphone", "crewlist");
	$content .= "</th><th>";
	$content .= lang("Access", "crewlist");
	$content .= "</th></tr></thead><tbody>";

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
	$content .= "</tbody></table>";


	$content .= "<br /><br />\n<h3>"._("Number of persons in crew")."</h3>\n";

	$qCount = db_query ("SELECT g.groupname, COUNT(gm.userID) as members FROM ".$sql_prefix."_group_members AS gm, ".$sql_prefix."_ACLs AS acl, ".$sql_prefix."_groups as g WHERE g.ID=gm.groupID AND gm.groupID=acl.groupID AND acl.accessmodule='crewlist' AND acl.access!='No' AND acl.eventID=".$sessioninfo->eventID." GROUP BY gm.groupID;");
	
	$content .= "<table class='crewlist summary-table'>\n";
	$content .= "<tr>\n";
	$content .= "<th>"._("Crew")."</th>\n";
	$content .= "<th>"._("Members")."</th>\n";
	$content .= "</tr>\n";
	
	$rownum = 1;
	while ($rCount = db_fetch ($qCount))
	{
		if ($rownum == 3)
		{
			$rownum = 1;
		}
		$content .= "<tr class='crewlistRow".$rownum."'>\n";
		$content .= "<td>";
		$content .= $rCount->groupname;
		$content .= "</td>\n";
		$content .= "<td style='text-align: center;'>";
		$content .= $rCount->members;
		$content .= "</td>\n";
		$content .= "</tr>\n";

		$rownum++;
	}

	if ($rownum == 3)
	{
		$rownum = 1;
	}
	$content .= "<tr class='crewlistRow".$rownum."'>\n";
	$content .= "<td><i>";
	$content .= _("Total in crew");
	$content .= "</i></td>\n";
	$content .= "<td style='text-align: center;'><i>";
	$content .= $totalcrewmembers;
	$content .= "</i></td>\n";
	$content .= "</tr>\n";

	$content .= "</table>\n";



} // End empty($action)
