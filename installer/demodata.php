<?php
/* FIXME: This file contains demodata, and should be replaced by a propper installer */


## This is things that is required!
db_query("INSERT INTO ".$sql_prefix."_groups SET groupname = '[PUBLIC]', groupType = 'access'");
db_query("INSERT INTO ".$sql_prefix."_users SET nick = 'Anonymous'");
db_query("INSERT INTO ".$sql_prefix."_events SET eventname = 'Global Event'");


## This is demodata
db_query("INSERT INTO ".$sql_prefix."_users SET
	nick = 'admin',
	password = '21232f297a57a5a743894a0e4a801fc3',
	EMail = 'admin@admin.net',
	globaladmin = 1
	");
db_query("INSERT INTO ".$sql_prefix."_users SET
	nick = 'Lak',
	password = '21232f297a57a5a743894a0e4a801fc3',
	EMail = 'laaknor@users.sourceforge.net',
	globaladmin = 0,
	firstName = 'Laaknor',
	lastName = 'TestUser'
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

db_query("INSERT INTO ".$sql_prefix."_groups SET groupname = 'Lak roxx i CS', grouppassword = 'suxx'");

db_query("INSERT INTO ".$sql_prefix."_groups SET groupname = 'WoW suxx0rz', grouppassword = 'y0'");

db_query("INSERT INTO ".$sql_prefix."_groups SET eventID = 7, groupname = 'DemoParty 6 Admins', groupType = 'access'");

db_query("INSERT INTO ".$sql_prefix."_groups SET eventID = 7, groupname = 'DemoParty 6 Crew', groupType = 'access'");

db_query("INSERT INTO ".$sql_prefix."_groups SET eventID = 7, groupname = 'DemoParty 6 dassvaskere', groupType = 'access'");

db_query("INSERT INTO ".$sql_prefix."_groups SET eventID = 6, groupname = 'DemoParty 5 Admins', groupType = 'access'");

db_query("INSERT INTO ".$sql_prefix."_group_members SET groupID = '2', userID = '3', access = 'Admin'");

db_query("INSERT INTO ".$sql_prefix."_group_members SET groupID = '3', userID = '3', access = 'Write'");

db_query("INSERT INTO ".$sql_prefix."_group_members SET groupID = '4', userID = '2', access = 'Admin'");

db_query("INSERT INTO ".$sql_prefix."_group_members SET groupID = '7', userID = '3', access = 'Admin'");

db_query("INSERT INTO ".$sql_prefix."_ACLs SET groupID = 7, eventID = 6, accessmodule = 'eventadmin', access = 'Admin'");

db_query("INSERT INTO ".$sql_prefix."_ACLs SET groupID = 4, eventID = 7, accessmodule = 'eventadmin', access = 'Admin'");

db_query("INSERT INTO ".$sql_prefix."_static SET eventID = 6, page = 'Testside', header = 'SideTest'");

db_query("INSERT INTO ".$sql_prefix."_static SET eventID = 7, page = 'Crew suxx', header = 'Admins Notes'");

db_query("INSERT INTO ".$sql_prefix."_ACLs SET groupID = 7, eventID = 6, subcategory = 1, accessmodule = 'static', access = 'Read'");

db_query("INSERT INTO ".$sql_prefix."_ACLs SET groupID = 4, eventID = 7, subcategory = 2, accessmodule = 'static', access = 'Read'");



config("register_firstname_required", "" , 1);
config("register_lastname_required", "" , 1);
config("users_may_create_clan", "" , 1);