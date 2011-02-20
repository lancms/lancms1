<?php

$acl = acl_access("news", "", $sessioninfo->eventID);
$global_acl = acl_access("news", "", 1);


if($action == "newsadmin" && ($acl == 'Admin' || $acl == 'Write')) {
	$content .= "<table>";

	$qListNews = db_query("SELECT * FROM ".$sql_prefix."_news WHERE eventID IN ($sessioninfo->eventID)");
	while($rListNews = db_fetch($qListNews)) {
		$content .= "<tr><td><a href=?module=news&action=editArticle&articleID=$rListNews->ID>".$rListNews->header."</a>";
		$content .= "</td></tr>\n\n";
	} // End while

	$content .= "</table>\n\n";
	$content .= "<form method=POST action=?module=news&action=doAddArticle>\n";
	$content .= "<input type=text name=articleHeader>\n";
	if($global_acl == 'Admin' || $global_acl == 'Write') $content .= "<br /><input type=checkbox name='global_article'>".lang("Add as global article", "news");
	$content .= "<br /><input type=submit value='".lang("Add new article", "news")."'>\n";
	$content .= "</form>";
} // End action == newsadmin

elseif($action == "doAddArticle" && ($acl == 'Admin' || $acl == 'Write')) {
	$header = $_POST['articleHeader'];
	$global_article = $_POST['global_article'];

	// Check if the article should be global, and set var
	if(($global_acl == 'Admin' || $global_acl == 'Write') && $global_article == 'on')
		 $global_article = 'yes';
	else
		$global_article = 'no';
	
	db_query("INSERT INTO ".$sql_prefix."_news SET
		header = '".db_escape($header)."',
		global = '$global_article',
		eventID = '$sessioninfo->eventID',
		createTime = '".time()."'");
	$log_new['header'] = $header;
	$log_new['global'] = $global_article;
	log_add("news", "addArticle", serialize($log_new));

	$qFindNews = db_query("SELECT ID FROM ".$sql_prefix."_news WHERE header = '".db_escape($header)."' AND eventID = '$sessioninfo->eventID'");
	$rFindNews = db_fetch($qFindNews);

	header("Location: ?module=news&action=editArticle&articleID=$rFindNews->ID");
}

elseif($action == "editArticle" && ($acl == 'Write' || $acl == 'Admin') && !empty($_GET['articleID'])) {
	$article = $_GET['articleID'];

	$qFindArticle = db_query("SELECT * FROM ".$sql_prefix."_news WHERE eventID = '$sessioninfo->eventID' AND ID = '$article'");
	$rFindArticle = db_fetch($qFindArticle);

	$content .= "<form method=POST action=?module=news&action=doEditArticle&articleID=$article>\n";
	$content .= "<input type=text name=header value='$rFindArticle->header'>\n";
	$content .= "<br /><textarea name=content class='mceEditor' rows=25 cols=60>";
	$content .= $rFindArticle->content."</textarea>";
#	$content .= "<input type=checkbox name=active value='$rFindArticle->active'>
	$content .= "<br /><input type=submit value='".lang("Save", "news")."'>";
	$content .= "</form>";

}

elseif($action == "doEditArticle" && ($acl == 'Write' || $acl == 'Admin') && !empty($_GET['articleID'])) {
	$header = $_POST['header'];
	$content = $_POST['content'];
	$article = $_GET['articleID'];
	
	db_query("UPDATE ".$sql_prefix."_news SET
		header = '".db_escape($header)."',
		content = '".db_escape($content)."'
		WHERE ID = '$article'");

	log_add("news", "edit", serialize($_POST));
	header("Location: ?module=news&action=newsadmin");
}
