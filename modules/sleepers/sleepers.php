<?php

// TODO: Allow toggling wakeup-service (js) (default: false)

$usertable = $sql_prefix."_users";
$ticketstable = $sql_prefix."_tickets";
$sleeperstable = $sql_prefix."_sleepers";

if (!config ('enable_sleepers', $sessioninfo->eventID))
{
	die (_('Module not activated'));
}

require __DIR__ . "/WakeupManager.php";
$wakeupManager = new WakeupManager($sessioninfo->eventID, $sleeperstable);

$acl_access = acl_access ('sleepers', "", $sessioninfo->eventID);
if ($acl_access != 'Admin' and $acl_access != 'Write')
{
	die (_('No access'));
}

$content .= "<h2>"._('Sleepers')."</h2>\n";

if ($action == 'addsleeper')
{
	$userid = $_GET['userid'];

	if (empty ($userid) or !is_numeric ($userid))
	{
		header ('Location: ?module=sleepers');
		die ();
	}

	if (!user_exists ($userid) || is_user_sleeping($userid) == true) {
		header ('Location: ?module=sleepers');
		die ();
	}

	$q = sprintf ('INSERT INTO %s (eventID, userID) VALUES (%s, %s)', $sleeperstable, $sessioninfo->eventID, db_escape($userid));
	db_query ($q);
	$log['userID'] = $userid;
	log_add ("sleepers", "addsleeper", serialize($log));

	header ('Location: ?module=sleepers');
	die ();
}
elseif ($action == 'removesleeper')
{
	$userid = $_GET['userid'];

	if (empty ($userid) or !is_numeric ($userid))
	{
		header ('Location: ?module=sleepers');
		die ();
	}

	if (!user_exists ($userid))
	{
		header ('Location: ?module=sleepers');
		die ();
	}
	$q = sprintf ('DELETE FROM %s WHERE eventID=%s AND userID=%s', $sleeperstable, $sessioninfo->eventID, db_escape($userid));
	db_query ($q);
	$log['userID'] = $userid;
	log_add ("sleepers", "removesleeper", serialize($log));

	header ('Location: ?module=sleepers');
	die ();
}
elseif ($action == 'rmWake')
{
	// Remove wake time on sleeper.
	$userID = $_GET['userID'];

	if (isset($userID) == false || is_numeric($userID) == false) {
		header ('Location: ?module=sleepers');
		die ();
	}

	$log = array('userID' => $userID);
	log_add ("sleepers", "removewakeup", serialize($log));

	$wakeupManager->removeWakeup($userID);
	header ('Location: ?module=sleepers&wakerm=' . $userID);
	die ();
}
elseif ($action == 'setwakegui')
{
	// Set wake time on sleeper, GUI.
	$userID = $_GET['userID'];

	if (isset($userID) == false || is_numeric($userID) == false) {
		header ('Location: ?module=sleepers');
		die ();
	}

	$content .= "<a href='?module=sleepers'>"._("Return to sleepers overview")."</a>\n";
	ob_start();
	include __DIR__ . "/setwake_gui.php";
	$content .= ob_get_clean();
}
elseif ($action == 'setWake')
{
	// Set wake time on sleeper.
	$userID = $_GET['userID'];
	$params = array();
	$check = array("day", "month", "year", "hours", "minutes", "seconds");

	if ((isset($userID) == false || is_numeric($userID) == false)) {
		header ('Location: ?module=sleepers');
		die();
	}

	foreach ($check as $key=>$param) {
		if (array_key_exists($param, $_POST) == false || is_numeric($_POST[$param]) == false) {
			header ('Location: ?module=sleepers&action=setwakegui&userID=' . $userID);
			die();
		}

		$params[$param] = intval($_POST[$param]);
	}

	$timestamp = mktime($params['hours'], $params['minutes'], $params['seconds'], $params['month'], $params['day'], $params['year']);

	if (time() >= $timestamp) {
		header ('Location: ?module=sleepers&action=setwakegui&userID=' . $userID . '&invalidtime');
		die();
	}

	$wakeupManager->setWakeup($userID, $timestamp);
	header ('Location: ?module=sleepers&wakeset=' . $userID);
	die();
}
elseif ($action == 'searchsleeper')
{

	$str = $_REQUEST['searchstring'];
	$scope = $_POST['scope'];

	$content .= "<a href='?module=sleepers'>"._("Return to sleepers overview")."</a>\n";

	if (empty ($str) or $str == "")
	{
		$content .= "<p>"._("You must enter a search string")."</p>\n";
	}
	else
	{
		if (is_numeric ($str))
		{
			$usersQ = sprintf ('SELECT nick, firstName, lastName, ID FROM %s WHERE ID=%s', $usertable, $str);
		}
		elseif ($scope == 'tickets')
		{
			$str = db_escape ($str);
			$content .= "<p>"._("Searching only users with tickets for this event")."</p>";

			$usersQ = sprintf ("SELECT DISTINCT u.nick as nick, u.firstName as firstName, u.lastName as lastName, u.ID as ID FROM %s as u, %s as t WHERE t.eventID=%s AND t.user=u.ID AND
			(u.nick LIKE '%%%s%%' OR
			u.firstName LIKE '%%%s%%' OR
			u.lastName LIKE '%%%s%%' OR
			CONCAT(u.firstName, ' ', u.lastName) LIKE '%%%s%%' OR
			EMail LIKE '%%%s%%'
			) ORDER BY u.ID
			", $usertable, $ticketstable, $sessioninfo->eventID, $str, $str, $str, $str, $str);
		}
		else
		{
			$str = db_escape ($str);
			$content .= "<p>"._("Searching all users")."</p>";

			$usersQ = sprintf ("SELECT nick, firstName, lastName, ID FROM %s WHERE ID > 1 AND
				(nick LIKE '%%%s%%' OR
				firstName LIKE '%%%s%%' OR
				lastName LIKE '%%%s%%' OR
				CONCAT(firstName, ' ', lastName) LIKE '%%%s%%' OR
				EMail LIKE '%%%s%%') ORDER BY ID
				", $usertable, $str, $str, $str, $str, $str);
		}
			$usersR = db_query ($usersQ);
			$usersC = db_num ($usersR);
			if ($usersC)
			{
				$border = 'style="border: solid 1px black; border-collapse: collapse;"';
				$content .= "<table $border'>\n";
				$content .= sprintf ("<tr><th>%s</th><th>%s</th><th>%s</th><th>%s</th><th></th></tr>\n", _('Nick'), _('Name'), _('Tickets'), _('Status'));
				while ($user = db_fetch ($usersR))
				{

					$sleepQ = sprintf ('SELECT * FROM %s WHERE userID=%s AND eventID=%s', $sleeperstable, $user->ID, $sessioninfo->eventID);
					$sleepR = db_query ($sleepQ);
					$sleepC = db_num ($sleepR);
					if ($sleepC)
					{
						$sleep = db_fetch($sleepR);

						$status = _("Sleeping since");
						$status .= " ".$sleep->sleepTimestamp;
						$sleeping = 1;
					}
					else
					{
						$status = _("Not sleeping");
						$sleeping = 0;
					}

					$ticketsQ = sprintf ('SELECT ticket.paid, ticket.status, type.name FROM %s AS ticket, %s AS type WHERE ticket.user=%s AND ticket.eventID=%s and ticket.status!="deleted" and ticket.ticketType=type.ticketTypeID', $ticketstable, $sql_prefix."_ticketTypes", $user->ID, $sessioninfo->eventID);
					$ticketsR = db_query ($ticketsQ);
					$ticketsC = db_num ($ticketsR);
					if ($ticketsC)
					{
						while ($ticket = db_fetch ($ticketsR))
						{
							unset ($ticketcolor);
							if ($tickets)
							{
								$tickets .= "<br />\n";
							}
							if ($ticket->paid == 'yes')
							{
								$ticketcolor = 'green';
							}
							else
							{
								$ticketcolor = 'orange';
							}
							$tickets .= "<span style='background-color: $ticketcolor'>".$ticket->name."</span>";
						}
					}
					else
					{
						$tickets = "<p><span class=\"ticket-error\">"._("No tickets for this event")."</span>";
						if (is_user_crew($user->ID) == true) {
							$tickets .= "<br /><span class=\"ticket-warn\">"._("This user is a crew-member.")."</span>";
						}
						$tickets .= "</p>";
					}


					$content .= sprintf ("<td %s>%s</td>", $border, $user->nick);
					$content .= sprintf ("<td %s>%s</td>", $border, $user->firstName." ".$user->lastName);
					$content .= sprintf ("<td %s>%s</td>", $border, $tickets);
					$content .= sprintf ("<td %s>%s</td>", $border, $status);

					$content .= sprintf ("<td %s>", $border);
					if ($sleeping)
					{
						$content .= sprintf ("<form action='?module=sleepers&action=removesleeper&userid=%s' method='POST'>", $user->ID);
						$content .= sprintf ("<input type='submit' class='btn' value='%s' />", _('Remove sleeper'));
						$content .= "</form>";
					}
					else
					{
						$content .= sprintf ("<form action='?module=sleepers&action=addsleeper&userid=%s' method='POST'>", $user->ID);
						$content .= sprintf ("<input type='submit' class='btn' value='%s' />", _('Add sleeper'));
						$content .= "</form>";
					}
					$content .= sprintf ("</td>");

					$content .= "</tr>\n";
					unset ($status);
					unset ($tickets);
				}
				$content .= "</table>\n";
			}
			else
			{
				$content .= "<p><b>"._('Found no matching users')."</b></p>";
			}
	}

}
else // empty($action) or $action==*
{

	$content .= "<script type=\"text/javascript\" src=\"templates/ontime/js/wakeup.js\"></script>\n";
	$content .= "<form action='?module=sleepers&action=searchsleeper' method='POST'>\n";
	$content .= _('Search for user:')."<br />\n";
	$content .= "<input type='text' name='searchstring' />\n";
	$content .= "<input type='submit' class='btn' value='"._('Search')."' />\n";
	$content .= "<br />\n";
	$content .= _("Search users with tickets for this event only:")." <input type='checkbox' CHECKED name='scope' value='tickets' />\n";
	$content .= "</form>\n";

	$sleeperQ = sprintf ('SELECT s.wakeupTimestamp, s.sleepTimestamp, u.ID, u.nick, CONCAT(u.firstName, " ",u.lastName) AS name FROM %s AS s, %s AS u WHERE s.eventID=%s AND s.userID=u.ID ORDER BY s.sleepTimestamp', $sql_prefix."_sleepers", $sql_prefix."_users", $sessioninfo->eventID);
	$sleeperR = db_query ($sleeperQ);
	$sleeperC = db_num ($sleeperR);

	$sleepersArrayJs = array();

	if ($sleeperC)
	{
		$border = 'style="border: solid 1px black; border-collapse: collapse;"';
		$content .= "<br /><p>"._('Number of sleeping users:')." ".$sleeperC."</p>\n";

		$content .= "<table $border>\n";
		$content .= sprintf ("<tr><th>%s</th><th>%s</th><th>%s</th><th>%s</th><th></th></tr>\n", _("Nick"), _("Name"), _("Went to bed"), _("Wakeup"));

		while ($sleeper = db_fetch ($sleeperR))
		{
			$wakeupManager->filterSleeper($sleeper);

			$userID = $sleeper->ID;
			$username = $sleeper->nick;
			$name = $sleeper->name;
			$wakeupTime = $wakeupManager->getWakeupTime($userID);
			$wakeupText = _("No wakeup") . ", <a href=\"?module=sleepers&amp;action=setwakegui&amp;userID=$userID\">" . mb_strtolower(_("Set wakeup")) . "</a>";
			if ($wakeupTime > 0) {
				$wakeupText = _("Loading");
			}

			$content .= "<tr>\n";
			$content .= sprintf ("<td %s>%s</td>\n", $border, $username);
			$content .= sprintf ("<td %s>%s</td>\n", $border, $name);
			$content .= sprintf ("<td %s>%s</td>\n", $border, $sleeper->sleepTimestamp);
			$content .= sprintf ("<td id=\"wakeup-" . $sleeper->ID . "\" %s>%s</td>\n", $border, $wakeupText);
			$content .= sprintf ("<td %s><form method='POST' action='?module=sleepers&action=searchsleeper&searchstring=%s'><input type='submit' class='btn' value='%s'></form></td>\n", $border, $sleeper->ID, _("View"));
			$content .= "</tr>\n";

			// Add javascript for user if has wakeup.
			if ($wakeupTime > 0) {
				$sleepersArrayJs[] = "new Sleeper($userID, '$name', '$username', $wakeupTime)";
			}
		}

		$content .= "</table>\n";
		$content .= "<script type=\"text/javascript\">
			window.onload = function() {
				var sleepers = [" . implode(", ", $sleepersArrayJs) . "];
				wakeupHandler.addLangString(0, '" . _('Day') . "');
				wakeupHandler.addLangString(1, '" . _('Hour') . "');
				wakeupHandler.addLangString(2, '" . _('Min') . "');
				wakeupHandler.addLangString(3, '" . _('Sec') . "');
				wakeupHandler.addLangString(4, '" . _('Days') . "');
				wakeupHandler.addLangString(5, '" . _('Hours') . "');
				wakeupHandler.addLangString(6, '" . _('Mins') . "');
				wakeupHandler.addLangString(7, '" . _('Secs') . "');
				wakeupHandler.addLangString(8, '" . _('%NAME% (%USERNAME%) shall be waken now!') . "');
				wakeupHandler.addLangString(9, '" . _('Waking alert!') . "');
				wakeupHandler.addLangString(10, '" . _('Wake me!') . "');
				wakeupHandler.addLangString(11, '" . _('Remove') . "');

				// Everything start here, so add options berfore calling this.
				wakeupHandler.setSleepers(sleepers);
			};
		</script>";
	}
	else
	{
		$content .= "<br /><p><i>"._('There are no sleeping users')."</i></p>\n";
	}

	$content .= "";
}
