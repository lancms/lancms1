<?php

## DB-connection

$sql_type = "mysql"; // SQL type. Valid are... mysql actually
$sql_host = "localhost"; // SQL Host
$sql_user = "OSGL"; // SQL username
$sql_pass = "ComPuterParty"; // Very very secret, if you read this, you should probably go shoot yourself, just to be safe
$sql_base = "OSGL"; // The database to use
$sql_prefix = "osgl"; // Someone asked for this a while back. prefix, and _ is added automatically



$osgl_session_cookie = "OSGL2-cake";

## All other settings should be done in some kind of installer....



global $sql_type;
global $osgl_session_cookie;
global $sql_prefix;