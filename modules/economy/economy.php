<?php

$action = $_GET['action'];
$account = $_GET['account'];
if(empty($account)) $account =0;

$acl_access = acl_access("economy", $account, $sessioninfo->eventID);
if($acl_access == 'No') die("You do not have access to economy!");

$acl = acl_access("economy", "", $sessioninfo->eventID);

if(empty($action)) {
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
			$content .= "<tr><td></td><td>$rGetAccounts->accountnumber</td>";
		}

		$content .= "<td>$rGetAccounts->accountText</td><td>";
		if($rGetAccounts->mainAccountNumber == 0) $content .= $rTotalBudget->sum;
		else $content .= $rGetAccounts->budget;
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