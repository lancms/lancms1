<?php


if($action == "finduser")
{
	$user = db_escape($_GET['username']);

	if(is_numeric($_GET['username'])) $finduser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '$user'");
	else $finduser = db_query("SELECT * FROM ".$sql_prefix."_users 
		WHERE (nick LIKE '%".$user."%'
		OR EMail LIKE '%".$user."%'
		OR firstName LIKE '%".$user."%'
		OR lastName LIKE '%".$user."%'
		OR ID = '$user')
		AND ID!=1
	");

	if(db_num($finduser) >= 20)
	{
		$content .= lang("Sorry, too many users, try to narrow down the search", "login");
	} // End if db_num > 20

	elseif(db_num($finduser) == 0)
	{
		$content .= lang("Sorry, no such user found", "login");

	}
	elseif(db_num($finduser) == 1)
	{
		$row = db_fetch($finduser);
		header("Location: index.php?module=login&action=password&userID=$row->ID");
	}
	else
	{
		// The search found more than 0 and less then 20 users
		$content .= "<table>";

		while($row = db_fetch($finduser)) {
			$content .= "<tr><td><a href=\"?module=login&amp;action=password&amp;userID=$row->ID\">$row->nick</a></td></tr>";
		} // End while row = db_fetch;

		$content .= "</table>";

	} // End else


} // End if action = finduser



elseif($action == "password" && !empty($_GET['userID']))
{

	$userID = $_GET['userID'];

	$get_user = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = ".db_escape($userID));

	$userinfo = db_fetch($get_user);

	$content .= "<p>".lang("Log in as:", "login")."&nbsp;&nbsp;&nbsp;".$userinfo->nick."</p>\n";
	$content .= "<form name='password' method=\"post\" action=\"?module=login&amp;action=login&amp;userID=$userID\">\n";
	$content .= "<p>".lang("Password:", "login")." <input class=\"login\" type=\"password\" name=\"password\" /></p>\n";
	$content .= "<p><input class=\"login\" type=\"submit\" value=\"Login\" /></p>\n";
	$content .= "</form>\n";
	$content .= "\n\n";
	$content .= "<script type='text/javascript' language='javascript'>document.forms['password'].elements['password'].focus()</script>\n";
	$content .= "\n\n";
	$content .= "<p><a href='?module=login&action=resetPassword&userID=$userID'>".lang("Reset password")."</a></p>\n";
}


elseif($action == "login" && isset($_GET['userID']) && isset($_POST['password']))
{
	/* User has spesified user ID and password, attempt to login */
	$password = $_POST['password'];
	$userID = $_GET['userID'];

	$get_user = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = ".db_escape($userID));
	$userinfo = db_fetch($get_user);

	if(md5($password) == $userinfo->password && $userinfo->EMailConfirmed == 1)
	{
		// Passwords match. Login the user
		db_query("UPDATE ".$sql_prefix."_session SET userID = '".db_escape($userID)."'
			WHERE sID = '".db_escape($_COOKIE[$lancms_session_cookie])."'");

		// logtype, 1 (login).
		$log_new['user_agent'] = $_SERVER['HTTP_USER_AGENT']; 
		log_add ("login", "success", serialize($log_new), NULL, $userID);

		header("Location: index.php"); // Move to index.php, should give a new userinfo-box
	} // End if passwords match
	
	elseif(md5($password) == $userinfo->password && $userinfo->EMailConfirmed != 1) {
		// Password is correct, but users mail isn't confirmed
		$content .= _("Your email hasn't been confirmed. Please go to the link sent in your email.");
		$content .= "<br /><br />";
		$content .= sprintf(_("If you haven't gotten any email from us yet, <a href=\"%s\">resend email</a>"), "?module=login&action=resendVerifyCode&user=$userinfo->ID&mail=$userinfo->EMail");
	} // End elseif EMailConfirmed != 1

	else
	{
		// Passwords does not match. Fuck the user
		$content .= lang("sorry, wrong password!", "login");

		// but log it:
		// logtype, 3 (failed login)
		$log_new['session_ID'] = $sessioninfo->sID;
		$log_new['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		log_add ("login", "failed_password", serialize($log_new), NULL, $userID);

	} // End else (password does not match)

} // End elseif action = login


elseif($action == "logout")
{
	// logtype, 2 (logout).
	log_add ("login", "logout");

	db_query("UPDATE ".$sql_prefix."_session 
		SET userID = 1 
		WHERE sID = '".db_escape($_COOKIE[$lancms_session_cookie])."'");

	// FIXME: Should probably return to referrer.
	header("Location: index.php");
}


elseif($action == "resetPassword" && !empty($_GET['userID'])) {

	$userID = $_GET['userID'];
	$qGetUser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '".db_escape($userID)."'");
	$rGetUser = db_fetch($qGetUser);

	if($rGetUser->EMailConfirmed == 0) {
		$content .= lang("The user has not confirmed his EMailAddress. Not able to reset password"); 
	} // End if EMailConfirmed == 0
	elseif($rGetUser->lastPasswordReset >= time()-86400) {
		$content .= lang("You have already tried to reset your password today. Please check your mails inbox and junkfolder");
	} // End elseif lastPasswordReset
	else {
		$content .= "<a href='?module=login&action=doResetPassword&userID=$userID'>";
		$content .= lang("I confirm that this is my account, and I want to reset the password");
		$content .= "</a>";
	}

} // End action = resetPassword


elseif($action == "doResetPassword" && !empty($_GET['userID'])) {
	$userID = $_GET['userID'];
        $qGetUser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '".db_escape($userID)."'");
        $rGetUser = db_fetch($qGetUser);

	if($rGetUser->EMailConfirmed == 0) $content .= lang("Email not confirmed!");
	elseif($rGetUser->lastPasswordReset >= time()-86400) $content .= lang("Multiple attempts at password reset. Not done");
	else {
		$genkey = md5(serialize($rGetUser) * time() * rand(0,100000));

		db_query("UPDATE ".$sql_prefix."_users SET passwordResetCode = '$genkey', lastPasswordReset = '".time()."'");
		$url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?module=login&action=newPassword&userID=$userID&key=$genkey";
		$email_content = sprintf(lang("<p>Hello %s %s.</p>
<br />
<p>You or somebody else from IP %s has tried to reset the account of $rGetUser->nick</p>
<br />
<p>Please verify that this is your account and set a new password by going to <a href='%s'>%s</a></p>"), '%%FIRSTNAME%%', '%%LASTNAME%%', $sessioninfo->userIP, $url, $url);
		$email_subject = sprintf(lang("Password reset for %s"), $eventinfo->eventname);
		db_query("INSERT INTO ".$sql_prefix."_cronjobs SET cronModule = 'MAIL', 
			toUser = '".db_escape($userID)."', 
			content = '".db_escape($email_content)."',
			subject = '".db_escape($email_subject)."',
			senderID = '$sessioninfo->userID'");
		$log_new['userID'] = $userID;
		log_add("login", "doResetPassword", serialize($log_new));

		$content .= lang("Email sent. Please check your inbox and spamfolder");
	} // End else
} // End doResetPassword


elseif($action == "newPassword" && !empty($_GET['userID'])&& !empty($_GET['key'])) {
	$userID = $_GET['userID'];
	$key = $_GET['key'];

	$qFindUser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE 
		ID = '".db_escape($userID)."' AND
		passwordResetCode = '".db_escape($key)."'");
	if(db_num($qFindUser) != 1) {
		$content .= lang("Key or userID not found, or already used. You can only reset the password once with each key");
	} // End if db_num() != 1
	else {
		$content .= lang("Please provide your new password, two times");
		$content .= "<form method=POST action='?module=login&action=doNewPassword&userID=$userID&key=$key'>\n";
		$content .= "<br /><input type='password' size=10 name='password1'>\n";
		$content .= "<br /><input type='password' size=10 name='password2'>\n";
		$content .= "<br /><input type='submit' value='".lang("Set new password")."'>\n";
		$content .= "</form>\n\n";
	} // End else

} // End action == newPassword


elseif($action == "doNewPassword" && !empty($_GET['userID'])&& !empty($_GET['key'])) {
        $userID = $_GET['userID'];
        $key = $_GET['key'];
	$pwd1 = $_POST['password1'];
	$pwd2 = $_POST['password2'];

        $qFindUser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE 
                ID = '".db_escape($userID)."' AND
                passwordResetCode = '".db_escape($key)."'");
        if(db_num($qFindUser) != 1) {
                $content .= lang("Key or userID not found, or already used. You can only reset the password once with each key");
        } // End if db_num() != 1
	elseif($pwd1 != $pwd2) $content .= lang("Passwords don't match. Go back and try again");
        else {

		db_query("UPDATE ".$sql_prefix."_users SET
			password = '".md5($pwd1)."',
			passwordResetCode = NULL
			WHERE ID = '".db_escape($userID)."'");

		$log_new['userID'] = $userID;
		log_add("login", "doNewPassword", serialize($log_new));

		$content .= sprintf(lang("Password successfully reset. Please go back to <a href='%s'>Login</a>"), "?module=login&action=password&userID=$userID");
	} // End else

} // End elseif action = doNewPassword


elseif($action == "resendVerifyCode") {
	$user = $_GET['user'];
	$mail = $_GET['mail'];

	$qFindUser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '".db_escape($user)."' AND EMail = '".db_escape($mail)."'");
	if(db_num($qFindUser) != 1) die("Could not find user and email?");
	$rFindUser = db_fetch($qFindUser);
	
	if($rFindUser->EMailVerifyCode == NULL) {
		$gencode = md5(rand(1,100000) * time());
		db_query("UPDATE ".$sql_prefix."_users SET EMailVerifyCode = '$gencode' WHERE ID = '$rFindUser->ID'");
	}
	else $gencode = $rFindUser->EMailVerifyCode;

	$url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?module=register&action=verifymail&userID=$user&verifycode=$gencode";
	$email_subject = lang("Verify your account");
	$email_content = sprintf(_("Hello %s.

You have tried to login to your account on %s, but haven't verified your account.

To verify your mailaddress, please go to %s"), '%%FIRSTNAME%%', $_SERVER['SERVER_NAME'], $url);
	send_email($user, $email_subject, $email_content);
	log_add("login", "resendVerifyCode");
	$content .= _("Email with verificationcode has been sent. Please wait for it to come (it might take a couple of minutes).");
}	
