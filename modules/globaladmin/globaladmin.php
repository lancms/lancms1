<?php
$action = $_REQUEST['action'];

if(acl_access("globaladmin", "", 0) != "Admin") die("You do not have propper rights!");


if(!isset($action))
{
	$content .= "<table>";
	$content .= "<tr><th>".lang("Event name", "globaladmin");
	$content .= "</th><th>";
	$content .= lang("Admin this event", "globaladmin");
	$content .= "</th><th>".lang("Set event public", "globaladmin");
	$content .= "</th><th>"._("Is open");
	$content .= "</th><th>"._("Design");
	$content .= "</th></tr>\n\n";

	$qListEvents = db_query("SELECT * FROM ".$sql_prefix."_events WHERE ID != 1");
	$rownum = 1;
	while($rListEvents = db_fetch($qListEvents)) {
		if ($rownum == 3)
		{
			$rownum = 1;
		}
	    $content .= "<tr class='row".$rownum."'><td>";
	    $content .= $rListEvents->eventname;
	    $content .= "</td><td>";
	    $content .= "<a href=\"?module=events&amp;action=setCurrentEvent&amp;eventID=$rListEvents->ID\">";
	    $content .= lang("Set active", "globaladmin");
	    $content .= "</a>";
	    $content .= "</td><td>";
	    if($rListEvents->eventPublic == 1) {
	        $content .= "<a href=\"?module=globaladmin&amp;eventID=$rListEvents->ID&amp;action=setPrivate\">".lang("Public", "globaladmin")."</a>";
	    } else {
	        $content .= "<a href=\"?module=globaladmin&amp;eventID=$rListEvents->ID&amp;action=setPublic\">".lang("Private", "globaladmin")."</a>";
		if($rListEvents->ID == $sessioninfo->eventID) $content .= " <a href=\"?module=eventadmin&amp;eventID=$rListEvents->ID&amp;action=eventaccess\">".lang("Attendee-access", "globaladmin")."</a>";
	    }
		$content .= "</td><td>";
		$content .= "<a href=?module=globaladmin&eventID=$rListEvents->ID&action=toggleClosed>";
		if($rListEvents->eventClosed == 0)
			$closedImage = 'images/icons/yes.png';
		else
			$closedImage = 'images/icons/no.png';
		$content .= "<img src=\"$closedImage\" width=\"50%\"></a>";

		$content .= "</td><td>\n";

		$content .= "<form action='index.php' method='get'><input type='hidden' name='module' value='globaladmin' /><input type='hidden' name='action' value='setDesign' /><input type='hidden' name='eventID' value='".$rListEvents->ID."' /><select name='design'>";


		$designs = list_designs ();
		foreach ($designs as $design)
		{
			$design_selected = "";
			if ($design == $rListEvents->eventDesign)
			{
				$design_selected = "SELECTED";
			}
			$content .= "<option ".$design_selected." value='".$design."'>".$design."</option>";
		}

		$content .= "</select><input type='submit' class=\"btn\" value='"._("Change")."' /></form>";


		$content .= "</td></tr>\n\n\n";
		$rownum++;
	} // End while(rListEvents)
	$content .= "</table>";
	/* List of global admin-options */
	$content .= "<br /><a href=\"?module=globaladmin&amp;action=addEvent\">".lang("Add new event", "globaladmin")."</a>";

	$content .= "<br /><br /><a href=\"?module=globaladmin&amp;action=config\">".lang("Change global options", "globaladmin")."</a>";
	$content .= "<br /><br /><a href=\"?module=logs\">".lang("View logs", "globaladmin")."</a>";
	$content .= "<br /><br /><a href=\"?module=useradmin\">".lang("User administration", "globaladmin")."</a>";
	$content .= "<br /><br /><a href=\"?module=globaladmin&amp;action=listGlobalRights\">".lang("List groups with global access", "globaladmin")."</a>";
	$content .= "<br /><br /><a href=\"?module=sessions\">".lang("List sessions", "globaladmin")."</a>";

} // End if !isset($action)


elseif($action == "addEvent")
{
	/* Action to add new events */

	if(isset($_GET['errormsg'])) $content .= $_GET['errormsg']."<br />\n";

	$content .= "<form method=\"post\" action=\"?module=globaladmin&amp;action=doAddEvent\">\n";
	$content .= "<p><input type=\"text\" name=\"eventname\" value='".$_GET['eventname']."' />\n".lang("Name of event", "globaladmin")."</p>";
	$content .= "<p><input type=\"submit\" value='".lang("Add event", "globaladmin")."' /></p>";
	$content .= "</form>\n";



} // End action = addEvent


elseif($action == "doAddEvent")
{
	$eventname = $_POST['eventname'];

	$qCheckExistingName = db_query("SELECT COUNT(eventname) AS count FROM ".$sql_prefix."_events WHERE eventname LIKE '".db_escape($eventname)."'");
	$rCheckExistingName = db_fetch($qCheckExistingName);

	if($rCheckExistingName->count > 0)
	{
		header("Location: ?module=globaladmin&action=addEvent&eventname=$eventname&errormsg=Event already exists");
	} // End if count of existing name > 0, make error

	else
	{
		// Name doesn't exists already; create it
		db_query("INSERT INTO ".$sql_prefix."_events SET
			eventname = '".db_escape($eventname)."',
			createdByTime = ".time().",
			createdByUser = '".$sessioninfo->userID."'
			");
		$qCheckEventID = db_query("SELECT ID FROM ".$sql_prefix."_events WHERE eventname LIKE '".db_escape($eventname)."'");
		$rCheckEventID = db_fetch($qCheckEventID);
		$log_new['eventname'] = $eventname;
		log_add("globaladmin", "doAddEvent", serialize($log_new));

		header("Location: ?module=events&action=setCurrentEvent&gotomodule=eventadmin&eventID=".$rCheckEventID->ID);
	} // End else (if name doesn't exists; create it)



} // End if action = doAddEvent

elseif($action == "setPrivate") {
	db_query("UPDATE ".$sql_prefix."_events SET eventPublic = 0 WHERE ID = '".db_escape($_GET['eventID'])."'");
	$log_new['eventID'] = $_GET['eventID'];
	log_add("globaladmin", "setPrivate", serialize($log_new));
	header("Location: ?module=globaladmin");
}

elseif($action == "setPublic") {
	db_query("UPDATE ".$sql_prefix."_events SET eventPublic = 1 WHERE ID = '".db_escape($_GET['eventID'])."'");
	$log_new['eventID'] = $_GET['eventID'];
	log_add("globaladmin", "setPublic", serialize($log_new));
	header("Location: ?module=globaladmin");
}

elseif($action == "config") {

	if($_GET['saved'] == "OK") $content .= lang("Config successfully saved", "globaladmin");

	$content .= "<form method=POST action='?module=globaladmin&amp;action=doConfig'>\n";
	for($i=0;$i<count($globalconfig['checkbox']);$i++) {
		$cfgtype = $globalconfig['checkbox'][$i];
		$cfg_current = config($cfgtype, 1);
		$content .= "<input type=checkbox name='".$globalconfig['checkbox'][$i]."'";
		if($cfg_current) $content .= " CHECKED";
		$content .= "> ".lang($globalconfig['checkbox'][$i], "globalconfigoption")."<br />\n";
	} // End for
	for($i=0;$i<count($globalconfig['text']);$i++) {
		$cfgtype = $globalconfig['text'][$i];
		$qFindTexts = db_query("SELECT * FROM ".$sql_prefix."_config WHERE config LIKE '".$cfgtype."%' AND eventID = 1");
		while($rFindTexts = db_fetch($qFindTexts)) {
			$content .= "<input type=text name='".$rFindTexts->config."' value='$rFindTexts->value'>";
			$content .= lang($rFindTexts->config, "globalconfigoption")."<br />";
		} // End while
	} // End for

	$content .= "<input type=submit value='".lang("Save", "globaladmin_config")."'></form>";


} // end action == config


elseif($action == "doConfig") {

	for($i=0;$i<count($globalconfig['checkbox']);$i++) {
		$glbcfg = $globalconfig['checkbox'][$i];

		$post = $_POST[$glbcfg];
		if($post == "on") $post = 1;
		else $post = "disable";
		#echo $evtcfg.": ".$post;
		$log_old[$glbcfg] = config($globalconfig['checkbox'][$i]);
		$log_new[$glbcfg] = $post;
		config($globalconfig['checkbox'][$i], 1, $post);
	} // End for

        for($i=0;$i<count($globalconfig['text']);$i++) {
                $cfgtype = $globalconfig['text'][$i];
                $qFindTexts = db_query("SELECT * FROM ".$sql_prefix."_config WHERE config LIKE '".$cfgtype."%' AND eventID = 1");
                while($rFindTexts = db_fetch($qFindTexts)) {
			$post = $_POST[$rFindTexts->config];
#                        $content .= "<input type=text name='".$rFindTexts->config."' value='$rFindTexts->value'>";
#                        $content .= lang($rFindTexts->config, "globalconfigoption")."<br />";
			$log_new[$rFindTexts->config] = $post;
			$log_old[$rFindTexts->config] = config($rFindTexts->config, 1);
			config($rFindTexts->config, 1, $post);
                } // End while
        } // End for


	log_add("globalconfig", "doConfig", serialize($log_new), serialize($log_old));
	header("Location: ?module=globaladmin&action=config&action=config&saved=OK");
}

elseif($action == "listGlobalRights") {
	$searcharray = "'globaladmin'";
	for($i=0;$i<count($globalaccess);$i++) {
		$searcharray .= ", '".$globalaccess[$i]."'";
	} // End for

	$qFindGroups = db_query("SELECT groups.groupname,groups.eventID,ACLs.accessmodule,ACLs.access FROM ".$sql_prefix."_groups groups JOIN ".$sql_prefix."_ACLs ACLs ON groups.ID=ACLs.groupID WHERE ACLs.accessmodule IN ($searcharray) AND ACLs.access != 'No'");
	$content .= "<h2>".lang("Groups with global rights", "globaladmin")."</h2>\n";
	$content .= "<table>\n";
	$content .= "<tr><th>".lang ("Event", "globaladmin")."</th><th>".lang ("Group", "globaladmin")."</th><th>".lang ("Module")."</th><th>".lang ("Access", "globaladmin")."</th></tr>\n";

	$count = 1;
	while($rFindGroups = db_fetch($qFindGroups)) {
		$qFindEventName = db_query("SELECT * FROM ".$sql_prefix."_events WHERE ID = '$rFindGroups->eventID'");
		$rFindEventName = db_fetch($qFindEventName);
		$content .= "<tr class='row$count'><td>";
		$content .= $rFindEventName->eventname;
		$content .= "</td><td>";
		$content .= $rFindGroups->groupname;
		$content .= "</td><td>";
		$content .= $rFindGroups->accessmodule;
		$content .= "</td><td>";
		$content .= $rFindGroups->access;
		$content .= "</td></tr>\n";

		if ($count == 2)
		{
			$count = 1;
		}
		else
		{
			$count = 2;
		}
	} // End while
	$content .= "</table>\n";

}

elseif($action == "toggleClosed" && isset($_GET['eventID'])) {
	$eventID = $_GET['eventID'];
	$qFindClosed = db_query("SELECT * FROM ".$sql_prefix."_events WHERE ID = '".db_escape($eventID)."'");
	$rFindClosed = db_fetch($qFindClosed);

	if($rFindClosed->eventClosed == 0) $newClosed = 1;
	else $newClosed = 0;
	db_query("UPDATE ".$sql_prefix."_events SET eventClosed = '$newClosed' WHERE ID = '".db_escape($eventID)."'");
	$log_new['eventID'] = $eventID;
	$log_new['closedStatus'] = $newClosed;
	$log_old['closedStatus'] = $rFindClosed->eventClosed;
	log_add("globaladmin", "toggleClosed", serialize($log_new), serialize($log_old));
	header("Location: ?module=globaladmin");
}

elseif ($action == "setDesign" and isset ($_REQUEST['eventID']) and isset ($_REQUEST['design']))
{
	$eventid = $_REQUEST['eventID'];
	$design = $_REQUEST['design'];

	$designs = list_designs ();

	if (in_array ($design, $designs))
	{
		// db_query() handles errors.
		$qUpdateDesign = db_query ("UPDATE ".$sql_prefix."_events SET eventDesign='".db_escape($design)."' WHERE ID = '".db_escape($eventid)."'");
	}

	header ("Location: ?module=globaladmin");
}
