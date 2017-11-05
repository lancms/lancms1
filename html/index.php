<?php

/*----------------------------------------------------------------*/
/* Initialize parameters                                          */
$design_menu = "";
$design_eventmenu = "";
$design_userinfo = "";
$design_eventlist = "";
$design_grouplist = "";
$design_head = "";
$facebook_likebox_url = "";
$hide_smarty = false;
$content = "";
/*----------------------------------------------------------------*/

require_once __DIR__ . '/../include.php';

// Create the twig instance.
$twigEnvironment = new Twig_Environment(
    new Twig_Loader_Filesystem(realpath(__DIR__.'/../templates/')),
    array(
        'cache' => '/tmp/lancms_twig_cache/',
        // 'debug' => true,
    )
);
$twigEnvironment->addExtension(new Twig_Extension_Debug());
$twigEnvironment->addFunction(new Twig_SimpleFunction('trans', function ($string) {
    return _($string);
}));

$module = (isset($_GET['module']) ? $_GET['module']: '');
$action = (isset($_GET['action']) ? $_GET['action']: '');
$api = (isset($_GET['api']) ? $_GET['api']: '');
// FIXME: error and hack-checking
if (empty($module) && empty($api))
{
#	$module = "static";
#	$qFindStatic = db_query("SELECT * FROM ".$sql_prefix."_static WHERE header = 'index' AND eventID = '$sessioninfo->eventID'");
#	$rFindStatic = db_fetch($qFindStatic);
#	$content .= $rFindStatic->page;
	$content .= display_systemstatic ("index", $sessioninfo->eventID);
	$content .= display_news ($sessioninfo->eventID);
}
elseif (isset ($module) && file_exists (__DIR__ . '/../modules/'.$module.'/'.$module.'.php'))
{
	include __DIR__ . '/../modules/'.$module.'/'.$module.'.php';
} // End if isset module
elseif (isset ($api) && file_exists (__DIR__ . '/../modules/'.$api.'/api.php'))
{
	include __DIR__ . '/../modules/'.$api.'/'.'api.php';
	$hide_smarty = TRUE;
} // End if isset api
else
{
	$content = "Hello World!";
}


$design_menu .= "<li><a href=\"index.php\">".lang("Main page", "index")."</a></li>\n";

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

$design_eventmenu = "";
if ($sessioninfo->eventID > 1)
{

	$links = array();

	// Should probably have some sort of event-config for enabled modules.
	$qListStaticPages = db_query("SELECT ID,header FROM ".$sql_prefix."_static WHERE eventID = '$sessioninfo->eventID' AND type = 'static' AND deleted = 0");
	while ($rListStaticPages = db_fetch($qListStaticPages))
	{
		if (acl_access("static", $rListStaticPages->ID, $sessioninfo->eventID) != 'No')
		{
			$cssClassItem = "";
			if ($module == 'static' && $action == 'viewpage' && (isset($_GET['page']) && $_GET['page'] == $rListStaticPages->ID)) {
				$cssClassItem = " class=\"active\"";
			}

			$links[] = "<li$cssClassItem><a href=\"?module=static&amp;action=viewPage&amp;page=$rListStaticPages->ID\">$rListStaticPages->header</a></li>";
		} // End if acl_access to page is allowed

	} // End while db_fetch(staticPages)

	if (config ("enable_FAQ", $sessioninfo->eventID)) {
		$links[] = "<li" . eventMenuItemCssClasses('FAQ') . "><a href=\"?module=FAQ&amp;action=read\">".lang("FAQ", "index")."</a></li>\n";
	}

	if (config ("seating_public", $sessioninfo->eventID)) {
		$links[] = "<li" . eventMenuItemCssClasses('seating') . "><a href=\"?module=seating\">".lang("Seatmap", "index")."</a></li>\n";
	}

	if (config ("enable_ticketorder", $sessioninfo->eventID)) {
		$links[] = "<li" . eventMenuItemCssClasses('ticketorder') . "><a href=\"?module=ticketorder\">".lang("Order ticket", "index")."</a></li>\n";
	}

	if (config ("enable_usertickets", $sessioninfo->eventID) && $sessioninfo->userID > 1) {
		$links[] = "<li" . eventMenuItemCssClasses('usertickets') . "><a href=\"?module=usertickets\">".lang("My tickets", "index")."</a></li>\n";
	}

	if (config ("enable_wannabe", $sessioninfo->eventID) && $sessioninfo->userID > 1) {
		$links[] = "<li" . eventMenuItemCssClasses('wannabe') . "><a href=\"?module=wannabe\">".lang("Apply for crewposition", "index")."</a></li>\n";
	}

	if (config ("enable_composystem", $sessioninfo->eventID)) {
		$links[] = "<li" . eventMenuItemCssClasses('compos') . "><a href=\"?module=compos\">".lang("Composignup", "index")."</a></li>\n";
	}

	if (config ("enable_crewlist", $sessioninfo->eventID) &&acl_access ("crewlist", "", $sessioninfo->eventID) != 'No') {
		$links[] = "<li" . eventMenuItemCssClasses('crewlist') . "><a href=\"?module=crewlist\">".lang("Crewlist", "index")."</a></li>\n";
	}

	$acl_ticketadmin = acl_access("ticketadmin", "", $sessioninfo->eventID);
	if ($acl_ticketadmin == 'Admin' || $acl_ticketadmin == 'Write') {
		$links[] = "<li" . eventMenuItemCssClasses('arrival') . "><a href=\"?module=arrival\">".lang("Arrival", "index")."</a></li>\n";
	}

	if (config ("enable_reseller", $sessioninfo->eventID) && acl_access("reseller", "", $sessioninfo->eventID) != 'No') {
		$links[] = "<li" . eventMenuItemCssClasses('reseller') . "><a href=\"?module=reseller\">".lang("Reseller", "index")."</a></li>\n";
	}

	if (config("enable_kiosk", $sessioninfo->eventID) && acl_access("kiosk_sales", "", $sessioninfo->eventID) != 'No') {
		$links[] = "<li" . eventMenuItemCssClasses('kiosk') . "><a href=\"?module=kiosk\">".lang("Kiosk", "index")."</a></li>\n";
	}

	$acl_sleepers = acl_access("sleepers", "", $sessioninfo->eventID);
	if ((config ('enable_sleepers', $sessioninfo->eventID)) and ($acl_sleepers == 'Admin' or $acl_sleepers == 'Write')) {
		$links[] = "<li" . eventMenuItemCssClasses('sleepers') . "><a href=\"?module=sleepers\">".lang("Sleepers", "index")."</a></li>\n";
	}

	if(acl_access("dashboard", "", $sessioninfo->eventID) != 'No') {
		$links[] = "<li" . eventMenuItemCssClasses('dashboard') . "><a href='?module=dashboard'>"._("Dashboard")."</a></li>\n";
	}

	// User has eventadmin-rights?
	$eventadmin = acl_access("eventadmin", "", $sessioninfo->eventID);
	if($eventadmin != 'No') {
		$links[] = "<li" . eventMenuItemCssClasses('eventadmin') . "><a href=\"?module=eventadmin\">".lang("Event Admin", "index")."</a></li>";
	}

	// Verify that there are links to display in menu.
	if (count($links) > 0) {
		$design_eventmenu = "<div class=\"box menu\"><ul>";
		$design_eventmenu .= implode("", $links);
		$design_eventmenu .= "</ul></div>";
	}

	unset($links);
}


if($sessioninfo->userID == 1)
{
	// User is not logged in
	$design_userinfo .= "<form class=\"pad bot\" method=\"get\" action=\"index.php\">\n";
	$design_userinfo .= "<input type=\"hidden\" name=\"module\" value=\"login\" />\n";
	$design_userinfo .= "<input type=\"hidden\" name=\"action\" value=\"finduser\" />\n";
	$design_userinfo .= "".lang ("Type your nick, email or name here:", "index")."\n";
	$design_userinfo .= "<input class=\"login\" type=\"text\" placeholder='Username' name=\"username\" />\n";
	$design_userinfo .= "<input class=\"login\" type=\"submit\" value=\"Login\" />";
	$design_userinfo .= "</form>\n";

	if($facebook_appID > 0 && $facebook_login == TRUE) {
		// Facebook Connect is set up and enabled. Using for login
		$design_userinfo .= "<script>
        window.fbAsyncInit = function() {
          FB.init({
            appId      : '".$facebook_appID."',
            status     : true,
            cookie     : true,
            xfbml      : true,
            oauth      : true,
          });
        };
        (function(d){
           var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
           js = d.createElement('script'); js.id = id; js.async = true;
           js.src = \"//connect.facebook.net/en_US/all.js\";
           d.getElementsByTagName('head')[0].appendChild(js);
         }(document));
      </script>
      <div class=\"fb-login-button\">Login with Facebook</div>
	";

	} // End facebook
} // End if sessioninfo says not logged in

else {
	// User actually is logged in!
	$design_userinfo .= "<p class=\"pad bot\">" . lang("You are logged in as:", "index");
	$design_userinfo .= "<br />";
	$design_userinfo .= display_username($sessioninfo->userID);
	$design_userinfo .= "</p><div class=\"menu top-border no-radius-top\"><ul>";
	$design_userinfo .= "<li><a href=\"?module=edituserinfo&action=editUserinfo&user=$sessioninfo->userID\">".lang("Edit userinfo", "index")."</a></li>\n";
	$design_userinfo .= "<li><a href=\"?module=edituserinfo&action=editPreferences&user=$sessioninfo->userID\">".lang("Edit my preferences", "index")."</a></li>\n";
	$design_userinfo .= "<li><a href=\"?module=edituserinfo&action=password\">".lang("Change password", "index")."</a></li>\n";
	$design_userinfo .= "<li><a href=\"?module=login&amp;action=logout\">".lang("Logout", "index")."</a></li>\n";
	$design_userinfo .= "</ul></div>";
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
	else $design_eventlist .= "<li><span>$rEventList->eventname</span></li>\n";
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

	if(db_num($qListGroups) != 0) {
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

if($facebook_likebox_url != FALSE) {
	$design_grouplist .= '
</div>
<div class="box">
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>

<div class="fb-like-box" data-href="'.$facebook_likebox_url.'" data-width="192" data-show-faces="true" data-stream="true" data-header="false"></div>
';
}

$design_head .= '<script src="templates/shared/lancms.js?v1"></script>' . PHP_EOL;
$design_head .= '<link rel="stylesheet" type="text/css" media="all" href="templates/shared/lancms.css?v1" />' . PHP_EOL;

if(!$hide_smarty) {

	if ($enableSmarty == true) {
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
		$smarty->assign("siteUrl", getUrlBase());

		// for logo and link in the page footer
		$smarty->assign("footer", $design_footer);


		$smarty_fetch = $smarty->fetch($smarty_display);
		if ($smarty_fetch == "" or empty ($smarty_fetch) or (!$smarty_fetch))
		{
			die("Could not display smarty. Are you sure you have write-access to the tmp/template*-folders?");
		}
		print $smarty_fetch;
	} else {
		$templateFolder = __DIR__ . "/templates/" . $eventinfo->eventDesign . "/" . $eventinfo->eventDesign . ".php";
		if (file_exists($templateFolder) == true) {
			$siteUrl = getUrlBase();

			include $templateFolder;
		} else {
			throw new Exception("Template " . __DIR__ . "/templates/" . $eventinfo->eventDesign . "/" . $eventinfo->eventDesign . ".php" . " do not exists.");
		}
	}
}

db_close();
