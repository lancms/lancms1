<?php


$qFindJobs = db_query("SELECT * FROM ".$sql_prefix."_cronjobs WHERE cronModule = 'SMS' AND (finishTime < 1 OR finishTime IS NULL) LIMIT $cron_limit");
$rFindJobs = db_fetch_all($qFindJobs);

if ((is_array($rFindJobs)) && (count($rFindJobs) > 0)) {
    foreach ($rFindJobs as $job) {
        $message = str_replace(" ", "%20", $job->content);
        $message = utf8_decode($message);
        $URL = sprintf(
            'http://api.clickatell.com/http/sendmsg?user=%s&password=%s&api_id=3600328&to=%s&text=%s',
    		$SMS_user, $SMS_pass, $job->toUser, $message
        );
        
        $fp = fopen("$URL", "r");
    	db_query(
            sprintf(
                "UPDATE %s_cronjobs SET finishTime = '%s' WHERE jobID = '%s'",
                $sql_prefix, time(), $job->jobID
            )
        );

    }
}
