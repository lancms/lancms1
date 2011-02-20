<?php
if(ob_get_length()) ob_clean();
header('Expires: Mon, 26 Jul 2000 05:00 00 GMT');
header('Last-Modified: '. gmdate('D, d M Y H:i:s') . 'GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: text/xml');
if($action == "searchitems") {
	$request = $_REQUEST['search'];

	$qFindWares = db_query("SELECT * FROM ".$sql_prefix."_kiosk_wares WHERE name LIKE '%".db_escape($request)."%'");
	
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	echo "\n<response>\n";
	while($rFindWares = db_fetch($qFindWares)) {
		echo "<item";
		echo " name='";
		echo $rFindWares->name;
		echo "'";
		echo " ID='";
		echo $rFindWares->ID;
		echo "' />";
	} // End while
	echo "</response>\n";
} // End action = searchitems

