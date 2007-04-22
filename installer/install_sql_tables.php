<?php

# Can't live with them, can't run anything without them... Creating users
db_query("CREATE TABLE IF NOT EXISTS ".$sql_prefix."_users (
	ID int(11) primary key auto_increment,
	nick varchar(35) NOT NULL,
	password varchar(50) default '',
	EMail varchar(50),
	globaladmin tinyint(1) default 0,
	firstName varchar(40),
	lastName varchar(40),
	MSNAddress varchar(35),
	ICQAddress int(12) default '',
	postNumber int(5) default '',
	postPlace varchar(35) default '',
	registerTime int(11) default 0,
	registerIP varchar(15) default '000.000.000.000',
	EMailVerifyCode varchar(25) default '',
	userInfoVerified tinyint(1) default 0
	)");


db_query('CREATE TABLE IF NOT EXISTS '.$sql_prefix.'_config (
	config varchar(32) NOT NULL primary key,
	value varchar(32) NOT NULL
	)');


// FIXME: eventID should probably be more dynamic...
db_query("CREATE TABLE IF NOT EXISTS ".$sql_prefix."_session (
	sID varchar(35) PRIMARY KEY,
	userIP varchar(15) default '000.000.000.000',
	userID int(11) default 0,
	eventID int(11) default 0,
	lastVisit int(11) default 0
	)");

db_query("CREATE TABLE IF NOT EXISTS ".$sql_prefix."_events (
	ID int(11) PRIMARY KEY auto_increment,
	eventname varchar(35) default '',
	createdByTime int(11) default 0,
	createdByUser int(11) default 0,
	eventClosed tinyint(1) default 0,
	eventPublic tinyint(1) default 0
	)");

db_query("CREATE TABLE IF NOT EXISTS ".$sql_prefix."_groups (
	ID int(11) auto_increment primary key,
	groupname varchar(40),
	grouppassword varchar(50) default '',
	created_by int(11) default 0,
	created_timestamp int(10) default 0,
	groupType enum('access', 'clan') default 'clan'
	)");

db_query("CREATE TABLE IF NOT EXISTS ".$sql_prefix."_group_members (
	groupID int(11),
	userID int(11),
	access enum('No', 'Read', 'Write', 'Admin') default 'No',
	primary key (groupID, userID)
	)");


db_query("CREATE TABLE IF NOT EXISTS ".$sql_prefix."_ACLs (
	groupID int(11),
	eventID int(11),
	subcategory varchar(35) default 0,
	accessmodule varchar(25),
	access enum('No', 'Read', 'Write', 'Admin') default 'No',
	primary key (groupID, eventID, accessmodule)
	)");
	
db_query("CREATE TABLE ".$sql_prefix."_lang (
	ID int(11) NOT NULL auto_increment,
	string text,
	language varchar(30) NOT NULL default 'english',
	module varchar(30) default NULL,
	translated text,
	PRIMARY KEY  (ID)
	)");
	
db_query("CREATE TABLE ".$sql_prefix."_static (
	ID int(11) NOT NULL auto_increment,
	eventID int(11) default 0,
	header varchar(35),
	page text,
	created_by int(11) default 0,
	created_timestamp int(10) default 0,
	modified_by int(11) default 0,
	modified_timestamp int(10) default 0,
	pageViews int(15) default 0,
	PRIMARY KEY (ID)
	)");