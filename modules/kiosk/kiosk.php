<?php

$acl = acl_access("kiosk_sales", "", $sessioninfo->eventID);
if($acl == 'No') die("Error, no access!");

$action = $_GET['action'];

if(empty($action)) {

	$content .= "<table>";
	$content .= "<tr><td colspan=2>";
	$content .= "<form method=POST action=?module=kiosk&action=addWare>";
	$content .= "<input type=text name=ware>";
	$content .= "<input type=submit value='".lang("Add", "kiosk")."'>\n";
	$content .= "</form>\n\n";
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
		$price = $rFindWare->price * $rFindBasket->amount;
		$content .= $price;
		$total_price = $total_price + $price;
		$content .= "</td></tr>";
	}
	$content .= "</table>";
	$content .= "</td><td>";
	$content .= "<font size=36 color=red>$total_price</font>";
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

