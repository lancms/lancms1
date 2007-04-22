<?php

$action = $_GET['action'];
$page = $_GET['page'];

$acl_access = acl_access("static", $page, $sessioninfo->eventID);

// Die if user does not have access at all
if($acl_access == "No" || empty($acl_access))
	die("Sorry, you do not have access to this!");

if($action == "viewPage" && !empty($page))
{
	/* This function will view the requested page. */
	$qViewPage = db_query("SELECT * FROM ".$sql_prefix."_static WHERE ID = '".db_escape($page)."'");
	$rViewPage = db_fetch($qViewPage);
	
	/* FIXME: Should probably be.... more... */
	$content .= $rViewPage->page;
	
	db_query("UPDATE ".$sql_prefix."_static SET pageViews = pageViews + 1 WHERE ID = ".db_escape($page));
} // End if action = viewPage