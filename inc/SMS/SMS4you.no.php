<?php


$qFindJobs = db_query("SELECT * FROM ".$sql_prefix."_cronjobs WHERE cronModule = 'SMS' LIMIT 0,1");

while($rFindJobs = db_fetch($qFindJobs)) {


	$URL = "http://gate1.sms4you.no/kundesenter/sendsms.php?user=".$SMS_user."&pass=".$SMS_pass."&fromAlpha=".$SMS_from."&destination=".$rFindJobs->toUser."&message=".urlencode($rFindJobs->content);
#	echo $URL;
	$fp = fopen($URL, "r");
	db_query("UPDATE ".$sql_prefix."_cronjobs SET finishTime = '".time()."' WHERE jobID = '$rFindJobs->jobID'");
}
