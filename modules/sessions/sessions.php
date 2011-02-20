<?php

if(acl_access("globaladmin", "", 0) != "Admin") die("You do not have propper rights!");


$action = $_GET['action'];

if(empty($action)) {

	$content .= "<h2>".lang ("Sessions", "sessions")."</h2>";
	$content .= "<table>\n";
	$content .= "<tr>\n";
	$content .= "<th>".lang ("IP-address", "sessions")."</th>\n";
	$content .= "<th>".lang ("Useragent", "sessions")."</th>\n";
	$content .= "<th>".lang ("User", "sessions")."</th>\n";
	$content .= "<th>".lang ("Event", "sessions")."</th>\n";
	$content .= "<th>".lang ("Last visit", "sessions")."</th>\n";
	$content .= "</tr>\n";
	$qFindSessions = db_query("SELECT * FROM ".$sql_prefix."_session");

	$count = 1;
	while($rFindSessions = db_fetch($qFindSessions)) {
		$content .= "<tr class='row$count'><td>\n";
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

		if ($count == 2)
		{
			$count = 1;
		}
		else
		{
			$count = 2;
		}
	} // End while
	$content .= "</table>\n";

}
