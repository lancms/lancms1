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
function config($config, $value = "NOTSET")
{
	$query = db_query("SELECT * FROM config WHERE config = '".db_escape($config)."'");
	$num = db_num($query);
	if ($value == "NOTSET") // No value is set. We should only SELECT to find out what the value is.
	{
		$object = fetch($query);

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
			return $object->value;
		}
	} // End if value == NOTSET
	else // $value IS set, so we should write/update that config
	{
		if ($num == 0) // That config doesn't exists yet. Insert it
		{
			query("INSERT INTO config SET config = '".db_escape($config)."', value = '".db_escape($value)."'");
		}
		else // That config exists. Update the existsing
		{
			query("UPDATE config SET value = '".db_escape($value)."' WHERE config = '".db_escape($config)."'");
		}
	} // End else

}


######################################################
function acl_access($module, $subcategory=0, $event=0, $user = "CHECK")
{
	// This function outputs if user has rights to something
	// FIXME: This might have to be extended to something....
	return TRUE;
}




######################################################