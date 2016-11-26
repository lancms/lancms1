<?php


$qFindJobs = db_query("SELECT * FROM ".$sql_prefix."_cronjobs WHERE cronModule = 'SMS' AND (finishTime < 1 OR finishTime IS NULL) LIMIT $cron_limit");
$rFindJobs = db_fetch_all($qFindJobs);

if ((is_array($rFindJobs)) && (count($rFindJobs) > 0)) {
    foreach ($rFindJobs as $job) {
        
        $sendToTelephones = explode(',', $job->toUser);
        
        // We must append 47 to all phone numbers, clickatell requires this.
        $sendToTelephones = map_array($sendToTelephones, function($number) {
            return '47' . $number;
        });
        
        $message = str_replace(" ", "%20", $job->content);
        $message = utf8_decode($message);
        $URL = sprintf(
            'https://api.clickatell.com/http/sendmsg?user=%s&password=%s&api_id=3600328&to=%s&text=%s',
    		$SMS_user,
            $SMS_pass,
            urlencode(implode(',', $sendToTelephones)),
            urlencode($message)
        );
        
        $curl = curl_init($URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($curl);
        curl_close($curl);
        
    	db_query(
            sprintf(
                "UPDATE %s_cronjobs SET finishTime = '%s' WHERE jobID = '%s'",
                $sql_prefix, time(), $job->jobID
            )
        );

    }
}
