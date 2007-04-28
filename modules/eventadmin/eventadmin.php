<?php

$action = $_GET['action'];
$eventID = $_SESSION['eventID'];

if(acl_access("eventadmin", "", $eventID) != "Admin")
	die("You are not admin!");
	
if(!isset($action))
{
	// No action specified. List all eventadmin tasks
	$content .= "<br><a href=?module=eventadmin&action=groupACLs>".lang("Accessrights", "eventadmin")."</a>\n";

} // End if !isset(action)


elseif($action == "groupACLs")
{
	// action to specify who has what rights on event
	$content .= "FIXME";
}