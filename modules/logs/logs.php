<?php

// FIXME: using nonexistant acl "logview" to make this globaladmin-only until we get global acls working
if (acl_access ("logview") != 'No')
{
	$action = $_GET['action'];
	$design_head .= "<link rel='stylesheet' type='text/css' href='templates/shared/logs.css' />";

	if (!isset ($action))
	{
		// FIXME: Hardcoding number of logentries to show:
		$num_of_rows = 30;

		$content .= "<h2>".lang ("Last 30 logentries", "logs")."</h2>";
		$content .= "<table class='logs'>";
		$content .= "<tr>";
		$content .= "<th>".lang ("Log", "logs")."#</th>";
		$content .= "<th>".lang ("Timestamp", "logs")."</th>";
		$content .= "<th>".lang ("User", "logs")."</th>";
		$content .= "<th>".lang ("Logtype", "logs")."</th>";
		$content .= "<th>".lang ("IP", "logs")." / ".lang ("Host", "logs")."</th>";
		$content .= "<th>".lang ("URL", "logs")."</th>";
		$content .= "</tr>";

		$logquery = sprintf ('SELECT ID, userID, INET_NTOA(userIP) as userIP, userHost, eventID, logType, logTextNew, logTextOld, logURL, logTime FROM %s_logs ORDER BY ID DESC LIMIT %s', $sql_prefix, $num_of_rows);
		$logresult = db_query ($logquery);
		
		$numrow = 1;
		while ($log = db_fetch ($logresult))
		{
			$userip = $log->userIP;
			if (!empty($log->userHost))
			{
				$userip .= " (".$log-userHost.")";
			}

			$content .= "<tr class='logrow".$numrow."' onClick='location.href=\"index.php?module=logs&action=details&id=".$log->ID."\"' >";

			$content .= "<td>".$log->ID."</td>";
			$content .= "<td>".$log->logTime."</td>";
			$content .= "<td>".display_username($log->userID)."</td>";
			$content .= "<td>".lang (log_logtype ($log->logType), "logs")."</td>";
			$content .= "<td>".$userip."</td>";
			$content .= "<td>".$log->logURL."</td>";
			$content .= "</tr>";
			
			$numrow++;
			if ($numrow == 3)
			{
				$numrow = 1;
			}
		}
		$content .= "</table>";
	}
	elseif ($action == 'details')
	{
		$logid = $_GET['id'];
		if (empty ($logid) or !is_numeric ($logid) or !log_get ($logid))
		{
			header ('Location: index.php?module=logs');
			die ();
		}
		else
		{
			$log = log_get ($logid);

			$userip = long2ip ($log->userIP);
			if (!empty ($log->userHost))
			{
				$userip .= " (".$log->userHost.")";
			}

			$content .= "<h2>".lang ("Details for logentry ")." #".$logid."</h2>";
			$content .= "<a href='javascript:history.back()'>Back</a>";
			$content .= "<table>";
			$content .= "<tr class='logrow1'><th>".lang ("User", "logs")."</th><td>".display_username($log->userID)."</td></tr>";
			$content .= "<tr class='logrow2'><th>".lang ("Timestamp", "logs")."</th><td>".$log->logTime."</td></tr>";
			$content .= "<tr class='logrow2'><th>".lang ("IP", "logs")." / ".lang ("Host", "logs")."</th><td>".$userip."</td></tr>";
			$content .= "<tr class='logrow1'><th>".lang ("URL", "logs")."</th><td>".$log->logURL."</td></tr>";
			$content .= "<tr class='logrow2'><th>".lang ("Logtype", "logs")."</th><td>".lang (log_logtype ($log->logType), "logs")."</td></tr>";
			$content .= "</table>";

			if (($log->logType == 5) or ($log->logType == 4))
			{
				$details = unserialize ($log->logTextNew);

				$content .= "<h3>".lang ("Info entered", "logs")."</h3>";

				$content .= "<table>";

				$content .= "<tr class='logrow1'><th></th><th>New</th></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("UserID", "logs")."</th><td>".$details['userid']."</td></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("Username", "logs")."</th><td>".$details['username']."</td></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("Password", "logs")."</th><td>".$details['md5_pass']."</td></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("Email", "logs")."</th><td>".$details['EMail']."</td></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("Firstname", "logs")."</th><td>".$details['firstName']."</td></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("Lastname", "logs")."</th><td>".$details['lastName']."</td></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("Gender", "logs")."</th><td>".$details['gender']."</td></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("Birthday", "logs")."</th><td>".$details['birthDay']."</td></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("Birthmonth", "logs")."</th><td>".$details['birthMonth']."</td></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("Birthyear", "logs")."</th><td>".$details['birthYear']."</td></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("Address", "logs")."</th><td>".$details['street']."</td></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("Postnumber", "logs")."</th><td>".$details['postnumber']."</td></tr>";
				$content .= "<tr class='logrow2'><th>".lang ("Cellphone", "logs")."</th><td>".$details['cellphone']."</td></tr>";

				$content .= "</table>";
			}
		}
	}
}
else
{
	die ('No access');
}