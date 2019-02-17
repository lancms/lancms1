<?php

## DB-connection
$sql_type = "mysqli"; // SQL type. mysqli or mysql, recommended is mysqli because mysql is deprecated.
$sql_host = "localhost"; // SQL Host
$sql_user = "lancms"; // SQL username
$sql_pass = "ComPuterParty"; // Very very secret, if you read this, you should probably go shoot yourself, just to be safe
$sql_base = "lancms"; // The database to use
$sql_prefix = "GO"; // Someone asked for this a while back. prefix, and _ is added automatically

$lancms_session_cookie = "lancms-cake";
$language = "norwegian"; // The user might want to customize this him self... Might be a FIXME

$design_title = "lancms";

/**
 * Allow pay on arrival?
 * @var int
 */
$ticketOrderAllowPreorderPayOnArrival = false;

// Define it to use smarty or a native solution by including a template file in php.
$enableSmarty = false;

// Facebook Login
$facebook_appID = "";
$facebook_login = FALSE;

// Facebook likebox
// Remove # and point to your Facebook page URL:
#	$facebook_likebox_url = 'http://www.facebook.com/GlobeLAN';

//----------------------------------------------------------------------------
// Payment config
$stripePaymentConfig = array(
    "secretKey" => "",
    "privateKey" => "",
    "imageLogo" => "",
    "companyName" => ""
);
//----------------------------------------------------------------------------

## Mail settings
$mail_from = 'noreply@globeorg.no';


# Put something usefull here if you want your logo in the page footer
# if you don't want it, comment it out
$design_footer['logo'] = 'http://default.globeorg.no/go.png';
$design_footer['width'] = 69;
$design_footer['height'] = 90;
$design_footer['url'] = 'http://default.globeorg.no/';
	

# cron type
$cron_type = 'include'; // Can be include or cron



## don't touch, includes override config if it exists
if(file_exists(__DIR__ . "/OverrideConfig.php")) {
    include_once __DIR__ . '/OverrideConfig.php';
}

global $sql_type;
global $lancms_session_cookie;
global $sql_prefix;
