<?php

$action = $_GET['action'];

$acl_access = acl_access("static", "", $sessioninfo->eventID);
