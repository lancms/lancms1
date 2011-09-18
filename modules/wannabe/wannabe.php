<?php

$eventID = $sessioninfo->eventID;
$action = $_GET['action'];

if($sessioninfo->userID == 1) die(lang("Please login to apply for crew", "wannabe"));

if(!isset($action)) {
	$content .= "<h2>".lang ("Apply as crew", "wannabe")."</h2>";
	if($_GET['Application'] == 'saved') $content .= "<h1>"._("Your application is saved")."</h1>";

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
			$content .= ">";
			switch($i) {
				case "0":
					$content .= lang("Nothing selected");
					break;
				case "1":
					$content .= lang("Of course!");
					break;
				case "2":
					$content .= lang("Sure");
					break;
				case "3":
					$content .= lang("Probably");
					break;
				case "4":
					$content .= lang("I'd rather not");
					break;
				case "5":
					$content .= lang("Not at all");
					break;
				default:
					$content .= lang("Unknown option");
			} // End switch
			$content .= "</option>\n";
		} // End for
		$content .= "</select>\n";
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
	$content .= "<tr><td></td><td><input type=submit value='".lang("Save", "wannabe")."'></form>";
	$content .= "<form method=POST action=?module=wannabe&action=removeApplication>\n";
	$content .= "<input type=submit value='".lang("Delete application", "wannabe")."'></form>";
	$content .= "</td></tr>";
	$content .= "</table>";

} // End if(!isset($action))


elseif($action == "doApplication") {

	$qFindCrews = db_query("SELECT ID FROM ".$sql_prefix."_wannabeCrews WHERE eventID = $eventID");
	while($rFindCrews = db_fetch($qFindCrews)) {
		$post = $_POST['crew'.$rFindCrews->ID];
		$log_new['crew'.$rFindCrews->ID] = $post;
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
		$log_new['appID_'.$rFindQuestions->ID];
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
	log_add("wannabe", "doApplication", serialize($log_new));
	header("Location: ?module=wannabe&Application=saved");
} // End doApplication

elseif($action == "removeApplication" ) {
	$content .= lang("Are you sure you want to delete your crewapplication?", "wannabe");
	$content .= "<br><a href=?module=wannabe&action=doRemoveApplication>".lang("Yes, I'm sure", "wannabe")."</a> - ";
	$content .= "<a href=?module=wannabe>".lang("No, I still wish to apply for crew", "wannabe")."</a>";
}

elseif($action == "doRemoveApplication") {

	db_query("DELETE FROM ".$sql_prefix."_wannabeResponse WHERE questionID IN (SELECT ID FROM ".$sql_prefix."_wannabeQuestions WHERE eventID = '$sessioninfo->eventID') AND userID = '$sessioninfo->userID'");
	db_query("DELETE FROM ".$sql_prefix."_wannabeCrewResponse WHERE crewID IN (SELECT ID FROM ".$sql_prefix."_wannabeCrews WHERE eventID = '$sessioninfo->eventID') AND userID = '$sessioninfo->userID'");
	log_add("wannabe", "doRemoveApplication");
	header("Location: ?module=wannabe");
}
