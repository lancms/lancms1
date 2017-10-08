<?php

######################################################
$configValues = null;
function config($config, $event = 1, $value = "NOTSET")
{
	global $sessioninfo, $sql_prefix, $configValues;

	// Speed performance by fetching all config values from database then reusing an array in runtime.
	// FIXME: APC cache this?
	if ($configValues == null) {
		$configValues = array();
		$query = db_query("SELECT * FROM ".$sql_prefix."_config WHERE 1");
		if (db_num($query) > 0) {
			while($row = db_fetch_assoc($query)) {
				$configValues[$row['eventID']][$row['config']] = $row['value'];
			}
		}
	}

	if ($value == "NOTSET") // No value is set. We should only SELECT to find out what the value is.
	{
		if (isset($configValues[$event][$config]) == false) {
			return false;
		}

		$value = $configValues[$event][$config];
		if ($value == 0) {
			return false;
		}

		return $value;
	} // End if value == NOTSET
	else // $value IS set, so we should write/update that config
	{
		if($value == "disable") $value = 0;
		if (isset($configValues[$event][$config]) == false) // That config doesn't exists yet. Insert it
		{
			db_query("INSERT INTO ".$sql_prefix."_config(config,value,eventID)VALUES('".db_escape($config)."','".db_escape($value)."','".db_escape($event)."')");
		}
		else // That config exists. Update the existsing
		{
			db_query("UPDATE ".$sql_prefix."_config SET value = '".db_escape($value)."'
				WHERE config = '".db_escape($config)."' AND
				eventID = '".db_escape($event)."'");
		}

		// Update runtime cache.
		$configValues[$event][$config] = $value;
	} // End else

}


######################################################
function acl_access($module, $subcategory=0, $event=1, $userID = "MYSELF", $check_global = 1)
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
	if($check_global) {
		$qGlobalAdmin = db_query("SELECT globaladmin FROM ".$sql_prefix."_users WHERE ID = ".db_escape($userID));
		$rGlobalAdmin = db_fetch($qGlobalAdmin);
		if($rGlobalAdmin->globaladmin == 1)
		{
			return "Admin";
		}
	} // End check_global
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
		WHERE eventID IN ('".db_escape($event)."', 1)
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

$langStrings = null;

function lang($string, $module = "index")
{
	global $language, $sql_prefix, $lang_method, $langStrings;

	if($lang_method == "gettext") {
		 return _($string);
	} else {

		// Fill language array if we need to.
		if ($langStrings == null) {
			$langStrings = array();
			$q = db_query("SELECT * FROM ".$sql_prefix."_lang WHERE 1");
			if (db_num($q) > 0) {
				while($row = db_fetch_assoc($q)) {
					$langStrings[$row['language']][$row['module']][] = $row['string'];
				}
			}
		}

		if (isset($langStrings[$language][$module])) {
			foreach ($langStrings[$language][$module] as $key => $value) {
				if ($string == $value) {
					return $value;
				}
			}
		}

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
#	return _($string);
	} // End else lang_method();
}



######################################################

function option_rights($default = 'No')
{
	/* This function returns <options> for use where you select what rights a group shall have */
	// Display No-rights
	$return = "<option value='No'";
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
				case 'r':
					// Right-protected. Check if the user has that right.
					if(acl_access($rSeatInfo->extra, "", "", $sessioninfo->userID) != 'No') $returncode = 1;
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

function display_systemstatic($message, $eventID=1) {
	global $sql_prefix;

	$qFindMessage = db_query("SELECT * FROM ".$sql_prefix."_static WHERE type = 'system' AND header = '".db_escape($message)."' AND eventID = '$eventID'");
	$rFindMessage = db_fetch($qFindMessage);

	// Catch if there is no result.
	if (db_num($qFindMessage) < 1) {
		return "";
	}

	return stripslashes($rFindMessage->page);
}

#############

// show news for events
// FIXME: should be fixed not to show news with active='no'
// FIXME: should support global news
function display_news ($eventid=1)
{
	global $sql_prefix;

	if (!config ("enable_news", $eventid))
	{
		return false;
	}


	$articles = NewsArticleManger::getInstance()->getArticles($eventid, true);

	if (count($articles) < 1) {
		return false;
	}
	$return = "<div class=\"news-section\"><h2>".lang ("News", "news")."</h2>\n";

	foreach ($articles as $article) {
		$return .= "<article class='newsbox'>";
		$return .= "<header><h3>" . $article->getHeader() . "</h3><div class=\"meta\">" . date("d.m.Y H:i:s", $article->getCreateTime()) . "</div></header>\n";
		$return .= "<div class=\"article-content\">" . $article->getContent() . "</div>\n";
		$return .= "</article>\n";
	}

	$return .= "</div>";

	return $return;
}


#############

// adding logentry:
function log_add ($logmodule, $logfunction, $lognew="0", $logold="0", $userid=0, $eventid=0, $userip=0, $userhost=0, $logurl=0)
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
		if(!empty($sessioninfo->userID))
			$userid = $sessioninfo->userID;
		else $userid = 1;
	}
	if ($eventid == 0)
	{
		$eventid = $sessioninfo->eventID;
	}

	if (!is_numeric($eventid)) $eventid = 0;

	if ($userip == 0)
	{
		$userip = $_SERVER['REMOTE_ADDR'];
	}
	if ($userhost == 0)
	{
		$userhost = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
		if (empty ($userhost))
		{
			$userhost = 'NULL';
		}
	}
	if ($logurl == 0)
	{
		$logurl = $_SERVER['REQUEST_URI'];
	}
	$query = sprintf ('INSERT INTO %s_logs (userID, userIP, userHost, eventID, logModule, logFunction, logTextNew, logTextOld, logURL) VALUES ("%s", INET_ATON("%s"), "%s", "%s", "%s", "%s", "%s", "%s", "%s")', $sql_prefix, db_escape ($userid), db_escape ($userip), db_escape ($userhost), db_escape ($eventid), db_escape ($logmodule), db_escape($logfunction), db_escape ($lognew), db_escape ($logold), db_escape ($logurl));

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
		case 5:
			$return = 'Registered user (useradmin)';
			break;
		case 6:
			$return = 'Ticket ordered';
			break;
		case 7:
			$return = 'Ticket canceled';
			break;
		case 8:
			$return = 'Changed password';
			break;
		case 9:
			$return = 'Changed userinfo';
			break;
		case 10:
			$return = 'Onsiteticket ordered';
			break;
		case 11:
			$return = 'Changed ticket paystatus';
			break;
		case 12:
			$return = 'Deleted ticket';
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

	if (!db_num ($result))
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

##### tickettype_getname - returns name of tickettype, false if nonexistant
function tickettype_getname ($typeid)
{
	global $sql_prefix;

	$query = sprintf ('SELECT name FROM %s_ticketTypes WHERE ticketTypeID=%s', $sql_prefix, db_escape ($typeid));
	$result = db_query ($query);

	if (!db_num ($result))
	{
		return (false);
	}
	else
	{
		$result = db_query ($query);
		$fetch = db_fetch ($result);
		return ($fetch->name);
	}
}


##### user_getpass - returns md5 of password for userid
function user_getpass ($userid)
{
	global $sql_prefix;

	$query = sprintf ('SELECT password FROM %s_users WHERE ID=%s', $sql_prefix, db_escape ($userid));
	$result = db_query ($query);

	if (!db_num ($result))
	{
		// no such user?!
		return (false);
	}
	else
	{
		$result = db_query ($query);
		$fetch = db_fetch ($result);
		return ($fetch->password);
	}
}

######## user_setpass - takes userid and md5 as parameters and sets password for user, returns true if done and false if no such user
function user_setpass ($userid, $md5)
{
	global $sql_prefix;

	$oldpass = user_getpass ($userid);
	if (!$oldpass)
	{
		return (false);
	}
	else
	{
		if ($oldpass == $md5)
		{
			return (true);
		}
		else
		{
			$query = sprintf ('UPDATE %s_users SET password="%s" WHERE ID=%s', $sql_prefix, db_escape ($md5), db_escape ($userid));
			db_query ($query);
			return (true);
		}
	}
}


##### user_getall - returns array with all userinfo->objects
function user_getall ($columns=array())
{
	global $sql_prefix;

    if (count($columns) < 1) {
        $columns = array('*');
    }

    // Escape all variables
    array_walk($columns, '_privateUserGetallWalk');

    $query = 'SELECT ' . implode(", ", $columns) . ' FROM ' . $sql_prefix . '_users WHERE ID>1';
	$result = db_query ($query);
    $return = array();
	while ($fetch = db_fetch ($result))
	{
		$return[] = $fetch;
	}
	return $return;
}

function _privateUserGetallWalk(&$item, $key) {
    $item = db_escape($item);
}

##### user_exists - returns true if userid exists, false if not
function user_exists ($userid)
{
	global $sql_prefix;

	$query = sprintf ('SELECT ID from %s_users WHERE ID=%s', $sql_prefix, db_escape ($userid));
	$result = db_query ($query);

	if (db_num ($result))
	{
		return (true);
	}
	else
	{
		return (false);
	}

}

/**
 * Searches after users in the database by nick, firstName, lastName, firstName and lastName, and email ordered by user ID.
 *
 * @param $query
 * @return array
 */
function user_find($query) {
    global $sql_prefix;

    $users = array();
    if (empty($query))
        return $users;

    $usersQ = sprintf("SELECT nick, firstName, lastName, ID FROM %s WHERE ID > 1 AND
      (nick LIKE '%%%s%%' OR firstName LIKE '%%%s%%' OR lastName LIKE '%%%s%%' OR CONCAT(firstName, ' ', lastName) LIKE '%%%s%%' OR EMail LIKE '%%%s%%') ORDER BY ID",
        $sql_prefix."_users", $query, $query, $query, $query, $query);

    $getUsers = db_query($usersQ);
    if (db_num($getUsers) > 0) {
        $users = db_fetch_all($getUsers);
    }

	return $users;
}

function kiosk_item_price($wareID) {
	global $sql_prefix;

	$qDefaultPrice = db_query("SELECT price FROM ".$sql_prefix."_kiosk_wares WHERE ID = '".db_escape($wareID)."'");
	$rDefaultPrice = db_fetch($qDefaultPrice);
	return $rDefaultPrice->price;
}



##### userprofile -- give it a userID, and it will return link to profile + name
function user_profile ($userid) {
	global $sql_prefix;

	$qFindUser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '".db_escape($userid)."'");
	$rFindUser = db_fetch($qFindUser);

	$return = "<a href='?module=profile&user=$rFindUser->ID'>";
	if(!empty($rFindUser->firstName) && !empty($rFindUser->lastName)) {
		$return .= $rFindUser->firstName." ".$rFindUser->lastName." ";
		$return .= lang("a.k.a.", "functions-user_profile");
		$return .= " ";
	}
	$return .= $rFindUser->nick;
	$return .= "</a>";
	return $return;
}


##### userinfo -- give it a userID, and it will return array with users information
function user_info ($userid) {
	global $sql_prefix;

	$return = array();

	$qFindUser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '".db_escape($userid)."'");
	$nFindUser = db_num($qFindUser);

	if ($nFindUser > 0) {
		$rFindUser = db_fetch($qFindUser);
		$return['ID'] = $rFindUser->ID;
		$return['nick'] = $rFindUser->nick;
		$return['fName'] = $rFindUser->firstName;
		$return['lName'] = $rFindUser->lastName;
		$return['allName'] = $rFindUser->firstName . " " . $rFindUser->lastName;
		$return['email'] = $rFindUser->EMail;
		$return['emailVerified'] = $rFindUser->EMailConfirmed == 1 ? true : false;
		$return['globalAdmin'] = $rFindUser->globaladmin == 1 ? true : false;
	}
	return count($return) > 0 ? $return : false;
}


function send_email ($userID, $subject, $content) {
	global $sql_prefix;
	global $sessioninfo;

	db_query("INSERT INTO ".$sql_prefix."_cronjobs SET
		cronModule = 'MAIL',
		toUser = '".db_escape($userID)."',
		content = '".db_escape($content)."',
		subject = '".db_escape($subject)."',
		senderID = '$sessioninfo->userID'");

	return true;
}

## returns an array of available designs
function list_designs ()
{
	$path = (dirname($_SERVER['SCRIPT_FILENAME']));
	$all_files = scandir ($path."/templates/");

	$dirs = array ();

	foreach ($all_files as $dir)
	{
		if (is_dir($path."/templates/".$dir) and $dir != "shared" and $dir != "." and $dir != "..")
		{
			$dirs[] = $dir;
		}

	}
	return ($dirs);
}

# this returns the group name, given an ID
function get_groupname ($groupid)
{
	global $sql_prefix;
	$qGroup = db_query ("SELECT groupname FROM ".$sql_prefix."_groups WHERE ID='".db_escape($groupid)."'");
	if (!db_num ($qGroup))
	{
		return (false);
	}
	$rGroup = db_fetch ($qGroup);
	return ($rGroup->groupname);
}

/**
 * Indicates if a user if sleeping.
 *
 * @param int $userID
 * @param int $eventID If null then current event ID is used.
 * @return bool
 */
function is_user_sleeping($userID, $eventID=null) {
	global $sql_prefix,$sessioninfo;

	if ($eventID == null)
		$eventID = $sessioninfo->eventID;

	$table = $sql_prefix . "_sleepers";
	$isSleepingQuery = db_query(sprintf("SELECT userID FROM %s WHERE userID = %s AND eventID = %s", $table, $userID, $eventID));

	return db_num($isSleepingQuery) > 0 ? true : false;
}

/**
 * Indicates if a user is crew, user is considered a crew-member when in a group with type "access".
 *
 * @param int $userID
 * @param int $eventID If null then current event ID is used.
 * @return bool
 */
function is_user_crew($userID, $eventID=null) {
	global $sql_prefix,$sessioninfo;

	if ($eventID == null)
		$eventID = $sessioninfo->eventID;

	$query = db_query(sprintf("SELECT g.ID FROM %s as g, %s as gm
								WHERE g.ID = gm.groupID AND g.groupType='access' AND (gm.userID=%s AND g.eventID=%s)",
		$sql_prefix . "_groups", $sql_prefix . "_group_members", $userID, $eventID));

	return db_num($query) > 0 ? true : false;
}

function eventMenuItemCssClasses($module) {
	$currentModule = (isset($_GET['module']) ? $_GET['module']: '');
	return ($currentModule == $module ? ' class="active"' : '');
}

/**
 * Returns the config by name provided from module config.
 * If the config is not found the default-argument is returned.
 *
 * @param string $module
 * @param string $name
 * @param mixed $default
 * @return mixed
 */
function getModuleConfig($module, $name, $default=false) {
	global $_MODULECONFIG;

	if (isset($_MODULECONFIG[$module]) && isset($_MODULECONFIG[$module][$name])) {
		return $_MODULECONFIG[$module][$name];
	}

	return $default;
}

/**
 * @param int $errorCode
 */
function printNoAccessError($errorCode=1) {
	header("Location: index.php?aclDeniedCode=" . $errorCode);
	die();
}

/**
 * @param int $errorCode
 */
function printErrorPage($errorCode=1) {
	header("Location: index.php?_error=" . $errorCode);
	die();
}

/**
 * @return string
 */
function getUrlBase() {
	$scriptName = $_SERVER['SERVER_NAME'];
	$httpBase = "http://";
	$suffix = "";

	if (isset($_SERVER['HTTPS']) && strlen(trim($_SERVER['HTTPS'])) > 0) {
		$httpBase = "https://";
	}

	// Apply port if its not 80 or 443
	if (isset($_SERVER['SERVER_PORT']) && (isset($_SERVER['PORT']) && $_SERVER['PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443)) {
		$suffix = ":" . $_SERVER['SERVER_PORT'];
	}

	return $httpBase . $scriptName . $suffix;
}

function getUserIP() {
	$client = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote = $_SERVER['REMOTE_ADDR'];

	if (filter_var($client, FILTER_VALIDATE_IP)) {
		$ip = $client;
	} elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
		$ip = $forward;
	} else {
		$ip = $remote;
	}

	return $ip;
}

/**
 * Flip array_map
 *
 * @param array $array
 * @param callback $callback
 *
 * @return array
 */
function map_array($array, $callback)
{
	return array_map($callback, $array);
}

function array_first(array $array)
{
	return array_values($array)[0] ?? null;
}
