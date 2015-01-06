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
 * Holder of mysql connection.
 */
$mysqlLink = null;

/**
 * Connects to database using config, supported types: mysql and mysqli (recommended)
 *
 * @param $loginInfo array Contains login info instead of params.
 * @return mixed returns mysqli class when using mysqli and link resource for mysql.
 */
function db_connect($loginInfo=null) {
    global $sql_type, $sql_host, $sql_user, $sql_pass, $sql_base, $mysqlLink, $mysqli;

    // Use $loginInfo?
    if (is_array($loginInfo)) {
        if (isset($loginInfo['host']))
            $sql_host = $loginInfo['host'];

        if (isset($loginInfo['user']))
            $sql_user = $loginInfo['user'];

        if (isset($loginInfo['pass']))
            $sql_user = $loginInfo['pass'];

        if (isset($loginInfo['db']))
            $sql_user = $loginInfo['db'];
    }

    switch ($sql_type)
    {
        case "mysql":
            $mysqlLink = mysql_connect($sql_host, $sql_user, $sql_pass) or die("Could not connect to MySQL-host. Error is: ".mysql_error());
            ## This might jump to installer......
            mysql_select_db($sql_base, $mysqlLink) or die("Could not select MySQL DB. Error is: ".mysql_error());
            return $mysqlLink;
        case "mysqli":
            $mysqli = new mysqli($sql_host, $sql_user, $sql_pass, $sql_base);
            if ($mysqli->connect_error) {
                die('Could not connect to MySQL-host. Error is: ' . $mysqli->connect_error);
            }
			return $mysqli;
        default:
            die("Something seriously wrong with variable sql_type in function db_connect. The reported sql_type is '$sql_type'");

    } // end switch ($sql_type)
}

/**
 * Disconnects the database connection.
 *
 * @param $res
 */
function db_close($res=null) {
    global $sql_type,$mysqlLink,$mysqli;

    switch ($sql_type) {
        case "mysql":
            mysql_close(($res == null ? $mysqlLink : $res));
            break;
        case "mysqli":
            if ($res == null)
                $res = $mysqli;

            if ($res != null)
                $res->close();
            break;
        default:
            die("Something seriously wrong with variable sql_type in function db_close. The reported sql_type is '$sql_type'");
    }
}

/**
 * Send a query to the database.
 *
 * @param $query
 * @param $res
 * @return bool|mysqli_result|resource
 */
function db_query($query, $res=null)
{
    /* Function to do queries to/from DBs */
    global $sql_type,$mysqli,$mysqlLink;
    switch ($sql_type)
    {
        case "mysql":
            $q = mysql_query($query, ($res == null ? $mysqlLink : $res)) or die("MYSQL Error with query (".$query.") because of: ".mysql_error());
            break;
        case "mysqli":
            if ($res == null)
                $res = $mysqli;

            $q = $res->query($query);
            if ($q == false) {
                die("MYSQLi Error with query (".$query.") because of: ".$res->error);
            }
			break;
        default:
            die("Something seriously wrong with variable sql_type in function db_query. The reported sql_type is '$sql_type'");

    } // end switch ($sql_type)

    return $q;
} // End function db_query()

/**
 * Returns the current row of a result set as an object
 *
 * @param $query
 * @return array|object|stdClass
 */
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
 * @param $res
 * @return string
 */
function db_escape($var, $res=null)
{
    global $sql_type,$mysqli,$mysqlLink;
    /* Function to escape strings before they are inserted to the DB. Should avoid some haxxoring, so should probably use it... */
    switch ($sql_type)
    {
        case "mysql":
            $return = mysql_real_escape_string($var, ($res == null ? $mysqlLink : $res));
            break;
        case "mysqli":
            if ($res == null)
                $res = $mysqli;

            $return = $res->real_escape_string($var);
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
 * @param $res
 * @return int|mixed
 */
function db_insert_id($res=null) {
    global $sql_type,$mysqli,$mysqlLink;
    switch ($sql_type)
    {
        case "mysql":
            $return = mysql_insert_id(($res == null ? $mysqlLink : $res));
            break;
        case "mysqli":
            if ($res == null)
                $res = $mysqli;

            $return = $res->insert_id;
            break;
        default:
            die("Something seriously wrong with variable sql_type in function " . __FUNCTION__);
    } // End switch ($sql_type)

    return $return;
}