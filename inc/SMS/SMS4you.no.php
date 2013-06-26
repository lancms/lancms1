<?php


$qFindJobs = db_query("SELECT * FROM ".$sql_prefix."_cronjobs WHERE cronModule = 'SMS' AND (finishTime < 1 OR finishTime IS NULL) LIMIT $cron_limit");

while($rFindJobs = db_fetch($qFindJobs)) {

	$message = str_replace(" ", "%20", $rFindJobs->content);
	$message = utf8_decode($message);
#	$URL = "http://gate1.sms4you.no/kundesenter/sendsms.php?user=".$SMS_user."&pass=".$SMS_pass."&fromAlpha=".$SMS_from."&destination=".$rFindJobs->toUser."&message=".$message;
   $URL = "http://193.142.108.131/ActiveServer/MT/?username=It4You&password=sms6777&refno=1234&sourceaddr=1963&fromAlpha=GlobeLAN&destinationaddr=".$rFindJobs->toUser."&message=".$message;
#	echo $URL;
	$fp = fopen("$URL", "r");
	db_query("UPDATE ".$sql_prefix."_cronjobs SET finishTime = '".time()."' WHERE jobID = '$rFindJobs->jobID'");
}
