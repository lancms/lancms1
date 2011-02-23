<?php

$eventID = $sessioninfo->eventID;
$acl_access = acl_access("FAQ", "", $eventID);
$action = $_GET['action'];
$faqID = $_GET['faqID'];

#if($acl_access == "No")
#	die("You do not have access to this!");
	

if($action == "read")
{
	// Read FAQs for current event.
	$qFAQs = db_query("SELECT * FROM ".$sql_prefix."_FAQ 
		WHERE eventID = '".db_escape($eventID)."'");
	
	while($rFAQs = db_fetch($qFAQs))
	{
		$content .= "<br /><a name=#".$rFAQs->ID." href=?module=FAQ&action=read&faqID=$rFAQs->ID>";
		$content .= $rFAQs->question;
		$content .= "</a>\n\n";
		
		if($faqID == $rFAQs->ID)
		{
			// The user has requested to view the current FAQ-ID
			$content .= "<br /><br />$rFAQs->answer";
		 } // End if $faqID == $rFAQs->ID
		 
		 
	} // End while $rFAQs = db_fetch()
	
	
} // End if action == read


elseif($action == "adminFAQs")
{
	// Do ACL-check if you have rights to do this
	if($acl_access != 'Admin')
		die("You have to have admin-rights to administer FAQs");
	
	$qFAQs = db_query("SELECT * FROM ".$sql_prefix."_FAQ
		WHERE eventID = '".db_escape($eventID)."'");
	
	if(mysql_num_rows($qFAQs) != 0) {
		$content .= '<table>';
		while($rFAQs = db_fetch($qFAQs))
		{
			// List FAQs
			$content .= "<tr><td><a name=#".$rFAQs->ID."></a>$rFAQs->question</td>\n";
			$content .= "<td><a href=\"?module=FAQ&amp;action=editFAQ&amp;faqID=$rFAQs->ID\">\n";
			$content .= lang("Edit FAQ", "FAQ");
			$content .= "</td><td>\n";
			$content .= "<a href=\"?module=FAQ&amp;action=deleteFAQ&amp;faqID=$rFAQs->ID\">\n";
			$content .= lang("Delete FAQ", "FAQ");
			$content .= "</td></tr>\n";
		} // End while
		$content .= '</table>';
	}
	
	$content .= "<form method=\"post\" action=\"?module=FAQ&amp;action=addFAQ\">\n";
	$content .= "<p class=\"nopad\"><textarea rows=\"2\" cols=\"50\" name=\"question\">".lang("Question", "FAQ")."</textarea></p>\n";
	$content .= "<p class=\"nopad\"><input type=\"submit\" value='".lang("Add question", "FAQ")."' /></p>\n";
	$content .= "</form>\n";

} // End elseif $action = adminFAQs


elseif($action == "editFAQ" && !empty($faqID))
{
	// Do ACL-check if you have rights to do this
	if($acl_access != 'Admin')
		die("You have to have admin-rights to administer FAQs");
	
	// Edit FAQ-item
	$qFAQ = db_query("SELECT * FROM ".$sql_prefix."_FAQ WHERE ID = '".db_escape($faqID)."'");
	$rFAQ = db_fetch($qFAQ);
	
	$content .= '<form method=POST action=?module=FAQ&action=doEditFAQ&faqID='.$rFAQ->ID.'>';
	$content .= '<textarea name=question rows=2 cols=50>'.$rFAQ->question.'</textarea>';
	$content .= '<br /><textarea name=answer rows=10 cols=50>'.$rFAQ->answer.'</textarea>';
	$content .= '<br /><input type=submit value='.lang("Save", "FAQ").'>';
} // End elseif($action == editFAQ)


elseif($action == "doEditFAQ" && !empty($faqID))
{
	// Do ACL-check if you have rights to do this
	if($acl_access != 'Admin')
		die("You have to have admin-rights to administer FAQs");
	
	$question = $_POST['question'];
	$answer = $_POST['answer'];
	
	db_query("UPDATE ".$sql_prefix."_FAQ SET
		answer = '".db_escape($answer)."',
		question = '".db_escape($question)."'
		WHERE ID = '".db_escape($faqID)."'
		AND eventID = '".db_escape($eventID)."'");
	$log_new['faqID'] = $faqID;
	$log_new['question'] = $question;
	$log_new['answer'] = $answer;
	log_add("FAQ", "doEditFAQ", serialize($log_new));
	header("Location: ?module=FAQ&action=adminFAQs");
} // End if action == doEditFAQ

elseif($action == "addFAQ")
{
	// Do ACL-check if you have rights to do this
	if($acl_access != 'Admin')
		die("You have to have admin-rights to administer FAQs");
	
	// Add a new FAQ-items
	$question = $_POST['question'];
	
	db_query("INSERT INTO ".$sql_prefix."_FAQ SET
		question = '".db_escape($question)."',
		eventID = '".db_escape($eventID)."'");
	
	// Find out what the hell we just did
	$qLastID = db_query("SELECT ID FROM ".$sql_prefix."_FAQ WHERE 
		eventID = ".db_escape($eventID)."
		AND question = '".db_escape($question)."'
		ORDER BY ID DESC LIMIT 0,1");
	$rLastID = db_fetch($qLastID);
	$log_new['faqID'] = $rLastID->ID;
	$log_new['question'] = $question;
	log_add("FAQ", "addFAQ", serialize($log_new));

	// Jump to edit-mode for this FAQ
	header("Location: ?module=FAQ&action=editFAQ&faqID=$rLastID->ID");
} // End if action == addFAQ)

elseif($action == "deleteFAQ" && !empty($faqID))
{
	/* Delete FAQ */
	// Do ACL-check if you have rights to do this
	if($acl_access != 'Admin')
		die("You have to have admin-rights to administer FAQs");
	
	db_query("DELETE FROM ".$sql_prefix."_FAQ
		WHERE ID = ".db_escape($faqID)."
		AND eventID = ".db_escape($eventID));
	log_add("FAQ", "deleteFAQ", serialize($faqID));
	header("Location: ?module=FAQ&action=adminFAQs");
} // End action == deleteFAQ
	
