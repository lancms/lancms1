<?php

$action = $_GET['action'];
$account = $_GET['account'];

$acl = acl_access("economy", $action