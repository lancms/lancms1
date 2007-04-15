<?php

## DB-connection

$sql_type = "mysql"; // SQL type. Valid are... mysql actually
$sql_host = "localhost"; // SQL Host
$sql_user = "OSGL"; // SQL username
$sql_pass = "ComPuterParty"; // Very very secret, if you read this, you should probably go shoot yourself, just to be safe
$sql_base = "OSGL"; // The database to use
$sql_prefix = "osgl_"; // Someone asked for this a while back.


## All other settings should be done in some kind of installer....



# Start DB-connection
global $sql_type;
switch ($sql_type)
{
	case "mysql":
		mysql_connect($sql_host, $sql_user, $sql_pass) or die("Could not connect to MySQL-host. Error is: ".mysql_error());
		## This might jump to installer......
		mysql_select_db($sql_base) or die("Could not select MySQL DB. Error is: ".mysql_error());
		break;
	default:
		die("Holy shit, someone forgot to set sql_type in config.php to something valid!");
} // End switch $sql_type



## Includes etc.
require 'inc/shared_functions.php';


## Set up Smarty-stuff
require 'inc/smarty/libs/Smarty.class.php';
$smarty = new Smarty();

$smarty->template_dir = 'templates/';
$smarty->compile_dir = 'tmp/templates_compile';
$smarty->config_dir = 'inc/smarty/';
$smarty->cache_dir = 'tmp/templates_cache/';



## This should be fixed to something... dynamic
$smarty_display = 'simple/simple.tpl';

