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
	$content .= "<p><a href=\"?module=kioskadmin\">" . _("Back to kioskadmin overview") . "</a></p>";
	$content .= "<h2>" . _("Kiosk Ware types") . "</h2>";
	$qWareTypes = db_query("SELECT * FROM ".$sql_prefix."_kiosk_waretypes");

	if (db_num($qWareTypes) > 0) {
		$content .= "<table class='better-table kioskware-types'><tr><th style='width: 80%'>" . _("Name") . "</th><th>" . _("Actions") . "</th></tr>";
		while($rWareTypes = db_fetch($qWareTypes)) {
			$content .= "<tr>";
			$content .= "<td class='tdLink' onClick='location.href=\"?module=kioskadmin&action=editWareType&wareType=$rWareTypes->ID\"'>";
			$content .= $rWareTypes->typeName . "</td><td>";

			// Edit button
			$content .= "<form style='display:inline;' action='?module=kioskadmin&action=editWaretype&wareType=" . $rWareTypes->ID . "' method='post'><input type='submit' value='" . _("Edit") . "' /></form>";

			// Delete button
			$content .= "&nbsp;<form style='display:inline;' action='?module=kioskadmin&action=rmWaretype&wareType=" . $rWareTypes->ID . "' method='post'><input type='submit' value='" . _("Remove") . "' /></form>";
			$content .= "</td></tr>";
		} // End while

		$content .= "</table>";
	} else {
		$content .= "<p><em>" . _("No ware types created yet.") . "</em></p>";
	}

	$content .= "<br /><form method=POST action=?module=kioskadmin&action=addWareType>\n";
	$content .= "<input type=text name='name'>\n";
	$content .= "<input type=submit value='".lang("Add new waretype", "kioskadmin")."'>\n";
	$content .= "</form>";

}

elseif($action == "addWareType" && !empty($_POST['name'])) {
	$name = $_POST['name'];
	if (isset($_GET['save'])) {
		// Update type
		$wareID = isset($_GET['wareType']) ? $_GET['wareType'] : -1;
		if ($wareID > 0) {
			db_query(sprintf("UPDATE %s SET typeName='%s' WHERE `ID`=%s", $sql_prefix . "_kiosk_waretypes", db_escape($name), $wareID));
		}

		header("Location: ?module=kioskadmin&action=wareTypes");
		exit;
	}

	$qFindDuplicate = db_query("SELECT * FROM ".$sql_prefix."_kiosk_waretypes WHERE typeName = '".db_escape($name)."'");
	if(db_num($qFindDuplicate) == 0) {
		db_query("INSERT INTO ".$sql_prefix."_kiosk_waretypes SET typeName = '".db_escape($name)."'");
	}
	$log_new['name'] = $name;
	log_add("kioskadmin", "addWareType", serialize($log_new));
	header("Location: ?module=kioskadmin&action=wareTypes");
} // End action == addWareType

elseif ($action == "rmWaretype" && isset($_GET['wareType'])) {
	$wareID = isset($_GET['wareType']) ? $_GET['wareType'] : -1;
	if ($wareID > 0) {
		db_query(sprintf("DELETE FROM %s WHERE `ID`=%s", $sql_prefix . "_kiosk_waretypes", $wareID));

		// Get all waretypes ID to delete barcodes.
		$qWares = db_query(sprintf("SELECT `ID` FROM %s WHERE `wareType`=%s", $sql_prefix . "_kiosk_wares", $wareID));
		if (db_num($qWares) > 0) {
			$wareIDs = array();
			while ($row = db_fetch($qWares)) {
				$wareIDs[] = $row->ID;
			}

			// Send big query to delete all barcodes.
			db_query(sprintf("DELETE FROM %s WHERE wareID IN(%s)", $sql_prefix . "_kiosk_barcodes", implode($wareIDs)));
			db_query(sprintf("DELETE FROM %s WHERE `ID` IN(%s)", $sql_prefix . "_kiosk_wares", implode($wareIDs)));
		}
	}

	log_add("kioskadmin", "rmWaretype", serialize($wareID));
	header("Location: ?module=kioskadmin&action=wareTypes");
	exit;
}

elseif($action == "editWaretype" && isset($_GET['wareType'])) {
	$wareID = $_GET['wareType'];

	$getWareType = db_query(sprintf("SELECT typeName, typeColor FROM %s WHERE `ID` = %s", $sql_prefix . "_kiosk_waretypes", $wareID));
	if (db_num($getWareType) < 1) {
		header("Location: ?module=kioskadmin&action=wareTypes");
		exit;
	}

	$typeRow = db_fetch($getWareType);

	// Display GUI.
	$content .= "<p><a href=\"?module=kioskadmin&amp;action=wareTypes\">" . _("Back to waretypes") . "</a></p>";
	$content .= "<h2>" . _("Edit ware type") . "</h2>";
	$content .= "<form action='?module=kioskadmin&action=addWareType&save=true&wareType=$wareID' method='post'>
	<table>
		<tr>
			<td><strong>" . _("Name") . "</strong></td>
			<td><input type='text' name='name' value='" . $typeRow->typeName . "' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type='submit' value='" . _("Save") . "' /></td>
		</tr>
	</table>
	</form>";
	unset($typeRow, $getWareType);
}

elseif($action == "wares") {
	$content .= "<p><a href=\"?module=kioskadmin\">" . _("Back to kioskadmin overview") . "</a></p>";
	$content .= "<h2>" . _("Kiosk Wares") . "</h2>";
	$qFindWares = db_query("SELECT wares.ID,wares.name,types.typeName,wares.price FROM ".$sql_prefix."_kiosk_wares wares JOIN ".$sql_prefix."_kiosk_waretypes types
		ON wares.wareType=types.ID");

	if (db_num($qFindWares) > 0) {
		$content .= "<table class='better-table kiosk-wares-table'>";
		$content .= "<tr><th>".lang("Name", "kioskadmin")."</th>";
		$content .= "<th>".lang("Type", "kioskadmin")."</th>";
		$content .= "<th>".lang("Barcodes", "kioskadmin")."</th>";
		$content .= "<th>".lang("Price (normal)")."</th>";
		$content .= "<th style='width: 20%'>".lang("Actions")."</th>";
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
			$content .= $rFindWares->price . "</td><td>";

			// Action links
			$content .= "<form style='display:inline;' action='?module=kioskadmin&action=editWare&wareID=" . $rFindWares->ID . "' method='post'>
			<input type='submit' value='" . _("Edit") . "' /></form>";
			$content .= "&nbsp;<form style='display:inline;' action='?module=kioskadmin&action=rmWare&wareID=" . $rFindWares->ID . "' method='post'>
			<input type='submit' value='" . _("Remove") . "' /></form>";

			$content .= "</td></tr>";
		} // End while
		$content .= "</table>";
	} else {
		$content .= "<p><em>" . _("No wares created yet.") . "</em></p>";
	}

	// Only allow adding wares if waretypes exists.
	$qWareTypes = db_query("SELECT * FROM ".$sql_prefix."_kiosk_waretypes");
	if (db_num($qWareTypes) > 0) {
		$content .= "<br /><form method=POST action='?module=kioskadmin&action=addWare'>\n";
		$content .= "<input type=text name='name'>\n";
		$content .= "<select name=wareType>\n";
		$qFindTypes = db_query("SELECT * FROM " . $sql_prefix . "_kiosk_waretypes");
		while ($rFindTypes = db_fetch($qFindTypes)) {
			$content .= "<option value='$rFindTypes->ID'>$rFindTypes->typeName</option>";
		} // End while
		$content .= "</select>\n";
		$content .= "<br /><input type=submit value='" . lang("Add ware", "kioskadmin") . "'>";
		$content .= "</form>\n";
	} else {
		$content .= "<p><em>" . _("No ware types exists to add wares, head over to '<a href='?module=kioskadmin&amp;action=wareTypes'>Admin waretype</a>' to create types.") . "</em></p>";
	}

} // End action == wares


elseif($action == "addWare" && !empty($_POST['name'])) {
	$name = $_POST ['name'];
	$wareType = $_POST['wareType'];

	db_query("INSERT INTO ".$sql_prefix."_kiosk_wares SET `name` = '".db_escape($name)."', wareType = '".db_escape($wareType)."'");

	$log_new['wareType'] = $wareType;
	$log_new['name'] = $name;
	log_add("kioskadmin", "addWare", serialize($log_new));

	header("Location: ?module=kioskadmin&action=wares");

} // End action = addWare

elseif($action == "editWare" && !empty($_GET['wareID'])) {
	$content .= "<p><a href=\"?module=kioskadmin&amp;action=wares\">" . _("Back to wares") . "</a></p>";
	$content .= "<h2>" . _("Edit Kiosk Ware") . "</h2>";
	$wareID = $_GET['wareID'];
	$qGetWare = db_query("SELECT * FROM ".$sql_prefix."_kiosk_wares WHERE ID = '".db_escape($wareID)."'");
	$rGetWare = db_fetch($qGetWare);

	$content .= "<form method=POST action='?module=kioskadmin&action=doEditWare&wareID=$wareID'>";
	$content .= "<table><tr><td><strong>".lang("Name", "kioskadmin")."</strong></td><td><input type=text name='name' value='$rGetWare->name'></td></tr>\n";
        $content .= "<tr><td><strong>".lang("Ware type", "kioskadmin")."</strong></td><td><select name=wareType>\n";
        $qFindTypes = db_query("SELECT * FROM ".$sql_prefix."_kiosk_waretypes");
        while($rFindTypes = db_fetch($qFindTypes)) {
                $content .= "<option value='$rFindTypes->ID'";
		if($rFindTypes->ID == $rGetWare->wareType) $content .= " SELECTED";
		$content .= ">$rFindTypes->typeName</option>";
        } // End while
        $content .= "</select></td></tr>\n";
	$content .= "<tr><td><strong>".lang("Price", "kioskadmin")."</strong></td><td><input type=text size=4 name=price value='$rGetWare->price'></td></tr>\n";

	$content .= "<tr><td>&nbsp;</td><td><input type=submit value='".lang("Save", "kioskadmin")."'></td></tr>";
	$content .= "</table></form>";

	$content .= "<br /><hr><br />";
	$content .= "<h3>" . lang("Barcodes", "kioskadmin") . "</h3>";
	$qFindBarcodes = db_query("SELECT * FROM ".$sql_prefix."_kiosk_barcodes WHERE wareID = '$wareID'");
	if (db_num($qFindBarcodes) > 0) {
		$content .= "<p>" . _("Click a barcode to delete it.") . "</p>";
		$content .= "<ul>";
		while($rFindBarcodes = db_fetch($qFindBarcodes)) {
			$content .= "<li><a href='?module=kioskadmin&amp;action=rmBarcode&barcode=" . $rFindBarcodes->barcode . "&wareID=$wareID'>" . $rFindBarcodes->barcode . "</a></li>";
		} // End while
		$content .= "</ul>";
	}

	$content .= "<form method=POST action='?module=kioskadmin&action=addBarcode&wareID=$wareID'>\n";
	$content .= "<br /><input type=text name=barcode>\n";
	$content .= "<br /><input type=submit value='".lang("Add barcode", "kioskadmin")."'>\n";
	$content .= "</form>";

}

elseif ($action == 'rmWare' && !empty($_GET['wareID'])) {
	$wareID = $_GET['wareID'];
	db_query(sprintf("DELETE FROM %s WHERE `ID`=%s", $sql_prefix . "_kiosk_wares", $wareID));
	db_query(sprintf("DELETE FROM %s WHERE wareID=%s", $sql_prefix . "_kiosk_barcodes", $wareID));

	log_add("kioskadmin", "rmWare", serialize($wareID));
	header("Location: ?module=kioskadmin&action=wares");
	exit;
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

elseif($action == "rmBarcode" && !empty($_GET['barcode']) && !empty($_GET['wareID'])) {
	$wareID = $_GET['wareID'];
	$barcode = $_GET['barcode'];

	db_query(sprintf("DELETE FROM %s WHERE barcode=%s AND wareID=%s", $sql_prefix . "_kiosk_barcodes", $barcode, $wareID));

	$log = array('barcode' => $barcode, 'wareID' => $wareID);
	log_add("kioskadmin", "rmBarcode", serialize($log));

	header("Location: ?module=kioskadmin&action=editWare&wareID=$wareID");
} // End action == rmBarcode


elseif($action == "credit") {
	$qFindCredits = db_query("SELECT u.nick,u.ID,SUM(totalPrice) AS totalPrice FROM ".$sql_prefix."_kiosk_sales ks JOIN ".$sql_prefix."_users u ON u.ID=ks.soldTo WHERE ks.credit = 1 AND creditPaid = 0 AND ks.totalPrice >0 AND eventID = '$sessioninfo->eventID' GROUP BY u.nick ORDER BY totalPrice DESC");

    $content .= '<h2>'._('Credit').'</h2>';

	$content .= "<table class='table'><thead><tr><th>"._('Name')."</th><th>"._('Amount')."</th></tr></thead>";
    $content .= "<tbody>";
	while($rFindCredits = db_fetch($qFindCredits)) {
		$content .= "<tr><td>";
		$content .= user_profile($rFindCredits->ID);
		$content .= "</td><td>\n";
		$content .= "<a href='?module=kioskadmin&action=viewCreditSales&user=$rFindCredits->ID'>";
		$content .= number_format($rFindCredits->totalPrice) . ' kr';
		$content .= "</a></td></tr>";
	} // End while
    $content .= "</tbody>";
	$content .= "</table>\n\n";
} // End action = credit

elseif($action == 'viewCreditSales' && !empty($_GET['user'])) {
	$user = $_GET['user'];

    $userInfo = user_info((int) $user);

    if ($userInfo !== false) {
        $qFindWares = db_query(sprintf(
            "SELECT kw.ID as kwID, kw.name, kw.price, kw.wareType, ksi.amount
            FROM (%s_kiosk_saleitems ksi LEFT JOIN %s_kiosk_sales ks ON ks.ID=ksi.saleID)
            LEFT JOIN %s_kiosk_wares kw ON ksi.wareID=kw.ID
            WHERE ks.credit=1 AND ks.soldTo = %d AND ks.totalPrice > 0 AND ks.eventID = %d
            ORDER BY kw.name ASC",
            $sql_prefix, $sql_prefix, $sql_prefix,
            $user, $sessioninfo->eventID
        ));
        $rFindWares = db_fetch_all($qFindWares);

        $sumAmount = $sumPrice = 0;

        foreach ($rFindWares as $product) {
            $amount = (int) $product->amount;
            $sumAmount += $amount;
            $sumPrice += ((int) $product->price) * $amount;
        }

        $content .= $twigEnvironment->render(
            'kioskadmin/credit.twig',
            array(
                'user' => $userInfo,
                'sums' => array(
                    'amount' => $sumAmount,
                    'price' => $sumPrice,
                ),
                'products' => $rFindWares,
            )
        );
    } else {
        $content .= '<p>User was not found.</p>';
    }
} // End action = viewCreditSales

elseif($action == "markcreditpaid" && !empty($_GET['user'])) {
	$user = $_GET['user'];

	db_query("UPDATE ".$sql_prefix."_kiosk_sales SET creditPaid = 1 WHERE soldTo = '".db_escape($user)."' AND creditPaid = 0 AND credit = 1 AND eventID = '$sessioninfo->eventID'");
	$log_new = serialize($user);
	log_add("kioskadmin", "markcreditpaid", $log_new);
	header("Location: ?module=kioskadmin&action=credit");

} // End action = markcreditpaid
