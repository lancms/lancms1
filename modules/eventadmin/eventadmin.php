<?php

$action = $_GET['action'];
$eventID = $sessioninfo->eventID;

$acl_eventadmin = acl_access("eventadmin", "", $eventID);

if($acl_eventadmin == 'No')
	die("You are not admin!");


if(!isset($action))
{
    $content .= "<ul class=\"eventadmin-menu-list\">";
	// No action specified. List all eventadmin tasks
	if(acl_access("eventadmin", "", $eventID) == 'Admin')
		$content .= "<li><a href=\"?module=eventadmin&amp;action=config\">".lang("Event config", "eventadmin")."</a></li>\n";
	if(acl_access("eventadmin", "", $sessioninfo->eventID) == ('Admin' || 'Write'))
		$content .= "<li><a href=\"?module=eventadmin&amp;action=groupManagement\">".lang("Group Management", "eventadmin")."</a></li>\n";
	if(acl_access("static", "", $eventID) != 'No')
		$content .= "<li><a href=\"?module=static&amp;action=listEventPages\">".lang("Edit static pages", "eventadmin")."</a></li>\n";
	if(acl_access("FAQ", "", $eventID) == 'Admin')
		$content .= "<li><a href=\"?module=FAQ&amp;action=adminFAQs\">".lang("Edit FAQs", "eventadmin")."</a></li>\n";
	if(acl_access("wannabeadmin", "", $eventID) != 'No')
		$content .= "<li><a href=\"?module=wannabeadmin&amp;action=adminWannabe\">".lang("WannabeCrew", "eventadmin")."</a></li>\n";
	if(acl_access("seatadmin", "", $eventID) == 'Admin')
		$content .= "<li><a href=\"?module=seatadmin\">".lang("Seatreg Admin", "eventadmin")."</a></li>\n";
	if(acl_access("ticketadmin", "", $eventID) == 'Admin')
		$content .= "<li><a href=\"?module=ticketadmin\">".lang("Ticket Admin", "eventadmin")."</a></li>\n";
#	if(acl_access("economy", "", $eventID) != 'No')
#		$content .= "<br /><a href='?module=economy'>".lang("Economy", "eventadmin")."</a>\n";
	if(acl_access("compoadmin", "", $eventID) != 'No')
		$content .= "<li><a href='?module=compoadmin'>".lang("Compoadmin", "eventadmin")."</a></li>\n";
	if(acl_access("news", "", $eventID) != 'No')
		$content .= "<li><a href='?module=news&action=newsadmin'>".lang("Newsadmin", "eventadmin")."</a></li>\n";
	$sendSMS_ACL = acl_access("sendSMS", "", 1);
	if($sendSMS_ACL == 'Admin' || $sendSMS_ACL == 'Write') $content .= "<li><a href=?module=SMS>".lang("Send SMS", "eventadmin")."</a></li>";
	if(acl_access("kiosk_admin", "", $eventID) != 'No')
		$content .= "<li><a href='?module=kioskadmin'>".lang("Kioskadmin", "eventadmin")."</a></li>\n";
	if(acl_access("forum", "", $sessioninfo->eventID) == 'Admin') 
		$content .= "<li><a href='?module=forumadmin'>".lang("Forumadmin", "eventadmin")."</a></li>\n";
	if(acl_access("infoscreen", "", $sessioninfo->eventID) != 'No')
		$content .= "<li><a href='?module=infoscreens'>"._("Infoscreens")."</a></li>\n";
	if(acl_access("globaladmin", "", $sessioninfo->eventID != 'No') && !empty($mailList)) 
		$content .= "<li><a href='?module=mail&action=massmail'>"._("Mass-mailer")."</a></li>\n";

    $content .= "</ul>";

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

	$log_new['groupID'] = $groupID;
	$log_new['access'] = $access;
	log_add("eventadmin", "changeGroupAccess", serialize($log_new));

	header("Location: ?module=eventadmin&action=groupACLs");
} // End action = doChangeGroupAccess

elseif($action == "groupManagement")
{
	// Action to manage groups
	$groups = UserGroupManager::getInstance()->getEventGroups();

	// If error is set; display error.
	if(isset($_GET['errormsg'])) $content .= $_GET['errormsg']."<br /><br />\n";

	if(count($groups) > 0) {
		$content .= '<table class="table full-width">';
		foreach ($groups as $group) {
			// list up all groups associated with this event
			$content .= "<tr><td><a href=\"?module=groups&amp;action=listGroup&amp;groupID=" . $group->getGroupID() . "\">";
			$content .= $group->getName() . "</a>";
			if(acl_access("eventadmin", "", $sessioninfo->eventID) == 'Admin') {
				$content .= "</td><td><a href=\"?module=eventadmin&amp;action=groupRights&amp;groupID=" . $group->getGroupID() . "\">";
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
		$log_new['groupName'] = $groupname;
		log_add("eventadmin", "addAccessGroup", serialize($log_new));
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
	if($_GET['saved'] == "OK") $content .= _("Config successfully saved");

	$content .= "<form method=\"post\" action='?module=eventadmin&amp;action=doConfig'>\n";
	for($i=0;$i<count($eventconfig['checkbox']);$i++) {
		$cfg_current = config($eventconfig['checkbox'][$i]['config'], $eventID);
		$content .= "<p class=\"nopad\"><input type=\"checkbox\" name='".$eventconfig['checkbox'][$i]['config']."'";
		if($cfg_current) $content .= " CHECKED";
		$content .= " /> ".$eventconfig['checkbox'][$i]['name']."</p>\n";
	} // End for

	$content .= "<p class=\"nopad\"><input type=\"submit\" value='".lang("Save", "eventadmin_config")."' /></p></form>";


} // end action == config


elseif($action == "doConfig") {
	if($acl_eventadmin != 'Admin') die("No access to admin event");
	for($i=0;$i<count($eventconfig['checkbox']);$i++) {
		$evtcfg = $eventconfig['checkbox'][$i]['config'];

		$log_old[$evtcfg] = config($evtcfg, $eventID);

		$post = $_POST[$evtcfg];
		if($post == "on") $post = 1;
		else $post = "disable";
		#echo $evtcfg.": ".$post;
		config($eventconfig['checkbox'][$i]['config'], $eventID, $post);

		$log_new[$evtcfg] = $post;
	} // End for
	log_add("eventadmin", "doConfig", serialize($log_new), serialize($log_old));
	header("Location: ?module=eventadmin&action=config&action=config&saved=OK");
}

elseif(($action == "groupRights" || $action == "changeGroupRights") && !empty($_GET['groupID'])) {
	if(acl_access("eventadmin", "", $eventID) != 'Admin')
		die("Sorry, you have to be eventadmin to give eventrights");
	$groupID = $_GET['groupID'];

	$content .= "<h2>"._("Editing group rights for:")." <strong>".get_groupname($groupID)."</strong></h2>\n<br />\n";
	$content .= "<a href=\"?module=eventadmin&amp;action=groupManagement\">".lang("Back to groups", "eventadmin")."</a>\n<br /><br />\n";
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
			$qFindAccess = db_query("SELECT * FROM ".$sql_prefix."_ACLs WHERE eventID IN (1, $eventID)
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
	$newright = $_REQUEST['groupRight'];
	$groupID = $_GET['groupID'];
	$accessmodule = $_GET['accessmodule'];
	if(acl_access("eventadmin", "", $eventID) != 'Admin')
		die("Sorry, you have to be eventadmin to give eventrights");
	if(in_array($accessmodule, $globalaccess) && acl_access("globaladmin", "", 0) != 'Admin')
		die("Sorry, you have to be globaladmin to give globalrights");
	if(in_array($accessmodule, $globalaccess)) $event = 1;
	else $event = $eventID;
	$qCheckExisting = db_query("SELECT * FROM ".$sql_prefix."_ACLs
		WHERE groupID = '".db_escape($groupID)."'
		AND accessmodule = '".db_escape($accessmodule)."'
		AND eventID = $event");
	if(db_num($qCheckExisting) == 0) {
		db_query("INSERT INTO ".$sql_prefix."_ACLs SET groupID = '".db_escape($groupID)."',
			accessmodule = '".db_escape($accessmodule)."',
			access = '".db_escape($newright)."',
			eventID = $event");
	} // end if
	else {
		db_query("UPDATE ".$sql_prefix."_ACLs SET access = '".db_escape($newright)."'
			WHERE accessmodule = '".db_escape($accessmodule)."'
			AND groupID = '".db_escape($groupID)."'
			AND eventID = $event");
	} // End else
	$log_new['groupID'] = $groupID;
	$log_new['accessmodule'] = $accessmodule;
	$log_new['access'] = $newright;

	log_add("eventadmin", "doChangeRight", serialize($log_new));

	if($accessmodule == 'eventAttendee') {
		header("Location: ?module=eventadmin&action=eventaccess");
	} else {
		header("Location: ?module=eventadmin&action=groupRights&groupID=$groupID");
	}
}

elseif($action == "eventaccess") {
	// if event is private, admin who can attend
	// FIXME: Only works for accessgroups for now...
	// Should be possible for specially invited people in clans, and all accessgroups
	$qListGroups = db_query("SELECT * FROM ".$sql_prefix."_groups WHERE groupType = 'access' AND ID != 1 ORDER BY eventID DESC");
	$row = 1;
	$content .= "<table>";
	while($rListGroups = db_fetch($qListGroups)) {
		$content .= "<tr class='listRow$row'><td>";
		$content .= $rListGroups->groupname;
		$content .= "</td><td>";
		$qCheckAccess = db_query("SELECT * FROM ".$sql_prefix."_ACLs 
			WHERE access != 'No' 
			AND accessmodule = 'eventAttendee' 
			AND eventID = '$sessioninfo->eventID' 
			AND groupID = '$rListGroups->ID'");
		if(db_num($qCheckAccess) == 0) {
			$content .= "<a href=?module=eventadmin&action=doChangeRights&groupID=$rListGroups->ID&accessmodule=eventAttendee&groupRight=Read>";
			$content .= "<img src=images/icons/no.png width=\"50%\"></a>";
//			$content .= lang("Allow attendee", "eventadmin")."</a>";
		} else {
			$content .= "<a href=?module=eventadmin&action=doChangeRights&groupID=$rListGroups->ID&accessmodule=eventAttendee&groupRight=No>";
			$content .= "<img src=images/icons/yes.png width=\"50%\"></a>";
//			$content .= lang("Disallow attendee", "eventadmin")."</a>";
		}
		$row++;
		if($row == 3) $row = 1;
	} // End while
	$content .= "</table>";

} // End if action == eventaccess
		
