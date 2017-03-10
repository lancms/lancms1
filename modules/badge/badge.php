<?php

if(acl_access("eventadmin", "", $sessioninfo->eventID) != ('Admin' || 'Write')) die("No access");
$action = $_GET['action'];


if($action == "listbadges") {

	// List all users that are member of some event-related group
	$qFindUsersWithAccess = db_query("SELECT DISTINCT gm.userID FROM ".$sql_prefix."_group_members gm
	      JOIN ".$sql_prefix."_ACLs acl ON acl.groupID=gm.groupID
			      WHERE acl.access != 'No' AND acl.eventID = '$sessioninfo->eventID'");

	$content .= "<table>";
	while($rFindUsersWithAccess = db_fetch($qFindUsersWithAccess)) {
		$user = UserManager::getInstance()->getUserByID($rFindUsersWithAccess->userID);
#		print_r($user);
		$content .= "<tr><td>".$user->getFirstName() . " " . $user->getLastName() . "</td>";
		$qFindBadge = db_query("SELECT * FROM ".$sql_prefix."_membershipCard WHERE eventID = '$sessioninfo->eventID' AND userID = '$rFindUsersWithAccess->userID'");
		if(db_num($qFindBadge) >= 1) {
			$content .= "<td>Found Badge</td>";
			$rFindBadge = db_fetch($qFindBadge);
		} // End if db_num == 0
		else {
			$content .= "<td><a href=?module=badge&action=createBadge&user=$rFindUsersWithAccess->userID>Create badge</a></td>";
	
		} // End else
		$content .= "</tr>\n\n";
	} // End while


	$content .= "</table>";

} // end action == listbadges


elseif ($action == "createBadge" && isset($_GET['user'])) {

	$userID = db_escape($_GET['user']);

	$content .= "<form method=POST action='?module=badge&action=doCreateBadge&user=$userID'>\n";
	$content .= "<input type=input name=badgeID>\n";
	$content .= "<input type=submit value='"._("Add badge")."'>\n";
	$content .= "</form>";



} // end elseif action == createBadge


elseif ($action == "doCreateBadge" && isset($_GET['user'])) {
	$userID = db_escape($_GET['user']);
	$badgeID = db_escape($_POST['badgeID']);

	db_query("INSERT INTO ".$sql_prefix."_membershipCard SET userID = '$userID', cardID = '$badgeID', eventID = '$sessioninfo->eventID'");
	$log['badgeID'] = $badgeID;
	$log['userID'] = $userID;
	log_add("badge", "createBadge", serialize($log));
	header("Location: ?module=badge&action=listbadges");
}
