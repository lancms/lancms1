<?php

$action = $_GET['action'];
if(empty($page)) $page = $_GET['page'];

if(empty($action)) $action = "viewPage";

$acl_access = acl_access("static", $page, $sessioninfo->eventID);

// Die if user does not have access at all
if($acl_access == "No" || empty($acl_access))
	die("Sorry, you do not have access to this!");

if($action == "viewPage" && !empty($page))
{
	/* This function will view the requested page. */
	// First, check users rights
	$acl_access = acl_access("static", $page, $sessioninfo->eventID);

	// Die if user does not have access at all
	if($acl_access == "No" || empty($acl_access))
		die("Sorry, you do not have access to this!");

	// Find the page
	$qViewPage = db_query("SELECT * FROM ".$sql_prefix."_static WHERE ID = '".db_escape($page)."'");
	$rViewPage = db_fetch($qViewPage);

	/* FIXME: Should probably be.... more... */
	$content .= $rViewPage->page;

	db_query("UPDATE ".$sql_prefix."_static SET pageViews = pageViews + 1 WHERE ID = ".db_escape($page));
} // End if action = viewPage


elseif($action == "listEventPages")
{
	/* This action list all static pages (that you have access to) */
	// No point in checking rights here; it is checked later

	$qListPages = db_query("SELECT * FROM ".$sql_prefix."_static
		WHERE eventID = ".$sessioninfo->eventID."
		ORDER BY header ASC");

	if(mysql_num_rows($qListPages) != 0) {
		$content .= '<table>';
		while($rListPages = db_fetch($qListPages))
		{
			// Check that what rights you have
			$rACL_access = acl_access("static", $rListPages->ID, $sessioninfo->eventID);
			if($rACL_access != ("Admin" || "Write"));
			else {
				// You do have some sort of right. Display link
				$content .= "<tr><td><a href=\"?module=static&action=editPage&page=$rListPages->ID\">";
				$content .= $rListPages->header."</a>\n";
				if($rACL_access == 'Admin') {
					$content .= "</td><td><a href=\"?module=static&action=editACL&page=$rListPages->ID\">";
					$content .= lang("Edit access", "static");
				} // End if rACL_access = 'Admin'
				$content .= "</td></tr>";
			} // End else

		} // End while
		$content .= "</table>\n";
	}
	if(acl_access("static", "", $sessioninfo->eventID) == 'Admin') {
		$content .= "<form method=\"post\" action=\"?module=static&amp;action=addNew\">\n";
		$content .= "<p class=\"nopad\"><input type=\"text\" name=\"name\" size=\"15\" />";
		$content .= "<input type=\"submit\" value='".lang("Add new page", "static")."' /></p>";
		$content .= "</form>";
	} //
       if(acl_access("globaladmin", "", 0) == 'Admin') {
                // User has globaladmin-access, give access to editing system messages
                $content .= "<br /><form method=GET>";
                $content .= "<input type=hidden name=action value=editPage>\n";
                $content .= "<input type=hidden name=module value=static>\n";
                $content .= "<select name=page>\n";
                for($i=0;$i<count($systemstatic);$i++) {
                        $content .= "<option value='$systemstatic[$i]'>$systemstatic[$i]</option>";
                }
                $content .= "</select>\n";
                $content .= "<input type=submit value='".lang("Edit system message", "static")."'>";
                $content .= "</form>";
        } // End if acl_access(globaladmin)

} // End action = listEventPages

elseif($action == "editPage" && !empty($page))
{
	if(is_numeric($page)) {
		$acl_access = acl_access("static", $page, $sessioninfo->eventID);

		// Die if user does not have access at all
		if($acl_access != ("Admin" || "Write") || empty($acl_access))
			die("Sorry, you do not have access to this! (access is $acl_access)");
	$qStaticPage = db_query("SELECT * FROM ".$sql_prefix."_static WHERE ID = '".db_escape($page)."'");

	} elseif(acl_access("globaladmin", "", 1) == 'Admin') {
		$qStaticPage = db_query("SELECT * FROM ".$sql_prefix."_static WHERE header = '".db_escape($page)."' AND type = 'system' AND eventID = 1");
	} else {
		die("Something went wrong... hacking?");
	} // End else
		
	/* Edit that page */
	// FIXME: This should be a HTML-editor!
	$rStaticPage = db_fetch($qStaticPage);

	$content .= "<form method=POST action=?module=static&action=doEditPage&page=$page>\n";
	if($rStaticPage->type == 'static') $content .= "<input type=text name=header value='$rStaticPage->header'>\n";
	else $content .= $page."\n";
	$content .= "<br /><textarea rows=25 cols=60 name=staticPage>".$rStaticPage->page."</textarea>\n";
	$content .= "<br /><input type=submit value='".lang("Save", "static")."'>\n";
	$content .= "</form>";

} // End action = editPage

elseif($action == "doEditPage" && !empty($page))
{
	$header = $_POST['header'];
	$pageContent = $_POST['staticPage'];
	/* Submit changes to DB */
	// First, check ACL-access
	if(is_numeric($page)) {
		$acl_access = acl_access("static", $page, $sessioninfo->eventID);

		// Die if user does not have access at all
		if($acl_access != ("Admin" || "Write") || empty($acl_access))
			die("Sorry, you do not have access to this!");
		

		db_query("UPDATE ".$sql_prefix."_static
			SET page = '".db_escape($pageContent)."',
			header = '".db_escape($header)."',
			modifiedByUser = ".$sessioninfo->userID.",
			modifiedTimestamp = ".time()."
			WHERE ID = ".db_escape($page));
		header("Location: ?module=static&action=viewPage&page=$page");
	} elseif(acl_access("globaladmin", "", 1) == 'Admin') {
		$qFindExisting = db_query("SELECT * FROM ".$sql_prefix."_static WHERE type='system' AND header = '".db_escape($page)."'");
		if(db_num($qFindExisting) == 0) {
			db_query("INSERT INTO ".$sql_prefix."_static SET 
				header = '".db_escape($page)."',
				page = '".db_escape($pageContent)."',
				eventID = 1,
				type='system',
				createdByUser = ".$sessioninfo->userID.",
				createdTimestamp = ".time());
		} else {
			db_query("UPDATE ".$sql_prefix."_static
				SET page = '".db_escape($pageContent)."',
	                        modifiedByUser = ".$sessioninfo->userID.",
         	                modifiedTimestamp = ".time()."
				WHERE header = '".db_escape($page)."'
				AND type = 'system'");
		} // End else
		header("Location: ?module=static&action=listEventPages");
	} // End elseif acl_access globaladmin
}

elseif($action == "addNew") {
	if(acl_access("static", "", $sessioninfo->eventID) != 'Admin') die("You don't have access to create new pages");
	$name = $_POST['name'];

	$qCheckName = db_query("SELECT * FROM ".$sql_prefix."_static WHERE eventID = $sessioninfo->eventID
		AND header LIKE '".db_escape($name)."'");
	if(db_num($qCheckName) == 0) {
		db_query("INSERT INTO ".$sql_prefix."_static SET
			eventID = $sessioninfo->eventID,
			header = '".db_escape($name)."',
			createdByUser = '$sessioninfo->userID',
			createdTimestamp = ".time()."
		");
		$qFindNewPageID = db_query("SELECT ID FROM ".$sql_prefix."_static WHERE eventID = $sessioninfo->eventID
		AND header LIKE '".db_escape($name)."'");
		$rFindNewPageID = db_fetch($qFindNewPageID);
		header("Location: ?module=static&action=editPage&page=$rFindNewPageID->ID");
	} // End if

} // End elseif action == "addNew"

elseif(($action == "editACL" OR $action == "changeACL") && isset($page)) {
	if($acl_access != 'Admin') die("No access");
	// Edit ACLs for this static page
	$qGetPageInfo = db_query("SELECT * FROM ".$sql_prefix."_static WHERE ID = '".db_escape($page)."'");
	$rGetPageInfo = db_fetch($qGetPageInfo);

	$content .= lang("Editing: ", "static");
	$content .= $rGetPageInfo->header;
	$content .= "<br /><table>";

	$qGetCurrentACL = db_query("SELECT a.*,g.groupname FROM ".$sql_prefix."_ACLs a JOIN ".$sql_prefix."_groups g ON g.ID=a.groupID
		WHERE a.eventID = '$sessioninfo->eventID'
		AND subcategory = '$page'
		AND accessmodule = 'static'");
	while($rGetCurrentACL = db_fetch($qGetCurrentACL)) {
		$content .= "<tr><td>";
		$content .= $rGetCurrentACL->groupname;
		$content .= "</td><td>";

		if($action == 'changeACL' && $_GET['groupID'] == $rGetCurrentACL->groupID)
		{
			$content .= "<form method=POST action=?module=static&amp;action=doChangeACL&amp;groupID=$rGetCurrentACL->groupID&amp;page=$page>\n";
			$content .= "<select name=groupRights>\n";
			$content .= option_rights($rGetCurrentACL->access);
			$content .= "</select><input type=submit value='".lang("Save", "group")."'>";
			$content .= "</form>";
		} // end action == changeACL
		else
		{
			$content .= "<a href=\"?module=static&action=changeACL&groupID=$rGetCurrentACL->groupID&page=$page\">";
			$content .= $rGetCurrentACL->access;
			$content .= "</a>\n";
		} // End action != 'changeACL'

		$content .= "</td></tr>";

	} // End while
	$content .= "</table>";

	$content .= "<form method='POST' action='?module=static&action=addNewACL&page=$page'>\n";
	// Get all global or event accessgroups
	$qGetGroups = db_query("SELECT * FROM ".$sql_prefix."_groups
		WHERE grouptype = 'access'
		AND eventID IN ($sessioninfo->eventID, 1)");
	$content .= "<select name=group>\n";
	while($rGetGroups = db_fetch($qGetGroups)) {
		$content .= "<option value='$rGetGroups->ID'>$rGetGroups->groupname</option>\n";
	} // End while rGetGroups

	$content .= "</select>";
	$content .= "<input type=submit value='".lang("Add group access", "static")."'>";
	$content .= "</form>";
}

elseif($action == "addNewACL" && isset($page)) {
	if($acl_access != 'Admin') die("No access");
	$group = $_POST['group'];

	$qCheckExistingACL = db_query("SELECT * FROM ".$sql_prefix."_ACLs
		WHERE groupID = '".db_escape($group)."'
		AND eventID = '$sessioninfo->eventID'
		AND accessmodule = 'static'
		AND subcategory = '".db_escape($page)."'
	");
	if(db_num($qCheckExistingACL) == 0) {
		db_query("INSERT INTO ".$sql_prefix."_ACLs
			SET groupID = '".db_escape($group)."',
			eventID = '$sessioninfo->eventID',
			accessmodule = 'static',
			subcategory = '".db_escape($page)."',
			access = 'Read'");
	}
	header("Location: ?module=static&action=editACL&page=$page");

} // End action == addNewACL

elseif($action == "doChangeACL" && isset($_GET['groupID']) && isset($page)) {
	if($acl_access != 'Admin') die("No access");
	$groupRights = $_POST['groupRights'];

	db_query("UPDATE ".$sql_prefix."_ACLs SET access = '".db_escape($groupRights)."'
		WHERE groupID = '".db_escape($_GET['groupID'])."'
		AND subcategory = '".db_escape($page)."'
		AND accessmodule = 'static'
		AND eventID = '$sessioninfo->eventID'
		");
	header("Location: ?module=static&action=editACL&page=$page");

} // End elseif action = doChangeACL

