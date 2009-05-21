<?php

$acl = acl_access("compoadmin", "", $sessioninfo->eventID);
$action = $_GET['action'];

if(!isset($action)) {
	$qListCompos = db_query("SELECT * FROM ".$sql_prefix."_compos WHERE eventID = '$sessioninfo->eventID'");

	$content .= "<table>";
	$row = 1;
	while($rListCompos = db_fetch($qListCompos)) {
		$content .= "<tr class=listRow$row><td>";
		$content .= $rListCompos->componame;
		$content .= "</td><td>\n";
		$content .= $rListCompos->type;
		$content .= "</td>";
		if($rListCompos->signupOpen == 1) $content .= "<td style='background-color: green;' onClick='location.href=\"?module=compoadmin&action=disableSignup&compo=$rListCompos->ID\"'>".lang("Signup is open", "compoadmin");
		else $content .= "<td style='background-color: red;' onClick='location.href=\"?module=compoadmin&action=enableSignup&compo=$rListCompos->ID\"'>".lang("Signup is closed", "compoadmin");
		$content .= "</td></tr>";
		$row++;
		if($row == 3) $row = 1;
	} // End while

	$content .= "</table>\n";

	$content .= "<br />";
	if($acl == 'Admin') {
		$content .= "<form method=POST action=?module=compoadmin&action=addCompo>\n";
		$content .= "<input type=text name=componame> ".lang("Compo name", "compoadmin");
		$content .= "<br />";
		$content .= "<select name=type>\n";
		for($i=0;$i<count($compotype);$i++) {
			$content .= "<option value='$compotype[$i]'>$compotype[$i]</option>\n";
		} // End for
		$content .= "</select>\n\n";
		$content .= "<br /><input type=text name=playersClan size=2> ".lang("Number of players per clan");
		$content .= "<br /><input type=text name=playersRound size=2> ".lang("Number of clans/players per round");
		$content .= "<br /><input type=submit value='".lang("Add compo", "compoadmin")."'>";
		$content .= "</form>";
	} // End if acl == Admin


}

elseif($action == "addCompo" && $acl == 'Admin') {
	$componame = $_POST['componame'];
	$type = $_POST['type'];
	$playersClan = $_POST['playersClan'];
	$playersRound = $_POST['playersRound'];

	db_query("INSERT INTO ".$sql_prefix."_compos SET eventID = '$sessioninfo->eventID',
		componame = '".db_escape($componame)."',
		type = '".db_escape($type)."',
		playersClan = '".db_escape($playersClan)."',
		playersRound = '".db_escape($playersRound)."'
	");
	header("Location: ?module=compoadmin");
}

elseif($action == "enableSignup" && isset($_GET['compo']) && $acl == ('Admin' || 'Write')) {
	$compo = $_GET['compo'];

	db_query("UPDATE ".$sql_prefix."_compos SET signupOpen = 1 WHERE ID = '".db_escape($compo)."'");

	header("Location: ?module=compoadmin");

}

elseif($action == "disableSignup" && isset($_GET['compo']) && $acl == ('Admin' || 'Write')) {

	$compo = $_GET['compo'];

	db_query("UPDATE ".$sql_prefix."_compos SET signupOpen = 0 WHERE ID = '".db_escape($compo)."'");

	header("Location: ?module=compoadmin");

}
