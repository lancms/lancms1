<?php
$acl = acl_access("infoscreen", "", $sessioninfo->eventID);

if($acl == 'No' || $acl == 'Read') die("No access to infoscreens");

$action = $_GET['action'];

if(empty($action)) {


#### START - screens ####
	$content .= "<div style='border: solid 1px black; border-collapse: collapse;'>\n";
	$content .= "<h3>"._('Screens')."</h3>";

	$qFindScreens = db_query("SELECT * FROM ".$sql_prefix."_infoscreens WHERE eventID = '$sessioninfo->eventID'");
	$content .= "<table style='border: solid 1px black; border-collapse: collapse;'>";
	while($rFindScreens = db_fetch($qFindScreens)) {
		$content .= "<tr><td style='border: solid 1px black; border-collapse: collapse;'>";
		$content .= $rFindScreens->name;
		$content .= "</td></tr>\n\n";
	}
	$content .= "</table>";


	if($acl == 'Admin') {
		$content .= "<form method=POST action='?module=infoscreens&action=addScreen'>\n";
		$content .= "<input type=text name='name'>"._("Screen name");
		$content .= "<br /><input type=submit value='"._("Add screen")."'>";
		$content .= "</form>\n";
	}

	$content .= "</div>\n";
#### END - screens ####

#### START - slides ####
	$content .= "<br /><div style='border: solid 1px black; border-collapse: collapse;'>\n";
	$content .= "<h3>"._('Slides')."</h3>\n";
	$content .= "<a href='?modules=infoscreens&action=newSlide'>"._('Create new slide')."</a>";
	
	# TODO: list slides here.
	$content .= "</div>";
#### END - slides
	
#### START - queues ####
	$content .= "<br /><div style='border: solid 1px black; border-collapse: collapse;'>\n";
	$content .= "<h3>"._('Queues')."</h3>\n";

	$content .= "</div>";
#### END - queues ####


}


elseif($action == "addScreen" && $acl == 'Admin') {
	$name = $_POST['name'];

	db_query("INSERT INTO ".$sql_prefix."_infoscreens SET name = '".db_escape($name)."', eventID = '$sessioninfo->eventID'");
	
	$log_new['name'] = $name;
	log_add("infoscreens", "addScreen", serialize($log_new));

	header("Location: ?module=infoscreens");


} // End elseif action == addScreen	
