<?php
$acl = acl_access("infoscreen", "", $sessioninfo->eventID);

if($acl == 'No' || $acl == 'Read') die("No access to infoscreens");

$action = $_GET['action'];

if (empty($action))
{
	#### START - screens ####
	$content .= "<div style='border: solid 1px black; border-collapse: collapse;'>\n";
	$content .= "<h3>"._('Screens')."</h3>";

	$qFindScreens = db_query("SELECT * FROM ".$sql_prefix."_infoscreens WHERE eventID = '$sessioninfo->eventID'");
	$content .= "<table style='border: solid 1px black; border-collapse: collapse;'>";
	while($rFindScreens = db_fetch($qFindScreens)) {
		$content .= "<tr><td style='border: solid 1px black; border-collapse: collapse;'>";
		$content .= $rFindScreens->name;
		$content .= "</td></tr>\n\n";
		# FIXME: would be nice to get to delete screens :-)
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

	if ($acl == 'Write' or $acl == 'Admin')
	{
		$content .= "<a href='?module=infoscreens&action=newSlide'>"._('Create new slide')."</a>";
	}
	
	# TODO: list slides here.
	$content .= "</div>";
	#### END - slides
	
	#### START - queues ####
	$content .= "<br /><div style='border: solid 1px black; border-collapse: collapse;'>\n";
	$content .= "<h3>"._('Queues')."</h3>\n";

	$content .= "</div>";
	#### END - queues ####
}

elseif ($action == 'newSlide' and ($acl == 'Write' or $acl == 'Admin'))
{
	$content .= "<h3>"._('Create a new slide')."</h3>\n";

	$content .= "<form method='POST' action='?module=infoscreens&action=saveSlide'>\n";
	$content .= _('Name:')." <input type=text name=header value='$rStaticPage->header'>\n";
	$content .= "<br /><textarea class='mceEditor' rows=25 cols=60 name='content'>".stripslashes($rStaticPage->page)."</textarea>\n";
	$content .= "<br /><input type=submit value='"._('Save')."'>\n";
	$content .= "</form>\n";

} //end action==newSlide

elseif ($action == 'saveSlide' and ($acl == 'Admin' or $acl == 'Write'))
{
	$slidetable = $sql_prefix."infoscreensSlides";
	$slideID = $_POST['slideID'];
/*	
	if (empty ($content) or empty ($name))
	{
		$
	}
	elseif (empty($slideID)
	{
		// this is new slide...
		$q = sprintf ('INSERT INTO %s (name, content) VALUES (%s, %s)', $slidetable, db_escape($));

		unset ($q);
	}
	else
	{
		// existing slide, update.
	}
*/
} // end action == saveSlide

elseif ($action == "addScreen" && $acl == 'Admin')
{
	$name = $_POST['name'];

	db_query("INSERT INTO ".$sql_prefix."_infoscreens SET name = '".db_escape($name)."', eventID = '$sessioninfo->eventID'");
	
	$log_new['name'] = $name;
	log_add("infoscreens", "addScreen", serialize($log_new));

	header("Location: ?module=infoscreens");


} // End elseif action == addScreen	
