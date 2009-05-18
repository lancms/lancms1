<?php

// check if user is logged in
if ($sessioninfo->userID <= 1)
{
	header ('Location: index.php');
	die ();
}

$action = $_GET['action'];

if ($action == 'password')
{
	$content .= "<h2>".lang ("Change password", "edituserinfo")."</h2>";
	$err = $_GET['err'];
	if ($err == 1)
	{
		$content .= "<font color='red'><b>".lang ("Passwords did not match", "edituserinfo")."</b></font>";
	}
	elseif ($err == 2)
	{
		$content .= "<font color='red'><b>".lang ("You need to fill in both fields", "edituserinfo")."</b></font>";
	}
	$content .= "<form action='index.php?module=edituserinfo&action=savepassword' method='POST'>";
	$content .= "<input name='npass1' type='password' /> ".lang ("New password", "edituserinfo")."<br />";
	$content .= "<input name='npass2' type='password' /> ".lang ("Confirm new password", "edituserinfo")."<br />";
	$content .= "<input type='submit' value='".lang ("Change password", "edituserinfo")."' />";
	$content .= "</form>";
}
elseif ($action == 'savepassword')
{
	$npass1 = $_POST['npass1'];
	$npass2 = $_POST['npass2'];
	if (!isset ($npass1) or !isset ($npass2) or empty ($npass1) or empty ($npass2))
	{
		header ('Location: index.php?module=edituserinfo&action=password&err=2');
		die ();
	}
	$md5p1 = md5 ($npass1);
	$md5p2 = md5 ($npass2);
	if ($md5p1 != $md5p2)
	{
		header ('Location: index.php?module=edituserinfo&action=password&err=1');
		die ();
	}

	// else, set new password:
	$oldpass = user_getpass ($sessioninfo->userID);
	user_setpass ($sessioninfo->userID, $md5p1);
	log_add (8, $md5p1, $oldpass);

	$content .= "<h2>".lang ("Password changed", "edituserinfo")."</h2>";
}

elseif($action == "editUserinfo" && isset($_GET['user'])) {
	// Edit userinfo
	$user = $_GET['user'];
	$userAdmin_acl = acl_access("userAdmin", "", 1);
	if($user == $sessioninfo->userID);
	elseif($userAdmin_acl == 'Admin' || $userAdmin_acl == 'Write');
	else die(lang("Not access to edit userinfo"));

	$qGetUserinfo = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '".db_escape($user)."'");
	$rGetUserinfo = db_fetch($qGetUserinfo);
	$content .= "<table>\n\n";
	$content .= "<form method=POST action=?module=edituserinfo&action=doEditUserinfo&user=$user>\n";
	$content .= "<tr><td><input type=text name=firstName value='$rGetUserinfo->firstName'>\n";
	$content .= "</td><td>".lang("Firstname", "edituserinfo")."</td></tr>\n";
	$content .= "<tr><td><input type=text name=lastName value='$rGetUserinfo->lastName'>\n";
	$content .= "</td><td>".lang("Surname", "edituserinfo")."</td></tr>\n";
	$content .= "<tr><td><input type=submit value='".lang("Save userinfo", "edituserinfo")."'>\n";
	$content .= "</td><td></td></tr>";
	$content .= "</form></table>\n\n";

} // End action == editUserinfo

elseif($action == "doEditUserinfo" && isset($_GET['user'])) {
	$user = $_GET['user'];
	$userAdmin_acl = acl_access("userAdmin", "", 1);
	if($user == $sessioninfo->userID);
	elseif($userAdmin_acl == 'Admin' || $userAdmin_acl == 'Write');
	else die(lang("Not access to edit userinfo", "edituserinfo"));

	// Get-parameters
	$user = $_GET['user'];

	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];

	$qGetUserinfo = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '".db_escape($user)."'");
	$rGetUserinfo = db_fetch_assoc($qGetUserinfo);
	log_add(9, serialize($_POST), serialize($rGetUserinfo));

	header("Location: ?module=edituserinfo&action=editUserinfo&user=$user&edited=success");
	

} // End elseif action == doEditUserinfo
else
{
	// no action defined? ship user back to start.
	header ('Location: index.php');
	die ();
}
