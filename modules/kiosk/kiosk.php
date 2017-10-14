<?php
use Lancms\Kiosk;
use Lancms\KioskSession;

$acl = acl_access("kiosk_sales", "", $sessioninfo->eventID);
if($acl == 'No') die("Error, no access!");

$action = $_GET['action'] ?? '';

$kiosk = new Kiosk();
$session = new KioskSession($kiosk, $sessioninfo->sID);

if(empty($action)) {

	// $design_head .= '<script type="text/javascript" src="inc/AJAX/ajax_suggest.js"></script>'."\n";
#	$design_head .= '<script type="text/javascript"> document.forms[\'barfield\'].ware.focus(); </script>';


#	$design_head .= '<link href="modules/kiosk/suggest.css" rel="stylesheet" type="text/css" />';

    $sellTo = null;

    if (($sellToId = $sessioninfo->kioskSaleTo) > 1) {
        $sellTo = [
            'id' => $sellToId,
            'nick' => display_username($sellToId),
        ];
    }

    $content .= $twigEnvironment->render('kiosk/gui.twig', [
        'error' => $_GET['error'] ?? null,
        'sellTo' => $sellTo,
        'cart' => [
            'items' => $session->getCartProducts(),
            'sum' => $session->getTotalSumPrice(),
        ],
        'creditDefault' => config("kiosk_userSale_credit_default", $sessioninfo->eventID),
    ]);

}

elseif($action == "addWare") {

	$ware = (int) $_REQUEST['ware'] ?? '';

    if (empty($ware)) {
        header('Location: ?module=kiosk&error=1');
        exit;
    }

	$qFindBarcode = db_query("SELECT * FROM ".$sql_prefix."_kiosk_barcodes WHERE barcode = '".db_escape($ware)."'");
	$qFindBadge = db_query("SELECT * FROM ".$sql_prefix."_membershipCard WHERE cardID = '".db_escape($ware)."' AND eventID IN ($sessioninfo->eventID, 1)");
	if(db_num($qFindBarcode) != 0) {
		$rFindBarcode = db_fetch($qFindBarcode);
		$wareID = $rFindBarcode->wareID;
	}
	elseif(stristr($ware, "UID1")) { // Reset to default userID
		$checkingUser =TRUE;
		$userID = 1;
//		$userID = str_replace("UID", "", $ware);
//
//		if(user_exists($userID)) {
//			db_query("UPDATE ".$sql_prefix."_session SET kioskSaleTo = '".db_escape($userID)."' WHERE
//				sID = '$sessioninfo->sID'");
//		} // End if user_Exists
//		else die(_("User not found!"));
   }
   elseif(db_num($qFindBadge) != 0) { //Badge found
		$checkingUser = TRUE;
		$rFindBadge = db_fetch($qFindBadge);
		$userID = $rFindBadge->userID;
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
			AND wareID = '$wareID'");

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

	elseif($checkingUser) {
		if(user_exists($userID)) {
			db_query("UPDATE ".$sql_prefix."_session SET kioskSaleTo = '".db_escape($userID)."' WHERE sID = '$sessioninfo->sID'");
		} // End if user exists

	}

	header("Location: ?module=kiosk");

} // End addWare

elseif($action == "removeWare") {
	$ware = (int) $_REQUEST['ware'] ?? '';

    if ($ware > 0) {
    	db_query("DELETE FROM ".$sql_prefix."_kiosk_shopbasket WHERE sID = '$sessioninfo->sID' AND wareID = '".db_escape($ware)."'");
    }

	header("Location: ?module=kiosk");
} // End if action = removeWare

elseif($action == "updateWare") {
	$ware = $_REQUEST['ware'] ?? '';
	$newAmount = (int) $_POST['amount'] ?? 0;

    if ($newAmount < 1) {
		db_query(sprintf(
            'DELETE FROM %s_kiosk_shopbasket WHERE sID = \'%s\' AND wareID = %d',
            db_prefix(),
            db_escape($sessioninfo->sID),
            db_escape($ware)
        ));
	} else {
		db_query(sprintf(
            'UPDATE %s_kiosk_shopbasket SET amount = %d WHERE sID = \'%s\' AND wareID = %d',
            db_prefix(),
            db_escape($newAmount),
            db_escape($sessioninfo->sID),
            db_escape($ware)
        ));
	} // End else
	header("Location: ?module=kiosk");
} // End if action = removeWare

elseif($action == "sell") {
	if($_POST['credit'] == 'yes' AND $sessioninfo->kioskSaleTo > 1) $credit = 1;
	else $credit = 0;
	$qCreateSale = db_query("INSERT INTO ".$sql_prefix."_kiosk_sales
		SET salesPerson = '$sessioninfo->userID',
		saleTime = '".time()."',
		soldTo = '".$sessioninfo->kioskSaleTo."',
		credit = '$credit',
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
