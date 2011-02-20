<?php

if(acl_access("translate", 0, 0) != 'Admin') die("You do not have access to translate-interface");

$action = $_GET['action'];
$language = $_GET['language'];
$mode = $_GET['mode'];

if(empty($mode)) $mode = "untranslated";

if(!isset($action)) {
	switch ($mode) {
		case "untranslated":
			$queryWhere = "language = '".db_escape($language)."' AND translated IS NULL AND ignoreExport = 0";
			break;
		default:
			$queryWhere = "1"; // Default to anything
			break;
	} // End switch

	$qGetTranslateList = db_query("SELECT * FROM ".$sql_prefix."_lang WHERE ".$queryWhere);

	$content .= '<table>';
	while($rGetTranslateList = db_fetch($qGetTranslateList)) {
		$content .= "<form method=POST action=?module=translate&action=changeTranslation&language=$language&translationID=$rGetTranslateList->ID>\n";
		$content .= "<tr><td>";
		$content .= $rGetTranslateList->string;
		$content .= "</td><td>";
		$content .= "<textarea name=translated cols=50 rows=3>$rGetTranslateList->translated</textarea>";
		$content .= "</td><td>";
		$content .= "<input type=checkbox name='ignoreExport'>";
		$content .= "</td><td>";
		$content .= "<input type=submit value='".lang("Save", "translate")."'>\n";
		$content .= "</form></td></tr>";
	} // End while
	$content .= "</table>";

}

elseif($action == "changeTranslation" && isset($_GET['translationID'])) {
	$translationID = $_GET['translationID'];
	$translated = $_POST['translated'];
	$language = $_GET['language'];
	$ignoreExport = $_POST['ignoreExport'];
	if($ignoreExport == 'on') $ignoreExport = 1;
	else $ignoreExport = 0;
#	die($ignoreExport." :: ".$_POST['ignoreExport']);
	if(empty($translation)) $translation = NULL;

	db_query("UPDATE ".$sql_prefix."_lang
		SET translated = '".db_escape($translated)."',
		ignoreExport = '$ignoreExport'
		WHERE ID = '".db_escape($translationID)."'");
	header("Location: ?module=translate&language=$language");
} // End elseif action == changeTranslation

elseif($action == "syncTranslate") {
	$script_dir = substr_replace($_SERVER['SCRIPT_FILENAME'], '', -9);
	$file = fopen($script_dir."installer/language.php", "w");

	$data = "<?php\n\n";

	$qFindTranslations = db_query("SELECT * FROM ".$sql_prefix."_lang WHERE translated IS NOT NULL AND ignoreExport = 0");
	while($rFindTranslations = db_fetch($qFindTranslations)) {
// Doesn't look good, but we're printing it to an outfile
$data .= 'db_query("UPDATE ".$sql_prefix."_lang SET translated = \'".db_escape("'.$rFindTranslations->translated.'")."\'
WHERE language = \'".db_escape("'.$rFindTranslations->language.'")."\' AND module = \'".db_escape("'.$rFindTranslations->module.'")."\' AND string = \'".db_escape("'.$rFindTranslations->string.'")."\'");'."\n\n";
	} // End while

	fwrite($file, $data);

}
