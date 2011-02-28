<?php
$acl = acl_access("infoscreen", "", $sessioninfo->eventID);

if($acl == 'No' || $acl == 'Read') die("No access to infoscreens");

$action = $_GET['action'];

$slidetable = $sql_prefix."_infoscreensSlides";
$queuetable = $sql_prefix."_infoscreensQueues";
$screentable = $sql_prefix."_infoscreens";

if (empty($action))
{
	$content .= "<h2>"._("Infoscreens")."</h2>\n";
	$content .= "<p>"._('Remember that all slides must be considered public!')."</p>\n";

	#### START - screens ####
	$content .= "<div style='border: solid 1px black; border-collapse: collapse; padding: 10px;'>\n";
	$content .= "<h3>"._('Screens')."</h3>";

	$qFindScreens = db_query("SELECT * FROM ".$sql_prefix."_infoscreens WHERE eventID = '$sessioninfo->eventID'");
	$content .= "<table style='border: solid 1px black; border-collapse: collapse;'>";
	while($rFindScreens = db_fetch($qFindScreens)) {
		$content .= "<tr><td style='border: solid 1px black; border-collapse: collapse;'>";
		$content .= $rFindScreens->name;
		$content .= "</td></tr>\n\n";
		# FIXME: would be nice to get to delete screens :-)
	}
	$content .= "</table>";


	if($acl == 'Admin') {
		$content .= "<form method=POST action='?module=infoscreens&action=addScreen'>\n";
		$content .= "<input type=text name='name'>"._("Screen name");
		$content .= "<br /><input type=submit value='"._("Add screen")."'>";
		$content .= "</form>\n";
	}

	$content .= "</div>\n";
	#### END - screens ####

	#### START - slides ####
	$content .= "<br /><div style='border: solid 1px black; border-collapse: collapse; padding: 10px;'>\n";
	$content .= "<h3>"._('Slides')."</h3>\n";

	if ($acl == 'Write' or $acl == 'Admin')
	{
		$content .= "<a href='?module=infoscreens&action=newSlide'>"._('Create new slide')."</a>";
	}

	$slideQ = sprintf ('SELECT * FROM %s WHERE eventID=%s', $slidetable, $sessioninfo->eventID);
	$slideR = db_query ($slideQ);
	$slideC = db_num ($slideR);
	if ($slideC)
	{
		$border = 'style="border: solid 1px black; border-collapse: collapse;"';
		$content .= "<table $border>\n";

		$content .= sprintf ("<tr><th>%s</th><th>%s</th><th>%s</th></tr>\n", _('Name'), _('Edit'), _('Preview'));

		while ($slide = db_fetch ($slideR))
		{
			$slide_edit = sprintf ("<form method='POST' action='?module=infoscreens&action=editSlide&slideID=%s'><input type='submit' value='%s' /></form>", $slide->ID, _('Edit'));
			$slide_preview = sprintf ("<form method='POST' action='party.php?slide=%s'><input type='submit' value='%s' /></form>", $slide->ID, _('Preview'));

			$content .= sprintf ("<tr><td>%s</td><td>%s</td><td>%s</td></tr>\n", $slide->name, $slide_edit, $slide_preview);
			unset ($slide_preview);
			unset ($slide_edit);
		}

		$content .= "</table>\n";
	}
	else
	{
		$content .= "<p><i>"._('There are no slides for this event')."</i></p>";
	}
	
	$content .= "</div>";
	#### END - slides
	
	#### START - queues ####
	$content .= "<br /><div style='border: solid 1px black; border-collapse: collapse; padding: 10px;'>\n";
	$content .= "<h3>"._('Queues')."</h3>\n";

	$screenQ = sprintf ("SELECT * FROM %s WHERE eventID=%s", $screentable, $sessioninfo->eventID);
	$screenR = db_query ($screenQ);
	$screenC = db_num ($screenR);
	if ($screenC)
	{
		while ($screen = db_fetch ($screenR))
		{
			$content .= "<h4><i>"._('Screen:')."</i> ".$screen->name."</h4>\n";

			$queueQ = sprintf ('SELECT q.ID, s.name as slide, q.wait FROM %s AS q, %s AS s WHERE q.eventID=%s AND q.slideID=s.ID AND q.screenID=%s ORDER BY ID', $queuetable, $slidetable, $sessioninfo->eventID, $screen->ID);
			$queueR = db_query ($queueQ);
			$queueC = db_num ($queueR);
			if ($queueC)
			{
				$content .= "<table $border>\n";
				$content .= sprintf ("<tr><th>%s</th><th>%s</th><th>%s</th></tr>\n", _('Slide'), _('Wait'), _('Remove'));

				while ($queue = db_fetch ($queueR))
				{
					$rmbut = sprintf ("<form method='POST' action='?module=infoscreens&action=queueRemove&queueID=%s'><input type='submit' value='%s' /></form>", $queue->ID, _('Remove'));
					$content .= sprintf ("<tr><td $border>%s</td><td $border>%s</td><td $border>%s</td></tr>\n", $queue->slide, $queue->wait, $rmbut);
					unset ($rmbut);
				}
				$content .= "</table>\n";

			}
			
			
			$slideQ = sprintf ('SELECT * FROM %s WHERE eventID=%s', $slidetable, $sessioninfo->eventID);
			$slideR = db_query ($slideQ);
			$slideC = db_num ($slideR);

			if ($slideC)
			{
				$content .= "<form method='POST' action='?module=infoscreens&action=queueAdd'>\n";
				$content .= "<input type='hidden' name='screenID' value='".$screen->ID."' />\n";

				$content .= "<select name='slideID'>\n";
				while ($slide = db_fetch($slideR))
				{
					$content .= sprintf ("<option value='%s'>%s</option>\n", $slide->ID, $slide->name);
				}
				$content .= "</select>\n";
				$content .= "<input type='text' name='wait' value='60' style='width: 20px;' />\n";
				$content .= "<input type='submit' value='"._('Add')."' />\n";
				$content .= "</form>\n";
			}
		}
	}

	$content .= "</div>";
	#### END - queues ####
}

elseif (($action == 'newSlide' or $action=='editSlide') and ($acl == 'Write' or $acl == 'Admin'))
{
	if ($action == 'newSlide')
	{
		$ae = false;
		$an = true;
	}
	else
	{
		$ae = true;
		$an = false;
	}

	if ($ae)
	{
		$slideID = $_REQUEST['slideID'];
		if (!is_numeric($slideID) or empty ($slideID))
		{
			header ('Location: ?module=infoscreens');
			die();
		}
	}

	if ($an)
	{
		$content .= "<h3>"._('Create a new slide')."</h3>\n";
	}
	elseif ($ae)
	{
		$content .= "<h3>"._('Edit slide')."</h3>\n";
	}

	if ($ae)
	{
		$slideQ = sprintf ('SELECT * FROM %s WHERE ID=%s', $slidetable, $slideID);
		$slideR = db_query ($slideQ);
		$slideC = db_num ($slideR);
		if ($slideC)
		{
			$slide = db_fetch ($slideR);
		}
		else
		{
			header ('Location: ?module=infoscreens');
			die();
		}
	}

	$content .= "<form method='POST' action='?module=infoscreens&action=saveSlide'>\n";
	if ($ae)
	{
		$content .= "<input type='hidden' name='slideID' value='".$slideID."' />\n";
	}
	$content .= _('Name:')." <input type='text' name='name' value='".stripslashes($slide->name)."' />\n";
	$content .= "<br /><textarea class='mceEditor' rows=25 cols=60 name='content'>".stripslashes($slide->content)."</textarea>\n";
	$content .= "<br /><input type=submit value='"._('Save')."'>\n";
	$content .= "</form>\n";

} //end action==newSlide

elseif ($action == 'saveSlide' and ($acl == 'Admin' or $acl == 'Write'))
{
	$slideID = $_REQUEST['slideID'];
	$content = $_POST['content'];
	$name = $_POST['name'];
	
	if (empty ($content) or empty ($name))
	{
		$content .= "<p>"._('You forgot either name or some content.. Go back and try again.')."</p>\n";
	}
	elseif (empty($slideID))
	{
		// this is new slide...
		$q = sprintf ('INSERT INTO %s (name, content, eventID) VALUES ("%s", "%s", %s)', $slidetable, db_escape($name), db_escape($content), $sessioninfo->eventID);
		db_query ($q);
		$log['ID'] = mysql_insert_id ();
		$log['name'] = $name;
		$log['content'] = $content;
		$log['eventID'] = $sessioninfo->eventID;
		log_add ("infoscreens", "newSlide", serialize ($log));
		unset ($q);
		# FIXME: logging of slide-actions

		header ('Location: ?module=infoscreens');
		die();
	}
	else
	{
		$q = sprintf ("UPDATE %s SET name='%s', content='%s' WHERE ID=%s", $slidetable, db_escape($name), db_escape($content), $slideID);
		db_query ($q);
		unset ($q);
		header ('Location: ?module=infoscreens');
		die();
	}

} // end action == saveSlide

elseif ($action == 'queueAdd' and ($acl == 'Admin' or $acl == 'Write'))
{
	$slideID = $_REQUEST['slideID'];
	$screenID = $_REQUEST['screenID'];
	$wait = $_REQUEST['wait'];

	if (!is_numeric($wait) or empty ($wait))
	{
		$wait = 60;
	}
	if (empty ($slideID) or !is_numeric ($slideID) or empty ($screenID) or !is_numeric ($screenID))
	{
		$content .= "<p>"._('You did something wrong. Go back and try again.')."</p>";
	}
	else
	{
		$q = sprintf ('INSERT INTO %s (slideID, eventID,  screenID, wait) VALUES (%s, %s, %s, %s)', $queuetable, db_escape($slideID), $sessioninfo->eventID, db_escape ($screenID), db_escape($wait));
		db_query ($q);

		header ('Location: ?module=infoscreens');
		die();
	}
} // end action == 'queueAdd'

elseif ($action == 'queueRemove' and ($acl == 'Admin' or $acl == 'Write'))
{
	$queueID = $_REQUEST['queueID'];
	if (empty ($queueID) or !is_numeric($queueID))
	{
		$content .= "<p>"._('You did something wrong. Go back and try again.')."</p>";
	}
	else
	{
		# FIXME: infoscreensQueues... should do some testing on the separation of different events and their acls... not sure this is 100% safe...
		$q = sprintf ('DELETE FROM %s WHERE ID=%s AND eventID=%s', $queuetable, db_escape($queueID), $sessioninfo->eventID);
		db_query ($q);
		header ('Location: ?module=infoscreens');
		die ();

	}
} // end action == 'queueRemove'

elseif ($action == "addScreen" && $acl == 'Admin')
{
	$name = $_POST['name'];

	db_query("INSERT INTO ".$sql_prefix."_infoscreens SET name = '".db_escape($name)."', eventID = '$sessioninfo->eventID'");
	
	$log_new['name'] = $name;
	log_add("infoscreens", "addScreen", serialize($log_new));

	header("Location: ?module=infoscreens");


} // End elseif action == addScreen	
