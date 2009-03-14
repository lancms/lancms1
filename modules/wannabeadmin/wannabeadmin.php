<?php

$eventID = $sessioninfo->eventID;
$action = $_GET['action'];
$acl_access = acl_access("wannabeadmin", "", $eventID);




if($action == "adminWannabe")
{
	/* Adminlist for wannabe-actions */

	if($acl_access == "Admin")
	{
		// User has wannabe adminrights
		$content .= "<br><a href=?module=wannabeadmin&amp;action=questions>".lang("Questions", "wannabeadmin")."</a>\n";

	} // End acl_access = Admin

	if($acl_access == 'Write' || $acl_access == 'Admin')
	{
		// User has wannabe write-access (may see and write comments)

	} // End acl_access > Write


} // End if action == "adminWannabe"


elseif(($action == "questions" || $action == "editQuestion" || $action == "editAnswers") && $acl_access == "Admin")
{
	/* Admin questions */

	// First. List up all the questions that exists
	$qListQuestions = db_query("SELECT * FROM ".$sql_prefix."_wannabeQuestions
		WHERE eventID = '".db_escape($eventID)."' ORDER BY questionOrder ASC, ID ASC");

	$content .= '<table>';

	while($rListQuestions = db_fetch($qListQuestions))
	{
		$content .= "<tr><td>\n";
		$content .= "<a href=?module=wannabeadmin&amp;action=editQuestion&amp;questionID=$rListQuestions->ID>";
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
			$content .= "<a href=?module=wannabeadmin&amp;action=editAnswers&amp;questionID=$rListQuestions->ID>";
			$content .= $rListQuestions->questionType;
			$content .= "</a>\n";
		} // End else (questiontype can have answers)
		$content .= "</td></tr>\n\n";
	} // End while rListQuestions

	$content .= "</table>\n\n\n\n";

	// If action == questions, we are not editing any questions.
	// Displaying add new question-form
	if($action == "questions")
	{
		$content .= "<form method=POST action=?module=wannabeadmin&amp;action=addQuestion>\n";
		$content .= "<textarea name=question rows=10 cols=60>".lang("New question", "wannabeadmin")."</textarea>\n";
		$content .= "<br><select name=questionType>\n";
		/* List up possible answer-types */
		$content .= "<option value=text>".lang("Text answer-field", "wannabeadmin")."</option>\n";
		$content .= "<option value=select>".lang("Dropdown", "wannabeadmin")."</option>\n";
		$content .= "<option value=checkbox>".lang("Checkbox", "wannabeadmin")."</option>\n";
		/* End listing answer-types */
		$content .= "</select>\n\n";
		$content .= "<br><input type=submit value='".lang("Add new question", "wannabeadmin")."'>";
	} // End if action == questions

	// if action == editQuestion. Display edit-question-form
	elseif($action == "editQuestion" && isset($_GET['questionID']))
	{
		// Add a link back to questionlist
		$content .= "<br><a href=?module=wannabeadmin&amp;action=questions>".lang("Cancel edit", "wannabeadmin")."</a>\n\n";

		// Get info about the question
		$qGetQuestion = db_query("SELECT * FROM ".$sql_prefix."_wannabeQuestions
			WHERE ID = '".db_escape($_GET['questionID'])."'");
		$rGetQuestion = db_fetch($qGetQuestion);


		$content .= "<br><form method=POST action=?module=wannabeadmin&amp;action=changeQuestion&amp;questionID=$rGetQuestion->ID>\n";
		$content .= "<textarea name=question cols=60 rows=10>".$rGetQuestion->question."</textarea>\n\n";
		$content .= "<br><input type=submit value='".lang("Change question", "wannabeadmin")."'>";
		$content .= "</form>\n\n\n\n";

	} // End if action == editQuestion

	// if action == editAnswer. Display edit-answer-form
	elseif($action == "editAnswers" && !empty($_GET['questionID']))
	{
		// Add a link back to questionlist
		if(empty($_GET['answerID']))
			$content .= "<br><a href=?module=wannabeadmin&amp;action=questions>".lang("Cancel edit", "wannabeadmin")."</a>\n\n<br>";
		// if in answer-mode: back to that question
		else {
			$content .= "<br><a href=?module=wannabeadmin&amp;action=editAnswers&amp;questionID=".$_GET['questionID'].">";
			$content .= lang("Cancel edit", "wannabeadmin")."</a>\n\n<br>";
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
				$content .= "<form method=POST action=?module=wannabeadmin&amp;action=doChangeAnswer&amp;answerID=$answerID>\n";
				$content .= "<textarea name=response cols=60 rows=10>$rGetAnswers->response</textarea>\n";
				$content .= "<br><input type=submit value='".lang("Change answer", "wannabeadmin")."'>\n";
				$content .= "</form>\n\n\n\n";
			} // end if rGetAnswers->ID == answerID

			else {
				$content .= "<br><a href=?module=wannabeadmin&amp;action=editAnswers&amp;answerID=$rGetAnswers->ID&amp;questionID=".$_GET['questionID'].">
				$rGetAnswers->response</a>\n";
			}

		} // end while rGetAnswers

		if($rGetQuestion->questionType == 'select' && empty($_GET['answerID'])) {
			// This is a select-type question. Allow adding answers
			$content .= "<form method=POST action=?module=wannabeadmin&amp;action=doAddAnswer&amp;questionID=".$_GET['questionID'].">";
			$content .= "<input type=text name=response>";
			$content .= "<br><input type=submit value='".lang("Add answer", "wannabeadmin")."'>\n";
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
