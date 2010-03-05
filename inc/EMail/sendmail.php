<?php


$qFindJobs = db_query("SELECT * FROM ".$sql_prefix."_cronjobs WHERE cronModule = 'MAIL' AND (finishTime < 1 OR finishTime IS NULL) LIMIT 0,20");

while($rFindJobs = db_fetch($qFindJobs)) {

	$to = $rFindJobs->toUser;
	$from = $MAIL_FROM;
	$subject = "OSGL Mail Subject";
	$mail_body = $rFindJobs->content;
	$mail_headers = "X-Mailer: OSGL-mailer";
	

	// Nice to wrap the mail
	$mail_body =  wordwrap($mail_body, 70);

	mail($to, $subject, $mail_body, $mail_headers);
	echo "Sent mail to $to \n";
#	db_query("UPDATE ".$sql_prefix."_cronjobs SET finishTime = '".time()."' WHERE jobID = '$rFindJobs->jobID'");
}
