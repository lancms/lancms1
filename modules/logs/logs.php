<?php

// FIXME: using nonexistant acl "logview" to make this globaladmin-only until we get global acls working
if (acl_access ("logview", "", $sessioninfo->eventID) != 'No')
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

		$logquery = sprintf ('SELECT ID, userID, INET_NTOA(userIP) as userIP, userHost, eventID, logModule, logFunction, logTextNew, logTextOld, logURL, logTime FROM %s_logs ORDER BY ID DESC LIMIT %s', $sql_prefix, $num_of_rows);
		$logresult = db_query ($logquery);

		$numrow = 1;
		while ($log = db_fetch ($logresult))
		{
			$userip = $log->userIP;
			if (!empty($log->userHost) && $log->userHost != "NULL")
			{
				$userip .= " (".$log->userHost.")";
			}

			$content .= "<tr class='logrow".$numrow."' onClick='location.href=\"index.php?module=logs&action=details&id=".$log->ID."\"' >";

			$content .= "<td>".$log->ID."</td>";
			$content .= "<td>".$log->logTime."</td>";
			$content .= "<td>".display_username($log->userID)."</td>";
			$content .= "<td>".lang ("log_".$log->logModule."__". $log->logFunction, "logs")."</td>";
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
			} // End if(!empty)

			$content .= "<h2>".lang ("Details for logentry ")." #".$logid."</h2>";
			$content .= "<a href='javascript:history.back()'>Back</a>";
			$content .= "<table>";
			$content .= "<tr class='logrow1'><th>".lang ("User", "logs")."</th><td>".display_username($log->userID)."</td></tr>";
			$content .= "<tr class='logrow2'><th>".lang ("Timestamp", "logs")."</th><td>".$log->logTime."</td></tr>";
			$content .= "<tr class='logrow2'><th>".lang ("IP", "logs")." / ".lang ("Host", "logs")."</th><td>".$userip."</td></tr>";
			$content .= "<tr class='logrow1'><th>".lang ("URL", "logs")."</th><td>".$log->logURL."</td></tr>";
			$content .= "<tr class='logrow2'><th>".lang ("Logtype", "logs")."</th><td>".lang ("log_".$log->logModule."__".$log->logFunction, "logs")."</td></tr>";
			$content .= "</table>";

			$content .= "<table><tr><td>";
			if(!empty($log->logTextNew) && $log->logTextNew != NULL) {
				$logNew = unserialize($log->logTextNew);

				$row = 1;
				$content .= "<table>";
				if(!empty($logNew)) foreach($logNew AS $type => $value) {

					$content .= "<tr class='logrow$row'><th>".$type."</th><td>".$value."</th></tr>";
					$row++;
					if($row == 3) $row = 1;
				} // End foreach logNew
				$content .= "</table>";
			} // End if(!empty(logTextNew)
			$content .= "</td><td>";

			if(!empty($log->logTextOld) && $log->logTextOld != NULL) {
				$logOld = unserialize($log->logTextOld);

				$row = 1;
				$content .= "<table>";
				if(!empty($logOld)) foreach($logOld AS $type => $value) {

					$content .= "<tr class='logrow$row'><th>".$type."</th><td>".$value."</th></tr>";
					$row++;
					if($row == 3) $row = 1;
				} // End foreach logOld
				$content .= "</table>";


			} // end if(!empty(logTextOld)
			$content .= "</td></tr></table>";
		} // End else
	} // End elseif action == details
}
else
{
	die ('No access');
}
