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
		if($rListCompos->signupOpen == 1) $content .= "<td style='background-color: green;' class=tdLink onClick='location.href=\"?module=compoadmin&action=disableSignup&compo=$rListCompos->ID\"'>".lang("Signup is open", "compoadmin");
		else $content .= "<td style='background-color: red;' class=tdLink onClick='location.href=\"?module=compoadmin&action=enableSignup&compo=$rListCompos->ID\"'>".lang("Signup is closed", "compoadmin");
		$content .= "</td>";
		if($rListCompos->signupOpen == 0) {
			// signup has closed, enable creating brackets etc.
			$content .= "<td class=tdLink onClick='location.href=\"?module=compoadmin&action=matchadmin&compo=$rListCompos->ID\"'>";
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

elseif($action == "matchadmin" && isset($_GET['compo'])) {
	$compo = $_GET['compo'];

	$qGetCompoinfo = db_query("SELECT * FROM ".$sql_prefix."_compos WHERE ID = '".db_escape($compo)."'");
	$rGetCompoinfo = db_fetch($qGetCompoinfo);

	$compotype = $rGetCompoinfo->type;

	$content .= "<table>";
	$content .= "<tr>";
	$content .= "<td class=tdLink onClick='location.href=\"?module=compoadmin&compo=$compo&action=randomizeMatch\"'>";
	$content .= lang("Randomize first match", "compoadmin")."</td>";
	$content .= "</tr></table>";

	$content .= "<table border=1>";
	$qFindMatches = db_query("SELECT * FROM ".$sql_prefix."_compoMatches WHERE compoID = '".db_escape($compo)."' ORDER BY matchOrder ASC");
	while($rFindMatches = db_fetch($qFindMatches)) {
		$content .= "<tr><td class=tdLink onClick='location.href=\"?module=compoadmin&compo=$compo&action=editMatch&matchID=$rFindMatches->matchID\"'>";
#		$content .= $rFindMatches->matchID;
#		$content .= "</td><td>";
		$qFindMatchmembers = db_query("SELECT * FROM ".$sql_prefix."_compoMatch_signup WHERE matchID = '$rFindMatches->matchID'");
		while($rFindMatchmembers = db_fetch($qFindMatchmembers)) {

			switch($compotype) {
				case 'clan':
					$qFindClanname = db_query("SELECT * FROM ".$sql_prefix."_groups WHERE ID = '$rFindMatchmembers->clanID'");
					$rFindClanname = db_fetch($qFindClanname);
					$content .= $rFindClanname->groupname." ";
					break;
				default:
					$content .= "unknown: $rFindMatchmembers->matchID";


			} // End switch
		} // End while
		$content .= "</td></tr>";
	} // End while rFindMatches

	$content .= "</table>";
}

elseif($action == "editMatch" && isset($_GET['matchID'])) {
	$matchID = $_GET['matchID'];
	$compoID = $_GET['compo'];

	$qFindMatch = db_query("SELECT * FROM ".$sql_prefix."_compoMatches WHERE matchID = '".db_escape($matchID)."'");
	$rFindMatch = db_fetch($qFindMatch);
	$qFindCompo = db_query("SELECT * FROM ".$sql_prefix."_compos WHERE ID = '".db_escape($compoID)."'");
	$rFindCompo = db_fetch($qFindCompo);

	$content .= "<table>";
	$content .= "<tr><th>".lang("Clan/user", "compoadmin");
	$content .= "</th><th>";
	$content .= lang("Result", "compoadmin");
	$content .= "</th><th>";
	$content .= lang("Win/lose");
	$content .= "</th></tr>";
	$content .= "<form method=POST action='?module=compoadmin&action=doEditMatch&matchID=$matchID&compoID=$compoID'>";

	$qFindSignedup = db_query("SELECT * FROM ".$sql_prefix."_compoMatch_signup WHERE matchID = '".db_escape($matchID)."'");
	while($rFindSignedup = db_fetch($qFindSignedup)) {


		switch($rFindCompo->type) {
			case 'clan':
				$qFindClan = db_query("SELECT * FROM ".$sql_prefix."_groups WHERE ID = '$rFindSignedup->clanID'");
				$rFindClan = db_fetch($qFindClan);
				$content .= "<tr><td>".$rFindClan->groupname."</td><td>";
				$content .= "<input type=text name='clan".$rFindSignedup->clanID."' value='$rFindSignedup->result'>";
				$content .= "</td><td>";

				$content .= "<select name='winlose$rFindSignedup->clanID'>";

				for($i=0;$i<count($winlosetype);$i++) {
					#echo "comparing $rFindSignedup->winlose with $winlosetype[$i]\n";
					$content .= "<option value='$winlosetype[$i]'";
					if($rFindSignedup->winlose == $winlosetype[$i]) $content .= " SELECTED";
					$content .= ">".lang($winlosetype[$i], "winlosetype")."</option>\n";
				}
				$content .= "</select>\n\n";

				$content .= "</td></tr>";
				break;
			default:
				$content .= "ERROR editMatch switch foobar";
				$content .= $rFindCompo->type." is wrong";
		} // End switch

	} // End while rFindSignedup
	$content .= "</table>\n";
	$content .= "<input type=submit value='".lang("Save", "compoadmin")."'></form>";

} // End action == editMatch

elseif($action == "doEditMatch" && !empty($_GET['matchID'])) {
	$matchID = $_GET['matchID'];
	$compoID = $_GET['compoID'];
	$qFindMatch = db_query("SELECT * FROM ".$sql_prefix."_compoMatches WHERE matchID = '".db_escape($matchID)."'");
	$rFindMatch = db_fetch($qFindMatch);
	$qFindCompo = db_query("SELECT * FROM ".$sql_prefix."_compos WHERE ID = '".db_escape($compoID)."'");
	$rFindCompo = db_fetch($qFindCompo);

	$qFindSignedup = db_query("SELECT * FROM ".$sql_prefix."_compoMatch_signup WHERE matchID = '".db_escape($matchID)."'");
	while($rFindSignedup = db_fetch($qFindSignedup)) {
		switch($rFindCompo->type) {
			case 'clan':
				$name_res = 'clan'.$rFindSignedup->clanID;
				$name_winlose = 'winlose'.$rFindSignedup->clanID;

				$result = $_POST[$name_res];
				$winlose = $_POST[$name_winlose];
#				print_r($_POST);
#				die($name.": ".$result);
				db_query("UPDATE ".$sql_prefix."_compoMatch_signup SET result = '".db_escape($result)."', winlose = '".db_escape($winlose)."' WHERE matchID = '".db_escape($matchID)."' AND clanID = '$rFindSignedup->clanID'");
				break;
			default:
				die("Error in doEditMatch -> switch");

		} // End switch

	} // end While
	header("Location: ?module=compoadmin&action=editMatch&matchID=$matchID&compo=$compoID");
} // End action = doEditMatch

elseif($action == "randomizeMatch" && isset($_GET['compo'])) {
	$compo = $_GET['compo'];
	$qGetCompoinfo = db_query("SELECT * FROM ".$sql_prefix."_compos WHERE ID = '".db_escape($compo)."'");
	$rGetCompoinfo = db_fetch($qGetCompoinfo);

	// FIXME: Should check if matches have been played, and fail if played
	db_query("DELETE FROM ".$sql_prefix."_compoMatch_signup WHERE matchID IN (SELECT matchID FROM ".$sql_prefix."_compoMatches WHERE compoID = '".db_escape($compo)."')");
	db_query("DELETE FROM ".$sql_prefix."_compoMatches WHERE compoID = '".db_escape($compo)."'");
	$matchPlayers = 1;
	$matchOrder = 1;

	$qFindSignup = db_query("SELECT * FROM ".$sql_prefix."_compoSignup WHERE compoID = '".db_escape($compo)."' ORDER BY RAND()");
	while($rFindSignup = db_fetch($qFindSignup))  {
		if($matchPlayers == 1) {
			// We've reached enough players this round, create a new match
			db_query("INSERT INTO ".$sql_prefix."_compoMatches SET compoID = '".db_escape($compo)."', matchOrder = $matchOrder");
			$matchOrder++;
		}
		$qMatchID = db_query("SELECT MAX(matchID) AS matchID FROM ".$sql_prefix."_compoMatches");
		$rMatchID = db_fetch($qMatchID);
		$matchID = $rMatchID->matchID;

		if($rGetCompoinfo->playersClan == 1) {
			db_query("INSERT INTO ".$sql_prefix."_compoMatch_signup SET matchID = '$matchID',
				clanID = 1,
				userID = '$rFindSignup->userID'");
		}
		else {
			db_query("INSERT INTO ".$sql_prefix."_compoMatch_signup SET matchID = '$matchID',
				clanID = '$rFindSignup->clanID'");
		}

		if($matchPlayers == $rGetCompoinfo->playersRound) {
			$matchPlayers = 1;
		}
		else $matchPlayers++;
	} // End while
	header("Location: ?module=compoadmin&compo=$compo&action=matchadmin");
} // End elseif action == randomizeMatch
