<?php
$action = $_GET['action'];

if(acl_access("globaladmin", "", 0) != "Admin") die("You do not have propper rights!");


if(!isset($action))
{
	$content .= "<table>";
	$content .= "<tr><th>".lang("Event name", "globaladmin");
	$content .= "</th><th>";
	$content .= lang("Admin this event", "globaladmin");
	$content .= "</th><th>";
	$content .= "</th><th>".lang("Set event public", "globaladmin");
	$content .= "</th></tr>\n\n";

	$qListEvents = db_query("SELECT * FROM ".$sql_prefix."_events WHERE ID != 1");
	while($rListEvents = db_fetch($qListEvents)) {
	    $content .= "<tr><td>";
	    $content .= $rListEvents->eventname;
	    $content .= "</td><td>";
	    $content .= "<a href=?module=events&amp;action=setCurrentEvent&amp;eventID=$rListEvents->ID>";
	    $content .= lang("Set active", "globaladmin");
	    $content .= "</a>";
	    $content .= "</td><td>";
	    if($rListEvents->eventPublic == 1) {
	        $content .= "<a href=?module=globaladmin&amp;eventID=$rListEvents->ID&amp;action=setPrivate>".lang("Public", "globaladmin")."</a>";
	    } else {
	        $content .= "<a href=?module=globaladmin&amp;eventID=$rListEvents->ID&amp;action=setPublic>".lang("Private", "globaladmin")."</a>";
	    }
	    $content .= "</td></tr>\n\n\n";
	} // End while(rListEvents)
	$content .= "</table>";
	/* List of global admin-options */
	$content .= "<br><a href=?module=globaladmin&amp;action=addEvent>".lang("Add new event", "globaladmin")."</a>";

} // End if !isset($action)


elseif($action == "addEvent")
{
	/* Action to add new events */

	if(isset($_GET['errormsg'])) $content .= $_GET['errormsg']."<br>\n";

	$content .= "<form method=POST action=?module=globaladmin&amp;action=doAddEvent>\n";
	$content .= "<input type=text name=eventname value='".$_GET['eventname']."'>\n".lang("Name of event", "globaladmin");
	$content .= "<br><input type=submit value='".lang("Add event", "globaladmin")."'>";



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

		header("Location: ?module=events&action=setCurrentEvent&gotomodule=eventadmin&eventID=".$rCheckEventID->ID);
	} // End else (if name doesn't exists; create it)



} // End if action = doAddEvent

elseif($action == "setPrivate") {
    db_query("UPDATE ".$sql_prefix."_events SET eventPublic = 0 WHERE ID = '".db_escape($_GET['eventID'])."'");
    header("Location: ?module=globaladmin");
}

elseif($action == "setPublic") {
    db_query("UPDATE ".$sql_prefix."_events SET eventPublic = 1 WHERE ID = '".db_escape($_GET['eventID'])."'");
    header("Location: ?module=globaladmin");
}