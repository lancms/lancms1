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

	// Details
	$content .= "<tr><td>";
	$qFindTicketDetails = db_query("SELECT COUNT(*) AS amount,tt.name AS name FROM ".$sql_prefix."_tickets t JOIN ".$sql_prefix."_ticketTypes tt ON t.ticketType = tt.ticketTypeID WHERE tt.eventID = '$sessioninfo->eventID' AND t.paid='yes' GROUP BY tt.name");
	while($rFindTicketDetails = db_fetch($qFindTicketDetails)) {
		$content .= "<br /><b>".$rFindTicketDetails->name."</b>: ";
		$content .= $rFindTicketDetails->amount;
#		$content .= "</li>";
	} // End while
	$content .= "</td><td>\n";
	$qFindKioskDetails = db_query("SELECT w.name,SUM(amount) AS amount FROM (".$sql_prefix."_kiosk_saleitems si LEFT JOIN ".$sql_prefix."_kiosk_sales s ON s.ID=si.saleID) LEFT JOIN ".$sql_prefix."_kiosk_wares w ON si.wareID=w.ID WHERE s.eventID = '$sessioninfo->eventID' GROUP BY w.name ORDER BY w.wareType,w.name");
#	$content .= "</td></tr>";
	while($rFindKioskDetails = db_fetch($qFindKioskDetails)) {
		$content .= "<br /><b>".$rFindKioskDetails->name."</b>: ";
		$content .= $rFindKioskDetails->amount;
	} // End while
	// End it
	$content .= "</td></tr>";
	$content .= "</table>";
}
