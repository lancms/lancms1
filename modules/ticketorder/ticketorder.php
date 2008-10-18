<?php
$eventID = $sessioninfo->eventID;

if(!config("enable_ticketorder", $eventID)) die("Ticketorder not enabled");
$action = $_GET['action'];