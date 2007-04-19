<?php
/* FIXME: This file contains demodata, and should be replaced by a propper installer */
db_query("INSERT INTO ".$sql_prefix."_users SET
	nick = 'admin',
	password = '21232f297a57a5a743894a0e4a801fc3',
	EMail = 'admin@admin.net',
	globaladmin = 1
	");

db_query("INSERT INTO ".$sql_prefix."_events SET
	eventname = 'DemoParty 1',
	eventClosed = 1,
	eventPublic = 1
	");

db_query("INSERT INTO ".$sql_prefix."_events SET
	eventname = 'DemoParty 2',
	eventClosed = 1,
	eventPublic = 1
	");

db_query("INSERT INTO ".$sql_prefix."_events SET
	eventname = 'DemoParty 3',
	eventClosed = 1,
	eventPublic = 1
	");

db_query("INSERT INTO ".$sql_prefix."_events SET
	eventname = 'DemoParty 4',
	eventClosed = 1,
	eventPublic = 1
	");

db_query("INSERT INTO ".$sql_prefix."_events SET
	eventname = 'DemoParty 5',
	eventPublic = 1
	");
db_query("INSERT INTO ".$sql_prefix."_events SET
	eventname = 'DemoParty 6',
	eventPublic = 1
	");
db_query("INSERT INTO ".$sql_prefix."_events SET
	eventname = 'DemoParty 7'
	");