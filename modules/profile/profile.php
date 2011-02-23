<?php

$user = $_GET['user'];


$qFindUser = db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = '".db_escape($user)."'");
$rFindUser = db_fetch($qFindUser);


$content .= "<table>";
$content .= "<tr><td>";
$content .= lang("Name", "profile");
$content .= "</td><td>";
$content .= $rFindUser->firstName." ".$rFindUser->lastName;
$content .= "</td></tr>";

$content .= "<tr><td>";
$content .= lang("Nick", "profile");
$content .= "</td><td>";
$content .= $rFindUser->nick;
$content .= "</td></tr>";

$content .= "<tr><td>";
$content .= lang("Groupmemberships", "profile");
$content .= "</td><td><ul>";
$qFindGroups = db_query("SELECT g.groupname,e.eventname FROM (".$sql_prefix."_groups g JOIN ".$sql_prefix."_group_members gm ON gm.groupID=g.ID) JOIN ".$sql_prefix."_events e ON g.eventID=e.ID WHERE gm.userID = '".db_escape($user)."'");
while($rFindGroups = db_fetch($qFindGroups)) {
	$content .= "<li>".$rFindGroups->eventname." &ndash; ".$rFindGroups->groupname."</li>\n";
}
$content .= "</ul></td></tr>\n\n\n";


$content .= "</table>";


$useradminread = acl_access ("userAdmin", "", 1);
if ($useradminread != "No")
{
	$content .= "<a href='index.php?module=edituserinfo&action=editUserinfo&user=".$rFindUser->ID."'>"._("Edit userinfo")."</a>";
}

