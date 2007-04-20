<?php

$action = $_GET['action'];


if($action == "register")
{
	$username = $_POST['username'];
	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];
	$EMail = $_POST['EMail'];
	
	/* Check if the username is free */
	$qCheckUsername = db_query("SELECT nick FROM ".$sql_prefix."_users 
		WHERE nick LIKE '".db_escape($username)."'");
	if(db_num($qCheckUsername) != 0)
	{
		$register_invalid = "Username already in use";
		$username = FALSE;
	} // end check if username is free
	
	/* Check if the passwords match */
	if ( $pass1 != $pass2)
	{
		$register_invalid = "Passwords does not match";
		$pass1 = FALSE;
		$pass2 = FALSE;
	} // End check if passwords match
	
	
	// If something went wrong in the registration; 
	// view the registration-page once more, with all fields marked.
	// Otherwise; register the user
	
	if($register_invalid == FALSE)
	{
		$hide_register = TRUE;
		$md5_pass = md5($pass1);
		db_query("INSERT INTO ".$sql_prefix."_users SET
			nick = '$username',
			password = '$md5_pass',
			EMail = '$EMail'
		");
	
	echo "User registered";
	} // End if register_invalid = FALSE
	
} // End action = regsiter
	
if(!isset($action) || $hide_register == FALSE)
{
	
	if($register_invalid) echo "<font color=red>$register_invalid</font><br><br>";

	$content .= "<form method=POST action=?module=register&action=register>\n";
	$content .= "<input type=text name=username value='$username'> ".lang("Username", "register");
	$content .= "<br><input type=password name=pass1 value='$pass1'> ".lang("Password", "register");
	$content .= "<br><input type=password name=pass2 value='$pass2'> ".lang("Password again", "register");
	$content .= "<br><input type=text name=EMail value='$EMail'> ".lang("E-Mail", "register");
	$content .= "<br><input type=submit value='".lang("Create user", "register")."'>";
		
	// Here we should do config()-checks to see if we should ask the user about these stuff...
	if(config("register_ask_postnumber") && config("register_postnumber_AJAX")) 
	{
		$head .= "<!-- Here we can input AJAX-code for postnumbers -->";
	} // End if AJAX postnumbers
		
} // End elseif !isset $action
