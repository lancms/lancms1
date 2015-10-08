<?php

$action = $_GET['action'];

if(!config("users_may_register")) die("users may not register yet");

if (!($sessioninfo->userID <= 1 or acl_access ("userAdmin", "", $sessioninfo->eventID) != 'No'))
{
	header ('Location: index.php');
	die ();
}

if($action == "register")
{
	$username = $_POST['username'];
	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];
	$EMail = $_POST['EMail'];
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$gender = $_POST['gender'];
	$birthDay = $_POST['birthDay'];
	$birthMonth = $_POST['birthMonth'];
	$birthYear = $_POST['birthYear'];
	$address = $_POST['address'];
	$postnumber = $_POST['postnumber'];
	$cellphone = $_POST['cellphone'];


	if(empty($username))
		$register_invalid = lang("Please provide a username", "register");
	if(strlen($username) <=1)
		$register_invalid = lang("Please provide a username", "register");
	if(!(strchr($email, "@")) && (strchr($email, ".")))
		$register_invalud = lang("Please provide a valid email-address", "register");

	/* Check if the username is free */
	$qCheckUsername = db_query("SELECT nick FROM ".$sql_prefix."_users
		WHERE nick LIKE '".db_escape($username)."'");
	if(db_num($qCheckUsername) != 0)
	{
		$register_invalid = lang("Username already in use", "register");
		$username = FALSE;
	} // end check if username is free

	/* Check if the passwords match */
	if ( $pass1 != $pass2)
	{
		$register_invalid = lang("Passwords does not match", "register");
		$pass1 = FALSE;
		$pass2 = FALSE;
	} // End check if passwords match

	/* Check if firstName is valid */
	if(config("register_firstname_required"))
	{
		if(strlen($firstName) <=2)
		{
			$firstName = FALSE;
			$register_invalid = lang("Firstname must be set", "register");
		} // End if strlen firstName
	} // End register_firstname_required

	/* Check if lastName is valid */
	if(config("register_lastname_required"))
	{
		if(strlen($lastName) <= 2)
		{
			$lastName = FALSE;
			$register_invalid = lang("Lastname must be set", "register");
		} // End if strlen lastName

	} // End register_lastname_required
	if(config("userinfo_birthyear_required")) {
		if($birthYear == "none") {
			$register_invalid = lang("Birthyear has to be set", "register");
		} // End if
	} // End elseif userinfo_lastname_required

	if(config("userinfo_birthday_required")) {
		if($birthDay == "none") {
			$register_invalid = lang("Birthday has to be set", "register");
		} // End if birthDay
		if($birthMonth == "none") {
			$register_invalid = lang("Birthmonth has to be set", "register");
		} // End if birthMonth
	} // End birthday_required
	if(config("userinfo_gender_required")) {
		if($gender != 'Male' AND $gender != 'Female')
			$register_invalid = lang("You have to specify your gender", "register");
	} // End if config(userinfo_gender_required)
	if(config("userinfo_address_required")) {
		if(!is_numeric($postnumber)) $register_invalid = lang("Postnumber has to be a number", "register");
		if(strlen($address) <=3) $register_invalid = lang("Please specify your address", "registert");
	} // End if config(userinfo_address_required)

	// FIXME? Cellphone is optional, we may need some code to 1) make it required, 2) hide it completely or 3) something else
	if (!empty($cellphone))
	{
		if (!is_numeric ($cellphone))
		{
			$register_invalid = lang ("Cellphone is supposed to be a number", "register");
		}
		// FIXME? Hardcoded lenght of cellphone numbers
		if (strlen ($cellphone) != 8)
		{
			$register_invalid = lang ("Cellphone is supposed to be eight digits", "register");
		}

	}

	// If something went wrong in the registration;
	// view the registration-page once more, with all fields marked.
	// Otherwise; register the user

	if(!$register_invalid)
	{
		$genkey = md5(rand(1,10000) * time());
		$hide_register = TRUE;
		$md5_pass = md5($pass1);
		db_query("INSERT INTO ".$sql_prefix."_users SET
			nick = '".db_escape($username)."',
			password = '$md5_pass',
			EMail = '".db_escape($EMail)."',
			firstName = '".db_escape($firstName)."',
			lastName = '".db_escape($lastName)."',
			gender = '".db_escape($gender)."',
			birthDay = '".db_escape($birthDay)."',
			birthMonth = '".db_escape($birthMonth)."',
			birthYear = '".db_escape($birthYear)."',
			street = '".db_escape($address)."',
			postnumber = '".db_escape($postnumber)."',
			cellphone = '".db_escape ($cellphone)."',
			registerIP = '".$_SERVER['REMOTE_ADDR']."',
			EMailVerifyCode = '".$genkey."',
			registerTime = '".time()."'
		");


		$newid = db_insert_id();

		$url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?module=register&action=verifymail&userID=$newid&verifycode=$genkey";
		$email_subject = lang("Verify your new account");
		$email_content = sprintf(lang("Hello %s.

You, or someone else has registered a new account on %s.

To verify your mailaddress, please go to <a href='%s'>%s</a>"), '%%FIRSTNAME%%', $_SERVER['SERVER_NAME'], $url, $url);

		send_email($newid, $email_subject, $email_content);		
		// Fix default preferences
		for($i=0;$i<count($userpersonalprefs);$i++) {
			if($userpersonalprefs[$i]['default_register'] == 1) {
				$prefname = $userpersonalprefs[$i]['name'];
				switch ($userpersonalprefs[$i]['type']) {

					case "checkbox":
						db_query("INSERT INTO ".$sql_prefix."_userPreferences
							SET userID = '$newid',
							name = '$prefname',
							value = 'on'");
//						echo "FOO?";
//						die();
						break;
				} // End switch
			} // End if userpersonalprefs_default_register == 1
		} // end for

		$logmsg['userid'] = $newid;
		$logmsg['username'] = $username;
		$logmsg['md5_pass'] = $md5_pass;
		$logmsg['EMail'] = $EMail;
		$logmsg['firstName'] = $firstName;
		$logmsg['lastName'] = $lastName;
		$logmsg['gender'] = $gender;
		$logmsg['birthDay'] = $birthDay;
		$logmsg['birthMonth'] = $birthMonth;
		$logmsg['birthYear'] = $birthYear;
		$logmsg['street'] = $address;
		$logmsg['postnumber'] = $postnumber;
		$logmsg['cellphone'] = $cellphone;

		if ($sessioninfo->userID <= 1)
		{
			// anonymous user registers = logtype 4
			log_add ("register", "anonymous", serialize ($logmsg));
		}
		else
		{
			// logged in user registers a new one = logtype 5
			log_add ("register", "registered", serialize ($logmsg));
		}

		$content .= lang("User registered", "register");
	} // End if register_invalid = FALSE

} // End action = register



#module=register&action=verifymail&userID=$newid&verifycode=$genkey
elseif($action == "verifymail" && isset($_GET['userID']) && isset($_GET['verifycode'])) {
	$hide_register = TRUE;
        $verifycode = $_GET['verifycode'];
        $userID = $_GET['userID'];

        if($verifycode == NULL) $content .= _("Verifycationcode not valid");
        else {
                $qCheckUser = db_query("SELECT EMailConfirmed,EMail,EMailVerifyCode FROM ".$sql_prefix."_users WHERE ID = '".db_escape($userID)."'");
                $rCheckUser = db_fetch($qCheckUser);

                if($rCheckUser->EMailConfirmed == 1) $content .= _("EMail was already verified. Not verified again");
                elseif($rCheckUser->EMailVerifyCode != $verifycode) {
                        $content .= "Verificationcode does not match. Try again";
                        $log_new['tried_verifycode'] = $verifycode;
                        $log_new['actual_verifycode'] = $rCheckUser->EMailVerifyCode;

                        log_add("register", "failed_verifycode", serialize($log_new), "", $userID);
                }
                elseif($rCheckUser->EMailVerifyCode == $verifycode) {
                        $content .= _("EMail verified. Welcome aboard");
                        $log_new['verifycode'] = $verifycode;
                        db_query("UPDATE ".$sql_prefix."_users SET EMailConfirmed = 1 WHERE ID = '".db_escape($userID)."'");
                        log_add("register", "confirmed_verifycode", serialize($log_new), "", $userID);
                } // End elseif EMailVerifyCode == verifycode
        } // End else
} // end action = verifymail



if(!isset($action) || $hide_register == FALSE)
{

	$design_head .= '<script type="text/javascript" src="inc/AJAX/ajax_postnumber.js"></script>'."\n";
	if($register_invalid) $content .= "<font color=red>$register_invalid</font><br><br>";

	$content .= "<form method=POST action=?module=register&amp;action=register>\n";
	$content .= "<input type=text name=username value='$username'> ".lang("Username", "register");
	$content .= "\n<br><input type=password name=pass1 value='$pass1'> ".lang("Password", "register");
	$content .= "\n<br><input type=password name=pass2 value='$pass2'> ".lang("Password again", "register");
	$content .= "\n<br><input type=text name=EMail value='$EMail'> ".lang("E-Mail", "register");
	if(config("register_firstname_required"))
		$content .= "\n<br><input type=text name=firstName value ='$firstName'> ".
		lang("First name", "register");
	if(config("register_lastname_required"))
		$content .= "\n<br><input type=text name=lastName value='$lastName'> ".
		lang("Last name", "register");
	if(config("userinfo_address_required")) {
		$content .= "\n<br><input type=text name=address value='$address'> ".lang("Address", "register");
		$content .= "\n<br><input type=text size=5 name=postnumber ID='postnumber' value='$postnumber' onkeyup=\"suggest();\">".lang("Postnumber", "register")."<div ID='postplace' name='postplace'></div>";
	}

	if(config("userinfo_gender_required")) {
		$content .= "<br><select name=gender>\n";
		$content .= "<option value=none>".lang("Gender", "register")."</option>\n";
		$content .= "<option value=Male";
		if($gender == "Male") $content .= " SELECTED";
		$content .= ">".lang("Male", "register")."</option>\n";
		$content .= "<option value=Female";
		if($gender == "Female") $content .= " SELECTED";
		$content .= ">".lang("Female", "register")."</option>\n";
		$content .= "</select>";
	} // End config(userinfo_gender_required)


	if(config("userinfo_birthday_required")) {
		$content .= "\n<br><select name=birthDay>";
		$content .= "<option value=none>".lang("Birthday", "register")."</option>";
		for($day=1;$day<=31;$day++) {
			$content .= "\n<option value=$day";
			if($birthDay == $day) $content .= " SELECTED";
			$content .= ">$day</option>";
		} // End for
		$content .= "</select>";
		$content .= "<select name=birthMonth>";
		$content .= "<option value=none>".lang("Birthmonth", "register")."</option>";
		for($month=1;$month<=12;$month++) {
			$content .= "\n<option value=$month";
			if($birthMonth == $month) $content .= " SELECTED";
			$content .= ">".$monthname[$month]."</option>";
		} // End for
		$content .= "</select>";

	} // End if config(userinfo_birthyear_required)
	if(config("userinfo_birthyear_required")) {
		$content .= "<select name=birthYear>";
		$content .= "<option value=none>".lang("Birthyear", "register")."</option>";
		for($year=1950;$year<=2009;$year++) {
			$content .= "\n<option value=$year";
			if($birthYear == $year) $content .= " SELECTED";
			$content .= ">$year</option>";
		} // End for
		$content .= "</select>";

	} // End if config(userinfo_birthyear_required)

	// FIXME? Cellphone is optional
	$content .= "<br />";
	$content .= "<input type='text' name='cellphone' value='".$cellphone."' /> ".lang ("Cellphone", "register")." (".lang ("optional", "register").")";


	$content .= "<br><input type=submit value='".lang("Create user", "register")."'>";
	$content .= "</form>\n";

	// Here we should do config()-checks to see if we should ask the user about these stuff...
	if(config("register_ask_postnumber") && config("register_postnumber_AJAX"))
	{
		$head .= "<!-- Here we can input AJAX-code for postnumbers -->";
	} // End if AJAX postnumbers

} // End elseif !isset $action

