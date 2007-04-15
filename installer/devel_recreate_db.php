<?php

/* Warning, warning, warning, WARNING!!! */
/* This file *HAS* to be removed before production! */

require '../config.php';
require '../inc/shared_functions.php';

mysql_connect($sql_host, $sql_user, $sql_pass);
mysql_select_db($sql_base);


db_query("DROP DATABASE $sql_base");
db_query("CREATE DATABASE $sql_base");
mysql_select_db($sql_base);

include 'install_sql_tables.php';

$dir = '../modules/';

$dh = opendir($dir);

while(( $file = readdir($dh)) !== false)
{
	if(is_dir($dir.$file) && $file != ".." && $file != ".")
	{
		// Directory exists
		if(file_exists($dir.$file."/install_".$file.".php")) {
			#echo "File $dir$file/install_$file.php exists!";
			include "$dir$file/install_$file.php";
		} // end file exists

	} // end if is_dir

} // end while

include 'demodata.php';