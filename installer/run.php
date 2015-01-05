<?php

require("table.php");
if(file_exists('../OverrideConfig.php')) require('../OverrideConfig.php');
else require('../config.php');
require('../inc/db_functions.php');
require('../inc/shared_functions.php');

db_connect();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
<title>lancms installer/updater</title>
<link rel="stylesheet" type="text/css" href="installer.css">
</head>
<body>
<?php
if(!isset($_GET["demodata"]))
{
	$db_file = "tables.dat";

	$db_tables = array();
	$file_tables = array();

	$tblq = db_query("SHOW TABLES");
	while($table = db_fetch_assoc($tblq))
	{
		$db_tables[strtolower($table[0])] = new table($table[0], true);
	}



	$file = file($db_file);
	$pr_tbl = null;
	$curtbl = null;

	foreach($file as $line)
	{
		$line = trim($line);

		if($line{0} == '#' || empty($line))
			continue;

		if(!$pr_tbl)
		{
			if(preg_match('@^TABLE ?([^/]+)@i', $line, $tblname))
			{
				$tblname = $tblname[1];
				$pr_tbl = str_replace("[prefix]", $sql_prefix, $tblname);
				$curtbl = & new table($pr_tbl);
				$file_tables[strtolower($pr_tbl)] = &$curtbl;
				continue;
			}
		}
		else
		{
			if($line{0} == '{')
				continue;
			if($line{0} == '}')
			{
				$pr_tbl = null;
				continue;
			}

			$col = preg_split("/([,]+)/", $line);

			if(strstr($col[1], "(") && !strstr($col[1], ")"))
			{
				$i = 1;
				$newcol = $col[1];

				while(1)
				{
					$newcol .= ",".$col[1+$i];

					if(strstr($newcol, ")"))
						break;

					$i++;
				}

				$col[1] = $newcol;
				$col[2] = $col[2+$i];
				$col[3] = $col[3+$i];
				$col[4] = $col[4+$i];
				$col[5] = $col[5+$i];

				//echo "Name: ".$col[0]." Type: ".$col[1]." Key: ".$col[2]." Null: ".$col[3]." Default: ".$col[4]." Extra: ".$col[5]."<br />";
			}

			$curtbl->add_column(trim($col[0]), trim($col[1]), trim($col[2]), trim($col[3]), trim($col[4]), trim($col[5]));
		}
	}

	foreach($file_tables as $key => $file_tbl)
	{
		if(array_key_exists(strtolower($key), $db_tables))
		{
			// Table exists in db
			$db_tables[strtolower($key)]->cmp($file_tbl);
		}
		else
		{
			// Table is new
			$file_tbl->create();
		}
	}

	foreach($db_tables as $key => $dbtbl)
	{
		if(!array_key_exists(strtolower($dbtbl->name), $file_tables))
		{
			db_query("DROP TABLE `".$dbtbl->name."`");
		}
	}

include_once 'add_default_content.php';
include_once 'language.php';

log_add("installer", "install/upgrade", serialize($_SERVER));
?>

<h1>Finito!</h1>
<div id="content">
<p>Your database should now be up to date with the current codebase.</p>
</div>
<?php
}
db_close();
?>
</body>
</html>
