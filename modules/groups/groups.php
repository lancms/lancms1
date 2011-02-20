<?php

$action = $_GET['action'];
$groupID = $_GET['groupID'];


if(!isset($action)) {
	$qListMyGroups = db_query("SELECT g.ID,g.groupname,g.groupType FROM ".$sql_prefix."_groups g
		JOIN ".$sql_prefix."_group_members m ON g.ID=m.groupID WHERE m.userID = ".$sessioninfo->userID);

	$content .= "<table>";
	while($rListMyGroups = db_fetch($qListMyGroups)) {
		$content .= "<tr><td><a href=\"?module=groups&action=listGroup&groupID=$rListMyGroups->ID\">";
		$content .= $rListMyGroups->groupname;
		$content .= "</a></td><td>".lang($rListMyGroups->groupType, "groups")."</td></tr>";
	} // End while(rListMyGroup);
	$content .= "</table>";

	$content .= "<br /><br />";

	$content .= lang("Create new clan", "groups");

	// Form to display to create clans (global groups, groupType = clan)
	if(!empty($_GET['errormsg'])) $content .= $_GET['errormsg']."<br />\n";
	$content .= "<form method=POST action=index.php?module=groups&amp;action=doCreateClan>\n";
	$content .= "<input type=text name=clanname value='".$_GET['clanname']."'> ".lang("Clan name", "groups");
	$content .= "<br /><input type=text name=clanpassword value='".$_GET['clanpassword']."'> ".lang("Clan password (to join the clan)", "groups");
	$content .= "<br /><input type=submit value='".lang("Create clan", "groups")."'>\n";
	$content .= "</form>\n";



} // End if !isset(action)

elseif(($action == 'listGroup') || ($action == 'addGroupMember') || ($action == 'changeGroupRights') && !empty($groupID))
{
	/* this action list the "group main page" */
	// FIXME: ACL...
	$searchUser = $_POST['searchUser'];

	// Display errormsg if it is set
	if(isset($_GET['errormsg'])) $content .= $_GET['errormsg']."<br />\n";

	// First, check what info we have about this group
	$qShowGroupInfo = db_query("SELECT * FROM ".$sql_prefix."_groups WHERE ID = ".db_escape($groupID));
	$rShowGroupInfo = db_fetch($qShowGroupInfo);

	$content .= lang("Group: ", "groups");
	$content .= $rShowGroupInfo->groupname;
	$content .= "<br /><br />";
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
		if(acl_access("grouprights", $groupID, 1) == 'Admin' && $action != 'changeGroupRights')
		{
			$content .= "<a href=\"?module=groups&action=changeGroupRights&groupID=$groupID&userID=$rListMembers->userID\">";
			$content .= $rListMembers->access;
			$content .= "</a>\n";
		} // End acl_access(grouprights) == admin & action != 'changeGroupRights'
		elseif(acl_access("grouprights", $groupID, 1) == 'Admin' && $action == 'changeGroupRights' && $_GET['userID'] == $rListMembers->userID)
		{
			$content .= "<form method=POST action=?module=groups&amp;action=doChangeGroupRights&amp;groupID=$groupID&amp;userID=".$_GET['userID'].">\n";
			$content .= "<select name=groupRights>\n";
			$content .= option_rights($rListMembers->access);
			$content .= "</select><input type=submit value='".lang("Save", "group")."'>";
			$content .= "</form>";
		} // end acl_access(grouprights == Admin & action == changeGroupRights
		else
			$content .= $rListMembers->access;

		$content .= "</td></tr>\n\n";
	} // End while $rListMembers

	$content .= "</table>";
	// Do test of users group-rights. If admin, display add members-form
	if(acl_access("grouprights", $groupID, 1) == 'Admin')
	{
		$content .= "<form method=POST action=?module=groups&amp;action=addGroupMember&amp;groupID=$groupID>\n";
		$content .= "<input type=text name=searchUser value='".$searchUser."'>";
		$content .= "<input type=submit value='".lang("Search user", "groups")."'>";
		$content .= "</form>";

		// If we're searching for a new user; display it
		if($action == "addGroupMember")
		{

			$qFindUser = db_query("SELECT * FROM ".$sql_prefix."_users
				WHERE ID LIKE '%".$searchUser."%'
				OR nick LIKE '%".$searchUser."%'
				OR EMail LIKE '%".$searchUser."%'
				OR firstName LIKE '%".$searchUser."%'
				OR lastName LIKE '%".$searchUser."%'
			");
			// Check how many results we found, give error if to few or to many
			if(db_num($qFindUser) == 0)
				$content .= lang("No users found", "groups");
			elseif(db_num($qFindUser) > 20)
				$content .= lang("Too many users found, please specify", "groups");
			else // We probably got between one and 20 results
			{
				$content .= '<br /><table>';
				while($rFindUser = db_fetch($qFindUser))
				{
					$content .= "<tr><td>";
					$content .= "<a href=\"?module=groups&amp;action=doAddMember&amp;userID=$rFindUser->ID&groupID=$groupID\">";
					$content .= $rFindUser->firstName." ".$rFindUser->lastName." (".$rFindUser->nick.")";
					$content .= "</td></tr>";
				} // End while rFindUser

				$content .= "</table>";
			} // End else

		} // End action == addGroupMember

	} // Enf if acl_access(grouprights) == Admin
} // End if action = ListGroup




elseif($action == "doCreateClan" && config("users_may_create_clan") && $sessioninfo->userID > 1)
{
	$clanname = $_POST['clanname'];
	$clanpwd = $_POST['clanpassword'];

	$qrCheckName = db_fetch(db_query("SELECT COUNT(*) AS amount FROM ".$sql_prefix."_groups WHERE groupname LIKE '".db_escape($clanname)."'"));

	if(empty($clanname))
	{
		// Clanname is not specified
		header("Location: ?module=groups&action=createClan&clanpassword=$clanpwd&errormsg=".lang("Please provide the name of the clan!", "groups"));
	} // End if empty clanname
	elseif(empty($clanpwd))
	{
		// Clan password is not specified
	//	header("Location: ?module=groups&action=createClan&clanname=$clanname&errormsg=".lang("Please provide a password for the clan!", "groups"));
	} // End if empty clanpwd

	elseif($qrCheckName->amount > 0)
	{
		// If clanname already exists
		header("Location: ?module=groups&action=createClan&clanname&$clanname&clanpassword=$clanpwd&errormsg=".lang("Clanname is already in use. Please choose another name", "groups"));
		log_add("groups", "clanCreateExists", serialize($_POST));
	} // End if clanname alreaddy exists

	else
	{
		// The clan name and password is accepted. INSERT to DB
		db_query("INSERT INTO ".$sql_prefix."_groups SET
			groupname = '".db_escape($clanname)."',
			grouppassword = '".$clanpwd."',
			createdByUser = '".$sessioninfo->userID."',
			createdTimestamp = ".time()
			);
		// Fetch whatever was just inserted into DB, as Ive never got mysql_insert_id() to work properly
		$qLastGroupID = db_query("SELECT ID FROM ".$sql_prefix."_groups WHERE createdByUser = ".$sessioninfo->userID." ORDER BY ID DESC LIMIT 0,1");
		$rLastGroupID = db_fetch($qLastGroupID);

		// Give the user admin-rights to his own group
		db_query("INSERT INTO ".$sql_prefix."_group_members SET
			groupID = '$rLastGroupID->ID',
			userID = '".$sessioninfo->userID."',
			access = 'Admin'");
		$log_new['clanname'] = $clanname;
		$log_new['clanID'] = $rLastGroupID->ID;
		$log_new['clanPWD'] = $clanpwd;

		log_add("groups", "clanCreate", serialize($log_new));
		header("Location: ?module=groups&action=listGroup&groupID=$rLastGroupID->ID");
	} // End else (clan name and pwd accepted)
} // End elseif action == doCreateClan


elseif($action == "doAddMember" && isset($_GET['userID']))
{
	// Do test of users group-rights
	if(acl_access("grouprights", $groupID, 1) != 'Admin')
		die("Sorry, you need admin-rights to do this!");

	$userID = $_GET['userID'];

	$qCheckUser = db_query("SELECT COUNT(*) AS count FROM ".$sql_prefix."_group_members
		WHERE userID = '".db_escape($userID)."'
		AND groupID = ".db_escape($groupID));
	$rCheckUser = db_fetch($qCheckUser);

	if($rCheckUser->count != 0)
		header("Location: ?module=groups&action=addGroupMember&groupID=$groupID&errormsg=".lang("User already member of group", "groups"));
	else
	{
		db_query("INSERT INTO ".$sql_prefix."_group_members SET
			groupID = ".db_escape($groupID).",
			userID = ".db_escape($userID).",
			access = 'Read'");

		$log_new['userID'] = $userID;
		$log_new['groupID'] = $groupID;
		log_add("groups", "addmember", serialize($log_new));

		header("Location: ?module=groups&action=listGroup&groupID=$groupID");
	} // End else

} // end action = doAddGroupMember


elseif($action == 'doChangeGroupRights' && !empty($groupID) && !empty($_GET['userID']))
{
	// Do test of users group-rights
	if(acl_access("grouprights", $groupID, 1) != 'Admin')
		die("Sorry, you need admin-rights to do this!");

	$access = $_POST['groupRights'];
	
	$log_new['userID'] = $_GET['userID'];
	$log_new['groupID'] = $groupID;
	
	if($access == "No") {
		db_query("DELETE FROM ".$sql_prefix."_group_members WHERE groupID = '".db_escape($groupID)."'
			AND userID = '".db_escape($_GET['userID'])."'");

		log_add("groups", "changeGroupRights_remove", serialize($log_new));
	} // End if access= no, delete
	else {
		db_query("UPDATE ".$sql_prefix."_group_members
			SET access = '".db_escape($access)."'
			WHERE groupID = '".db_escape($groupID)."'
			AND userID = '".db_escape($_GET['userID'])."'");
		$log_new['access'] = $access;
		log_add("groups", "changeGroupRights", serialize($log_new));
	} // End else
	header("Location: ?module=groups&action=listGroup&groupID=$groupID");
} // End if action == doChangeGroupRights

else {
	$content .= "action == ".$action;
	$content .= "userID == ".$sessioninfo->userID;
	$content .= "create_clan == ".config("users_may_create_clan");
}
