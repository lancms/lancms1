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


$menu = "<a href=index.php>Main page</a>";
$menu .= "<br><a href=index.php?module=register>Register user</a>";

$smarty->assign("menu", $menu);


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

if(acl_access("mojo")) $design_userinfo .= "<br>You have mojo!";

$smarty->assign("userinfo", $design_userinfo);


$smarty->assign("content", $content);



$smarty->display($smarty_display);