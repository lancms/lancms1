<?php

$action = $_GET['action'];
$forum = $_GET['forum'];

if(empty($action)) {
	$content .= "<table>";
	$qFindForums = db_query("SELECT * FROM ".$sql_prefix."_forums WHERE eventID = '$sessioninfo->eventID' AND disabled = 0");
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
	if(acl_access("forum", $forum, $sessioninfo->eventID) && $sessioninfo->userID > 1) $content .= "<a href=?module=forum&action=newThread&forum=$forum>".lang("Start new thread", "forum")."</a>";	
	$content .= "<table>";
	$qFindThreads = db_query("SELECT * FROM ".$sql_prefix."_forumThreads WHERE forumID = '".db_escape($forum)."' AND threadDeleted = 0 ORDER BY lastPost DESC");
	while($rFindThreads = db_fetch($qFindThreads)) {
		$link_start = "<a href=?module=forum&action=viewThread&thread=$rFindThreads->ID>";
		$content .= "<tr><td>";
		$content .= $link_start.$rFindThreads->threadTopic."</a>";
		$content .= "</td><td>";
		$qFindLastPost = db_query("SELECT * FROM ".$sql_prefix."_forumPosts WHERE threadID = '$rFindThreads->ID' ORDER BY postTimestamp DESC LIMIT 0,1");
		$rFindLastPost = db_fetch($qFindLastPost);
		$content .= lang("Last post by: ", "forum");
		$content .= user_profile($rFindLastPost->postAuthor);
		$content .= " ".date("Y-m-d H:m:s", $rFindLastPost->postTimestamp);
		$content .= "</td></tr>\n\n";
	} // End while
	$content .= "</table>";
} // elseif action = viewForum

elseif($action == "newThread" && isset($forum) && $sessioninfo->userID > 1) {


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

elseif($action == "doNewThread" && isset($_GET['forum']) && $sessioninfo->userID) {
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

	$content .= "<h3>".stripslashes ($rFindThread->threadTopic)."</h3>";
	
	
	$content .= "<table class='viewthread'>";

	$qFindPosts = db_query("SELECT * FROM ".$sql_prefix."_forumPosts WHERE threadID = '".db_escape($thread)."' ORDER BY postTimestamp ASC");
	while ($rFindPosts = db_fetch($qFindPosts))
	{
		$qFindAuthor = db_query (sprintf ("SELECT nick, CONCAT(firstName, ' ', lastName) AS name FROM %s_users WHERE ID=%s", $sql_prefix, $rFindPosts->postAuthor));
		$rFindAuthor = db_fetch ($qFindAuthor);
		$nick = $rFindAuthor->nick;
		$name = $rFindAuthor->name;

		$content .= "<tr><td>";
		$content .= stripslashes (nl2br ($rFindPosts->postContent));
		$content .= "</td>";
		$content .= "<td clasS='info'>";
		$content .= "<b>"._("Nick:")."</b> <a href='?module=profile&user=".$rFindPosts->postAuthor."'>$nick</a>";
		$content .= "<br />";
		$content .= "<b>"._("Name:")."</b> $name";
		$content .= "<br />";
		$content .= "<b>"._("Posted:")."</b> ".date("Y-m-d H:m:s", $rFindPosts->postTimestamp);
		$content .= "</td></tr>";
		unset ($nick);
		unset ($name);
	} // End while
	$content .= "</table>\n\n\n\n";
	$content .= "<br />";
	if($sessioninfo->userID > 1) $content .= "<a href=?module=forum&action=newPost&thread=$thread>".lang("New reply", "forum")."</a>";
}

elseif($action == "newPost" && isset($_GET['thread']) && $sessioninfo->userID > 1) {
	$thread = $_GET['thread'];

	$content .= "<form method=POST action=?module=forum&action=doNewPost&thread=$thread>";
	$content .= "<textarea name=postContent rows=10 cols=60></textarea>";
	$content .= "<br /><input type=submit value='".lang("Add reply", "forum")."'>";
	$content .= "</form>";
}

elseif($action == "doNewPost" && isset($_GET['thread']) && $sessioninfo->userID > 1) {
	$thread = $_GET['thread'];
	$postContent = $_POST['postContent'];

	db_query("INSERT INTO ".$sql_prefix."_forumPosts SET
		threadID = '".db_escape($thread)."',
		postAuthor = '$sessioninfo->userID',
		postTimestamp = '".time()."',
		postContent = '".db_escape($postContent)."'");
	
	db_query("UPDATE ".$sql_prefix."_users SET forumPosts = forumPosts + 1 WHERE ID = '$sessioninfo->userID'");
	db_query("UPDATE ".$sql_prefix."_forumThreads SET lastPost = '".time()."' WHERE ID = '".db_escape($thread)."'");

	$log_new['thread'] = $thread;
	$log_new['postcontent'] = $postContent;
	log_add("forum", "newpost", serialize($log_new));

	header("Location: ?module=forum&action=viewThread&thread=$thread");
}
else {
	$content .= "Error?";
}
