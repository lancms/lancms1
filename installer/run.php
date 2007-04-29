<?php

require("table.php");
require('../config.php');
require('../inc/shared_functions.php');

mysql_connect($sql_host, $sql_user, $sql_pass);
mysql_select_db($sql_base);

if(!isset($_GET["demodata"]))
{
	$db_file = "tables.dat";
	
	$db_tables = array();
	$file_tables = array();
	
	$tblq = db_query("SHOW TABLES");
	while($table = mysql_fetch_array($tblq))
	{
		$db_tables[$table[0]] = new table($table[0], true);
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
				$file_tables[$pr_tbl] = &$curtbl;
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
					$i++;
					if(strstr($newcol, ")"))
						break;	
				}
				
				$col[1] = $newcol;
				$col[2] = $col[2+$i];
				$col[3] = $col[3+$i];
				$col[4] = $col[4+$i];
				$col[5] = $col[5+$i];
			}
				
			$curtbl->add_column(trim($col[0]), trim($col[1]), trim($col[2]), trim($col[3]), trim($col[4]), trim($col[5]));
		}
	}
	
	foreach($file_tables as $key => $file_tbl)
	{
		if(array_key_exists($key, $db_tables))
		{
			// Table exists in db
			$db_tables[$key]->cmp($file_tbl);
		}
		else
		{
			// Table is new
			$file_tbl->create();
		}
	}
	
	foreach($db_tables as $key => $dbtbl)
	{
		if(!array_key_exists($key, $file_tables))
		{
			db_query("DROP TABLE `$key`");
		}
	}
?>
<h1>Finito!</h1>
<p>Your database should now be up to date with the current codebase.</p>
<p>Would you like adding some demo data to your database? <br><a href="run.php?demodata=1">Yes, please</a></p>
<?php
}
else
{
	include("demodata.php");
	
	echo "<h1>Like that!</h1>\n<p>Your database should now be filled with sweet demo data</p>\n";
}
?>
