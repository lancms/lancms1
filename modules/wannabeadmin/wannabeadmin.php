<?php

$eventID = $sessioninfo->eventID;
$action = $_GET['action'];
$acl_access = acl_access("wannabeadmin", "", $eventID);

if($acl_access == 'No') die("You don't have access to this");


if($action == "adminWannabe")
{
	/* Adminlist for wannabe-actions */

	if($acl_access == "Admin")
	{
		// User has wannabe adminrights
		$content .= "<br /><a href=\"?module=wannabeadmin&amp;action=questions\">".lang("Questions", "wannabeadmin")."</a>\n";
		$content .= "<br /><a href=\"?module=wannabeadmin&amp;action=crews\">".lang("Crews", "wannabeadmin")."</a>\n";

	} // End acl_access = Admin

	if($acl_access == 'Write' || $acl_access == 'Admin')
	{
		// User has wannabe write-access (may see and write comments)
		$content .= "<br /><a href=\"?module=wannabeadmin&amp;action=listApplications\">".lang("View Applications", "wannabeadmin")."</a>";

	} // End acl_access > Write


} // End if action == "adminWannabe"


elseif(($action == "questions" || $action == "editQuestion" || $action == "editAnswers") && $acl_access == "Admin")
{
	/* Admin questions */

	// First. List up all the questions that exists
	$qListQuestions = db_query("SELECT * FROM ".$sql_prefix."_wannabeQuestions
		WHERE eventID = '".db_escape($eventID)."' ORDER BY questionOrder ASC, ID ASC");

	if(mysql_num_rows($qListQuestions) != 0) {
		$content .= '<table>';

		while($rListQuestions = db_fetch($qListQuestions))
		{
			$content .= "<tr><td>\n";
			$content .= "<a href=\"?module=wannabeadmin&amp;action=editQuestion&amp;questionID=$rListQuestions->ID\">";
			$content .= $rListQuestions->question;
			$content .= "</a>";
			$content .= "</td><td>\n";

			// Questiontype is text. Can't edit answers
			if($rListQuestions->questionType == "text")
			{
				$content .= $rListQuestions->questionType;
			} // End if questionType == text

			// Questiontype can have answers
			else
			{
				$content .= "<a href=\"?module=wannabeadmin&amp;action=editAnswers&amp;questionID=$rListQuestions->ID\">";
				$content .= $rListQuestions->questionType;
				$content .= "</a>\n";
			} // End else (questiontype can have answers)
			$content .= "</td></tr>\n\n";
		} // End while rListQuestions

		$content .= "</table>\n\n\n\n";
	}

	// If action == questions, we are not editing any questions.
	// Displaying add new question-form
	if($action == "questions")
	{
		$content .= "<form method=\"post\" action=\"?module=wannabeadmin&amp;action=addQuestion\">\n";
		$content .= "<p class=\"nopad\"><textarea name=\"question\" rows=\"10\" cols=\"60\">".lang("New question", "wannabeadmin")."</textarea></p>\n";
		$content .= "<p class=\"nopad\"><select name=\"questionType\">\n";
		/* List up possible answer-types */
		$content .= "<option value=\"text\">".lang("Text answer-field", "wannabeadmin")."</option>\n";
		$content .= "<option value=\"select\">".lang("Dropdown", "wannabeadmin")."</option>\n";
		$content .= "<option value=\"checkbox\">".lang("Checkbox", "wannabeadmin")."</option>\n";
		/* End listing answer-types */
		$content .= "</select></p>\n\n";
		$content .= "<p class=\"nopad\"><input type=\"submit\" value='".lang("Add new question", "wannabeadmin")."' /></p>";
		$content .= "</form>\n";
	} // End if action == questions

	// if action == editQuestion. Display edit-question-form
	elseif($action == "editQuestion" && isset($_GET['questionID']))
	{
		// Add a link back to questionlist
		$content .= "<br /><a href=\"?module=wannabeadmin&amp;action=questions\">".lang("Cancel edit", "wannabeadmin")."</a>\n\n";

		// Get info about the question
		$qGetQuestion = db_query("SELECT * FROM ".$sql_prefix."_wannabeQuestions
			WHERE ID = '".db_escape($_GET['questionID'])."'");
		$rGetQuestion = db_fetch($qGetQuestion);


		$content .= "<br /><form method=\"post\" action=\"?module=wannabeadmin&amp;action=changeQuestion&amp;questionID=$rGetQuestion->ID\">\n";
		$content .= "<p class=\"nopad\"><textarea name=\"question\" cols=\"60\" rows=\"10\">".$rGetQuestion->question."</textarea></p>\n\n";
		$content .= "<p class=\"nopad\"><input type=\"submit\" value='".lang("Change question", "wannabeadmin")."' /></p>";
		$content .= "</form>\n\n\n\n";

	} // End if action == editQuestion

	// if action == editAnswer. Display edit-answer-form
	elseif($action == "editAnswers" && !empty($_GET['questionID']))
	{
		// Add a link back to questionlist
		if(empty($_GET['answerID']))
			$content .= "<br /><a href=\"?module=wannabeadmin&amp;action=questions\">".lang("Cancel edit", "wannabeadmin")."</a>\n\n<br />";
		// if in answer-mode: back to that question
		else {
			$content .= "<br /><a href=\"?module=wannabeadmin&amp;action=editAnswers&amp;questionID=".$_GET['questionID']."\">";
			$content .= lang("Cancel edit", "wannabeadmin")."</a>\n\n<br />";
		} // end else

		// Get info about the question
		$qGetQuestion = db_query("SELECT * FROM ".$sql_prefix."_wannabeQuestions
			WHERE ID = '".db_escape($_GET['questionID'])."'");
		$rGetQuestion = db_fetch($qGetQuestion);

		// Get the answers
		$qGetAnswers = db_query("SELECT * FROM ".$sql_prefix."_wannabeQuestionInfo
			WHERE questionID = $rGetQuestion->ID ORDER BY answerOrder ASC, ID ASC");
		while($rGetAnswers = db_fetch($qGetAnswers)) {
			if (!empty($_GET['answerID'])) $answerID = $_GET['answerID'];


			if($rGetAnswers->ID == $answerID) {
				// We are currently editing a answer
				$content .= "<form method=\"post\" action=\"?module=wannabeadmin&amp;action=doChangeAnswer&amp;answerID=$answerID\">\n";
				$content .= "<textarea name=\"response\" cols=\"60\" rows=\"10\">$rGetAnswers->response</textarea>\n";
				$content .= "<br /><input type=\"submit\" value='".lang("Change answer", "wannabeadmin")."' />\n";
				$content .= "</form>\n\n\n\n";
			} // end if rGetAnswers->ID == answerID

			else {
				$content .= "<br /><a href=\"?module=wannabeadmin&amp;action=editAnswers&amp;answerID=$rGetAnswers->ID&amp;questionID=".$_GET['questionID']."\">
				$rGetAnswers->response</a>\n";
			}

		} // end while rGetAnswers

		if($rGetQuestion->questionType == 'select' && empty($_GET['answerID'])) {
			// This is a select-type question. Allow adding answers
			$content .= "<form method=\"post\" action=\"?module=wannabeadmin&amp;action=doAddAnswer&amp;questionID=".$_GET['questionID']."\">";
			$content .= "<p class=\"nopad\"><input type=\"text\" name=\"response\" /></p>";
			$content .= "<p><input type=\"submit\" value='".lang("Add answer", "wannabeadmin")."' /></p>\n";
			$content .= "</form>\n\n\n";
		} // End if rGetQuestion->questionType = select

	} // end if action == editAnswers

} // End if action == questions

elseif($action == "doAddAnswer" && $acl_access == "Admin") {
	$response = $_POST['response'];
	$questionID = $_GET['questionID'];
	$extra = $_POST['extra'];

	db_query("INSERT INTO ".$sql_prefix."_wannabeQuestionInfo SET
		questionID = '".db_escape($questionID)."',
		response = '".db_escape($response)."',
		extra = '".db_escape($extra)."'");

	header("Location: ?module=wannabeadmin&action=editAnswers&questionID=".$questionID);

} // end action == doAddAnswer

elseif($action == "addQuestion" && $acl_access == "Admin")
{
	$question = $_POST['question'];
	$questionType = $_POST['questionType'];

	db_query("INSERT INTO ".$sql_prefix."_wannabeQuestions
		SET eventID = ".db_escape($eventID).",
		question = '".db_escape($question)."',
		questionType = '".db_escape($questionType)."'
		");

	// I've never got mysql_last_row() to work. Doing it manually
	$qFindQuestion = db_query("SELECT * FROM ".$sql_prefix."_wannabeQuestions
		WHERE question LIKE '".db_escape($question)."'
		AND eventID = '".db_escape($eventID)."'
		ORDER BY ID DESC
		LIMIT 0,1");
	$rFindQuestion = db_fetch($qFindQuestion);
	$questionID = $rFindQuestion->ID;

	header("Location: ?module=wannabeadmin&action=editQuestion&questionID=".$questionID);

} // End if action == addQuestion


elseif($action == "changeQuestion" && isset($_GET['questionID']) && $acl_access == 'Admin') {
	$question = $_POST['question'];
	db_query("UPDATE ".$sql_prefix."_wannabeQuestions SET question = '".db_escape($question)."' WHERE ID = '".db_escape($_GET['questionID'])."'");
	header("Location: ?module=wannabeadmin&action=questions");
}

elseif($action == "listApplications") {
	$qListApplications = db_query("SELECT DISTINCT userID FROM ".$sql_prefix."_wannabeResponse res
		JOIN ".$sql_prefix."_wannabeQuestions ques ON res.questionID=ques.ID WHERE ques.eventID = $eventID");
	
	if(mysql_num_rows($qListApplications) != 0) {
	$content .= "<table>";
		while($rListApplications = db_fetch($qListApplications)) {
			$content .= "<tr><td>";
			$content .= "<a href=\"?module=wannabeadmin&action=viewApplication&user=$rListApplications->userID\">";
			$content .= display_username($rListApplications->userID);
			$content .= "</a>";
			$content .= "</td></tr>";
		} // End while rListApplications
		$content .= "</table>";
	}

} // End action=listApplications

elseif($action == "viewApplication" && !empty($_GET['user'])) {
	// Add CSS for this action
	$design_head .= '<link href="templates/shared/wannabe.css" rel="stylesheet" type="text/css">';
	$user = $_GET['user'];

	$content .= "<h2>".lang ("Application from:", "wannabeadmin")." ".display_username ($user)."</h2>";
	$content .= "<a href=\"?module=wannabeadmin&action=listApplications\">".lang ("Back to list", "wannabeadmin")."</a>";

	$content .= "<table>";

	$qListCrewResponses = db_query("SELECT crew.crewname,
		(SELECT response FROM ".$sql_prefix."_wannabeCrewResponse
			WHERE userID = '".db_escape($user)."' AND crewID=crew.ID) AS response
		FROM ".$sql_prefix."_wannabeCrews crew WHERE eventID = $eventID");
	while($rListCrewResponses = db_fetch($qListCrewResponses)) {
		$content .= "<tr><td> <i>Crew:</i> ";
		$content .= $rListCrewResponses->crewname;
		$content .= "</td><td>";
		$content .= lang("WannabeCrewListPreference".$rListCrewResponses->response, "wannabe_crewprefs");
		$content .= "</td></tr>";
	} // End while rListCrewResponses


	$qListResponse = db_query("SELECT ques.question,ques.questionType,res.response FROM ".$sql_prefix."_wannabeResponse res
		JOIN ".$sql_prefix."_wannabeQuestions ques ON res.questionID=ques.ID WHERE res.userID = ".db_escape($user));

	while($rListResponse = db_fetch($qListResponse)) {

		$content .= "<tr><td>";
		$content .= $rListResponse->question;
		$content .= "</td><td>";
		switch ($rListResponse->questionType) {
			case "text":
				$content .= $rListResponse->response;
				break;
			case "checkbox":
				if($rListResponse->response == "on") $content .= lang("Yes", "wannabeadmin");
				else $content .= lang("No", "wannabeadmin");
				break;
			case "select":
				$qGetDropdownAnswer = db_query("SELECT response FROM ".$sql_prefix."_wannabeQuestionInfo WHERE ID = ".$rListResponse->response);
				$rGetDropdownAnswer = db_fetch($qGetDropdownAnswer);
				$content .= $rGetDropdownAnswer->response;
				break;
			default:
				$content .= "WTF in viewApplications->switch->default!";
		} // End switch
		$content .= "</td></tr>";

	} // End while rListResponse

	$content .= "</table>";

	$content .= "<table>\n\n";

	// FIXME: Ugly way of doing this...
	$qListCrewHeaders = db_query("SELECT crewname FROM ".$sql_prefix."_wannabeCrews WHERE eventID = $eventID");
	$content .= "<tr><td></td>";
	while($rListCrewHeaders = db_fetch($qListCrewHeaders)) {
		$content .= "<th>";
		$content .= $rListCrewHeaders->crewname;
		$content .= "</th>";
	} // End while rListCrewHeaders


	// List admins that has said something
	$qListAdmins = db_query("SELECT DISTINCT u.nick,c.adminID FROM ".$sql_prefix."_users u
		JOIN ".$sql_prefix."_wannabeComment c ON u.ID=c.adminID
		WHERE crewID IN (SELECT ID FROM ".$sql_prefix."_wannabeCrews WHERE eventID = $eventID)");

	while($rListAdmins = db_fetch($qListAdmins)) {
		$content .= "<tr><th>";
		$content .= $rListAdmins->nick;
		$content .= "</th>";
		if($rListAdmins->adminID == $sessioninfo->userID) $editcmt = TRUE; // Current user, allow changing comments
		$qListCrews = db_query("SELECT ID FROM ".$sql_prefix."_wannabeCrews WHERE eventID = $eventID");
		while($rListCrews = db_fetch($qListCrews)) {
			$crewID = $rListCrews->ID;

			$qListComments = db_query("SELECT * FROM ".$sql_prefix."_wannabeComment WHERE userID = '".db_escape($user)."'
				AND crewID = $crewID AND adminID = $rListAdmins->adminID");
			$rListComments = db_fetch($qListComments);
			$content .= "<td class=wannabeCommentStyle".$rListComments->approval.">";
			if($editcmt) {
				$content .= "<a href=\"?module=wannabeadmin&action=changeComment&crewID=$crewID&user=$user\">";
				if(empty($rListComments->comment)) $content .= lang("Comment", "wannabeadmin");
				else $content .= $rListComments->comment;
				$content .= "</a>";
			}
			else $content .= $rListComments->comment;
			$content .= "</td>\n";

		} // End while rListCrews

		$content .= "</tr>\n\n";
	} // End while rListAdmins

	if($acl_access != 'Read' && !$editcmt) {
		$qGetUsernick = db_query("SELECT nick FROM ".$sql_prefix."_users WHERE ID = $sessioninfo->userID");
		$rGetUsernick = db_fetch($qGetUsernick);
		$content .= "<tr><th>$rGetUsernick->nick</th>";
		$qListCrewsAppend = db_query("SELECT ID FROM ".$sql_prefix."_wannabeCrews WHERE eventID = $eventID");
		while($rListCrewsAppend = db_fetch($qListCrewsAppend)) {
			$content .= "<td>";
			$content .= "<a href=\"?module=wannabeadmin&action=changeComment&crewID=".$rListCrewsAppend->ID."&user=$user\">";
			$content .= lang("Comment", "wannabeadmin");
			$content .= "</a></td>";
		} // End while rListCrewsAppend
	} // End if acl_access != read

	$content .= "</table>";




} // End elseif action == viewApplications

elseif($action == "crews") {
	$qListCrews = db_query("SELECT * FROM ".$sql_prefix."_wannabeCrews WHERE eventID = $eventID");
	
	if(mysql_num_rows($qListCrews) != 0) {
		$content .= "<table>";
		while($rListCrews = db_fetch($qListCrews)) {
			$content .= "<tr><td>";
			$content .= $rListCrews->crewname;
			$content .= "</td></tr>";
		} // End while (rListCrews)

		$content .= "</table>";
	}
	$content .= "<form method=\"post\" action=\"?module=wannabeadmin&amp;action=doAddCrew\">\n";
	$content .= "<p class=\"nopad\"><input type=\"text\" name=\"crewname\" />\n";
	$content .= "<input type=\"submit\" value='".lang("Add crew", "wannabeadmin")."' /></p>";
	$content .= "</form>";
}

elseif($action == "doAddCrew") {
	$crewname = $_POST['crewname'];
	db_query("INSERT INTO ".$sql_prefix."_wannabeCrews SET crewname = '".db_escape($crewname)."', eventID = $eventID");
	header("Location: ?module=wannabeadmin&action=crews");
} // End action == doAddCrew

elseif($action == "changeComment" && !empty($_GET['crewID']) && !empty($_GET['user'])) {
	$user = $_GET['user'];
	$crewID = $_GET['crewID'];

	$qCheckExisting = db_query("SELECT * FROM ".$sql_prefix."_wannabeComment WHERE adminID = $sessioninfo->userID
		AND userID = '".db_escape($user)."' AND crewID = '".db_escape($crewID)."'");
	$rCheckExisting = db_fetch($qCheckExisting);

	$content .= "<form method=\"post\" action=\"?module=wannabeadmin&amp;action=doChangeComment&amp;user=$user&amp;crewID=$crewID\">\n";
	$content .= "<select name=\"approval\">";
	for($i=0;$i<6;$i++) {
		$content .= "<option value=$i";
		if($i==$rCheckExisting->approval) $content .= " SELECTED";
		$content .= ">".lang("wannabeAdminCmt".$i, "wannabeadmin_prefs")."</option>";
	} // End for
	$content .= "</select>";
	$content .= "<br /><textarea name=\"comment\" rows=\"5\" cols=\"40\">$rCheckExisting->comment</textarea>";
	$content .= "<br /><input type=\"submit\" value='".lang("Save comment", "wannabeadmin")."' />";
	$content .= "</form>";
} // End elseif action==changeComment


elseif($action == "doChangeComment") {
	$comment = $_POST['comment'];
	$approval = $_POST['approval'];
	$user = $_GET['user'];
	$crewID = $_GET['crewID'];

	$qCheckExisting = db_query("SELECT * FROM ".$sql_prefix."_wannabeComment WHERE adminID = $sessioninfo->userID
		AND userID = '".db_escape($user)."' AND crewID = '".db_escape($crewID)."'");
	if(db_num($qCheckExisting) > 0) {
		db_query("UPDATE ".$sql_prefix."_wannabeComment
			SET approval = '".db_escape($approval)."',
			comment = '".db_escape($comment)."'
			WHERE adminID = $sessioninfo->userID
			AND userID = '".db_escape($user)."'
			AND crewID = '".db_escape($crewID)."'");
	} // End if db_num exists
	else {
		db_query(" INSERT INTO ".$sql_prefix."_wannabeComment
			SET approval = '".db_escape($approval)."',
			comment = '".db_escape($comment)."',
			adminID = $sessioninfo->userID ,
			userID = '".db_escape($user)."',
			crewID = '".db_escape($crewID)."'");
	} // End else

	header("Location: ?module=wannabeadmin&action=viewApplication&user=$user");
} // End elseif action == doChangeComment
