<?php

$action = $_GET['action'];
$acl = acl_access("forum", "", $sessioninfo->eventID);
if($acl != 'Admin') die("No access to forumadmin");

$forumID = $_GET['forumID'];

if(!isset($action) || ($action == "editForum" && isset($_GET['forumID']))) {
	

	$qFindForums = db_query("SELECT * FROM ".$sql_prefix."_forums WHERE eventID = '$sessioninfo->eventID'");
	$content .= "<table>";
	while($rFindForums = db_fetch($qFindForums)) {
		$content .= "<tr><td class=tdLink onClick='location.href=\"?module=forumadmin&action=editForum&forumID=$rFindForums->ID\"'>$rFindForums->name</td></tr>\n";
	} // End while
	$content .= "</table>\n\n";

	if($action == "editForum") {
		$qFindForum = db_query("SELECT * FROM ".$sql_prefix."_forums WHERE eventID = '$sessioninfo->eventID' 
			AND ID = '".db_escape($_GET['forumID'])."'");
		$rFindForum = db_fetch($qFindForum);
		$content .= "<form method=POST action='?module=forumadmin&action=editForum&forumID=".$_GET['forumID']."'>\n";
		$submit_text = lang("Change forum");
	}
	else {
		$content .= "<form method=POST action='?module=forumadmin&action=addForum'>\n";
		$submit_text = lang("Add forum");
	}

	$content .= "<input type=text name='name' value='$rFindForum->name'>".lang("Forum name");
	$content .= "<br /><input type=text name='description' value='$rFindForum->description'>".lang("Forum description");
	$content .= "<br /><input type=submit value='".$submit_text."'>";
	$content .= "</form>";

} // End if !isset(action)

elseif($action == "addForum" && !empty($_POST['name'])) {

	$name = $_POST['name'];
	$description = $_POST['description'];

	db_query("INSERT INTO ".$sql_prefix."_forums SET 
		eventID = '$sessioninfo->eventID',
		name = '".db_escape($name)."',
		description = '".db_escape($description)."'");
	log_add("forumadmin", "addForum", serialize($_POST));
	header("Location: ?module=forumadmin");
} // End action == addForum
