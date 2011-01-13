<?php

## DB-connection

if(file_exists("OverrideConfig.php")) {
    include_once 'OverrideConfig.php';
}

else {

    $sql_type = "mysql"; // SQL type. Valid are... mysql actually
    $sql_host = "localhost"; // SQL Host
    $sql_user = "lancms"; // SQL username
    $sql_pass = "ComPuterParty"; // Very very secret, if you read this, you should probably go shoot yourself, just to be safe
    $sql_base = "lancms"; // The database to use
    $sql_prefix = "lancms"; // Someone asked for this a while back. prefix, and _ is added automatically



    $lancms_session_cookie = "lancms-cake";
    $language = "norwegian"; // The user might want to customize this him self... Might be a FIXME

    $design_title = "lancms";

	$facebook_appID = "";
	$facebook_login = TRUE;

	## Mail settings
	$mail_from = 'noreply@globeorg.no';
}
## All other settings should be done in some kind of installer....



global $sql_type;
global $lancms_session_cookie;
global $sql_prefix;
