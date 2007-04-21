<?php

$action = $_GET['action'];
$groupID = $_GET['groupID'];


if($action == "listGroup" && !empty($groupID))
{
	/* this action list the "group main page" */
	
	// First, check what info we have about this group
	$qShowGroupInfo = db_query("SELECT * FROM ".$sql_prefix."_groups WHERE ID = ".db_escape($groupID));
	$rShowGroupInfo = db_fetch($qShowGroupInfo);
	
	$content .= lang("Group: ", "groups");
	$content .= $rShowGroupInfo->groupname;
	$content .= "<br><br>";
	$content .= "<table>";
	$content .= "<tr><th>".lang("Nick", "groups");
	$content .= "</th><th>".lang("Access", "groups");
	$content .= "</th></tr>\n\n";
	
	// Show a list of all members of this group, and their rights
	$qListMembers = db_query("SELECT * FROM ".$sql_prefix."_group_members 
		WHERE groupID = ".db_escape($groupID));
	while($rListMembers = db_fetch($qListMembers))
	{
		$content .= "<tr><td>";
		// Get info about this user
		$qUserInfo = db_query("SELECT nick FROM ".$sql_prefix."_users 
			WHERE ID = '$rListMembers->userID'");
		$rUserInfo = db_fetch($qUserInfo);
		$content .= $rUserInfo->nick;
		$content .= "</td><td>";
		$content .= $rListMembers->access;
		$content .= "</td></tr>\n\n";
	} // End while $rListMembers
	
	$content .= "</table>";
	
} // End if action = ListGroup