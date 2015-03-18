<?php

header("Content-type: text/html; charset=UTF-8");
require_once 'config.php';
echo '<html><head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
echo "</head><body>";
//echo "<img src=createimage.php border=1>";


$q = query("SELECT DISTINCT u.ID AS ID,nick AS nick, CONCAT(u.firstName,' ',u.lastName) AS name, gm.groupID AS groupID FROM GO_users u JOIN GO_group_members gm ON u.ID=gm.userID WHERE gm.groupID  IN (SELECT ID FROM GO_groups WHERE eventID IN (23) AND groupType = 'access') ORDER BY name");

while($r = fetch($q)) {
	$text0 = urlencode($r->name);
	$g = query("SELECT groupname AS name FROM GO_groups WHERE ID = '$r->groupID'");
	$group = fetch($g);
	switch ($r->nick) {
		case "apefisk":
			$group->name = 'Crewcare-chief';
			break;
#		case "joss":
#			$group->name = 'PIG-chief';
#			break;
		case "Knight":
			$group->name = 'Game-chief';
			break;
		case "Andozer":
			$group->name = 'PartyInfo-chief';
			break;
		case "Supersiv":
			$group->name = 'Kiosk-chief';
			break;
		case "Laaknor":
			$group->name = 'Security-chief';
			break;
		case "pushit":
			$group->name = 'Hovedansvarlig';
			break;
		case "Yrjan":
			$group->name = 'Okonomiadmin';
			break;
#		case "Supersiv":
#			$group->name = 'Hovedansvarlig';
#			break;
		
	}

	$text1 = utf8_decode(urlencode($group->name));
	$text2 = utf8_decode(urlencode($r->nick));
	//echo $text0.":".$text1;
	echo "<img src=createimage.php?text0=$text0&text1=$text1&text2=$text2&bar=UID$r->ID border=1>\n";
}
