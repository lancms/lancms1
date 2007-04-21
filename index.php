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


$design_menu = "<a href=index.php>Main page</a>";
$design_menu .= "<br><a href=index.php?module=register>Register user</a>";


if($sessioninfo->eventID > 0)
{
	$design_eventmenu .= "y0";
}

$smarty->assign("menu", $design_menu);


if($sessioninfo->userID == 0)
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
	$design_userinfo .= "You are a luser!";
}

if(acl_access("mojo") == "Admin") $design_userinfo .= "<br>".lang("You have mojo!");

// This should probably be a function that checks what events you have access to
$qEventList = db_query("SELECT * FROM ".$sql_prefix."_events WHERE eventPublic = 1 AND eventClosed = 0");
while($rEventList = db_fetch($qEventList))
{
	if($rEventList->ID != $sessioninfo->eventID) $design_eventlist .= "<br><a href=?module=events&action=setCurrentEvent&eventID=$rEventList->ID>
	$rEventList->eventname</a>";
	else $design_eventlist .= "<br>$rEventList->eventname\n";
}

// This should probably list something... What groups you are member of?
//$design_grouplist .= "You might be a member of something... I do not know";
if($sessioninfo->userID != 0)
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
		$design_grouplist .= "<br><a href=?module=groups&action=listGroup&groupID=$rListGroups->groupID>";
		$design_grouplist .= $rListGroups->groupname."</a>\n\n";
	} // End rListGroups
} // end if sessioninfo->userID != 0


$smarty->assign("grouplist", $design_grouplist);
$smarty->assign("eventlist", $design_eventlist);
$smarty->assign("userinfo", $design_userinfo);
$smarty->assign("eventmenu", $design_eventmenu);

$smarty->assign("content", $content);
$smarty->assign("title", $design_title);



$smarty->display($smarty_display);