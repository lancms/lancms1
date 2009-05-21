<?php

$action = $_GET['action'];
$eventID = $sessioninfo->eventID;

$acl_eventadmin = acl_access("eventadmin", "", $eventID);

if($acl_eventadmin == 'No')
	die("You are not admin!");


if(!isset($action))
{
	// No action specified. List all eventadmin tasks
	if(acl_access("eventadmin", "", $eventID) == 'Admin')
		$content .= "<br /><a href=\"?module=eventadmin&amp;action=config\">".lang("Event config", "eventadmin")."</a>\n";
	if(acl_access("eventadmin", "", $sessioninfo->eventID) = ('Admin' || 'Write'))
		$content .= "<br /><a href=\"?module=eventadmin&amp;action=groupManagement\">".lang("Group Management", "eventadmin")."</a>\n";
	if(acl_access("static", "", $eventID) != 'No')
		$content .= "<br /><a href=\"?module=static&amp;action=listEventPages\">".lang("Edit static pages", "eventadmin")."</a>\n";
	if(acl_access("FAQ", "", $eventID) == 'Admin')
		$content .= "<br /><a href=\"?module=FAQ&amp;action=adminFAQs\">".lang("Edit FAQs", "eventadmin")."</a>\n";
	if(acl_access("wannabeadmin", "", $eventID) != 'No')
		$content .= "<br /><a href=\"?module=wannabeadmin&amp;action=adminWannabe\">".lang("WannabeCrew", "eventadmin")."</a>\n";
	if(acl_access("seatadmin", "", $eventID) == 'Admin')
		$content .= "<br /><a href=\"?module=seatadmin\">".lang("Seatreg Admin", "eventadmin")."</a>\n";
	if(acl_access("ticketadmin", "", $eventID) == 'Admin')
		$content .= "<br /><a href=\"?module=ticketadmin\">".lang("Ticket Admin", "eventadmin")."</a>\n";
	if(acl_access("economy", "", $eventID) != 'No')
		$content .= "<br /><a href='?module=economy'>".lang("Economy", "eventadmin")."</a>\n";
	if(acl_access("compoadmin", "", $eventID) != 'No')
		$content .= "<br /><a href='?module=compoadmin'>".lang("Compoadmin", "eventadmin")."</a>\n";

} // End if !isset(action)


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
		AND eventID = '".$eventID."'
		AND accessmodule = 'eventadmin'");
	header("Location: ?module=eventadmin&action=groupACLs");
} // End action = doChangeGroupAccess

elseif($action == "groupManagement")
{
	// Action to manage groups
	$qListGroups = db_query("SELECT * FROM ".$sql_prefix."_groups
		WHERE eventID = '$eventID' ORDER BY groupname ASC");
	// If error is set; display error.
	if(isset($_GET['errormsg'])) $content .= $_GET['errormsg']."<br /><br />\n";

	if(mysql_num_rows($qListGroups) != 0) {
		$content .= '<table>';
		while($rListGroups = db_fetch($qListGroups))
		{
			// list up all groups associated with this event
			$content .= "<tr><td><a href=\"?module=groups&amp;action=listGroup&amp;groupID=$rListGroups->ID\">";
			$content .= $rListGroups->groupname."</a>";
			if(acl_access("eventadmin", "", $sessioninfo->eventID) == 'Admin') {
				$content .= "</td><td><a href=\"?module=eventadmin&amp;action=groupRights&amp;groupID=$rListGroups->ID\">";
				$content .= lang("Change group rights", "eventadmin")."</a>";
			}
			$content .= "</td></tr>";
		} // End while

		$content .= '</table>';
	}

	// Display form to add new groups
	$content .= "<form method=\"post\" action=\"?module=eventadmin&amp;action=addGroup\">\n";
	$content .= "<p><input type=\"text\" name=\"groupname\" />\n";
	$content .= "<input type=\"submit\" value='".lang("Add group", "eventadmin")."' /></p>";
	$content .= "</form>";
} // End action = groupManagement


elseif($action == "addGroup" && !empty($_POST['groupname']))
{
	// Action to do add of groups
	$groupname = $_POST['groupname'];

	if(acl_access("eventadmin", "", $eventID) != 'Admin')
		die("Sorry, you have to be eventadmin to create event groups");

	$qCheckGroupName = db_query("SELECT COUNT(*) AS count FROM ".$sql_prefix."_groups
		WHERE groupname LIKE '".db_escape($groupname)."'");
	$rCheckGroupName = db_fetch($qCheckGroupName);

	if($rCheckGroupName->count == 0)
	{
		// Name does not exist. Add the group
		db_query("INSERT INTO ".$sql_prefix."_groups SET
			eventID = '$eventID',
			groupname = '".db_escape($groupname)."',
			createdByUser = '$sessioninfo->userID',
			createdTimestamp = '".time()."',
			groupType = 'access'");
		header("Location: ?module=eventadmin&action=groupManagement");
	} // End if count = 0
	else
	{
		// Name exists, fail back to groupList
		header("Location: ?module=eventadmin&action=groupManagement&errormsg=".lang("Group name already exists", "eventadmin"));
	} // end else

} // end if action == addGroup

elseif($action == "config") {
	if($acl_eventadmin != 'Admin') die("No access to admin event");
	if($_GET['saved'] == "OK") $content .= "Config successfully saved";

	$content .= "<form method=\"post\" action='?module=eventadmin&amp;action=doConfig'>\n";
	for($i=0;$i<count($eventconfig['checkbox']);$i++) {
		$cfg_current = config($eventconfig['checkbox'][$i], $eventID);
		$content .= "<p class=\"nopad\"><input type=\"checkbox\" name='".$eventconfig['checkbox'][$i]."'";
		if($cfg_current) $content .= " CHECKED";
		$content .= " /> ".lang($eventconfig['checkbox'][$i], "eventconfigoption")."</p>\n";
	} // End for

	$content .= "<p class=\"nopad\"><input type=\"submit\" value='".lang("Save", "eventadmin_config")."' /></p></form>";


} // end action == config


elseif($action == "doConfig") {
	if($acl_eventadmin != 'Admin') die("No access to admin event");
	for($i=0;$i<count($eventconfig['checkbox']);$i++) {
		$evtcfg = $eventconfig['checkbox'][$i];

		$post = $_POST[$evtcfg];
		if($post == "on") $post = 1;
		else $post = "disable";
		#echo $evtcfg.": ".$post;
		config($eventconfig['checkbox'][$i], $eventID, $post);
	} // End for

	header("Location: ?module=eventadmin&action=config&action=config&saved=OK");
}

elseif(($action == "groupRights" || $action == "changeGroupRights") && !empty($_GET['groupID'])) {
	if(acl_access("eventadmin", "", $eventID) != 'Admin')
		die("Sorry, you have to be eventadmin to give eventrights");
	$groupID = $_GET['groupID'];

	$content .= "<a href=\"?module=eventadmin&amp;action=groupManagement\">".lang("Back to groups", "eventadmin")."</a>\n";
	$content .= "<table>";

	// List up eventaccess-rights

	for($i=0;$i<count($eventaccess);$i++) {
		$qFindAccess = db_query("SELECT * FROM ".$sql_prefix."_ACLs WHERE eventID = $eventID
			AND groupID = '".db_escape($groupID)."' AND accessmodule = '".$eventaccess[$i]."'");
		$rFindAccess = db_fetch($qFindAccess);

		$access = $rFindAccess->access;
		if(!isset($access)) $access = 'No';
		$content .= "<tr><td>";
		$content .= $eventaccess[$i];
		$content .= "</td><td>";
		if($action == "changeGroupRights" && $eventaccess[$i] == $_GET['accessmodule']) {
			$content .= "<form method=POST action=?module=eventadmin&amp;action=doChangeRights&amp;groupID=$groupID&amp;accessmodule=$eventaccess[$i]>";
			$content .= "<select name=groupRight>";
			$content .= option_rights($access);
			$content .= "</select>";
			$content .= "<input type=submit value='".lang("Save", "eventadmin")."' />";
			$content .= "</form>";
		} // End if
		else {
			$content .= "<a href=\"?module=eventadmin&amp;action=changeGroupRights&amp;groupID=$groupID&amp;accessmodule=$eventaccess[$i]\">";
			$content .= $access;
			$content .= "</a>\n";
		} // End else
		$content .= "</td></tr>";
	} // End for

	// List up globalrights if you have globalrights
	if(acl_access("globaladmin", "", 0) == 'Admin') {
		$content .= "<tr></tr>";
		for($i=0;$i<count($globalaccess);$i++) {
			$qFindAccess = db_query("SELECT * FROM ".$sql_prefix."_ACLs WHERE eventID = $eventID
				AND groupID = '".db_escape($groupID)."' AND accessmodule = '".$globalaccess[$i]."'");
			$rFindAccess = db_fetch($qFindAccess);

			$access = $rFindAccess->access;
			if(!isset($access)) $access = 'No';
			$content .= "<tr><td>";
			$content .= $globalaccess[$i];
			$content .= "</td><td>";
			if($action == "changeGroupRights" && $globalaccess[$i] == $_GET['accessmodule']) {
				$content .= "<form method=\"post\" action=\"?module=eventadmin&amp;action=doChangeRights&amp;groupID=$groupID&amp;accessmodule=$globalaccess[$i]\">";
				$content .= "<select name=groupRight>";
				$content .= option_rights($access);
				$content .= "</select>";
				$content .= "<input type=submit value='".lang("Save", "eventadmin")."'>";
				$content .= "</form>";
			} // End if
			else {
				$content .= "<a href=\"?module=eventadmin&amp;action=changeGroupRights&amp;groupID=$groupID&amp;accessmodule=$globalaccess[$i]\">";
				$content .= $access;
				$content .= "</a>\n";
			} // End else
			$content .= "</td></tr>";
		} // End for
	} // End if acl_access(globaladmin);
	$content .= "</table>";
} // End elseif action== groupRights

elseif($action == "doChangeRights" && !empty($_GET['groupID']) && !empty($_GET['accessmodule'])) {
	$newright = $_POST['groupRight'];
	$groupID = $_GET['groupID'];
	$accessmodule = $_GET['accessmodule'];
	if(acl_access("eventadmin", "", $eventID) != 'Admin')
		die("Sorry, you have to be eventadmin to give eventrights");
	if(in_array($accessmodule, $globalaccess) && acl_access("globaladmin", "", 0) != 'Admin')
		die("Sorry, you have to be globaladmin to give globalrights");

	$qCheckExisting = db_query("SELECT * FROM ".$sql_prefix."_ACLs
		WHERE groupID = '".db_escape($groupID)."'
		AND accessmodule = '".db_escape($accessmodule)."'
		AND eventID = $eventID");
	if(db_num($qCheckExisting) == 0) {
		db_query("INSERT INTO ".$sql_prefix."_ACLs SET groupID = '".db_escape($groupID)."',
			accessmodule = '".db_escape($accessmodule)."',
			access = '".db_escape($newright)."',
			eventID = $eventID");
	} // end if
	else {
		db_query("UPDATE ".$sql_prefix."_ACLs SET access = '".db_escape($newright)."'
			WHERE accessmodule = '".db_escape($accessmodule)."'
			AND groupID = '".db_escape($groupID)."'
			AND eventID = $eventID");
	} // End else

	header("Location: ?module=eventadmin&action=groupRights&groupID=$groupID");
}
