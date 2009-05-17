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
		$content .= "<th>".lang ("New", "logs")."</th>";
		$content .= "<th>".lang ("Old", "logs")."</th>";
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

			$content .= "<tr class='logrow".$numrow."'>";

			$content .= "<td>".$log->ID."</td>";
			$content .= "<td>".$log->logTime."</td>";
			$content .= "<td>".display_username($log->userID)."</td>";
			$content .= "<td>".lang (log_logtype ($log->logType), "logs")."</td>";
			$content .= "<td>".$log->logTextNew."</td>";
			$content .= "<td>".$log->logTextOld."</td>";
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
}
else
{
	die ('No access');
}
