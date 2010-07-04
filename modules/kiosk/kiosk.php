<?php

$acl = acl_access("kiosk_sales", "", $sessioninfo->eventID);
if($acl == 'No') die("Error, no access!");

$action = $_GET['action'];

if(empty($action)) {

	$design_head .= '<script type="text/javascript" src="inc/AJAX/ajax_suggest.js"></script>'."\n";
	$design_head .= '<link href="modules/kiosk/suggest.css" rel="stylesheet" type="text/css" />';

	$content .= "<table>";
	$content .= "<tr><td colspan=2>";
	$content .= '<div id="suggestcontent" onclick="hideSuggestions();">';
	$content .= "<form method=POST action=?module=kiosk&action=addWare>";
	$content .= "<input type=text name=ware id=ware onkeyup=\"handleKeyUp(event)\" value='' />";
	$content .= "<div id='scroll'><div id='suggest'></div></div>";
	$content .= "<input type=submit value='".lang("Add", "kiosk")."'>\n";
	$content .= "</form></div>\n\n";
	$content .= "</td></tr>";

	$content .= "<tr><td>";
	$content .= "<table border=1>";
	$qFindBasket = db_query("SELECT * FROM ".$sql_prefix."_kiosk_shopbasket WHERE sID = '$sessioninfo->sID'");
	while($rFindBasket = db_fetch($qFindBasket)) {
		$content .= "<tr><td>";
		$qFindWare = db_query("SELECT * FROM ".$sql_prefix."_kiosk_wares WHERE ID = '$rFindBasket->wareID'");
		$rFindWare = db_fetch($qFindWare);
		$content .= $rFindWare->name;
		$content .= "</td><td>";
		$content .= $rFindBasket->amount; // FIXME: Needs to have menues and crewprices
		$content .= "</td><td>";
		$price = kiosk_item_price($rFindBasket->wareID) * $rFindBasket->amount;
		$content .= $price;
		$total_price = $total_price + $price;
		$content .= "</td></tr>";
	}
	$content .= "</table>";
	$content .= "</td><td>";
	$content .= "<font size=36 color=red>$total_price</font>";
	$content .= "<form method=POST action=?module=kiosk&action=sell>";
	$content .= "<input type=submit value='".lang("SELL", "kiosk")."'>";
	$content .= "</form>\n\n";
	$content .= "</td></tr>";
	$content .= "</table>";


}



elseif($action == "addWare") {

	$ware = $_POST['ware'];

	$qFindBarcode = db_query("SELECT * FROM ".$sql_prefix."_kiosk_barcodes WHERE barcode = '".db_escape($ware)."'");
	if(db_num($qFindBarcode) != 0) {
		$rFindBarcode = db_fetch($qFindBarcode);
		$wareID = $rFindBarcode->wareID;
	}
	// FIXME: Should have a AJAX search for wares too


	$qFindBasket = db_query("SELECT * FROM ".$sql_prefix."_kiosk_shopbasket 
		WHERE sID = '$sessioninfo->sID'
		AND wareID = $wareID");

	if(db_num($qFindBasket) == 0) {
		db_query("INSERT INTO ".$sql_prefix."_kiosk_shopbasket
			SET sID = '$sessioninfo->sID',
			wareID = $wareID");
	} // End if_db_num == 0
	else {
		db_query("UPDATE ".$sql_prefix."_kiosk_shopbasket
			SET amount = amount + 1
			WHERE sID = '$sessioninfo->sID'
			AND wareID = $wareID");
	} // End else
	header("Location: ?module=kiosk");
} // End addWare

elseif($action == "sell") {
	$qCreateSale = db_query("INSERT INTO ".$sql_prefix."_kiosk_sales 
		SET salesPerson = '$sessioninfo->userID',
		saleTime = '".time()."',
		eventID = '$sessioninfo->eventID'");
	$qSaleID = db_query("SELECT ID FROM ".$sql_prefix."_kiosk_sales 
		WHERE salesPerson = '$sessioninfo->userID' 
		AND eventID = '$sessioninfo->eventID'
		ORDER BY ID DESC LIMIT 0,1");
	$rSaleID = db_fetch($qSaleID);
	$saleID = $rSaleID->ID;


	$qFindBasket = db_query("SELECT * FROM ".$sql_prefix."_kiosk_shopbasket WHERE sID = '$sessioninfo->sID'");
	while($rFindBasket = db_fetch($qFindBasket)) {
		db_query("INSERT INTO ".$sql_prefix."_kiosk_saleitems 
			SET saleID = '$saleID',
			wareID = '$rFindBasket->wareID',
			amount = '$rFindBasket->amount'
		");
		db_query("DELETE FROM ".$sql_prefix."_kiosk_shopbasket
			WHERE sID = '$sessioninfo->sID'
			AND wareID = '$rFindBasket->wareID'");
		$total_price = $total_price + (kiosk_item_price($rFindBasket->wareID) * $rFindBasket->amount);
	} // End while
	db_query("UPDATE ".$sql_prefix."_kiosk_sales SET totalPrice = '$total_price' WHERE ID = '$saleID'");
	header("Location: ?module=kiosk");
	
	
} // End action == sell
