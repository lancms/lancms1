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


elseif($action == "groupACLs" || $action == "changeGroupAccess")
{
	// action to specify who has what rights on event
	
	// Check what groups exists
	$qListGroups = db_query("SELECT groupID,access FROM ".$sql_prefix."_ACLs 
		WHERE eventID IN ($eventID, 0) 
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
		// If changeGroupAccess is set, show select to change access
		if($action == 'changeGroupAccess' && $_GET['groupID'] == $rListGroups->groupID) 
		{
			$content .= "<td><form method=POST action=?module=eventadmin&action=doChangeGroupAccess&groupID=".$_GET['groupID'].">\n";
			$content .= "<select name=groupRights>\n";
			$content .= option_rights($rListGroups->access);
			$content .= "</select><input type=submit value='".lang("Save", "eventadmin")."'>";
			$content .= "</form></td>";
		} // End if action != changeGroupAccess
		else
		{
			$content .= "<td><a href=?module=eventadmin&action=changeGroupAccess&groupID=$rListGroups->groupID>";
			$content .= lang($rListGroups->access, "eventadmin")."</a></td>";
			
		} // End else
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


elseif($action == "addGroupACL" && isset($_POST['groupID']))
{
	// Action to add new groups to ACL
	// This action requires admin-rights on the event
	if(acl_access("eventadmin", "", $eventID) != 'Admin')
		die("Sorry, you have to be eventadmin to give eventrights");
	
	$groupID = $_POST['groupID'];
	
	// Check if that group already has rights on the event
	$qCheckGroupRights = db_query("SELECT COUNT(*) AS count FROM ".$sql_prefix."_ACLs WHERE
		eventID = '$eventID' AND 
		groupID = '".db_escape($groupID)."' AND
		accessmodule = 'eventadmin'");
	$rCheckGroupRights = db_fetch($qCheckGroupRights);
	
	// if it has rights; die
	if($rCheckGroupRights->count > 0)
		die("That group already has rights....");
	
	// Else; add group to access-table
	db_query("INSERT INTO ".$sql_prefix."_ACLs SET 
		eventID = '$eventID', 
		groupID = '".db_escape($groupID)."', 
		accessmodule = 'eventadmin'");
		
	// Redirect back to eventadmin&action=groupACLs
	header("Location: ?module=eventadmin&action=groupACLs");
} // End if action = addGroupACL


elseif($action == "doChangeGroupAccess" && isset($_GET['groupID']))
{
	// Action do change what right the group has on eventadmin
	if(acl_access("eventadmin", "", $eventID) != 'Admin')
		die("Sorry, you have to be eventadmin to give eventrights");
	$groupID = $_GET['groupID'];
	$access = $_POST['groupRights']; 
	
	// FIXME: should probably do a check so that you can't remove the last group with admin-rights
	
	db_query("UPDATE ".$sql_prefix."_ACLs SET access = '".db_escape($access)."' 
		WHERE groupID = '".db_escape($groupID)."'
		AND eventID = '".$eventID."'");
	header("Location: ?module=eventadmin&action=groupACLs");
} // End action = doChangeGroupAccess