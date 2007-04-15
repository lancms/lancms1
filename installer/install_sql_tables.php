<?php

# Can't live with them, can't run anything without them... Creating users
db_query("CREATE TABLE IF NOT EXISTS ".$sql_prefix."_users (
	ID int(11) primary key auto_increment,
	nick varchar(35) NOT NULL,
	password varchar(50) default '',
	EMail varchar(50),
	globaladmin tinyint(1) default 0
	)");


db_query('CREATE TABLE IF NOT EXISTS '.$sql_prefix.'_config (
	config varchar(32) NOT NULL primary key,
	value varchar(32) NOT NULL
	)');

db_query("CREATE TABLE IF NOT EXISTS ".$sql_prefix."_session (
	sID varchar(35) PRIMARY KEY,
	userIP varchar(15) default '000.000.000.000',
	userID int(11) default 0,
	lastVisit int(11) default 0
	)");