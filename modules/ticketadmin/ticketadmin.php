<?php
$eventID = $sessioninfo->eventID;

if(acl_access("ticketadmin", "", $eventID) != 'Admin') die("You do not have ticketadmin-rights");
$action = $_GET['action'] ?? null;


if(!isset($action) || $action == "editticket") {

    $qListTickets = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE eventID = '$eventID'");

    if(db_num($qListTickets) != 0 && $action != 'editticket') {
        $content .= '<table>';
        $content .= '<tr><th>'.lang("Ticketnumber", "ticketadmin");
        $content .= '</th><th>'.lang("Ticketname", "ticketadmin");
        $content .= '</th><th>'.lang("Tickettype", "ticketadmin");
        $content .= '</th><th>'.lang("Price", "ticketadmin");
	$content .= '</th><th>'.lang("Number of tickets", "ticketadmin");
        $content .= '</th><th>'.lang("Sold tickets of type (total/seated/paid)", "ticketadmin");
        $content .= '</th></tr>';
        while($rListTickets = db_fetch($qListTickets)) {
	$content .= "<tr><td>\n";
	$content .= $rListTickets->ticketTypeID;
	$content .= "</td><td>\n";
	$content .= "<a href=\"?module=ticketadmin&amp;action=editticket&amp;editticket=$rListTickets->ticketTypeID\">\n";
	$content .= $rListTickets->name;
	$content .= "</a></td><td>\n";
	$content .= lang($rListTickets->type, "ticketadmin");
	$content .= "</td><td>\n";
	$content .= $rListTickets->price;
	$content .= "</td><td>\n";
	$content .= $rListTickets->maxTickets;
	$content .= "</td><td class=tdLink onClick='location.href=\"?module=ticketadmin&action=listTickets&tickettype=$rListTickets->ticketTypeID\"'>\n";
	$qNumTicketsOfType = db_query("SELECT COUNT(*) AS count FROM ".$sql_prefix."_tickets 
	    WHERE eventID = '$eventID' AND ticketType = '$rListTickets->ticketTypeID' AND status != 'deleted'");
	$rNumTicketsOfType = db_fetch($qNumTicketsOfType);
	$qNumTicketsUsed = db_query("SELECT COUNT(*) AS count FROM ".$sql_prefix."_tickets
		WHERE eventID = '$eventID' AND ticketType='$rListTickets->ticketTypeID' AND status = 'used'");
	$rNumTicketsUsed = db_fetch($qNumTicketsUsed);
	$qNumTicketsPaid = db_query("SELECT COUNT(*) AS count FROM ".$sql_prefix."_tickets
		WHERE eventID = '$eventID' AND ticketType='$rListTickets->ticketTypeID' AND status != 'deleted' AND paid = 'yes'");
	$rNumTicketsPaid = db_fetch($qNumTicketsPaid);
	$content .= $rNumTicketsOfType->count."/".$rNumTicketsUsed->count."/".$rNumTicketsPaid->count;
	$content .= "</td></tr>\n";
        } // End while
        $content .= "</table>\n";
    } // End if db_num() != 0
    $content .= "<br /><br />\n";
    // add form for adding tickets
    $content .= "<table>\n";
    $content .= "<tr><td>\n";
    // If we're editing a ticket, display values of ticket, else, addticket

    $rGetTicketInfo = new \stdClass();
    $rGetTicketInfo->type = '';
    $rGetTicketInfo->name = '';
    $rGetTicketInfo->price = '';
    $rGetTicketInfo->description = '';
    $rGetTicketInfo->active = false;
    $rGetTicketInfo->allowSeating = true;
    $rGetTicketInfo->maxTickets = 0;

    if($action == "editticket" && isset($_GET['editticket'])) {
        $qGetTicketInfo = db_query("SELECT * FROM ".$sql_prefix."_ticketTypes WHERE ticketTypeID = '".db_escape($_GET['editticket'])."'");
        $rGetTicketInfo = db_fetch($qGetTicketInfo);
        $qGetAdditionalTickets = db_query("SELECT rel.additionalTicketTypeID AS relID FROM {$sql_prefix}_ticketTypes_additional AS rel INNER JOIN {$sql_prefix}_ticketTypes AS tt ON tt.ticketTypeID = rel.additionalTicketTypeID WHERE rel.ticketTypeID = '" . db_escape($_GET['editticket']) . "'");
        $rAdditionalTicketIds = array_map(
            static function ($row) { return (int) $row->relID; },
            db_fetch_all($qGetAdditionalTickets)
        );
        $content .= "<form method=\"post\" action=\"?module=ticketadmin&amp;action=doeditticket&amp;editticket=".$_GET['editticket']."\">\n";
    } // end if action = editticket
    else {
        $content .= "<form method=\"post\" action=\"?module=ticketadmin&amp;action=addtickettype\">\n";
        $rAdditionalTicketIds = [];
    }
    $qAdditionalTickets = db_query("SELECT ticketTypeID,name FROM {$sql_prefix}_ticketTypes WHERE active = 1 AND allowSellingStandalone = 0 AND ticketTypeID != '" . db_escape((int) ($_GET['editticket'] ?? null)) . "' AND eventID = '" . db_escape($eventID) . "' ORDER BY name ASC");
    $rAdditionalTickets = db_fetch_all($qAdditionalTickets);
    $content .= "<p class=\"nopad\"><input type=\"text\" name=\"name\" value='$rGetTicketInfo->name' /></p>\n";
    $content .= "</td><td>";
    $content .= lang("Name of ticket", "ticketadmin");
    $content .= "</td></tr><tr><td>\n";
    $content .= "<p class=\"nopad\"><input type=\"text\" name=\"price\" value='$rGetTicketInfo->price' /></p>\n";
    $content .= "</td><td>\n";
    $content .= lang("Price of ticket", "ticketadmin");
    $content .= "</td></tr><tr><td>\n";
    $content .= "<p class=\"nopad\"><textarea rows=\"4\" name=\"description\" cols=\"40\">$rGetTicketInfo->description</textarea></p>\n";
    $content .= "</td><td>\n";
    $content .= _("Description");
    $content .= "</td></tr><tr><td>\n";
    $content .= "<select name=\"type\">\n";
        $content .= "<option value=\"prepaid\"";
        if($rGetTicketInfo->type == 'prepaid') $content .= " selected";
        $content .= ">".lang("Prepaid ticket", "ticketadmin")."</option>\n";

        $content .= "<option value=\"preorder\"";
        if($rGetTicketInfo->type == 'preorder') $content .= " selected";
        $content .= ">".lang("Preordered ticket", "ticketadmin")."</option>\n";

        $content .= "<option value=\"onsite-computer\"";
        if($rGetTicketInfo->type == 'onsite-computer') $content .= " selected";
        $content .= ">".lang("Onsite ticket with computer", "ticketadmin")."</option>\n";

        $content .= "<option value=\"onsite-visitor\"";
        if($rGetTicketInfo->type == 'onsite-visitor') $content .= " selected";
        $content .= ">".lang("Onsite ticket without computer", "ticketadmin")."</option>\n";

        $content .= "<option value=\"reseller\"";
        if($rGetTicketInfo->type == 'reseller') $content .= " selected";
        $content .= ">".lang("Prepaid ticket reseller", "ticketadmin")."</option>\n";

    $content .= "</select>\n";
    $content .= "</td><td>\n";
    $content .= lang("Type of ticket", "ticketadmin");
    $content .= "</td></tr><tr><td>\n";
    $content .= "<p class=\"nopad\"><input type=\"checkbox\" name=\"active\" value=\"1\"";
    if($rGetTicketInfo->active == 1) $content .= " checked";
    $content .= " /></p>\n";
    $content .= "</td><td>\n";
    $content .= lang("Tickettype is active?", "ticketadmin");
    $content .= "</td></tr><tr><td>\n";
    $content .= "<p class=\"nopad\"><input type=\"checkbox\" name=\"allowSeating\" value=\"1\"";
    if($rGetTicketInfo->allowSeating) $content .= " checked";
    $content .= " /></p>\n";
    $content .= "</td><td>\n";
    $content .= lang("Allow seating with this ticket?", "ticketadmin");
    $content .= "</td></tr><tr><td>\n";
    $content .= "<input type=text size=5 name=maxTickets";
    if($rGetTicketInfo->maxTickets > 0) $maxTickets = $rGetTicketInfo->maxTickets;
    else $maxTickets = 0;
    $content .= " value='$maxTickets'";
    $content .= ">";
    $content .= '</td><td>';
    $content .= lang("Max tickets to sell", "ticketadmin");
    $content .= "</td></tr><tr><td>\n";
    $content .= '<input type="checkbox" name="allow_selling_standalone" value="1"';
    if ($rGetTicketInfo->allowSellingStandalone) $content .= ' checked';
    $content .= ' />';
    $content .= '</td><td>';
    $content .= lang('Allow selling as standalone?', 'ticketadmin');
    $content .= "</td></tr><tr><td>\n";
    $content .= '<input type="checkbox" name="allow_changing_owner_or_user" value="1"';
    if ($rGetTicketInfo->allowChangingOwnerOrUser) $content .= ' checked';
    $content .= ' />';
    $content .= '</td><td>';
    $content .= lang('Allow changing owner or user?', 'ticketadmin');
    $content .= "</td></tr><tr><td>\n";
    if (count($rAdditionalTickets) > 0) {
        $content .= '<select name="additional_tickets[]" multiple>';
        foreach ($rAdditionalTickets as $additionalTicket) {
            $content .= '<option value="' . $additionalTicket->ticketTypeID . '"';
            if (in_array((int)$additionalTicket->ticketTypeID, $rAdditionalTicketIds, true)) {
                $content .= ' selected';
            }
            $content .= '>' . htmlspecialchars($additionalTicket->name) . '</option>';
        }
        $content .= '</select>';
        $content .= '</td><td>';
        $content .= lang('Additional tickets', 'ticketadmin');
        $content .= "</td></tr><tr><td>\n";
    }
    if($action == "editticket") $content .= "<input type=\"submit\" value='".lang("Edit tickettype", "ticketadmin")."' />\n";
    else $content .= "<p class=\"nopad\"><input type=\"submit\" value='".lang("Add tickettype", "ticketadmin")."' /></p>\n";
    $content .= "</form></td></tr></table>\n";

    if($action == "editticket" && $rGetTicketInfo->type == 'reseller') {
	$qGetRights = db_query("SELECT a.groupID,g.groupname 
		FROM ".$sql_prefix."_ACLs a 
		JOIN ".$sql_prefix."_groups g ON a.groupID=g.ID 
		WHERE a.eventID = '$sessioninfo->eventID' 
		AND accessmodule = 'reseller' 
		AND subcategory = '$rGetTicketInfo->ticketTypeID'");
	$content .= "<table>\n";
	while($rGetRights = db_fetch($qGetRights)) {
		$content .= "<tr><td>";
		$content .= "<a href=?module=ticketadmin&action=removeRight&group=$rGetRights->groupID&ticketType=$rGetTicketInfo->ticketTypeID>";
		$content .= $rGetRights->groupname;
		$content .= "</a></td></tr>";
	} // End while rGetRights
	$qFindGroups = db_query("SELECT * FROM ".$sql_prefix."_groups WHERE eventID IN (1, $sessioninfo->eventID) AND groupType = 'access'");
	$content .= "<tr><td>";
	$content .= "<form method=POST action=?module=ticketadmin&action=addReseller&ticketType=$rGetTicketInfo->ticketTypeID>";
	while($rFindGroups = db_fetch($qFindGroups));
	$content .= "</table>";

   } // if action == editticket && type = reseller

} // End if !isset($action)

elseif($action == "addtickettype") {
    $name = db_escape($_POST['name']);
    $description = db_escape($_POST['description']);
    $price = db_escape($_POST['price']);
    $type = db_escape($_POST['type']);
    $maxTickets = db_escape($_POST['maxTickets']);
    $allowSellingStandalone = db_escape(filter_var($_POST['allow_selling_standalone'] ?? null, \FILTER_VALIDATE_BOOLEAN));
    $allowChangingOwnerOrUser = db_escape(filter_var($_POST['allow_changing_owner_or_user'] ?? null, \FILTER_VALIDATE_BOOLEAN));
    if($_POST['allowSeating'] == 1) $allowSeating = 1;
    else $allowSeating = 0;
    if($_POST['active'] == 1) $active = 1;
    else $active = 0;

    db_query("INSERT INTO ".$sql_prefix."_ticketTypes SET 
        name = '$name', 
        description = '$description', 
        price = '$price', 
        allowSeating = '$allowSeating', 
        type = '$type', 
        active = '$active',
	maxTickets = '$maxTickets',
	    allowSellingStandalone = '$allowSellingStandalone',
	    allowChangingOwnerOrUser = '$allowChangingOwnerOrUser',
        eventID = '$eventID'");
    $ticketTypeID = db_insert_id();

    $log_new['name'] = $name;
    $log_new['price'] = $price;
    $log_new['type'] = $type;
    $log_new['active'] = $active;
    $log_new['maxTickets'] = $maxTickets;
    $log_new['allowSellingStandalone'] = $allowSellingStandalone;
    $log_new['allowChangingOwnerOrUser'] = $allowChangingOwnerOrUser;
    log_add("ticketadmin", "addTicketType", serialize($log_new));

    $additionalTicketIds = array_unique(array_map('intval', array_filter(
        is_array($_POST['additional_tickets'] ?? null) ? $_POST['additional_tickets'] : [],
        'is_numeric'
    )));

    if (count($additionalTicketIds) > 0) {
        foreach ($additionalTicketIds as $additionalTicketId) {
            db_query("INSERT INTO {$sql_prefix}_ticketTypes_additional(ticketTypeID, additionalTicketTypeID)VALUES('" . db_escape($ticketTypeID) . "', '" . db_escape($additionalTicketId) . "')");
        }
    }

    header("Location: ?module=ticketadmin");

} // end if action == addtickettype

elseif($action == "doeditticket" && !empty($_GET['editticket'])) {
    $name = db_escape($_POST['name']);
    $description = db_escape($_POST['description']);
    $price = db_escape($_POST['price']);
    $type = db_escape($_POST['type']);
    $maxTickets = db_escape($_POST['maxTickets']);
    $allowSellingStandalone = db_escape(filter_var($_POST['allow_selling_standalone'] ?? null, \FILTER_VALIDATE_BOOLEAN));
    $allowChangingOwnerOrUser = db_escape(filter_var($_POST['allow_changing_owner_or_user'] ?? null, \FILTER_VALIDATE_BOOLEAN));
    if($_POST['allowSeating'] == 1) $allowSeating = 1;
    else $allowSeating = 0;
    if($_POST['active'] == 1) $active = 1;
    else $active = 0;

    db_query("UPDATE ".$sql_prefix."_ticketTypes SET
        name = '$name', 
        description = '$description', 
        price = '$price', 
        type = '$type', 
        active = '$active',
        allowSeating = '$allowSeating',
        allowSellingStandalone = '$allowSellingStandalone',
        allowChangingOwnerOrUser = '$allowChangingOwnerOrUser',
	maxTickets = '$maxTickets'
        WHERE ticketTypeID = '".db_escape($_GET['editticket'])."'");
    $log_new['name'] = $name;
    $log_new['price'] = $price;
    $log_new['type'] = $type;
    $log_new['allowSeating'] = $allowSeating;
    $log_new['active'] = $active;
    $log_new['maxTickets'] = $maxTickets;
    $log_new['allowSellingStandalone'] = $allowSellingStandalone;
    $log_new['allowChangingOwnerOrUser'] = $allowChangingOwnerOrUser;

    $additionalTicketIds = array_unique(array_map('intval', array_filter(
        is_array($_POST['additional_tickets'] ?? null) ? $_POST['additional_tickets'] : [],
        'is_numeric'
    )));

    db_query("DELETE FROM {$sql_prefix}_ticketTypes_additional WHERE ticketTypeID = '" . db_escape($_GET['editticket']) . "'");
    if (count($additionalTicketIds) > 0) {
        foreach ($additionalTicketIds as $additionalTicketId) {
            db_query("INSERT INTO {$sql_prefix}_ticketTypes_additional(ticketTypeID, additionalTicketTypeID)VALUES('" . db_escape($_GET['editticket']) . "', '" . db_escape($additionalTicketId) . "')");
        }
    }

    log_add("ticketadmin", "doEditTicket", serialize($log_new));
    header("Location: ?module=ticketadmin");
} // End if action = doeditticket

elseif($action == "listTickets") {
	if(empty($_GET['tickettype'])) $where = '1';
	else $where = 'ticketType = '.$_GET['tickettype'];

	$qGetTickets = db_query("SELECT * FROM ".$sql_prefix."_tickets WHERE eventID = '$sessioninfo->eventID' AND $where");
	$content .= "<table>\n";
        $content .= "<tr><th>".lang("Ticketnumber", "ticketorder");
        $content .= "</th><th>".lang("Owner", "ticketorder");
        $content .= "</th><th>".lang("User", "ticketorder");
        $content .= "</th><th>".lang("Status", "ticketorder");
        $content .= "</th><th>".lang("Map placement", "ticketorder");
	$content .= "</th><th>".lang("Paid?", "ticketorder");
	$content .= "</th></tr>\n\n";

	while($rGetTickets = db_fetch($qGetTickets)) {
		$content .= "<tr><td>";
		$content .= $rGetTickets->ticketID;
		$content .= "</td><td>";
		$content .= user_profile($rGetTickets->owner);
		$content .= "</td><td>";
		$content .= user_profile($rGetTickets->user);
		$content .= "</td><td>";
		if($rGetTickets->status == 'used') $content .= _("used");
		elseif($rGetTickets->status == 'deleted') $content .= _("deleted");
		else $content .= _("not used");
#		$content .= lang($rGetTickets->status, "ticketorder");
		$content .= "</td><td>";
		$content .= "<a href='?module=seating&ticketID=$rGetTickets->ticketID'>";

		$qFindSeating = db_query("SELECT * FROM ".$sql_prefix."_seatReg_seatings WHERE ticketID = '$rGetTickets->ticketID'");
		if(db_num($qFindSeating) == 1) {
			$rFindSeating = db_fetch($qFindSeating);
			$content .= $rFindSeating->seatX." / ".$rFindSeating->seatY;
		} // End db_num($qFindSeating)
		else $content .= lang("No seat chosen");

		$content .= "</a>";
		$content .= "</td><td>";
		if($rGetTickets->paid == 'yes') $content .= _("yes");
		else $content .= _("no");
#		$content .= lang($rGetTickets->paid, "ticketadmin");
		$content .= "</td></tr>\n\n";
	} // End while rGetTickets
	$content .= "</table>";
}
