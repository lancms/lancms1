<?php
#require 'inc/php-gettext/gettext.inc';
require 'config.php';
require 'inc/db_functions.php';

// Include common libs
require __DIR__ . "/inc/lib/SqlObject.php";
require __DIR__ . "/inc/lib/TicketManager.php";


# Start DB-connection
db_connect();



## Includes etc.
require 'inc/shared_functions.php';
require 'inc/shared_session.php';

## Include and set up i18n
if($language == 'norwegian') $lang = 'nb_NO.utf8';
putenv("LANGUAGE=$lang");
putenv("LANG=$lang");
setlocale (LC_ALL, $lang);
bindtextdomain("messages", "./i18n");
bind_textdomain_codeset("messages", "UTF-8");
textdomain("messages");

require 'inc/shared_config.php';

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
	else $smarty_display = 'Alfa1/Alfa1.tpl';
	#else $smarty_display = 'LarvikLAN/LarvikLAN.tpl';
#	else die("Design doesn't exists");

} // End $hide_smarty != 1


#if($cron_type != 'cron') {
// If remote addr is set, include cron-stuff
if(isset($_SERVER['REMOTE_ADDR'])) { 

	if($use_SMS_system == "SMS4you.no" && !empty($SMS_from) && !empty($SMS_user) && !empty($SMS_pass)) {
        	include_once 'inc/SMS/SMS4you.no.php';

	}
	include_once 'inc/EMail/sendmail.php';
}
