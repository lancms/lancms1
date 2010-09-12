<?php

$action = $_GET['action'];
$forum = $_GET['forum'];

if(empty($action)) {
	$content .= "<table>";
	$qFindForums = db_query("SELECT * FROM GO_forums WHERE eventID = '$sessioninfo->eventID'");
	while($rFindForums = db_fetch($qFindForums)) {
		$link_start = "<a href=?module=forum&action=viewForum&forum=$rFindForums->ID>";
		$content .= "<tr><td>";
		$content .= $link_start.$rFindForums->name."</a>";
		$content .= "</td><td>";
		$content .= $link_start.$rFindForums->description."</a>";
		$content .= "</td></tr>";
	} // End rFindForums
	$content .= "</table>";
} // End if empty(action)

elseif($action == "viewForum" && isset($forum)) {
	if(acl_access("forum", $forum, $sessioninfo->eventID)) $content .= "<a href=?module=forum&action=newThread&forum=$forum>".lang("Start new thread", "forum")."</a>";	
	$content .= "<table>";
	$qFindThreads = db_query("SELECT * FROM GO_forumThreads WHERE forumID = '".db_escape($forum)."' AND threadDeleted = 0 ORDER BY lastPost DESC");
	while($rFindThreads = db_fetch($qFindThreads)) {
		$link_start = "<a href=?module=forum&action=viewThread&thread=$rFindThreads->ID>";
		$content .= "<tr><td>";
		$content .= $link_start.$rFindThreads->threadTopic."</a>";
		$content .= "</td></tr>";
	} // End while
	$content .= "</table>";
} // elseif action = viewForum

elseif($action == "newThread" && isset($forum)) {

	$content .= "<table>";
	$content .= "<form method=POST action=?module=forum&action=doNewThread&forum=$forum>\n";
	$content .= "<tr><td>";
	$content .= lang("Thread name", "forum");
	$content .= "</td><td>";
	$content .= "<input type=text name=threadName>";
	$content .= "</td></tr>";
	$content .= "<tr><td>";
	$content .= lang("Thread content", "forum");
	$content .= "</td><td>";
	$content .= "<textarea name=threadContent cols=60 rows=10></textarea>";
	$content .= "</td></tr>";
	$content .= "<tr><td></td><td>";
	$content .= "<input type=submit value='".lang("Add thread", "forum")."'>";
	$content .= "</td></tr>";
	$content .= "</table>";

}

elseif($action == "doNewTread" && isset($forum)) {
	$threadName = $_POST['threadName'];
	$threadContent = $_POST['threadContent'];

	$checkRights = 
