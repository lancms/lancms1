<?php

$ua = $_SERVER['HTTP_USER_AGENT'];

if (preg_match ('/Chrome/', $ua))
{
	require_once ("extra_chrome.css");
}
else
{
	require_once ("extra_normal.css");
}


?>
