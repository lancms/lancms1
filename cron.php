<?php
if(!isset($_SERVER['REMOTE_ADDR'])) {
	$hide_smarty = 1;
	include_once 'include.php';
}

if ((isset($use_SMS_system)) && (!empty($SMS_from) && !empty($SMS_user) && !empty($SMS_pass))) {
	switch ($use_SMS_system) {
		case 'SMS4you.no':
		case 'clickatell':
			include_once 'inc/SMS/'.$use_SMS_system.'.php';
			break;
	}
}

include_once 'inc/EMail/sendmail.php';
