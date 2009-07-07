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
$eventconfig['checkbox'][] = 'enable_composystem';
$eventconfig['checkbox'][] = 'enable_reseller';


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
$eventaccess[] = 'compoadmin';
$eventaccess[] = 'reseller';


## GlobalACLs
$globalaccess[] = 'clanAdmin';
$globalaccess[] = 'userAdmin';
$globalaccess[] = 'translate';
$globalaccess[] = 'logview';

## SpecialACLs
$specialaccess[] = 'groupaccess'; // Used inside groups
$specialaccess[] = 'globaladmin'; // Can do anything
$specialaccess[] = 'eventAttendee'; // May access the event, if non-public


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


## Compotypes
$compotype[] = 'FFA';
$compotype[] = 'clan';
$compotype[] = '1on1';


$userprefs[0]['type'] = 'text';
$userprefs[0]['name'] = 'firstName';
$userprefs[0]['displayName'] = 'First name';
$userprefs[0]['mandatory'] = config("register_firstname_required");

$userprefs[1]['type'] = 'text';
$userprefs[1]['name'] = 'lastName';
$userprefs[1]['displayName'] = 'Last name';
$userprefs[1]['mandatory'] = config("register_lastname_required");

$userprefs[2]['type'] = 'dropdown';
$userprefs[2]['name'] = 'birthDay';
$userprefs[2]['displayName'] = 'Birthday';
$userprefs[2]['mandatory'] = config("userinfo_birthday_required");
$userprefs[2]['group_pref'] = 1;
$userprefs[2]['group_pref_begin'] = 1;
for($i=1;$i<32;$i++) $userprefs_birthDay_values[$i] = $i;
$userprefs[2]['dropdown_values'] = $userprefs_birthDay_values;

$userprefs[3]['type'] = 'dropdown';
$userprefs[3]['name'] = 'birthMonth';
$userprefs[3]['mandatory'] = config("userinfo_birthday_required");
$userprefs[3]['group_pref'] = 1;
$userprefs[3]['dropdown_values'] = $monthname;

$userprefs[4]['type'] = 'dropdown';
$userprefs[4]['name'] = 'birthYear';
$userprefs[4]['mandatory'] = config("userinfo_birthyear_required");
$userprefs[4]['group_pref'] = 1;
$userprefs[4]['group_pref_end'] = 1;
for($i=1950;$i<2009;$i++) $userprefs_birthYear_values[$i] = $i;
$userprefs[4]['dropdown_values'] = $userprefs_birthYear_values;

$userprefs[5]['type'] = 'text';
$userprefs[5]['name'] = 'street';
$userprefs[5]['displayName'] = 'Streetadress';
$userprefs[5]['mandatory'] = config("userinfo_address_required");

$userprefs[6]['type'] = 'text';
$userprefs[6]['name'] = 'postNumber';
$userprefs[6]['displayName'] = 'Postnumber';
$userprefs[6]['mandatory'] = config("userinfo_address_required");

$userprefs[7]['type'] = 'dropdown';
$userprefs[7]['name'] = 'gender';
$userprefs[7]['displayName'] = 'Gender';
$userprefs[7]['mandatory'] = config("userinfo_gender_required");
$userprefs[7]['dropdown_values'] = array ('Male' => 'Male', 'Female' => 'Female');
