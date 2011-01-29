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
$eventconfig['checkbox'][] = 'seating_public';
$eventconfig['checkbox'][] = 'enable_wannabe';
$eventconfig['checkbox'][] = 'enable_composystem';
$eventconfig['checkbox'][] = 'enable_reseller';
$eventconfig['checkbox'][] = 'enable_crewlist';
$eventconfig['checkbox'][] = 'enable_news';
$eventconfig['checkbox'][] = 'enable_kiosk';
$eventconfig['checkbox'][] = 'enable_forum';

## Globalconfigs
$globalconfig['checkbox'][] = 'users_may_create_clan';
$globalconfig['checkbox'][] = 'register_firstname_required';
$globalconfig['checkbox'][] = 'register_lastname_required';
$globalconfig['checkbox'][] = 'users_may_register';
$globalconfig['checkbox'][] = 'userinfo_birthday_required';
$globalconfig['checkbox'][] = 'userinfo_birthyear_required';
$globalconfig['checkbox'][] = 'userinfo_gender_required';
$globalconfig['checkbox'][] = 'userinfo_address_required';
$globalconfig['text'][] = 'hostname';



## eventACLs

$eventaccess[] = 'eventadmin';
$eventaccess[] = 'FAQ';
$eventaccess[] = 'ticketadmin';
$eventaccess[] = 'static';
$eventaccess[] = 'seating'; // Access to seat any other users
$eventaccess[] = 'wannabeadmin';
#$eventaccess[] = 'economy';
$eventaccess[] = 'crewlist';
$eventaccess[] = 'compoadmin';
$eventaccess[] = 'kiosk_admin';
$eventaccess[] = 'kiosk_stats';
$eventaccess[] = 'kiosk_sales';
$eventaccess[] = 'news';


## GlobalACLs
$globalaccess[] = 'clanAdmin';
$globalaccess[] = 'userAdmin';
$globalaccess[] = 'translate';
$globalaccess[] = 'logview';
$globalaccess[] = 'sendSMS';
$globalaccess[] = 'massmail';

## SpecialACLs
$specialaccess[] = 'groupaccess'; // Used inside groups
$specialaccess[] = 'globaladmin'; // Can do anything
$specialaccess[] = 'eventAttendee'; // May access the event, if non-public
$specialaccess[] = 'reseller'; // Gives access to reseller and tickets

## Static system-messages
$systemstatic[] = 'index'; // Main page
$systemstatic[] = 'ticketorder'; // Text to display below ticketorder-page
$systemstatic[] = 'seatmap'; // Text to display in the bottom of seatmap


## Datestuff

$monthname[1] = lang('January');
$monthname[2] = lang('February');
$monthname[3] = lang('March');
$monthname[4] = lang('April');
$monthname[5] = lang('May');
$monthname[6] = lang('June');
$monthname[7] = lang('July');
$monthname[8] = lang('August');
$monthname[9] = lang('September');
$monthname[10] = lang('October');
$monthname[11] = lang('November');
$monthname[12] = lang('December');


## Compotypes
$compotype[] = 'FFA';
$compotype[] = 'clan';
$compotype[] = '1on1';
$winlosetype[] = 'win';
$winlosetype[] = 'loss';
$winlosetype[] = 'na';

$userprefs[0]['type'] = 'text';
$userprefs[0]['name'] = 'nick';
$userprefs[0]['edit_userAdmin'] = 'Admin';
$userprefs[0]['displayName'] = lang('Username');

$userprefs[1]['type'] = 'text';
$userprefs[1]['name'] = 'firstName';
$userprefs[1]['displayName'] = lang('First name');
$userprefs[1]['mandatory'] = config("register_firstname_required");

$userprefs[2]['type'] = 'text';
$userprefs[2]['name'] = 'lastName';
$userprefs[2]['displayName'] = lang('Last name');
$userprefs[2]['mandatory'] = config("register_lastname_required");

$userprefs[3]['type'] = 'dropdown';
$userprefs[3]['name'] = 'birthDay';
$userprefs[3]['displayName'] = lang('Birthday');
$userprefs[3]['mandatory'] = config("userinfo_birthday_required");
$userprefs[3]['group_pref'] = 1;
$userprefs[3]['group_pref_begin'] = 1;
for($i=1;$i<32;$i++) $userprefs_birthDay_values[$i] = $i;
$userprefs[3]['dropdown_values'] = $userprefs_birthDay_values;

$userprefs[4]['type'] = 'dropdown';
$userprefs[4]['name'] = 'birthMonth';
$userprefs[4]['mandatory'] = config("userinfo_birthday_required");
$userprefs[4]['group_pref'] = 1;
$userprefs[4]['dropdown_values'] = $monthname;

$userprefs[5]['type'] = 'dropdown';
$userprefs[5]['name'] = 'birthYear';
$userprefs[5]['mandatory'] = config("userinfo_birthyear_required");
$userprefs[5]['group_pref'] = 1;
$userprefs[5]['group_pref_end'] = 1;
for($i=1950;$i<2009;$i++) $userprefs_birthYear_values[$i] = $i;
$userprefs[5]['dropdown_values'] = $userprefs_birthYear_values;

$userprefs[6]['type'] = 'text';
$userprefs[6]['name'] = 'street';
$userprefs[6]['displayName'] = lang('Streetadress');
$userprefs[6]['mandatory'] = config("userinfo_address_required");

$userprefs[7]['type'] = 'text';
$userprefs[7]['name'] = 'postNumber';
$userprefs[7]['displayName'] = lang('Postnumber');
$userprefs[7]['mandatory'] = config("userinfo_address_required");

$userprefs[8]['type'] = 'dropdown';
$userprefs[8]['name'] = 'gender';
$userprefs[8]['displayName'] = lang('Gender');
$userprefs[8]['mandatory'] = config("userinfo_gender_required");
$userprefs[8]['edit_userAdmin'] = 'Write'; // Require Write-access in userAdmin to change this
$userprefs[8]['dropdown_values'] = array ('Male' => 'Male', 'Female' => 'Female');

$userprefs[9]['type'] = 'text';
$userprefs[9]['name'] = 'cellphone';
$userprefs[9]['displayName'] = lang('Cellphone');

$userprefs[10]['type'] = 'text';
$userprefs[10]['name'] = 'EMail';
$userprefs[10]['displayName'] = lang('EMail');


## SpecialListings
$listingtype[0]['type'] = 'eventAttendee';
$listingtype[0]['name'] = lang('List all attendees on current event');
$listingtype[0]['SQL'] = "SELECT u.firstName,u.lastName,u.nick FROM GO_users u JOIN GO_tickets t ON t.user=u.ID WHERE t.paid = 'yes' AND t.eventID=$sessioninfo->eventID";
#$listingtype[0]['displaymode'] = 'CSV';


$listingtype[1]['type'] = 'yearAttendee';
$listingtype[1]['name'] = 'List all attendees on specified year';
$listingtype[1]['option'] = 1;


$listingtype[2]['type'] = 'ticketsSold';
$listingtype[2]['name'] = 'List all tickets sold';

## Personal settings

$userpersonalprefs[0]['type'] = 'checkbox';
$userpersonalprefs[0]['name'] = 'allowViewMail';
$userpersonalprefs[0]['displayName'] = lang('Allow other users to see my mailadress');
$userpersonalprefs[0]['default_register'] = 1; // Have this enabled per default when user registers # FIXME, should not be enabled
$userpersonalprefs[0]['required_on'] = 0; // Don't let the user disable this # FIXME, should not be enabled


$mailList[0]['name'] = lang('All users');
$mailList[0]['SQL'] = 'SELECT DISTINCT ID FROM '.$sql_prefix.'_users WHERE ID != 1';
