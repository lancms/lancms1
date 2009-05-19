<?php

$design_head .= "<link rel='stylesheet' href='templates/shared/useradmin.css' type='text/css' />";

// FIXME: global acl as eventid, confused, still, am.
if (acl_access ("userAdmin", "", $sessioninfo->eventID) == 'No')
{
	header ('Location: index.php?emsg="No access"');
	die ();
}
// else, got atleast read-access to userAdmin:

$action = $_GET['action'];

if (!isset ($action) or empty ($action))
{
	$content .= "<h2>".lang ("User administration", "useradmin")."</h2>";
	$content .= "<form action='index.php' method='GET'>";
	$content .= "<input type='hidden' name='module' value='useradmin' />";
	$content .= "<input type='hidden' name='action' value='listall' />";
	$content .= "<input type='submit' value='".lang ("View all users", "useradmin")."' />";
	$content .= "</form>";
	$content .= "<h3>Regular search</h3>";
	$content .= "Searches for nick, first or last name and email address";
	$content .= "<form>";
	$content .= "<input type='text' />";
	$content .= " <input type='submit' value='".lang ("Search", "useradmin")."' />";
	$content .= "</form>";
//	$content .= "<h3>Detailed search</h3>";
//	$content .= "Search for users with tickets for a specific event";

}
elseif ($action == 'listall')
{
	$content .= "<h2>List of all users</h2>";
	$content .= "<a href='index.php?module=useradmin'>Back to user administration</a>";
	$users = user_getall ();

	$content .= "<table class='userlist'>";
	$content .= "<tr>";
	$content .= "<th>ID</th";
	$content .= "<th>Username</th";
	$content .= "<th>Firstname</th";
	$content .= "<th>Lastname</th";
	$content .= "<th>Email</th";
	$content .= "<th>Address</th";
	$content .= "<th>Postnumber</th";
	$content .= "<th>Cellphonee</th";
	$content .= "</tr>";

	$ucount = 1;
	foreach ($users as $ui)
	{

		$onclick = "onClick='location.href=\"index.php?module=useradmin&action=details&userid=".$ui->ID."\"'";

		if ($ui->globaladmin > 0)
		{
			$content .= "<tr ".$onclick." class='userrow0'>";
		}
		else
		{
			$content .= "<tr ".$onclick." class='userrow".$ucount."'>";
		}

		$content .= "<td>".$ui->ID."</td>";
		$content .= "<td>".$ui->nick."</td>";
		$content .= "<td>".$ui->firstName."</td>";
		$content .= "<td>".$ui->lastName."</td>";
		$content .= "<td>".$ui->EMail."</td>";
		$content .= "<td>".$ui->street."</td>";
		$content .= "<td>".$ui->postnumber."</td>";
		$content .= "<td>".$ui->cellphone."</td>";

		$content .= "</tr>\n";

		$ucount++;
		if ($ucount == 3)
		{
			$ucount = 1;
		}
	}

	$content .= "</table>";

}
elseif ($action == 'details')
{
	$id = $_GET['userid'];
	if (!isset ($id) or empty ($id))
	{
		header ('Location: index.php?emsg=Missing userid');
		die ();
	}
	if (!user_exists ($id))
	{
		$content .= "<h2 class='userred'>No user with ID ".$id."</h2>";

	}
	else
	{
		$content .= "<h2>User details: ".display_username ($id)."</h2>";
		$content .= "<a href='javascript:history.back ()'>Back</a>";
	}
}
else
{
#	header ('Location: index.php?emsg="No such action"');
#	die ();
}
