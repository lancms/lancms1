<?php


$qFindJobs = db_query("SELECT * FROM ".$sql_prefix."_cronjobs WHERE cronModule = 'SMS' LIMIT 0,1");

while($rFindJobs = db_fetch($qFindJobs)) {


	$fp = fopen("http://gate1.sms4you.no/kundesenter/sendsms.php?user=".$SMS_user."&pass=".$SMS_pass."&fromid=".$SMS_from."&destination=".$rFindJobs->toUser."&message=".url_encode($rFindJobs->content), "r");
}