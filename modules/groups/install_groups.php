<?php
# This should probably depend on... register?

db_query("CREATE TABLE IF NOT EXISTS $sql_prefix.groups (
	ID int(11) auto_increment primary key,
	groupname varchar(40),
	grouppassword varchar(50) default '',
	created_by int(11) default 0,
	created_timestamp int(10) default 0
	)");

db_query("CREATE TABLE IF NOT EXISTS $sql_prefix.group_members (
	groupID int(11),
	userID int(11),
	admin tinyint(1) default 0,
	primary key (groupID, userID)
	)");