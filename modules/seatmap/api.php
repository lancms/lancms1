<?php
if(ob_get_length()) ob_clean();
header('Expires: Mon, 26 Jul 2000 05:00 00 GMT');
header('Last-Modified: '. gmdate('D, d M Y H:i:s') . 'GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json');

$eventID = $sessioninfo->eventID;
$eventinfoQ = db_query("SELECT * FROM ".$sql_prefix."_events WHERE ID = '$eventID'");
$eventinfo = db_fetch($eventinfoQ);
$export['eventinfo']['name'] = $eventinfo->eventname;

if(config("seating_enabled", $eventID)) $export['seating_enabled'] = true;
else $export['seating_enabled'] = false;

$export['seats']['open'] = 0;
$export['seats']['not_open'] = "0";
$export['seats']['password_reserved'] = 0;


$seatQ = db_query("SELECT type,COUNT(*) AS amount FROM ".$sql_prefix."_seatReg WHERE eventID = '$eventID' AND type IN ('d','n', 'p') GROUP BY type");
while($seatR = db_fetch($seatQ)) {
	if($seatR->type == 'd') $export['seats']['open'] = $seatR->amount;
	if($seatR->type == 'n') $export['seats']['not_open'] = $seatR->amount;
	if($seatR->type == 'p') $export['seats']['password_reserved'] = $seatR->amount;
}
$seatsTakenQ = db_query("SELECT COUNT(*) AS amount FROM ".$sql_prefix."_seatReg_seatings WHERE eventID = '$eventID'");
$seatsTakenR = db_fetch($seatsTakenQ);
$export['seats_taken'] = $seatsTakenR->amount;

echo json_encode($export);

#    echo json_encode(map_array($products, function(Lancms\KioskProduct $product) {
#        return array(
#            'id' => $product->getId(),
#            'name' => $product->getName(),
#            'description' => number_format($product->getPrice()) . ' kr',
#            'price' => $product->getPrice(),
#            'url' => '?module=kiosk&action=addWare&ware=' . $product->getId(),
#        );
#    }));
#} // End action = searchitems
