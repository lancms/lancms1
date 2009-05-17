<?php

######################################################
function db_query($query)
{
/* Function to do queries to/from DBs */
	global $sql_type;
	switch ($sql_type)
	{
		case "mysql":
			$q = mysql_query($query) or die("MYSQL Error with query (".$query.") because of: ".mysql_error());
			break;
		default:
			die("Something seriously wrong with variable sql_type in function db_query. The reported sql_type is '$sql_type'");

	} // end switch ($sql_type)

	return $q;
} // End function db_query()

######################################################
function db_fetch($query) {
	global $sql_type;
/* Function to fetch results from db_query */
	switch ($sql_type)
	{
		case "mysql":

			$return = mysql_fetch_object($query); // Memo to self: can not die, as it should return false if nothing there
			break;
		default:
			die("Something seriously wrong with variable sql_type in function db_fetch");
	} // End switch ($sql_type)

	return $return;
} // End function db_fetch


######################################################
function db_escape($var)
{
	global $sql_type;
/* Function to escape strings before they are inserted to the DB. Should avoid some haxxoring, so should probably use it... */
	switch ($sql_type)
	{
		case "mysql":
			$return = mysql_real_escape_string($var);
			break;
		default:
			die("Something seriously wrong with variable sql_type in function db_escape");
	} // End switch ($sql_type)

	return $return;

} // End function db_escape

######################################################
function db_num ($q)
{
	global $sql_type;
	switch ($sql_type)
	{
		case "mysql":
			$return = mysql_num_rows($q);
			break;
		default:
			die("Something seriously wrong with variable sql_type in function db_num");
	} // End switch ($sql_type)

	return $return;
}


######################################################
function config($config, $event = 1, $value = "NOTSET")
{
	global $sql_prefix;
	$query = db_query("SELECT * FROM ".$sql_prefix."_config WHERE config = '".db_escape($config)."' AND eventID = '".db_escape($event)."'");
	$num = db_num($query);
	if ($value == "NOTSET") // No value is set. We should only SELECT to find out what the value is.
	{
		$object = db_fetch($query);

		if ($num == 0) // No such value exists. That probably means that noone has activated it yet, and therefor, it is false, or turned off
		{
			return FALSE;
		}

		elseif ($object->value == 0) // The config exists, and the value is 0, which is turned off
		{
			return FALSE;
		}
		else // If it exists, and it is not turned off; just output it
		{
//			echo "config: ".$config.", event = ".$event.", value = ".$object->value;
			return $object->value;
		}
	} // End if value == NOTSET
	else // $value IS set, so we should write/update that config
	{
		if($value == "disable") $value = 0;
		if ($num == 0) // That config doesn't exists yet. Insert it
		{
			db_query("INSERT INTO ".$sql_prefix."_config SET config = '".db_escape($config)."',
				value = '".db_escape($value)."',
				eventID = '".db_escape($event)."'");
		}
		else // That config exists. Update the existsing
		{
			db_query("UPDATE ".$sql_prefix."_config SET value = '".db_escape($value)."'
				WHERE config = '".db_escape($config)."' AND
				eventID = '".db_escape($event)."'");
		}
	} // End else

}


######################################################
function acl_access($module, $subcategory=0, $event=1, $userID = "MYSELF")
{
	/* Check what rights the user has to a module or event. */

	global $sql_prefix;
	global $sessioninfo;
	if($userID == "MYSELF")
		$userID = $sessioninfo->userID; // Use current user
	/* Biiip. Not the correct way of doing it!
	// Check if user is anonymous (and don't give access to anything)
	if(!$sessioninfo->userID)
	{
		return "No";
		break;
	}
	*/
	// Check if user is global admin
	$qGlobalAdmin = db_query("SELECT globaladmin FROM ".$sql_prefix."_users WHERE ID = ".db_escape($userID));
	$rGlobalAdmin = db_fetch($qGlobalAdmin);
	if($rGlobalAdmin->globaladmin == 1)
	{
		return "Admin";
		break;
	}

	// Check what groups the user is a member of
	$qCheckGroups = db_query("SELECT groupID FROM ".$sql_prefix."_group_members
		WHERE userID = ".db_escape($userID));
	$groupList = FALSE; // List of groups a user is member of
	$groupList = '1';
	while($rCheckGroups = db_fetch($qCheckGroups))
	{
		$groupList .= " ,";
		$groupList .= $rCheckGroups->groupID;

	} // End while CheckGroups

	// Check what the highest ACL-right you have on event
#	if($event != 1) // Event-ID 1 is used on things that are not event-specific.
#	{

#		$qCheckEventRight = db_query("SELECT access FROM ".$sql_prefix."_ACLs
#			WHERE eventID = '".db_escape($event)."'
#			AND groupID IN ($groupList)
#			AND accessmodule = 'eventadmin'
#			AND subcategory = ('".db_escape($subcategory)."' OR 0)
#			ORDER BY access = 'Admin' DESC,
#			access = 'Write' DESC,
#			access = 'Read' DESC,
#			access = 'No' DESC
#			LIMIT 0,1
#			");
#		$rCheckEventRight = db_fetch($qCheckEventRight);
#		if(isset($rCheckEventRight->access))
#		{
#			return $rCheckEventRight->access;
#			break;
#		}

#	} // End if event != 0/check eventACL.
	// Not admin, check what rights the group has
	if($module == "grouprights" && $subcategory != 0)
	{
		// What rights does this user has for the group in subcategory
		$qCheckGroupRights = db_query("SELECT access FROM ".$sql_prefix."_group_members
			WHERE groupID = '".db_escape($subcategory)."'
			AND userID = '".db_escape($userID)."'");
		$rCheckGroupRights = db_fetch($qCheckGroupRights);
		if($rCheckGroupRights->access) return $rCheckGroupRights->access;
		else return "No";
	} // End elseif module = grouprights

	$qCheckModuleRight = db_query("SELECT access FROM ".$sql_prefix."_ACLs
		WHERE eventID = '".db_escape($event)."'
		AND groupID IN ($groupList)
		AND accessmodule = '".db_escape($module)."'
		AND subcategory IN(0, '".db_escape($subcategory)."')
		ORDER BY access = 'Admin' DESC,
		access = 'Write' DESC,
		access = 'Read' DESC,
		access = 'No' DESC
		LIMIT 0,1
		");

	$rCheckModuleRight = db_fetch($qCheckModuleRight);
	if(!empty($rCheckModuleRight))
		return $rCheckModuleRight->access;
	else // None of the above has matched. Return No access
		return 'No';
}




######################################################

function lang($string, $module = "index")
{
	global $language; // Get default/current language
	global $sql_prefix;

	// Check to see if that string exists
	$q = db_query("SELECT * FROM ".$sql_prefix."_lang
		WHERE string = '".db_escape($string)."'
		AND language = '".db_escape($language)."'
		AND module = '".db_escape($module)."'");

	// How many occurences of string
	$num = db_num($q);
	if ($num == 0)
	{
		/* The string does not exist in the database, add it */
		db_query("INSERT INTO ".$sql_prefix."_lang
			SET string = '".db_escape($string)."',
			language = '".db_escape($language)."',
			module = '".db_escape($module)."'");
		return $string;
	} // End not exists

	elseif ($num >= 2)
	{
		die("There is an error in the lang()-function, more than one existance of string: '".$string."' in module: '".$module."' for language: '".$language."'. FIX IT!");
	}
	else // String should have returned a result of one row
	{
		$r = db_fetch($q);
		if ((empty($r->translated)) || (!isset($r->translated)))
		{
			return $string; // String has not been translated
		}
		else
		{
			return $r->translated; // String has been translated.
		}
	}
}


######################################################

function option_rights($default = 'No')
{
	/* This function returns <options> for use where you select what rights a group shall have */
	// Display No-rights
	$return .= "<option value='No'";
	if($default == 'No') $return .= ' selected';
	$return .= ">".lang("No", "functions")."</option>\n";

	// Display Read-rights
	$return .= "<option value='Read'";
	if($default == 'Read') $return .= ' selected';
	$return .= ">".lang("Read", "functions")."</option>\n";

	// Display Write-rights
	$return .= "<option value='Write'";
	if($default == 'Write') $return .= ' selected';
	$return .= ">".lang("Write", "functions")."</option>\n";

	// Display Admin-rights
	$return .= "<option value='Admin'";
	if($default == 'Admin') $return .= ' selected';
	$return .= ">".lang("Admin", "functions")."</option>\n";

	return $return;
} // End function option_rights


function seating_rights($seatX, $seatY, $ticketID, $eventID, $password = 0) {
    global $sql_prefix;
    global $sessioninfo;
    $qSeatInfo = db_query("SELECT * FROM ".$sql_prefix."_seatReg WHERE eventID = '$eventID'
        AND seatX = '$seatX' AND seatY = '$seatY'");
    $rSeatInfo = db_fetch($qSeatInfo);
    $seating_enabled = config("seating_enabled", $eventID);

    $returncode = 0;
    // Check event-rights
    $acl_event_seating = acl_access("seating", "", $eventID);
    // Check if the seat is already taken
    $qCheckAlreadySeated = db_query("SELECT seatingID FROM ".$sql_prefix."_seatReg_seatings WHERE
        eventID = '$eventID' AND seatX = '$seatX' AND seatY = '$seatY'");
    if(db_num($qCheckAlreadySeated) != 0) $returncode = FALSE;
    elseif($acl_event_seating == 'Admin' || $acl_event_seating == 'Write') $returncode = TRUE;

    elseif($seating_enabled == 1) {
        // Seating is enabled for this event?

        // Get info about the ticket
        $qTicketInfo = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE eventID = '$eventID' AND
	ticketID = '$ticketID'");
        $rTicketInfo = db_fetch($qTicketInfo);

        if($rTicketInfo->owner == $sessioninfo->userID || $rTicketInfo->user == $sessioninfo->userID) {
			$type = $rSeatInfo->type;
			switch ($type) {
	 			case 'd':
	   				// Seat is a normal seat
	   	   			$returncode = 1;
	        		break;
	   		 	case 'g':
	    	    	// Groupprotected. Check if access to group
	    	    	if(acl_access("grouprights", $rSeatInfo->extra, "", $sessioninfo->userID) != 'No') $returncode = 1;
	    	    	break;
	    		case 'p':
	    		    // Password-protected. Check if password correct
	    		    if($password == $rSeatInfo->extra) $returncode = 1;
	    		    #die("password: $password, matching against $rSeatInfo->extra");
	    	    	break;
	    	    default:
	    	    	die("type: ".$type);
	    	} // End switch($type)
        } // End if rTicketInfo->owner || user == session-userID

    } // End elseif(config(seating_enabled))

    return $returncode;

} // End function seating_rights

######################################################

// Displays name and nick in listings
function display_username($userID) {
	global $sql_prefix;

	$qCheckUserinfo = db_query("SELECT nick,firstname,lastname FROM ".$sql_prefix."_users WHERE ID = '".db_escape($userID)."'");
	$rCheckUserinfo = db_fetch($qCheckUserinfo);

	return $rCheckUserinfo->firstname." ".$rCheckUserinfo->lastname." (".$rCheckUserinfo->nick.")";
} // End display_username($userID)

function display_systemstatic($message) {
	global $sql_prefix;
	
	$qFindMessage = db_query("SELECT * FROM ".$sql_prefix."_static WHERE type = 'system' AND header = '".db_escape($message)."'");
	$rFindMessage = db_fetch($qFindMessage);

	return $rFindMessage->page;
}

#############

// adding logentry:
function log_add ($logtype, $lognew="0", $logold="0", $userid=0, $eventid=0, $userip=0, $userhost=0, $logurl=0)
{


	global $sql_prefix;
	global $sessioninfo;

	if ($lognew == "0")
	{
		$lognew = 'NULL';
	}
	if ($logold == "0")
	{
		$logold = 'NULL';
	}
	if ($userid == 0)
	{
		$userid = $sessioninfo->userID;
	}
	if ($eventid == 0)
	{
		$eventid = $sessioninfo->eventID;
	}
	if ($userip == 0)
	{
		$userip = $_SERVER['REMOTE_ADDR'];
	}
	if ($userhost == 0)
	{
		$userhost = $_SERVER['REMOTE_HOST'];
		if (empty ($userhost))
		{
			$userhost = 'NULL';
		}
	}
	if ($logurl == 0)
	{
		$logurl = $_SERVER['REQUEST_URI'];
	}
	$query = sprintf ('INSERT INTO %s_logs (userID, userIP, userHost, eventID, logType, logTextNew, logTextOld, logURL) VALUES (%s, INET_ATON("%s"), %s, %s, %s, "%s", "%s", "%s")', $sql_prefix, db_escape ($userid), db_escape ($userip), db_escape ($userhost), db_escape ($eventid), db_escape ($logtype), db_escape ($lognew), db_escape ($logold), db_escape ($logurl));

	db_query ($query);
}

##### log_logtype - returns name for logtype:
function log_logtype ($logtype)
{
	switch ($logtype)
	{
		case 1:
			$return = 'Logged in';
			break;
		case 2:
			$return = 'Logged out';
			break;
		case 3:
			$return = 'Failed login';
			break;
		case 4:
			$return = 'Registered user';
			break;
		
		default:
			$return = 'Unknown';
			break;
	}
	return ($return);
}



############ log_get - returns log-object if logID exists, false if not.
function log_get ($logid)
{
	global $sql_prefix;

	$query = sprintf ('SELECT * FROM %s_logs WHERE ID=%s', $sql_prefix, db_escape ($logid));

	$result = db_query ($query);

	if (!mysql_num_rows ($result))
	{
		return (false);
	}
	else
	{
		$result = db_query ($query);
		$fetch = db_fetch ($result);
		return ($fetch);
	}
}
