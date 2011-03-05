<?php

$acl = acl_access("kiosk_admin", "", $sessioninfo->eventID);

if($acl == 'No') die("No access!");

$action = $_GET['action'];

if(empty($action)) {

	$content .= "<ul>";
	$content .= "<li><a href=?module=kioskadmin&action=wareTypes>".lang("Admin waretypes")."</a></li>\n";
	$content .= "<li><a href=?module=kioskadmin&action=wares>".lang("Admin wares")."</a></li>\n";
	$content .= "<li><a href=?module=kioskadmin&action=credit>"._("Admin credit")."</a></li>\n";
	$content .= "</ul>";

	$content .= "<ul>";
	$content .= "<li><a href=?module=kioskadmin&action=printBarcodes>".lang("Print barcodes")."</a></li>\n";
	$content .= "</ul>";
}


elseif($action == "wareTypes") {
	$qWareTypes = db_query("SELECT * FROM ".$sql_prefix."_kiosk_waretypes");
	
	$content .= "<table>";
	while($rWareTypes = db_fetch($qWareTypes)) {
		$content .= "<tr>";
		$content .= "<td class='tdLink' onClick='location.href=\"?module=kioskadmin&action=editWareType&wareType=$rWareTypes->ID\"'>";
		$content .= $rWareTypes->typeName;
		$content .= "</td></tr>";
	} // End while

	$content .= "</table>";

	$content .= "<br /><form method=POST action=?module=kioskadmin&action=addWareType>\n";
	$content .= "<input type=text name='name'>\n";
	$content .= "<input type=submit value='".lang("Add new waretype", "kioskadmin")."'>\n";
	$content .= "</form>";

}

elseif($action == "addWareType" && !empty($_POST['name'])) {
	$name = $_POST['name'];
	$qFindDuplicate = db_query("SELECT * FROM ".$sql_prefix."_kiosk_waretypes WHERE typeName = '".db_escape($name)."'");
	if(db_num($qFindDuplicate) == 0) {
		db_query("INSERT INTO ".$sql_prefix."_kiosk_waretypes SET typeName = '".db_escape($name)."'");
	}
	$log_new['name'] = $name;
	log_add("kioskadmin", "addWareType", serialize($log_new));
	header("Location: ?module=kioskadmin&action=wareTypes");
} // End action == addWareType

elseif($action == "editWareType" && !empty($_GET['wareType'])) {
	// FIXME: Create editing, disabling and deletion of wareTypes
}

elseif($action == "wares") {
	$qFindWares = db_query("SELECT wares.ID,wares.name,types.typeName,wares.price FROM ".$sql_prefix."_kiosk_wares wares JOIN ".$sql_prefix."_kiosk_waretypes types 
		ON wares.wareType=types.ID");

	$content .= "<table>";
	$content .= "<tr><th>".lang("Name", "kioskadmin")."</th>";
	$content .= "<th>".lang("Type", "kioskadmin")."</th>";
	$content .= "<th>".lang("Barcodes", "kioskadmin")."</th>";
	$content .= "<th>".lang("Price (normal)")."</th>";
	$content .= "</tr>";
	while($rFindWares = db_fetch($qFindWares)) {
		$content .= "<tr>";
		$onclick = "class='tdLink' onClick='location.href=\"?module=kioskadmin&action=editWare&wareID=$rFindWares->ID\"'";
		$content .= "<td $onclick>";
		$content .= $rFindWares->name;
		$content .= "</td><td $onclick>";
		$content .= $rFindWares->typeName;
		$content .= "</td><td $onclick>";
		$qFindBarcodes = db_query("SELECT * FROM ".$sql_prefix."_kiosk_barcodes WHERE wareID = '$rFindWares->ID'");
		$content .= db_num($qFindBarcodes);
		$content .= "</td><td $onclick>";
		$content .= $rFindWares->price;
		$content .= "</td></tr>";
	} // End while
	$content .= "</table>";

	$content .= "<form method=POST action='?module=kioskadmin&action=addWare'>\n";
	$content .= "<input type=text name='name'>\n";
	$content .= "<br /><select name=wareType>\n";
	$qFindTypes = db_query("SELECT * FROM ".$sql_prefix."_kiosk_waretypes");
	while($rFindTypes = db_fetch($qFindTypes)) {
		$content .= "<option value='$rFindTypes->ID'>$rFindTypes->typeName</option>";
	} // End while
	$content .= "</select>\n";
	$content .= "<br /><input type=submit value='".lang("Add ware", "kioskadmin")."'>";
	$content .= "</form>\n";

} // End action == wares


elseif($action == "addWare" && !empty($_POST['name'])) {
	$name = $_POST ['name'];
	$wareType = $_POST['wareType'];

	db_query("INSERT INTO ".$sql_prefix."_kiosk_wares SET name = '".db_escape($name)."', wareType = '".db_escape($wareType)."'");

	$log_new['wareType'] = $wareType;
	$log_new['name'] = $name;
	log_add("kioskadmin", "addWare", serialize($log_new));

	header("Location: ?module=kioskadmin&action=wares");

} // End action = addWare

elseif($action == "editWare" && !empty($_GET['wareID'])) {
	$wareID = $_GET['wareID'];
	$qGetWare = db_query("SELECT * FROM ".$sql_prefix."_kiosk_wares WHERE ID = '".db_escape($wareID)."'");
	$rGetWare = db_fetch($qGetWare);

	$content .= "<form method=POST action='?module=kioskadmin&action=doEditWare&wareID=$wareID'>";
	$content .= "<input type=text name='name' value='$rGetWare->name'> ".lang("Name", "kioskadmin")."\n";
        $content .= "<br /><select name=wareType>\n";
        $qFindTypes = db_query("SELECT * FROM ".$sql_prefix."_kiosk_waretypes");
        while($rFindTypes = db_fetch($qFindTypes)) {
                $content .= "<option value='$rFindTypes->ID'";
		if($rFindTypes->ID == $rGetWare->wareType) $content .= " SELECTED";
		$content .= ">$rFindTypes->typeName</option>";
        } // End while
        $content .= "</select> ".lang("Ware type", "kioskadmin")."\n";
	$content .= "<br /><input type=text size=4 name=price value='$rGetWare->price'> ".lang("Price", "kioskadmin")."\n";

	$content .= "<br /><input type=submit value='".lang("Save", "kioskadmin")."'>";
	$content .= "</form>";

	$content .= "<br /><hr><br />";
	$content .= lang("Barcodes", "kioskadmin");
	$qFindBarcodes = db_query("SELECT * FROM ".$sql_prefix."_kiosk_barcodes WHERE wareID = '$wareID'");
	while($rFindBarcodes = db_fetch($qFindBarcodes)) {
		$content .= "<br />".$rFindBarcodes->barcode;
	} // End while
	
	$content .= "<form method=POST action='?module=kioskadmin&action=addBarcode&wareID=$wareID>'\n";
	$content .= "<br /><input type=text name=barcode>\n";
	$content .= "<br /><input type=submit value='".lang("Add barcode", "kioskadmin")."'>\n";
	$content .= "</form>";

}

elseif($action == "doEditWare" && !empty($_GET['wareID'])) {
	$wareID = $_GET['wareID'];
	$name = $_POST['name'];
	$wareType = $_POST['wareType'];
	$price = $_POST['price'];
	
	db_query("UPDATE ".$sql_prefix."_kiosk_wares SET
		wareType = '".db_escape($wareType)."',
		name = '".db_escape($name)."',
		price = '".db_escape($price)."'

		WHERE ID = '".db_escape($wareID)."'");

	log_add("kioskadmin", "doEditWare", serialize($_POST));

	header("Location: ?module=kioskadmin&action=wares");
} // End action == doEditWare

elseif($action == "addBarcode" && !empty($_GET['wareID'])) {
	$wareID = $_GET['wareID'];
	$barcode = $_POST['barcode'];
	
	$qFindBarcode = db_query("SELECT * FROM ".$sql_prefix."_kiosk_barcodes WHERE barcode = '".db_escape($barcode)."'");
	if(db_num($qFindBarcode) == 0) {
		db_query("INSERT INTO ".$sql_prefix."_kiosk_barcodes 
			SET wareID = '".db_escape($wareID)."', 
			barcode = '".db_escape($barcode)."'");
		$log_new['wareID'] = $wareID;
		$log_new['barcode'] = $barcode;
		log_add("kioskadmin", "addbarcode", serialize($log_new));
	} // End db_num()

	header("Location: ?module=kioskadmin&action=editWare&wareID=$wareID");
} // End action == addBarcode


elseif($action == "credit") {
	$qFindCredits = db_query("SELECT u.nick,u.ID,SUM(totalPrice) AS totalPrice FROM ".$sql_prefix."_kiosk_sales ks JOIN ".$sql_prefix."_users u ON u.ID=ks.soldTo WHERE ks.credit = 1 AND creditPaid = 0 GROUP BY u.nick ORDER BY totalPrice DESC");

	$content .= "<table>";
	while($rFindCredits = db_fetch($qFindCredits)) {
		$content .= "<tr><td>";
		$content .= user_profile($rFindCredits->ID);
		$content .= "</td><td>\n";
		$content .= $rFindCredits->totalPrice;
	} // End while
	$content .= "</table>\n\n";
} // End action = credit
