<?php


if($action == "finduser")
{
	$user = db_escape($_GET['username']);

	$finduser = db_query("SELECT * FROM ".$sql_prefix."_users 
	WHERE nick LIKE '%".$user."%'
	OR EMail LIKE '%".$user."%'
	OR firstName LIKE '%".$user."%'
	OR lastName LIKE '%".$user."%'
	");

	if(db_num($finduser) >= 20)
	{
		$content .= "Sorry, to many users, to narrow down the search";
	} // End if db_num > 20

	elseif(db_num($finduser) == 0)
	{
		$content .= "Sorry, no such user found";

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
			$content .= "<tr><td><a href=?module=login&action=password&userID=$row->ID>$row->nick</a></td></tr>";
		} // End while row = db_fetch;

		$content .= "</table>";

	} // End else


} // End if action = finduser



elseif($action == "password" && !empty($_GET['userID']))
{

	$userID = $_GET['userID'];

	$get_user = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = ".db_escape($userID));

	$userinfo = db_fetch($get_user);

	$content .= "Logging in as: ".$userinfo->nick."<br>\n";
	$content .= "<form method=POST action=?module=login&action=login&userID=$userID>\n";
	$content .= "<input type=password name=password><br>\n";
	$content .= "<input type=submit value=Login>\n";
}


elseif($action == "login" && isset($_GET['userID']) && isset($_POST['password']))
{
	/* User has spesified user ID and password, attempt to login */
	$password = $_POST['password'];
	$userID = $_GET['userID'];

	$get_user = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = ".db_escape($userID));
	$userinfo = db_fetch($get_user);

	if(md5($password) == $userinfo->password)
	{
		// Passwords match. Login the user
		db_query("UPDATE ".$sql_prefix."_session SET userID = '".db_escape($userID)."'
			WHERE sID = '".db_escape($_COOKIE[$osgl_session_cookie])."'");
		header("Location: index.php"); // Move to index.php, should give a new userinfo-box
	} // End if passwords match

	else
	{
		// Passwords does not match. Fuck the user
		$content .= "sorry, wrong password!";
	} // End else (password does not match)

} // End elseif action = login


elseif($action == "logout")
{
	db_query("UPDATE ".$sql_prefix."_session 
		SET userID = 0 
		WHERE sID = '".db_escape($_COOKIE[$osgl_session_cookie])."'");
	// FIXME: Should probably return to referrer.
	header("Location: index.php");
}