#!/usr/bin/env php
<?php

#
# This script is created by Mathias Bøhn Grytemark.
# Released under GPLv2 like the rest of lancms.
#
# 
# The purpose of this script is to automaticly synchronize mailing lists maintained by mailman.
# For this to work, the script needs to be run from contrib/ because we import the MySQL-settings from config.php/OverrideConfig.php.
#
# This means you have to set the $lancmsdir parameter to let us know which directory lancms lives in.
#
# Mailman also needs to run on the same machine as the script and lancms.
#
# Symlink the script into /etc/cron.hourly/ as root to run it automaticly.
#    ''
#
#
# The script assumes mailing lists are named the same as the groups. If they are not: see the $reallistname examples.
# To create maling lists for whole crews, see the $eventcrewlist example.
#
# To activate mailing list for a group: 
# - Add "$eventaccess[] = 'mailinglist'" to OverideConfig.php
# - Give the group access to the module 'mailinglist' via the eventadmin user interface.



##### WHERE DOES LANCMS LIVE?

// Example:
// $lancmsdir = '/var/www/lancms/';

// Or like we do it:
#$lancmsdir = '/data/web/no/globeorg/lancms/htdocs/';
$lancmsdir = '/var/www/partysys.globelan.no/htdocs/';


##### GROUP NAME OVERRIDE
$reallistname['77'] = "globelan-17-security";
$reallistname['74'] = "globelan-17-kiosk";
$reallistname['108'] = 'globelan-20';


##### EVENTS WITH CREWLIST
$eventcrewlist['13'] = "globelan-17-crew";




##### SQL_INFO IS COLLECTED FROM config.php or OverrideConfig.php!

require_once ($lancmsdir.'/config.php');
include_once ($lancmsdir.'/OverrideConfig.php');

require_once ($lancmsdir . "/inc/db_functions.php");


##############################################################

# sql table prefix
$spref = $sql_prefix;

db_connect();

##############################################################



$qGroups = db_query ("SELECT g.ID AS ID, g.groupname AS name FROM ".$spref."_groups AS g, ".$spref."_ACLs as acl WHERE acl.groupID=g.ID AND acl.accessmodule='mailinglist' AND acl.access!='No'");

if (!db_num ($qGroups))
{
	exit ("No groups with mailinglist activated!\n");
}

while ($rGroups = db_fetch ($qGroups))
{
	$members = "";

	if (isset($reallistname[$rGroups->ID]))
	{
		$listname = $reallistname[$rGroups->ID];
	}
	else
	{
		$listname = $rGroups->name;
	}

	print "Syncing group ".$rGroups->name." (mailinglist: ".$listname."\n";

	$qMembers = db_query ("SELECT u.EMail AS email FROM ".$spref."_users AS u, ".$spref."_group_members AS gm WHERE gm.groupID='".$rGroups->ID."' AND u.ID=gm.userID");
	if (!db_num ($qMembers))
	{
		print "# No members for this list\n\n";
		continue;
	}
	while ($rMembers = db_fetch ($qMembers))
	{
#		print "* ".$rMembers->email."\n";  #debug
		$members .= $rMembers->email."\n";
	}


	system ('echo "'.$members.'" | sync_members -w=no -g=yes -a=no -f - '.$listname);

	print "\nFinished syncing group ".$rGroups->name." (mailinglist: ".$listname."\n\n\n";
}


foreach ($eventcrewlist as $event => $listname)
{
	$qEventname = db_query ("SELECT eventname FROM ".$spref."_events WHERE ID='".$event."'");
	if (!db_num ($qEventname))
	{
		print "# Event ".$event." doesn't exist\n\n";
		continue;
	}
	$rEventname = db_fetch ($qEventname);
	print "Syncing event crew list for event ".$rEventname->eventname." (#".$event.") (mailinglist: ".$listname.")\n";

	$qMembers = db_query ("SELECT DISTINCT u.EMail as email FROM ".$spref."_users AS u, ".$spref."_group_members AS gm, ".$spref."_ACLs as acl WHERE u.ID=gm.userID AND acl.groupID=gm.groupID AND acl.accessmodule='crewlist' AND acl.access!='No' AND acl.eventID='".$event."'") or print ("# SQL error: ".mysql_error ()."\n");

	if (!db_num ($qMembers))
	{
		print "# No members for this list\n\n";
		continue;
	}
	while ($rMembers = db_fetch ($qMembers))
	{
#		print "* ".$rMembers->email."\n";  #debug
		$members .= $rMembers->email."\n";
	}
	system ('echo "'.$members.'" | sync_members -w=no -g=yes -a=no -f - '.$listname);


#	$qMembers = mysql_query("SELECT DISTINCT u.EMail as email FROM ".$spref."_users AS u,".$spref."_group_members gm WHERE gm.userID=u.ID 
#		JOIN ".$spref."_ACLs acl ON acl.groupID=gm.groupID 
#		WHERE acl.accessmodule = 'crewlist' AND acl.access != 'No' AND acl.eventID = '".$event."'") or die ("# SQL error: ".mysql_error());

	print "\nFinished syncing event crew list for event ".$rEventname->eventname." (#".$event.") (mailinglist: ".$listname.")\n\n\n";
}

db_close();


?>
