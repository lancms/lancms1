<?php


if(db_num(db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = 1")) == 0)
	db_query("INSERT INTO ".$sql_prefix."_users SET ID = 1, nick = 'Anonymous user'");

if(db_num(db_query("SELECT * FROM ".$sql_prefix."_events WHERE ID = 1")) == 0)
	db_query("INSERT INTO ".$sql_prefix."_events SET ID = 1, eventname = 'Ingen event'");

if(db_num(db_query("SELECT * FROM ".$sql_prefix."_groups WHERE ID = 1")) == 0)
	db_query("INSERT INTO ".$sql_prefix."_groups SET ID = 1, groupname = '[PUBLIC]', groupType = 'access'");

# Add a global admin with password "admin"
if(db_num(db_query("SELECT * FROM ".$sql_prefix."_users WHERE ID = 2")) == 0)
	db_query("INSERT INTO ".$sql_prefix."_users SET ID = 2, globaladmin = 1, nick = 'globaladmin', password = '".md5("admin")."'");

