<?php

$file = "PostNrSS.txt";

$fp = fopen($file, "r");

$output = '<?php

';
$output .= 'db_query("DELETE FROM ".$sql_prefix."_postnumber WHERE country=47");';
$output .= "\n";
while(!feof($fp)) 
{
	$line = fgets($fp);
	$line = str_replace('"', "", $line);
	
	$CSV = split(";", $line);
	
	$postnumber = $CSV[0];
	$postplace = $CSV[1];
	$description = $CSV[2];
	$description2 = $CSV[3];
	$countycode = $CSV[4];
	$countyname = $CSV[5];
		
	if(!empty($postnumber) && $postNrUsed[$postnumber] != TRUE) 
	$output .= 'db_query("INSERT INTO ".$sql_prefix."_postnumber SET postnumber = \''.$postnumber.'\', postplace = \''.$postplace.'\', county = \''.$countycode.'\', country = \'47\'");
	';
	$postNrUsed[$postnumber] = TRUE;
}


$fp = fopen ("../list/norwegian.php", "w");
fputs($fp, $output) or die("Could not write!");
fclose($fp);
