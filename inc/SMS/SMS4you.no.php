<?php


$qFindJobs = db_query("SELECT * FROM ".$sql_prefix."_cronjobs WHERE cronModule = 'SMS' AND (finishTime < 1 OR finishTime IS NULL) LIMIT 0,20");

while($rFindJobs = db_fetch($qFindJobs)) {


	$URL = "http://gate1.sms4you.no/kundesenter/sendsms.php?user=".$SMS_user."&pass=".$SMS_pass."&fromAlpha=".$SMS_from."&destination=".$rFindJobs->toUser."&message=".$rFindJobs->content;
#	echo $URL;
	$fp = fopen("$URL", "r");
	db_query("UPDATE ".$sql_prefix."_cronjobs SET finishTime = '".time()."' WHERE jobID = '$rFindJobs->jobID'");
}
