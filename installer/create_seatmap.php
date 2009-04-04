<?php

if(file_exists('../SVN_OverrideConfig.php')) require('../SVN_OverrideConfig.php');
else require('../config.php');
require('../inc/shared_functions.php');

$eventID = 2; // EventID for the event to insert seatmap.ini to

mysql_connect($sql_host, $sql_user, $sql_pass);
mysql_select_db($sql_base);

// Delete current seatreg-table
db_query("DELETE FROM ".$sql_prefix."_seatReg WHERE eventID = '$eventID'");

$file = fopen("seatmap.ini", "r");
$lineY = 0;
$valid_chars = array('d','p','g','t','b','w','o','a');


while($line = fgets($file)) {
	// Display line
	$x = 0;
	$strlen = strlen($line); // Get how long this line is
	while($x<$strlen) { 
		echo $line[$x];
		$qCheckExists = db_query("SELECT * FROM ".$sql_prefix."_seatReg WHERE eventID = $eventID 
			AND seatY = '$lineY' AND seatX = '$x'");

		switch ($line[$x]) {
			case "d":
				$color = 'blue';
				break;
			case "w":
				$color = 'black';
				break;
			case "p":
				$color = 'red';
				break;
			case "g":
				$color = 'green';
				break;
			case "t":
				$color = 'white';
				break;
			case "a":
				$color = 'green';
				break;
			case "o":
				$color = 'purple';
				break;
			case "b":
				$color = 'white';
				break;
			default:
				$color = 'purple';
				break;
		} // End switch
		if(db_num($qCheckExists) == 0 && in_array($line[$x], $valid_chars )) {
			// Line doesn't exists, create it
			db_query("INSERT INTO ".$sql_prefix."_seatReg SET eventID = '$eventID',
				seatX = '$x',
				seatY = '$lineY',
				type = '$line[$x]',
				color = '$color'
				");
		} // End if db_num == 0



		$x++;
	}

#	echo "\n";
$lineY++;
} // End while

