<?php

require_once 'include.php';
$module = $_GET['module'];
$action = $_GET['action'];

// FIXME: error and hack-checking
if(isset($module) && file_exists('modules/'.$module.'/'.$module.'.php'))
{
	include 'modules/'.$module.'/'.$module.'.php';
} // End if isset module

else
{
	$content = "Hello World!";
}


$design_menu = "<a href=index.php>Main page</a>\n";
if(acl_access("globaladmin", "", 0) == 'Admin')
	$design_menu .= "<br><a href=?module=globaladmin>".lang("Global Admin", "index")."</a>\n";

if(config("users_may_register"))
	$design_menu .= "<br><a href=index.php?module=register>Register user</a>\n";
if(config("users_may_create_clan") && $sessioninfo->userID > 1)
	$design_menu .= "<br><a href=index.php?module=groups>".lang("My groups", "index")."</a>\n";



if($sessioninfo->eventID > 1)
{
	// Should probably have some sort of event-config for enabled modules.
	$qListStaticPages = db_query("SELECT ID,header FROM ".$sql_prefix."_static WHERE eventID = '$sessioninfo->eventID'");
	while($rListStaticPages = db_fetch($qListStaticPages))
	{
		if(acl_access("static", $rListStaticPages->ID, $sessioninfo->eventID) != 'No')
		{
			$design_eventmenu .= "<br><a href=?module=static&amp;action=viewPage&amp;page=$rListStaticPages->ID>$rListStaticPages->header</a>";
		} // End if acl_access to page is allowed

	} // End while db_fetch(staticPages)

	if(config("enable_FAQ", $sessioninfo->eventID))
		$design_eventmenu .= "<br><a href=?module=FAQ&amp;action=read>".lang("FAQ", "index")."</a>";
	if(config("enable_ticketorder", $sessioninfo->eventID) && $sessioninfo->userID > 1)
		$design_eventmenu .= "<br><a href=?module=ticketorder>".lang("Order ticket", "index")."</a>";
	if(config("enable_wannabe", $sessioninfo->eventID) && $sessioninfo->userID > 1)
		$design_eventmenu .= "<br><a href=?module=wannabe>".lang("Wannabe", "index")."</a>";





	// User has eventadmin-rights?
	$eventadmin = acl_access("eventadmin", "", $sessioninfo->eventID);
	if($eventadmin == "Admin" || $eventadmin == "Write")
		$design_eventmenu .= "<br><a href=?module=eventadmin>".lang("Event Admin", "index")."</a>";


}


if($sessioninfo->userID == 1)
{
	// User is not logged in
	$design_userinfo .= "<form method=GET action=index.php>\n";
	$design_userinfo .= "<input type=hidden name=module value=login>\n";
	$design_userinfo .= "<input type=hidden name=action value=finduser>\n";
	$design_userinfo .= "<input type=text name=username>\n";
	$design_userinfo .= "<input type=submit value='Login'>";
	$design_userinfo .= "</form>\n";
} // End if sessioninfo says not logged in

else {
	// User actually is logged in!
	$qGetUserinfo = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '$sessioninfo->userID'");
	$rGetUserinfo = db_fetch($qGetUserinfo);
	$design_userinfo .= lang("You are logged in as:", "index");
	$design_userinfo .= "<br>";
	$design_userinfo .= $rGetUserinfo->firstName." ".$rGetUserinfo->lastName;
	$design_userinfo .= " (".$rGetUserinfo->nick.")";
	//$design_userinfo .= "You are a luser!";
	$design_menu .= "<br><a href=?module=login&amp;action=logout>".lang("Logout", "index")."</a>\n";
}

#if(acl_access("mojo") == "Admin") $design_userinfo .= "<br>".lang("You have mojo!");

// This should probably be a function that checks what events you have access to
$qEventList = db_query("SELECT * FROM ".$sql_prefix."_events WHERE eventPublic = 1 AND eventClosed = 0");
while($rEventList = db_fetch($qEventList))
{
	if($rEventList->ID != $sessioninfo->eventID) $design_eventlist .= "<br><a href=?module=events&amp;action=setCurrentEvent&amp;eventID=$rEventList->ID>
	$rEventList->eventname</a>";
	else $design_eventlist .= "<br>$rEventList->eventname\n";
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
	while($rListGroups = db_fetch($qListGroups))
	{
		$design_grouplist .= "<br><a href=?module=groups&amp;action=listGroup&amp;groupID=$rListGroups->groupID>";
		$design_grouplist .= $rListGroups->groupname."</a>\n\n";
	} // End rListGroups
} // end if sessioninfo->userID != 0


$smarty->assign("grouplist", $design_grouplist);
$smarty->assign("eventlist", $design_eventlist);
$smarty->assign("userinfo", $design_userinfo);
$smarty->assign("eventmenu", $design_eventmenu);
$smarty->assign("menu", $design_menu);

$smarty->assign("content", $content);
$smarty->assign("title", $design_title);
$smarty->assign("head", $design_head);



$smarty->display($smarty_display);