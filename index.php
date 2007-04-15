<?php

require_once 'include.php';
$module = $_GET['module'];

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
$smarty->assign("userinfo", "You are a luser");
$smarty->assign("content", $content);



$smarty->display($smarty_display);