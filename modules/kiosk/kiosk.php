<?php

$acl = acl_access("kiosk_sales", "", $sessioninfo->eventID);
if($acl == 'No') die("Error, no access!");

$action = $_GET['action'];

if(empty($action)) {

	$design_head .= '<script type="text/javascript" src="inc/AJAX/ajax_suggest.js"></script>'."\n";
#	$design_head .= '<script type="text/javascript"> document.forms[\'barfield\'].ware.focus(); </script>';


#	$design_head .= '<link href="modules/kiosk/suggest.css" rel="stylesheet" type="text/css" />';

	$content .= "<table>";
	$content .= "<tr><td colspan=2>";
	$content .= "<form method='POST' action='?module=kiosk&action=addWare' name='barfield'>\n";
	$content .= "<input type=text name='ware' id='ware' tabindex=1 onkeyup=\"suggest();\" autocomplete=\"off\"/>";
	$content .= "<div id='suggest'></div>";
	$content .= "<input type=submit value='".lang("Add", "kiosk")."'>\n";
	$content .= "</form>\n\n";
	$content .= "<script type='text/javascript' language='javascript'>document.forms['barfield'].elements['ware'].focus()</script>\n";
	$content .= "</td><td>";
	if($sessioninfo->kioskSaleTo > 1) {
		$content .= _("Currently selling to:");
		$content .= " ".display_username($sessioninfo->kioskSaleTo);
		$content .= "<form method=POST action='?module=kiosk&action=addWare' name='resetSaleTo'>\n";
		$content .= "<input type='hidden' name='ware' value='UID1'>";
		$content .= "<input type=submit value='"._("Remove user")."'>";
		$content .= "</form>\n";
	} // End kioskSaleTo
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
		$total_warecount = $total_warecount + 1;
		$content .= "</td><td>";
		$content .= "<a href=?module=kiosk&action=addWare&ware=$rFindBasket->wareID><img src='inc/images/plus-15px.png' border=0 alt='".lang("Add one more item", "kiosk")."' /></a> ";
		$content .= "<a href=?module=kiosk&action=removeWare&ware=$rFindBasket->wareID><img src='inc/images/minus-15px.png' border=0 alt='".lang("Remove one item", "kiosk")."' /></a>";
		$content .= "</td></tr>";
	}
	$content .= "</table>";
	$content .= "</td><td>";
	if($total_warecount > 0) {
		$content .= "<font size=36 color=red>$total_price</font>";
		$content .= "<form method=POST action=?module=kiosk&action=sell>";
		$content .= "<input type=submit value='".lang("SELL", "kiosk")."'>";
	} // End if total_warecount > 0
	$content .= "</form>\n\n";
	$content .= "</td></tr>";
	$content .= "</table>";


}



elseif($action == "addWare") {

	$ware = $_REQUEST['ware'];

	$qFindBarcode = db_query("SELECT * FROM ".$sql_prefix."_kiosk_barcodes WHERE barcode = '".db_escape($ware)."'");
	if(db_num($qFindBarcode) != 0) {
		$rFindBarcode = db_fetch($qFindBarcode);
		$wareID = $rFindBarcode->wareID;
	}
	elseif(stristr($ware, "UID")) {
		$checkingUser =TRUE;
		$userID = str_replace("UID", "", $ware);

		if(user_exists($userID)) {
			db_query("UPDATE ".$sql_prefix."_session SET kioskSaleTo = '".db_escape($userID)."' WHERE 
				sID = '$sessioninfo->sID'");
		} // End if user_Exists
		else die(_("User not found!"));
	}
	else { // Assume we've used AJAX search, and that $ware is an ID
		$qFindWareID = db_query("SELECT * FROM ".$sql_prefix."_kiosk_wares WHERE ID = '".db_escape($ware)."'");
		if(db_num($qFindWareID) == 1) {
			$rFindWareID = db_fetch($qFindWareID);
			$wareID = $rFindWareID->ID;
		} // End if db_num == 1
	} // End else
	if(!$checkingUser) {
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
	} // End if checkingUser == FALSE
	header("Location: ?module=kiosk");
} // End addWare

elseif($action == "removeWare") {
	$ware = $_REQUEST['ware'];

	$qFindAmount = db_query("SELECT * FROM ".$sql_prefix."_kiosk_shopbasket WHERE 
		sID = '$sessioninfo->sID'
		AND wareID = '".db_escape($ware)."'
		");
	$rFindAmount = db_fetch($qFindAmount);
	if($rFindAmount->amount == 1) {
		db_query("DELETE FROM ".$sql_prefix."_kiosk_shopbasket WHERE sID = '$sessioninfo->sID' AND wareID = '".db_escape($ware)."'");
	} // End if amount = 1
	else {
		db_query("UPDATE ".$sql_prefix."_kiosk_shopbasket SET amount = amount - 1
			WHERE sID = '$sessioninfo->sID' AND wareID = '".db_escape($ware)."'");
	} // End else
	header("Location: ?module=kiosk");
} // End if action = removeWare
 
elseif($action == "sell") {
	$qCreateSale = db_query("INSERT INTO ".$sql_prefix."_kiosk_sales 
		SET salesPerson = '$sessioninfo->userID',
		saleTime = '".time()."',
		soldTo = '".$sessioninfo->kioskSaleTo."',
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
	db_query("UPDATE ".$sql_prefix."_session SET kioskSaleTo = '1' WHERE sID = '$sessioninfo->sID'");
	header("Location: ?module=kiosk");
	
	
} // End action == sell
