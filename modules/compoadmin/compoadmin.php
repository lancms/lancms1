<?php

$acl = acl_access("compoadmin", "", $sessioninfo->eventID);
$action = $_GET['action'];

if(!isset($action)) {
	$qListCompos = db_query("SELECT * FROM ".$sql_prefix."_compos WHERE eventID = '$sessioninfo->eventID'");

	$content .= "<table>";
	while($rListCompos = db_fetch($qListCompos)) {
		$content .= "<tr><td>";
		$content .= $rListCompos->componame;
		$content .= "</td><td>\n";
		$content .= $rListCompos->type;
		$content .= "</td></tr>";
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
