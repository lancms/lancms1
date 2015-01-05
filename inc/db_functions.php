<?php
/**
 * @author edvin
 */

/**
 * Simple holder for mysqli.
 * @var $mysqli mysqli
 */
$mysqli = null;

/**
 * Connects to database using config, supported types: mysql and mysqli (recommended)
 *
 * @return bool
 */
function db_connect() {
    global $sql_type, $sql_host, $sql_user, $sql_pass, $sql_base, $mysqli;
    switch ($sql_type)
    {
        case "mysql":
            mysql_connect($sql_host, $sql_user, $sql_pass) or die("Could not connect to MySQL-host. Error is: ".mysql_error());
            ## This might jump to installer......
            mysql_select_db($sql_base) or die("Could not select MySQL DB. Error is: ".mysql_error());
            break;
        case "mysqli":
            $mysqli = new mysqli($sql_host, $sql_user, $sql_pass, $sql_base);
            if ($mysqli->connect_error) {
                die('Could not connect to MySQL-host. Error is: ' . $mysqli->connect_error);
            }
			break;
        default:
            die("Something seriously wrong with variable sql_type in function db_connect. The reported sql_type is '$sql_type'");

    } // end switch ($sql_type)
    return true;
}

/**
 * Disconnects the database connection.
 */
function db_close() {
    global $sql_type,$mysqli;
    switch ($sql_type) {
        case "mysql":
            mysql_close();
            break;
        case "mysqli":
            if ($mysqli != null)
                $mysqli->close();
            break;
        default:
            die("Something seriously wrong with variable sql_type in function db_close. The reported sql_type is '$sql_type'");
    }
}

/**
 * Send a query to the database.
 *
 * @param $query
 * @return bool|mysqli_result|resource
 */
function db_query($query)
{
    /* Function to do queries to/from DBs */
    global $sql_type,$mysqli;
    switch ($sql_type)
    {
        case "mysql":
            $q = mysql_query($query) or die("MYSQL Error with query (".$query.") because of: ".mysql_error());
            break;
        case "mysqli":
            $q = $mysqli->query($query);
            if ($q == false) {
                die("MYSQLi Error with query (".$query.") because of: ".$mysqli->error);
            }
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
        case "mysqli":
            if (!($query instanceof mysqli_result)) {
                return array();
            }
            $return = $query->fetch_object();
            break;
        default:
            die("Something seriously wrong with variable sql_type in function " . __FUNCTION__);
    } // End switch ($sql_type)

    return $return;
} // End function db_fetch

/**
 * returns assoc array instead of object
 *
 * @param $query
 * @return array
 */
function db_fetch_assoc($query) {
    global $sql_type;
    /* Function to fetch results from db_query */
    switch ($sql_type)
    {
        case "mysql":
            $return = mysql_fetch_assoc($query); // Memo to self: can not die, as it should return false if nothing there
            break;
        case "mysqli":
            if (!($query instanceof mysqli_result)) {
                return array();
            }
            $return = $query->fetch_assoc();
            break;
        default:
            die("Something seriously wrong with variable sql_type in function " . __FUNCTION__);
    } // End switch ($sql_type)

    return $return;
} // End function db_fetch

/**
 * return db_num_fields
 *
 * @param $query
 * @return int
 */
function db_num_fields($query) {
    global $sql_type;

    switch ($sql_type) {
        case "mysql":
            $return = mysql_num_fields($query);
            break;
        case "mysqli":
            if (!($query instanceof mysqli_result)) {
                return 0;
            }
            $return = $query->field_count;
            break;
        default:
            die("Something seriously wrong with variable sql_type in function " . __FUNCTION__);
    } // End switch ($sql_type)

    return $return;
} // End function db_num_fields


###
/**
 * Returns an object which contains field definition information from the specified result set.
 *
 * @param $query
 * @param $column_num
 * @return string
 */
function db_field_name($query, $column_num) {
    global $sql_type;

    switch($sql_type) {
        case "mysql":
            $return = mysql_field_name($query, $column_num);
            break;
        case "mysqli":
            if (!($query instanceof mysqli_result)) {
                return false;
            }
            $return = $query->fetch_field_direct($column_num);
            break;
        default:
            die("Something seriously wrong with variable sql_type in function " . __FUNCTION__);

    } // End switch

    return $return;
} // End function db_field_name

/**
 * Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
 *
 * @param $var
 * @return string
 */
function db_escape($var)
{
    global $sql_type,$mysqli;
    /* Function to escape strings before they are inserted to the DB. Should avoid some haxxoring, so should probably use it... */
    switch ($sql_type)
    {
        case "mysql":
            $return = mysql_real_escape_string($var);
            break;
        case "mysqli":
            $return = $mysqli->real_escape_string($var);
            break;
        default:
            die("Something seriously wrong with variable sql_type in function " . __FUNCTION__);
    } // End switch ($sql_type)

    return $return;

} // End function db_escape

/**
 * Gets the number of rows in a result
 *
 * @param $q
 * @return int
 */
function db_num ($q)
{
    global $sql_type;
    switch ($sql_type)
    {
        case "mysql":
            $return = mysql_num_rows($q);
            break;
        case "mysqli":
            if (!($q instanceof mysqli_result)) {
                return false;
            }
            $return = $q->num_rows;
            break;
        default:
            die("Something seriously wrong with variable sql_type in function " . __FUNCTION__);
    } // End switch ($sql_type)

    return $return;
}

/**
 * Returns the auto generated id used in the last query
 *
 * @return int|mixed
 */
function db_insert_id() {
    global $sql_type,$mysqli;
    switch ($sql_type)
    {
        case "mysql":
            $return = mysql_insert_id();
            break;
        case "mysqli":
            $return = $mysqli->insert_id;
            break;
        default:
            die("Something seriously wrong with variable sql_type in function " . __FUNCTION__);
    } // End switch ($sql_type)

    return $return;
}