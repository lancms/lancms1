<?php
/* FIXME: This file contains demodata, and should be replaced by a propper installer */
db_query("INSERT INTO ".$sql_prefix."_users SET
	nick = 'admin',
	password = '21232f297a57a5a743894a0e4a801fc3',
	globaladmin = 1
	");