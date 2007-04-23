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


elseif($action == "createClan" && config("users_may_create_clan") && $sessioninfo->userID != 0)
{
	// Form to display to create clans (global groups, groupType = clan)
	if(!empty($_GET['errormsg'])) $content .= $_GET['errormsg']."<br>\n";
	$content .= "<form method=POST action=index.php?module=groups&action=doCreateClan>\n";
	$content .= "<input type=text name=clanname value='".$_GET['clanname']."'> ".lang("Clan name", "groups");
	$content .= "<br><input type=text name=clanpassword value='".$_GET['clanpassword']."'> ".lang("Clan password (to join the clan)", "groups");
	$content .= "<br><input type=submit value='".lang("Create clan", "groups")."'>\n";
	
	
} // end if action == createClan

elseif($action == "doCreateClan" && config("users_may_create_clan") && $sessioninfo->userID != 0)
{
	$clanname = $_POST['clanname'];
	$clanpwd = $_POST['clanpassword'];
	
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
	/*
	$qrCheckName = db_fetch(db_query("SELECT COUNT(*) AS amount FROM ".$sql_prefix."_groups WHERE groupname LIKE '".db_escape($clanname)."'"));
		
	elseif($qrCheckName->amount > 0)
	{
		// If clanname already exists
		header("Location: ?module=groups&action=createClan&clanname&$clanname&clanpassword=$clanpwd&errormsg=".lang("Clanname is already in use. Please choose another name", "groups"));
	} // End if clanname alreaddy exists
	*/
	else
	{
		// The clan name and password is accepted. INSERT to DB
		db_query("INSERT INTO ".$sql_prefix."_groups SET 
			groupname = '".db_escape($clanname)."', 
			grouppassword = '".$clanpwd."',
			created_by = '".$sessioninfo->userID."',
			created_timestamp = ".time()
			);
		// Fetch whatever was just inserted into DB, as Ive never got mysql_insert_id() to work properly
		$qLastGroupID = db_query("SELECT ID FROM ".$sql_prefix."_groups WHERE created_by = ".$sessioninfo->userID." ORDER BY ID DESC LIMIT 0,1");
		$rLastGroupID = db_fetch($qLastGroupID);
		
		// Give the user admin-rights to his own group
		db_query("INSERT INTO ".$sql_prefix."_group_members SET 
			groupID = '$rLastGroupID->ID', 
			userID = '".$sessioninfo->userID."', 
			access = 'Admin'");
		header("Location: ?module=groups&action=listGroups&groupID=$rLastGroupID->ID");
	} // End else (clan name and pwd accepted)
} // End elseif action == doCreateClan