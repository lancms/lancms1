<?php

$acl = acl_access("compoadmin", "", $sessioninfo->eventID);
$action = $_GET['action'];
$compoID = $_GET['compoID'];

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
		if($rListCompos->signupOpen == 1) $content .= "<td style='background-color: green;' class=tdLink onClick='location.href=\"?module=compoadmin&action=disableSignup&compo=$rListCompos->ID\"'>".lang("Signup is open", "compoadmin");
		else $content .= "<td style='background-color: red;' class=tdLink onClick='location.href=\"?module=compoadmin&action=enableSignup&compo=$rListCompos->ID\"'>".lang("Signup is closed", "compoadmin");
		$content .= "</td>";
		if($rListCompos->signupOpen == 0) {
			// signup has closed, enable creating brackets etc.
			$content .= "<td class=tdLink onClick='location.href=\"?module=compoadmin&action=matchadmin&compoID=$rListCompos->ID\"'>";
			$content .= lang("Match admin", "compoadmin");
		} else {
			$content .= "<td>";
			$content .= lang("Match admin (close signup to enable)", "compoadmin");
		}
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

elseif($action == "matchadmin" && isset($compoID)) {
	$qGetCompoinfo = db_query("SELECT * FROM ".$sql_prefix."_compos WHERE ID = '".db_escape($compoID)."'");
	$rGetCompoinfo = db_fetch($qGetCompoinfo);

	$compotype = $rGetCompoinfo->type;

	$qFindRounds = db_query("SELECT * FROM ".$sql_prefix."_compoRound WHERE compoID = '".db_escape($compoID)."' ORDER BY compoRoundNumber ASC");
	while($rFindRounds = db_fetch($qFindRounds)) {
		$content .= "<table>";
		$content .= "<tr><th>";
		$content .= "Round ".$rFindRounds->compoRoundNumber;
		$content .= "</th><th>";
		$content .= $rFindRounds->roundName;
		$content .= "</th>";
		$content .= "<td class=tdLink onClick='location.href=\"?module=compoadmin&action=randomizeRound&compoID=$compoID&round=$rFindRounds->roundID\"'>";
		$content .= "test?";
		$content .= "</td></tr>";

		$content .= "</table>";

		
		$content .= "<br /><hr>";
	} // End while rFindRounds
	
	$qMaxRound = db_query("SELECT MAX(compoRoundNumber) AS max_roundnumber FROM ".$sql_prefix."_compoRound WHERE compoID = '".db_escape($compoID)."'");
	$rMaxRound = db_fetch($qMaxRound);
	$roundnumber = $rMaxRound->max_roundnumber + 1;

	$content .= "<form method=POST action=?module=compoadmin&compoID=$compoID&action=addRound>\n";
	$content .= "<input type=text name=roundName value='$rGetCompoinfo->componame round $roundnumber'>".lang("Round name", "compoadmin");
	$content .= "<input type=hidden name=roundnumber value='$roundnumber'>\n";
	$content .= "<br />";
	$content .= "<input type=submit value='".lang("Add round", "compoadmin")."'>";
	$content .= "</form>\n\n\n";
	

	

} // End matchadmin

elseif($action == 'addRound' && isset($compoID)) {
	$roundName = $_POST['roundName'];
	$roundnumber = $_POST['roundnumber'];
	
	db_query("INSERT INTO ".$sql_prefix."_compoRound SET
		compoID = '".db_escape($compoID)."',
		compoRoundNumber = '".db_escape($roundnumber)."',
		roundName = '".db_escape($roundName)."'");
	header("Location: ?module=compoadmin&action=matchadmin&compoID=$compoID");


} // End action == addRound

elseif ($action == "randomizeRound" && isset($compoID) && isset($_GET['round'])) {
	$round = $_GET['round'];

	$qGetRound = db_query("SELECT * FROM ".$sql_prefix."_compoRound WHERE roundID = '".db_escape($round)."'");
	$rGetRound = db_fetch($qGetRound);

	
}
