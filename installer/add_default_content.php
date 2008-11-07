<?php


if(db_num(db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = 1")) == 0) 
	db_query("INSERT INTO ".$sql_prefix."_users SET ID = 1, nick = 'Anonymous user'");

if(db_num(db_query("SELECT * FROM ".$sql_prefix."_events WHERE ID = 1")) == 0) 
	db_query("INSERT INTO ".$sql_prefix."_events SET ID = 1, eventname = 'Ingen event'");

if(db_num(db_query("SELECT * FROM ".$sql_prefix."_groups WHERE ID = 1")) == 0)
	db_query("INSERT INTO ".$sql_prefix."_groups SET ID = 1, groupname = '[PUBLIC]'");
