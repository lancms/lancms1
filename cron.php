<?php

$hide_smarty = 1;


if($use_SMS_system == "SMS4you.no" && !empty($SMS_from) && !empty($SMS_user) && !empty($SMS_pass)) {
	include_once 'inc/SMS/SMS4you.no.php';

}