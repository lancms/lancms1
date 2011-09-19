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


## Include and set up i18n
if($language == 'norwegian') $lang = 'nb_NO.utf8';
putenv("LANGUAGE=$lang");
putenv("LANG=$lang");
setlocale (LC_ALL, $lang);
bindtextdomain("messages", "./i18n");
bind_textdomain_codeset("messages", "UTF-8");
textdomain("messages");


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


if($cron_type != 'cron') {

	if($use_SMS_system == "SMS4you.no" && !empty($SMS_from) && !empty($SMS_user) && !empty($SMS_pass)) {
        	include_once 'inc/SMS/SMS4you.no.php';

	}
	include_once 'inc/EMail/sendmail.php';
}
