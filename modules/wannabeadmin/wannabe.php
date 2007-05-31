<?php

$eventID = $sessioninfo->eventID;
$action = $_GET['action'];
$acl_access = acl_access("wannabeadmin", "", $eventID);




if($action == "adminWannabe")
{
	/* Adminlist for wannabe-actions */
	
	if($acl_access == "Admin")
	{
		// User has wannabe adminrights
		
	} // End acl_access = Admin
	
	if($acl_access == 'Write' || $acl_access == 'Admin')
	{
		// User has wannabe write-access (may see and write comments)
		
	} // End acl_access > Write 
	
	
} // End if action == "adminWannabe"