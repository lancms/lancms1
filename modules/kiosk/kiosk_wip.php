<?php

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$acl = acl_access("kiosk_sales", "", $sessioninfo->eventID);
if($acl == 'No') {
    $content .= "Error, no access!";
    return;
}

$kiosk = new \Lancms\Kiosk\LanKiosk();
$kioskSession = \Lancms\Kiosk\LanKioskSession::create(null, null);
$kioskGui = new \Lancms\Kiosk\KioskGui();
$kioskGui->prepare($kioskSession);

$action = $request->query->getAlnum("action");

if(!$request->query->has("action")) {

    $kioskGui->front();

} // End no action

elseif($action == "endSession") {

    $kioskGui->endSession();

} // End endSession

elseif($action == "addWare") {

    $kioskGui->addWare();

} // End addWare

elseif($action == "removeWare") {

    $kioskGui->removeWare();

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
