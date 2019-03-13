<?php
include_once '/srv/partysys/current/include.php';

$eventID = 31;

$q = db_query("SELECT u.*,ts.seatX,ts.seatY FROM (GO_tickets t JOIN GO_users u ON u.ID=t.user) JOIN GO_seatReg_seatings ts ON ts.ticketID=t.ticketID WHERE t.eventID = '$eventID' ORDER BY seatY,seatX ASC");

#$skipRow = array(5,8,11,14,17);
#$skipSeat = array(15,28);
$rader = 12;
$plasser = 32;

#for($x=1;$x<$rader;$x++) {
#	for($y=1;$y<$plasser;$y++) {
#		$seat[$y][$x]['nick'] = 'LEDIG';
#	}
#}

#print_r($seat);
#die();
while($r = db_fetch($q)) {
	$nick = $r->nick;
	$name = $r->firstName." ".$r->lastName;
	
	switch($r->seatX) {

		case 3:
			$rowID = 1;
			break;
		case 4:
			$rowID = 2;
			break;
		case 6:
			$rowID = 3;
			break;
		case 7:
			$rowID = 4;
			break;
		case 9:
			$rowID = 5;
			break;
		case 10:
			$rowID = 6;
			break;
		case 12:
			$rowID = 7;
			break;
		case 13:
			$rowID = 8;
			break;
		case 15:
			$rowID = 9;
			break;
		case 16:
			$rowID = 10;
			break;
		case 18:
			$rowID = 11;
			break;
		case 19:
			$rowID = 12;
			break;


	}

	switch ($r->seatY) {
		case ($r->seatY >=3 AND $r->seatY < 15):
			$seatID = $r->seatY - 2;
			break;

		case ($r->seatY >15 AND $r->seatY < 28):
			$seatID = $r->seatY - 3;
			break;
		case ($r->seatY > 28 AND $r->seatY <37):
			$seatID = $r->seatY - 4;
			break;


	} // End switch

#	if($r->seatX == $meta->min_seatX) $seatID = 1;
#	else $seatID++;
	$seat[$rowID][$seatID]['nick'] = $nick;
	$seat[$rowID][$seatID]['name'] = $name;

#echo "S".$rowID."R".$seatID."($nick) (DEBUG: $r->seatX, p$r->seatY)\n";

	
}
#print_r($seat);
#die();
$rowID = 0;
$seatID = 0;

echo "<html>
	<head>
	<style type='text/css'>
	.footer { page-break-after: always; }
	</style>
</head>
<body>
";	

foreach($seat as $y => $x) {
	$rowID++;
	foreach($x as $key => $value) {
		$seatID++;
		echo "<h2>Dette er rad $rowID, plass $seatID</h2>\n";
		if($value['nick'] == 'LEDIG') echo "<h1>Denne plassen er LEDIG. Ta kontakt med Security for å reservere</h1>\n";
		else echo "<h1>Her sitter ".$value['name']." a.k.a. ".$value['nick']." </h1>\n";

		echo "<p>Velkommen til GlobeLAN 31. Som deltager på GlobeLAN blir du medlem av i organisasjonen Vestfold Digitale Ungdom, og har mulighet til å påvirke i organisasjonen, og hvordan GlobeLAN styres fremover..</p>\n\n\n";
		echo "<p>Har du noen spørsmål, ta gjerne kontakt med oss på Discord: https://discord.gg/gKmjKj9\n\n"; 
		echo "<p><img src='https://www.globelan.no/wp-content/uploads/2018/04/VDU_Web-Logo.jpg' width=180>\n";

		echo "<div class='footer'></div>\n\n\n\n";
#		echo "ROW: $y SEAT: $key OWNED BY: ".$value['nick']."\n";

	}
	$seatID = 0;
#	break;
}


