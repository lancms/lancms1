<?php

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til billettype")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Add tickettype")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billettype er aktiv?")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Tickettype is active?")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billetttype")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Type of ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Onsite billett uten datamaskin")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Onsite ticket without computer")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Onsite billett med datamaskin")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Onsite ticket with computer")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Forh�ndsbestilt billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Preordered ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Forh�ndsbetalt billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Prepaid ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Pris p� billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Price of ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Navn p� billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Name of ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Navn p� arrangement")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("Event name")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Administrer dette arrangementet")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("Admin this event")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Set arrangement offentlig")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("Set event public")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Sett aktiv")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("Set active")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Offentlig")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("Public")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til nytt arrangement")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("Add new event")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Endre globale innstillinger")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("Change global options")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Global admin")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Global Admin")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Wannabe")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Wannabe")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Arrangementadministrasjon")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Event Admin")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Logg ut")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Logout")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ingenting valgt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe_crewprefs")."' AND string = '".db_escape("WannabeCrewListPreference0")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Absolutt!")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe_crewprefs")."' AND string = '".db_escape("WannabeCrewListPreference1")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Gjerne")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe_crewprefs")."' AND string = '".db_escape("WannabeCrewListPreference2")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Sikkert")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe_crewprefs")."' AND string = '".db_escape("WannabeCrewListPreference3")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Helst ikke")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe_crewprefs")."' AND string = '".db_escape("WannabeCrewListPreference4")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Overhodet ikke")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe_crewprefs")."' AND string = '".db_escape("WannabeCrewListPreference5")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lagre")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe")."' AND string = '".db_escape("Save")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukernavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Username")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Passord")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Password")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Passord igjen")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Password again")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("E-post")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("E-Mail")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fornavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("First name")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Etternavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Last name")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Adresse")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Address")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Postnummer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Postnumber")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kj�nn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Gender")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mann")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Male")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kvinne")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Female")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("F�dselsdag")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Birthday")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("F�dselsm�ned")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Birthmonth")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("F�dsels�r")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Birthyear")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lag bruker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Create user")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Arrangementkonfigurasjon")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Event config")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Gruppeadministrasjon")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Group Management")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Rediger statiske sider")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Edit static pages")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Rediger FAQ")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Edit FAQs")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("WannabeCrew")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("WannabeCrew")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Plassadministrasjon")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Seatreg Admin")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billettadministrasjon")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Ticket Admin")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Endre grupperettigheter")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Change group rights")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til gruppe")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Add group")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Gruppe:")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Group: ")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Nick")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Nick")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tilgang")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Access")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("S�k etter bruker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Search user")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tilbake til grupper")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Back to groups")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Sp�rsm�l")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Questions")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Crew")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Crews")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Vis s�knader")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("View Applications")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til crew")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Add crew")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lagre")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("translate")."' AND string = '".db_escape("Save")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kommentar")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Comment")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ikke valgt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin_prefs")."' AND string = '".db_escape("wannabeAdminCmt0")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Overhodet ikke")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin_prefs")."' AND string = '".db_escape("wannabeAdminCmt1")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Helst ikke")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin_prefs")."' AND string = '".db_escape("wannabeAdminCmt2")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Whatevah")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin_prefs")."' AND string = '".db_escape("wannabeAdminCmt3")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kan vel hende")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin_prefs")."' AND string = '".db_escape("wannabeAdminCmt4")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Absolutt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin_prefs")."' AND string = '".db_escape("wannabeAdminCmt5")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lagre kommentar")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Save comment")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Bytt plass")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("seatadmin")."' AND string = '".db_escape("Change seats")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til rad")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("seatadmin")."' AND string = '".db_escape("Add row")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til kolonne")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("seatadmin")."' AND string = '".db_escape("Add column")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Nullstill kart")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("seatadmin")."' AND string = '".db_escape("Reset map")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Vennligst skriv inn et brukernavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Please provide a username")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Bruker registrert")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("User registered")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Aktiver billettbestilling")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventconfigoption")."' AND string = '".db_escape("enable_ticketorder")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Aktiver FAQ")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventconfigoption")."' AND string = '".db_escape("enable_FAQ")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Plassregistrering aktivert")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventconfigoption")."' AND string = '".db_escape("seating_enabled")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Aktiver Wannabe")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventconfigoption")."' AND string = '".db_escape("enable_wannabe")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lagre")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin_config")."' AND string = '".db_escape("Save")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ingen")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("functions")."' AND string = '".db_escape("No")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lese")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("functions")."' AND string = '".db_escape("Read")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Skrive")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("functions")."' AND string = '".db_escape("Write")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Admin")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("functions")."' AND string = '".db_escape("Admin")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lagre")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("group")."' AND string = '".db_escape("Save")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fornavn m� settes")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Firstname must be set")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Etternavn m� fylles inn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Lastname must be set")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Du m� spesifisere hvilket kj�nn du har")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("You have to specify your gender")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Postnummer m� v�re et nummer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Postnumber has to be a number")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Vennligst spesifiser adressen din")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("registert")."' AND string = '".db_escape("Please specify your address")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ingen brukere funnet")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("No users found")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lagre")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Save")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til ny side")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("static")."' AND string = '".db_escape("Add new page")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lagre")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("static")."' AND string = '".db_escape("Save")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Rediger tilgang")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("static")."' AND string = '".db_escape("Edit access")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Redigerer:")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("static")."' AND string = '".db_escape("Editing: ")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til gruppetilgang")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("static")."' AND string = '".db_escape("Add group access")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Du er logget inn som:")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("You are logged in as:")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Logg inn som:")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("login")."' AND string = '".db_escape("Log in as:")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Passord:")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("login")."' AND string = '".db_escape("Password:")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Beklager, ingen slik bruker funnet")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("login")."' AND string = '".db_escape("Sorry, no such user found")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Sp�rsm�l")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("FAQ")."' AND string = '".db_escape("Question")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til sp�rsm�l")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("FAQ")."' AND string = '".db_escape("Add question")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukere kan opprette klaner")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("users_may_create_clan")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Registrering, fornavn p�krevd")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("register_firstname_required")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Registrering, etternavn p�krevd")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("register_lastname_required")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukere kan registrere seg")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("users_may_register")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukerinformasjon, f�dselsdag p�krevd")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("userinfo_birthday_required")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukerinfo, f�dsels�r p�krevd")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("userinfo_birthyear_required")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukerinfo, kj�nn p�krevd")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("userinfo_gender_required")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukerinfo, adresse p�krevd")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("userinfo_address_required")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lagre")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin_config")."' AND string = '".db_escape("Save")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("For mange brukere funnet, vennligst spesifiser")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Too many users found, please specify")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Beklager, feil passord!")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("login")."' AND string = '".db_escape("sorry, wrong password!")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Januar")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("January")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Februar")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("February")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mars")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("March")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("April")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("April")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mai")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("May")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Juni")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("June")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Juli")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("July")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("August")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("August")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("September")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("September")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Oktober")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("October")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("November")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("November")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Desember")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("December")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Hovedsiden")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Main page")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("�konomi")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Economy")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Hovedkonto")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Main account")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kontonummer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Accountnumber")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Bel�p")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Amount")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kontobeskrivelse")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Account description")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ny konto")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("New account")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lagre endringer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Save changes")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Nytt sp�rsm�l")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("New question")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tekstsp�rsm�lsfelt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Text answer-field")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Nedtrekksmeny")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Dropdown")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Avkrysningsboks")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Checkbox")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til nytt sp�rsm�l")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Add new question")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("S�knad fra:")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Application from:")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tilbake til listen")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Back to list")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("S�k crew")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe")."' AND string = '".db_escape("Apply as crew")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Crew")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Crew")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Svar")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Response")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Sp�rsm�l")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Question")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Svar")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Answer")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billettnummer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Ticketnumber")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billettnavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Ticketname")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billettype")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Tickettype")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Pris")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Price")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Solgte billetter")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Sold tickets of type")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("forh�ndsbestilt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("preorder")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Rediger billettype")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Edit tickettype")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Beklager, for mange brukere. Fors�k � begrense s�ket ditt mer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("login")."' AND string = '".db_escape("Sorry, too many users, try to narrow down the search")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ikke besvart")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe_crewprefs")."' AND string = '".db_escape("WannabeCrewListPreference")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("F�dsels�r m� settes")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Birthyear has to be set")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("F�dselsdag m� settes")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Birthday has to be set")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("F�dselsm�ned m� settes")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Birthmonth has to be set")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Vennligst logg inn for � s�ke som crew")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe")."' AND string = '".db_escape("Please login to apply for crew")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("S�ker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Applicant")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lagre")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("FAQ")."' AND string = '".db_escape("Save")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Rediger FAQ")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("FAQ")."' AND string = '".db_escape("Edit FAQ")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Slett FAQ")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("FAQ")."' AND string = '".db_escape("Delete FAQ")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("FAQ")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("FAQ")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Crewliste")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Crewlist")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Sett passord")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("seatadmin")."' AND string = '".db_escape("Set password")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billettbestilling")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Order ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kj�p billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Buy ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billettnummer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Ticketnumber")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billettype")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Tickettype")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Status")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Status")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kartplassering")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Map placement")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Bruker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("User")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Eier")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Owner")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("ikke brukt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("notused")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Plasser p� kartet")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Place on map")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Oppdater kart")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Update map")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Plassregistrering ikke aktivert enn�")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Seating not enabled yet")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("S�k etter bruker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Search user")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Avbestill billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Cancel ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Er du sikker p� at du vil avbestille denne billetten?")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Are you sure you wish to delete this ticket?")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Nei, det ville blitt en feil")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("No, this would be a mistake")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ja, jeg trenger den ikke")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Yes, I don't need it")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lag ny klan")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Create new clan")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Klannavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Clan name")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Klan passord (for � bli med i klanen)")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Clan password (to join the clan)")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lag klan")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Create clan")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukernavn allerede i bruk")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Username already in use")."'");

