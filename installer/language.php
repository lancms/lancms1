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

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Forhåndsbestilt billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Preordered ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Forhåndsbetalt billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Prepaid ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Pris på billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Price of ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Navn på billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Name of ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Navn på arrangement")."'
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

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kjønn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Gender")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mann")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Male")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kvinne")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Female")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fødselsdag")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Birthday")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fødselsmåned")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Birthmonth")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fødselsår")."'
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

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Søk etter bruker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Search user")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tilbake til grupper")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Back to groups")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Spørsmål")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Questions")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Crew")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Crews")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Vis søknader")."'
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

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fornavn må settes")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Firstname must be set")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Etternavn må fylles inn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Lastname must be set")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Du må spesifisere hvilket kjønn du har")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("You have to specify your gender")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Postnummer må være et nummer")."'
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

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Spørsmål")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("FAQ")."' AND string = '".db_escape("Question")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til spørsmål")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("FAQ")."' AND string = '".db_escape("Add question")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukere kan opprette klaner")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("users_may_create_clan")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Registrering, fornavn påkrevd")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("register_firstname_required")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Registrering, etternavn påkrevd")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("register_lastname_required")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukere kan registrere seg")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("users_may_register")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukerinformasjon, fødselsdag påkrevd")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("userinfo_birthday_required")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukerinfo, fødselsår påkrevd")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("userinfo_birthyear_required")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukerinfo, kjønn påkrevd")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globalconfigoption")."' AND string = '".db_escape("userinfo_gender_required")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukerinfo, adresse påkrevd")."'
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

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Økonomi")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Economy")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Hovedkonto")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Main account")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kontonummer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Accountnumber")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Beløp")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Amount")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kontobeskrivelse")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Account description")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ny konto")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("New account")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lagre endringer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Save changes")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Nytt spørsmål")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("New question")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tekstspørsmålsfelt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Text answer-field")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Nedtrekksmeny")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Dropdown")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Avkrysningsboks")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Checkbox")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til nytt spørsmål")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Add new question")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Søknad fra:")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Application from:")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tilbake til listen")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Back to list")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Søk crew")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe")."' AND string = '".db_escape("Apply as crew")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Crew")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Crew")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Svar")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabeadmin")."' AND string = '".db_escape("Response")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Spørsmål")."'
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

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("forhåndsbestilt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("preorder")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Rediger billettype")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Edit tickettype")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Beklager, for mange brukere. Forsøk å begrense søket ditt mer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("login")."' AND string = '".db_escape("Sorry, too many users, try to narrow down the search")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ikke besvart")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe_crewprefs")."' AND string = '".db_escape("WannabeCrewListPreference")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fødselsår må settes")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Birthyear has to be set")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fødselsdag må settes")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Birthday has to be set")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fødselsmåned må settes")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Birthmonth has to be set")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Vennligst logg inn for å søke som crew")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe")."' AND string = '".db_escape("Please login to apply for crew")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Søker")."'
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

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kjøp billett")."'
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

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Plasser på kartet")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Place on map")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Oppdater kart")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Update map")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Plassregistrering ikke aktivert ennå")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Seating not enabled yet")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Søk etter bruker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Search user")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Avbestill billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Cancel ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Er du sikker på at du vil avbestille denne billetten?")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Are you sure you wish to delete this ticket?")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Nei, det ville blitt en feil")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("No, this would be a mistake")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ja, jeg trenger den ikke")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Yes, I don't need it")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lag ny klan")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Create new clan")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Klannavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Clan name")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Klan passord (for å bli med i klanen)")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Clan password (to join the clan)")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lag klan")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Create clan")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukernavn allerede i bruk")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Username already in use")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Slett søknad")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe")."' AND string = '".db_escape("Delete application")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Betalt?")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Paid?")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Rediger systemmeldinger")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("static")."' AND string = '".db_escape("Edit system message")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mine grupper")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("My groups")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Bruker allerede medlem av gruppen")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("User already member of group")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Navn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("crewlist")."' AND string = '".db_escape("Name")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Nick")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("crewlist")."' AND string = '".db_escape("Nick")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("E-post")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("crewlist")."' AND string = '".db_escape("EMail")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mobil")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("crewlist")."' AND string = '".db_escape("Cellphone")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tilgang")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("crewlist")."' AND string = '".db_escape("Access")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Denne plassen er tilgjengelig")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("seatmap_table")."' AND string = '".db_escape("This seat is available")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ta plass")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("seatmap_table")."' AND string = '".db_escape("Take seat")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ledig")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("seatmap")."' AND string = '".db_escape("Free")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Denne plassen er beskyttet av passord. Hvis du kjenner til passordet kan du ta den")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("seatmap_table")."' AND string = '".db_escape("This seat is password-protected. If you know the password, you can take it")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Er du sikker på at du vil slette din crewsøknad?")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe")."' AND string = '".db_escape("Are you sure you want to delete your crewapplication?")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ja, jeg er sikker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe")."' AND string = '".db_escape("Yes, I'm sure")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Nei, jeg ønsker fortsatt å søke crew")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("wannabe")."' AND string = '".db_escape("No, I still wish to apply for crew")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("brukt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("used")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("klan")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("clan")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Søk eier")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Search owner")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Sett gruppetilgang")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("seatadmin")."' AND string = '".db_escape("Set groupaccess")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Denne plassen er beskyttet av gruppetilgang. Du er medlem av en gruppe med tilgang. Halleluja!")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("seatmap_table")."' AND string = '".db_escape("This seat is protected by group. You are a member of a group with access. Halleluja!")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fant for mange brukere. Vennligst spesifiser")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Found to many users matching, please specify")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Klannavn er allerede i bruk. Vennligst spesifiser et annet navn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Clanname is already in use. Please choose another name")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Compoadmin")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Compoadmin")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Componavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("compoadmin")."' AND string = '".db_escape("Compo name")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Antall spillere per klan")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Number of players per clan")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til compo")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("compoadmin")."' AND string = '".db_escape("Add compo")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Vennligst legg inn navnet på klanen!")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("groups")."' AND string = '".db_escape("Please provide the name of the clan!")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Aktiver composystem")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventconfigoption")."' AND string = '".db_escape("enable_composystem")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Antall klaner/spillere per runde")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Number of clans/players per round")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Compopåmelding")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Composignup")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Påmelding")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("compos")."' AND string = '".db_escape("Signup")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mobil")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Cellphone")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("valgfritt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("optional")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mobiltelefon skal være 8 tegn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Cellphone is supposed to be eight digits")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Passord stemte ikke med hverandre")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Passwords does not match")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Registrer bruker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Register user")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Vis logger")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("View logs")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Siste 30 loggrader")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Last 30 logentries")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Logg")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Log")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tid")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Timestamp")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Bruker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("User")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Loggtype")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Logtype")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ny")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("New")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Gammel")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Old")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("IP")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("IP")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Host")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Host")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("URL")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("URL")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Bruker innlogget")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("User logged in")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Bruker utlogget")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("User logged out")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Innlogget")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Logged in")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Registrert bruker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Registered user")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Utlogget")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Logged out")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Detaljer for loggrad")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Details for logentry ")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Info innlagt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Info entered")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("BrukerID")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("UserID")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukernavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Username")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Passord")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Password")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Epost")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Email")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fornavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Firstname")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Etternavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Lastname")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kjønn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Gender")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fødselsdag")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Birthday")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fødselsmåned")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Birthmonth")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fødselsår")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Birthyear")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Adresse")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Address")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Postnummer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Postnumber")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mobil")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Cellphone")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Feilet innlogging")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Failed login")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Meld meg på")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("compos")."' AND string = '".db_escape("Sign me up")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Meld meg av")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("compos")."' AND string = '".db_escape("Remove me")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billett avbestilt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Ticket canceled")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billett bestilt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Ticket ordered")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billettdetaljer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Ticketdetails")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billettype")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Tickettype")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Antall billetter")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Number of tickets")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("BillettID")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("TicketID")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Eier")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Owner")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Status")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Status")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Betalt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Paid")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Plassering")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Seating")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Bytt passord")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Change password")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Bytt passord")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo")."' AND string = '".db_escape("Change password")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Nytt passord")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo")."' AND string = '".db_escape("New password")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Bekreft nytt passord")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo")."' AND string = '".db_escape("Confirm new password")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Passord endret")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo")."' AND string = '".db_escape("Password changed")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Endre passord")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Changed password")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Passorddetaljer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Password details")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ny MD5")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("New MD5")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Gammel MD5")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Old MD5")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukeradministrasjon")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("User administration")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Vis alle brukere")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("View all users")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Søk")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("Search")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukeradministrasjon")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("User administration")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Søk etter bruker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Search user")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tilbake til søk")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("Back to search")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("Ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Navn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("Name")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Addresse")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("Adress")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("E-post")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("E-mail")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fødselsdag")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("Birthday")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ikke betalt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("Not paid")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Betalt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("Paid")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ankomst")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Arrival")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Har plass")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("Seated")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Har ikke plass")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("Not seated")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("I døra med PC")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("onsite-computer")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("I døra uten PC")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("onsite-visitor")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til ny billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("Add new ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til billett til bruker")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("Add ticket to user")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Påmelding er avsluttet")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("compoadmin")."' AND string = '".db_escape("Signup is closed")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Påmelding er åpen")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("compoadmin")."' AND string = '".db_escape("Signup is open")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kampadmin (avslutt påmelding for å aktivere)")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("compoadmin")."' AND string = '".db_escape("Match admin (close signup to enable)")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kampadmin")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("compoadmin")."' AND string = '".db_escape("Match admin")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Sett opp første kamp med tilfeldig rekkefølge")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("compoadmin")."' AND string = '".db_escape("Randomize first match")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Solgte billetter av type (totalt/plassert/betalt)")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Sold tickets of type (total/seated/paid)")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Påmelding er stengt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("compos")."' AND string = '".db_escape("Signup has closed")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Slett billett")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("arrival")."' AND string = '".db_escape("Delete ticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("ja")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("yes")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("nei")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("no")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Endre billetts betaltstatus")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Changed ticket paystatus")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("I døra-billett bestilt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Onsiteticket ordered")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Betalt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketorder")."' AND string = '".db_escape("Paid")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Registrert bruker (useradmin)")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("Registered user (useradmin)")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mobiltelefon skal være et nummer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("register")."' AND string = '".db_escape("Cellphone is supposed to be a number")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Forhandler")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Reseller")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Aktiver forhandlerstøtte")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventconfigoption")."' AND string = '".db_escape("enable_reseller")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Navn på arrangement")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("Name of event")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til arrangement")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("Add event")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Rediger brukerinformasjon")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Edit userinfo")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukernavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("Username")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fornavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("First name")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Etternavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("Last name")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fødselsdag")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("Birthday")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Januar")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("January")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Februar")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("February")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mars")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("March")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("April")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("April")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mai")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("May")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Juni")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("June")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Juli")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("July")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("August")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("August")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("September")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("September")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Oktober")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("October")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("November")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("November")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Desember")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("December")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Adresse")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("Streetadress")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Postnummer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("Postnumber")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kjønn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("Gender")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mobilnummer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("Cellphone")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Lagre")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo")."' AND string = '".db_escape("Save")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Gutt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("Male")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Jente")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo_prefs")."' AND string = '".db_escape("Female")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Oversikt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Main")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kvitteringer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Receipts")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Regnskap")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Bookkeeping")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Kontoer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Accounts")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fra")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("From")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tid (YYYY-MM-DD HH:MM)")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Timestamp (YYYY-MM-DD HH:MM)")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Beskrivelse")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Description")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Legg til kvittering")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("economy")."' AND string = '".db_escape("Add receipt")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Forhåndsbetalt billett hos forhandler")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("Prepaid ticket reseller")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Rediger mine innstillinger")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Edit my preferences")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tillat andre å se min e-post-adresse")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("edituserinfo")."' AND string = '".db_escape("Allow other users to see my mailadress")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ikke tilgang til å redigere brukerinformasjon")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("index")."' AND string = '".db_escape("Not access to edit userinfo")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Liste over grupper med globale tilganger")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("List groups with global access")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Send SMS")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("SMS")."' AND string = '".db_escape("Send SMS")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Vis liste")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("listing")."' AND string = '".db_escape("Show list")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Privat")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("Private")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Deltager-tilgang")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("globaladmin")."' AND string = '".db_escape("Attendee-access")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tillatt deltagelse")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Allow attendee")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Ikke tillat deltagelse")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("eventadmin")."' AND string = '".db_escape("Disallow attendee")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("forhåndsbetalt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("ticketadmin")."' AND string = '".db_escape("prepaid")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Innlogging vellykket")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("log_login__success")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Billett kjøpt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("log_ticketorder__buyticket")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Innlogging")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("log_login__login")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Utlogging")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("log_login__logout")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Nytt passord satt")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("log_edituser__setNewPass")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Feilet innlogging: feil passord")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("logs")."' AND string = '".db_escape("log_login__failed_password")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Rediger brukerinfo")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("Edit userinfo")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("List alle brukere")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("List of all users")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tilbake til brukeradministrasjon")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("Back to user administration")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("ID")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("ID")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Brukernavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("Username")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Fornavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("Firstname")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Etternavn")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("Lastname")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("E-post")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("Email")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Adresse")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("Address")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Postnummer")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("Postnumber")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Mobil")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("Cellphone")."'");

db_query("UPDATE ".$sql_prefix."_lang SET translated = '".db_escape("Tilbake")."'
WHERE language = '".db_escape("norwegian")."' AND module = '".db_escape("useradmin")."' AND string = '".db_escape("Back")."'");

