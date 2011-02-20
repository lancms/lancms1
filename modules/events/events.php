<?php

$action = $_GET['action'];
$eventID = $_GET['eventID'];


if($action == "setCurrentEvent" && isset($eventID))
{
	$escape = db_escape($eventID); // Prepare it for SQL

	// Update the session-table
	db_query("UPDATE ".$sql_prefix."_session SET
		eventID = '$escape'
		WHERE sID = '".$_COOKIE[$lancms_session_cookie]."'");

	// Goto $_GET['gotomodule'] if exists
	if(!empty($_GET['gotomodule']))
		header("Location: index.php?module=".$_GET['gotomodule']);
	// Return to referer if it exists
	elseif(!empty($_SERVER['HTTP_REFERER']))
		header("Location: ".$_SERVER['HTTP_REFERER']);
	else // If no referer, go back to index. Should probably never happen...
		header("Location: index.php");
}
