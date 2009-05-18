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
else
{
	// no action defined? ship user back to start.
	header ('Location: index.php');
	die ();
}
