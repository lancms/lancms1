<?php
if(ob_get_length()) ob_clean();
header('Expires: Mon, 26 Jul 2000 05:00 00 GMT');
header('Last-Modified: '. gmdate('D, d M Y H:i:s') . 'GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json');
if($action == "searchitems") {
	$request = $_REQUEST['search'] ?? '';

    $kiosk = new Lancms\Kiosk();
    $products = $kiosk->search($request);

    echo json_encode(map_array($products, function(Lancms\KioskProduct $product) {
        return array(
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => number_format($product->getPrice()) . ' kr',
            'price' => $product->getPrice(),
            'url' => '?module=kiosk&action=addWare&ware=' . $product->getId(),
        );
    }));
} // End action = searchitems
