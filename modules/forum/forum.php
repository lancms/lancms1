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

elseif($action == "doNewThread" && isset($_GET['forum'])) {
	$threadName = $_POST['threadName'];
	$threadContent = $_POST['threadContent'];
	$forum = $_GET['forum'];
	db_query("INSERT INTO ".$sql_prefix."_forumThreads SET 
		threadStarter = '".$sessioninfo->userID."',
		forumID = '".db_escape($forum)."',
		threadTopic = '".db_escape($threadName)."'");
	$qFindThreadID = db_query("SELECT * FROM ".$sql_prefix."_forumThreads WHERE threadStarter = '".$sessioninfo->userID."' ORDER BY ID DESC LIMIT 0,1");
	$rFindThreadID = db_fetch($qFindThreadID);
	db_query("INSERT INTO ".$sql_prefix."_forumPosts 
		SET postAuthor = '".$sessioninfo->userID."',
		threadID = '".$rFindThreadID->ID."',
		postTimestamp = '".time()."',
		postContent = '".db_escape($threadContent)."'");
	db_query("UPDATE ".$sql_prefix."_users SET forumPosts = forumPosts + 1 WHERE ID = '$sessioninfo->userID'");

	$log_new['threadName'] = $threadName;
	$log_new['threadContent'] = $threadContent;
	$log_new['forum'] = $forum;
	log_add("forum", "newThread", serialize($log_new));
	
	header("Location: ?module=forum&action=viewThread&thread=$rFindThreadID->ID");
}


elseif($action == "viewThread" && isset($_GET['thread'])) {
	$thread = $_GET['thread'];
	$qFindThread = db_query("SELECT * FROM ".$sql_prefix."_forumThreads WHERE ID = '".db_escape($thread)."'");
	$rFindThread = db_fetch($qFindThread);

	$content .= "<table>";
	$content .= "<tr><th>";
	$content .= $rFindThread->threadTopic;
	$content .= "</th></tr>";

	$content .= "</table>";
	$content .= "<table border=1>";

	$qFindPosts = db_query("SELECT * FROM ".$sql_prefix."_forumPosts WHERE threadID = '".db_escape($thread)."'");
	while($rFindPosts = db_fetch($qFindPosts)) {
		$content .= "<tr><td>";
		$content .= nl2br($rFindPosts->postContent);
		$content .= "</td><td>";
		$qFindUser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '$rFindPosts->postAuthor' LIMIT 0,1");
		$rFindUser = db_fetch($qFindUser);
		$content .= lang("User:", "forum");
		$content .= $rFindUser->nick;
		$content .= "<br />";
		$content .= lang("Name:", "forum");
		$content .= $rFindUser->firstName." ".$rFindUser->lastName;
		$content .= "</td></tr>";
	} // End while
	$content .= "</table>";
	$content .= "<br />";
	$content .= "<a href=?module=forum&action=newPost&thread=$thread>".lang("New reply", "forum")."</a>";
}

elseif($action == "newPost" && isset($_GET['thread'])) {
	$thread = $_GET['thread'];

	$content .= "<form method=POST action=?module=forum&action=doNewPost&thread=$thread>";
	$content .= "<textarea name=postContent rows=10 cols=60></textarea>";
	$content .= "<br /><input type=submit value='".lang("Add reply", "forum")."'>";
	$content .= "</form>";
}

elseif($action == "doNewPost" && isset($_GET['thread'])) {
	$thread = $_GET['thread'];
	$postContent .= $_POST['postContent'];

	db_query("INSERT INTO ".$sql_prefix."_forumPosts SET
		threadID = '".db_escape($thread)."',
		postAuthor = '$sessioninfo->userID',
		postTimestamp = '".time()."',
		postContent = '".db_escape($postContent)."'");
	
	db_query("UPDATE ".$sql_prefix."_users SET forumPosts = forumPosts + 1 WHERE ID = '$sessioninfo->userID'");

	$log_new['thread'] = $thread;
	$log_new['postcontent'] = $postContent;
	log_add("forum", "newpost", serialize($log_new));

	header("Location: ?module=forum&action=viewThread&thread=$thread");
}
else {
	$content .= "Error?";
}
