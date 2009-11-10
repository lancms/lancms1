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
	log_add ("edituser", "setNewPass", $md5p1, $oldpass);

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
	$rGetUserinfo = db_fetch_assoc($qGetUserinfo);
	$content .= "<table>\n\n";
	$content .= "<form method=POST action=?module=edituserinfo&action=doEditUserinfo&user=$user>\n\n";

	for($i=0;$i<count($userprefs);$i++) {
		if($userprefs[$i]['group_pref'] != 1 || $userprefs[$i]['group_pref_begin'] == 1) $content .= "<tr><td>";
		$content .= lang($userprefs[$i]['displayName'], "edituserinfo_prefs");
		if($userprefs[$i]['mandatory'] && !empty($userprefs[$i]['displayName'])) $content .= " <font color=red>*</font>";
		if($userprefs[$i]['group_pref'] != 1 || $userprefs[$i]['group_pref_begin'] == 1) $content .= "</td><td>";

		$name = $userprefs[$i]['name'];
		if($userprefs[$i]['edit_userAdmin'] == 'Write' && ($userAdmin_acl != 'Admin' && $userAdmin_acl != 'Write')) $edit = FALSE;
		elseif($userprefs[$i]['edit_userAdmin'] == 'Admin' && $userAdmin_acl != 'Admin') $edit = FALSE;
		else $edit = TRUE;

		if($edit == TRUE) // Edit is enabled
		switch($userprefs[$i]['type']) {
			case "text":
				$content .= "<input type=text name='$name' value='$rGetUserinfo[$name]'>\n";
				break;
			case "dropdown":
				$content .= "<select name='$name'>\n";
				foreach($userprefs[$i]['dropdown_values'] AS $value => $displayname) {
					$content .= "<option value='$value'";
					if($rGetUserinfo[$name] == $value) $content .= " SELECTED";
					$content .= ">";
					if(is_numeric($displayname)) $content .= $displayname;
					else $content .= lang($displayname, "edituserinfo_prefs");
					$content .= "</option>\n";
				} // End foreach
				$content .= "</select>";
				break;
		} // End switch
		elseif($edit == FALSE)
			$content .= $rGetUserinfo[$name];
		if($userprefs[$i]['group_pref'] != 1 || $userprefs[$i]['group_pref_end'] == 1) $content .= "</td></tr>\n\n\n";
	} // End for
	$content .= "<tr><td><input type=submit value='".lang("Save", "edituserinfo")."'></td></tr>\n\n";
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

#	$firstName = $_POST['firstName'];
#	$lastName = $_POST['lastName'];

	$qGetUserinfo = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '".db_escape($user)."'");
	$rGetUserinfo = db_fetch_assoc($qGetUserinfo);
	for($i=0;$i<count($userprefs);$i++) {
		$name = $userprefs[$i]['name'];
		$value = $_POST[$name];
		if($userprefs[$i]['edit_userAdmin'] == 'Write' && ($userAdmin_acl != 'Admin' || $userAdmin_acl != 'Write'));
		elseif($userprefs[$i]['edit_userAdmin'] == 'Admin' && $userAdmin_acl != 'Admin');
		elseif($rGetUserinfo[$name] != $value) {
#			die("Not same on $rGet
			// User has changed this setting, change it in DB
			db_query("UPDATE ".$sql_prefix."_users SET
				$name = '$value' WHERE ID = '".db_escape($user)."'");
			$log['old'][] = $rGetUserinfo[$name];
			$log['new'][] = $value;
		} // End if oldvalue != newvalue

	} // End for

	log_add("editinfo", "doEditUserinfo", serialize($log['new']), serialize($log['old']));

	header("Location: ?module=edituserinfo&action=editUserinfo&user=$user&edited=success");


} // End elseif action == doEditUserinfo

elseif($action == "editPreferences" && isset($_GET['user'])) {
	$userID = $_GET['user'];
	$userAdmin_acl = acl_access("userAdmin", "", 1);
	if($userID == $sessioninfo->userID);
	elseif($userAdmin_acl == 'Admin' || $userAdmin_acl == 'Write');
	else die(lang("Not access to edit userinfo"));

	$content .= "<table><form method=POST action=?module=edituserinfo&action=doEditPreferences&user=$userID>";
	for($i=0;$i<count($userpersonalprefs);$i++) {

		$prefname = $userpersonalprefs[$i]['name'];
		$qFindPref = db_query("SELECT * FROM ".$sql_prefix."_userPreferences WHERE userID = '".db_escape($userID)."' AND name = '$prefname'");
		$rFindPref = db_fetch($qFindPref);

		$content .= "<tr><td>";
		switch($userpersonalprefs[$i]['type']) {
			case "checkbox":
				$content .= "<input type='checkbox' name='$prefname'";
				if($rFindPref->value == "on" || $userpersonalprefs[$i]['required_on']) $content .= " CHECKED";
				if($userpersonalprefs[$i]['required_on']) $content .= " DISABLED";
				$content .= ">";

		} // End switch

		$content .= "</td><td>";
		$content .= lang($userpersonalprefs[$i]['displayName'], "edituserinfo");
		$content .= "</td></tr>";

	} // End for
	$content .= "<tr><td></td><td><input type=submit value='".lang("Save", "edituserinfo")."'></tr>";
	$content .= "</form></table>";
} // end elseif action = editPreferences

elseif($action == "doEditPreferences" && isset($_GET['user'])) {
	$userID = $_GET['user'];
	$userAdmin_acl = acl_access("userAdmin", "", 1);
	if($userID == $sessioninfo->userID);
	elseif($userAdmin_acl == 'Admin' || $userAdmin_acl == 'Write');
	else die(lang("Not access to edit userinfo"));

	for($i=0;$i<count($userpersonalprefs);$i++) {
		$prefname = $userpersonalprefs[$i]['name'];
		$POST = $_POST[$prefname];
		if($userpersonalprefs[$i]['required_on'] == 1) $POST = "on";
		$qFindPref = db_query("SELECT * FROM ".$sql_prefix."_userPreferences WHERE userID = '".db_escape($userID)."' AND name = '$prefname'");
		$numFindPref = db_num($qFindPref);

		if($numFindPref == 0) {
			db_query("INSERT INTO ".$sql_prefix."_userPreferences
				SET userID = '".db_escape($userID)."',
				name = '$prefname',
				value = '".db_escape($POST)."'");
		} // End if
		else
			db_query("UPDATE ".$sql_prefix."_userPreferences SET value = '".db_escape($POST)."'
				WHERE userID = '".db_escape($userID)."'
				AND name = '$prefname'");

	} // End for
	header("Location: ?module=edituserinfo&action=editPreferences&user=$userID&change=success");

}
else
{
	// no action defined? ship user back to start.
	header ('Location: index.php');
	die ();
}
