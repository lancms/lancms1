<?php

# Can't live with them, can't run anything without them... Creating users
db_query("CREATE TABLE IF NOT EXISTS $sql_prefix.users
	ID int(11) primary key auto_increment,
	nick varchar(35) NOT NULL,
	password varchar(50) default '',
	EMail varchar(50),
	globaladmin tinyint(1) default 0"
);


db_query("CREATE TABLE IF NOT EXISTS $sql_prefix.config
	config varchar(32) NOT NULL primary key,
	value varchar(32) NOT NULL
");

