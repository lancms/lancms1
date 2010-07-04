<?php

if($action == "searchitems") {
	$request = $_REQUEST['search'];

	$qFindWares = db_query("SELECT * FROM ".$sql_prefix."_kiosk_wares WHERE name LIKE '%".db_escape($request)."%'");
	
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	echo "\n<response>\n";
	while($rFindWares = db_fetch($qFindWares)) {
		echo "<name>";
		echo $rFindWares->name;
		echo "</name>\n";
	} // End while
	echo "</response>\n";
} // End action = searchitems

