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


## eventconfigs

$eventconfig['checkbox'][] = 'enable_ticketorder';
$eventconfig['checkbox'][] = 'enable_FAQ';
$eventconfig['checkbox'][] = 'seating_enabled';
$eventconfig['checkbox'][] = 'enable_wannabe';


## Globalconfigs
$globalconfig['checkbox'][] = 'users_may_create_clan';
$globalconfig['checkbox'][] = 'register_firstname_required';
$globalconfig['checkbox'][] = 'register_lastname_required';
$globalconfig['checkbox'][] = 'users_may_register';
$globalconfig['checkbox'][] = 'userinfo_birthday_required';
$globalconfig['checkbox'][] = 'userinfo_birthyear_required';
$globalconfig['checkbox'][] = 'userinfo_gender_required';
$globalconfig['checkbox'][] = 'userinfo_address_required';




## eventACLs

$eventaccess[] = 'eventadmin';
$eventaccess[] = 'FAQ';
$eventaccess[] = 'ticketadmin';
$eventaccess[] = 'static';
$eventaccess[] = 'seating'; // Access to seat any other users
$eventaccess[] = 'wannabeadmin';
$eventaccess[] = 'economy';
$eventaccess[] = 'crewlist';


## GlobalACLs
$globalaccess[] = 'clanAdmin';
$globalaccess[] = 'userAdmin';
$globalaccess[] = 'translate';


## SpecialACLs
$specialaccess[] = 'groupaccess'; // Used inside groups
$specialaccess[] = 'globaladmin'; // Can do anything


## Static system-messages
$systemstatic[] = 'index'; // Main page
$systemstatic[] = 'ticketorder'; // Text to display below ticketorder-page
$systemstatic[] = 'seatmap'; // Text to display in the bottom of seatmap


## Datestuff

$monthname[1] = 'January';
$monthname[2] = 'February';
$monthname[3] = 'March';
$monthname[4] = 'April';
$monthname[5] = 'May';
$monthname[6] = 'June';
$monthname[7] = 'July';
$monthname[8] = 'August';
$monthname[9] = 'September';
$monthname[10] = 'October';
$monthname[11] = 'November';
$monthname[12] = 'December';
