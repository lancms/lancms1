<?php

require_once ('include.php');


$queuetable = $sql_prefix."_infoscreensQueues";
$screentable = $sql_prefix."_infoscreens";
$slidetable = $sql_prefix."_infoscreensSlides";
$eid = $sessioninfo->eventID;

$s = $_GET['s'];
$q = $_GET['q'];

$slide = $_GET['slide'];

if (empty ($slide) or !is_numeric($slide))
{

if (empty ($s) or !is_numeric($s))
{
	$query = sprintf ('select * from %s WHERE eventID=%s ORDER BY ID', $screentable, $eid);
	$result = db_query ($query);
	$count = db_num ($result);

	if ($count)
	{
		$fetch = db_fetch($result);
		$s = $fetch->ID;
	}
	else
	{
		die (_('There are no screens for this event'));
	}
}
else
{
	$query = sprintf ('select * from %s WHERE eventID=%s AND ID=%s ORDER BY ID', $screentable, $eid, db_escape($s));
	$result = db_query ($query);
	$count = db_num ($result);

	if ($count)
	{
		$fetch = db_fetch($result);
		$s = $fetch->ID;
	}
	else
	{
		die (_('The screen requested does not exist'));
	}
	
}


if (empty ($q) or !is_numeric($q))
{
	$query = sprintf ('select * from %s WHERE eventID=%s and screenID=%s ORDER BY ID', $queuetable, $eid, db_escape($s));
	$result = db_query ($query);
	$count = db_num ($result);

	if (!$count)
	{
		die (_('There are no queue for this screen'));
	}
	else
	{
		$num = 1;
		while ($fetch = db_fetch ($result))
		{
			if ($num == 1)
			{
				$showslide = $fetch->slideID;
				$wait = $fetch->wait;
			}
			if ($num < 3)
			{
				$nextinqueue = $fetch->ID;
			}
			else
			{
				break;
			}
			$num++;
		}
	}
}
else
{
	$query = sprintf ('select * from %s WHERE ID=%s ORDER BY ID', $queuetable, db_escape($q));
	$result = db_query ($query);
	$count = db_num ($result);
	$fetch = db_fetch ($result);
	$showslide = $fetch->slideID;
	$wait = $fetch->wait;

	$query = sprintf ('select * from %s WHERE eventID=%s and screenID=%s and ID>%s ORDER BY ID', $queuetable, $eid, db_escape($s), db_escape($q));
	$result = db_query ($query);
	$count = db_num ($result);
	if ($count)
	{
		$fetch = db_fetch ($result);
		$nextinqueue = $fetch->ID;
	}
	else
	{
		$query = sprintf ('select * from %s WHERE eventID=%s and screenID=%s ORDER BY ID', $queuetable, $eid, db_escape($s));
		$result = db_query ($query);
		$fetch = db_fetch($result);
		$nextinqueue = $fetch->ID;
	}
}


if ($showslide and $nextinqueue)
{
	showslide ($showslide, $wait, $nextinqueue, $s);
}
elseif (empty ($showslide))
{
	die ('wtf.. no slide to show!');
}
elseif (empty ($nextinqueue))
{
	die ('wtf.. no next in queue!');
}
else
{
	die ('wtf..');
}
}
else // if $slide is something - preview
{
	previewslide ($slide);
}

function showslide($showslide, $wait, $nextinqueue, $s)
{
	global $slidetable;

	$slideQ = sprintf ('SELECT * FROM %s WHERE ID=%s', $slidetable, db_escape($showslide));
	$slideR = db_query ($slideQ);
	$slideC = db_num ($slideR);
	
	if (!$slideC)
	{
		die ("wtf.. slide doesn't exist!");
	}
	$slide = db_fetch ($slideR);
	

	header ('Refresh: '.$wait.';url="?q='.$nextinqueue.'&s='.$s.'"');


	print "<html>\n<head>\n";

	print "<link rel='stylesheet' href='templates/shared/party.css' type='text/css' />";

	print "</head>\n<body>\n";

	print $slide->content;

	print "</body>\n<html>\n";

}

function previewslide ($slide)
{
	global $slidetable;
	$slideQ = sprintf ('SELECT * FROM %s WHERE ID=%s', $slidetable, db_escape($slide));
	$slideR = db_query ($slideQ);
	$slideC = db_num ($slideR);
	
	if (!$slideC)
	{
		die ("wtf.. slide doesn't exist!");
	}
	$slide = db_fetch ($slideR);
	
	print "<html>\n<head>\n";

	print "<link rel='stylesheet' href='templates/shared/party.css' type='text/css' />";

	print "</head>\n<body>\n";

	print "<input type='button' onclick='history.back()' value='Back'/>\n";
	print "<hr />\n";

	print $slide->content;

	print "</body>\n<html>\n";
}


?>
