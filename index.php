<?php

require_once 'include.php';
$module = $_GET['module'];
$action = $_GET['action'];
$api = $_GET['api'];
// FIXME: error and hack-checking
if (empty($module) && empty($api))
{
#	$module = "static";
#	$qFindStatic = db_query("SELECT * FROM ".$sql_prefix."_static WHERE header = 'index' AND eventID = '$sessioninfo->eventID'");
#	$rFindStatic = db_fetch($qFindStatic);
#	$content .= $rFindStatic->page;
	$content .= display_systemstatic ("index", $sessioninfo->eventID);
}
elseif (isset ($module) && file_exists ('modules/'.$module.'/'.$module.'.php'))
{
	include ('modules/'.$module.'/'.$module.'.php');
} // End if isset module
elseif (isset ($api) && file_exists ('modules/'.$api.'/api.php'))
{
	include ('modules/'.$api.'/'.'api.php');
	$hide_smarty = TRUE;
} // End if isset api
else
{
	$content = "Hello World!";
}


$design_menu = "<li><a href=\"index.php\">".lang ("Main page", "index")."</a></li>\n";

if (acl_access ("globaladmin", "", 0) == 'Admin')
{
	$design_menu .= "<li><a href=\"?module=globaladmin\">".lang ("Global Admin", "index")."</a></li>\n";
}
// FIXME: this uses userAdmin as an eventACL, while it should be a global ACL.
if (config ("users_may_register") and ($sessioninfo->userID <= 1 or acl_access ("userAdmin", "", $sessioninfo->eventID) != 'No'))
{
	$design_menu .= "<li><a href=\"index.php?module=register\">".lang ("Register user", "index")."</a></li>\n";
}
if (config ("users_may_create_clan") && $sessioninfo->userID > 1)
{
	$design_menu .= "<li><a href=\"index.php?module=groups\">".lang("My groups", "index")."</a></li>\n";
}



if ($sessioninfo->eventID > 1)
{
	// Should probably have some sort of event-config for enabled modules.
	$qListStaticPages = db_query("SELECT ID,header FROM ".$sql_prefix."_static WHERE eventID = '$sessioninfo->eventID' AND type = 'static'");
	while ($rListStaticPages = db_fetch($qListStaticPages))
	{
		if (acl_access("static", $rListStaticPages->ID, $sessioninfo->eventID) != 'No')
		{
			$design_eventmenu .= "<li><a href=\"?module=static&amp;action=viewPage&amp;page=$rListStaticPages->ID\">$rListStaticPages->header</a></li>";
		} // End if acl_access to page is allowed

	} // End while db_fetch(staticPages)

	if (config ("enable_FAQ", $sessioninfo->eventID))
		$design_eventmenu .= "<li><a href=\"?module=FAQ&amp;action=read\">".lang("FAQ", "index")."</a></li>\n";
	if (config ("seating_public", $sessioninfo->eventID))
		$design_eventmenu .= "<li><a href=\"?module=seating\">".lang("Seatmap", "index")."</a></li>\n";
	if (config ("enable_ticketorder", $sessioninfo->eventID) && $sessioninfo->userID > 1)
		$design_eventmenu .= "<li><a href=\"?module=ticketorder\">".lang("Order ticket", "index")."</a></li>\n";
	if (config ("enable_wannabe", $sessioninfo->eventID) && $sessioninfo->userID > 1)
		$design_eventmenu .= "<li><a href=\"?module=wannabe\">".lang("Wannabe", "index")."</a></li>\n";
	if (config ("enable_composystem", $sessioninfo->eventID))
		$design_eventmenu .= "<li><a href=\"?module=compos\">".lang("Composignup", "index")."</a></li>\n";
	if (config ("enable_crewlist", $sessioninfo->eventID) &&acl_access ("crewlist", "", $sessioninfo->eventID) != 'No')
		$design_eventmenu .= "<li><a href=\"?module=crewlist\">".lang("Crewlist", "index")."</a></li>\n";
	$acl_ticketadmin = acl_access("ticketadmin", "", $sessioninfo->eventID);
	if ($acl_ticketadmin == 'Admin' || acl_ticketadmin == 'Write')
		$design_eventmenu .= "<li><a href=\"?module=arrival\">".lang("Arrival", "index")."</a></li>\n";
	if (config ("enable_reseller", $sessioninfo->eventID) && acl_access("reseller", "", $sessioninfo->eventID) != 'No')
		$design_eventmenu .= "<li><a href=\"?module=reseller\">".lang("Reseller", "index")."</a></li>\n";
	if (config("enable_kiosk", $sessioninfo->eventID) && acl_access("kiosk_sales", "", $sessioninfo->eventID) != 'No')
		$design_eventmenu .= "<li><a href=\"?module=kiosk\">".lang("Kiosk", "index")."</a></li>\n";
	if (config("enable_forum", $sessioninfo->eventID))
		$design_eventmenu .= "<li><a href=\"?module=forum\">".lang("Forum", "index")."</a></li>\n";
	





	// User has eventadmin-rights?
	$eventadmin = acl_access("eventadmin", "", $sessioninfo->eventID);
	if($eventadmin != 'No')
		$design_eventmenu .= "<li><a href=\"?module=eventadmin\">".lang("Event Admin", "index")."</a></li>";


}


if($sessioninfo->userID == 1)
{
	// User is not logged in
	$design_userinfo .= "<form method=\"get\" action=\"index.php\">\n";
	$design_userinfo .= "<p><input type=\"hidden\" name=\"module\" value=\"login\" /></p>\n";
	$design_userinfo .= "<p><input type=\"hidden\" name=\"action\" value=\"finduser\" /></p>\n";
	$design_userinfo .= "<p><input class=\"login\" type=\"text\" name=\"username\" /></p>\n";
	$design_userinfo .= "<p><input class=\"login\" type=\"submit\" value=\"Login\" /></p>";
	$design_userinfo .= "</form>\n";
} // End if sessioninfo says not logged in

else {
	// User actually is logged in!
	$design_userinfo .= lang("You are logged in as:", "index");
	$design_userinfo .= "<br />";
	$design_userinfo .= display_username($sessioninfo->userID);
	$design_userinfo .= "<ul>";
	$design_userinfo .= "<li><a href=\"?module=edituserinfo&action=editUserinfo&user=$sessioninfo->userID\">".lang("Edit userinfo", "index")."</a></li>\n";
	$design_userinfo .= "<li><a href=\"?module=edituserinfo&action=editPreferences&user=$sessioninfo->userID\">".lang("Edit my preferences", "index")."</a></li>\n";
	$design_userinfo .= "<li><a href=\"?module=edituserinfo&action=password\">".lang("Change password", "index")."</a></li>\n";
	$design_userinfo .= "<li><a href=\"?module=login&amp;action=logout\">".lang("Logout", "index")."</a></li>\n";
	$design_userinfo .= "</ul>";
}

#if(acl_access("mojo") == "Admin") $design_userinfo .= "<br />".lang("You have mojo!");

// FIXME:This should probably be a function that checks what events you have access to
$qEventList = db_query("SELECT * FROM ".$sql_prefix."_events WHERE eventClosed = 0 AND ID != 1 ORDER BY ID DESC");
while($rEventList = db_fetch($qEventList))
{
	if($rEventList->ID != $sessioninfo->eventID && $rEventList->eventPublic == 1) {
		$design_eventlist .= "<li><a href=\"?module=events&amp;action=setCurrentEvent&amp;eventID=$rEventList->ID\">
		$rEventList->eventname</a></li>";
	}
	elseif($rEventList->ID != $sessioninfo->eventID && $rEventList->eventPublic == 0) {
		// Event is not public, check if we have access to it
		if(acl_access("eventAttendee", "", $rEventList->ID) != 'No') {
			$design_eventlist .= "<li><a href=\"?module=events&amp;action=setCurrentEvent&amp;eventID=$rEventList->ID\">
			$rEventList->eventname</a></li>";
		} // End if acl_access
		// Else we should not do anything
	} // End if eventPublic =0
	else $design_eventlist .= "<li>$rEventList->eventname</li>\n";
}

// This should probably list something... What groups you are member of?
//$design_grouplist .= "You might be a member of something... I do not know";
if($sessioninfo->userID != 1)
{
	// User is logged in, display what groups you are member of
	$qListGroups = db_query("SELECT ".$sql_prefix."_groups.groupname,
		".$sql_prefix."_group_members.groupID FROM
		".$sql_prefix."_group_members INNER JOIN
		".$sql_prefix."_groups ON
		".$sql_prefix."_group_members.groupID =
		".$sql_prefix."_groups.ID
		WHERE ".$sql_prefix."_group_members.userID = $sessioninfo->userID");

	if(mysql_num_rows($qListGroups) != 0) {
		while($rListGroups = db_fetch($qListGroups))
		{
			$design_grouplist .= "<li><a href=\"?module=groups&amp;action=listGroup&amp;groupID=$rListGroups->groupID\">";
			$design_grouplist .= $rListGroups->groupname."</a></li>\n";
		} // End rListGroups
	}
} // end if sessioninfo->userID != 0


$design_head .= '<script type="text/javascript" src="inc/TinyMCE/tiny_mce.js"></script>

<script type="text/javascript">
tinyMCE.init({
	theme : "advanced",
        mode : "specific_textareas",
	editor_selector : /(mceEditor|mceRichText)/,
	theme_advanced_toolbar_location : "top",
	theme_advanced_resizing : true,
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",

});
</script>

';


if(!$hide_smarty) {

	if ($sessioninfo->eventID > 1)
	{
		$smarty->assign ("eventinfo", $eventinfo);
	}

	$smarty->assign("grouplist", $design_grouplist);
	$smarty->assign("eventlist", $design_eventlist);
	$smarty->assign("userinfo", $design_userinfo);
	$smarty->assign("sessioninfo", $sessioninfo);
	$smarty->assign("eventmenu", $design_eventmenu);
	$smarty->assign("menu", $design_menu);

	$smarty->assign("content", $content);
	$smarty->assign("title", $design_title);
	$smarty->assign("head", $design_head);



	$smarty->display($smarty_display);

}
