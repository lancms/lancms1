<?php

$action = $_GET['action'];
$eventID = $sessioninfo->eventID;

$acl_eventadmin = acl_access("eventadmin", "", $eventID);

if($acl_eventadmin != ("Admin" || 'Write' || 'Read'))
	die("You are not admin!");

	
if(!isset($action))
{
	// No action specified. List all eventadmin tasks
	$content .= "<br><a href=?module=eventadmin&amp;action=groupACLs>".lang("Accessrights", "eventadmin")."</a>\n";

} // End if !isset(action)


elseif($action == "groupACLs")
{
	// action to specify who has what rights on event
	
	// Check what groups exists
	$qListGroups = db_query("SELECT groupID,access FROM ".$sql_prefix."_ACLs 
		WHERE eventID IN ($eventID, 0) AND access != 'No' 
		ORDER BY access = 'Admin' DESC,
		access = 'Write' DESC,
		access = 'Read' DESC,
		access = 'No'");
	
	$content .= '<table>';
	$content .= '<tr><th>'.lang("Group name", "eventadmin").'</th><th>'.lang("Event rights", "eventadmin").'</th></tr>';
	
	while($rListGroups = db_fetch($qListGroups))
	{
		// List up groups
		$rGroupInfo = db_fetch(db_query("SELECT * FROM ".$sql_prefix."_groups WHERE ID = $rListGroups->groupID"));
		$content .= "<tr><td>".$rGroupInfo->groupname."</td>";
		$content .= "<td>".lang($rListGroups->access, "eventadmin")."</td>";
		
		$content .= "</tr>";
	}
	
	$content .= "</table>";
	
	$content .= "<form method=POST action=?module=eventadmin&action=addGroupACL>\n";
	$content .= "<select name=groupID>\n";
	$qNoAccessGroups = db_query("SELECT * FROM ".$sql_prefix."_groups WHERE eventID IN (0, $eventID) 
		AND groupType = 'access' ORDER BY groupname ASC");
	while($rNoAccessGroups = db_fetch($qNoAccessGroups))
	{
		// List up all groups
		// Skip those that already has rights
		
		$qCheckExisting = db_query("SELECT groupID FROM ".$sql_prefix."_ACLs 
			WHERE groupID = $rNoAccessGroups->ID 
			AND eventID = $eventID 
			AND accessmodule = 'eventadmin'");
		// Skip it
		#echo db_num($qCheckExisting);
		if(db_num($qCheckExisting) > 0)
		{
			
			#$content .= "Skipped $rNoAccessGroups->groupname !\n";
			
		}
		
		else 
		{
			
			// group does not have eventadmin yet, list it
			$content .= "ELSE $rNoAccessGroups->groupname ! \n";
			$content .= "<option value=$rNoAccessGroups->ID>$rNoAccessGroups->groupname</option>\n";
		} // End else
			
		
		
	} // End while rNoAccessGroup
	$content .= "</select>\n";
	$content .= "<input type=submit value='".lang("Add group", "eventadmin")."'>\n";
	$content .= "</form>\n\n\n";
		
	
}