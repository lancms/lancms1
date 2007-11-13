<?php

require 'config.php';


# Start DB-connection

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
require 'inc/shared_session.php';


## Set up Smarty-stuff
require 'inc/smarty/libs/Smarty.class.php';
$smarty = new Smarty();

$smarty->template_dir = 'templates/';
$smarty->compile_dir = 'tmp/templates_compile';
$smarty->config_dir = 'inc/smarty/';
$smarty->cache_dir = 'tmp/templates_cache/';



## This should be fixed to something... dynamic
#$smarty_display = 'simple/simple.tpl';
$smarty_display = 'GlobeLAN11/GlobeLAN11.tpl';




## Define diffrent variables
$seattype['b'] = 'Blank';
$seattype['d'] = 'Open seat';
$seattype['p'] = 'Password-protected seat';
$seattype['g'] = 'Group-protected seat';
$seattype['t'] = 'Text';
$seattype['w'] = 'Wall';
$seattype['o'] = 'Door';
$seattype['a'] = 'Area';

