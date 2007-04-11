<?php

# This could have looked something like this, but users is probably something that should be in a basic install 
/*
db_query("CREATE TABLE IF NOT EXISTS $sql_prefix.users
ID int(11) primary key auto_increment,
nick varchar(35) NOT NULL,
password varchar(50) default '',
country tinyint(3) default 0,
EMail varchar(50),
MSNaddress varchar(50),
MSNpublic tinyint(1) default 0,
postcode tinyint(6),
globaladmin tinyint(1) default 0
");
*/