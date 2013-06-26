<?php
if(!isset($_SERVER['REMOTE_ADDR'])) {
	$hide_smarty = 1;
	include_once 'include.php';
}

if($use_SMS_system == "SMS4you.no" && !empty($SMS_from) && !empty($SMS_user) && !empty($SMS_pass)) {
	include_once 'inc/SMS/SMS4you.no.php';

}

include_once 'inc/EMail/sendmail.php';
