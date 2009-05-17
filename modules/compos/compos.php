<?php

$action = $_GET['action'];
$compo = $_GET['compo'];

if(!config("enable_composystem", $sessioninfo->eventID)) die("Composystem not enabled yet");



// First, find out if we should enable signup
$signup = 0;
$qFindTicket = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE user = '$sessioninfo->userID' 
	AND eventID = '$sessioninfo->eventID'
	AND status = 'used'");
if(db_num($qFindTicket) != 0)
	// User has one or more tickets and they are used. Allow user to signup
	$signup = 1;
if(!empty($compo)) {
	// If a compo is set, check if we're already signed up
	$qCheckSignup = db_query("SELECT * FROM ".$sql_prefix."_compoSignup 
		WHERE compoID = '".db_escape($compo)."'
		AND (userID = '$sessioninfo->userID' OR clanID IN (SELECT groupID FROM ".$sql_prefix."_group_members 
			WHERE userID = '$sessioninfo->userID'))");
	if(db_num($qCheckSignup) > 0) $signup = 2; // User has already signed up
	$rCheckSignup = db_fetch($qCheckSignup);
} // End if(!empty($compo))
#die("Signup $signup".print_r($rCheckSignup));
$design_head .= "<!-- signup: $signup -->\n";
if(!isset($action)) {
	$qListCompos = db_query("SELECT * FROM ".$sql_prefix."_compos WHERE eventID = '$sessioninfo->eventID'");
	$content .= "<table>\n";
	while($rListCompos = db_fetch($qListCompos)) {
		$content .= "<tr><td>";
		$content .= $rListCompos->componame;
		$content .= "</td><td>";
		$content .= "<a href=?module=compos&action=listSignedup&compo=$rListCompos->ID>";
		$content .= lang("Signup", "compos");
		$content .= "</a></td></tr>\n";
	} // End while
	$content .= "</table>\n\n";

}

elseif($action == "listSignedup" && isset($compo)) {
	// First, get information about this compo
	$qCompoInfo = db_query("SELECT * FROM ".$sql_prefix."_compos WHERE ID = '".db_escape($compo)."'");
	$rCompoInfo = db_fetch($qCompoInfo);

	$qListSignedup = db_query("SELECT * FROM ".$sql_prefix."_compoSignup WHERE compoID = '".db_escape($compo)."'");
	$content .= "<table>\n";
	while($rListSignedup = db_fetch($qListSignedup)) {
		$content .= "<tr><td>\n";
		// Get info based on compotype
		switch($rCompoInfo->type) {
			case "clan":
				$qGetClaninfo = db_query("SELECT * FROM ".$sql_prefix."_groups WHERE ID = '".db_escape($rListSignedup->clanID)."'");
				$rGetClaninfo = db_fetch($qGetClaninfo);
				$content .= $rGetClaninfo->groupname;
				break;
			case "1on1":
			case "FFA":
				$qGetUserinfo = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '".db_escape($rListSignedup->userID)."'");
				$rGetUserinfo = db_fetch($qGetUserinfo);
				$content .= $rGetUserinfo->nick;
				break;
		} // End switch
		$content .= "</td></tr>\n\n";
	} // End while
	$content .= "</table>\n";

	if($signup == 1){
		$content .= "<form method=POST action=?module=compos&action=signup&compo=$compo>\n";
		switch($rCompoInfo->type) {
			case "clan":
				$content .= "<select name=clanID>";
				$qListMyClans = db_query("SELECT groups.groupname,groups.ID FROM ".$sql_prefix."_groups groups 
					JOIN ".$sql_prefix."_group_members members ON members.groupID=groups.ID 
					WHERE members.userID = '$sessioninfo->userID' AND access IN ('Write', 'Admin')
					and groupType = 'clan'");
				while($rListMyClans = db_fetch($qListMyClans)) {
					$content .= "<option value='$rListMyClans->ID'>$rListMyClans->groupname</option>\n";
				} // End while
				$content .= "</select>\n";
				break; // End case clan
			case "1on1":
				// Don't do anything, just display signup-button
				break;
			case "FFA":
				// Don't do anything, just display signup-button
				break;

		} // End switch
		$content .= "<input type=submit value='".lang("Sign me up", "compos")."'>\n";
		$content .= "</form>";
	} // End if(signup == 1)
	elseif($signup == 2) {
		$content .= "<form method=POST action=?module=compos&action=removeSignup&compo=$compo>";
		switch($rCompoInfo->type) {
			case "clan":
				$content .= "<input type=hidden name=clanID value='$rCheckSignup->clanID'>\n";
				break;
			default:
				// Default, don't do anything, use userID
				break;
		} // End switch
		$content .= "<input type=submit value='".lang("Remove me", "compos")."'>\n";
		$content .= "</form>";
	} // End elseif(signup == 2)
} // End elseif action == listSignedup


elseif($action == "signup" && isset($compo)) {
	$qCompoInfo = db_query("SELECT * FROM ".$sql_prefix."_compos WHERE ID = '".db_escape($compo)."'");
	$rCompoInfo = db_fetch($qCompoInfo);
	
	switch($rCompoInfo->type) {
		case "clan":
			$clanID = $_POST['clanID'];
			$acl = acl_access("grouprights", $clanID, "", $sessioninfo->userID);
			if($acl == 'Admin' || $acl == 'Write') {
				db_query("INSERT INTO ".$sql_prefix."_compoSignup SET
					compoID = '".db_escape($compo)."',
					clanID = '".db_escape($clanID)."'");
			} // End if acl == Admin||Write
			break; // Break case clan
		case "1on1":
			// Signing on user
			#die("
		case "FFA":
			// Signing on user
			db_query("INSERT INTO ".$sql_prefix."_compoSignup SET
				compoID = '".db_escape($compo)."',
				userID = '".db_escape($sessioninfo->userID)."'");
			break;
	} // End switch
	header("Location: ?module=compos&action=listSignedup&compo=$compo");

} // End action == signup

elseif($action == "removeSignup" && isset($compo)) {
        $qCompoInfo = db_query("SELECT * FROM ".$sql_prefix."_compos WHERE ID = '".db_escape($compo)."'");
        $rCompoInfo = db_fetch($qCompoInfo);

	switch($rCompoInfo->type) {
		case "clan":
			$clanID = $_POST['clanID'];
			$acl = acl_access ("grouprights", $clanID, "", $sessioninfo->userID);
			if($acl == 'Admin' || $acl == 'Write') {
				db_query("DELETE FROM ".$sql_prefix."_compoSignup
					WHERE compoID = '".db_escape($compo)."'
					AND clanID = '".db_escape($clanID)."'");
			} // End if acl == Admin||Write
			break;
		case "1on1":
			// Remove user
		case "FFA":
			// Remove user
			db_query("DELETE FROM ".$sql_prefix."_compoSignup WHERE
				compoID = '".db_escape($compo)."' AND
				userID = '".db_escape($sessioninfo->userID)."'");
			break;
	} // End switch

	header("Location: ?module=compos&action=listSignedup&compo=$compo");
} // End action == removeSignup
