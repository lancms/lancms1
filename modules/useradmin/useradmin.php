<?php

$design_head .= "<link rel='stylesheet' href='templates/shared/useradmin.css' type='text/css' />";

$acl_useradmin = acl_access("userAdmin", "", 1);

// Checking if no access to useradmin
if ($acl_useradmin == 'No')
{
	header ('Location: index.php?emsg="No access"');
	die ();
}
// else, got atleast read-access to userAdmin:

$action = $_GET['action'];

if (!isset ($action) or empty ($action))
{
	$content .= "<h2>".lang ("User administration", "useradmin")."</h2>";
	$content .= "<form action='index.php' method='GET'>\n";
	$content .= "<input type='hidden' name='module' value='useradmin' />\n";
	$content .= "<input type='hidden' name='action' value='listall' />\n";
	$content .= "<input type='submit' value='".lang ("View all users", "useradmin")."' />\n";
	$content .= "</form>\n\n";
	$content .= "<h3>Regular search</h3>\n";
	$content .= "Searches for nick, first or last name and email address\n";
	$content .= "<form method='GET' action='index.php'>\n";
        $content .= "<input type='hidden' name='module' value='useradmin' />\n";
        $content .= "<input type='hidden' name='action' value='search' />\n";
	$content .= "<input type='text' name='search' />\n";
	$content .= " <input type='submit' value='".lang ("Search", "useradmin")."' />\n";
	$content .= "</form>\n\n";
//	$content .= "<h3>Detailed search</h3>";
//	$content .= "Search for users with tickets for a specific event";

}
elseif ($action == 'listall' || $action == 'search')
{
	$content .= "<h2>".lang("List of all users", "useradmin")."</h2>";
	$content .= "<a href='index.php?module=useradmin'>".lang("Back to user administration", "useradmin")."</a>";

	if($action == "listall") $users = user_getall ();
	else {
		$s = db_escape($_GET['search']);
		$qFindUsers = db_query("SELECT * FROM ".$sql_prefix."_users 
			WHERE ID = '$s'
			OR nick LIKE '%$s%'
			OR firstName LIKE '%$s%'
			OR lastName LIKE '%$s%'
			OR EMail LIKE '%$s%'
		");
		while($rFindUsers = db_fetch($qFindUsers)) {
			$users[] = $rFindUsers;
		} // End while
	} // End else

	$content .= "<table class='userlist'>";
	$content .= "<tr>";
	$content .= "<th>".lang("ID", "useradmin")."</th>";
	$content .= "<th>".lang("Username", "useradmin")."</th>";
	$content .= "<th>".lang("Firstname", "useradmin")."</th>";
	$content .= "<th>".lang("Lastname", "useradmin")."</th>";
	$content .= "<th>".lang("Email", "useradmin")."</th>";
	$content .= "<th>".lang("Address", "useradmin")."</th>";
	$content .= "<th>".lang("Postnumber", "useradmin")."</th>";
	$content .= "<th>".lang("Cellphone", "useradmin")."</th>";
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
		$content .= "<td>".$ui->postNumber."</td>";
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
		$content .= "<h2 class='userred'>".lang("No user with ID", "useradmin")." ".$id."</h2>";

	}
	else
	{
		$content .= "<h2>User details: ".display_username ($id)."</h2>";
		if($acl_useradmin == 'Admin' || $acl_useradmin == 'Write') {
			$content .= "<a href=?module=edituserinfo&action=editUserinfo&user=$id>".lang("Edit userinfo", "useradmin")."</a> <br />";
		}
		$content .= "<a href='javascript:history.back ()'>".lang("Back", "useradmin")."</a>";
	}
}
else
{
#	header ('Location: index.php?emsg="No such action"');
#	die ();
}
