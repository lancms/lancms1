<?php

$action = $_GET['action'];


if($action == "register")
{
	$username = $_POST['username'];
	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];
	$EMail = $_POST['EMail'];
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];

	/* Check if the username is free */
	$qCheckUsername = db_query("SELECT nick FROM ".$sql_prefix."_users
		WHERE nick LIKE '".db_escape($username)."'");
	if(db_num($qCheckUsername) != 0)
	{
		$register_invalid = lang("Username already in use", "register");
		$username = FALSE;
	} // end check if username is free

	/* Check if the passwords match */
	elseif ( $pass1 != $pass2)
	{
		$register_invalid = lang("Passwords does not match", "register");
		$pass1 = FALSE;
		$pass2 = FALSE;
	} // End check if passwords match

	/* Check if firstName is valid */
	elseif(config("register_firstname_required", 0))
	{
		if(strlen($firstName) <=2)
		{
			$firstName = FALSE;
			$register_invalid = lang("Firstname must be set", "register");
		} // End if strlen firstName
	} // End register_firstname_required

	/* Check if lastName is valid */
	elseif(config("register_lastname_required", 0))
	{
		if(strlen($lastName) <= 2)
		{
			$lastName = FALSE;
			$register_invalid = lang("Lastname must be set", "register");
		} // End if strlen lastName

	} // End register_lastname_required


	// If something went wrong in the registration;
	// view the registration-page once more, with all fields marked.
	// Otherwise; register the user

	if($register_invalid == FALSE)
	{
		$hide_register = TRUE;
		$md5_pass = md5($pass1);
		db_query("INSERT INTO ".$sql_prefix."_users SET
			nick = '".db_escape($username)."',
			password = '$md5_pass',
			EMail = '".db_escape($EMail)."',
			firstName = '".db_escape($firstName)."',
			lastName = '".db_escape($lastName)."'
		");

	$content .= "User registered";
	} // End if register_invalid = FALSE

} // End action = regsiter

if(!isset($action) || $hide_register == FALSE)
{

	if($register_invalid) echo "<font color=red>$register_invalid</font><br><br>";

	$content .= "<form method=POST action=?module=register&amp;action=register>\n";
	$content .= "<input type=text name=username value='$username'> ".lang("Username", "register");
	$content .= "<br><input type=password name=pass1 value='$pass1'> ".lang("Password", "register");
	$content .= "<br><input type=password name=pass2 value='$pass2'> ".lang("Password again", "register");
	$content .= "<br><input type=text name=EMail value='$EMail'> ".lang("E-Mail", "register");
	if(config("register_firstname_required", 0))
		$content .= "<br><input type=text name=firstName value ='$firstName'> ".
		lang("First name", "register");
	if(config("register_lastname_required", 0))
		$content .= "<br><input type=text name=lastName value='$lastName'> ".
		lang("Last name", "register");
	$content .= "<br><input type=submit value='".lang("Create user", "register")."'>";
	$content .= "</form>\n";

	// Here we should do config()-checks to see if we should ask the user about these stuff...
	if(config("register_ask_postnumber") && config("register_postnumber_AJAX"))
	{
		$head .= "<!-- Here we can input AJAX-code for postnumbers -->";
	} // End if AJAX postnumbers

} // End elseif !isset $action
