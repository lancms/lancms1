<?php

$eventID = $sessioninfo->eventID;
$action = $_GET['action'];

if(!isset($action)) {

	$qListQuestions = db_query("SELECT * FROM ".$sql_prefix."_wannabeQuestions WHERE eventID = $eventID");
	$content .= "<table>";
	$content .= "<form method=POST action=?module=wannabe&action=doApplication>";
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
