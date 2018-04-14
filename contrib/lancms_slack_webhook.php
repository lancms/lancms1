<?php
$db = mysqli_connect("localhost", "username", "password");
mysqli_select_db($db,"database");

$URL = "https://hooks.slack.com/services/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";

#$payload = '{"text": "This is a line of text sent to you.\nThis is another line."}';

$query = mysqli_query($db,"SELECT l.*,u.nick FROM GO_logs l JOIN GO_users u ON l.userID=u.ID WHERE l.logModule = 'seating' AND IRCBotRead = 0 LIMIT 0,1") or die(mysqli_error($db));
$row = mysqli_fetch_object($query) or die(mysqli_error($db));

$logTextNew = unserialize($row->logTextNew);
#print_r($logTextNew);
#die();
$payload = '{"text": "'.$row->nick.' valgte seg en plass med billettID '.$logTextNew['ticketID'].' (plass X'.$logTextNew['seatX'].' Y'.$logTextNew['seatY'].')"}';



$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $URL);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($curl);
curl_close($curl);

if($server_output == "ok") mysqli_query($db,"UPDATE GO_logs SET IRCBotRead = 1 WHERE ID = '$row->ID'");
