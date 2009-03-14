<?php


## Define diffrent variables
$seattype['b'] = 'Blank';
$seattype['d'] = 'Open seat';
$seattype['p'] = 'Password-protected seat';
$seattype['g'] = 'Group-protected seat';
$seattype['t'] = 'Text';
$seattype['w'] = 'Wall';
$seattype['o'] = 'Door';
$seattype['a'] = 'Area';


$maxTicketsPrUser = 5;



$eventconfig['checkbox'][] = 'enable_ticketorder';
$eventconfig['checkbox'][] = 'enable_FAQ';
$eventconfig['checkbox'][] = 'seating_enabled';
$eventconfig['checkbox'][] = 'enable_wannabe';


$globalconfig['checkbox'][] = 'users_may_create_clan';
$globalconfig['checkbox'][] = 'register_firstname_required';
$globalconfig['checkbox'][] = 'register_lastname_required';