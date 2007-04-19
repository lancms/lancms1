<?php

$action = $_GET['action'];
$eventID = $_GET['eventID'];


if($action == "setCurrentEvent" && isset($eventID))
{
	$escape = db_escape($eventID); // Prepare it for SQL
	
	// Update the session-table
	db_query("UPDATE ".$sql_prefix."_session SET 
		eventID = '$escape' 
		WHERE sID = '".$_COOKIE[$osgl_session_cookie]."'");
		
	// FIXME: This should probably be a function to return to referer
	header("Location: index.php");
}