<?php

$manager = NewsArticleManger::getInstance();
$acl = acl_access("news", "", $sessioninfo->eventID);
$global_acl = acl_access("news", "", 1);

$content .= "<div class=\"newsadmin\">";
if($action == "newsadmin" && ($acl == 'Admin' || $acl == 'Write')) {

	$articles = $manager->getArticles($sessioninfo->eventID);
	if (count($articles) > 0) {
		$content .= "<h2>" . _("Manage news articles") . "</h2>";
		$content .= "<table class='table full-width'><tbody><tr><th>" . _("Name") . "</th><th>" . _("Actions") . "</th></tr>";
		foreach ($articles as $article) {
			$content .= "
			<tr>
				<td>".$article->getHeader()."
				" . ($article->isActive() ? " <span style=\"color:#01B501; font-style:italic;\">(" . _("Published") . ")</span>" : " <span style=\"color:#EA0505; font-style:italic;\">(" . _("Draft") . ")</span>") . "
				" . ($article->isGlobal() ? " <span style=\"color:#E29405; font-style:italic;\">(" . _("Global") . ")</span>" : "") . "
				</td>
				<td>
					<button onclick=\"window.location = '?module=$module&amp;action=editArticle&amp;articleID=" . $article->getArticleID() . "';\">" . _("Edit") . "</button>
					<button onclick=\"window.location = '?module=$module&amp;action=deleteArticle&amp;articleID=" . $article->getArticleID() . "';\">" . _("Delete") . "</button>
				</td>
			</tr>\n\n";
		} // end foreach
		$content .= "</tbody></table>\n\n";
	} // end if

	$content .= "<div class=\"create-form\"><h2>" . _("Create new article") . "</h2>";
	$content .= "<form method=\"post\" action=\"?module=news&action=doAddArticle\">\n";
	$content .= "<input type=\"text\" name=\"articleHeader\" placeholder=\"" . _("Article header...") . "\" />\n";
	$content .= "<input type=\"submit\" value='".lang("Add new article", "news")."' />\n";
	$content .= "</form></div>";

	$content .= "";
} // End action == newsadmin

elseif($action == "doAddArticle" && ($acl == 'Admin' || $acl == 'Write')) {
	$header = $_POST['articleHeader'];
	$global_article = $_POST['global_article'];

	// Check if the article should be global, and set var
	if(($global_acl == 'Admin' || $global_acl == 'Write') && $global_article == 'on')
		 $global_article = true;
	else
		$global_article = false;

	$article = $manager->createArticle($header, "", $sessioninfo->eventID, false, $global_article);
	if ($article instanceof NewsArticle) {
		header("Location: ?module=news&action=editArticle&articleID=" . $article->getArticleID());
	} else {
		header("Location: ?module=news&action=newsadmin&error=failedcreate");
	}
	die();

}

elseif($action == "editArticle" && ($acl == 'Write' || $acl == 'Admin') && !empty($_GET['articleID'])) {
	$article = $_GET['articleID'];

	$content .= "<h2>" . _("Edit article") . "</h2>";
	$content .= "<p><a href=\"?module=$module&action=newsadmin\">&laquo; " . _("Back") . "</a></p>";

	$article = $manager->getArticle($article);
	if ($article instanceof NewsArticle) {

		$isActive = ($article->isActive() ? " checked=\"checked\"" : "");
		$isGlobal = ($article->isGlobal() ? " checked=\"checked\"" : "");

		$content .= "<form method=\"post\" action=\"?module=news&action=doEditArticle&articleID=" . $article->getArticleID() . "\">";
		$content .= "<table class='table full-width news-editor'>\n";
		$content .= "<tr><th>" . _("Title") . "</th><td><input type=\"text\" name=\"header\" value='" . $article->getHeader() . "'></td></tr>\n";
		$content .= "<tr><th class='top'>" . _("Content") . "</th><td><textarea name=\"content\" class='mceEditor' rows=\"25\" cols=\"60\">";
		$content .= $article->getContent() . "</textarea></td></tr>";
		$content .= "<tr><th><label for=\"isactive\">" . _("Is active?") . "</label></th><td><input id=\"isactive\" type=\"checkbox\" name=\"isactive\" value=\"yes\"$isActive /></td></tr>";
		$content .= "<tr><th><label for=\"isglobal\">" . _("Is global article?") . "</label></th><td><input id=\"isglobal\" type=\"checkbox\" name=\"isglobal\" value=\"yes\"$isGlobal /></td></tr>";
		$content .= "<tr><td>&nbsp;</td><td><input type=\"submit\" value='"._("Save")."' /></td></tr>";
		$content .= "</table></form>";
	} else {
		$content .= "<p>" . _("Fant ikke artikkelen") . "</p>";
	}

}

elseif($action == "doEditArticle" && ($acl == 'Write' || $acl == 'Admin') && !empty($_GET['articleID'])) {
	$header = $_POST['header'];
	$content = $_POST['content'];
	$article = $_GET['articleID'];
	$isActive = isset($_POST['isactive']) && $_POST['isactive'] == "yes" ? true : false;
	$isGlobal = isset($_POST['isglobal']) && $_POST['isglobal'] == "yes" ? true : false;

	$article = $manager->getArticle($article);
	if ($article instanceof NewsArticle) {
		$article->setHeader($header);
		$article->setContent($content);
		$article->setIsActive($isActive);
		$article->setIsGlobal($isGlobal);
		$article->commitChanges();
	} else {
		header("Location: ?module=news&action=newsadmin&error=articlenotfound");
		die();
	}

	header("Location: ?module=news&action=newsadmin");
	die();
}

elseif($action == "deleteArticle" && ($acl == 'Write' || $acl == 'Admin') && !empty($_GET['articleID'])) {
	$article = $_GET['articleID'];

	$article = $manager->getArticle($article);
	if ($article instanceof NewsArticle) {
		$manager->deleteArticle($article);
	} else {
		header("Location: ?module=news&action=newsadmin&error=articlenotfound");
		die();
	}

	header("Location: ?module=news&action=newsadmin");
	die();
}
$content .= "</div>";
