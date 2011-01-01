<?php


// First, delete all old sessions
$now = time();

$day_ago = $now - 86400;

db_query("DELETE FROM ".$sql_prefix."_session WHERE lastVisit < ".$day_ago);


if(empty($_COOKIE[$osgl_session_cookie]))
{
	$do = "create_session";

} // end if empty cookie

// Delete all sessions older than 24 hours


else // if cookie is set
{

	$test_exists = db_query("SELECT * FROM ".$sql_prefix."_session WHERE sID = '".$_COOKIE[$osgl_session_cookie]."'");
	if(db_num($test_exists) == 0)
	{
		// Cookie is set, but it is no longer valid
		// Delete cookie/set timeout in past
		setcookie($osgl_session_cookie, "", time()-10800);

		// Create a new session
		$do = "create_session";
	} // End if test_exists == 0

	else {
		// hopefully, since sID == PRIMARY KEY, this should be the same as db_num($test_exists) == 1. Update session
		db_query("UPDATE ".$sql_prefix."_session SET lastVisit = ".time()." WHERE sID = '".db_escape($_COOKIE[$osgl_session_cookie])."'");

	} // End else (if cookie is valid)

} // End if cookie is set



if($do == "create_session")
{
	// User is not logged in; generate a new seed
	$generate = md5(rand(0,9999999).microtime());

	$host = str_replace(".", "_",$_SERVER['SERVER_NAME']);

	// Find if servername matches any urls defined in eventAutoURL in events.
	// If it matches, use this event when creating session
#	$qFindAutoEventURL = db_query("SELECT ID FROM ".$sql_prefix."_events WHERE eventAutoURL LIKE '%$host%'
#		AND eventClosed = 0 AND eventPublic = 1");
#	$rFindAutoEventURL = db_fetch($qFindAutoEventURL);
	$FindAutoEventURL = config("hostname_".$host);

	if(empty($FindAutoEventURL)) {
		config("hostname_".$host, 1, 1); // Add the hostname-stuff to config, so it can be set
		$FindAutoEventURL = 1;
	}
	$sess_eventID = $FindAutoEventURL;	
#	if($rFindAutoEventURL->ID) $sess_eventID = $rFindAutoEventURL->ID;
#	else $sess_eventID = 1;

	db_query("INSERT INTO ".$sql_prefix."_session SET
		sID = '$generate',
		userIP = '".$_SERVER['REMOTE_ADDR']."',
		userAgent = '".$_SERVER['HTTP_USER_AGENT']."',
		lastVisit = '".time()."',
		eventID = '$FindAutoEventURL'");
	
	setcookie($osgl_session_cookie, $generate);

} // End if do == create_session



$query_session = db_query("SELECT * FROM ".$sql_prefix."_session WHERE sID = '".$_COOKIE[$osgl_session_cookie]."'");
$sessioninfo = db_fetch($query_session);
global $sessioninfo;

if(empty($sessioninfo->userID)) // Session is empty
	$sessioninfo->userID = 1; // Set session user to anonymous.
if(empty($sessioninfo->eventID) && $sess_eventID) 
	// Event is empty and we're creating a new session
	$sessioninfo->eventID = $sess_eventID;

// Create eventinfo with info about this event
$query_event = db_query("SELECT * FROM ".$sql_prefix."_events WHERE ID = '$sessioninfo->eventID'");
$eventinfo = db_fetch($query_event);
