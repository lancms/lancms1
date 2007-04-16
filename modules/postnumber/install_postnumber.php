<?php

db_query("CREATE TABLE IF NOT EXISTS ".$sql_prefix."_postnumber (
	ID int(11) primary key auto_increment,
	postnumber varchar(7) NOT NULL,
	postplace varchar(50) default '',
	country tinyint(3) default 0,
	county int(5) default 0,
	comments text
	)");

db_query("CREATE TABLE IF NOT EXISTS ".$sql_prefix."_counties (
	ID int(5) primary key,
	countyname varchar(35),
	countyID int(11)
	)");

db_query("CREATE TABLE IF NOT EXISTS ".$sql_prefix."_countries (
	ID tinyint(4) primary key,
	countryname varchar(25)
	)");
	
	
require '../modules/postnumber/postnumbers/norwegian.php';