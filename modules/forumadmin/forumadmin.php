<?php

$action = $_GET['action'];
$acl = acl_access("forum", "", $sessioninfo->eventID);
if($acl != 'Admin') die("No access to forumadmin");

$forumID = $_GET['forumID'];

if(!isset($action) || ($action == "editForum" && isset($_GET['forumID']))) {
	

	$qFindForums = db_query("SELECT * FROM ".$sql_prefix."_forums WHERE eventID = '$sessioninfo->eventID'");
	$content .= "<table>";
	while($rFindForums = db_fetch($qFindForums)) {
		$content .= "<tr><td class=tdLink onClick='location.href=\"?module=forumadmin&action=editForum&forumID=$rFindForums->ID\"'>$rFindForums->name</td>";
		if($rFindForums->disabled == 0) $lang_text = lang("Enabled");
		else $lang_text = lang("Disabled");
		$content .= "<td><a href=?module=forumadmin&action=enableddisabled&forumID=$rFindForums->ID>$lang_text</a>";
		$content .= "</td></tr>\n";
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

elseif($action == "enableddisabled" && isset($forumID)) {
	$qFindForum = db_query("SELECT * FROM ".$sql_prefix."_forums WHERE ID = '".db_escape($forumID)."' AND eventID = '$sessioninfo->eventID'");
	$rFindForum = db_fetch($qFindForum);

	if($rFindForum->disabled == 1) $next_status = 0;
	else $next_status = 1;

	db_query("UPDATE ".$sql_prefix."_forums SET disabled = '$next_status' WHERE ID = '$rFindForum->ID'");
	$log_new['forumName'] = $rFindForum->name;
	$log_new['status'] = $next_status;
	$log_old['status'] = $rFindForum->disabled;

	log_add("forumadmin", "enabledisable", serialize($log_new), serialize($log_old));
	header("Location: ?module=forumadmin");
}
