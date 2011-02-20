<?php

$action = $_GET['action'];
$account = $_GET['account'];
if(empty($account)) $account =0;

$acl_access = acl_access("economy", $account, $sessioninfo->eventID);
if($acl_access == 'No') die("You do not have access to economy!");

$acl = acl_access("economy", "", $sessioninfo->eventID);

if(empty($action) || $action == "editAccount") {
	$content .= economy_display_submenu();
	$qGetAccounts = db_query("SELECT * FROM ".$sql_prefix."_economy_accounts
		WHERE eventID = $sessioninfo->eventID ORDER BY accountnumber ASC");
	$content .= '<table>';

	while($rGetAccounts = db_fetch($qGetAccounts)) {
		if($rGetAccounts->mainAccountNumber == 0) {
			$content .= "<tr><td>$rGetAccounts->accountnumber</td><td></td>";
			$qTotalBudget = db_query("SELECT SUM(budget) AS sum FROM ".$sql_prefix."_economy_accounts
				WHERE eventID = '$sessioninfo->eventID'
				AND mainAccountNumber = '$rGetAccounts->accountnumber'");
			$rTotalBudget = db_fetch($qTotalBudget);
			$total_budget = $total_budget + $rTotalBudget->sum;
		} else {
			$content .= "<tr><td></td><td>";
			if($acl == 'Admin') $content .= "<a href=?module=economy&action=editAccount&account=$rGetAccounts->accountnumber>";
			$content .= $rGetAccounts->accountnumber;
			if($acl == 'Admin') $content .= "</a>";
			$content .= "</td>";
		}

		if($action == "editAccount" && $account == $rGetAccounts->accountnumber && $acl == 'Admin') {
			$content .= "<td><form method=POST action=?module=economy&action=doEditAccount&account=$account>";
			$content .= "<input type=text name=accountText size=25 value='$rGetAccounts->accountText'>";
			$content .= "</td><td>";
			$content .= "<input type=text size=5 name=budget value='$rGetAccounts->budget'>";
			$content .= "</td><td>";
			$content .= "<input type=submit value='".lang("Save changes", "economy")."'>";
			$content .= "</form>";
		} else {
			$content .= "<td>$rGetAccounts->accountText</td><td>";
			if($rGetAccounts->mainAccountNumber == 0) $content .= $rTotalBudget->sum;
			else $content .= $rGetAccounts->budget;
		}
		$content .= "</td></tr>";
	} // End while rGetAccounts
	$content .= "<tr><td></td><td></td><td></td><td><b>$total_budget</b></td></tr>";
	$content .= "</table>";

	if($acl == 'Admin') {
		$content .= '<p>';
		$content .= "<form method=POST action=?module=economy&action=addAccount>";
		$content .= "<select name='mainAccount'>";
		$content .= "<option value='0'>".lang("Main account", "economy")."</option>";
		$qGetMainAccounts = db_Query("SELECT * FROM ".$sql_prefix."_economy_accounts WHERE mainAccountNumber = 0 ORDER BY accountnumber ASC");
		while($rGetMainAccounts = db_fetch($qGetMainAccounts)) {
			$content .= "<option value='$rGetMainAccounts->accountnumber'>$rGetMainAccounts->accountnumber - $rGetMainAccounts->accountText";
			$content .= "</option>\n";
		} // End while

		$content .= "</select>\n\n";
		$content .= "<br><input type=text name='accountnumber' size=5 value='100'> ".lang("Accountnumber", "economy");
		$content .= "<br><input type=text name='budget' size=5 value=0> ".lang("Amount", "economy");
		$content .= "<br><input type=text name='accounttext' size=25> ".lang("Account description", "economy");

		$content .= "<br><input type=submit value='".lang("New account", "economy")."'>";
		$content .= "</form>";
	} // End if acl==Admin
} // End if(empty($action))

elseif($action == "addAccount") {
	$accountnumber = $_POST['accountnumber'];
	$budget = $_POST['budget'];
	$accounttext = $_POST['accounttext'];
	$mainAccount = $_POST['mainAccount'];

	$qCheckExisting = db_query("SELECT * FROM ".$sql_prefix."_economy_accounts
		WHERE eventID = '$sessioninfo->eventID'
		AND accountnumber = '".db_escape($accountnumber)."'");
	if(db_num($qCheckExisting) != 0) {
		$content .= lang("Accountnumber already exists", "economy")." ($accountnumber)";
		$content .= "<br><a href='?module=economy'>".lang("Back", "economy")."</a>";
	}
	else {
		db_query("INSERT INTO ".$sql_prefix."_economy_accounts SET
			eventID = '$sessioninfo->eventID',
			accountnumber = '".db_escape($accountnumber)."',
			budget = '".db_escape($budget)."',
			accountText = '".db_escape($accounttext)."',
			mainAccountNumber = '".db_escape($mainAccount)."'");
		header("Location: ?module=economy");
	} // End else

}

elseif($action == "doEditAccount" && $acl == 'Admin' && !empty($account)) {
	$accountText = $_POST['accountText'];
	$budget = $_POST['budget'];


	db_query("UPDATE ".$sql_prefix."_economy_accounts
		SET accountText = '".db_escape($accountText)."',
		budget = '".db_escape($budget)."'
		WHERE eventID = '$sessioninfo->eventID'
		AND accountnumber = '".db_escape($account)."'");
	header("Location: ?module=economy");
} // End elseif action == doEditAccount

elseif($action == "receipt" && $acl == 'Admin') {
	$content .= economy_display_submenu();
	$content .= "<table>\n";
	$qListReceipt = db_query("SELECT * FROM ".$sql_prefix."_economy_receipt WHERE eventID = '$sessioninfo->eventID'");

	while($rListReceipt = db_fetch($qListReceipt)) {
		$content .= "<tr><td>";
		$content .= $rListReceipt->ID;
		$content .= "</td><td>";
		$content .= $rListReceipt->from_location;
		$content .= "</td></tr>\n";
	} // End while
	$content .= "</table>\n\n";

	$content .= "<form method=POST action=?module=economy&action=addReceipt>\n";
	$content .= "<input type=text name='from_location'> ".lang("From", "economy");
	$content .= "<br />";
	$content .= "<input type=text name=timestamp> ".lang("Timestamp (YYYY-MM-DD HH:MM)", "economy");
	$content .= "<br /><input type=text name=description> ".lang("Description", "economy")."\n";
	$content .= "<br /><input type=text name=amount> ".lang("Amount", "economy")."\n";
	$content .= "<br /><input type=submit value='".lang("Add receipt", "economy")."'>";
	$content .= "</form>\n";
} // End action == receipt

elseif($action == "addReceipt" && $acl == 'Admin') {
	$from_location = $_POST['from_location'];
	$timestamp = $_POST['timestamp'];
	$description = $_POST['description'];
	$amount = $_POST['amount'];

	db_query("INSERT INTO ".$sql_prefix."_economy_receipt 
		SET from_location = '".db_escape($from_location)."',
		timestamp = '".db_escape($timestamp)."',
		description = '".db_escape($description)."',
		amount = '".db_escape($amount)."',
		eventID = '$sessioninfo->eventID'");
	header("Location: ?module=economy&action=receipt");
}
elseif($action == "accounts" && $acl == 'Admin') {
	$content .= economy_display_submenu();	

}

elseif($action == "bookkeeping" && $acl == 'Admin') {
	$content .= economy_display_submenu();

}
function economy_display_submenu() {
	$action = $_GET['action'];
	$acl = acl_access("economy", "", $sessioninfo->userID);

	if(empty($action) && $acl == 'Admin') {
		$return = lang("Main", "economy");
	} else {
		$return = "<a href=?module=economy>".lang("Main", "economy")."</a>";
	}

	$return .= " ";
	if($action == "receipt" && $acl == 'Admin') {
		$return .= lang("Receipts", "economy");
	}
	else {
		$return .= "<a href=?module=economy&action=receipt>".lang("Receipts", "economy")."</a>";
	}
	$return .= " ";
	if($action == "bookkeeping" && $acl == 'Admin') {
		$return .= lang("Bookkeeping", "economy");
	} else {
		$return .= "<a href=?module=economy&action=bookkeeping>".lang("Bookkeeping", "economy")."</a>";
	}
	$return .= " ";
	if($action == "accounts" && $acl == 'Admin') {
		$return .= lang("Accounts", "economy");
	} else {
		$return .= "<a href=?module=economy&action=accounts>".lang("Accounts", "economy")."</a>";
	}
	return $return;
}
