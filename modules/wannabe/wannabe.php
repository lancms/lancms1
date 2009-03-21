<?php

$eventID = $sessioninfo->eventID;
$action = $_GET['action'];

if(!isset($action)) {

	$content .= "<table>";
	$content .= "<form method=POST action=?module=wannabe&action=doApplication>";

	// First, list up possible crews
	$qListCrews = db_query("SELECT crew.ID,crew.crewname,
		(SELECT response FROM ".$sql_prefix."_wannabeCrewResponse
			WHERE crewID=crew.ID AND userID = $sessioninfo->userID) AS response
		FROM ".$sql_prefix."_wannabeCrews crew
		WHERE crew.eventID = $eventID");
	while($rListCrews = db_fetch($qListCrews)) {
		$content .= "<tr><td>";
		$content .= $rListCrews->crewname;
		$content .= "</td><td>";
		$content .= "<select name=crew".$rListCrews->ID.">\n";
		for($i=0;$i<6;$i++) {
			$content .= "<option value=$i";
			if($rListCrews->response == $i) $content .= " SELECTED";
			$content .= ">".lang("WannabeCrewListPreference".$i, "wannabe_crewprefs")."</option>\n";
		} // End for
		$content .= "</select>";
	} // End while



	$qListQuestions = db_query("SELECT * FROM ".$sql_prefix."_wannabeQuestions WHERE eventID = $eventID");

	while($rListQuestions = db_fetch($qListQuestions)) {
		$content .= "<tr><td>";
		$content .= $rListQuestions->question;
		$content .= "</td><td>";

		// Find users answer to this question
		$qMyResponse = db_query("SELECT response FROM ".$sql_prefix."_wannabeResponse WHERE
			userID = ".$sessioninfo->userID." AND questionID = ".$rListQuestions->ID);

		$rMyResponse = db_fetch($qMyResponse);

		switch($rListQuestions->questionType) {
			case "text":
				$content .= "<textarea name=appID_".$rListQuestions->ID." cols=65 rows=10>$rMyResponse->response</textarea>";
				break;

			case "select":
				$qFindSelect = db_query("SELECT * FROM ".$sql_prefix."_wannabeQuestionInfo WHERE questionID = $rListQuestions->ID");
				$content .= "<select name=appID_".$rListQuestions->ID.">";

				while($rFindSelect = db_fetch($qFindSelect)) {
					$content .= "<option value='".$rFindSelect->ID."'";
					if($rFindSelect->ID == $rMyResponse->response) $content .= " SELECTED";
					$content .= ">".$rFindSelect->response."</option>";
				} // End while
				$content .= "</select>";
				break;
			case "radio":
				break;
			case "checkbox":
				$content .= "<input type=checkbox name=appID_".$rListQuestions->ID;
				if($rMyResponse->response == "on") $content .= " CHECKED";
				$content .= ">";
				break;

		} // End switch
		$content .= "</td></tr>";


	} // End while rListQuestions
	$content .= "<tr><td></td><td><input type=submit value='".lang("Save", "wannabe")."'></form></td></tr>";
	$content .= "</table>";

} // End if(!isset($action))


elseif($action == "doApplication") {

	$qFindCrews = db_query("SELECT ID FROM ".$sql_prefix."_wannabeCrews WHERE eventID = $eventID");
	while($rFindCrews = db_fetch($qFindCrews)) {
		$post = $_POST['crew'.$rFindCrews->ID];
		$qCheckResponded = db_query("SELECT * FROM ".$sql_prefix."_wannabeCrewResponse
			WHERE crewID = '$rFindCrews->ID' AND userID = '$sessioninfo->userID'");
		if(db_num($qCheckResponded) > 0) {
			db_query("UPDATE ".$sql_prefix."_wannabeCrewResponse SET response = '".db_escape($post)."'
				WHERE userID = $sessioninfo->userID
				AND crewID = $rFindCrews->ID");
		} // End if db_num > 0
		else {
			db_query("INSERT INTO ".$sql_prefix."_wannabeCrewResponse SET response = '".db_escape($post)."',
				userID = $sessioninfo->userID,
				crewID = $rFindCrews->ID");
		} // End else
	} // End while rFindCrews



	$qFindQuestions = db_query("SELECT ID FROM ".$sql_prefix."_wannabeQuestions WHERE eventID = $eventID");

	while($rFindQuestions = db_fetch($qFindQuestions)) {
		#$appID = "appID_"$rFindQuestions->ID'
		$post = $_POST['appID_'.$rFindQuestions->ID];
		#die($post);
		$qCheckResponded = db_query("SELECT * FROM ".$sql_prefix."_wannabeResponse WHERE
			questionID = $rFindQuestions->ID AND userID = $sessioninfo->userID");
		if(db_num($qCheckResponded) > 0) {
			db_query("UPDATE ".$sql_prefix."_wannabeResponse SET response = '".db_escape($post)."'
				WHERE questionID = $rFindQuestions->ID AND userID = $sessioninfo->userID");
		} // End if db_num(qCheckResponded)
		else
			db_query("INSERT INTO ".$sql_prefix."_wannabeResponse SET
				response = '".db_escape($post)."',
				questionID = $rFindQuestions->ID,
				userID = $sessioninfo->userID
				");

	} // End while
	header("Location: ?module=wannabe");
} // End doApplication
