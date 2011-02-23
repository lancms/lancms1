<?php
$acl = acl_access("infoscreen", "", $sessioninfo->eventID);

if($acl == 'No' || $acl == 'Read') die("No access to infoscreens");

$action = $_GET['action'];

if(empty($action)) {
	$qFindScreens = db_query("SELECT * FROM ".$sql_prefix."_infoscreens WHERE eventID = '$sessioninfo->eventID'");
	$content .= "<table>";
	while($rFindScreens = db_fetch($qFindScreens)) {
		$content .= "<tr><td>";
		$content .= $rFindScreens->name;
		$content .= "</td></tr>\n\n";
	}
	$content .= "</table>";


	if($acl == 'Admin') {
		$content .= "<form method=POST action='?module=infoscreens&action=addScreen'>\n";
		$content .= "<input type=text name='name'>"._("Screen name");
		$content .= "<br /><input type=submit value='"._("Add screen")."'>";
		$content .= "</form>";
	}

}


elseif($action == "addScreen" && $acl == 'Admin') {
	$name = $_POST['name'];

	db_query("INSERT INTO ".$sql_prefix."_infoscreens SET name = '".db_escape($name)."', eventID = '$sessioninfo->eventID'");
	
	$log_new['name'] = $name;
	log_add("infoscreens", "addScreen", serialize($log_new));

	header("Location: ?module=infoscreens");


} // End elseif action == addScreen	
