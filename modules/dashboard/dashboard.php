<?php

$acl = acl_access("dashboard", "", $sessioninfo->eventID);
if($acl == 'No') die(_("No access to dashboard"));


$action = $_GET['dashboard'];

if(empty($action)) {
	$design_head .= "<meta http-equiv='refresh' content='10'>";
	$content .= "<table border=1>";
	$content .= "<tr><th>";
	$content .= _("Tickets sold");
	$content .= "</th><th>";
	$content .= _("Kiosk sales");
	$content .= "</th></tr>";
	$content .= "<tr><td>";
	// Security
	$content .= "<b>"._("Money earned")."</b>: ";
	$qCountTicketsPrice = db_query("SELECT SUM(price) AS price FROM ".$sql_prefix."_tickets t 
	JOIN ".$sql_prefix."_ticketTypes tt 
	ON tt.ticketTypeID=t.ticketType 
	WHERE t.paid='yes' AND tt.eventID = '$sessioninfo->eventID'");
	$rCountTicketsPrice = db_fetch($qCountTicketsPrice);
	if($rCountTicketsPrice->price > 0) $content .= $rCountTicketsPrice->price;
	else $content .= "0";
	$content .= "<br />";
	$content .= "<b>"._("Tickets sold")."</b>: ";
	$qCountTicketsSold = db_query("SELECT COUNT(*) AS amount FROM ".$sql_prefix."_tickets t
	JOIN ".$sql_prefix."_ticketTypes tt
	ON tt.ticketTypeID=t.ticketType
	WHERE t.paid='yes' AND tt.eventID = '$sessioninfo->eventID'");
	$rCountTicketsSold = db_fetch($qCountTicketsSold);
	$content .= $rCountTicketsSold->amount;
	$content .= "</td><td>";
	// Kiosk
	$content .= "<b>"._("Sold for (total)")."</b>: ";
	$qCheckKiosk = db_query("SELECT SUM(totalPrice) AS totalPrice FROM ".$sql_prefix."_kiosk_sales WHERE eventID = '$sessioninfo->eventID'");
	$rCheckKiosk = db_fetch($qCheckKiosk);
	$content .= $rCheckKiosk->totalPrice;
	$qCheckCreditSale = db_query("SELECT SUM(totalPrice) AS creditSale FROM ".$sql_prefix."_kiosk_sales WHERE eventID = '$sessioninfo->eventID' AND credit = 1");
	$rCheckCreditSale = db_fetch($qCheckCreditSale);
	$content .= "<br /><b>"._("Creditsale")."</b>: ";
	$content .= $rCheckCreditSale->creditSale;
	$content .= "</td></tr>";
	$content .= "</table>";
}
