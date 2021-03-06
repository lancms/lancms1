<?php

$userid = $_GET['user'];

$arrivalRef = $_GET['arrivalref'] ?? null;

$useradminread = acl_access ("userAdmin", "", 1);

$userR = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '".db_escape($userid)."'");
$user = db_fetch($userR);

$border = "style='border: solid 1px black; border-collapse: collapse;'";

$content .= "<table $border>\n";
$content .= sprintf ("<tr><th %s>%s</th><td %s>%s</td></tr>\n", $border, _('Name'), $border, $user->firstName." ".$user->lastName);

$content .= sprintf ("<tr><th %s>%s</th><td %s>%s</td></tr>\n", $border, _('Nick'), $border, $user->nick);


if ($useradminread != "No")
{
	$content .= sprintf ("<tr><th %s>%s</th><td %s>%s</td></tr>\n", $border, _('Email'), $border, $user->EMail);
	$content .= sprintf ("<tr><th %s>%s</th><td %s>%s</td></tr>\n", $border, _('Cellphone'), $border, $user->cellphone);
	$content .= sprintf ("<tr><th %s>%s</th><td %s>%s</td></tr>\n", $border, _('Gender'), $border, _($user->gender));
	$content .= sprintf ("<tr><th %s>%s</th><td %s>%s</td></tr>\n", $border, _('Birthday'), $border, $user->birthDay.". ".$user->birthMonth." ".$user->birthYear);
	$content .= sprintf ("<tr><th %s>%s</th><td %s>%s</td></tr>\n", $border, _('Address'), $border, $user->street);
	$content .= sprintf ("<tr><th %s>%s</th><td %s>%s</td></tr>\n", $border, _('Postnumber'), $border, $user->postNumber);

	$relativeName = $user->relative_name ?? null;
	$relativeCellphone = $user->relative_cellphone ?? null;

	$relativeString = 'None';
	if (is_string($relativeName) && $relativeName !== '') {
	    $relativeString = htmlspecialchars($relativeName);

	    if (is_string($relativeCellphone) && $relativeCellphone !== '') {
	        $relativeString .= ', ' . htmlspecialchars($relativeCellphone);
        }
    }

	$content .= sprintf ("<tr><th %s>%s</th><td %s>%s</td></tr>\n", $border, _('Guardian'), $border, $relativeString);
}


$qFindGroups = db_query("SELECT g.groupname,e.eventname FROM (".$sql_prefix."_groups g JOIN ".$sql_prefix."_group_members gm ON gm.groupID=g.ID) JOIN ".$sql_prefix."_events e ON g.eventID=e.ID WHERE gm.userID = '".$user->ID."'");

if (db_num($qFindGroups))
{
	$content .= "<tr><th>";
	$content .= _("Groupmemberships");
	$content .= "</th><td><ul>";
	while($rFindGroups = db_fetch($qFindGroups)) {
		$content .= "<li>".$rFindGroups->eventname." &ndash; ".$rFindGroups->groupname."</li>\n";
	}
	$content .= "</ul></td></tr>\n\n\n";
}

$content .= "</table>\n";


if ($useradminread != "No")
{
	$content .= "<br />\n";

	$editUrl = '?module=edituserinfo&action=editUserinfo&user=' . $user->ID;
	if (is_array($arrivalRef)) {
        $editUrl .= '&' . http_build_query(['arrivalref' => $arrivalRef]);
    }
	$content .= sprintf ("<form method='POST' action='%s'><input type='submit' value='%s' /></form>\n", $editUrl, _('Edit userinfo'));
}

