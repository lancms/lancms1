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
		WHERE eventID = '".db_escape($eventID)."'");
	
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
	elseif($action == "editAnswers" && isset($_GET['questionID']))
	{
		// Add a link back to questionlist
		$content .= "<br><a href=?module=wannabeadmin&amp;action=questions>".lang("Cancel edit", "wannabeadmin")."</a>\n\n";
		
		// Get info about the question
		$qGetQuestion = db_query("SELECT * FROM ".$sql_prefix."_wannabeQuestions 
			WHERE ID = '".db_escape($_GET['questionID'])."'");
		$rGetQuestion = db_fetch($qGetQuestion);
		
		
	} // end if action == editAnswers

} // End if action == questions


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