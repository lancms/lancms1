<?php

if(file_exists('../SVN_OverrideConfig.php')) require('../SVN_OverrideConfig.php');
else require('../config.php');
require('../inc/shared_functions.php');


$eventID = 3; // EventID to import the DB to



$from = mysql_connect($sql_host, "OSGLconvert", "ComPuterParty") or die(mysql_error());
mysql_select_db("net_globelan_15", $from) or die(mysql_error());

$todb = mysql_connect($sql_host, $sql_user, $sql_pass) or die(mysql_error());
mysql_select_db($sql_base, $todb) or die(mysql_error());




$qFromUsers = mysql_query("SELECT * FROM users WHERE ID != 1 AND verified = 0", $from);

while($rFromUsers = mysql_fetch_object($qFromUsers)) {

	/* ******************************************************************************* */
	/* *                 Convert to users-table                                        */
	/* ******************************************************************************* */

	$qFindToUser = mysql_query("SELECT ID FROM ".$sql_prefix."_users WHERE firstName LIKE '".$rFromUsers->firstName."'
		AND lastName LIKE '".$rFromUsers->lastName."' AND nick LIKE '".$rFromUsers->nick."'", $todb) or die(mysql_error());

	if(mysql_num_rows($qFindToUser) == 0) {
		echo "Didn't find $rFromUsers->nick, inserting\n";
		if($rFromUsers->gender == 1) $gender = 'Female';
		else $gender = 'Male';
		mysql_query("INSERT INTO ".$sql_prefix."_users SET
			nick = '".$rFromUsers->nick."',
			firstName = '".$rFromUsers->firstName."',
			lastName = '".$rFromUsers->lastName."',
			EMail = '".$rFromUsers->EMail."',
			gender = '".$gender."',
			cellphone = '".$rFromUsers->cellphone."',
			birthDay = '".$rFromUsers->birthDAY."',
			birthMonth = '".$rFromUsers->birthMONTH."',
			birthYear = '".$rFromUsers->birthYEAR."',
			password = '".$rFromUsers->password."',
			street = '".$rFromUsers->street."',
			postNumber = '".$rFromUsers->postNr."',
			postPlace = '".$rFromUsers->postPlace."'

		", $todb) or die(mysql_error());
		$qFindToUser = mysql_query("SELECT ID FROM ".$sql_prefix."_users WHERE firstName LIKE '".$rFromUsers->firstName."'
			AND lastName LIKE '".$rFromUsers->lastName."' AND nick LIKE '".$rFromUsers->nick."'", $todb);
	} // End if mysql_num_rows(qFindToUser);
	else echo "Found ".$rFromUsers->nick;

	$rFindUser = mysql_fetch_object($qFindToUser);

	$currentUser = $rFindUser->ID;








} // End qUsers





