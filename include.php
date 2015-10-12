<?php

use Symfony\Component\HttpFoundation\Request;

error_reporting(E_ALL ^ E_NOTICE);

session_start();

require_once __DIR__ . '/vendor/autoload.php';

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$request = Request::createFromGlobals();
$requestGet = $request->query;
$requestPost = $request->request;

#require 'inc/php-gettext/gettext.inc';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/inc/db_functions.php';

require_once __DIR__ . "/inc/lib/HtmlElement.php";

// Include common libs
require_once __DIR__ . "/inc/lib/SqlObject.php";
require_once __DIR__ . "/inc/lib/EventSqlObject.php";
require_once __DIR__ . "/inc/lib/User.php";
require_once __DIR__ . "/inc/lib/UserGroup.php";
require_once __DIR__ . "/inc/lib/UserGroupMember.php";
require_once __DIR__ . "/inc/lib/UserGroupManager.php";
require_once __DIR__ . "/inc/lib/UserManager.php";
require_once __DIR__ . "/inc/lib/TicketManager.php";
require_once __DIR__ . "/inc/lib/NewsArticleManager.php";
require_once __DIR__ . "/inc/lib/wannabe/Manager.php";

# Start DB-connection
db_connect();

## Includes etc.
require __DIR__ . '/inc/shared_functions.php';
require __DIR__ . '/inc/shared_session.php';

## Include and set up i18n
if($language == 'norwegian') $lang = 'nb_NO.utf8';
putenv("LANGUAGE=$lang");
putenv("LANG=$lang");
setlocale (LC_ALL, $lang);
bindtextdomain("messages", __DIR__ . "/i18n");
bind_textdomain_codeset("messages", "UTF-8");
textdomain("messages");

require __DIR__ . '/inc/shared_config.php';

if($hide_smarty != 1 && $enableSmarty == true) {
	## Set up Smarty-stuff
	require __DIR__ . '/inc/smarty/libs/Smarty.class.php';
	$smarty = new Smarty();

	$smarty->template_dir = __DIR__ . '/html/templates/';
	$smarty->compile_dir = __DIR__ . '/tmp/templates_compile';
	$smarty->config_dir = __DIR__ . '/inc/smarty/';
	$smarty->cache_dir = __DIR__ . '/tmp/templates_cache/';



	## This should be fixed to something... dynamic
	#$smarty_display = 'simple/simple.tpl';
	#$smarty_display = 'GlobeLAN11/GlobeLAN11.tpl';
	if(file_exists($smarty->template_dir.$eventinfo->eventDesign."/".$eventinfo->eventDesign.".tpl")) {
		$smarty_display = $eventinfo->eventDesign."/".$eventinfo->eventDesign.".tpl";
	}
	else $smarty_display = 'Alfa1/Alfa1.tpl';
	#else $smarty_display = 'LarvikLAN/LarvikLAN.tpl';
#	else die("Design doesn't exists");

} // End $hide_smarty != 1


#if($cron_type != 'cron') {
// If remote addr is set, include cron-stuff
if(isset($_SERVER['REMOTE_ADDR'])) { 

	if(isset($use_SMS_system) && $use_SMS_system == "SMS4you.no" && !empty($SMS_from) && !empty($SMS_user) && !empty($SMS_pass)) {
        	include_once __DIR__ . '/inc/SMS/SMS4you.no.php';

	}
	include_once __DIR__ . '/inc/EMail/sendmail.php';
}
