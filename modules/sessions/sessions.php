<?php

if(acl_access("globaladmin", "", 0) != "Admin") die("You do not have propper rights!");


$action = $_GET['action'];

if(empty($action)) {

	$content .= "<table>";
	$qFindSessions = db_query("SELECT * FROM ".$sql_prefix."_session");
	while($rFindSessions = db_fetch($qFindSessions)) {
		$content .= "<tr><td>\n";
		$content .= $rFindSessions->userIP;
		$content .= "</td><td>\n";
		$content .= $rFindSessions->userAgent;
		$content .= "</td><td>\n";
		$content .= $rFindSessions->userID;
		$content .= "</td><td>\n";
		$content .= $rFindSessions->eventID;
		$content .= "</td><td>\n";
		$content .= date("Y-m-d G:m:s", $rFindSessions->lastVisit);
		$content .= "</td></tr>\n\n";
	} // End while
	$content .= "</table>";

}
