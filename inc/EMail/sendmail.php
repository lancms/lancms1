<?php


$qFindJobs = db_query("SELECT * FROM ".$sql_prefix."_cronjobs WHERE cronModule = 'MAIL' AND (finishTime < 1 OR finishTime IS NULL) LIMIT 0,20");

while($rFindJobs = db_fetch($qFindJobs)) {

	$qUserInfo = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '$rFindJobs->toUser'");
	$rUserInfo = db_fetch($qUserInfo);
	$to = $rUserInfo->EMail;
#	$from = $MAIL_FROM;
	$from = "osgl@globeorg.no";
	$subject = $rFindJobs->subject;
	$mail_body = $rFindJobs->content;
	$mail_headers = "X-Mailer: OSGL-mailer\r\n";
	$mail_headers .= "MIME-Version: 1.0\r\n"; 
	$mail_headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$mail_headers .= "From: $from";
	

	// Nice to wrap the mail
	$mail_body =  wordwrap($mail_body, 70);
	$html_mail = "<HTML><BODY>
$mail_body
</BODY></HTML>";
	mail($to, $subject, $html_mail, $mail_headers);
#	echo "Sent mail to $to \n";
	db_query("UPDATE ".$sql_prefix."_cronjobs SET finishTime = '".time()."' WHERE jobID = '$rFindJobs->jobID'");
}
