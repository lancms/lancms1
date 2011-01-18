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
require 'inc/shared_config.php';

	
error_reporting(E_ALL | E_STRICT);

define('LOCALE_DIR', './i18n');
define('DEFAULT_LOCALE', 'en_US');
if($language == 'norwegian') $locale = 'nb';



if(!function_exists('gettext')) {
	include_once 'inc/php-gettext/gettext.php';
	include_once 'inc/php-gettext/streams.php';
	include_once 'inc/php-gettext/gettext.inc';
}

setlocale(LC_MESSAGES, $locale);
bindtextdomain("i18n", './i18n');
textdomain('i18n');
#$domain = 'i18n';
#bindtextdomain($domain, LOCALE_DIR);




if($hide_smarty != 1) {
	## Set up Smarty-stuff
	require 'inc/smarty/libs/Smarty.class.php';
	$smarty = new Smarty();

	$smarty->template_dir = 'templates/';
	$smarty->compile_dir = 'tmp/templates_compile';
	$smarty->config_dir = 'inc/smarty/';
	$smarty->cache_dir = 'tmp/templates_cache/';



	## This should be fixed to something... dynamic
	#$smarty_display = 'simple/simple.tpl';
	#$smarty_display = 'GlobeLAN11/GlobeLAN11.tpl';
	if(file_exists($smarty->template_dir."/".$eventinfo->eventDesign."/".$eventinfo->eventDesign.".tpl")) {
		$smarty_display = $eventinfo->eventDesign."/".$eventinfo->eventDesign.".tpl";
	}
	#else $smarty_display = 'LarvikLAN/LarvikLAN.tpl';
	else die("Design doesn't exists");

} // End $hide_smarty != 1

